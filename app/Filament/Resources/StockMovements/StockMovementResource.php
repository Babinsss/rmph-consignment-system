<?php

namespace App\Filament\Resources\StockMovements;

use App\Filament\Resources\StockMovements\Pages\ManageStockMovements;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Reports & Stockcards';

    protected static ?string $navigationLabel = 'Audit Movement Ledger';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['item', 'user', 'deliveryItem'])->latest('id');
        $user = Auth::user();

        if ($user && $user->isPharmacyStaff()) {
            $query->where('department', 'pharmacy');
        } elseif ($user && $user->isMedicalSupplyStaff()) {
            $query->where('department', 'medical_supplies');
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable()
                    ->description(fn (StockMovement $record): string => $record->item?->item_code ?? ''),
                TextColumn::make('department')
                    ->label('Stream')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pharmacy' => 'info',
                        'medical_supplies' => 'teal',
                        default => 'gray',
                    }),
                TextColumn::make('movement_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'DELIVERY_IN' => '📥 Inward Delivery',
                        'REQUEST_OUT' => '📤 Request Issued',
                        'ADJUSTMENT' => '⚙️ Adjustment',
                        'RETURN_TO_SUPPLIER' => '↺ Supplier Return',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'DELIVERY_IN' => 'success',
                        'REQUEST_OUT' => 'warning',
                        'ADJUSTMENT' => 'info',
                        'RETURN_TO_SUPPLIER' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('lot_number')
                    ->label('Lot #')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('expiry_date')
                    ->label('Expiry')
                    ->date('M Y')
                    ->placeholder('N/A')
                    ->toggleable(),
                TextColumn::make('source_destination')
                    ->label('Source / Destination')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('quantity_in')
                    ->label('In (+)')
                    ->numeric()
                    ->color('success')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : '—'),
                TextColumn::make('quantity_out')
                    ->label('Out (-)')
                    ->numeric()
                    ->color('warning')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "-{$state}" : '—'),
                TextColumn::make('balance_after')
                    ->label('Balance')
                    ->numeric()
                    ->weight('bold'),
                TextColumn::make('unit_cost')
                    ->label('Cost')
                    ->money('PHP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_cost')
                    ->label('Total Value')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Custodian')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('movement_type')
                    ->label('Movement Type')
                    ->options([
                        'DELIVERY_IN' => 'Inward Delivery',
                        'REQUEST_OUT' => 'Request Issued',
                        'ADJUSTMENT' => 'Adjustment',
                        'RETURN_TO_SUPPLIER' => 'Return to Consignor',
                    ]),
                SelectFilter::make('department')
                    ->label('Department Stream')
                    ->options([
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                    ]),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('Date From'),
                        DatePicker::make('until')->label('Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockMovements::route('/'),
        ];
    }
}
