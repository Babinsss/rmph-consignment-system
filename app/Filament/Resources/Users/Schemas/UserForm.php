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
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Official Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->revealable(),
                Select::make('role')
                    ->label('System Role & Access Scope')
                    ->options([
                        'admin' => 'Hospital Admin (Full Access to All Consignments)',
                        'pharmacy_staff' => 'Pharmacy Custodian / Pharmacist',
                        'medical_supply_staff' => 'Central CSR / Medical Supplies Custodian',
                        'department_requester' => 'Ward / Department Requester (Requisitions Only)',
                    ])
                    ->default('pharmacy_staff')
                    ->required(),
                TextInput::make('department')
                    ->label('Assigned Hospital Unit / Ward')
                    ->placeholder('e.g. pharmacy, medical_supplies, ICU, OR')
                    ->default('pharmacy')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active Staff Account')
                    ->default(true)
                    ->required(),
            ]);
    }
}
