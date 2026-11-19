/**
 * api.js - Gerbang Komunikasi Data Frontend ke Backend (Digimart)
 * Seluruh pemanggilan jaringan dipusatkan di sini menggunakan Fetch API standar
 */

const API = {
    /**
     * Helper request internal dengan penanganan galat
     */
    request: async (endpoint, options = {}) => {
        const baseUrl = window.CONFIG ? window.CONFIG.API_BASE_URL : '../Be/api/';
        const url = baseUrl + endpoint;

        try {
            const config = {
                headers: {
                    'Content-Type': 'application/json',
                    ...(options.headers || {})
                },
                ...options
            };

            const res = await fetch(url, config);
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return await res.json();
        } catch (error) {
            console.warn(`[API] Gagal memuat dari ${url}, menggunakan mode fallback:`, error.message);
            return { sukses: false, pesan: error.message };
        }
    },

    // 1. Dashboard & Ringkasan
    getDashboard: async () => {
        return await API.request('dashboard.php');
    },

    // 2. Produk
    getProduk: async (params = {}) => {
        const query = new URLSearchParams(params).toString();
        return await API.request(`produk.php${query ? '?' + query : ''}`);
    },

    getProdukDetail: async (id) => {
        return await API.request(`produk.php?id=${encodeURIComponent(id)}`);
    },

    simpanProduk: async (data) => {
        return await API.request('produk.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    hapusProduk: async (id) => {
        return await API.request(`produk.php?action=delete&id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ id_produk: id, action: 'delete' })
        });
    },

    // 3. Kategori
    getKategori: async () => {
        return await API.request('kategori.php');
    },

    simpanKategori: async (data) => {
        return await API.request('kategori.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    hapusKategori: async (id) => {
        return await API.request(`kategori.php?action=delete&id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ id_kategori: id, action: 'delete' })
        });
    },

    // 4. Pelanggan
    getPelanggan: async () => {
        return await API.request('pelanggan.php');
    },

    simpanPelanggan: async (data) => {
        return await API.request('pelanggan.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    hapusPelanggan: async (id) => {
        return await API.request(`pelanggan.php?action=delete&id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ id_pelanggan: id, action: 'delete' })
        });
    },

    // 5. Pesanan
    getPesanan: async (params = {}) => {
        const query = new URLSearchParams(params).toString();
        return await API.request(`pesanan.php${query ? '?' + query : ''}`);
    },

    getPesananDetail: async (id) => {
        return await API.request(`pesanan.php?id=${encodeURIComponent(id)}`);
    },

    buatPesanan: async (data) => {
        return await API.request('pesanan.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    updateStatusPesanan: async (id_pesanan, status_pesanan) => {
        return await API.request('pesanan.php?action=update_status', {
            method: 'POST',
            body: JSON.stringify({ id_pesanan, status_pesanan, action: 'update_status' })
        });
    },

    hapusPesanan: async (id) => {
        return await API.request(`pesanan.php?action=delete&id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ id_pesanan, action: 'delete' })
        });
    },

    // 6. Pembayaran
    getPembayaran: async () => {
        return await API.request('pembayaran.php');
    },

    simpanPembayaran: async (data) => {
        return await API.request('pembayaran.php', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    hapusPembayaran: async (id) => {
        return await API.request(`pembayaran.php?action=delete&id=${encodeURIComponent(id)}`, {
            method: 'POST',
            body: JSON.stringify({ id_pembayaran: id, action: 'delete' })
        });
    },

    // 7. Laporan
    getLaporan: async (params = {}) => {
        const query = new URLSearchParams(params).toString();
        return await API.request(`laporan.php${query ? '?' + query : ''}`);
    }
};

window.API = API;

