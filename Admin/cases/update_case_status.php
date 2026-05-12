<?php
require '../auth/auth_check.php';
require '../../config/db.php';

$caseId          = $_POST['case_id'];
$newStatus       = $_POST['status'] ?? '';
$sensitivity = $_POST['sensitivity'] ?? '';
$relevance       = $_POST['relevance'] ?? '';
$closureComment  = trim($_POST['closure_comment'] ?? '');
$changedBy       = $_SESSION['admin_name'];

/* Fetch current status */
$stmt = $pdo->prepare("SELECT status FROM cases WHERE casenumber = ?");
$stmt->execute([$caseId]);
$currentStatus = $stmt->fetchColumn();

/* ✅ ENFORCE CLOSURE COMMENT */
if ($newStatus === 'Closed' && $currentStatus !== 'Closed') {
    if ($closureComment === '') {
        $_SESSION['error'] = 'A closure comment is required when closing a case.';
        header("Location: view.php?casenumber=$caseId");
        exit;
    }
}

/* Update case status */
$pdo->prepare("UPDATE cases SET status=?, Case_sensitivity=?, Case_relevance=? WHERE casenumber=?")
    ->execute([$newStatus, $sensitivity, $relevance, $caseId]);


    /* Record status audit */
$pdo->prepare("
        INSERT INTO case_status_audit
        (case_id, old_status, new_status, changed_by)
        VALUES (?, ?, ?, ?)
    ")->execute([$caseId, $currentStatus, $newStatus, $changedBy]);

$pdo->prepare("
            INSERT INTO case_comments
            (case_id, comment, comment_type, created_by)
            VALUES (?, ?, 'internal', ?)
        ")->execute([$caseId, $closureComment, $changedBy]);
    
if ($currentStatus !== $newStatus) {

    if ($newStatus === 'Closed') {
        $_SESSION['success'] = 'Case closed successfully.';
    } else {
        $_SESSION['success'] = 'Case details updated successfully.';
    }

} else {
    $_SESSION['success'] = 'Case saved (no status change).';
}

header("Location: view.php?casenumber=$caseId");
exit;