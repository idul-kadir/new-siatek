<?php
/*
    =====================================================
    PROSES SURAT PENUNJUKKAN (JURUSAN)
    =====================================================
    Handling AJAX request edit & reload data surat penunjukkan.
    Sesuai konvensi php-native-crud-instructions.md
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../../fungsi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek sesi login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    echo json_encode(['status' => false, 'pesan' => 'Sesi berakhir. Silakan login kembali']);
    exit;
}

// ================= EDIT SURAT PENUNJUKKAN =================
if (isset($_POST['edit-surat-penunjukkan'])) {
    $id = bersihkan($_POST['id'] ?? '');
    $no_surat = bersihkan($_POST['no_surat'] ?? '');
    $tanggal = bersihkan($_POST['tanggal'] ?? '');
    $jenis = bersihkan($_POST['jenis'] ?? '');
    $dosen = $_POST['dosen'] ?? [];

    if ($id === '' || $no_surat === '') {
        echo json_encode(['status' => false, 'pesan' => 'ID dan Nomor Surat wajib diisi']);
        exit;
    }

    // Format tanggal jika dikirim string
    $tanggal_sql = $tanggal !== '' ? date('Y-m-d', strtotime($tanggal)) : date('Y-m-d');

    // Default string kosong '' jika tidak diisi/tidak berlaku
    $p1 = ''; $p2 = '';
    $pg1 = ''; $pg2 = ''; $pg3 = ''; $pg4 = ''; $pg5 = '';

    $dosenList = is_array($dosen) ? $dosen : [];

    if (in_array($jenis, ['pemskr', 'pemkp'])) {
        $p1 = isset($dosenList[0]) && trim($dosenList[0]) !== '' ? bersihkan($dosenList[0]) : '';
        if ($jenis === 'pemskr') {
            $p2 = isset($dosenList[1]) && trim($dosenList[1]) !== '' ? bersihkan($dosenList[1]) : '';
        } else {
            $p2 = '';
        }
    } else {
        $pg1 = isset($dosenList[0]) && trim($dosenList[0]) !== '' ? bersihkan($dosenList[0]) : '';
        $pg2 = isset($dosenList[1]) && trim($dosenList[1]) !== '' ? bersihkan($dosenList[1]) : '';
        $pg3 = isset($dosenList[2]) && trim($dosenList[2]) !== '' ? bersihkan($dosenList[2]) : '';
        $pg4 = isset($dosenList[3]) && trim($dosenList[3]) !== '' ? bersihkan($dosenList[3]) : '';
        $pg5 = isset($dosenList[4]) && trim($dosenList[4]) !== '' ? bersihkan($dosenList[4]) : '';
    }

    $valP1 = "'$p1'";
    $valP2 = "'$p2'";
    $valPg1 = "'$pg1'";
    $valPg2 = "'$pg2'";
    $valPg3 = "'$pg3'";
    $valPg4 = "'$pg4'";
    $valPg5 = "'$pg5'";

    $update = query("UPDATE surat_penunjukkan SET 
        no_surat = '$no_surat', 
        tanggal = '$tanggal_sql', 
        pembimbing1 = $valP1, 
        pembimbing2 = $valP2, 
        penguji1 = $valPg1, 
        penguji2 = $valPg2, 
        penguji3 = $valPg3, 
        penguji4 = $valPg4, 
        penguji5 = $valPg5 
        WHERE id = '$id'");

    if ($update) {
        echo json_encode(['status' => true, 'pesan' => 'Data surat penunjukkan berhasil diubah']);
    } else {
        $err = mysqli_error($GLOBALS['koneksi'] ?? $koneksi);
        echo json_encode(['status' => false, 'pesan' => "Data gagal diubah: $err"]);
    }
    exit;
}
?>
