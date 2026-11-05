<?php

namespace App\Filament\Resources\StockRequests\Pages;

use App\Filament\Resources\StockRequests\StockRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditStockRequest extends EditRecord
{
    protected static string $resource = StockRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
