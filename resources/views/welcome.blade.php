<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>B站播放器</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #000; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }
        #dplayer { width: 100vw; height: 100vh; }
        .player-logo { position: fixed; z-index: 100; pointer-events: none; }
        .player-logo.top-left { top: 20px; left: 20px; }
        .player-logo.top-right { top: 20px; right: 20px; }
        .player-logo.bottom-left { bottom: 70px; left: 20px; }
        .player-logo.bottom-right { bottom: 70px; right: 20px; }
        .player-logo img { max-width: 120px; max-height: 40px; opacity: 0.7; }
        .player-logo .text-watermark { color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 500; text-shadow: 0 1px 3px rgba(0,0,0,0.5); white-space: nowrap; user-select: none; }
    </style>
</head>
<body>
    <div id="dplayer"></div>
    <div class="player-logo" id="player-logo" style="display:none;"></div>

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
        }

        // 加载视频列表，支持选择播放
        async function loadVideo(videoId) {
            try {
                if (videoId) {
                    const res = await fetch(`/api/videos/${videoId}`);
                    const video = await res.json();
                    if (video && !video.error) {
                        initPlayer(video);
                        return;
                    }
                }
                // 默认播放第一个
                const res = await fetch('/api/videos');
                const videos = await res.json();
                if (videos.length > 0) {
                    initPlayer(videos[0]);
                }
            } catch (e) { console.error('加载视频失败:', e); }
        }

        // 从URL参数获取视频ID
        const urlParams = new URLSearchParams(window.location.search);
        const videoId = urlParams.get('v');

        // 移动端：保持竖屏播放
        loadVideo(videoId);
    </script>
</body>

@include('components.notice-popup')
</html>
