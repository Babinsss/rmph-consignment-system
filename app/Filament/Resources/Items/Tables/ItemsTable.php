<?php

namespace App\Filament\Resources\Items\Tables;

use App\Enums\ItemCategory;
use App\Models\Item;
use App\Support\CustodianScope;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => CustodianScope::applyToItems(
                $query->with(['consignor', 'batches.delivery.consignor'])
            ))
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
                    ->sortable()
                    ->description(function (Item $record): ?string {
                        $otherConsignors = $record->batches
                            ->where('quantity_remaining', '>', 0)
                            ->filter(fn ($b) => $b->delivery && $b->delivery->consignor_id !== $record->consignor_id)
                            ->map(fn ($b) => $b->delivery?->consignor?->name)
                            ->unique()
                            ->filter();

                        if ($otherConsignors->isNotEmpty()) {
                            return 'Also stocked from: '.$otherConsignors->implode(', ');
                        }

                        return null;
                    }),
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
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Items Only')
                    ->falseLabel('Inactive Items Only'),
            ])
            ->recordActions([
                Action::make('view_batches')
                    ->label('Batches')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->modalHeading(fn (Item $record) => "[{$record->code}] {$record->description} — Depot Batches")
                    ->modalDescription('Active consignment stock batches currently held in the central depot.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Item $record) => view('filament.resources.items.modals.stock-batches', ['item' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
