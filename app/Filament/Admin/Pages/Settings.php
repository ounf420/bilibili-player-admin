<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.admin.pages.settings';

    protected static ?string $navigationLabel = '播放器设置';

    protected static ?string $title = '播放器设置';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = DB::table('player_settings')
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🎨 水印设置')
                    ->description('设置播放器右上角显示的水印')
                    ->schema([
                        Select::make('logo_type')
                            ->label('水印类型')
                            ->options([
                                'text' => '📝 纯文字',
                                'image' => '🖼️ 图片Logo',
                            ])
                            ->default('text')
                            ->required()
                            ->live(),

                        TextInput::make('logo_text')
                            ->label('水印文字')
                            ->placeholder('例如：B站播放器')
                            ->helperText('输入想要显示的文字')
                            ->visible(fn ($get) => $get('logo_type') === 'text'),

                        TextInput::make('logo_url')
                            ->label('Logo图片地址')
                            ->placeholder('https://example.com/logo.png')
                            ->helperText('输入Logo图片的网址')
                            ->visible(fn ($get) => $get('logo_type') === 'image'),

                        Select::make('logo_position')
                            ->label('水印位置')
                            ->options([
                                'top-left' => '↖️ 左上角',
                                'top-right' => '↗️ 右上角',
                                'bottom-left' => '↙️ 左下角',
                                'bottom-right' => '↘️ 右下角',
                            ])
                            ->default('top-right'),
                    ]),

                Section::make('🎮 播放设置')
                    ->description('控制视频播放的基本行为')
                    ->schema([
                        Toggle::make('autoplay')
                            ->label('自动播放')
                            ->helperText('打开页面自动播放视频')
                            ->default(true),

                        Toggle::make('loop')
                            ->label('循环播放')
                            ->helperText('视频播完自动重播')
                            ->default(false),

                        Select::make('preload')
                            ->label('预加载')
                            ->options([
                                'auto' => '自动（推荐）',
                                'metadata' => '仅加载信息',
                                'none' => '不预加载',
                            ])
                            ->default('auto'),

                        TextInput::make('volume')
                            ->label('默认音量')
                            ->numeric()
                            ->default(0.7)
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1)
                            ->helperText('0是静音，1是最大'),
                    ]),

                Section::make('🎬 广告时段设置')
                    ->description('控制各类广告的播放时长和填充逻辑')
                    ->schema([
                        TextInput::make('preroll_duration')
                            ->label('前贴片时段时长（秒）')
                            ->numeric()
                            ->default(120)
                            ->minValue(0)
                            ->maxValue(600)
                            ->step(1)
                            ->helperText('多条广告连续播放，超时截断，不足循环填充。设为0不播。'),

                        TextInput::make('midroll_duration')
                            ->label('中贴片时段时长（秒）')
                            ->numeric()
                            ->default(60)
                            ->minValue(0)
                            ->maxValue(300)
                            ->step(1)
                            ->helperText('中插广告的连续播放时长。设为0不播。'),

                        TextInput::make('postroll_duration')
                            ->label('后贴片时段时长（秒）')
                            ->numeric()
                            ->default(60)
                            ->minValue(0)
                            ->maxValue(300)
                            ->step(1)
                            ->helperText('片尾广告的连续播放时长。设为0不播。'),
                    ]),

                Section::make('👑 VIP免广告控制')
                    ->description('控制VIP用户是否跳过前贴片广告（其他广告VIP也要看）')
                    ->schema([
                        Toggle::make('vip_skip_ads_gold')
                            ->label('🥇 黄金VIP免广告')
                            ->helperText('开启后黄金VIP用户跳过前贴片广告')
                            ->default(true),

                        Toggle::make('vip_skip_ads_diamond')
                            ->label('💎 钻石VIP免广告')
                            ->helperText('开启后钻石VIP用户跳过前贴片广告')
                            ->default(true),

                        Toggle::make('vip_skip_ads_star')
                            ->label('👑 星钻VIP免广告')
                            ->helperText('开启后星钻VIP用户跳过前贴片广告')
                            ->default(true),
                    ]),

                Section::make('✨ 外观设置')
                    ->description('自定义播放器外观')
                    ->schema([
                        ColorPicker::make('theme_color')
                            ->label('主题颜色')
                            ->default('#00a1d6')
                            ->helperText('播放器按钮的颜色'),

                        Toggle::make('show_screenshot')
                            ->label('截图按钮')
                            ->helperText('是否显示截图按钮')
                            ->default(true),

                        Toggle::make('show_setting')
                            ->label('设置按钮')
                            ->helperText('是否显示设置按钮')
                            ->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            DB::table('player_settings')
                ->where('setting_key', $key)
                ->update(['setting_value' => $value]);
        }

        $this->notify('success', '设置已保存！');
    }
}
