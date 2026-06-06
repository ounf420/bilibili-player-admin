<?php

namespace App\Filament\Resources\PlayerOrderResource\Pages;

use App\Filament\Resources\PlayerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayerOrder extends EditRecord
{
    protected static string $resource = PlayerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
