<?php
require 'config/auth.php';

if (isset($_GET['read'])) {
    foreach ($_SESSION['notifikasi'] as &$n) {
        $n['dibaca'] = true;
    }

    unset($n);

    header('Location: notifikasi.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">
        <h1>Notifikasi Sistem</h1>
        <p class="subtitle">
            Informasi status cuti, absensi, dan perubahan data
        </p>

        <a class="btn-download" href="notifikasi.php?read=1">
            <i class="fa-solid fa-check-double"></i>
            Tandai Semua Dibaca
        </a>

        <div class="notif-list">
            <?php if (empty($_SESSION['notifikasi'])): ?>
                <div class="notif-card">
                    <b>Belum ada notifikasi.</b>
                    <span>-</span>
                </div>
            <?php else: ?>
                <?php foreach (array_reverse($_SESSION['notifikasi']) as $n): ?>
                    <div class="notif-card <?= empty($n['dibaca']) ? 'unread' : '' ?>">
                        <div>
                            <?php if (empty($n['dibaca'])): ?>
                                <i class="fa-solid fa-circle-info"></i>
                            <?php else: ?>
                                <i class="fa-regular fa-circle-check"></i>
                            <?php endif; ?>

                            <b><?= esc($n['pesan']) ?></b>
                        </div>

                        <span><?= esc($n['waktu']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>