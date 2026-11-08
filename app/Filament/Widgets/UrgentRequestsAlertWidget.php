<?php

namespace App\Filament\Widgets;

use App\Enums\RequestStatus;
use App\Models\Request;
use Filament\Widgets\Widget;

class UrgentRequestsAlertWidget extends Widget
{
    protected string $view = 'filament.widgets.urgent-requests-alert';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Request::where('is_urgent', true)
            ->where('status', RequestStatus::Pending)
            ->exists();
    }

    public function getPendingUrgentRequests()
    {
        return Request::with(['unit', 'requestedBy', 'lines.item'])
            ->where('is_urgent', true)
            ->where('status', RequestStatus::Pending)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
