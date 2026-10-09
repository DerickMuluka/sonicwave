<?php
require_once __DIR__ . '/../../includes/init.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); exit('Bad request'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$track = $stmt->fetch();
if (!$track) { http_response_code(404); exit('Not found'); }

// Resolve local file path — support uploads, downloads, video imports
$path = null;
if (!empty($track['file_path'])) {
    $candidate = ROOT_PATH . '/' . ltrim($track['file_path'], '/');
    if (is_file($candidate)) $path = $candidate;
}
if (!$path && !empty($track['cached_path'])) {
    $candidate = ROOT_PATH . '/' . ltrim($track['cached_path'], '/');
    if (is_file($candidate)) $path = $candidate;
}
if (!$path && !empty($track['stream_url'])) {
    // stream_url may have been set to an uploads-relative path
    $candidate = ROOT_PATH . '/' . ltrim(parse_url($track['stream_url'], PHP_URL_PATH), '/');
    // Strip the base URL prefix if present
    $rel = preg_replace('#^.*?/sonicwave/#', '', $candidate);
    if ($rel && is_file(ROOT_PATH . '/' . $rel)) $path = ROOT_PATH . '/' . $rel;
}

if (!$path) { http_response_code(404); exit('File missing'); }

if (empty($_SERVER['HTTP_RANGE'])) {
    $pdo->prepare("UPDATE tracks SET plays = plays + 1 WHERE id = ?")->execute([$id]);
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mimes = [
    'mp3'  => 'audio/mpeg',
    'wav'  => 'audio/wav',
    'ogg'  => 'audio/ogg',
    'oga'  => 'audio/ogg',
    'm4a'  => 'audio/mp4',
    'aac'  => 'audio/aac',
    'flac' => 'audio/flac',
    'opus' => 'audio/ogg',
    'webm' => 'video/webm', // could be video or audio; most browsers handle
    'mp4'  => 'video/mp4',
    'mov'  => 'video/quicktime',
    'mkv'  => 'video/x-matroska',
];

header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
header('Accept-Ranges: bytes');
header('Cache-Control: no-cache');
header('Content-Length: ' . filesize($path));

$size = filesize($path);
$start = 0; $end = $size - 1;
if (!empty($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)) {
    $start = (int)$m[1];
    if ($m[2] !== '') $end = (int)$m[2];
    if ($start > $end || $end >= $size) {
        header('HTTP/1.1 416 Range Not Satisfiable');
        header("Content-Range: bytes */$size");
        exit;
    }
    header('HTTP/1.1 206 Partial Content');
    header("Content-Range: bytes $start-$end/$size");
    header('Content-Length: ' . ($end - $start + 1));
}

$fp = fopen($path, 'rb');
fseek($fp, $start);
$remaining = $end - $start + 1;
while ($remaining > 0 && !feof($fp) && connection_status() === CONNECTION_NORMAL) {
    $read = min(8192, $remaining);
    echo fread($fp, $read);
    flush();
    $remaining -= $read;
}
fclose($fp);
exit;