<?php

namespace App\Filament\Portal\Resources\Requests\Pages;

use App\Filament\Portal\Resources\Requests\RequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Requisition'),
        ];
    }
}
