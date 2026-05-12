<?php
// echo "It is reaching here";

require '../config/db.php';
// echo "It is reaching here2";

require '../utils/casenumber.php';
// echo "It is reaching here3";

// require '../admin/Dashboard/dompdf/autoload.inc.php';

// echo "It is reaching here4";


require '../utils/vendor/autoload.php'; 
require '../languages/index.php';


use Dompdf\Dompdf;
use Dompdf\Options;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for dev (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Optional: error log file path
// $logFile = __DIR__ . '/../logs/error_log.txt';
// function error_log($message) {
//     global $logFile;
//     error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, $logFile);
// }
// Get language from URL, session, or default to English
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';

// Save it in session so it persists
$_SESSION['lang'] = $lang;

$token = $_POST['token'] ?? '';
if (!$token) {
    die("Token missing.");
}

// Form inputs
$client_token         = $token;
$affiliation          = $_POST['affiliation'] ?? '';
$identity_choice      = $_POST['identity_choice'] ?? '';
$full_name            = $_POST['fullname'] ?? null;
$email                = $_POST['contact_email'] ?? null;
$phone                = $_POST['phone'] ?? null;
$department           = $_POST['department'] ?? null;
$incident_description = $_POST['incident_description'] ?? '';
$incident_when        = $_POST['incident_when'] ?? null;
$incident_where       = $_POST['incident_where'] ?? null;
$incident_division    = $_POST['incident_division'] ?? null;
$case_manager    = "alert.rw@bdo-ea.com";

// Generate case number
try {
    $case_number = generateCaseNumber($pdo, $client_token);
    if (!$case_number) throw new Exception("Failed to generate case number.");
} catch (Exception $e) {
    error_log("Case Number Error: " . $e->getMessage());
    die("Error generating case number.");
}

// Handle file uploads

$uploaded_files = [];
$target_dir = "Uploads/"; // Make sure this directory exists and is writable

try {
    // Check if the file input is present
    if (isset($_FILES['incident_evidence'])) {
        $files = $_FILES['incident_evidence'];
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

        // Normalize to array
        $names = is_array($files['name']) ? $files['name'] : [$files['name']];
        $tmp_names = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
        $errors = is_array($files['error']) ? $files['error'] : [$files['error']];

        foreach ($names as $index => $filename) {
            $tmp_name = $tmp_names[$index];
            $error = $errors[$index];

            error_log("Processing file: $filename | Temp: $tmp_name | Error code: $error");

            if ($error !== UPLOAD_ERR_OK) {
                error_log("File upload error code: $error for file: $filename");
                continue;
            }

            if (!is_uploaded_file($tmp_name)) {
                error_log("Temp file is not a valid uploaded file: $tmp_name");
                continue;
            }

            $mime = mime_content_type($tmp_name);
            error_log("MIME type for $filename: $mime");

            if (!in_array($mime, $allowedTypes)) {
                error_log("Rejected file due to MIME type: $mime");
                continue;
            }

            $uniqueName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($filename));
            $filepath = $target_dir . $uniqueName;

            if (move_uploaded_file($tmp_name, $filepath)) {
                chmod($filepath, 0644);
                $uploaded_files[] = $filepath;
                error_log("Successfully uploaded file to: $filepath");
            } else {
                error_log("Failed to move uploaded file: $filename");
            }
        }
    } else {
        error_log("No file input 'incident_evidence' was received.");
    }
} catch (Exception $e) {
    error_log("Exception during upload: " . $e->getMessage());
}


$files_json = json_encode($uploaded_files);
error_log("Uploaded files JSON: $files_json");



//final file handler

