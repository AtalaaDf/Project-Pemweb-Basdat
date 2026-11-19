<?php
require_once __DIR__ . '/../config/koneksi.php';
$pageTitle = 'Pelanggan';
$basePrefix = '../'; $assetPrefix = '../';
$data = $koneksi->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
 <div><h2 class="page-heading mb-1">Data Pelanggan</h2><div class="text-secondary">Kelola data pelanggan.</div></div>
 <a href="tambah.php" class="btn btn-primary">+ Tambah Pelanggan</a>
</div>
<div class="card p-3"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>id_pelanggan</th><th>Nama Pelanggan</th><th>Email</th><th>Nomor HP</th><th>Alamat</th><th>Aksi</th></tr></thead><tbody>
<?php if($data->num_rows): while($row=$data->fetch_assoc()): ?>
<tr><td><?= (int)$row['id_pelanggan'] ?></td><td><?= htmlspecialchars((string)($row['nama_pelanggan'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['email'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['no_hp'] ?? '')) ?></td><td><?= htmlspecialchars((string)($row['alamat'] ?? '')) ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= (int)$row['id_pelanggan'] ?>">Edit</a>
<a class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?" href="hapus.php?id=<?= (int)$row['id_pelanggan'] ?>">Hapus</a>
</td></tr>
<?php endwhile; else: ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
