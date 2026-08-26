<?php
/**
 * Top Navbar — Statis (Tailwind)
 * Variabel: $pageTitle (string) — judul halaman di navbar
 *
 * Avatar: SVG inline deterministik (inisial nama + gradient navy→accent),
 *         tanpa dependency ke service pihak ketiga (ui-avatars.com).
 */
$pageTitle = $pageTitle ?? 'Beranda';

/**
 * Render avatar SVG inline (32×32 default).
 * Gradient: navy-700 → accent-500. Initial uppercase diambil dari nama.
 *
 * @param string $name  Nama user (akan di-trim ke 2 karakter awal).
 * @param int    $size  Ukuran pixel (square).
 * @return string       Markup <svg>.
 */
function navbar_avatar(string $name, int $size = 32): string {
    $name = trim($name);
    if ($name === '') $name = '?';
    // Ambil inisial: huruf pertama dari kata pertama + huruf pertama dari kata kedua (kalau ada)
    $parts = preg_split('/\s+/', $name);
    $initial = mb_strtoupper(mb_substr($parts[0], 0, 1));
    if (isset($parts[1]) && $parts[1] !== '') {
        $initial .= mb_strtoupper(mb_substr($parts[1], 0, 1));
    } else {
        // kalau single word, pakai 2 huruf pertama
        $initial = mb_strtoupper(mb_substr($parts[0], 0, 2));
    }
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
            <?= navbar_avatar('Admin Dept', 32) ?>
            <div class="hidden md:block leading-tight text-left">
                <div class="flex items-center gap-1.5">
                    <p class="text-sm font-medium text-slate-900">Admin Dept</p>
                    <span class="relative flex h-2 w-2" title="Online">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </div>
                <p class="text-xs text-slate-500">Super Admin</p>
            </div>
            <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden md:block"></i>
        </button>
    </div>
</header>
