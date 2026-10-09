<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$tid = (int)($in['track_id'] ?? 0);
if ($tid) db()->prepare("INSERT INTO play_history (user_id, track_id) VALUES (?,?)")->execute([current_user()['id'], $tid]);
json_response(['ok' => true]);