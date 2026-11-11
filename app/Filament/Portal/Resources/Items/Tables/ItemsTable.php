<?php

namespace App\Filament\Portal\Resources\Items\Tables;

use App\Enums\ItemCategory;
use App\Models\Item;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['consignor', 'batches.delivery.consignor']))
            ->striped()
            ->defaultSort('description', 'asc')
            ->emptyStateHeading('No Consignment Items Available')
            ->emptyStateDescription('Active medical supplies and drugs in the central consignment depot will be listed here.')
            ->emptyStateIcon('heroicon-o-archive-box')
            ->columns([
                TextColumn::make('code')
                    ->label('Item Code')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Item Description')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('unit_of_measure')
                    ->label('Unit of Measure')
                    ->sortable(),
                TextColumn::make('consignor.name')
                    ->label('Consignor (Supplier)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('current_stock')
                    ->label('Depot Stock')
                    ->state(fn (Item $record): int => $record->current_stock)
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "Stock: {$state}" : 'Out of Stock')
                    ->icon(fn (int $state): string => $state > 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(ItemCategory::class),
                SelectFilter::make('consignor_id')
                    ->label('Consignor')
                    ->relationship('consignor', 'name')
                    ->query(function (Builder $query, array $data): Builder {
                        $consignorId = $data['value'] ?? null;
                        if (! $consignorId) {
                            return $query;
                        }

                        return $query->where(function (Builder $q) use ($consignorId) {
                            $q->where('consignor_id', $consignorId)
                                ->orWhereHas('batches.delivery', function (Builder $dq) use ($consignorId) {
                                    $dq->where('consignor_id', $consignorId);
                                });
                        });
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
