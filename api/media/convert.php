<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/media_tools.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

if (!FFMPEG_ENABLED) {
    json_response([
        'error' => 'Conversion not available',
        'detail' => 'FFmpeg is not installed on this server. Ask your host to enable it, or install the ffmpeg binary in /bin/.',
    ], 503);
}

$trackId = (int)($in['track_id'] ?? 0);
$format  = strtolower(clean_str($in['format'] ?? 'mp3', 10));

if (!$trackId) json_response(['error' => 'Missing track id'], 422);

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$trackId]);
$track = $stmt->fetch();
if (!$track) json_response(['error' => 'Track not found'], 404);

// Find local file
$localPath = null;
if ($track['file_path'] && in_array($track['source'], ['upload','direct'], true)) {
    $p = ROOT_PATH . '/' . ltrim($track['file_path'], '/');
    if (is_file($p)) $localPath = $p;
} elseif (!empty($track['cached_path'])) {
    $p = ROOT_PATH . '/' . ltrim($track['cached_path'], '/');
    if (is_file($p)) $localPath = $p;
}

if (!$localPath) {
    // Download from source first (if possible)
    if (YTDLP_ENABLED && in_array($track['source'], ['youtube','vimeo','soundcloud','bandcamp'], true) && $track['source_url']) {
        $tmpDir = VIDEO_PATH . '/tmp_' . $track['id'] . '_' . bin2hex(random_bytes(4));
        @mkdir($tmpDir, 0755, true);
        $dl = ytdlp_download($track['source_url'], $tmpDir, 'video', 'best');
        if ($dl['ok']) $localPath = $dl['path'];
    }
    if (!$localPath) {
        json_response(['error' => 'No local file to convert. Download the source first.'], 422);
    }
}

$conv = ffmpeg_convert($localPath, $format);
if (!$conv['ok']) json_response(['error' => $conv['error'] ?? 'Conversion failed'], 500);

// Cache converted file path in the track
$rel = 'uploads/cache/' . basename($conv['path']);
$pdo->prepare("UPDATE tracks SET cached_path = ? WHERE id = ?")->execute([$rel, $trackId]);

json_response([
    'ok' => true,
    'url' => url('api/media/serve.php?track_id=' . $trackId . '&format=' . $format),
    'size' => $conv['size'],
    'format' => $format,
]);