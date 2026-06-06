<?php

namespace App\Filament\Resources\PlayerPlanResource\Pages;

use App\Filament\Resources\PlayerPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerPlans extends ListRecords
{
    protected static string $resource = PlayerPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
