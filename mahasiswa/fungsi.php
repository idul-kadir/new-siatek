<?php
/**
 * Fungsi bersama di-unifikasi ke root/fungsi.php (satu-satunya sumber kebenaran).
 * File ini cukup jadi alias: me-require root/fungsi.php supaya halaman-halaman
 * portal mahasiswa yang sudah me-require __DIR__/fungsi.php tetap bekerja
 * tanpa mengubah require-nya satu per satu.
 *
 * Semua fungsi (query, bersihkan, tampil, base_url, url_file, upload_file,
 * catat_file) kini didefinisikan sekali saja di root/fungsi.php.
 */
require_once dirname(__DIR__) . '/fungsi.php';
