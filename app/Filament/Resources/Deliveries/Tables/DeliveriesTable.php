<?php

namespace App\Filament\Resources\Deliveries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('delivery_number')
                    ->label('DR Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('supplier.name')
                    ->label('Consignor')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('delivery_date')
                    ->label('Delivered On')
                    ->date()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('total_cost')
                    ->label('Total Value')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('receivedBy.name')
                    ->label('Received By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('po_reference')
                    ->label('PO Ref')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('delivery_date', 'desc')
            ->filters([
                SelectFilter::make('department')
                    ->options([
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                    ]),
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
