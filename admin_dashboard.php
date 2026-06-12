<?php
/**
 * admin_dashboard.php
 * Interface administrateur complète.
 */
require "auth_middleware.php";
require_admin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kinnesis — Administration</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --c0: #060a12; --accent: #3d8ef0; --accent2: #5ba3ff;
  --green: #22d65e; --red: #f05a5a; --amber: #f0a834; --purple: #a78bfa;
  --border: rgba(255,255,255,0.07); --border2: rgba(255,255,255,0.13);
  --text: #e8eef8; --muted: rgba(232,238,248,0.45); --muted2: rgba(232,238,248,0.22);
}
html, body { min-height: 100vh; background: var(--c0); font-family: 'DM Sans', sans-serif; color: var(--text); }
.orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
.orb1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(61,142,240,0.12) 0%, transparent 70%); top: -150px; right: -80px; }
.orb2 { width: 380px; height: 380px; background: radial-gradient(circle, rgba(167,139,250,0.08) 0%, transparent 70%); bottom: -80px; left: -50px; }
.grid-bg { position: fixed; inset: 0; z-index: 0; background-image: linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px); background-size: 56px 56px; mask-image: radial-gradient(ellipse 100% 70% at 50% 0%, black 0%, transparent 80%); }
.app { position: relative; z-index: 10; display: flex; min-height: 100vh; }

/* Sidebar */
.sidebar { width: 250px; flex-shrink: 0; background: rgba(13,21,35,0.93); border-right: 1px solid var(--border); backdrop-filter: blur(22px); display: flex; flex-direction: column; padding: 26px 18px; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; }
.sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; }
.sidebar-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #1a3a6e, #2560c0); border: 1px solid rgba(91,163,255,0.28); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 14px rgba(61,142,240,0.18); flex-shrink: 0; }
.brand-name { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700; letter-spacing: -0.01em; }
.admin-badge { display: inline-flex; align-items: center; gap: 5px; margin-left: 4px; margin-bottom: 24px; font-size: 10px; background: rgba(167,139,250,0.12); border: 1px solid rgba(167,139,250,0.22); color: var(--purple); border-radius: 5px; padding: 3px 9px; font-weight: 500; letter-spacing: 0.05em; }
.nav-label { font-size: 10px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted2); padding: 0 8px; margin-bottom: 7px; margin-top: 4px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; font-size: 13.5px; color: var(--muted); cursor: pointer; transition: all 0.18s; margin-bottom: 2px; text-decoration: none; position: relative; }
.nav-item:hover { background: rgba(255,255,255,0.05); color: var(--text); }
.nav-item.active { background: rgba(61,142,240,0.11); color: var(--accent2); }
.nav-item.active::before { content: ''; position: absolute; left: 0; top: 22%; bottom: 22%; width: 3px; background: var(--accent); border-radius: 2px; }
.sidebar-spacer { flex: 1; }
.user-card { background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 12px; padding: 13px; display: flex; align-items: center; gap: 10px; }
.avatar { width: 34px; height: 34px; border-radius: 9px; background: linear-gradient(135deg, #3b1f7a, #7c3aed); display: flex; align-items: center; justify-content: center; font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; flex-shrink: 0; color: white; }
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role-sm { font-size: 10px; color: var(--purple); margin-top: 2px; }
.logout-btn { color: var(--muted); background: none; border: none; cursor: pointer; padding: 4px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; }
.logout-btn:hover { color: var(--red); background: rgba(240,90,90,0.1); }

/* Main */
.main { margin-left: 250px; flex: 1; padding: 32px 36px; min-height: 100vh; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; animation: fadeIn 0.4s ease both; }
.page-title { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: -0.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 4px; }
.live-badge { display: flex; align-items: center; gap: 7px; background: rgba(167,139,250,0.09); border: 1px solid rgba(167,139,250,0.2); border-radius: 20px; padding: 6px 14px; font-size: 12px; color: var(--purple); font-weight: 500; }
.pulse-p { width: 7px; height: 7px; border-radius: 50%; background: var(--purple); animation: pulse-p 2s ease-in-out infinite; }
@keyframes pulse-p { 0%,100% { box-shadow: 0 0 0 0 rgba(167,139,250,0.5); } 50% { box-shadow: 0 0 0 5px rgba(167,139,250,0); } }
@keyframes pulse-anim { 0%,100% { box-shadow: 0 0 0 0 rgba(34,214,94,0.5); } 50% { box-shadow: 0 0 0 5px rgba(34,214,94,0); } }

/* Tabs */
.tabs { display: flex; gap: 4px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 12px; padding: 4px; margin-bottom: 26px; width: fit-content; animation: fadeIn 0.4s ease both; }
.tab-btn { padding: 8px 20px; border-radius: 9px; border: none; background: transparent; color: var(--muted); font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 500; cursor: pointer; transition: all 0.18s; }
.tab-btn.active { background: rgba(61,142,240,0.14); color: var(--accent2); border: 1px solid rgba(61,142,240,0.22); }
.tab-btn:hover:not(.active) { color: var(--text); background: rgba(255,255,255,0.05); }
.tab-panel { display: none; animation: fadeInUp 0.3s ease both; }
.tab-panel.active { display: block; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
.stat-card { background: rgba(17,30,48,0.88); border: 1px solid var(--border); border-radius: 16px; padding: 20px 22px; position: relative; overflow: hidden; transition: border-color 0.2s, transform 0.2s; }
.stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
.stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); }
.stat-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; margin-bottom: 13px; }
.stat-icon.blue { background: rgba(61,142,240,0.14); } .stat-icon.green { background: rgba(34,214,94,0.11); } .stat-icon.amber { background: rgba(240,168,52,0.11); } .stat-icon.purple { background: rgba(167,139,250,0.12); }
.stat-val { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 700; letter-spacing: -0.03em; line-height: 1; }
.stat-label { font-size: 11.5px; color: var(--muted); margin-top: 4px; }

/* Grid */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }

