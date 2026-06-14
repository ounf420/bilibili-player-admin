<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; background: #000; overflow: hidden; }
        
        /* 播放器全屏 */
        .player-wrap { width: 100%; height: 100vh; position: relative; }
        #dplayer { 
            width: 100%; 
            height: 100%; 
            position: relative; 
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
        
        /* 水印 - 放在播放器容器内，全屏也能显示 */
        .watermark {
            position: absolute; z-index: 999; pointer-events: none;
            font-size: {{ $player->watermark_font_size ?? 14 }}px;
            color: {{ $player->watermark_color ?? '#ffffff' }};
            opacity: {{ $player->watermark_opacity ?? 0.3 }};
        }
        .watermark.top-left { top: 60px; left: 60px; }
        .watermark.top-right { top: 60px; right: 60px; }
        .watermark.bottom-left { bottom: 60px; left: 60px; }
        .watermark.bottom-right { bottom: 60px; right: 60px; }
        .watermark.center { top: 50%; left: 50%; transform: translate(-50%, -50%); }
        @media (max-width: 768px) {
            .watermark { font-size: {{ intval($player->watermark_font_size * 0.75) ?: 10 }}px; }
            .watermark.top-left, .watermark.top-right,
            .watermark.bottom-left, .watermark.bottom-right { top: auto; bottom: auto; left: auto; right: auto; }
            .watermark.top-left { top: 4%; left: 4%; }
            .watermark.top-right { top: 4%; right: 4%; }
            .watermark.bottom-left { bottom: 4%; left: 4%; }
            .watermark.bottom-right { bottom: 4%; right: 4%; }
        }
        
        /* 播放器Logo */
        .player-logo {
            position: absolute; z-index: 998; pointer-events: none;
            opacity: {{ $player->logo_opacity ?? 0.6 }};
        }
        .player-logo img {
            height: {{ $player->logo_size ?? 48 }}px;
            width: auto;
        }
        .player-logo.top-left { top: 60px; left: 60px; }
        .player-logo.top-right { top: 60px; right: 60px; }
        .player-logo.bottom-left { bottom: 60px; left: 60px; }
        .player-logo.bottom-right { bottom: 60px; right: 60px; }
        @media (max-width: 768px) {
            .player-logo img { height: 32px; }
            .player-logo.top-left, .player-logo.top-right,
            .player-logo.bottom-left, .player-logo.bottom-right { top: auto; bottom: auto; left: auto; right: auto; }
            .player-logo.top-left { top: 4%; left: 4%; }
            .player-logo.top-right { top: 4%; right: 4%; }
            .player-logo.bottom-left { bottom: 4%; left: 4%; }
            .player-logo.bottom-right { bottom: 4%; right: 4%; }
        }
        
        /* ========== 上一集/下一集按钮 ========== */
        .dp-ep-btn {
            display: none; /* 有剧集时才显示 */
            cursor: pointer;
            padding: 0 6px;
            font-size: 13px;
            color: rgba(255,255,255,.7);
            transition: color .2s;
            line-height: 1;
            user-select: none;
        }
        .dp-ep-btn:hover { color: #fff; }
        .dp-ep-btn.has-ep { display: inline-flex; align-items: center; }
        .dp-ep-btn svg { width: 18px; height: 18px; fill: currentColor; }

        /* 隐藏DPlayer默认控制栏 */
        .dplayer-controller { display: none !important; }
        .dplayer-controller-mask { display: none !important; }

        /* ========== 优酷风格控制栏 ========== */
        .yk-controls {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            z-index: 20;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
            padding: 40px 15px 12px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .dplayer:hover .yk-controls,
        .dplayer.paused .yk-controls { opacity: 1; }

        /* 进度条 */
        .yk-progress { position: relative; width: 100%; height: 22px; cursor: pointer; display: flex; align-items: center; }
        .yk-progress-track { position: relative; width: 100%; height: 3px; background: rgba(255,255,255,0.2); border-radius: 2px; transition: height 0.15s; }
        .yk-progress:hover .yk-progress-track { height: 5px; }
        .yk-progress-buffer { position: absolute; top: 0; left: 0; height: 100%; background: rgba(255,255,255,0.3); border-radius: 2px; }
        .yk-progress-played { position: absolute; top: 0; left: 0; height: 100%; background: #ff6b35; border-radius: 2px; display: flex; align-items: center; justify-content: flex-end; }
        .yk-progress-dot { width: 14px; height: 14px; background: #ff6b35; border-radius: 50%; transform: scale(0); transition: transform 0.15s; box-shadow: 0 0 8px rgba(255,107,53,0.6); margin-right: -7px; }
        .yk-progress:hover .yk-progress-dot { transform: scale(1); }
        .yk-progress-tooltip { position: absolute; bottom: 100%; left: 0; transform: translateX(-50%); background: rgba(0,0,0,0.9); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; white-space: nowrap; pointer-events: none; opacity: 0; transition: opacity 0.1s; margin-bottom: 10px; }
        .yk-progress:hover .yk-progress-tooltip { opacity: 1; }

        /* 控制按钮行 */
        .yk-bar { display: flex; align-items: center; height: 40px; }
        .yk-btn { width: 36px; height: 36px; background: none; border: none; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: background 0.2s; padding: 0; flex-shrink: 0; }
        .yk-btn:hover { background: rgba(255,255,255,0.15); }
        .yk-btn svg { width: 22px; height: 22px; fill: currentColor; }
        .yk-btn-play svg { width: 30px; height: 30px; }
        .yk-time { color: rgba(255,255,255,0.9); font-size: 13px; margin: 0 10px; font-variant-numeric: tabular-nums; user-select: none; white-space: nowrap; }
        .yk-volume { display: flex; align-items: center; position: relative; }
        .yk-volume-bar { width: 0; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; overflow: hidden; transition: width 0.2s; cursor: pointer; margin-left: 6px; }
        .yk-volume:hover .yk-volume-bar { width: 70px; }
        .yk-volume-fill { height: 100%; background: #fff; border-radius: 2px; }
        .yk-right { margin-left: auto; display: flex; align-items: center; }
        .yk-speed-text { font-size: 13px; font-weight: 600; color: #fff; }

        /* 迷你进度条（顶部） */
        .yk-mini-bar { position: absolute; top: 0; left: 0; right: 0; height: 2px; z-index: 25; background: transparent; }
        .yk-mini-bar-inner { height: 100%; background: #ff6b35; width: 0; transition: width 0.2s linear; }

        /* 倍速菜单 */
        .yk-speed-menu { display: none; position: absolute; bottom: 55px; right: 60px; background: rgba(20,20,20,0.95); border-radius: 8px; padding: 6px 0; min-width: 70px; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.5); z-index: 30; }
        .yk-speed-item { padding: 6px 16px; font-size: 13px; color: rgba(255,255,255,0.7); cursor: pointer; text-align: center; transition: all 0.15s; }
        .yk-speed-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .yk-speed-item.active { color: #ff6b35; }

        /* Toast */
        .yk-toast { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background: rgba(0,0,0,0.8); color: #fff; padding: 8px 20px; border-radius: 6px; font-size: 14px; z-index: 30; opacity: 0; transition: opacity 0.3s; pointer-events: none; }
        .yk-toast.show { opacity: 1; }

        /* 弹幕控制 */
        .yk-danmaku { display: flex; align-items: center; gap: 4px; margin: 0 8px; }
        .yk-danmaku-toggle { display: flex; align-items: center; gap: 4px; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: background 0.2s; user-select: none; }
        .yk-danmaku-toggle:hover { background: rgba(255,255,255,0.1); }
        .yk-danmaku-toggle svg { color: rgba(255,255,255,0.7); }
        .yk-danmaku-toggle.active svg { color: #ff6b35; }
        .yk-danmaku-text { font-size: 12px; color: rgba(255,255,255,0.7); }
        .yk-danmaku-toggle.active .yk-danmaku-text { color: #ff6b35; }
        .yk-danmaku-color-btn { background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.2s; display: flex; align-items: center; justify-content: center; }
        .yk-danmaku-color-btn:hover { background: rgba(255,255,255,0.1); }
        .yk-danmaku-color-btn svg { color: rgba(255,255,255,0.7); }
        .yk-danmaku-input-wrap { display: flex; align-items: center; flex: 1; max-width: 300px; }
        .yk-danmaku-input { flex: 1; height: 30px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 4px; color: #fff; font-size: 13px; padding: 0 10px; outline: none; transition: border-color 0.2s; }
        .yk-danmaku-input::placeholder { color: rgba(255,255,255,0.4); }
        .yk-danmaku-input:focus { border-color: rgba(255,255,255,0.4); }
        .yk-danmaku-send { height: 30px; padding: 0 14px; background: #ff6b35; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; transition: background 0.2s; flex-shrink: 0; margin-left: 4px; }
        .yk-danmaku-send:hover { background: #e55a2b; }

        /* 弹幕颜色面板 */
        .yk-danmaku-color-panel { display: none; position: absolute; bottom: 55px; left: 50%; transform: translateX(-50%); background: rgba(20,20,20,0.95); border-radius: 8px; padding: 12px; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.5); z-index: 30; }
        .yk-danmaku-color-panel-title { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
        .yk-danmaku-colors { display: flex; gap: 8px; }
        .yk-danmaku-color-item { width: 24px; height: 24px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; transition: all 0.15s; }
        .yk-danmaku-color-item:hover { transform: scale(1.15); }
        .yk-danmaku-color-item.active { border-color: #fff; box-shadow: 0 0 8px rgba(255,255,255,0.4); }

        /* 移动端 */
        @media (max-width: 768px) {
            .yk-controls { padding: 25px 10px 8px; }
            .yk-btn { width: 30px; height: 30px; }
            .yk-btn svg { width: 18px; height: 18px; }
            .yk-btn-play svg { width: 26px; height: 26px; }
            .yk-time { font-size: 12px; margin: 0 6px; }
            
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
            .yk-danmaku-text { display: none; }
            .yk-danmaku-input { height: 26px; font-size: 12px; }
            .yk-danmaku-send { height: 26px; font-size: 11px; padding: 0 10px; }
        }

        /* 跑马灯动画 */
        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* ========== 选集浮窗触发按钮 ========== */
        .ep-trigger {
            position: absolute; top: 50%; right: 12px;
            transform: translateY(-50%);
            width: 44px; height: 110px;
            background: rgba(0,0,0,.65);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 10px;
            cursor: pointer;
            display: none; /* 有选集时才显示 */
            flex-direction: column;
            align-items: center; justify-content: center; gap: 6px;
            z-index: 800;
            backdrop-filter: blur(6px);
            transition: background .2s, transform .2s;
        }
        .ep-trigger:hover {
            background: rgba(0,0,0,.8);
            transform: translateY(-50%) scale(1.05);
        }
        .ep-trigger .icon {
            width: 22px; height: 22px;
            border: 2px solid rgba(255,255,255,.7);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
        }
        .ep-trigger .icon::before {
            content: ''; display: block;
            width: 10px; height: 10px;
            border-right: 2px solid rgba(255,255,255,.7);
            border-bottom: 2px solid rgba(255,255,255,.7);
            transform: rotate(-45deg);
            margin-right: 2px;
        }
        .ep-trigger .label {
            font-size: 11px; color: rgba(255,255,255,.7);
            letter-spacing: 1px; writing-mode: vertical-rl;
        }
        .ep-trigger.has-ep { display: flex; }
        
        /* ========== 选集浮窗面板 ========== */
        .ep-panel {
            position: absolute; top: 0; right: 0;
            width: 320px; height: 100%;
            background: rgba(10,10,10,.88);
            backdrop-filter: blur(12px);
            border-left: 1px solid rgba(255,255,255,.06);
            display: flex; flex-direction: column;
            z-index: 850;
            transform: translateX(100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
        }
        .ep-panel.open {
            transform: translateX(0);
        }
        
        /* 浮窗头部 */
        .ep-panel-header {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .ep-panel-header .left {
            display: flex; align-items: center; gap: 8px;
        }
        .ep-panel-header .title {
            font-size: 15px; font-weight: 600; color: #e8e8e8;
        }
        .ep-panel-header .count {
            font-size: 12px; color: #666;
        }
        .ep-panel-header .close-btn {
            width: 30px; height: 30px;
            background: rgba(255,255,255,.06);
            border: none; border-radius: 6px;
            color: #888; font-size: 18px;
            cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            transition: all .15s;
        }
        .ep-panel-header .close-btn:hover {
            background: rgba(255,255,255,.12); color: #fff;
        }
        
        /* 剧集标签 */
        .ep-series-tabs {
            display: flex; gap: 6px; padding: 10px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0; overflow-x: auto;
        }
        .ep-series-tabs::-webkit-scrollbar { display: none; }
        .ep-stab {
            padding: 5px 14px; border-radius: 6px; font-size: 12px;
            background: rgba(255,255,255,.06); color: #aaa;
            cursor: pointer; white-space: nowrap; transition: all .15s;
        }
        .ep-stab:hover { background: rgba(255,255,255,.1); color: #fff; }
        .ep-stab.active { background: {{ $player->theme_color ?? '#ff6b00' }}; color: #fff; }
        
        /* 集数列表 */
        .ep-scroll {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            padding: 12px 16px;
        }
        .ep-scroll::-webkit-scrollbar { width: 5px; }
        .ep-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }
        .ep-scroll::-webkit-scrollbar-track { background: transparent; }
        
        .ep-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 6px;
        }
        .ep-item {
            height: 36px; border: 1px solid rgba(255,255,255,.08);
            border-radius: 6px; background: rgba(255,255,255,.03);
            color: #bbb; font-size: 12px; font-weight: 500;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center; transition: all .15s;
            white-space: nowrap; overflow: hidden;
        }
        .ep-item:hover {
            background: rgba(255,255,255,.1); color: #fff;
            border-color: rgba(255,255,255,.2);
        }
        .ep-item.active {
            background: {{ $player->theme_color ?? '#ff6b00' }};
            color: #fff; border-color: {{ $player->theme_color ?? '#ff6b00' }};
            font-weight: 700;
            box-shadow: 0 0 10px {{ $player->theme_color ?? '#ff6b00' }}33;
        }
        
        /* 移动端：浮窗变底部 */
        @media (max-width: 768px) {
            .ep-trigger {
                right: auto; left: 50%; top: auto; bottom: 12px;
                transform: translateX(-50%);
                width: 100px; height: 38px;
                flex-direction: row;
                border-radius: 10px;
            }
            .ep-trigger:hover { transform: translateX(-50%) scale(1.05); }
            .ep-trigger .label {
                writing-mode: horizontal-tb; font-size: 12px;
            }
            .ep-trigger .icon::before {
                transform: rotate(45deg); margin: 0;
            }
            
            .ep-panel {
                width: 100%; height: 55%;
                top: auto; bottom: 0; right: 0;
                border-left: none; border-top: 1px solid rgba(255,255,255,.08);
                border-radius: 16px 16px 0 0;
                transform: translateY(100%);
            }
            .ep-panel.open { transform: translateY(0); }
            
            .ep-grid { grid-template-columns: repeat(auto-fill, minmax(44px, 1fr)); gap: 4px; }
            .ep-item { height: 32px; font-size: 11px; }
        }
        /* ========== 进度条图标动画 ========== */
        @keyframes dp-walk{0%,100%{transform:rotate(0deg) translateY(0)}25%{transform:rotate(-8deg) translateY(-3px)}50%{transform:rotate(0deg) translateY(0)}75%{transform:rotate(8deg) translateY(-3px)}}
        @keyframes dp-run{0%,100%{transform:translateY(0) scaleX(1)}25%{transform:translateY(-6px) scaleX(1.05)}50%{transform:translateY(0) scaleX(1)}75%{transform:translateY(-4px) scaleX(0.95)}}
        @keyframes dp-drive{0%,100%{transform:translateX(0) rotate(0deg)}25%{transform:translateX(2px) rotate(2deg)}50%{transform:translateX(0) rotate(0deg)}75%{transform:translateX(-2px) rotate(-2deg)}}
        @keyframes dp-train{0%{transform:translateX(-2px)}50%{transform:translateX(2px)}100%{transform:translateX(-2px)}}
        @keyframes dp-fly{0%,100%{transform:translateY(0) rotate(-5deg)}50%{transform:translateY(-8px) rotate(5deg)}}
        @keyframes dp-bounce{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-8px) scale(1.1)}}
        @keyframes dp-spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
        @keyframes dp-pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.3);opacity:0.8}}
        @keyframes dp-shake{0%,100%{transform:rotate(0deg)}20%{transform:rotate(-15deg)}40%{transform:rotate(15deg)}60%{transform:rotate(-10deg)}80%{transform:rotate(10deg)}}
        @keyframes dp-wobble{0%,100%{transform:translateX(0) rotate(0)}25%{transform:translateX(-4px) rotate(-10deg)}50%{transform:translateX(0) rotate(0)}75%{transform:translateX(4px) rotate(10deg)}}
        @keyframes dp-pop{0%{transform:scale(0.5);opacity:0}50%{transform:scale(1.2);opacity:1}100%{transform:scale(1);opacity:1}}
        @keyframes dp-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        @keyframes dp-yum{0%,100%{transform:rotate(0deg) scale(1)}25%{transform:rotate(-10deg) scale(1.1)}75%{transform:rotate(10deg) scale(1.1)}}
        @keyframes dp-wave{0%,100%{transform:rotate(0deg)}25%{transform:rotate(20deg)}75%{transform:rotate(-20deg)}}
        @keyframes dp-game{0%,100%{transform:translateY(0) rotate(0)}25%{transform:translateY(-4px) rotate(-5deg)}50%{transform:translateY(0) rotate(0)}75%{transform:translateY(-4px) rotate(5deg)}}
        .dp-icon-walk{animation:dp-walk 0.6s ease-in-out infinite}
        .dp-icon-run{animation:dp-run 0.4s ease-in-out infinite}
        .dp-icon-drive{animation:dp-drive 0.3s ease-in-out infinite}
        .dp-icon-train{animation:dp-train 0.15s linear infinite}
        .dp-icon-fly{animation:dp-fly 0.8s ease-in-out infinite}
        .dp-icon-bounce{animation:dp-bounce 0.6s ease-in-out infinite}
        .dp-icon-spin{animation:dp-spin 2s linear infinite}
        .dp-icon-pulse{animation:dp-pulse 0.8s ease-in-out infinite}
        .dp-icon-shake{animation:dp-shake 0.5s ease-in-out infinite}
        .dp-icon-wobble{animation:dp-wobble 0.6s ease-in-out infinite}
        .dp-icon-pop{animation:dp-pop 0.4s ease-out}
        .dp-icon-float{animation:dp-float 1.5s ease-in-out infinite}
        .dp-icon-yum{animation:dp-yum 0.5s ease-in-out infinite}
        .dp-icon-wave{animation:dp-wave 0.4s ease-in-out infinite}
        .dp-icon-game{animation:dp-game 0.6s ease-in-out infinite}
    </style>
</head>
<body>
    <div class="player-wrap">
        <div id="dplayer"></div>
        @if($player->watermark_text)
        <div class="watermark {{ $player->watermark_position ?? 'custom' }}" id="watermarkEl">{{ $player->watermark_text }}</div>
        @endif
        @if($player->logo_url)
        <div class="player-logo {{ $player->logo_position ?? 'top-left' }}">
            <img src="{{ $player->logo_url }}" alt="logo" onerror="this.style.display='none'">
        </div>
        @endif
        
        <!-- 选集触发按钮（悬浮在播放器右侧） -->
        <div class="ep-trigger" id="epTrigger" onclick="togglePanel()">
            <div class="icon"></div>
            <span class="label">选集</span>
        </div>
        
        <!-- 选集浮窗面板 -->
        <div class="ep-panel" id="epPanel">
            <div class="ep-panel-header">
                <div class="left">
                    <span class="title">选集</span>
                    <span class="count" id="epCount"></span>
                </div>
                <button class="close-btn" onclick="togglePanel()">✕</button>
            </div>
            <div class="ep-series-tabs" id="epTabs"></div>
            <div class="ep-scroll" id="epScroll">
                <div class="ep-grid" id="epGrid"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js"></script>
    <script src="/js/media-engine.js"></script>
    <script>
        const PLAYER_ID = {{ $player->id }};
        const PLAYER_CODE = '{{ $player->player_code }}';
        const THEME = '{{ $player->theme_color ?? "#ff6b00" }}';
        
        let dp = null, allSeries = [], allEpisodes = [], currentSeriesId = null, currentVideoId = null;
        let panelOpen = false;
        
        const urlParams = new URLSearchParams(window.location.search);
        const directUrl = urlParams.get('url');
        const noAd = urlParams.get('no_ad') === '1';
        
        // ========== 初始化播放器 ==========
        const playlist = <?php
            echo $player->videos->map(function($v) {
                return [
                    'id' => $v->id,
                    'title' => $v->title,
                    'url' => $v->url,
                    'cover' => $v->cover,
                    'series_id' => $v->series_id,
                    'episode_number' => $v->episode_number,
                ];
            })->values()->toJson();
        ?>;
        
        const firstVideo = playlist.length > 0 ? playlist[0] : null;
        const videoUrl = directUrl || (firstVideo ? firstVideo.url : '');
        const videoPic = firstVideo ? (firstVideo.cover || '') : '';
        
        // 没有视频源时的处理
        const isNoAd = {{ $player->version === 'flagship' || $player->version === 'advanced' ? 'true' : 'false' }};
        const bgUrl = '{{ $player->background_image ?? '' }}';
        const bgUrlMobile = '{{ $player->background_image_mobile ?? '' }}';
        const container = document.getElementById('dplayer');
        
        // 检测是否为移动端
        const isMobile = window.innerWidth <= 768;
        
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
        
        dp = new DPlayer({
            container: container,
            theme: THEME,
            autoplay: {{ $player->autoplay ? 'true' : 'false' }},
            loop: {{ $player->loop_play ? 'true' : 'false' }},
            screenshot: true,
            hotkey: true,
            preload: 'auto',
            volume: 0.7,
            mutex: true,
            video: { url: videoUrl, pic: videoPic, type: 'auto' },
            danmaku: {{ $player->show_danmaku ? '{}' : 'false' }},
            contextmenu: [{ text: '{{ $player->name }}', link: window.location.href }]
        });
        


        // ========== 全屏兼容：把水印和Logo移入播放器容器 ==========
        (function() {
            const dpContainer = document.querySelector('.dplayer');
            if (!dpContainer) return;
            
            // 无视频源时：隐藏DPlayer的video元素，让背景图显示
            if (!videoUrl) {
                const dpVideoWrap = dpContainer.querySelector('.dplayer-video-wrap');
                if (dpVideoWrap) dpVideoWrap.style.display = 'none';
            }
            
            // 移动水印
            const wm = document.getElementById('watermarkEl');
            if (wm) { dpContainer.appendChild(wm); wm.style.position = 'absolute'; }
            // 移动Logo
            const logo = document.querySelector('.player-logo');
            if (logo) { dpContainer.appendChild(logo); logo.style.position = 'absolute'; }
        })();

        // ========== 记忆播放 ==========
        const resumeKey = 'dp_resume_' + PLAYER_ID;
        let resumeTime = 0;
        let resumeChecked = false;
        
        // 从localStorage读取记忆位置
        try {
            const saved = localStorage.getItem(resumeKey);
            if (saved) {
                const data = JSON.parse(saved);
                // 7天内有效
                if (data.time > 5 && data.url === videoUrl && Date.now() - data.ts < 7*24*60*60*1000) {
                    resumeTime = data.time;
                }
            }
        } catch(e) {}
        
        // 定期保存播放进度（每5秒）
        let saveTimer = null;
        dp.on('timeupdate', function() {
            if (!saveTimer) {
                saveTimer = setTimeout(function() {
                    saveTimer = null;
                    if (dp.video.currentTime > 5) {
                        try {
                            localStorage.setItem(resumeKey, JSON.stringify({
                                time: Math.floor(dp.video.currentTime),
                                url: videoUrl,
                                ts: Date.now()
                            }));
                        } catch(e) {}
                    }
                }, 5000);
            }
        });
        
        // 播放开始后恢复进度
        dp.on('playing', function() {
            if (resumeChecked) return;
            resumeChecked = true;
            if (resumeTime > 0) {
                // 显示恢复提示
                const notice = document.createElement('div');
                notice.style.cssText = 'position:absolute;bottom:60px;left:50%;transform:translateX(-50%);z-index:9999;background:rgba(0,0,0,.85);color:#fff;padding:10px 20px;border-radius:8px;font-size:14px;display:flex;align-items:center;gap:12px;backdrop-filter:blur(6px);box-shadow:0 4px 16px rgba(0,0,0,.4);';
                
                const timeStr = Math.floor(resumeTime/60) + ':' + String(Math.floor(resumeTime%60)).padStart(2,'0');
                notice.innerHTML = '<span>上次播放到 ' + timeStr + '</span>';
                
                const resumeBtn = document.createElement('button');
                resumeBtn.textContent = '继续播放';
                resumeBtn.style.cssText = 'background:#00a1d6;color:#fff;border:none;padding:4px 14px;border-radius:4px;cursor:pointer;font-size:13px;';
                resumeBtn.onclick = function() {
                    dp.seek(resumeTime);
                    notice.remove();
                };
                
                const dismissBtn = document.createElement('button');
                dismissBtn.textContent = '✕';
                dismissBtn.style.cssText = 'background:none;color:rgba(255,255,255,.6);border:none;cursor:pointer;font-size:16px;padding:0 4px;';
                dismissBtn.onclick = function() { notice.remove(); };
                
                notice.appendChild(resumeBtn);
                notice.appendChild(dismissBtn);
                
                const container = document.getElementById('dplayer');
                container.appendChild(notice);
                
                // 8秒后自动消失
                setTimeout(function() { if (notice.parentNode) notice.remove(); }, 8000);
            }
        });
        
        // 播放结束清除记忆
        dp.on('ended', function() {
            try { localStorage.removeItem(resumeKey); } catch(e) {}
        });
        
        // ========== 快捷键增强 ==========
        // DPlayer自带hotkey已开启，这里补充额外快捷键
        document.addEventListener('keydown', function(e) {
            // 排除输入框
            const tag = e.target.tagName;
            if (tag === 'INPUT' || tag === 'TEXTAREA' || e.target.isContentEditable) return;
            
            // F键：全屏切换
            if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                dp.fullScreen.toggle();
            }
            // M键：静音切换
            if (e.key === 'm' || e.key === 'M') {
                e.preventDefault();
                dp.toggle();
                dp.video.muted = !dp.video.muted;
                dp.notice(dp.video.muted ? '已静音' : '已开启声音', 1500);
            }
            // 左方向键：快退5秒（DPlayer默认10秒，改为5秒更精细）
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                dp.seek(Math.max(0, dp.video.currentTime - 5));
            }
            // 右方向键：快进5秒
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                dp.seek(dp.video.currentTime + 5);
            }
            // 上方向键：音量+
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                dp.volume(Math.min(1, dp.video.volume + 0.1));
            }
            // 下方向键：音量-
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                dp.volume(Math.max(0, dp.video.volume - 0.1));
            }
            // 空格：播放/暂停（DPlayer已处理，这里不重复）
            // N键：下一集
            if (e.key === 'n' || e.key === 'N') {
                e.preventDefault();
                playNextEp();
            }
            // P键：上一集
            if (e.key === 'p' || e.key === 'P') {
                e.preventDefault();
                playPrevEp();
            }
        });
        
        // ========== 优酷风格控制栏 ==========
        (function() {
            const dpContainer = document.querySelector('.dplayer');
            if (!dpContainer) return;

            // 隐藏默认控制栏
            const defCtrl = dpContainer.querySelector('.dplayer-controller');
            const defMask = dpContainer.querySelector('.dplayer-controller-mask');
            if (defCtrl) defCtrl.style.display = 'none';
            if (defMask) defMask.style.display = 'none';

            // 创建控制栏
            const controls = document.createElement('div');
            controls.className = 'yk-controls';
            controls.innerHTML = `
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

                <!-- 控制按钮 -->
                <div class="yk-bar">
                    <!-- 上一集 -->
                    <button class="yk-btn" id="ykPrev" title="上一集">
                        <svg viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                    </button>

                    <!-- 播放 -->
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

                    <!-- 时间 -->
                    <span class="yk-time">
                        <span id="ykCurTime">00:00</span> / <span id="ykDuration">00:00</span>
                    </span>

                    <!-- 弹幕控制 -->
                    <div class="yk-danmaku">
                        <div class="yk-danmaku-toggle" id="ykDanmakuToggle" title="弹幕开关">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M20 2H4C2.9 2 2 2.9 2 4v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H4V4h16v12z" fill="currentColor"/>
                                <rect x="5" y="7" width="14" height="1.5" rx="0.75" fill="currentColor"/>
                                <rect x="5" y="10.5" width="10" height="1.5" rx="0.75" fill="currentColor"/>
                                <rect x="5" y="14" width="7" height="1.5" rx="0.75" fill="currentColor"/>
                            </svg>
                            <span class="yk-danmaku-text">弹幕</span>
                        </div>
                        <button class="yk-danmaku-color-btn" id="ykDanmakuColorBtn" title="弹幕颜色">
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-1 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- 弹幕输入框 -->
                    <div class="yk-danmaku-input-wrap">
                        <input type="text" class="yk-danmaku-input" id="ykDanmakuInput" placeholder="发个友善的弹幕见证当下" maxlength="50">
                        <button class="yk-danmaku-send" id="ykDanmakuSend">发送</button>
                    </div>

                    <!-- 右侧 -->
                    <div class="yk-right">
                        <button class="yk-btn" id="ykSpeedBtn" title="倍速">
                            <span class="yk-speed-text">1x</span>
                        </button>
                        <button class="yk-btn" id="ykPip" title="画中画">
                            <svg viewBox="0 0 24 24"><path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/></svg>
                        </button>
                        <button class="yk-btn" id="ykWebFull" title="网页全屏">
                            <svg viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                        </button>
                        <button class="yk-btn" id="ykFull" title="全屏">
                            <svg viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                        </button>
                    </div>
                </div>
            `;
            dpContainer.appendChild(controls);

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
                {name:'白色',color:'#ffffff'},{name:'红色',color:'#ff4444'},
                {name:'橙色',color:'#ff6b35'},{name:'黄色',color:'#ffcc00'},
                {name:'绿色',color:'#00cc66'},{name:'蓝色',color:'#00aaff'},
                {name:'紫色',color:'#aa66ff'},{name:'粉色',color:'#ff66aa'}
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

            // ========== 控制栏逻辑 ==========
            let currentSpeed = 1;
            let danmakuColor = '#ffffff';
            let danmakuVisible = true;
            let isDragging = false;

            function showToast(msg) {
                const t = document.getElementById('ykToast');
                t.textContent = msg;
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 1500);
            }

            function formatTime(sec) {
                if (isNaN(sec)) return '00:00';
                const m = Math.floor(sec / 60);
                const s = Math.floor(sec % 60);
                return String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            }

            // 播放/暂停
            document.getElementById('ykPlay').onclick = function() {
                dp.toggle();
            };
            dp.on('play', function() {
                document.getElementById('ykPlay').innerHTML = '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
            });
            dp.on('pause', function() {
                document.getElementById('ykPlay').innerHTML = '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
            });

            // 上一集/下一集
            document.getElementById('ykPrev').onclick = function() { if (typeof playPrevEp === 'function') playPrevEp(); };
            document.getElementById('ykNext').onclick = function() { if (typeof playNextEp === 'function') playNextEp(); };

            // 音量
            document.getElementById('ykVolBtn').onclick = function() {
                dp.video.muted = !dp.video.muted;
                updateVolumeIcon();
            };
            function updateVolumeIcon() {
                const icon = document.getElementById('ykVolIcon');
                if (dp.video.muted || dp.video.volume === 0) {
                    icon.innerHTML = '<path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>';
                } else {
                    icon.innerHTML = '<path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>';
                }
                document.getElementById('ykVolFill').style.width = (dp.video.muted ? 0 : dp.video.volume * 100) + '%';
            }
            // 音量条拖动
            document.getElementById('ykVolBar').onclick = function(e) {
                const rect = this.getBoundingClientRect();
                const ratio = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                dp.volume(ratio);
                document.getElementById('ykVolFill').style.width = (ratio * 100) + '%';
            };

            // 时间更新
            dp.on('timeupdate', function() {
                if (isDragging) return;
                const cur = dp.video.currentTime;
                const dur = dp.video.duration;
                document.getElementById('ykCurTime').textContent = formatTime(cur);
                document.getElementById('ykDuration').textContent = formatTime(dur);
                if (dur) {
                    const pct = (cur / dur) * 100;
                    document.getElementById('ykPlayed').style.width = pct + '%';
                    document.getElementById('ykMiniInner').style.width = pct + '%';
                }
            });
            dp.on('loadedmetadata', function() {
                document.getElementById('ykDuration').textContent = formatTime(dp.video.duration);
            });

            // 缓冲进度
            dp.on('progress', function() {
                if (dp.video.buffered.length > 0) {
                    const buf = dp.video.buffered.end(dp.video.buffered.length - 1);
                    const dur = dp.video.duration;
                    if (dur) document.getElementById('ykBuffer').style.width = (buf / dur * 100) + '%';
                }
            });

            // 进度条拖动
            const progressEl = document.getElementById('ykProgress');
            progressEl.onmousedown = function(e) {
                isDragging = true;
                seekTo(e);
            };
            document.addEventListener('mousemove', function(e) {
                if (isDragging) seekTo(e);
            });
            document.addEventListener('mouseup', function() { isDragging = false; });
            function seekTo(e) {
                const rect = progressEl.getBoundingClientRect();
                const ratio = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                document.getElementById('ykPlayed').style.width = (ratio * 100) + '%';
                if (dp.video.duration) dp.seek(ratio * dp.video.duration);
            }
            // 预览时间
            progressEl.onmousemove = function(e) {
                const rect = progressEl.getBoundingClientRect();
                const ratio = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                const tooltip = document.getElementById('ykTooltip');
                tooltip.textContent = formatTime(ratio * dp.video.duration);
                tooltip.style.left = (ratio * 100) + '%';
            };

            // 倍速
            document.getElementById('ykSpeedBtn').onclick = function(e) {
                e.stopPropagation();
                const menu = document.getElementById('ykSpeedMenu');
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            };
            document.querySelectorAll('.yk-speed-item').forEach(function(item) {
                item.onclick = function(e) {
                    e.stopPropagation();
                    currentSpeed = parseFloat(this.dataset.speed);
                    dp.video.playbackRate = currentSpeed;
                    document.getElementById('ykSpeedBtn').querySelector('.yk-speed-text').textContent = currentSpeed + 'x';
                    document.querySelectorAll('.yk-speed-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('ykSpeedMenu').style.display = 'none';
                    showToast('倍速: ' + currentSpeed + 'x');
                };
            });
            document.addEventListener('click', function() {
                document.getElementById('ykSpeedMenu').style.display = 'none';
            });

            // 画中画
            document.getElementById('ykPip').onclick = function() {
                if (document.pictureInPictureElement) {
                    document.exitPictureInPicture();
                } else if (dp.video.requestPictureInPicture) {
                    dp.video.requestPictureInPicture();
                }
            };

            // 网页全屏
            document.getElementById('ykWebFull').onclick = function() {
                dpContainer.classList.toggle('dplayer-fulled');
                document.body.style.overflow = dpContainer.classList.contains('dplayer-fulled') ? 'hidden' : '';
            };

            // 全屏
            document.getElementById('ykFull').onclick = function() {
                dp.fullScreen.toggle();
            };

            // 弹幕开关
            document.getElementById('ykDanmakuToggle').onclick = function() {
                danmakuVisible = !danmakuVisible;
                this.classList.toggle('active', danmakuVisible);
                if (dp.danmaku) {
                    danmakuVisible ? dp.danmaku.show() : dp.danmaku.hide();
                }
                showToast(danmakuVisible ? '弹幕开启' : '弹幕关闭');
            };

            // 弹幕颜色
            document.getElementById('ykDanmakuColorBtn').onclick = function(e) {
                e.stopPropagation();
                const panel = document.getElementById('ykDanmakuColorPanel');
                panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
            };
            document.querySelectorAll('.yk-danmaku-color-item').forEach(function(item) {
                item.onclick = function(e) {
                    e.stopPropagation();
                    danmakuColor = this.dataset.color;
                    document.querySelectorAll('.yk-danmaku-color-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('ykDanmakuColorPanel').style.display = 'none';
                    showToast('弹幕颜色已更改');
                };
            });
            document.addEventListener('click', function() {
                document.getElementById('ykDanmakuColorPanel').style.display = 'none';
            });

            // 发送弹幕
            function sendDanmaku() {
                const input = document.getElementById('ykDanmakuInput');
                const text = input.value.trim();
                if (!text) return;
                if (dp.danmaku) {
                    dp.danmaku.draw({ text: text, color: danmakuColor, type: 'right' });
                }
                input.value = '';
            }
            document.getElementById('ykDanmakuSend').onclick = sendDanmaku;
            document.getElementById('ykDanmakuInput').onkeydown = function(e) {
                if (e.key === 'Enter') { e.preventDefault(); sendDanmaku(); }
            };
            document.getElementById('ykDanmakuInput').onclick = function(e) { e.stopPropagation(); };

            // 初始化音量图标
            updateVolumeIcon();
        })();
        
        // ========== 进度条跟随图标 ==========
        function injectProgressIcon() {
            const barWrap = document.querySelector('.dplayer-bar-wrap');
            if (!barWrap || document.querySelector('.dp-progress-icon')) return;
            
            // 创建跟随图标
            const icon = document.createElement('div');
            icon.className = 'dp-progress-icon';
            icon.style.cssText = `
                position:absolute;bottom:100%;left:0;
                width:32px;height:32px;
                transform:translateX(-50%) translateY(-4px);
                z-index:10;pointer-events:none;
                display:none;
                transition:none;
            `;
            icon.innerHTML = `<img src="" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5));" />`;
            barWrap.style.position = 'relative';
            barWrap.appendChild(icon);
            
            // 监听播放进度更新图标位置
            dp.on('timeupdate', function() {
                if (!dp.video.duration) return;
                const percent = dp.video.currentTime / dp.video.duration;
                icon.style.left = (percent * 100) + '%';
            });
        }
        
        // 延迟注入，等待播放器渲染完成
        setTimeout(function() {
            injectProgressIcon();
            // 如果配置了进度条图标则显示
            @if($player->progress_icon_url)
            const iconContainer = document.querySelector('.dp-progress-icon');
            const iconUrl = '{{ $player->progress_icon_url }}';
            if (iconContainer) {
                // 判断是emoji还是图片URL
                if (iconUrl.startsWith('http') || iconUrl.startsWith('/')) {
                    iconContainer.innerHTML = '<img src="' + iconUrl + '" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5));" />';
                } else {
                    // emoji - 根据图标类型应用不同动画
                    const animMap = {
                        'walk': ['🚶','🚶‍♂️','🚶‍♀️'],
                        'run': ['🏃','🏃‍♂️','🏃‍♀️','🚴','🚴‍♂️','🚴‍♀️','🛴','⛸️','🏄','🏊','🤸','💃','🕺'],
                        'drive': ['🚗','🚕','🚙','🏎️','🚌','🚎','🏍️','🛵','🚜'],
                        'train': ['🚂','🚄','🚅'],
                        'fly': ['🚀','✈️','🚁','🛸'],
                        'bounce': ['🐕','🐈','🐈‍⬛','🐰','🐼','🐨','🦊','🐿️','🐧','🦔','🐝','🦋','🐞','🐸','🐭','🐹','🐻','🦁','🐯','🐮','🐷','🐵','🦄','🐲','🦕','🐢','🦆'],
                        'spin': ['👾','🤖','🌀','💎','🔮','🪩'],
                        'pulse': ['⚡','🌟','🔥','💫','✨','🌈','☄️','💥','🔔','🎵','🎶','💝','💖','💗','❤️‍🔥','🫧'],
                        'shake': ['🥊','🥋','⚔️','🗡️','🛡️','🏹','🪓','🔱','⚽','🏀','⚾','🎾','🏐','🎱','🏓','🏸','🎯','🥅'],
                        'wobble': ['🤡','👻','💀','☠️','👽','🤪','😎','🥸','🤩','💩','🤠','🧐'],
                        'pop': ['🎄','🎃','🧧','🎆','🎈','🎁','🎊','🏮','🎐','🧨','🎀','🎗️','🏅','🥇','🏆','🎖️'],
                        'float': ['🧙','🧙‍♂️','🧙‍♀️','🧚','🧜','🧝','🧛','🧟','🥷','🦸','🦸‍♂️','🦸‍♀️','🦹','🦹‍♂️','🦹‍♀️','🎅','🤶','👸','🤴'],
                        'yum': ['🍔','🍕','🍟','🌭','🍿','🧁','🍩','🍪','🍦','☕','🧋','🥤','🍺','🍻','🍷','🧃','🍉','🍓'],
                        'wave': ['👋','🤙','✌️','🤟','👍','🤌','💪','🦵'],
                        'game': ['🎮','🕹️','🎲','♟️','🃏','🎰','🧩','🪄','🧿'],
                    };
                    let animClass = 'dp-icon-bounce';
                    for(const [cls, emojis] of Object.entries(animMap)){
                        if(emojis.includes(iconUrl)){ animClass = 'dp-icon-'+cls; break; }
                    }
                    iconContainer.innerHTML = '<span class="'+animClass+'" style="font-size:26px;line-height:1;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5));display:inline-block;">' + iconUrl + '</span>';
                }
                iconContainer.style.display = 'block';
            }
            @endif
        }, 800);

        // 自动播放静音
        {{ $player->autoplay ? "
        dp.video.muted = true;
        dp.video.autoplay = true;
        setTimeout(function() { dp.video.muted = true; dp.play().catch(function(){}); }, 300);
        " : '' }}
        let hasUnmuted = false;
        dp.on('click', function() {
            if (!hasUnmuted) { hasUnmuted = true; dp.video.muted = false; dp.notice('已开启声音', 2000); }
        });
        
        // 切换按钮显示状态
        function updateEpisodeButtons() {
            // 优酷版控制栏已内置上一集/下一集按钮，此函数保留兼容
        }
        
        // 上一集
        function playPrevEp() {
            if (allEpisodes.length <= 1) return;
            const idx = allEpisodes.findIndex(v => v.id === currentVideoId);
            const prevIdx = idx > 0 ? idx - 1 : allEpisodes.length - 1;
            const ep = allEpisodes[prevIdx];
            if (ep) {
                currentVideoId = ep.id;
                dp.switchVideo({ url: ep.url || ep.video_url });
                dp.play();
                highlightCurrent();
                if (adEngine && showAds && effectiveAdMode !== 'none' && !noAd) {
                    setTimeout(function() { adEngine.playPreRoll(); }, 300);
                }
            }
        }
        
        // 下一集
        function playNextEp() {
            if (allEpisodes.length <= 1) return;
            const idx = allEpisodes.findIndex(v => v.id === currentVideoId);
            const nextIdx = idx < allEpisodes.length - 1 ? idx + 1 : 0;
            const ep = allEpisodes[nextIdx];
            if (ep) {
                currentVideoId = ep.id;
                dp.switchVideo({ url: ep.url || ep.video_url });
                dp.play();
                highlightCurrent();
                if (adEngine && showAds && effectiveAdMode !== 'none' && !noAd) {
                    setTimeout(function() { adEngine.playPreRoll(); }, 300);
                }
            }
        }
        
        // 广告系统
        const showAds = {{ $player->show_ads ? 'true' : 'false' }};
        const adMode = '{{ $player->ad_mode }}';
        const hasAdModule = {{ ($player->hasAdModule() ?? false) ? 'true' : 'false' }};
        
        // 用户素材广告列表
        const userAdsRaw = @json($player->enabledAds);
        // 字段映射：用户广告 title→brand_name, logo_url→brand_logo
        const userAds = userAdsRaw.map(ad => ({
            ...ad,
            brand_name: ad.brand_name || ad.title || '',
            brand_logo: ad.brand_logo || ad.logo_url || '',
        }));
        
        // 确定广告模式：没广告模块→只能平台广告；选了用户/混合但没素材→兜底平台广告
        let effectiveAdMode = adMode;
        if (!hasAdModule && (adMode === 'user' || adMode === 'mixed')) {
            effectiveAdMode = 'platform'; // 没广告模块，强制平台广告
        } else if ((adMode === 'user' || adMode === 'mixed') && userAds.length === 0) {
            effectiveAdMode = 'platform'; // 选了用户广告但没素材，兜底平台广告
            console.log('[广告] 用户广告为空，兜底展示平台广告');
        }
        
        // 广告引擎实例（切换集数时需要触发广告）
        let adEngine = null;
        
        if (showAds && effectiveAdMode !== 'none' && !noAd) {
            try {
                adEngine = new MediaManager(dp, {
                    enabled: true, ads: userAds,
                    adMode: effectiveAdMode, playerId: PLAYER_ID,
                    decorationId: '{{ $player->ad_decoration_id }}',
                    prerrollDuration: {{ $player->preroll_duration ?? 0 }},
                    midrollDuration: {{ $player->midroll_duration ?? 0 }},
                    postrollDuration: {{ $player->postroll_duration ?? 0 }}
                });
                {{ $player->autoplay ? 'if (adEngine.promoVideo) adEngine.promoVideo.muted = true;' : '' }}
                adEngine.onReady = function() {
                    if (effectiveAdMode === 'none') {
                        // 无广告模式：只播开屏，跳过贴片
                        adEngine.playSplash();
                    } else {
                        adEngine.playSplash().then(function() { adEngine.playPreRoll(); });
                    }
                };
                dp.on('timeupdate', function() { if (adEngine && effectiveAdMode !== 'none') adEngine.checkMidRoll(dp.video.currentTime); });
                dp.on('pause', function() { if (adEngine && effectiveAdMode !== 'none' && !adEngine.isAdPlaying) adEngine.playPausePromo(); });
                dp.on('play', function() { if (adEngine) adEngine.endPausePromo(); });
            } catch(e) { console.warn('广告引擎加载失败:', e); }
        }
        
        // 跑马灯 - 独立于广告引擎，即使无广告模式也能显示
        @if($player->show_marquee && $player->marquee_text)
        (function() {
            var marqueeSpeed = {{ $player->marquee_speed ?? 12 }};
            var marqueeColor = '{{ $player->marquee_color ?? "#ffffff" }}';
            setTimeout(function() {
                if (adEngine && adEngine.showMarquee) {
                    // 有广告引擎，用引擎的showMarquee
                    adEngine.showMarquee({
                        type: 'marquee',
                        text_content: '{{ addslashes($player->marquee_text) }}',
                        text_color: marqueeColor,
                        speed: marqueeSpeed,
                        enabled: true
                    }, true);
                } else {
                    // 无广告引擎，直接创建跑马灯
                    var container = document.getElementById('dplayer');
                    if (!container) return;
                    var marquee = document.createElement('div');
                    marquee.className = 'media-marquee';
                    marquee.style.cssText = 'position:absolute;top:20%;left:0;right:0;z-index:150;overflow:hidden;background:transparent;padding:8px 0;cursor:pointer;animation:fadeIn 0.3s ease;';
                    var textEl = document.createElement('div');
                    textEl.style.cssText = 'white-space:nowrap;display:inline-block;padding-left:100%;animation:marqueeScroll ' + marqueeSpeed + 's linear infinite;font-size:15px;color:' + marqueeColor + ';text-shadow:1px 1px 3px rgba(0,0,0,0.7),0 0 8px rgba(0,0,0,0.4);font-weight:500;';
                    var txt = '{{ addslashes($player->marquee_text) }}';
                    textEl.textContent = txt + '　　　　' + txt;
                    marquee.appendChild(textEl);
                    container.appendChild(marquee);
                }
            }, 3000);
        })();
        @endif
        
        dp.on('play', function() { fetch('/api/player/' + PLAYER_ID + '/view', { method: 'POST' }).catch(function(){}); });
        
        // ========== 浮窗控制 ==========
        
        function togglePanel() {
            panelOpen = !panelOpen;
            document.getElementById('epPanel').classList.toggle('open', panelOpen);
        }
        
        // 点击浮窗外关闭
        document.addEventListener('click', function(e) {
            if (!panelOpen) return;
            const panel = document.getElementById('epPanel');
            const trigger = document.getElementById('epTrigger');
            if (panel.contains(e.target) || trigger.contains(e.target)) return;
            togglePanel();
        });
        
        // ========== 选集功能 ==========
        
        async function loadEpisodes() {
            try {
                const res = await fetch('/api/player/' + PLAYER_CODE + '/videos');
                const data = await res.json();
                allEpisodes = data.data || [];
                
                const res2 = await fetch('/api/player/' + PLAYER_CODE + '/series');
                const data2 = await res2.json();
                allSeries = data2.data || [];
                
                if (allEpisodes.length === 0 && playlist.length > 0) {
                    allEpisodes = playlist;
                }
                
                if (allEpisodes.length === 0) return;
                
                // 显示触发按钮
                document.getElementById('epTrigger').classList.add('has-ep');
                
                renderUI();
                
                if (firstVideo) {
                    currentVideoId = firstVideo.id;
                    highlightCurrent();
                }
                
            } catch (e) {
                console.error('加载选集失败:', e);
                if (playlist.length > 0) {
                    allEpisodes = playlist;
                    document.getElementById('epTrigger').classList.add('has-ep');
                    renderUI();
                    currentVideoId = playlist[0].id;
                    highlightCurrent();
                }
            }
        }
        
        function renderUI() {
            document.getElementById('epCount').textContent = '(' + allEpisodes.length + '集)';
            
            // 剧集标签
            const tabsEl = document.getElementById('epTabs');
            const seriesIds = [...new Set(allEpisodes.map(v => v.series_id).filter(Boolean))];
            
            if (seriesIds.length > 1) {
                let html = '<div class="ep-stab active" data-sid="all" onclick="switchSeries(null)">全部</div>';
                seriesIds.forEach((sid, i) => {
                    const s = allSeries.find(x => x.id === sid);
                    const name = s ? s.title : ('剧集' + (i + 1));
                    html += '<div class="ep-stab" data-sid="' + sid + '" onclick="switchSeries(' + sid + ')">' + esc(name) + '</div>';
                });
                tabsEl.innerHTML = html;
            } else {
                tabsEl.style.display = 'none';
            }
            
            renderEpisodes(allEpisodes);
        }
        
        function renderEpisodes(videos) {
            const grid = document.getElementById('epGrid');
            let html = '';
            videos.forEach(v => {
                const num = v.episode_number || '?';
                html += '<div class="ep-item" data-id="' + v.id + '" data-url="' + esc(v.url || '') + '" onclick="playEp(this)" title="' + esc(v.title || '') + '">' + num + '</div>';
            });
            grid.innerHTML = html;
        }
        
        function switchSeries(sid) {
            currentSeriesId = sid;
            document.querySelectorAll('.ep-stab').forEach(t => {
                t.classList.toggle('active', (sid === null && t.dataset.sid === 'all') || (t.dataset.sid == sid));
            });
            const filtered = sid ? allEpisodes.filter(v => v.series_id == sid) : allEpisodes;
            renderEpisodes(filtered);
            highlightCurrent();
            document.getElementById('epCount').textContent = '(' + filtered.length + '集)';
        }
        
        function playEp(el) {
            const url = el.dataset.url;
            if (!url) return;
            currentVideoId = parseInt(el.dataset.id);
            dp.switchVideo({ url: url });
            dp.play();
            highlightCurrent();
            // 切换集数时触发贴片广告
            if (adEngine && showAds && effectiveAdMode !== 'none' && !noAd) {
                setTimeout(function() {
                    adEngine.playPreRoll();
                }, 300);
            }
        }
        
        function highlightCurrent() {
            document.querySelectorAll('.ep-item').forEach(el => {
                el.classList.toggle('active', el.dataset.id == currentVideoId);
            });
            const active = document.querySelector('.ep-item.active');
            if (active) active.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        function esc(s) {
            if (!s) return '';
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        
        // 初始化 - 选集始终加载（不受广告影响）
        console.log('[选集] 开始加载, PLAYER_CODE:', PLAYER_CODE);
        loadEpisodes().then(function() {
            console.log('[选集] 加载完成, 集数:', allEpisodes.length);
        }).catch(function(e) {
            console.error('[选集] 加载异常:', e);
        });
    </script>
</body>
</html>
