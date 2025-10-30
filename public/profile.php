<?php
require_once '../functions/auth.php';
requireLogin();
require_once '../config/database.php';

$user = currentUser($conn);
$uid = $user['id'];
$errors = [];
$success = null;

// Update profil (name, email)
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $uid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $errors[] = "Email sudah dipakai oleh pengguna lain.";
    } else {
        $stmt2 = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt2->bind_param("ssi", $name, $email, $uid);
        $stmt2->execute();
        $success = "Profil berhasil diperbarui.";
        $_SESSION['user_name'] = $name;
    }
}

// Ubah password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new1 = $_POST['new_password'];
    $new2 = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $row['password'])) {
        $errors[] = "Password sekarang salah.";
    } elseif ($new1 !== $new2) {
        $errors[] = "Konfirmasi password tidak cocok.";
    } else {
        $hash = password_hash($new1, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt2->bind_param("si", $hash, $uid);
        $stmt2->execute();
        $success = "Password berhasil diubah.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil & Ubah Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body {
          background-color: #f5f6fa;
      }
      .container {
          max-width: 700px;
          margin-top: 50px;
      }
      .card {
          border-radius: 14px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      }
      .form-control:focus {
          border-color: #0d6efd;
          box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.25);
      }
      .btn-primary {
          border-radius: 8px;
      }
  </style>
</head>
<body>

<div class="container">
  <a href="dashboard.php" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard</a>

  <div class="card p-4">
      <h3 class="text-center text-primary mb-4">👤 Profil Pengguna</h3>

      <?php if(!empty($errors)): ?>
          <div class="alert alert-danger">
              <ul class="mb-0">
                  <?php foreach($errors as $e): ?>
                      <li><?= htmlspecialchars($e) ?></li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>

      <?php if($success): ?>
          <div class="alert alert-success text-center">
              <?= htmlspecialchars($success) ?>
          </div>
      <?php endif; ?>

      <!-- Form Update Profil -->
      <form method="post" class="mb-4">
          <div class="mb-3">
              <label class="form-label fw-semibold">Nama Lengkap</label>
              <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
          </div>
          <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
          </div>
          <button name="update_profile" type="submit" class="btn btn-primary w-100">💾 Simpan Perubahan Profil</button>
      </form>

      <hr class="my-4">

      <h4 class="text-secondary mb-3">🔒 Ubah Password</h4>
      <form method="post">
          <div class="mb-3">
              <label class="form-label fw-semibold">Password Sekarang</label>
              <input type="password" name="current_password" class="form-control" placeholder="Masukkan password sekarang" required>
          </div>
          <div class="mb-3">
              <label class="form-label fw-semibold">Password Baru</label>
              <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
          </div>
          <div class="mb-3">
              <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Ketik ulang password baru" required>
          </div>
          <button name="change_password" type="submit" class="btn btn-warning w-100">🔁 Ubah Password</button>
      </form>
  </div>
</div>

</body>
</html>
