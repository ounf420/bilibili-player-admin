<?php
namespace App\Filament\Admin\Resources;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static ?string $navigationLabel = '评论管理';
    protected static string | \UnitEnum | null $navigationGroup = '内容审核';
    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.nickname')->label('用户')->formatStateUsing(fn ($state, $record) => $record->user?->nickname ?: $record->user?->username ?: '匿名'),
                TextColumn::make('content')->label('内容')->limit(60),
                TextColumn::make('video_id')->label('视频ID')->limit(20),
                TextColumn::make('likes')->label('点赞')->sortable(),
                TextColumn::make('status')->label('状态')
                    ->formatStateUsing(fn ($state) => $state == 1 ? '✅ 正常' : '❌ 已隐藏'),
                TextColumn::make('created_at')->label('时间')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Action::make('hide')
                    ->label('隐藏')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 0])),
                Action::make('show')
                    ->label('恢复')
                    ->icon(Heroicon::OutlinedEye)
                    ->action(fn ($record) => $record->update(['status' => 1])),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CommentResource\Pages\ListComments::route('/'),
        ];
    }
}
