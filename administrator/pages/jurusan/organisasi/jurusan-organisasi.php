<?php
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// ================= AMBIL DATA PEJABAT JURUSAN & DAFTAR DOSEN =================
$qJurusan = query("SELECT j.*, d1.nama AS nama_kajur, d1.nip AS nip_kajur, d2.nama AS nama_sekjur, d2.nip AS nip_sekjur, d3.nama AS nama_kalab, d3.nip AS nip_kalab, d4.nama AS nama_penjamu, d4.nip AS nip_penjamu, d5.nama AS nama_pengelolah, d5.nip AS nip_pengelolah FROM jurusan j LEFT JOIN dosen d1 ON j.kajur = d1.id LEFT JOIN dosen d2 ON j.sekjur = d2.id LEFT JOIN dosen d3 ON j.kalab = d3.id LEFT JOIN dosen d4 ON j.penjamu = d4.id LEFT JOIN dosen d5 ON j.pengelolah_jurnal = d5.id LIMIT 1");
$rowJurusan = mysqli_fetch_assoc($qJurusan);

$pejabatMap = [
    ['id' => 'kajur', 'jabatan' => 'Ketua Jurusan', 'icon' => 'fa-crown', 'color' => 'orange'],
    ['id' => 'sekjur', 'jabatan' => 'Sekretaris Jurusan', 'icon' => 'fa-pen-nib', 'color' => 'sky'],
    ['id' => 'kalab', 'jabatan' => 'Kepala Laboratorium', 'icon' => 'fa-flask', 'color' => 'violet'],
    ['id' => 'penjamu', 'jabatan' => 'Penjamin Mutu', 'icon' => 'fa-shield-halved', 'color' => 'emerald'],
    ['id' => 'pengelolah_jurnal', 'jabatan' => 'Ketua Pengelola Jurnal', 'icon' => 'fa-book-open', 'color' => 'indigo'],
];
$pejabatList = [];
foreach($pejabatMap as $p) {
    $nipKey = 'nip_' . $p['id'];
    $namaKey = 'nama_' . $p['id'];
    if ($p['id'] === 'pengelolah_jurnal') {
        $nipKey = 'nip_pengelolah';
        $namaKey = 'nama_pengelolah';
    }
    $pejabatList[] = [
        'id' => $p['id'],
        'jabatan' => $p['jabatan'],
        'icon' => $p['icon'],
        'color' => $p['color'],
        'nip' => $rowJurusan[$nipKey] ?? '',
        'nama' => $rowJurusan[$namaKey] ?? '',
        'dosen_id' => $rowJurusan[$p['id']] ?? '',
    ];
}

