<?php
/**
 * Front-controller / Router SIATEK (Portal Mahasiswa).
 *
 * Layout sama dengan administrator (Tailwind CSS).
 */

declare(strict_types=1);
session_start();

/* ============ Route Map ============ */
$routeMap = [
    'dashboard' => ['path' => 'beranda', 'title' => 'Beranda Mahasiswa'],
    'beranda'   => ['path' => 'beranda', 'title' => 'Beranda Mahasiswa'],
    'skripsi'   => ['path' => 'skripsi', 'title' => 'Skripsi Mahasiswa'],
    'kp'        => ['path' => 'kp',      'title' => 'Kerja Praktek'],
    'biodata'   => ['path' => 'biodata', 'title' => 'Biodata Mahasiswa'],
    'arsip'     => ['path' => 'arsip',   'title' => 'Arsip Dokumen'],
];

$aliases = ['dashboard' => 'beranda'];

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
    $route = $routeMap['beranda'];
    $pageFile = "{$pagesDir}/{$route['path']}/beranda.php";
    $pageTitle = 'Halaman Tidak Ditemukan';
    http_response_code(404);
    $notFound = true;
    $pageKey = 'beranda';
}

$activePage = $pageKey;

/* ---------- Auth Guard ---------- */
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']) || !empty($_SESSION['nim']);
$peran      = $_SESSION['peran'] ?? (!empty($_SESSION['nim']) ? 'mahasiswa' : 'staf');

if (!$sudahLogin) {
    header('Location: /redesain-siatek/');
    exit;
}
if ($peran !== 'mahasiswa') {
    header('Location: /redesain-siatek/administrator/');
    exit;
}

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?> - SIATEK Mahasiswa</title>

    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../administrator/css/dashboard.css">

    <style>
        * { font-family: "Inter", sans-serif; }
        body { background-color: #f1f5f9; }

        .sidebar-bg { background-color: #1a365d; }
        .nav-item { color: #94a3b8; }
        .nav-item:hover { background-color: #234670; color: #fff; }
        .nav-active {
            background-color: #11243d !important;
            color: #fff !important;
            font-weight: 500 !important;
            border-left: 4px solid #f97316 !important;
        }
        .nav-active > i { color: #f97316 !important; opacity: 1 !important; }
        .nav-active > span { color: #fff !important; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }

        @media (max-width: 767.98px) {
            #sidebar {
                position: fixed; top: 4rem; left: 0; bottom: 0; width: 256px;
                display: flex !important; flex-direction: column;
                transform: translateX(-100%); transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1040; box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
            }
            #sidebar.mobile-open { transform: translateX(0); }
        }

        @media (min-width: 768px) {
            #sidebar {
                width: 256px; min-width: 0; overflow-x: hidden;
                transition: width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }
        
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 1050; padding: 16px; visibility: hidden; opacity: 0;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .modal-overlay.show { visibility: visible; opacity: 1; }
    </style>
</head>
<body class="bg-slate-100">
    <div class="flex h-screen overflow-hidden">
        <?php include __DIR__ . '/components/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include __DIR__ . '/components/top-navbar.php'; ?>
            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <?php include $pageFile; ?>
        </div>
    </div>
    
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

    <script src="../administrator/js/dashboard.js"></script>
    <script>
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
        var m = document.getElementById('globalHapusModal');
        if (m && m.classList) m.classList.remove('show');
        _globalHapusCb = null;
    }
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('globalHapusBtn');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var cb = _globalHapusCb;
            closeGlobalHapus();
            if (typeof cb === 'function') cb();
        });
    });
    </script>
</body>
</html>