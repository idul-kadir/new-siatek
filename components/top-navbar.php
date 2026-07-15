<?php
/**
 * Top Navbar — Statis
 * Variabel: $pageTitle (string) — judul halaman di navbar
 */

$pageTitle = $pageTitle ?? 'Beranda';
?>

<header class="top-navbar">
    <button class="btn hamburger-btn" id="hamburgerBtn">
        <i class="bi bi-list fs-4"></i>
    </button>
    <div class="navbar-title">
        <h1 class="h5 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
        <small class="text-muted d-none d-sm-block">Sistem Informasi Akademik Teknik Elektro &amp; Komputer</small>
    </div>
    <div class="navbar-actions d-flex align-items-center gap-2 gap-sm-3">
        <button class="btn btn-icon position-relative" title="Notifikasi">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-badge">3</span>
        </button>
        <div class="user-profile d-flex align-items-center gap-2">
            <img src="https://ui-avatars.com/api/?name=Ridwan+Kadir&background=0d6efd&color=fff&size=36" alt="Avatar" class="avatar-img">
            <div class="user-info d-none d-sm-block">
                <span class="user-name fw-semibold d-block" style="font-size:.85rem;">Ridwan Kadir</span>
                <small class="text-muted" style="font-size:.7rem;">Administrator</small>
            </div>
            <i class="bi bi-chevron-down text-muted d-none d-sm-block" style="font-size:.7rem;"></i>
        </div>
    </div>
</header>