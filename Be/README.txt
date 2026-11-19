SISTEM INFORMASI PENJUALAN ONLINE - PHP NATIVE

ISI:
- PHP Native + MySQLi
- Bootstrap 5 (memerlukan koneksi internet untuk CDN)
- CRUD pelanggan, kategori, produk, pesanan, pembayaran
- Dashboard ringkasan
- database.sql

CARA INSTALASI DI LARAGON:
1. Ekstrak folder penjualan_online ke D:\laragon\www\
2. Jalankan Apache dan MySQL dari Laragon.
3. Buka http://localhost/phpmyadmin
4. Pilih menu Import, lalu pilih file database.sql dari folder proyek.
   Alternatif: buka tab SQL dan jalankan isi database.sql.
5. Periksa config/koneksi.php:
   host=localhost, user=root, password kosong, database=penjualan_online.
   Sesuaikan jika konfigurasi MySQL Anda berbeda.
6. Buka http://localhost/penjualan_online/

URUTAN PENGISIAN DATA:
1. Tambahkan pelanggan.
2. Tambahkan kategori.
3. Tambahkan produk.
4. Buat pesanan (butuh pelanggan dan produk).
5. Tambahkan pembayaran (butuh pesanan).

CATATAN ERD:
Tabel pesanan mengikuti ERD yang diberikan: satu pesanan menyimpan satu id_produk.
Relasi pembayaran dibuat 1:0..1 (satu pesanan maksimal memiliki satu pembayaran).
Untuk keamanan dan integritas, penghapusan pelanggan/produk yang masih dipakai pesanan
akan ditolak oleh database. Kategori yang dihapus akan membuat id_kategori produk menjadi NULL.

CATATAN:
Ini starter project untuk pembelajaran lokal, belum dilengkapi login/role, CSRF token,
fitur upload gambar produk, cetak invoice, atau payment gateway.
