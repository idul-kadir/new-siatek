<?php
require_once __DIR__ . '/koneksi.php';
$r = mysqli_query($koneksi, 'SHOW CREATE TABLE pemilik_file');
$row = mysqli_fetch_row($r);
echo $row[1];
