<?php
require 'config/auth.php';
require_admin();

$msg = '';
$err = '';
$edit = null;

$jabatanList = [
    'Kepala Dinas',
    'Sekretaris',
    'Kasubbag Umum dan Kepegawaian',
    'Staff Subbagian Umum dan Kepegawaian',
    'Staff Administrasi',
    'Staff Operasional',
    'Staff Lapangan',
    'Staff Pelayanan'
];

$unitList = [
    'Sekretariat',
    'Subbagian Umum dan Kepegawaian',
    'Bidang Lalu Lintas',
    'Bidang Angkutan',
    'Bidang Sarana Prasarana'
];

if (isset($_GET['hapus'])) {
    $_SESSION['pegawai'] = array_values(array_filter(
        $_SESSION['pegawai'],
        fn($p) => $p['nip'] != $_GET['hapus']
    ));

    add_notif('Data pegawai berhasil dihapus.');
    header('Location: pegawai.php?msg=hapus');
    exit;
}

if (isset($_GET['edit'])) {
    $edit = find_pegawai($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old     = $_POST['old_nip'] ?? '';
    $nip     = trim($_POST['nip'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $jabatan = $_POST['jabatan'] ?? '';
    $unit    = $_POST['unit'] ?? '';
    $alamat  = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $status  = $_POST['status'] ?? 'Aktif';

    if ($nip === '' || $nama === '' || $jabatan === '' || $unit === '') {
        $err = 'NIP, nama, jabatan, dan unit kerja wajib diisi.';
    }

    foreach ($_SESSION['pegawai'] as $p) {
        if ($p['nip'] === $nip && $p['nip'] !== $old) {
            $err = 'NIP sudah terdaftar, gunakan NIP lain.';
        }
    }

    if ($err === '') {
        $data = compact(
            'nip',
            'nama',
            'jabatan',
            'unit',
            'alamat',
            'telepon',
            'email',
            'status'
        );

        if ($old) {
            foreach ($_SESSION['pegawai'] as &$p) {
                if ($p['nip'] === $old) {
                    $p = $data;
                    break;
                }
            }

            unset($p);

            $msg = 'Data pegawai berhasil diperbarui.';
            add_notif('Data pegawai ' . $nama . ' diperbarui.');
        } else {
            $_SESSION['pegawai'][] = $data;

            $msg = 'Data pegawai berhasil ditambahkan.';
            add_notif('Pegawai baru ' . $nama . ' ditambahkan.');
        }

        $edit = null;
    }
}

$q = strtolower($_GET['q'] ?? '');
$data = $_SESSION['pegawai'];

if ($q) {
    $data = array_values(array_filter($data, function ($p) use ($q) {
        return str_contains(
            strtolower($p['nama'] . ' ' . $p['nip'] . ' ' . $p['jabatan'] . ' ' . $p['unit']),
            $q
        );
    }));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">
        <h1>Data Pegawai</h1>
        <p class="subtitle">Kelola data pegawai</p>

        <?php if ($msg || isset($_GET['msg'])): ?>
            <div class="notice">
                <?= $msg ?: 'Aksi berhasil dilakukan.' ?>
            </div>
        <?php endif; ?>

        <?php if ($err): ?>
            <div class="alert">
                <?= $err ?>
            </div>
        <?php endif; ?>

        <form class="search-row" method="get">
            <input
                name="q"
                value="<?= esc($q) ?>"
                placeholder="Cari NIP / nama / jabatan / unit"
            >

            <button class="btn-small">
                <i class="fa-solid fa-magnifying-glass"></i>
                Cari
            </button>

            <a class="btn-light-mini" href="pegawai.php">
                Reset
            </a>
        </form>

        <form class="pegawai-form-full" method="post">
            <input
                type="hidden"
                name="old_nip"
                value="<?= esc($edit['nip'] ?? '') ?>"
            >

            <div>
                <label>NIP</label>
                <input
                    name="nip"
                    value="<?= esc($edit['nip'] ?? '') ?>"
                    required
                >
            </div>

            <div>
                <label>Nama</label>
                <input
                    name="nama"
                    value="<?= esc($edit['nama'] ?? '') ?>"
                    required
                >
            </div>

            <div>
                <label>Jabatan</label>
                <select name="jabatan" required>
                    <option value="">Pilih Jabatan</option>

                    <?php foreach ($jabatanList as $j): ?>
                        <option value="<?= esc($j) ?>" <?= ($edit['jabatan'] ?? '') === $j ? 'selected' : '' ?>>
                            <?= esc($j) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Unit Kerja</label>
                <select name="unit" required>
                    <option value="">Pilih Unit</option>

                    <?php foreach ($unitList as $u): ?>
                        <option value="<?= esc($u) ?>" <?= ($edit['unit'] ?? '') === $u ? 'selected' : '' ?>>
                            <?= esc($u) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label>Alamat</label>
                <input
                    name="alamat"
                    value="<?= esc($edit['alamat'] ?? '') ?>"
                >
            </div>

            <div>
                <label>No HP</label>
                <input
                    name="telepon"
                    value="<?= esc($edit['telepon'] ?? '') ?>"
                >
            </div>

            <div>
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    value="<?= esc($edit['email'] ?? '') ?>"
                >
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    <option value="Aktif" <?= ($edit['status'] ?? '') === 'Aktif' ? 'selected' : '' ?>>
                        Aktif
                    </option>

                    <option value="Tidak Aktif" <?= ($edit['status'] ?? '') === 'Tidak Aktif' ? 'selected' : '' ?>>
                        Tidak Aktif
                    </option>
                </select>
            </div>

            <button class="btn-blue">
                <?php if ($edit): ?>
                    <i class="fa-solid fa-pen-to-square"></i>
                    Update Pegawai
                <?php else: ?>
                    <i class="fa-solid fa-user-plus"></i>
                    Tambah Pegawai
                <?php endif; ?>
            </button>
        </form>

        <table class="simple-table">
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Unit Kerja</th>
                <th>No HP</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php foreach ($data as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($p['nip']) ?></td>
                    <td><?= esc($p['nama']) ?></td>
                    <td><?= esc($p['jabatan']) ?></td>
                    <td><?= esc($p['unit']) ?></td>
                    <td><?= esc($p['telepon']) ?></td>
                    <td>
                        <?php if (($p['status'] ?? '') === 'Aktif'): ?>
                            <span style="background:#dcfce7;color:#15803d;padding:6px 14px;border-radius:20px;font-weight:700;">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span style="background:#fee2e2;color:#b91c1c;padding:6px 14px;border-radius:20px;font-weight:700;">
                                <?= esc($p['status']) ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="pegawai.php?edit=<?= urlencode($p['nip']) ?>">
                            <i class="fa-solid fa-pen"></i>
                            Edit
                        </a>

                        |

                        <a
                            class="danger-link"
                            onclick="return confirm('Hapus data pegawai ini?')"
                            href="pegawai.php?hapus=<?= urlencode($p['nip']) ?>"
                        >
                            <i class="fa-solid fa-trash"></i>
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</div>
</body>
</html>