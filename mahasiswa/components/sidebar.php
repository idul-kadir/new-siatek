<?php
/**
 * Sidebar Navigation - Portal Mahasiswa (Tailwind)
 */
$activePage = $activePage ?? '';
?>

<!-- Sidebar Overlay (mobile) -->
<div class="modal-overlay md:!flex md:!hidden" id="sidebarOverlay" style="background:rgba(15,23,42,0.5); z-index:1040; padding:0;"></div>

<aside id="sidebar" class="sidebar-bg w-64 flex-shrink-0 flex-col overflow-y-auto sidebar-scroll hidden md:flex md:relative md:translate-x-0 z-20">

    <!-- Brand -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-white/10 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-accent-500 rounded-lg flex items-center justify-center text-white text-lg transition-transform hover:scale-105">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="font-semibold text-white text-base leading-tight tracking-wide">SIATEK</h1>
                <p class="text-xs text-slate-400 tracking-wide">Portal Mahasiswa</p>
            </div>
        </div>
        <button class="md:hidden text-slate-400 hover:text-white text-xl leading-none" id="sidebarCloseBtn" aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-3 space-y-0.5 px-2">

        <!-- 1. Beranda -->
        <a href="/redesain-siatek/mahasiswa/beranda" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'beranda' ? 'nav-active' : '' ?>">
            <i class="fas fa-home w-5 text-center text-base"></i>
            <span class="flex-1">Beranda</span>
        </a>

        <!-- 2. Skripsi -->
        <a href="/redesain-siatek/mahasiswa/skripsi" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'skripsi' ? 'nav-active' : '' ?>">
            <i class="fas fa-book w-5 text-center text-base"></i>
            <span class="flex-1">Skripsi</span>
        </a>

        <!-- 3. Kerja Praktek -->
        <a href="/redesain-siatek/mahasiswa/kp" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'kp' ? 'nav-active' : '' ?>">
            <i class="fas fa-briefcase w-5 text-center text-base"></i>
            <span class="flex-1">Kerja Praktek</span>
        </a>

        <!-- 4. Biodata -->
        <a href="/redesain-siatek/mahasiswa/biodata" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'biodata' ? 'nav-active' : '' ?>">
            <i class="fas fa-user-circle w-5 text-center text-base"></i>
            <span class="flex-1">Biodata</span>
        </a>

        <!-- 5. Arsip -->
        <a href="/redesain-siatek/mahasiswa/arsip" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition <?= $activePage === 'arsip' ? 'nav-active' : '' ?>">
            <i class="fas fa-archive w-5 text-center text-base"></i>
            <span class="flex-1">Arsip</span>
        </a>

    </nav>

    <!-- Footer (logout) -->
    <div class="border-t border-white/10 p-2 flex-shrink-0">
        <a href="/redesain-siatek/?keluar=1" class="nav-item flex items-center space-x-3 px-4 py-2.5 rounded-r-lg text-sm font-normal transition text-red-300 hover:!text-red-200">
            <i class="fas fa-sign-out-alt w-5 text-center text-base"></i>
            <span class="flex-1">Logout</span>
        </a>
    </div>
</aside>

<style>
@media (min-width: 768px) {
    #sidebar { display: flex !important; transform: translateX(0) !important; position: static !important; height: 100vh; }
    #sidebarOverlay { display: none !important; }
}
</style>