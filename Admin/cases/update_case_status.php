<?php
header('Content-Type: text/html; charset=utf-8');
require '../auth/auth_check.php';
require '../../config/db.php';
require '../../languages/index.php'; // Contains $lang_data array

require '../../utils/vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$caseId          = $_POST['case_id'] ?? '';
$newStatus       = $_POST['status'] ?? '';
$sensitivity     = $_POST['sensitivity'] ?? '';
$relevance       = $_POST['relevance'] ?? '';
$closureComment  = trim($_POST['closure_comment'] ?? '');
$changedBy       = $_SESSION['admin_name'] ?? 'System Admin';

if (!$caseId) {
    $_SESSION['error'] = 'Invalid case identifier.';
    header("Location: index.php");
    exit;
}

/* Fetch full case details and client information */
$stmt = $pdo->prepare("
    SELECT cases.*, clients.name AS client_name 
    FROM cases 
    LEFT JOIN clients ON cases.client_token = clients.token 
    WHERE cases.casenumber = ?
");
$stmt->execute([$caseId]);
$case = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$case) {
    $_SESSION['error'] = 'Case not found.';
    header("Location: index.php");
    exit;
}

$currentStatus       = $case['status'];
$sentToPartner       = $case['sent_to_partner'] ?? null;
$incidentDescription = $case['incident_description'] ?? '';
$lang                = strtolower($case['language'] ?? 'en');

// Fallback language check
if (!isset($lang_data[$lang])) {
    $lang = 'en';
}

/* ✅ ENFORCE CLOSURE COMMENT */
if ($newStatus === 'Closed' && $currentStatus !== 'Closed') {
    if ($closureComment === '') {
        $_SESSION['error'] = 'A closure comment is required when closing a case.';
        header("Location: view.php?casenumber=$caseId");
        exit;
    }
}

$markAsSent = false;

