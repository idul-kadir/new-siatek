<?php
/**
 * Halaman: Surat (Jurusan)
 * Agenda surat masuk & surat keluar tingkat jurusan, dilengkapi file lampiran.
 * Tampilan statis (data dummy dalam array PHP) — JS untuk tab masuk/keluar,
 * cari, filter jenis & tahun, pagination, dan modal detail.
 */
$jb = array(
    'TND'       => array('bg-sky-50 text-sky-700', 'TND'),
    'SK'        => array('bg-violet-50 text-violet-700', 'SK'),
    'Undangan'  => array('bg-amber-50 text-amber-700', 'Undangan'),
    'Nota Dinas'=> array('bg-emerald-50 text-emerald-700', 'Nota Dinas'),
    'Dll'       => array('bg-slate-100 text-slate-600', 'Lainnya'),
);
$rows = array(
    // ===== SURAT MASUK =====
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'Undangan','agenda'=>'2026/037/MIS','tanggal'=>'18 Agu 2026','pihak'=>'Universitas Indonesia','perihal'=>'Undangan Seminar Nasional &amp; Call for Paper','nomor'=>'U-IN/SEM/2026/014','file'=>'Lampiran_Undangan_Seminar_UI.pdf','size'=>'312 KB','ket'=>'Diteruskan ke bagian kemahasiswaan untuk tindak lanjut.','cari'=>'2026/037/mis universitas indonesia undangan seminar nasional call for paper u in sem 2026 014 undangan'),
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'Nota Dinas','agenda'=>'2026/036/MIS','tanggal'=>'15 Agu 2026','pihak'=>'BKN Pusat','perihal'=>'Penyampaian Hasil Verifikasi SKP (Integrasi)','nomor'=>'BKN/0512/VI/2026','file'=>'Lampiran_Verifikasi_SKP_BKN.pdf','size'=>'248 KB','ket'=>'Menjadi lampiran proses penilaian SKP dosen.','cari'=>'2026/036/mis bkn pusat penyampaian hasil verifikasi skp integrasi bkn 0512 vi 2026 nota dinas'),
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'SK','agenda'=>'2026/035/MIS','tanggal'=>'12 Agu 2026','pihak'=>'LLDIKTI Wilayah XVI','perihal'=>'SK Beban Kerja Dosen Semester Ganjil 2026/2027','nomor'=>'LLD16/2026/088','file'=>'SK_BKD_LLDIKTI_P16.pdf','size'=>'501 KB','ket'=>'Didaftarkan sebagai dasar penyusunan jadwal mengajar.','cari'=>'2026/035/mis lldikti wilayah xvi sk beban kerja dosen semester ganjil 2026 2027 lld16 2026 088 sk'),
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'Dll','agenda'=>'2026/034/MIS','tanggal'=>'8 Agu 2026','pihak'=>'Kemendikbudristek','perihal'=>'Tindak Lanjut Hasil Akreditasi Program Studi','nomor'=>'KMB/0241/2026','file'=>'Tindaklanjut_Akreditasi_Kemendikbud.pdf','size'=>'690 KB','ket'=>'','cari'=>'2026/034/mis kemendikbudristek tindak lanjut hasil akreditasi program studi kmb 0241 2026 dll'),
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'Undangan','agenda'=>'2026/033/MIS','tanggal'=>'5 Agu 2026','pihak'=>'Politeknik Negeri Gorontalo','perihal'=>'Undangan Kegiatan PKKM dan Koordinasi','nomor'=>'PNG/PKKM/2026/072','file'=>'Undangan_PKKM_Poltek_Gorontalo.pdf','size'=>'287 KB','ket'=>'Koordinasi persiapan kegiatan PKKM lintas program studi.','cari'=>'2026/033/mis politeknik negeri gorontalo undangan kegiatan pkkm dan koordinasi png pkkm 2026 072 undangan'),
    array('jenis'=>'masuk','tahun'=>'2026','jenisur'=>'TND','agenda'=>'2026/032/MIS','tanggal'=>'1 Agu 2026','pihak'=>'LLDIKTI Wilayah XVI','perihal'=>'TND Kenaikan Pangkat Dosen','nomor'=>'LLD16/2026/061','file'=>'TND_Kenaikan_Pangkat.pdf','size'=>'180 KB','ket'=>'Diteruskan ke dosen terkait.','cari'=>'2026/032/mis lldikti wilayah xvi tnd kenaikan pangkat dosen lld16 2026 061 tnd'),
    array('jenis'=>'masuk','tahun'=>'2025','jenisur'=>'Dll','agenda'=>'2025/190/MIS','tanggal'=>'28 Jul 2026','pihak'=>'Universitas PGRI Gorontalo','perihal'=>'Penawaran Kerjasama Penelitian dan Pengabdian','nomor'=>'UPG/2025/011','file'=>'Kerjasama_UPG_Riset_PKMS.pdf','size'=>'214 KB','ket'=>'Diteruskan ke gugus penelitian.','cari'=>'2025/190/mis universitas pgri gorontalo penawaran kerjasama penelitian dan pengabdian upg 2025 011 dll'),
    // ===== SURAT KELUAR =====
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'Undangan','agenda'=>'2026/098/KEL','tanggal'=>'17 Agu 2026','pihak'=>'Panitia PKKM','perihal'=>'Undangan Rapat Persiapan Kegiatan PKKM','nomor'=>'B/TE/2026/098','file'=>'Keluar_Undangan_Rapat_PKKM.pdf','size'=>'155 KB','ket'=>'','cari'=>'2026/098/kel panitia pkkm undangan rapat persiapan kegiatan pkkm b te 2026 098 undangan'),
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'SK','agenda'=>'2026/097/KEL','tanggal'=>'14 Agu 2026','pihak'=>'Akademik &amp; Kemahasiswaan','perihal'=>'SK Penguji Skripsi Tahap Akhir TA Ganjil 2026/2027','nomor'=>'B/TE/2026/097','file'=>'SK_Penguji_Skripsi_TAGanjil.pdf','size'=>'402 KB','ket'=>'Melampirkan daftar penguji 42 mahasiswa.','cari'=>'2026/097/kel akademik kemahasiswaan sk penguji skripsi tahap akhir ta ganjil 2026 2027 b te 2026 097 sk'),
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'TND','agenda'=>'2026/096/KEL','tanggal'=>'10 Agu 2026','pihak'=>'Akademik','perihal'=>'TND Pembimbing MBKM Angkatan 2026','nomor'=>'B/TE/2026/096','file'=>'TND_Pembimbing_MBKM_2026.pdf','size'=>'188 KB','ket'=>'','cari'=>'2026/096/kel akademik tnd pembimbing mbkm angkatan 2026 b te 2026 096 tnd'),
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'Dll','agenda'=>'2026/095/KEL','tanggal'=>'6 Agu 2026','pihak'=>'Baznas Provinsi Gorontalo','perihal'=>'Permohonan Peminjaman Tenda Kegiatan Mahasiswa','nomor'=>'B/TE/2026/095','file'=>'Permohonan_Tenda_Baznas.pdf','size'=>'142 KB','ket'=>'','cari'=>'2026/095/kel baznas provinsi gorontalo permohonan peminjaman tenda kegiatan mahasiswa b te 2026 095 dll'),
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'Nota Dinas','agenda'=>'2026/094/KEL','tanggal'=>'3 Agu 2026','pihak'=>'Seluruh Dosen Teknik Elektro','perihal'=>'Nota Dinas Pengumpulan RPS dan Kontrak Perkuliahan','nomor'=>'B/TE/2026/094','file'=>'NotaDinas_RPS_KontrakKuliah.pdf','size'=>'166 KB','ket'=>'Batas pengumpulan 7 hari sebelum perkuliahan dimulai.','cari'=>'2026/094/kel seluruh dosen teknik elektro nota dinas pengumpulan rps dan kontrak perkuliahan b te 2026 094 nota dinas'),
    array('jenis'=>'keluar','tahun'=>'2026','jenisur'=>'Undangan','agenda'=>'2026/093/KEL','tanggal'=>'30 Jul 2026','pihak'=>'BSNP','perihal'=>'Undangan Asesor Akreditasi Program Studi','nomor'=>'B/TE/2026/093','file'=>'Keluar_Undangan_Asesor.pdf','size'=>'204 KB','ket'=>'Asesor dijadwalkan 3-5 September 2026.','cari'=>'2026/093/kel bsnp undangan asesor akreditasi program studi b te 2026 093 undangan'),
);
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #c4b5fd; color: #6d28d9; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .jb-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .file-chip { display: inline-flex; align-items: center; gap: .5rem; min-width: 0; max-width: 230px; padding: .32rem .55rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #f8fafc; }
    .file-chip .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .45rem; font-size: .72rem; flex-shrink: 0; }
    .f-pdf   { background: #fee2e2; color: #dc2626; }
    .f-word  { background: #dbeafe; color: #2563eb; }
    .file-chip .f-meta { min-width: 0; }
    .file-chip .f-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: .72rem; color: #334155; }
    .file-chip .f-sub { display: block; font-size: .63rem; color: #94a3b8; }
    .f-down { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .4rem; background: #fff; border: 1px solid #e2e8f0; color: #64748b; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
    .f-down:hover { background: #6d28d9; border-color: #6d28d9; color: #fff; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #c4b5fd; color: #6d28d9; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    /* ===== Lembar surat resmi (preview on-screen) ===== */
    .letter-sheet { font-family: Georgia, 'Times New Roman', serif; color: #1e293b; }
    .letter-sheet .sheet { background: #fff; }
    .letter-sheet .kop { text-align: center; border-bottom: 3px double #334155; padding-bottom: 10px; }
    .letter-sheet .kop .l1 { font-size: 9px; letter-spacing: .3px; color: #334155; line-height: 1.5; }
    .letter-sheet .kop h2 { font-size: 19px; font-weight: 800; letter-spacing: .5px; margin: 4px 0 2px; }
    .letter-sheet .kop .fak { font-size: 11px; font-weight: 700; }
    .letter-sheet .kop .al { font-size: 10px; line-height: 1.5; margin-top: 3px; }
    .letter-sheet .tgl { text-align: right; font-size: 11px; margin-top: 14px; }
    .letter-sheet .meta { text-align: right; font-size: 11px; line-height: 1.7; margin-top: 6px; }
    .letter-sheet .meta b { font-weight: 600; }
    .letter-sheet .sal { margin-top: 12px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .sal .kpd { font-weight: 600; }
    .letter-sheet .pembuka { margin-top: 10px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .isi { margin-top: 4px; }
    .letter-sheet .isi p { font-size: 11px; line-height: 1.65; text-align: justify; text-indent: 2em; margin-top: 3px; }
    .letter-sheet .tutup { font-size: 11px; line-height: 1.65; text-align: justify; margin-top: 8px; }
    .letter-sheet .ttd { margin-top: 18px; font-size: 11px; line-height: 1.6; }
    .letter-sheet .ttd .skr { margin-bottom: 62px; }
    .letter-sheet .ttd .nm { font-weight: 700; text-decoration: underline; }
    .letter-sheet .tb { margin-top: 16px; font-size: 10px; line-height: 1.65; }
    .letter-sheet .fld-label { font-size: 10.5px; }
    .letter-sheet .placeholder { color: #94a3b8; font-style: italic; text-indent: 0 !important; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-lg text-violet-600">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Surat</h1>
                    <p class="text-xs text-slate-500">Agenda surat masuk dan surat keluar tingkat jurusan, beserta lampiran file.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnBuatSurat" class="inline-flex items-center gap-1.5 rounded-lg bg-violet-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-violet-500/25 transition hover:bg-violet-600">
                    <i class="fas fa-plus"></i>Buat Surat
                </button>
            </div>
        </div>
    </section>

    <!-- ===== Tab Jenis ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="masuk" aria-selected="true"><span class="tdot bg-sky-500"></span>Surat Masuk<span class="tnum" id="cnt-masuk">7</span></button>
            <button type="button" class="tab-btn" data-tab="keluar" aria-selected="false"><span class="tdot bg-rose-500"></span>Surat Keluar<span class="tnum" id="cnt-keluar">6</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari perihal, nomor agenda, nomor surat, pihak…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-violet-400 focus:bg-white">
            </div>
            <select id="fJenis" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400">
                <option value="">Semua Jenis</option>
                <option value="TND">TND</option>
                <option value="SK">SK</option>
                <option value="Undangan">Undangan</option>
                <option value="Nota Dinas">Nota Dinas</option>
                <option value="Dll">Lainnya</option>
            </select>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400">
                <option value="">Semua Tahun</option>
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> dari <b class="text-slate-800">0</b> surat</p>
        <p class="text-xs text-slate-400"><i class="fas fa-paperclip mr-1 text-violet-400"></i>Klik ikon <i class="fas fa-download mx-1 text-violet-500"></i>untuk mengunduh lampiran surat.</p>
    </section>

    <!-- ===== Tabel Surat ===== -->
    <section>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th>No. Agenda</th>
                            <th>Tanggal</th>
                            <th>Pengirim / Tujuan</th>
                            <th>Perihal</th>
                            <th>Nomor Surat</th>
                            <th>Jenis</th>
                            <th>File</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbSurat">
<?php foreach ($rows as $i => $r): ?>
                        <tr class="reveal" data-jenis="<?= $r['jenis'] ?>" data-tanggal="<?= $r['tanggal'] ?>" data-tahun="<?= $r['tahun'] ?>" data-pihak="<?= $r['pihak'] ?>" data-perihal="<?= $r['perihal'] ?>" data-nomor="<?= $r['nomor'] ?>" data-jenisur="<?= $r['jenisur'] ?>" data-file="<?= $r['file'] ?>" data-ket="<?= $r['ket'] ?>" data-cari="<?= $r['cari'] ?>">
                            <td><span class="font-semibold text-slate-700"><?= $r['agenda'] ?></span></td>
                            <td class="whitespace-nowrap"><?= $r['tanggal'] ?></td>
                            <td><span class="font-medium text-slate-700"><?= $r['pihak'] ?></span></td>
                            <td class="max-w-[240px]"><span class="line-clamp-2 font-medium text-slate-700"><?= $r['perihal'] ?></span></td>
                            <td class="whitespace-nowrap text-slate-500"><?= $r['nomor'] ?></td>
                            <td><span class="jb-badge <?= $jb[$r['jenisur']][0] ?>"><?= $jb[$r['jenisur']][1] ?></span></td>
                            <td><span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span><span class="f-meta"><span class="f-name"><?= $r['file'] ?></span><span class="f-sub"><?= $r['size'] ?></span></span><button type="button" class="f-down" title="Unduh"><i class="fas fa-download"></i></button></span></td>
                            <td><div class="inline-flex items-center justify-end gap-1.5">
                                <button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat Detail</span></button>
                            </div></td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
                <p class="text-[11px] text-slate-400" id="pgInfo">Halaman 1 dari 1</p>
                <div class="inline-flex items-center gap-1.5" id="pgWrap"></div>
            </div>
        </div>
    </section>

</main>

<!-- ===== Modal Detail Surat ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-envelope-open-text mr-1 text-violet-500"></i>Detail Surat</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-detail-close>&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3.5">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">No. Agenda</p>
                    <p class="text-sm font-bold text-slate-800" id="dtAgenda">—</p>
                </div>
                <span class="jb-badge" id="dtTipe">—</span>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tanggal</p><p class="text-sm font-medium text-slate-700" id="dtTanggal">—</p></div>
                <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nomor Surat</p><p class="text-sm font-medium text-slate-700" id="dtNomor">—</p></div>
            </div>
            <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pengirim / Tujuan</p><p class="text-sm font-medium text-slate-700" id="dtPihak">—</p></div>
            <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Perihal</p><p class="text-sm font-semibold text-slate-800" id="dtPerihal">—</p></div>
            <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Jenis Surat</p><p class="text-sm font-medium text-slate-700" id="dtJenis">—</p></div>
            <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Lampiran</p><div id="dtFile"></div></div>
            <div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Catatan</p><p class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600" id="dtKet">—</p></div>
        </div>
    </div>
</div>

<!-- ===== Modal Buat Surat ===== -->
<div class="modal-overlay" id="buatModal" role="dialog" aria-modal="true">
    <div class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-100 text-sm text-violet-600"><i class="fas fa-file-signature"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Buat Surat</h6>
                    <p class="text-[11px] text-slate-400">Susun surat — pratinjau langsung &amp; cetak oleh <b>sistem</b> format A4.</p>
                </div>
            </div>
            <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-buat-close>&times;</button>
        </div>

        <div class="grid flex-1 gap-0 overflow-y-auto md:grid-cols-2">
            <!-- ===== FORM ===== -->
            <div id="paneLForm" class="space-y-3.5 border-b border-slate-200 p-5 md:border-b-0 md:border-r">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="lfArah" class="mb-1 block text-xs font-semibold text-slate-600">Arah Surat</label>
                        <select id="lfArah" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                            <option value="keluar" selected>Surat Keluar</option>
                            <option value="masuk">Surat Masuk</option>
                        </select>
                    </div>
                    <div>
                        <label for="lfJenis" class="mb-1 block text-xs font-semibold text-slate-600">Jenis Surat</label>
                        <select id="lfJenis" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                            <option value="TND">TND</option>
                            <option value="SK">SK</option>
                            <option value="Undangan" selected>Undangan</option>
                            <option value="Nota Dinas">Nota Dinas</option>
                            <option value="Dll">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="lfTanggal" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal Surat</label>
                        <input type="date" id="lfTanggal" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                    <div>
                        <label for="lfAgenda" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Agenda</label>
                        <input type="text" id="lfAgenda" placeholder="2026/099/KEL" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="lfNomor" class="mb-1 block text-xs font-semibold text-slate-600">Nomor Surat</label>
                        <input type="text" id="lfNomor" placeholder="B/TE/2026/099" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                    <div>
                        <label for="lfLampiran" class="mb-1 block text-xs font-semibold text-slate-600">Lampiran</label>
                        <input type="text" id="lfLampiran" placeholder="1 (satu) berkas" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                </div>
                <div>
                    <label for="lfPihak" class="mb-1 block text-xs font-semibold text-slate-600" id="lblPihak">Tujuan Surat</label>
                    <input type="text" id="lfPihak" placeholder="Panitia PKKM — Universitas Negeri Gorontalo" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                </div>
                <div>
                    <label for="lfHal" class="mb-1 block text-xs font-semibold text-slate-600">Hal / Perihal <span class="text-rose-500">*</span></label>
                    <input type="text" id="lfHal" placeholder="Undangan Rapat Persiapan PKKM" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                </div>
                <div id="grpKepada">
                    <div>
                        <label for="lfKepada" class="mb-1 block text-xs font-semibold text-slate-600">Kepada Yth.</label>
                        <input type="text" id="lfKepada" placeholder="Ketua Pelaksana PKKM" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label for="lfNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Penandatangan</label>
                            <input type="text" id="lfNama" value="Ir. Nourman Amoho, S.T., M.T." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                        </div>
                        <div>
                            <label for="lfNip" class="mb-1 block text-xs font-semibold text-slate-600">NIP</label>
                            <input type="text" id="lfNip" value="19700915 199512 1 001" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="lfJabatan" class="mb-1 block text-xs font-semibold text-slate-600">Jabatan</label>
                        <input type="text" id="lfJabatan" value="Ketua Jurusan Teknik Elektro" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white">
                    </div>
                </div>
                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <label for="lfIsi" class="block text-xs font-semibold text-slate-600">Isi Surat <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-slate-400">Paragraf baru: gunakan <b>||</b></span>
                    </div>
                    <textarea id="lfIsi" rows="7" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm leading-relaxed outline-none focus:border-violet-400 focus:bg-white">Menindaklanjuti surat dari Pimpinan Universitas Nomor ... tanggal ..., bersama ini kami sampaikan bahwa:||1. kegiatan PKKM akan dilaksanakan pada tanggal 24 Agustus 2026;||2. undangan selengkapnya tercantum pada lampiran.</textarea>
                </div>
                <div id="grpTembusan">
                    <label for="lfTembusan" class="mb-1 block text-xs font-semibold text-slate-600">Tembusan <span class="font-normal text-slate-400">(opsional, satu per baris)</span></label>
                    <textarea id="lfTembusan" rows="2" placeholder="1. Arsip&#10;2. Ketua Program Studi" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-violet-400 focus:bg-white"></textarea>
                </div>
            </div>

            <!-- ===== PREVIEW SURAT ===== -->
            <div class="overflow-y-auto bg-slate-100 p-5">
                <div class="mb-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                    <i class="fas fa-eye text-violet-500"></i> Pratinjau surat resmi
                    <span class="ml-auto hidden items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 md:inline-flex">
                        <i class="fas fa-circle text-[6px]"></i> Update otomatis
                    </span>
                </div>
                <div class="letter-sheet rounded-lg bg-white p-6 shadow-sm md:p-8" id="pvSheet"></div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <p class="text-[11px] text-slate-400"><i class="fas fa-print mr-1 text-violet-400"></i>Surat dicetak formal A4 oleh sistem print.</p>
            <div class="flex items-center gap-2">
                <button type="button" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-300" data-buat-close>Batal</button>
                <button type="button" id="btnCetak" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700"><i class="fas fa-print mr-1"></i>Cetak</button>
                <button type="button" id="btnSimpanSurat" class="rounded-lg bg-violet-500 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-violet-500/25 transition hover:bg-violet-600"><i class="fas fa-check mr-1"></i>Simpan ke Agenda</button>
            </div>
        </div>
    </div>
</div>

<!-- iframe cetak tersembunyi (print sistem) -->
<div hidden><iframe id="printFrame" title="Pratinjau Cetak Surat"></iframe></div>

<!-- Toast -->
<div id="suratToast" class="pointer-events-none fixed bottom-6 left-1/2 z-[2000] -translate-x-1/2 translate-y-16 rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white opacity-0 shadow-lg transition-all duration-300"></div>

<script>
(function () {
    var tb = document.getElementById('tbSurat');
    var surat = Array.prototype.slice.call(document.querySelectorAll('#tbSurat tr'));
    var fCari = document.getElementById('fCari');
    var fJenis = document.getElementById('fJenis');
    var fTahun = document.getElementById('fTahun');
    var activeTab = 'masuk';
    var page = 1, PER = 6;

    function miniChip(nama, label) {
        return '<span class="file-chip"><span class="f-ico f-pdf"><i class="fas fa-file-pdf"></i></span>' +
            '<span class="f-meta"><span class="f-name">' + nama + '</span><span class="f-sub">' + label + '</span></span>' +
            '<button type="button" class="f-down" title="Unduh ' + nama + '"><i class="fas fa-download"></i></button></span>';
    }
    function jbBadge(j) {
        var map = {
            'TND': 'bg-sky-50 text-sky-700',
            'SK': 'bg-violet-50 text-violet-700',
            'Undangan': 'bg-amber-50 text-amber-700',
            'Nota Dinas': 'bg-emerald-50 text-emerald-700',
            'Dll': 'bg-slate-100 text-slate-600'
        };
        return '<span class="jb-badge ' + (map[j] || map.Dll) + '">' + (j === 'Dll' ? 'Lainnya' : j) + '</span>';
    }

    function recount() {
        var m = 0, k = 0;
        surat.forEach(function (tr) {
            if (tr.getAttribute('data-jenis') === 'keluar') k++; else m++;
        });
        document.getElementById('cnt-masuk').textContent = m;
        document.getElementById('cnt-keluar').textContent = k;
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var jn = fJenis.value;
        var th = fTahun.value;
        return surat.filter(function (tr) {
            if (tr.getAttribute('data-jenis') !== activeTab) return false;
            if (jn && tr.getAttribute('data-jenisur') !== jn) return false;
            if (th && tr.getAttribute('data-tahun') !== th) return false;
            return !kata || tr.getAttribute('data-cari').indexOf(kata) !== -1;
        });
    }
    function render() {
        var v = visible();
        var pages = Math.max(1, Math.ceil(v.length / PER));
        if (page > pages) page = pages;
        var start = (page - 1) * PER;
        var slice = v.slice(start, start + PER);
        surat.forEach(function (tr) { tr.style.display = 'none'; });
        slice.forEach(function (tr) { tr.style.display = ''; });
        document.getElementById('jmlInfo').innerHTML = 'Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + v.length + '</b> surat';
        document.getElementById('pgInfo').textContent = 'Halaman ' + page + ' dari ' + pages;
        var wrap = document.getElementById('pgWrap');
        wrap.innerHTML = '';
        var prev = document.createElement('button');
        prev.className = 'pg-btn';
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.disabled = page <= 1;
        prev.addEventListener('click', function () { if (page > 1) { page--; render(); } });
        wrap.appendChild(prev);
        for (var i = 1; i <= pages; i++) {
            (function (n) {
                var b = document.createElement('button');
                b.className = 'pg-btn' + (n === page ? ' on' : '');
                b.textContent = n;
                b.addEventListener('click', function () { page = n; render(); });
                wrap.appendChild(b);
            })(i);
        }
        var next = document.createElement('button');
        next.className = 'pg-btn';
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.disabled = page >= pages;
        next.addEventListener('click', function () { if (page < pages) { page++; render(); } });
        wrap.appendChild(next);
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
            b.setAttribute('aria-selected', 'true');
            activeTab = b.getAttribute('data-tab');
            page = 1;
            render();
        });
    });
    fCari.addEventListener('input', function () { page = 1; render(); });
    fJenis.addEventListener('change', function () { page = 1; render(); });
    fTahun.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = '';
        fJenis.value = '';
        fTahun.value = '';
        page = 1;
        render();
    });

    /* ===== Modal helper ===== */
    var dm = document.getElementById('detailModal');
    dm.querySelectorAll('[data-detail-close]').forEach(function (b) {
        b.addEventListener('click', function () { dm.classList.remove('show'); });
    });
    dm.addEventListener('click', function (e) { if (e.target === dm) dm.classList.remove('show'); });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-detail');
        if (!btn) return;
        var src = btn.closest('tr');
        if (!src) return;
        document.getElementById('dtAgenda').textContent = src.querySelector('td').textContent.trim();
        document.getElementById('dtTipe').innerHTML = src.getAttribute('data-jenis') === 'keluar'
            ? '<span class="jb-badge bg-rose-50 text-rose-700"><i class="fas fa-arrow-right mr-1"></i>Keluar</span>'
            : '<span class="jb-badge bg-sky-50 text-sky-700"><i class="fas fa-arrow-down-left mr-1"></i>Masuk</span>';
        document.getElementById('dtTanggal').textContent = src.getAttribute('data-tanggal');
        document.getElementById('dtNomor').textContent = src.getAttribute('data-nomor');
        document.getElementById('dtPihak').textContent = src.getAttribute('data-pihak');
        document.getElementById('dtPerihal').textContent = src.getAttribute('data-perihal');
        document.getElementById('dtJenis').innerHTML = jbBadge(src.getAttribute('data-jenisur'));
        document.getElementById('dtFile').innerHTML = miniChip(src.getAttribute('data-file'), 'Lampiran PDF');
        document.getElementById('dtKet').textContent = src.getAttribute('data-ket') || '—';
        dm.classList.add('show');
    });

    /* ===== Buat Surat (komposer + cetak sistem) ===== */
    function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function cval(f) { var el = document.getElementById(f); return el ? el.value.trim() : ''; }
    var BULAN_P = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    function pad2(n) { return (n < 10 ? '0' : '') + n; }
    function isoToday() { var d = new Date(); return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }
    function fmtTglPenuh(v) {
        var p = (v || '').split('-');
        if (p.length !== 3) return '-';
        return (+p[2]) + ' ' + BULAN_P[+p[1] - 1] + ' ' + p[0];
    }
    function fmtTglShort(v) {
        var p = (v || '').split('-');
        if (p.length !== 3) return '-';
        return (+p[2]) + ' ' + BULAN_S[+p[1] - 1] + ' ' + p[0];
    }
    function paraToHtml(src) {
        var parts = (src || '').split('||');
        var out = '';
        for (var i = 0; i < parts.length; i++) {
            var p = parts[i].replace(/\s*\n\s*/g, ' ').replace(/\s{2,}/g, ' ').trim();
            if (p) out += '<p>' + esc(p) + '</p>';
        }
        return out;
    }
    function nextAgenda(jenis) {
        var suffix = jenis === 'keluar' ? 'KEL' : 'MIS';
        var year = new Date().getFullYear();
        var re = new RegExp('^' + year + '/(\\d+)/' + suffix + '$');
        var max = 0;
        surat.forEach(function (tr) {
            var td = tr.querySelector('td');
            if (!td) return;
            var m = (td.textContent.trim() + '').match(re);
            if (m) max = Math.max(max, parseInt(m[1], 10));
        });
        var s = String(max + 1);
        while (s.length < 3) s = '0' + s;
        return year + '/' + s + '/' + suffix;
    }

    function buildBody() {
        var arah = cval('lfArah') === 'masuk' ? 'masuk' : 'keluar';
        var agenda = cval('lfAgenda') || nextAgenda('keluar');
        var nomor = cval('lfNomor');
        var lamp = cval('lfLampiran') || '—';
        var hal = cval('lfHal') || '—';
        var pihak = esc(cval('lfPihak'));
        var kepada = esc(cval('lfKepada'));
        var tgl = fmtTglPenuh(cval('lfTanggal'));
        var isi = paraToHtml(cval('lfIsi'));

        var h = '<div class="sheet">';
        h += '<div class="kop">'
            + '<p class="l1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI<br>UNIVERSITAS NEGERI GORONTALO — FAKULTAS TEKNIK</p>'
            + '<h2>JURUSAN TEKNIK ELEKTRO</h2>'
            + '<p class="fak">Jl. Prof. Dr. Ing. B.J. Habibie, Moutong, Tilongkabila, Kab. Bone Bolango 96554</p>'
            + '<p class="al">Telp. (0435) 821125 &bull; e-mail: jtek@ung.ac.id &bull; laman: elektro.ung.ac.id</p>'
            + '</div>';
        h += '<p class="tgl">Gorontalo, ' + tgl + '</p>';
        h += '<div class="meta">'
            + '<p>Nomor &nbsp;: ' + esc(agenda) + '</p>'
            + (nomor && arah === 'keluar' ? '<p>Nomor Surat &nbsp;: ' + esc(nomor) + '</p>' : '')
            + '<p>Lampiran &nbsp;: ' + esc(lamp) + '</p>'
            + '<p>Hal &nbsp;: <b>' + esc(hal) + '</b></p>'
            + '</div>';
        if (arah === 'keluar') {
            h += '<div class="sal"><p class="kpd">Kepada Yth.</p>'
                + (kepada ? '<p><b>' + kepada + '</b></p>' : '<p class="placeholder">Bapak/Ibu …</p>')
                + '<p>di</p><p>Tempat</p></div>';
        } else {
            h += '<div class="sal"><p class="kpd">Dari &nbsp;: ' + pihak + '</p></div>';
        }
        h += '<p class="pembuka">Dengan hormat,</p>';
        h += '<div class="isi">' + (isi || '<p class="placeholder">Tulis isi surat di kolom kiri…</p>') + '</div>';
        h += '<p class="tutup">Demikian surat ini disampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>';
        if (arah === 'keluar') {
            h += '<div class="ttd">'
                + '<p class="skr">' + esc(cval('lfJabatan') || 'Ketua Jurusan Teknik Elektro') + '</p>'
                + '<p class="nm">' + esc(cval('lfNama') || 'Ir. Nourman Amoho, S.T., M.T.') + '</p>'
                + '<p>NIP. ' + esc(cval('lfNip') || '19700915 199512 1 001') + '</p>'
                + '</div>';
        }
        var tb = (cval('lfTembusan') || '').split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
        if (arah === 'keluar' && tb.length) {
            h += '<div class="tb"><p><b>Tembusan Yth.:</b></p>'
                + tb.map(function (s, i) { return '<p>' + (i + 1) + '. ' + esc(s) + '</p>'; }).join('')
                + '</div>';
        }
        h += '</div>';
        return h;
    }
    function renderPreview() {
        document.getElementById('pvSheet').innerHTML = buildBody();
    }

    var PRINT_CSS = [
        '@page { size: A4; margin: 0; }',
        '* { box-sizing: border-box; margin: 0; padding: 0; }',
        'body { font-family: Georgia, "Times New Roman", serif; color: #000; font-size: 12pt; line-height: 1.5; }',
        '.sheet { max-width: 210mm; margin: 0 auto; padding: 18mm 20mm; }',
        '.kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 7mm; }',
        '.kop .l1 { font-size: 9pt; letter-spacing: .3px; line-height: 1.4; }',
        '.kop h2 { font-size: 17pt; font-weight: 800; letter-spacing: .5px; margin: 2mm 0 1mm; }',
        '.kop .fak { font-size: 11pt; font-weight: 700; }',
        '.kop .al { font-size: 9pt; margin-top: 1.5mm; }',
        '.tgl { text-align: right; font-size: 11.5pt; margin-top: 7mm; }',
        '.meta { text-align: right; font-size: 11.5pt; line-height: 1.7; margin-top: 2mm; }',
        '.meta b { font-weight: 600; }',
        '.sal { margin-top: 5mm; font-size: 12pt; line-height: 1.6; }',
        '.sal .kpd { font-weight: 600; }',
        '.sal .placeholder { color: #888; font-style: italic; }',
        '.pembuka { margin-top: 4mm; font-size: 12pt; line-height: 1.6; }',
        '.isi p { font-size: 12pt; line-height: 1.7; text-align: justify; text-indent: 2em; margin-top: 2pt; }',
        '.isi .placeholder { color: #888; font-style: italic; text-indent: 0 !important; }',
        '.tutup { font-size: 12pt; line-height: 1.7; text-align: justify; margin-top: 4mm; }',
        '.ttd { margin-top: 10mm; font-size: 12pt; line-height: 1.7; }',
        '.ttd .skr { margin-bottom: 24mm; }',
        '.ttd .nm { font-weight: 700; text-decoration: underline; }',
        '.tb { margin-top: 10mm; font-size: 10.5pt; line-height: 1.6; }'
    ].join('\n');
    function buildPrintDoc() {
        return '<!doctype html><html lang="id"><head><meta charset="utf-8">'
            + '<title>Cetak Surat — ' + esc(cval('lfAgenda') || '') + '</title>'
            + '<style>' + PRINT_CSS + '</style></head><body>'
            + buildBody()
            + '</body></html>';
    }
    function printSurat() {
        renderPreview();
        var ifr = document.getElementById('printFrame');
        ifr.srcdoc = buildPrintDoc();
        ifr.onload = function () {
            setTimeout(function () {
                try { ifr.contentWindow.focus(); ifr.contentWindow.print(); } catch (e) { /* ignore */ }
            }, 250);
        };
    }

    function syncArah() {
        var arah = cval('lfArah') || 'keluar';
        var masuk = arah === 'masuk';
        document.getElementById('grpKepada').classList.toggle('hidden', masuk);
        document.getElementById('grpTembusan').classList.toggle('hidden', masuk);
        document.getElementById('lblPihak').textContent = masuk ? 'Pengirim Surat' : 'Tujuan Surat';
        var ag = document.getElementById('lfAgenda');
        var no = document.getElementById('lfNomor');
        ag.value = nextAgenda(arah);
        var y = new Date().getFullYear();
        var m = (ag.value + '').match(/^\d+\/(\d+)\/(?:KEL|MIS)$/);
        no.value = arah === 'keluar' ? 'B/TE/' + y + '/' + (m ? m[1] : '001') : '';
        renderPreview();
    }
    function resetBuatForm() {
        document.getElementById('lfTanggal').value = isoToday();
        document.getElementById('lfHal').value = '';
        document.getElementById('lfPihak').value = '';
        document.getElementById('lfKepada').value = '';
        document.getElementById('lfLampiran').value = '1 (satu) berkas';
        document.getElementById('lfTembusan').value = '';
        document.getElementById('lfIsi').value = 'Menindaklanjuti surat dari Pimpinan Universitas Nomor ... tanggal ..., bersama ini kami sampaikan bahwa:||1. kegiatan PKKM akan dilaksanakan pada tanggal 24 Agustus 2026;||2. undangan selengkapnya tercantum pada lampiran.';
        document.getElementById('lfNama').value = 'Ir. Nourman Amoho, S.T., M.T.';
        document.getElementById('lfNip').value = '19700915 199512 1 001';
        document.getElementById('lfJabatan').value = 'Ketua Jurusan Teknik Elektro';
        syncArah();
    }

    var toast = document.getElementById('suratToast');
    function showToast(msg, isErr) {
        toast.textContent = msg;
        toast.classList.toggle('bg-rose-600', !!isErr);
        toast.classList.toggle('bg-emerald-600', !isErr);
        toast.classList.remove('translate-y-16', 'opacity-0');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(function () {
            toast.classList.add('translate-y-16', 'opacity-0');
        }, 1800);
    }

    var bm = document.getElementById('buatModal');
    document.getElementById('btnBuatSurat').addEventListener('click', function () {
        resetBuatForm();
        bm.classList.add('show');
    });
    bm.querySelectorAll('[data-buat-close]').forEach(function (b) {
        b.addEventListener('click', function () { bm.classList.remove('show'); });
    });
    bm.addEventListener('click', function (e) { if (e.target === bm) bm.classList.remove('show'); });

    var FIELDS = ['lfArah','lfJenis','lfTanggal','lfAgenda','lfNomor','lfPihak','lfHal','lfLampiran','lfKepada','lfIsi','lfNama','lfNip','lfJabatan','lfTembusan'];
    FIELDS.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) { el.addEventListener('input', renderPreview); el.addEventListener('change', renderPreview); }
    });
    document.getElementById('lfArah').addEventListener('change', syncArah);

    document.getElementById('btnCetak').addEventListener('click', printSurat);

    document.getElementById('btnSimpanSurat').addEventListener('click', function () {
        var arah = cval('lfArah') === 'masuk' ? 'masuk' : 'keluar';
        var agenda = cval('lfAgenda');
        var hal = cval('lfHal');
        if (!agenda) { showToast('Nomor agenda wajib diisi', true); return; }
        if (!hal) { showToast('Hal / perihal wajib diisi', true); return; }
        var tglIso = cval('lfTanggal') || isoToday();
        var tglS = fmtTglShort(tglIso);
        var tahun = tglIso.slice(0, 4);
        var pihak = cval('lfPihak');
        var nomor = cval('lfNomor');
        var jenisur = cval('lfJenis') || 'Dll';
        var file = 'Surat_' + agenda.replace(/[\/\\]/g, '-') + '.pdf';
        var ket = 'Dibuat oleh Buat Surat — draf siap cetak.';
        var cari = (agenda + ' ' + pihak + ' ' + hal + ' ' + jenisur + ' ' + nomor + ' ' + tahun + ' ' + tglS).toLowerCase();
        var badgeMap = { 'TND': 'bg-sky-50 text-sky-700', 'SK': 'bg-violet-50 text-violet-700', 'Undangan': 'bg-amber-50 text-amber-700', 'Nota Dinas': 'bg-emerald-50 text-emerald-700', 'Dll': 'bg-slate-100 text-slate-600' };
        var badgeTxt = jenisur === 'Dll' ? 'Lainnya' : jenisur;
        var tr = '<tr class="reveal" data-jenis="' + arah + '" data-tanggal="' + tglS + '" data-tahun="' + tahun + '" data-pihak="' + esc(pihak) + '" data-perihal="' + esc(hal) + '" data-nomor="' + esc(nomor) + '" data-jenisur="' + esc(jenisur) + '" data-file="' + esc(file) + '" data-ket="' + esc(ket) + '" data-cari="' + esc(cari) + '">'
            + '<td><span class="font-semibold text-slate-700">' + esc(agenda) + '</span></td>'
            + '<td class="whitespace-nowrap">' + tglS + '</td>'
            + '<td><span class="font-medium text-slate-700">' + esc(pihak) + '</span></td>'
            + '<td class="max-w-[240px]"><span class="line-clamp-2 font-medium text-slate-700">' + esc(hal) + '</span></td>'
            + '<td class="whitespace-nowrap text-slate-500">' + esc(nomor) + '</td>'
            + '<td><span class="jb-badge ' + badgeMap[jenisur] + '">' + badgeTxt + '</span></td>'
            + '<td>' + miniChip(file, 'Lampiran PDF — baru') + '</td>'
            + '<td><div class="inline-flex items-center justify-end gap-1.5"><button type="button" class="btn-detail btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat Detail</span></button></div></td>'
            + '</tr>';
        document.getElementById('tbSurat').insertAdjacentHTML('beforeend', tr);
        surat = Array.prototype.slice.call(document.querySelectorAll('#tbSurat tr'));
        activeTab = arah;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.setAttribute('aria-selected', b.getAttribute('data-tab') === arah ? 'true' : 'false');
        });
        page = 1;
        recount();
        render();
        bm.classList.remove('show');
        showToast('Surat ' + agenda + ' disimpan ke agenda (dummy) ✓', false);
    });

    recount();
    render();
})();
</script>