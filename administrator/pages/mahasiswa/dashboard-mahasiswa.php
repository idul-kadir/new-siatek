<?php
/**
 * DASHBOARD MAHASISWA — halaman setelah mahasiswa login.
 * Data nyata dari tabel `mahasiswa` via session nim; modul akademik
 * lain (skripsi, jadwal, pengumuman) menyusul saat DB-nya tersedia.
 */
require_once __DIR__ . '/../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

$nim = $_SESSION['nim'] ?? $_SESSION['user_id'] ?? '';
$stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM mahasiswa WHERE nim = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $nim);
mysqli_stmt_execute($stmt);
$m = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$m) {
    echo '<main class="content-area content-scroll"><div class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">'
        . '<i class="fas fa-user-graduate text-4xl text-slate-300"></i>'
        . '<p class="mt-3 font-medium text-slate-500">Data mahasiswa dengan NIM ' . tampil($nim) . ' tidak ditemukan.</p></div></main>';
    return;
}
$aktif = strtolower(trim((string)$m['status'])) === 'aktif';
$inisial = tampil(mb_strtoupper(mb_substr(preg_replace('/[^\p{L} ]/u', '', explode(' ', trim((string)$m['nama']))[0] ?? ''), 0, 1))
    . mb_substr(preg_replace('/[^A-Za-z]/', '', explode(' ', trim((string)$m['nama']))[1] ?? ''), 0, 1));
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) both; }

    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none; background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px); background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }

    .ann-row { display: flex; gap: .75rem; padding: .7rem 0; align-items: flex-start; }
    .ann-row + .ann-row { border-top: 1px dashed #f1f5f9; }
    .ann-row .a-ico { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: .55rem; flex-shrink: 0; font-size: .7rem; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Dashboard Mahasiswa</h1>
                    <p class="text-xs text-slate-500">Ringkasan perkuliahan dan aktivitas akademik Anda.</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= $aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?php if ($aktif): ?><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span><?php endif; ?>
                <?= $aktif ? 'Mahasiswa Aktif' : 'Status: ' . tampil($m['status']) ?>
            </span>
        </div>
    </section>

    <!-- ===== Kartu Profil ===== -->
    <section class="mb-5">
        <div class="reveal rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-5">
                <svg width="72" height="72" viewBox="0 0 72 72" class="shrink-0" aria-hidden="true">
                    <defs><linearGradient id="mhsAvGrad" x1="0" y1="0" x2="72" y2="72" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#1a365d"/><stop offset="1" stop-color="#f97316"/>
                    </linearGradient></defs>
                    <rect width="72" height="72" rx="36" fill="url(#mhsAvGrad)"/>
                    <text x="36" y="38" fill="#fff" font-family="Inter, sans-serif" font-size="24" font-weight="700" text-anchor="middle" dominant-baseline="central"><?= $inisial ?></text>
                </svg>
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-lg font-bold tracking-tight text-slate-800"><?= tampil($m['nama']) ?></h2>
                    <p class="mt-0.5 text-xs text-slate-500">NIM <?= tampil($m['nim']) ?><?= $m['angkatan'] ? ' · Angkatan ' . (int)$m['angkatan'] : '' ?></p>
                </div>
                <div class="w-full sm:w-auto lg:min-w-[280px]">
                    <div class="rounded-xl bg-slate-50 px-4 py-2">
                        <div class="fact" style="display:flex;align-items:flex-start;gap:.65rem;padding:.45rem 0;">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.85rem;height:1.85rem;border-radius:.55rem;background:#fff7ed;color:#ea580c;font-size:.72rem;flex-shrink:0;"><i class="fas fa-graduation-cap"></i></span>
                            <div><p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Program Studi</p><p style="font-size:.8rem;font-weight:600;color:#334155;"><?= tampil($m['prodi'] ?: '—') ?></p></div>
                        </div>
                        <div class="fact" style="display:flex;align-items:flex-start;gap:.65rem;padding:.45rem 0;border-top:1px dashed #f1f5f9;">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:1.85rem;height:1.85rem;border-radius:.55rem;background:#fff7ed;color:#ea580c;font-size:.72rem;flex-shrink:0;"><i class="fas fa-envelope"></i></span>
                            <div><p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Email</p><p style="font-size:.8rem;font-weight:600;color:#334155;"><?= tampil($m['email'] ?: '—') ?></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Tiles ===== -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-orange tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-xs font-medium uppercase tracking-wide text-white/85">Fase Skripsi</p>
                    <p class="mt-2 text-lg font-bold leading-snug">Belum Mulai</p>
                    <p class="mt-1 text-[11px] text-white/70">daftar skripsi utk memulai</p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-book-open-reader"></i></span>
            </div>
        </div>
        <div class="tile-sky tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-xs font-medium uppercase tracking-wide text-white/85">Jadwal Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold">0</p>
                    <p class="mt-1 text-[11px] text-white/70">kelas terjadwal</p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-calendar-day"></i></span>
            </div>
        </div>
        <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengumuman Baru</p>
                    <p class="mt-2 text-2xl font-bold">3</p>
                    <p class="mt-1 text-[11px] text-white/70">7 hari terakhir</p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-bullhorn"></i></span>
            </div>
        </div>
        <div class="tile-violet tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0"><p class="text-xs font-medium uppercase tracking-wide text-white/85">Angkatan</p>
                    <p class="mt-2 text-2xl font-bold"><?= $m['angkatan'] ? (int)$m['angkatan'] : '—' ?></p>
                    <p class="mt-1 text-[11px] text-white/70">tahun masuk</p></div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-users"></i></span>
            </div>
        </div>
    </section>

    <!-- ===== Dua kolom konten ===== -->
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="reveal rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-bullhorn mr-1.5 text-orange-500"></i>Pengumuman Terbaru</h3>
            </div>
            <div class="px-5 py-2">
                <div class="ann-row"><span class="a-ico bg-orange-100 text-orange-600"><i class="fas fa-thumbtack"></i></span>
                    <div class="min-w-0"><p class="text-[13px] font-semibold text-slate-800">Sosialisasi SKP &amp; Perkuliahan Semester Ganjil</p><p class="text-[11px] text-slate-400">Yasin Mohamad · 2 jam lalu</p></div></div>
                <div class="ann-row"><span class="a-ico bg-sky-100 text-sky-600"><i class="fas fa-file-lines"></i></span>
                    <div class="min-w-0"><p class="text-[13px] font-semibold text-slate-800">Jadwal Kuliah Semester Ganjil TA 2026/2027</p><p class="text-[11px] text-slate-400">Ridwan Kadir · kemarin</p></div></div>
                <div class="ann-row"><span class="a-ico bg-violet-100 text-violet-600"><i class="fas fa-flask"></i></span>
                    <div class="min-w-0"><p class="text-[13px] font-semibold text-slate-800">Pendaftaran Praktikum Laboratorium Dibuka</p><p class="text-[11px] text-slate-400">Ridwan Kadir · 3 hari lalu</p></div></div>
            </div>
        </div>

        <div class="reveal rounded-xl border border-dashed border-slate-300 bg-white shadow-sm" style="animation-delay:.08s">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-calendar-day mr-1.5 text-sky-500"></i>Jadwal Hari Ini</h3>
            </div>
            <div class="py-14 text-center">
                <i class="fas fa-mug-hot text-4xl text-slate-200"></i>
                <p class="mt-3 text-sm font-medium text-slate-500">Tidak ada kelas terjadwal hari ini</p>
                <p class="mt-1 text-xs text-slate-400">Modul jadwal kuliah mahasiswa akan terhubung setelah data jadwal siap.</p>
            </div>
        </div>
    </section>

</main>
