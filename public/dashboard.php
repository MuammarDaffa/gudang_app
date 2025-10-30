<?php
require_once '../functions/auth.php';
requireLogin();
require_once '../config/database.php';

$user = currentUser($conn);
?>
<!doctype html>
<html>
<head><title>Dashboard Admin Gudang</title></head>
<body>
  <h2>Selamat datang, <?= htmlspecialchars($user['name']) ?></h2>
  <ul>
    <li><a href="products.php">Kelola Produk</a></li>
    <li><a href="profile.php">Profil & Ubah Password</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</body>
</html>
