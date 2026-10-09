<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$pid = (int)($in['playlist_id'] ?? 0);
if (!$pid) json_response(['error' => 'Missing playlist_id'], 422);

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM playlists WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$pid, current_user()['id']]);
if (!$stmt->fetch()) json_response(['error' => 'Not yours'], 403);

$name = clean_str($in['name'] ?? '', 150);
if (strlen($name) < 1) json_response(['error' => 'Name is required'], 422);
$desc = clean_str($in['description'] ?? '', 500);
$public = !empty($in['is_public']) ? 1 : 0;

$pdo->prepare("UPDATE playlists SET name = ?, description = ?, is_public = ? WHERE id = ?")
    ->execute([$name, $desc ?: null, $public, $pid]);

audit_log('playlist.update', 'playlist', $pid);
json_response(['ok' => true]);