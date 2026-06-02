<?php
namespace App\Filament\Admin\Resources;

use App\Models\Danmaku;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class DanmakuResource extends Resource
{
    protected static ?string $model = Danmaku::class;
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;
    protected static ?string $navigationLabel = '弹幕管理';
    protected static string | \UnitEnum | null $navigationGroup = '内容审核';
    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->limit(12),
                TextColumn::make('content')->label('内容')->limit(50),
                TextColumn::make('user')->label('用户')->limit(20),
                TextColumn::make('color')->label('颜色')
                    ->formatStateUsing(fn ($state) => '<span style="background:' . $state . ';padding:2px 8px;border-radius:3px;color:#fff;font-size:11px;">' . $state . '</span>')->html(),
                TextColumn::make('time')->label('时间(秒)'),
                TextColumn::make('video_id')->label('视频ID')->limit(12),
                TextColumn::make('status')->label('状态')
                    ->formatStateUsing(fn ($state) => $state == 1 ? '✅' : '❌'),
                TextColumn::make('created_at')->label('时间')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Action::make('hide')
                    ->label('隐藏')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->requiresConfirmation()
                    ->action(fn ($record) => DB::table('danmaku')->where('id', $record->id)->update(['status' => 0, 'enabled' => 0])),
                Action::make('show')
                    ->label('恢复')
                    ->icon(Heroicon::OutlinedEye)
                    ->action(fn ($record) => DB::table('danmaku')->where('id', $record->id)->update(['status' => 1, 'enabled' => 1])),
                DeleteAction::make()
                    ->action(fn ($record) => DB::table('danmaku')->where('id', $record->id)->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => \App\Filament\Admin\Resources\DanmakuResource\Pages\ListDanmaku::route('/')];
    }
}
