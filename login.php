<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek panjang password minimal 8 karakter
    if (strlen($password) < 8) {
        $error = "Password harus memiliki minimal 8 karakter.";
    } else {
        $result = $conn->query("SELECT * FROM users WHERE username='$username'");
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Password salah!😡";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styleloginn.css">
</head>
<body>
    <!-- Container utama login -->
    <div class="login-container">
        <h2>Login</h2>

        <!-- Pesan error jika login gagal -->
        <?php if (isset($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <!-- Input username -->
            <input type="text" name="username" placeholder="Username" required>

            <!-- Input password -->
            <input type="password" name="password" placeholder="Password (minimal 8 karakter)" required minlength="8">

            <!-- Tombol login -->
            <button type="submit">Login</button>
        </form>
        
        <p>Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
    <!-- Partikel glowing -->
<div class="particles"></div>

<!-- Awan bergerak -->
<div class="clouds">
    <div class="cloud1"></div>
    <div class="cloud2"></div>
    <div class="cloud3"></div>
</div>

</body>
</html>
