/**
 * config.js - Konfigurasi Global Aplikasi Digimart (Frontend)
 */
const CONFIG = {
    STORE_NAME: 'Digimart',
    TAGLINE: 'Solusi belanja perangkat digital dan aksesoris teknologi modern, praktis, dan terpercaya.',
    
    // Otomatis tentukan path ke folder Be/api/ secara relatif
    API_BASE_URL: (() => {
        const path = window.location.pathname.replace(/\\/g, '/');
        if (path.includes('/admin/')) {
            return '../../Be/api/';
        }
        return '../Be/api/';
    })(),

    COLORS: {
        ACCENT: '#A6B4FC',
        ACCENT_TEXT: '#0F172A',
        ACCENT_HOVER: '#818CF8',
        ACCENT_SOFT: '#EEF2FF',
        ACCENT_DEEP: '#4F46E5',
        SURFACE: '#F8FAFC',
        BORDER: '#E2E8F0',
        TEXT: '#0F172A',
        MUTED: '#64748B'
    },
    
    STATUS_PESANAN: ['Menunggu', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'],
    METODE_PEMBAYARAN_DEFAULT: ['Transfer Bank BCA', 'Transfer Bank Mandiri', 'QRIS', 'ShopeePay', 'COD (Bayar di Tempat)'],
    TOAST_DURATION: 3500, // ms
    STORAGE_KEYS: {
        CART: 'digimart_cart_items',
        USER_DATA: 'digimart_checkout_info'
    }
};

window.CONFIG = CONFIG;

