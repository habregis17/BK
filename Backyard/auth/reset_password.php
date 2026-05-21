<?php
session_start();
require '../../config/db.php';

/* ✅ Check if user is allowed here */
if (empty($_SESSION['reset_user_id'])) {
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

        $stmt->execute([$hash, $_SESSION['reset_user_id']]);

        /* ✅ Clear reset session */
        unset($_SESSION['reset_user_id']);

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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #e81a3b; /* BDO Red */
      --secondary-color: #333;
      --glass-bg: rgba(255, 255, 255, 0.9);
    }

    body.auth-body {
      margin: 0;
      padding: 0;
      font-family: 'Proxima Nova', 'Montserrat', sans-serif;
      background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), 
                  url('https://wallpapercat.com/w/full/d/d/4/1603108-1920x1080-desktop-1080p-kigali-rwanda-wallpaper-photo.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .auth-card {
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      padding: 3rem;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 420px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .auth-logo {
      max-width: 120px;
      margin-bottom: 1.5rem;
    }

    h2 {
      color: #333;
      font-weight: 700;
      margin-bottom: 1rem;
      font-size: 1.5rem;
      letter-spacing: -0.5px;
    }

    .muted {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 2rem;
    }

    .auth-card input {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: inherit;
      box-sizing: border-box;
    }

    .auth-card button {
      width: 100%;
      padding: 12px;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s, background-color 0.2s;
      margin-top: 1rem;
    }

    .auth-card button:hover {
      background-color: var(--secondary-color);
      transform: translateY(-1px);
    }

    .error {
      background: #fee2e2;
      color: #dc2626;
      padding: 10px;
      border-radius: 8px;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }
  </style>
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

    <input type="password" name="password" placeholder="New Password" required autocomplete="new-password">

    <input type="password" name="confirm" placeholder="Confirm Password" required autocomplete="new-password">

    <button type="submit">
      Update Password
    </button>

  </form>

</div>

</body>
</html>