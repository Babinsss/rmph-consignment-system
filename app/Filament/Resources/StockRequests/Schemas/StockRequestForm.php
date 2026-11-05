<?php

namespace App\Filament\Resources\StockRequests\Schemas;

use App\Models\Item;
use App\Models\StockRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StockRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('request_number')
                    ->label('Requisition Slip (RIS) No.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn (): string => 'RIS-'.date('Ymd-His')),
                Select::make('department')
                    ->label('Consignment Stream')
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
                Select::make('requesting_unit')
                    ->label('Authorized Requesting Department / Unit')
                    ->options(StockRequest::ALLOWED_REQUESTING_UNITS)
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('requester_name')
                    ->label('Requisitioned By (Staff Name)')
                    ->default(fn (): ?string => Auth::user()?->name)
                    ->required(),
                DatePicker::make('request_date')
                    ->label('Date Requested')
                    ->default(now())
                    ->required(),
                TextInput::make('purpose')
                    ->label('Purpose / Procedure')
                    ->placeholder('e.g. Inpatient Ward replenishment, Patient emergency'),
                TextInput::make('patient_mrn')
                    ->label('Patient Hospital / MRN (Optional)')
                    ->placeholder('e.g. MRN-2026-9912'),
                Textarea::make('notes')
                    ->label('Additional Clinical Notes')
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label('Requested Consignment Items')
                    ->relationship('items')
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->options(function (Get $get): array {
                                $dept = $get('../../department') ?? 'pharmacy';

                                return Item::where('department', $dept)
                                    ->get()
                                    ->mapWithKeys(fn (Item $item) => [
                                        $item->id => "{$item->item_code} - {$item->name} (Stock: {$item->current_stock} {$item->uom})",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->columnSpan(8),
                        TextInput::make('quantity_requested')
                            ->label('Qty Requested')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(4),
                    ])
                    ->columns(12)
                    ->columnSpanFull()
                    ->minItems(1)
                    ->defaultItems(1),
            ]);
    }
}
