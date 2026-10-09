<?php
require_once __DIR__ . '/../../includes/init.php';
require_admin_api();
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = $pdo->query("SELECT u.id, u.username, u.email, u.role, u.is_banned, u.created_at,
        (SELECT COUNT(*) FROM tracks t WHERE t.user_id = u.id) AS track_count
        FROM users u ORDER BY u.created_at DESC LIMIT 200")->fetchAll();
    json_response(['users' => $rows]);
}
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$action = $in['action'] ?? ''; $uid = (int)($in['user_id'] ?? 0);
if (!$uid) json_response(['error' => 'Missing user id'], 422);
if ($uid === (int)current_user()['id']) json_response(['error' => 'Cannot modify your own account'], 403);
if ($action === 'ban') { $pdo->prepare("UPDATE users SET is_banned = 1 WHERE id = ?")->execute([$uid]); audit_log('admin.user.ban','user',$uid); json_response(['ok' => true]); }
if ($action === 'unban') { $pdo->prepare("UPDATE users SET is_banned = 0 WHERE id = ?")->execute([$uid]); json_response(['ok' => true]); }
if ($action === 'promote') {
    $role = in_array($in['role'] ?? '', ['moderator','admin'], true) ? $in['role'] : 'user';
    $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $uid]);
    audit_log('admin.user.role','user',$uid,['role'=>$role]);
    json_response(['ok' => true]);
}
json_response(['error' => 'Unknown action'], 400);