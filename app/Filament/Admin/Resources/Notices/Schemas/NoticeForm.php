<?php

namespace App\Filament\Admin\Resources\Notices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                Section::make('📢 公告内容')
                    ->schema([
                        TextInput::make('title')
                            ->label('公告标题')
                            ->required()
                            ->maxLength(200)
                            ->placeholder('请输入公告标题'),

                        Select::make('type')
                            ->label('公告类型')
                            ->options([
                                'system' => '📋 系统公告',
                                'update' => '🔄 更新日志',
                                'activity' => '🎉 活动公告',
                                'maintenance' => '🔧 维护通知',
                                'feature' => '✨ 新功能',
                                'security' => '🔒 安全提醒',
                            ])
                            ->default('system')
                            ->required(),

                        RichEditor::make('content')
                            ->label('公告内容')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3', 'bulletList', 'orderedList',
                                'link', 'attachFiles', 'blockquote', 'codeBlock',
                            ])
                            ->columnSpanFull(),

                        Textarea::make('summary')
                            ->label('摘要')
                            ->rows(2)
                            ->maxLength(200)
                            ->placeholder('简短描述，用于列表展示（留空自动截取）')
                            ->helperText('最多200字，用于弹窗和列表预览'),
                    ]),

                Section::make('🎨 显示设置')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('icon')
                                    ->label('图标')
                                    ->options([
                                        'fas fa-bell' => '🔔 铃铛',
                                        'fas fa-bullhorn' => '📣 喇叭',
                                        'fas fa-gift' => '🎁 礼物',
                                        'fas fa-star' => '⭐ 星星',
                                        'fas fa-fire' => '🔥 火焰',
                                        'fas fa-rocket' => '🚀 火箭',
                                        'fas fa-info-circle' => 'ℹ️ 信息',
                                        'fas fa-exclamation-triangle' => '⚠️ 警告',
                                        'fas fa-shield-alt' => '🔒 安全',
                                        'fas fa-tools' => '🔧 工具',
                                        'fas fa-sync' => '🔄 更新',
                                        'fas fa-magic' => '✨ 新功能',
                                    ])
                                    ->default('fas fa-bell'),

                                Select::make('bg_color')
                                    ->label('主题色')
                                    ->options([
                                        'linear-gradient(135deg, #667eea, #764ba2)' => '💜 紫色（默认）',
                                        'linear-gradient(135deg, #f59e0b, #f97316)' => '🟠 橙色',
                                        'linear-gradient(135deg, #10b981, #059669)' => '🟢 绿色',
                                        'linear-gradient(135deg, #3b82f6, #2563eb)' => '🔵 蓝色',
                                        'linear-gradient(135deg, #ef4444, #dc2626)' => '🔴 红色',
                                        'linear-gradient(135deg, #ec4899, #db2777)' => '🩷 粉色',
                                    ])
                                    ->default('linear-gradient(135deg, #667eea, #764ba2)'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_popup')
                                    ->label('首页弹窗')
                                    ->default(true)
                                    ->helperText('用户打开首页时弹出'),

                                Toggle::make('is_marquee')
                                    ->label('滚动公告')
                                    ->default(false)
                                    ->helperText('在页面顶部滚动显示'),

                                Toggle::make('is_top')
                                    ->label('置顶')
                                    ->default(false)
                                    ->helperText('置顶显示在公告列表最前面'),
                            ]),
                    ]),

                Section::make('👥 发布对象')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('target_users')
                                    ->label('目标用户')
                                    ->options([
                                        'all' => '全部用户',
                                        'new' => '新用户（7天内注册）',
                                        'active' => '活跃用户',
                                        'vip' => 'VIP用户',
                                    ])
                                    ->default('all'),

                                Select::make('position')
                                    ->label('展示位置')
                                    ->options([
                                        'all' => '全站',
                                        'home' => '仅首页',
                                        'user' => '仅用户中心',
                                        'player' => '仅播放器',
                                    ])
                                    ->default('all'),
                            ]),
                    ]),

                Section::make('⏰ 时间设置')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('发布状态')
                                    ->options([
                                        0 => '📝 草稿',
                                        1 => '✅ 已发布',
                                        2 => '⏸️ 已下线',
                                    ])
                                    ->default(0)
                                    ->required(),

                                DateTimePicker::make('published_at')
                                    ->label('定时发布')
                                    ->helperText('留空则立即发布'),

                                DateTimePicker::make('expires_at')
                                    ->label('过期时间')
                                    ->helperText('留空则永不过期'),
                            ]),

                        TextInput::make('sort_order')
                            ->label('排序权重')
                            ->numeric()
                            ->default(0)
                            ->helperText('数字越大越靠前'),
                    ]),
            ]);
    }
}
