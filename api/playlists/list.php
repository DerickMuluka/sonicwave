<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
$pdo = db();
$stmt = $pdo->prepare("SELECT p.id, p.name, p.description, p.is_public,
    (SELECT COUNT(*) FROM playlist_tracks pt WHERE pt.playlist_id = p.id) AS track_count
    FROM playlists p WHERE p.user_id = ? ORDER BY p.created_at DESC");
$stmt->execute([current_user()['id']]);
$out = array_map(fn($r) => [
    'id' => (int)$r['id'], 'name' => $r['name'], 'description' => $r['description'],
    'is_public' => (int)$r['is_public'], 'track_count' => (int)$r['track_count'],
], $stmt->fetchAll());
json_response(['playlists' => $out]);