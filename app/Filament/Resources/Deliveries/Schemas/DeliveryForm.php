<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use App\Models\Item;
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
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select the accredited supplier.',
                            ]),

                        TextInput::make('dr_number')
                            ->label('DR / Invoice #')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. DR-2026-0814')
                            ->validationMessages([
                                'required' => 'Please enter the delivery receipt (DR) number.',
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
                                    ->relationship('item', 'description')
                                    ->getOptionLabelFromRecordUsing(fn (Item $record) => "[{$record->code}] {$record->description} ({$record->unit_of_measure})")
                                    ->searchable()
                                    ->preload()
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
