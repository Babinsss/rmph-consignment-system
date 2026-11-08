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
}
