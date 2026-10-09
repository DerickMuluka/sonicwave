<?php
require_once __DIR__ . '/../includes/init.php';
$user = current_user();
$pageTitle = $pageTitle ?? APP_NAME;
$extraCss = $extraCss ?? [];
$extraJs  = $extraJs ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#07080e">
<title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/player.css') ?>">
<link rel="stylesheet" href="<?= asset('css/nav.css') ?>">
<?php foreach ((array)$extraCss as $c): ?>
<link rel="stylesheet" href="<?= asset('css/' . $c) ?>">
<?php endforeach; ?>
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<script>window.BASE_URL = "<?= e(BASE_URL) ?>";window.CURRENT_USER = <?= json_encode($user) ?>;</script>
</head>
<body class="no-player">
<div class="ambient-bg" aria-hidden="true">
  <div class="ambient-orb orb-1"></div>
  <div class="ambient-orb orb-2"></div>
  <div class="ambient-orb orb-3"></div>
</div>
<div class="ambient-scrim" aria-hidden="true"></div>