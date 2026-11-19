<?php
require_once __DIR__ . '/../config/koneksi.php';
$isEdit = isset($_GET['id']);
$data = [];
if ($isEdit) {
 $id=(int)$_GET['id']; $stmt=$koneksi->prepare("SELECT * FROM kategori WHERE id_kategori=?"); $stmt->bind_param('i',$id); $stmt->execute();
 $data=$stmt->get_result()->fetch_assoc(); if(!$data) die('Data tidak ditemukan.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $values = [$_POST['nama_kategori'] ?? '',$_POST['deskripsi'] ?? ''];
 if ($isEdit) {
  $id=(int)$_GET['id']; $stmt=$koneksi->prepare("UPDATE kategori SET nama_kategori=?,deskripsi=? WHERE id_kategori=?");
  $stmt->bind_param('sss', $values[0], $values[1], $id);
 } else {
  $stmt=$koneksi->prepare("INSERT INTO kategori (nama_kategori,deskripsi) VALUES (?,?)");
  $stmt->bind_param('ss', $values[0], $values[1]);
 }
 $stmt->execute(); $_SESSION['flash']='Data kategori berhasil disimpan.'; header('Location: index.php'); exit;
}
$pageTitle = ($isEdit?'Edit ':'Tambah ').'Kategori';
$basePrefix='../'; $assetPrefix='../';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><h2 class="page-heading"><?= $isEdit?'Edit':'Tambah' ?> Kategori</h2></div>
<div class="card form-card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Nama Kategori</label><input class="form-control" type="text" name="nama_kategori" value="<?= htmlspecialchars((string)($data['nama_kategori'] ?? '')) ?>" required></div>
<div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($data['+repr(n)+'] ?? '') ?></textarea></div>

<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan</button><a href="index.php" class="btn btn-light">Kembali</a></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
