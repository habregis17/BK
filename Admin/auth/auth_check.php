<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (empty($_SESSION['admin_id'])) {

    // Save requested URL before redirect
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    header('Location: /BK/Admin/auth/login.php');
    exit;
}