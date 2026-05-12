<?php
require '../auth/auth_check.php';
require '../../config/db.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
  http_response_code(403);
  exit('Access denied');
}

function generateUniqueId(PDO $pdo): string {
  do {
    $id = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE token = ?");
    $stmt->execute([$id]);
  } while ($stmt->fetch());
  return $id;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$description = trim($_POST['description'] ?? '');
$bdoContact = trim($_POST['bdo_contact'] ?? '');
$clientContact = trim($_POST['client_contact'] ?? '');
$userIds = $_POST['users'] ?? [];

if ($name === '') {
  $_SESSION['error'] = 'Client name is required';
  header('Location: clients.php');
  exit;
}

$token = generateUniqueId($pdo);

try {
  $pdo->beginTransaction();

  // Insert client
  $stmt = $pdo->prepare("
    INSERT INTO clients (name, email, Telephone, Description, BDO_contact, Client_Contact, token)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $name,
    $email,
    $telephone,
    $description,
    $bdoContact,
    $clientContact,
    $token
  ]);

  // Assign users
  if (!empty($userIds)) {
    $assign = $pdo->prepare(
      "INSERT INTO user_client_assignments (client_token, user_id) VALUES (?, ?)"
    );

    foreach ($userIds as $uid) {
      $assign->execute([$token, $uid]);
    }
  }

  $pdo->commit();
  $_SESSION['success'] = 'Client added successfully';

} catch (Throwable $e) {
  $pdo->rollBack();
  $_SESSION['error'] = 'Failed to add client';
}

header('Location: view.php?token=' . urlencode($token));
exit;
