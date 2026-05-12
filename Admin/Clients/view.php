<?php
require '../auth/auth_check.php';
require '../../config/db.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    http_response_code(403);
    exit('Access denied');
}

$client = null;

/* Resolve by token (preferred) */
if (!empty($_GET['token'])) {
    $clientToken = trim($_GET['token']);

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE token = ?");
    $stmt->execute([$clientToken]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

/* Optional fallback: resolve by ID */
} elseif (!empty($_GET['id'])) {
    $clientId = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$clientId]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$client) {
    exit('Client not found');
}

/* Canonical token (VARCHAR!) */
$clientToken = $client['token'];


/* Fetch users */
$users = $pdo->query("
  SELECT id, name, email
  FROM admin_users
  ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch assignments using client token */
$assigned = $pdo->prepare("
  SELECT user_id
  FROM user_client_assignments
  WHERE client_token = ?
");
$assigned = $pdo->prepare("
  SELECT user_id
  FROM user_client_assignments
  WHERE client_token = ?
");
$assigned->execute([$clientToken]);
$assignedUserIds = array_column(
    $assigned->fetchAll(PDO::FETCH_ASSOC),
    'user_id'
);

$assignedUsers = array_filter($users, fn($u) =>
    in_array($u['id'], $assignedUserIds)
);

$unassignedUsers = array_filter($users, fn($u) =>
    !in_array($u['id'], $assignedUserIds)
);

$pageTitle = 'Client details';
require '../includes/header.php';
require '../includes/sidebar.php';
?>
<h2>View Client Details</h2>

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <i class="fa-solid fa-check-circle"></i>
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-error">
    <i class="fa-solid fa-exclamation-circle"></i>
    <?= htmlspecialchars($_SESSION['error']) ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card">
    
  <h3>Client Information</h3>
  <form method="POST" action="update_client.php">
    <input type="hidden" name="client_id" value="<?= $clientToken ?>">

    <div class="form-grid">
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>">
      </div>
  
      <div class="form-group">
        <label>Telephone</label>
        <input type="text" name="telephone" value="<?= htmlspecialchars($client['Telephone']) ?>">
      </div>

      <div class="form-group">
        <label>BDO Contact</label>
        <input type="text" name="bdo_contact" value="<?= htmlspecialchars($client['BDO_contact']) ?>">
      </div>

      <div class="form-group">
        <label>Client Contact</label>
        <input type="text" name="client_contact" value="<?= htmlspecialchars($client['Client_Contact']) ?>">
      </div>

      <div class="form-group">
        <label>Token</label>
        <input type="text" value="<?= htmlspecialchars($client['token']) ?>" readonly>
      </div>
    </div>

    <button class="primary-btn">Update Client</button>
  </form>
</div>

<div class="card">
  <h3>Manage User Assignments</h3>
  <p class="muted">Select users to add on the left and users to remove on the right. Save when ready.</p>


  <form method="POST" action="assign_users.php">
  <input type="hidden" name="client_token" value="<?= htmlspecialchars($client['token']) ?>">

  <div class="card">
    <h3>Assign Users to Client</h3>
    <p class="muted">Checked users will have access to this client.</p>

    <div class="checkbox-grid">
      <?php foreach ($users as $user): ?>
        <label class="checkbox-item">
          <input
            type="checkbox"
            name="users[]"
            value="<?= $user['id'] ?>"
            <?= in_array($user['id'], $assignedUserIds) ? 'checked' : '' ?>
          >
          <div class="user-info">
            <strong><?= htmlspecialchars($user['name']) ?></strong>
            <small><?= htmlspecialchars($user['email']) ?></small>
          </div>
        </label>
      <?php endforeach; ?>
    </div>

  </div>
    <button type="submit" class="primary-btn">Save Assignments</button>

</form>
</div>
<?php require '../includes/footer.php'; ?>