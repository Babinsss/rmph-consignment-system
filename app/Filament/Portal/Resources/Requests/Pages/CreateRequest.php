<?php

namespace App\Filament\Portal\Resources\Requests\Pages;

use App\Filament\Portal\Resources\Requests\RequestResource;
use App\Models\User;
use App\Services\RequestService;
use App\Services\RequestWindowService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User $user */
        $user = auth()->user();

        $isUrgent = (bool) ($data['is_urgent'] ?? false);
        $windowService = app(RequestWindowService::class);
        $check = $windowService->canSubmitRequest($isUrgent);

        if (! $check['allowed']) {
            Notification::make()
                ->title('Regular Window Closed')
                ->body($check['message'])
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'is_urgent' => $check['message'],
            ]);
        }

        $requestService = app(RequestService::class);

        $data['request_number'] = $requestService->generateRequestNumber();
        $data['unit_id'] = $user->unit_id;
        $data['requested_by_id'] = $user->id;
        $data['request_date'] = Carbon::now('Asia/Manila');

        return $data;
    }
}
