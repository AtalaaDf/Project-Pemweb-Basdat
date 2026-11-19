<?php
require_once __DIR__ . '/../config/koneksi.php';
$isEdit = isset($_GET['id']);
$data = [];
if ($isEdit) {
 $id=(int)$_GET['id']; $stmt=$koneksi->prepare("SELECT * FROM pembayaran WHERE id_pembayaran=?"); $stmt->bind_param('i',$id); $stmt->execute();
 $data=$stmt->get_result()->fetch_assoc(); if(!$data) die('Data tidak ditemukan.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $values = [$_POST['id_pesanan'] ?? '',$_POST['tanggal_bayar'] ?? '',$_POST['jumlah_bayar'] ?? '',$_POST['metode_bayar'] ?? ''];
 if ($isEdit) {
  $id=(int)$_GET['id']; $stmt=$koneksi->prepare("UPDATE pembayaran SET id_pesanan=?,tanggal_bayar=?,jumlah_bayar=?,metode_bayar=? WHERE id_pembayaran=?");
  $stmt->bind_param('isdss', $values[0], $values[1], $values[2], $values[3], $id);
 } else {
  $stmt=$koneksi->prepare("INSERT INTO pembayaran (id_pesanan,tanggal_bayar,jumlah_bayar,metode_bayar) VALUES (?,?,?,?)");
  $stmt->bind_param('isds', $values[0], $values[1], $values[2], $values[3]);
 }
 $stmt->execute(); $_SESSION['flash']='Data pembayaran berhasil disimpan.'; header('Location: index.php'); exit;
}
$pageTitle = ($isEdit?'Edit ':'Tambah ').'Pembayaran';
$basePrefix='../'; $assetPrefix='../';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><h2 class="page-heading"><?= $isEdit?'Edit':'Tambah' ?> Pembayaran</h2></div>
<div class="card form-card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Pesanan</label><select class="form-select" name="id_pesanan" required><option value="">-- Pilih Pesanan --</option><?php $opts=$koneksi->query("SELECT p.id_pesanan,pl.nama_pelanggan,pr.nama_produk FROM pesanan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan JOIN produk pr ON p.id_produk=pr.id_produk ORDER BY p.id_pesanan DESC"); while($o=$opts->fetch_assoc()): ?><option value="<?= $o['id_pesanan'] ?>" <?= (string)($data['id_pesanan'] ?? '')===(string)$o['id_pesanan']?'selected':'' ?>>#<?= $o['id_pesanan'] ?> - <?= htmlspecialchars($o['nama_pelanggan'].' / '.$o['nama_produk']) ?></option><?php endwhile; ?></select></div>
<div class="mb-3"><label class="form-label">Tanggal Bayar</label><input class="form-control" type="datetime-local" name="tanggal_bayar" value="<?= !empty($data['tanggal_bayar']) ? date('Y-m-d\TH:i', strtotime($data['tanggal_bayar'])) : '' ?>" ></div>
<div class="mb-3"><label class="form-label">Jumlah Bayar</label><input class="form-control" type="number" name="jumlah_bayar" value="<?= htmlspecialchars((string)($data['jumlah_bayar'] ?? '')) ?>" required></div>
<div class="mb-3"><label class="form-label">Metode Pembayaran</label><input class="form-control" type="text" name="metode_bayar" value="<?= htmlspecialchars((string)($data['metode_bayar'] ?? '')) ?>" ></div>

<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan</button><a href="index.php" class="btn btn-light">Kembali</a></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
