<?php
/**
 * Halaman placeholder untuk sementara: Arsip Dokumen Mahasiswa
 */
require_once __DIR__ . '/../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');
$nim = $_SESSION['nim'] ?? '';
?>
<main class="content-area content-scroll">
    <section class="mb-6">
        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 mx-auto mb-4">
                <i class="fas fa-archive"></i>
            </div>
            <h2 class="text-base font-bold text-slate-800">Halaman Arsip Dokumen</h2>
            <p class="mt-2 text-xs text-slate-500">Arsip dokumen resmi: SK, transkrip sementara, surat izin penelitian, dll.</p>
            <p class="mt-4 text-xs text-slate-400">Halaman ini dalam tahap pengembangan. Silakan coba halaman lain.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="/redesain-siatek/mahasiswa/beranda" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-200">
                    <i class="fas fa-arrow-left"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>
</main>