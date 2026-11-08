<?php

namespace App\Filament\Portal\Resources\Requests\Pages;

use App\Enums\ItemCategory;
use App\Filament\Portal\Resources\Requests\RequestResource;
use App\Models\Item;
use App\Models\User;
use App\Services\RequestService;
use App\Services\RequestWindowService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected string $view = 'filament.portal.pages.create-request';

    public string $search = '';

    public string $categoryFilter = 'all';

    public array $cart = []; // [$itemId => $quantity]

    public bool $is_urgent = false;

    public string $urgent_reason = '';

    public string $notes = '';

    public bool $showConfirmModal = false;

    public array $presetReasons = [
        'Acute Trauma Emergency',
        'Critical Low Stock in Ward',
        'STAT Bedside Procedure',
        'Unexpected Surgical Complication',
        'ICU/HDU Rapid Response',
    ];

    public function mount(): void
    {
        parent::mount();

        if (request()->query('urgent') == 1) {
            $this->is_urgent = true;
        }
    }

    public function toggleUrgent(): void
    {
        $this->is_urgent = ! $this->is_urgent;
        if (! $this->is_urgent) {
            $this->urgent_reason = '';
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function getIsWindowOpenProperty(): bool
    {
        return app(RequestWindowService::class)->isRegularWindowOpen();
    }

    public function getScheduleSummaryProperty(): string
    {
        return app(RequestWindowService::class)->getScheduleSummary();
    }

    public function addToCart(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]++;
        } else {
            $this->cart[$itemId] = 1;
        }
    }

    public function incrementQuantity(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]++;
        }
    }

    public function decrementQuantity(int $itemId): void
    {
        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]--;
            if ($this->cart[$itemId] <= 0) {
                unset($this->cart[$itemId]);
            }
        }
    }

    public function removeFromCart(int $itemId): void
    {
        unset($this->cart[$itemId]);
    }

    public function selectPresetReason(string $preset): void
    {
        $this->is_urgent = true;
        $clean = trim($this->urgent_reason);

        if (empty($clean)) {
            $this->urgent_reason = $preset.': ';
        } elseif (! str_contains($clean, $preset)) {
            $this->urgent_reason = $clean.' • '.$preset;
        }
    }

    public function openConfirm(): void
    {
        $this->validateRequisition();
        $this->showConfirmModal = true;
    }

    public function closeConfirm(): void
    {
        $this->showConfirmModal = false;
    }

    public function submitRequisition(): mixed
    {
        $this->validateRequisition();

        /** @var User $user */
        $user = auth()->user();

        $lines = [];
        foreach ($this->cart as $itemId => $qty) {
            if ($qty > 0) {
                $lines[] = [
                    'item_id' => (int) $itemId,
                    'quantity_requested' => (int) $qty,
                ];
            }
        }

        $service = app(RequestService::class);
        $request = $service->submitRequest([
            'unit_id' => $user->unit_id,
            'is_urgent' => $this->is_urgent,
            'urgent_reason' => $this->is_urgent ? $this->urgent_reason : null,
            'notes' => $this->notes,
            'lines' => $lines,
        ], $user);

        Notification::make()
            ->title($this->is_urgent ? 'STAT Emergency Requisition Sent' : 'Requisition Submitted')
            ->body("Requisition #{$request->request_number} submitted to Central Storage depot.")
            ->success()
            ->send();

        return redirect()->to(url('/portal/requests'));
    }

    protected function validateRequisition(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Requisition List Empty')
                ->body('Please add at least one consigned item to your request list.')
                ->warning()
                ->send();

            throw ValidationException::withMessages([
                'cart' => 'Please add at least one consigned item to your request list.',
            ]);
        }

        $windowService = app(RequestWindowService::class);
        $check = $windowService->canSubmitRequest($this->is_urgent);

        if (! $check['allowed']) {
            Notification::make()
                ->title('Regular Window Closed')
                ->body($check['message'])
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'window' => $check['message'],
            ]);
        }

        if ($this->is_urgent) {
            $reason = trim($this->urgent_reason);
            if (empty($reason) || mb_strlen($reason) < 10) {
                Notification::make()
                    ->title('Clinical Reason Required')
                    ->body('Please provide a clinical justification of at least 10 characters for STAT requests.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'urgent_reason' => 'Mandatory clinical justification must be at least 10 characters.',
                ]);
            }
        }
    }

    public function getAvailableItems(): Collection
    {
        $query = Item::query()
            ->where('is_active', true)
            ->with('consignor')
            ->withSum('activeBatches as current_stock', 'quantity_remaining');

        if ($this->categoryFilter === 'supply') {
            $query->where('category', ItemCategory::Supply);
        } elseif ($this->categoryFilter === 'drug') {
            $query->where('category', ItemCategory::Drug);
        }

        if (filled($this->search)) {
            $term = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', $term)
                    ->orWhere('code', 'like', $term);
            });
        }

        return $query->orderBy('description')->get();
    }

    public function getCartItemsProperty(): array
    {
        if (empty($this->cart)) {
            return [];
        }

        $items = Item::whereIn('id', array_keys($this->cart))->get()->keyBy('id');
        $result = [];

        foreach ($this->cart as $itemId => $qty) {
            if (isset($items[$itemId])) {
                $result[] = [
                    'item' => $items[$itemId],
                    'quantity' => $qty,
                ];
            }
        }

        return $result;
    }

    public function getTotalUnitsProperty(): int
    {
        return array_sum($this->cart);
    }
}
