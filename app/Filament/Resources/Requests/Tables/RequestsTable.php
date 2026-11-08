<?php

namespace App\Filament\Resources\Requests\Tables;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Services\FulfillmentService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class RequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('request_date', 'desc')
            ->emptyStateHeading('No Requisitions Received')
            ->emptyStateDescription('Supply requests submitted by hospital units (CSR, HDU, Dialysis, Pharmacies, Yakap) will appear here for custodian fulfillment.')
            ->emptyStateIcon('heroicon-o-inbox-stack')
            ->columns([
                TextColumn::make('request_number')
                    ->label('Request #')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit.name')
                    ->label('Requesting Unit')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('request_date')
                    ->label('Date & Time')
                    ->dateTime('M j, Y • g:i A')
                    ->sortable(),

                TextColumn::make('is_urgent')
                    ->label('Priority')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'STAT (Urgent)' : 'Regular')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-bolt' : 'heroicon-m-calendar')
                    ->color(fn (bool $state): string => $state ? 'danger' : 'gray')
                    ->sortable(),

                TextColumn::make('lines_count')
                    ->label('Items')
                    ->counts('lines')
                    ->sortable(),

                TextColumn::make('total_requested')
                    ->label('Units Requested')
                    ->state(fn (Request $record): int => $record->total_requested),

                TextColumn::make('total_issued')
                    ->label('Units Issued')
                    ->state(fn (Request $record): int => $record->total_issued)
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('requestedBy.name')
                    ->label('Requested By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('unit_id')
                    ->label('Hospital Unit')
                    ->relationship('unit', 'name'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(RequestStatus::class),

                TernaryFilter::make('is_urgent')
                    ->label('Priority')
                    ->trueLabel('Urgent (STAT) Only')
                    ->falseLabel('Regular Only'),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('fulfill')
                    ->label('Fulfill (FEFO)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Request $record): bool => ($record->isPending() || $record->isPartiallyFulfilled())
                        && (auth()->user()?->hasAnyRole(['admin', 'custodian']) ?? false)
                    )
                    ->modalHeading(fn (Request $record): string => "Fulfill Requisition #{$record->request_number}")
                    ->modalDescription(fn (Request $record): string => "System recommends batches by First-Expiry-First-Out (FEFO) for {$record->unit->name}. Review or adjust the quantities to issue.")
                    ->modalWidth('4xl')
                    ->schema(function (Request $record): array {
                        $recommendations = app(FulfillmentService::class)->getFefoRecommendations($record);
                        $sections = [];

                        foreach ($recommendations as $rec) {
                            $batchComponents = [];

                            if (empty($rec['batches'])) {
                                $batchComponents[] = Placeholder::make("no_stock_{$rec['line_id']}")
                                    ->label('Stock Status')
                                    ->content(new HtmlString('<span class="text-xs font-semibold text-red-600 dark:text-red-400">⚠️ No available unexpired stock in Central Depot for this item!</span>'));
                            } else {
                                foreach ($rec['batches'] as $batch) {
                                    $batchComponents[] = TextInput::make("allocations.{$rec['line_id']}.{$batch['batch_id']}")
                                        ->label("Lot #{$batch['lot_number']} • Expires {$batch['expiration_date']} ({$batch['days_until_expiration']}d remaining)")
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($batch['quantity_available'])
                                        ->default($batch['suggested_issue_quantity'])
                                        ->helperText("Depot Stock Available: {$batch['quantity_available']} units • FEFO Suggested: {$batch['suggested_issue_quantity']} units");
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

                        $fulfillmentService = app(FulfillmentService::class);
                        $updated = $fulfillmentService->fulfillRequest($record, $allocations, auth()->user());

                        Notification::make()
                            ->title('Requisition Fulfilled')
                            ->body("Requisition {$updated->request_number} marked as {$updated->status->getLabel()}. Depot stock and stock card updated.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Request $record): bool => $record->isPending() && (auth()->user()?->hasAnyRole(['admin', 'custodian']) ?? false))
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->placeholder('e.g. Temporarily out of depot stock, incorrect item requested, etc.'),
                    ])
                    ->action(function (Request $record, array $data): void {
                        $record->update([
                            'status' => RequestStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Requisition Rejected')
                            ->body("Requisition {$record->request_number} has been rejected.")
                            ->danger()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
