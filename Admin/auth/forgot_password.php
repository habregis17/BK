<?php
require '../../config/db.php';
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE email=?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin) {
        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $pdo->prepare('INSERT INTO admin_password_resets (admin_id, token_hash, expires_at)
                       VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))')
            ->execute([$admin['id'], $hash]);

        // send email with link:
        // /Admin/auth/reset_password.php?token=$token
    }

    $message = 'If the email exists, a reset link has been sent.';
}
?>