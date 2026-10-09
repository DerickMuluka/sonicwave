<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();

$trackId = (int)($_GET['track_id'] ?? 0);
$format  = strtolower(clean_str($_GET['format'] ?? '', 10));

if (!$trackId || !$format) { http_response_code(400); exit('Bad request'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT title, artist, cached_path FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$trackId]);
$track = $stmt->fetch();
if (!$track || !$track['cached_path']) { http_response_code(404); exit('Not found'); }

$path = ROOT_PATH . '/' . ltrim($track['cached_path'], '/');
if (!is_file($path)) { http_response_code(404); exit('File missing'); }

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mimes = ['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','m4a'=>'audio/mp4','flac'=>'audio/flac','aac'=>'audio/aac','mp4'=>'video/mp4','webm'=>'video/webm'];

header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
header('Accept-Ranges: bytes');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;