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
$allowedLevels = [3, 4, 5, 6, 9];

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
$isFilterProdi = in_array($userLevel, [3, 5], true);
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
    <div class="lg:col-span-7 space-y-4">

      <!-- KONDISI A: FORM BUKA BATCH INLINE (Tampil hanya jika BELUM ADA batch terbuka) -->
      <div id="wrapFormBukaBatch" class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-gradient-to-r from-orange-50 to-amber-50 px-5 py-4">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm">
              <i class="fas fa-calendar-plus text-base"></i>
            </div>
            <div>
              <h2 class="text-sm font-bold text-slate-800">Buka Batch Ujian</h2>
              <p class="text-xs text-slate-500">Atur periode pelaksanaan ujian</p>
            </div>
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

      <!-- KONDISI B: BATCH TERBUKA & TABEL PESERTA (Tampil saat ADA batch terbuka) -->
      <div id="wrapBatchTerbuka" class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        
        <!-- Header Identitas Batch Aktif -->
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm">
                <i class="fas fa-folder-open text-base"></i>
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <h2 class="text-base font-bold text-slate-800" id="displayNamaBatch">Batch Ujian</h2>
                  <span id="displayBadgeStatus" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Dibuka / Aktif
                  </span>
                </div>
                <!-- Informasi Periode Tanggal & Operator Pembuat Batch -->
                <p class="text-xs text-slate-500 mt-0.5">
                  <i class="far fa-calendar-alt text-orange-500 mr-1"></i>
                  Periode: <strong class="text-slate-700" id="displayPeriodeBatch">-</strong>
                  &bull; Dibuat oleh: <span class="text-slate-600" id="displayPembuatBatch">-</span>
                </p>
              </div>
            </div>

            <!-- Tombol Pengaturan Batch: Ubah Periode atau Tutup Batch -->
            <div class="flex items-center gap-2">
              <button type="button" id="btnEditBatchInline" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                <i class="fas fa-pen text-slate-400"></i> Ubah Periode
              </button>
              <button type="button" id="btnTutupBatch" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition shadow-2xs">
                <i class="fas fa-times-circle"></i> Tutup Batch
              </button>
            </div>
          </div>
        </div>

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
        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Buka <span class="text-rose-500">*</span></label>
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
     JAVASCRIPT LOGIC (FRONTEND JQUERY & AJAX)
     =========================================================================== -->
<script>
$(document).ready(function() {
  // Endpoint pemrosesan backend AJAX
  const PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/ujian/proses.php';

  // Variabel State Aplikasi
  let activeBatch     = null;  // Menyimpan objek data batch ujian yang sedang aktif
  let hasOpenBatch    = false; // Flag apakah saat ini ada batch yang terbuka
  let debounceAntrian = null;  // Timer debounce pencarian antrian
  let debouncePeserta = null;  // Timer debounce pencarian peserta batch

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
  // FUNGSI 1: MEMERIKSA STATUS BATCH AKTIF DARI BACKEND
  // ---------------------------------------------------------------------------
  // Memeriksa ke backend apakah saat ini ada batch yang terbuka.
  // - Jika ADA: Sisi kiri menampilkan kartu batch aktif & tabel peserta.
  //   Sisi kanan mengaktifkan seluruh checkbox antrian mahasiswa.
  // - Jika TIDAK ADA: Sisi kiri menampilkan form buka batch baru.
  //   Sisi kanan menonaktifkan (disabled) checkbox antrian mahasiswa.
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

        hasOpenBatch = (res.ada_batch_aktif === true && res.batch_aktif !== null);
        activeBatch  = res.batch_aktif || null;

        if (hasOpenBatch && activeBatch) {
          // KONDISI B: Ada batch terbuka -> Tampilkan kartu batch & tabel peserta
          $('#wrapFormBukaBatch').addClass('hidden');
          $('#wrapBatchTerbuka').removeClass('hidden');

          $('#displayNamaBatch').text(activeBatch.nama);
          $('#displayPeriodeBatch').text(activeBatch.periode_display);
          $('#displayPembuatBatch').text(activeBatch.pembuat);
          $('#displayBadgeStatus').text(activeBatch.status_label);

          // Muat tabel peserta di sisi kiri
          muatTabelPeserta();
        } else {
          // KONDISI A: Belum ada batch terbuka -> Tampilkan form buka batch inline
          $('#wrapBatchTerbuka').addClass('hidden');
          $('#wrapFormBukaBatch').removeClass('hidden');

          $('#btnMasukkanMassal').addClass('hidden');
        }

        // Muat antrian di sisi kanan (checkbox otomatis enabled jika batch aktif, disabled jika belum ada)
        muatListAntrian();
      },
      error: function(xhr, status, err) {
        toast('Gagal terhubung dengan server: ' + err, 'error');
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

      if (!confirm(`Keluarkan ${nama} (${nim}) dari ${activeBatch.nama}?`)) return;

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
            // Refresh kedua sisi
            muatTabelPeserta();
            muatListAntrian();
          } else {
            toast(res.pesan, 'error');
          }
        },
        error: function(xhr, status, err) {
          toast('Terjadi kesalahan: ' + err, 'error');
        }
      });
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

    if (!confirm(`Keluarkan ${selectedIds.length} mahasiswa terpilih dari ${activeBatch.nama}?`)) return;

    const btn = $(this);
    btn.prop('disabled', true);

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
        btn.prop('disabled', false);
        if (res.status) {
          toast(res.pesan, 'success');
          muatTabelPeserta();
          muatListAntrian();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        btn.prop('disabled', false);
        toast('Terjadi kesalahan saat mengeluarkan peserta: ' + err, 'error');
      }
    });
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
  // TUTUP / HAPUS BATCH UJIAN
  // ---------------------------------------------------------------------------
  $('#btnTutupBatch').on('click', function() {
    if (!activeBatch) return;

    if (!confirm(`Yakin ingin menutup dan menyelesaikan ${activeBatch.nama}?`)) {
      return;
    }

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
          // Refresh status batch (akan kembali ke tampilan form buka batch)
          muatStatusBatchAktif();
        } else {
          toast(res.pesan, 'error');
        }
      },
      error: function(xhr, status, err) {
        toast('Gagal menutup batch: ' + err, 'error');
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
