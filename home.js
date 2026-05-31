/* =============================================
   VANASAGA ID — home.js  (shared ALL pages)
   v3.1 — bug-fixed, error-proof
   ============================================= */
(function () {
  'use strict';

  /* ── helper: safe querySelector ── */
  function qs(sel)  { return document.querySelector(sel); }
  function qsa(sel) { return Array.from(document.querySelectorAll(sel)); }

  /* ══ Scroll Progress Bar ══ */
  var prog = document.getElementById('scroll-progress');
  if (prog) {
    function onScrollProg() {
      var el  = document.documentElement;
      var top = el.scrollTop || document.body.scrollTop;
      var h   = el.scrollHeight - el.clientHeight;
      prog.style.width = (h > 0 ? (top / h * 100) : 0) + '%';
    }
    window.addEventListener('scroll', onScrollProg, { passive: true });
    onScrollProg();
  }

  /* ══ Navbar scroll ══ */
  var nav = qs('.navbar');
  if (nav) {
    function onNavScroll() {
      if (window.scrollY > 40) {
        nav.classList.add('scrolled');
      } else {
        nav.classList.remove('scrolled');
      }
    }
    window.addEventListener('scroll', onNavScroll, { passive: true });
    onNavScroll();
  }

  /* ══ Mobile drawer ══
     Try both class names used in index.html and store.html */
  var ham      = qs('.hamburger');
  var drawer   = qs('.drawer') || qs('.mobile-drawer');
  var backdrop = qs('.drawer-back') || qs('.drawer-backdrop');

  if (ham && drawer && backdrop) {
    function drawerOpen() {
      ham.classList.add('open');
      drawer.classList.add('on');
      backdrop.classList.add('on');
      document.body.style.overflow = 'hidden';
    }
    function drawerClose() {
      ham.classList.remove('open');
      drawer.classList.remove('on');
      backdrop.classList.remove('on');
      document.body.style.overflow = '';
    }
    function drawerToggle(e) {
      e.stopPropagation();
      if (drawer.classList.contains('on')) {
        drawerClose();
      } else {
        drawerOpen();
      }
    }

    ham.addEventListener('click', drawerToggle);
    ham.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        drawerToggle(e);
      }
    });
    backdrop.addEventListener('click', drawerClose);
    qsa('a', drawer).forEach(function (a) {
      a.addEventListener('click', drawerClose);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') drawerClose();
    });
  }

  /* ══ Copy IP ══ */
  window.copyIP = function (ip) {
    function done() { window.showToast('\u2713 ' + ip + ' berhasil disalin!'); }
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(ip).then(done).catch(function () { fallbackCopy(ip, done); });
    } else {
      fallbackCopy(ip, done);
    }
  };

  function fallbackCopy(text, cb) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity  = '0';
    ta.style.top      = '-999px';
    ta.style.left     = '-999px';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); cb(); } catch (err) { /* silent */ }
    document.body.removeChild(ta);
  }

  /* ══ Toast ══ */
  window.showToast = function (msg) {
    var t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg || '\u2713 Done!';
    t.classList.add('show');
    clearTimeout(t._vanasagaTimer);
    t._vanasagaTimer = setTimeout(function () { t.classList.remove('show'); }, 2800);
  };

  /* ══ Server Status (home page only) ══ */
  var sTxt = document.getElementById('sv-text');
  var sDot = document.getElementById('sv-dot');
  
  if (sTxt && sDot) {
    function checkServerStatus() {
      fetch('https://api.mcsrvstat.us/3/vanasaga.com')
        .then(function (r) {
          if (!r.ok) throw new Error('network');
          return r.json();
        })
        .then(function (d) {
          if (d && d.online) {
            var on = (d.players && d.players.online != null) ? d.players.online : 0;
            var mx = (d.players && d.players.max    != null) ? d.players.max    : '?';
            sTxt.innerHTML = '<strong style="color:var(--tx)">' + on + '</strong>/' + mx + ' Pemain Online';
            // Reset style dot jika server kembali online
            sDot.style.cssText = ''; 
          } else {
            sTxt.textContent = 'Server Offline';
            sDot.style.cssText = 'background:#f87171;box-shadow:0 0 7px #f87171;animation:none';
          }
        })
        .catch(function () {
          sTxt.textContent = 'Status tidak tersedia';
          sDot.style.cssText = 'background:var(--gold);box-shadow:0 0 7px var(--gold);animation:none';
        });
    }

    // 1. Panggil fungsinya saat halaman pertama kali dimuat
    checkServerStatus();

    // 2. Jalankan ulang fungsinya secara otomatis setiap X milidetik
    // 30000 = 30 detik. Silakan ubah angkanya sesuai kebutuhan (10000 = 10 detik).
    setInterval(checkServerStatus, 30000);
  }

  /* ══ Intersection Observer — scroll reveal ══ */
  var revealEls = qsa('.reveal');
  if (revealEls.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
      revealEls.forEach(function (el) { io.observe(el); });
    } else {
      revealEls.forEach(function (el) { el.classList.add('in'); });
    }
  }

  /* ══ Back to Top ══ */
  var btt = document.getElementById('back-top');
  if (btt) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) {
        btt.classList.add('show');
      } else {
        btt.classList.remove('show');
      }
    }, { passive: true });
    btt.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ══ Particle Canvas (desktop only — saves mobile perf) ══ */
  var canvas = document.getElementById('bg-canvas');
  if (canvas && window.innerWidth > 768) {
    var ctx  = canvas.getContext('2d');
    var COLS = ['#c0a3ff', '#b0b8ff', '#f5c842', '#4ade80', '#67e8f9', '#f472b6'];
    var BS   = 7;
    var W, H, pts, raf;
    var last = 0;

    /* mkPt declared BEFORE resize so resize can call it */
    function mkPt(bot) {
      return {
        x  : Math.random() * W,
        y  : bot ? H + BS : Math.random() * H,
        vx : (Math.random() - 0.5) * 0.22,
        vy : -(Math.random() * 0.32 + 0.07),
        c  : COLS[Math.floor(Math.random() * COLS.length)],
        a  : Math.random() * 0.18 + 0.04,
        s  : Math.floor(BS * (0.5 + Math.random() * 0.5))
      };
    }

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
      pts = [];
      for (var i = 0; i < 28; i++) pts.push(mkPt(false));
    }

    function tick(ts) {
      raf = requestAnimationFrame(tick);
      if (ts - last < 42) return;
      last = ts;
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < pts.length; i++) {
        var p = pts[i];
        p.x += p.vx;
        p.y += p.vy;
        if (p.y < -BS * 2 || p.x < -20 || p.x > W + 20) {
          pts[i] = mkPt(true);
          continue;
        }
        ctx.globalAlpha = p.a;
        ctx.fillStyle   = p.c;
        ctx.fillRect(Math.floor(p.x), Math.floor(p.y), p.s, p.s);
      }
      ctx.globalAlpha = 1;
    }

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        cancelAnimationFrame(raf);
      } else {
        last = 0;
        raf  = requestAnimationFrame(tick);
      }
    });

    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(resize, 200);
    }, { passive: true });

    resize();
    raf = requestAnimationFrame(tick);
  }

  /* helper used above */
  function qsa(sel, root) {
    return Array.from((root || document).querySelectorAll(sel));
  }

})();
