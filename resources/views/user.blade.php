<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户中心</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        // 立即应用主题，避免闪烁
        (function() {
            const theme = localStorage.getItem('setting_theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #eef2ff;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --bg-card: #ffffff;
            --bg-dark: #0f172a;
            --text: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.1);
            --radius: 16px;
            --radius-sm: 10px;
            --success: #10b981;
            --danger: #ef4444;
        }
        
        [data-theme="dark"] {
            --primary: #818cf8;
            --primary-dark: #6366f1;
            --primary-light: #1e1b4b;
            --accent: #fbbf24;
            --bg: #0f172a;
            --bg-card: #1e293b;
            --bg-dark: #020617;
            --text: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #334155;
            --shadow: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.5);
        }
        
        [data-theme="dark"] .navbar { background: rgba(15,23,42,0.9); }
        [data-theme="dark"] .navbar-links { background: #1e293b; }
        [data-theme="dark"] .sidebar { background: #1e293b; }
        [data-theme="dark"] .sidebar.active { background: #1e293b; }
        [data-theme="dark"] .modal { background: #1e293b; }
        [data-theme="dark"] .security-item[style*="background:#fff"] { background: var(--bg-card) !important; }
        [data-theme="dark"] input, [data-theme="dark"] select, [data-theme="dark"] textarea { background: #0f172a; color: #f1f5f9; border-color: #334155; }
        [data-theme="dark"] .modal-overlay .modal,
        [data-theme="dark"] [style*="background:#fff"],
        [data-theme="dark"] [style*="background: #fff"] { background: var(--bg-card) !important; }
        [data-theme="dark"] .plan-card { background: var(--bg-card) !important; border-color: var(--border) !important; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'PingFang SC', 'Microsoft YaHei', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }

        /* NAVBAR */
        .navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); }
        .navbar-inner { max-width: 100%; margin: 0; padding: 0 40px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; }
        .navbar-brand .logo { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), #8b5cf6); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
        .navbar-links { display: flex; align-items: center; gap: 8px; }
        .navbar-links a { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; color: var(--text-secondary); transition: all 0.2s; }
        .navbar-links a:hover { color: var(--primary); background: var(--primary-light); }
        .navbar-user { display: flex; align-items: center; gap: 12px; }
        .navbar-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; cursor: pointer; }
        .mobile-menu-btn { display: none; background: none; border: none; font-size: 20px; cursor: pointer; padding: 8px; }
        
        /* 移动端侧边栏切换按钮 - 桌面端隐藏 */
        .sidebar-toggle { display: none; }
        .sidebar-overlay { display: none; }

        /* LAYOUT */
        .page { max-width: 100%; margin: 0; padding: 88px 40px 40px; }
        .page-header { margin-bottom: 32px; }
        .page-header h1 { font-size: 28px; font-weight: 800; }
        .page-header p { color: var(--text-secondary); margin-top: 4px; }

        .layout { display: grid; grid-template-columns: 300px 1fr; gap: 32px; align-items: start; }

        /* SIDEBAR */
        .sidebar { position: sticky; top: 88px; height: fit-content; min-width: 280px; }
        .profile-card { background: var(--bg-card); border-radius: var(--radius); padding: 28px; text-align: center; border: 1px solid var(--border); margin-bottom: 16px; }
        .avatar-wrap { position: relative; width: 88px; height: 88px; margin: 0 auto 16px; }
        .avatar-wrap img { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); }
        .avatar-edit { position: absolute; bottom: 0; right: 0; width: 28px; height: 28px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; cursor: pointer; border: 2px solid #fff; }
        .profile-name { font-size: 18px; font-weight: 700; }
        .profile-id { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        .nav-menu { background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
        
        /* 移动端侧边栏可滚动 */
        @media (max-width: 768px) {
            .sidebar .nav-menu { overflow-y: auto; max-height: calc(100vh - 200px); }
        }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 14px 20px; font-size: 14px; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: all 0.15s ease; border-left: 3px solid transparent; }
        .nav-item:hover { background: var(--primary-light); color: var(--primary); }
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 600; border-left-color: var(--primary); }
        .nav-item.active i { color: var(--primary); }
        .nav-item:hover i { transform: scale(1.02); }
        .nav-divider { height: 1px; background: var(--border); margin: 0 20px; }
        .nav-item.danger { color: var(--danger); }
        .nav-item.danger:hover { background: #fef2f2; }

        /* CONTENT */
        .content { min-height: 60vh; flex: 1; }
        .panel { display: none; }
        .panel.active { display: block; }

        .card { background: var(--bg-card); border-radius: var(--radius); padding: 28px; border: 1px solid var(--border); margin-bottom: 20px; }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .card-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--primary); }

        /* BUTTONS */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-danger { background: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-ghost { background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }
        
        /* DEPLOY TABS */
        .deploy-tab { flex: 1; padding: 8px 12px; border: none; background: transparent; color: var(--text-muted); font-size: 13px; font-weight: 500; cursor: pointer; border-radius: 6px; transition: all 0.2s; }
        .deploy-tab:hover { color: var(--text); background: rgba(255,255,255,0.5); }
        .deploy-tab.active { background: var(--primary); color: #fff; }

        /* FORMS */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-size: 14px; transition: all 0.2s; background: var(--bg-card); }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 6px; }
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }

        /* PROFILE FIELDS */
        .field-item { display: flex; align-items: center; justify-content: space-between; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); margin-bottom: 12px; transition: all 0.2s; }
        .field-item:hover { border-color: var(--primary); box-shadow: var(--shadow); }
        .field-label { font-size: 13px; color: var(--text-muted); }
        .field-value { font-size: 14px; font-weight: 500; margin-top: 4px; }
        .field-action { font-size: 13px; color: var(--primary); cursor: pointer; font-weight: 600; white-space: nowrap; }

        /* SECURITY ITEMS */
        .security-item { display: flex; align-items: center; justify-content: space-between; padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--border); margin-bottom: 12px; transition: all 0.2s; }
        .security-item:hover { border-color: var(--primary); box-shadow: var(--shadow); }
        .security-left { display: flex; align-items: center; gap: 16px; }

        /* 第三方绑定卡片美化 */
        .social-bind-card { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 10px; transition: all 0.2s; background: var(--bg-card); }
        .social-bind-card:hover { border-color: var(--primary); box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .social-bind-left { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
        .social-bind-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); flex-shrink: 0; background: var(--bg-secondary); }
        .social-bind-avatar-placeholder { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .social-bind-info { min-width: 0; }
        .social-bind-info h4 { margin: 0; font-size: 14px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px; }
        .social-bind-info h4 .platform-badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 500; }
        .social-bind-info p { margin: 4px 0 0; font-size: 12px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .social-bind-status { display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 12px; }
        .social-bind-status .btn { white-space: nowrap; }
        .security-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .security-info h4 { font-size: 14px; font-weight: 600; }
        .security-info p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stat-card { background: var(--bg); border-radius: var(--radius-sm); padding: 20px; text-align: center; cursor: default; }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .stat-card:hover .stat-icon { transform: scale(1.02); }
        .stat-num { font-size: 24px; font-weight: 800; }
        .stat-card:hover .stat-num { color: var(--primary); }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        /* DANGER ZONE */
        .danger-zone { border-color: #fecaca; background: #fef2f2; }
        .danger-zone .card-title { color: var(--danger); }

        /* MODAL */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 200; }
        .modal-overlay.show { display: flex; }
        .modal { background: var(--bg-card); border-radius: var(--radius); padding: 32px; width: 90%; max-width: 440px; box-shadow: var(--shadow-lg); animation: modalIn 0.3s ease; }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .modal h3 { font-size: 18px; font-weight: 700; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 28px; }

        /* TOAST */
        .toast { position: fixed; top: 80px; right: 24px; z-index: 999; padding: 14px 24px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; color: #fff; box-shadow: var(--shadow-lg); transform: translateX(120%); transition: transform 0.3s; }
        .toast.show { transform: translateX(0); }
        .toast-success { background: var(--success); }
        .toast-error { background: var(--danger); }

        /* EMPTY STATE */
        .empty { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; }
        .empty p { font-size: 15px; }

        /* LOGIN REQUIRED */
        .login-required { text-align: center; padding: 120px 20px; }
        .login-required i { font-size: 64px; color: var(--text-muted); opacity: 0.2; margin-bottom: 24px; display: block; }
        .login-required h2 { font-size: 24px; margin-bottom: 12px; }
        .login-required p { color: var(--text-secondary); margin-bottom: 32px; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            /* 导航栏 */
            .mobile-menu-btn { display: block; }
            .navbar-links { 
                display: none;
                position: fixed;
                top: 64px;
                left: 0;
                right: 0;
                background: #fff;
                flex-direction: column;
                padding: 16px;
                border-bottom: 1px solid var(--border);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                z-index: 99;
            }
            .navbar-links.active { display: flex; }
            .navbar-links a { padding: 12px 16px; width: 100%; }
            .navbar-inner { padding: 0 16px; }
            .navbar-user { display: none; }
            
            /* 页面布局 */
            .page { padding: 76px 16px 24px; }
            .page-header { margin-bottom: 20px; }
            .page-header h1 { font-size: 22px; }
            
            /* 侧边栏 - 移动端可折叠 */
            .layout { grid-template-columns: 1fr; gap: 0; }
            .sidebar { 
                position: fixed;
                left: -280px;
                top: 64px;
                bottom: 0;
                width: 280px;
                background: #fff;
                z-index: 100;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                transition: left 0.3s ease;
                border-right: 1px solid var(--border);
                padding-bottom: 20px;
            }
            .sidebar.active { left: 0; }
            .sidebar .nav-menu { overflow-y: auto; max-height: calc(100vh - 200px); }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 64px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.4);
                z-index: 99;
            }
            .sidebar-overlay.active { display: block; }
            .sidebar-toggle {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 16px;
                background: var(--primary);
                color: #fff;
                border-radius: var(--radius);
                margin-bottom: 16px;
                font-weight: 600;
                cursor: pointer;
            }
            
            /* 统计卡片 */
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-icon { width: 40px; height: 40px; font-size: 16px; }
            .stat-num { font-size: 22px; }
            
            /* 表单 */
            .form-row { grid-template-columns: 1fr; }
            .form-input { padding: 14px 12px; font-size: 16px; } /* 防止iOS缩放 */
            .form-group { margin-bottom: 16px; }
            
            /* 卡片 */
            .card { padding: 20px 16px; margin-bottom: 16px; }
            .card-title { font-size: 15px; margin-bottom: 16px; }
            
            /* 按钮 */
            .btn { padding: 12px 20px; }
            .btn-block { width: 100%; justify-content: center; }
            
            /* 安全项 */
            .security-item { flex-direction: column; align-items: flex-start; gap: 12px; }
            .security-item .btn { width: 100%; justify-content: center; }
            
            /* 表格 - 水平滚动 */
            .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 600px; }
            
            /* 播放器卡片 */
            .player-card { flex-direction: column; }
            .player-card .player-actions { width: 100%; flex-direction: row; gap: 8px; }
            .player-card .player-actions .btn { flex: 1; justify-content: center; }
            
            /* 模态框 */
            .modal { padding: 16px; }
            .modal-content { 
                max-height: 85vh;
                overflow-y: auto;
                border-radius: 16px 16px 0 0;
                margin-top: auto;
            }
            .modal-header { padding: 20px 16px; }
            .modal-body { padding: 16px; }
            .modal-footer { padding: 16px; flex-direction: column; gap: 8px; }
            .modal-footer .btn { width: 100%; justify-content: center; }
            
            /* 弹窗/升级卡片 */
            .plan-card { padding: 16px; }
            .plan-card .plan-features { font-size: 13px; }
            
            /* 公告列表 */
            .notice-item { flex-direction: column; }
            .notice-item .notice-content { width: 100%; }
            .notice-item .notice-time { margin-top: 8px; }
            
            /* 钱包/财务 */
            .balance-card { flex-direction: column; text-align: center; }
            .balance-card .balance-actions { width: 100%; }
            .balance-card .balance-actions .btn { width: 100%; justify-content: center; }
            
            /* 底部安全区 */
            .page { padding-bottom: calc(24px + env(safe-area-inset-bottom)); }
        }
        
        /* 小屏幕手机 (< 480px) */
        @media (max-width: 480px) {
            .page { padding: 68px 12px 20px; }
            .card { padding: 16px 12px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card { padding: 12px; }
            .stat-num { font-size: 20px; }
            .btn { padding: 12px 16px; font-size: 13px; }
            .form-input { padding: 12px 10px; }
            .modal-content { border-radius: 12px; }
        }
        
        /* 横屏优化 */
        @media (max-height: 500px) and (orientation: landscape) {
            .page { padding-top: 68px; }
            .modal-content { max-height: 90vh; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">
            <div class="logo"><i class="fas fa-play"></i></div>
            <span>DPlayer</span>
        </a>
        <div class="navbar-links">
            <a href="/">首页</a>
            <a href="/player">播放器</a>
        </div>
        <div class="navbar-user" id="navUser"></div>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
    </div>
</nav>

<div id="loginRequired" style="display:none;">
    <div class="login-required">
        <i class="fas fa-user-lock"></i>
        <h2>请先登录</h2>
        <p>登录后即可访问您的用户中心</p>
        <a href="/login" class="btn btn-primary" style="padding:12px 32px;"><i class="fas fa-sign-in-alt"></i> 立即登录</a>
    </div>
</div>

<div class="page" id="mainContent" style="display:none;">
    <div class="page-header">
        <h1>用户中心</h1>
        <p>管理您的个人信息和账号设置</p>
    </div>

    <div class="layout">
        <!-- 移动端侧边栏切换按钮 -->
        <div class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i> <span>菜单</span>
        </div>
        
        <!-- 侧边栏遮罩 -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="profile-card">
                <div class="avatar-wrap">
                    <img src="" id="userAvatar" alt="">
                    <div class="avatar-edit" onclick="openModal('avatarModal')"><i class="fas fa-camera"></i></div>
                </div>
                <div class="profile-name" id="userName">-</div>
                <div class="profile-id" id="userId">ID: -</div>
            </div>
            <div class="nav-menu">
                <div class="nav-item active" data-panel="overview"><i class="fas fa-th-large"></i> 概览</div>
                <div class="nav-item" data-panel="profile"><i class="fas fa-user"></i> 个人资料</div>
                <div class="nav-item" data-panel="security"><i class="fas fa-shield-alt"></i> 账号安全</div>
                <div class="nav-divider"></div>
                <div class="nav-item" data-panel="data"><i class="fas fa-database"></i> 我的数据</div>
                <div class="nav-item" data-panel="analytics"><i class="fas fa-chart-line"></i> 数据统计</div>
                <div class="nav-item" data-panel="notices"><i class="fas fa-bell"></i> 公告中心</div>
                <div class="nav-item" data-panel="player"><i class="fas fa-play-circle"></i> 我的播放器</div>
                <div class="nav-item" data-panel="upgrade"><i class="fas fa-crown"></i> 版本升级</div>
                <div class="nav-item" data-panel="finance"><i class="fas fa-wallet"></i> 我的钱包</div>
                <div class="nav-item" data-panel="materials"><i class="fas fa-images"></i> 素材管理</div>
                <div class="nav-item" data-panel="videos"><i class="fas fa-film"></i> 视频管理</div>
                <div class="nav-item" data-panel="settings"><i class="fas fa-cog"></i> 系统设置</div>
                <div class="nav-divider"></div>
                <div class="nav-item danger" onclick="logout()"><i class="fas fa-sign-out-alt"></i> 退出登录</div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <!-- 概览 -->
            <div class="panel active" id="panel-overview">
                <div class="card">
                    <div class="card-title"><i class="fas fa-hand-wave"></i> 欢迎回来</div>
                    <div style="display:flex;align-items:center;gap:20px;">
                        <img src="" id="overviewAvatar" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid var(--primary);">
                        <div>
                            <div style="font-size:20px;font-weight:700;" id="overviewName">-</div>
                            <div style="color:var(--text-secondary);margin-top:4px;" id="overviewEmail">-</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title"><i class="fas fa-chart-bar"></i> 数据统计</div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-eye"></i></div>
                            <div class="stat-num" id="statViews">0</div>
                            <div class="stat-label">播放次数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-heart"></i></div>
                            <div class="stat-num" id="statFav">0</div>
                            <div class="stat-label">收藏数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-clock"></i></div>
                            <div class="stat-num" id="statHistory">0</div>
                            <div class="stat-label">观看记录</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title"><i class="fas fa-bolt"></i> 快捷操作</div>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
                        <button class="btn btn-outline" onclick="switchPanel('profile')"><i class="fas fa-user-edit"></i> 编辑资料</button>
                        <button class="btn btn-outline" onclick="switchPanel('security')"><i class="fas fa-key"></i> 修改密码</button>
                        <button class="btn btn-outline" onclick="switchPanel('data')"><i class="fas fa-download"></i> 导出数据</button>
                        <button class="btn btn-outline" onclick="switchPanel('settings')"><i class="fas fa-cog"></i> 系统设置</button>
                    </div>
                </div>
            </div>

            <!-- 个人资料 -->
            <div class="panel" id="panel-profile">
                <div class="card">
                    <div class="card-title"><i class="fas fa-user"></i> 基本信息</div>
                    <div class="field-item">
                        <div><div class="field-label">昵称</div><div class="field-value" id="infoNickname">-</div></div>
                        <div class="field-action" onclick="openModal('nicknameModal')">修改</div>
                    </div>
                    <div class="field-item">
                        <div><div class="field-label">用户名</div><div class="field-value" id="infoUsername">-</div></div>
                        <div style="font-size:12px;color:var(--text-muted);">不可修改</div>
                    </div>
                    <div class="field-item">
                        <div><div class="field-label">性别</div><div class="field-value" id="infoGender">未设置</div></div>
                        <div class="field-action" onclick="openModal('genderModal')">修改</div>
                    </div>
                    <div class="field-item">
                        <div><div class="field-label">生日</div><div class="field-value" id="infoBirthday">未设置</div></div>
                        <div class="field-action" onclick="openModal('birthdayModal')">修改</div>
                    </div>
                    <div class="field-item">
                        <div><div class="field-label">个人简介</div><div class="field-value" id="infoBio">未设置</div></div>
                        <div class="field-action" onclick="openModal('bioModal')">修改</div>
                    </div>
                </div>
            </div>

            <!-- 账号安全 -->
            <div class="panel" id="panel-security">
                <div class="card">
                    <div class="card-title"><i class="fas fa-shield-alt"></i> 安全设置</div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-lock"></i></div>
                            <div class="security-info"><h4>登录密码</h4><p>已设置，建议定期更换</p></div>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="openModal('passwordModal')">修改密码</button>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-mobile-alt"></i></div>
                            <div class="security-info"><h4>手机号码</h4><p id="infoPhone">未绑定</p></div>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="openModal('phoneModal')" id="phoneBtn">绑定手机</button>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-envelope"></i></div>
                            <div class="security-info"><h4>电子邮箱</h4><p id="infoEmail">未绑定</p></div>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="openModal('emailModal')" id="emailBtn">绑定邮箱</button>
                    </div>
                </div>

                <!-- 第三方账号绑定 -->
                <div class="card" style="margin-top:16px;">
                    <div class="card-title"><i class="fas fa-link"></i> 第三方账号绑定</div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">绑定第三方账号后可快速登录</div>
                    <div id="socialBindingsList">
                        <div style="text-align:center;padding:20px;color:var(--text-muted);">加载中...</div>
                    </div>
                </div>

                <div class="card danger-zone">
                    <div class="card-title"><i class="fas fa-exclamation-triangle"></i> 危险操作</div>
                    <div class="security-item" style="border-color:#fecaca;background:#fff;">
                        <div class="security-left">
                            <div class="security-icon" style="background:#fef2f2;color:#ef4444;"><i class="fas fa-trash-alt"></i></div>
                            <div class="security-info"><h4 style="color:var(--danger);">注销账号</h4><p>永久删除账号及所有数据，不可恢复</p></div>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="deleteAccount()">申请注销</button>
                    </div>
                </div>
            </div>

            <!-- 我的数据 -->
            <div class="panel" id="panel-data">
                <div class="card">
                    <div class="card-title"><i class="fas fa-database"></i> 数据管理</div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#e0e7ff;color:#6366f1;"><i class="fas fa-file-export"></i></div>
                            <div class="security-info"><h4>导出数据</h4><p>下载您的个人数据副本（JSON格式）</p></div>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="exportData()"><i class="fas fa-download"></i> 导出</button>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#fce7f3;color:#ec4899;"><i class="fas fa-history"></i></div>
                            <div class="security-info"><h4>清除历史</h4><p>清空所有观看历史记录</p></div>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="clearHistory()"><i class="fas fa-trash"></i> 清除</button>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-heart-broken"></i></div>
                            <div class="security-info"><h4>清空收藏</h4><p>移除所有收藏的视频</p></div>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="clearFavorites()"><i class="fas fa-trash"></i> 清除</button>
                    </div>
                </div>
            </div>

            <!-- 数据统计 -->
            <div class="panel" id="panel-analytics">
                <!-- 加载状态 -->
                <div id="analytics-loading" style="text-align:center;padding:60px;color:var(--text-muted);">
                    <i class="fas fa-spinner fa-spin" style="font-size:32px;margin-bottom:16px;display:block;"></i>
                    加载统计数据中...
                </div>
                
                <!-- 统计内容 -->
                <div id="analytics-content" style="display:none;">
                    <!-- 概览统计卡片 -->
                    <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff;">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <div class="stat-num" id="analytics-players">0</div>
                            <div class="stat-label">播放器数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff;">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="stat-num" id="analytics-videos">0</div>
                            <div class="stat-label">视频总数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff;">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-num" id="analytics-views">0</div>
                            <div class="stat-label">总播放量</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #fff;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-num" id="analytics-orders">0</div>
                            <div class="stat-label">订单数</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: #fff;">
                                <i class="fas fa-yen-sign"></i>
                            </div>
                            <div class="stat-num" id="analytics-spent">¥0</div>
                            <div class="stat-label">消费金额</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <!-- 播放趋势图 -->
                        <div class="card">
                            <div class="card-title"><i class="fas fa-chart-area"></i> 近7天播放趋势</div>
                            <div id="viewsChart" style="height: 250px; display: flex; align-items: flex-end; gap: 8px; padding: 20px 0;">
                                <div style="text-align:center;width:100%;padding:40px;color:var(--text-muted);">暂无数据</div>
                            </div>
                        </div>

                        <!-- 广告收入 -->
                        <div class="card">
                            <div class="card-title"><i class="fas fa-ad"></i> 广告收入统计</div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div style="text-align: center; padding: 20px; background: var(--bg); border-radius: 12px;">
                                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">贴片广告</div>
                                    <div style="font-size: 24px; font-weight: 700; color: var(--primary);" id="ad-preroll">¥0</div>
                                </div>
                                <div style="text-align: center; padding: 20px; background: var(--bg); border-radius: 12px;">
                                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">中插广告</div>
                                    <div style="font-size: 24px; font-weight: 700; color: #f59e0b;" id="ad-midroll">¥0</div>
                                </div>
                                <div style="text-align: center; padding: 20px; background: var(--bg); border-radius: 12px;">
                                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">后贴片</div>
                                    <div style="font-size: 24px; font-weight: 700; color: #10b981;" id="ad-postroll">¥0</div>
                                </div>
                                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, var(--primary-light) 0%, #e0e7ff 100%); border-radius: 12px;">
                                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">广告总收入</div>
                                    <div style="font-size: 28px; font-weight: 800; color: var(--primary);" id="ad-total">¥0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 热门播放器 -->
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-title"><i class="fas fa-trophy"></i> 热门播放器 TOP5</div>
                        <div id="topPlayersList">
                            <div style="text-align:center;padding:40px;color:var(--text-muted);">暂无播放器</div>
                        </div>
                    </div>

                    <!-- 版本信息 -->
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-title"><i class="fas fa-crown"></i> 当前版本</div>
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-size: 20px; font-weight: 700;" id="version-name">免费版</div>
                                <div style="color: var(--text-muted); margin-top: 4px;" id="version-expire">永久有效</div>
                            </div>
                            <button class="btn btn-primary" onclick="switchPanel('upgrade')">
                                <i class="fas fa-arrow-up"></i> 升级版本
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 公告中心 -->
            <div class="panel" id="panel-notices">
                <div class="card">
                    <div class="card-title"><i class="fas fa-bell"></i> 公告中心</div>
                    <div id="noticesList" style="space-y:12px;">
                        <div style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-spinner fa-spin" style="font-size:24px;margin-bottom:12px;display:block;"></i>
                            加载中...
                        </div>
                    </div>
                </div>
            </div>
            <!-- 我的播放器 -->
            <div class="panel" id="panel-player">
                <div class="card">
                    <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;background:linear-gradient(135deg,var(--primary),#8b5cf6);border-radius:12px;margin-bottom:20px;color:#fff;">
                        <div style="flex:1;">
                            <div style="font-size:12px;opacity:0.8;">可用播放器额度</div>
                            <div style="font-size:32px;font-weight:800;" id="playerQuotaCount">-</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:12px;opacity:0.8;">已使用</div>
                            <div style="font-size:20px;font-weight:600;" id="playerUsedCount">-</div>
                        </div>
                        <button class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);" onclick="showQuotaPurchase()"><i class="fas fa-shopping-cart"></i> 购买额度</button>
                    </div>
                    <div class="card-title" style="justify-content:space-between;">
                        <div><i class="fas fa-play-circle"></i> 我的播放器</div>
                        <button class="btn btn-primary btn-sm" onclick="showCreatePlayer()"><i class="fas fa-plus"></i> 创建播放器</button>
                    </div>
                    <div id="playerList">
                        <div style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-spinner fa-spin" style="font-size:24px;margin-bottom:12px;display:block;"></i>
                            加载中...
                        </div>
                    </div>
                </div>

                <!-- 创建/编辑播放器表单 -->
                <div class="card" id="playerFormCard" style="display:none;">
                    <div class="card-title"><i class="fas fa-edit"></i> <span id="playerFormTitle">创建播放器</span></div>
                    <form id="playerForm" onsubmit="submitPlayerForm(event)">
                        <input type="hidden" id="playerId" value="">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">播放器名称 *</label>
                                <input type="text" id="playerName" class="form-input" required placeholder="例如：我的视频播放器">
                            </div>
                            <div class="form-group">
                                <label class="form-label">主题色</label>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;">
                                    <div class="color-dot active" data-color="#6366f1" style="width:32px;height:32px;border-radius:8px;background:#6366f1;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#8b5cf6" style="width:32px;height:32px;border-radius:8px;background:#8b5cf6;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#ec4899" style="width:32px;height:32px;border-radius:8px;background:#ec4899;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#f43f5e" style="width:32px;height:32px;border-radius:8px;background:#f43f5e;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#f97316" style="width:32px;height:32px;border-radius:8px;background:#f97316;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#22c55e" style="width:32px;height:32px;border-radius:8px;background:#22c55e;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#06b6d4" style="width:32px;height:32px;border-radius:8px;background:#06b6d4;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#3b82f6" style="width:32px;height:32px;border-radius:8px;background:#3b82f6;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                    <div class="color-dot" data-color="#1e293b" style="width:32px;height:32px;border-radius:8px;background:#1e293b;cursor:pointer;border:3px solid transparent;transition:all 0.2s;"></div>
                                </div>
                                <input type="hidden" id="playerColor" value="#6366f1">
                            </div>
                        </div>
                        
                        <!-- 版本选择 -->
                        <div class="form-group">
                            <label class="form-label">播放器版本</label>
                            <div id="versionOptions" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:8px;">
                                <div class="version-card" data-version="free" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:2px solid #e2e8f0;border-radius:12px;padding:16px;cursor:pointer;text-align:center;transition:all 0.2s;" onclick="selectVersion('free')">
                                    <div style="font-size:24px;margin-bottom:8px;">🆓</div>
                                    <div style="font-weight:600;font-size:14px;">免费版</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">平台默认广告</div>
                                </div>
                                <div class="version-card" data-version="basic" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:2px solid #93c5fd;border-radius:12px;padding:16px;cursor:pointer;text-align:center;transition:all 0.2s;" onclick="selectVersion('basic')">
                                    <div style="font-size:24px;margin-bottom:8px;">⭐</div>
                                    <div style="font-weight:600;font-size:14px;">基础版</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">可自定义外观</div>
                                </div>
                                <div class="version-card" data-version="advanced" style="background:linear-gradient(135deg,#faf5ff,#ede9fe);border:2px solid #c4b5fd;border-radius:12px;padding:16px;cursor:pointer;text-align:center;transition:all 0.2s;" onclick="selectVersion('advanced')">
                                    <div style="font-size:24px;margin-bottom:8px;">💎</div>
                                    <div style="font-weight:600;font-size:14px;">高级版</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">+自定义域名</div>
                                </div>
                                <div class="version-card" data-version="flagship" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:2px solid #fcd34d;border-radius:12px;padding:16px;cursor:pointer;text-align:center;transition:all 0.2s;" onclick="selectVersion('flagship')">
                                    <div style="font-size:24px;margin-bottom:8px;">👑</div>
                                    <div style="font-weight:600;font-size:14px;">旗舰版</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">+广告模块</div>
                                </div>
                            </div>
                            <input type="hidden" id="playerVersion" value="free">
                            <div id="versionUpgradeHint" style="display:none;margin-top:12px;padding:12px;background:linear-gradient(135deg,#fff7ed,#ffedd5);border:1px solid #fed7aa;border-radius:8px;font-size:13px;color:#9a3412;">
                                <i class="fas fa-info-circle"></i> <span id="versionHintText">需要升级套餐才能使用此版本功能</span>
                                <a href="/user" style="color:#ea580c;font-weight:600;margin-left:8px;">去升级 →</a>
                            </div>
                        </div>

                        <!-- 播放器模板选择 -->
                        <div class="form-group">
                            <label class="form-label">播放器模板</label>
                            <div id="templateOptions" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:8px;">
                                <div class="template-card active" data-template="standard" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:2px solid #e2e8f0;border-radius:12px;padding:16px;cursor:pointer;transition:all 0.2s;" onclick="selectTemplate('standard')">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:48px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-play" style="color:#fff;font-size:14px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:14px;">标准版</div>
                                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">DPlayer 经典风格</div>
                                        </div>
                                    </div>
                                    <div style="margin-top:10px;display:flex;gap:4px;flex-wrap:wrap;">
                                        <span style="font-size:10px;background:#e0e7ff;color:#6366f1;padding:2px 6px;border-radius:4px;">弹幕</span>
                                        <span style="font-size:10px;background:#e0e7ff;color:#6366f1;padding:2px 6px;border-radius:4px;">广告</span>
                                        <span style="font-size:10px;background:#e0e7ff;color:#6366f1;padding:2px 6px;border-radius:4px;">选集</span>
                                    </div>
                                </div>
                                <div class="template-card" data-template="youku" style="background:linear-gradient(135deg,#1a1a2e,#16213e);border:2px solid #333;border-radius:12px;padding:16px;cursor:pointer;transition:all 0.2s;" onclick="selectTemplate('youku')">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:48px;height:36px;background:linear-gradient(135deg,#ff6b35,#ff8e6e);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-bolt" style="color:#fff;font-size:14px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:14px;color:#fff;">优酷风格</div>
                                            <div style="font-size:11px;color:#888;margin-top:2px;">简洁橙红主题</div>
                                        </div>
                                    </div>
                                    <div style="margin-top:10px;display:flex;gap:4px;flex-wrap:wrap;">
                                        <span style="font-size:10px;background:rgba(255,107,53,0.2);color:#ff6b35;padding:2px 6px;border-radius:4px;">渐变控制栏</span>
                                        <span style="font-size:10px;background:rgba(255,107,53,0.2);color:#ff6b35;padding:2px 6px;border-radius:4px;">迷你进度条</span>
                                        <span style="font-size:10px;background:rgba(255,107,53,0.2);color:#ff6b35;padding:2px 6px;border-radius:4px;">快捷键</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="playerTemplate" value="standard">
                        </div>

                        <!-- 自定义外观区域（需要基础版及以上） -->
                        <div id="customizeSection">
                            <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1px solid #bae6fd;border-radius:10px;padding:16px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i class="fas fa-crown" style="color:#f59e0b;"></i>
                                    <span style="font-weight:600;color:#0369a1;">自定义外观</span>
                                    <span style="font-size:11px;background:#3b82f6;color:#fff;padding:2px 8px;border-radius:10px;">基础版+</span>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Logo URL</label>
                                        <input type="text" id="playerLogo" class="form-input" placeholder="https://example.com/logo.png">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">背景图/视频 URL</label>
                                        <input type="text" id="playerBackground" class="form-input" placeholder="https://example.com/bg.jpg 或 .mp4">
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">支持图片(jpg/png)和视频(mp4)，无视频时显示。仅无广告版本可用</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">移动端背景图 URL</label>
                                        <input type="text" id="playerBackgroundMobile" class="form-input" placeholder="https://example.com/bg-mobile.jpg">
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">竖屏专用，留空使用横屏背景</div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">进度条图标</label>
                                        <input type="text" id="playerProgressIcon" class="form-input" placeholder="自定义图标URL或选择下方预设">
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">留空不显示，建议32x32透明PNG</div>
                                        <!-- 分类图标选择器 -->
                                        <div style="margin-top:10px;">
                                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                                                <button type="button" class="icon-cat-tab active" data-cat="all">全部</button>
                                                <button type="button" class="icon-cat-tab" data-cat="run">🏃 奔跑</button>
                                                <button type="button" class="icon-cat-tab" data-cat="vehicle">🚗 交通</button>
                                                <button type="button" class="icon-cat-tab" data-cat="animal">🐾 萌宠</button>
                                                <button type="button" class="icon-cat-tab" data-cat="game">🎮 游戏</button>
                                                <button type="button" class="icon-cat-tab" data-cat="hero">🦸 英雄</button>
                                                <button type="button" class="icon-cat-tab" data-cat="effect">✨ 特效</button>
                                                <button type="button" class="icon-cat-tab" data-cat="festive">🎉 节日</button>
                                                <button type="button" class="icon-cat-tab" data-cat="funny">😂 搞怪</button>
                                                <button type="button" class="icon-cat-tab" data-cat="battle">⚔️ 战斗</button>
                                                <button type="button" class="icon-cat-tab" data-cat="food">🍔 美食</button>
                                            </div>
                                            <div id="iconPickerGrid" style="display:flex;flex-wrap:wrap;gap:6px;max-height:200px;overflow-y:auto;padding:4px;background:var(--bg);border-radius:8px;">
                                                <div class="preset-icon selected" data-icon="" data-cat="all" title="不使用">无</div>
                                            </div>
                                            <div style="font-size:11px;color:var(--text-muted);margin-top:6px;">💡 点击选中，支持emoji和自定义图片URL</div>
                                        </div>
                                        <style>
                                        .icon-cat-tab{padding:4px 10px;border:1px solid var(--border);border-radius:16px;background:var(--bg-card);color:var(--text-secondary);font-size:12px;cursor:pointer;transition:all .15s;white-space:nowrap;}
                                        .icon-cat-tab:hover{border-color:var(--primary);color:var(--primary);}
                                        .icon-cat-tab.active{background:var(--primary);color:#fff;border-color:var(--primary);}
                                        .preset-icon{width:36px;height:36px;border:2px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;transition:all .15s;background:var(--bg-card);}
                                        .preset-icon:hover{border-color:var(--primary);transform:scale(1.1);}
                                        .preset-icon.selected{border-color:var(--primary);background:var(--primary-light);box-shadow:0 0 0 2px rgba(99,102,241,0.2);}
                                        .preset-icon[data-icon=""]{font-size:11px;color:var(--text-muted);}
                                        /* 图标选择器动画预览 */
                                        @keyframes dp-walk{0%,100%{transform:rotate(0deg) translateY(0)}25%{transform:rotate(-8deg) translateY(-3px)}50%{transform:rotate(0deg) translateY(0)}75%{transform:rotate(8deg) translateY(-3px)}}
                                        @keyframes dp-run{0%,100%{transform:translateY(0) scaleX(1)}25%{transform:translateY(-6px) scaleX(1.05)}50%{transform:translateY(0) scaleX(1)}75%{transform:translateY(-4px) scaleX(0.95)}}
                                        @keyframes dp-drive{0%,100%{transform:translateX(0) rotate(0deg)}25%{transform:translateX(2px) rotate(2deg)}50%{transform:translateX(0) rotate(0deg)}75%{transform:translateX(-2px) rotate(-2deg)}}
                                        @keyframes dp-fly{0%,100%{transform:translateY(0) rotate(-5deg)}50%{transform:translateY(-8px) rotate(5deg)}}
                                        @keyframes dp-bounce{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-8px) scale(1.1)}}
                                        @keyframes dp-spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
                                        @keyframes dp-pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.3);opacity:0.8}}
                                        @keyframes dp-shake{0%,100%{transform:rotate(0deg)}20%{transform:rotate(-15deg)}40%{transform:rotate(15deg)}60%{transform:rotate(-10deg)}80%{transform:rotate(10deg)}}
                                        @keyframes dp-wobble{0%,100%{transform:translateX(0) rotate(0)}25%{transform:translateX(-4px) rotate(-10deg)}50%{transform:translateX(0) rotate(0)}75%{transform:translateX(4px) rotate(10deg)}}
                                        @keyframes dp-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
                                        @keyframes dp-yum{0%,100%{transform:rotate(0deg) scale(1)}25%{transform:rotate(-10deg) scale(1.1)}75%{transform:rotate(10deg) scale(1.1)}}
                                        @keyframes dp-wave{0%,100%{transform:rotate(0deg)}25%{transform:rotate(20deg)}75%{transform:rotate(-20deg)}}
                                        @keyframes dp-game{0%,100%{transform:translateY(0) rotate(0)}25%{transform:translateY(-4px) rotate(-5deg)}50%{transform:translateY(0) rotate(0)}75%{transform:translateY(-4px) rotate(5deg)}}
                                        .icon-anim-walk{animation:dp-walk 0.6s ease-in-out infinite}
                                        .icon-anim-run{animation:dp-run 0.4s ease-in-out infinite}
                                        .icon-anim-drive{animation:dp-drive 0.3s ease-in-out infinite}
                                        .icon-anim-fly{animation:dp-fly 0.8s ease-in-out infinite}
                                        .icon-anim-bounce{animation:dp-bounce 0.6s ease-in-out infinite}
                                        .icon-anim-spin{animation:dp-spin 2s linear infinite}
                                        .icon-anim-pulse{animation:dp-pulse 0.8s ease-in-out infinite}
                                        .icon-anim-shake{animation:dp-shake 0.5s ease-in-out infinite}
                                        .icon-anim-wobble{animation:dp-wobble 0.6s ease-in-out infinite}
                                        .icon-anim-float{animation:dp-float 1.5s ease-in-out infinite}
                                        .icon-anim-yum{animation:dp-yum 0.5s ease-in-out infinite}
                                        .icon-anim-wave{animation:dp-wave 0.4s ease-in-out infinite}
                                        .icon-anim-game{animation:dp-game 0.6s ease-in-out infinite}
                                        </style>
                                        <script>
                                        (function(){
                                            const icons = [
                                                // 🏃 奔跑追赶（爱奇艺风格）
                                                {e:'🚶',n:'行人',c:'run',a:'walk'},{e:'🚶‍♂️',n:'男生走路',c:'run',a:'walk'},{e:'🚶‍♀️',n:'女生走路',c:'run',a:'walk'},
                                                {e:'🏃',n:'跑步',c:'run',a:'run'},{e:'🏃‍♂️',n:'男生跑步',c:'run',a:'run'},{e:'🏃‍♀️',n:'女生跑步',c:'run',a:'run'},
                                                {e:'🚴',n:'骑车',c:'run',a:'run'},{e:'🚴‍♂️',n:'男生骑车',c:'run',a:'run'},{e:'🚴‍♀️',n:'女生骑车',c:'run',a:'run'},
                                                {e:'🛴',n:'滑板车',c:'run',a:'run'},{e:'⛸️',n:'滑冰',c:'run',a:'run'},{e:'🏄',n:'冲浪',c:'run',a:'run'},
                                                {e:'🏊',n:'游泳',c:'run',a:'run'},{e:'🤸',n:'翻跟斗',c:'run',a:'run'},{e:'💃',n:'跳舞',c:'run',a:'run'},
                                                {e:'🕺',n:'男生跳舞',c:'run',a:'run'},{e:'🧎',n:'跪坐',c:'run',a:'walk'},{e:'🦵',n:'踢腿',c:'run',a:'wave'},
                                                // 🚗 交通工具（优酷风格）
                                                {e:'🚗',n:'汽车',c:'vehicle',a:'drive'},{e:'🚕',n:'出租车',c:'vehicle',a:'drive'},{e:'🚙',n:'SUV',c:'vehicle',a:'drive'},
                                                {e:'🏎️',n:'赛车',c:'vehicle',a:'drive'},{e:'🚌',n:'公交',c:'vehicle',a:'drive'},{e:'🚎',n:'电车',c:'vehicle',a:'drive'},
                                                {e:'🚂',n:'火车',c:'vehicle',a:'drive'},{e:'🚄',n:'高铁',c:'vehicle',a:'drive'},{e:'🚅',n:'动车',c:'vehicle',a:'drive'},
                                                {e:'🚀',n:'火箭',c:'vehicle',a:'fly'},{e:'✈️',n:'飞机',c:'vehicle',a:'fly'},{e:'🚁',n:'直升机',c:'vehicle',a:'fly'},
                                                {e:'🛸',n:'UFO',c:'vehicle',a:'fly'},{e:'🚢',n:'轮船',c:'vehicle',a:'drive'},{e:'⛵',n:'帆船',c:'vehicle',a:'drive'},
                                                {e:'🏍️',n:'摩托',c:'vehicle',a:'drive'},{e:'🛵',n:'电动车',c:'vehicle',a:'drive'},{e:'🚜',n:'拖拉机',c:'vehicle',a:'drive'},
                                                // 🐾 萌宠动物（B站风格）
                                                {e:'🐕',n:'小狗',c:'animal',a:'bounce'},{e:'🐈',n:'小猫',c:'animal',a:'bounce'},{e:'🐈‍⬛',n:'黑猫',c:'animal',a:'bounce'},
                                                {e:'🐰',n:'兔子',c:'animal',a:'bounce'},{e:'🐼',n:'熊猫',c:'animal',a:'bounce'},{e:'🐨',n:'考拉',c:'animal',a:'bounce'},
                                                {e:'🦊',n:'狐狸',c:'animal',a:'bounce'},{e:'🐿️',n:'松鼠',c:'animal',a:'bounce'},{e:'🐢',n:'乌龟',c:'animal',a:'bounce'},
                                                {e:'🐧',n:'企鹅',c:'animal',a:'bounce'},{e:'🦔',n:'刺猬',c:'animal',a:'bounce'},{e:'🐝',n:'蜜蜂',c:'animal',a:'fly'},
                                                {e:'🦋',n:'蝴蝶',c:'animal',a:'fly'},{e:'🐞',n:'瓢虫',c:'animal',a:'fly'},{e:'🦆',n:'鸭子',c:'animal',a:'bounce'},
                                                {e:'🐸',n:'青蛙',c:'animal',a:'bounce'},{e:'🐭',n:'老鼠',c:'animal',a:'bounce'},{e:'🐹',n:'仓鼠',c:'animal',a:'bounce'},
                                                {e:'🐻',n:'小熊',c:'animal',a:'bounce'},{e:'🦁',n:'狮子',c:'animal',a:'bounce'},{e:'🐯',n:'老虎',c:'animal',a:'bounce'},
                                                {e:'🐮',n:'奶牛',c:'animal',a:'bounce'},{e:'🐷',n:'小猪',c:'animal',a:'bounce'},{e:'🐵',n:'猴子',c:'animal',a:'bounce'},
                                                {e:'🦄',n:'独角兽',c:'animal',a:'bounce'},{e:'🐲',n:'龙',c:'animal',a:'bounce'},{e:'🦕',n:'恐龙',c:'animal',a:'bounce'},
                                                // 🎮 游戏动漫（B站风格）
                                                {e:'🎮',n:'手柄',c:'game',a:'game'},{e:'👾',n:'外星人',c:'game',a:'spin'},{e:'🤖',n:'机器人',c:'game',a:'spin'},
                                                {e:'🕹️',n:'摇杆',c:'game',a:'game'},{e:'🎲',n:'骰子',c:'game',a:'shake'},{e:'♟️',n:'棋子',c:'game',a:'game'},
                                                {e:'🃏',n:'小丑牌',c:'game',a:'game'},{e:'🎰',n:'老虎机',c:'game',a:'spin'},{e:'🧩',n:'拼图',c:'game',a:'game'},
                                                {e:'🪄',n:'魔杖',c:'game',a:'float'},{e:'🔮',n:'水晶球',c:'game',a:'spin'},{e:'🧿',n:'恶魔之眼',c:'game',a:'spin'},
                                                // 🦸 超级英雄（优酷漫威风格）
                                                {e:'🦸',n:'超级英雄',c:'hero',a:'float'},{e:'🦸‍♂️',n:'男英雄',c:'hero',a:'float'},{e:'🦸‍♀️',n:'女英雄',c:'hero',a:'float'},
                                                {e:'🦹',n:'反派',c:'hero',a:'float'},{e:'🦹‍♂️',n:'男反派',c:'hero',a:'float'},{e:'🦹‍♀️',n:'女反派',c:'hero',a:'float'},
                                                {e:'🧙',n:'法师',c:'hero',a:'float'},{e:'🧙‍♂️',n:'男法师',c:'hero',a:'float'},{e:'🧙‍♀️',n:'女法师',c:'hero',a:'float'},
                                                {e:'🥷',n:'忍者',c:'hero',a:'float'},{e:'🧝',n:'精灵',c:'hero',a:'float'},{e:'🧛',n:'吸血鬼',c:'hero',a:'float'},
                                                {e:'🧟',n:'僵尸',c:'hero',a:'wobble'},{e:'🧜',n:'美人鱼',c:'hero',a:'float'},{e:'🧚',n:'仙女',c:'hero',a:'float'},
                                                {e:'🎅',n:'圣诞老人',c:'hero',a:'float'},{e:'🤶',n:'圣诞奶奶',c:'hero',a:'float'},{e:'👸',n:'公主',c:'hero',a:'float'},
                                                {e:'🤴',n:'王子',c:'hero',a:'float'},{e:'🧶',n:'毛线',c:'hero',a:'bounce'},{e:'🪢',n:'绳结',c:'hero',a:'bounce'},
                                                // ✨ 炫酷特效
                                                {e:'⚡',n:'闪电',c:'effect',a:'pulse'},{e:'🌟',n:'星星',c:'effect',a:'pulse'},{e:'🔥',n:'火焰',c:'effect',a:'pulse'},
                                                {e:'💫',n:'流星',c:'effect',a:'pulse'},{e:'✨',n:'闪光',c:'effect',a:'pulse'},{e:'🌈',n:'彩虹',c:'effect',a:'pulse'},
                                                {e:'☄️',n:'彗星',c:'effect',a:'fly'},{e:'💎',n:'钻石',c:'effect',a:'spin'},{e:'🌀',n:'漩涡',c:'effect',a:'spin'},
                                                {e:'💥',n:'爆炸',c:'effect',a:'pulse'},{e:'🔔',n:'铃铛',c:'effect',a:'shake'},{e:'🎵',n:'音符',c:'effect',a:'float'},
                                                {e:'🎶',n:'双音符',c:'effect',a:'float'},{e:'💝',n:'爱心礼盒',c:'effect',a:'pulse'},{e:'💖',n:'闪亮爱心',c:'effect',a:'pulse'},
                                                {e:'💗',n:'粉色爱心',c:'effect',a:'pulse'},{e:'❤️‍🔥',n:'燃烧爱心',c:'effect',a:'pulse'},{e:'🫧',n:'泡泡',c:'effect',a:'float'},
                                                {e:'🪩',n:'镜球',c:'effect',a:'spin'},{e:'🫠',n:'融化',c:'effect',a:'wobble'},
                                                // 🎉 节日氛围
                                                {e:'🎄',n:'圣诞树',c:'festive',a:'bounce'},{e:'🎃',n:'南瓜',c:'festive',a:'bounce'},{e:'🧧',n:'红包',c:'festive',a:'bounce'},
                                                {e:'🎆',n:'烟花',c:'festive',a:'pulse'},{e:'🎈',n:'气球',c:'festive',a:'float'},{e:'🎁',n:'礼物',c:'festive',a:'bounce'},
                                                {e:'🎊',n:'彩带',c:'festive',a:'shake'},{e:'🏮',n:'灯笼',c:'festive',a:'float'},{e:'🎐',n:'风铃',c:'festive',a:'float'},
                                                {e:'🎋',n:'七夕',c:'festive',a:'float'},{e:'🎑',n:'中秋',c:'festive',a:'float'},{e:'🧨',n:'鞭炮',c:'festive',a:'shake'},
                                                {e:'🎀',n:'蝴蝶结',c:'festive',a:'bounce'},{e:'🎗️',n:'丝带',c:'festive',a:'float'},{e:'🏅',n:'金牌',c:'festive',a:'spin'},
                                                {e:'🥇',n:'第一名',c:'festive',a:'bounce'},{e:'🏆',n:'奖杯',c:'festive',a:'bounce'},{e:'🎖️',n:'勋章',c:'festive',a:'spin'},
                                                // 😂 搞怪趣味
                                                {e:'🤡',n:'小丑',c:'funny',a:'wobble'},{e:'👽',n:'外星人',c:'funny',a:'float'},{e:'🤪',n:'搞怪',c:'funny',a:'wobble'},
                                                {e:'😎',n:'酷',c:'funny',a:'wobble'},{e:'🥸',n:'伪装',c:'funny',a:'wobble'},{e:'🤩',n:'崇拜',c:'funny',a:'pulse'},
                                                {e:'💩',n:'便便',c:'funny',a:'bounce'},{e:'🤠',n:'牛仔',c:'funny',a:'wobble'},{e:'🧐',n:'侦探',c:'funny',a:'wobble'},
                                                {e:'👻',n:'幽灵',c:'funny',a:'float'},{e:'💀',n:'骷髅',c:'funny',a:'shake'},{e:'☠️',n:'骷髅旗',c:'funny',a:'shake'},
                                                {e:'🤌',n:'捏手指',c:'funny',a:'wave'},{e:'👋',n:'挥手',c:'funny',a:'wave'},{e:'🤙',n:'666',c:'funny',a:'wave'},
                                                {e:'✌️',n:'胜利',c:'funny',a:'wave'},{e:'🤟',n:'爱你',c:'funny',a:'wave'},{e:'👍',n:'点赞',c:'funny',a:'wave'},
                                                // ⚔️ 热血战斗
                                                {e:'⚔️',n:'双剑',c:'battle',a:'shake'},{e:'🗡️',n:'剑',c:'battle',a:'shake'},{e:'🛡️',n:'盾牌',c:'battle',a:'shake'},
                                                {e:'🏹',n:'弓箭',c:'battle',a:'shake'},{e:'🪓',n:'斧头',c:'battle',a:'shake'},{e:'🔱',n:'三叉戟',c:'battle',a:'shake'},
                                                {e:'🥊',n:'拳击',c:'battle',a:'shake'},{e:'🥋',n:'武术',c:'battle',a:'shake'},{e:'⚽',n:'足球',c:'battle',a:'bounce'},
                                                {e:'🏀',n:'篮球',c:'battle',a:'bounce'},{e:'⚾',n:'棒球',c:'battle',a:'bounce'},{e:'🎾',n:'网球',c:'battle',a:'bounce'},
                                                {e:'🏐',n:'排球',c:'battle',a:'bounce'},{e:'🎱',n:'台球',c:'battle',a:'bounce'},{e:'🏓',n:'乒乓球',c:'battle',a:'bounce'},
                                                {e:'🏸',n:'羽毛球',c:'battle',a:'bounce'},{e:'🥅',n:'球门',c:'battle',a:'bounce'},{e:'🎯',n:'靶心',c:'battle',a:'pulse'},
                                                // 🍔 美食小吃
                                                {e:'🍔',n:'汉堡',c:'food',a:'yum'},{e:'🍕',n:'披萨',c:'food',a:'yum'},{e:'🍟',n:'薯条',c:'food',a:'yum'},
                                                {e:'🌭',n:'热狗',c:'food',a:'yum'},{e:'🍿',n:'爆米花',c:'food',a:'yum'},{e:'🧁',n:'蛋糕',c:'food',a:'yum'},
                                                {e:'🍩',n:'甜甜圈',c:'food',a:'spin'},{e:'🍪',n:'饼干',c:'food',a:'yum'},{e:'🍦',n:'冰淇淋',c:'food',a:'yum'},
                                                {e:'☕',n:'咖啡',c:'food',a:'yum'},{e:'🧋',n:'奶茶',c:'food',a:'yum'},{e:'🥤',n:'饮料',c:'food',a:'yum'},
                                                {e:'🍺',n:'啤酒',c:'food',a:'yum'},{e:'🍻',n:'干杯',c:'food',a:'yum'},{e:'🍷',n:'红酒',c:'food',a:'yum'},
                                                {e:'🧃',n:'果汁',c:'food',a:'yum'},{e:'🍉',n:'西瓜',c:'food',a:'yum'},{e:'🍓',n:'草莓',c:'food',a:'yum'},
                                            ];
                                            const grid = document.getElementById('iconPickerGrid');
                                            const tabs = document.querySelectorAll('.icon-cat-tab');
                                            const input = document.getElementById('playerProgressIcon');
                                            function render(cat){
                                                grid.innerHTML = '<div class="preset-icon'+(input.value===''?' selected':'')+'" data-icon="" title="不使用">无</div>';
                                                icons.forEach(i=>{
                                                    if(cat!=='all'&&i.c!==cat) return;
                                                    const sel = input.value===i.e?' selected':'';
                                                    const anim = i.a?' icon-anim-'+i.a:'';
                                                    grid.innerHTML += '<div class="preset-icon'+sel+anim+'" data-icon="'+i.e+'" title="'+i.n+'">'+i.e+'</div>';
                                                });
                                                bindClicks();
                                            }
                                            function bindClicks(){
                                                grid.querySelectorAll('.preset-icon').forEach(el=>{
                                                    el.onclick = function(){
                                                        grid.querySelectorAll('.preset-icon').forEach(e=>e.classList.remove('selected'));
                                                        this.classList.add('selected');
                                                        input.value = this.dataset.icon;
                                                    };
                                                });
                                            }
                                            tabs.forEach(tab=>{
                                                tab.onclick = function(){
                                                    tabs.forEach(t=>t.classList.remove('active'));
                                                    this.classList.add('active');
                                                    render(this.dataset.cat);
                                                };
                                            });
                                            window.syncProgressIconPicker = function(val){
                                                grid.querySelectorAll('.preset-icon').forEach(e=>e.classList.remove('selected'));
                                                const match = grid.querySelector('.preset-icon[data-icon="'+val+'"]');
                                                if(match) match.classList.add('selected');
                                            };
                                            render('all');
                                        })();
                                        </script>
                                    </div>
                                </div>
                                
                                <!-- 视频解析配置 -->
                                <div style="background:var(--bg);border-radius:10px;padding:16px;margin-bottom:16px;">
                                    <div style="font-weight:600;margin-bottom:12px;"><i class="fas fa-magic"></i> 视频解析</div>
                                    <div class="form-group">
                                        <label class="form-label">解析接口地址</label>
                                        <input type="text" id="playerParseUrl" class="form-input" placeholder="https://api.example.com/parse?url=">
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                                            配置后可通过 <code>/youku/player/{slug}?url=视频链接</code> 自动解析播放<br>
                                            接口需返回JSON格式：<code>{"url": "真实播放地址"}</code>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 水印配置 -->
                                <div style="background:var(--bg);border-radius:10px;padding:16px;margin-bottom:16px;">
                                    <div style="font-weight:600;margin-bottom:12px;"><i class="fas fa-tint"></i> 水印设置</div>
                                    <div class="form-group">
                                        <label class="form-label">水印文字</label>
                                        <input type="text" id="playerWatermark" class="form-input" placeholder="© My Site">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">预设位置</label>
                                            <select id="playerWatermarkPosition" class="form-input" onchange="toggleWatermarkPosition()">
                                                <option value="custom">自定义位置</option>
                                                <option value="top-left">左上角</option>
                                                <option value="top-right">右上角</option>
                                                <option value="bottom-left">左下角</option>
                                                <option value="bottom-right">右下角</option>
                                                <option value="center">居中</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">字体大小</label>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <input type="range" id="playerWatermarkSize" min="10" max="60" value="14" style="flex:1;" oninput="document.getElementById('wmSizeVal').textContent=this.value+'px'">
                                                <span id="wmSizeVal" style="min-width:40px;font-size:13px;">14px</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">颜色</label>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <input type="color" id="playerWatermarkColor" value="#ffffff" style="width:40px;height:36px;border:none;cursor:pointer;">
                                                <input type="text" id="playerWatermarkColorHex" class="form-input" value="#ffffff" placeholder="#ffffff" style="flex:1;" oninput="document.getElementById('playerWatermarkColor').value=this.value">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">透明度</label>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <input type="range" id="playerWatermarkOpacity" min="0" max="100" value="30" style="flex:1;" oninput="document.getElementById('wmOpacityVal').textContent=this.value+'%'">
                                                <span id="wmOpacityVal" style="min-width:40px;font-size:13px;">30%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 自定义坐标 -->
                                    <div id="watermarkCustomPosition">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">X 坐标 (px 或 %)</label>
                                                <input type="text" id="playerWatermarkX" class="form-input" placeholder="10px 或 50%" value="10px">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Y 坐标 (px 或 %)</label>
                                                <input type="text" id="playerWatermarkY" class="form-input" placeholder="10px 或 50%" value="10px">
                                            </div>
                                        </div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:-8px;">
                                            支持 px（像素）或 %（百分比），如：10px、50%、calc(100%-100px)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">宽高比</label>
                                        <select id="playerRatio" class="form-input">
                                            <option value="16:9">16:9</option>
                                            <option value="4:3">4:3</option>
                                            <option value="1:1">1:1</option>
                                            <option value="21:9">21:9</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">圆角</label>
                                        <input type="text" id="playerRadius" class="form-input" value="12px" placeholder="12px">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 自定义域名区域（高级版+） -->
                        <div id="domainSection" style="display:none;">
                            <div style="background:linear-gradient(135deg,#faf5ff,#ede9fe);border:1px solid #c4b5fd;border-radius:10px;padding:16px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i class="fas fa-globe" style="color:#8b5cf6;"></i>
                                    <span style="font-weight:600;color:#6d28d9;">自定义域名</span>
                                    <span style="font-size:11px;background:#8b5cf6;color:#fff;padding:2px 8px;border-radius:10px;">高级版+</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">绑定域名</label>
                                    <input type="text" id="playerDomain" class="form-input" placeholder="player.example.com">
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">绑定后可通过此域名访问播放器</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 广告模块区域（旗舰版） -->
                        <div id="adCreateSection" style="display:none;">
                            <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fcd34d;border-radius:10px;padding:16px;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i class="fas fa-ad" style="color:#f59e0b;"></i>
                                    <span style="font-weight:600;color:#92400e;">广告模块</span>
                                    <span style="font-size:11px;background:#f59e0b;color:#fff;padding:2px 8px;border-radius:10px;">旗舰版</span>
                                </div>
                                <div class="form-group">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="checkbox" id="pAdEnabled">
                                        <span style="font-size:13px;">启用自定义广告（启用后可自定义前贴片、暂停广告等）</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">功能开关</label>
                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-top:8px;">
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pAutoplay"> <span style="font-size:13px;">自动播放</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pLoop"> <span style="font-size:13px;">循环播放</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pMuted"> <span style="font-size:13px;">默认静音</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pDanmaku"> <span style="font-size:13px;">显示弹幕</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pDownload"> <span style="font-size:13px;">下载按钮</span>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--bg);border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" id="pShare" checked> <span style="font-size:13px;">分享按钮</span>
                                </label>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;margin-top:8px;">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                            <button type="button" class="btn btn-ghost" onclick="hidePlayerForm()">取消</button>
                        </div>
                    </form>
                </div>

                <!-- 播放器详情 -->
                <div class="card" id="playerDetailCard" style="display:none;">
                    <div class="card-title" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i class="fas fa-info-circle"></i>
                            <span id="detailName">-</span>
                            <span id="detailVersionBadge" style="font-size:11px;padding:2px 8px;border-radius:10px;color:#fff;font-weight:600;background:#94a3b8;">免费版</span>
                            <span id="detailVersionExpire" style="font-size:11px;color:#94a3b8;font-variant-numeric:tabular-nums;"></span>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button class="btn btn-outline btn-sm" onclick="editCurrentPlayer()"><i class="fas fa-edit"></i> 编辑</button>
                            <button class="btn btn-primary btn-sm" onclick="upgradeCurrentPlayer()"><i class="fas fa-arrow-up"></i> 升级</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCurrentPlayer()"><i class="fas fa-trash"></i> 删除</button>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">视频数量</div>
                            <div style="font-size:24px;font-weight:700;color:var(--primary);" id="detailVideoCount">0</div>
                        </div>
                        <div>
                            <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px;">播放次数</div>
                            <div style="font-size:24px;font-weight:700;color:var(--primary);" id="detailViewCount">0</div>
                        </div>
                    </div>
                    <div style="margin-top:20px;">
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">嵌入代码</div>
                        <div style="background:var(--bg-dark);color:#e2e8f0;padding:14px 16px;border-radius:10px;font-family:monospace;font-size:12px;position:relative;word-break:break-all;">
                            <span id="detailEmbedCode">-</span>
                            <button onclick="copyEmbedCode()" style="position:absolute;top:6px;right:6px;background:rgba(255,255,255,0.1);color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:11px;">复制</button>
                        </div>
                    </div>
                    <div style="margin-top:20px;">
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">访问链接</div>
                        <a id="detailEmbedUrl" href="#" target="_blank" style="color:var(--primary);font-size:13px;word-break:break-all;"></a>
                    </div>
                    
                    <!-- 部署代码下载 -->
                    <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <div style="font-weight:600;"><i class="fas fa-code"></i> 部署代码（支持广告）</div>
                        </div>
                        <div id="adsInfoBadge" style="display:none;background:linear-gradient(135deg,#ff6b00,#ff9800);color:#fff;padding:8px 16px;border-radius:8px;font-size:12px;margin-bottom:12px;">
                            <i class="fas fa-ad"></i> 已包含 <span id="adsCount">0</span> 个广告，部署后自动展示
                        </div>
                        
                        <!-- 部署方式切换 -->
                        <div style="display:flex;gap:4px;margin-bottom:12px;background:var(--bg-dark);padding:4px;border-radius:8px;">
                            <button class="deploy-tab active" onclick="switchDeployTab('html')" id="tab-html">HTML文件</button>
                            <button class="deploy-tab" onclick="switchDeployTab('js')" id="tab-js">JS脚本</button>
                            <button class="deploy-tab" onclick="switchDeployTab('iframe')" id="tab-iframe">iframe嵌入</button>
                            <button class="deploy-tab" onclick="switchDeployTab('package')" id="tab-package">一键部署包</button>
                        </div>
                        
                        <!-- HTML部署 -->
                        <div id="deploy-html" class="deploy-content" style="display:block;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <span style="font-size:12px;color:var(--text-muted);">完整的HTML页面，包含播放器和广告引擎</span>
                                <div style="display:flex;gap:8px;">
                                    <button class="btn btn-outline btn-sm" onclick="downloadDeployCode('html')"><i class="fas fa-download"></i> 下载HTML</button>
                                    <button class="btn btn-outline btn-sm" onclick="copyDeployCode('html')"><i class="fas fa-copy"></i> 复制</button>
                                </div>
                            </div>
                            <div style="background:var(--bg-dark);color:#e2e8f0;padding:14px 16px;border-radius:10px;font-family:monospace;font-size:11px;max-height:200px;overflow-y:auto;">
                                <pre id="deployCodeHtml" style="margin:0;white-space:pre-wrap;">加载中...</pre>
                            </div>
                        </div>
                        
                        <!-- JS脚本部署 -->
                        <div id="deploy-js" class="deploy-content" style="display:none;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <span style="font-size:12px;color:var(--text-muted);">独立JS文件，可引入到现有页面</span>
                                <div style="display:flex;gap:8px;">
                                    <button class="btn btn-outline btn-sm" onclick="downloadDeployCode('js')"><i class="fas fa-download"></i> 下载JS</button>
                                    <button class="btn btn-outline btn-sm" onclick="copyDeployCode('js')"><i class="fas fa-copy"></i> 复制</button>
                                </div>
                            </div>
                            <div style="background:var(--bg-dark);color:#e2e8f0;padding:14px 16px;border-radius:10px;font-family:monospace;font-size:11px;max-height:200px;overflow-y:auto;">
                                <pre id="deployCodeJs" style="margin:0;white-space:pre-wrap;">加载中...</pre>
                            </div>
                            <div style="margin-top:8px;padding:8px 12px;background:#fef3c7;border-radius:6px;font-size:12px;color:#92400e;">
                                <i class="fas fa-info-circle"></i> 使用方法：将此JS文件上传到您的服务器，然后在HTML中通过 &lt;script src="player.js"&gt;&lt;/script&gt; 引入
                            </div>
                        </div>
                        
                        <!-- iframe嵌入 -->
                        <div id="deploy-iframe" class="deploy-content" style="display:none;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <span style="font-size:12px;color:var(--text-muted);">最简单的嵌入方式，直接粘贴到网页中</span>
                                <div style="display:flex;gap:8px;">
                                    <button class="btn btn-outline btn-sm" onclick="copyDeployCode('iframe')"><i class="fas fa-copy"></i> 复制</button>
                                </div>
                            </div>
                            <div style="background:var(--bg-dark);color:#e2e8f0;padding:14px 16px;border-radius:10px;font-family:monospace;font-size:11px;max-height:200px;overflow-y:auto;">
                                <pre id="deployCodeIframe" style="margin:0;white-space:pre-wrap;">加载中...</pre>
                            </div>
                            <div style="margin-top:12px;padding:12px;background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;">
                                <div style="font-weight:600;color:#0369a1;margin-bottom:8px;"><i class="fas fa-info-circle"></i> 动态播放接口</div>
                                <div style="font-size:12px;color:#0c4a6e;line-height:1.8;">
                                    <div>支持 <code style="background:#e0f2fe;padding:2px 6px;border-radius:4px;">?url=视频地址</code> 参数动态播放任意视频：</div>
                                    <div style="background:#0c4a6e;color:#7dd3fc;padding:8px 12px;border-radius:6px;margin-top:6px;font-family:monospace;word-break:break-all;">
                                        <span id="iframeUrlExample">加载中...</span>
                                    </div>
                                    <div style="margin-top:6px;color:#64748b;">适用于苹果CMS、苹果程序等第三方系统对接</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 一键部署包 -->
                        <div id="deploy-package" class="deploy-content" style="display:none;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <span style="font-size:12px;color:var(--text-muted);">下载完整播放器项目，上传到你的网站即可使用</span>
                                <div style="display:flex;gap:8px;">
                                    <a href="/player-widget.tar.gz" class="btn btn-primary btn-sm" download><i class="fas fa-download"></i> 下载部署包</a>
                                </div>
                            </div>
                            <div style="background:var(--bg-dark);color:#e2e8f0;padding:16px;border-radius:10px;">
                                <div style="font-weight:600;margin-bottom:12px;color:#10b981;"><i class="fas fa-box"></i> 部署包内容</div>
                                <div style="font-size:13px;line-height:2;">
                                    <div>📁 <code>index.html</code> - 主页面（播放器界面）</div>
                                    <div>📁 <code>config.js</code> - 配置文件（修改此文件）</div>
                                    <div>📁 <code>README.md</code> - 使用说明</div>
                                </div>
                                <div style="margin-top:16px;padding:12px;background:#1a1a1a;border-radius:8px;">
                                    <div style="font-weight:600;margin-bottom:8px;color:#f59e0b;"><i class="fas fa-cog"></i> 快速配置</div>
                                    <div style="font-size:12px;color:#94a3b8;line-height:1.8;">
                                        <div>1. 下载并解压部署包</div>
                                        <div>2. 编辑 <code style="background:#333;padding:2px 6px;border-radius:4px;">config.js</code> 文件：</div>
                                        <div style="margin-left:20px;">
                                            <div>- <code>site_url</code>: 你的播放器站点地址</div>
                                            <div>- <code>player_id</code>: <span id="packagePlayerId" style="color:#60a5fa;">-</span></div>
                                            <div>- <code>player_key</code>: <span id="packagePlayerKey" style="color:#60a5fa;">***</span></div>
                                        </div>
                                        <div>3. 上传到你的网站目录</div>
                                        <div>4. 访问页面即可使用</div>
                                    </div>
                                </div>
                                <div style="margin-top:12px;padding:10px;background:#065f46;border-radius:6px;font-size:12px;color:#6ee7b7;">
                                    <i class="fas fa-lightbulb"></i> 支持部署到二级目录、子域名或独立站点
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top:12px;display:flex;gap:16px;font-size:12px;color:var(--text-muted);">
                            <div>播放器ID: <span id="detailPlayerId" style="color:var(--primary);">-</span></div>
                            <div>密钥: <span id="detailPlayerKey" style="color:var(--primary);">***</span></div>
                            <button class="btn btn-ghost btn-sm" onclick="showPlayerKey()" style="font-size:11px;">显示密钥</button>
                        </div>
                    </div>
                    
                    <!-- 广告管理 -->
                    <div id="adModeSection" style="margin-top:24px;border-top:1px solid var(--border);padding-top:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                            <div style="font-weight:600;"><i class="fas fa-ad"></i> 广告管理</div>
                            <select id="adModeSelect" class="form-input" style="width:140px;" onchange="updateAdMode()">
                                <option value="platform">平台广告</option>
                                <option value="user">用户广告</option>
                                <option value="mixed">混合模式</option>
                                <option value="none">无广告</option>
                            </select>
                        </div>
                        <!-- 购买去广告提示 -->
                        <div id="buyAdFreeHint" style="display:none;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:16px;margin-bottom:16px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="font-size:32px;">🚫</div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;color:#166534;">去除广告</div>
                                    <div style="font-size:13px;color:#15803d;margin-top:4px;">购买后可关闭平台广告（开屏广告除外），享受纯净播放体验</div>
                                </div>
                                <button onclick="showAdFreePurchase()" style="padding:8px 16px;background:#22c55e;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;white-space:nowrap;">
                                    立即购买
                                </button>
                            </div>
                        </div>
                        <!-- 购买广告模块提示 -->
                        <div id="buyAdModuleHint" style="display:none;background:linear-gradient(135deg,#fff7ed,#fef3c7);border:1px solid #fbbf24;border-radius:12px;padding:16px;margin-bottom:16px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="font-size:32px;">📢</div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;color:#92400e;">开通广告投放功能</div>
                                    <div style="font-size:13px;color:#b45309;margin-top:4px;">购买广告模块后，即可自定义广告投放，赚取收益</div>
                                </div>
                                <button onclick="showAdModulePurchase()" style="padding:8px 16px;background:#f59e0b;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;white-space:nowrap;">
                                    立即购买
                                </button>
                            </div>
                        </div>
                        <!-- 提示去素材管理 -->
                        <div id="adModeHint" style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;margin-top:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="fas fa-info-circle" style="color:var(--primary);font-size:16px;"></i>
                                <div style="flex:1;font-size:13px;color:var(--text-muted);">
                                    自定义广告请在左侧 <b>「素材管理」</b> 中添加和管理，支持前贴片、中贴片、后贴片、暂停广告等类型
                                </div>
                            </div>
                        </div>
                        <!-- 时段设置 -->
                        <div id="adDurationSection" style="display:none;margin-top:16px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;">
                            <div style="font-weight:600;margin-bottom:12px;"><i class="fas fa-clock"></i> 贴片时段设置</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">设置每个位置的广告时段时长，系统会从素材中随机抽取填充。设为0则使用素材自身时长。</div>
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                                <div>
                                    <label style="font-size:12px;color:var(--text-muted);">前贴片（秒）</label>
                                    <input type="number" id="prerollDurationInput" class="form-input" value="0" min="0" max="600" step="10" onchange="saveAdDurations()">
                                </div>
                                <div>
                                    <label style="font-size:12px;color:var(--text-muted);">中贴片（秒）</label>
                                    <input type="number" id="midrollDurationInput" class="form-input" value="0" min="0" max="600" step="10" onchange="saveAdDurations()">
                                </div>
                                <div>
                                    <label style="font-size:12px;color:var(--text-muted);">后贴片（秒）</label>
                                    <input type="number" id="postrollDurationInput" class="form-input" value="0" min="0" max="600" step="10" onchange="saveAdDurations()">
                                </div>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:8px;">
                                <i class="fas fa-lightbulb"></i> 推荐：前贴片120秒、中贴片60秒、后贴片60秒
                            </div>
                        </div>
                        <!-- 跑马灯设置 -->
                        <div id="marqueeSection" style="display:none;margin-top:16px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;">
                            <div style="font-weight:600;margin-bottom:12px;"><i class="fas fa-scroll"></i> 跑马灯广告</div>
                            <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">在视频播放时显示滚动文字广告，不影响观看体验。</div>
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                    <input type="checkbox" id="showMarqueeInput" onchange="saveMarqueeSettings()">
                                    <span style="font-size:13px;">启用跑马灯</span>
                                </label>
                            </div>
                            <div>
                                <label style="font-size:12px;color:var(--text-muted);">滚动文字内容</label>
                                <input type="text" id="marqueeTextInput" class="form-input" placeholder="输入跑马灯文字，如：欢迎来到XX影视" maxlength="200" onchange="saveMarqueeSettings()">
                            </div>
                            <div style="margin-top:12px;">
                                <label style="font-size:12px;color:var(--text-muted);">滚动速度（秒/圈）</label>
                                <input type="number" id="marqueeSpeedInput" class="form-input" value="12" min="3" max="60" step="1" onchange="saveMarqueeSettings()">
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">数字越小滚动越快，推荐8-15秒</div>
                            </div>
                            <div style="margin-top:12px;">
                                <label style="font-size:12px;color:var(--text-muted);">字体颜色</label>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <input type="color" id="marqueeColorInput" value="#ffffff" style="width:40px;height:32px;border:1px solid var(--border);border-radius:6px;cursor:pointer;" onchange="document.getElementById('marqueeColorText').value=this.value;saveMarqueeSettings()">
                                    <input type="text" id="marqueeColorText" class="form-input" value="#ffffff" style="width:100px;" onchange="document.getElementById('marqueeColorInput').value=this.value;saveMarqueeSettings()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 版本升级 -->
            <div class="panel" id="panel-upgrade">
                <div class="card">
                    <div class="card-title"><i class="fas fa-crown"></i> 版本升级</div>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">选择适合您的版本，解锁更多功能</p>
                    
                    <!-- 当前版本 -->
                    <div id="currentPlanInfo" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1px solid #bae6fd;border-radius:12px;padding:20px;margin-bottom:24px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:48px;height:48px;background:#3b82f6;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-user" style="color:#fff;font-size:20px;"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text-muted);">当前版本</div>
                                <div style="font-size:20px;font-weight:700;color:#1e40af;" id="currentPlanName">免费版</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 套餐列表 -->
                    <div id="plansList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
                        <!-- 动态加载 -->
                    </div>
                </div>
            </div>

            <!-- 我的钱包 -->
            <div class="panel" id="panel-finance">
                <!-- 余额卡片 -->
                <div class="card" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border:none;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-size:14px;opacity:0.8;">账户余额</div>
                            <div style="font-size:36px;font-weight:800;margin-top:8px;" id="userBalance">¥0.00</div>
                            <div style="font-size:13px;opacity:0.7;margin-top:4px;">
                                累计充值：<span id="totalRecharged">¥0.00</span> | 累计消费：<span id="totalSpent">¥0.00</span>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <button class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);" onclick="showRedeemModal()">
                                <i class="fas fa-gift"></i> 卡密兑换
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 快捷操作 -->
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
                    <div class="card" style="text-align:center;cursor:pointer;" onclick="showRedeemModal()">
                        <i class="fas fa-gift" style="font-size:28px;color:var(--primary);margin-bottom:8px;"></i>
                        <div style="font-weight:600;">卡密兑换</div>
                        <div style="font-size:12px;color:var(--text-muted);">使用充值卡兑换余额</div>
                    </div>
                    <div class="card" style="text-align:center;cursor:pointer;" onclick="switchPanel('upgrade')">
                        <i class="fas fa-crown" style="font-size:28px;color:var(--accent);margin-bottom:8px;"></i>
                        <div style="font-weight:600;">购买套餐</div>
                        <div style="font-size:12px;color:var(--text-muted);">升级播放器版本</div>
                    </div>
                    <div class="card" style="text-align:center;cursor:pointer;" onclick="showTransactions()">
                        <i class="fas fa-history" style="font-size:28px;color:var(--success);margin-bottom:8px;"></i>
                        <div style="font-weight:600;">交易记录</div>
                        <div style="font-size:12px;color:var(--text-muted);">查看收支明细</div>
                    </div>
                </div>

                <!-- 最近交易 -->
                <div class="card">
                    <div class="card-title"><i class="fas fa-exchange-alt"></i> 最近交易</div>
                    <div id="recentTransactions">
                        <div style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-spinner fa-spin"></i> 加载中...
                        </div>
                    </div>
                </div>
            </div>

            <!-- 卡密兑换弹窗 -->
            <div id="redeemModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:none;align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:16px;padding:32px;width:400px;max-width:90%;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                        <h3 style="font-size:18px;font-weight:700;">卡密兑换</h3>
                        <button onclick="closeRedeemModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted);">&times;</button>
                    </div>
                    <div class="form-group">
                        <label class="form-label">卡号</label>
                        <input type="text" id="redeemCardNo" class="form-input" placeholder="请输入卡号" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label class="form-label">卡密</label>
                        <input type="text" id="redeemSecret" class="form-input" placeholder="请输入卡密" maxlength="8">
                    </div>
                    <div id="redeemError" style="display:none;color:var(--danger);font-size:13px;margin-bottom:12px;"></div>
                    <button class="btn btn-primary" style="width:100%;" onclick="redeemCard()">
                        <i class="fas fa-check"></i> 立即兑换
                    </button>
                </div>
            </div>

            <!-- 交易记录弹窗 -->
            <div id="transactionsModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
                <div style="background:#fff;border-radius:16px;padding:32px;width:600px;max-width:90%;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                        <h3 style="font-size:18px;font-weight:700;">交易记录</h3>
                        <button onclick="closeTransactionsModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted);">&times;</button>
                    </div>
                    <div id="transactionsList">
                        <div style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fas fa-spinner fa-spin"></i> 加载中...
                        </div>
                    </div>
                </div>
            </div>

            <!-- 素材管理 -->
            <div class="panel" id="panel-materials">
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <div class="card-title" style="margin-bottom:0;"><i class="fas fa-images"></i> 素材管理</div>
                        <button class="btn btn-primary btn-sm" onclick="showMaterialForm()"><i class="fas fa-plus"></i> 添加素材</button>
                    </div>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">管理您的广告素材，可投放到单个、多个或全部播放器。素材包含前贴片、中贴片、后贴片、暂停广告。</p>

                    <!-- 添加/编辑素材表单 -->
                    <div id="materialFormCard" style="display:none;background:var(--bg);border-radius:10px;padding:16px;margin-bottom:16px;">
                        <input type="hidden" id="materialId">
                        
                        <!-- 基本信息 -->
                        <div style="font-weight:600;margin-bottom:12px;color:var(--primary);"><i class="fas fa-info-circle"></i> 基本信息</div>
                        <div class="form-group">
                            <label class="form-label">素材名称 *</label>
                            <input type="text" id="materialName" class="form-input" placeholder="如：品牌推广视频">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">素材类型</label>
                                <select id="materialType" class="form-input" onchange="toggleMaterialFields()">
                                    <option value="video">视频</option>
                                    <option value="image">图片</option>
                                    <option value="text">文字</option>
                                    <option value="html">HTML代码</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">展示位置</label>
                                <select id="materialPosition" class="form-input">
                                    <option value="preroll">前贴片</option>
                                    <option value="midroll">中贴片</option>
                                    <option value="postroll">后贴片</option>
                                    <option value="pause">暂停广告</option>
                                </select>
                            </div>
                        </div>

                        <!-- 素材内容 -->
                        <div style="font-weight:600;margin:16px 0 12px;color:var(--primary);"><i class="fas fa-photo-video"></i> 素材内容</div>
                        <div class="form-group">
                            <label class="form-label">素材地址 *（视频/图片/HTML链接）</label>
                            <input type="url" id="materialMediaUrl" class="form-input" placeholder="https://example.com/video.mp4 或图片地址">
                        </div>
                        <div class="form-group" id="materialCoverGroup">
                            <label class="form-label">封面图（视频封面/缩略图）</label>
                            <input type="url" id="materialCoverUrl" class="form-input" placeholder="https://example.com/cover.jpg">
                        </div>
                        <div class="form-group" id="materialContentGroup" style="display:none;">
                            <label class="form-label">文字内容</label>
                            <textarea id="materialContent" class="form-input" rows="3" placeholder="广告文字内容，支持HTML"></textarea>
                        </div>

                        <!-- 广告文案 -->
                        <div style="font-weight:600;margin:16px 0 12px;color:var(--primary);"><i class="fas fa-font"></i> 广告文案（可选）</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">广告标题</label>
                                <input type="text" id="materialTitle" class="form-input" placeholder="限时特惠！">
                            </div>
                            <div class="form-group">
                                <label class="form-label">品牌Logo</label>
                                <input type="url" id="materialLogoUrl" class="form-input" placeholder="https://example.com/logo.png">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">广告描述</label>
                            <input type="text" id="materialDescription" class="form-input" placeholder="详细描述文字...">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">按钮文字</label>
                                <input type="text" id="materialCtaText" class="form-input" placeholder="立即购买">
                            </div>
                            <div class="form-group">
                                <label class="form-label">按钮链接</label>
                                <input type="url" id="materialCtaUrl" class="form-input" placeholder="https://example.com/buy">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">点击跳转链接</label>
                            <input type="url" id="materialClickUrl" class="form-input" placeholder="点击广告整体跳转的链接">
                        </div>

                        <!-- 播放设置 -->
                        <div style="font-weight:600;margin:16px 0 12px;color:var(--primary);"><i class="fas fa-cog"></i> 播放设置</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">可跳过</label>
                                <label class="toggle-switch" style="margin-top:4px;">
                                    <input type="checkbox" id="materialSkippable" checked>
                                    <span class="toggle-slider"></span>
                                    <span style="margin-left:8px;font-size:13px;color:var(--text-muted);">允许观众跳过此广告</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">时长（秒）</label>
                                <input type="number" id="materialDuration" class="form-input" value="15" min="1" max="120">
                            </div>
                            <div class="form-group">
                                <label class="form-label">跳过等待（秒）</label>
                                <input type="number" id="materialSkipAfter" class="form-input" value="5" min="0" max="30">
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">开启"可跳过"后生效</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:2;">
                                <label class="form-label">进度条图标</label>
                                <input type="url" id="materialProgressIcon" class="form-input" placeholder="图标URL（留空不显示）">
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">小图标会跟随进度条移动，类似爱奇艺角色效果，建议28x28px透明PNG</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">优先级</label>
                                <select id="materialPriority" class="form-input">
                                    <option value="0">普通</option>
                                    <option value="1">高</option>
                                    <option value="2">最高</option>
                                </select>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">高优先级素材会被优先展示</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">每日频次上限</label>
                                <input type="number" id="materialFrequencyCap" class="form-input" value="0" min="0">
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">0 = 不限制</div>
                            </div>
                        </div>

                        <!-- 投放时间 -->
                        <div style="font-weight:600;margin:16px 0 12px;color:var(--primary);"><i class="fas fa-calendar"></i> 投放时间（可选）</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">开始时间</label>
                                <input type="datetime-local" id="materialStartAt" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">结束时间</label>
                                <input type="datetime-local" id="materialEndAt" class="form-input">
                            </div>
                        </div>

                        <!-- 投放目标 -->
                        <div style="font-weight:600;margin:16px 0 12px;color:var(--primary);"><i class="fas fa-bullseye"></i> 投放目标</div>
                        <div class="form-group">
                            <label class="form-label">投放范围 *</label>
                            <select id="materialTargetType" class="form-input" onchange="toggleMaterialTargetPlayers()">
                                <option value="single">单个播放器</option>
                                <option value="multiple">选择多个播放器</option>
                                <option value="all">全部已开通广告的播放器</option>
                            </select>
                        </div>
                        <div id="materialTargetPlayers" style="display:none;" class="form-group">
                            <label class="form-label">选择播放器</label>
                            <div id="materialPlayersList" style="max-height:150px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:8px;">
                                <div style="color:var(--text-muted);">加载中...</div>
                            </div>
                        </div>

                        <div style="display:flex;gap:8px;margin-top:16px;border-top:1px solid var(--border);padding-top:16px;">
                            <button class="btn btn-primary" onclick="saveMaterial()"><i class="fas fa-save"></i> 保存</button>
                            <button class="btn btn-outline" onclick="hideMaterialForm()">取消</button>
                        </div>
                    </div>

                    <!-- 素材列表 -->
                    <div id="materialsList">
                        <div style="text-align:center;padding:20px;color:var(--text-muted);">加载中...</div>
                    </div>
                </div>
            </div>

            <!-- 视频管理 -->
            <div class="panel" id="panel-videos">
                <!-- 批量导入 -->
                <div class="card">
                    <div class="card-title"><i class="fas fa-paste"></i> 快速导入</div>
                    <p style="color:var(--text-secondary);font-size:13px;margin-bottom:12px;">
                        粘贴剧集数据，格式：每行一个 <code style="background:var(--bg);padding:2px 6px;border-radius:4px;">集名$url</code>（用$分隔），支持从豆瓣资源站等站点复制。
                    </p>
                    <div class="form-group">
                        <label class="form-label">剧名</label>
                        <input type="text" class="form-input" id="importTitle" placeholder="例如：择天记">
                    </div>
                    <div class="form-group">
                        <label class="form-label">视频列表</label>
                        <textarea class="form-input" id="importData" rows="8" placeholder="第01集$https://xxx.com/01/index.m3u8
