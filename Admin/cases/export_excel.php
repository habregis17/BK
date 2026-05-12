<?php
ini_set('display_errors', 0);
error_reporting(0);

while (ob_get_level()) {
    ob_end_clean();
}

require '../../config/db.php';
require_once '../../admin-old/Dashboard/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* -----------------------------
   INPUT
------------------------------ */
$casenumber = $_GET['casenumber'] ?? '';
if (!$casenumber) {
    http_response_code(400);
    exit;
}

/* -----------------------------
   CASE DATA
------------------------------ */
$stmt = $pdo->prepare("
    SELECT cases.*, clients.name AS client_name
    FROM cases
    JOIN clients ON cases.client_token = clients.token
    WHERE cases.casenumber = ?
");
$stmt->execute([$casenumber]);
$case = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$case) {
    http_response_code(404);
    exit;
}

/* -----------------------------
   COMMENTS (FOR CLOSURE FEEDBACK)
------------------------------ */
$commentsStmt = $pdo->prepare("
    SELECT comment
    FROM case_comments
    WHERE case_id = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$commentsStmt->execute([$case['casenumber']]);
$closureComment = $commentsStmt->fetchColumn() ?: '';

/* -----------------------------
   SPREADSHEET
------------------------------ */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Case Details');

/* -----------------------------
   HEADERS & VALUES
------------------------------ */
$data = [
    'Case Number' => $case['casenumber'],
    'Status' => $case['status'],
    'Sensitivity' => $case['Case_sensitivity'],
    'Relevance' => $case['Case_relevance'],
    'Submitted At' => $case['submitted_at'],
    'Client' => $case['client_name'],
    'Anonymity' => $case['identity_choice'],
    'Incident When' => $case['incident_when'],
    'Incident Where' => $case['incident_where'],
    'Division' => $case['incident_division'],
    'Description' => $case['incident_description'],
    'Feedback' => $closureComment
];

/* -----------------------------
   WRITE ROWS
------------------------------ */
$col = 'A';
foreach ($data as $header => $value) {
    $sheet->setCellValue($col.'1', $header);
    $sheet->setCellValue($col.'2', $value);
    $col++;
}

/* -----------------------------
   STYLE HEADER
------------------------------ */
$sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);

foreach (range('A', $sheet->getHighestColumn()) as $c) {
    $sheet->getColumnDimension($c)->setAutoSize(true);
}

/* -----------------------------
   OUTPUT
------------------------------ */
$filename = 'Case_' . $casenumber . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;