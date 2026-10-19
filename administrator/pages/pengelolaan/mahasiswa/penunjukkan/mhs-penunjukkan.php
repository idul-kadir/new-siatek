<?php
require_once __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// Hak akses: Level 3 (Operator Prodi), 4 (Operator Jurusan), 5 (Kaprodi), 6, dan 9
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
        <p class="text-sm text-slate-600 mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="<?= base_url() ?>/administrator/beranda" class="inline-flex items-center px-4 py-2 bg-[#1a365d] hover:bg-[#234670] text-white text-xs font-semibold rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>
</main>
<?php
    return;
}

// Khusus Operator Prodi (Level 3) & Kaprodi (Level 5), wajib sesuai prodi yang diampu
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
  .btn-circle:hover .tip {
    opacity: 1;
    visibility: visible;
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

  .content-scroll {
    overflow-y: auto;
    min-height: 0;
  }

  .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
  .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
  .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }

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
</style>

<main class="content-area content-scroll">

  <!-- Toast Container -->
  <div id="toastContainer"></div>

  <!-- ===== Page Header ===== -->
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
          <i class="fas fa-file-signature"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Penunjukkan Mahasiswa</h1>
          <p class="text-xs text-slate-500">
            Penetapan dosen pembimbing dan penguji mahasiswa Kerja Praktek serta Skripsi
            <?php if ($isFilterProdi && $namaProdiUser !== ''): ?>
              &bull; <span class="font-semibold text-orange-600"><?= tampil($namaProdiUser) ?></span>
            <?php endif; ?>
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <?php if ($isFilterProdi && $namaProdiUser !== ''): ?>
          <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
            <i class="fas fa-building-columns text-[11px] text-orange-500"></i> <?= tampil($namaProdiUser) ?>
          </span>
        <?php endif; ?>
      </div>
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

  <!-- ===== Filter Bar ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      
      <!-- Search NIM / Nama -->
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama atau NIM mahasiswa..."
          class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
      </div>

      <!-- Filter Kategori (Semua / Skripsi / Kerja Praktek) -->
      <select id="fKategori"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="semua" selected>Semua Kategori</option>
        <option value="skripsi">Skripsi</option>
        <option value="kp">Kerja Praktek</option>
      </select>

      <!-- Filter Jenis Penunjukkan -->
      <select id="fJenis"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="semua" selected>Semua Jenis Penunjukkan</option>
        <option value="pembimbing">Dosen Pembimbing</option>
        <option value="penguji">Dosen Penguji</option>
      </select>

      <!-- Filter Prodi -->
      <select id="fProdi"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isFilterProdi ? 'disabled' : '' ?>>
        <?php if (!$isFilterProdi): ?>
          <option value="" selected>Semua Program Studi</option>
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

      <!-- Reset Button -->
      <button type="button" id="btnReset"
        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
        <i class="fas fa-times mr-1"></i>Reset
      </button>

    </div>
  </section>

  <!-- ===== Daftar Penunjukkan ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Mahasiswa Menunggu Penunjukkan</h2>
        </div>
      </div>
      <div id="tabelWrap" class="max-h-[600px] overflow-y-auto">
        <div class="acc-loading py-4 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data...</p></div>
      </div>
    </div>
  </section>

  <!-- ===== Modal Penunjukkan Dosen ===== -->
  <div class="modal-overlay" id="modalPenunjukkan" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col relative border border-slate-200">
      
      <!-- Header Modal -->
      <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50 flex-shrink-0">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
            <i class="fas fa-file-signature"></i>
          </div>
          <div>
            <h3 class="font-bold text-slate-800 text-sm leading-snug" id="modalJudulTugas">Penunjukkan Dosen</h3>
            <p class="text-xs text-slate-500" id="modalSubJudul">Tetapkan dosen pembimbing atau penguji</p>
          </div>
        </div>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-2xl leading-none px-2" data-modal-close>&times;</button>
      </div>

      <!-- Info Mahasiswa -->
      <div class="bg-slate-50/70 border-b border-slate-100 px-5 py-3 text-xs text-slate-700">
        <div>
          <span class="font-bold text-slate-800 text-sm block" id="mhsNama">-</span>
          <span class="text-slate-500 font-mono text-xs mt-0.5 block" id="mhsNimProdi">-</span>
        </div>
        <p class="text-slate-600 mt-2 text-xs leading-relaxed" id="mhsJudul">-</p>
      </div>

      <!-- Form Body -->
      <form id="formPenunjukkan" class="p-5 space-y-4">
        <input type="hidden" name="simpan-penunjukkan" value="1">
        <input type="hidden" name="nim" id="inNim">
        <input type="hidden" name="sumber" id="inSumber">
        <input type="hidden" name="jenis_tugas" id="inJenisTugas">
        <input type="hidden" name="progres_sekarang" id="inProgres">

        <!-- Nomor Surat -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            Nomor Surat Penunjukkan <span class="text-rose-500">*</span>
          </label>
          <input type="text" name="no_surat" id="inNoSurat" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white" placeholder="Contoh: 123/UN47.B5/TU/2026" required>
        </div>

        <!-- Dynamic Dosen Fields -->
        <div id="dynamicDosenFields" class="space-y-3">
          <div class="py-6 text-center"><div class="spinner-slate mx-auto"></div></div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" class="px-4 py-2 text-xs rounded-lg border border-slate-200 text-slate-600 font-medium hover:bg-slate-100 transition" data-modal-close>Batal</button>
          <button type="submit" id="btnSimpanPenunjukkan" class="px-4 py-2 text-xs rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-sm transition disabled:opacity-50" data-text="Tetapkan Penunjukkan">
            Tetapkan Penunjukkan
          </button>
        </div>
      </form>

    </div>
  </div>