第02集$https://xxx.com/02/index.m3u8
第03集$https://xxx.com/03/index.m3u8" style="font-family:monospace;font-size:12px;"></textarea>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button class="btn-primary" onclick="batchImport()"><i class="fas fa-upload"></i> 导入并创建剧集</button>
                        <span id="importStatus" style="font-size:13px;color:var(--text-secondary);"></span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;">
                        <span><i class="fas fa-film"></i> 剧集管理</span>
                        <button class="btn-primary" onclick="openModal('seriesModal')"><i class="fas fa-plus"></i> 新建剧集</button>
                    </div>
                    <div id="seriesList" style="margin-top:12px;">
                        <div style="text-align:center;color:var(--text-secondary);padding:30px;">加载中...</div>
                    </div>
                </div>
                
                <div class="card" id="videoListCard" style="display:none;">
                    <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;">
                        <span><i class="fas fa-list"></i> 视频列表 - <span id="currentSeriesName">-</span></span>
                        <div style="display:flex;gap:8px;">
                            <button class="btn-outline" onclick="backToSeriesList()"><i class="fas fa-arrow-left"></i> 返回</button>
                            <button class="btn-primary" onclick="openModal('videoModal')"><i class="fas fa-plus"></i> 添加视频</button>
                        </div>
                    </div>
                    <div id="videoList" style="margin-top:12px;">
                        <div style="text-align:center;color:var(--text-secondary);padding:30px;">加载中...</div>
                    </div>
                </div>
                
                <div class="card" id="unbindVideoCard">
                    <div class="card-title"><i class="fas fa-link"></i> 绑定到播放器</div>
                    <p style="color:var(--text-secondary);font-size:13px;margin-bottom:12px;">选择剧集后，可批量绑定到指定播放器，播放器页面会自动显示选集面板。</p>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="bindPlayerSelect" style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:6px;background:var(--bg-card);color:var(--text-primary);">
                            <option value="">选择播放器...</option>
                        </select>
                        <button class="btn-primary" onclick="bindSeriesToPlayer()"><i class="fas fa-link"></i> 绑定</button>
                    </div>
                </div>
            </div>

            <!-- 系统设置 -->
            <div class="panel" id="panel-settings">
                <div class="card">
                    <div class="card-title"><i class="fas fa-cog"></i> 偏好设置</div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-palette"></i></div>
                            <div class="security-info"><h4>主题模式</h4><p>选择界面显示风格</p></div>
                        </div>
                        <select class="form-input" style="width:120px;" id="themeSelect" onchange="saveSetting('theme', this.value)">
                            <option value="light">浅色</option>
                            <option value="dark">深色</option>
                            <option value="auto">跟随系统</option>
                        </select>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-globe"></i></div>
                            <div class="security-info"><h4>语言</h4><p>界面显示语言</p></div>
                        </div>
                        <select class="form-input" style="width:120px;" id="langSelect" onchange="saveSetting('language', this.value)">
                            <option value="zh-CN">简体中文</option>
                            <option value="zh-TW">繁體中文</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div class="security-item">
                        <div class="security-left">
                            <div class="security-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-bell"></i></div>
                            <div class="security-info"><h4>通知提醒</h4><p>接收系统通知和更新提醒</p></div>
                        </div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" id="notifyCheck" onchange="saveSetting('notify', this.checked)" checked>
                            <span style="font-size:14px;">开启</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->