/* Panel */
.panel { background: rgba(17,30,48,0.88); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; }
.panel-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; border-bottom: 1px solid var(--border); }
.panel-title { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.panel-count { font-size: 11px; background: rgba(255,255,255,0.07); border: 1px solid var(--border2); border-radius: 20px; padding: 2px 10px; color: var(--muted); }
.panel-body { padding: 14px; }

/* Room cards */
.room-card { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 11px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); margin-bottom: 8px; transition: all 0.18s; }
.room-card:last-child { margin-bottom: 0; }
.room-card:hover { background: rgba(255,255,255,0.05); border-color: var(--border2); }
.room-card.open { border-left: 3px solid var(--green); }
.room-info { display: flex; align-items: center; gap: 11px; }
.room-indicator { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.room-indicator.open { background: var(--green); box-shadow: 0 0 7px rgba(34,214,94,0.5); animation: pulse-anim 2s ease-in-out infinite; }
.room-indicator.closed { background: rgba(255,255,255,0.13); }
.room-name { font-size: 13.5px; font-weight: 500; }
.room-status { font-size: 11px; color: var(--muted); margin-top: 2px; }
.room-status.open { color: var(--green); }

/* Buttons */
.toggle-btn { display: flex; align-items: center; gap: 6px; padding: 7px 13px; border-radius: 8px; border: 1px solid var(--border2); background: rgba(255,255,255,0.05); color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.18s; white-space: nowrap; }
.toggle-btn.open-action { background: rgba(34,214,94,0.09); border-color: rgba(34,214,94,0.24); color: var(--green); }
.toggle-btn.open-action:hover { background: rgba(34,214,94,0.16); }
.toggle-btn.close-action { background: rgba(240,90,90,0.09); border-color: rgba(240,90,90,0.24); color: var(--red); }
.toggle-btn.close-action:hover { background: rgba(240,90,90,0.16); }
.toggle-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* User table */
.user-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.user-table th { font-size: 11px; font-weight: 500; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); padding: 0 14px 12px; text-align: left; }
.user-table td { padding: 11px 14px; border-top: 1px solid var(--border); vertical-align: middle; }
.user-table tr:hover td { background: rgba(255,255,255,0.02); }
.role-tag { display: inline-flex; align-items: center; font-size: 10.5px; padding: 3px 9px; border-radius: 5px; font-weight: 500; letter-spacing: 0.04em; }
.role-tag.admin { background: rgba(167,139,250,0.12); border: 1px solid rgba(167,139,250,0.2); color: var(--purple); }
.role-tag.user { background: rgba(61,142,240,0.10); border: 1px solid rgba(61,142,240,0.18); color: var(--accent2); }
.u-avatar { width: 30px; height: 30px; border-radius: 7px; background: linear-gradient(135deg, #1e3a6e, #3d8ef0); display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; margin-right: 9px; vertical-align: middle; }

/* Form */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.field { margin-bottom: 0; }
.field label { display: block; font-size: 11px; font-weight: 500; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); margin-bottom: 7px; }
.field input, .field select { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09); border-radius: 10px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px; padding: 11px 13px; outline: none; transition: border-color 0.2s, background 0.2s, box-shadow 0.2s; }
.field input::placeholder { color: var(--muted2); }
.field input:focus, .field select:focus { border-color: rgba(61,142,240,0.5); background: rgba(61,142,240,0.05); box-shadow: 0 0 0 3px rgba(61,142,240,0.09); }
.field select option { background: #111e30; }
.form-actions { display: flex; gap: 10px; margin-top: 18px; }
.btn-primary { display: flex; align-items: center; gap: 7px; padding: 11px 20px; background: linear-gradient(135deg, #2258b8, #3d8ef0); border: none; border-radius: 10px; color: white; font-family: 'Syne', sans-serif; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: filter 0.2s, transform 0.15s, box-shadow 0.2s; box-shadow: 0 4px 18px rgba(61,142,240,0.3); }
.btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 6px 24px rgba(61,142,240,0.45); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.btn-secondary { display: flex; align-items: center; gap: 7px; padding: 11px 18px; background: rgba(255,255,255,0.05); border: 1px solid var(--border2); border-radius: 10px; color: var(--muted); font-family: 'DM Sans', sans-serif; font-size: 13.5px; cursor: pointer; transition: all 0.18s; }
.btn-secondary:hover { background: rgba(255,255,255,0.09); color: var(--text); }

/* Access */
.access-item { display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; border-radius: 10px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); margin-bottom: 7px; font-size: 13.5px; }
.access-item:last-child { margin-bottom: 0; }
.switch { width: 36px; height: 20px; background: rgba(255,255,255,0.1); border: 1px solid var(--border2); border-radius: 10px; position: relative; cursor: pointer; transition: background 0.2s; flex-shrink: 0; }
.switch.on { background: rgba(34,214,94,0.3); border-color: rgba(34,214,94,0.4); }
.switch::after { content: ''; position: absolute; width: 14px; height: 14px; background: rgba(255,255,255,0.45); border-radius: 50%; top: 2px; left: 2px; transition: left 0.2s, background 0.2s; }
.switch.on::after { left: 18px; background: var(--green); }

/* Logs */
.log-entry { display: flex; align-items: flex-start; gap: 11px; padding: 11px 14px; border-radius: 10px; margin-bottom: 5px; background: rgba(255,255,255,0.02); border: 1px solid transparent; transition: background 0.15s; }
.log-entry:hover { background: rgba(255,255,255,0.04); border-color: var(--border); }
.log-dot { width: 7px; height: 7px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
.log-dot.open { background: var(--green); } .log-dot.close { background: var(--red); }
.log-text { flex: 1; }
.log-main { font-size: 13px; } .log-main strong { font-weight: 500; }
.log-time { font-size: 11px; color: var(--muted2); margin-top: 2px; font-family: monospace; }
.log-tag { font-size: 10px; padding: 2px 8px; border-radius: 5px; font-weight: 500; text-transform: uppercase; align-self: center; flex-shrink: 0; }
.log-tag.open { background: rgba(34,214,94,0.11); color: var(--green); }
.log-tag.close { background: rgba(240,90,90,0.11); color: var(--red); }

/* States */
.empty-state { padding: 34px 20px; text-align: center; color: var(--muted); font-size: 13.5px; line-height: 1.7; }
.skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 100%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 8px; height: 52px; margin-bottom: 8px; }
@keyframes shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* Toast */
#toasts { position: fixed; top: 22px; right: 22px; z-index: 9999; display: flex; flex-direction: column; gap: 9px; pointer-events: none; }
.toast { display: flex; align-items: center; gap: 10px; padding: 13px 17px; background: rgba(17,30,48,0.97); border: 1px solid var(--border2); border-radius: 12px; font-size: 13.5px; backdrop-filter: blur(20px); box-shadow: 0 8px 28px rgba(0,0,0,0.4); animation: toastIn 0.32s cubic-bezier(0.16,1,0.3,1) both; pointer-events: all; }
.toast.success { border-left: 3px solid var(--green); }
.toast.error { border-left: 3px solid var(--red); }
@keyframes toastIn { from { opacity:0; transform:translateX(18px) scale(0.96); } to { opacity:1; transform:translateX(0) scale(1); } }
@keyframes toastOut { from { opacity:1; } to { opacity:0; transform:translateX(14px) scale(0.96); } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.09); border-radius: 2px; }
@media (max-width: 960px) { .sidebar { display:none; } .main { margin-left:0; padding:22px 18px; } .stats-grid { grid-template-columns:repeat(2,1fr); } .grid-2 { grid-template-columns:1fr; } }

/* ── Dispositifs IoT ─────────────────────────────────────────────────────── */
.devices-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
.devices-summary { display:flex; gap:10px; flex-wrap:wrap; }
.ds-pill { display:inline-flex; align-items:center; gap:6px; padding:5px 13px; border-radius:20px; font-size:12px; font-weight:500; }
.ds-pill.online  { background:rgba(34,214,94,0.10);  border:1px solid rgba(34,214,94,0.22);  color:var(--green); }
.ds-pill.warning { background:rgba(240,168,52,0.10); border:1px solid rgba(240,168,52,0.22); color:var(--amber); }
.ds-pill.offline { background:rgba(240,90,90,0.10);  border:1px solid rgba(240,90,90,0.22);  color:var(--red);   }
.ds-pill-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.ds-pill.online  .ds-pill-dot { background:var(--green); animation:pulse-anim 2s ease-in-out infinite; }
.ds-pill.warning .ds-pill-dot { background:var(--amber); animation:pulse-warn 2s ease-in-out infinite; }
.ds-pill.offline .ds-pill-dot { background:var(--red); }
@keyframes pulse-warn { 0%,100%{box-shadow:0 0 0 0 rgba(240,168,52,0.5);}50%{box-shadow:0 0 0 5px rgba(240,168,52,0);} }

.devices-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:14px; }

.device-card {
  background: rgba(17,30,48,0.88);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 20px;
  position: relative;
  overflow: hidden;
  transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
  animation: fadeInUp 0.35s ease both;
}
.device-card:hover { transform:translateY(-3px); border-color:var(--border2); box-shadow:0 8px 28px rgba(0,0,0,0.35); }
.device-card::before {
  content:'';
  position:absolute; left:0; top:0; bottom:0; width:3px;
  border-radius:3px 0 0 3px;
  transition: background 0.3s;
}
.device-card.online  { border-color:rgba(34,214,94,0.18); }
.device-card.online::before  { background:var(--green); box-shadow:0 0 12px rgba(34,214,94,0.35); }
.device-card.warning { border-color:rgba(240,168,52,0.2); }
.device-card.warning::before { background:var(--amber); box-shadow:0 0 12px rgba(240,168,52,0.3); }
.device-card.offline { border-color:rgba(240,90,90,0.2); }
.device-card.offline::before { background:var(--red);   box-shadow:0 0 12px rgba(240,90,90,0.25); }

.device-card-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; }
.device-icon-wrap { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.device-card.online  .device-icon-wrap { background:rgba(34,214,94,0.11); }
.device-card.warning .device-icon-wrap { background:rgba(240,168,52,0.11); }
.device-card.offline .device-icon-wrap { background:rgba(240,90,90,0.11); }

.device-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:10.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; }
.device-card.online  .device-badge { background:rgba(34,214,94,0.12);  border:1px solid rgba(34,214,94,0.25);  color:var(--green); }
.device-card.warning .device-badge { background:rgba(240,168,52,0.12); border:1px solid rgba(240,168,52,0.28); color:var(--amber); }
.device-card.offline .device-badge { background:rgba(240,90,90,0.12);  border:1px solid rgba(240,90,90,0.28);  color:var(--red);   }
.device-badge-dot { width:5px; height:5px; border-radius:50%; flex-shrink:0; }
.device-card.online  .device-badge-dot { background:var(--green); animation:pulse-anim 2s ease-in-out infinite; }
.device-card.warning .device-badge-dot { background:var(--amber); animation:pulse-warn 2s ease-in-out infinite; }
.device-card.offline .device-badge-dot { background:var(--red); }

