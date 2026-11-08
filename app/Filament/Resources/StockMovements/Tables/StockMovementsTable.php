<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Models\StockMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('No Stock Card Records')
            ->emptyStateDescription('Audit history of received shipments, ward issuances, and physical inventory count adjustments will appear here.')
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M j, Y • g:i A')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Movement Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        StockMovement::TYPE_RECEIVE => 'RECEIVE (Inflow)',
                        StockMovement::TYPE_ISSUE => 'ISSUE (Dispatched)',
                        StockMovement::TYPE_ADJUST => 'ADJUST (Physical)',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        StockMovement::TYPE_RECEIVE => 'heroicon-m-arrow-down-tray',
                        StockMovement::TYPE_ISSUE => 'heroicon-m-arrow-up-tray',
                        StockMovement::TYPE_ADJUST => 'heroicon-m-scale',
                        default => 'heroicon-m-arrows-right-left',
                    })
                    ->colors([
                        'success' => StockMovement::TYPE_RECEIVE,
                        'info' => StockMovement::TYPE_ISSUE,
                        'warning' => StockMovement::TYPE_ADJUST,
                    ])
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('batch.item.description')
                    ->label('Consigned Item')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('batch.item.code')
                    ->label('Code')
                    ->badge(),

                TextColumn::make('batch.lot_number')
                    ->label('Lot / Batch #')
                    ->badge()
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Qty Changed')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "+{$state} units" : "{$state} units")
                    ->icon(fn (int $state): string => $state > 0 ? 'heroicon-m-plus' : ($state < 0 ? 'heroicon-m-minus' : 'heroicon-m-minus-small'))
                    ->colors([
                        'success' => fn (int $state): bool => $state > 0,
                        'danger' => fn (int $state): bool => $state < 0,
                        'gray' => fn (int $state): bool => $state === 0,
                    ])
                    ->sortable(),

                TextColumn::make('balance_after')
                    ->label('Running Balance')
                    ->formatStateUsing(fn (int $state): string => number_format($state).' units')
                    ->badge()
                    ->color('teal')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Transacted By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Reason / Destination')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Movement Type')
                    ->options([
                        StockMovement::TYPE_RECEIVE => 'RECEIVE (Shipments Received)',
                        StockMovement::TYPE_ISSUE => 'ISSUE (Dispatched to Units)',
                        StockMovement::TYPE_ADJUST => 'ADJUST (Physical Adjustments)',
                    ]),

                SelectFilter::make('item_id')
                    ->label('Item')
                    ->relationship('batch.item', 'description'),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
