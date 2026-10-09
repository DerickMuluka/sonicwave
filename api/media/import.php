<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/media_tools.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid session token'], 403);

$url = trim((string)($in['url'] ?? ''));
if (!$url) json_response(['error' => 'URL is required'], 422);

$source = null; $id = null; $title = 'Imported Track'; $artist = 'Unknown'; $cover = null; $embedType = 'iframe';
$localPath = null; $streamUrl = null;
$isDirectAudio = false;

// ── Detect platform ─────────────────────────────────────
if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/shorts/)([A-Za-z0-9_\-]{6,20})#', $url, $m)) {
    $source = 'youtube'; $id = $m[1]; $embedType = 'video';
    $meta = @file_get_contents('https://www.youtube.com/oembed?url=' . urlencode('https://www.youtube.com/watch?v=' . $id) . '&format=json');
    if ($meta) { $j = json_decode($meta, true); if (!empty($j['title'])) $title = clean_str($j['title'], 200); if (!empty($j['author_name'])) $artist = clean_str($j['author_name'], 200); }
    $cover = "https://i.ytimg.com/vi/{$id}/hqdefault.jpg";
}
elseif (preg_match('#vimeo\.com/(\d+)#', $url, $m)) {
    $source = 'vimeo'; $id = $m[1]; $embedType = 'video';
    $meta = @file_get_contents('https://vimeo.com/api/oembed.json?url=' . urlencode('https://vimeo.com/' . $id));
    if ($meta) { $j = json_decode($meta, true); if (!empty($j['title'])) $title = clean_str($j['title'], 200); if (!empty($j['author_name'])) $artist = clean_str($j['author_name'], 200); if (!empty($j['thumbnail_url'])) $cover = $j['thumbnail_url']; }
}
elseif (preg_match('#spotify\.com/(?:intl-[a-z]+/)?(track|album|playlist|episode)/([A-Za-z0-9]+)#', $url, $m)) {
    $source = 'spotify'; $id = $m[2]; $embedType = 'iframe';
    $title = ucfirst($m[1]) . ' on Spotify';
    $meta = @file_get_contents('https://open.spotify.com/oembed?url=' . urlencode($url));
    if ($meta) { $j = json_decode($meta, true); if (!empty($j['title'])) $title = clean_str($j['title'], 200); if (!empty($j['thumbnail_url'])) $cover = $j['thumbnail_url']; }
}
elseif (preg_match('#soundcloud\.com/[^/]+/[^/?]+#', $url)) {
    $source = 'soundcloud'; $embedType = 'iframe';
    $meta = @file_get_contents('https://soundcloud.com/oembed?format=json&url=' . urlencode($url));
    if ($meta) { $j = json_decode($meta, true); if (!empty($j['title'])) $title = clean_str($j['title'], 200); if (!empty($j['author_name'])) $artist = clean_str($j['author_name'], 200); if (!empty($j['thumbnail_url'])) $cover = $j['thumbnail_url']; }
}
elseif (preg_match('#deezer\.com/(?:[a-z]{2}/)?track/(\d+)#', $url, $m)) {
    $source = 'deezer'; $id = $m[1]; $embedType = 'iframe';
    $meta = @file_get_contents('https://api.deezer.com/track/' . $id);
    if ($meta) { $j = json_decode($meta, true); if (!empty($j['title'])) $title = clean_str($j['title'], 200); if (!empty($j['artist']['name'])) $artist = clean_str($j['artist']['name'], 200); if (!empty($j['album']['cover_medium'])) $cover = $j['album']['cover_medium']; }
}
elseif (preg_match('#music\.apple\.com/#', $url)) {
    $source = 'apple'; $embedType = 'iframe'; $title = 'Apple Music Track';
}
elseif (preg_match('#\.bandcamp\.com/(track|album)/#', $url)) {
    $source = 'bandcamp'; $embedType = 'iframe';
}
elseif (preg_match('#^https?://.+\.(mp3|wav|ogg|m4a|flac|aac|opus)(\?.*)?$#i', $url, $m)) {
    $source = 'direct'; $title = basename(parse_url($url, PHP_URL_PATH)); $embedType = 'audio';
    $isDirectAudio = true;
}
elseif (preg_match('#^https?://.+\.(mp4|webm|mov)(\?.*)?$#i', $url)) {
    $source = 'direct'; $title = basename(parse_url($url, PHP_URL_PATH)); $embedType = 'video';
    $isDirectAudio = true;
}
else {
    json_response(['error' => 'Unsupported URL. Supported: YouTube, Vimeo, Spotify, SoundCloud, Deezer, Apple Music, Bandcamp, direct MP3/MP4'], 422);
}

