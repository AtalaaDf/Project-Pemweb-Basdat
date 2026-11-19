CREATE DATABASE IF NOT EXISTS penjualan_online
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE penjualan_online;

CREATE TABLE IF NOT EXISTS pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  no_hp VARCHAR(20),
  alamat TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produk (
  id_produk INT AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT NULL,
  nama_produk VARCHAR(100) NOT NULL,
  harga DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
    REFERENCES kategori(id_kategori)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pesanan (
  id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT NOT NULL,
  id_produk INT NOT NULL,
  tanggal_pesanan DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_harga DECIMAL(12,2) NOT NULL DEFAULT 0,
  status_pesanan VARCHAR(30) NOT NULL DEFAULT 'Menunggu',
  CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY (id_pelanggan)
    REFERENCES pelanggan(id_pelanggan)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pesanan_produk FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pembayaran (
  id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT NOT NULL UNIQUE,
  tanggal_bayar DATETIME NULL,
  jumlah_bayar DECIMAL(12,2) NOT NULL DEFAULT 0,
  metode_bayar VARCHAR(50),
  CONSTRAINT fk_pembayaran_pesanan FOREIGN KEY (id_pesanan)
    REFERENCES pesanan(id_pesanan)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contoh data awal (opsional)
INSERT INTO kategori (nama_kategori, deskripsi)
SELECT 'Elektronik', 'Perangkat elektronik' WHERE NOT EXISTS
(SELECT 1 FROM kategori WHERE nama_kategori='Elektronik');
INSERT INTO kategori (nama_kategori, deskripsi)
SELECT 'Aksesori', 'Aksesori komputer dan gadget' WHERE NOT EXISTS
(SELECT 1 FROM kategori WHERE nama_kategori='Aksesori');
INSERT INTO produk (id_kategori, nama_produk, harga, stok)
SELECT (SELECT id_kategori FROM kategori WHERE nama_kategori='Elektronik' LIMIT 1),
       'Mouse Wireless', 85000, 20
WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama_produk='Mouse Wireless');
