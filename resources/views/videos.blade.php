<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>影视中心 - 高清影视在线观看</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
            background: #0b0b0f; color: #e8e8e8; min-height: 100vh; overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; }

        /* ===== 导航栏 ===== */
        .nav-bar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            height: 56px; background: rgba(15,15,20,0.95);
            backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; padding: 0 24px;
        }
        .nav-logo { font-size: 20px; font-weight: 700; color: #fff; margin-right: 28px; white-space: nowrap; }
        .nav-logo span { color: #e6a817; }
        .nav-links { display: flex; gap: 2px; flex: 1; overflow-x: auto; scrollbar-width: none; }
        .nav-links::-webkit-scrollbar { display: none; }
        .nav-link {
            padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 400;
            color: rgba(255,255,255,0.6); cursor: pointer; white-space: nowrap; transition: all 0.2s;
            position: relative;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-link.active { color: #fff; font-weight: 500; }
        .nav-link.active::after {
            content: ''; position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%);
            width: 16px; height: 2px; background: #e6a817; border-radius: 1px;
        }
        .nav-search {
            display: flex; align-items: center; background: rgba(255,255,255,0.08);
            border-radius: 20px; padding: 6px 14px; margin-left: 16px; min-width: 200px;
            transition: background 0.2s;
        }
        .nav-search:focus-within { background: rgba(255,255,255,0.12); border: 1px solid rgba(230,168,23,0.3); }
        .nav-search svg { width: 16px; height: 16px; fill: rgba(255,255,255,0.4); flex-shrink: 0; }
        .nav-search input {
            flex: 1; background: none; border: none; outline: none;
            font-size: 13px; color: #fff; margin-left: 8px;
        }
        .nav-search input::placeholder { color: rgba(255,255,255,0.3); }
        .nav-right { display: flex; align-items: center; gap: 10px; margin-left: 12px; }
        .nav-vip {
            padding: 5px 14px; border-radius: 16px; font-size: 12px; font-weight: 500;
            background: linear-gradient(135deg, #e6a817, #f0c040); color: #1a1200;
            cursor: pointer; white-space: nowrap; transition: transform 0.2s;
        }
        .nav-vip:hover { transform: scale(1.05); }
        .nav-login {
            padding: 6px 16px; border-radius: 20px; font-size: 13px;
            background: rgba(255,255,255,0.1); color: #fff; cursor: pointer; white-space: nowrap;
        }
        .nav-login:hover { background: rgba(255,255,255,0.18); }
        .nav-avatar {
            width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: none; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 500; color: #fff; position: relative;
        }
        .avatar-menu {
            display: none; position: absolute; top: 100%; right: 0; margin-top: 8px;
            background: #1a1a24; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; padding: 8px; min-width: 160px; z-index: 100;
        }
        .nav-avatar:hover .avatar-menu { display: block; }
        .avatar-menu a {
            display: flex; align-items: center; gap: 8px; padding: 10px 14px;
            border-radius: 8px; font-size: 13px; color: rgba(255,255,255,0.7); transition: 0.2s;
        }
        .avatar-menu a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .avatar-menu a.logout { color: #fb7299; }
        .mobile-menu-btn { display: none; background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; }

        /* ===== Banner轮播 ===== */
        .banner-section { margin-top: 56px; position: relative; height: 420px; overflow: hidden; }
        .banner-track { display: flex; height: 100%; transition: transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94); }
        .banner-slide { min-width: 100%; height: 100%; display: flex; position: relative; cursor: pointer; }
        .banner-bg {
            position: absolute; inset: 0; background-size: cover; background-position: center;
            filter: blur(40px) brightness(0.25); transform: scale(1.3);
        }
        .banner-content {
            position: relative; z-index: 1; display: flex; width: 100%;
            max-width: 1200px; margin: 0 auto; padding: 40px 40px; gap: 28px; align-items: center;
        }
        .banner-poster {
            flex-shrink: 0; width: 220px; height: 310px; border-radius: 12px; overflow: hidden;
            box-shadow: 0 16px 48px rgba(0,0,0,0.6);
        }
        .banner-poster img { width: 100%; height: 100%; object-fit: cover; }
        .banner-info { flex: 1; padding: 20px 0; }
        .banner-title {
            font-size: 32px; font-weight: 700; color: #fff; line-height: 1.3; margin-bottom: 12px;
            text-shadow: 0 2px 16px rgba(0,0,0,0.5);
        }
        .banner-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
        .banner-score {
            background: rgba(230,168,23,0.15); color: #e6a817; padding: 3px 10px;
            border-radius: 6px; font-size: 14px; font-weight: 600;
        }
        .banner-tag {
            padding: 3px 10px; border-radius: 4px; font-size: 12px;
            background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
        }
        .banner-tag.vip { background: rgba(230,168,23,0.2); color: #e6a817; }
        .banner-desc {
            font-size: 14px; color: rgba(255,255,255,0.5); line-height: 1.8;
            margin-bottom: 20px; max-width: 480px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .banner-actions { display: flex; gap: 12px; }
        .btn-play {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 28px; border-radius: 22px; font-size: 14px; font-weight: 500;
            background: linear-gradient(135deg, #e6a817, #f0c040); color: #1a1200;
            border: none; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-play:hover { transform: scale(1.05); box-shadow: 0 4px 16px rgba(230,168,23,0.35); }
        .btn-play svg { width: 16px; height: 16px; fill: #1a1200; }
        .btn-detail {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 24px; border-radius: 22px; font-size: 14px;
            background: rgba(255,255,255,0.1); color: #fff; cursor: pointer;
            border: 1px solid rgba(255,255,255,0.15); transition: background 0.2s;
        }
        .btn-detail:hover { background: rgba(255,255,255,0.18); }
        .banner-dots {
            position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 6px; z-index: 10;
        }
        .banner-dot {
            width: 8px; height: 8px; border-radius: 4px; background: rgba(255,255,255,0.25);
            cursor: pointer; transition: all 0.3s;
        }
        .banner-dot.active { width: 24px; background: #e6a817; }

        /* ===== 筛选栏 ===== */
        .section-wrap { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .filter-bar {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px; padding: 16px 20px; margin: 24px 0 20px;
        }
        .filter-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
        .filter-row:last-child { margin-bottom: 0; }
        .filter-label { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.4); min-width: 40px; padding-top: 5px; }
        .filter-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .ftag {
            padding: 4px 14px; border-radius: 16px; font-size: 12px;
            color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.04);
            cursor: pointer; transition: all 0.2s; border: 1px solid transparent;
        }
        .ftag:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .ftag.active { background: rgba(230,168,23,0.12); color: #e6a817; border-color: rgba(230,168,23,0.3); }

        /* ===== 排序 + 搜索 ===== */
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; }
        .sort-tabs { display: flex; gap: 2px; background: rgba(255,255,255,0.04); border-radius: 20px; padding: 3px; border: 1px solid rgba(255,255,255,0.06); }
        .sort-tab {
            padding: 6px 16px; border-radius: 16px; font-size: 13px;
            color: rgba(255,255,255,0.5); cursor: pointer; transition: all 0.2s;
        }
        .sort-tab.active { background: #e6a817; color: #1a1200; font-weight: 500; }
        .kw-search {
            display: flex; align-items: center; background: rgba(255,255,255,0.06);
            border-radius: 20px; padding: 6px 14px; max-width: 280px; border: 1px solid rgba(255,255,255,0.06);
        }
        .kw-search:focus-within { border-color: rgba(230,168,23,0.3); }
        .kw-search svg { width: 14px; height: 14px; fill: rgba(255,255,255,0.3); flex-shrink: 0; }
        .kw-search input {
            flex: 1; background: none; border: none; outline: none;
            font-size: 13px; color: #fff; margin-left: 8px;
        }
        .kw-search input::placeholder { color: rgba(255,255,255,0.25); }

        /* ===== 主内容区 ===== */
        .main-content { display: flex; gap: 24px; }
        .main-left { flex: 1; min-width: 0; }
        .main-right { width: 280px; flex-shrink: 0; }

        /* ===== 视频网格 ===== */
        .video-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 16px;
        }
        .vcard { cursor: pointer; transition: transform 0.3s; }
        .vcard:hover { transform: translateY(-6px); }
        .vcard:hover .card-poster img { transform: scale(1.06); }
        .card-poster {
            position: relative; border-radius: 10px; overflow: hidden;
            padding-top: 140%; background: #1a1a24;
        }
        .card-poster img {
            position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s;
        }
        .card-poster .play-icon {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s;
        }
        .vcard:hover .play-icon { opacity: 1; }
        .play-icon svg { width: 40px; height: 40px; fill: rgba(255,255,255,0.9); filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3)); }
        .tag-vip {
            position: absolute; top: 8px; left: 8px;
            padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;
            background: linear-gradient(135deg, #e6a817, #f0c040); color: #1a1200;
        }
        .tag-quality {
            position: absolute; top: 8px; right: 8px;
            padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;
            color: #e6a817; background: rgba(0,0,0,0.6);
        }
        .tag-ep {
            position: absolute; bottom: 8px; right: 8px;
            padding: 2px 8px; border-radius: 4px; font-size: 11px;
            background: rgba(0,0,0,0.7); color: rgba(255,255,255,0.8);
        }
        .tag-score {
            position: absolute; bottom: 8px; left: 8px;
            padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;
            color: #e6a817; background: rgba(0,0,0,0.7);
        }
        .card-title {
            margin-top: 8px; font-size: 14px; font-weight: 500; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
            color: rgba(255,255,255,0.85); transition: color 0.2s;
        }
        .vcard:hover .card-title { color: #e6a817; }
        .card-meta {
            margin-top: 4px; font-size: 12px; color: rgba(255,255,255,0.3);
            display: flex; gap: 8px;
        }

        /* ===== 排行榜 ===== */
        .rank-panel {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px; position: sticky; top: 72px;
        }
        .rank-head {
            padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.06);
            font-size: 15px; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 8px;
        }
        .rank-head svg { width: 18px; height: 18px; fill: #e6a817; }
        .rank-item {
            display: flex; align-items: center; gap: 10px; padding: 10px 16px;
            cursor: pointer; transition: background 0.2s;
        }
        .rank-item:hover { background: rgba(255,255,255,0.04); }
        .rank-num {
            width: 20px; height: 20px; border-radius: 4px; font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.06); flex-shrink: 0;
        }
        .rank-item:nth-child(2) .rank-num { background: rgba(255,71,87,0.2); color: #ff4757; }
        .rank-item:nth-child(3) .rank-num { background: rgba(255,99,72,0.2); color: #ff6348; }
        .rank-item:nth-child(4) .rank-num { background: rgba(255,165,2,0.2); color: #ffa502; }
        .rank-title {
            flex: 1; font-size: 13px; color: rgba(255,255,255,0.7);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .rank-views { font-size: 11px; color: rgba(255,255,255,0.25); flex-shrink: 0; }

        /* ===== 加载更多 ===== */
        .load-more { text-align: center; padding: 30px 0; }
        .load-more button {
            padding: 10px 36px; border-radius: 20px; font-size: 13px;
            background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.1); cursor: pointer; transition: all 0.2s;
        }
        .load-more button:hover { border-color: #e6a817; color: #e6a817; }
        .load-more button:disabled { opacity: 0.4; cursor: not-allowed; }
        .empty-state {
            text-align: center; padding: 60px 20px; color: rgba(255,255,255,0.25);
        }
        .empty-state svg { width: 48px; height: 48px; fill: rgba(255,255,255,0.1); margin-bottom: 12px; }

        /* ===== 页脚 ===== */
        .site-footer {
            padding: 40px 24px 24px; margin-top: 48px;
            border-top: 1px solid rgba(255,255,255,0.06);
            text-align: center; color: rgba(255,255,255,0.25); font-size: 12px; line-height: 2;
        }
        .site-footer a { color: rgba(255,255,255,0.35); transition: color 0.2s; }
        .site-footer a:hover { color: #e6a817; }

        /* ===== 移动端 ===== */
        @media (max-width: 1024px) {
            .main-right { display: none; }
            .video-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
        }
        @media (max-width: 768px) {
            .nav-bar { padding: 0 12px; height: 48px; }
            .nav-logo { font-size: 16px; margin-right: 12px; }
            .nav-links { display: none; }
            .nav-links.open {
                display: flex; position: absolute; top: 48px; left: 0; right: 0;
                background: rgba(15,15,20,0.98); flex-direction: column; padding: 12px;
                border-bottom: 1px solid rgba(255,255,255,0.06);
            }
            .nav-links.open .nav-link { padding: 12px 16px; }
            .mobile-menu-btn { display: block; }
            .nav-search { display: none; }
            .nav-vip { padding: 4px 10px; font-size: 11px; }
            .nav-login { padding: 5px 12px; font-size: 12px; }

            .banner-section { height: 320px; }
            .banner-content { padding: 24px 16px; gap: 16px; }
            .banner-poster { width: 120px; height: 170px; }
            .banner-title { font-size: 20px; }
            .banner-desc { font-size: 13px; -webkit-line-clamp: 1; }
            .btn-play { padding: 8px 20px; font-size: 13px; }
            .btn-detail { padding: 8px 16px; font-size: 13px; }

            .section-wrap { padding: 0 12px; }
            .filter-bar { padding: 12px; }
            .video-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .card-title { font-size: 13px; }
            .card-meta { font-size: 11px; }
            .top-bar { flex-direction: column; align-items: stretch; }
            .kw-search { max-width: 100%; }
        }
        @media (max-width: 480px) {
            .video-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .banner-poster { width: 100px; height: 140px; }
        }
    </style>
</head>
<body>

<!-- 导航栏 -->
<nav class="nav-bar">
    <a href="/" class="nav-logo">影视<span>中心</span></a>
    <button class="mobile-menu-btn" onclick="document.getElementById('navLinks').classList.toggle('open')">☰</button>
    <div class="nav-links" id="navLinks">
        <a href="/" class="nav-link">首页</a>
        <a href="/v" class="nav-link active">影视中心</a>
        <a href="/player" class="nav-link">播放器</a>
        <a href="/vip" class="nav-link">VIP会员</a>
    </div>
    <div class="nav-search">
        <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        <input type="text" placeholder="搜索视频、演员、导演..." id="navSearchInput">
    </div>
    <div class="nav-right">
        <a href="/vip" class="nav-vip">👑 VIP</a>
        <a href="/login" class="nav-login" id="navLoginBtn">登录</a>
        <div class="nav-avatar" id="navAvatar" style="display:none;">
            U
            <div class="avatar-menu">
                <a href="/user">👤 用户中心</a>
                <a href="/account">⚙ 账号设置</a>
                <a href="javascript:void(0)" class="logout" onclick="logout()">🚪 退出登录</a>
            </div>
        </div>
    </div>
</nav>

<!-- Banner轮播 -->
<div class="banner-section">
    <div class="banner-track" id="bannerTrack"></div>
    <div class="banner-dots" id="bannerDots"></div>
</div>

<!-- 筛选 + 内容 -->
<div class="section-wrap">
    <!-- 筛选栏 -->
    <div class="filter-bar">
        <div class="filter-row">
            <span class="filter-label">分类</span>
            <div class="filter-tags" id="fCategory"><span class="ftag active" data-v="">全部</span></div>
        </div>
        <div class="filter-row">
            <span class="filter-label">地区</span>
            <div class="filter-tags" id="fRegion"><span class="ftag active" data-v="">全部</span></div>
        </div>
        <div class="filter-row">
            <span class="filter-label">年份</span>
            <div class="filter-tags" id="fYear"><span class="ftag active" data-v="">全部</span></div>
        </div>
        <div class="filter-row">
            <span class="filter-label">类型</span>
            <div class="filter-tags" id="fGenre"><span class="ftag active" data-v="">全部</span></div>
        </div>
    </div>

    <!-- 排序 + 搜索 -->
    <div class="top-bar">
        <div class="sort-tabs">
            <span class="sort-tab active" data-s="new">最新</span>
            <span class="sort-tab" data-s="hot">最热</span>
            <span class="sort-tab" data-s="score">评分</span>
        </div>
        <div class="kw-search">
            <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="kwSearch" placeholder="搜索...">
        </div>
    </div>

    <!-- 主内容 -->
    <div class="main-content">
        <div class="main-left">
            <div class="video-grid" id="vGrid"></div>
            <div class="empty-state" id="emptyState" style="display:none;">
                <svg viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
                <p>没有找到相关视频</p>
            </div>
            <div class="load-more" id="loadMore" style="display:none;">
                <button id="lmBtn" onclick="loadMore()">加载更多</button>
            </div>
        </div>
        <div class="main-right">
            <div class="rank-panel">
                <div class="rank-head">
                    <svg viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    热门排行
                </div>
                <div id="rankList"></div>
            </div>
        </div>
    </div>
</div>

<!-- 页脚 -->
<footer class="site-footer">
    <div>© 2026 影视中心 · 仅供演示 · <a href="/player">播放器演示</a> · <a href="/vip">VIP会员</a></div>
    <div style="margin-top:4px;">Powered by DPlayer + Laravel</div>
</footer>

<script>
const S = { page: 1, pp: 24, last: 1, loading: false, cat: '', region: '', year: '', genre: '', sort: 'new', kw: '', ri: 0, rt: null };

function fmt(n) { if (!n) return '0'; if (n >= 10000) return (n / 10000).toFixed(1) + '万'; if (n >= 1000) return (n / 1000).toFixed(1) + 'k'; return n + ''; }
function fmtDur(s) { if (!s) return ''; return Math.floor(s / 60) + ':' + (s % 60 < 10 ? '0' : '') + (s % 60); }

// 登录状态
function initNav() {
    const t = localStorage.getItem('token'), u = localStorage.getItem('user');
    if (t && u) {
        try {
            const j = JSON.parse(u);
            document.getElementById('navLoginBtn').style.display = 'none';
            const av = document.getElementById('navAvatar');
            av.style.display = 'flex';
            av.childNodes[0].textContent = (j.nickname || j.username || 'U').charAt(0).toUpperCase();
        } catch (e) {}
    }
}
function logout() {
    const t = localStorage.getItem('token');
    if (t) fetch('/api/auth/logout', { method: 'POST', headers: { 'Authorization': 'Bearer ' + t } });
    localStorage.removeItem('token'); localStorage.removeItem('user'); location.reload();
}

// Banner轮播
async function initCarousel() {
    try {
        const r = await fetch('/api/movie/recommend'), d = await r.json();
        if (!d.success || !d.data.length) return;
        const track = document.getElementById('bannerTrack'), dots = document.getElementById('bannerDots');
        track.innerHTML = d.data.map((v, i) => {
            const vipTag = v.vip_level > 0 ? `<span class="banner-tag vip">${v.vip_level == 2 ? 'SVIP' : 'VIP'}</span>` : '';
            return `<div class="banner-slide" onclick="location.href='/v/${v.id}'">
                <div class="banner-bg" style="background-image:url('${v.cover}')"></div>
                <div class="banner-content">
                    <div class="banner-poster"><img src="${v.cover}" alt="${v.title}"></div>
                    <div class="banner-info">
                        <div class="banner-title">${v.title}</div>
                        <div class="banner-meta">
                            ${v.score ? `<span class="banner-score">⭐ ${v.score}</span>` : ''}
                            ${v.quality ? `<span class="banner-tag">${v.quality}</span>` : ''}
                            ${vipTag}
                            ${v.year ? `<span class="banner-tag">${v.year}</span>` : ''}
                            ${v.region ? `<span class="banner-tag">${v.region}</span>` : ''}
                        </div>
                        <div class="banner-desc">${v.description || v.genre + (v.actors ? ' · ' + v.actors : '')}</div>
                        <div class="banner-actions">
                            <button class="btn-play" onclick="event.stopPropagation();location.href='/v/${v.id}'">
                                <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> 立即观看
                            </button>
                            <button class="btn-detail" onclick="event.stopPropagation();location.href='/v/${v.id}'">详情</button>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
        dots.innerHTML = d.data.map((_, i) => `<div class="banner-dot${i === 0 ? ' active' : ''}" onclick="goSlide(${i})"></div>`).join('');
        if (d.data.length > 1) S.rt = setInterval(() => { S.ri = (S.ri + 1) % d.data.length; goSlide(S.ri); }, 5000);
    } catch (e) { console.error('轮播加载失败:', e); }
}
function goSlide(i) {
    document.getElementById('bannerTrack').style.transform = `translateX(-${i * 100}%)`;
    document.querySelectorAll('.banner-dot').forEach((d, j) => d.classList.toggle('active', j === i));
    S.ri = i;
}

// 筛选
async function initFilters() {
    try {
        const r = await fetch('/api/movie/filters'), d = await r.json();
        if (!d.success) return;
        const m = { category: 'fCategory', region: 'fRegion', year: 'fYear', genre: 'fGenre' };
        Object.entries(m).forEach(([k, id]) => {
            const el = document.getElementById(id);
            (d.data[k + 's'] || d.data[k] || []).forEach(v => {
                const t = document.createElement('span');
                t.className = 'ftag'; t.dataset.v = v; t.textContent = v;
                t.onclick = () => selFilter(id, t);
                el.appendChild(t);
            });
        });
    } catch (e) { console.error('筛选加载失败:', e); }
}
function selFilter(id, tag) {
    document.getElementById(id).querySelectorAll('.ftag').forEach(t => t.classList.remove('active'));
    tag.classList.add('active');
    S[id.slice(1).toLowerCase()] = tag.dataset.v;
    S.page = 1; loadVideos(true);
}

// 搜索
let st;
document.getElementById('kwSearch').addEventListener('input', e => {
    clearTimeout(st);
    st = setTimeout(() => { S.kw = e.target.value.trim(); S.page = 1; loadVideos(true); }, 400);
});
document.getElementById('navSearchInput').addEventListener('input', e => {
    clearTimeout(st);
    st = setTimeout(() => { S.kw = e.target.value.trim(); S.page = 1; document.getElementById('kwSearch').value = S.kw; loadVideos(true); }, 400);
});

// 排序
document.querySelectorAll('.sort-tab').forEach(t => {
    t.onclick = () => {
        document.querySelectorAll('.sort-tab').forEach(x => x.classList.remove('active'));
        t.classList.add('active'); S.sort = t.dataset.s; S.page = 1; loadVideos(true);
    };
});

// 加载视频
async function loadVideos(replace) {
    if (S.loading) return;
    S.loading = true;
    if (replace) document.getElementById('vGrid').innerHTML = '';
    const p = new URLSearchParams({ page: S.page, per_page: S.pp, sort: S.sort });
    if (S.cat) p.set('category', S.cat);
    if (S.region) p.set('region', S.region);
    if (S.year) p.set('year', S.year);
    if (S.genre) p.set('genre', S.genre);
    if (S.kw) p.set('keyword', S.kw);
    try {
        const r = await fetch('/api/movie/list?' + p), d = await r.json();
        if (!d.success) throw new Error();
        const vs = d.data.data || [];
        S.last = d.data.last_page || 1;
        const g = document.getElementById('vGrid'), e = document.getElementById('emptyState'), l = document.getElementById('loadMore');
        if (!vs.length && replace) { e.style.display = 'block'; l.style.display = 'none'; }
        else {
            e.style.display = 'none';
            vs.forEach(v => g.insertAdjacentHTML('beforeend', card(v)));
            l.style.display = S.page < S.last ? 'block' : 'none';
            document.getElementById('lmBtn').disabled = false;
        }
    } catch (e) { console.error('视频加载失败:', e); }
    S.loading = false;
}

function card(v) {
    const vip = v.vip_level == 2 ? '<span class="tag-vip">SVIP</span>' : v.vip_level == 1 ? '<span class="tag-vip">VIP</span>' : '';
    const q = v.quality && v.quality !== 'SD' ? `<span class="tag-quality">${v.quality}</span>` : '';
    const ep = v.episode_count > 1 ? `<span class="tag-ep">${v.is_ending ? v.episode_count + '集全' : '更新至' + v.episode_count + '集'}</span>` : (v.duration ? `<span class="tag-ep">${fmtDur(v.duration)}</span>` : '');
    const sc = v.score > 0 ? `<span class="tag-score">⭐ ${v.score}</span>` : '';
    return `<div class="vcard" onclick="location.href='/v/${v.id}'">
        <div class="card-poster">
            <img src="${v.cover || 'https://via.placeholder.com/300x420/1a1a24/666?text=No+Cover'}" alt="${v.title}" loading="lazy">
            <div class="play-icon"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
            ${vip}${q}${ep}${sc}
        </div>
        <div class="card-title">${v.title}</div>
        <div class="card-meta"><span>👁 ${fmt(v.views)}</span><span>❤ ${fmt(v.likes)}</span></div>
    </div>`;
}

function loadMore() {
    if (S.page >= S.last) return;
    S.page++;
    document.getElementById('lmBtn').disabled = true;
    loadVideos(false);
}

// 排行榜
async function loadRank() {
    try {
        const r = await fetch('/api/movie/ranking?limit=10'), d = await r.json();
        if (!d.success) return;
        document.getElementById('rankList').innerHTML = d.data.map((v, i) =>
            `<div class="rank-item" onclick="location.href='/v/${v.id}'">
                <span class="rank-num">${i + 1}</span>
                <span class="rank-title">${v.title}</span>
                <span class="rank-views">${fmt(v.views)}</span>
            </div>`
        ).join('');
    } catch (e) { console.error('排行加载失败:', e); }
}

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    initNav(); initCarousel(); initFilters(); loadVideos(true); loadRank();
});
</script>

@include('components.notice-popup')
</body>
</html>
