<?php

namespace App\Filament\Resources\Deliveries\Pages;

use App\Filament\Resources\Deliveries\DeliveryResource;
use App\Models\Consignor;
use App\Models\Delivery;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\View;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;

    protected static bool $canCreateAnother = false;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName())
            ->footer([
                Group::make([
                    Flex::make([
                        View::make('filament.resources.deliveries.live-totals'),
                        Actions::make($this->getFormActions())
                            ->alignment(Alignment::End),
                    ])
                        ->verticallyAlignCenter()
                        ->from('md'),
                ])
                    ->extraAttributes([
                        'class' => 'sticky bottom-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3.5 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 shadow-lg mt-8',
                    ]),
            ]);
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Review & Receive Stock')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('primary')
            ->size(Size::Large)
            ->requiresConfirmation()
            ->modalHeading('Confirm Delivery Receipt Entry')
            ->modalDescription(function (): HtmlString {
                $batches = $this->data['batches'] ?? [];
                $totalBatches = count($batches);
                $totalUnits = 0;
                foreach ($batches as $batch) {
                    $totalUnits += (int) ($batch['quantity_received'] ?? 0);
                }

                $consignor = Consignor::find($this->data['consignor_id'] ?? null);
                $consignorName = $consignor?->name ?? 'Selected Supplier';
                $drNumber = $this->data['dr_number'] ?? 'N/A';

                return new HtmlString(
                    '<div class="space-y-3 text-slate-700 text-left">'
                    .'<p class="text-sm sm:text-base leading-relaxed">'
                    .'You are receiving <strong class="font-semibold text-slate-900">'.$totalBatches.' batch(es)</strong> totaling <strong class="font-bold text-teal-800">'.number_format($totalUnits).' unit(s)</strong> from <strong class="font-semibold text-slate-900">'.$consignorName.'</strong>, DR <strong class="font-semibold text-slate-900">'.$drNumber.'</strong>. Confirm receiving?'
                    .'</p>'
                    .'<div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-900 flex items-start gap-2">'
                    .'<svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>'
                    .'<span>Please verify that physical quantities, lot numbers, and expiry dates match supplier delivery paperwork before confirming. Received items will be immediately added to depot inventory.</span>'
                    .'</div>'
                    .'</div>'
                );
            })
            ->modalSubmitActionLabel('Confirm & Receive into Stock')
            ->modalCancelActionLabel('Back');
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
