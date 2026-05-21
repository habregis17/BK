<?php
session_start();

/**
 * If admin is already logged in,
 * send them to the dashboard
 */
if (isset($_SESSION['admin_id'])) {
    header('Location: /BK/Backyard/dashboard/index.php');
    exit;
}

/**
 * Otherwise, always go to login
 */
header('Location: /BK/Backyard/auth/login.php');
exit;