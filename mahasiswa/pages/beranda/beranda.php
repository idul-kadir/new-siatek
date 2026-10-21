<?php
/**
 * Halaman: Beranda Mahasiswa (Dasbor Terpadu SIATEK)
 * Desain bersih, fungsional, bebas AI-slop, mengadopsi warna & tipografi halaman Skripsi.
 */

declare(strict_types=1);


if (isset($GLOBALS['koneksi']) && $GLOBALS['koneksi'] instanceof mysqli) {
    mysqli_set_charset($GLOBALS['koneksi'], 'utf8mb4');
}

$nim = $_SESSION['nim'] ?? $_SESSION['username'] ?? $_SESSION['user_id'] ?? '';
$nim_clean = bersihkan($nim);

/* ================================================================
   1. DATA MAHASISWA & PENASIHAT AKADEMIK (PA)
   ================================================================ */
$dMhs = [
    'nama'     => 'Mahasiswa',
    'nim'      => $nim,
    'prodi'    => '-',
    'angkatan' => '-',
    'status'   => 'Aktif',
    'pa'       => 'Belum Ditentukan',
    'nip_pa'   => '-'
];

$qMhs = query("SELECT nama, nim, prodi, angkatan, status, dosen_pa FROM mahasiswa WHERE TRIM(nim) = '$nim_clean' LIMIT 1");
if ($qMhs && mysqli_num_rows($qMhs) > 0) {
    $rowM = mysqli_fetch_assoc($qMhs);
    $dMhs['nama']     = trim((string)$rowM['nama']) ?: 'Mahasiswa';
    $dMhs['nim']      = trim((string)$rowM['nim']) ?: $nim;
    $dMhs['angkatan'] = trim((string)$rowM['angkatan']) ?: '-';
    $dMhs['status']   = trim((string)$rowM['status']) ?: 'Aktif';

    // Resolusi nama prodi lengkap
    $kodeProdi = bersihkan((string)$rowM['prodi']);
    $qProdi = query("SELECT nama FROM prodi WHERE kode = '$kodeProdi' OR nama = '$kodeProdi' LIMIT 1");
    if ($qProdi && mysqli_num_rows($qProdi) > 0) {
        $dMhs['prodi'] = mysqli_fetch_assoc($qProdi)['nama'];
    } else {
        $dMhs['prodi'] = $rowM['prodi'] ?: '-';
    }

    // Resolusi nama & NIP Dosen PA
    if (!empty($rowM['dosen_pa'])) {
        $nipPa = bersihkan((string)$rowM['dosen_pa']);
        $qPa = query("SELECT nama, nip FROM dosen WHERE nip = '$nipPa' OR id = '$nipPa' LIMIT 1");
        if ($qPa && mysqli_num_rows($qPa) > 0) {
            $rPa = mysqli_fetch_assoc($qPa);
            $dMhs['pa']     = $rPa['nama'];
            $dMhs['nip_pa'] = $rPa['nip'] ?: $nipPa;
        } else {
            $dMhs['pa']     = $nipPa;
            $dMhs['nip_pa'] = $nipPa;
        }
    }
}

$isAktif = strtolower(trim((string)$dMhs['status'])) === 'aktif';

/* ================================================================
   2. DATA SKRIPSI / TUGAS AKHIR
   ================================================================ */
$hasSkripsiRecord  = false;
$isSkripsiInactive = false;
$dSkr = [
    'judul'         => 'Belum mengajukan judul skripsi',
    'status'        => 'Belum Terdaftar',
    'pembimbing_1'  => 'Belum Ditentukan',
    'nip_pemb_1'    => '-',
    'pembimbing_2'  => 'Belum Ditentukan',
    'nip_pemb_2'    => '-',
    'ujian_prop'    => 'Belum dijadwalkan',
    'jam_prop'      => '-',
    'ruang_prop'    => '-',
    'ujian_hasil'   => 'Belum dijadwalkan',
    'jam_hasil'     => '-',
    'ruang_hasil'   => '-',
    'ujian_tutup'   => 'Belum dijadwalkan',
    'jam_tutup'     => '-',
    'ruang_tutup'   => '-',
    'sop_aktif'     => 'Menunggu data SOP',
    'total_sop'     => 0,
    'selesai_sop'   => 0,
    'persen_sop'    => 0,
    'urutan_aktif'  => 1
];

