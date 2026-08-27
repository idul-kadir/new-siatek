<?php
/**
 * Halaman: Biodata Pegawai/Dosen — TERHUBUNG DATABASE.
 * Sumber data  : tabel `dosen` (+ anak tabel `riwayat_pendidikan`).
 * Pengguna     : dari session login; fallback nip 10041993 (admin laboran).
 * Aksi simpan  : pages/biodata/proses.php (lihat file itu untuk logikanya).
 */
require_once __DIR__ . '/../../fungsi.php';
mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');

/* ---------- Tentukan pengguna aktif ---------- */
$nipAktif = $_SESSION['nip'] ?? $_SESSION['username'] ?? '10041993';
if (isset($_GET['nip']) && preg_match('/^[0-9]{6,25}$/', (string)$_GET['nip'])) {
    $nipAktif = $_GET['nip']; // mode lihat biodata orang lain (opsional)
}

$stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM dosen WHERE nip = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $nipAktif);
mysqli_stmt_execute($stmt);
$d = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$d) {
    echo '<main class="content-area content-scroll"><div class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">'
        . '<i class="fas fa-user-slash text-4xl text-slate-300"></i>'
        . '<p class="mt-3 font-medium text-slate-500">Data pegawai dengan NIP ' . tampil($nipAktif) . ' tidak ditemukan.</p></div></main>';
    return;
}

/* Riwayat pendidikan */
$stmt = mysqli_prepare($GLOBALS['koneksi'], 'SELECT * FROM riwayat_pendidikan WHERE nip=? ORDER BY tahun DESC, id DESC');
mysqli_stmt_bind_param($stmt, 's', $nipAktif);
mysqli_stmt_execute($stmt);
$resRp = mysqli_stmt_get_result($stmt);

$bidangArr   = array_values(array_filter(array_map('trim', explode(',', (string)$d['keahlian'])), 'strlen'));
$nidnTampil  = trim((string)$d['nidn']);
$rank        = ['TK'=>0,'SD'=>1,'SMP'=>2,'SMA/SMK'=>3,'D1'=>4,'D2'=>5,'D3'=>6,'D4'=>7,'S1'=>8,'S2'=>9,'S3'=>10];
$eduTerakhir = '';
foreach (mysqli_fetch_all($resRp, MYSQLI_ASSOC) as $r) {
    if ($eduTerakhir === '' || ($rank[$r['jenjang']] ?? -1) >= ($rank[$eduTerakhir] ?? -1)) $eduTerakhir = $r['jenjang'];
}
mysqli_data_seek($resRp, 0);

