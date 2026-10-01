<?php
/**
 * Halaman: Arsip Lain (Pangkalan Data)
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../fungsi.php';

// Ambil list user aktif untuk modal Bagikan Kepada
$listDosen = [];
$ambil_dosen = query("SELECT id, nama FROM dosen WHERE LOWER(status) = 'aktif' ORDER BY nama ASC");
if ($ambil_dosen) {
    while ($rowDosen = mysqli_fetch_assoc($ambil_dosen)) {
        $listDosen[] = $rowDosen;
    }
}

// Ambil daftar tahun unik dari database yang ada data arsipnya
$listTahunFilter = [];
$qTahunFilter = query("SELECT DISTINCT tahun FROM arsip_lain ORDER BY tahun DESC");
if ($qTahunFilter) {
    while ($rt = mysqli_fetch_assoc($qTahunFilter)) {
        $listTahunFilter[] = $rt['tahun'];
    }
}

// Ambil data arsip_lain sesuai hak akses user yang login
$userId = $_SESSION['id'] ?? '';
$sqlArsip = "SELECT a.*, f.nama_file as nf, f.lokasi, f.format_file, d.nama AS nama_pengupload 
             FROM arsip_lain a
             LEFT JOIN files f ON a.file = f.id
             LEFT JOIN dosen d ON a.pengupload = d.id
             WHERE a.jenis_akses = 'publik' 
                OR a.pengupload = '$userId'
                OR a.id IN (SELECT id_arsip FROM pemilik_file WHERE pemilik = '$userId')
             ORDER BY a.tahun DESC, a.id DESC";
$ambil_arsip = query($sqlArsip);
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* ===== Baris dokumen (Tanpa Accordion) ===== */
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
  #modalOverlay { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); transition: opacity 0.3s ease; }
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
          <i class="fas fa-archive"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Arsip Lain / Dokumen Bebas</h1>
          <p class="text-xs text-slate-500">Unggah dokumen bebas, foto, sertifikat pribadi (maks. 20MB).</p>
        </div>
      </div>
      <button type="button" onclick="bukaModalForm()" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
        <i class="fas fa-plus text-sm"></i>
        <span class="tip">Unggah Dokumen</span>
      </button>
    </div>
  </section>

  <!-- ===== Toolbar (Cari + Filter Tahun) ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="inputCari" onkeyup="filterList()" placeholder="Cari dokumen, pengunggah, atau hak akses..." 
               class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
      </div>
      <select id="filterTahun" onchange="filterList()" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Tahun</option>
        <?php foreach ($listTahunFilter as $thn): ?>
        <option value="<?= $thn ?>"><?= $thn ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterAkses" onchange="filterList()" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Akses</option>
        <option value="private">Private</option>
        <option value="terbatas">Terbatas</option>
        <option value="publik">Publik</option>
      </select>
    </div>
  </section>

  <!-- Ringkasan -->
  <section class="mb-3">
    <p class="text-xs text-slate-500">Menampilkan <b id="jmlDitemukan" class="text-slate-800">3</b> dokumen</p>
  </section>

  <!-- ===== Tabel Dokumen ===== -->
  <section class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100 sticky top-0 z-10 shadow-sm">
          <tr>
            <th class="w-12 py-3 pl-4">No</th>
            <th class="px-3 py-3">Nama Dokumen</th>
            <th class="px-3 py-3">Pengunggah</th>
            <th class="px-3 py-3">Akses</th>
            <th class="px-3 py-3">Tahun</th>
            <th class="w-28 py-3 pr-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="tbodyArsip" class="divide-y divide-slate-100">
          <?php if (mysqli_num_rows($ambil_arsip) > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = mysqli_fetch_assoc($ambil_arsip)): ?>
              <?php
              $idAkses = $row['jenis_akses'];
              $badgeAkses = '';
              if ($idAkses == 'publik') {
                  $badgeAkses = '<span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700"><i class="fas fa-globe mr-1"></i> Publik</span>';
              } else if ($idAkses == 'terbatas') {
                  $badgeAkses = '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"><i class="fas fa-lock mr-1"></i> Terbatas</span>';
              } else {
                  $badgeAkses = '<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-user-lock mr-1"></i> Private</span>';
              }
              
              $ekstensi = strtolower(pathinfo($row['nf'] ?? '', PATHINFO_EXTENSION));
              $file_ico = '<span class="file-ico file-other w-8 h-8 text-sm"><i class="fas fa-file"></i></span>';
              $format = 'LAINNYA';
              if (in_array($ekstensi, ['pdf'])) {
                  $file_ico = '<span class="file-ico file-pdf w-8 h-8 text-sm"><i class="fas fa-file-pdf"></i></span>';
                  $format = 'PDF';
              } else if (in_array($ekstensi, ['doc', 'docx'])) {
                  $file_ico = '<span class="file-ico file-word w-8 h-8 text-sm"><i class="fas fa-file-word"></i></span>';
                  $format = 'DOCX';
              } else if (in_array($ekstensi, ['xls', 'xlsx'])) {
                  $file_ico = '<span class="file-ico file-excel w-8 h-8 text-sm"><i class="fas fa-file-excel"></i></span>';
                  $format = 'XLSX';
              } else if (in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                  $file_ico = '<span class="file-ico file-img w-8 h-8 text-sm"><i class="fas fa-file-image"></i></span>';
                  $format = 'IMG';
              }
              
              $urlFile = '#';
              if ($row['file'] != '') {
                  $urlFile = url_file($row['file']);
              }
              
              $isOwner = ($row['pengupload'] == $userId);
              
              // Ambil list penyintas untuk edit
              $penyintasArr = [];
              if ($idAkses == 'terbatas') {
                  $qPen = query("SELECT p.pemilik, d.nama FROM pemilik_file p LEFT JOIN dosen d ON p.pemilik = d.id WHERE p.id_arsip = '" . $row['id'] . "'");
                  while ($rp = mysqli_fetch_assoc($qPen)) {
                      $penyintasArr[] = ['id' => $rp['pemilik'], 'nama' => $rp['nama']];
                  }
              }
              $dataPenyintas = htmlspecialchars(json_encode($penyintasArr), ENT_QUOTES, 'UTF-8');
              ?>
              <tr class="dok-row-tb hover:bg-slate-50/50 transition" data-tahun="<?= $row['tahun'] ?>" data-akses="<?= $row['jenis_akses'] ?>" data-cari="<?= strtolower(tampil($row['deskripsi']) . ' ' . tampil($row['nama_pengupload']) . ' ' . $row['jenis_akses']) ?>">
                <td class="py-3 pl-4 align-middle"><?= $no ?></td>
                <td class="px-3 py-3 align-middle max-w-[200px] md:max-w-xs">
                  <div class="flex items-center gap-2.5">
                    <?= $file_ico ?>
                    <span class="font-semibold text-slate-800 truncate" title="<?= tampil($row['deskripsi']) ?>"><?= tampil($row['deskripsi']) ?></span>
                  </div>
                </td>
                <td class="px-3 py-3 align-middle text-slate-600 max-w-[120px] truncate" title="<?= tampil($row['nama_pengupload']) ?>"><?= tampil($row['nama_pengupload']) ?></td>
                <td class="px-3 py-3 align-middle"><?= $badgeAkses ?></td>
                <td class="px-3 py-3 align-middle font-bold text-slate-700"><?= $row['tahun'] ?></td>
                <td class="py-3 pr-4 text-right align-middle">
                  <div class="flex justify-end gap-1.5">
                    <button onclick="detailData('<?= $row['id'] ?>')" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-info-circle text-xs"></i><span class="tip">Detail</span></button>
                    <?php if ($urlFile !== '#'): ?>
                    <button onclick="salinLink('<?= $urlFile ?>')" class="btn-circle bg-indigo-500 text-white shadow-sm hover:bg-indigo-600"><i class="fas fa-link text-xs"></i><span class="tip">Salin Link</span></button>
                    <a href="<?= $urlFile ?>" target="_blank" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-download text-xs"></i><span class="tip">Unduh</span></a>
                    <?php endif; ?>
                    <?php if ($isOwner): ?>
                    <button onclick="editData('<?= $row['id'] ?>')" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                    <button onclick="hapusData('<?= $row['id'] ?>')" class="btn-circle bg-red-500 text-white shadow-sm hover:bg-red-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Hidden data for edit/detail -->
                  <input type="hidden" id="data_deskripsi_<?= $row['id'] ?>" value="<?= tampil($row['deskripsi']) ?>">
                  <input type="hidden" id="data_tahun_<?= $row['id'] ?>" value="<?= $row['tahun'] ?>">
                  <input type="hidden" id="data_akses_<?= $row['id'] ?>" value="<?= $row['jenis_akses'] ?>">
                  <input type="hidden" id="data_pengupload_<?= $row['id'] ?>" value="<?= tampil($row['nama_pengupload']) ?>">
                  <input type="hidden" id="data_format_<?= $row['id'] ?>" value="<?= $format ?>">
                  <input type="hidden" id="data_url_<?= $row['id'] ?>" value="<?= $urlFile ?>">
                  <input type="hidden" id="data_penyintas_<?= $row['id'] ?>" value="<?= $dataPenyintas ?>">
                </td>
              </tr>
              <?php $no++; ?>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="py-6 text-center text-slate-400">
                <div class="inline-flex items-center gap-2">
                  <i class="fas fa-box-open"></i> Belum ada dokumen yang diunggah.
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <div class="dok-empty" id="emptyState"><i class="fas fa-box-open"></i> Tidak ada dokumen ditemukan.</div>
    
    <!-- Pagination Footer -->
    <div class="border-t border-slate-100 bg-slate-50/50 p-4 flex flex-col sm:flex-row items-center justify-between gap-3" id="paginationWrap" style="display:none;">
      <div class="text-xs text-slate-500">
        Menampilkan <b id="infoTampilMulai" class="text-slate-800">0</b> - <b id="infoTampilSampai" class="text-slate-800">0</b> dari <b id="infoTotalDitemukan" class="text-slate-800">0</b> dokumen
      </div>
      <div class="flex items-center gap-1" id="paginationControls">
        <!-- Pagination tombol akan di-generate via JS -->
      </div>
    </div>
  </section>

