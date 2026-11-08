<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Hospital Unit / Ward Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Central Supply Room (CSR)')
                    ->validationMessages([
                        'required' => 'Please enter the hospital unit name.',
                    ]),
                TextInput::make('code')
                    ->label('Unit Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g. CSR, HDU, DIALYSIS')
                    ->helperText('Short uppercase abbreviation used on requisitions and badges.')
                    ->validationMessages([
                        'required' => 'Please enter a unit code.',
                        'unique' => 'A unit with this code already exists.',
                    ]),
                TextInput::make('description')
                    ->label('Unit Scope / Department Description (Optional)')
                    ->placeholder('e.g. Inpatient surgical supply station')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Active Hospital Unit')
                    ->helperText('Only active units can submit requisitions in the portal.')
                    ->default(true),
            ]);
    }
}
