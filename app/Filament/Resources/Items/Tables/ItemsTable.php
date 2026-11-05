<?php

namespace App\Filament\Resources\Items\Tables;

use App\Models\DeliveryItem;
use App\Models\Item;
use App\Services\InventoryService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_code')
                    ->label('Item Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Item $record): ?string => $record->generic_name ? "Generic: {$record->generic_name}" : null),
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
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uom')
                    ->label('UOM')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('current_stock')
                    ->label('Available Stock')
                    ->badge()
                    ->getStateUsing(fn (Item $record): int => $record->current_stock)
                    ->color(fn (Item $record): string => match ($record->stock_status) {
                        'out_of_stock' => 'danger',
                        'low_stock' => 'warning',
                        default => 'success',
                    })
                    ->suffix(fn (Item $record): string => " {$record->uom}")
                    ->sortable(false),
                TextColumn::make('minimum_stock_level')
                    ->label('Min Level')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('total_valuation')
                    ->label('Stock Value')
                    ->money('PHP')
                    ->getStateUsing(fn (Item $record): float => $record->total_valuation)
                    ->sortable(false),
                TextColumn::make('supplier.name')
                    ->label('Consignor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department Stream')
                    ->options([
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                    ]),
                Filter::make('low_stock')
                    ->label('Low Stock / Depleted Only')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('deliveryItems', function ($q) {
                            $q->where('status', 'available');
                        }, '<=', 1);
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('stockcard')
                    ->label('Stockcard')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('info')
                    ->url(fn (Item $record): string => route('stockcard.print', ['item' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('adjust')
                    ->label('Adjust')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->color('warning')
                    ->modalHeading(fn (Item $record): string => "Stock Adjustment: {$record->name}")
                    ->modalDescription('Record breakage, spoilage, physical inventory count variance, or returns to consignor.')
                    ->modalSubmitActionLabel('Confirm Adjustment')
                    ->form([
                        Select::make('delivery_item_id')
                            ->label('Affected Batch / Lot')
                            ->options(function (Item $record): array {
                                return $record->deliveryItems()
                                    ->where('quantity_remaining', '>', 0)
                                    ->get()
                                    ->mapWithKeys(function (DeliveryItem $batch): array {
                                        $exp = $batch->expiry_date ? " (Exp: {$batch->expiry_date->format('M Y')})" : '';

                                        return [$batch->id => "Lot {$batch->lot_number}{$exp} - {$batch->quantity_remaining} available"];
                                    })
                                    ->toArray();
                            })
                            ->required(),
                        Select::make('movement_type')
                            ->label('Adjustment Type')
                            ->options([
                                'ADJUSTMENT' => 'Physical Variance / Breakage / Spoilage',
                                'RETURN_TO_SUPPLIER' => 'Return Defective / Recalled to Consignor',
                            ])
                            ->default('ADJUSTMENT')
                            ->required(),
                        Select::make('direction')
                            ->label('Direction')
                            ->options([
                                'deduct' => 'Deduct (Loss, Damage, or Return)',
                                'add' => 'Add (Surplus / Reversal)',
                            ])
                            ->default('deduct')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Adjustment Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        Textarea::make('reason')
                            ->label('Clinical / Audit Reason')
                            ->placeholder('e.g. Broken ampoule during ward transfer, or periodic inventory reconciliation.')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (Item $record, array $data, InventoryService $inventoryService): void {
                        $batch = DeliveryItem::findOrFail($data['delivery_item_id']);
                        $isDeduction = $data['direction'] === 'deduct';
                        $quantity = (int) $data['quantity'];

                        try {
                            $movement = $inventoryService->adjustStock(
                                item: $record,
                                batch: $batch,
                                quantity: $quantity,
                                type: $data['movement_type'],
                                isDeduction: $isDeduction,
                                reason: $data['reason'],
                                actor: auth()->user()
                            );

                            Notification::make()
                                ->title('Stock Adjustment Recorded')
                                ->body("Recorded {$movement->movement_type} of {$quantity} {$record->uom} for Lot {$batch->lot_number}.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Adjustment Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('bincard')
                    ->label('Bin Card')
                    ->icon('heroicon-o-tag')
                    ->color('gray')
                    ->url(fn (Item $record): string => route('bincard.print', $record))
                    ->openUrlInNewTab(),
                Action::make('stockcard')
                    ->label('Stockcard')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('info')
                    ->url(fn (Item $record): string => route('stockcard.print', $record))
                    ->openUrlInNewTab(),
                Action::make('usage')
                    ->label('Item Usage')
                    ->icon('heroicon-o-chart-bar')
                    ->color('gray')
                    ->url(fn (Item $record): string => route('item-usage.print', $record))
                    ->openUrlInNewTab(),
                Action::make('rapc')
                    ->label('RAPC')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('warning')
                    ->url(fn (Item $record): string => route('rapc.print', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('critical_pr')
                    ->label('Critical PR (App. 47)')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('teal')
                    ->url(fn (): string => route('purchase-request.critical'))
                    ->openUrlInNewTab(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
