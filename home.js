/* =========================================
   VANASAGA HOME CORE ENGINE - 2026
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. LOGIKA UTAMA HAMBURGER MENU (SIDE-OVERLAY) ---
    const menuToggle = document.getElementById('mobile-menu');
    const mobileNav = document.getElementById('mobile-nav');
    const body = document.body;

    // Fungsi Global untuk buka/tutup menu
    window.toggleMenu = function() {
        if (!menuToggle || !mobileNav) return;

        const isActive = menuToggle.classList.toggle('is-active');
        mobileNav.classList.toggle('active');
        
        // Kunci scroll body agar tidak bergeser saat menu buka
        body.style.overflow = isActive ? 'hidden' : 'auto';
    }

    // Event Listener Klik Hamburger
    if (menuToggle) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Mencegah trigger ke document click
            toggleMenu();
        });
    }

    // CLOSE SENSOR: Klik di area kosong (luar panel menu) akan menutup menu
    document.addEventListener('click', (e) => {
        if (mobileNav.classList.contains('active')) {
            // Jika yang diklik bukan bagian dari mobileNav dan bukan tombol toggle
            if (!mobileNav.contains(e.target) && !menuToggle.contains(e.target)) {
                toggleMenu();
            }
        }
    });

    // --- 2. LOGIKA SALIN IP (COPY TO CLIPBOARD) ---
    window.copyIP = function(ip) {
        navigator.clipboard.writeText(ip).then(() => {
            showToast(`IP ${ip} Berhasil Disalin!`);
        }).catch(err => {
            console.error('Gagal copy: ', err);
        });
    }

    // --- 3. SISTEM NOTIFIKASI (TOAST) ---
    function showToast(message) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'custom-toast';
        toast.innerHTML = `<i class="fas fa-magic" style="color: #a855f7; margin-right: 10px;"></i> ${message}`;

        container.appendChild(toast);

        // Animasi keluar ke arah kanan (sinkron dengan CSS posisi right)
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(50px)'; 
            toast.style.transition = '0.5s ease-in';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    // --- 4. REAL-TIME PLAYER COUNT ---
    async function fetchPlayers() {
        const countElement = document.getElementById('player-count');
        if (!countElement) return;

        try {
            // Menggunakan API publik mcstatus.io untuk vanasagaid.xyz
            const response = await fetch('https://api.mcstatus.io/v2/status/java/vanasagaid.xyz');
            const data = await response.json();
            
            if (data.online) {
                countElement.innerText = `${data.players.online} / ${data.players.max}`;
            } else {
                countElement.innerText = "Server Offline";
            }
        } catch (error) {
            console.warn("Gagal mengambil data pemain.");
        }
    }

    // Jalankan saat load dan refresh tiap 1 menit
    fetchPlayers();
    setInterval(fetchPlayers, 60000);
});