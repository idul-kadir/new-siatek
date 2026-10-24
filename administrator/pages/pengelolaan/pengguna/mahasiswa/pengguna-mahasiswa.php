<?php
require_once __DIR__ . "/../../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan pengguna sudah login sebagai staf
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$peran      = $_SESSION['peran'] ?? (!empty($_SESSION['nim']) ? 'mahasiswa' : 'staf');

if (!$sudahLogin || $peran === 'mahasiswa') {
    header('Location: ' . base_url() . '/');
    exit;
}

$userLevel     = (int)($_SESSION['level'] ?? 0);
$isFilterProdi = in_array($userLevel, [3, 5], true);
$userProdi     = $isFilterProdi ? (function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''))) : '';
$namaProdiUser = '';

// Ambil statistik awal untuk panel ringkasan
$whereProdiAwal = '';
if ($isFilterProdi) {
    if ($userProdi !== '') {
        $whereProdiAwal = "WHERE (prodi = '$userProdi' OR prodi IN (SELECT nama FROM prodi WHERE kode = '$userProdi'))";
    } else {
        $whereProdiAwal = "WHERE 1=0";
    }
}

// 1. Total Mahasiswa
$qTotal = query("SELECT COUNT(*) AS total FROM mahasiswa $whereProdiAwal");
$totalMhs = (int)(mysqli_fetch_assoc($qTotal)['total'] ?? 0);

// 2. Mahasiswa Tanpa Dosen PA
$whereTanpaPa = $whereProdiAwal ? "$whereProdiAwal AND (dosen_pa IS NULL OR dosen_pa = '' OR dosen_pa = '0')" : "WHERE (dosen_pa IS NULL OR dosen_pa = '' OR dosen_pa = '0')";
$qTanpaPa = query("SELECT COUNT(*) AS total FROM mahasiswa $whereTanpaPa");
$totalTanpaPa = (int)(mysqli_fetch_assoc($qTanpaPa)['total'] ?? 0);

// 3. Rincian status murni dari database (tidak dikurangi/digabung)
$daftarStatus = [];
$qStatus = query("SELECT status, COUNT(*) AS jml FROM mahasiswa $whereProdiAwal GROUP BY status ORDER BY jml DESC");
if ($qStatus) {
    while ($rs = mysqli_fetch_assoc($qStatus)) {
        $stNama = trim((string)$rs['status']);
        if ($stNama === '') {
            $stNama = 'Tidak Diketahui';
        }
        $daftarStatus[$stNama] = (int)$rs['jml'];
    }
}

// Ambil daftar prodi
$daftarProdi = [];
$qProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
if ($qProdi) {
    while ($rp = mysqli_fetch_assoc($qProdi)) {
        $daftarProdi[] = $rp;
        if ($isFilterProdi && ($rp['kode'] === $userProdi || $rp['nama'] === $userProdi)) {
            $namaProdiUser = $rp['nama'];
        }
    }
}

// Ambil daftar angkatan
$daftarAngkatan = [];
$qAng = query("SELECT DISTINCT angkatan FROM mahasiswa WHERE angkatan IS NOT NULL AND angkatan != '' AND angkatan != '0000' ORDER BY angkatan DESC");
if ($qAng) {
    while ($ra = mysqli_fetch_assoc($qAng)) {
        $daftarAngkatan[] = $ra['angkatan'];
    }
}

// Ambil daftar dosen aktif untuk dropdown PA
$daftarDosen = [];
$qDosen = query("SELECT id, nama, nip FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' ORDER BY nama ASC");
if ($qDosen) {
    while ($rd = mysqli_fetch_assoc($qDosen)) {
        $daftarDosen[] = $rd;
    }
}

