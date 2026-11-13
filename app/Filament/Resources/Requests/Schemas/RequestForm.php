<?php

namespace App\Filament\Resources\Requests\Schemas;

use App\Models\Item;
use App\Models\Request;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class RequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Requisition Header')
                    ->columns(3)
                    ->components([
                        Placeholder::make('request_number')
                            ->label('Requisition #')
                            ->content(fn ($record) => $record?->request_number ?? 'Pending'),

                        Placeholder::make('unit_name')
                            ->label('Requesting Unit')
                            ->content(fn ($record) => $record?->unit?->name ?? '-'),

                        Placeholder::make('requested_by')
                            ->label('Requested By')
                            ->content(fn ($record) => $record?->requestedBy?->name ?? '-'),

                        Placeholder::make('request_date')
                            ->label('Date & Time')
                            ->content(fn ($record) => $record?->request_date?->format('M j, Y • g:i A') ?? '-'),

                        Placeholder::make('priority')
                            ->label('Priority')
                            ->content(fn ($record) => $record?->is_urgent ? 'STAT (Urgent)' : 'Regular'),

                        Placeholder::make('status')
                            ->label('Current Status')
                            ->content(fn ($record) => $record?->status?->getLabel() ?? '-'),

                        Placeholder::make('downtime_info')
                            ->label('Downtime / Paper Slip Reference')
                            ->visible(fn ($record) => (bool) $record?->is_downtime)
                            ->columnSpanFull()
                            ->content(fn ($record) => 'Recorded via Downtime Entry'.($record?->downtime_reference ? " (Ref: {$record->downtime_reference})" : '')),

                        Placeholder::make('urgent_reason')
                            ->label('Clinical Reason (STAT)')
                            ->visible(fn ($record) => (bool) $record?->is_urgent)
                            ->columnSpanFull()
                            ->content(fn ($record) => $record?->urgent_reason ?? 'None provided'),

                        Placeholder::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->visible(fn ($record) => filled($record?->rejection_reason))
                            ->columnSpanFull()
                            ->content(fn ($record) => $record?->rejection_reason ?? '-'),

                        Textarea::make('notes')
                            ->label('Remarks / Ward Instructions')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Section::make('Requested Consigned Items')
                    ->components([
                        Repeater::make('lines')
                            ->relationship('lines')
                            ->disabled()
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->relationship('item', 'description')
                                    ->getOptionLabelFromRecordUsing(fn (Item $record) => "[{$record->code}] {$record->description} ({$record->unit_of_measure})")
                                    ->columnSpan(4),

                                TextInput::make('quantity_requested')
                                    ->label('Qty Requested')
                                    ->columnSpan(1),

                                TextInput::make('quantity_issued')
                                    ->label('Qty Issued')
                                    ->columnSpan(1),
                            ])
                            ->columns(6)
                            ->deletable(false)
                            ->addable(false),
                    ]),

                Section::make('Issued Batches & Lot Traceability (FEFO)')
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
                                    $issuedBy = e($movement->performer?->name ?? 'System');
                                    $issuedAt = e($movement->created_at?->timezone('Asia/Manila')->format('M j, Y g:i A') ?? '-');

                                    return "<tr>
                                        <td class='px-3 py-2 font-mono text-xs'>{$itemCode}</td>
                                        <td class='px-3 py-2 text-xs'>{$itemDesc}</td>
                                        <td class='px-3 py-2 font-mono text-xs'>{$lot}</td>
                                        <td class='px-3 py-2 text-xs'>{$expiry}</td>
                                        <td class='px-3 py-2 text-xs font-semibold text-right'>{$qty}</td>
                                        <td class='px-3 py-2 text-xs'>{$issuedBy} ({$issuedAt})</td>
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
                                                    <th class='px-3 py-2'>Issued By</th>
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
