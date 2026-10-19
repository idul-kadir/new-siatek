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

  /* ===== Drawer Arsip & Riwayat Tahapan (Offcanvas) ===== */
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
  .hidden {
    display: none !important;
  }
</style>

<main class="content-area content-scroll">

  <!-- Toast Container -->
  <div id="toastContainer"></div>

  <!-- ===== Page Header ===== -->
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
          <i class="fas fa-briefcase"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Kerja Praktek Mahasiswa</h1>
          <p class="text-xs text-slate-500">Monitoring &amp; progres tahapan kerja praktek mahasiswa</p>
        </div>
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
      
      <!-- Search NIM / Nama / Tempat -->
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama, NIM, atau tempat KP..."
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
          if ($userProdi !== '') {
              $qProdi = query("SELECT kode, nama FROM prodi WHERE kode = '" . bersihkan($userProdi) . "' LIMIT 1");
              $namaProdiUser = $userProdi;
              if($qProdi && ($pr = mysqli_fetch_assoc($qProdi))){
                  $namaProdiUser = $pr['nama'];
              }
              echo '<option value="' . tampil($userProdi) . '" selected>' . tampil($namaProdiUser) . '</option>';
          } else {
              echo '<option value="" selected disabled>Belum ada program studi</option>';
          }
          ?>
        <?php endif; ?>
      </select>

      <!-- Filter Angkatan -->
      <select id="fAngkatan"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Angkatan</option>
        <?php
        $whereAngkatan = "WHERE m.angkatan IS NOT NULL AND m.angkatan != ''";
        if ($isFilterProdi) {
            if ($userProdi !== '') {
                $whereAngkatan .= " AND m.prodi = '" . bersihkan($userProdi) . "'";
            } else {
                $whereAngkatan .= " AND 1=0";
            }
        }
        $qAngkatan = query("SELECT DISTINCT m.angkatan FROM kp k JOIN mahasiswa m ON k.nim = m.nim $whereAngkatan ORDER BY m.angkatan DESC");
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

      <!-- Filter Progres / SOP KP -->
      <select id="fProgres"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Progres (SOP)</option>
        <?php
        $qCols = query("SHOW COLUMNS FROM sop_kp");
        if (!$qCols) {
            $err = mysqli_error($GLOBALS['koneksi'] ?? $koneksi);
            echo '<option value="" disabled>Tabel sop_kp tidak ditemukan: ' . tampil($err) . '</option>';
        } else {
            $cols = [];
            while ($c = mysqli_fetch_assoc($qCols)) {
                $cols[] = $c['Field'];
            }
            $qCount = query("SELECT COUNT(*) AS c FROM sop_kp");
            $totalRows = $qCount ? (int)mysqli_fetch_assoc($qCount)['c'] : 0;
            if ($totalRows === 0) {
                echo '<option value="" disabled>Tabel sop_kp masih kosong (0 data di DB)</option>';
            } else {
                $qData = query("SELECT * FROM sop_kp");
                if ($qData) {
                    while ($row = mysqli_fetch_assoc($qData)) {
                        $val = $row['no'] ?? $row['id'] ?? $row['proses'] ?? $row['nomor'] ?? reset($row);
                        $lbl = $row['keterangan'] ?? $row['nama'] ?? $row['tahapan'] ?? $row['deskripsi'] ?? $row['judul'] ?? '';
                        if ($lbl === '') {
                            foreach ($row as $k => $v) {
                                if (!in_array(strtolower($k), ['id', 'no', 'nomor', 'proses', 'waktu']) && !is_numeric($v) && trim($v) !== '') {
                                    $lbl = $v;
                                    break;
                                }
                            }
                        }
                        if ($val !== '') {
                            echo '<option value="' . tampil($val) . '">' . tampil($lbl ?: ('Tahap ' . $val)) . '</option>';
                        }
                    }
                }
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

  <!-- ===== Daftar Kerja Praktek ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Kerja Praktek</h2>
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
        <p class="text-sm text-slate-500">Tidak ada aksi yang bisa dilakukan oleh operator untuk saat ini. Tahap kerja praktek sedang dikerjakan oleh mahasiswa bersangkutan.</p>
      </div>
    </div>
  </div>

  <?php if ($userLevel === 9): ?>
  <!-- ===== Modal Hard Reset KP (Khusus Super Admin Level 9 - Keamanan Berlapis) ===== -->
  <div class="modal-overlay" id="modalHardReset" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative border border-rose-200">
      <!-- Header Peringatan Bahaya -->
      <div class="bg-rose-50 border-b border-rose-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5 text-rose-700 font-bold text-sm">
          <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-600 text-white shadow-xs">
            <i class="fas fa-triangle-exclamation text-sm"></i>
          </span>
          <span>HARD RESET DATA KERJA PRAKTEK</span>
        </div>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none transition" data-modal-close>&times;</button>
      </div>

      <div class="p-6 space-y-4">
        <!-- Callout Bahaya -->
        <div class="rounded-xl bg-rose-50/70 border border-rose-200/80 p-3.5 text-xs text-rose-800 leading-relaxed">
          <strong class="font-bold block mb-1 text-rose-900 flex items-center gap-1.5">
            <i class="fas fa-radiation"></i> PERINGATAN TINGKAT TINGGI:
          </strong>
          Tindakan ini bersifat <span class="font-bold underline">PERMANEN</span> dan tidak dapat dibatalkan. Seluruh data kerja praktek, berkas persyaratan, riwayat tahapan SOP, dan surat penunjukkan mahasiswa bersangkutan akan dihapus total dari sistem.
        </div>

        <!-- Identitas Target Mahasiswa -->
        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-xs space-y-1">
          <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Mahasiswa:</span>
            <span class="font-bold text-slate-800" id="resetNamaMhs">-</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500 font-medium">NIM Target:</span>
            <span class="font-mono font-bold text-rose-600" id="resetNimTarget">-</span>
          </div>
        </div>

        <!-- Konfirmasi Teks Berlapis -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            Ketik NIM <span class="font-mono font-bold text-rose-600" id="labelTargetNim">-</span> untuk membuka konfirmasi:
          </label>
          <input type="text" id="inputKonfirmasiNim" autocomplete="off" placeholder="Masukkan NIM persis sesuai target..." 
                 class="w-full text-xs rounded-lg border border-slate-300 p-2.5 font-mono focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500 transition">
          <p class="text-[11px] text-slate-400 mt-1">Tombol eksekusi hanya akan aktif jika NIM yang Anda ketik cocok sempurna.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
          <button type="button" class="flex-1 py-2 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" data-modal-close>
            Batal
          </button>
          <button type="button" id="btnEksekusiHardReset" disabled 
                  class="flex-1 py-2 text-xs font-semibold rounded-lg bg-rose-600 text-white opacity-40 cursor-not-allowed transition flex items-center justify-center gap-1.5 shadow-sm">
            <i class="fas fa-trash-can"></i> <span id="textBtnReset">Hapus Permanen</span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ===== Modal Lembar Kerja ===== -->
  <div class="modal-overlay" id="modalLembarKerja" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
        <h6 class="font-semibold text-slate-900 text-[15px]">
            <i class="fas fa-clipboard-check mr-2 text-[#f97316]"></i>
            Lembar Kerja Operator - Kerja Praktek
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

  <!-- ===== Drawer Samping: Arsip & Riwayat Tahapan KP ===== -->
  <div class="drawer-overlay" id="drawerArsip" role="dialog" aria-modal="true">
    <div class="drawer-container">
      <!-- Header Drawer (Sticky di atas) -->
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
                <i class="fas fa-box-archive text-emerald-500"></i> Arsip &amp; Riwayat Tahapan Kerja Praktek
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
          <p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data arsip &amp; riwayat...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Script ===== -->
  <script>
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/kp/proses.php';

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
                'ambil-tabel-kp': 1,
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

    // Toggle Mode Edit Data di Dalam Drawer
    function toggleEditBox(btn, isEdit) {
        if (!btn) return;
        var el = btn.jquery ? btn[0] : btn;
        var box = el.closest ? (el.closest('.data-section-box') || el.closest('.acc-body')) : null;
        if (!box) {
            var $b = $(el).closest('.data-section-box');
            if (!$b.length) $b = $(el).closest('.acc-body');
            box = $b[0];
        }
        if (!box) return;

        var viewBox = box.querySelector('.view-box');
        var editBox = box.querySelector('.edit-box');
        var editBtn = box.querySelector('.btn-toggle-edit');

        if (isEdit) {
            if (viewBox) {
                viewBox.style.setProperty('display', 'none', 'important');
                viewBox.classList.add('hidden');
            }
            if (editBox) {
                editBox.style.setProperty('display', 'block', 'important');
                editBox.classList.remove('hidden');
            }
            if (editBtn) {
                editBtn.style.setProperty('display', 'none', 'important');
            }
        } else {
            if (editBox) {
                editBox.style.setProperty('display', 'none', 'important');
                editBox.classList.add('hidden');
            }
            if (viewBox) {
                viewBox.style.setProperty('display', 'block', 'important');
                viewBox.classList.remove('hidden');
            }
            if (editBtn) {
                editBtn.style.removeProperty('display');
                editBtn.style.display = '';
            }
        }
    }
    window.toggleEditBox = toggleEditBox;

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

      // ===== Drawer Arsip & Riwayat Handlers =====
      function bukaDrawerArsip(nim, stageToOpen, infoMhs) {
          var drawer = $('#drawerArsip');
          var konten = $('#drawerArsipKonten');
          drawer.addClass('show');

          if (infoMhs) {
              if (infoMhs.nama) $('#drawerHeaderNama').text(infoMhs.nama);
              $('#drawerHeaderNim').text(nim);
              if (infoMhs.prodi) $('#drawerHeaderProdi').text(infoMhs.prodi);
          } else {
              $('#drawerHeaderNama').text('Memuat Nama...');
              $('#drawerHeaderNim').text(nim);
              $('#drawerHeaderProdi').text('-');
          }
          $('#drawerHeaderStatus').addClass('hidden');

          if (!stageToOpen) {
              konten.html('<div class="acc-loading py-12 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data arsip &amp; riwayat tahapan...</p></div>');
          }

          $.ajax({
              url: PROSES_URL,
              method: 'POST',
              data: {
                  'ambil-arsip-riwayat': 1,
                  'nim': nim
              },
              dataType: 'json',
              success: function(respon) {
                  if(respon.status) {
                      if (respon.mhs) {
                          $('#drawerHeaderNama').text(respon.mhs.nama);
                          $('#drawerHeaderNim').text(respon.mhs.nim);
                          $('#drawerHeaderProdi').text(respon.mhs.prodi + (respon.mhs.angkatan ? ' (' + respon.mhs.angkatan + ')' : ''));
                          if (respon.mhs.status) {
                              $('#drawerHeaderStatus').text(respon.mhs.status)
                                                     .removeClass('hidden')
                                                     .attr('class', 'inline-block text-[10px] font-bold px-2.5 py-0.5 rounded-full ' + (respon.mhs.badge_class || 'bg-slate-100 text-slate-700'));
                          }
                      }
                      konten.html(respon.data);
                      if (stageToOpen) {
                          var targetCard = $('[data-card-stage="' + stageToOpen + '"].card-completed');
                          if (targetCard.length) {
                              targetCard.find('.acc-body').first().show().removeClass('hidden');
                              targetCard.find('.acc-chevron').first().addClass('rotate-180');
                              targetCard[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                          }
                      }
                  } else {
                      konten.html('<div class="text-center py-12 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="font-medium">' + respon.pesan + '</p></div>');
                  }
              },
              error: function(xhr, status, err) {
                  var detail = xhr.responseText ? xhr.responseText.substring(0, 300) : (err || 'Terjadi kesalahan pada server');
                  konten.html('<div class="text-center py-12 text-rose-500"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="font-medium">Gagal memuat arsip mahasiswa dari server:</p><p class="text-xs text-slate-500 mt-2 font-mono bg-slate-50 p-3 rounded border border-slate-200 max-w-md mx-auto text-left whitespace-pre-wrap">' + detail + '</p></div>');
              }
          });
      }

      // Tombol Arsip di Tabel
      $(document).on('click', '.btnArsip', function(){
          var nim = $(this).data('nim');
          var nama = $(this).data('nama') || '';
          var prodi = $(this).data('prodi') || '';
          bukaDrawerArsip(nim, null, { nama: nama, prodi: prodi });
      });

      // Tutup Drawer
      $(document).on('click', '[data-drawer-close]', function(e){
          e.stopPropagation();
          $('#drawerArsip').removeClass('show');
      });

      $('#drawerArsip').on('click', function(e){
          if (e.target === this) {
              $(this).removeClass('show');
          }
      });

      // Buka & Tutup Semua Accordion
      $(document).on('click', '#btnBukaSemuaAcc', function(){
          $('#accordionArsip .card-completed .acc-body').show().removeClass('hidden');
          $('#accordionArsip .card-completed .acc-chevron').addClass('rotate-180');
      });

      $(document).on('click', '#btnTutupSemuaAcc', function(){
          $('#accordionArsip .card-completed .acc-body').hide().addClass('hidden');
          $('#accordionArsip .card-completed .acc-chevron').removeClass('rotate-180');
      });

      // Toggle Accordion per Stage
      $(document).on('click', '.btn-toggle-accordion', function(e){
          if ($(e.target).closest('button, a, input, select, textarea').length) return;
          var card = $(this).closest('.card-completed');
          if (!card.length) return;
          var body = card.find('.acc-body');
          var chevron = $(this).find('.acc-chevron');
          if (body.is(':visible')) {
              body.hide().addClass('hidden');
              chevron.removeClass('rotate-180');
          } else {
              body.show().removeClass('hidden');
              chevron.addClass('rotate-180');
          }
      });

      // Koreksi & Batal
      $(document).on('click', '.btn-toggle-edit', function(e){
          e.preventDefault();
          e.stopPropagation();
          toggleEditBox(this, true);
      });

      $(document).on('click', '.btn-cancel-edit', function(e){
          e.preventDefault();
          e.stopPropagation();
          toggleEditBox(this, false);
      });

      // Submit Form Koreksi
      $(document).on('submit', '.form-koreksi-arsip', function(e){
          e.preventDefault();
          var form = this;
          var btn = $(form).find('button[type="submit"]');
          var nim = $(form).find('input[name="nim"]').val();
          var stageNum = $(form).closest('[data-card-stage]').data('card-stage');
          var oriText = btn.text();

          btn.prop('disabled', true).html('<span class="spinner-slate" style="border-top-color:#fff; width:0.75rem; height:0.75rem;"></span> Menyimpan...');

          var formData = new FormData(form);
          formData.append('koreksi-arsip-step', '1');

          $.ajax({
              url: PROSES_URL,
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function(res) {
                  toast(res.pesan, res.status ? 'success' : 'error');
                  if(res.status) {
                      bukaDrawerArsip(nim, stageNum);
                      muatUlangTabel();
                  }
              },
              error: function(xhr, status, err) {
                  var detail = xhr.responseText ? xhr.responseText.substring(0, 150) : err;
                  toast('Gagal menyimpan koreksi: ' + detail, 'error');
              },
              complete: function() {
                  btn.prop('disabled', false).text(oriText);
              }
          });
      });

      // Unggah Ulang / Ganti Berkas Mahasiswa atau Operator dari Drawer
      $(document).on('change', '.input-ganti-berkas', function(){
          var fileInput = this;
          if(!fileInput.files || fileInput.files.length === 0) return;

          var idSyarat = $(fileInput).data('id-syarat');
          var nim = $(fileInput).data('nim');
          var row = $(fileInput).closest('.item-berkas-row');
          var labelBtn = $(fileInput).closest('.btn-upload-ulang-label');
          var statusTag = row.find('.status-berkas-tag');
          var linkLihat = row.find('.link-lihat-file');

          var oriLabel = labelBtn.find('.label-txt').text();
          labelBtn.find('.label-txt').text('Mengunggah...');
          $(fileInput).prop('disabled', true);

          var formData = new FormData();
          formData.append('ganti-berkas-kp', '1');
          formData.append('nim', nim);
          formData.append('id_persyaratan', idSyarat);
          formData.append('file', fileInput.files[0]);

          $.ajax({
              url: PROSES_URL,
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function(res) {
                  toast(res.pesan, res.status ? 'success' : 'error');
                  if(res.status) {
                      statusTag.removeClass('bg-slate-100 bg-amber-100 bg-rose-100 text-slate-500 text-amber-700 text-rose-700')
                               .addClass('bg-emerald-100 text-emerald-700')
                               .text('Tersimpan');
                      if(res.url_file) {
                          linkLihat.attr('href', res.url_file).removeClass('hidden');
                      }
                      labelBtn.find('.label-txt').text('Unggah Ulang');
                  } else {
                      labelBtn.find('.label-txt').text(oriLabel);
                  }
              },
              error: function(xhr, status, err) {
                  var detail = xhr.responseText ? xhr.responseText.substring(0, 150) : err;
                  toast('Gagal mengunggah file: ' + detail, 'error');
                  labelBtn.find('.label-txt').text(oriLabel);
              },
              complete: function() {
                  $(fileInput).prop('disabled', false).val('');
              }
          });
      });

      // Lembar Kerja Button Click
      $(document).on('click', '.btnLembarKerja', function(){
          var nim = $(this).data('nim');
          var progres = $(this).data('progres');
          var statusKp = ($(this).data('status-kp') || '').toString().toLowerCase();
          var isAdmin = $(this).data('is-admin') == 1;

          if (statusKp === 'tidak aktif' || isAdmin) {
              $('#modalLembarKerja').addClass('show');
              $('#lembarKerjaKonten').html('<div class="acc-loading py-8 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat lembar kerja...</p></div>');
              
              $.ajax({
                  url: PROSES_URL,
                  method: 'POST',
                  data: {
                      'muat-lembar-kerja': 1,
                      'nim': nim,
                      'status_kp': statusKp,
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

      // Tutup modal
      $(document).on('click', '[data-modal-close]', function(e){
          e.stopPropagation();
          $(this).closest('.modal-overlay').removeClass('show');
      });

      <?php if ($userLevel === 9): ?>
      var targetNimReset = '';
      var targetNamaReset = '';

      // Buka Modal Hard Reset
      $(document).on('click', '.btnHapus', function(){
          targetNimReset = ($(this).data('nim') || '').toString().trim();
          targetNamaReset = ($(this).data('nama') || '').toString().trim();
          if (!targetNamaReset) {
              var tr = $(this).closest('tr');
              targetNamaReset = tr.find('.font-semibold').text() || 'Mahasiswa ini';
          }

          $('#resetNamaMhs').text(targetNamaReset);
          $('#resetNimTarget').text(targetNimReset);
          $('#labelTargetNim').text(targetNimReset);
          $('#inputKonfirmasiNim').val('').prop('disabled', false);
          $('#btnEksekusiHardReset').prop('disabled', true)
                                   .addClass('opacity-40 cursor-not-allowed')
                                   .removeClass('hover:bg-rose-700 shadow-rose-500/20')
                                   .html('<i class="fas fa-trash-can"></i> <span id="textBtnReset">Hapus Permanen</span>');
          $('#modalHardReset').addClass('show');
          setTimeout(function(){ $('#inputKonfirmasiNim').focus(); }, 150);
      });

      // Validasi ketik NIM
      $(document).on('input keyup paste', '#inputKonfirmasiNim', function(){
          var typed = $(this).val().trim();
          var btn = $('#btnEksekusiHardReset');
          if (typed === targetNimReset && targetNimReset !== '') {
              btn.prop('disabled', false)
                 .removeClass('opacity-40 cursor-not-allowed')
                 .addClass('hover:bg-rose-700 shadow-rose-500/20');
          } else {
              btn.prop('disabled', true)
                 .addClass('opacity-40 cursor-not-allowed')
                 .removeClass('hover:bg-rose-700 shadow-rose-500/20');
          }
      });

      // Eksekusi Hard Reset
      $(document).on('click', '#btnEksekusiHardReset', function(){
          var btn = $(this);
          var typed = $('#inputKonfirmasiNim').val().trim();
          if (typed !== targetNimReset || targetNimReset === '') {
              toast('NIM konfirmasi tidak sesuai!', 'error');
              return;
          }

          btn.prop('disabled', true).html('<i class="fas fa-spinner animate-spin mr-1"></i> Menghapus permanen...');
          $('#inputKonfirmasiNim').prop('disabled', true);

          $.ajax({
              url: PROSES_URL,
              method: 'POST',
              data: {
                  'hapus-data-kp': 1,
                  'nim': targetNimReset,
                  'konfirmasi_nim': typed
              },
              dataType: 'json',
              success: function(respon) {
                  if (respon.status) {
                      toast(respon.pesan, 'success');
                      $('#modalHardReset').removeClass('show');
                      muatUlangTabel();
                  } else {
                      toast(respon.pesan, 'error');
                      btn.prop('disabled', false).html('<i class="fas fa-trash-can"></i> Hapus Permanen');
                      $('#inputKonfirmasiNim').prop('disabled', false);
                  }
              },
              error: function(xhr, status, err) {
                  var detail = xhr.responseText ? xhr.responseText.substring(0, 150) : (err || 'Terjadi kesalahan pada server');
                  toast('Gagal melakukan reset: ' + detail, 'error');
                  btn.prop('disabled', false).html('<i class="fas fa-trash-can"></i> Hapus Permanen');
                  $('#inputKonfirmasiNim').prop('disabled', false);
              }
          });
      });
      <?php endif; ?>
    });
  </script>

</main>