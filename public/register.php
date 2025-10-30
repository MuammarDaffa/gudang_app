<?php
include '../config/database.php';
require '../functions/mailer.php'; // untuk kirim email aktivasi

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $activation_code = md5(uniqid(rand(), true));

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "Email sudah terdaftar!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, activation_code) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $activation_code);
        $stmt->execute();

        $activation_link = "http://localhost/gudang_app/public/activate.php?code=" . $activation_code;
        sendActivationEmail($email, $activation_link);

        echo "Registrasi berhasil! Silakan cek email untuk aktivasi akun.";
    }
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Nama Lengkap" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Daftar</button>
</form>
