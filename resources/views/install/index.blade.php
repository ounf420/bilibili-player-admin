<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装向导</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a1a;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .installer {
            width: 100%;
            max-width: 720px;
        }
        .installer-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .installer-header h1 {
            font-size: 28px;
            background: linear-gradient(135deg, #00f0ff, #ff2d95);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .installer-header p {
            color: #888;
            font-size: 14px;
        }

        /* Steps indicator */
        .steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 32px;
        }
        .step-item {
            display: flex;
            align-items: center;
        }
        .step-circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        .step-item.active .step-circle {
            border-color: #00f0ff;
            color: #00f0ff;
            box-shadow: 0 0 12px rgba(0,240,255,0.3);
        }
        .step-item.done .step-circle {
            border-color: #ff2d95;
            background: #ff2d95;
            color: #fff;
        }
        .step-label {
            font-size: 13px;
            color: #555;
            margin-left: 8px;
            white-space: nowrap;
        }
        .step-item.active .step-label { color: #00f0ff; }
        .step-item.done .step-label { color: #ff2d95; }
        .step-line {
            width: 40px;
            height: 2px;
            background: #333;
            margin: 0 8px;
            align-self: center;
        }
        .step-line.done { background: #ff2d95; }

        /* Card */
        .card {
            background: #12122a;
            border: 1px solid #1e1e3a;
            border-radius: 12px;
            padding: 32px;
        }

        /* Step panels */
        .step-panel { display: none; }
        .step-panel.active { display: block; }

        /* Environment checks */
        .check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #0a0a1a;
        }
        .check-name { font-size: 14px; }
        .check-info { font-size: 12px; color: #888; }
        .check-status i { font-size: 20px; }
        .check-status .pass { color: #00f0ff; }
        .check-status .fail { color: #ff2d95; }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #ccc;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            background: #0a0a1a;
            border: 1px solid #2a2a4a;
            border-radius: 8px;
            color: #e0e0e0;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #00f0ff;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Buttons */
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00f0ff, #00b8d4);
            color: #0a0a1a;
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary {
            background: #1e1e3a;
            color: #ccc;
            border: 1px solid #2a2a4a;
        }
        .btn-secondary:hover { background: #2a2a4a; }
        .btn-pink {
            background: linear-gradient(135deg, #ff2d95, #d4267a);
            color: #fff;
        }
        .btn-pink:hover { opacity: 0.9; }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-sm { padding: 8px 16px; font-size: 13px; }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .alert-success { background: rgba(0,240,255,0.1); color: #00f0ff; border: 1px solid rgba(0,240,255,0.2); }
        .alert-error { background: rgba(255,45,149,0.1); color: #ff2d95; border: 1px solid rgba(255,45,149,0.2); }
        .alert-info { background: rgba(255,255,255,0.05); color: #aaa; border: 1px solid #2a2a4a; }

        /* Summary */
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #1e1e3a;
            font-size: 14px;
        }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { color: #888; }

        /* Progress */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #1e1e3a;
            border-radius: 3px;
            overflow: hidden;
            margin: 16px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00f0ff, #ff2d95);
            width: 0%;
            transition: width 0.5s;
            border-radius: 3px;
        }
        .install-log {
            background: #0a0a1a;
            border-radius: 8px;
            padding: 16px;
            max-height: 200px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 13px;
            color: #888;
            margin-top: 16px;
        }
        .install-log .log-line { margin-bottom: 4px; }
        .install-log .log-line.success { color: #00f0ff; }
        .install-log .log-line.error { color: #ff2d95; }

        .success-panel {
            text-align: center;
            padding: 40px 0;
        }
        .success-panel i {
            font-size: 64px;
            color: #00f0ff;
            margin-bottom: 16px;
        }
        .success-panel h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .success-panel p {
            color: #888;
        }

        .test-result {
            margin-top: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: none;
        }

        @media (max-width: 600px) {
            .card { padding: 20px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .step-label { display: none; }
            .step-line { width: 24px; margin: 0 4px; }
            .installer-header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="installer">
    <div class="installer-header">
        <h1><i class="ri-install-line"></i> 系统安装向导</h1>
        <p>请按照以下步骤完成系统安装配置</p>
    </div>

    <!-- Steps indicator -->
    <div class="steps" id="stepsIndicator">
        <div class="step-item active" data-step="0">
            <div class="step-circle">1</div>
            <span class="step-label">环境检测</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item" data-step="1">
            <div class="step-circle">2</div>
            <span class="step-label">数据库配置</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item" data-step="2">
            <div class="step-circle">3</div>
            <span class="step-label">管理员设置</span>
        </div>
        <div class="step-line"></div>
        <div class="step-item" data-step="3">
            <div class="step-circle">4</div>
            <span class="step-label">安装</span>
        </div>
    </div>

    <div class="card">
        <!-- Step 1: Environment Check -->
        <div class="step-panel active" id="step0">
            <h3 style="margin-bottom:20px;"><i class="ri-checkbox-circle-line" style="color:#00f0ff"></i> 环境检测</h3>

            @if($allPassed)
                <div class="alert alert-success"><i class="ri-check-line"></i> 环境检测全部通过，可以继续安装</div>
            @else
                <div class="alert alert-error"><i class="ri-error-warning-line"></i> 部分环境检测未通过，请修复后刷新页面</div>
            @endif

            @foreach($checks as $check)
                <div class="check-row">
                    <div>
                        <div class="check-name">{{ $check['name'] }}</div>
                        <div class="check-info">当前: {{ $check['current'] }} @if($check['required']) | 要求: {{ $check['required'] }} @endif</div>
                    </div>
                    <div class="check-status">
                        @if($check['passed'])
                            <i class="ri-check-line pass"></i>
                        @else
                            <i class="ri-close-line fail"></i>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="actions">
                <div></div>
                <button class="btn btn-primary" onclick="goToStep(1)" {{ $allPassed ? '' : 'disabled' }}>
                    下一步 <i class="ri-arrow-right-line"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Database Config -->
        <div class="step-panel" id="step1">
            <h3 style="margin-bottom:20px;"><i class="ri-database-2-line" style="color:#00f0ff"></i> 数据库配置</h3>

            <div id="dbAlert" class="alert" style="display:none;"></div>

            <div class="form-row">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" id="db_host" value="127.0.0.1" placeholder="127.0.0.1">
                </div>
                <div class="form-group">
                    <label>端口</label>
                    <input type="text" id="db_port" value="3306" placeholder="3306">
                </div>
            </div>
            <div class="form-group">
                <label>数据库名称</label>
                <input type="text" id="db_database" placeholder="请输入数据库名称">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" id="db_username" value="root" placeholder="root">
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" id="db_password" placeholder="请输入数据库密码">
                </div>
            </div>

            <div id="testResult" class="test-result"></div>

            <div class="actions">
                <button class="btn btn-secondary" onclick="goToStep(0)">
                    <i class="ri-arrow-left-line"></i> 上一步
                </button>
                <div>
                    <button class="btn btn-secondary btn-sm" id="btnTestDb" onclick="testDatabase()" style="margin-right:8px;">
                        <i class="ri-link"></i> 测试连接
                    </button>
                    <button class="btn btn-primary" id="btnNext1" onclick="validateDbAndNext()">
                        下一步 <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Admin Account -->
        <div class="step-panel" id="step2">
            <h3 style="margin-bottom:20px;"><i class="ri-user-settings-line" style="color:#00f0ff"></i> 管理员设置</h3>

            <div class="form-group">
                <label>站点名称</label>
                <input type="text" id="site_name" placeholder="请输入站点名称">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>管理员用户名</label>
                    <input type="text" id="admin_name" placeholder="请输入管理员用户名">
                </div>
                <div class="form-group">
                    <label>管理员邮箱</label>
                    <input type="email" id="admin_email" placeholder="请输入管理员邮箱">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" id="admin_password" placeholder="请输入密码（至少6位）">
                </div>
                <div class="form-group">
                    <label>确认密码</label>
                    <input type="password" id="admin_password_confirmation" placeholder="请再次输入密码">
                </div>
            </div>

            <div id="adminAlert" class="alert" style="display:none;"></div>

            <div class="actions">
                <button class="btn btn-secondary" onclick="goToStep(1)">
                    <i class="ri-arrow-left-line"></i> 上一步
                </button>
                <button class="btn btn-primary" onclick="validateAdminAndNext()">
                    下一步 <i class="ri-arrow-right-line"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Install -->
        <div class="step-panel" id="step3">
            <h3 style="margin-bottom:20px;"><i class="ri-rocket-2-line" style="color:#00f0ff"></i> 确认安装</h3>

            <div id="summaryPanel">
                <div class="summary-item">
                    <span class="summary-label">数据库主机</span>
                    <span id="sum_host"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">数据库名称</span>
                    <span id="sum_database"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">数据库用户</span>
                    <span id="sum_username"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">站点名称</span>
                    <span id="sum_site_name"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">管理员账号</span>
                    <span id="sum_admin_name"></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">管理员邮箱</span>
                    <span id="sum_admin_email"></span>
                </div>
            </div>

            <div id="installProgress" style="display:none;">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="install-log" id="installLog"></div>
            </div>

            <div id="successPanel" class="success-panel" style="display:none;">
                <i class="ri-checkbox-circle-line"></i>
                <h2>安装成功！</h2>
                <p>系统已成功安装，即将跳转到登录页面...</p>
            </div>

            <div class="actions" id="installActions">
                <button class="btn btn-secondary" onclick="goToStep(2)">
                    <i class="ri-arrow-left-line"></i> 上一步
                </button>
                <button class="btn btn-pink" id="btnInstall" onclick="doInstall()">
                    <i class="ri-download-line"></i> 开始安装
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 0;
let dbTestPassed = false;

function goToStep(step) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');

    const items = document.querySelectorAll('.step-item');
    const lines = document.querySelectorAll('.step-line');

    items.forEach((item, i) => {
        item.classList.remove('active', 'done');
        if (i < step) item.classList.add('done');
        else if (i === step) item.classList.add('active');
    });
    lines.forEach((line, i) => {
        line.classList.toggle('done', i < step);
    });

    currentStep = step;

    if (step === 3) {
        document.getElementById('sum_host').textContent = document.getElementById('db_host').value + ':' + document.getElementById('db_port').value;
        document.getElementById('sum_database').textContent = document.getElementById('db_database').value;
        document.getElementById('sum_username').textContent = document.getElementById('db_username').value;
        document.getElementById('sum_site_name').textContent = document.getElementById('site_name').value;
        document.getElementById('sum_admin_name').textContent = document.getElementById('admin_name').value;
        document.getElementById('sum_admin_email').textContent = document.getElementById('admin_email').value;
    }
}

async function testDatabase() {
    const btn = document.getElementById('btnTestDb');
    const result = document.getElementById('testResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> 测试中...';
    result.style.display = 'none';

    try {
        const res = await fetch('/install/test-database', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                host: document.getElementById('db_host').value,
                port: document.getElementById('db_port').value,
                database: document.getElementById('db_database').value,
                username: document.getElementById('db_username').value,
                password: document.getElementById('db_password').value
            })
        });
        const data = await res.json();
        result.style.display = 'block';
        if (data.success) {
            result.className = 'test-result alert alert-success';
            result.innerHTML = '<i class="ri-check-line"></i> ' + (data.message || '数据库连接成功');
            dbTestPassed = true;
        } else {
            result.className = 'test-result alert alert-error';
            result.innerHTML = '<i class="ri-close-line"></i> ' + (data.message || '数据库连接失败');
            dbTestPassed = false;
        }
    } catch (e) {
        result.style.display = 'block';
        result.className = 'test-result alert alert-error';
        result.innerHTML = '<i class="ri-close-line"></i> 请求失败: ' + e.message;
        dbTestPassed = false;
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="ri-link"></i> 测试连接';
}

function validateDbAndNext() {
    const alert = document.getElementById('dbAlert');
    const db = document.getElementById('db_database').value.trim();
    const user = document.getElementById('db_username').value.trim();

    if (!db || !user) {
        alert.style.display = 'block';
        alert.className = 'alert alert-error';
        alert.innerHTML = '<i class="ri-error-warning-line"></i> 请填写完整的数据库信息';
        return;
    }

    if (!dbTestPassed) {
        alert.style.display = 'block';
        alert.className = 'alert alert-error';
        alert.innerHTML = '<i class="ri-error-warning-line"></i> 请先测试数据库连接';
        return;
    }

    alert.style.display = 'none';
    goToStep(2);
}

function validateAdminAndNext() {
    const alert = document.getElementById('adminAlert');
    const site = document.getElementById('site_name').value.trim();
    const name = document.getElementById('admin_name').value.trim();
    const email = document.getElementById('admin_email').value.trim();
    const pwd = document.getElementById('admin_password').value;
    const pwd2 = document.getElementById('admin_password_confirmation').value;

    if (!site || !name || !email || !pwd) {
        alert.style.display = 'block';
        alert.className = 'alert alert-error';
        alert.innerHTML = '<i class="ri-error-warning-line"></i> 请填写所有必填字段';
        return;
    }
    if (pwd.length < 6) {
        alert.style.display = 'block';
        alert.className = 'alert alert-error';
        alert.innerHTML = '<i class="ri-error-warning-line"></i> 密码长度不能少于6位';
        return;
    }
    if (pwd !== pwd2) {
        alert.style.display = 'block';
        alert.className = 'alert alert-error';
        alert.innerHTML = '<i class="ri-error-warning-line"></i> 两次输入的密码不一致';
        return;
    }

    alert.style.display = 'none';
    goToStep(3);
}

function addLog(msg, type) {
    const log = document.getElementById('installLog');
    const line = document.createElement('div');
    line.className = 'log-line' + (type ? ' ' + type : '');
    line.textContent = msg;
    log.appendChild(line);
    log.scrollTop = log.scrollHeight;
}

async function doInstall() {
    const btn = document.getElementById('btnInstall');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> 安装中...';

    document.getElementById('installProgress').style.display = 'block';
    document.getElementById('summaryPanel').style.display = 'none';

    addLog('正在初始化安装...');
    document.getElementById('progressFill').style.width = '20%';

    try {
        addLog('正在配置数据库...');
        document.getElementById('progressFill').style.width = '40%';

        const res = await fetch('/install', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                host: document.getElementById('db_host').value,
                port: document.getElementById('db_port').value,
                database: document.getElementById('db_database').value,
                db_username: document.getElementById('db_username').value,
                db_password: document.getElementById('db_password').value,
                site_name: document.getElementById('site_name').value,
                admin_name: document.getElementById('admin_name').value,
                admin_email: document.getElementById('admin_email').value,
                admin_password: document.getElementById('admin_password').value
            })
        });

        document.getElementById('progressFill').style.width = '70%';
        addLog('正在创建数据表和初始数据...');

        const data = await res.json();

        if (data.success) {
            document.getElementById('progressFill').style.width = '100%';
            addLog('安装完成！', 'success');

            document.getElementById('installProgress').style.display = 'none';
            document.getElementById('successPanel').style.display = 'block';
            document.getElementById('installActions').style.display = 'none';

            setTimeout(() => { window.location.href = '/login'; }, 3000);
        } else {
            addLog('安装失败: ' + (data.message || '未知错误'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-download-line"></i> 重新安装';
        }
    } catch (e) {
        addLog('请求失败: ' + e.message, 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-download-line"></i> 重新安装';
    }
}
</script>
</body>
</html>
