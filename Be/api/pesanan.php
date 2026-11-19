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

$mockPesanan = [
    ['id_pesanan' => 101, 'id_pelanggan' => 1, 'nama_pelanggan' => 'Ahmad Fauzi', 'id_produk' => 1, 'nama_produk' => 'Mouse Wireless Ergonomis', 'tanggal_pesanan' => '2026-11-19 10:15:00', 'total_harga' => 85000, 'status_pesanan' => 'Selesai'],
    ['id_pesanan' => 102, 'id_pelanggan' => 2, 'nama_pelanggan' => 'Siti Nurhaliza', 'id_produk' => 2, 'nama_produk' => 'Mechanical Keyboard TKL Pastel', 'tanggal_pesanan' => '2026-11-19 11:30:00', 'total_harga' => 345000, 'status_pesanan' => 'Diproses'],
    ['id_pesanan' => 103, 'id_pelanggan' => 3, 'nama_pelanggan' => 'Budi Santoso', 'id_produk' => 3, 'nama_produk' => 'Desk Mat Anti-Slip Soft Blue', 'tanggal_pesanan' => '2026-11-19 13:00:00', 'total_harga' => 65000, 'status_pesanan' => 'Menunggu']
];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $status = $_GET['status'] ?? '';

    if ($dbConnected) {
        try {
            if ($id > 0) {
                $stmt = $koneksi->prepare("SELECT p.*, pl.nama_pelanggan, pl.email, pl.no_hp, pl.alamat, pr.nama_produk, pr.harga 
                    FROM pesanan p 
                    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                    JOIN produk pr ON p.id_produk = pr.id_produk 
                    WHERE p.id_pesanan = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $data = $stmt->get_result()->fetch_assoc();
                if ($data) {
                    echo json_encode(['sukses' => true, 'data' => $data]);
                } else {
                    echo json_encode(['sukses' => false, 'pesan' => 'Pesanan tidak ditemukan.']);
                }
            } else {
                $sql = "SELECT p.*, pl.nama_pelanggan, pr.nama_produk 
                    FROM pesanan p 
                    JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                    JOIN produk pr ON p.id_produk = pr.id_produk 
                    WHERE 1=1";
                if (!empty($status)) {
                    $sql .= " AND p.status_pesanan = '" . $koneksi->real_escape_string($status) . "'";
                }
                $sql .= " ORDER BY p.id_pesanan DESC";
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
        foreach ($mockPesanan as $p) {
            if ($p['id_pesanan'] === $id) { $found = $p; break; }
        }
        echo json_encode(['sukses' => (bool)$found, 'data' => $found, 'sumber' => 'fallback']);
    } else {
        $filtered = $mockPesanan;
        if (!empty($status)) {
            $filtered = array_values(array_filter($filtered, fn($p) => $p['status_pesanan'] === $status));
        }
        echo json_encode(['sukses' => true, 'data' => $filtered, 'sumber' => 'fallback']);
    }
    exit;
}

if ($method === 'POST') {
    $input = getJsonInput();

    // Update Status Pesanan Saja
    if ($action === 'update_status' || isset($input['action']) && $input['action'] === 'update_status') {
        $id = (int)($input['id_pesanan'] ?? $_GET['id'] ?? 0);
        $status_baru = trim($input['status_pesanan'] ?? '');

        if (!$id || empty($status_baru)) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID pesanan dan status baru wajib diisi.']);
            exit;
        }

        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Status pesanan berhasil diubah (mode simulasi).']);
            exit;
        }

        try {
            $stmt = $koneksi->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?");
            $stmt->bind_param('si', $status_baru, $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Status pesanan berhasil diperbarui.']);
        } catch (Throwable $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
        exit;
    }

    // Hapus Pesanan
    if ($action === 'delete' || isset($input['action']) && $input['action'] === 'delete') {
        $id = (int)($input['id_pesanan'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID pesanan tidak valid.']);
            exit;
        }
        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Pesanan berhasil dihapus (mode simulasi).']);
            exit;
        }
        try {
            $stmt = $koneksi->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Pesanan berhasil dihapus.']);
        } catch (Throwable $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Gagal menghapus pesanan: ' . $e->getMessage()]);
        }
        exit;
    }

    // Buat Pesanan Baru (Checkout)
    $id_pelanggan = (int)($input['id_pelanggan'] ?? 0);
    $id_produk = (int)($input['id_produk'] ?? 0);
    $total_harga = (float)($input['total_harga'] ?? 0);
    $status_pesanan = trim($input['status_pesanan'] ?? 'Menunggu');

    // Jika checkout publik mengirim data pelanggan langsung (nama, email, no_hp, alamat)
    if ($id_pelanggan === 0 && !empty($input['nama_pelanggan'])) {
        $nama = trim($input['nama_pelanggan']);
        $email = trim($input['email'] ?? '');
        $no_hp = trim($input['no_hp'] ?? '');
        $alamat = trim($input['alamat'] ?? '');

        if ($dbConnected) {
            try {
                $stmtPl = $koneksi->prepare("INSERT INTO pelanggan (nama_pelanggan, email, no_hp, alamat) VALUES (?, ?, ?, ?)");
                $stmtPl->bind_param('ssss', $nama, $email, $no_hp, $alamat);
                $stmtPl->execute();
                $id_pelanggan = $stmtPl->insert_id;
            } catch (Throwable $e) {
                // Ignore jika gagal insert pelanggan
            }
        } else {
            $id_pelanggan = 99;
        }
    }

    if (!$id_produk) {
        echo json_encode(['sukses' => false, 'pesan' => 'Produk pesanan wajib dipilih.']);
        exit;
    }

    if (!$dbConnected) {
        echo json_encode([
            'sukses' => true, 
            'pesan' => 'Pesanan berhasil dibuat (mode simulasi).', 
            'id_pesanan' => rand(200, 999),
            'id_pelanggan' => $id_pelanggan
        ]);
        exit;
    }

    try {
        $tanggal = date('Y-m-d H:i:s');
        $stmt = $koneksi->prepare("INSERT INTO pesanan (id_pelanggan, id_produk, tanggal_pesanan, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisds', $id_pelanggan, $id_produk, $tanggal, $total_harga, $status_pesanan);
        $stmt->execute();
        $id_pesanan = $stmt->insert_id;

        echo json_encode([
            'sukses' => true,
            'pesan' => 'Pesanan berhasil dibuat.',
            'id_pesanan' => $id_pesanan,
            'id_pelanggan' => $id_pelanggan
        ]);
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        echo json_encode(['sukses' => false, 'pesan' => 'ID pesanan tidak valid.']);
        exit;
    }
    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Pesanan berhasil dihapus (mode simulasi).']);
        exit;
    }
    try {
        $stmt = $koneksi->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['sukses' => true, 'pesan' => 'Pesanan berhasil dihapus.']);
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menghapus pesanan: ' . $e->getMessage()]);
    }
    exit;
}

