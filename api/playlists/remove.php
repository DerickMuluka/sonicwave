<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$pid = (int)($in['playlist_id'] ?? 0);
$tid = (int)($in['track_id'] ?? 0);
if (!$pid || !$tid) json_response(['error' => 'Missing ids'], 422);

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$pid, current_user()['id']]);
if (!$stmt->fetch()) json_response(['error' => 'Not yours'], 403);

$pdo->prepare("DELETE FROM playlist_tracks WHERE playlist_id = ? AND track_id = ?")->execute([$pid, $tid]);
audit_log('playlist.track.remove', 'playlist', $pid, ['track_id' => $tid]);
json_response(['ok' => true]);