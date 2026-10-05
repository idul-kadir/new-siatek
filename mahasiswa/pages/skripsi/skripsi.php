<?php
/**
 * Halaman: Skripsi Mahasiswa (dummy data layout 2 kolom)
 */
require_once __DIR__ . '/../../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');
$nim = $_SESSION['nim'] ?? '';

/* --- Data Dummy Informasi Skripsi --- */
$infoSkripsi = [
    'nama'          => $_SESSION['nama'] ?? 'Mohamad Rivan Abdullah',
    'nim'           => $nim !== '' ? $nim : '05201425002',
    'prodi'         => $_SESSION['prodi'] ?? 'S1 Teknik Informatika',
    'angkatan'      => '2020',
    'judul'         => 'Rancang Bangun Sistem Informasi Monitoring Skripsi Berbasis SOP Interaktif Menggunakan Web Responsive',
    'bidang'        => 'Rekayasa Perangkat Lunak & Sistem Informasi',
    'pembimbing_1'  => 'Dr. Ir. Mohamad S. Abubakar, M.T.',
    'nip_pemb_1'    => '197508122002121001',
    'pembimbing_2'  => 'Nur Rahmawati, S.T., M.Kom.',
    'nip_pemb_2'    => '198403152010122003',
    'pa'            => 'Bagus Priyo Raharjo, S.T., M.T.',
    'nip_pa'        => '198205162010121005',
    'ujian_prop'    => 'Belum dijadwalkan',
    'ujian_hasil'   => 'Belum dijadwalkan',
    'ujian_tutup'   => 'Belum dijadwalkan',
];

