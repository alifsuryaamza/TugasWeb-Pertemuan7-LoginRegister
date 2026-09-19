<?php
require 'functions.php';
require_guest();

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = e($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (mb_strlen($name) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    if ($email === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (empty($errors) && find_user_by_email($email)) {
        $errors[] = 'Email sudah terdaftar, silakan pakai email lain atau login.';
    }

    if (empty($errors)) {
        $users = load_users();
        $users[] = [
            'id'         => uniqid('u_', true),
            'name'       => $name,
            'email'      => e($email),
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (save_users($users)) {
            set_flash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: login.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan data. Cek permission folder data/.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h1>Buat Akun</h1>
    <p class="sub">Isi data di bawah untuk mendaftar.</p>

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
        <label for="name">Nama</label>
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Konfirmasi Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Daftar</button>
    </form>

    <p class="foot">Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</body>
</html>
