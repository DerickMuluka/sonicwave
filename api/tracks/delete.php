<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);

$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$tid = (int)($in['track_id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$tid, current_user()['id']]);
$track = $stmt->fetch();
if (!$track) json_response(['error' => 'Not found or not yours'], 404);

// Delete files
foreach ([$track['file_path'], $track['cover_path']] as $rel) {
    if ($rel) {
        $p = ROOT_PATH . '/' . ltrim($rel, '/');
        if (is_file($p)) @unlink($p);
    }
}
$pdo->prepare("DELETE FROM tracks WHERE id = ?")->execute([$tid]);
json_response(['ok' => true]);