<?php
require_once __DIR__ . '/cors.php';

$dbConnected = false;
$koneksi = null;

try {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'penjualan_online';
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $koneksi = new mysqli($host, $user, $password, $database);
    $koneksi->set_charset('utf8mb4');
    $dbConnected = true;
} catch (Throwable $e) {
    $dbConnected = false;
}

$counts = ['pelanggan' => 3, 'kategori' => 4, 'produk' => 6, 'pesanan' => 3, 'pembayaran' => 2];
$totalPenjualan = 495000;
$recent = [
    ['id_pesanan' => 103, 'nama_pelanggan' => 'Budi Santoso', 'nama_produk' => 'Desk Mat Anti-Slip Soft Blue', 'tanggal_pesanan' => '2026-11-19 13:00:00', 'total_harga' => 65000, 'status_pesanan' => 'Menunggu'],
    ['id_pesanan' => 102, 'nama_pelanggan' => 'Siti Nurhaliza', 'nama_produk' => 'Mechanical Keyboard TKL Pastel', 'tanggal_pesanan' => '2026-11-19 11:30:00', 'total_harga' => 345000, 'status_pesanan' => 'Diproses'],
    ['id_pesanan' => 101, 'nama_pelanggan' => 'Ahmad Fauzi', 'nama_produk' => 'Mouse Wireless Ergonomis', 'tanggal_pesanan' => '2026-11-19 10:15:00', 'total_harga' => 85000, 'status_pesanan' => 'Selesai']
];

if ($dbConnected) {
    try {
        $realCounts = [];
        foreach (['pelanggan', 'kategori', 'produk', 'pesanan', 'pembayaran'] as $t) {
            $res = $koneksi->query("SELECT COUNT(*) AS total FROM `$t`");
            $realCounts[$t] = $res ? (int)$res->fetch_assoc()['total'] : 0;
        }

        $resSum = $koneksi->query("SELECT SUM(total_harga) AS grand_total FROM pesanan WHERE status_pesanan != 'Dibatalkan'");
        $realTotal = ($resSum && $row = $resSum->fetch_assoc()) ? (float)($row['grand_total'] ?? 0) : 0;

        $resRecent = $koneksi->query("SELECT p.id_pesanan, pl.nama_pelanggan, pr.nama_produk, p.tanggal_pesanan, p.total_harga, p.status_pesanan
            FROM pesanan p 
            JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
            JOIN produk pr ON p.id_produk = pr.id_produk 
            ORDER BY p.id_pesanan DESC LIMIT 5");
        $realRecent = [];
        if ($resRecent) {
            while ($r = $resRecent->fetch_assoc()) {
                $realRecent[] = $r;
            }
        }

        echo json_encode([
            'sukses' => true,
            'counts' => $realCounts,
            'total_penjualan' => $realTotal,
            'pesanan_terbaru' => $realRecent,
            'sumber' => 'database'
        ]);
        exit;
    } catch (Throwable $e) {}
}

echo json_encode([
    'sukses' => true,
    'counts' => $counts,
    'total_penjualan' => $totalPenjualan,
    'pesanan_terbaru' => $recent,
    'sumber' => 'fallback'
]);

