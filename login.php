<?php
require 'functions.php';
require_guest();

$errors = [];
$email = '';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } else {
        $user = find_user_by_email($email);

        // Pesan sengaja dibuat sama biar tidak bocorin email mana yang terdaftar
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            login_user($user);
            if ($remember) {
                set_remember_cookie($user['id']);
            }
            set_flash('success', 'Login berhasil. Selamat datang, ' . $user['name'] . '!');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h1>Login</h1>
    <p class="sub">Masuk ke akun kamu.</p>

    <?php if ($flash): ?>
        <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label class="check">
            <input type="checkbox" name="remember"> Ingat saya (30 hari)
        </label>

        <button type="submit">Login</button>
    </form>

    <p class="foot">Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
</body>
</html>
