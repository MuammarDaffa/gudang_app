<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// ambil data user saat login
function currentUser($conn) {
    if (!isLoggedIn()) return null;
    $id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, name, email, is_active FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>
