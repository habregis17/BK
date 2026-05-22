<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../../utils/casenumber.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
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

try {

    /* -----------------------------
       1. COLLECT INPUTS
    ------------------------------ */
    $client_token = $_POST['client_token'] ?? '';
    $channel      = $_POST['channel'] ?? '';
    $language    = $_POST['language'] ?? '';
    $affiliation  = $_POST['affiliation'] ?? '';
    $anonymity    = $_POST['anonymity'] ?? '';

    $wb_name  = trim($_POST['whistleblower_name'] ?? '');
    $wb_email = trim($_POST['whistleblower_email'] ?? '');
    $wb_phone = trim($_POST['whistleblower_phone'] ?? '');

    $incident_when        = trim($_POST['when'] ?? '');
    $incident_where       = trim($_POST['where'] ?? '');
    $incident_division    = trim($_POST['which'] ?? '');
    $incident_description = trim($_POST['description'] ?? '');

    $submitted_by = $_SESSION['admin_name'] ?? 'Admin';

    /* -----------------------------
       2. VALIDATION
    ------------------------------ */
    if (empty($client_token) || empty($channel) || empty($anonymity)) {
        throw new Exception('Required fields (Entity, Channel, Anonymity) are missing.');
    }

    /* -----------------------------
       3. VERIFY CLIENT + GEN NUMBER
    ------------------------------ */
    $checkClient = $pdo->prepare("SELECT name FROM clients WHERE token = ?");
    $checkClient->execute([$client_token]);

    if (!$checkClient->fetch()) {
        throw new Exception("Invalid Entity selected.");
    }

    $casenumber = generateCaseNumber($pdo, $client_token);

    if (!$casenumber) {
        throw new Exception("Failed to generate case number.");
    }

    /* -----------------------------
       4. SAVE TO DATABASE
    ------------------------------ */
    $sql = "INSERT INTO cases (
                casenumber, client_token, status, Case_sensitivity, Case_relevance,affiliation,
                identity_choice, submitted_at, submitted_by, channel,
                full_name, email, phone,
                incident_when, incident_where, incident_division, incident_description,case_manager,language,files
            ) VALUES (
                ?, ?, 'New', 'Low','',?,?, NOW(), ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,'',?,?
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $casenumber,
        $client_token,
        $affiliation,
        $anonymity,
        $submitted_by,
        $channel,
        $wb_name,
        $wb_email,
        $wb_phone,
        $incident_when,
        $incident_where,
        $incident_division,
        $incident_description,
        $language,
        $files_json
    ]);

    $_SESSION['success'] = "New case created successfully: $casenumber";
    header('Location: index.php');
    exit;

} 
catch (Exception $e) {
    echo "<h3 style='color:red;'>Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}
