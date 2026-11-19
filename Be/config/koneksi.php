<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'penjualan_online';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $koneksi = new mysqli($host, $user, $password, $database);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan database penjualan_online sudah diimpor. Detail: ' . htmlspecialchars($e->getMessage()));
}
