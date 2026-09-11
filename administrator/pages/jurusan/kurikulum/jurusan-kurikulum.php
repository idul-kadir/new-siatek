<?php
require_once __DIR__ . '/../../../../fungsi.php';
/**
 * Halaman: Kurikulum
 * Data mengikuti tabel `kurikulum`: kode, nama, tahun, prodi, status.
 * JS: AJAX CRUD, filter, tab, toast, loading indicator, modal konfirmasi.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* Spinner CSS */
    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
    .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }

    /* Toast container */
    #toastContainer {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: .5rem; pointer-events: none;
    }
    .toast-item {
        display: flex; align-items: center; gap: .6rem; min-width: 240px; background: #0f172a; color: #fff; padding: .6rem 1rem; border-radius: .6rem; font-size: .8rem;
        box-shadow: 0 6px 18px rgba(15,23,42,.35); transition: transform .3s ease, opacity .3s ease, height .3s ease; pointer-events: auto;
        max-height: 120px; overflow: hidden; position: relative; animation: slideIn .3s ease forwards;
    }
    .toast-item.toast-success { background: linear-gradient(135deg, #10b981, #059669); }
    .toast-item.toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .toast-item.toast-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .toast-item.toast-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .toast-icon { font-size: 1.1rem; flex-shrink: 0; }
    .toast-close {
        position: absolute; top: .4rem; right: .4rem; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 20px; height: 20px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; line-height: 1;
    }
    .toast-close:hover { background: rgba(255,255,255,0.3); }
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .toast-item.hide { animation: slideOut .3s ease forwards; }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Kurikulum</h1>
                    <p class="text-xs text-slate-500">Data kurikulum program studi — tahun berlaku, status, dan prodi.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3.5 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                <i class="fas fa-plus text-[10px]"></i> Tambah Kurikulum
            </button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari kode, nama kurikulum…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400" disabled>
                <option value="">Memuat tahun...</option>
            </select>
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400" disabled>
                <option value="">Memuat prodi...</option>
            </select>
            <select id="fStatus" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200" disabled>
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Tabel Kurikulum ===== -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Daftar Kurikulum</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlData">0</span> kurikulum ditemukan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-database"></i> <span id="badgeData">0</span> total
                </span>
            </div>

            <div id="areaTabel">
                <div id="noHasil" class="hidden py-16 text-center">
                    <i class="fas fa-book-open text-4xl text-slate-300"></i>
                    <p class="mt-3 font-medium text-slate-500">Tidak ada kurikulum ditemukan</p>
                    <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
                </div>

                <div class="max-h-[560px] overflow-auto" id="wrapTabel">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                        <tr>
                            <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Kode</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Nama Kurikulum</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Program Studi</th>
                            <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tBody" class="divide-y divide-slate-100">
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- ===== Modal Tambah / Edit Kurikulum ===== -->
<div class="modal-overlay" id="kkModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm" id="kkModalTitle"><i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kurikulum</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <form id="kkForm" class="p-5 space-y-4">
            <input type="hidden" id="inpKode" value="">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Kurikulum</label>
                <input type="text" id="inpNama" required placeholder="mis. Kurikulum OBE"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <select id="inpTahun" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400" disabled>
                        <option>Memuat...</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                    <select id="inpStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Program Studi</label>
                <select id="inpProdi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400" disabled>
                    <option>Memuat...</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" data-modal-close class="px-3 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-[#1a365d] hover:bg-[#234670] text-white font-medium" data-text="Simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Detail Kurikulum ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-eye mr-1 text-slate-500"></i>Detail Kurikulum</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
        </div>
        <div class="p-5 space-y-3">
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-hashtag"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Kode</p>
                    <p class="text-sm font-bold text-slate-800 font-mono" id="dtKode">—</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-sm font-bold text-orange-600"><i class="fas fa-book"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Nama Kurikulum</p>
                    <p class="text-sm font-bold text-slate-800" id="dtNama">—</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-calendar"></i></span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Tahun</p>
                        <p class="text-sm font-bold text-slate-800" id="dtTahun">—</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-sm font-bold text-slate-600"><i class="fas fa-toggle-on"></i></span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Status</p>
                        <p class="text-sm font-bold text-slate-800" id="dtStatus">—</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-4 py-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sm font-bold text-sky-600"><i class="fas fa-graduation-cap"></i></span>
                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Program Studi</p>
                    <p class="text-sm font-bold text-slate-800" id="dtProdi">—</p>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-100 px-5 py-3 flex justify-end">
            <button type="button" data-modal-close class="px-4 py-2 text-xs rounded-lg bg-slate-900 hover:bg-slate-700 text-white font-medium transition">Tutup</button>
        </div>
    </div>
</div>

<script>
(function () {
    var BASE_URL = '<?= base_url(); ?>';
    var PROSES_URL = BASE_URL + '/administrator/pages/jurusan/kurikulum/proses.php';

    var body = document.getElementById('tBody');
    var fTahun = document.getElementById('fTahun');
    var fCari = document.getElementById('fCari');
    var fProdi = document.getElementById('fProdi');
    var fStatus = document.getElementById('fStatus');
    var btnReset = document.getElementById('btnReset');
    var wrapTabel = document.getElementById('wrapTabel');
    var noHasil = document.getElementById('noHasil');

    /* ===== Loading Helper ===== */
    function setButtonLoading(btn, isLoading) {
        if (isLoading) {
            btn.classList.add('is-loading');
            btn.setAttribute('disabled', '');
            btn.innerHTML = '<span class="spinner-slate"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
        } else {
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
            btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
        }
    }

    /* ===== Toast Notification ===== */
    var toastContainer = null;
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            document.body.appendChild(toastContainer);
        }
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe;
        var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
        item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'hide\');setTimeout(function(){item.remove()},350)">&times;</button>';
        toastContainer.appendChild(item);
        requestAnimationFrame(function(){ item.classList.add('show'); });
        setTimeout(function(){ item.classList.add('hide'); setTimeout(function(){ item.remove(); }, 350); }, 4000);
    }

    /* ===== Load Data ===== */
    function muatData() {
        if (!wrapTabel) return;
        body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-500"><div class="spinner-slate mx-auto"></div><p class="mt-3">Memuat data...</p></td></tr>';

        $.ajax({
            url: PROSES_URL + '?action=ambil-tabel-kurikulum',
            method: 'GET',
            dataType: 'json',
            success: function(respon){
                if (respon.status && respon.data) {
                    renderTable(respon.data);
                    updateCounts(respon.data);
                    if (wrapTabel) wrapTabel.style.display = respon.data.length > 0 ? '' : 'none';
                    if (noHasil) noHasil.classList.toggle('hidden', respon.data.length > 0);
                } else {
                    body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-500">Gagal memuat ' + (respon.pesan || 'data') + '</td></tr>';
                }
            },
            error: function(){
                body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-500">Terjadi kesalahan pada server</td></tr>';
            }
        });
    }

    function renderTable(rows) {
        if (!body) return;
        body.innerHTML = '';
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            var tr = document.createElement('tr');
            tr.className = (i % 2 === 0) ? 'bg-white transition hover:bg-orange-50' : 'bg-slate-50/60 transition hover:bg-orange-50';
            tr.setAttribute('data-kode', r.kode);
            tr.setAttribute('data-tahun', r.tahun);
            tr.setAttribute('data-prodi', r.prodi);
            tr.setAttribute('data-status', r.status);
            var prodiText = r.prodi_nama || r.prodi;
            tr.setAttribute('data-cari', (r.kode + ' ' + r.nama + ' ' + prodiText + ' ' + r.status).toLowerCase());
            tr.innerHTML =
                '<td class="py-4 pl-5 pr-4"><span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 font-mono">' + r.kode + '</span></td>' +
                '<td class="px-4 py-4"><p class="font-semibold text-slate-800 leading-snug">' + r.nama + '</p></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">' + r.tahun + '</span></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full ' + warnaProdi(r.prodi) + ' px-2.5 py-1 text-xs font-bold">' + prodiText + '</span></td>' +
                '<td class="px-4 py-4"><span class="inline-flex items-center rounded-full ' + warnaStatus(r.status) + ' px-2.5 py-1 text-xs font-bold">' + r.status + '</span></td>' +
                '<td class="py-4 pr-5"><div class="flex items-center gap-1.5">' +
                    '<button type="button" class="btn-circle btn-view bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Detail</span></button>' +
                    '<button type="button" class="btn-circle btn-edit bg-sky-500 text-white shadow-sm hover:bg-sky-600" data-kode="' + r.kode + '"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>' +
                    '<button type="button" class="btn-circle btn-hapus bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-kode="' + r.kode + '"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>' +
                '</div></td>';
            body.appendChild(tr);
        }
        rowsLoaded = Array.prototype.slice.call(body.querySelectorAll('tr'));
    }

    function updateCounts(rows) {
        if (jmlEl) jmlEl.textContent = rows.length;
        if (badgeEl) badgeEl.textContent = rows.length;
    }

    /* ===== Filter ===== */
    function cocok(tr) {
        if (fTahun && fTahun.value !== '' && tr.getAttribute('data-tahun') !== fTahun.value) return false;
        if (fProdi.value !== '' && tr.getAttribute('data-prodi') !== fProdi.value) return false;
        if (fStatus.value !== '' && tr.getAttribute('data-status') !== fStatus.value) return false;
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        if (kata !== '' && (tr.getAttribute('data-cari') || '').indexOf(kata) === -1) return false;
        return true;
    }

    function render() {
        var total = 0;
        for (var i = 0; i < rowsLoaded.length; i++) {
            var tampil = cocok(rowsLoaded[i]);
            rowsLoaded[i].style.display = tampil ? '' : 'none';
            if (tampil) total++;
        }
        if (jmlEl) jmlEl.textContent = total;
        if (badgeEl) badgeEl.textContent = total;
        if (noHasil) noHasil.classList.toggle('hidden', total > 0);
        if (wrapTabel) wrapTabel.style.display = total > 0 ? '' : 'none';
    }

    var jmlEl = document.getElementById('jmlData');
    var badgeEl = document.getElementById('badgeData');
    var rowsLoaded = [];

    if (fTahun) fTahun.addEventListener('change', function () { render(); });
    if (fCari) fCari.addEventListener('input', function () { render(); });
    if (fProdi) fProdi.addEventListener('change', function () { render(); });
    if (fStatus) fStatus.addEventListener('change', function () { render(); });
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fTahun) fTahun.value = '';
        if (fProdi) fProdi.value = '';
        if (fStatus) fStatus.value = '';
        render();
    });

    /* ===== Aksi ===== */
    var modal = document.getElementById('kkModal');
    var mTitle = document.getElementById('kkModalTitle');
    var mForm = document.getElementById('kkForm');
    var mKode = document.getElementById('inpKode');
    var mNama = document.getElementById('inpNama');
    var mTahun = document.getElementById('inpTahun');
    var mStatus = document.getElementById('inpStatus');
    var mProdi = document.getElementById('inpProdi');
    var mEditing = null;
    var detailModal = document.getElementById('detailModal');

    function openTambah() {
        mEditing = null;
        mTitle.innerHTML = '<i class="fas fa-plus mr-1 text-[#f97316]"></i>Tambah Kurikulum';
        mForm.reset();
        if (mTahun) mTahun.value = (new Date()).getFullYear().toString();
        if (modal) modal.classList.add('show');
    }

    function openEdit(kode) {
        mEditing = kode;
        var tr = body.querySelector('[data-kode="' + kode + '"]');
        if (!tr) {
            toast('Data tidak ditemukan', 'error');
            return;
        }
        var kodeText = tr.querySelector('.font-mono') ? tr.querySelector('.font-mono').textContent : '';
        var namaText = tr.querySelector('p.font-semibold') ? tr.querySelector('p.font-semibold').textContent : '';
        var tahunVal = tr.getAttribute('data-tahun') || '';
        var prodiVal = tr.getAttribute('data-prodi') || '';
        var statusVal = tr.getAttribute('data-status') || 'aktif';
        
        mKode.value = kodeText;
        mNama.value = namaText;
        if (mTahun) mTahun.value = tahunVal;
        if (mProdi) mProdi.value = prodiVal;
        if (mStatus) mStatus.value = statusVal.toLowerCase();
        mTitle.innerHTML = '<i class="fas fa-pen mr-1 text-[#0ea5e9]"></i>Edit Kurikulum';
        if (modal) modal.classList.add('show');
    }

    function openDetail(kode) {
        $.ajax({
            url: PROSES_URL + '?action=ambil-kurikulum&kode=' + encodeURIComponent(kode),
            method: 'GET',
            dataType: 'json',
            success: function(respon){
                if (respon.status && respon.data) {
                    var d = respon.data;
                    document.getElementById('dtKode').textContent = d.kode;
                    document.getElementById('dtNama').textContent = d.nama;
                    document.getElementById('dtTahun').textContent = d.tahun;
                    document.getElementById('dtProdi').textContent = d.prodi_nama || d.prodi;
                    document.getElementById('dtStatus').textContent = d.status;
                    document.getElementById('detailModal').classList.add('show');
                } else {
                    toast('Gagal mengambil data', 'error');
                }
            },
            error: function(){ toast('Terjadi kesalahan pada server', 'error'); }
        });
    }

    function warnaProdi(p) {
        if (p === 's1teknikkomputer') return 'bg-emerald-100 text-emerald-700';
        if (p === 's1pendidikanvokasionalrekayasaelektro') return 'bg-violet-100 text-violet-700';
        return 'bg-sky-100 text-sky-700';
    }

    function warnaStatus(s) {
        if (s === 'aktif') return 'bg-emerald-100 text-emerald-700';
        return 'bg-rose-100 text-rose-700';
    }

    document.getElementById('btnTambah').addEventListener('click', openTambah);

    /* ===== Event Delegation ===== */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('button');
        if (!btn) return;
        
        if (btn.classList.contains('btn-edit')) {
            e.preventDefault();
            var kode = btn.getAttribute('data-kode');
            openEdit(kode);
            return;
        }
        
        if (btn.classList.contains('btn-hapus')) {
            e.preventDefault();
            var kode = btn.getAttribute('data-kode');
            if (typeof konfirmasiHapus === 'function') {
                konfirmasiHapus('kurikulum ini', function(){
                    $.ajax({
                        url: PROSES_URL,
                        method: 'POST',
                        data: { 'hapus-data-kurikulum': 1, kode: kode },
                        dataType: 'json',
                        success: function(respon){
                            toast(respon.pesan, respon.status ? 'success' : 'error');
                            if (respon.status) muatData();
                        },
                        error: function(){ toast('Terjadi kesalahan pada server', 'error'); }
                    });
                });
            } else {
                toast('Fungsi konfirmasi tidak tersedia', 'error');
            }
            return;
        }
        
        if (btn.classList.contains('btn-view')) {
            e.preventDefault();
            var kode = btn.closest('tr').querySelector('.font-mono').textContent;
            openDetail(kode);
            return;
        }
        
        var closeBtn = btn.closest('[data-modal-close]');
        if (closeBtn) {
            if (modal) modal.classList.remove('show');
            if (detailModal) detailModal.classList.remove('show');
            return;
        }
    });

    modal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { modal.classList.remove('show'); });
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

    detailModal.querySelectorAll('[data-modal-close]').forEach(function (b) {
        b.addEventListener('click', function () { detailModal.classList.remove('show'); });
    });
    detailModal.addEventListener('click', function (e) { if (e.target === detailModal) detailModal.classList.remove('show'); });

    /* ===== Modal Form Submit ===== */
    mForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var kode = mKode.value.trim();
        var nama = mNama.value.trim();
        var tahun = mTahun.value || '';
        var prodi = mProdi.value || '';
        var status = mStatus.value || 'aktif';

        if ((mEditing && kode === '') || nama === '' || tahun === '' || prodi === '') {
            toast('Semua field wajib diisi', 'error');
            return;
        }

        var btnSimpan = this.querySelector('button[type="submit"]');
        setButtonLoading(btnSimpan, true);

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: mEditing 
                ? { 'edit-data-kurikulum': 1, kode: kode, nama: nama, tahun: tahun, prodi: prodi, status: status }
                : { 'tambah-data-kurikulum': 1, nama: nama, tahun: tahun, prodi: prodi, status: status },
            dataType: 'json',
            success: function(respon){
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if (respon.status) {
                    modal.classList.remove('show');
                    mForm.reset();
                    muatData();
                }
            },
            error: function(){ toast('Terjadi kesalahan pada server', 'error'); },
            complete: function(){ setButtonLoading(btnSimpan, false); }
        });
    });

    /* ===== Load Data Saat DOM Siap ===== */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            loadProdi();
            loadTahun();
        });
    } else {
        loadProdi();
        loadTahun();
    }

    function loadProdi() {
        $.ajax({
            url: PROSES_URL + '?action=ambil-prodi-kurikulum',
            method: 'GET',
            dataType: 'json',
            success: function(respon){
                if (respon.status && respon.data) {
                    var html = '<option value="">Semua Prodi</option>';
                    var htmlModal = '';
                    for (var i = 0; i < respon.data.length; i++) {
                        var p = respon.data[i];
                        html += '<option value="' + p.kode + '">' + p.nama + '</option>';
                        htmlModal += '<option value="' + p.kode + '">' + p.nama + '</option>';
                    }
                    fProdi.innerHTML = html;
                    fProdi.disabled = false;
                    if (btnReset) btnReset.disabled = false;
                    if (mProdi) { mProdi.innerHTML = htmlModal; mProdi.disabled = false; }
                    muatData();
                }
            },
            error: function(){ fProdi.innerHTML = '<option value="">Gagal memuat prodi</option>'; }
        });
    }

    function loadTahun() {
        $.ajax({
            url: PROSES_URL + '?action=ambil-tahun-kurikulum',
            method: 'GET',
            dataType: 'json',
            success: function(respon){
                if (respon.status && respon.data) {
                    var htmlFilter = '<option value="">Semua Tahun</option>';
                    var htmlModal = '';
                    for (var i = 0; i < respon.data.length; i++) {
                        htmlFilter += '<option value="' + respon.data[i].tahun + '">' + respon.data[i].tahun + '</option>';
                        htmlModal += '<option value="' + respon.data[i].tahun + '">' + respon.data[i].tahun + '</option>';
                    }
                    if (fTahun) { fTahun.innerHTML = htmlFilter; fTahun.disabled = false; }
                    if (mTahun) { mTahun.innerHTML = htmlModal; mTahun.disabled = false; }
                    muatData();
                }
            },
            error: function(){ if (fTahun) fTahun.innerHTML = '<option value="">Gagal memuat tahun</option>'; }
        });
    }
})();
</script>
