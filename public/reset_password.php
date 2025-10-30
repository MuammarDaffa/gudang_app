<?php
include '../config/database.php';

if (isset($_GET['token']) && isset($_POST['reset'])) {
    $token = $_GET['token'];
    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $newpass, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Password berhasil diubah! <a href='login.php'>Login</a>";
    } else {
        echo "Token tidak valid!";
    }
}
?>

<form method="POST">
    <input type="password" name="password" placeholder="Password Baru" required>
    <button type="submit" name="reset">Ubah Password</button>
</form>
