<?php
ob_start();
require '../../config/db.php';
require_once '../../utils/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);  // ✅ Required for remote images
$dompdf = new Dompdf($options);  // ✅ Use the options here

$casenumber = $_GET['casenumber'] ?? '';
if (!$casenumber) die("No case number.");

$stmt = $pdo->prepare("SELECT cases.*, clients.name AS client_name 
                       FROM cases 
                       JOIN clients ON cases.client_token = clients.token 
                       WHERE cases.casenumber = ?");
$stmt->execute([$casenumber]);
$case = $stmt->fetch();

if (!$case) die("Case not found.");

$identity = strtolower($case['identity_choice']);
$showIdentity = in_array($identity, ['identifiable', 'identifiable_to_bdo_only', 'identified']);


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

// ✅ Replace SVG logo with working PNG
$systemLogo = 'https://upload.wikimedia.org/wikipedia/commons/9/9e/BDO_Deutsche_Warentreuhand_Logo.svg';
$clientLogo = 'https://images.africanfinancials.com/rw-bok-logo-min.png';
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Case Report ' . htmlspecialchars($case['casenumber'] ?? '') . '</title>
<style>
  /* ----- Page setup ----- */
  @page {
    margin-top: 120px;  /* enough space for the fixed header */
    margin-bottom: 60px; /* enough space for footer */
    margin-left: 40px;
    margin-right: 40px;
  }

  body {
    font-family: "DejaVu Sans", "Trebuchet MS", sans-serif;
    color: #333;
    font-size: 12px;
    line-height: 1.5;
    margin: 0;
  }

  /* ----- Fixed Header ----- */
  header {
    position: fixed;
    top: -100px; /* pushes header into margin space */
    left: 0;
    right: 0;
    height: 90px;
    padding: 10px 0;
  }

  .header-container {
    width: 100%;
    display: table;
  }
  .header-left {
    display: table-cell;
    width: 25%;
    vertical-align: middle;
    text-align: left;
    padding-left: 20px;
  }
  .header-left img {
    height: 45px;
  }
  .header-right {
    display: table-cell;
    width: 75%;
    text-align: right;
    vertical-align: middle;
    padding-right: 20px;
  }
  .header-right h1 {
    color: #ED1A3B;
    margin: 0;
    font-size: 16px;
  }
  .header-right h2 {
    margin: 2px 0 0;
    font-size: 12px;
    color: #555;
  }

  /* ----- Fixed Footer ----- */
  footer {
    position: fixed;
    bottom: -40px;
    left: 0;
    right: 0;
    height: 40px;
    border-top: 1px solid #ccc;
    text-align: center;
    font-size: 10px;
    color: #777;
    padding-top: 5px;
  }

  /* ----- Main Content ----- */
  main {
    margin-top: 0;
  }

  section {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #ddd;
  }

  h3 {
    color: #ED1A3B;
    border-left: 4px solid #ED1A3B;
    padding-left: 8px;
    margin-bottom: 10px;
    font-size: 14px;
  }

  label {
    display: block;
    font-weight: bold;
    margin: 8px 0 4px;
    font-size: 12px;
    color: #444;
  }

  .field {
    background: #fafafa;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 12px;
  }

    .fieldstatus {
    background: #98002e;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 12px;
    font-weight:bold;
    color:white;
  }

  .textarea.field {
    white-space: pre-wrap;
  }

  .status-box {
    background: #98002e;
    color: #fff;
    font-weight: bold;
    padding: 6px 80px;
    border-radius: 4px;
    display: inline-block;
    min-width: 120px;
    text-align: center;
  }

  .sensitivity {
    color: #98002e;
    font-size: 13px;
    font-weight: bold;
  }

  /* Page number counter */
  .pagenum:before {
    content: counter(page);
  }
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <div class="header-container">
    <div class="header-left">
      <img src="' . $systemLogo . '" alt="BDO Logo">
    </div>
    <div class="header-right">
      <h1>Whistleblower Case Management System</h1>
      <h2>Case #: ' . htmlspecialchars($case['casenumber'] ?? '') . '</h2>
      <h2 class="sensitivity">Sensitivity: ' . htmlspecialchars($case['Case_sensitivity'] ?? '') . '</h2>
    </div>
  </div>
</header>

<!-- FOOTER -->
<footer>
  Page <span class="pagenum"></span> | &copy; ' . date('Y') . ' BDO East Africa (Rwanda) Ltd.
</footer>

<!-- MAIN CONTENT -->
<main>
  <section>
    <label>Status</label>
    <div class="fieldstatus">' . htmlspecialchars($case['status'] ?? '') . '</div>

    <label>Relevance</label>
    <div class="fieldstatus">' . htmlspecialchars($case['Case_relevance'] ?? '') . '</div>

    <label>Submitted At</label>
    <div class="field">' . htmlspecialchars($case['submitted_at'] ?? '') . '</div>

    <label>Last Updated By</label>
    <div class="field">' . htmlspecialchars($case['updated_by'] ?? '') . ' on ' . htmlspecialchars($case['last_updated'] ?? '') . '</div>
  </section>

  <section>
    <h3>Reporter Information</h3>

    <label>Concerned Client</label>
    <div class="field">' . htmlspecialchars($case['client_name'] ?? '') . '</div>

    <label>Affiliation</label>
    <div class="field">' . htmlspecialchars($case['affiliation'] ?? '') . '</div>

    <label>Anonymity</label>
    <div class="field">' . htmlspecialchars($case['identity_choice'] ?? '') . '</div>';

if ($showIdentity) {
  $html .= '
    <label>Full Name</label>
    <div class="field">' . htmlspecialchars($case['full_name'] ?? '') . '</div>
    <label>Email</label>
    <div class="field">' . htmlspecialchars($case['email'] ?? '') . '</div>

    <label>Telephone</label>
    <div class="field">' . htmlspecialchars($case['phone'] ?? '') . '</div>';
}

$html .= '
  </section>

  <section>
    <h3>Incident Details</h3>

    <label>When did the incident(s) take place?</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_when'] ?? '')) . '</div>

    <label>Where did the incident(s) take place?</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_where'] ?? '')) . '</div>

    <label>Which department/site does it concern?</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_division'] ?? '')) . '</div>

    <label>Description of the incident(s)</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_description'] ?? '')) . '</div>

    <label>Feedback (Applicable if Closed)</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($closureComment)) . '</div>
  </section>
</main>

</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
ob_end_clean();
$pdfOutput = $dompdf->output();

// Check if this is an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfOutput));
    header('Content-Disposition: inline; filename="Case_' . $case['casenumber'] . '.pdf"');
    echo $pdfOutput;
} else {
    $dompdf->stream('Case_' . $case['casenumber'] . '.pdf', ["Attachment" => false]);
}
exit;
