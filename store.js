/* =========================================
   VANASAGA STORE ENGINE - FULL STATIC
   ========================================= */

document.addEventListener("DOMContentLoaded", () => {
    const storeGrid = document.getElementById("storeGrid");
    
    // --- 1. DATA RANK LENGKAP ---
    const dataRanks = [
        {
            nama_rank: "VISCOUNT",
            tipe: "Trial 30 Hari",
            harga: 5000,
            benefit: "/heal, /feed, /craft, Viscount Kit (7d), 100K Money, 10 PlayerVault, 1K ClaimBlock"
        },
        {
            nama_rank: "ARCHDUKE",
            tipe: "Trial 30 Hari",
            harga: 25000,
            benefit: "/fly, /heal, /feed, /craft, /anvil, /smithingtable, /enderchest, Archduke Kit (7d), 250K Money, 20 PlayerVault, 2.5K ClaimBlock"
        },
        {
            nama_rank: "ROYALTY",
            tipe: "Permanent",
            harga: 60000,
            benefit: "/fly, /heal, /feed, /craft, /anvil, /smithingtable, /enderchest, /repair, /tp, /enchant, /lightning, Royalty Kit (7d), 500K Money, 40 PlayerVault, 10K ClaimBlock"
        },
        {
            nama_rank: "EMPEROR",
            tipe: "Permanent",
            harga: 120000,
            benefit: "/fly, /heal, /feed, /craft, /anvil, /smithingtable, /enderchest, /repair, /repairall, /tp, /enchant, /lightning, /vsh, /dmsp, /near, Emperor Kit (7d), 700K Money, 50 PlayerVault, 25K ClaimBlock"
        },
        {
            nama_rank: "OVERLORD",
            tipe: "Permanent",
            harga: 200000,
            benefit: "/fly, /heal, /feed, /anvil, /smithingtable, /repairall, /enchant, /vsh, /weather, /lightning, /invsee, /dmsp, /glow, /rage, /chent, /time, Overlord Kit (7d), 2M Money, 75 PlayerVault, 50K ClaimBlock"
        },
        {
            nama_rank: "GODKING",
            tipe: "Permanent",
            harga: 350000,
            benefit: "/fly, /heal, /feed, /anvil, /smithingtable, /repairall, /enchant, /vsh, /weather, /lightning, /invsee, /dmsp, /glow, /rage, /chent, /time, /ignite, /dmc, /kill, /tpprem, GodKing Kit (7d), 5M Money, 100 PlayerVault, 100K ClaimBlock"
        }
    ];

    // --- 2. RENDER CARD RANK KE HTML ---
    if (!storeGrid) return;
    storeGrid.innerHTML = ""; 
    
    dataRanks.forEach((rank, index) => {
        const safeBenefit = rank.benefit.replace(/'/g, "\\'"); 
        const card = `
            <div class="store-card slide-up" style="animation-delay: ${0.1 * index}s">
                <div class="store-icon floating"><i class="fa fa-crown"></i></div>
                <h3 class="rank-name">${rank.nama_rank}</h3>
                <div class="rank-badge" style="display: inline-block; background: rgba(168, 85, 247, 0.2); color: #a855f7; padding: 4px 15px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; margin-bottom: 15px;">${rank.tipe.toUpperCase()}</div>
                <button class="btn-see" onclick="window.openFeatures('${rank.nama_rank}', '${safeBenefit}')">
                    <i class="fa fa-list-ul"></i> SEE FEATURES
                </button>
                <div class="price">Rp ${new Intl.NumberFormat('id-ID').format(rank.harga)}</div>
                <button class="btn-buy" onclick="window.checkout('${rank.nama_rank}', ${rank.harga})">
                    <i class="fa fa-shopping-cart"></i> CHECKOUT
                </button>
            </div>
        `;
        storeGrid.insertAdjacentHTML('beforeend', card);
    });
});

// --- 3. MODAL FEATURES ---
window.openFeatures = function(name, benefits) {
    const modal = document.getElementById('featureModal');
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');

    if (modal && title && body) {
        title.innerHTML = `<span style="color: #a855f7; font-size: 0.8rem; text-transform: uppercase; display: block; opacity: 0.8;">Rank Benefits</span> ${name}`;
        
        const benefitArray = benefits.split(',').map(b => b.trim()).filter(b => b !== "");
        
        const benefitListHTML = benefitArray.map(item => `
            <li class="benefit-item">
                <i class="fas fa-check-circle"></i>
                <span>${item}</span>
            </li>
        `).join('');
        
        body.innerHTML = benefitArray.length > 0 ? `
            <div class="custom-scroll">
                <ul class="benefit-grid">
                    ${benefitListHTML}
                </ul>
            </div>` : `<p style="text-align:center; color:#666; padding: 20px;">Detail fitur segera hadir.</p>`;
        
        modal.classList.add('show');
    }
}

window.closeModal = function() { document.getElementById('featureModal')?.classList.remove('show'); }

// --- 4. LOGIKA CHECKOUT LANGSUNG KE WHATSAPP ---
window.checkout = function(rankName, price) {
    const nomorWA = "6285136735426";
    const hargaFormat = new Intl.NumberFormat('id-ID').format(price);
    
    const pesan = `Halo Admin Vanasaga, saya tertarik untuk membeli Rank ${rankName} seharga Rp ${hargaFormat}. Bagaimana cara pembayarannya?`;
    const linkWA = `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`;
    
    window.open(linkWA, '_blank');
}