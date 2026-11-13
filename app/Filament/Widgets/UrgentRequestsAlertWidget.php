<?php

namespace App\Filament\Widgets;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Support\CustodianScope;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class UrgentRequestsAlertWidget extends Widget
{
    protected string $view = 'filament.widgets.urgent-requests-alert';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return CustodianScope::applyToRequests(
            Request::query()
                ->where('is_urgent', true)
                ->where('status', RequestStatus::Pending)
        )->exists();
    }

    /**
     * @return Collection<int, Request>
     */
    public function getPendingUrgentRequests(): Collection
    {
        return CustodianScope::applyToRequests(
            Request::with(['unit', 'requestedBy', 'lines.item'])
                ->where('is_urgent', true)
                ->where('status', RequestStatus::Pending)
                ->orderBy('created_at', 'asc')
        )->get();
    }
}
