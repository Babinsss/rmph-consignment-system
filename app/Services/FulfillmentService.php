<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\Batch;
use App\Models\Request;
use App\Models\RequestLine;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FulfillmentService
{
    /**
     * Get First-Expiry-First-Out (FEFO) batch allocation recommendations for a requisition.
     *
     * @return array<int, array{
     *     line_id: int,
     *     item_id: int,
     *     item_code: string,
     *     item_description: string,
     *     quantity_requested: int,
     *     quantity_issued: int,
     *     pending_quantity: int,
     *     batches: array<int, array{
     *         batch_id: int,
     *         lot_number: string,
     *         expiration_date: string,
     *         days_until_expiration: int,
     *         quantity_available: int,
     *         suggested_issue_quantity: int,
     *     }>,
     *     total_suggested: int,
     *     has_sufficient_stock: bool,
     * }>
     */
    public function getFefoRecommendations(Request $request, ?User $custodian = null): array
    {
        $today = Carbon::today('Asia/Manila');
        $scopedCategory = $custodian?->custodianCategory();
        $request->loadMissing('lines.item');
        $recommendations = [];

        foreach ($request->lines as $line) {
            if ($scopedCategory !== null && $line->item->category !== $scopedCategory) {
                continue;
            }

            $pending = $line->pending_quantity;

            // Fetch available unexpired batches ordered by earliest expiration date first (FEFO)
            $availableBatches = Batch::where('item_id', $line->item_id)
                ->where('quantity_remaining', '>', 0)
                ->where('is_active', true)
                ->where('expiration_date', '>=', $today)
                ->orderBy('expiration_date', 'asc')
                ->get();

            $batchAllocations = [];
            $needed = $pending;
            $totalSuggested = 0;

            foreach ($availableBatches as $batch) {
                $qtyAvailable = $batch->quantity_remaining;
                $allocate = min($needed, $qtyAvailable);

                $batchAllocations[] = [
                    'batch_id' => $batch->id,
                    'lot_number' => $batch->lot_number,
                    'expiration_date' => $batch->expiration_date->format('M j, Y'),
                    'days_until_expiration' => (int) $today->diffInDays($batch->expiration_date, false),
                    'quantity_available' => $qtyAvailable,
                    'suggested_issue_quantity' => $allocate,
                ];

                $totalSuggested += $allocate;
                $needed -= $allocate;
            }

            $recommendations[$line->id] = [
                'line_id' => $line->id,
                'item_id' => $line->item_id,
                'item_code' => $line->item->code,
                'item_description' => $line->item->description,
                'quantity_requested' => $line->quantity_requested,
                'quantity_issued' => $line->quantity_issued,
                'pending_quantity' => $pending,
                'batches' => $batchAllocations,
                'total_suggested' => $totalSuggested,
                'has_sufficient_stock' => $totalSuggested >= $pending,
            ];
        }

        return $recommendations;
    }

    /**
     * Fulfill a requisition by issuing stock from selected batches using database transactions and row locking.
     *
     * @param  array<int, array{batch_id: int, quantity: int, line_id: int}>  $allocations
     */
    public function fulfillRequest(Request $request, array $allocations, User $custodian): Request
    {
        return DB::transaction(function () use ($request, $allocations, $custodian) {
            // Lock the request row
            /** @var Request $lockedRequest */
            $lockedRequest = Request::where('id', $request->id)->lockForUpdate()->firstOrFail();

            if ($lockedRequest->isFulfilled() || $lockedRequest->isRejected() || $lockedRequest->isCancelled()) {
                throw ValidationException::withMessages([
                    'status' => 'This requisition has already been completed, rejected, or cancelled.',
                ]);
            }

            $now = Carbon::now('Asia/Manila');
            $today = $now->copy()->startOfDay();
            $scopedCategory = $custodian->custodianCategory();
            $lineAllocatedTotals = [];

            foreach ($allocations as $allocation) {
                $quantityToIssue = (int) ($allocation['quantity'] ?? 0);

                if ($quantityToIssue <= 0) {
                    continue;
                }

                $batchId = (int) $allocation['batch_id'];
                $lineId = (int) $allocation['line_id'];

                // Lock the request line first and enforce ownership, category scope, and no over-issuance
                /** @var RequestLine $line */
                $line = RequestLine::with('item')->where('id', $lineId)->lockForUpdate()->firstOrFail();

                if ($line->request_id !== $lockedRequest->id) {
                    throw ValidationException::withMessages([
                        'allocations' => 'Invalid requisition line reference.',
                    ]);
                }

                if ($scopedCategory !== null && $line->item->category !== $scopedCategory) {
                    throw ValidationException::withMessages([
                        'allocations' => "You are only authorized to fulfill {$scopedCategory->getLabel()} items.",
                    ]);
                }

                $lineAllocatedTotals[$lineId] = ($lineAllocatedTotals[$lineId] ?? 0) + $quantityToIssue;
                if ($lineAllocatedTotals[$lineId] > $line->pending_quantity) {
                    throw ValidationException::withMessages([
                        'allocations' => "Cannot issue {$lineAllocatedTotals[$lineId]} units of {$line->item->description}; only {$line->pending_quantity} units remain pending.",
                    ]);
                }

                // Lock the batch row to prevent race conditions
                /** @var Batch $batch */
                $batch = Batch::where('id', $batchId)->lockForUpdate()->firstOrFail();

                if ($batch->item_id !== $line->item_id) {
                    throw ValidationException::withMessages([
                        'allocations' => "Batch {$batch->lot_number} does not belong to {$line->item->description}.",
                    ]);
                }

                if (! $batch->is_active || $batch->expiration_date->lt($today)) {
                    throw ValidationException::withMessages([
                        'allocations' => "Cannot issue expired or inactive batch {$batch->lot_number}.",
                    ]);
                }

                if ($batch->quantity_remaining < $quantityToIssue) {
                    throw ValidationException::withMessages([
                        'allocations' => "Insufficient stock in batch {$batch->lot_number}. Available: {$batch->quantity_remaining}, requested to issue: {$quantityToIssue}.",
                    ]);
                }

                // Decrement batch stock
                $batch->decrement('quantity_remaining', $quantityToIssue);
                $balanceAfter = $batch->quantity_remaining;

                // Log immutable stock movement
                StockMovement::create([
                    'batch_id' => $batch->id,
                    'user_id' => $custodian->id,
                    'type' => StockMovement::TYPE_ISSUE,
                    'quantity' => -$quantityToIssue,
                    'balance_after' => $balanceAfter,
                    'movement_date' => $now->toDateString(),
                    'reference_number' => $lockedRequest->request_number,
                    'reason' => "Issued to {$lockedRequest->unit->name} via Requisition #{$lockedRequest->request_number}",
                ]);

                // Increment issued quantity on the request line
                $line->increment('quantity_issued', $quantityToIssue);
            }

            // Determine final status
            $lockedRequest->load('lines');
            $allFulfilled = $lockedRequest->lines->every(fn (RequestLine $l) => $l->quantity_issued >= $l->quantity_requested);
            $anyIssued = $lockedRequest->lines->some(fn (RequestLine $l) => $l->quantity_issued > 0);

            $status = match (true) {
                $allFulfilled => RequestStatus::Fulfilled,
                $anyIssued => RequestStatus::PartiallyFulfilled,
                default => RequestStatus::Pending,
            };

            $lockedRequest->update([
                'status' => $status,
                'fulfilled_by_id' => $custodian->id,
                'fulfilled_at' => $now,
            ]);

            return $lockedRequest->fresh(['lines.item', 'unit', 'requestedBy', 'fulfilledBy']);
        });
    }

    /**
     * Reject a pending requisition with category-scope safety.
     */
    public function rejectRequest(Request $request, string $reason, User $custodian): Request
    {
        if (blank($reason)) {
            throw ValidationException::withMessages([
                'rejection_reason' => 'A reason is required to reject a requisition.',
            ]);
        }

        return DB::transaction(function () use ($request, $reason, $custodian) {
            /** @var Request $lockedRequest */
            $lockedRequest = Request::with('lines.item')->where('id', $request->id)->lockForUpdate()->firstOrFail();

            if (! $lockedRequest->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Only pending requisitions can be rejected.',
                ]);
            }

            $scopedCategory = $custodian->custodianCategory();
            if ($scopedCategory !== null) {
                $hasOtherCategory = $lockedRequest->lines->some(fn (RequestLine $l) => $l->item?->category !== $scopedCategory);
                if ($hasOtherCategory) {
                    throw ValidationException::withMessages([
                        'status' => 'This requisition includes items from another category and must be reviewed with an administrator or general custodian.',
                    ]);
                }
            }

            $lockedRequest->update([
                'status' => RequestStatus::Rejected,
                'rejection_reason' => trim($reason),
            ]);

            return $lockedRequest->fresh(['lines.item', 'unit', 'requestedBy']);
        });
    }

    /**
     * Adjust physical batch stock (for breakage, count difference, correction) with row locking and mandatory reason.
     */
    public function adjustStock(Batch $batch, int $newQuantity, string $reason, User $custodian): Batch
    {
        if (blank($reason)) {
            throw ValidationException::withMessages([
                'reason' => 'A mandatory reason is required for any physical stock adjustment.',
            ]);
        }

        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'new_quantity' => 'Batch stock quantity cannot be negative.',
            ]);
        }

        return DB::transaction(function () use ($batch, $newQuantity, $reason, $custodian) {
            /** @var Batch $lockedBatch */
            $lockedBatch = Batch::with('item')->where('id', $batch->id)->lockForUpdate()->firstOrFail();

            $scopedCategory = $custodian->custodianCategory();
            if ($scopedCategory !== null && $lockedBatch->item?->category !== $scopedCategory) {
                throw ValidationException::withMessages([
                    'batch' => "You are only authorized to adjust {$scopedCategory->getLabel()} batches.",
                ]);
            }

            $diff = $newQuantity - $lockedBatch->quantity_remaining;

            if ($diff === 0) {
                return $lockedBatch;
            }

            $lockedBatch->update([
                'quantity_remaining' => $newQuantity,
            ]);

            $now = Carbon::now('Asia/Manila');
            $ref = 'ADJ-'.Carbon::now()->format('YmdHis');

            StockMovement::create([
                'batch_id' => $lockedBatch->id,
                'user_id' => $custodian->id,
                'type' => StockMovement::TYPE_ADJUST,
                'quantity' => $diff,
                'balance_after' => $newQuantity,
                'movement_date' => $now->toDateString(),
                'reference_number' => $ref,
                'reason' => $reason,
            ]);

            return $lockedBatch->fresh();
        });
    }
}
