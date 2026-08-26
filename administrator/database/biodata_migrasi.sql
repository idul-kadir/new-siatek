-- ============================================================
-- MIGRASI BIODATA — menyesuaikan DB `siatek` dgn halaman Biodata
-- Prinsip: tabel `dosen` yang sudah ada (37 baris, PK = nip)
--          TETAP dipakai sebagai sumber identitas + login.
--          Yang ditambah hanya yang belum ada:
--            1. 2 kolom kecil di `dosen` (aman utk kode lama)
--            2. Anak-tabel riwayat pendidikan (TK s.d. S3)
-- Bidang ilmu dinamis  -> kolom lama `keahlian` (pisah koma)
-- Homebase             -> kolom lama `prodi`
-- Email / No HP / Foto -> kolom lama email / no_telp / profil
-- Password & level     -> kolom lama password / level (tetap)
-- ============================================================

SET NAMES utf8mb4;

-- 1. Kolom tambahan di dosen (nullable — tidak mengganggu data/kode lama)
--    Catatan: jalankan sekali; MySQL 8 tidak punya ADD COLUMN IF NOT EXISTS.
ALTER TABLE `dosen`
  ADD COLUMN `status_kepegawaian` VARCHAR(20) NULL
    COMMENT 'PNS/PPPK/Kontrak' AFTER `jabatan_fungsional`,
  ADD COLUMN `tahun_mulai_bertugas` SMALLINT UNSIGNED NULL
    COMMENT 'Tahun mulai bekerja' AFTER `status_kepegawaian`;

-- 2. Riwayat pendidikan multi-baris per orang
--    Catatan: tanpa FOREIGN KEY karena `dosen` masih MyISAM
--    (FK tidak didukung); integritas nip dijaga di level aplikasi.
CREATE TABLE IF NOT EXISTS `riwayat_pendidikan` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nip`           VARCHAR(25)      NOT NULL,
  `jenjang`       ENUM('TK','SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3') NOT NULL,
  `program_studi` VARCHAR(150)     NOT NULL,
  `universitas`   VARCHAR(150)     NOT NULL,
  `tahun`         SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rp_nip` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
