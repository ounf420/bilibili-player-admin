<!-- 首页弹窗公告 -->
<div id="notice-popup" style="display:none;position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);" onclick="closeNoticePopup()"></div>
    <div id="notice-popup-card" style="position:relative;width:90%;max-width:480px;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,0.25);animation:noticeSlideUp 0.4s ease;">
        <!-- 头部 -->
        <div id="notice-popup-header" style="background:linear-gradient(135deg,#667eea,#764ba2);padding:28px 28px 24px;color:#fff;">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                <div id="notice-popup-icon" style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;">
                    <i class="fas fa-bell"></i>
                </div>
                <div style="flex:1;">
                    <div id="notice-popup-type" style="font-size:12px;opacity:0.8;margin-bottom:2px;">系统公告</div>
                    <h3 id="notice-popup-title" style="margin:0;font-size:20px;font-weight:700;">公告标题</h3>
                </div>
                <button onclick="closeNoticePopup()" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;transition:all 0.2s;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="notice-popup-time" style="font-size:12px;opacity:0.7;"></div>
        </div>
        <!-- 内容 -->
        <div id="notice-popup-body" style="padding:28px;max-height:50vh;overflow-y:auto;line-height:1.8;color:#333;font-size:15px;">
            <div id="notice-popup-loading" style="text-align:center;padding:30px;color:#999;">
                <i class="fas fa-spinner fa-spin" style="font-size:24px;color:#667eea;margin-bottom:12px;display:block;"></i>
                加载中...
            </div>
            <div id="notice-popup-content" style="display:none;"></div>
        </div>
        <!-- 底部 -->
        <div style="padding:20px 28px;background:#f8f9fa;border-top:1px solid #eee;display:flex;align-items:center;justify-content:space-between;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;color:#666;user-select:none;">
                <input type="checkbox" id="notice-popup-remember" style="display:none;">
                <span id="notice-checkbox-mark" style="width:18px;height:18px;border:2px solid #ddd;border-radius:4px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                    <i class="fas fa-check" style="font-size:10px;color:#fff;display:none;"></i>
                </span>
                今日不再显示
            </label>
            <button onclick="closeNoticePopup()" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;padding:12px 32px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 15px rgba(102,126,234,0.3);">
                我知道了
            </button>
        </div>
    </div>
</div>

<style>
@keyframes noticeSlideUp {
    from { opacity:0; transform:translateY(40px) scale(0.95); }
    to { opacity:1; transform:translateY(0) scale(1); }
}
@keyframes noticeFadeOut {
    from { opacity:1; transform:scale(1); }
    to { opacity:0; transform:scale(0.95); }
}

/* 内容样式 */
#notice-popup-content h1, #notice-popup-content h2, #notice-popup-content h3 {
    color:#667eea; margin-top:0;
}
#notice-popup-content p { margin-bottom:14px; }
#notice-popup-content ul, #notice-popup-content ol { margin-bottom:14px; padding-left:20px; }
#notice-popup-content li { margin-bottom:8px; }
#notice-popup-content a { color:#667eea; text-decoration:none; }
#notice-popup-content a:hover { text-decoration:underline; }
#notice-popup-content img { max-width:100%; border-radius:10px; margin:12px 0; cursor:pointer; }
#notice-popup-content blockquote {
    background:#f0f4ff; border-left:4px solid #667eea; padding:14px 18px;
    border-radius:0 10px 10px 0; margin:14px 0; color:#444;
}
#notice-popup-content code {
    background:#f1f5f9; padding:2px 8px; border-radius:4px; font-size:13px; color:#e11d48;
}
#notice-popup-content pre {
    background:#1e293b; color:#e2e8f0; padding:16px; border-radius:10px;
    overflow-x:auto; margin:14px 0;
}
#notice-popup-content pre code { background:none; color:inherit; }
</style>

<script>
let noticePopupData = null;

// 自定义checkbox
document.getElementById('notice-popup-remember').addEventListener('change', function() {
    const mark = document.getElementById('notice-checkbox-mark');
    const check = mark.querySelector('i');
    if (this.checked) {
        mark.style.background = '#667eea';
        mark.style.borderColor = '#667eea';
        check.style.display = 'block';
    } else {
        mark.style.background = 'transparent';
        mark.style.borderColor = '#ddd';
        check.style.display = 'none';
    }
});

function shouldShowNoticePopup() {
    const today = new Date().toDateString();
    return localStorage.getItem('notice_popup_hidden') !== today;
}

async function showNoticePopup() {
    if (!shouldShowNoticePopup()) return;
    
    try {
        const res = await fetch('/api/notices/popup');
        const data = await res.json();
        
        if (data.success && data.data) {
            noticePopupData = data.data;
            renderNoticePopup(noticePopupData);
            const popup = document.getElementById('notice-popup');
            popup.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    } catch (e) {
        console.error('获取公告失败:', e);
    }
}

function renderNoticePopup(notice) {
    const typeNames = {
        'system': '系统公告', 'update': '更新日志', 'activity': '活动公告',
        'maintenance': '维护通知', 'feature': '新功能', 'security': '安全提醒'
    };
    
    document.getElementById('notice-popup-title').textContent = notice.title;
    document.getElementById('notice-popup-type').textContent = typeNames[notice.type] || '公告';
    
    if (notice.icon) {
        document.getElementById('notice-popup-icon').innerHTML = `<i class="${notice.icon}"></i>`;
    }
    if (notice.bg_color) {
        document.getElementById('notice-popup-header').style.background = notice.bg_color;
    }
    if (notice.published_at) {
        document.getElementById('notice-popup-time').textContent = new Date(notice.published_at).toLocaleString('zh-CN');
    }
    
    document.getElementById('notice-popup-loading').style.display = 'none';
    const content = document.getElementById('notice-popup-content');
    content.innerHTML = notice.content;
    content.style.display = 'block';
    
    // 图片点击放大
    content.querySelectorAll('img').forEach(img => {
        img.onclick = () => window.open(img.src, '_blank');
    });
}

function closeNoticePopup() {
    const remember = document.getElementById('notice-popup-remember').checked;
    if (remember) {
        localStorage.setItem('notice_popup_hidden', new Date().toDateString());
    }
    
    if (noticePopupData) {
        fetch(`/api/notices/${noticePopupData.id}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }).catch(() => {});
    }
    
    const popup = document.getElementById('notice-popup');
    const card = document.getElementById('notice-popup-card');
    card.style.animation = 'noticeFadeOut 0.3s ease';
    
    setTimeout(() => {
        popup.style.display = 'none';
        document.body.style.overflow = '';
        card.style.animation = '';
    }, 300);
}

// ESC关闭
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeNoticePopup();
});

// 延迟显示
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(showNoticePopup, 800);
});
</script>
