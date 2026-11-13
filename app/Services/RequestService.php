<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestService
{
    public function __construct(
        protected RequestWindowService $windowService
    ) {}

    /**
     * Generate the next unique requisition number.
     * Format: REQ-YYYY-XXXX (e.g. REQ-2026-0001)
     */
    public function generateRequestNumber(): string
    {
        $year = Carbon::now('Asia/Manila')->format('Y');
        $count = Request::whereYear('created_at', $year)->count() + 1;

        do {
            $number = sprintf('REQ-%s-%04d', $year, $count++);
        } while (Request::where('request_number', $number)->exists());

        return $number;
    }

    /**
     * Submit a new requisition from a hospital unit.
     *
     * @param  array{
     *     unit_id?: ?int,
     *     is_urgent?: bool,
     *     urgent_reason?: ?string,
     *     notes?: ?string,
     *     lines: array<int, array{
     *         item_id: int,
     *         quantity_requested: int,
     *     }>
     * }  $data
     */
    public function submitRequest(array $data, User $requester): Request
    {
        $isUrgent = (bool) ($data['is_urgent'] ?? false);

        // Schedule check
        $check = $this->windowService->canSubmitRequest($isUrgent);
        if (! $check['allowed']) {
            throw ValidationException::withMessages([
                'is_urgent' => $check['message'],
            ]);
        }

        if ($isUrgent && (blank($data['urgent_reason'] ?? null) || strlen(trim($data['urgent_reason'])) < 10)) {
            throw ValidationException::withMessages([
                'urgent_reason' => 'A clinical justification (at least 10 characters) is mandatory for STAT requisitions. Do not include patient personal data.',
            ]);
        }

        return DB::transaction(function () use ($data, $requester, $isUrgent) {
            $unitId = $requester->unit_id ?? $data['unit_id'] ?? null;

            if (! $unitId) {
                throw ValidationException::withMessages([
                    'unit_id' => 'A valid hospital unit must be assigned to submit requisitions.',
                ]);
            }

            $request = Request::create([
                'request_number' => $this->generateRequestNumber(),
                'unit_id' => $unitId,
                'requested_by_id' => $requester->id,
                'request_date' => Carbon::now('Asia/Manila'),
                'is_urgent' => $isUrgent,
                'urgent_reason' => $isUrgent ? ($data['urgent_reason'] ?? null) : null,
                'status' => RequestStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] as $lineData) {
                $request->lines()->create([
                    'item_id' => $lineData['item_id'],
                    'quantity_requested' => (int) $lineData['quantity_requested'],
                    'quantity_issued' => 0,
                ]);
            }

            return $request->load(['lines.item', 'unit', 'requestedBy']);
        });
    }

    /**
     * Record a pre-printed paper Downtime Requisition entered within 4 hours of server recovery.
     * Bypasses live schedule-window checks because the slip was physically submitted during downtime.
     *
     * @param  array{
     *     unit_id: int,
     *     requested_by_id?: ?int,
     *     request_date?: string|\DateTimeInterface|null,
     *     downtime_reference?: ?string,
     *     is_urgent?: bool,
     *     urgent_reason?: ?string,
     *     notes?: ?string,
     *     lines: array<int, array{
     *         item_id: int,
     *         quantity_requested: int,
     *     }>
     * }  $data
     */
    public function submitDowntimeRequest(array $data, User $custodian): Request
    {
        $isUrgent = (bool) ($data['is_urgent'] ?? false);

        if ($isUrgent && (blank($data['urgent_reason'] ?? null) || mb_strlen(trim($data['urgent_reason'])) < 10)) {
            throw ValidationException::withMessages([
                'urgent_reason' => 'A clinical justification (at least 10 characters) is mandatory for STAT requisitions. Do not include patient personal data.',
            ]);
        }

        if (empty($data['lines'])) {
            throw ValidationException::withMessages([
                'lines' => 'Please add at least one item to the downtime requisition.',
            ]);
        }

        $slipDate = filled($data['request_date'] ?? null)
            ? Carbon::parse($data['request_date'], 'Asia/Manila')
            : Carbon::now('Asia/Manila');

        if ($slipDate->gt(Carbon::now('Asia/Manila')->addMinutes(5))) {
            throw ValidationException::withMessages([
                'request_date' => 'Downtime slip date/time cannot be in the future.',
            ]);
        }

        return DB::transaction(function () use ($data, $custodian, $isUrgent, $slipDate) {
            $request = Request::create([
                'request_number' => $this->generateRequestNumber(),
                'unit_id' => (int) $data['unit_id'],
                'requested_by_id' => filled($data['requested_by_id'] ?? null) ? (int) $data['requested_by_id'] : $custodian->id,
                'request_date' => $slipDate,
                'is_urgent' => $isUrgent,
                'is_downtime' => true,
                'downtime_reference' => $data['downtime_reference'] ?? null,
                'urgent_reason' => $isUrgent ? trim((string) $data['urgent_reason']) : null,
                'status' => RequestStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] as $lineData) {
                $request->lines()->create([
                    'item_id' => (int) $lineData['item_id'],
                    'quantity_requested' => (int) $lineData['quantity_requested'],
                    'quantity_issued' => 0,
                ]);
            }

            return $request->load(['lines.item', 'unit', 'requestedBy']);
        });
    }

    /**
     * Cancel a pending requisition from the requesting unit portal.
     */
    public function cancelRequest(Request $request, User $requester, ?string $reason = null): Request
    {
        return DB::transaction(function () use ($request, $requester, $reason) {
            /** @var Request $lockedRequest */
            $lockedRequest = Request::where('id', $request->id)->lockForUpdate()->firstOrFail();

            if ($requester->unit_id !== $lockedRequest->unit_id && ! $requester->isAdmin()) {
                throw ValidationException::withMessages([
                    'unit_id' => 'You are only authorized to cancel requisitions for your own unit.',
                ]);
            }

            if (! $lockedRequest->isPending()) {
                throw ValidationException::withMessages([
                    'status' => 'Only pending requisitions that have not been processed can be cancelled.',
                ]);
            }

            $lockedRequest->update([
                'status' => RequestStatus::Cancelled,
                'rejection_reason' => filled($reason) ? trim($reason) : 'Cancelled by requesting unit staff.',
            ]);

            return $lockedRequest->fresh(['lines.item', 'unit', 'requestedBy']);
        });
    }
}
