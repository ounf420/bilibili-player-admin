<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;

class PlayerQuotaSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel = '额度设置';
    protected static string|\UnitEnum|null $navigationGroup = '用户管理';
    protected static ?int $navigationSort = 6;
    protected static ?string $title = '播放器额度设置';
    protected string $view = 'filament.admin.pages.player-quota-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_quota' => Cache::get('player_default_quota', 1),
            'max_quota' => Cache::get('player_max_quota', 10),
            'enable_purchase' => Cache::get('player_enable_purchase', false),
            'price_per_quota' => Cache::get('player_price_per_quota', 9.9),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('默认配置')
                    ->description('新用户注册时的默认额度配置')
                    ->schema([
                        TextInput::make('default_quota')
                            ->label('注册赠送额度')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->helperText('新用户注册时自动赠送的播放器数量'),
                        
                        TextInput::make('max_quota')
                            ->label('最大额度上限')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(1000)
                            ->required()
                            ->helperText('单个用户最多可拥有的播放器数量'),
                    ]),
                
                Section::make('购买配置')
                    ->description('额度购买相关配置')
                    ->schema([
                        Toggle::make('enable_purchase')
                            ->label('启用额度购买')
                            ->default(false)
                            ->helperText('开启后用户可在前端购买播放器额度'),
                        
                        TextInput::make('price_per_quota')
                            ->label('单价（元/个）')
                            ->numeric()
                            ->default(9.9)
                            ->minValue(0)
                            ->maxValue(9999)
                            ->prefix('¥')
                            ->required()
                            ->helperText('每个播放器额度的购买价格'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        Cache::put('player_default_quota', $data['default_quota'], now()->addYears(1));
        Cache::put('player_max_quota', $data['max_quota'], now()->addYears(1));
        Cache::put('player_enable_purchase', $data['enable_purchase'], now()->addYears(1));
        Cache::put('player_price_per_quota', $data['price_per_quota'], now()->addYears(1));

        Notification::make()
            ->title('保存成功')
            ->success()
            ->send();
    }
}
