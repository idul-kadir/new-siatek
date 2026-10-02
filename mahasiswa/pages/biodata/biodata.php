<?php
/**
 * Halaman: Biodata Mahasiswa — TERHUBUNG DATABASE.
 * Sumber data  : tabel `mahasiswa`.
 * Pengguna     : dari session login.
 * Aksi simpan  : pages/biodata/proses.php.
 */
require_once __DIR__ . '/../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

/* ---------- Tentukan pengguna aktif ---------- */
$nimAktif = $_SESSION['nim'] ?? '';

$stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM mahasiswa WHERE TRIM(nim) = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $nimAktif);
mysqli_stmt_execute($stmt);
$d = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$d) {
    echo '<main class="content-area content-scroll"><div class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">'
        . '<i class="fas fa-user-slash text-4xl text-slate-300"></i>'
        . '<p class="mt-3 font-medium text-slate-500">Data mahasiswa dengan NIM ' . tampil($nimAktif) . ' tidak ditemukan.</p></div></main>';
    return;
}
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; bottom: calc(100% + 6px); right: 0; background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .form-label { display: block; margin-bottom: .25rem; font-size: .75rem; font-weight: 600; color: #475569; }
    .form-input, .form-select { width: 100%; border-radius: .5rem; border: 1px solid #e2e8f0; background: #f8fafc; padding: .5rem .75rem; font-size: .875rem; outline: none; transition: all .15s ease; }
    .form-input:focus, .form-select:focus { border-color: #fb923c; background: #fff; }
    .form-input[readonly] { cursor: not-allowed; color: #64748b; }

    .fact-row { display: flex; align-items: flex-start; gap: .65rem; padding: .45rem 0; }
    .fact-row + .fact-row { border-top: 1px dashed #f1f5f9; }
    .fact-row .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.85rem; height: 1.85rem; border-radius: .55rem; background: #fff7ed; color: #ea580c; font-size: .72rem; flex-shrink: 0; }
    .fact-row .f-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
    .fact-row .f-value { font-size: .8rem; font-weight: 600; color: #334155; line-height: 1.35; }

    .pw-check { display: inline-flex; align-items: center; gap: .3rem; font-size: 10px; font-weight: 600; color: #94a3b8; }
    .pw-check i { font-size: .58rem; }
    .pw-check.ok { color: #059669; }

    #bioToast { position: fixed; right: 20px; bottom: 20px; z-index: 2000; display: none; align-items: center; gap: .6rem; max-width: 360px; border-radius: .75rem; background: #0f172a; color: #fff; padding: .7rem 1rem; font-size: .82rem; box-shadow: 0 12px 24px rgba(15,23,42,.25); }
    #bioToast.show { display: flex; animation: riseIn .3s ease; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Biodata Mahasiswa</h1>
                    <p class="text-xs text-slate-500">Data profil, akademik, dan akun pengguna Anda.</p>
                </div>
            </div>
            <?php $aktif = strtolower(trim((string)$d['status'])) === 'aktif'; ?>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= $aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?php if ($aktif): ?><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span><?php endif; ?>
                <?= $aktif ? 'Mahasiswa Aktif' : 'Status: ' . tampil($d['status']) ?>
            </span>
        </div>
    </section>

    <!-- ===== Kartu Profil Mahasiswa ===== -->
    <section class="mb-5">
        <div class="reveal rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-5">
                <div class="relative shrink-0">
                    <div id="avatarWrap" class="relative shrink-0">
                    <?php
                    $fotoProfil = trim((string)$d['foto']);
                    $srcFoto = $fotoProfil !== '' ? url_file($fotoProfil) : '';
                    if ($srcFoto !== '') {
                    ?>
                    <img src="<?= tampil($srcFoto) ?>" alt="Foto Profil" class="h-[88px] w-[88px] rounded-full object-cover ring-2 ring-slate-200">
                    <?php } else { ?>
                    <svg width="88" height="88" viewBox="0 0 88 88" aria-hidden="true">
                        <defs>
                            <linearGradient id="bioAvGrad" x1="0" y1="0" x2="88" y2="88" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#1a365d"/><stop offset="1" stop-color="#f97316"/>
                            </linearGradient>
                        </defs>
                        <rect width="88" height="88" rx="44" fill="url(#bioAvGrad)"/>
                        <text x="44" y="46" fill="#fff" font-family="Inter, sans-serif" font-size="30" font-weight="700" text-anchor="middle" dominant-baseline="central"><?= tampil(mb_substr(preg_replace('/[^\p{L}]/u', '', explode(' ', (string)$d['nama'])[0]), 0, 2)) ?></text>
                    </svg>
                    <?php } ?>
                    </div>
                    <?php
                    $adaFoto = ($srcFoto !== '');
                    ?>
                    <div class="absolute -bottom-1 right-0">
                        <button type="button" id="btnFoto" class="btn-circle bg-orange-500 text-white shadow-md hover:bg-orange-600" title="Aksi Foto Profil">
                            <i class="fas fa-camera text-xs"></i><span class="tip">Aksi Foto Profil</span>
                        </button>
                        <div id="menuFoto" class="hidden absolute right-0 bottom-full mb-2 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg z-50">
                            <button type="button" id="menuGantiFoto" class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-medium text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-camera text-orange-500"></i>Ganti Foto
                            </button>
                            <button type="button" id="menuHapusFoto" <?= $adaFoto ? '' : 'disabled' ?> class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-medium <?= $adaFoto ? 'text-rose-600 hover:bg-rose-50' : 'text-slate-300 cursor-not-allowed' ?>">
                                <i class="fas fa-trash"></i>Hapus Foto
                            </button>
                        </div>
                    </div>
                    <input type="file" id="inputFoto" accept="image/*" hidden>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 id="pfNama" class="truncate text-lg font-bold tracking-tight text-slate-800"><?= tampil($d['nama']) ?></h2>
                    <p id="pfIdentitas" class="mt-0.5 text-xs text-slate-500">NIM <?= tampil($d['nim']) ?> · <?= tampil($d['prodi']) ?></p>
                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-[10px] font-bold text-orange-700"><i class="fas fa-calendar"></i>Angkatan <?= tampil($d['angkatan']) ?></span>
                    </div>
                </div>
                <div class="w-full sm:w-auto lg:min-w-[290px]">
                    <div class="rounded-xl bg-slate-50 px-4 py-2">
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-building-columns"></i></span>
                            <div><p class="f-label">Program Studi</p><p class="f-value"><?= trim((string)$d['prodi']) !== '' ? tampil($d['prodi']) : '-' ?></p></div>
                        </div>
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-envelope"></i></span>
                            <div><p class="f-label">Email</p><p class="f-value"><?= trim((string)$d['email']) !== '' ? tampil($d['email']) : '-' ?></p></div>
                        </div>
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-phone"></i></span>
                            <div><p class="f-label">No. Telepon / WA</p><p class="f-value"><?= trim((string)$d['no_hp']) !== '' ? tampil($d['no_hp']) : '-' ?></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Dua Kolom: Data Profil & Password ===== -->
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:items-start">

        <!-- Data Profil & Kontak -->
        <div class="reveal rounded-xl border border-slate-200 bg-white shadow-sm" style="animation-delay:.05s">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-user mr-1.5 text-orange-500"></i>Data Profil &amp; Kontak</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Perubahan tersimpan langsung ke database.</p>
                </div>
            </div>
            <form id="formAkademik" method="post" action="/redesain-siatek/mahasiswa/pages/biodata/proses.php" class="space-y-3.5 p-5" novalidate>

                <!-- Identitas inti -->
                <div>
                    <label class="form-label" for="inNama">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="inNama" name="nama" value="<?= tampil($d['nama']) ?>" class="form-input">
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="inNip">NIM</label>
                        <input type="text" id="inNip" name="nip" value="<?= tampil($d['nim']) ?>" readonly class="form-input" style="background-color: #f1f5f9;">
                    </div>
                    <div>
                        <label class="form-label" for="inProdi">Program Studi</label>
                        <input type="text" id="inProdi" name="prodi" value="<?= tampil($d['prodi']) ?>" class="form-input">
                    </div>
                </div>

                <!-- Status & Angkatan -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="inAngkatan">Angkatan</label>
                        <input type="number" id="inAngkatan" name="angkatan" value="<?= tampil($d['angkatan']) ?>" readonly class="form-input" style="background-color: #f1f5f9;">
                    </div>
                    <div>
                        <label class="form-label" for="inStatus">Status</label>
                        <input type="text" id="inStatus" name="status" value="<?= tampil($d['status']) ?>" readonly class="form-input" style="background-color: #f1f5f9;">
                    </div>
                </div>

                <!-- Kontak -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 mt-2 pt-3 border-t border-slate-100">
                    <div>
                        <label class="form-label" for="inHp">No. Handphone/WhatsApp</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input type="text" id="inHp" name="no_hp" value="<?= tampil($d['no_hp']) ?>" inputmode="numeric" placeholder="Contoh: 081234..." class="form-input pl-8">
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="inEmail">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input type="email" id="inEmail" name="email" value="<?= tampil($d['email']) ?>" placeholder="email@contoh.com" class="form-input pl-8">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 flex items-center justify-end border-t border-slate-100">
                    <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-lg bg-[#1a365d] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#234670] focus:ring-4 focus:ring-[#1a365d]/20 active:scale-[.98]">
                        <i class="fas fa-save"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Ubah Password -->
        <div class="reveal rounded-xl border border-slate-200 bg-white shadow-sm" style="animation-delay:.1s">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-lock mr-1.5 text-orange-500"></i>Ubah Password</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Perbarui kata sandi akun portal Anda secara berkala.</p>
                </div>
            </div>
            <form id="formPassword" method="post" action="/redesain-siatek/mahasiswa/pages/biodata/proses.php" class="space-y-3.5 p-5" novalidate>
                <div>
                    <label class="form-label" for="inPwLama">Password Saat Ini <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="inPwLama" name="pw_lama" placeholder="Password lama Anda" class="form-input pr-10" autocomplete="current-password" required>
                        <button type="button" data-toggle-pw="inPwLama" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                </div>
                <div>
                    <label class="form-label" for="inPwBaru">Password Baru <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="inPwBaru" name="pw_baru" placeholder="Minimal 8 karakter" class="form-input pr-10" autocomplete="new-password" required>
                        <button type="button" data-toggle-pw="inPwBaru" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                            <div id="pwBar" class="h-full w-0 rounded-full transition-all duration-300"></div>
                        </div>
                        <span id="pwLabel" class="w-14 text-right text-[10px] font-bold text-slate-400">-</span>
                    </div>
                    <div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-1">
                        <span class="pw-check" data-rule="len"><i class="fas fa-circle"></i>Min. 8 karakter</span>
                        <span class="pw-check" data-rule="upper"><i class="fas fa-circle"></i>Huruf besar</span>
                        <span class="pw-check" data-rule="lower"><i class="fas fa-circle"></i>Huruf kecil</span>
                        <span class="pw-check" data-rule="digit"><i class="fas fa-circle"></i>Angka</span>
                    </div>
                </div>
                <div>
                    <label class="form-label" for="inPwKonfirmasi">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="inPwKonfirmasi" name="pw_konfirmasi" placeholder="Ulangi password baru" class="form-input pr-10" autocomplete="new-password" required>
                        <button type="button" data-toggle-pw="inPwKonfirmasi" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i class="fas fa-eye text-xs"></i></button>
                    </div>
                    <p id="pwMatchInfo" class="mt-1 hidden text-[11px] font-semibold text-rose-500"><i class="fas fa-circle-exclamation mr-1"></i>Konfirmasi belum sama dengan password baru.</p>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" id="btnUbahPassword" name="ubah-password" value="1" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                        <i class="fas fa-key"></i>Ubah Password
                    </button>
                </div>
            </form>
        </div>

    </section>

</main>

<div id="bioToast">
    <i class="fas fa-circle-check text-emerald-400"></i>
    <span id="bioToastMsg" class="flex-1">Pesan notifikasi</span>
</div>

<script>
$(function(){
    // Toggle Password
    $('[data-toggle-pw]').on('click', function(){
        var targetId = $(this).data('toggle-pw');
        var input = $('#' + targetId);
        var type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Toast Notification
    function toast(msg, type) {
        var t = $('#bioToast');
        $('#bioToastMsg').text(msg);
        t.find('i').attr('class', type === 'error' ? 'fas fa-circle-xmark text-rose-400' : 'fas fa-circle-check text-emerald-400');
        t.addClass('show');
        setTimeout(function(){ t.removeClass('show'); }, 3000);
    }

    // Submit Ubah Data
    $('#formAkademik').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize() + '&ubah-biodata=1',
            dataType: 'json',
            success: function(res){
                btn.prop('disabled', false);
                if (res.status) {
                    toast(res.pesan, 'success');
                    if(res.data && res.data.nama) {
                        $('#pfNama').text(res.data.nama);
                    }
                } else {
                    toast(res.pesan, 'error');
                }
            },
            error: function(){
                btn.prop('disabled', false);
                toast('Gagal menghubungi server', 'error');
            }
        });
    });

    // Submit Ubah Password
    $('#formPassword').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize() + '&ubah-password=1',
            dataType: 'json',
            success: function(res){
                btn.prop('disabled', false);
                if (res.status) {
                    toast(res.pesan, 'success');
                    form[0].reset();
                } else {
                    toast(res.pesan, 'error');
                }
            },
            error: function(){
                btn.prop('disabled', false);
                toast('Gagal menghubungi server', 'error');
            }
        });
    });

    // Foto Dropdown Toggle
    $('#btnFoto').on('click', function(e){
        e.stopPropagation();
        $('#menuFoto').toggleClass('hidden');
    });

    $(document).on('click', function(){
        $('#menuFoto').addClass('hidden');
    });

    // Ganti Foto
    $('#menuGantiFoto').on('click', function(){
        $('#inputFoto').click();
    });

    $('#inputFoto').on('change', function(){
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('foto', file);
        formData.append('unggah-foto', 1);

        $.ajax({
            url: '/redesain-siatek/mahasiswa/pages/biodata/proses.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res){
                if (res.status) {
                    toast(res.pesan, 'success');
                    if (res.data && res.data.foto_url) {
                        $('#avatarWrap').html('<img src="' + res.data.foto_url + '" alt="Foto Profil" class="h-[88px] w-[88px] rounded-full object-cover ring-2 ring-slate-200">');
                        $('#menuHapusFoto').removeAttr('disabled').removeClass('text-slate-300 cursor-not-allowed').addClass('text-rose-600 hover:bg-rose-50');
                    }
                } else {
                    toast(res.pesan, 'error');
                }
            },
            error: function(){
                toast('Gagal mengunggah foto', 'error');
            }
        });
    });

    // Hapus Foto
    $('#menuHapusFoto').on('click', function(){
        if ($(this).attr('disabled')) return;
        if (!confirm('Hapus foto profil ini?')) return;

        $.ajax({
            url: '/redesain-siatek/mahasiswa/pages/biodata/proses.php',
            method: 'POST',
            data: { 'hapus-foto': 1 },
            dataType: 'json',
            success: function(res){
                if (res.status) {
                    toast(res.pesan, 'success');
                    location.reload();
                } else {
                    toast(res.pesan, 'error');
                }
            },
            error: function(){
                toast('Gagal menghapus foto', 'error');
            }
        });
    });
});
</script>