<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'Profile';
$extraJs = ['profile.js'];
$extraCss = ['profile.css'];
$pdo = db(); $uid = current_user()['id'];

$tracks   = (int)$pdo->query("SELECT COUNT(*) FROM tracks WHERE user_id = $uid")->fetchColumn();
$plays    = (int)$pdo->query("SELECT COALESCE(SUM(plays),0) FROM tracks WHERE user_id = $uid")->fetchColumn();
$likes    = (int)$pdo->query("SELECT COUNT(*) FROM likes WHERE user_id = $uid")->fetchColumn();
$sessions = (int)$pdo->query("SELECT COUNT(*) FROM play_history WHERE user_id = $uid")->fetchColumn();

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>

  <div class="profile-header">
    <div class="profile-avatar"><?= e(strtoupper(substr($user['username'], 0, 1))) ?></div>
    <div>
      <h1><?= e($user['username']) ?></h1>
      <p><?= e($user['email']) ?> · <span class="role-pill role-<?= e($user['role']) ?>"><?= e(ucfirst($user['role'])) ?></span></p>
    </div>
  </div>

  <div class="stat-grid">
    <div class="stat-card primary">
      <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
      <div class="label">Uploaded</div><div class="value"><?= $tracks ?></div>
    </div>
    <div class="stat-card warm">
      <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
      <div class="label">Liked</div><div class="value"><?= $likes ?></div>
    </div>
    <div class="stat-card cool">
      <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3" fill="currentColor"/></svg></div>
      <div class="label">Plays on your tracks</div><div class="value"><?= number_format($plays) ?></div>
    </div>
    <div class="stat-card success">
      <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <div class="label">Listening sessions</div><div class="value"><?= number_format($sessions) ?></div>
    </div>
  </div>

  <div class="tabs" id="profileTabs">
    <button class="tab active" data-tab="security">Security</button>
    <button class="tab" data-tab="history">Listening history</button>
  </div>

  <div class="tab-panel active" data-panel="security">
    <div class="card-form">
      <h3>Change password</h3>
      <p style="font-size:13px;color:var(--muted);margin-bottom:18px">Choose a strong password of at least 8 characters.</p>
      <div class="auth-error" id="pwErr" style="display:none"></div>
      <div class="auth-success" id="pwOk" style="display:none"></div>
      <form id="pwForm">
        <label>Current password</label>
        <div class="input-wrap">
          <input type="password" id="curPw" required autocomplete="current-password">
          <span class="pw-toggle" data-pw-toggle="curPw" role="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
        <label>New password</label>
        <div class="input-wrap">
          <input type="password" id="newPw" required minlength="8" autocomplete="new-password">
          <span class="pw-toggle" data-pw-toggle="newPw" role="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
        </div>
        <label>Confirm new password</label>
        <div class="input-wrap">
          <input type="password" id="conPw" required minlength="8" autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:10px">Update password</button>
      </form>
    </div>
  </div>

  <div class="tab-panel" data-panel="history">
    <div id="historyBox"><div class="empty"><p>Loading…</p></div></div>
  </div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';