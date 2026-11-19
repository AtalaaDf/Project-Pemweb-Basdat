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

$mockKategori = [
    ['id_kategori' => 1, 'nama_kategori' => 'Elektronik', 'deskripsi' => 'Perangkat elektronik dan gadget modern'],
    ['id_kategori' => 2, 'nama_kategori' => 'Aksesori', 'deskripsi' => 'Aksesori komputer dan perlengkapan meja kerja'],
    ['id_kategori' => 3, 'nama_kategori' => 'Audio', 'deskripsi' => 'Perangkat dengar dan mikrofon berkualitas'],
    ['id_kategori' => 4, 'nama_kategori' => 'Penyimpanan', 'deskripsi' => 'Flashdisk, harddisk eksternal, dan kartu memori']
];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($dbConnected) {
        try {
            if ($id > 0) {
                $stmt = $koneksi->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $data = $stmt->get_result()->fetch_assoc();
                if ($data) {
                    echo json_encode(['sukses' => true, 'data' => $data]);
                } else {
                    echo json_encode(['sukses' => false, 'pesan' => 'Kategori tidak ditemukan.']);
                }
            } else {
                $res = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
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
        foreach ($mockKategori as $k) {
            if ($k['id_kategori'] === $id) { $found = $k; break; }
        }
        echo json_encode(['sukses' => (bool)$found, 'data' => $found, 'sumber' => 'fallback']);
    } else {
        echo json_encode(['sukses' => true, 'data' => $mockKategori, 'sumber' => 'fallback']);
    }
    exit;
}

if ($method === 'POST') {
    $input = getJsonInput();

    // Hapus
    if ($action === 'delete' || isset($input['action']) && $input['action'] === 'delete') {
        $id = (int)($input['id_kategori'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID tidak valid.']);
            exit;
        }
        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil dihapus (mode simulasi).']);
            exit;
        }
        try {
            $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id_kategori = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil dihapus.']);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Kategori tidak dapat dihapus: ' . $e->getMessage()]);
        }
        exit;
    }

    // Tambah / Update
    $id = isset($input['id_kategori']) ? (int)$input['id_kategori'] : 0;
    $nama = trim($input['nama_kategori'] ?? '');
    $deskripsi = trim($input['deskripsi'] ?? '');

    if (empty($nama)) {
        echo json_encode(['sukses' => false, 'pesan' => 'Nama kategori wajib diisi.']);
        exit;
    }

    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil disimpan (mode simulasi).']);
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?");
            $stmt->bind_param('ssi', $nama, $deskripsi, $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil diperbarui.']);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
            $stmt->bind_param('ss', $nama, $deskripsi);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Kategori baru berhasil ditambahkan.', 'id_kategori' => $stmt->insert_id]);
        }
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menyimpan kategori: ' . $e->getMessage()]);
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
        echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil dihapus (mode simulasi).']);
        exit;
    }
    try {
        $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['sukses' => true, 'pesan' => 'Kategori berhasil dihapus.']);
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menghapus kategori: ' . $e->getMessage()]);
    }
    exit;
}