</main>

<!-- Modal Detail Dokumen -->
<div id="modalDetail" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div id="modalOverlayDetail" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="tutupModalDetail()"></div>
  <div class="modal-box relative w-full max-w-lg scale-95 transform rounded-2xl bg-white shadow-2xl opacity-0 overflow-hidden">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
          <i class="fas fa-file-alt text-lg"></i>
        </div>
        <div>
          <h3 class="text-sm font-bold text-slate-800">Detail Arsip</h3>
          <p class="text-[10px] font-medium text-slate-500">Informasi lengkap dokumen terunggah</p>
        </div>
      </div>
      <button onclick="tutupModalDetail()" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <!-- Modal Body -->
    <div class="p-6">
      <div class="mb-5 border-l-2 border-orange-500 pl-3">
        <h4 class="text-base font-bold text-slate-800" id="detJudul">-</h4>
        <p class="mt-1 text-xs font-medium text-slate-500">Tahun <span id="detTahun">-</span></p>
      </div>

      <div class="mb-5 rounded-xl border border-slate-100 bg-slate-50/50 p-4">
        <div class="grid grid-cols-2 gap-y-4 text-xs">
          <div>
            <span class="mb-1 block text-slate-400">Pengunggah</span>
            <div class="flex items-center gap-1.5 font-semibold text-slate-700">
              <i class="fas fa-user-circle text-slate-300"></i>
              <span id="detPengupload">-</span>
            </div>
          </div>
          <div>
            <span class="mb-1 block text-slate-400">Tipe / Format</span>
            <span class="font-bold text-slate-700" id="detFormat">-</span>
          </div>
          <div class="col-span-2">
            <span class="mb-1 block text-slate-400">Hak Akses</span>
            <div id="detAkses">-</div>
          </div>
        </div>
      </div>

      <div id="detWrapperPenyintas" style="display:none;">
        <h5 class="mb-2 text-xs font-bold text-slate-700">Dibagikan Kepada:</h5>
        <div class="rounded-xl border border-slate-100 bg-white p-3 shadow-sm">
          <div id="detListPenyintas" class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto content-scroll"></div>
        </div>
      </div>
    </div>
    
    <!-- Modal Footer -->
    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-6 py-4">
      <button type="button" onclick="tutupModalDetail()" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 transition">Tutup Dialog</button>
      <div class="flex items-center gap-2">
        <button type="button" onclick="salinLink(document.getElementById('detBtnUnduh').href)" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-600 transition">
          <i class="fas fa-link"></i> Salin Link
        </button>
        <a id="detBtnUnduh" href="#" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-600 transition">
          <i class="fas fa-download"></i> Unduh File
        </a>
      </div>
    </div>
  </div>
