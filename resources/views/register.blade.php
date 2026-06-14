<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - DPlayer 广告系统</title>
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
        .register-container {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .register-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 40px;
        }
        .register-header {
            text-align: center; margin-bottom: 32px;
        }
        .register-logo {
            width: 60px; height: 60px; margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff;
        }
        .register-header h1 {
            font-size: 24px; font-weight: 700; margin-bottom: 8px;
        }
        .register-header p {
            font-size: 14px; color: var(--text-secondary);
        }
        .form-group {
            margin-bottom: 18px;
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
            text-align: center; margin-top: 16px; font-size: 13px;
        }
        .form-links a {
            color: var(--primary); text-decoration: none;
        }
        .form-links a:hover { text-decoration: underline; }
        .password-strength {
            margin-top: 8px; display: flex; gap: 4px;
        }
        .strength-bar {
            flex: 1; height: 4px; border-radius: 2px; background: var(--border);
        }
        .strength-bar.weak { background: #ef4444; }
        .strength-bar.medium { background: #f59e0b; }
        .strength-bar.strong { background: #10b981; }
        .toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px);
            padding: 12px 24px; border-radius: 8px; font-size: 14px;
            z-index: 9999; transition: transform 0.3s; color: #fff;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }

        @media (max-width: 480px) {
            .register-container {
                padding: 16px;
            }
            .register-card {
                padding: 28px 20px;
            }
            .register-header h1 {
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
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo"><i class="fas fa-play"></i></div>
                <h1>创建账号</h1>
                <p>注册你的 DPlayer 账号</p>
            </div>
            
            <form id="registerForm">
                <div class="form-group">
                    <label class="form-label">用户名</label>
                    <input type="text" class="form-input" id="username" placeholder="3-20位字母数字下划线" required>
                    <div class="form-error" id="usernameError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">邮箱</label>
                    <input type="email" class="form-input" id="email" placeholder="用于接收验证码和找回密码" required>
                    <div class="form-error" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">手机号（可选）</label>
                    <input type="tel" class="form-input" id="phone" placeholder="用于账号安全">
                    <div class="form-error" id="phoneError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">密码</label>
                    <input type="password" class="form-input" id="password" placeholder="至少6位" required>
                    <div class="password-strength">
                        <div class="strength-bar" id="str1"></div>
                        <div class="strength-bar" id="str2"></div>
                        <div class="strength-bar" id="str3"></div>
                        <div class="strength-bar" id="str4"></div>
                    </div>
                    <div class="form-error" id="passwordError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">确认密码</label>
                    <input type="password" class="form-input" id="confirmPassword" placeholder="再次输入密码" required>
                    <div class="form-error" id="confirmPasswordError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-user-plus"></i> 注册
                </button>
            </form>
            
            <div class="form-links">
                已有账号？ <a href="/login">立即登录</a>
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
        
        // 密码强度检测
        document.getElementById('password').addEventListener('input', (e) => {
            const password = e.target.value;
            const bars = [document.getElementById('str1'), document.getElementById('str2'), 
                         document.getElementById('str3'), document.getElementById('str4')];
            
            // 重置
            bars.forEach(bar => bar.className = 'strength-bar');
            
            if (password.length === 0) return;
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength++;
            
            const classes = ['weak', 'weak', 'medium', 'strong'];
            for (let i = 0; i < strength; i++) {
                bars[i].classList.add(classes[strength - 1]);
            }
        });
        
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const btn = document.getElementById('submitBtn');
            
            // 验证
            let hasError = false;
            
            if (!username || username.length < 3) {
                document.getElementById('usernameError').textContent = '用户名至少3个字符';
                document.getElementById('usernameError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('usernameError').classList.remove('show');
            }
            
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').textContent = '请输入有效的邮箱地址';
                document.getElementById('emailError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('emailError').classList.remove('show');
            }
            
            if (phone && !/^1[3-9]\d{9}$/.test(phone)) {
                document.getElementById('phoneError').textContent = '请输入有效的手机号';
                document.getElementById('phoneError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('phoneError').classList.remove('show');
            }
            
            if (!password || password.length < 6) {
                document.getElementById('passwordError').textContent = '密码至少6位';
                document.getElementById('passwordError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('passwordError').classList.remove('show');
            }
            
            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').textContent = '两次密码不一致';
                document.getElementById('confirmPasswordError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('confirmPasswordError').classList.remove('show');
            }
            
            if (hasError) return;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 注册中...';
            
            try {
                const res = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, phone: phone || undefined, password })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    localStorage.setItem('token', data.data.token);
                    localStorage.setItem('user', JSON.stringify(data.data.user));
                    showToast('注册成功，正在跳转...');
                    setTimeout(() => window.location.href = '/user', 1000);
                } else {
                    showToast(data.message || '注册失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-user-plus"></i> 注册';
            }
        });
    </script>
</body>
</html>
