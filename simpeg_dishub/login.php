<?php
session_start();
require 'config/koneksi.php';

$error = '';
$logoutMsg = '';

if (isset($_GET['logout'])) {
    $logoutMsg = 'Anda berhasil logout.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['password'] === $password) {
            $_SESSION['user'] = [
                'id'       => $row['id'],
                'username' => $row['username'],
                'nama'     => $row['nama'],
                'nip'      => $row['nip'],
                'jabatan'  => $row['jabatan'],
                'role'     => $row['role'],
                'email'    => $row['email'],
                'telepon'  => $row['telepon'],
                'foto'     => $row['foto']
            ];

            header('Location: dashboard.php');
            exit;
        }
    }

    $error = 'Username atau password salah';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIMPEG DISHUB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-page">
    <div class="login-card">
        <div class="logo-login center">
            <img src="assets/img/logo-dishub.png" alt="Logo Dishub" class="logo-big">

            <h2>SIMPEG</h2>
            <p>Dinas Perhubungan</p>
        </div>

        <h1>Login</h1>
        <p class="muted">Sistem Informasi Kepegawaian</p>

        <?php if ($error): ?>
            <div class="alert"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($logoutMsg): ?>
            <div class="notice"><?= $logoutMsg ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input name="username" placeholder="admin / irawan / atasan" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="admin123 / 12345 / atasan123" required>

            <button class="btn-primary full">Masuk</button>
        </form>
    </div>
</body>
</html>