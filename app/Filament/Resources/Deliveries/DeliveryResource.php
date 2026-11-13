<?php

namespace App\Filament\Resources\Deliveries;

use App\Filament\Resources\Deliveries\Pages\CreateDelivery;
use App\Filament\Resources\Deliveries\Pages\ListDeliveries;
use App\Filament\Resources\Deliveries\Pages\ViewDelivery;
use App\Filament\Resources\Deliveries\Schemas\DeliveryForm;
use App\Filament\Resources\Deliveries\Tables\DeliveriesTable;
use App\Models\Delivery;
use App\Support\CustodianScope;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static UnitEnum|string|null $navigationGroup = 'Inventory Master';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Deliveries (DRs)';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return CustodianScope::applyToDeliveries(parent::getEloquentQuery());
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return DeliveryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveries::route('/'),
            'create' => CreateDelivery::route('/create'),
            'view' => ViewDelivery::route('/{record}'),
        ];
    }
}
