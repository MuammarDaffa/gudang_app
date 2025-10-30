<?php
include '../config/database.php';
require '../functions/mailer.php';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $token = md5(uniqid(rand(), true));

    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $reset_link = "http://localhost/gudang_app/public/reset_password.php?token=" . $token;
        sendResetEmail($email, $reset_link);
        echo "<div class='alert success'>✅ Tautan reset password telah dikirim ke email Anda.</div>";
    } else {
        echo "<div class='alert error'>❌ Email tidak ditemukan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password</title>
  <style>
    * {
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #007bff, #6610f2);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    .container {
      background: #fff;
      width: 100%;
      max-width: 380px;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      text-align: center;
    }

    h2 {
      color: #333;
      margin-bottom: 20px;
    }

    form input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
      outline: none;
      transition: all 0.3s ease;
    }

    form input:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0,123,255,0.3);
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      background: #007bff;
      color: white;
      font-size: 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #0056b3;
    }

    .alert {
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .alert.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .note {
      margin-top: 10px;
      font-size: 13px;
      color: #666;
    }

    a {
      color: #007bff;
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
    <h2>🔒 Reset Password</h2>
    <p class="note">Masukkan email Anda untuk menerima tautan reset password.</p>
    <form method="POST">
      <input type="email" name="email" placeholder="Masukkan email Anda" required>
      <button type="submit" name="submit">Kirim Tautan Reset</button>
    </form>
    <p class="note"><a href="login.php">Kembali ke Halaman Login</a></p>
  </div>
</body>
</html>
