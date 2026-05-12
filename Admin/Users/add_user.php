<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../assets/mailer.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    http_response_code(403);
    exit('Access denied');
}

/* -----------------------------
   INPUT
------------------------------ */
$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$userType  = $_POST['user_type'] ?? '';
$status    = $_POST['status'] ?? 'Active';
$clients   = $_POST['clients'] ?? [];

/* -----------------------------
   VALIDATION
------------------------------ */
if ($name === '' || $email === '' || $userType === '') {
    $_SESSION['error'] = 'Required fields are missing';
    header('Location: index.php');
    exit;
}

// Avoid duplicate emails

$stmt2 = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
    $stmt2->execute([$email]);

    if ($stmt2->fetch()) {
    $_SESSION['error'] = "Email already exists. Use another email.";
    header('Location: index.php');
    exit;
    }


/* -----------------------------
   TEMP PASSWORD
------------------------------ */
$tempPassword = bin2hex(random_bytes(4)); // 8 characters
$passwordHash = password_hash($tempPassword, PASSWORD_DEFAULT);

/* -----------------------------
User ID
------------------------------ */

function generateAdminId(PDO $pdo): string {
    $year = date('Y');

    $stmt = $pdo->prepare("
        SELECT id 
        FROM admin_users 
        WHERE id LIKE ?
        ORDER BY id DESC 
        LIMIT 1
    ");

    $like = "ADM{$year}%";
    $stmt->execute([$like]);

    $lastId = $stmt->fetchColumn();

    if ($lastId) {
        // Extract numeric part
        $number = (int) substr($lastId, -3);
        $next = $number + 1;
    } else {
        $next = 1;
    }

    return 'ADM' . $year . str_pad($next, 3, '0', STR_PAD_LEFT);
}
$Adminid = generateAdminId($pdo);

/* -----------------------------
   TRANSACTION
------------------------------ */
try {
    $pdo->beginTransaction();
    

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO admin_users
        (id, name, email, telephone, user_type, status, password_hash, must_reset_password, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
    ");

    $stmt->execute([
        $Adminid,
        $name,
        $email,
        $telephone,
        $userType,
        $status,
        $passwordHash
    ]);

    

    $userId = $pdo->lastInsertId();
    $pdo->commit();

    /* -----------------------------
       SEND EMAIL
    ------------------------------ */
    
    $result = sendUserCredentialsSMTP($name, $email, $tempPassword);

    if (!$result) {
        error_log("EMAIL FAILED for: $email");
        $_SESSION['error'] = 'User created, but email failed.';
    } else {
        $_SESSION['success'] = 'User created and email sent.';
    }

} 


catch (Throwable $e) {
    return false;
}


/* -----------------------------
   REDIRECT
------------------------------ */
header('Location: index.php');
exit;