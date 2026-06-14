<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑播放器 - {{ $player->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .form-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            padding: 32px;
        }
        .form-section {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
        }
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: #6366f1;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
        }
        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #6366f1;
        }
        .color-picker {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .color-option:hover,
        .color-option.active {
            border-color: #1e293b;
            transform: scale(1.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        /* 移动端适配 */
        @media (max-width: 768px) {
            .form-card { padding: 20px 16px; border-radius: 12px; }
            .section-title { font-size: 16px; }
            .form-input, .form-select, textarea { padding: 12px; font-size: 16px; }
            .checkbox-group { grid-template-columns: 1fr; }
            .color-option { width: 36px; height: 36px; }
            .btn-primary, .btn-outline { width: 100%; justify-content: center; padding: 12px; }
            nav .flex { flex-wrap: wrap; }
            nav h1 { font-size: 16px; }
            .form-section { margin-bottom: 20px; }
            .player-actions { flex-direction: column; }
            .player-actions .btn { width: 100%; }
        }
        @media (max-width: 480px) {
            .form-card { padding: 16px 12px; }
            .form-input, .form-select, textarea { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="min-h-screen">
        <!-- 顶部导航 -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-16 gap-4">
                    <a href="{{ route('user.player.show', $player->id) }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">编辑播放器</h1>
                </div>
            </div>
        </nav>

        <!-- 主要内容 -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.player.update', $player->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- 基本信息 -->
                <div class="form-card mb-6">
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i>
                            基本信息
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">播放器名称 *</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $player->name) }}" required>
                        </div>
                    </div>

                    <!-- 外观配置 -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-palette"></i>
                            外观配置
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">主题色</label>
                            <div class="color-picker">
                                <div class="color-option {{ $player->theme_color == '#6366f1' ? 'active' : '' }}" style="background: #6366f1" data-color="#6366f1"></div>
                                <div class="color-option {{ $player->theme_color == '#8b5cf6' ? 'active' : '' }}" style="background: #8b5cf6" data-color="#8b5cf6"></div>
                                <div class="color-option {{ $player->theme_color == '#ec4899' ? 'active' : '' }}" style="background: #ec4899" data-color="#ec4899"></div>
                                <div class="color-option {{ $player->theme_color == '#f43f5e' ? 'active' : '' }}" style="background: #f43f5e" data-color="#f43f5e"></div>
                                <div class="color-option {{ $player->theme_color == '#f97316' ? 'active' : '' }}" style="background: #f97316" data-color="#f97316"></div>
                                <div class="color-option {{ $player->theme_color == '#eab308' ? 'active' : '' }}" style="background: #eab308" data-color="#eab308"></div>
                                <div class="color-option {{ $player->theme_color == '#22c55e' ? 'active' : '' }}" style="background: #22c55e" data-color="#22c55e"></div>
                                <div class="color-option {{ $player->theme_color == '#06b6d4' ? 'active' : '' }}" style="background: #06b6d4" data-color="#06b6d4"></div>
                                <div class="color-option {{ $player->theme_color == '#3b82f6' ? 'active' : '' }}" style="background: #3b82f6" data-color="#3b82f6"></div>
                                <div class="color-option {{ $player->theme_color == '#1e293b' ? 'active' : '' }}" style="background: #1e293b" data-color="#1e293b"></div>
                            </div>
                            <input type="hidden" name="theme_color" id="theme_color" value="{{ $player->theme_color }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Logo URL</label>
                                <input type="text" name="logo_url" class="form-input" value="{{ old('logo_url', $player->logo_url) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">进度条图标</label>
                                <input type="text" name="progress_icon_url" class="form-input" value="{{ old('progress_icon_url', $player->progress_icon_url) }}" placeholder="跟随进度条移动的小图标URL">
                                <div style="font-size:11px;color:#999;margin-top:4px;">类似爱奇艺角色效果，建议32x32透明PNG，留空不显示</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">水印文字</label>
                                <input type="text" name="watermark_text" class="form-input" value="{{ old('watermark_text', $player->watermark_text) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">水印位置</label>
                            <select name="watermark_position" class="form-select">
                                <option value="top-left" {{ $player->watermark_position == 'top-left' ? 'selected' : '' }}>左上角</option>
                                <option value="top-right" {{ $player->watermark_position == 'top-right' ? 'selected' : '' }}>右上角</option>
                                <option value="bottom-left" {{ $player->watermark_position == 'bottom-left' ? 'selected' : '' }}>左下角</option>
                                <option value="bottom-right" {{ $player->watermark_position == 'bottom-right' ? 'selected' : '' }}>右下角</option>
                                <option value="center" {{ $player->watermark_position == 'center' ? 'selected' : '' }}>居中</option>
                            </select>
                        </div>
                    </div>

                    <!-- 功能配置 -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-sliders-h"></i>
                            功能配置
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">播放设置</label>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoplay" value="1" {{ $player->autoplay ? 'checked' : '' }}>
                                    <span>自动播放</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="loop_play" value="1" {{ $player->loop_play ? 'checked' : '' }}>
                                    <span>循环播放</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="muted" value="1" {{ $player->muted ? 'checked' : '' }}>
                                    <span>默认静音</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">显示控制</label>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_title" value="1" {{ $player->show_title ? 'checked' : '' }}>
                                    <span>视频标题</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_controls" value="1" {{ $player->show_controls ? 'checked' : '' }}>
                                    <span>控制栏</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_quality" value="1" {{ $player->show_quality ? 'checked' : '' }}>
                                    <span>画质切换</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_speed" value="1" {{ $player->show_speed ? 'checked' : '' }}>
                                    <span>倍速播放</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_fullscreen" value="1" {{ $player->show_fullscreen ? 'checked' : '' }}>
                                    <span>全屏按钮</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_pip" value="1" {{ $player->show_pip ? 'checked' : '' }}>
                                    <span>画中画</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_download" value="1" {{ $player->show_download ? 'checked' : '' }}>
                                    <span>下载按钮</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_share" value="1" {{ $player->show_share ? 'checked' : '' }}>
                                    <span>分享按钮</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">弹幕设置</label>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="show_danmaku" value="1" {{ $player->show_danmaku ? 'checked' : '' }}>
                                    <span>显示弹幕</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="allow_danmaku" value="1" {{ $player->allow_danmaku ? 'checked' : '' }}>
                                    <span>允许发弹幕</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 尺寸配置 -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-expand"></i>
                            尺寸配置
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="form-group">
                                <label class="form-label">宽度</label>
                                <input type="text" name="width" class="form-input" value="{{ old('width', $player->width) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">高度</label>
                                <input type="text" name="height" class="form-input" value="{{ old('height', $player->height) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">宽高比</label>
                                <select name="aspect_ratio" class="form-select">
                                    <option value="16:9" {{ $player->aspect_ratio == '16:9' ? 'selected' : '' }}>16:9</option>
                                    <option value="4:3" {{ $player->aspect_ratio == '4:3' ? 'selected' : '' }}>4:3</option>
                                    <option value="1:1" {{ $player->aspect_ratio == '1:1' ? 'selected' : '' }}>1:1</option>
                                    <option value="21:9" {{ $player->aspect_ratio == '21:9' ? 'selected' : '' }}>21:9</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">圆角</label>
                                <input type="text" name="border_radius" class="form-input" value="{{ old('border_radius', $player->border_radius) }}">
                            </div>
                        </div>
                    </div>

                    <!-- 广告配置 -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-ad"></i>
                            广告配置
                        </div>
                        
                        @if($player->has_ad_module)
                        <div class="form-group">
                            <label class="checkbox-item">
                                <input type="checkbox" name="show_ads" value="1" {{ $player->show_ads ? 'checked' : '' }}>
                                <span>显示广告</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">广告装饰方案</label>
                            <select name="ad_decoration_id" class="form-select">
                                <option value="">默认</option>
                                @foreach($decorations as $deco)
                                <option value="{{ $deco->id }}" {{ $player->ad_decoration_id == $deco->id ? 'selected' : '' }}>{{ $deco->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="form-group">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                <div class="text-gray-500 mb-2">
                                    <i class="fas fa-lock text-xl"></i>
                                </div>
                                <p class="text-gray-600 text-sm mb-2">广告投放功能需要先开通广告模块</p>
                                <a href="{{ route('user.player.show', $player->id) }}" class="text-blue-500 text-sm hover:underline">前往播放器详情页购买广告模块 →</a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- 提交按钮 -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('user.player.show', $player->id) }}" class="btn-secondary">取消</a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>保存修改
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 颜色选择器
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('theme_color').value = this.dataset.color;
            });
        });
    </script>
</body>
</html>
