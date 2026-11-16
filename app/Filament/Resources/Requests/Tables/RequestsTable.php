<?php

namespace App\Filament\Resources\Requests\Tables;

use App\Enums\RequestStatus;
use App\Filament\Resources\Requests\Actions\RequestActions;
use App\Models\Request;
use App\Support\CustodianScope;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->poll('15s')
            ->defaultSort('request_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => CustodianScope::applyToRequests($query))
            ->emptyStateHeading('No Requisitions Received')
            ->emptyStateDescription('Supply requests submitted by hospital units (CSR, HDU, Dialysis, Pharmacies, Yakap) will appear here for custodian fulfillment.')
            ->emptyStateIcon('heroicon-o-inbox-stack')
            ->columns([
                TextColumn::make('request_number')
                    ->label('Request #')
                    ->badge()
                    ->description(fn (Request $record): ?string => $record->is_downtime
                        ? ('Downtime Slip'.($record->downtime_reference ? " • Ref: {$record->downtime_reference}" : ''))
                        : null)
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
                    ->badge()
                    ->description(function (Request $record): ?string {
                        if (! $record->isPending() || ! $record->created_at) {
                            return null;
                        }

                        $minutes = (int) $record->created_at->diffInMinutes(now());
                        $slaBreached = $record->is_urgent ? $minutes >= 15 : $minutes >= 120;
                        $elapsed = $minutes >= 60 ? floor($minutes / 60).'h '.($minutes % 60).'m' : "{$minutes}m";

                        return $slaBreached ? "SLA overdue ({$elapsed})" : "Waiting {$elapsed}";
                    }),

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
                RequestActions::fulfill(),
                RequestActions::reject(),
                RequestActions::printIssueSlip(),
            ])
            ->toolbarActions([]);
    }
}
