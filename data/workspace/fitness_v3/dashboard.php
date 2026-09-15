<?php session_start(); if(!isset($_SESSION['user_id'])){header('Location: index.php');exit;} require_once __DIR__.'/api/config.php'; $stmt=$pdo->prepare('SELECT first_name FROM users WHERE id=?'); $stmt->execute([$_SESSION['user_id']]); $chatUser=$stmt->fetch(); $firstName=$chatUser['first_name']??'کاربر'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>خانه | AK Fitness Pro</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--bg:#0a0e1a;--card:rgba(13,19,33,.85);--border:rgba(255,255,255,.06);--text:#e2e8f0;--dim:#64748b;--neon:#00d4ff;--green:#00e676;--orange:#ff9800;--pink:#ff4081;--red:#ff4d6a;--input:rgba(255,255,255,.04)}
body{font-family:'Vazirmatn',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;padding-bottom:140px}
.container{max-width:500px;margin:0 auto;padding:16px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.header-left{display:flex;align-items:center;gap:12px}
.avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--neon),#0077b6);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff}
.greeting{font-size:18px;font-weight:700}
.greeting span{color:var(--dim);font-size:11px;font-weight:400;display:block;margin-top:2px}
.header-btns{display:flex;gap:8px}
.btn-settings{background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.15);color:var(--neon);width:38px;height:38px;border-radius:10px;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s}
.btn-settings:hover{background:rgba(0,212,255,.15);transform:rotate(60deg)}
.btn-logout{background:rgba(255,77,106,.08);border:1px solid rgba(255,77,106,.15);color:var(--red);width:38px;height:38px;border-radius:10px;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s}
.btn-logout:hover{background:rgba(255,77,106,.15)}
.card{background:var(--card);;border:1px solid var(--border);border-radius:16px;padding:16px;margin-bottom:12px}
.rings-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:8px 0 20px}
.ring-card{text-align:center;padding:20px 4px 14px;border-radius:16px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05)}
.ring-card:hover{border-color:rgba(255,255,255,.1)}
.ring-svg{width:64px;height:64px;margin:0 auto 6px;display:block}
.ring-svg circle{fill:none;stroke-linecap:round}
.ring-bg{stroke:rgba(255,255,255,.04);stroke-width:5}
.ring-fg{stroke-width:5;transition:stroke-dashoffset 1.5s cubic-bezier(.4,0,.2,1)}
.ring-val{font-size:15px;font-weight:700;line-height:1;margin-bottom:2px}
.ring-label{font-size:10px;color:var(--dim);font-weight:400}
.ring-sub{font-size:9px;color:var(--dim);opacity:.6;margin-top:1px}
.meal-card{background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:12px;padding:12px;margin-bottom:8px;cursor:pointer;transition:.2s}
.meal-card:hover{border-color:rgba(0,212,255,.15)}
.meal-header{display:flex;justify-content:space-between;align-items:center}
.meal-type{font-weight:600;font-size:14px}
.meal-macros{font-size:11px;color:var(--dim)}
.meal-body{display:none;padding-top:8px;border-top:1px solid var(--border);margin-top:8px}
.meal-body.show{display:block}
.meal-food{display:flex;justify-content:space-between;padding:3px 0;font-size:12px;color:var(--dim)}
.meal-food .name{color:var(--text)}
.week-chart{display:flex;align-items:flex-end;gap:6px;height:120px;padding:10px 0}
.week-bar{flex:1;display:flex;flex-direction:column;align-items:center;gap:2px}
.bar-wrap{width:100%;display:flex;gap:2px;align-items:flex-end;height:90px}
.bar{flex:1;border-radius:4px 4px 0 0;transition:height .8s ease;min-height:2px}
.bar.cal{background:linear-gradient(to top,var(--neon),rgba(0,212,255,.3))}
.bar.pro{background:linear-gradient(to top,var(--green),rgba(0,230,118,.3))}
.week-label{font-size:9px;color:var(--dim)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);;z-index:200;display:none;align-items:flex-end;justify-content:center}
.modal-overlay.show{display:flex}
.modal{background:var(--card);border:1px solid var(--border);border-radius:20px 20px 0 0;width:100%;max-width:500px;max-height:85vh;overflow-y:auto;padding:20px}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.modal-header h3{font-size:16px;font-weight:600}
.modal-close{background:none;border:none;color:var(--dim);font-size:24px;cursor:pointer}
.form-group{margin-bottom:12px}
.form-group label{display:block;font-size:12px;color:var(--dim);margin-bottom:4px}
.form-group input,.form-group select{width:100%;padding:10px;background:var(--input);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:inherit;font-size:14px;outline:none}
.form-group input:focus,.form-group select:focus{border-color:var(--neon)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.save-btn{width:100%;padding:12px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--neon),#0077b6);color:#fff;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;margin-top:8px}
.save-btn:hover{transform:translateY(-1px)}
h2{font-size:15px;font-weight:600;margin:16px 0 10px;color:var(--dim)}
.empty{text-align:center;padding:20px;color:var(--dim);font-size:13px}
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(10,14,26,.95);border-top:1px solid var(--border);display:flex;justify-content:space-around;padding:10px 0;z-index:90}
.nav-item span:first-child{font-size:20px}
@media(min-width:768px){body{padding-top:0}.bottom-nav{position:fixed;top:0;left:50%;transform:translateX(-50%);width:auto;border-bottom:1px solid var(--border);padding:4px 20px;border-radius:0 0 14px 14px;background:rgba(10,14,26,.95);display:flex;gap:4px;z-index:999}.container{padding-top:60px}.chat-fab{bottom:24px!important;left:24px;z-index:200}.nav-item{color:var(--dim)}.nav-item.active{color:var(--neon)}}
.nav-item{display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;color:var(--dim);font-size:11px;padding:4px 12px;border-radius:10px;transition:.3s}
.nav-item.active{color:var(--neon);background:rgba(0,212,255,.08)}
.nav-item span:first-child{font-size:20px}
</style>
</head>
<body>
<div class="container">
<div class="header">
<div class="header-left">
<div class="avatar" id="avatarEl">?</div>
<div class="greeting"><span id="dateEl"></span>سلام <span id="nameEl">...</span> 👋</div>
</div>
<div class="header-btns">
<button class="btn-settings" onclick="openSettings()" title="تنظیمات">⚙️</button>
<button class="btn-logout" onclick="fetch('api/auth.php?action=logout').then(function(){location.href='index.php'})">🚪</button>
</div>
</div>

