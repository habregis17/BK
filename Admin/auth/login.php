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

        // Check if mandatory password reset is required
        if ((int)$admin['must_reset_password'] === 1) {
            $_SESSION['reset_user_id'] = $admin['id'];

            // Preserve redirect target through reset process
            if (!empty($_SESSION['redirect_after_login'])) {
                $_SESSION['redirect_after_reset'] = $_SESSION['redirect_after_login'];
            }

            session_write_close();
            header("Location: reset_password.php");
            exit;
        }

        // Rehash password if algorithm has changed
        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE admin_users SET password_hash=? WHERE id=?')
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
      margin-bottom: 2rem;
      font-size: 1.5rem;
      letter-spacing: -0.5px;
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