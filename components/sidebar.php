<?php
/**
 * Sidebar Navigation - Statis (Tailwind) - Pretty URL.
 *
 * Setiap link menggunakan base href dari index.php (lihat <base href>),
 * sehingga href="/redesain-siatek/dashboard" akan jadi "/redesain-siatek/dashboard"
 * dan di-rewrite oleh .htaccess ke index.php?page=dashboard.
 */
$activePage = $activePage ?? '';
?>

<!-- Sidebar Overlay (mobile) -->
<div class="modal-overlay md:!flex md:!hidden" id="sidebarOverlay" style="background:rgba(15,23,42,0.5); z-index:1040; padding:0;"></div>

<aside id="sidebar" class="sidebar-bg w-64 flex-shrink-0 flex-col overflow-y-auto sidebar-scroll hidden md:flex md:relative md:translate-x-0 z-20">

    <!-- Brand -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-accent-500 rounded-lg flex items-center justify-center text-white text-lg transition-transform hover:scale-105">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <h1 class="font-semibold text-white text-base leading-tight tracking-wide">SIATEK</h1>
                <p class="text-xs text-slate-400 tracking-wide">T. Elektro &amp; Komputer</p>
            </div>
        </div>
        <button class="md:hidden text-slate-400 hover:text-white text-xl leading-none" id="sidebarCloseBtn" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-3 space-y-0.5 px-2">

        <!-- 1. Beranda -->
        <a href="/redesain-siatek/beranda" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'beranda' ? 'nav-active' : '' ?>">
            <i class="fas fa-home w-5 text-center text-base"></i>
            <span class="flex-1">Beranda</span>
        </a>

        <!-- 2. Pengelolaan -->
        <?php
        $pengelolaanPages = ['mhs-skripsi','mhs-kp','mhs-verifikasi','mhs-alumni','mhs-kegiatan',
                             'bimbingan-skripsi','bimbingan-kp','bimbingan-pa',
                             'pengguna-mahasiswa','pengguna-dosen','pengelolaan-broadcast'];
        $pengelolaanActive = in_array($activePage, $pengelolaanPages, true);
        ?>
        <div>
            <button class="nav-item nav-parent w-full flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $pengelolaanActive ? 'open' : '' ?>" data-target="sub-pengelolaan">
                <i class="fas fa-folder w-5 text-center text-base"></i>
                <span class="flex-1 text-left">Pengelolaan</span>
                <i class="fas fa-chevron-right nav-chevron text-xs opacity-50"></i>
            </button>
            <div class="nav-submenu <?= $pengelolaanActive ? 'open' : '' ?>" id="sub-pengelolaan">
                <div class="nav-section-header">Data Mahasiswa &amp; Alumni</div>
                <a href="/redesain-siatek/mhs-skripsi" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'mhs-skripsi' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-book w-5 text-center text-sm opacity-70"></i><span>Skripsi</span></a>
                <a href="/redesain-siatek/mhs-kp" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'mhs-kp' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-briefcase w-5 text-center text-sm opacity-70"></i><span>Kerja Praktek</span></a>
                <a href="/redesain-siatek/mhs-verifikasi" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'mhs-verifikasi' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-clipboard w-5 text-center text-sm opacity-70"></i><span>Verifikasi Berkas</span></a>
                <a href="/redesain-siatek/mhs-alumni" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'mhs-alumni' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-graduation-cap w-5 text-center text-sm opacity-70"></i><span>Data Alumni</span></a>
                <a href="/redesain-siatek/mhs-kegiatan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'mhs-kegiatan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-calendar w-5 text-center text-sm opacity-70"></i><span>Kegiatan</span></a>

                <div class="nav-section-header border-t border-white/5 mt-2 pt-3">Master &amp; Pengaturan</div>

                <!-- Bimbingan (nested) -->
                <?php $bimbPages = ['bimbingan-skripsi','bimbingan-kp','bimbingan-pa']; $bimbActive = in_array($activePage, $bimbPages, true); ?>
                <button class="sub-item sub-parent w-full flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $bimbActive ? 'open' : '' ?>" data-target="sub-bimbingan">
                    <i class="fas fa-users w-5 text-center text-sm opacity-70"></i>
                    <span class="flex-1 text-left">Data Bimbingan</span>
                    <i class="fas fa-chevron-right sub-chevron text-xs opacity-50"></i>
                </button>
                <div class="nav-submenu--nested <?= $bimbActive ? 'open' : '' ?>" id="sub-bimbingan">
                    <a href="/redesain-siatek/bimbingan-skripsi" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'bimbingan-skripsi' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-book w-5 text-center text-sm opacity-70"></i><span>Bimbingan Skripsi</span></a>
                    <a href="/redesain-siatek/bimbingan-kp" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'bimbingan-kp' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-briefcase w-5 text-center text-sm opacity-70"></i><span>Bimbingan KP</span></a>
                    <a href="/redesain-siatek/bimbingan-pa" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'bimbingan-pa' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-user w-5 text-center text-sm opacity-70"></i><span>Bimbingan PA</span></a>
                </div>

                <!-- Pengguna (nested) -->
                <?php $pengPages = ['pengguna-mahasiswa','pengguna-dosen']; $pengActive = in_array($activePage, $pengPages, true); ?>
                <button class="sub-item sub-parent w-full flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $pengActive ? 'open' : '' ?>" data-target="sub-pengguna">
                    <i class="fas fa-id-card w-5 text-center text-sm opacity-70"></i>
                    <span class="flex-1 text-left">Data Pengguna</span>
                    <i class="fas fa-chevron-right sub-chevron text-xs opacity-50"></i>
                </button>
                <div class="nav-submenu--nested <?= $pengActive ? 'open' : '' ?>" id="sub-pengguna">
                    <a href="/redesain-siatek/pengguna-mahasiswa" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pengguna-mahasiswa' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-user w-5 text-center text-sm opacity-70"></i><span>Pengguna Mahasiswa</span></a>
                    <a href="/redesain-siatek/pengguna-dosen" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pengguna-dosen' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-user-tie w-5 text-center text-sm opacity-70"></i><span>Pengguna Dosen</span></a>
                </div>

                <a href="/redesain-siatek/pengelolaan-broadcast" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pengelolaan-broadcast' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-bullhorn w-5 text-center text-sm opacity-70"></i><span>Broadcast</span></a>
            </div>
        </div>

        <!-- 3. Pangkalan Data -->
        <?php $pdPages = ['pd-pendidikan','pd-penelitian','pd-pengabdian','pd-penunjang','pd-arsip','pd-skp']; $pdActive = in_array($activePage, $pdPages, true); ?>
        <div>
            <button class="nav-item nav-parent w-full flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $pdActive ? 'open' : '' ?>" data-target="sub-pangkalan">
                <i class="fas fa-database w-5 text-center text-base"></i>
                <span class="flex-1 text-left">Pangkalan Data</span>
                <i class="fas fa-chevron-right nav-chevron text-xs opacity-50"></i>
            </button>
            <div class="nav-submenu <?= $pdActive ? 'open' : '' ?>" id="sub-pangkalan">
                <a href="/redesain-siatek/pd-pendidikan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-pendidikan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-book w-5 text-center text-sm opacity-70"></i><span>Pendidikan &amp; Pengajaran</span></a>
                <a href="/redesain-siatek/pd-penelitian" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-penelitian' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-search w-5 text-center text-sm opacity-70"></i><span>Penelitian</span></a>
                <a href="/redesain-siatek/pd-pengabdian" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-pengabdian' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-heart w-5 text-center text-sm opacity-70"></i><span>Pengabdian</span></a>
                <a href="/redesain-siatek/pd-penunjang" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-penunjang' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-tools w-5 text-center text-sm opacity-70"></i><span>Data Penunjang</span></a>
                <a href="/redesain-siatek/pd-arsip" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-arsip' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-archive w-5 text-center text-sm opacity-70"></i><span>Arsip Lain</span></a>
                <a href="/redesain-siatek/pd-skp" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'pd-skp' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-file-alt w-5 text-center text-sm opacity-70"></i><span>SKP</span></a>
            </div>
        </div>

        <!-- 4. Biodata -->
        <a href="/redesain-siatek/biodata" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'biodata' ? 'nav-active' : '' ?>">
            <i class="fas fa-user-circle w-5 text-center text-base"></i>
            <span class="flex-1">Biodata</span>
        </a>

        <!-- 5. Jadwal Kuliah -->
        <a href="/redesain-siatek/jadwal" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'jadwal' ? 'nav-active' : '' ?>">
            <i class="fas fa-calendar-alt w-5 text-center text-base"></i>
            <span class="flex-1">Jadwal Kuliah</span>
        </a>

        <!-- 6. Sinkronisasi Sister -->
        <a href="/redesain-siatek/sister" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'sister' ? 'nav-active' : '' ?>">
            <i class="fas fa-sync w-5 text-center text-base"></i>
            <span class="flex-1">Sinkronisasi Sister</span>
        </a>

        <!-- 7. Jurusan -->
        <?php
        $jurusanPages = ['arsip-jurusan','jurusan-berita','tulis-berita','jurusan-tridharma',
                         'jurusan-dok-akademik','jurusan-dok-akreditasi','jurusan-dok-lkps',
                         'jurusan-jadwal','jurusan-kerjasama','jurusan-keuangan','jurusan-kurikulum',
                         'jurusan-laporan','jurusan-matkul-rps','jurusan-matkul-mbkm',
                         'jurusan-organisasi','jurusan-peminjaman','jurusan-program-extra',
                         'jurusan-skp','jurusan-surat','jurusan-surat-penunjukkan','jurusan-tracer'];
        $jurusanActive = in_array($activePage, $jurusanPages, true);
        ?>
        <div>
            <button class="nav-item nav-parent w-full flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $jurusanActive ? 'open' : '' ?>" data-target="sub-jurusan">
                <i class="fas fa-building w-5 text-center text-base"></i>
                <span class="flex-1 text-left">Jurusan</span>
                <i class="fas fa-chevron-right nav-chevron text-xs opacity-50"></i>
            </button>
            <div class="nav-submenu <?= $jurusanActive ? 'open' : '' ?>" id="sub-jurusan">
                <a href="/redesain-siatek/arsip-jurusan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'arsip-jurusan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-archive w-5 text-center text-sm opacity-70"></i><span>Arsip</span></a>
                <a href="/redesain-siatek/jurusan-berita" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= in_array($activePage, ['jurusan-berita','tulis-berita'], true) ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-newspaper w-5 text-center text-sm opacity-70"></i><span>Berita</span></a>
                <a href="/redesain-siatek/jurusan-tridharma" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-tridharma' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-ellipsis-h w-5 text-center text-sm opacity-70"></i><span>Data Tridharma</span></a>
                <a href="/redesain-siatek/jurusan-dok-akademik" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-dok-akademik' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-file-alt w-5 text-center text-sm opacity-70"></i><span>Dokumen Akademik</span></a>
                <a href="/redesain-siatek/jurusan-dok-akreditasi" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-dok-akreditasi' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-check-circle w-5 text-center text-sm opacity-70"></i><span>Dokumen Akreditasi</span></a>
                <a href="/redesain-siatek/jurusan-dok-lkps" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-dok-lkps' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-chart-bar w-5 text-center text-sm opacity-70"></i><span>Dokumen LKPS</span></a>
                <a href="/redesain-siatek/jurusan-jadwal" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-jadwal' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-calendar-alt w-5 text-center text-sm opacity-70"></i><span>Jadwal Kuliah</span></a>
                <a href="/redesain-siatek/jurusan-kerjasama" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-kerjasama' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-handshake w-5 text-center text-sm opacity-70"></i><span>Kerja Sama</span></a>
                <a href="/redesain-siatek/jurusan-keuangan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-keuangan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-dollar-sign w-5 text-center text-sm opacity-70"></i><span>Keuangan</span></a>
                <a href="/redesain-siatek/jurusan-kurikulum" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-kurikulum' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-book-open w-5 text-center text-sm opacity-70"></i><span>Kurikulum</span></a>
                <a href="/redesain-siatek/jurusan-laporan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-laporan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-chart-line w-5 text-center text-sm opacity-70"></i><span>Laporan Kinerja</span></a>
                <a href="/redesain-siatek/jurusan-matkul-rps" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-matkul-rps' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-book w-5 text-center text-sm opacity-70"></i><span>Matakuliah / RPS</span></a>
                <a href="/redesain-siatek/jurusan-matkul-mbkm" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-matkul-mbkm' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-globe w-5 text-center text-sm opacity-70"></i><span>Matakuliah MBKM</span></a>
                <a href="/redesain-siatek/jurusan-organisasi" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-organisasi' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-users w-5 text-center text-sm opacity-70"></i><span>Organisasi</span></a>
                <a href="/redesain-siatek/jurusan-peminjaman" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-peminjaman' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-exchange-alt w-5 text-center text-sm opacity-70"></i><span>Peminjaman</span></a>
                <a href="/redesain-siatek/jurusan-program-extra" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-program-extra' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-star w-5 text-center text-sm opacity-70"></i><span>Program Extra</span></a>
                <a href="/redesain-siatek/jurusan-skp" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-skp' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-file-alt w-5 text-center text-sm opacity-70"></i><span>SKP</span></a>
                <a href="/redesain-siatek/jurusan-surat" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-surat' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-envelope w-5 text-center text-sm opacity-70"></i><span>Surat</span></a>
                <a href="/redesain-siatek/jurusan-surat-penunjukkan" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-surat-penunjukkan' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-file-check w-5 text-center text-sm opacity-70"></i><span>Surat Penunjukkan</span></a>
                <a href="/redesain-siatek/jurusan-tracer" class="sub-item flex items-center space-x-3 px-4 py-2 rounded-r-lg text-[13px] transition <?= $activePage === 'jurusan-tracer' ? 'nav-active' : 'nav-item' ?>"><i class="fas fa-map-marker-alt w-5 text-center text-sm opacity-70"></i><span>Tracer Studi</span></a>
            </div>
        </div>

        <!-- Pengaturan -->
        <div class="pt-3 mt-3 border-t border-white/10">
            <p class="nav-section-header">Pengaturan</p>
            <a href="#" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition">
                <i class="fas fa-cog w-5 text-center text-base"></i>
                <span class="flex-1">Konfigurasi</span>
            </a>
        </div>
    </nav>

    <!-- Footer (logout) -->
    <div class="border-t border-white/10 p-2 flex-shrink-0">
        <a href="#" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition text-red-300 hover:!text-red-200">
            <i class="fas fa-sign-out-alt w-5 text-center text-base"></i>
            <span class="flex-1">Logout</span>
        </a>
    </div>
</aside>

<style>
@media (min-width: 768px) {
    #sidebar { display: flex !important; transform: translateX(0) !important; position: static !important; height: 100vh; }
    #sidebarOverlay { display: none !important; }
}
</style>