<?php
include '../config/database.php';

if (isset($_GET['token']) && isset($_POST['reset'])) {
    $token = $_GET['token'];
    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $newpass, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<div class='alert success'>✅ Password berhasil diubah! <a href='login.php'>Login</a></div>";
    } else {
        echo "<div class='alert error'>❌ Token tidak valid!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<style>
  * {
    font-family: "Poppins", sans-serif;
    box-sizing: border-box;
  }

  body {
    background: linear-gradient(135deg, #1A73E8, #673AB7);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }

  .container {
    background: #fff;
    width: 100%;
    max-width: 400px;
    padding: 35px 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    text-align: center;
    animation: fadeIn 0.8s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }

  h2 {
    color: #333;
    margin-bottom: 15px;
  }

  p {
    color: #666;
    font-size: 14px;
    margin-bottom: 25px;
  }

  input[type="password"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    transition: border 0.3s, box-shadow 0.3s;
  }

  input[type="password"]:focus {
    border-color: #1A73E8;
    box-shadow: 0 0 5px rgba(26, 115, 232, 0.4);
    outline: none;
  }

  button {
    width: 100%;
    padding: 12px;
    border: none;
    background: #1A73E8;
    color: white;
    font-size: 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
  }

  button:hover {
    background: #155AB6;
  }

  .alert {
    margin-bottom: 15px;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
  }

  .alert.success {
    background: #D4EDDA;
    color: #155724;
    border: 1px solid #C3E6CB;
  }

  .alert.error {
    background: #F8D7DA;
    color: #721C24;
    border: 1px solid #F5C6CB;
  }

  a {
    color: #1A73E8;
    text-decoration: none;
    font-weight: 500;
  }

  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>🔐 Reset Password</h2>
    <p>Masukkan password baru Anda untuk mengatur ulang akun.</p>

    <form method="POST">
      <input type="password" name="password" placeholder="Password Baru" required>
      <button type="submit" name="reset">Ubah Password</button>
    </form>

    <p><a href="login.php">← Kembali ke Login</a></p>
  </div>
</body>
</html>
