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
                <div class="card-kpi-icon"><i class="bi bi-diagram-3-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">1.124</span>
                    <span class="card-kpi-label">Teknik Elektro</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--primary">
                <div class="card-kpi-icon"><i class="bi bi-laptop-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">978</span>
                    <span class="card-kpi-label">Teknik Komputer</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--success">
                <div class="card-kpi-icon"><i class="bi bi-building"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">745</span>
                    <span class="card-kpi-label">Pendidikan Teknik Elektro</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--warning">
                <div class="card-kpi-icon"><i class="bi bi-person-workspace"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">142</span>
                    <span class="card-kpi-label">Dosen Terdaftar</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--info">
                <div class="card-kpi-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">38</span>
                    <span class="card-kpi-label">Skripsi Aktif</span>
                </div>
            </a>
            <a href="#" class="kpi-card card-kpi card-kpi--primary">
                <div class="card-kpi-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div class="card-kpi-body">
                    <span class="card-kpi-value" data-skeleton="true">24</span>
                    <span class="card-kpi-label">KP Aktif</span>
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
        </div>
    </section>

    <!-- ===== Charts Row 2x2 ===== -->
    <section class="mb-4">
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-bar-chart-fill me-1"></i>Angkatan Mahasiswa</h6>
                        <small class="text-muted">Angkatan mahasiswa 5 tahun terakhir (sejajar per prodi)</small>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="chartAngkatan"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h6 class="chart-card-title"><i class="bi bi-pie-chart-fill me-1"></i>Ringkasan Aktif</h6>
                        <small class="text-muted">Distribusi KP &amp; Skripsi per fase saat ini</small>
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
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-pen-fill me-1"></i>Tanda Tangan Digital</h6>
                        <a href="#" class="feed-card-action">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Ahmad Fauzi <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;">SK-2026/0847</span></small>
                                <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Surat Penunjukan Pembimbing — Proposal</p>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Siti Nurhaliza <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;">SK-2026/0851</span></small>
                                <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Surat Penunjukan Penguji — Hasil</p>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Budi Santoso <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;">SK-2026/0855</span></small>
                                <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Surat Penugasan Pembimbing — KP</p>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Dewi Lestari <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;">SK-2026/0860</span></small>
                                <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Surat Penunjukan Penguji — Tutup</p>
                        </div>
                        <hr>
                        <div class="broadcast-item">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Rizky Pratama <span class="text-muted ms-1" style="font-size:.7rem; font-weight:400;">SK-2026/0863</span></small>
                                <button class="btn btn-sm btn-signature" data-bs-toggle="modal" data-bs-target="#signModal">TTD</button>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Surat Keterangan Lulus — Yudisium</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Aktivitas Skripsi -->
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-journal-text me-1"></i>Log Aktivitas Skripsi</h6>
                        <a href="#" class="feed-card-action">Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Mariska Palakum</small>
                                <span class="badge bg-primary-subtle text-primary">Proposal</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Mengunggah laporan proposal &amp; lembar bimbingan</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>1 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Yusfiyah F Daud</small>
                                <span class="badge bg-success-subtle text-success">Tutup</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Verifikasi lembar pembimbing disetujui</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>3 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Salsabila Buka</small>
                                <span class="badge bg-info-subtle text-info">KP</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Membuat surat penunjukan pembimbing</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>5 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Nur Uyun I Yusuf</small>
                                <span class="badge bg-warning-subtle text-warning">Hasil</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Mengupload berita acara &amp; lembar penilaian hasil</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>Kemarin</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Kerja Praktek -->
            <div class="col-12 col-xl-4">
                <div class="feed-card">
                    <div class="feed-card-header">
                        <h6 class="feed-card-title"><i class="bi bi-briefcase-fill me-1"></i>Log Kerja Praktek</h6>
                        <a href="#" class="feed-card-action">Semua <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="feed-card-body">
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Andi Wijaya</small>
                                <span class="badge bg-primary-subtle text-primary">Penugasan</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Mengunggah surat keterangan magang &amp; lembar bimbingan</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>2 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Dina Rahmawati</small>
                                <span class="badge bg-success-subtle text-success">Pelaksanaan</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Verifikasi laporan tengah magang disetujui</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>4 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item mb-3">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Fajar Hidayat</small>
                                <span class="badge bg-info-subtle text-info">Laporan</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Mengupload laporan akhir KP &amp; lembar penilaian</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>6 jam lalu</small>
                        </div>
                        <hr>
                        <div class="broadcast-item">
                            <div class="d-flex justify-content-between mb-1 gap-2">
                                <small class="fw-semibold">Bayu Taufik</small>
                                <span class="badge bg-danger-subtle text-danger">Sidang</span>
                            </div>
                            <p class="broadcast-snippet mb-0" style="font-size:.8rem; color:#6c757d;">Membuat surat penunjukan penguji sidang KP</p>
                            <small class="text-muted" style="font-size:.7rem;"><i class="bi bi-clock me-1"></i>Kemarin</small>
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