.device-name { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; letter-spacing:-0.01em; margin-bottom:4px; }
.device-id   { font-size:11px; color:var(--muted2); font-family:monospace; }
.device-meta { display:flex; flex-direction:column; gap:7px; padding-top:14px; border-top:1px solid var(--border); margin-top:4px; }
.device-meta-row { display:flex; align-items:center; gap:7px; font-size:12px; color:var(--muted); }
.device-meta-row svg { flex-shrink:0; opacity:.6; }
.device-meta-val { color:var(--text); font-size:12.5px; font-weight:500; }
.device-card.offline .device-meta-val.last-seen { color:var(--red); }
.device-card.warning .device-meta-val.last-seen { color:var(--amber); }
.device-card.online  .device-meta-val.last-seen { color:var(--green); }

/* Card shimmer */
.device-skeleton { background:linear-gradient(90deg,rgba(255,255,255,0.04) 0%,rgba(255,255,255,0.08) 50%,rgba(255,255,255,0.04) 100%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:18px; height:170px; }

/* Toast warning */
.toast.warning { border-left:3px solid var(--amber); }

/* Refresh indicator */
.refresh-ring { width:28px; height:28px; position:relative; flex-shrink:0; }
.refresh-ring svg { transition:transform 0.3s; }
.refresh-ring.spinning svg { animation:spin 0.8s linear; }
.next-refresh { font-size:11px; color:var(--muted2); }

@media (max-width:960px) { .devices-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .devices-grid { grid-template-columns:1fr; } }

/* ── Carte dispositif étendue : état serrure + mode ─────────────────────── */

/* Ligne séparatrice entre connectivité et contrôles */
.device-divider { border: none; border-top: 1px solid var(--border); margin: 14px 0 12px; }

/* Badges état serrure */
.lock-state-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 11px; border-radius: 8px;
  font-size: 12px; font-weight: 600;
  transition: all 0.25s;
}
.lock-state-badge.open  { background: rgba(34,214,94,0.12);  border: 1px solid rgba(34,214,94,0.28);  color: var(--green); }
.lock-state-badge.close { background: rgba(255,255,255,0.05); border: 1px solid var(--border2);         color: var(--muted); }
.lock-state-badge svg   { flex-shrink: 0; }

/* Badges mode */
.mode-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 6px;
  font-size: 10.5px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
  transition: all 0.3s;
}
.mode-badge.access { background: rgba(61,142,240,0.11); border: 1px solid rgba(61,142,240,0.22); color: var(--accent2); }
.mode-badge.enroll { background: rgba(240,168,52,0.15); border: 1px solid rgba(240,168,52,0.35); color: var(--amber);
                     box-shadow: 0 0 12px rgba(240,168,52,0.15); animation: enrollPulse 2.5s ease-in-out infinite; }
@keyframes enrollPulse { 0%,100%{box-shadow:0 0 8px rgba(240,168,52,0.12);}50%{box-shadow:0 0 18px rgba(240,168,52,0.3);} }
.mode-badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.mode-badge.access .mode-badge-dot { background: var(--accent2); }
.mode-badge.enroll .mode-badge-dot { background: var(--amber); animation: pulse-warn 2s ease-in-out infinite; }

/* Rangée état + mode */
.device-states-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; gap: 8px; flex-wrap: wrap; }

/* Boutons mode dans les cartes */
.mode-btn {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  width: 100%; padding: 9px 12px;
  border-radius: 9px; border: none; cursor: pointer;
  font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
  transition: all 0.2s; white-space: nowrap;
}
.mode-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.mode-btn.to-enroll {
  background: rgba(240,168,52,0.13); border: 1px solid rgba(240,168,52,0.32); color: var(--amber);
}
.mode-btn.to-enroll:hover:not(:disabled) { background: rgba(240,168,52,0.22); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(240,168,52,0.2); }
.mode-btn.to-access {
  background: rgba(61,142,240,0.13); border: 1px solid rgba(61,142,240,0.32); color: var(--accent2);
}
.mode-btn.to-access:hover:not(:disabled) { background: rgba(61,142,240,0.22); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(61,142,240,0.2); }

/* Carte en mode enroll : accentuation du bord supérieur */
.device-card.mode-enroll { border-top: 2px solid rgba(240,168,52,0.4); }

