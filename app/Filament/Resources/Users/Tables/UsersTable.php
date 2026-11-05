<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Staff Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrator',
                        'pharmacy_staff' => 'Pharmacy Staff',
                        'medical_supply_staff' => 'Med Supply Staff',
                        'department_requester' => 'Ward Requester',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'pharmacy_staff' => 'info',
                        'medical_supply_staff' => 'teal',
                        'department_requester' => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('department')
                    ->label('Unit / Department')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'pharmacy_staff' => 'Pharmacy Staff',
                        'medical_supply_staff' => 'Medical Supply Staff',
                        'department_requester' => 'Requester',
                    ]),
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
