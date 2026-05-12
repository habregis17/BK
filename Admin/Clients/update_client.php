<?php
require '../auth/auth_check.php';
require '../../config/db.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    http_response_code(403);
    exit('Access denied');
}

// Validate required fields
if (!isset($_POST['client_id'], $_POST['name'])) {
    $_SESSION['error'] = 'Missing required fields';
    header('Location: view.php?token=' . $_POST['client_id']);
    exit;
}

$clientId = $_POST['client_id'];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$bdoContact = trim($_POST['bdo_contact'] ?? '');
$clientContact = trim($_POST['client_contact'] ?? '');

// Validate name
if (empty($name)) {
    $_SESSION['error'] = 'Client name is required';
    header('Location: view.php?token=' . $clientId);
    exit;
}

// Update client
try {
    $stmt = $pdo->prepare("
        UPDATE clients
        SET name = ?, email = ?, telephone = ?, BDO_contact = ?, Client_Contact = ?
        WHERE token = ?
    ");
    
    $success = $stmt->execute([
        $name,
        $email,
        $telephone,
        $bdoContact,
        $clientContact,
        $clientId
    ]);

    if ($success) {
        $_SESSION['success'] = 'Client details updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update client';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

header('Location: view.php?token=' . $clientId);
exit;
?>