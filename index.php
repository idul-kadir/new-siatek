<?php
/**
 * Front-controller / Router SIATEK.
 *
 * Cara kerja:
 *   - Setiap halaman punya slug via ?page=xxx
 *   - STATIK: sidebar, top-navbar, footer (di-include sekali)
 *   - KONTEN: pages/<folder>/.../<id>.php  (struktur nested sesuai menu)
 *
 * Default: ?page=dashboard → pages/beranda/beranda.php
 *
 * Mapping slug → folder ada di $routeMap.
 *
 * Layout: Tailwind CSS (lihat template.html). Bootstrap dihapus dari
 * dependency utama, namun fallback ada di css/dashboard.css untuk
 * halaman lain yang masih menggunakan class Bootstrap.
 */

declare(strict_types=1);
session_start();

/* ============ Route Map ============ */
$routeMap = [
    // === Top-level ===
    'dashboard'         => ['path' => 'beranda',                 'title' => 'Beranda'],
    'beranda'           => ['path' => 'beranda',                 'title' => 'Beranda'],

    // === Pengelolaan > Mahasiswa ===
    'mhs-skripsi'       => ['path' => 'pengelolaan/mahasiswa/skripsi',    'title' => 'Mahasiswa - Skripsi'],
    'mhs-kp'            => ['path' => 'pengelolaan/mahasiswa/kp',         'title' => 'Mahasiswa - Kerja Praktek'],
    'mhs-verifikasi'    => ['path' => 'pengelolaan/mahasiswa/verifikasi', 'title' => 'Mahasiswa - Verifikasi Berkas'],
    'mhs-alumni'        => ['path' => 'pengelolaan/mahasiswa/alumni',     'title' => 'Mahasiswa - Data Alumni'],
    'mhs-kegiatan'      => ['path' => 'pengelolaan/mahasiswa/kegiatan',   'title' => 'Mahasiswa - Kegiatan'],

    // === Pengelolaan > Bimbingan ===
    'bimbingan-skripsi' => ['path' => 'pengelolaan/bimbingan/skripsi', 'title' => 'Bimbingan Skripsi'],
    'bimbingan-kp'      => ['path' => 'pengelolaan/bimbingan/kp',      'title' => 'Bimbingan Kerja Praktek'],
    'bimbingan-pa'      => ['path' => 'pengelolaan/bimbingan/pa',      'title' => 'Bimbingan Penasihat Akademik'],

    // === Pengelolaan > Pengguna ===
    'pengguna-mahasiswa' => ['path' => 'pengelolaan/pengguna/mahasiswa', 'title' => 'Pengguna Mahasiswa'],
    'pengguna-dosen'     => ['path' => 'pengelolaan/pengguna/dosen',     'title' => 'Pengguna Dosen'],

    // === Pengelolaan > Broadcast ===
    'pengelolaan-broadcast' => ['path' => 'pengelolaan/broadcast', 'title' => 'Pengelolaan Broadcast'],

    // === Pangkalan Data ===
    'pd-pendidikan' => ['path' => 'pangkalan-data/pendidikan', 'title' => 'Pangkalan Data - Pendidikan'],
    'pd-penelitian' => ['path' => 'pangkalan-data/penelitian', 'title' => 'Pangkalan Data - Penelitian'],
    'pd-pengabdian' => ['path' => 'pangkalan-data/pengabdian', 'title' => 'Pangkalan Data - Pengabdian'],
    'pd-penunjang'  => ['path' => 'pangkalan-data/penunjang',  'title' => 'Pangkalan Data - Penunjang'],
    'pd-arsip'      => ['path' => 'pangkalan-data/arsip',      'title' => 'Pangkalan Data - Arsip'],
    'pd-skp'        => ['path' => 'pangkalan-data/skp',        'title' => 'Pangkalan Data - SKP'],

    // === Top-level Lain ===
    'biodata' => ['path' => 'biodata', 'title' => 'Biodata'],
    'jadwal'  => ['path' => 'jadwal',  'title' => 'Jadwal Kuliah'],
    'sister'  => ['path' => 'sister',  'title' => 'Sinkronisasi Sister'],

    // === Jurusan ===
    'arsip-jurusan'              => ['path' => 'jurusan/arsip',                'title' => 'Jurusan - Arsip'],
    'jurusan-berita'             => ['path' => 'jurusan/berita',               'title' => 'Jurusan - Berita'],
    'tulis-berita'               => ['path' => 'jurusan/berita',               'title' => 'Jurusan - Tulis Berita'],
    'jurusan-tridharma'          => ['path' => 'jurusan/tridharma',            'title' => 'Jurusan - Tridharma'],
    'jurusan-dok-akademik'       => ['path' => 'jurusan/akademik',         'title' => 'Jurusan - Dokumen Akademik'],
    'jurusan-dok-akreditasi'     => ['path' => 'jurusan/akreditasi',       'title' => 'Jurusan - Dokumen Akreditasi'],
    'jurusan-dok-lkps'           => ['path' => 'jurusan/lkps',             'title' => 'Jurusan - Dokumen LKPS'],
    'jurusan-jadwal'             => ['path' => 'jurusan/jadwal',               'title' => 'Jurusan - Jadwal'],
    'jurusan-kerjasama'          => ['path' => 'jurusan/kerjasama',            'title' => 'Jurusan - Kerja Sama'],
    'jurusan-keuangan'           => ['path' => 'jurusan/keuangan',             'title' => 'Jurusan - Keuangan'],
    'jurusan-kurikulum'          => ['path' => 'jurusan/kurikulum',            'title' => 'Jurusan - Kurikulum'],
    'jurusan-laporan'            => ['path' => 'jurusan/laporan',              'title' => 'Jurusan - Laporan'],
    'jurusan-matkul-rps'         => ['path' => 'jurusan/matakuliah',           'title' => 'Jurusan - Matakuliah / RPS'],
    'jurusan-matkul-mbkm'        => ['path' => 'jurusan/matakuliah-mbkm',      'title' => 'Jurusan - Matakuliah MBKM'],
    'jurusan-organisasi'         => ['path' => 'jurusan/organisasi',           'title' => 'Jurusan - Organisasi'],
    'jurusan-peminjaman'         => ['path' => 'jurusan/peminjaman',           'title' => 'Jurusan - Peminjaman'],
    'jurusan-program-extra'      => ['path' => 'jurusan/program-extra',        'title' => 'Jurusan - Program Extra'],
    'jurusan-skp'                => ['path' => 'jurusan/skp',                  'title' => 'Jurusan - SKP'],
    'jurusan-surat'              => ['path' => 'jurusan/surat',                'title' => 'Jurusan - Surat'],
    'jurusan-surat-penunjukkan'  => ['path' => 'jurusan/penunjukkan',         'title' => 'Jurusan - Surat Penunjukkan'],
    'jurusan-tracer'             => ['path' => 'jurusan/tracer',               'title' => 'Jurusan - Tracer Studi'],
];

