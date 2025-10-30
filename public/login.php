<?php
include '../config/database.php';
session_start();

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_active'] == 1) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
        } else {
            $error = "⚠️ Akun belum diaktifkan!";
        }
    } else {
        $error = "❌ Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Gudang</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #fff;
            width: 360px;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-10px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #4e73df;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.4);
        }

        button {
            width: 100%;
            background: #4e73df;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #2e59d9;
        }

        .link {
            margin-top: 1rem;
            display: block;
            color: #4e73df;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .link:hover {
            text-decoration: underline;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0% {transform: translateX(0);}
            25% {transform: translateX(-5px);}
            50% {transform: translateX(5px);}
            75% {transform: translateX(-5px);}
            100% {transform: translateX(0);}
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Login Sistem Gudang</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Masukkan Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" name="login">Masuk</button>
        </form>

        <a href="forgot_password.php" class="link">Lupa Password?</a>
    </div>
</body>
</html>
