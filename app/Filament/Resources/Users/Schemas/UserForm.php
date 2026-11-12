<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Staff Full Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Aliana Marie Dula')
                    ->validationMessages([
                        'required' => 'Please enter the staff member full name.',
                    ]),
                TextInput::make('email')
                    ->label('Hospital Email / Username')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('e.g. aliana.dula@rmph.local')
                    ->validationMessages([
                        'required' => 'Please enter a valid login email address.',
                        'unique' => 'An account with this email address already exists.',
                    ]),
                TextInput::make('password')
                    ->label('Account Password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->helperText('Leave blank to keep existing password when editing.'),
                Select::make('roles')
                    ->label('Assigned Role(s)')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                        'admin' => 'Administrator (RMPH IT)',
                        'custodian' => 'General Custodian (All Categories)',
                        'custodian_supplies' => 'Supplies Custodian (Medical Supplies Only)',
                        'custodian_drugs' => 'Drugs Custodian (Drugs & Medicine Only)',
                        'viewer' => 'Viewer (Chief of Hospital - Read Only)',
                        'requesting_unit' => 'Requesting Unit Staff (Ward)',
                        default => $record->name,
                    })
                    ->multiple()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => 'Please select at least one role for this user.',
                    ]),
                Select::make('unit_id')
                    ->label('Assigned Hospital Unit')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Mandatory for Requesting Unit staff (CSR, HDU, Dialysis, Pharmacies, Yakap). Leave blank for Central Staff, Chief, or IT.'),
                Toggle::make('is_active')
                    ->label('Active User Account')
                    ->helperText('Inactive users cannot sign into either portal.')
                    ->default(true),
            ]);
    }
}
