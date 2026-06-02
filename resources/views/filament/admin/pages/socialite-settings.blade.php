<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-2" />
                保存配置
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
            <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 inline mr-1" />
            使用说明
        </h3>
        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
            <li>1. 访问 <a href="http://login.cxavn.cn" target="_blank" class="underline">login.cxavn.cn</a> 注册账号</li>
            <li>2. 在用户中心获取 APPID 和 APPKEY</li>
            <li>3. 填入上方对应字段并保存</li>
            <li>4. 前台登录页和账号中心会自动显示已启用的平台</li>
        </ul>
    </div>
</x-filament-panels::page>
