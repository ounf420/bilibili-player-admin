<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->name }} - 播放器管理</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            padding: 24px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-outline:hover {
            background: #6366f1;
            color: white;
        }
        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
            border: 2px solid #fecaca;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-danger:hover {
            background: #dc2626;
            color: white;
        }
        .video-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .video-item:hover {
            background: #f1f5f9;
        }
        .video-thumb {
            width: 120px;
            height: 68px;
            border-radius: 8px;
            object-fit: cover;
            background: #e2e8f0;
        }
        .embed-code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            position: relative;
        }
        .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        .copy-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        .preview-frame {
            width: 100%;
            aspect-ratio: {{ $player->aspect_ratio }};
            border-radius: {{ $player->border_radius }};
            overflow: hidden;
            background: #000;
        }
        /* 移动端适配 */
        @media (max-width: 768px) {
            .form-card { padding: 20px 16px; border-radius: 12px; }
            .section-title { font-size: 16px; }
            .form-input, textarea { padding: 12px; font-size: 16px; }
            .btn-primary, .btn-outline { width: 100%; justify-content: center; padding: 12px; }
            nav .flex { flex-wrap: wrap; gap: 8px; }
            nav h1 { font-size: 16px; }
            .copy-btn { padding: 8px; font-size: 11px; }
            .embed-code textarea { font-size: 11px; padding: 10px; }
            .py-8 { padding-top: 16px; padding-bottom: 16px; }
            .mb-8 { margin-bottom: 16px; }
            .player-actions { flex-direction: column; gap: 8px; }
            .player-actions .btn { width: 100%; text-align: center; justify-content: center; }
        }
        @media (max-width: 480px) {
            .form-card { padding: 16px 12px; }
            .form-input, textarea { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- 顶部导航 -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user.player.index') }}" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">{{ $player->name }}</h1>
                        <span class="px-3 py-1 text-sm rounded-full {{ $player->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $player->is_active ? '已启用' : '已禁用' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('user.player.edit', $player->id) }}" class="btn-outline">
                            <i class="fas fa-edit"></i> 编辑
                        </a>
                        <form action="{{ route('user.player.destroy', $player->id) }}" method="POST" onsubmit="return confirm('确定删除此播放器？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">
                                <i class="fas fa-trash"></i> 删除
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 主要内容 -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 左侧：播放器信息 -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- 统计卡片 -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="card text-center">
                            <div class="text-3xl font-bold" style="color: {{ $player->theme_color }}">{{ $player->video_count }}</div>
                            <div class="text-sm text-gray-500 mt-1">视频数量</div>
                        </div>
                        <div class="card text-center">
                            <div class="text-3xl font-bold" style="color: {{ $player->theme_color }}">{{ number_format($player->view_count) }}</div>
                            <div class="text-sm text-gray-500 mt-1">播放次数</div>
                        </div>
                        <div class="card text-center">
                            <div class="text-3xl font-bold" style="color: {{ $player->theme_color }}">{{ $player->decoration ? $player->decoration->name : '默认' }}</div>
                            <div class="text-sm text-gray-500 mt-1">广告方案</div>
                        </div>
                    </div>

                    <!-- 视频列表 -->
                    <div class="card">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">视频列表</h2>
                            <button onclick="document.getElementById('addVideoModal').classList.remove('hidden')" class="btn-primary text-sm">
                                <i class="fas fa-plus"></i> 添加视频
                            </button>
                        </div>

                        @if($player->videos->isEmpty())
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-video text-4xl mb-3"></i>
                                <p>还没有添加视频</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($player->videos as $video)
                                <div class="video-item">
                                    <img src="{{ $video->cover_url ?? '/images/default-cover.jpg' }}" class="video-thumb" alt="{{ $video->title }}">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $video->title }}</h4>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <span><i class="fas fa-eye mr-1"></i>{{ number_format($video->views) }}</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('user.player.video.remove', $player->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- 嵌入代码 -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4">嵌入代码</h2>
                        <div class="embed-code">
                            <button class="copy-btn" onclick="copyCode()">
                                <i class="fas fa-copy mr-1"></i> 复制
                            </button>
                            <code id="embedCode">&lt;iframe src="{{ $player->embed_url }}" width="100%" height="auto" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</code>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            将此代码粘贴到您的网页中即可嵌入播放器
                        </p>
                    </div>
                </div>

                <!-- 右侧：配置预览 -->
                <div class="space-y-6">
                    <!-- 预览 -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4">预览</h2>
                        <div class="preview-frame" style="background: linear-gradient(135deg, {{ $player->theme_color }}, {{ $player->theme_color }}88)">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-play-circle text-6xl text-white opacity-80"></i>
                            </div>
                        </div>
                    </div>

                    <!-- 配置信息 -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4">配置信息</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">主题色</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded" style="background: {{ $player->theme_color }}"></div>
                                    <span>{{ $player->theme_color }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">宽高比</span>
                                <span>{{ $player->aspect_ratio }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">圆角</span>
                                <span>{{ $player->border_radius }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">自动播放</span>
                                <span>{{ $player->autoplay ? '是' : '否' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">显示弹幕</span>
                                <span>{{ $player->show_danmaku ? '是' : '否' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">显示广告</span>
                                <span>{{ $player->show_ads ? '是' : '否' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">水印</span>
                                <span>{{ $player->watermark_text ?? '无' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 访问链接 -->
                    <div class="card">
                        <h2 class="text-lg font-semibold mb-4">访问链接</h2>
                        <div class="bg-gray-50 rounded-lg p-3 break-all text-sm text-gray-600">
                            {{ $player->embed_url }}?id={{ $player->player_code }}&key={{ $player->player_key }}
                        </div>
                        <a href="{{ $player->embed_url }}?id={{ $player->player_code }}&key={{ $player->player_key }}" target="_blank" class="btn-outline w-full justify-center mt-3 text-sm">
                            <i class="fas fa-external-link-alt"></i> 在新窗口打开
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 添加视频弹窗 -->
    <div id="addVideoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">添加视频</h3>
                <button onclick="document.getElementById('addVideoModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if($availableVideos->isEmpty())
                <p class="text-gray-500 text-center py-8">没有可添加的视频</p>
            @else
                <div class="space-y-3">
                    @foreach($availableVideos as $video)
                    <form action="{{ route('user.player.video.add', $player->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="video_id" value="{{ $video->id }}">
                        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg">
                            <img src="{{ $video->cover_url ?? '/images/default-cover.jpg' }}" class="w-16 h-9 object-cover rounded" alt="">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $video->title }}</p>
                            </div>
                            <button type="submit" class="btn-primary text-sm py-1 px-3">
                                添加
                            </button>
                        </div>
                    </form>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('embedCode').textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('代码已复制！');
            }).catch(() => {
                const textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('代码已复制！');
            });
        }
    </script>
</body>
</html>
