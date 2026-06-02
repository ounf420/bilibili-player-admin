<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>正在加载... - DPlayer影视</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <style>
        :root {
            --bg-dark: #0b0b0f;
            --bg-page: #141418;
            --bg-card: #1c1c22;
            --bg-hover: #25252d;
            --green: #00be06;
            --green-dark: #00a305;
            --gold: #f5a623;
            --accent: #fb7299;
            --text: #e8e8e8;
            --text-sec: #999;
            --text-muted: #666;
            --border: #2a2a32;
            --radius: 8px;
            --radius-lg: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background: var(--bg-page); color: var(--text); line-height: 1.6; overflow-x: hidden;
        }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: var(--bg-dark); height: 60px; border-bottom: 1px solid var(--border); }
        .nav-inner { max-width: 1400px; margin: 0 auto; padding: 0 24px; height: 100%; display: flex; align-items: center; gap: 24px; }
        .nav-logo { display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 700; font-size: 20px; flex-shrink: 0; }
        .nav-logo i { color: var(--green); font-size: 22px; }
        .nav-links { display: flex; gap: 4px; }
        .nav-links a { padding: 8px 16px; border-radius: 20px; font-size: 14px; color: rgba(255,255,255,.6); transition: .2s; }
        .nav-links a:hover, .nav-links a.active { color: #fff; background: rgba(255,255,255,.1); }
        .nav-search { flex: 1; max-width: 400px; position: relative; margin-left: auto; }
        .nav-search input { width: 100%; padding: 8px 40px 8px 16px; border-radius: 20px; border: 1px solid var(--border); background: rgba(255,255,255,.06); color: #fff; font-size: 13px; outline: none; transition: .2s; }
        .nav-search input:focus { border-color: var(--green); background: rgba(255,255,255,.1); }
        .nav-search input::placeholder { color: var(--text-muted); }
        .nav-search i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; cursor: pointer; }
        .nav-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .btn-vip { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; background: linear-gradient(135deg, #ffd700, #ff8c00); color: #fff; border: none; cursor: pointer; transition: .2s; }
        .btn-vip:hover { opacity: .9; transform: scale(1.03); }
        .avatar { width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--green); cursor: pointer; object-fit: cover; }
        .user-dd { position: relative; }
        .user-menu { display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 10px; padding: 8px; min-width: 160px; z-index: 100; }
        .user-dd:hover .user-menu { display: block; }
        .user-menu a { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; font-size: 13px; color: rgba(255,255,255,.7); transition: .2s; }
        .user-menu a:hover { background: rgba(255,255,255,.1); color: #fff; }
        .user-menu a.logout { color: var(--accent); }
        .mobile-menu-btn { display: none; background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; padding: 8px; }

        .main-wrap { padding-top: 60px; max-width: 1400px; margin: 0 auto; display: flex; gap: 0; min-height: calc(100vh - 60px); }
        .player-section { flex: 1; min-width: 0; background: var(--bg-dark); }
        .player-box { position: relative; width: 100%; background: #000; }
        .player-box #dplayer { width: 100%; aspect-ratio: 16/9; }
        .player-logo { position: absolute; z-index: 100; pointer-events: none; }
        .player-logo.top-left { top: 20px; left: 20px; }
        .player-logo.top-right { top: 20px; right: 20px; }
        .player-logo.bottom-left { bottom: 70px; left: 20px; }
        .player-logo.bottom-right { bottom: 70px; right: 20px; }
        .player-logo img { max-width: 120px; max-height: 40px; opacity: 0.7; }
        .player-logo .text-watermark { color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 500; text-shadow: 0 1px 3px rgba(0,0,0,0.5); white-space: nowrap; user-select: none; }

        .video-info { padding: 20px 24px; background: var(--bg-dark); border-bottom: 1px solid var(--border); }
        .video-title { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 10px; line-height: 1.4; }
        .video-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .video-meta span { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        .video-meta span i { font-size: 12px; }
        .video-actions { display: flex; gap: 8px; margin-top: 14px; flex-wrap: wrap; }
        .act-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 20px; font-size: 13px; border: 1px solid var(--border); background: transparent; color: var(--text-sec); cursor: pointer; transition: .2s; }
        .act-btn:hover { border-color: var(--green); color: var(--green); }
        .act-btn.active { border-color: var(--green); color: var(--green); background: rgba(0,190,6,.08); }
        .act-btn.liked { border-color: var(--accent); color: var(--accent); }
        .act-btn i { font-size: 14px; }

        .video-desc-wrap { padding: 20px 24px; background: var(--bg-page); }
        .desc-toggle { display: flex; align-items: center; justify-content: space-between; cursor: pointer; padding: 12px 16px; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); transition: .2s; }
        .desc-toggle:hover { border-color: var(--green); }
        .desc-toggle span { font-size: 14px; font-weight: 600; color: var(--text); }
        .desc-toggle i { color: var(--text-muted); transition: .2s; }
        .desc-toggle.open i { transform: rotate(180deg); }
        .desc-content { display: none; margin-top: 12px; padding: 16px; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); font-size: 14px; color: var(--text-sec); line-height: 1.8; }
        .desc-content.show { display: block; }
        .desc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; }
        .desc-row { display: flex; gap: 8px; font-size: 13px; }
        .desc-row .lbl { color: var(--text-muted); min-width: 50px; flex-shrink: 0; }
        .desc-row .val { color: var(--text-sec); }

        .episode-section { padding: 0 24px 20px; background: var(--bg-page); }
        .episode-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .episode-header h3 { font-size: 16px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px; }
        .episode-header h3 i { color: var(--green); }
        .episode-grid { display: flex; gap: 8px; flex-wrap: wrap; }
        .ep-btn { min-width: 48px; padding: 8px 14px; border-radius: var(--radius); background: var(--bg-card); border: 1px solid var(--border); color: var(--text-sec); font-size: 13px; font-weight: 500; cursor: pointer; transition: .2s; text-align: center; }
        .ep-btn:hover { border-color: var(--green); color: var(--green); }
        .ep-btn.active { background: var(--green); border-color: var(--green); color: #fff; }

        .comment-section { padding: 20px 24px 40px; background: var(--bg-page); }
        .comment-header { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-size: 16px; font-weight: 700; color: #fff; }
        .comment-header i { color: var(--green); }
        .comment-header .count { font-size: 13px; color: var(--text-muted); font-weight: 400; margin-left: 4px; }
        .comment-input-wrap { display: flex; gap: 12px; margin-bottom: 24px; }
        .comment-avatar { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg, var(--green), #00e676); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; font-weight: 600; }
        .comment-input-box { flex: 1; position: relative; }
        .comment-input-box textarea { width: 100%; padding: 12px 16px; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg-card); color: var(--text); font-size: 14px; resize: none; min-height: 60px; outline: none; transition: .2s; font-family: inherit; }
        .comment-input-box textarea:focus { border-color: var(--green); }
        .comment-input-box textarea::placeholder { color: var(--text-muted); }
        .comment-submit { position: absolute; bottom: 8px; right: 8px; padding: 6px 16px; border-radius: 16px; background: var(--green); color: #fff; border: none; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; }
        .comment-submit:hover { background: var(--green-dark); }
        .comment-list { display: flex; flex-direction: column; gap: 16px; }
        .comment-item { display: flex; gap: 12px; }
        .comment-item .c-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: var(--bg-hover); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 12px; }
        .comment-item .c-body { flex: 1; }
        .comment-item .c-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .comment-item .c-time { font-size: 11px; color: var(--text-muted); margin-left: 8px; }
        .comment-item .c-text { font-size: 13px; color: var(--text-sec); margin-top: 4px; line-height: 1.6; }
        .comment-item .c-actions { display: flex; gap: 12px; margin-top: 6px; }
        .comment-item .c-actions span { font-size: 12px; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 4px; transition: .2s; }
        .comment-item .c-actions span:hover { color: var(--green); }

        .sidebar { width: 380px; flex-shrink: 0; background: var(--bg-dark); border-left: 1px solid var(--border); overflow-y: auto; max-height: calc(100vh - 60px); position: sticky; top: 60px; }
        .sidebar-header { padding: 16px 20px; font-size: 15px; font-weight: 700; color: #fff; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
        .sidebar-header i { color: var(--green); }
        .rec-list { padding: 8px 0; }
        .rec-item { display: flex; gap: 12px; padding: 10px 20px; cursor: pointer; transition: .2s; }
        .rec-item:hover { background: var(--bg-hover); }
        .rec-item.active { background: rgba(0,190,6,.08); }
        .rec-thumb { width: 140px; aspect-ratio: 16/9; border-radius: 6px; overflow: hidden; flex-shrink: 0; position: relative; background: var(--bg-hover); }
        .rec-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .rec-thumb .rec-dur { position: absolute; bottom: 4px; right: 4px; padding: 1px 6px; border-radius: 3px; background: rgba(0,0,0,.75); color: #fff; font-size: 11px; font-weight: 500; }
        .rec-thumb .rec-vip-tag { position: absolute; top: 4px; left: 4px; padding: 1px 6px; border-radius: 3px; font-size: 10px; font-weight: 700; background: linear-gradient(135deg, #ffd700, #ff8c00); color: #fff; }
        .rec-info { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
        .rec-title { font-size: 13px; font-weight: 600; color: var(--text); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; margin-bottom: 4px; }
        .rec-meta { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }

        .footer { background: var(--bg-dark); color: rgba(255,255,255,.4); padding: 32px 24px 20px; text-align: center; font-size: 12px; border-top: 1px solid var(--border); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        @media (max-width: 1024px) {
            .sidebar { width: 300px; }
            .rec-thumb { width: 120px; }
        }
        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            .nav-links { display: none; position: absolute; top: 60px; left: 0; right: 0; background: var(--bg-dark); flex-direction: column; padding: 12px; border-bottom: 1px solid var(--border); }
            .nav-links.open { display: flex; }
            .nav-search { display: none; }
            .main-wrap { flex-direction: column; }
            .player-box #dplayer { aspect-ratio: auto; height: auto; min-height: 200px; }
            .sidebar { width: 100%; max-height: none; position: static; border-left: none; border-top: 1px solid var(--border); }
            .rec-item { padding: 10px 16px; }
            .rec-thumb { width: 110px; }
            .video-info { padding: 16px; }
            .video-title { font-size: 17px; }
            .video-desc-wrap, .episode-section, .comment-section { padding-left: 16px; padding-right: 16px; }
            .desc-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .video-meta { gap: 10px; }
            .video-actions { gap: 6px; }
            .act-btn { padding: 6px 12px; font-size: 12px; }
            .rec-thumb { width: 100px; }
        }

        /* 隐藏DPlayer中央暂停/播放图标 */
        .dplayer-bezel,
        .dplayer-bezel .dplayer-bezel-icon,
        .dplayer-bezel .dplayer-bezel-transition { display: none !important; opacity: 0 !important; animation: none !important; }
        .dplayer-mobile-play { display: none !important; }
        .dplayer-mask { display: none !important; }

        /* 控制栏按钮 - 上一集/播放/下一集 */
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

        /* 弹幕输入栏 - 默认隐藏，全屏才显示 */
        .dp-danmaku-bar { display: none; align-items: center; gap: 6px; margin-left: 8px; flex-shrink: 0; }
        .dplayer-fulled .dp-danmaku-bar { display: flex; }
        .dp-danmaku-bar .dm-toggle { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; cursor: pointer; color: rgba(255,255,255,.7); transition: .2s; border-radius: 4px; }
        .dp-danmaku-bar .dm-toggle:hover { color: #fff; }
        .dp-danmaku-bar .dm-toggle.active { color: #00be06; }
        .dp-danmaku-bar .dm-toggle svg { width: 18px; height: 18px; fill: currentColor; }
        .dp-danmaku-bar .dm-color-btn { width: 24px; height: 24px; border-radius: 50%; cursor: pointer; border: 2px solid rgba(255,255,255,.3); transition: .2s; flex-shrink: 0; }
        .dp-danmaku-bar .dm-color-btn:hover { border-color: #fff; }
        .dp-danmaku-bar .dm-color-picker { display: none; position: absolute; bottom: 42px; right: 0; background: #1c1c22; border: 1px solid #2a2a32; border-radius: 8px; padding: 10px; z-index: 200; box-shadow: 0 4px 20px rgba(0,0,0,.5); }
        .dp-danmaku-bar .dm-color-picker.show { display: flex; flex-wrap: wrap; gap: 6px; width: 160px; }
        .dp-danmaku-bar .dm-color-picker span { width: 22px; height: 22px; border-radius: 4px; cursor: pointer; border: 2px solid transparent; transition: .2s; }
        .dp-danmaku-bar .dm-color-picker span:hover, .dp-danmaku-bar .dm-color-picker span.active { border-color: #fff; transform: scale(1.15); }
        .dp-danmaku-bar .dm-color-picker span[data-vip] { position: relative; }
        .dp-danmaku-bar .dm-color-picker span[data-vip]::after { content: '👑'; position: absolute; top: -6px; right: -6px; font-size: 8px; line-height: 1; }
        .dp-danmaku-bar .dm-input-wrap { display: flex; align-items: center; gap: 4px; background: rgba(255,255,255,.1); border-radius: 16px; padding: 4px 4px 4px 12px; border: 1px solid rgba(255,255,255,.15); transition: .2s; flex: 1; min-width: 0; max-width: 200px; }
        .dp-danmaku-bar .dm-input-wrap:focus-within { border-color: #00be06; background: rgba(255,255,255,.15); }
        .dp-danmaku-bar .dm-input { flex: 1; min-width: 0; background: none; border: none; color: #fff; font-size: 13px; outline: none; width: 80px; }
        .dp-danmaku-bar .dm-input::placeholder { color: rgba(255,255,255,.4); }
        .dp-danmaku-bar .dm-send { padding: 5px 14px; border-radius: 12px; background: #00be06; color: #fff; border: none; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; white-space: nowrap; flex-shrink: 0; }
        .dp-danmaku-bar .dm-send:hover { background: #00a305; }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-inner">
        <a href="/" class="nav-logo"><i class="fas fa-play-circle"></i><span>DPlayer影视</span></a>
        <button class="mobile-menu-btn" onclick="document.getElementById('navL').classList.toggle('open')"><i class="fas fa-bars"></i></button>
        <div class="nav-links" id="navL">
            <a href="/">首页</a>
            <a href="/v">影视中心</a>
            <a href="/v?type=movie">电影</a>
            <a href="/v?type=tv">电视剧</a>
            <a href="/v?type=anime">动漫</a>
        </div>
        <div class="nav-search">
            <div style="position:relative;flex:1;max-width:320px;">
                <input type="text" placeholder="搜索视频..." id="searchInput" onkeydown="if(event.key==='Enter')doSearch()" oninput="onSearchInput(this.value)" autocomplete="off">
                <div id="searchSuggest" style="display:none;position:absolute;top:100%;left:0;right:0;background:#1c1c22;border:1px solid #2a2a32;border-radius:8px;margin-top:4px;z-index:100;max-height:300px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.5);"></div>
            </div>
            <i class="fas fa-search" onclick="doSearch()"></i>
        </div>
        <div class="nav-right" id="navUser">
            <a href="/login" style="color:rgba(255,255,255,.7);font-size:13px">登录</a>
        </div>
    </div>
</nav>

<div class="main-wrap">
    <div class="player-section">
        <div class="player-box">
            <div id="dplayer"></div>
            <div class="player-logo" id="player-logo" style="display:none;"></div>
        </div>
        <div class="video-info" id="videoInfo">
            <div class="video-title" id="vTitle">加载中...</div>
            <div class="video-meta" id="vMeta"></div>
            <div class="video-actions" id="vActions">
                <button class="act-btn" id="btnLike" onclick="toggleLike()"><i class="far fa-thumbs-up"></i> 点赞</button>
                <button class="act-btn" id="btnFav" onclick="toggleFav()"><i class="far fa-heart"></i> 收藏</button>
                <button class="act-btn" onclick="shareVideo()"><i class="fas fa-share-alt"></i> 分享</button>
            </div>
        </div>
        <div class="video-desc-wrap" id="descSection" style="display:none;">
            <div class="desc-toggle" onclick="toggleDesc(this)">
                <span><i class="fas fa-info-circle" style="color:var(--green);margin-right:6px;"></i> 简介详情</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="desc-content" id="descContent"></div>
        </div>
        <div class="episode-section" id="episodeSection" style="display:none;">
            <div class="episode-header">
                <h3><i class="fas fa-list"></i> 选集</h3>
                <span id="epCount" style="font-size:13px;color:var(--text-muted);"></span>
            </div>
            <div class="episode-grid" id="episodeGrid"></div>
        </div>
        <div class="comment-section">
            <div class="comment-header">
                <i class="fas fa-comments"></i> 评论 <span class="count" id="commentCount">(0)</span>
            </div>
            <div class="comment-input-wrap" id="commentInputWrap">
                <div class="comment-avatar" id="commentAvatar">?</div>
                <div class="comment-input-box">
                    <textarea id="commentText" placeholder="说点什么吧..."></textarea>
                    <button class="comment-submit" onclick="submitComment()">发布</button>
                </div>
            </div>
            <div class="comment-list" id="commentList">
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-comment-slash" style="font-size:24px;display:block;margin-bottom:8px;opacity:.5;"></i>
                    暂无评论，快来抢沙发吧~
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header"><i class="fas fa-fire"></i> 推荐视频</div>
        <div class="rec-list" id="recList">
            <div style="padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px;">
                <i class="fas fa-spinner fa-spin" style="font-size:20px;display:block;margin-bottom:8px;"></i>
                加载中...
            </div>
        </div>
    </div>
</div>

<footer class="footer">© 2026 DPlayer影视 · 基于 Laravel + DPlayer 构建 · 仅供演示</footer>

<script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flv.js@1.6.2/dist/flv.min.js"></script>
<script src="/js/media-engine.js"></script>
<script>
let dp = null, mediaMgr = null, settings = {};
let currentVideo = null, allVideos = [];

// ========== UTILS ==========
function fmt(n) { if (!n) return '0'; return n >= 10000 ? (n/10000).toFixed(1) + '万' : n + ''; }
function fmtDur(s) { if (!s) return ''; return Math.floor(s/60) + ':' + (s%60 < 10 ? '0' : '') + (s%60); }
function genPlayUrl(id) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let token = '';
    for (let i = 0; i < 16; i++) token += chars.charAt(Math.floor(Math.random() * chars.length));
    return '/v/' + token + '-' + id + '.html';
}
function getH() {
    const t = localStorage.getItem('token');
    const h = { 'Accept': 'application/json' };
    if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
}

// ========== NAV ==========
function initNav() {
    const t = localStorage.getItem('token'), u = localStorage.getItem('user');
    const el = document.getElementById('navUser');
    if (t && u) {
        try {
            const j = JSON.parse(u);
            el.innerHTML = `
                <a href="/vip" class="btn-vip"><i class="fas fa-crown"></i> VIP</a>
                <div class="user-dd">
                    <img src="${j.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(j.nickname||j.username) + '&background=00be06&color=fff&size=64'}" class="avatar">
                    <div class="user-menu">
                        <a href="/user"><i class="fas fa-user"></i> 用户中心</a>
                        <a href="/account"><i class="fas fa-cog"></i> 账号中心</a>
                        <a class="logout" href="javascript:logout()"><i class="fas fa-sign-out-alt"></i> 退出</a>
                    </div>
                </div>`;
            const ca = document.getElementById('commentAvatar');
            ca.textContent = (j.nickname||j.username||'?').charAt(0).toUpperCase();
        } catch(e) {}
    }
}
function logout() {
    const t = localStorage.getItem('token');
    if (t) fetch('/api/auth/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + t } });
    localStorage.removeItem('token'); localStorage.removeItem('user');
    location.reload();
}
function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    if (q) {
        // 记录搜索
        fetch('/api/search/log', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ q }) });
        location.href = '/v?search=' + encodeURIComponent(q);
    }
    document.getElementById('searchSuggest').style.display = 'none';
}

let _searchTimer = null;
async function onSearchInput(val) {
    const box = document.getElementById('searchSuggest');
    if (!val || val.trim().length < 1) { box.style.display = 'none'; return; }
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(async () => {
        try {
            const r = await fetch('/api/search/suggest?q=' + encodeURIComponent(val.trim()));
            const list = await r.json();
            if (!list.length) { box.style.display = 'none'; return; }
            box.style.display = 'block';
            box.innerHTML = list.map(v => `
                <div onclick="loadVideoById('${v.id}');document.getElementById('searchSuggest').style.display='none'"
                     style="display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;transition:.15s;"
                     onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='none'">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${v.title}</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.4);">${v.category||''} ${v.vip_level>0?'<span style="color:#ffd700;">VIP</span>':''}</div>
                    </div>
                    ${v.score>0?`<div style="font-size:12px;color:#ffd700;">${v.score}分</div>`:''}
                </div>
            `).join('');
        } catch(e) {}
    }, 200);
}

// 点击外部关闭搜索建议
document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav-search')) {
        document.getElementById('searchSuggest').style.display = 'none';
    }
});

// ========== PLAYER ==========
async function loadSettings() {
    try { const r = await fetch('/api/settings'); settings = await r.json(); userVipLevel = settings.user_vip_level || 0; } catch(e) {}
}

function showLogo() {
    const el = document.getElementById('player-logo');
    const lt = settings.logo_type || 'text', lp = settings.logo_position || 'top-right';
    el.className = 'player-logo ' + lp;
    if (lt === 'image' && settings.logo_url) {
        el.innerHTML = `<img src="${settings.logo_url}" alt="Logo">`;
        el.style.display = 'block';
    } else if (lt === 'text' && settings.logo_text) {
        el.innerHTML = `<span class="text-watermark">${settings.logo_text}</span>`;
        el.style.display = 'block';
    } else { el.style.display = 'none'; }
}

function buildVideoConfig(video) {
    const type = video.type || 'mp4';
    const config = { url: video.url, type: type, pic: video.cover || '' };
    if (type === 'm3u8' && typeof Hls !== 'undefined' && Hls.isSupported()) {
        config.type = 'customHls';
        config.customType = {
            customHls: function(videoEl) {
                const hls = new Hls();
                hls.loadSource(videoEl.src);
                hls.attachMedia(videoEl);
                hls.on(Hls.Events.MANIFEST_PARSED, () => videoEl.play());
                hls.on(Hls.Events.ERROR, (e, d) => { if (d.fatal) { console.error('HLS错误:', d); hls.destroy(); } });
            }
        };
    } else if (type === 'flv' && typeof flvjs !== 'undefined' && flvjs.isSupported()) {
        config.type = 'customFlv';
        config.customType = {
            customFlv: function(videoEl) {
                const fp = flvjs.createPlayer({ type: 'flv', url: videoEl.src });
                fp.attachMediaElement(videoEl); fp.load(); fp.play();
            }
        };
    }
    return config;
}

async function initPlayer(video) {
    await loadSettings();
    if (dp) dp.destroy();
    if (mediaMgr) mediaMgr.destroy();

    currentVideo = video;
    document.title = video.title + ' - DPlayer影视';

    document.getElementById('vTitle').textContent = video.title;
    document.getElementById('vMeta').innerHTML = `
        <span><i class="fas fa-eye"></i> ${fmt(video.views)}次播放</span>
        ${video.duration ? `<span><i class="fas fa-clock"></i> ${fmtDur(video.duration)}</span>` : ''}
        ${video.type ? `<span><i class="fas fa-film"></i> ${video.type.toUpperCase()}</span>` : ''}
        <span><i class="fas fa-calendar"></i> ${video.created_at ? video.created_at.substring(0,10) : ''}</span>
    `;

    const vc = buildVideoConfig(video);
    dp = new DPlayer({
        container: document.getElementById('dplayer'),
        autoplay: settings.autoplay !== false,
        theme: settings.theme_color || '#00be06',
        loop: settings.loop || false,
        lang: 'zh-cn',
        screenshot: settings.show_screenshot !== false,
        hotkey: true,
        preload: settings.preload || 'auto',
        volume: settings.volume || 0.7,
        mutex: true,
        video: vc,
        danmaku: {
            id: video.id,
            api: '/api/danmaku',
            user: 'visitor'
        }
    });

    // 注入控制栏按钮
    setTimeout(injectEpisodeButtons, 400);
    setTimeout(injectDanmakuBar, 500);

    mediaMgr = new MediaManager(dp);
    await mediaMgr.loadCampaigns();
    await mediaMgr.playSplash();
    await mediaMgr.playPreroll();
    // 广告播完尝试恢复播放（可能被浏览器拦截，用户手动点击即可）
    try { dp.play(); } catch(e) {}
    mediaMgr.showMarquee();
    mediaMgr.startOverlayRotation();
    mediaMgr.showQrcode();
    mediaMgr.showBanner();
    var hasPlayed = false;
    dp.on('play', () => { hasPlayed = true; mediaMgr.clearPausePromo(); });
    dp.on('timeupdate', () => { if (!dp.video.paused) mediaMgr.checkMidroll(dp.video.currentTime); });
    dp.on('pause', () => { if (hasPlayed && !mediaMgr.skipNextPausePromo && !mediaMgr._inAdSlot) mediaMgr.playPausePromo(); });
    dp.on('ended', async () => { mediaMgr._inAdSlot = true; const played = await mediaMgr.playPostroll(); if (!played) mediaMgr._inAdSlot = false; });
    showLogo();

    document.querySelectorAll('.rec-item').forEach(el => {
        el.classList.toggle('active', el.dataset.id == video.id);
    });
    checkFavLike();
}

function updateDesc(video) {
    const descSection = document.getElementById('descSection');
    const descContent = document.getElementById('descContent');
    const hasDesc = video.description || video.director || video.actors || video.region || video.genre;
    if (!hasDesc) { descSection.style.display = 'none'; return; }
    descSection.style.display = 'block';
    let html = video.description ? `<p style="margin-bottom:12px;">${video.description}</p>` : '';
    const fields = [
        { l: '导演', v: video.director }, { l: '演员', v: video.actors },
        { l: '地区', v: video.region }, { l: '年份', v: video.year },
        { l: '类型', v: video.genre }, { l: '语言', v: video.language },
        { l: '标签', v: video.tags },
    ].filter(f => f.v);
    if (fields.length) {
        html += '<div class="desc-grid">' + fields.map(f =>
            `<div class="desc-row"><span class="lbl">${f.l}</span><span class="val">${f.v}</span></div>`
        ).join('') + '</div>';
    }
    descContent.innerHTML = html;
}

function updateEpisodes(video) {
    const section = document.getElementById('episodeSection');
    const grid = document.getElementById('episodeGrid');
    if (video.episode_count && video.episode_count > 1) {
        section.style.display = 'block';
        document.getElementById('epCount').textContent = `共${video.episode_count}集`;
        let html = '';
        for (let i = 1; i <= Math.min(video.episode_count, 50); i++) {
            html += `<button class="ep-btn ${i === 1 ? 'active' : ''}" onclick="playEpisode(${i})">第${i}集</button>`;
        }
        grid.innerHTML = html;
    } else { section.style.display = 'none'; }
}

function playEpisode(ep) {
    document.querySelectorAll('.ep-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.ep-btn')[ep-1]?.classList.add('active');
}

function toggleDesc(el) {
    el.classList.toggle('open');
    document.getElementById('descContent').classList.toggle('show');
}

// ========== RECOMMEND ==========
async function loadRecommend(videoId) {
    try {
        const url = videoId ? `/api/recommend?video_id=${videoId}&limit=12` : '/api/videos';
        const r = await fetch(url);
        allVideos = await r.json();
        const list = document.getElementById('recList');
        if (!allVideos.length) {
            list.innerHTML = '<div style="padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px;">暂无视频</div>';
            return;
        }
        list.innerHTML = allVideos.map(v => `
            <div class="rec-item" data-id="${v.id}" onclick="loadVideoById('${v.id}')">
                <div class="rec-thumb">
                    ${v.cover ? `<img src="${v.cover}" alt="${v.title}" loading="lazy">` : `<div style="width:100%;height:100%;background:linear-gradient(135deg,#1a1a2e,#16213e);display:flex;align-items:center;justify-content:center;"><i class="fas fa-play" style="color:rgba(255,255,255,.3);font-size:20px;"></i></div>`}
                    ${v.duration ? `<span class="rec-dur">${fmtDur(v.duration)}</span>` : ''}
                    ${v.vip_level > 0 ? '<span class="rec-vip-tag">VIP</span>' : ''}
                </div>
                <div class="rec-info">
                    <div class="rec-title">${v.title}</div>
                    <div class="rec-meta"><i class="fas fa-eye"></i> ${fmt(v.views)}次播放</div>
                </div>
            </div>
        `).join('');
    } catch(e) { console.error('加载推荐失败:', e); }
}

async function loadVideoById(id, pushState) {
    try {
        const h = getH();
        const r = await fetch(`/api/videos/${id}`, { headers: h });
        const v = await r.json();
        if (r.status === 403 && v.error === 'vip_required') {
            showVipLock(v);
            return;
        }
        if (v && !v.error) {
            hideVipLock();
            initPlayer(v);
            updateDesc(v);
            updateEpisodes(v);
            loadComments();
            loadRecommend(id);
            if (pushState !== false) {
                history.pushState({ id: id }, v.title, genPlayUrl(id));
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch(e) { console.error('加载视频失败:', e); }
}

function showVipLock(info) {
    const container = document.getElementById('dplayer');
    if (dp) { try { dp.destroy(); } catch(e){} dp = null; }
    const levelNames = {1:'黄金VIP', 2:'钻石VIP', 3:'星钻VIP'};
    const levelColors = {1:'linear-gradient(135deg,#ffd700,#ff8c00)', 2:'linear-gradient(135deg,#b9f2ff,#00d4ff)', 3:'linear-gradient(135deg,#e0b0ff,#8b5cf6)'};
    const lv = info.required_vip_level || 1;
    container.innerHTML = `
        <div style="width:100%;height:100%;background:#0a0a0a;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:20px;">
            <div style="width:80px;height:80px;border-radius:50%;background:${levelColors[lv]};display:flex;align-items:center;justify-content:center;font-size:36px;">👑</div>
            <div style="font-size:20px;color:#fff;font-weight:600;">${info.message || 'VIP专属内容'}</div>
            <div style="font-size:14px;color:rgba(255,255,255,0.6);">升级${levelNames[lv] || 'VIP'}即可观看</div>
            <a href="/vip" style="background:${levelColors[lv]};color:#fff;padding:10px 32px;border-radius:24px;font-size:14px;font-weight:600;text-decoration:none;box-shadow:0 4px 15px rgba(0,0,0,0.3);">立即开通</a>
        </div>`;
}

function hideVipLock() {
    // initPlayer会重建dplayer内容，无需额外处理
}
window.addEventListener('popstate', (e) => {
    if (e.state?.id) loadVideoById(e.state.id, false);
});

// ========== INTERACTIONS ==========
async function checkFavLike() {
    const t = localStorage.getItem('token');
    if (!t || !currentVideo) return;
    try {
        const [fr, lr] = await Promise.all([
            fetch('/api/favorites/check', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer '+t }, body: JSON.stringify({ video_id: currentVideo.id }) }),
            fetch('/api/likes/check', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer '+t }, body: JSON.stringify({ video_id: currentVideo.id }) })
        ]);
        const fd = await fr.json(), ld = await lr.json();
        if (fd.data?.favorited) { document.getElementById('btnFav').classList.add('active'); document.getElementById('btnFav').innerHTML = '<i class="fas fa-heart"></i> 已收藏'; }
        if (ld.data?.liked) { document.getElementById('btnLike').classList.add('liked'); document.getElementById('btnLike').innerHTML = '<i class="fas fa-thumbs-up"></i> 已点赞'; }
    } catch(e) {}
}

async function toggleLike() {
    const t = localStorage.getItem('token');
    if (!t) { location.href = '/login'; return; }
    if (!currentVideo) return;
    try {
        const r = await fetch('/api/likes/toggle', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer '+t }, body: JSON.stringify({ video_id: currentVideo.id, type: 1 }) });
        const d = await r.json();
        const b = document.getElementById('btnLike');
        if (d.data?.liked) { b.classList.add('liked'); b.innerHTML = '<i class="fas fa-thumbs-up"></i> 已点赞'; }
        else { b.classList.remove('liked'); b.innerHTML = '<i class="far fa-thumbs-up"></i> 点赞'; }
    } catch(e) {}
}

async function toggleFav() {
    const t = localStorage.getItem('token');
    if (!t) { location.href = '/login'; return; }
    if (!currentVideo) return;
    try {
        const r = await fetch('/api/favorites/toggle', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer '+t }, body: JSON.stringify({ video_id: currentVideo.id }) });
        const d = await r.json();
        const b = document.getElementById('btnFav');
        if (d.data?.favorited) { b.classList.add('active'); b.innerHTML = '<i class="fas fa-heart"></i> 已收藏'; }
        else { b.classList.remove('active'); b.innerHTML = '<i class="far fa-heart"></i> 收藏'; }
    } catch(e) {}
}

function shareVideo() {
    if (navigator.share) {
        navigator.share({ title: currentVideo?.title || 'DPlayer影视', url: location.href });
    } else {
        navigator.clipboard.writeText(location.href).then(() => alert('链接已复制！'));
    }
}

// ========== COMMENTS ==========
async function submitComment() {
    const t = localStorage.getItem('token');
    if (!t) { location.href = '/login'; return; }
    const text = document.getElementById('commentText').value.trim();
    if (!text) return;
    try {
        const r = await fetch('/api/comments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer '+t },
            body: JSON.stringify({ video_id: currentVideo?.id, content: text })
        });
        const d = await r.json();
        if (d.success) { document.getElementById('commentText').value = ''; loadComments(); }
    } catch(e) {}
}

async function loadComments() {
    if (!currentVideo) return;
    try {
        const r = await fetch(`/api/comments?video_id=${currentVideo.id}`);
        const d = await r.json();
        const list = document.getElementById('commentList');
        const comments = d.data || d || [];
        document.getElementById('commentCount').textContent = `(${comments.length})`;
        if (!comments.length) {
            list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;"><i class="fas fa-comment-slash" style="font-size:24px;display:block;margin-bottom:8px;opacity:.5;"></i>暂无评论，快来抢沙发吧~</div>';
            return;
        }
        const levelBadge = (lv) => {
            if (lv === 1) return '<span style="background:linear-gradient(135deg,#ffd700,#ff8c00);color:#fff;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:700;margin-left:4px;">黄金VIP</span>';
            if (lv === 2) return '<span style="background:linear-gradient(135deg,#b9f2ff,#00d4ff);color:#000;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:700;margin-left:4px;">钻石VIP</span>';
            if (lv >= 3) return '<span style="background:linear-gradient(135deg,#e0b0ff,#8b5cf6);color:#fff;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:700;margin-left:4px;">星钻VIP</span>';
            return '';
        };
        list.innerHTML = comments.map(c => {
            const vipLv = c.user?.vip_level || 0;
            const avatarBg = vipLv > 0 ? 'background:linear-gradient(135deg,#ffd700,#ff8c00);' : 'background:#00be06;';
            return `
            <div class="comment-item">
                <div class="c-avatar" style="${avatarBg}">${(c.user?.nickname||c.user?.username||'?').charAt(0).toUpperCase()}</div>
                <div class="c-body">
                    <span class="c-name">${c.user?.nickname||c.user?.username||'匿名用户'}${levelBadge(vipLv)}</span>
                    <span class="c-time">${c.created_at||''}</span>
                    <div class="c-text">${c.content}</div>
                    <div class="c-actions">
                        <span onclick="likeComment(${c.id}, this)" style="cursor:pointer;"><i class="far fa-thumbs-up"></i> <span>${c.likes||0}</span></span>
                        <span onclick="replyComment(${c.id}, '${c.user?.nickname||c.user?.username||'匿名'}')" style="cursor:pointer;"><i class="far fa-comment"></i> 回复</span>
                    </div>
                </div>
            </div>`;
        }).join('');
    } catch(e) {}
}

async function likeComment(commentId, el) {
    const t = localStorage.getItem('token');
    if (!t) { location.href = '/login'; return; }
    try {
        const r = await fetch(`/api/comments/${commentId}/like`, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + t, 'Content-Type': 'application/json' }
        });
        const d = await r.json();
        if (d.success) {
            const span = el.querySelector('span');
            let count = parseInt(span.textContent) || 0;
            span.textContent = d.liked ? count + 1 : Math.max(0, count - 1);
            el.style.color = d.liked ? '#00be06' : '';
        }
    } catch(e) {}
}

function replyComment(commentId, username) {
    const input = document.getElementById('commentText');
    if (input) {
        input.value = `@${username} `;
        input.focus();
        input.dataset.parentId = commentId;
    }
}

// ========== 控制栏按钮 ==========
function injectEpisodeButtons() {
    if (!dp) return;
    var leftIcons = dp.container.querySelector('.dplayer-icons-left');
    if (!leftIcons || leftIcons.querySelector('.dplayer-prev-btn')) return;

    var playBtn = leftIcons.querySelector('.dplayer-play-icon');
    if (!playBtn) return;

    // 上一集
    var prevBtn = document.createElement('div');
    prevBtn.className = 'dplayer-prev-btn';
    prevBtn.title = '上一集';
    prevBtn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>';
    prevBtn.addEventListener('click', function(e) { e.stopPropagation(); playPrev(); });

    // 播放/暂停
    var pauseBtn = document.createElement('div');
    pauseBtn.className = 'dplayer-pause-btn';
    pauseBtn.title = '播放/暂停';
    pauseBtn.innerHTML = dp.paused ?
        '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>' :
        '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
    pauseBtn.addEventListener('click', function(e) {
        e.stopPropagation(); dp.toggle();
        setTimeout(function() {
            pauseBtn.innerHTML = dp.paused ?
                '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>' :
                '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
        }, 100);
    });

    // 下一集
    var nextBtn = document.createElement('div');
    nextBtn.className = 'dplayer-next-btn';
    nextBtn.title = '下一集';
    nextBtn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>';
    nextBtn.addEventListener('click', function(e) { e.stopPropagation(); playNext(); });

    // 插入按钮
    playBtn.parentNode.insertBefore(prevBtn, playBtn);
    playBtn.parentNode.insertBefore(pauseBtn, playBtn);
    playBtn.parentNode.insertBefore(nextBtn, playBtn);
    playBtn.style.display = 'none';

    // 同步状态
    dp.on('play', function() { pauseBtn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>'; });
    dp.on('pause', function() { pauseBtn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>'; });
}

function playPrev() {
    if (!allVideos.length || !currentVideo) return;
    var idx = allVideos.findIndex(function(v) { return v.id === currentVideo.id; });
    if (idx > 0) loadVideoById(allVideos[idx - 1].id);
}

function playNext() {
    if (!allVideos.length || !currentVideo) return;
    var idx = allVideos.findIndex(function(v) { return v.id === currentVideo.id; });
    if (idx < allVideos.length - 1) loadVideoById(allVideos[idx + 1].id);
}

// ========== 弹幕 ==========
let danmakuColor = '#ffffff';
const DANMAKU_FREE_COLORS = ['#ffffff','#ff4444','#ff8800','#ffcc00','#00cc00','#00cccc','#4488ff','#cc44ff','#fb7299'];
const DANMAKU_VIP_COLORS = ['#ffd700','#ff69b4','#00ffff','#ff4500','#7cfc00','#ff1493','#00bfff'];
const DANMAKU_COLORS = [...DANMAKU_FREE_COLORS, ...DANMAKU_VIP_COLORS];
let userVipLevel = 0;

function injectDanmakuBar() {
    const right = document.querySelector('.dplayer-icons-right');
    if (!right || document.querySelector('.dp-danmaku-bar')) return;

    const bar = document.createElement('div');
    bar.className = 'dp-danmaku-bar';
    bar.innerHTML = `
        <div class="dm-toggle active" onclick="toggleDanmaku(this)" title="弹幕开关">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/><path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/></svg>
        </div>
        <div style="position:relative;">
            <div class="dm-color-btn" onclick="toggleColorPicker()" style="background:${danmakuColor}" title="弹幕颜色"></div>
            <div class="dm-color-picker" id="dmColorPicker">
                ${DANMAKU_FREE_COLORS.map(c => `<span style="background:${c}" data-color="${c}" onclick="pickDanmakuColor('${c}')"></span>`).join('')}
                <div style="width:100%;height:1px;background:rgba(255,255,255,.15);margin:4px 0;"></div>
                ${DANMAKU_VIP_COLORS.map(c => `<span style="background:${c}" data-color="${c}" data-vip="1" onclick="pickDanmakuColor('${c}')" title="VIP专属"></span>`).join('')}
            </div>
        </div>
        <div class="dm-input-wrap">
            <input class="dm-input" id="dmInput" placeholder="发个友善的弹幕见证当下" maxlength="100"
                onkeydown="if(event.key==='Enter')sendDanmaku()">
            <button class="dm-send" onclick="sendDanmaku()">发送</button>
        </div>
    `;

    // 插入到控制栏右侧最前面
    right.insertBefore(bar, right.firstChild);
}

function toggleDanmaku(el) {
    if (!dp || !dp.danmaku) return;
    el.classList.toggle('active');
    if (el.classList.contains('active')) {
        dp.danmaku.show();
    } else {
        dp.danmaku.hide();
    }
}

function toggleColorPicker() {
    const picker = document.getElementById('dmColorPicker');
    if (picker) picker.classList.toggle('show');
}

function pickDanmakuColor(color) {
    // VIP专属颜色检查
    if (DANMAKU_VIP_COLORS.includes(color) && userVipLevel <= 0) {
        if (confirm('此颜色为VIP专属，是否前往开通VIP？')) location.href = '/vip';
        return;
    }
    danmakuColor = color;
    const btn = document.querySelector('.dm-color-btn');
    if (btn) btn.style.background = color;
    const picker = document.getElementById('dmColorPicker');
    if (picker) picker.classList.remove('show');
    document.querySelectorAll('#dmColorPicker span').forEach(s => {
        s.classList.toggle('active', s.dataset.color === color);
    });
}

async function sendDanmaku() {
    const input = document.getElementById('dmInput');
    if (!input) return;
    const text = input.value.trim();
    if (!text) return;
    const t = localStorage.getItem('token');
    if (!t) { location.href = '/login'; return; }

    // 发送到服务器
    try {
        await fetch('/api/danmaku', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + t },
            body: JSON.stringify({
                id: currentVideo?.id,
                text: text,
                time: dp?.video?.currentTime || 0,
                color: danmakuColor,
                type: 'scroll'
            })
        });
    } catch(e) {}

    // 立即在播放器中显示
    if (dp && dp.danmaku) {
        dp.danmaku.draw({
            text: text,
            color: danmakuColor,
            type: 'scroll'
        });
    }
    input.value = '';
}

// ========== INIT ==========
document.addEventListener('DOMContentLoaded', async () => {
    initNav();
    const serverVideoId = '{{ $videoId ?? "" }}';
    const params = new URLSearchParams(location.search);
    const videoId = serverVideoId || params.get('v') || params.get('id') || params.get('video');

    await loadRecommend();

    if (videoId) {
        await loadVideoById(videoId, false);
    } else if (allVideos.length > 0) {
        await loadVideoById(allVideos[0].id, false);
    }
});
</script>
</body>
</html>
