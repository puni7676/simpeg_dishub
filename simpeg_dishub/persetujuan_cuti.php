<?php
require 'config/auth.php';
require_approval();

$msg = '';
$err = '';

function badge_cuti($status)
{
    if ($status === 'Disetujui') {
        return '<span style="background:#dcfce7;color:#15803d;padding:6px 14px;border-radius:20px;font-weight:700;">Disetujui</span>';
    }

    if ($status === 'Pending') {
        return '<span style="background:#fef3c7;color:#b45309;padding:6px 14px;border-radius:20px;font-weight:700;">Pending</span>';
    }

    return '<span style="background:#fee2e2;color:#b91c1c;padding:6px 14px;border-radius:20px;font-weight:700;">Ditolak</span>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = $_POST['id'] ?? '';
    $aksi    = $_POST['aksi'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');

    if ($aksi === 'Ditolak' && $catatan === '') {
        $err = 'Alasan penolakan wajib diisi.';
    }

    if ($err === '') {
        foreach ($_SESSION['cuti'] as &$c) {
            if ($c['id'] == $id) {
                if ($c['status'] !== 'Pending') {
                    $err = 'Pengajuan ini sudah diproses sebelumnya.';
                    break;
                }

                $c['status']  = $aksi;
                $c['catatan'] = $catatan;

                $msg = 'Pengajuan cuti berhasil diproses.';
                add_notif('Pengajuan cuti ' . $c['nama'] . ' ' . $aksi . '.');
                break;
            }
        }

        unset($c);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Cuti</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">
        <h1>Persetujuan Cuti</h1>
        <h3>Menunggu persetujuan</h3>

        <?php if ($msg): ?>
            <div class="notice"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($err): ?>
            <div class="alert"><?= $err ?></div>
        <?php endif; ?>

        <section class="approval-panel">
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Pegawai</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php foreach ($_SESSION['cuti'] as $i => $c): ?>
                    <tr class="<?= $c['status'] !== 'Pending' ? 'fade' : '' ?>">
                        <td><?= $i + 1 ?></td>

                        <td><?= esc($c['nama']) ?></td>

                        <td><?= esc($c['jenis']) ?></td>

                        <td>
                            <?= tgl_id($c['mulai']) ?> -
                            <?= tgl_id($c['selesai']) ?>
                        </td>

                        <td><?= esc($c['durasi']) ?></td>

                        <td><?= badge_cuti($c['status']) ?></td>

                        <td>
                            <?php if ($c['status'] === 'Pending'): ?>
                                <form class="inline approval-form" method="post">
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">

                                    <input
                                        name="catatan"
                                        placeholder="Catatan jika ditolak"
                                    >

                                    <button class="ok" name="aksi" value="Disetujui">
                                        <i class="fa-solid fa-check"></i>
                                    </button>

                                    <button class="no" name="aksi" value="Ditolak">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="font-weight:700;color:#666;">
                                    Selesai
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="table-foot">
                <b>Menampilkan <?= count($_SESSION['cuti']) ?> data</b>

                <div>
                    <button>&lt;</button>
                    <button class="page-active">1</button>
                    <button>&gt;</button>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>