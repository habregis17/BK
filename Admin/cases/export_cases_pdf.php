<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

while (ob_get_level()) {
    ob_end_clean();
}

require '../../config/db.php';
require_once '../../utils/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/* -----------------------------
   DOMPDF SETUP
------------------------------ */
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

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
   FETCH ALL CASES
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
    die('No cases found.');
}



/* -----------------------------
   PDF HEADER VARIABLES
------------------------------ */
$systemLogo = '../assets/images/ourlogo.png';

/* -----------------------------
   BASE STYLES (ONCE)
------------------------------ */
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page {
  margin: 120px 40px 60px 40px;
}

body {
  font-family: "DejaVu Sans", "Trebuchet MS", sans-serif;
  font-size: 12px;
  color: #333;
}

header {
  position: fixed;
  top: -100px;
  left: 0;
  right: 0;
  height: 90px;
}

header img {
  height: 45px;
}

header h1 {
  color: #ED1A3B;
  margin: 0;
}

footer {
  position: fixed;
  bottom: -40px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: 10px;
  color: #777;
}

section {
  margin-bottom: 20px;
  border-bottom: 1px dashed #ddd;
  padding-bottom: 10px;
}

h3 {
  color: #ED1A3B;
  border-left: 4px solid #ED1A3B;
  padding-left: 8px;
}

label {
  font-weight: bold;
  display: block;
  margin-top: 8px;
}

.field {
  background: #fafafa;
  border: 1px solid #ddd;
  padding: 6px;
  margin-bottom: 4px;
}

.fieldstatus {
  background: #98002e;
  color: white;
  padding: 6px;
  font-weight: bold;
  display: inline-block;
}

.page-break {
  page-break-before: always;
}
</style>
</head>
<body>

<header>
  <img src="$systemLogo" alt="Logo">
  <h1>Whistleblower Case Management System</h1>
</header>

<footer>
  BDO East Africa(Rwanda) Whistleblowing Management System | Page <span class="pagenum"></span>
</footer>
HTML;

/* -----------------------------
   LOOP THROUGH CASES
------------------------------ */
foreach ($cases as $index => $case) {

    if ($index > 0) {
        $html .= '<div class="page-break"></div>';
    }


    $identity = strtolower($case['identity_choice']);
    $showIdentity = in_array($identity, [
        'identifiable',
        'identified',
        'identifiable to bdo only'
    ]);

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



    $html .= "
    <main>
      <section>
        <label>Case Number</label>
        <div class='field'>{$case['casenumber']}</div>

        <label>Status</label>
        <div class='fieldstatus'>{$case['status']}</div>

        <label>Sensitivity</label>
        <div class='field'>{$case['Case_sensitivity']}</div>

        <label>Relevance</label>
        <div class='field'>{$case['Case_relevance']}</div>

        <label>Submitted At</label>
        <div class='field'>{$case['submitted_at']}</div>
      </section>

      <section>
        <h3>Reporter Information</h3>
        <label>Client</label>
        <div class='field'>{$case['client_name']}</div>

        <label>Anonymity</label>
        <div class='field'>{$case['identity_choice']}</div>";

    if ($showIdentity) {
        $html .= "
        <label>Full Name</label>
        <div class='field'>{$case['full_name']}</div>

        <label>Email</label>
        <div class='field'>{$case['email']}</div>

        <label>Telephone</label>
        <div class='field'>{$case['phone']}</div>";
    }

    $html .= "
      </section>

      <section>
        <h3>Incident Details</h3>
        <label>When</label>
        <div class='field'>{$case['incident_when']}</div>

        <label>Where</label>
        <div class='field'>{$case['incident_where']}</div>

        <label>Division</label>
        <div class='field'>{$case['incident_division']}</div>

        <label>Description</label>
        <div class='field'>{$case['incident_description']}</div>

        <label>Feedback</label>
        <div class='field'>{$closureComment}</div>
      </section>
    </main>";
}

$html .= '</body></html>';

/* -----------------------------
   RENDER PDF
------------------------------ */
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();

// Check if this is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfOutput));
    header('Content-Disposition: attachment; filename="Cases_Export.pdf"');
    echo $pdfOutput;
} else {
    $dompdf->stream('Cases_Export.pdf', ['Attachment' => false]);
}
exit;