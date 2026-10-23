<!-- ============================================================
     KONTEN SURAT — PERMOHONAN IZIN KERJA PRAKTEK (PERSETUJUAN PA)
     Data dinamis dari database.
     Simpan di folder content-surat/, dimuat oleh template-a4.php.

     Surat ini TANPA kop surat (langsung judul surat di bagian atas).
     Filter: Hanya dapat dicetak jika status KP mahasiswa != 'tidak aktif'.
     ============================================================ -->

<?php
function surat_permohonan_izin_kp($nim){
    $nimClean = bersihkan($nim);

    // Ambil data Mahasiswa & KP
    $sql = "SELECT 
                k.*,
                m.nama AS nama_mahasiswa,
                m.nim AS nim_mahasiswa,
                m.prodi,
                m.no_hp,
                m.dosen_pa,
                pr.nama AS nama_prodi,
                d.nama AS nama_pa,
                d.nip AS nip_pa
            FROM kp k
            JOIN mahasiswa m ON TRIM(k.nim) = TRIM(m.nim)
            LEFT JOIN prodi pr ON m.prodi = pr.kode
            LEFT JOIN dosen d ON (m.dosen_pa = d.id OR m.dosen_pa = d.nip)
            WHERE k.nim = '$nimClean'
            LIMIT 1";
    $q = query($sql);
    $data = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_assoc($q) : [];

    // Filter: Hanya dapat dicetak jika status KP mahasiswa != 'tidak aktif'
    $statusKp = strtolower(trim((string)($data['status'] ?? '')));
    if ($statusKp === 'tidak aktif' || empty($data)) {
        echo '<div style="font-family: Arial, sans-serif; padding: 25px; text-align: center; color: #991b1b; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 8px; margin: 40px auto; max-width: 600px;">';
        echo '<h3 style="margin-bottom: 8px; font-size: 16pt;">Dokumen Belum Tersedia</h3>';
        echo '<p style="font-size: 11pt; color: #374151; line-height: 1.5;">Dokumen permohonan izin Kerja Praktek belum dapat dicetak karena status Kerja Praktek Anda tidak aktif.</p>';
        echo '</div>';
        return;
    }

    $namaMhs  = $data['nama_mahasiswa'] ?? '-';
    $nimMhs   = $data['nim_mahasiswa'] ?? $nimClean;
    $prodiMhs = !empty($data['nama_prodi']) ? $data['nama_prodi'] : 'S1 Teknik Elektro';
    $sksMhs   = !empty($data['sks']) ? $data['sks'] : '-';
    $noHpMhs  = !empty($data['no_hp']) ? $data['no_hp'] : '-';

    // Rencana KP
    $mulaiRaw = !empty($data['tanggal_mulai']) && $data['tanggal_mulai'] !== '0000-00-00'
                ? $data['tanggal_mulai']
                : (!empty($data['tgl_start']) && $data['tgl_start'] !== '0000-00-00' ? $data['tgl_start'] : '');
    $selesaiRaw = !empty($data['tanggal_selesai']) && $data['tanggal_selesai'] !== '0000-00-00'
                  ? $data['tanggal_selesai']
                  : (!empty($data['tanggal_penarikan']) && $data['tanggal_penarikan'] !== '0000-00-00' ? $data['tanggal_penarikan'] : '');

    $tglMulai = (!empty($mulaiRaw) && function_exists('tgl_indo')) ? tgl_indo($mulaiRaw) : $mulaiRaw;
    $tglSelesai = (!empty($selesaiRaw) && function_exists('tgl_indo')) ? tgl_indo($selesaiRaw) : $selesaiRaw;
    $waktuPelaksanaan = ($tglMulai && $tglSelesai) ? ($tglMulai . ' s.d ' . $tglSelesai) : ($tglMulai ?: ($tglSelesai ?: '-'));

    $tempatKp = !empty($data['nama_instansi']) ? $data['nama_instansi'] : (!empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : '-')));
    $alamatKp = !empty($data['alamat_instansi']) ? $data['alamat_instansi'] : (!empty($data['alamat']) ? $data['alamat'] : '-');
    $tujuanKp = !empty($data['pimpinan_instansi']) ? $data['pimpinan_instansi'] : (!empty($data['tujuan_surat']) ? $data['tujuan_surat'] : '-');

    // Data Penasihat Akademik (PA)
    $namaPa = $data['nama_pa'] ?? '-';
    $nipPa  = $data['nip_pa'] ?? '-';

    // Tanggal cetak surat
    $tanggalSurat = function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y');
