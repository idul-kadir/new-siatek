-- ============================================================
-- MIGRASI LOGIN MAHASISWA
-- Satu pintu login: staf dari tabel dosen, mahasiswa dari tabel baru ini.
-- Password: bcrypt (password_hash).
-- Kolom mengikuti kebutuhan dashboard mahasiswa tahap awal.
-- ============================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `mahasiswa` (
  `nim`        VARCHAR(20)      NOT NULL,
  `nama`       VARCHAR(150)     NOT NULL,
  `password`   VARCHAR(255)     NULL,
  `email`      VARCHAR(100)     NULL,
  `no_hp`      VARCHAR(20)      NULL,
  `prodi`      VARCHAR(60)      NULL,
  `angkatan`   SMALLINT UNSIGNED NULL,
  `status`     ENUM('aktif','lulus','nonaktif') NOT NULL DEFAULT 'aktif',
  `foto`       VARCHAR(255)     NULL,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nim`),
  KEY `idx_mhs_nama` (`nama`),
  KEY `idx_mhs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun contoh (dummy): NIM 521421011, password: Kuat12345
INSERT IGNORE INTO `mahasiswa` (`nim`,`nama`,`password`,`prodi`,`angkatan`,`status`)
VALUES ('521421011','Mohamad Ryan Noor Sahidu',
        '$2y$10$qq4YdwU9wzHPH/KpMI65SuIsxKje.E3erdQq3B3nNqy0UNpKI30Nu', -- bcrypt dari "Kuat12345"
        'S1 Teknik Elektro',2021,'aktif');
