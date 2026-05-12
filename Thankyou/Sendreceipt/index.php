<?php
require '../../config/db.php';
// require_once '../../admin/Dashboard//dompdf/autoload.inc.php';
require '../../utils/vendor/autoload.php'; // Adjust this if using Composer elsewhere

// load translations
require '../../languages/index.php';

// Get language from URL, session, or default to English
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';

// Save it in session so it persists
$_SESSION['lang'] = $lang;

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get case number
$casenumber = $_POST['casenumber'] ?? '';
if (!$casenumber) die("No case number.");

// Fetch case
$stmt = $pdo->prepare("SELECT cases.*, clients.name AS client_name 
                       FROM cases 
                       JOIN clients ON cases.client_token = clients.token 
                       WHERE cases.casenumber = ?");
$stmt->execute([$casenumber]);
$case = $stmt->fetch();
if (!$case) die("Case not found.");

// Prepare variables
$identity = strtolower($case['identity_choice']);
$showIdentity = in_array($identity, ['identifiable', 'identifiable to bdo only', 'identified']);
$systemLogo = 'https://cdn.bdo.global/images/bdo_logo/1.0.0/bdo_logo_color.png';
$clientLogo = 'https://images.africanfinancials.com/rw-bok-logo-min.png';

// Build HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Case Report ' . htmlspecialchars($case['casenumber']) . '</title>
<style>
  body { font-family: "Trebuchet MS", sans-serif; color: #333; margin: 30px; }
  header { text-align: center; margin-bottom: 30px; }
  .logo-row { display: flex; justify-content: space-between; align-items: center; }
  .logo-row img { height: 60px; max-width: 45%; }
  h1 { color: #ED1A3B; margin: 15px 0 0; }
  h2 { margin: 0; font-weight: normal; color: #555; font-size: 16px; }
  section { margin-bottom: 25px; }
  label { display: block; font-weight: bold; margin: 10px 0 5px; font-size: 13px; }
  .field { background: #f7f7f7; border: 1px solid #ccc; border-radius: 4px; padding: 8px; font-size: 12px; }
  textarea.field { white-space: pre-wrap; }
  .files div { margin: 5px 0; font-size: 13px; }
  .status-box { background: #00A86B; color: white; font-weight: bold; padding: 8px; border-radius: 5px; text-align: center; }
  footer { text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ccc; padding-top: 12px; margin-top: 40px; }
</style>
</head>
<body>

<header>
  <div class="logo-row">
    <img src="' . $systemLogo . '" alt="System Logo" />
  </div>
  <h1>Whistleblower Case Management System</h1>
  <h2>'. $lang_data[$lang]['casenumbertext'] . ' #: ' . htmlspecialchars($case['casenumber']) . '</h2>
</header>

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

<footer>
  &copy; ' . date('Y') . ' Whistleblower Solution - BDO East Africa (Rwanda) Ltd. All rights reserved.
</footer>

</body>
</html>
';

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

// Email sending
$user_email =$_POST['receipt_email'] ?? '';

$case_token = $case['casenumber'];

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';        // Outlook SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'alert.rw@bdo-ea.com'; // Your Outlook email
    $mail->Password   = 'Bdo@2023!';         // App password or real password (if allowed)
    $mail->SMTPSecure = 'tls';                       // Encryption
    $mail->Port       = 587;                         // TLS port

    $mail->setFrom('alert.rw@bdo-ea.com', 'BDO Whistleblowing Platform');
    $mail->addAddress($user_email);

    $mail->isHTML(true);
    $mail->Subject = $lang_data[$lang]['email_title'] . " - Ref #$case_token";
$mail->Body = '
<div style="font-family: Trebuchet MS, sans-serif; padding: 20px; color: #333; max-width: 600px; margin: auto;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="' . $systemLogo . '" alt="BDO Logo" style="max-width: 200px; height: auto;">
    </div>
    
        <h2 style="color: #ED1A3B;">'. $lang_data[$lang]['thankyouforreport'] . '</h2>
    <p>'. $lang_data[$lang]['Dear'] . ' ' . htmlspecialchars($case['full_name']) . ',</p>

    <p>'. $lang_data[$lang]['thankyoumessagesuccess'] . '</p>
    <h3 style="color: #ED1A3B;">' . htmlspecialchars($case_token) . '</h3>

    <p>'. $lang_data[$lang]['email_pfa_assistance'] . '</p>
    <br/>
    <p>'. $lang_data[$lang]['email_best_regards'] . ',<br/><strong>BDO Whistleblowing Support Team</strong></p>

    <hr style="margin-top: 30px;">
    <small style="color: #999;">This is an automated message. Please do not reply.</small>
</div>';

    $mail->AltBody = "Thank you for your report. Case Ref: $case_token. See attached PDF.";

    $mail->addAttachment($tempPdfPath, "case_$case_token.pdf");
  if ($mail->send()) {
    echo "
    <div style='font-family: Arial, sans-serif; padding: 15px; background-color: #ED1A3B; color: #FFFFFF; border: 1px solid #c3e6cb; border-radius: 5px;'>
      ✅ <strong></strong> " . $lang_data[$lang]['receiptsuccessmessage'] . " <strong>" . htmlspecialchars($user_email) . "</strong>.
      </div>
    <script>
        setTimeout(function() {
            window.location.href = '../../';
        }, 5000);
    </script>
    ";}
     else {
    echo "<div style='font-family: Arial, sans-serif; padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
            ❌ <strong>Error:</strong> Failed to send receipt. Mailer Error: {$mail->ErrorInfo}
          </div>";
}


    // Delete temp file
    unlink($tempPdfPath);
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
