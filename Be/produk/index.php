<?php
require_once __DIR__ . '/../config/koneksi.php';
$pageTitle = 'Produk';
$basePrefix = '../'; $assetPrefix = '../';
$data = $koneksi->query("SELECT pr.*, k.nama_kategori FROM produk pr LEFT JOIN kategori k ON pr.id_kategori=k.id_kategori ORDER BY pr.id_produk DESC");
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
 <div><h2 class="page-heading mb-1">Data Produk</h2><div class="text-secondary">Kelola data produk.</div></div>
 <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
</div>
<div class="card p-3"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>id_produk</th><th>Kategori</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody>
<?php if($data->num_rows): while($row=$data->fetch_assoc()): ?>
<tr><td><?= (int)$row['id_produk'] ?></td><td><?= htmlspecialchars((string)($row['nama_kategori'] ?? '-')) ?></td><td><?= htmlspecialchars((string)($row['nama_produk'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['harga'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['stok'] ?? '')) ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$row['id_produk'] ?>">Edit</a>
<a class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?" href="hapus.php?id=<?= (int)$row['id_produk'] ?>">Hapus</a>
</td></tr>
<?php endwhile; else: ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
