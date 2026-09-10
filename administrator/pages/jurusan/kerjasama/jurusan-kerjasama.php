<?php
/**
 * Halaman: Kerja Sama
 * Tampilan murni HTML statis (tanpa data PHP / JavaScript dinamis).
 * Karakteristik data mengikuti tabel `kerjasama`:
 * instansi, tanggal (tahun), deskripsi, tingkat, bidang, bukti, file, logo.
 * Satu kerja sama dapat memiliki lebih dari satu dokumen (IA, PKS, MoU) —
 * link dokumen menyatu pada badge bukti, sehingga satu bukti = satu dokumen.
 * JS hanya untuk: tab tingkat, cari, filter, pagination 15/halaman, modal tambah/edit, dan hapus.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }


    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .tip-wrap { position: relative; }
    .tip-wrap .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); max-width: 260px; overflow: hidden; text-overflow: ellipsis; }
    .tip-wrap:hover .tip { opacity: 1; visibility: visible; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569;
        font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }

    .doc-row { display: flex; align-items: center; gap: .5rem; }
    .doc-row select { flex: 0 0 auto; width: 100px; }
    .doc-row .doc-file { flex: 1; min-width: 0; }
    .doc-row .doc-name { font-size: .75rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .doc-row .doc-del { flex: 0 0 auto; }
    .doc-row.is-existing { background: #ecfdf5; border-radius: 6px; padding: 4px; }
    .doc-row.is-new { background: #fffbeb; border-radius: 6px; padding: 4px; }
    .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
    #toastContainer { position: fixed; top: 15px; right: 15px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
    .toast-item { min-width: 300px; background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); color: #fff; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .toast-item.show { animation: slideIn 0.3s ease; }
    .toast-item.hide { animation: slideOut 0.3s ease; }
    .toast-icon { font-size: 16px; }
    .toast-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; opacity: 0.8; padding: 0; line-height: 1; }
    .toast-close:hover { opacity: 1; }
    @keyframes slideIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
    .toast-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .toast-error { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .toast-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .acc-loading { padding: 60px 20px; text-align: center; color: #64748b; }
    .acc-empty { padding: 60px 20px; text-align: center; color: #94a3b8; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kerja Sama</h1>
                    <p class="text-xs text-slate-500">Data kerja sama jurusan dengan instansi — bidang pendidikan, pengabdian, dan penelitian.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Kerja Sama</span>
            </button>
        </div>
    </section>

    <!-- ===== Statistik (informasi saja, bukan tombol filter) ===== -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="tile-orange tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-orange-500/25">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Kerja Sama</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="statTotal">0</p>
                    <p class="mt-2 text-[11px] text-white/70">seluruh instansi mitra</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-building"></i></span>
            </div>
        </div>
        <div class="tile-sky tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pendidikan</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="statPendidikan">0</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang pendidikan</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-graduation-cap"></i></span>
            </div>
        </div>
        <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengabdian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="statPengabdian">0</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang pengabdian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hands-helping"></i></span>
            </div>
        </div>
        <div class="tile-violet tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Penelitian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight" id="statPenelitian">0</p>
                    <p class="mt-2 text-[11px] text-white/70">bidang penelitian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-flask"></i></span>
            </div>
        </div>
    </section>

    <!-- ===== Tabs Tingkat Kerja Sama ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="" aria-selected="true"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-all">50</span></button>
            <button type="button" class="tab-btn" data-tab="Lokal" aria-selected="false"><span class="tdot bg-sky-500"></span>Lokal<span class="tnum" id="cnt-Lokal">40</span></button>
            <button type="button" class="tab-btn" data-tab="Nasional" aria-selected="false"><span class="tdot bg-emerald-500"></span>Nasional<span class="tnum" id="cnt-Nasional">9</span></button>
            <button type="button" class="tab-btn" data-tab="Internasional" aria-selected="false"><span class="tdot bg-violet-500"></span>Internasional<span class="tnum" id="cnt-Internasional">1</span></button>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter) ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari instansi, deskripsi…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option>2026</option>
                <option>2025</option>
                <option>2024</option>
                <option>2023</option>
                <option>2022</option>
                <option>2021</option>
                <option>2020</option>
            </select>
            <select id="fBukti" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Bukti</option>
                <option>IA</option>
                <option>MoU</option>
                <option>PKS</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Tabel Kerja Sama ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Daftar Kerja Sama</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlData">50</span> kerja sama ditemukan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-database"></i> <span id="badgeData">50</span> total
                </span>
            </div>

            <div id="areaTabel">
                <div id="noHasil" class="hidden py-16 text-center">
                    <i class="fas fa-handshake text-4xl text-slate-300"></i>
                    <p class="mt-3 font-medium text-slate-500">Tidak ada kerja sama ditemukan</p>
                    <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
                </div>

                <div class="max-h-[560px] overflow-auto" id="wrapTabel">
                <table class="w-full min-w-[1000px] text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Instansi</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Bidang</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tingkat</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Bukti</th>
                            <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tBody" class="divide-y divide-slate-100">
                        <tr><td colspan="6" class="py-16 text-center text-slate-500"><div class="spinner-slate mx-auto"></div><p class="mt-3">Memuat data...</p></td></tr>
                    </tbody>
                </table>
            </div>

            <div id="footerTabel" class="flex items-center justify-between border-t border-slate-100 px-5 py-3">
                <button type="button" id="btnPrev" class="pg-btn"><i class="fas fa-chevron-left text-xs"></i></button>
                <p class="text-xs font-semibold text-slate-500" id="lblPage">Halaman 1 / 1</p>
                <button type="button" id="btnNext" class="pg-btn"><i class="fas fa-chevron-right text-xs"></i></button>
            </div>
            </div>
        </div>
    </section>

</main>

<div id="toastContainer"></div>

<!-- ===== Modal Tambah / Edit Kerja Sama (frontend only) ===== -->
<div class="modal-overlay" id="ksModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm" id="ksModalTitle"><i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kerja Sama</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <form id="ksForm" class="p-5 space-y-4">
            <input type="hidden" id="inpId" name="id" value="">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Instansi <span class="text-rose-500">*</span></label>
                <input type="text" id="inpInstansi" name="instansi" required placeholder="mis. PT. PLN UP3 Gorontalo"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi <span class="text-rose-500">*</span></label>
                <input type="text" id="inpDeskripsi" name="deskripsi" required placeholder="Ringkasan kerja sama"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun <span class="text-rose-500">*</span></label>
                    <select id="inpTahun" name="tahun" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="">-- Pilih Tahun --</option>
                        <option>2026</option><option>2025</option><option>2024</option><option>2023</option><option>2022</option><option>2021</option><option>2020</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Bidang <span class="text-rose-500">*</span></label>
                    <select id="inpBidang" name="bidang" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="">-- Pilih Bidang --</option>
                        <option>Pendidikan</option><option>Pengabdian</option><option>Penelitian</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tingkat <span class="text-rose-500">*</span></label>
                    <select id="inpTingkat" name="tingkat" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="">-- Pilih Tingkat --</option>
                        <option>Lokal</option><option>Nasional</option><option>Internasional</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Dokumen Bukti <span class="text-[10px] font-normal text-slate-400">(opsional)</span></label>
                <p class="mb-2 text-[11px] text-slate-500 leading-snug">
                    <i class="fas fa-info-circle text-sky-500 mr-1"></i>
                    Dokumen yang tersimpan bertanda <span class="inline-flex items-center gap-1 rounded bg-emerald-100 text-emerald-700 px-1.5 py-px text-[9px] font-bold">tersimpan</span>. Tidak perlu upload ulang jika tidak ingin menggantinya — biarkan kosong saat edit.
                </p>
                <div id="docList" class="space-y-2"></div>
                <button type="button" id="btnAddDoc" class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-orange-400 hover:text-orange-600">
                    <i class="fas fa-plus text-[10px]"></i> Tambah Dokumen
                </button>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-modal-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" data-text="Simpan" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var ITEM_PER_PAGE = 15;
    var BASE_URL = '<?= base_url() ?>';
    var PROSES_URL = BASE_URL + '/administrator/pages/jurusan/kerjasama/proses.php';

    var tab = '', halaman = 1;

    var body = document.getElementById('tBody');
    var rows = [];
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fBukti = document.getElementById('fBukti');
    var jmlEl = document.getElementById('jmlData');
    var badgeEl = document.getElementById('badgeData');
    var kosongEl = document.getElementById('noHasil');
    var wrapTabel = document.getElementById('wrapTabel');
    var footerTabel = document.getElementById('footerTabel');
    var lblPage = document.getElementById('lblPage');
    var btnPrev = document.getElementById('btnPrev');
    var btnNext = document.getElementById('btnNext');

    /* ===== Toast Notifikasi (global) ===== */
    window.toast = function(pesan, tipe) {
        var c = document.getElementById('toastContainer');
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + (tipe || 'info');
        var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
        if (c) c.appendChild(item); else document.body.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });
        setTimeout(function(){ if (item.parentElement) item.remove(); }, 4000);
    };

    /* ===== Muat Statistik dari Database ===== */
    function muatStatistik() {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-statistik-kerjasama': 1 },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    var d = respon.data;
                    var elT = document.getElementById('statTotal'); if (elT) elT.textContent = d.total.toLocaleString('id-ID');
                    var elP = document.getElementById('statPendidikan'); if (elP) elP.textContent = d.pendidikan.toLocaleString('id-ID');
                    var elA = document.getElementById('statPengabdian'); if (elA) elA.textContent = d.pengabdian.toLocaleString('id-ID');
                    var elN = document.getElementById('statPenelitian'); if (elN) elN.textContent = d.penelitian.toLocaleString('id-ID');
                }
            }
        });
    }

    /* ===== Muat Ulang Tabel dari Database ===== */
    function muatUlangTabel() {
        body.innerHTML = '<tr><td colspan="6" class="py-16 text-center text-slate-500"><div class="spinner-slate mx-auto"></div><p class="mt-3">Memuat data...</p></td></tr>';

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-tabel-kerjasama': 1 },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    body.innerHTML = respon.data;
                    rows = Array.prototype.slice.call(body.querySelectorAll('tr[data-id]'));
                    render();
                } else {
                    body.innerHTML = '<tr><td colspan="6" class="py-16 text-center text-rose-500">' + respon.pesan + '</td></tr>';
                }
            },
            error: function() {
                body.innerHTML = '<tr><td colspan="6" class="py-16 text-center text-rose-500">Gagal memuat data dari server.</td></tr>';
            }
        });
    }

    /* ===== Hapus Data ===== */
    function hapusData(id, btn) {
        var tr = btn.closest('tr');
        var nama = tr ? tr.querySelector('p.font-semibold').textContent : '';
        if (typeof window.konfirmasiHapus === 'function') {
            window.konfirmasiHapus(nama, function(){
                eksekusiHapus(id, btn);
            });
        } else if (confirm('Hapus kerja sama ini?')) {
            eksekusiHapus(id, btn);
        }
    }

    function eksekusiHapus(id, btn) {
        btn.setAttribute('disabled', '');
        var teks = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-slate"></span>';
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'hapus-data-kerjasama': 1, id: id },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    toast(respon.pesan, 'success');
                    muatUlangTabel();
                    muatStatistik();
                } else {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = teks;
                    toast(respon.pesan, 'error');
                }
            },
            error: function() {
                btn.removeAttribute('disabled');
                btn.innerHTML = teks;
                toast('Terjadi kesalahan pada server', 'error');
            }
        });
    }

    /* ===== Event Delegation untuk Edit & Hapus ===== */
    body.addEventListener('click', function(e) {
        var btnEdit = e.target.closest('.btnEdit');
        if (btnEdit) { openEdit(btnEdit.closest('tr')); return; }
        var btnHapus = e.target.closest('.btnHapus');
        if (btnHapus) {
            var id = btnHapus.getAttribute('data-id');
            if (id) hapusData(id, btnHapus);
        }
    });

    function cocok(tr, ta) {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        if (kata !== '' && (tr.getAttribute('data-cari') || '').indexOf(kata) === -1) return false;
        if (ta !== null && ta !== '' && tr.getAttribute('data-tingkat') !== ta) return false;
        if (fTahun && fTahun.value !== '' && tr.getAttribute('data-tahun') !== fTahun.value) return false;
        return true;
    }

    function updateCounts() {
        var cnt = { '': 0, 'Lokal': 0, 'Nasional': 0, 'Internasional': 0 };
        for (var i = 0; i < rows.length; i++) {
            var ti = rows[i].getAttribute('data-tingkat');
            if (cocok(rows[i], null)) { cnt['']++; cnt[ti] = (cnt[ti] || 0) + 1; }
        }
        var c = document.getElementById('cnt-all');
        if (c) c.textContent = cnt[''].toLocaleString('id-ID');
        ['Lokal', 'Nasional', 'Internasional'].forEach(function (t) {
            var el = document.getElementById('cnt-' + t);
            if (el) el.textContent = (cnt[t] || 0).toLocaleString('id-ID');
        });
    }

    function render() {
        var tutup = (halaman - 1) * ITEM_PER_PAGE;
        var pos = 0, total = 0;
        for (var i = 0; i < rows.length; i++) {
            var tampil = cocok(rows[i], tab);
            if (tampil) {
                total++;
                rows[i].style.display = (pos >= tutup && pos < tutup + ITEM_PER_PAGE) ? '' : 'none';
                pos++;
            } else {
                rows[i].style.display = 'none';
            }
        }
        var jmlHal = Math.max(1, Math.ceil(total / ITEM_PER_PAGE));
        if (halaman > jmlHal) { halaman = jmlHal; render(); return; }
        if (jmlEl) jmlEl.textContent = total.toLocaleString('id-ID');
        if (badgeEl) badgeEl.textContent = total.toLocaleString('id-ID');
        if (lblPage) lblPage.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;
        if (btnPrev) btnPrev.disabled = halaman <= 1;
        if (btnNext) btnNext.disabled = halaman >= jmlHal;
        if (kosongEl) kosongEl.classList.toggle('hidden', total > 0);
        if (wrapTabel) wrapTabel.style.display = total > 0 ? '' : 'none';
        if (footerTabel) footerTabel.style.display = total > 0 ? '' : 'none';
        updateCounts();
    }

    function setTab(t) {
        tab = t;
        halaman = 1;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.setAttribute('aria-selected', b.getAttribute('data-tab') === t ? 'true' : 'false');
        });
        render();
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () { setTab(b.getAttribute('data-tab')); });
    });
    if (fCari) fCari.addEventListener('input', function () { halaman = 1; render(); });
    if (fTahun) fTahun.addEventListener('change', function () { halaman = 1; render(); });
    if (fBukti) fBukti.addEventListener('change', function () { halaman = 1; render(); });
    if (btnPrev) btnPrev.addEventListener('click', function () { if (halaman > 1) { halaman--; render(); } });
    if (btnNext) btnNext.addEventListener('click', function () { halaman++; render(); });
    var btnReset = document.getElementById('btnReset');
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fTahun) fTahun.value = '';
        if (fBukti) fBukti.value = '';
        setTab('');
    });

    /* ===== Modal Tambah / Edit ===== */
    var modal = document.getElementById('ksModal');
    var mTitle = document.getElementById('ksModalTitle');
    var mForm = document.getElementById('ksForm');
    var mInstansi = document.getElementById('inpInstansi');
    var mTahun = document.getElementById('inpTahun');
    var mBidang = document.getElementById('inpBidang');
    var mTingkat = document.getElementById('inpTingkat');
    var mDeskripsi = document.getElementById('inpDeskripsi');
    var mEditing = null;

    /* ---- Dynamic doc list ---- */
    var docList = document.getElementById('docList');
    var btnAddDoc = document.getElementById('btnAddDoc');

    function addDocRow(list, type, fileName, existing) {
        var d = document.createElement('div');
        var fname = fileName || '';
        var displayName = fname ? fname : 'Belum ada file';
        var isExisting = existing && existing.id_bukti ? true : false;
        var idBukti = isExisting ? existing.id_bukti : 0;
        var badge = isExisting
            ? '<span class="ml-1 inline-flex items-center gap-1 rounded bg-emerald-100 text-emerald-700 px-1.5 py-px text-[9px] font-bold">tersimpan</span>'
            : '<span class="ml-1 inline-flex items-center gap-1 rounded bg-amber-100 text-amber-700 px-1.5 py-px text-[9px] font-bold">baru</span>';
        d.className = 'doc-row ' + (isExisting ? 'is-existing' : 'is-new');
        d.innerHTML =
            '<select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-sm outline-none focus:border-orange-400">' +
                '<option value="IA"' + (type === 'IA' ? ' selected' : '') + '>IA</option>' +
                '<option value="MoU"' + (type === 'MoU' ? ' selected' : '') + '>MoU</option>' +
                '<option value="PKS"' + (type === 'PKS' ? ' selected' : '') + '>PKS</option>' +
            '</select>' +
            '<div class="doc-file flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2">' +
                '<i class="fas fa-file-pdf text-slate-400 text-xs shrink-0"></i>' +
                '<span class="doc-name">' + displayName + '</span>' + badge +
                '<label class="ml-auto inline-flex cursor-pointer items-center gap-1 rounded bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white transition hover:bg-slate-700 shrink-0">' +
                    '<i class="fas fa-folder-open text-[9px]"></i> Ganti' +
                    '<input type="file" class="hidden" accept=".pdf,.doc,.docx">' +
                '</label>' +
            '</div>' +
            '<button type="button" class="doc-del btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" style="width:1.75rem;height:1.75rem;" title="' + (isExisting ? 'Hapus dokumen ini' : 'Hapus baris') + '"><i class="fas fa-xmark text-[10px]"></i></button>';
        var fileInp = d.querySelector('input[type="file"]');
        var nameEl = d.querySelector('.doc-name');
        var badgeEl = d.querySelector('.ml-1.inline-flex');
        var typeSelect = d.querySelector('select');
        var badgeText = isExisting ? 'tersimpan' : 'baru';
        if(fileInp) badgeText += ' (dirubah)';
        d.setAttribute('data-id-bukti', idBukti);
        d.setAttribute('data-existing', isExisting ? '1' : '0');
        typeSelect.addEventListener('change', function() {
            var newType = this.value;
            nameEl.textContent = fname ? fname : (newType ? newType : 'Belum ada file');
            badgeEl.textContent = badgeText;
            if (isExisting && idBukti > 0) {
                updateJenisBukti(idBukti, newType, d);
            }
        });
        fileInp.addEventListener('change', function () {
            if (fileInp.files.length) {
                nameEl.textContent = fileInp.files[0].name;
                // tandai jadi baris baru (file lama akan dihapus dari server saat submit)
                if (isExisting) {
                    d.classList.remove('is-existing');
                    d.classList.add('is-new');
                    if (badgeEl) {
                        badgeEl.classList.remove('bg-emerald-100', 'text-emerald-700');
                        badgeEl.classList.add('bg-amber-100', 'text-amber-700');
                        badgeEl.textContent = 'diganti';
                    }
                }
            }
        });
        d.querySelector('.doc-del').addEventListener('click', function () {
            if (isExisting) {
                if (typeof window.konfirmasiHapus === 'function') {
                    window.konfirmasiHapus('Dokumen ' + type + ' (' + fname + ')?', function(){
                        hapusBukti(idBukti, d);
                    });
                } else if (confirm('Hapus dokumen ' + type + '?')) {
                    hapusBukti(idBukti, d);
                }
            } else {
                d.remove();
            }
        });
        list.appendChild(d);
    }

    function updateJenisBukti(idBukti, jenisBaru, rowEl) {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'update-jenis-bukti': 1, id_bukti: idBukti, jenis_bukti: jenisBaru },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    toast(respon.pesan, 'success');
                    muatUlangTabel();
                } else {
                    toast(respon.pesan, 'error');
                }
            },
            error: function() { toast('Gagal mengubah jenis bukti', 'error'); }
        });
    }

    function hapusBukti(idBukti, rowEl) {
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'hapus-bukti-kerjasama': 1, id_bukti: idBukti },
            dataType: 'json',
            success: function(respon){
                if(respon.status){
                    rowEl.remove();
                    toast(respon.pesan, 'success');
                } else {
                    toast(respon.pesan, 'error');
                }
            },
            error: function(){ toast('Gagal menghapus dokumen', 'error'); }
        });
    }

    function getDocs(list) {
        var docs = [];
        var rows = list.querySelectorAll('.doc-row');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var t = row.querySelector('select').value;
            var n = row.querySelector('.doc-name').textContent;
            var f = row.querySelector('input[type="file"]');
            var punyaFile = f && f.files && f.files.length > 0;
            var idBukti = parseInt(row.getAttribute('data-id-bukti') || '0', 10) || 0;
            var isExisting = row.getAttribute('data-existing') === '1';
            // Hanya masukkan baris yang:
            // - baru ditambah (ada file baru dipilih), atau
            // - existing yang filenya diganti (punyaFile true)
            if (punyaFile) {
                docs.push({ type: t, name: n, file: f, id_bukti: idBukti, existing: isExisting });
            }
        }
        return docs;
    }

    function setDocs(list, docs) {
        list.innerHTML = '';
        for (var i = 0; i < docs.length; i++) addDocRow(list, docs[i].type, docs[i].name, docs[i].existing || null);
    }

    if (btnAddDoc) btnAddDoc.addEventListener('click', function () { addDocRow(docList, 'IA', ''); });

    /* ---- Open / Close ---- */
    function openTambah() {
        mEditing = null;
        mTitle.innerHTML = '<i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kerja Sama';
        mForm.reset();
        var inpId = document.getElementById('inpId');
        if (inpId) inpId.value = '';
        docList.innerHTML = '';
        addDocRow(docList, 'IA', '');
        if (mTahun) mTahun.value = '2026';
        if (modal) modal.classList.add('show');
    }

    function openEdit(tr) {
        mEditing = tr;
        var inpId = document.getElementById('inpId');
        if (inpId) inpId.value = tr.getAttribute('data-id') || '';
        var inst = tr.querySelector('p.font-semibold');
        var desc = tr.querySelector('p.line-clamp-1');
        mInstansi.value = inst ? inst.textContent : '';
        mDeskripsi.value = desc ? desc.textContent : '';
        mTahun.value = tr.getAttribute('data-tahun') || '';
        // normalkan tahun (hapus spasi / trim)
        if (mTahun.value === 'null' || mTahun.value === '') mTahun.value = '';
        mBidang.value = tr.getAttribute('data-bidang') || '';
        mTingkat.value = tr.getAttribute('data-tingkat') || '';

        // ambil bukti + id bukti dari tombol Edit (supaya bisa hapus per dokumen)
        var btnEdit = tr.querySelector('.btnEdit');
        var namaBukti = [];
        var idBukti = [];
        if (btnEdit) {
            try {
                namaBukti = JSON.parse(btnEdit.getAttribute('data-bukti') || '[]');
                idBukti = JSON.parse(btnEdit.getAttribute('data-bukti-ids') || '[]');
            } catch (e) { /* abaikan */ }
        }
        var docs = [];
        for (var i = 0; i < namaBukti.length; i++) {
            docs.push({
                type: namaBukti[i],
                name: 'dokumen-' + (namaBukti[i] || 'IA').toLowerCase() + '-' + (idBukti[i] || 0) + '.pdf',
                existing: { id_bukti: idBukti[i] }
            });
        }
        if (docs.length === 0) docs.push({ type: 'IA', name: '' });
        setDocs(docList, docs);

        mTitle.innerHTML = '<i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Kerja Sama';
        if (modal) modal.classList.add('show');
    }

    function escapeHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    document.getElementById('btnTambah').addEventListener('click', openTambah);
    modal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { modal.classList.remove('show'); });
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

    /* ---- Helpers ---- */
    function buatBadgeBukti(type, name) {
        var fname = name || ('dokumen-' + type.toLowerCase() + '.pdf');
        return '<a href="#" class="tip-wrap inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200"><i class="fas fa-download text-[10px]"></i>' + type + '<span class="tip">' + fname + '</span></a>';
    }

    function warnaBidang(b) {
        if (b === 'Pengabdian') return 'bg-emerald-100 text-emerald-700';
        if (b === 'Penelitian') return 'bg-violet-100 text-violet-700';
        return 'bg-sky-100 text-sky-700';
    }

    function warnaTingkat(t) {
        if (t === 'Nasional') return 'bg-emerald-100 text-emerald-700';
        if (t === 'Internasional') return 'bg-rose-100 text-rose-700';
        return 'bg-slate-100 text-slate-600';
    }

    function badgeBuktiHTML(docs) {
        var h = '';
        for (var i = 0; i < docs.length; i++) h += buatBadgeBukti(docs[i].type, docs[i].name);
        return h;
    }

    function applyForm(tr) {
        var instansi = mInstansi.value.trim();
        var deskripsi = mDeskripsi.value.trim() || 'Kerja sama dengan instansi mitra';
        var tahun = mTahun.value || '2026';
        var bidang = mBidang.value || 'Pendidikan';
        var tingkat = mTingkat.value || 'Lokal';
        var docs = getDocs(docList);
        var buktiStr = docs.map(function (d) { return d.type; }).join(' ');

        tr.setAttribute('data-tahun', tahun);
        tr.setAttribute('data-bidang', bidang);
        tr.setAttribute('data-tingkat', tingkat);
        tr.setAttribute('data-bukti', buktiStr);
        tr.setAttribute('data-cari', (instansi + ' ' + deskripsi + ' ' + bidang + ' ' + tingkat + ' ' + buktiStr).toLowerCase());

        tr.querySelector('p.font-semibold').textContent = instansi;
        tr.querySelector('p.line-clamp-1').textContent = deskripsi;

        var td = tr.querySelectorAll('td');
        td[1].innerHTML = '<span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">' + tahun + '</span>';
        td[2].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaBidang(bidang) + ' px-2.5 py-1 text-xs font-bold">' + bidang + '</span>';
        td[3].innerHTML = '<span class="inline-flex items-center rounded-full ' + warnaTingkat(tingkat) + ' px-2.5 py-1 text-xs font-bold">' + tingkat + '</span>';
        td[4].innerHTML = '<div class="flex flex-wrap items-center gap-1">' + badgeBuktiHTML(docs) + '</div>';
    }

    function barisBaru() {
        return '<td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"></span><div class="min-w-0"><p class="font-semibold text-slate-800 leading-snug">&#8212;</p><p class="text-xs text-slate-500 line-clamp-1"></p></div></div></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-2.5 py-1 text-xs font-bold">Pendidikan</span></td>' +
            '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-xs font-bold">Lokal</span></td>' +
            '<td class="px-4 py-4"><div class="flex flex-wrap items-center gap-1"></div></td>' +
            '<td class="py-4 pr-5"><div class="flex items-center gap-1.5"><button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button><button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button></div></td>';
    }

    function badgeNomor() {
        var nRows = body.querySelectorAll('tr');
        for (var i = 0; i < nRows.length; i++) {
            var numEl = nRows[i].querySelector('span.text-base.font-bold');
            if (numEl) numEl.textContent = (i + 1) + '';
        }
    }

    mForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var instansi = mInstansi.value.trim();
        var deskripsi = mDeskripsi.value.trim();
        var tahun = mTahun.value;
        var bidang = mBidang.value;
        var tingkat = mTingkat.value;
        var docs = getDocs(docList);

        if (instansi === '') {
            toast('Field Instansi wajib diisi.', 'error');
            mInstansi.focus();
            return;
        }
        if (tahun === '') {
            toast('Field Tahun wajib diisi.', 'error');
            mTahun.focus();
            return;
        }
        if (bidang === '') {
            toast('Field Bidang wajib diisi.', 'error');
            mBidang.focus();
            return;
        }
        if (tingkat === '') {
            toast('Field Tingkat wajib diisi.', 'error');
            mTingkat.focus();
            return;
        }
        if (deskripsi === '') {
            toast('Field Deskripsi wajib diisi.', 'error');
            mDeskripsi.focus();
            return;
        }

        var btnSimpan = this.querySelector('button[type="submit"]');
        btnSimpan.setAttribute('disabled', '');
        btnSimpan.innerHTML = '<span class="spinner-slate"></span> ' + (btnSimpan.getAttribute('data-text') || btnSimpan.textContent);

        var formData = new FormData(this);
        formData.append(mEditing ? 'edit-data-kerjasama' : 'tambah-data-kerjasama', 1);

        var i = 0;
        var j = 0;
        var allDocRows = docList.querySelectorAll('.doc-row');
        allDocRows.forEach(function(row) {
            var t = row.querySelector('select').value || 'IA';
            var f = row.querySelector('input[type="file"]');
            var file = f && f.files && f.files.length > 0 ? f.files[0] : null;
            var idBukti = parseInt(row.getAttribute('data-id-bukti') || '0', 10) || 0;
            if (idBukti > 0) {
                formData.append('id_bukti[' + j + ']', idBukti);
                formData.append('jenis_bukti[' + j + ']', t);
                j++;
            }
            if (file) {
                formData.append('jenis_' + i, t);
                formData.append('file_' + i, file);
                i++;
            }
        });

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(respon) {
                btnSimpan.removeAttribute('disabled');
                btnSimpan.innerHTML = btnSimpan.getAttribute('data-text') || btnSimpan.textContent;

                if (respon.status) {
                    toast(respon.pesan, 'success');
                    modal.classList.remove('show');
                    mForm.reset();
                    muatUlangTabel();
                    muatStatistik();
                } else {
                    toast(respon.pesan, 'error');
                }
            },
            error: function() {
                btnSimpan.removeAttribute('disabled');
                btnSimpan.innerHTML = btnSimpan.getAttribute('data-text') || btnSimpan.textContent;
                toast('Terjadi kesalahan pada server', 'error');
            }
        });
    });

    /* ===== Inisialisasi: muat data dari database ===== */
    muatUlangTabel();
    muatStatistik();
})();
</script>