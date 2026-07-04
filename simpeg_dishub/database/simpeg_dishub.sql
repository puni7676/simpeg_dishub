CREATE DATABASE IF NOT EXISTS simpeg_dishub;
USE simpeg_dishub;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','pegawai','atasan') NOT NULL,
  nip VARCHAR(30),
  nama VARCHAR(100),
  jabatan VARCHAR(100),
  email VARCHAR(100),
  telepon VARCHAR(20)
);

CREATE TABLE pegawai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(30) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL,
  jabatan VARCHAR(100) NOT NULL,
  unit_kerja VARCHAR(100) NOT NULL,
  alamat TEXT,
  nomor_telepon VARCHAR(20),
  email VARCHAR(100),
  status_pegawai VARCHAR(30) DEFAULT 'Aktif'
);

CREATE TABLE absensi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(30),
  tanggal DATE,
  jam_masuk TIME,
  jam_pulang TIME,
  status VARCHAR(30),
  keterangan TEXT
);

CREATE TABLE cuti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(30),
  jenis_cuti VARCHAR(50),
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  alasan TEXT,
  status ENUM('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  catatan_approval TEXT
);

CREATE TABLE notifikasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  pesan TEXT,
  waktu DATETIME,
  dibaca TINYINT(1) DEFAULT 0
);

INSERT INTO users(username,password,role,nip,nama,jabatan,email,telepon) VALUES
('admin','admin123','admin','ADM001','Admin Kepegawaian','Admin SIMPEG DISHUB','admin@dishub.go.id','081111111111'),
('irawan','12345','pegawai','19800101','Irawan','Staff Subbagian Umum dan Kepegawaian','irawan@dishub.go.id','081234567890'),
('atasan','atasan123','atasan','ATS001','Kepala Bagian','Kepala Bagian/Atasan','atasan@dishub.go.id','082222222222');

INSERT INTO pegawai(nip,nama,jabatan,unit_kerja,alamat,nomor_telepon,email,status_pegawai) VALUES
('19800101','Irawan','Staff Subbagian Umum dan Kepegawaian','Subbagian Umum dan Kepegawaian','Yogyakarta','081234567890','irawan@dishub.go.id','Aktif'),
('19800202','Budi','Staff Operasional','Bidang Lalu Lintas','Sleman','081234567891','budi@dishub.go.id','Aktif'),
('19800303','Siti','Staff Administrasi','Sekretariat','Bantul','081234567892','siti@dishub.go.id','Aktif'),
('19800404','Andi','Staff Lapangan','Bidang Angkutan','Kulon Progo','081234567893','andi@dishub.go.id','Aktif'),
('19800505','Rina','Staff Pelayanan','Bidang Sarana Prasarana','Gunungkidul','081234567894','rina@dishub.go.id','Aktif');
