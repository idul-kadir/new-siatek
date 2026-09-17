<?php
/**
 * SCRIPT RESET PASSWORD - SIATEK
 * 
 * Cara pakai:
 *   1. Upload file ini ke folder /redesain-siatek/login/
 *   2. Akses via browser: http://localhost/redesain-siatek/login/reset_password.php
 *   3. Masukkan NIP/NIM dan password baru
 *   4. Hapus file ini setelah selesai (penting untuk keamanan!)
 * 
 * CATATAN: Script ini hanya untuk emergency reset. Setelah selesai, HAPUS file ini.
 */

// Load koneksi database
require_once __DIR__ . '/../fungsi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$pesan = '';
$jenis = ''; // 'success' atau 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip_nim = trim($_POST['nip_nim'] ?? '');
    $pw_baru = trim($_POST['password_baru'] ?? '');
    $pw_konf = trim($_POST['password_konfirmasi'] ?? '');

    if ($nip_nim === '' || $pw_baru === '') {
        $pesan = 'NIP/NIM dan password wajib diisi';
        $jenis = 'error';
    } elseif (strlen($pw_baru) < 8) {
        $pesan = 'Password baru minimal 8 karakter';
        $jenis = 'error';
    } elseif (!preg_match('/[A-Z]/', $pw_baru) || !preg_match('/[a-z]/', $pw_baru) || !preg_match('/[0-9]/', $pw_baru)) {
        $pesan = 'Password harus mengandung huruf besar, kecil, dan angka';
        $jenis = 'error';
    } elseif ($pw_baru !== $pw_konf) {
        $pesan = 'Konfirmasi password tidak cocok';
        $jenis = 'error';
    } else {
        // Cari di tabel dosen
        $nip_esc = mysqli_real_escape_string($koneksi, $nip_nim);
        
        // Cek dosen
        $row = mysqli_fetch_assoc(query("SELECT id, nip FROM dosen WHERE nip = '$nip_esc' LIMIT 1"));
        
        if ($row) {
            // User adalah dosen/staf
            $hash = password_hash($pw_baru, PASSWORD_DEFAULT);
            query("UPDATE dosen SET password = '$hash' WHERE nip = '$nip_esc'");
            $pesan = "Password untuk NIP {$nip_nim} (Dosen) berhasil direset!";
            $jenis = 'success';
        } else {
            // Cek mahasiswa
            $row = mysqli_fetch_assoc(query("SELECT nim FROM mahasiswa WHERE nim = '$nip_esc' LIMIT 1"));
            
            if ($row) {
                // User adalah mahasiswa
                $hash = password_hash($pw_baru, PASSWORD_DEFAULT);
                query("UPDATE mahasiswa SET password = '$hash' WHERE nim = '$nip_esc'");
                $pesan = "Password untuk NIM {$nip_nim} (Mahasiswa) berhasil direset!";
                $jenis = 'success';
            } else {
                $pesan = "NIP/NIM '{$nip_nim}' tidak ditemukan di database";
                $jenis = 'error';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - SIATEK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: "Inter", sans-serif; }
        body { background-color: #f1f5f9; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Warning Box -->
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Penting: Hapus Setelah Selesai</p>
                    <p class="text-xs text-amber-700 mt-1">
                        File ini harus dihapus segera setelah password berhasil direset. 
                        Script ini memberikan akses penuh ke database password.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
            <div class="text-center mb-6">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-600 mb-3">
                    <i class="fas fa-key text-lg"></i>
                </div>
                <h1 class="text-xl font-bold text-slate-800">Reset Password</h1>
                <p class="text-sm text-slate-500 mt-1">Masukkan NIP/NIM dan password baru</p>
            </div>

            <?php if ($pesan): ?>
                <div class="mb-5 rounded-xl p-4 <?php echo $jenis === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200'; ?>">
                    <i class="fas <?php echo $jenis === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($pesan); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">NIP / NIM</label>
                    <input type="text" name="nip_nim" required 
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                           placeholder="Contoh: 10041993">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Password Baru</label>
                    <input type="password" name="password_baru" required 
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                           placeholder="Minimal 8 karakter">
                    <p class="text-xs text-slate-400 mt-1">Harus mengandung huruf besar, kecil, dan angka</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_konfirmasi" required 
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                           placeholder="Ulangi password baru">
                </div>

                <button type="submit" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Reset Password
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-200">
                <a href="/redesain-siatek/login/" class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-slate-700">
                    <i class="fas fa-arrow-left"></i>Kembali ke halaman login
                </a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-4 text-center">
            <p class="text-xs text-slate-400">
                <i class="fas fa-info-circle mr-1"></i>
                Password baru akan langsung aktif setelah direset
            </p>
        </div>
    </div>
</body>
</html>
