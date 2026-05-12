<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../");
    exit();
}

require '../../../config/db.php';

$case_number = $_POST['case_number'] ?? '';
$status = $_POST['status'] ?? '';
$feedback = $_POST['feedback'] ?? '';
$sensitivity = $_POST['sensitivity'] ?? '';
$updated_by = $_SESSION['email'] ?? '';
$casemanager = $_POST['casemanager']?? '';

// Update query now includes updated_by
$stmt = $pdo->prepare("UPDATE cases SET status = ?, feedback = ?, updated_by = ?, Case_sensitivity = ?, Case_manager = ? WHERE casenumber = ?");
$stmt->execute([$status, $feedback, $updated_by,$sensitivity,$casemanager, $case_number]);

header("Location:../Cases/?casenumber=" . urlencode($case_number));
exit();
