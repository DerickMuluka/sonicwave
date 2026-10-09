<?php
require_once __DIR__ . '/includes/init.php';

if (!is_logged_in()) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#07080e">
<title><?= APP_NAME ?> — Music, video & podcasts from every platform</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/landing.css') ?>">
</head>
<body class="landing">
<nav class="landing-nav">
  <a href="<?= url('index.php') ?>" class="brand">
    <div class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
    <span>SonicWave</span>
  </a>
  <div style="display:flex;gap:10px">
    <a href="<?= url('login.php') ?>" class="btn btn-ghost">Sign in</a>
    <a href="<?= url('register.php') ?>" class="btn btn-primary">Get started</a>
  </div>
</nav>

<section class="landing-hero">
  <div class="hero-badge"><span class="dot"></span> Streaming worldwide · 190+ countries</div>
  <h1>Every sound.<br>Every story.<br>One <span class="grad">wave</span>.</h1>
  <p class="lede">SonicWave brings together music, video, and podcasts from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, and Bandcamp — inside one unified player. Upload your own, build playlists, and let our AI DJ shape the mood.</p>
  <div class="hero-actions">
    <a href="<?= url('register.php') ?>" class="btn btn-primary">
      Create free account
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
    <a href="<?= url('login.php') ?>" class="btn btn-ghost">I already have an account</a>
  </div>
  <div class="hero-stats">
    <div class="stat"><div class="stat-num">9</div><div class="stat-label">Platforms</div></div>
    <div class="stat"><div class="stat-num">190+</div><div class="stat-label">Countries</div></div>
    <div class="stat"><div class="stat-num">AI</div><div class="stat-label">Powered DJ</div></div>
    <div class="stat"><div class="stat-num">4K</div><div class="stat-label">Video ready</div></div>
  </div>
</section>

<section class="feature-section">
  <h2>Built for the way you listen</h2>
  <p class="sub">Every feature you'd expect from a modern streaming service — plus a few you didn't.</p>
  <div class="feature-grid">
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></div>
      <h3>Universal import</h3>
      <p>Paste a link from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, or Bandcamp. We pull metadata and stream it inside SonicWave.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L9.5 8.5 3 11l6.5 2.5L12 20l2.5-6.5L21 11l-6.5-2.5z"/></svg></div>
      <h3>AI DJ & moods</h3>
      <p>Pick a mood or type how you feel — our AI builds a personalised queue that adapts to your listening history over time.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
      <h3>Video & audio download</h3>
      <p>Download in MP3, MP4, or WebM. Convert any track from video to audio and back — with up to 4K resolution options.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
      <h3>Secure by design</h3>
      <p>CSRF-protected forms, bcrypt hashing, prepared statements, and full audit logging keep your library safe.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8.5 22 9.3 17 14 18.2 21 12 17.8 5.8 21 7 14 2 9.3 9 8.5 12 2"/></svg></div>
      <h3>Smart playlists</h3>
      <p>Build playlists that cross platforms. Mix a Spotify track with a YouTube video and your own uploads in the same queue.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 4 4 6-6"/></svg></div>
      <h3>Insights & analytics</h3>
      <p>See your top genres, most-played artists, and listening streaks. Admins get full platform metrics in real time.</p>
    </div>
  </div>
</section>

<section class="feature-section" style="padding-top:20px">
  <h2 style="font-size:14px;letter-spacing:2px;color:var(--muted);text-transform:uppercase;font-weight:700;text-align:center">Works with your favourite platforms</h2>
  <div class="platforms" style="margin-top:24px">
    <span>YouTube</span><span>Spotify</span><span>SoundCloud</span><span>Vimeo</span><span>Apple Music</span><span>Deezer</span><span>Bandcamp</span><span>Direct MP3</span>
  </div>
</section>

<section class="cta-section">
  <h2>Start listening in under a minute.</h2>
  <p>No credit card. No hidden fees. Just music, video, and podcasts — from everywhere.</p>
  <a href="<?= url('register.php') ?>" class="btn">Create your free account</a>
</section>

<footer style="text-align:center;padding:30px 20px;color:var(--muted);font-size:13px">
  © <?= date('Y') ?> <?= APP_NAME ?>. Built for listeners, everywhere.
</footer>
</body>
</html>
<?php
    exit;
}

$pageTitle = 'Home';
$extraCss = ['dashboard.css'];
$extraJs  = ['home.js', 'backgrounds.js'];

