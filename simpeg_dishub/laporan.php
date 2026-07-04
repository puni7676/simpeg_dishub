<?php
require 'config/auth.php';

$jenis = $_GET['jenis'] ?? '';

function badge_status($status)
{
    if (
        $status === 'Aktif' ||
        $status === 'Disetujui' ||
        $status === 'Hadir' ||
        $status === 'Hadir Lengkap' ||
        $status === 'Hadir - Sudah Absen Masuk'
    ) {
        return '<span style="background:#dcfce7;color:#15803d;padding:6px 14px;border-radius:20px;font-weight:700;">' . esc($status) . '</span>';
    }

    if ($status === 'Pending') {
        return '<span style="background:#fef3c7;color:#b45309;padding:6px 14px;border-radius:20px;font-weight:700;">Pending</span>';
    }

    return '<span style="background:#fee2e2;color:#b91c1c;padding:6px 14px;border-radius:20px;font-weight:700;">' . esc($status) . '</span>';
}

if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=laporan_' . ($jenis ?: 'simpeg') . '.csv');

    if ($jenis === 'cuti') {
        echo "Nama,Jenis Cuti,Tanggal Mulai,Tanggal Selesai,Status\n";

        foreach ($_SESSION['cuti'] as $c) {
            echo $c['nama'] . ',' . $c['jenis'] . ',' . $c['mulai'] . ',' . $c['selesai'] . ',' . $c['status'] . "\n";
        }
    } elseif ($jenis === 'absen') {
        echo "Nama,Tanggal,Jam Masuk,Jam Pulang,Status\n";

        foreach ($_SESSION['absensi'] as $nama => $rows) {
            foreach ($rows as $tgl => $a) {
                echo $nama . ',' . $tgl . ',' . $a['masuk'] . ',' . $a['pulang'] . ',' . $a['status'] . "\n";
            }
        }
    } else {
        echo "NIP,Nama,Jabatan,Unit,No HP,Status\n";

        foreach ($_SESSION['pegawai'] as $p) {
            echo $p['nip'] . ',' . $p['nama'] . ',' . $p['jabatan'] . ',' . $p['unit'] . ',' . $p['telepon'] . ',' . $p['status'] . "\n";
        }
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kepegawaian</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content report">
        <h1>Laporan Kepegawaian</h1>

        <form class="filter-row" method="get">
            <div>
                <label>Jenis Laporan</label>
                <select name="jenis">
                    <option value="pegawai" <?= $jenis === 'pegawai' ? 'selected' : '' ?>>
                        Laporan Data Pegawai
                    </option>

                    <option value="absen" <?= $jenis === 'absen' ? 'selected' : '' ?>>
                        Laporan Absen
                    </option>

                    <option value="cuti" <?= $jenis === 'cuti' ? 'selected' : '' ?>>
                        Laporan Cuti
                    </option>

                    <option value="rekap" <?= $jenis === 'rekap' ? 'selected' : '' ?>>
                        Rekap Kehadiran
                    </option>
                </select>
            </div>

            <div>
                <label>Periode</label>
                <input type="date" name="awal" value="<?= esc($_GET['awal'] ?? '2026-04-01') ?>">
            </div>

            <span>s.d.</span>

            <input type="date" name="akhir" value="<?= esc($_GET['akhir'] ?? '2026-04-30') ?>">

            <button class="btn-small">
                <i class="fa-solid fa-filter"></i>
                Tampilkan
            </button>
        </form>

        <div class="report-grid">
            <a href="laporan.php?jenis=pegawai" class="report-card green">
                <div class="r-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>
                    <h3>Laporan Data Pegawai</h3>
                    <p>Menampilkan laporan data pegawai</p>
                </div>

                <b><i class="fa-solid fa-download"></i></b>
            </a>

            <a href="laporan.php?jenis=absen" class="report-card blue">
                <div class="r-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>

                <div>
                    <h3>Laporan Absen</h3>
                    <p>Menampilkan laporan absen pegawai</p>
                </div>

                <b><i class="fa-solid fa-download"></i></b>
            </a>

            <a href="laporan.php?jenis=cuti" class="report-card orange">
                <div class="r-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>

                <div>
                    <h3>Laporan Cuti</h3>
                    <p>Menampilkan laporan pengajuan cuti</p>
                </div>

                <b><i class="fa-solid fa-download"></i></b>
            </a>

            <a href="laporan.php?jenis=rekap" class="report-card purple">
                <div class="r-icon">
                    <i class="fa-solid fa-chart-column"></i>
                </div>

                <div>
                    <h3>Rekap Kehadiran</h3>
                    <p>Menampilkan rekap kehadiran pegawai</p>
                </div>

                <b><i class="fa-solid fa-download"></i></b>
            </a>
        </div>

        <?php if ($jenis): ?>
            <h2>Hasil <?= esc(strtoupper($jenis)) ?></h2>

            <a class="btn-download" href="laporan.php?jenis=<?= urlencode($jenis) ?>&download=1">
                <i class="fa-solid fa-file-csv"></i>
                Download CSV
            </a>

            <table class="simple-table">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>

                <?php $no = 1; ?>

                <?php if ($jenis === 'cuti'): ?>

                    <?php foreach ($_SESSION['cuti'] as $c): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td><?= esc($c['nama']) ?></td>

                            <td>
                                <?= esc($c['jenis'] . ' (' . tgl_id($c['mulai']) . ' - ' . tgl_id($c['selesai']) . ')') ?>
                            </td>

                            <td>
                                <?= badge_status($c['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php elseif ($jenis === 'absen'): ?>

                    <?php foreach ($_SESSION['absensi'] as $nama => $rows): ?>
                        <?php foreach ($rows as $tgl => $a): ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td><?= esc($nama) ?></td>

                                <td>
                                    Tanggal <?= esc($tgl) ?>,
                                    Masuk <?= esc($a['masuk'] ?: '-') ?>,
                                    Pulang <?= esc($a['pulang'] ?: '-') ?>
                                </td>

                                <td>
                                    <?= badge_status($a['status']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                <?php elseif ($jenis === 'rekap'): ?>

                    <?php foreach ($_SESSION['pegawai'] as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td><?= esc($p['nama']) ?></td>

                            <td>Rekap Kehadiran April 2026</td>

                            <td>
                                <?= badge_status($p['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <?php foreach ($_SESSION['pegawai'] as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td><?= esc($p['nama']) ?></td>

                            <td>
                                <?= esc($p['jabatan'] . ' - ' . $p['unit']) ?>
                            </td>

                            <td>
                                <?= badge_status($p['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </table>
        <?php endif; ?>
    </main>
</div>
</body>
</html>