$qSkr = query("SELECT * FROM skripsi WHERE nim = '$nim_clean' LIMIT 1");
if ($qSkr && mysqli_num_rows($qSkr) > 0) {
    $rowSkr = mysqli_fetch_assoc($qSkr);
    $hasSkripsiRecord = true;
    $rawStatusSkr = strtolower(trim((string)($rowSkr['status'] ?? '')));
    $isSkripsiInactive = ($rawStatusSkr === 'tidak aktif' || $rawStatusSkr === 'nonaktif');
    $dSkr['status'] = $isSkripsiInactive ? 'Tidak Aktif' : ($rawStatusSkr !== '' ? ucfirst($rawStatusSkr) : 'Aktif');

    $judulSkr = trim((string)($rowSkr['judul'] ?? ''));
    if ($judulSkr !== '') {
        $dSkr['judul'] = $judulSkr;
    }

    // Jadwal Ujian
    if (!empty($rowSkr['tanggal_proposal']) && $rowSkr['tanggal_proposal'] !== '0000-00-00') {
        $dSkr['ujian_prop'] = date('d M Y', strtotime($rowSkr['tanggal_proposal']));
    }
    if (!empty($rowSkr['jam_proposal'])) {
        $dSkr['jam_prop'] = $rowSkr['jam_proposal'];
    } elseif (!empty($rowSkr['jam'])) {
        $dSkr['jam_prop'] = $rowSkr['jam'];
    }
    if (!empty($rowSkr['ruangan_proposal'])) {
        $dSkr['ruang_prop'] = $rowSkr['ruangan_proposal'];
    } elseif (!empty($rowSkr['ruang'])) {
        $dSkr['ruang_prop'] = $rowSkr['ruang'];
    }

    if (!empty($rowSkr['tanggal_hasil']) && $rowSkr['tanggal_hasil'] !== '0000-00-00') {
        $dSkr['ujian_hasil'] = date('d M Y', strtotime($rowSkr['tanggal_hasil']));
    }
    if (!empty($rowSkr['jam_hasil'])) {
        $dSkr['jam_hasil'] = $rowSkr['jam_hasil'];
    }
    if (!empty($rowSkr['ruangan_hasil'])) {
        $dSkr['ruang_hasil'] = $rowSkr['ruangan_hasil'];
    }

    if (!empty($rowSkr['tanggal_tutup']) && $rowSkr['tanggal_tutup'] !== '0000-00-00') {
        $dSkr['ujian_tutup'] = date('d M Y', strtotime($rowSkr['tanggal_tutup']));
    }
    if (!empty($rowSkr['jam_tutup'])) {
        $dSkr['jam_tutup'] = $rowSkr['jam_tutup'];
    }
    if (!empty($rowSkr['ruangan_tutup'])) {
        $dSkr['ruang_tutup'] = $rowSkr['ruangan_tutup'];
    }

    // Fallback Jadwal Hasil & Tutup dari tabel terpisah jika ada
    $tahapanSkr = ['hasil' => 'tgl_ujian', 'tutup' => 'tgl_ujian'];
    foreach ($tahapanSkr as $tabel => $kolomTgl) {
        if ($dSkr['ujian_' . $tabel] !== 'Belum dijadwalkan') continue;
        $cekTabel = query("SHOW TABLES LIKE '$tabel'");
        if ($cekTabel && mysqli_num_rows($cekTabel) > 0) {
            $qJadwal = query("SELECT * FROM $tabel WHERE nim = '$nim_clean' LIMIT 1");
            if ($qJadwal && mysqli_num_rows($qJadwal) > 0) {
                $j = mysqli_fetch_assoc($qJadwal);
                if (!empty($j[$kolomTgl]) && $j[$kolomTgl] !== '0000-00-00') {
                    $dSkr['ujian_' . $tabel] = date('d M Y', strtotime($j[$kolomTgl]));
                }
                if (!empty($j['jam']) && $dSkr['jam_' . $tabel] === '-') {
                    $dSkr['jam_' . $tabel] = $j['jam'];
                }
                if (!empty($j['ruang']) && $dSkr['ruang_' . $tabel] === '-') {
                    $dSkr['ruang_' . $tabel] = $j['ruang'];
                }
            }
        }
    }

    // Pembimbing Skripsi dari surat_penunjukkan
    $noSuratSkr = bersihkan((string)($rowSkr['penunjukkan_pembimbing'] ?? ''));
    $qSuratSkr = ($noSuratSkr !== '')
        ? query("SELECT pembimbing1, pembimbing2 FROM surat_penunjukkan WHERE (id = '$noSuratSkr' OR no_surat = '$noSuratSkr' OR nim = '$nim_clean') AND keterangan LIKE '%PEMBIMBING%' LIMIT 1")
        : query("SELECT pembimbing1, pembimbing2 FROM surat_penunjukkan WHERE nim = '$nim_clean' AND keterangan LIKE '%PEMBIMBING%' AND keterangan NOT LIKE '%KP%' ORDER BY id DESC LIMIT 1");

    if ($qSuratSkr && mysqli_num_rows($qSuratSkr) > 0) {
        $sSkr = mysqli_fetch_assoc($qSuratSkr);
        if (!empty($sSkr['pembimbing1'])) {
            $p1 = bersihkan((string)$sSkr['pembimbing1']);
            $dSkr['nip_pemb_1'] = $p1;
            $qP1 = query("SELECT nama, nip FROM dosen WHERE nip = '$p1' OR id = '$p1' LIMIT 1");
            if ($qP1 && mysqli_num_rows($qP1) > 0) {
                $rP1 = mysqli_fetch_assoc($qP1);
                $dSkr['pembimbing_1'] = $rP1['nama'];
                $dSkr['nip_pemb_1']   = $rP1['nip'] ?: $p1;
            }
        }
        if (!empty($sSkr['pembimbing2'])) {
            $p2 = bersihkan((string)$sSkr['pembimbing2']);
            $dSkr['nip_pemb_2'] = $p2;
            $qP2 = query("SELECT nama, nip FROM dosen WHERE nip = '$p2' OR id = '$p2' LIMIT 1");
            if ($qP2 && mysqli_num_rows($qP2) > 0) {
                $rP2 = mysqli_fetch_assoc($qP2);
                $dSkr['pembimbing_2'] = $rP2['nama'];
                $dSkr['nip_pemb_2']   = $rP2['nip'] ?: $p2;
            }
        }
    }
}

