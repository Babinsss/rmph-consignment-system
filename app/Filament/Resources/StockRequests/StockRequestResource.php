<?php

namespace App\Filament\Resources\StockRequests;

use App\Filament\Resources\StockRequests\Pages\CreateStockRequest;
use App\Filament\Resources\StockRequests\Pages\EditStockRequest;
use App\Filament\Resources\StockRequests\Pages\ListStockRequests;
use App\Filament\Resources\StockRequests\Schemas\StockRequestForm;
use App\Filament\Resources\StockRequests\Tables\StockRequestsTable;
use App\Models\StockRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class StockRequestResource extends Resource
{
    protected static ?string $model = StockRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Consignment Movement';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return StockRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockRequestsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user && $user->isPharmacyStaff()) {
            $query->where('department', 'pharmacy');
        } elseif ($user && $user->isMedicalSupplyStaff()) {
            $query->where('department', 'medical_supplies');
        } elseif ($user && $user->isRequester()) {
            $query->where('requester_id', $user->id);
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
            'index' => ListStockRequests::route('/'),
            'create' => CreateStockRequest::route('/create'),
            'edit' => EditStockRequest::route('/{record}/edit'),
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
