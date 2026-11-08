<?php

namespace App\Filament\Portal\Widgets;

use App\Enums\RequestStatus;
use App\Models\Item;
use App\Models\Request;
use App\Services\RequestWindowService;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class UnitInfoWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.unit-info-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getUnitData(): array
    {
        $user = auth()->user();
        $now = Carbon::now('Asia/Manila');
        $windowService = app(RequestWindowService::class);
        $isWindowOpen = $windowService->isRegularWindowOpen($now);
        $scheduleSummary = $windowService->getScheduleSummary();

        $recentRequests = Request::with('lines.item')
            ->where('unit_id', $user?->unit_id)
            ->orderBy('request_date', 'desc')
            ->limit(5)
            ->get();

        return [
            'userName' => $user?->name ?? 'Staff',
            'unitName' => $user?->unit?->name ?? 'Hospital Unit',
            'unitCode' => $user?->unit?->code ?? 'UNIT',
            'isWindowOpen' => $isWindowOpen,
            'scheduleSummary' => $scheduleSummary,
            'currentTime' => $now->format('l, F j, Y • g:i A'),
            'totalItemsCount' => Item::where('is_active', true)->count(),
            'pendingRequestsCount' => Request::where('unit_id', $user?->unit_id)->where('status', RequestStatus::Pending)->count(),
            'totalRequestsCount' => Request::where('unit_id', $user?->unit_id)->count(),
            'recentRequests' => $recentRequests,
        ];
    }
}
