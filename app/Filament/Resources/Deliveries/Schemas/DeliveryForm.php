<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class DeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Delivery Receipt Header')
                    ->description('Record the supplier, Delivery Receipt (DR) reference number, and delivery date.')
                    ->columns(2)
                    ->components([
                        Select::make('consignor_id')
                            ->label('Accredited Consignor (Supplier)')
                            ->relationship('consignor', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select the accredited supplier delivering these goods.',
                            ]),

                        TextInput::make('dr_number')
                            ->label('Delivery Receipt (DR) Number')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. DR-2026-0814')
                            ->helperText('Exact DR or invoice number printed on physical supplier paperwork.')
                            ->validationMessages([
                                'required' => 'Please enter the delivery receipt (DR) number.',
                            ]),

                        DatePicker::make('delivery_date')
                            ->label('Date of Delivery')
                            ->default(now())
                            ->required()
                            ->validationMessages([
                                'required' => 'Please enter the date the shipment arrived at the hospital.',
                            ]),

                        Select::make('received_by_id')
                            ->label('Received By (Stock Custodian)')
                            ->relationship('receivedBy', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select the custodian who physically verified the delivery.',
                            ]),

                        Textarea::make('notes')
                            ->label('Delivery Notes / Physical Inspection Remarks (Optional)')
                            ->placeholder('Notes on temperature conditions, packaging seals, physical count verification, etc.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Delivered Consigned Items & Batches')
                    ->description('Enter each item received under this DR. Each line creates an active batch in the central storage depot.')
                    ->components([
                        Placeholder::make('live_delivery_totals')
                            ->label('Live Delivery Totals')
                            ->content(function (Get $get): HtmlString {
                                $batches = $get('batches') ?? [];
                                $totalBatches = count($batches);
                                $totalUnits = 0;
                                $totalValue = 0;

                                foreach ($batches as $batch) {
                                    $qty = (int) ($batch['quantity_received'] ?? 0);
                                    $cost = (float) ($batch['unit_cost'] ?? 0);
                                    $totalUnits += $qty;
                                    $totalValue += ($qty * $cost);
                                }

                                $formattedUnits = number_format($totalUnits);
                                $canViewCost = auth()->user()?->canViewUnitCosts() ?? false;

                                $costHtml = '';
                                if ($canViewCost) {
                                    $formattedValue = '₱ '.number_format($totalValue, 2);
                                    $costHtml = '<div class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-teal-50 border border-teal-200 text-teal-900">'
                                        .'<span class="text-xs text-teal-700 font-medium">Total DR Value:</span>'
                                        .'<strong class="text-sm font-bold text-teal-950">'.$formattedValue.'</strong>'
                                        .'</div>';
                                }

                                return new HtmlString(
                                    '<div class="flex flex-wrap items-center gap-3 p-3.5 rounded-xl bg-gray-50 border border-gray-200">'
                                    .'<div class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-gray-200 text-gray-800">'
                                    .'<span class="text-xs text-gray-500 font-medium">Total Batches:</span>'
                                    .'<strong class="text-sm font-bold text-gray-900">'.$totalBatches.'</strong>'
                                    .'</div>'
                                    .'<div class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-gray-200 text-gray-800">'
                                    .'<span class="text-xs text-gray-500 font-medium">Total Units Delivered:</span>'
                                    .'<strong class="text-sm font-bold text-emerald-700">'.$formattedUnits.' units</strong>'
                                    .'</div>'
                                    .$costHtml
                                    .'</div>'
                                );
                            }),

                        Repeater::make('batches')
                            ->label('Batch Items')
                            ->relationship('batches')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Consigned Item')
                                    ->relationship('item', 'description')
                                    ->getOptionLabelFromRecordUsing(fn (Item $record) => "[{$record->code}] {$record->description} ({$record->unit_of_measure})")
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select the delivered item.',
                                    ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 4,
                                    ]),

                                TextInput::make('lot_number')
                                    ->label('Lot / Batch #')
                                    ->required()
                                    ->placeholder('e.g. LOT-2026-A1')
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'required' => 'Please enter the manufacturer lot number.',
                                    ])
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),

                                DatePicker::make('expiration_date')
                                    ->label('Expiration Date')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please enter the expiration date printed on the packaging.',
                                    ])
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),

                                TextInput::make('quantity_received')
                                    ->label('Quantity Delivered')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder('Units')
                                    ->validationMessages([
                                        'required' => 'Please enter the delivered quantity.',
                                        'min' => 'Quantity must be at least 1.',
                                    ])
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),

                                TextInput::make('unit_cost')
                                    ->label('Unit Cost (PHP)')
                                    ->prefix('₱')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->placeholder('0.00')
                                    ->visible(fn () => auth()->user()?->canViewUnitCosts() ?? false)
                                    ->required(fn () => auth()->user()?->canViewUnitCosts() ?? false)
                                    ->validationMessages([
                                        'required' => 'Please enter the unit cost per piece/unit.',
                                    ])
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),
                            ])
                            ->columns(12)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['item_id'])
                                ? Item::find($state['item_id'])?->description.' (Lot: '.($state['lot_number'] ?? '—').')'
                                : 'New Batch Line'
                            )
                            ->addActionLabel('+ Add Another Consigned Item Line')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['quantity_remaining'] = $data['quantity_received'];
                                $data['is_active'] = true;

                                return $data;
                            }),
                    ]),
            ]);
    }
}
