/* =============================================
   VANASAGA ID — store.js v3.0
   Loads AFTER home.js (which handles navbar, drawer, particles)
   ============================================= */
(function () {
  'use strict';

  /* ══ TAB NAVIGATION ══ */
  const tabBtns    = document.querySelectorAll('.tab-btn');
  const tabPanels  = document.querySelectorAll('.tab-panel, .tab-content');

  function openTab(targetId, clickedBtn) {
    // Support both data-tab attribute and inline onclick param
    const id = targetId || (clickedBtn && clickedBtn.dataset.tab);
    if (!id) return;

    // Deactivate all
    tabPanels.forEach(p => p.classList.remove('active'));
    tabBtns.forEach(b => b.classList.remove('active'));

    // Activate target
    const target = document.getElementById(id);
    if (target) {
      target.classList.add('active');
      // Re-trigger card animations
      target.querySelectorAll('.product-card').forEach((card, i) => {
        card.style.animationDelay = (i * 0.07) + 's';
        card.style.animation = 'none';
        card.offsetHeight; // reflow
        card.style.animation = '';
      });
    }

    // Activate clicked btn
    if (clickedBtn) {
      clickedBtn.classList.add('active');
    } else {
      // find by data-tab
      tabBtns.forEach(b => { if (b.dataset.tab === id) b.classList.add('active'); });
    }
  }

  // Expose globally for inline onclick support in store.html
  window.openTab = openTab;

  // Also support data-tab attribute pattern
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.tab;
      if (id) openTab(id, btn);
    });
  });

  /* ══ OPEN FIRST TAB ══ */
  const firstPanel = document.querySelector('.tab-panel, .tab-content');
  const firstBtn   = document.querySelector('.tab-btn');
  if (firstPanel && !firstPanel.classList.contains('active')) {
    firstPanel.classList.add('active');
  }
  if (firstBtn && !firstBtn.classList.contains('active')) {
    firstBtn.classList.add('active');
  }

  /* ══ URL HASH → TAB ══ */
  const hash = window.location.hash.replace('#', '');
  if (hash) {
    const panel = document.getElementById(hash);
    if (panel && (panel.classList.contains('tab-panel') || panel.classList.contains('tab-content'))) {
      openTab(hash);
    }
  }

  /* ══ CARD HOVER GLOW EFFECT ══ */
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect = card.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width  * 100).toFixed(1);
      const y = ((e.clientY - rect.top)  / rect.height * 100).toFixed(1);
      card.style.setProperty('--mx', x + '%');
      card.style.setProperty('--my', y + '%');
    });
  });

})();
