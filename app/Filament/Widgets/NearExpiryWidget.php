<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryItem;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NearExpiryWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('⏳ Batch Expiry Monitoring (Next 90 Days)')
            ->description('Physical lots approaching expiration date to prioritize for FEFO issuance.')
            ->query(function (): Builder {
                $user = Auth::user();
                $ninetyDaysFromNow = Carbon::now()->addDays(90);

                $query = DeliveryItem::query()
                    ->where('status', 'available')
                    ->where('quantity_remaining', '>', 0)
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', $ninetyDaysFromNow)
                    ->orderBy('expiry_date', 'asc');

                if ($user && $user->isPharmacyStaff()) {
                    $query->whereHas('item', fn ($q) => $q->where('department', 'pharmacy'));
                } elseif ($user && $user->isMedicalSupplyStaff()) {
                    $query->whereHas('item', fn ($q) => $q->where('department', 'medical_supplies'));
                }

                return $query;
            })
            ->columns([
                TextColumn::make('item.item_code')
                    ->label('Code')
                    ->weight('bold'),
                TextColumn::make('item.name')
                    ->label('Item Name'),
                TextColumn::make('lot_number')
                    ->label('Lot / Batch No.')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('expiry_date')
                    ->label('Expiration Date')
                    ->date()
                    ->badge()
                    ->color(function (DeliveryItem $record): string {
                        if ($record->expiry_date->isPast()) {
                            return 'danger';
                        }
                        if ($record->expiry_date->diffInDays(now()) <= 30) {
                            return 'warning';
                        }

                        return 'info';
                    }),
                TextColumn::make('quantity_remaining')
                    ->label('Remaining Stock')
                    ->suffix(fn (DeliveryItem $record): string => " {$record->item->uom}"),
                TextColumn::make('delivery.supplier.name')
                    ->label('Consignor')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(false);
    }
}
