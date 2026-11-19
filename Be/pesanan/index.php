<?php
require_once __DIR__ . '/../config/koneksi.php';
$pageTitle = 'Pesanan';
$basePrefix = '../'; $assetPrefix = '../';
$data = $koneksi->query("SELECT p.*, pl.nama_pelanggan, pr.nama_produk FROM pesanan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan JOIN produk pr ON p.id_produk=pr.id_produk ORDER BY p.id_pesanan DESC");
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
 <div><h2 class="page-heading mb-1">Data Pesanan</h2><div class="text-secondary">Kelola data pesanan.</div></div>
 <a href="tambah.php" class="btn btn-primary">+ Tambah Pesanan</a>
</div>
<div class="card p-3"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>id_pesanan</th><th>Pelanggan</th><th>Produk</th><th>Tanggal Pesanan</th><th>Total Harga</th><th>Status Pesanan</th><th>Aksi</th></tr></thead><tbody>
<?php if($data->num_rows): while($row=$data->fetch_assoc()): ?>
<tr><td><?= (int)$row['id_pesanan'] ?></td><td><?= htmlspecialchars((string)($row['nama_pelanggan'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['nama_produk'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['tanggal_pesanan'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['total_harga'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['status_pesanan'] ?? '')) ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$row['id_pesanan'] ?>">Edit</a>
<a class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?" href="hapus.php?id=<?= (int)$row['id_pesanan'] ?>">Hapus</a>
</td></tr>
<?php endwhile; else: ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
