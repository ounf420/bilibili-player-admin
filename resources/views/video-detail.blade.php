<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>视频详情 - DPlayer</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--bg-dark:#1a1a2e;--bg-page:#f4f5f7;--bg-card:#fff;--green:#00be06;--green-dark:#00a305;--gold:#f5a623;--accent:#fb7299;--text:#222;--text-sec:#666;--text-muted:#999;--border:#e8e8e8;--radius:8px;--radius-lg:12px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','PingFang SC','Microsoft YaHei',sans-serif;background:var(--bg-page);color:var(--text);line-height:1.6}
a{text-decoration:none;color:inherit}img{max-width:100%;display:block}

.nav{position:fixed;top:0;left:0;right:0;z-index:1000;background:var(--bg-dark);height:60px}
.nav-inner{max-width:1400px;margin:0 auto;padding:0 24px;height:100%;display:flex;align-items:center;gap:24px}
.nav-logo{display:flex;align-items:center;gap:8px;color:#fff;font-weight:700;font-size:20px;flex-shrink:0}
.nav-logo i{color:var(--green);font-size:22px}
.nav-links{display:flex;gap:4px}
.nav-links a{padding:8px 16px;border-radius:20px;font-size:14px;color:rgba(255,255,255,.7);transition:.2s}
.nav-links a:hover,.nav-links a.active{color:#fff;background:rgba(255,255,255,.1)}
.nav-right{display:flex;align-items:center;gap:12px;margin-left:auto}
.btn-vip{padding:6px 16px;border-radius:20px;font-size:12px;font-weight:600;background:linear-gradient(135deg,#ffd700,#ff8c00);color:#fff;border:none;cursor:pointer}
.avatar{width:32px;height:32px;border-radius:50%;border:2px solid var(--green);cursor:pointer;object-fit:cover}
.user-dd{position:relative}
.user-menu{display:none;position:absolute;top:100%;right:0;margin-top:8px;background:var(--bg-dark);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:8px;min-width:160px;z-index:100}
.user-dd:hover .user-menu{display:block}
.user-menu a{display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;font-size:13px;color:rgba(255,255,255,.7);transition:.2s}
.user-menu a:hover{background:rgba(255,255,255,.1);color:#fff}
.user-menu a.logout{color:var(--accent)}
.mobile-menu-btn{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer;padding:8px}

.detail-wrap{padding:76px 24px 40px;max-width:1400px;margin:0 auto}
.detail-header{display:flex;gap:32px;margin-bottom:32px}
.detail-poster{width:240px;flex-shrink:0}
.detail-poster img{width:100%;border-radius:var(--radius-lg);box-shadow:0 8px 24px rgba(0,0,0,.15)}
.detail-poster .ph{width:100%;padding-top:140%;border-radius:var(--radius-lg);background:linear-gradient(135deg,#667eea,#764ba2)}
.detail-info{flex:1}
.detail-title{font-size:28px;font-weight:800;margin-bottom:10px}
.detail-badges{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap}
.badge{padding:3px 10px;border-radius:4px;font-size:11px;font-weight:600}
.badge-cat{background:var(--bg-page);color:var(--green)}
.badge-vip1{background:linear-gradient(135deg,#f5a623,#ff6b35);color:#fff}
.badge-vip2{background:linear-gradient(135deg,#ffd700,#ff8c00);color:#fff}
.badge-q{background:#1a1a2e;color:#ffd700}
.badge-sc{background:#fef3c7;color:#d97706}
.badge-status{background:var(--bg-page);color:var(--text-muted)}
.detail-meta{display:flex;gap:20px;margin-bottom:14px;flex-wrap:wrap}
.detail-meta span{font-size:13px;color:var(--text-sec);display:flex;align-items:center;gap:4px}
.detail-meta i{color:var(--text-muted)}
.detail-desc{font-size:14px;color:var(--text-sec);line-height:1.8;margin-bottom:20px}
.detail-actions{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 24px;border-radius:24px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:.2s}
.btn-green{background:var(--green);color:#fff}
.btn-green:hover{background:var(--green-dark)}
.btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text-sec)}
.btn-ghost:hover{border-color:var(--green);color:var(--green)}
.btn-ghost.active{border-color:var(--green);color:var(--green);background:rgba(0,190,6,.05)}
.btn-ghost.liked{border-color:var(--accent);color:var(--accent);background:rgba(251,114,153,.05)}

.vip-banner{background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;display:none;align-items:center;gap:12px}
.vip-banner.show{display:flex}
.vip-banner i{font-size:28px;color:#ffd700}
.vip-banner-text{flex:1}
.vip-banner-text h4{color:#fff;font-size:14px}
.vip-banner-text p{color:rgba(255,255,255,.5);font-size:12px}

.info-card{background:var(--bg-card);border-radius:var(--radius);padding:20px;border:1px solid var(--border);margin-bottom:24px}
.info-card h3{font-size:16px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:6px}
.info-card h3 i{color:var(--green)}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.info-row{display:flex;gap:8px;font-size:13px}
.info-row .lbl{color:var(--text-muted);min-width:50px}
.info-row .val{font-weight:500}

.related-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px}
.rcard{cursor:pointer;transition:.2s}
.rcard:hover{transform:translateY(-2px)}
.rcard .poster{position:relative;border-radius:var(--radius);overflow:hidden;padding-top:140%;background:linear-gradient(135deg,#667eea,#764ba2)}
.rcard .poster img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.rcard .poster .tag-vip{position:absolute;top:6px;left:6px;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;background:linear-gradient(135deg,#ffd700,#ff8c00)}
.rcard .poster .tag-score{position:absolute;bottom:6px;left:6px;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;color:#ffd700;background:rgba(0,0,0,.7)}
.rcard .rtitle{margin-top:6px;font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rcard .rmeta{font-size:11px;color:var(--text-muted)}

.footer{background:var(--bg-dark);color:rgba(255,255,255,.5);padding:40px 24px 24px;margin-top:40px}
.footer-inner{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px}
.footer-brand{display:flex;align-items:center;gap:8px;color:#fff;font-weight:600;font-size:15px}
.footer-brand i{color:var(--green)}
.footer-links{display:flex;gap:20px}
.footer-links a{font-size:13px;color:rgba(255,255,255,.4)}
.footer-links a:hover{color:var(--green)}
.footer-copy{width:100%;text-align:center;padding-top:20px;margin-top:20px;border-top:1px solid rgba(255,255,255,.08);font-size:12px;color:rgba(255,255,255,.3)}

@media(max-width:768px){
.mobile-menu-btn{display:block}
.nav-links{display:none;position:absolute;top:60px;left:0;right:0;background:var(--bg-dark);flex-direction:column;padding:12px}
.nav-links.open{display:flex}
.detail-header{flex-direction:column;gap:16px}
.detail-poster{width:160px}
.detail-title{font-size:22px}
.info-grid{grid-template-columns:1fr}
.related-grid{grid-template-columns:repeat(auto-fill,minmax(140px,1fr))}
}
</style>
</head>
<body>

<nav class="nav"><div class="nav-inner">
<a href="/" class="nav-logo"><i class="fas fa-play-circle"></i><span>DPlayer</span></a>
<button class="mobile-menu-btn" onclick="document.getElementById('navL').classList.toggle('open')"><i class="fas fa-bars"></i></button>
<div class="nav-links" id="navL"><a href="/">首页</a><a href="/v">影视中心</a><a href="/player">播放器</a><a href="/vip">VIP会员</a></div>
<div class="nav-right" id="navUser"><a href="/login" style="color:rgba(255,255,255,.7);font-size:13px">登录</a></div>
</div></nav>

<div class="detail-wrap">
<div class="detail-header" id="header"></div>
<div class="vip-banner" id="vipBanner"><i class="fas fa-crown"></i><div class="vip-banner-text"><h4>此内容需要VIP会员</h4><p>开通VIP即可畅享全部高清内容</p></div><a href="/vip" class="btn btn-green" style="font-size:12px;padding:8px 18px"><i class="fas fa-crown"></i> 开通VIP</a></div>
<div class="info-card"><h3><i class="fas fa-info-circle"></i> 详细信息</h3><div class="info-grid" id="infoGrid"></div></div>
<div class="info-card"><h3><i class="fas fa-film"></i> 相似推荐</h3><div class="related-grid" id="relatedGrid"></div></div>
</div>

<footer class="footer"><div class="footer-inner">
<div class="footer-brand"><i class="fas fa-play-circle"></i> DPlayer 广告系统</div>
<div class="footer-links"><a href="/">首页</a><a href="/v">影视中心</a><a href="/vip">VIP会员</a><a href="/player">播放器</a></div>
<div class="footer-copy">© 2026 DPlayer 广告播放器管理系统</div>
</div></footer>

<script>
const vid='{{$videoId}}';
function fmt(n){if(!n)return'0';if(n>=10000)return(n/10000).toFixed(1)+'万';return n+''}
function fmtDur(s){if(!s)return'';return Math.floor(s/60)+':'+(s%60<10?'0':'')+(s%60)}
function getH(){const t=localStorage.getItem('token');const h={'Accept':'application/json'};if(t)h['Authorization']='Bearer '+t;return h}

function initNav(){
const t=localStorage.getItem('token'),u=localStorage.getItem('user'),el=document.getElementById('navUser');
if(t&&u){try{const j=JSON.parse(u);el.innerHTML=`<a href="/vip" class="btn-vip"><i class="fas fa-crown"></i> VIP</a><div class="user-dd"><img src="${j.avatar||'https://ui-avatars.com/api/?name='+encodeURIComponent(j.nickname||j.username)+'&background=00be06&color=fff&size=64'}" class="avatar"><div class="user-menu"><a href="/user"><i class="fas fa-user"></i>用户中心</a><a href="/account"><i class="fas fa-cog"></i>账号中心</a><a class="logout" href="javascript:logout()"><i class="fas fa-sign-out-alt"></i>退出</a></div></div>`}catch(e){}}
}
function logout(){const t=localStorage.getItem('token');if(t)fetch('/api/auth/logout',{method:'POST',headers:{'Authorization':'Bearer '+t}});localStorage.removeItem('token');localStorage.removeItem('user');location.reload()}

async function loadDetail(){
try{const r=await fetch('/api/movie/'+vid,{headers:getH()}),d=await r.json();if(!d.success){document.getElementById('header').innerHTML='<p style="color:red">视频不存在</p>';return}
const v=d.data.video,rel=d.data.related||[];
document.title=v.title+' - DPlayer';

const badges=[];
if(v.category)badges.push(`<span class="badge badge-cat">${v.category}</span>`);
if(v.vip_level==1)badges.push('<span class="badge badge-vip1">VIP</span>');
if(v.vip_level==2)badges.push('<span class="badge badge-vip2">SVIP</span>');
if(v.quality&&v.quality!=='SD')badges.push(`<span class="badge badge-q">${v.quality}</span>`);
if(v.score>0)badges.push(`<span class="badge badge-sc"><i class="fas fa-star"></i> ${v.score}</span>`);
badges.push(`<span class="badge badge-status">${v.is_ending?'已完结':'连载中'}</span>`);

document.getElementById('header').innerHTML=`
<div class="detail-poster">${v.cover?`<img src="${v.cover}">`:'<div class="ph"></div>'}</div>
<div class="detail-info">
<h1 class="detail-title">${v.title}</h1>
<div class="detail-badges">${badges.join('')}</div>
<div class="detail-meta">
<span><i class="fas fa-eye"></i> ${fmt(v.views)}次播放</span>
<span><i class="fas fa-heart"></i> ${fmt(v.likes)}赞</span>
${v.duration?`<span><i class="fas fa-clock"></i> ${fmtDur(v.duration)}</span>`:''}
${v.episode_count>1?`<span><i class="fas fa-list"></i> 共${v.episode_count}集</span>`:''}
</div>
<p class="detail-desc">${v.description||'暂无简介'}</p>
<div class="detail-actions">
<a href="javascript:void(0)" onclick="goPlay('${v.id}')" class="btn btn-green"><i class="fas fa-play"></i> 立即播放</a>
<button class="btn btn-ghost" id="btnFav" onclick="toggleFav()"><i class="far fa-heart"></i> 收藏</button>
<button class="btn btn-ghost" id="btnLike" onclick="toggleLike()"><i class="far fa-thumbs-up"></i> 点赞</button>
</div></div>`;

if(v.vip_level>0){const u=localStorage.getItem('user');if(u){const j=JSON.parse(u);if(j.vip_level<v.vip_level)document.getElementById('vipBanner').classList.add('show')}else document.getElementById('vipBanner').classList.add('show')}

const rows=[];
if(v.director)rows.push({l:'导演',v:v.director});if(v.actors)rows.push({l:'演员',v:v.actors});
if(v.region)rows.push({l:'地区',v:v.region});if(v.year)rows.push({l:'年份',v:v.year});
if(v.genre)rows.push({l:'类型',v:v.genre});if(v.language)rows.push({l:'语言',v:v.language});
if(v.tags)rows.push({l:'标签',v:v.tags});if(v.episode_count>1)rows.push({l:'集数',v:v.episode_count+'集'});
document.getElementById('infoGrid').innerHTML=rows.map(r=>`<div class="info-row"><span class="lbl">${r.l}</span><span class="val">${r.v}</span></div>`).join('');

if(rel.length)document.getElementById('relatedGrid').innerHTML=rel.map(r=>{
const vip=r.vip_level==2?'<span class="tag-vip">SVIP</span>':r.vip_level==1?'<span class="tag-vip">VIP</span>':'';
const sc=r.score>0?`<span class="tag-score"><i class="fas fa-star" style="font-size:8px"></i> ${r.score}</span>`:'';
return`<div class="rcard" onclick="location.href='/v/${r.id}'"><div class="poster"><img src="${r.cover||'https://via.placeholder.com/300x420/667eea/fff?text=Video'}" alt="${r.title}" loading="lazy">${vip}${sc}</div><div class="rtitle">${r.title}</div><div class="rmeta"><i class="fas fa-eye"></i> ${fmt(r.views)}</div></div>`}).join('');

checkFavLike()}catch(e){console.error(e)}}

async function checkFavLike(){const t=localStorage.getItem('token');if(!t)return;
try{const[fr,lr]=await Promise.all([
fetch('/api/favorites/check',{method:'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+t},body:JSON.stringify({video_id:vid})}),
fetch('/api/likes/check',{method:'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+t},body:JSON.stringify({video_id:vid})})
]);const fd=await fr.json(),ld=await lr.json();
if(fd.data?.favorited){document.getElementById('btnFav').classList.add('active');document.getElementById('btnFav').innerHTML='<i class="fas fa-heart"></i> 已收藏'}
if(ld.data?.liked){document.getElementById('btnLike').classList.add('liked');document.getElementById('btnLike').innerHTML='<i class="fas fa-thumbs-up"></i> 已点赞'}}catch(e){}}

async function toggleFav(){const t=localStorage.getItem('token');if(!t){location.href='/login';return}
try{const r=await fetch('/api/favorites/toggle',{method:'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+t},body:JSON.stringify({video_id:vid})}),d=await r.json();
const b=document.getElementById('btnFav');if(d.data?.favorited){b.classList.add('active');b.innerHTML='<i class="fas fa-heart"></i> 已收藏'}else{b.classList.remove('active');b.innerHTML='<i class="far fa-heart"></i> 收藏'}}catch(e){}}

async function toggleLike(){const t=localStorage.getItem('token');if(!t){location.href='/login';return}
try{const r=await fetch('/api/likes/toggle',{method:'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+t},body:JSON.stringify({video_id:vid,type:1})}),d=await r.json();
const b=document.getElementById('btnLike');if(d.data?.liked){b.classList.add('liked');b.innerHTML='<i class="fas fa-thumbs-up"></i> 已点赞'}else{b.classList.remove('liked');b.innerHTML='<i class="far fa-thumbs-up"></i> 点赞'}}catch(e){}}

function genPlayUrl(id){const c='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';let t='';for(let i=0;i<16;i++)t+=c.charAt(Math.floor(Math.random()*c.length));return'/v/'+t+'-'+id+'.html'}
function goPlay(id){location.href=genPlayUrl(id)}
document.addEventListener('DOMContentLoaded',()=>{initNav();loadDetail()});
</script>
</body>
</html>
