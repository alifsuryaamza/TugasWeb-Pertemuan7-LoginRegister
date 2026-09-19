<?php
require 'functions.php';
require_login(); 

$flash = get_flash();

$current = null;
foreach (load_users() as $u) {
    if ($u['id'] === $_SESSION['user_id']) {
        $current = $u;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h1>Dashboard</h1>

    <?php if ($flash): ?>
        <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <p class="sub">Halo, <strong><?= e($_SESSION['name']) ?></strong> 👋</p>

    <table class="info">
        <tr><th>Nama</th><td><?= e($_SESSION['name']) ?></td></tr>
        <tr><th>Email</th><td><?= e($_SESSION['email']) ?></td></tr>
        <tr><th>Terdaftar</th><td><?= e($current['created_at'] ?? '-') ?></td></tr>
    </table>

    <a class="btn-out" href="logout.php">Logout</a>
</div>
</body>
</html>
