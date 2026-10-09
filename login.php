<?php
require_once __DIR__ . '/includes/init.php';
if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<script>window.BASE_URL = "<?= e(BASE_URL) ?>";</script>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-visual">
    <div class="auth-visual-content">
      <div class="brand" style="margin-bottom:30px">
        <div class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
        <span>SonicWave</span>
      </div>
      <h1>Your library,<br>ready when <span class="grad">you</span> are.</h1>
      <p>Sign in to pick up where you left off — playlists, queue, AI DJ, all waiting.</p>
      <div class="auth-features">
        <div class="auth-feature"><div class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div> Music, video, and podcasts — from 9 platforms</div>
        <div class="auth-feature"><div class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L9.5 8.5 3 11l6.5 2.5L12 20l2.5-6.5L21 11l-6.5-2.5z"/></svg></div> Personalised AI DJ and recommendations</div>
        <div class="auth-feature"><div class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></div> Download for offline listening</div>
      </div>
    </div>
  </div>
  <div class="auth-form-side">
    <div class="auth-card">
      <h2>Sign in</h2>
      <p class="sub">Enter your details to continue.</p>
      <div class="auth-success" id="ok"></div>
      <div class="auth-error" id="err"></div>
      <form id="loginForm" autocomplete="on">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required autocomplete="email">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" required autocomplete="current-password" minlength="8">
          <span class="pw-toggle" data-pw-toggle="password" role="button" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
        <button type="submit" class="btn btn-primary" id="submitBtn">Sign in</button>
      </form>
      <div class="auth-switch">New to SonicWave? <a href="<?= url('register.php') ?>">Create an account</a></div>
    </div>
  </div>
</div>
<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>