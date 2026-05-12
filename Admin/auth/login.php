<?php
session_start();
require '../../config/db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$admin) {
    $error = 'Account not Found';
} else {

    if ($admin['status'] !== 'Active') {
        $error = "Your account is inactive. Contact administrator.";
    } 
    elseif (!password_verify($password, $admin['password_hash'])) {
        $error = 'Invalid email or password';
    } 
    else {

        if ($admin['must_reset_password'] == 1) {

        $_SESSION['user_id'] = $admin['id'];

        // preserve redirect
        if (!empty($_SESSION['redirect_after_login'])) {
            $_SESSION['redirect_after_reset'] = $_SESSION['redirect_after_login'];
        }

        header("Location: ../users/reset_password.php");
        exit;
        }

        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE admin_users SET password=? WHERE id=?')
                ->execute([$newHash, $admin['id']]);
        }

        session_regenerate_id(true);

        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['user_type']  = $admin['user_type'];

        if (isset($_SESSION['redirect_after_login'])) {
                $redirectUrl = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirectUrl");
                exit;
            } 
        else {
                header('Location: ../index.php');
                exit;
            }
    }
}
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="auth-body">

<div class="auth-card">
  <img src="../assets/images/ourlogo.png" class="auth-logo">
  <h2>BDO WB SYSTEM - Admin Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" action="login.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Enter</button>
  </form>

</div>

</body>
</html>