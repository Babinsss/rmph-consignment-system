<?php

namespace App\Filament\Resources\Items\Schemas;

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
                TextInput::make('description')
                    ->label('Item Description / Generic Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Sterile Disposable Syringe 3ml with needle')
                    ->helperText('Full clinical description of the supply or drug.')
                    ->validationMessages([
                        'required' => 'Please enter the item description.',
                    ]),
                TextInput::make('code')
                    ->label('Hospital Item Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g. SUP-001, DRG-001')
                    ->helperText('Unique inventory code.')
                    ->validationMessages([
                        'required' => 'Please provide a unique item code.',
                        'unique' => 'An item with this code already exists.',
                    ]),
                Select::make('category')
                    ->label('Item Classification')
                    ->options(function () {
                        $scoped = auth()->user()?->custodianCategory();
                        if ($scoped) {
                            return [$scoped->value => $scoped->getLabel()];
                        }

                        return ItemCategory::class;
                    })
                    ->default(fn () => auth()->user()?->custodianCategory()?->value)
                    ->disabled(fn () => auth()->user()?->custodianCategory() !== null)
                    ->dehydrated()
                    ->required()
                    ->validationMessages([
                        'required' => 'Please choose whether this is a Medical Supply or Drug & Medicine.',
                    ]),
                TextInput::make('unit_of_measure')
                    ->label('Packaging Unit of Measure')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g. box of 100, vial, ampule, piece, bottle')
                    ->helperText('How this item is counted and distributed.')
                    ->validationMessages([
                        'required' => 'Please specify the unit of measure.',
                    ]),
                Select::make('consignor_id')
                    ->label('Accredited Consignor (Supplier)')
                    ->relationship('consignor', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => 'Please select the accredited supplier.',
                    ]),
                TextInput::make('minimum_level')
                    ->label('Minimum Stock Alert Level')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('When total depot stock drops to or below this quantity, low-stock warnings are flagged.')
                    ->required()
                    ->validationMessages([
                        'required' => 'Please set a minimum stock alert level (enter 0 if none).',
                    ]),
                Toggle::make('is_active')
                    ->label('Active in Hospital Catalog')
                    ->helperText('Inactive items cannot be requested by clinical wards.')
                    ->default(true),
            ]);
    }
}
