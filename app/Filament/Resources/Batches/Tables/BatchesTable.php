<?php

namespace App\Filament\Resources\Batches\Tables;

use App\Enums\ItemCategory;
use App\Models\Batch;
use App\Services\FulfillmentService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('expiration_date', 'asc')
            ->emptyStateHeading('No Consignment Batches in Depot')
            ->emptyStateDescription('Batches created from confirmed Delivery Receipts (DR) will appear here with lot numbers, expiration tracking, and live depot stock.')
            ->emptyStateIcon('heroicon-o-circle-stack')
            ->columns([
                TextColumn::make('lot_number')
                    ->label('Lot / Batch #')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.description')
                    ->label('Item Description')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('item.code')
                    ->label('Item Code')
                    ->badge()
                    ->sortable(),

                TextColumn::make('item.category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),

                TextColumn::make('delivery.dr_number')
                    ->label('DR Number')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('delivery.consignor.name')
                    ->label('Consignor (Supplier)')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('expiration_date')
                    ->label('Expiration Date')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('expiry_status')
                    ->label('Expiry Status')
                    ->badge()
                    ->state(function (Batch $record): string {
                        if ($record->is_expired) {
                            return 'Expired';
                        }
                        if ($record->is_expiring_soon) {
                            $days = Carbon::today('Asia/Manila')->diffInDays($record->expiration_date, false);

                            return "Expires in {$days}d (< 90d)";
                        }

                        return 'OK (> 90d)';
                    })
                    ->icon(function (Batch $record): string {
                        if ($record->is_expired) {
                            return 'heroicon-m-x-circle';
                        }
                        if ($record->is_expiring_soon) {
                            return 'heroicon-m-exclamation-triangle';
                        }

                        return 'heroicon-m-check-circle';
                    })
                    ->color(function (Batch $record): string {
                        if ($record->is_expired) {
                            return 'danger';
                        }
                        if ($record->is_expiring_soon) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('expiration_date', $direction)),

                TextColumn::make('quantity_remaining')
                    ->label('Stock on Hand')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state} units" : 'Depleted (0)')
                    ->icon(fn (int $state): string => $state > 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->description(fn (Batch $record) => "of {$record->quantity_received} received")
                    ->sortable(),

                TextColumn::make('unit_cost')
                    ->label('Unit Cost')
                    ->money('PHP')
                    ->visible(fn () => auth()->user()?->canViewUnitCosts() ?? false)
                    ->sortable(),

                TextColumn::make('remaining_total')
                    ->label('Remaining Value')
                    ->state(fn (Batch $record) => $record->remaining_total)
                    ->money('PHP')
                    ->visible(fn () => auth()->user()?->canViewUnitCosts() ?? false)
                    ->sortable(),
            ])
            ->filters([
                Filter::make('in_stock')
                    ->label('In Stock Only (Qty > 0)')
                    ->query(fn (Builder $query) => $query->where('quantity_remaining', '>', 0))
                    ->default(),

                Filter::make('expiring_soon')
                    ->label('Near Expiry (Within 90 Days)')
                    ->query(fn (Builder $query) => $query->expiringSoon(90)),

                Filter::make('expired')
                    ->label('Expired Batches')
                    ->query(fn (Builder $query) => $query->expired()),

                SelectFilter::make('category')
                    ->label('Category')
                    ->options(ItemCategory::class)
                    ->query(fn (Builder $query, array $data) => filled($data['value']) ? $query->whereHas('item', fn ($q) => $q->where('category', $data['value'])) : $query),

                SelectFilter::make('consignor_id')
                    ->label('Consignor')
                    ->relationship('delivery.consignor', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('adjustStock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin', 'custodian']) ?? false)
                    ->modalHeading(fn (Batch $record): string => "Physical Count Adjustment: Lot #{$record->lot_number}")
                    ->modalDescription(fn (Batch $record): string => "Current system stock: {$record->quantity_remaining} units of {$record->item->description}. Enter the verified physical count and mandatory audit reason.")
                    ->schema([
                        TextInput::make('new_quantity')
                            ->label('Verified Physical Count on Hand')
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (Batch $record): int => $record->quantity_remaining)
                            ->required(),
                        Textarea::make('reason')
                            ->label('Mandatory Adjustment Reason')
                            ->placeholder('e.g. Weekly physical inventory count discrepancy, broken ampule during shelving, etc.')
                            ->required(),
                    ])
                    ->action(function (Batch $record, array $data): void {
                        $fulfillmentService = app(FulfillmentService::class);
                        $updated = $fulfillmentService->adjustStock($record, (int) $data['new_quantity'], $data['reason'], auth()->user());

                        Notification::make()
                            ->title('Stock Adjusted')
                            ->body("Batch {$updated->lot_number} balance updated to {$updated->quantity_remaining}. Immutable stock movement logged.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
