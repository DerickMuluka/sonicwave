<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'Explore';
$extraJs = ['search.js'];
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>
  <h1 style="font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:8px">Explore</h1>
  <p style="color:var(--muted);margin-bottom:22px">
    Search your library — or paste a link from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, or Bandcamp to import and download.
  </p>
  <div class="chips" id="genreChips">
    <button class="chip active" data-genre="">All</button>
    <?php foreach (['Pop','Rock','Hip-Hop','Electronic','Jazz','Classical','R&B','Afrobeat','Gospel','Lo-Fi'] as $g): ?>
      <button class="chip" data-genre="<?= $g ?>"><?= $g ?></button>
    <?php endforeach; ?>
  </div>
  <div id="results"></div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';