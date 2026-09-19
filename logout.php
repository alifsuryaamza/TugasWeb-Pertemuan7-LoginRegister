<?php
require 'functions.php';

if (isset($_SESSION['user_id'])) {
    $users = load_users();
    foreach ($users as &$u) {
        if ($u['id'] === $_SESSION['user_id']) {
            unset($u['remember_token']);
        }
    }
    unset($u);
    save_users($users);
}
clear_remember_cookie();

$_SESSION = [];
session_destroy();

session_start();
set_flash('success', 'Kamu sudah logout.');
header('Location: login.php');
exit;
