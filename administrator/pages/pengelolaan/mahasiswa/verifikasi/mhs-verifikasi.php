<?php
require __DIR__ . "/../../../../fungsi.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

$userLevel = (int)($_SESSION['level'] ?? 0);
$isLevel3  = ($userLevel === 3);
$userProdi = $isLevel3 ? get_prodi_user() : '';
$namaProdiUser = '';
if ($isLevel3 && $userProdi !== '') {
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

  .modal-overlay { position: fixed; inset: 0; z-index: 1050; background: rgba(15,23,42,.6); display: none; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(2px); }
  .modal-overlay.show { display: flex; animation: fadeIn .2s ease-out forwards; }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

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
    <div id="toastContainer"></div>

    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Verifikasi Berkas</h1>
                    <p class="text-xs text-slate-500">
                        Verifikasi berkas persyaratan skripsi mahasiswa
                        <?php if ($isLevel3 && $namaProdiUser !== ''): ?>
                            &bull; <span class="font-semibold text-orange-600"><?= tampil($namaProdiUser) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php if ($isLevel3 && $namaProdiUser !== ''): ?>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-700">
                    <i class="fas fa-building-columns text-[11px]"></i> Program Studi: <?= tampil($namaProdiUser) ?>
                </span>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Filter Bar ===== -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <!-- Search NIM / Nama -->
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari nama atau NIM mahasiswa..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>

            <!-- Filter Prodi -->
            <select id="fProdi" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 <?= $isLevel3 ? 'pointer-events-none opacity-80 cursor-not-allowed' : '' ?>" <?= $isLevel3 ? 'disabled' : '' ?>>
                <?php if (!$isLevel3): ?>
                    <option value="" selected>Semua Program Studi</option>
                    <?php
                    $qProdi = query("SELECT kode, nama FROM prodi ORDER BY nama ASC");
                    if($qProdi){
                        while($pr = mysqli_fetch_assoc($qProdi)){
                            echo '<option value="' . tampil($pr['kode']) . '">' . tampil($pr['nama']) . '</option>';
                        }
                    }
                    ?>
                <?php else: ?>
                    <option value="<?= tampil($userProdi) ?>" selected><?= tampil($namaProdiUser ?: $userProdi) ?></option>
                <?php endif; ?>
            </select>

            <!-- Filter Status -->
            <select id="fStatus" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="" selected>Semua Status Berkas</option>
                <option value="menunggu">Menunggu Verifikasi</option>
                <option value="diterima">Diterima</option>
                <option value="ditolak">Ditolak</option>
            </select>

            <!-- Reset Button -->
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </button>
        </div>
    </section>

    <!-- ===== Daftar Verifikasi ===== -->
    <section>
        <div id="tabelWrap" class="space-y-4">
            <div class="acc-loading py-4 text-center"><div class="spinner-slate mx-auto"></div><p class="mt-3 text-muted" style="font-size: .85rem;">Memuat data...</p></div>
        </div>
    </section>

    <!-- Modal Pratinjau & Verifikasi Berkas -->
    <div class="modal-overlay" id="modalPratinjau" role="dialog" aria-modal="true">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[90vh] overflow-hidden flex flex-col relative">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug" id="pratinjauNamaBerkas">Nama Berkas</h3>
                        <p class="text-xs text-slate-500" id="pratinjauNamaMahasiswa">Nama Mahasiswa</p>
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-2xl leading-none px-2" data-modal-close>&times;</button>
            </div>

            <!-- Body Modal -->
            <div class="flex flex-1 overflow-hidden bg-slate-100">
                <!-- Kiri: Penampil File -->
                <div class="flex-1 border-r border-slate-200 p-2 relative h-full flex flex-col items-center justify-center" id="containerIframe">
                    <div class="acc-loading py-4 text-center absolute inset-0 flex flex-col items-center justify-center z-0 bg-slate-100" id="loadingPratinjau">
                        <div class="spinner-slate mx-auto"></div>
                        <p class="mt-3 text-muted text-xs">Memuat dokumen...</p>
                    </div>
                    <iframe id="iframePratinjau" class="w-full h-full rounded-lg bg-white shadow-sm relative z-10" src="about:blank" onload="document.getElementById('loadingPratinjau').style.display='none'"></iframe>
                </div>

                <!-- Kanan: Panel Aksi -->
                <div class="w-80 bg-white flex flex-col flex-shrink-0">
                    <div class="p-5 flex-1 overflow-y-auto">
                        <div class="mb-4">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-700 mb-2 uppercase tracking-wide">
                                Tahap SOP
                            </span>
                            <p class="text-sm text-slate-600 leading-relaxed font-medium" id="pratinjauSop"></p>
                        </div>

                        <div class="mb-4 pt-4 border-t border-slate-100">
                            <h4 class="text-xs font-bold text-slate-800 mb-2 uppercase tracking-wide">Status Saat Ini</h4>
                            <span id="pratinjauBadgeStatus" class="inline-flex items-center rounded border px-3 py-1 text-xs font-medium"></span>
                            <p id="pratinjauCatatan" class="text-xs text-slate-500 italic mt-2 hidden"></p>
                        </div>
                        
                        <!-- Form Tolak Inline -->
                        <div id="panelFormTolakPratinjau" class="hidden mt-4 pt-4 border-t border-rose-100 bg-rose-50 -mx-5 px-5 pb-5">
                            <h4 class="text-xs font-bold text-rose-800 mb-2 uppercase tracking-wide"><i class="fas fa-exclamation-circle mr-1"></i> Alasan Penolakan</h4>
                            <textarea id="ketTolakPratinjau" rows="3" class="w-full rounded-md border border-rose-300 px-3 py-2 text-sm outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 bg-white" placeholder="Tuliskan alasan berkas ini ditolak..." required></textarea>
                            <div class="flex gap-2 mt-3">
                                <button type="button" class="btnBatalTolak flex-1 rounded-md bg-white border border-slate-300 hover:bg-slate-50 py-1.5 text-xs font-semibold text-slate-700 transition">Batal</button>
                                <button type="button" class="btnSubmitTolakPratinjau flex-1 rounded-md bg-rose-600 hover:bg-rose-700 py-1.5 text-xs font-semibold text-white transition" data-text="Kirim Penolakan">Kirim Penolakan</button>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Bawah: Tombol Aksi -->
                    <div class="p-5 border-t border-slate-200 bg-slate-50" id="panelAksiPratinjau">
                        <h4 class="text-xs font-bold text-slate-800 mb-3 text-center uppercase tracking-wide">Ubah Status / Verifikasi</h4>
                        <div class="flex gap-2">
                            <button type="button" class="btnTolakInline flex-1 rounded-lg bg-rose-500 hover:bg-rose-600 py-2.5 text-sm font-semibold text-white transition flex justify-center items-center gap-2">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                            <button type="button" class="btnAksiPratinjau flex-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 py-2.5 text-sm font-semibold text-white transition flex justify-center items-center gap-2" data-aksi="diterima">
                                <i class="fas fa-check"></i> Terima
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    var PROSES_URL = '<?= base_url() ?>/administrator/pages/pengelolaan/mahasiswa/verifikasi/proses.php';

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
        var status = $('#fStatus').val() || '';
        var prodi = '<?= $isLevel3 ? addslashes($userProdi) : '' ?>' || $('#fProdi').val() || '';

        $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 
                'ambil-tabel': 1,
                'cari': cari,
                'status': status,
                'prodi': prodi
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

        var delayTimer;
        $('#fCari').on('keyup input', function(){
            clearTimeout(delayTimer);
            delayTimer = setTimeout(muatUlangTabel, 300);
        });

        $('#fStatus, #fProdi').on('change', function(){
            muatUlangTabel();
        });

        $('#btnReset').on('click', function(){
            $('#fCari').val('');
            $('#fStatus').val('');
            <?php if (!$isLevel3): ?>
            $('#fProdi').val('');
            <?php else: ?>
            $('#fProdi').val('<?= addslashes($userProdi) ?>');
            <?php endif; ?>
            muatUlangTabel();
        });

        // Buka Modal Pratinjau
        $(document).on('click', '.btnPratinjau, .btnPratinjauStatus', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).data('url');
            var namaBerkas = $(this).data('namaberkas');
            var mhs = $(this).data('mhs');
            var sop = $(this).data('sop');
            var status = $(this).data('status');
            var badgestyle = $(this).data('badgestyle');
            var ket = $(this).data('ket') || '';

            $('#pratinjauNamaBerkas').text(namaBerkas);
            $('#pratinjauNamaMahasiswa').text(mhs);
            $('#pratinjauSop').text(sop);
            
            var badge = $('#pratinjauBadgeStatus');
            badge.removeClass().addClass('inline-flex items-center rounded border px-3 py-1 text-xs font-medium ' + badgestyle);
            badge.text(status.charAt(0).toUpperCase() + status.slice(1));
            
            if (ket !== '') {
                $('#pratinjauCatatan').show().text('Catatan: ' + ket);
            } else {
                $('#pratinjauCatatan').hide();
            }

            // Reset form tolak inline
            $('#panelFormTolakPratinjau').hide();
            $('#ketTolakPratinjau').val('');

            // Setup tombol aksi & simpan ID
            var panelAksi = $('#panelAksiPratinjau');
            if (status === 'menunggu') {
                panelAksi.show();
                $('.btnAksiPratinjau').data('id', id);
                $('.btnSubmitTolakPratinjau').data('id', id);
            } else {
                panelAksi.show();
                $('.btnAksiPratinjau').data('id', id);
                $('.btnSubmitTolakPratinjau').data('id', id);
            }

            // Tampilkan loading dan muat iframe
            document.getElementById('loadingPratinjau').style.display = 'flex';
            $('#iframePratinjau').attr('src', url);

            $('#modalPratinjau').addClass('show');
        });

        // Toggle Form Tolak Inline di Modal Pratinjau
        $(document).on('click', '.btnTolakInline', function(){
            $('#panelAksiPratinjau').hide();
            $('#panelFormTolakPratinjau').slideDown(200);
            $('#ketTolakPratinjau').focus();
        });

        $(document).on('click', '.btnBatalTolak', function(){
            $('#panelFormTolakPratinjau').slideUp(200, function(){
                $('#panelAksiPratinjau').show();
            });
        });

        // Submit Terima
        $(document).on('click', '.btnAksiPratinjau', function(){
            var id = $(this).data('id');
            var btn = this;
            setButtonLoading(btn, true);
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'verifikasi-berkas': 1, 'id': id, 'aksi': 'diterima' },
                dataType: 'json',
                success: function(respon) {
                    toast(respon.pesan, respon.status ? 'success' : 'error');
                    if (respon.status) {
                        $('#modalPratinjau').removeClass('show');
                        $('#iframePratinjau').attr('src', 'about:blank');
                        muatUlangTabel();
                        if (typeof window.cekNotifikasiVerifikasi === 'function') {
                            window.cekNotifikasiVerifikasi(true);
                        }
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

        // Submit Tolak Inline
        $(document).on('click', '.btnSubmitTolakPratinjau', function(){
            var id = $(this).data('id');
            var ket = $('#ketTolakPratinjau').val().trim();
            
            if (ket === '') {
                toast('Alasan penolakan wajib diisi', 'error');
                $('#ketTolakPratinjau').focus();
                return;
            }

            var btn = this;
            setButtonLoading(btn, true);

            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'verifikasi-berkas': 1, 'id': id, 'aksi': 'ditolak', 'ket': ket },
                dataType: 'json',
                success: function(respon) {
                    toast(respon.pesan, respon.status ? 'success' : 'error');
                    if (respon.status) {
                        $('#modalPratinjau').removeClass('show');
                        $('#iframePratinjau').attr('src', 'about:blank');
                        muatUlangTabel();
                        if (typeof window.cekNotifikasiVerifikasi === 'function') {
                            window.cekNotifikasiVerifikasi(true);
                        }
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

        $(document).on('click', '[data-modal-close]', function(e){
            e.stopPropagation();
            $(this).closest('.modal-overlay').removeClass('show');
        });
    });
</script>