<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/media_tools.php';
require_login_api();

$id     = (int)($_GET['id'] ?? 0);
$format = strtolower(clean_str($_GET['format'] ?? 'source', 10));
$quality = clean_str($_GET['quality'] ?? 'best', 10);

if (!$id) { http_response_code(400); exit('Bad request'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$track = $stmt->fetch();
if (!$track) { http_response_code(404); exit('Not found'); }

$safeTitle = preg_replace('/[^A-Za-z0-9_\- ]/', '_', $track['artist'] . ' - ' . $track['title']);
$safeTitle = trim($safeTitle) ?: 'track';

// ── 1. Local file → just serve it ──────────────────────────
if (in_array($track['source'], ['upload','direct'], true) && $track['file_path']) {
    $path = ROOT_PATH . '/' . ltrim($track['file_path'], '/');
    if (is_file($path)) {
        // If format requested differs from source, convert first
        $srcExt = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $wantExt = match($format) {
            'mp3' => 'mp3', 'm4a' => 'm4a', 'wav' => 'wav', 'ogg' => 'ogg',
            'mp4' => 'mp4', 'webm' => 'webm',
            default => $srcExt,
        };

        if ($wantExt !== $srcExt && FFMPEG_ENABLED) {
            $conv = ffmpeg_convert($path, $wantExt);
            if ($conv['ok']) {
                $path = $conv['path'];
                $srcExt = $wantExt;
            }
        }

        $mimes = ['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','m4a'=>'audio/mp4','flac'=>'audio/flac','aac'=>'audio/aac','mp4'=>'video/mp4','webm'=>'video/webm'];
        header('Content-Type: ' . ($mimes[$srcExt] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $safeTitle . '.' . $srcExt . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}

// ── 2. External (YouTube, Vimeo, SoundCloud, direct) ───────
$downloadSource = $track['source_url'] ?: $track['source_url'];
if (in_array($track['source'], ['youtube','vimeo','soundcloud','bandcamp'], true) && $downloadSource) {
    if (!YTDLP_ENABLED) {
        // Fallback: JSON error explaining why
        http_response_code(503);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Server-side download not available',
            'detail' => 'yt-dlp binary is not installed on this host. On shared hosting, download features require yt-dlp.',
            'fallback_url' => $track['source_url'],
        ]);
        exit;
    }

    $tmpDir = VIDEO_PATH . '/tmp_' . $track['id'] . '_' . bin2hex(random_bytes(4));
    @mkdir($tmpDir, 0755, true);

    $wantAudio = in_array($format, ['mp3','m4a','wav','ogg','aac'], true) || $format === 'audio';
    $targetFormat = match($format) {
        'mp3' => 'audio', 'm4a' => 'audio', 'wav' => 'audio', 'ogg' => 'audio', 'aac' => 'audio',
        default => 'video',
    };

    $dl = ytdlp_download($downloadSource, $tmpDir, $targetFormat, $quality);
    if (!$dl['ok']) {
        // Clean up
        array_map('unlink', glob($tmpDir . '/*'));
        @rmdir($tmpDir);
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Download failed', 'detail' => $dl['error'] ?? 'unknown']);
        exit;
    }

    $srcPath = $dl['path'];
    $srcExt  = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));

    // Convert if requested a specific format different from what we downloaded
    $wantExt = match($format) {
        'mp3' => 'mp3', 'm4a' => 'm4a', 'wav' => 'wav', 'ogg' => 'ogg', 'aac' => 'aac',
        'mp4' => 'mp4', 'webm' => 'webm',
        default => $srcExt,
    };

    if ($wantExt !== $srcExt && FFMPEG_ENABLED) {
        $conv = ffmpeg_convert($srcPath, $wantExt);
        if ($conv['ok']) {
            @unlink($srcPath);
            $srcPath = $conv['path'];
            $srcExt = $wantExt;
        }
    }

    $mimes = ['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','m4a'=>'audio/mp4','flac'=>'audio/flac','aac'=>'audio/aac','mp4'=>'video/mp4','webm'=>'video/webm'];
    header('Content-Type: ' . ($mimes[$srcExt] ?? 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $safeTitle . '.' . $srcExt . '"');
    header('Content-Length: ' . filesize($srcPath));

    // Stream it
    $fp = fopen($srcPath, 'rb');
    while (!feof($fp)) {
        echo fread($fp, 8192);
        flush();
    }
    fclose($fp);

    // Cleanup
    @unlink($srcPath);
    @rmdir($tmpDir);
    exit;
}

// ── 3. Spotify/Apple/Deezer — DRM protected, cannot download ──
if (in_array($track['source'], ['spotify','apple','deezer'], true)) {
    http_response_code(451); // Unavailable for legal reasons
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'This platform does not allow downloads',
        'detail' => ucfirst($track['source']) . ' content is DRM-protected and cannot be downloaded. You can stream it in the player.',
        'fallback_url' => $track['source_url'],
    ]);
    exit;
}

// ── 4. Nothing matched ─────────────────────────────────────
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'No downloadable source available']);