</main>

<script>
(function() {
  var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/penunjukkan/proses.php';

  // ================= TOAST NOTIFICATION =================
  window.toast = function(pesan, tipe) {
    tipe = tipe || 'info';
    var iconMap = {
      success: 'fas fa-check-circle',
      error: 'fas fa-exclamation-circle',
      info: 'fas fa-info-circle'
    };
    var container = document.getElementById('toastContainer');
    if (!container) return;

    var toastEl = document.createElement('div');
    toastEl.className = 'toast-item toast-' + tipe;
    toastEl.innerHTML = '<i class="' + (iconMap[tipe] || iconMap.info) + ' toast-icon"></i>' +
                        '<span>' + pesan + '</span>' +
                        '<button type="button" class="toast-close">&times;</button>';

    container.appendChild(toastEl);
    setTimeout(function() { toastEl.classList.add('show'); }, 10);

    var closeBtn = toastEl.querySelector('.toast-close');
    if (closeBtn) {
      closeBtn.onclick = function() { removeToast(toastEl); };
    }
    setTimeout(function() { removeToast(toastEl); }, 4000);
  };

  function removeToast(el) {
    if (!el || !el.parentNode) return;
    el.classList.remove('show');
    el.classList.add('hide');
    setTimeout(function() { if (el.parentNode) el.parentNode.removeChild(el); }, 350);
  }

  // ================= LOADING BUTTON HELPER =================
  function setButtonLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
      btn.classList.add('is-loading');
      btn.setAttribute('disabled', '');
      btn.innerHTML = '<span class="spinner-slate mr-1.5" style="border-top-color:#fff;"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
    } else {
      btn.classList.remove('is-loading');
      btn.removeAttribute('disabled');
      btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
    }
  }

  // ================= UPDATE SIDEBAR BADGE =================
  function updateSidebarBadge(totalMenunggu) {
    var sbBadge = document.getElementById('sidebarPenunjukkanBadge');
    if (sbBadge) {
      if (totalMenunggu > 0) {
        sbBadge.textContent = totalMenunggu > 99 ? '99+' : String(totalMenunggu);
        sbBadge.classList.remove('hidden');
        sbBadge.classList.add('inline-flex');
      } else {
        sbBadge.classList.add('hidden');
        sbBadge.classList.remove('inline-flex');
      }
    }
  }

  // ================= MUAT ULANG TABEL =================
  function muatUlangTabel() {
    var container = document.getElementById('tabelWrap');
    if (!container) return;

    container.innerHTML = '<div class="acc-loading py-6 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data...</p></div>';

    var cariVal     = $('#fCari').val() || '';
    var kategoriVal = $('#fKategori').val() || 'semua';
    var jenisVal    = $('#fJenis').val() || 'semua';
    var prodiVal    = '<?= $isFilterProdi ? addslashes($userProdi) : '' ?>' || $('#fProdi').val() || '';

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'ambil-tabel-antrian': 1,
        'cari': cariVal,
        'kategori': kategoriVal,
        'jenis': jenisVal,
        'prodi': prodiVal
      },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          container.innerHTML = res.data;
          updateSidebarBadge(res.total || 0);
        } else {
          container.innerHTML = '<div class="py-12 text-center text-rose-500 text-sm">' + (res.pesan || 'Gagal memuat data') + '</div>';
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX error:', status, error, xhr.responseText);
        container.innerHTML = '<div class="py-12 text-center text-rose-500 text-sm">Terjadi kesalahan koneksi server: ' + (error || status) + '</div>';
      }
    });
  }

  window.muatUlangTabel = muatUlangTabel;

  // ================= CEK BADGE LIVE =================
  function cekBadgeLive() {
    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: { 'cek-badge-penunjukkan': 1 },
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          updateSidebarBadge(res.total_menunggu || 0);
        }
      }
    });
  }

  // Filter Event Listeners
  $('#fCari').on('keyup', function() {
    clearTimeout(window._searchTimer);
    window._searchTimer = setTimeout(muatUlangTabel, 300);
  });
  $('#fKategori, #fJenis, #fProdi').on('change', muatUlangTabel);

  $('#btnReset').on('click', function() {
    $('#fCari').val('');
    $('#fKategori').val('semua');
    $('#fJenis').val('semua');
    if ($('#fProdi').is('select') && !$('#fProdi').prop('disabled')) {
      $('#fProdi').val('');
    }
    muatUlangTabel();
  });

  // ================= OPEN MODAL PENUNJUKKAN =================
  $(document).on('click', '.btnTentukanDosen', function() {
    var btn = $(this);
    var sumber = btn.data('sumber');
    var jenisTugas = btn.data('jenis-tugas');
    var nim = String(btn.data('nim'));
    var nama = String(btn.data('nama'));
    var prodi = String(btn.data('prodi'));
    var judul = String(btn.data('judul'));
    var progres = btn.data('progres');

    $('#inNim').val(nim);
    $('#inSumber').val(sumber);
    $('#inJenisTugas').val(jenisTugas);
    $('#inProgres').val(progres);
    $('#inNoSurat').val('');

    $('#mhsNama').text(nama);
    $('#mhsNimProdi').text(nim + ' • ' + prodi);
    $('#mhsJudul').text(judul || '-');

    var judulModal = 'Penunjukkan Dosen';
    var subJudul = 'Tetapkan dosen pembimbing atau penguji';

    if (sumber === 'skripsi') {
      if (jenisTugas === 'pembimbing') {
        judulModal = 'Penunjukkan Pembimbing Skripsi';
        subJudul = 'Tetapkan Dosen Pembimbing 1 dan Pembimbing 2';
      } else if (jenisTugas === 'penguji_hasil') {
        judulModal = 'Penunjukkan Penguji Ujian Hasil';
        subJudul = 'Tetapkan Dosen Penguji Ujian Hasil Skripsi (1-5 orang)';
      } else if (jenisTugas === 'penguji_tutup') {
        judulModal = 'Penunjukkan Penguji Ujian Tutup';
        subJudul = 'Tetapkan Dosen Penguji Ujian Tutup / Komprehensif (1-5 orang)';
      }
    } else if (sumber === 'kp') {
      if (jenisTugas === 'pembimbing') {
        judulModal = 'Penunjukkan Pembimbing Kerja Praktek';
        subJudul = 'Tetapkan Dosen Pembimbing Kerja Praktek';
      } else if (jenisTugas === 'penguji') {
        judulModal = 'Penunjukkan Penguji Seminar KP';
        subJudul = 'Tetapkan Dosen Penguji Seminar KP';
      }
    }

    $('#modalJudulTugas').text(judulModal);
    $('#modalSubJudul').text(subJudul);

    var container = $('#dynamicDosenFields');
    container.html('<div class="py-6 text-center"><div class="spinner-slate mx-auto"></div></div>');
    $('#modalPenunjukkan').addClass('show');

    // Fetch list dosen & saran prefill
    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: {
        'ambil-detail-penunjukkan': 1,
        'nim': nim,
        'sumber': sumber,
        'jenis_tugas': jenisTugas
      },
      dataType: 'json',
      success: function(res) {
        if (!res.status) {
          container.html('<p class="text-xs text-rose-500 text-center">Gagal memuat list dosen.</p>');
          return;
        }

        var listDosen = res.list_dosen || [];
        var saran = res.saran_dosen || {};

        function renderSelectOptions(selectedVal) {
          var opts = '<option value="">-- Pilih Dosen --</option>';
          listDosen.forEach(function(d) {
            var isSel = (String(d.id) === String(selectedVal)) ? ' selected' : '';
            opts += '<option value="' + d.id + '"' + isSel + '>' + d.nama + '</option>';
          });
          return opts;
        }

        var htmlFields = '';

        if (sumber === 'skripsi') {
          if (jenisTugas === 'pembimbing') {
            htmlFields += '<div>' +
                            '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Pembimbing 1 <span class="text-rose-500">*</span></label>' +
                            '<select name="pembimbing1" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white" required>' +
                              renderSelectOptions(saran.pembimbing1 || '') +
                            '</select>' +
                          '</div>' +
                          '<div>' +
                            '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Pembimbing 2</label>' +
                            '<select name="pembimbing2" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">' +
                              renderSelectOptions(saran.pembimbing2 || '') +
                            '</select>' +
                          '</div>';
          } else if (jenisTugas === 'penguji_hasil' || jenisTugas === 'penguji_tutup') {
            for (var i = 1; i <= 5; i++) {
              var labelRec = '';
              var curVal = saran['penguji' + i] || '';
              if (jenisTugas === 'penguji_hasil' && i === 1 && curVal) labelRec = ' <span class="text-orange-600 font-normal">(Disarankan: Pembimbing 1)</span>';
              if (jenisTugas === 'penguji_hasil' && i === 2 && curVal) labelRec = ' <span class="text-orange-600 font-normal">(Disarankan: Pembimbing 2)</span>';
              var isReq = (i === 1) ? ' required' : '';
              var star = (i === 1) ? ' <span class="text-rose-500">*</span>' : '';

              htmlFields += '<div>' +
                              '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Penguji ' + i + star + labelRec + '</label>' +
                              '<select name="penguji' + i + '" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"' + isReq + '>' +
                                renderSelectOptions(curVal) +
                              '</select>' +
                            '</div>';
            }
          }
        } else if (sumber === 'kp') {
          if (jenisTugas === 'pembimbing') {
            htmlFields += '<div>' +
                            '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Pembimbing KP <span class="text-rose-500">*</span></label>' +
                            '<select name="pembimbing1" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white" required>' +
                              renderSelectOptions(saran.pembimbing1 || '') +
                            '</select>' +
                          '</div>';
          } else if (jenisTugas === 'penguji') {
            var curP1 = saran.penguji1 || '';
            var labelRecP1 = curP1 ? ' <span class="text-orange-600 font-normal">(Disarankan: Pembimbing KP)</span>' : '';
            htmlFields += '<div>' +
                            '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Penguji 1 <span class="text-rose-500">*</span>' + labelRecP1 + '</label>' +
                            '<select name="penguji1" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white" required>' +
                              renderSelectOptions(curP1) +
                            '</select>' +
                          '</div>' +
                          '<div>' +
                            '<label class="block text-xs font-semibold text-slate-700 mb-1">Dosen Penguji 2</label>' +
                            '<select name="penguji2" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">' +
                              renderSelectOptions('') +
                            '</select>' +
                          '</div>';
          }
        }

        container.html(htmlFields);
      },
      error: function() {
        container.html('<p class="text-xs text-rose-500 text-center">Gagal terhubung ke server.</p>');
      }
    });
  });

  // ================= CLOSE MODAL =================
  $(document).on('click', '[data-modal-close]', function() {
    $('.modal-overlay').removeClass('show');
  });

  // ================= SUBMIT FORM =================
  $('#formPenunjukkan').on('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('btnSimpanPenunjukkan');
    setButtonLoading(btn, true);

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          toast(res.pesan, 'success');
          $('#modalPenunjukkan').removeClass('show');
          muatUlangTabel();
          cekBadgeLive();
        } else {
          toast(res.pesan || 'Gagal menyimpan penunjukkan', 'error');
        }
      },
      error: function() {
        toast('Terjadi kesalahan pada server saat menyimpan penunjukkan', 'error');
      },
      complete: function() {
        setButtonLoading(btn, false);
      }
    });
  });

  // Inisialisasi awal
  $(document).ready(function() {
    muatUlangTabel();
    cekBadgeLive();
  });

})();
</script>