<div class="rings-row">
<div class="ring-card"><svg class="ring-svg" viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" id="calRing" cx="18" cy="18" r="15.9" stroke="#00d4ff" style="--c:#00d4ff" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 18 18)"/></svg><div class="ring-val" id="calVal" style="color:#00d4ff">0</div><div class="ring-label">🔥 کالری</div><div class="ring-sub" id="calSub">0/0</div></div>
<div class="ring-card"><svg class="ring-svg" viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" id="proRing" cx="18" cy="18" r="15.9" stroke="#00e676" style="--c:#00e676" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 18 18)"/></svg><div class="ring-val" id="proVal" style="color:#00e676">0</div><div class="ring-label">🥩 پروتئین</div><div class="ring-sub" id="proSub">0/0</div></div>
<div class="ring-card"><svg class="ring-svg" viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" id="carbRing" cx="18" cy="18" r="15.9" stroke="#ff9800" style="--c:#ff9800" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 18 18)"/></svg><div class="ring-val" id="carbVal" style="color:#ff9800">0</div><div class="ring-label">🌾 کربو</div><div class="ring-sub" id="carbSub">0/0</div></div>
<div class="ring-card"><svg class="ring-svg" viewBox="0 0 36 36"><circle class="ring-bg" cx="18" cy="18" r="15.9"/><circle class="ring-fg" id="fatRing" cx="18" cy="18" r="15.9" stroke="#ff4081" style="--c:#ff4081" stroke-dasharray="100" stroke-dashoffset="100" transform="rotate(-90 18 18)"/></svg><div class="ring-val" id="fatVal" style="color:#ff4081">0</div><div class="ring-label">🫒 چربی</div><div class="ring-sub" id="fatSub">0/0</div></div>
</div>

