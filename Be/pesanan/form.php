<?php
require_once __DIR__ . '/../config/koneksi.php';
$isEdit = isset($_GET['id']);
$data = [];
if ($isEdit) {
 $id=(int)$_GET['id']; $stmt=$koneksi->prepare("SELECT * FROM pesanan WHERE id_pesanan=?"); $stmt->bind_param('i',$id); $stmt->execute();
 $data=$stmt->get_result()->fetch_assoc(); if(!$data) die('Data tidak ditemukan.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $values = [$_POST['id_pelanggan'] ?? '',$_POST['id_produk'] ?? '',$_POST['tanggal_pesanan'] ?? '',$_POST['total_harga'] ?? '',$_POST['status_pesanan'] ?? ''];
 if ($isEdit) {
  $id=(int)$_GET['id']; $stmt=$koneksi->prepare("UPDATE pesanan SET id_pelanggan=?,id_produk=?,tanggal_pesanan=?,total_harga=?,status_pesanan=? WHERE id_pesanan=?");
  $stmt->bind_param('iisdss', $values[0], $values[1], $values[2], $values[3], $values[4], $id);
 } else {
  $stmt=$koneksi->prepare("INSERT INTO pesanan (id_pelanggan,id_produk,tanggal_pesanan,total_harga,status_pesanan) VALUES (?,?,?,?,?)");
  $stmt->bind_param('iisds', $values[0], $values[1], $values[2], $values[3], $values[4]);
 }
 $stmt->execute(); $_SESSION['flash']='Data pesanan berhasil disimpan.'; header('Location: index.php'); exit;
}
$pageTitle = ($isEdit?'Edit ':'Tambah ').'Pesanan';
$basePrefix='../'; $assetPrefix='../';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><h2 class="page-heading"><?= $isEdit?'Edit':'Tambah' ?> Pesanan</h2></div>
<div class="card form-card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Pelanggan</label><select class="form-select" name="id_pelanggan" required><option value="">-- Pilih Pelanggan --</option><?php $opts=$koneksi->query("SELECT id_pelanggan,nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan"); while($o=$opts->fetch_assoc()): ?><option value="<?= $o['id_pelanggan'] ?>" <?= (string)($data['id_pelanggan'] ?? '')===(string)$o['id_pelanggan']?'selected':'' ?>><?= htmlspecialchars($o['nama_pelanggan']) ?></option><?php endwhile; ?></select></div>
<div class="mb-3"><label class="form-label">Produk</label><select class="form-select" name="id_produk" required><option value="">-- Pilih Produk --</option><?php $opts=$koneksi->query("SELECT id_produk,nama_produk FROM produk ORDER BY nama_produk"); while($o=$opts->fetch_assoc()): ?><option value="<?= $o['id_produk'] ?>" <?= (string)($data['id_produk'] ?? '')===(string)$o['id_produk']?'selected':'' ?>><?= htmlspecialchars($o['nama_produk']) ?></option><?php endwhile; ?></select></div>
<div class="mb-3"><label class="form-label">Tanggal Pesanan</label><input class="form-control" type="datetime-local" name="tanggal_pesanan" value="<?= !empty($data['tanggal_pesanan']) ? date('Y-m-d\TH:i', strtotime($data['tanggal_pesanan'])) : '' ?>" ></div>
<div class="mb-3"><label class="form-label">Total Harga</label><input class="form-control" type="number" name="total_harga" value="<?= htmlspecialchars((string)($data['total_harga'] ?? '')) ?>" required></div>
<div class="mb-3"><label class="form-label">Status Pesanan</label><select class="form-select" name="status_pesanan" ><option value="">-- Pilih Status Pesanan --</option><option value="Menunggu"><?= htmlspecialchars((string)($data['status_pesanan'] ?? ''))==="Menunggu"?"selected":"" ?></option><option value="Diproses"><?= htmlspecialchars((string)($data['status_pesanan'] ?? ''))==="Diproses"?"selected":"" ?></option><option value="Dikirim"><?= htmlspecialchars((string)($data['status_pesanan'] ?? ''))==="Dikirim"?"selected":"" ?></option><option value="Selesai"><?= htmlspecialchars((string)($data['status_pesanan'] ?? ''))==="Selesai"?"selected":"" ?></option><option value="Dibatalkan"><?= htmlspecialchars((string)($data['status_pesanan'] ?? ''))==="Dibatalkan"?"selected":"" ?></option></select></div>

<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan</button><a href="index.php" class="btn btn-light">Kembali</a></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
