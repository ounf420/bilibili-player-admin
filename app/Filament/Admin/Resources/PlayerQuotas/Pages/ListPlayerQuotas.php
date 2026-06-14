<?php

namespace App\Filament\Admin\Resources\PlayerQuotas\Pages;

use App\Filament\Admin\Resources\PlayerQuotas\PlayerQuotaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlayerQuotas extends ListRecords
{
    protected static string $resource = PlayerQuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
