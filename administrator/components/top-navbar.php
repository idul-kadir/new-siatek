<?php
/**
 * Top Navbar — Statis (Tailwind)
 * Variabel: $pageTitle (string) — judul halaman di navbar
 *
 * Avatar: SVG inline deterministik (inisial nama + gradient navy→accent),
 *         tanpa dependency ke service pihak ketiga (ui-avatars.com).
 */
$pageTitle = $pageTitle ?? 'Beranda';

/* ===== Identitas pengguna aktif (dari session login) ===== */
$nmUser = trim((string)($_SESSION['nama'] ?? '')) !== '' ? $_SESSION['nama'] : 'Pengguna';
$tipeU  = strtolower(trim((string)($_SESSION['tipe_login'] ?? '')));
$levelU = (string)($_SESSION['level'] ?? '');
$roleTeks = ($tipeU === 'superadmin' || $levelU === '0')
    ? 'Super Admin'
    : ($tipeU === 'admin' ? 'Admin' : 'Dosen');

/* ===== Fungsi bersama (upload_file, url_file, dll) ===== */
require_once __DIR__ . '/../fungsi.php';

/* ===== Foto profil: prefer session, fallback query DB =====
   Kolom profil (dosen) / foto (mahasiswa) menyimpan id file di tabel files.
   Dipakai url_file() untuk dirubah jadi URL nyata. */
$fotoProfil = (string)($_SESSION['profil'] ?? '');
if (trim($fotoProfil) === '' && !empty($_SESSION['peran'])) {
    // ambil dari DB agar user yang belum re-login tetap dapat fotonya
    $idRef = $_SESSION['nip'] ?? $_SESSION['nim'] ?? '';
    if ($idRef !== '') {
        if ($_SESSION['peran'] === 'staf') {
            $kol = 'profil'; $tab = 'dosen'; $keyCol = 'nip';
        } else {
            $kol = 'foto';   $tab = 'mahasiswa'; $keyCol = 'nim';
        }
        $q = mysqli_query($GLOBALS['koneksi'], "SELECT `$kol` AS f FROM `$tab` WHERE `$keyCol` = '" . mysqli_real_escape_string($GLOBALS['koneksi'], $idRef) . "' LIMIT 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        if ($r && trim((string)$r['f']) !== '') {
            $fotoProfil = $r['f'];
            $_SESSION['profil'] = $fotoProfil;
        }
    }
}

/**
 * Render avatar: foto profil jika ada, SVG inisial jika tidak.
 *
 * @param string $name  Nama user (akan di-trim ke 2 karakter awal).
 * @param int    $size  Ukuran pixel (square).
 * @param string $foto  URL/path foto profil (kosong = SVG inisial).
 * @return string       Markup <img> atau <svg>.
 */
function navbar_avatar(string $name, int $size = 32, string $foto = ''): string {
    // jika ada foto profil, tampilkan sebagai img
    $foto = trim($foto);
    if ($foto !== '') {
        $s = (int) $size;
        $src = htmlspecialchars($foto, ENT_QUOTES, 'UTF-8');
        return '<img src="' . $src . '" alt="Avatar" width="' . $s . '" height="' . $s . '" class="rounded-full object-cover" style="width:' . $s . 'px;height:' . $s . 'px;">';
    }

    // fallback: SVG inisial nama
    $name = trim($name);
    if ($name === '') $name = '?';
    $parts = preg_split('/\s+/', $name);
    $initial = mb_strtoupper(mb_substr($parts[0], 0, 1));
    if (isset($parts[1]) && $parts[1] !== '') {
        $initial .= mb_strtoupper(mb_substr($parts[1], 0, 1));
    } else {
        $initial = mb_strtoupper(mb_substr($parts[0], 0, 2));
    }
    $initial = htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
    $id = 'av_' . substr(md5($name . $size), 0, 8);
    $s  = (int) $size;
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$s}" height="{$s}" viewBox="0 0 32 32" aria-hidden="true">
  <defs>
    <linearGradient id="{$id}" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
      <stop offset="0" stop-color="#1a365d"/>
      <stop offset="1" stop-color="#f97316"/>
    </linearGradient>
  </defs>
  <rect width="32" height="32" rx="16" fill="url(#{$id})"/>
  <text x="16" y="16" fill="#fff" font-family="Inter, system-ui, sans-serif" font-size="12" font-weight="700" text-anchor="middle" dominant-baseline="central" letter-spacing="0.5">{$initial}</text>
</svg>
SVG;
}
?>

<header class="bg-white h-16 border-b border-slate-200 shadow-sm flex items-center justify-between px-6 sticky top-0 z-10 flex-shrink-0">

    <!-- Left: toggle sidebar + hamburger + judul -->
    <div class="flex items-center gap-3 min-w-0">
        <button id="sidebarToggle" class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 active:bg-slate-300 transition flex-shrink-0" aria-label="Buka menu" title="Buka menu">
            <i id="sidebarToggleIcon" class="fa-solid fa-bars-staggered"></i>
        </button>
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-semibold text-slate-900 tracking-tight truncate"><?= htmlspecialchars($pageTitle) ?></h2>
            <p class="text-xs text-slate-500 hidden sm:block">Sistem Informasi Administrasi Teknik Elektro dan Komputer</p>
        </div>
    </div>

    <!-- Right: user -->
    <div class="flex items-center gap-3">
        <!-- Divider vertikal halus -->
        <span class="hidden md:block w-px h-8 bg-slate-200 mx-1"></span>
        <button type="button" class="flex items-center gap-3 px-2 py-1 rounded-lg hover:bg-slate-50 transition" aria-label="Menu pengguna">
            <?php
            // $fotoProfil berisi id file di tabel files -> ubah jadi URL file asli
            if (trim($fotoProfil) !== '') {
                $fotoSrc = url_file($fotoProfil);
            } else {
                $fotoSrc = '';
            }
            ?>
            <?= navbar_avatar($nmUser, 32, $fotoSrc) ?>
            <div class="hidden md:block leading-tight text-left">
                <div class="flex items-center gap-1.5">
                    <p class="text-sm font-medium text-slate-900"><?= htmlspecialchars($nmUser) ?></p>
                    <span class="relative flex h-2 w-2" title="Online">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </div>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($roleTeks) ?></p>
            </div>
            <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden md:block"></i>
        </button>
    </div>
</header>
