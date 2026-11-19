<?php
require_once __DIR__ . '/../config/koneksi.php';
$isEdit = isset($_GET['id']);
$data = [];
if ($isEdit) {
 $id=(int)$_GET['id']; $stmt=$koneksi->prepare("SELECT * FROM produk WHERE id_produk=?"); $stmt->bind_param('i',$id); $stmt->execute();
 $data=$stmt->get_result()->fetch_assoc(); if(!$data) die('Data tidak ditemukan.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $values = [$_POST['id_kategori'] ?? '',$_POST['nama_produk'] ?? '',$_POST['harga'] ?? '',$_POST['stok'] ?? ''];
 if ($isEdit) {
  $id=(int)$_GET['id']; $stmt=$koneksi->prepare("UPDATE produk SET id_kategori=?,nama_produk=?,harga=?,stok=? WHERE id_produk=?");
  $stmt->bind_param('isdis', $values[0], $values[1], $values[2], $values[3], $id);
 } else {
  $stmt=$koneksi->prepare("INSERT INTO produk (id_kategori,nama_produk,harga,stok) VALUES (?,?,?,?)");
  $stmt->bind_param('isdi', $values[0], $values[1], $values[2], $values[3]);
 }
 $stmt->execute(); $_SESSION['flash']='Data produk berhasil disimpan.'; header('Location: index.php'); exit;
}
$pageTitle = ($isEdit?'Edit ':'Tambah ').'Produk';
$basePrefix='../'; $assetPrefix='../';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><h2 class="page-heading"><?= $isEdit?'Edit':'Tambah' ?> Produk</h2></div>
<div class="card form-card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Kategori</label><select class="form-select" name="id_kategori" ><option value="">-- Pilih Kategori --</option><?php $opts=$koneksi->query("SELECT id_kategori,nama_kategori FROM kategori ORDER BY nama_kategori"); while($o=$opts->fetch_assoc()): ?><option value="<?= $o['id_kategori'] ?>" <?= (string)($data['id_kategori'] ?? '')===(string)$o['id_kategori']?'selected':'' ?>><?= htmlspecialchars($o['nama_kategori']) ?></option><?php endwhile; ?></select></div>
<div class="mb-3"><label class="form-label">Nama Produk</label><input class="form-control" type="text" name="nama_produk" value="<?= htmlspecialchars((string)($data['nama_produk'] ?? '')) ?>" required></div>
<div class="mb-3"><label class="form-label">Harga</label><input class="form-control" type="number" name="harga" value="<?= htmlspecialchars((string)($data['harga'] ?? '')) ?>" ></div>
<div class="mb-3"><label class="form-label">Stok</label><input class="form-control" type="number" name="stok" value="<?= htmlspecialchars((string)($data['stok'] ?? '')) ?>" ></div>

<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan</button><a href="index.php" class="btn btn-light">Kembali</a></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
