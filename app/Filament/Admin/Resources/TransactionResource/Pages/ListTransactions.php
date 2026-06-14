<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use App\Filament\Admin\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function table(Table $table): Table
    {
        return TransactionResource::table($table);
    }
}
