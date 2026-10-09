<?php
require_once __DIR__ . '/../../includes/init.php';
require_admin_api();
$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = $pdo->query("SELECT t.id, t.title, t.artist, t.source, t.plays, t.likes, t.is_approved, t.created_at
        FROM tracks t ORDER BY t.created_at DESC LIMIT 200")->fetchAll();
    json_response(['tracks' => $rows]);
}
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$action = $in['action'] ?? ''; $tid = (int)($in['track_id'] ?? 0);
if ($action === 'delete' && $tid) {
    $stmt = $pdo->prepare("SELECT file_path, cover_path FROM tracks WHERE id = ? LIMIT 1");
    $stmt->execute([$tid]);
    $t = $stmt->fetch();
    if ($t) foreach ([$t['file_path'], $t['cover_path']] as $rel) {
        if ($rel && !str_starts_with($rel, 'http')) { $p = ROOT_PATH . '/' . ltrim($rel, '/'); if (is_file($p)) @unlink($p); }
    }
    $pdo->prepare("DELETE FROM tracks WHERE id = ?")->execute([$tid]);
    audit_log('admin.track.delete','track',$tid);
    json_response(['ok' => true]);
}
json_response(['error' => 'Unknown action'], 400);