<?php
// Include Composer's autoloader (adjust path if needed based on your directory structure)
require 'utils/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


try {
   
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'alert.rw@bdo-ea.com';
    $mail->Password   = 'Bdo@2023!'; // Replace with your 16-character App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port       = 587;

     // Server settings
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->Debugoutput = function($str, $level) {
        echo "<div style='font-family: monospace; font-size: 12px; border-bottom: 1px solid #eee; padding: 2px;'>[SMTP Debug] " . htmlspecialchars($str) . "</div>";
    };

    // Recipients
    $mail->setFrom('alert.rw@bdo-ea.com', 'BDO Test Script');
    $mail->addAddress('francois.habuwiteka@bdo-ea.com'); // Your recipient address

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Independent Test - BDO Portal';
    $mail->Body    = '<b>Success!</b> If you are reading this email, Office 365 SMTP authentication and TLS connection are fully working.';

    $mail->send();
    echo "<h3 style='color: green;'>Email sent successfully! Authentication and connection are working.</h3>";
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</h3>";
}