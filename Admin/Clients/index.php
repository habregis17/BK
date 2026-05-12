<?php
require '../auth/auth_check.php';
require '../../config/db.php';
require '../includes/header.php';
require '../includes/sidebar.php';


if ($_SESSION['user_type'] !== 'Super Admin') {
    exit('Forbidden');
}

/* Fetch all clients */
$clients = $pdo->query("
    SELECT *
    FROM clients
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch admins */
$admins = $pdo->query("
    SELECT id, name, email
    FROM admin_users
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* Fetch assignments */
$assignments = $pdo->query("
    SELECT *
    FROM user_client_assignments
")->fetchAll(PDO::FETCH_ASSOC);

/* Build lookup [client_id => [user_ids]] */
$clientAssignments = [];
foreach ($assignments as $a) {
    $clientAssignments[$a['client_token']][] = $a['user_id'];
}
?>
<div class="page-header">
  <div>
    <h2>Clients Management</h2>
    <p class="muted">Manage clients and assign responsible users</p>
  </div>
  <button class="secondary-btn" onclick="openAddClientModal()">
    <i class="fas fa-plus"></i>Add New Client
  </button>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Token</th>
            <th>Telephone</th>
            <th>BDO Contact</th>
            <th>Client Contact</th>
            <th>Assigned Users</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client['name']) ?></td>
            <td><?= htmlspecialchars($client['email']) ?></td>
            <td><?= htmlspecialchars($client['token']) ?></td>
            <td><?= htmlspecialchars($client['Telephone']) ?></td>
            <td><?= htmlspecialchars($client['BDO_contact']) ?></td>
            <td><?= htmlspecialchars($client['Client_Contact']) ?></td>
            <td>
                <?php
                $assignedUserIds = $clientAssignments[$client['token']] ?? [];
                $assignedUsers = array_filter($admins, function($admin) use ($assignedUserIds) {
                    return in_array($admin['id'], $assignedUserIds);
                });
                echo implode(', ', array_map(function($u) { return htmlspecialchars($u['name']); }, $assignedUsers));
                ?>
            </td>
            <td>
                <div class="action-buttons">
              <a class="btn-view" href="view.php?token=<?= $client['token'] ?>"><i class="fa-solid fa-eye"></i> View</a>
              <a class="btn-view portal" href="/Submit/?token=<?= urlencode($client['token']) ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Portal</a>
            </div>
            </td>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div id="addClientModal" class="modal">
  <div class="modal-content large">

    <h3>Add New Client</h3>
<form method="POST" action="add_client.php">
      <div class="form-grid">
        <div>
          <label>Client Name *</label>
          <input type="text" name="name" required>
        </div>

        <div>
          <label>Email</label>
          <input type="email" name="email">
        </div>

        <div>
          <label>Telephone</label>
          <input type="text" name="telephone">
        </div>

        <div>
          <label>BDO Contact</label>
          <input type="text" name="bdo_contact">
        </div>

        <div>
          <label>Client Contact</label>
          <input type="text" name="client_contact">
        </div>

        <div class="full">
          <label>Description</label>
          <textarea name="description" rows="3"></textarea>
        </div>
      </div>

      <hr>

      <h4>Assign Users</h4>
      <div class="checkbox-grid">
        <?php foreach ($admins as $admin): ?>
          <label class="checkbox-item">
            <input type="checkbox" name="users[]" value="<?= $admin['id'] ?>">
            <div class="user-info">
              <strong><?= htmlspecialchars($admin['name']) ?></strong>
              <small><?= htmlspecialchars($admin['email']) ?></small>
            </div>
          </label>
        <?php endforeach; ?>
      </div>

      <div class="modal-actions">
        <button type="submit" class="primary-btn">Save Client</button>
        <button type="button" class="btn" onclick="closeAddClientModal()">Cancel</button>
      </div>

    </form>
  </div>
</div>

<script>
function openAddClientModal() {
  document.getElementById('addClientModal').style.display = 'flex';
}

function closeAddClientModal() {
  document.getElementById('addClientModal').style.display = 'none';
}
</script>

<?php require '../includes/footer.php'; ?>
