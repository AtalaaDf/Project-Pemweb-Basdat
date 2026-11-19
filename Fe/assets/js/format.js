/**
 * format.js - Utilitas Pemformatan Data Digimart
 */

/**
 * Memformat angka integer/decimal menjadi string Rupiah Indonesia
 * Contoh: 85000 -> "Rp 85.000"
 */
function formatRupiah(nominal) {
    const angka = Number(nominal) || 0;
    return 'Rp ' + angka.toLocaleString('id-ID');
}

/**
 * Memformat tanggal string/timestamp ke format lokal yang rapi
 * Contoh: "2026-11-19 14:00:00" -> "19 Nov 2026, 14:00"
 */
function formatTanggal(tanggalStr) {
    if (!tanggalStr) return '-';
    const date = new Date(tanggalStr);
    if (isNaN(date.getTime())) return tanggalStr;
    
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Menghasilkan HTML badge status pesanan dengan token desain Soft Pink & Semantik
 */
function formatStatusBadge(status) {
    const s = String(status || '').trim();
    let badgeClass = 'bg-indigo-50 text-indigo-700 border border-indigo-200';
    
    if (s === 'Selesai' || s === 'Lunas') {
        badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    } else if (s === 'Menunggu' || s === 'Diproses') {
        badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
    } else if (s === 'Dikirim') {
        badgeClass = 'bg-sky-50 text-sky-700 border border-sky-200';
    } else if (s === 'Dibatalkan' || s === 'Gagal') {
        badgeClass = 'bg-rose-50 text-rose-700 border border-rose-200';
    }

    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">${escapeHtml(s || 'Menunggu')}</span>`;
}

/**
 * Sanitasi string untuk mencegah celah XSS saat disisipkan ke DOM
 */
function escapeHtml(text) {
    if (!text && text !== 0) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

window.formatRupiah = formatRupiah;
window.formatTanggal = formatTanggal;
window.formatStatusBadge = formatStatusBadge;
window.escapeHtml = escapeHtml;

