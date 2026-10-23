<!-- ============================================================
     KONTEN SURAT — LEMBAR BIMBINGAN KERJA PRAKTEK
     Data dinamis dari database.
     Simpan di folder content-surat/, dimuat oleh template-a4.php.

     Surat ini MEMBUTUHKAN kop → kop diload di sini (include).
     Filter: Hanya dapat dicetak jika progres KP > 6.
     ============================================================ -->

<?php
function lembar_bimbingan_kp($nim){
    $nimClean = bersihkan($nim);

    // Filter: Dokumen ini HANYA bisa dicetak jika progres KP mahasiswa sudah > 6
    $progres = function_exists('get_progres_kp') ? get_progres_kp($nimClean) : 0;
    if ($progres === '-' || (int)$progres <= 6) {
        echo '<div style="font-family: Arial, sans-serif; padding: 25px; text-align: center; color: #991b1b; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 8px; margin: 40px auto; max-width: 600px;">';
        echo '<h3 style="margin-bottom: 8px; font-size: 16pt;">Dokumen Belum Tersedia</h3>';
        echo '<p style="font-size: 11pt; color: #374151; line-height: 1.5;">Dokumen <b>Lembar Bimbingan Kerja Praktek</b> belum dapat dicetak karena persyaratan tahapan Kerja Praktek belum terpenuhi.</p>';
        echo '</div>';
        return;
    }

    // Ambil data KP & Mahasiswa
    $sqlKp = "SELECT 
                k.*,
                m.nama AS nama_mahasiswa,
                m.nim AS nim_mahasiswa
              FROM kp k
              LEFT JOIN mahasiswa m ON TRIM(k.nim) = TRIM(m.nim)
              WHERE k.nim = '$nimClean'
              LIMIT 1";
    $qKp = query($sqlKp);
    $data = ($qKp && mysqli_num_rows($qKp) > 0) ? mysqli_fetch_assoc($qKp) : [];

    // Fallback data mahasiswa jika record kp belum ada
    if (empty($data['nama_mahasiswa'])) {
        $qMhs = query("SELECT nama, nim FROM mahasiswa WHERE nim = '$nimClean' LIMIT 1");
        if ($qMhs && $rMhs = mysqli_fetch_assoc($qMhs)) {
            $data['nama_mahasiswa'] = $rMhs['nama'];
            $data['nim_mahasiswa']  = $rMhs['nim'];
        }
    }

    $namaMhs = $data['nama_mahasiswa'] ?? '-';
    $nimMhs  = $data['nim_mahasiswa'] ?? $nimClean;

    // Lokasi Kerja Praktek
    $lokasiKp = !empty($data['nama_instansi']) ? $data['nama_instansi'] : (!empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : '-')));

    // Waktu Pelaksanaan
    $mulaiRaw = !empty($data['tanggal_mulai']) && $data['tanggal_mulai'] !== '0000-00-00'
                ? $data['tanggal_mulai']
                : (!empty($data['tgl_start']) && $data['tgl_start'] !== '0000-00-00' ? $data['tgl_start'] : '');
    $selesaiRaw = !empty($data['tanggal_selesai']) && $data['tanggal_selesai'] !== '0000-00-00'
                  ? $data['tanggal_selesai']
                  : (!empty($data['tanggal_penarikan']) && $data['tanggal_penarikan'] !== '0000-00-00' ? $data['tanggal_penarikan'] : '');

    $tglMulai = (!empty($mulaiRaw) && function_exists('tgl_indo')) ? tgl_indo($mulaiRaw) : $mulaiRaw;
    $tglSelesai = (!empty($selesaiRaw) && function_exists('tgl_indo')) ? tgl_indo($selesaiRaw) : $selesaiRaw;
    $waktuPelaksanaan = ($tglMulai && $tglSelesai) ? ($tglMulai . ' - ' . $tglSelesai) : ($tglMulai ?: ($tglSelesai ?: '-'));

    // Ambil Dosen Pembimbing KP
    $namaPembimbing = '';
    $nipPembimbing  = '';
    $idSuratBimb = (int)($data['surat_penunjukkan_pembimbing'] ?? 0);
    $qSuratBimb = ($idSuratBimb > 0)
        ? query("SELECT pembimbing1 FROM surat_penunjukkan WHERE id = $idSuratBimb LIMIT 1")
        : query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nimClean' AND keterangan LIKE '%PEMBIMBING%KP%' ORDER BY id DESC LIMIT 1");
    if ($qSuratBimb && mysqli_num_rows($qSuratBimb) > 0) {
        $sb = mysqli_fetch_assoc($qSuratBimb);
        $idP1 = $sb['pembimbing1'];
        $qDsn = query("SELECT nama, nip FROM dosen WHERE id = '$idP1' OR nip = '$idP1' LIMIT 1");
        if ($qDsn && ($rd = mysqli_fetch_assoc($qDsn))) {
            $namaPembimbing = $rd['nama'];
            $nipPembimbing  = $rd['nip'];
        }
    }

    include __DIR__ . '/kop-surat.php'; 
