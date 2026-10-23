/**
 * SIATEK — Dashboard JS (Beranda, Modal TTD)
 * Sumber kebenaran warna: index.php (sidebar/topnav) dan CSS variable.
 */
(function () {
    'use strict';

    // ============= CHART DATA (single source) =============
    var data = window.__berandaData || null;

    // ============= CHART COLORS (tema terang / white cards) =============
    var orange      = '#f97316';
    var orangeLight = '#fdba74';
    var navy        = '#1a365d';
    var tooltipBg   = '#0f172a';
    var gridLine    = '#eef2f7';               // grid halus di atas kartu putih
    var tickColor   = '#64748b';

    // ============= ENROLLMENT LINE CHART =============
    var enrollCanvas = document.getElementById('enrollmentChart');
    if (enrollCanvas && data && data.enrollment) {
        // Build gradient fill from canvas height
        var ctxE = enrollCanvas.getContext('2d');
        var gradOrange = ctxE.createLinearGradient(0, 0, 0, 260);
        gradOrange.addColorStop(0, 'rgba(249,115,22,0.30)');
        gradOrange.addColorStop(1, 'rgba(249,115,22,0.00)');

        new Chart(ctxE, {
            type: 'line',
            data: {
                labels: data.enrollment.labels,
                datasets: [{
                    label: 'Mahasiswa Baru',
                    data: data.enrollment.values,
                    borderColor: orange,
                    backgroundColor: gradOrange,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: orange,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: orange
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        borderColor: 'rgba(249,115,22,0.35)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (c) {
                                return ' Mahasiswa Baru: ' + c.parsed.y + ' orang';
                            }
                        }

                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridLine, drawBorder: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ============= ALUMNI LINE CHART (TREN KELULUSAN 5 TAHUN) =============
    var alumniCanvas = document.getElementById('alumniChart');
    if (alumniCanvas && data && data.alumni) {
        var ctxA = alumniCanvas.getContext('2d');
        var skyBlue = '#0ea5e9';
        var gradSky = ctxA.createLinearGradient(0, 0, 0, 260);
        gradSky.addColorStop(0, 'rgba(14,165,233,0.30)');
        gradSky.addColorStop(1, 'rgba(14,165,233,0.00)');

        new Chart(ctxA, {
            type: 'line',
            data: {
                labels: data.alumni.labels,
                datasets: [{
                    label: 'Kelulusan / Alumni',
                    data: data.alumni.values,
                    borderColor: skyBlue,
                    backgroundColor: gradSky,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: skyBlue,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: skyBlue
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        borderColor: 'rgba(14,165,233,0.35)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (c) {
                                return ' Kelulusan: ' + c.parsed.y + ' orang';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridLine, drawBorder: false },
                        ticks: { 
                            color: tickColor, 
                            font: { size: 11 },
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // ============= CONCENTRATION DOUGHNUT =============
    var concCanvas = document.getElementById('concentrationChart');
    if (concCanvas && data && data.concentration && data.concentration.values.length > 0) {
        var totalConc = data.concentration.values.reduce(function (a, b) { return a + b; }, 0);
        // Warna per prodi diteruskan dari PHP (Elektro=navy, Komputer=orange, Pendidikan=emerald);
        // fallback palet terang bila data warna tidak tersedia.
        var fallbackConc = ['#1a365d', '#f97316', '#10b981', '#38bdf8', '#fbbf24', '#8b5cf6'];
        var concColors = (data.concentration.colors && data.concentration.colors.length)
            ? data.concentration.colors
            : fallbackConc;
        var centerLabel = {
            id: 'centerLabel',
            afterDraw: function (chart) {
                var ctx = chart.ctx;
                var meta = chart.getDatasetMeta(0);
                if (!meta.data.length) return;
                var x = meta.data[0].x;
                var y = meta.data[0].y;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#94a3b8';
                ctx.font = '500 11px Inter, sans-serif';
                ctx.fillText('Total', x, y - 16);
                ctx.fillStyle = '#0f172a';
                ctx.font = '700 20px Inter, sans-serif';
                ctx.fillText(totalConc.toLocaleString('id-ID'), x, y + 5);
                ctx.restore();
            }
        };
        new Chart(concCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.concentration.labels,
                datasets: [{
                    data: data.concentration.values,
                    backgroundColor: concColors,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#475569',
                            font: { size: 11 },
                            padding: 12,
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        borderColor: 'rgba(38,164,232,0.35)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (c) {
                                var v = c.parsed;
                                var pct = totalConc > 0 ? ((v / totalConc) * 100).toFixed(1) : 0;
                                return ' ' + c.label + ': ' + v.toLocaleString('id-ID') + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            },
            plugins: [centerLabel]
        });
    }

    // ============= MODAL TTD =============
    function showModal() {
        var m = document.getElementById('ttdModal');
        if (m) m.classList.add('show');
    }
    function hideModal() {
        var m = document.getElementById('ttdModal');
        if (m) m.classList.remove('show');
    }
    document.querySelectorAll('[data-modal-open]').forEach(function (el) {
        el.addEventListener('click', showModal);
    });
    document.querySelectorAll('[data-modal-close]').forEach(function (el) {
        el.addEventListener('click', hideModal);
    });

    // ============= SIGNATURE PAD =============
    var canvas = document.getElementById('signatureCanvas');
    if (canvas) {
        var ctx = canvas.getContext('2d');
        function resizePad() {
            var rect = canvas.getBoundingClientRect();
            canvas.width  = rect.width;
            canvas.height = rect.height;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#0d172a';
        }
        resizePad();
        window.addEventListener('resize', resizePad);

        var drawing = false;
        function getXY(e) {
            var r = canvas.getBoundingClientRect();
            var src = (e.touches && e.touches[0]) || e;
            return [src.clientX - r.left, src.clientY - r.top];
        }
        canvas.addEventListener('mousedown', function (e) {
            drawing = true;
            var p = getXY(e);
            ctx.beginPath();
            ctx.moveTo(p[0], p[1]);
        });
        canvas.addEventListener('mousemove', function (e) {
            if (!drawing) return;
            var p = getXY(e);
            ctx.lineTo(p[0], p[1]);
            ctx.stroke();
        });
        canvas.addEventListener('mouseup', function () { drawing = false; });
        canvas.addEventListener('mouseleave', function () { drawing = false; });

        var clearBtn = document.getElementById('clearSignature');
        if (clearBtn) clearBtn.addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        var confirmBtn = document.getElementById('confirmSignature');
        if (confirmBtn) confirmBtn.addEventListener('click', function () {
            confirmBtn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Tersimpan!';
            confirmBtn.classList.remove('bg-[#1a365d]');
            confirmBtn.classList.add('bg-green-600');
            setTimeout(function () {
                hideModal();
                confirmBtn.innerHTML = 'Tanda Tangani';
                confirmBtn.classList.remove('bg-green-600');
                confirmBtn.classList.add('bg-[#1a365d]');
            }, 1000);
        });
    }
})();

// ============= SIDEBAR TOGGLE (desktop collapse + mobile drawer) =============
// Desktop: sembunyikan/tampilkan sidebar supaya lembar kerja lebih lebar (tersimpan di localStorage).
// Mobile : sidebar "peek" terbuka sebentar lalu menutup ke arah tombol saat halaman dimuat;
//          tombol jadi navy + efek kelip sebagai ajakan klik, dan berubah abu saat sidebar terbuka.
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var closeBtn = document.getElementById('sidebarCloseBtn');
    var sidebarToggle = document.getElementById('sidebarToggle');

    // Ikon modern: hamburger (fa-bars-staggered) <=> X (fa-xmark), dengan pop halus.
    function setIcon(open) {
        if (!sidebarToggle) return;
        var icon = sidebarToggle.querySelector('i');
        if (!icon) return;
        icon.className = 'fa-solid ' + (open ? 'fa-xmark' : 'fa-bars-staggered');
        icon.classList.remove('icon-pop');
        void icon.offsetWidth;
        icon.classList.add('icon-pop');
    }

    // ---- Desktop: collapse sidebar (lembar kerja melebar) ----
    // Selalu terbuka di awal; tersembunyi hanya jika user klik tombol.
    function setCollapsed(collapsed) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        setIcon(!collapsed);
        if (sidebarToggle) {
            sidebarToggle.setAttribute('title', collapsed ? 'Tampilkan menu samping' : 'Sembunyikan menu samping');
            sidebarToggle.setAttribute('aria-label', collapsed ? 'Tampilkan menu samping' : 'Sembunyikan menu samping');
        }
    }

    // ---- Mobile: buka/tutup drawer ----
    function setMobileSidebar(open) {
        if (sidebar) sidebar.classList.toggle('mobile-open', open);
        if (overlay) overlay.classList.toggle('show', open);
        document.body.classList.toggle('mobile-nav-open', open);
        setIcon(open);
    }

    // ---- Peek sekali di layar kecil: buka sebentar lalu menutup ke arah tombol ----
    function mobilePeek() {
        if (!sidebar || window.innerWidth >= 768) return;
        setTimeout(function () {
            sidebar.classList.add('mobile-open');
            setTimeout(function () {
                sidebar.classList.remove('mobile-open');
            }, 1400);
        }, 600);
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth >= 768) {
                setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
            } else {
                var isOpen = sidebar ? sidebar.classList.contains('mobile-open') : false;
                setMobileSidebar(!isOpen);
            }
        });
    }
    if (overlay)  overlay.addEventListener('click',  function () { setMobileSidebar(false); });
    if (closeBtn) closeBtn.addEventListener('click', function () { setMobileSidebar(false); });

    mobilePeek();
});

// ============= NAV SUBMENU =============
// Submenu memakai accordion berbasis `max-height` (lihat .nav-submenu di index.php).
// Toggle class `open` (bukan `hidden`) agar sinkron dengan CSS dan bisa expand/collapse.
// Pakai event delegation agar tetap berfungsi walau konten sidebar dimuat ulang.
document.addEventListener('DOMContentLoaded', function () {
    function toggleSubmenu(btn, sub) {
        var isOpen = sub.classList.contains('open');
        sub.classList.toggle('open', !isOpen);
        btn.classList.toggle('open', !isOpen);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.nav-parent, .sub-parent');
        if (!btn) return;
        var target = btn.getAttribute('data-target');
        if (!target) return;
        var sub = document.getElementById(target);
        if (!sub) return;
        toggleSubmenu(btn, sub);
    });

    // Auto-ensure active parents for active menu items
    var activeItem = document.querySelector('#sidebar .nav-active');
    if (activeItem) {
        var nestedSub = activeItem.closest('.nav-submenu--nested');
        if (nestedSub) {
            nestedSub.classList.add('open');
            var subParent = document.querySelector('[data-target="' + nestedSub.id + '"]');
            if (subParent) {
                subParent.classList.add('open', 'has-active');
            }
        }
        var rootSub = activeItem.closest('.nav-submenu');
        if (rootSub) {
            rootSub.classList.add('open');
            var rootParent = document.querySelector('[data-target="' + rootSub.id + '"]'); if (rootParent) rootParent.classList.add('open', 'has-active'); } } }); /*
            if (rootParent) {
                rootParent.classList.add('open', 'has-active');
            }
        }
    }
    });
});

*/ // ============= NOTIFIKASI VERIFIKASI BERKAS RINGAN (BADGE SIDEBAR + WEB AUDIO) =============
(function () {
    'use strict';

    var SESSION_STATE_KEY = 'siatek_notif_verifikasi_state_v3';
    var POLL_VISIBLE_MS   = 4000; // 4 detik saat tab aktif
    var POLL_HIDDEN_MS    = 8000; // 8 detik saat tab di latar belakang

    var audioCtx     = null;
    var pendingSound = false;
    var isFetching   = false;
    var lastCheckTs  = 0;
    var pollTimer    = null;

    function ensureAudioContext() {
        var AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return null;
        if (!audioCtx) {
            try {
                audioCtx = new AudioContextClass();
            } catch (e) {
                return null;
            }
        }
        return audioCtx;
    }

    // Sintesis bunyi "ting-tong" jernih via Web Audio API (0 KB file eksternal, hemat memori)
    function playNotifChime() {
        var ctx = ensureAudioContext();
        if (!ctx) return;

        try {
            if (ctx.state === 'suspended') {
                ctx.resume().then(function () {
                    pendingSound = false;
                    triggerOscillator(ctx);
                }).catch(function () {
                    pendingSound = true;
                });
                return;
            }
            triggerOscillator(ctx);
        } catch (e) {
            pendingSound = true;
        }
    }

    function triggerOscillator(ctx) {
        try {
            var now = ctx.currentTime;

            // Nada lonceng 3-nada jernih: D5 (587.33 Hz) -> G5 (783.99 Hz) -> C6 (1046.50 Hz)
            var notes = [
                { freq: 587.33,  start: 0,    dur: 0.16, peak: 0.28 },
                { freq: 783.99,  start: 0.13, dur: 0.18, peak: 0.30 },
                { freq: 1046.50, start: 0.27, dur: 0.38, peak: 0.32 }
            ];

            notes.forEach(function (n) {
                var osc  = ctx.createOscillator();
                var gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(n.freq, now + n.start);

                gain.gain.setValueAtTime(0.0001, now + n.start);
                gain.gain.exponentialRampToValueAtTime(n.peak, now + n.start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + n.start + n.dur);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + n.start);
                osc.stop(now + n.start + n.dur + 0.02);
            });
        } catch (e) {}
    }

    // Unlock AudioContext pada interaksi pertama user jika sempat tertahan autoplay browser
    function unlockAudioOnGesture() {
        var ctx = ensureAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume().catch(function () {});
        }
        if (pendingSound) {
            pendingSound = false;
            playNotifChime();
        }
    }
    ['pointerdown', 'mousedown', 'click', 'keydown', 'touchstart'].forEach(function (evt) {
        document.addEventListener(evt, unlockAudioOnGesture, { passive: true });
    });

    function updateSidebarBadge(totalMhs) {
        var sbDot   = document.getElementById('sidebarPengelolaanBadge');
        var sbBadge = document.getElementById('sidebarVerifikasiBadge');

        if (sbDot) {
            sbDot.classList.toggle('hidden', totalMhs === 0);
        }
        if (sbBadge) {
            if (totalMhs > 0) {
                sbBadge.textContent = totalMhs > 99 ? '99+' : String(totalMhs);
                sbBadge.classList.remove('hidden');
                sbBadge.classList.add('inline-flex');
            } else {
                sbBadge.classList.add('hidden');
                sbBadge.classList.remove('inline-flex');
            }
        }
    }

    function getStoredState() {
        try {
            var raw = sessionStorage.getItem(SESSION_STATE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function saveStoredState(state) {
        try {
            sessionStorage.setItem(SESSION_STATE_KEY, JSON.stringify(state));
        } catch (e) {}
    }

    function isAnyModalOpen() {
        var modals = document.querySelectorAll('.modal-overlay.show');
        return modals && modals.length > 0;
    }

    function cekNotifikasiVerifikasi(silentAfterAction) {
        if (isFetching) return;
        isFetching = true;
        lastCheckTs = Date.now();

        var url = (typeof window.SIATEK_VERIFIKASI_PROSES_URL === 'string' && window.SIATEK_VERIFIKASI_PROSES_URL !== '')
            ? window.SIATEK_VERIFIKASI_PROSES_URL
            : '/redesain-siatek/administrator/pages/pengelolaan/mahasiswa/verifikasi/proses.php';

        var formData = new FormData();
        formData.append('cek-notifikasi', '1');

        fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            cache: 'no-store'
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (!res || !res.status) return;

            var totalBerkas = parseInt(res.total_berkas, 10) || 0;
            var totalMhs    = parseInt(res.total_mhs, 10) || 0;
            var maxId       = parseInt(res.max_id, 10) || 0;
            var maxFile     = String(res.max_file || '');
            var fingerprint = String(res.fingerprint || '0');

            // Tampilkan jumlah mahasiswa yang menunggu di samping menu sidebar
            updateSidebarBadge(totalMhs);

            var prev = getStoredState();
            var isFirstCheckInSession = !prev;

            var hasNewDocument = prev && totalMhs > 0 && (
                totalMhs > (prev.total_mhs || 0) ||
                totalBerkas > (prev.total_berkas || 0) ||
                maxId > (prev.max_id || 0) ||
                (maxFile !== '' && maxFile !== (prev.max_file || '')) ||
                (fingerprint !== '0' && fingerprint !== (prev.fingerprint || '0'))
            );

            var countChanged = prev && (
                totalMhs !== (prev.total_mhs || 0) ||
                totalBerkas !== (prev.total_berkas || 0) ||
                fingerprint !== (prev.fingerprint || '0')
            );

            saveStoredState({
                total_berkas: totalBerkas,
                total_mhs: totalMhs,
                max_id: maxId,
                max_file: maxFile,
                fingerprint: fingerprint
            });

            if (silentAfterAction) return;

            if (countChanged && typeof window.muatUlangTabel === 'function' && !isAnyModalOpen()) {
                window.muatUlangTabel();
            }

            if (totalMhs > 0 && (hasNewDocument || isFirstCheckInSession)) {
                playNotifChime();
            }
        })
        .catch(function () {})
        .finally(function () {
            isFetching = false;
        });
    }

    function updateSidebarPenunjukkanBadge(total) {
        var sbBadge = document.getElementById('sidebarPenunjukkanBadge');
        if (sbBadge) {
            if (total > 0) {
                sbBadge.textContent = total > 99 ? '99+' : String(total);
                sbBadge.classList.remove('hidden');
                sbBadge.classList.add('inline-flex');
            } else {
                sbBadge.classList.add('hidden');
                sbBadge.classList.remove('inline-flex');
            }
        }
    }

    function cekNotifikasiPenunjukkan() {
        var sbBadge = document.getElementById('sidebarPenunjukkanBadge');
        if (!sbBadge) return;
        var url = (typeof window.SIATEK_PENUNJUKKAN_PROSES_URL === 'string' && window.SIATEK_PENUNJUKKAN_PROSES_URL !== '')
            ? window.SIATEK_PENUNJUKKAN_PROSES_URL
            : '/redesain-siatek/administrator/pages/pengelolaan/mahasiswa/penunjukkan/proses.php';

        var formData = new FormData();
        formData.append('cek-badge-penunjukkan', '1');

        fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            cache: 'no-store'
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res.status) {
                updateSidebarPenunjukkanBadge(parseInt(res.total_menunggu, 10) || 0);
            }
        })
        .catch(function () {});
    }

    function scheduleNextPoll() {
        if (pollTimer) clearTimeout(pollTimer);
        var delay = document.hidden ? POLL_HIDDEN_MS : POLL_VISIBLE_MS;
        pollTimer = setTimeout(function () {
            cekNotifikasiVerifikasi(false);
            cekNotifikasiPenunjukkan();
            scheduleNextPoll();
        }, delay);
    }

    window.cekNotifikasiVerifikasi = cekNotifikasiVerifikasi;
    window.cekNotifikasiPenunjukkan = cekNotifikasiPenunjukkan;

    document.addEventListener('DOMContentLoaded', function () {
        cekNotifikasiVerifikasi(false);
        cekNotifikasiPenunjukkan();
        scheduleNextPoll();

        document.addEventListener('visibilitychange', function () {
            scheduleNextPoll();
            if (!document.hidden && (Date.now() - lastCheckTs > 2000)) {
                cekNotifikasiVerifikasi(false);
                cekNotifikasiPenunjukkan();
            }
        });
    });
})();

