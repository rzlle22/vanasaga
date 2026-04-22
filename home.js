// --- Deklarasi Variabel ---
const mobileMenuBtn = document.getElementById('mobile-menu'); // Ini langsung nargetin <i> icon FontAwesome
const mobileNav = document.getElementById('mobile-nav');

// --- Fungsi Toggle Menu (Buka/Tutup) ---
mobileMenuBtn.addEventListener('click', (e) => {
    // Mencegah klik tembus ke bawah
    e.stopPropagation(); 
    
    mobileNav.classList.toggle('active');
    
    // Ganti icon dari Garis Tiga (bars) jadi Silang (times)
    if (mobileMenuBtn.classList.contains('fa-bars')) {
        mobileMenuBtn.classList.replace('fa-bars', 'fa-times');
    } else {
        mobileMenuBtn.classList.replace('fa-times', 'fa-bars');
    }
});

// --- Fungsi Tutup Menu saat Link Diklik ---
const mobileLinks = document.querySelectorAll('.mobile-nav-links a');
mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
        mobileNav.classList.remove('active');
        mobileMenuBtn.classList.replace('fa-times', 'fa-bars'); // Balikin icon ke garis tiga
    });
});

// --- FITUR BARU: Klik di luar menu untuk menutup ---
document.addEventListener('click', (e) => {
    // Kalau menu lagi kebuka, dan yang diklik BUKAN area menu & BUKAN tombol iconnya
    if (mobileNav.classList.contains('active') && !mobileNav.contains(e.target) && e.target !== mobileMenuBtn) {
        mobileNav.classList.remove('active');
        mobileMenuBtn.classList.replace('fa-times', 'fa-bars');
    }
});

// --- Fungsi Copy IP & Toast Notification ---
function copyIP(ip, port = null) {
    let textToCopy = ip;
    if (port) {
        textToCopy = `${ip} (Port: ${port})`;
    }

    navigator.clipboard.writeText(textToCopy).then(() => {
        showToast();
    }).catch(err => {
        console.error('Gagal menyalin IP: ', err);
    });
}

function showToast() {
    const toast = document.getElementById('toast');
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// --- Fetch Real Server Status ---
document.addEventListener("DOMContentLoaded", () => {
    const statusText = document.getElementById('server-status-text');
    const statusDot = document.getElementById('server-dot');
    
    // IP Server kamu
    const serverIP = 'vanasagaid.xyz';

    // Mengambil data real-time menggunakan API dari mcsrvstat.us
    fetch(`https://api.mcsrvstat.us/3/${serverIP}`)
        .then(response => response.json())
        .then(data => {
            if (data.online) {
                // JIKA SERVER ON
                // Mengambil angka real dari API
                const playersOnline = data.players.online; 
                
                statusText.innerHTML = `<strong style="color:white;">${playersOnline}</strong> Players Online`;
                statusDot.style.backgroundColor = "var(--success)"; // Hijau
                statusDot.style.boxShadow = "0 0 10px var(--success)";
            } else {
                // JIKA SERVER OFF
                statusText.innerText = "Server Offline";
                statusDot.style.backgroundColor = "#ef4444"; // Merah
                statusDot.style.boxShadow = "0 0 10px #ef4444"; // Glow Merah
            }
        })
        .catch(error => {
            // JIKA API GAGAL DIMUAT (Misal koneksi internet user jelek)
            console.error('Error memuat status server:', error);
            statusText.innerText = "Gagal memuat status";
            statusDot.style.backgroundColor = "#f59e0b"; // Warna Orange peringatan
            statusDot.style.boxShadow = "0 0 10px #f59e0b";
        });
});
