SIMPEG DISHUB - versi final revisi sesuai SRS FR-01 sampai FR-08

Cara pakai:
1. Extract folder simpeg_dishub ke C:\xampp\htdocs\
2. Jalankan XAMPP, aktifkan Apache.
3. Buka browser: http://localhost/simpeg_dishub

Login:
- admin / admin123
- irawan / 12345
- atasan / atasan123

Fitur yang sudah direvisi:
- FR-01 Absensi Digital: absen masuk/pulang, validasi duplikasi.
- FR-02 Validasi Otomatis: validasi data kosong, tanggal cuti, NIP duplikat, hak akses role.
- FR-03 Data Pegawai: tambah, edit, hapus, cari, NIP, nama, jabatan dropdown, unit kerja, alamat, no HP, email, status.
- FR-04 Pengajuan Cuti: jenis cuti, tanggal, alasan, status pending.
- FR-05 Approval Cuti: approve/reject oleh admin/atasan, alasan reject wajib.
- FR-06 Dashboard Monitoring: ringkasan pegawai, cuti aktif, pending, grafik.
- FR-07 Laporan: laporan pegawai/absen/cuti/rekap dan download CSV.
- FR-08 Notifikasi Sistem: ikon lonceng, daftar notifikasi, tandai dibaca.

Catatan:
Aplikasi ini memakai session PHP agar mudah langsung dicoba tanpa import database.
File database/simpeg_dishub.sql tetap disediakan sebagai rancangan database MySQL.
