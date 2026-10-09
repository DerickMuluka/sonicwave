<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'Upload & Import';
$extraJs = ['upload.js'];
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/nav.php';
?>
<main class="main">
  <?php include __DIR__ . '/templates/topbar.php'; ?>
  <h1 style="font-size:26px;font-weight:800;letter-spacing:-.5px;margin-bottom:8px">Upload & Import</h1>
  <p style="color:var(--muted);margin-bottom:26px">Add audio from your device or import from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, or Bandcamp.</p>

  <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:22px" class="upload-grid">
    <div class="card" style="padding:26px;cursor:default">
      <h3 style="font-size:16px;margin-bottom:18px">Upload audio</h3>
      <form id="uploadForm" enctype="multipart/form-data">
        <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Audio file</label>
        <div id="dropZone" style="border:2px dashed var(--border);border-radius:14px;padding:34px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:20px">
          <div style="font-size:32px;margin-bottom:10px;color:var(--primary-2)">♪</div>
          <div style="font-weight:600;margin-bottom:4px">Drop your audio file here</div>
          <div style="font-size:13px;color:var(--muted)">or click to browse — MP3, WAV, OGG, M4A, FLAC, AAC up to 40 MB</div>
          <input type="file" id="audioInput" name="audio" accept=".mp3,.wav,.ogg,.m4a,.flac,.aac,.webm,.opus,audio/*" style="display:none" required>
        </div>
        <div id="fileInfo" style="font-size:13px;color:var(--muted);margin-bottom:16px"></div>

        <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Title</label>
        <input type="text" name="title" id="fTitle" required maxlength="200" style="width:100%;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);margin-bottom:14px;outline:none;font-family:inherit">

        <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Artist</label>
        <input type="text" name="artist" id="fArtist" required maxlength="200" style="width:100%;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);margin-bottom:14px;outline:none;font-family:inherit">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div>
            <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Album</label>
            <input type="text" name="album" maxlength="200" style="width:100%;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);outline:none;font-family:inherit">
          </div>
          <div>
            <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Genre</label>
            <select name="genre" style="width:100%;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);outline:none;font-family:inherit">
              <?php foreach (['Unknown','Pop','Rock','Hip-Hop','Electronic','Jazz','Classical','R&B','Country','Reggae','Afrobeat','Gospel','Lo-Fi'] as $g): ?>
                <option><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div style="margin-top:14px">
          <label style="display:block;font-size:13px;color:var(--muted);margin-bottom:8px;font-weight:600">Cover image (optional)</label>
          <input type="file" name="cover" accept="image/*" style="width:100%;font-size:13px;color:var(--muted)">
        </div>

        <button type="submit" class="btn btn-primary" id="upBtn" style="width:100%;justify-content:center;margin-top:22px;padding:13px">Upload track</button>
        <div id="upProgress" style="margin-top:14px;display:none">
          <div style="height:6px;background:rgba(255,255,255,.1);border-radius:999px;overflow:hidden">
            <div id="upFill" style="height:100%;width:0;background:var(--grad-main);transition:width .2s"></div>
          </div>
        </div>
      </form>
    </div>

    <div>
      <div class="card" style="padding:22px;cursor:default;margin-bottom:18px">
        <h3 style="font-size:15px;margin-bottom:8px">Import from any platform</h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:14px">Paste a link from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, or Bandcamp.</p>
        <input type="text" id="mediaUrl" placeholder="https://…" style="width:100%;background:var(--bg-0);border:1px solid var(--border);border-radius:10px;padding:11px 14px;color:var(--text);margin-bottom:12px;outline:none;font-family:inherit">
        <button class="btn btn-primary" id="importBtn" style="width:100%;justify-content:center">Import</button>
        <div class="platform-grid">
          <div class="platform-tile"><span class="dot" style="background:#ef4444"></span>YouTube</div>
          <div class="platform-tile"><span class="dot" style="background:#22c55e"></span>Spotify</div>
          <div class="platform-tile"><span class="dot" style="background:#f97316"></span>SoundCloud</div>
          <div class="platform-tile"><span class="dot" style="background:#22d3ee"></span>Vimeo</div>
          <div class="platform-tile"><span class="dot" style="background:#ff5c8a"></span>Apple Music</div>
          <div class="platform-tile"><span class="dot" style="background:#a855f7"></span>Deezer</div>
          <div class="platform-tile"><span class="dot" style="background:#67e8f9"></span>Bandcamp</div>
          <div class="platform-tile"><span class="dot" style="background:#94a3b8"></span>Direct MP3/MP4</div>
        </div>
      </div>

      <div class="card" style="padding:22px;cursor:default">
        <h3 style="font-size:15px;margin-bottom:12px">Guidelines</h3>
        <ul style="font-size:13px;color:var(--muted);line-height:1.9;list-style:none;padding:0">
          <li>• Use high-bitrate audio (320 kbps or higher)</li>
          <li>• Square cover art (1:1) looks best across the app</li>
          <li>• Add accurate genre tags for better discovery</li>
          <li>• Max file size: 40 MB per track</li>
          <li>• Only upload content you have the rights to share</li>
        </ul>
      </div>
    </div>
  </div>
</main>
<?php
include __DIR__ . '/templates/player.php';
include __DIR__ . '/templates/footer.php';