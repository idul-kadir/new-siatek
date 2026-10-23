<!-- ============================================================
     KONTEN SURAT — PENERBITAN PERMOHONAN KERJA PRAKTEK
     Data dinamis dari database.
     Simpan di folder content-surat/, dimuat oleh template-a4.php.

     Surat ini MEMBUTUHKAN kop → kop diload di sini (include).
     ============================================================ -->

<?php
function surat_permohonan_kp($nim){
    $nimClean = bersihkan($nim);

    // Ambil data KP mahasiswa utama
    $sqlKp = "SELECT 
                k.*,
                m.nama AS nama_mahasiswa,
                m.nim AS nim_mahasiswa,
                m.prodi,
                pr.nama AS nama_prodi
              FROM kp k
              LEFT JOIN mahasiswa m ON TRIM(k.nim) = TRIM(m.nim)
              LEFT JOIN prodi pr ON m.prodi = pr.kode
              WHERE k.nim = '$nimClean'
              LIMIT 1";
    $qKp = query($sqlKp);
    $data = ($qKp && mysqli_num_rows($qKp) > 0) ? mysqli_fetch_assoc($qKp) : [];

    // Fallback data mahasiswa jika record kp belum ada
    if (empty($data['nama_mahasiswa'])) {
        $qMhs = query("SELECT m.nama, m.nim, m.prodi, pr.nama AS nama_prodi FROM mahasiswa m LEFT JOIN prodi pr ON m.prodi = pr.kode WHERE m.nim = '$nimClean' LIMIT 1");
        if ($qMhs && $rMhs = mysqli_fetch_assoc($qMhs)) {
            $data['nama_mahasiswa'] = $rMhs['nama'];
            $data['nim_mahasiswa']  = $rMhs['nim'];
            $data['nama_prodi']     = $rMhs['nama_prodi'];
        }
    }

    // Nomor surat dinamis
    $nomorSurat = '';
    $qNomor = query("SELECT nomor FROM nomor_surat WHERE (keterangan LIKE '%Permohonan%KP%' OR keterangan LIKE '%Penerbitan%KP%') AND keterangan LIKE '%$nimClean%' ORDER BY id DESC LIMIT 1");
    if ($qNomor && ($rNomor = mysqli_fetch_assoc($qNomor)) && !empty($rNomor['nomor'])) {
        $nomorSurat = $rNomor['nomor'];
    } else {
        $nomorSurat = function_exists('get_nomor_surat') ? get_nomor_surat('permohonan kp') : '';
        if ($nomorSurat === 'Tidak Ditemukan' || empty($nomorSurat)) {
            $nomorSurat = function_exists('get_nomor_surat') ? get_nomor_surat('surat permohonan kerja praktek') : '';
        }
        if ($nomorSurat === 'Tidak Ditemukan' || empty($nomorSurat)) {
            $nomorSurat = '-';
        }
    }

    // Tanggal surat
    $tanggalSurat = function_exists('tgl_indo') ? tgl_indo(date('Y-m-d')) : date('d F Y');

    // Data Lokasi & Tujuan
    $lokasiKp = !empty($data['nama_instansi']) ? $data['nama_instansi'] : (!empty($data['instansi']) ? $data['instansi'] : (!empty($data['tempat_kp']) ? $data['tempat_kp'] : (!empty($data['tempat']) ? $data['tempat'] : '-')));
    $tujuanSurat = !empty($data['pimpinan_instansi']) ? $data['pimpinan_instansi'] : (!empty($data['tujuan_surat']) ? $data['tujuan_surat'] : '-');

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

    // Program Studi
    $prodiMhs = !empty($data['nama_prodi']) ? $data['nama_prodi'] : 'S1 Teknik Elektro';

    // Daftar Mahasiswa (Otomatis mendeteksi teman satu kelompok di instansi yang sama jika ada)
    $daftarMahasiswa = [];
    if (!empty($lokasiKp) && $lokasiKp !== '-') {
        $lokasiEsc = bersihkan($lokasiKp);
        $qKelompok = query("SELECT m.nama, m.nim 
                            FROM kp k 
                            JOIN mahasiswa m ON TRIM(k.nim) = TRIM(m.nim) 
                            WHERE (k.nama_instansi = '$lokasiEsc') 
                            ORDER BY m.nim ASC");
        if ($qKelompok && mysqli_num_rows($qKelompok) > 0) {
            while ($rm = mysqli_fetch_assoc($qKelompok)) {
                $daftarMahasiswa[] = $rm;
            }
        }
    }
    // Jika tidak ada kelompok atau belum ada instansi, tampilkan mahasiswa yang bersangkutan
    if (empty($daftarMahasiswa)) {
        $daftarMahasiswa[] = [
            'nama' => $data['nama_mahasiswa'] ?? '-',
            'nim'  => $data['nim_mahasiswa'] ?? $nimClean
        ];
    }

    // Pejabat Ketua Jurusan murni dari database
    $namaKajur = '';
    $nipKajur  = '';
    $ttdKajur  = '';
    $qKajur = query("SELECT d.nama AS nama_kajur, d.nip AS nip_kajur, d.ttd FROM jurusan j LEFT JOIN dosen d ON j.kajur = d.id LIMIT 1");
    if ($qKajur && ($rKajur = mysqli_fetch_assoc($qKajur))) {
        $namaKajur = $rKajur['nama_kajur'] ?? '';
        $nipKajur  = $rKajur['nip_kajur'] ?? '';
        $ttdKajur  = $rKajur['ttd'] ?? '';
    }

    // URL untuk verifikasi QR Code
    $qrUrl = base_url() . '/dokumen/surat-permohonan-kp/' . $nimClean;

    include __DIR__ . '/kop-surat.php'; 
?>

<!-- Header Meta: Nomor, Hal, Tanggal -->
<table style="width: 100%; margin-top: 8px; border-collapse: collapse; font-size: 12pt;">
    <tr>
        <td style="width: 75px; vertical-align: top;">Nomor</td>
        <td style="width: 15px; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><?= htmlspecialchars($nomorSurat, ENT_QUOTES, 'UTF-8') ?></td>
        <td style="text-align: right; vertical-align: top; width: 220px;"><?= htmlspecialchars($tanggalSurat, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Hal</td>
        <td style="vertical-align: top;">:</td>
        <td colspan="2" style="vertical-align: top;"><b>Penerbitan Permohonan Kerja Praktek</b></td>
    </tr>
</table>

<!-- Tujuan Surat / Penerima -->
<div style="margin-top: 18px; line-height: 1.35; font-size: 12pt;">
    <p>Yth. Koordinator FT</p>
    <p><b>Universitas Negeri Gorontalo</b></p>
    <p>Di Gorontalo</p>
</div>

<!-- Paragraf Pembuka -->
<p style="text-align: justify; margin-top: 14px; line-height: 1.35; font-size: 12pt;">
    Sehubungan dengan pelaksanaan Kerja Praktek Mahasiswa Program Studi <?= htmlspecialchars($prodiMhs, ENT_QUOTES, 'UTF-8') ?> Jurusan Teknik Elektro Dan Komputer, maka melalui surat ini kami memohon untuk dapat diterbitkan surat permohonan kerja praktek mahasiswa dengan nama, waktu pelaksanaan, dan lokasi kerja praktek sebagai berikut :
</p>

<!-- Tabel 1: Info Lokasi, Tujuan Surat, Waktu Pelaksanaan -->
<table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12pt;">
    <tr>
        <td style="border: 1px solid #000; padding: 4px 8px; width: 220px;">Lokasi</td>
        <td style="border: 1px solid #000; padding: 4px 8px;"><?= htmlspecialchars($lokasiKp, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 4px 8px;">Tujuan Surat</td>
        <td style="border: 1px solid #000; padding: 4px 8px;"><?= htmlspecialchars($tujuanSurat, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <tr>
        <td style="border: 1px solid #000; padding: 4px 8px;">Waktu Pelaksanaan</td>
        <td style="border: 1px solid #000; padding: 4px 8px;"><?= htmlspecialchars($waktuPelaksanaan, ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
</table>

<!-- Tabel 2: Daftar Mahasiswa -->
<table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12pt;">
    <thead>
        <tr>
            <th style="border: 1px solid #000; padding: 4px 8px; font-weight: normal; text-align: center;">Nama</th>
            <th style="border: 1px solid #000; padding: 4px 8px; font-weight: normal; text-align: center; width: 240px;">NIM</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daftarMahasiswa as $mhs): ?>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 8px;"><?= htmlspecialchars($mhs['nama'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="border: 1px solid #000; padding: 4px 8px; text-align: center;"><?= htmlspecialchars($mhs['nim'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Paragraf Penutup -->
<p style="text-align: justify; margin-top: 14px; line-height: 1.35; font-size: 12pt;">
    Demikian atas perhatian dan kerjasamanya, kami sampaikan terima kasih.
</p>

<!-- Tanda Tangan Ketua Jurusan -->
<table class="ttd-wrap" style="width: 100%; margin-top: 25px; font-size: 12pt;">
    <tr>
        <td></td>
        <td style="width: 260px;">Ketua Jurusan</td>
    </tr>
    <tr class="ttd-place">
        <td></td>
        <td>
            <?php if (!empty($ttdKajur)): ?>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= rawurlencode($qrUrl) ?>" alt="QR Code" style="margin-top:10px;">
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
</table>
<?php } ?>
