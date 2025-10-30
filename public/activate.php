<?php
include '../config/database.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $stmt = $conn->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE activation_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Akun berhasil diaktifkan! <a href='login.php'>Login sekarang</a>";
    } else {
        echo "Kode aktivasi tidak valid!";
    }
}
?>
