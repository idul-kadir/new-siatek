<?php
require_once __DIR__ . "/../../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Pastikan pengguna sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// 2. Pembatasan Hak Akses: Hanya Level 1, 5, 6, dan 9
$userLevel = (int)($_SESSION['level'] ?? 0);
if (!in_array($userLevel, [1, 5, 6, 9], true)) {
    http_response_code(403);
    ?>
    <main class="content-area p-6">
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-6 text-center text-rose-800 shadow-sm">
            <i class="fas fa-ban text-3xl mb-2 text-rose-500"></i>
            <h2 class="text-base font-bold">Akses Ditolak</h2>
            <p class="text-xs text-rose-600 mt-1">Menu Bimbingan Penasihat Akademik (PA) hanya dapat diakses oleh Dosen PA, Ketua Program Studi, dan Pengelola Jurusan.</p>
        </div>
    </main>
    <?php
    return;
}

// Data identitas pengguna
$idDosenSession  = bersihkan((string)($_SESSION['id'] ?? ''));
$nipDosenSession = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
$isKaprodi       = ($userLevel === 5);
$isJurusan       = ($userLevel === 6);
$isSuperAdmin    = ($userLevel === 9);
$isDosenMurni    = ($userLevel === 1);
$userProdi       = $isKaprodi ? (function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''))) : '';

// Ambil daftar prodi untuk filter
$daftarProdi = [];
$qP = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
if ($qP) {
    while ($rp = mysqli_fetch_assoc($qP)) {
        $daftarProdi[] = $rp;
    }
}

