<?php

namespace App\Filament\Admin\Resources\Decorations\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DecorationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🎨 基本信息')
                    ->schema([
                        TextInput::make('name')
                            ->label('方案名称')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('例如：品牌高端风格、简约风格'),

                        Toggle::make('enabled')
                            ->label('启用')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0)
                            ->helperText('数字越大越靠前'),
                    ]),

                Section::make('🏷️ 角标设置')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('badge_text')
                                    ->label('角标文字')
                                    ->default('推广')
                                    ->placeholder('推广'),

                                TextInput::make('badge_color')
                                    ->label('角标背景色')
                                    ->default('rgba(255,255,255,0.15)'),

                                TextInput::make('badge_text_color')
                                    ->label('角标文字色')
                                    ->default('#ffffff'),
                            ]),
                    ]),

                Section::make('📊 进度条设置')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('progress_color')
                                    ->label('进度条颜色')
                                    ->default('#00c853')
                                    ->placeholder('#00c853'),

                                TextInput::make('progress_bg')
                                    ->label('进度条背景色')
                                    ->default('rgba(255,255,255,0.2)'),
                            ]),

                        Toggle::make('show_progress_bar')
                            ->label('显示进度条')
                            ->default(true),
                    ]),

                Section::make('🌑 遮罩效果')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('overlay_opacity')
                                    ->label('遮罩透明度')
                                    ->options([
                                        '0.5' => '浅 (0.5)',
                                        '0.6' => '较浅 (0.6)',
                                        '0.7' => '标准 (0.7)',
                                        '0.8' => '较深 (0.8)',
                                        '0.9' => '深 (0.9)',
                                        '1.0' => '全黑 (1.0)',
                                    ])
                                    ->default('0.7'),

                                Select::make('overlay_gradient')
                                    ->label('遮罩方向')
                                    ->options([
                                        'top' => '顶部渐变',
                                        'bottom' => '底部渐变',
                                        'both' => '上下渐变',
                                    ])
                                    ->default('both'),
                            ]),
                    ]),

                Section::make('✨ 动画效果')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('animation')
                                    ->label('入场动画')
                                    ->options([
                                        'none' => '无动画',
                                        'fade' => '淡入',
                                        'slide' => '滑入',
                                        'zoom' => '缩放',
                                    ])
                                    ->default('fade'),

                                Select::make('text_stroke')
                                    ->label('文字描边')
                                    ->options([
                                        '0' => '无描边',
                                        '1' => '轻描边',
                                        '2' => '中描边',
                                        '3' => '重描边',
                                    ])
                                    ->default('0'),
                            ]),

                        TextInput::make('text_shadow_color')
                            ->label('文字阴影色')
                            ->default('rgba(0,0,0,0.8)'),
                    ]),

                Section::make('🔘 按钮样式')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('cta_style')
                                    ->label('按钮形状')
                                    ->options([
                                        'rounded' => '圆角',
                                        'pill' => '胶囊形',
                                        'rect' => '方形',
                                    ])
                                    ->default('rounded'),

                                TextInput::make('cta_color')
                                    ->label('按钮颜色')
                                    ->default('#00c853'),

                                TextInput::make('cta_text_color')
                                    ->label('按钮文字色')
                                    ->default('#ffffff'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('close_btn_style')
                                    ->label('关闭按钮样式')
                                    ->options([
                                        'icon' => '仅图标 ✕',
                                        'text' => '仅文字',
                                        'both' => '图标+文字',
                                    ])
                                    ->default('icon'),

                                Select::make('countdown_style')
                                    ->label('倒计时样式')
                                    ->options([
                                        'text' => '纯文字',
                                        'bar' => '进度条',
                                        'circular' => '环形',
                                    ])
                                    ->default('text'),
                            ]),
                    ]),

                Section::make('📐 布局控制')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('show_brand_area')
                                    ->label('显示品牌区域')
                                    ->default(true)
                                    ->helperText('底部品牌Logo+名称'),
                            ]),
                    ]),

                Section::make('💻 高级设置')
                    ->schema([
                        Textarea::make('custom_css')
                            ->label('自定义CSS')
                            ->rows(4)
                            ->placeholder('.ad-overlay { border: 2px solid #fff; }')
                            ->helperText('留空则使用默认样式，仅限高级用户'),
                    ])
                    ->collapsible(),
            ]);
    }
}
