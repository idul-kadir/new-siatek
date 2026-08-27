<?php
/**
 * Fungsi bersama di-unifikasi ke root/fungsi.php (satu-satunya sumber kebenaran).
 * File ini cukup jadi alias: me-require root/fungsi.php supaya halaman-halaman
 * lama yang sudah me-require __DIR__/fungsi.php (mis. sidebar, top-navbar,
 * biodata, dsb) tetap bekerja tanpa mengubah satu per satu require-nya.
 *
 * Semua fungsi (query, bersihkan, tampil, base_url, url_file, upload_file,
 * catat_file) kini didefinisikan sekali saja di root/fungsi.php.
 */
require_once dirname(__DIR__) . '/fungsi.php';