?>

<!-- Perihal Surat di Bagian Kiri Atas (Tanpa Kop Surat) -->
<div style="margin-top: 0; margin-bottom: 18px;">
    <p style="font-size: 12pt; font-weight: bold; margin: 0;">Hal Permohonan Izin Kerja Praktek</p>
</div>

<!-- Tujuan Surat -->
<div style="margin-bottom: 22px; line-height: 1.35; font-size: 12pt;">
    <p style="margin: 0;">Yth. Ketua Jurusan</p>
    <p style="margin: 0; font-weight: bold;">Universitas Negeri Gorontalo</p>
    <p style="margin: 0;">Di Gorontalo</p>
</div>

<!-- Pengantar -->
<p style="font-size: 12pt; margin-bottom: 12px;">Yang bertanda tangan dibawah ini,</p>

<!-- Identitas Mahasiswa -->
<table style="width: 100%; border-collapse: collapse; font-size: 12pt; line-height: 1.4; margin-bottom: 14px;">
    <tr>
        <td style="width: 220px; vertical-align: top;">Nama</td>
        <td style="width: 15px; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($namaMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">NIM</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($nimMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Prodi</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($prodiMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Jumlah SKS telah ditempuh</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($sksMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">NO. Telp/HP</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($noHpMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
</table>

<!-- Keterangan Pelaksanaan -->
<p style="font-size: 12pt; margin-bottom: 12px;">Bermaksud melakukan Kerja Praktek pada :</p>

<!-- Rincian Rencana KP -->
<table style="width: 100%; border-collapse: collapse; font-size: 12pt; line-height: 1.4; margin-bottom: 16px;">
    <tr>
        <td style="width: 220px; vertical-align: top;">Waktu</td>
        <td style="width: 15px; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($waktuPelaksanaan, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Tempat</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($tempatKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Alamat</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($alamatKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Tujuan Surat kepada</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($tujuanKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
</table>

<!-- Paragraf Penutup -->
<p style="text-align: justify; line-height: 1.45; font-size: 12pt; margin-bottom: 14px;">
    Sehubungan dengan hal tersebut, dengan ini kami mohon bantuan Bapak/Ibu untuk dapat diterbitkan surat pengantar melaksanakan <b>Kerja Praktek</b> pada instansi tersebut diatas.
</p>

<p style="text-align: justify; line-height: 1.45; font-size: 12pt; margin-bottom: 25px;">
    Atas perhatian dan bantuan Bapak/Ibu, kami ucapkan terima kasih
</p>

<!-- Tanda Tangan: Kiri (Penasihat Akademik), Kanan (Pemohon) -->
<table class="ttd-wrap" style="width: 100%; font-size: 12pt; border-collapse: collapse;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <p style="margin: 0;">Menyetujui :</p>
            <p style="margin: 0;">Penasihat Akademik,</p>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <p style="margin: 0;">Gorontalo, <?= htmlspecialchars($tanggalSurat, ENT_QUOTES, 'UTF-8') ?></p>
            <p style="margin: 0;">Pemohon</p>
        </td>
    </tr>
    <tr style="height: 85px;">
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <p style="margin: 0;"><b><u><?= htmlspecialchars($namaPa, ENT_QUOTES, 'UTF-8') ?></u></b></p>
            <p style="margin: 0;"><?= !empty($nipPa) && $nipPa !== '-' ? (strpos($nipPa, 'NIP') === false ? 'NIP. ' . htmlspecialchars($nipPa, ENT_QUOTES, 'UTF-8') : htmlspecialchars($nipPa, ENT_QUOTES, 'UTF-8')) : '-' ?></p>
        </td>
        <td style="vertical-align: top;">
            <p style="margin: 0;"><?= htmlspecialchars($namaMhs, ENT_QUOTES, 'UTF-8') ?></p>
        </td>
    </tr>
</table>
<?php } ?>
