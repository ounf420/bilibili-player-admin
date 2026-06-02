<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                保存配置
            </x-filament::button>
        </div>
    </form>
    
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h3 class="text-lg font-medium mb-2">使用说明</h3>
        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <li>• 登录 <a href="https://open.alipay.com" target="_blank" class="text-blue-500">支付宝开放平台</a> 创建网页/移动应用</li>
            <li>• 开通「当面付」功能，获取 APPID</li>
            <li>• 在「密钥管理」生成 RSA2 密钥对，上传应用公钥，获取支付宝公钥</li>
            <li>• 首次配置建议开启「沙箱模式」测试</li>
            <li>• 测试通过后关闭沙箱，使用正式环境</li>
        </ul>
    </div>
</x-filament-panels::page>