// Progres SOP Skripsi
$currProgSkripsi = get_progres_skripsi($nim_clean);
$lastProsesIdSkripsi = ($currProgSkripsi !== '-') ? (int) $currProgSkripsi : 0;

$qSopSkr = query("SELECT * FROM sop_skripsi ORDER BY CAST(no AS UNSIGNED) ASC");
if ($qSopSkr && mysqli_num_rows($qSopSkr) > 0) {
    $allSopSkr = [];
    while ($r = mysqli_fetch_assoc($qSopSkr)) {
        $allSopSkr[] = $r;
    }
    $dSkr['total_sop'] = count($allSopSkr);

    if ($lastProsesIdSkripsi <= 0) {
        $dSkr['selesai_sop']  = 0;
        $dSkr['urutan_aktif'] = 1;
        $firstSkrKet = $allSopSkr[0]['keterangan'] ?? $allSopSkr[0]['nama'] ?? $allSopSkr[0]['tahapan'] ?? '';
        $dSkr['sop_aktif']    = ($firstSkrKet !== '') ? ucfirst(trim((string)$firstSkrKet)) : 'Pengajuan Berkas';
    } else {
        $countSelesai = 0;
        $judulAktif = '';
        foreach ($allSopSkr as $itemSop) {
            $noSop = (int)$itemSop['no'];
            if ($noSop < $lastProsesIdSkripsi) {
                $countSelesai++;
            } elseif ($noSop === $lastProsesIdSkripsi) {
                $skrKet = $itemSop['keterangan'] ?? $itemSop['nama'] ?? $itemSop['tahapan'] ?? '';
                $judulAktif = ($skrKet !== '') ? ucfirst(trim((string)$skrKet)) : '';
            }
        }
        $dSkr['selesai_sop']  = $countSelesai;
        $dSkr['urutan_aktif'] = $lastProsesIdSkripsi;
        $dSkr['sop_aktif']    = $judulAktif ?: ($lastProsesIdSkripsi >= $dSkr['total_sop'] ? 'Semua tahapan selesai' : 'Tahap ' . $lastProsesIdSkripsi);
    }
    $dSkr['persen_sop'] = $dSkr['total_sop'] > 0 ? (int)round(($dSkr['selesai_sop'] / $dSkr['total_sop']) * 100) : 0;
}

/* ================================================================
   3. DATA KERJA PRAKTEK (KP)
   ================================================================ */
$hasKpRecord  = false;
$isKpInactive = false;
$dKp = [
    'judul'         => 'Belum mengajukan judul Kerja Praktek',
    'instansi'      => 'Belum Ditentukan',
    'lokasi'        => '-',
    'status'        => 'Belum Terdaftar',
    'pembimbing'    => 'Belum Ditentukan',
    'nip_pemb'      => '-',
    'tgl_mulai'     => 'Belum dijadwalkan',
    'tgl_selesai'   => 'Belum dijadwalkan',
    'jadwal_seminar'=> 'Belum dijadwalkan',
    'jam_seminar'   => '-',
    'ruangan_seminar'=> '-',
    'sop_aktif'     => 'Menunggu data SOP',
    'total_sop'     => 0,
    'selesai_sop'   => 0,
    'persen_sop'    => 0,
    'urutan_aktif'  => 1
];

