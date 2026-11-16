<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hospital Requisition Schedule Window
    |--------------------------------------------------------------------------
    |
    | Regular unit requisitions are accepted during these ISO-8601 weekdays
    | (1 = Monday ... 7 = Sunday) before the configured cutoff hour in the
    | hospital timezone. Urgent (STAT) requests are accepted 24/7.
    |
    */
    'timezone' => env('CONSIGNMENT_TIMEZONE', 'Asia/Manila'),

    'regular_window_days' => [1, 2, 3, 4, 5], // Monday to Friday

    'regular_window_cutoff_hour' => (int) env('CONSIGNMENT_CUTOFF_HOUR', 17), // 5:00 PM

    'holidays' => [
        // 'YYYY-MM-DD',
    ],
];
