<?php
$isAdminArea = $isAdminArea ?? false;
$basePrefix = $basePrefix ?? '';
?>
<?php if ($isAdminArea): ?>
        </main>
    </div>
<?php else: ?>
    </main>
    <!-- Footer Publik (PW-1, PW-5, desain.md 4.11) -->
    <footer class="bg-slate-50 border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Info Toko -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-[#A6B4FC] text-[#0F172A] flex items-center justify-center font-bold text-sm">D</span>
                        <span class="font-bold text-base text-slate-900">Digimart</span>
                    </div>
                    <p class="text-sm text-slate-500 max-w-sm leading-relaxed">
                        Solusi belanja perangkat digital dan aksesoris teknologi modern, praktis, dan terpercaya. Temukan kenyamanan berbelanja perlengkapan gadget favorit Anda.
                    </p>
                </div>

                <!-- Navigasi Cepat -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Toko</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= $basePrefix ?>index.php" class="text-slate-600 hover:text-indigo-600 transition">Beranda</a></li>
                        <li><a href="<?= $basePrefix ?>katalog.php" class="text-slate-600 hover:text-indigo-600 transition">Katalog Produk</a></li>
                        <li><a href="<?= $basePrefix ?>pesan.php" class="text-slate-600 hover:text-indigo-600 transition">Pemesanan</a></li>
                        <li><a href="<?= $basePrefix ?>kontak.php" class="text-slate-600 hover:text-indigo-600 transition">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Akses Area Pengelola -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Pengelola</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= $basePrefix ?>admin/index.php" class="text-slate-600 hover:text-indigo-600 transition">Seller Centre</a></li>
                        <li><a href="<?= $basePrefix ?>admin/laporan.php" class="text-slate-600 hover:text-indigo-600 transition">Laporan Penjualan</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> Digimart. Hak Cipta Dilindungi. Sistem Informasi Penjualan Online.</p>
                <p>Minimalist UI &bull; PHP Native &bull; Tailwind CSS</p>
            </div>
        </div>
    </footer>
<?php endif; ?>

</body>
</html>
