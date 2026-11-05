<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LowStockAlertWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('⚠️ Low Stock & Depleted Consignment Items')
            ->description('Items that require immediate delivery replenishment from suppliers.')
            ->query(function (): Builder {
                $user = Auth::user();
                $query = Item::query()->where('is_active', true);

                if ($user && $user->isPharmacyStaff()) {
                    $query->where('department', 'pharmacy');
                } elseif ($user && $user->isMedicalSupplyStaff()) {
                    $query->where('department', 'medical_supplies');
                }

                return $query;
            })
            ->columns([
                TextColumn::make('item_code')
                    ->label('Code')
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Item Name')
                    ->description(fn (Item $record): ?string => $record->generic_name),
                TextColumn::make('department')
                    ->label('Stream')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pharmacy' => 'info',
                        'medical_supplies' => 'teal',
                        default => 'gray',
                    }),
                TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->badge()
                    ->getStateUsing(fn (Item $record): int => $record->current_stock)
                    ->color(fn (Item $record): string => $record->current_stock <= 0 ? 'danger' : 'warning')
                    ->suffix(fn (Item $record): string => " {$record->uom}"),
                TextColumn::make('minimum_stock_level')
                    ->label('Reorder Min.')
                    ->suffix(fn (Item $record): string => " {$record->uom}"),
                TextColumn::make('supplier.name')
                    ->label('Consignor (Supplier)')
                    ->badge()
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
