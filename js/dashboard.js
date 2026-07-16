/**
 * SIATEK v3.0 — Dashboard JavaScript
 * Handles: Sidebar toggle (mobile), Submenu accordion (smooth),
 *          Chart.js (static data), Calendar, Announcement, Signature modal
 */

(function () {
    'use strict';

    /* =========================================================
       1. SIDEBAR TOGGLE (Mobile Overlay)
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

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) closeSidebar();
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeSidebar();
    });

    /* =========================================================
       2. SIDEBAR SUBMENU — Accordion (smooth)
       ========================================================= */
    document.querySelectorAll('.nav-parent').forEach(function (parent) {
        parent.addEventListener('click', function (e) {
            e.preventDefault();

            // Di mode icon-only (tablet), submenu tidak dipakai
            if (window.innerWidth >= 768 && window.innerWidth < 1200) return;

            var targetId = parent.getAttribute('data-target');
            var submenu = document.getElementById(targetId);

            // Accordion: tutup submenu lain yang terbuka
            document.querySelectorAll('.nav-parent').forEach(function (other) {
                if (other !== parent) {
                    other.classList.remove('open');
                    var otherId = other.getAttribute('data-target');
                    var otherSub = document.getElementById(otherId);
                    if (otherSub && otherSub.classList.contains('open')) {
                        otherSub.style.maxHeight = otherSub.scrollHeight + 'px';
                        otherSub.offsetHeight;
                        otherSub.style.maxHeight = '0px';
                        otherSub.classList.remove('open');
                    }
                }
            });

            // Toggle submenu saat ini
                if (submenu) {
                if (submenu.classList.contains('open')) {
                    // Close
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    submenu.offsetHeight;
                    submenu.style.maxHeight = '0px';
                    submenu.classList.remove('open');
                    parent.classList.remove('open');
                } else {
                    // Open
                    submenu.classList.add('open');
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    submenu.addEventListener('transitionend', function fn() {
                        submenu.style.maxHeight = 'none';
                        submenu.removeEventListener('transitionend', fn);
                    });
                    parent.classList.add('open');
                }
            }
        });
    });

    /* =========================================================
       2b. SUB-PARENT (Nested Submenu — Bimbingan & Pengguna)
       ========================================================= */
    document.querySelectorAll('.sub-parent').forEach(function (subParent) {
        subParent.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = subParent.getAttribute('data-target');
            var nestedSub = document.getElementById(targetId);

            if (nestedSub) {
                if (nestedSub.classList.contains('open')) {
                    // Close
                    nestedSub.style.maxHeight = nestedSub.scrollHeight + 'px';
                    nestedSub.offsetHeight;
                    nestedSub.style.maxHeight = '0px';
                    nestedSub.classList.remove('open');
                    subParent.classList.remove('active');
                } else {
                    // Open
                    nestedSub.classList.add('open');
                    nestedSub.style.maxHeight = nestedSub.scrollHeight + 'px';
                    subParent.classList.add('active');
                    nestedSub.addEventListener('transitionend', function fn() {
                        nestedSub.style.maxHeight = 'none';
                        nestedSub.removeEventListener('transitionend', fn);
                    });
                }
            }
        });
    });

    window.addEventListener('resize', function () {
        document.querySelectorAll('.nav-submenu.open').forEach(function (sub) {
            sub.style.maxHeight = 'none';
        });
    });

    /* =========================================================
       3. ANNOUNCEMENT WIDGET TOGGLE
       ========================================================= */
    var annToggle = document.getElementById('announcementToggle');
    var annBody = document.getElementById('announcementBody');

    if (annToggle && annBody) {
        annToggle.addEventListener('click', function () {
            var icon = annToggle.querySelector('i');
            if (annBody.classList.contains('collapsed')) {
                annBody.classList.remove('collapsed');
                annBody.style.maxHeight = annBody.scrollHeight + 'px';
                icon.className = 'bi bi-chevron-up';
                setTimeout(function () { annBody.style.maxHeight = '1000px'; }, 350);
            } else {
                annBody.style.maxHeight = annBody.scrollHeight + 'px';
                annBody.offsetHeight;
                annBody.classList.add('collapsed');
                icon.className = 'bi bi-chevron-down';
            }
        });
    }

    /* =========================================================
       4. MINI CALENDAR
       ========================================================= */
    var calendarGrid = document.getElementById('calendarGrid');
    var calMonthYear = document.getElementById('calMonthYear');
    var calPrev = document.getElementById('calPrev');
    var calNext = document.getElementById('calNext');

    var calDate = new Date(2026, 6, 1);

    var eventsByMonth = {
        '2026-6': { 12: 'today', 14: 'exam', 18: 'advising', 21: 'exam', 25: 'advising', 28: 'exam' }
    };

    var dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    var monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    var TODAY = { d: 15, m: 6, y: 2026 };

    function renderCalendar(date) {
        if (!calendarGrid) return;
        var year = date.getFullYear();
        var month = date.getMonth();

        calMonthYear.textContent = monthNames[month] + ' ' + year;

        var firstDay = new Date(year, month, 1).getDay();
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var daysInPrevMonth = new Date(year, month, 0).getDate();

        var events = eventsByMonth[year + '-' + month] || {};

        var html = '';
        dayNames.forEach(function (name) {
            html += '<div class="cal-day-name">' + name + '</div>';
        });

        for (var i = firstDay - 1; i >= 0; i--) {
            html += '<div class="cal-day other-month">' + (daysInPrevMonth - i) + '</div>';
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var classes = 'cal-day';
            var isToday = d === TODAY.d && month === TODAY.m && year === TODAY.y;
            var ev = events[d];
            if (isToday) classes += ' today';
            if (ev === 'exam') classes += ' has-event';
            if (ev === 'advising') classes += ' has-event has-advising';
            html += '<div class="' + classes + '">' + d + '</div>';
        }

        var totalCells = firstDay + daysInMonth;
        var remaining = 42 - totalCells;
        for (var j = 1; j <= remaining; j++) {
            html += '<div class="cal-day other-month">' + j + '</div>';
        }

        calendarGrid.innerHTML = html;
    }

    if (calPrev) calPrev.addEventListener('click', function () {
        calDate.setMonth(calDate.getMonth() - 1);
        renderCalendar(calDate);
    });
    if (calNext) calNext.addEventListener('click', function () {
        calDate.setMonth(calDate.getMonth() + 1);
        renderCalendar(calDate);
    });

    renderCalendar(calDate);

    /* =========================================================
       5. CHART.JS — STATIC DATA
       ========================================================= */
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
    Chart.defaults.plugins.legend.labels.padding = 14;
    Chart.defaults.elements.bar.borderRadius = 4;
    Chart.defaults.elements.bar.borderSkipped = false;
    Chart.defaults.scale.grid.color = 'rgba(0,0,0,0.05)';
    Chart.defaults.scale.grid.drawBorder = false;

    /* 5a. Angkatan — 5 tahun terakhir, sejajar (bukan tumpuk) */
    (function () {
        var ctx = document.getElementById('chartAngkatan');
        if (!ctx) return;

        var years = ['2021','2022','2023','2024','2025'];
        var elektro   = [285,310,340,365,390];
        var komputer  = [230,250,270,290,310];
        var d3elektro = [85,90,92,95,98];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    { label: 'Teknik Elektro',    data: elektro,    backgroundColor: 'rgba(13,110,253,0.85)', borderRadius: 3, maxBarThickness: 28 },
                    { label: 'Teknik Komputer',    data: komputer,   backgroundColor: 'rgba(99,102,241,0.85)', borderRadius: 3, maxBarThickness: 28 },
                    { label: 'Pendidikan Teknik Elektro', data: d3elektro, backgroundColor: 'rgba(25,135,84,0.85)', borderRadius: 3, maxBarThickness: 28 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { backgroundColor: '#1e293b', titleFont: { size: 13, weight: '600' }, bodyFont: { size: 12 }, padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: { stacked: false, ticks: { maxRotation: 0 } },
                    y: { stacked: false, beginAtZero: true }
                },
                animation: { easing: 'easeInQuad', duration: 1200 }
            }
        });
    })();

    /* 5b. Skripsi & KP — 4 kategori */
    (function () {
        var ctx = document.getElementById('chartSkripsi');
        if (!ctx) return;

        var data = [24, 38, 18, 12];
        var total = data.reduce(function (a, b) { return a + b; }, 0);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Kerja Praktek', 'Proposal Skripsi', 'Hasil Penelitian', 'Tutup'],
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(13,202,240,0.85)',
                        'rgba(13,110,253,0.85)',
                        'rgba(251,191,36,0.85)',
                        'rgba(168,85,247,0.85)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                layout: { padding: 10 },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (c) {
                                var pct = ((c.parsed / total) * 100).toFixed(1);
                                return c.label + ': ' + c.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                },
                animation: { easing: 'easeInQuad', animateRotate: true, duration: 1400 }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function (chart) {
                    var w = chart.width;
                    var h = chart.height;
                    var c = chart.ctx;
                    c.save();
                    c.font = '800 ' + Math.round(h * 0.14) + 'px "Inter", system-ui, sans-serif';
                    c.fillStyle = '#111827';
                    c.textAlign = 'center';
                    c.textBaseline = 'middle';
                    c.fillText(total, w / 2, h / 2 - 8);
                    c.font = '500 ' + Math.round(h * 0.055) + 'px "Inter", system-ui, sans-serif';
                    c.fillStyle = '#6b7280';
                    c.fillText('Total Aktif', w / 2, h / 2 + 18);
                    c.restore();
                }
            }]
        });
    })();

    /* 5c. Sister */
    (function () {
        var ctx = document.getElementById('chartSister');
        if (!ctx) return;

        var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        var pendidikan = [45, 52, 48, 60, 55, 62, 58, 70, 65, 72, 68, 75];
        var penelitian  = [30, 35, 28, 40, 38, 42, 45, 48, 44, 50, 52, 55];
        var pengabdian  = [20, 22, 25, 28, 30, 27, 32, 35, 33, 38, 40, 42];

        var c = ctx.getContext('2d');
        var g1 = c.createLinearGradient(0,0,0,280);
        g1.addColorStop(0,'rgba(13,110,253,0.3)'); g1.addColorStop(1,'rgba(13,110,253,0)');
        var g2 = c.createLinearGradient(0,0,0,280);
        g2.addColorStop(0,'rgba(251,191,36,0.3)'); g2.addColorStop(1,'rgba(251,191,36,0)');
        var g3 = c.createLinearGradient(0,0,0,280);
        g3.addColorStop(0,'rgba(168,85,247,0.3)'); g3.addColorStop(1,'rgba(168,85,247,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Pendidikan', data: pendidikan, borderColor: 'rgba(13,110,253,0.9)', backgroundColor: g1, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2 },
                    { label: 'Penelitian',  data: penelitian,  borderColor: 'rgba(251,191,36,0.9)', backgroundColor: g2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2 },
                    { label: 'Pengabdian',  data: pengabdian,  borderColor: 'rgba(168,85,247,0.9)', backgroundColor: g3, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { backgroundColor: '#1e293b', padding: 12, cornerRadius: 8 }
                },
                scales: { y: { beginAtZero: true } },
                animation: { easing: 'easeInQuad', duration: 1500 }
            }
        });
    })();

    /* 5d. Beban Kerja */
    (function () {
        var ctx = document.getElementById('chartBeban');
        if (!ctx) return;

        var days = ['Kam', 'Jum', 'Sab', 'Min', 'Sen', 'Sel', 'Rab'];
        var broadcast = [18, 22, 14, 8,  26, 31, 25];
        var dokumen   = [12, 15,  9, 4,  18, 22, 19];
        var labelDates = ['9 Jul', '10 Jul', '11 Jul', '12 Jul', '13 Jul', '14 Jul', '15 Jul'];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [
                    { label: 'Broadcast', data: broadcast, backgroundColor: 'rgba(37,211,102,0.85)', borderRadius: 4, maxBarThickness: 28 },
                    { label: 'Dokumen',   data: dokumen,   backgroundColor: 'rgba(13,110,253,0.85)', borderRadius: 4, maxBarThickness: 28 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { weight: '600' },
                        callbacks: {
                            title: function (items) {
                                return labelDates[items[0].dataIndex];
                            }
                        }
                    }
                },
                scales: { y: { beginAtZero: true } },
                animation: { easing: 'easeInQuad', duration: 1200 }
            }
        });
    })();

    /* =========================================================
       6. SKELETON LOADING
       ========================================================= */
    setTimeout(function () {
        var skeletonElements = document.querySelectorAll('[data-skeleton="true"]');
        skeletonElements.forEach(function (el) { el.removeAttribute('data-skeleton'); });
        document.body.classList.add('skeleton-loaded');
    }, 1200);

    /* =========================================================
       7. SIGNATURE MODAL
       ========================================================= */
    var signModalEl = document.getElementById('signModal');
    if (signModalEl && typeof bootstrap !== 'undefined') {
        var signModal = new bootstrap.Modal(signModalEl);
        var btnConfirm = document.getElementById('btnConfirmSign');
        var confirmCheck = document.getElementById('confirmSignCheck');

        document.querySelectorAll('.btn-signature').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                signModal.show();
            });
        });

        if (btnConfirm) {
            btnConfirm.addEventListener('click', function () {
                if (confirmCheck && confirmCheck.checked) {
                    btnConfirm.innerHTML = '<i class="bi bi-check-circle me-1"></i> Tersimpan!';
                    btnConfirm.classList.remove('btn-primary');
                    btnConfirm.classList.add('btn-success');
                    btnConfirm.disabled = true;

                    setTimeout(function () {
                        signModal.hide();
                        btnConfirm.innerHTML = '<i class="bi bi-pen-fill me-1"></i> Konfirmasi & Tanda Tangan';
                        btnConfirm.classList.remove('btn-success');
                        btnConfirm.classList.add('btn-primary');
                        btnConfirm.disabled = false;
                        if (confirmCheck) confirmCheck.checked = false;
                    }, 1500);
                } else {
                    if (confirmCheck) {
                        confirmCheck.focus();
                        confirmCheck.classList.add('is-invalid');
                        setTimeout(function () { confirmCheck.classList.remove('is-invalid'); }, 2000);
                    }
                }
            });
        }

        signModalEl.addEventListener('hidden.bs.modal', function () {
            if (btnConfirm) {
                btnConfirm.innerHTML = '<i class="bi bi-pen-fill me-1"></i> Konfirmasi & Tanda Tangan';
                btnConfirm.classList.remove('btn-success');
                btnConfirm.classList.add('btn-primary');
                btnConfirm.disabled = false;
            }
            if (confirmCheck) confirmCheck.checked = false;
        });
    }

    /* =========================================================
       5. AUTO-OPEN SIDEBAR PARENT YANG PUNYA CHILD AKTIF
       ========================================================= */
    function autoOpenActiveParent() {
        var currentPage = window.location.search; // ex: '?page=beranda' atau '?page=mhs-skripsi'

        // Cari semua sub-item yang aktif
        document.querySelectorAll('.nav-submenu .sub-item.active').forEach(function (activeItem) {
            var submenu = activeItem.closest('.nav-submenu');
            if (submenu) {
                var parentId = submenu.id.replace('sub-', '');
                var parentBtn = document.querySelector('.nav-parent[data-target="' + parentId + '"]');
                if (parentBtn && !parentBtn.classList.contains('open')) {
                    parentBtn.classList.add('open');
                }
                if (submenu && !submenu.classList.contains('open')) {
                    submenu.classList.add('open');
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    setTimeout(function () {
                        submenu.style.maxHeight = 'none';
                    }, 350);
                }
            }
        });

        // Auto-open sub-parent (nested) jika ada child aktif
        document.querySelectorAll('.nav-submenu .sub-parent.active').forEach(function (activeSub) {
            var nestedId = activeSub.getAttribute('data-target');
            var nestedSub = document.getElementById(nestedId);
            if (nestedSub && !nestedSub.classList.contains('open')) {
                nestedSub.classList.add('open');
                nestedSub.style.maxHeight = nestedSub.scrollHeight + 'px';
                setTimeout(function () {
                    nestedSub.style.maxHeight = 'none';
                }, 350);
                activeSub.classList.add('active');
            }
        });
    }

    // Jalankan saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoOpenActiveParent);
    } else {
        autoOpenActiveParent();
    }
})();
