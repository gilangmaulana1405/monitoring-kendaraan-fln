/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

// Inisialisasi Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '289d17420b0f46c80612', // <-- ini kunci public app kamu dari Pusher
    cluster: 'ap1', // <-- ini cluster server kamu (ap1 = Asia Pasific 1)
    forceTLS: true, // <-- pakai HTTPS
});

// Lalu dengarkan semua kendaraan yang ada
document.addEventListener("DOMContentLoaded", function () {
    window.kendaraanIds.forEach(id => {
        Echo.channel(`kendaraan.${id}`) // Menggunakan public channel
            .listen('.KendaraanUpdated', (event) => {
                console.log("Realtime Event:", event);
                const card = document.querySelector(`[data-id='${event.id}']`);
                if (!card) return;

                const badge = card.querySelector(".status-badge");
                badge.textContent = event.status;
                badge.classList.remove("bg-success", "bg-warning", "bg-danger");

                switch (event.status) {
                    case "Stand By":
                        badge.classList.add("bg-success");
                        break;
                    case "Pergi":
                        badge.classList.add("bg-warning");
                        break;
                    case "Perbaikan":
                        badge.classList.add("bg-danger");
                        break;
                }

                const waktu = card.querySelector(".waktu-update");
                waktu.textContent = event.updated_at;
            });

    });
});
