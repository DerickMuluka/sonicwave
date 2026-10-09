<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
$limit = min(20, max(4, (int)($_GET['limit'] ?? 12)));
$ids = ai_recommend_for_user(current_user()['id'], $limit);
if (!$ids) json_response(['tracks' => []]);
$pdo = db();
$ph = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id IN ($ph)");
$stmt->execute($ids);
$out = array_map(fn($r) => track_payload($r), $stmt->fetchAll());
json_response(['tracks' => $out]);