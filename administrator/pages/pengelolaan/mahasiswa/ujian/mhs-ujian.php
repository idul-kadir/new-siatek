<?php
/**
 * ==============================================================================
 * HALAMAN PENGELOLAAN UJIAN MAHASISWA (mhs-ujian)
 * ==============================================================================
 * URL: http://localhost/redesain-siatek/administrator/mhs-ujian
 * Layout: Grid berdampingan 2 bagian (rasio 7:5)
 * 
 * Sisi Kiri (7 Bagian / lg:col-span-7):
 * - Jika BELUM ADA batch ujian terbuka: Menampilkan form pembukaan batch inline.
 * - Jika SUDAH ADA batch ujian terbuka: Menampilkan kartu batch aktif dan tabel
 *   daftar mahasiswa yang sudah dimasukkan ke dalam batch ujian tersebut.
 * 
 * Sisi Kanan (5 Bagian / lg:col-span-5):
 * - Menampilkan antrian mahasiswa yang telah siap ujian (Skripsi Hasil, Tutup,
 *   dan Seminar KP).
 * - Khusus KP: Mahasiswa wajib sudah ada penunjukkan penguji.
 * - Checkbox otomatis berstatus disabled jika belum ada batch terbuka di sebelah kiri,
 *   dan otomatis aktif saat batch ujian telah dibuka.
 * ==============================================================================
 */

require_once __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// -----------------------------------------------------------------------------
// GUARD 1: VALIDASI LOGIN PENGGUNA
// -----------------------------------------------------------------------------
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// -----------------------------------------------------------------------------
// GUARD 2: HAK AKSES OPERATOR
// Level 3: Operator Prodi, Level 4: Operator Jurusan, Level 5: Kaprodi, Level 6: Admin, Level 9: Superadmin
// -----------------------------------------------------------------------------
$userLevel = (int)($_SESSION['level'] ?? 0);
$allowedLevels = [3, 4, 6, 9];

if (!in_array($userLevel, $allowedLevels, true)) {
    http_response_code(403);
?>
<main class="content-area content-scroll">
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center max-w-lg mx-auto mt-12 shadow-sm">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-lock"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Akses Ditolak</h3>
        <p class="text-sm text-slate-600 mb-4">Anda tidak memiliki izin untuk mengakses halaman pengelolaan ujian ini.</p>
        <a href="<?= base_url() ?>/administrator/beranda" class="inline-flex items-center px-4 py-2 bg-[#1a365d] hover:bg-[#234670] text-white text-xs font-semibold rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>
</main>
<?php
    return;
}

