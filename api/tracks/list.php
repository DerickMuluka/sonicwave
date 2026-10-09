<?php
require_once __DIR__ . '/../../includes/init.php';

$pdo = db();
$uid = current_user()['id'] ?? 0;

$where = ["t.is_approved = 1"];
$args = [];

if (!empty($_GET['id'])) {
    $where[] = "t.id = ?"; $args[] = (int)$_GET['id'];
} else {
    if (!empty($_GET['genre'])) { $where[] = "t.genre = ?"; $args[] = clean_str($_GET['genre'], 60); }
    if (!empty($_GET['q'])) {
        $q = '%' . clean_str($_GET['q'], 100) . '%';
        $where[] = "(t.title LIKE ? OR t.artist LIKE ? OR t.album LIKE ? OR t.description LIKE ?)";
        $args[] = $q; $args[] = $q; $args[] = $q; $args[] = $q;
    }
    if (!empty($_GET['liked']) && $uid) {
        $where[] = "EXISTS (SELECT 1 FROM likes l WHERE l.user_id = ? AND l.track_id = t.id)";
        $args[] = $uid;
    }
    if (!empty($_GET['mine']) && $uid) {
        $where[] = "t.user_id = ?";
        $args[] = $uid;
    }
}

$limit = min(200, max(1, (int)($_GET['limit'] ?? 20)));
$offset = max(0, (int)($_GET['offset'] ?? 0));

$sql = "SELECT t.* FROM tracks t WHERE " . implode(' AND ', $where) . " ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll();

$likedIds = [];
if ($uid && $rows) {
    $ids = array_column($rows, 'id');
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $ls = $pdo->prepare("SELECT track_id FROM likes WHERE user_id = ? AND track_id IN ($ph)");
    $ls->execute(array_merge([$uid], $ids));
    $likedIds = array_column($ls->fetchAll(), 'track_id');
}

$out = array_map(fn($r) => track_payload($r, $likedIds), $rows);
json_response(['tracks' => $out]);