?>

<!-- Judul Dokumen -->
<p class="judul" style="margin-top: 10px; margin-bottom: 14px;"><b><u>LEMBAR BIMBINGAN KERJA PRAKTEK</u></b></p>

<!-- Identitas Mahasiswa -->
<table style="width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 12px; line-height: 1.35;">
    <tr>
        <td style="width: 170px; vertical-align: top;">Nama</td>
        <td style="width: 15px; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($namaMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">NIM</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($nimMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Lokasi Kerja Praktek</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($lokasiKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Waktu Pelaksanaan</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($waktuPelaksanaan, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
</table>

<!-- Tabel Log Konsultasi Bimbingan (Tinggi area konsultasi disesuaikan seperti format asli) -->
<table style="width: 100%; border-collapse: collapse; font-size: 11pt; border: 1.5px solid #000;">
    <thead>
        <tr>
            <th style="border: 1px solid #000; padding: 6px 8px; width: 170px; text-align: center; font-weight: bold;">Tanggal Konsultasi</th>
            <th style="border: 1px solid #000; padding: 6px 8px; text-align: center; font-weight: bold;">Koreksi</th>
            <th style="border: 1px solid #000; padding: 6px 8px; width: 110px; text-align: center; font-weight: bold;">Paraf</th>
        </tr>
    </thead>
    <tbody>
        <!-- Area kolom bimbingan bergaris penuh (tinggi kolom asistensi) -->
        <tr>
            <td style="border: 1px solid #000; height: 520px; vertical-align: top; padding: 6px;"></td>
            <td style="border: 1px solid #000; height: 520px; vertical-align: top; padding: 6px;"></td>
            <td style="border: 1px solid #000; height: 520px; vertical-align: top; padding: 6px;"></td>
        </tr>
    </tbody>
</table>

<!-- Catatan Bawah & Kolom Pembimbing -->
<table style="width: 100%; margin-top: 8px; font-size: 11pt; border-collapse: collapse;">
    <tr>
        <td style="vertical-align: top; font-style: italic;">*)Jumlah bimbingan minimal 8 kali</td>
        <td style="width: 250px; text-align: center; vertical-align: top;">Pembimbing</td>
    </tr>
    <tr style="height: 85px;">
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: center;">
            <p style="margin: 0;"><b><u><?= htmlspecialchars($namaPembimbing, ENT_QUOTES, 'UTF-8') ?></u></b></p>
            <p style="margin: 0;"><?= !empty($nipPembimbing) && $nipPembimbing !== '-' ? (strpos($nipPembimbing, 'NIP') === false ? 'NIP. ' . htmlspecialchars($nipPembimbing, ENT_QUOTES, 'UTF-8') : htmlspecialchars($nipPembimbing, ENT_QUOTES, 'UTF-8')) : '-' ?></p>
        </td>
    </tr>
</table>
<?php } ?>
