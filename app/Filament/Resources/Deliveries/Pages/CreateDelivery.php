<?php

namespace App\Filament\Resources\Deliveries\Pages;

use App\Filament\Resources\Deliveries\DeliveryResource;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function afterCreate(): void
    {
        $inventoryService = new InventoryService;
        $inventoryService->processInwardDelivery($this->record, Auth::user());
    }
}
