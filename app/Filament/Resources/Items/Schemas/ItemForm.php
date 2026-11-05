<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('item_code')
                    ->label('Item Code / SKU')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. PHAR-PAR-500 or MED-GLV-070'),
                TextInput::make('name')
                    ->label('Item Description / Full Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('generic_name')
                    ->label('Generic Name (Pharmacy)')
                    ->placeholder('e.g. Paracetamol'),
                TextInput::make('brand_name')
                    ->label('Brand Name')
                    ->placeholder('e.g. Biogesic'),
                Select::make('department')
                    ->label('Consignment Stream')
                    ->options([
                        'pharmacy' => 'Pharmacy (Medicines & Injections)',
                        'medical_supplies' => 'Medical Supplies (Surgical & Consumables)',
                    ])
                    ->required(),
                TextInput::make('category')
                    ->label('Category')
                    ->placeholder('e.g. Antibiotics, PPE, Wound Care'),
                Select::make('uom')
                    ->label('Unit of Measurement (UOM)')
                    ->options([
                        'piece' => 'Piece (pc)',
                        'box' => 'Box (bx)',
                        'vial' => 'Vial (vl)',
                        'ampoule' => 'Ampoule (amp)',
                        'bottle' => 'Bottle (btl)',
                        'pack' => 'Pack (pk)',
                        'pair' => 'Pair (pr)',
                        'roll' => 'Roll (rl)',
                        'set' => 'Set',
                        'kit' => 'Kit',
                    ])
                    ->required()
                    ->searchable(),
                Select::make('supplier_id')
                    ->label('Primary Consignor / Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('minimum_stock_level')
                    ->label('Reorder / Low-Stock Level')
                    ->numeric()
                    ->default(10)
                    ->required(),
                TextInput::make('unit_cost')
                    ->label('Consignment Unit Cost (₱)')
                    ->numeric()
                    ->prefix('₱')
                    ->default(0.00)
                    ->required(),
                TextInput::make('selling_price')
                    ->label('Patient Charge Price (₱) [Optional]')
                    ->numeric()
                    ->prefix('₱'),
                Toggle::make('is_active')
                    ->label('Active Item')
                    ->default(true)
                    ->required(),
                Textarea::make('description')
                    ->label('Specifications & Storage Notes')
                    ->columnSpanFull(),
            ]);
    }
}
