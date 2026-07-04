<?php
session_start();

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

function init_data()
{
    if (!isset($_SESSION['pegawai'])) {
        $_SESSION['pegawai'] = [
            [
                'nip' => '19800101',
                'nama' => 'Irawan',
                'jabatan' => 'Staff Subbagian Umum dan Kepegawaian',
                'unit' => 'Subbagian Umum dan Kepegawaian',
                'alamat' => 'Yogyakarta',
                'telepon' => '081234567890',
                'email' => 'irawan@dishub.go.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '19800202',
                'nama' => 'Budi',
                'jabatan' => 'Staff Operasional',
                'unit' => 'Bidang Lalu Lintas',
                'alamat' => 'Sleman',
                'telepon' => '081234567891',
                'email' => 'budi@dishub.go.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '19800303',
                'nama' => 'Siti',
                'jabatan' => 'Staff Administrasi',
                'unit' => 'Sekretariat',
                'alamat' => 'Bantul',
                'telepon' => '081234567892',
                'email' => 'siti@dishub.go.id',
                'status' => 'Aktif'
            ],
            [
                'nip' => '19800404',
                'nama' => 'Andi',
                'jabatan' => 'Staff Lapangan',
                'unit' => 'Bidang Angkutan',
                'alamat' => 'Kulon Progo',
                'telepon' => '081234567893',
                'email' => 'andi@dishub.go.id',
                'status' => 'Tidak Aktif'
            ],
            [
                'nip' => '19800505',
                'nama' => 'Rina',
                'jabatan' => 'Staff Pelayanan',
                'unit' => 'Bidang Sarana Prasarana',
                'alamat' => 'Gunungkidul',
                'telepon' => '081234567894',
                'email' => 'rina@dishub.go.id',
                'status' => 'Aktif'
            ],
        ];
    }

    if (!isset($_SESSION['absensi'])) {
        $_SESSION['absensi'] = [];
    }

    if (!isset($_SESSION['cuti'])) {
        $_SESSION['cuti'] = [
            [
                'id' => 1,
                'nip' => '19800202',
                'nama' => 'Budi',
                'jenis' => 'Cuti Tahunan',
                'mulai' => '2026-04-20',
                'selesai' => '2026-04-22',
                'durasi' => '3 Hari',
                'alasan' => 'Keperluan keluarga',
                'status' => 'Pending',
                'catatan' => ''
            ],
            [
                'id' => 2,
                'nip' => '19800303',
                'nama' => 'Siti',
                'jenis' => 'Cuti Sakit',
                'mulai' => '2026-04-21',
                'selesai' => '2026-04-21',
                'durasi' => '1 Hari',
                'alasan' => 'Sakit',
                'status' => 'Pending',
                'catatan' => ''
            ],
            [
                'id' => 3,
                'nip' => '19800404',
                'nama' => 'Andi',
                'jenis' => 'Cuti Tahunan',
                'mulai' => '2026-04-27',
                'selesai' => '2026-04-30',
                'durasi' => '4 Hari',
                'alasan' => 'Liburan keluarga',
                'status' => 'Pending',
                'catatan' => ''
            ],
            [
                'id' => 4,
                'nip' => '19800505',
                'nama' => 'Rina',
                'jenis' => 'Cuti Alasan Penting',
                'mulai' => '2026-04-25',
                'selesai' => '2026-04-25',
                'durasi' => '1 Hari',
                'alasan' => 'Urusan penting',
                'status' => 'Pending',
                'catatan' => ''
            ],
        ];
    }

    if (!isset($_SESSION['notifikasi'])) {
        $_SESSION['notifikasi'] = [
            [
                'pesan' => 'Selamat datang di SIMPEG DISHUB.',
                'waktu' => date('d/m/Y H:i:s'),
                'dibaca' => false
            ],
            [
                'pesan' => 'Terdapat pengajuan cuti yang menunggu persetujuan.',
                'waktu' => date('d/m/Y H:i:s'),
                'dibaca' => false
            ],
        ];
    }
}

init_data();

function role()
{
    return $_SESSION['user']['role'] ?? 'pegawai';
}

function is_admin()
{
    return role() === 'admin';
}

function is_atasan()
{
    return role() === 'atasan';
}

function can_approve()
{
    return is_admin() || is_atasan();
}

function require_admin()
{
    if (!is_admin()) {
        header('Location: dashboard.php?error=unauthorized');
        exit;
    }
}

function require_approval()
{
    if (!can_approve()) {
        header('Location: dashboard.php?error=unauthorized');
        exit;
    }
}

function tgl_id($date)
{
    return date('d/m/Y', strtotime($date));
}

function hari_ini()
{
    return date('Y-m-d');
}

function esc($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function find_pegawai($nip)
{
    foreach ($_SESSION['pegawai'] as $p) {
        if ($p['nip'] === $nip) {
            return $p;
        }
    }

    return null;
}

function add_notif($pesan)
{
    $_SESSION['notifikasi'][] = [
        'pesan' => $pesan,
        'waktu' => date('d/m/Y H:i:s'),
        'dibaca' => false
    ];
}

function unread_count()
{
    return count(array_filter($_SESSION['notifikasi'], function ($n) {
        return empty($n['dibaca']);
    }));
}

function tanggal_indonesia()
{
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    return $hari[date('l')] . ', ' . date('d') . ' ' . $bulan[date('F')] . ' ' . date('Y');
}
?>