/* ✅ Check if relevance is Core Banking Operation Issue and status chosen is Closed */
if ($relevance === 'Core Banking Operation Issue' && $newStatus === 'Closed') {
    if ($sentToPartner !== 'yes') {
        
        // ------------------ 1. GENERATE PDF ATTACHMENT ------------------
        $systemLogo = 'https://cdn.bdo.global/images/bdo_logo/1.0.0/bdo_logo_color.png';
        $identity = strtolower(trim($case['identity_choice'] ?? ''));
        $showIdentity = in_array($identity, ['identifiable', 'identifiable to bdo only', 'identified']);

        $pdfHtml = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Case Report ' . htmlspecialchars($case['casenumber']) . '</title>
        <style>
          @page { margin-top: 120px; margin-bottom: 60px; margin-left: 40px; margin-right: 40px; }
          body { font-family: "DejaVu Sans", "Trebuchet MS", sans-serif; color: #333; font-size: 12px; line-height: 1.5; margin: 0; }
          header { position: fixed; top: -100px; left: 0; right: 0; height: 90px; padding: 10px 0; }
          .header-container { width: 100%; display: table; }
          .header-left { display: table-cell; width: 25%; vertical-align: middle; text-align: left; padding-left: 20px; }
          .header-left img { height: 45px; }
          .header-right { display: table-cell; width: 75%; text-align: right; vertical-align: middle; padding-right: 20px; }
          .header-right h1 { color: #E81A3B; margin: 0; font-size: 16px; }
          .header-right h2 { margin: 2px 0 0; font-size: 12px; color: #555; }
          footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 40px; border-top: 1px solid #ccc; text-align: center; font-size: 10px; color: #777; padding-top: 5px; }
          main { margin-top: 0; }
          section { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px dashed #ddd; }
          h3 { color: #E81A3B; border-left: 4px solid #E81A3B; padding-left: 8px; margin-bottom: 10px; font-size: 14px; }
          label { display: block; font-weight: bold; margin: 8px 0 4px; font-size: 12px; color: #444; }
          .field { background: #fafafa; border: 1px solid #ddd; border-radius: 4px; padding: 8px 10px; font-size: 12px; }
          .textarea.field { white-space: pre-wrap; }
          .pagenum:before { content: counter(page); }
        </style>
        </head>
        <body>
        <header>
          <div class="header-container">
            <div class="header-left"><img src="' . $systemLogo . '" alt="BDO Logo"></div>
            <div class="header-right">
              <h1>' . ($lang_data[$lang]['export_system_name'] ?? 'BDO Whistleblower System') . '</h1>
              <h2>' . ($lang_data[$lang]['casenumbertext'] ?? 'Case') . ' #: ' . htmlspecialchars($case['casenumber']) . '</h2>
            </div>
          </div>
        </header>
        <footer>
          Page <span class="pagenum"></span> | &copy; ' . date('Y') . ' BDO East Africa (Rwanda) Ltd.
        </footer>
        <main>
          <section>
            <label>' . ($lang_data[$lang]['export_submitted_at'] ?? 'Submitted At') . '</label>
            <div class="field">' . htmlspecialchars($case['submitted_at'] ?? date('Y-m-d H:i:s')) . '</div>
          </section>
          <section>
            <h3>' . ($lang_data[$lang]['export_reporter_information'] ?? 'Reporter Information') . '</h3>
            <label>' . ($lang_data[$lang]['export_concerned_client'] ?? 'Concerned Entity') . '</label>
            <div class="field">' . htmlspecialchars($case['client_name'] ?? 'N/A') . '</div>
            <label>' . ($lang_data[$lang]['export_affiliation'] ?? 'Affiliation') . '</label>
            <div class="field">' . htmlspecialchars($case['affiliation'] ?? 'Other') . '</div>
            <label>' . ($lang_data[$lang]['export_anonymity'] ?? 'Anonymity') . '</label>
            <div class="field">' . htmlspecialchars($case['identity_choice'] ?? 'Anonymous') . '</div>';

        if ($showIdentity) {
            $pdfHtml .= '
            <label>' . ($lang_data[$lang]['fullname'] ?? 'Full Name') . '</label>
            <div class="field">' . htmlspecialchars($case['full_name'] ?? 'N/A') . '</div>
            <label>' . ($lang_data[$lang]['email'] ?? 'Email') . '</label>
            <div class="field">' . htmlspecialchars($case['email'] ?? 'N/A') . '</div>
            <label>' . ($lang_data[$lang]['telephone'] ?? 'Phone') . '</label>
            <div class="field">' . htmlspecialchars($case['phone'] ?? 'N/A') . '</div>';
        }

        $pdfHtml .= '
          </section>
          <section>
            <h3>' . ($lang_data[$lang]['export_incident_details'] ?? 'Incident Details') . '</h3>
            <label>' . ($lang_data[$lang]['when'] ?? 'When') . '</label>
            <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_when'] ?? 'N/A')) . '</div>
            <label>' . ($lang_data[$lang]['where'] ?? 'Where') . '</label>
            <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_where'] ?? 'N/A')) . '</div>
            <label>' . ($lang_data[$lang]['division'] ?? 'Division') . '</label>
            <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_division'] ?? 'N/A')) . '</div>
            <label>' . ($lang_data[$lang]['indetails'] ?? 'Details') . '</label>
            <div class="field textarea">' . nl2br(htmlspecialchars($case['incident_description'] ?? 'N/A')) . '</div>
          </section>';

        if (!empty($closureComment)) {
            $pdfHtml .= '
          <section>
            <h3>Closure Resolution Notes</h3>
            <div class="field textarea">' . nl2br(htmlspecialchars($closureComment)) . '</div>
          </section>';
        }

        $pdfHtml .= '
        </main>
        </body>
        </html>';

        // Render PDF using Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $tempPdfPath = sys_get_temp_dir() . "/case_" . $caseId . ".pdf";
        file_put_contents($tempPdfPath, $dompdf->output());

        // ------------------ 2. BUILD ERP PARSER BODY & HEADERS ------------------
        $partnerEmail = "bk@bk.rw"; // Replace with your actual partner/ERP address
        $subject = "BDO WHISTLEBLOWING SYSTEM - CORE BANKING OPERATION ISSUE - ESCALATION";

        $boundary = md5(time());
        $headers  = "From: BDO WHISTLEBLOWING SYSTEM - BANK OF KIGALI PLC <alert.rw@bdo-ea.com>\r\n";
        $headers .= "Reply-To: alert.rw@bdo-ea.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        $htmlEmail = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "DejaVu Sans", "Trebuchet MS", Arial, sans-serif; background-color: #FFFFFF; margin: 0; padding: 20px; color: #333333; }
        .email-container { max-width: 650px; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-top: 5px solid #E81A3B; margin: 0 auto; }
        .email-header { background: #1a1a1a; color: #ffffff; padding: 25px 20px; text-align: center; }
        .email-body { padding: 30px; }
        .erp-block { background: #FFFFFF; color: #FFFFFF; font-family: "DejaVu Sans", "Trebuchet MS", Arial, sans-serif; padding: 15px 18px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 25px; line-height: 1.6; border: 1px solid #e1e4e8; border-left: 4px solid #E81A3B; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .meta-table td { padding: 9px 12px; border-bottom: 1px solid #edf2f7; font-size: 0.9rem; }
        .meta-table td.label { font-weight: 600; color: #008fd2; width: 35%; }
        .section-title { font-size: 0.95rem; color: #E81A3B; border-bottom: 2px solid #edf2f7; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .content-box { background: #FFFFFF; padding: 16px; border-radius: 6px; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap; border: 1px solid #e1e4e8; color: #FFFFFF; }
        .email-footer { background: #333333; padding: 18px; text-align: center; font-size: 0.8rem; color: #FFFFFF; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="https://cdn.bdo.global/images/bdo_logo/1.0.0/bdo_logo_color.png" alt="BDO Logo" style="height: 42px; display: block; margin: 0 auto 12px auto; border: 0;" />
            <h2 style="margin: 0; font-size: 1.2rem; color: #ffffff; font-weight: 600;">BDO WHISTLEBLOWING SYSTEM - BANK OF KIGALI PLC</h2>
            <span style="color: #E81A3B; font-size: 0.75rem; font-weight: bold; display: block; margin-top: 4px; letter-spacing: 0.8px; text-transform: uppercase;">Automated Case Escalation</span>
        </div>

        <div class="email-body">
            <div class="erp-block">
                <strong>SOURCE</strong> &nbsp;&nbsp;&nbsp;: BDO WHISTLEBLOWING SYSTEM - BANK OF KIGALI PLC <br>
                <strong>EXTERNAL REF NO</strong> : ' . htmlspecialchars($caseId) . '<br>
                <strong>CASE ANONYMITY</strong> &nbsp;&nbsp;&nbsp;: ' . htmlspecialchars($case['identity_choice'] ?? 'Anonymous') . '<br>
                <strong>ISSUE CATEGORY</strong> &nbsp;&nbsp;&nbsp;: Core Banking Operation Issue<br>
                <strong>LANGUAGE</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' . strtoupper(htmlspecialchars($lang)) . '<br>
                <strong>DATE SUBMITTED</strong> &nbsp;&nbsp;&nbsp;: ' . $case['submitted_at'] . ' UTC<br>
            </div>

            <div class="section-title">Incident Context</div>
            <table class="meta-table">
                <tr><td class="label">' .htmlspecialchars($lang_data[$lang]['when'] ?? 'When:', ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($case['incident_when'] ?? 'N/A') . '</td></tr>
                <tr><td class="label">' . htmlspecialchars($lang_data[$lang]['where'] ?? 'Location (Where):', ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($case['incident_where'] ?? 'N/A') . '</td></tr>
                <tr><td class="label">' . htmlspecialchars($lang_data[$lang]['division'] ?? 'Division:', ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($case['incident_division'] ?? 'N/A') . '</td></tr>
            </table>

            <div class="section-title">Detailed Summary</div>
            <div class="content-box">' . htmlspecialchars($incidentDescription) . '</div>';

    if (!empty($closureComment)) {
        $htmlEmail .= '
            <div class="section-title">Closure Resolution Notes</div>
            <div class="content-box" style="border-left: 4px solid #98002e;">' . htmlspecialchars($closureComment) . '</div>';
    }

    $htmlEmail .= '
        </div>
        <div class="email-footer">
            Automated Notice generated upon case closure. PDF summary and uploaded evidence(if any) files are attached.
        </div>
    </div>
</body>
</html>';
        // Multipart MIME assembly
        $emailPayload = "--{$boundary}\r\n";
        $emailPayload .= "Content-Type: text/html; charset=UTF-8\r\n";
        $emailPayload .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $emailPayload .= $htmlEmail . "\r\n\r\n";

        // ------------------ 3. ATTACH DYNAMICALLY GENERATED PDF ------------------
        if (file_exists($tempPdfPath)) {
            $pdfData = chunk_split(base64_encode(file_get_contents($tempPdfPath)));
            $emailPayload .= "--{$boundary}\r\n";
            $emailPayload .= "Content-Type: application/pdf; name=\"Case_{$caseId}.pdf\"\r\n";
            $emailPayload .= "Content-Transfer-Encoding: base64\r\n";
            $emailPayload .= "Content-Disposition: attachment; filename=\"Case_{$caseId}.pdf\"\r\n\r\n";
            $emailPayload .= $pdfData . "\r\n\r\n";
        }

        // ------------------ 4. ATTACH UPLOADED EVIDENCE FILES ------------------
        $rawFiles  = $case['files'] ?? '';
        $filesList = [];
        if ($rawFiles) {
            $decoded = json_decode($rawFiles, true);
            if (is_array($decoded)) {
                $filesList = $decoded;
            } else {
                $filesList = array_filter(explode(',', $rawFiles));
            }
        }

        foreach ($filesList as $file) {
            $filePath = "../../Submit/Uploads/" . trim($file);
            if (file_exists($filePath)) {
                $fileName = basename($filePath);
                $fileData = chunk_split(base64_encode(file_get_contents($filePath)));
                $fileType = mime_content_type($filePath) ?: 'application/octet-stream';

                $emailPayload .= "--{$boundary}\r\n";
                $emailPayload .= "Content-Type: {$fileType}; name=\"{$fileName}\"\r\n";
                $emailPayload .= "Content-Transfer-Encoding: base64\r\n";
                $emailPayload .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
                $emailPayload .= $fileData . "\r\n\r\n";
            }
            if (!file_exists($filePath)) {
    error_log("Attachment missing: " . $filePath);
}
        }

        $emailPayload .= "--{$boundary}--";

        // Dispatch Email
        $mail = new PHPMailer(true);
        // UTF-8
        $mail->CharSet = 'UTF-8';
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.office365.com';                  
            $mail->SMTPAuth   = true;
            $mail->Username   = 'alert.rw@bdo-ea.com';                 
            $mail->Password   = 'Bdo@2023!';     
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption        
            $mail->Port       = 587;

            $mail->SMTPDebug  = 2; // Enables full SMTP output logging
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP Debug: $str"); // Logs to your PHP error log
            };

            // Sender and Recipient
            $mail->setFrom('alert.rw@bdo-ea.com', 'BDO WHISTLEBLOWING SYSTEM - BANK OF KIGALI PLC');
            $mail->addAddress($partnerEmail);

            // Email Subject & HTML Body
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlEmail;

            // Attach Generated PDF Report
            if (file_exists($tempPdfPath)) {
                $mail->addAttachment($tempPdfPath, "Case_{$caseId}.pdf");
            }

            // Attach Uploaded Evidence Files
            foreach ($filesList as $file) {
                $filePath = "../../Submit/Uploads/" . trim($file);
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath, basename($filePath));
                }
            }

            $mail->send();
            $mailSent = true;

        } catch (Exception $e) {
            $mailSent = false;
            error_log("Partner Email Error: " . $mail->ErrorInfo);
        }

        // Delete temp PDF
        if (file_exists($tempPdfPath)) {
            unlink($tempPdfPath);
        }

        if (!$mailSent) {
            // 🛑 SAFETY SHIELD: Abort execution if mail failed
            $_SESSION['error'] = 'Failed to send email notification and PDF attachment to the partner. Case update has been aborted for safety.';
            header("Location: view.php?casenumber=$caseId");
            exit;
        } else {
            $markAsSent = true;
        }
        // Delete temp PDF
        if (file_exists($tempPdfPath)) {
            unlink($tempPdfPath);
        }

        if (!$mailSent) {
            // 🛑 SAFETY SHIELD: Abort execution if mail failed[cite: 3]
            $_SESSION['error'] = 'Failed to send email notification and PDF attachment to the partner. Case update has been aborted for safety.';
            header("Location: view.php?casenumber=$caseId");
            exit;
        } else {
            $markAsSent = true;
        }
    }
}

try {
    // Start Transaction to guarantee data integrity
    $pdo->beginTransaction();

    /* Update case status, relevance, sensitivity, and optionally mark sent_to_partner as 'yes' */
    if ($markAsSent) {
        $pdo->prepare("UPDATE cases SET status=?, Case_sensitivity=?, Case_relevance=?, sent_to_partner='yes' WHERE casenumber=?")
            ->execute([$newStatus, $sensitivity, $relevance, $caseId]);
    } else {
        $pdo->prepare("UPDATE cases SET status=?, Case_sensitivity=?, Case_relevance=? WHERE casenumber=?")
            ->execute([$newStatus, $sensitivity, $relevance, $caseId]);
    }

    /* Record status audit */
    $pdo->prepare("
        INSERT INTO case_status_audit
        (case_id, old_status, new_status, changed_by)
        VALUES (?, ?, ?, ?)
    ")->execute([$caseId, $currentStatus, $newStatus, $changedBy]);

    if ($closureComment !== '') {
        $pdo->prepare("
            INSERT INTO case_comments
            (case_id, comment, comment_type, created_by)
            VALUES (?, ?, 'internal', ?)
        ")->execute([$caseId, $closureComment, $changedBy]);
    }

    // Commit changes
    $pdo->commit();

    /* Set success notification */
    if ($currentStatus !== $newStatus) {
        if ($newStatus === 'Closed') {
            if ($markAsSent) {
                $_SESSION['success'] = 'Case closed successfully. PDF report and attachments sent to the technical partner and marked as sent!';
            } else {
                $_SESSION['success'] = 'Case closed successfully.';
            }
        } else {
            $_SESSION['success'] = 'Case details updated successfully.';
        }
    } else {
        if ($markAsSent) {
            $_SESSION['success'] = 'Case saved. PDF report and attachments sent to the technical partner and marked as sent!';
        } else {
            $_SESSION['success'] = 'Case saved (no status change).';
        }
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Database error occurred: ' . $e->getMessage();
}

header("Location: view.php?casenumber=$caseId");
exit;