<?php

namespace App\Filament\Portal\Resources\Items\Pages;

use App\Enums\ItemCategory;
use App\Filament\Portal\Resources\Items\ItemResource;
use App\Models\Item;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Consigned Items')
                ->badge(Item::where('is_active', true)->count()),
            'supplies' => Tab::make('Medical Supplies')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Supply))
                ->badge(Item::where('is_active', true)->where('category', ItemCategory::Supply)->count()),
            'drugs' => Tab::make('Drugs & Medicine')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('category', ItemCategory::Drug))
                ->badge(Item::where('is_active', true)->where('category', ItemCategory::Drug)->count()),
        ];
    }
}
