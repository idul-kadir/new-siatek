/**
 * SIATEK v3.0 — Dashboard JavaScript (Tailwind version)
 *
 * Menangani:
 *   - Sidebar mobile toggle (slide in/out)
 *   - Submenu accordion (smooth open/close)
 *   - Auto-open parent yang punya child aktif
 *   - Chart.js (line + doughnut, data dari window.__berandaData)
 *   - Modal TTD (show/hide manual)
 *   - Signature canvas (mouse + touch)
 */

(function () {
    'use strict';

    /* =========================================================
       1. SIDEBAR TOGGLE (Mobile)
       ========================================================= */
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var hamburger = document.getElementById('hamburgerBtn');
    var sidebarClose = document.getElementById('sidebarCloseBtn');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (sidebar.classList.contains('mobile-open')) closeSidebar();
            else openSidebar();
        });
    }
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    document.querySelectorAll('#sidebar .nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) {
                // jangan close kalau linknya cuma parent (submenu toggle)
                if (link.classList.contains('nav-parent')) return;
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeSidebar();
    });

    /* =========================================================
       2. SUBMENU ACCORDION
       ========================================================= */
    function setMaxHeight(el, value) {
        if (value === null) {
            el.style.maxHeight = '';
        } else {
            el.style.maxHeight = value + 'px';
        }
    }

    document.querySelectorAll('.nav-parent').forEach(function (parent) {
        parent.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = parent.getAttribute('data-target');
            var submenu = document.getElementById(targetId);
            if (!submenu) return;

            // Accordion: tutup submenu lain
            document.querySelectorAll('.nav-parent').forEach(function (other) {
                if (other === parent) return;
                var oid = other.getAttribute('data-target');
                var osub = document.getElementById(oid);
                if (osub && osub.classList.contains('open')) {
                    osub.style.maxHeight = osub.scrollHeight + 'px';
                    osub.offsetHeight; // reflow
                    osub.style.maxHeight = '0px';
                    osub.classList.remove('open');
                    other.classList.remove('open');
                    setTimeout(function () { osub.style.maxHeight = ''; }, 350);
                }
            });

            if (submenu.classList.contains('open')) {
                // close
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                submenu.offsetHeight;
                submenu.style.maxHeight = '0px';
                submenu.classList.remove('open');
                parent.classList.remove('open');
                setTimeout(function () { submenu.style.maxHeight = ''; }, 350);
            } else {
                // open
                submenu.classList.add('open');
                parent.classList.add('open');
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                submenu.addEventListener('transitionend', function fn() {
                    submenu.style.maxHeight = '';
                    submenu.removeEventListener('transitionend', fn);
                });
            }
        });
    });

    document.querySelectorAll('.sub-parent').forEach(function (subParent) {
        subParent.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = subParent.getAttribute('data-target');
            var nested = document.getElementById(targetId);
            if (!nested) return;

            if (nested.classList.contains('open')) {
                nested.style.maxHeight = nested.scrollHeight + 'px';
                nested.offsetHeight;
                nested.style.maxHeight = '0px';
                nested.classList.remove('open');
                subParent.classList.remove('open');
                setTimeout(function () { nested.style.maxHeight = ''; }, 350);
            } else {
                nested.classList.add('open');
                subParent.classList.add('open');
                nested.style.maxHeight = nested.scrollHeight + 'px';
                nested.addEventListener('transitionend', function fn() {
                    nested.style.maxHeight = '';
                    nested.removeEventListener('transitionend', fn);
                });
            }
        });
    });

    window.addEventListener('resize', function () {
        document.querySelectorAll('.nav-submenu.open, .nav-submenu--nested.open').forEach(function (sub) {
            sub.style.maxHeight = '';
        });
    });

    /* =========================================================
       3. AUTO-OPEN PARENT YANG PUNYA CHILD AKTIF
       ========================================================= */
    function autoOpenActiveParent() {
        // Untuk nav-submenu utama (.sub-item.nav-active)
        document.querySelectorAll('.nav-submenu .sub-item.nav-active').forEach(function (activeItem) {
            var submenu = activeItem.closest('.nav-submenu');
            if (submenu && !submenu.classList.contains('open')) {
                submenu.classList.add('open');
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                setTimeout(function () { submenu.style.maxHeight = ''; }, 350);
            }
            var parentId = submenu ? submenu.id : '';
            if (parentId) {
                var parentBtn = document.querySelector('.nav-parent[data-target="' + parentId + '"]');
                if (parentBtn && !parentBtn.classList.contains('open')) {
                    parentBtn.classList.add('open');
                }
                if (parentBtn) parentBtn.classList.add('has-active');
            }
        });

        // Untuk nested (.sub-item.nav-active di dalam .nav-submenu--nested)
        document.querySelectorAll('.nav-submenu--nested .sub-item.nav-active').forEach(function (activeItem) {
            var nested = activeItem.closest('.nav-submenu--nested');
            if (nested && !nested.classList.contains('open')) {
                nested.classList.add('open');
                nested.style.maxHeight = nested.scrollHeight + 'px';
                setTimeout(function () { nested.style.maxHeight = ''; }, 350);
            }
            var nestedId = nested ? nested.id : '';
            if (nestedId) {
                var subParentBtn = document.querySelector('.sub-parent[data-target="' + nestedId + '"]');
                if (subParentBtn && !subParentBtn.classList.contains('open')) {
                    subParentBtn.classList.add('open');
                }
                if (subParentBtn) subParentBtn.classList.add('has-active');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoOpenActiveParent);
    } else {
        autoOpenActiveParent();
    }

    /* =========================================================
       4. CHART.JS
       ========================================================= */
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
    Chart.defaults.plugins.legend.labels.padding = 14;

    var orange = '#f97316';
    var navy   = '#1a365d';
    var slateLight = '#e2e8f0';

    /* 4a. Enrollment Trend (Line Chart) */
    (function () {
        var ctx = document.getElementById('enrollmentChart');
        if (!ctx) return;

        var labels = ['2020','2021','2022','2023','2024'];
        var datasetLabels = ['Total'];
        var values = [[150, 165, 180, 175, 172]];

        if (window.__berandaData && window.__berandaData.enrollment) {
            var ed = window.__berandaData.enrollment;
            if (ed.labels && ed.labels.length) labels = ed.labels;
            if (ed.datasetLabels && ed.datasetLabels.length) datasetLabels = ed.datasetLabels;
            if (ed.values && ed.values.length) values = ed.values;
        }

        // Build line chart: gabungkan semua prodi jadi total
        var totalPerYear = labels.map(function (_, i) {
            var sum = 0;
            values.forEach(function (vals) { sum += (vals[i] || 0); });
            return sum;
        });

        var ctxObj = ctx.getContext('2d');
        var gradient = ctxObj.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(249, 115, 22, 0.15)');
        gradient.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

        new Chart(ctxObj, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Mahasiswa Baru',
                    data: totalPerYear,
                    borderColor: orange,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: orange,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: slateLight, borderDash: [5, 5] }, ticks: { color: '#64748b' } },
                    x: { grid: { display: false }, ticks: { color: '#64748b' } }
                }
            }
        });
    })();

    /* 4b. Distribusi Konsentrasi (Doughnut) */
    (function () {
        var ctx = document.getElementById('concentrationChart');
        if (!ctx) return;

        var labels = ['Tenaga Listrik','Telekomunikasi','Kendali','Elektronika'];
        var values = [35, 25, 20, 20];
        if (window.__berandaData && window.__berandaData.concentration) {
            var cd = window.__berandaData.concentration;
            if (cd.labels && cd.labels.length) labels = cd.labels;
            if (cd.values && cd.values.length) values = cd.values;
        }

        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [orange, navy, '#10b981', '#94a3b8', '#6366f1', '#dc2626', '#7c3aed'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, font: { size: 11 }, color: '#475569', usePointStyle: true, boxWidth: 8 }
                    }
                }
            }
        });
    })();

    /* =========================================================
       5. MODAL TTD (Tailwind, tanpa Bootstrap)
       ========================================================= */
    var signModal = document.getElementById('signModal');
    var signatureCanvas = document.getElementById('signatureCanvas');
    var clearBtn = document.getElementById('clearSignature');
    var confirmBtn = document.getElementById('confirmSignature');

    function showModal() {
        if (!signModal) return;
        signModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(initSignaturePad, 50);
    }
    function hideModal() {
        if (!signModal) return;
        signModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-modal-open="signModal"], [data-bs-target="#signModal"], .btn-signature').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            showModal();
        });
    });
    document.querySelectorAll('[data-modal-close], [data-bs-dismiss="modal"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            hideModal();
        });
    });
    if (signModal) {
        signModal.addEventListener('click', function (e) {
            if (e.target === signModal) hideModal();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && signModal && signModal.classList.contains('show')) hideModal();
    });

    /* 5a. Signature pad */
    var sigCtx, drawing = false, lastX = 0, lastY = 0;
    function initSignaturePad() {
        if (!signatureCanvas) return;
        if (sigCtx) return;
        // Resize canvas to its CSS size
        var rect = signatureCanvas.getBoundingClientRect();
        signatureCanvas.width = rect.width;
        signatureCanvas.height = rect.height;
        sigCtx = signatureCanvas.getContext('2d');
        sigCtx.lineWidth = 2;
        sigCtx.lineCap = 'round';
        sigCtx.strokeStyle = '#1a365d';

        function getPos(evt) {
            var rect = signatureCanvas.getBoundingClientRect();
            var t = evt.touches ? evt.touches[0] : evt;
            return { x: t.clientX - rect.left, y: t.clientY - rect.top };
        }
        function start(e) {
            e.preventDefault();
            drawing = true;
            var p = getPos(e);
            lastX = p.x; lastY = p.y;
        }
        function move(e) {
            if (!drawing) return;
            e.preventDefault();
            var p = getPos(e);
            sigCtx.beginPath();
            sigCtx.moveTo(lastX, lastY);
            sigCtx.lineTo(p.x, p.y);
            sigCtx.stroke();
            lastX = p.x; lastY = p.y;
        }
        function end() { drawing = false; }

        signatureCanvas.addEventListener('mousedown', start);
        signatureCanvas.addEventListener('mousemove', move);
        signatureCanvas.addEventListener('mouseup', end);
        signatureCanvas.addEventListener('mouseleave', end);
        signatureCanvas.addEventListener('touchstart', start, { passive: false });
        signatureCanvas.addEventListener('touchmove', move, { passive: false });
        signatureCanvas.addEventListener('touchend', end);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (!sigCtx || !signatureCanvas) return;
            sigCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        });
    }
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            confirmBtn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Tersimpan!';
            confirmBtn.classList.remove('bg-[#1a365d]');
            confirmBtn.classList.add('bg-green-600');
            setTimeout(function () {
                hideModal();
                confirmBtn.innerHTML = 'Tanda Tangani';
                confirmBtn.classList.remove('bg-green-600');
                confirmBtn.classList.add('bg-[#1a365d]');
                if (sigCtx && signatureCanvas) sigCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
            }, 1200);
        });
    }

    /* =========================================================
       6. EXPORT BUTTON (placeholder)
       ========================================================= */
    var btnExport = document.getElementById('btnExport');
    if (btnExport) {
        btnExport.addEventListener('click', function () {
            // Placeholder: generate CSV dari window.__berandaData
            if (!window.__berandaData) {
                alert('Tidak ada data untuk di-export.');
                return;
            }
            var rows = [];
            rows.push(['Kategori','Nilai']);
            rows.push(['Total Mahasiswa', window.__berandaData.concentration.values.reduce(function(a,b){return a+b;}, 0)]);
            rows.push(['Skripsi Aktif', 'lihat DB']);
            alert('Export laporan berhasil disiapkan (placeholder).');
        });
    }
})();
