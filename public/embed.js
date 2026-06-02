/**
 * 影视中心嵌入式播放器 v1.0
 * 外站一行代码引入，自带完整广告系统
 *
 * 使用方式：
 * <div id="my-player"></div>
 * <script src="http://dem.viesta.cn/embed.js"
 *   data-container="#my-player"
 *   data-video="v001"
 *   data-width="100%"
 *   data-height="500">
 * </script>
 */

(function() {
    'use strict';

    // ========== 配置 ==========
    const SCRIPT = document.currentScript;
    const BASE_URL = new URL(SCRIPT.src).origin;

    const CONFIG = {
        container: SCRIPT.getAttribute('data-container') || '#my-player',
        videoId: SCRIPT.getAttribute('data-video') || '',
        videoUrl: SCRIPT.getAttribute('data-url') || '',
        videoType: SCRIPT.getAttribute('data-type') || '',
        videoCover: SCRIPT.getAttribute('data-cover') || '',
        width: SCRIPT.getAttribute('data-width') || '100%',
        height: SCRIPT.getAttribute('data-height') || '500',
        autoplay: SCRIPT.getAttribute('data-autoplay') !== 'false',
        theme: SCRIPT.getAttribute('data-theme') || '#e6a817',
        logo: SCRIPT.getAttribute('data-logo') || '',
        logoText: SCRIPT.getAttribute('data-logo-text') || '',
    };

    // ========== 注入样式 ==========
    function injectStyles() {
        if (document.getElementById('ep-player-styles')) return;
        const style = document.createElement('style');
        style.id = 'ep-player-styles';
        style.textContent = `
            .ep-wrap { position: relative; width: ${CONFIG.width}; max-width: 100%; background: #000; border-radius: 8px; overflow: hidden; }
            .ep-wrap .dplayer { width: 100%; height: 100%; }
            .ep-logo { position: absolute; z-index: 100; pointer-events: none; }
            .ep-logo.tl { top: 12px; left: 12px; }
            .ep-logo.tr { top: 12px; right: 12px; }
            .ep-logo.bl { bottom: 60px; left: 12px; }
            .ep-logo.br { bottom: 60px; right: 12px; }
            .ep-logo img { max-width: 100px; max-height: 32px; opacity: 0.7; }
            .ep-logo span { color: rgba(255,255,255,0.5); font-size: 12px; text-shadow: 0 1px 3px rgba(0,0,0,0.5); user-select: none; }
        `;
        document.head.appendChild(style);
    }

    // ========== 加载依赖 ==========
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
            const s = document.createElement('script');
            s.src = src; s.onload = resolve; s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function loadCSS(href) {
        return new Promise((resolve) => {
            if (document.querySelector(`link[href="${href}"]`)) { resolve(); return; }
            const l = document.createElement('link');
            l.rel = 'stylesheet'; l.href = href; l.onload = resolve;
            document.head.appendChild(l);
        });
    }

    // ========== 工具函数 ==========
    function detectType(url) {
        if (/\.m3u8(\?|$)/i.test(url)) return 'm3u8';
        if (/\.flv(\?|$)/i.test(url)) return 'flv';
        if (/\.mp4(\?|$)/i.test(url)) return 'mp4';
        if (/\.webm(\?|$)/i.test(url)) return 'mp4';
        return 'mp4'; // 默认mp4
    }

    // ========== API请求 ==========
    async function api(path) {
        const res = await fetch(`${BASE_URL}${path}`);
        return res.json();
    }

    // ========== 初始化 ==========
    async function init() {
        injectStyles();

        // 加载依赖
        await Promise.all([
            loadCSS('https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css'),
            loadScript('https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js'),
            loadScript('https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js'),
            loadScript('https://cdn.jsdelivr.net/npm/flv.js@1.6.2/dist/flv.min.js'),
        ]);

        // 加载媒体引擎
        await loadScript(`${BASE_URL}/js/media-engine.js`);

        // 获取容器
        const container = document.querySelector(CONFIG.container);
        if (!container) { console.error('[EmbedPlayer] 容器不存在:', CONFIG.container); return; }

        // 创建播放器容器
        const wrap = document.createElement('div');
        wrap.className = 'ep-wrap';
        wrap.style.height = CONFIG.height + (CONFIG.height.match(/\d$/) ? 'px' : '');
        container.appendChild(wrap);

        const playerDiv = document.createElement('div');
        playerDiv.id = 'ep-dplayer-' + Date.now();
        playerDiv.style.width = '100%';
        playerDiv.style.height = '100%';
        wrap.appendChild(playerDiv);

        // 加载视频
        let video;
        if (CONFIG.videoUrl) {
            // 方式三：直接传URL
            video = {
                url: CONFIG.videoUrl,
                type: CONFIG.videoType || detectType(CONFIG.videoUrl),
                cover: CONFIG.videoCover || '',
                title: '外部视频'
            };
        } else {
            try {
                if (CONFIG.videoId) {
                    // 方式一：用系统视频ID
                    video = await api(`/api/videos/${CONFIG.videoId}`);
                } else {
                    // 方式二：默认播放第一个
                    const videos = await api('/api/videos');
                    video = videos[0];
                }
            } catch (e) {
                console.error('[EmbedPlayer] 加载视频失败:', e);
                return;
            }
        }

        if (!video || video.error) {
            console.error('[EmbedPlayer] 视频不存在');
            return;
        }

        // 加载设置
        let settings = {};
        try { settings = await api('/api/settings'); } catch (e) {}

        // 构建视频配置
        const vc = { url: video.url, type: video.type || 'mp4', pic: video.cover || '' };

        // HLS支持
        if (video.type === 'm3u8' && typeof Hls !== 'undefined' && Hls.isSupported()) {
            vc.type = 'customHls';
            vc.customType = {
                customHls: function(videoEl) {
                    const hls = new Hls();
                    hls.loadSource(videoEl.src);
                    hls.attachMedia(videoEl);
                    hls.on(Hls.Events.MANIFEST_PARSED, () => videoEl.play());
                    hls.on(Hls.Events.ERROR, (_, data) => { if (data.fatal) hls.destroy(); });
                }
            };
        }
        // FLV支持
        else if (video.type === 'flv' && typeof flvjs !== 'undefined' && flvjs.isSupported()) {
            vc.type = 'customFlv';
            vc.customType = {
                customFlv: function(videoEl) {
                    const flv = flvjs.createPlayer({ type: 'flv', url: videoEl.src });
                    flv.attachMediaElement(videoEl);
                    flv.load();
                    flv.play();
                }
            };
        }

        // 创建DPlayer
        const dp = new DPlayer({
            container: playerDiv,
            autoplay: CONFIG.autoplay,
            theme: CONFIG.theme,
            lang: 'zh-cn',
            screenshot: true,
            hotkey: true,
            preload: 'auto',
            volume: 0.7,
            mutex: true,
            video: vc
        });

        // 初始化广告系统
        const mm = new MediaManager(dp);
        await mm.loadCampaigns();

        // 广告流程
        await mm.playSplash();
        await mm.playPreroll();

        dp.on('timeupdate', () => { if (!dp.video.paused) mm.checkMidroll(dp.video.currentTime); });
        dp.on('pause', () => { if (!mm.skipNextPausePromo) mm.playPausePromo(); });
        dp.on('play', () => { mm.clearPausePromo(); });
        dp.on('ended', () => { mm.playPostroll(); });

        mm.showMarquee();
        mm.startOverlayRotation();

        // 水印
        if (CONFIG.logo || CONFIG.logoText) {
            const logoEl = document.createElement('div');
            logoEl.className = 'ep-logo tr';
            if (CONFIG.logo) {
                logoEl.innerHTML = `<img src="${CONFIG.logo}" alt="Logo">`;
            } else {
                logoEl.innerHTML = `<span>${CONFIG.logoText}</span>`;
            }
            wrap.appendChild(logoEl);
        }

        console.log('[EmbedPlayer] 初始化完成 ✓');
    }

    // DOM Ready后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
