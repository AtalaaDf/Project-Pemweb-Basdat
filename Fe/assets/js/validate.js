/**
 * validate.js - Utilitas Validasi Formulir Interaktif di Sisi Klien
 */

const VALIDATORS = {
    wajib: (nilai) => {
        const valid = String(nilai || '').trim().length > 0;
        return { valid, pesan: 'Bidang ini wajib diisi.' };
    },
    email: (nilai) => {
        if (!nilai) return { valid: true };
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const valid = regex.test(String(nilai).trim());
        return { valid, pesan: 'Format email tidak valid (contoh: nama@domain.com).' };
    },
    noHp: (nilai) => {
        if (!nilai) return { valid: true };
        const clean = String(nilai).trim();
        const valid = clean.length >= 8 && clean.length <= 16 && /^[0-9+\-\s]+$/.test(clean);
        return { valid, pesan: 'Nomor telepon tidak valid (minimal 8 digit).' };
    },
    angkaPositif: (nilai) => {
        if (nilai === '' || nilai === null || nilai === undefined) return { valid: true };
        const num = Number(nilai);
        const valid = !isNaN(num) && num >= 0;
        return { valid, pesan: 'Nilai angka tidak boleh bernilai negatif.' };
    },
    minimalSatu: (nilai) => {
        const num = Number(nilai);
        const valid = !isNaN(num) && num >= 1;
        return { valid, pesan: 'Jumlah minimal adalah 1.' };
    }
};

/**
 * Memvalidasi sebuah input HTML berdasarkan array aturan
 * Menampilkan pesan error di bawah input jika tidak valid
 */
function validasiInput(inputEl, aturanList = []) {
    if (!inputEl) return true;
    
    const nilai = inputEl.value;
    let hasil = { valid: true, pesan: '' };

    for (const aturan of aturanList) {
        if (typeof VALIDATORS[aturan] === 'function') {
            const check = VALIDATORS[aturan](nilai);
            if (!check.valid) {
                hasil = check;
                break;
            }
        }
    }

    // Tampilkan / hilangkan pesan error di DOM
    const idError = inputEl.id ? `error-${inputEl.id}` : null;
    let errorEl = idError ? document.getElementById(idError) : null;

    if (!hasil.valid) {
        inputEl.classList.add('border-danger', 'focus:ring-danger');
        inputEl.classList.remove('border-border', 'focus:ring-accent');
        
        if (!errorEl && idError) {
            errorEl = document.createElement('p');
            errorEl.id = idError;
            errorEl.className = 'text-xs text-danger mt-1.5 transition-opacity duration-200';
            inputEl.parentNode.appendChild(errorEl);
        }
        if (errorEl) {
            errorEl.textContent = hasil.pesan;
            errorEl.style.display = 'block';
        }
    } else {
        inputEl.classList.remove('border-danger', 'focus:ring-danger');
        inputEl.classList.add('border-border', 'focus:ring-accent');
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    }

    return hasil.valid;
}

window.VALIDATORS = VALIDATORS;
window.validasiInput = validasiInput;

