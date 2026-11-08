<?php

namespace App\Filament\Portal\Resources\Requests\Tables;

use App\Enums\RequestStatus;
use App\Models\Request;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('request_date', 'desc')
            ->emptyStateHeading('No Requisitions Submitted Yet')
            ->emptyStateDescription('Your unit has not submitted any supply requisitions. Click the button below to request medical supplies or medicines from the central depot.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->columns([
                TextColumn::make('request_number')
                    ->label('Request #')
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
                    ->color(fn (bool $state): string => $state ? 'danger' : 'gray'),

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
                EditAction::make()
                    ->visible(fn (Request $record): bool => $record->isPending()),
            ])
            ->toolbarActions([]);
    }
}
