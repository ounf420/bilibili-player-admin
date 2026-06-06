<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>播放器</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #000; }
        #dplayer { width: 100%; height: 100%; }
        .dplayer-bezel,
        .dplayer-bezel .dplayer-bezel-icon,
        .dplayer-bezel .dplayer-bezel-transition { display: none !important; opacity: 0 !important; animation: none !important; }
        .dplayer-mobile-play { display: none !important; }
        .dplayer-mask { display: none !important; }
        .dplayer-icons-left .dplayer-prev-btn,
        .dplayer-icons-left .dplayer-next-btn,
        .dplayer-icons-left .dplayer-pause-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; cursor: pointer; color: #fff; transition: all .2s; border-radius: 4px; }
        .dplayer-icons-left .dplayer-prev-btn:hover,
        .dplayer-icons-left .dplayer-next-btn:hover,
        .dplayer-icons-left .dplayer-pause-btn:hover { color: #00be06; background: rgba(255,255,255,.1); }
        .dplayer-icons-left .dplayer-prev-btn svg,
        .dplayer-icons-left .dplayer-next-btn svg,
        .dplayer-icons-left .dplayer-pause-btn svg { width: 20px; height: 20px; fill: currentColor; }
        .dplayer-icons-left .dplayer-pause-btn { width: 40px; height: 40px; }
        .dplayer-icons-left .dplayer-pause-btn svg { width: 24px; height: 24px; }
        .dp-danmaku-bar { display: none; align-items: center; gap: 6px; margin-left: 8px; flex-shrink: 0; }
        .dplayer-fulled .dp-danmaku-bar { display: flex; }
        .dp-danmaku-bar .dm-toggle { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; cursor: pointer; color: rgba(255,255,255,.7); transition: .2s; border-radius: 4px; }
        .dp-danmaku-bar .dm-toggle:hover { color: #fff; }
        .dp-danmaku-bar .dm-toggle.active { color: #00be06; }
        .dp-danmaku-bar .dm-input-wrap { display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 16px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); transition: .2s; }
        .dp-danmaku-bar .dm-input-wrap:focus-within { border-color: #00be06; background: rgba(255,255,255,.15); }
        .dp-danmaku-bar .dm-input { flex: 1; min-width: 0; background: none; border: none; color: #fff; font-size: 13px; outline: none; width: 80px; }
        .dp-danmaku-bar .dm-input::placeholder { color: rgba(255,255,255,.4); }
        .dp-danmaku-bar .dm-send { padding: 5px 14px; border-radius: 12px; background: #00be06; color: #fff; border: none; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; white-space: nowrap; flex-shrink: 0; }
        .dp-danmaku-bar .dm-send:hover { background: #00a305; }
        .player-logo { position: absolute; z-index: 100; pointer-events: none; }
        .player-logo.top-left { top: 20px; left: 20px; }
        .player-logo.top-right { top: 20px; right: 20px; }
        .player-logo.bottom-left { bottom: 70px; left: 20px; }
        .player-logo.bottom-right { bottom: 70px; right: 20px; }
        .player-logo img { max-width: 120px; max-height: 40px; opacity: 0.7; }
        .player-logo .text-watermark { color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 500; text-shadow: 0 1px 3px rgba(0,0,0,0.5); white-space: nowrap; user-select: none; }
        .preroll-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 200; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        .preroll-overlay video { width: 100%; height: 100%; object-fit: contain; }
        .preroll-skip { position: absolute; bottom: 80px; right: 30px; padding: 10px 24px; border-radius: 20px; background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3); font-size: 14px; cursor: pointer; backdrop-filter: blur(8px); transition: .2s; }
        .preroll-skip:hover { background: rgba(255,255,255,.25); }
    </style>
</head>
<body>
<div id="dplayer"></div>
<div class="player-logo" id="player-logo" style="display:none;"></div>
<script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flv.js@1.6.2/dist/flv.min.js"></script>
<script src="/js/media-engine.js"></script>
<script>
let dp = null, settings = {}, currentVideo = null;

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
}

function getHeaders() {
    return { 'X-CSRF-TOKEN': getCsrf(), 'Accept': 'application/json' };
}

async function loadSettings() {
    try {
        const r = await fetch('/api/settings');
        const d = await r.json();
        settings = d.data || d;
        applyWatermark();
    } catch (e) {
        console.warn('settings failed', e);
    }
}

function applyWatermark() {
    const el = document.getElementById('player-logo');
    if (!el || !settings) return;
    const show = settings.player_watermark_show || 0;
    if (!show) { el.style.display = 'none'; return; }
    const pos = settings.player_watermark_position || 'top-right';
    const type = settings.player_watermark_type || 'text';
    const txt = settings.player_watermark_text || '';
    const imgUrl = settings.player_watermark_image_url || '';
    if (type === 'image' && imgUrl) {
        el.innerHTML = '<img src="' + imgUrl + '" referrerpolicy="no-referrer">';
    } else if (type === 'text' && txt) {
        el.innerHTML = '<span class="text-watermark">' + txt + '</span>';
    } else {
        el.style.display = 'none';
        return;
    }
    el.className = 'player-logo ' + pos;
    el.style.display = 'block';
}

async function loadVideoById(id, pushState) {
    if (!id) return;
    try {
        const r = await fetch('/api/videos/' + id, { headers: getHeaders() });
        const d = await r.json();
        const v = d.data || d;
        if (!v || !v.id) return;
        currentVideo = v;
        if (pushState !== false) history.pushState({ id: v.id }, '', '/player?id=' + v.id);
        document.title = v.title + ' - 播放器';
        initPlayer(v);
    } catch (e) {
        console.error('loadVideoById failed', e);
    }
}

function initPlayer(v) {
    if (dp) { dp.destroy(); dp = null; }
    const container = document.getElementById('dplayer');
    container.innerHTML = '';
    let videoUrl = v.video_url || '';
    if (videoUrl && !videoUrl.startsWith('http')) videoUrl = window.location.origin + videoUrl;
    const isHls = videoUrl.indexOf('.m3u8') !== -1;
    const isFlv = videoUrl.indexOf('.flv') !== -1;
    const dpOptions = {
        container: container,
        autoplay: true,
        theme: '#00be06',
        lang: 'zh-cn',
        screenshot: true,
        hotkey: true,
        preload: 'auto',
        volume: 0.7,
        mutex: true,
        video: {
            url: videoUrl,
            type: isHls ? 'customHls' : (isFlv ? 'customFlv' : 'auto'),
            customType: {
                customHls: function(video, player) {
                    if (Hls.isSupported()) {
                        const hls = new Hls();
                        hls.loadSource(video.src);
                        hls.attachMedia(video);
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = videoUrl;
                    }
                },
                customFlv: function(video, player) {
                    if (flvjs.isSupported()) {
                        const flvPlayer = flvjs.createPlayer({ type: 'flv', url: video.src });
                        flvPlayer.attachMediaElement(video);
                        flvPlayer.load();
                        flvPlayer.play();
                    }
                }
            }
        },
        danmaku: { id: v.id, api: '/api/danmaku', addition: [] }
    };
    dp = new DPlayer(dpOptions);
    window.dp = dp;
    dp.on('play', function() {
        fetch('/api/videos/' + v.id + '/play', { method: 'POST', headers: getHeaders() }).catch(function() {});
    });
    loadAds(v.id);
}

async function loadAds(videoId) {
    try {
        const r = await fetch('/api/ads?video_id=' + videoId);
        const d = await r.json();
        const ads = d.data || d;
        if (!ads || !ads.length) return;
        const preroll = ads.find(function(a) { return a.type === 'preroll'; });
        if (preroll) playAd(preroll);
    } catch (e) {
        console.warn('loadAds failed', e);
    }
}

function playAd(ad) {
    if (!dp || !ad) return;
    dp.pause();
    const overlay = document.createElement('div');
    overlay.className = 'preroll-overlay';
    overlay.innerHTML = '<video src="' + ad.video_url + '" autoplay muted></video><button class="preroll-skip" onclick="this.parentElement.remove();dp.play();">跳过广告 ' + (ad.duration || 5) + 's</button>';
    document.querySelector('.dplayer').appendChild(overlay);
    setTimeout(function() { overlay.remove(); dp.play(); }, (ad.duration || 5) * 1000);
}

async function init() {
    await loadSettings();
    const params = new URLSearchParams(location.search);
    const videoId = params.get('id');
    if (videoId) {
        await loadVideoById(videoId, false);
    } else {
        try {
            const r = await fetch('/api/videos', { headers: getHeaders() });
            const d = await r.json();
            const videos = d.data || d;
            if (videos && videos.length > 0) await loadVideoById(videos[0].id, false);
        } catch (e) {
            console.error('load videos failed', e);
        }
    }
}

window.addEventListener('popstate', function(e) {
    if (e.state && e.state.id) loadVideoById(e.state.id, false);
});

init();
</script>
</body>
</html>