<div class="modal-overlay" id="avatarModal">
    <div class="modal">
        <h3>更换头像</h3>
        <div class="form-group">
            <label class="form-label">头像URL</label>
            <input type="url" class="form-input" id="newAvatar" placeholder="https://example.com/avatar.jpg">
            <div class="form-hint">输入图片链接地址</div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('avatarModal')">取消</button>
            <button class="btn btn-primary" onclick="saveAvatar()">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="nicknameModal">
    <div class="modal">
        <h3>修改昵称</h3>
        <div class="form-group">
            <label class="form-label">新昵称</label>
            <input type="text" class="form-input" id="newNickname" placeholder="请输入昵称" maxlength="20">
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('nicknameModal')">取消</button>
            <button class="btn btn-primary" onclick="saveNickname()">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="genderModal">
    <div class="modal">
        <h3>修改性别</h3>
        <div class="form-group">
            <label class="form-label">选择性别</label>
            <select class="form-input" id="newGender">
                <option value="0">保密</option>
                <option value="1">男</option>
                <option value="2">女</option>
            </select>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('genderModal')">取消</button>
            <button class="btn btn-primary" onclick="saveGender()">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="birthdayModal">
    <div class="modal">
        <h3>修改生日</h3>
        <div class="form-group">
            <label class="form-label">选择日期</label>
            <input type="date" class="form-input" id="newBirthday">
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('birthdayModal')">取消</button>
            <button class="btn btn-primary" onclick="saveBirthday()">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="bioModal">
    <div class="modal">
        <h3>修改简介</h3>
        <div class="form-group">
            <label class="form-label">个人简介</label>
            <textarea class="form-input" id="newBio" rows="3" placeholder="介绍一下自己..." maxlength="200"></textarea>
            <div class="form-hint">最多200字</div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('bioModal')">取消</button>
            <button class="btn btn-primary" onclick="saveBio()">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <h3>修改密码</h3>
        <div class="form-group">
            <label class="form-label">当前密码</label>
            <input type="password" class="form-input" id="oldPassword" placeholder="请输入当前密码">
        </div>
        <div class="form-group">
            <label class="form-label">新密码</label>
            <input type="password" class="form-input" id="newPassword" placeholder="至少6位">
        </div>
        <div class="form-group">
            <label class="form-label">确认新密码</label>
            <input type="password" class="form-input" id="confirmPassword" placeholder="再次输入新密码">
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('passwordModal')">取消</button>
            <button class="btn btn-primary" onclick="changePassword()">确认修改</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="phoneModal">
    <div class="modal">
        <h3>绑定手机</h3>
        <div class="form-group">
            <label class="form-label">手机号码</label>
            <input type="tel" class="form-input" id="newPhone" placeholder="请输入手机号" maxlength="11">
        </div>
        <div class="form-group">
            <label class="form-label">验证码</label>
            <div style="display:flex;gap:10px;">
                <input type="text" class="form-input" id="phoneCode" placeholder="6位验证码" maxlength="6" style="flex:1;">
                <button class="btn btn-outline btn-sm" id="sendPhoneCode" onclick="sendCode('phone')">发送验证码</button>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('phoneModal')">取消</button>
            <button class="btn btn-primary" onclick="savePhone()">确认绑定</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="emailModal">
    <div class="modal">
        <h3>绑定邮箱</h3>
        <div class="form-group">
            <label class="form-label">邮箱地址</label>
            <input type="email" class="form-input" id="newEmail" placeholder="请输入邮箱">
        </div>
        <div class="form-group">
            <label class="form-label">验证码</label>
            <div style="display:flex;gap:10px;">
                <input type="text" class="form-input" id="emailCode" placeholder="6位验证码" maxlength="6" style="flex:1;">
                <button class="btn btn-outline btn-sm" id="sendEmailCode" onclick="sendCode('email')">发送验证码</button>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('emailModal')">取消</button>
            <button class="btn btn-primary" onclick="saveEmail()">确认绑定</button>
        </div>
    </div>
