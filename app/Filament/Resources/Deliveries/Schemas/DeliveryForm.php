<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use App\Models\Consignor;
use App\Models\Item;
use App\Support\CustodianScope;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        $enterKeyScript = "
            const addBtn = document.querySelector('[data-repeater-add-batches]');
            if (addBtn) {
                addBtn.click();
                setTimeout(() => {
                    const row = document.querySelector('.fi-fo-table-repeater tbody tr:last-child');
                    if (row) {
                        const target = row.querySelector('.choices__input, input:not([type=hidden]), select');
                        if (target) { target.focus(); }
                    }
                }, 200);
            }
        ";

        return $schema
            ->components([
                Section::make('Delivery Receipt Header')
                    ->description('Record supplier DR details and the verifying stock custodian.')
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->components([
                        Select::make('consignor_id')
                            ->label('Accredited Consignor')
                            ->relationship('consignor', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select the accredited supplier.',
                            ]),

                        TextInput::make('dr_number')
                            ->label('DR / Invoice #')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. DR-2026-0814')
                            ->rules([
                                fn (Get $get): Unique => Rule::unique('deliveries', 'dr_number')
                                    ->where(fn ($query) => $query->where('consignor_id', $get('consignor_id'))),
                            ])
                            ->validationMessages([
                                'required' => 'Please enter the delivery receipt (DR) number.',
                                'unique' => 'This DR / Invoice # has already been recorded for the selected consignor.',
                            ]),

                        DatePicker::make('delivery_date')
                            ->label('Date of Delivery')
                            ->default(now())
                            ->required()
                            ->validationMessages([
                                'required' => 'Please enter the arrival date.',
                            ]),

                        Select::make('received_by_id')
                            ->label('Received By')
                            ->relationship('receivedBy', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select the custodian who verified this delivery.',
                            ]),

                        Textarea::make('notes')
                            ->label('Delivery Notes / Physical Inspection Remarks (Optional)')
                            ->placeholder('Notes on packaging seals, cold-chain temperature conditions, physical inspection findings...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Delivered Consigned Items & Batches')
                    ->description('Enter each received item line. Each line creates an active batch in the central storage depot.')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('batches')
                            ->label('Batch Items')
                            ->relationship('batches')
                            ->compact()
                            ->table(function (): array {
                                $canViewCost = auth()->user()?->canViewUnitCosts() ?? false;

                                $columns = [
                                    TableColumn::make('Consigned Item')
                                        ->width($canViewCost ? '34%' : '44%')
                                        ->markAsRequired(),
                                    TableColumn::make('Lot / Batch #')
                                        ->width($canViewCost ? '18%' : '20%')
                                        ->markAsRequired(),
                                    TableColumn::make('Expiration Date')
                                        ->width($canViewCost ? '18%' : '20%')
                                        ->markAsRequired(),
                                    TableColumn::make('Quantity Delivered')
                                        ->width($canViewCost ? '15%' : '16%')
                                        ->markAsRequired(),
                                ];

                                if ($canViewCost) {
                                    $columns[] = TableColumn::make('Unit Cost (PHP)')
                                        ->width('15%')
                                        ->markAsRequired();
                                }

                                return $columns;
                            })
                            ->schema([
                                Select::make('item_id')
                                    ->label('Consigned Item')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->options(function (Get $get): array {
                                        $selectedConsignorId = $get('/consignor_id') ?? $get('../../consignor_id');
                                        $selectedConsignor = $selectedConsignorId ? Consignor::find($selectedConsignorId) : null;

                                        $supplierItems = [];
                                        $otherItems = [];

                                        $query = Item::with('consignor')->where('is_active', true)->orderBy('description');
                                        CustodianScope::applyToItems($query);
                                        $allItems = $query->get();

                                        foreach ($allItems as $item) {
                                            $label = "[{$item->code}] {$item->description} ({$item->unit_of_measure})";
                                            if ($selectedConsignorId && (string) $item->consignor_id === (string) $selectedConsignorId) {
                                                $supplierItems[$item->id] = $label;
                                            } else {
                                                $consignorTag = $item->consignor ? " — {$item->consignor->name}" : '';
                                                $otherItems[$item->id] = "{$label}{$consignorTag}";
                                            }
                                        }

                                        if ($selectedConsignorId && ! empty($supplierItems)) {
                                            $supplierName = $selectedConsignor ? $selectedConsignor->name : 'Selected Supplier';

                                            return [
                                                "Accredited to {$supplierName}" => $supplierItems,
                                                'Other Hospital Catalog Items' => $otherItems,
                                            ];
                                        }

                                        return $supplierItems + $otherItems;
                                    })
                                    ->helperText(function (Get $get): ?HtmlString {
                                        $itemId = $get('item_id');
                                        $consignorId = $get('/consignor_id') ?? $get('../../consignor_id');

                                        if (! $itemId || ! $consignorId) {
                                            return null;
                                        }

                                        $item = Item::with('consignor')->find($itemId);
                                        if (! $item) {
                                            return null;
                                        }

                                        if ((string) $item->consignor_id !== (string) $consignorId) {
                                            $catalogSupplier = $item->consignor?->name ?? 'another supplier';
                                            $drSupplier = Consignor::find($consignorId)?->name ?? 'this delivery';

                                            return new HtmlString(
                                                '<span class="inline-flex items-center gap-1.5 text-xs text-amber-800 font-medium bg-amber-50 px-2 py-1 rounded border border-amber-200 mt-1">'
                                                .'<svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>'
                                                ."<span><strong>Cross-Supplier Delivery:</strong> Catalog primary supplier is <u>{$catalogSupplier}</u>. Receiving this DR will record a new batch under <u>{$drSupplier}</u>.</span>"
                                                .'</span>'
                                            );
                                        }

                                        return null;
                                    })
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select the delivered item.',
                                    ]),

                                TextInput::make('lot_number')
                                    ->label('Lot / Batch #')
                                    ->required()
                                    ->placeholder('e.g. LOT-2026-A1')
                                    ->maxLength(100)
                                    ->rules([
                                        fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $itemId = $get('item_id');
                                            $lot = trim((string) $value);

                                            if (! $itemId || $lot === '') {
                                                return;
                                            }

                                            $allBatches = $get('/batches') ?? $get('../../batches') ?? [];
                                            $duplicates = 0;

                                            foreach ($allBatches as $batch) {
                                                $otherItem = $batch['item_id'] ?? null;
                                                $otherLot = trim((string) ($batch['lot_number'] ?? ''));

                                                if ((string) $otherItem === (string) $itemId && strcasecmp($otherLot, $lot) === 0) {
                                                    $duplicates++;
                                                }
                                            }

                                            if ($duplicates > 1) {
                                                $fail('This item and lot number combination has already been added in another row.');
                                            }
                                        },
                                    ])
                                    ->validationMessages([
                                        'required' => 'Please enter the manufacturer lot number.',
                                    ]),

                                DatePicker::make('expiration_date')
                                    ->label('Expiration Date')
                                    ->required()
                                    ->afterOrEqual('today')
                                    ->validationMessages([
                                        'required' => 'Please enter the expiration date printed on the packaging.',
                                        'after_or_equal' => 'Expiration date cannot be in the past.',
                                    ]),

                                TextInput::make('quantity_received')
                                    ->label('Quantity Delivered')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->placeholder('Units')
                                    ->extraInputAttributes(function () use ($enterKeyScript) {
                                        $canViewCost = auth()->user()?->canViewUnitCosts() ?? false;

                                        return $canViewCost ? [] : [
                                            'x-on:keydown.enter.prevent' => $enterKeyScript,
                                        ];
                                    })
                                    ->validationMessages([
                                        'required' => 'Please enter the delivered quantity.',
                                        'min' => 'Quantity must be at least 1.',
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
                                    ->extraInputAttributes([
                                        'x-on:keydown.enter.prevent' => $enterKeyScript,
                                    ])
                                    ->validationMessages([
                                        'required' => 'Please enter the unit cost per unit.',
                                        'min' => 'Unit cost cannot be negative.',
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->cloneable()
                            ->deletable()
                            ->reorderable(false)
                            ->collapsible(false)
                            ->addActionLabel('+ Add Consigned Item Line')
                            ->addAction(fn (Action $action) => $action->extraAttributes(['data-repeater-add-batches' => 'true']))
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['quantity_remaining'] = $data['quantity_received'];
                                $data['is_active'] = true;

                                return $data;
                            }),
                    ]),
            ]);
    }
}
