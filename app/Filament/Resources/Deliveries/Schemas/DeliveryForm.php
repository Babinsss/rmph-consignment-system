<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('delivery_number')
                    ->label('Delivery Receipt (DR) Number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn (): string => 'DR-'.date('Ymd-His')),
                Select::make('supplier_id')
                    ->label('Consignor / Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('department')
                    ->label('Department Stream')
                    ->options([
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                    ])
                    ->default(function () {
                        $user = Auth::user();
                        if ($user && $user->isPharmacyStaff()) {
                            return 'pharmacy';
                        }
                        if ($user && $user->isMedicalSupplyStaff()) {
                            return 'medical_supplies';
                        }

                        return 'pharmacy';
                    })
                    ->live()
                    ->required(),
                DatePicker::make('delivery_date')
                    ->label('Date Delivered')
                    ->default(now())
                    ->required(),
                TextInput::make('po_reference')
                    ->label('Purchase / Consignment Order Ref')
                    ->placeholder('e.g. PO-2026-004'),
                Select::make('received_by_id')
                    ->label('Received By')
                    ->relationship('receivedBy', 'name')
                    ->default(fn (): ?int => Auth::id())
                    ->searchable()
                    ->required(),
                Textarea::make('notes')
                    ->label('Delivery Notes / Inspection Remarks')
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label('Consigned Stock Items (Batches & Lots)')
                    ->relationship('items')
                    ->schema([
                        Select::make('item_id')
                            ->label('Catalog Item')
                            ->options(function (Get $get): array {
                                $dept = $get('../../department') ?? 'pharmacy';

                                return Item::where('department', $dept)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $item = Item::find($state);
                                    if ($item) {
                                        $set('unit_cost', $item->unit_cost);
                                    }
                                }
                            })
                            ->columnSpan(3),
                        TextInput::make('lot_number')
                            ->label('Lot / Batch No.')
                            ->placeholder('e.g. LOT-9921')
                            ->required()
                            ->columnSpan(2),
                        DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->columnSpan(2),
                        TextInput::make('quantity_received')
                            ->label('Qty Received')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('unit_cost')
                            ->label('Unit Cost (₱)')
                            ->numeric()
                            ->prefix('₱')
                            ->default(0.00)
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(11)
                    ->columnSpanFull()
                    ->minItems(1)
                    ->defaultItems(1),
            ]);
    }
}
