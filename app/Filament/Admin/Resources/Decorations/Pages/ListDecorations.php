<?php

namespace App\Filament\Admin\Resources\Decorations\Pages;

use App\Filament\Admin\Resources\Decorations\DecorationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDecorations extends ListRecords
{
    protected static string $resource = DecorationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
