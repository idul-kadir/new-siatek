-- ============================================================
-- MIGRASI BERANDA - melengkapi tabel yang dibutuhkan dashboard
-- Sumber kebutuhan: administrator/pages/beranda/beranda.php
--   prodi(kode,nama) | skripsi | kp | surat_penunjukkan | agenda | berita
-- Catatan: tabel mahasiswa sudah ada; kolom status disesuaikan agar
--          cocok dgn kosakata halaman ('Aktif'/'Alumni').
-- ============================================================

SET NAMES utf8mb4;

-- ---------- PRODI ----------
CREATE TABLE IF NOT EXISTS `prodi` (
  `kode` VARCHAR(30)  NOT NULL,
  `nama` VARCHAR(80)  NOT NULL,
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `prodi` (`kode`,`nama`) VALUES
  ('s1teknikelektro',  'S1 Teknik Elektro'),
  ('s1teknikkomputer', 'S1 Teknik Komputer'),
  ('d3teknikelektro',  'D3 Teknik Elektro');

-- ---------- MAHASISWA: samakan kosakata status ----------
ALTER TABLE `mahasiswa`
  MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'Aktif';
UPDATE `mahasiswa` SET `status`='Aktif', `prodi`='s1teknikelektro' WHERE `nim`='521421011';

INSERT IGNORE INTO `mahasiswa` (`nim`,`nama`,`password`,`email`,`prodi`,`angkatan`,`status`) VALUES
  ('521420025','Salsabila Buka',            NULL,'salsabila@ung.ac.id','s1teknikelektro', 2021,'Aktif'),
  ('521422062','Alya Pratiwi Putri Junus',  NULL,'alya@ung.ac.id',     's1teknikkomputer',2022,'Aktif'),
  ('521422100','Fadil Rahman',              NULL,'fadil@ung.ac.id',    's1teknikkomputer',2026,'Aktif'),
  ('531423001','Andi Saputra',              NULL,'andi@ung.ac.id',     'd3teknikelektro', 2023,'Aktif'),
  ('521418010','Rina Oktaviani',            NULL,'rina@ung.ac.id',     's1teknikelektro', 2018,'Alumni');

-- ---------- SKRIPSI ----------
CREATE TABLE IF NOT EXISTS `skripsi` (
  `id`     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nim`    VARCHAR(20)     NOT NULL,
  `judul`  VARCHAR(255)    NULL,
  `status` VARCHAR(20)     NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  KEY `idx_skripsi_nim` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `skripsi` (`nim`,`judul`,`status`) VALUES
  ('521421011','Evaluasi Quality of Service Jaringan RT/RW Net Fiber Optik','aktif'),
  ('521420025','Sistem Monitoring Kualitas Air Berbasis IoT','aktif');

-- ---------- KERJA PRAKTEK ----------
CREATE TABLE IF NOT EXISTS `kp` (
  `id`     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nim`    VARCHAR(20)     NOT NULL,
  `judul`  VARCHAR(255)    NULL,
  `status` VARCHAR(20)     NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  KEY `idx_kp_nim` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kp` (`nim`,`judul`,`status`) VALUES
  ('521422062','Perancangan Dashboard Monitoring Jaringan','aktif');

-- ---------- SURAT PENUNJUKKAN ----------
CREATE TABLE IF NOT EXISTS `surat_penunjukkan` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nim`     VARCHAR(20)   NOT NULL,
  `jenis`   VARCHAR(30)   NOT NULL DEFAULT 'pembimbing',
  `nomor`   VARCHAR(60)   NULL,
  `tanggal` DATE          NULL,
  `ttd`     VARCHAR(255)  NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_sp_nim` (`nim`),
  KEY `idx_sp_ttd` (`ttd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `surat_penunjukkan` (`nim`,`jenis`,`nomor`,`tanggal`,`ttd`) VALUES
  ('521421011','Pembimbing Skripsi','300/UN47.B5.5/TD.06/2024','2024-10-01',''),
  ('521422062','Penguji Hasil',    'B/TE/2026/097',           '2026-02-28',''),
  ('521420025','Pembimbing KP',    'B/TE/2026/101',           '2026-08-10',''),
  ('521420025','Penguji Tutup',    'B/TE/2026/133',           '2026-06-09','sudah');

-- ---------- AGENDA (tanggal unix; tanggal_fmt auto) ----------
CREATE TABLE IF NOT EXISTS `agenda` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `penulis`     VARCHAR(25)  NULL,
  `agenda`      VARCHAR(200) NOT NULL,
  `tanggal`     BIGINT       NOT NULL,
  `tanggal_fmt` VARCHAR(40)  GENERATED ALWAYS AS
                (DATE_FORMAT(FROM_UNIXTIME(`tanggal`),'%d %M %Y')) STORED,
  PRIMARY KEY (`id`),
  KEY `idx_agenda_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `agenda` (`penulis`,`agenda`,`tanggal`) VALUES
  ('197102222001121001','Sidang Proposal Skripsi Periode September', UNIX_TIMESTAMP() + 2*86400),
  ('10041993',          'Rapat Koordinasi Laboratorium',             UNIX_TIMESTAMP() + 5*86400);

-- ---------- BERITA (skema legacy kompatibel blueprint 11.6) ----------
CREATE TABLE IF NOT EXISTS `berita` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul`      VARCHAR(200) NOT NULL DEFAULT '',
  `gambar`     VARCHAR(250) NOT NULL DEFAULT '',
  `isi`        TEXT         NULL,
  `deskripsi`  VARCHAR(255) NOT NULL DEFAULT '',
  `sumber`     VARCHAR(200) NOT NULL DEFAULT '',
  `kategori`   VARCHAR(200) NOT NULL DEFAULT '',
  `penulis`    VARCHAR(200) NOT NULL DEFAULT '',
  `tanggal`    VARCHAR(50)  NOT NULL DEFAULT '',
  `log`        INT          NOT NULL DEFAULT 0,
  `keterangan` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_berita_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `berita` (`judul`,`deskripsi`,`penulis`,`tanggal`) VALUES
  ('Jadwal Kuliah Semester Ganjil TA 2026/2027 Ditetapkan','Jadwal perkuliahan seluruh program studi sudah ditetapkan dan dapat diakses mahasiswa.','Humas JTEK','2026-08-24'),
  ('Pendaftaran Praktikum Laboratorium Dibuka','Pendaftaran praktikum semester ganjil dibuka hingga akhir bulan ini.','Laboran JTEK','2026-08-23');
