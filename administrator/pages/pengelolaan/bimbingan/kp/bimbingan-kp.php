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
            <p class="text-xs text-rose-600 mt-1">Menu Bimbingan Kerja Praktek hanya dapat diakses oleh Dosen Pembimbing, Ketua Program Studi, dan Pengelola Jurusan.</p>
        </div>
    </main>
    <?php
    return;
}

// Data identitas pengguna
$idDosenSession  = bersihkan((string)($_SESSION['id'] ?? ''));
$nipDosenSession = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
$isKaprodi       = ($userLevel === 5);
$userProdi       = $isKaprodi ? (function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''))) : '';
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
  .spinner-slate {
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(15,23,42,0.2);
    border-top-color: #1a365d;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ===== Drawer Offcanvas Arsip & Riwayat Tahapan ===== */
  .drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(2px);
    z-index: 1060;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.3s;
  }
  .drawer-overlay.show {
    visibility: visible;
    opacity: 1;
  }
  .drawer-container {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 660px;
    background: #f8fafc;
    box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2);
    z-index: 1065;
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
  }
  .drawer-overlay.show .drawer-container {
    transform: translateX(0);
  }
  .acc-chevron.rotate-180 {
    transform: rotate(180deg);
  }
</style>

<main class="content-area content-scroll">

  <!-- ===== Page Header ===== -->
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600 shadow-xs">
          <i class="fas fa-briefcase"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Bimbingan Kerja Praktek</h1>
          <p class="text-xs text-slate-500">Monitoring progresline &amp; capaian mahasiswa bimbingan kerja praktek</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Peringatan jika Kaprodi belum ada prodi -->
  <?php if ($isKaprodi && $userProdi === ''): ?>
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 shadow-sm">
      <i class="fas fa-exclamation-triangle text-xl text-amber-500 shrink-0"></i>
      <div>
        <p class="font-semibold">Program Studi Belum Tercatat</p>
        <p class="text-xs text-amber-700">Akun Anda belum memiliki data program studi yang tercatat di sistem. Tidak ada data mahasiswa yang dapat ditampilkan.</p>
      </div>
    </div>
  <?php endif; ?>

  <!-- ===== Metric Summary Cards ===== -->
  <section class="mb-5">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <!-- Total Mahasiswa Bimbingan -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-500">Total Bimbingan</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-xs">
            <i class="fas fa-users"></i>
          </span>
        </div>
        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-800" id="statTotal">—</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Mahasiswa terdaftar</p>
      </div>

      <!-- Bimbingan Aktif -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-500">Bimbingan Aktif</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 text-xs">
            <i class="fas fa-user-clock"></i>
          </span>
        </div>
        <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600" id="statAktif">—</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Sedang menempuh KP</p>
      </div>

      <!-- Tahap Seminar KP -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-500">Tahap Seminar KP</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-orange-600 text-xs">
            <i class="fas fa-graduation-cap"></i>
          </span>
        </div>
        <p class="mt-2 text-2xl font-bold tracking-tight text-orange-600" id="statSiapUjian">—</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Penguji &amp; seminar KP</p>
      </div>

      <!-- Rata-rata Progres -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-500">Rata-rata Progres</span>
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 text-xs">
            <i class="fas fa-chart-pie"></i>
          </span>
        </div>
        <p class="mt-2 text-2xl font-bold tracking-tight text-purple-600" id="statRataProgres">—%</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Kelulusan tahapan SOP</p>
      </div>
    </div>
  </section>

  <!-- ===== Filter Bar ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      
      <!-- Search NIM / Nama / Judul / Tempat KP -->
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama, NIM, tempat KP, judul..."
          class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white transition">
      </div>

      <!-- Filter Prodi (Untuk Level 6 & 9) -->
      <?php if ($userLevel === 6 || $userLevel === 9): ?>
      <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Program Studi</option>
        <?php
        $qProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
        if ($qProdi) {
            while ($pr = mysqli_fetch_assoc($qProdi)) {
                echo '<option value="' . tampil($pr['kode']) . '">' . tampil($pr['nama']) . '</option>';
            }
        }
        ?>
      </select>
      <?php elseif ($isKaprodi): ?>
      <input type="hidden" id="fProdi" value="<?= tampil($userProdi) ?>">
      <?php else: ?>
      <input type="hidden" id="fProdi" value="">
      <?php endif; ?>

      <!-- Filter Dosen Pembimbing (Khusus Level 5, 6, 9) -->
      <?php if (in_array($userLevel, [5, 6, 9], true)): ?>
      <select id="fPembimbing" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 max-w-[220px]">
        <option value="" <?= ($userLevel === 9) ? 'selected' : '' ?>>Semua Dosen Pembimbing</option>
        <option value="saya" <?= ($userLevel !== 9) ? 'selected' : '' ?>>★ Bimbingan Saya (Sebagai Dosen)</option>
        <?php
        $whereDsn = ($isKaprodi && $userProdi !== '') ? "AND prodi = '" . bersihkan($userProdi) . "'" : "";
        $qDsnList = query("SELECT id, nip, nama FROM dosen WHERE LOWER(posisi) = 'dosen' AND LOWER(status) = 'aktif' $whereDsn ORDER BY nama ASC");
        if ($qDsnList) {
            while ($ds = mysqli_fetch_assoc($qDsnList)) {
                $valId = !empty($ds['id']) ? $ds['id'] : $ds['nip'];
                echo '<option value="' . tampil($valId) . '">' . tampil($ds['nama']) . '</option>';
            }
        }
        ?>
      </select>
      <?php else: ?>
      <input type="hidden" id="fPembimbing" value="saya">
      <?php endif; ?>

      <!-- Filter Status KP -->
      <select id="fStatus" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Status</option>
        <option value="aktif" selected>Aktif</option>
        <option value="selesai">Selesai / Alumni</option>
        <option value="tidak aktif">Tidak Aktif</option>
      </select>

      <!-- Filter Progres / SOP KP -->
      <select id="fProgres" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 max-w-[190px]">
        <option value="">Semua Tahap SOP</option>
        <?php
        $sopKpList = get_sop_kp_list();
        foreach ($sopKpList as $sop) {
            echo '<option value="' . (int)$sop['no'] . '">SOP ' . (int)$sop['no'] . ': ' . tampil($sop['keterangan']) . '</option>';
        }
        ?>
      </select>

      <!-- Reset Button -->
      <button type="button" id="btnReset"
        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
        <i class="fas fa-times mr-1"></i>Reset
      </button>

    </div>
  </section>

  <!-- ===== Tabel Data Bimbingan Kerja Praktek ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 bg-slate-50/50">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Mahasiswa Bimbingan Kerja Praktek</h2>
          <p class="text-xs text-slate-400 mt-0.5">Data kemajuan bimbingan KP &amp; status tahapan aktif</p>
        </div>
      </div>
      <div id="tabelWrap" class="max-h-[650px] overflow-y-auto">
        <div class="acc-loading py-12 text-center">
          <div class="spinner-slate mx-auto"></div>
          <p class="mt-3 text-slate-400 text-xs">Memuat data mahasiswa bimbingan...</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Drawer Samping: Arsip & Riwayat Tahapan (Progresline KP) ===== -->
  <div class="drawer-overlay" id="drawerArsip" role="dialog" aria-modal="true">
    <div class="drawer-container">
      <!-- Header Drawer (Sticky) -->
      <div class="px-5 py-3.5 border-b border-slate-200 bg-white shrink-0 shadow-2xs" id="drawerArsipHeader">
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-3 min-w-0">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-bold shadow-xs">
              <i class="fas fa-briefcase text-lg"></i>
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="text-sm font-bold text-slate-800 leading-snug truncate" id="drawerHeaderNama">Memuat Nama...</h3>
                <span id="drawerHeaderStatus" class="hidden text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5 leading-tight">
                NIM: <span class="font-mono font-semibold text-slate-700" id="drawerHeaderNim">-</span> &bull; 
                <span class="font-medium text-slate-600" id="drawerHeaderProdi">-</span>
              </p>
              <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                <i class="fas fa-box-archive text-emerald-500"></i> Progresline &amp; Riwayat Tahapan SOP Kerja Praktek
              </p>
            </div>
          </div>
          <button type="button" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 text-xl leading-none transition" data-drawer-close>
            &times;
          </button>
        </div>
      </div>

      <!-- Isi Drawer (Scrollable) -->
      <div id="drawerArsipKonten" class="flex-1 overflow-y-auto p-6 content-scroll">
        <div class="acc-loading py-12 text-center">
          <div class="spinner-slate mx-auto"></div>
          <p class="mt-3 text-slate-400 text-xs">Memuat data arsip &amp; riwayat progresline...</p>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- ===== Script Interaktif ===== -->
