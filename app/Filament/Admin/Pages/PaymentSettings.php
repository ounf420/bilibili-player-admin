<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class PaymentSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCreditCard;
    protected static ?string $navigationLabel = '支付配置';
    protected static string | \UnitEnum | null $navigationGroup = 'VIP管理';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.admin.pages.payment-settings';
    protected static ?string $title = '支付配置';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'alipay_app_id' => $this->getSetting('alipay_app_id'),
            'alipay_private_key' => $this->getSetting('alipay_private_key'),
            'alipay_public_key' => $this->getSetting('alipay_public_key'),
            'alipay_sandbox' => (bool) $this->getSetting('alipay_sandbox', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('支付宝当面付配置')
                    ->description('配置支付宝商户信息，用于扫码支付')
                    ->schema([
                        Forms\Components\TextInput::make('alipay_app_id')
                            ->label('应用ID (APPID)')
                            ->placeholder('2021000000000000')
                            ->required(),
                        Forms\Components\Textarea::make('alipay_private_key')
                            ->label('应用私钥')
                            ->placeholder('MIIEvQIBADANBgkqh...')
                            ->rows(4)
                            ->required(),
                        Forms\Components\Textarea::make('alipay_public_key')
                            ->label('支付宝公钥')
                            ->placeholder('MIIBIjANBgkqh...')
                            ->rows(4)
                            ->required(),
                        Forms\Components\Toggle::make('alipay_sandbox')
                            ->label('沙箱模式')
                            ->helperText('开启后使用支付宝沙箱环境测试')
                            ->default(false),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormData(): array
    {
        return $this->form->getState();
    }

    public function save(): void
    {
        $data = $this->getFormData();
        
        foreach ($data as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['setting_key' => $key],
                [
                    'setting_value' => json_encode($value),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->notify('success', '支付配置已保存');
    }

    private function getSetting(string $key, $default = null)
    {
        $row = DB::table('settings')->where('setting_key', $key)->first();
        if ($row) {
            $value = json_decode($row->setting_value, true);
            return $value ?? $default;
        }
        return $default;
    }

    public static function getAlipayConfig(): array
    {
        $settings = DB::table('settings')
            ->whereIn('setting_key', ['alipay_app_id', 'alipay_private_key', 'alipay_public_key', 'alipay_sandbox'])
            ->pluck('setting_value', 'setting_key')
            ->map(fn($v) => json_decode($v, true))
            ->toArray();

        return [
            'app_id' => $settings['alipay_app_id'] ?? '',
            'private_key' => $settings['alipay_private_key'] ?? '',
            'public_key' => $settings['alipay_public_key'] ?? '',
            'sandbox' => ($settings['alipay_sandbox'] ?? false) === true,
        ];
    }
}