// ── VALIDATION: Try to download and validate before saving ──
$downloadNote = null;
$validated = false;

if (in_array($source, ['youtube','vimeo','soundcloud','bandcamp'], true)) {
    if (!YTDLP_ENABLED) {
        json_response([
            'error' => 'Cannot import this URL',
            'detail' => 'yt-dlp is not installed on this server. Without it, the track cannot be downloaded and would not play in SonicWave.',
        ], 503);
    }

    $tmpDir = VIDEO_PATH . '/import_' . bin2hex(random_bytes(6));
    @mkdir($tmpDir, 0755, true);

    $dl = ytdlp_download($url, $tmpDir, 'video', 'best');

    if (!$dl['ok'] || empty($dl['path']) || !is_file($dl['path'])) {
        // Cleanup and reject
        foreach (glob($tmpDir . '/*') as $f) @unlink($f);
        @rmdir($tmpDir);
        json_response([
            'error' => 'Import failed — the URL could not be downloaded',
            'detail' => $dl['error'] ?? 'Unknown yt-dlp error',
        ], 422);
    }

    // Validate the downloaded file is non-empty and has valid extension
    $size = filesize($dl['path']);
    $ext = strtolower(pathinfo($dl['path'], PATHINFO_EXTENSION));
    if ($size < 1024) {
        foreach (glob($tmpDir . '/*') as $f) @unlink($f);
        @rmdir($tmpDir);
        json_response(['error' => 'Downloaded file is too small / corrupt'], 422);
    }
    if (!in_array($ext, ['mp4','webm','mkv','mp3','m4a','opus','ogg','aac','flac','wav'], true)) {
        foreach (glob($tmpDir . '/*') as $f) @unlink($f);
        @rmdir($tmpDir);
        json_response(['error' => 'Downloaded file has an unsupported format: ' . $ext], 422);
    }

    // Move into permanent storage
    if (!is_dir(VIDEO_PATH)) @mkdir(VIDEO_PATH, 0755, true);
    $permanent = VIDEO_PATH . '/' . bin2hex(random_bytes(8)) . '.' . $ext;
    if (!rename($dl['path'], $permanent)) {
        foreach (glob($tmpDir . '/*') as $f) @unlink($f);
        @rmdir($tmpDir);
        json_response(['error' => 'Could not save downloaded file'], 500);
    }
    $localPath = 'uploads/video/' . basename($permanent);
    $embedType = ($ext === 'mp4' || $ext === 'webm') ? 'video' : 'audio';
    $validated = true;

    foreach (glob($tmpDir . '/*') as $f) @unlink($f);
    @rmdir($tmpDir);
}
elseif ($source === 'direct' && $isDirectAudio) {
    // Optionally validate URL is reachable
    $head = @get_headers($url, 1);
    if (!$head || (!isset($head[0]) || !preg_match('#200|206|301|302#', $head[0]))) {
        json_response(['error' => 'Direct URL is not reachable'], 422);
    }
    $validated = true;
}
else {
    // Embedded (Spotify, Apple, Deezer) — always playable via iframe
    $validated = true;
}

if (!$validated) {
    json_response(['error' => 'Import could not be validated'], 422);
}

$mood = ai_detect_mood($title . ' ' . $artist . ' ' . $source);
$desc = ai_describe_track($title, $artist, ucfirst($source));

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO tracks (user_id, title, artist, genre, file_path, cover_path, source, source_url, external_id, embed_type, stream_url, mood, description, is_approved)
                       VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1)");
$stmt->execute([
    current_user()['id'], $title, $artist, ucfirst($source),
    $localPath, $cover, $source, $url, $id,
    $localPath ? $embedType : 'iframe',
    null, $mood, $desc
]);
$newId = (int)$pdo->lastInsertId();

if ($localPath) {
    $pdo->prepare("UPDATE tracks SET stream_url = ?, file_path = ? WHERE id = ?")
        ->execute([url('api/tracks/stream.php?id=' . $newId), $localPath, $newId]);
}

audit_log('track.import', 'track', $newId, ['source' => $source, 'downloaded' => (bool)$localPath]);

json_response([
    'ok' => true,
    'id' => $newId,
    'title' => $title,
    'artist' => $artist,
    'source' => $source,
    'cover' => $cover,
    'downloaded' => (bool)$localPath,
    'note' => $downloadNote,
]);