</div>

<!-- 新建剧集模态框 -->
<div class="modal-overlay" id="seriesModal">
    <div class="modal">
        <h3 id="seriesModalTitle">新建剧集</h3>
        <input type="hidden" id="editSeriesId">
        <div class="form-group">
            <label class="form-label">剧名</label>
            <input type="text" class="form-input" id="seriesTitle" placeholder="例如：三体">
        </div>
        <div class="form-group">
            <label class="form-label">封面URL</label>
            <input type="text" class="form-input" id="seriesCover" placeholder="https://example.com/cover.jpg">
        </div>
        <div class="form-group">
            <label class="form-label">简介</label>
            <textarea class="form-input" id="seriesDesc" rows="3" placeholder="剧集简介（选填）"></textarea>
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('seriesModal')">取消</button>
            <button class="btn btn-primary" onclick="saveSeries()">保存</button>
        </div>
    </div>
</div>

<!-- 添加视频模态框 -->
<div class="modal-overlay" id="videoModal">
    <div class="modal">
        <h3 id="videoModalTitle">添加视频</h3>
        <input type="hidden" id="editVideoId">
        <div class="form-group">
            <label class="form-label">视频标题</label>
            <input type="text" class="form-input" id="videoTitle" placeholder="例如：第1集">
        </div>
        <div class="form-group">
            <label class="form-label">集数</label>
            <input type="number" class="form-input" id="videoEpisode" placeholder="1" min="1">
        </div>
        <div class="form-group">
            <label class="form-label">视频URL</label>
            <div style="display:flex;gap:8px;">
                <input type="text" class="form-input" id="videoUrl" placeholder="https://example.com/video.mp4 或 .m3u8" style="flex:1;">
                <button class="btn btn-ghost" onclick="parseVideoUrl()" id="parseBtn" style="white-space:nowrap;">
                    <i class="fas fa-magic"></i> 解析
                </button>
            </div>
            <div id="parseResult" style="display:none;margin-top:8px;padding:8px;background:var(--bg-secondary);border-radius:6px;font-size:13px;">
                <div id="parseStatus"></div>
                <div id="parseInfo" style="margin-top:4px;color:var(--text-secondary);"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">封面URL</label>
            <input type="text" class="form-input" id="videoCover" placeholder="https://example.com/thumb.jpg">
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="closeModal('videoModal')">取消</button>
            <button class="btn btn-primary" onclick="saveVideo()">保存</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API = '/api';
let currentUser = null;

function getToken() { return localStorage.getItem('token'); }
function authHeaders() { return { 'Authorization': 'Bearer ' + getToken(), 'Content-Type': 'application/json' }; }

function toast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + (isError ? 'toast-error' : 'toast-success') + ' show';
    setTimeout(() => t.classList.remove('show'), 3000);
}
// 别名
function showToast(msg, type) { toast(msg, type === 'error'); }

function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// 移动端导航菜单切换
function toggleMobileMenu() {
    document.querySelector('.navbar-links').classList.toggle('active');
}

// 移动端侧边栏切换
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}

// 切换面板
function switchPanel(name) {
    if (name === "player") loadPlayers();
    if (name === "upgrade") loadPlans();
    if (name === "materials") loadMaterials();
    if (name === "finance") loadFinance();
    if (name === "security") loadSocialBindings();
    if (name === "analytics") loadAnalytics();
    document.querySelectorAll('.nav-item[data-panel]').forEach(i => i.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`.nav-item[data-panel="${name}"]`).classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');
    
    // 移动端关闭侧边栏
    if (window.innerWidth <= 768) {
        document.querySelector('.sidebar').classList.remove('active');
        document.querySelector('.sidebar-overlay').classList.remove('active');
    }
}

// 侧边栏点击
document.querySelectorAll('.nav-item[data-panel]').forEach(item => {
    item.addEventListener('click', () => switchPanel(item.dataset.panel));
});

// 检查登录
function checkLogin() {
    if (!getToken()) {
        document.getElementById('loginRequired').style.display = 'block';
        document.getElementById('mainContent').style.display = 'none';
        return false;
    }
    document.getElementById('loginRequired').style.display = 'none';
    document.getElementById('mainContent').style.display = 'block';
    return true;
}

// 初始化导航
function initNav() {
    const userStr = localStorage.getItem('user');
    const navUser = document.getElementById('navUser');
    if (userStr) {
        try {
            const u = JSON.parse(userStr);
            const avatar = u.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(u.nickname || u.username || 'U') + '&background=6366f1&color=fff&size=80';
            navUser.innerHTML = `<img src="${avatar}" class="navbar-avatar" onclick="location.href='/user'">`;
        } catch(e) {}
    } else {
        navUser.innerHTML = '<a href="/login" class="btn btn-primary btn-sm"><i class="fas fa-sign-in-alt"></i> 登录</a>';
    }
}

// 加载用户信息
async function loadProfile() {
    try {
        const res = await fetch(API + '/auth/me', { headers: authHeaders() });
        const data = await res.json();
        if (!data.success) { logout(); return; }
        currentUser = data.data.user || data.data;
        localStorage.setItem('user', JSON.stringify(currentUser));
        renderAll();
    } catch(e) {
        currentUser = JSON.parse(localStorage.getItem('user') || '{}');
        renderAll();
    }
}

function renderAll() {
    const u = currentUser;
    const avatar = u.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(u.nickname || u.username || 'U') + '&background=6366f1&color=fff&size=160';

    // 头像
    document.getElementById('userAvatar').src = avatar;
    document.getElementById('overviewAvatar').src = avatar;

    // 名称
    document.getElementById('userName').textContent = u.nickname || u.username || '用户';
    document.getElementById('userId').textContent = 'ID: ' + (u.id || '-');
    document.getElementById('overviewName').textContent = u.nickname || u.username || '用户';
    document.getElementById('overviewEmail').textContent = u.email || u.phone || '未绑定手机/邮箱';

    // 资料
    document.getElementById('infoNickname').textContent = u.nickname || '未设置';
    document.getElementById('infoUsername').textContent = u.username || '-';
    document.getElementById('infoGender').textContent = u.gender === 1 ? '男' : u.gender === 2 ? '女' : '未设置';
    document.getElementById('infoBirthday').textContent = u.birthday || '未设置';
    document.getElementById('infoBio').textContent = u.bio || '这个人很懒，什么都没写~';

    // 安全
    document.getElementById('infoPhone').textContent = u.phone ? '已绑定：' + u.phone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2') : '未绑定';
    document.getElementById('phoneBtn').textContent = u.phone ? '更换' : '绑定';
    document.getElementById('infoEmail').textContent = u.email ? '已绑定：' + u.email.replace(/(.{2}).*(@.*)/, '$1***$2') : '未绑定';
    document.getElementById('emailBtn').textContent = u.email ? '更换' : '绑定';

    // 统计
    loadStats();
    loadNotices();
}

async function loadStats() {
    // 历史和收藏接口暂未实现，显示默认值
    document.getElementById('statViews').textContent = '0';
    document.getElementById('statFav').textContent = '0';
}

