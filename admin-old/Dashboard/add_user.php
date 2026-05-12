<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');
require '../../config/db.php';

require '../../utils/vendor/autoload.php'; 


if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ------------------ FUNCTIONS ------------------

function generateRandomPassword($length = 10) {
    $bytes = random_bytes($length);
    return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
}

function generateAdminId($pdo) {
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM admin_users WHERE id LIKE ?");
    $like = "ADM$year%";
    $stmt->execute([$like]);
    $count = $stmt->fetch()['total'] + 1;
    return 'ADM' . $year . str_pad($count, 3, '0', STR_PAD_LEFT);
}

function sendNewAdminEmail($email, $name, $rawPassword, $adminId) {
    $mail = new PHPMailer(true);
    try {
        // ---------- MAIL SERVER CONFIG ----------
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // change if needed
        $mail->SMTPAuth = true;
        $mail->Username = 'alert.rw@bdo-ea.com'; // replace with your email
        $mail->Password = 'Bdo@2023!'; // use App Password or env variable
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // ---------- RECIPIENTS ----------
        $mail->setFrom('alert.rw@bdo-ea.com', 'BDO Whistleblower Management System');
        $mail->addAddress($email, $name);

        // ---------- EMAIL CONTENT ----------
        $mail->isHTML(true);
        $mail->Subject = 'Your Admin Account Has Been Created';

        $mail->Body = '
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
                    <p style="margin: 5px 0;"><strong>Password:</strong> ' . htmlspecialchars($rawPassword) . '</p>
                </div>

                <p>You can access the admin portal using the link below:</p>
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
        </div>';

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// ------------------ MAIN LOGIC ------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $userType = trim($_POST['user_type'] ?? '');

    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'error' => 'Name and email are required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }

    $rawPassword = generateRandomPassword();
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
    $adminId = generateAdminId($pdo);

    $stmt = $pdo->prepare("INSERT INTO admin_users (id, name, email, Telephone, user_type, password_hash) VALUES (?, ?, ?, ?, ?, ?)");

    try {
        $stmt->execute([$adminId, $name, $email, $telephone, $userType, $hashedPassword]);

        // Send the email
        $emailSent = sendNewAdminEmail($email, $name, $rawPassword, $adminId);

        echo json_encode([
            'success' => true,
            'password' => $rawPassword,
            'id' => $adminId,
            'email_sent' => $emailSent
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'error' => 'Email already exists.']);
        } else {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>
