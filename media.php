<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'Media Hub';
$extraCss = ['media.css'];
$extraJs  = ['media.js'];
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>
  <h1 style="font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:8px">Media Hub</h1>
  <p style="color:var(--muted);margin-bottom:22px">Video, audio, and iframe content from every supported platform.</p>
  <div id="mediaContent"></div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';