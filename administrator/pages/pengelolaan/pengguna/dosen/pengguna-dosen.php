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
if ($isFilterProdi && $userProdi !== '') {
    $whereProdiAwal = "WHERE (prodi = '$userProdi' OR prodi IN (SELECT nama FROM prodi WHERE kode = '$userProdi'))";
}

// 1. Total Dosen & Staf
$qTotal = query("SELECT COUNT(*) AS total FROM dosen $whereProdiAwal");
$totalDosen = (int)(mysqli_fetch_assoc($qTotal)['total'] ?? 0);

// 2. Dosen Belum Memiliki NIDN
$whereTanpaNidn = $whereProdiAwal ? "$whereProdiAwal AND (nidn IS NULL OR nidn = '' OR nidn = '0')" : "WHERE (nidn IS NULL OR nidn = '' OR nidn = '0')";
$qTanpaNidn = query("SELECT COUNT(*) AS total FROM dosen $whereTanpaNidn");
$totalTanpaNidn = (int)(mysqli_fetch_assoc($qTanpaNidn)['total'] ?? 0);

// 3. Rincian status murni dari database (aktif, tidak aktif)
$daftarStatus = [];
$qStatus = query("SELECT status, COUNT(*) AS jml FROM dosen $whereProdiAwal GROUP BY status ORDER BY jml DESC");
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

