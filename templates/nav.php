<?php
$cur = basename($_SERVER['PHP_SELF']);
$isActive = fn($p) => $cur === $p ? 'active' : '';
?>
<div class="hnav-wrap">
  <nav class="hnav">
    <a href="<?= url('index.php') ?>" class="brand">
      <div class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
      <span>SonicWave</span>
    </a>

    <div class="hnav-links">
      <a href="<?= url('index.php') ?>" class="hnav-link <?= $isActive('index.php') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="label">Home</span>
      </a>
      <a href="<?= url('search.php') ?>" class="hnav-link <?= $isActive('search.php') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span class="label">Explore</span>
      </a>
      <a href="<?= url('media.php') ?>" class="hnav-link <?= $isActive('media.php') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
        <span class="label">Media Hub</span>
      </a>
      <a href="<?= url('library.php') ?>" class="hnav-link <?= $isActive('library.php') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        <span class="label">Library</span>
      </a>
      <a href="<?= url('library.php?tab=playlists') ?>" class="hnav-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h13"/><path d="M3 12h13"/><path d="M3 18h9"/><circle cx="18" cy="16" r="3"/><path d="M18 13V7l3-1v3"/></svg>
        <span class="label">Playlists</span>
      </a>
      <a href="<?= url('upload.php') ?>" class="hnav-link <?= $isActive('upload.php') ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span class="label">Upload</span>
      </a>
    </div>

    <div class="hnav-right">
      <div class="hnav-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="globalSearch" placeholder="Search…" autocomplete="off">
      </div>
    </div>
  </nav>
</div>