// 加载数据统计
async function loadAnalytics() {
    const loading = document.getElementById('analytics-loading');
    const content = document.getElementById('analytics-content');
    
    try {
        // 显示加载状态
        loading.style.display = 'block';
        content.style.display = 'none';
        
        const res = await fetch(API + '/user/stats', { headers: authHeaders() });
        if (!res.ok) {
            if (res.status === 401) {
                loading.innerHTML = '<i class="fas fa-lock" style="font-size:32px;margin-bottom:16px;display:block;color:var(--warning);"></i>登录已过期，请<a href="/login" style="color:var(--primary);text-decoration:underline;">重新登录</a>';
                // 清除过期的token
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                return;
            }
            loading.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:16px;display:block;color:var(--danger);"></i>加载失败，请稍后重试';
            return;
        }
        const data = await res.json();
        
        // 隐藏加载状态，显示内容
        loading.style.display = 'none';
        content.style.display = 'block';
        
        // 更新概览统计
        document.getElementById('analytics-players').textContent = data.basic.player_count || 0;
        document.getElementById('analytics-videos').textContent = data.basic.video_count || 0;
        document.getElementById('analytics-views').textContent = (data.basic.total_views || 0).toLocaleString();
        document.getElementById('analytics-orders').textContent = data.basic.order_count || 0;
        document.getElementById('analytics-spent').textContent = '¥' + (data.basic.total_spent || 0);
        
        // 更新广告收入
        document.getElementById('ad-preroll').textContent = '¥' + (data.ad_revenue.preroll || 0);
        document.getElementById('ad-midroll').textContent = '¥' + (data.ad_revenue.midroll || 0);
        document.getElementById('ad-postroll').textContent = '¥' + (data.ad_revenue.postroll || 0);
        document.getElementById('ad-total').textContent = '¥' + (data.ad_revenue.total || 0);
        
        // 更新版本信息
        const versionNames = { 'free': '免费版', 'basic': '基础版', 'advanced': '高级版', '旗舰': '旗舰版' };
        document.getElementById('version-name').textContent = versionNames[data.version.name] || data.version.name || '免费版';
        if (data.version.expire_at) {
            const expireDate = new Date(data.version.expire_at);
            document.getElementById('version-expire').textContent = '到期时间：' + expireDate.toLocaleDateString('zh-CN');
        } else {
            document.getElementById('version-expire').textContent = '永久有效';
        }
        
        // 渲染播放趋势图
        renderViewsChart(data.daily_views);
        
        // 渲染热门播放器
        renderTopPlayers(data.top_players);
    } catch (e) {
        console.error('加载统计数据失败:', e);
        loading.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:32px;margin-bottom:16px;display:block;color:var(--danger);"></i>加载失败，请稍后重试';
    }
}

// 渲染播放趋势柱状图
function renderViewsChart(dailyViews) {
    const container = document.getElementById('viewsChart');
    if (!dailyViews || dailyViews.length === 0) {
        container.innerHTML = '<div style="text-align:center;width:100%;padding:40px;color:var(--text-muted);">暂无数据</div>';
        return;
    }
    
    const maxViews = Math.max(...dailyViews.map(d => d.views), 1);
    const days = ['日', '一', '二', '三', '四', '五', '六'];
    
    container.innerHTML = dailyViews.map(d => {
        const date = new Date(d.date);
        const dayName = days[date.getDay()];
        const height = (d.views / maxViews * 100);
        return `
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                <div style="font-size:12px;color:var(--text-muted);">${d.views}</div>
                <div style="width:100%;height:${Math.max(height, 5)}%;background:linear-gradient(180deg, var(--primary) 0%, #818cf8 100%);border-radius:6px 6px 0 0;min-height:20px;transition:height 0.5s ease;"></div>
                <div style="font-size:12px;color:var(--text-muted);">${dayName}</div>
            </div>
        `;
    }).join('');
}

// 渲染热门播放器
function renderTopPlayers(players) {
    const container = document.getElementById('topPlayersList');
    if (!players || players.length === 0) {
        container.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">暂无播放器</div>';
        return;
    }
    
    const medals = ['🥇', '🥈', '🥉', '4', '5'];
    container.innerHTML = players.map((p, i) => `
        <div class="security-item" style="margin-bottom:12px;">
            <div class="security-left">
                <div style="width:36px;height:36px;border-radius:50%;background:${i < 3 ? 'linear-gradient(135deg, #fbbf24, #f59e0b)' : 'var(--bg)'};display:flex;align-items:center;justify-content:center;font-size:${i < 3 ? '18px' : '14px'};font-weight:700;color:${i < 3 ? '#fff' : 'var(--text-muted)'};">
                    ${medals[i]}
                </div>
                <div class="security-info">
                    <h4>${p.name || '播放器 #' + p.id}</h4>
                    <p>播放量：${(p.view_count || 0).toLocaleString()}</p>
                </div>
            </div>
        </div>
    `).join('');
}

// 加载公告列表
async function loadNotices() {
    try {
        const res = await fetch(API + '/notices/list', { headers: authHeaders() });
        const data = await res.json();
        const container = document.getElementById('noticesList');
        
        if (data.success && data.data && data.data.length > 0) {
            const typeIcons = {
                'system': 'fas fa-bell', 'update': 'fas fa-sync', 'activity': 'fas fa-gift',
                'maintenance': 'fas fa-tools', 'feature': 'fas fa-magic', 'security': 'fas fa-shield-alt'
            };
            const typeNames = {
                'system': '系统公告', 'update': '更新日志', 'activity': '活动公告',
                'maintenance': '维护通知', 'feature': '新功能', 'security': '安全提醒'
            };
            const typeColors = {
                'system': '#6366f1', 'update': '#3b82f6', 'activity': '#f59e0b',
                'maintenance': '#ef4444', 'feature': '#10b981', 'security': '#ec4899'
            };
            
            container.innerHTML = data.data.map(notice => `
                <div class="security-item" style="cursor:pointer;" onclick="showNoticeDetail(${notice.id})">
                    <div class="security-left">
                        <div class="security-icon" style="background:${typeColors[notice.type] || '#6366f1'}20;color:${typeColors[notice.type] || '#6366f1'};">
                            <i class="${typeIcons[notice.type] || 'fas fa-bell'}"></i>
                        </div>
                        <div class="security-info">
                            <h4>${notice.title}</h4>
                            <p>${notice.summary || (notice.content || '').substring(0, 60) + '...'}</p>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">
                                <span>${typeNames[notice.type] || '公告'}</span>
                                <span style="margin:0 8px;">·</span>
                                <span>${new Date(notice.published_at || notice.created_at).toLocaleDateString('zh-CN')}</span>
                            </div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right" style="color:var(--text-muted);"></i>
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
                    <i class="fas fa-bell-slash" style="font-size:48px;opacity:0.3;margin-bottom:16px;display:block;"></i>
                    <p>暂无公告</p>
                </div>
            `;
        }
    } catch(e) {
        document.getElementById('noticesList').innerHTML = `
            <div style="text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fas fa-exclamation-circle" style="font-size:24px;margin-bottom:12px;display:block;"></i>
                加载失败
            </div>
        `;
    }
}

// 显示公告详情
async function showNoticeDetail(id) {
    try {
        const res = await fetch(API + '/notices/' + id);
        const data = await res.json();
        if (data.success && data.data) {
            const notice = data.data;
            const modal = document.createElement('div');
            modal.className = 'modal-overlay show';
            modal.id = 'noticeDetailModal';
            modal.innerHTML = `
                <div class="modal" style="max-width:600px;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                        <h3 style="margin:0;">${notice.title}</h3>
                        <button class="btn btn-ghost" onclick="document.getElementById('noticeDetailModal').remove()" style="padding:8px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:16px;">
                        ${new Date(notice.published_at || notice.created_at).toLocaleString('zh-CN')}
                    </div>
                    <div style="line-height:1.8;color:var(--text);">${notice.content}</div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // 标记已读
            fetch(API + '/notices/' + id + '/read', { method: 'POST', headers: authHeaders() }).catch(() => {});
        }
    } catch(e) {
        toast('加载失败', true);
    }
}

// 保存头像
async function saveAvatar() {
    const avatar = document.getElementById('newAvatar').value;
    if (!avatar) { toast('请输入头像URL', true); return; }
    await updateProfile({ avatar });
    closeModal('avatarModal');
}

// 保存昵称
async function saveNickname() {
    const nickname = document.getElementById('newNickname').value;
    if (!nickname) { toast('请输入昵称', true); return; }
    await updateProfile({ nickname });
    closeModal('nicknameModal');
}

// 保存性别
async function saveGender() {
    const gender = parseInt(document.getElementById('newGender').value);
    await updateProfile({ gender });
    closeModal('genderModal');
}

// 保存生日
async function saveBirthday() {
    const birthday = document.getElementById('newBirthday').value;
    if (!birthday) { toast('请选择生日', true); return; }
    await updateProfile({ birthday });
    closeModal('birthdayModal');
}

// 保存简介
async function saveBio() {
    const bio = document.getElementById('newBio').value;
    await updateProfile({ bio });
    closeModal('bioModal');
}

// 更新资料通用方法
async function updateProfile(data) {
    try {
        const res = await fetch(API + '/auth/profile', { method: 'PUT', headers: authHeaders(), body: JSON.stringify(data) });
        const result = await res.json();
        if (result.success) {
            Object.assign(currentUser, data);
            localStorage.setItem('user', JSON.stringify(currentUser));
            renderAll();
            toast('保存成功');
        } else {
            toast(result.message || '保存失败', true);
        }
    } catch(e) { toast('操作失败', true); }
}

// 修改密码
async function changePassword() {
    const oldPassword = document.getElementById('oldPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (!oldPassword || !newPassword) { toast('请填写完整', true); return; }
    if (newPassword !== confirmPassword) { toast('两次密码不一致', true); return; }
    if (newPassword.length < 6) { toast('密码至少6位', true); return; }
    try {
        const res = await fetch(API + '/auth/change-password', { method: 'POST', headers: authHeaders(), body: JSON.stringify({ old_password: oldPassword, new_password: newPassword, new_password_confirmation: confirmPassword }) });
        const data = await res.json();
        if (data.success) { toast('密码修改成功'); closeModal('passwordModal'); document.getElementById('oldPassword').value = ''; document.getElementById('newPassword').value = ''; document.getElementById('confirmPassword').value = ''; } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 发送验证码
async function sendCode(type) {
    const target = type === 'phone' ? document.getElementById('newPhone').value : document.getElementById('newEmail').value;
    if (!target) { toast(type === 'phone' ? '请输入手机号' : '请输入邮箱', true); return; }
    const btn = type === 'phone' ? document.getElementById('sendPhoneCode') : document.getElementById('sendEmailCode');
    btn.disabled = true; let i = 60; btn.textContent = i + 's';
    const timer = setInterval(() => { i--; btn.textContent = i + 's'; if (i <= 0) { clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码'; } }, 1000);
    try {
        const res = await fetch(API + '/auth/send-code', { method: 'POST', headers: authHeaders(), body: JSON.stringify({ target, type }) });
        const data = await res.json();
        if (data.success) { toast('验证码已发送'); } else { toast(data.message, true); clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码'; }
    } catch(e) { toast('发送失败', true); clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码'; }
}

// 绑定手机
async function savePhone() {
    const phone = document.getElementById('newPhone').value;
    const code = document.getElementById('phoneCode').value;
    if (!phone || !code) { toast('请填写完整', true); return; }
    try {
        const res = await fetch(API + '/auth/bind-phone', { method: 'POST', headers: authHeaders(), body: JSON.stringify({ phone, code }) });
        const data = await res.json();
        if (data.success) { currentUser.phone = phone; renderAll(); toast('绑定成功'); closeModal('phoneModal'); } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 绑定邮箱
async function saveEmail() {
    const email = document.getElementById('newEmail').value;
    const code = document.getElementById('emailCode').value;
    if (!email || !code) { toast('请填写完整', true); return; }
    try {
        const res = await fetch(API + '/auth/bind-email', { method: 'POST', headers: authHeaders(), body: JSON.stringify({ email, code }) });
        const data = await res.json();
        if (data.success) { currentUser.email = email; renderAll(); toast('绑定成功'); closeModal('emailModal'); } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 导出数据
async function exportData() {
    try {
        const res = await fetch(API + '/auth/export-data', { headers: authHeaders() });
        const data = await res.json();
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a'); a.href = url; a.download = 'my-data.json'; a.click();
            URL.revokeObjectURL(url);
            toast('导出成功');
        } else { toast(data.message, true); }
    } catch(e) { toast('导出失败', true); }
}

// 清除历史
async function clearHistory() {
    if (!confirm('确定要清空观看历史吗？')) return;
    try {
        const res = await fetch(API + '/history/clear', { method: 'POST', headers: authHeaders() });
        const data = await res.json();
        if (data.success) { toast('已清空'); loadStats(); } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 清空收藏
async function clearFavorites() {
    if (!confirm('确定要清空所有收藏吗？')) return;
    try {
        const res = await fetch(API + '/favorites/clear', { method: 'POST', headers: authHeaders() });
        const data = await res.json();
        if (data.success) { toast('已清空'); loadStats(); } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 注销账号
async function deleteAccount() {
    if (!confirm('确定要注销账号吗？此操作不可恢复！')) return;
    try {
        const res = await fetch(API + '/auth/delete-account', { method: 'POST', headers: authHeaders() });
        const data = await res.json();
        if (data.success) { localStorage.clear(); toast('账号已注销'); setTimeout(() => location.href = '/', 2000); } else { toast(data.message, true); }
    } catch(e) { toast('操作失败', true); }
}

// 应用主题
function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else if (theme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
    } else {
        document.documentElement.removeAttribute('data-theme');
    }
}

// 保存设置
function saveSetting(key, value) {
    localStorage.setItem('setting_' + key, value);
    if (key === 'theme') applyTheme(value);
    toast('设置已保存');
}

// 加载设置
function loadSettings() {
    const theme = localStorage.getItem('setting_theme') || 'light';
    document.getElementById('themeSelect').value = theme;
    applyTheme(theme);
    
    document.getElementById('langSelect').value = localStorage.getItem('setting_language') || 'zh-CN';
    document.getElementById('notifyCheck').checked = localStorage.getItem('setting_notify') !== 'false';
    
    // 监听系统主题变化
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (localStorage.getItem('setting_theme') === 'auto') {
            applyTheme('auto');
        }
    });
}


// ========== 版本升级 ==========
let currentPlanLevel = 0;

function loadPlans() {
    // 获取套餐列表
    fetch(API + '/plans', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            if (data.data) {
                renderPlans(data.data);
            }
        })
        .catch(() => {
            document.getElementById('plansList').innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">加载失败，请刷新重试</div>';
        });
    
    // 获取用户当前版本
    fetch(API + '/user/plan-level', { headers: authHeaders() })
        .then(r => r.ok ? r.json() : { level: 0 })
        .then(data => {
            currentPlanLevel = data.level || 0;
            const planNames = {0: '免费版', 1: '基础版', 2: '高级版', 3: '旗舰版'};
            const planColors = {0: '#94a3b8', 1: '#3b82f6', 2: '#8b5cf6', 3: '#f59e0b'};
            document.getElementById('currentPlanName').textContent = planNames[currentPlanLevel] || '免费版';
            document.getElementById('currentPlanName').style.color = planColors[currentPlanLevel] || '#94a3b8';
        })
        .catch(() => {
            currentPlanLevel = 0;
        });
}

function renderPlans(plans) {
    const list = document.getElementById('plansList');
    
    // 只显示版本升级套餐（type=plan），不含广告模块和去广告
    const versionPlans = plans.filter(p => p.type === 'plan');
    
    // 按版本等级分组
    const grouped = {};
    versionPlans.forEach(p => {
        if (!grouped[p.level]) grouped[p.level] = [];
        grouped[p.level].push(p);
    });
    
    const levelNames = {0: '免费版', 1: '基础版', 2: '高级版', 3: '旗舰版'};
    const levelColors = {0: '#94a3b8', 1: '#3b82f6', 2: '#8b5cf6', 3: '#f59e0b'};
    const levelIcons = {0: '🆓', 1: '⭐', 2: '💎', 3: '👑'};
    const levelFeatures = {
        0: ['基础播放功能', '平台默认广告'],
        1: ['自定义主题色', '自定义水印', '自定义Logo'],
        2: ['基础版全部功能', '自定义域名', '品牌独立'],
        3: ['高级版全部功能', '广告模块', '去广告', 'API接口']
    };
    
    let html = '';
    
    // 显示 level 1-3 的付费版本
    [1, 2, 3].forEach(level => {
        const levelPlans = grouped[level] || [];
        if (levelPlans.length === 0) return;
        
        const isCurrentLevel = currentPlanLevel >= level;
        const color = levelColors[level];
        const features = levelFeatures[level] || [];
        
        // 按时长排序：月→季→年→永久
        const sortedPlans = levelPlans.sort((a,b) => a.duration_type - b.duration_type);
        
        html += `
        <div style="background:#fff;border:2px solid ${isCurrentLevel ? color : '#e2e8f0'};border-radius:16px;overflow:hidden;transition:all 0.3s;${isCurrentLevel ? 'opacity:0.7;' : ''}">
            <div style="background:linear-gradient(135deg,${color}15,${color}05);padding:20px;text-align:center;border-bottom:1px solid #f1f5f9;">
                <div style="font-size:36px;margin-bottom:8px;">${levelIcons[level]}</div>
                <div style="font-size:20px;font-weight:700;color:${color};">${levelNames[level]}</div>
                ${isCurrentLevel ? '<div style="font-size:12px;color:#10b981;margin-top:4px;">✓ 当前版本</div>' : ''}
            </div>
            <div style="padding:20px;">
                <ul style="list-style:none;padding:0;margin:0 0 16px;">
                    ${features.map(f => `<li style="padding:4px 0;font-size:13px;color:#475569;"><i class="fas fa-check" style="color:${color};margin-right:8px;"></i>${f}</li>`).join('')}
                </ul>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    ${sortedPlans.map(p => `
                        <button class="btn ${isCurrentLevel ? 'btn-ghost' : 'btn-outline'}" 
                            style="width:100%;justify-content:space-between;${isCurrentLevel ? 'opacity:0.5;cursor:not-allowed;' : ''}"
                            onclick="${isCurrentLevel ? '' : `purchasePlan(${p.id})`}">
                            <span>${p.duration_type === 1 ? '月卡' : p.duration_type === 2 ? '季卡' : p.duration_type === 3 ? '年卡' : '永久'}</span>
                            <div style="text-align:right;">
                                <span style="font-weight:700;color:${color};">¥${p.sale_price || p.price}</span>
                                ${p.sale_price && p.sale_price != p.price ? `<span style="font-size:11px;color:#94a3b8;text-decoration:line-through;margin-left:6px;">¥${p.price}</span>` : ''}
                                ${p.badge ? `<span style="font-size:10px;background:${color}20;color:${color};padding:1px 6px;border-radius:8px;margin-left:4px;">${p.badge}</span>` : ''}
                            </div>
                        </button>
                    `).join('')}
                </div>
            </div>
        </div>`;
    });
    
    list.innerHTML = html || '<div style="text-align:center;padding:40px;color:var(--text-muted);">暂无可升级套餐</div>';
}

function purchasePlan(planId) {
    if (!confirm('确定要购买此套餐吗？')) return;
    
    fetch(API + '/orders', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plan_id: planId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.id) {
            // 创建成功，跳转到余额支付
            if (confirm('订单已创建，是否使用余额支付？')) {
                payOrder(data.data.id);
            }
        } else {
            showToast(data.message || '购买失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

function payOrder(orderId) {
    fetch(API + '/orders/' + orderId + '/pay', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.message && data.message.includes('成功')) {
            showToast('支付成功！版本已升级，正在跳转...');
            // 跳转到播放器页面并打开创建表单
            setTimeout(() => {
                switchPanel('player');
                setTimeout(() => showCreatePlayer(), 500);
            }, 1000);
        } else {
            showToast(data.message || '支付失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// ========== 第三方账号绑定 ==========
const platformNames = { qq: 'QQ', wx: '微信', alipay: '支付宝', sina: '微博', baidu: '百度', douyin: '抖音', huawei: '华为', xiaomi: '小米', github: 'GitHub', google: 'Google' };
const platformIcons = { qq: 'fab fa-qq', wx: 'fab fa-weixin', alipay: 'fab fa-alipay', sina: 'fab fa-weibo', baidu: 'fas fa-search', douyin: 'fab fa-tiktok', huawei: 'fas fa-mobile-alt', xiaomi: 'fas fa-mobile-alt', github: 'fab fa-github', google: 'fab fa-google' };
const platformColors = { qq: '#12B7F5', wx: '#07C160', alipay: '#1677FF', sina: '#E6162D', baidu: '#3388FF', douyin: '#000', huawei: '#CF0A2C', xiaomi: '#FF6900', github: '#333', google: '#4285F4' };

function loadSocialBindings() {
    const container = document.getElementById('socialBindingsList');
    if (!container) return;
    
    // 同时获取已绑定列表和可用平台
    Promise.all([
        fetch(API + '/socialite/bindings', { headers: authHeaders() }).then(r => r.json()),
        fetch(API + '/socialite/platforms').then(r => r.json())
    ]).then(([bindingsData, platformsData]) => {
        const allBindings = bindingsData.data || []; // [{platform, bound, nickname, ...}]
        const platforms = platformsData.data || []; // [{key, name, icon, color}]
        
        // 只显示已绑定的
        const bindings = allBindings.filter(b => b.bound);
        // 未绑定的平台从platforms里取
        const boundPlatforms = bindings.map(b => b.platform);
        
        let html = '';

        // 已绑定的账号
        bindings.forEach(b => {
            const name = platformNames[b.platform] || b.platform;
            const icon = platformIcons[b.platform] || 'fas fa-user';
            const color = platformColors[b.platform] || '#666';
            const avatarHtml = b.avatar
                ? `<img src="${b.avatar}" class="social-bind-avatar" onerror="this.outerHTML='<div class=\\'social-bind-avatar-placeholder\\' style=\\'background:${color}15;color:${color};\\'><i class=\\'${icon}\\'></i></div>'" />`
                : `<div class="social-bind-avatar-placeholder" style="background:${color}15;color:${color};"><i class="${icon}"></i></div>`;
            html += `<div class="social-bind-card">
                <div class="social-bind-left">
                    ${avatarHtml}
                    <div class="social-bind-info">
                        <h4>${name} <span class="platform-badge" style="background:${color}15;color:${color};">已绑定</span></h4>
                        <p>${b.nickname || '未获取到昵称'}${b.bound_at ? ' · ' + b.bound_at : ''}</p>
                    </div>
                </div>
                <div class="social-bind-status">
                    <button class="btn btn-outline-danger btn-sm" onclick="unbindSocial('${b.platform}')" style="font-size:12px;padding:4px 12px;">解绑</button>
                </div>
            </div>`;
        });

        // 未绑定的平台（只显示后台开启了的）
        const unboundPlatforms = platforms.filter(p => !boundPlatforms.includes(p.key));
        unboundPlatforms.forEach(p => {
            html += `<div class="social-bind-card" style="opacity:0.7;">
                <div class="social-bind-left">
                    <div class="social-bind-avatar-placeholder" style="background:${p.color}10;color:${p.color};"><i class="${p.icon}"></i></div>
                    <div class="social-bind-info">
                        <h4>${p.name}</h4>
                        <p>点击绑定第三方账号</p>
                    </div>
                </div>
                <div class="social-bind-status">
                    <button class="btn btn-primary btn-sm" onclick="bindSocial('${p.key}')" style="font-size:12px;padding:4px 16px;">绑定</button>
                </div>
            </div>`;
        });
        
        if (!html) {
            html = '<div style="text-align:center;padding:20px;color:var(--text-muted);">暂无可用的第三方平台</div>';
        }
        
        container.innerHTML = html;
    }).catch(() => {
        container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">加载失败</div>';
    });
}

function bindSocial(platform) {
    // 打开弹窗进行第三方授权
    fetch(API + '/socialite/login?type=' + platform)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data && data.data.url) {
                // 清除之前的回调数据
                localStorage.removeItem('socialite_callback');
                
                const popup = window.open(data.data.url, 'socialite_bind', 'width=600,height=500,scrollbars=yes');
                
                // 处理绑定结果的通用函数
                function handleBindResult(result) {
                    if (result.need_bind && result.temp_key) {
                        fetch(API + '/socialite/bind', {
                            method: 'POST',
                            headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ temp_key: result.temp_key })
                        })
                        .then(r => r.json())
                        .then(bindResult => {
                            if (bindResult.success) {
                                showToast('绑定成功');
                                loadSocialBindings();
                            } else {
                                showToast(bindResult.message || '绑定失败', 'error');
                            }
                        })
                        .catch(() => showToast('绑定失败', 'error'));
                    } else if (result.success && result.token) {
                        showToast('该账号已绑定');
                        loadSocialBindings();
                    } else if (result.message) {
                        showToast(result.message, 'error');
                    }
                }
                
                // 监听postMessage回调
                function onMessage(e) {
                    if (e.data && (e.data.need_bind || e.data.success || e.data.message)) {
                        handleBindResult(e.data);
                        window.removeEventListener('message', onMessage);
                        clearInterval(pollTimer);
                    }
                }
                window.addEventListener('message', onMessage);
                
                // 兜底：轮询localStorage（popup跨域后window.opener可能丢失）
                const pollTimer = setInterval(() => {
                    const stored = localStorage.getItem('socialite_callback');
                    if (stored) {
                        try {
                            const result = JSON.parse(stored);
                            handleBindResult(result);
                        } catch(e) {}
                        localStorage.removeItem('socialite_callback');
                        clearInterval(pollTimer);
                        window.removeEventListener('message', onMessage);
                    }
                }, 1000);
                
                // 30秒后停止轮询
                setTimeout(() => {
                    clearInterval(pollTimer);
                    window.removeEventListener('message', onMessage);
                }, 30000);
            } else {
                showToast(data.message || '获取登录地址失败', 'error');
            }
        })
        .catch(() => showToast('网络错误', 'error'));
}

function unbindSocial(platform) {
    if (!confirm('确定要解绑' + (platformNames[platform] || platform) + '吗？')) return;
    fetch(API + '/socialite/unbind', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ platform: platform })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success || data.message) {
            showToast('已解绑');
            loadSocialBindings();
        } else {
            showToast(data.message || '解绑失败', 'error');
        }
    })
    .catch(() => showToast('操作失败', 'error'));
}

// ========== 财务管理 ==========
function loadFinance() {
    // 加载余额
    fetch(API + '/finance/balance', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            document.getElementById('userBalance').textContent = '¥' + parseFloat(data.balance || 0).toFixed(2);
            document.getElementById('totalRecharged').textContent = '¥' + parseFloat(data.total_recharged || 0).toFixed(2);
            document.getElementById('totalSpent').textContent = '¥' + parseFloat(data.total_spent || 0).toFixed(2);
        })
        .catch(() => {});
    
    // 加载最近交易
    fetch(API + '/finance/transactions?per_page=5', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('recentTransactions');
            if (!data.data || data.data.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">暂无交易记录</div>';
                return;
            }
            
            list.innerHTML = data.data.map(t => `
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9;">
                    <div>
                        <div style="font-weight:600;">${t.type_name || (t.type === 'recharge' ? '充值' : t.type === 'purchase' ? '消费' : '退款')}</div>
                        <div style="font-size:12px;color:var(--text-muted);">${t.description || '-'}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;color:${t.amount >= 0 ? 'var(--success)' : 'var(--danger)'};">
                            ${t.amount >= 0 ? '+' : ''}${parseFloat(t.amount).toFixed(2)}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">${new Date(t.created_at).toLocaleString('zh-CN')}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('recentTransactions').innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">加载失败</div>';
        });
}

function showRedeemModal() {
    document.getElementById('redeemModal').style.display = 'flex';
    document.getElementById('redeemCardNo').value = '';
    document.getElementById('redeemSecret').value = '';
    document.getElementById('redeemError').style.display = 'none';
}

function closeRedeemModal() {
    document.getElementById('redeemModal').style.display = 'none';
}

function redeemCard() {
    const cardNo = document.getElementById('redeemCardNo').value.trim();
    const secret = document.getElementById('redeemSecret').value.trim();
    
    if (!cardNo || !secret) {
        document.getElementById('redeemError').textContent = '请输入卡号和卡密';
        document.getElementById('redeemError').style.display = 'block';
        return;
    }
    
    fetch(API + '/finance/redeem', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ card_no: cardNo, card_secret: secret })
    })
    .then(r => r.json())
    .then(data => {
        if (data.message && data.message.includes('成功')) {
            showToast(data.message);
            closeRedeemModal();
            loadFinance();
        } else {
            document.getElementById('redeemError').textContent = data.message || '兑换失败';
            document.getElementById('redeemError').style.display = 'block';
        }
    })
    .catch(() => {
        document.getElementById('redeemError').textContent = '网络错误';
        document.getElementById('redeemError').style.display = 'block';
    });
}

