<?php
// Chat Widget Component - include in all pages
// Requires: user session, $firstName variable
?>
<style>
/* Toast Notification */
.toast{position:fixed;top:20px;left:50%;transform:translateX(-50%);background:rgba(13,19,33,.95);border:1px solid rgba(0,230,118,.2);color:#e2e8f0;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:500;z-index:9999;opacity:0;transition:opacity .3s,transform .3s;pointer-events:none;font-family:'Vazirmatn',sans-serif;box-shadow:0 8px 30px rgba(0,0,0,.4)}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);pointer-events:auto}
.toast.error{border-color:rgba(255,77,106,.2);background:rgba(30,10,15,.95)}

/* Chat Scrollbar */
.chat-messages::-webkit-scrollbar{width:5px}
.chat-messages::-webkit-scrollbar-track{background:transparent}
.chat-messages::-webkit-scrollbar-thumb{background:rgba(0,212,255,.15);border-radius:3px}
.chat-messages{scrollbar-width:thin;scrollbar-color:rgba(0,212,255,.15) transparent}

/* Chat FAB */
.chat-fab{position:fixed;bottom:88px;left:16px;width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#00b4d8,#0077b6);border:none;color:#fff;font-size:20px;cursor:pointer;z-index:200;box-shadow:0 4px 16px rgba(0,180,216,.25);transition:transform .2s;display:flex;align-items:center;justify-content:center}
.chat-fab:active{transform:scale(.92)}

/* Chat Panel */
.chat-panel{position:fixed;bottom:148px;left:16px;width:320px;max-width:calc(100vw - 32px);height:420px;background:rgba(10,14,26,.97);border:1px solid rgba(255,255,255,.08);border-radius:20px;z-index:150;display:none;flex-direction:column;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.5)}
.chat-panel.show{display:flex}

.chat-header{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;justify-content:space-between;align-items:center}
.chat-header-title{display:flex;align-items:center;gap:8px}
.chat-header-dot{width:8px;height:8px;border-radius:50%;background:#00e676}
.chat-header span{font-weight:600;font-size:14px}
.chat-close{background:none;border:none;color:#64748b;font-size:20px;cursor:pointer;padding:4px}

/* Chat Messages */
.chat-messages{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:8px}
.chat-msg{padding:10px 14px;border-radius:14px;font-size:13px;max-width:88%;line-height:1.7;word-wrap:break-word}
.chat-msg.user{background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.1);align-self:flex-start;border-bottom-right-radius:4px}
.chat-msg.agent{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);align-self:flex-end;border-bottom-left-radius:4px}
.chat-msg.welcome{background:rgba(0,212,255,.04);border:1px solid rgba(0,212,255,.06);align-self:center;text-align:center;font-size:12px;color:#64748b;max-width:95%}
.chat-msg.goal-change{background:rgba(0,230,118,.08);border:1px solid rgba(0,230,118,.12);align-self:center;text-align:center}

/* Status Messages */
.chat-status{align-self:center;display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:12px;font-size:11px;color:#64748b;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.04)}
.status-dot{width:6px;height:6px;border-radius:50%;background:#00d4ff;animation:blink 1s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

/* Chat Input */
.chat-input-wrap{display:flex;gap:8px;padding:12px;border-top:1px solid rgba(255,255,255,.06)}
.chat-input-wrap input{flex:1;padding:10px 12px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:10px;color:#e2e8f0;font-family:'Vazirmatn',sans-serif;font-size:13px;outline:none}
.chat-input-wrap input:focus{border-color:rgba(0,212,255,.3)}
.chat-send{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#00b4d8,#0077b6);border:none;color:#fff;font-size:16px;cursor:pointer}
.chat-send:active{transform:scale(.95)}
</style>

<div class="toast" id="toast"></div>
<button class="chat-fab" onclick="toggleChat()">💬</button>
<div class="chat-panel" id="chatPanel">
<div class="chat-header">
<div class="chat-header-title"><div class="chat-header-dot"></div><span>ایجنت AK Fitness</span></div>
<button class="chat-close" onclick="toggleChat()">✕</button>
</div>
<div class="chat-messages" id="chatMessages"></div>
<div class="chat-input-wrap">
<input type="text" id="chatInput" placeholder="نام غذا یا سوال ورزشی..." onkeydown="if(event.key==='Enter')sendChat()">
<button class="chat-send" onclick="sendChat()">➤</button>
</div>
</div>

<script>
var chatUserName='<?php echo htmlspecialchars($firstName ?? "کاربر"); ?>';
var chatOpen=false,chatInited=false;

function showToast(msg,isError){
var t=document.getElementById('toast');
t.textContent=msg;
t.className='toast show'+(isError?' error':'');
setTimeout(function(){t.className='toast'},2500);
}

function toggleChat(){
chatOpen=!chatOpen;
var p=document.getElementById('chatPanel');
if(chatOpen){p.classList.add('show');if(!chatInited){chatInited=true;showWelcome()}}
else p.classList.remove('show');
}

function showWelcome(){
var w=document.getElementById('chatMessages');
w.innerHTML='<div class="chat-msg welcome">💪 سلام '+chatUserName+'!<br>من ایجنت تغذیه و ورزش هستم.<br>غذا بپرس، هدف عوض کن، رژیم بپرس!</div>';
}

function addStatus(text){
var w=document.getElementById('chatMessages');
var s=document.createElement('div');s.className='chat-status';s.id='chatStatus';
s.innerHTML='<div class="status-dot"></div><span>'+text+'</span>';
w.appendChild(s);w.scrollTop=w.scrollHeight;
}

function removeStatus(){
var s=document.getElementById('chatStatus');if(s)s.remove();
}

function sendChat(){
var input=document.getElementById('chatInput'),msg=input.value.trim();
if(!msg)return;input.value='';
var wrap=document.getElementById('chatMessages');
removeStatus();
wrap.innerHTML+='<div class="chat-msg user">'+msg+'</div>';
wrap.scrollTop=wrap.scrollHeight;
addStatus('📡 در حال پردازش...');
setTimeout(function(){var s=document.getElementById('chatStatus');if(s)s.querySelector('span').textContent='🧠 در حال فکر...'},1500);
fetch('api/chat.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:msg})}).then(function(r){return r.json()}).then(function(d){
removeStatus();
if(d.success&&d.data){
var cls='chat-msg agent';
if(d.data.goal_changed)cls='chat-msg goal-change';
wrap.innerHTML+='<div class="'+cls+'">'+(d.data.reply||'پاسخی دریافت نشد').replace(/\n/g,'<br>')+'</div>';
if(d.data.goal_changed)showToast('✅ هدف ذخیره شد!');
}else{wrap.innerHTML+='<div class="chat-msg agent">❌ خطا</div>';}
wrap.scrollTop=wrap.scrollHeight;
}).catch(function(){
removeStatus();
wrap.innerHTML+='<div class="chat-msg agent">⚠️ خطا در ارتباط</div>';
wrap.scrollTop=wrap.scrollHeight;
});
}
</script>
