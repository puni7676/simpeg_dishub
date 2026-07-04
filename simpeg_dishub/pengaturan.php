<?php
require 'config/koneksi.php';
require 'config/auth.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['hapus_foto'])) {
        $id = $_SESSION['user']['id'];
        $fotoLama = $_SESSION['user']['foto'] ?? '';

        if (!empty($fotoLama) && file_exists($fotoLama)) {
            unlink($fotoLama);
        }

        $foto = '';

        $stmt = $koneksi->prepare("
            UPDATE users 
            SET foto = ?
            WHERE id = ?
        ");

        $stmt->bind_param("si", $foto, $id);
        $stmt->execute();

        $_SESSION['user']['foto'] = '';

        add_notif('Foto profil berhasil dihapus.');
        $msg = 'Foto profil berhasil dihapus.';
    }

    if (isset($_POST['profil'])) {
        $id = $_SESSION['user']['id'];

        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $telepon = trim($_POST['telepon']);

        $foto = $_SESSION['user']['foto'] ?? '';

        if (!empty($_FILES['foto']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png'];
            $maxSize = 2 * 1024 * 1024;

            if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
                $err = 'Foto hanya boleh JPG atau PNG.';
            } elseif ($_FILES['foto']['size'] > $maxSize) {
                $err = 'Ukuran foto maksimal 2 MB.';
            } else {
                $folder = 'assets/img/';

                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }

                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $namaFile = time() . '_profil.' . $ext;
                $tujuan = $folder . $namaFile;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
                    if (!empty($foto) && file_exists($foto)) {
                        unlink($foto);
                    }

                    $foto = $tujuan;
                } else {
                    $err = 'Foto profil gagal diupload.';
                }
            }
        }

        if ($err === '') {
            $stmt = $koneksi->prepare("
                UPDATE users 
                SET nama = ?, email = ?, telepon = ?, foto = ?
                WHERE id = ?
            ");

            $stmt->bind_param("ssssi", $nama, $email, $telepon, $foto, $id);
            $stmt->execute();

            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['telepon'] = $telepon;
            $_SESSION['user']['foto'] = $foto;

            add_notif('Profil berhasil diperbarui.');
            $msg = 'Profil berhasil disimpan.';
        }
    }

    if (isset($_POST['password'])) {
        if (($_POST['baru'] ?? '') !== ($_POST['konfirmasi'] ?? '')) {
            $err = 'Konfirmasi password tidak sama.';
        } elseif (strlen($_POST['baru'] ?? '') < 5) {
            $err = 'Password baru minimal 5 karakter.';
        } else {
            add_notif('Password berhasil diperbarui.');
            $msg = 'Password berhasil diperbarui. (Demo session)';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="app">

    <?php include 'sidebar.php'; ?>

    <main class="content">
        <h1>Pengaturan</h1>
        <p class="subtitle">Profil dan password pengguna</p>

        <?php if ($msg): ?>
            <div class="notice"><?= $msg ?></div>
        <?php endif; ?>

        <?php if ($err): ?>
            <div class="alert"><?= $err ?></div>
        <?php endif; ?>

        <div class="settings-grid">

            <form class="form-box" method="post" enctype="multipart/form-data">
                <h2>Profil</h2>

                <label>Foto Profil</label>

                <?php if (!empty($_SESSION['user']['foto'])): ?>
                    <img
                        id="fotoSaatIni"
                        src="<?= esc($_SESSION['user']['foto']) ?>?v=<?= time() ?>"
                        style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:10px 0;"
                    >
                <?php else: ?>
                    <div
                        id="fotoSaatIni"
                        style="width:80px;height:80px;border-radius:50%;background:linear-gradient(#ffc989,#75452e);margin:10px 0;"
                    ></div>
                <?php endif; ?>

                <img
                    id="previewFoto"
                    style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:10px 0;display:none;"
                >

                <input type="file" name="foto" accept="image/jpeg,image/png">

                <small>Format hanya JPG/PNG. Maksimal ukuran 2 MB.</small>

                <?php if (!empty($_SESSION['user']['foto'])): ?>
                    <button
                        type="submit"
                        name="hapus_foto"
                        class="btn-light"
                        onclick="return confirm('Yakin ingin menghapus foto profil?')"
                    >
                        Hapus Foto
                    </button>
                <?php endif; ?>

                <label>Nama Lengkap</label>
                <input name="nama" value="<?= esc($_SESSION['user']['nama']) ?>" required>

                <label>Jabatan</label>
                <input value="<?= esc($_SESSION['user']['jabatan']) ?>" readonly>
                <small>Jabatan hanya bisa diubah admin lewat menu Data Pegawai.</small>

                <label>Email</label>
                <input type="email" name="email" value="<?= esc($_SESSION['user']['email'] ?? '') ?>">

                <label>No HP</label>
                <input name="telepon" value="<?= esc($_SESSION['user']['telepon'] ?? '') ?>">

                <button name="profil" class="btn-blue">
                    Simpan Profil
                </button>
            </form>

            <form class="form-box" method="post">
                <h2>Ubah Password</h2>

                <label>Password Lama</label>
                <input type="password" name="lama">

                <label>Password Baru</label>
                <input type="password" name="baru">

                <label>Konfirmasi Password</label>
                <input type="password" name="konfirmasi">

                <button name="password" class="btn-blue">
                    Simpan Password
                </button>
            </form>

        </div>
    </main>
</div>

<script>
const inputFoto = document.querySelector('input[name="foto"]');
const previewFoto = document.getElementById('previewFoto');

inputFoto.addEventListener('change', function () {
    const file = this.files[0];

    if (file) {
        previewFoto.src = URL.createObjectURL(file);
        previewFoto.style.display = 'block';
    } else {
        previewFoto.style.display = 'none';
    }
});
</script>

</body>
</html>