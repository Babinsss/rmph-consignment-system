<?php

namespace App\Filament\Resources\Requests\Pages;

use App\Enums\RequestStatus;
use App\Filament\Resources\Requests\RequestResource;
use App\Models\HospitalUnit;
use App\Models\Item;
use App\Models\Request;
use App\Models\User;
use App\Services\RequestService;
use App\Support\CustodianScope;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('record_downtime')
                ->label('Record Downtime Slip')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs']) ?? false)
                ->modalHeading('Record Manual / Downtime Requisition Slip')
                ->modalDescription('Use this only for physical paper requisitions submitted when the portal or schedule window was unavailable.')
                ->modalWidth('3xl')
                ->schema([
                    Select::make('unit_id')
                        ->label('Requesting Hospital Unit')
                        ->options(fn () => HospitalUnit::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('requested_by_id')
                        ->label('Requested By (Ward Nurse / Pharmacist)')
                        ->options(fn () => User::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    DateTimePicker::make('request_date')
                        ->label('Paper Slip Date & Time')
                        ->default(fn () => now('Asia/Manila'))
                        ->maxDate(fn () => now('Asia/Manila')->addMinutes(5))
                        ->required(),

                    TextInput::make('downtime_reference')
                        ->label('Paper Slip / Downtime Reference #')
                        ->maxLength(100)
                        ->placeholder('e.g. RIS-MANUAL-0042'),

                    Toggle::make('is_urgent')
                        ->label('Urgent (STAT) Emergency')
                        ->live()
                        ->default(false),

                    Textarea::make('urgent_reason')
                        ->label('Clinical / STAT Reason')
                        ->required(fn (Get $get): bool => (bool) $get('is_urgent'))
                        ->visible(fn (Get $get): bool => (bool) $get('is_urgent'))
                        ->rows(2),

                    Textarea::make('notes')
                        ->label('Remarks / Downtime Context')
                        ->rows(2),

                    Repeater::make('lines')
                        ->label('Requested Consigned Items')
                        ->minItems(1)
                        ->defaultItems(1)
                        ->columns(4)
                        ->schema([
                            Select::make('item_id')
                                ->label('Item')
                                ->options(function () {
                                    return CustodianScope::applyToItems(
                                        Item::query()->where('is_active', true)->orderBy('description')
                                    )
                                        ->get()
                                        ->mapWithKeys(fn (Item $item) => [
                                            $item->id => "[{$item->code}] {$item->description} ({$item->unit_of_measure})",
                                        ]);
                                })
                                ->searchable()
                                ->required()
                                ->columnSpan(3),

                            TextInput::make('quantity_requested')
                                ->label('Qty')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->columnSpan(1),
                        ]),
                ])
                ->action(function (array $data): void {
                    try {
                        $request = app(RequestService::class)->submitDowntimeRequest($data, auth()->user());
                    } catch (ValidationException|InvalidArgumentException $exception) {
                        Notification::make()
                            ->title('Cannot Record Downtime Slip')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Downtime Requisition Recorded')
                        ->body("Requisition {$request->request_number} was created and is ready for FEFO fulfillment.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        $baseQuery = fn () => CustodianScope::applyToRequests(Request::query());

        $totalCount = $baseQuery()->count();

        $pendingUrgentCount = $baseQuery()
            ->where('is_urgent', true)
            ->where('status', RequestStatus::Pending)
            ->count();

        $pendingTotalCount = $baseQuery()
            ->where('status', RequestStatus::Pending)
            ->count();

        $fulfilledCount = $baseQuery()
            ->where('status', RequestStatus::Fulfilled)
            ->count();

        $rejectedCount = $baseQuery()
            ->where('status', RequestStatus::Rejected)
            ->count();

        $cancelledCount = $baseQuery()
            ->where('status', RequestStatus::Cancelled)
            ->count();

        return [
            'all' => Tab::make('All Requisitions')
                ->badge($totalCount),

            'urgent' => Tab::make('Urgent (STAT)')
                ->icon('heroicon-m-bolt')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_urgent', true))
                ->badge($pendingUrgentCount > 0 ? (string) $pendingUrgentCount : null)
                ->badgeColor('danger'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Pending))
                ->badge($pendingTotalCount > 0 ? (string) $pendingTotalCount : null)
                ->badgeColor('warning'),

            'fulfilled' => Tab::make('Fulfilled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Fulfilled))
                ->badge($fulfilledCount)
                ->badgeColor('success'),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Rejected))
                ->badge($rejectedCount),

            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequestStatus::Cancelled))
                ->badge($cancelledCount),
        ];
    }
}
