<?php
require_once __DIR__ . '/../../includes/init.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); exit('Bad request'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$track = $stmt->fetch();
if (!$track) { http_response_code(404); exit('Not found'); }

$safeTitle = preg_replace('/[^A-Za-z0-9_\- ]/', '_', $track['artist'] . ' - ' . $track['title']);

// For local uploads → stream with Content-Disposition
if (in_array($track['source'], ['upload','direct'], true) && $track['file_path']) {
    $path = ROOT_PATH . '/' . ltrim($track['file_path'], '/');
    if (is_file($path)) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ext = $ext ?: 'mp3';
        $mimes = ['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','m4a'=>'audio/mp4','flac'=>'audio/flac','aac'=>'audio/aac','mp4'=>'video/mp4','webm'=>'video/webm'];
        header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $safeTitle . '.' . $ext . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}

// For external sources
if (in_array($track['source'], ['youtube','vimeo','spotify','soundcloud','apple','deezer','bandcamp'], true)) {
    // Try to redirect to the original source; browsers may open it in a new tab.
    // For a real downloader we'd use yt-dlp or an API — out of scope here.
    if ($track['source_url']) {
        header('Location: ' . $track['source_url']);
        exit;
    }
}

http_response_code(404);
exit('No downloadable source available');