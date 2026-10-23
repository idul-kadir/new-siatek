<!-- ============================================================
     KONTEN SURAT — SURAT KETERANGAN PEMBIMBING KERJA PRAKTEK
     Data dinamis dari database.
     Simpan di folder content-surat/, dimuat oleh template-a4.php.

     Surat ini TANPA kop surat (langsung judul surat di bagian atas).
     Filter: Hanya dapat dicetak jika progres KP > 9.
     ============================================================ -->

<?php
function surat_keterangan_pembimbing_kp($nim){
    $nimClean = bersihkan($nim);

    // Filter: Dokumen ini HANYA bisa dicetak jika progres KP mahasiswa sudah > 9
    $progres = function_exists('get_progres_kp') ? get_progres_kp($nimClean) : 0;
    if ($progres === '-' || (int)$progres <= 9) {
        echo '<div style="font-family: Arial, sans-serif; padding: 25px; text-align: center; color: #991b1b; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 8px; margin: 40px auto; max-width: 600px;">';
        echo '<h3 style="margin-bottom: 8px; font-size: 16pt;">Dokumen Belum Tersedia</h3>';
        echo '<p style="font-size: 11pt; color: #374151; line-height: 1.5;">Dokumen <b>Surat Keterangan Pembimbing</b> belum dapat dicetak karena persyaratan tahapan Kerja Praktek belum terpenuhi.</p>';
        echo '</div>';
        return;
    }

    // Ambil data Mahasiswa & KP
    $sql = "SELECT 
                m.nama AS nama_mahasiswa,
                m.nim AS nim_mahasiswa,
                m.angkatan,
                m.prodi,
                pr.nama AS nama_prodi,
                k.judul AS judul_kp,
                k.surat_penunjukkan_pembimbing
            FROM mahasiswa m
            LEFT JOIN prodi pr ON m.prodi = pr.kode
            LEFT JOIN kp k ON TRIM(m.nim) = TRIM(k.nim)
            WHERE m.nim = '$nimClean'
            LIMIT 1";
    $qMhs = query($sql);
    $data = ($qMhs && mysqli_num_rows($qMhs) > 0) ? mysqli_fetch_assoc($qMhs) : [];

    $namaMhs     = $data['nama_mahasiswa'] ?? '-';
    $nimMhs      = $data['nim_mahasiswa'] ?? $nimClean;
    $prodiMhs    = !empty($data['nama_prodi']) ? $data['nama_prodi'] : 'S1 Teknik Elektro';
    $angkatanMhs = !empty($data['angkatan']) ? $data['angkatan'] : '-';
    $judulKp     = !empty($data['judul_kp']) ? $data['judul_kp'] : '-';

    // Ambil data Surat Penunjukan Pembimbing KP terkait
    $noSuratPenunjukan = '-';
    $tglPenunjukan     = date('d F Y');
    $namaPembimbing    = '';
    $nipPembimbing     = '';

    $idSuratBimb = (int)($data['surat_penunjukkan_pembimbing'] ?? 0);
    $sqlSp = ($idSuratBimb > 0)
        ? "SELECT sp.*, 
                  (SELECT nomor FROM nomor_surat WHERE nomor_surat.id = sp.no_surat LIMIT 1) AS nomor_surat_relasi,
                  d.nama AS nama_pembimbing, d.nip AS nip_pembimbing 
           FROM surat_penunjukkan sp 
           LEFT JOIN dosen d ON (sp.pembimbing1 = d.id OR sp.pembimbing1 = d.nip)
           WHERE sp.id = $idSuratBimb LIMIT 1"
        : "SELECT sp.*, 
                  (SELECT nomor FROM nomor_surat WHERE nomor_surat.id = sp.no_surat LIMIT 1) AS nomor_surat_relasi,
                  d.nama AS nama_pembimbing, d.nip AS nip_pembimbing 
           FROM surat_penunjukkan sp 
           LEFT JOIN dosen d ON (sp.pembimbing1 = d.id OR sp.pembimbing1 = d.nip)
           WHERE sp.nim = '$nimClean' AND (sp.keterangan LIKE '%PEMBIMBING%KP%' OR sp.keterangan = 'PEMBIMBING KP')
           ORDER BY sp.id DESC LIMIT 1";

    $qSp = query($sqlSp);
    if ($qSp && ($sp = mysqli_fetch_assoc($qSp))) {
        $noSuratPenunjukan = !empty($sp['nomor_surat_relasi']) ? $sp['nomor_surat_relasi'] : (!empty($sp['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($sp['no_surat']) : $sp['no_surat']) : '-');
        if (!empty($sp['tanggal']) && $sp['tanggal'] !== '0000-00-00') {
            $tglPenunjukan = function_exists('tgl_indo') ? tgl_indo($sp['tanggal']) : date('d F Y', strtotime($sp['tanggal']));
        }
        $namaPembimbing = $sp['nama_pembimbing'] ?? '';
        $nipPembimbing  = $sp['nip_pembimbing'] ?? '';
    }

    // Tanggal cetak surat hari ini
    $tanggalSurat = function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y');
?>

<!-- Judul Surat Keterangan Pembimbing (Tanpa Kop Surat) -->
<div style="margin-top: 10px; margin-bottom: 25px; text-align: center;">
    <p style="font-size: 12pt; font-weight: bold; margin: 0; letter-spacing: 0.5px;">SURAT KETERANGAN PEMBIMBING</p>
</div>

<!-- Paragraf Pembuka -->
<p style="text-align: justify; line-height: 1.5; font-size: 12pt; margin-bottom: 18px;">
    Tim Pembimbing yang diangkat dengan Surat Penunjukan Ketua Jurusan Teknik Elektro dan Komputer No. <?= htmlspecialchars($noSuratPenunjukan, ENT_QUOTES, 'UTF-8') ?> Tanggal <?= htmlspecialchars($tglPenunjukan, ENT_QUOTES, 'UTF-8') ?> dengan ini menyatakan bahwa penyusunan laporan hasil Kerja Praktek atas nama :
</p>

<!-- Identitas Mahasiswa -->
<table style="width: 100%; border-collapse: collapse; font-size: 12pt; line-height: 1.45; margin-bottom: 18px;">
    <tr>
        <td style="width: 160px; vertical-align: top;">Nama</td>
        <td style="width: 15px; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($namaMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">NIM</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($nimMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Program Studi</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($prodiMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Angkatan</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($angkatanMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Judul</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top; text-align: justify;"><?= htmlspecialchars($judulKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
</table>

<!-- Paragraf Penutup -->
<p style="text-align: justify; line-height: 1.5; font-size: 12pt; margin-bottom: 35px;">
    Telah selesai dan disetujui untuk diajukan pada Seminar Hasil Kerja Praktek, Program Studi <?= htmlspecialchars($prodiMhs, ENT_QUOTES, 'UTF-8') ?> Jurusan Teknik Elektro Dan Komputer
</p>

<!-- Tanda Tangan Pembimbing -->
<table class="ttd-wrap" style="width: 100%; font-size: 12pt; border-collapse: collapse;">
    <tr>
        <td></td>
        <td style="width: 270px; text-align: left;">Gorontalo, <?= htmlspecialchars($tanggalSurat, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: left;">Pembimbing</td>
    </tr>
    <tr style="height: 85px;">
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: left;">
            <p style="margin: 0;"><b><u><?= htmlspecialchars($namaPembimbing, ENT_QUOTES, 'UTF-8') ?></u></b></p>
            <p style="margin: 0;"><?= !empty($nipPembimbing) && $nipPembimbing !== '-' ? (strpos($nipPembimbing, 'NIP') === false ? 'NIP. ' . htmlspecialchars($nipPembimbing, ENT_QUOTES, 'UTF-8') : htmlspecialchars($nipPembimbing, ENT_QUOTES, 'UTF-8')) : '-' ?></p>
        </td>
    </tr>
</table>
<?php } ?>
