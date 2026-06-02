<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>找回密码 - DPlayer 广告系统</title>
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
        .container {
            width: 100%; max-width: 420px; padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 40px;
        }
        .header {
            text-align: center; margin-bottom: 32px;
        }
        .logo {
            width: 60px; height: 60px; margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff;
        }
        .header h1 {
            font-size: 24px; font-weight: 700; margin-bottom: 8px;
        }
        .header p {
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
        .steps {
            display: flex; justify-content: center; margin-bottom: 24px;
        }
        .step {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--text-muted);
        }
        .step.active { color: var(--primary); font-weight: 600; }
        .step-number {
            width: 24px; height: 24px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600;
            border: 2px solid var(--border); background: #fff;
        }
        .step.active .step-number {
            border-color: var(--primary); background: var(--primary); color: #fff;
        }
        .step-line {
            width: 40px; height: 2px; background: var(--border);
        }
        .code-input {
            display: flex; gap: 8px; justify-content: center;
        }
        .code-input input {
            width: 48px; height: 56px; text-align: center;
            font-size: 24px; font-weight: 600;
            border: 2px solid var(--border); border-radius: 8px;
            outline: none; transition: all 0.2s;
        }
        .code-input input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,161,214,0.1);
        }
        .toast {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px);
            padding: 12px 24px; border-radius: 8px; font-size: 14px;
            z-index: 9999; transition: transform 0.3s; color: #fff;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }
        .hidden { display: none; }

        @media (max-width: 480px) {
            .container {
                padding: 16px;
            }
            .card {
                padding: 28px 20px;
            }
            .header h1 {
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
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo"><i class="fas fa-key"></i></div>
                <h1>找回密码</h1>
                <p>通过邮箱验证码重置密码</p>
            </div>
            
            <div class="steps">
                <div class="step active" id="step1">
                    <div class="step-number">1</div>
                    <span>输入邮箱</span>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step2">
                    <div class="step-number">2</div>
                    <span>验证邮箱</span>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step3">
                    <div class="step-number">3</div>
                    <span>重置密码</span>
                </div>
            </div>
            
            <!-- 步骤1: 输入邮箱 -->
            <form id="step1Form">
                <div class="form-group">
                    <label class="form-label">注册邮箱</label>
                    <input type="email" class="form-input" id="email" placeholder="请输入注册时的邮箱" required>
                    <div class="form-error" id="emailError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="sendCodeBtn">
                    <i class="fas fa-paper-plane"></i> 发送验证码
                </button>
            </form>
            
            <!-- 步骤2: 输入验证码 -->
            <form id="step2Form" class="hidden">
                <div class="form-group">
                    <label class="form-label">验证码已发送至 <span id="emailDisplay"></span></label>
                    <div class="code-input">
                        <input type="text" maxlength="1" id="code1" oninput="moveToNext(this, 'code2')">
                        <input type="text" maxlength="1" id="code2" oninput="moveToNext(this, 'code3')">
                        <input type="text" maxlength="1" id="code3" oninput="moveToNext(this, 'code4')">
                        <input type="text" maxlength="1" id="code4" oninput="moveToNext(this, 'code5')">
                        <input type="text" maxlength="1" id="code5" oninput="moveToNext(this, 'code6')">
                        <input type="text" maxlength="1" id="code6">
                    </div>
                    <div class="form-error" id="codeError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="verifyBtn">
                    <i class="fas fa-check"></i> 验证
                </button>
                
                <div class="form-links">
                    没收到？ <a href="#" id="resendBtn">重新发送</a>
                </div>
            </form>
            
            <!-- 步骤3: 重置密码 -->
            <form id="step3Form" class="hidden">
                <div class="form-group">
                    <label class="form-label">新密码</label>
                    <input type="password" class="form-input" id="newPassword" placeholder="至少6位" required>
                    <div class="form-error" id="newPasswordError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">确认密码</label>
                    <input type="password" class="form-input" id="confirmPassword" placeholder="再次输入密码" required>
                    <div class="form-error" id="confirmPasswordError"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="resetBtn">
                    <i class="fas fa-lock"></i> 重置密码
                </button>
            </form>
            
            <div class="form-links">
                <a href="/login">返回登录</a>
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    
    <script>
        let currentEmail = '';
        
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = `toast toast-${type} show`;
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
        
        function moveToNext(current, nextId) {
            if (current.value.length === 1) {
                document.getElementById(nextId).focus();
            }
        }
        
        function getCode() {
            return ['code1','code2','code3','code4','code5','code6']
                .map(id => document.getElementById(id).value).join('');
        }
        
        function setStep(step) {
            document.getElementById('step1Form').classList.toggle('hidden', step !== 1);
            document.getElementById('step2Form').classList.toggle('hidden', step !== 2);
            document.getElementById('step3Form').classList.toggle('hidden', step !== 3);
            
            document.getElementById('step1').classList.toggle('active', step >= 1);
            document.getElementById('step2').classList.toggle('active', step >= 2);
            document.getElementById('step3').classList.toggle('active', step >= 3);
        }
        
        // 步骤1: 发送验证码
        document.getElementById('step1Form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const btn = document.getElementById('sendCodeBtn');
            
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('emailError').textContent = '请输入有效的邮箱地址';
                document.getElementById('emailError').classList.add('show');
                return;
            }
            document.getElementById('emailError').classList.remove('show');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 发送中...';
            
            try {
                const res = await fetch('/api/auth/send-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, type: 'reset_password' })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    currentEmail = email;
                    document.getElementById('emailDisplay').textContent = email.replace(/(.{2}).*(@.*)/, '$1***$2');
                    setStep(2);
                    document.getElementById('code1').focus();
                    showToast('验证码已发送');
                } else {
                    showToast(data.message || '发送失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> 发送验证码';
            }
        });
        
        // 步骤2: 验证验证码
        document.getElementById('step2Form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const code = getCode();
            const btn = document.getElementById('verifyBtn');
            
            if (code.length !== 6) {
                document.getElementById('codeError').textContent = '请输入完整的验证码';
                document.getElementById('codeError').classList.add('show');
                return;
            }
            document.getElementById('codeError').classList.remove('show');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 验证中...';
            
            try {
                const res = await fetch('/api/auth/verify-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: currentEmail, code, type: 'reset_password' })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    setStep(3);
                    showToast('验证成功');
                } else {
                    showToast(data.message || '验证失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> 验证';
            }
        });
        
        // 重新发送
        document.getElementById('resendBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            
            try {
                const res = await fetch('/api/auth/send-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: currentEmail, type: 'reset_password' })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    showToast('验证码已重新发送');
                } else {
                    showToast(data.message || '发送失败', 'error');
                }
            } catch (err) {
                showToast('网络错误', 'error');
            }
        });
        
        // 步骤3: 重置密码
        document.getElementById('step3Form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const password = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const btn = document.getElementById('resetBtn');
            
            let hasError = false;
            
            if (!password || password.length < 6) {
                document.getElementById('newPasswordError').textContent = '密码至少6位';
                document.getElementById('newPasswordError').classList.add('show');
                hasError = true;
            } else {
                document.getElementById('newPasswordError').classList.remove('show');
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
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 重置中...';
            
            try {
                const code = getCode();
                const res = await fetch('/api/auth/reset-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: currentEmail, code, password })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    showToast('密码重置成功，正在跳转登录...');
                    setTimeout(() => window.location.href = '/login', 2000);
                } else {
                    showToast(data.message || '重置失败', 'error');
                }
            } catch (err) {
                showToast('网络错误，请重试', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> 重置密码';
            }
        });
    </script>
</body>
</html>
