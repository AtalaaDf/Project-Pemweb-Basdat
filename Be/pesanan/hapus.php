<?php
require_once __DIR__ . '/../config/koneksi.php';
$id=(int)($_GET['id'] ?? 0);
$stmt=$koneksi->prepare("DELETE FROM pesanan WHERE id_pesanan=?");
$stmt->bind_param('i',$id);
try { $stmt->execute(); $_SESSION['flash']='Data berhasil dihapus.'; }
catch (mysqli_sql_exception $e) { $_SESSION['flash']='Data tidak dapat dihapus karena masih digunakan oleh data lain.'; }
header('Location: index.php'); exit;
