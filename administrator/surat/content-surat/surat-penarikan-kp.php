<!-- ============================================================
     KONTEN SURAT — SURAT PENGANTAR PENARIKAN KERJA PRAKTEK
     Data DUMMY, dimuat oleh template-a4.php.
     ============================================================ -->

<?php 
include __DIR__ . '/kop-surat.php'; 

// Nilai dummy awal
$namaMhs  = 'Mohamad Ryan Noor Sahidu';
$nimMhs   = !empty($nim) ? $nim : '521421011';
$prodiMhs = 'S1 Teknik Elektro';
$tempatKp = 'PT. Telkom Indonesia Witel Gorontalo';
$noSurat  = '145/UN47.B5.5/PK.01/' . date('Y');

// Ambil data jika tersedia
if (!empty($nim)) {
    require_once __DIR__ . '/../../fungsi.php';
    $nimClean = bersihkan($nim);

    $qMhs = query("SELECT m.nama, m.nim, p.nama AS prodi_nama FROM mahasiswa m LEFT JOIN prodi p ON m.prodi = p.kode WHERE m.nim = '$nimClean' LIMIT 1");
    if ($qMhs && $rMhs = mysqli_fetch_assoc($qMhs)) {
        if (!empty($rMhs['nama'])) $namaMhs = $rMhs['nama'];
        if (!empty($rMhs['nim'])) $nimMhs = $rMhs['nim'];
        if (!empty($rMhs['prodi_nama'])) $prodiMhs = $rMhs['prodi_nama'];
    }

    $qKp = query("SELECT instansi, tempat, tempat_kp FROM kp WHERE nim = '$nimClean' LIMIT 1");
    if ($qKp && $rKp = mysqli_fetch_assoc($qKp)) {
        $instansiReal = $rKp['instansi'] ?: ($rKp['tempat'] ?: ($rKp['tempat_kp'] ?: ''));
        if (!empty($instansiReal)) $tempatKp = $instansiReal;
    }
}

$tanggalSurat = function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y');
?>

<p class="judul"><b><u>SURAT PENGANTAR PENARIKAN MAHASISWA KERJA PRAKTEK</u></b></p>
<p class="nomor">Nomor : <?= htmlspecialchars($noSurat, ENT_QUOTES, 'UTF-8') ?></p>

<p class="pembuka">Ketua Jurusan Teknik Elektro Dan Komputer Fakultas Teknik Universitas Negeri Gorontalo dengan ini menyampaikan penarikan mahasiswa yang telah menyelesaikan kegiatan Kerja Praktek (KP) sebagai berikut :</p>

<table class="data">
    <tr>
        <td style="width: 170px;">Nama Mahasiswa</td>
        <td style="width: 25px;">:</td>
        <td><?= htmlspecialchars($namaMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>NIM</td>
        <td>:</td>
        <td><?= htmlspecialchars($nimMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>Program Studi</td>
        <td>:</td>
        <td><?= htmlspecialchars($prodiMhs, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>Tempat / Instansi KP</td>
        <td>:</td>
        <td><b><?= htmlspecialchars($tempatKp, ENT_QUOTES, 'UTF-8') ?></b></td>
    </tr>
</table>

<p class="penutup">Sehubungan dengan telah berakhirnya masa pelaksanaan Kerja Praktek tersebut, kami atas nama Pimpinan Jurusan mengucapkan terima kasih yang sebesar-besarnya atas kesempatan, bimbingan, serta kerja sama yang telah diberikan kepada mahasiswa kami selama melaksanakan kegiatan Kerja Praktek.</p>

<table class="ttd-wrap">
    <tr>
        <td></td>
        <td>Gorontalo, <?= $tanggalSurat ?></td>
    </tr>
    <tr>
        <td></td>
        <td>Ketua Jurusan</td>
    </tr>
    <tr class="ttd-place">
        <td></td>
        <td>
            <svg viewBox="0 0 200 70" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 55 C 35 5, 55 62, 82 28 S 118 55, 145 22 S 180 45, 192 28"
                      fill="none" stroke="#000" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><b><u>Yasin Mohamad, ST., MT.</u></b></td>
    </tr>
    <tr>
        <td></td>
        <td><b>NIP. 197102222001121001</b></td>
    </tr>
    <tr height="35px"></tr>
    <tr>
        <td>Tembusan disampaikan kepada Yth. :</td>
        <td></td>
    </tr>
    <tr>
        <td>1. Dekan Fakultas Teknik UNG</td>
        <td></td>
    </tr>
    <tr>
        <td>2. Dosen Pembimbing Kerja Praktek</td>
        <td></td>
    </tr>
    <tr>
        <td>3. Mahasiswa yang bersangkutan</td>
        <td></td>
    </tr>
    <tr>
        <td>4. Arsip</td>
        <td></td>
    </tr>
</table>
