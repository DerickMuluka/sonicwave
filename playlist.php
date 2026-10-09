<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pid = (int)($_GET['id'] ?? 0);
if (!$pid) { header('Location: ' . url('library.php?tab=playlists')); exit; }

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND (user_id = ? OR is_public = 1) LIMIT 1");
$stmt->execute([$pid, current_user()['id']]);
$pl = $stmt->fetch();
if (!$pl) { header('Location: ' . url('library.php?tab=playlists')); exit; }
$isOwner = (int)$pl['user_id'] === (int)current_user()['id'];

$pageTitle = $pl['name'];
$extraJs = ['playlist.js'];
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>

  <div data-playlist-id="<?= $pid ?>"
       data-is-owner="<?= $isOwner ? '1' : '0' ?>"
       data-pl-name="<?= e($pl['name']) ?>"
       data-pl-desc="<?= e($pl['description'] ?? '') ?>"
       data-pl-public="<?= (int)$pl['is_public'] ?>"
       style="display:flex;gap:24px;align-items:end;margin-bottom:28px;flex-wrap:wrap">
    <div style="width:180px;height:180px;border-radius:16px;background:var(--grad-main);display:grid;place-items:center;box-shadow:0 20px 50px rgba(124,92,255,.4);flex-shrink:0">
      <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" style="width:70px;height:70px"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
    </div>
    <div style="min-width:0;flex:1">
      <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.4px;font-weight:700;margin-bottom:8px">
        Playlist <?= $pl['is_public'] ? '· Public' : '· Private' ?>
      </div>
      <h1 style="font-size:36px;font-weight:800;letter-spacing:-1px;line-height:1.1;margin-bottom:12px"><?= e($pl['name']) ?></h1>
      <?php if ($pl['description']): ?>
        <p style="color:var(--muted);font-size:14px;margin-bottom:12px;max-width:600px"><?= e($pl['description']) ?></p>
      <?php endif; ?>
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
        <button class="btn btn-primary" id="playAllBtn">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px"><path d="M8 5v14l11-7z"/></svg>
          Play all
        </button>
        <?php if ($isOwner): ?>
          <button class="btn btn-ghost" id="addTracksBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add tracks
          </button>
          <button class="btn btn-ghost" id="editPlBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
          </button>
          <button class="btn btn-danger" id="deletePlBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            Delete
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div id="plTracks"><div class="empty"><p>Loading tracks…</p></div></div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';