<?php
/**
 * Halaman Beranda — Dasbor Utama SIATEK
 *
 * Mengimplementasikan arsitektur Multi-Peran Adaptif:
 *   - Admin / Pimpinan Jurusan & Kaprodi (Level 9, 6, 5) -> beranda-admin.php
 *   - Dosen Pengajar & Pembimbing (Level 1)               -> beranda-dosen.php
 *   - Operator Program Studi & Jurusan (Level 3, 4)      -> beranda-operator.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../koneksi.php';
if (!function_exists('query')) {
    require_once __DIR__ . '/../../../fungsi.php';
}

/* ================================================================
   IDENTIFIKASI PERAN & MODE TAMPILAN
   ================================================================ */
$userLevel = (int)($_SESSION['level'] ?? 0);
$tipeLogin = strtolower(trim((string)($_SESSION['tipe_login'] ?? '')));
$userProdi = function_exists('get_prodi_user') ? get_prodi_user() : trim((string)($_SESSION['prodi'] ?? ''));
$nmUser    = trim((string)($_SESSION['nama'] ?? '')) !== '' ? $_SESSION['nama'] : 'Pengguna';

// Label peran & Default role view
if ($tipeLogin === 'superadmin' || $userLevel === 9) {
    $roleTeks = 'Super Admin';
    $defaultRoleView = 'superadmin';
} elseif ($userLevel === 6) {
    $roleTeks = 'Pengelola Jurusan';
    // Cek di tabel jurusan: Ketua Jurusan (kajur) atau Sekretaris Jurusan (sekjur)
    $idSession  = bersihkan((string)($_SESSION['id'] ?? ''));
    $nipSession = bersihkan((string)($_SESSION['nip'] ?? $_SESSION['user_id'] ?? ''));
    $qJur = query("
        SELECT j.kajur, j.sekjur,
               d1.nip AS nip_kajur, d2.nip AS nip_sekjur
        FROM jurusan j
        LEFT JOIN dosen d1 ON j.kajur = d1.id
        LEFT JOIN dosen d2 ON j.sekjur = d2.id
        LIMIT 1
    ");
    if ($qJur && ($rJur = mysqli_fetch_assoc($qJur))) {
        $isKajur = ($idSession !== '' && (string)$rJur['kajur'] === $idSession)
                || ($nipSession !== '' && ((string)$rJur['kajur'] === $nipSession || (string)($rJur['nip_kajur'] ?? '') === $nipSession));
        $isSekjur = ($idSession !== '' && (string)$rJur['sekjur'] === $idSession)
                || ($nipSession !== '' && ((string)$rJur['sekjur'] === $nipSession || (string)($rJur['nip_sekjur'] ?? '') === $nipSession));

        if ($isKajur) {
            $roleTeks = 'Ketua Jurusan';
        } elseif ($isSekjur) {
            $roleTeks = 'Sekretaris Jurusan';
        } else {
            $roleTeks = 'Pengelola Jurusan';
        }
    }
    $defaultRoleView = 'jurusan';
} elseif ($userLevel === 5) {
    $roleTeks = 'Ketua Program Studi (Kaprodi)';
    $defaultRoleView = 'prodi';
} elseif ($userLevel === 3 || $userLevel === 4) {
    $roleTeks = 'Operator Akademik';
    $defaultRoleView = 'operator';
} else {
    $roleTeks = 'Dosen Pengajar & Pembimbing';
    $defaultRoleView = 'dosen';
}

// Pratinjau Super Admin (Level 9) & Mode Ganda Pengelola Jurusan (Level 6):
$requestedView = strtolower(trim((string)($_GET['view'] ?? '')));
$validViews    = ['superadmin', 'jurusan', 'prodi', 'operator', 'dosen', 'admin'];
if ($userLevel === 9 && in_array($requestedView, $validViews, true)) {
    // Backward compatibility: jika param 'admin', arahkan ke jurusan
    $activeRoleView = ($requestedView === 'admin') ? 'jurusan' : $requestedView;
} elseif ($userLevel === 6) {
    // Pengelola Jurusan (Kajur / Sekjur) dapat beralih antara Mode Jurusan dan Mode Dosen
    if (in_array($requestedView, ['jurusan', 'dosen'], true)) {
        $_SESSION['beranda_view_level6'] = $requestedView;
    }
    $activeRoleView = $_SESSION['beranda_view_level6'] ?? $defaultRoleView;
} else {
    $activeRoleView = $defaultRoleView;
}

/* ================================================================
   HELPERS & FORMATTING
   ================================================================ */
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$today_label = $hari[(int)date('w')] . ', ' . (int)date('d') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');

if (!function_exists('f')) {
    function f($n){ return number_format((int)$n, 0, ',', '.'); }
}

if (!function_exists('spark_path')) {
    function spark_path(array $vals, int $w = 120, int $h = 36): string {
        $n = count($vals);
        if ($n === 0) return '';
        $max = max(1, max($vals));
        $min = min($vals);
        $span = max(1, $max - $min);
        $pts = [];
        foreach ($vals as $i => $v) {
            $x = $n === 1 ? $w / 2 : ($i / ($n - 1)) * $w;
            $y = $h - (($v - $min) / $span) * ($h - 4) - 2;
            $pts[] = round($x, 1) . ',' . round($y, 1);
        }
        return implode(' ', $pts);
    }
}

if (!function_exists('beranda_pick_color')) {
    function beranda_pick_color(string $nama): string {
        $n = strtolower($nama);
        if (strpos($n, 'komputer') !== false) return '#f97316';   // Orange SIATEK (Teknik Komputer)
        if (strpos($n, 'rekayasa') !== false || strpos($n, 'vokasional') !== false || strpos($n, 'pendidikan') !== false) {
            return '#94a3b8';                                      // Slate SIATEK (Pend. Vokasional Rekayasa Elektro)
        }
        if (strpos($n, 'elektro')  !== false) return '#1a365d';   // Navy SIATEK (Teknik Elektro)
        return '#64748b';                                          // Default Slate
    }
}
?>

<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<main class="content-area content-scroll">
    <div class="space-y-5 max-w-full">

        <!-- ============ 5 ROLE VIEW SWITCHER (KHUSUS SUPER ADMIN) ============ -->
        <?php if ($userLevel === 9): ?>
        <section class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 px-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2.5 text-xs text-slate-700">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs font-bold">
                    <i class="fas fa-eye"></i>
                </span>
                <span>Mode Pratinjau Super Admin: melihat tampilan <strong><?= htmlspecialchars(strtoupper($activeRoleView)) ?></strong></span>
            </div>
            <div class="inline-flex rounded-lg bg-slate-100 p-1 text-xs gap-1 flex-wrap">
                <a href="/redesain-siatek/administrator/beranda?view=superadmin" 
                   class="px-2.5 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'superadmin' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-screwdriver-wrench mr-1"></i> Superadmin (9)
                </a>
                <a href="/redesain-siatek/administrator/beranda?view=jurusan" 
                   class="px-2.5 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'jurusan' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-building-columns mr-1"></i> Jurusan (6)
                </a>
                <a href="/redesain-siatek/administrator/beranda?view=prodi" 
                   class="px-2.5 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'prodi' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-user-tie mr-1"></i> Kaprodi (5)
                </a>
                <a href="/redesain-siatek/administrator/beranda?view=operator" 
                   class="px-2.5 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'operator' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-desktop mr-1"></i> Operator (3,4)
                </a>
                <a href="/redesain-siatek/administrator/beranda?view=dosen" 
                   class="px-2.5 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'dosen' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-chalkboard-user mr-1"></i> Dosen (1)
                </a>
            </div>
        </section>
        <?php endif; ?>

        <!-- ============ DUAL-MODE SWITCHER (KHUSUS PENGELOLA JURUSAN LEVEL 6) ============ -->
        <?php if ($userLevel === 6): ?>
        <section class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 px-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-2.5 text-xs text-slate-700">
                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-xs font-bold">
                    <i class="fas fa-arrows-rotate"></i>
                </span>
                <span>Mode Tampilan: <strong class="text-slate-900"><?= $activeRoleView === 'dosen' ? 'Mode Dosen (Pengajar & Pembimbing)' : 'Mode ' . htmlspecialchars($roleTeks) ?></strong></span>
            </div>
            <div class="inline-flex rounded-lg bg-slate-100 p-1 text-xs gap-1">
                <a href="/redesain-siatek/administrator/beranda?view=jurusan" 
                   class="px-3 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'jurusan' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-building-columns mr-1.5"></i> Mode Jurusan
                </a>
                <a href="/redesain-siatek/administrator/beranda?view=dosen" 
                   class="px-3 py-1.5 rounded-md font-medium transition <?= $activeRoleView === 'dosen' ? 'bg-[#1a365d] text-white shadow-xs font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' ?>">
                    <i class="fas fa-chalkboard-user mr-1.5"></i> Mode Dosen
                </a>
            </div>
        </section>
        <?php endif; ?>

        <!-- ============ DISPATCH 5 SUB-VIEW BERDASARKAN ROLE ============ -->
        <?php
        switch ($activeRoleView) {
            case 'superadmin':
                require __DIR__ . '/beranda-superadmin.php';
                break;
            case 'jurusan':
                require __DIR__ . '/beranda-jurusan.php';
                break;
            case 'prodi':
                require __DIR__ . '/beranda-prodi.php';
                break;
            case 'operator':
                require __DIR__ . '/beranda-operator.php';
                break;
            case 'dosen':
            default:
                require __DIR__ . '/beranda-dosen.php';
                break;
        }
        ?>

    </div>
</main>
