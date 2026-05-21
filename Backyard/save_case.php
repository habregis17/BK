<?php
session_start();
if (!isset($_SESSION['agent_id'])) exit();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_token = $_POST['client_token'];
    $channel = $_POST['channel'];
    $full_name = $_POST['full_name'] ?? 'N/A';
    $phone = $_POST['phone'] ?? 'N/A';
    $affiliation = $_POST['affiliation'];
    $incident_date = !empty($_POST['incident_date']) ? $_POST['incident_date'] : null;
    $incident_description = $_POST['incident_description'];
    $incident_where = $_POST['incident_where'];
    $incident_division = $_POST['incident_division'];
    $sensitivity = $_POST['Case_sensitivity'];
    
    // Generate unique case number
    $casenumber = "CASE-BYRD-" . strtoupper(substr(md5(uniqid()), 0, 6)) . "-" . date('Ym') . "00" . rand(1,9);

    $sql = "INSERT INTO cases (
                casenumber, client_token, affiliation, identity_choice, full_name, 
                phone, incident_date, incident_description, files, 
                status, incident_where, incident_division, Case_sensitivity, 
                Case_manager, channel, language
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'New', ?, ?, ?, ?, ?, 'en')";

    $identity_choice = !empty($_POST['full_name']) ? "Identifiable" : "Anonymous";
    $empty_files = "[]";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssss", 
        $casenumber, $client_token, $affiliation, $identity_choice, $full_name,
        $phone, $incident_date, $incident_description, $empty_files,
        $incident_where, $incident_division, $sensitivity,
        $_SESSION['agent_id'], $channel
    );

    if ($stmt->execute()) {
        echo "<script>alert('Case " . $casenumber . " saved successfully!'); window.location='index.php';</script>";
    } else {
        echo "Error saving case: " . $stmt->error;
    }
    $stmt->close();
}
?>