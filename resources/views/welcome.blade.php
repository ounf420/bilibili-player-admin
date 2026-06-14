<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>B站播放器</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <style>
        :root {
            --primary: #00a1d6;
            --primary-dark: #0088b9;
            --bg-dark: #0a0a0f;
            --bg-card: #12121a;
            --bg-hover: #1a1a25;
            --text-primary: #e8e8e8;
            --text-secondary: #888;
            --border: #222;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg-dark);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* 顶部导航 */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: rgba(10, 10, 15, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 1000;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
        }

        .nav-logo i { font-size: 24px; }

        .nav-search {
            flex: 1;
            max-width: 400px;
            margin: 0 40px;
            position: relative;
        }

        .nav-search input {
            width: 100%;
            height: 36px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 0 40px 0 16px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }

        .nav-search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 161, 214, 0.2);
        }

        .nav-search i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .nav-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            background: var(--bg-hover);
            color: var(--primary);
            border-color: var(--primary);
        }

        /* 主内容区 */
        .main-content {
            padding-top: 56px;
            display: flex;
            min-height: 100vh;
        }

        /* 左侧播放器区域 */
        .player-section {
            flex: 1;
            padding: 20px;
        }

        .player-wrapper {
            position: relative;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }

        #dplayer {
            width: 100%;
            aspect-ratio: 16/9;
        }

        /* 视频信息 */
        .video-info {
            padding: 20px;
            background: var(--bg-card);
            border-radius: 12px;
            margin-top: 16px;
        }

        .video-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .video-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            color: var(--text-secondary);
            font-size: 13px;
        }

        .video-meta i {
            margin-right: 4px;
            color: var(--primary);
        }

        .video-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .action-btn:hover {
            background: rgba(0, 161, 214, 0.1);
            border-color: var(--primary);
            color: var(--primary);
        }

        .action-btn.active {
            background: rgba(0, 161, 214, 0.15);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* 右侧推荐列表 */
        .recommend-section {
            width: 360px;
            padding: 20px;
            background: var(--bg-card);
            border-left: 1px solid var(--border);
            overflow-y: auto;
            max-height: calc(100vh - 56px);
        }

        .recommend-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recommend-title i {
            color: var(--primary);
        }

        .recommend-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .recommend-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: var(--bg-hover);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .recommend-item:hover {
            background: rgba(0, 161, 214, 0.1);
            transform: translateX(4px);
        }

        .recommend-thumb {
            width: 140px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
        }

        .recommend-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recommend-thumb .duration {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .recommend-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .recommend-name {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .recommend-stats {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .recommend-stats i {
            margin-right: 2px;
        }

        /* 水印 */
        .player-logo {
            position: absolute;
            z-index: 100;
            pointer-events: none;
        }
        .player-logo.top-left { top: 20px; left: 20px; }
        .player-logo.top-right { top: 20px; right: 20px; }
        .player-logo.bottom-left { bottom: 70px; left: 20px; }
        .player-logo.bottom-right { bottom: 70px; right: 20px; }
        .player-logo img { max-width: 120px; max-height: 40px; opacity: 0.7; }
        .player-logo .text-watermark {
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            white-space: nowrap;
            user-select: none;
        }

        /* 响应式 */
        @media (max-width: 1024px) {
            .recommend-section {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .player-section {
                padding: 12px;
            }
            .nav-search {
                display: none;
            }
            .video-actions {
                flex-wrap: wrap;
            }
        }

        /* 加载动画 */
        .loading-skeleton {
            background: linear-gradient(90deg, var(--bg-hover) 25%, var(--bg-card) 50%, var(--bg-hover) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 6px;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <!-- 顶部导航 -->
    <nav class="top-nav">
        <div class="nav-logo">
            <i class="ri-video-chat-fill"></i>
            <span>B站播放器</span>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="搜索视频..." id="searchInput">
            <i class="ri-search-line"></i>
        </div>
        <div class="nav-actions">
            <div class="nav-btn" title="历史记录">
                <i class="ri-history-line"></i>
            </div>
            <div class="nav-btn" title="收藏">
                <i class="ri-heart-line"></i>
            </div>
            <div class="nav-btn" title="设置">
                <i class="ri-settings-3-line"></i>
            </div>
        </div>
    </nav>

    <!-- 主内容区 -->
    <div class="main-content">
        <!-- 播放器区域 -->
        <div class="player-section">
            <div class="player-wrapper">
                <div id="dplayer"></div>
                <div class="player-logo" id="player-logo" style="display:none;"></div>
            </div>

            <!-- 视频信息 -->
            <div class="video-info">
                <h1 class="video-title" id="videoTitle">正在加载...</h1>
                <div class="video-meta">
                    <span><i class="ri-play-circle-line"></i> <span id="viewCount">0</span> 次播放</span>
                    <span><i class="ri-time-line"></i> <span id="uploadTime">--</span></span>
                    <span><i class="ri-thumb-up-line"></i> <span id="likeCount">0</span></span>
                </div>
                <div class="video-actions">
                    <div class="action-btn" id="btnLike">
                        <i class="ri-thumb-up-line"></i>
                        <span>点赞</span>
                    </div>
                    <div class="action-btn" id="btnCoin">
                        <i class="ri-coin-line"></i>
                        <span>投币</span>
                    </div>
                    <div class="action-btn" id="btnFav">
                        <i class="ri-star-line"></i>
                        <span>收藏</span>
                    </div>
                    <div class="action-btn" id="btnShare">
                        <i class="ri-share-forward-line"></i>
                        <span>分享</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右侧推荐 -->
        <div class="recommend-section">
            <div class="recommend-title">
                <i class="ri-fire-line"></i>
                <span>推荐视频</span>
            </div>
            <div class="recommend-list" id="recommendList">
                <!-- 动态加载 -->
            </div>
        </div>
    </div>

    <!-- DPlayer核心 -->
    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
    <!-- HLS.js 支持 m3u8 -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js"></script>
    <!-- FLV.js 支持 flv -->
    <script src="https://cdn.jsdelivr.net/npm/flv.js@1.6.2/dist/flv.min.js"></script>
    <!-- 推广管理器 -->
    <script src="/js/media-engine.js"></script>
    <script>
        let dp = null;
        let mediaManager = null;
        let settings = {};

        async function loadSettings() {
            try {
                const res = await fetch('/api/settings');
                settings = await res.json();
            } catch (e) { console.error('加载设置失败:', e); }
        }

        function showLogo() {
            const logoEl = document.getElementById('player-logo');
            const logoType = settings.logo_type || 'text';
            const logoPosition = settings.logo_position || 'top-right';
            logoEl.className = `player-logo ${logoPosition}`;
            if (logoType === 'image' && settings.logo_url) {
                logoEl.innerHTML = `<img src="${settings.logo_url}" alt="Logo">`;
                logoEl.style.display = 'block';
            } else if (logoType === 'text' && settings.logo_text) {
                logoEl.innerHTML = `<span class="text-watermark">${settings.logo_text}</span>`;
                logoEl.style.display = 'block';
            } else {
                logoEl.style.display = 'none';
            }
        }

        // 根据视频类型构建DPlayer配置
        function buildVideoConfig(video) {
            const type = video.type || 'mp4';
            const config = {
                url: video.url,
                type: type,
                pic: video.cover || '',
            };

            // M3U8 使用 hls.js 解码
            if (type === 'm3u8' && typeof Hls !== 'undefined' && Hls.isSupported()) {
                config.type = 'customHls';
                config.customType = {
                    customHls: function(video, player) {
                        const hls = new Hls();
                        hls.loadSource(video.src);
                        hls.attachMedia(video);
                        hls.on(Hls.Events.MANIFEST_PARSED, function() {
                            video.play();
                        });
                        hls.on(Hls.Events.ERROR, function(event, data) {
                            if (data.fatal) {
                                console.error('HLS加载失败:', data);
                                hls.destroy();
                            }
                        });
                    }
                };
            }
            // FLV 使用 flv.js 解码
            else if (type === 'flv' && typeof flvjs !== 'undefined' && flvjs.isSupported()) {
                config.type = 'customFlv';
                config.customType = {
                    customFlv: function(video, player) {
                        const flvPlayer = flvjs.createPlayer({
                            type: 'flv',
                            url: video.src,
                        });
                        flvPlayer.attachMediaElement(video);
                        flvPlayer.load();
                        flvPlayer.play();
                    }
                };
            }

            return config;
        }

        async function initPlayer(video) {
            await loadSettings();

            if (dp) { dp.destroy(); }
            if (mediaManager) { mediaManager.destroy(); }

            const videoConfig = buildVideoConfig(video);

            dp = new DPlayer({
                container: document.getElementById('dplayer'),
                autoplay: settings.autoplay !== false,
                theme: settings.theme_color || '#00a1d6',
                loop: settings.loop || false,
                lang: 'zh-cn',
                screenshot: settings.show_screenshot !== false,
                hotkey: true,
                preload: settings.preload || 'auto',
                volume: settings.volume || 0.7,
                mutex: true,
                video: videoConfig
            });

            mediaManager = new MediaManager(dp);
            await mediaManager.loadAds();

            // 1. 开屏推广
            await mediaManager.playSplash();

            // 2. 前贴片推广
            setTimeout(async () => {
                await mediaManager.playPreroll();
            }, 100);

            // 3. 中贴片推广（按触发时间）
            dp.on('timeupdate', () => {
                if (dp.video.paused) return;
                mediaManager.checkMidroll(dp.video.currentTime);
            });

            // 4. 暂停推广
            dp.on('pause', () => {
                if (mediaManager.skipNextPauseAd) return;
                mediaManager.playPauseAd();
            });
            dp.on('play', () => {
                mediaManager.clearPauseAd();
            });

            // 5. 后贴片推广
            dp.on('ended', () => {
                mediaManager.playPostroll();
            });

            // 6. 跑马灯
            mediaManager.showMarquee();

            // 7. 角标
            mediaManager.showOverlay();

            // 8. 扫码贴片
            mediaManager.showQrcode();

            // 9. 横幅
            mediaManager.showBanner();

            showLogo();

            // 更新视频信息
            document.getElementById('videoTitle').textContent = settings.title || '未知视频';
            document.getElementById('viewCount').textContent = settings.view_count || '0';
            document.getElementById('uploadTime').textContent = settings.upload_time || '今天';
            document.getElementById('likeCount').textContent = settings.like_count || '0';
        }

        // 加载推荐视频
        async function loadRecommendVideos() {
            try {
                const res = await fetch('/api/recommend');
                const videos = await res.json();
                const container = document.getElementById('recommendList');
                container.innerHTML = videos.map(v => `
                    <div class="recommend-item" onclick="loadVideo('${v.id}')">
                        <div class="recommend-thumb">
                            <img src="${v.cover || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTQwIiBoZWlnaHQ9IjgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxNDAiIGhlaWdodD0iODAiIGZpbGw9IiMyMiIvPjx0ZXh0IHg9IjcwIiB5PSI0NCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzU1NSIgZm9udC1zaXplPSIxMiI+5Yqp5Zu954q25YiXPC90ZXh0Pjwvc3ZnPg=='}" alt="">
                            <span class="duration">${v.duration || '00:00'}</span>
                        </div>
                        <div class="recommend-info">
                            <div class="recommend-name">${v.title || '未知视频'}</div>
                            <div class="recommend-stats">
                                <span><i class="ri-play-circle-line"></i> ${v.views || '0'}</span>
                                <span><i class="ri-thumb-up-line"></i> ${v.likes || '0'}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (e) {
                console.error('加载推荐失败:', e);
            }
        }

        // 加载视频
        async function loadVideo(videoId) {
            try {
                if (videoId) {
                    const res = await fetch(`/api/video/${videoId}`);
                    const video = await res.json();
                    if (video) {
                        initPlayer(video);
                        return;
                    }
                }
                // 默认加载设置
                const res = await fetch('/api/settings');
                const settings = await res.json();
                if (settings) {
                    initPlayer(settings);
                }
            } catch (e) { console.error('加载视频失败:', e); }
        }

        // 交互按钮
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('active');
                const icon = this.querySelector('i');
                if (icon.classList.contains('ri-thumb-up-line')) {
                    icon.classList.toggle('ri-thumb-up-fill');
                } else if (icon.classList.contains('ri-star-line')) {
                    icon.classList.toggle('ri-star-fill');
                }
            });
        });

        // 从URL参数获取视频ID
        const urlParams = new URLSearchParams(window.location.search);
        const videoId = urlParams.get('v');

        // 初始化
        loadVideo(videoId);
        loadRecommendVideos();
    </script>
</body>

@include('components.notice-popup')
</html>
