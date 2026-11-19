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

$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$mockLaporan = [
    'ringkasan' => [
        'total_transaksi' => 3,
        'total_pendapatan' => 495000,
        'rata_rata' => 165000,
        'transaksi_selesai' => 1,
        'transaksi_proses' => 2
    ],
    'transaksi' => [
        ['id_pesanan' => 101, 'nama_pelanggan' => 'Ahmad Fauzi', 'nama_produk' => 'Mouse Wireless Ergonomis', 'tanggal_pesanan' => '2026-11-19 10:15:00', 'total_harga' => 85000, 'status_pesanan' => 'Selesai', 'metode_bayar' => 'QRIS'],
        ['id_pesanan' => 102, 'nama_pelanggan' => 'Siti Nurhaliza', 'nama_produk' => 'Mechanical Keyboard TKL Pastel', 'tanggal_pesanan' => '2026-11-19 11:30:00', 'total_harga' => 345000, 'status_pesanan' => 'Diproses', 'metode_bayar' => 'Transfer Bank BCA'],
        ['id_pesanan' => 103, 'nama_pelanggan' => 'Budi Santoso', 'nama_produk' => 'Desk Mat Anti-Slip Soft Blue', 'tanggal_pesanan' => '2026-11-19 13:00:00', 'total_harga' => 65000, 'status_pesanan' => 'Menunggu', 'metode_bayar' => '-']
    ]
];

if ($dbConnected) {
    try {
        $where = "WHERE 1=1";
        if (!empty($dari)) {
            $where .= " AND p.tanggal_pesanan >= '" . $koneksi->real_escape_string($dari) . " 00:00:00'";
        }
        if (!empty($sampai)) {
            $where .= " AND p.tanggal_pesanan <= '" . $koneksi->real_escape_string($sampai) . " 23:59:59'";
        }

        $sql = "SELECT p.*, pl.nama_pelanggan, pr.nama_produk, pb.metode_bayar, pb.tanggal_bayar 
            FROM pesanan p
            JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
            JOIN produk pr ON p.id_produk = pr.id_produk
            LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
            $where
            ORDER BY p.id_pesanan DESC";
        $res = $koneksi->query($sql);
        $items = [];
        $totalRev = 0;
        $countSelesai = 0;
        $countProses = 0;

        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $items[] = $r;
                if ($r['status_pesanan'] !== 'Dibatalkan') {
                    $totalRev += (float)$r['total_harga'];
                }
                if ($r['status_pesanan'] === 'Selesai') {
                    $countSelesai++;
                } else {
                    $countProses++;
                }
            }
        }

        $totalTrans = count($items);
        $avg = $totalTrans > 0 ? ($totalRev / $totalTrans) : 0;

        echo json_encode([
            'sukses' => true,
            'ringkasan' => [
                'total_transaksi' => $totalTrans,
                'total_pendapatan' => $totalRev,
                'rata_rata' => $avg,
                'transaksi_selesai' => $countSelesai,
                'transaksi_proses' => $countProses
            ],
            'transaksi' => $items,
            'sumber' => 'database'
        ]);
        exit;
    } catch (Throwable $e) {}
}

echo json_encode([
    'sukses' => true,
    'ringkasan' => $mockLaporan['ringkasan'],
    'transaksi' => $mockLaporan['transaksi'],
    'sumber' => 'fallback'
]);

