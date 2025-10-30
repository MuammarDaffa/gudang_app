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

    // cek email unik (kecuali milik sendiri)
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
        $success = "Profil diperbarui.";
        $_SESSION['user_name'] = $name;
    }
}

// Ubah password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new1 = $_POST['new_password'];
    $new2 = $_POST['confirm_password'];

    // ambil password hash dari DB
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
<html>
<head><title>Profil & Ubah Password</title></head>
<body>
  <a href="dashboard.php"><< Kembali</a>
  <h2>Profil</h2>

  <?php foreach($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>
  <?php if ($success): ?>
    <p style="color:green"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nama</label><br>
    <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
    <label>Email (username)</label><br>
    <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    <button name="update_profile" type="submit">Simpan Profil</button>
  </form>

  <hr>
  <h3>Ubah Password</h3>
  <form method="post">
    <input type="password" name="current_password" placeholder="Password sekarang" required><br>
    <input type="password" name="new_password" placeholder="Password baru" required><br>
    <input type="password" name="confirm_password" placeholder="Konfirmasi password" required><br>
    <button name="change_password" type="submit">Ubah Password</button>
  </form>
</body>
</html>
