<?php
session_start();

define('USERS_FILE', __DIR__ . '/data/users.json');
define('REMEMBER_DAYS', 30);

function e($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

function load_users() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $data = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users($users) {
    $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

function find_user_by_email($email) {
    foreach (load_users() as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return $user;
        }
    }
    return null;
}

function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function login_user($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
}

function set_remember_cookie($userId) {
    $token   = bin2hex(random_bytes(32));
    $users   = load_users();
    foreach ($users as &$u) {
        if ($u['id'] === $userId) {
            $u['remember_token'] = hash('sha256', $token);
        }
    }
    unset($u);
    save_users($users);
    setcookie('remember_me', $userId . ':' . $token, [
        'expires'  => time() + 86400 * REMEMBER_DAYS,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function clear_remember_cookie() {
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/');
    }
}

function check_remember_cookie() {
    if (isset($_SESSION['user_id']) || empty($_COOKIE['remember_me'])) {
        return;
    }
    $parts = explode(':', $_COOKIE['remember_me'], 2);
    if (count($parts) !== 2) {
        return;
    }
    [$id, $token] = $parts;
    foreach (load_users() as $user) {
        if ($user['id'] === $id
            && !empty($user['remember_token'])
            && hash_equals($user['remember_token'], hash('sha256', $token))) {
            login_user($user);
            return;
        }
    }
}

function is_logged_in() {
    check_remember_cookie();
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Silakan login dulu untuk mengakses dashboard.');
        header('Location: login.php');
        exit;
    }
}

function require_guest() {
    if (is_logged_in()) {
        header('Location: dashboard.php');
        exit;
    }
}
