<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的播放器 - 用户中心</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .player-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .player-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.2);
        }
        .player-preview {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .player-preview i {
            font-size: 48px;
            color: rgba(255,255,255,0.8);
        }
        .player-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }
        .btn-outline {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-outline:hover {
            background: #6366f1;
            color: white;
        }
        .stat-item {
            text-align: center;
            padding: 16px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #6366f1;
        }
        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }
        /* 移动端适配 */
        @media (max-width: 768px) {
            .player-card { border-radius: 12px; }
            .player-preview { height: 140px; }
            .player-badge { font-size: 11px; padding: 3px 8px; }
            .btn-primary, .btn-outline { width: 100%; text-align: center; justify-content: center; padding: 10px 16px; }
            .stat-item { padding: 12px; }
            .stat-value { font-size: 20px; }
            nav .flex { flex-wrap: wrap; gap: 8px; }
            nav h1 { font-size: 16px; }
            .py-8 { padding-top: 16px; padding-bottom: 16px; }
            .mb-8 { margin-bottom: 16px; }
            .player-actions { flex-direction: column; gap: 8px; }
            .player-actions .btn { width: 100%; text-align: center; justify-content: center; }
        }
        @media (max-width: 480px) {
            .stat-value { font-size: 18px; }
            .btn-primary { padding: 10px 12px; font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- 顶部导航 -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user.index') }}" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">我的播放器</h1>
                    </div>
                    <a href="{{ route('user.player.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>创建播放器
                    </a>
                </div>
            </div>
        </nav>

        <!-- 主要内容 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($players->isEmpty())
                <!-- 空状态 -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-play-circle text-4xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">还没有播放器</h3>
                    <p class="text-gray-500 mb-6">创建你的专属播放器，自定义外观和功能</p>
                    <a href="{{ route('user.player.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>立即创建
                    </a>
                </div>
            @else>
                <!-- 统计卡片 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="stat-item">
                            <div class="stat-value">{{ $players->total() }}</div>
                            <div class="stat-label">播放器总数</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="stat-item">
                            <div class="stat-value">{{ $players->sum('video_count') }}</div>
                            <div class="stat-label">视频总数</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <div class="stat-item">
                            <div class="stat-value">{{ number_format($players->sum('view_count')) }}</div>
                            <div class="stat-label">总播放量</div>
                        </div>
                    </div>
                </div>

                <!-- 播放器列表 -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($players as $player)
                    <div class="player-card">
                        <div class="player-preview" style="background: linear-gradient(135deg, {{ $player->theme_color }}, {{ $player->theme_color }}88)">
                            <i class="fas fa-play-circle"></i>
                            <div class="player-badge">
                                {{ $player->is_active ? '已启用' : '已禁用' }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2">{{ $player->name }}</h3>
                            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                <span><i class="fas fa-video mr-1"></i> {{ $player->video_count }} 个视频</span>
                                <span><i class="fas fa-eye mr-1"></i> {{ number_format($player->view_count) }} 次播放</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('user.player.show', $player->id) }}" class="btn-outline flex-1 text-center text-sm">
                                    管理
                                </a>
                                <button onclick="copyEmbed('{{ $player->embed_code }}')" class="btn-primary flex-1 text-sm">
                                    复制代码
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- 分页 -->
                <div class="mt-8">
                    {{ $players->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyEmbed(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('嵌入代码已复制到剪贴板！');
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('嵌入代码已复制！');
            });
        }
    </script>
</body>
</html>
