<?php
/**
 * login.php
 * Page de connexion — affiche les erreurs via $_GET['error'].
 */
session_start();

// Déjà connecté → rediriger
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
switch ($_GET['error'] ?? '') {
    case 'champs_vides':           $error = 'Veuillez remplir tous les champs.'; break;
    case 'identifiants_incorrects': $error = 'Nom ou PIN incorrect.';             break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kinnesis — Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --c0: #060a12; --accent: #3d8ef0; --accent2: #5ba3ff;
  --green: #22d65e; --red: #f05a5a;
  --border: rgba(255,255,255,0.07); --border2: rgba(255,255,255,0.12);
  --text: #e8eef8; --muted: rgba(232,238,248,0.45); --muted2: rgba(232,238,248,0.22);
}
html, body { height: 100%; background: var(--c0); font-family: 'DM Sans', sans-serif; color: var(--text); overflow: hidden; }

/* Orbs */
.orb { position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; animation: drift 14s ease-in-out infinite alternate; }
.orb1 { width: 520px; height: 520px; background: radial-gradient(circle, rgba(61,142,240,0.22) 0%, transparent 70%); top: -160px; left: -100px; }
.orb2 { width: 400px; height: 400px; background: radial-gradient(circle, rgba(100,80,220,0.14) 0%, transparent 70%); bottom: -100px; right: -60px; animation-duration: 11s; animation-delay: -5s; }
@keyframes drift { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(30px,40px) scale(1.06); } }

.grid-bg {
  position: fixed; inset: 0; z-index: 0;
  background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
  background-size: 60px 60px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
}

.scene { position: relative; z-index: 10; height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }

.card {
  width: 100%; max-width: 420px;
  background: rgba(17,30,48,0.88);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 48px 44px 42px;
  backdrop-filter: blur(24px);
  box-shadow: 0 0 0 1px rgba(61,142,240,0.07), 0 40px 80px rgba(0,0,0,0.5);
  animation: slideUp 0.6s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes slideUp { from { opacity:0; transform:translateY(24px) scale(0.97); } to { opacity:1; transform:translateY(0) scale(1); } }

/* Logo */
.logo { display: flex; align-items: center; gap: 13px; margin-bottom: 36px; }
.logo-icon {
  width: 44px; height: 44px;
  background: linear-gradient(135deg, #1a3a6e, #2560c0);
  border: 1px solid rgba(91,163,255,0.3);
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 20px rgba(61,142,240,0.22);
  flex-shrink: 0;
}
.logo-text .brand { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: -0.02em; line-height: 1.1; }
.logo-text .tagline { font-size: 11px; color: var(--muted); letter-spacing: 0.05em; text-transform: uppercase; margin-top: 3px; }

/* Error */
.error-box {
  display: flex; align-items: center; gap: 9px;
  background: rgba(240,90,90,0.10);
  border: 1px solid rgba(240,90,90,0.25);
  border-radius: 10px;
  padding: 11px 14px;
  font-size: 13.5px;
  color: var(--red);
  margin-bottom: 22px;
  animation: slideUp 0.3s ease both;
}

/* Fields */
.field { margin-bottom: 16px; }
.field label { display: block; font-size: 11.5px; font-weight: 500; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
.field-inner { position: relative; display: flex; align-items: center; }
.field-icon { position: absolute; left: 13px; color: var(--muted); pointer-events: none; display: flex; align-items: center; }
.field input {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.09);
  border-radius: 11px;
  color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  padding: 13px 13px 13px 42px;
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.field input::placeholder { color: var(--muted2); }
.field input:focus { border-color: rgba(61,142,240,0.5); background: rgba(61,142,240,0.05); box-shadow: 0 0 0 3px rgba(61,142,240,0.10); }

/* Submit */
.btn-submit {
  width: 100%; margin-top: 26px; padding: 14px;
  background: linear-gradient(135deg, #2258b8, #3d8ef0);
  border: none; border-radius: 11px;
  color: white; font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 600;
  cursor: pointer; position: relative; overflow: hidden;
  transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
  box-shadow: 0 4px 22px rgba(61,142,240,0.35);
}
.btn-submit::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent); border-radius: inherit; }
.btn-submit:hover { filter: brightness(1.1); box-shadow: 0 6px 30px rgba(61,142,240,0.5); transform: translateY(-1px); }
.btn-submit:active { transform: translateY(0); }
.btn-inner { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 8px; }

/* Footer */
.card-footer { margin-top: 26px; padding-top: 18px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: center; gap: 7px; }
.status-dot { width: 7px; height: 7px; border-radius: 50%; background: #22d65e; box-shadow: 0 0 6px rgba(34,214,94,0.6); animation: pulse-dot 2s ease-in-out infinite; }
@keyframes pulse-dot { 0%,100% { box-shadow: 0 0 6px rgba(34,214,94,0.6); } 50% { box-shadow: 0 0 12px rgba(34,214,94,0.9); } }
.card-footer span { font-size: 12px; color: var(--muted); }
</style>
</head>
<body>

<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="grid-bg"></div>

<div class="scene">
  <div class="card">

    <div class="logo">
      <div class="logo-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
          <path d="M12 2L4 6V12C4 16.4 7.4 20.5 12 22C16.6 20.5 20 16.4 20 12V6L12 2Z" stroke="rgba(91,163,255,0.9)" stroke-width="1.5" stroke-linejoin="round"/>
          <path d="M9 12L11 14L15 10" stroke="#5ba3ff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="logo-text">
        <div class="brand">Kinnesis</div>
        <div class="tagline">Contrôle d'accès</div>
      </div>
    </div>

    <?php if ($error): ?>
    <div class="error-box">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form action="auth_login.php" method="POST">
      <div class="field">
        <label>Identifiant</label>
        <div class="field-inner">
          <span class="field-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" name="nom" placeholder="Votre nom" autocomplete="username" required>
        </div>
      </div>

      <div class="field">
        <label>Code PIN</label>
        <div class="field-inner">
          <span class="field-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input type="password" name="pin" placeholder="••••••" autocomplete="current-password" required>
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <span class="btn-inner">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Se connecter
        </span>
      </button>
    </form>

    <div class="card-footer">
      <div class="status-dot"></div>
      <span>Connexion chiffrée · Système sécurisé</span>
    </div>

  </div>
</div>

</body>
</html>
