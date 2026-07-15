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
 * File juga bisa punya helper/modal/partial di folder yang sama, misal:
 *   require_once __DIR__ . '/pages/pengelolaan/broadcast/includes/modal-konfirmasi.php';
 *
 * Untuk include file lain relatif ke halaman, gunakan __DIR__ di file halaman:
 *   require_once __DIR__ . '/modal-jadwal.php';
 */

declare(strict_types=1);
session_start();

/* ============ Route Map ============ */
$routeMap = [
    // === Top-level ===
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
    'jurusan-arsip'              => ['path' => 'jurusan/arsip',                'title' => 'Jurusan - Arsip'],
    'jurusan-berita'             => ['path' => 'jurusan/berita',               'title' => 'Jurusan - Berita'],
    'jurusan-tridharma'          => ['path' => 'jurusan/tridharma',            'title' => 'Jurusan - Tridharma'],
    'jurusan-dok-akademik'       => ['path' => 'jurusan/dokumen/akademik',     'title' => 'Jurusan - Dokumen Akademik'],
    'jurusan-dok-akreditasi'     => ['path' => 'jurusan/dokumen/akreditasi',   'title' => 'Jurusan - Dokumen Akreditasi'],
    'jurusan-dok-lkps'           => ['path' => 'jurusan/dokumen/lkps',         'title' => 'Jurusan - Dokumen LKPS'],
    'jurusan-jadwal'             => ['path' => 'jurusan/jadwal',               'title' => 'Jurusan - Jadwal'],
    'jurusan-kerjasama'          => ['path' => 'jurusan/kerjasama',            'title' => 'Jurusan - Kerja Sama'],
    'jurusan-keuangan'           => ['path' => 'jurusan/keuangan',             'title' => 'Jurusan - Keuangan'],
    'jurusan-kurikulum'          => ['path' => 'jurusan/kurikulum',            'title' => 'Jurusan - Kurikulum'],
    'jurusan-laporan'            => ['path' => 'jurusan/laporan',              'title' => 'Jurusan - Laporan'],
    'jurusan-matkul-rps'         => ['path' => 'jurusan/matakuliah/rps',       'title' => 'Jurusan - Matakuliah / RPS'],
    'jurusan-matkul-mbkm'        => ['path' => 'jurusan/matakuliah/mbkm',      'title' => 'Jurusan - Matakuliah MBKM'],
    'jurusan-organisasi'         => ['path' => 'jurusan/organisasi',           'title' => 'Jurusan - Organisasi'],
    'jurusan-peminjaman'         => ['path' => 'jurusan/peminjaman',           'title' => 'Jurusan - Peminjaman'],
    'jurusan-program-extra'      => ['path' => 'jurusan/program-extra',        'title' => 'Jurusan - Program Extra'],
    'jurusan-skp'                => ['path' => 'jurusan/skp',                  'title' => 'Jurusan - SKP'],
    'jurusan-surat'              => ['path' => 'jurusan/surat',                'title' => 'Jurusan - Surat'],
    'jurusan-surat-penunjukkan'  => ['path' => 'jurusan/surat/penunjukkan',    'title' => 'Jurusan - Surat Penunjukkan'],
    'jurusan-tracer'             => ['path' => 'jurusan/tracer',               'title' => 'Jurusan - Tracer Studi'],
];

/* Alias (URL singkat yang dikenal) */
$aliases = [
    'dashboard' => 'beranda',
];

/* ============ Resolve Slug ============ */
$rawPage = isset($_GET['page']) ? (string) $_GET['page'] : '';
$pageKey = preg_replace('/[^a-z0-9\-]/', '', strtolower($rawPage));
if ($pageKey === '') $pageKey = 'dashboard';

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
    $pageKey = 'dashboard';
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
    <link rel="preconnect" href="https://ui-avatars.com" crossorigin>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Local CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="app-shell sidebar-collapsed">

    <!-- ========= SIDEBAR (statis) ========= -->
    <?php include __DIR__ . '/components/sidebar.php'; ?>

    <!-- ========= APP AREA ========= -->
    <div class="app-area">

        <!-- Top Navbar (statis) -->
        <?php include __DIR__ . '/components/top-navbar.php'; ?>

        <!-- ===== KONTEN DINAMIS (hanya bagian ini yang berubah) ===== -->
        <?php include $pageFile; ?>

        <!-- Footer (statis) -->
        <?php include __DIR__ . '/components/footer.php'; ?>

    </div>

    <!-- Modal TTD -->
    <div class="modal fade" id="signModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-pen-fill me-1"></i>Tanda Tangan Digital</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-2" style="font-size:.82rem;">Tanda tangan elektronik ini mengesahkan dokumen di bawah ini.</p>
                    <canvas id="signatureCanvas" class="signature-pad"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" id="clearSignature">Hapus</button>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Tanda Tangani</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Local JS -->
    <script src="js/dashboard.js"></script>

</body>
</html>