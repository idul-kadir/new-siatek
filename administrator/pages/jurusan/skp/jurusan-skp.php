<?php
/**
 * Halaman: SKP — Sasaran Kinerja Pegawai (Jurusan)
 * Monitoring SKP dosen yang sudah masuk (diunggah dari akun masing-masing dosen).
 * Setiap 1 data SKP memiliki 2 file:
 *   - File Draft  : hasil unggahan dosen (pengisian SKP).
 *   - File Final  : hasil verifikasi pejabat jurusan.
 */
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #7dd3fc; color: #0369a1; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }

    .st-badge { display: inline-flex; align-items: center; gap: .3rem; padding: .22rem .6rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }
    .pr-badge { display: inline-block; padding: .18rem .55rem; border-radius: .45rem; font-size: 10px; font-weight: 700; white-space: nowrap; }

    .pin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pin-table thead th { background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: .6rem .85rem; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .pin-table tbody td { padding: .7rem .85rem; font-size: .78rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .pin-table tbody tr:last-child td { border-bottom: none; }
    .pin-table tbody tr { transition: background .15s ease; }
    .pin-table tbody tr:hover { background: #f8fafc; }

    .file-chip { display: inline-flex; align-items: center; gap: .5rem; min-width: 0; max-width: 220px; padding: .32rem .55rem; border-radius: .6rem; border: 1px solid #e2e8f0; background: #f8fafc; }
    .file-chip .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .45rem; font-size: .72rem; flex-shrink: 0; }
    .f-pdf   { background: #fee2e2; color: #dc2626; }
    .f-word  { background: #dbeafe; color: #2563eb; }
    .file-chip .f-meta { min-width: 0; }
    .file-chip .f-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; font-size: .72rem; color: #334155; }
    .file-chip .f-sub { display: block; font-size: .63rem; color: #94a3b8; }
    .f-down { display: inline-flex; align-items: center; justify-content: center; width: 1.6rem; height: 1.6rem; border-radius: .4rem; background: #fff; border: 1px solid #e2e8f0; color: #64748b; cursor: pointer; flex-shrink: 0; transition: all .15s ease; }
    .f-down:hover { background: #0e7490; border-color: #0e7490; color: #fff; }

    .empty-dash { display: inline-flex; align-items: center; gap: .35rem; color: #94a3b8; font-style: italic; }
    .empty-dash i { color: #cbd5e1; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #7dd3fc; color: #0369a1; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }

    #toastSkp { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #toastSkp.show { display: flex; animation: riseIn .3s ease; }

    /* CSS Spinner & Loading state */
    .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-lg text-sky-600">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">SKP — Sasaran Kinerja Pegawai</h1>
                    <p class="text-xs text-slate-500">Monitoring SKP dosen yang sudah masuk. Draft diunggah dari akun masing-masing dosen, lalu diverifikasi pejabat jurusan menjadi file final.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Tab Status ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="semua" aria-selected="false"><span class="tdot bg-slate-500"></span>Semua<span class="tnum" id="cnt-semua">0</span></button>
            <button type="button" class="tab-btn" data-tab="menunggu" aria-selected="true"><span class="tdot bg-amber-500"></span>Menunggu Review<span class="tnum" id="cnt-menunggu">0</span></button>
            <button type="button" class="tab-btn" data-tab="selesai" aria-selected="false"><span class="tdot bg-emerald-500"></span>Selesai<span class="tnum" id="cnt-selesai">0</span></button>
        </div>
    </section>

    <!-- ===== Toolbar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama dosen, NIP…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-sky-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Tahun</option>
                <option value="2026">2026</option>
                <option value="2025">2025</option>
            </select>
            <select id="fSemester" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-400">
                <option value="">Semua Semester</option>
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- ===== Ringkasan ===== -->
    <section class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-slate-500" id="jmlInfo">Menampilkan <b class="text-slate-800">0</b> dari <b class="text-slate-800">0</b> data</p>
        <p class="text-xs text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Setiap SKP memerlukan 2 file: <b>draft</b> (unggahan dosen) dan <b>final</b> (hasil verifikasi).</p>
    </section>

    <!-- ===== Tabel SKP ===== -->
    <section>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto" id="tabelWrap">
                <table class="pin-table">
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>File Draft</th>
                            <th>File Final</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbSkp">
                        <!-- Data dimuat dari AJAX -->
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

<!-- ===== Modal Detail / Review SKP ===== -->
<div class="modal-overlay" id="detailModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-bullseye mr-1 text-sky-500"></i>Detail &amp; Review SKP</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-detail-close>&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3.5">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700" id="dtInisial">--</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800" id="dtNama">—</p>
                        <p class="text-[11px] text-slate-500" id="dtNip">—</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="st-badge" id="dtStatus">—</span>
                    <span class="pr-badge bg-sky-50 text-sky-700" id="dtPeriode">—</span>
                </div>
            </div>

            <div>
                <p class="mb-2 text-[11px] font-bold text-slate-600 uppercase tracking-wide">File dalam SKP ini</p>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-3">
                        <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold text-amber-700"><i class="fas fa-pen-to-square"></i>File Draft <span class="font-normal text-amber-500">— unggahan dosen</span></p>
                        <div id="dtDraft"></div>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3">
                        <p class="mb-2 flex items-center gap-1.5 text-[11px] font-bold text-emerald-700"><i class="fas fa-file-circle-check"></i>File Final <span class="font-normal text-emerald-500">— hasil verifikasi</span></p>
                        <div id="dtFinal"></div>
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-1 text-[11px] font-bold text-slate-600 uppercase tracking-wide">Catatan Verifikasi</p>
                <p class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600" id="dtKet">—</p>
            </div>

            <div id="dtActions" class="flex flex-wrap items-center justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" class="btn-final-lg rounded-lg bg-emerald-500 hover:bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition"><i class="fas fa-file-arrow-up mr-1"></i>Unggah File Final</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal Unggah File Final ===== -->
<div class="modal-overlay" id="finalModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-file-circle-check mr-1 text-emerald-500"></i>Unggah File Final</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-final-close>&times;</button>
        </div>
        <form id="frmFinal" class="p-5 space-y-3">
            <input type="hidden" id="fpId" name="id">
            <p class="text-xs text-slate-500">Memverifikasi SKP: <b id="fpNama" class="text-slate-700">—</b></p>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">File Final (hasil verifikasi) <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-2.5">
                    <i class="fas fa-file-upload text-emerald-500"></i>
                    <span class="text-xs text-slate-500" id="fpFileLabel">Belum ada file dipilih</span>
                    <label class="ml-auto cursor-pointer rounded-lg bg-emerald-500 px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-emerald-600">
                        Pilih File
                        <input type="file" id="fpFile" name="file_final" accept=".pdf,.doc,.docx" class="hidden">
                    </label>
                </div></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Catatan Verifikasi</label>
                <textarea id="fpKet" rows="2" placeholder="mis. SKP disetujui, capaian sesuai target." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-400 focus:bg-white"></textarea></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-final-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition" data-text="Simpan Final"><i class="fas fa-check mr-1"></i>Simpan Final</button>
            </div>
        </form>
    </div>
</div>

<div id="toastSkp"></div>

<script>
(function () {
    var PROSES_URL = 'pages/jurusan/skp/proses.php';
    var tb = document.getElementById('tbSkp');
    var skp = [];
    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var fSemester = document.getElementById('fSemester');
    var activeTab = 'menunggu';
    var page = 1, PER = 6;

    function toast(msg, type) {
        var t = document.getElementById('toastSkp');
        var ico = type === 'error' ? 'fa-exclamation-circle text-rose-400' : 'fa-check-circle text-emerald-400';
        t.innerHTML = '<i class="fas ' + ico + '"></i> ' + msg;
        t.classList.add('show');
        clearTimeout(t._tm);
        t._tm = setTimeout(function () { t.classList.remove('show'); }, 2600);
    }

    function setButtonLoading(btn, isLoading) {
        if (isLoading) {
            btn.classList.add('is-loading');
            btn.setAttribute('disabled', '');
            btn.innerHTML = '<span class="spinner-slate"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
        } else {
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
            btn.innerHTML = (btn.getAttribute('data-text') || btn.textContent);
        }
    }

    function muatUlangTabel() {
        tb.innerHTML = '<tr><td colspan="6" class="py-12 text-center"><div class="spinner-slate mx-auto mb-2"></div><p class="text-xs text-slate-500">Memuat data SKP...</p></td></tr>';

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-tabel-skp': 1 },
            dataType: 'json',
            success: function (respon) {
                if (respon.status) {
                    tb.innerHTML = respon.data;
                    skp = Array.prototype.slice.call(document.querySelectorAll('#tbSkp tr:not(.empty-row)'));
                    recount();
                    render();
                } else {
                    tb.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-rose-500">' + respon.pesan + '</td></tr>';
                }
            },
            error: function () {
                tb.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-rose-500">Terjadi kesalahan pada server</td></tr>';
            }
        });
    }

    function miniChip(nama, label, url) {
        var ext = (nama.split('.').pop() || 'pdf').toLowerCase();
        var cls = ext === 'doc' || ext === 'docx' ? 'f-word' : 'f-pdf';
        var ico = ext === 'doc' || ext === 'docx' ? 'fa-file-word' : 'fa-file-pdf';
        var link = url ? '<a href="' + url + '" target="_blank" class="f-down" title="Unduh ' + nama + '"><i class="fas fa-download"></i></a>' : '';
        return '<span class="file-chip"><span class="f-ico ' + cls + '"><i class="fas ' + ico + '"></i></span>' +
            '<span class="f-meta"><span class="f-name">' + nama + '</span><span class="f-sub">' + label + '</span></span>' +
            link + '</span>';
    }

    function chipHTML(f, sub, url) {
        return f ? miniChip(f, sub, url) : '<span class="empty-dash"><i class="fas fa-minus"></i>Belum ada</span>';
    }

    function recount() {
        var m = 0, s = 0;
        skp.forEach(function (tr) {
            if (tr.getAttribute('data-status') === 'selesai') s++; else m++;
        });
        document.getElementById('cnt-semua').textContent = skp.length;
        document.getElementById('cnt-menunggu').textContent = m;
        document.getElementById('cnt-selesai').textContent = s;
    }

    function visible() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var th = fTahun.value;
        var sem = fSemester.value;
        return skp.filter(function (tr) {
            if (activeTab !== 'semua' && tr.getAttribute('data-status') !== activeTab) return false;
            if (th && tr.getAttribute('data-tahun') !== th) return false;
            if (sem && tr.getAttribute('data-semester') !== sem) return false;
            var cariText = tr.getAttribute('data-cari') || '';
            return !kata || cariText.indexOf(kata) !== -1;
        });
    }

    function render() {
        var v = visible();
        var pages = Math.max(1, Math.ceil(v.length / PER));
        if (page > pages) page = pages;
        var start = (page - 1) * PER;
        var slice = v.slice(start, start + PER);
        skp.forEach(function (tr) { tr.style.display = 'none'; });
        slice.forEach(function (tr) { tr.style.display = ''; });
        document.getElementById('jmlInfo').innerHTML = 'Menampilkan <b class="text-slate-800">' + slice.length + '</b> dari <b class="text-slate-800">' + v.length + '</b> data';
        
        var pgContainer = document.querySelector('.border-t.border-slate-100');
        if (v.length === 0) {
            if (pgContainer) pgContainer.style.display = 'none';
        } else {
            if (pgContainer) pgContainer.style.display = 'flex';
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
    fTahun.addEventListener('change', function () { page = 1; render(); });
    fSemester.addEventListener('change', function () { page = 1; render(); });
    document.getElementById('btnReset').addEventListener('click', function () {
        fCari.value = '';
        fTahun.value = '';
        fSemester.value = '';
        page = 1;
        render();
    });

    /* ===== Modal helpers ===== */
    function bindClose(m, attr) {
        m.querySelectorAll('[' + attr + ']').forEach(function (b) {
            b.addEventListener('click', function () { m.classList.remove('show'); });
        });
        m.addEventListener('click', function (e) { if (e.target === m) m.classList.remove('show'); });
    }
    var dm = document.getElementById('detailModal');
    var fm = document.getElementById('finalModal');
    bindClose(dm, 'data-detail-close');
    bindClose(fm, 'data-final-close');

    var fpFile = document.getElementById('fpFile');
    fpFile.addEventListener('change', function () {
        document.getElementById('fpFileLabel').textContent = fpFile.files.length ? fpFile.files[0].name : 'Belum ada file dipilih';
    });

    /* ===== Aksi ===== */
    var finalTarget = null;
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle, .btn-final-lg');
        if (!btn) return;
        var src = btn.closest('tr');
        if (!src) return;
        var nama = src.getAttribute('data-nama');
        var id = src.getAttribute('data-id');
        
        if (btn.classList.contains('btn-final') || btn.classList.contains('btn-final-lg') || btn.classList.contains('btn-edit')) {
            finalTarget = src;
            document.getElementById('fpId').value = id;
            document.getElementById('fpNama').textContent = nama;
            document.getElementById('fpFileLabel').textContent = 'Belum ada file dipilih';
            document.getElementById('fpKet').value = '';
            fpFile.value = '';
            fm.classList.add('show');
            return;
        }
        if (btn.classList.contains('btn-hapus')) {
            if (confirm('Hapus data SKP "' + nama + '"?')) {
                $.ajax({
                    url: PROSES_URL,
                    method: 'POST',
                    data: { 'hapus-data-skp': 1, 'id': id },
                    dataType: 'json',
                    success: function (respon) {
                        toast(respon.pesan, respon.status ? 'success' : 'error');
                        if (respon.status) {
                            muatUlangTabel();
                        }
                    },
                    error: function () {
                        toast('Terjadi kesalahan pada server', 'error');
                    }
                });
            }
            return;
        }
        if (btn.classList.contains('btn-detail')) {
            var st = src.getAttribute('data-status');
            document.getElementById('dtNama').textContent = nama;
            document.getElementById('dtNip').textContent = 'NIP ' + src.getAttribute('data-nip');
            document.getElementById('dtInisial').textContent = src.querySelector('.bg-sky-100').textContent;
            document.getElementById('dtPeriode').innerHTML = src.children[1].innerHTML;
            document.getElementById('dtStatus').innerHTML = src.children[2].innerHTML;
            
            var draftFile = src.getAttribute('data-draft');
            var draftUrl = src.getAttribute('data-draft-url');
            document.getElementById('dtDraft').innerHTML = chipHTML(draftFile, draftFile + ' · ' + src.getAttribute('data-hari'), draftUrl);
            
            var fin = src.getAttribute('data-final') || '';
            var finalUrl = src.getAttribute('data-final-url');
            document.getElementById('dtFinal').innerHTML = chipHTML(fin, fin ? fin + ' · final' : '', finalUrl);
            
            document.getElementById('dtKet').textContent = src.getAttribute('data-ket') || '—';
            document.getElementById('dtActions').style.display = (st === 'selesai') ? 'none' : 'flex';
            dm.classList.add('show');
        }
    });

    document.getElementById('frmFinal').addEventListener('submit', function (e) {
        e.preventDefault();
        var btnSimpan = this.querySelector('button[type="submit"]');
        setButtonLoading(btnSimpan, true);

        var data = new FormData(this);
        data.append('unggah-final-skp', 1);

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: data,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (respon) {
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if (respon.status) {
                    fm.classList.remove('show');
                    dm.classList.remove('show');
                    muatUlangTabel();
                }
            },
            error: function () {
                toast('Terjadi kesalahan pada server', 'error');
            },
            complete: function () {
                setButtonLoading(btnSimpan, false);
            }
        });
    });

    // Panggilan awal muat data
    muatUlangTabel();
})();
</script>
