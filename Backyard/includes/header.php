<?php
// expects session started via auth_check
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$userType  = $_SESSION['user_type'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?></title>

  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="topbar">
  <div class="topbar-left">
    <img src="../assets/images/ourlogo.png" alt="Org Logo" class="logo">
  </div>
  <h2>BDO East Africa(Rwanda) Ltd Whistleblowing Platform</h2>
  <div class="topbar-right">
    <div class="user-info">
      <i class="fa-solid fa-circle-user avatar"></i>
      <div class="user-text">
        <span class="name"><?= htmlspecialchars($adminName) ?></span>
        <span class="role"><?= htmlspecialchars($userType) ?></span>
      </div>
    </div>
  </div>
</header>

<div class="layout">