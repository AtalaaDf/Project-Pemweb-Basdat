<?php
require_once __DIR__ . '/../config/koneksi.php';
$pageTitle = 'Pembayaran';
$basePrefix = '../'; $assetPrefix = '../';
$data = $koneksi->query("SELECT b.*, p.id_pesanan, pl.nama_pelanggan FROM pembayaran b JOIN pesanan p ON b.id_pesanan=p.id_pesanan JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan ORDER BY b.id_pembayaran DESC");
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
 <div><h2 class="page-heading mb-1">Data Pembayaran</h2><div class="text-secondary">Kelola data pembayaran.</div></div>
 <a href="tambah.php" class="btn btn-primary">+ Tambah Pembayaran</a>
</div>
<div class="card p-3"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>id_pembayaran</th><th>Pesanan</th><th>Tanggal Bayar</th><th>Jumlah Bayar</th><th>Metode Pembayaran</th><th>Aksi</th></tr></thead><tbody>
<?php if($data->num_rows): while($row=$data->fetch_assoc()): ?>
<tr><td><?= (int)$row['id_pembayaran'] ?></td><td><?= htmlspecialchars((string)($row['id_pesanan'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['tanggal_bayar'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['jumlah_bayar'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['metode_bayar'] ?? '')) ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$row['id_pembayaran'] ?>">Edit</a>
<a class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?" href="hapus.php?id=<?= (int)$row['id_pembayaran'] ?>">Hapus</a>
</td></tr>
<?php endwhile; else: ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
