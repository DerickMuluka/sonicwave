<?php
require_once __DIR__ . '/../../includes/init.php';
require_login_api();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['error' => 'Method not allowed'], 405);
$in = json_input();
if (!csrf_verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($in['csrf'] ?? ''))) json_response(['error' => 'Invalid token'], 403);
$text = clean_str($in['text'] ?? '', 500);
json_response(['mood' => ai_detect_mood($text)]);