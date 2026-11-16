<?php

namespace App\Filament\Resources\Requests\Pages;

use App\Filament\Resources\Requests\Actions\RequestActions;
use App\Filament\Resources\Requests\RequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RequestActions::fulfill(),
            RequestActions::reject(),
            RequestActions::printIssueSlip(),
        ];
    }
}