/* Alias (URL singkat yang dikenal) */
$aliases = [
    'dashboard' => 'beranda',
];

/* ============ Resolve Slug ============ */
$rawPage = isset($_GET['page']) ? (string) $_GET['page'] : '';
$pageKey = preg_replace('/[^a-z0-9\-]/', '', strtolower($rawPage));
if ($pageKey === '') $pageKey = 'beranda';

$pageKey = $aliases[$pageKey] ?? $pageKey;
$route = $routeMap[$pageKey] ?? null;

$pagesDir = __DIR__ . '/pages';

/* ============ Resolve File Path ============ */
if ($route) {
    $pageFile = "{$pagesDir}/{$route['path']}/{$pageKey}.php";
    if (!is_file($pageFile)) {
        // fallback ke beranda, dengan flag not-found untuk UI
        $route = $routeMap['beranda'];
        $pageFile = "{$pagesDir}/{$route['path']}/beranda.php";
        $pageTitle = 'Halaman Tidak Ditemukan';
        http_response_code(404);
        $notFound = true;
    } else {
        $pageTitle = $route['title'];
        $notFound = false;
    }
} else {
    // slug tidak dikenal sama sekali
    $route = $routeMap['beranda'];
    $pageFile = "{$pagesDir}/{$route['path']}/beranda.php";
    $pageTitle = 'Halaman Tidak Ditemukan';
    http_response_code(404);
    $notFound = true;
    $pageKey = 'beranda';
}

