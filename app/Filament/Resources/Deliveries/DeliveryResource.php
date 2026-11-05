<?php

namespace App\Filament\Resources\Deliveries;

use App\Filament\Resources\Deliveries\Pages\CreateDelivery;
use App\Filament\Resources\Deliveries\Pages\EditDelivery;
use App\Filament\Resources\Deliveries\Pages\ListDeliveries;
use App\Filament\Resources\Deliveries\Schemas\DeliveryForm;
use App\Filament\Resources\Deliveries\Tables\DeliveriesTable;
use App\Models\Delivery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Consignment Movement';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DeliveryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveriesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->isPharmacyStaff()) {
            $query->where('department', 'pharmacy');
        } elseif ($user && $user->isMedicalSupplyStaff()) {
            $query->where('department', 'medical_supplies');
        }

        return $query;
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
            'edit' => EditDelivery::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
