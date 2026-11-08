<?php

namespace App\Filament\Resources\Requests\Pages;

use App\Enums\RequestStatus;
use App\Filament\Resources\Requests\RequestResource;
use App\Models\Request;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    public function getTabs(): array
    {
        $pendingUrgentCount = Request::where('is_urgent', true)
            ->where('status', RequestStatus::Pending)
            ->count();

        $pendingTotalCount = Request::where('status', RequestStatus::Pending)->count();

        return [
            'all' => Tab::make('All Requisitions')
                ->badge(Request::count()),

            'urgent' => Tab::make('🚨 Urgent (STAT)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_urgent', true))
                ->badge($pendingUrgentCount > 0 ? (string) $pendingUrgentCount : null)
                ->badgeColor('danger'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Pending))
                ->badge($pendingTotalCount > 0 ? (string) $pendingTotalCount : null)
                ->badgeColor('warning'),

            'fulfilled' => Tab::make('Fulfilled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Fulfilled))
                ->badge(Request::where('status', RequestStatus::Fulfilled)->count())
                ->badgeColor('success'),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Rejected))
                ->badge(Request::where('status', RequestStatus::Rejected)->count()),
        ];
    }
}
