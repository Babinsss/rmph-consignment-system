<?php

namespace App\Filament\Pages;

use App\Models\Consignor;
use App\Models\Unit;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Reports extends Page
{
    protected string $view = 'filament.pages.reports';

    protected static ?string $title = 'Consignment Inventory Reports';

    protected static UnitEnum|string|null $navigationGroup = 'Reports & Audits';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Inventory Reports';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']) ?? false;
    }

    public function getViewData(): array
    {
        return [
            'consignors' => Consignor::where('is_active', true)->orderBy('name')->get(),
            'units' => Unit::where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