$qKp = query("SELECT * FROM kp WHERE nim = '$nim_clean' LIMIT 1");
if ($qKp && mysqli_num_rows($qKp) > 0) {
    $rowKp = mysqli_fetch_assoc($qKp);
    $hasKpRecord = true;
    $rawStatusKp = strtolower(trim((string)($rowKp['status'] ?? '')));
    $isKpInactive = ($rawStatusKp === 'tidak aktif' || $rawStatusKp === 'nonaktif');
    $dKp['status'] = $isKpInactive ? 'Tidak Aktif' : ($rawStatusKp !== '' ? ucfirst($rawStatusKp) : 'Aktif');

    $judulKp = trim((string)($rowKp['judul'] ?? ''));
    if ($judulKp !== '') {
        $dKp['judul'] = $judulKp;
    }

    $instansi = trim((string)($rowKp['instansi'] ?? $rowKp['tempat_kp'] ?? $rowKp['tempat'] ?? ''));
    if ($instansi === '' && !empty($rowKp['lokasi'])) {
        $instansi = trim((string)$rowKp['lokasi']);
    }
    if ($instansi !== '') {
        $dKp['instansi'] = $instansi;
    }

    $lokasi = trim((string)($rowKp['alamat'] ?? $rowKp['kota'] ?? ''));
    if ($lokasi !== '') {
        $dKp['lokasi'] = $lokasi;
    }

    // Jadwal Seminar KP
    $tglSem = !empty($rowKp['tanggal_seminar']) ? $rowKp['tanggal_seminar'] : ($rowKp['tanggal'] ?? '');
    if (!empty($tglSem) && $tglSem !== '0000-00-00') {
        $dKp['jadwal_seminar'] = date('d M Y', strtotime($tglSem));
    }
    if (!empty($rowKp['jam_seminar'])) {
        $dKp['jam_seminar'] = $rowKp['jam_seminar'];
    } elseif (!empty($rowKp['jam'])) {
        $dKp['jam_seminar'] = $rowKp['jam'];
    }
    if (!empty($rowKp['ruangan_seminar'])) {
        $dKp['ruangan_seminar'] = $rowKp['ruangan_seminar'];
    } elseif (!empty($rowKp['ruangan'])) {
        $dKp['ruangan_seminar'] = $rowKp['ruangan'];
    } elseif (!empty($rowKp['ruang'])) {
        $dKp['ruangan_seminar'] = $rowKp['ruang'];
    }

    // Tanggal Mulai & Selesai KP
    if (!empty($rowKp['tanggal_mulai']) && $rowKp['tanggal_mulai'] !== '0000-00-00') {
        $dKp['tgl_mulai'] = date('d M Y', strtotime($rowKp['tanggal_mulai']));
    }
    if (!empty($rowKp['tanggal_selesai']) && $rowKp['tanggal_selesai'] !== '0000-00-00') {
        $dKp['tgl_selesai'] = date('d M Y', strtotime($rowKp['tanggal_selesai']));
    }

    // Pembimbing KP
    $idSuratBimb = (int)($rowKp['surat_penunjukkan_pembimbing'] ?? 0);
    $qSuratKp = ($idSuratBimb > 0)
        ? query("SELECT pembimbing1 FROM surat_penunjukkan WHERE id = $idSuratBimb LIMIT 1")
        : query("SELECT pembimbing1 FROM surat_penunjukkan WHERE nim = '$nim_clean' AND keterangan LIKE '%PEMBIMBING%KP%' ORDER BY id DESC LIMIT 1");
    if ($qSuratKp && mysqli_num_rows($qSuratKp) > 0) {
        $sKp = mysqli_fetch_assoc($qSuratKp);
        $idP1Kp = bersihkan((string)($sKp['pembimbing1'] ?? ''));
        if ($idP1Kp !== '') {
            $dKp['nip_pemb'] = $idP1Kp;
            $qDsnKp = query("SELECT nama, nip FROM dosen WHERE id = '$idP1Kp' OR nip = '$idP1Kp' LIMIT 1");
            if ($qDsnKp && mysqli_num_rows($qDsnKp) > 0) {
                $rdKp = mysqli_fetch_assoc($qDsnKp);
                $dKp['pembimbing'] = $rdKp['nama'];
                $dKp['nip_pemb']   = $rdKp['nip'] ?: $idP1Kp;
            }
        }
    }
}

// Progres SOP KP
$currProgKp = get_progres_kp($nim_clean);
$lastProsesIdKp = ($currProgKp !== '-') ? (int) $currProgKp : 0;

$allSopKp = function_exists('get_sop_kp_list') ? get_sop_kp_list() : [];
if (empty($allSopKp)) {
    $qSopKp = query("SELECT * FROM sop_kp");
    if ($qSopKp && mysqli_num_rows($qSopKp) > 0) {
        while ($r = mysqli_fetch_assoc($qSopKp)) {
            $noVal  = $r['no'] ?? $r['id'] ?? $r['proses'] ?? $r['nomor'] ?? reset($r);
            $ketVal = $r['keterangan'] ?? $r['nama'] ?? $r['tahapan'] ?? $r['deskripsi'] ?? $r['judul'] ?? '';
            $allSopKp[] = [
                'no'         => (int)$noVal,
                'keterangan' => trim((string)$ketVal)
            ];
        }
        usort($allSopKp, fn($a, $b) => $a['no'] <=> $b['no']);
    }
}

if (!empty($allSopKp)) {
    $dKp['total_sop'] = count($allSopKp);

    if ($lastProsesIdKp <= 0) {
        $dKp['selesai_sop']  = 0;
        $dKp['urutan_aktif'] = 1;
        $firstKet = $allSopKp[0]['keterangan'] ?? '';
        $dKp['sop_aktif']    = ($firstKet !== '') ? ucfirst($firstKet) : 'Pengajuan Berkas KP';
    } else {
        $countSelesaiKp = 0;
        $judulAktifKp = '';
        foreach ($allSopKp as $itemSop) {
            $noSop = (int)$itemSop['no'];
            if ($noSop < $lastProsesIdKp) {
                $countSelesaiKp++;
            } elseif ($noSop === $lastProsesIdKp) {
                $ketText = $itemSop['keterangan'] ?? '';
                $judulAktifKp = ($ketText !== '') ? ucfirst(trim((string)$ketText)) : '';
            }
        }
        $dKp['selesai_sop']  = $countSelesaiKp;
        $dKp['urutan_aktif'] = $lastProsesIdKp;
        $dKp['sop_aktif']    = $judulAktifKp ?: ($lastProsesIdKp >= $dKp['total_sop'] ? 'Semua tahapan selesai' : 'Tahap ' . $lastProsesIdKp);
    }
    $dKp['persen_sop'] = $dKp['total_sop'] > 0 ? (int)round(($dKp['selesai_sop'] / $dKp['total_sop']) * 100) : 0;
}

