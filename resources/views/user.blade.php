<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>用户中心 - DPlayer 广告系统</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
:root {
            --primary: #00be06; --primary-dark: #00a305; --primary-light: #e8fcee;
            --accent: #fb7299; --accent-dark: #e85680;
            --vip-gold: #f5a623;
            --bg: #f5f7fa; --bg-card: #ffffff;
            --text: #1a1a2e; --text-secondary: #6b7280; --text-muted: #9ca3af;
            --border: #e5e7eb;
            --shadow: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.12);
            --radius: 12px; --radius-lg: 16px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        /* NAVBAR */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
background: #1a1a2e;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .navbar-inner { max-width: 1400px; margin: 0 auto; padding: 0 24px; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .navbar-brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; color:#fff; }
        .navbar-brand .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #00be06, #00a305); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
        .navbar-links { display: flex; align-items: center; gap: 6px; }
        .navbar-links a { padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; color: rgba(255,255,255,.7); transition: all 0.2s; }
        .navbar-links a:hover, .navbar-links a.active { color: #fff; background: rgba(255,255,255,.1); }
        .navbar-user { display: flex; align-items: center; gap: 12px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
        .btn-primary { background: linear-gradient(135deg, #00be06, #00a305); color: #fff; }
        .btn-vip { background: linear-gradient(135deg, #ffd700, #ff8c00); color: #fff; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-danger { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .btn-danger:hover { background: #ef4444; color: #fff; }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { background: var(--accent-dark); }
        .mobile-menu-btn { display: none; background: none; border: none; font-size: 22px; cursor: pointer; padding: 8px; color:#fff; }

        /* LAYOUT */
        .page-container { padding: 84px 24px 60px; max-width: 1200px; margin: 0 auto; display: flex; gap: 24px; }
        .sidebar { width: 280px; flex-shrink: 0; }
        .content { flex: 1; min-width: 0; }

        /* PROFILE CARD */
        .profile-card {
            background: var(--bg-card); border-radius: var(--radius-lg); padding: 28px;
            border: 1px solid var(--border); text-align: center; margin-bottom: 16px;
        }
        .profile-avatar {
            width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 12px;
            border: 3px solid var(--primary); object-fit: cover;
        }
        .profile-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .profile-id { font-size: 12px; color: var(--text-muted); margin-bottom: 12px; }
        .vip-badge {
            display: inline-flex; align-items: center; gap: 4px; padding: 4px 14px;
            border-radius: 20px; font-size: 12px; font-weight: 700;
        }
        .vip-badge.vip-0 { background: var(--bg); color: var(--text-muted); }
        .vip-badge.vip-1 { background: linear-gradient(135deg, #f5a623, #ff6b35); color: #fff; }
        .vip-badge.vip-2 { background: linear-gradient(135deg, #ffd700, #ff8c00); color: #fff; }
        .profile-stats {
            display: flex; justify-content: space-around; margin-top: 16px;
            padding-top: 16px; border-top: 1px solid var(--border);
        }
        .stat-item { text-align: center; }
        .stat-num { font-size: 20px; font-weight: 800; color: var(--primary); }
        .stat-label { font-size: 12px; color: var(--text-muted); }

        /* SIDEBAR MENU */
        .side-menu { background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
        .side-menu-item {
            display: flex; align-items: center; gap: 12px; padding: 14px 20px;
            font-size: 14px; font-weight: 500; color: var(--text-secondary);
            cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent;
        }
        .side-menu-item:hover { background: var(--primary-light); color: var(--primary); }
        .side-menu-item.active { background: var(--primary-light); color: var(--primary); border-left-color: var(--primary); font-weight: 600; }
        .side-menu-item i { width: 18px; text-align: center; }
        .side-menu-item .count { margin-left: auto; background: var(--bg); color: var(--text-muted); padding: 1px 8px; border-radius: 10px; font-size: 11px; }

        /* PANELS */
        .panel { display: none; }
        .panel.active { display: block; }
        .panel-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .panel-header h2 { font-size: 22px; font-weight: 700; }
        .panel-header .sub { font-size: 13px; color: var(--text-muted); }

        /* VIDEO GRID */
        .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .video-card {
            background: var(--bg-card); border-radius: var(--radius); overflow: hidden;
            border: 1px solid var(--border); cursor: pointer; transition: all 0.3s;
        }
        .video-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .video-card .thumb { position: relative; padding-top: 56.25%; background: linear-gradient(135deg, #667eea, #764ba2); }
        .video-card .thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .video-card .thumb .badge-vip { position: absolute; top: 6px; left: 6px; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; color: #fff; }
        .video-card .thumb .progress-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: rgba(0,0,0,0.3); }
        .video-card .thumb .progress-bar .bar { height: 100%; background: var(--accent); border-radius: 0 2px 0 0; }
        .video-card .info { padding: 10px 12px; }
        .video-card .info h4 { font-size: 13px; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .video-card .info .meta { font-size: 11px; color: var(--text-muted); }

        /* VIP INFO */
        .vip-info-card {
            background: linear-gradient(135deg, #1a1a2e, #16213e); border-radius: var(--radius-lg);
            padding: 28px; color: #fff; margin-bottom: 20px;
        }
        .vip-info-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .vip-info-top h3 { font-size: 20px; }
        .vip-info-top .level-tag { padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; }
        .vip-info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .vip-info-item { text-align: center; }
        .vip-info-item .num { font-size: 24px; font-weight: 800; color: #ffd700; }
        .vip-info-item .label { font-size: 12px; color: rgba(255,255,255,0.5); }

        /* SETTINGS FORM */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);
            font-size: 14px; transition: all 0.2s; background: var(--bg-card);
        }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,161,214,0.1); }
        .form-row { display: flex; gap: 16px; }
        .form-row .form-group { flex: 1; }
        .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .setting-card {
            background: var(--bg-card); border-radius: var(--radius); padding: 24px;
            border: 1px solid var(--border); margin-bottom: 16px;
        }
        .setting-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .setting-card h3 i { color: var(--primary); }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; display: block; }
        .empty-state p { font-size: 15px; margin-bottom: 16px; }

        /* LOGIN REQUIRED */
        .login-required { text-align: center; padding: 80px 20px; }
        .login-required i { font-size: 64px; color: var(--text-muted); opacity: 0.2; margin-bottom: 20px; }
        .login-required h2 { font-size: 22px; margin-bottom: 8px; }
        .login-required p { color: var(--text-secondary); margin-bottom: 24px; }

        /* TOAST */
        .toast {
            position: fixed; top: 80px; right: 24px; z-index: 9999;
            padding: 12px 20px; border-radius: 10px; font-size: 14px;
            background: #10b981; color: #fff; box-shadow: var(--shadow-lg);
            transform: translateX(120%); transition: transform 0.3s;
        }
        .toast.show { transform: translateX(0); }
        .toast.error { background: #ef4444; }

        /* FOOTER */
        .footer { background: var(--text); color: rgba(255,255,255,0.7); padding: 48px 24px 32px; margin-top: 60px; }
        .footer-inner { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 600; font-size: 16px; }
        .footer-brand .logo-icon { width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; }
        .footer-links { display: flex; gap: 24px; }
        .footer-links a { color: rgba(255,255,255,0.6); font-size: 14px; }
        .footer-links a:hover { color: #fff; }
        .footer-copy { width: 100%; text-align: center; padding-top: 24px; margin-top: 24px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 13px; color: rgba(255,255,255,0.4); }

        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            .navbar-links { display: none; position: absolute; top: 64px; left: 0; right: 0; background: #1a1a2e; border-bottom: 1px solid rgba(255,255,255,.1); flex-direction: column; padding: 16px; }
            .navbar-links.open { display: flex; }
            .navbar-links a { width: 100%; padding: 12px 16px; }
            .page-container { flex-direction: column; }
            .sidebar { width: 100%; }
            .video-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
            .form-row { flex-direction: column; gap: 0; }
            .vip-info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-brand">
            <div class="logo-icon"><i class="fas fa-play"></i></div>
            <span>DPlayer 广告系统</span>
        </a>
        <button class="mobile-menu-btn" onclick="document.getElementById('navLinks').classList.toggle('open')"><i class="fas fa-bars"></i></button>
        <div class="navbar-links" id="navLinks">
            <a href="/">首页</a>
            <a href="/v">影视中心</a>
            <a href="/player">播放器演示</a>
            <a href="/vip">VIP会员</a>
        </div>
        <div class="navbar-user" id="navUser"></div>
    </div>
</nav>

<div id="loginRequired" style="display:none;">
    <div class="login-required" style="padding-top:140px;">
        <i class="fas fa-user-lock"></i>
        <h2>请先登录</h2>
        <p>登录后即可查看您的用户中心</p>
        <a href="/login" class="btn btn-primary" style="padding:12px 32px;"><i class="fas fa-sign-in-alt"></i> 立即登录</a>
    </div>
</div>

<div class="page-container" id="mainContent" style="display:none;">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="profile-card">
            <img src="" class="profile-avatar" id="userAvatar" alt="">
            <div class="profile-name" id="userName">-</div>
            <div class="profile-id" id="userId">-</div>
            <span class="vip-badge vip-0" id="userVip">普通用户</span>
            <div class="profile-stats">
                <div class="stat-item"><div class="stat-num" id="statFav">0</div><div class="stat-label">收藏</div></div>
                <div class="stat-item"><div class="stat-num" id="statHistory">0</div><div class="stat-label">观看</div></div>
                <div class="stat-item"><div class="stat-num" id="statLikes">0</div><div class="stat-label">点赞</div></div>
            </div>
        </div>
        <div class="side-menu">
            <div class="side-menu-item active" data-panel="history"><i class="fas fa-history"></i> 观看历史 <span class="count" id="countHistory">0</span></div>
            <div class="side-menu-item" data-panel="favorites"><i class="fas fa-heart"></i> 我的收藏 <span class="count" id="countFav">0</span></div>
            <div class="side-menu-item" data-panel="vip"><i class="fas fa-crown"></i> VIP信息</div>

        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <!-- 观看历史 -->
        <div class="panel active" id="panel-history">
            <div class="panel-header">
                <div><h2>观看历史</h2><span class="sub">最近观看的视频</span></div>
                <button class="btn btn-outline" onclick="clearHistory()"><i class="fas fa-trash"></i> 清空</button>
            </div>
            <div class="video-grid" id="historyGrid"></div>
            <div class="empty-state" id="historyEmpty" style="display:none;">
                <i class="fas fa-history"></i>
                <p>还没有观看记录</p>
                <a href="/v" class="btn btn-primary">去逛逛</a>
            </div>
        </div>

        <!-- 我的收藏 -->
        <div class="panel" id="panel-favorites">
            <div class="panel-header">
                <div><h2>我的收藏</h2><span class="sub">收藏的视频</span></div>
            </div>
            <div class="video-grid" id="favGrid"></div>
            <div class="empty-state" id="favEmpty" style="display:none;">
                <i class="fas fa-heart"></i>
                <p>还没有收藏视频</p>
                <a href="/v" class="btn btn-primary">去逛逛</a>
            </div>
        </div>

        <!-- VIP信息 -->
        <div class="panel" id="panel-vip">
            <div class="panel-header"><h2>VIP会员信息</h2></div>
            <div class="vip-info-card">
                <div class="vip-info-top">
                    <h3 id="vipTitle">我的会员</h3>
                    <span class="level-tag" id="vipTag" style="background:rgba(255,255,255,0.1);">普通用户</span>
                </div>
                <div class="vip-info-grid">
                    <div class="vip-info-item"><div class="num" id="vipLevel">-</div><div class="label">会员等级</div></div>
                    <div class="vip-info-item"><div class="num" id="vipExpire">-</div><div class="label">到期时间</div></div>
                    <div class="vip-info-item"><div class="num" id="vipDays">-</div><div class="label">剩余天数</div></div>
                </div>
                <div style="text-align:center;margin-top:20px;">
                    <a href="/vip" class="btn btn-vip"><i class="fas fa-crown"></i> 开通/续费VIP</a>
                </div>
            </div>
            <div class="setting-card">
                <h3><i class="fas fa-star"></i> VIP特权</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> 免广告</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> 1080P/4K画质</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> VIP专属内容</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> 离线缓存</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> 多设备播放</div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-secondary);"><i class="fas fa-check-circle" style="color:#10b981;"></i> 专属客服</div>
                </div>
            </div>
        </div>



</div>

<div class="toast" id="toast"></div>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand"><div class="logo-icon"><i class="fas fa-play"></i></div><span>DPlayer 广告系统</span></div>
        <div class="footer-links">
            <a href="/">首页</a><a href="/v">影视中心</a><a href="/vip">VIP会员</a><a href="/player">播放器</a>
        </div>
        <div class="footer-copy">© 2026 DPlayer 广告播放器管理系统</div>
    </div>
</footer>

<script>
const API = '/api';
function getToken() { return localStorage.getItem('token'); }
function authHeaders() { return { 'Authorization': 'Bearer ' + getToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' }; }
function formatNum(n) { if(!n)return'0'; if(n>=10000)return(n/10000).toFixed(1)+'万'; return n+''; }
function formatDuration(s) { if(!s)return''; return Math.floor(s/60)+':'+(s%60<10?'0':'')+(s%60); }

function toast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = 'toast show' + (isError ? ' error' : '');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// 检查登录
function checkLogin() {
    if (!getToken()) {
        document.getElementById('loginRequired').style.display = 'block';
        document.getElementById('mainContent').style.display = 'none';
        return false;
    }
    document.getElementById('loginRequired').style.display = 'none';
    document.getElementById('mainContent').style.display = 'flex';
    return true;
}

// 初始化导航栏
function initNav() {
    const userStr = localStorage.getItem('user');
    const navUser = document.getElementById('navUser');
    if (userStr) {
        try {
            const u = JSON.parse(userStr);
            navUser.innerHTML = `
                <a href="/vip" class="btn btn-vip" style="font-size:12px;padding:6px 14px;"><i class="fas fa-crown"></i> VIP</a>
                <a href="/user" class="btn btn-primary" style="font-size:12px;padding:6px 14px;"><i class="fas fa-user"></i> 用户中心</a>`;
        } catch(e) {}
    } else {
        navUser.innerHTML = '<a href="/login" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> 登录</a>';
    }
}

// 加载用户信息
async function loadProfile() {
    try {
        const res = await fetch(API + '/auth/me', { headers: authHeaders() });
        const data = await res.json();
        if (!data.success) return;
        const u = data.data;
        // 更新本地缓存
        localStorage.setItem('user', JSON.stringify(u));
        renderProfile(u);
    } catch(e) {
        const u = JSON.parse(localStorage.getItem('user') || '{}');
        renderProfile(u);
    }
}

function renderProfile(u) {
    document.getElementById('userAvatar').src = u.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(u.nickname || u.username || 'U') + '&background=00a1d6&color=fff&size=160';
    document.getElementById('userName').textContent = u.nickname || u.username || '用户';
    document.getElementById('userId').textContent = 'ID: ' + (u.id || '-');
    const vipEl = document.getElementById('userVip');
    if (u.vip_level == 2) { vipEl.className = 'vip-badge vip-2'; vipEl.innerHTML = '<i class="fas fa-crown"></i> SVIP'; }
    else if (u.vip_level == 1) { vipEl.className = 'vip-badge vip-1'; vipEl.innerHTML = '<i class="fas fa-crown"></i> VIP'; }
    else { vipEl.className = 'vip-badge vip-0'; vipEl.textContent = '普通用户'; }

    // VIP信息
    loadVipInfo(u);
}

async function loadVipInfo(u) {
    const isVip = u.vip_level > 0;
    const tag = document.getElementById('vipTag');
    if (u.vip_level == 2) { tag.style.background = 'linear-gradient(135deg,#ffd700,#ff8c00)'; tag.textContent = 'SVIP超级会员'; }
    else if (u.vip_level == 1) { tag.style.background = 'linear-gradient(135deg,#f5a623,#ff6b35)'; tag.textContent = 'VIP会员'; }
    else { tag.textContent = '普通用户'; }
    document.getElementById('vipTitle').textContent = isVip ? '我的会员' : '开通会员';
    document.getElementById('vipLevel').textContent = u.vip_level == 2 ? 'SVIP' : (u.vip_level == 1 ? 'VIP' : '普通');
    if (u.vip_expire_at) {
        const expire = new Date(u.vip_expire_at);
        const now = new Date();
        const days = Math.max(0, Math.ceil((expire - now) / 86400000));
        document.getElementById('vipExpire').textContent = u.vip_expire_at.split(' ')[0];
        document.getElementById('vipDays').textContent = days + '天';
    } else {
        document.getElementById('vipExpire').textContent = '未开通';
        document.getElementById('vipDays').textContent = '-';
    }
}

// 加载观看历史
async function loadHistory() {
    try {
        const res = await fetch(API + '/history', { headers: authHeaders() });
        const data = await res.json();
        const items = data.data?.data || [];
        document.getElementById('countHistory').textContent = items.length;
        document.getElementById('statHistory').textContent = items.length;
        const grid = document.getElementById('historyGrid');
        const empty = document.getElementById('historyEmpty');
        if (items.length === 0) { grid.style.display = 'none'; empty.style.display = 'block'; return; }
        grid.style.display = 'grid'; empty.style.display = 'none';
        grid.innerHTML = items.map(v => {
            const vip = v.vip_level == 2 ? 'badge-vip-2' : (v.vip_level == 1 ? 'badge-vip-1' : '');
            const vipLabel = v.vip_level == 2 ? 'SVIP' : (v.vip_level == 1 ? 'VIP' : '');
            const pct = v.duration > 0 ? Math.min(100, Math.round(v.progress / v.duration * 100)) : 0;
            return `<div class="video-card" onclick="location.href='/v/${v.id}'">
                <div class="thumb">
                    <img src="${v.cover || 'https://via.placeholder.com/400x225/667eea/fff?text=Video'}" alt="${v.title}" loading="lazy">
                    ${vipLabel ? `<span class="badge-vip ${vip}" style="position:absolute;top:6px;left:6px;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;">${vipLabel}</span>` : ''}
                    <div class="progress-bar"><div class="bar" style="width:${pct}%"></div></div>
                </div>
                <div class="info"><h4>${v.title}</h4><div class="meta">已看${pct}% · ${new Date(v.watched_at).toLocaleDateString()}</div></div>
            </div>`;
        }).join('');
    } catch(e) { console.error(e); }
}

// 加载收藏
async function loadFavorites() {
    try {
        const res = await fetch(API + '/favorites', { headers: authHeaders() });
        const data = await res.json();
        const items = data.data?.data || [];
        document.getElementById('countFav').textContent = items.length;
        document.getElementById('statFav').textContent = items.length;
        const grid = document.getElementById('favGrid');
        const empty = document.getElementById('favEmpty');
        if (items.length === 0) { grid.style.display = 'none'; empty.style.display = 'block'; return; }
        grid.style.display = 'grid'; empty.style.display = 'none';
        grid.innerHTML = items.map(v => {
            const vip = v.vip_level == 2 ? 'badge-vip-2' : (v.vip_level == 1 ? 'badge-vip-1' : '');
            const vipLabel = v.vip_level == 2 ? 'SVIP' : (v.vip_level == 1 ? 'VIP' : '');
            return `<div class="video-card" onclick="location.href='/v/${v.id}'">
                <div class="thumb">
                    <img src="${v.cover || 'https://via.placeholder.com/400x225/667eea/fff?text=Video'}" alt="${v.title}" loading="lazy">
                    ${vipLabel ? `<span class="badge-vip ${vip}" style="position:absolute;top:6px;left:6px;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;">${vipLabel}</span>` : ''}
                </div>
                <div class="info"><h4>${v.title}</h4><div class="meta"><i class="fas fa-star" style="color:#f5a623;font-size:10px;"></i> ${v.score || '-'} · ${formatNum(v.views)}次播放</div></div>
            </div>`;
        }).join('');
    } catch(e) { console.error(e); }
}

// 侧边栏切换
document.querySelectorAll('.side-menu-item').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('.side-menu-item').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        item.classList.add('active');
        document.getElementById('panel-' + item.dataset.panel).classList.add('active');
    });
});

// 清空历史
async function clearHistory() {
    if (!confirm('确定要清空观看历史吗？')) return;
    try {
        const res = await fetch(API + '/history/clear', { method: 'POST', headers: authHeaders() });
        const data = await res.json();
        if (data.success) { toast('已清空'); loadHistory(); }
    } catch(e) { toast('操作失败', true); }
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
    initNav();
    if (checkLogin()) {
        loadProfile();
        loadHistory();
        loadFavorites();
    }
});

</script>

</body>
</html>