// Palet warna untuk status bar & chips
function get_status_dosen_style($statusName) {
    $st = strtolower($statusName);
    if ($st === 'aktif') {
        return ['dot' => 'bg-emerald-500', 'bg' => 'hover:bg-emerald-50 hover:border-emerald-300', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500'];
    } elseif ($st === 'tidak aktif' || str_contains($st, 'non')) {
        return ['dot' => 'bg-rose-500', 'bg' => 'hover:bg-rose-50 hover:border-rose-300', 'text' => 'text-rose-700', 'bar' => 'bg-rose-500'];
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
        <i class="fas fa-chalkboard-user text-xl"></i>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl font-bold text-slate-800">Pengguna Dosen &amp; Staf</h1>
          <?php if ($isFilterProdi && $namaProdiUser !== ''): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-800">
              <i class="fas fa-building-columns mr-1 text-[10px]"></i> <?= tampil($namaProdiUser) ?>
            </span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Manajemen akun pengguna dosen dan tenaga kependidikan: profil, peran hak akses, kontak, dan reset password.</p>
      </div>
    </div>

    <!-- Tombol Aksi Kanan -->
    <div class="flex items-center gap-2">
      <button type="button" id="btnSegarkan" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition" title="Segarkan Data">
        <i class="fas fa-rotate text-slate-500"></i>
        <span>Segarkan</span>
      </button>

      <button type="button" id="btnTambah" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 hover:bg-orange-600 px-4 py-2 text-xs font-bold text-white shadow-sm shadow-orange-500/20 transition">
        <i class="fas fa-user-plus text-xs"></i>
        <span>Tambah Dosen</span>
      </button>
    </div>
  </section>

  <!-- ===== 2. Panel Ringkasan Terpadu (Menyesuaikan Filter Aktif Secara Dinamis) ===== -->
  <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      
      <!-- Sisi Kiri: Angka Total Sesuai Filter & Visual Distribution Bar -->
      <div class="flex items-center gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-lg">
          <i class="fas fa-id-badge"></i>
        </div>
        <div>
          <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-slate-900" id="statTotalText"><?= number_format($totalDosen, 0, ',', '.') ?></span>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Terdaftar</span>
          </div>
          <!-- Bar Komposisi Dinamis Sesuai Lingkup Filter Aktif -->
          <div class="w-60 sm:w-80 h-2 bg-slate-100 rounded-full overflow-hidden flex mt-2 shadow-inner" id="barKomposisi" title="Distribusi status dosen riil">
            <?php foreach ($daftarStatus as $stNama => $stJml): 
              $pct = $totalDosen > 0 ? round(($stJml / $totalDosen) * 100) : 0;
              $style = get_status_dosen_style($stNama);
            ?>
              <div class="h-full <?= $style['bar'] ?> bar-seg transition-all duration-300" 
                   data-status-key="<?= htmlspecialchars($stNama) ?>" 
                   style="width: <?= $pct ?>%;" 
                   title="<?= htmlspecialchars(ucfirst($stNama)) ?>: <?= number_format($stJml, 0, ',', '.') ?> dosen"></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Sisi Kanan: Status Chips Dinamis Terintegrasi -->
      <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
        
        <?php foreach ($daftarStatus as $stNama => $stJml): 
          $style = get_status_dosen_style($stNama);
        ?>
          <button type="button" 
                  class="btnQuickStatus inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-medium text-slate-700 transition <?= $style['bg'] ?>"
                  data-status="<?= htmlspecialchars($stNama) ?>" 
                  title="Klik untuk filter status <?= htmlspecialchars(ucfirst($stNama)) ?>">
            <span class="h-2 w-2 rounded-full <?= $style['dot'] ?>"></span>
            <span><?= htmlspecialchars(ucfirst($stNama)) ?></span>
            <span class="font-bold <?= $style['text'] ?> stat-count-num" data-status-key="<?= htmlspecialchars($stNama) ?>"><?= number_format($stJml, 0, ',', '.') ?></span>
          </button>
        <?php endforeach; ?>

        <!-- Indikator Dosen Tanpa NIDN -->
        <button type="button" 
                class="inline-flex items-center gap-2 rounded-xl border border-dashed border-amber-300 bg-amber-50/60 px-3 py-2 text-xs font-medium text-amber-800 transition hover:bg-amber-100/60"
                id="btnHighlightTanpaNidn"
                title="Dosen yang belum mengisi NIDN">
          <i class="fas fa-triangle-exclamation text-amber-500 text-xs"></i>
          <span>Tanpa NIDN</span>
          <span class="font-bold text-amber-700" id="statTanpaNidnText"><?= number_format($totalTanpaNidn, 0, ',', '.') ?></span>
        </button>

      </div>
    </div>
  </section>

  <!-- ===== 3. Filter & Toolbar Pencarian ===== -->
  <section class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-xs">
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
      
      <!-- Input Pencarian -->
      <div class="relative flex-1">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" id="fCari" placeholder="Cari nama, NIP, NIDN, email, atau jabatan dosen..."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
      </div>

      <!-- Dropdown Filter Toolbar -->
      <div class="flex flex-wrap items-center gap-2">
        
        <!-- Filter Prodi (Hanya jika admin global/tidak terkunci prodi) -->
        <?php if (!$isFilterProdi || $userProdi === ''): ?>
          <select id="fProdi" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:border-orange-400 focus:bg-white">
            <option value="">Semua Program Studi</option>
            <?php foreach ($daftarProdi as $p): ?>
              <option value="<?= htmlspecialchars($p['kode']) ?>"><?= htmlspecialchars($p['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input type="hidden" id="fProdi" value="<?= htmlspecialchars($userProdi) ?>">
        <?php endif; ?>

        <!-- Filter Peran / Hak Akses (Level) -->
        <select id="fLevel" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:border-orange-400 focus:bg-white">
          <option value="">Semua Peran / Hak Akses</option>
          <option value="1">Dosen Pengajar</option>
          <option value="5">Ketua Program Studi</option>
          <option value="6">Pengelola Jurusan (Kajur/Sekjur)</option>
          <option value="3">Operator Prodi</option>
          <option value="4">Operator Jurusan</option>
          <option value="9">Superadmin</option>
        </select>

        <!-- Filter Status Akun -->
        <select id="fStatus" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:border-orange-400 focus:bg-white">
          <option value="">Semua Status</option>
          <?php foreach ($daftarStatus as $stNama => $stJml): ?>
            <option value="<?= htmlspecialchars($stNama) ?>"><?= htmlspecialchars(ucfirst($stNama)) ?></option>
          <?php endforeach; ?>
        </select>

        <!-- Tombol Reset Filter -->
        <button type="button" id="btnReset" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition shrink-0" title="Reset Semua Filter">
          <i class="fas fa-times"></i>
          <span>Reset</span>
        </button>
      </div>
    </div>
  </section>

  <!-- ===== 4. TABEL DATA DOSEN (TABEL ASLI SIATEK) ===== -->
  <section class="space-y-4">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
              <th class="py-3.5 px-4 text-center w-14 font-semibold uppercase tracking-wider text-[11px]">No</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[210px]">Dosen / Staf</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[180px]">Prodi &amp; Jabatan</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[160px]">Hak Akses / Peran</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider text-[11px] min-w-[160px]">Kontak (WhatsApp / Email)</th>
              <th class="py-3.5 px-4 text-center font-semibold uppercase tracking-wider text-[11px] w-28">Status</th>
              <th class="py-3.5 px-4 text-center font-semibold uppercase tracking-wider text-[11px] w-36">Aksi</th>
            </tr>
          </thead>
          <tbody id="dosenBody" class="divide-y divide-slate-100 text-xs">
            <tr>
              <td colspan="7" class="py-16 text-center text-slate-400">
                <div class="spinner-slate mx-auto mb-2"></div>
                <p class="text-xs font-medium">Memuat data dosen...</p>
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
      <h3 class="text-base font-bold text-slate-800">Tidak ada data dosen ditemukan</h3>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        Tidak ditemukan akun dosen atau staf yang sesuai dengan kata kunci pencarian atau filter yang dipilih.
      </p>
    </div>

    <!-- Paginasi Footer -->
    <div id="paginationWrap" class="flex flex-wrap items-center justify-between gap-3 pt-2">
      <div class="text-xs font-semibold text-slate-700">
        <span id="infoPaginasi">0 Dosen</span>
        <span class="text-slate-400 font-normal ml-1" id="lblPage"></span>
      </div>
      <div class="flex items-center gap-1" id="pgWrap">
        <!-- Tombol paginasi di-generate dinamis -->
      </div>
    </div>
  </section>

  <!-- ===== Modal Tambah / Edit Dosen ===== -->
  <div class="modal-overlay" id="modalDosen" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
        <h6 class="font-bold text-slate-900 text-sm flex items-center gap-2">
          <i class="fas fa-chalkboard-user text-orange-500"></i>
          <span id="modalDosenTitle">Tambah Dosen Baru</span>
        </h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>

      <form id="formDosen">
        <input type="hidden" id="dMode" name="mode" value="tambah">
        <input type="hidden" id="dId" name="id">

        <div class="p-5 space-y-3.5 max-h-[75vh] overflow-y-auto">
          
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="dNip" class="mb-1 block text-xs font-bold text-slate-700">NIP <span class="text-rose-500">*</span></label>
              <input type="text" id="dNip" name="nip" required placeholder="Contoh: 19840110..."
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-mono outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
              <label for="dNidn" class="mb-1 block text-xs font-bold text-slate-700">NIDN</label>
              <input type="text" id="dNidn" name="nidn" placeholder="Nomor Induk Dosen Nasional"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-mono outline-none focus:border-orange-400 focus:bg-white">
            </div>
          </div>

          <div>
            <label for="dNama" class="mb-1 block text-xs font-bold text-slate-700">Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span></label>
            <input type="text" id="dNama" name="nama" required placeholder="Contoh: Dr. Abdul Halim Paris, S.T., M.T."
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="dProdi" class="mb-1 block text-xs font-bold text-slate-700">Program Studi</label>
              <select id="dProdi" name="prodi"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="">-- Jurusan Teknik Elektro (Umum) --</option>
                <?php foreach ($daftarProdi as $p): ?>
                  <option value="<?= htmlspecialchars($p['kode']) ?>"><?= htmlspecialchars($p['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="dLevel" class="mb-1 block text-xs font-bold text-slate-700">Hak Akses / Peran <span class="text-rose-500">*</span></label>
              <select id="dLevel" name="level" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="1">Dosen Pengajar</option>
                <option value="5">Ketua Program Studi (Kaprodi)</option>
                <option value="6">Pengelola Jurusan (Kajur/Sekjur)</option>
                <option value="3">Operator Program Studi</option>
                <option value="4">Operator Jurusan</option>
                <option value="9">Superadmin</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="dStatus" class="mb-1 block text-xs font-bold text-slate-700">Status Akun <span class="text-rose-500">*</span></label>
              <select id="dStatus" name="status" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="aktif">Aktif</option>
                <option value="tidak aktif">Tidak Aktif</option>
              </select>
            </div>
            <div>
              <label for="dPosisi" class="mb-1 block text-xs font-bold text-slate-700">Posisi Pegawai</label>
              <select id="dPosisi" name="posisi"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="dosen">Dosen</option>
                <option value="operator">Operator / Staf Administrasi</option>
                <option value="laboran">Laboran</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div>
              <label for="dJafung" class="mb-1 block text-xs font-bold text-slate-700">Jabatan Fungsional</label>
              <select id="dJafung" name="jabatan_fungsional"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="">-- Belum Ada --</option>
                <option value="Tenaga Pengajar">Tenaga Pengajar</option>
                <option value="Asisten Ahli">Asisten Ahli</option>
                <option value="Lektor">Lektor</option>
                <option value="Lektor Kepala">Lektor Kepala</option>
                <option value="Guru Besar">Guru Besar / Profesor</option>
                <option value="Laboran">Laboran</option>
              </select>
            </div>
            <div>
              <label for="dKepegawaian" class="mb-1 block text-xs font-bold text-slate-700">Kepegawaian</label>
              <select id="dKepegawaian" name="status_kepegawaian"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="">PNS</option>
                <option value="PPPK">PPPK</option>
                <option value="Non-PNS">Non-PNS / Kontrak</option>
              </select>
            </div>
            <div>
              <label for="dKeterangan" class="mb-1 block text-xs font-bold text-slate-700">Keterangan Dosen</label>
              <select id="dKeterangan" name="keterangan"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
                <option value="dtps">DTPS (Homebase)</option>
                <option value="dosen luar">Dosen Luar</option>
                <option value="">Lainnya</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="dEmail" class="mb-1 block text-xs font-bold text-slate-700">Alamat Email</label>
              <input type="email" id="dEmail" name="email" placeholder="dosen@ung.ac.id"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <div>
              <label for="dNoTelp" class="mb-1 block text-xs font-bold text-slate-700">No. HP / WhatsApp</label>
              <input type="text" id="dNoTelp" name="no_telp" placeholder="08xxxxxxxxxx"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            </div>
          </div>

          <!-- Password Awal (Hanya saat tambah akun) -->
          <div id="wrapPassword">
            <label for="dPassword" class="mb-1 block text-xs font-bold text-slate-700">Password Awal</label>
            <input type="text" id="dPassword" name="password" placeholder="Kosongkan jika ingin disamakan dengan NIP"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            <p class="text-[11px] text-slate-400 mt-1">Jika dikosongkan, password login default disetarakan dengan NIP dosen.</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
          <button type="submit" id="btnSubmitDsn" data-text="Simpan Dosen" class="px-4 py-1.5 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold shadow-sm">Simpan Dosen</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Modal Reset Password ===== -->
  <div class="modal-overlay" id="modalResetPw" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
        <h6 class="font-bold text-slate-900 text-sm flex items-center gap-2">
          <i class="fas fa-key text-amber-500"></i>
          <span>Reset Password Dosen</span>
        </h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>

      <form id="formResetPw">
        <input type="hidden" id="rId" name="id">
        <div class="p-5 space-y-3">
          <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-xs">
            <p class="text-amber-800 font-medium">Reset password untuk akun:</p>
            <p class="font-bold text-slate-800 mt-0.5" id="rNamaText">-</p>
            <p class="font-mono text-slate-600 text-[11px]" id="rNipText">NIP: -</p>
          </div>

          <div>
            <label for="rPwBaru" class="mb-1 block text-xs font-bold text-slate-700">Password Baru (Opsional)</label>
            <input type="text" id="rPwBaru" name="password_baru" placeholder="Biarkan kosong untuk reset sama dengan NIP"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs outline-none focus:border-orange-400 focus:bg-white">
            <p class="text-[11px] text-slate-400 mt-1">Default: password disetarakan kembali dengan NIP dosen.</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
          <button type="submit" id="btnSubmitResetPw" data-text="Reset Password" class="px-4 py-1.5 text-xs rounded-md bg-amber-500 hover:bg-amber-600 text-white font-semibold shadow-sm">Reset Password</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Modal Konfirmasi Hapus ===== -->
  <div class="modal-overlay" id="modalHapus" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-2xl text-rose-600 mb-4">
        <i class="fas fa-trash-alt"></i>
      </div>
      <h3 class="mb-1 text-base font-bold text-slate-800">Hapus Akun Dosen?</h3>
      <p class="text-xs text-slate-500 mb-4">Tindakan ini akan menghapus akun dosen/staf dari sistem secara permanen.</p>

      <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-left text-xs mb-5">
        <p class="text-slate-500 font-medium">Dosen / Staf:</p>
        <p class="font-bold text-slate-800 text-sm mt-0.5" id="hNamaText">-</p>
        <p class="font-mono text-slate-600 text-[11px]" id="hNipText">NIP: -</p>
      </div>

      <div class="flex justify-center gap-2">
        <button type="button" class="px-4 py-2 text-xs rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
        <button type="button" id="btnKonfirmasiHapus" data-text="Ya, Hapus" class="px-4 py-2 text-xs rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold shadow-sm">Ya, Hapus</button>
      </div>
    </div>
  </div>

</main>

<script>
$(document).ready(function() {
  var BASE_URL = '<?= base_url() ?>';
  var PROSES_URL = BASE_URL + '/administrator/pages/pengelolaan/pengguna/dosen/proses.php';
  var currentPage = 1;
  var targetHapusId = '';
  var cariTimer = null;

  // Inisialisasi awal tabel
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

  // ================= COPY TEKS KE CLIPBOARD =================
  $(document).on('click', '.btnSalinTeks', function(e) {
    e.stopPropagation();
    var teks = $(this).data('teks');
    if (teks) {
      navigator.clipboard.writeText(teks).then(function() {
        toast('Teks disalin ke clipboard: ' + teks, 'info');
      }).catch(function() {
        var tempInput = document.createElement("input");
        tempInput.value = teks;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        toast('Teks disalin ke clipboard: ' + teks, 'info');
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

    var cari   = $('#fCari').val();
    var prodi  = $('#fProdi').val();
    var level  = $('#fLevel').val();
    var status = $('#fStatus').val();

    updateActiveStatusHighlight();

    $('#dosenBody').html(
      '<tr><td colspan="7" class="py-16 text-center text-slate-400">' +
        '<div class="spinner-slate mx-auto mb-2"></div>' +
        '<p class="text-xs font-medium">Memuat data dosen &amp; staf...</p>' +
      '</td></tr>'
    );

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'ambil-tabel-dosen': 1,
        'cari': cari,
        'prodi': prodi,
        'level': level,
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
            $('#dosenBody').html(res.tbody);
          } else {
            $('#noHasil').removeClass('hidden');
            $('#paginationWrap').addClass('hidden');
            $('#dosenBody').html(res.tbody);
          }

          var total = res.total_filtered;
          var limit = res.limit;
          var start = total > 0 ? (page - 1) * limit + 1 : 0;
          var end   = Math.min(page * limit, total);

          $('#infoPaginasi').text(total.toLocaleString('id-ID') + ' Dosen / Staf Terdaftar');
          $('#lblPage').text('Menampilkan ' + start + ' - ' + end + ' dari ' + total.toLocaleString('id-ID') + ' (Halaman ' + res.page + ' / ' + res.total_pages + ')');

          renderPagination(res.page, res.total_pages);

          // SINKRONKAN OVERVIEW BAR DENGAN LINGKUP FILTER YANG AKTIF
          if (res.stats) {
            var stTot  = Number(res.stats.total) || 0;
            var stNidn = Number(res.stats.tanpa_nidn) || 0;

            $('#statTotalText').text(stTot.toLocaleString('id-ID'));
            $('#statTanpaNidnText').text(stNidn.toLocaleString('id-ID'));

            // Perbarui angka rincian status
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

  // ================= RENDER PAGINATION BUTTONS =================
  function renderPagination(page, totalPages) {
    var $wrap = $('#pgWrap');
    $wrap.empty();

    if (totalPages <= 1) return;

    var prevDisabled = page <= 1 ? 'disabled' : '';
    $wrap.append('<button type="button" class="pg-btn pg-nav" data-page="' + (page - 1) + '" ' + prevDisabled + ' title="Halaman Sebelumnya"><i class="fas fa-chevron-left text-xs"></i></button>');

    var start = Math.max(1, page - 2);
    var end   = Math.min(totalPages, page + 2);

    if (start > 1) {
      $wrap.append('<button type="button" class="pg-btn" data-page="1">1</button>');
      if (start > 2) {
        $wrap.append('<span class="px-1 text-slate-400 text-xs font-bold">...</span>');
      }
    }

    for (var i = start; i <= end; i++) {
      var activeClass = i === page ? 'on' : '';
      $wrap.append('<button type="button" class="pg-btn ' + activeClass + '" data-page="' + i + '">' + i + '</button>');
    }

    if (end < totalPages) {
      if (end < totalPages - 1) {
        $wrap.append('<span class="px-1 text-slate-400 text-xs font-bold">...</span>');
      }
      $wrap.append('<button type="button" class="pg-btn" data-page="' + totalPages + '">' + totalPages + '</button>');
    }

    var nextDisabled = page >= totalPages ? 'disabled' : '';
    $wrap.append('<button type="button" class="pg-btn pg-nav" data-page="' + (page + 1) + '" ' + nextDisabled + ' title="Halaman Selanjutnya"><i class="fas fa-chevron-right text-xs"></i></button>');
  }

  // Paginasi click listener
  $(document).on('click', '.pg-btn:not(:disabled)', function() {
    var targetPage = parseInt($(this).data('page'), 10);
    if (targetPage && targetPage !== currentPage) {
      muatUlangTabel(targetPage);
    }
  });

  // ================= EVENT LISTENER FILTER =================
  $('#fProdi, #fLevel, #fStatus').on('change', function() {
    muatUlangTabel(1);
  });

  // Quick Status Chips click
  $(document).on('click', '.btnQuickStatus', function() {
    var st = $(this).data('status');
    var current = $('#fStatus').val();

    if (current === st) {
      $('#fStatus').val('');
    } else {
      $('#fStatus').val(st);
    }
    muatUlangTabel(1);
  });

  // Highlight tanpa NIDN
  $('#btnHighlightTanpaNidn').on('click', function() {
    $('#fCari').val('');
    $('#fStatus').val('');
    toast('Menampilkan seluruh data dosen yang belum memiliki NIDN', 'info');
    muatUlangTabel(1);
  });

  // Live Search dengan Debounce 300ms
  $('#fCari').on('input', function() {
    clearTimeout(cariTimer);
    cariTimer = setTimeout(function() {
      muatUlangTabel(1);
    }, 300);
  });

  // Reset Filter
  $('#btnReset').on('click', function() {
    $('#fCari').val('');
    if (!$('#fProdi').is('input[type="hidden"]')) {
      $('#fProdi').val('');
    }
    $('#fLevel').val('');
    $('#fStatus').val('');
    muatUlangTabel(1);
  });

  // Tombol Segarkan
  $('#btnSegarkan').on('click', function() {
    muatUlangTabel(currentPage);
    toast('Data dosen berhasil disegarkan', 'success');
  });

  // ================= MODAL CONTROLLER =================
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

  // Buka Modal Tambah Dosen
  $('#btnTambah').on('click', function() {
    $('#formDosen')[0].reset();
    $('#dMode').val('tambah');
    $('#dId').val('');
    $('#modalDosenTitle').text('Tambah Dosen Baru');
    $('#dNip').prop('readonly', false).removeClass('bg-slate-100 cursor-not-allowed').addClass('bg-slate-50');
    $('#wrapPassword').show();
    $('#dStatus').val('aktif');
    $('#dLevel').val('1');
    $('#dPosisi').val('dosen');
    $('#dKeterangan').val('dtps');
    bukaModal('#modalDosen');
  });

  // Buka Modal Edit Dosen
  $(document).on('click', '.btnEdit', function() {
    var $btn = $(this);
    var id          = $btn.data('id') || '';
    var nip         = $btn.data('nip') || '';
    var nidn        = $btn.data('nidn') || '';
    var nama        = $btn.data('nama') || '';
    var prodi       = $btn.data('prodi') || '';
    var level       = $btn.data('level') || '1';
    var status      = $btn.data('status') || 'aktif';
    var posisi      = $btn.data('posisi') || 'dosen';
    var jafung      = $btn.data('jafung') || '';
    var kepegawaian = $btn.data('kepegawaian') || '';
    var keterangan  = $btn.data('keterangan') || 'dtps';
    var email       = $btn.data('email') || '';
    var notelp      = $btn.data('notelp') || '';

    $('#dMode').val('edit');
    $('#dId').val(id);
    $('#modalDosenTitle').text('Edit Data Dosen / Staf');
    $('#dNip').val(nip).prop('readonly', false).removeClass('bg-slate-100 cursor-not-allowed').addClass('bg-slate-50');
    $('#dNidn').val(nidn);
    $('#dNama').val(nama);
    $('#dProdi').val(prodi);
    $('#dLevel').val(level);
    $('#dStatus').val(status.toLowerCase());
    $('#dPosisi').val(posisi.toLowerCase() || 'dosen');
    $('#dJafung').val(jafung);
    $('#dKepegawaian').val(kepegawaian);
    $('#dKeterangan').val(keterangan.toLowerCase());
    $('#dEmail').val(email);
    $('#dNoTelp').val(notelp);
    $('#wrapPassword').hide();

    bukaModal('#modalDosen');
  });

  // Submit Form Tambah / Edit
  $('#formDosen').on('submit', function(e) {
    e.preventDefault();
    var mode = $('#dMode').val();
    var keyAksi = (mode === 'tambah') ? 'tambah-data-dosen' : 'edit-data-dosen';
    var btn = document.getElementById('btnSubmitDsn');

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
    var id   = $(this).data('id') || '';
    var nip  = $(this).data('nip') || '';
    var nama = $(this).data('nama') || '';

    $('#rId').val(id);
    $('#rNamaText').text(nama);
    $('#rNipText').text('NIP: ' + (nip || '-'));
    $('#rPwBaru').val('');
    bukaModal('#modalResetPw');
  });

  // Submit Form Reset Password
  $('#formResetPw').on('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSubmitResetPw');
    setButtonLoading(btn, true);

    var dataForm = $(this).serialize() + '&reset-password-dosen=1';

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
    targetHapusId = $(this).data('id') || '';
    var nip  = $(this).data('nip') || '';
    var nama = $(this).data('nama') || '';

    $('#hNamaText').text(nama);
    $('#hNipText').text('NIP: ' + (nip || '-'));
    bukaModal('#modalHapus');
  });

  // Konfirmasi Hapus Dosen
  $('#btnKonfirmasiHapus').on('click', function() {
    if (!targetHapusId) return;
    var btn = this;
    setButtonLoading(btn, true);

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'hapus-data-dosen': 1,
        'id': targetHapusId
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
        targetHapusId = '';
      }
    });
  });
});
</script>
