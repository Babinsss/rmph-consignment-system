<?php

namespace App\Filament\Portal\Resources\Requests\Pages;

use App\Filament\Portal\Resources\Requests\Actions\PortalRequestActions;
use App\Filament\Portal\Resources\Requests\RequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PortalRequestActions::printIssueSlip(),
            PortalRequestActions::cancel(),
        ];
    }
}