function showTransactions() {
    document.getElementById('transactionsModal').style.display = 'flex';
    
    fetch(API + '/finance/transactions?per_page=50', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('transactionsList');
            if (!data.data || data.data.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">暂无交易记录</div>';
                return;
            }
            
            list.innerHTML = data.data.map(t => `
                <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0;border-bottom:1px solid #f1f5f9;">
                    <div>
                        <div style="font-weight:600;">${t.type_name || (t.type === 'recharge' ? '充值' : t.type === 'purchase' ? '消费' : '退款')}</div>
                        <div style="font-size:13px;color:var(--text-muted);">${t.description || '-'}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">${new Date(t.created_at).toLocaleString('zh-CN')}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;font-size:18px;color:${t.amount >= 0 ? 'var(--success)' : 'var(--danger)'};">
                            ${t.amount >= 0 ? '+' : ''}${parseFloat(t.amount).toFixed(2)}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">余额: ¥${parseFloat(t.balance_after).toFixed(2)}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('transactionsList').innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);">加载失败</div>';
        });
}

function closeTransactionsModal() {
    document.getElementById('transactionsModal').style.display = 'none';
}

// ========== 播放器管理 ==========
let currentPlayerId = null;

function loadPlayers() {
    fetch(API + '/user/players', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            // 更新额度显示
            if (data.quota) {
                document.getElementById('playerQuotaCount').textContent = data.quota.available;
                document.getElementById('playerUsedCount').textContent = data.quota.used;
            }
            
            // 根据配置显示购买按钮
            const purchaseBtn = document.querySelector('#panel-player .btn[onclick*="showToast"]');
            if (purchaseBtn && data.config) {
                if (data.config.enable_purchase) {
                    purchaseBtn.onclick = () => showToast('购买功能开发中');
                    purchaseBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> 购买额度 (¥' + data.config.price_per_quota + '/个)';
                } else {
                    purchaseBtn.style.display = 'none';
                }
            }
            
            const list = document.getElementById('playerList');
            if (!data.data || data.data.length === 0) {
                list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-muted);"><i class="fas fa-play-circle" style="font-size:48px;margin-bottom:16px;display:block;opacity:0.3;"></i>还没有播放器<br><button class="btn btn-primary btn-sm" style="margin-top:16px;" onclick="showCreatePlayer()"><i class="fas fa-plus"></i> 创建第一个</button></div>';
                return;
            }
            let html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">';
            data.data.forEach(p => {
                const versionLabels = {free:'免费版',basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
                const versionColors = {free:'#94a3b8',basic:'#3b82f6',advanced:'#8b5cf6',flagship:'#f59e0b'};
                const vLabel = versionLabels[p.version] || '免费版';
                const vColor = versionColors[p.version] || '#94a3b8';
                const templateLabels = {standard:'标准版',youku:'优酷风格'};
                const templateColors = {standard:'#6366f1',youku:'#ff6b35'};
                const tLabel = templateLabels[p.template] || '标准版';
                const tColor = templateColors[p.template] || '#6366f1';
                html += `<div style="background:var(--bg);border-radius:12px;overflow:hidden;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="showPlayerDetail(${p.id})">
                    <div style="height:120px;background:linear-gradient(135deg,${p.theme_color},${p.theme_color}88);display:flex;align-items:center;justify-content:center;position:relative;">
                        <i class="fas fa-play-circle" style="font-size:36px;color:rgba(255,255,255,0.8);"></i>
                        <div style="position:absolute;top:8px;right:8px;display:flex;gap:4px;">
                            <span style="background:${vColor};color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600;">${vLabel}</span>
                            <span style="background:${tColor};color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;font-weight:600;">${tLabel}</span>
                        </div>
                    </div>
                    <div style="padding:16px;">
                        <div style="font-weight:600;margin-bottom:6px;">${p.name}</div>
                        <div style="display:flex;gap:16px;font-size:12px;color:var(--text-muted);">
                            <span><i class="fas fa-video"></i> ${p.video_count||0} 视频</span>
                            <span><i class="fas fa-eye"></i> ${p.view_count||0} 播放</span>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            list.innerHTML = html;
        })
        .catch(() => {
            document.getElementById('playerList').innerHTML = '<div style="text-align:center;padding:40px;color:var(--danger);">加载失败</div>';
        });
}



function showCreatePlayer() {
    // 检查额度
    const quotaEl = document.getElementById('playerQuotaCount');
    if (quotaEl && parseInt(quotaEl.textContent) <= 0) {
        showToast('播放器额度不足，请先购买', 'error');
        return;
    }
    currentPlayerId = null;
    document.getElementById('playerFormTitle').textContent = '创建播放器';
    document.getElementById('playerId').value = '';
    document.getElementById('playerName').value = '';
    document.getElementById('playerColor').value = '#6366f1';
    document.getElementById('playerLogo').value = '';
    document.getElementById('playerProgressIcon').value = '';
    document.getElementById('playerWatermark').value = '';
    document.getElementById('playerRatio').value = '16:9';
    document.getElementById('playerRadius').value = '12px';
    document.getElementById('pAutoplay').checked = false;
    document.getElementById('pLoop').checked = false;
    document.getElementById('pMuted').checked = false;
    document.getElementById('pDanmaku').checked = false;
    document.getElementById('pDownload').checked = false;
    document.getElementById('pShare').checked = true;
    document.querySelectorAll('.color-dot').forEach(d => d.style.borderColor = 'transparent');
    document.querySelector('.color-dot[data-color="#6366f1"]').style.borderColor = '#1e293b';
    
    // 先显示表单，再初始化版本选择
    document.getElementById('playerFormCard').style.display = 'block';
    document.getElementById('playerDetailCard').style.display = 'none';
    
    // 确保所有版本卡片可见可点击
    document.querySelectorAll('.version-card').forEach(card => {
        card.style.pointerEvents = 'auto';
        card.style.opacity = '1';
    });
    
    // 初始化版本选择（默认免费版）
    selectVersion('free');

    // 初始化模板选择（默认标准版）
    selectTemplate('standard');
    const templateSection = document.getElementById('templateOptions');
    if (templateSection) {
        templateSection.parentElement.style.display = 'block';
    }

    // 加载用户套餐等级
    loadUserPlanLevel();
}

function hidePlayerForm() {
    document.getElementById('playerFormCard').style.display = 'none';
}

// 水印位置切换
function toggleWatermarkPosition() {
    const position = document.getElementById('playerWatermarkPosition').value;
    const customDiv = document.getElementById('watermarkCustomPosition');
    
    if (position === 'custom') {
        customDiv.style.display = 'block';
    } else {
        customDiv.style.display = 'none';
    }
}

// 版本选择相关
let selectedVersion = 'free';
let userPlanLevel = 0; // 用户当前套餐等级

function selectVersion(version) {
    const versionLevels = {free: 0, basic: 1, advanced: 2, flagship: 3};
    const versionNames = {free:'免费版',basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
    const requiredLevel = versionLevels[version];
    
    // 更新选中状态
    document.querySelectorAll('.version-card').forEach(card => {
        card.style.borderColor = '#e2e8f0';
        card.style.transform = '';
    });
    const selected = document.querySelector(`.version-card[data-version="${version}"]`);
    if (selected) {
        selected.style.borderColor = version === 'free' ? '#6366f1' : 
            version === 'basic' ? '#3b82f6' : 
            version === 'advanced' ? '#8b5cf6' : '#f59e0b';
        selected.style.transform = 'scale(1.02)';
    }
    
    document.getElementById('playerVersion').value = version;
    selectedVersion = version;
    
    // 检查用户是否有权限使用该版本
    const hint = document.getElementById('versionUpgradeHint');
    const hintText = document.getElementById('versionHintText');
    
    if (requiredLevel > userPlanLevel) {
        hint.style.display = 'block';
        hintText.textContent = `${versionNames[version]}需要升级到${versionNames[version]}套餐才能使用`;
    } else {
        hint.style.display = 'none';
    }
    
    // 更新自定义区域显示
    updateCustomizeSection(version);
}

// 模板选择相关
let selectedTemplate = 'standard';

function selectTemplate(template) {
    selectedTemplate = template;
    document.getElementById('playerTemplate').value = template;

    // 更新选中状态
    document.querySelectorAll('.template-card').forEach(card => {
        card.style.borderColor = card.dataset.template === 'youku' ? '#333' : '#e2e8f0';
        card.classList.remove('active');
    });

    const selected = document.querySelector(`.template-card[data-template="${template}"]`);
    if (selected) {
        selected.style.borderColor = template === 'youku' ? '#ff6b35' : '#6366f1';
        selected.classList.add('active');
    }
}

function updateCustomizeSection(version) {
    const customizeSection = document.getElementById('customizeSection');
    if (!customizeSection) return;
    
    const canCustomize = ['basic', 'advanced', 'flagship'].includes(version);
    const canDomain = ['advanced', 'flagship'].includes(version);
    const canAd = version === 'flagship';
    
    if (canCustomize) {
        // 基础版及以上：显示并启用自定义外观
        customizeSection.style.display = 'block';
        customizeSection.style.opacity = '1';
        customizeSection.style.filter = 'none';
        customizeSection.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = false;
            el.style.pointerEvents = 'auto';
        });
    } else {
        // 免费版：显示但禁用
        customizeSection.style.display = 'block';
        customizeSection.style.opacity = '0.5';
        customizeSection.style.filter = 'grayscale(50%)';
        customizeSection.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
            el.style.pointerEvents = 'none';
        });
    }
    
    // 自定义域名区域（高级版+）
    const domainSection = document.getElementById('domainSection');
    if (domainSection) {
        domainSection.style.display = canDomain ? 'block' : 'none';
    }
    
    // 广告模块区域（旗舰版）
    const adCreateSection = document.getElementById('adCreateSection');
    if (adCreateSection) {
        adCreateSection.style.display = canAd ? 'block' : 'none';
    }
}

// 加载用户套餐等级和可选版本配置
let availableVersions = ['free', 'basic', 'advanced', 'flagship']; // 后台配置的可选版本，默认全部可选

function loadUserPlanLevel() {
    fetch(API + '/user/plan-level', { headers: authHeaders() })
        .then(r => {
            if (!r.ok) throw new Error('API error');
            return r.json();
        })
        .then(data => {
            userPlanLevel = data.level || 0;
            availableVersions = (data.available_versions && data.available_versions.length > 0) 
                ? data.available_versions 
                : ['free', 'basic', 'advanced', 'flagship'];
            updateVersionOptions();
            // 自动选中用户已购买的最高版本
            const levelToVersion = {0:'free', 1:'basic', 2:'advanced', 3:'flagship'};
            const autoVersion = levelToVersion[userPlanLevel] || 'free';
            selectVersion(autoVersion);
        })
        .catch(() => {
            userPlanLevel = 0;
            availableVersions = ['free', 'basic', 'advanced', 'flagship'];
            updateVersionOptions();
        });
}

// 根据后台配置更新版本选项显示
function updateVersionOptions() {
    const versionCards = document.querySelectorAll('.version-card');
    versionCards.forEach(card => {
        const version = card.dataset.version;
        if (availableVersions.includes(version)) {
            card.style.display = 'block';
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
        } else {
            card.style.display = 'block';
            card.style.opacity = '0.4';
            card.style.pointerEvents = 'none';
        }
    });
}

