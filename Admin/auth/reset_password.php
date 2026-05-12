<?php
session_start();
require '../../config/db.php';

/* ✅ Check if user is allowed here */
if (empty($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password === '' || $confirm === '') {
        $error = "All fields are required";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE admin_users
            SET password_hash = ?, must_reset_password = 0
            WHERE id = ?
        ");

        $stmt->execute([$hash, $_SESSION['user_id']]);

        /* ✅ Clear reset session */
        unset($_SESSION['user_id']);

        $_SESSION['success'] = "Password updated successfully. Please login.";
        header('Location: ../auth/login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="auth-body">

<div class="auth-card">

  <img src="../assets/images/ourlogo.png" class="auth-logo">

  <h2>Reset Your Password</h2>

  <p class="muted">You must change your password before continuing</p>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (!empty($_SESSION['error'])): ?>
    <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>

  <form method="POST">

    <input type="password" name="password" placeholder="New Password" required>

    <input type="password" name="confirm" placeholder="Confirm Password" required>

    <button type="submit" class="primary-btn">
      Update Password
    </button>

  </form>

</div>

</body>
</html>