// -----------------------------------------------------------------------------
// IDENTIFIKASI PROGRAM STUDI PENGGUNA (Khusus Level 3 & Level 5)
// -----------------------------------------------------------------------------
$isFilterProdi = ($userLevel === 3);
$userProdi     = $isFilterProdi ? get_prodi_user() : '';
$namaProdiUser = '';
if ($isFilterProdi && $userProdi !== '') {
    $qPr = query("SELECT nama FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
    if ($qPr && ($rP = mysqli_fetch_assoc($qPr))) {
        $namaProdiUser = $rP['nama'];
    }
}
?>

<!-- ===========================================================================
     CSS STYLING KHUSUS HALAMAN UJIAN
     =========================================================================== -->
<style>
  /* Area konten dapat di-scroll vertikal dengan lancar */
  .content-scroll {
    overflow-y: auto;
    min-height: 0;
  }

  /* Indikator loading spinner melingkar */
  .spinner-slate {
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(15,23,42,0.2);
    border-top-color: #f97316;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* Wadah melayang untuk notifikasi toast di sudut kanan atas */
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
  .toast-item.show { transform: translateX(0); opacity: 1; }
  .toast-item.hide { transform: translateX(120%); opacity: 0; }
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
</style>

<main class="content-area content-scroll">

  <!-- Container Notifikasi Toast -->
  <div id="toastContainer"></div>

  <!-- ===== JUDUL & HEADER HALAMAN ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-sm">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Pengelolaan Ujian Mahasiswa</h1>
          <p class="text-xs text-slate-500">
            Penetapan batch pelaksanaan ujian dan alokasi antrian mahasiswa
            <?php if ($isFilterProdi && $namaProdiUser !== ''): ?>
              &bull; <span class="font-semibold text-orange-600"><?= tampil($namaProdiUser) ?></span>
            <?php endif; ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- =========================================================================
       GRID UTAMA BERDAMPINGAN 2 BAGIAN: RASIO 7 : 5
       ========================================================================= -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

    <!-- =======================================================================
         SISI KIRI: LEBAR 7 / 12 KOLOM (lg:col-span-7)
         Menampilkan Form Buka Batch (jika belum ada) ATAU Batch Terbuka & Pesertanya
         ======================================================================= -->
    <div class="lg:col-span-7">

      <!-- KONDISI A: FORM BUKA BATCH INLINE (Tampil saat belum ada batch atau operator ingin membuka batch baru) -->
      <div id="wrapFormBukaBatch" class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-gradient-to-r from-orange-50 to-amber-50 px-5 py-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm">
                <i class="fas fa-calendar-plus text-base"></i>
              </div>
              <div>
                <h2 class="text-sm font-bold text-slate-800">Buka Batch Ujian Baru</h2>
                <p class="text-xs text-slate-500">Atur periode pelaksanaan ujian</p>
              </div>
            </div>
            <!-- Tombol Batal jika operator sebelumnya sedang melihat kartu batch -->
            <button type="button" id="btnBatalBukaBatchBaru" class="hidden text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition shadow-2xs">
              <i class="fas fa-arrow-left mr-1 text-slate-400"></i> Kembali ke Batch
            </button>
          </div>
        </div>

        <!-- Form Inline untuk Menentukan Tanggal Buka dan Tanggal Tutup -->
        <form id="formBukaBatchInline" class="p-5 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Input Tanggal Buka (Default: Hari Ini) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Mulai Buka <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_buka" id="inlineTglBuka" value="<?= date('Y-m-d') ?>" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium outline-none focus:border-orange-500 transition">
            </div>

            <!-- Input Tanggal Tutup (Default: 5 Hari ke Depan) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Penutupan <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_tutup" id="inlineTglTutup" value="<?= date('Y-m-d', strtotime('+5 days')) ?>" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium outline-none focus:border-orange-500 transition">
            </div>
          </div>

          <!-- Tombol Eksekusi Buka Batch -->
          <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" id="btnSubmitBukaBatchInline" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-orange-600">
              <i class="fas fa-check"></i> Buka Batch Ujian
            </button>
          </div>
        </form>
      </div>

      <!-- KONDISI B: BATCH TERBUKA / TERTUTUP & TABEL PESERTA (Tampil saat ADA batch) -->
      <div id="wrapBatchTerbuka" class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        
        <!-- Header Identitas Batch (Terbuka / Tertutup) -->
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm" id="iconBoxBatch">
                <i class="fas fa-folder-open text-base" id="iconBatchStatus"></i>
              </div>
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <h2 class="text-base font-bold text-slate-800" id="displayNamaBatch">Batch Ujian</h2>
                  <span id="displayBadgeStatus" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Dibuka / Aktif
                  </span>
                  <!-- Dropdown Switcher jika ada beberapa batch di sistem -->
                  <select id="selectPilihBatch" class="hidden rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 outline-none focus:border-orange-400 shadow-2xs">
                  </select>
                </div>
                <!-- Informasi Periode Tanggal & Operator Pembuat Batch -->
                <p class="text-xs text-slate-500 mt-0.5">
                  <i class="far fa-calendar-alt text-orange-500 mr-1"></i>
                  Periode: <strong class="text-slate-700" id="displayPeriodeBatch">-</strong>
                  &bull; Dibuat oleh: <span class="text-slate-600" id="displayPembuatBatch">-</span>
                </p>

                <!-- Countdown Sisa Waktu (Berstruktur, Terbaca Jelas, & Tidak Memakan Space Ekstra) -->
                <div id="wrapCountdownBaris" class="hidden mt-2.5 flex items-center gap-2 flex-wrap">
                  <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                    <i class="fas fa-hourglass-half text-orange-500"></i>
                    <span>Sisa Waktu:</span>
                  </span>
                  <div class="inline-flex items-center gap-1">
                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-mono font-bold text-slate-800 shadow-2xs text-xs">
                      <span id="cdHari">00</span> <span class="text-[10px] font-normal text-slate-400">hari</span>
                    </span>
                    <span class="text-slate-400 font-bold">:</span>
                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-mono font-bold text-slate-800 shadow-2xs text-xs">
                      <span id="cdJam">00</span> <span class="text-[10px] font-normal text-slate-400">jam</span>
                    </span>
                    <span class="text-slate-400 font-bold">:</span>
                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-mono font-bold text-slate-800 shadow-2xs text-xs">
                      <span id="cdMenit">00</span> <span class="text-[10px] font-normal text-slate-400">mnt</span>
                    </span>
                    <span class="text-slate-400 font-bold">:</span>
                    <span class="px-2 py-0.5 rounded-md bg-orange-50 border border-orange-200 font-mono font-bold text-orange-600 shadow-2xs text-xs">
                      <span id="cdDetik">00</span> <span class="text-[10px] font-normal text-orange-400">dtk</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tombol Pengaturan Batch: Buka Batch Baru, Ubah Periode, atau Tutup Batch -->
            <div class="flex items-center gap-2">
              <button type="button" id="btnBukaBatchBaru" class="hidden inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition">
                <i class="fas fa-plus"></i> Buka Batch Baru
              </button>
              <button type="button" id="btnEditBatchInline" class="hidden inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                <i class="fas fa-pen text-slate-400"></i> Ubah Periode
              </button>
              <button type="button" id="btnTutupBatch" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition shadow-2xs">
                <i class="fas fa-times-circle"></i> Tutup Batch
              </button>
            </div>
          </div>
        </div>

        <!-- Banner Status Kelayakan Buka Batch Baru (Khusus Batch Tertutup) -->
        <div id="wrapBannerKelayakan" class="hidden px-5 py-3 border-b border-slate-100 bg-white"></div>

        <!-- Toolbar Pencarian & Filter Peserta di Batch Terbuka -->
        <div class="p-3 border-b border-slate-100 bg-white">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap items-center gap-2 flex-1">
              <!-- Cari Peserta berdasarkan NIM atau Nama -->
              <div class="relative flex-1 min-w-[160px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCariPeserta" placeholder="Cari peserta di batch..."
                  class="w-full rounded-lg border border-slate-200 bg-slate-50 py-1.5 pl-8 pr-3 text-xs outline-none focus:border-orange-400 focus:bg-white transition">
              </div>

              <!-- Filter Program Studi -->
              <select id="fProdiPeserta"
                class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs outline-none focus:border-orange-400 <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isFilterProdi ? 'disabled' : '' ?>>
                <?php if (!$isFilterProdi): ?>
                  <option value="" selected>Semua Prodi</option>
                  <?php
                  $qProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
                  if ($qProdi) {
                      while ($pr = mysqli_fetch_assoc($qProdi)) {
                          echo '<option value="' . tampil($pr['kode']) . '">' . tampil($pr['nama']) . '</option>';
                      }
                  }
                  ?>
                <?php else: ?>
                  <option value="<?= tampil($userProdi) ?>" selected><?= tampil($namaProdiUser ?: $userProdi) ?></option>
                <?php endif; ?>
              </select>
            </div>

            <!-- Tombol Aksi Massal: Keluarkan Mahasiswa Terpilih -->
            <button type="button" id="btnKeluarkanMassal" class="hidden inline-flex items-center gap-1 rounded-lg bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition">
              <i class="fas fa-user-minus"></i> Keluarkan (<span id="countPilihPeserta">0</span>)
            </button>
          </div>
        </div>

        <!-- Wadah Tabel Peserta Batch (Di-load via AJAX) -->
        <div id="wrapTabelPeserta" class="max-h-[500px] overflow-y-auto">
          <div class="py-12 text-center text-slate-400">
            <div class="spinner-slate mx-auto"></div>
            <p class="mt-2 text-xs">Memuat peserta batch...</p>
          </div>
        </div>
      </div>

    </div>

    <!-- =======================================================================
         SISI KANAN: LEBAR 5 / 12 KOLOM (lg:col-span-5)
         Menampilkan List Antrian Mahasiswa Siap Ujian
         ======================================================================= -->
    <div class="lg:col-span-5">
      <div id="wrapAntrianMahasiswa" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        
        <!-- Header Kolom Antrian -->
        <div class="border-b border-slate-100 bg-orange-50/70 px-4 py-3.5">
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">
                <i class="fas fa-user-clock"></i>
              </div>
              <h2 class="text-sm font-bold text-slate-800">Antrian Mahasiswa Ujian</h2>
              <span id="badgeTotalAntrian" class="rounded-full bg-orange-500 px-2 py-0.5 text-[11px] font-bold text-white shadow-2xs">0</span>
            </div>

            <!-- Tombol Masukkan Massal (Muncul saat ada mahasiswa yang dicentang) -->
            <button type="button" id="btnMasukkanMassal" class="hidden inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition">
              <i class="fas fa-plus"></i> Masukkan (<span id="countPilihAntrian">0</span>)
            </button>
          </div>
        </div>

        <!-- Toolbar Pencarian & Filter Antrian Mahasiswa -->
        <div class="p-3 border-b border-slate-100 bg-slate-50/50 space-y-2">
          <!-- Input Cari Nama atau NIM -->
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            <input type="search" id="fCariAntrian" placeholder="Cari nama atau NIM mahasiswa..."
              class="w-full rounded-lg border border-slate-200 bg-white py-1.5 pl-8 pr-3 text-xs outline-none focus:border-orange-400 transition">
          </div>

          <div class="flex items-center gap-2">
            <!-- Filter Kategori Ujian: Hasil, Tutup, Seminar KP -->
            <select id="fKategoriAntrian" class="flex-1 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-orange-400">
              <option value="semua" selected>Semua Kategori</option>
              <option value="skripsi_hasil">Ujian Hasil Skripsi</option>
              <option value="skripsi_tutup">Ujian Tutup Skripsi</option>
              <option value="kp">Seminar KP</option>
            </select>

            <!-- Filter Program Studi -->
            <select id="fProdiAntrian"
              class="flex-1 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-orange-400 <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isFilterProdi ? 'disabled' : '' ?>>
              <?php if (!$isFilterProdi): ?>
                <option value="" selected>Semua Prodi</option>
                <?php
                if ($qProdi) {
                    mysqli_data_seek($qProdi, 0);
                    while ($pr = mysqli_fetch_assoc($qProdi)) {
                        echo '<option value="' . tampil($pr['kode']) . '">' . tampil($pr['nama']) . '</option>';
                    }
                }
                ?>
              <?php else: ?>
                <option value="<?= tampil($userProdi) ?>" selected><?= tampil($namaProdiUser ?: $userProdi) ?></option>
              <?php endif; ?>
            </select>
          </div>
        </div>

        <!-- Wadah List Antrian Mahasiswa (Format Compact, Di-load via AJAX) -->
        <div id="wrapListAntrian" class="max-h-[520px] overflow-y-auto">
          <div class="py-12 text-center text-slate-400">
            <div class="spinner-slate mx-auto"></div>
            <p class="mt-2 text-xs">Memuat antrian mahasiswa...</p>
          </div>
        </div>

      </div>
    </div>

  </div>

</main>

<!-- ===========================================================================
     MODAL OVERLAY: UBAH PERIODE BATCH UJIAN
     =========================================================================== -->
<div class="modal-overlay" id="modalEditBatch" role="dialog" aria-modal="true">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50">
      <h6 class="font-bold text-slate-800 text-sm flex items-center gap-2">
        <i class="fas fa-calendar-alt text-orange-500"></i> Ubah Periode Batch Ujian
      </h6>
      <button type="button" class="text-slate-400 hover:text-slate-700 text-lg leading-none" data-close-modal>&times;</button>
    </div>
    <form id="formEditBatch" class="p-5 space-y-4">
      <input type="hidden" name="id_batch" id="editIdBatch" value="">

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Batch Ujian</label>
        <input type="text" id="editNamaBatch" readonly
          class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-bold text-slate-700 outline-none">
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <label class="block text-xs font-semibold text-slate-700">Tanggal Mulai Buka <span class="text-rose-500">*</span></label>
          <button type="button" id="btnSetTglBukaHariIni" class="text-[10px] text-orange-600 hover:text-orange-700 font-semibold underline">
            Set Hari Ini
          </button>
        </div>
        <input type="date" name="tanggal_buka" id="editTglBuka" required
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-500 transition">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Penutupan <span class="text-rose-500">*</span></label>
        <input type="date" name="tanggal_tutup" id="editTglTutup" required
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-500 transition">
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" class="px-4 py-2 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium" data-close-modal>Batal</button>
        <button type="submit" id="btnUpdateBatch" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-sm transition">
          <i class="fas fa-save mr-1"></i> Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ===========================================================================
     MODAL OVERLAY: ATUR JADWAL UJIAN PESERTA BATCH
     (NOMOR UNDANGAN, JAM, TANGGAL, RUANG UJIAN)
     =========================================================================== -->
<div class="modal-overlay" id="modalAturJadwal" role="dialog" aria-modal="true">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
    <!-- Header Modal -->
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50">
      <div class="flex items-center gap-2.5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-500 text-white shadow-sm">
          <i class="fas fa-sliders-h text-sm"></i>
        </div>
        <div>
          <h6 class="font-bold text-slate-800 text-sm">Pengaturan Jadwal Ujian</h6>
          <p class="text-xs text-slate-500">Lengkapi rincian ujian peserta batch</p>
        </div>
      </div>
      <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-close-modal>&times;</button>
    </div>

    <!-- Form Pengaturan Jadwal -->
    <form id="formAturJadwal" class="p-5 space-y-4">
      <input type="hidden" name="id_peserta" id="aturIdPeserta" value="">
      <input type="hidden" name="id_batch" id="aturIdBatch" value="">
      <input type="hidden" name="nim" id="aturNim" value="">
      <input type="hidden" name="kategori" id="aturKategori" value="">

      <!-- Identitas Mahasiswa (Banner Informatif) -->
      <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 flex items-center justify-between">
        <div>
          <div class="text-xs font-bold text-slate-800" id="aturNamaMhs">-</div>
          <div class="text-[11px] font-mono text-slate-500 flex items-center gap-1.5 mt-0.5">
            <i class="fas fa-id-card text-[10px] text-slate-400"></i>
            <span id="aturNimMhs">-</span>
          </div>
        </div>
        <span id="aturTahapanTag" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
          Ujian
        </span>
      </div>

      <!-- Informasi Dosen Penguji -->
      <div id="aturWrapPenguji" class="rounded-lg bg-orange-50/70 border border-orange-200/80 p-3 hidden">
        <div class="text-[11px] font-bold text-orange-900 flex items-center gap-1.5 mb-1.5">
          <i class="fas fa-user-tie text-orange-600 text-xs"></i>
          <span>Dosen Penguji</span>
        </div>
        <div id="aturListPenguji" class="space-y-1.5 text-xs text-slate-700">
          <!-- Diisi dinamis via JavaScript -->
        </div>
      </div>

      <!-- Field 1: Nomor Undangan -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center justify-between">
          <span>Nomor Undangan <span class="text-rose-500">*</span></span>
          <span class="text-[10px] font-normal text-slate-400"><i class="fas fa-lock mr-0.5"></i> Otomatis</span>
        </label>
        <div class="relative">
          <i class="far fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
          <input type="text" name="nomor_undangan" id="aturNomorUndangan" required readonly
            placeholder="Nomor undangan otomatis..."
            value=""
            class="w-full rounded-lg border border-slate-200 bg-slate-100/90 pl-8 pr-8 py-2 text-xs font-semibold text-slate-700 outline-none cursor-not-allowed select-none">
          <i class="fas fa-lock absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400" title="Nomor undangan otomatis tidak dapat diedit"></i>
        </div>
      </div>

      <!-- Grid 2 Kolom: Tanggal & Jam -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Field 2: Tanggal Ujian -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Tanggal Ujian <span class="text-rose-500">*</span>
          </label>
          <input type="date" name="tanggal" id="aturTanggalUjian" required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium outline-none focus:border-orange-500 transition">
        </div>

        <!-- Field 3: Jam Ujian -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Jam Pelaksanaan <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <i class="far fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            <input type="text" name="jam" id="aturJamUjian" required
              placeholder="09:00 - 11:00 WITA"
              class="w-full rounded-lg border border-slate-300 pl-8 pr-3 py-2 text-xs font-medium outline-none focus:border-orange-500 transition">
          </div>
        </div>
      </div>

      <!-- Field 4: Ruang Ujian -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">
          Ruang Ujian <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <i class="fas fa-door-open absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
          <input type="text" name="ruang" id="aturRuangUjian" required
            placeholder="Contoh: Ruang Sidang"
            class="w-full rounded-lg border border-slate-300 pl-8 pr-3 py-2 text-xs font-medium outline-none focus:border-orange-500 transition">
        </div>
      </div>

      <!-- Footer Tombol Modal -->
      <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" class="px-4 py-2 text-xs rounded-lg border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition" data-close-modal>
          Batal
        </button>
        <button type="submit" id="btnSimpanJadwal" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-sm transition">
          <i class="fas fa-save"></i> Simpan Jadwal
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ===========================================================================
     JAVASCRIPT LOGIC (FRONTEND JQUERY & AJAX)
     =========================================================================== -->
<script>
$(document).ready(function() {
  // Endpoint pemrosesan backend AJAX
  const PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/ujian/proses.php';

  // Variabel State Aplikasi
  let activeBatch      = null;  // Menyimpan objek data batch ujian yang sedang aktif
  let hasOpenBatch     = false; // Flag apakah saat ini ada batch yang terbuka
  let debounceAntrian  = null;  // Timer debounce pencarian antrian
  let debouncePeserta  = null;  // Timer debounce pencarian peserta batch
  let countdownTimer   = null;  // Timer interval untuk countdown sisa waktu batch
  let serverTimeOffset = 0;     // Selisih antara jam server dan perangkat klien (ms)
  let canOpenNewBatch  = true;  // Flag kelayakan membuka batch baru (syarat: seluruh peserta naik step)
  let pesanKelayakan   = '';    // Pesan validasi kelayakan membuka batch baru
  let sisaBelumDiatur  = 0;     // Jumlah peserta yang belum diatur jadwalnya di batch sebelumnya

  // ---------------------------------------------------------------------------
  // FUNGSI HELPER: NOTIFIKASI TOAST MELAYANG
  // ---------------------------------------------------------------------------
  function toast(message, type = 'success') {
    const container = $('#toastContainer');
    const icons = {
      success: 'fas fa-check-circle',
      error:   'fas fa-times-circle',
      info:    'fas fa-info-circle'
    };
    const toastItem = $(`
      <div class="toast-item toast-${type}">
        <i class="${icons[type] || icons.info} toast-icon"></i>
        <span>${message}</span>
        <button type="button" class="toast-close">&times;</button>
      </div>
    `);

    container.append(toastItem);
    setTimeout(() => toastItem.addClass('show'), 50);

    // Otomatis menghilang setelah 4 detik
    const hideTimeout = setTimeout(() => {
      toastItem.removeClass('show').addClass('hide');
      setTimeout(() => toastItem.remove(), 400);
    }, 4000);

    toastItem.find('.toast-close').on('click', function() {
      clearTimeout(hideTimeout);
      toastItem.removeClass('show').addClass('hide');
      setTimeout(() => toastItem.remove(), 400);
    });
  }

  // ---------------------------------------------------------------------------
  // FUNGSI HELPER: MODAL KONFIRMASI TINDAKAN (DILARANG MENGGUNAKAN WINDOW.CONFIRM / ALERT)
  // Menggunakan modal global konfirmasiHapus(nama, callback) berdesain Tailwind modern
  // ---------------------------------------------------------------------------
  function mintaKonfirmasi(namaItem, onConfirm, labelTombol = 'Hapus') {
    if (typeof konfirmasiHapus === 'function') {
      const btn = document.getElementById('globalHapusBtn');
      const oriText = btn ? btn.textContent : 'Hapus';
      if (btn && labelTombol) btn.textContent = labelTombol;

      konfirmasiHapus(namaItem, function() {
        if (btn) btn.textContent = oriText;
        if (typeof onConfirm === 'function') onConfirm();
      });

      // Kembalikan label tombol asli jika user menutup modal via tombol Batal atau tombol silang (X)
      const modal = document.getElementById('globalHapusModal');
      if (modal && window.MutationObserver) {
        const observer = new MutationObserver(function() {
          if (!modal.classList.contains('show') && btn) {
            btn.textContent = oriText;
            observer.disconnect();
          }
        });
        observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
      }
    } else {
      // Fallback jika halaman diakses mandiri di luar layout induk administrator
      let modal = $('#modalKonfirmasiInline');
      if (modal.length === 0) {
        $('body').append(`
          <div class="modal-overlay" id="modalKonfirmasiInline" role="dialog" aria-modal="true">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden">
              <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50">
                <h6 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                  <i class="fas fa-exclamation-triangle text-rose-500"></i> Konfirmasi Tindakan
                </h6>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-close-inline-konfirmasi>&times;</button>
              </div>
              <div class="p-5">
                <p class="text-xs text-slate-600 mb-2">Apakah Anda yakin ingin memproses tindakan berikut?</p>
                <p id="inlineKonfirmasiItem" class="text-xs font-semibold text-slate-800 bg-slate-50 rounded-lg px-3 py-2 border border-slate-200"></p>
              </div>
              <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
                <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition" data-close-inline-konfirmasi>Batal</button>
                <button type="button" id="btnInlineKonfirmasiAksi" class="px-4 py-1.5 text-xs rounded-md bg-rose-500 hover:bg-rose-600 text-white font-semibold shadow-sm transition">${labelTombol}</button>
              </div>
            </div>
          </div>
        `);
        $('[data-close-inline-konfirmasi]').on('click', function() {
          $('#modalKonfirmasiInline').removeClass('show');
        });
      }
      $('#inlineKonfirmasiItem').text(namaItem);
      $('#btnInlineKonfirmasiAksi').text(labelTombol).off('click').on('click', function() {
        $('#modalKonfirmasiInline').removeClass('show');
        if (typeof onConfirm === 'function') onConfirm();
      });
      $('#modalKonfirmasiInline').addClass('show');
    }
  }

  // ---------------------------------------------------------------------------
  // KONTROL MODAL DIALOG
  // ---------------------------------------------------------------------------
  $('[data-close-modal]').on('click', function() {
    $('.modal-overlay').removeClass('show');
  });

  $('.modal-overlay').on('click', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
      $(this).removeClass('show');
    }
  });

  // ---------------------------------------------------------------------------
  // FUNGSI COUNTDOWN SISA WAKTU BATCH (BERSTRUKTUR & INFORMATIF)
  // ---------------------------------------------------------------------------
  function mulaiCountdown(bukaTs, tutupTs) {
    hentikanCountdown();

    const wrap = $('#wrapCountdownBaris');
    if (!bukaTs || !tutupTs) {
      wrap.addClass('hidden');
      return;
    }

    const endMs = tutupTs * 1000;

    function perbaruiDisplay() {
      const nowMs  = Date.now() + serverTimeOffset;
      const sisaMs = endMs - nowMs;

      const elHari   = $('#cdHari');
      const elJam    = $('#cdJam');
      const elMenit  = $('#cdMenit');
      const elDetik  = $('#cdDetik');
      const elTag    = $('#cdUrgencyTag');
      const elText   = $('#cdUrgencyText');
      const elDot    = $('#cdUrgencyDot');

      // Kondisi jika waktu penutupan telah berakhir
      if (sisaMs <= 0) {
        hentikanCountdown();
        elHari.text('00');
        elJam.text('00');
        elMenit.text('00');
        elDetik.text('00');

        wrap.removeClass('hidden');
        elTag.attr('class', 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200');
        elDot.attr('class', 'w-1.5 h-1.5 rounded-full bg-rose-500');
        elText.text('Waktu Berakhir');
        return;
      }

      const detik = Math.floor((sisaMs / 1000) % 60);
      const menit = Math.floor((sisaMs / (1000 * 60)) % 60);
      const jam   = Math.floor((sisaMs / (1000 * 60 * 60)) % 24);
      const hari  = Math.floor(sisaMs / (1000 * 60 * 60 * 24));

      const pad = (n) => String(n).padStart(2, '0');
      elHari.text(pad(hari));
      elJam.text(pad(jam));
      elMenit.text(pad(menit));
      elDetik.text(pad(detik));

      wrap.removeClass('hidden');

      const totalSisaJam = sisaMs / (1000 * 60 * 60);
      if (totalSisaJam <= 3) {
        elTag.attr('class', 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200');
        elDot.attr('class', 'w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse');
        elText.text('Segera Berakhir (< 3 Jam)');
      } else if (totalSisaJam <= 24) {
        elTag.attr('class', 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200');
        elDot.attr('class', 'w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse');
        elText.text('Tersisa < 24 Jam');
      } else {
        elTag.attr('class', 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200');
        elDot.attr('class', 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse');
        elText.text('Berjalan');
      }
    }

    perbaruiDisplay();
    countdownTimer = setInterval(perbaruiDisplay, 1000);
  }

  function hentikanCountdown() {
    if (countdownTimer) {
      clearInterval(countdownTimer);
      countdownTimer = null;
    }
    $('#wrapCountdownBaris').addClass('hidden');
  }

  // ---------------------------------------------------------------------------
  // FUNGSI 1: MEMERIKSA STATUS BATCH AKTIF / TERTUTUP DARI BACKEND
  // ---------------------------------------------------------------------------
  // Memeriksa ke backend apakah saat ini ada batch yang terbuka atau tertutup.
  // - Jika ADA BATCH TERBUKA (is_aktif: true):
  //     * Sisi kiri: Menampilkan kartu batch aktif, countdown timer, dan tombol "Keluarkan".
  //     * Sisi kanan: Mengaktifkan seluruh checkbox antrian mahasiswa.
  // - Jika BATCH TERTUTUP (is_aktif: false):
  //     * Sisi kiri: Tetap menampilkan kartu batch & peserta, tombol aksi berganti "Mengatur",
  //       muncul tombol "Buka Batch Baru", countdown dihentikan.
  //     * Sisi kanan: Menonaktifkan (disabled) checkbox antrian mahasiswa.
  // - Jika BELUM ADA BATCH SAMA SEKALI:
  //     * Sisi kiri: Menampilkan form pembukaan batch baru secara inline.
  // ---------------------------------------------------------------------------
  function muatStatusBatchAktif(targetId = null) {
    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: {
        'ambil-status-batch-aktif': 1,
        'id_batch': targetId || 0
      },
      dataType: 'json',
      success: function(res) {
        if (!res.status) {
          toast(res.pesan || 'Gagal memeriksa status batch', 'error');
          return;
        }

        // Simpan objek batch aktif atau batch terpilih
        activeBatch     = res.batch_aktif || null;
        hasOpenBatch    = (res.ada_batch_aktif === true && activeBatch !== null && activeBatch.is_aktif === true);
        canOpenNewBatch = (res.bisa_buka_batch_baru === true);
        pesanKelayakan  = res.pesan_kelayakan || '';
        sisaBelumDiatur = res.sisa_belum_diatur || 0;

        // Sinkronisasi dropdown selector batch (jika ada lebih dari 1 batch dalam riwayat)
        const selBatch = $('#selectPilihBatch');
        if (Array.isArray(res.semua_batch) && res.semua_batch.length > 0) {
          selBatch.empty();
          res.semua_batch.forEach(function(b) {
            const statusTxt = b.is_aktif ? 'Aktif' : 'Tertutup';
            const opt = $('<option>').val(b.id).text(`${b.periode_display} (${statusTxt})`);
            if (activeBatch && String(activeBatch.id) === String(b.id)) {
              opt.prop('selected', true);
            }
            selBatch.append(opt);
          });
          if (res.semua_batch.length > 1) {
            selBatch.removeClass('hidden');
          } else {
            selBatch.addClass('hidden');
          }
        } else {
          selBatch.addClass('hidden');
        }

        // 3. Tampilkan kartu batch jika ada batch (baik aktif maupun tertutup)
        if (activeBatch) {
          // Sinkronisasi selisih waktu perangkat klien terhadap server
          if (res.server_now) {
            serverTimeOffset = (res.server_now * 1000) - Date.now();
          }

          // Tampilkan kartu batch & sembunyikan form buka batch inline
          $('#wrapFormBukaBatch').addClass('hidden');
          $('#wrapBatchTerbuka').removeClass('hidden');

          $('#displayNamaBatch').text(activeBatch.nama);
          $('#displayPeriodeBatch').text(activeBatch.periode_display);
          $('#displayPembuatBatch').text(activeBatch.pembuat);

          // Update badge status visual
          $('#displayBadgeStatus')
            .text(activeBatch.status_label)
            .attr('class', 'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold border ' + (activeBatch.status_class || 'bg-slate-100 text-slate-700 border-slate-200'));

          // Status batch aktif/terbuka: jika flag is_aktif true atau status db masih 'buka'
          const isBatchOpen = (activeBatch.is_aktif || activeBatch.status === 'buka');

          if (isBatchOpen) {
            // KONDISI B1: Batch Aktif / Terbuka
            $('#wrapBannerKelayakan').addClass('hidden');
            // - Jalankan countdown timer
            mulaiCountdown(activeBatch.buka_ts, activeBatch.tutup_ts);
            // - Tampilkan tombol Tutup Batch & Ubah Periode, sembunyikan Buka Batch Baru
            $('#btnTutupBatch').removeClass('hidden');
            $('#btnEditBatchInline').removeClass('hidden');
            $('#btnBukaBatchBaru').addClass('hidden');
            // - Ikon kartu status terbuka (emerald)
            $('#iconBoxBatch').attr('class', 'flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm');
            $('#iconBatchStatus').attr('class', 'fas fa-folder-open text-base');
          } else {
            // KONDISI B2: Batch Ditutup / Tertutup
            // - Hentikan countdown timer
            hentikanCountdown();
            // - Sembunyikan tombol Tutup Batch
            $('#btnTutupBatch').addClass('hidden');
            // Jika batch ditutup tapi peserta masih 0 (koreksi salah input), tetap izinkan Ubah Periode
            if (activeBatch.total_peserta === 0) {
              $('#btnEditBatchInline').removeClass('hidden');
            } else {
              $('#btnEditBatchInline').addClass('hidden');
            }
            $('#btnBukaBatchBaru').removeClass('hidden');
            // - Ikon kartu status tertutup (slate)
            $('#iconBoxBatch').attr('class', 'flex h-10 w-10 items-center justify-center rounded-xl bg-slate-600 text-white shadow-sm');
            $('#iconBatchStatus').attr('class', 'fas fa-archive text-base');

            // Sembunyikan banner teks agar antarmuka bersih dan ringkas;
            // Keaktifan tombol "Buka Batch Baru" menjadi penanda utama bahwa batch dapat dibuka
            $('#wrapBannerKelayakan').addClass('hidden').empty();

            if (canOpenNewBatch) {
              $('#btnBukaBatchBaru').removeClass('opacity-50 cursor-not-allowed').attr('title', 'Buka periode batch ujian baru');
            } else {
              $('#btnBukaBatchBaru').addClass('opacity-50 cursor-not-allowed').attr('title', `Masih terdapat ${sisaBelumDiatur} mahasiswa yang belum diatur jadwalnya`);
            }
          }

          // Muat tabel peserta di sisi kiri (tombol aksi otomatis berubah: Keluarkan vs Mengatur)
          muatTabelPeserta();
        } else {
          // KONDISI A: Belum ada batch sama sekali di database -> Tampilkan form buka batch baru
          hentikanCountdown();
          $('#wrapBatchTerbuka').addClass('hidden');
          $('#wrapFormBukaBatch').removeClass('hidden');
          $('#btnBatalBukaBatchBaru').addClass('hidden');
          $('#btnMasukkanMassal').addClass('hidden');
        }

        // Muat antrian di sisi kanan (checkbox otomatis aktif jika batch aktif, disabled jika tertutup/belum ada)
        muatListAntrian();
      },
      error: function(xhr, status, err) {
        let msg = err;
        try {
          if (xhr.responseJSON && xhr.responseJSON.pesan) {
            msg = xhr.responseJSON.pesan;
          } else if (xhr.responseText) {
            const clean = $('<div>').html(xhr.responseText).text().trim();
            if (clean) msg = clean.substring(0, 150);
          }
        } catch(e) {}
        toast('Gagal terhubung dengan server: ' + msg, 'error');
      }
    });
  }

  // ---------------------------------------------------------------------------
  // FUNGSI 2: MEMUAT TABEL PESERTA BATCH (SISI KIRI)
  // ---------------------------------------------------------------------------
  function muatTabelPeserta() {
    if (!activeBatch) return;

    const wrap = $('#wrapTabelPeserta');
    wrap.html('<div class="py-12 text-center text-slate-400"><div class="spinner-slate mx-auto"></div><p class="mt-2 text-xs">Memuat peserta batch...</p></div>');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: {
        'ambil-tabel-peserta': 1,
        'id_batch': activeBatch.id,
        'cari': $('#fCariPeserta').val(),
        'prodi': $('#fProdiPeserta').val()
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          wrap.html(res.data);
          bindPesertaEvents();
          updatePesertaBulkToolbar();
        } else {
          wrap.html(`<div class="py-10 text-center text-rose-500 text-xs">${res.pesan}</div>`);
        }
      },
      error: function() {
        wrap.html('<div class="py-10 text-center text-rose-500 text-xs">Gagal memuat peserta batch.</div>');
      }
    });
  }

  // ---------------------------------------------------------------------------
  // FUNGSI 3: MEMUAT LIST ANTRIAN MAHASISWA SIAP UJIAN (SISI KANAN)
  // ---------------------------------------------------------------------------
  function muatListAntrian() {
    const wrap = $('#wrapListAntrian');
    wrap.html('<div class="py-12 text-center text-slate-400"><div class="spinner-slate mx-auto"></div><p class="mt-2 text-xs">Memuat antrian mahasiswa...</p></div>');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: {
        'ambil-tabel-antrian': 1,
        'id_batch': (activeBatch ? activeBatch.id : 0),
        'batch_terbuka': (hasOpenBatch ? '1' : '0'),
        'cari': $('#fCariAntrian').val(),
        'prodi': $('#fProdiAntrian').val(),
        'kategori': $('#fKategoriAntrian').val()
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          wrap.html(res.data);
          $('#badgeTotalAntrian').text(res.total);
          bindAntrianEvents();
          updateAntrianBulkToolbar();
        } else {
          wrap.html(`<div class="py-10 text-center text-rose-500 text-xs">${res.pesan}</div>`);
        }
      },
      error: function() {
        wrap.html('<div class="py-10 text-center text-rose-500 text-xs">Gagal memuat antrian ujian.</div>');
      }
    });
  }

  // ---------------------------------------------------------------------------
  // EVENT LISTENER: ANTRIAN MAHASISWA (SISI KANAN)
  // ---------------------------------------------------------------------------
  function bindAntrianEvents() {
    // Saat checkbox antrian dicentang / diubah
    $('.cb-antrian').on('change', function() {
      updateAntrianBulkToolbar();
    });

    // Tombol plus (+) untuk memasukkan satu mahasiswa langsung
    $('.btn-masukkan-peserta').on('click', function() {
      if (!hasOpenBatch || !activeBatch) {
        toast('Buka batch ujian terlebih dahulu.', 'error');
        return;
      }

      const nim  = $(this).data('nim');
      const nama = $(this).data('nama');

      $.ajax({
        url: PROSES_URL,
        type: 'POST',
        data: {
          'tambah-peserta-batch': 1,
          'id_batch': activeBatch.id,
          'nim': nim
        },
        dataType: 'json',
        success: function(res) {
          if (res.status) {
            toast(res.pesan, 'success');
            // Refresh kedua sisi secara reaktif
            muatTabelPeserta();
            muatListAntrian();
          } else {
            toast(res.pesan, 'error');
          }
        },
        error: function(xhr, status, err) {
          toast('Gagal memproses data: ' + err, 'error');
        }
      });
    });
  }

  // Perbarui tombol "+ Masukkan (N)" berdasarkan jumlah checkbox yang dicentang
  function updateAntrianBulkToolbar() {
    const count = $('.cb-antrian:checked').length;
    $('#countPilihAntrian').text(count);
    if (count > 0 && hasOpenBatch) {
      $('#btnMasukkanMassal').removeClass('hidden');
    } else {
      $('#btnMasukkanMassal').addClass('hidden');
    }
  }

  // Aksi Masukkan Massal (operator centang beberapa mahasiswa lalu klik tombol)
  $('#btnMasukkanMassal').on('click', function() {
    if (!hasOpenBatch || !activeBatch) {
      toast('Buka batch ujian terlebih dahulu.', 'error');
      return;
    }

    const selectedNims = [];
    $('.cb-antrian:checked').each(function() {
      selectedNims.push($(this).val());
    });

    if (selectedNims.length === 0) return;

    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner animate-spin"></i> Memproses...');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: {
        'tambah-peserta-batch': 1,
        'id_batch': activeBatch.id,
        'nims': selectedNims
      },
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Masukkan (<span id="countPilihAntrian">0</span>)');
        if (res.status) {
          toast(res.pesan, 'success');
          // Refresh tabel peserta di kiri dan antrian di kanan
          muatTabelPeserta();
          muatListAntrian();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Masukkan (<span id="countPilihAntrian">0</span>)');
        toast('Gagal memasukkan mahasiswa: ' + err, 'error');
      }
    });
  });

  // ---------------------------------------------------------------------------
  // EVENT LISTENER: PESERTA BATCH (SISI KIRI)
  // ---------------------------------------------------------------------------
  function bindPesertaEvents() {
    // Checkbox pilih semua peserta
    $('#checkAllPeserta').on('change', function() {
      $('.cb-peserta').prop('checked', $(this).is(':checked'));
      updatePesertaBulkToolbar();
    });

    $('.cb-peserta').on('change', function() {
      const allChecked = $('.cb-peserta:checked').length === $('.cb-peserta').length;
      $('#checkAllPeserta').prop('checked', allChecked);
      updatePesertaBulkToolbar();
    });

    // Tombol keluarkan satu mahasiswa dari batch
    $('.btn-keluarkan-peserta').on('click', function() {
      if (!activeBatch) return;

      const idPeserta = $(this).data('id-peserta');
      const nama      = $(this).data('nama');
      const nim       = $(this).data('nim');
      const btn       = $(this);

      // Konfirmasi via Modal Global (DILARANG confirm() browser)
      mintaKonfirmasi(`${nama} (${nim}) dari ${activeBatch.nama}`, function() {
        btn.prop('disabled', true).addClass('opacity-50 pointer-events-none');

        $.ajax({
          url: PROSES_URL,
          type: 'POST',
          data: {
            'hapus-peserta-batch': 1,
            'id_batch': activeBatch.id,
            'id_peserta': idPeserta
          },
          dataType: 'json',
          success: function(res) {
            if (res.status) {
              toast(res.pesan, 'success');
              // Refresh tabel peserta & antrian secara reaktif
              muatTabelPeserta();
              muatListAntrian();
            } else {
              toast(res.pesan, 'error');
            }
          },
          error: function(xhr, status, err) {
            toast('Terjadi kesalahan: ' + err, 'error');
          },
          complete: function() {
            btn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
          }
        });
      }, 'Keluarkan');
    });
  }

  // Perbarui tombol "Keluarkan Terpilih (N)"
  function updatePesertaBulkToolbar() {
    const count = $('.cb-peserta:checked').length;
    $('#countPilihPeserta').text(count);
    if (count > 0) {
      $('#btnKeluarkanMassal').removeClass('hidden');
    } else {
      $('#btnKeluarkanMassal').addClass('hidden');
    }
  }

  // Aksi Keluarkan Massal dari Batch
  $('#btnKeluarkanMassal').on('click', function() {
    if (!activeBatch) return;

    const selectedIds = [];
    $('.cb-peserta:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) return;

    const btn = $(this);

    // Konfirmasi massal via Modal Global (DILARANG confirm() browser)
    mintaKonfirmasi(`${selectedIds.length} mahasiswa terpilih dari ${activeBatch.nama}`, function() {
      btn.prop('disabled', true).addClass('opacity-50 pointer-events-none').html('<i class="fas fa-spinner animate-spin mr-1"></i> Mengeluarkan...');

      $.ajax({
        url: PROSES_URL,
        type: 'POST',
        data: {
          'hapus-peserta-batch': 1,
          'id_batch': activeBatch.id,
          'ids_peserta': selectedIds
        },
        dataType: 'json',
        success: function(res) {
          if (res.status) {
            toast(res.pesan, 'success');
            muatTabelPeserta();
            muatListAntrian();
          } else {
            toast(res.pesan, 'error');
          }
        },
        error: function(xhr, status, err) {
          toast('Terjadi kesalahan saat mengeluarkan peserta: ' + err, 'error');
        },
        complete: function() {
          btn.prop('disabled', false).removeClass('opacity-50 pointer-events-none').html('<i class="fas fa-user-minus mr-1"></i> Keluarkan Terpilih (<span id="countPilihPeserta">0</span>)');
        }
      });
    }, 'Keluarkan');
  });

  // ---------------------------------------------------------------------------
  // AUTO-UPDATE TANGGAL PENUTUPAN: 5 HARI SETELAH TANGGAL BUKA
  // ---------------------------------------------------------------------------
  $('#inlineTglBuka').on('change', function() {
    const val = $(this).val();
    if (val) {
      const dt = new Date(val);
      dt.setDate(dt.getDate() + 5);
      const yyyy = dt.getFullYear();
      const mm   = String(dt.getMonth() + 1).padStart(2, '0');
      const dd   = String(dt.getDate()).padStart(2, '0');
      $('#inlineTglTutup').val(`${yyyy}-${mm}-${dd}`);
    }
  });

  // ---------------------------------------------------------------------------
  // SUBMIT FORM BUKA BATCH INLINE (SISI KIRI)
  // ---------------------------------------------------------------------------
  $('#formBukaBatchInline').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnSubmitBukaBatchInline');
    btn.prop('disabled', true).html('<i class="fas fa-spinner animate-spin mr-1"></i> Membuka Batch...');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: $(this).serialize() + '&tambah-batch=1',
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-check"></i> Buka Batch');
        if (res.status) {
          toast(res.pesan, 'success');
          // Muat batch aktif yang baru dibuka
          muatStatusBatchAktif(res.id_batch);
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        btn.prop('disabled', false).html('<i class="fas fa-check"></i> Buka Batch');
        let errorMsg = err;
        try {
          if (xhr.responseJSON && xhr.responseJSON.pesan) {
            errorMsg = xhr.responseJSON.pesan;
          } else if (xhr.responseText) {
            const cleanText = $('<div>').html(xhr.responseText).text().trim();
            if (cleanText) errorMsg = cleanText.substring(0, 120);
          }
        } catch(e) {}
        toast('Gagal membuka batch: ' + errorMsg, 'error');
      }
    });
  });

  // ---------------------------------------------------------------------------
  // UBAH PERIODE BATCH VIA MODAL
  // ---------------------------------------------------------------------------
  $('#btnSetTglBukaHariIni').on('click', function() {
    const today = new Date().toISOString().split('T')[0];
    $('#editTglBuka').val(today);
  });

  $('#btnEditBatchInline').on('click', function() {
    if (!activeBatch) return;

    $('#editIdBatch').val(activeBatch.id);
    $('#editNamaBatch').val(activeBatch.nama);
    $('#editTglBuka').val(activeBatch.tanggal_buka);
    $('#editTglTutup').val(activeBatch.tanggal_tutup);
    $('#modalEditBatch').addClass('show');
  });

  $('#formEditBatch').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnUpdateBatch');
    btn.prop('disabled', true).html('<i class="fas fa-spinner animate-spin mr-1"></i> Menyimpan...');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: $(this).serialize() + '&edit-batch=1',
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
        if (res.status) {
          toast(res.pesan, 'success');
          $('#modalEditBatch').removeClass('show');
          muatStatusBatchAktif(activeBatch.id);
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
        toast('Gagal memperbarui batch: ' + err, 'error');
      }
    });
  });

  // ---------------------------------------------------------------------------
  // TUTUP / SELESAIKAN BATCH UJIAN
  // ---------------------------------------------------------------------------
  $('#btnTutupBatch').on('click', function() {
    if (!activeBatch) return;

    const btn = $(this);

    // Konfirmasi penutupan batch via Modal Global (DILARANG confirm() browser)
    mintaKonfirmasi(`Batch Ujian: ${activeBatch.nama} (Status batch akan ditutup dan pendaftaran dikunci. Anda dapat mengatur jadwal ujian peserta setelah ini)`, function() {
      btn.prop('disabled', true).addClass('opacity-50 pointer-events-none');

      $.ajax({
        url: PROSES_URL,
        type: 'POST',
        data: {
          'hapus-batch': 1,
          'id_batch': activeBatch.id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status) {
            toast(res.pesan, 'success');
            hentikanCountdown();
            // Refresh batch ini (sekarang berstatus tertutup) sehingga tombol aksi berganti "Mengatur"
            muatStatusBatchAktif(res.id_batch || activeBatch.id);
          } else {
            toast(res.pesan, 'error');
          }
        },
        error: function(xhr, status, err) {
          toast('Gagal menutup batch: ' + err, 'error');
        },
        complete: function() {
          btn.prop('disabled', false).removeClass('opacity-50 pointer-events-none');
        }
      });
    }, 'Tutup Batch');
  });

  // ---------------------------------------------------------------------------
  // SWITCH BATCH UJIAN DARI DROPDOWN SELECTOR
  // ---------------------------------------------------------------------------
  $('#selectPilihBatch').on('change', function() {
    const selectedId = $(this).val();
    if (selectedId) {
      muatStatusBatchAktif(selectedId);
    }
  });

  // ---------------------------------------------------------------------------
  // TOGGLE FORM BUKA BATCH BARU (DARI KONDISI BATCH TERTUTUP)
  // ---------------------------------------------------------------------------
  $('#btnBukaBatchBaru').on('click', function() {
    // Validasi syarat: seluruh peserta batch sebelumnya wajib sudah diatur jadwalnya & naik step
    if (!canOpenNewBatch) {
      toast(pesanKelayakan || 'Lengkapi pengaturan jadwal seluruh peserta pada batch ini terlebih dahulu sebelum membuka batch baru.', 'info');
      return;
    }

    $('#wrapBatchTerbuka').addClass('hidden');
    $('#wrapFormBukaBatch').removeClass('hidden');
    $('#btnBatalBukaBatchBaru').removeClass('hidden');
  });

  $('#btnBatalBukaBatchBaru').on('click', function() {
    if (activeBatch) {
      $('#wrapFormBukaBatch').addClass('hidden');
      $('#wrapBatchTerbuka').removeClass('hidden');
    }
  });

  // ---------------------------------------------------------------------------
  // MODAL ATUR JADWAL UJIAN PESERTA (NOMOR UNDANGAN, TANGGAL, JAM, RUANG)
  // ---------------------------------------------------------------------------
  // Event delegasi saat tombol "Mengatur" / "Ubah Jadwal" diklik
  $(document).on('click', '.btn-atur-jadwal', function() {
    const idPeserta  = $(this).data('id-peserta');
    const idBatch    = $(this).data('id-batch') || (activeBatch ? activeBatch.id : '');
    const nim        = $(this).data('nim');
    const nama       = $(this).data('nama');
    const kategori   = $(this).data('kategori') || '';
    const tahapan    = $(this).data('tahapan') || 'Ujian';
    const noUndangan = $(this).data('nomor-undangan') || '';
    const tanggal    = $(this).data('tanggal') || '';
    const jam        = $(this).data('jam') || '';
    const ruang      = $(this).data('ruang') || '';
    let pengujiData  = $(this).data('penguji');

    // Parse data penguji jika dalam format JSON string
    if (typeof pengujiData === 'string') {
      try { pengujiData = JSON.parse(pengujiData); } catch(e) { pengujiData = []; }
    }
    if (!Array.isArray(pengujiData)) pengujiData = [];

    // Isi identitas & kategori ke modal form
    $('#aturIdPeserta').val(idPeserta);
    $('#aturIdBatch').val(idBatch);
    $('#aturNim').val(nim);
    $('#aturKategori').val(kategori);
    $('#aturNamaMhs').text(nama);
    $('#aturNimMhs').text(nim);
    $('#aturTahapanTag').text(tahapan);

    // Tampilkan daftar dosen penguji di modal
    if (pengujiData.length > 0) {
      let pengujiHtml = '';
      pengujiData.forEach(function(item, idx) {
        const peran = item.peran || ('Penguji ' + (idx + 1));
        pengujiHtml += '<div class="flex items-start gap-2 text-[11px]">' +
          '<span class="font-bold text-orange-700 shrink-0 w-20">' + peran + ':</span>' +
          '<span class="text-slate-800 font-semibold">' + (item.nama || '-') + '</span>' +
        '</div>';
      });
      $('#aturListPenguji').html(pengujiHtml);
      $('#aturWrapPenguji').removeClass('hidden');
    } else {
      $('#aturListPenguji').html('<p class="text-[11px] text-slate-400 italic">Dosen penguji belum ditetapkan.</p>');
      $('#aturWrapPenguji').removeClass('hidden');
    }

    // Isi field jadwal
    $('#aturTanggalUjian').val(tanggal);
    $('#aturJamUjian').val(jam);
    $('#aturRuangUjian').val(ruang);

    // Nomor Undangan: jika sudah ada gunakan yang tersimpan, jika belum ada panggil AJAX real-time
    if (noUndangan) {
      $('#aturNomorUndangan').val(noUndangan);
      $('#btnSimpanJadwal').prop('disabled', false);
    } else {
      $('#aturNomorUndangan').val('Memuat nomor surat...');
      $('#btnSimpanJadwal').prop('disabled', true);
      $.ajax({
        url: PROSES_URL,
        type: 'POST',
        data: { 'ambil-nomor-undangan-terbaru': 1 },
        dataType: 'json',
        success: function(res) {
          if (res && res.status && res.nomor) {
            $('#aturNomorUndangan').val(res.nomor);
          } else {
            $('#aturNomorUndangan').val('');
          }
        },
        error: function() {
          $('#aturNomorUndangan').val('');
        },
        complete: function() {
          $('#btnSimpanJadwal').prop('disabled', false);
        }
      });
    }

    // Tampilkan modal
    $('#modalAturJadwal').addClass('show');
  });

  // Submit Simpan Pengaturan Jadwal Ujian Peserta
  $('#formAturJadwal').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnSimpanJadwal');
    btn.prop('disabled', true).html('<i class="fas fa-spinner animate-spin mr-1"></i> Menyimpan...');

    $.ajax({
      url: PROSES_URL,
      type: 'POST',
      data: $(this).serialize() + '&atur-jadwal-peserta=1',
      dataType: 'json',
      success: function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Jadwal');
        if (res.status) {
          toast(res.pesan, 'success');
          $('#modalAturJadwal').removeClass('show');
          // Refresh isi tabel peserta & status kelayakan batch
          muatTabelPeserta();
          if (activeBatch) {
            muatStatusBatchAktif(activeBatch.id);
          }
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Jadwal');
        let errorMsg = err;
        try {
          if (xhr.responseJSON && xhr.responseJSON.pesan) {
            errorMsg = xhr.responseJSON.pesan;
          } else if (xhr.responseText) {
            const cleanText = $('<div>').html(xhr.responseText).text().trim();
            if (cleanText) errorMsg = cleanText.substring(0, 150);
          }
        } catch(e) {}
        toast('Gagal menyimpan jadwal: ' + errorMsg, 'error');
      }
    });
  });

  // ---------------------------------------------------------------------------
  // FILTER & PENCARIAN REAL-TIME
  // ---------------------------------------------------------------------------
  $('#fCariAntrian').on('input', function() {
    clearTimeout(debounceAntrian);
    debounceAntrian = setTimeout(muatListAntrian, 300);
  });

  $('#fKategoriAntrian, #fProdiAntrian').on('change', function() {
    muatListAntrian();
  });

  $('#fCariPeserta').on('input', function() {
    clearTimeout(debouncePeserta);
    debouncePeserta = setTimeout(muatTabelPeserta, 300);
  });

  $('#fProdiPeserta').on('change', function() {
    muatTabelPeserta();
  });

  // ---------------------------------------------------------------------------
  // PEMUATAN AWAL SAAT HALAMAN DIBUKA
  // ---------------------------------------------------------------------------
  muatStatusBatchAktif();

});
</script>
