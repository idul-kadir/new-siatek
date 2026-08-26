<?php
/**
 * INDEX (root) = HALAMAN LOGIN.
 * Proses otentikasi ada di login/proses.php (AJAX + JSON, konvensi php-native-crud).
 * File ini hanya menangani: logout (?keluar=1), pengalihan bila sudah login,
 * dan merender tampilan form login.
 */
require_once __DIR__ . '/fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

/* ===================== LOGOUT ===================== */
if (isset($_GET['keluar'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: /redesain-siatek/');
    exit;
}

/* Sudah login? -> langsung ke portal masing-masing */
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
$peran      = $_SESSION['peran'] ?? (!empty($_SESSION['nim']) ? 'mahasiswa' : 'staf');
if ($sudahLogin) {
    header('Location: /redesain-siatek/' . ($peran === 'mahasiswa' ? 'mahasiswa' : 'administrator'));
    exit;
}

/* Tampilkan halaman login */
require __DIR__ . '/login/login.php';
