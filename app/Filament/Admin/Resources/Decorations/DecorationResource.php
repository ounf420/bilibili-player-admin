<?php

namespace App\Filament\Admin\Resources\Decorations;

use App\Filament\Admin\Resources\Decorations\Pages\CreateDecoration;
use App\Filament\Admin\Resources\Decorations\Pages\EditDecoration;
use App\Filament\Admin\Resources\Decorations\Pages\ListDecorations;
use App\Filament\Admin\Resources\Decorations\Schemas\DecorationForm;
use App\Filament\Admin\Resources\Decorations\Tables\DecorationsTable;
use App\Models\Decoration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DecorationResource extends Resource
{
    protected static ?string $model = Decoration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?string $navigationLabel = '广告装饰';

    protected static string|\UnitEnum|null $navigationGroup = '广告管理';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DecorationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DecorationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDecorations::route('/'),
            'create' => CreateDecoration::route('/create'),
            'edit' => EditDecoration::route('/{record}/edit'),
        ];
    }
}
