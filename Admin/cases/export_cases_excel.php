<?php
/* =====================================================
   EXPORT ALL CASES TO EXCEL — SAME COLUMNS AS BEFORE
===================================================== */

ob_start();
ini_set('display_errors', 0);
error_reporting(0);

require '../../config/db.php';
require '../../admin-old/Dashboard/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* -----------------------------
   FILTERS (SAME AS index.php)
------------------------------ */
$where = [];
$params = [];

if (!empty($_GET['status'])) {
    $where[] = 'cases.status = ?';
    $params[] = $_GET['status'];
}

if (!empty($_GET['sensitivity'])) {
    $where[] = 'cases.Case_sensitivity = ?';
    $params[] = $_GET['sensitivity'];
}

if (!empty($_GET['relevance'])) {
    $where[] = 'cases.Case_relevance = ?';
    $params[] = $_GET['relevance'];
}

if (!empty($_GET['search'])) {
    $where[] = '(cases.casenumber LIKE ? OR clients.name LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

if (!empty($_GET['date_from'])) {
    $where[] = 'cases.submitted_at >= ?';
    $params[] = $_GET['date_from'];
}

if (!empty($_GET['date_to'])) {
    $where[] = 'cases.submitted_at <= ?';
    $params[] = $_GET['date_to'];
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* -----------------------------
   FETCH CASES
------------------------------ */
$sql = "
  SELECT cases.*, clients.name AS client_name
  FROM cases
  JOIN clients ON cases.client_token = clients.token
  $whereSql
  ORDER BY cases.submitted_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cases) {
    exit;
}

/* -----------------------------
   EXCEL SETUP
------------------------------ */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Cases Export');

/* -----------------------------
   HEADERS (UNCHANGED)
------------------------------ */
$headers = [
    'Case Number',
    'Status',
    'Sensitivity',
    'Relevance',
    'Submitted At',
    'Client',
    'Anonymity',
    'Incident When',
    'Incident Where',
    'Division',
    'Description',
    'Feedback'
];

$sheet->fromArray($headers, null, 'A1');
$sheet->getStyle('A1:L1')->getFont()->setBold(true);

/* -----------------------------
   DATA ROWS
------------------------------ */
$row = 2;

foreach ($cases as $case) {

    /* Fetch comments */
    $commentsStmt = $pdo->prepare("
    SELECT * FROM case_comments
    WHERE case_id = ?
    ORDER BY created_at ASC
    ");
    $commentsStmt->execute([$case['casenumber']]);
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

    $closureComment = '';
    if (!empty($comments)) {
    $lastComment = end($comments);
    $closureComment = $lastComment['comment'] ?? '';
    reset($comments);
    }

    $sheet->fromArray([
        $case['casenumber'],
        $case['status'],
        $case['Case_sensitivity'],
        $case['Case_relevance'],
        $case['submitted_at'],
        $case['client_name'],
        $case['identity_choice'],
        $case['incident_when'],
        $case['incident_where'],
        $case['incident_division'],
        $case['incident_description'],
        $closureComment
    ], null, 'A' . $row);

    $row++;
}

/* -----------------------------
   AUTOSIZE COLUMNS
------------------------------ */
foreach (range('A', 'L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* -----------------------------
   OUTPUT
------------------------------ */
ob_end_clean();

// Check if this is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Cases_Export.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
} else {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Cases_Export.xlsx"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}
exit;
