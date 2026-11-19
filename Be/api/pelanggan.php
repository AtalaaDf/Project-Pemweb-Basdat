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

$mockPelanggan = [
    ['id_pelanggan' => 1, 'nama_pelanggan' => 'Ahmad Fauzi', 'email' => 'fauzi@example.com', 'no_hp' => '081234567890', 'alamat' => 'Jl. Merdeka No. 45, Jakarta'],
    ['id_pelanggan' => 2, 'nama_pelanggan' => 'Siti Nurhaliza', 'email' => 'siti@example.com', 'no_hp' => '085678901234', 'alamat' => 'Jl. Diponegoro No. 12, Bandung'],
    ['id_pelanggan' => 3, 'nama_pelanggan' => 'Budi Santoso', 'email' => 'budi@example.com', 'no_hp' => '087890123456', 'alamat' => 'Jl. Sudirman No. 88, Surabaya']
];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($dbConnected) {
        try {
            if ($id > 0) {
                $stmt = $koneksi->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $data = $stmt->get_result()->fetch_assoc();
                if ($data) {
                    echo json_encode(['sukses' => true, 'data' => $data]);
                } else {
                    echo json_encode(['sukses' => false, 'pesan' => 'Pelanggan tidak ditemukan.']);
                }
            } else {
                $res = $koneksi->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
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
        foreach ($mockPelanggan as $p) {
            if ($p['id_pelanggan'] === $id) { $found = $p; break; }
        }
        echo json_encode(['sukses' => (bool)$found, 'data' => $found, 'sumber' => 'fallback']);
    } else {
        echo json_encode(['sukses' => true, 'data' => $mockPelanggan, 'sumber' => 'fallback']);
    }
    exit;
}

if ($method === 'POST') {
    $input = getJsonInput();

    // Hapus
    if ($action === 'delete' || isset($input['action']) && $input['action'] === 'delete') {
        $id = (int)($input['id_pelanggan'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID tidak valid.']);
            exit;
        }
        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Pelanggan berhasil dihapus (mode simulasi).']);
            exit;
        }
        try {
            $stmt = $koneksi->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Data pelanggan berhasil dihapus.']);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi pesanan.']);
        }
        exit;
    }

    // Tambah / Update
    $id = isset($input['id_pelanggan']) ? (int)$input['id_pelanggan'] : 0;
    $nama = trim($input['nama_pelanggan'] ?? '');
    $email = trim($input['email'] ?? '');
    $no_hp = trim($input['no_hp'] ?? '');
    $alamat = trim($input['alamat'] ?? '');

    if (empty($nama)) {
        echo json_encode(['sukses' => false, 'pesan' => 'Nama pelanggan wajib diisi.']);
        exit;
    }

    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Data pelanggan berhasil disimpan (mode simulasi).', 'id_pelanggan' => 99]);
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $koneksi->prepare("UPDATE pelanggan SET nama_pelanggan = ?, email = ?, no_hp = ?, alamat = ? WHERE id_pelanggan = ?");
            $stmt->bind_param('ssssi', $nama, $email, $no_hp, $alamat, $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Data pelanggan berhasil diperbarui.']);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO pelanggan (nama_pelanggan, email, no_hp, alamat) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $nama, $email, $no_hp, $alamat);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Pelanggan baru berhasil ditambahkan.', 'id_pelanggan' => $stmt->insert_id]);
        }
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menyimpan data pelanggan: ' . $e->getMessage()]);
    }
    exit;
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) {
        echo json_encode(['sukses' => false, 'pesan' => 'ID tidak valid.']);
        exit;
    }
    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Pelanggan berhasil dihapus (mode simulasi).']);
        exit;
    }
    try {
        $stmt = $koneksi->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['sukses' => true, 'pesan' => 'Data pelanggan berhasil dihapus.']);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi pesanan.']);
    }
    exit;
}