<h2>🍽️ وعده‌های امروز</h2>
<div id="todayMeals"><div class="empty">⏳ در حال بارگذاری...</div></div>

<h2>📊 نمودار هفتگی</h2>
<div class="card">
<div class="week-chart" id="weekChart"></div>
<div style="display:flex;justify-content:center;gap:16px;margin-top:4px"><span style="font-size:10px;color:var(--dim)">🔵 کالری</span><span style="font-size:10px;color:var(--dim)">🟢 پروتئین</span></div>
</div>
</div>

<nav class="bottom-nav">
<a href="dashboard.php" class="nav-item active"><span>🏠</span><span>خانه</span></a>
<a href="meals.php" class="nav-item"><span>🍽️</span><span>وعده‌ها</span></a>
<a href="groups.php" class="nav-item"><span>👥</span><span>گروه‌ها</span></a>
</nav>

<div class="modal-overlay" id="settingsModal">
<div class="modal">
<div class="modal-header"><h3>⚙️ تنظیمات پروفایل</h3><button class="modal-close" onclick="document.getElementById('settingsModal').classList.remove('show')">✕</button></div>
<div class="form-row">
<div class="form-group"><label>نام</label><input type="text" id="sFirstName"></div>
<div class="form-group"><label>نام خانوادگی</label><input type="text" id="sLastName"></div>
</div>
<div class="form-row">
<div class="form-group"><label>سن</label><input type="number" id="sAge" min="10" max="100"></div>
<div class="form-group"><label>جنسیت</label><select id="sGender"><option value="male">مرد</option><option value="female">زن</option></select></div>
</div>
<div class="form-row">
<div class="form-group"><label>وزن (کیلو)</label><input type="number" id="sWeight" step="0.1" min="20" max="300"></div>
<div class="form-group"><label>قد (سانتی‌متر)</label><input type="number" id="sHeight" min="100" max="250"></div>
</div>
<hr style="border-color:var(--border);margin:12px 0">
<div style="font-size:13px;font-weight:600;margin-bottom:10px">🎯 اهداف روزانه</div>
<div class="form-row">
<div class="form-group"><label>🔥 کالری</label><input type="number" id="sCalories" min="500" max="10000"></div>
<div class="form-group"><label>🥩 پروتئین (گرم)</label><input type="number" id="sProtein" min="10" max="500"></div>
</div>
<div class="form-row">
<div class="form-group"><label>🌾 کربوهیدرات (گرم)</label><input type="number" id="sCarbs" min="10" max="1000"></div>
<div class="form-group"><label>🫒 چربی (گرم)</label><input type="number" id="sFat" min="10" max="500"></div>
</div>
<button class="save-btn" onclick="saveSettings()">💾 ذخیره تنظیمات</button>
</div>
</div>

<?php include __DIR__.'/includes/chat_widget.php'; ?>

