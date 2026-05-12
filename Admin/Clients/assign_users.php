<?php
require '../auth/auth_check.php';
require '../../config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SESSION['user_type'] !== 'Super Admin') {
    http_response_code(403);
    exit('Access denied');
}

$clientToken = trim($_POST['client_token'] ?? '');
$userIds = $_POST['users'] ?? [];

if ($clientToken === '') {
    throw new Exception('Client token missing');
}

// ✅ user_id is VARCHAR (e.g. ADM2025001)
$userIds = array_values(array_filter($userIds));

try {
    $pdo->beginTransaction();

    // Clear existing assignments
    $pdo->prepare(
        "DELETE FROM user_client_assignments WHERE client_token = ?"
    )->execute([$clientToken]);

    // Insert new assignments
    if (!empty($userIds)) {
        $stmt = $pdo->prepare(
            "INSERT INTO user_client_assignments (client_token, user_id)
             VALUES (?, ?)"
        );
        foreach ($userIds as $uid) {
            $stmt->execute([$clientToken, $uid]);
        }
    }

    $pdo->commit();
    $_SESSION['success'] = 'Client user assignments updated successfully.';

} catch (Throwable $e) {
    $pdo->rollBack();
    echo '<pre style="color:red;font-weight:bold">';
    echo $e->getMessage();
    echo '</pre>';
    exit;
}

header('Location: view.php?token=' . urlencode($clientToken));
exit;