/* --- Ambil data alur SOP Skripsi dari database --- */
$steps = [];
$querySop = query("SELECT * FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
$idxCounter = 1;

if ($querySop && mysqli_num_rows($querySop) > 0) {
    while ($row = mysqli_fetch_assoc($querySop)) {
        // Status dummy awal: step 1 selesai, step 2 aktif, sisanya menunggu
        $st = ($idxCounter == 1) ? 'selesai' : (($idxCounter == 2) ? 'aktif' : 'menunggu');
        
        $steps[] = [
            'id'       => $row['no'],
            'urutan'   => $idxCounter,
            'judul'    => $row['keterangan'],
            'desc'     => 'Eksekutor: ' . ucfirst(strtolower($row['eksekutor'])),
            'executor' => ucfirst(strtolower($row['eksekutor'])),
            'status'   => $st,
        ];
        $idxCounter++;
    }
}

$total    = count($steps);
$selesai  = count(array_filter($steps, fn($s) => $s['status'] === 'selesai'));
$progres  = $total > 0 ? round($selesai / $total * 100) : 0;

/* Ambil step aktif saat ini */
$currentStep = null;
foreach ($steps as $s) {
    if ($s['status'] === 'aktif') {
        $currentStep = $s;
        break;
    }
}
if (!$currentStep && !empty($steps)) {
    $currentStep = $steps[0];
}
?>
<style>
    .content-scroll { overflow-y: auto; height: calc(100vh - 64px); }

    /* Custom Scrollbar kecil untuk panel SOP */
    .sop-scroll { max-height: 500px; overflow-y: auto; padding-right: 8px; }
    .sop-scroll::-webkit-scrollbar { width: 4px; }
    .sop-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .sop-scroll::-webkit-scrollbar-track { background: transparent; }

    .step-node { position: relative; display: flex; align-items: flex-start; gap: .75rem; }
    .step-line { position: absolute; left: 15px; top: 32px; bottom: -12px; width: 2px; background: #e2e8f0; }
    .step-line.done { background: #22c55e; }

    .step-dot {
        width: 32px; height: 32px; border-radius: 9999px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; font-weight: 700; z-index: 10; position: relative;
        border: 2px solid transparent;
    }
    .step-dot.done  { background: #dcfce7; color: #16a34a; border-color: #22c55e; }
    .step-dot.aktif { background: #fff7ed; color: #ea580c; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.15); }
    .step-dot.wait  { background: #f8fafc; color: #94a3b8; border-color: #e2e8f0; }

    .step-item-card {
        flex: 1; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff;
        padding: .65rem .85rem; margin-bottom: .85rem; transition: all .15s ease;
    }
    .step-item-card.aktif { border-color: #f97316; background: #fffaf5; }
    .step-item-card.done  { border-color: #e2e8f0; background: #fafafa; }
    .step-item-card.wait  { opacity: .7; }

    .progres-bar-track { height: 7px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
    .progres-bar-fill  { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #1a365d, #f97316); transition: width .5s ease; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .45s cubic-bezier(.16,1,.3,1) forwards; }

    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,.2); border-top-color: #1a365d; border-radius: 50%; animation: spin .7s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: .7; cursor: not-allowed !important; pointer-events: none; }

    #skripsiToast {
        position: fixed; right: 20px; top: 20px; z-index: 2000;
        display: none; align-items: center; gap: .6rem; max-width: 360px;
        border-radius: .75rem; padding: .7rem 1rem; font-size: .82rem;
        box-shadow: 0 12px 24px rgba(15,23,42,.18); color: #fff;
    }
    #skripsiToast.show { display: flex; animation: riseIn .3s ease; }
    #skripsiToast.success { background: linear-gradient(135deg,#16a34a,#15803d); }
    #skripsiToast.error   { background: linear-gradient(135deg,#dc2626,#b91c1c); }
    #skripsiToast.info    { background: linear-gradient(135deg,#1a365d,#234670); }
</style>

<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Skripsi Mahasiswa</h1>
                    <p class="text-xs text-slate-500">Lembar Kerja Aktivitas Skripsi Sesuai Prosedur SOP</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                <i class="fas fa-list-check"></i><?= $selesai ?>/<?= $total ?> SOP Selesai
            </span>
        </div>
    </section>

    <!-- ===== Panel Informasi Skripsi ===== -->
    <section class="mb-6 reveal">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col xl:flex-row gap-6">
            
            <!-- Kiri: Identitas, Judul & Jadwal Ujian -->
            <div class="flex-1 space-y-5 border-b xl:border-b-0 xl:border-r border-slate-100 pb-5 xl:pb-0 xl:pr-6">
                <div>
                    <!-- Badge Identitas Mahasiswa -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-bold shadow-sm">
                            <i class="fas fa-user-graduate text-orange-400"></i><?= htmlspecialchars($infoSkripsi['nama']) ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 text-orange-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-id-card"></i><?= htmlspecialchars($infoSkripsi['nim']) ?>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-xs font-bold">
                            <i class="fas fa-graduation-cap"></i><?= htmlspecialchars($infoSkripsi['prodi']) ?> (<?= htmlspecialchars($infoSkripsi['angkatan']) ?>)
                        </span>
                    </div>

                    <!-- Judul Skripsi -->
                    <div class="space-y-1">
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Judul Skripsi / Tugas Akhir</p>
                        <h2 class="text-lg font-extrabold text-slate-800 leading-snug">
                            <?= htmlspecialchars($infoSkripsi['judul']) ?>
                        </h2>
                    </div>
                </div>

                <!-- Jadwal Ujian Sejajar -->
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Jadwal Ujian Skripsi</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-orange-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Proposal</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_prop']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-sky-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Hasil</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_hasil']) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <i class="fas fa-calendar-check text-emerald-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">Tutup</p>
                                <p class="text-[11px] font-bold text-slate-700"><?= htmlspecialchars($infoSkripsi['ujian_tutup']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Dosen (PA, Pembimbing 1, Pembimbing 2 disusun 3 ke bawah) -->
            <div class="w-full xl:w-72 flex-shrink-0 space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Penasihat Akademik</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pa']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pa']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pembimbing 1</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pembimbing_1']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pemb_1']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 flex-shrink-0 text-[11px] font-bold">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pembimbing 2</p>
                            <p class="text-[11px] font-bold text-slate-800"><?= htmlspecialchars($infoSkripsi['pembimbing_2']) ?></p>
                            <p class="text-[10px] text-slate-400">NIP. <?= htmlspecialchars($infoSkripsi['nip_pemb_2']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== Tata Letak 2 Kolom (Besar: Lembar Kerja SOP | Kecil: Progress Line) ===== -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">

        <!-- ===== KOLOM BESAR (md:col-span-7 lg:col-span-8): Lembar Kerja Aktivitas SOP ===== -->
        <div class="md:col-span-7 lg:col-span-8 space-y-5 reveal">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <!-- Header Lembar Kerja -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold"><?= $currentStep['urutan'] ?></span>
                            <h3 class="text-base font-bold text-slate-800"><?= htmlspecialchars($currentStep['judul']) ?></h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Eksekutor SOP: <strong class="text-orange-600"><?= htmlspecialchars($currentStep['executor']) ?></strong></p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                        <i class="fas fa-pen"></i>Aktif
                    </span>
                </div>

                <!-- Konten Aktivitas Formulir / Task SOP -->
                <div class="space-y-4">
                    <div class="rounded-lg bg-orange-50/60 border border-orange-100 p-4 text-xs text-slate-700 leading-relaxed">
                        <i class="fas fa-info-circle text-orange-500 mr-1.5"></i>
                        Silakan lengkapi berkas dan persyaratan berikut untuk menyelesaikan tahapan <strong><?= htmlspecialchars($currentStep['judul']) ?></strong>.
                    </div>

                    <form id="formSopKerja" method="post" action="javascript:void(0)" class="space-y-4">
                        <!-- Upload KRS -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Upload KRS Terakhir (PDF) <span class="text-rose-500">*</span></label>
                            <input type="file" accept=".pdf" class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-slate-50 focus:bg-white focus:outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Maksimal 2 MB, format file PDF.</p>
                        </div>

                        <!-- Upload Transkrip -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Upload Transkrip Nilai (PDF) <span class="text-rose-500">*</span></label>
                            <input type="file" accept=".pdf" class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-slate-50 focus:bg-white focus:outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Maksimal 2 MB, format file PDF.</p>
                        </div>

                        <!-- Upload Draf Proposal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Upload Draf Proposal Skripsi (PDF/DOCX) <span class="text-rose-500">*</span></label>
                            <input type="file" accept=".pdf,.docx" class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2 bg-slate-50 focus:bg-white focus:outline-none">
                        </div>

                        <!-- Catatan Catatan Tambahan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan untuk Pembimbing/Admin</label>
                            <textarea rows="3" class="w-full text-xs text-slate-700 border border-slate-200 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none" placeholder="Tuliskan catatan opsional..."></textarea>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button type="submit" id="btnSubmitSop" data-text="Kirim & Tandai Selesai" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-xs font-bold text-white shadow-md shadow-orange-500/20 hover:bg-orange-600 transition">
                                <i class="fas fa-paper-plane"></i>Kirim & Tandai Selesai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ===== KOLOM KECIL (md:col-span-5 lg:col-span-4): Progress Line Stepper ===== -->
        <div class="md:col-span-5 lg:col-span-4 space-y-4 reveal" style="animation-delay:.1s">
            
            <!-- Ringkasan Progress Bar -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-600">Progress Alur SOP</span>
                    <span class="text-xs font-bold text-orange-600"><?= $progres ?>%</span>
                </div>
                <div class="progres-bar-track">
                    <div class="progres-bar-fill" style="width:<?= $progres ?>%;"></div>
                </div>
                <p class="mt-2 text-[11px] text-slate-400"><?= $selesai ?> dari <?= $total ?> langkah SOP selesai.</p>
            </div>

            <!-- List Stepper SOP -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b border-slate-100">Alur SOP Skripsi</h3>
                
                <div class="space-y-0 sop-scroll">
                    <?php foreach ($steps as $i => $s):
                        $isLast = ($i === $total - 1);
                        $badgeClass = $s['status'] === 'selesai' ? 'done' : ($s['status'] === 'aktif' ? 'aktif' : 'wait');
                        $cardClass  = $badgeClass;
                        $connClass  = $s['status'] === 'selesai' ? 'done' : '';
                    ?>
                    <div class="step-node">
                        <!-- Dot + Line -->
                        <div class="flex flex-col items-center">
                            <div class="step-dot <?= $badgeClass ?>">
                                <?php if ($s['status'] === 'selesai'): ?>
                                    <i class="fas fa-check"></i>
                                <?php elseif ($s['status'] === 'aktif'): ?>
                                    <i class="fas fa-pen"></i>
                                <?php else: ?>
                                    <?= $s['urutan'] ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!$isLast): ?>
                            <div class="step-line <?= $connClass ?>"></div>
                            <?php endif; ?>
                        </div>

                        <!-- Text Info -->
                        <div class="step-item-card <?= $cardClass ?>">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">Step <?= $s['urutan'] ?></span>
                                <span class="text-[9px] font-semibold text-slate-500"><?= htmlspecialchars($s['executor']) ?></span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 leading-snug mt-0.5"><?= htmlspecialchars($s['judul']) ?></h4>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

</main>

<div id="skripsiToast">
    <i class="fas fa-circle-check"></i>
    <span id="skripsiToastMsg" class="flex-1">Notifikasi</span>
    <button type="button" onclick="document.getElementById('skripsiToast').classList.remove('show')" class="text-white/70 hover:text-white ml-1"><i class="fas fa-xmark text-xs"></i></button>
</div>

<script>
function toast(msg, type) {
    var t = document.getElementById('skripsiToast');
    document.getElementById('skripsiToastMsg').textContent = msg;
    t.className = 'show ' + (type || 'info');
    clearTimeout(t._tid);
    t._tid = setTimeout(function(){ t.classList.remove('show'); }, 3500);
}

function setButtonLoading(btn, isLoading) {
    if (isLoading) {
        btn.classList.add('is-loading');
        btn.setAttribute('disabled', '');
        btn.innerHTML = '<span class="spinner-slate"></span> Memproses...';
    } else {
        btn.classList.remove('is-loading');
        btn.removeAttribute('disabled');
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>' + (btn.getAttribute('data-text') || 'Kirim & Tandai Selesai');
    }
}

document.getElementById('formSopKerja').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSubmitSop');
    setButtonLoading(btn, true);
    toast('Data formulir SOP berhasil disimulasikan.', 'success');
    setTimeout(function(){ setButtonLoading(btn, false); }, 1500);
});
</script>