// Palet warna untuk status bar & chips
function get_status_style($statusName) {
    $st = strtolower($statusName);
    if (str_contains($st, 'aktif') && !str_contains($st, 'tidak') && !str_contains($st, 'non')) {
        return ['dot' => 'bg-emerald-500', 'bg' => 'hover:bg-emerald-50 hover:border-emerald-300', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500'];
    } elseif (str_contains($st, 'lulus') || str_contains($st, 'alumni')) {
        return ['dot' => 'bg-sky-500', 'bg' => 'hover:bg-sky-50 hover:border-sky-300', 'text' => 'text-sky-700', 'bar' => 'bg-sky-500'];
    } elseif (str_contains($st, 'cuti')) {
        return ['dot' => 'bg-amber-400', 'bg' => 'hover:bg-amber-50 hover:border-amber-300', 'text' => 'text-amber-700', 'bar' => 'bg-amber-400'];
    } elseif (str_contains($st, 'drop out') || $st === 'do') {
        return ['dot' => 'bg-rose-500', 'bg' => 'hover:bg-rose-50 hover:border-rose-300', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500'];
    } elseif (str_contains($st, 'keluar') || str_contains($st, 'pindah')) {
        return ['dot' => 'bg-purple-500', 'bg' => 'hover:bg-purple-50 hover:border-purple-300', 'text' => 'text-purple-700', 'bar' => 'bg-purple-500'];
    } elseif (str_contains($st, 'tidak') || str_contains($st, 'non')) {
        return ['dot' => 'bg-slate-400', 'bg' => 'hover:bg-slate-100 hover:border-slate-300', 'text' => 'text-slate-700', 'bar' => 'bg-slate-400'];
    }
    return ['dot' => 'bg-slate-400', 'bg' => 'hover:bg-slate-50 hover:border-slate-300', 'text' => 'text-slate-600', 'bar' => 'bg-slate-300'];
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

  .content-scroll {
    overflow-y: auto;
    min-height: 0;
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
  .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
  .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
  .pg-btn.on { background: #1a365d; border-color: #1a365d; color: #fff; }

  /* Modal Overlay */
  .modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1050;
    background: rgba(15,23,42,.6);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(2px);
  }
  .modal-overlay.show {
    display: flex;
    animation: fadeIn .2s ease-out forwards;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* Spinner & Loading */
  .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #f97316; border-radius: 50%; animation: spin 0.7s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }

  /* Toast notification */
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

  /* Active chip indicator */
  .btnQuickStatus.is-active {
    outline: 2px solid #f97316;
    outline-offset: 1px;
    background-color: #ffffff !important;
    box-shadow: 0 1px 4px rgba(249, 115, 22, 0.25);
  }
</style>

<main class="content-area content-scroll p-4 sm:p-6 space-y-4">
  <!-- Toast Container -->
  <div id="toastContainer"></div>

  <!-- ===== 1. Page Header ===== -->
  <section class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 text-white shadow-md shadow-orange-500/20">
        <i class="fas fa-users-gear text-xl"></i>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl font-bold text-slate-800">Pengguna Mahasiswa</h1>
          <?php if ($isFilterProdi && $namaProdiUser !== ''): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-800">
              <i class="fas fa-building-columns mr-1 text-[10px]"></i> <?= tampil($namaProdiUser) ?>
            </span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Manajemen akun pengguna mahasiswa: tambah, edit profil, kontak, dan reset password.</p>
      </div>
    </div>

    <!-- Tombol Aksi Kanan -->
    <div class="flex items-center gap-2">
      <button type="button" id="btnSegarkan" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition" title="Segarkan Data">
        <i class="fas fa-rotate text-slate-500"></i>
        <span>Segarkan</span>
      </button>

      <button type="button" id="btnBukaImport" class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 px-3.5 py-2 text-xs font-bold shadow-2xs transition" title="Import Massal Mahasiswa dari Excel / CSV">
        <i class="fas fa-file-excel text-emerald-600 text-sm"></i>
        <span>Import Excel / CSV</span>
      </button>

      <button type="button" id="btnTambah" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 hover:bg-orange-600 px-4 py-2 text-xs font-bold text-white shadow-sm shadow-orange-500/20 transition">
        <i class="fas fa-user-plus text-xs"></i>
        <span>Tambah Mahasiswa</span>
      </button>
    </div>
  </section>

  <?php if ($isFilterProdi && $userProdi === ''): ?>
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 shadow-sm">
      <i class="fas fa-exclamation-triangle text-xl text-amber-500 shrink-0"></i>
      <div>
        <p class="font-semibold">Program Studi Belum Tercatat</p>
        <p class="text-xs text-amber-700">Akun Anda belum memiliki data program studi yang tercatat di sistem. Tidak ada data mahasiswa yang dapat ditampilkan.</p>
      </div>
    </div>
  <?php endif; ?>
  <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      
      <!-- Sisi Kiri: Angka Total Sesuai Filter & Visual Distribution Bar -->
      <div class="flex items-center gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-lg">
          <i class="fas fa-id-card-clip"></i>
        </div>
        <div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-slate-900" id="statTotalText"><?= number_format($totalMhs, 0, ',', '.') ?></span>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa Terdaftar</span>
          </div>
          <!-- Bar Komposisi Dinamis Sesuai Lingkup Filter Aktif -->
          <div class="w-60 sm:w-80 h-2 bg-slate-100 rounded-full overflow-hidden flex mt-2 shadow-inner" id="barKomposisi" title="Distribusi status mahasiswa riil">
            <?php foreach ($daftarStatus as $stNama => $stJml): 
              $pct = $totalMhs > 0 ? round(($stJml / $totalMhs) * 100) : 0;
              $style = get_status_style($stNama);
            ?>
              <div class="bar-seg <?= $style['bar'] ?> h-full transition-all" data-status-key="<?= htmlspecialchars($stNama) ?>" style="width: <?= $pct ?>%" title="<?= htmlspecialchars($stNama) ?>: <?= number_format($stJml, 0, ',', '.') ?>"></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Sisi Kanan: Status Metrik Riil (Klik untuk Filter Cepat Status) -->
      <div class="flex flex-wrap items-center gap-2 sm:gap-2.5" id="wrapStatusChips">
        <?php foreach ($daftarStatus as $stNama => $stJml): 
          $style = get_status_style($stNama);
        ?>
          <button type="button" class="btnQuickStatus flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 <?= $style['bg'] ?> border border-slate-200 text-left transition group cursor-pointer" data-status="<?= htmlspecialchars($stNama) ?>" title="Filter mahasiswa berstatus <?= htmlspecialchars($stNama) ?>">
            <span class="h-2.5 w-2.5 rounded-full <?= $style['dot'] ?> shrink-0"></span>
            <div>
              <div class="text-[10px] font-semibold text-slate-500 group-hover:<?= $style['text'] ?>"><?= htmlspecialchars($stNama) ?></div>
              <div class="text-xs font-bold text-slate-800 leading-tight stat-count-num" data-status-key="<?= htmlspecialchars($stNama) ?>"><?= number_format($stJml, 0, ',', '.') ?></div>
            </div>
          </button>
        <?php endforeach; ?>

        <!-- Tanpa Dosen PA (Informasi Kritis yang Langsung Bikin Ngeh) -->
        <button type="button" class="btnQuickStatus flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-300 text-left transition group cursor-pointer" data-status="tanpa_pa" title="Filter mahasiswa yang belum memiliki Dosen PA">
          <i class="fas fa-triangle-exclamation text-amber-600 text-xs shrink-0"></i>
          <div>
            <div class="text-[10px] font-bold text-amber-800">Tanpa Dosen PA</div>
            <div class="text-xs font-bold text-amber-900 leading-tight" id="statTanpaPaText"><?= number_format($totalTanpaPa, 0, ',', '.') ?></div>
          </div>
        </button>
      </div>

    </div>
  </section>

  <!-- ===== 3. Toolbar Pencarian & Filter Terpadu ===== -->
  <section class="rounded-xl border border-slate-200 bg-white p-3 shadow-xs">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5">
      <!-- Search Input -->
      <div class="lg:col-span-5 relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama, NIM, email, atau no. telepon..."
               class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition" />
      </div>

      <!-- Filter Program Studi -->
      <div class="lg:col-span-3">
        <select id="fProdi" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isFilterProdi ? 'disabled' : '' ?>>
          <?php if (!$isFilterProdi): ?>
            <option value="">-- Semua Program Studi --</option>
            <?php foreach ($daftarProdi as $p): ?>
              <option value="<?= htmlspecialchars($p['kode']) ?>">
                <?= htmlspecialchars($p['nama']) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="<?= htmlspecialchars($userProdi) ?>" selected><?= htmlspecialchars($namaProdiUser ?: $userProdi) ?></option>
          <?php endif; ?>
        </select>
      </div>

      <!-- Filter Angkatan -->
      <div class="lg:col-span-2">
        <select id="fAngkatan" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition">
          <option value="">-- Semua Angkatan --</option>
          <?php foreach ($daftarAngkatan as $ang): ?>
            <option value="<?= htmlspecialchars($ang) ?>">Angkatan <?= htmlspecialchars($ang) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Status Dropdown (Dinamis dari Status Database Riil) -->
      <div class="lg:col-span-2 flex items-center gap-1.5">
        <select id="fStatus" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-800 focus:bg-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition">
          <option value="">-- Semua Status --</option>
          <?php foreach ($daftarStatus as $stNama => $stJml): ?>
            <option value="<?= htmlspecialchars($stNama) ?>"><?= htmlspecialchars($stNama) ?></option>
          <?php endforeach; ?>
          <option value="tanpa_pa">Tanpa Dosen PA</option>
        </select>
        <button type="button" id="btnReset" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition shrink-0" title="Reset Semua Filter">
          <i class="fas fa-times"></i>
          <span>Reset</span>
        </button>
      </div>
    </div>
  </section>

  <!-- ===== 4. TABEL DATA MAHASISWA (TABEL ASLI SIATEK) ===== -->
  <section class="space-y-4">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
              <th class="py-3.5 px-4 text-center w-14 font-semibold uppercase tracking-wider text-[11px]">No</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[200px]">Mahasiswa</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[170px]">Program Studi &amp; Angkatan</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[170px]">Kontak (WhatsApp / Email)</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[170px]">Dosen PA</th>
              <th class="py-3.5 px-4 text-center font-semibold uppercase tracking-wider text-[11px] w-28">Status</th>
              <th class="py-3.5 px-4 text-center font-semibold uppercase tracking-wider text-[11px] w-36">Aksi</th>
            </tr>
          </thead>
          <tbody id="mahasiswaBody" class="divide-y divide-slate-100 text-xs">
            <tr>
              <td colspan="7" class="py-16 text-center text-slate-400">
                <div class="spinner-slate mx-auto mb-2"></div>
                <p class="text-xs font-medium">Memuat data mahasiswa...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div id="noHasil" class="hidden rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400 mb-3">
        <i class="fas fa-user-slash"></i>
      </div>
      <h3 class="text-base font-bold text-slate-800">Tidak ada data mahasiswa</h3>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        Tidak ditemukan mahasiswa yang sesuai dengan kata kunci pencarian atau filter yang dipilih.
      </p>
    </div>

    <!-- Paginasi Footer -->
    <div id="paginationWrap" class="flex flex-wrap items-center justify-between gap-3 pt-2">
      <div class="text-xs font-semibold text-slate-700">
        <span id="infoPaginasi">0 Mahasiswa</span>
        <span class="text-slate-400 font-normal ml-1" id="lblPage"></span>
      </div>
      <div class="flex items-center gap-1" id="pgWrap">
        <!-- Tombol paginasi di-generate dinamis -->
      </div>
    </div>
  </section>

  <!-- ===== Modal Tambah / Edit Mahasiswa ===== -->
  <div class="modal-overlay" id="modalMahasiswa" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
        <h6 class="font-bold text-slate-900 text-sm flex items-center gap-2">
          <i class="fas fa-user-graduate text-orange-500"></i>
          <span id="modalMahasiswaTitle">Tambah Mahasiswa Baru</span>
        </h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>

      <form id="formMahasiswa">
        <input type="hidden" id="mMode" name="mode" value="tambah">
        <div class="p-5 space-y-3.5 max-h-[75vh] overflow-y-auto">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="mNim" class="mb-1 block text-xs font-bold text-slate-700">NIM <span class="text-rose-500">*</span></label>
              <input type="text" id="mNim" name="nim" required placeholder="Contoh: 531422001"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-mono outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
              <label for="mStatus" class="mb-1 block text-xs font-bold text-slate-700">Status Akun <span class="text-rose-500">*</span></label>
              <select id="mStatus" name="status" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <?php foreach ($daftarStatus as $stNama => $stJml): ?>
                  <option value="<?= htmlspecialchars($stNama) ?>"><?= htmlspecialchars($stNama) ?></option>
                <?php endforeach; ?>
                <?php if (empty($daftarStatus)): ?>
                  <option value="Aktif">Aktif</option>
                  <option value="Lulus">Lulus</option>
                  <option value="Cuti">Cuti</option>
                  <option value="Drop Out">Drop Out</option>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <div>
            <label for="mNama" class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" id="mNama" name="nama" required placeholder="Nama lengkap mahasiswa sesuai KTP / Ijazah"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="mProdi" class="mb-1 block text-xs font-bold text-slate-700">Program Studi <span class="text-rose-500">*</span></label>
              <select id="mProdi" name="prodi" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>">
                <?php if ($isFilterProdi): ?>
                  <option value="<?= htmlspecialchars($userProdi) ?>" selected><?= htmlspecialchars($namaProdiUser ?: $userProdi) ?></option>
                <?php else: ?>
                  <option value="">Pilih Program Studi</option>
                  <?php foreach ($daftarProdi as $p): ?>
                    <option value="<?= htmlspecialchars($p['kode']) ?>"><?= htmlspecialchars($p['nama']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div>
              <label for="mAngkatan" class="mb-1 block text-xs font-bold text-slate-700">Tahun Angkatan <span class="text-rose-500">*</span></label>
              <input type="number" id="mAngkatan" name="angkatan" required min="2000" max="2099" value="<?= date('Y') ?>"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            </div>
          </div>

          <div>
            <label for="mDosenPa" class="mb-1 block text-xs font-bold text-slate-700">Dosen Pembimbing Akademik (PA)</label>
            <select id="mDosenPa" name="dosen_pa"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
              <option value="">-- Belum Ditugaskan / Pilih Dosen PA --</option>
              <?php foreach ($daftarDosen as $dsn): ?>
                <option value="<?= htmlspecialchars($dsn['id']) ?>">
                  <?= htmlspecialchars($dsn['nama']) ?><?= !empty($dsn['nip']) ? ' (NIP: ' . htmlspecialchars($dsn['nip']) . ')' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="mEmail" class="mb-1 block text-xs font-bold text-slate-700">Alamat Email</label>
              <input type="email" id="mEmail" name="email" placeholder="mahasiswa@ung.ac.id"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
              <label for="mNoHp" class="mb-1 block text-xs font-bold text-slate-700">No. HP / WhatsApp</label>
              <input type="text" id="mNoHp" name="no_hp" placeholder="08xxxxxxxxxx"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            </div>
          </div>

          <!-- Password (hanya saat tambah baru) -->
          <div id="wrapPassword">
            <label for="mPassword" class="mb-1 block text-xs font-bold text-slate-700">Password Awal</label>
            <input type="text" id="mPassword" name="password" placeholder="Kosongkan jika ingin disamakan dengan NIM"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            <p class="text-[11px] text-slate-400 mt-1">Jika dikosongkan, password login disamakan dengan NIM mahasiswa.</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
          <button type="submit" id="btnSubmitMhs" data-text="Simpan Mahasiswa" class="px-4 py-1.5 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm">Simpan Mahasiswa</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Modal Reset Password (hanya level 9) ===== -->
  <?php if ($userLevel === 9): ?>
  <div class="modal-overlay" id="modalResetPw" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
        <h6 class="font-bold text-slate-900 text-sm flex items-center gap-2">
          <i class="fas fa-key text-amber-500"></i>
          <span>Reset Password Mahasiswa</span>
        </h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>

      <form id="formResetPw">
        <input type="hidden" id="rNim" name="nim">
        <div class="p-5 space-y-3">
          <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-xs">
            <p class="text-amber-800 font-medium">Reset password untuk akun:</p>
            <p class="font-bold text-slate-800 mt-0.5" id="rNamaText">-</p>
            <p class="font-mono text-slate-600 text-[11px]" id="rNimText">NIM: -</p>
          </div>

          <div>
            <label for="rPwBaru" class="mb-1 block text-xs font-bold text-slate-700">Password Baru (Opsional)</label>
            <input type="text" id="rPwBaru" name="password_baru" placeholder="Biarkan kosong untuk reset sama dengan NIM"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            <p class="text-[11px] text-slate-400 mt-1">Default: password disetarakan kembali dengan NIM mahasiswa.</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
          <button type="submit" id="btnSubmitResetPw" data-text="Reset Password" class="px-4 py-1.5 text-xs rounded-md bg-amber-500 hover:bg-amber-600 text-white font-semibold shadow-sm">Reset Password</button>
        </div>
      </form>
    </div>
  </div>

  <?php endif; ?>

  <!-- ===== Modal Konfirmasi Hapus (hanya level 6 dan 9) ===== -->
  <?php if (in_array($userLevel, [6, 9], true)): ?>
  <div class="modal-overlay" id="modalHapus" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-2xl text-rose-600 mb-4">
        <i class="fas fa-trash-alt"></i>
      </div>
      <h3 class="mb-1 text-base font-bold text-slate-800">Hapus Akun Mahasiswa?</h3>
      <p class="text-xs text-slate-500 mb-4">Tindakan ini akan menghapus akun mahasiswa dari sistem secara permanen.</p>

      <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-left text-xs mb-5">
        <p class="text-slate-500 font-medium">Mahasiswa:</p>
        <p class="font-bold text-slate-800 text-sm mt-0.5" id="hNamaText">-</p>
        <p class="font-mono text-slate-600 text-[11px]" id="hNimText">NIM: -</p>
      </div>

      <div class="flex justify-center gap-2">
        <button type="button" class="px-4 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
        <button type="button" id="btnKonfirmasiHapus" data-text="Ya, Hapus" class="px-4 py-2 text-xs rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-sm">Ya, Hapus</button>
      </div>
    </div>
  </div>

  <?php endif; ?>

  <!-- ===== Modal Import Excel / CSV Massal ===== -->
  <div class="modal-overlay" id="modalImport" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[92vh]">
      <!-- Header Modal -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/70">
        <div class="flex items-center gap-2.5">
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm shadow-emerald-500/20">
            <i class="fas fa-file-excel"></i>
          </div>
          <div>
            <h6 class="font-bold text-slate-900 text-sm">Import Mahasiswa Massal (Excel / CSV)</h6>
            <p class="text-[11px] text-slate-500">Unggah berkas untuk mendaftarkan mahasiswa angkatan baru sekaligus.</p>
          </div>
        </div>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>

      <!-- Body Modal -->
      <div class="p-6 overflow-y-auto space-y-5">
        
        <!-- Baris Pengaturan Target Import: Prodi & Angkatan & Duplikasi -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5">
          <!-- Pilihan Program Studi Sasaran (Wajib & Terisolasi) -->
          <div class="rounded-xl border border-orange-200 bg-orange-50/60 p-3.5 md:col-span-2">
            <label for="importTargetProdi" class="block text-xs font-bold text-orange-900 mb-1 flex items-center justify-between">
              <span>Program Studi Tujuan <span class="text-rose-500">*</span></span>
              <span class="text-[10px] font-normal text-orange-700 bg-orange-200/60 px-1.5 py-0.5 rounded">Diunggah per Prodi</span>
            </label>
            <p class="text-[11px] text-orange-800/80 mb-2">Seluruh mahasiswa dalam berkas ini otomatis dimasukkan ke program studi yang dipilih:</p>
            <select id="importTargetProdi" class="w-full rounded-lg border border-orange-300 bg-white px-3 py-2 text-xs font-bold text-slate-800 outline-none focus:border-orange-500 shadow-2xs">
              <?php if (!$isFilterProdi || $userProdi === ''): ?>
                <option value="">-- Pilih Program Studi --</option>
              <?php endif; ?>
              <?php foreach ($daftarProdi as $p): ?>
                <?php if ($isFilterProdi && $userProdi !== '' && $p['kode'] !== $userProdi && $p['nama'] !== $userProdi) continue; ?>
                <option value="<?= htmlspecialchars($p['kode']) ?>" <?= ($isFilterProdi && ($p['kode'] === $userProdi || $p['nama'] === $userProdi)) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['nama']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Opsi Tahun Angkatan -->
          <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3.5">
            <label for="importTargetAngkatan" class="block text-xs font-bold text-slate-700 mb-1">
              Tahun Angkatan <span class="text-rose-500">*</span>
            </label>
            <p class="text-[11px] text-slate-500 mb-2">Angkatan mahasiswa yang diunggah.</p>
            <input type="number" id="importTargetAngkatan" value="<?= (int)date('Y') ?>" min="2000" max="2099"
                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-800 outline-none focus:border-emerald-500 shadow-2xs">
          </div>

          <!-- Opsi Perlakuan Data Ganda -->
          <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3.5">
            <label for="importModeGanda" class="block text-xs font-bold text-slate-700 mb-1">
              Jika NIM Sudah Ada
            </label>
            <p class="text-[11px] text-slate-500 mb-2">Aksi bila NIM telah terdaftar.</p>
            <select id="importModeGanda" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-500 shadow-2xs">
              <option value="skip" selected>Lewati / Abaikan (Skip)</option>
              <option value="update">Perbarui Profil (Update)</option>
            </select>
          </div>
        </div>

        <!-- Banner Unduh Template CSV -->
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="flex items-start gap-2.5">
            <i class="fas fa-file-csv text-emerald-600 text-lg mt-0.5"></i>
            <div>
              <div class="text-xs font-bold text-emerald-950">Format Berkas Pengunggahan</div>
              <p class="text-[11px] text-emerald-800 mt-0.5">
                Gunakan template resmi dengan susunan kolom: <span class="font-mono font-semibold bg-emerald-100/70 px-1 py-0.5 rounded">NIM</span>, <span class="font-mono font-semibold bg-emerald-100/70 px-1 py-0.5 rounded">Nama Lengkap</span>, <span class="font-mono text-emerald-700 bg-emerald-100/70 px-1 py-0.5 rounded">No HP/WA</span>, <span class="font-mono text-emerald-700 bg-emerald-100/70 px-1 py-0.5 rounded">Email</span>.
              </p>
            </div>
          </div>
          <button type="button" id="btnUnduhTemplateCsv"
             class="inline-flex items-center justify-center gap-1.5 shrink-0 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs px-3.5 py-2 shadow-xs transition cursor-pointer">
            <i class="fas fa-download text-xs"></i> Unduh Template CSV
          </button>
        </div>

        <!-- Dropzone / File Picker Area -->
        <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-400 bg-slate-50 hover:bg-emerald-50/30 transition p-6 text-center cursor-pointer relative group" id="dropzoneArea">
          <input type="file" id="inputImportFile" accept=".csv,.xlsx,.xls,text/csv" class="hidden">
          <div class="flex flex-col items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white border border-slate-200 text-emerald-600 shadow-sm mb-2.5 group-hover:scale-105 transition transform">
              <i class="fas fa-file-arrow-up text-xl"></i>
            </div>
            <p class="text-sm font-bold text-slate-800" id="fileImportLabel">Pilih atau Seret (Drag & Drop) File CSV / Excel di sini</p>
            <p class="text-xs text-slate-400 mt-0.5">Format .CSV, .XLSX, atau .XLS (NIM berawalan 0 tetap aman tersimpan)</p>
            <button type="button" class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition" onclick="document.getElementById('inputImportFile').click()">
              <i class="fas fa-folder-open text-xs"></i> Telusuri Berkas
            </button>
          </div>
        </div>

        <!-- Preview & Validasi Data Table (Hidden secara default, tampil saat file dipilih) -->
        <div id="previewImportArea" class="hidden space-y-2.5">
          <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2">
            <div class="flex items-center gap-2">
              <h6 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <i class="fas fa-table-list text-emerald-600"></i>
                Pratinjau Data Mahasiswa:
              </h6>
              <span id="previewCountBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                0 baris siap diimpor
              </span>
            </div>
            <div class="text-[11px] text-slate-500 italic">
              *Password default otomatis menggunakan NIM masing-masing mahasiswa
            </div>
          </div>

          <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white max-h-52">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 text-[11px] font-bold text-slate-600 uppercase border-b border-slate-200 sticky top-0">
                <tr>
                  <th class="py-2.5 px-3 w-12 text-center">No</th>
                  <th class="py-2.5 px-3">NIM</th>
                  <th class="py-2.5 px-3">Nama Lengkap</th>
                  <th class="py-2.5 px-3">Prodi Tujuan</th>
                  <th class="py-2.5 px-3">Angkatan</th>
                  <th class="py-2.5 px-3">No. HP / WA</th>
                  <th class="py-2.5 px-3">Email</th>
                </tr>
              </thead>
              <tbody id="previewImportTbody" class="divide-y divide-slate-100 text-slate-700">
                <!-- Baris preview dinamis diisi via JS -->
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Footer Modal -->
      <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-200 bg-slate-50">
        <button type="button" class="px-4 py-2 text-xs rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold transition" data-modal-close>Batal</button>
        <button type="button" id="btnEksekusiImport" disabled data-text="Mulai Import Data" class="px-5 py-2 text-xs rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold shadow-sm shadow-emerald-600/20 transition flex items-center gap-1.5">
          <i class="fas fa-file-import text-xs"></i>
          <span>Mulai Import Data</span>
        </button>
      </div>
    </div>
  </div>

</main>

<script>
$(document).ready(function() {
  var BASE_URL = '<?= base_url() ?>';
  var PROSES_URL = BASE_URL + '/administrator/pages/pengelolaan/pengguna/mahasiswa/proses.php';
  var currentPage = 1;
  var targetHapusNim = '';
  var cariTimer = null;

  // Inisialisasi awal
  muatUlangTabel(1);

  // ================= TOAST NOTIFIKASI =================
  function toast(pesan, tipe) {
    tipe = tipe || 'info';
    var container = document.getElementById('toastContainer');
    var item = document.createElement('div');
    item.className = 'toast-item toast-' + tipe;
    var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
    item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span class="leading-tight">' + pesan + '</span><button class="toast-close">&times;</button>';
    container.appendChild(item);
    requestAnimationFrame(function(){ item.classList.add('show'); });

    var dismiss = setTimeout(function(){
      item.classList.add('hide');
      setTimeout(function(){ item.remove(); }, 350);
    }, 4000);

    $(item).find('.toast-close').on('click', function() {
      clearTimeout(dismiss);
      item.classList.add('hide');
      setTimeout(function(){ item.remove(); }, 350);
    });
  }

  // ================= LOADING BUTTON =================
  function setButtonLoading(btn, isLoading) {
    if (!btn) return;
    var $btn = $(btn);
    var text = $btn.attr('data-text') || $btn.text();

    if (isLoading) {
      $btn.addClass('is-loading').prop('disabled', true);
      $btn.html('<span class="spinner-slate"></span> ' + text);
    } else {
      $btn.removeClass('is-loading').prop('disabled', false);
      $btn.html(text);
    }
  }

  // ================= COPY NIM KE CLIPBOARD =================
  $(document).on('click', '.btnSalinNim', function(e) {
    e.stopPropagation();
    var nim = $(this).data('nim');
    if (nim) {
      navigator.clipboard.writeText(nim).then(function() {
        toast('NIM ' + nim + ' disalin ke clipboard', 'info');
      }).catch(function() {
        var tempInput = document.createElement("input");
        tempInput.value = nim;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        toast('NIM ' + nim + ' disalin ke clipboard', 'info');
      });
    }
  });

  // ================= SINKRONISASI TAMPILAN FILTER STATUS AKTIF =================
  function updateActiveStatusHighlight() {
    var curStatus = $('#fStatus').val();
    $('.btnQuickStatus').removeClass('is-active');
    if (curStatus) {
      $('.btnQuickStatus[data-status="' + curStatus + '"]').addClass('is-active');
    }
  }

  // ================= MUAT ULANG TABEL & OVERVIEW DINAMIS =================
  function muatUlangTabel(page) {
    page = page || currentPage;
    currentPage = page;

    var cari     = $('#fCari').val();
    var prodi    = $('#fProdi').val();
    var angkatan = $('#fAngkatan').val();
    var status   = $('#fStatus').val();

    updateActiveStatusHighlight();

    $('#mahasiswaBody').html(
      '<tr><td colspan="7" class="py-16 text-center text-slate-400">' +
        '<div class="spinner-slate mx-auto mb-2"></div>' +
        '<p class="text-xs font-medium">Memuat data mahasiswa...</p>' +
      '</td></tr>'
    );

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'ambil-tabel-mahasiswa': 1,
        'cari': cari,
        'prodi': prodi,
        'angkatan': angkatan,
        'status': status,
        'page': page,
        'limit': 15
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          if (res.total_filtered > 0) {
            $('#noHasil').addClass('hidden');
            $('#paginationWrap').removeClass('hidden');
            $('#mahasiswaBody').html(res.tbody);
          } else {
            $('#noHasil').removeClass('hidden');
            $('#paginationWrap').addClass('hidden');
            $('#mahasiswaBody').html(res.tbody);
          }

          var total = res.total_filtered;
          var limit = res.limit;
          var start = total > 0 ? (page - 1) * limit + 1 : 0;
          var end   = Math.min(page * limit, total);

          $('#infoPaginasi').text(total.toLocaleString('id-ID') + ' Mahasiswa Terdaftar');
          $('#lblPage').text('Menampilkan ' + start + ' - ' + end + ' dari ' + total.toLocaleString('id-ID') + ' mahasiswa (Halaman ' + res.page + ' / ' + res.total_pages + ')');

          renderPagination(res.page, res.total_pages);

          // SINKRONKAN OVERVIEW BAR DENGAN LINGKUP FILTER YANG AKTIF
          if (res.stats) {
            var stTot = Number(res.stats.total) || 0;
            var stTpa = Number(res.stats.tanpa_pa) || 0;

            $('#statTotalText').text(stTot.toLocaleString('id-ID'));
            $('#statTanpaPaText').text(stTpa.toLocaleString('id-ID'));

            // Perbarui jumlah setiap status riil
            $('.stat-count-num').each(function() {
              var key = $(this).data('status-key');
              var cnt = (res.stats.status_list && res.stats.status_list[key] !== undefined) ? Number(res.stats.status_list[key]) : 0;
              $(this).text(cnt.toLocaleString('id-ID'));

              // Perbarui lebar bar proporsi sesuai data terfilter
              var pct = stTot > 0 ? Math.round((cnt / stTot) * 100) : 0;
              $('.bar-seg[data-status-key="' + key + '"]').css('width', pct + '%').attr('title', key + ': ' + cnt.toLocaleString('id-ID'));
            });
          }

        } else {
          toast(res.pesan || 'Gagal memuat data', 'error');
        }
      },
      error: function() {
        toast('Terjadi kesalahan pada koneksi server', 'error');
      }
    });
  }

  // ================= RENDER PAGINATION =================
  function renderPagination(cur, total) {
    var $wrap = $('#pgWrap');
    $wrap.empty();

    if (total <= 1) return;

    var $prev = $('<button type="button" class="pg-btn"><i class="fas fa-chevron-left text-[10px]"></i></button>');
    if (cur <= 1) {
      $prev.prop('disabled', true);
    } else {
      $prev.on('click', function() { muatUlangTabel(cur - 1); });
    }
    $wrap.append($prev);

    var startPage = Math.max(1, cur - 2);
    var endPage   = Math.min(total, cur + 2);

    if (startPage > 1) {
      $wrap.append($('<button type="button" class="pg-btn">1</button>').on('click', function() { muatUlangTabel(1); }));
      if (startPage > 2) {
        $wrap.append('<span class="text-xs text-slate-400 px-1">...</span>');
      }
    }

    for (var p = startPage; p <= endPage; p++) {
      (function(pageIndex) {
        var $btn = $('<button type="button" class="pg-btn ' + (pageIndex === cur ? 'on' : '') + '">' + pageIndex + '</button>');
        $btn.on('click', function() { muatUlangTabel(pageIndex); });
        $wrap.append($btn);
      })(p);
    }

    if (endPage < total) {
      if (endPage < total - 1) {
        $wrap.append('<span class="text-xs text-slate-400 px-1">...</span>');
      }
      $wrap.append($('<button type="button" class="pg-btn">' + total + '</button>').on('click', function() { muatUlangTabel(total); }));
    }

    var $next = $('<button type="button" class="pg-btn"><i class="fas fa-chevron-right text-[10px]"></i></button>');
    if (cur >= total) {
      $next.prop('disabled', true);
    } else {
      $next.on('click', function() { muatUlangTabel(cur + 1); });
    }
    $wrap.append($next);
  }

  // ================= EVENT LISTENER FILTER =================
  $('#fCari').on('input', function() {
    clearTimeout(cariTimer);
    cariTimer = setTimeout(function() {
      muatUlangTabel(1);
    }, 350);
  });

  $('#fProdi, #fAngkatan, #fStatus').on('change', function() {
    muatUlangTabel(1);
  });

  // Klik Cepat pada Chip Status Riil untuk Filter Langsung
  $(document).on('click', '.btnQuickStatus', function() {
    var st = $(this).data('status');
    // Jika chip yang sama diklik lagi, batalkan filter status (kembali ke semua)
    if ($('#fStatus').val() === String(st)) {
      $('#fStatus').val('');
    } else {
      $('#fStatus').val(st);
    }
    muatUlangTabel(1);
  });

  $('#btnReset').on('click', function() {
    $('#fCari').val('');
    $('#fProdi').val('<?= $isFilterProdi ? $userProdi : "" ?>');
    $('#fAngkatan').val('');
    $('#fStatus').val('');
    muatUlangTabel(1);
  });

  $('#btnSegarkan').on('click', function() {
    muatUlangTabel(currentPage);
    toast('Data mahasiswa diperbarui', 'info');
  });

  // ================= MODAL HANDLERS =================
  function bukaModal(id) {
    $(id).addClass('show');
  }
  function tutupModal() {
    $('.modal-overlay').removeClass('show');
  }
  $('[data-modal-close]').on('click', tutupModal);
  $('.modal-overlay').on('click', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
      tutupModal();
    }
  });

  // Buka Modal Tambah
  $('#btnTambah').on('click', function() {
    $('#formMahasiswa')[0].reset();
    $('#mMode').val('tambah');
    $('#modalMahasiswaTitle').text('Tambah Mahasiswa Baru');
    $('#mNim').prop('readonly', false).removeClass('bg-slate-100 cursor-not-allowed').addClass('bg-slate-50');
    $('#wrapPassword').show();
    $('#mStatus').val('Aktif');
    bukaModal('#modalMahasiswa');
  });

  // Buka Modal Edit
  $(document).on('click', '.btnEdit', function() {
    var $btn = $(this);
    var nim       = $btn.data('nim') || '';
    var nama      = $btn.data('nama') || '';
    var prodi     = String($btn.data('prodi') || '').trim();
    var namaProdi = String($btn.data('nama-prodi') || '').trim();
    var angkatan  = $btn.data('angkatan') || '';
    var status    = $btn.data('status') || 'Aktif';
    var dosenPa   = $btn.data('dosen-pa') || '';
    var email     = $btn.data('email') || '';
    var noHp      = $btn.data('nohp') || '';

    $('#mMode').val('edit');
    $('#modalMahasiswaTitle').text('Edit Data Mahasiswa');
    $('#mNim').val(nim).prop('readonly', true).addClass('bg-slate-100 cursor-not-allowed').removeClass('bg-slate-50');
    $('#mNama').val(nama);
    $('#mAngkatan').val(angkatan);
    $('#mStatus').val(status);
    $('#mDosenPa').val(dosenPa);
    $('#mEmail').val(email);
    $('#mNoHp').val(noHp);
    $('#wrapPassword').hide();

    // Normalisasi alias prodi legacy (s1teknikomputer -> s1teknikkomputer)
    if (prodi === 's1teknikomputer') {
      prodi = 's1teknikkomputer';
    }

    // Set pilihan dropdown Program Studi
    $('#mProdi').val(prodi);

    // Jika belum terpilih (misal datanya nama lengkap atau beda format), cocokkan secara cerdas
    if (!$('#mProdi').val()) {
      $('#mProdi option').each(function() {
        var optVal  = String($(this).val() || '').trim();
        var optText = String($(this).text() || '').trim().toLowerCase();
        if (
          optVal.toLowerCase() === prodi.toLowerCase() ||
          optText === prodi.toLowerCase() ||
          (namaProdi && optText === namaProdi.toLowerCase())
        ) {
          $('#mProdi').val(optVal);
          return false;
        }
      });
    }

    bukaModal('#modalMahasiswa');
  });

  // Submit Form Tambah / Edit
  $('#formMahasiswa').on('submit', function(e) {
    e.preventDefault();
    var mode = $('#mMode').val();
    var keyAksi = (mode === 'tambah') ? 'tambah-data-mahasiswa' : 'edit-data-mahasiswa';
    var btn = document.getElementById('btnSubmitMhs');

    setButtonLoading(btn, true);

    var dataForm = $(this).serialize() + '&' + keyAksi + '=1';

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: dataForm,
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          toast(res.pesan, 'success');
          tutupModal();
          muatUlangTabel();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function() {
        toast('Terjadi kesalahan pada server', 'error');
      },
      complete: function() {
        setButtonLoading(btn, false);
      }
    });
  });

  // Buka Modal Reset Password
  $(document).on('click', '.btnResetPw', function() {
    var nim  = $(this).data('nim') || '';
    var nama = $(this).data('nama') || '';

    $('#rNim').val(nim);
    $('#rNamaText').text(nama);
    $('#rNimText').text('NIM: ' + nim);
    $('#rPwBaru').val('');
    bukaModal('#modalResetPw');
  });

  // Submit Form Reset Password
  $('#formResetPw').on('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSubmitResetPw');
    setButtonLoading(btn, true);

    var dataForm = $(this).serialize() + '&reset-password-mahasiswa=1';

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: dataForm,
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          toast(res.pesan, 'success');
          tutupModal();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function() {
        toast('Terjadi kesalahan pada server', 'error');
      },
      complete: function() {
        setButtonLoading(btn, false);
      }
    });
  });

  // Buka Modal Hapus
  $(document).on('click', '.btnHapus', function() {
    targetHapusNim = $(this).data('nim') || '';
    var nama = $(this).data('nama') || '';

    $('#hNamaText').text(nama);
    $('#hNimText').text('NIM: ' + targetHapusNim);
    bukaModal('#modalHapus');
  });

  // Konfirmasi Hapus Mahasiswa
  $('#btnKonfirmasiHapus').on('click', function() {
    if (!targetHapusNim) return;
    var btn = this;
    setButtonLoading(btn, true);

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'hapus-data-mahasiswa': 1,
        'nim': targetHapusNim
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          toast(res.pesan, 'success');
          tutupModal();
          muatUlangTabel();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function() {
        toast('Terjadi kesalahan pada server', 'error');
      },
      complete: function() {
        setButtonLoading(btn, false);
        targetHapusNim = '';
      }
    });
  });

  // ================= 8. FITUR IMPORT EXCEL / CSV MASSAL =================
  var fileImportTerpilih = null;

  // Buka modal import
  $('#btnBukaImport').on('click', function() {
    fileImportTerpilih = null;
    $('#inputImportFile').val('');
    $('#fileImportLabel').text('Pilih atau Drag & Drop Berkas Excel (.xlsx, .xls) / CSV di sini');
    $('#previewImportArea').addClass('hidden');
    $('#previewImportTbody').empty();
    $('#previewCountBadge').text('0 baris siap diproses');
    $('#btnEksekusiImport').prop('disabled', true);
    bukaModal('#modalImport');
  });

  // Handler Download Template CSV via JavaScript Blob (Pasti berhasil tanpa terpengaruh rewrite URL/sesi)
  $('#btnUnduhTemplateCsv').on('click', function() {
    // Header dan contoh data (UTF-8 BOM agar rapi langsung di Excel)
    var baris = [
      'NIM;Nama Lengkap;No HP/WA;Email',
      '0531427001;Fauzan Ahmad;081234567890;fauzan@ung.ac.id',
      '531427002;Dewi Lestari;082198765432;dewi.lestari@ung.ac.id',
      '0521427003;Rahmat Hidayat;085211223344;rahmat@ung.ac.id'
    ];
    var csvContent = '\uFEFF' + baris.join('\r\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'template_import_mahasiswa_siatek.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    toast('Template CSV berhasil diunduh.', 'success');
  });

  // Drag & drop listener
  var dropzone = document.getElementById('dropzoneArea');
  if (dropzone) {
    ['dragenter', 'dragover'].forEach(function(eventName) {
      dropzone.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('border-emerald-500', 'bg-emerald-50/50');
      }, false);
    });

    ['dragleave', 'drop'].forEach(function(eventName) {
      dropzone.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-emerald-500', 'bg-emerald-50/50');
      }, false);
    });

    dropzone.addEventListener('drop', function(e) {
      var dt = e.dataTransfer;
      var files = dt.files;
      if (files && files.length > 0) {
        handleFileImport(files[0]);
      }
    }, false);
  }

  // File input change
  $('#inputImportFile').on('change', function() {
    if (this.files && this.files.length > 0) {
      handleFileImport(this.files[0]);
    }
  });

  function handleFileImport(file) {
    var ext = file.name.split('.').pop().toLowerCase();
    if (!['csv', 'xlsx', 'xls'].includes(ext)) {
      toast('Format file tidak didukung. Mohon gunakan format .csv, .xlsx, atau .xls', 'error');
      return;
    }

    fileImportTerpilih = file;
    $('#fileImportLabel').html('<span class="text-emerald-700 font-bold">' + file.name + '</span> (' + (file.size / 1024).toFixed(1) + ' KB)');

    // Baca pratinjau jika file CSV langsung via FileReader di browser
    if (ext === 'csv') {
      var reader = new FileReader();
      reader.onload = function(e) {
        var text = e.target.result;
        renderPreviewCsv(text);
      };
      reader.readAsText(file);
    } else {
      // Untuk xlsx/xls, tampilkan info siap import
      $('#previewImportArea').removeClass('hidden');
      $('#previewCountBadge').text('File Excel Terpilih');
      $('#previewImportTbody').html('<tr><td colspan="7" class="py-4 text-center text-slate-500 italic"><i class="fas fa-file-excel text-emerald-600 mr-1.5"></i> File Excel "' + file.name + '" siap diuraikan dan diproses oleh server.</td></tr>');
      $('#btnEksekusiImport').prop('disabled', false);
    }
  }

  function toTitleCase(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/(?:^|\s|-|\.)\S/g, function(char) {
      return char.toUpperCase();
    });
  }

  function renderPreviewCsv(csvText) {
    var lines = csvText.split(/\r\n|\n/).filter(function(line) { return line.trim() !== ''; });
    if (lines.length <= 1) {
      toast('File CSV tampaknya kosong atau hanya berisi baris judul.', 'error');
      $('#btnEksekusiImport').prop('disabled', true);
      return;
    }

    var delimiter = lines[0].indexOf(';') > -1 ? ';' : ',';
    var rows = [];
    var startIndex = 0;

    // Cek apakah baris 0 adalah header (ada tulisan nim/nama)
    var headerRow = lines[0].toLowerCase();
    if (headerRow.includes('nim') || headerRow.includes('nama')) {
      startIndex = 1;
    }

    for (var i = startIndex; i < lines.length; i++) {
      var cols = lines[i].split(delimiter).map(function(c) { return c.replace(/^["']|["']$/g, '').trim(); });
      if (cols.length >= 2 && cols[0] !== '') {
        rows.push(cols);
      }
    }

    if (rows.length === 0) {
      toast('Tidak ada data baris yang valid ditemukan dalam CSV.', 'error');
      $('#btnEksekusiImport').prop('disabled', true);
      return;
    }

    var namaProdiTujuan = $('#importTargetProdi option:selected').text().trim() || 'Prodi Terpilih';
    var thAngkatan = $('#importTargetAngkatan').val() || '<?= date('Y') ?>';

    $('#previewCountBadge').text(rows.length + ' mahasiswa ditemukan');
    var tbodyHtml = '';
    var previewLimit = Math.min(rows.length, 10);

    for (var r = 0; r < previewLimit; r++) {
      var d = rows[r];
      // Jaga NIM sebagai teks murni (termasuk awalan 0)
      var nim = String(d[0] || '').trim();
      var nama = toTitleCase(String(d[1] || '').trim());
      var noHp = String(d[2] || '').trim();
      var email = String(d[3] || '').trim();

      tbodyHtml += '<tr>' +
        '<td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]">' + (r + 1) + '</td>' +
        '<td class="py-2 px-3 font-mono font-bold text-slate-800">' + (nim || '-') + '</td>' +
        '<td class="py-2 px-3 font-medium text-slate-800">' + (nama || '-') + '</td>' +
        '<td class="py-2 px-3 text-slate-600 font-semibold">' + namaProdiTujuan + '</td>' +
        '<td class="py-2 px-3 text-slate-600">' + thAngkatan + '</td>' +
        '<td class="py-2 px-3 text-slate-500 font-mono text-[11px]">' + (noHp || '-') + '</td>' +
        '<td class="py-2 px-3 text-slate-500 text-[11px]">' + (email || '-') + '</td>' +
      '</tr>';
    }

    if (rows.length > previewLimit) {
      tbodyHtml += '<tr><td colspan="7" class="py-2.5 px-3 text-center text-slate-400 text-[11px] italic bg-slate-50">... dan ' + (rows.length - previewLimit) + ' mahasiswa lainnya akan diproses ke prodi ini</td></tr>';
    }

    $('#previewImportTbody').html(tbodyHtml);
    $('#previewImportArea').removeClass('hidden');
    $('#btnEksekusiImport').prop('disabled', false);
  }

  // Update preview bila operator mengganti prodi atau angkatan di modal
  $('#importTargetProdi, #importTargetAngkatan').on('change', function() {
    if (fileImportTerpilih && fileImportTerpilih.name.toLowerCase().endsWith('.csv')) {
      var reader = new FileReader();
      reader.onload = function(e) {
        renderPreviewCsv(e.target.result);
      };
      reader.readAsText(fileImportTerpilih);
    }
  });

  // Eksekusi Import Mahasiswa
  $('#btnEksekusiImport').on('click', function() {
    var prodiPilihan = $('#importTargetProdi').val() || '';
    if (!prodiPilihan) {
      toast('Wajib memilih Program Studi tujuan import!', 'error');
      $('#importTargetProdi').focus();
      return;
    }

    var angkatanPilihan = $('#importTargetAngkatan').val() || '';
    if (!angkatanPilihan) {
      toast('Tahun angkatan wajib diisi!', 'error');
      $('#importTargetAngkatan').focus();
      return;
    }

    if (!fileImportTerpilih) {
      toast('Silakan pilih berkas Excel atau CSV terlebih dahulu.', 'error');
      return;
    }

    var btn = this;
    setButtonLoading(btn, true);

    var formData = new FormData();
    formData.append('import-mahasiswa-massal', 1);
    formData.append('file_import', fileImportTerpilih);
    formData.append('prodi', prodiPilihan);
    formData.append('angkatan', angkatanPilihan);
    formData.append('mode_ganda', $('#importModeGanda').val() || 'skip');

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          toast(res.pesan, 'success');
          tutupModal();
          muatUlangTabel();
        } else {
          toast(res.pesan || 'Gagal memproses import data', 'error');
        }
      },
      error: function(xhr, status, error) {
        var pesanError = 'Gagal menghubungi server saat memproses berkas import.';
        if (xhr.responseJSON && xhr.responseJSON.pesan) {
          pesanError = xhr.responseJSON.pesan;
        } else if (xhr.responseText) {
          // Bersihkan tag HTML jika PHP melempar pesan error/warning
          var cleanText = xhr.responseText.replace(/<[^>]+>/g, '').trim();
          if (cleanText) {
            pesanError = cleanText.length > 150 ? cleanText.substring(0, 150) + '...' : cleanText;
          }
        }
        toast(pesanError, 'error');
      },
      complete: function() {
        setButtonLoading(btn, false);
      }
    });
  });
});
</script>