<script>
  var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/bimbingan/kp/proses.php';

  function muatRingkasanStatistik() {
      var prodiVal = $('#fProdi').val() || '';
      var pembVal  = $('#fPembimbing').val() || '';
      $.ajax({
          url: PROSES_URL,
          type: 'POST',
          dataType: 'json',
          data: {
              'ambil-ringkasan-statistik': 1,
              prodi: prodiVal,
              pembimbing: pembVal
          },
          success: function(res) {
              if (res && res.status) {
                  $('#statTotal').text(res.total ?? 0);
                  $('#statAktif').text(res.aktif ?? 0);
                  $('#statSiapUjian').text(res.siap_ujian ?? 0);
                  $('#statRataProgres').text((res.rata_progres ?? 0) + '%');
              }
          }
      });
  }

  function muatTabelBimbingan() {
      var wrap = $('#tabelWrap');
      wrap.html('<div class="acc-loading py-12 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-slate-400 text-xs">Memuat data mahasiswa bimbingan...</p></div>');

      var cariVal   = $('#fCari').val();
      var prodiVal  = $('#fProdi').val();
      var pembVal   = $('#fPembimbing').val();
      var statusVal = $('#fStatus').val();
      var progVal   = $('#fProgres').val();

      $.ajax({
          url: PROSES_URL,
          type: 'POST',
          data: {
              'ambil-tabel-bimbingan-kp': 1,
              cari: cariVal,
              prodi: prodiVal,
              pembimbing: pembVal,
              status: statusVal,
              progres: progVal
          },
          success: function(html) {
              wrap.html(html);
          },
          error: function(xhr, status, error) {
              wrap.html('<div class="p-8 text-center text-rose-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-xs font-semibold">Gagal memuat data bimbingan KP dari server.</p></div>');
          }
      });
  }

  // Handler Buka Drawer Arsip / Progresline
  function bukaDrawerArsip(nim, infoMhs) {
      var drawer = $('#drawerArsip');
      var konten = $('#drawerArsipKonten');

      if (infoMhs) {
          $('#drawerHeaderNama').text(infoMhs.nama || '-');
          $('#drawerHeaderNim').text(nim || '-');
          $('#drawerHeaderProdi').text(infoMhs.prodi || '-');
      } else {
          $('#drawerHeaderNama').text('Memuat Nama...');
          $('#drawerHeaderNim').text(nim || '-');
          $('#drawerHeaderProdi').text('-');
      }

      konten.html('<div class="acc-loading py-12 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-slate-400 text-xs">Memuat data arsip &amp; riwayat progresline...</p></div>');
      drawer.addClass('show');

      $.ajax({
          url: PROSES_URL,
          type: 'POST',
          data: {
              'ambil-arsip-riwayat': 1,
              nim: nim
          },
          dataType: 'json',
          success: function(res) {
              if (res.status) {
                  $('#drawerHeaderNama').text(res.nama);
                  $('#drawerHeaderNim').text(res.nim);
                  $('#drawerHeaderProdi').text(res.prodi);

                  var sttBadge = $('#drawerHeaderStatus');
                  var stt = (res.status_kp || 'aktif').toLowerCase();
                  if (stt === 'selesai' || stt === 'alumni') {
                      sttBadge.text('Selesai').removeClass('hidden').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700');
                  } else if (stt === 'tidak aktif') {
                      sttBadge.text('Tidak Aktif').removeClass('hidden').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700');
                  } else {
                      sttBadge.text('Aktif').removeClass('hidden').attr('class', 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700');
                  }

                  konten.html(res.html);
              } else {
                  konten.html('<div class="text-center py-12 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><p class="text-xs font-semibold">' + (res.pesan || 'Gagal memuat arsip.') + '</p></div>');
              }
          },
          error: function(xhr, status, error) {
              var pesanError = 'Terjadi kesalahan pada server saat memuat riwayat progresline.';
              if (xhr.responseJSON && xhr.responseJSON.pesan) {
                  pesanError = xhr.responseJSON.pesan;
              }
              konten.html('<div class="text-center py-12 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><p class="text-xs font-semibold">' + pesanError + '</p></div>');
          }
      });
  }

  // Tutup Drawer
  $(document).on('click', '[data-drawer-close]', function() {
      $('#drawerArsip').removeClass('show');
  });

  $('#drawerArsip').on('click', function(e) {
      if ($(e.target).is('#drawerArsip')) {
          $(this).removeClass('show');
      }
  });

  $(document).keyup(function(e) {
      if (e.key === "Escape") {
          $('#drawerArsip').removeClass('show');
      }
  });

  // Tombol Arsip di Tabel
  $(document).on('click', '.btnArsip', function() {
      var nim   = $(this).data('nim');
      var nama  = $(this).data('nama');
      var prodi = $(this).data('prodi');
      bukaDrawerArsip(nim, { nama: nama, prodi: prodi });
  });

  // Accordion Drawer Handler
  $(document).on('click', '#accordionArsip .btn-toggle-accordion', function(e) {
      var card = $(this).closest('.card-completed');
      var body = card.find('.acc-body');
      var chevron = $(this).find('.acc-chevron');

      body.stop(true, true).slideToggle(200, function() {
          if (body.is(':visible')) {
              body.removeClass('hidden');
              chevron.addClass('rotate-180');
          } else {
              body.addClass('hidden');
              chevron.removeClass('rotate-180');
          }
      });
  });

  $(document).on('click', '#btnBukaSemuaAcc', function() {
      $('#accordionArsip .card-completed .acc-body').slideDown(200).removeClass('hidden');
      $('#accordionArsip .card-completed .acc-chevron').addClass('rotate-180');
  });

  $(document).on('click', '#btnTutupSemuaAcc', function() {
      $('#accordionArsip .card-completed .acc-body').slideUp(200, function() {
          $(this).addClass('hidden');
      });
      $('#accordionArsip .card-completed .acc-chevron').removeClass('rotate-180');
  });

  // Event Listeners Filter
  var timeoutCari = null;
  $('#fCari').on('keyup search', function() {
      clearTimeout(timeoutCari);
      timeoutCari = setTimeout(function() {
          muatTabelBimbingan();
      }, 300);
  });

  $('#fProdi, #fPembimbing').on('change', function() {
      muatRingkasanStatistik();
      muatTabelBimbingan();
  });

  $('#fStatus, #fProgres').on('change', function() {
      muatTabelBimbingan();
  });

  var defaultPembimbing = '<?= ($userLevel === 9) ? "" : "saya" ?>';

  $('#btnReset').on('click', function() {
      $('#fCari').val('');
      if ($('#fProdi').is('select')) $('#fProdi').val('');
      if ($('#fPembimbing').is('select')) $('#fPembimbing').val(defaultPembimbing);
      $('#fStatus').val('aktif');
      $('#fProgres').val('');
      muatRingkasanStatistik();
      muatTabelBimbingan();
  });

  // Inisialisasi awal saat halaman dimuat
  $(document).ready(function() {
      muatRingkasanStatistik();
      muatTabelBimbingan();
  });
</script>
