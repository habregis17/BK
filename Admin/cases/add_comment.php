<?php
require '../auth/auth_check.php';
require '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caseId      = $_POST['case_id']; // Case numbers are strings, don't cast to (int)
    $comment     = trim($_POST['comment']);
    $type        = $_POST['comment_type']; // internal | client
    $createdBy  = $_SESSION['admin_name'];

    if ($comment !== '' && in_array($type, ['internal','client'])) {
        $stmt = $pdo->prepare("
            INSERT INTO case_comments (case_id, comment, comment_type, created_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$caseId, $comment, $type, $createdBy]);
    }

    header("Location: view.php?casenumber=$caseId");
    exit;
}