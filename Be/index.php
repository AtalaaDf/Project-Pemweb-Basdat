<?php
require_once __DIR__ . '/config/koneksi.php';
$pageTitle = 'Dashboard';
$assetPrefix = $basePrefix = '';
$counts = [];
foreach (['pelanggan','kategori','produk','pesanan','pembayaran'] as $t) {
    $result = $koneksi->query("SELECT COUNT(*) AS total FROM `$t`");
    $counts[$t] = $result->fetch_assoc()['total'];
}
$recent = $koneksi->query("SELECT p.id_pesanan, pl.nama_pelanggan, pr.nama_produk, p.tanggal_pesanan, p.total_harga, p.status_pesanan
FROM pesanan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan
JOIN produk pr ON p.id_produk=pr.id_produk ORDER BY p.id_pesanan DESC LIMIT 5");
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
 <div><h2 class="page-heading mb-1">Dashboard</h2><div class="text-secondary">Ringkasan sistem informasi penjualan online.</div></div>
 <a class="btn btn-primary" href="pesanan/tambah.php">+ Buat Pesanan</a>
</div>
<div class="row g-3 mb-4">
<?php $labels=['pelanggan'=>'Pelanggan','kategori'=>'Kategori','produk'=>'Produk','pesanan'=>'Pesanan','pembayaran'=>'Pembayaran']; foreach($labels as $key=>$label): ?>
<div class="col-sm-6 col-xl">
 <div class="card card-stat p-3 h-100"><div class="text-secondary"><?= $label ?></div><div class="display-6 fw-semibold"><?= $counts[$key] ?></div><a class="small text-decoration-none" href="<?= $key ?>/index.php">Kelola data →</a></div>
</div>
<?php endforeach; ?>
</div>
<div class="card p-3">
 <h5 class="mb-3">Pesanan Terbaru</h5>
 <div class="table-responsive"><table class="table align-middle">
 <thead><tr><th>ID</th><th>Pelanggan</th><th>Produk</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead><tbody>
 <?php if($recent->num_rows): while($r=$recent->fetch_assoc()): ?>
 <tr><td>#<?= $r['id_pesanan'] ?></td><td><?= htmlspecialchars($r['nama_pelanggan']) ?></td><td><?= htmlspecialchars($r['nama_produk']) ?></td><td><?= htmlspecialchars($r['tanggal_pesanan']) ?></td><td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td><td><span class="badge text-bg-light"><?= htmlspecialchars($r['status_pesanan']) ?></span></td></tr>
 <?php endwhile; else: ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada pesanan.</td></tr><?php endif; ?>
 </tbody></table></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
