<?php

namespace App\Filament\Resources\PlayerOrderResource\Pages;

use App\Filament\Resources\PlayerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerOrders extends ListRecords
{
    protected static string $resource = PlayerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
