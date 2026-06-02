<!-- 首页弹窗公告 -->
<div id="notice-popup" class="notice-popup" style="display: none;">
    <div class="notice-popup-overlay"></div>
    <div class="notice-popup-content">
        <div class="notice-popup-header">
            <div class="notice-popup-icon">
                <i class="fas fa-bullhorn"></i>
            </div>
            <h3 class="notice-popup-title">系统公告</h3>
            <button class="notice-popup-close" onclick="closeNoticePopup()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notice-popup-body">
            <div class="notice-popup-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <span>加载中...</span>
            </div>
            <div class="notice-popup-content-area" style="display: none;">
                <!-- 公告内容将通过JS填充 -->
            </div>
        </div>
        <div class="notice-popup-footer">
            <label class="notice-popup-checkbox">
                <input type="checkbox" id="notice-popup-remember">
                <span class="checkmark"></span>
                <span>今日不再显示</span>
            </label>
            <button class="notice-popup-btn" onclick="closeNoticePopup()">
                我知道了
            </button>
        </div>
    </div>
</div>

<style>
/* 弹窗公告样式 */
.notice-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.notice-popup-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.notice-popup-content {
    position: relative;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.notice-popup-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.notice-popup-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.notice-popup-title {
    flex: 1;
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.notice-popup-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.notice-popup-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.notice-popup-body {
    padding: 24px;
    max-height: 50vh;
    overflow-y: auto;
}

.notice-popup-loading {
    text-align: center;
    color: #666;
    padding: 20px;
}

.notice-popup-loading i {
    font-size: 24px;
    margin-bottom: 8px;
    display: block;
    color: #667eea;
}

.notice-popup-content-area {
    line-height: 1.6;
    color: #333;
}

.notice-popup-content-area h1,
.notice-popup-content-area h2,
.notice-popup-content-area h3 {
    color: #667eea;
    margin-top: 0;
}

.notice-popup-content-area p {
    margin-bottom: 12px;
}

.notice-popup-content-area ul,
.notice-popup-content-area ol {
    margin-bottom: 12px;
    padding-left: 20px;
}

.notice-popup-content-area li {
    margin-bottom: 6px;
}

.notice-popup-content-area a {
    color: #667eea;
    text-decoration: none;
}

.notice-popup-content-area a:hover {
    text-decoration: underline;
}

.notice-popup-content-area .highlight {
    background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
    padding: 12px 16px;
    border-radius: 8px;
    margin: 12px 0;
    border-left: 4px solid #667eea;
}

.notice-popup-content-area .warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 12px 16px;
    border-radius: 8px;
    margin: 12px 0;
    color: #856404;
}

.notice-popup-content-area .success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 12px 16px;
    border-radius: 8px;
    margin: 12px 0;
    color: #155724;
}

.notice-popup-content-area .feature-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 16px 0;
}

.notice-popup-content-area .feature-item {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
}

.notice-popup-content-area .feature-item i {
    font-size: 24px;
    color: #667eea;
    margin-bottom: 8px;
    display: block;
}

.notice-popup-content-area .feature-item h4 {
    margin: 0 0 4px;
    font-size: 14px;
    color: #333;
}

.notice-popup-content-area .feature-item p {
    margin: 0;
    font-size: 12px;
    color: #666;
}

.notice-popup-footer {
    padding: 16px 24px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #eee;
}

.notice-popup-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    color: #666;
    user-select: none;
}

.notice-popup-checkbox input {
    display: none;
}

.notice-popup-checkbox .checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #ddd;
    border-radius: 4px;
    position: relative;
    transition: all 0.2s;
}

.notice-popup-checkbox input:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.notice-popup-checkbox input:checked + .checkmark::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 2px;
    width: 5px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.notice-popup-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.notice-popup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* 移动端适配 */
@media (max-width: 768px) {
    .notice-popup-content {
        width: 95%;
        max-height: 85vh;
        margin: 20px;
    }
    
    .notice-popup-header {
        padding: 16px 20px;
    }
    
    .notice-popup-title {
        font-size: 16px;
    }
    
    .notice-popup-body {
        padding: 16px 20px;
        max-height: 60vh;
    }
    
    .notice-popup-footer {
        padding: 12px 20px;
        flex-direction: column;
        gap: 12px;
    }
    
    .notice-popup-btn {
        width: 100%;
        padding: 12px;
    }
    
    .notice-popup-content-area .feature-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// 弹窗公告功能
let noticePopupData = null;

// 检查是否今日不再显示
function shouldShowNoticePopup() {
    const today = new Date().toDateString();
    const hiddenDate = localStorage.getItem('notice_popup_hidden');
    return hiddenDate !== today;
}

// 显示弹窗公告
async function showNoticePopup() {
    if (!shouldShowNoticePopup()) {
        console.log('今日不再显示弹窗公告');
        return;
    }
    
    try {
        const response = await fetch('/api/notices/popup');
        const data = await response.json();
        
        if (data.success && data.data) {
            noticePopupData = data.data;
            renderNoticePopup(noticePopupData);
            document.getElementById('notice-popup').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            console.log('没有需要显示的弹窗公告');
        }
    } catch (error) {
        console.error('获取弹窗公告失败:', error);
    }
}

// 渲染弹窗内容
function renderNoticePopup(notice) {
    const contentArea = document.querySelector('.notice-popup-content-area');
    const loading = document.querySelector('.notice-popup-loading');
    
    // 更新标题
    document.querySelector('.notice-popup-title').textContent = notice.title || '系统公告';
    
    // 更新图标
    const icon = document.querySelector('.notice-popup-icon i');
    if (notice.icon) {
        icon.className = notice.icon;
    }
    
    // 更新头部背景
    if (notice.bg_color) {
        document.querySelector('.notice-popup-header').style.background = notice.bg_color;
    }
    
    // 渲染HTML内容
    contentArea.innerHTML = notice.content;
    
    // 隐藏加载状态，显示内容
    loading.style.display = 'none';
    contentArea.style.display = 'block';
    
    // 处理图片点击放大
    contentArea.querySelectorAll('img').forEach(img => {
        img.style.cursor = 'pointer';
        img.style.maxWidth = '100%';
        img.style.borderRadius = '8px';
        img.onclick = function() {
            window.open(this.src, '_blank');
        };
    });
}

// 关闭弹窗
function closeNoticePopup() {
    const remember = document.getElementById('notice-popup-remember').checked;
    
    if (remember) {
        const today = new Date().toDateString();
        localStorage.setItem('notice_popup_hidden', today);
    }
    
    // 标记已读
    if (noticePopupData) {
        fetch(`/api/notices/${noticePopupData.id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).catch(error => console.error('标记已读失败:', error));
    }
    
    // 关闭弹窗
    const popup = document.getElementById('notice-popup');
    popup.style.animation = 'fadeOut 0.3s ease';
    
    setTimeout(() => {
        popup.style.display = 'none';
        document.body.style.overflow = '';
        popup.style.animation = '';
    }, 300);
}

// 点击遮罩层关闭
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('notice-popup-overlay')) {
        closeNoticePopup();
    }
});

// ESC键关闭
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNoticePopup();
    }
});

// 页面加载完成后显示弹窗
document.addEventListener('DOMContentLoaded', function() {
    // 延迟1秒显示，让页面先加载完成
    setTimeout(showNoticePopup, 1000);
});
</script>

<!-- 添加淡出动画 -->
<style>
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>