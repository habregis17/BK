<?php
$isSuperAdmin = ($_SESSION['user_type'] ?? '') === 'Super Admin';
?>
<aside class="sidebar">
  <nav class="menu">
    <a href="../dashboard/index.php" class="menu-item">
      <i class="fa-solid fa-house"></i><span>Dashboard</span>
    </a>
</aside>

<main class="content">