<?php

namespace App\Filament\Resources\Items\Tables;

use App\Enums\ItemCategory;
use App\Models\Item;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('description', 'asc')
            ->emptyStateHeading('No Consigned Items Found')
            ->emptyStateDescription('Medical supplies and drugs in the central consignment depot will be listed here. Click "New Item" to register an item in the catalog.')
            ->emptyStateIcon('heroicon-o-archive-box')
            ->columns([
                TextColumn::make('code')
                    ->label('Item Code')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
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
                TextColumn::make('minimum_level')
                    ->label('Min. Level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_stock')
                    ->label('Depot Stock Level')
                    ->state(fn (Item $record): int => $record->current_stock)
                    ->badge()
                    ->formatStateUsing(function (Item $record): string {
                        if ($record->is_low_stock) {
                            return "Below Min ({$record->current_stock})";
                        }

                        return "Stock OK ({$record->current_stock})";
                    })
                    ->icon(fn (Item $record): string => $record->is_low_stock ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                    ->color(fn (Item $record): string => $record->is_low_stock ? 'danger' : 'success')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(ItemCategory::class),
                SelectFilter::make('consignor_id')
                    ->label('Consignor')
                    ->relationship('consignor', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Items Only')
                    ->falseLabel('Inactive Items Only'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
