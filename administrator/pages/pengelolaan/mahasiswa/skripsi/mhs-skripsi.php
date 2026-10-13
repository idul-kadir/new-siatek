<?php
require __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

$userLevel = (int)($_SESSION['level'] ?? 0);

// Jika level 3 atau 5, ambil program studi user
$isFilterProdi = in_array($userLevel, [3, 5], true);
$userProdi     = $isFilterProdi ? get_prodi_user() : '';
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

  .btn-circle-lg {
    width: 2.5rem;
    height: 2.5rem;
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

  /* CSS Spinner untuk Loading Indicator */
  .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
  .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
  .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }

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
          <i class="fas fa-book"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Skripsi Mahasiswa</h1>
          <p class="text-xs text-slate-500">Monitoring &amp; progres tahapan skripsi mahasiswa</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Filter Bar ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      
      <!-- Search NIM / Nama -->
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama atau NIM mahasiswa..."
          class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
      </div>

      <!-- Filter Prodi -->
      <select id="fProdi"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 <?= $isFilterProdi ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isFilterProdi ? 'disabled' : '' ?>>
        <?php if (!$isFilterProdi): ?>
          <option value="">Semua Program Studi</option>
          <?php
          $qProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
          if($qProdi){
              while($pr = mysqli_fetch_assoc($qProdi)){
                  echo '<option value="' . tampil($pr['kode']) . '">' . tampil($pr['nama']) . '</option>';
              }
          }
          ?>
        <?php else: ?>
          <?php
          $qProdi = query("SELECT kode, nama FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
          $namaProdiUser = $userProdi;
          if($qProdi && ($pr = mysqli_fetch_assoc($qProdi))){
              $namaProdiUser = $pr['nama'];
          }
          echo '<option value="' . tampil($userProdi) . '" selected>' . tampil($namaProdiUser) . '</option>';
          ?>
        <?php endif; ?>
      </select>

      <!-- Filter Angkatan -->
      <select id="fAngkatan"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Angkatan</option>
        <?php
        $whereAngkatan = "WHERE m.angkatan IS NOT NULL AND m.angkatan != ''";
        if ($isFilterProdi && $userProdi !== '') {
            $whereAngkatan .= " AND m.prodi = '" . bersihkan($userProdi) . "'";
        }
        $qAngkatan = query("SELECT DISTINCT m.angkatan FROM skripsi s JOIN mahasiswa m ON s.nim = m.nim $whereAngkatan ORDER BY m.angkatan DESC");
        if($qAngkatan){
            while($ang = mysqli_fetch_assoc($qAngkatan)){
                echo '<option value="' . tampil($ang['angkatan']) . '">Angkatan ' . tampil($ang['angkatan']) . '</option>';
            }
        }
        ?>
      </select>

      <!-- Filter Status Mahasiswa -->
      <select id="fStatus"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="Aktif" selected>Aktif</option>
        <option value="tidak aktif">Tidak Aktif</option>
        <option value="Alumni">Alumni</option>
        <option value="Kadaluarsa">Kadaluarsa</option>
      </select>

      <!-- Filter Progres / SOP Skripsi -->
      <select id="fProgres"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Progres (SOP)</option>
        <?php
        $qSop = query("SELECT no, keterangan FROM sop_skripsi ORDER BY no ASC");
        if($qSop){
            while($sop = mysqli_fetch_assoc($qSop)){
                echo '<option value="' . (int)$sop['no'] . '">' . tampil($sop['keterangan']) . '</option>';
            }
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

  <!-- ===== Daftar Skripsi ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Skripsi</h2>
        </div>
      </div>
      <div id="tabelWrap" class="max-h-[600px] overflow-y-auto">
        <div class="acc-loading py-4 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data...</p></div>
      </div>
    </div>
  </section>

  <!-- ===== Modal Info Aksi ===== -->
  <div class="modal-overlay" id="modalInfoAksi" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden p-6 relative">
      <button type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      <div class="text-center pt-2">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-3xl text-slate-400 mb-4">
          <i class="fas fa-clock"></i>
        </div>
        <h3 class="mb-2 text-lg font-bold text-slate-800">Menunggu Mahasiswa</h3>
        <p class="text-sm text-slate-500">Tidak ada aksi yang bisa dilakukan oleh operator untuk saat ini. Tahap skripsi sedang dikerjakan oleh mahasiswa bersangkutan.</p>
      </div>
    </div>
  </div>

  <!-- ===== Modal Konfirmasi Hapus ===== -->
  <div class="modal-overlay" id="modalKonfirmasiHapus" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden p-6 relative">
      <button type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 text-xl leading-none" id="btnCloseKonfirmasiHapus">&times;</button>
      <div class="text-center pt-2">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 text-3xl text-rose-500 mb-4">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="mb-2 text-lg font-bold text-slate-800">Konfirmasi Penolakan</h3>
        <p class="text-sm text-slate-500 mb-6">Apakah Anda yakin menolak pendaftaran ini? Mahasiswa akan Dihapus dari data pendaftaran skripsi!</p>
        <div class="flex gap-3">
          <button type="button" id="btnBatalHapus" class="flex-1 rounded-lg bg-slate-200 hover:bg-slate-300 py-2.5 text-sm font-semibold text-slate-700">Batal</button>
          <button type="button" id="btnYaHapus" class="flex-1 rounded-lg bg-rose-600 hover:bg-rose-700 py-2.5 text-sm font-semibold text-white">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Modal Lembar Kerja ===== -->
  <div class="modal-overlay" id="modalLembarKerja" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
        <h6 class="font-semibold text-slate-900 text-[15px]">
            <i class="fas fa-clipboard-check mr-2 text-[#f97316]"></i>
            Lembar Kerja Operator
        </h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>
      <div id="lembarKerjaKonten" class="p-6 max-h-[70vh] overflow-y-auto">
        <div class="acc-loading py-8 text-center">
            <div class="spinner-slate mx-auto"></div>
            <p class="mt-3 text-muted" style="font-size: .85rem;">Memuat lembar kerja...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Script ===== -->
  <script>
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/skripsi/proses.php';

    function setButtonLoading(btn, isLoading) {
        if(!btn) return;
        if (isLoading) {
            btn.classList.add('is-loading');
            btn.setAttribute('disabled', '');
            btn.innerHTML = '<span class="spinner-slate" style="border-top-color:#fff;"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
        } else {
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
            btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
        }
    }

    function toast(pesan, tipe) {
      tipe = tipe || 'info';
      var container = document.getElementById('toastContainer');
      var item = document.createElement('div');
      item.className = 'toast-item toast-' + tipe;
      var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
      item.innerHTML = '<i class="fas ' + ikon + ' toast-icon"></i><span>' + pesan + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'hide\');setTimeout(function(){item.remove()},350)">&times;</button>';
      container.appendChild(item);
      requestAnimationFrame(function(){ item.classList.add('show'); });
      setTimeout(function(){ item.classList.add('hide'); setTimeout(function(){ item.remove(); }, 350); }, 4000);
    }

    function muatUlangTabel() {
        var container = document.getElementById('tabelWrap');
        if (!container) return;
        container.innerHTML = '<div class="acc-loading py-4 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data...</p></div>';

        var cari = $('#fCari').val() || '';
        var prodi = '<?= $isFilterProdi ? addslashes($userProdi) : '' ?>' || $('#fProdi').val() || '';
        var angkatan = $('#fAngkatan').val() || '';
        var status = $('#fStatus').val() || '';
        var progres = $('#fProgres').val() || '';

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 
                'ambil-tabel-skripsi': 1,
                'cari': cari,
                'prodi': prodi,
                'angkatan': angkatan,
                'status': status,
                'progres': progres
            },
            dataType: 'json',
            success: function(respon) {
                if (respon.status) {
                    container.innerHTML = respon.data;
                } else {
                    container.innerHTML = '<div class="acc-empty text-center py-4">Gagal memuat data: ' + (respon.pesan || '') + '</div>';
                }
            },
            error: function() {
                container.innerHTML = '<div class="acc-empty text-center py-4">Terjadi kesalahan pada server</div>';
            }
        });
    }

    $(document).ready(function(){
      muatUlangTabel();

      // Filter events
      var delayTimer;
      $('#fCari').on('keyup input', function(){
          clearTimeout(delayTimer);
          delayTimer = setTimeout(muatUlangTabel, 300);
      });

      $('#fProdi, #fAngkatan, #fStatus, #fProgres').on('change', function(){
          muatUlangTabel();
      });

      $('#btnReset').on('click', function(){
          $('#fCari').val('');
          <?php if (!$isFilterProdi): ?>
          $('#fProdi').val('');
          <?php else: ?>
          $('#fProdi').val('<?= addslashes($userProdi) ?>');
          <?php endif; ?>
          $('#fAngkatan').val('');
          $('#fStatus').val('Aktif');
          $('#fProgres').val('');
          muatUlangTabel();
      });

      // Tombol Aksi Handlers
      $(document).on('click', '.btnArsip', function(){
          var nim = $(this).data('nim');
          toast('Membuka arsip mahasiswa NIK/NIM: ' + nim, 'info');
      });

      $(document).on('click', '.btnLembarKerja', function(){
          var nim = $(this).data('nim');
          var progres = $(this).data('progres');
          var statusSkripsi = ($(this).data('status-skripsi') || '').toString().toLowerCase();
          var isAdmin = $(this).data('is-admin') == 1;

          if (statusSkripsi === 'tidak aktif' || isAdmin) {
              // Buka modal lembar kerja
              $('#modalLembarKerja').addClass('show');
              $('#lembarKerjaKonten').html('<div class="acc-loading py-8 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat lembar kerja...</p></div>');
              
              // Load isi lembar kerja dari proses.php
              $.ajax({
                  url: PROSES_URL,
                  method: 'POST',
                  data: {
                      'muat-lembar-kerja': 1,
                      'nim': nim,
                      'status_skripsi': statusSkripsi,
                      'progres': progres
                  },
                  dataType: 'json',
                  success: function(respon) {
                      if(respon.status) {
                          $('#lembarKerjaKonten').html(respon.data);
                      } else {
                          $('#lembarKerjaKonten').html('<div class="text-center py-8 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="font-medium">' + respon.pesan + '</p></div>');
                      }
                  },
                  error: function() {
                      $('#lembarKerjaKonten').html('<div class="text-center py-8 text-slate-500"><i class="fas fa-exclamation-circle text-3xl mb-3"></i><p class="font-medium">Terjadi kesalahan pada server saat memuat lembar kerja.</p></div>');
                  }
              });
          } else {
              $('#modalInfoAksi').addClass('show');
          }
      });

      // Tutup modal hanya dengan tombol close (X)
      $(document).on('click', '[data-modal-close]', function(e){
          e.stopPropagation();
          $(this).closest('.modal-overlay').removeClass('show');
      });

      $(document).on('click', '.btnHapus', function(){
          var nim = $(this).data('nim');
          var tr = $(this).closest('tr');
          var nama = tr.find('.font-semibold').text() || 'Mahasiswa ini';
          if(typeof konfirmasiHapus === 'function') {
              konfirmasiHapus('Data skripsi ' + nama, function() {
                  $.ajax({
                      url: PROSES_URL,
                      method: 'POST',
                      data: { 'hapus-data-skripsi': 1, 'nim': nim },
                      dataType: 'json',
                      success: function(respon) {
                          toast(respon.pesan, respon.status ? 'success' : 'error');
                          if(respon.status) muatUlangTabel();
                      }
                  });
              });
          }
      });
    });
  </script>

</main>