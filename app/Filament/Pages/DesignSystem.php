<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DesignSystem extends Page
{
    protected string $view = 'filament.pages.design-system';

    protected static ?string $title = 'Hospital Design Tokens & Style Guide';

    protected static UnitEnum|string|null $navigationGroup = 'System Guidelines';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Design System';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return ! app()->environment('production')
            && (auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs', 'viewer']) ?? false);
    }
}
