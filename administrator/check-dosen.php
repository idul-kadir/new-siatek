<?php
require_once __DIR__ . '/koneksi.php';
$res = mysqli_query($koneksi, "SELECT id, nama, status, posisi FROM dosen WHERE nama LIKE '%rahmat%' OR nama LIKE '%dongka%'");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
} else {
    echo "Query error: " . mysqli_error($koneksi);
}