$dosenList = [];
$qDosen = query("SELECT id, nip, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
while($d = mysqli_fetch_assoc($qDosen)){ $dosenList[] = $d; }
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
    .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    /* ===== Tombol bulat + tooltip ===== */
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Baris jabatan (konsep daftar, responsif murni) ===== */
    .org-grid { display: grid; grid-template-columns: 1fr; gap: .8rem; }
    @media (min-width: 900px) { .org-grid { grid-template-columns: 1fr 1fr; } }
    .org-row { display: flex; align-items: center; gap: .85rem; border: 1px solid #e2e8f0; background: #fff; border-radius: 14px; padding: .85rem .95rem; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; cursor: pointer; }
    .org-row:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -12px rgba(15,23,42,.22); border-color: #c7d2fe; }
    .org-row--on { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.35); }
    .org-avatar { display: inline-flex; align-items: center; justify-content: center; width: 2.8rem; height: 2.8rem; border-radius: 14px; font-weight: 800; font-size: .85rem; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(15,23,42,.06); }
    .org-jab { font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; display: flex; align-items: center; gap: .3rem; margin-bottom: 2px; }
    .org-nama { font-size: .85rem; font-weight: 700; color: #1e293b; line-height: 1.3; }

    /* ===== Kartu prodi ===== */
    .prodi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    .prodi-card { position: relative; overflow: hidden; border-radius: 16px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,.05); transition: transform .2s ease, box-shadow .2s ease; }
    .prodi-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -16px rgba(15,23,42,.25); }
    .prodi-card::after { content: ""; position: absolute; inset: 0; border-radius: 16px; box-shadow: inset 0 0 0 1px var(--pr); opacity: 0; transition: opacity .2s ease; pointer-events: none; }
    .prodi-card:hover::after { opacity: .6; }
    .prodi-banner { height: 74px; background: linear-gradient(135deg, var(--b1), var(--b2)); position: relative; }
    .prodi-banner .shape { position: absolute; border-radius: 9999px; background: rgba(255,255,255,.14); }
    .prodi-badge { position: absolute; top: .7rem; right: .7rem; display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; }
    .prodi-avatar { position: absolute; left: 1.1rem; top: 3.1rem; display: inline-flex; align-items: center; justify-content: center; width: 2.9rem; height: 2.9rem; border-radius: 14px; font-weight: 800; font-size: .85rem; box-shadow: 0 4px 10px -2px rgba(15,23,42,.35); border: 3px solid #fff; }
    .badge-pro { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }

    /* ===== Modal & empty ===== */
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 90; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.show { display: flex; }
    .empty-hidden { display: none; }
    .empty-show { display: flex; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Organisasi</h1>
                    <p class="text-xs text-slate-500">Struktur kepemimpinan jurusan &amp; program studi — klik baris untuk mengganti pejabat.</p>
                </div>
            </div>
            <button type="button" id="btnTambah" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tambah Prodi</span>
            </button>
        </div>
    </section>

    <!-- ===== Pejabat Struktural ===== -->
    <section class="mb-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-user-tie mr-1.5 text-orange-500"></i>Pejabat Struktural</h2>
            <p class="text-xs font-medium text-slate-500"><i class="fas fa-id-badge mr-1 text-orange-400"></i><span class="font-bold text-slate-700">7</span> jabatan</p>
        </div>
        <div class="org-grid" id="orgList">
            <?php foreach($pejabatList as $p): $ini = $p['nama'] !== '' ? $p : null; ?>
            <div class="org-row reveal" data-id="<?= tampil($p['id']) ?>" data-cari="<?= strtolower(($ini ? $ini['nama'] : '') . ' ' . $p['jabatan']) ?>" data-jabatan="<?= tampil($p['jabatan']) ?>" data-nama="<?= tampil($ini['nama'] ?? '') ?>" data-nip="<?= tampil($ini['nip'] ?? '') ?>">
                <span class="org-avatar bg-gradient-to-br from-<?= $p['color'] ?>-500 to-<?= $p['color'] ?>-400 text-white"><?= $ini ? strtoupper(substr($ini['nama'], 0, 2)) : '—' ?></span>
                <div class="min-w-0 flex-1">
                    <p class="org-jab text-<?= $p['color'] ?>-500"><i class="fas fa-<?= $p['icon'] ?>"></i><?= tampil($p['jabatan']) ?></p>
                    <p class="org-nama"><?= tampil($ini['nama'] ?? 'Belum diisi') ?></p>
                </div>
                <span class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Ubah Pejabat</span></span>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Klik baris jabatan atau ikon pensil untuk mengganti pejabat yang menjabat.</p>
    </section>

    <!-- ===== Program Studi ===== -->
    <section class="mb-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-building-columns mr-1.5 text-orange-500"></i>Program Studi</h2>
            <p class="text-xs font-medium text-slate-500"><i class="fas fa-graduation-cap mr-1 text-orange-400"></i><span class="font-bold text-slate-700">3</span> prodi aktif</p>
        </div>
        <div class="prodi-grid" id="prodiGrid">
            <!-- Diisi via AJAX dari proses.php -->
        </div>
        <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Kaprodi setiap prodi dikelola lewat ikon pensil pada kartu.</p>
    </section>
</main>

<!-- ===== Modal Tambah Prodi ===== -->
<div class="modal-overlay" id="tambahModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-plus mr-1 text-orange-500"></i>Tambah Program Studi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-tambah-close>&times;</button>
        </div>
        <form id="frmTambah" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Program Studi</label>
                <input type="text" id="tpNama" required placeholder="mis. S1 Teknik Informatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                <input type="text" id="tpKode" required placeholder="mis. s1teknikinformatika" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kaprodi</label>
                    <select id="tpKaprodi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="">-- Pilih Dosen --</option>
                        <?php foreach($dosenList as $d): ?>
                        <option value="<?= tampil($d['nip'] ?: $d['id']) ?>"><?= tampil($d['nama']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenjang</label>
                    <select id="tpJenjang" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1</option><option>D3</option><option>D4</option><option>Profesi</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                <select id="tpStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-tambah-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Edit Prodi ===== -->
<div class="modal-overlay" id="editModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-pen mr-1 text-sky-500"></i>Edit Program Studi</h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-edit-close>&times;</button>
        </div>
        <form id="frmEdit" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Program Studi</label>
                <input type="text" id="epNama" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode</label>
                <input type="hidden" id="epKodeLama">
                <input type="text" id="epKode" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Kaprodi</label>
                    <select id="epKaprodi" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option value="">-- Pilih Dosen --</option>
                        <?php foreach($dosenList as $d): ?>
                        <option value="<?= tampil($d['nip'] ?: $d['id']) ?>"><?= tampil($d['nama']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenjang</label>
                    <select id="epJenjang" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                        <option>S1</option><option>D3</option><option>D4</option><option>Profesi</option>
                    </select></div>
            </div>
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                <select id="epStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-edit-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal Ubah Pejabat ===== -->
<div class="modal-overlay" id="pengurusModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200">
            <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-user-tie mr-1 text-sky-500"></i>Ubah Pejabat <span id="pengJabatan" class="text-slate-400 font-normal"></span></h6>
            <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-peng-close>&times;</button>
        </div>
        <form id="frmPengurus" class="p-5 space-y-3">
            <div><label class="block text-[11px] font-semibold text-slate-500 mb-1">Pilih Dosen</label>
                <select id="pengDosen" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach($dosenList as $d): ?>
                    <option value="<?= tampil($d['id']) ?>"><?= tampil($d['nama']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="flex justify-end gap-2 pt-1 border-t border-slate-100">
                <button type="button" data-peng-close class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium transition"><i class="fas fa-save mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/jurusan/organisasi/proses.php';
    
    function toast(pesan, tipe) {
        if (typeof window.toast === 'function') {
            window.toast(pesan, tipe);
            return;
        }
        var t = document.createElement('div');
        t.textContent = pesan;
        var bg = tipe === 'success' ? '#10b981' : (tipe === 'error' ? '#ef4444' : '#3b82f6');
        t.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;background:' + bg + ';color:#fff;padding:.6rem 1rem;border-radius:.6rem;font-size:.8rem;box-shadow:0 6px 18px rgba(15,23,42,.35);transition:opacity .3s ease;';
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 300); }, 2400);
    }
    
    function setButtonLoading(btn, isLoading) {
        if (isLoading) {
            btn.classList.add('is-loading');
            btn.setAttribute('disabled', '');
            btn.innerHTML = '<span class="spinner-slate" style="border-top-color:#fff"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
        } else {
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
            btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
        }
    }

    /* ===== Modal helpers ===== */
    function bindClose(modal, attr) {
        modal.querySelectorAll('[' + attr + ']').forEach(function (b) {
            b.addEventListener('click', function () { modal.classList.remove('show'); });
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });
    }
    var tm = document.getElementById('tambahModal');
    var em = document.getElementById('editModal');
    bindClose(tm, 'data-tambah-close');
    bindClose(em, 'data-edit-close');

    var btnTambah = document.getElementById('btnTambah');
    if (btnTambah) btnTambah.addEventListener('click', function () {
        document.getElementById('frmTambah').reset();
        tm.classList.add('show');
        document.getElementById('tpNama').focus();
    });

    /* ===== Baris jabatan: klik → ubah pejabat ===== */
    var pengTarget = null;
    var pm = document.getElementById('pengurusModal');

    function openPeng(row) {
        if (pengTarget) pengTarget.classList.remove('org-row--on');
        pengTarget = row;
        row.classList.add('org-row--on');
        var jabatan = row.getAttribute('data-jabatan');
        var nama = row.getAttribute('data-nama');
        document.getElementById('pengJabatan').textContent = '— ' + jabatan;
        var sel = document.getElementById('pengDosen');
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].textContent.trim().indexOf(nama) !== -1) { sel.selectedIndex = i; break; }
        }
        pm.classList.add('show');
    }

    document.getElementById('orgList').addEventListener('click', function (e) {
        var row = e.target.closest('.org-row');
        if (!row) return;
        openPeng(row);
    });

    var frmPengurus = document.getElementById('frmPengurus');
    if (frmPengurus) frmPengurus.addEventListener('submit', function (e) {
        e.preventDefault();
        var sel = document.getElementById('pengDosen');
        var dosen_id = sel.options[sel.selectedIndex].value;
        var nama = sel.options[sel.selectedIndex].text;
        var nip = pengTarget ? pengTarget.getAttribute('data-nip') : ''; // Simpan nip lama jika ada (kita tidak pass NIP dari option lagi)
        var jabatan = '';
        if (pengTarget) jabatan = pengTarget.getAttribute('data-jabatan');
        
        var btnSimpan = frmPengurus.querySelector('button[type="submit"]');
        if (!btnSimpan.hasAttribute('data-text')) btnSimpan.setAttribute('data-text', btnSimpan.innerHTML);
        setButtonLoading(btnSimpan, true);

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: {
                'edit-pejabat-jurusan': 1,
                jabatan: jabatan,
                dosen_id: dosen_id
            },
            dataType: 'json',
            success: function(respon) {
                if(respon.status) {
                    toast(respon.pesan, 'success');
                    if (pengTarget) {
                        pengTarget.setAttribute('data-nama', nama);
                        pengTarget.querySelector('.org-nama').textContent = nama;
                        // NIP biarkan text lama karena dosenList tidak render NIP di option lagi
                        pengTarget.querySelector('.org-avatar').textContent = nama.split(' ').map(function (w) { return w.charAt(0); }).slice(0, 2).join('').toUpperCase();
                        pengTarget.classList.remove('org-row--on');
                    }
                    pm.classList.remove('show');
                } else {
                    toast(respon.pesan || 'Gagal mengubah pejabat', 'error');
                }
            },
            error: function() { toast('Terjadi kesalahan pada server', 'error'); },
            complete: function() { setButtonLoading(btnSimpan, false); }
        });
    });

    pm.querySelectorAll('[data-peng-close]').forEach(function (b) {
        b.addEventListener('click', function () { pm.classList.remove('show'); });
    });
    pm.addEventListener('click', function (e) { if (e.target === pm) pm.classList.remove('show'); });

    /* ===== Aksi kartu prodi ===== */
    var editTarget = null;
    
    function muatUlangProdi() {
        var grid = document.getElementById('prodiGrid');
        grid.innerHTML = '<div class="col-span-full py-8 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-2 text-xs text-slate-500">Memuat data program studi...</p></div>';
        
        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'ambil-tabel-prodi': 1 },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    grid.innerHTML = respon.data;
                } else {
                    grid.innerHTML = '<div class="col-span-full py-8 text-center text-rose-500 text-sm">Gagal memuat program studi</div>';
                }
            },
            error: function() {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-rose-500 text-sm">Terjadi kesalahan pada server</div>';
            }
        });
    }

    document.getElementById('prodiGrid').addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-circle');
        if (!btn) return;
        var card = btn.closest('.prodi-card');
        var nama = card.querySelector('h3').textContent.trim();
        var kode = card.querySelector('code').textContent.trim();
        
        if (btn.classList.contains('btn-aksi')) {
            toast('Detail program studi "' + nama + '"', 'info');
        } else if (btn.classList.contains('btn-edit')) {
            editTarget = card;
            var kaprodi = card.querySelector('.truncate').textContent.trim();
            var jenjang = card.querySelector('.badge-pro.bg-sky-100').textContent.trim();
            var status = card.querySelector('.prodi-banner .badge-pro').textContent.trim();
            
            document.getElementById('epKodeLama').value = kode;
            document.getElementById('epNama').value = nama;
            document.getElementById('epKode').value = kode;
            document.getElementById('epJenjang').value = jenjang;
            var kapSel = document.getElementById('epKaprodi');
            for (var i = 0; i < kapSel.options.length; i++) {
                if (kapSel.options[i].textContent.trim().indexOf(kaprodi) !== -1 || kapSel.options[i].value === kaprodi) {
                    kapSel.selectedIndex = i; 
                    break; 
                }
            }
            var stSel = document.getElementById('epStatus');
            for (var j = 0; j < stSel.options.length; j++) {
                if (stSel.options[j].value.toLowerCase() === status.toLowerCase()) { stSel.selectedIndex = j; break; }
            }
            em.classList.add('show');
        } else if (btn.classList.contains('btn-hapus')) {
            if (typeof window.konfirmasiHapus === 'function') {
                window.konfirmasiHapus('program studi "' + nama + '"', function() {
                    $.ajax({
                        url: PROSES_URL,
                        method: 'POST',
                        data: {
                            'hapus-data-prodi': 1,
                            kode: kode
                        },
                        dataType: 'json',
                        success: function(respon) {
                            toast(respon.pesan, respon.status ? 'success' : 'error');
                            if (respon.status) {
                                muatUlangProdi();
                            }
                        },
                        error: function() { toast('Terjadi kesalahan pada server', 'error'); }
                    });
                });
            } else {
                if (confirm('Hapus program studi "' + nama + '"?')) {
                    $.ajax({
                        url: PROSES_URL,
                        method: 'POST',
                        data: {
                            'hapus-data-prodi': 1,
                            kode: kode
                        },
                        dataType: 'json',
                        success: function(respon) {
                            toast(respon.pesan, respon.status ? 'success' : 'error');
                            if (respon.status) {
                                muatUlangProdi();
                            }
                        },
                        error: function() { toast('Terjadi kesalahan pada server', 'error'); }
                    });
                }
            }
        }
    });

    /* ===== Submit Tambah ===== */
    var frmTambah = document.getElementById('frmTambah');
    if (frmTambah) frmTambah.addEventListener('submit', function (e) {
        e.preventDefault();
        var btnSimpan = frmTambah.querySelector('button[type="submit"]');
        if (!btnSimpan.hasAttribute('data-text')) btnSimpan.setAttribute('data-text', btnSimpan.innerHTML);
        setButtonLoading(btnSimpan, true);

        var nama = document.getElementById('tpNama').value.trim();
        var kode = document.getElementById('tpKode').value.trim();
        var kaprodi = document.getElementById('tpKaprodi').value;
        var jenjang = document.getElementById('tpJenjang').value;
        var status = document.getElementById('tpStatus').value;

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: {
                'tambah-data-prodi': 1,
                nama: nama,
                kode: kode,
                kaprodi: kaprodi,
                jenjang: jenjang,
                status: status
            },
            dataType: 'json',
            success: function(respon) {
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if (respon.status) {
                    tm.classList.remove('show');
                    frmTambah.reset();
                    muatUlangProdi();
                }
            },
            error: function() { toast('Terjadi kesalahan pada server', 'error'); },
            complete: function() { setButtonLoading(btnSimpan, false); }
        });
    });

    /* ===== Submit Edit ===== */
    var frmEdit = document.getElementById('frmEdit');
    if (frmEdit) frmEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        var btnSimpan = frmEdit.querySelector('button[type="submit"]');
        if (!btnSimpan.hasAttribute('data-text')) btnSimpan.setAttribute('data-text', btnSimpan.innerHTML);
        setButtonLoading(btnSimpan, true);

        var kode_lama = document.getElementById('epKodeLama').value.trim();
        var nama = document.getElementById('epNama').value.trim();
        var kode = document.getElementById('epKode').value.trim();
        var kaprodi = document.getElementById('epKaprodi').value;
        var jenjang = document.getElementById('epJenjang').value;
        var status = document.getElementById('epStatus').value;

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: {
                'edit-data-prodi': 1,
                kode_lama: kode_lama,
                nama: nama,
                kode: kode,
                kaprodi: kaprodi,
                jenjang: jenjang,
                status: status
            },
            dataType: 'json',
            success: function(respon) {
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if (respon.status) {
                    em.classList.remove('show');
                    muatUlangProdi();
                }
            },
            error: function() { toast('Terjadi kesalahan pada server', 'error'); },
            complete: function() { setButtonLoading(btnSimpan, false); }
        });
    });
    /* ===== Load Awal ===== */
    document.addEventListener('DOMContentLoaded', function() {
        muatUlangProdi();
    });
})();
</script>