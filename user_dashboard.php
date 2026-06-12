<?php
/**
 * user_dashboard.php
 * Interface utilisateur standard.
 */
require "auth_middleware.php";
require_auth();

// Les admins ont leur propre page
if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kinnesis — Mon espace</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --c0: #060a12; --accent: #3d8ef0; --accent2: #5ba3ff;
  --green: #22d65e; --red: #f05a5a; --amber: #f0a834;
  --border: rgba(255,255,255,0.07); --border2: rgba(255,255,255,0.12);
  --text: #e8eef8; --muted: rgba(232,238,248,0.45); --muted2: rgba(232,238,248,0.22);
}
html, body { min-height: 100vh; background: var(--c0); font-family: 'DM Sans', sans-serif; color: var(--text); }
.orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
.orb1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(61,142,240,0.13) 0%, transparent 70%); top: -150px; right: -80px; }
.orb2 { width: 380px; height: 380px; background: radial-gradient(circle, rgba(34,214,94,0.07) 0%, transparent 70%); bottom: -80px; left: -60px; }
.grid-bg { position: fixed; inset: 0; z-index: 0; background-image: linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px); background-size: 56px 56px; mask-image: radial-gradient(ellipse 100% 80% at 50% 0%, black 0%, transparent 85%); }
.app { position: relative; z-index: 10; display: flex; min-height: 100vh; }

/* Sidebar */
.sidebar { width: 240px; flex-shrink: 0; background: rgba(13,21,35,0.93); border-right: 1px solid var(--border); backdrop-filter: blur(20px); display: flex; flex-direction: column; padding: 26px 18px; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; }
.sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
.sidebar-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #1a3a6e, #2560c0); border: 1px solid rgba(91,163,255,0.28); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 14px rgba(61,142,240,0.18); flex-shrink: 0; }
.brand-name { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700; letter-spacing: -0.01em; }
.nav-label { font-size: 10px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted2); padding: 0 8px; margin-bottom: 7px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13.5px; color: var(--muted); cursor: pointer; transition: all 0.18s; margin-bottom: 2px; text-decoration: none; position: relative; }
.nav-item:hover { background: rgba(255,255,255,0.05); color: var(--text); }
.nav-item.active { background: rgba(61,142,240,0.11); color: var(--accent2); }
.nav-item.active::before { content: ''; position: absolute; left: 0; top: 22%; bottom: 22%; width: 3px; background: var(--accent); border-radius: 2px; }
.sidebar-spacer { flex: 1; }
.user-card { background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 12px; padding: 13px; display: flex; align-items: center; gap: 10px; }
.avatar { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #1e4a8a, #3d8ef0); display: flex; align-items: center; justify-content: center; font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; flex-shrink: 0; color: white; }
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role-badge { display: inline-flex; align-items: center; font-size: 10px; background: rgba(61,142,240,0.12); border: 1px solid rgba(61,142,240,0.2); color: var(--accent2); border-radius: 5px; padding: 2px 7px; margin-top: 3px; font-weight: 500; }
.logout-btn { color: var(--muted); background: none; border: none; cursor: pointer; padding: 4px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; }
.logout-btn:hover { color: var(--red); background: rgba(240,90,90,0.1); }

/* Main */
.main { margin-left: 240px; flex: 1; padding: 34px 36px; min-height: 100vh; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 30px; animation: fadeIn 0.4s ease both; }
.page-title { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: -0.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 4px; }
.live-badge { display: flex; align-items: center; gap: 7px; background: rgba(34,214,94,0.09); border: 1px solid rgba(34,214,94,0.2); border-radius: 20px; padding: 6px 14px; font-size: 12px; color: var(--green); font-weight: 500; }
.pulse { width: 7px; height: 7px; border-radius: 50%; background: var(--green); animation: pulse-anim 1.8s ease-in-out infinite; }
@keyframes pulse-anim { 0%,100% { box-shadow: 0 0 0 0 rgba(34,214,94,0.5); } 50% { box-shadow: 0 0 0 5px rgba(34,214,94,0); } }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 26px; animation: fadeInUp 0.4s ease 0.08s both; }
.stat-card { background: rgba(17,30,48,0.88); border: 1px solid var(--border); border-radius: 16px; padding: 20px 22px; position: relative; overflow: hidden; transition: border-color 0.2s, transform 0.2s; }
.stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
.stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); }
.stat-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.stat-icon.blue { background: rgba(61,142,240,0.14); } .stat-icon.green { background: rgba(34,214,94,0.11); } .stat-icon.amber { background: rgba(240,168,52,0.11); }
.stat-val { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 700; letter-spacing: -0.03em; line-height: 1; }
.stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; }

