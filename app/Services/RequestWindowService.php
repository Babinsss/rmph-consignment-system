<?php

namespace App\Services;

use Carbon\Carbon;

class RequestWindowService
{
    /**
     * Check if the regular requisition window is currently open.
     * Regular window: Monday to Friday, closing at 5:00 PM.
     */
    public function isRegularWindowOpen(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now('Asia/Manila');

        // Weekday (Monday = 1, Friday = 5) and before 17:00 (5:00 PM)
        return $now->isWeekday() && $now->hour < 17;
    }

    /**
     * Determine if a requisition can be submitted right now.
     * Urgent (STAT) requests are allowed 24/7.
     * Regular requests are only allowed during the open window.
     *
     * @return array{allowed: bool, message: ?string}
     */
    public function canSubmitRequest(bool $isUrgent = false, ?Carbon $now = null): array
    {
        if ($isUrgent) {
            return [
                'allowed' => true,
                'message' => null,
            ];
        }

        if ($this->isRegularWindowOpen($now)) {
            return [
                'allowed' => true,
                'message' => null,
            ];
        }

        return [
            'allowed' => false,
            'message' => 'The regular requisition window is closed. Regular requests are accepted Monday to Friday before 5:00 PM. For emergency needs, please submit an Urgent (STAT) request.',
        ];
    }

    /**
     * Get a human-readable description of the schedule.
     */
    public function getScheduleSummary(): string
    {
        return 'Monday to Friday (until 5:00 PM) • Urgent STAT 24/7';
    }
}
