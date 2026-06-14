<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;

class AdSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;
    protected static ?string $navigationLabel = '广告设置';
    protected static string|\UnitEnum|null $navigationGroup = '广告管理';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = '广告全局设置';
    protected string $view = 'filament.admin.pages.ad-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'force_platform_ads' => Cache::get('force_platform_ads', false),
            'force_platform_positions' => Cache::get('force_platform_positions', []),
            'force_user_ads' => Cache::get('force_user_ads', false),
            'force_user_positions' => Cache::get('force_user_positions', []),
            'default_ad_mode' => Cache::get('default_ad_mode', 'platform'),
            'max_user_ads' => Cache::get('max_user_ads', 5),
            'ad_skip_default' => Cache::get('ad_skip_default', 5),
            'preroll_duration' => Cache::get('preroll_duration', 15),
            'midroll_duration' => Cache::get('midroll_duration', 15),
            'postroll_duration' => Cache::get('postroll_duration', 15),
            'pause_duration' => Cache::get('pause_duration', 0),
            'splash_duration' => Cache::get('splash_duration', 5),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('平台广告控制')
                    ->description('控制平台广告的全局展示策略')
                    ->schema([
                        Toggle::make('force_platform_ads')
                            ->label('强制展示平台广告')
                            ->default(false)
                            ->live()
                            ->helperText('开启后，所有播放器都将强制展示平台广告素材（覆盖用户设置）'),
                        
                        CheckboxList::make('force_platform_positions')
                            ->label('强制展示的平台广告位置')
                            ->options([
                                'splash' => '开屏广告',
                                'preroll' => '前贴片',
                                'midroll' => '中贴片',
                                'postroll' => '后贴片',
                                'pause' => '暂停广告',
                            ])
                            ->columns(5)
                            ->visible(fn ($get) => $get('force_platform_ads'))
                            ->helperText('不选则强制全部位置，选择后只强制对应位置的平台广告'),
                    ]),
                
                Section::make('用户广告控制')
                    ->description('强制展示用户配置的广告素材')
                    ->schema([
                        Toggle::make('force_user_ads')
                            ->label('强制展示用户广告')
                            ->default(false)
                            ->live()
                            ->helperText('开启后，强制展示用户在对应位置配置的广告素材（即使用户关闭了广告）'),
                        
                        CheckboxList::make('force_user_positions')
                            ->label('强制展示的用户广告位置')
                            ->options([
                                'preroll' => '前贴片',
                                'midroll' => '中贴片',
                                'postroll' => '后贴片',
                                'pause' => '暂停广告',
                            ])
                            ->columns(4)
                            ->visible(fn ($get) => $get('force_user_ads'))
                            ->helperText('选择要强制展示的用户广告位置'),
                    ]),
                
                Section::make('广告模式默认值')
                    ->description('新创建播放器的默认广告设置')
                    ->schema([
                        Select::make('default_ad_mode')
                            ->label('新播放器默认广告模式')
                            ->options([
                                'platform' => '平台广告',
                                'user' => '用户广告',
                                'mixed' => '混合模式',
                                'none' => '无广告',
                            ])
                            ->default('platform')
                            ->required()
                            ->helperText('新创建的播放器默认使用的广告模式'),
                    ]),
                
                Section::make('用户广告限制')
                    ->description('限制用户自定义广告的数量和行为')
                    ->schema([
                        TextInput::make('max_user_ads')
                            ->label('每个播放器最大广告数')
                            ->numeric()
                            ->default(5)
                            ->minValue(0)
                            ->maxValue(50)
                            ->required()
                            ->helperText('每个播放器最多可以配置多少个自定义广告，设为0禁止用户广告'),
                        
                        TextInput::make('ad_skip_default')
                            ->label('默认跳过等待秒数')
                            ->numeric()
                            ->default(5)
                            ->minValue(0)
                            ->maxValue(30)
                            ->required()
                            ->helperText('广告播放多少秒后显示跳过按钮'),
                    ]),
                
                Section::make('广告时长配置')
                    ->description('配置各类广告的默认时长（秒）')
                    ->schema([
                        TextInput::make('preroll_duration')
                            ->label('前贴片时长')
                            ->numeric()
                            ->default(15)
                            ->minValue(0)
                            ->maxValue(120)
                            ->suffix('秒')
                            ->required(),
                        
                        TextInput::make('midroll_duration')
                            ->label('中贴片时长')
                            ->numeric()
                            ->default(15)
                            ->minValue(0)
                            ->maxValue(120)
                            ->suffix('秒')
                            ->required(),
                        
                        TextInput::make('postroll_duration')
                            ->label('后贴片时长')
                            ->numeric()
                            ->default(15)
                            ->minValue(0)
                            ->maxValue(120)
                            ->suffix('秒')
                            ->required(),
                        
                        TextInput::make('pause_duration')
                            ->label('暂停广告时长')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(60)
                            ->suffix('秒')
                            ->helperText('设为0则暂停广告不自动关闭')
                            ->required(),
                        
                        TextInput::make('splash_duration')
                            ->label('开屏广告时长')
                            ->numeric()
                            ->default(5)
                            ->minValue(0)
                            ->maxValue(30)
                            ->suffix('秒')
                            ->helperText('设为0则开屏广告不自动关闭')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        Cache::put('force_platform_ads', $data['force_platform_ads'], now()->addYears(1));
        Cache::put('force_platform_positions', $data['force_platform_positions'] ?? [], now()->addYears(1));
        Cache::put('force_user_ads', $data['force_user_ads'] ?? false, now()->addYears(1));
        Cache::put('force_user_positions', $data['force_user_positions'] ?? [], now()->addYears(1));
        Cache::put('default_ad_mode', $data['default_ad_mode'], now()->addYears(1));
        Cache::put('max_user_ads', $data['max_user_ads'], now()->addYears(1));
        Cache::put('ad_skip_default', $data['ad_skip_default'], now()->addYears(1));
        Cache::put('preroll_duration', $data['preroll_duration'], now()->addYears(1));
        Cache::put('midroll_duration', $data['midroll_duration'], now()->addYears(1));
        Cache::put('postroll_duration', $data['postroll_duration'], now()->addYears(1));
        Cache::put('pause_duration', $data['pause_duration'], now()->addYears(1));
        Cache::put('splash_duration', $data['splash_duration'], now()->addYears(1));

        Notification::make()
            ->title('广告设置已保存')
            ->success()
            ->send();
    }
}
