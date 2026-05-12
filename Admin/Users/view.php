<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../assets/mailer.php';

include '../includes/header.php';
include '../includes/sidebar.php';

if ($_SESSION['user_type'] !== 'Super Admin') {
    http_response_code(403);
    exit('Access denied');
}

$userId = $_GET['id'] ?? '';
if ($userId === '') {
    exit('Invalid user');
}

/* Fetch user */
$stmt = $pdo->prepare("
    SELECT id, name, email, telephone, user_type, status, created_at
    FROM admin_users
    WHERE id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('User not found');
}

/* Fetch assigned clients (read-only) */
$stmt = $pdo->prepare("
    SELECT c.name
    FROM user_client_assignments uca
    JOIN clients c ON c.token = uca.client_token
    WHERE uca.user_id = ?
");
$stmt->execute([$userId]);
$assignedClients = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>
<h2>User Profile</h2>
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

<form method="POST" action="update_user.php">

  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

  <div class="card">

    <div class="form-grid">            
      <div class="form-group">
        <label>User ID</label>
        <input type="text" value="<?= $user['id'] ?>" disabled>
      </div>

      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
      </div>

      <div class="form-group">
        <label>Telephone</label>
        <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
      </div>

      <div>
        <label>User Type</label>
        <select name="user_type">
          <option <?= $user['user_type']=='Super Admin'?'selected':'' ?>>Super Admin</option>
          <option <?= $user['user_type']=='BDO User'?'selected':'' ?>>BDO User</option>
          <option <?= $user['user_type']=='Client User'?'selected':'' ?>>Client User</option>
        </select>
      </div>

      <div
        <label>Status</label>
        <select name="status">
          <option <?= $user['status']=='Active'?'selected':'' ?>>Active</option>
          <option <?= $user['status']=='Inactive'?'selected':'' ?>>Inactive</option>
        </select>
      </div>
    </div>

  </div>
  <div class="card">
    <h3>Assigned Clients</h3>

    <?php if (empty($assignedClients)): ?>
      <p class="muted">No clients assigned</p>
    <?php else: ?>
      <ul class="list">
        <?php foreach ($assignedClients as $client): ?>
          <li><?= htmlspecialchars($client) ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="muted">Client assignments are managed from the Clients page.</p>
    <?php endif; ?>
  </div>

  <div class="card actions">
    <button class="primary-btn">Save Changes</button>

    <button type="submit" formaction="resend_credentials.php" style="margin-left: 10px;background: #333333;">
      Resend Credentials & Force Reset
    </button>
  </div>
</form>

<?php include '../includes/footer.php'; ?>

