<?php
session_start();
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/db.php';

$casenumber = $_GET['casenumber'] ?? '';

if (empty($casenumber)) {
    echo "Case number not provided.";
    exit();
}

// Fetch case details, ensuring it belongs to the current agent
$agent_id = $_SESSION['agent_id'];
$case_sql = "
    SELECT c.*, cl.name AS client_name
    FROM cases c
    JOIN clients cl ON c.client_token = cl.token
    WHERE c.casenumber = ? AND c.Case_manager = ?
";
$case_stmt = $conn->prepare($case_sql);
$case_stmt->bind_param("ss", $casenumber, $agent_id);
$case_stmt->execute();
$case_result = $case_stmt->get_result();
$case_details = $case_result->fetch_assoc();

if (!$case_details) {
    echo "Case not found or you do not have permission to view it.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Case Details - <?php echo htmlspecialchars($casenumber); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Backyard Portal - Case Details</span>
            <a href="index.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Case #<?php echo htmlspecialchars($case_details['casenumber']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Client:</strong> <?php echo htmlspecialchars($case_details['client_name']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> <?php echo htmlspecialchars($case_details['status']); ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Sensitivity:</strong> <?php echo htmlspecialchars($case_details['Case_sensitivity']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Channel:</strong> <?php echo htmlspecialchars($case_details['channel']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Incident Description:</strong>
                    <p><?php echo nl2br(htmlspecialchars($case_details['incident_description'])); ?></p>
                </div>
                <!-- Add more details as needed -->
            </div>
        </div>
    </div>
</body>
</html>
