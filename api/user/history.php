<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
$pdo = db();
$stmt = $pdo->prepare("
  SELECT t.*, MAX(ph.played_at) AS last_played
  FROM play_history ph JOIN tracks t ON t.id = ph.track_id
  WHERE ph.user_id = ?
  GROUP BY t.id
  ORDER BY last_played DESC LIMIT 50");
$stmt->execute([current_user()['id']]);
$out = array_map(fn($r) => array_merge(track_payload($r), ['last_played' => $r['last_played']]), $stmt->fetchAll());
json_response(['tracks' => $out]);