/* Overlay "hors ligne" sur les boutons */
.device-card.offline .mode-btn { opacity: 0.3; cursor: not-allowed; pointer-events: none; }

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
    <div class="admin-badge">
      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      Admin
    </div>
    <div class="nav-label">Tableau de bord</div>
    <a class="nav-item active" href="#" onclick="tab('overview');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Vue d'ensemble
    </a>
    <a class="nav-item" href="#" onclick="tab('users');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      Utilisateurs
    </a>
    <a class="nav-item" href="#" onclick="tab('access');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
      Accès
    </a>
    <a class="nav-item" href="#" onclick="tab('logs');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Journaux
    </a>
    <a class="nav-item" href="#" onclick="tab('devices');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><circle cx="12" cy="10" r="2"/><path d="M12 8v0M12 12v0"/></svg>
      Dispositifs IoT
    </a>
    <a class="nav-item" href="#" onclick="tab('fingerprints');return false;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
      Empreintes
    </a>
    <div class="sidebar-spacer"></div>
    <div class="user-card">
      <div class="avatar" id="user-avatar">??</div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($_SESSION['nom']) ?></div>
        <div class="user-role-sm">Administrateur</div>
      </div>
      <a href="auth_logout.php" class="logout-btn" title="Déconnexion">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </a>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Administration</div>
        <div class="page-sub" id="current-time">—</div>
      </div>
      <div class="live-badge"><div class="pulse-p"></div>Panneau admin</div>
    </div>

    <div class="tabs">
      <button class="tab-btn active" id="tb-overview" onclick="tab('overview')">Vue d'ensemble</button>
      <button class="tab-btn" id="tb-users"    onclick="tab('users')">Utilisateurs</button>
      <button class="tab-btn" id="tb-access"   onclick="tab('access')">Accès</button>
      <button class="tab-btn" id="tb-enroll"   onclick="tab('enroll')">Badges RFID</button>
      <button class="tab-btn" id="tb-fingerprints" onclick="tab('fingerprints')">Empreintes</button>
      <button class="tab-btn" id="tb-logs"     onclick="tab('logs')">Journaux</button>
      <button class="tab-btn" id="tb-devices"  onclick="tab('devices')">Dispositifs IoT</button>
    </div>

    <!-- ── OVERVIEW ── -->
    <div class="tab-panel active" id="p-overview">
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3d8ef0" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><div class="stat-val" id="st-rooms">—</div><div class="stat-label">Salles</div></div>
        <div class="stat-card"><div class="stat-icon green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="1.8" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div class="stat-val" id="st-open">—</div><div class="stat-label">Ouvertes</div></div>
        <div class="stat-card"><div class="stat-icon purple"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="stat-val" id="st-users">—</div><div class="stat-label">Utilisateurs</div></div>
        <div class="stat-card"><div class="stat-icon amber"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f0a834" stroke-width="1.8" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div><div class="stat-val" id="st-logs">—</div><div class="stat-label">Actions</div></div>
      </div>
      <div class="grid-2">
        <div class="panel">
          <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Serrures</div><span class="panel-count" id="rc-overview">—</span></div>
          <div class="panel-body" id="rooms-overview"><div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div></div>
        </div>
        <div class="panel">
          <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Dernières actions</div><span class="panel-count" id="lc-overview">—</span></div>
          <div class="panel-body" id="logs-overview" style="max-height:360px;overflow-y:auto;"><div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div></div>
        </div>
      </div>
    </div>

    <!-- ── USERS ── -->
    <div class="tab-panel" id="p-users">
      <div class="grid-2" style="align-items:start;">
        <div class="panel">
          <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Tous les utilisateurs</div><span class="panel-count" id="uc">—</span></div>
          <div style="overflow-x:auto;"><table class="user-table">
            <thead><tr><th>Nom</th><th>Rôle</th><th>Salles</th></tr></thead>
            <tbody id="users-tbody"><tr><td colspan="3"><div class="skeleton" style="margin:14px"></div></td></tr></tbody>
          </table></div>
        </div>
        <div class="panel">
          <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>Créer un utilisateur</div></div>
          <div class="panel-body">
            <div class="form-grid">
              <div class="field"><label>Nom</label><input type="text" id="new-nom" placeholder="ex: Jean Dupont"></div>
              <div class="field"><label>Code PIN</label><input type="password" id="new-pin" placeholder="min. 4 caractères"></div>
              <div class="field" style="grid-column:1/-1;"><label>Rôle</label>
                <select id="new-role"><option value="user">Utilisateur</option><option value="admin">Administrateur</option></select>
              </div>
            </div>
            <div class="form-actions">
              <button class="btn-primary" id="btn-create" onclick="createUser()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Créer
              </button>
              <button class="btn-secondary" onclick="document.getElementById('new-nom').value='';document.getElementById('new-pin').value='';">Annuler</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── ACCESS ── -->
    <div class="tab-panel" id="p-access">
      <div class="grid-2" style="align-items:start;">
        <div class="panel">
          <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>Attribuer des accès</div></div>
          <div class="panel-body">
            <div class="field" style="margin-bottom:16px;"><label>Sélectionner un utilisateur</label>
              <select id="access-user" onchange="loadAccess()"><option value="">— Choisir —</option></select>
            </div>
            <div id="access-list"><div class="empty-state" style="padding:20px 0;">Sélectionnez un utilisateur</div></div>
          </div>
        </div>
        <div class="panel">
          <div class="panel-header"><div class="panel-title">Guide</div></div>
          <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
            <div style="padding:14px;background:rgba(61,142,240,0.07);border:1px solid rgba(61,142,240,0.15);border-radius:10px;font-size:13px;color:var(--muted);line-height:1.6;"><div style="color:var(--accent2);font-weight:500;margin-bottom:5px;">1. Choisir un utilisateur</div>Sélectionnez dans la liste ci-contre.</div>
            <div style="padding:14px;background:rgba(34,214,94,0.06);border:1px solid rgba(34,214,94,0.15);border-radius:10px;font-size:13px;color:var(--muted);line-height:1.6;"><div style="color:var(--green);font-weight:500;margin-bottom:5px;">2. Basculer les accès</div>Activez ou désactivez chaque salle. Les changements sont immédiats.</div>
            <div style="padding:14px;background:rgba(240,168,52,0.06);border:1px solid rgba(240,168,52,0.15);border-radius:10px;font-size:13px;color:var(--muted);line-height:1.6;"><div style="color:var(--amber);font-weight:500;margin-bottom:5px;">Sécurité</div>Les accès sont vérifiés côté serveur à chaque action.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── BADGES RFID ── -->
    <div class="tab-panel" id="p-enroll">
      <div class="panel" style="max-width: 600px;">
        <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="2" y="6" width="20" height="14" rx="2"/><rect x="2" y="6" width="20" height="4"/></svg>Enrôler un badge RFID</div></div>
        <div class="panel-body">
          <!-- État initial -->
          <div id="enroll-initial">
            <div class="field" style="margin-bottom:16px;"><label>Sélectionner un utilisateur</label>
              <select id="enroll-user"><option value="">— Choisir —</option></select>
            </div>
            <div class="form-actions">
              <button class="btn-primary" id="btn-start-enroll" onclick="startEnroll()" disabled style="opacity:0.5;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="6" width="20" height="14" rx="2"/><rect x="2" y="6" width="20" height="4"/></svg>Démarrer l'enrôlement
              </button>
            </div>
          </div>

          <!-- État d'attente -->
          <div id="enroll-waiting" style="display:none;text-align:center;padding:40px 20px;">
            <div style="margin-bottom:24px;">
              <div class="spinner" style="width:50px;height:50px;border:3px solid rgba(255,255,255,0.1);border-top:3px solid var(--accent);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto;"></div>
            </div>
            <div style="font-size:15px;font-weight:500;margin-bottom:8px;">Veuillez scanner le badge...</div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:24px;">Présentez le badge RFID au lecteur</div>
            <button class="btn-secondary" onclick="cancelEnroll()">Annuler</button>
          </div>

          <!-- État succès -->
          <div id="enroll-success" style="display:none;text-align:center;padding:40px 20px;">
            <div style="margin-bottom:20px;">
              <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="2" stroke-linecap="round" style="margin:0 auto;"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="font-size:16px;font-weight:600;color:var(--green);margin-bottom:8px;">Badge enregistré avec succès!</div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:24px;">L'utilisateur peut maintenant accéder aux serrures</div>
            <button class="btn-primary" onclick="resetEnroll()">Enrôler un autre badge</button>
          </div>

          <!-- État erreur -->
          <div id="enroll-error" style="display:none;padding:20px;background:rgba(240,90,90,0.08);border:1px solid rgba(240,90,90,0.2);border-radius:12px;text-align:center;">
            <div style="font-size:14px;color:var(--red);font-weight:500;margin-bottom:8px;">Erreur lors de l'enrôlement</div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:16px;" id="enroll-error-msg"></div>
            <button class="btn-secondary" onclick="resetEnroll()">Recommencer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── LOGS ── -->
    <div class="tab-panel" id="p-logs">
      <div class="panel">
        <div class="panel-header"><div class="panel-title"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Journal complet</div><span class="panel-count" id="lc-all">—</span></div>
        <div class="panel-body" id="all-logs" style="max-height:560px;overflow-y:auto;"><div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div></div>
      </div>
    </div>
    <!-- ── DISPOSITIFS IoT ── -->
    <div class="tab-panel" id="p-devices">

      <!-- Toolbar : résumé + bouton refresh manuel -->
      <div class="devices-toolbar">
        <div class="devices-summary" id="dev-summary">
          <span class="ds-pill online"><span class="ds-pill-dot"></span><span id="dev-count-online">—</span> En ligne</span>
          <span class="ds-pill warning"><span class="ds-pill-dot"></span><span id="dev-count-warning">—</span> Instable</span>
          <span class="ds-pill offline"><span class="ds-pill-dot"></span><span id="dev-count-offline">—</span> Hors ligne</span>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
          <span class="next-refresh" id="dev-next-refresh">Actualisation dans 5 s</span>
          <button onclick="manualRefreshDevices()" title="Actualiser maintenant" style="background:rgba(255,255,255,0.05);border:1px solid var(--border2);border-radius:9px;padding:6px 8px;cursor:pointer;color:var(--muted);display:flex;align-items:center;gap:6px;font-size:12px;transition:all 0.18s;" onmouseover="this.style.background='rgba(255,255,255,0.09)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
            <div class="refresh-ring" id="dev-refresh-ring">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            </div>
            Rafraîchir
          </button>
        </div>
      </div>

      <!-- Grille de cartes -->
      <div class="devices-grid" id="devices-grid">
        <!-- skeletons affichés avant le premier chargement -->
        <div class="device-skeleton"></div>
        <div class="device-skeleton" style="opacity:.7"></div>
        <div class="device-skeleton" style="opacity:.4"></div>
      </div>
    </div>

    <!-- ── EMPREINTES DIGITALES ── -->
    <div class="tab-panel" id="p-fingerprints">
      <div class="grid-2" style="align-items:start;">

        <!-- Liste des empreintes -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
              Empreintes enregistrées
            </div>
            <span class="panel-count" id="fp-count">—</span>
          </div>
          <div class="panel-body" id="fp-list" style="max-height:480px;overflow-y:auto;">
            <div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div>
          </div>
        </div>

        <!-- Enrôler une empreinte -->
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Enrôler une empreinte
            </div>
          </div>
          <div class="panel-body">

            <!-- État initial -->
            <div id="fp-enroll-initial">
              <div class="field" style="margin-bottom:14px;">
                <label>Utilisateur</label>
                <select id="fp-enroll-user" onchange="checkFingerEnrollReady()">
                  <option value="">— Choisir un utilisateur —</option>
                </select>
              </div>
              <div class="field" style="margin-bottom:18px;">
                <label>Salle (serrure)</label>
                <select id="fp-enroll-salle" onchange="checkFingerEnrollReady()">
                  <option value="">— Choisir une salle —</option>
                </select>
              </div>
              <div class="form-actions">
                <button class="btn-primary" id="btn-start-finger-enroll" onclick="startFingerEnroll()" disabled style="opacity:0.5;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571"/></svg>
                  Démarrer l'enrôlement
                </button>
              </div>
              <div style="margin-top:20px;padding:14px;background:rgba(167,139,250,0.07);border:1px solid rgba(167,139,250,0.18);border-radius:10px;font-size:12.5px;color:var(--muted);line-height:1.6;">
                <div style="color:var(--purple);font-weight:500;margin-bottom:6px;">Comment ça fonctionne</div>
                1. Sélectionnez l'utilisateur et la salle.<br>
                2. La serrure passe en mode enrôlement empreinte (LED violette clignotante).<br>
                3. L'utilisateur pose son doigt deux fois sur le capteur.<br>
                4. Le système revient automatiquement en mode accès.
              </div>
            </div>

            <!-- Attente -->
            <div id="fp-enroll-waiting" style="display:none;text-align:center;padding:40px 20px;">
              <div style="margin-bottom:20px;">
                <div style="width:56px;height:56px;margin:0 auto;border:3px solid rgba(167,139,250,0.15);border-top:3px solid var(--purple);border-radius:50%;animation:spin 1s linear infinite;"></div>
              </div>
              <div style="font-size:15px;font-weight:500;margin-bottom:8px;color:var(--purple);">Posez votre doigt sur le capteur…</div>
              <div style="font-size:13px;color:var(--muted);margin-bottom:8px;">La LED de la serrure clignote en violet</div>
              <div style="font-size:12px;color:var(--muted2);margin-bottom:24px;">Deux captures successives sont nécessaires</div>
              <button class="btn-secondary" onclick="cancelFingerEnroll()">Annuler</button>
            </div>

            <!-- Succès -->
            <div id="fp-enroll-success" style="display:none;text-align:center;padding:40px 20px;">
              <div style="margin-bottom:20px;">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="2" stroke-linecap="round" style="margin:0 auto;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div style="font-size:16px;font-weight:600;color:var(--green);margin-bottom:8px;">Empreinte enregistrée !</div>
              <div style="font-size:13px;color:var(--muted);margin-bottom:24px;">L'utilisateur peut maintenant accéder via son empreinte digitale</div>
              <button class="btn-primary" onclick="resetFingerEnroll()">Enrôler une autre empreinte</button>
            </div>

            <!-- Erreur -->
            <div id="fp-enroll-error" style="display:none;padding:20px;background:rgba(240,90,90,0.08);border:1px solid rgba(240,90,90,0.2);border-radius:12px;text-align:center;">
              <div style="font-size:14px;color:var(--red);font-weight:500;margin-bottom:8px;">Erreur lors de l'enrôlement</div>
              <div style="font-size:12px;color:var(--muted);margin-bottom:16px;" id="fp-enroll-error-msg"></div>
              <button class="btn-secondary" onclick="resetFingerEnroll()">Recommencer</button>
            </div>

          </div>
        </div>
      </div>
    </div>