function submitPlayerForm(e) {
    e.preventDefault();
    const id = document.getElementById('playerId').value;
    const body = {
        name: document.getElementById('playerName').value,
        theme_color: document.getElementById('playerColor').value,
        logo_url: document.getElementById('playerLogo').value,
        background_image: document.getElementById('playerBackground').value || null,
        background_image_mobile: document.getElementById('playerBackgroundMobile').value || null,
        progress_icon_url: document.getElementById('playerProgressIcon').value || null,
        parse_url: document.getElementById('playerParseUrl').value || null,
        watermark_text: document.getElementById('playerWatermark').value,
        watermark_position: document.getElementById('playerWatermarkPosition').value,
        watermark_font_size: parseInt(document.getElementById('playerWatermarkSize').value) || 14,
        watermark_color: document.getElementById('playerWatermarkColorHex').value || '#ffffff',
        watermark_opacity: parseInt(document.getElementById('playerWatermarkOpacity').value) / 100 || 0.3,
        watermark_x: document.getElementById('playerWatermarkX').value || null,
        watermark_y: document.getElementById('playerWatermarkY').value || null,
        aspect_ratio: document.getElementById('playerRatio').value,
        border_radius: document.getElementById('playerRadius').value,
        autoplay: document.getElementById('pAutoplay').checked ? 1 : 0,
        loop_play: document.getElementById('pLoop').checked ? 1 : 0,
        muted: document.getElementById('pMuted').checked ? 1 : 0,
        show_danmaku: document.getElementById('pDanmaku').checked ? 1 : 0,
        show_download: document.getElementById('pDownload').checked ? 1 : 0,
        show_share: document.getElementById('pShare').checked ? 1 : 0,
        template: document.getElementById('playerTemplate').value || 'standard',
    };
    
    // 新建时添加版本字段
    if (!id) {
        body.version = document.getElementById('playerVersion').value || 'free';
        
        // 高级版+：自定义域名
        const domainEl = document.getElementById('playerDomain');
        if (domainEl && domainEl.value && ['advanced', 'flagship'].includes(body.version)) {
            body.custom_domain = domainEl.value;
        }
        
        // 旗舰版：广告模块
        const adEl = document.getElementById('pAdEnabled');
        if (adEl && body.version === 'flagship') {
            body.ad_enabled = adEl.checked ? 1 : 0;
        }
    }
    
    const url = id ? API + '/user/players/' + id : API + '/user/players';
    const method = id ? 'PUT' : 'POST';
    fetch(url, {
        method,
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.id || data.data) {
            hidePlayerForm();
            loadPlayers();
            showToast(id ? '播放器已更新' : '播放器创建成功');
        } else {
            showToast(data.message || '操作失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

function showPlayerDetail(id) {
    fetch(API + '/user/players/' + id, { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const p = data.data || data;
            currentPlayerId = p.id;
            document.getElementById('detailName').textContent = p.name;
            document.getElementById('detailVideoCount').textContent = p.video_count || 0;
            document.getElementById('detailViewCount').textContent = (p.view_count || 0).toLocaleString();
            document.getElementById('detailEmbedCode').textContent = p.embed_code || '<iframe src="' + p.embed_url + '" width="100%" height="auto" frameborder="0" allowfullscreen></iframe>';
            document.getElementById('detailEmbedUrl').textContent = p.access_url || p.embed_url;
            document.getElementById('detailEmbedUrl').href = p.access_url || p.embed_url;
            
            // 版本信息显示
            const versionLabels = {free:'免费版',basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
            const versionColors = {free:'#94a3b8',basic:'#3b82f6',advanced:'#8b5cf6',flagship:'#f59e0b'};
            const versionInfo = p.version_info || {};
            const effectiveVersion = versionInfo.effective_version || p.version || 'free';
            const vLabel = versionLabels[effectiveVersion] || '免费版';
            const vColor = versionColors[effectiveVersion] || '#94a3b8';
            const versionActive = versionInfo.version_active !== false;
            
            document.getElementById('detailVersionBadge').textContent = vLabel;
            document.getElementById('detailVersionBadge').style.background = vColor;
            
            // 版本到期时间 - 动态倒计时
            if (window._versionCountdownTimer) { clearInterval(window._versionCountdownTimer); window._versionCountdownTimer = null; }
            const expireEl = document.getElementById('detailVersionExpire');
            if (expireEl) {
                if (!versionActive && effectiveVersion === 'free') {
                    expireEl.textContent = '❌ 版本已过期，已降级为免费版';
                    expireEl.style.color = '#ef4444';
                } else if (versionInfo.version_expire_at) {
                    const expDate = new Date(versionInfo.version_expire_at.replace(' ', 'T'));
                    function updateVersionCountdown() {
                        const now = new Date();
                        const diff = expDate - now;
                        if (diff <= 0) {
                            expireEl.textContent = '❌ 版本已过期，已降级为免费版';
                            expireEl.style.color = '#ef4444';
                            if (window._versionCountdownTimer) { clearInterval(window._versionCountdownTimer); window._versionCountdownTimer = null; }
                            return;
                        }
                        const d = Math.floor(diff / 86400000);
                        const h = Math.floor((diff % 86400000) / 3600000);
                        const m = Math.floor((diff % 3600000) / 60000);
                        const s = Math.floor((diff % 60000) / 1000);
                        const dd = d > 0 ? d + '天 ' : '';
                        expireEl.textContent = '⏳ ' + dd + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                        expireEl.style.color = diff <= 7*86400000 ? '#ef4444' : '#94a3b8';
                    }
                    updateVersionCountdown();
                    window._versionCountdownTimer = setInterval(updateVersionCountdown, 1000);
                } else if (effectiveVersion !== 'free') {
                    expireEl.textContent = '⏳ 永久有效';
                    expireEl.style.color = '#22c55e';
                } else {
                    expireEl.textContent = '';
                }
            }
            
            // 根据版本显示/隐藏功能
            const customizeSection = document.getElementById('customizeSection');
            if (customizeSection) {
                customizeSection.style.display = versionInfo.can_customize ? 'block' : 'none';
            }
            
            const adSection = document.getElementById('adModeSection');
            if (adSection) {
                adSection.style.display = 'block';
                // 显示/隐藏购买去广告提示（已购买且未过期则显示到期时间）
                const buyAdFreeHint = document.getElementById('buyAdFreeHint');
                if (buyAdFreeHint) {
                    if (versionInfo.has_ad_free) {
                        let label = '✅ 已开通去广告功能';
                        if (versionInfo.ad_free_expires_at) {
                            label += '，到期时间：' + versionInfo.ad_free_expires_at;
                        } else {
                            label += '，永久有效';
                        }
                        buyAdFreeHint.innerHTML = '<div style="display:flex;align-items:center;gap:12px;"><div style="font-size:32px;">🚫</div><div style="flex:1;"><div style="font-weight:600;color:#166534;">' + label + '</div></div></div>';
                        buyAdFreeHint.style.display = 'block';
                        buyAdFreeHint.style.borderColor = '#86efac';
                        buyAdFreeHint.style.background = 'linear-gradient(135deg,#f0fdf4,#dcfce7)';
                    } else {
                        buyAdFreeHint.style.display = 'block';
                    }
                }
                // 显示/隐藏购买广告模块提示
                const buyAdModuleHint = document.getElementById('buyAdModuleHint');
                if (buyAdModuleHint) {
                    if (versionInfo.has_ad_module) {
                        let label = '✅ 已开通广告投放功能';
                        if (versionInfo.ad_module_expires_at) {
                            label += '，到期时间：' + versionInfo.ad_module_expires_at;
                        } else {
                            label += '，永久有效';
                        }
                        buyAdModuleHint.innerHTML = '<div style="display:flex;align-items:center;gap:12px;"><div style="font-size:32px;">📢</div><div style="flex:1;"><div style="font-weight:600;color:#92400e;">' + label + '</div></div></div>';
                        buyAdModuleHint.style.display = 'block';
                        buyAdModuleHint.style.borderColor = '#fbbf24';
                        buyAdModuleHint.style.background = 'linear-gradient(135deg,#fff7ed,#fef3c7)';
                    } else {
                        buyAdModuleHint.style.display = 'block';
                    }
                }
                // 没有去广告功能时，禁用"无广告"选项
                const noneOpt = document.querySelector('#adModeSelect option[value="none"]');
                if (noneOpt) noneOpt.disabled = !versionInfo.has_ad_free;
                // 没有广告模块时，禁用"用户广告"和"混合模式"
                const userOpt = document.querySelector('#adModeSelect option[value="user"]');
                const mixedOpt = document.querySelector('#adModeSelect option[value="mixed"]');
                if (userOpt) userOpt.disabled = !versionInfo.has_ad_module;
                if (mixedOpt) mixedOpt.disabled = !versionInfo.has_ad_module;
            }
            
            // 广告设置
            document.getElementById('adModeSelect').value = p.ad_mode || 'platform';
            // 显示提示（选择用户广告或混合模式时提示去素材管理）
            const adModeHint = document.getElementById('adModeHint');
            if (adModeHint) adModeHint.style.display = (p.ad_mode === 'user' || p.ad_mode === 'mixed') ? 'block' : 'none';
            // 时段设置
            const durSection = document.getElementById('adDurationSection');
            if (durSection) durSection.style.display = (p.ad_mode === 'user' || p.ad_mode === 'mixed') ? 'block' : 'none';
            document.getElementById('prerollDurationInput').value = p.preroll_duration || 0;
            document.getElementById('midrollDurationInput').value = p.midroll_duration || 0;
            document.getElementById('postrollDurationInput').value = p.postroll_duration || 0;
            
            // 跑马灯设置
            const marqueeSection = document.getElementById('marqueeSection');
            if (marqueeSection) marqueeSection.style.display = (p.ad_mode === 'user' || p.ad_mode === 'mixed' || p.ad_mode === 'platform') ? 'block' : 'none';
            document.getElementById('showMarqueeInput').checked = !!p.show_marquee;
            document.getElementById('marqueeTextInput').value = p.marquee_text || '';
            document.getElementById('marqueeSpeedInput').value = p.marquee_speed || 12;
            const mc = p.marquee_color || '#ffffff';
            document.getElementById('marqueeColorInput').value = mc;
            document.getElementById('marqueeColorText').value = mc;
            
            document.getElementById('playerDetailCard').style.display = 'block';
            document.getElementById('playerFormCard').style.display = 'none';
            
            // 加载广告列表
            loadPlayerAds();
            // 加载部署代码
            loadDeployCode();
            // 加载所有播放器列表（用于广告投放）
            loadAllPlayersList();
        });
}

function editCurrentPlayer() {
    if (!currentPlayerId) return;
    fetch(API + '/user/players/' + currentPlayerId, { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const p = data.data || data;
            document.getElementById('playerFormTitle').textContent = '编辑播放器';
            document.getElementById('playerId').value = p.id;
            document.getElementById('playerName').value = p.name || '';
            document.getElementById('playerColor').value = p.theme_color || '#6366f1';
            document.getElementById('playerLogo').value = p.logo_url || '';
            document.getElementById('playerBackground').value = p.background_image || '';
            document.getElementById('playerBackgroundMobile').value = p.background_image_mobile || '';
            document.getElementById('playerProgressIcon').value = p.progress_icon_url || '';
            if(window.syncProgressIconPicker) syncProgressIconPicker(p.progress_icon_url || '');
            document.getElementById('playerParseUrl').value = p.parse_url || '';
            document.getElementById('playerWatermark').value = p.watermark_text || '';
            
            // 加载水印配置
            document.getElementById('playerWatermarkPosition').value = p.watermark_position || 'bottom-right';
            document.getElementById('playerWatermarkSize').value = p.watermark_font_size || 14;
            document.getElementById('wmSizeVal').textContent = (p.watermark_font_size || 14) + 'px';
            document.getElementById('playerWatermarkColor').value = p.watermark_color || '#ffffff';
            document.getElementById('playerWatermarkColorHex').value = p.watermark_color || '#ffffff';
            const opacity = Math.round((p.watermark_opacity || 0.3) * 100);
            document.getElementById('playerWatermarkOpacity').value = opacity;
            document.getElementById('wmOpacityVal').textContent = opacity + '%';
            document.getElementById('playerWatermarkX').value = p.watermark_x || '10px';
            document.getElementById('playerWatermarkY').value = p.watermark_y || '10px';
            toggleWatermarkPosition();
            
            document.getElementById('playerRatio').value = p.aspect_ratio || '16:9';
            document.getElementById('playerRadius').value = p.border_radius || '12px';
            document.getElementById('pAutoplay').checked = p.autoplay;
            document.getElementById('pLoop').checked = p.loop_play;
            document.getElementById('pMuted').checked = p.muted;
            document.getElementById('pDanmaku').checked = p.show_danmaku;
            document.getElementById('pDownload').checked = p.show_download;
            document.getElementById('pShare').checked = p.show_share;

            // 加载模板选择（编辑时隐藏模板选择区域）
            const template = p.template || 'standard';
            document.getElementById('playerTemplate').value = template;
            selectTemplate(template);

            // 编辑时隐藏模板选择
            const templateSection = document.getElementById('templateOptions');
            if (templateSection) {
                templateSection.parentElement.style.display = 'none';
            }

            document.querySelectorAll('.color-dot').forEach(d => d.style.borderColor = 'transparent');
            const dot = document.querySelector(`.color-dot[data-color="${p.theme_color}"]`);
            if (dot) dot.style.borderColor = '#1e293b';
            
            // 根据版本显示/隐藏自定义区域
            const versionInfo = p.version_info || {};
            const customizeSection = document.getElementById('customizeSection');
            if (customizeSection) {
                if (versionInfo.can_customize) {
                    customizeSection.style.display = 'block';
                    // 启用所有自定义字段
                    customizeSection.querySelectorAll('input, select').forEach(el => el.disabled = false);
                    customizeSection.style.opacity = '1';
                } else {
                    // 免费版：显示但禁用，并显示升级提示
                    customizeSection.style.display = 'block';
                    customizeSection.querySelectorAll('input, select').forEach(el => el.disabled = true);
                    customizeSection.style.opacity = '0.5';
                }
            }
            
            document.getElementById('playerDetailCard').style.display = 'none';
            document.getElementById('playerFormCard').style.display = 'block';
        });
}

function deleteCurrentPlayer() {
    if (!currentPlayerId || !confirm('确定删除此播放器？')) return;
    fetch(API + '/user/players/' + currentPlayerId, {
        method: 'DELETE',
        headers: { ...authHeaders(), 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(() => {
        currentPlayerId = null;
        document.getElementById('playerDetailCard').style.display = 'none';
        loadPlayers();
        showToast('播放器已删除');
    });
}

// 升级当前播放器版本
let selectedUpgradeVersion = null;

function upgradeCurrentPlayer() {
    if (!currentPlayerId) return;
    
    const currentVersion = document.getElementById('detailVersionBadge').textContent;
    const versionMap = {'免费版': 'free', '基础版': 'basic', '高级版': 'advanced', '旗舰版': 'flagship'};
    const currentCode = versionMap[currentVersion] || 'free';
    selectedUpgradeVersion = null;
    
    const allVersions = [
        {code: 'free', name: '免费版', icon: '🆓', color: '#94a3b8', level: 0},
        {code: 'basic', name: '基础版', icon: '⭐', color: '#3b82f6', level: 1},
        {code: 'advanced', name: '高级版', icon: '💎', color: '#8b5cf6', level: 2},
        {code: 'flagship', name: '旗舰版', icon: '👑', color: '#f59e0b', level: 3}
    ];
    const currentLevel = allVersions.find(v => v.code === currentCode)?.level || 0;
    // 只显示比当前版本高的版本（不允许降级）
    const versions = allVersions.filter(v => v.level > currentLevel);
    if (versions.length === 0) {
        showToast('已经是最高版本');
        return;
    }
    
    let cardsHtml = versions.map(v => {
        const isCurrent = v.code === currentCode;
        const onclick = isCurrent ? '' : "selectUpgradeVersion('" + v.code + "')";
        return '<div id="ver_' + v.code + '" onclick="' + onclick + '" style="' +
            'background:' + (isCurrent ? v.color + '15' : '#f8fafc') + ';' +
            'border:2px solid ' + (isCurrent ? v.color : '#e2e8f0') + ';' +
            'border-radius:12px;padding:16px;cursor:' + (isCurrent ? 'default' : 'pointer') + ';' +
            'text-align:center;transition:all 0.2s;">' +
            '<div style="font-size:28px;margin-bottom:8px;">' + v.icon + '</div>' +
            '<div style="font-weight:700;color:' + v.color + ';">' + v.name + '</div>' +
            (isCurrent ? '<div style="font-size:11px;color:#10b981;margin-top:4px;">✓ 当前版本</div>' : '') +
            '</div>';
    }).join('');
    
    const modal = document.createElement('div');
    modal.id = 'upgradeModal';
    modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;';
    modal.innerHTML = `
        <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:90%;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="font-size:18px;font-weight:700;">升级播放器版本</h3>
                <button onclick="document.getElementById('upgradeModal').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">&times;</button>
            </div>
            <div style="font-size:13px;color:#64748b;margin-bottom:16px;">当前版本：<strong>${currentVersion}</strong></div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;">
                ${cardsHtml}
            </div>
            <div id="upgradeHint" style="display:none;font-size:13px;color:#64748b;margin-bottom:12px;text-align:center;"></div>
            <button id="upgradeConfirmBtn" onclick="confirmUpgrade()" style="display:none;width:100%;padding:12px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;">
                确认升级
            </button>
        </div>
    `;
    document.body.appendChild(modal);
}

function selectUpgradeVersion(code) {
    selectedUpgradeVersion = code;
    // 高亮选中
    document.querySelectorAll('[id^="ver_"]').forEach(el => {
        el.style.borderColor = '#e2e8f0';
        el.style.background = '#f8fafc';
    });
    const el = document.getElementById('ver_' + code);
    if (el) {
        const colors = {basic:'#3b82f6',advanced:'#8b5cf6',flagship:'#f59e0b'};
        el.style.borderColor = colors[code] || '#6366f1';
        el.style.background = (colors[code] || '#6366f1') + '10';
    }
    // 显示确认按钮
    const names = {basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
    document.getElementById('upgradeHint').style.display = 'block';
    document.getElementById('upgradeHint').textContent = '将升级到：' + (names[code] || code);
    document.getElementById('upgradeConfirmBtn').style.display = 'block';
}

function confirmUpgrade() {
    if (!selectedUpgradeVersion) return;
    // 关闭升级弹窗，打开套餐购买弹窗
    const modal = document.getElementById('upgradeModal');
    if (modal) modal.remove();
    showPurchaseModal(selectedUpgradeVersion);
}

// 显示套餐购买弹窗
function showPurchaseModal(version) {
    const versionNames = {basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
    const versionColors = {basic:'#3b82f6',advanced:'#8b5cf6',flagship:'#f59e0b'};
    
    // 加载套餐列表
    fetch(API + '/plans', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            // 版本代码映射：播放器版本code → 套餐code前缀
            const versionMap = {
                basic: ['basic'],
                advanced: ['premium', 'pro'],
                flagship: ['ultimate'],
            };
            const matchCodes = versionMap[version] || [version];
            const plans = (data.data || []).filter(p => 
                p.type === 'plan' && matchCodes.some(c => p.code === c || p.code.startsWith(c + '_'))
            );
            if (plans.length === 0) {
                showToast('暂无可购买套餐', 'error');
                return;
            }
            
            let plansHtml = plans.map(p => `
                <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <div>
                        <div style="font-weight:600;">${p.duration_type === 1 ? '月卡' : p.duration_type === 2 ? '季卡' : p.duration_type === 3 ? '年卡' : '永久'}</div>
                        <div style="font-size:12px;color:#94a3b8;">${p.duration_days > 0 ? p.duration_days + '天' : '永久有效'}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:20px;font-weight:700;color:var(--primary);">¥${p.sale_price || p.price}</div>
                        ${p.sale_price ? '<div style="font-size:12px;color:#94a3b8;text-decoration:line-through;">¥' + p.price + '</div>' : ''}
                        <button onclick="purchasePlayerPlan(${p.id})" style="margin-top:6px;padding:6px 16px;background:${versionColors[version]};color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">购买</button>
                    </div>
                </div>
            `).join('');
            
            const modal = document.createElement('div');
            modal.id = 'purchaseModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:90%;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:18px;font-weight:700;">购买 ${versionNames[version] || version}</h3>
                        <button onclick="document.getElementById('purchaseModal').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">&times;</button>
                    </div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:16px;">选择套餐类型：</div>
                    ${plansHtml}
                </div>
            `;
            document.body.appendChild(modal);
        })
        .catch(() => showToast('加载套餐失败', 'error'));
}

// 购买播放器套餐
function purchasePlayerPlan(planId) {
    const modal = document.getElementById('purchaseModal');
    if (modal) modal.remove();
    
    fetch(API + '/orders', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plan_id: planId, player_id: currentPlayerId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.id) {
            if (confirm('订单已创建，是否使用余额支付？')) {
                payPlayerOrder(data.data.id);
            }
        } else {
            showToast(data.message || '创建订单失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// 支付播放器订单
function payPlayerOrder(orderId) {
    fetch(API + '/orders/' + orderId + '/pay', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.message && data.message.includes('成功')) {
            showToast('购买成功！播放器版本已升级');
            loadPlayers();
            setTimeout(() => showPlayerDetail(currentPlayerId), 500);
        } else {
            showToast(data.message || '支付失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// 显示去广告购买弹窗
function showAdFreePurchase() {
    fetch(API + '/plans', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const plans = (data.data || []).filter(p => p.type === 'ad_free');
            if (plans.length === 0) {
                showToast('暂无可购买的去广告套餐', 'error');
                return;
            }
            
            let plansHtml = plans.map(p => `
                <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <div>
                        <div style="font-weight:600;">${p.duration_type === 1 ? '月卡' : p.duration_type === 2 ? '季卡' : p.duration_type === 3 ? '年卡' : '永久'}</div>
                        <div style="font-size:12px;color:#94a3b8;">${p.duration_days > 0 ? p.duration_days + '天' : '永久有效'}</div>
                        <div style="font-size:11px;color:#10b981;margin-top:4px;">${p.badge || ''}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:20px;font-weight:700;color:#22c55e;">¥${p.sale_price || p.price}</div>
                        ${p.sale_price ? '<div style="font-size:12px;color:#94a3b8;text-decoration:line-through;">¥' + p.price + '</div>' : ''}
                        <button onclick="purchaseAdFree(${p.id})" style="margin-top:6px;padding:6px 16px;background:#22c55e;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">购买</button>
                    </div>
                </div>
            `).join('');
            
            const modal = document.createElement('div');
            modal.id = 'adFreeModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:90%;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:18px;font-weight:700;">🚫 购买去除广告</h3>
                        <button onclick="document.getElementById('adFreeModal').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">&times;</button>
                    </div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:16px;">购买后可关闭平台广告（开屏广告除外），享受纯净播放体验</div>
                    ${plansHtml}
                </div>
            `;
            document.body.appendChild(modal);
        })
        .catch(() => showToast('加载失败', 'error'));
}

// 购买去广告功能
function purchaseAdFree(planId) {
    const modal = document.getElementById('adFreeModal');
    if (modal) modal.remove();
    
    fetch(API + '/orders', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plan_id: planId, player_id: currentPlayerId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.id) {
            if (confirm('订单已创建，是否使用余额支付？')) {
                payPlayerOrder(data.data.id);
            }
        } else {
            showToast(data.message || '创建订单失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// 显示广告模块购买弹窗
function showAdModulePurchase() {
    fetch(API + '/plans', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const plans = (data.data || []).filter(p => p.type === 'ad_module');
            if (plans.length === 0) {
                showToast('暂无可购买的广告模块', 'error');
                return;
            }
            
            let plansHtml = plans.map(p => `
                <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <div>
                        <div style="font-weight:600;">${p.duration_type === 1 ? '月卡' : p.duration_type === 2 ? '季卡' : p.duration_type === 3 ? '年卡' : '永久'}</div>
                        <div style="font-size:12px;color:#94a3b8;">${p.duration_days > 0 ? p.duration_days + '天' : '永久有效'}</div>
                        <div style="font-size:11px;color:#10b981;margin-top:4px;">${p.badge || ''}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:20px;font-weight:700;color:#f59e0b;">¥${p.sale_price || p.price}</div>
                        ${p.sale_price ? '<div style="font-size:12px;color:#94a3b8;text-decoration:line-through;">¥' + p.price + '</div>' : ''}
                        <button onclick="purchaseAdModule(${p.id})" style="margin-top:6px;padding:6px 16px;background:#f59e0b;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">购买</button>
                    </div>
                </div>
            `).join('');
            
            const modal = document.createElement('div');
            modal.id = 'adModuleModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:90%;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:18px;font-weight:700;">📢 购买广告投放模块</h3>
                        <button onclick="document.getElementById('adModuleModal').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">&times;</button>
                    </div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:16px;">开通后即可自定义广告投放，赚取收益</div>
                    ${plansHtml}
                </div>
            `;
            document.body.appendChild(modal);
        })
        .catch(() => showToast('加载失败', 'error'));
}

// 购买广告模块
function purchaseAdModule(planId) {
    const modal = document.getElementById('adModuleModal');
    if (modal) modal.remove();
    
    fetch(API + '/orders', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plan_id: planId, player_id: currentPlayerId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.id) {
            if (confirm('订单已创建，是否使用余额支付？')) {
                payPlayerOrder(data.data.id);
            }
        } else {
            showToast(data.message || '创建订单失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// 显示额度购买弹窗
function showQuotaPurchase() {
    fetch(API + '/plans', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            const plans = (data.data || []).filter(p => p.code && p.code.startsWith('quota_'));
            if (plans.length === 0) {
                showToast('暂无可购买的额度套餐', 'error');
                return;
            }
            
            const quotaIcons = {10: '📦', 50: '📦📦', 100: '🏆'};
            let plansHtml = plans.map(p => `
                <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:14px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <div>
                        <div style="font-weight:600;">${quotaIcons[p.description?.match(/\d+/)?.[0]] || '📦'} ${p.name}</div>
                        <div style="font-size:12px;color:#94a3b8;">${p.description || '永久有效'}</div>
                        <div style="font-size:11px;color:#10b981;margin-top:4px;">${p.badge || ''}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:20px;font-weight:700;color:#8b5cf6;">¥${p.sale_price || p.price}</div>
                        ${p.sale_price && p.sale_price != p.price ? '<div style="font-size:12px;color:#94a3b8;text-decoration:line-through;">¥' + p.price + '</div>' : ''}
                        <button onclick="purchaseQuota(${p.id})" style="margin-top:6px;padding:6px 16px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">购买</button>
                    </div>
                </div>
            `).join('');
            
            const modal = document.createElement('div');
            modal.id = 'quotaModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:90%;max-height:80vh;overflow-y:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:18px;font-weight:700;">📦 购买播放器额度</h3>
                        <button onclick="document.getElementById('quotaModal').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">&times;</button>
                    </div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:16px;">购买后永久增加播放器额度，可用于创建更多播放器</div>
                    ${plansHtml}
                </div>
            `;
            document.body.appendChild(modal);
        })
        .catch(() => showToast('加载失败', 'error'));
}

// 购买额度
function purchaseQuota(planId) {
    const modal = document.getElementById('quotaModal');
    if (modal) modal.remove();
    
    fetch(API + '/orders', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ plan_id: planId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.id) {
            if (confirm('订单已创建，是否使用余额支付？')) {
                payQuotaOrder(data.data.id);
            }
        } else {
            showToast(data.message || '创建订单失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

// 支付额度订单
function payQuotaOrder(orderId) {
    fetch(API + '/orders/' + orderId + '/pay', {
        method: 'POST',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.message && data.message.includes('成功')) {
            showToast(data.message || '支付成功！额度已增加');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || '支付失败', 'error');
        }
    })
    .catch(() => showToast('网络错误', 'error'));
}

function doUpgradePlayer(version) {
    const modal = document.getElementById('upgradeModal');
    if (modal) modal.remove();
    
    console.log('开始升级:', version, 'currentPlayerId:', currentPlayerId);
    
    fetch(API + '/user/players/' + currentPlayerId, {
        method: 'PUT',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ version: version })
    })
    .then(r => {
        console.log('响应状态:', r.status);
        if (!r.ok) {
            return r.json().then(err => { throw new Error(err.message || '请求失败'); });
        }
        return r.json();
    })
    .then(data => {
        console.log('升级响应:', data);
        const names = {basic:'基础版',advanced:'高级版',flagship:'旗舰版'};
        if (data.data && data.data.version) {
            showToast('已升级到' + (names[data.data.version] || data.data.version));
            loadPlayers();
            setTimeout(() => showPlayerDetail(currentPlayerId), 300);
        } else if (data.message) {
            showToast(data.message, 'error');
        } else {
            showToast('升级成功');
            loadPlayers();
            setTimeout(() => showPlayerDetail(currentPlayerId), 300);
        }
    })
    .catch(err => {
        console.error('升级错误:', err);
        showToast(err.message || '网络错误', 'error');
    });
}

function copyEmbedCode() {
    const code = document.getElementById('detailEmbedCode').textContent;
    navigator.clipboard.writeText(code).then(() => showToast('已复制'));
}

// ========== 广告管理 ==========
function loadPlayerAds() { /* 已移除内联广告管理，统一在素材管理中操作 */ }

function showAddAdForm() {}
function hideAdForm() {}
function editAd(id) {}
function saveAd() {}
function deleteAd(id) {}

function updateAdMode() {
    const mode = document.getElementById('adModeSelect').value;
    
    // 选择"无广告"时校验是否已购买去广告功能
    if (mode === 'none') {
        const noneOpt = document.querySelector('#adModeSelect option[value="none"]');
        if (noneOpt && noneOpt.disabled) {
            document.getElementById('adModeSelect').value = 'platform'; // 还原
            showAdFreePurchase(); // 弹出购买提示
            return;
        }
    }
    // 选择"用户广告/混合"时校验是否已开通广告模块
    if (mode === 'user' || mode === 'mixed') {
        const opt = document.querySelector('#adModeSelect option[value="' + mode + '"]');
        if (opt && opt.disabled) {
            document.getElementById('adModeSelect').value = 'platform'; // 还原
            showAdModulePurchase();
            return;
        }
    }
    
    fetch(API + '/user/players/' + currentPlayerId + '/ad-mode', {
        method: 'PUT',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ ad_mode: mode })
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) {
            showToast('广告模式已更新');
            const adModeHint = document.getElementById('adModeHint');
            if (adModeHint) adModeHint.style.display = (mode === 'user' || mode === 'mixed') ? 'block' : 'none';
            const durSection = document.getElementById('adDurationSection');
            if (durSection) durSection.style.display = (mode === 'user' || mode === 'mixed') ? 'block' : 'none';
            const marqueeSection = document.getElementById('marqueeSection');
            if (marqueeSection) marqueeSection.style.display = (mode === 'none') ? 'none' : 'block';
        }
    });
}

// 保存时段设置
function saveAdDurations() {
    const body = {
        preroll_duration: parseInt(document.getElementById('prerollDurationInput').value) || 0,
        midroll_duration: parseInt(document.getElementById('midrollDurationInput').value) || 0,
        postroll_duration: parseInt(document.getElementById('postrollDurationInput').value) || 0,
    };
    fetch(API + '/user/players/' + currentPlayerId, {
        method: 'PUT',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) showToast('时段设置已保存');
    })
    .catch(() => showToast('保存失败', 'error'));
}

// 保存跑马灯设置
function saveMarqueeSettings() {
    const body = {
        show_marquee: document.getElementById('showMarqueeInput').checked ? 1 : 0,
        marquee_text: document.getElementById('marqueeTextInput').value.trim(),
        marquee_speed: parseInt(document.getElementById('marqueeSpeedInput').value) || 12,
        marquee_color: document.getElementById('marqueeColorInput').value || '#ffffff'
    };
    fetch(API + '/user/players/' + currentPlayerId, {
        method: 'PUT',
        headers: { ...authHeaders(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) showToast('跑马灯设置已保存');
    })
    .catch(() => showToast('保存失败', 'error'));
}

// ========== 素材管理（独立面板） ==========
let materialsList = [];
let allPlayersForMaterial = [];

function loadMaterials() {
    const headers = authHeaders();
    
    // 加载所有播放器列表（用于投放目标选择）- 只加载有广告模块的播放器
    fetch(API + '/user/players-list?ad_module_only=1', { headers })
        .then(r => r.json())
        .then(data => { allPlayersForMaterial = data.data || []; });
    
    // 加载所有播放器的广告
    fetch(API + '/user/players', { headers })
        .then(r => r.json())
        .then(data => {
            const players = data.data?.data || data.data || [];
            materialsList = [];
            
            // 如果只有一个播放器，直接加载其广告
            if (players.length === 1) {
                loadPlayerAdsForMaterials(players[0].id);
            } else if (players.length > 1) {
                // 加载所有播放器的广告
                let loaded = 0;
                players.forEach(p => {
                    fetch(API + '/user/players/' + p.id + '/ads', { headers })
                        .then(r => r.json())
                        .then(adData => {
                            const ads = adData.data || [];
                            ads.forEach(ad => {
                                ad._playerName = p.name;
                                ad._playerId = p.id;
                                materialsList.push(ad);
                            });
                            loaded++;
                            if (loaded >= players.length) renderMaterialsList();
                        });
                });
            } else {
                renderMaterialsList();
            }
        })
        .catch(err => {
            console.error('加载素材失败:', err);
            document.getElementById('materialsList').innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">加载失败</div>';
        });
}

function loadPlayerAdsForMaterials(playerId) {
    fetch(API + '/user/players/' + playerId + '/ads', { headers: authHeaders() })
        .then(r => r.json())
        .then(data => {
            materialsList = (data.data || []).map(ad => {
                ad._playerId = playerId;
                return ad;
            });
            renderMaterialsList();
        });
}

function renderMaterialsList() {
    const el = document.getElementById('materialsList');
    if (materialsList.length === 0) {
        el.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);"><i class="fas fa-images" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.3"></i>暂无素材，点击上方"添加素材"创建</div>';
        return;
    }
    
    const typeIcons = { video: 'fa-video', image: 'fa-image', text: 'fa-font' };
    const posNames = { preroll: '前贴片', midroll: '中贴片', postroll: '后贴片', pause: '暂停' };
    
    el.innerHTML = materialsList.map(ad => `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                <div style="width:40px;height:40px;border-radius:8px;background:var(--bg-dark);display:flex;align-items:center;justify-content:center;">
                    <i class="fas ${typeIcons[ad.type] || 'fa-file'}" style="color:var(--primary);"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-weight:500;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${ad.name || '未命名'}</div>
                    <div style="font-size:12px;color:var(--text-muted);">${posNames[ad.position] || ad.position} · ${ad.duration || 0}秒${ad._playerName ? ' · ' + ad._playerName : ''}</div>
                </div>
            </div>
            <div style="display:flex;gap:4px;">
                <button class="btn btn-ghost btn-sm" onclick="editMaterial(${ad.id}, ${ad._playerId || currentPlayerId})" title="编辑"><i class="fas fa-edit"></i></button>
                <button class="btn btn-ghost btn-sm" onclick="deleteMaterial(${ad.id}, ${ad._playerId || currentPlayerId})" title="删除" style="color:#ef4444;"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `).join('');
}

function showMaterialForm() {
    document.getElementById('materialFormCard').style.display = 'block';
    document.getElementById('materialId').value = '';
    document.getElementById('materialName').value = '';
    document.getElementById('materialMediaUrl').value = '';
    document.getElementById('materialType').value = 'video';
    document.getElementById('materialPosition').value = 'preroll';
    document.getElementById('materialSkippable').checked = true;
    document.getElementById('materialDuration').value = '15';
    document.getElementById('materialSkipAfter').value = '5';
    document.getElementById('materialProgressIcon').value = '';
    document.getElementById('materialClickUrl').value = '';
    document.getElementById('materialContent').value = '';
    document.getElementById('materialCoverUrl').value = '';
    document.getElementById('materialTitle').value = '';
    document.getElementById('materialDescription').value = '';
    document.getElementById('materialCtaText').value = '';
    document.getElementById('materialCtaUrl').value = '';
    document.getElementById('materialLogoUrl').value = '';
    document.getElementById('materialPriority').value = '0';
    document.getElementById('materialFrequencyCap').value = '0';
    document.getElementById('materialStartAt').value = '';
    document.getElementById('materialEndAt').value = '';
    document.getElementById('materialTargetType').value = 'single';
    toggleMaterialFields();
    toggleMaterialTargetPlayers();
}

function hideMaterialForm() {
    document.getElementById('materialFormCard').style.display = 'none';
}

// 根据素材类型显示/隐藏字段
function toggleMaterialFields() {
    const type = document.getElementById('materialType').value;
    const contentGroup = document.getElementById('materialContentGroup');
    const coverGroup = document.getElementById('materialCoverGroup');
    if (contentGroup) contentGroup.style.display = (type === 'text' || type === 'html') ? 'block' : 'none';
    if (coverGroup) coverGroup.style.display = (type === 'video') ? 'block' : 'none';
}

function toggleMaterialTargetPlayers() {
    const targetType = document.getElementById('materialTargetType').value;
    const section = document.getElementById('materialTargetPlayers');
    if (targetType === 'multiple') {
        section.style.display = 'block';
        renderMaterialPlayersList();
    } else {
        section.style.display = 'none';
    }
}

function renderMaterialPlayersList() {
    const el = document.getElementById('materialPlayersList');
    if (allPlayersForMaterial.length === 0) {
        el.innerHTML = '<div style="color:var(--text-muted);text-align:center;padding:12px;"><i class="fas fa-info-circle"></i> 暂无已开通广告模块的播放器<br><span style="font-size:12px;">请先在播放器详情页购买广告模块</span></div>';
        return;
    }
    el.innerHTML = allPlayersForMaterial.map(p => `
        <label style="display:flex;align-items:center;gap:8px;padding:4px;cursor:pointer;">
            <input type="checkbox" class="material-player-cb" value="${p.id}">
            <span style="font-size:13px;">${p.name}</span>
            <span style="font-size:11px;color:var(--primary);background:var(--primary-bg);padding:1px 6px;border-radius:4px;">已开通</span>
        </label>
    `).join('');
}

function saveMaterial() {
    const name = document.getElementById('materialName').value.trim();
    const mediaUrl = document.getElementById('materialMediaUrl').value.trim();
    
    if (!name) { showToast('请填写素材名称'); return; }
    if (!mediaUrl) { showToast('请填写素材地址'); return; }
    
    const targetType = document.getElementById('materialTargetType').value;
    let targetPlayerIds = [];
    
    if (targetType === 'single') {
        // 当前播放器列表第一个
        if (allPlayersForMaterial.length > 0) {
            targetPlayerIds = [allPlayersForMaterial[0].id];
        }
    } else if (targetType === 'multiple') {
        document.querySelectorAll('.material-player-cb:checked').forEach(cb => {
            targetPlayerIds.push(parseInt(cb.value));
        });
        if (targetPlayerIds.length === 0) { showToast('请选择播放器'); return; }
    } else {
        targetPlayerIds = allPlayersForMaterial.map(p => p.id);
    }
    
    if (targetPlayerIds.length === 0) { showToast('没有可用的播放器'); return; }
    
    const body = {
        name: name,
        media_type: document.getElementById('materialType').value,
        media_url: mediaUrl,
        cover_url: document.getElementById('materialCoverUrl').value.trim(),
        title: document.getElementById('materialTitle').value.trim(),
        description: document.getElementById('materialDescription').value.trim(),
        content: document.getElementById('materialContent').value.trim(),
        cta_text: document.getElementById('materialCtaText').value.trim(),
        cta_url: document.getElementById('materialCtaUrl').value.trim(),
        logo_url: document.getElementById('materialLogoUrl').value.trim(),
        position: document.getElementById('materialPosition').value,
        skippable: document.getElementById('materialSkippable').checked,
        duration: parseInt(document.getElementById('materialDuration').value) || 15,
        skip_after: parseInt(document.getElementById('materialSkipAfter').value) || 5,
        progress_icon: document.getElementById('materialProgressIcon').value.trim() || null,
        click_url: document.getElementById('materialClickUrl').value.trim(),
        priority: parseInt(document.getElementById('materialPriority').value) || 0,
        frequency_cap: parseInt(document.getElementById('materialFrequencyCap').value) || 0,
        start_at: document.getElementById('materialStartAt').value || null,
        end_at: document.getElementById('materialEndAt').value || null,
        target_type: targetType,
        target_player_ids: targetType === 'all' ? [] : targetPlayerIds,
        enabled: true
    };
    
    const editId = document.getElementById('materialId').value;
    
    // 保存到每个目标播放器
    let saved = 0;
    let total = targetPlayerIds.length;
    
    targetPlayerIds.forEach(pid => {
        const url = editId ? API + '/user/players/' + pid + '/ads/' + editId : API + '/user/players/' + pid + '/ads';
        const method = editId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: { ...authHeaders(), 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        .then(r => r.json())
        .then(data => {
            saved++;
            if (saved >= total) {
                showToast('保存成功');
                hideMaterialForm();
                loadMaterials();
            }
        })
        .catch(err => {
            saved++;
            console.error('保存失败:', err);
            if (saved >= total) {
                showToast('部分保存失败');
                loadMaterials();
            }
        });
    });
}

function editMaterial(adId, playerId) {
    const ad = materialsList.find(a => a.id === adId);
    if (!ad) return;
    
    document.getElementById('materialFormCard').style.display = 'block';
    document.getElementById('materialId').value = ad.id;
    document.getElementById('materialName').value = ad.name || '';
    document.getElementById('materialMediaUrl').value = ad.media_url || '';
    document.getElementById('materialType').value = ad.media_type || 'video';
    document.getElementById('materialPosition').value = ad.position || 'preroll';
    document.getElementById('materialSkippable').checked = ad.skippable !== false;
    document.getElementById('materialDuration').value = ad.duration || 15;
    document.getElementById('materialSkipAfter').value = ad.skip_after || 5;
    document.getElementById('materialProgressIcon').value = ad.progress_icon || '';
    document.getElementById('materialClickUrl').value = ad.click_url || '';
    document.getElementById('materialContent').value = ad.content || '';
    document.getElementById('materialCoverUrl').value = ad.cover_url || '';
    document.getElementById('materialTitle').value = ad.title || '';
    document.getElementById('materialDescription').value = ad.description || '';
    document.getElementById('materialCtaText').value = ad.cta_text || '';
    document.getElementById('materialCtaUrl').value = ad.cta_url || '';
    document.getElementById('materialLogoUrl').value = ad.logo_url || '';
    document.getElementById('materialPriority').value = ad.priority || 0;
    document.getElementById('materialFrequencyCap').value = ad.frequency_cap || 0;
    document.getElementById('materialStartAt').value = ad.start_at ? ad.start_at.replace(' ', 'T').substring(0, 16) : '';
    document.getElementById('materialEndAt').value = ad.end_at ? ad.end_at.replace(' ', 'T').substring(0, 16) : '';
    toggleMaterialFields();
}

function deleteMaterial(adId, playerId) {
    if (!confirm('确定删除此素材？')) return;
    
    fetch(API + '/user/players/' + playerId + '/ads/' + adId, {
        method: 'DELETE',
        headers: authHeaders()
    })
    .then(r => r.json())
    .then(data => {
        showToast('删除成功');
        loadMaterials();
    })
    .catch(err => {
        showToast('删除失败');
    });
}

// ========== 部署代码 ==========
let deployCodeData = null;

function switchDeployTab(tab) {
    // 更新标签样式
    document.querySelectorAll('.deploy-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    
    // 更新内容显示
    document.querySelectorAll('.deploy-content').forEach(c => c.style.display = 'none');
    document.getElementById('deploy-' + tab).style.display = 'block';
}

function loadDeployCode() {
    if (!currentPlayerId) return;
    
    fetch(API + '/user/players/' + currentPlayerId + '/deploy', { headers: authHeaders() })
        .then(r => {
            if (!r.ok) throw new Error('请求失败');
            return r.json();
        })
        .then(data => {
            if (data.data) {
                deployCodeData = data.data;
                
                // 更新HTML代码
                document.getElementById('deployCodeHtml').textContent = data.data.html;
                
                // 更新JS代码
                document.getElementById('deployCodeJs').textContent = data.data.js;
                
                // 更新iframe代码
                document.getElementById('deployCodeIframe').textContent = data.data.embed_code;
                
                // 更新动态播放接口示例
                const embedUrl = data.data.embed_url;
                const urlExample = embedUrl + (embedUrl.includes('?') ? '&' : '?') + 'url=https://example.com/video.mp4';
                document.getElementById('iframeUrlExample').textContent = urlExample;
                
                // 更新一键部署包的播放器信息
                document.getElementById('packagePlayerId').textContent = data.data.player_id;
                document.getElementById('packagePlayerKey').textContent = '***';
                
                // 更新播放器信息
                document.getElementById('detailPlayerId').textContent = data.data.player_id;
                document.getElementById('detailPlayerKey').textContent = '***';
                
                // 显示广告信息
                if (data.data.has_ads) {
                    document.getElementById('adsInfoBadge').style.display = 'block';
                    document.getElementById('adsCount').textContent = data.data.ads_count;
                } else {
                    document.getElementById('adsInfoBadge').style.display = 'none';
                }
            } else {
                document.getElementById('deployCodeHtml').textContent = '加载失败：' + (data.message || '未知错误');
            }
        })
        .catch(err => {
            console.error('加载部署代码失败:', err);
            document.getElementById('deployCodeHtml').textContent = '加载失败，请刷新重试';
        });
}

function downloadDeployCode(type) {
    if (!deployCodeData) return;
    
    let content, filename, mimeType;
    
    if (type === 'js') {
        content = deployCodeData.js;
        filename = 'player-' + currentPlayerId + '.js';
        mimeType = 'application/javascript';
    } else {
        content = deployCodeData.html;
        filename = 'player-' + currentPlayerId + '.html';
        mimeType = 'text/html';
    }
    
    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
    showToast('已下载 ' + filename);
}

function copyDeployCode(type) {
    if (!deployCodeData) return;
    
    let content;
    if (type === 'js') {
        content = deployCodeData.js;
    } else if (type === 'iframe') {
        content = deployCodeData.embed_code;
    } else {
        content = deployCodeData.html;
    }
    
    navigator.clipboard.writeText(content).then(() => {
        showToast('已复制到剪贴板');
    });
}

function showPlayerKey() {
    if (!deployCodeData) return;
    
    document.getElementById('detailPlayerKey').textContent = deployCodeData.player_key;
    document.getElementById('packagePlayerKey').textContent = deployCodeData.player_key;
}

// ========== 多播放器投放 ==========
let allPlayersList = [];

function loadAllPlayersList() { /* 已移除，素材管理有独立加载 */ }
function toggleTargetPlayers() {}

function renderTargetPlayersList() {
    const list = document.getElementById('targetPlayersList');
    // 只显示有广告模块的播放器
    const adPlayers = allPlayersList.filter(p => p.has_ad_module);
    
    if (adPlayers.length === 0) {
        list.innerHTML = '<div style="color:var(--text-muted);padding:8px;">暂无支持广告投放的播放器<br><span style="font-size:11px;">请先为播放器开通广告模块</span></div>';
        return;
    }
    
    let html = '';
    adPlayers.forEach(p => {
        html += `<label style="display:flex;align-items:center;gap:8px;padding:6px;cursor:pointer;">
            <input type="checkbox" name="targetPlayers" value="${p.id}" ${p.id == currentPlayerId ? 'checked' : ''}>
            <span style="font-size:13px;">${p.name}</span>
            <span style="font-size:11px;color:#10b981;background:#f0fdf4;padding:1px 6px;border-radius:4px;">已开通</span>
        </label>`;
    });
    list.innerHTML = html;
}

// 颜色选择
document.querySelectorAll('.color-dot').forEach(dot => {
    dot.addEventListener('click', function() {
        document.querySelectorAll('.color-dot').forEach(d => d.style.borderColor = 'transparent');
        this.style.borderColor = '#1e293b';
        document.getElementById('playerColor').value = this.dataset.color;
    });
});

// 退出登录
function logout() {
    fetch(API + '/auth/logout', { method: 'POST', headers: authHeaders() }).catch(() => {});
    localStorage.clear();
    location.href = '/login';
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
    initNav();
    if (checkLogin()) {
        loadProfile();
        loadSettings();
        loadSeriesList();
        loadPlayersForBind();
    }
});

// ========== 视频管理 ==========
let currentSeriesId = null;
let allPlayers = [];

// 加载播放器列表（用于绑定）
async function loadPlayersForBind() {
    try {
        const res = await fetch(API + '/user/players-list', { headers: authHeaders() });
        const data = await res.json();
        allPlayers = data.data || [];
        const select = document.getElementById('bindPlayerSelect');
        select.innerHTML = '<option value="">选择播放器...</option>';
        allPlayers.forEach(p => {
            select.innerHTML += '<option value="' + p.id + '">' + escapeHtml(p.name) + '（' + p.player_code + '）</option>';
        });
    } catch (e) {
        console.error('加载播放器失败:', e);
    }
}

// 加载剧集列表
async function loadSeriesList() {
    try {
        const res = await fetch(API + '/user/series', { headers: authHeaders() });
        const data = await res.json();
        const list = data.data || [];
        const container = document.getElementById('seriesList');
        
        if (list.length === 0) {
            container.innerHTML = '<div style="text-align:center;color:var(--text-secondary);padding:30px;">'
                + '<i class="fas fa-film" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px;"></i>'
                + '还没有剧集，点击上方"新建剧集"开始</div>';
            return;
        }
        
        let html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;">';
        list.forEach(s => {
            html += '<div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:14px;cursor:pointer;" onclick="openSeries(' + s.id + ')">'
                + '<div style="display:flex;align-items:center;gap:12px;">'
                + (s.cover ? '<img src="' + escapeHtml(s.cover) + '" style="width:50px;height:70px;border-radius:4px;object-fit:cover;">' 
                    : '<div style="width:50px;height:70px;border-radius:4px;background:var(--border);display:flex;align-items:center;justify-content:center;"><i class="fas fa-film" style="color:var(--text-secondary);"></i></div>')
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escapeHtml(s.title) + '</div>'
                + '<div style="font-size:12px;color:var(--text-secondary);margin-top:4px;">' + (s.videos_count || 0) + '集' + (s.is_ending ? ' · 已完结' : ' · 连载中') + '</div>'
                + '</div>'
                + '<div style="display:flex;gap:4px;">'
                + '<button class="btn-outline" style="padding:4px 8px;font-size:11px;" onclick="event.stopPropagation();editSeries(' + s.id + ')"><i class="fas fa-edit"></i></button>'
                + '<button class="btn-outline" style="padding:4px 8px;font-size:11px;color:var(--danger);" onclick="event.stopPropagation();deleteSeries(' + s.id + ')"><i class="fas fa-trash"></i></button>'
                + '</div>'
                + '</div></div>';
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (e) {
        console.error('加载剧集失败:', e);
        document.getElementById('seriesList').innerHTML = '<div style="text-align:center;color:var(--danger);padding:30px;">加载失败</div>';
    }
}

// 打开剧集（查看视频列表）
async function openSeries(id) {
    currentSeriesId = id;
    document.getElementById('videoListCard').style.display = 'block';
    document.getElementById('videoListCard').scrollIntoView({ behavior: 'smooth' });
    
    try {
        const res = await fetch(API + '/user/series/' + id, { headers: authHeaders() });
        const data = await res.json();
        const series = data.data;
        document.getElementById('currentSeriesName').textContent = series.title;
        
        const videos = series.videos || [];
        const container = document.getElementById('videoList');
        
        if (videos.length === 0) {
            container.innerHTML = '<div style="text-align:center;color:var(--text-secondary);padding:30px;">还没有视频，点击"添加视频"开始</div>';
            return;
        }
        
        let html = '<div style="display:flex;flex-direction:column;gap:6px;">';
        videos.forEach(v => {
            html += '<div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg);border:1px solid var(--border);border-radius:6px;">'
                + '<div style="width:36px;height:36px;border-radius:6px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">' + (v.episode_number || '-') + '</div>'
                + '<div style="flex:1;min-width:0;">'
                + '<div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escapeHtml(v.title) + '</div>'
                + '<div style="font-size:11px;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escapeHtml(v.url || '') + '</div>'
                + '</div>'
                + '<div style="display:flex;gap:4px;">'
                + '<button class="btn-outline" style="padding:4px 8px;font-size:11px;" onclick="editVideo(' + v.id + ')"><i class="fas fa-edit"></i></button>'
                + '<button class="btn-outline" style="padding:4px 8px;font-size:11px;color:var(--danger);" onclick="deleteVideo(' + v.id + ')"><i class="fas fa-trash"></i></button>'
                + '</div></div>';
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (e) {
        console.error('加载视频列表失败:', e);
    }
}

function backToSeriesList() {
    currentSeriesId = null;
    document.getElementById('videoListCard').style.display = 'none';
}

// 保存剧集
async function saveSeries() {
    const id = document.getElementById('editSeriesId').value;
    const title = document.getElementById('seriesTitle').value.trim();
    if (!title) { toast('请输入剧名', true); return; }
    
    const body = {
        title: title,
        cover: document.getElementById('seriesCover').value.trim(),
        description: document.getElementById('seriesDesc').value.trim(),
    };
    
    try {
        const url = id ? (API + '/user/series/' + id) : (API + '/user/series');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: authHeaders(), body: JSON.stringify(body) });
        const data = await res.json();
        if (data.message) toast(data.message);
        closeModal('seriesModal');
        document.getElementById('seriesTitle').value = '';
        document.getElementById('seriesCover').value = '';
        document.getElementById('seriesDesc').value = '';
        document.getElementById('editSeriesId').value = '';
        loadSeriesList();
    } catch (e) {
        toast('操作失败', true);
    }
}

// 编辑剧集
async function editSeries(id) {
    try {
        const res = await fetch(API + '/user/series/' + id, { headers: authHeaders() });
        const data = await res.json();
        const s = data.data;
        document.getElementById('editSeriesId').value = s.id;
        document.getElementById('seriesTitle').value = s.title || '';
        document.getElementById('seriesCover').value = s.cover || '';
        document.getElementById('seriesDesc').value = s.description || '';
        document.getElementById('seriesModalTitle').textContent = '编辑剧集';
        openModal('seriesModal');
    } catch (e) {
        toast('获取信息失败', true);
    }
}

// 删除剧集
async function deleteSeries(id) {
    if (!confirm('确定删除此剧集？（不会删除已添加的视频）')) return;
    try {
        await fetch(API + '/user/series/' + id, { method: 'DELETE', headers: authHeaders() });
        toast('删除成功');
        loadSeriesList();
    } catch (e) {
        toast('删除失败', true);
    }
}

// 解析视频链接
async function parseVideoUrl() {
    const url = document.getElementById('videoUrl').value.trim();
    if (!url) {
        toast('请输入视频链接', true);
        return;
    }

    // 检查是否是直接的视频链接
    if (url.match(/\.(mp4|m3u8|flv|avi|wmv)(\?|$)/i)) {
        toast('这已经是视频链接，无需解析');
        return;
    }

    const parseBtn = document.getElementById('parseBtn');
    const parseResult = document.getElementById('parseResult');
    const parseStatus = document.getElementById('parseStatus');
    const parseInfo = document.getElementById('parseInfo');

    // 显示解析状态
    parseResult.style.display = 'block';
    parseStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 正在解析...';
    parseInfo.textContent = '';
    parseBtn.disabled = true;

    try {
        const res = await fetch('/api/video/parse', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        });

        const data = await res.json();

        if (data.success && data.data) {
            const video = data.data;
            parseStatus.innerHTML = '<i class="fas fa-check-circle" style="color:var(--success)"></i> 解析成功';
            
            // 显示解析结果
            let info = [];
            if (video.title) info.push('标题: ' + video.title);
            if (video.duration) info.push('时长: ' + formatDuration(video.duration));
            if (video.uploader) info.push('作者: ' + video.uploader);
            parseInfo.textContent = info.join(' | ');

            // 自动填充标题（如果为空）
            const titleInput = document.getElementById('videoTitle');
            if (!titleInput.value.trim() && video.title) {
                titleInput.value = video.title;
            }

            // 自动填充封面（如果为空）
            const coverInput = document.getElementById('videoCover');
            if (!coverInput.value.trim() && video.thumbnail) {
                coverInput.value = video.thumbnail;
            }

            // 替换URL为真实地址
            if (video.formats && video.formats.length > 0) {
                // 选择最佳格式（优先mp4，然后是最高质量）
                const bestFormat = video.formats.find(f => f.ext === 'mp4') || video.formats[0];
                document.getElementById('videoUrl').value = bestFormat.url;
                toast('解析成功，已获取真实播放地址');
            } else if (video.url) {
                document.getElementById('videoUrl').value = video.url;
                toast('解析成功，已获取真实播放地址');
            }
        } else {
            parseStatus.innerHTML = '<i class="fas fa-exclamation-circle" style="color:var(--error)"></i> 解析失败';
            parseInfo.textContent = data.message || '无法解析该链接';
            toast('解析失败: ' + (data.message || '未知错误'), true);
        }
    } catch (e) {
        parseStatus.innerHTML = '<i class="fas fa-exclamation-circle" style="color:var(--error)"></i> 解析失败';
        parseInfo.textContent = '网络错误或服务不可用';
        toast('解析失败: ' + e.message, true);
    } finally {
        parseBtn.disabled = false;
    }
}

// 格式化时长
function formatDuration(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = Math.floor(seconds % 60);
    if (h > 0) return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    return m + ':' + String(s).padStart(2, '0');
}

// 保存视频
async function saveVideo() {
    if (!currentSeriesId) { toast('请先选择剧集', true); return; }
    const id = document.getElementById('editVideoId').value;
    const title = document.getElementById('videoTitle').value.trim();
    const url = document.getElementById('videoUrl').value.trim();
    if (!title) { toast('请输入视频标题', true); return; }
    if (!url) { toast('请输入视频URL', true); return; }
    
    const body = {
        series_id: currentSeriesId,
        episode_number: parseInt(document.getElementById('videoEpisode').value) || 1,
        title: title,
        url: url,
        cover: document.getElementById('videoCover').value.trim(),
    };
    
    try {
        const reqUrl = id ? (API + '/user/videos/' + id) : (API + '/user/videos');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(reqUrl, { method, headers: authHeaders(), body: JSON.stringify(body) });
        const data = await res.json();
        if (data.message) toast(data.message);
        closeModal('videoModal');
        document.getElementById('videoTitle').value = '';
        document.getElementById('videoEpisode').value = '';
        document.getElementById('videoUrl').value = '';
        document.getElementById('videoCover').value = '';
        document.getElementById('editVideoId').value = '';
        openSeries(currentSeriesId);
    } catch (e) {
        toast('操作失败', true);
    }
}

// 编辑视频
async function editVideo(id) {
    // 简化处理，直接用当前列表数据
    toast('编辑功能开发中');
}

// 删除视频
async function deleteVideo(id) {
    if (!confirm('确定删除此视频？')) return;
    try {
        await fetch(API + '/user/videos/' + id, { method: 'DELETE', headers: authHeaders() });
        toast('删除成功');
        if (currentSeriesId) openSeries(currentSeriesId);
    } catch (e) {
        toast('删除失败', true);
    }
}

// 批量导入
async function batchImport() {
    const title = document.getElementById('importTitle').value.trim();
    const raw = document.getElementById('importData').value.trim();
    if (!title) { toast('请输入剧名', true); return; }
    if (!raw) { toast('请粘贴视频列表', true); return; }
    
    const statusEl = document.getElementById('importStatus');
    statusEl.textContent = '解析中...';
    statusEl.style.color = 'var(--text-secondary)';
    
    // 解析格式：每行 集名$url（$分隔）
    const lines = raw.split('\n').map(line => line.trim()).filter(line => line.length > 0);
    const parsed = [];
    
    for (const line of lines) {
        let name = '', url = '';
        if (line.includes('$')) {
            const parts = line.split('$');
            name = parts[0].trim();
            url = parts.slice(1).join('$').trim(); // URL中可能有$号
        } else if (line.includes('http')) {
            // 纯URL，自动编号
            url = line.trim();
            name = '';
        } else {
            continue; // 跳过无效行
        }
        if (url) {
            // 去掉URL后面的其他内容（如线路信息）
            if (url.includes('http')) {
                url = url.substring(url.indexOf('http'));
            }
            // 截断到URL结束（空格或特殊字符）
            url = url.split(/\s/)[0];
            parsed.push({ name, url });
        }
    }
    
    if (parsed.length === 0) {
        statusEl.textContent = '未解析到有效视频';
        statusEl.style.color = 'var(--danger)';
        return;
    }
    
    statusEl.textContent = '正在导入 ' + parsed.length + ' 个视频...';
    
    try {
        const res = await fetch(API + '/user/series/import', {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ title: title, lines: parsed })
        });
        const data = await res.json();
        
        if (res.ok) {
            statusEl.textContent = data.message;
            statusEl.style.color = 'var(--success)';
            toast(data.message);
            document.getElementById('importTitle').value = '';
            document.getElementById('importData').value = '';
            loadSeriesList();
        } else {
            statusEl.textContent = data.message || '导入失败';
            statusEl.style.color = 'var(--danger)';
            toast(data.message || '导入失败', true);
        }
    } catch (e) {
        statusEl.textContent = '请求失败';
        statusEl.style.color = 'var(--danger)';
        toast('导入失败', true);
    }
}

// 绑定剧集到播放器
async function bindSeriesToPlayer() {
    if (!currentSeriesId) { toast('请先选择剧集', true); return; }
    const playerId = document.getElementById('bindPlayerSelect').value;
    if (!playerId) { toast('请选择播放器', true); return; }
    
    try {
        const res = await fetch(API + '/user/series/' + currentSeriesId + '/bind-player', {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ player_id: playerId })
        });
        const data = await res.json();
        toast(data.message || '绑定成功');
    } catch (e) {
        toast('绑定失败', true);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

</body>
</html>
