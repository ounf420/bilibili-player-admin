<?php

namespace App\Filament\Admin\Resources\PlayerQuotas;

use App\Filament\Admin\Resources\PlayerQuotas\Pages\CreatePlayerQuota;
use App\Filament\Admin\Resources\PlayerQuotas\Pages\EditPlayerQuota;
use App\Filament\Admin\Resources\PlayerQuotas\Pages\ListPlayerQuotas;
use App\Filament\Admin\Resources\PlayerQuotas\Schemas\PlayerQuotaForm;
use App\Filament\Admin\Resources\PlayerQuotas\Tables\PlayerQuotasTable;
use App\Models\PlayerQuota;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlayerQuotaResource extends Resource
{
    protected static ?string $model = PlayerQuota::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = '播放器额度';

    protected static ?string $modelLabel = '额度';

    protected static ?string $pluralModelLabel = '额度';

    protected static ?int $navigationSort = 5;

    protected static string|\UnitEnum|null $navigationGroup = '用户管理';

    public static function form(Schema $schema): Schema
    {
        return PlayerQuotaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayerQuotasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerQuotas::route('/'),
            'create' => CreatePlayerQuota::route('/create'),
            'edit' => EditPlayerQuota::route('/{record}/edit'),
        ];
    }
}
