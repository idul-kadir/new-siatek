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
                        borderWidth: 1
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

// ============= SIDEBAR TOGGLE (mobile) =============
document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.getElementById('hamburgerBtn');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    function toggleSidebar(show) {
        if (!sidebar) return;
        if (show === undefined) show = sidebar.classList.contains('-translate-x-full');
        sidebar.classList.toggle('-translate-x-full', !show);
        if (overlay) overlay.classList.toggle('hidden', !show);
    }
    if (hamburger) hamburger.addEventListener('click', function () { toggleSidebar(true); });
    if (overlay)  overlay.addEventListener('click',  function () { toggleSidebar(false); });
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
});
