<?php
/**
 * Top Navbar — Statis (Tailwind)
 * Variabel: $pageTitle (string) — judul halaman di navbar
 */
$pageTitle = $pageTitle ?? 'Beranda';
?>

<header class="bg-white h-16 border-b border-slate-200 flex items-center justify-between px-6 sticky top-0 z-10 flex-shrink-0">

    <!-- Left: hamburger + judul -->
    <div class="flex items-center gap-3 min-w-0">
        <button id="hamburgerBtn" class="md:hidden text-slate-600 hover:text-slate-900 text-xl leading-none px-2" aria-label="Buka menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="min-w-0">
            <h2 class="text-base sm:text-lg font-semibold text-slate-900 tracking-tight truncate"><?= htmlspecialchars($pageTitle) ?></h2>
            <p class="text-xs text-slate-500 hidden sm:block">Sistem Informasi Administrasi Teknik Elektro dan Komputer</p>
        </div>
    </div>

    <!-- Right: user -->
    <div class="flex items-center gap-3 cursor-pointer">
        <img src="https://ui-avatars.com/api/?background=f97316&color=fff&name=A" alt="User"
             class="w-8 h-8 rounded-full">
        <div class="hidden md:block leading-tight">
            <p class="text-sm font-medium text-slate-900">Admin Dept</p>
            <p class="text-xs text-slate-500">Super Admin</p>
        </div>
    </div>
</header>
