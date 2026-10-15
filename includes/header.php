<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php" aria-label="<?= e(APP_NAME) ?> home">
            <img src="assets/img/djangogirls-logo.jpg" alt="Django Girls logo" class="logo"
                 onerror="this.onerror=null;this.src='assets/img/img/djangogirls-logo.jpg';">
        </a>
        <nav class="nav" aria-label="Main navigation">
            <a href="index.php">Home</a>
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php">Dashboard</a>
                <form action="logout.php" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-small btn-outline">Log out</button>
                </form>
            <?php else: ?>
                <a href="login.php">Log in</a>
                <a href="register.php" class="btn btn-small">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
<?php foreach (get_flashes() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?>" role="alert"><?= e($f['message']) ?></div>
<?php endforeach; ?>
