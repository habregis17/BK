<?php
$isSuperAdmin = ($_SESSION['user_type'] ?? '') === 'Super Admin';
?>
<aside class="sidebar">
  <nav class="menu">
    <a href="../dashboard/index.php" class="menu-item">
      <i class="fa-solid fa-house"></i><span>Dashboard</span>
    </a>

    <a href="../cases/index.php" class="menu-item">
      <i class="fa-solid fa-list-check"></i><span>Cases</span>
    </a>
    
    <?php if ($_SESSION['user_type'] === 'Super Admin'): ?>
        <a href="../clients/index.php" class="menu-item">
          <i class="fa-solid fa-briefcase"></i><span>Clients</span>
        </a>
    <?php endif; ?>

    <?php if ($isSuperAdmin): ?>
      <a href="../users/index.php" class="menu-item">
        <i class="fa-solid fa-users"></i><span>Users</span>
      </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="menu-item logout">
      <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
    </a>
  </div>
</aside>

<main class="content">