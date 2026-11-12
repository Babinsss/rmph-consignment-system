<?php

namespace App\Filament\Resources\Items\Pages;

use App\Enums\ItemCategory;
use App\Filament\Resources\Items\ItemResource;
use App\Models\Item;
use App\Support\CustodianScope;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        $category = $user?->custodianCategory();

        // For category-scoped custodians, only the relevant tab is shown.
        if ($category === ItemCategory::Supply) {
            return [
                'supplies' => Tab::make('Medical Supplies')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Supply))
                    ->badge(Item::where('category', ItemCategory::Supply)->count()),
            ];
        }

        if ($category === ItemCategory::Drug) {
            return [
                'drugs' => Tab::make('Drugs & Medicine')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Drug))
                    ->badge(Item::where('category', ItemCategory::Drug)->count()),
            ];
        }

        // Admin / generic custodian / viewer — full tab set.
        return [
            'all' => Tab::make('All Items')
                ->badge(Item::count()),
            'supplies' => Tab::make('Medical Supplies')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Supply))
                ->badge(Item::where('category', ItemCategory::Supply)->count()),
            'drugs' => Tab::make('Drugs & Medicine')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Drug))
                ->badge(Item::where('category', ItemCategory::Drug)->count()),
        ];
    }
}
