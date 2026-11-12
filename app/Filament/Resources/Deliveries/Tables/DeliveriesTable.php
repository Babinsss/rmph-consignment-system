<?php

namespace App\Filament\Resources\Deliveries\Tables;

use App\Models\Delivery;
use App\Support\CustodianScope;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('delivery_date', 'desc')
            ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => CustodianScope::applyToDeliveries($query))
            ->emptyStateHeading('No Consignment Deliveries Recorded')
            ->emptyStateDescription('When an accredited supplier arrives at the hospital depot, click "New Delivery" above to log the Delivery Receipt (DR) and register received batches.')
            ->emptyStateIcon('heroicon-o-truck')
            ->columns([
                TextColumn::make('dr_number')
                    ->label('DR Number')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('consignor.name')
                    ->label('Consignor (Supplier)')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('batches_count')
                    ->label('Batches')
                    ->counts('batches')
                    ->badge()
                    ->sortable(),

                TextColumn::make('total_units')
                    ->label('Total Units')
                    ->state(fn (Delivery $record) => $record->total_units)
                    ->formatStateUsing(fn ($state) => number_format((int) $state).' units')
                    ->badge()
                    ->color('teal')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Value')
                    ->state(fn (Delivery $record) => $record->total_amount)
                    ->money('PHP')
                    ->visible(fn () => auth()->user()?->canViewUnitCosts() ?? false),

                TextColumn::make('receivedBy.name')
                    ->label('Received By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('consignor_id')
                    ->label('Consignor')
                    ->relationship('consignor', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