$pdo = db();
$uid = current_user()['id'];
$username = $user['username'];

$hour = (int)date('G');
$timeGreeting = $hour < 5 ? 'Late night session'
    : ($hour < 12 ? 'Morning rotation'
    : ($hour < 17 ? 'Afternoon listening'
    : ($hour < 22 ? 'Evening vibes' : 'Late night session')));

$recent  = $pdo->query("SELECT * FROM tracks WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 12")->fetchAll();
$popular = $pdo->query("SELECT * FROM tracks WHERE is_approved = 1 ORDER BY plays DESC LIMIT 8")->fetchAll();

$userTracks = (int)$pdo->query("SELECT COUNT(*) FROM tracks WHERE user_id = $uid")->fetchColumn();
$userLikes  = (int)$pdo->query("SELECT COUNT(*) FROM likes WHERE user_id = $uid")->fetchColumn();
$userPlays  = (int)$pdo->query("SELECT COUNT(*) FROM play_history WHERE user_id = $uid")->fetchColumn();

$activity = $pdo->query("
    SELECT u.username, t.title AS track_title, t.id AS track_id, ph.played_at
    FROM play_history ph
    JOIN users u ON u.id = ph.user_id
    JOIN tracks t ON t.id = ph.track_id
    WHERE ph.user_id != $uid
    ORDER BY ph.played_at DESC
    LIMIT 6
")->fetchAll();

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>

  <div class="dash">
    <section class="dash-hero" data-rotating-hero>
      <div class="dash-hero-inner">
        <div class="kicker"><span class="pulse"></span> <?= e($timeGreeting) ?></div>
        <h1>Your sound, <span class="accent">your rules</span>.</h1>
        <p>Pick up your queue, discover fresh drops, or let the AI DJ shape a mood. Everything you upload, like, and play lives here.</p>
        <div class="dash-hero-actions">
          <a href="<?= url('upload.php') ?>" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Add music
          </a>
          <a href="<?= url('search.php') ?>" class="btn btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Explore
          </a>
        </div>
      </div>
      <div class="dash-hero-stats">
        <div class="hero-stat">
          <div class="label">In your library</div>
          <div class="value"><?= $userTracks ?><small> tracks</small></div>
        </div>
        <div class="hero-stat">
          <div class="label">Liked</div>
          <div class="value"><?= $userLikes ?><small> saved</small></div>
        </div>
        <div class="hero-stat">
          <div class="label">Your plays</div>
          <div class="value"><?= number_format($userPlays) ?></div>
        </div>
        <div class="hero-stat">
          <div class="label">On platform</div>
          <div class="value"><?= count($recent) + count($popular) ?><small> tracks</small></div>
        </div>
      </div>
    </section>

    <div class="bento">
      <div class="bento-tile tile-5 ai-tile">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L9.5 8.5 3 11l6.5 2.5L12 20l2.5-6.5L21 11l-6.5-2.5z"/></svg></span>
            AI DJ
          </div>
          <span class="tile-action">Beta</span>
        </div>
        <p style="font-size:12px;color:var(--muted);margin-bottom:14px;line-height:1.55">Choose a vibe — get a personalised queue built from your listening history and the platform library.</p>
        <div class="mood-chips">
          <button class="mood-chip" data-ai-mood="chill"><span class="emoji">🌙</span>Chill</button>
          <button class="mood-chip" data-ai-mood="focus"><span class="emoji">🎯</span>Focus</button>
          <button class="mood-chip" data-ai-mood="energetic"><span class="emoji">⚡</span>Workout</button>
          <button class="mood-chip" data-ai-mood="happy"><span class="emoji">☀️</span>Happy</button>
          <button class="mood-chip" data-ai-mood="romantic"><span class="emoji">💗</span>Romantic</button>
          <button class="mood-chip" data-ai-mood="sad"><span class="emoji">🌧️</span>Late night</button>
        </div>
      </div>

      <div class="bento-tile tile-7">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15 8.5 22 9.3 17 14 18.2 21 12 17.8 5.8 21 7 14 2 9.3 9 8.5 12 2"/></svg></span>
            Recommended for you
          </div>
          <button class="tile-action" id="aiRefresh">Refresh</button>
        </div>
        <div id="aiRecs">
          <p style="color:var(--muted);font-size:13px">Play a few tracks and recommendations will appear here.</p>
        </div>
      </div>

      <div class="bento-tile tile-8">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span>
            New on SonicWave
          </div>
          <a class="tile-action" href="<?= url('search.php') ?>">View all →</a>
        </div>
        <?php if (!$recent): ?>
          <p style="color:var(--muted);font-size:13px;padding:20px 0;text-align:center">No tracks yet. <a href="<?= url('upload.php') ?>" style="color:var(--primary-2)">Upload or import one</a>.</p>
        <?php else: ?>
          <div style="display:grid;gap:2px">
            <?php foreach (array_slice($recent, 0, 6) as $t): $p = track_payload($t); ?>
              <div class="mini-track" data-play-track="<?= $p['id'] ?>">
                <div class="cover"><?php if ($p['cover_path']): ?><img src="<?= e($p['cover_path']) ?>" alt=""><?php else: ?>♪<?php endif; ?></div>
                <div class="info">
                  <div class="name"><?= e($p['title']) ?></div>
                  <div class="artist"><?= e($p['artist']) ?></div>
                </div>
                <span class="play-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="bento-tile tile-4">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span>
            Trending
          </div>
          <span class="tile-action">Last 24h</span>
        </div>
        <?php if (!$popular): ?>
          <p style="color:var(--muted);font-size:13px;padding:20px 0;text-align:center">Nothing trending yet.</p>
        <?php else: ?>
          <div style="display:grid;gap:2px">
            <?php foreach (array_slice($popular, 0, 5) as $t): $p = track_payload($t); ?>
              <div class="mini-track" data-play-track="<?= $p['id'] ?>">
                <div class="cover"><?php if ($p['cover_path']): ?><img src="<?= e($p['cover_path']) ?>" alt=""><?php else: ?>♪<?php endif; ?></div>
                <div class="info">
                  <div class="name"><?= e($p['title']) ?></div>
                  <div class="artist"><?= e($p['artist']) ?></div>
                </div>
                <span class="play-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="bento-tile tile-3">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
            Community
          </div>
        </div>
        <?php if (!$activity): ?>
          <p style="color:var(--muted);font-size:13px;padding:20px 0;text-align:center">No community plays yet.</p>
        <?php else: ?>
          <?php foreach ($activity as $a): ?>
            <div class="activity-item">
              <div class="av"><?= e(strtoupper(substr($a['username'], 0, 1))) ?></div>
              <div class="content">
                <div class="who"><?= e($a['username']) ?></div>
                <div class="what">played <strong><?= e($a['track_title']) ?></strong></div>
                <div class="when"><?= date('M j, g:i a', strtotime($a['played_at'])) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="bento-tile tile-5">
        <div class="tile-head">
          <div class="tile-title">
            <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
            Import from anywhere
          </div>
          <a class="tile-action" href="<?= url('upload.php') ?>">Import →</a>
        </div>
        <p style="font-size:12px;color:var(--muted);margin-bottom:14px;line-height:1.55">Paste a link — we handle the rest. Download to MP3, MP4, or WebM.</p>
        <div class="platform-strip">
          <span class="platform-pill"><span class="dot" style="background:#ef4444"></span>YouTube</span>
          <span class="platform-pill"><span class="dot" style="background:#22c55e"></span>Spotify</span>
          <span class="platform-pill"><span class="dot" style="background:#f97316"></span>SoundCloud</span>
          <span class="platform-pill"><span class="dot" style="background:#22d3ee"></span>Vimeo</span>
          <span class="platform-pill"><span class="dot" style="background:#ff5c8a"></span>Apple Music</span>
          <span class="platform-pill"><span class="dot" style="background:#a855f7"></span>Deezer</span>
          <span class="platform-pill"><span class="dot" style="background:#67e8f9"></span>Bandcamp</span>
        </div>
      </div>
    </div>

    <?php if ($recent): ?>
      <section>
        <div class="strip-head">
          <h2>Fresh drops</h2>
          <a class="view-all" href="<?= url('search.php') ?>">View all →</a>
        </div>
        <div class="card-rail">
          <?php foreach ($recent as $t): $p = track_payload($t); ?>
            <div class="card" data-play-track="<?= $p['id'] ?>">
              <div class="card-cover">
                <?php if ($p['cover_path']): ?><img src="<?= e($p['cover_path']) ?>" alt=""><?php else: ?>♪<?php endif; ?>
                <div class="play-overlay"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></div>
              </div>
              <div class="card-title"><?= e($p['title']) ?></div>
              <div class="card-sub"><?= e($p['artist']) ?> <?= platformBadge($p['source']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';