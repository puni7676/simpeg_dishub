<?php $page = basename($_SERVER['PHP_SELF']); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<aside class="sidebar">
    <div class="brand">
        <img src="assets/img/logo-dishub.png" alt="Logo Dishub" class="brand-logo">

        <div>
            <b>SIMPEG</b>
            <span>DISHUB</span>
        </div>
    </div>

    <nav>
        <a href="dashboard.php" class="<?= $page == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <a href="absensi.php" class="<?= $page == 'absensi.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Absensi</span>
        </a>

        <a href="cuti.php" class="<?= $page == 'cuti.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-signature"></i>
            <span>Cuti</span>
        </a>

        <a href="pegawai.php" class="<?= $page == 'pegawai.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span>Pegawai</span>
        </a>

        <a href="persetujuan_cuti.php" class="<?= $page == 'persetujuan_cuti.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-check"></i>
            <span>Approval</span>
        </a>

        <a href="laporan.php" class="<?= $page == 'laporan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            <span>Laporan</span>
        </a>

        <a href="notifikasi.php" class="<?= $page == 'notifikasi.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-bell"></i>
            <span>Notifikasi</span>
        </a>

        <a href="pengaturan.php" class="<?= $page == 'pengaturan.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i>
            <span>Pengaturan</span>
        </a>

        <a href="logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>