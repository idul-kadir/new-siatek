<?php
/**
 * Halaman: SKP (Pangkalan Data)
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../fungsi.php';

// Ambil list user aktif
$listDosen = [];
$ambil_dosen = query("SELECT id, nama FROM dosen WHERE LOWER(status) = 'aktif' ORDER BY nama ASC");
if ($ambil_dosen) {
    while ($rowDosen = mysqli_fetch_assoc($ambil_dosen)) {
        $listDosen[] = $rowDosen;
    }
}

// Ambil daftar tahun unik dari database
$listTahunFilter = [];
$qTahunFilter = query("SELECT DISTINCT tahun FROM skp ORDER BY tahun DESC");
if ($qTahunFilter) {
    while ($rt = mysqli_fetch_assoc($qTahunFilter)) {
        $listTahunFilter[] = $rt['tahun'];
    }
}

// Ambil data skp sesuai hak akses user yang login
$userId = $_SESSION['id'] ?? '';
$sqlSkp = "SELECT s.*, p.nama_file as pnf, p.lokasi as plokasi, p.format_file as pff, 
             h.nama_file as hnf, h.lokasi as hlokasi, h.format_file as hff,
             d.nama AS nama_dosen 
             FROM skp s
             LEFT JOIN files p ON s.penilaian = p.id
             LEFT JOIN files h ON s.hasil = h.id
             LEFT JOIN dosen d ON s.dosen = d.id
             WHERE s.dosen = '$userId'
             ORDER BY s.tahun DESC, s.id DESC";
$ambil_skp = query($sqlSkp);
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Baris dokumen ===== */
    .dok-row { display: flex; align-items: center; gap: .8rem; padding: .75rem .9rem; border-radius: .85rem; background: #fff; border: 1px solid #e2e8f0; transition: border-color .15s ease, box-shadow .15s ease; margin-bottom: 0.5rem; }
    .dok-row:hover { border-color: #cbd5e1; box-shadow: 0 3px 10px -4px rgba(15,23,42,.1); }

    .file-ico { display: inline-flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border-radius: .75rem; font-size: 1.15rem; flex-shrink: 0; }
    .file-pdf   { background: #fee2e2; color: #dc2626; }
    .file-word  { background: #dbeafe; color: #2563eb; }
    .file-excel { background: #d1fae5; color: #059669; }
    .file-img   { background: #fef3c7; color: #d97706; }
    .file-other { background: #f1f5f9; color: #64748b; }

    /* Pagination */
    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; padding: 0 .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
    .pg-btn.on { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }

  .dok-row-tb:hover { background: #f8fafc; }
  .dok-empty { padding: 1.1rem; text-align: center; border-top: 1px dashed #e2e8f0; background: #fff; color: #64748b; font-size: .8rem; display: none; }
  .dok-empty i { margin-right: .4rem; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

  /* Toast Notification */
  #toastContainer {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column-reverse;
    gap: 0.5rem;
    pointer-events: none;
  }
  .toast-item {
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border-radius: 0.625rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(100%);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 300px;
  }
  .toast-item.show {
    transform: translateY(0);
    opacity: 1;
  }
  .toast-item.hide {
    transform: translateY(100%);
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

  /* Modal Backdrop & Animation */
  #modalOverlay { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); transition: opacity 0.3s ease; display: none; position: fixed; inset: 0; z-index: 90; align-items: center; justify-content: center; padding: 1rem; }
  #modalOverlay.show { display: flex; opacity: 1; }
  .modal-box { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease; }
  
  /* Input Custom Styles */
  .input-field {
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background-color: #f8fafc;
    padding: 0.625rem 1rem;
    font-size: 0.8125rem;
    color: #334155;
    transition: all 0.2s;
  }
  .input-field:focus {
    border-color: #f97316;
    background-color: #fff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
  }
  .form-label {
    display: block;
    margin-bottom: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
  }

  /* Spinner */
  .btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
  .btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
  .spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
</style>

<main class="content-area content-scroll">
  <div id="toastContainer"></div>

  <!-- ===== Page Header ===== -->
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
          <i class="fas fa-file-contract"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">SKP</h1>
          <p class="text-xs text-slate-500">Sasaran Kinerja Pegawai: target, realisasi, penilaian.</p>
        </div>
      </div>
      <button type="button" onclick="bukaModalForm()" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
        <i class="fas fa-plus text-sm"></i>
        <span class="tip">Unggah SKP</span>
      </button>
    </div>
  </section>

  <!-- ===== Toolbar (Cari + Filter Tahun) ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      <select id="filterTahun" onchange="muatUlangTabel()" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Tahun</option>
        <?php foreach ($listTahunFilter as $thn): ?>
        <option value="<?= $thn ?>"><?= $thn ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </section>

  <!-- ===== List Data ===== -->
  <section>
    <div id="tabelWrap">
      <!-- Data dimuat lewat AJAX -->
    </div>
  </section>
</main>

<!-- ===== Modal Form ===== -->
<div id="modalOverlay">
  <div class="modal-box w-full max-w-md scale-95 transform rounded-2xl bg-white p-5 opacity-0 shadow-2xl">
    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 class="text-base font-bold text-slate-800" id="modalTitle">Unggah SKP</h3>
      <button type="button" onclick="tutupModalForm()" class="text-slate-400 transition hover:text-red-500">
        <i class="fas fa-times text-lg"></i>
      </button>
    </div>

    <form id="formSkp">
      <input type="hidden" name="id" id="formId">

      <div class="mb-3">
        <label class="form-label">Tahun <span class="text-red-500">*</span></label>
        <select name="tahun" id="formTahun" class="input-field" required>
          <?php $thn = (int)date('Y'); ?>
          <?php foreach (range($thn, $thn - 5) as $y): ?>
            <option value="<?= $y ?>" <?= $y === $thn ? 'selected' : '' ?>><?= $y ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="form-label">File Penilaian (Draft) <span class="text-red-500" id="reqPenilaian">*</span></label>
        <div class="relative">
          <input type="file" name="penilaian" id="formPenilaian" class="input-field" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
        </div>
        <p class="mt-1 text-[10px] text-slate-400" id="hintPenilaian">Wajib diisi saat menambah data baru. Maks 8MB.</p>
      </div>
      


      <div class="mt-5 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
        <button type="button" onclick="tutupModalForm()" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100">Batal</button>
        <button type="submit" id="btnSimpan" class="btn-loader rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600" data-text="Simpan">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  var PROSES_URL = '<?= base_url() ?>/administrator/pages/pangkalan-data/skp/proses.php';

  function toast(pesan, tipe = 'success') {
    var tc = document.getElementById('toastContainer');
    var t = document.createElement('div');
    t.className = 'toast-item toast-' + tipe;
    
    var icon = 'fa-check-circle';
    if(tipe === 'error') icon = 'fa-exclamation-circle';
    else if(tipe === 'info') icon = 'fa-info-circle';

    t.innerHTML = `
      <i class="fas ${icon} toast-icon"></i>
      <span>${pesan}</span>
      <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;
    tc.appendChild(t);
    
    setTimeout(function(){
      t.classList.add('show');
    }, 10);

    setTimeout(function(){
      t.classList.remove('show');
      t.classList.add('hide');
      setTimeout(function(){ t.remove(); }, 300);
    }, 3000);
  }

  function setButtonLoading(btn, isLoading) {
    if (isLoading) {
      btn.classList.add('is-loading');
      btn.setAttribute('disabled', '');
      btn.innerHTML = '<span class="spinner-slate"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
    } else {
      btn.classList.remove('is-loading');
      btn.removeAttribute('disabled');
      btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
    }
  }

  function bukaModalForm() {
    document.getElementById('formSkp').reset();
    document.getElementById('formId').value = '';
    document.getElementById('modalTitle').textContent = 'Unggah SKP';
    document.getElementById('formPenilaian').setAttribute('required', '');
    document.getElementById('reqPenilaian').style.display = 'inline';
    document.getElementById('hintPenilaian').textContent = 'Wajib diisi saat menambah data baru. Maks 8MB.';
    
    var ov = document.getElementById('modalOverlay');
    var bx = ov.querySelector('.modal-box');
    ov.classList.add('show');
    setTimeout(function(){
      bx.classList.remove('scale-95', 'opacity-0');
      bx.classList.add('scale-100', 'opacity-100');
    }, 10);
  }

  function editSkp(id, tahun) {
    bukaModalForm();
    document.getElementById('modalTitle').textContent = 'Edit SKP';
    document.getElementById('formId').value = id;
    document.getElementById('formTahun').value = tahun;
    document.getElementById('formPenilaian').removeAttribute('required');
    document.getElementById('reqPenilaian').style.display = 'none';
    document.getElementById('hintPenilaian').textContent = 'Biarkan kosong jika tidak ingin mengubah file draft saat edit. Maks 8MB.';
  }

  function tutupModalForm() {
    var ov = document.getElementById('modalOverlay');
    var bx = ov.querySelector('.modal-box');
    bx.classList.remove('scale-100', 'opacity-100');
    bx.classList.add('scale-95', 'opacity-0');
    setTimeout(function(){
      ov.classList.remove('show');
    }, 300);
  }

  function muatUlangTabel() {
    var container = document.getElementById('tabelWrap');
    container.innerHTML = '<div class="acc-loading py-5 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-sm text-slate-500">Memuat data...</p></div>';

    var fd = new FormData();
    fd.append('ambil-tabel-skp', '1');
    fd.append('tahun', document.getElementById('filterTahun').value);

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(respon) {
        if (respon.status) {
          container.innerHTML = respon.data;
        } else {
          container.innerHTML = '<div class="dok-empty" style="display:block;"><i class="fas fa-folder-open"></i>Gagal memuat data</div>';
        }
      },
      error: function() {
        container.innerHTML = '<div class="dok-empty" style="display:block;"><i class="fas fa-exclamation-triangle"></i>Terjadi kesalahan pada server</div>';
      }
    });
  }

  $('#formSkp').on('submit', function(e) {
    e.preventDefault();
    var btnSimpan = this.querySelector('button[type="submit"]');
    setButtonLoading(btnSimpan, true);

    var data = new FormData(this);
    var isEdit = document.getElementById('formId').value !== '';
    if(isEdit) {
      data.append('edit-data-skp', '1');
    } else {
      data.append('tambah-data-skp', '1');
    }

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: data,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(respon) {
        if (respon && typeof respon === 'object') {
          toast(respon.pesan, respon.status ? 'success' : 'error');
          if (respon.status) {
            tutupModalForm();
            muatUlangTabel();
          }
        } else {
          toast('Respon server bukan JSON valid', 'error');
        }
      },
      error: function(xhr) {
        var msg = 'Terjadi kesalahan pada server (HTTP ' + xhr.status + ')';
        if (xhr.responseText) {
          try {
            var res = JSON.parse(xhr.responseText);
            if (res && res.pesan) {
              msg = res.pesan;
            }
          } catch(e) {
            msg = xhr.responseText.replace(/<[^>]*>?/gm, '').trim().substring(0, 100);
          }
        }
        toast(msg, 'error');
      },
      complete: function() {
        setButtonLoading(btnSimpan, false);
      }
    });
  });

  function hapusSkp(id, tahun) {
    if(typeof konfirmasiHapus !== 'undefined') {
      konfirmasiHapus('Data SKP Tahun ' + tahun, function() {
        prosesHapus(id);
      });
    } else {
      if(confirm('Yakin hapus data SKP Tahun ' + tahun + '?')) {
        prosesHapus(id);
      }
    }
  }

  function prosesHapus(id) {
    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: { 'hapus-data-skp': 1, 'id': id },
      dataType: 'json',
      success: function(respon) {
        toast(respon.pesan, respon.status ? 'success' : 'error');
        if(respon.status) muatUlangTabel();
      },
      error: function() {
        toast('Terjadi kesalahan pada server', 'error');
      }
    });
  }

  // Load awal
  $(document).ready(function() {
    muatUlangTabel();
  });
</script>