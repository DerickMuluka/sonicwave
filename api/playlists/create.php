<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$name = clean_str($in['name'] ?? '', 150);
if (strlen($name) < 1) json_response(['error' => 'Playlist name is required'], 422);
$desc = clean_str($in['description'] ?? '', 500);
$public = !empty($in['is_public']) ? 1 : 1;
$pdo = db();
$pdo->prepare("INSERT INTO playlists (user_id, name, description, is_public) VALUES (?,?,?,?)")
    ->execute([current_user()['id'], $name, $desc ?: null, $public]);
json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);