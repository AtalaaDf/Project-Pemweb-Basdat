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

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$mockPembayaran = [
    ['id_pembayaran' => 1, 'id_pesanan' => 101, 'nama_pelanggan' => 'Ahmad Fauzi', 'nama_produk' => 'Mouse Wireless Ergonomis', 'tanggal_bayar' => '2026-11-19 10:20:00', 'jumlah_bayar' => 85000, 'metode_bayar' => 'QRIS'],
    ['id_pembayaran' => 2, 'id_pesanan' => 102, 'nama_pelanggan' => 'Siti Nurhaliza', 'nama_produk' => 'Mechanical Keyboard TKL Pastel', 'tanggal_bayar' => '2026-11-19 11:45:00', 'jumlah_bayar' => 345000, 'metode_bayar' => 'Transfer Bank BCA']
];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($dbConnected) {
        try {
            if ($id > 0) {
                $stmt = $koneksi->prepare("SELECT pb.*, ps.id_pelanggan, pl.nama_pelanggan, pr.nama_produk, ps.total_harga 
                    FROM pembayaran pb
                    JOIN pesanan ps ON pb.id_pesanan = ps.id_pesanan
                    JOIN pelanggan pl ON ps.id_pelanggan = pl.id_pelanggan
                    JOIN produk pr ON ps.id_produk = pr.id_produk
                    WHERE pb.id_pembayaran = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $data = $stmt->get_result()->fetch_assoc();
                if ($data) {
                    echo json_encode(['sukses' => true, 'data' => $data]);
                } else {
                    echo json_encode(['sukses' => false, 'pesan' => 'Data pembayaran tidak ditemukan.']);
                }
            } else {
                $sql = "SELECT pb.*, pl.nama_pelanggan, pr.nama_produk, ps.total_harga 
                    FROM pembayaran pb
                    JOIN pesanan ps ON pb.id_pesanan = ps.id_pesanan
                    JOIN pelanggan pl ON ps.id_pelanggan = pl.id_pelanggan
                    JOIN produk pr ON ps.id_produk = pr.id_produk
                    ORDER BY pb.id_pembayaran DESC";
                $res = $koneksi->query($sql);
                $items = [];
                while ($r = $res->fetch_assoc()) {
                    $items[] = $r;
                }
                echo json_encode(['sukses' => true, 'data' => $items, 'sumber' => 'database']);
            }
            exit;
        } catch (Throwable $e) {}
    }

    // Fallback
    if ($id > 0) {
        $found = null;
        foreach ($mockPembayaran as $p) {
            if ($p['id_pembayaran'] === $id) { $found = $p; break; }
        }
        echo json_encode(['sukses' => (bool)$found, 'data' => $found, 'sumber' => 'fallback']);
    } else {
        echo json_encode(['sukses' => true, 'data' => $mockPembayaran, 'sumber' => 'fallback']);
    }
    exit;
}

if ($method === 'POST') {
    $input = getJsonInput();

    // Hapus
    if ($action === 'delete' || isset($input['action']) && $input['action'] === 'delete') {
        $id = (int)($input['id_pembayaran'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID tidak valid.']);
            exit;
        }
        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Data pembayaran berhasil dihapus (mode simulasi).']);
            exit;
        }
        try {
            $stmt = $koneksi->prepare("DELETE FROM pembayaran WHERE id_pembayaran = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Data pembayaran berhasil dihapus.']);
        } catch (Throwable $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Gagal menghapus pembayaran: ' . $e->getMessage()]);
        }
        exit;
    }

    // Tambah / Update
    $id = isset($input['id_pembayaran']) ? (int)$input['id_pembayaran'] : 0;
    $id_pesanan = (int)($input['id_pesanan'] ?? 0);
    $tanggal_bayar = !empty($input['tanggal_bayar']) ? $input['tanggal_bayar'] : date('Y-m-d H:i:s');
    $jumlah_bayar = (float)($input['jumlah_bayar'] ?? 0);
    $metode_bayar = trim($input['metode_bayar'] ?? 'Transfer Bank');

    if (!$id_pesanan) {
        echo json_encode(['sukses' => false, 'pesan' => 'ID pesanan wajib dipilih.']);
        exit;
    }

    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Pembayaran berhasil dicatat (mode simulasi).', 'id_pembayaran' => 88]);
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $koneksi->prepare("UPDATE pembayaran SET id_pesanan = ?, tanggal_bayar = ?, jumlah_bayar = ?, metode_bayar = ? WHERE id_pembayaran = ?");
            $stmt->bind_param('isdsi', $id_pesanan, $tanggal_bayar, $jumlah_bayar, $metode_bayar, $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Data pembayaran berhasil diperbarui.']);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO pembayaran (id_pesanan, tanggal_bayar, jumlah_bayar, metode_bayar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isds', $id_pesanan, $tanggal_bayar, $jumlah_bayar, $metode_bayar);
            $stmt->execute();
            
            // Otomatis ubah status pesanan menjadi Selesai jika pembayaran dicatat
            $koneksi->query("UPDATE pesanan SET status_pesanan = 'Selesai' WHERE id_pesanan = $id_pesanan");

            echo json_encode(['sukses' => true, 'pesan' => 'Pembayaran berhasil dicatat.', 'id_pembayaran' => $stmt->insert_id]);
        }
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal mencatat pembayaran: ' . $e->getMessage()]);
    }
    exit;
}

