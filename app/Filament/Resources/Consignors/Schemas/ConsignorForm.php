<?php

namespace App\Filament\Resources\Consignors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConsignorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Consignor / Supplier Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Zuellig Pharma Corporation')
                    ->validationMessages([
                        'required' => 'Please enter the official supplier name.',
                    ]),
                TextInput::make('code')
                    ->label('Supplier Code')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g. ZUELLIG, METRODRUG')
                    ->helperText('Short reference code for reports and search.')
                    ->validationMessages([
                        'unique' => 'A supplier with this code already exists.',
                    ]),
                TextInput::make('contact_person')
                    ->label('Sales Representative / Contact Person')
                    ->placeholder('e.g. Juan dela Cruz')
                    ->maxLength(255),
                TextInput::make('contact_number')
                    ->label('Official Contact Number')
                    ->placeholder('e.g. 0917-123-4567 or (036) 621-0000')
                    ->tel()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Business Email Address')
                    ->placeholder('e.g. orders@zuelligpharma.com')
                    ->email()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Warehouse / Business Address')
                    ->placeholder('Physical office or depot address for deliveries')
                    ->columnSpanFull()
                    ->maxLength(500),
                Toggle::make('is_active')
                    ->label('Accredited / Active Consignor')
                    ->helperText('Inactive suppliers cannot be selected on new delivery receipts.')
                    ->default(true),
            ]);
    }
}