/* Content */
.content-grid { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 20px; animation: fadeInUp 0.4s ease 0.16s both; }
.panel { background: rgba(17,30,48,0.88); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
.panel-header { display: flex; align-items: center; justify-content: space-between; padding: 17px 22px; border-bottom: 1px solid var(--border); }
.panel-title { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.panel-count { font-size: 11px; background: rgba(255,255,255,0.07); border: 1px solid var(--border2); border-radius: 20px; padding: 2px 10px; color: var(--muted); }
.panel-body { padding: 14px; }

/* Room cards */
.room-card { display: flex; align-items: center; justify-content: space-between; padding: 13px 15px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); margin-bottom: 9px; transition: all 0.2s; }
.room-card:last-child { margin-bottom: 0; }
.room-card:hover { background: rgba(255,255,255,0.05); border-color: var(--border2); }
.room-card.open { border-left: 3px solid var(--green); }
.room-info { display: flex; align-items: center; gap: 11px; }
.room-indicator { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.room-indicator.open { background: var(--green); box-shadow: 0 0 8px rgba(34,214,94,0.5); animation: pulse-anim 2s ease-in-out infinite; }
.room-indicator.closed { background: rgba(255,255,255,0.14); }
.room-name { font-size: 14px; font-weight: 500; }
.room-status { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.room-status.open { color: var(--green); }

/* Buttons */
.toggle-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; border: 1px solid var(--border2); background: rgba(255,255,255,0.05); color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.18s; white-space: nowrap; }
.toggle-btn.open-action { background: rgba(34,214,94,0.09); border-color: rgba(34,214,94,0.24); color: var(--green); }
.toggle-btn.open-action:hover { background: rgba(34,214,94,0.16); }
.toggle-btn.close-action { background: rgba(240,90,90,0.09); border-color: rgba(240,90,90,0.24); color: var(--red); }
.toggle-btn.close-action:hover { background: rgba(240,90,90,0.16); }
.toggle-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* Logs */
.log-entry { display: flex; align-items: flex-start; gap: 11px; padding: 11px 14px; border-radius: 10px; margin-bottom: 5px; background: rgba(255,255,255,0.02); border: 1px solid transparent; transition: background 0.15s; }
.log-entry:hover { background: rgba(255,255,255,0.04); border-color: var(--border); }
.log-dot { width: 7px; height: 7px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
.log-dot.open { background: var(--green); } .log-dot.close { background: var(--red); }
.log-text { flex: 1; min-width: 0; }
.log-main { font-size: 13px; } .log-main strong { font-weight: 500; }
.log-time { font-size: 11px; color: var(--muted2); margin-top: 2px; font-family: monospace; }
.log-tag { font-size: 10px; padding: 2px 8px; border-radius: 5px; font-weight: 500; text-transform: uppercase; align-self: center; flex-shrink: 0; }
.log-tag.open { background: rgba(34,214,94,0.11); color: var(--green); }
.log-tag.close { background: rgba(240,90,90,0.11); color: var(--red); }

/* States */
.empty-state { padding: 34px 20px; text-align: center; color: var(--muted); font-size: 13.5px; line-height: 1.7; }
.skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 100%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; height: 56px; margin-bottom: 8px; }
@keyframes shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }

/* Toast */
#toasts { position: fixed; top: 22px; right: 22px; z-index: 9999; display: flex; flex-direction: column; gap: 9px; pointer-events: none; }
.toast { display: flex; align-items: center; gap: 10px; padding: 13px 17px; background: rgba(17,30,48,0.97); border: 1px solid var(--border2); border-radius: 12px; font-size: 13.5px; backdrop-filter: blur(20px); box-shadow: 0 8px 28px rgba(0,0,0,0.4); animation: toastIn 0.32s cubic-bezier(0.16,1,0.3,1) both; pointer-events: all; }
.toast.success { border-left: 3px solid var(--green); }
.toast.error { border-left: 3px solid var(--red); }
@keyframes toastIn { from { opacity:0; transform:translateX(18px) scale(0.96); } to { opacity:1; transform:translateX(0) scale(1); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateX(14px) scale(0.96); } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeInUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.09); border-radius: 2px; }
@media (max-width: 800px) { .sidebar { display:none; } .main { margin-left:0; padding:22px 18px; } .content-grid { grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="grid-bg"></div>
<div id="toasts"></div>

<div class="app">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2L4 6V12C4 16.4 7.4 20.5 12 22C16.6 20.5 20 16.4 20 12V6L12 2Z" stroke="rgba(91,163,255,0.9)" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 12L11 14L15 10" stroke="#5ba3ff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <span class="brand-name">Kinnesis</span>
    </div>
    <div class="nav-label">Navigation</div>
    <a class="nav-item active" href="#">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Mes salles
    </a>
    <div class="sidebar-spacer"></div>
    <div class="user-card">
      <div class="avatar" id="user-avatar">??</div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($_SESSION['nom']) ?></div>
        <div class="user-role-badge">Utilisateur</div>
      </div>
      <a href="auth_logout.php" class="logout-btn" title="Déconnexion">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </a>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Bonjour, <?= htmlspecialchars(explode(' ', $_SESSION['nom'])[0]) ?> </div>
        <div class="page-sub" id="current-time">—</div>
      </div>
      <div class="live-badge"><div class="pulse"></div>En ligne</div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#3d8ef0" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div class="stat-val" id="stat-total">—</div><div class="stat-label">Salles accessibles</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="1.8" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
        <div class="stat-val" id="stat-open">—</div><div class="stat-label">Ouvertes</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon amber"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#f0a834" stroke-width="1.8" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
        <div class="stat-val" id="stat-logs">—</div><div class="stat-label">Mes actions</div>
      </div>
    </div>

    <div class="content-grid">
      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Mes salles
          </div>
          <span class="panel-count" id="rooms-count">—</span>
        </div>
        <div class="panel-body" id="rooms"><div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div></div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <div class="panel-title">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Historique
          </div>
          <span class="panel-count" id="logs-count">—</span>
        </div>
        <div class="panel-body" id="logs" style="max-height:420px;overflow-y:auto;"><div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div></div>
      </div>
    </div>
  </main>
</div>

<script>
const USER_ID = <?= (int)$_SESSION['user_id'] ?>;
const NOM = <?= json_encode($_SESSION['nom']) ?>;

document.getElementById('user-avatar').textContent = NOM.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);
function updateTime(){document.getElementById('current-time').textContent=new Date().toLocaleDateString('fr-FR',{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});}
updateTime();setInterval(updateTime,60000);

function toast(msg,type='success'){
  const c=document.getElementById('toasts'),t=document.createElement('div');
  t.className=`toast ${type}`;
  const ic=type==='success'?`<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`:`<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f05a5a" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
  t.innerHTML=`${ic}<span>${msg}</span>`;c.appendChild(t);
  setTimeout(()=>{t.style.animation='toastOut 0.28s ease forwards';setTimeout(()=>t.remove(),280);},3200);
}

function toggle(salle,btn){
  const isOpen=parseInt(btn.dataset.state)===1,action=isOpen?'close':'open';
  btn.disabled=true;btn.textContent='…';
  fetch(`lock_toggle.php?user_id=${USER_ID}&numero=${salle}&action=${action}`)
    .then(r=>r.json())
    .then(d=>{
      if(d.success){toast(`Salle ${salle} ${isOpen?'fermée':'ouverte'}`,'success');loadRooms();loadLogs();}
      else{toast(d.message||'Accès refusé','error');btn.disabled=false;}
    })
    .catch(()=>{toast('Erreur réseau','error');btn.disabled=false;});
}

function loadRooms(){
  fetch('lock_get_state.php?all=1')
    .then(r=>r.json())
    .then(data=>{
      const open=data.filter(r=>r.etat==1).length;
      document.getElementById('stat-total').textContent=data.length;
      document.getElementById('stat-open').textContent=open;
      document.getElementById('rooms-count').textContent=`${open}/${data.length}`;
      if(!data.length){document.getElementById('rooms').innerHTML='<div class="empty-state">Aucune salle assignée.<br>Contactez un administrateur.</div>';return;}
      let h='';
      data.forEach((r,i)=>{
        const o=r.etat==1,s=o?'open':'closed';
        h+=`<div class="room-card ${s}" style="animation:fadeInUp .3s ease ${i*.05}s both">
          <div class="room-info"><div class="room-indicator ${s}"></div><div>
            <div class="room-name">Salle ${r.numero_salle}</div>
            <div class="room-status ${s}">${o?'Ouverte · Accès autorisé':'Fermée'}</div>
          </div></div>
          <button class="toggle-btn ${o?'close-action':'open-action'}" data-state="${o?1:0}" onclick="toggle(${r.numero_salle},this)">${o?'Fermer':'Ouvrir'}</button>
        </div>`;
      });
      document.getElementById('rooms').innerHTML=h;
    })
    .catch(()=>{document.getElementById('rooms').innerHTML='<div class="empty-state">Impossible de charger les salles</div>';});
}

function loadLogs(){
  fetch(`log_get.php?user_id=${USER_ID}`)
    .then(r=>r.json())
    .then(data=>{
      document.getElementById('stat-logs').textContent=data.length;
      document.getElementById('logs-count').textContent=`${data.length} entrées`;
      if(!data.length){document.getElementById('logs').innerHTML='<div class="empty-state">Aucune action enregistrée</div>';return;}
      let h='';
      data.forEach((log,i)=>{
        const o=['open','ouverture','ouverte'].includes((log.action||'').toLowerCase());
        const d=new Date(log.date_action),ts=isNaN(d)?log.date_action:d.toLocaleString('fr-FR',{hour:'2-digit',minute:'2-digit',day:'2-digit',month:'short'});
        h+=`<div class="log-entry" style="animation-delay:${i*.03}s">
          <div class="log-dot ${o?'open':'close'}"></div>
          <div class="log-text"><div class="log-main">Salle <strong>${log.numero_salle}</strong></div><div class="log-time">${ts}</div></div>
          <span class="log-tag ${o?'open':'close'}">${o?'Ouvert':'Fermé'}</span>
        </div>`;
      });
      document.getElementById('logs').innerHTML=h;
    })
    .catch(()=>{document.getElementById('logs').innerHTML='<div class="empty-state">Impossible de charger</div>';});
}

loadRooms();loadLogs();
setInterval(()=>{loadRooms();loadLogs();},5000);
</script>
</body>
</html>