/* ================================================================
   4. DATA JADWAL KULIAH HARI INI
   ================================================================ */
$hariMap = [
    'Sunday' => 'MINGGU', 'Monday' => 'SENIN', 'Tuesday' => 'SELASA',
    'Wednesday' => 'RABU', 'Thursday' => 'KAMIS', 'Friday' => 'JUMAT', 'Saturday' => 'SABTU'
];
$hariIni = $hariMap[date('l')] ?? 'SENIN';
$hariIndo = [
    'SENIN' => 'Senin', 'SELASA' => 'Selasa', 'RABU' => 'Rabu',
    'KAMIS' => 'Kamis', 'JUMAT' => 'Jumat', 'SABTU' => 'Sabtu', 'MINGGU' => 'Minggu'
];
$namaHariIni = $hariIndo[$hariIni] ?? 'Senin';

$jadwalHariIni = [];

// Query jadwal kuliah hari ini dengan relasi lengkap ke matakuliah, kurikulum, prodi, ruang_kuliah, dan dosen
$qJadwal = query("
    SELECT jk.id, jk.hari, jk.jam, jk.kelas, jk.ruangan, jk.tahun_ajaran,
           rk.nama AS ruangan_nama,
           mk.nama AS nama_matkul, mk.sks AS sks_matkul,
           p.nama AS prodi_nama, k.prodi AS prodi_kode,
           d1.nama AS nama_dosen1, d2.nama AS nama_dosen2
    FROM jadwal_kuliah jk
    LEFT JOIN matakuliah mk ON jk.id_matakuliah = mk.id
    LEFT JOIN dosen d1 ON (jk.pengajar1 = d1.id OR jk.pengajar1 = d1.nip)
    LEFT JOIN dosen d2 ON (jk.pengajar2 = d2.id OR jk.pengajar2 = d2.nip)
    LEFT JOIN ruang_kuliah rk ON jk.ruangan = rk.id
    LEFT JOIN kurikulum k ON mk.kurikulum = k.kode
    LEFT JOIN prodi p ON k.prodi = p.kode
    WHERE UPPER(jk.hari) = '$hariIni' AND (k.prodi = '$kodeProdi' OR p.kode = '$kodeProdi' OR p.nama = '{$dMhs['prodi']}')
    ORDER BY jk.jam ASC
");

// Fallback jika tidak ada data spesifik prodi pada hari ini, tampilkan semua jadwal aktif hari ini
if (!$qJadwal || mysqli_num_rows($qJadwal) === 0) {
    $qJadwal = query("
        SELECT jk.id, jk.hari, jk.jam, jk.kelas, jk.ruangan, jk.tahun_ajaran,
               rk.nama AS ruangan_nama,
               mk.nama AS nama_matkul, mk.sks AS sks_matkul,
               p.nama AS prodi_nama, k.prodi AS prodi_kode,
               d1.nama AS nama_dosen1, d2.nama AS nama_dosen2
        FROM jadwal_kuliah jk
        LEFT JOIN matakuliah mk ON jk.id_matakuliah = mk.id
        LEFT JOIN dosen d1 ON (jk.pengajar1 = d1.id OR jk.pengajar1 = d1.nip)
        LEFT JOIN dosen d2 ON (jk.pengajar2 = d2.id OR jk.pengajar2 = d2.nip)
        LEFT JOIN ruang_kuliah rk ON jk.ruangan = rk.id
        LEFT JOIN kurikulum k ON mk.kurikulum = k.kode
        LEFT JOIN prodi p ON k.prodi = p.kode
        WHERE UPPER(jk.hari) = '$hariIni'
        ORDER BY jk.jam ASC
    ");
}

if ($qJadwal && mysqli_num_rows($qJadwal) > 0) {
    while ($r = mysqli_fetch_assoc($qJadwal)) {
        $namaMatkul = trim((string)($r['nama_matkul'] ?? ''));

        // Dosen pengampu (mendukung multi pengajar / team teaching)
        $listDosen = [];
        if (!empty($r['nama_dosen1'])) $listDosen[] = trim((string)$r['nama_dosen1']);
        if (!empty($r['nama_dosen2'])) $listDosen[] = trim((string)$r['nama_dosen2']);

        // Ruangan & Kelas
        $ruangan = !empty($r['ruangan_nama']) ? trim((string)$r['ruangan_nama']) : (!empty($r['ruangan']) ? trim((string)$r['ruangan']) : '-');
        $kelas   = !empty($r['kelas']) ? trim((string)$r['kelas']) : '-';

        // Program Studi
        $prodiItem = !empty($r['prodi_nama']) ? trim((string)$r['prodi_nama']) : (!empty($r['prodi_kode']) ? trim((string)$r['prodi_kode']) : $dMhs['prodi']);

        $jadwalHariIni[] = [
            'jam'         => trim((string)($r['jam'] ?? '-')),
            'mata_kuliah' => $namaMatkul !== '' ? $namaMatkul : 'Mata Kuliah',
            'prodi'       => $prodiItem,
            'sks'         => (int)($r['sks_matkul'] ?? 2),
            'dosen'       => $listDosen,
            'ruangan'     => $ruangan,
            'kelas'       => $kelas,
        ];
    }
}
?>

<style>
    .content-scroll { overflow-y: auto; height: calc(100vh - 64px); }
    .progres-bar-track { height: 7px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
    .progres-bar-fill  { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #1a365d, #f97316); transition: width .5s ease; }
    @keyframes riseIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .4s cubic-bezier(.16,1,.3,1) forwards; }
</style>

<main class="content-area content-scroll p-4 sm:p-6 space-y-6">

    <!-- ==============================================================
         1. HEADER IDENTITAS MAHASISWA & PENASIHAT AKADEMIK (PA)
         ============================================================== -->
    <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm reveal">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            
            <!-- Kiri: Sambutan & Identitas Mahasiswa -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-xl text-orange-600 flex-shrink-0">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-800">
                                Selamat Datang, <?= htmlspecialchars($dMhs['nama']) ?>
                            </h1>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-bold <?= $isAktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' ?>">
                                <?php if ($isAktif): ?>
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                    </span>
                                <?php endif; ?>
                                <?= $isAktif ? 'Mahasiswa Aktif' : htmlspecialchars($dMhs['status']) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Portal Layanan Akademik Skripsi & Kerja Praktek (SIATEK)
                        </p>
                    </div>
                </div>

                <!-- Badge Identitas Mahasiswa (Gaya Presisi Halaman Skripsi) -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-bold shadow-sm">
                        <i class="fas fa-id-card text-orange-400"></i>NIM <?= htmlspecialchars($dMhs['nim']) ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 text-sky-800 px-3 py-1 text-xs font-bold">
                        <i class="fas fa-graduation-cap"></i><?= htmlspecialchars($dMhs['prodi']) ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-xs font-bold">
                        <i class="fas fa-calendar-alt"></i>Angkatan <?= htmlspecialchars($dMhs['angkatan']) ?>
                    </span>
                </div>
            </div>

            <!-- Kanan: Penasihat Akademik (PA) Card -->
            <div class="lg:w-80 flex-shrink-0 bg-slate-50 rounded-xl border border-slate-200/80 p-3.5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 flex-shrink-0 text-sm">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Penasihat Akademik (PA)</p>
                    <p class="text-xs font-bold text-slate-800 truncate" title="<?= htmlspecialchars($dMhs['pa']) ?>">
                        <?= htmlspecialchars($dMhs['pa']) ?>
                    </p>
                    <p class="text-[10px] text-slate-500">
                        NIP. <?= htmlspecialchars($dMhs['nip_pa']) ?>
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- ==============================================================
         2. DUAL MONITORING AKADEMIK (SKRIPSI & KERJA PRAKTEK)
         ============================================================== -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 reveal">

        <!-- ===== KARTU SKRIPSI / TUGAS AKHIR ===== -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                
                <!-- Header Kartu Skripsi -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-100 text-orange-600 text-sm">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Skripsi / Tugas Akhir</h2>
                            <p class="text-[11px] text-slate-400">Lembar Aktivitas Sesuai Prosedur SOP</p>
                        </div>
                    </div>
                    <?php if (!$hasSkripsiRecord): ?>
                        <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-0.5 text-[11px] font-bold">
                            Belum Terdaftar
                        </span>
                    <?php elseif ($isSkripsiInactive): ?>
                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2.5 py-0.5 text-[11px] font-bold">
                            Non-Aktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 text-orange-700 px-2.5 py-0.5 text-[11px] font-bold">
                            <i class="fas fa-list-check text-[10px]"></i><?= $dSkr['selesai_sop'] ?>/<?= $dSkr['total_sop'] ?> SOP
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Judul Skripsi -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Judul Skripsi</p>
                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3">
                        <p class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($dSkr['judul']) ?>
                        </p>
                    </div>
                </div>

                <!-- Progress Bar SOP Skripsi -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-bold text-slate-600">
                            Tahap Aktif: <strong class="text-orange-600"><?= htmlspecialchars($dSkr['sop_aktif']) ?></strong>
                        </span>
                        <span class="text-[11px] font-extrabold text-slate-700"><?= $dSkr['persen_sop'] ?>%</span>
                    </div>
                    <div class="progres-bar-track">
                        <div class="progres-bar-fill" style="width: <?= min(100, max(0, $dSkr['persen_sop'])) ?>%;"></div>
                    </div>
                </div>

                <!-- Dosen Pembimbing Skripsi -->
                <div class="pt-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Dosen Pembimbing</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="rounded-lg border border-slate-100 bg-slate-50/70 p-2.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Pembimbing 1</p>
                            <p class="text-[11px] font-bold text-slate-800 truncate" title="<?= htmlspecialchars($dSkr['pembimbing_1']) ?>">
                                <?= htmlspecialchars($dSkr['pembimbing_1']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400">NIP. <?= htmlspecialchars($dSkr['nip_pemb_1']) ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-100 bg-slate-50/70 p-2.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Pembimbing 2</p>
                            <p class="text-[11px] font-bold text-slate-800 truncate" title="<?= htmlspecialchars($dSkr['pembimbing_2']) ?>">
                                <?= htmlspecialchars($dSkr['pembimbing_2']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400">NIP. <?= htmlspecialchars($dSkr['nip_pemb_2']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Jadwal Ujian Sejajar -->
                <div class="pt-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Jadwal Ujian Skripsi</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2 text-center">
                            <p class="text-[9px] font-bold uppercase text-orange-600">Proposal</p>
                            <p class="text-[10px] font-bold text-slate-700 truncate"><?= htmlspecialchars($dSkr['ujian_prop']) ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2 text-center">
                            <p class="text-[9px] font-bold uppercase text-sky-600">Hasil</p>
                            <p class="text-[10px] font-bold text-slate-700 truncate"><?= htmlspecialchars($dSkr['ujian_hasil']) ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2 text-center">
                            <p class="text-[9px] font-bold uppercase text-emerald-600">Tutup</p>
                            <p class="text-[10px] font-bold text-slate-700 truncate"><?= htmlspecialchars($dSkr['ujian_tutup']) ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi Skripsi -->
            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">
                    <i class="fas fa-info-circle mr-1 text-slate-400"></i>Periksa tahapan SOP & berkas
                </span>
                <a href="/redesain-siatek/mahasiswa/skripsi" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-3.5 py-1.5 text-xs font-bold shadow-sm transition">
                    <span>Buka Skripsi</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- ===== KARTU KERJA PRAKTEK (KP) ===== -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                
                <!-- Header Kartu KP -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-600 text-sm">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Kerja Praktek (KP)</h2>
                            <p class="text-[11px] text-slate-400">Pengajuan, Bimbingan & Seminar KP</p>
                        </div>
                    </div>
                    <?php if (!$hasKpRecord): ?>
                        <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-2.5 py-0.5 text-[11px] font-bold">
                            Belum Terdaftar
                        </span>
                    <?php elseif ($isKpInactive): ?>
                        <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2.5 py-0.5 text-[11px] font-bold">
                            Non-Aktif
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 text-sky-700 px-2.5 py-0.5 text-[11px] font-bold">
                            <i class="fas fa-list-check text-[10px]"></i><?= $dKp['selesai_sop'] ?>/<?= $dKp['total_sop'] ?> SOP
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Judul & Instansi KP -->
                <div class="space-y-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Instansi / Judul KP</p>
                    <div class="rounded-lg bg-slate-50 border border-slate-100 p-3 space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                            <i class="fas fa-building text-sky-500 text-[11px]"></i>
                            <span class="truncate"><?= htmlspecialchars($dKp['instansi']) ?></span>
                        </div>
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($dKp['judul']) ?>
                        </p>
                    </div>
                </div>

                <!-- Progress Bar SOP KP -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-bold text-slate-600">
                            Tahap Aktif: <strong class="text-sky-600"><?= htmlspecialchars($dKp['sop_aktif']) ?></strong>
                        </span>
                        <span class="text-[11px] font-extrabold text-slate-700"><?= $dKp['persen_sop'] ?>%</span>
                    </div>
                    <div class="progres-bar-track">
                        <div class="progres-bar-fill" style="width: <?= min(100, max(0, $dKp['persen_sop'])) ?>%;"></div>
                    </div>
                </div>

                <!-- Dosen Pembimbing KP -->
                <div class="pt-2">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">Dosen Pembimbing KP</p>
                    <div class="rounded-lg border border-slate-100 bg-slate-50/70 p-2.5">
                        <p class="text-[9px] font-bold text-slate-400 uppercase">Pembimbing Lapangan / Akademik</p>
                        <p class="text-[11px] font-bold text-slate-800 truncate" title="<?= htmlspecialchars($dKp['pembimbing']) ?>">
                            <?= htmlspecialchars($dKp['pembimbing']) ?>
                        </p>
                        <p class="text-[9px] text-slate-400">NIP. <?= htmlspecialchars($dKp['nip_pemb']) ?></p>
                    </div>
                </div>

                <!-- Periode Pelaksanaan & Seminar KP -->
                <div class="pt-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Jadwal Kerja Praktek</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2">
                            <p class="text-[9px] font-bold uppercase text-sky-600">Periode KP</p>
                            <p class="text-[10px] font-bold text-slate-700 truncate">
                                <?= ($dKp['tgl_mulai'] !== 'Belum dijadwalkan') ? htmlspecialchars($dKp['tgl_mulai']) : '-' ?> s/d <?= ($dKp['tgl_selesai'] !== 'Belum dijadwalkan') ? htmlspecialchars($dKp['tgl_selesai']) : '-' ?>
                            </p>
                        </div>
                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2">
                            <p class="text-[9px] font-bold uppercase text-emerald-600">Seminar KP</p>
                            <p class="text-[10px] font-bold text-slate-700 truncate"><?= htmlspecialchars($dKp['jadwal_seminar']) ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi KP -->
            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">
                    <i class="fas fa-info-circle mr-1 text-slate-400"></i>Periksa tahapan SOP KP & seminar
                </span>
                <a href="/redesain-siatek/mahasiswa/kp" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white px-3.5 py-1.5 text-xs font-bold shadow-sm transition">
                    <span>Buka Kerja Praktek</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </section>

    <!-- ==============================================================
         3. JADWAL KULIAH HARI INI
         ============================================================== -->
    <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm reveal">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 text-base flex-shrink-0">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800">
                        Jadwal Kuliah Hari Ini — <?= htmlspecialchars($namaHariIni) ?>
                    </h3>
                    <p class="text-xs text-slate-400">Sesi perkuliahan yang terjadwal aktif pada hari ini</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                <i class="fas fa-clock text-slate-400"></i><?= count($jadwalHariIni) ?> Kelas Terjadwal
            </span>
        </div>

        <!-- Tabel Jadwal Kuliah Hari Ini -->
        <div class="mt-4 overflow-x-auto">
            <?php if (empty($jadwalHariIni)): ?>
                <div class="p-8 text-center rounded-xl border border-dashed border-slate-200 bg-slate-50/60">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-200 text-slate-400 mb-2 text-xl">
                        <i class="fas fa-calendar-check text-emerald-500"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Tidak Ada Perkuliahan Hari Ini (<?= htmlspecialchars($namaHariIni) ?>)</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Tidak ada mata kuliah yang terjadwal berlangsung pada hari ini.
                    </p>
                </div>
            <?php else: ?>
                <table class="w-full text-left text-xs" id="tabelJadwalKuliah">
                    <thead>
                        <tr class="border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                            <th class="py-2.5 px-3">Jam</th>
                            <th class="py-2.5 px-3">Mata Kuliah</th>
                            <th class="py-2.5 px-3">Program Studi</th>
                            <th class="py-2.5 px-3 text-center">SKS</th>
                            <th class="py-2.5 px-3">Dosen Pengampu</th>
                            <th class="py-2.5 px-3">Ruangan &amp; Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($jadwalHariIni as $jh): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 align-top whitespace-nowrap">
                                    <span class="font-bold text-slate-800 block text-xs">
                                        <i class="fas fa-clock text-orange-500 mr-1 text-[11px]"></i><?= htmlspecialchars($jh['jam']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 align-top">
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm">
                                        <?= htmlspecialchars($jh['mata_kuliah']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded bg-sky-50 text-sky-700 px-2 py-0.5 text-[11px] font-semibold border border-sky-100">
                                        <i class="fas fa-graduation-cap text-[10px]"></i><?= htmlspecialchars($jh['prodi']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 align-top text-center whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 text-[10px] font-bold">
                                        <?= (int)$jh['sks'] ?> SKS
                                    </span>
                                </td>
                                <td class="py-3 px-3 align-top text-slate-700">
                                    <?php if (empty($jh['dosen'])): ?>
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    <?php elseif (count($jh['dosen']) === 1): ?>
                                        <div class="flex items-start gap-1.5">
                                            <i class="fas fa-user-tie text-slate-400 text-[11px] mt-0.5 flex-shrink-0"></i>
                                            <span class="font-medium text-slate-800 text-xs"><?= htmlspecialchars($jh['dosen'][0]) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="space-y-1.5">
                                            <?php foreach ($jh['dosen'] as $idx => $dosenNama): ?>
                                                <div class="flex items-start gap-1.5">
                                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded bg-slate-100 text-[9px] font-bold text-slate-600 flex-shrink-0 mt-0.5 border border-slate-200">
                                                        <?= $idx + 1 ?>
                                                    </span>
                                                    <span class="font-medium text-slate-800 text-xs leading-snug">
                                                        <?= htmlspecialchars($dosenNama) ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 align-top whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                        <i class="fas fa-location-dot text-orange-500 text-[10px]"></i>
                                        <span><?= htmlspecialchars($jh['ruangan']) ?></span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span class="text-slate-800 font-bold">Kelas <?= htmlspecialchars($jh['kelas']) ?></span>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    <!-- ==============================================================
         4. INFORMASI PANDUAN & LAYANAN AKADEMIK
         ============================================================== -->
    <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm reveal">
        <div class="flex flex-col md:flex-row items-start gap-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 flex-shrink-0 text-base">
                <i class="fas fa-circle-question"></i>
            </div>
            <div class="flex-1 space-y-2">
                <h3 class="text-sm font-bold text-slate-800">Panduan Prosedur Administrasi Akademik</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap tahapan pada <strong class="text-slate-800">Skripsi</strong> dan <strong class="text-slate-800">Kerja Praktek</strong> diproses secara berurutan sesuai SOP. Pastikan dokumen persyaratan telah diunggah dalam format PDF/dokumen yang valid. Apabila Anda membutuhkan validasi surat penunjukkan atau aktivasi berkas, silakan menghadap <strong class="text-slate-800">Operator Program Studi / Jurusan</strong> pada jam layanan operasional.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-[11px] text-slate-500 border-t border-slate-100">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-clock text-orange-500"></i>Jam Layanan: Senin - Jumat (08.00 - 16.00 WITA)
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-sky-500"></i>Ruang Pelayanan Akademik Jurusan Teknik Elektro & Komputer
                    </span>
                </div>
            </div>
        </div>
    </section>

</main>
