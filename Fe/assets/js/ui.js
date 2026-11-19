/**
 * ui.js - Komponen Bersama & Interaksi UI Digimart
 * Mencakup: Progress Bar, Dialog Modal Konfirmasi, Toast Notifikasi, & Navbar Mobile (DOM)
 */

document.addEventListener('DOMContentLoaded', () => {
    inisialisasiProgressBar();
    inisialisasiNavbarMobile();
    inisialisasiToastContainer();
    inisialisasiDialogModal();
});

/* ==========================================================================
   1. PROGRESS BAR NAVIGASI (PW-7, PW-8, PW-9)
   ========================================================================== */
function inisialisasiProgressBar() {
    let bar = document.getElementById('page-progress-bar');
    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'page-progress-bar';
        document.body.appendChild(bar);
    }

    // Tangani perpindahan halaman internal
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        const target = link.getAttribute('target');

        // Lewati anchor kosong, javascript, tautan luar, atau target blank
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || target === '_blank' || href.startsWith('http')) {
            return;
        }

        // Jalankan animasi progress bar
        bar.style.opacity = '1';
        bar.style.width = '75%';
    });

    // Selesaikan saat halaman selesai dimuat
    window.addEventListener('load', () => {
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            setTimeout(() => { bar.style.width = '0%'; }, 300);
        }, 200);
    });
}

/* ==========================================================================
   2. NAVBAR MOBILE (MANIPULASI DOM MURNI) (PW-8, PW-9, PW-10)
   ========================================================================== */
function inisialisasiNavbarMobile() {
    const btnToggle = document.getElementById('btn-nav-mobile');
    const panelMenu = document.getElementById('panel-nav-mobile');
    const ikonMenu = document.getElementById('ikon-nav-hamburger');
    const ikonTutup = document.getElementById('ikon-nav-close');

    if (!btnToggle || !panelMenu) return;

    function bukaMenu() {
        btnToggle.setAttribute('aria-expanded', 'true');
        panelMenu.classList.remove('hidden');
        document.body.classList.add('scroll-locked');
        if (ikonMenu) ikonMenu.classList.add('hidden');
        if (ikonTutup) ikonTutup.classList.remove('hidden');
    }

    function tutupMenu() {
        btnToggle.setAttribute('aria-expanded', 'false');
        panelMenu.classList.add('hidden');
        document.body.classList.remove('scroll-locked');
        if (ikonMenu) ikonMenu.classList.remove('hidden');
        if (ikonTutup) ikonTutup.classList.add('hidden');
    }

    btnToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = btnToggle.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
            tutupMenu();
        } else {
            bukaMenu();
        }
    });

    // Tutup jika klik di luar panel
    document.addEventListener('click', (e) => {
        if (!panelMenu.contains(e.target) && !btnToggle.contains(e.target)) {
            tutupMenu();
        }
    });

    // Tutup dengan tombol Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') tutupMenu();
    });

    // Tutup otomatis jika ukuran layar diperbesar melewati 768px (Desktop)
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            tutupMenu();
        }
    });
}

/* ==========================================================================
   3. TOAST NOTIFIKASI SINGKAT (PW-7, PW-8)
   ========================================================================== */
let toastContainer = null;

function inisialisasiToastContainer() {
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm pointer-events-none';
        document.body.appendChild(toastContainer);
    }
}

