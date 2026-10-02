<?php
/**
 * Halaman placeholder untuk sementara: Beranda Mahasiswa
 */
require_once __DIR__ . '/../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

$nim = $_SESSION['nim'] ?? '';
$stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM mahasiswa WHERE TRIM(nim) = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $nim);
mysqli_stmt_execute($stmt);
$m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$m) {
    echo '<main class="content-area content-scroll"><div class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">'
        . '<i class="fas fa-user-graduate text-4xl text-slate-300"></i>'
        . '<p class="mt-3 font-medium text-slate-500">Data mahasiswa dengan NIM ' . tampil($nim) . ' tidak ditemukan.</p></div></main>';
    return;
}
?>
<main class="content-area content-scroll">
    <section class="mb-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-100 text-xl text-orange-600">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-bold text-slate-800 truncate"><?= tampil($m['nama']) ?></h1>
                    <p class="text-xs text-slate-500">NIM <?= tampil($m['nim']) ?> · Prodi <?= tampil($m['prodi']) ?> · Angkatan <?= tampil($m['angkatan']) ?></p>
                </div>
                <?php
                $aktif = strtolower(trim((string)$m['status'])) === 'aktif';
                ?>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= $aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                    <?php if ($aktif): ?><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span><?php endif; ?>
                    <?= $aktif ? 'Mahasiswa Aktif' : tampil($m['status']) ?>
                </span>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <a href="/redesain-siatek/mahasiswa/biodata" class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center hover:bg-orange-50 hover:border-orange-200 transition">
                    <i class="fas fa-id-badge text-lg text-orange-500"></i>
                    <span class="text-xs font-semibold text-slate-700">Biodata</span>
                </a>
                <a href="/redesain-siatek/mahasiswa/skripsi" class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center hover:bg-orange-50 hover:border-orange-200 transition">
                    <i class="fas fa-book text-lg text-orange-500"></i>
                    <span class="text-xs font-semibold text-slate-700">Skripsi</span>
                </a>
                <a href="/redesain-siatek/mahasiswa/kp" class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center hover:bg-orange-50 hover:border-orange-200 transition">
                    <i class="fas fa-briefcase text-lg text-orange-500"></i>
                    <span class="text-xs font-semibold text-slate-700">Kerja Praktek</span>
                </a>
                <a href="/redesain-siatek/mahasiswa/arsip" class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center hover:bg-orange-50 hover:border-orange-200 transition">
                    <i class="fas fa-archive text-lg text-orange-500"></i>
                    <span class="text-xs font-semibold text-slate-700">Arsip</span>
                </a>
            </div>
        </div>
    </section>
</main>