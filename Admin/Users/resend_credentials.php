<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../assets/mailer.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    exit('Forbidden');
}

$userId = $_POST['user_id'];

/* Fetch user */
$stmt = $pdo->prepare("SELECT name, email FROM admin_users WHERE id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$tempPassword = bin2hex(random_bytes(4));
$hash = password_hash($tempPassword, PASSWORD_DEFAULT);

/* Update password + force reset */
$stmt = $pdo->prepare("
    UPDATE admin_users
    SET password_hash=?, must_reset_password=1
    WHERE id=?
");
$stmt->execute([$hash, $userId]);

sendUserCredentialsSMTP($user['name'], $user['email'], $tempPassword);

$_SESSION['success'] = 'Credentials resent and password reset enforced';
header('Location: view.php?id=' . $userId);
exit;