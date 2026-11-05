<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Company / Supplier Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Supplier Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. SUP-METRO'),
                Select::make('consignment_type')
                    ->label('Consignment Stream')
                    ->options([
                        'both' => 'Both (Pharmacy & Medical Supplies)',
                        'pharmacy' => 'Pharmacy Only',
                        'medical_supplies' => 'Medical Supplies Only',
                    ])
                    ->default('both')
                    ->required(),
                TextInput::make('contact_person')
                    ->label('Key Contact Person'),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email(),
                TextInput::make('phone')
                    ->label('Contact Phone / Mobile')
                    ->tel(),
                TextInput::make('payment_terms')
                    ->label('Payment / Consignment Terms')
                    ->placeholder('e.g. 30 days post-consumption'),
                Textarea::make('address')
                    ->label('Office / Warehouse Address')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active Consignor')
                    ->default(true)
                    ->required(),
            ]);
    }
}
