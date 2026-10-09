<?php
require_once __DIR__ . '/../config/config.php';

function current_user(): ?array { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return !empty($_SESSION['user']['id']); }
function is_admin(): bool { return (current_user()['role'] ?? '') === 'admin'; }
function is_moderator(): bool { return in_array(current_user()['role'] ?? '', ['admin','moderator'], true); }

function require_login(): void {
    if (!is_logged_in()) { header('Location: ' . url('login.php')); exit; }
}
function require_admin(): void {
    if (!is_logged_in()) { header('Location: ' . url('login.php')); exit; }
    if (!is_admin()) { header('Location: ' . url('index.php')); exit; }
}
function require_login_api(): void {
    if (!is_logged_in()) json_response(['error' => 'Authentication required'], 401);
}
function require_admin_api(): void {
    require_login_api();
    if (!is_admin()) json_response(['error' => 'Admin access required'], 403);
}
function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'       => (int)$user['id'],
        'username' => $user['username'],
        'email'    => $user['email'],
        'avatar'   => $user['avatar'] ?? null,
        'role'     => $user['role'] ?? 'user',
    ];
}