<?php
require '../auth/auth_check.php';
require '../../config/db.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    exit('Forbidden');
}

$stmt = $pdo->prepare("
    UPDATE admin_users
    SET name=?, telephone=?, user_type=?, status=?
    WHERE id=?
");

$stmt->execute([
    $_POST['name'],
    $_POST['telephone'],
    $_POST['user_type'],
    $_POST['status'],
    $_POST['user_id']
]);

$_SESSION['success'] = 'User updated successfully';
header('Location: view.php?id=' . $_POST['user_id']);
exit;