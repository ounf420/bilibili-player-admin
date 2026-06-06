<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>播放器 - 智能视频广告管理平台</title>
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
            padding: 8px 16px; border-radius: 8px; font-size: 14px;
            color: rgba(255,255,255,0.6); cursor: pointer; white-space: nowrap; transition: all 0.2s;
            position: relative;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-link.active { color: #fff; font-weight: 500; }
        .nav-link.active::after {
            content: ''; position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%);
            width: 16px; height: 2px; background: #e6a817; border-radius: 1px;
        }
        .nav-right { display: flex; align-items: center; gap: 10px; margin-left: auto; }
            padding: 5px 14px; border-radius: 16px; font-size: 12px; font-weight: 500;
            background: linear-gradient(135deg, #e6a817, #f0c040); color: #1a1200;
            cursor: pointer; white-space: nowrap; transition: transform 0.2s;
        }
        .nav-login {
            padding: 6px 16px; border-radius: 20px; font-size: 13px;
            background: rgba(255,255,255,0.1); color: #fff; cursor: pointer; white-space: nowrap;
        }
        .nav-login:hover { background: rgba(255,255,255,0.18); }
        .nav-avatar {
            width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: none; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 500; color: #fff;
        }
        .mobile-menu-btn { display: none; background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; }

        /* ===== Hero区域 ===== */
        .hero-section {
            margin-top: 56px; padding: 100px 24px 80px; text-align: center;
            background: radial-gradient(ellipse at 50% 0%, rgba(230,168,23,0.08) 0%, transparent 60%);
            position: relative; overflow: hidden;
        }
        .hero-section::before {
            content: ''; position: absolute; top: -200px; left: 50%; transform: translateX(-50%);
            width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, rgba(230,168,23,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 16px; border-radius: 20px; font-size: 13px;
            background: rgba(230,168,23,0.1); color: #e6a817; margin-bottom: 24px;
            border: 1px solid rgba(230,168,23,0.15);
        }
        .hero-title {
            font-size: 48px; font-weight: 800; color: #fff; line-height: 1.3;
            margin-bottom: 16px; max-width: 700px; margin-left: auto; margin-right: auto;
        }
        .hero-title span { color: #e6a817; }
        .hero-sub {
            font-size: 18px; color: rgba(255,255,255,0.45); line-height: 1.8;
            max-width: 560px; margin: 0 auto 36px;
        }
        .hero-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px; border-radius: 28px; font-size: 16px; font-weight: 500;
            background: linear-gradient(135deg, #e6a817, #f0c040); color: #1a1200;
            border: none; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(230,168,23,0.3); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px; border-radius: 28px; font-size: 16px;
            background: rgba(255,255,255,0.06); color: #fff; cursor: pointer;
            border: 1px solid rgba(255,255,255,0.12); transition: all 0.2s;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); }

        /* ===== 数据统计 ===== */
        .stats-bar {
            display: flex; justify-content: center; gap: 48px; padding: 40px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .stat-item { text-align: center; }
        .stat-num { font-size: 32px; font-weight: 700; color: #e6a817; }
        .stat-label { font-size: 13px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        /* ===== 功能模块 ===== */
        .features-section { padding: 80px 24px; max-width: 1100px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 48px; }
        .section-tag {
            display: inline-block; padding: 4px 14px; border-radius: 12px; font-size: 12px;
            background: rgba(230,168,23,0.1); color: #e6a817; margin-bottom: 12px;
        }
        .section-title { font-size: 32px; font-weight: 700; color: #fff; margin-bottom: 12px; }
        .section-desc { font-size: 15px; color: rgba(255,255,255,0.4); max-width: 500px; margin: 0 auto; }

        .features-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
        }
        .feature-card {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px; padding: 28px; transition: all 0.3s;
            cursor: default;
        }
        .feature-card:hover {
            background: rgba(255,255,255,0.05); border-color: rgba(230,168,23,0.15);
            transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 24px;
            margin-bottom: 16px;
        }
        .feature-name { font-size: 18px; font-weight: 600; color: #fff; margin-bottom: 8px; }
        .feature-desc { font-size: 13px; color: rgba(255,255,255,0.4); line-height: 1.7; }
        .feature-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
        .feature-tag {
            padding: 3px 10px; border-radius: 10px; font-size: 11px;
            background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.45);
        }

        /* ===== 广告类型展示 ===== */
        .ad-types-section { padding: 60px 24px 80px; max-width: 1100px; margin: 0 auto; }
        .ad-types-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .ad-type-card {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px; padding: 20px; text-align: center; transition: all 0.3s;
        }
        .ad-type-card:hover {
            border-color: rgba(230,168,23,0.2); background: rgba(230,168,23,0.03);
        }
        .ad-type-icon { font-size: 28px; margin-bottom: 10px; }
        .ad-type-name { font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.8); }
        .ad-type-sub { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 4px; }

        /* ===== 技术栈 ===== */
        .tech-section {
            padding: 60px 24px; max-width: 1100px; margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.04);
        }
        .tech-grid { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 32px; }
        .tech-chip {
            padding: 8px 20px; border-radius: 20px; font-size: 13px;
            background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.5);
            border: 1px solid rgba(255,255,255,0.06);
        }

        /* ===== CTA ===== */
        .cta-section {
            padding: 80px 24px; text-align: center;
            background: radial-gradient(ellipse at 50% 100%, rgba(230,168,23,0.06) 0%, transparent 60%);
        }
        .cta-title { font-size: 28px; font-weight: 700; color: #fff; margin-bottom: 12px; }
        .cta-desc { font-size: 15px; color: rgba(255,255,255,0.4); margin-bottom: 28px; }

        /* ===== 页脚 ===== */
        .site-footer {
            padding: 40px 24px 24px; border-top: 1px solid rgba(255,255,255,0.06);
            text-align: center; color: rgba(255,255,255,0.25); font-size: 12px; line-height: 2;
        }
        .site-footer a { color: rgba(255,255,255,0.35); transition: color 0.2s; }
        .site-footer a:hover { color: #e6a817; }

        /* ===== 移动端 ===== */
        @media (max-width: 768px) {
            .nav-bar { padding: 0 12px; height: 48px; }
            .nav-logo { font-size: 16px; margin-right: 12px; }
            .nav-links { display: none; }
            .nav-links.open {
                display: flex; position: absolute; top: 48px; left: 0; right: 0;
                background: rgba(15,15,20,0.98); flex-direction: column; padding: 12px;
                border-bottom: 1px solid rgba(255,255,255,0.06);
            }
            .mobile-menu-btn { display: block; }
            .nav-login { padding: 5px 12px; font-size: 12px; }

            .hero-section { padding: 60px 16px 50px; }
            .hero-title { font-size: 28px; }
            .hero-sub { font-size: 14px; }
            .btn-primary, .btn-secondary { padding: 12px 24px; font-size: 14px; }

            .stats-bar { gap: 24px; flex-wrap: wrap; }
            .stat-num { font-size: 24px; }

            .features-grid { grid-template-columns: 1fr; }
            .ad-types-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<!-- 导航栏 -->
<nav class="nav-bar">
    <a href="/" class="nav-logo">播放器<span>系统</span></a>
    <button class="mobile-menu-btn" onclick="document.getElementById('navLinks').classList.toggle('open')">☰</button>
    <div class="nav-links" id="navLinks">
        <a href="/" class="nav-link active">首页</a>
        
        <a href="/player" class="nav-link">播放器</a>
        
    </div>
    <div class="nav-right">
        
        <a href="/login" class="nav-login" id="navLoginBtn">登录</a>
        <a href="/user" class="nav-avatar" id="navAvatar" style="display:none;">U</a>
    </div>
</nav>

<!-- Hero -->
<section class="hero-section">
    <div class="hero-badge">🎬 智能视频广告管理平台</div>
    <h1 class="hero-title">打造专属<span>视频广告</span>生态系统</h1>
    <p class="hero-sub">集成17种广告类型的全栈视频播放器管理系统，支持多格式视频播放、智能广告投放、用户管理与数据分析</p>
    <div class="hero-actions">
        
        <a href="/player" class="btn-secondary">▶ 播放器演示</a>
    </div>
</section>

<!-- 数据统计 -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-num">17</div>
        <div class="stat-label">广告类型</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">3</div>
        <div class="stat-label">视频格式</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">6</div>
        <div class="stat-label">内容分类</div>
    </div>
    <div class="stat-item">
        <div class="stat-num">∞</div>
        <div class="stat-label">播放次数</div>
    </div>
</div>

<!-- 功能模块 -->
<section class="features-section">
    <div class="section-header">
        <div class="section-tag">核心功能</div>
        <h2 class="section-title">全方位视频管理能力</h2>
        <p class="section-desc">从视频播放到广告投放，从用户管理到数据洞察，一站式解决</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(230,168,23,0.1);">🎬</div>
            <div class="feature-name">多格式播放器</div>
            <div class="feature-desc">基于DPlayer深度定制，支持MP4、M3U8(HLS)、FLV三种格式，自适应码率切换，支持弹幕、截图、画中画</div>
            <div class="feature-tags">
                <span class="feature-tag">MP4</span>
                <span class="feature-tag">M3U8</span>
                <span class="feature-tag">FLV</span>
                <span class="feature-tag">HLS</span>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(102,126,234,0.1);">📢</div>
            <div class="feature-name">17种广告类型</div>
            <div class="feature-desc">覆盖视频全生命周期的广告方案：开屏、前贴片、中贴片、后贴片、暂停、跑马灯、角标、浮窗、画中画等</div>
            <div class="feature-tags">
                <span class="feature-tag">开屏广告</span>
                <span class="feature-tag">贴片广告</span>
                <span class="feature-tag">暂停广告</span>
                <span class="feature-tag">角标广告</span>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(251,114,153,0.1);">👥</div>
            <div class="feature-name">用户体系</div>
            <div class="feature-desc">完整的注册/登录/找回密码流程，账号中心管理，管理员与普通用户权限隔离</div>
            <div class="feature-tags">
                <span class="feature-tag">注册登录</span>
                
                <span class="feature-tag">权限管理</span>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(0,190,6,0.1);">📊</div>
            <div class="feature-name">数据后台</div>
            <div class="feature-desc">基于Filament v5构建的管理后台，视频管理、广告管理、用户管理、播放统计、公告系统一应俱全</div>
            <div class="feature-tags">
                <span class="feature-tag">Filament v5</span>
                <span class="feature-tag">数据统计</span>
                <span class="feature-tag">可视化</span>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(118,75,162,0.1);">🔗</div>
            <div class="feature-name">第三方登录</div>
            <div class="feature-desc">集成Laravel Socialite，支持微信、QQ、GitHub、Google等13种第三方登录方式，后台一键配置</div>
            <div class="feature-tags">
                <span class="feature-tag">微信</span>
                <span class="feature-tag">QQ</span>
                <span class="feature-tag">GitHub</span>
                <span class="feature-tag">Google</span>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(255,165,2,0.1);">🛡️</div>
            <div class="feature-name">安全防护</div>
            <div class="feature-desc">广告拦截器智能规避（媒体引擎关键词替代方案），Token认证鉴权，管理员账号体系隔离保护</div>
            <div class="feature-tags">
                <span class="feature-tag">拦截器规避</span>
                <span class="feature-tag">Token认证</span>
                <span class="feature-tag">权限隔离</span>
            </div>
        </div>
    </div>
</section>

<!-- 广告类型展示 -->
<section class="ad-types-section">
    <div class="section-header">
        <div class="section-tag">广告矩阵</div>
        <h2 class="section-title">17种广告类型全覆盖</h2>
        <p class="section-desc">从视频打开到关闭，每个环节都有对应的广告方案</p>
    </div>
    <div class="ad-types-grid">
        <div class="ad-type-card"><div class="ad-type-icon">🚀</div><div class="ad-type-name">开屏广告</div><div class="ad-type-sub">视频加载前展示</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">▶️</div><div class="ad-type-name">前贴片广告</div><div class="ad-type-sub">播放前强制观看</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">⏸️</div><div class="ad-type-name">中贴片广告</div><div class="ad-type-sub">播放中定时插入</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🔚</div><div class="ad-type-name">后贴片广告</div><div class="ad-type-sub">播放结束后展示</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">⏯️</div><div class="ad-type-name">暂停广告</div><div class="ad-type-sub">暂停时画中画展示</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">📰</div><div class="ad-type-name">跑马灯广告</div><div class="ad-type-sub">滚动文字提醒</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🏷️</div><div class="ad-type-name">角标广告</div><div class="ad-type-sub">角落常驻展示</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🪟</div><div class="ad-type-name">浮窗广告</div><div class="ad-type-sub">可关闭悬浮窗</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🖼️</div><div class="ad-type-name">画中画广告</div><div class="ad-type-sub">视频缩小+广告全屏</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">📱</div><div class="ad-type-name">扫码贴片</div><div class="ad-type-sub">二维码引导转化</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🎯</div><div class="ad-type-name">横幅广告</div><div class="ad-type-sub">顶部/底部横幅</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">💧</div><div class="ad-type-name">水印广告</div><div class="ad-type-sub">品牌水印常驻</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🔔</div><div class="ad-type-name">弹窗广告</div><div class="ad-type-sub">定时弹窗提醒</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">📊</div><div class="ad-type-name">进度条广告</div><div class="ad-type-sub">进度条上方展示</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">🎭</div><div class="ad-type-name">片尾推荐</div><div class="ad-type-sub">播放结束推荐内容</div></div>
        <div class="ad-type-card"><div class="ad-type-icon">⏰</div><div class="ad-type-name">定时广告</div><div class="ad-type-sub">按时间段投放</div></div>
    </div>
</section>

<!-- 技术栈 -->
<section class="tech-section">
    <div class="section-header">
        <div class="section-tag">技术架构</div>
        <h2 class="section-title">现代化技术栈</h2>
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
        <span class="tech-chip">Laravel Socialite</span>
        <span class="tech-chip">Token Auth</span>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2 class="cta-title">开始体验</h2>
    <p class="cta-desc">体验智能广告播放系统</p>
    <div class="hero-actions">
        
        
    </div>
</section>

<!-- 页脚 -->
<footer class="site-footer">
    <div>© 2026 播放器 · 智能视频广告管理平台 · <a href="/player">播放器演示</a></div>
    <div style="margin-top:4px;">Powered by Laravel + DPlayer + Filament</div>
</footer>

<script>
// 登录状态
(function() {
    const t = localStorage.getItem('token'), u = localStorage.getItem('user');
    if (t && u) {
        try {
            const j = JSON.parse(u);
            document.getElementById('navLoginBtn').style.display = 'none';
            const av = document.getElementById('navAvatar');
            av.style.display = 'flex';
            av.textContent = (j.nickname || j.username || 'U').charAt(0).toUpperCase();
        } catch (e) {}
    }
})();
</script>

</body>
</html>
