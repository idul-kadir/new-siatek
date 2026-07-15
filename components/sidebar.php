<?php
/**
 * Sidebar Navigation — Statis (dimuat ulang tiap halaman tapi isinya sama)
 *
 * Cara pakai:  di index.php → include 'components/sidebar.php';
 *
 * Variabel $activePage (string) akan otomatis menandai menu aktif
 * dengan class "active" — diset oleh router di index.php sebelum include ini.
 */

$activePage = $activePage ?? ''; // ex: "dashboard", "mhs-skripsi", "jurusan-arsip", dst.
?>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-cpu-fill"></i>
        </div>
        <span class="brand-text">SIATEK</span>
        <button class="btn sidebar-close-btn d-lg-none" id="sidebarCloseBtn">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <!-- 1. Beranda -->
        <div class="nav-group">
            <a href="index.php?page=dashboard" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>
                <span class="nav-label">Beranda</span>
            </a>
        </div>

        <!-- 2. Pengelolaan -->
        <div class="nav-group">
            <button class="nav-item nav-parent" data-target="sub-pengelolaan">
                <i class="bi bi-folder-fill nav-icon"></i>
                <span class="nav-label">Pengelolaan</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </button>
            <div class="nav-submenu" id="sub-pengelolaan">
                <div class="sub-section-header">Data Mahasiswa &amp; Alumni</div>
                <a href="index.php?page=mhs-skripsi" class="sub-item <?= $activePage === 'mhs-skripsi' ? 'active' : '' ?>">
                    <i class="bi bi-journal-text sub-icon"></i>
                    <span class="sub-text">Skripsi</span>
                </a>
                <a href="index.php?page=mhs-kp" class="sub-item <?= $activePage === 'mhs-kp' ? 'active' : '' ?>">
                    <i class="bi bi-briefcase sub-icon"></i>
                    <span class="sub-text">Kerja Praktek</span>
                </a>
                <a href="index.php?page=mhs-verifikasi" class="sub-item <?= $activePage === 'mhs-verifikasi' ? 'active' : '' ?>">
                    <i class="bi bi-clipboard-check sub-icon"></i>
                    <span class="sub-text">Verifikasi Berkas</span>
                </a>
                <a href="index.php?page=mhs-alumni" class="sub-item <?= $activePage === 'mhs-alumni' ? 'active' : '' ?>">
                    <i class="bi bi-mortarboard sub-icon"></i>
                    <span class="sub-text">Data Alumni</span>
                </a>
                <a href="index.php?page=mhs-kegiatan" class="sub-item <?= $activePage === 'mhs-kegiatan' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event sub-icon"></i>
                    <span class="sub-text">Kegiatan</span>
                </a>

                <div class="sub-section-header sub-section-header--with-divider">Master &amp; Pengaturan</div>

                <!-- Data Bimbingan (parent) -->
                <button class="sub-item sub-parent" data-target="sub-bimbingan">
                    <i class="bi bi-people sub-icon"></i>
                    <span class="sub-text">Data Bimbingan</span>
                    <i class="bi bi-chevron-right sub-chevron"></i>
                </button>
                <div class="nav-submenu nav-submenu--nested" id="sub-bimbingan">
                    <a href="index.php?page=bimbingan-skripsi" class="sub-item <?= $activePage === 'bimbingan-skripsi' ? 'active' : '' ?>">
                        <i class="bi bi-journal-check sub-icon"></i>
                        <span class="sub-text">Bimbingan Skripsi</span>
                    </a>
                    <a href="index.php?page=bimbingan-kp" class="sub-item <?= $activePage === 'bimbingan-kp' ? 'active' : '' ?>">
                        <i class="bi bi-briefcase sub-icon"></i>
                        <span class="sub-text">Bimbingan Kerja Praktek</span>
                    </a>
                    <a href="index.php?page=bimbingan-pa" class="sub-item <?= $activePage === 'bimbingan-pa' ? 'active' : '' ?>">
                        <i class="bi bi-person sub-icon"></i>
                        <span class="sub-text">Bimbingan Penasihat Akademik</span>
                    </a>
                </div>

                <!-- Data Pengguna (parent) -->
                <button class="sub-item sub-parent" data-target="sub-pengguna">
                    <i class="bi bi-person-badge sub-icon"></i>
                    <span class="sub-text">Data Pengguna</span>
                    <i class="bi bi-chevron-right sub-chevron"></i>
                </button>
                <div class="nav-submenu nav-submenu--nested" id="sub-pengguna">
                    <a href="index.php?page=pengguna-mahasiswa" class="sub-item <?= $activePage === 'pengguna-mahasiswa' ? 'active' : '' ?>">
                        <i class="bi bi-person sub-icon"></i>
                        <span class="sub-text">Pengguna Mahasiswa</span>
                    </a>
                    <a href="index.php?page=pengguna-dosen" class="sub-item <?= $activePage === 'pengguna-dosen' ? 'active' : '' ?>">
                        <i class="bi bi-person-workspace sub-icon"></i>
                        <span class="sub-text">Pengguna Dosen</span>
                    </a>
                </div>

                <a href="index.php?page=pengelolaan-broadcast" class="sub-item <?= $activePage === 'pengelolaan-broadcast' ? 'active' : '' ?>">
                    <i class="bi bi-megaphone sub-icon"></i>
                    <span class="sub-text">Broadcast</span>
                </a>
            </div>
        </div>

        <!-- 3. Pangkalan Data -->
        <div class="nav-group">
            <button class="nav-item nav-parent" data-target="sub-pangkalan">
                <i class="bi bi-database-fill nav-icon"></i>
                <span class="nav-label">Pangkalan Data</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </button>
            <div class="nav-submenu" id="sub-pangkalan">
                <a href="index.php?page=pd-pendidikan" class="sub-item <?= $activePage === 'pd-pendidikan' ? 'active' : '' ?>">
                    <i class="bi bi-book-fill sub-icon"></i>Pendidikan &amp; Pengajaran
                </a>
                <a href="index.php?page=pd-penelitian" class="sub-item <?= $activePage === 'pd-penelitian' ? 'active' : '' ?>">
                    <i class="bi bi-search sub-icon"></i>Penelitian
                </a>
                <a href="index.php?page=pd-pengabdian" class="sub-item <?= $activePage === 'pd-pengabdian' ? 'active' : '' ?>">
                    <i class="bi bi-heart-fill sub-icon"></i>Pengabdian
                </a>
                <a href="index.php?page=pd-penunjang" class="sub-item <?= $activePage === 'pd-penunjang' ? 'active' : '' ?>">
                    <i class="bi bi-tools sub-icon"></i>Data Penunjang
                </a>
                <a href="index.php?page=pd-arsip" class="sub-item <?= $activePage === 'pd-arsip' ? 'active' : '' ?>">
                    <i class="bi bi-archive-fill sub-icon"></i>Arsip Lain
                </a>
                <a href="index.php?page=pd-skp" class="sub-item <?= $activePage === 'pd-skp' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text-fill sub-icon"></i>SKP
                </a>
            </div>
        </div>

        <!-- 4. Biodata -->
        <div class="nav-group">
            <a href="index.php?page=biodata" class="nav-item <?= $activePage === 'biodata' ? 'active' : '' ?>">
                <i class="bi bi-person-circle nav-icon"></i>
                <span class="nav-label">Biodata</span>
            </a>
        </div>

        <!-- 5. Jadwal Kuliah -->
        <div class="nav-group">
            <a href="index.php?page=jadwal" class="nav-item <?= $activePage === 'jadwal' ? 'active' : '' ?>">
                <i class="bi bi-calendar-week-fill nav-icon"></i>
                <span class="nav-label">Jadwal Kuliah</span>
            </a>
        </div>

        <!-- 6. Sinkronisasi Sister -->
        <div class="nav-group">
            <a href="index.php?page=sister" class="nav-item <?= $activePage === 'sister' ? 'active' : '' ?>">
                <i class="bi bi-arrow-repeat nav-icon"></i>
                <span class="nav-label">Sinkronisasi Sister</span>
            </a>
        </div>

        <!-- 7. Jurusan -->
        <div class="nav-group">
            <button class="nav-item nav-parent" data-target="sub-jurusan">
                <i class="bi bi-building nav-icon"></i>
                <span class="nav-label">Jurusan</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </button>
            <div class="nav-submenu" id="sub-jurusan">
                <a href="index.php?page=jurusan-arsip" class="sub-item <?= $activePage === 'jurusan-arsip' ? 'active' : '' ?>">
                    <i class="bi bi-archive sub-icon"></i>
                    <span class="sub-text">Arsip</span>
                </a>
                <a href="index.php?page=jurusan-berita" class="sub-item <?= $activePage === 'jurusan-berita' ? 'active' : '' ?>">
                    <i class="bi bi-newspaper sub-icon"></i>
                    <span class="sub-text">Berita</span>
                </a>
                <a href="index.php?page=jurusan-tridharma" class="sub-item <?= $activePage === 'jurusan-tridharma' ? 'active' : '' ?>">
                    <i class="bi bi-three-dots sub-icon"></i>
                    <span class="sub-text">Data Tridharma</span>
                </a>
                <a href="index.php?page=jurusan-dok-akademik" class="sub-item <?= $activePage === 'jurusan-dok-akademik' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text sub-icon"></i>
                    <span class="sub-text">Dokumen Akademik</span>
                </a>
                <a href="index.php?page=jurusan-dok-akreditasi" class="sub-item <?= $activePage === 'jurusan-dok-akreditasi' ? 'active' : '' ?>">
                    <i class="bi bi-patch-check sub-icon"></i>
                    <span class="sub-text">Dokumen Akreditas</span>
                </a>
                <a href="index.php?page=jurusan-dok-lkps" class="sub-item <?= $activePage === 'jurusan-dok-lkps' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-bar-graph sub-icon"></i>
                    <span class="sub-text">Dokumen LKPS</span>
                </a>
                <a href="index.php?page=jurusan-jadwal" class="sub-item <?= $activePage === 'jurusan-jadwal' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-week sub-icon"></i>
                    <span class="sub-text">Jadwal Kuliah</span>
                </a>
                <a href="index.php?page=jurusan-kerjasama" class="sub-item <?= $activePage === 'jurusan-kerjasama' ? 'active' : '' ?>">
                    <i class="bi bi-handshake sub-icon"></i>
                    <span class="sub-text">Kerja Sama</span>
                </a>
                <a href="index.php?page=jurusan-keuangan" class="sub-item <?= $activePage === 'jurusan-keuangan' ? 'active' : '' ?>">
                    <i class="bi bi-currency-dollar sub-icon"></i>
                    <span class="sub-text">Keuangan</span>
                </a>
                <a href="index.php?page=jurusan-kurikulum" class="sub-item <?= $activePage === 'jurusan-kurikulum' ? 'active' : '' ?>">
                    <i class="bi bi-book sub-icon"></i>
                    <span class="sub-text">Kurikulum</span>
                </a>
                <a href="index.php?page=jurusan-laporan" class="sub-item <?= $activePage === 'jurusan-laporan' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up sub-icon"></i>
                    <span class="sub-text">Laporan Kinerja</span>
                </a>
                <a href="index.php?page=jurusan-matkul-rps" class="sub-item <?= $activePage === 'jurusan-matkul-rps' ? 'active' : '' ?>">
                    <i class="bi bi-journal-code sub-icon"></i>
                    <span class="sub-text">Matakuliah / RPS</span>
                </a>
                <a href="index.php?page=jurusan-matkul-mbkm" class="sub-item <?= $activePage === 'jurusan-matkul-mbkm' ? 'active' : '' ?>">
                    <i class="bi bi-globe sub-icon"></i>
                    <span class="sub-text">Matakuliah MBKM</span>
                </a>
                <a href="index.php?page=jurusan-organisasi" class="sub-item <?= $activePage === 'jurusan-organisasi' ? 'active' : '' ?>">
                    <i class="bi bi-people sub-icon"></i>
                    <span class="sub-text">Organisasi</span>
                </a>
                <a href="index.php?page=jurusan-peminjaman" class="sub-item <?= $activePage === 'jurusan-peminjaman' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right sub-icon"></i>
                    <span class="sub-text">Peminjaman</span>
                </a>
                <a href="index.php?page=jurusan-program-extra" class="sub-item <?= $activePage === 'jurusan-program-extra' ? 'active' : '' ?>">
                    <i class="bi bi-stars sub-icon"></i>
                    <span class="sub-text">Program Extra</span>
                </a>
                <a href="index.php?page=jurusan-skp" class="sub-item <?= $activePage === 'jurusan-skp' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text sub-icon"></i>
                    <span class="sub-text">SKP</span>
                </a>
                <a href="index.php?page=jurusan-surat" class="sub-item <?= $activePage === 'jurusan-surat' ? 'active' : '' ?>">
                    <i class="bi bi-envelope sub-icon"></i>
                    <span class="sub-text">Surat</span>
                </a>
                <a href="index.php?page=jurusan-surat-penunjukkan" class="sub-item <?= $activePage === 'jurusan-surat-penunjukkan' ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-check sub-icon"></i>
                    <span class="sub-text">Surat Penunjukkan</span>
                </a>
                <a href="index.php?page=jurusan-tracer" class="sub-item <?= $activePage === 'jurusan-tracer' ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt sub-icon"></i>
                    <span class="sub-text">Tracer Studi</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="#" class="nav-item nav-logout">
            <i class="bi bi-box-arrow-right nav-icon"></i>
            <span class="nav-label">Logout</span>
        </a>
    </div>
</aside>
