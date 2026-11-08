<?php

namespace App\Filament\Resources\Consignors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ConsignorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('No Consignors Registered')
            ->emptyStateDescription('Accredited pharmaceutical and medical suppliers will be listed here. Click "New Consignor" above to add a partner.')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->columns([
                TextColumn::make('name')
                    ->label('Consignor (Supplier) Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Supplier Code')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable(),
                TextColumn::make('contact_number')
                    ->label('Contact Number')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('items_count')
                    ->label('Consigned Items')
                    ->counts('items')
                    ->badge()
                    ->color('teal')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Accredited')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Accreditation Status')
                    ->boolean()
                    ->trueLabel('Accredited / Active Only')
                    ->falseLabel('Inactive Only'),
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