</div>
<div id="modalForm" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div id="modalOverlay" class="absolute inset-0" onclick="tutupModalForm()"></div>
  <div class="modal-box relative w-full max-w-lg scale-95 transform rounded-2xl bg-white shadow-2xl opacity-0">
    
    <!-- Modal Header -->
    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4 rounded-t-2xl">
      <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
          <i class="fas fa-file-upload text-sm"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800" id="modalTitle">Unggah Dokumen</h3>
      </div>
      <button onclick="tutupModalForm()" class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm hover:bg-slate-100 hover:text-slate-600 transition">
        <i class="fas fa-times text-xs"></i>
      </button>
    </div>
    
    <!-- Modal Body -->
    <form id="formArsip">
      <div class="px-6 py-5 max-h-[70vh] overflow-y-auto content-scroll">
        <input type="hidden" name="id" id="inputId">
        <input type="hidden" name="aksi" id="inputAksi" value="tambah">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Tahun</label>
            <select id="inputTahun" name="tahun" required class="input-field">
              <!-- Diisi via JS -->
            </select>
          </div>
          <div>
            <label class="form-label flex items-center justify-between">
              <span>Jenis Akses <span class="text-red-500">*</span></span>
              <span id="labelIconAkses" class="text-slate-400 font-normal"><i class="fas fa-user-lock"></i></span>
            </label>
            <select id="inputAkses" name="akses" required class="input-field">
              <option value="private" selected>Private (Hanya saya)</option>
              <option value="terbatas">Terbatas (Beberapa orang)</option>
              <option value="publik">Publik (Semua orang)</option>
            </select>
          </div>
        </div>
        
        <div class="mb-4 relative" id="groupPenyintas" style="display:none;">
          <label class="form-label">Bagikan Kepada <span class="text-red-500">*</span> <span class="text-slate-400 font-normal">(minimal 1 orang)</span></label>
          <div class="relative">
            <input type="text" id="inputSearchUser" class="input-field !pl-9" placeholder="Ketik nama dosen...">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          </div>
          
          <!-- Dropdown Hasil Pencarian (Diisi dari Database) -->
          <div id="dropdownUsers" class="absolute z-50 mt-1 hidden w-full rounded-xl border border-slate-200 bg-white shadow-xl max-h-48 overflow-y-auto">
            <ul class="text-xs text-slate-700 divide-y divide-slate-100">
              <?php foreach ($listDosen as $dosen): ?>
              <li class="px-4 py-2.5 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition" onclick="pilihUser('<?= addslashes($dosen['id']) ?>', '<?= addslashes($dosen['nama']) ?>')">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-[10px]"><?= strtoupper(substr($dosen['nama'], 0, 1)) ?></div>
                  <span class="font-medium"><?= addslashes($dosen['nama']) ?></span>
                </div>
                <i class="fas fa-plus text-slate-300"></i>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          
          <div id="containerUserTerpilih" class="mt-2.5 flex flex-wrap gap-2"></div>
          <div id="hiddenInputUsers"></div>
        </div>

        <div class="mb-4">
          <label class="form-label">Judul / Keterangan Dokumen <span class="text-red-500">*</span></label>
          <input type="text" id="inputNamaFile" name="nama_file" required class="input-field" placeholder="Contoh: CV Terbaru 2023, Transkrip Nilai, dll">
        </div>
        
        <div class="mb-2">
          <label class="form-label">File Dokumen <span class="text-red-500">*</span> <span class="text-slate-400 font-normal">(opsional untuk edit)</span></label>
          <div class="flex items-center gap-2 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3">
            <i class="fas fa-file-alt text-slate-400"></i>
            <span id="labelNamaFile" class="text-sm text-slate-500 truncate max-w-[200px] md:max-w-xs">Belum ada file dipilih</span>
            <label class="ml-auto inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700">
              <i class="fas fa-folder-open text-xs"></i> Pilih File
              <input type="file" id="inputLampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.txt" class="hidden">
            </label>
          </div>
          <p class="mt-1.5 text-[10px] text-slate-500">Format: PDF, JPG, DOCX, XLSX, PPTX (Maks. 20MB)</p>
        </div>
      </div>
      
      <!-- Modal Footer -->
      <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 bg-slate-50/80 px-6 py-4 rounded-b-2xl">
        <button type="button" onclick="tutupModalForm()" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 transition">Batal</button>
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-xs font-semibold text-white hover:bg-orange-600 shadow-sm transition-all" data-text="Simpan Dokumen">
          <i class="fas fa-save"></i> Simpan Dokumen
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  var PROSES_URL = '<?= base_url() ?>/administrator/pages/pangkalan-data/arsip/proses.php';

  // Toast Helper
  function toast(pesan, tipe = 'success') {
    const icon = tipe === 'success' ? 'fa-check-circle' : tipe === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    const bg   = tipe === 'success' ? 'toast-success' : tipe === 'error' ? 'toast-error' : 'toast-info';
    const html = `
      <div class="toast-item ${bg}">
        <i class="fas ${icon} toast-icon"></i>
        <span>${pesan}</span>
        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
      </div>
    `;
    const container = document.getElementById('toastContainer');
    container.insertAdjacentHTML('beforeend', html);
    const el = container.lastElementChild;
    setTimeout(() => el.classList.add('show'), 10);
    setTimeout(() => {
      el.classList.remove('show');
      el.classList.add('hide');
      setTimeout(() => el.remove(), 350);
    }, 4000);
  }

  // Button Loader
  function setButtonLoading(btn, isLoading) {
    if (isLoading) {
      btn.classList.add('is-loading');
      btn.setAttribute('disabled', '');
      btn.innerHTML = '<span class="spinner"></span> Menyimpan...';
    } else {
      btn.classList.remove('is-loading');
      btn.removeAttribute('disabled');
      btn.innerHTML = btn.getAttribute('data-text') || 'Simpan Data';
    }
  }

  // Modal Action
  // Script untuk Multi-select User dengan Badge
  const inputSearchUser = document.getElementById('inputSearchUser');
  const dropdownUsers   = document.getElementById('dropdownUsers');
  let selectedUsers     = [];

  // Tampilkan/Sembunyikan Dropdown saat input difokuskan
  inputSearchUser.addEventListener('focus', () => {
    dropdownUsers.classList.remove('hidden');
  });
  
  // Tutup dropdown kalau klik di luar
  document.addEventListener('click', (e) => {
    if (!inputSearchUser.contains(e.target) && !dropdownUsers.contains(e.target)) {
      dropdownUsers.classList.add('hidden');
    }
  });

  // Filter List (Dummy lokal)
  inputSearchUser.addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    const lis = dropdownUsers.querySelectorAll('li');
    lis.forEach(li => {
      const text = li.querySelector('span').innerText.toLowerCase();
      li.style.display = text.includes(term) ? 'flex' : 'none';
    });
  });

  function pilihUser(id, nama) {
    if(!selectedUsers.some(u => u.id == id)) {
      selectedUsers.push({ id, nama });
      renderUserBadges();
    }
    inputSearchUser.value = '';
    // reset filter
    dropdownUsers.querySelectorAll('li').forEach(li => li.style.display = 'flex');
    dropdownUsers.classList.add('hidden');
  }

  function hapusUser(id) {
    selectedUsers = selectedUsers.filter(u => u.id != id);
    renderUserBadges();
  }

  function renderUserBadges() {
    const container = document.getElementById('containerUserTerpilih');
    const hiddenWrap = document.getElementById('hiddenInputUsers');
    container.innerHTML = '';
    hiddenWrap.innerHTML = '';

    selectedUsers.forEach(u => {
      // Bikin UI Badge
      container.innerHTML += `
        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 pl-2.5 pr-1 py-1 text-[11px] font-semibold text-orange-700 border border-orange-200 shadow-sm">
          <i class="fas fa-user-circle text-orange-500 text-[10px]"></i>
          ${u.nama}
          <button type="button" class="flex h-4 w-4 items-center justify-center rounded-full bg-orange-200 text-orange-700 hover:bg-orange-300 transition" onclick="hapusUser('${u.id}')">
            <i class="fas fa-times text-[9px]"></i>
          </button>
        </span>
      `;
      // Bikin input hidden untuk ditarik form serialize
      hiddenWrap.innerHTML += `<input type="hidden" name="penyintas[]" value="${u.id}">`;
    });
  }

  // Setup Tahun Dinamis untuk Form Upload saja
  const selectTahun = document.getElementById('inputTahun');
  const currentYear = new Date().getFullYear();
  let tahunOptions = '';
  for (let i = currentYear; i >= currentYear - 6; i--) {
    tahunOptions += `<option value="${i}">${i}</option>`;
  }
  selectTahun.innerHTML = tahunOptions;
  
  // Update icon label akses berdasarkan pilihan
  document.getElementById('inputAkses').addEventListener('change', function() {
    const val = this.value;
    const labelIcon = document.getElementById('labelIconAkses');
    
    if (val === 'terbatas') {
      document.getElementById('groupPenyintas').style.display = 'block';
      labelIcon.innerHTML = '<i class="fas fa-lock text-amber-500"></i>';
      labelIcon.title = 'Terbatas';
    } else {
      document.getElementById('groupPenyintas').style.display = 'none';
      selectedUsers = [];
      renderUserBadges();
      
      if (val === 'publik') {
        labelIcon.innerHTML = '<i class="fas fa-globe text-emerald-500"></i>';
        labelIcon.title = 'Publik';
      } else {
        labelIcon.innerHTML = '<i class="fas fa-user-lock text-slate-400"></i>';
        labelIcon.title = 'Private';
      }
    }
  });
  const modalForm  = document.getElementById('modalForm');
  const modalInner = modalForm.querySelector('.modal-box');
  
  function bukaModalForm() {
    document.getElementById('formArsip').reset();
    document.getElementById('inputId').value = '';
    document.getElementById('inputAksi').value = 'tambah';
    document.getElementById('modalTitle').textContent = 'Unggah Dokumen';
    document.getElementById('inputAkses').value = 'private';
    document.getElementById('labelIconAkses').innerHTML = '<i class="fas fa-user-lock text-slate-400"></i>';
    document.getElementById('labelIconAkses').title = 'Private';
    
    document.getElementById('labelNamaFile').textContent = 'Belum ada file dipilih';
    document.getElementById('labelNamaFile').className = 'text-sm text-slate-500 truncate max-w-[200px] md:max-w-xs';
    
    document.getElementById('groupPenyintas').style.display = 'none';
    selectedUsers = [];
    renderUserBadges();
    
    modalForm.classList.remove('hidden');
    modalForm.classList.add('flex');
    setTimeout(() => {
      modalForm.classList.remove('opacity-0');
      modalInner.classList.remove('opacity-0', 'scale-95');
      modalInner.classList.add('opacity-100', 'scale-100');
    }, 10);
  }
  
  function tutupModalForm() {
    modalForm.classList.add('opacity-0');
    modalInner.classList.remove('opacity-100', 'scale-100');
    modalInner.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
      modalForm.classList.remove('flex');
      modalForm.classList.add('hidden');
    }, 250);
  }

  // Ambil data tabel via load jQuery
  function loadArsip() {
    $('#tbodyArsip').html('<tr><td colspan="7" class="py-6 text-center text-slate-400"><div class="inline-flex items-center gap-2"><span class="spinner-slate"></span> Memuat data...</div></td></tr>');
    // Memuat hanya bagian tbody saja tanpa me-reload seluruh halaman
    $('#tbodyArsip').load(window.location.href + ' #tbodyArsip > *', function() {
      // Reload daftar filter tahun jika ada tahun baru dari data terbaru
      $('#filterTahun').load(window.location.href + ' #filterTahun > *', function() {
        currentPage = 1;
        filterList();
      });
    });
  }

  function editData(id) {
    const tahun = document.getElementById('data_tahun_' + id).value;
    const deskripsi = document.getElementById('data_deskripsi_' + id).value;
    const akses = document.getElementById('data_akses_' + id).value;
    const penyintas = JSON.parse(document.getElementById('data_penyintas_' + id).value);
    
    bukaModalForm();
    document.getElementById('modalTitle').textContent = 'Edit Arsip Lain';
    document.getElementById('inputAksi').value = 'edit';
    document.getElementById('inputId').value = id;
    document.getElementById('inputTahun').value = tahun;
    document.getElementById('inputNamaFile').value = deskripsi;
    document.getElementById('inputAkses').value = akses;
    document.getElementById('labelIconAkses').innerHTML = aksesIcon(akses);
    document.getElementById('labelIconAkses').title = aksesLabel(akses);
    
    if (akses === 'terbatas') {
      document.getElementById('groupPenyintas').style.display = 'block';
      selectedUsers = penyintas;
      renderUserBadges();
    } else {
      document.getElementById('groupPenyintas').style.display = 'none';
      selectedUsers = [];
      renderUserBadges();
    }
  }
  
  // Modal Detail
  const modalDetail = document.getElementById('modalDetail');
  const modalDetailInner = modalDetail.querySelector('.modal-box');
  const modalDetailOverlay = document.getElementById('modalOverlayDetail');
  
  function detailData(id) {
    document.getElementById('detJudul').textContent = document.getElementById('data_deskripsi_' + id).value;
    document.getElementById('detPengupload').textContent = document.getElementById('data_pengupload_' + id).value;
    document.getElementById('detTahun').textContent = document.getElementById('data_tahun_' + id).value;
    document.getElementById('detFormat').textContent = document.getElementById('data_format_' + id).value;
    document.getElementById('detBtnUnduh').href = document.getElementById('data_url_' + id).value;
    
    const akses = document.getElementById('data_akses_' + id).value;
    const aksesEl = document.getElementById('detAkses');
    let badge = '';
    let showPenyintas = false;
    
    if (akses === 'publik') {
      badge = '<span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700"><i class="fas fa-globe mr-1"></i> Publik</span>';
    } else if (akses === 'terbatas') {
      badge = '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"><i class="fas fa-lock mr-1"></i> Terbatas</span>';
      showPenyintas = true;
    } else {
      badge = '<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600"><i class="fas fa-user-lock mr-1"></i> Private</span>';
    }
    aksesEl.innerHTML = badge;
    document.getElementById('detWrapperPenyintas').style.display = showPenyintas ? 'block' : 'none';
    
    if (showPenyintas) {
      const penyintas = JSON.parse(document.getElementById('data_penyintas_' + id).value);
      const container = document.getElementById('detListPenyintas');
      container.innerHTML = '';
      penyintas.forEach(u => {
        container.innerHTML += `<span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-medium text-orange-700 border border-orange-200">
          <i class="fas fa-user text-[9px]"></i> ${u.nama}
        </span>`;
      });
    }
    
    modalDetail.classList.remove('hidden');
    modalDetail.classList.add('flex');
    setTimeout(() => {
      modalDetail.classList.remove('opacity-0');
      modalDetailInner.classList.remove('opacity-0', 'scale-95');
      modalDetailInner.classList.add('opacity-100', 'scale-100');
    }, 10);
  }
  
  function tutupModalDetail() {
    modalDetail.classList.add('opacity-0');
    modalDetailInner.classList.remove('opacity-100', 'scale-100');
    modalDetailInner.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
      modalDetail.classList.remove('flex');
      modalDetail.classList.add('hidden');
    }, 250);
  }

  function aksesIcon(val) {
    if (val === 'terbatas') return '<i class="fas fa-lock text-amber-500"></i>';
    if (val === 'publik') return '<i class="fas fa-globe text-emerald-500"></i>';
    return '<i class="fas fa-user-lock text-slate-400"></i>';
  }
  
  function aksesLabel(val) {
    if (val === 'terbatas') return 'Terbatas';
    if (val === 'publik') return 'Publik';
    return 'Private';
  }

  function salinLink(url) {
    if (!url || url === '#') {
      toast('Tautan tidak valid.', 'error');
      return;
    }
    
    // Convert relative/absolute to full qualified URL
    const fullUrl = new URL(url, window.location.href).href;
    
    navigator.clipboard.writeText(fullUrl).then(() => {
      toast('Tautan berhasil disalin ke clipboard!', 'success');
    }).catch(err => {
      // fallback copy
      const temp = document.createElement("input");
      document.body.appendChild(temp);
      temp.value = fullUrl;
      temp.select();
      document.execCommand("copy");
      document.body.removeChild(temp);
      toast('Tautan berhasil disalin ke clipboard!', 'success');
    });
  }

  function hapusData(id) {
    const row = document.querySelector('input[id="data_deskripsi_'+id+'"]').closest('tr');
    const namaDokumen = document.getElementById('data_deskripsi_' + id).value;
    
    if (typeof konfirmasiHapus === 'function') {
      konfirmasiHapus(namaDokumen, function() {
        const btn = row.querySelector('.bg-red-500');
        if (btn) btn.classList.add('is-loading');
        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('hapus-data-arsip', '1');
        
        fetch(PROSES_URL, {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status) {
            toast(data.pesan, 'success');
            loadArsip();
          } else {
            if (btn) btn.classList.remove('is-loading');
            toast(data.pesan, 'error');
          }
        })
        .catch(err => {
          if (btn) btn.classList.remove('is-loading');
          toast('Terjadi kesalahan', 'error');
        });
      });
    }
  }

  document.getElementById('inputLampiran').addEventListener('change', function() {
    const label = document.getElementById('labelNamaFile');
    if (this.files && this.files.length > 0) {
      label.textContent = this.files[0].name;
      label.classList.remove('text-slate-500');
      label.classList.add('text-slate-800', 'font-medium');
    } else {
      label.textContent = 'Belum ada file dipilih';
      label.classList.add('text-slate-500');
      label.classList.remove('text-slate-800', 'font-medium');
    }
  });

  document.getElementById('formArsip').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi Minimal 1 Orang Jika Terbatas
    const akses = document.getElementById('inputAkses').value;
    if (akses === 'terbatas' && selectedUsers.length === 0) {
      toast('Pilih minimal 1 dosen yang diizinkan untuk akses Terbatas!', 'error');
      return;
    }

    const btn = this.querySelector('button[type="submit"]');
    setButtonLoading(btn, true);
    
    const formData = new FormData(this);
    formData.append('simpan-data-arsip', '1');
    
    fetch(PROSES_URL, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      setButtonLoading(btn, false);
      if (data.status) {
        tutupModalForm();
        toast(data.pesan, 'success');
        loadArsip();
      } else {
        toast(data.pesan, 'error');
      }
    })
    .catch(err => {
      setButtonLoading(btn, false);
      toast('Terjadi kesalahan koneksi', 'error');
    });
  });

  // Pagination & Filter List State
  let currentPage = 1;
  const itemsPerPage = 25;
  let matchingRows = [];

  function filterList() {
    const cari  = document.getElementById('inputCari').value.toLowerCase();
    const thn   = document.getElementById('filterTahun').value.toLowerCase();
    const akses = document.getElementById('filterAkses').value.toLowerCase();
    
    const rows = document.querySelectorAll('#tbodyArsip tr.dok-row-tb');
    matchingRows = [];
    
    rows.forEach(row => {
      const dataCari  = row.getAttribute('data-cari') || '';
      const dataThn   = row.getAttribute('data-tahun') || '';
      const dataAkses = row.getAttribute('data-akses') || '';
      
      const matchCari  = dataCari.includes(cari);
      const matchThn   = thn === '' || dataThn === thn;
      const matchAkses = akses === '' || dataAkses === akses;
      
      if (matchCari && matchThn && matchAkses) {
        matchingRows.push(row);
      } else {
        row.style.display = 'none';
      }
    });

    const total = matchingRows.length;
    document.getElementById('jmlDitemukan').textContent = total;
    document.getElementById('emptyState').style.display = total === 0 && rows.length > 0 ? 'block' : 'none';
    
    // Reset page jika melampaui
    const totalPages = Math.ceil(total / itemsPerPage) || 1;
    if (currentPage > totalPages) currentPage = 1;
    
    renderPagination();
  }

  function renderPagination() {
    const total = matchingRows.length;
    const totalPages = Math.ceil(total / itemsPerPage) || 1;
    const paginationWrap = document.getElementById('paginationWrap');
    
    if (total === 0) {
      paginationWrap.style.display = 'none';
      return;
    }
    
    paginationWrap.style.display = 'flex';
    
    // Tampilkan hanya item pada halaman aktif
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    
    matchingRows.forEach((row, idx) => {
      if (idx >= start && idx < end) {
        row.style.display = '';
        // update nomor urut
        row.querySelector('td:first-child').textContent = idx + 1;
      } else {
        row.style.display = 'none';
      }
    });
    
    document.getElementById('infoTampilMulai').textContent = total > 0 ? start + 1 : 0;
    document.getElementById('infoTampilSampai').textContent = end;
    document.getElementById('infoTotalDitemukan').textContent = total;
    
    // Bikin tombol pagination
    const controls = document.getElementById('paginationControls');
    controls.innerHTML = '';
    
    // Tombol Prev
    const btnPrev = document.createElement('button');
    btnPrev.className = 'pg-btn';
    btnPrev.innerHTML = '<i class="fas fa-chevron-left text-[10px]"></i>';
    btnPrev.disabled = currentPage === 1;
    btnPrev.onclick = () => { if (currentPage > 1) { currentPage--; renderPagination(); } };
    controls.appendChild(btnPrev);
    
    // Tombol Halaman (Maksimal 5 tombol angka di sekitar halaman aktif)
    for (let p = 1; p <= totalPages; p++) {
      if (p === 1 || p === totalPages || (p >= currentPage - 2 && p <= currentPage + 2)) {
        const btnPage = document.createElement('button');
        btnPage.className = 'pg-btn ' + (p === currentPage ? 'on' : '');
        btnPage.textContent = p;
        btnPage.onclick = () => { currentPage = p; renderPagination(); };
        controls.appendChild(btnPage);
      } else if (p === currentPage - 3 || p === currentPage + 3) {
        const dot = document.createElement('span');
        dot.className = 'px-1 text-slate-400 text-xs';
        dot.textContent = '...';
        controls.appendChild(dot);
      }
    }
    
    // Tombol Next
    const btnNext = document.createElement('button');
    btnNext.className = 'pg-btn';
    btnNext.innerHTML = '<i class="fas fa-chevron-right text-[10px]"></i>';
    btnNext.disabled = currentPage === totalPages;
    btnNext.onclick = () => { if (currentPage < totalPages) { currentPage++; renderPagination(); } };
    controls.appendChild(btnNext);
  }
</script>