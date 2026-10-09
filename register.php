<?php
require_once __DIR__ . '/includes/init.php';
if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create account · <?= APP_NAME ?></title>
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
      <h1>Join <span class="grad">millions</span> of<br>listeners worldwide.</h1>
      <p>Create a free account to upload your own music, import from any platform, and stream in high quality — everywhere.</p>
    </div>
  </div>
  <div class="auth-form-side">
    <div class="auth-card">
      <h2>Create your account</h2>
      <p class="sub">It's free and takes under a minute.</p>
      <div class="auth-error" id="err"></div>
      <form id="registerForm" autocomplete="on">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+" title="Letters, numbers, and underscores only">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required autocomplete="email">
        <label for="password">Password (min 8 characters)</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
          <span class="pw-toggle" data-pw-toggle="password" role="button" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
        <button type="submit" class="btn btn-primary" id="submitBtn">Create account</button>
      </form>
      <div class="auth-switch">Already have an account? <a href="<?= url('login.php') ?>">Sign in</a></div>
    </div>
  </div>
</div>
<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>