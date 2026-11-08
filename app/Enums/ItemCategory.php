<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ItemCategory: string implements HasColor, HasIcon, HasLabel
{
    case Supply = 'supply';
    case Drug = 'drug';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Supply => 'Medical Supply',
            self::Drug => 'Drugs & Medicine',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Supply => 'info',
            self::Drug => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Supply => 'heroicon-m-archive-box',
            self::Drug => 'heroicon-m-beaker',
        };
    }
}
