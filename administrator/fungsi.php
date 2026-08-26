<?php
require __DIR__ . '/koneksi.php';

function query($sql){
  global $koneksi;
  return mysqli_query($koneksi,$sql);
}

function bersihkan($data) {
  global $koneksi;
    // 1. Buang spasi di awal dan akhir
    $data = trim($data);
    
    // 2. Buang backslash (\)
    $data = stripslashes($data);
    
    // 3. Buang tag HTML / Script (XSS protection)
    $data = strip_tags($data);
    
    // 4. Escape karakter khusus SQL seperti ' atau " (SQL Injection protection)
    $data = mysqli_real_escape_string($koneksi, $data);
    
    return $data;
}

// Fungsi khusus untuk menampilkan data dari database agar aman di HTML,
// termasuk saat dipakai di atribut HTML seperti data-nama-file.
function tampil($data) {
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}