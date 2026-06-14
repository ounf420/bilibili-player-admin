<!DOCTYPE html>
<html lang="zh-CN" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>播放器 - 智能视频广告管理平台</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <style>
        /* ===== 暗色主题 ===== */
        [data-theme="dark"] {
            --bg: #050508;
            --bg-nav: rgba(5,5,8,0.85);
            --card: rgba(255,255,255,0.03);
            --card-hover: rgba(255,255,255,0.05);
            --border: rgba(255,255,255,0.06);
            --border-hover: rgba(0,240,255,0.2);
            --text: #e0e0e0;
            --text-dim: rgba(255,255,255,0.4);
            --text-title: #fff;
            --neon-cyan: #00f0ff;
            --neon-pink: #ff2d95;
            --neon-purple: #b44dff;
            --grid-opacity: 0.015;
            --glow-cyan: rgba(0,240,255,0.4);
            --glow-pink: rgba(255,45,149,0.4);
            --btn-ghost-border: rgba(255,255,255,0.12);
            --btn-ghost-hover: rgba(255,255,255,0.1);
            --feat-tag-bg: rgba(255,255,255,0.04);
            --feat-tag-border: rgba(255,255,255,0.04);
            --feat-tag-color: rgba(255,255,255,0.45);
            --tech-bg: rgba(255,255,255,0.03);
            --tech-color: rgba(255,255,255,0.5);
            --footer-color: rgba(255,255,255,0.2);
            --footer-link: rgba(255,255,255,0.3);
            --ad-name: rgba(255,255,255,0.85);
            --hero-gradient: radial-gradient(ellipse, rgba(0,240,255,0.08) 0%, transparent 50%),
                             radial-gradient(ellipse at 70% 40%, rgba(255,45,149,0.06) 0%, transparent 50%);
            --cta-gradient: radial-gradient(ellipse, rgba(255,45,149,0.06) 0%, transparent 60%);
            --switch-bg: rgba(255,255,255,0.08);
        }

        /* ===== 亮色主题 ===== */
        [data-theme="light"] {
            --bg: #f5f5f7;
            --bg-nav: rgba(255,255,255,0.85);
            --card: #fff;
            --card-hover: #fff;
            --border: rgba(0,0,0,0.08);
            --border-hover: rgba(0,161,214,0.3);
            --text: #333;
            --text-dim: #888;
            --text-title: #1a1a1a;
            --neon-cyan: #0099cc;
            --neon-pink: #e6006e;
            --neon-purple: #8833cc;
            --grid-opacity: 0.04;
            --glow-cyan: rgba(0,153,204,0.2);
            --glow-pink: rgba(230,0,110,0.2);
            --btn-ghost-border: rgba(0,0,0,0.12);
            --btn-ghost-hover: rgba(0,0,0,0.04);
            --feat-tag-bg: rgba(0,0,0,0.04);
            --feat-tag-border: rgba(0,0,0,0.06);
            --feat-tag-color: #666;
            --tech-bg: rgba(0,0,0,0.03);
            --tech-color: #666;
            --footer-color: rgba(0,0,0,0.3);
            --footer-link: rgba(0,0,0,0.4);
            --ad-name: #333;
            --hero-gradient: radial-gradient(ellipse, rgba(0,153,204,0.06) 0%, transparent 50%),
                             radial-gradient(ellipse at 70% 40%, rgba(230,0,110,0.04) 0%, transparent 50%);
            --cta-gradient: radial-gradient(ellipse, rgba(230,0,110,0.04) 0%, transparent 60%);
            --switch-bg: rgba(0,0,0,0.06);
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'SF Pro Display', -apple-system, 'PingFang SC', 'Microsoft YaHei', sans-serif;
            overflow-x: hidden;
            transition: background 0.4s, color 0.4s;
        }
        a { color: inherit; text-decoration: none; }

        /* 背景网格 */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(rgba(0,240,255,var(--grid-opacity)) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,240,255,var(--grid-opacity)) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ===== NAV ===== */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            height: 60px;
            background: var(--bg-nav);
            backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 32px;
            transition: background 0.4s, border-color 0.4s;
        }
        .nav-logo {
            font-size: 20px; font-weight: 800;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-pink));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-right: 40px;
        }
        .nav-links { display: flex; gap: 4px; flex: 1; }
        .nav-link {
            padding: 8px 18px; border-radius: 8px; font-size: 14px;
            color: var(--text-dim); cursor: pointer; transition: all 0.2s;
        }
        .nav-link:hover { color: var(--text-title); background: rgba(255,255,255,0.05); }
        .nav-link.active {
            color: var(--neon-cyan);
            background: rgba(0,240,255,0.06);
        }
        .nav-right { margin-left: auto; display: flex; gap: 12px; align-items: center; }
        .nav-login {
            padding: 7px 20px; border-radius: 8px; font-size: 13px;
            border: 1px solid rgba(0,240,255,0.3); color: var(--neon-cyan);
            cursor: pointer; transition: all 0.3s;
        }
        .nav-login:hover {
            background: rgba(0,240,255,0.1);
            box-shadow: 0 0 20px rgba(0,240,255,0.15);
        }

        /* 主题切换按钮 */
        .theme-switch {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--switch-bg);
            border: 1px solid var(--border);
            color: var(--text-dim);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s;
            font-size: 18px;
            position: relative;
        }
        .theme-switch:hover {
            background: rgba(0,240,255,0.1);
            border-color: var(--neon-cyan);
            color: var(--neon-cyan);
        }
        .theme-switch .icon-sun,
        .theme-switch .icon-moon {
            position: absolute;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [data-theme="dark"] .theme-switch .icon-sun {
            opacity: 0; transform: rotate(90deg) scale(0);
        }
        [data-theme="dark"] .theme-switch .icon-moon {
            opacity: 1; transform: rotate(0) scale(1);
        }
        [data-theme="light"] .theme-switch .icon-sun {
            opacity: 1; transform: rotate(0) scale(1);
        }
        [data-theme="light"] .theme-switch .icon-moon {
            opacity: 0; transform: rotate(-90deg) scale(0);
        }

        .mobile-btn { display: none; background: none; border: none; color: var(--text); font-size: 22px; cursor: pointer; }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            padding: 140px 32px 100px;
            text-align: center;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; top: 60px; left: 50%; transform: translateX(-50%);
            width: 800px; height: 500px;
            background: var(--hero-gradient);
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px; border-radius: 24px; font-size: 13px;
            background: rgba(0,240,255,0.06);
            border: 1px solid rgba(0,240,255,0.15);
            color: var(--neon-cyan);
            margin-bottom: 32px;
            animation: pulse-border 3s ease-in-out infinite;
        }
        @keyframes pulse-border {
            0%,100% { border-color: rgba(0,240,255,0.15); }
            50% { border-color: rgba(0,240,255,0.4); }
        }
        .hero h1 {
            font-size: 56px; font-weight: 800; line-height: 1.2;
            max-width: 750px; margin: 0 auto 20px;
            color: var(--text-title);
            transition: color 0.4s;
        }
        .hero h1 .cyan { color: var(--neon-cyan); text-shadow: 0 0 30px var(--glow-cyan); }
        .hero h1 .pink { color: var(--neon-pink); text-shadow: 0 0 30px var(--glow-pink); }
        .hero-sub {
            font-size: 17px; color: var(--text-dim); line-height: 1.8;
            max-width: 580px; margin: 0 auto 40px;
        }
        .hero-btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-glow {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px; border-radius: 12px; font-size: 15px; font-weight: 600;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
            color: #fff; cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 30px var(--glow-cyan);
        }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 50px var(--glow-cyan), 0 8px 30px rgba(0,0,0,0.3);
        }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px; border-radius: 12px; font-size: 15px;
            border: 1px solid var(--btn-ghost-border); color: var(--text-title);
            cursor: pointer; transition: all 0.3s;
        }
        .btn-ghost:hover {
            border-color: var(--neon-pink);
            background: var(--btn-ghost-hover);
            box-shadow: 0 0 20px var(--glow-pink);
        }

        /* ===== STATS ===== */
        .stats {
            display: flex; justify-content: center; gap: 60px;
            padding: 48px 24px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            transition: border-color 0.4s;
        }
        .stat { text-align: center; }
        .stat-num {
            font-size: 36px; font-weight: 800;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-pink));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .stat-label { font-size: 13px; color: var(--text-dim); margin-top: 6px; }

        /* ===== FEATURES ===== */
        .features {
            padding: 100px 32px;
            max-width: 1200px; margin: 0 auto;
        }
        .sec-header { text-align: center; margin-bottom: 60px; }
        .sec-tag {
            display: inline-block; padding: 5px 16px; border-radius: 8px;
            font-size: 12px; font-weight: 500;
            background: rgba(0,240,255,0.06); color: var(--neon-cyan);
            border: 1px solid rgba(0,240,255,0.12);
            margin-bottom: 16px;
        }
        .sec-title { font-size: 36px; font-weight: 700; color: var(--text-title); margin-bottom: 14px; transition: color 0.4s; }
        .sec-desc { font-size: 15px; color: var(--text-dim); max-width: 500px; margin: 0 auto; }

        .feat-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
        }
        .feat-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.4s;
            overflow: hidden;
        }
        .feat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--neon-cyan), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .feat-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 40px rgba(0,240,255,0.05);
        }
        .feat-card:hover::before { opacity: 1; }
        .feat-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 20px;
        }
        .feat-name { font-size: 18px; font-weight: 600; color: var(--text-title); margin-bottom: 10px; transition: color 0.4s; }
        .feat-desc { font-size: 13px; color: var(--text-dim); line-height: 1.8; }
        .feat-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 16px; }
        .feat-tag {
            padding: 3px 10px; border-radius: 6px; font-size: 11px;
            background: var(--feat-tag-bg); color: var(--feat-tag-color);
            border: 1px solid var(--feat-tag-border);
            transition: all 0.4s;
        }

        /* ===== AD TYPES ===== */
        .ad-section {
            padding: 80px 32px;
            max-width: 1200px; margin: 0 auto;
        }
        .ad-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
        }
        .ad-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s;
            cursor: default;
        }
        .ad-card:hover {
            border-color: rgba(255,45,149,0.25);
            background: rgba(255,45,149,0.03);
            box-shadow: 0 0 20px rgba(255,45,149,0.06);
        }
        .ad-icon { font-size: 32px; margin-bottom: 12px; }
        .ad-name { font-size: 14px; font-weight: 500; color: var(--ad-name); transition: color 0.4s; }
        .ad-sub { font-size: 11px; color: var(--text-dim); margin-top: 4px; }

        /* ===== TECH ===== */
        .tech {
            padding: 60px 32px;
            max-width: 1200px; margin: 0 auto;
            border-top: 1px solid var(--border);
            transition: border-color 0.4s;
        }
        .tech-grid {
            display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 36px;
        }
        .tech-chip {
            padding: 8px 22px; border-radius: 8px; font-size: 13px;
            background: var(--tech-bg);
            border: 1px solid var(--border);
            color: var(--tech-color);
            transition: all 0.3s;
        }
        .tech-chip:hover {
            border-color: var(--neon-cyan);
            color: var(--neon-cyan);
            background: rgba(0,240,255,0.04);
        }

        /* ===== CTA ===== */
        .cta {
            padding: 100px 32px; text-align: center;
            position: relative;
        }
        .cta::before {
            content: '';
            position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 600px; height: 400px;
            background: var(--cta-gradient);
            pointer-events: none;
        }
        .cta h2 { font-size: 32px; font-weight: 700; color: var(--text-title); margin-bottom: 14px; transition: color 0.4s; }
        .cta p { font-size: 15px; color: var(--text-dim); margin-bottom: 32px; }

        /* ===== FOOTER ===== */
        .footer {
            padding: 40px 32px 24px;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--footer-color);
            font-size: 12px; line-height: 2;
            transition: all 0.4s;
        }
        .footer a { color: var(--footer-link); transition: color 0.2s; }
        .footer a:hover { color: var(--neon-cyan); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .nav { padding: 0 16px; height: 52px; }
            .nav-logo { font-size: 17px; margin-right: 16px; }
            .nav-links { display: none; }
            .nav-links.open {
                display: flex; flex-direction: column;
                position: absolute; top: 52px; left: 0; right: 0;
                background: var(--bg-nav); padding: 12px;
                border-bottom: 1px solid var(--border);
            }
            .mobile-btn { display: block; }
            .hero { padding: 80px 16px 60px; }
            .hero h1 { font-size: 30px; }
            .hero-sub { font-size: 14px; }
            .stats { gap: 24px; flex-wrap: wrap; }
            .stat-num { font-size: 26px; }
            .feat-grid { grid-template-columns: 1fr; }
            .ad-grid { grid-template-columns: repeat(2, 1fr); }
            .btn-glow, .btn-ghost { padding: 12px 28px; font-size: 14px; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <a href="/" class="nav-logo">播放器系统</a>
    <button class="mobile-btn" onclick="document.getElementById('navLinks').classList.toggle('open')">☰</button>
    <div class="nav-links" id="navLinks">
        <a href="/" class="nav-link active">首页</a>
        <a href="/player" class="nav-link">播放器</a>
    </div>
    <div class="nav-right">
        <!-- 主题切换 -->
        <div class="theme-switch" onclick="toggleTheme()" title="切换主题">
            <i class="ri-sun-line icon-sun"></i>
            <i class="ri-moon-line icon-moon"></i>
        </div>
        <a href="/login" class="nav-login" id="navLogin">登录</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">⚡ 智能视频广告管理平台</div>
    <h1>打造专属<span class="cyan">视频广告</span><br><span class="pink">生态系统</span></h1>
    <p class="hero-sub">集成17种广告类型的全栈视频播放器管理系统<br>支持多格式视频播放、智能广告投放、用户管理与数据分析</p>
    <div class="hero-btns">
        <a href="/player" class="btn-glow"><i class="ri-play-fill"></i> 立即体验</a>
        <a href="/user-center" class="btn-ghost"><i class="ri-dashboard-line"></i> 用户中心</a>
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stat"><div class="stat-num">17</div><div class="stat-label">广告类型</div></div>
    <div class="stat"><div class="stat-num">3</div><div class="stat-label">视频格式</div></div>
    <div class="stat"><div class="stat-num">6</div><div class="stat-label">内容分类</div></div>
    <div class="stat"><div class="stat-num">∞</div><div class="stat-label">播放次数</div></div>
</div>

<!-- FEATURES -->
<section class="features">
    <div class="sec-header">
        <div class="sec-tag">核心功能</div>
        <h2 class="sec-title">全方位视频管理能力</h2>
        <p class="sec-desc">从视频播放到广告投放，一站式解决</p>
    </div>
    <div class="feat-grid">
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(0,240,255,0.08)">🎬</div>
            <div class="feat-name">多格式播放器</div>
            <div class="feat-desc">基于DPlayer深度定制，支持MP4、M3U8(HLS)、FLV三种格式，自适应码率切换</div>
            <div class="feat-tags">
                <span class="feat-tag">MP4</span><span class="feat-tag">M3U8</span><span class="feat-tag">FLV</span>
            </div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(255,45,149,0.08)">📢</div>
            <div class="feat-name">17种广告类型</div>
            <div class="feat-desc">覆盖视频全生命周期：开屏、前贴片、中贴片、后贴片、暂停、跑马灯、角标等</div>
            <div class="feat-tags">
                <span class="feat-tag">开屏</span><span class="feat-tag">贴片</span><span class="feat-tag">暂停</span><span class="feat-tag">角标</span>
            </div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(180,77,255,0.08)">👥</div>
            <div class="feat-name">用户体系</div>
            <div class="feat-desc">完整的注册/登录流程，账号中心管理，管理员与普通用户权限隔离</div>
            <div class="feat-tags">
                <span class="feat-tag">注册登录</span><span class="feat-tag">权限管理</span>
            </div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(0,240,255,0.08)">📊</div>
            <div class="feat-name">数据后台</div>
            <div class="feat-desc">基于Filament v5构建，视频管理、广告管理、用户管理、播放统计一应俱全</div>
            <div class="feat-tags">
                <span class="feat-tag">Filament v5</span><span class="feat-tag">数据统计</span>
            </div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(180,77,255,0.08)">🔗</div>
            <div class="feat-name">第三方登录</div>
            <div class="feat-desc">集成Laravel Socialite，支持微信、QQ、GitHub等13种第三方登录方式</div>
            <div class="feat-tags">
                <span class="feat-tag">微信</span><span class="feat-tag">QQ</span><span class="feat-tag">GitHub</span>
            </div>
        </div>
        <div class="feat-card">
            <div class="feat-icon" style="background:rgba(255,45,149,0.08)">🛡️</div>
            <div class="feat-name">安全防护</div>
            <div class="feat-desc">广告拦截器智能规避，Token认证鉴权，管理员账号体系隔离保护</div>
            <div class="feat-tags">
                <span class="feat-tag">拦截器规避</span><span class="feat-tag">Token认证</span>
            </div>
        </div>
    </div>
</section>

<!-- AD TYPES -->
<section class="ad-section">
    <div class="sec-header">
        <div class="sec-tag">广告矩阵</div>
        <h2 class="sec-title">17种广告类型全覆盖</h2>
        <p class="sec-desc">从视频打开到关闭，每个环节都有对应的广告方案</p>
    </div>
    <div class="ad-grid">
        <div class="ad-card"><div class="ad-icon">🚀</div><div class="ad-name">开屏广告</div><div class="ad-sub">视频加载前展示</div></div>
        <div class="ad-card"><div class="ad-icon">▶️</div><div class="ad-name">前贴片广告</div><div class="ad-sub">播放前强制观看</div></div>
        <div class="ad-card"><div class="ad-icon">⏸️</div><div class="ad-name">中贴片广告</div><div class="ad-sub">播放中定时插入</div></div>
        <div class="ad-card"><div class="ad-icon">🔚</div><div class="ad-name">后贴片广告</div><div class="ad-sub">播放结束后展示</div></div>
        <div class="ad-card"><div class="ad-icon">⏯️</div><div class="ad-name">暂停广告</div><div class="ad-sub">暂停时展示</div></div>
        <div class="ad-card"><div class="ad-icon">📰</div><div class="ad-name">跑马灯广告</div><div class="ad-sub">滚动文字提醒</div></div>
        <div class="ad-card"><div class="ad-icon">🏷️</div><div class="ad-name">角标广告</div><div class="ad-sub">角落常驻展示</div></div>
        <div class="ad-card"><div class="ad-icon">🪟</div><div class="ad-name">浮窗广告</div><div class="ad-sub">可关闭悬浮窗</div></div>
        <div class="ad-card"><div class="ad-icon">🖼️</div><div class="ad-name">画中画广告</div><div class="ad-sub">视频缩小+广告全屏</div></div>
        <div class="ad-card"><div class="ad-icon">📱</div><div class="ad-name">扫码贴片</div><div class="ad-sub">二维码引导转化</div></div>
        <div class="ad-card"><div class="ad-icon">🎯</div><div class="ad-name">横幅广告</div><div class="ad-sub">顶部/底部横幅</div></div>
        <div class="ad-card"><div class="ad-icon">💧</div><div class="ad-name">水印广告</div><div class="ad-sub">品牌水印常驻</div></div>
        <div class="ad-card"><div class="ad-icon">🔔</div><div class="ad-name">弹窗广告</div><div class="ad-sub">定时弹窗提醒</div></div>
        <div class="ad-card"><div class="ad-icon">📊</div><div class="ad-name">进度条广告</div><div class="ad-sub">进度条上方展示</div></div>
        <div class="ad-card"><div class="ad-icon">🎭</div><div class="ad-name">片尾推荐</div><div class="ad-sub">播放结束推荐</div></div>
        <div class="ad-card"><div class="ad-icon">⏰</div><div class="ad-name">定时广告</div><div class="ad-sub">按时间段投放</div></div>
    </div>
</section>

<!-- TECH -->
<section class="tech">
    <div class="sec-header">
        <div class="sec-tag">技术架构</div>
        <h2 class="sec-title">现代化技术栈</h2>
    </div>
    <div class="tech-grid">
        <span class="tech-chip">Laravel 13</span>
        <span class="tech-chip">Filament v5</span>
        <span class="tech-chip">DPlayer</span>
        <span class="tech-chip">HLS.js</span>
        <span class="tech-chip">FLV.js</span>
        <span class="tech-chip">MySQL 8.0</span>
        <span class="tech-chip">PHP 8.3</span>
        <span class="tech-chip">Nginx</span>
        <span class="tech-chip">Socialite</span>
        <span class="tech-chip">Token Auth</span>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <h2>开始体验</h2>
    <p>体验智能广告播放系统</p>
    <div class="hero-btns">
        <a href="/player" class="btn-glow"><i class="ri-play-fill"></i> 播放器演示</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div>© 2026 播放器 · 智能视频广告管理平台 · <a href="/player">播放器演示</a></div>
    <div>Powered by Laravel + DPlayer + Filament</div>
</footer>

<script>
// ===== 主题切换系统 =====
(function() {
    const html = document.documentElement;
    const saved = localStorage.getItem('theme-mode'); // 'dark' | 'light' | 'auto' | null

    function getAutoTheme() {
        const hour = new Date().getHours();
        // 6:00-18:00 亮色，其余暗色
        return (hour >= 6 && hour < 18) ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
    }

    // 初始化
    if (saved === 'auto' || !saved) {
        applyTheme(getAutoTheme());
    } else {
        applyTheme(saved);
    }

    // 自动模式下每分钟检查一次
    if (saved === 'auto' || !saved) {
        setInterval(function() {
            if (localStorage.getItem('theme-mode') === 'auto' || !localStorage.getItem('theme-mode')) {
                applyTheme(getAutoTheme());
            }
        }, 60000);
    }
})();

function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const modes = ['dark', 'light', 'auto'];
    const saved = localStorage.getItem('theme-mode') || 'auto';
    const idx = modes.indexOf(saved);
    const next = modes[(idx + 1) % modes.length];

    localStorage.setItem('theme-mode', next);

    if (next === 'auto') {
        const hour = new Date().getHours();
        html.setAttribute('data-theme', (hour >= 6 && hour < 18) ? 'light' : 'dark');
    } else {
        html.setAttribute('data-theme', next);
    }

    // 提示
    const tips = { dark: '🌙 暗色模式', light: '☀️ 亮色模式', auto: '🔄 自动切换' };
    showToast(tips[next]);
}

function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);padding:10px 24px;background:rgba(0,0,0,0.8);color:#fff;border-radius:8px;font-size:14px;z-index:9999;animation:fadeInUp .3s ease;';
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; }, 1500);
    setTimeout(() => t.remove(), 2000);
}

// 登录状态
(function() {
    const t = localStorage.getItem('token'), u = localStorage.getItem('user');
    if (t && u) {
        try {
            const j = JSON.parse(u);
            document.getElementById('navLogin').textContent = j.nickname || j.username || '用户';
            document.getElementById('navLogin').href = '/user';
        } catch(e) {}
    }
})();
</script>

</body>
</html>