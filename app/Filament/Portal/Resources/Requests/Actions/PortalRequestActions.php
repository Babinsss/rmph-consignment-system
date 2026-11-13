<?php

namespace App\Filament\Portal\Resources\Requests\Actions;

use App\Models\Request;
use App\Services\RequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PortalRequestActions
{
    public static function cancel(): Action
    {
        return Action::make('cancel')
            ->label('Cancel Request')
            ->icon('heroicon-o-no-symbol')
            ->color('gray')
            ->visible(fn (Request $record): bool => $record->isPending())
            ->requiresConfirmation()
            ->modalHeading(fn (Request $record): string => "Cancel Requisition #{$record->request_number}")
            ->modalDescription('Only pending requests that have not been issued can be cancelled.')
            ->schema([
                Textarea::make('reason')
                    ->label('Cancellation Reason (Optional)')
                    ->placeholder('e.g. Duplicate submission, order no longer needed...')
                    ->maxLength(500),
            ])
            ->action(function (Request $record, array $data): void {
                try {
                    app(RequestService::class)->cancelRequest($record, auth()->user(), $data['reason'] ?? null);
                } catch (ValidationException|InvalidArgumentException $exception) {
                    Notification::make()
                        ->title('Cannot Cancel Request')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Requisition Cancelled')
                    ->body("Requisition {$record->request_number} has been cancelled.")
                    ->success()
                    ->send();
            });
    }

    public static function printIssueSlip(): Action
    {
        return Action::make('print_issue_slip')
            ->label('Issue Slip PDF')
            ->icon('heroicon-o-printer')
            ->color('info')
            ->visible(fn (Request $record): bool => $record->total_issued > 0)
            ->url(fn (Request $record): string => route('reports.requests.issue-slip', $record), shouldOpenInNewTab: true);
    }
}
