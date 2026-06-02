<?php

namespace App\Filament\Admin\Resources\Notices\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class NoticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('公告内容')
                    ->schema([
                        TextInput::make('title')
                            ->label('标题')
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('内容')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3', 'bulletList', 'orderedList',
                                'link', 'attachFiles', 'blockquote', 'codeBlock',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('公告设置')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('类型')
                                    ->options([
                                        'system' => '系统公告',
                                        'activity' => '活动公告',
                                        'update' => '更新日志',
                                        'maintenance' => '维护通知',
                                    ])
                                    ->default('system')
                                    ->required(),

                                Select::make('status')
                                    ->label('状态')
                                    ->options([
                                        0 => '草稿',
                                        1 => '已发布',
                                        2 => '已下线',
                                    ])
                                    ->default(0)
                                    ->required(),

                                Select::make('target_users')
                                    ->label('目标用户')
                                    ->options([
                                        'all' => '全部用户',
                                        'vip' => 'VIP用户',
                                        'new' => '新用户',
                                    ])
                                    ->default('all'),

                                Select::make('position')
                                    ->label('投放位置')
                                    ->options([
                                        'all' => '全站（不含播放器）',
                                        'home' => '首页',
                                        'v' => '影视页',
                                        'account' => '账号中心',
                                    ])
                                    ->default('all')
                                    ->required(),

                                Select::make('icon')
                                    ->label('图标')
                                    ->options([
                                        'fas fa-bell' => '🔔 铃铛',
                                        'fas fa-gift' => '🎁 礼物',
                                        'fas fa-star' => '⭐ 星星',
                                        'fas fa-fire' => '🔥 火焰',
                                        'fas fa-info-circle' => 'ℹ️ 信息',
                                        'fas fa-exclamation-triangle' => '⚠️ 警告',
                                        'fas fa-tools' => '🔧 工具',
                                        'fas fa-rocket' => '🚀 火箭',
                                    ])
                                    ->default('fas fa-bell'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_top')
                                    ->label('置顶')
                                    ->default(false),

                                Toggle::make('is_popup')
                                    ->label('弹窗显示')
                                    ->default(false),

                                Toggle::make('is_marquee')
                                    ->label('滚动显示')
                                    ->default(false),
                            ]),

                        Grid::make(2)
                            ->schema([
                                ColorPicker::make('bg_color')
                                    ->label('背景色')
                                    ->nullable(),

                                TextInput::make('sort_order')
                                    ->label('排序')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('数字越大越靠前'),
                            ]),
                    ]),

                Section::make('时间设置')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('published_at')
                                    ->label('发布时间')
                                    ->helperText('留空则立即发布'),

                                DateTimePicker::make('expires_at')
                                    ->label('过期时间')
                                    ->helperText('留空则永不过期'),
                            ]),
                    ]),
            ]);
    }
}
