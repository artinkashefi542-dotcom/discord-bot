<?php session_start(); if(!isset($_SESSION['user_id'])){header('Location: index.php');exit;} require_once __DIR__.'/api/config.php'; $stmt=$pdo->prepare('SELECT first_name FROM users WHERE id=?'); $stmt->execute([$_SESSION['user_id']]); $chatUser=$stmt->fetch(); $firstName=$chatUser['first_name']??'کاربر'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>وعده‌ها | AK Fitness Pro</title>
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--bg:#0a0e1a;--card:rgba(13,19,33,.85);--border:rgba(255,255,255,.06);--text:#e2e8f0;--dim:#64748b;--neon:#00d4ff;--green:#00e676;--orange:#ff9800;--pink:#ff4081;--red:#ff4d6a;--input:rgba(255,255,255,.04)}
body{font-family:'Vazirmatn',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;padding-bottom:140px}
.container{max-width:500px;margin:0 auto;padding:16px}
h2{font-size:18px;font-weight:700;margin:16px 0 10px;background:linear-gradient(135deg,var(--neon),#0077b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.card{background:var(--card);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:16px;padding:16px;margin-bottom:12px}
.meal-type select{width:100%;padding:12px 14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;color:var(--text);font-family:inherit;font-size:15px;font-weight:500;outline:none;margin-bottom:12px;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b'%3E%3Cpath d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:14px center;cursor:pointer;transition:border-color .2s}
.meal-type select:focus{border-color:var(--neon)}
.food-rows{display:flex;flex-direction:column;gap:8px;margin-bottom:8px}
.food-row{background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:12px;padding:8px;display:grid;grid-template-columns:1fr 60px auto auto;gap:6px;align-items:center;animation:slideIn .2s ease}
@keyframes slideIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.food-row input[type="text"]{padding:8px 10px;background:var(--input);border:1px solid var(--border);border-radius:8px;color:var(--text);font-family:inherit;font-size:13px;outline:none;width:100%}
.food-row input[type="text"]:focus{border-color:var(--neon)}
.food-row input[type="number"]{padding:8px;background:var(--input);border:1px solid var(--border);border-radius:8px;color:var(--text);font-family:inherit;font-size:13px;text-align:center;outline:none;width:60px}
.food-row input[type="number"]:focus{border-color:var(--neon)}
.search-btn{background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.2);color:var(--neon);padding:8px 10px;border-radius:8px;font-size:12px;cursor:pointer;transition:.2s;font-family:inherit;white-space:nowrap}
.search-btn:hover{background:rgba(0,212,255,.2)}
.search-btn:disabled{opacity:.5;cursor:wait}
.search-btn.done{background:rgba(0,230,118,.1);border-color:rgba(0,230,118,.2);color:var(--green)}
.search-btn.fail{background:rgba(255,77,106,.1);border-color:rgba(255,77,106,.2);color:var(--red)}
.remove-btn{background:rgba(255,77,106,.08);border:none;color:var(--red);width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:14px;transition:.2s;display:flex;align-items:center;justify-content:center}
.remove-btn:hover{background:rgba(255,77,106,.15)}
.search-result{margin-top:6px;border-radius:10px;overflow:hidden}
.search-result.ok{background:rgba(0,230,118,.05);border:1px solid rgba(0,230,118,.1)}
.search-result.err{background:rgba(255,77,106,.05);border:1px solid rgba(255,77,106,.1);color:var(--red);padding:8px 10px;font-size:12px;border-radius:10px}
.result-header{padding:8px 10px;font-size:12px;font-weight:600;display:flex;justify-content:space-between;align-items:center;cursor:pointer}
.result-header b{color:var(--text)}
.result-header span{color:var(--dim);font-size:10px}
.nut-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:4px;padding:0 8px 8px}
.nut-item{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.04);border-radius:8px;padding:8px 6px;text-align:center;transition:.2s;cursor:pointer}
.nut-item:hover{background:rgba(0,212,255,.06);border-color:rgba(0,212,255,.12)}
.nut-val{font-size:16px;font-weight:700;color:var(--neon)}
.nut-label{font-size:9px;color:var(--dim);margin-top:2px}
.nut-detail{display:none;padding:6px 10px 8px;font-size:11px;color:var(--dim);line-height:1.8;border-top:1px solid rgba(255,255,255,.04)}
.nut-detail.show{display:block}
.btn{width:100%;padding:12px;border:none;border-radius:12px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;transition:.3s}
.btn-primary{background:linear-gradient(135deg,var(--neon),#0077b6);color:#fff;box-shadow:0 4px 20px rgba(0,212,255,.2)}
.btn-primary:hover{transform:translateY(-1px)}
.btn-primary:disabled{opacity:.4;cursor:not-allowed;transform:none}
.btn-secondary{background:rgba(255,255,255,.05);border:1px solid var(--border);color:var(--text);margin-top:8px}
.meal-card{background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:12px;padding:12px;margin-bottom:8px;cursor:pointer;transition:.2s}
.meal-card:hover{border-color:rgba(0,212,255,.15)}
.meal-header{display:flex;justify-content:space-between;align-items:center}
.meal-header .type{font-weight:600;font-size:14px}
.meal-header .macros{font-size:11px;color:var(--dim)}
.meal-body{display:none;padding-top:8px;border-top:1px solid var(--border);margin-top:8px}
.meal-body.show{display:block}
.meal-food{display:flex;justify-content:space-between;padding:4px 0;font-size:12px;color:var(--dim)}
.meal-food .name{color:var(--text)}
.history-scroll{display:flex;gap:8px;overflow-x:auto;padding:4px 0;scrollbar-width:none}
.history-scroll::-webkit-scrollbar{display:none}
.day-card{min-width:100px;background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:10px;text-align:center;cursor:pointer;transition:.2s;flex-shrink:0}
.day-card:hover,.day-card.active{border-color:var(--neon);background:rgba(0,212,255,.05)}
.day-card .date{font-size:11px;color:var(--dim);margin-bottom:4px}
.day-card .cal{font-size:16px;font-weight:700;color:var(--neon)}
.day-card .pro{font-size:11px;color:var(--green)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:200;display:none;align-items:flex-end;justify-content:center}
.modal-overlay.show{display:flex}
.modal{background:var(--card);border:1px solid var(--border);border-radius:20px 20px 0 0;width:100%;max-width:500px;max-height:80vh;overflow-y:auto;padding:20px}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.modal-header h3{font-size:16px;font-weight:600}
.modal-close{background:none;border:none;color:var(--dim);font-size:24px;cursor:pointer}
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
<h2>🍽️ افزودن وعده</h2>
<div class="card">
<div class="meal-type">
<select id="mealType">
<option value="صبحانه">🌅 صبحانه</option>
<option value="ناهار" selected>☀️ ناهار</option>
<option value="شام">🌙 شام</option>
<option value="میان‌وعده">🍪 میان‌وعده</option>
</select>
</div>
<div class="food-rows" id="foodRows"></div>
<button class="btn btn-secondary" onclick="addFoodRow()">➕ افزودن غذا</button>
<div style="margin-top:8px"><button class="btn btn-primary" id="submitBtn" onclick="submitMeal()" disabled>⚠️ ابتدا سرچ کنید</button></div>
</div>
<h2>📋 وعده‌های امروز</h2>
<div id="todayMeals"><div class="empty">⏳ در حال بارگذاری...</div></div>
<h2>📅 تاریخچه</h2>
<div class="card"><div class="history-scroll" id="historyScroll"></div></div>
</div>
<div class="modal-overlay" id="dayModal"><div class="modal"><div class="modal-header"><h3 id="modalTitle">📋 وعده‌ها</h3><button class="modal-close" onclick="closeModal()">✕</button></div><div id="modalBody"></div></div></div>
<nav class="bottom-nav">
<a href="dashboard.php" class="nav-item"><span>🏠</span><span>خانه</span></a>
<a href="meals.php" class="nav-item active"><span>🍽️</span><span>وعده‌ها</span></a>
<a href="groups.php" class="nav-item"><span>👥</span><span>گروه‌ها</span></a>
</nav>
<?php include __DIR__.'/includes/chat_widget.php'; ?>
<script>
var rowId=0,foodResults={};
function addFoodRow(){
rowId++;
var r=document.createElement('div');r.className='food-row';r.id='row-'+rowId;
r.innerHTML='<div class="autocomplete-wrap"><input type="text" class="food-input" placeholder="نام غذا و توضیحات..." data-id="'+rowId+'" oninput="searchFood(this)" onfocus="searchFood(this)"><div class="autocomplete-list" id="ac-'+rowId+'"></div></div><input type="number" class="grams-input" value="100" min="1" data-id="'+rowId+'" title="گرم"><button class="search-btn" id="sbtn-'+rowId+'" onclick="searchFoodAI('+rowId+')">🔍</button><button class="remove-btn" onclick="removeRow('+rowId+')">✕</button><div class="search-result" id="sres-'+rowId+'"></div>';
document.getElementById('foodRows').appendChild(r);
updateSubmitBtn();
}
function removeRow(id){var el=document.getElementById('row-'+id);if(el)el.remove();updateSubmitBtn()}
var searchTimer;
function searchFood(el){
var id=el.dataset.id,q=el.value.trim();
var ac=document.getElementById('ac-'+id);
if(q.length<1){if(ac)ac.classList.remove('show');return}
clearTimeout(searchTimer);
searchTimer=setTimeout(function(){
fetch('api/foods.php?action=search&q='+encodeURIComponent(q)).then(function(r){return r.json()}).then(function(d){
if(!d.success||!d.data||!d.data.length){if(ac)ac.classList.remove('show');return}
ac.innerHTML='';
d.data.forEach(function(f){
var div=document.createElement('div');div.className='autocomplete-item';
div.textContent=f.name+' ('+f.calories+' kcal/100g)';
div.onclick=function(){el.value=f.name;ac.classList.remove('show')};
ac.appendChild(div);
});
ac.classList.add('show');
});
},300);
}
async function searchFoodAI(id){
var row=document.getElementById('row-'+id);
var name=row.querySelector('.food-input').value.trim();
var grams=parseFloat(row.querySelector('.grams-input').value)||100;
if(!name){showToast('نام غذا را وارد کنید',true);return}
var btn=document.getElementById('sbtn-'+id);
var res=document.getElementById('sres-'+id);
btn.textContent='⏳';btn.disabled=true;res.innerHTML='';
try{
var r=await fetch('api/agent.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:name,grams:grams})});
var d=await r.json();
if(d.data&&d.data.status==='done'&&d.data.food_data){
var fd=d.data.food_data;
foodResults[id]=fd;
btn.textContent='✅';btn.className='search-btn done';
var gid='detail-'+id;
res.className='search-result ok';
res.innerHTML='<div class="result-header" onclick="document.getElementById(\''+gid+'\').classList.toggle(\'show\')"><b>✅ '+fd.food_name+' ('+fd.per_grams+'g)</b><span>⬇️ جزئیات</span></div><div class="nut-grid"><div class="nut-item"><div class="nut-val">'+Math.round(fd.calories)+'</div><div class="nut-label">🔥 کالری</div></div><div class="nut-item"><div class="nut-val">'+Math.round(fd.protein)+'</div><div class="nut-label">🥩 پروتئین</div></div><div class="nut-item"><div class="nut-val">'+Math.round(fd.carbs)+'</div><div class="nut-label">🌾 کربو</div></div><div class="nut-item"><div class="nut-val">'+Math.round(fd.fat)+'</div><div class="nut-label">🫒 چربی</div></div><div class="nut-item"><div class="nut-val">'+Math.round(fd.fiber)+'</div><div class="nut-label">🌿 فیبر</div></div><div class="nut-item"><div class="nut-val">'+Math.round(fd.sugar)+'</div><div class="nut-label">🍬 قند</div></div></div><div class="nut-detail" id="'+gid+'">🦴 کلسیم: '+Math.round(fd.calcium)+'mg | 🩸 آهن: '+Math.round(fd.iron)+'mg<br>💧 سدیم: '+Math.round(fd.sodium)+'mg | ⚡ پتاسیم: '+Math.round(fd.potassium)+'mg<br>🍊 ویتامین C: '+Math.round(fd.vitamin_c)+'mg | ☀️ ویتامین D: '+Math.round(fd.vitamin_d)+'mg<br>👁️ ویتامین A: '+Math.round(fd.vitamin_a)+'mcg | 🫀 کلسترول: '+Math.round(fd.cholesterol)+'mg</div>';
}else{
foodResults[id]=null;
btn.textContent='❌';btn.className='search-btn fail';
res.className='search-result err';res.innerHTML='پیدا نشد، نام دقیق‌تری وارد کنید';
}
}catch(e){
foodResults[id]=null;
btn.textContent='❌';btn.className='search-btn fail';
res.className='search-result err';res.innerHTML='خطا در ارتباط';
}
btn.disabled=false;updateSubmitBtn();
}
function updateSubmitBtn(){
var btn=document.getElementById('submitBtn');
var rows=document.querySelectorAll('.food-row');
if(!rows.length){btn.disabled=true;btn.textContent='⚠️ ابتدا غذا اضافه کنید';return}
var allDone=[].every.call(rows,function(r){var id=r.id.replace('row-','');return foodResults[id]&&foodResults[id].calories});
btn.disabled=!allDone;
if(allDone)btn.textContent='✅ ثبت وعده ('+rows.length+' غذا)';
else{var searched=[].filter.call(rows,function(r){var id=r.id.replace('row-','');return foodResults[id]}).length;btn.textContent='⚠️ '+searched+'/'+rows.length+' غذا سرچ شده';}
}
async function submitMeal(){
var rows=document.querySelectorAll('.food-row');
if(!rows.length)return;
var btn=document.getElementById('submitBtn');btn.disabled=true;btn.textContent='⏳ در حال ثبت...';
var mealType=document.getElementById('mealType').value;
var foods=[];
[].forEach.call(rows,function(row){
var id=row.id.replace('row-','');
var fd=foodResults[id];
if(fd)foods.push({food_name:fd.food_name,grams:fd.per_grams,calories:fd.calories,protein:fd.protein,carbs:fd.carbs,fat:fd.fat,fiber:fd.fiber||0,sugar:fd.sugar||0,calcium:fd.calcium||0,iron:fd.iron||0,sodium:fd.sodium||0,potassium:fd.potassium||0,vitamin_a:fd.vitamin_a||0,vitamin_c:fd.vitamin_c||0,vitamin_d:fd.vitamin_d||0,cholesterol:fd.cholesterol||0});
});
var r=await fetch('api/meals.php?action=add',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({meal_type:mealType,date:new Date().toISOString().split('T')[0],foods:foods})});
var d=await r.json();
foodResults={};
if(d.success){showToast('✅ وعده ثبت شد!');document.getElementById('foodRows').innerHTML='';addFoodRow();loadTodayMeals();loadHistory();}
else showToast(d.message||'خطا',true);
btn.disabled=false;updateSubmitBtn();
}
function loadTodayMeals(){
var today=new Date().toISOString().split('T')[0];
fetch('api/meals.php?action=list&date='+today).then(function(r){return r.json()}).then(function(d){
var wrap=document.getElementById('todayMeals');
if(!d.success||!d.data||!d.data.length){wrap.innerHTML='<div class="empty">هیچ وعده‌ای ثبت نشده</div>';return}
var icons={'صبحانه':'🌅','ناهار':'☀️','شام':'🌙','میان‌وعده':'🍪'};
var html='';
d.data.forEach(function(m){
var mcals=0,mpro=0,mcarb=0,mfat=0;
if(m.foods)m.foods.forEach(function(f){mcals+=parseFloat(f.calories)||0;mpro+=parseFloat(f.protein)||0;mcarb+=parseFloat(f.carbs)||0;mfat+=parseFloat(f.fat)||0});
html+='<div class="meal-card" onclick="this.querySelector(\'.meal-body\').classList.toggle(\'show\')"><div class="meal-header"><span class="type">'+(icons[m.meal_type]||'🍽️')+' '+m.meal_type+'</span><span class="macros">'+Math.round(mcals)+' kcal | '+Math.round(mpro)+'g پروتئین</span></div><div class="meal-body">';
if(m.foods)m.foods.forEach(function(f){html+='<div class="meal-food"><span class="name">'+f.food_name+' ('+f.grams+'g)</span><span>'+Math.round(f.calories)+' kcal</span></div>'});
html+='</div></div>';
});
wrap.innerHTML=html;
});
}
function loadHistory(){
fetch('api/meals.php?action=history&days=14').then(function(r){return r.json()}).then(function(d){
var wrap=document.getElementById('historyScroll');
if(!d.success||!d.data||!d.data.length){wrap.innerHTML='<div class="empty">تاریخچه‌ای نیست</div>';return}
var today=new Date().toISOString().split('T')[0];
var html='';
d.data.forEach(function(day){
var isActive=day.date===today?'active':'';
var label=day.date===today?'امروز':day.date===new Date(Date.now()-86400000).toISOString().split('T')[0]?'دیروز':day.date;
html+='<div class="day-card '+isActive+'" onclick="showDay(\''+day.date+'\')"><div class="date">'+label+'</div><div class="cal">'+Math.round(day.total_calories||0)+'</div><div class="pro">'+Math.round(day.total_protein||0)+'g</div></div>';
});
wrap.innerHTML=html;
});
}
function showDay(date){
fetch('api/meals.php?action=list&date='+date).then(function(r){return r.json()}).then(function(d){
var body=document.getElementById('modalBody');
document.getElementById('modalTitle').textContent='📋 وعده‌های '+date;
var icons={'صبحانه':'🌅','ناهار':'☀️','شام':'🌙','میان‌وعده':'🍪'};
var html='';
if(d.success&&d.data&&d.data.length){
d.data.forEach(function(m){
html+='<div class="meal-card"><div class="meal-header"><span class="type">'+(icons[m.meal_type]||'🍽️')+' '+m.meal_type+'</span></div><div class="meal-body show">';
if(m.foods)m.foods.forEach(function(f){html+='<div class="meal-food"><span class="name">'+f.food_name+' ('+f.grams+'g)</span><span>'+Math.round(f.calories)+' kcal | '+Math.round(f.protein)+'g</span></div>'});
html+='</div></div>';
});
}else html='<div class="empty">هیچ وعده‌ای ثبت نشده</div>';
body.innerHTML=html;
document.getElementById('dayModal').classList.add('show');
});
}
function closeModal(){document.getElementById('dayModal').classList.remove('show')}
document.addEventListener('click',function(e){document.querySelectorAll('.autocomplete-list.show').forEach(function(ac){if(!ac.contains(e.target))ac.classList.remove('show')})});
addFoodRow();loadTodayMeals();loadHistory();
</script>
</body></html>
