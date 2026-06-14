<?php

namespace App\Filament\Admin\Resources\Decorations\Pages;

use App\Filament\Admin\Resources\Decorations\DecorationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDecoration extends EditRecord
{
    protected static string $resource = DecorationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
