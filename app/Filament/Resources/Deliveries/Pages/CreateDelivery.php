<?php

namespace App\Filament\Resources\Deliveries\Pages;

use App\Filament\Resources\Deliveries\DeliveryResource;
use App\Models\Delivery;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Confirm Delivery Receipt Entry')
            ->modalDescription('Please verify that the supplier name, DR number, batch quantities, and expiration dates match the physical delivery paperwork. Once saved, these items will be officially received into central storage stock.')
            ->modalSubmitActionLabel('Confirm & Receive into Stock');
    }

    protected function afterCreate(): void
    {
        /** @var Delivery $delivery */
        $delivery = $this->record;

        foreach ($delivery->batches as $batch) {
            StockMovement::firstOrCreate([
                'batch_id' => $batch->id,
                'type' => StockMovement::TYPE_RECEIVE,
            ], [
                'user_id' => $delivery->received_by_id,
                'quantity' => $batch->quantity_received,
                'balance_after' => $batch->quantity_received,
                'movement_date' => $delivery->delivery_date,
                'reference_number' => $delivery->dr_number,
                'reason' => 'Initial delivery from DR #'.$delivery->dr_number,
            ]);
        }
    }
}
