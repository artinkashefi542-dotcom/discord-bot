<?php session_start(); if(!isset($_SESSION['user_id'])){header('Location: index.php');exit;} require_once __DIR__.'/api/config.php'; $stmt=$pdo->prepare('SELECT first_name FROM users WHERE id=?'); $stmt->execute([$_SESSION['user_id']]); $chatUser=$stmt->fetch(); $firstName=$chatUser['first_name']??'کاربر'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>گروه‌ها | AK Fitness Pro</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--bg:#0a0e1a;--card:rgba(13,19,33,.85);--border:rgba(255,255,255,.06);--text:#e2e8f0;--dim:#64748b;--neon:#00d4ff;--green:#00e676;--input:rgba(255,255,255,.04)}
body{font-family:'Vazirmatn',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;padding-bottom:140px}
.container{max-width:500px;margin:0 auto;padding:16px}
h2{font-size:18px;font-weight:700;margin-bottom:12px;background:linear-gradient(135deg,var(--neon),#0077b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.card{background:var(--card);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:16px;padding:16px;margin-bottom:12px}
.action-btns{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
.action-btn{padding:14px;border-radius:14px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;transition:.2s;border:none}
.action-btn.create{background:linear-gradient(135deg,var(--neon),#0077b6);color:#fff;box-shadow:0 4px 20px rgba(0,212,255,.2)}
.action-btn.join{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--text)}
.action-btn:hover{transform:translateY(-1px)}
.group-card{background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:14px;padding:14px;margin-bottom:10px;cursor:pointer;transition:.2s}
.group-card:hover{border-color:rgba(0,212,255,.2);background:rgba(0,212,255,.03)}
.group-name{font-size:16px;font-weight:700;margin-bottom:4px}
.group-code{font-size:12px;color:var(--neon);font-family:monospace;background:rgba(0,212,255,.08);padding:2px 8px;border-radius:6px;display:inline-block}
.group-date{font-size:11px;color:var(--dim);margin-top:4px}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:200;display:none;align-items:flex-end;justify-content:center}
.modal-overlay.show{display:flex}
.modal{background:var(--card);border:1px solid var(--border);border-radius:20px 20px 0 0;width:100%;max-width:500px;max-height:80vh;overflow-y:auto;padding:20px}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.modal-header h3{font-size:16px;font-weight:600}
.modal-close{background:none;border:none;color:var(--dim);font-size:24px;cursor:pointer}
.modal input{width:100%;padding:12px;background:var(--input);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:inherit;font-size:14px;outline:none;margin-bottom:10px}
.modal input:focus{border-color:var(--neon)}
.btn{width:100%;padding:12px;border:none;border-radius:12px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;transition:.3s}
.btn-primary{background:linear-gradient(135deg,var(--neon),#0077b6);color:#fff;box-shadow:0 4px 20px rgba(0,212,255,.2)}
.btn-primary:hover{transform:translateY(-1px)}
.member-row{display:flex;justify-content:space-between;align-items:center;padding:10px;border-bottom:1px solid var(--border);transition:.2s}
.member-row:last-child{border-bottom:none}
.member-row:hover{background:rgba(0,212,255,.03);border-radius:8px}
.member-name{font-weight:600;font-size:14px}
.member-rank{font-size:12px;color:var(--dim)}
.member-stats{display:flex;gap:8px;font-size:11px;color:var(--dim)}
.member-stats span{background:rgba(255,255,255,.04);padding:2px 6px;border-radius:4px}
.rank-badge{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;margin-left:8px}
.rank-1{background:linear-gradient(135deg,#ffd700,#ff8c00);color:#000}
.rank-2{background:linear-gradient(135deg,#c0c0c0,#808080);color:#000}
.rank-3{background:linear-gradient(135deg,#cd7f32,#8b4513);color:#fff}
.rank-other{background:rgba(255,255,255,.05);color:var(--dim)}
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(10,14,26,.95);border-top:1px solid var(--border);display:flex;justify-content:space-around;padding:10px 0;z-index:90}
.nav-item span:first-child{font-size:20px}
@media(min-width:768px){body{padding-top:0}.bottom-nav{position:fixed;top:0;left:50%;transform:translateX(-50%);width:auto;border-bottom:1px solid var(--border);padding:4px 20px;border-radius:0 0 14px 14px;background:rgba(10,14,26,.95);display:flex;gap:4px;z-index:999}.container{padding-top:60px}.chat-fab{bottom:24px!important;left:24px;z-index:200}.nav-item{color:var(--dim)}.nav-item.active{color:var(--neon)}}
.nav-item{display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;color:var(--dim);font-size:11px;padding:4px 12px;border-radius:10px;transition:.3s}
.nav-item.active{color:var(--neon);background:rgba(0,212,255,.08)}
.nav-item span:first-child{font-size:20px}
.empty{text-align:center;padding:30px;color:var(--dim);font-size:13px}
</style>
</head>
<body>
<div class="container">
<h2>👥 گروه‌ها</h2>
<div class="action-btns">
<button class="action-btn create" onclick="document.getElementById('createModal').classList.add('show')">➕ ساخت گروه</button>
<button class="action-btn join" onclick="document.getElementById('joinModal').classList.add('show')">🔗 جوین شدن</button>
</div>
<div id="groupList"><div class="empty">⏳ در حال بارگذاری...</div></div>
</div>
<div class="modal-overlay" id="createModal"><div class="modal"><div class="modal-header"><h3>➕ ساخت گروه جدید</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">✕</button></div><input type="text" id="groupName" placeholder="نام گروه..."><button class="btn btn-primary" onclick="createGroup()">ساخت گروه</button></div></div>
<div class="modal-overlay" id="joinModal"><div class="modal"><div class="modal-header"><h3>🔗 جوین شدن به گروه</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">✕</button></div><input type="text" id="groupCode" placeholder="کد گروه را وارد کنید..." maxlength="6" style="text-align:center;font-size:20px;font-weight:700;letter-spacing:4px"><button class="btn btn-primary" onclick="joinGroup()">جوین شدن</button></div></div>
<div class="modal-overlay" id="detailModal"><div class="modal"><div class="modal-header"><h3 id="detailTitle">🏆 لیدربورد</h3><button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('show')">✕</button></div><div id="detailBody"></div></div></div>
<nav class="bottom-nav">
<a href="dashboard.php" class="nav-item"><span>🏠</span><span>خانه</span></a>
<a href="meals.php" class="nav-item"><span>🍽️</span><span>وعده‌ها</span></a>
<a href="groups.php" class="nav-item active"><span>👥</span><span>گروه‌ها</span></a>
</nav>
<?php include __DIR__.'/includes/chat_widget.php'; ?>
<script>
function loadGroups(){
fetch('api/groups.php?action=list').then(function(r){return r.json()}).then(function(d){
var wrap=document.getElementById('groupList');
if(!d.success||!d.data||!d.data.length){wrap.innerHTML='<div class="empty">هنوز عضو هیچ گروهی نیستید<br><small>گروه بسازید یا با کد جوین شوید</small></div>';return}
var h='';d.data.forEach(function(g){
h+='<div class="group-card" onclick="showDetail('+g.id+',\''+g.name+'\')"><div class="group-name">'+g.name+'</div><div class="group-code">🔑 '+g.code+'</div><div class="group-date">ساخته شده: '+g.created_at+'</div></div>';
});
wrap.innerHTML=h;
});
}
function createGroup(){
var name=document.getElementById('groupName').value.trim();
if(!name){ showToast('نام گروه را وارد کنید');return}
fetch('api/groups.php?action=create',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name:name})}).then(function(r){return r.json()}).then(function(d){
if(d.success){showToast('✅ گروه ساخته شد!\nکد گروه: '+d.data.code);document.getElementById('createModal').classList.remove('show');document.getElementById('groupName').value='';loadGroups();}
else showToast(d.message||'خطا',true);
});
}
function joinGroup(){
var code=document.getElementById('groupCode').value.trim().toUpperCase();
if(!code){ showToast('کد گروه را وارد کنید');return}
fetch('api/groups.php?action=join',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({code:code})}).then(function(r){return r.json()}).then(function(d){
if(d.success){showToast('✅ عضو شدید!');document.getElementById('joinModal').classList.remove('show');document.getElementById('groupCode').value='';loadGroups();}
else showToast(d.message||'خطا',true);
});
}
function showDetail(gid,name){
document.getElementById('detailTitle').textContent='🏆 '+name;
document.getElementById('detailBody').innerHTML='<div class="empty">⏳ در حال بارگذاری...</div>';
document.getElementById('detailModal').classList.add('show');
fetch('api/groups.php?action=detail&group_id='+gid).then(function(r){return r.json()}).then(function(d){
if(!d.success||!d.data||!d.data.members){document.getElementById('detailBody').innerHTML='<div class="empty">خطا</div>';return}
var members=d.data.members;var h='';
members.forEach(function(m,i){
var rank=i+1;var rankClass=rank<=3?'rank-'+rank:'rank-other';
var medals=['🥇','🥈','🥉'];var medal=rank<=3?medals[rank-1]:'#'+rank;
h+='<div class="member-row"><div style="display:flex;align-items:center"><div class="rank-badge '+rankClass+'">'+medal+'</div><div><div class="member-name">'+m.first_name+' '+m.last_name+'</div><div class="member-rank">@'+m.username+'</div></div></div><div class="member-stats"><span>🔥 '+Math.round(m.today.cal)+' kcal</span><span>🥩 '+Math.round(m.today.pro)+'g</span></div></div>';
});
if(!members.length)h='<div class="empty">عضوی نیست</div>';
document.getElementById('detailBody').innerHTML=h;
});
}
loadGroups();
</script>
</body></html>
