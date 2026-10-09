<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();

$pid = (int)($_GET['playlist_id'] ?? 0);
if (!$pid) json_response(['error' => 'Missing playlist_id'], 422);

$pdo = db();
$uid = current_user()['id'];

$stmt = $pdo->prepare("SELECT id, user_id, name, description, is_public FROM playlists WHERE id = ? LIMIT 1");
$stmt->execute([$pid]);
$pl = $stmt->fetch();
if (!$pl) json_response(['error' => 'Playlist not found'], 404);
if ((int)$pl['user_id'] !== $uid && (int)$pl['is_public'] !== 1) {
    json_response(['error' => 'You do not have access to this playlist'], 403);
}

$stmt = $pdo->prepare("
    SELECT t.*,
           (SELECT COUNT(*) FROM likes l WHERE l.user_id = ? AND l.track_id = t.id) AS liked
    FROM playlist_tracks pt
    JOIN tracks t ON t.id = pt.track_id
    WHERE pt.playlist_id = ?
    ORDER BY pt.position ASC, pt.added_at ASC
");
$stmt->execute([$uid, $pid]);
$rows = $stmt->fetchAll();

$out = [];
foreach ($rows as $r) {
    $p = track_payload($r, [(int)$r['id']]);
    $p['liked'] = (bool)$r['liked'];
    $out[] = $p;
}

json_response([
    'playlist' => [
        'id' => (int)$pl['id'],
        'name' => $pl['name'],
        'description' => $pl['description'],
        'is_public' => (int)$pl['is_public'],
        'is_owner' => (int)$pl['user_id'] === $uid,
    ],
    'tracks' => $out,
    'count' => count($out),
]);