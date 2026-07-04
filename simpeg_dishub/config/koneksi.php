<?php
$host = 'localhost: 3303';
$user = 'root';
$pass = '';
$db   = 'simpeg_dishub';

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}
?>
