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
        echo "Tautan reset password telah dikirim ke email Anda.";
    } else {
        echo "Email tidak ditemukan!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Masukkan email Anda" required>
    <button type="submit" name="submit">Kirim Tautan Reset</button>
</form>
