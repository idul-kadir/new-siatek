-- ============================================================
-- ROLLBACK SELURUH PERUBAHAN DATABASE OLEH ASISTEN
-- Jalankan HANYA bagian yang memang ingin dibatalkan.
-- Semua statement sengaja aktif; matikan (beri --) yang mau dipertahankan.
-- ============================================================
SET NAMES utf8mb4;

-- ---------- A. Tabel buatan asisten (terakhir, saat fitur Beranda) ----------
DROP TABLE IF EXISTS `skripsi`;
DROP TABLE IF EXISTS `kp`;
DROP TABLE IF EXISTS `surat_penunjukkan`;
DROP TABLE IF EXISTS `agenda`;
DROP TABLE IF EXISTS `berita`;

-- ---------- B. Tabel mahasiswa (dipakai login mahasiswa!) ----------
-- Kalau di-drop, login mahasiswa & portal /mahasiswa tidak akan berfungsi.
DROP TABLE IF EXISTS `mahasiswa`;

-- ---------- C. Reset akun 10041993 (SEBELUM drop kolom) ----------
UPDATE `dosen`
   SET `nama`='Ridwan Kadir', `nidn`='', `keahlian`='',
       `jabatan_fungsional`='', `status_kepegawaian`=NULL,
       `tahun_mulai_bertugas`=NULL, `prodi`=''
 WHERE `nip`='10041993';

-- ---------- D. Kolom tambahan di dosen ----------
ALTER TABLE `dosen` DROP COLUMN `status_kepegawaian`;
ALTER TABLE `dosen` DROP COLUMN `tahun_mulai_bertugas`;

-- ---------- E. Tabel riwayat pendidikan ----------
DROP TABLE IF EXISTS `riwayat_pendidikan`;
-- Password akun ini saat ini: bcrypt dari 'Kuat12345'.
-- Ganti lewat halaman Biodata > Ubah Password bila dirasa perlu.

-- ---------- F. Catatan (tidak perlu dijalankan) ----------
-- * Tabel `prodi` TIDAK di-drop: sudah ada sebelum asisten menyentuh,
--   asisten hanya menambah 3 baris isian. Hapus manual bila mau:
--   DELETE FROM prodi WHERE kode IN ('s1teknikelektro','s1teknikkomputer','d3teknikelektro');
-- * Berita/agenda/surat_penunjukkan/skripsi/kp berisi data contoh dummy.
