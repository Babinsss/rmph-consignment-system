<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\StockRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Calculate current available physical stock for an item across active batches.
     */
    public function calculateTotalLiveBalance(Item $item): int
    {
        return (int) DeliveryItem::where('item_id', $item->id)
            ->where('status', 'available')
            ->sum('quantity_remaining');
    }

    /**
     * Calculate total consignment valuation sitting in inventory for an item.
     */
    public function calculateTotalValuation(Item $item): float
    {
        return (float) (DeliveryItem::where('item_id', $item->id)
            ->where('status', 'available')
            ->selectRaw('SUM(quantity_remaining * unit_cost) as total')
            ->value('total') ?? 0.0);
    }

    /**
     * Process an incoming delivery and record perpetual stockcard ledger movements.
     */
    public function processInwardDelivery(Delivery $delivery, ?User $actor = null): void
    {
        DB::transaction(function () use ($delivery, $actor) {
            $actorId = $actor?->id ?? $delivery->received_by_id;
            $supplierName = $delivery->supplier?->name ?? 'Supplier';

            $totalDeliveryCost = 0.0;

            foreach ($delivery->items as $batch) {
                $item = $batch->item;

                // Ensure initial batch quantities and costs are consistent
                $batch->quantity_remaining = $batch->quantity_received;
                $batch->total_cost = $batch->quantity_received * $batch->unit_cost;
                $batch->status = 'available';
                $batch->save();

                $totalDeliveryCost += (float) $batch->total_cost;

                // Calculate updated balance
                $newBalance = $this->calculateTotalLiveBalance($item);

                // Create stockcard movement record
                StockMovement::create([
                    'item_id' => $item->id,
                    'delivery_item_id' => $batch->id,
                    'department' => $delivery->department,
                    'movement_type' => 'DELIVERY_IN',
                    'reference_number' => $delivery->delivery_number,
                    'reference_type' => Delivery::class,
                    'reference_id' => $delivery->id,
                    'source_destination' => $supplierName,
                    'lot_number' => $batch->lot_number,
                    'expiry_date' => $batch->expiry_date,
                    'quantity_in' => $batch->quantity_received,
                    'quantity_out' => 0,
                    'balance_after' => $newBalance,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $batch->total_cost,
                    'user_id' => $actorId,
                    'remarks' => "Inward delivery via DR #{$delivery->delivery_number}".($delivery->po_reference ? " (PO: {$delivery->po_reference})" : ''),
                ]);
            }

            $delivery->update([
                'total_cost' => $totalDeliveryCost,
                'status' => 'received',
            ]);
        });
    }

    /**
     * Issue an approved or pending stock request using FEFO (First-Expired, First-Out) allocation.
     */
    public function issueRequest(StockRequest $request, ?User $actor = null): void
    {
        DB::transaction(function () use ($request, $actor) {
            $actorId = $actor?->id ?? $request->issued_by_id ?? $request->requester_id;

            foreach ($request->items as $requestItem) {
                $item = $requestItem->item;
                $needed = $requestItem->quantity_requested;

                // Lock available batches by FEFO: nearest expiry first, then oldest received
                $batches = DeliveryItem::where('item_id', $item->id)
                    ->where('quantity_remaining', '>', 0)
                    ->where('status', 'available')
                    ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
                    ->orderBy('id', 'ASC')
                    ->lockForUpdate()
                    ->get();

                $availableStock = $batches->sum('quantity_remaining');

                if ($availableStock < $needed) {
                    throw new InvalidArgumentException(
                        "Insufficient consignment stock for item '{$item->name}'. Requested: {$needed}, Available: {$availableStock}"
                    );
                }

                $issuedForThisItem = 0;
                $totalItemCost = 0.0;

                foreach ($batches as $batch) {
                    if ($needed <= 0) {
                        break;
                    }

                    $deduct = min($batch->quantity_remaining, $needed);
                    $batch->quantity_remaining -= $deduct;
                    if ($batch->quantity_remaining === 0) {
                        $batch->status = 'depleted';
                    }
                    $batch->save();

                    $needed -= $deduct;
                    $issuedForThisItem += $deduct;
                    $batchCost = $deduct * (float) $batch->unit_cost;
                    $totalItemCost += $batchCost;

                    // Compute balance after this deduction
                    $newBalance = $this->calculateTotalLiveBalance($item);

                    // Record ledger stock movement
                    StockMovement::create([
                        'item_id' => $item->id,
                        'delivery_item_id' => $batch->id,
                        'department' => $request->department,
                        'movement_type' => 'REQUEST_OUT',
                        'reference_number' => $request->request_number,
                        'reference_type' => StockRequest::class,
                        'reference_id' => $request->id,
                        'source_destination' => $request->requesting_unit,
                        'lot_number' => $batch->lot_number,
                        'expiry_date' => $batch->expiry_date,
                        'quantity_in' => 0,
                        'quantity_out' => $deduct,
                        'balance_after' => $newBalance,
                        'unit_cost' => $batch->unit_cost,
                        'total_cost' => $batchCost,
                        'user_id' => $actorId,
                        'remarks' => "Stock issue for {$request->requesting_unit} (Requester: {$request->requester_name})",
                    ]);
                }

                $avgUnitCost = $issuedForThisItem > 0 ? ($totalItemCost / $issuedForThisItem) : $item->unit_cost;

                $requestItem->update([
                    'quantity_issued' => $issuedForThisItem,
                    'unit_cost' => $avgUnitCost,
                    'total_cost' => $totalItemCost,
                ]);
            }

            $request->update([
                'status' => 'issued',
                'issued_by_id' => $actorId,
                'issued_at' => now(),
            ]);
        });
    }

    /**
     * Record a manual stock adjustment (e.g. spoilage, breakage, physical count discrepancy, or return to supplier).
     */
    public function adjustStock(
        Item $item,
        DeliveryItem $batch,
        int $quantity,
        string $type = 'ADJUSTMENT',
        bool $isDeduction = true,
        string $reason = '',
        ?User $actor = null
    ): StockMovement {
        return DB::transaction(function () use ($item, $batch, $quantity, $type, $isDeduction, $reason, $actor) {
            $actorId = $actor?->id ?? auth()->id();

            if ($isDeduction) {
                if ($batch->quantity_remaining < $quantity) {
                    throw new InvalidArgumentException(
                        "Cannot deduct {$quantity} units from Lot '{$batch->lot_number}'. Only {$batch->quantity_remaining} remaining."
                    );
                }

                $batch->quantity_remaining -= $quantity;
                if ($batch->quantity_remaining === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $qtyIn = 0;
                $qtyOut = $quantity;
            } else {
                $batch->quantity_remaining += $quantity;
                if ($batch->status === 'depleted') {
                    $batch->status = 'available';
                }
                $batch->save();

                $qtyIn = $quantity;
                $qtyOut = 0;
            }

            $totalCost = $quantity * (float) $batch->unit_cost;
            $newBalance = $this->calculateTotalLiveBalance($item);
            $referenceNumber = 'ADJ-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -4));

            return StockMovement::create([
                'item_id' => $item->id,
                'delivery_item_id' => $batch->id,
                'department' => $item->department,
                'movement_type' => $type,
                'reference_number' => $referenceNumber,
                'reference_type' => DeliveryItem::class,
                'reference_id' => $batch->id,
                'source_destination' => $type === 'RETURN_TO_SUPPLIER'
                    ? ($item->supplier?->name ?? 'Consignor Return')
                    : 'Physical Count / Spoilage Adjustment',
                'lot_number' => $batch->lot_number,
                'expiry_date' => $batch->expiry_date,
                'quantity_in' => $qtyIn,
                'quantity_out' => $qtyOut,
                'balance_after' => $newBalance,
                'unit_cost' => $batch->unit_cost,
                'total_cost' => $totalCost,
                'user_id' => $actorId,
                'remarks' => $reason ?: "Stock adjustment ({$type}) for Lot #{$batch->lot_number}",
            ]);
        });
    }
}