/* Untuk highlight sidebar di sidebar.php */
$activePage = $pageKey;

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?> — SIATEK</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://ui-avatars.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Local CSS (fallback untuk halaman lain yang masih pakai class Bootstrap) -->
    <link rel="stylesheet" href="css/dashboard.css">

    <style>
        * { font-family: "Inter", sans-serif; }
        body { background-color: #f1f5f9; }

        /* ===== Sidebar Navy Gelap ===== */
        .sidebar-bg { background-color: #1a365d; }

        /* ===== Nav Item Default ===== */
        .nav-item {
            color: #94a3b8;
        }
        .nav-item:hover {
            background-color: #234670;
            color: #fff;
        }
        .nav-active {
            background-color: #11243d !important;
            color: #fff !important;
            font-weight: 500 !important;
            border-left: 4px solid #f97316 !important;
        }
        .nav-active > i { color: #f97316 !important; opacity: 1 !important; }
        .nav-active > span { color: #fff !important; }

        .nav-parent.has-active {
            background-color: #234670 !important;
            color: #fff !important;
            font-weight: 500 !important;
        }
        .nav-parent.has-active > i { color: #f97316 !important; opacity: 1 !important; }

        /* ===== Nav Group Section Header ===== */
        .nav-section-header {
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 14px 16px 6px;
        }

        /* ===== Submenu Smooth Accordion ===== */
        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }
        .nav-submenu.open { max-height: 1200px; }

        .nav-submenu--nested {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
            padding-left: 14px;
        }
        .nav-submenu--nested.open { max-height: 500px; }

        .nav-chevron {
            transition: transform 0.25s ease;
        }
        .nav-parent.open .nav-chevron { transform: rotate(90deg); }
        .sub-parent .sub-chevron {
            margin-left: auto;
            transition: transform 0.25s ease;
        }
        .sub-parent.open .sub-chevron { transform: rotate(90deg); }

        /* ===== Card hover effect ===== */
        .card-hover {
            transition: all 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.07);
        }

        /* ===== Card shadow clean & tipis ===== */
        .card-shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }

        /* Sidebar scrollbar (di konteks navy) */
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }

        /* ===== Mobile sidebar slide (drawer di bawah navbar) ===== */
        @media (max-width: 767.98px) {
            #sidebar {
                position: fixed;
                top: 4rem;
                left: 0;
                bottom: 0;
                width: 256px;
                display: flex !important;
                flex-direction: column;
                transform: translateX(-100%);
                transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1040;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
            }
            #sidebar.mobile-open { transform: translateX(0); }
        }

        /* ===== Toggle button (mobile): navy + efek kelip pelan, abu saat sidebar terbuka ===== */
        @media (max-width: 767.98px) {
            #sidebarToggle {
                background-color: #1a365d !important;
                border-color: #1a365d !important;
                color: #ffffff !important;
                animation: siatekToggleBlink 4.5s ease-in-out infinite;
            }
            body.mobile-nav-open #sidebarToggle {
                background-color: #e2e8f0 !important;
                border-color: #cbd5e1 !important;
                color: #64748b !important;
                animation: none;
            }
        }
        @keyframes siatekToggleBlink {
            0%, 90%, 100% { opacity: 1; }
            95%           { opacity: 0.45; }
        }

        /* ===== Ikon tombol: pop halus saat berganti ===== */
        .icon-pop { animation: siatekIconPop 0.35s ease; }
        @keyframes siatekIconPop {
            0%   { transform: scale(0.5) rotate(-90deg); opacity: 0.3; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        /* ===== Desktop sidebar collapse (sembunyikan supaya lembar kerja lebar) ===== */
        @media (min-width: 768px) {
            #sidebar {
                width: 256px;
                min-width: 0;
                overflow-x: hidden;
                transition: width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            }
            body.sidebar-collapsed #sidebar {
                width: 0;
            }
        }

        /* ===== Modal overlay (Tailwind-style) ===== */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 1050; padding: 16px;
            visibility: hidden; opacity: 0;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .modal-overlay.show { visibility: visible; opacity: 1; }
        .signature-pad {
            width: 100%; height: 180px; background: #fff;
            border: 1px solid #e2e8f0; border-radius: 8px;
            touch-action: none;
        }
    </style>
