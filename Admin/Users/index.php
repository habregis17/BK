<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../includes/header.php';
require '../includes/sidebar.php';


if ($_SESSION['user_type'] !== 'Super Admin') {
    exit('Forbidden');
}

$users = $pdo->query("
  SELECT id, name, email, telephone, user_type, status, created_at
  FROM admin_users
  ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="clients-header">
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
  <div>
    <h2>Users Management</h2>
  </div>

  <button class="primary-btn" onclick="openAddUserModal()">
    <i class="fas fa-user-plus"></i> Add User
  </button>
</div>
<table class="data-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Telephone</th>
      <th>User Type</th>
      <th>Status</th>
      <th>Created</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['telephone']) ?></td>

        <!-- USER TYPE BADGE -->
        <td>
          <?php if ($u['user_type'] === 'Super Admin'): ?>
            <span class="badge danger">Super Admin</span>
          <?php elseif ($u['user_type'] === 'BDO User'): ?>
            <span class="badge primary">BDO User</span>
          <?php else: ?>
            <span class="badge muted">Client User</span>
          <?php endif; ?>
        </td>

        <!-- STATUS -->
        <td>
          <span class="badge <?= $u['status']==='Active'?'success':'secondary' ?>">
            <?= $u['status'] ?>
          </span>
        </td>

        <td><?= $u['created_at'] ?></td>

        <td>
          <a class="btn-view" href="view.php?id=<?= $u['id'] ?>">
            <i class="fa-solid fa-eye"></i> View
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="addUserModal" class="modal">
  <div class="modal-content large">

    <h3>Add New User</h3>

    <form method="POST" action="add_user.php">

      <div class="form-grid">
        <div>
          <label>Full Name *</label>
          <input type="text" name="name" required>
        </div>

        <div>
          <label>Email *</label>
          <input type="email" name="email" required>
        </div>

        <div>
          <label>Telephone</label>
          <input type="text" name="telephone">
        </div>

        <div>
          <label>User Type</label>
          <select name="user_type" required>
            <option value="Super Admin">Super Admin</option>
            <option value="BDO User">BDO User</option>
            <option value="Client User">Client User</option>
          </select>
        </div>

        <div>
          <label>Status</label>
          <select name="status">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <hr>
      <div class="modal-actions">
        <button type="submit" class="primary-btn">
          <i class="fas fa-user-plus"></i> Create User
        </button>
        <button type="button" class="btn" onclick="closeAddUserModal()">Cancel</button>
      </div>

    </form>
  </div>
</div>
<script>
function openAddUserModal() {
  document.getElementById('addUserModal').style.display = 'flex';
}

function closeAddUserModal() {
  document.getElementById('addUserModal').style.display = 'none';
}
</script>

<?php include '../includes/footer.php'; ?>