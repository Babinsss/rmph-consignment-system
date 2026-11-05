<?php

namespace App\Filament\Resources\StockRequests\Tables;

use App\Models\StockRequest;
use App\Services\InventoryService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Throwable;

class StockRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('RIS Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('department')
                    ->label('Stream')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pharmacy' => 'info',
                        'medical_supplies' => 'teal',
                        default => 'gray',
                    }),
                TextColumn::make('requesting_unit')
                    ->label('Unit / Ward')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester_name')
                    ->label('Requisitioned By')
                    ->searchable(),
                TextColumn::make('request_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'issued' => 'success',
                        'rejected', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('issuedBy.name')
                    ->label('Issued By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('request_date', 'desc')
            ->filters([
                SelectFilter::make('department')
                    ->options([
                        'pharmacy' => 'Pharmacy',
                        'medical_supplies' => 'Medical Supplies',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'approved' => 'Approved',
                        'issued' => 'Issued',
                        'rejected' => 'Rejected',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('info')
                    ->visible(fn (StockRequest $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (StockRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by_id' => Auth::id(),
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Request Approved')
                            ->body("Stock request #{$record->request_number} is now approved and ready for issuance.")
                            ->success()
                            ->send();
                    }),
                Action::make('issue')
                    ->label('Issue Stocks')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('success')
                    ->visible(fn (StockRequest $record): bool => in_array($record->status, ['pending', 'approved']))
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Consignment Stock Issuance')
                    ->modalDescription('This action will immediately deduct inventory batches via FEFO (nearest expiration first) and generate stockcard ledger movements.')
                    ->action(function (StockRequest $record) {
                        try {
                            $inventoryService = new InventoryService;
                            $inventoryService->issueRequest($record, Auth::user());

                            Notification::make()
                                ->title('Stocks Issued Successfully')
                                ->body("Stock request #{$record->request_number} has been issued and inventory balances updated.")
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Issuance Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('print_ris')
                    ->label('Print Slip')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('gray')
                    ->url(fn (StockRequest $record): string => route('requisition.print', ['stockRequest' => $record->id]))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
