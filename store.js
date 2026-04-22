// ==========================================
// FUNGSI TAB NAVIGASI (STORE.JS)
// ==========================================
function openTab(tabId) {
    // 1. Sembunyikan semua isi tab
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => {
        content.classList.remove('active');
    });

    // 2. Hilangkan warna aktif dari semua tombol
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    // 3. Munculkan tab konten yang baru dipilih
    const targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.classList.add('active');
    }

    // 4. Beri warna aktif pada tombol yang sedang diklik
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('active');
    }
}
