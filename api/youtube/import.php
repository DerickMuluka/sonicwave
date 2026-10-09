<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$url = trim((string)($in['url'] ?? ''));
if (!preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_\-]{6,20})#', $url, $m)) {
    json_response(['error' => 'Invalid YouTube URL'], 422);
}
$vid = $m[1];

// Fetch oEmbed metadata
$oembed = @file_get_contents('https://www.youtube.com/oembed?url=' . urlencode('https://www.youtube.com/watch?v=' . $vid) . '&format=json');
$title = 'YouTube Track';
$artist = 'YouTube';
$cover = "https://i.ytimg.com/vi/{$vid}/hqdefault.jpg";
if ($oembed) {
    $j = json_decode($oembed, true);
    if (!empty($j['title'])) $title = clean_str($j['title'], 200);
    if (!empty($j['author_name'])) $artist = clean_str($j['author_name'], 200);
    if (!empty($j['thumbnail_url'])) $cover = $j['thumbnail_url'];
}

// Save as external track (streamed via YouTube embed in the player)
$pdo = db();
$pdo->prepare("INSERT INTO tracks (user_id, title, artist, genre, file_path, cover_path, source, external_id)
               VALUES (?,?,?,?,?,?, 'youtube', ?)")
    ->execute([current_user()['id'], $title, $artist, 'YouTube', $vid, $cover, $vid]);

json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId(), 'title' => $title, 'artist' => $artist]);