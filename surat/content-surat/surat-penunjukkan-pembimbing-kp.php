<!-- ============================================================
     KONTEN SURAT — PENUNJUKAN DOSEN PEMBIMBING KERJA PRAKTEK
     Data dinamis dari database.
     Simpan di folder content-surat/, dimuat oleh template-a4.php.

     Surat ini MEMBUTUHKAN kop → kop diload di sini (include).
     ============================================================ -->

<?php
function surat_penunjukkan_pembimbing_kp($nim){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Dokumen rahasia: Wajib ada sesi login dan minimal level 1
    if (empty($_SESSION['id']) || (int)($_SESSION['level'] ?? 0) < 1) {
        echo '<div style="font-family: Arial, sans-serif; padding: 25px; text-align: center; color: #991b1b; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 8px; margin: 40px auto; max-width: 600px;">';
        echo '<h3 style="margin-bottom: 8px; font-size: 16pt;">Akses Ditolak</h3>';
        echo '<p style="font-size: 11pt; color: #374151; line-height: 1.5;">Dokumen ini bersifat rahasia dan hanya dapat diakses oleh staf/pejabat berwenang.</p>';
        echo '</div>';
        return;
    }

    $nimClean = bersihkan($nim);

    // Filter progres KP pembimbing KP > 6
    $progres = function_exists('get_progres_kp') ? get_progres_kp($nimClean) : 0;
    if ($progres === '-' || (int)$progres <= 6) {
        echo '<div style="font-family: Arial, sans-serif; padding: 25px; text-align: center; color: #991b1b; background: #fef2f2; border: 1.5px solid #f87171; border-radius: 8px; margin: 40px auto; max-width: 600px;">';
        echo '<h3 style="margin-bottom: 8px; font-size: 16pt;">Dokumen Belum Tersedia</h3>';
        echo '<p style="font-size: 11pt; color: #374151; line-height: 1.5;">Dokumen surat penunjukan belum dapat diterbitkan karena persyaratan tahapan Kerja Praktek belum terpenuhi.</p>';
        echo '</div>';
        return;
    }

    $sql = "SELECT 
                sp.*,
                (SELECT nomor FROM nomor_surat WHERE nomor_surat.id = sp.no_surat LIMIT 1) AS nomor_surat_relasi,
                m.nama AS nama_mahasiswa,
                m.nim AS nim_mahasiswa,
                k.judul AS judul_kp,
                k.nama_instansi AS instansi_kp,
                d1.nama AS nama_pembimbing1, d1.nip AS nip_pembimbing1,
                d2.nama AS nama_pembimbing2, d2.nip AS nip_pembimbing2
            FROM surat_penunjukkan sp
            LEFT JOIN mahasiswa m ON (TRIM(m.nim) = TRIM(sp.nim))
            LEFT JOIN kp k ON (TRIM(k.nim) = TRIM(sp.nim))
            LEFT JOIN dosen d1 ON (sp.pembimbing1 = d1.id OR sp.pembimbing1 = d1.nip)
            LEFT JOIN dosen d2 ON (sp.pembimbing2 = d2.id OR sp.pembimbing2 = d2.nip)
            WHERE (sp.nim = '$nimClean' OR sp.id = '$nimClean' OR sp.no_surat = '$nimClean')
              AND (sp.keterangan LIKE '%PEMBIMBING%KP%' OR sp.keterangan = 'PEMBIMBING KP')
            ORDER BY sp.id DESC LIMIT 1";

    $cek_penunjukkan = query($sql);

    // Fallback jika tidak ditemukan dengan filter spesifik
    if(!$cek_penunjukkan || mysqli_num_rows($cek_penunjukkan) < 1){
        $sqlFallback = "SELECT 
                    sp.*,
                    (SELECT nomor FROM nomor_surat WHERE nomor_surat.id = sp.no_surat LIMIT 1) AS nomor_surat_relasi,
                    m.nama AS nama_mahasiswa,
                    m.nim AS nim_mahasiswa,
                    k.judul AS judul_kp,
                    k.nama_instansi AS instansi_kp,
                    d1.nama AS nama_pembimbing1, d1.nip AS nip_pembimbing1,
                    d2.nama AS nama_pembimbing2, d2.nip AS nip_pembimbing2
                FROM surat_penunjukkan sp
                LEFT JOIN mahasiswa m ON (TRIM(m.nim) = TRIM(sp.nim))
                LEFT JOIN kp k ON (TRIM(k.nim) = TRIM(sp.nim))
                LEFT JOIN dosen d1 ON (sp.pembimbing1 = d1.id OR sp.pembimbing1 = d1.nip)
                LEFT JOIN dosen d2 ON (sp.pembimbing2 = d2.id OR sp.pembimbing2 = d2.nip)
                WHERE (sp.nim = '$nimClean' OR sp.id = '$nimClean' OR sp.no_surat = '$nimClean')
                ORDER BY sp.id DESC LIMIT 1";
        $cek_penunjukkan = query($sqlFallback);
    }

    if(!$cek_penunjukkan || mysqli_num_rows($cek_penunjukkan) < 1){
        echo 'Data tidak valid';
        exit;
    }
    $data = mysqli_fetch_assoc($cek_penunjukkan);

    // Pastikan data mahasiswa & KP terisi jika relasi di atas kosong
    if (empty($data['nama_mahasiswa'])) {
        $nimTarget = !empty($data['nim']) ? bersihkan($data['nim']) : $nimClean;
        $qMhs = query("SELECT nama, nim FROM mahasiswa WHERE nim = '$nimTarget' LIMIT 1");
        if ($qMhs && $rMhs = mysqli_fetch_assoc($qMhs)) {
            $data['nama_mahasiswa'] = $rMhs['nama'];
            $data['nim_mahasiswa']  = $rMhs['nim'];
        }
    }
    if (empty($data['judul_kp'])) {
        $nimTarget = !empty($data['nim_mahasiswa']) ? bersihkan($data['nim_mahasiswa']) : (!empty($data['nim']) ? bersihkan($data['nim']) : $nimClean);
        $qKp = query("SELECT judul, nama_instansi FROM kp WHERE nim = '$nimTarget' LIMIT 1");
        if ($qKp && $rKp = mysqli_fetch_assoc($qKp)) {
            $data['judul_kp']    = $rKp['judul'];
            $data['instansi_kp'] = $rKp['nama_instansi'] ?? '';
        }
    }

    $tempatKp = !empty($data['instansi_kp']) ? $data['instansi_kp'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : '-');

    // Nomor surat
    $nomorSurat = !empty($data['nomor_surat_relasi']) ? $data['nomor_surat_relasi'] : (!empty($data['no_surat']) ? (function_exists('dapatkan_nomor_surat_teks') ? dapatkan_nomor_surat_teks($data['no_surat']) : $data['no_surat']) : '-');

    // Kumpulkan daftar pembimbing yang valid
    $daftarPembimbing = [];
    for ($i = 1; $i <= 2; $i++) {
        $namaPemb = trim((string)($data["nama_pembimbing$i"] ?? ''));
        $nipPemb  = trim((string)($data["nip_pembimbing$i"] ?? ''));
        if ($namaPemb !== '') {
            $daftarPembimbing[] = [
                'peran' => 'Pembimbing ' . (count($daftarPembimbing) + 1),
                'nama'  => $namaPemb,
                'nip'   => $nipPemb !== '' ? $nipPemb : '-'
            ];
        }
    }

    // Tanggal surat
    if (!empty($data['tanggal']) && $data['tanggal'] !== '0000-00-00') {
        $tanggalSurat = function_exists('tgl_indo') ? tgl_indo($data['tanggal']) : date('d F Y', strtotime($data['tanggal']));
    } else {
        $tanggalSurat = function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y');
    }

    // Pejabat Ketua Jurusan murni dari database
    $namaKajur = '';
    $nipKajur  = '';
    $qKajur = query("SELECT d.nama AS nama_kajur, d.nip AS nip_kajur FROM jurusan j LEFT JOIN dosen d ON j.kajur = d.id LIMIT 1");
    if ($qKajur && ($rKajur = mysqli_fetch_assoc($qKajur))) {
        $namaKajur = $rKajur['nama_kajur'] ?? '';
        $nipKajur  = $rKajur['nip_kajur'] ?? '';
    }

    // URL untuk verifikasi QR Code
    $qrUrl = base_url() . '/dokumen/surat-penunjukkan-pembimbing-kp/' . $nimClean;

    include __DIR__ . '/kop-surat.php'; 
?>

<p class="judul"><b><u>SURAT PENUNJUKAN DOSEN PEMBIMBING KERJA PRAKTEK</u></b></p>
<p class="nomor">Nomor : <?= htmlspecialchars($nomorSurat, ENT_QUOTES, 'UTF-8') ?></p>

<p class="pembuka">Ketua Jurusan Teknik Elektro Dan Komputer Fakultas Teknik Universitas Negeri Gorontalo dengan ini menunjuk Bapak/Ibu yang namanya tercantum di bawah ini sebagai Pembimbing Kerja Praktek (KP) sebagai berikut :</p>

<table class="data">
    <?php foreach ($daftarPembimbing as $idx => $pemb): ?>
    <tr>
        <td rowspan="2" style="width:40px"><?= $idx + 1 ?></td>
        <td width="130px"><?= htmlspecialchars($pemb['peran'], ENT_QUOTES, 'UTF-8') ?></td>
        <td width="25px">:</td>
        <td><?= htmlspecialchars($pemb['nama'], ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td>NIP</td>
        <td>:</td>
        <td><?= htmlspecialchars($pemb['nip'], ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr height="8px"></tr>
    <tr>
        <td colspan="4">Mahasiswa yang dibimbing,</td>
    </tr>
    <tr>
        <td colspan="2">Nama</td>
        <td>:</td>
        <td><?= htmlspecialchars($data['nama_mahasiswa'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td colspan="2">NIM</td>
        <td>:</td>
        <td><?= htmlspecialchars($data['nim_mahasiswa'] ?? $data['nim'] ?? $nimClean, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top;">Tempat / Instansi KP</td>
        <td style="vertical-align: top;">:</td>
        <td><?= htmlspecialchars($tempatKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top;">Judul Laporan KP</td>
        <td style="vertical-align: top;">:</td>
        <td style="text-align: justify;"><b><?= htmlspecialchars($data['judul_kp'] ?? '-', ENT_QUOTES, 'UTF-8') ?></b></td>
    </tr>
</table>

<p class="penutup">Kepada Bapak/Ibu yang namanya tercantum dalam Surat Penunjukan ini agar dapat melaksanakan tugas dengan penuh tanggung jawab. Surat Keputusan Dosen Pembimbing Kerja Praktek akan diterbitkan kemudian.</p>

<table class="ttd-wrap">
    <tr>
        <td></td>
        <td>Gorontalo, <?= htmlspecialchars($tanggalSurat, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td></td>
        <td>Ketua Jurusan</td>
    </tr>
    <tr class="ttd-place">
        <td></td>
        <td>
            <?php if (!empty($data['ttd'])): ?>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= rawurlencode($qrUrl) ?>" alt="QR Code" style="margin-top:10px; margin-left:20px">
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><b><u><?= htmlspecialchars($namaKajur, ENT_QUOTES, 'UTF-8') ?></u></b></td>
    </tr>
    <tr>
        <td></td>
        <td><b>NIP. <?= htmlspecialchars($nipKajur, ENT_QUOTES, 'UTF-8') ?></b></td>
    </tr>
    <tr height="8px"></tr>
    <tr>
        <td>Tembusan disampaikan kepada Yth. :</td>
        <td></td>
    </tr>
    <tr>
        <td>1. Dosen Pembimbing KP</td>
        <td></td>
    </tr>
    <tr>
        <td>2. Mahasiswa yang bersangkutan</td>
        <td></td>
    </tr>
    <tr>
        <td>3. Arsip</td>
        <td></td>
    </tr>
</table>
<?php } ?>
