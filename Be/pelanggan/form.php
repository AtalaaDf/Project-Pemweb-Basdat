<?php
require_once __DIR__ . '/../config/koneksi.php';
$isEdit = isset($_GET['id']);
$data = [];
if ($isEdit) {
 $id=(int)$_GET['id']; $stmt=$koneksi->prepare("SELECT * FROM pelanggan WHERE id_pelanggan=?"); $stmt->bind_param('i',$id); $stmt->execute();
 $data=$stmt->get_result()->fetch_assoc(); if(!$data) die('Data tidak ditemukan.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $values = [$_POST['nama_pelanggan'] ?? '',$_POST['email'] ?? '',$_POST['no_hp'] ?? '',$_POST['alamat'] ?? ''];
 if ($isEdit) {
  $id=(int)$_GET['id']; $stmt=$koneksi->prepare("UPDATE pelanggan SET nama_pelanggan=?,email=?,no_hp=?,alamat=? WHERE id_pelanggan=?");
  $stmt->bind_param('sssss', $values[0], $values[1], $values[2], $values[3], $id);
 } else {
  $stmt=$koneksi->prepare("INSERT INTO pelanggan (nama_pelanggan,email,no_hp,alamat) VALUES (?,?,?,?)");
  $stmt->bind_param('ssss', $values[0], $values[1], $values[2], $values[3]);
 }
 $stmt->execute(); $_SESSION['flash']='Data pelanggan berhasil disimpan.'; header('Location: index.php'); exit;
}
$pageTitle = ($isEdit?'Edit ':'Tambah ').'Pelanggan';
$basePrefix='../'; $assetPrefix='../';
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><h2 class="page-heading"><?= $isEdit?'Edit':'Tambah' ?> Pelanggan</h2></div>
<div class="card form-card p-4">
<form method="post">
<div class="mb-3"><label class="form-label">Nama Pelanggan</label><input class="form-control" type="text" name="nama_pelanggan" value="<?= htmlspecialchars((string)($data['nama_pelanggan'] ?? '')) ?>" required></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= htmlspecialchars((string)($data['email'] ?? '')) ?>" ></div>
<div class="mb-3"><label class="form-label">Nomor HP</label><input class="form-control" type="text" name="no_hp" value="<?= htmlspecialchars((string)($data['no_hp'] ?? '')) ?>" ></div>
<div class="mb-3"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" rows="3"><?= htmlspecialchars($data['+repr(n)+'] ?? '') ?></textarea></div>

<div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan</button><a href="index.php" class="btn btn-light">Kembali</a></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
