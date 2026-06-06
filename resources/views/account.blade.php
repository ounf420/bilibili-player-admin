<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账号中心 - DPlayer 广告系统</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #00a1d6;
            --primary-dark: #0088b9;
            --primary-light: #e8f7fc;
            --accent: #fb7299;
            --bg: #f5f7fa;
            --bg-card: #ffffff;
            --text: #1a1a2e;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
            --radius: 12px;
            --radius-lg: 16px;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        /* NAVBAR */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .navbar-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            height: 64px; display: flex; align-items: center; justify-content: space-between;
        }
        .navbar-brand {
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: 18px; color: var(--text);
        }
        .navbar-brand .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
        }
        .navbar-links { display: flex; align-items: center; gap: 8px; }
        .navbar-links a {
            padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500;
            color: var(--text-secondary); transition: all 0.2s;
        }
        .navbar-links a:hover { color: var(--primary); background: var(--primary-light); }
        .navbar-links a.active { color: var(--primary); background: var(--primary-light); }
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text);
            cursor: pointer;
            padding: 8px;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600;
            cursor: pointer; border: none; transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; box-shadow: 0 2px 8px rgba(0,161,214,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,161,214,0.4); }
        .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        /* LAYOUT */
        .page-container {
            max-width: 1200px; margin: 0 auto; padding: 88px 24px 40px;
            display: grid; grid-template-columns: 280px 1fr; gap: 24px;
        }

        /* SIDEBAR */
        .sidebar {
            background: var(--bg-card); border-radius: var(--radius-lg);
            padding: 24px; box-shadow: var(--shadow);
            position: sticky; top: 88px; height: fit-content;
        }
        .user-profile {
            display: flex; align-items: center; gap: 14px;
            padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border);
        }
        .user-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 20px; font-weight: 700;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-weight: 600; font-size: 16px; }
        .user-id { font-size: 12px; color: var(--text-muted); }
        .user-badge {
            display: inline-block; padding: 2px 8px; border-radius: 6px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: #fff; font-size: 11px; font-weight: 600; margin-top: 4px;
        }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 10px; font-size: 14px; font-weight: 500;
            color: var(--text-secondary); transition: all 0.2s;
        }
        .sidebar-menu li a:hover { color: var(--primary); background: var(--primary-light); }
        .sidebar-menu li a.active { color: var(--primary); background: var(--primary-light); font-weight: 600; }
        .sidebar-menu li a i { width: 18px; text-align: center; }

        /* MAIN CONTENT */
        .main-content { min-height: 400px; }
        .loading {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            height: 300px; color: var(--text-muted);
        }
        .loading i { font-size: 32px; margin-bottom: 12px; }

        /* SECTION CARDS */
        .section-card {
            background: var(--bg-card); border-radius: var(--radius-lg);
            padding: 24px; box-shadow: var(--shadow); margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px; font-weight: 700; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title i { color: var(--primary); }

        /* SECURITY ITEMS */
        .security-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px; border-radius: var(--radius); border: 1px solid var(--border);
            margin-bottom: 12px; transition: all 0.2s;
        }
        .security-item:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
        .security-item-left { display: flex; align-items: center; gap: 14px; }
        .security-item-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .security-item-info h4 { font-size: 14px; font-weight: 600; }
        .security-item-info p { font-size: 12px; color: var(--text-muted); }

        /* PROFILE FORM */
        .profile-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
        }
        .profile-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px; border-radius: var(--radius); border: 1px solid var(--border);
        }
        .profile-item-label { font-size: 13px; color: var(--text-muted); }
        .profile-item-value { font-size: 14px; font-weight: 500; }
        .profile-item-action { font-size: 13px; color: var(--primary); cursor: pointer; }

        /* SOCIAL ITEMS */
        .social-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px; border-radius: var(--radius); border: 1px solid var(--border);
            margin-bottom: 12px;
        }
        .social-item-left { display: flex; align-items: center; gap: 14px; }
        .social-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 20px;
        }
        .social-info h4 { font-size: 14px; font-weight: 600; }
        .social-info p { font-size: 12px; color: var(--text-muted); }

        /* MEMBERSHIP */
        .membership-card {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: var(--radius-lg); padding: 24px; color: #fff;
        }
        .membership-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .membership-title { font-size: 18px; font-weight: 700; }
        .membership-badge {
            padding: 4px 12px; border-radius: 20px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            font-size: 12px; font-weight: 600;
        }
        .membership-info p { font-size: 14px; opacity: 0.8; }
        .membership-expire { font-size: 12px; opacity: 0.6; margin-top: 8px; }
        .membership-features {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 20px;
        }
        .membership-feature {
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            font-size: 12px; opacity: 0.8;
        }
        .membership-feature i { font-size: 20px; }

        /* DANGER ZONE */
        .danger-zone { border-color: var(--danger); }
        .danger-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px; border-radius: var(--radius); border: 1px solid #fecaca;
            background: #fef2f2;
        }
        .danger-item-info h4 { font-size: 14px; font-weight: 600; color: var(--danger); }
        .danger-item-info p { font-size: 12px; color: var(--text-muted); }

        /* NOTICE MARQUEE */
        .notice-marquee {
            overflow: hidden; white-space: nowrap;
            padding: 8px 0; background: linear-gradient(90deg, var(--primary-light), transparent, var(--primary-light));
            border-radius: var(--radius);
        }
        .notice-marquee-content {
            display: inline-block; animation: marquee 20s linear infinite;
        }
        .notice-marquee-content .notice-item {
            display: inline-block; margin-right: 60px; font-size: 14px; color: var(--primary);
        }
        .notice-marquee-content .notice-item i { margin-right: 8px; }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* QUICK ENTRY */
        .quick-entry {
            display: flex; flex-direction: column; align-items: center;
            padding: 16px; border-radius: var(--radius); border: 1px solid var(--border);
            cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .quick-entry:hover { border-color: var(--primary); box-shadow: var(--shadow-md); transform: translateY(-2px); }

        /* MODAL */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            display: none; align-items: center; justify-content: center; z-index: 2000;
        }
        .modal-overlay.show { display: flex; }
        .modal {
            background: var(--bg-card); border-radius: var(--radius-lg);
            padding: 32px; width: 90%; max-width: 440px; box-shadow: var(--shadow-lg);
        }
        .modal h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
            border-radius: 10px; font-size: 14px; transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,161,214,0.1); }
        .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }

        /* TOAST */
        .toast {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(100px);
            padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 500;
            color: #fff; z-index: 3000; transition: transform 0.3s;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast-success { background: var(--success); }
        .toast-error { background: var(--danger); }

        /* MOBILE DRAWER */
        .mobile-drawer-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 1500; opacity: 0; visibility: hidden; transition: all 0.3s;
        }
        .mobile-drawer-overlay.show { opacity: 1; visibility: visible; }
        .mobile-drawer {
            position: fixed; top: 0; left: -300px; bottom: 0; width: 280px;
            background: var(--bg-card); z-index: 1600; transition: left 0.3s;
            overflow-y: auto; padding: 24px;
        }
        .mobile-drawer.show { left: 0; }
        .mobile-drawer-header {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border);
        }
        .mobile-drawer-header h3 { font-size: 18px; font-weight: 700; }
        .mobile-drawer-close {
            background: none; border: none; font-size: 20px; color: var(--text-muted);
            cursor: pointer; padding: 8px;
        }
        .mobile-drawer-menu { list-style: none; }
        .mobile-drawer-menu li a {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 10px; font-size: 15px; font-weight: 500;
            color: var(--text-secondary); transition: all 0.2s;
        }
        .mobile-drawer-menu li a:hover { color: var(--primary); background: var(--primary-light); }
        .mobile-drawer-menu li a.active { color: var(--primary); background: var(--primary-light); font-weight: 600; }
        .mobile-drawer-menu li a i { width: 20px; text-align: center; }

        /* PAGE HEADER (MOBILE) */
        .page-header {
            display: none;
            align-items: center; gap: 12px;
            margin-bottom: 20px;
        }
        .page-header-back {
            background: none; border: none; font-size: 18px; color: var(--text);
            cursor: pointer; padding: 8px;
        }
        .page-header h2 { font-size: 18px; font-weight: 700; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            .navbar-links { display: none; }
            .page-container {
                grid-template-columns: 1fr;
                padding: 80px 16px 24px;
            }
            .sidebar { display: none; }
            .page-header { display: flex; }
            .profile-grid { grid-template-columns: 1fr; }
            .membership-features { grid-template-columns: repeat(2, 1fr); }
            .security-item, .social-item, .profile-item {
                flex-direction: column; align-items: flex-start; gap: 12px;
            }
            .security-item > .btn, .social-item > .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">
            <div class="logo-icon"><i class="fas fa-play"></i></div>
            <span>DPlayer 广告系统</span>
        </a>
        <button class="mobile-menu-btn" onclick="toggleDrawer()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-links">
            <a href="/">首页</a>
            <a href="/player">播放器</a>
            <a href="/account" class="active">账号中心</a>
            <a href="#" id="logoutBtn" style="color:var(--danger);">退出</a>
        </div>
    </div>
</nav>

<!-- MOBILE DRAWER -->
<div class="mobile-drawer-overlay" id="drawerOverlay" onclick="toggleDrawer()"></div>
<aside class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <h3>账号中心</h3>
        <button class="mobile-drawer-close" onclick="toggleDrawer()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="user-profile">
        <div class="user-avatar" id="drawerAvatar">?</div>
        <div class="user-info">
            <div class="user-name" id="drawerName">加载中...</div>
            <div class="user-id" id="drawerId">ID: ---</div>
        </div>
    </div>
    <ul class="mobile-drawer-menu">
        <li><a href="#" data-page="dashboard" class="active" onclick="showPage('dashboard')"><i class="fas fa-home"></i> 首页</a></li>
        <li><a href="#" data-page="security" onclick="showPage('security')"><i class="fas fa-shield-alt"></i> 账号安全</a></li>
        <li><a href="#" data-page="profile" onclick="showPage('profile')"><i class="fas fa-user"></i> 账号信息</a></li>
        <li><a href="#" data-page="social" onclick="showPage('social')"><i class="fas fa-link"></i> 第三方绑定</a></li>
        <li><a href="#" data-page="privacy" onclick="showPage('privacy')"><i class="fas fa-eye-slash"></i> 隐私设置</a></li>
        <li><a href="#" data-page="danger" onclick="showPage('danger')"><i class="fas fa-exclamation-triangle"></i> 账号注销</a></li>
        <li><a href="#" style="color:var(--danger);" onclick="logout()"><i class="fas fa-sign-out-alt"></i> 退出登录</a></li>
    </ul>
</aside>

<!-- MAIN -->
<div class="page-container">
    <!-- SIDEBAR (DESKTOP) -->
    <aside class="sidebar">
        <div class="user-profile">
            <div class="user-avatar" id="userAvatar">?</div>
            <div class="user-info">
                <div class="user-name" id="userName">加载中...</div>
                <div class="user-id" id="userId">ID: ---</div>
                <div class="user-badge" id="userBadge" style="display:none;">普通用户</div>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#security" class="active"><i class="fas fa-shield-alt"></i> 账号安全</a></li>
            <li><a href="#profile"><i class="fas fa-user"></i> 账号信息</a></li>
            <li><a href="#membership"><i class="fas fa-crown"></i> 会员订阅</a></li>
            <li><a href="#social"><i class="fas fa-link"></i> 第三方绑定</a></li>
            <li><a href="#privacy"><i class="fas fa-eye-slash"></i> 隐私设置</a></li>
            <li><a href="#danger"><i class="fas fa-exclamation-triangle"></i> 账号注销</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="main-content" id="mainContent">
        <!-- PAGE HEADER (MOBILE) -->
        <div class="page-header" id="pageHeader">
            <button class="page-header-back" onclick="toggleDrawer()">
                <i class="fas fa-bars"></i>
            </button>
            <h2 id="pageTitle">账号安全</h2>
        </div>

        <!-- PAGES CONTAINER -->
        <div id="pagesContainer">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>加载中...</p>
            </div>
        </div>
    </main>
</div>

<!-- MODALS -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <h3>修改密码</h3>
        <div class="form-group">
            <label class="form-label">原密码</label>
            <input type="password" class="form-input" id="oldPassword" placeholder="请输入原密码">
        </div>
        <div class="form-group">
            <label class="form-label">新密码</label>
            <input type="password" class="form-input" id="newPassword" placeholder="至少6位">
        </div>
        <div class="form-group">
            <label class="form-label">确认密码</label>
            <input type="password" class="form-input" id="confirmNewPassword" placeholder="再次输入新密码">
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('passwordModal')">取消</button>
            <button class="btn btn-primary" id="changePasswordBtn">确认修改</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="nicknameModal">
    <div class="modal">
        <h3>修改昵称</h3>
        <div class="form-group">
            <label class="form-label">新昵称</label>
            <input type="text" class="form-input" id="newNickname" placeholder="请输入新昵称">
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('nicknameModal')">取消</button>
            <button class="btn btn-primary" id="saveNicknameBtn">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="genderModal">
    <div class="modal">
        <h3>修改性别</h3>
        <div class="form-group">
            <label class="form-label">选择性别</label>
            <select class="form-input" id="newGender">
                <option value="0">未知</option>
                <option value="1">男</option>
                <option value="2">女</option>
            </select>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('genderModal')">取消</button>
            <button class="btn btn-primary" id="saveGenderBtn">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="birthdayModal">
    <div class="modal">
        <h3>修改生日</h3>
        <div class="form-group">
            <label class="form-label">选择生日</label>
            <input type="date" class="form-input" id="newBirthday">
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('birthdayModal')">取消</button>
            <button class="btn btn-primary" id="saveBirthdayBtn">保存</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="phoneModal">
    <div class="modal">
        <h3>绑定手机号</h3>
        <div class="form-group">
            <label class="form-label">手机号</label>
            <input type="tel" class="form-input" id="newPhone" placeholder="请输入手机号" maxlength="11">
        </div>
        <div class="form-group">
            <label class="form-label">验证码</label>
            <div style="display:flex;gap:10px;">
                <input type="text" class="form-input" id="phoneCode" placeholder="6位验证码" maxlength="6" style="flex:1;">
                <button class="btn btn-outline btn-sm" id="sendPhoneCodeBtn" onclick="sendVerifyCode('phone')">发送验证码</button>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('phoneModal')">取消</button>
            <button class="btn btn-primary" id="savePhoneBtn">确认绑定</button>
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
                <button class="btn btn-outline btn-sm" id="sendEmailCodeBtn" onclick="sendVerifyCode('email')">发送验证码</button>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('emailModal')">取消</button>
            <button class="btn btn-primary" id="saveEmailBtn">确认绑定</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
    const API_BASE = '/api';
    let currentUser = null;
    let currentPage = 'dashboard';
    
    function getToken() { return localStorage.getItem('token'); }
    
    function toggleDrawer() {
        const overlay = document.getElementById('drawerOverlay');
        const drawer = document.getElementById('mobileDrawer');
        overlay.classList.toggle('show');
        drawer.classList.toggle('show');
    }
    
    function showPage(page) {
        currentPage = page;
        
        // 更新drawer菜单状态
        document.querySelectorAll('.mobile-drawer-menu a[data-page]').forEach(a => {
            a.classList.toggle('active', a.dataset.page === page);
        });
        
        // 更新sidebar菜单状态
        document.querySelectorAll('.sidebar-menu a').forEach(a => {
            a.classList.toggle('active', a.getAttribute('href') === '#' + page);
        });
        
        // 更新页面标题
        const titles = {
            security: '账号安全', profile: '账号信息', membership: '会员订阅', social: '第三方绑定',
            privacy: '隐私设置', danger: '账号注销'
        };
        document.getElementById('pageTitle').textContent = titles[page] || page;
        
        // 渲染页面内容
        renderPage(page);
        
        // 关闭drawer
        const overlay = document.getElementById('drawerOverlay');
        const drawer = document.getElementById('mobileDrawer');
        overlay.classList.remove('show');
        drawer.classList.remove('show');
    }
    
    function renderPage(page) {
        const container = document.getElementById('pagesContainer');
        
        switch(page) {
            case 'dashboard':
                container.innerHTML = renderDashboardPage();
                loadDashboardNotices();
                loadDashboardStats();
                break;
            case 'security':
                container.innerHTML = renderSecurityPage();
                break;
            case 'profile':
                container.innerHTML = renderProfilePage();
                break;
            case 'social':
                container.innerHTML = '<div class="section-card"><div class="section-title"><i class="fas fa-link"></i> 第三方绑定</div><div id="socialBindings">加载中...</div></div>';
                loadSocialBindings();
                break;
            case 'privacy':
                container.innerHTML = renderPrivacyPage();
                break;
            case 'danger':
                container.innerHTML = renderDangerPage();
                break;
            case 'membership':
                container.innerHTML = renderMembershipPage();
                break;
        }
    }
    
    function renderDashboardPage() {
        return `
            <!-- 公告滚动栏 -->
            <div class="section-card" id="noticeMarquee" style="display:none;">
                <div class="notice-marquee">
                    <div class="notice-marquee-content" id="noticeMarqueeContent"></div>
                </div>
            </div>
            
            <!-- 用户卡片 -->
            <div class="section-card">
                <div style="display:flex;align-items:center;gap:20px;">
                    <div class="user-avatar" style="width:72px;height:72px;font-size:28px;">${currentUser.nickname ? currentUser.nickname[0] : '?'}</div>
                    <div style="flex:1;">
                        <div style="font-size:20px;font-weight:700;">${currentUser.nickname || currentUser.username}</div>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">ID: ${currentUser.id}</div>
                        <div style="margin-top:8px;">
                            ${
                                ? '<span style="background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">👑 ' + 
                                : '<span style="background:var(--border);color:var(--text-muted);padding:4px 12px;border-radius:20px;font-size:12px;">普通用户</span>'
                            }
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="showPage('profile')">编辑资料</button>
                </div>
            </div>
            
            <!-- 数据概览 -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-chart-bar"></i> 数据概览</div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;" id="dashboardStats">
                    <div style="text-align:center;padding:16px;background:var(--bg);border-radius:var(--radius);">
                        <div style="font-size:24px;font-weight:700;color:var(--primary);" id="statHistory">-</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">观看记录</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--bg);border-radius:var(--radius);">
                        <div style="font-size:24px;font-weight:700;color:var(--accent);" id="statFavorites">-</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">收藏数</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--bg);border-radius:var(--radius);">
                        <div style="font-size:24px;font-weight:700;color:var(--success);" id="statComments">-</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">评论数</div>
                    </div>
                </div>
            </div>
            
            <!-- 快捷入口 -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-th-large"></i> 快捷入口</div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                    <div class="quick-entry" onclick="showPage('security')">
                        <div style="width:44px;height:44px;border-radius:12px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-shield-alt"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">账号安全</div>
                    </div>
                    <div class="quick-entry" onclick="showPage('social')">
                        <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-link"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">第三方绑定</div>
                    </div>
                    <div class="quick-entry" onclick="showPage('profile')">
                        <div style="width:44px;height:44px;border-radius:12px;background:#e0e7ff;color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-user"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">账号信息</div>
                    </div>
                    <div class="quick-entry" onclick="showPage('privacy')">
                        <div style="width:44px;height:44px;border-radius:12px;background:#fce7f3;color:#ec4899;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-eye-slash"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">隐私设置</div>
                    </div>
                    <div class="quick-entry" onclick="showPage('membership')">
                        <div style="width:44px;height:44px;border-radius:12px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-crown"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">会员中心</div>
                    </div>
                    <div class="quick-entry" onclick="exportData()">
                        <div style="width:44px;height:44px;border-radius:12px;background:#f3e8ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:18px;"><i class="fas fa-file-export"></i></div>
                        <div style="font-size:13px;font-weight:500;margin-top:8px;">数据导出</div>
                    </div>
                </div>
            </div>
            
            <!-- 最近活动 -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-clock"></i> 最近活动</div>
                <div style="color:var(--text-muted);font-size:14px;text-align:center;padding:20px;">
                    暂无活动记录
                </div>
            </div>
        `;
    }
    
    async function loadDashboardStats() {
        try {
            const [histRes, favRes] = await Promise.all([
                api('/history').catch(() => ({ data: [] })),
                api('/favorites').catch(() => ({ data: [] }))
            ]);
            const histCount = histRes.data ? (Array.isArray(histRes.data) ? histRes.data.length : 0) : 0;
            const favCount = favRes.data ? (Array.isArray(favRes.data) ? favRes.data.length : 0) : 0;
            document.getElementById('statHistory').textContent = histCount;
            document.getElementById('statFavorites').textContent = favCount;
            document.getElementById('statComments').textContent = '-';
        } catch (err) {
            console.error('加载统计数据失败', err);
        }
    }

    async function loadDashboardNotices() {
        try {
            const data = await api('/notices/marquee');
            if (data.success && data.data.length > 0) {
                const container = document.getElementById('noticeMarquee');
                const content = document.getElementById('noticeMarqueeContent');
                container.style.display = 'block';
                content.innerHTML = data.data.map(notice => 
                    `<span class="notice-item"><i class="${notice.icon || 'fas fa-bell'}"></i> ${notice.title}</span>`
                ).join('');
            }
        } catch (err) {
            console.error('加载公告失败', err);
        }
    }
    
    function renderSecurityPage() {
        return `
            <div class="section-card">
                <div class="section-title"><i class="fas fa-shield-alt"></i> 账号安全</div>
                
                <div class="security-item">
                    <div class="security-item-left">
                        <div class="security-item-icon" style="background:#dbeafe;color:#2563eb;"><i class="fas fa-lock"></i></div>
                        <div class="security-item-info">
                            <h4>登录密码</h4>
                            <p>已设置，建议定期更换密码</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="document.getElementById('passwordModal').classList.add('show')">修改密码</button>
                </div>
                
                <div class="security-item">
                    <div class="security-item-left">
                        <div class="security-item-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-mobile-alt"></i></div>
                        <div class="security-item-info">
                            <h4>绑定手机</h4>
                            <p>${currentUser.phone ? '已绑定：' + currentUser.phone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2') : '未绑定'}</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="document.getElementById('phoneModal').classList.add('show')">${currentUser.phone ? '更换手机' : '绑定手机'}</button>
                </div>
                
                <div class="security-item">
                    <div class="security-item-left">
                        <div class="security-item-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-envelope"></i></div>
                        <div class="security-item-info">
                            <h4>绑定邮箱</h4>
                            <p>${currentUser.email ? '已绑定：' + currentUser.email.replace(/(.{2}).*(@.*)/, '$1***$2') : '未绑定'}</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="document.getElementById('emailModal').classList.add('show')">${currentUser.email ? '更换邮箱' : '绑定邮箱'}</button>
                </div>
            </div>
        `;
    }
    
    function renderProfilePage() {
        return `
            <div class="section-card">
                <div class="section-title"><i class="fas fa-user"></i> 账号信息</div>
                <div class="profile-grid">
                    <div class="profile-item">
                        <div>
                            <div class="profile-item-label">昵称</div>
                            <div class="profile-item-value">${currentUser.nickname || '未设置'}</div>
                        </div>
                        <div class="profile-item-action" onclick="document.getElementById('nicknameModal').classList.add('show')">修改</div>
                    </div>
                    <div class="profile-item">
                        <div>
                            <div class="profile-item-label">用户名</div>
                            <div class="profile-item-value">${currentUser.username}</div>
                        </div>
                    </div>
                    <div class="profile-item">
                        <div>
                            <div class="profile-item-label">性别</div>
                            <div class="profile-item-value">${currentUser.gender_name || '未设置'}</div>
                        </div>
                        <div class="profile-item-action" onclick="document.getElementById('genderModal').classList.add('show')">修改</div>
                    </div>
                    <div class="profile-item">
                        <div>
                            <div class="profile-item-label">生日</div>
                            <div class="profile-item-value">${currentUser.birthday || '未设置'}</div>
                        </div>
                        <div class="profile-item-action" onclick="document.getElementById('birthdayModal').classList.add('show')">修改</div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function renderMembershipPage() {
        // 会员订阅功能已移除
        return `
            <div class="section-card">
                <div class="section-title"><i class="fas fa-crown"></i> 会员订阅</div>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#f97316);display:flex;align-items:center;justify-content:center;font-size:28px;">👑</div>
                    <div>
                        <div style="font-size:18px;font-weight:700;">${
                        <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">
                            ${
                        </div>
                    </div>
                </div>
                    <i class="fas fa-crown"></i> ${
                </a>
            </div>
        `;
    }
    
    function renderPrivacyPage() {
        return `
            <div class="section-card">
                <div class="section-title"><i class="fas fa-eye-slash"></i> 隐私设置</div>
                <div class="security-item">
                    <div class="security-item-left">
                        <div class="security-item-icon" style="background:#e0e7ff;color:#6366f1;"><i class="fas fa-eye"></i></div>
                        <div class="security-item-info">
                            <h4>个人资料可见性</h4>
                            <p>控制其他用户是否可以看到你的个人资料</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm">公开</button>
                </div>
                <div class="security-item">
                    <div class="security-item-left">
                        <div class="security-item-icon" style="background:#fce7f3;color:#ec4899;"><i class="fas fa-history"></i></div>
                        <div class="security-item-info">
                            <h4>播放历史</h4>
                            <p>是否保存你的播放历史记录</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-sm">开启</button>
                </div>
            </div>
        `;
    }
    
    function renderDangerPage() {
        return `
            <div class="section-card danger-zone">
                <div class="section-title"><i class="fas fa-exclamation-triangle"></i> 危险操作</div>
                <div class="danger-item">
                    <div class="danger-item-info">
                        <h4>注销账号</h4>
                        <p>永久删除账号及所有数据，此操作不可恢复</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="deleteAccount()">申请注销</button>
                </div>
            </div>
            <div class="section-card">
                <div class="section-title"><i class="fas fa-download"></i> 数据导出</div>
                <p style="font-size:14px;color:var(--text-secondary);margin-bottom:16px;">导出你的个人数据，包括账号信息、播放历史、收藏列表等。</p>
                <button class="btn btn-outline" onclick="exportData()"><i class="fas fa-file-export"></i> 导出我的数据</button>
            </div>
        `;
    }
    
    // API调用
    async function api(path, options = {}) {
        const headers = { 'Content-Type': 'application/json' };
        const token = getToken();
        if (token) headers['Authorization'] = 'Bearer ' + token;
        const res = await fetch(API_BASE + path, { ...options, headers });
        return res.json();
    }
    
    // 检查登录状态
    async function checkAuth() {
        if (!getToken()) {
            window.location.href = '/login';
            return;
        }
        
        try {
            const data = await api('/auth/me');
            if (data.success) {
                currentUser = data.data.user;
                renderUserInfo();
                renderPage('dashboard');
            } else {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        } catch (err) {
            console.error(err);
            showToast('加载失败', 'error');
        }
    }
    
    // 渲染用户信息
    function renderUserInfo() {
        // 桌面端
        document.getElementById('userAvatar').textContent = currentUser.nickname ? currentUser.nickname[0] : '?';
        document.getElementById('userName').textContent = currentUser.nickname || currentUser.username;
        document.getElementById('userId').textContent = 'ID: ' + currentUser.id;
        
        // 移动端drawer
        document.getElementById('drawerAvatar').textContent = currentUser.nickname ? currentUser.nickname[0] : '?';
        document.getElementById('drawerName').textContent = currentUser.nickname || currentUser.username;
        document.getElementById('drawerId').textContent = 'ID: ' + currentUser.id;
        
        const badge = document.getElementById('userBadge');
        if (
            badge.textContent = '👑 ' + 
            badge.style.display = 'inline-block';
        }
    }
    
    // 第三方绑定
    const PLATFORM_COLORS = {
        qq: '#12b7f5', wx: '#07c160', alipay: '#1677ff', sina: '#e6162d',
        baidu: '#2319dc', douyin: '#000', huawei: '#cf0a2c', xiaomi: '#ff6900',
        microsoft: '#00a4ef', feishu: '#3370ff', dingtalk: '#0089ff', gitee: '#c71d23', github: '#333'
    };
    const PLATFORM_ICONS = {
        qq: 'fab fa-qq', wx: 'fab fa-weixin', alipay: 'fab fa-alipay', sina: 'fab fa-weibo',
        baidu: 'fab fa-baidu', douyin: 'fab fa-tiktok', huawei: 'fas fa-mobile-alt', xiaomi: 'fas fa-mobile',
        microsoft: 'fab fa-microsoft', feishu: 'fas fa-paper-plane', dingtalk: 'fab fa-dingtalk', gitee: 'fab fa-git-alt', github: 'fab fa-github'
    };

    async function loadSocialBindings() {
        const container = document.getElementById('socialBindings');
        if (!container) return;
        
        try {
            const data = await api('/socialite/bindings');
            if (data.success) {
                renderSocialBindings(data.data);
            } else {
                container.innerHTML = '<p style="color:var(--text-muted);font-size:14px;">加载失败</p>';
            }
        } catch (err) {
            container.innerHTML = '<p style="color:var(--text-muted);font-size:14px;">加载失败</p>';
        }
    }

    function renderSocialBindings(bindings) {
        const container = document.getElementById('socialBindings');
        container.innerHTML = bindings.map(item => `
            <div class="social-item">
                <div class="social-item-left">
                    <div class="social-icon" style="background:${PLATFORM_COLORS[item.platform] || '#666'}">
                        <i class="${PLATFORM_ICONS[item.platform] || 'fas fa-link'}"></i>
                    </div>
                    <div class="social-info">
                        <h4>${item.name}</h4>
                        <p>${item.bound ? '已绑定' + (item.nickname ? '：' + item.nickname : '') : '未绑定'}</p>
                    </div>
                </div>
                ${item.bound 
                    ? `<button class="btn btn-outline btn-sm" onclick="unbindSocial('${item.platform}')">解绑</button>`
                    : `<button class="btn btn-primary btn-sm" onclick="bindSocial('${item.platform}')">绑定</button>`
                }
            </div>
        `).join('');
    }

    async function bindSocial(platform) {
        try {
            const data = await api('/socialite/login?type=' + platform);
            if (data.success && data.data.url) {
                const width = 600, height = 700;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                window.open(data.data.url, 'socialite_' + platform, 
                    `width=${width},height=${height},left=${left},top=${top}`);
                
                window.addEventListener('message', async function handler(e) {
                    if (e.data && e.data.platform === platform) {
                        window.removeEventListener('message', handler);
                        if (e.data.need_bind) {
                            const bindResult = await api('/socialite/bind', {
                                method: 'POST',
                                body: JSON.stringify({ temp_key: e.data.temp_key })
                            });
                            if (bindResult.success) {
                                showToast(bindResult.message, 'success');
                                loadSocialBindings();
                            } else {
                                showToast(bindResult.message, 'error');
                            }
                        } else if (e.data.success) {
                            showToast('登录成功', 'success');
                            loadSocialBindings();
                        } else {
                            showToast(e.data.message || '操作失败', 'error');
                        }
                    }
                });
            } else {
                showToast(data.message || '获取登录地址失败', 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    }

    async function unbindSocial(platform) {
        if (!confirm('确定要解绑该账号吗？')) return;
        
        try {
            const data = await api('/socialite/unbind', {
                method: 'POST',
                body: JSON.stringify({ platform })
            });
            if (data.success) {
                showToast(data.message, 'success');
                loadSocialBindings();
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    }
    
    // 修改密码
    document.getElementById('changePasswordBtn').addEventListener('click', async () => {
        const oldPassword = document.getElementById('oldPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmNewPassword').value;
        
        if (newPassword !== confirmPassword) {
            showToast('两次密码不一致', 'error');
            return;
        }
        
        try {
            const data = await api('/auth/change-password', {
                method: 'POST',
                body: JSON.stringify({ old_password: oldPassword, new_password: newPassword, new_password_confirmation: confirmPassword })
            });
            if (data.success) {
                showToast('密码修改成功', 'success');
                closeModal('passwordModal');
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });
    
    // 修改昵称
    document.getElementById('saveNicknameBtn').addEventListener('click', async () => {
        const nickname = document.getElementById('newNickname').value;
        
        try {
            const data = await api('/auth/profile', {
                method: 'PUT',
                body: JSON.stringify({ nickname })
            });
            if (data.success) {
                currentUser.nickname = nickname;
                renderUserInfo();
                showToast('昵称修改成功', 'success');
                closeModal('nicknameModal');
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });
    
    // 修改性别
    document.getElementById('saveGenderBtn').addEventListener('click', async () => {
        const gender = document.getElementById('newGender').value;
        try {
            const data = await api('/auth/profile', {
                method: 'PUT',
                body: JSON.stringify({ gender: parseInt(gender) })
            });
            if (data.success) {
                currentUser.gender = parseInt(gender);
                currentUser.gender_name = ['未知','男','女'][parseInt(gender)];
                renderUserInfo();
                showToast('性别修改成功', 'success');
                closeModal('genderModal');
                renderPage(currentPage);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });

    // 修改生日
    document.getElementById('saveBirthdayBtn').addEventListener('click', async () => {
        const birthday = document.getElementById('newBirthday').value;
        if (!birthday) { showToast('请选择生日', 'error'); return; }
        try {
            const data = await api('/auth/profile', {
                method: 'PUT',
                body: JSON.stringify({ birthday })
            });
            if (data.success) {
                currentUser.birthday = birthday;
                renderUserInfo();
                showToast('生日修改成功', 'success');
                closeModal('birthdayModal');
                renderPage(currentPage);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });

    // 发送验证码
    async function sendVerifyCode(type) {
        const target = type === 'phone' 
            ? document.getElementById('newPhone').value 
            : document.getElementById('newEmail').value;
        if (!target) { showToast(type === 'phone' ? '请输入手机号' : '请输入邮箱', 'error'); return; }
        
        const btn = type === 'phone' ? document.getElementById('sendPhoneCodeBtn') : document.getElementById('sendEmailCodeBtn');
        btn.disabled = true;
        let countdown = 60;
        btn.textContent = countdown + 's';
        const timer = setInterval(() => {
            countdown--;
            btn.textContent = countdown + 's';
            if (countdown <= 0) { clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码'; }
        }, 1000);
        
        try {
            const data = await api('/auth/send-code', {
                method: 'POST',
                body: JSON.stringify({ target, type })
            });
            if (data.success) {
                showToast('验证码已发送', 'success');
            } else {
                showToast(data.message, 'error');
                clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码';
            }
        } catch (err) {
            showToast('发送失败', 'error');
            clearInterval(timer); btn.disabled = false; btn.textContent = '发送验证码';
        }
    }

    // 绑定手机
    document.getElementById('savePhoneBtn').addEventListener('click', async () => {
        const phone = document.getElementById('newPhone').value;
        const code = document.getElementById('phoneCode').value;
        if (!phone || !code) { showToast('请填写完整信息', 'error'); return; }
        try {
            const data = await api('/auth/bind-phone', {
                method: 'POST',
                body: JSON.stringify({ phone, code })
            });
            if (data.success) {
                currentUser.phone = phone;
                renderUserInfo();
                showToast('手机号绑定成功', 'success');
                closeModal('phoneModal');
                renderPage(currentPage);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });

    // 绑定邮箱
    document.getElementById('saveEmailBtn').addEventListener('click', async () => {
        const email = document.getElementById('newEmail').value;
        const code = document.getElementById('emailCode').value;
        if (!email || !code) { showToast('请填写完整信息', 'error'); return; }
        try {
            const data = await api('/auth/bind-email', {
                method: 'POST',
                body: JSON.stringify({ email, code })
            });
            if (data.success) {
                currentUser.email = email;
                renderUserInfo();
                showToast('邮箱绑定成功', 'success');
                closeModal('emailModal');
                renderPage(currentPage);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    });

    // 导出数据
    async function exportData() {
        try {
            const data = await api('/auth/export-data');
            if (data.success) {
                const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'my-data.json';
                a.click();
                URL.revokeObjectURL(url);
                showToast('数据导出成功', 'success');
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    }
    
    // 注销账号
    async function deleteAccount() {
        if (!confirm('确定要注销账号吗？此操作不可恢复！')) return;
        
        try {
            const data = await api('/auth/delete-account', { method: 'POST' });
            if (data.success) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                showToast('账号已注销', 'success');
                setTimeout(() => window.location.href = '/', 2000);
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('操作失败', 'error');
        }
    }
    
    // 退出登录
    function logout() {
        api('/auth/logout', { method: 'POST' });
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
    document.getElementById('logoutBtn').addEventListener('click', (e) => {
        e.preventDefault();
        logout();
    });
    
    // MODAL
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }
    
    // TOAST
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.className = `toast toast-${type} show`;
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // SIDEBAR MENU CLICK (DESKTOP)
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('href').substring(1);
            showPage(page);
        });
    });
    
    // 初始化
    checkAuth();
</script>

</body>
</html>
