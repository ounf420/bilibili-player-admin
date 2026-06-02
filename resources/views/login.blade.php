<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - DPlayer 广告系统</title>
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
            --text: #1a1a2e;
            --text-secondary: #6b7280;
            --border: #e5e7eb;
            --radius: 12px;
            --shadow: 0 4px 6px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 40px;
        }
        .login-header {
            text-align: center; margin-bottom: 32px;
        }
        .login-logo {
            width: 60px; height: 60px; margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff;
        }
        .login-header h1 {
            font-size: 24px; font-weight: 700; margin-bottom: 8px;
        }
        .login-header p {
            font-size: 14px; color: var(--text-secondary);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block; font-size: 14px; font-weight: 500;
            margin-bottom: 8px; color: var(--text);
        }
        .form-input {
            width: 100%; padding: 12px 14px;
            border: 1px solid var(--border); border-radius: 8px;
            font-size: 14px; outline: none; transition: all 0.2s;
        }
        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,161,214,0.1);
        }
        .form-input::placeholder { color: #9ca3af; }
        .form-error {
            font-size: 12px; color: #ef4444; margin-top: 6px;
            display: none;
        }
        .form-error.show { display: block; }
        .btn {
            width: 100%; padding: 12px; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; box-shadow: 0 2px 8px rgba(0,161,214,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,161,214,0.4);
        }
        .btn:disabled {
            opacity: 0.6; cursor: not-allowed; transform: none;
        }
        .form-links {
            display: flex; justify-content: space-between;
            margin-top: 16px; font-size: 13px;
        }
        .form-links a {
            color: var(--primary); text-decoration: none;
        }
        .form-links a:hover { text-decoration: underline; }
        .divider {
            display: flex; align-items: center; margin: 24px 0;
            color: var(--text-secondary); font-size: 13px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }
        .divider span { padding: 0 12px; }
        .social-login {
            display: flex; justify-content: center; gap: 16px;
        }
        .social-btn {
            width: 44px; height: 44px; border-radius: 10px;
            border: 1px solid var(--border); background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; cursor: pointer; transition: all 0.2s;
        }
        .social-btn:hover {
            border-color: var(--primary); color: var(--primary);
        }
        .toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px);
            padding: 12px 24px; border-radius: 8px; font-size: 14px;
            z-index: 9999; transition: transform 0.3s; color: #fff;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }

        @media (max-width: 480px) {
            .login-container {
                padding: 16px;
            }
            .login-card {
                padding: 28px 20px;
            }
            .login-header h1 {
                font-size: 22px;
            }
            .form-input {
                padding: 12px 14px;
            }
            .btn-primary {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo"><i class="fas fa-play"></i></div>
                <h1>欢迎回来</h1>
                <p>登录你的 DPlayer 账号</p>
            </div>
            
            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">账号</label>
                    <input type="text" class="form-input" id="account" placeholder="用户名/邮箱/手机号" required>
                    <div class="form-error" id="accountError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">密码</label>
                    <input type="password" class="form-input" id="password" placeholder="请输入密码" required>
                    <div class="form-error" id="passwordError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> 登录
                </button>
            </form>
            
            <div class="form-links">
                <a href="/register">注册账号</a>
                <a href="/forgot-password">忘记密码？</a>
            </div>
            
            <div class="divider" id="socialDivider" style="display:none"><span>其他登录方式</span></div>
            
            <div class="social-login" id="socialLoginBtns">
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    
    <script>
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = `toast toast-${type} show`;
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
        
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const account = document.getElementById('account').value.trim();
            const password = document.getElementById('password').value;
            const btn = document.getElementById('submitBtn');
            
            // 验证
            let hasError = false;
            
            if (!account) {
                document.getElementById('accountError').textContent = '请输入账号';
                document.getElementById('accountError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('accountError').classList.remove('show');
            }
            
            if (!password) {
                document.getElementById('passwordError').textContent = '请输入密码';
                document.getElementById('passwordError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('passwordError').classList.remove('show');
            }
            
            if (hasError) return;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 登录中...';
            
            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ account, password })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    localStorage.setItem('token', data.data.token);
                    localStorage.setItem('user', JSON.stringify(data.data.user));
                    showToast('登录成功，正在跳转...');
                    setTimeout(() => window.location.href = '/account', 1000);
                } else {
                    showToast(data.message || '登录失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> 登录';
            }
        });
        
        // 加载启用的平台
        async function loadSocialPlatforms() {
            try {
                const res = await fetch('/api/socialite/platforms');
                const data = await res.json();
                if (data.success && data.data.length > 0) {
                    const container = document.getElementById('socialLoginBtns');
                    container.innerHTML = data.data.map(p => 
                        `<button class="social-btn" title="${p.name}登录" onclick="socialLogin('${p.key}')" style="color:${p.color}"><i class="${p.icon}"></i></button>`
                    ).join('');
                    document.getElementById('socialDivider').style.display = 'flex';
                }
            } catch (err) {
                console.error('加载社交平台失败', err);
            }
        }
        loadSocialPlatforms();
        // 第三方登录
        async function socialLogin(type) {
            try {
                const res = await fetch('/api/socialite/login?type=' + type);
                const data = await res.json();
                
                if (data.success && data.data.url) {
                    // 打开新窗口进行授权
                    const width = 600, height = 700;
                    const left = (screen.width - width) / 2;
                    const top = (screen.height - height) / 2;
                    window.open(data.data.url, 'socialite_' + type, 
                        `width=${width},height=${height},left=${left},top=${top}`);
                    
                    // 监听回调消息
                    window.addEventListener('message', async function handler(e) {
                        if (e.data && e.data.platform === type) {
                            window.removeEventListener('message', handler);
                            
                            if (e.data.need_bind) {
                                // 新用户，自动注册登录
                                const loginRes = await fetch('/api/socialite/login-with-temp', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ temp_key: e.data.temp_key })
                                });
                                const loginData = await loginRes.json();
                                
                                if (loginData.success) {
                                    localStorage.setItem('token', loginData.data.token);
                                    localStorage.setItem('user', JSON.stringify(loginData.data.user));
                                    showToast('登录成功，正在跳转...');
                                    setTimeout(() => window.location.href = '/account', 1000);
                                } else {
                                    showToast(loginData.message || '登录失败', 'error');
                                }
                            } else if (e.data.success) {
                                // 已绑定用户直接登录
                                localStorage.setItem('token', e.data.token);
                                localStorage.setItem('user', JSON.stringify(e.data.user));
                                showToast('登录成功，正在跳转...');
                                setTimeout(() => window.location.href = '/account', 1000);
                            } else {
                                showToast(e.data.message || '登录失败', 'error');
                            }
                        }
                    });
                } else {
                    showToast(data.message || '获取登录地址失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            }
        }
    </script>
</body>
</html>
