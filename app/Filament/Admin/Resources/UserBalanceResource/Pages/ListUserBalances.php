<?php

namespace App\Filament\Admin\Resources\UserBalanceResource\Pages;

use App\Filament\Admin\Resources\UserBalanceResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListUserBalances extends ListRecords
{
    protected static string $resource = UserBalanceResource::class;

    public function table(Table $table): Table
    {
        return UserBalanceResource::table($table);
    }
}
