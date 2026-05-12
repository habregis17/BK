<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../utils/vendor/autoload.php';



function sendUserCredentialsSMTP($name, $email, $password) {

    $mail = new PHPMailer(true);

    try {

        // /* DEBUG MODE */
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';

        /* SMTP CONFIG */
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // change if needed
        $mail->SMTPAuth = true;
        $mail->Username = 'alert.rw@bdo-ea.com'; // replace with your email
        $mail->Password = 'Bdo@2023!'; // use App Password or env variable
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        /* SENDER */
        $mail->setFrom('alert.rw@bdo-ea.com', 'BDO Whistleblower System');

        /* RECIPIENT */
        $mail->addAddress($email, $name);

        /* EMAIL FORMAT */
        $mail->isHTML(true);
        $mail->Subject = 'Your System Access Credentials';

        /* ✅ BRANDED TEMPLATE */
        $mail->Body = getEmailTemplate($name, $email, $password);

        $mail->send();
        return true;

    } 
    catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function getEmailTemplate($name, $email, $password) {

return '
        
        <div style="font-family: Trebuchet MS, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
            <div style="background-color: #FFFFFF; color: white; padding: 15px; text-align: center;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9e/BDO_Deutsche_Warentreuhand_Logo.svg" alt="BDO Logo" style="max-height: 50px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 20px;">BDO Whistleblower Management System</h2>
            </div>

            <div style="padding: 25px; color: #333;">
                <p>Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
                <p>Your admin account has been successfully created in the BDO Whistleblower Management System.</p>

                <div style="background-color: #f5f5f5; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>Username:</strong> ' . htmlspecialchars($email) . '</p>
                    <p style="margin: 5px 0;"><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>
                </div>

                <p>You can access the admin portal using the credentials above and reset your password to your liking</p>
                <p style="text-align: center;">
                    <a href="https://bdowb.rw/BK/admin" 
                       style="background-color: #ED1A3B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                       Go to Admin Portal
                    </a>
                </p>

                <p>If you encounter any issues, please contact the system administrator.</p>

                <p style="margin-top: 25px;">Best regards,<br>
                <strong>BDO East Africa (Rwanda) Ltd</strong></p>
            </div>

            <div style="background-color: #f5f5f5; text-align: center; padding: 10px; font-size: 12px; color: #777;">
                © ' . date('Y') . ' BDO East Africa (Rwanda) Ltd. All rights reserved.
            </div>
        </div>
';
}

// Resend credentials email settings
function ResendUserCredentialsSMTPsendUserCredentialsSMTP($name, $email, $password) {

    $mail = new PHPMailer(true);

    try {

        // /* DEBUG MODE */
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';

        /* SMTP CONFIG */
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // change if needed
        $mail->SMTPAuth = true;
        $mail->Username = 'alert.rw@bdo-ea.com'; // replace with your email
        $mail->Password = 'Bdo@2023!'; // use App Password or env variable
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        /* SENDER */
        $mail->setFrom('alert.rw@bdo-ea.com', 'BDO Whistleblower System');

        /* RECIPIENT */
        $mail->addAddress($email, $name);

        /* EMAIL FORMAT */
        $mail->isHTML(true);
        $mail->Subject = 'Your System Access Credentials - Password Reset';

        /* ✅ BRANDED TEMPLATE */
        $mail->Body = resendgetEmailTemplate($name, $email, $password);

        $mail->send();
        return true;

    } 
    catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function resendgetEmailTemplate($name, $email, $password) {

return '
        
        <div style="font-family: Trebuchet MS, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
            <div style="background-color: #FFFFFF; color: white; padding: 15px; text-align: center;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9e/BDO_Deutsche_Warentreuhand_Logo.svg" alt="BDO Logo" style="max-height: 50px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 20px;">BDO Whistleblower Management System</h2>
            </div>

            <div style="padding: 25px; color: #333;">
                <p>Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
                <p>Your admin account has been successfully reset in the BDO Whistleblower Management System.</p>

                <div style="background-color: #f5f5f5; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>Username:</strong> ' . htmlspecialchars($email) . '</p>
                    <p style="margin: 5px 0;"><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>
                </div>

                <p>You can access the admin portal using the credentials above and reset your password to your liking:</p>
                <p style="text-align: center;">
                    <a href="https://bdowb.rw/BK/admin" 
                       style="background-color: #ED1A3B; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                       Go to Admin Portal
                    </a>
                </p>

                <p>If you encounter any issues, please contact the system administrator.</p>

                <p style="margin-top: 25px;">Best regards,<br>
                <strong>BDO East Africa (Rwanda) Ltd</strong></p>
            </div>

            <div style="background-color: #f5f5f5; text-align: center; padding: 10px; font-size: 12px; color: #777;">
                © ' . date('Y') . ' BDO East Africa (Rwanda) Ltd. All rights reserved.
            </div>
        </div>
';
}

?>
