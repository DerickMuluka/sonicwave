<?php
require_once __DIR__ . '/includes/init.php';
require_admin();
$pageTitle = 'Admin Dashboard';
$extraCss = ['admin.css'];
$extraJs  = ['admin.js'];
$section = $_GET['section'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<script>window.BASE_URL = "<?= e(BASE_URL) ?>";</script>
</head>
<body>
<div class="admin-wrap">
  <aside class="admin-sidebar">
    <a href="<?= url('admin.php') ?>" class="brand">
      <div class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
      <span>SonicWave</span><span class="admin-badge">Admin</span>
    </a>
    <nav class="nav-group">
      <div class="nav-title">Overview</div>
      <a href="?section=dashboard" class="nav-link <?= $section==='dashboard'?'active':'' ?>"><span class="nav-icon">◉</span> Dashboard</a>
    </nav>
    <nav class="nav-group">
      <div class="nav-title">Management</div>
      <a href="?section=users" class="nav-link <?= $section==='users'?'active':'' ?>"><span class="nav-icon">◐</span> Users</a>
      <a href="?section=tracks" class="nav-link <?= $section==='tracks'?'active':'' ?>"><span class="nav-icon">♪</span> Tracks</a>
    </nav>
    <nav class="nav-group">
      <div class="nav-title">Account</div>
      <a href="<?= url('index.php') ?>" class="nav-link"><span class="nav-icon">↩</span> Back to app</a>
      <a href="<?= url('logout.php') ?>" class="nav-link"><span class="nav-icon">⎋</span> Sign out</a>
    </nav>
  </aside>

  <main class="admin-main" data-admin="<?= e($section) ?>">
    <h1 style="font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:8px">
      <?= $section === 'users' ? 'User Management' : ($section === 'tracks' ? 'Content Management' : 'Dashboard') ?>
    </h1>
    <p style="color:var(--muted);margin-bottom:26px;font-size:14px">
      <?= $section === 'users' ? 'Manage roles, ban abusive accounts, and review activity.' : ($section === 'tracks' ? 'Review, approve, or remove content across all users.' : 'Real-time platform health, growth, and engagement metrics.') ?>
    </p>

    <?php if ($section === 'dashboard'): ?>
      <div class="stat-grid" id="statGrid"><div class="stat-card"><div class="label">Loading…</div><div class="value">—</div></div></div>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-top:20px">
        <h3 style="font-size:15px;margin-bottom:12px">Administrator responsibilities</h3>
        <ul style="font-size:13px;color:var(--muted);line-height:1.9;list-style:none;padding:0">
          <li>• <strong style="color:var(--text)">Moderate content</strong> — approve uploads, remove infringing tracks, investigate reports.</li>
          <li>• <strong style="color:var(--text)">Manage users</strong> — promote moderators, ban abusive accounts, restore access on appeal.</li>
          <li>• <strong style="color:var(--text)">Monitor platform health</strong> — track sign-ups, plays, and engagement trends.</li>
          <li>• <strong style="color:var(--text)">Broadcast announcements</strong> — communicate maintenance windows or policy updates.</li>
          <li>• <strong style="color:var(--text)">Audit trail</strong> — every administrative action is logged with actor, timestamp, and IP.</li>
        </ul>
      </div>
    <?php elseif ($section === 'users'): ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">
        <table class="data-table">
          <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Tracks</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody id="usersBody"><tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Loading…</td></tr></tbody>
        </table>
      </div>
    <?php elseif ($section === 'tracks'): ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">
        <table class="data-table">
          <thead><tr><th>ID</th><th>Title</th><th>Artist</th><th>Source</th><th>Plays</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="tracksBody"><tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Loading…</td></tr></tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>
<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>