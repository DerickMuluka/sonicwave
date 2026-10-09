<?php
require_once __DIR__ . '/../../includes/init.php';
require_admin_api();
$pdo = db();
json_response([
    'users'          => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'new_users_week' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE created_at > (NOW() - INTERVAL 7 DAY)")->fetchColumn(),
    'tracks'         => (int)$pdo->query("SELECT COUNT(*) FROM tracks")->fetchColumn(),
    'pending'        => (int)$pdo->query("SELECT COUNT(*) FROM tracks WHERE is_approved = 0")->fetchColumn(),
    'plays'          => (int)$pdo->query("SELECT COALESCE(SUM(plays),0) FROM tracks")->fetchColumn(),
    'active_today'   => (int)$pdo->query("SELECT COUNT(DISTINCT user_id) FROM play_history WHERE played_at > (NOW() - INTERVAL 1 DAY)")->fetchColumn(),
]);