<?php

namespace App\Filament\Portal\Resources\Items\Schemas;

use App\Enums\ItemCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                Select::make('category')
                    ->options(ItemCategory::class)
                    ->required(),
                TextInput::make('unit_of_measure')
                    ->required(),
                Select::make('consignor_id')
                    ->relationship('consignor', 'name')
                    ->required(),
                TextInput::make('minimum_level')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