$levelTeks = (string)$d['level'] === '0' ? 'Super Admin' : ((string)$d['level'] === '1' ? 'Pengguna' : 'Admin');
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

    .bidang-tag { display: inline-flex; align-items: center; gap: .45rem; border-radius: 9999px; border: 1px solid #fed7aa; background: #fff7ed; color: #c2410c; font-size: .72rem; font-weight: 700; padding: .34rem .5rem .34rem .8rem; max-width: 100%; }
    .bidang-tag .t-nama { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bidang-tag .t-x { display: inline-flex; align-items: center; justify-content: center; width: 1.2rem; height: 1.2rem; border-radius: 9999px; color: #f97316; flex-shrink: 0; transition: all .15s ease; cursor: pointer; }
    .bidang-tag .t-x:hover { background: #f97316; color: #fff; }

    .fact-row { display: flex; align-items: flex-start; gap: .65rem; padding: .45rem 0; }
    .fact-row + .fact-row { border-top: 1px dashed #f1f5f9; }
    .fact-row .f-ico { display: inline-flex; align-items: center; justify-content: center; width: 1.85rem; height: 1.85rem; border-radius: .55rem; background: #fff7ed; color: #ea580c; font-size: .72rem; flex-shrink: 0; }
    .fact-row .f-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
    .fact-row .f-value { font-size: .8rem; font-weight: 600; color: #334155; line-height: 1.35; }

    .edu-row { display: flex; align-items: center; gap: .75rem; padding: .5rem 0; }
    .edu-row + .edu-row { border-top: 1px dashed #f1f5f9; }
    .edu-row .e-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 1.55rem; border-radius: .45rem; background: #fff7ed; color: #c2410c; font-size: .66rem; font-weight: 800; letter-spacing: .03em; }
    .edu-row .e-title { font-size: .78rem; font-weight: 600; color: #334155; }
    .edu-row .e-sub { font-size: .68rem; color: #94a3b8; }

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
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Biodata Pegawai</h1>
                    <p class="text-xs text-slate-500">Data kepegawaian, bidang ilmu, dan akun pengguna yang sedang login.</p>
                </div>
            </div>
            <?php $aktif = strtolower(trim((string)$d['status'])) === 'aktif'; ?>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold <?= $aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?php if ($aktif): ?><span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span></span><?php endif; ?>
                <?= $aktif ? 'Akun Aktif' : 'Akun Non-Aktif' ?>
            </span>
        </div>
    </section>

    <!-- ===== Kartu Profil Pegawai ===== -->
    <section class="mb-5">
        <div class="reveal rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-5">
                <div class="relative shrink-0">
                    <div id="avatarWrap" class="relative shrink-0">
                    <?php
                    // profil menyimpan id file di tabel files -> ubah jadi URL nyata
                    $fotoProfil = trim((string)$d['profil']);
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
                        <text x="44" y="46" fill="#fff" font-family="Inter, sans-serif" font-size="30" font-weight="700" text-anchor="middle" dominant-baseline="central"><?= tampil(mb_substr(preg_replace('/[^\p{L}]/u', '', explode(',', (string)$d['nama'])[0]), 0, 2)) ?></text>
                    </svg>
                    <?php } ?>
                    </div>
                    <?php
                    // kendali foto: SATU tombol -> saat diklik muncul 2 pilihan (Ganti/Hapus).
                    // Dropdown diposisikan ke kanan (right-0) dengan z-index tinggi supaya
                    // tidak tertimpa sidebar/konten di sebelah kirinya.
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
                    <p id="pfIdentitas" class="mt-0.5 text-xs text-slate-500">NIP <?= tampil($d['nip']) ?><?= $nidnTampil !== '' ? ' · NIDN ' . tampil($nidnTampil) : '' ?></p>
                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        <?php if (trim((string)$d['posisi']) !== ''): ?><span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2.5 py-1 text-[10px] font-bold text-orange-700"><i class="fas fa-flask"></i><?= tampil($d['posisi']) ?></span><?php endif; ?>
                        <?php if (count($bidangArr) > 0): ?><span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-bold text-sky-700"><i class="fas fa-microscope"></i>Bidang Ilmu: <span id="badgeBidang"><?= count($bidangArr) ?></span></span><?php endif; ?>
                        <?php if ($eduTerakhir !== ''): ?><span id="badgeEdu" class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2.5 py-1 text-[10px] font-bold text-violet-700"><i class="fas fa-graduation-cap"></i><?= tampil($eduTerakhir) ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="w-full sm:w-auto lg:min-w-[290px]">
                    <div class="rounded-xl bg-slate-50 px-4 py-2">
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-building-columns"></i></span>
                            <div><p class="f-label">Homebase</p><p class="f-value"><?= trim((string)$d['prodi']) !== '' ? tampil($d['prodi']) : '—' ?></p></div>
                        </div>
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-envelope"></i></span>
                            <div><p class="f-label">Email Institusi</p><p class="f-value"><?= trim((string)$d['email']) !== '' ? tampil($d['email']) : '—' ?></p></div>
                        </div>
                        <div class="fact-row">
                            <span class="f-ico"><i class="fas fa-shield-halved"></i></span>
                            <div><p class="f-label">Level Akses</p><p class="f-value"><?= tampil($levelTeks) ?><?= trim((string)$d['tipe_login']) !== '' ? ' · ' . tampil($d['tipe_login']) : '' ?></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Dua Kolom: Data Akademik & Password =====
         items-start: tiap card memakai tinggi sesuai isinya sendiri,
         tidak setara/di-stretch mengikuti card sebelahnya. -->
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:items-start">

        <!-- Data Kepegawaian & Akademik -->
        <div class="reveal rounded-xl border border-slate-200 bg-white shadow-sm" style="animation-delay:.05s">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-microscope mr-1.5 text-orange-500"></i>Data Kepegawaian &amp; Akademik</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Perubahan tersimpan langsung ke database.</p>
                </div>
            </div>
            <form id="formAkademik" method="post" action="/redesain-siatek/administrator/pages/biodata/proses.php" class="space-y-3.5 p-5" novalidate>
                <input type="hidden" name="bidang" id="bidangData" value="">

                <!-- Identitas inti -->
                <div>
                    <label class="form-label" for="inNama">Nama Lengkap (dengan gelar) <span class="text-rose-500">*</span></label>
                    <input type="text" id="inNama" name="nama" value="<?= tampil($d['nama']) ?>" class="form-input">
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="inNip">NIP</label>
                        <input type="text" id="inNip" name="nip" value="<?= tampil($d['nip']) ?>" inputmode="numeric" class="form-input">
                    </div>
                    <div>
                        <label class="form-label" for="inNidn">NIDN</label>
                        <input type="text" id="inNidn" name="nidn" value="<?= tampil($nidnTampil) ?>" inputmode="numeric" class="form-input">
                    </div>
                </div>

                <!-- Bidang Ilmu -->
                <div>
                    <label class="form-label">Bidang Ilmu <span class="font-normal text-slate-400">(bebas &amp; dinamis — ketik untuk menambah)</span></label>
                    <div class="flex gap-1.5">
                        <input type="text" id="bidangInput" list="bidangList" autocomplete="off" placeholder="Ketik atau pilih dari daftar, lalu Enter…" class="form-input flex-1">
                        <datalist id="bidangList"></datalist>
                        <button type="button" id="btnBidangAdd" class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-orange-500 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                            <i class="fas fa-plus"></i>Tambah
                        </button>
                    </div>
                    <div id="bidangTags" class="mt-2 flex flex-wrap gap-1.5"><?php foreach ($bidangArr as $b): ?><span class="bidang-tag" data-nama="<?= tampil($b) ?>"><span class="t-nama"><?= tampil($b) ?></span><button type="button" class="t-x" title="Hapus bidang"><i class="fas fa-times text-[9px]"></i></button></span><?php endforeach; ?></div>
                    <p class="mt-1.5 text-[11px] font-semibold text-slate-400"><span id="bidangCount" class="font-bold text-orange-600"><?= count($bidangArr) ?></span> bidang terdaftar.</p>
                </div>

                <!-- Kepegawaian -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <?php
                    $jabfungGrup = [
                        'Fungsional Dosen' => ['Asisten Ahli','Lektor','Lektor Kepala','Guru Besar'],
                        'Fungsional Tenaga Teknis' => ['Laboran','Pranata Laboratorium','Pranata Komputer'],
                        'Fungsional Umum & Tertentu' => ['Pustakawan','Arsiparis','Analis Kepegawaian','Bendahara','Perencana','Penyuluh'],
                    ];
                    $jabfungCur = trim((string)$d['jabatan_fungsional']);
                    $jabfungAda = false;
                    foreach ($jabfungGrup as $g) if (in_array($jabfungCur, $g, true)) { $jabfungAda = true; break; }
                    ?>
                    <div>
                        <label class="form-label" for="inJabfung">Jabatan Fungsional</label>
                        <select id="inJabfung" name="jabfung" class="form-select">
                            <option value="">— belum diisi —</option>
                            <?php foreach ($jabfungGrup as $grup => $list): ?>
                            <optgroup label="<?= tampil($grup) ?>">
                                <?php foreach ($list as $j): ?><option value="<?= tampil($j) ?>"<?= $jabfungCur === $j ? ' selected' : '' ?>><?= tampil($j) ?></option><?php endforeach; ?>
                            </optgroup>
                            <?php endforeach; ?>
                            <?php if ($jabfungCur !== '' && !$jabfungAda): ?><option value="<?= tampil($jabfungCur) ?>" selected><?= tampil($jabfungCur) ?></option><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="inStatus">Status Kepegawaian</label>
                        <?php $stKpg = (string)$d['status_kepegawaian']; ?>
                        <select id="inStatus" name="status_kepegawaian" class="form-select">
                            <option value="">— belum diisi —</option>
                            <option value="PNS"<?= $stKpg === 'PNS' ? ' selected' : '' ?>>PNS</option>
                            <option value="PPPK"<?= $stKpg === 'PPPK' ? ' selected' : '' ?>>PPPK</option>
                            <option value="Kontrak"<?= $stKpg === 'Kontrak' ? ' selected' : '' ?>>Kontrak</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="inTahun">Tahun Mulai Bertugas</label>
                        <input type="number" id="inTahun" name="tahun_mulai" min="1950" max="2100" value="<?= $d['tahun_mulai_bertugas'] !== null ? (int)$d['tahun_mulai_bertugas'] : '' ?>" class="form-input">
                    </div>
                    <div>
                        <label class="form-label" for="inProdi">Homebase Prodi</label>
                        <input type="text" id="inProdi" name="prodi" value="<?= tampil($d['prodi']) ?>" placeholder="mis. s1teknikelektro" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="inHp">No. HP / WhatsApp</label>
                        <input type="tel" id="inHp" name="no_hp" value="<?= tampil($d['no_telp']) ?>" class="form-input" autocomplete="tel">
                    </div>
                    <div>
                        <label class="form-label" for="inEmail">Email Institusi</label>
                        <input type="email" id="inEmail" name="email" value="<?= tampil($d['email']) ?>" class="form-input" autocomplete="email">
                    </div>
                </div>

                <!-- Riwayat Pendidikan (DB) -->
                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <label class="form-label mb-0">Riwayat Pendidikan</label>
                        <button type="button" id="btnEdu" class="text-[11px] font-bold text-orange-600 hover:text-orange-700"><i class="fas fa-plus mr-0.5"></i>Tambah</button>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-4 py-1.5" id="eduList">
                        <?php if (mysqli_num_rows($resRp) === 0): ?>
                        <p class="py-3 text-center text-xs italic text-slate-400">Belum ada data pendidikan — klik Tambah untuk mengisi.</p>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($resRp)): ?>
                        <div class="edu-row">
                            <span class="e-badge"><?= tampil($r['jenjang']) ?></span>
                            <div class="min-w-0 flex-1"><p class="e-title truncate"><?= tampil($r['program_studi']) ?></p><p class="e-sub"><?= tampil($r['universitas']) ?><?= $r['tahun'] ? ' · ' . (int)$r['tahun'] : '' ?></p></div>
                            <a href="#" data-hapus-pendidikan="<?= (int)$r['id'] ?>" class="edu-del btn-circle bg-slate-200 text-slate-500 hover:bg-rose-100 hover:text-rose-600" title="Hapus"><i class="fas fa-trash text-[10px]"></i><span class="tip">Hapus</span></a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" id="btnSimpanAkademik" name="ubah-biodata" value="1" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                        <i class="fas fa-check"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Ganti Password -->
        <div class="reveal rounded-xl border border-slate-200 bg-white shadow-sm" style="animation-delay:.10s">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800"><i class="fas fa-lock mr-1.5 text-orange-500"></i>Akun &amp; Keamanan</h3>
                    <p class="mt-0.5 text-xs text-slate-400">Username <b><?= tampil($d['nip']) ?></b> · perubahan tersimpan ke database.</p>
                </div>
            </div>
            <form id="formPassword" method="post" action="/redesain-siatek/administrator/pages/biodata/proses.php" class="space-y-3.5 p-5" novalidate>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label" for="inUsername">Username</label>
                        <input type="text" id="inUsername" value="<?= tampil($d['nip']) ?>" readonly class="form-input">
                    </div>
                    <div>
                        <label class="form-label" for="inRole">Role Sistem</label>
                        <input type="text" value="<?= tampil($levelTeks) ?>" readonly class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label" for="inPwLama">Password Saat Ini <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="inPwLama" name="pw_lama" placeholder="Masukkan password sekarang" class="form-input pr-10" autocomplete="current-password" required>
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
                        <span id="pwLabel" class="w-14 text-right text-[10px] font-bold text-slate-400">—</span>
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

<!-- ===== Modal Tambah Riwayat Pendidikan ===== -->
<div class="modal-overlay" id="eduModal" role="dialog" aria-modal="true">
    <form id="formEdu" method="post" action="/redesain-siatek/administrator/pages/biodata/proses.php" class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl" novalidate>
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-sm text-orange-600"><i class="fas fa-graduation-cap"></i></span>
                <div>
                    <h6 class="text-sm font-semibold text-slate-900">Tambah Riwayat Pendidikan</h6>
                    <p class="text-[11px] text-slate-400">Jenjang paling baru ditulis lebih dulu.</p>
                </div>
            </div>
            <button type="button" class="text-xl leading-none text-slate-400 hover:text-slate-700" data-edu-close>&times;</button>
        </div>
        <div class="space-y-3.5 p-5">
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="form-label" for="eduJenjang">Jenjang <span class="text-rose-500">*</span></label>
                    <select id="eduJenjang" name="jenjang" class="form-select" required>
                        <option value="">—</option>
                        <?php foreach (['S3','S2','S1','D4','D3','D2','D1','SMA/SMK','SMP','SD','TK'] as $j): ?><option><?= $j ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="form-label" for="eduProdi">Program Studi <span class="text-rose-500">*</span></label>
                    <input type="text" id="eduProdi" name="prodi_rp" placeholder="Magister Teknik Elektro" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="form-label" for="eduUniv">Universitas <span class="text-rose-500">*</span></label>
                    <input type="text" id="eduUniv" name="univ" placeholder="Universitas Negeri Gorontalo" class="form-input" required>
                </div>
                <div>
                    <label class="form-label" for="eduTahun">Tahun <span class="text-rose-500">*</span></label>
                    <input type="number" id="eduTahun" name="tahun_rp" min="1950" max="2100" placeholder="2016" class="form-input" required>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="rounded-lg bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-300" data-edu-close>Batal</button>
            <button type="submit" id="btnTambahPendidikan" name="tambah-pendidikan" value="1" class="rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600"><i class="fas fa-plus mr-1"></i>Tambahkan</button>
        </div>
    </form>
</div>

<!-- Toast -->
<div id="bioToast"></div>

<script>
(function () {
    var toast = document.getElementById('bioToast');
    function showToast(msg, isErr) {
        toast.innerHTML = '<i class="fas ' + (isErr ? 'fa-circle-exclamation' : 'fa-circle-check') + '"></i>' + msg;
        toast.className = 'show ' + (isErr ? 'bg-rose-600' : 'bg-emerald-600');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(function () { toast.className = ''; }, 2600);
    }

    /* ===== Loading state tombol =====
       Ubah isi tombol jadi spinner + teks "Menyimpan...", matikan klik,
       lalu kembalikan isi semula saat selesai. */
    function setLoading(btn, aktif) {
        if (!btn) return;
        if (aktif) {
            btn.disabled = true;
            btn.dataset.html = btn.innerHTML;
            // tombol ikon-only (btn-circle) cukup spinner; tombol teks pakai + label
            var punyaTeks = /[A-Za-z]/.test(btn.textContent.trim());
            btn.innerHTML = punyaTeks
                ? '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'
                : '<i class="fas fa-spinner fa-spin"></i>';
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        } else {
            btn.disabled = false;
            if (btn.dataset.html) { btn.innerHTML = btn.dataset.html; delete btn.dataset.html; }
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    }

    /* ===== Aksi foto profil: satu tombol -> pilihan Ganti / Hapus ===== */
    var btnFoto = document.getElementById('btnFoto');
    var menuFoto = document.getElementById('menuFoto');
    var inputFoto = document.getElementById('inputFoto');
    var menuGanti = document.getElementById('menuGantiFoto');
    var menuHapus = document.getElementById('menuHapusFoto');

    // bangun ulang avatar jadi SVG inisial (dipakai kalau foto dihapus)
    function avatarSvg() {
        var nama = (document.getElementById('pfNama') || {}).textContent || '?';
        var parts = nama.trim().split(/\s+/);
        var inisial = (parts[0] || '?').charAt(0);
        if (parts.length > 1 && parts[1]) inisial += parts[1].charAt(0);
        inisial = inisial.toUpperCase() || '?';
        return '<svg id="avY" width="88" height="88" viewBox="0 0 88 88" aria-hidden="true">'
            + '<defs><linearGradient id="avYGrad" x1="0" y1="0" x2="88" y2="88" gradientUnits="userSpaceOnUse">'
            + '<stop offset="0" stop-color="#1a365d"/><stop offset="1" stop-color="#f97316"/>'
            + '</linearGradient></defs>'
            + '<rect width="88" height="88" rx="44" fill="url(#avYGrad)"/>'
            + '<text x="44" y="46" fill="#fff" font-family="Inter, sans-serif" font-size="30" font-weight="700" text-anchor="middle" dominant-baseline="central">'
            + inisial + '</text></svg>';
    }

    // tampilkan/sembunyikan menu, beri status aktif untuk opsi hapus
    function setMenuHapus(adaFoto) {
        if (adaFoto) {
            menuHapus.disabled = false;
            menuHapus.className = 'flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-medium text-rose-600 hover:bg-rose-50';
        } else {
            menuHapus.disabled = true;
            menuHapus.className = 'flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-medium text-slate-300 cursor-not-allowed';
        }
    }
    function tutupMenuFoto() { menuFoto.classList.add('hidden'); }

    // klik tombol kamera -> buka/tutup menu pilihan
    btnFoto.addEventListener('click', function (e) {
        e.stopPropagation();
        menuFoto.classList.toggle('hidden');
    });
    // klik di mana pun di luar menu -> tutup
    document.addEventListener('click', function (e) {
        if (!menuFoto.contains(e.target)) tutupMenuFoto();
    });
    menuFoto.addEventListener('click', function (e) { e.stopPropagation(); });

    // pilih "Ganti Foto" -> buka dialog pilih file
    menuGanti.addEventListener('click', function () {
        tutupMenuFoto();
        inputFoto.value = '';
        inputFoto.click();
    });

    // pilih "Hapus Foto" -> AJAX hapus lalu avatar kembali ke SVG inisial
    menuHapus.addEventListener('click', function () {
        tutupMenuFoto();
        if (!window.confirm('Hapus foto profil ini?')) return;

        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: { 'hapus-foto': 1 },
            dataType: 'json',
            success: function (respon) {
                if (respon.status) {
                    document.getElementById('avatarWrap').innerHTML = avatarSvg();
                    setMenuHapus(false);
                    showToast(respon.pesan, false);
                } else {
                    showToast(respon.pesan || 'Gagal menghapus foto', true);
                }
            },
            error: function () { showToast('Terjadi kesalahan pada server', true); }
        });
    });

    // pilih file -> upload foto
    inputFoto.addEventListener('change', function () {
        var file = inputFoto.files && inputFoto.files[0];
        if (!file) return;

        // validasi tipe & ukuran di sisi client sebelum dikirim
        if (!/\.(jpe?g|png|gif|webp)$/i.test(file.name)) {
            showToast('Format file harus JPG/PNG/GIF/WEBP', true);
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran foto maksimal 2 MB', true);
            return;
        }

        var btn = btnFoto;
        btn.disabled = true;

        var fd = new FormData();
        fd.append('foto', file);
        fd.append('unggah-foto', 1);

        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: fd,
            contentType: false, // wajib untuk upload file
            processData: false, // wajib untuk upload file
            dataType: 'json',
            success: function (respon) {
                btn.disabled = false;
                if (respon.status && respon.data && respon.data.foto_url) {
                    // segarkan HANYA konten avatar, tanpa reload halaman
                    var url = respon.data.foto_url;
                    document.getElementById('avatarWrap').innerHTML =
                        '<img src="' + url + '" alt="Foto Profil" class="h-[88px] w-[88px] rounded-full object-cover ring-2 ring-slate-200">';
                    setMenuHapus(true);
                    showToast(respon.pesan, false);
                } else {
                    showToast(respon.pesan || 'Gagal mengunggah foto', true);
                }
            },
            error: function () {
                btn.disabled = false;
                showToast('Terjadi kesalahan pada server', true);
            }
        });
    });

    /* ===== Bidang Ilmu: tag input ===== */
    function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    var BIDANG_PRESET = [
        'Sistem Tenaga Listrik', 'Transmisi dan Distribusi Tenaga Listrik', 'Energi Terbarukan',
        'Kendaraan Listrik', 'Elektronika', 'Elektronika Daya', 'Rangkaian Listrik',
        'Kendali dan Instrumentasi', 'Otomasi Industri', 'Telekomunikasi', 'Pengolahan Sinyal',
        'Robotika', 'Instrumentasi Biomedik', 'Internet of Things (IoT)',
        'Jaringan Komputer', 'Kecerdasan Buatan', 'Machine Learning', 'Data Science',
        'Computer Vision', 'Keamanan Siber', 'Rekayasa Perangkat Lunak', 'Pemrograman Web',
        'Basis Data', 'Sistem Informasi', 'Komputasi Awan', 'Sistem Tertanam (Embedded Systems)',
        'Pengembangan Game'
    ];
    var bidangInput = document.getElementById('bidangInput');
    var bidangTags = document.getElementById('bidangTags');

    function paintBidang() {
        document.getElementById('bidangCount').textContent = bidangTags.children.length;
        var badge = document.getElementById('badgeBidang');
        if (badge) badge.textContent = bidangTags.children.length;
    }
    function hasBidang(nama) {
        return !!bidangTags.querySelector('.bidang-tag[data-nama="' + nama.replace(/"/g, '\\"') + '"]');
    }
    function addBidang(nama) {
        nama = (nama || '').trim().replace(/\s+/g, ' ');
        if (!nama) { showToast('Nama bidang ilmu masih kosong', true); return false; }
        if (hasBidang(nama)) { showToast('Bidang "' + nama + '" sudah terdaftar', true); return false; }
        var t = document.createElement('span');
        t.className = 'bidang-tag';
        t.setAttribute('data-nama', nama);
        t.innerHTML = '<span class="t-nama">' + esc(nama) + '</span>'
            + '<button type="button" class="t-x" title="Hapus bidang"><i class="fas fa-times text-[9px]"></i></button>';
        bidangTags.appendChild(t);
        paintBidang();
        return true;
    }
    function tryAddBidang() {
        if (addBidang(bidangInput.value)) bidangInput.value = '';
        bidangInput.focus();
    }
    document.getElementById('btnBidangAdd').addEventListener('click', tryAddBidang);
    bidangInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); tryAddBidang(); }
    });
    bidangTags.addEventListener('click', function (e) {
        var x = e.target.closest('.t-x');
        if (!x) return;
        x.closest('.bidang-tag').remove();
        paintBidang();
    });
    BIDANG_PRESET.slice().sort(function (a, b) { return a.localeCompare(b); }).forEach(function (n) {
        var o = document.createElement('option'); o.value = n;
        document.getElementById('bidangList').appendChild(o);
    });

    /* ===== Kirim tag bidang + simpan biodata lewat AJAX ===== */
    document.getElementById('formAkademik').addEventListener('submit', function (e) {
        e.preventDefault();
        var nama = document.getElementById('inNama').value.trim();
        if (!nama) { showToast('Nama lengkap wajib diisi', true); return; }
        if (bidangTags.children.length === 0) { showToast('Tambahkan minimal satu bidang ilmu', true); return; }
        var vals = [];
        Array.prototype.forEach.call(bidangTags.children, function (t) { vals.push(t.getAttribute('data-nama')); });
        var form = e.target;
        var data = new FormData(form);
        data.set('bidang', vals.join('|'));
        data.set('ubah-biodata', '1');

        var btnSimpan = document.getElementById('btnSimpanAkademik');
        setLoading(btnSimpan, true);

        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (respon) {
                setLoading(btnSimpan, false);
                if (respon.status) {
                    showToast(respon.pesan, false);
                    // update nama & badge bidang di kartu profil
                    document.getElementById('pfNama').textContent = respon.data.nama;
                    var bd = respon.data.bidang || [];
                    var badge = document.getElementById('badgeBidang');
                    if (badge) badge.textContent = bd.length;
                } else {
                    showToast(respon.pesan, true);
                }
            },
            error: function () { setLoading(btnSimpan, false); showToast('Terjadi kesalahan pada server', true); }
        });
    });

    /* ===== Modal riwayat pendidikan ===== */
    var eduModal = document.getElementById('eduModal');
    document.getElementById('btnEdu').addEventListener('click', function () {
        document.getElementById('eduJenjang').value = '';
        document.getElementById('eduProdi').value = '';
        document.getElementById('eduUniv').value = '';
        document.getElementById('eduTahun').value = '';
        eduModal.classList.add('show');
        document.getElementById('eduProdi').focus();
    });
    eduModal.querySelectorAll('[data-edu-close]').forEach(function (b) {
        b.addEventListener('click', function () { eduModal.classList.remove('show'); });
    });
    eduModal.addEventListener('click', function (e) { if (e.target === eduModal) eduModal.classList.remove('show'); });

    /* ===== Reload konten daftar riwayat pendidikan (hanya bagian itu) ===== */
    function muatUlangPendidikan() {
        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: { 'detail-pendidikan': 1 },
            dataType: 'json',
            success: function (respon) {
                if (respon.status) {
                    $('#eduList').html(respon.data);
                    pasangHapusPendidikan();
                    // perbarui badge jenjang terakhir di kartu profil
                    var rows = $('#eduList .e-badge').map(function () { return this.textContent.trim(); }).get();
                    var rank = { TK:0, SD:1, 'SMA/SMK':2, D1:3, D2:4, D3:5, D4:6, S1:7, S2:8, S3:9 };
                    rows.sort(function (a, b) { return (rank[b] ?? -1) - (rank[a] ?? -1); });
                    var badgeEdu = document.getElementById('badgeEdu');
                    if (badgeEdu) badgeEdu.textContent = rows[0] || '';
                }
            },
            error: function () { showToast('Gagal memuat riwayat pendidikan', true); }
        });
    }

    /* ===== Tambah riwayat pendidikan (AJAX) ===== */
    document.getElementById('formEdu').addEventListener('submit', function (e) {
        e.preventDefault();
        var btnTambah = document.getElementById('btnTambahPendidikan');
        setLoading(btnTambah, true);
        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: $(this).serialize() + '&tambah-pendidikan=1',
            dataType: 'json',
            success: function (respon) {
                setLoading(btnTambah, false);
                if (respon.status) {
                    showToast(respon.pesan, false);
                    eduModal.classList.remove('show');
                    muatUlangPendidikan();
                } else {
                    showToast(respon.pesan, true);
                }
            },
            error: function () { setLoading(btnTambah, false); showToast('Terjadi kesalahan pada server', true); }
        });
    });

    /* ===== Hapus riwayat pendidikan (AJAX) ===== */
    function pasangHapusPendidikan() {
        $('#eduList [data-hapus-pendidikan]').off('click').on('click', function (e) {
            e.preventDefault();
            if (!confirm('Hapus riwayat pendidikan ini?')) return;
            var id = $(this).data('hapus-pendidikan');
            var btn = this;
            setLoading(btn, true);
            $.ajax({
                url: '/redesain-siatek/administrator/pages/biodata/proses.php?hapus-pendidikan=' + id,
                method: 'GET',
                dataType: 'json',
                success: function (respon) {
                    setLoading(btn, false);
                    if (respon.status) {
                        showToast(respon.pesan, false);
                        muatUlangPendidikan();
                    } else {
                        showToast(respon.pesan, true);
                    }
                },
                error: function () { setLoading(btn, false); showToast('Terjadi kesalahan pada server', true); }
            });
        });
    }
    pasangHapusPendidikan();

    /* ===== Show/hide password ===== */
    document.querySelectorAll('[data-toggle-pw]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = document.getElementById(btn.getAttribute('data-toggle-pw'));
            var show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            btn.innerHTML = '<i class="fas ' + (show ? 'fa-eye-slash' : 'fa-eye') + ' text-xs"></i>';
        });
    });

    /* ===== Kekuatan password ===== */
    var pwBaru = document.getElementById('inPwBaru');
    var pwKonf = document.getElementById('inPwKonfirmasi');
    var pwBar = document.getElementById('pwBar');
    var pwLabel = document.getElementById('pwLabel');

    function rules(v) {
        return { len: v.length >= 8, upper: /[A-Z]/.test(v), lower: /[a-z]/.test(v), digit: /[0-9]/.test(v) };
    }
    function paintStrength() {
        var v = pwBaru.value;
        var r = rules(v);
        Object.keys(r).forEach(function (k) {
            var el = document.querySelector('.pw-check[data-rule="' + k + '"]');
            if (!el) return;
            el.classList.toggle('ok', !!v && r[k]);
            el.querySelector('i').className = r[k] ? 'fas fa-circle-check' : 'fas fa-circle';
        });
        var score = (!v ? 0 : [r.len, r.upper, r.lower, r.digit].filter(Boolean).length);
        var conf = [
            { w: '0%', c: 'bg-slate-200', t: '—', tc: 'text-slate-400' },
            { w: '25%', c: 'bg-rose-400', t: 'Lemah', tc: 'text-rose-500' },
            { w: '50%', c: 'bg-orange-400', t: 'Cukup', tc: 'text-orange-500' },
            { w: '75%', c: 'bg-amber-400', t: 'Bagus', tc: 'text-amber-500' },
            { w: '100%', c: 'bg-emerald-500', t: 'Kuat', tc: 'text-emerald-600' }
        ][score];
        pwBar.style.width = conf.w;
        pwBar.className = 'h-full rounded-full transition-all duration-300 ' + conf.c;
        pwLabel.textContent = conf.t;
        pwLabel.className = 'w-14 text-right text-[10px] font-bold ' + conf.tc;
        document.getElementById('pwMatchInfo').classList.toggle('hidden', !(pwKonf.value.length > 0 && pwKonf.value !== v));
    }
    pwBaru.addEventListener('input', paintStrength);
    pwKonf.addEventListener('input', paintStrength);
    paintStrength();

    /* Kirim ganti password lewat AJAX */
    document.getElementById('formPassword').addEventListener('submit', function (e) {
        e.preventDefault();
        if (pwBaru.value !== pwKonf.value) { showToast('Konfirmasi password belum sama', true); return; }
        var btnPw = document.getElementById('btnUbahPassword');
        setLoading(btnPw, true);
        $.ajax({
            url: '/redesain-siatek/administrator/pages/biodata/proses.php',
            method: 'POST',
            data: $(this).serialize() + '&ubah-password=1',
            dataType: 'json',
            success: function (respon) {
                setLoading(btnPw, false);
                if (respon.status) {
                    showToast(respon.pesan, false);
                    document.getElementById('inPwLama').value = '';
                    document.getElementById('inPwBaru').value = '';
                    document.getElementById('inPwKonfirmasi').value = '';
                    paintStrength();
                } else {
                    showToast(respon.pesan, true);
                }
            },
            error: function () { setLoading(btnPw, false); showToast('Terjadi kesalahan pada server', true); }
        });
    });
})();
</script>
