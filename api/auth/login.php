<?php
require_once __DIR__ . '/../../includes/init.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid session token'], 403);

$email = clean_email($in['email'] ?? '');
$password = (string)($in['password'] ?? '');
if (!$email || !$password) json_response(['error' => 'Email and password are required'], 422);

$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? ['count' => 0, 'ts' => time()];
if (time() - $_SESSION['login_attempts']['ts'] > 300) $_SESSION['login_attempts'] = ['count' => 0, 'ts' => time()];
if ($_SESSION['login_attempts']['count'] >= 8) json_response(['error' => 'Too many attempts. Try again later.'], 429);

$pdo = db();
$stmt = $pdo->prepare("SELECT id, username, email, password_hash, role, is_banned FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['login_attempts']['count']++;
    json_response(['error' => 'Invalid email or password'], 401);
}
if ((int)$user['is_banned'] === 1) json_response(['error' => 'This account has been suspended'], 403);

$_SESSION['login_attempts'] = ['count' => 0, 'ts' => time()];
login_user($user);
$pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
audit_log('user.login', 'user', (int)$user['id']);
json_response(['ok' => true, 'user' => ['id' => (int)$user['id'], 'username' => $user['username'], 'role' => $user['role']]]);