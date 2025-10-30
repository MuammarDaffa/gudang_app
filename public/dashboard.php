<?php
require_once '../functions/auth.php';
requireLogin();
require_once '../config/database.php';

$user = currentUser($conn);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin Gudang</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      background: linear-gradient(135deg, #4e73df, #1cc88a);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dashboard-container {
      background: #fff;
      padding: 2.5rem;
      width: 400px;
      border-radius: 1rem;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #333;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .welcome {
      color: #555;
      margin-bottom: 2rem;
      font-size: 1rem;
    }

    ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    li {
      margin: 0.8rem 0;
    }

    a {
      display: block;
      padding: 0.75rem;
      border-radius: 0.5rem;
      text-decoration: none;
      color: #fff;
      font-weight: 500;
      background: #4e73df;
      transition: all 0.3s ease;
    }

    a:hover {
      background: #2e59d9;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }

    .logout {
      background: #e74a3b;
    }

    .logout:hover {
      background: #c0392b;
      box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    footer {
      margin-top: 2rem;
      font-size: 0.85rem;
      color: #888;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h2>👋 Selamat Datang</h2>
    <p class="welcome"><?= htmlspecialchars($user['name']) ?></p>

    <ul>
      <li><a href="products.php">📦 Kelola Produk</a></li>
      <li><a href="profile.php">👤 Profil & Ubah Password</a></li>
      <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>

    <footer>© 2025 Sistem Gudang</footer>
  </div>
</body>
</html>
