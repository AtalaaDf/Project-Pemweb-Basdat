<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Toko Online';
$basePrefix = $basePrefix ?? '';
$assetPrefix = $assetPrefix ?? '';
$isAdminArea = $isAdminArea ?? false;
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Digimart</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#FFFFFF',
                        surface: '#F8FAFC',
                        border: '#E2E8F0',
                        text: '#0F172A',
                        muted: '#64748B',
                        accent: '#A6B4FC',
                        'accent-text': '#0F172A',
                        'accent-hover': '#818CF8',
                        'accent-soft': '#EEF2FF',
                        'accent-deep': '#4F46E5',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- CSS Kustom Digimart -->
    <link href="<?= $assetPrefix ?>assets/css/custom.css" rel="stylesheet">

    <!-- Skrip Konfigurasi & Utilitas Inti (di-load di head agar siap dipakai) -->
    <script src="<?= $assetPrefix ?>assets/js/config.js"></script>
    <script src="<?= $assetPrefix ?>assets/js/format.js"></script>
    <script src="<?= $assetPrefix ?>assets/js/validate.js"></script>
    <script src="<?= $assetPrefix ?>assets/js/cart.js"></script>
    <script src="<?= $assetPrefix ?>assets/js/api.js"></script>
    <script src="<?= $assetPrefix ?>assets/js/ui.js"></script>
</head>
<body class="min-h-full flex flex-col font-sans text-slate-900 bg-white antialiased">

<!-- Progress Bar di atas Navbar (PW-2) -->
<div id="page-progress-bar"></div>

<?php if (!$isAdminArea): ?>
    <!-- ====================================================================
         NAVBAR PUBLIK (PW-1, PW-5, PW-7, PW-8, PW-9, PW-10)
         ==================================================================== -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Logo & Brand -->
            <a href="<?= $basePrefix ?>index.php" class="flex items-center gap-2.5 group">
                <span class="w-9 h-9 rounded-xl bg-[#A6B4FC] text-[#0F172A] flex items-center justify-center font-bold text-lg shadow-sm group-hover:scale-105 transition-transform">D</span>
                <div class="flex flex-col">
                    <span class="font-bold text-lg tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">Digimart</span>
                    <span class="text-[10px] text-slate-500 leading-none hidden sm:block">Digital & Tech Store</span>
                </div>
            </a>

            <!-- Navigasi Desktop -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="<?= $basePrefix ?>index.php" class="text-sm font-medium transition-colors <?= $current === 'index.php' && empty($subPage) ? 'text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900' ?>">Beranda</a>
                <a href="<?= $basePrefix ?>katalog.php" class="text-sm font-medium transition-colors <?= $current === 'katalog.php' ? 'text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900' ?>">Katalog</a>
                <a href="<?= $basePrefix ?>pesan.php" class="text-sm font-medium transition-colors <?= $current === 'pesan.php' ? 'text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900' ?>">Pemesanan</a>
                <a href="<?= $basePrefix ?>kontak.php" class="text-sm font-medium transition-colors <?= $current === 'kontak.php' ? 'text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900' ?>">Kontak</a>
            </nav>

            <!-- Aksi Kanan (Keranjang & Akses Admin) -->
            <div class="flex items-center gap-3">
                <!-- Tombol Keranjang Belanja -->
                <a href="<?= $basePrefix ?>pesan.php" class="relative p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition" title="Keranjang Belanja">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span id="badge-keranjang-count" class="hidden absolute top-1 right-1 w-4 h-4 bg-indigo-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                </a>

                <!-- Tautan Seller Centre / Admin -->
                <a href="<?= $basePrefix ?>admin/index.php" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                    <span>Seller Centre</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>

                <!-- Tombol Hamburger Mobile (PW-8, PW-9) -->
                <button type="button" id="btn-nav-mobile" aria-expanded="false" aria-controls="panel-nav-mobile" class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition" aria-label="Buka menu navigasi">
                    <svg id="ikon-nav-hamburger" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="ikon-nav-close" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Panel Navigasi Mobile (DOM Controlled) -->
        <div id="panel-nav-mobile" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-2 shadow-lg">
            <a href="<?= $basePrefix ?>index.php" class="block px-3 py-2 rounded-lg text-sm font-medium <?= $current === 'index.php' ? 'bg-[#EEF2FF] text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-50' ?>">Beranda</a>
            <a href="<?= $basePrefix ?>katalog.php" class="block px-3 py-2 rounded-lg text-sm font-medium <?= $current === 'katalog.php' ? 'bg-[#EEF2FF] text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-50' ?>">Katalog Produk</a>
            <a href="<?= $basePrefix ?>pesan.php" class="block px-3 py-2 rounded-lg text-sm font-medium <?= $current === 'pesan.php' ? 'bg-[#EEF2FF] text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-50' ?>">Formulir Pemesanan</a>
            <a href="<?= $basePrefix ?>kontak.php" class="block px-3 py-2 rounded-lg text-sm font-medium <?= $current === 'kontak.php' ? 'bg-[#EEF2FF] text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-50' ?>">Kontak Toko</a>
            <div class="pt-2 border-t border-slate-100">
                <a href="<?= $basePrefix ?>admin/index.php" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 text-center">Kelola Toko (Seller Centre)</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
<?php else: ?>
    <!-- ====================================================================
         LAYOUT ADMIN / SELLER CENTRE (BD-1, BD-2, BI-2)
         ==================================================================== -->
    <header class="sticky top-0 z-40 bg-white border-b border-slate-200 h-16 flex items-center px-4 sm:px-6 justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= $basePrefix ?>admin/index.php" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-[#A6B4FC] text-[#0F172A] flex items-center justify-center font-bold text-base">D</span>
                <span class="font-bold text-base tracking-tight text-slate-900">Digimart Admin</span>
            </a>
            <span class="text-xs px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-medium">Seller Centre</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= $basePrefix ?>index.php" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Lihat Toko Publik</span>
            </a>
        </div>
    </header>

    <div class="flex-1 flex max-w-7xl w-full mx-auto">
        <!-- Sidebar Admin (Static 256px di Desktop, Adaptive di Mobile) -->
        <aside class="w-64 border-r border-slate-200 bg-slate-50/50 p-4 shrink-0 hidden md:block">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-3">Menu Master</div>
            <nav class="space-y-1">
                <a href="<?= $basePrefix ?>admin/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= $current === 'index.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span>Dashboard</span>
                </a>
                <a href="<?= $basePrefix ?>produk/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/produk/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    <span>Data Produk</span>
                </a>
                <a href="<?= $basePrefix ?>kategori/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/kategori/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    <span>Data Kategori</span>
                </a>
                <a href="<?= $basePrefix ?>pelanggan/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/pelanggan/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Data Pelanggan</span>
                </a>
                <a href="<?= $basePrefix ?>pesanan/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/pesanan/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    <span>Data Pesanan</span>
                </a>
                <a href="<?= $basePrefix ?>pembayaran/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/pembayaran/') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    <span>Data Pembayaran</span>
                </a>
                <div class="pt-3 pb-1 border-t border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3">Laporan</div>
                <a href="<?= $basePrefix ?>admin/laporan.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], 'laporan.php') !== false ? 'bg-[#A6B4FC] text-[#0F172A] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span>Rekapitulasi Penjualan</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6 md:p-8 bg-white overflow-x-hidden">
<?php endif; ?>

<!-- Flash Message Otomatis Sesi PHP -->
<?php if (!empty($_SESSION['flash'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof tampilkanToast === 'function') {
                tampilkanToast(<?= json_encode($_SESSION['flash']) ?>, 'sukses');
            }
        });
    </script>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