// Ambil daftar angkatan untuk filter
$daftarAngkatan = [];
$qAng = query("SELECT DISTINCT angkatan FROM mahasiswa WHERE angkatan IS NOT NULL AND angkatan != '' AND angkatan != '0000' ORDER BY angkatan DESC");
if ($qAng) {
    while ($ra = mysqli_fetch_assoc($qAng)) {
        $daftarAngkatan[] = $ra['angkatan'];
    }
}
?>
<style>
  .btn-circle {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    transition: all .15s ease;
  }
  .btn-circle:hover {
    transform: translateY(-1px);
  }
  .btn-circle .tip {
    position: absolute;
    top: 115%;
    left: 50%;
    transform: translateX(-50%);
    background: #0f172a;
    color: #fff;
    font-size: 11px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity .15s ease;
    z-index: 40;
    pointer-events: none;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .25);
  }
  .btn-circle:hover .tip {
    opacity: 1;
    visibility: visible;
  }
  .pg-btn {
    min-width: 2rem;
    height: 2rem;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
  }
  .pg-btn:hover {
    border-color: #fdba74;
    color: #c2410c;
  }
  .pg-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
  }
  .pg-btn.on {
    background: #1e3a5f;
    border-color: #1e3a5f;
    color: #fff;
  }
  /* Drawer Offcanvas Riwayat Buku PA */
  #drawerRiwayatBackdrop {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s;
  }
  #drawerRiwayatBackdrop.active {
    opacity: 1;
    visibility: visible;
  }
  #drawerRiwayatPanel {
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  #drawerRiwayatPanel.active {
    transform: translateX(0);
  }
  /* Status Card Options */
  .status-radio-card {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1.5px solid #e2e8f0;
  }
  .status-radio-card:hover {
    border-color: #cbd5e1;
    background-color: #f8fafc;
  }
  .status-radio-card.active {
    border-color: #f97316;
    background-color: #fff7ed;
  }
  .content-scroll {
    overflow-y: auto;
    min-height: 0;
  }
  /* Rich Text Editor & Catatan Content Styling */
  .rich-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    color: #475569;
    background: transparent;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .rich-btn:hover {
    background-color: #e2e8f0;
    color: #0f172a;
  }
  .rich-btn:active {
    background-color: #cbd5e1;
  }
  .rich-editor:focus {
    outline: none;
  }
  .rich-editor:empty:before {
    content: attr(placeholder);
    color: #94a3b8;
    pointer-events: none;
    display: block;
  }
  .rich-editor ul, .catatan-content ul {
    list-style-type: disc !important;
    margin-left: 1.25rem !important;
    margin-top: 0.35rem !important;
    margin-bottom: 0.35rem !important;
  }
  .rich-editor ol, .catatan-content ol {
    list-style-type: decimal !important;
    margin-left: 1.25rem !important;
    margin-top: 0.35rem !important;
    margin-bottom: 0.35rem !important;
  }
  .rich-editor li, .catatan-content li {
    display: list-item !important;
    margin-bottom: 0.2rem !important;
  }
  .rich-editor b, .rich-editor strong, .catatan-content b, .catatan-content strong {
    font-weight: 700 !important;
  }
  .rich-editor i, .rich-editor em, .catatan-content i, .catatan-content em {
    font-style: italic !important;
  }
  .rich-editor u, .catatan-content u {
    text-decoration: underline !important;
  }
  .rich-editor p, .catatan-content p {
    margin-bottom: 0.35rem !important;
  }
  .rich-editor p:last-child, .catatan-content p:last-child {
    margin-bottom: 0 !important;
  }
  /* Toast Notification */
  #toastContainer {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    pointer-events: none;
  }
  .toast-item {
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
    transform: translateX(120%);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
    max-width: 380px;
  }
  .toast-item.show {
    transform: translateX(0);
    opacity: 1;
  }
  .toast-item.hide {
    transform: translateX(120%);
    opacity: 0;
  }
  .toast-success { background: linear-gradient(135deg, #059669, #10b981); }
  .toast-error   { background: linear-gradient(135deg, #dc2626, #ef4444); }
  .toast-info    { background: linear-gradient(135deg, #0284c7, #0ea5e9); }
  .toast-warning { background: linear-gradient(135deg, #d97706, #f59e0b); }
  .toast-icon { font-size: 1rem; flex-shrink: 0; }
  .toast-close {
    margin-left: auto;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.15s;
    background: none;
    border: none;
    color: #fff;
    font-size: 1rem;
    padding: 0;
  }
  .toast-close:hover { opacity: 1; }
</style>

<main class="content-area content-scroll p-4 sm:p-6 space-y-6">

  <!-- ===== Page Header ===== -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 text-white shadow-md shadow-orange-500/20">
        <i class="fas fa-user-graduate text-xl"></i>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl font-bold text-slate-800">Bimbingan Penasihat Akademik (PA)</h1>
          <?php if ($isKaprodi): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
              Kaprodi <?= tampil($userProdi) ?>
            </span>
          <?php elseif ($isJurusan): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
              Pimpinan Jurusan
            </span>
          <?php elseif ($isSuperAdmin): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800">
              Super Admin
            </span>
          <?php else: ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-800">
              Dosen PA
            </span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Evaluasi studi semesteran, pengisian Buku PA & monitoring perkembangan mahasiswa</p>
      </div>
    </div>

    <!-- Quick Action / Switcher untuk Kaprodi & Jurusan -->
    <div class="flex items-center gap-2">
      <?php if ($userLevel !== 9): ?>
        <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-medium">
          <button type="button" id="btnModeSaya" onclick="gantiModePembimbing('saya')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all shadow-xs bg-white text-orange-600 font-bold">
            <i class="fas fa-user-tie"></i>Bimbingan Saya
          </button>
          <button type="button" id="btnModeSemua" onclick="gantiModePembimbing('semua')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900 font-semibold">
            <i class="fas fa-users"></i><?= $isKaprodi ? 'Semua Mahasiswa Prodi' : 'Semua Mahasiswa' ?>
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ===== Ringkasan Metrik KPI ===== -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1. Total Mahasiswa PA -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500">Total Mahasiswa PA</span>
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-xs">
          <i class="fas fa-users"></i>
        </span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <span class="text-2xl font-bold text-slate-800" id="statTotalMhs">0</span>
        <span class="text-xs text-slate-400">mahasiswa</span>
      </div>
      <p class="text-[11px] text-slate-400 mt-1">Dalam lingkup filter aktif</p>
    </div>

    <!-- 2. Terdaftar - Aktif -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500">Terdaftar - Aktif</span>
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 text-xs">
          <i class="fas fa-user-check"></i>
        </span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <span class="text-2xl font-bold text-emerald-600" id="statAktif">0</span>
        <span class="text-xs text-slate-400">mahasiswa</span>
      </div>
      <p class="text-[11px] text-slate-400 mt-1">Aktif berkegiatan kuliah</p>
    </div>

    <!-- 3. Cuti -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500">Sedang Cuti</span>
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 text-xs">
          <i class="fas fa-bed"></i>
        </span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <span class="text-2xl font-bold text-sky-600" id="statCuti">0</span>
        <span class="text-xs text-slate-400">mahasiswa</span>
      </div>
      <p class="text-[11px] text-slate-400 mt-1">Status cuti semester</p>
    </div>

    <!-- 4. Tidak Aktif / Perlu Perhatian -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500">Perlu Perhatian</span>
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 text-xs">
          <i class="fas fa-triangle-exclamation"></i>
        </span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <span class="text-2xl font-bold text-rose-600" id="statTidakAktif">0</span>
        <span class="text-xs text-slate-400">mahasiswa</span>
      </div>
      <p class="text-[11px] text-slate-400 mt-1">Tidak aktif / mengundurkan diri</p>
    </div>
  </div>

  <!-- ===== Filter Bar ===== -->
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
      <!-- Search NIM / Nama -->
      <div class="sm:col-span-2 lg:col-span-2 relative">
        <input type="search" id="fCari" placeholder="Cari nama atau NIM mahasiswa..."
               class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition" />
        <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
      </div>

      <!-- Filter Program Studi -->
      <div>
        <select id="fProdi" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition"
                <?= ($isKaprodi && !empty($userProdi)) ? 'disabled' : '' ?>>
          <option value="">-- Semua Prodi --</option>
          <?php foreach ($daftarProdi as $p): ?>
            <option value="<?= htmlspecialchars($p['kode']) ?>" <?= ($isKaprodi && ($userProdi === $p['kode'] || $userProdi === $p['nama'])) ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Angkatan -->
      <div>
        <select id="fAngkatan" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition">
          <option value="">-- Semua Angkatan --</option>
          <?php foreach ($daftarAngkatan as $ang): ?>
            <option value="<?= htmlspecialchars($ang) ?>" data-angkatan="<?= (int)$ang ?>">Angkatan <?= htmlspecialchars($ang) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Status -->
      <div>
        <select id="fStatus" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition">
          <option value="">-- Semua Status --</option>
          <option value="Terdaftar - Aktif">Terdaftar - Aktif</option>
          <option value="Terdaftar - Tidak Aktif">Terdaftar - Tidak Aktif</option>
          <option value="Cuti">Cuti</option>
          <option value="Tidak Aktif">Tidak Aktif</option>
          <option value="belum_isi">Belum Ada Isian PA</option>
        </select>
      </div>

      <!-- Reset Filter Button -->
      <div>
        <button type="button" id="btnReset"
          class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition">
          <i class="fas fa-times"></i>Reset
        </button>
      </div>
    </div>
  </div>

  <!-- ===== Tabel Mahasiswa Bimbingan PA ===== -->
  <div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="text-sm font-bold text-slate-800">Daftar Mahasiswa Bimbingan PA</h3>
        <p class="text-xs text-slate-400 mt-0.5">Klik tombol <strong>Isi Buku PA</strong> untuk mencatat perkembangan semesteran</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs text-slate-500" id="infoPaginasi">Memuat data...</span>
        <button type="button" onclick="muatDataPA(1)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 shadow-2xs transition">
          <i class="fas fa-rotate text-slate-500"></i>Segarkan
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-500">
            <th class="px-4 py-3 text-center w-12">No</th>
            <th class="px-4 py-3 min-w-[200px]">Mahasiswa</th>
            <th class="px-4 py-3 min-w-[150px]">Program Studi</th>
            <th class="px-4 py-3 min-w-[180px]">Dosen PA</th>
            <th class="px-4 py-3 min-w-[220px]">Evaluasi Terakhir</th>
            <th class="px-4 py-3 min-w-[170px]">Status Terkini</th>
            <th class="px-4 py-3 text-center w-28">Aksi</th>
          </tr>
        </thead>
        <tbody id="tabelBodyPA" class="divide-y divide-slate-100 text-xs">
          <tr>
            <td colspan="7" class="py-12 text-center text-slate-400">
              <i class="fas fa-spinner fa-spin text-2xl text-orange-500 mb-2"></i>
              <p>Memuat data bimbingan PA...</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
      <div>Menampilkan halaman <strong id="currPageSpan">1</strong></div>
      <div class="flex items-center gap-1.5" id="navPaginasi">
        <!-- Tombol paginasi akan dirender otomatis -->
      </div>
    </div>
  </div>

</main>

<!-- ===== MODAL FORM PENGISIAN BUKU PA ===== -->
<div id="modalIsianPA" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-fade-in">
    <!-- Modal Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white shadow-xs">
          <i class="fas fa-book-open"></i>
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-800" id="modalJudul">Pengisian Buku PA (Evaluasi Studi)</h3>
          <p class="text-xs text-slate-500" id="modalSubjudul">Formulir evaluasi semesteran mahasiswa</p>
        </div>
      </div>
      <button type="button" onclick="tutupModalIsianPA()" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 text-base transition">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-6 overflow-y-auto">
      <form id="formIsianPA" onsubmit="simpanFormIsianPA(event)" class="space-y-5">
        <div class="hidden">
          <input type="hidden" id="formIdIsian" name="id" value="0">
          <input type="hidden" id="formNim" name="nim" value="">
        </div>

        <!-- Ringkasan Mahasiswa -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
          <div>
            <div class="text-[11px] text-slate-400 uppercase font-semibold">Mahasiswa</div>
            <div class="text-sm font-bold text-slate-800" id="formNamaMhs">-</div>
            <div class="text-xs text-slate-500 font-mono mt-0.5" id="formNimMhs">-</div>
          </div>
          <div class="text-right">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800" id="formProdiMhs">-</span>
          </div>
        </div>

        <!-- Baris: Semester, IPS, IPK, SKS -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Semester <span class="text-rose-500">*</span></label>
            <input type="number" id="formSemester" name="semester" min="1" max="14" required
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="1-14">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">IPS</label>
            <input type="text" id="formIps" name="ips" step="0.01"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="e.g. 3.50">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">IPK</label>
            <input type="text" id="formIpk" name="ipk" step="0.01"
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="e.g. 3.45">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">SKS <span class="text-rose-500">*</span></label>
            <input type="number" id="formSks" name="sks" min="0" max="160" required
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none" placeholder="Total/Sem">
          </div>
        </div>

        <!-- Tanggal Konsultasi -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Konsultasi Bimbingan <span class="text-rose-500">*</span></label>
          <input type="date" id="formTanggal" name="tanggal" required
                 class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none">
        </div>

        <!-- 4 Opsi Status dengan Penjelasan Lengkap -->
        <div>
          <label class="block text-xs font-bold text-slate-800 mb-2">
            Status Mahasiswa <span class="text-rose-500">*</span>
            <span class="text-[11px] font-normal text-slate-400 block sm:inline sm:ml-2">Pilih kondisi akademik mahasiswa saat ini:</span>
          </label>
          
          <input type="hidden" id="formStatusVal" name="status" value="Terdaftar - Aktif">

          <div class="space-y-2">
            <!-- 1. Terdaftar - Aktif -->
            <div class="status-radio-card active rounded-xl p-3 flex items-start gap-3" onclick="pilihStatusForm('Terdaftar - Aktif')">
              <input type="radio" name="opt_status" id="optStatus1" value="Terdaftar - Aktif" checked class="mt-0.5 text-orange-500 focus:ring-orange-500">
              <label for="optStatus1" class="cursor-pointer text-xs flex-1">
                <span class="font-bold text-emerald-700 block">1. Terdaftar - Aktif</span>
                <span class="text-slate-600 text-[11px] leading-tight block mt-0.5">
                  Mahasiswa terdaftar dalam sistem kampus (SIAT/SIAKAD) dan aktif dalam kegiatan perkuliahan.
                </span>
              </label>
            </div>

            <!-- 2. Terdaftar - Tidak Aktif -->
            <div class="status-radio-card rounded-xl p-3 flex items-start gap-3" onclick="pilihStatusForm('Terdaftar - Tidak Aktif')">
              <input type="radio" name="opt_status" id="optStatus2" value="Terdaftar - Tidak Aktif" class="mt-0.5 text-orange-500 focus:ring-orange-500">
              <label for="optStatus2" class="cursor-pointer text-xs flex-1">
                <span class="font-bold text-amber-700 block">2. Terdaftar - Tidak Aktif</span>
                <span class="text-slate-600 text-[11px] leading-tight block mt-0.5">
                  Mahasiswa terdaftar namun tidak aktif dalam kegiatan perkuliahan.
                </span>
              </label>
            </div>

            <!-- 3. Cuti -->
            <div class="status-radio-card rounded-xl p-3 flex items-start gap-3" onclick="pilihStatusForm('Cuti')">
              <input type="radio" name="opt_status" id="optStatus3" value="Cuti" class="mt-0.5 text-orange-500 focus:ring-orange-500">
              <label for="optStatus3" class="cursor-pointer text-xs flex-1">
                <span class="font-bold text-sky-700 block">3. Cuti</span>
                <span class="text-slate-600 text-[11px] leading-tight block mt-0.5">
                  Mahasiswa yang sedang cuti.
                </span>
              </label>
            </div>

            <!-- 4. Tidak Aktif -->
            <div class="status-radio-card rounded-xl p-3 flex items-start gap-3" onclick="pilihStatusForm('Tidak Aktif')">
              <input type="radio" name="opt_status" id="optStatus4" value="Tidak Aktif" class="mt-0.5 text-orange-500 focus:ring-orange-500">
              <label for="optStatus4" class="cursor-pointer text-xs flex-1">
                <span class="font-bold text-rose-700 block">4. Tidak Aktif</span>
                <span class="text-slate-600 text-[11px] leading-tight block mt-0.5">
                  Mahasiswa yang dalam sistem kampus sudah tidak aktif / Mengundurkan diri.
                </span>
              </label>
            </div>
          </div>
        </div>

        <!-- Catatan Evaluasi Dosen PA (Rich Text) -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold text-slate-700">Catatan &amp; Evaluasi Dosen PA <span class="text-rose-500">*</span></label>
            <span class="text-[11px] text-slate-400">Mendukung format poin, penomoran, &amp; tebal &bull; <span id="charCount">0</span> karakter</span>
          </div>

          <div class="border border-slate-200 rounded-lg overflow-hidden focus-within:border-orange-500 focus-within:ring-1 focus-within:ring-orange-500 transition bg-white shadow-2xs">
            <!-- Toolbar Editor -->
            <div class="flex items-center flex-wrap gap-1 p-1.5 bg-slate-50 border-b border-slate-200 text-slate-600 select-none">
              <button type="button" class="rich-btn" onclick="formatCatatan('bold')" title="Tebal (Bold / Ctrl+B)">
                <i class="fas fa-bold"></i>
              </button>
              <button type="button" class="rich-btn" onclick="formatCatatan('italic')" title="Miring (Italic / Ctrl+I)">
                <i class="fas fa-italic"></i>
              </button>
              <button type="button" class="rich-btn" onclick="formatCatatan('underline')" title="Garis Bawah (Underline / Ctrl+U)">
                <i class="fas fa-underline"></i>
              </button>
              <span class="h-4 w-[1px] bg-slate-300 mx-0.5"></span>
              <button type="button" class="rich-btn" onclick="formatCatatan('insertUnorderedList')" title="Daftar Poin (Bullet List)">
                <i class="fas fa-list-ul"></i>
              </button>
              <button type="button" class="rich-btn" onclick="formatCatatan('insertOrderedList')" title="Daftar Nomor (Numbered List)">
                <i class="fas fa-list-ol"></i>
              </button>
              <span class="h-4 w-[1px] bg-slate-300 mx-0.5"></span>
              <button type="button" class="rich-btn text-slate-400 hover:text-rose-600" onclick="formatCatatan('removeFormat')" title="Hapus Format">
                <i class="fas fa-eraser"></i>
              </button>
            </div>

            <!-- Contenteditable Editor -->
            <div id="editorCatatan" contenteditable="true"
                 class="p-3 text-xs text-slate-700 min-h-[90px] max-h-[220px] overflow-y-auto outline-none rich-editor leading-relaxed"
                 placeholder="Tuliskan evaluasi perkembangan, saran pengambilan mata kuliah, kendala studi, atau arahan untuk mahasiswa..."></div>
          </div>

          <!-- Hidden Textarea untuk sinkronisasi form submit -->
          <textarea id="formCatatan" name="catatan" class="hidden"></textarea>
        </div>

        <!-- Modal Footer -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
          <button type="button" onclick="tutupModalIsianPA()" class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">
            Batal
          </button>
          <button type="submit" id="btnSubmitForm" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs rounded-lg bg-[#f97316] hover:bg-[#ea580c] text-white font-semibold shadow-sm shadow-orange-500/20 transition">
            <i class="fas fa-save"></i>
            <span>Simpan Buku PA</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus Isian PA -->
<div id="modalKonfirmasiHapusPA" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 hidden">
  <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6 text-center transform transition-all">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
      <i class="fas fa-trash-alt text-lg"></i>
    </div>
    <h3 class="text-base font-bold text-slate-800 mb-1">Hapus Catatan Buku PA</h3>
    <p class="text-xs text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin ingin menghapus data catatan Buku PA semester ini? Tindakan ini tidak dapat dibatalkan.</p>
    <div class="flex items-center justify-center gap-2">
      <button type="button" onclick="tutupModalHapusPA()" class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium transition">
        Batal
      </button>
      <button type="button" id="btnEksekusiHapusPA" onclick="eksekusiHapusPA()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-sm transition">
        <i class="fas fa-trash-alt text-[11px]"></i> Ya, Hapus Data
      </button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div id="toastContainer"></div>

<!-- ===== DRAWER OFFCANVAS RIWAYAT BUKU PA ===== -->
<div id="drawerRiwayatBackdrop" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs" onclick="tutupDrawerRiwayatPA()">
  <div id="drawerRiwayatPanel" class="fixed top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col z-50" onclick="event.stopPropagation()">
    <!-- Drawer Header -->
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/70">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-xs">
          <i class="fas fa-history"></i>
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-800">Riwayat Buku PA Mahasiswa</h3>
          <p class="text-xs text-slate-500" id="drawerSubjudul">Timeline evaluasi semesteran</p>
        </div>
      </div>
      <button type="button" onclick="tutupDrawerRiwayatPA()" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 text-base transition">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Drawer Mahasiswa Info Banner -->
    <div class="px-6 py-3 bg-blue-50/60 border-b border-blue-100 flex items-center justify-between">
      <div>
        <div class="font-bold text-slate-800 text-sm" id="drawerNamaMhs">-</div>
        <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-600">
          <span class="font-mono font-medium" id="drawerNimMhs">-</span>
          <span>&bull;</span>
          <span id="drawerProdiMhs">-</span>
        </div>
      </div>
      <button type="button" id="btnTambahDariDrawer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#f97316] hover:bg-[#ea580c] text-white text-xs font-semibold shadow-sm shadow-orange-500/20 transition">
        <i class="fas fa-plus"></i>
        <span>Isi Semester Baru</span>
      </button>
    </div>

    <!-- Drawer Content (Timeline Riwayat) -->
    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="drawerBodyRiwayat">
      <div class="py-12 text-center text-slate-400">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
        <p class="text-xs">Memuat riwayat Buku PA...</p>
      </div>
    </div>
  </div>
</div>

<script>
  var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/bimbingan/pa/proses.php';
  var userRoleMode = '<?= ($userLevel === 9) ? "semua" : "saya" ?>';
  var curPage = 1;

  document.addEventListener('DOMContentLoaded', function() {
    updateOpsiAngkatan(userRoleMode);
    muatStatistikPA();
    muatDataPA(1);

    // Event filter otomatis
    var searchTimer = null;
    document.getElementById('fCari').addEventListener('input', function() {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(function() {
        muatStatistikPA();
        muatDataPA(1);
      }, 350);
    });

    ['fProdi', 'fAngkatan', 'fStatus'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) {
        el.addEventListener('change', function() {
          muatStatistikPA();
          muatDataPA(1);
        });
      }
    });

    // Reset button handler
    var btnReset = document.getElementById('btnReset');
    if (btnReset) {
      btnReset.addEventListener('click', function() {
        document.getElementById('fCari').value = '';
        var fp = document.getElementById('fProdi');
        if (fp && !fp.disabled) fp.value = '';
        var fa = document.getElementById('fAngkatan');
        if (fa) fa.value = '';
        var fs = document.getElementById('fStatus');
        if (fs) fs.value = '';
        muatStatistikPA();
        muatDataPA(1);
      });
    }

    // Set default tanggal hari ini pada form modal
    var tglEl = document.getElementById('formTanggal');
    if (tglEl && !tglEl.value) {
      var now = new Date();
      var day = ("0" + now.getDate()).slice(-2);
      var month = ("0" + (now.getMonth() + 1)).slice(-2);
      tglEl.value = now.getFullYear() + "-" + month + "-" + day;
    }
  });

  // Switcher Mode: Bimbingan Saya vs Semua
  function gantiModePembimbing(mode) {
    userRoleMode = mode;
    var btnSaya = document.getElementById('btnModeSaya');
    var btnSemua = document.getElementById('btnModeSemua');

    if (btnSaya && btnSemua) {
      if (mode === 'saya') {
        btnSaya.className = "inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg transition-all shadow-xs bg-white text-orange-600 font-bold";
        btnSemua.className = "inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900 font-semibold";
      } else {
        btnSemua.className = "inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg transition-all shadow-xs bg-white text-orange-600 font-bold";
        btnSaya.className = "inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900 font-semibold";
      }
    }

    updateOpsiAngkatan(mode);
    muatStatistikPA();
    muatDataPA(1);
  }

  // Khusus mode Semua Mahasiswa (kecuali Superadmin), filter opsi angkatan hanya sampai tahun - 7
  function updateOpsiAngkatan(mode) {
    var fAng = document.getElementById('fAngkatan');
    if (!fAng) return;
    var isModeSemuaNonAdmin = (mode === 'semua' && <?= ($userLevel === 9 ? 'false' : 'true') ?>);
    var tahunBatas = <?= (int)date('Y') - 7 ?>;

    var opts = fAng.querySelectorAll('option[data-angkatan]');
    opts.forEach(function(opt) {
      var ang = parseInt(opt.getAttribute('data-angkatan')) || 0;
      if (isModeSemuaNonAdmin && ang < tahunBatas) {
        opt.hidden = true;
        opt.disabled = true;
      } else {
        opt.hidden = false;
        opt.disabled = false;
      }
    });

    if (fAng.selectedOptions.length > 0 && fAng.selectedOptions[0].disabled) {
      fAng.value = '';
    }
  }

  // Muat Angka Metrik KPI
  function muatStatistikPA() {
    var fd = new FormData();
    fd.append('ambil-ringkasan-statistik', '1');
    fd.append('pembimbing', userRoleMode);
    fd.append('prodi', document.getElementById('fProdi') ? document.getElementById('fProdi').value : '');
    fd.append('angkatan', document.getElementById('fAngkatan') ? document.getElementById('fAngkatan').value : '');
    fd.append('status', document.getElementById('fStatus') ? document.getElementById('fStatus').value : '');
    fd.append('cari', document.getElementById('fCari') ? document.getElementById('fCari').value : '');

    fetch(PROSES_URL, { method: 'POST', body: fd })
      .then(function(res) { return res.json(); })
      .then(function(resp) {
        if (resp.status && resp.data) {
          document.getElementById('statTotalMhs').textContent = resp.data.total;
          document.getElementById('statAktif').textContent = resp.data.aktif;
          document.getElementById('statCuti').textContent = resp.data.cuti;
          document.getElementById('statTidakAktif').textContent = resp.data.tidak_aktif;
        }
      })
      .catch(function(err) {
        console.error('Gagal mengambil statistik PA:', err);
      });
  }

  // Muat Baris Tabel Data
  function muatDataPA(page) {
    curPage = page || 1;
    var tbody = document.getElementById('tabelBodyPA');
    tbody.innerHTML = '<tr><td colspan="7" class="py-12 text-center text-slate-400"><i class="fas fa-spinner fa-spin text-2xl text-orange-500 mb-2"></i><p>Memuat data bimbingan PA...</p></td></tr>';

    var fd = new FormData();
    fd.append('ambil-tabel-bimbingan-pa', '1');
    fd.append('pembimbing', userRoleMode);
    fd.append('prodi', document.getElementById('fProdi') ? document.getElementById('fProdi').value : '');
    fd.append('angkatan', document.getElementById('fAngkatan') ? document.getElementById('fAngkatan').value : '');
    fd.append('status', document.getElementById('fStatus') ? document.getElementById('fStatus').value : '');
    fd.append('cari', document.getElementById('fCari') ? document.getElementById('fCari').value : '');
    fd.append('page', curPage);

    fetch(PROSES_URL, { method: 'POST', body: fd })
      .then(function(res) { return res.text(); })
      .then(function(html) {
        tbody.innerHTML = html;
        var meta = document.getElementById('paPaginationMeta');
        var totalPages = meta ? (parseInt(meta.getAttribute('data-total-pages')) || 1) : 1;
        var totalData = meta ? (parseInt(meta.getAttribute('data-total-data')) || 0) : 0;
        renderPaginasiPA(curPage, totalPages, totalData);
      })
      .catch(function(err) {
        tbody.innerHTML = '<tr><td colspan="7" class="py-12 text-center text-rose-500 font-medium">Terjadi kesalahan pada server saat memuat data.</td></tr>';
      });
  }

  // Render Tombol Paginasi Standar SIATEK
  function renderPaginasiPA(page, totalPages, totalData) {
    var nav = document.getElementById('navPaginasi');
    if (!nav) return;
    nav.innerHTML = '';

    document.getElementById('currPageSpan').textContent = page + ' dari ' + totalPages + ' (' + totalData + ' Mahasiswa)';
    document.getElementById('infoPaginasi').textContent = 'Halaman ' + page + ' dari ' + totalPages;

    if (totalPages <= 1) return;

    // Tombol Sebelumnya
    var prev = document.createElement('button');
    prev.type = 'button';
    prev.className = 'pg-btn';
    prev.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
    prev.disabled = (page <= 1);
    prev.onclick = function() { if (page > 1) muatDataPA(page - 1); };
    nav.appendChild(prev);

    // Tombol Angka
    var start = Math.max(1, page - 2);
    var end = Math.min(totalPages, page + 2);

    if (start > 1) {
      var b1 = document.createElement('button');
      b1.type = 'button';
      b1.className = 'pg-btn';
      b1.textContent = '1';
      b1.onclick = function() { muatDataPA(1); };
      nav.appendChild(b1);
      if (start > 2) {
        var dots = document.createElement('span');
        dots.className = 'px-1 text-slate-400 text-xs';
        dots.textContent = '...';
        nav.appendChild(dots);
      }
    }

    for (var i = start; i <= end; i++) {
      (function(p) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'pg-btn' + (p === page ? ' on' : '');
        b.textContent = p;
        b.onclick = function() { muatDataPA(p); };
        nav.appendChild(b);
      })(i);
    }

    if (end < totalPages) {
      if (end < totalPages - 1) {
        var dots = document.createElement('span');
        dots.className = 'px-1 text-slate-400 text-xs';
        dots.textContent = '...';
        nav.appendChild(dots);
      }
      var bLast = document.createElement('button');
      bLast.type = 'button';
      bLast.className = 'pg-btn';
      bLast.textContent = totalPages;
      bLast.onclick = function() { muatDataPA(totalPages); };
      nav.appendChild(bLast);
    }

    // Tombol Selanjutnya
    var next = document.createElement('button');
    next.type = 'button';
    next.className = 'pg-btn';
    next.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
    next.disabled = (page >= totalPages);
    next.onclick = function() { if (page < totalPages) muatDataPA(page + 1); };
    nav.appendChild(next);
  }

  // Pilihan Status Radio Card pada Form Modal
  function pilihStatusForm(status) {
    document.getElementById('formStatusVal').value = status;
    var cards = document.querySelectorAll('.status-radio-card');
    cards.forEach(function(card) {
      var radio = card.querySelector('input[type="radio"]');
      if (radio.value === status) {
        radio.checked = true;
        card.classList.add('active');
      } else {
        radio.checked = false;
        card.classList.remove('active');
      }
    });
  }

  // Rich Text Editor Commands & Sync
  function formatCatatan(cmd, val) {
    var editor = document.getElementById('editorCatatan');
    if (!editor) return;
    editor.focus();
    document.execCommand(cmd, false, val || null);
    syncCatatanEditor();
  }

  function syncCatatanEditor() {
    var editor = document.getElementById('editorCatatan');
    var txt = document.getElementById('formCatatan');
    var charCount = document.getElementById('charCount');
    if (!editor || !txt) return;

    var html = editor.innerHTML;
    var plain = editor.innerText.trim();
    if (!plain && (html === '<br>' || html === '<p><br></p>' || html === '<div><br></div>' || html === '')) {
      txt.value = '';
      if (charCount) charCount.textContent = '0';
    } else {
      txt.value = html;
      if (charCount) charCount.textContent = plain.length;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    var editor = document.getElementById('editorCatatan');
    if (editor) {
      editor.addEventListener('input', syncCatatanEditor);
      editor.addEventListener('keyup', syncCatatanEditor);
      editor.addEventListener('blur', syncCatatanEditor);
      editor.addEventListener('paste', function() {
        setTimeout(syncCatatanEditor, 20);
      });
    }
  });

  // Toast Notification
  function toast(pesan, tipe) {
    tipe = tipe || 'info';
    var container = document.getElementById('toastContainer');
    if (!container) return;
    var item = document.createElement('div');
    item.className = 'toast-item toast-' + tipe;
    var ikon = 'fa-info-circle';
    if (tipe === 'success') ikon = 'fa-check-circle';
    else if (tipe === 'error') ikon = 'fa-circle-xmark';
    else if (tipe === 'warning') ikon = 'fa-triangle-exclamation';

    item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'hide\');setTimeout(function(){item.remove()},350)">&times;</button>';
    container.appendChild(item);
    requestAnimationFrame(function(){ item.classList.add('show'); });
    setTimeout(function(){ item.classList.add('hide'); setTimeout(function(){ item.remove(); }, 350); }, 4000);
  }

  // Modal Buka & Tutup
  function bukaModalIsianPA(nim, nama, prodi, idIsian) {
    if (userRoleMode === 'semua' && <?= ($userLevel === 9 ? 'false' : 'true') ?>) {
      toast('Pengisian evaluasi Buku PA hanya dapat dilakukan oleh Dosen PA mahasiswa bersangkutan pada tab "Bimbingan Saya".', 'error');
      return;
    }

    document.getElementById('formIdIsian').value = idIsian || 0;
    document.getElementById('formNim').value = nim;
    document.getElementById('formNamaMhs').textContent = nama;
    document.getElementById('formNimMhs').textContent = nim;
    document.getElementById('formProdiMhs').textContent = prodi;

    // Reset Form Input
    document.getElementById('formSemester').value = '';
    document.getElementById('formIps').value = '';
    document.getElementById('formIpk').value = '';
    document.getElementById('formSks').value = '';
    document.getElementById('formCatatan').value = '';
    var editorEl = document.getElementById('editorCatatan');
    if (editorEl) editorEl.innerHTML = '';
    syncCatatanEditor();
    pilihStatusForm('Terdaftar - Aktif');

    if (idIsian && idIsian > 0) {
      document.getElementById('modalJudul').textContent = 'Perbarui Evaluasi Buku PA';
      document.getElementById('modalSubjudul').textContent = 'Edit catatan evaluasi semester mahasiswa';
      
      // Ambil detail data untuk edit
      var fd = new FormData();
      fd.append('ambil-detail-isian', '1');
      fd.append('id', idIsian);

      fetch(PROSES_URL, { method: 'POST', body: fd })
        .then(function(res) { return res.json(); })
        .then(function(resp) {
          if (resp.status && resp.data) {
            document.getElementById('formSemester').value = resp.data.semester;
            document.getElementById('formIps').value = resp.data.ips || '';
            document.getElementById('formIpk').value = resp.data.ipk || '';
            document.getElementById('formSks').value = resp.data.sks || '';
            
            var cVal = resp.data.catatan || '';
            document.getElementById('formCatatan').value = cVal;
            if (cVal && !/<[a-z][\s\S]*>/i.test(cVal)) {
              cVal = cVal.replace(/\n/g, '<br>');
            }
            if (editorEl) editorEl.innerHTML = cVal;
            syncCatatanEditor();

            document.getElementById('formTanggal').value = resp.data.tanggal || '';
            pilihStatusForm(resp.data.status || 'Terdaftar - Aktif');
          }
        });
    } else {
      document.getElementById('modalJudul').textContent = 'Pengisian Buku PA (Evaluasi Studi)';
      document.getElementById('modalSubjudul').textContent = 'Formulir evaluasi semesteran mahasiswa';
    }

    document.getElementById('modalIsianPA').classList.remove('hidden');
  }

  function tutupModalIsianPA() {
    document.getElementById('modalIsianPA').classList.add('hidden');
  }

  // Simpan Form Modal
  function simpanFormIsianPA(e) {
    e.preventDefault();
    syncCatatanEditor();

    var editor = document.getElementById('editorCatatan');
    var plain = editor ? editor.innerText.trim() : '';
    if (!plain) {
      toast('Catatan & Evaluasi Dosen PA wajib diisi.', 'warning');
      if (editor) editor.focus();
      return;
    }

    var btn = document.getElementById('btnSubmitForm');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    var fd = new FormData(document.getElementById('formIsianPA'));
    fd.append('simpan-isian-pa', '1');

    fetch(PROSES_URL, { method: 'POST', body: fd })
      .then(function(res) { return res.json(); })
      .then(function(resp) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Buku PA';

        if (resp.status) {
          toast(resp.pesan, 'success');
          tutupModalIsianPA();
          muatStatistikPA();
          muatDataPA(curPage);
          
          // Jika drawer sedang terbuka untuk mahasiswa ini, refresh drawer
          var nimDrawer = document.getElementById('drawerNimMhs').textContent;
          if (nimDrawer === document.getElementById('formNim').value) {
            bukaDrawerRiwayatPA(nimDrawer);
          }
        } else {
          toast(resp.pesan, 'error');
        }
      })
      .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Buku PA';
        toast('Terjadi kesalahan koneksi server saat menyimpan data.', 'error');
      });
  }

  // Drawer Riwayat Pengisian Buku PA
  function bukaDrawerRiwayatPA(nim) {
    var backdrop = document.getElementById('drawerRiwayatBackdrop');
    var panel = document.getElementById('drawerRiwayatPanel');
    var body = document.getElementById('drawerBodyRiwayat');

    backdrop.classList.add('active');
    panel.classList.add('active');
    body.innerHTML = '<div class="py-12 text-center text-slate-400"><i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i><p class="text-xs">Memuat riwayat Buku PA...</p></div>';

    var fd = new FormData();
    fd.append('ambil-riwayat-pa', '1');
    fd.append('nim', nim);
    fd.append('pembimbing', userRoleMode);

    fetch(PROSES_URL, { method: 'POST', body: fd })
      .then(function(res) { return res.json(); })
      .then(function(resp) {
        if (resp.status) {
          var m = resp.mahasiswa;
          document.getElementById('drawerNamaMhs').textContent = m.nama;
          document.getElementById('drawerNimMhs').textContent = m.nim;
          document.getElementById('drawerProdiMhs').textContent = m.prodi + ' (Angk. ' + m.angkatan + ')';

          // Tampilkan atau sembunyikan tombol tambah cepat berdasarkan kewenangan
          var btnTambah = document.getElementById('btnTambahDariDrawer');
          if (btnTambah) {
            if (resp.can_edit) {
              btnTambah.classList.remove('hidden');
              btnTambah.onclick = function() {
                bukaModalIsianPA(m.nim, m.nama, m.prodi, 0);
              };
            } else {
              btnTambah.classList.add('hidden');
            }
          }

          if (resp.riwayat.length === 0) {
            body.innerHTML = '\
              <div class="py-16 text-center text-slate-400">\
                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300"></i>\
                <p class="text-sm font-semibold text-slate-600">Belum Ada Riwayat Isian PA</p>\
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Dosen PA belum mencatat riwayat konsultasi semester untuk mahasiswa ini.</p>\
              </div>';
            return;
          }

          var html = '<div class="space-y-4">';
          resp.riwayat.forEach(function(r) {
            var stBadge = 'bg-slate-100 text-slate-600 border-slate-200';
            if (r.status === 'Terdaftar - Aktif') stBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            else if (r.status === 'Terdaftar - Tidak Aktif') stBadge = 'bg-amber-50 text-amber-700 border-amber-200';
            else if (r.status === 'Cuti') stBadge = 'bg-sky-50 text-sky-700 border-sky-200';
            else if (r.status === 'Tidak Aktif') stBadge = 'bg-rose-50 text-rose-700 border-rose-200';

            var actionBtnHtml = '';
            if (resp.can_edit) {
              actionBtnHtml = '\
                <div class="flex items-center gap-1.5">\
                  <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 transition shadow-2xs" onclick="bukaModalIsianPA(\'' + m.nim + '\', \'' + m.nama.replace(/'/g, "\\'") + '\', \'' + m.prodi.replace(/'/g, "\\'") + '\', ' + r.id + ')">\
                    <i class="fas fa-pen text-[10px]"></i>Edit\
                  </button>\
                  <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition shadow-2xs" onclick="konfirmasiHapusIsianPA(' + r.id + ', \'' + m.nim + '\')">\
                    <i class="fas fa-trash-alt text-[10px]"></i>Hapus\
                  </button>\
                </div>';
            }

            html += '\
              <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs hover:border-slate-300 transition">\
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-2.5">\
                  <div class="flex items-center gap-2">\
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">\
                      Semester ' + r.semester + '\
                    </span>\
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold border ' + stBadge + '">\
                      ' + r.status + '\
                    </span>\
                  </div>\
                  <div class="text-[11px] text-slate-400">\
                    <i class="far fa-calendar-alt mr-1"></i>' + r.tanggal + '\
                  </div>\
                </div>\
                <div class="grid grid-cols-3 gap-2 py-1 bg-slate-50 rounded-lg text-center text-xs mb-3">\
                  <div><span class="text-slate-400 block text-[10px]">IPS</span><strong class="text-slate-800">' + r.ips + '</strong></div>\
                  <div><span class="text-slate-400 block text-[10px]">IPK</span><strong class="text-slate-800">' + r.ipk + '</strong></div>\
                  <div><span class="text-slate-400 block text-[10px]">Beban SKS</span><strong class="text-slate-800">' + r.sks + ' SKS</strong></div>\
                </div>\
                <div>\
                  <span class="text-[11px] font-semibold text-slate-500 block mb-1">Catatan &amp; Evaluasi Dosen PA:</span>\
                  <div class="text-xs text-slate-700 bg-slate-50/70 p-3 rounded-lg border border-slate-200/80 leading-relaxed catatan-content">\
                    ' + (r.catatan ? (/<[a-z][\s\S]*>/i.test(r.catatan) ? r.catatan : r.catatan.replace(/\n/g, '<br>')) : '<span class="italic text-slate-400">Tidak ada catatan</span>') + '\
                  </div>\
                </div>\
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">\
                  <span>Dicatat: ' + r.rekaman + '</span>\
                  ' + actionBtnHtml + '\
                </div>\
              </div>';
          });
          html += '</div>';
          body.innerHTML = html;
        } else {
          body.innerHTML = '<div class="py-12 text-center text-rose-500 text-xs font-semibold">' + resp.pesan + '</div>';
        }
      })
      .catch(function(err) {
        body.innerHTML = '<div class="py-12 text-center text-rose-500 text-xs font-semibold">Gagal memuat riwayat Buku PA.</div>';
      });
  }

  function tutupDrawerRiwayatPA() {
    var backdrop = document.getElementById('drawerRiwayatBackdrop');
    var panel = document.getElementById('drawerRiwayatPanel');
    backdrop.classList.remove('active');
    panel.classList.remove('active');
  }

  // Konfirmasi & Eksekusi Hapus Isian PA (Tanpa native confirm/alert)
  var targetHapusId = null;
  var targetHapusNim = null;

  function konfirmasiHapusIsianPA(id, nim) {
    targetHapusId = id;
    targetHapusNim = nim;
    var modal = document.getElementById('modalKonfirmasiHapusPA');
    if (modal) modal.classList.remove('hidden');
  }

  function tutupModalHapusPA() {
    targetHapusId = null;
    targetHapusNim = null;
    var modal = document.getElementById('modalKonfirmasiHapusPA');
    if (modal) modal.classList.add('hidden');
    var btn = document.getElementById('btnEksekusiHapusPA');
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-trash-alt text-[11px]"></i> Ya, Hapus Data';
    }
  }

  function eksekusiHapusPA() {
    if (!targetHapusId || !targetHapusNim) return;
    var btn = document.getElementById('btnEksekusiHapusPA');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';
    }

    var fd = new FormData();
    fd.append('hapus-isian-pa', '1');
    fd.append('id', targetHapusId);

    var curNim = targetHapusNim;

    fetch(PROSES_URL, { method: 'POST', body: fd })
      .then(function(res) { return res.json(); })
      .then(function(resp) {
        tutupModalHapusPA();
        if (resp.status) {
          toast(resp.pesan, 'success');
          bukaDrawerRiwayatPA(curNim);
          muatStatistikPA();
          muatDataPA(curPage);
        } else {
          toast(resp.pesan, 'error');
        }
      })
      .catch(function(err) {
        tutupModalHapusPA();
        toast('Terjadi kesalahan koneksi saat menghapus data.', 'error');
      });
  }
</script>
