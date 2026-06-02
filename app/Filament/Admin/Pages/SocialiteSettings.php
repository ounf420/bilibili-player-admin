<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class SocialiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedLink;

    protected string $view = 'filament.admin.pages.socialite-settings';

    protected static ?string $navigationLabel = '第三方登录';

    protected static ?string $title = '第三方登录配置';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'api_url' => config('services.socialite.api_url', 'https://login.cxavn.cn'),
            'appid' => config('services.socialite.appid'),
            'appkey' => config('services.socialite.appkey'),
            'callback' => config('services.socialite.callback'),
            'enabled_qq' => config('services.socialite.platforms.qq', true),
            'enabled_wx' => config('services.socialite.platforms.wx', true),
            'enabled_alipay' => config('services.socialite.platforms.alipay', true),
            'enabled_sina' => config('services.socialite.platforms.sina', true),
            'enabled_baidu' => config('services.socialite.platforms.baidu', false),
            'enabled_douyin' => config('services.socialite.platforms.douyin', true),
            'enabled_huawei' => config('services.socialite.platforms.huawei', false),
            'enabled_xiaomi' => config('services.socialite.platforms.xiaomi', false),
            'enabled_microsoft' => config('services.socialite.platforms.microsoft', false),
            'enabled_feishu' => config('services.socialite.platforms.feishu', false),
            'enabled_dingtalk' => config('services.socialite.platforms.dingtalk', false),
            'enabled_gitee' => config('services.socialite.platforms.gitee', false),
            'enabled_github' => config('services.socialite.platforms.github', true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('🔑 聚合登录配置')
                    ->description('彩虹聚合登录平台的接口配置')
                    ->schema([
                        TextInput::make('api_url')
                            ->label('API地址')
                            ->required()
                            ->default('https://login.cxavn.cn')
                            ->placeholder('https://login.cxavn.cn')
                            ->helperText('聚合登录平台的域名，不含connect.php'),

                        TextInput::make('appid')
                            ->label('APPID')
                            ->required()
                            ->placeholder('在聚合登录平台获取'),

                        TextInput::make('appkey')
                            ->label('APPKEY')
                            ->required()
                            ->password()
                            ->revealable()
                            ->placeholder('在聚合登录平台获取'),

                        TextInput::make('callback')
                            ->label('回调地址')
                            ->required()
                            ->default('https://dem.viesta.cn/api/socialite/callback')
                            ->helperText('第三方登录成功后跳转回来的地址'),
                    ]),

                Section::make('📱 启用平台')
                    ->description('选择在前台显示哪些第三方登录平台')
                    ->schema([
                        Toggle::make('enabled_qq')->label('QQ登录')->default(true)->inline(false),
                        Toggle::make('enabled_wx')->label('微信登录')->default(true)->inline(false),
                        Toggle::make('enabled_alipay')->label('支付宝登录')->default(true)->inline(false),
                        Toggle::make('enabled_sina')->label('微博登录')->default(true)->inline(false),
                        Toggle::make('enabled_douyin')->label('抖音登录')->default(true)->inline(false),
                        Toggle::make('enabled_github')->label('GitHub登录')->default(true)->inline(false),
                        Toggle::make('enabled_baidu')->label('百度登录')->default(false)->inline(false),
                        Toggle::make('enabled_huawei')->label('华为登录')->default(false)->inline(false),
                        Toggle::make('enabled_xiaomi')->label('小米登录')->default(false)->inline(false),
                        Toggle::make('enabled_microsoft')->label('微软登录')->default(false)->inline(false),
                        Toggle::make('enabled_feishu')->label('飞书登录')->default(false)->inline(false),
                        Toggle::make('enabled_dingtalk')->label('钉钉登录')->default(false)->inline(false),
                        Toggle::make('enabled_gitee')->label('Gitee登录')->default(false)->inline(false),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // 写入 .env 文件
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $envContent = $this->updateEnvValue($envContent, 'SOCIALITE_API_URL', $data['api_url']);
        $envContent = $this->updateEnvValue($envContent, 'SOCIALITE_APPID', $data['appid']);
        $envContent = $this->updateEnvValue($envContent, 'SOCIALITE_APPKEY', $data['appkey']);
        $envContent = $this->updateEnvValue($envContent, 'SOCIALITE_CALLBACK', $data['callback']);

        file_put_contents($envPath, $envContent);

        // 清除配置缓存
        Artisan::call('config:clear');

        Notification::make()
            ->title('第三方登录配置已保存！')
            ->success()
            ->send();
    }

    private function updateEnvValue(string $envContent, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $replacement, $envContent);
        }

        // 如果不存在，添加到文件末尾
        return rtrim($envContent) . "\n{$replacement}\n";
    }
}
