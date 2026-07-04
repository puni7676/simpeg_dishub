<?php
require 'config/auth.php';
date_default_timezone_set('Asia/Jakarta');

$nama = $_SESSION['user']['nama'];
$today = hari_ini();
$msg = '';

if (!isset($_SESSION['absensi'][$nama][$today])) {
    $_SESSION['absensi'][$nama][$today] = [
        'masuk' => '',
        'pulang' => '',
        'status' => 'Belum Absen'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['aksi'] === 'masuk' && !$_SESSION['absensi'][$nama][$today]['masuk']) {
        $_SESSION['absensi'][$nama][$today]['masuk'] = date('H:i:s');
        $_SESSION['absensi'][$nama][$today]['status'] = 'Hadir - Sudah Absen Masuk';
        $msg = 'Absen masuk berhasil disimpan.';
        add_notif($nama . ' berhasil absen masuk.');
    } elseif ($_POST['aksi'] === 'pulang' && $_SESSION['absensi'][$nama][$today]['masuk'] && !$_SESSION['absensi'][$nama][$today]['pulang']) {
        $_SESSION['absensi'][$nama][$today]['pulang'] = date('H:i:s');
        $_SESSION['absensi'][$nama][$today]['status'] = 'Hadir Lengkap';
        $msg = 'Absen pulang berhasil disimpan.';
        add_notif($nama . ' berhasil absen pulang.');
    } else {
        $msg = 'Validasi: absen masuk dulu sebelum absen pulang, dan absen tidak boleh duplikat.';
    }
}

$abs = $_SESSION['absensi'][$nama][$today];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="welcome">
            <div>
                <h2>Selamat Datang,</h2>
                <h1><?= esc($nama) ?></h1>
                <p><?= esc($_SESSION['user']['jabatan']) ?></p>
            </div>

            <b><?= tanggal_indonesia() ?></b>
        </div>

        <?php if ($msg): ?>
            <div class="notice"><?= $msg ?></div>
        <?php endif; ?>

        <section class="absen-box">
            <h1>Absen Hari Ini</h1>

            <div class="absen-grid">

                <div class="absen-card masuk">
                    <div class="absen-title">Absen Masuk</div>

                    <div class="absen-icon">
                        <i class="fa-solid fa-right-to-bracket"></i>
                    </div>

                    <p>Jam Masuk</p>
                    <h3><?= $abs['masuk'] ?: '-' ?></h3>

                    <form method="post">
                        <input type="hidden" name="aksi" value="masuk">
                        <button>Absen Masuk</button>
                    </form>
                </div>

                <div class="absen-card pulang">
                    <div class="absen-title">Absen Pulang</div>

                    <div class="absen-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>

                    <p>Jam Pulang</p>
                    <h3><?= $abs['pulang'] ?: 'Belum Absen Pulang' ?></h3>

                    <form method="post">
                        <input type="hidden" name="aksi" value="pulang">
                        <button>Absen Pulang</button>
                    </form>
                </div>

            </div>

            <h2>Status Kehadiran</h2>
            <p class="status-line"><?= esc($abs['status']) ?></p>
        </section>
    </main>
</div>
</body>
</html>