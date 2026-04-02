/* =========================================
   VANASAGA HOME CORE ENGINE - 2026
   (Optimized with Dynamic Server Status)
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

    // --- 4. REAL-TIME PLAYER COUNT & DYNAMIC STATUS DOT ---
    async function cekStatusServer() {
        const ipServer = 'vanasagaid.xyz'; // IP Server Kamu
        const titikStatus = document.getElementById('server-dot');
        const teksStatus = document.getElementById('server-status-text');

        // Cegah error jika elemen tidak ditemukan (misal saat berada di halaman store)
        if (!titikStatus || !teksStatus) return;

        try {
            // Menggunakan API publik mcsrvstat.us
            const response = await fetch(`https://api.mcsrvstat.us/2/${ipServer}`);
            const data = await response.json();

            // Bersihkan class bawaan
            titikStatus.classList.remove('online', 'offline');

            if (data.online) {
                // Jika server ONLINE
                titikStatus.classList.add('online');
                teksStatus.innerHTML = `Server Online - <span style="color: #22c55e; font-weight: 800;">${data.players.online}</span> Pemain`;
            } else {
                // Jika server OFFLINE
                titikStatus.classList.add('offline');
                teksStatus.innerHTML = '<span style="color: #ef4444; font-weight: 800;">Server Offline</span>';
            }
        } catch (error) {
            console.warn("Gagal mengambil data status server:", error);
            // Default ke offline jika gagal fetch API
            titikStatus.classList.add('offline');
            teksStatus.innerHTML = '<span style="color: #ef4444;">Gagal memuat status</span>';
        }
    }

    // Jalankan fungsi cek status saat halaman dimuat dan refresh otomatis tiap 1 menit
    cekStatusServer();
    setInterval(cekStatusServer, 60000);

});