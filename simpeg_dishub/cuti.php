<?php
require 'config/auth.php';

$msg = '';
$err = '';

$nama = $_SESSION['user']['nama'];
$nip  = $_SESSION['user']['nip'] ?? '19800101';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis   = $_POST['jenis'] ?? '';
    $mulai   = $_POST['mulai'] ?? '';
    $selesai = $_POST['selesai'] ?? '';
    $alasan  = trim($_POST['alasan'] ?? '');

    if ($jenis === '' || $mulai === '' || $selesai === '') {
        $err = 'Jenis cuti, tanggal mulai, dan tanggal selesai wajib diisi.';
    } elseif (strtotime($selesai) < strtotime($mulai)) {
        $err = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
    } else {
        foreach ($_SESSION['cuti'] as $c) {
            if (
                $c['nip'] === $nip &&
                $c['mulai'] === $mulai &&
                $c['selesai'] === $selesai &&
                $c['status'] !== 'Ditolak'
            ) {
                $err = 'Pengajuan cuti pada tanggal tersebut sudah ada.';
            }
        }
    }

    if ($err === '') {
        $durasiHari = max(1, (strtotime($selesai) - strtotime($mulai)) / 86400 + 1);

        $_SESSION['cuti'][] = [
            'id'      => time(),
            'nip'     => $nip,
            'nama'    => $nama,
            'jenis'   => $jenis,
            'mulai'   => $mulai,
            'selesai' => $selesai,
            'durasi'  => $durasiHari . ' Hari',
            'alasan'  => $alasan,
            'status'  => 'Pending',
            'catatan' => ''
        ];

        add_notif('Pengajuan cuti baru dari ' . $nama . ' menunggu persetujuan.');
        $msg = 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan.';
    }
}

$last = array_values(array_filter($_SESSION['cuti'], function ($c) use ($nama) {
    return $c['nama'] === $nama;
}));

$last = end($last);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Cuti</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content no-pad">
        <div class="page-head">
            <h1>Pengajuan Cuti</h1>
            <p>Dashboard / Cuti / Pengajuan Cuti</p>
        </div>

        <?php if ($msg): ?>
            <div class="notice inside"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($err): ?>
            <div class="alert inside"><?= $err ?></div>
        <?php endif; ?>

        <div class="cuti-layout">
            <form class="cuti-form" method="post">
                <label>Jenis Cuti</label>
                <select name="jenis" required>
                    <option value="">Pilih Jenis Cuti</option>
                    <option value="Cuti Sakit">Cuti Sakit</option>
                    <option value="Cuti Tahunan">Cuti Tahunan</option>
                    <option value="Cuti Alasan Penting">Cuti Alasan Penting</option>
                </select>

                <div class="two-col">
                    <div>
                        <label>Tanggal Mulai</label>
                        <input type="date" name="mulai" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div>
                        <label>Tanggal Selesai</label>
                        <input type="date" name="selesai" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <label>Alasan Cuti</label>
                <textarea name="alasan" placeholder="Opsional" maxlength="255"></textarea>
                <small>Maksimal 255 karakter</small>

                <div class="actions">
                    <button class="btn-blue">
                        <i class="fa-solid fa-paper-plane"></i>
                        Ajukan Cuti
                    </button>

                    <button type="reset" class="btn-light">
                        Reset
                    </button>
                </div>
            </form>

            <aside class="status-card">
                <h2>Status Pengajuan</h2>

                <div class="clock">
                    <i class="fa-regular fa-clock"></i>
                </div>

                <h2 class="pending">
                    <?= $last ? strtoupper($last['status']) : 'BELUM ADA' ?>
                </h2>

                <p>
                    <?php if ($last): ?>
                        <?= $last['status'] === 'Pending'
                            ? 'Menunggu persetujuan atasan.'
                            : 'Pengajuan sudah diproses.'
                        ?>
                    <?php else: ?>
                        Belum mengajukan cuti.
                    <?php endif; ?>
                </p>

                <?php if ($last): ?>
                    <br>

                    <p>
                        Diajukan pada<br>
                        <?= tgl_id($last['mulai']) ?>
                    </p>

                    <?php if (!empty($last['catatan'])): ?>
                        <p>
                            Catatan:<br>
                            <b><?= esc($last['catatan']) ?></b>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>

                <p>
                    Diajukan oleh<br>
                    <b><?= esc($nama) ?></b>
                </p>
            </aside>
        </div>
    </main>
</div>
</body>
</html>