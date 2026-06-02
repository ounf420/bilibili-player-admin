<?php

namespace App\Filament\Admin\Resources\Ads\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('📢 基本信息')
                    ->schema([
                        TextInput::make('name')
                            ->label('广告名称')
                            ->required()
                            ->maxLength(200),

                        Select::make('type')
                            ->label('广告类型')
                            ->options([
                                'preroll_5s' => '⚡ 5秒极速前贴',
                                'preroll_15s' => '🎬 15秒标准前贴',
                                'preroll_30s' => '🎬 30秒标准前贴',
                                'preroll_60s' => '🎬 60秒标准前贴',
                                'preroll_trueview' => '⏭️ TrueView可跳过',
                                'midroll' => '⏸️ 中贴片广告',
                                'postroll' => '🏁 后贴片广告',
                                'pause_max' => '🖥️ 暂停MAX全屏',
                                'pause_mini' => '🔲 迷你暂停浮窗',
                                'splash' => '💫 开屏广告',
                                'overlay' => '🏷️ 视频角标',
                                'marquee' => '📜 跑马灯',
                                'qrcode' => '📱 扫码贴片',
                                'interactive' => '🎮 互动贴片',
                                'shake' => '📳 摇一摇广告',
                                'banner' => '🖼️ 横幅广告',
                                'brand' => '💎 品牌广告',
                            ])
                            ->required()
                            ->live()
                            ->searchable(),

                        Textarea::make('description')
                            ->label('广告描述')
                            ->rows(3),
                    ]),

                // ========== 视频类广告 ==========
                Section::make('🎬 视频素材')
                    ->schema([
                        Textarea::make('media_url')
                            ->label('视频/图片地址')
                            ->placeholder('https://example.com/ad.mp4 或 .png')
                            ->rows(3)
                            ->required()
                            ->helperText('支持视频(MP4/M3U8)和图片(PNG/JPG)格式'),

                        Select::make('media_type')
                            ->label('素材类型')
                            ->options([
                                'video' => '🎬 视频',
                                'image' => '🖼️ 图片',
                            ])
                            ->default('video'),

                        TextInput::make('duration')
                            ->label('播放时长(秒)')
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(120),

                        Toggle::make('skippable')
                            ->label('可跳过')
                            ->default(true),

                        TextInput::make('skip_after')
                            ->label('可跳过秒数')
                            ->numeric()
                            ->default(5)
                            ->helperText('播几秒后可跳过'),

                        Toggle::make('fullscreen')
                            ->label('全屏播放')
                            ->default(true)
                            ->helperText('广告视频是否全屏展示'),

                        TextInput::make('trigger_time')
                            ->label('触发时间(秒)')
                            ->numeric()
                            ->default(0)
                            ->helperText('中贴片专用：播放到第几秒触发，0=不触发'),
                    ])
                    ->visible(fn ($get) => in_array($get('type'), [
                        'preroll_5s', 'preroll_15s', 'preroll_30s', 'preroll_60s', 'preroll_trueview',
                        'midroll', 'postroll', 'pause_max', 'pause_mini', 'splash'
                    ])),

                // ========== 跑马灯广告 ==========
                Section::make('📜 跑马灯设置')
                    ->schema([
                        Textarea::make('text_content')
                            ->label('滚动文字')
                            ->rows(3)
                            ->required(),

                        TextInput::make('text_color')
                            ->label('文字颜色')
                            ->default('#ffffff'),

                        TextInput::make('brand_name')
                            ->label('品牌名称'),
                    ])
                    ->visible(fn ($get) => $get('type') === 'marquee'),

                // ========== 角标广告 ==========
                Section::make('🏷️ 角标设置')
                    ->schema([
                        Textarea::make('media_url')
                            ->label('角标素材')
                            ->placeholder('https://example.com/overlay.png 或 .mp4')
                            ->rows(2)
                            ->required()
                            ->helperText('支持图片(PNG/JPG)和视频(MP4)格式'),

                        Select::make('media_type')
                            ->label('素材类型')
                            ->options([
                                'image' => '🖼️ 图片',
                                'video' => '🎬 视频',
                            ])
                            ->default('image'),

                        Select::make('position')
                            ->label('显示位置')
                            ->options([
                                'top-left' => '左上角',
                                'top-right' => '右上角',
                                'bottom-left' => '左下角',
                                'bottom-right' => '右下角',
                            ])
                            ->default('bottom-right'),

                        TextInput::make('width')
                            ->label('宽度(px)')
                            ->numeric()
                            ->default(320),

                        TextInput::make('height')
                            ->label('高度(px)')
                            ->numeric()
                            ->default(180),
                    ])
                    ->visible(fn ($get) => $get('type') === 'overlay'),

                // ========== 扫码贴片 ==========
                Section::make('📱 扫码贴片设置')
                    ->schema([
                        Textarea::make('media_url')
                            ->label('二维码图片')
                            ->placeholder('https://example.com/qrcode.png')
                            ->rows(2)
                            ->required(),

                        TextInput::make('qrcode_url')
                            ->label('扫码链接')
                            ->placeholder('https://example.com/landing'),

                        TextInput::make('duration')
                            ->label('显示时长(秒)')
                            ->numeric()
                            ->default(15),

                        Toggle::make('closable')
                            ->label('可关闭')
                            ->default(true),
                    ])
                    ->visible(fn ($get) => $get('type') === 'qrcode'),

                // ========== 横幅广告 ==========
                Section::make('🖼️ 横幅设置')
                    ->schema([
                        Textarea::make('media_url')
                            ->label('横幅图片')
                            ->placeholder('https://example.com/banner.jpg')
                            ->rows(2)
                            ->required(),

                        TextInput::make('duration')
                            ->label('显示时长(秒)')
                            ->numeric()
                            ->default(10),
                    ])
                    ->visible(fn ($get) => $get('type') === 'banner'),

                // ========== 互动广告 ==========
                Section::make('🎮 互动设置')
                    ->schema([
                        Select::make('interactive_type')
                            ->label('互动类型')
                            ->options([
                                'swipe' => '滑动互动',
                                'shake' => '摇一摇',
                                'click' => '点击互动',
                            ])
                            ->default('click'),

                        Textarea::make('media_url')
                            ->label('互动素材')
                            ->rows(2)
                            ->required(),

                        TextInput::make('duration')
                            ->label('互动时长(秒)')
                            ->numeric()
                            ->default(10),
                    ])
                    ->visible(fn ($get) => in_array($get('type'), ['interactive', 'shake'])),

                // ========== 品牌信息（通用） ==========
                Section::make('💎 品牌信息')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand_name')
                                    ->label('品牌名称')
                                    ->placeholder('例如：小米科技'),

                                TextInput::make('brand_logo')
                                    ->label('品牌Logo URL')
                                    ->placeholder('https://example.com/logo.png')
                                    ->helperText('展示在广告底部的品牌图标'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('click_url')
                                    ->label('跳转链接')
                                    ->placeholder('https://example.com')
                                    ->helperText('用户点击广告后跳转的地址'),

                                TextInput::make('cta_text')
                                    ->label('按钮文字')
                                    ->default('了解更多')
                                    ->placeholder('了解更多'),
                            ]),
                    ])
                    ->visible(fn ($get) => !in_array($get('type'), ['marquee', 'overlay']))
                    ->collapsible(),

                // ========== 通用设置 ==========
                Section::make('⚙️ 通用设置')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('priority')
                                    ->label('优先级')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('数字越大越优先'),

                                TextInput::make('frequency_cap')
                                    ->label('频次上限')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('每用户每天最多展示次数，0=不限'),

                                Toggle::make('enabled')
                                    ->label('启用')
                                    ->default(true),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('start_date')
                                    ->label('开始日期')
                                    ->placeholder('2026-01-01'),

                                TextInput::make('end_date')
                                    ->label('结束日期')
                                    ->placeholder('2026-12-31'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('target_videos')
                                    ->label('指定视频ID')
                                    ->placeholder('v001,v002')
                                    ->helperText('留空=全部视频，多个用逗号分隔'),

                                TextInput::make('target_category')
                                    ->label('指定分类')
                                    ->placeholder('电影,电视剧')
                                    ->helperText('留空=全部分类'),
                            ]),
                    ]),
            ]);
    }
}
