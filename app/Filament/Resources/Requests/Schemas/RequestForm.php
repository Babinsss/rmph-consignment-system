<?php

namespace App\Filament\Resources\Requests\Schemas;

use App\Models\Item;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
            ]);
    }
}
