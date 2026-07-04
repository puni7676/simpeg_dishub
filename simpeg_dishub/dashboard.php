<?php
require 'config/auth.php';

$aktif = count(array_filter($_SESSION['pegawai'], fn($p) => $p['status'] === 'Aktif'));
$pending = count(array_filter($_SESSION['cuti'], fn($c) => $c['status'] === 'Pending'));
$cutiAktif = count(array_filter($_SESSION['cuti'], fn($c) => $c['status'] === 'Disetujui'));

$totalPegawai = count($_SESSION['pegawai']);
$today = hari_ini();

$hadirHariIni = 0;
foreach ($_SESSION['absensi'] as $namaPegawai => $rows) {
    if (!empty($rows[$today]['masuk'])) {
        $hadirHariIni++;
    }
}

$persenHadir = $totalPegawai > 0 ? round(($hadirHariIni / $totalPegawai) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS Utama -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">

        <div class="topbar">
            <div></div>

            <div class="icons">
                <a class="bell" href="notifikasi.php">
                    <i class="fa-solid fa-bell"></i>
                    <sup><?= unread_count() ?></sup>
                </a>
        <?php if (!empty($_SESSION['user']['foto'])): ?>
            <img class="avatar" src="<?= esc($_SESSION['user']['foto']) ?>?v=<?= time() ?>" alt="Foto Profil">
        <?php else: ?>
            <div class="avatar"></div>
        <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert">
                Akses ditolak. Menu tersebut hanya untuk role tertentu.
            </div>
        <?php endif; ?>

        <h1>Dashboard</h1>
        <p class="subtitle">Ringkasan Informasi Kepegawaian</p>

        <section class="summary">

            <a href="pegawai.php" class="stat icon-only">
                <div class="circle">
                    <i class="fa-solid fa-users"></i>
                </div>
                <p>Pegawai Aktif</p>
                <h2><?= $aktif ?></h2>
            </a>

            <a href="laporan.php" class="stat">
                <div class="circle">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <p>Kehadiran Hari Ini</p>
                <h2><?= $persenHadir ?>%</h2>
                <span><?= $hadirHariIni ?> / <?= $totalPegawai ?> Pegawai</span>
            </a>

            <a href="laporan.php" class="stat">
                <div class="circle">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>
                <p>Cuti Aktif</p>
                <h2><?= $cutiAktif ?></h2>
                <span>Pegawai</span>
            </a>

            <a href="persetujuan_cuti.php" class="stat">
                <div class="circle">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <p>Pengajuan Pending</p>
                <h2><?= $pending ?></h2>
                <span>Menunggu Persetujuan</span>
            </a>

        </section>

        <section class="chart-panel">
            <div>
                <h3>Persentase Kehadiran (April 2026)</h3>
                <canvas id="barChart"></canvas>
                <div class="legend">■ Kehadiran</div>
            </div>

            <div>
                <h3>Statistik Cuti April 2026</h3>
                <canvas id="doughnutChart"></canvas>
            </div>
        </section>

    </main>
</div>

<script src="assets/js/chart.js"></script>
</body>
</html>