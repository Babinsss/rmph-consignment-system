<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Delivery;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    /**
     * Record a new delivery and create all associated batches with initial stock movements.
     *
     * @param  array{
     *     consignor_id: int,
     *     dr_number: string,
     *     delivery_date: string|\DateTimeInterface,
     *     received_by_id?: ?int,
     *     notes?: ?string,
     *     batches: array<int, array{
     *         item_id: int,
     *         lot_number: string,
     *         expiration_date: string|\DateTimeInterface,
     *         quantity_received: int,
     *         unit_cost: float|string,
     *     }>
     * }  $data
     */
    public function recordDelivery(array $data, ?User $recordedBy = null): Delivery
    {
        return DB::transaction(function () use ($data, $recordedBy) {
            $receivedById = $data['received_by_id'] ?? $recordedBy?->id ?? auth()->id();

            $delivery = Delivery::create([
                'consignor_id' => $data['consignor_id'],
                'dr_number' => $data['dr_number'],
                'delivery_date' => $data['delivery_date'],
                'received_by_id' => $receivedById,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['batches'] as $batchData) {
                $quantity = (int) $batchData['quantity_received'];

                /** @var Batch $batch */
                $batch = $delivery->batches()->create([
                    'item_id' => $batchData['item_id'],
                    'lot_number' => $batchData['lot_number'],
                    'expiration_date' => $batchData['expiration_date'],
                    'quantity_received' => $quantity,
                    'quantity_remaining' => $quantity,
                    'unit_cost' => $batchData['unit_cost'],
                    'is_active' => true,
                ]);

                StockMovement::create([
                    'batch_id' => $batch->id,
                    'user_id' => $receivedById,
                    'type' => StockMovement::TYPE_RECEIVE,
                    'quantity' => $quantity,
                    'balance_after' => $quantity,
                    'movement_date' => $delivery->delivery_date,
                    'reference_number' => $delivery->dr_number,
                    'reason' => 'Delivery Receipt #'.$delivery->dr_number,
                ]);
            }

            return $delivery->load(['batches.item', 'consignor', 'receivedBy']);
        });
    }
}