function tampilkanToast(pesan, tipe = 'sukses') {
    inisialisasiToastContainer();

    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg text-sm transition-all duration-300 transform translate-y-2 opacity-0';

    let iconSvg = '';
    if (tipe === 'sukses') {
        toast.classList.add('bg-white', 'text-slate-800', 'border-emerald-200');
        iconSvg = `<svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
    } else if (tipe === 'galat' || tipe === 'error') {
        toast.classList.add('bg-white', 'text-slate-800', 'border-rose-200');
        iconSvg = `<svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    } else {
        toast.classList.add('bg-white', 'text-slate-800', 'border-indigo-200');
        iconSvg = `<svg class="w-5 h-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
    }

    toast.innerHTML = `
        ${iconSvg}
        <span class="flex-1 font-medium">${escapeHtml(pesan)}</span>
    `;

    toastContainer.appendChild(toast);

    // Animasi muncul
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Hapus otomatis setelah durasi
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }, (window.CONFIG ? window.CONFIG.TOAST_DURATION : 3500));
}

/* ==========================================================================
   4. MODAL DIALOG KONFIRMASI (PW-7, PW-8, PW-9)
   ========================================================================== */
let modalOverlay = null;

function inisialisasiDialogModal() {
    if (modalOverlay) return;

    modalOverlay = document.createElement('div');
    modalOverlay.id = 'modal-konfirmasi-container';
    modalOverlay.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200';
    modalOverlay.setAttribute('role', 'dialog');
    modalOverlay.setAttribute('aria-modal', 'true');

    modalOverlay.innerHTML = `
        <div id="modal-box" class="bg-white rounded-2xl border border-slate-100 shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-200">
            <h3 id="modal-judul" class="text-lg font-bold text-slate-900 mb-2">Konfirmasi Tindakan</h3>
            <p id="modal-pesan" class="text-sm text-slate-600 mb-6 leading-relaxed">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="btn-modal-batal" class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                <button type="button" id="btn-modal-konfirmasi" class="px-5 py-2.5 rounded-lg text-sm font-semibold transition">Ya, Lanjutkan</button>
            </div>
        </div>
    `;

    document.body.appendChild(modalOverlay);
}

function bukaDialogKonfirmasi({
    judul = 'Konfirmasi Tindakan',
    pesan = 'Apakah Anda yakin ingin melanjutkan?',
    tipe = 'primer', // 'primer' (soft blue #a6b4fc) atau 'bahaya' (rose red)
    teksKonfirmasi = 'Ya, Lanjutkan',
    teksBatal = 'Batal',
    onKonfirmasi = () => {}
}) {
    inisialisasiDialogModal();

    const judulEl = document.getElementById('modal-judul');
    const pesanEl = document.getElementById('modal-pesan');
    const btnKonfirmasi = document.getElementById('btn-modal-konfirmasi');
    const btnBatal = document.getElementById('btn-modal-batal');
    const modalBox = document.getElementById('modal-box');

    judulEl.textContent = judul;
    pesanEl.textContent = pesan;
    btnKonfirmasi.textContent = teksKonfirmasi;
    btnBatal.textContent = teksBatal;

    // Tipe warna tombol konfirmasi
    btnKonfirmasi.className = 'px-5 py-2.5 rounded-lg text-sm font-semibold transition hover-lift';
    if (tipe === 'bahaya') {
        btnKonfirmasi.classList.add('bg-rose-600', 'text-white', 'hover:bg-rose-700');
    } else {
        btnKonfirmasi.classList.add('bg-[#A6B4FC]', 'text-[#0F172A]', 'hover:bg-[#818CF8]');
    }

    function tutupModal() {
        modalOverlay.classList.remove('opacity-100');
        modalOverlay.classList.add('opacity-0');
        modalBox.classList.remove('scale-100');
        modalBox.classList.add('scale-95');
        setTimeout(() => {
            modalOverlay.classList.add('hidden');
            document.body.classList.remove('scroll-locked');
        }, 200);
    }

    // Bersihkan listener sebelumnya
    const newBtnKonfirmasi = btnKonfirmasi.cloneNode(true);
    btnKonfirmasi.parentNode.replaceChild(newBtnKonfirmasi, btnKonfirmasi);

    const newBtnBatal = btnBatal.cloneNode(true);
    btnBatal.parentNode.replaceChild(newBtnBatal, btnBatal);

    newBtnBatal.addEventListener('click', tutupModal);

    newBtnKonfirmasi.addEventListener('click', () => {
        tutupModal();
        if (typeof onKonfirmasi === 'function') onKonfirmasi();
    });

    // Buka tampilan
    modalOverlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalOverlay.classList.remove('opacity-0');
        modalOverlay.classList.add('opacity-100');
        modalBox.classList.remove('scale-95');
        modalBox.classList.add('scale-100');
        document.body.classList.add('scroll-locked');
        newBtnKonfirmasi.focus();
    });
}

window.tampilkanToast = tampilkanToast;
window.bukaDialogKonfirmasi = bukaDialogKonfirmasi;

