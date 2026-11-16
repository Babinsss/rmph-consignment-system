<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RequestStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case PartiallyFulfilled = 'partially_fulfilled';
    case Fulfilled = 'fulfilled';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PartiallyFulfilled => 'Partly Fulfilled',
            self::Fulfilled => 'Fulfilled',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::PartiallyFulfilled => 'info',
            self::Fulfilled => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-clock',
            self::PartiallyFulfilled => 'heroicon-m-arrow-path',
            self::Fulfilled => 'heroicon-m-check-badge',
            self::Rejected => 'heroicon-m-x-circle',
            self::Cancelled => 'heroicon-m-no-symbol',
        };
    }
}
