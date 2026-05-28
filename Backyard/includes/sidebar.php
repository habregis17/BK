<?php
$isSuperAdmin = ($_SESSION['user_type'] ?? '') === 'Super Admin';
?>
<aside class="sidebar">
  <nav class="menu">
    <a href="../dashboard/index.php" class="menu-item">
      <i class="fa-solid fa-house"></i><span>Dashboard</span>
    </a>
  </nav>

  <div class="sidebar-bottom">
    <a href="../auth/logout.php" class="menu-item logout">
      <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
    </a>
  </div>
</aside>

<main class="content">