<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'Your Library';
$extraJs = ['library.js'];
$tab = $_GET['tab'] ?? 'all';
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>
  <h1 style="font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:20px">Your Library</h1>
  <div class="chips">
    <a href="?tab=all" class="chip <?= $tab==='all'?'active':'' ?>">All tracks</a>
    <a href="?tab=mine" class="chip <?= $tab==='mine'?'active':'' ?>">My uploads</a>
    <a href="?tab=liked" class="chip <?= $tab==='liked'?'active':'' ?>">Liked</a>
    <a href="?tab=playlists" class="chip <?= $tab==='playlists'?'active':'' ?>">Playlists</a>
  </div>
  <div id="libContent"><div class="empty"><p>Loading…</p></div></div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';