<script>
var persianDays=['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
function setRing(id,pct){var el=document.getElementById(id);if(el)el.style.strokeDashoffset=100-Math.min(pct,100)}

function loadData(){
fetch('api/dashboard.php').then(function(r){return r.json()}).then(function(d){
if(!d.success||!d.data){location.href='index.php';return}
var u=d.data.user,t=d.data.totals||{};
document.getElementById('nameEl').textContent=u.first_name||u.username;
document.getElementById('avatarEl').textContent=(u.first_name||'?')[0];
var now=new Date();
document.getElementById('dateEl').textContent=persianDays[now.getDay()]+' '+now.getDate()+'/'+(now.getMonth()+1);

var goals={cal:u.daily_calories||2000,pro:u.daily_protein||120,carb:u.daily_carbs||250,fat:u.daily_fat||65};
var consumed={cal:t.cal||0,pro:t.pro||0,carb:t.carb||0,fat:t.fat||0};

document.getElementById('calVal').textContent=Math.round(consumed.cal);
document.getElementById('calSub').textContent=Math.round(consumed.cal)+'/'+goals.cal;
setRing('calRing',(consumed.cal/goals.cal)*100);

document.getElementById('proVal').textContent=Math.round(consumed.pro);
document.getElementById('proSub').textContent=Math.round(consumed.pro)+'/'+goals.pro;
setRing('proRing',(consumed.pro/goals.pro)*100);

document.getElementById('carbVal').textContent=Math.round(consumed.carb);
document.getElementById('carbSub').textContent=Math.round(consumed.carb)+'/'+goals.carb;
setRing('carbRing',(consumed.carb/goals.carb)*100);

document.getElementById('fatVal').textContent=Math.round(consumed.fat);
document.getElementById('fatSub').textContent=Math.round(consumed.fat)+'/'+goals.fat;
setRing('fatRing',(consumed.fat/goals.fat)*100);

var icons={'صبحانه':'🌅','ناهار':'☀️','شام':'🌙','میان‌وعده':'🍪'};
var mw=document.getElementById('todayMeals');
var meals=d.data.meals||[];
if(!meals.length){mw.innerHTML='<div class="empty">هیچ وعده‌ای ثبت نشده</div>';}
else{var mh='';meals.forEach(function(m){mh+='<div class="meal-card" onclick="this.querySelector(\'.meal-body\').classList.toggle(\'show\')"><div class="meal-header"><span class="meal-type">'+(icons[m.meal_type]||'🍽️')+' '+m.meal_type+'</span><span class="meal-macros">'+Math.round(m.mcal||0)+' kcal</span></div><div class="meal-body">';if(m.foods)m.foods.forEach(function(f){mh+='<div class="meal-food"><span class="name">'+f.food_name+'</span><span>'+Math.round(f.calories)+' kcal | '+Math.round(f.protein)+'g</span></div>'});mh+='</div></div>'});mw.innerHTML=mh;}

var week=d.data.week||[];
var maxCal=Math.max.apply(null,week.map(function(w){return w.calories||1}));
var wh='';week.forEach(function(w){
var ch=Math.max(((w.calories||0)/maxCal)*80,2);
var ph=Math.max(((w.protein||0)/(maxCal/10))*80,2);
var dObj=new Date(w.date);
var label=persianDays[dObj.getDay()].substring(0,3);
wh+='<div class="week-bar"><div class="bar-wrap"><div class="bar cal" style="height:'+ch+'px"></div><div class="bar pro" style="height:'+ph+'px"></div></div><div class="week-label">'+label+'</div></div>';
});
document.getElementById('weekChart').innerHTML=wh;
}).catch(function(){document.getElementById('todayMeals').innerHTML='<div class="empty">خطا در بارگذاری</div>'});
}

function openSettings(){
fetch('api/settings.php?action=get').then(function(r){return r.json()}).then(function(d){
if(!d.success||!d.data)return;
var u=d.data;
document.getElementById('sFirstName').value=u.first_name||'';
document.getElementById('sLastName').value=u.last_name||'';
document.getElementById('sAge').value=u.age||'';
document.getElementById('sGender').value=u.gender||'male';
document.getElementById('sWeight').value=u.weight||'';
document.getElementById('sHeight').value=u.height||'';
document.getElementById('sCalories').value=u.daily_calories||'';
document.getElementById('sProtein').value=u.daily_protein||'';
document.getElementById('sCarbs').value=u.daily_carbs||'';
document.getElementById('sFat').value=u.daily_fat||'';
document.getElementById('settingsModal').classList.add('show');
});
}

function saveSettings(){
var data={
first_name:document.getElementById('sFirstName').value.trim(),
last_name:document.getElementById('sLastName').value.trim(),
age:parseInt(document.getElementById('sAge').value)||0,
gender:document.getElementById('sGender').value,
weight:parseFloat(document.getElementById('sWeight').value)||0,
height:parseFloat(document.getElementById('sHeight').value)||0,
daily_calories:parseInt(document.getElementById('sCalories').value)||0,
daily_protein:parseInt(document.getElementById('sProtein').value)||0,
daily_carbs:parseInt(document.getElementById('sCarbs').value)||0,
daily_fat:parseInt(document.getElementById('sFat').value)||0
};
fetch('api/settings.php?action=update',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}).then(function(r){return r.json()}).then(function(d){
showToast('✅ تنظیمات ذخیره شد!');
document.getElementById('settingsModal').classList.remove('show');
loadData();
});
}

loadData();
</script>
</body></html>
