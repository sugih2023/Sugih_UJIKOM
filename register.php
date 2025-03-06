<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Memeriksa apakah password memiliki panjang minimal 8 karakter
    if (strlen($password) < 8) {
        $error = "Password harus memiliki minimal 8 karakter.";
    } else {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Periksa apakah username sudah ada
        $sql_check = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Jika username belum ada, lanjutkan dengan insert
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if ($conn->query($sql)) {
                header("Location: login.php?success=Registrasi Berhasil! Silakan Login.");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styleregister.css">
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        
        <?php if (isset($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password (min. 8 karakter)" required minlength="8">
            <button type="submit">Daftar</button>
        </form>
        
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
