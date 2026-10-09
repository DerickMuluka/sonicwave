<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);

$mood = clean_str($in['mood'] ?? 'chill', 40);
$ids  = ai_build_dj_queue(current_user()['id'], $mood, 20);

$pdo = db();
if (!$ids) {
    $rows = $pdo->query("SELECT * FROM tracks WHERE is_approved = 1 ORDER BY RAND() LIMIT 20")->fetchAll();
    $fallback = true;
} else {
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM tracks WHERE id IN ($ph)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    $byId = [];
    foreach ($rows as $r) $byId[(int)$r['id']] = $r;
    $ordered = [];
    foreach ($ids as $id) if (isset($byId[$id])) $ordered[] = $byId[$id];
    $rows = $ordered;
    $fallback = false;
}

$out = array_map(fn($r) => track_payload($r), $rows);

json_response([
    'tracks'   => $out,
    'mood'     => $mood,
    'count'    => count($out),
    'fallback' => $fallback,
]);