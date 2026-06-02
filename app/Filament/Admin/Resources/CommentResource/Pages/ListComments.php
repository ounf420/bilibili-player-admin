<?php
namespace App\Filament\Admin\Resources\CommentResource\Pages;

use App\Filament\Admin\Resources\CommentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
