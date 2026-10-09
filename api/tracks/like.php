<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$tid = (int)($in['track_id'] ?? 0);
if (!$tid) json_response(['error' => 'Missing track id'], 422);
$pdo = db(); $uid = current_user()['id'];
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND track_id = ? LIMIT 1");
$stmt->execute([$uid, $tid]);
$existing = $stmt->fetch();
if ($existing) {
    $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$existing['id']]);
    $pdo->prepare("UPDATE tracks SET likes = GREATEST(likes - 1, 0) WHERE id = ?")->execute([$tid]);
    json_response(['ok' => true, 'liked' => false]);
}
$pdo->prepare("INSERT INTO likes (user_id, track_id) VALUES (?,?)")->execute([$uid, $tid]);
$pdo->prepare("UPDATE tracks SET likes = likes + 1 WHERE id = ?")->execute([$tid]);
json_response(['ok' => true, 'liked' => true]);