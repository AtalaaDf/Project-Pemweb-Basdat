/**
 * cart.js - Simulasi Keranjang Belanja Sisi Klien (LocalStorage) Digimart
 * Memenuhi requirement Pemweb simulasi belanja & pemesanan tanpa mengubah ERD
 */

const Cart = {
    ambilItem: () => {
        try {
            const data = localStorage.getItem(window.CONFIG ? window.CONFIG.STORAGE_KEYS.CART : 'digimart_cart_items');
            return data ? JSON.parse(data) : [];
        } catch (e) {
            console.error('Gagal membaca data keranjang:', e);
            return [];
        }
    },

    simpanItem: (items) => {
        try {
            localStorage.setItem(window.CONFIG ? window.CONFIG.STORAGE_KEYS.CART : 'digimart_cart_items', JSON.stringify(items));
            Cart.perbaruiBadge();
        } catch (e) {
            console.error('Gagal menyimpan data keranjang:', e);
        }
    },

    tambahProduk: (produk, jumlah = 1) => {
        const items = Cart.ambilItem();
        const index = items.findIndex(i => Number(i.id_produk) === Number(produk.id_produk));
        
        if (index > -1) {
            items[index].jumlah += jumlah;
        } else {
            items.push({
                id_produk: produk.id_produk,
                nama_produk: produk.nama_produk,
                harga: Number(produk.harga),
                jumlah: jumlah,
                nama_kategori: produk.nama_kategori || 'Elektronik'
            });
        }

        Cart.simpanItem(items);
        if (typeof window.tampilkanToast === 'function') {
            window.tampilkanToast(`"${produk.nama_produk}" ditambahkan ke keranjang belanja.`, 'sukses');
        }
    },

    hapusProduk: (id_produk) => {
        let items = Cart.ambilItem();
        items = items.filter(i => Number(i.id_produk) !== Number(id_produk));
        Cart.simpanItem(items);
        if (typeof window.tampilkanToast === 'function') {
            window.tampilkanToast('Produk dihapus dari keranjang belanja.', 'info');
        }
    },

    hitungTotalHarga: () => {
        const items = Cart.ambilItem();
        return items.reduce((total, i) => total + (i.harga * i.jumlah), 0);
    },

    hitungTotalItem: () => {
        const items = Cart.ambilItem();
        return items.reduce((total, i) => total + i.jumlah, 0);
    },

    kosongkan: () => {
        localStorage.removeItem(window.CONFIG ? window.CONFIG.STORAGE_KEYS.CART : 'digimart_cart_items');
        Cart.perbaruiBadge();
    },

    perbaruiBadge: () => {
        const badge = document.getElementById('badge-keranjang-count');
        if (!badge) return;
        const total = Cart.hitungTotalItem();
        badge.textContent = total;
        if (total > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    Cart.perbaruiBadge();
});

window.Cart = Cart;

