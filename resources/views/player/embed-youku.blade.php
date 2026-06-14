<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->name ?? '优酷风格播放器' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.7/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; background: #000; overflow: hidden; }

        #dplayer { 
            width: 100%; 
            height: 100%; 
        }

        /* 背景媒体（图片或视频） */
        .bg-media {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            object-fit: cover;
            pointer-events: none;
        }
        
        .bg-media img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        
        .bg-media video {
            width: 100%; height: 100%;
            object-fit: cover;
        }

        /* 隐藏DPlayer默认控制栏 */
        .dplayer-controller { display: none !important; }
        .dplayer-controller-mask { display: none !important; }

        /* ========== 优酷风格控制栏 ========== */
        .yk-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 20;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
            padding: 40px 15px 12px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .dplayer:hover .yk-controls,
        .dplayer.paused .yk-controls {
            opacity: 1;
        }

        /* 时间行（时间+进度条） */
        .yk-time-row {
            display: flex;
            align-items: center;
            padding: 0 12px 4px;
            gap: 10px;
        }

        .yk-time {
            font-size: 12px;
            color: #fff;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ========== 进度条（优酷风格） ========== */
        .yk-progress {
            position: relative;
            flex: 1;
            height: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .yk-progress-track {
            position: relative;
            width: 100%;
            height: 3px;
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
            transition: height 0.15s;
        }

        .yk-progress:hover .yk-progress-track {
            height: 5px;
        }

        .yk-progress-buffer {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

        .yk-progress-played {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #ff6b35;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        /* 进度条圆点（优酷风格：hover时才显示） */
        .yk-progress-dot {
            width: 14px;
            height: 14px;
            background: #ff6b35;
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.15s;
            box-shadow: 0 0 8px rgba(255,107,53,0.6);
            margin-right: -7px;
        }

        .yk-progress:hover .yk-progress-dot {
            transform: scale(1);
        }

        /* 进度条预览时间 */
        .yk-progress-tooltip {
            position: absolute;
            bottom: 100%;
            left: 0;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.9);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.1s;
            margin-bottom: 10px;
        }

        .yk-progress:hover .yk-progress-tooltip {
            opacity: 1;
        }

        /* ========== 控制按钮行（优酷风格） ========== */
        .yk-bar {
            display: flex;
            align-items: center;
            height: 40px;
        }

        .yk-btn {
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s;
            padding: 0;
            flex-shrink: 0;
        }

        .yk-btn:hover {
            background: rgba(255,255,255,0.15);
        }

        .yk-btn svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }

        .yk-btn-play svg { width: 30px; height: 30px; }

        /* 时间 */
        .yk-time {
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            margin: 0 10px;
            font-variant-numeric: tabular-nums;
            user-select: none;
            white-space: nowrap;
        }

        /* 音量 */
        .yk-volume {
            display: flex;
            align-items: center;
            position: relative;
        }

        .yk-volume-bar {
            width: 0;
            height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 2px;
            overflow: hidden;
            transition: width 0.2s;
            cursor: pointer;
            margin-left: 6px;
        }

        .yk-volume:hover .yk-volume-bar {
            width: 70px;
        }

        .yk-volume-fill {
            height: 100%;
            background: #fff;
            border-radius: 2px;
        }

        /* 右侧按钮组 */
        .yk-right {
            margin-left: auto;
            display: flex;
            align-items: center;
        }

        /* 倍速文字 */
        .yk-speed-text {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        /* ========== 迷你进度条（顶部） ========== */
        .yk-mini-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            z-index: 25;
            background: transparent;
        }

        .yk-mini-bar-inner {
            height: 100%;
            background: #ff6b35;
            transition: width 0.1s linear;
        }

        .dplayer:hover .yk-mini-bar { opacity: 0; }

        /* ========== 倍速菜单 ========== */
        .yk-speed-menu {
            position: absolute;
            bottom: 60px;
            right: 15px;
            background: rgba(0,0,0,0.95);
            border-radius: 8px;
            padding: 8px 0;
            z-index: 30;
            opacity: 0;
            transform: translateY(5px);
            transition: all 0.2s;
            pointer-events: none;
            min-width: 80px;
        }

        .yk-speed-menu.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .yk-speed-item {
            padding: 10px 20px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .yk-speed-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .yk-speed-item.active {
            color: #ff6b35;
            font-weight: 600;
        }

        /* ========== 选集面板（优酷风格） ========== */
        .yk-episode-panel {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 320px;
            max-width: 80vw;
            background: rgba(0,0,0,0.95);
            z-index: 35;
            transform: translateX(100%);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }

        .yk-episode-panel.show {
            transform: translateX(0);
        }

        .yk-episode-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .yk-episode-head-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .yk-episode-close {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .yk-episode-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .yk-ep-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 36px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
            margin: 4px;
        }

        .yk-ep-item:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .yk-ep-item.active {
            background: #ff6b35;
            border-color: #ff6b35;
            color: #fff;
        }

        /* 选集触发按钮（右侧边缘） */
        .yk-ep-trigger {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 80px;
            background: rgba(0,0,0,0.6);
            border: none;
            border-radius: 6px 0 0 6px;
            color: #fff;
            cursor: pointer;
            z-index: 25;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s;
        }

        .dplayer:hover .yk-ep-trigger { opacity: 1; }
        .yk-ep-trigger:hover { background: rgba(0,0,0,0.8); }

        /* ========== Toast ========== */
        .yk-toast {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 40;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .yk-toast.show { opacity: 1; }

        /* ========== Logo ========== */
        .yk-logo {
            position: absolute;
            z-index: 15;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .dplayer:hover .yk-logo { opacity: 1; }

        .yk-logo img {
            height: {{ $player->logo_size ?? 48 }}px;
            width: auto;
            border-radius: 4px;
        }

        /* ========== 文字水印 ========== */
        .yk-watermark {
            position: absolute;
            z-index: 15;
            pointer-events: none;
            user-select: none;
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* ========== 弹幕控制（优酷风格） ========== */
        .yk-danmaku {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-right: 8px;
        }

        /* PC端：弹幕区域居中 */
        @media (min-width: 769px) {
            .yk-bar { position: relative; }
            .yk-danmaku-center {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                gap: 6px;
                z-index: 1;
            }
        }

        /* 弹幕按钮通用样式 */
        .yk-danmaku-btn {
            display: flex;
            align-items: center;
            font-size: 18px;
            color: #fff;
            font-weight: bold;
            position: relative;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: background 0.2s;
            user-select: none;
        }

        .yk-danmaku-btn:hover {
            background: rgba(255,255,255,0.1);
        }

        /* 右下角图标 */
        .yk-danmaku-btn .badge-icon {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 10px;
            height: 10px;
        }

        /* 弹幕开关关闭状态 */
        .yk-danmaku-btn.off .badge-icon {
            opacity: 0;
        }

        /* 弹幕颜色按钮 */
        .yk-danmaku-color-btn {
            width: 28px;
            height: 28px;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.2s;
            padding: 0;
            margin: 0 2px;
        }

        .yk-danmaku-color-btn:hover {
            background: rgba(255,255,255,0.15);
        }

        .yk-danmaku-color-btn svg {
            fill: #fff;
            transition: fill 0.2s;
        }

        /* 弹幕颜色面板 */
        .yk-danmaku-color-panel {
            position: absolute;
            bottom: 60px;
            right: 180px;
            background: rgba(0,0,0,0.95);
            border-radius: 8px;
            padding: 12px;
            z-index: 30;
            opacity: 0;
            transform: translateY(5px);
            transition: all 0.2s;
            pointer-events: none;
            min-width: 160px;
        }

        .yk-danmaku-color-panel.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .yk-danmaku-color-panel-title {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 8px;
        }

        .yk-danmaku-colors {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .yk-danmaku-color-item {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.15s;
        }

        .yk-danmaku-color-item:hover {
            transform: scale(1.15);
        }

        .yk-danmaku-color-item.active {
            border-color: #fff;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.5);
        }

        /* 弹幕输入框 */
        .yk-danmaku-input-wrap {
            width: 250px;
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.15);
            border-radius: 4px;
            height: 30px;
            padding: 0 8px;
            margin-left: 8px;
            flex-shrink: 0;
        }
        .yk-danmaku-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 12px;
        }
        .yk-danmaku-input::placeholder { color: rgba(255,255,255,0.5); }
        .yk-danmaku-send {
            background: #00a0ff;
            border: none;
            color: #fff;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 6px;
        }

        /* ========== 移动端 ========== */
        @media (max-width: 768px) {
            .yk-controls { padding: 25px 10px 8px; }
            .yk-btn { width: 30px; height: 30px; }
            .yk-btn-play svg { width: 26px; height: 26px; }
            .yk-time { font-size: 12px; margin: 0 6px; }
            .yk-episode-panel { width: 260px; }
            
            /* 移动端非全屏：隐藏弹幕颜色和输入框 */
            .yk-danmaku-color-btn,
            .yk-danmaku-input-wrap {
                display: none !important;
            }
            
            /* 移动端全屏（横屏）：显示完整弹幕控制 */
            .dplayer-full-screen .yk-danmaku-color-btn,
            .dplayer-full-screen .yk-danmaku-input-wrap,
            .dplayer-web-fullscreen .yk-danmaku-color-btn,
            .dplayer-web-fullscreen .yk-danmaku-input-wrap {
                display: flex !important;
            }
            
            .yk-danmaku { margin-right: 4px; }
            .yk-danmaku-toggle { padding: 2px 4px; }
            .yk-danmaku-toggle svg { width: 16px; height: 16px; margin-right: 2px; }
            .yk-danmaku-toggle .yk-danmaku-text { font-size: 10px; }
            .yk-danmaku-color-btn { width: 22px; height: 22px; }
            .yk-danmaku-color-swatch { width: 12px; height: 12px; }
            .yk-danmaku-input-wrap { margin-left: 2px; }
            .yk-danmaku-input { 
                width: 100px; 
                height: 26px; 
                font-size: 11px; 
                padding: 0 8px; 
                border-radius: 13px 0 0 13px; 
            }
            .yk-danmaku-send { 
                height: 26px; 
                padding: 0 10px; 
                font-size: 11px; 
                border-radius: 0 13px 13px 0; 
            }
            .yk-danmaku-color-panel { 
                right: 60px; 
                bottom: 50px; 
                padding: 10px; 
                min-width: 140px; 
            }
            .yk-danmaku-color-item { width: 20px; height: 20px; }
        }

        @media (max-width: 480px) {
            .yk-controls { padding: 20px 8px 6px; }
            .yk-bar { height: 36px; }
            .yk-btn { width: 28px; height: 28px; }
            .yk-btn-play svg { width: 24px; height: 24px; }
            .yk-time { font-size: 11px; margin: 0 4px; }
            
            /* 小屏幕弹幕控制 */
            .yk-danmaku-toggle .yk-danmaku-text { display: none; }
            .yk-danmaku-input { width: 80px; }
            .yk-danmaku-color-panel { right: 40px; }
        }
    </style>
</head>
<body>
    <div id="dplayer"></div>

    <script>
    (function() {
        const PLAYER_ID = '{{ $player->player_code ?? $player->id }}';
        let videoUrl = '{{ $player->videos->first()->url ?? '' }}';
        const isNoAd = {{ $player->version === 'flagship' || $player->version === 'advanced' ? 'true' : 'false' }};
        const bgUrl = '{{ $player->background_image ?? '' }}';
        const bgUrlMobile = '{{ $player->background_image_mobile ?? '' }}';
        const parseUrl = '{{ $player->parse_url ?? '' }}';
        const container = document.getElementById('dplayer');
        let dp;
        
        // 检测URL参数中是否有视频链接
        const urlParams = new URLSearchParams(window.location.search);
        const externalUrl = urlParams.get('url');
        
        // 如果有外部链接且配置了解析接口
        if (externalUrl && parseUrl) {
            // 显示加载状态
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#000;color:#fff;font-size:16px;"><i class="fas fa-spinner fa-spin" style="margin-right:10px;"></i>正在解析视频...</div>';
            
            // 调用解析接口
            fetch(parseUrl + encodeURIComponent(externalUrl))
                .then(res => res.json())
                .then(data => {
                    if (data.url) {
                        videoUrl = data.url;
                        initPlayer();
                    } else {
                        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#000;color:#ff4444;font-size:16px;">解析失败：未获取到视频地址</div>';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#000;color:#ff4444;font-size:16px;">解析失败：' + err.message + '</div>';
                });
        } else {
            // 没有外部链接，直接初始化播放器
            initPlayer();
        }
        
        function initPlayer() {
            // 检测是否为移动端
            const isMobile = window.innerWidth <= 768;
            
            // 没有视频源时的处理
            if (!videoUrl) {
                if (isNoAd && bgUrl) {
                    // 无广告版本：显示背景图/视频
                    const currentBg = (isMobile && bgUrlMobile) ? bgUrlMobile : bgUrl;
                    const isVideo = currentBg.match(/\.(mp4|webm|ogg)$/i);
                    if (isVideo) {
                        const bgVideo = document.createElement('div');
                        bgVideo.className = 'bg-media';
                        bgVideo.innerHTML = '<video src="' + currentBg + '" autoplay loop muted playsinline></video>';
                        container.appendChild(bgVideo);
                    } else {
                        const bgImg = document.createElement('div');
                        bgImg.className = 'bg-media';
                        bgImg.innerHTML = '<img src="' + currentBg + '" alt="背景">';
                        container.appendChild(bgImg);
                    }
                } else {
                    // 其他版本：播放随机广告
                    // TODO: 接入广告引擎
                    console.log('无视频源，播放广告');
                }
            }
            
            // 初始化DPlayer
            initDPlayer();
            
            // 获取DPlayer容器
            const dpContainer = dp.container;
            
            // 无视频源时：隐藏DPlayer的video元素，让背景图显示
            if (!videoUrl) {
                const dpVideoWrap = dpContainer.querySelector('.dplayer-video-wrap');
                if (dpVideoWrap) dpVideoWrap.style.display = 'none';
            }
            
            // 隐藏默认控制栏
            const defCtrl = dpContainer.querySelector('.dplayer-controller');
            const defMask = dpContainer.querySelector('.dplayer-controller-mask');
            if (defCtrl) defCtrl.style.display = 'none';
            if (defMask) defMask.style.display = 'none';
            
            // 创建控制栏
            createControls(dpContainer);
        }

        // DPlayer初始化
        function initDPlayer() {
            dp = new DPlayer({
                container: container,
                video: {
                    url: videoUrl,
                    type: videoUrl.includes('.m3u8') ? 'customHls' : 'auto',
                },
                autoplay: videoUrl ? {{ $player->autoplay ? 'true' : 'false' }} : false,
                theme: '#ff6b35',
                loop: false,
                hotkey: true,
                preload: 'auto',
                volume: 0.7,
                mutex: true,
                lang: 'zh-cn',
                customType: {
                    customHls: function(video) {
                        if (Hls.isSupported()) {
                            const hls = new Hls();
                            hls.loadSource(video.src);
                            hls.attachMedia(video);
                        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                            video.src = video.src;
                        }
                    }
                }
            });
        }

        // 创建控制栏
        function createControls(dpContainer) {
            // ========== 创建控制栏 ==========
        const controls = document.createElement('div');
        controls.className = 'yk-controls';
        controls.innerHTML = `
            <!-- 时间显示（进度条前面） -->
            <div class="yk-time-row">
                <span class="yk-time" id="ykCurTime">00:00</span>
                <!-- 进度条 -->
                <div class="yk-progress" id="ykProgress">
                    <div class="yk-progress-track">
                        <div class="yk-progress-buffer" id="ykBuffer"></div>
                        <div class="yk-progress-played" id="ykPlayed">
                            <div class="yk-progress-dot"></div>
                        </div>
                    </div>
                    <div class="yk-progress-tooltip" id="ykTooltip">00:00</div>
                </div>
                <span class="yk-time yk-countdown" id="ykCountdown">-00:00</span>
            </div>

            <!-- 控制按钮 -->
            <div class="yk-bar">
                <!-- 上一集 -->
                <button class="yk-btn" id="ykPrev" title="上一集">
                    <svg viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                </button>

                <!-- 播放（在上一集和下一集中间） -->
                <button class="yk-btn yk-btn-play" id="ykPlay">
                    <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>

                <!-- 下一集 -->
                <button class="yk-btn" id="ykNext" title="下一集">
                    <svg viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                </button>

                <!-- 音量 -->
                <div class="yk-volume">
                    <button class="yk-btn" id="ykVolBtn" title="静音">
                        <svg viewBox="0 0 24 24" id="ykVolIcon"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                    </button>
                    <div class="yk-volume-bar" id="ykVolBar">
                        <div class="yk-volume-fill" id="ykVolFill" style="width:70%"></div>
                    </div>
                </div>

                <!-- 弹幕居中区域(PC) -->
                <div class="yk-danmaku-center">
                    <!-- 弹幕控制 -->
                    <div class="yk-danmaku">
                        <!-- 弹幕开关（大号弹字+蓝色勾选） -->
                        <div class="yk-danmaku-btn" id="ykDanmakuToggle" title="弹幕开关">
                            弹
                            <svg class="badge-icon" viewBox="0 0 24 24" width="10" height="10">
                                <circle cx="12" cy="12" r="12" fill="#00a0ff"/>
                                <path d="M8 12l3 3 5-5" stroke="#fff" stroke-width="2" fill="none"/>
                            </svg>
                        </div>

                        <!-- 弹幕设置（大号弹字+齿轮） -->
                        <div class="yk-danmaku-btn" id="ykDanmakuColorBtn" title="弹幕设置">
                            弹
                            <svg class="badge-icon" viewBox="0 0 24 24" width="10" height="10">
                                <path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.39-1.08-.7-1.66-.94l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.58.24-1.14.55-1.66.94l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.39 1.08.7 1.66.94l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.58-.24 1.14-.55 1.66-.94l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z" fill="#fff"/>
                            </svg>
                        </div>
                    </div>

                    <!-- 弹幕输入框 -->
                    <div class="yk-danmaku-input-wrap">
                        <input type="text" class="yk-danmaku-input" id="ykDanmakuInput" placeholder="输入弹幕..." maxlength="50">
                        <button class="yk-danmaku-send" id="ykDanmakuSend">发送</button>
                    </div>
                </div>

                <!-- 右侧 -->
                <div class="yk-right">
                    <!-- 倍速 -->
                    <button class="yk-btn" id="ykSpeedBtn" title="倍速">
                        <span class="yk-speed-text">1x</span>
                    </button>

                    <!-- 画中画 -->
                    <button class="yk-btn" id="ykPip" title="画中画">
                        <svg viewBox="0 0 24 24"><path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/></svg>
                    </button>

                    <!-- 网页全屏 -->
                    <button class="yk-btn" id="ykWebFull" title="网页全屏">
                        <svg viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                    </button>

                    <!-- 全屏 -->
                    <button class="yk-btn" id="ykFull" title="全屏">
                        <svg viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                    </button>
                </div>
            </div>
        `;
        dpContainer.appendChild(controls);

        // ========== 全屏自动隐藏控制栏 ==========
        let hideControlsTimer = null;

        function showControlsUI() {
            const el = dpContainer.querySelector('.yk-controls');
            const ep = dpContainer.querySelector('.yk-ep-trigger');
            if (el) el.style.opacity = '1';
            if (ep) ep.style.opacity = '1';
            dpContainer.style.cursor = '';
            clearTimeout(hideControlsTimer);
        }

        function hideControlsUI() {
            const el = dpContainer.querySelector('.yk-controls');
            const ep = dpContainer.querySelector('.yk-ep-trigger');
            if (el) el.style.opacity = '0';
            if (ep) ep.style.opacity = '0';
            dpContainer.style.cursor = 'none';
        }

        function startHideTimer() {
            clearTimeout(hideControlsTimer);
            hideControlsTimer = setTimeout(() => {
                if (document.fullscreenElement && dp && !dp.video.paused) {
                    hideControlsUI();
                }
            }, 3000);
        }

        // 全屏时鼠标移动显示控制栏
        dpContainer.addEventListener('mousemove', () => {
            if (document.fullscreenElement) {
                showControlsUI();
                startHideTimer();
            }
        });

        // 全屏时鼠标离开隐藏控制栏
        dpContainer.addEventListener('mouseleave', () => {
            if (document.fullscreenElement && dp && !dp.video.paused) {
                hideControlsUI();
            }
        });

        // 监听全屏状态变化
        document.addEventListener('fullscreenchange', () => {
            if (document.fullscreenElement) {
                startHideTimer();
            } else {
                clearTimeout(hideControlsTimer);
                showControlsUI();
            }
        });

        // 播放/暂停时重置定时器
        dp.video.addEventListener('play', () => {
            if (document.fullscreenElement) startHideTimer();
        });

        dp.video.addEventListener('pause', () => {
            clearTimeout(hideControlsTimer);
            showControlsUI();
        });

        // 迷你进度条
        const miniBar = document.createElement('div');
        miniBar.className = 'yk-mini-bar';
        miniBar.innerHTML = '<div class="yk-mini-bar-inner" id="ykMiniInner"></div>';
        dpContainer.appendChild(miniBar);

        // Toast
        const toast = document.createElement('div');
        toast.className = 'yk-toast';
        toast.id = 'ykToast';
        dpContainer.appendChild(toast);

        // 倍速菜单
        const speedMenu = document.createElement('div');
        speedMenu.className = 'yk-speed-menu';
        speedMenu.id = 'ykSpeedMenu';
        speedMenu.innerHTML = [0.5,0.75,1,1.25,1.5,2,3].map(s =>
            `<div class="yk-speed-item${s===1?' active':''}" data-speed="${s}">${s}x</div>`
        ).join('');
        dpContainer.appendChild(speedMenu);

        // 弹幕颜色面板
        const danmakuColorPanel = document.createElement('div');
        danmakuColorPanel.className = 'yk-danmaku-color-panel';
        danmakuColorPanel.id = 'ykDanmakuColorPanel';
        const danmakuColors = [
            {name:'白色', color:'#ffffff'},
            {name:'红色', color:'#ff4444'},
            {name:'橙色', color:'#ff6b35'},
            {name:'黄色', color:'#ffcc00'},
            {name:'绿色', color:'#00cc66'},
            {name:'蓝色', color:'#00aaff'},
            {name:'紫色', color:'#aa66ff'},
            {name:'粉色', color:'#ff66aa'},
        ];
        danmakuColorPanel.innerHTML = `
            <div class="yk-danmaku-color-panel-title">弹幕颜色</div>
            <div class="yk-danmaku-colors">
                ${danmakuColors.map(c => 
                    `<div class="yk-danmaku-color-item${c.color==='#ffffff'?' active':''}" 
                          data-color="${c.color}" 
                          style="background:${c.color}" 
                          title="${c.name}"></div>`
                ).join('')}
            </div>
        `;
        dpContainer.appendChild(danmakuColorPanel);

        // ========== DOM ==========
        const ykPlay = document.getElementById('ykPlay');
        const ykProgress = document.getElementById('ykProgress');
        const ykBuffer = document.getElementById('ykBuffer');
        const ykPlayed = document.getElementById('ykPlayed');
        const ykTooltip = document.getElementById('ykTooltip');
        const ykMiniInner = document.getElementById('ykMiniInner');
        const ykCurTime = document.getElementById('ykCurTime');
        const ykCountdown = document.getElementById('ykCountdown');
        const ykVolBtn = document.getElementById('ykVolBtn');
        const ykVolBar = document.getElementById('ykVolBar');
        const ykVolFill = document.getElementById('ykVolFill');
        const ykSpeedBtn = document.getElementById('ykSpeedBtn');
        const ykSpeedMenu = document.getElementById('ykSpeedMenu');
        const ykPip = document.getElementById('ykPip');
        const ykWebFull = document.getElementById('ykWebFull');
        const ykFull = document.getElementById('ykFull');
        const ykToast = document.getElementById('ykToast');

        // 弹幕相关DOM
        const ykDanmakuToggle = document.getElementById('ykDanmakuToggle');
        const ykDanmakuColorBtn = document.getElementById('ykDanmakuColorBtn');
        const ykDanmakuColorPanel = document.getElementById('ykDanmakuColorPanel');
        const ykDanmakuInput = document.getElementById('ykDanmakuInput');
        const ykDanmakuSend = document.getElementById('ykDanmakuSend');

        // ========== 工具函数 ==========
        function fmt(s) {
            if (isNaN(s)) return '00:00';
            return String(Math.floor(s/60)).padStart(2,'0') + ':' + String(Math.floor(s%60)).padStart(2,'0');
        }

        let toastTmr = null;
        function showToast(msg) {
            ykToast.textContent = msg;
            ykToast.classList.add('show');
            clearTimeout(toastTmr);
            toastTmr = setTimeout(() => ykToast.classList.remove('show'), 1500);
        }

        // ========== 播放/暂停 ==========
        ykPlay.onclick = () => dp.toggle();

        dp.on('play', () => {
            ykPlay.innerHTML = '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
            dpContainer.classList.remove('paused');
        });

        dp.on('pause', () => {
            ykPlay.innerHTML = '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
            dpContainer.classList.add('paused');
        });

        // ========== 时间更新 ==========
        dp.on('timeupdate', () => {
            const cur = dp.video.currentTime;
            const dur = dp.video.duration;
            if (!dur) return;
            const pct = (cur / dur) * 100;
            const remaining = dur - cur;
            ykCurTime.textContent = fmt(cur);
            ykCountdown.textContent = '-' + fmt(remaining);
            ykPlayed.style.width = pct + '%';
            ykMiniInner.style.width = pct + '%';
        });

        dp.on('progress', () => {
            if (dp.video.buffered.length > 0) {
                const loaded = dp.video.buffered.end(dp.video.buffered.length - 1);
                const dur = dp.video.duration;
                if (dur) ykBuffer.style.width = (loaded / dur * 100) + '%';
            }
        });

        // ========== 进度条交互 ==========
        let isDragging = false;

        function seekFromEvent(e) {
            const rect = ykProgress.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const pct = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
            dp.seek(pct * dp.video.duration);
        }

        // 鼠标事件
        ykProgress.onmousedown = (e) => {
            isDragging = true;
            seekFromEvent(e);
        };

        document.addEventListener('mousemove', (e) => {
            if (isDragging) seekFromEvent(e);

            // 预览时间
            const rect = ykProgress.getBoundingClientRect();
            if (e.clientX >= rect.left && e.clientX <= rect.right) {
                const pct = (e.clientX - rect.left) / rect.width;
                ykTooltip.textContent = fmt(pct * dp.video.duration);
                ykTooltip.style.left = (e.clientX - rect.left) + 'px';
            }
        });

        document.addEventListener('mouseup', () => { isDragging = false; });

        // 触摸事件（移动端）
        ykProgress.ontouchstart = (e) => {
            isDragging = true;
            seekFromEvent(e);
            e.preventDefault();
        };

        document.addEventListener('touchmove', (e) => {
            if (isDragging) {
                seekFromEvent(e);
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchend', () => { isDragging = false; });

        // ========== 音量 ==========
        let lastVol = 0.7;

        ykVolBtn.onclick = () => {
            if (dp.video.volume > 0) {
                lastVol = dp.video.volume;
                dp.volume(0);
                ykVolFill.style.width = '0%';
                document.getElementById('ykVolIcon').innerHTML = '<path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>';
            } else {
                dp.volume(lastVol);
                ykVolFill.style.width = (lastVol * 100) + '%';
                document.getElementById('ykVolIcon').innerHTML = '<path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>';
            }
        };

        ykVolBar.onclick = (e) => {
            const rect = ykVolBar.getBoundingClientRect();
            const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            dp.volume(pct);
            ykVolFill.style.width = (pct * 100) + '%';
            if (pct > 0) {
                document.getElementById('ykVolIcon').innerHTML = '<path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>';
            }
        };

        // ========== 倍速 ==========
        ykSpeedBtn.onclick = (e) => {
            e.stopPropagation();
            ykSpeedMenu.classList.toggle('show');
        };

        ykSpeedMenu.querySelectorAll('.yk-speed-item').forEach(item => {
            item.onclick = (e) => {
                e.stopPropagation();
                const spd = parseFloat(item.dataset.speed);
                dp.speed(spd);
                ykSpeedBtn.querySelector('.yk-speed-text').textContent = spd + 'x';
                ykSpeedMenu.querySelectorAll('.yk-speed-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                ykSpeedMenu.classList.remove('show');
                showToast(spd + 'x');
            };
        });

        // ========== 弹幕功能 ==========
        let danmakuEnabled = true;
        let danmakuColor = '#ffffff';

        // 弹幕开关
        ykDanmakuToggle.onclick = () => {
            danmakuEnabled = !danmakuEnabled;
            ykDanmakuToggle.classList.toggle('off', !danmakuEnabled);
            showToast(danmakuEnabled ? '弹幕已开启' : '弹幕已关闭');
            
            // 显示/隐藏DPlayer弹幕层
            const danmakuLayer = dpContainer.querySelector('.dplayer-danmaku');
            if (danmakuLayer) {
                danmakuLayer.style.display = danmakuEnabled ? '' : 'none';
            }
        };

        // 弹幕设置按钮（打开颜色面板）
        ykDanmakuColorBtn.onclick = (e) => {
            e.stopPropagation();
            ykDanmakuColorPanel.classList.toggle('show');
        };

        // 点击空白关闭面板
        document.addEventListener('click', () => {
            ykDanmakuColorPanel.classList.remove('show');
        });

        // 弹幕颜色选择
        ykDanmakuColorPanel.querySelectorAll('.yk-danmaku-color-item').forEach(item => {
            item.onclick = (e) => {
                e.stopPropagation();
                danmakuColor = item.dataset.color;
                // 更新按钮SVG颜色
                ykDanmakuColorBtn.querySelector('svg circle:last-child').setAttribute('fill', danmakuColor);
                ykDanmakuColorPanel.querySelectorAll('.yk-danmaku-color-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                ykDanmakuColorPanel.classList.remove('show');
            };
        });

        // 发送弹幕
        function sendDanmaku() {
            const text = ykDanmakuInput.value.trim();
            if (!text) return;
            
            // 使用DPlayer的弹幕API
            dp.danmaku.draw({
                text: text,
                color: danmakuColor,
                type: 'right' // 右侧滚动弹幕
            });
            
            ykDanmakuInput.value = '';
            showToast('弹幕已发送');
        }

        ykDanmakuSend.onclick = (e) => {
            e.stopPropagation();
            sendDanmaku();
        };

        ykDanmakuInput.onclick = (e) => e.stopPropagation();

        ykDanmakuInput.onkeydown = (e) => {
            e.stopPropagation();
            if (e.key === 'Enter') {
                e.preventDefault();
                sendDanmaku();
            }
        };

        // ========== 其他按钮 ==========
        ykPip.onclick = () => {
            if (document.pictureInPictureElement) document.exitPictureInPicture();
            else if (dp.video.requestPictureInPicture) dp.video.requestPictureInPicture();
        };

        let isWebFull = false;
        ykWebFull.onclick = () => {
            isWebFull = !isWebFull;
            if (isWebFull) {
                dpContainer.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:99999;';
                showToast('网页全屏');
            } else {
                dpContainer.style.cssText = '';
                showToast('退出网页全屏');
            }
        };

        ykFull.onclick = () => {
            if (document.fullscreenElement) document.exitFullscreen();
            else dpContainer.requestFullscreen();
        };

        // 双击播放/暂停
        dp.video.ondblclick = () => {
            dp.toggle();
        };

        // 单击显示控制栏（防止与双击冲突）
        let clickTmr = null;
        dp.video.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (clickTmr) { clearTimeout(clickTmr); clickTmr = null; return; }
            clickTmr = setTimeout(() => { 
                clickTmr = null;
                // 单击：显示/隐藏控制栏
                const controls = dpContainer.querySelector('.yk-controls');
                if (controls) controls.style.opacity = controls.style.opacity === '1' ? '0' : '1';
            }, 250);
        };

        // 点击空白关闭菜单
        dpContainer.onclick = (e) => {
            if (!ykSpeedMenu.contains(e.target) && e.target !== ykSpeedBtn) {
                ykSpeedMenu.classList.remove('show');
            }
            if (!ykDanmakuColorPanel.contains(e.target) && e.target !== ykDanmakuColorBtn && !ykDanmakuColorBtn.contains(e.target)) {
                ykDanmakuColorPanel.classList.remove('show');
            }
        };

        // 快捷键
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            switch(e.key) {
                case ' ':
                case 'k': e.preventDefault(); dp.toggle(); break;
                case 'f': e.preventDefault(); ykFull.click(); break;
                case 'm': e.preventDefault(); ykVolBtn.click(); break;
                case 'ArrowLeft': e.preventDefault(); dp.seek(dp.video.currentTime - 5); break;
                case 'ArrowRight': e.preventDefault(); dp.seek(dp.video.currentTime + 5); break;
                case 'ArrowUp':
                    e.preventDefault();
                    dp.volume(Math.min(1, dp.video.volume + 0.1));
                    ykVolFill.style.width = (dp.video.volume * 100) + '%';
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    dp.volume(Math.max(0, dp.video.volume - 0.1));
                    ykVolFill.style.width = (dp.video.volume * 100) + '%';
                    break;
            }
        });

        // ========== 选集 ==========
        @if($player->videos->count() > 1)
        const eps = @json($player->videos->select('id','title','sort_order'));

        const epTrigger = document.createElement('button');
        epTrigger.className = 'yk-ep-trigger';
        epTrigger.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>';
        epTrigger.onclick = () => epPanel.classList.toggle('show');
        dpContainer.appendChild(epTrigger);

        const epPanel = document.createElement('div');
        epPanel.className = 'yk-episode-panel';
        epPanel.innerHTML = `
            <div class="yk-episode-head">
                <span class="yk-episode-head-title">选集 (${eps.length}集)</span>
                <button class="yk-episode-close" onclick="this.parentElement.parentElement.classList.remove('show')">✕</button>
            </div>
            <div class="yk-episode-body" id="ykEpBody"></div>
        `;
        dpContainer.appendChild(epPanel);

        const epBody = document.getElementById('ykEpBody');
        eps.forEach((ep, i) => {
            const btn = document.createElement('div');
            btn.className = 'yk-ep-item' + (i === 0 ? ' active' : '');
            btn.textContent = i + 1;
            btn.title = ep.title || ('第' + (i+1) + '集');
            btn.onclick = () => showToast('第' + (i+1) + '集');
            epBody.appendChild(btn);
        });
        @endif

        // ========== Logo（标准版位置） ==========
        @if($player->logo_url)
        const logo = document.createElement('div');
        logo.className = 'yk-logo';
        logo.innerHTML = '<img src="{{ $player->logo_url }}" alt="logo">';
        // 响应式位置：移动端用4%，桌面用60px
        const logoPos = '{{ $player->logo_position ?? "top-left" }}';
        const logoEdge = window.innerWidth <= 768 ? '4%' : '60px';
        if (logoPos === 'center') {
            logo.style.top = '50%';
            logo.style.left = '50%';
            logo.style.transform = 'translate(-50%, -50%)';
        } else {
            if (logoPos.includes('top')) logo.style.top = logoEdge;
            if (logoPos.includes('bottom')) logo.style.bottom = logoEdge;
            if (logoPos.includes('left')) logo.style.left = logoEdge;
            if (logoPos.includes('right')) logo.style.right = logoEdge;
        }
        // 移动端缩小logo
        if (window.innerWidth <= 768) {
            logo.querySelector('img').style.height = '32px';
        }
        dpContainer.appendChild(logo);
        @endif

        // ========== 文字水印（响应式位置） ==========
        @if($player->watermark_text)
        const watermark = document.createElement('div');
        watermark.className = 'yk-watermark';
        watermark.textContent = '{{ $player->watermark_text }}';
        // 响应式位置：移动端用4%，桌面用60px
        const wmPos = '{{ $player->watermark_position ?? "top-right" }}';
        const wmFontSize = {{ intval($player->watermark_font_size) ?: 14 }};
        const wmColor = '{{ $player->watermark_color ?? "rgba(255,255,255,0.6)" }}';
        const wmOpacity = {{ $player->watermark_opacity ?? 0.3 }};
        // 移动端缩小字体
        watermark.style.fontSize = (window.innerWidth <= 768 ? Math.max(10, wmFontSize * 0.75) : wmFontSize) + 'px';
        watermark.style.color = wmColor;
        watermark.style.opacity = wmOpacity;
        const wmEdge = window.innerWidth <= 768 ? '4%' : '60px';
        if (wmPos === 'center') {
            watermark.style.top = '50%';
            watermark.style.left = '50%';
            watermark.style.transform = 'translate(-50%, -50%)';
        } else {
            if (wmPos.includes('top')) watermark.style.top = wmEdge;
            if (wmPos.includes('bottom')) watermark.style.bottom = wmEdge;
            if (wmPos.includes('left')) watermark.style.left = wmEdge;
            if (wmPos.includes('right')) watermark.style.right = wmEdge;
        }
        dpContainer.appendChild(watermark);
        @endif

        // ========== 进度条图标 ==========
        @if($player->progress_icon_url)
        const progIcon = document.createElement('div');
        progIcon.style.cssText = 'position:absolute;top:50%;left:0;transform:translate(-50%,-50%);width:28px;height:28px;z-index:5;pointer-events:none;';
        const iconUrl = '{{ $player->progress_icon_url }}';
        if (iconUrl.startsWith('http') || iconUrl.startsWith('/')) {
            progIcon.innerHTML = '<img src="' + iconUrl + '" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 1px 3px rgba(0,0,0,0.5));">';
        } else {
            progIcon.innerHTML = '<span style="font-size:22px;line-height:1;filter:drop-shadow(0 1px 3px rgba(0,0,0,0.5));">' + iconUrl + '</span>';
        }
        ykProgress.querySelector('.yk-progress-track').appendChild(progIcon);

        dp.on('timeupdate', () => {
            if (!dp.video.duration) return;
            progIcon.style.left = (dp.video.currentTime / dp.video.duration * 100) + '%';
        });
        @endif

        // ========== 自动播放 ==========
        @if($player->autoplay)
        dp.video.muted = true;
        setTimeout(() => { dp.play().catch(()=>{}); }, 300);
        let unmuted = false;
        dp.on('click', () => {
            if (!unmuted) { unmuted = true; dp.video.muted = false; showToast('已开启声音'); }
        });
        @endif

        // ========== 记忆播放 ==========
        const saved = localStorage.getItem('dp_time_' + PLAYER_ID);
        if (saved && parseFloat(saved) > 0) {
            dp.on('loadedmetadata', () => {
                dp.seek(parseFloat(saved));
                showToast('已恢复播放位置');
            });
        }
        dp.on('timeupdate', () => {
            localStorage.setItem('dp_time_' + PLAYER_ID, dp.video.currentTime);
        });
        }
    })();
    </script>
</body>
</html>
