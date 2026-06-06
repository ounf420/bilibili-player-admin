<?php

namespace App\Filament\Resources\PlayerOrderResource\Pages;

use App\Filament\Resources\PlayerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlayerOrder extends ViewRecord
{
    protected static string $resource = PlayerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
