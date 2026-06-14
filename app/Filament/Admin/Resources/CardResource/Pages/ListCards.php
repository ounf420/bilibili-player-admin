<?php

namespace App\Filament\Admin\Resources\CardResource\Pages;

use App\Filament\Admin\Resources\CardResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    public function table(Table $table): Table
    {
        return CardResource::table($table);
    }
}
