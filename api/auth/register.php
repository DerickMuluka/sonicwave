<?php
require_once __DIR__ . '/../../includes/init.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid session token'], 403);

$username = clean_str($in['username'] ?? '', 50);
$email = clean_email($in['email'] ?? '');
$password = (string)($in['password'] ?? '');

if (strlen($username) < 3) json_response(['error' => 'Username must be at least 3 characters'], 422);
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) json_response(['error' => 'Username may only contain letters, numbers, and underscores'], 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_response(['error' => 'Please enter a valid email address'], 422);
if (strlen($password) < 8) json_response(['error' => 'Password must be at least 8 characters'], 422);

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) json_response(['error' => 'Username or email is already in use'], 409);

$hash = password_hash($password, PASSWORD_BCRYPT);
$pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?,?,?)")->execute([$username, $email, $hash]);
$id = (int)$pdo->lastInsertId();
audit_log('user.register', 'user', $id, ['username' => $username]);
json_response(['ok' => true, 'id' => $id, 'username' => $username]);