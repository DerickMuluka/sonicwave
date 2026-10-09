<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$pid = (int)($in['playlist_id'] ?? 0);
$tid = (int)($in['track_id'] ?? 0);
if (!$pid || !$tid) json_response(['error' => 'Missing playlist_id or track_id'], 422);

$pdo = db();
$uid = current_user()['id'];

$stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$pid, $uid]);
if (!$stmt->fetch()) json_response(['error' => 'Playlist not found or not yours'], 404);

$stmt = $pdo->prepare("SELECT id FROM tracks WHERE id = ? LIMIT 1");
$stmt->execute([$tid]);
if (!$stmt->fetch()) json_response(['error' => 'Track not found'], 404);

$stmt = $pdo->prepare("SELECT COALESCE(MAX(position), 0) + 1 AS pos FROM playlist_tracks WHERE playlist_id = ?");
$stmt->execute([$pid]);
$pos = (int)$stmt->fetch()['pos'];

try {
    $pdo->prepare("INSERT INTO playlist_tracks (playlist_id, track_id, position) VALUES (?, ?, ?)")
        ->execute([$pid, $tid, $pos]);
    audit_log('playlist.track.add', 'playlist', $pid, ['track_id' => $tid]);
    json_response(['ok' => true, 'position' => $pos]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') json_response(['error' => 'Already in this playlist'], 409);
    json_response(['error' => 'Could not add track'], 500);
}