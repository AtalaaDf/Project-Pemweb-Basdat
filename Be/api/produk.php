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

// Fallback Mock Produk jika DB belum di-import
$mockProduk = [
    ['id_produk' => 1, 'id_kategori' => 1, 'nama_kategori' => 'Elektronik', 'nama_produk' => 'Mouse Wireless Ergonomis', 'harga' => 85000, 'stok' => 20, 'deskripsi' => 'Mouse wireless 2.4GHz dengan desain ergonomis dan silent click.'],
    ['id_produk' => 2, 'id_kategori' => 1, 'nama_kategori' => 'Elektronik', 'nama_produk' => 'Mechanical Keyboard TKL Pastel', 'harga' => 345000, 'stok' => 12, 'deskripsi' => 'Keyboard mekanikal 87 tombol dengan switch linier lembut dan keycaps pastel.'],
    ['id_produk' => 3, 'id_kategori' => 2, 'nama_kategori' => 'Aksesori', 'nama_produk' => 'Desk Mat Anti-Slip Soft Blue', 'harga' => 65000, 'stok' => 25, 'deskripsi' => 'Alas meja kerja kulit sintetis premium ukuran 80x40 cm anti-air.'],
    ['id_produk' => 4, 'id_kategori' => 2, 'nama_kategori' => 'Aksesori', 'nama_produk' => 'USB Hub Type-C 4-in-1 Aluminum', 'harga' => 120000, 'stok' => 15, 'deskripsi' => 'Adapter multiport Type-C dengan 3 port USB 3.0 dan 1 port HDMI 4K.'],
    ['id_produk' => 5, 'id_kategori' => 1, 'nama_kategori' => 'Elektronik', 'nama_produk' => 'TWS Bluetooth Earbuds Minimalis', 'harga' => 189000, 'stok' => 18, 'deskripsi' => 'Earbuds nirkabel dengan suara jernih, latensi rendah, dan daya tahan baterai 24 jam.'],
    ['id_produk' => 6, 'id_kategori' => 2, 'nama_kategori' => 'Aksesori', 'nama_produk' => 'Stand Laptop Lipat Aluminium', 'harga' => 95000, 'stok' => 30, 'deskripsi' => 'Dudukan laptop portabel dapat disetel 6 tingkat ketinggian untuk postur nyaman.']
];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $kategori = $_GET['kategori'] ?? '';
    $cari = $_GET['cari'] ?? '';

    if ($dbConnected) {
        try {
            if ($id > 0) {
                $stmt = $koneksi->prepare("SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id_kategori WHERE p.id_produk = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $res = $stmt->get_result();
                $data = $res->fetch_assoc();
                if ($data) {
                    echo json_encode(['sukses' => true, 'data' => $data]);
                } else {
                    echo json_encode(['sukses' => false, 'pesan' => 'Produk tidak ditemukan.']);
                }
            } else {
                $sql = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id_kategori WHERE 1=1";
                $params = [];
                $types = '';

                if (!empty($kategori)) {
                    $sql .= " AND k.nama_kategori = ?";
                    $params[] = $kategori;
                    $types .= 's';
                }
                if (!empty($cari)) {
                    $sql .= " AND (p.nama_produk LIKE ? OR k.nama_kategori LIKE ?)";
                    $like = "%$cari%";
                    $params[] = $like;
                    $params[] = $like;
                    $types .= 'ss';
                }

                $sql .= " ORDER BY p.id_produk DESC";
                $stmt = $koneksi->prepare($sql);
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $res = $stmt->get_result();
                $items = [];
                while ($r = $res->fetch_assoc()) {
                    $items[] = $r;
                }
                echo json_encode(['sukses' => true, 'data' => $items, 'sumber' => 'database']);
            }
            exit;
        } catch (Throwable $e) {
            // Gunakan fallback jika error query
        }
    }

    // Fallback response jika DB belum diimport
    if ($id > 0) {
        $found = null;
        foreach ($mockProduk as $p) {
            if ($p['id_produk'] === $id) { $found = $p; break; }
        }
        if ($found) {
            echo json_encode(['sukses' => true, 'data' => $found, 'sumber' => 'fallback']);
        } else {
            echo json_encode(['sukses' => false, 'pesan' => 'Produk tidak ditemukan']);
        }
    } else {
        $filtered = $mockProduk;
        if (!empty($kategori)) {
            $filtered = array_values(array_filter($filtered, fn($p) => $p['nama_kategori'] === $kategori));
        }
        if (!empty($cari)) {
            $filtered = array_values(array_filter($filtered, fn($p) => stripos($p['nama_produk'], $cari) !== false));
        }
        echo json_encode(['sukses' => true, 'data' => $filtered, 'sumber' => 'fallback']);
    }
    exit;
}

if ($method === 'POST') {
    $input = getJsonInput();

    // Hapus Produk
    if ($action === 'delete' || isset($input['action']) && $input['action'] === 'delete') {
        $id = (int)($input['id_produk'] ?? $_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['sukses' => false, 'pesan' => 'ID produk tidak valid.']);
            exit;
        }

        if (!$dbConnected) {
            echo json_encode(['sukses' => true, 'pesan' => 'Produk simulasi berhasil dihapus.']);
            exit;
        }

        try {
            $stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Produk berhasil dihapus.']);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['sukses' => false, 'pesan' => 'Produk tidak dapat dihapus karena sudah memiliki riwayat pesanan.']);
        }
        exit;
    }

    // Tambah atau Update Produk
    $id = isset($input['id_produk']) ? (int)$input['id_produk'] : 0;
    $id_kategori = !empty($input['id_kategori']) ? (int)$input['id_kategori'] : null;
    $nama_produk = trim($input['nama_produk'] ?? '');
    $harga = (float)($input['harga'] ?? 0);
    $stok = (int)($input['stok'] ?? 0);

    if (empty($nama_produk)) {
        echo json_encode(['sukses' => false, 'pesan' => 'Nama produk wajib diisi.']);
        exit;
    }

    if (!$dbConnected) {
        echo json_encode(['sukses' => true, 'pesan' => 'Data produk berhasil disimpan (mode simulasi).']);
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $koneksi->prepare("UPDATE produk SET id_kategori = ?, nama_produk = ?, harga = ?, stok = ? WHERE id_produk = ?");
            $stmt->bind_param('isdii', $id_kategori, $nama_produk, $harga, $stok, $id);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Produk berhasil diperbarui.']);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO produk (id_kategori, nama_produk, harga, stok) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isdi', $id_kategori, $nama_produk, $harga, $stok);
            $stmt->execute();
            echo json_encode(['sukses' => true, 'pesan' => 'Produk baru berhasil ditambahkan.', 'id_produk' => $stmt->insert_id]);
        }
    } catch (Throwable $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Gagal menyimpan produk: ' . $e->getMessage()]);
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
        echo json_encode(['sukses' => true, 'pesan' => 'Produk simulasi berhasil dihapus.']);
        exit;
    }
    try {
        $stmt = $koneksi->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['sukses' => true, 'pesan' => 'Produk berhasil dihapus.']);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['sukses' => false, 'pesan' => 'Produk tidak dapat dihapus karena tercatat dalam pesanan.']);
    }
    exit;
}

