<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<title>VIP会员 - {{ config('app.name', 'B站播放器') }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0a0a0a;color:#e8e8e8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','PingFang SC','Hiragino Sans GB','Microsoft YaHei',sans-serif;min-height:100vh}
a{color:inherit;text-decoration:none}

/* 导航 */
.nav{position:fixed;top:0;left:0;right:0;height:60px;background:rgba(10,10,10,0.95);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;padding:0 24px;z-index:100}
.nav-logo{font-size:20px;font-weight:700;background:linear-gradient(135deg,#e6a817,#f0c040);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-right{margin-left:auto;display:flex;gap:16px;align-items:center}
.nav-link{font-size:14px;color:rgba(255,255,255,0.6);cursor:pointer;transition:color 0.2s}
.nav-link:hover{color:#e6a817}

/* 顶部VIP区域 */
.vip-header{padding:80px 20px 40px;text-align:center;background:linear-gradient(180deg,rgba(230,168,23,0.08) 0%,transparent 100%)}
.vip-badge{display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:20px;background:linear-gradient(135deg,#e6a817,#f0c040);color:#1a1a1a;font-weight:700;font-size:18px;margin-bottom:12px}
.vip-badge i{font-size:20px}
.vip-desc{color:rgba(255,255,255,0.5);font-size:14px}

/* 套餐卡片 */
.plans-section{padding:0 20px 30px;max-width:900px;margin:0 auto}
.section-title{font-size:18px;font-weight:600;margin-bottom:20px;text-align:center}
.plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}

.plan-card{background:rgba(255,255,255,0.04);border:2px solid rgba(255,255,255,0.08);border-radius:16px;padding:24px;cursor:pointer;transition:all 0.3s;position:relative;overflow:hidden}
.plan-card:hover{border-color:rgba(230,168,23,0.3);transform:translateY(-2px)}
.plan-card.selected{border-color:#e6a817;background:rgba(230,168,23,0.08)}
.plan-card.popular::before{content:'热门';position:absolute;top:12px;right:-28px;background:#e6a817;color:#1a1a1a;font-size:12px;padding:4px 32px;transform:rotate(45deg);font-weight:600}
.plan-level{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.plan-level-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px}
.plan-level-icon.gold{background:linear-gradient(135deg,#e6a817,#f0c040)}
.plan-level-icon.diamond{background:linear-gradient(135deg,#60a5fa,#3b82f6)}
.plan-level-icon.star{background:linear-gradient(135deg,#a78bfa,#7c3aed)}
.plan-level-name{font-size:16px;font-weight:600}
.plan-price{margin:12px 0}
.plan-price .amount{font-size:32px;font-weight:700;color:#e6a817}
.plan-price .amount .unit{font-size:14px;font-weight:400;color:rgba(255,255,255,0.5)}
.plan-price .original{font-size:13px;color:rgba(255,255,255,0.3);text-decoration:line-through;margin-left:8px}
.plan-price .daily{font-size:12px;color:rgba(255,255,255,0.4);margin-top:4px}
.plan-days{font-size:13px;color:rgba(255,255,255,0.5)}
.plan-features{margin-top:12px;display:flex;flex-direction:column;gap:6px}
.plan-feature{font-size:12px;color:rgba(255,255,255,0.5);display:flex;align-items:center;gap:6px}
.plan-feature i{color:#10b981;font-size:10px}

/* 支付弹窗 */
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);backdrop-filter:blur(8px);z-index:9999;justify-content:center;align-items:center;padding:20px}
.modal-overlay.show{display:flex}
.modal{background:#1a1a1a;border-radius:20px;width:100%;max-width:420px;overflow:hidden;animation:modalIn 0.3s ease}
@keyframes modalIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}
.modal-header{padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between;align-items:center}
.modal-title{font-size:18px;font-weight:600}
.modal-close{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.06);border:none;color:rgba(255,255,255,0.5);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.modal-close:hover{background:rgba(255,255,255,0.1)}
.modal-body{padding:24px}

/* 支付方式 */
.pay-method{display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;background:rgba(255,255,255,0.04);border:2px solid rgba(255,255,255,0.08);cursor:pointer;transition:all 0.2s}
.pay-method:hover{border-color:rgba(230,168,23,0.3);background:rgba(230,168,23,0.05)}
.pay-method.selected{border-color:#e6a817;background:rgba(230,168,23,0.08)}
.pay-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.pay-icon img{width:100%;height:100%;object-fit:cover}
.pay-icon.alipay{background:#1677ff}
.pay-icon.card{background:linear-gradient(135deg,#10b981,#059669)}
.pay-info{flex:1}
.pay-name{font-weight:600;font-size:15px}
.pay-desc{font-size:12px;color:rgba(255,255,255,0.4);margin-top:2px}
.pay-check{width:20px;height:20px;border-radius:50%;border:2px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center}
.pay-check.active{border-color:#e6a817;background:#e6a817}
.pay-check.active::after{content:'✓';font-size:12px;color:#1a1a1a;font-weight:700}

/* 二维码区域 */
.qr-section{text-align:center;padding:20px 0}
.qr-amount{font-size:14px;color:rgba(255,255,255,0.5);margin-bottom:16px}
.qr-amount .price{font-size:28px;font-weight:700;color:#e6a817}
.qr-box{display:inline-block;padding:16px;background:#fff;border-radius:12px;margin-bottom:12px}
.qr-box img{width:200px;height:200px;display:block}
.qr-tip{font-size:12px;color:rgba(255,255,255,0.4)}
.qr-timer{font-size:13px;color:#e6a817;margin-top:8px}

/* 卡密输入 */
.card-form{display:flex;flex-direction:column;gap:16px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-label{font-size:13px;color:rgba(255,255,255,0.6)}
.form-input{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:12px 16px;color:#e8e8e8;font-size:15px;outline:none;transition:border-color 0.2s}
.form-input:focus{border-color:#e6a817}
.form-input::placeholder{color:rgba(255,255,255,0.2)}

/* 按钮 */
.btn{padding:12px 24px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:all 0.2s;border:none;width:100%}
.btn-primary{background:linear-gradient(135deg,#e6a817,#f0c040);color:#1a1a1a}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(230,168,23,0.3)}
.btn-primary:disabled{opacity:0.5;cursor:not-allowed;transform:none;box-shadow:none}
.btn-cancel{background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.6);padding:12px 24px;border-radius:10px;font-size:15px;cursor:pointer;border:none;width:100%}
.btn-cancel:hover{background:rgba(255,255,255,0.1)}

/* 成功状态 */
.success-section{text-align:center;padding:30px 0}
.success-icon{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#fff}
.success-title{font-size:20px;font-weight:600;margin-bottom:8px}
.success-desc{font-size:14px;color:rgba(255,255,255,0.5);margin-bottom:20px}

/* VIP特权 */
.privileges-section{padding:20px;max-width:900px;margin:0 auto}
.privileges-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px}
.privilege-card{background:rgba(255,255,255,0.04);border-radius:12px;padding:16px;text-align:center}
.privilege-icon{font-size:24px;margin-bottom:8px}
.privilege-name{font-size:13px;font-weight:500}

/* 错误提示 */
.error-toast{position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;padding:10px 20px;border-radius:8px;font-size:14px;z-index:10000;display:none;animation:fadeInDown 0.3s}
@keyframes fadeInDown{from{opacity:0;transform:translate(-50%,-10px)}to{opacity:1;transform:translate(-50%,0)}}

/* 响应式 */
@media(max-width:640px){
  .plans-grid{grid-template-columns:1fr}
  .privileges-grid{grid-template-columns:repeat(3,1fr)}
  .plan-price .amount{font-size:28px}
}
</style>
</head>
<body>

<!-- 导航 -->
<nav class="nav">
  <a href="/" class="nav-logo">B站播放器</a>
  <div class="nav-right">
    <a href="/videos" class="nav-link">影视中心</a>
    <a href="/vip" class="nav-link" style="color:#e6a817">VIP会员</a>
    <a href="/account" class="nav-link" id="navUser">账号中心</a>
  </div>
</nav>

<!-- 顶部VIP区域 -->
<div class="vip-header">
  <div class="vip-badge">
    <i class="fas fa-crown"></i>
    <span>VIP会员</span>
  </div>
  <div class="vip-desc">尊享无广告体验 · 高清画质 · 专属特权</div>
</div>

<!-- VIP特权 -->
<div class="privileges-section">
  <div class="privileges-grid">
    <div class="privilege-card">
      <div class="privilege-icon">🚫</div>
      <div class="privilege-name">跳过前贴片</div>
    </div>
    <div class="privilege-card">
      <div class="privilege-icon">🎬</div>
      <div class="privilege-name">1080P画质</div>
    </div>
    <div class="privilege-card">
      <div class="privilege-icon">💬</div>
      <div class="privilege-name">彩色弹幕</div>
    </div>
    <div class="privilege-card">
      <div class="privilege-icon">📥</div>
      <div class="privilege-name">离线缓存</div>
    </div>
    <div class="privilege-card">
      <div class="privilege-icon">🎯</div>
      <div class="privilege-name">专属标识</div>
    </div>
    <div class="privilege-card">
      <div class="privilege-icon">⭐</div>
      <div class="privilege-name">成长加速</div>
    </div>
  </div>
</div>

<!-- 套餐选择 -->
<div class="plans-section">
  <div class="section-title">选择套餐</div>
  <div class="plans-grid" id="plansGrid">
    <!-- 动态加载 -->
  </div>
</div>

<!-- 支付弹窗 -->
<div class="modal-overlay" id="payModal">
  <div class="modal">
    <!-- 步骤1：选择支付方式 -->
    <div id="payStep1">
      <div class="modal-header">
        <div class="modal-title">选择支付方式</div>
        <button class="modal-close" onclick="closePayModal()">✕</button>
      </div>
      <div class="modal-body">
        <div style="display:flex;flex-direction:column;gap:12px;">
          <div class="pay-method" onclick="selectPayMethod('alipay')" id="payAlipay">
            <div class="pay-icon alipay">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                <path d="M21.422 15.358c-1.546-.678-3.226-1.398-4.968-2.148.582-1.398 1.032-2.904 1.332-4.476h-4.044V6.96h4.836V5.582h-4.836V2.132H12.22v3.45H7.384v1.378h4.836v1.774H7.896v1.378h7.152c-.252 1.158-.6 2.244-1.032 3.216-1.764-.744-3.468-1.458-5.1-2.112-2.16 1.218-3.78 2.796-4.8 4.686.96 1.68 2.58 3.06 4.74 4.02 1.56-.84 3.12-1.68 4.68-2.52 1.56.84 3.12 1.68 4.68 2.52 2.16-.96 3.78-2.34 4.74-4.02-.534-.96-1.2-1.836-1.98-2.622zM9.124 19.898c-1.68-.78-2.94-1.86-3.66-3.12.72-1.26 1.98-2.34 3.66-3.12 1.56.72 3.12 1.44 4.68 2.16-.42 1.08-.96 2.1-1.62 3.06-1.56-.72-3.12-1.44-4.68-2.16.12.06.24.12.36.18l-.74.24.36.06z"/>
              </svg>
            </div>
            <div class="pay-info">
              <div class="pay-name">支付宝当面付</div>
              <div class="pay-desc">扫码支付，即时到账</div>
            </div>
            <div class="pay-check" id="payAlipayCheck"></div>
          </div>

          <div class="pay-method" onclick="selectPayMethod('card')" id="payCard">
            <div class="pay-icon card">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
              </svg>
            </div>
            <div class="pay-info">
              <div class="pay-name">卡密兑换</div>
              <div class="pay-desc">使用卡号+卡密兑换VIP</div>
            </div>
            <div class="pay-check" id="payCardCheck"></div>
          </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:12px;">
          <button class="btn-cancel" onclick="closePayModal()">取消</button>
          <button class="btn btn-primary" id="payNextBtn" onclick="payNext()" disabled>下一步</button>
        </div>
      </div>
    </div>

    <!-- 步骤2A：支付宝二维码 -->
    <div id="payStep2Alipay" style="display:none">
      <div class="modal-header">
        <div class="modal-title">支付宝支付</div>
        <button class="modal-close" onclick="closePayModal()">✕</button>
      </div>
      <div class="modal-body">
        <div class="qr-section">
          <div class="qr-amount">应付金额 <span class="price" id="qrAmount">¥0</span></div>
          <div class="qr-box" id="qrBox">
            <img id="qrImage" src="" alt="支付宝二维码">
          </div>
          <div class="qr-tip">请使用支付宝扫码支付</div>
          <div class="qr-timer" id="qrTimer">支付剩余时间：14:59</div>
        </div>
        <div style="margin-top:20px;display:flex;gap:12px;">
          <button class="btn-cancel" onclick="backToStep1()">返回</button>
          <button class="btn btn-primary" onclick="queryPayResult()">我已完成支付</button>
        </div>
      </div>
    </div>

    <!-- 步骤2B：卡密输入 -->
    <div id="payStep2Card" style="display:none">
      <div class="modal-header">
        <div class="modal-title">卡密兑换</div>
        <button class="modal-close" onclick="closePayModal()">✕</button>
      </div>
      <div class="modal-body">
        <div class="card-form">
          <div class="form-group">
            <label class="form-label">卡号</label>
            <input type="text" class="form-input" id="cardNo" placeholder="请输入卡号" maxlength="20">
          </div>
          <div class="form-group">
            <label class="form-label">卡密</label>
            <input type="password" class="form-input" id="cardPwd" placeholder="请输入卡密" maxlength="20">
          </div>
        </div>
        <div style="margin-top:20px;display:flex;gap:12px;">
          <button class="btn-cancel" onclick="backToStep1()">返回</button>
          <button class="btn btn-primary" onclick="redeemCard()">立即兑换</button>
        </div>
      </div>
    </div>

    <!-- 步骤3：支付成功 -->
    <div id="payStep3" style="display:none">
      <div class="modal-header">
        <div class="modal-title">支付成功</div>
        <button class="modal-close" onclick="closePayModal()">✕</button>
      </div>
      <div class="modal-body">
        <div class="success-section">
          <div class="success-icon">✓</div>
          <div class="success-title">恭喜，VIP已开通！</div>
          <div class="success-desc" id="successDesc">您的VIP有效期至 2026-07-03</div>
          <button class="btn btn-primary" onclick="closePayModal();location.reload();">确定</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 错误提示 -->
<div class="error-toast" id="errorToast"></div>

<script>
const API = '/api';
let plans = [];
let selectedPlan = null;
let selectedPayMethod = null;
let currentOrderNo = null;
let qrTimerInterval = null;

// 检查登录状态
function getToken() { return localStorage.getItem('token'); }
function getUser() { try { return JSON.parse(localStorage.getItem('user')); } catch(e) { return null; } }

// 显示错误提示
function showError(msg) {
  const toast = document.getElementById('errorToast');
  toast.textContent = msg;
  toast.style.display = 'block';
  setTimeout(() => toast.style.display = 'none', 3000);
}

// API请求
async function apiGet(path) {
  const token = getToken();
  const headers = token ? { 'Authorization': 'Bearer ' + token } : {};
  const res = await fetch(API + path, { headers });
  return res.json();
}

async function apiPost(path, data) {
  const token = getToken();
  const headers = { 'Content-Type': 'application/json' };
  if (token) headers['Authorization'] = 'Bearer ' + token;
  const res = await fetch(API + path, { method: 'POST', headers, body: JSON.stringify(data) });
  return res.json();
}

// 加载套餐
async function loadPlans() {
  const data = await apiGet('/vip/plans');
  if (data.success) {
    plans = data.data || data.plans;
    renderPlans();
  }
}

// 渲染套餐
function renderPlans() {
  const grid = document.getElementById('plansGrid');
  const levelIcons = { 1: 'gold', 2: 'diamond', 3: 'star' };
  const levelNames = { 1: '黄金VIP', 2: '钻石VIP', 3: '星钻VIP' };
  const levelEmoji = { 1: '👑', 2: '💎', 3: '⭐' };
  
  grid.innerHTML = plans.map(plan => `
    <div class="plan-card ${plan.is_popular ? 'popular' : ''}" onclick="selectPlan(${plan.id})" id="plan${plan.id}">
      <div class="plan-level">
        <div class="plan-level-icon ${levelIcons[plan.level]}">${levelEmoji[plan.level]}</div>
        <div class="plan-level-name">${levelNames[plan.level]}</div>
      </div>
      <div class="plan-price">
        <span class="amount">¥${plan.price}<span class="unit">/${plan.duration_days}天</span></span>
        ${plan.sale_price ? `<span class="original">¥${plan.sale_price}</span>` : ''}
        <div class="daily">约¥${(plan.price / plan.duration_days).toFixed(2)}/天</div>
      </div>
      <div class="plan-features">
        ${plan.features ? (typeof plan.features === 'string' ? JSON.parse(plan.features) : plan.features).map(f => `<div class="plan-feature"><i class="fas fa-check"></i>${f}</div>`).join('') : ''}
      </div>
    </div>
  `).join('');
}

// 选择套餐
function selectPlan(planId) {
  selectedPlan = plans.find(p => p.id === planId);
  document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
  document.getElementById('plan' + planId).classList.add('selected');
  openPayModal();
}

// 打开支付弹窗
function openPayModal() {
  if (!getToken()) {
    showError('请先登录');
    setTimeout(() => window.location.href = '/login', 1500);
    return;
  }
  selectedPayMethod = null;
  document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
  document.querySelectorAll('.pay-check').forEach(c => c.classList.remove('active'));
  document.getElementById('payNextBtn').disabled = true;
  document.getElementById('payModal').classList.add('show');
  showStep('payStep1');
}

// 关闭支付弹窗
function closePayModal() {
  document.getElementById('payModal').classList.remove('show');
  if (qrTimerInterval) clearInterval(qrTimerInterval);
  selectedPayMethod = null;
  currentOrderNo = null;
}

// 返回步骤1
function backToStep1() {
  showStep('payStep1');
  if (qrTimerInterval) clearInterval(qrTimerInterval);
}

// 显示步骤
function showStep(stepId) {
  ['payStep1', 'payStep2Alipay', 'payStep2Card', 'payStep3'].forEach(id => {
    document.getElementById(id).style.display = id === stepId ? 'block' : 'none';
  });
}

// 选择支付方式
function selectPayMethod(method) {
  selectedPayMethod = method;
  document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
  document.querySelectorAll('.pay-check').forEach(c => c.classList.remove('active'));
  document.getElementById(method === 'alipay' ? 'payAlipay' : 'payCard').classList.add('selected');
  document.getElementById(method === 'alipay' ? 'payAlipayCheck' : 'payCardCheck').classList.add('active');
  document.getElementById('payNextBtn').disabled = false;
}

// 下一步
function payNext() {
  if (!selectedPayMethod || !selectedPlan) return;
  if (selectedPayMethod === 'alipay') {
    startAlipay();
  } else {
    showStep('payStep2Card');
  }
}

// 开始支付宝支付
async function startAlipay() {
  const data = await apiPost('/payment/alipay/create', { plan_id: selectedPlan.id });
  if (data.error) {
    showError(data.error);
    return;
  }
  currentOrderNo = data.order_no;
  document.getElementById('qrAmount').textContent = '¥' + data.amount;
  document.getElementById('qrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(data.qr_url);
  showStep('payStep2Alipay');
  startQrTimer();
}

// 二维码倒计时
function startQrTimer() {
  let seconds = 899; // 15分钟
  if (qrTimerInterval) clearInterval(qrTimerInterval);
  qrTimerInterval = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
      clearInterval(qrTimerInterval);
      showError('支付超时，请重新下单');
      backToStep1();
      return;
    }
    const min = Math.floor(seconds / 60);
    const sec = seconds % 60;
    document.getElementById('qrTimer').textContent = `支付剩余时间：${min}:${sec.toString().padStart(2, '0')}`;
  }, 1000);
}

// 查询支付结果
async function queryPayResult() {
  if (!currentOrderNo) return;
  const data = await apiPost('/payment/alipay/query', { order_no: currentOrderNo });
  if (data.status === 'paid') {
    showSuccess(data.vip_expired_at);
  } else {
    showError('尚未检测到支付，请稍后再试');
  }
}

// 卡密兑换
async function redeemCard() {
  const cardNo = document.getElementById('cardNo').value.trim();
  const cardPwd = document.getElementById('cardPwd').value.trim();
  if (!cardNo || !cardPwd) {
    showError('请输入卡号和卡密');
    return;
  }
  const data = await apiPost('/payment/card/redeem', { card_no: cardNo, card_password: cardPwd });
  if (data.error) {
    showError(data.error);
    return;
  }
  showSuccess(data.vip_expired_at);
}

// 显示成功
function showSuccess(expiredAt) {
  if (qrTimerInterval) clearInterval(qrTimerInterval);
  document.getElementById('successDesc').textContent = expiredAt 
    ? `您的VIP有效期至 ${expiredAt.split(' ')[0]}` 
    : 'VIP已成功开通';
  showStep('payStep3');
}

// 初始化
loadPlans();

// 导航栏用户状态
const user = getUser();
if (user) {
  document.getElementById('navUser').textContent = user.nickname || user.username || '账号中心';
}
</script>

</body>
</html>
