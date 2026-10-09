<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$pid = (int)($in['playlist_id'] ?? 0);
db()->prepare("DELETE FROM playlists WHERE id = ? AND user_id = ?")->execute([$pid, current_user()['id']]);
json_response(['ok' => true]);