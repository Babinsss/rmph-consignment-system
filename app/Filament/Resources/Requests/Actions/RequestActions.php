<?php

namespace App\Filament\Resources\Requests\Actions;

use App\Models\Request;
use App\Services\FulfillmentService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;

class RequestActions
{
    public static function fulfill(): Action
    {
        return Action::make('fulfill')
            ->label('Fulfill (FEFO)')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (Request $record): bool => ($record->isPending() || $record->isPartiallyFulfilled())
                && (auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs']) ?? false)
            )
            ->modalHeading(fn (Request $record): string => "Fulfill Requisition #{$record->request_number}")
            ->modalDescription(fn (Request $record): string => "System recommends batches by First-Expiry-First-Out (FEFO) for {$record->unit->name}. Review or adjust the quantities to issue.")
            ->modalWidth('4xl')
            ->schema(function (Request $record): array {
                $recommendations = app(FulfillmentService::class)->getFefoRecommendations($record, auth()->user());
                $sections = [];

                if (empty($recommendations)) {
                    $sections[] = Placeholder::make('no_scoped_lines')
                        ->label('Category Scope Notice')
                        ->content(new HtmlString('<span class="text-sm font-medium text-amber-700">No pending items in this requisition fall under your assigned inventory category.</span>'));

                    return $sections;
                }

                foreach ($recommendations as $rec) {
                    $batchComponents = [];

                    if (empty($rec['batches'])) {
                        $batchComponents[] = Placeholder::make("no_stock_{$rec['line_id']}")
                            ->label('Stock Status')
                            ->content(new HtmlString('<span class="text-xs font-semibold text-red-600 dark:text-red-400">No available unexpired stock in Central Depot for this item.</span>'));
                    } else {
                        foreach ($rec['batches'] as $batch) {
                            $maxIssue = min((int) $batch['quantity_available'], (int) $rec['pending_quantity']);

                            $batchComponents[] = TextInput::make("allocations.{$rec['line_id']}.{$batch['batch_id']}")
                                ->label("Lot #{$batch['lot_number']} • Expires {$batch['expiration_date']} ({$batch['days_until_expiration']}d remaining)")
                                ->numeric()
                                ->minValue(0)
                                ->maxValue($maxIssue)
                                ->default($batch['suggested_issue_quantity'])
                                ->helperText("Depot Stock Available: {$batch['quantity_available']} units • Max for Line: {$rec['pending_quantity']} • FEFO Suggested: {$batch['suggested_issue_quantity']} units");
                        }
                    }

                    $sections[] = Section::make("[{$rec['item_code']}] {$rec['item_description']}")
                        ->description("Requested: {$rec['quantity_requested']} • Already Issued: {$rec['quantity_issued']} • Still Needed: {$rec['pending_quantity']}")
                        ->columns(2)
                        ->components($batchComponents);
                }

                return $sections;
            })
            ->action(function (Request $record, array $data): void {
                $allocations = [];
                foreach ($data['allocations'] ?? [] as $lineId => $batches) {
                    foreach ($batches as $batchId => $qty) {
                        if ((int) $qty > 0) {
                            $allocations[] = [
                                'line_id' => (int) $lineId,
                                'batch_id' => (int) $batchId,
                                'quantity' => (int) $qty,
                            ];
                        }
                    }
                }

                if (empty($allocations)) {
                    Notification::make()
                        ->title('No Stock Selected')
                        ->body('Please specify at least one batch quantity to issue.')
                        ->warning()
                        ->send();

                    return;
                }

                try {
                    $updated = app(FulfillmentService::class)->fulfillRequest($record, $allocations, auth()->user());
                } catch (ValidationException|InvalidArgumentException|RuntimeException $exception) {
                    Notification::make()
                        ->title('Issuance Blocked')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Requisition Fulfilled')
                    ->body("Requisition {$updated->request_number} marked as {$updated->status->getLabel()}. Depot stock and stock card updated.")
                    ->success()
                    ->send();
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Request $record): bool => $record->isPending()
                && (auth()->user()?->hasAnyRole(['admin', 'custodian', 'custodian_supplies', 'custodian_drugs']) ?? false)
            )
            ->requiresConfirmation()
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Reason for Rejection')
                    ->required()
                    ->placeholder('e.g. Temporarily out of depot stock, incorrect item requested, etc.'),
            ])
            ->action(function (Request $record, array $data): void {
                try {
                    app(FulfillmentService::class)->rejectRequest($record, $data['rejection_reason'], auth()->user());
                } catch (ValidationException|InvalidArgumentException|RuntimeException $exception) {
                    Notification::make()
                        ->title('Rejection Blocked')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Requisition Rejected')
                    ->body("Requisition {$record->request_number} has been rejected.")
                    ->danger()
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