</head>
<body class="bg-slate-100">

    <div class="flex h-screen overflow-hidden">

        <!-- ========= SIDEBAR (statis) ========= -->
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <!-- ========= APP AREA ========= -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar (statis) -->
            <?php include __DIR__ . '/components/top-navbar.php'; ?>

            <!-- ===== KONTEN DINAMIS (hanya bagian ini yang berubah) ===== -->
            <?php include $pageFile; ?>

            <!-- Footer (statis) -->
            <?php include __DIR__ . '/components/footer.php'; ?>

        </div>
    </div>

    <!-- ===== Modal TTD (Tailwind, tanpa Bootstrap JS) ===== -->
    <div class="modal-overlay" id="signModal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen-fancy mr-1 text-[#f97316]"></i>Tanda Tangan Digital</h6>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
            </div>
            <div class="p-5">
                <p class="text-slate-500 mb-3 text-xs">Tanda tangan elektronik ini mengesahkan dokumen di bawah ini.</p>
                <canvas id="signatureCanvas" class="signature-pad"></canvas>
            </div>
            <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
                <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" id="clearSignature">Hapus</button>
                <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-[#1a365d] hover:bg-[#234670] text-white font-medium" id="confirmSignature">Tanda Tangani</button>
            </div>
        </div>
    </div>

    <!-- ===== Modal Konfirmasi Hapus (Global) ===== -->
    <div class="modal-overlay" id="globalHapusModal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-exclamation-triangle mr-1 text-rose-500"></i>Konfirmasi Hapus</h6>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" onclick="closeGlobalHapus()">&times;</button>
            </div>
            <div class="p-5">
                <p class="text-sm text-slate-600">Yakin ingin menghapus item ini?</p>
                <p id="globalHapusNama" class="mt-2 text-sm font-semibold text-slate-800 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200 hidden"></p>
            </div>
            <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
                <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" onclick="closeGlobalHapus()">Batal</button>
                <button type="button" id="globalHapusBtn" class="px-4 py-1.5 text-xs rounded-md bg-rose-500 hover:bg-rose-600 text-white font-semibold">Hapus</button>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Local JS -->
    <script src="js/dashboard.js"></script>

    <script>
    /* ===== Global Delete Confirmation ===== */
    var _globalHapusCb = null;
    function konfirmasiHapus(nama, onConfirm) {
        var modal = document.getElementById('globalHapusModal');
        var namaEl = document.getElementById('globalHapusNama');
        if (nama) { namaEl.textContent = nama; namaEl.classList.remove('hidden'); }
        else { namaEl.classList.add('hidden'); }
        _globalHapusCb = typeof onConfirm === 'function' ? onConfirm : null;
        modal.classList.add('show');
    }
    function closeGlobalHapus() {
        document.getElementById('globalHapusModal').classList.remove('show');
        _globalHapusCb = null;
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('globalHapusBtn').addEventListener('click', function () {
            closeGlobalHapus();
            if (_globalHapusCb) _globalHapusCb();
        });
    });
    </script>

</body>
</html>
