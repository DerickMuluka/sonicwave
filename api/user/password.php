<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid session token'], 403);

$current = (string)($in['current_password'] ?? '');
$new     = (string)($in['new_password'] ?? '');
$confirm = (string)($in['confirm_password'] ?? '');

if (!$current || !$new || !$confirm) json_response(['error' => 'All fields are required'], 422);
if ($new !== $confirm) json_response(['error' => 'New passwords do not match'], 422);
if (strlen($new) < 8) json_response(['error' => 'New password must be at least 8 characters'], 422);

$pdo = db();
$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ? LIMIT 1");
$stmt->execute([current_user()['id']]);
$row = $stmt->fetch();
if (!$row) json_response(['error' => 'User not found'], 404);
if (!password_verify($current, $row['password_hash'])) json_response(['error' => 'Current password is incorrect'], 401);

$hash = password_hash($new, PASSWORD_BCRYPT);
$pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, current_user()['id']]);
audit_log('user.password.change', 'user', current_user()['id']);

json_response(['ok' => true]);