// Save to DB
try {
    $stmt = $pdo->prepare("INSERT INTO cases (
        casenumber,
        client_token,
        affiliation,
        identity_choice,
        full_name,
        email,
        phone,
        department,
        incident_date,
        incident_description,
        files,
        incident_when,
        incident_where,
        incident_division,
        case_manager,
        language
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $success = $stmt->execute([
        $case_number,
        $client_token,
        $affiliation,
        $identity_choice,
        $full_name,
        $email,
        $phone,
        $department,
        null, // incident_date - fixed this too
        $incident_description,
        $files_json,
        $incident_when,
        $incident_where,
        $incident_division,
        $case_manager,
        $lang,
        
    ]);

} catch (PDOException $e) {
    echo "DB Insert Error: " . $e->getMessage();
    exit;
}


$casenumber = $case_number;
if (!$casenumber) {
    die("No case number.");
}

// Fetch full case data for email/pdf
try {
    $stmt = $pdo->prepare("SELECT cases.*, clients.name AS client_name 
                           FROM cases 
                           JOIN clients ON cases.client_token = clients.token 
                           WHERE cases.casenumber = ?");
    $stmt->execute([$casenumber]);
    $case = $stmt->fetch();
    if (!$case) throw new Exception("Case not found.");
} catch (Exception $e) {
    error_log("Fetch Case Error: " . $e->getMessage());
    die("Error fetching case details.");
}

// -------------- PDF GENERATION -------------
$systemLogo = 'https://cdn.bdo.global/images/bdo_logo/1.0.0/bdo_logo_color.png';
$identity = strtolower($case['identity_choice']);
$showIdentity = in_array($identity, ['identifiable', 'identifiable to bdo only', 'identified']);

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Case Report ' . htmlspecialchars($case['casenumber']) . '</title>
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
      <h1>'. $lang_data[$lang]['export_system_name'] . '</h1>
      <h2>'. $lang_data[$lang]['casenumbertext'] . ' #: ' . htmlspecialchars($case['casenumber']) . '</h2>
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

    <label>'. $lang_data[$lang]['export_submitted_at'] . '</label>
    <div class="field">' . htmlspecialchars($case['submitted_at']) . '</div>

  </section>

  <section>
    <h3>'. $lang_data[$lang]['export_reporter_information'] . '</h3>

    <label>'. $lang_data[$lang]['export_concerned_client'] . '</label>
    <div class="field">' . htmlspecialchars($case['client_name']) . '</div>

    <label>'. $lang_data[$lang]['export_affiliation'] . '</label>
    <div class="field">' . htmlspecialchars($case['affiliation']) . '</div>

    <label>'. $lang_data[$lang]['export_anonymity'] . '</label>
    <div class="field">' . htmlspecialchars($case['identity_choice']) . '</div>';

if ($showIdentity) {
  $html .= '
    <label>'. $lang_data[$lang]['fullname'] . '</label>
    <div class="field">' . htmlspecialchars($case['full_name']) . '</div>

    <label>'. $lang_data[$lang]['email'] . '</label>
    <div class="field">' . htmlspecialchars($case['email']) . '</div>

    <label>'. $lang_data[$lang]['telephone'] . '</label>
    <div class="field">' . htmlspecialchars($case['phone']) . '</div>';
}

$html .= '
  </section>

  <section>
    <h3>'. $lang_data[$lang]['export_incident_details'] . '</h3>

    <label>'. $lang_data[$lang]['when'] . '</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_when'])) . '</div>

    <label>'. $lang_data[$lang]['where'] . '</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_where'])) . '</div>

    <label>'. $lang_data[$lang]['division'] . '</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_division'])) . '</div>

    <label>'. $lang_data[$lang]['indetails'] . '</label>
    <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_description'])) . '</div>

    
  </section>
</main>

</body>
</html>';

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Save PDF temporarily
$tempPdfPath = sys_get_temp_dir() . "/case_$casenumber.pdf";
file_put_contents($tempPdfPath, $dompdf->output());

//--------------- End PDF Generation ----------

// ------------------ SEND EMAILS ------------------

// 1️⃣ Reporter email (if identifiable)
if ($showIdentity && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'alert.rw@bdo-ea.com';
        $mail->Password   = 'Bdo@2023!';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('alert.rw@bdo-ea.com', 'BK WHISTLEBLOWING PLATFORM');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $lang_data[$lang]['email_title'] . " - Ref #$casenumber";
        $mail->Body = '
<div style="font-family: Trebuchet MS, sans-serif; padding: 10px; color: #333; max-width: 600px; margin: auto;">
    <div style="text-align: center; margin-bottom: 10px;">
        <img src="' . $systemLogo . '" alt="BDO Logo" style="max-width: 200px; height: auto;">
    </div>
    <h2 style="color: #ED1A3B;">'. $lang_data[$lang]['thankyouforreport'] . '</h2>
    <p>'. $lang_data[$lang]['Dear'] . ' ' . htmlspecialchars($case['full_name']) . ',</p>
    <p>'. $lang_data[$lang]['thankyoumessagesuccess'] . '</p>
    <h3 style="color: #ED1A3B;">' . htmlspecialchars($casenumber) . '</h3>
    <p>'. $lang_data[$lang]['email_pfa_assistance'] . '</p>
    <br/>
    <p>'. $lang_data[$lang]['email_best_regards'] . ',<br/><strong>BDO Whistleblowing Support Team</strong></p>
    <hr style="margin-top: 30px;">
    <small style="color: #999;">This is an automated message. Please do not reply.</small>
</div>';
        $mail->AltBody = "Thank you for your report. Case Ref: $casenumber. See attached PDF.";
        $mail->addAttachment($tempPdfPath, "case_$casenumber.pdf");
        $mail->send();
    } catch (Exception $e) {
        error_log("Reporter Email/PDF Error: " . $e->getMessage());
    }
}

// 2️⃣ Reviewer email (always)
try {
    $reviewers = ['alert.rw@bdo-ea.com']; // Add more if needed
    foreach ($reviewers as $revEmail) {
        $mailReview = new PHPMailer(true);
        $mailReview->isSMTP();
        $mailReview->Host       = 'smtp.office365.com';
        $mailReview->SMTPAuth   = true;
        $mailReview->Username   = 'alert.rw@bdo-ea.com';
        $mailReview->Password   = 'Bdo@2023!';
        $mailReview->SMTPSecure = 'tls';
        $mailReview->Port       = 587;

        $mailReview->setFrom('alert.rw@bdo-ea.com', 'BDO Whistleblowing Platform');
        $mailReview->addAddress($revEmail);
        // $mailReview->addCC('arlette.umwari@bdo-ea.com', 'Arlette Umwari');


        $reporterInfo = ($identity_choice === 'Anonymous') ? 'Anonymous reporter' : htmlspecialchars($case['full_name']);
        $reviewLink = "https://bdowb.rw/BK/admin/Dashboard/Cases/?casenumber=" . urlencode($casenumber);

        $mailReview->isHTML(true);
        $mailReview->Subject = "New Case Submitted - Ref #$casenumber";
        $mailReview->Body = '
        
        <div style="font-family: Trebuchet MS, sans-serif; padding: 10px; color: #333; max-width: 600px; margin: auto;">
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="' . $systemLogo . '" alt="BDO Logo" style="max-width: 200px; height: auto;">
            </div>

            <h2 style="color: #ED1A3B;">New Case Submitted to BK Whistleblower System</h2>

            <p>Dear Reviewer,</p>

            <p>A new case has been submitted via the BK Whistleblower Reporting Platform. Below are the details:</p>

            <p><strong>Case Number:</strong> ' . htmlspecialchars($casenumber) . '</p>
            <p><strong>Reporter:</strong> ' . $reporterInfo . '</p>
            <p><strong>Incident Summary:</strong><br/>' . nl2br(htmlspecialchars($case['incident_description'])) . '</p>

            <p>
                <a href="' . $reviewLink . '" 
                   style="display: inline-block; background-color: #ED1A3B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;">
                   Review Case
                </a>
            </p>

            <br/>
            <p>Best regards,<br/><strong>BDO Whistleblowing Support Team</strong></p>

            <hr style="margin-top: 30px;">
            <small style="color: #999;">This is an automated notification. Please do not reply.</small>
        </div>';

        $mailReview->AltBody = "New case submitted. Case Ref: $casenumber. Reporter: $reporterInfo. Review at $reviewLink";
        $mailReview->addAttachment($tempPdfPath, "Case_$casenumber.pdf");
        $mailReview->send();
    }
}
 catch (Exception $e) {
    error_log("Reviewer Email/PDF Error: " . $e->getMessage());
}

// Delete temp PDF after all emails
if (file_exists($tempPdfPath)) unlink($tempPdfPath);

// Redirect to thank you page
header("Location: ../Thankyou/?casenumber=" . urlencode($casenumber) . "&lang=" . urlencode($lang));
exit();
