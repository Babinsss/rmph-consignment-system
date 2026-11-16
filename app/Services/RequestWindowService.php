<?php

namespace App\Services;

use Carbon\Carbon;

class RequestWindowService
{
    /**
     * Check if the regular requisition window is currently open.
     * Regular window: Configured weekdays (default Monday to Friday) before cutoff hour (default 5:00 PM),
     * excluding configured hospital holidays.
     */
    public function isRegularWindowOpen(?Carbon $now = null): bool
    {
        $timezone = config('consignment.timezone', 'Asia/Manila');
        $now = ($now ?? Carbon::now($timezone))->copy()->setTimezone($timezone);

        $allowedDays = config('consignment.regular_window_days', [1, 2, 3, 4, 5]);
        $cutoffHour = (int) config('consignment.regular_window_cutoff_hour', 17);
        $holidays = config('consignment.holidays', []);

        if (in_array($now->toDateString(), $holidays, true)) {
            return false;
        }

        return in_array($now->dayOfWeekIso, $allowedDays, true) && $now->hour < $cutoffHour;
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
