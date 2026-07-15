<?php
/**
 * Halaman Beranda — Dashboard utama dengan KPI cards, charts, feeds, dll.
 *
 * Cara pakai:  include __DIR__ . '/pages/beranda.php';
 *              (di dalam index.php setelah setup $pageTitle & $activePage)
 */
?>

<main class="content-area">

    <!-- ===== KPI Summary Cards ===== -->
    <section class="mb-4">
        <div class="kpi-row">
            <a href="#" class="kpi-card card-kpi card-kpi--info">
                <div class="card-kpi-icon"><i class="bi bi-people-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">2.847</span>
                    <span class="card-kpi-label">Mahasiswa Aktif</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--success">
                <div class="card-kpi-icon"><i class="bi bi-person-workspace"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">142</span>
                    <span class="card-kpi-label">Dosen Terdaftar</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--primary">
                <div class="card-kpi-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">38</span>
                    <span class="card-kpi-label">Bimbingan Skripsi Aktif</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--danger">
                <div class="card-kpi-icon"><i class="bi bi-pen-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">7</span>
                    <span class="card-kpi-label">Menunggu TTD</span>
                </div>
                <span class="card-kpi-badge badge-flash">Penting</span>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--whatsapp">
                <div class="card-kpi-icon"><i class="bi bi-whatsapp"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">156</span>
                    <span class="card-kpi-label">WA Terkirim (Hari Ini)</span>
                </div>
            </a>
        </div>
    </section>

    <!-- ===== Charts Row 2x2 ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-bar-chart-fill me-1"></i>Angkatan Mahasiswa</h6>
                        <small class="text-muted">S1 Teknik Elektro vs Teknik Komputer (2009–2025)</small>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="chartAngkatan"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-pie-chart-fill me-1"></i>Progress Skripsi</h6>
                        <small class="text-muted">Distribusi tahapan bimbingan saat ini</small>
                    </div>
                    <div class="chart-card-body chart-card-body--center">
                        <canvas id="chartSkripsi"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-graph-up-arrow me-1"></i>Sinkronisasi Sister</h6>
                        <small class="text-muted">Tren pembaruan data (Pendidikan, Penelitian, Pengabdian)</small>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="chartSister"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-bar-chart-line-fill me-1"></i>Beban Kerja Operasional</h6>
                        <small class="text-muted">Broadcast &amp; dokumen diproses (7 hari terakhir)</small>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="chartBeban"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Bottom Feed & Shortcuts ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <!-- Tanda Tangan Digital -->
            <div class="col-12 col-xl-5">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-pen-fill me-1"></i>Tanda Tangan Digital</h6>
                        <a href="#" class="feed-card-action">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover feed-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="font-size:.78rem;">Mahasiswa</th>
                                        <th style="font-size:.78rem;">No. Surat</th>
                                        <th style="font-size:.78rem;" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-size:.82rem;">Ahmad Fauzi</td>
                                        <td style="font-size:.82rem;">SK-2026/0847</td>
                                        <td class="text-end"><button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:.82rem;">Siti Nurhaliza</td>
                                        <td style="font-size:.82rem;">SK-2026/0851</td>
                                        <td class="text-end"><button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:.82rem;">Budi Santoso</td>
                                        <td style="font-size:.82rem;">SK-2026/0855</td>
                                        <td class="text-end"><button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:.82rem;">Dewi Lestari</td>
                                        <td style="font-size:.82rem;">SK-2026/0860</td>
                                        <td class="text-end"><button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:.82rem;">Rizky Pratama</td>
                                        <td style="font-size:.82rem;">SK-2026/0863</td>
                                        <td class="text-end"><button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Broadcast Terbaru -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-whatsapp me-1"></i>Broadcast Terbaru</h6>
                        <a href="#" class="feed-card-action">Arsip <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Semua Dosen</small>
                                <span class="badge badge-success-subtle text-success">Terkirim</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">"Dimohon kehadiran seluruh dosen pada rapat..."</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>2 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Mahasiswa Aktif</small>
                                <span class="badge badge-success-subtle text-success">Terkirim</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">"Jadwal ujian semester genap telah dipublikasikan..."</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>5 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Bimbingan Skripsi</small>
                                <span class="badge badge-warning-subtle text-warning">Terjadwal</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">"Pengingat jadwal bimbingan skripsi minggu ini..."</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>Kemarin</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Mahasiswa Baru -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-person-plus-fill me-1"></i>Mahasiswa Baru</h6>
                        <a href="#" class="feed-card-action">Detail <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <div class="student-log-item d-flex align-items-center gap-2 mb-3">
                            <div class="student-avatar" style="background:#e7f1ff; color:#0d6efd;">AF</div>
                            <div class="flex-grow-1 min-w-0">
                                <small class="fw-semibold d-block text-truncate" style="font-size:.82rem;">Ahmad Fauzi</small>
                                <small class="text-muted d-block text-truncate" style="font-size:.72rem;">NIM: 2025100001 • T. Elektro</small>
                            </div>
                            <span class="badge bg-success-subtle text-success" style="font-size:.68rem;">Verifikasi</span>
                        </div>
                        <div class="student-log-item d-flex align-items-center gap-2 mb-3">
                            <div class="student-avatar" style="background:#f0f8e8; color:#198754;">SN</div>
                            <div class="flex-grow-1 min-w-0">
                                <small class="fw-semibold d-block text-truncate" style="font-size:.82rem;">Siti Nurhaliza</small>
                                <small class="text-muted d-block text-truncate" style="font-size:.72rem;">NIM: 2025100002 • T. Komputer</small>
                            </div>
                            <span class="badge bg-success-subtle text-success" style="font-size:.68rem;">Verifikasi</span>
                        </div>
                        <div class="student-log-item d-flex align-items-center gap-2 mb-3">
                            <div class="student-avatar" style="background:#fef3e2; color:#fd7e14;">BS</div>
                            <div class="flex-grow-1 min-w-0">
                                <small class="fw-semibold d-block text-truncate" style="font-size:.82rem;">Budi Santoso</small>
                                <small class="text-muted d-block text-truncate" style="font-size:.72rem;">NIM: 2025100003 • T. Elektro</small>
                            </div>
                            <span class="badge bg-info-subtle text-info" style="font-size:.68rem;">Pending</span>
                        </div>
                        <div class="student-log-item d-flex align-items-center gap-2 mb-3">
                            <div class="student-avatar" style="background:#fce4ec; color:#dc3545;">DL</div>
                            <div class="flex-grow-1 min-w-0">
                                <small class="fw-semibold d-block text-truncate" style="font-size:.82rem;">Dewi Lestari</small>
                                <small class="text-muted d-block text-truncate" style="font-size:.72rem;">NIM: 2025100004 • T. Komputer</small>
                            </div>
                            <span class="badge bg-success-subtle text-success" style="font-size:.68rem;">Verifikasi</span>
                        </div>
                        <div class="student-log-item d-flex align-items-center gap-2">
                            <div class="student-avatar" style="background:#f3e8fd; color:#6f42c1;">RP</div>
                            <div class="flex-grow-1 min-w-0">
                                <small class="fw-semibold d-block text-truncate" style="font-size:.82rem;">Rizky Pratama</small>
                                <small class="text-muted d-block text-truncate" style="font-size:.72rem;">NIM: 2025100005 • T. Elektro</small>
                            </div>
                            <span class="badge bg-warning-subtle text-warning" style="font-size:.68rem;">Revisi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Classic Widgets (Pengumuman & Agenda) ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <!-- Pengumuman -->
            <div class="col-12 col-lg-5">
                <div class="feed-card feed-card--collapsible" id="announcementWidget">
                    <div class="feed-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="feed-card-title mb-0"><i class="bi bi-megaphone-fill me-1"></i>Pengumuman</h6>
                            <button class="btn btn-sm btn-link text-muted p-0" title="Edit Pengumuman">
                                <i class="bi bi-gear"></i>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-link text-muted p-0 ms-auto" id="announcementToggle" title="Sembunyikan/Tampilkan">
                            <i class="bi bi-chevron-up"></i>
                        </button>
                    </div>
                    <div class="feed-card-body" id="announcementBody">
                        <div class="announcement-item mb-3">
                            <span class="badge bg-primary mb-1" style="font-size:.65rem;">Baru</span>
                            <p class="mb-0 mt-1" style="font-size:.82rem;"><strong>Rapat Koordinasi Semester Genap 2025/2026</strong></p>
                            <small class="text-muted" style="font-size:.72rem;">Diumumkan, 10 Juli 2026</small>
                        </div>
                        <hr>
                        <div class="announcement-item mb-3">
                            <p class="mb-0" style="font-size:.82rem;"><strong>Jadwal UTS Semester Genap Telah Dipublikasikan</strong></p>
                            <small class="text-muted" style="font-size:.72rem;">Diumumkan, 8 Juli 2026</small>
                        </div>
                        <hr>
                        <div class="announcement-item">
                            <p class="mb-0" style="font-size:.82rem;"><strong>Pendaftaran Yudisium Periode Agustus 2026 Dibuka</strong></p>
                            <small class="text-muted" style="font-size:.72rem;">Diumumkan, 5 Juli 2026</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agenda / Kalender Mini -->
            <div class="col-12 col-lg-7">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-calendar-week me-1"></i>Agenda</h6>
                        <div class="d-flex align-items-center gap-2 ms-auto">
                            <button class="btn btn-sm btn-link text-dark p-0" id="calPrev"><i class="bi bi-chevron-left"></i></button>
                            <span class="fw-semibold" style="font-size:.85rem;" id="calMonthYear">Juli 2026</span>
                            <button class="btn btn-sm btn-link text-dark p-0" id="calNext"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="feed-card-body">
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Diisi oleh JS -->
                        </div>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2"><strong>Agenda Mendatang:</strong></small>
                            <div class="agenda-item d-flex align-items-center gap-2 mb-2">
                                <span class="agenda-dot agenda-dot--exam"></span>
                                <small style="font-size:.78rem;"><strong>14 Juli</strong> — UTS Semester Genap 2025/2026</small>
                            </div>
                            <div class="agenda-item d-flex align-items-center gap-2 mb-2">
                                <span class="agenda-dot agenda-dot--advising"></span>
                                <small style="font-size:.78rem;"><strong>18 Juli</strong> — Bimbingan Skripsi Batch 3</small>
                            </div>
                            <div class="agenda-item d-flex align-items-center gap-2">
                                <span class="agenda-dot agenda-dot--exam"></span>
                                <small style="font-size:.78rem;"><strong>21 Juli</strong> — Ujian Praktek Pemrograman</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Sinkronisasi Sister Status ===== -->
    <section class="mb-4">
        <div class="feed-card">
            <div class="feed-card-header">
                <h6 class="feed-card-title"><i class="bi bi-arrow-repeat me-1"></i>Status Sinkronisasi Sister</h6>
                <button class="btn btn-sm btn-primary ms-auto"><i class="bi bi-arrow-repeat me-1"></i>Sinkronisasi Sekarang</button>
            </div>
            <div class="feed-card-body p-0">
                <div class="row g-0 sister-grid">
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Pendidikan &amp; Pengajaran</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">12 Jul 2026, 08:30 • 247 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Penelitian</span>
                                <span class="badge bg-warning-subtle text-warning">Sinkron</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">10 Jul 2026, 14:15 • 89 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Pengabdian</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">11 Jul 2026, 09:45 • 156 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Data Penunjang</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">11 Jul 2026, 09:50 • 34 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Arsip Lain</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">11 Jul 2026, 09:55 • 18 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">SKP</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">11 Jul 2026, 10:00 • 42 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Data Dosen</span>
                                <span class="badge bg-success-subtle text-success">OK</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">12 Jul 2026, 07:00 • 142 record</small>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="sister-status-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:.82rem;">Data Mahasiswa</span>
                                <span class="badge bg-danger-subtle text-danger">Perlu</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">05 Jul 2026, 11:20 • 2.847 record</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
