/**
 * api.js - Helper Komunikasi Data dan Jaringan Digimart
 * Memusatkan akses request, submit form, dan interaksi data
 */

const API = {
    /**
     * Mengirim data form via fetch atau standar POST
     */
    kirimData: async (url, data, method = 'POST') => {
        try {
            const formData = new FormData();
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    formData.append(key, data[key]);
                }
            }

            const response = await fetch(url, {
                method: method,
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Permintaan gagal dengan status ${response.status}`);
            }

            return { sukses: true };
        } catch (error) {
            console.error('API Error:', error);
            return { sukses: false, pesan: error.message };
        }
    }
};

window.API = API;