</div>

  </main>
</div>

<script>
const ADMIN_ID = <?= (int)$_SESSION['user_id'] ?>;
const NOM = <?= json_encode($_SESSION['nom']) ?>;

document.getElementById('user-avatar').textContent = NOM.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);
function updateTime(){document.getElementById('current-time').textContent=new Date().toLocaleDateString('fr-FR',{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});}
updateTime();setInterval(updateTime,60000);

// Toast (success | error | warning)
function toast(msg, type='success'){
  const c=document.getElementById('toasts'), t=document.createElement('div');
  t.className=`toast ${type}`;
  const icons = {
    success: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`,
    error:   `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f05a5a" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    warning: `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f0a834" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`
  };
  t.innerHTML=`${icons[type]||icons.success}<span>${msg}</span>`;
  c.appendChild(t);
  setTimeout(()=>{t.style.animation='toastOut 0.28s ease forwards';setTimeout(()=>t.remove(),280);},3500);
}

// ── Dispositifs IoT ──────────────────────────────────────────────────────────
const prevDeviceStates   = {};
const notifiedOffline    = new Set();
let   devicePollInterval = null;
let   devCountdown       = 5;

// ── SVG Icons ────────────────────────────────────────────────────────────────
const ICON_WIFI_ON = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="1.8" stroke-linecap="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>`;
const ICON_WIFI_WN = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f0a834" stroke-width="1.8" stroke-linecap="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>`;
const ICON_WIFI_OF = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f05a5a" stroke-width="1.8" stroke-linecap="round"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a11 11 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.56 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>`;
const ICON_CLOCK   = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`;
const ICON_LOCK_O  = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#22d65e" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>`;
const ICON_LOCK_C  = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="opacity:.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`;
const ICON_ENROLL  = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="6" width="20" height="14" rx="2"/><rect x="2" y="6" width="20" height="4"/><line x1="12" y1="13" x2="12" y2="17"/><line x1="10" y1="15" x2="14" y2="15"/></svg>`;
const ICON_CHECK   = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`;

/** Formate diff_sec en texte lisible */
function formatDiff(sec) {
  if (sec === null || sec < 0) return 'jamais vu';
  if (sec < 5)   return 'à l\'instant';
  if (sec < 60)  return `il y a ${sec} s`;
  if (sec < 3600){ const m = Math.floor(sec/60), s = sec % 60; return `il y a ${m} min${s > 0 ? ' ' + s + ' s' : ''}`; }
  return `il y a ${Math.floor(sec/3600)} h`;
}

/**
 * Construit le HTML complet d'une carte dispositif.
 * Inclut : connectivité, état serrure, mode, bouton changement de mode.
 */
function buildDeviceCard(d) {
  const cs      = d.device_status;
  const isOpen  = d.etat === 1;
  const isEnroll= d.mode === 'enroll';
  const wLabel  = cs==='online' ? 'En ligne' : cs==='warning' ? 'Instable' : 'Hors ligne';
  const wIcon   = cs==='online' ? ICON_WIFI_ON : cs==='warning' ? ICON_WIFI_WN : ICON_WIFI_OF;
  const lockIcon= isOpen ? ICON_LOCK_O : ICON_LOCK_C;
  const modeLbl = isEnroll ? 'Mode enrôlement' : 'Mode accès';
  const modeBtn = isEnroll
    ? `<button class="mode-btn to-access" onclick="setRoomMode(${d.numero_salle},'access',this)">${ICON_CHECK} Désactiver l'enrôlement</button>`
    : `<button class="mode-btn to-enroll" onclick="setRoomMode(${d.numero_salle},'enroll',this)">${ICON_ENROLL} Mode enrôlement</button>`;

  return `
  <div class="device-card ${cs}${isEnroll?' mode-enroll':''}" id="dev-card-${d.numero_salle}">
    <div class="device-card-top">
      <div class="device-icon-wrap">${wIcon}</div>
      <div class="device-badge"><span class="device-badge-dot"></span>${wLabel}</div>
    </div>
    <div class="device-name">Salle ${d.numero_salle}</div>
    <div class="device-id">ESP32 · serrure #${d.serrure_id}</div>
    <hr class="device-divider">
    <div class="device-states-row">
      <span class="lock-state-badge ${isOpen?'open':'close'}">${lockIcon} ${isOpen?'Ouverte':'Fermée'}</span>
      <span class="mode-badge ${isEnroll?'enroll':'access'}"><span class="mode-badge-dot"></span>${modeLbl}</span>
    </div>
    <div class="device-meta" style="margin-bottom:12px;">
      <div class="device-meta-row">${ICON_CLOCK}<span>Dernier signal :</span><span class="device-meta-val last-seen">${formatDiff(d.diff_sec)}</span></div>
    </div>
    <div class="mode-btn-wrap">${modeBtn}</div>
  </div>`;
}

/**
 * Met à jour les éléments dynamiques d'une carte existante sans la reconstruire.
 */
function patchDeviceCard(el, d) {
  const cs      = d.device_status;
  const isOpen  = d.etat === 1;
  const isEnroll= d.mode === 'enroll';

  el.className = `device-card ${cs}${isEnroll?' mode-enroll':''}`;

  const wIcon  = cs==='online'?ICON_WIFI_ON:cs==='warning'?ICON_WIFI_WN:ICON_WIFI_OF;
  const wLabel = cs==='online'?'En ligne':cs==='warning'?'Instable':'Hors ligne';
  el.querySelector('.device-icon-wrap').innerHTML = wIcon;
  el.querySelector('.device-badge').innerHTML     = `<span class="device-badge-dot"></span>${wLabel}`;
  el.querySelector('.last-seen').textContent      = formatDiff(d.diff_sec);

  const lockEl = el.querySelector('.lock-state-badge');
  lockEl.className = `lock-state-badge ${isOpen?'open':'close'}`;
  lockEl.innerHTML = `${isOpen?ICON_LOCK_O:ICON_LOCK_C} ${isOpen?'Ouverte':'Fermée'}`;

  const modeEl = el.querySelector('.mode-badge');
  modeEl.className = `mode-badge ${isEnroll?'enroll':'access'}`;
  modeEl.innerHTML = `<span class="mode-badge-dot"></span>${isEnroll?'Mode enrôlement':'Mode accès'}`;

  const btn = el.querySelector('.mode-btn');
  if (btn && !btn.disabled) {
    if (isEnroll) {
      btn.className = 'mode-btn to-access';
      btn.innerHTML = `${ICON_CHECK} Désactiver l'enrôlement`;
      btn.onclick   = () => setRoomMode(d.numero_salle, 'access', btn);
    } else {
      btn.className = 'mode-btn to-enroll';
      btn.innerHTML = `${ICON_ENROLL} Mode enrôlement`;
      btn.onclick   = () => setRoomMode(d.numero_salle, 'enroll', btn);
    }
  }
}

/** Met à jour les compteurs du résumé */
function updateDeviceSummary(devices) {
  const c = { online:0, warning:0, offline:0 };
  devices.forEach(d => c[d.device_status] = (c[d.device_status]||0) + 1);
  document.getElementById('dev-count-online').textContent  = c.online;
  document.getElementById('dev-count-warning').textContent = c.warning;
  document.getElementById('dev-count-offline').textContent = c.offline;
}

/** Charge et rend les cartes. Détecte les transitions importantes. */
function loadDevices() {
  const ring = document.getElementById('dev-refresh-ring');
  if (ring) { ring.classList.add('spinning'); setTimeout(() => ring.classList.remove('spinning'), 800); }

  fetch('get_rooms_full_state.php')
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(devices => {
      const grid = document.getElementById('devices-grid');
      if (!Array.isArray(devices) || !devices.length) {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1">Aucun dispositif trouvé</div>';
        return;
      }

      updateDeviceSummary(devices);

      devices.forEach(d => {
        const prev = prevDeviceStates[d.numero_salle] || {};

        // Connectivité → offline
        if (prev.status && prev.status !== 'offline' && d.device_status === 'offline') {
          if (!notifiedOffline.has(d.numero_salle)) {
            notifiedOffline.add(d.numero_salle);
            toast(`⚠️ Salle ${d.numero_salle} est hors ligne`, 'error');
          }
        }
        if (d.device_status !== 'offline' && notifiedOffline.has(d.numero_salle)) {
          notifiedOffline.delete(d.numero_salle);
          if (prev.status === 'offline') toast(`✅ Salle ${d.numero_salle} est de nouveau en ligne`, 'success');
        }
        // Retour automatique en mode accès (badge scanné)
        if (prev.mode === 'enroll' && d.mode === 'access') {
          toast(`🔓 Salle ${d.numero_salle} : enrôlement terminé`, 'warning');
        }

        prevDeviceStates[d.numero_salle] = { status: d.device_status, mode: d.mode };
      });

      const existingIds = new Set([...grid.querySelectorAll('.device-card')].map(el => el.id));

      devices.forEach(d => {
        const cardId   = `dev-card-${d.numero_salle}`;
        const existing = document.getElementById(cardId);
        if (existing) {
          patchDeviceCard(existing, d);
          existingIds.delete(cardId);
        } else {
          const tmp = document.createElement('div');
          tmp.innerHTML = buildDeviceCard(d).trim();
          grid.appendChild(tmp.firstChild);
        }
      });

      existingIds.forEach(id => { const el = document.getElementById(id); if (el) el.remove(); });
    })
    .catch(err => { console.error('loadDevices:', err); toast('Impossible de contacter le serveur', 'error'); });
}

/**
 * Bascule le mode d'une serrure.
 * Appelé par les boutons "Mode enrôlement" / "Désactiver l'enrôlement".
 */
function setRoomMode(numero, mode, btn) {
  btn.disabled = true;
  const origHTML = btn.innerHTML;
  btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> En cours…`;

  fetch(`set_mode.php?numero=${encodeURIComponent(numero)}&mode=${encodeURIComponent(mode)}`)
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
    .then(d => {
      if (d.success) {
        const label = mode==='enroll' ? 'Mode enrôlement activé' : 'Mode accès rétabli';
        toast(`Salle ${numero} — ${label}`, mode==='enroll'?'warning':'success');
        loadDevices();
      } else {
        toast(d.message || 'Erreur', 'error');
        btn.innerHTML = origHTML; btn.disabled = false;
      }
    })
    .catch(() => { toast('Erreur réseau', 'error'); btn.innerHTML = origHTML; btn.disabled = false; });
}

/** Démarre le polling toutes les 5 secondes */
function startDevicePolling() {
  if (devicePollInterval) return;
  loadDevices(); devCountdown = 5; _updateDevCountdown();
  devicePollInterval = setInterval(() => {
    devCountdown--; _updateDevCountdown();
    if (devCountdown <= 0) { loadDevices(); devCountdown = 5; }
  }, 1000);
}

/** Arrête le polling */
function stopDevicePolling() {
  if (devicePollInterval) { clearInterval(devicePollInterval); devicePollInterval = null; }
}

function _updateDevCountdown() {
  const el = document.getElementById('dev-next-refresh');
  if (el) el.textContent = devCountdown <= 0 ? 'Actualisation…' : `Actualisation dans ${devCountdown} s`;
}

/** Rafraîchissement manuel */
function manualRefreshDevices() { devCountdown = 5; loadDevices(); }

// ── Initial load ─────────────────────────────────────────────────────────────

// Tabs
const navItems = document.querySelectorAll('.nav-item');
function tab(name){
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById(`p-${name}`).classList.add('active');
  document.getElementById(`tb-${name}`).classList.add('active');
  navItems.forEach(n=>{n.classList.remove('active'); if(n.getAttribute('onclick')?.includes(name)) n.classList.add('active');});
  if(name==='logs')    loadAllLogs();
  if(name==='users')   loadUsers();
  if(name==='access')  {loadUsers();loadRooms();}
  if(name==='enroll')  {loadUsers();resetEnroll();}
  // Démarrer / arrêter le polling IoT selon l'onglet actif
  if(name==='devices') startDevicePolling();
  else                 stopDevicePolling();
  if(name==='fingerprints') { loadFingerprints(); loadSallesForFingerEnroll(); updateFingerEnrollUserSelect(); resetFingerEnroll(); }
}

// Rooms
let allRooms=[];
function loadRooms(){
  fetch('lock_get_state.php?all=1')
    .then(r=>r.json())
    .then(data=>{
      allRooms=data;
      const open=data.filter(r=>r.etat==1).length;
      document.getElementById('st-rooms').textContent=data.length;
      document.getElementById('st-open').textContent=open;
      document.getElementById('rc-overview').textContent=`${open}/${data.length}`;
      if(!data.length){document.getElementById('rooms-overview').innerHTML='<div class="empty-state">Aucune salle</div>';return;}
      let h='';
      data.forEach((r,i)=>{
        const o=r.etat==1,s=o?'open':'closed';
        h+=`<div class="room-card ${s}" style="animation:fadeInUp .3s ease ${i*.05}s both">
          <div class="room-info"><div class="room-indicator ${s}"></div><div>
            <div class="room-name">Salle ${r.numero_salle}</div>
            <div class="room-status ${s}">${o?'Ouverte':'Fermée'}</div>
          </div></div>
          <button class="toggle-btn ${o?'close-action':'open-action'}" data-state="${o?1:0}" onclick="toggleRoom(${r.numero_salle},this)">${o?'Fermer':'Ouvrir'}</button>
        </div>`;
      });
      document.getElementById('rooms-overview').innerHTML=h;
    })
    .catch(()=>{document.getElementById('rooms-overview').innerHTML='<div class="empty-state">Erreur de chargement</div>';});
}

function toggleRoom(salle,btn){
  const o=parseInt(btn.dataset.state)===1,action=o?'close':'open';
  btn.disabled=true;btn.textContent='…';
  fetch(`lock_toggle.php?user_id=${ADMIN_ID}&numero=${salle}&action=${action}`)
    .then(r=>r.json())
    .then(d=>{
      if(d.success){toast(`Salle ${salle} ${o?'fermée':'ouverte'}`,'success');loadRooms();loadOverviewLogs();}
      else{toast(d.message||'Erreur','error');btn.disabled=false;}
    })
    .catch(()=>{toast('Erreur réseau','error');btn.disabled=false;});
}

// Logs helper
function renderLogs(data,containerId,countId){
  if(countId) document.getElementById(countId).textContent=`${data.length} entrées`;
  if(!data.length){document.getElementById(containerId).innerHTML='<div class="empty-state">Aucune entrée</div>';return;}
  let h='';
  data.forEach((log,i)=>{
    const o=['open','ouverture','ouverte'].includes((log.action||'').toLowerCase());
    const d=new Date(log.date_action),ts=isNaN(d)?log.date_action:d.toLocaleString('fr-FR',{hour:'2-digit',minute:'2-digit',day:'2-digit',month:'short'});
    h+=`<div class="log-entry" style="animation-delay:${i*.025}s">
      <div class="log-dot ${o?'open':'close'}"></div>
      <div class="log-text"><div class="log-main"><strong>${log.nom}</strong> · Salle ${log.numero_salle}</div><div class="log-time">${ts}</div></div>
      <span class="log-tag ${o?'open':'close'}">${o?'Ouvert':'Fermé'}</span>
    </div>`;
  });
  document.getElementById(containerId).innerHTML=h;
}

function loadOverviewLogs(){
  fetch('log_get.php')
    .then(r=>r.json())
    .then(data=>{document.getElementById('st-logs').textContent=data.length;renderLogs(data,'logs-overview','lc-overview');})
    .catch(()=>{});
}

function loadAllLogs(){
  document.getElementById('all-logs').innerHTML='<div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div>';
  fetch('log_get.php').then(r=>r.json()).then(data=>renderLogs(data,'all-logs','lc-all')).catch(()=>{});
}

// Users
let cachedUsers=[];
function loadUsers(){
  fetch('admin_get_users.php')
    .then(r=>r.json())
    .then(data=>{
      cachedUsers=data;
      document.getElementById('st-users').textContent=data.length;
      document.getElementById('uc').textContent=`${data.length} utilisateurs`;
      let rows='';
      data.forEach(u=>{
        const ini=u.nom.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);
        rows+=`<tr>
          <td><span class="u-avatar">${ini}</span>${u.nom}</td>
          <td><span class="role-tag ${u.role}">${u.role==='admin'?'Admin':'Utilisateur'}</span></td>
          <td style="color:var(--muted)">${u.nb_salles}</td>
        </tr>`;
      });
      document.getElementById('users-tbody').innerHTML=rows||'<tr><td colspan="3"><div class="empty-state">Aucun utilisateur</div></td></tr>';

      // Remplir select accès
      const sel=document.getElementById('access-user'), prev=sel.value;
      sel.innerHTML='<option value="">— Choisir —</option>';
      data.filter(u=>u.role==='user').forEach(u=>{sel.innerHTML+=`<option value="${u.id}">${u.nom}</option>`;});
      if(prev) sel.value=prev;
      
      // Mettre à jour le select enrôlement
      updateEnrollUserSelect();
    })
    .catch(()=>toast('Erreur chargement utilisateurs','error'));
}

function createUser(){
  const nom=document.getElementById('new-nom').value.trim();
  const pin=document.getElementById('new-pin').value.trim();
  const role=document.getElementById('new-role').value;
  if(!nom||!pin){toast('Nom et PIN requis','error');return;}
  if(pin.length<4){toast('PIN trop court','error');return;}
  const btn=document.getElementById('btn-create');
  btn.disabled=true;btn.textContent='Création…';
  const fd=new FormData();fd.append('nom',nom);fd.append('pin',pin);fd.append('role',role);
  fetch('admin_create_user.php',{method:'POST',body:fd})
    .then(r=>r.json())
    .then(d=>{
      if(d.success){toast(d.message,'success');document.getElementById('new-nom').value='';document.getElementById('new-pin').value='';loadUsers();}
      else toast(d.message,'error');
    })
    .catch(()=>toast('Erreur réseau','error'))
    .finally(()=>{btn.disabled=false;btn.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Créer';});
}

// Access
function loadAccess(){
  const uid=document.getElementById('access-user').value;
  if(!uid){document.getElementById('access-list').innerHTML='<div class="empty-state" style="padding:20px 0;">Sélectionnez un utilisateur</div>';return;}
  document.getElementById('access-list').innerHTML='<div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div>';

  // Récupérer les accès actuels via les logs (on utilise get_state + filtrage simple)
  // Pour une implémentation complète, ajouter une API admin_get_access.php
  // Ici on affiche toutes les salles avec toggle
  let h='';
  allRooms.forEach(r=>{
    h+=`<div class="access-item">
      <div>
        <div style="font-size:13.5px;font-weight:500;">Salle ${r.numero_salle}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">${r.etat==1?'Actuellement ouverte':'Actuellement fermée'}</div>
      </div>
      <div class="switch" id="sw-${r.salle_id}" onclick="toggleAccess(this,${uid},${r.salle_id},${r.numero_salle})"></div>
    </div>`;
  });
  document.getElementById('access-list').innerHTML=h||'<div class="empty-state">Aucune salle</div>';
}

function toggleAccess(el,userId,salleId,salleNum){
  const isOn=el.classList.contains('on');
  const action=isOn?'revoke':'grant';
  el.style.opacity='0.5';el.style.pointerEvents='none';
  const fd=new FormData();fd.append('user_id',userId);fd.append('salle_id',salleId);fd.append('action',action);
  fetch('admin_assign_access.php',{method:'POST',body:fd})
    .then(r=>r.json())
    .then(d=>{
      if(d.success){el.classList.toggle('on');toast(`Salle ${salleNum} : ${d.message}`,'success');}
      else toast(d.message,'error');
    })
    .catch(()=>toast('Erreur réseau','error'))
    .finally(()=>{el.style.opacity='';el.style.pointerEvents='';});
}

// Enrollment
let enrollWatchInterval = null;
let enrollUserId = null;

function updateEnrollUserSelect(){
  const sel = document.getElementById('enroll-user'), prev = sel.value;
  sel.innerHTML = '<option value="">— Choisir —</option>';
  cachedUsers.filter(u => u.role === 'user').forEach(u => {
    sel.innerHTML += `<option value="${u.id}">${u.nom}</option>`;
  });
  if(prev) sel.value = prev;
  sel.onchange = () => {
    document.getElementById('btn-start-enroll').disabled = !sel.value;
    document.getElementById('btn-start-enroll').style.opacity = sel.value ? '1' : '0.5';
  };
}

function startEnroll(){
  const userId = document.getElementById('enroll-user').value;
  if(!userId){toast('Sélectionnez un utilisateur','error');return;}
  
  const btn = document.getElementById('btn-start-enroll');
  btn.disabled = true;
  
  const fd = new FormData();
  fd.append('user_id', userId);
  
  fetch('admin_start_enroll.php', {method:'POST', body:fd})
    .then(r => {
      if(!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    })
    .then(d => {
      if(d.success){
        enrollUserId = userId;
        showEnrollState('waiting');
        watchEnrollStatus();
      } else {
        toast(d.message || 'Erreur serveur', 'error');
        btn.disabled = false;
      }
    })
    .catch((err) => {
      console.error('Erreur:', err);
      toast('Erreur réseau ou serveur', 'error');
      btn.disabled = false;
    });
}

function watchEnrollStatus(){
  if(enrollWatchInterval) clearInterval(enrollWatchInterval);
  
  enrollWatchInterval = setInterval(() => {
    fetch('check_enroll.php')
      .then(r => r.json())
      .then(d => {
        if(!d.enroll){
          // Enrôlement terminé
          clearInterval(enrollWatchInterval);
          enrollWatchInterval = null;
          
          // Attendre un peu avant d'afficher le succès
          setTimeout(() => {
            showEnrollState('success');
            toast('Badge enregistré avec succès!', 'success');
          }, 300);
        }
      })
      .catch(() => {});
  }, 500); // Réduit de 800ms à 500ms pour plus de réactivité
}

function showEnrollState(state){
  document.getElementById('enroll-initial').style.display = state === 'initial' ? 'block' : 'none';
  document.getElementById('enroll-waiting').style.display = state === 'waiting' ? 'block' : 'none';
  document.getElementById('enroll-success').style.display = state === 'success' ? 'block' : 'none';
  document.getElementById('enroll-error').style.display = state === 'error' ? 'block' : 'none';
}

function cancelEnroll(){
  if(enrollWatchInterval) clearInterval(enrollWatchInterval);
  enrollWatchInterval = null;
  enrollUserId = null;
  resetEnroll();
  toast('Enrôlement annulé', 'error');
}

function resetEnroll(){
  enrollUserId = null;
  if(enrollWatchInterval) clearInterval(enrollWatchInterval);
  enrollWatchInterval = null;
  showEnrollState('initial');
  document.getElementById('enroll-user').value = '';
  document.getElementById('btn-start-enroll').disabled = true;
  document.getElementById('btn-start-enroll').style.opacity = '0.5';
}

// Init
loadRooms();
loadOverviewLogs();
loadUsers();
updateEnrollUserSelect();
// Polling serrures + logs toutes les 15 s
setInterval(()=>{loadRooms();loadOverviewLogs();},15000);
// Note : le polling IoT (loadDevices) est géré par startDevicePolling/stopDevicePolling
// selon l'onglet actif — pas de setInterval global ici.


// ═══════════════════════════════════════════════════════════
// GESTION DES EMPREINTES DIGITALES
// ═══════════════════════════════════════════════════════════

let fingerEnrollWatchInterval = null;
let fingerEnrollSalleId       = null;
let fingerEnrollUserId        = null;

function loadFingerprints() {
  document.getElementById('fp-list').innerHTML =
    '<div class="skeleton"></div><div class="skeleton" style="opacity:.7"></div>';

  fetch('admin_finger_get.php')
    .then(r => r.json())
    .then(data => {
      document.getElementById('fp-count').textContent =
        data.length + ' empreinte' + (data.length !== 1 ? 's' : '');

      if (!data.length) {
        document.getElementById('fp-list').innerHTML =
          '<div class="empty-state">Aucune empreinte enregistrée</div>';
        return;
      }

      let html = '';
      data.forEach((emp, i) => {
        const d   = new Date(emp.date_creation);
        const ds  = isNaN(d) ? emp.date_creation
          : d.toLocaleDateString('fr-FR', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
        const ini = emp.nom.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);

        html += `<div class="access-item" style="animation:fadeInUp .3s ease ${i*.04}s both">
          <div style="display:flex;align-items:center;gap:12px;">
            <span class="u-avatar">${ini}</span>
            <div>
              <div style="font-size:13.5px;font-weight:500;">${emp.nom}</div>
              <div style="font-size:11px;color:var(--muted);margin-top:2px;">ID #${emp.fingerprint_id} &nbsp;·&nbsp; ${ds}</div>
            </div>
          </div>
          <div style="display:flex;gap:8px;align-items:center;">
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:6px;font-size:10.5px;font-weight:600;background:rgba(167,139,250,0.12);border:1px solid rgba(167,139,250,0.22);color:var(--purple);letter-spacing:.04em;">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/></svg>
              Slot ${emp.fingerprint_id}
            </span>
            <button class="toggle-btn close-action"
              onclick="deleteFingerprint(${emp.id},'${emp.nom.replace(/'/g,"\'")}',${emp.fingerprint_id},this)">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
              </svg>
              Supprimer
            </button>
          </div>
        </div>`;
      });

      document.getElementById('fp-list').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('fp-list').innerHTML =
        '<div class="empty-state">Erreur de chargement</div>';
    });
}

function deleteFingerprint(id, nom, fingerprintId, btn) {
  if (!confirm('Supprimer l\'empreinte #' + fingerprintId + ' de ' + nom + ' ?')) return;
  btn.disabled = true; btn.innerHTML = '…';
  const fd = new FormData(); fd.append('id', id);
  fetch('admin_finger_delete.php', {method:'POST',body:fd})
    .then(r => r.json())
    .then(d => {
      if (d.success) { toast(d.message,'success'); loadFingerprints(); }
      else { toast(d.message||'Erreur','error'); btn.disabled=false; btn.innerHTML='Supprimer'; }
    })
    .catch(() => { toast('Erreur réseau','error'); btn.disabled=false; btn.innerHTML='Supprimer'; });
}

function startFingerEnroll() {
  const userId  = document.getElementById('fp-enroll-user').value;
  const salleId = document.getElementById('fp-enroll-salle').value;
  if (!userId || !salleId) { toast('Sélectionnez un utilisateur et une salle','error'); return; }

  const btn = document.getElementById('btn-start-finger-enroll');
  btn.disabled = true;

  const fd = new FormData(); fd.append('user_id',userId); fd.append('salle_id',salleId);
  fetch('admin_finger_enroll_start.php',{method:'POST',body:fd})
    .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
    .then(d => {
      if (d.success) {
        fingerEnrollUserId  = userId;
        fingerEnrollSalleId = salleId;
        showFpEnrollState('waiting');
        watchFingerEnrollStatus();
      } else {
        toast(d.message||'Erreur serveur','error');
        btn.disabled = false;
      }
    })
    .catch(err => { console.error(err); toast('Erreur réseau','error'); btn.disabled=false; });
}

function watchFingerEnrollStatus() {
  if (fingerEnrollWatchInterval) clearInterval(fingerEnrollWatchInterval);
  fingerEnrollWatchInterval = setInterval(() => {
    if (!fingerEnrollSalleId) { clearInterval(fingerEnrollWatchInterval); return; }
    fetch('check_finger_enroll.php?salle_id=' + fingerEnrollSalleId)
      .then(r => r.json())
      .then(d => {
        if (!d.enrolling) {
          clearInterval(fingerEnrollWatchInterval);
          fingerEnrollWatchInterval = null;
          setTimeout(() => {
            showFpEnrollState('success');
            toast('Empreinte enregistrée avec succès !','success');
            loadFingerprints();
          }, 400);
        }
      })
      .catch(() => {});
  }, 600);
}

function showFpEnrollState(state) {
  ['initial','waiting','success','error'].forEach(s => {
    document.getElementById('fp-enroll-' + s).style.display = (s===state)?'block':'none';
  });
}

function cancelFingerEnroll() {
  if (fingerEnrollWatchInterval) clearInterval(fingerEnrollWatchInterval);
  fingerEnrollWatchInterval = null;

  const salleId = fingerEnrollSalleId || document.getElementById('fp-enroll-salle').value;
  if (salleId) {
    const fd = new FormData();
    fd.append('salle_id', salleId);

    fetch('cancel_enroll.php', {
      method: 'POST',
      body: fd
    })
    .then(r => r.ok ? r.json() : Promise.reject('HTTP ' + r.status))
    .then(d => {
      if (!d.success) {
        toast(d.message || 'Annulation non confirmée', 'error');
      }
    })
    .catch(err => {
      console.error(err);
      toast('Erreur annulation', 'error');
    });
  }

  fingerEnrollUserId = null; fingerEnrollSalleId = null;
  resetFingerEnroll();
  toast('Enrôlement annulé','warning');
}

function resetFingerEnroll() {
  fingerEnrollUserId = null; fingerEnrollSalleId = null;
  if (fingerEnrollWatchInterval) clearInterval(fingerEnrollWatchInterval);
  fingerEnrollWatchInterval = null;
  showFpEnrollState('initial');
  document.getElementById('fp-enroll-user').value  = '';
  document.getElementById('fp-enroll-salle').value = '';
  const btn = document.getElementById('btn-start-finger-enroll');
  btn.disabled = true; btn.style.opacity = '0.5';
}

function checkFingerEnrollReady() {
  const u = document.getElementById('fp-enroll-user').value;
  const s = document.getElementById('fp-enroll-salle').value;
  const btn = document.getElementById('btn-start-finger-enroll');
  btn.disabled = !(u && s); btn.style.opacity = (u && s) ? '1' : '0.5';
}

function loadSallesForFingerEnroll() {
  fetch('lock_get_state.php?all=1')
    .then(r => r.json())
    .then(salles => {
      const sel  = document.getElementById('fp-enroll-salle');
      const prev = sel.value;
      sel.innerHTML = '<option value="">— Choisir une salle —</option>';
      salles.forEach(s => {
        sel.innerHTML += '<option value="' + s.salle_id + '">Salle ' + s.numero_salle + '</option>';
      });
      if (prev) sel.value = prev;
    })
    .catch(() => {});
}

function updateFingerEnrollUserSelect() {
  const sel = document.getElementById('fp-enroll-user');
  if (!sel) return;
  const prev = sel.value;
  sel.innerHTML = '<option value="">— Choisir un utilisateur —</option>';
  cachedUsers.forEach(u => {
    sel.innerHTML += '<option value="' + u.id + '">' + u.nom + '</option>';
  });
  if (prev) sel.value = prev;
}
</script>
</body>
</html>
