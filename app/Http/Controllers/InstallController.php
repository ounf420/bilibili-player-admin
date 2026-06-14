<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    protected $installLock;

    public function __construct()
    {
        $this->installLock = storage_path('installed.lock');
    }

    /**
     * 检查是否已安装
     */
    public function check()
    {
        if (File::exists($this->installLock)) {
            return redirect('/');
        }
        return redirect('/install');
    }

    /**
     * 安装首页 - 环境检测
     */
    public function index()
    {
        if (File::exists($this->installLock)) {
            return redirect('/')->with('error', '系统已安装');
        }

        $checks = $this->checkEnvironment();
        $allPassed = collect($checks)->every(fn($c) => $c['passed']);

        return view('install.index', compact('checks', 'allPassed'));
    }

    /**
     * 数据库配置页面
     */
    public function database()
    {
        if (File::exists($this->installLock)) {
            return redirect('/');
        }

        return view('install.database');
    }

    /**
     * 测试数据库连接
     */
    public function testDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'port' => 'required|integer',
            'database' => 'required',
            'username' => 'required',
        ]);

        try {
            config([
                'database.connections.install' => [
                    'driver' => 'mysql',
                    'host' => $request->host,
                    'port' => $request->port,
                    'database' => $request->database,
                    'username' => $request->username,
                    'password' => $request->password ?? '',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ]
            ]);

            DB::connection('install')->getPdo();
            return response()->json(['success' => true, 'message' => '数据库连接成功！']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '连接失败：' . $e->getMessage()]);
        }
    }

    /**
     * 管理员账号页面
     */
    public function admin()
    {
        if (File::exists($this->installLock)) {
            return redirect('/');
        }

        return view('install.admin');
    }

    /**
     * 执行安装
     */
    public function install(Request $request)
    {
        if (File::exists($this->installLock)) {
            return redirect('/');
        }

        $request->validate([
            'host' => 'required',
            'port' => 'required|integer',
            'database' => 'required',
            'db_username' => 'required',
            'admin_name' => 'required|string|max:50',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:6',
            'site_name' => 'required|string|max:100',
        ]);

        try {
            // 1. 写入 .env
            $this->writeEnv($request);

            // 2. 清除配置缓存
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            // 3. 连接数据库
            DB::purge('mysql');
            DB::reconnect('mysql');

            // 4. 运行迁移
            Artisan::call('migrate', ['--force' => true]);

            // 5. 创建管理员
            $this->createAdmin($request);

            // 6. 初始化设置
            $this->initSettings($request->site_name);

            // 7. 写入安装锁
            File::put($this->installLock, json_encode([
                'installed_at' => now()->toDateTimeString(),
                'version' => '1.0.0',
                'admin_email' => $request->admin_email,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => '安装成功！',
                'redirect' => '/login'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '安装失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 安装完成页面
     */
    public function complete()
    {
        return view('install.complete');
    }

    /**
     * 环境检测
     */
    protected function checkEnvironment()
    {
        $checks = [];

        // PHP版本
        $checks[] = [
            'name' => 'PHP版本',
            'required' => '>= 8.1',
            'current' => PHP_VERSION,
            'passed' => version_compare(PHP_VERSION, '8.1.0', '>=')
        ];

        // 必要扩展
        $extensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl'];
        foreach ($extensions as $ext) {
            $checks[] = [
                'name' => "PHP扩展: {$ext}",
                'required' => '已安装',
                'current' => extension_loaded($ext) ? '已安装' : '未安装',
                'passed' => extension_loaded($ext)
            ];
        }

        // 目录权限
        $directories = [
            'storage' => storage_path(),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($directories as $name => $path) {
            $writable = is_writable($path);
            $checks[] = [
                'name' => "目录权限: {$name}",
                'required' => '可写',
                'current' => $writable ? '可写' : '不可写',
                'passed' => $writable
            ];
        }

        return $checks;
    }

    /**
     * 写入.env文件
     */
    protected function writeEnv(Request $request)
    {
        $envPath = base_path('.env');
        $password = addslashes($request->db_password ?? '');
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        $envContent = "APP_NAME=\"{$request->site_name}\"\n";
        $envContent .= "APP_ENV=production\n";
        $envContent .= "APP_KEY={$appKey}\n";
        $envContent .= "APP_DEBUG=false\n";
        $envContent .= "APP_URL=http://{$_SERVER['HTTP_HOST']}\n\n";
        $envContent .= "LOG_CHANNEL=stack\n";
        $envContent .= "LOG_LEVEL=warning\n\n";
        $envContent .= "DB_CONNECTION=mysql\n";
        $envContent .= "DB_HOST={$request->host}\n";
        $envContent .= "DB_PORT={$request->port}\n";
        $envContent .= "DB_DATABASE={$request->database}\n";
        $envContent .= "DB_USERNAME={$request->db_username}\n";
        $envContent .= "DB_PASSWORD=\"{$password}\"\n\n";
        $envContent .= "SESSION_DRIVER=file\n";
        $envContent .= "SESSION_LIFETIME=120\n\n";
        $envContent .= "BROADCAST_DRIVER=log\n";
        $envContent .= "CACHE_DRIVER=file\n";
        $envContent .= "QUEUE_DRIVER=sync\n";

        File::put($envPath, $envContent);
    }

    /**
     * 创建管理员
     */
    protected function createAdmin(Request $request)
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // 检查是否已有管理员
        $exists = DB::table('users')->where('role', 'admin')->exists();
        if ($exists) {
            return;
        }

        DB::table('users')->insert([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 初始化系统设置
     */
    protected function initSettings($siteName)
    {
        if (!Schema::hasTable('player_settings')) {
            return;
        }

        $defaultSettings = [
            'player_watermark_show' => '0',
            'player_watermark_text' => '',
            'player_watermark_position' => 'top-right',
            'player_watermark_type' => 'text',
            'preroll_duration' => '5',
            'midroll_duration' => '5',
            'postroll_duration' => '5',
            'splash_duration' => '5',
        ];

        foreach ($defaultSettings as $key => $value) {
            DB::table('player_settings')->insertOrIgnore([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
