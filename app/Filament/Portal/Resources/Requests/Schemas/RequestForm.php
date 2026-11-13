<?php

namespace App\Filament\Portal\Resources\Requests\Schemas;

use App\Models\Item;
use App\Models\Request;
use App\Services\RequestWindowService;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class RequestForm
{
    public static function configure(Schema $schema): Schema
    {
        $windowService = app(RequestWindowService::class);
        $isWindowOpen = $windowService->isRegularWindowOpen();
        $scheduleSummary = $windowService->getScheduleSummary();

        return $schema
            ->components([
                Section::make('Requisition Header')
                    ->description('Supply request from your hospital unit to Central Storage Depot.')
                    ->columns(2)
                    ->components([
                        Placeholder::make('window_status_banner')
                            ->columnSpanFull()
                            ->content(new HtmlString(
                                $isWindowOpen
                                    ? '<div class="p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs font-medium flex items-center gap-2.5">'
                                      .'<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-teal-700 text-white uppercase shrink-0">Schedule Open</span>'
                                      .'<span>Regular Requisition Window is <strong>OPEN</strong> ('.$scheduleSummary.'). Normal requests will be fulfilled during central depot operating hours.</span>'
                                      .'</div>'
                                    : '<div class="p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-medium flex items-center gap-2.5">'
                                      .'<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-600 text-white uppercase shrink-0">Window Closed</span>'
                                      .'<span>Regular Window is <strong>CLOSED</strong>. For emergency clinical needs, switch on <strong>Urgent (STAT) Emergency Requisition</strong> below (accepted 24/7).</span>'
                                      .'</div>'
                            )),

                        Placeholder::make('unit_display')
                            ->label('Requesting Clinical Unit')
                            ->content(fn () => auth()->user()?->unit?->name ?? 'Hospital Unit'),

                        Placeholder::make('requester_display')
                            ->label('Requesting Staff Member')
                            ->content(fn () => auth()->user()?->name ?? 'Staff'),

                        Toggle::make('is_urgent')
                            ->label('Urgent (STAT) Emergency Requisition')
                            ->live()
                            ->default(! $isWindowOpen)
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-calendar')
                            ->onColor('danger')
                            ->helperText('Enable for immediate critical emergency ward needs. Accepted 24/7 and flagged to central stock custodians with highest priority.')
                            ->columnSpanFull(),

                        Textarea::make('urgent_reason')
                            ->label('Mandatory Clinical Justification (for Urgent STAT)')
                            ->placeholder('Describe the clinical emergency (e.g. STAT procedure in HDU, acute trauma care, sudden post-op complication). Strict hospital privacy rule: Do NOT enter patient names, bed numbers, or MRNs.')
                            ->helperText('State clinical and departmental urgency only. Personal patient identifiers are strictly forbidden.')
                            ->minLength(10)
                            ->rows(3)
                            ->visible(fn ($get) => (bool) $get('is_urgent'))
                            ->required(fn ($get) => (bool) $get('is_urgent'))
                            ->validationMessages([
                                'required' => 'Please provide a clinical justification for this STAT emergency requisition.',
                                'min' => 'Please enter at least 10 characters explaining the clinical justification.',
                            ])
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Ward Notes / Delivery Remarks (Optional)')
                            ->placeholder('Any special instructions for the central storage custodian regarding this order...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Requested Consigned Items')
                    ->description('Select the medical supplies or medicines needed and quantity.')
                    ->components([
                        Repeater::make('lines')
                            ->label('Requisition Item Lines')
                            ->relationship('lines')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Consigned Item')
                                    ->relationship('item', 'description')
                                    ->getOptionLabelFromRecordUsing(fn (Item $record) => "[{$record->code}] {$record->description} ({$record->unit_of_measure}) • Depot Stock: {$record->current_stock}")
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select an item from the catalog.',
                                    ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 8,
                                    ]),

                                TextInput::make('quantity_requested')
                                    ->label('Quantity Needed')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->placeholder('Qty')
                                    ->validationMessages([
                                        'required' => 'Please enter a quantity.',
                                        'min' => 'Requested quantity must be at least 1.',
                                    ])
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 4,
                                    ]),
                            ])
                            ->columns(12)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('+ Add Another Consigned Item')
                            ->itemLabel(fn (array $state): ?string => isset($state['item_id']) ? Item::find($state['item_id'])?->description : 'New Item Line')
                            ->collapsible(),
                    ]),

                Section::make('Issued Batches & Lot Numbers')
                    ->visible(fn (?Request $record): bool => $record !== null && $record->getIssuedMovements()->isNotEmpty())
                    ->components([
                        Placeholder::make('issued_batches_summary')
                            ->hiddenLabel()
                            ->content(function (?Request $record): HtmlString {
                                if (! $record) {
                                    return new HtmlString('');
                                }

                                $rows = $record->getIssuedMovements()->map(function ($movement): string {
                                    $itemCode = e($movement->batch?->item?->code ?? 'N/A');
                                    $itemDesc = e($movement->batch?->item?->description ?? 'Unknown Item');
                                    $lot = e($movement->batch?->lot_number ?? 'N/A');
                                    $expiry = e($movement->batch?->expiration_date?->format('Y-m-d') ?? 'N/A');
                                    $qty = abs((int) $movement->quantity);

                                    return "<tr>
                                        <td class='px-3 py-2 font-mono text-xs'>{$itemCode}</td>
                                        <td class='px-3 py-2 text-xs'>{$itemDesc}</td>
                                        <td class='px-3 py-2 font-mono text-xs'>{$lot}</td>
                                        <td class='px-3 py-2 text-xs'>{$expiry}</td>
                                        <td class='px-3 py-2 text-xs font-semibold text-right'>{$qty}</td>
                                    </tr>";
                                })->implode('');

                                return new HtmlString(
                                    "<div class='overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700'>
                                        <table class='w-full text-left border-collapse'>
                                            <thead class='bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300'>
                                                <tr>
                                                    <th class='px-3 py-2'>Code</th>
                                                    <th class='px-3 py-2'>Description</th>
                                                    <th class='px-3 py-2'>Lot #</th>
                                                    <th class='px-3 py-2'>Expiry</th>
                                                    <th class='px-3 py-2 text-right'>Issued</th>
                                                </tr>
                                            </thead>
                                            <tbody class='divide-y divide-gray-200 dark:divide-gray-700'>{$rows}</tbody>
                                        </table>
                                    </div>"
                                );
                            }),
                    ]),
            ]);
    }
}
