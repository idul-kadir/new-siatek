<?php
require __DIR__ . "/../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

// ambil statistik
$totalQ = mysqli_fetch_assoc(query("SELECT COUNT(*) AS jml FROM arsip_jurusan"))['jml'] ?? 0;
$tahunMinQ = mysqli_fetch_assoc(query("SELECT MIN(tahun) AS thn FROM arsip_jurusan"))['thn'] ?? '-';
$tahunMaxQ = mysqli_fetch_assoc(query("SELECT MAX(tahun) AS thn FROM arsip_jurusan"))['thn'] ?? '-';
$penguploadTerbanyak = mysqli_fetch_assoc(query("SELECT a.pengupload, d.nama AS nama_user, COUNT(*) AS jml FROM arsip_jurusan a LEFT JOIN dosen d ON a.pengupload = d.id GROUP BY a.pengupload, d.nama ORDER BY jml DESC LIMIT 1"));
$namaUploadQ = $penguploadTerbanyak['nama_user'] ?? $penguploadTerbanyak['pengupload'] ?? '-';
$jmlUploadQ = $penguploadTerbanyak['jml'] ?? 0;

// ambil data arsip
$ambil = query("SELECT a.*, d.nama AS nama_user FROM arsip_jurusan a LEFT JOIN dosen d ON a.pengupload = d.id ORDER BY a.tahun DESC, a.id DESC");
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

  .tile-orange {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
  }

  .tile-sky {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
  }

  .tile-emerald {
    background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  }

  .tile-violet {
    background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%);
  }

  .tile-corak {
    position: relative;
    overflow: hidden;
  }

  .tile-corak::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: radial-gradient(rgba(255, 255, 255, .22) 1px, transparent 1px);
    background-size: 12px 12px;
    opacity: .35;
    mix-blend-mode: overlay;
  }

  .tile-corak>* {
    position: relative;
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

  .pg-btn:hover {
    border-color: #fdba74;
    color: #c2410c;
  }

  .pg-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
  }

  .pg-btn.on {
    background: #1e3a5f;
    border-color: #1e3a5f;
    color: #fff;
  }

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
          <i class="fas fa-archive"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Arsip Jurusan</h1>
          <p class="text-xs text-slate-500">Dokumen arsip tingkat jurusan</p>
        </div>
      </div>
      <button type="button" id="btnTambah"
        class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
        <i class="fas fa-plus text-sm"></i>
        <span class="tip">Tambah Dokumen</span>
      </button>
    </div>
  </section>

  <!-- ===== Statistik ===== -->
  <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="tile-orange tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Dokumen</p>
          <p class="mt-2 text-2xl font-bold tracking-tight" id="statTotal"><?= $totalQ ?></p>
          <p class="mt-2 text-[11px] text-white/70">seluruh arsip tersimpan</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-box-archive"></i></span>
      </div>
    </div>
    <div class="tile-sky tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-sky-500/25" style="animation-delay:.05s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terlama</p>
          <p class="mt-2 text-2xl font-bold tracking-tight" id="statTahunMin"><?= $tahunMinQ ?></p>
          <p class="mt-2 text-[11px] text-white/70">arsip tertua</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hourglass-start"></i></span>
      </div>
    </div>
    <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-emerald-500/25" style="animation-delay:.10s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terbaru</p>
          <p class="mt-2 text-2xl font-bold tracking-tight" id="statTahunMax"><?= $tahunMaxQ ?></p>
          <p class="mt-2 text-[11px] text-white/70">arsip paling baru</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-layer-group"></i></span>
      </div>
    </div>
    <div class="tile-violet tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-violet-500/25" style="animation-delay:.15s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengunggah Terbanyak</p>
          <p class="mt-2 text-xl font-bold tracking-tight truncate" id="statPengupload"><?= tampil($namaUploadQ) ?></p>
          <p class="mt-2 text-[11px] text-white/70" id="statPenguploadJml"><?= $jmlUploadQ ?> dokumen</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-user"></i></span>
      </div>
    </div>
  </section>

  <!-- ===== Toolbar (cari + filter tahun, live via JS) ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama dokumen, pengupload..."
          class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
      </div>
      <select id="fTahun"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Tahun</option>
        <?php
        $tahunUnik = query("SELECT DISTINCT tahun FROM arsip_jurusan ORDER BY tahun DESC");
        while($th = mysqli_fetch_assoc($tahunUnik)){
        ?>
        <option value="<?= $th['tahun'] ?>"><?= $th['tahun'] ?></option>
        <?php } ?>
      </select>
      <button type="button" id="btnReset"
        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
        <i class="fas fa-times mr-1"></i>Reset
      </button>
    </div>
  </section>

  <!-- ===== Daftar Arsip ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Dokumen</h2>
          <p class="mt-0.5 text-xs text-slate-500"><span id="jmlArsip"><?= $totalQ ?></span> arsip ditemukan</p>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
          <i class="fas fa-file-archive"></i> <span id="badgeArsip"><?= $totalQ ?></span> file
        </span>
      </div>
      <div id="noHasil" class="<?= $totalQ > 0 ? 'hidden' : '' ?> py-16 text-center">
        <i class="fas fa-archive text-4xl text-slate-300"></i>
        <p class="mt-3 font-medium text-slate-500">Tidak ada arsip ditemukan</p>
        <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter tahun.</p>
      </div>
      <div id="tabelWrap" class="<?= $totalQ > 0 ? '' : 'hidden' ?> max-h-[560px] overflow-y-auto">
        <table class="w-full text-left text-sm">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Dokumen</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Diunggah oleh</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody id="arsipBody" class="divide-y divide-slate-100">
            <?php
            if(mysqli_num_rows($ambil) > 0){
                $no = 1;
                while($data = mysqli_fetch_assoc($ambil)){
                    $url = url_file($data['file']);
                    $ekstensi = '';
                    if($data['file'] !== ''){
                        $rowFile = mysqli_fetch_assoc(query("SELECT format_file FROM files WHERE id='" . bersihkan($data['file']) . "' LIMIT 1"));
                        $ekstensi = strtoupper($rowFile['format_file'] ?? '');
                    }
                    ?>
                    <tr class="transition hover:bg-orange-50 <?= ($no % 2 == 0) ? 'bg-slate-50/60' : 'bg-white' ?>" data-id="<?= $data['id'] ?>" data-tahun="<?= $data['tahun'] ?>" data-nama="<?= strtolower($data['nama']) ?>" data-ptext="<?= tampil($data['nama']) ?>" data-pengupload="<?= strtolower($data['nama_user'] ?? $data['pengupload']) ?>" data-file="<?= $data['file'] ?>">
                        <td class="py-4 pl-5 pr-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm"><?= $no ?></span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 leading-snug"><?= tampil($data['nama']) ?></p>
                                    <?php if($ekstensi !== ''): ?>
                                    <span class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold <?= in_array($ekstensi, ['PDF']) ? 'bg-rose-500 text-white' : 'bg-emerald-600 text-white' ?>"><i class="fas fa-file-export"></i><?= $ekstensi ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700"><?= $data['tahun'] ?></span>
                        </td>
                        <td class="px-4 py-4 text-slate-600"><?= tampil($data['nama_user'] ?? $data['pengupload']) ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <?php if($url !== ''): ?>
                                <a href="<?= $url ?>" target="_blank" rel="noopener" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                                </a>
                                <?php endif; ?>
                                <button type="button" class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                                </button>
                                <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="<?= $data['id'] ?>">
                                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $no++;
                }
            } ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div id="paginationWrap" class="<?= $totalQ > 0 ? '' : 'hidden' ?> flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
        <p class="pg-label text-xs font-semibold text-slate-500" id="lblPage">Halaman 1 / 1</p>
        <div class="inline-flex items-center gap-1.5" id="pgWrap"></div>
      </div>

    </div>
  </section>

  <!-- ===== Modal Unggah / Edit Data ===== -->
  <div class="modal-overlay" id="arsipModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
        <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-archive mr-1 text-[#f97316]"></i><span id="arsipModalTitle">Unggah Dokumen</span></h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" data-modal-close>&times;</button>
      </div>
      <form id="arsipForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="fId" value="">
        <input type="hidden" name="file_lama" id="fFileLama" value="">
        <div class="grid gap-4 p-5">
          <div>
            <label for="fNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Dokumen</label>
            <input type="text" id="fNama" name="nama" required
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
              placeholder="contoh: SK Pendirian">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="fInTahun" class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
              <select id="fInTahun" name="tahun" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                <?php
                for($y = date('Y'); $y >= 2000; $y--){
                ?>
                <option value="<?= $y ?>"><?= $y ?></option>
                <?php } ?>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-xs font-semibold text-slate-600">Diunggah oleh</label>
              <input type="text" value="<?= tampil($_SESSION['nama'] ?? 'Sistem') ?>" readonly
                class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
            </div>
          </div>
          <div>
            <label for="fFile" class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
            <input type="file" id="fFile" name="file_upload"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white file:mr-3 file:rounded-md file:border-0 file:bg-orange-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-orange-600">
          </div>
        </div>
        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" data-modal-close>Batal</button>
          <button type="submit" class="px-4 py-1.5 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    var isEdit = false;
    var BASE_URL = '<?= base_url() ?>';
    var PROSES_URL = BASE_URL + '/administrator/pages/jurusan/arsip/proses.php';

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

    function hapusArsip(id, btn){
      try {
        var tr = btn.closest('tr');
        var nama = tr.querySelector('p.font-semibold').textContent;
        if (typeof konfirmasiHapus === 'function') {
          konfirmasiHapus(nama, function(){
            $.ajax({
              url: PROSES_URL,
              method: 'POST',
              data: { 'hapus-data-arsip-jurusan': 1, id: id },
              dataType: 'json',
              success: function(respon){
                toast(respon.pesan, respon.status ? 'success' : 'error');
                if(respon.status){
                  muatUlangTabel();
                  muatStatistik();
                }
              },
              error: function(xhr){
                toast('Terjadi kesalahan pada server', 'error');
              }
            });
          });
        } else {
          toast('Fungsi konfirmasi tidak tersedia', 'error');
        }
      } catch(err) {
        toast('Error: ' + err.message, 'error');
      }
    }

    (function(){
      var ITEM_PER_PAGE = 10;
      var halaman = 1;
      var fCari = document.getElementById('fCari');
      var fTahun = document.getElementById('fTahun');
      var arsipBody = document.getElementById('arsipBody');
      var jmlArsip = document.getElementById('jmlArsip');
      var badgeArsip = document.getElementById('badgeArsip');
      var noHasil = document.getElementById('noHasil');
      var lblPage = document.getElementById('lblPage');
      var pgWrap = document.getElementById('pgWrap');

      function jumlahHalaman(){
        var visible = arsipBody.querySelectorAll('tr[data-id]');
        var count = 0;
        for(var i = 0; i < visible.length; i++){
          if(visible[i].style.display !== 'none') count++;
        }
        return Math.max(1, Math.ceil(count / ITEM_PER_PAGE));
      }

      function render(){
        var kata = (fCari.value || '').toLowerCase().trim();
        var tahun = fTahun.value;
        var tampil = 0;
        var rows = arsipBody.querySelectorAll('tr[data-id]');

        for(var i = 0; i < rows.length; i++){
          var ok = true;
          if(kata !== ''){
            var teks = (rows[i].getAttribute('data-nama') || '') + ' ' + (rows[i].getAttribute('data-pengupload') || '');
            if(teks.indexOf(kata) === -1) ok = false;
          }
          if(ok && tahun !== '' && rows[i].getAttribute('data-tahun') !== tahun) ok = false;
          rows[i].style.display = ok ? '' : 'none';
          if(ok) tampil++;
        }

        var jmlHal = jumlahHalaman();
        if(halaman > jmlHal) halaman = jmlHal;

        var allVisible = [];
        for(var i = 0; i < rows.length; i++){
          if(rows[i].style.display !== 'none') allVisible.push(rows[i]);
        }
        for(var i = 0; i < rows.length; i++) rows[i].style.display = 'none';
        var tutup = (halaman - 1) * ITEM_PER_PAGE;
        for(var i = tutup; i < tutup + ITEM_PER_PAGE && i < allVisible.length; i++){
          allVisible[i].style.display = '';
        }

        if(jmlArsip) jmlArsip.textContent = tampil;
        if(badgeArsip) badgeArsip.textContent = tampil;
        if(noHasil) noHasil.classList.toggle('hidden', tampil > 0);
        var tabelWrap = document.getElementById('tabelWrap');
        if(tabelWrap) tabelWrap.classList.toggle('hidden', tampil === 0);
        var paginationWrap = document.getElementById('paginationWrap');
        if(paginationWrap) paginationWrap.classList.toggle('hidden', tampil === 0);
        if(lblPage) lblPage.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;

        if(pgWrap){
          pgWrap.innerHTML = '';
          var prev = document.createElement('button');
          prev.type = 'button';
          prev.className = 'pg-btn';
          prev.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
          prev.disabled = halaman <= 1;
          prev.addEventListener('click', function(){ if(halaman > 1){ halaman--; render(); } });
          pgWrap.appendChild(prev);

          for(var i = 1; i <= jmlHal; i++){
            (function(n){
              var b = document.createElement('button');
              b.type = 'button';
              b.className = 'pg-btn' + (n === halaman ? ' on' : '');
              b.textContent = n;
              b.addEventListener('click', function(){ halaman = n; render(); });
              pgWrap.appendChild(b);
            })(i);
          }

          var next = document.createElement('button');
          next.type = 'button';
          next.className = 'pg-btn';
          next.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
          next.disabled = halaman >= jmlHal;
          next.addEventListener('click', function(){ if(halaman < jmlHal){ halaman++; render(); } });
          pgWrap.appendChild(next);
        }
      }

      if(fCari) fCari.addEventListener('input', function(){ halaman = 1; render(); });
      if(fTahun) fTahun.addEventListener('change', function(){ halaman = 1; render(); });
      render();

      document.getElementById('btnReset').addEventListener('click', function(){
        fCari.value = '';
        fTahun.value = '';
        halaman = 1;
        render();
      });

      // expose fungsi reload ke global scope agar bisa dipanggil dari handler lain (hapusArsip, form submit)
      window.muatUlangTabel = function(){
        $.ajax({
          url: PROSES_URL,
          method: 'POST',
          data: { 'ambil-tabel-arsip-jurusan': 1 },
          dataType: 'json',
          success: function(respon){
            if(respon.status){
              $('#arsipBody').html(respon.data);
              render();
            }
          },
          error: function(xhr){
            toast('Terjadi kesalahan pada server', 'error');
          }
        });
      };

      window.muatStatistik = function(){
        $.ajax({
          url: PROSES_URL,
          method: 'POST',
          data: { 'ambil-statistik-arsip': 1 },
          dataType: 'json',
          success: function(respon){
            if(respon.status){
              $('#statTotal').text(respon.data.total);
              $('#statTahunMin').text(respon.data.tahun_min);
              $('#statTahunMax').text(respon.data.tahun_max);
              $('#statPengupload').text(respon.data.pengupload_nama);
              $('#statPenguploadJml').text(respon.data.pengupload_jml + ' dokumen');
              $('#jmlArsip').text(respon.data.total);
              $('#badgeArsip').text(respon.data.total);
            }
          },
          error: function(xhr){
            toast('Terjadi kesalahan pada server', 'error');
          }
        });
      };
    })();

    /* ===== Modal Unggah / Edit ===== */
    document.addEventListener('DOMContentLoaded', function(){
      var overlay = document.getElementById('arsipModal');
      var title = document.getElementById('arsipModalTitle');
      var f = document.getElementById('arsipForm');
      var iId = document.getElementById('fId');
      var iNama = document.getElementById('fNama');
      var iTahun = document.getElementById('fInTahun');
      var iFileLama = document.getElementById('fFileLama');

      function show(t, editMode){
        title.textContent = t;
        isEdit = editMode;
        if(!editMode){
          f.reset();
          iId.value = '';
          iFileLama.value = '';
        }
        overlay.classList.add('show');
      }
      function closeModal(){ overlay.classList.remove('show'); }

      document.getElementById('btnTambah').addEventListener('click', function(){
        show('Unggah Dokumen Baru', false);
      });
      var cls = overlay.querySelectorAll('[data-modal-close]');
      for(var i = 0; i < cls.length; i++)
        cls[i].addEventListener('click', closeModal);
      overlay.addEventListener('click', function(e){ if(e.target === overlay) closeModal(); });

      f.addEventListener('submit', function(e){
        e.preventDefault();

        var data = new FormData(f);
        data.append(isEdit ? 'edit-data-arsip-jurusan' : 'tambah-data-arsip-jurusan', 1);

        $.ajax({
          url: PROSES_URL,
          method: 'POST',
          data: data,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(respon){
            toast(respon.pesan, respon.status ? 'success' : 'error');
            if(respon.status){
              closeModal();
              muatUlangTabel();
              muatStatistik();
            }
          },
          error: function(xhr){
            toast('Terjadi kesalahan pada server', 'error');
          }
        });
      });

      document.addEventListener('click', function(e){
        if(e.target.closest('.btnCircleEdit')){
          var tr = e.target.closest('tr');
          iId.value = tr.getAttribute('data-id');
          iNama.value = tr.getAttribute('data-ptext');
          iTahun.value = tr.getAttribute('data-tahun');
          iFileLama.value = tr.getAttribute('data-file');
          show('Edit Dokumen', true);
        }
        if(e.target.closest('.btnHapus')){
          var btn = e.target.closest('.btnHapus');
          var id = btn.getAttribute('data-id');
          var tr = btn.closest('tr');
          var nama = tr.querySelector('p.font-semibold').textContent;
          hapusArsip(id, btn);
        }
      });
    });
  </script>

</main>
