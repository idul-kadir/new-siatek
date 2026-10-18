-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 18, 2026 at 12:00 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siatek`
--

-- --------------------------------------------------------

--
-- Table structure for table `arsip_jurusan`
--

CREATE TABLE `arsip_jurusan` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` year NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengupload` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `arsip_lain`
--

CREATE TABLE `arsip_lain` (
  `id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` int NOT NULL,
  `semester` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pengupload` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_akses` set('private','terbatas','publik') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arsip_lain`
--

INSERT INTO `arsip_lain` (`id`, `deskripsi`, `tahun`, `semester`, `file`, `pengupload`, `jenis_akses`) VALUES
('1790747466_7cb2fa865b25dda9', 'tanpa tanda', 2026, '-', '1790747466_bb521b0ccb04ec8e', '2e7b39ada2', 'private'),
('1790750932_26cfbd84bd66e5b9', 'cie cie', 2026, '-', '1790750932_07f1f191da82f5ec', '2e7b39ada2', 'terbatas'),
('1790751423_aebaf2bd6ca2dbda', 'coba ini wa', 2024, '-', '1790751423_7750230728396129', '5864493a02', 'terbatas'),
('1790752665_ecb0c25e289448cb', 'ini powli publik', 2026, '-', '1790752665_6ff035cf45f76329', '2e7b39ada2', 'publik');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int NOT NULL,
  `judul` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isi` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sumber` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `kategori` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `penulis` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `log` int NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `berkas_kp`
--

CREATE TABLE `berkas_kp` (
  `id` int NOT NULL,
  `id_persyaratan` int NOT NULL,
  `nim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `berkas_skripsi`
--

CREATE TABLE `berkas_skripsi` (
  `id` int NOT NULL,
  `id_persyaratan` int NOT NULL,
  `nim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berkas_skripsi`
--

INSERT INTO `berkas_skripsi` (`id`, `id_persyaratan`, `nim`, `file`) VALUES
(10, 1, '05201425003', '1791769835_301ec1ca66c31e4d'),
(11, 2, '05201425003', '1791769840_8dbad05d9c1458c5'),
(12, 3, '05201425003', '1791769846_5f11576331fd8b54'),
(13, 48, '05201425003', '1791769864_3ef6743109bd154a'),
(14, 49, '05201425003', '1791769878_ff1398794a4ec98d'),
(15, 50, '05201425003', '1791769884_417514527154cb89'),
(43, 56, '05201425003', '1791959092_21a2f49d4e449b28'),
(44, 57, '05201425003', '1791959096_ba88b4b62fb4dac7'),
(45, 58, '05201425003', '1791959102_c6cd4a0fd88de42f');

-- --------------------------------------------------------

--
-- Table structure for table `bukti_kerjasama`
--

CREATE TABLE `bukti_kerjasama` (
  `id` int NOT NULL,
  `id_kerjasama` int DEFAULT NULL,
  `nama_bukti` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bukti_kerjasama`
--

INSERT INTO `bukti_kerjasama` (`id`, `id_kerjasama`, `nama_bukti`, `file`) VALUES
(5, 1789018320, 'PKS', '1789028213_c825fb0f90fe1043'),
(9, 1789024097, 'MoU', '1789029968_e7669f8c7c9f825a');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_akademik`
--

CREATE TABLE `dokumen_akademik` (
  `id` int NOT NULL,
  `kategori` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` year NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_akreditas`
--

CREATE TABLE `dokumen_akreditas` (
  `id` int NOT NULL,
  `kategori` int DEFAULT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` year NOT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengupload` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nip` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `nidn` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ket` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `posisi` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_telp` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `tentang` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `profil` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ttd` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` int NOT NULL DEFAULT '1' COMMENT '1 = dosen biasa\r\n3 = operator prodi\r\n4 = operator jurusan\r\n5 = kaprodi\r\n6 = kajur/sekjur\r\n9 = superadmin',
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `prodi` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_sister` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan_fungsional` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status_kepegawaian` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'PNS/PPPK/Kontrak',
  `tahun_mulai_bertugas` smallint UNSIGNED DEFAULT NULL COMMENT 'Tahun mulai bekerja',
  `sertifikasi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `scholar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `keahlian` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nip`, `nidn`, `nama`, `password`, `ket`, `tipe_login`, `posisi`, `alamat`, `no_telp`, `tentang`, `profil`, `email`, `ttd`, `level`, `status`, `prodi`, `id_sister`, `jabatan_fungsional`, `status_kepegawaian`, `tahun_mulai_bertugas`, `sertifikasi`, `scholar`, `keahlian`, `keterangan`) VALUES
('113ef62d22', '198401102018031001', '', 'Jamal Darussalam Giu, ST., MT.', '', '', '', 'dosen', '', '', '', NULL, '', '', 1, 'tidak aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('14c4f7be30', '197411252001121002', '0025117408', 'Ervan Hasan Harun, ST., MT.', '$2y$10$aA0jkFWuoS.ZKs7Hpzn.cucctn5ctOCnvd1IsMDYqTtioYiQBiFGG', 'YW5kcm9tZWRh', 'pengguna', 'dosen', 'Perum Graha Nirwana, Kayubulan Limboto', '082292324055', 'Ervan H. Harun adalah seorang dosen di Universitas Negeri Gorontalo (UNG) yang memiliki passion dan kompetensi dalam bidang ilmu tenaga listrik. Ia memiliki keahlian dan pemahaman mendalam tentang transformator, pemodelan dan pemantauan sistem kelistrikan, serta optimasi pemakaian energi. Dengan pengetahuan dan pengalamannya yang luas, Ervan dikenal sebagai salah satu dosen yang ahli di bidangnya dan selalu memberikan yang terbaik bagi mahasiswanya. Ia juga aktif dalam riset dan publikasi nasional dalam bidang tenaga listrik.', NULL, 'ervanharun@ung.ac.id', '', 1, 'aktif', '', '48ca2022-a169-4c27-b519-4bf596d4a727', 'Lektor', NULL, NULL, '101104709276', 'https://scholar.google.co.id/citations?hl=id&user=Qvd9NIAAAAAJ', 'Sistem Tenaga Listrik', 'dtps'),
('1a127ddb8e', '196704212003121001', '0021046705', 'Zainudin Bonok, ST.,MT.', '$2y$10$3Ar4DkCpjjiWKqIsfNVT2u/VVbKrYuooKDDMTAoPtVofV7PzbnEoC', 'Mjc5', 'pengguna', 'dosen', 'Jl. Prof. Dr. HB. Jassin No 387 Kel dulalowo, Kec. Kota tengah, kota Gorontalo', '082394733692', 'Zainudin Bonok adalah seorang dosen senior yang bekerja di Universitas Negeri Gorontalo (UNG) dan memiliki pemahaman yang mendalam tentang ilmu telekomunikasi. Beliau memperoleh gelar S2 dari Universitas Hasanuddin, yang menunjukkan dedikasi dan komitmennya terhadap bidang ilmunya. Zainudin Bonok diakui dan dihormati dalam komunitas akademis karena keahlian dan dedikasinya dalam bidang ilmu telekomunikasi. Beliau memiliki kemampuan yang sangat baik dalam memotivasi dan membimbing para mahasiswa untuk mencapai potensi mereka yang terbaik. Ia memiliki pemikiran yang inovatif dan menyediakan solusi yang kreatif dan efektif untuk masalah dalam bidang ini. Zainudin Bonok diakui sebagai salah satu pemimpin dalam bidang ilmu telekomunikasi dan memiliki pengaruh yang sangat besar dalam lingkup akademis.', NULL, 'zainudinbonok@ung.ac.id', '', 5, 'aktif', '', '2702ed99-c1fd-4711-ae30-bc30eac1fb46', 'Lektor', NULL, NULL, '12100104708113', 'https://scholar.google.co.id/citations?hl=id&user=N5-8s7QAAAAJ', 'telekomunikasi', 'dtps'),
('274eccd1e7', '197704042001121001', '0004047704', 'Dr. Lanto M. Kamil Amali, ST., MT.', '$2y$10$16rbhx4BB6wPpMbv0VgAquCtcpGoruHdnP6JjGPwvFbZxc7ms0lJy', 'ODYy', 'pengguna', 'dosen', 'Perum Graha 42 Blok D No.3 Tahap 2. Jalan Padang Kelurahan Tapa Kecamatan Sipatana Kota Gorontalo (K', '085240073797', 'Lanto M. Kamil Amali, seorang dosen teknik elektro yang memiliki keahlian luar biasa dalam bidang pembangkit listrik. Ia memiliki pemahaman mendalam mengenai pengujian dan penelitian di laboratorium tegangan tinggi, dan telah menunjukkan kompetensinya dengan menjadi ketua tim pengabdian desa tertinggal. Berkat dedikasi dan kreativitasnya, Lanto berhasil menciptakan pembangkit piko hidro yang efisien dan membantu memperbaiki kualitas hidup masyarakat setempat. Ia diakui sebagai salah satu pemimpin di bidangnya dan dianggap sebagai salah satu dosen teknik elektro paling berbakat saat ini.', NULL, 'kamilamali@ung.ac.id', '', 1, 'aktif', '', '7666167a-f194-402e-9ed4-e087ea450389', 'Lektor', NULL, NULL, '11100104706485', 'https://scholar.google.co.id/citations?hl=id&user=M-qnHQoAAAAJ', 'electrical engineering', 'dtps'),
('284d6fc348', '198207252008121004', '0025078202', 'Dr. Mohamad Syafri Tuloli, ST, MT', '6b736440b7845ad0501e9fb330b5c886', 'Mjkw', 'pengguna', 'dosen', '', '081314270499', '', NULL, '', '', 1, 'aktif', '', '9a7f9c6b-1cdb-4f4b-8ec4-f3bf3be80f63', '', NULL, NULL, '', 'https://scholar.google.com/citations?user=Fxwl4iAAAAAJ&hl=en', '', ''),
('2e7b39ada2', '10041993', '', 'Ridwan Kadir', '$2y$10$xi.eZeUR4ytgoPbZkcCvo.A5IFB5/BQdKLt.tTdxDbwixjQEXpsAW', 'MDEyMzQ1OTg3Ng==', 'admin', '', '', '081234', '', '1787895256_d59c69ec2d1a00ac', 'idulkadir10@gmail.com', '', 9, 'aktif', 'Laboratorium JUTEK', '', 'Laboran', 'PPPK', 2020, '', '', 'Kecerdasan Buatan, Basis Data, Komputasi Awan, Pemrograman Web', 'dtps'),
('30b3c9b359', '197201021998022001', '0002017206', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', '348f44c6dc812296f32c7b5fae39f485', 'MzM2', 'pengguna', 'dosen', '', '085240002858', '', NULL, '', '', 1, 'aktif', '', '8428f979-3984-4703-9855-c65ba44d2c88', '', NULL, NULL, '', 'https://scholar.google.com/citations?user=4aFHqboAAAAJ&hl=id', '', ''),
('36d1464083', '197502142001122004', '0914027501', 'Ade Irawaty Tolago, ST., MT.', '$2y$10$KnrY9zMGcDc4HgZ7OOj3eeymdojawmzZn86ag1U9WXp1EKmbc4SAy', 'NzY1', 'admin', 'dosen', 'Jl.Selayat No.A3 /5', '081341538715', 'Ade Irawaty Tolago adalah seorang dosen teknik elektro yang memiliki minat dan kompetensi dalam bidang tenaga listrik. Ia memiliki pengetahuan yang luas dan dalam dalam bidang pembangkit dan distribusi listrik. Keahlian Ade Irawaty Tolago dalam mengajar dan membimbing mahasiswa sangat terkenal dan membuatnya populer di kalangan mahasiswa dan rekan-rekan sejawatnya. Ia memiliki pribadi yang ramah, ceria, dan mudah diajak berkomunikasi. Selain itu, Ade Irawaty Tolago juga memiliki visi dan misi yang kuat untuk terus meningkatkan pengetahuan dan kompetensinya dalam bidang tenaga listrik dan menyebarluaskan pengetahuan tersebut kepada mahasiswa dan masyarakat. ', NULL, 'adeirawty75@ung.ac.id', '', 1, 'aktif', '', '28ea5543-627a-4423-ba96-d4d3745b192b', 'Lektor', NULL, NULL, '16-002003-0120', 'https://scholar.google.co.id/citations?hl=id&user=5Ka3CXUAAAAJ', 'Teknik Tenaga Listrik', 'dtps'),
('3b30f0f020', '25032002', '4755', 'Rahayu Halid', 'b639ca7a3ade8ebfd4722c411939f1b7', 'MDU0', 'pengguna', 'operator', '', '08', '', NULL, '', '', 3, 'aktif', '', '', '', NULL, NULL, '', '', '', ''),
('4aa69775f4', '199706282025062011', '056', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', '$2y$10$b5guJyIAGy1UwAQCQoVQLufx5pgHKZTo/71mhBRUdFQw.wLMnR14i', 'NzY1', 'pengguna', 'dosen', '', '081245005529', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('5864493a02', '197102222001121001', '0022027105', 'Yasin Mohamad, ST., MT.', '$2y$10$GRSIABFWXQd0KOHIuiLEpO6nKW/jj2MuYjmlx6wA9lA83L6WoDvQa', 'eWFzaW5nYW50ZW5n', 'admin', 'dosen', 'Perum Altira Blok A2 No 4 Mongolato Telaga Kab. Gorontalo', '081340007879', 'Yasin Mohamad adalah seorang dosen Teknik Elektro di Universitas Negeri Gorontalo (UNG) yang memiliki keahlian dan pengalaman yang luas dalam bidang Tenaga Listrik. Beliau sangat fokus dan terampil dalam mengatasi masalah pembangkit dan distribusi tenaga listrik, dan memiliki kemampuan untuk memberikan solusi yang efektif dan inovatif. Yasin memiliki publikasi ilmiah yang sangat baik dan diakui dalam bidang tersebut, dan sering mengikuti konferensi dan seminar untuk memperkaya pengetahuan dan memperluas jaringan profesional. Ia sangat berdedikasi dan memiliki passion yang tinggi untuk berbagi dan memberikan manfaat bagi masyarakat melalui ilmunya. Kecerdasaan dan kompetensinya membuat Yasin diakui sebagai salah satu dosen terbaik di bidang Tenaga Listrik di UNG.', NULL, 'yasinmohamad@ung.ac.id', 'file/ttd-197102222001121001.png', 6, 'aktif', '', '23e15cad-ec26-4a25-b994-868a24da55ea', 'Lektor Kepala', NULL, NULL, '101104709275', 'https://scholar.google.com/citations?user=u0ISEeMAAAAJ&hl=id', 'Power System And Energy', 'dtps'),
('637dfbddee', '197510172005012001', '0017107504', 'Jumiati Ilham, ST., MT.', '$2y$10$VJ47kAYrLx9H6tqAgqGKmuWDvV6XS8IbAcswqYzd8NjPaTzxtMWGa', 'OTY3', 'pengguna', 'dosen', 'jln. Jamaludin Malik Kel. Limba U2 Kec. Kota Selatan Kota Gorontalo', '085255890292', 'Jumiati Ilham adalah seorang dosen Teknik Elektro yang berfokus dibidang tenaga listrik yang memiliki pengalaman dan pengetahuan luas dalam bidangnya. Beliau sangat terkenal di kalangan mahasiswa dan koleganya sebagai dosen yang sangat profesional dan memiliki kemampuan mengajar yang sangat baik. Kompetensinya dalam bidang tenaga listrik membuat Jumiati saat ini menjadi anggota tim penjamin mutu fakultas, membuktikan bahwa beliau merupakan salah satu dosen terbaik dalam bidangnya.', NULL, 'jumiatiilham@ung.ac.id', '', 1, 'aktif', '', '5d0f950d-c318-4965-aa0c-bb7a80bf13db', 'Lektor', NULL, NULL, '11100104716669', 'https://scholar.google.co.id/citations?hl=id&user=3Pc08MEAAAAJ', 'Energi listrik', 'dtps'),
('645eef9c30', '197604272006042002', '0027047604', 'Salmawaty Tansa, ST., M.Eng.', '$2y$10$V2b5Pe39XX8xiVEDbt45Y.o8Kmvj6jlu8bSRjCCsnZznWVtbL/ukC', 'OTY3', 'pengguna', 'dosen', '-', '085299558200', 'Salmawaty Tansa adalah seorang dosen teknik elektro yang memiliki kompetensi luas dalam bidang elektronika. Ia memiliki kemampuan yang sangat baik dalam mengajarkan dan memahami konsep-konsep yang berkaitan dengan elektronika. Ia memiliki pengalaman yang luas dalam penelitian dan pengembangan proyek elektronika, dan sangat dihormati oleh rekan-rekannya dan mahasiswa-mahasiswanya. Salmawaty memiliki pandangan yang inovatif dan memiliki kemampuan untuk berpikir di luar kotak, yang membantunya untuk terus berkembang dan menemukan solusi baru untuk masalah yang dihadapi dalam bidang elektronika.', NULL, '', '', 1, 'aktif', '', 'a2e642f5-3456-4f2d-979c-cbabf3b1095f', 'Lektor', NULL, NULL, '12100104701329', 'https://scholar.google.co.id/citations?hl=id&user=KNmJpksAAAAJ', 'Elektronika', 'dtps'),
('64809b0cfc', '197410032001121001', '0003107401', 'Amirudin Yunus Dako, ST., M.Eng.', '$2y$10$67PXswMePE/upKh9C4Zw7OAZ5LolbF4vhc4D2ruDMJSPoaUVYt5ae', 'NDc4', 'admin', 'dosen', 'Desa iloheluma kecamatan kec. Tilongkabila Kab. Bone Bolango Prov. Gorontalo, Indonesia', '08124484858', 'Amirudin Y. Dako, memiliki keahlian yang unik dan luar biasa dalam bidang sistem komputer dan pengolahan multimedia. Beliau memiliki kemampuan yang sangat jarang ditemukan dalam pemetaan dan pengolahan basis data. Dengan kombinasi kemampuannya dalam teknologi dan kepeduliannya pada masyarakat, Amirudin memimpin proyek pengembangan potensi desa dalam hal teknologi informasi. Keahlian dan dedikasi Amirudin membuat beliau menjadi salah satu dosen yang paling dicari dan dikenal di komunitas akademis dan industri.', NULL, 'amirudin.dako@ung.ac.id', '', 6, 'aktif', '', 'fc48d47e-c772-4db9-840d-349b85502e07', 'Lektor', NULL, NULL, '11100104700869', 'https://scholar.google.co.id/citations?hl=id&user=L6B36dQAAAAJ', 'computer system', 'dtps'),
('6806f5f8a7', '197710262002122003', '0026107704', 'Dr. Raghel Yunginger, S.Pd., M.Si.', 'c55c4132b0e9751c7fbfc4a734af62d1', 'NDQ2', 'pengguna', 'dosen', '', '085220626075', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dosen luar'),
('6a30d58b44', '199510302025061004', '6585', 'Hendy Prasetyo, S.S.T., M.T.', '338359414517bfe20063c5f14f483a8d', 'NzM4', 'pengguna', 'dosen', '', '089520305020', '', NULL, '', '', 5, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('6be551a649', '197410222005011002', '0022107405', 'Idham Halid Lahay, ST., M.Sc.', '$2y$10$w9TIkDOGnSyzANJzYob5DubHvzlsfFtSHQ9RfbqiYTnLp0LkI5UMW', 'NzMx', 'pengguna', 'dosen', '', '085221993397', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dosen luar'),
('6eba3d13ea', '197004052009121001', '0005047007', 'Dr. Bambang Panji Asmara, ST., MT.', '$2y$10$XLLAtmzgn2Sof.yGfDImQumlqifvhufVxkJ1HzC7g9G4NHChC3tc.', 'Mzgx', 'pengguna', 'dosen', '-jl palma lr sawit 1 perum griya laguna 2 blok c 11 kelurahan buladu kec kota barat kota gorontalo', '081354516290', 'Bambang Panji Asmara adalah seorang dosen yang mengabdikan diri di Universitas Negeri Gorontalo (UNG). Beliau memiliki minat yang sangat kuat dalam bidang ilmu sistem komputer dan kecerdasan buatan. Saat ini, beliau sedang melanjutkan pendidikan S3 di Universitas Hasanuddin (UNHAS), menunjukkan komitmennya terhadap pengembangan diri dan ilmu pengetahuan. Bambang Panji Asmara memiliki pemahaman yang sangat dalam tentang topik ini dan memberikan sumbangsih yang besar bagi pengembangan ilmu dan teknologi dalam bidang sistem komputer dan kecerdasan buatan. Beliau memiliki kemampuan yang sangat baik dalam memimpin dan membimbing para mahasiswa untuk mencapai potensial mereka. Ia diakui sebagai salah satu pemimpin dalam bidang ilmu sistem komputer dan kecerdasan buatan dan memiliki pengaruh yang sangat besar dalam lingkup akademis.', NULL, 'bambang@ung.ac.id', '', 5, 'aktif', 's1teknikelektro', '33f357e4-748b-49fa-ba66-da55387bea0b', 'Lektor', NULL, NULL, '15-002002-0147', 'https://scholar.google.co.id/citations?hl=id&user=ZCCmzSMAAAAJ', 'Telekomunikasi', 'dtps'),
('71ba4d781e', '197605232006041002', '3453', 'Prof. Dr. Ir. Eduart Wolok, ST., MT., IPU., ASEAN.', 'b400fffd1edf3e10906779be43f86a0a', 'MTc5', 'pengguna', 'dosen', '', '08122541254', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', ''),
('7bf1f8337d', '196506161994121001', '0016066505', 'Dr.Ir. Arifin Matoka, MT.', '$2y$10$sDbY1jiWuJedPE6LZLvK7uiiYbjaqbndNAugK93/YkmPNMvWbdoGy', 'Mjcz', 'pengguna', 'dosen', '-', '085215881509', 'Arifin Matoka adalah seorang dosen senior yang mengajar di Universitas Negeri Gorontalo (UNG). Beliau memiliki latar belakang pendidikan yang kuat, dengan gelar S3 dalam bidang ilmu tenaga listrik. Arifin Matoka sangat diakui dan dihormati dalam komunitas akademis karena keahlian dan dedikasinya dalam bidang ilmu tenaga listrik. Beliau memiliki pemahaman yang mendalam tentang topik ini dan memberikan sumbangsih yang besar bagi pengembangan ilmu dan teknologi. Arifin Matoka memiliki kemampuan yang sangat baik dalam memotivasi dan membimbing para mahasiswa untuk mencapai potensi mereka yang terbaik. Ia diakui sebagai salah satu pemimpin dalam bidang ini dan memiliki pengaruh yang sangat besar dalam lingkup akademis.', NULL, '', '', 1, 'aktif', '', 'e1f4604d-6786-44a2-9682-ebcde6df525a', 'Lektor', NULL, NULL, '16002001084', '', '', 'dtps'),
('7fbcc3969e', '197401162000121001', '0016017405', 'Taufiq Ismail Yusuf, ST., M.Si.', '$2y$10$SPaAk5gQNq8/WrzeF4Pe0e7KPyyTgSd4Rhw3hPuWK.fTA7xbvsBie', 'NTYz', 'pengguna', 'dosen', '-', '08114310666', 'Taufik Ismail Yusuf, memiliki kemampuan yang luar biasa dalam bidang ilmu pembangkit tenaga listrik. Beliau memiliki pemahaman mendalam tentang pembangkit dan distribusi listrik. Namun, yang membedakan Taufik adalah kemampuannya untuk menyajikan materi dengan sangat jelas dan mudah dipahami oleh para mahasiswa. Beliau dikenal memiliki gaya pengajaran yang interaktif dan inovatif, sehingga membuat para mahasiswa merasa tertarik dan semangat belajar.', NULL, '', '', 1, 'aktif', '', '090e5d07-e9a2-4f0b-8b43-ea8ff88fb084', 'Lektor', NULL, NULL, '1049110885', 'https://scholar.google.co.id/citations?hl=id&user=O9G9rdoAAAAJ', 'Sistem Tenaga Listrik', 'dtps'),
('80f75173bc', '199410202025061006', '564', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', '25d5cdb0eed312870384efcbde5aaf28', 'MTIx', 'pengguna', 'dosen', '', '0811433733', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', 'Forensika Digital', 'dtps'),
('81399d8536', '200004252025062007', '4757778679230082', 'Nurul, S.Pd., M.Pd', '683ca41b220a399228ecb3f6e59353ca', 'Nzg1', 'pengguna', 'dosen', '', '085293494470', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('8e6dd82846', '197902052005011002', '0005027905', 'Wrastawa Ridwan, ST., MT., MCE, CDSEA', '$2y$10$RQ7g.sjDo7rrj1iT7NaSwuPKc0TUovwUVH2M4zXckkDPmaFsKbi3K', 'MDAwNTAyNzkwNQ==', 'pengguna', 'dosen', 'Jl. Membramo I, Samping Asrama Putri Bolaang Mongondow Selatan, Kel. Bulotadaa Timur, Kec. Sipatana,', '081340386481', 'Wrastawa Ridwan merupakan dosen teknik elektro handal yang memiliki banyak pengalaman dalam mengajar berbagai mata kuliah. Ia dikenal sebagai sosok yang tegas dan sangat disiplin, memastikan mahasiswanya selalu memahami materi yang diajarkan. Kompetensi Wrastawa Ridwan tidak hanya terbatas pada bidang kendali cerdas, namun ia juga memiliki pengetahuan luas dan pengalaman dalam bidang elektronika dan sistem komputer. Keterampilan dan dedikasi Wrastawa Ridwan dalam mengajar menjadikannya salah satu dosen terfavorit di kampus.', NULL, 'wridwan@ung.ac.id', '', 1, 'aktif', '', '3a35517d-3f7d-40cc-b865-007e89b9a194', 'Lektor Kepala', NULL, NULL, '11100104718255', 'https://scholar.google.co.id/citations?user=_UZRY50AAAAJ&hl=id&oi=ao', 'Control, Intelligent Systems', 'dtps'),
('916933c219', '199603152024062003', '00000000', 'Ulfatun Nadifa, S.Pd, M.Kom.', '$2y$10$Vx/1U.WKanY3sAYMLEf7e.osnge/GkNOQbbUssvHUXNjfW/xMk6Hm', 'MzI5', 'pengguna', 'dosen', '', '085340100181', '', NULL, '', '', 1, 'aktif', '', 'e726c129-63ed-491b-8da2-036b529a77b4', '', NULL, NULL, '', '', '', 'dtps'),
('92459d083c', '197506242005011003', '0024067502', 'Syahrir Abdussamad, ST., MT.', '$2y$10$V2b5Pe39XX8xiVEDbt45Y.o8Kmvj6jlu8bSRjCCsnZznWVtbL/ukC', 'OTY3', 'pengguna', 'dosen', 'Jln Jeruk Perum Griya Persada Lestari Blok E3 Kelurahan Huangobotu Kecamatan Dungingi Kota Gorontalo', '081340032063', 'Syahrir Abdulsamad adalah seorang dosen muda yang sangat kompeten dalam bidang telekomunikasi. Ia memiliki kemampuan yang luar biasa dan sudah terbukti melalui penelitian dan publikasi ilmiah yang dilakukannya. Selain mengajar di UNG, Syahrir juga membagi waktunya untuk mengajar di universitas lain dan memberikan sumbangsih pada dunia pendidikan. Beliau memiliki bakat mengajar dan memotivasi mahasiswanya untuk terus berkembang dan mengejar cita-cita mereka.', NULL, 'syahrirabdussamad@ung.ac.id', '', 5, 'aktif', '', 'd737d5c0-0caa-4dea-83f7-a01353954e42', 'Lektor', NULL, NULL, '12100104701531', 'https://scholar.google.co.id/citations?hl=id&user=2oKHWV8AAAAJ', 'Electrical Engineering', 'dtps'),
('98a4a6f7b7', '196807051997021001', '0005076805', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', '$2y$10$V4tjRZqNST6okBGcXRD9COhv5clUBU/WpdTy90DqzwPX4zdGSJj1a', 'U0FSZGkxNTg3ODY=', 'pengguna', 'dosen', 'Jl. Arif Rahman Hakim Perum Misfalah Blok F3 Kota Gorontalo', '081288823621', 'Sardi Salim adalah seorang dosen yang berdedikasi di Universitas Negeri Gorontalo (UNG). Beliau memperoleh gelar S3 dari Universitas Gadjah Mada (UGM), menunjukkan komitmen dan passion-nya terhadap bidang ilmu pembangkit tenaga listrik. Sardi Salim memiliki pemahaman yang sangat dalam tentang topik ini dan membawa kemajuan dan inovasi baru dalam bidang pembangkit tenaga listrik. Beliau memiliki kemampuan yang sangat baik dalam memimpin dan membimbing para mahasiswa untuk mencapai potensial mereka. Ia diakui sebagai salah satu ahli dalam bidang ilmu pembangkit tenaga listrik dan memiliki reputasi yang sangat baik dalam komunitas akademis.', NULL, 'sardi@ung.ac.id', '', 1, 'aktif', '', '342e32aa-64c5-46a7-886f-6ba9ccee8a3f', 'Lektor Kepala', NULL, NULL, '091104707124', 'https://scholar.google.co.id/citations?hl=id&user=aDuxmg8AAAAJ', 'Energi Dan Ketenagalistrikan', 'dtps'),
('9eee0f6743', '197306221999032002', '0022067302', 'Sri Wahyuni Dali, ST., MT.', '$2y$10$j9fERSSBNjPF1KFjfJffpOD7/e.FB0iXwT7gqPhI3YCsNbiznjps2', '', 'pengguna', 'dosen', '-', '081356361525', '', NULL, 'sri.dali@ung.ac.id', '', 1, 'tidak aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('a5e8c20ec3', '199502142024061001', '1414029501', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', '$2y$10$Vx/1U.WKanY3sAYMLEf7e.osnge/GkNOQbbUssvHUXNjfW/xMk6Hm', 'MzI5', 'pengguna', 'dosen', '', '085242099275', '', NULL, '', '', 1, 'aktif', '', '47932445-d538-4f61-a2e0-b35af51bc63b', '', NULL, NULL, '', '', '', 'dtps'),
('a8fd2f4fae', '199408122025061006', '1416047001', 'Wildan, S.Pd. M.Pd', 'fee9827476060f5907eebb33434bfae9', 'ODEz', 'pengguna', 'dosen', '', '085338935129', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('d53ee838e9', '196107061990031006', '006076107', 'Ir. Wahab Musa, MT.,Ph.D', '$2y$10$qMPRPv5CMbzZg4x/ZcpHqO/U6nHYYgThoJ8HnKtVYeKlo8YIxg5XS', 'V01pcjA2MDc2aXIyMw==', 'pengguna', 'dosen', 'Jl. Yusuf Hasiru 48 D, Sipatana Kota Gorontalo', '081340035065', 'Wahab Musa adalah seorang profesional dalam bidang ilmu telekomunikasi dan kontrol. Ia bekerja sebagai dosen di Universitas Negeri Gorontalo (UNG) dan memiliki latar belakang pendidikan yang kuat, dengan gelar S1 dari Universitas Hasanuddin (UNHAS), S2 dari Institut Teknologi Bandung (ITB), dan S3 dari Northern University UMM.', NULL, 'wmusa@ung.ac.id', '', 1, 'aktif', '', '8f321503-1eca-4d45-b6bf-6ffaded98485', 'Lektor Kepala', NULL, NULL, '15 00201 0273', 'https://scholar.google.co.id/citations?user=Jhs_tS8AAAAJ&hl=id&oi=ao', 'Artificial Intelligence', 'dtps'),
('e323dbea7d', '20101993', '', 'Arum', '$2y$10$MaTnWkgP2nS8ISAEN6vFEexJL6pDdzYSY61VfL9sS65RehJXUTEI2', 'ZWxla3RybzIwMTE=', 'admin', 'operator', '', '082394911882', '', NULL, '', '', 3, 'aktif', 's1teknikelektro', '', '', NULL, NULL, '', '', '', 'dtps'),
('e8e4016ded', '198901022015041005', '0002018908', 'Ikhsan Hidayat, S.Kom., MT.', '$2y$10$V4ZTPwze2Ne4U5ReXgpste2Phk1db6R8ADcskBwwBWTVtWQ4XxXUy', 'QmlzbWlsbGFoMjAyNQ==', 'pengguna', 'dosen', 'wongkaditi permai blok C18', '082187249227', 'Iksan Hidayat adalah seorang dosen muda yang memiliki kemampuan interdisipliner dalam bidang teknik elektro dan komputer. Ia sangat memahami dan memiliki pengetahuan mendalam tentang IoT (Internet of Things), yang mengkombinasikan kemampuan elektronika, komputer dan jaringan untuk menciptakan sistem cerdas dan terhubung. Iksan Hidayat juga merupakan ahli dalam bidang pengembangan perangkat lunak dan sistem kendali otomatis, yang membantu mengatasi masalah-masalah yang kompleks dalam lingkungan industri dan rumah tangga. Ia juga aktif dalam penelitian dan pengembangan teknologi baru yang inovatif, dan sangat dihormati oleh rekan-rekan dan mahasiswanya sebagai pemimpin dalam bidang ini.', NULL, 'ikhsan_hidayat@ung.ac.id', '', 1, 'aktif', '', 'e3f2ac20-d145-4ee1-ab4b-96cd61cc85db', 'Asisten Ahli', NULL, NULL, '', 'https://scholar.google.co.id/citations?hl=id&user=iRQ2wpAAAAAJ', 'Power Systems, Inverter', 'dtps'),
('f154c17f55', '197801272005011001', '0027017803', 'Rahmat D.R. Dako, ST., M.Eng.', '$2y$10$7xUkBZQzGElaZf97ZopYgexMIBdD1WvjU0/5W18brAdKR/hGCzPgO', 'MTIz', 'superadmin', 'dosen', '-', '082349781978', 'Rahmat Deddy, dosen teknik elektro yang memiliki pengalaman dan keahlian dalam bidang sistem komputer. Ia memiliki kemampuan yang diatas rata-rata dalam pemecahan masalah dan algoritma. Rahmat juga merupakan seorang programmer dan web developer yang berpengalaman, dan beberapa aplikasi yang pernah ia buat dan bekerjasama dengan tim, termasuk \"Simpel-fsb\" (Sistem Informasi Pengelolaan Surat Elektronik) dan \"SiSFO TE\". Selain itu, Rahmat juga dikenal sebagai consultant dalam bidang IT dan memiliki pengalaman mengajar di berbagai jurusan. Ia juga aktif dalam pengabdian masyarakat dengan mengembangkan potensi desa dalam bidang IT, termasuk dengan membuat aplikasi computerized adaptive test.', NULL, '', '', 1, 'aktif', '', '5f52fa37-af84-46f2-a27a-688aaddb2c3f', 'Lektor', NULL, NULL, '11100104709355', 'https://scholar.google.co.id/citations?hl=id&user=hbIE0e0AAAAJ', 'software engineering', 'dtps'),
('f53eb48005', '197201282005011003', '0028017204', 'Ifan Wiranto, ST., MT.', '$2y$10$hqh7vHGiUgoZi4hl5N2DKu8B1aAEluJxkLwwDp8JMW5xwpMOhFdj6', 'KjEyMzQ1Iw==', 'pengguna', 'dosen', 'Jln. Nusi Yunus Latief, Kompleks Masjid Asy Syarif, Desa Bulila, Kec. Telaga, Kab. Gorontalo', '081340035050', 'Ifan Wiranto adalah seorang ahli dan pakar dalam bidang sistem komputer dan kecerdasan buatan. Beliau memiliki kemampuan yang luar biasa dalam memahami dan menerapkan teknologi terbaru dalam bidang ini, dan memiliki bakat untuk menemukan solusi yang inovatif dan efisien untuk berbagai masalah. Ifan juga memiliki publikasi ilmiah yang berkualitas dan berpengaruh dalam bidang ini, dan memiliki jaringan yang luas dan kuat di industri dan akademia.', NULL, 'ifan_te@ung.ac.id', '', 1, 'aktif', '', '73f0f263-2ba1-4f5d-979c-499c16d26a6b', 'Lektor', NULL, NULL, '12100104704388', 'https://scholar.google.co.id/citations?hl=id&user=RAu4Pn0AAAAJ', 'control Engineering And AI', 'dtps'),
('fad50faeb8', '199903232025062011', '123', 'Afifah Farhanah Akadji, S.Si., M.Si.', '$2y$10$IJNqtWS8Dg9OoRmu9FeOxuAXL4SAbEZIObqOdIz4jupNJFXdfhQ1K', 'MjMwMzk5', 'pengguna', 'dosen', '', '082271123119', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', 'dtps'),
('fad50ffmahe', '', '', 'Dosen Luar', '', '', '', 'dosen', '', '', '', NULL, '', '', 1, 'aktif', '', '', '', NULL, NULL, '', '', '', ''),
('fb4b7ab909', '197011052001121001', '0005117003', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '$2y$10$yRkfQBIS/.ziZkz0boLKPOQYUn/oAzQffUukumj3/mUzwQFGvTaVi', 'cGxhdGZvcm01QTRFRjY=', 'pengguna', 'dosen', '-', '08124462945', 'Iskandar Zulkarnain Nasibu adalah seorang dosen yang sangat berbakat dan terampil di Universitas Negeri Gorontalo (UNG). Beliau memiliki kecerdasan alami yang memungkinkannya untuk menguasai empat konsentrasi dalam bidang teknik, seperti Sistem Komputer, Tenaga Listrik, Elektronika dan juga seorang pakar dalam bidang kontrol. Beliau juga memiliki pengalaman yang sangat luas sebagai pembimbing dalam kompetisi robot nasional, menunjukkan kemampuannya dalam memberikan bimbingan dan motivasi yang baik bagi para peserta. Iskandar Zulkarnain Nasibu memiliki kemampuan yang sangat baik dalam memimpin dan membimbing para mahasiswa untuk mencapai potensial mereka. Ia diakui sebagai salah satu pakar dalam bidang teknik dan memiliki pengaruh yang sangat besar dalam lingkup akademis. Beliau membawa kontribusi yang besar dalam pengembangan ilmu pengetahuan dan teknologi.', NULL, 'zul.nasibu@ung.ac.id', '', 1, 'aktif', '', '964cf5b4-e611-4dd9-ba31-48758b8e6805', 'Asisten Ahli', NULL, NULL, '20-001019-0007', 'https://scholar.google.co.id/citations?user=iKH_4k0AAAAJ&hl=id&oi=sra', 'Programming And Electronics', 'dtps');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_file` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `format_file` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `waktu_unggah` int NOT NULL,
  `pengunggah` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tabel_asal` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `nama_file`, `format_file`, `waktu_unggah`, `pengunggah`, `tabel_asal`, `lokasi`) VALUES
('1787895256_d59c69ec2d1a00ac', '1787895256_d59c69ec2d1a00ac.jpg', 'jpg', 1787895256, NULL, 'dosen', 'file/1787895256_d59c69ec2d1a00ac.jpg'),
('1787897096_1be7d1a7aa57ef7b', '1787897096_1be7d1a7aa57ef7b.xlsx', 'xlsx', 1787897096, NULL, 'arsip_jurusan', 'file/1787897096_1be7d1a7aa57ef7b.xlsx'),
('1787897110_6dd369ccedd098e3', '1787897110_6dd369ccedd098e3.pdf', 'pdf', 1787897110, NULL, 'arsip_jurusan', 'file/1787897110_6dd369ccedd098e3.pdf'),
('1787898172_22ac3ab1e0c2298c', '1787898172_22ac3ab1e0c2298c.pdf', 'pdf', 1787898335, NULL, 'arsip_jurusan', 'file/1787898172_22ac3ab1e0c2298c.pdf'),
('1788315164_956032e2fd719669', '1788315164_956032e2fd719669.jpg', 'jpg', 1788315164, NULL, 'berita', 'file/1788315164_956032e2fd719669.jpg'),
('1788315169_a80c4f5231b6d457', '1788315169_a80c4f5231b6d457.jpg', 'jpg', 1788315169, NULL, 'berita', 'file/1788315169_a80c4f5231b6d457.jpg'),
('1788315180_9a5208b8a189ee17', '1788315180_9a5208b8a189ee17.jpg', 'jpg', 1788315180, NULL, 'berita', 'file/1788315180_9a5208b8a189ee17.jpg'),
('1789007146_5cf94c75ad2aa136', '1789007146_5cf94c75ad2aa136_98340e5a.pdf', 'pdf', 1789007146, NULL, 'kerjasama', 'file/1789007146_5cf94c75ad2aa136_98340e5a.pdf'),
('1789007330_c557c2d77456bfbb', '1789007330_c557c2d77456bfbb_cf78da77.pdf', 'pdf', 1789007330, NULL, 'kerjasama', 'file/1789007330_c557c2d77456bfbb_cf78da77.pdf'),
('1789008440_807a1ac9209e5385', '1789008440_807a1ac9209e5385_38fe145c.pdf', 'pdf', 1789008440, NULL, 'kerjasama', 'file/1789008440_807a1ac9209e5385_38fe145c.pdf'),
('1789008922_3e6f4adc1a9da42a', '1789008922_3e6f4adc1a9da42a_6de6f307.pdf', 'pdf', 1789008922, NULL, 'kerjasama', 'file/1789008922_3e6f4adc1a9da42a_6de6f307.pdf'),
('1789024097_414ba3707757bdda', '1789024097_414ba3707757bdda_5cbb29d6.pdf', 'pdf', 1789024097, '2e7b39ada2', 'kerjasama', 'file/1789024097_414ba3707757bdda_5cbb29d6.pdf'),
('1789026934_05fc7bf9df3a7006', '1789026934_05fc7bf9df3a7006_4b78c070.pdf', 'pdf', 1789026934, '2e7b39ada2', 'kerjasama', 'file/1789026934_05fc7bf9df3a7006_4b78c070.pdf'),
('1789028213_c825fb0f90fe1043', '1789028213_c825fb0f90fe1043_97af7571.pdf', 'pdf', 1789028213, '2e7b39ada2', 'kerjasama', 'file/1789028213_c825fb0f90fe1043_97af7571.pdf'),
('1789029680_cee6edf0543088ec', '1789029680_cee6edf0543088ec_fdf5ba9e.pdf', 'pdf', 1789029680, '2e7b39ada2', 'kerjasama', 'file/1789029680_cee6edf0543088ec_fdf5ba9e.pdf'),
('1789029968_e7669f8c7c9f825a', '1789029968_e7669f8c7c9f825a_d1c23c20.pdf', 'pdf', 1789029968, '2e7b39ada2', 'kerjasama', 'file/1789029968_e7669f8c7c9f825a_d1c23c20.pdf'),
('1790132782_356ccde6b9767816', '1790132782_356ccde6b9767816_896df00c.pdf', 'pdf', 1790132892, '2e7b39ada2', 'matakuliah', 'file/1790132782_356ccde6b9767816_896df00c.pdf'),
('1790132927_901239f8cde8af9b', '1790132927_901239f8cde8af9b_0494d8ac.pdf', 'pdf', 1790132927, '2e7b39ada2', 'matakuliah', 'file/1790132927_901239f8cde8af9b_0494d8ac.pdf'),
('1790133010_7f6b129d17f8733a', '1790133010_7f6b129d17f8733a_6f02acf1.pdf', 'pdf', 1790133010, '2e7b39ada2', 'matakuliah', 'file/1790133010_7f6b129d17f8733a_6f02acf1.pdf'),
('1790747466_bb521b0ccb04ec8e', '1790747466_bb521b0ccb04ec8e_db58f4ea.pdf', 'pdf', 1790747466, '2e7b39ada2', 'arsip_lain', 'file/1790747466_bb521b0ccb04ec8e_db58f4ea.pdf'),
('1790750932_07f1f191da82f5ec', '1790750932_07f1f191da82f5ec_da9e76ad.pdf', 'pdf', 1790750932, '2e7b39ada2', 'arsip_lain', 'file/1790750932_07f1f191da82f5ec_da9e76ad.pdf'),
('1790751423_7750230728396129', '1790751423_7750230728396129_33251c36.pdf', 'pdf', 1790751423, '5864493a02', 'arsip_lain', 'file/1790751423_7750230728396129_33251c36.pdf'),
('1790752665_6ff035cf45f76329', '1790752665_6ff035cf45f76329_586aa5e3.jpeg', 'jpeg', 1790752665, '2e7b39ada2', 'arsip_lain', 'file/1790752665_6ff035cf45f76329_586aa5e3.jpeg'),
('1790830344_418cd5408b124dad', '1790830344_418cd5408b124dad_62d9b4c4.pdf', 'pdf', 1790830344, '2e7b39ada2', 'skp', 'file/1790830344_418cd5408b124dad_62d9b4c4.pdf'),
('1790830688_57b67562565db7dc', '1790830688_57b67562565db7dc_0654dcc2.xlsx', 'xlsx', 1790830688, '2e7b39ada2', 'skp', 'file/1790830688_57b67562565db7dc_0654dcc2.xlsx'),
('1790831176_bb395d111b9c7c19', '1790831176_bb395d111b9c7c19_a0151fc1.xlsx', 'xlsx', 1790831176, '2e7b39ada2', 'skp', 'file/1790831176_bb395d111b9c7c19_a0151fc1.xlsx'),
('1790831193_208ca2479fb8cf37', '1790831193_208ca2479fb8cf37_6ab661fe.pdf', 'pdf', 1790831193, '2e7b39ada2', 'skp', 'file/1790831193_208ca2479fb8cf37_6ab661fe.pdf'),
('1790831746_73d9acea274e59aa', '1790831746_73d9acea274e59aa_36841925.xlsx', 'xlsx', 1790831746, '2e7b39ada2', 'skp', 'file/1790831746_73d9acea274e59aa_36841925.xlsx'),
('1790837502_1f100575fa36f795', '1790837502_1f100575fa36f795_a1960cca.xlsx', 'xlsx', 1790837502, '2e7b39ada2', 'skp', 'file/1790837502_1f100575fa36f795_a1960cca.xlsx'),
('1790838199_362d98653ee9adba', '1790838199_362d98653ee9adba_decd48d5.xlsx', 'xlsx', 1790838199, '2e7b39ada2', 'skp', 'file/1790838199_362d98653ee9adba_decd48d5.xlsx'),
('1790838204_0ccddee80d104d06', '1790838204_0ccddee80d104d06_33cff6e9.xlsx', 'xlsx', 1790838204, '2e7b39ada2', 'skp', 'file/1790838204_0ccddee80d104d06_33cff6e9.xlsx'),
('1790838608_4d57ff995dcd3666', '1790838608_4d57ff995dcd3666_733cb3c4.xlsx', 'xlsx', 1790838608, '2e7b39ada2', 'skp', 'file/1790838608_4d57ff995dcd3666_733cb3c4.xlsx'),
('1790838614_5a5da755b5760808', '1790838614_5a5da755b5760808_7b5b1845.xlsx', 'xlsx', 1790838614, '2e7b39ada2', 'skp', 'file/1790838614_5a5da755b5760808_7b5b1845.xlsx'),
('1790906317_52249baadbb89da0', '1790906317_52249baadbb89da0_2c5a566b.xlsx', 'xlsx', 1790906317, '2e7b39ada2', 'skp', 'file/1790906317_52249baadbb89da0_2c5a566b.xlsx'),
('1791276905_a7549fd824620b00', '1791276905_a7549fd824620b00_00b32284.pdf', 'pdf', 1791276905, '', 'temp_skripsi', 'file/1791276905_a7549fd824620b00_00b32284.pdf'),
('1791337133_af7db5a5bee7c3f4', '1791337133_af7db5a5bee7c3f4_2067febd.pdf', 'pdf', 1791337133, '', 'temp_skripsi', 'file/1791337133_af7db5a5bee7c3f4_2067febd.pdf'),
('1791337150_a67a02d8a5384633', '1791337150_a67a02d8a5384633_3b5bbba2.pdf', 'pdf', 1791337150, '', 'temp_skripsi', 'file/1791337150_a67a02d8a5384633_3b5bbba2.pdf'),
('1791337244_b08a3c69390675a5', '1791337244_b08a3c69390675a5_4b5e787f.pdf', 'pdf', 1791337244, '', 'temp_skripsi', 'file/1791337244_b08a3c69390675a5_4b5e787f.pdf'),
('1791337403_f96da331425bd9a5', '1791337403_f96da331425bd9a5_58cfa4f9.pdf', 'pdf', 1791337403, '', 'temp_skripsi', 'file/1791337403_f96da331425bd9a5_58cfa4f9.pdf'),
('1791337415_f67980bd1369a3b4', '1791337415_f67980bd1369a3b4_e0fd5cbf.pdf', 'pdf', 1791337415, '', 'temp_skripsi', 'file/1791337415_f67980bd1369a3b4_e0fd5cbf.pdf'),
('1791447929_e1ca4d9ef5b2f183', '1791447929_e1ca4d9ef5b2f183_02e42d04.pdf', 'pdf', 1791447929, '', 'temp_skripsi', 'file/1791447929_e1ca4d9ef5b2f183_02e42d04.pdf'),
('1791448823_fd7aa575e4c8feb8', '1791448823_fd7aa575e4c8feb8_f57f17d9.pdf', 'pdf', 1791448823, '', 'temp_skripsi', 'file/1791448823_fd7aa575e4c8feb8_f57f17d9.pdf'),
('1791449438_9e82a0dac6da35cf', '1791449438_9e82a0dac6da35cf_4f307ee9.pdf', 'pdf', 1791449438, '', 'temp_skripsi', 'file/1791449438_9e82a0dac6da35cf_4f307ee9.pdf'),
('1791449485_f2ec97151d209ad2', '1791449485_f2ec97151d209ad2_fe395576.pdf', 'pdf', 1791512119, '', 'temp_skripsi', 'file/1791449485_f2ec97151d209ad2_fe395576.pdf'),
('1791511140_b5b83e4f2f6bc25b', '1791511140_b5b83e4f2f6bc25b_c27d7244.pdf', 'pdf', 1791511140, '', 'temp_skripsi', 'file/1791511140_b5b83e4f2f6bc25b_c27d7244.pdf'),
('1791511150_130fd1a724a07352', '1791511150_130fd1a724a07352_9efba5e2.pdf', 'pdf', 1791511150, '', 'temp_skripsi', 'file/1791511150_130fd1a724a07352_9efba5e2.pdf'),
('1791511162_e981d23d1b205504', '1791511162_e981d23d1b205504_cb10803e.pdf', 'pdf', 1791511162, '', 'temp_skripsi', 'file/1791511162_e981d23d1b205504_cb10803e.pdf'),
('1791511694_745f9f06fe93f3a1', '1791511694_745f9f06fe93f3a1_1c969dc5.pdf', 'pdf', 1791511694, '', 'temp_skripsi', 'file/1791511694_745f9f06fe93f3a1_1c969dc5.pdf'),
('1791511716_9969e227e749a9b7', '1791511716_9969e227e749a9b7_b93f92e0.pdf', 'pdf', 1791511716, '', 'temp_skripsi', 'file/1791511716_9969e227e749a9b7_b93f92e0.pdf'),
('1791769835_301ec1ca66c31e4d', '1791769835_301ec1ca66c31e4d_6170c146.pdf', 'pdf', 1791769835, '', 'temp_skripsi', 'file/1791769835_301ec1ca66c31e4d_6170c146.pdf'),
('1791769840_8dbad05d9c1458c5', '1791769840_8dbad05d9c1458c5_65680c75.pdf', 'pdf', 1791770293, '', 'temp_skripsi', 'file/1791769840_8dbad05d9c1458c5_65680c75.pdf'),
('1791769846_5f11576331fd8b54', '1791769846_5f11576331fd8b54_c3235719.pdf', 'pdf', 1791769846, '', 'temp_skripsi', 'file/1791769846_5f11576331fd8b54_c3235719.pdf'),
('1791769864_3ef6743109bd154a', '1791769864_3ef6743109bd154a_345d9dcc.pdf', 'pdf', 1791769864, '', 'temp_skripsi', 'file/1791769864_3ef6743109bd154a_345d9dcc.pdf'),
('1791769878_ff1398794a4ec98d', '1791769878_ff1398794a4ec98d_85fc300a.pdf', 'pdf', 1791769878, '', 'temp_skripsi', 'file/1791769878_ff1398794a4ec98d_85fc300a.pdf'),
('1791769884_417514527154cb89', '1791769884_417514527154cb89_c4353d28.pdf', 'pdf', 1791769884, '', 'temp_skripsi', 'file/1791769884_417514527154cb89_c4353d28.pdf'),
('1791789550_86d604fb6f975322', '1791789550_86d604fb6f975322_9e8a294c.pdf', 'pdf', 1791789550, '2e7b39ada2', 'skripsi', 'file/1791789550_86d604fb6f975322_9e8a294c.pdf'),
('1791959092_21a2f49d4e449b28', '1791959092_21a2f49d4e449b28_17b21764.pdf', 'pdf', 1791959137, '', 'temp_skripsi', 'file/1791959092_21a2f49d4e449b28_17b21764.pdf'),
('1791959096_ba88b4b62fb4dac7', '1791959096_ba88b4b62fb4dac7_1dd7322b.pdf', 'pdf', 1791959096, '', 'temp_skripsi', 'file/1791959096_ba88b4b62fb4dac7_1dd7322b.pdf'),
('1791959102_c6cd4a0fd88de42f', '1791959102_c6cd4a0fd88de42f_2c9067d2.pdf', 'pdf', 1791959102, '', 'temp_skripsi', 'file/1791959102_c6cd4a0fd88de42f_2c9067d2.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `gambar_beranda`
--

CREATE TABLE `gambar_beranda` (
  `id` int NOT NULL,
  `gambar` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `judul` varchar(200) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kuliah`
--

CREATE TABLE `jadwal_kuliah` (
  `id` int NOT NULL,
  `id_matakuliah` int DEFAULT NULL,
  `hari` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `jam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `prodi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengajar1` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengajar2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ruangan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_ajaran` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_kuliah`
--

INSERT INTO `jadwal_kuliah` (`id`, `id_matakuliah`, `hari`, `jam`, `kelas`, `prodi`, `pengajar1`, `pengajar2`, `ruangan`, `tahun_ajaran`) VALUES
(1, 269, 'SELASA', '13:00 - 15:30', 'A', NULL, '7fbcc3969e', '14c4f7be30', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(2, 270, 'RABU', '13:00 - 15:30', 'C', NULL, '645eef9c30', '36d1464083', 'R.K2.11', '2026/2027 - Ganjil'),
(3, 370, 'SABTU', '13:00 - 15:30', 'A', NULL, 'f53eb48005', 'fad50faeb8', 'R.K3.16', '2026/2027 - Ganjil'),
(4, 370, 'SENIN', '08:00 - 10:30', 'B', NULL, 'f53eb48005', 'fad50faeb8', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(5, 373, 'KAMIS', '14:40 - 17:10', 'A', NULL, '14c4f7be30', '36d1464083', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(6, 369, 'SENIN', '08:00 - 10:30', 'A', NULL, '36d1464083', '7fbcc3969e', 'R.K2.11', '2026/2027 - Ganjil'),
(7, 369, 'SENIN', '15:30 - 18:00', 'B', NULL, '7fbcc3969e', '36d1464083', 'R.K2.11', '2026/2027 - Ganjil'),
(8, 369, 'JUMAT', '08:00 - 10:30', 'C', NULL, '7fbcc3969e', '36d1464083', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(9, 369, 'SELASA', '08:00 - 10:30', 'D', NULL, '7fbcc3969e', '36d1464083', 'R.K2.11', '2026/2027 - Ganjil'),
(10, 386, 'KAMIS', '15:30 - 18:00', 'A', NULL, 'fad50faeb8', 'f53eb48005', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(11, 386, 'JUMAT', '13:00 - 15:30', 'B', NULL, 'f53eb48005', 'fad50faeb8', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(12, 391, 'SELASA', '15:30 - 18:00', 'A', NULL, '4aa69775f4', '5864493a02', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(13, 391, 'RABU', '15:30 - 18:00', 'B', NULL, '5864493a02', '4aa69775f4', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(14, 374, 'JUMAT', '13:00 - 15:30', 'A', NULL, '284d6fc348', '1a127ddb8e', 'R.K2.11', '2026/2027 - Ganjil'),
(15, 374, 'SELASA', '15:30 - 18:00', 'B', NULL, '284d6fc348', '1a127ddb8e', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(16, 371, 'SELASA', '08:00 - 10:30', 'A', NULL, '64809b0cfc', '916933c219', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(17, 371, 'RABU', '08:00 - 10:30', 'B', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(18, 372, 'SABTU', '11:20 - 12:10', 'A', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(19, 372, 'SABTU', '09:40 - 10:30', 'B', NULL, '64809b0cfc', '916933c219', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(20, 372, 'SABTU', '10:30 - 11:20', 'C', NULL, '64809b0cfc', '916933c219', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(21, 387, 'RABU', '13:00 - 15:30', 'A', NULL, 'fb4b7ab909', 'a8fd2f4fae', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(22, 387, 'SELASA', '14:40 - 17:10', 'B', NULL, 'a8fd2f4fae', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(23, 388, 'SABTU', '13:00 - 14:40', 'A', NULL, 'fb4b7ab909', 'a8fd2f4fae', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(24, 388, 'SABTU', '14:40 - 15:30', 'B', NULL, 'a8fd2f4fae', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(25, 389, 'RABU', '15:30 - 17:10', 'A', NULL, '6eba3d13ea', '645eef9c30', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(26, 389, 'SENIN', '08:00 - 09:40', 'B', NULL, '645eef9c30', '6eba3d13ea', 'R.K3.16', '2026/2027 - Ganjil'),
(27, 390, 'SABTU', '14:40 - 15:30', 'A', NULL, '6eba3d13ea', '645eef9c30', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(28, 390, 'SABTU', '13:50 - 14:40', 'B', NULL, '645eef9c30', '6eba3d13ea', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(29, 392, 'KAMIS', '13:00 - 15:30', 'A', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(30, 392, 'RABU', '08:00 - 10:30', 'B', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(31, 393, 'SABTU', '13:50 - 14:40', 'A', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(32, 393, 'SABTU', '13:00 - 13:50', 'B', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(33, 404, 'JUMAT', '09:40 - 10:30', 'A', NULL, 'fb4b7ab909', '92459d083c', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(34, 404, 'JUMAT', '09:40 - 10:30', 'B', NULL, '92459d083c', 'fb4b7ab909', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(35, 405, 'SELASA', '10:30 - 12:10', 'A', NULL, '64809b0cfc', 'e8e4016ded', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(36, 406, 'JUMAT', '08:50 - 09:40', 'A', NULL, '64809b0cfc', 'e8e4016ded', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(37, 407, 'SELASA', '15:30 - 18:00', 'A', NULL, 'e8e4016ded', '6eba3d13ea', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(38, 407, 'KAMIS', '13:00 - 15:30', 'B', NULL, '6eba3d13ea', 'e8e4016ded', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(39, 408, 'RABU', '10:30 - 12:10', 'A', NULL, 'e8e4016ded', '6eba3d13ea', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(40, 408, 'RABU', '08:00 - 09:40', 'B', NULL, 'e8e4016ded', '6eba3d13ea', 'R.K2.11', '2026/2027 - Ganjil'),
(41, 409, 'SABTU', '09:40 - 12:10', 'A', NULL, 'f53eb48005', '6a30d58b44', 'R.K3.16', '2026/2027 - Ganjil'),
(42, 409, 'RABU', '13:00 - 15:30', 'B', NULL, 'f53eb48005', '6a30d58b44', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(43, 410, 'SENIN', '15:30 - 17:10', 'A', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(44, 410, 'SENIN', '10:30 - 12:10', 'B', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(45, 403, 'KAMIS', '09:40 - 12:10', 'A', NULL, 'fb4b7ab909', '92459d083c', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(46, 403, 'SELASA', '08:00 - 10:30', 'B', NULL, '92459d083c', 'fb4b7ab909', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(47, 368, 'SABTU', '08:00 - 09:40', 'A', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K2.11', '2026/2027 - Ganjil'),
(48, 270, 'RABU', '08:00 - 10:30', 'A', NULL, '36d1464083', '645eef9c30', 'R.K3.16', '2026/2027 - Ganjil'),
(49, 270, 'SABTU', '15:30 - 18:00', 'B', NULL, '36d1464083', '645eef9c30', 'R.K3.16', '2026/2027 - Ganjil'),
(50, 273, 'SENIN', '13:00 - 15:30', 'A', NULL, '916933c219', '637dfbddee', 'R.K3.16', '2026/2027 - Ganjil'),
(51, 273, 'KAMIS', '13:00 - 15:30', 'B', NULL, '637dfbddee', '916933c219', 'R.K3.16', '2026/2027 - Ganjil'),
(52, 273, 'SENIN', '15:30 - 18:00', 'C', NULL, '637dfbddee', '916933c219', 'R.K3.16', '2026/2027 - Ganjil'),
(53, 272, 'SELASA', '15:30 - 18:00', 'A', NULL, '36d1464083', '81399d8536', 'R.K3.16', '2026/2027 - Ganjil'),
(54, 272, 'JUMAT', '13:00 - 15:30', 'B', NULL, '36d1464083', '81399d8536', 'R.K3.16', '2026/2027 - Ganjil'),
(55, 285, 'KAMIS', '08:00 - 10:30', 'A', NULL, '4aa69775f4', 'f154c17f55', 'R.K2.11', '2026/2027 - Ganjil'),
(56, 285, 'SELASA', '08:00 - 10:30', 'B', NULL, 'f154c17f55', '4aa69775f4', 'R.K3.16', '2026/2027 - Ganjil'),
(57, 314, 'KAMIS', '14:40 - 16:20', 'A', NULL, '274eccd1e7', '7bf1f8337d', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(58, 292, 'SELASA', '13:00 - 15:30', 'A', NULL, 'f53eb48005', 'a5e8c20ec3', 'R.K2.11', '2026/2027 - Ganjil'),
(59, 292, 'KAMIS', '13:00 - 15:30', 'B', NULL, 'a5e8c20ec3', 'f53eb48005', 'R.K2.11', '2026/2027 - Ganjil'),
(60, 274, 'RABU', '13:00 - 15:30', 'A', NULL, '30b3c9b359', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(61, 274, 'SELASA', '13:00 - 15:30', 'B', NULL, '30b3c9b359', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(62, 274, 'SENIN', '08:00 - 10:30', 'C', NULL, '284d6fc348', '80f75173bc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(63, 275, 'SABTU', '08:00 - 08:50', 'A', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(64, 275, 'SABTU', '08:50 - 08:50', 'B', NULL, '30b3c9b359', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(65, 275, 'SABTU', '09:40 - 10:30', 'C', NULL, '80f75173bc', '284d6fc348', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(66, 288, 'SENIN', '08:00 - 09:40', 'A', NULL, '98a4a6f7b7', '5864493a02', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(67, 288, 'SENIN', '10:30 - 12:10', 'B', NULL, '5864493a02', '98a4a6f7b7', 'R.K2.11', '2026/2027 - Ganjil'),
(68, 289, 'SABTU', '13:00 - 13:50', 'A', NULL, '98a4a6f7b7', '5864493a02', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(69, 289, 'SABTU', '13:50 - 14:40', 'B', NULL, '5864493a02', '98a4a6f7b7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(70, 290, 'SELASA', '10:30 - 12:10', 'A', NULL, '645eef9c30', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(71, 291, 'JUMAT', '13:00 - 13:50', 'A', NULL, '645eef9c30', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(72, 286, 'JUMAT', '15:30 - 17:10', 'A', NULL, '645eef9c30', 'f53eb48005', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(73, 286, 'RABU', '10:30 - 12:10', 'B', NULL, 'f53eb48005', '645eef9c30', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(74, 287, 'KAMIS', '10:30 - 11:20', 'A', NULL, '645eef9c30', 'f53eb48005', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(75, 287, 'KAMIS', '11:20 - 12:10', 'B', NULL, 'f53eb48005', '645eef9c30', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(76, 293, 'RABU', '08:00 - 10:30', 'A', NULL, '92459d083c', '14c4f7be30', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(77, 293, 'SENIN', '15:30 - 18:00', 'B', NULL, '92459d083c', '14c4f7be30', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(78, 294, 'KAMIS', '15:30 - 16:20', 'A', NULL, '92459d083c', '14c4f7be30', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(79, 294, 'KAMIS', '16:20 - 17:10', 'B', NULL, '92459d083c', '14c4f7be30', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(80, 309, 'SENIN', '09:40 - 11:20', 'A', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(81, 317, 'SENIN', '09:40 - 11:20', 'A', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(82, 309, 'SELASA', '10:30 - 12:10', 'B', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(83, 317, 'SELASA', '10:30 - 12:10', 'B', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(84, 310, 'JUMAT', '08:00 - 08:50', 'A', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(85, 318, 'JUMAT', '08:00 - 08:50', 'A', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(86, 310, 'JUMAT', '08:50 - 09:40', 'B', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(87, 318, 'JUMAT', '08:50 - 09:40', 'B', NULL, '274eccd1e7', 'a8fd2f4fae', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(88, 311, 'SELASA', '13:00 - 15:30', 'A', NULL, '7bf1f8337d', '274eccd1e7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(89, 312, 'SABTU', '08:00 - 09:40', 'A', NULL, '14c4f7be30', '7fbcc3969e', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(90, 313, 'SELASA', '08:00 - 10:30', 'A', NULL, '5864493a02', '36d1464083', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(91, 307, 'RABU', '15:30 - 17:10', 'A', NULL, '637dfbddee', '274eccd1e7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(92, 319, 'RABU', '15:30 - 17:10', 'A', NULL, '637dfbddee', '274eccd1e7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(93, 308, 'JUMAT', '15:30 - 16:20', 'A', NULL, '637dfbddee', '274eccd1e7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(95, 315, 'KAMIS', '16:20 - 17:10', 'A', NULL, '274eccd1e7', '7bf1f8337d', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(96, 326, 'SENIN', '08:00 - 09:40', 'A', 's1teknikelektro', '6a30d58b44', '92459d083c', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(97, 323, 'SENIN', '13:00 - 14:40', 'A', NULL, '1a127ddb8e', 'f53eb48005', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(98, 324, 'JUMAT', '08:00 - 09:40', 'A', NULL, 'fb4b7ab909', '92459d083c', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(99, 325, 'JUMAT', '08:00 - 08:50', 'A', NULL, 'fb4b7ab909', '92459d083c', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(100, 322, 'RABU', '13:00 - 14:40', 'A', NULL, '6eba3d13ea', '1a127ddb8e', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(101, 306, 'KAMIS', '16:20 - 18:00', 'A', NULL, '274eccd1e7', 'f53eb48005', 'R.K3.16', '2026/2027 - Ganjil'),
(102, 316, 'KAMIS', '16:20 - 18:00', 'A', NULL, '274eccd1e7', 'f53eb48005', 'R.K3.16', '2026/2027 - Ganjil'),
(103, 348, 'KAMIS', '08:00 - 09:40', 'A', NULL, '637dfbddee', '274eccd1e7', 'R.K3.16', '2026/2027 - Ganjil'),
(104, 354, 'JUMAT', '15:30 - 18:00', 'A', NULL, 'fb4b7ab909', '6a30d58b44', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(105, 356, 'SELASA', '14:40 - 17:10', 'A', NULL, '916933c219', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(106, 357, 'RABU', '08:00 - 10:30', 'A', NULL, '637dfbddee', '274eccd1e7', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(107, 360, 'SENIN', '13:00 - 15:30', 'A', NULL, '6a30d58b44', 'fb4b7ab909', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(108, 321, 'KAMIS', '13:00 - 14:40', 'A', NULL, '645eef9c30', '6a30d58b44', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(109, 355, 'RABU', '13:00 - 15:30', 'A', NULL, 'a5e8c20ec3', '7bf1f8337d', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(110, 268, 'JUMAT', '09:40 - 11:20', 'A', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K3.16', '2026/2027 - Ganjil'),
(111, 271, 'JUMAT', '08:00 - 09:40', 'A', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K3.16', '2026/2027 - Ganjil'),
(112, 373, 'KAMIS', '08:00 - 10:30', 'B', NULL, '14c4f7be30', '36d1464083', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(113, 368, 'SABTU', '13:00 - 14:40', 'B', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K2.11', '2026/2027 - Ganjil'),
(114, 368, 'SELASA', '10:30 - 12:10', 'C', NULL, 'fad50ffmahe', 'fad50ffmahe', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(115, 374, 'SELASA', '08:00 - 10:30', 'C', NULL, '284d6fc348', '1a127ddb8e', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(116, 371, 'RABU', '15:30 - 18:00', 'C', NULL, '64809b0cfc', '916933c219', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(117, 373, 'JUMAT', '15:30 - 18:00', 'C', NULL, '14c4f7be30', '36d1464083', 'R.K3.16', '2026/2027 - Ganjil'),
(118, 370, 'RABU', '08:00 - 10:30', 'C', NULL, 'fad50faeb8', 'f53eb48005', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(119, 374, 'RABU', '09:40 - 12:10', 'D', NULL, '1a127ddb8e', '284d6fc348', 'R.K2.11', '2026/2027 - Ganjil'),
(120, 371, 'RABU', '13:00 - 15:30', 'D', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(121, 372, 'SABTU', '08:50 - 09:40', 'D', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(122, 370, 'RABU', '15:30 - 18:00', 'D', NULL, 'f53eb48005', 'fad50faeb8', 'R.K3.16', '2026/2027 - Ganjil'),
(123, 373, 'SABTU', '09:40 - 12:10', 'D', NULL, '36d1464083', '14c4f7be30', 'R.K2.11', '2026/2027 - Ganjil'),
(124, 374, 'SENIN', '15:30 - 18:00', 'E', NULL, '1a127ddb8e', '284d6fc348', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(125, 371, 'SENIN', '08:00 - 10:30', 'E', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(126, 372, 'SABTU', '08:00 - 08:50', 'E', NULL, '916933c219', '64809b0cfc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(127, 271, 'SELASA', '10:30 - 12:10', 'B', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K2.11', '2026/2027 - Ganjil'),
(128, 269, 'RABU', '15:30 - 18:00', 'B', NULL, '14c4f7be30', '7fbcc3969e', 'R.K2.11', '2026/2027 - Ganjil'),
(129, 269, 'JUMAT', '13:00 - 15:30', 'C', NULL, '7fbcc3969e', '14c4f7be30', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(130, 272, 'SENIN', '13:00 - 15:30', 'C', NULL, '81399d8536', '36d1464083', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(131, 275, 'SABTU', '10:30 - 11:20', 'D', NULL, '30b3c9b359', '80f75173bc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(132, 274, 'KAMIS', '08:00 - 10:30', 'D', NULL, '30b3c9b359', '80f75173bc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(133, 411, 'JUMAT', '08:00 - 08:50', 'A', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(134, 411, 'JUMAT', '08:50 - 09:40', 'B', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(135, 271, 'SENIN', '10:30 - 12:10', 'C', NULL, 'fad50ffmahe', 'fad50ffmahe', 'R.K3.16', '2026/2027 - Ganjil'),
(136, 285, 'RABU', '13:00 - 15:30', 'C', NULL, '4aa69775f4', 'f154c17f55', 'R.K3.16', '2026/2027 - Ganjil'),
(137, 292, 'SELASA', '15:30 - 18:00', 'C', NULL, 'f53eb48005', 'a5e8c20ec3', 'R.K2.11', '2026/2027 - Ganjil'),
(138, 288, 'SENIN', '13:00 - 14:40', 'C', NULL, '98a4a6f7b7', '5864493a02', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(139, 289, 'SABTU', '14:40 - 15:30', 'C', NULL, '98a4a6f7b7', '5864493a02', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(140, 290, 'SELASA', '13:00 - 14:40', 'B', NULL, 'fb4b7ab909', '645eef9c30', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(141, 290, 'KAMIS', '08:00 - 09:40', 'C', NULL, '645eef9c30', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(142, 291, 'JUMAT', '13:50 - 14:40', 'B', NULL, 'fb4b7ab909', '645eef9c30', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(143, 268, 'KAMIS', '10:30 - 12:10', 'B', NULL, 'fad50ffmahe', 'fad50ffmahe', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(144, 386, 'JUMAT', '08:00 - 10:30', 'C', NULL, 'fad50faeb8', 'f53eb48005', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(145, 391, 'SELASA', '13:00 - 15:30', 'C', NULL, '4aa69775f4', '5864493a02', 'R.K3.16', '2026/2027 - Ganjil'),
(146, 387, 'SENIN', '15:30 - 18:00', 'C', NULL, 'fb4b7ab909', 'a8fd2f4fae', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(147, 388, 'SABTU', '13:50 - 14:40', 'C', NULL, 'fb4b7ab909', 'a8fd2f4fae', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(148, 389, 'SENIN', '10:30 - 12:10', 'C', NULL, '645eef9c30', '6eba3d13ea', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(149, 390, 'SABTU', '13:00 - 13:50', 'C', NULL, '6eba3d13ea', '645eef9c30', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(150, 392, 'RABU', '15:30 - 18:00', 'C', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(151, 392, 'SENIN', '13:00 - 15:30', 'D', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(152, 393, 'SABTU', '15:30 - 16:20', 'C', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(153, 393, 'SABTU', '15:30 - 16:20', 'D', NULL, '80f75173bc', 'f154c17f55', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(154, 405, 'KAMIS', '08:00 - 09:40', 'B', NULL, 'e8e4016ded', '64809b0cfc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(155, 405, 'KAMIS', '09:40 - 11:20', 'C', NULL, '64809b0cfc', 'e8e4016ded', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(156, 406, 'JUMAT', '08:50 - 09:40', 'B', NULL, 'e8e4016ded', '64809b0cfc', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(157, 407, 'SENIN', '14:40 - 17:10', 'C', NULL, 'e8e4016ded', '6eba3d13ea', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(158, 408, 'SENIN', '13:00 - 14:40', 'C', NULL, 'e8e4016ded', '6eba3d13ea', 'R.K2.11', '2026/2027 - Ganjil'),
(159, 410, 'SELASA', '10:30 - 12:10', 'C', NULL, 'f154c17f55', '80f75173bc', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(160, 444, 'KAMIS', '08:00 - 09:40', 'A', NULL, 'a5e8c20ec3', '81399d8536', 'LabInstalasi', '2026/2027 - Ganjil'),
(161, 445, 'KAMIS', '10:30 - 12:10', 'A', NULL, '4aa69775f4', '274eccd1e7', 'LabInstalasi', '2026/2027 - Ganjil'),
(162, 446, 'KAMIS', '13:00 - 14:40', 'A', NULL, '4aa69775f4', '5864493a02', 'LabInstalasi', '2026/2027 - Ganjil'),
(163, 448, 'SELASA', '10:30 - 12:10', 'A', NULL, '81399d8536', '637dfbddee', 'LabInstalasi', '2026/2027 - Ganjil'),
(164, 447, 'SENIN', '15:30 - 17:10', 'A', NULL, 'a5e8c20ec3', '274eccd1e7', 'LabInstalasi', '2026/2027 - Ganjil'),
(165, 449, 'RABU', '10:30 - 12:10', 'A', NULL, 'fad50faeb8', '6eba3d13ea', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(166, 440, 'SABTU', '09:40 - 11:20', 'A', NULL, 'fad50ffmahe', 'fad50ffmahe', 'LabInstalasi', '2026/2027 - Ganjil'),
(167, 441, 'JUMAT', '15:30 - 17:10', 'A', NULL, 'fad50ffmahe', 'fad50ffmahe', 'LabInstalasi', '2026/2027 - Ganjil'),
(168, 351, 'SABTU', '08:00 - 11:20', 'A', NULL, 'fb4b7ab909', '274eccd1e7', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(169, 361, 'KAMIS', '09:40 - 12:10', 'A', NULL, '645eef9c30', '6eba3d13ea', 'R.K3.16', '2026/2027 - Ganjil'),
(170, 362, 'SENIN', '08:00 - 10:30', 'A', NULL, '1a127ddb8e', '92459d083c', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(171, 412, 'JUMAT', '15:30 - 17:10', 'A', NULL, '64809b0cfc', '92459d083c', 'R.K2.11', '2026/2027 - Ganjil'),
(172, 349, 'SELASA', '13:00 - 14:40', 'A', NULL, '64809b0cfc', '98a4a6f7b7', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(173, 442, 'RABU', '15:30 - 17:10', 'A', NULL, 'a5e8c20ec3', '81399d8536', 'LabInstalasi', '2026/2027 - Ganjil'),
(174, 443, 'SENIN', '10:30 - 12:10', 'A', NULL, 'a5e8c20ec3', '4aa69775f4', 'LabInstalasi', '2026/2027 - Ganjil'),
(175, 284, 'RABU', '10:30 - 12:10', 'A', NULL, '5864493a02', '36d1464083', 'R.K3.16', '2026/2027 - Ganjil'),
(176, 284, 'KAMIS', '10:30 - 12:10', 'B', NULL, '36d1464083', '5864493a02', 'R.K2.11', '2026/2027 - Ganjil'),
(177, 284, 'SELASA', '10:30 - 12:10', 'C', NULL, '5864493a02', '36d1464083', 'R.K3.16', '2026/2027 - Ganjil'),
(178, 385, 'SENIN', '10:30 - 12:10', 'A', NULL, '64809b0cfc', '92459d083c', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(179, 385, 'KAMIS', '15:30 - 17:10', 'B', NULL, '92459d083c', '64809b0cfc', 'R.K2.11', '2026/2027 - Ganjil'),
(180, 385, 'KAMIS', '13:00 - 14:40', 'C', NULL, '64809b0cfc', '92459d083c', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(181, 268, 'KAMIS', '08:00 - 09:40', 'C', NULL, 'fad50ffmahe', 'fad50ffmahe', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(182, 358, 'SENIN', '15:30 - 18:00', 'A', NULL, '7bf1f8337d', '5864493a02', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(183, 431, 'JUMAT', '13:00 - 15:30', 'A', NULL, '80f75173bc', '92459d083c', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(184, 434, 'JUMAT', '08:00 - 10:30', 'A', NULL, '916933c219', '645eef9c30', 'R.K2.11', '2026/2027 - Ganjil'),
(185, 426, 'SELASA', '08:00 - 10:30', 'A', NULL, 'fad50faeb8', '645eef9c30', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(186, 428, 'KAMIS', '15:30 - 18:00', 'A', NULL, '6eba3d13ea', '645eef9c30', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(187, 291, 'JUMAT', '14:40 - 15:30', 'C', NULL, '645eef9c30', 'fb4b7ab909', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(188, 315, 'KAMIS', '17:10 - 18:00', 'B', NULL, '274eccd1e7', '7bf1f8337d', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `kajur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sekjur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kalab` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penjamu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengelolah_jurnal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id`, `nama`, `kajur`, `sekjur`, `kalab`, `penjamu`, `pengelolah_jurnal`) VALUES
(123, 'Jurusan Teknik Elektro dan Komputer', '5864493a02', '64809b0cfc', 'fb4b7ab909', '1a127ddb8e', '92459d083c');

-- --------------------------------------------------------

--
-- Table structure for table `kategori-akreditas`
--

CREATE TABLE `kategori-akreditas` (
  `no` int NOT NULL,
  `kode_dokumen` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori-akreditas`
--

INSERT INTO `kategori-akreditas` (`no`, `kode_dokumen`, `keterangan`) VALUES
(1, 'K1', 'Visi, Misi, Tujuan dan Strategi'),
(2, 'K2', 'Tata Pamong, Tata Kelola dan Kerjasama'),
(3, 'K3', 'Mahasiswa'),
(4, 'K4', 'Sumber Daya Manusia'),
(5, 'K5', 'Keuangan, Sarana dan Prasarana'),
(6, 'K6', 'Pendidikan'),
(7, 'K7', 'Penelitian'),
(8, 'K8', 'Pengabdian kepada Masyarakat'),
(9, 'K9', 'Luaran dan Capaian Tridharma');

-- --------------------------------------------------------

--
-- Table structure for table `kerjasama`
--

CREATE TABLE `kerjasama` (
  `id` int NOT NULL,
  `instansi` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` year DEFAULT NULL,
  `deskripsi` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `tingkat` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `bidang` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kerjasama`
--

INSERT INTO `kerjasama` (`id`, `instansi`, `tahun`, `deskripsi`, `tingkat`, `bidang`) VALUES
(1789018320, 'daman', '2022', 'Kisah Haru Purna Tugas Ir. Wahab Musa, MT.,Ph.D', 'Nasional', 'Penelitian'),
(1789024097, 'test', '2026', 'testing2', 'Internasional', 'Pengabdian');

-- --------------------------------------------------------

--
-- Table structure for table `kp`
--

CREATE TABLE `kp` (
  `nim` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_surat_pengantar` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_ajaran` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_upload_balasan` date DEFAULT NULL,
  `tgl_start` date DEFAULT NULL,
  `sks` int DEFAULT NULL,
  `nama_instansi` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat_instansi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pimpinan_instansi` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_balas_instansi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_balas_surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_surat_penarikan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_penarikan` date DEFAULT NULL,
  `tanggal_seminar` date DEFAULT NULL,
  `waktu` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tempat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `batas_laporan` date DEFAULT NULL,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `surat_penunjukkan_pembimbing` int DEFAULT NULL,
  `surat_penunjukkan_penguji` int DEFAULT NULL,
  `nilai1` int DEFAULT NULL,
  `nilai2` int DEFAULT NULL,
  `nilai3` int DEFAULT NULL,
  `nilai_lapangan` int DEFAULT NULL,
  `total_nilai` int DEFAULT NULL,
  `batas_revisi` date DEFAULT NULL,
  `status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instansi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tempat_kp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pembimbing_lapangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `proses` int NOT NULL DEFAULT '1',
  `penunjukkan_pembimbing` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pembimbing` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penunjukkan_penguji` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_undangan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_seminar` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ruangan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kp`
--

INSERT INTO `kp` (`nim`, `no_surat_pengantar`, `tahun_ajaran`, `tanggal_upload_balasan`, `tgl_start`, `sks`, `nama_instansi`, `alamat_instansi`, `pimpinan_instansi`, `no_balas_instansi`, `no_balas_surat`, `no_surat_penarikan`, `tanggal_penarikan`, `tanggal_seminar`, `waktu`, `tempat`, `batas_laporan`, `judul`, `surat_penunjukkan_pembimbing`, `surat_penunjukkan_penguji`, `nilai1`, `nilai2`, `nilai3`, `nilai_lapangan`, `total_nilai`, `batas_revisi`, `status`, `instansi`, `tempat_kp`, `pembimbing_lapangan`, `proses`, `penunjukkan_pembimbing`, `pembimbing`, `penunjukkan_penguji`, `no_undangan`, `jam_seminar`, `ruangan`, `tanggal_mulai`, `tanggal_selesai`) VALUES
('05201425002', NULL, NULL, NULL, '2026-10-19', 110, 'Kindhali TV', 'Jl. Aa Wahab, Desa Tenggela Kec. Telaga Biru, Kab. Gorontalo', 'Stasiun Manager', NULL, NULL, NULL, '2026-12-19', NULL, NULL, 'Kindhali TV', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aktif', 'Kindhali TV', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-19', '2026-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `kurikulum`
--

CREATE TABLE `kurikulum` (
  `kode` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tahun` year NOT NULL,
  `prodi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','tidak aktif') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurikulum`
--

INSERT INTO `kurikulum` (`kode`, `nama`, `tahun`, `prodi`, `status`) VALUES
('1753425335', 'Kurikulum KKNI', '2017', 's1teknikelektro', 'tidak aktif'),
('1753425420', 'Kurikulum KKNI - MBKM', '2020', 's1teknikelektro', 'tidak aktif'),
('1753425426', 'Kurikulum 2023', '2023', 's1teknikkomputer', 'tidak aktif'),
('1784211306', 'Kurikulum OBE', '2026', 's1pendidikanvokasionalrekayasaelektro', 'aktif'),
('1785722041', 'Kurikulum OBE', '2025', 's1teknikelektro', 'aktif'),
('1785725609', 'Kurikulum OBE', '2025', 's1teknikkomputer', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ket` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `prodi` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `angkatan` year DEFAULT NULL,
  `dosen_pa` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tempat_lahir` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ktp` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('MOHAMAD RIVAN ABDULLAH', '05201425002', '$2y$10$CLMziVQLW/0qMVDYBsfabuqiM/yAh.HT80o4Z.d6alDGD9p.UI1hG', 'rivanabdullah55@gmail.com', '81242966521', 'Aktif', 'MDUyMDE0MjUwMDIxMjNl', 's1teknikelektro', '2025', '36d1464083', '', NULL, '', ''),
('ZULKARNAIN H. PELEALU', '05201425003', '$2y$10$lrcg686HmiyZ0Ce3LuVhB.3RurosrNJaxFPmBipL.EtexmYLKXNr6', 'zulkarnainpelealu40@gmail.com', '085136278219', 'Aktif', 'MDUyMDE0MjUwMDMxMjNl', 's1teknikelektro', '2025', '98a4a6f7b7', '', NULL, '', ''),
('FAHMI A. UNO', '05201425004', '0dbfbe597e467ed3b3c731ad7c332a39', 'fahmiauno@gmail.com', '82292031842', 'Aktif', 'MDUyMDE0MjUwMDQxMjNl', 's1teknikelektro', '2025', '98a4a6f7b7', '', NULL, '', ''),
('GERAL ANDREAN', '05201425005', '7b3cef094c8527343d89b7627a19818e', 'geralandrean@gmail.com', '082233525993', 'Aktif', 'MDUyMDE0MjUwMDUxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('SURYA PRAKOSO', '05201425006', '565f9b6faa9dfc353bf263b57ce9dd8a', 'suryaprakoso157@gmail.com', '087769352428', 'Aktif', 'MDUyMDE0MjUwMDYwMTA1MjAwNw==', 's1teknikelektro', '2025', '7bf1f8337d', 'Jakarta ', '2007-05-01', 'Jl. Rusli Datau \nKel/Desa : Tanggikiki \nKecamatan : Sipatana ', 'file/05201425006-passfoto.jpeg'),
('SITI KOMARI HERAP', '05201425007', '799af43f78f1e2c69ef1d256f321809f', 'sitikomariherap@gmail.com', '0859189606571', 'Aktif', 'MDUyMDE0MjUwMDcxMjNl', 's1teknikelektro', '2025', '7fbcc3969e', '', NULL, '', ''),
('MUH. THARIK ALFIKRI HADJU', '05201425008', 'd17eaa5a53863a896c77eada7664e1a6', 'starstrike096@gmail.com', '085183085117', 'Aktif', 'MDUyMDE0MjUwMDgxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('FAREL AFRIYANTO UMAR', '05201425009', 'f0c8b812d0378a1e438793aada10929d', 'farelafriyantoumar@gmail.com', '085241554091', 'Aktif', 'MDUyMDE0MjUwMDkxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MUH. NESTA S H DG. M', '05201425010', '636dc4cdefa9814dec693746e2b0f787', 'muhnesta540@gmail.com', '085756811079', 'Aktif', 'MDUyMDE0MjUwMTAxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', 'file/05201425010-passfoto.png'),
('VERA ENJELINA HUSIN', '05201425011', '8eacc63028f02f5634f7d2ab60273113', 'veraenjelina85@gmail.com', '081243310433', 'Aktif', 'MDUyMDE0MjUwMTExMjNl', 's1teknikelektro', '2025', '5864493a02', 'Desa Toima, Kec. Bunta, Kab. Banggai', '2008-02-04', 'Toima, Kec. Bunta, Kabupaten banggai,  Sulawesi Tengah ', 'file/05201425011-passfoto.jpg'),
('VICTORIO GRANT PUA', '05201425012', '48258d7f769462f5912c2ca7c61db64a', 'grantpua7@gmail.com', '082194926216', 'Aktif', 'MDUyMDE0MjUwMTIxMjNl', 's1teknikelektro', '2025', '5864493a02', 'Mananggu', '2007-06-30', 'Dusun pinaesaan, Desa kaaruyan, Kecamatan mananggu', 'file/05201425012-passfoto.jpg'),
('RIVO PAULUS PANGAU', '05201425013', '13686951e09b8925b88e1bb0e6ce02c3', 'rivopangau058@gmail.com', '082156728585', 'Aktif', 'MDUyMDE0MjUwMTMxMjNl', 's1teknikelektro', '2025', '274eccd1e7', '', NULL, '', ''),
('MUH. DESTA S. H.DG. M.', '05201425014', 'b8983ac202af702cc1f15bdc2cb0efe2', 'mdesta880@gmail.com', '085396205751', 'Aktif', 'MDUyMDE0MjUwMTQxMjNl', 's1teknikelektro', '2025', '274eccd1e7', '', NULL, '', ''),
('RISWAN NURHAMIDIN', '05201425015', 'ca03771c09e9822bab0f85492f9bb291', 'riswannurhamidin88@gmail.com', '081244268738', 'Aktif', 'MDUyMDE0MjUwMTUxMjNl', 's1teknikelektro', '2025', NULL, 'Ikhwan', '2007-03-13', 'Sulawesi utara kecamatan dumoga barat', 'file/05201425015-passfoto.jpeg'),
('TAHFIDZUL ILMI', '05201425016', 'e4af86d08252702a98d431b414b0fe7c', 'tahfidzulafis123@gmail.com', '082271661498', 'Aktif', 'MDUyMDE0MjUwMTYxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('NEMINA ERINA NUSANTARA', '05201425017', 'c18e57a2a0eb95e33cff5708616b4ce7', 'neminaerinan@gmail.com', '081774127248', 'Aktif', 'MDUyMDE0MjUwMTcxMjNl', 's1teknikelektro', '2025', '5864493a02', '', NULL, '', ''),
('AL ZIKRI MUHAIMIN', '05201425018', '0309e4e15b6835852861622dd019c808', 'alzikrimuhaimin4@gmail.com', '085756266475', 'Aktif', 'MDUyMDE0MjUwMTgxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('EXSAL BAGOE', '05201425019', '39144c7c06a1556459de6c65bccd27e8', 'bagoeexsal@gmail.com', '085696311002', 'Aktif', 'MDUyMDE0MjUwMTkxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MUHAMAD HIKMAH HANIF ALQODRY', '05201425020', 'b220b8f4918f896f17eee2cd5af1d71f', 'hanifalqodry30@gmail.com', '085394959559', 'Aktif', 'MDUyMDE0MjUwMjAxMjNl', 's1teknikelektro', '2025', 'fb4b7ab909', '', NULL, '', ''),
('IRFAN A.AMBOLOLO', '05201425021', '8994db35fefecec4c05992f91ea21961', 'iifan2373@gmail.com', '082283561901', 'Aktif', 'MDUyMDE0MjUwMjExMjNl', 's1teknikelektro', '2025', 'fb4b7ab909', 'Tumotok 12 November 2004', '2004-11-12', 'Sulawesi Tengah, Ampana kabupaten Tojo una-una, desa tumotok ', 'file/05201425021-passfoto.jpg'),
('ANDI RASYA', '05201425022', '553d1c01c584e0f08efea0ab086b1041', 'andirasya081@gmail.com', '082188789435', 'Aktif', 'MDUyMDE0MjUwMjIxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOHAMAD RASYA RAHIM', '05201425023', '7d2f571324336db288f6a2229d9db4c1', 'rsyarhim@gmail.com', '082393227900', 'Aktif', 'MDUyMDE0MjUwMjMxMjNl', 's1teknikelektro', '2025', '1a127ddb8e', '', NULL, '', ''),
('ZULFIKAR DANIAL', '05201425024', 'fec5e14f7e73b199600084da7a769950', 'zulfikardanial26@gmail.com', '087778131721', 'Aktif', 'MDUyMDE0MjUwMjQxMjNl', 's1teknikelektro', '2025', '92459d083c', '', NULL, '', ''),
('PUTRI R. SULUI', '05201425025', '0b2b17f12668d51951c1aee23e0174d6', 'putrirsului8@gmail.com', '081241015364', 'Aktif', 'MDUyMDE0MjUwMjUxMjNl', 's1teknikelektro', '2025', 'fb4b7ab909', '', NULL, '', ''),
('KRISNA ALFRETS NAYOAN', '05201425026', '84470a515e103ba314ba6b322ef8c829', 'krisnanayoan2766@gmail.com', '081243465744', 'Aktif', 'MDUyMDE0MjUwMjYxMjNl', 's1teknikelektro', '2025', '637dfbddee', '', NULL, '', ''),
('MUH. MUKHLIS A. JUSUF', '05201425027', '44f45bb804b5c6bdb6e674a57c8d83ca', 'mukhlisjusuf30@gmail.com', '082349121011', 'Aktif', 'MDUyMDE0MjUwMjcxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOHAMAD FATUR RAMADHAN S. ALIMUN', '05201425028', '5097193c0eb6e46e51140ab11d9ab112', 'faturalimun69@gmail.com', '0895803405859', 'Aktif', 'MDUyMDE0MjUwMjgxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('EGIDIUS DIGO', '05201425029', '0c9beb10e8baa9ecd459149089ae9755', 'orangganteng010907@gmail.com', '', 'Aktif', 'MDUyMDE0MjUwMjkxMjNl', 's1teknikelektro', '2025', '92459d083c', 'Ende', '2007-09-01', 'DUSUN KILOMETER DUA\nRT/RW       : 000/000\nKEL/DESA :OMBULODATA\nKECAMATAN : KWANDANG', 'file/05201425029-passfoto.jpg'),
('ARYANSYAH AKUNA', '05201425030', '7792ea7627043207d39a9ca00f5a9e11', 'aryansyahakuna@gmail.com', '081342834260', 'Aktif', 'YXJ5YWFrdW5hMTE=', 's1teknikelektro', '2025', 'f154c17f55', 'Gorontalo ', '2006-01-11', 'Desa Talango, Kecamatan Kabila Kabupaten Bone Bolango, Provinsi Gorontalo ', 'file/05201425030-passfoto.jpg'),
('MUHAMAD RIZQY KADULLAH', '05201425031', '483fa97515b9e452d351a150be15a69a', 'kadullahrizqy3@gmail.com', '085171571337', 'Aktif', 'MDUyMDE0MjUwMzExMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('ISTARTO PULUMODUYO', '05201425032', 'f93cd549efd3455f64b7e608df621b7f', 'tiopulumoduyo8@gmail.com', '0895321118677', 'Aktif', 'MDUyMDE0MjUwMzIxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('KHAIRUDDIN D. HASAN', '05201425033', 'cdba5e865c7843d373c88de89acde8ea', 'khairudinhsan@gmail.com', '085345718752', 'Aktif', 'MDUyMDE0MjUwMzMxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD AL FAREL DE BINTA', '05201425034', 'd45e2b2ee752eef1e4f0c2c6ae7ee4f2', 'muhalfareldebinta@gmail.com', '083835797538', 'Aktif', 'MDUyMDE0MjUwMzQxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MARCIKAL THORIQ MONOARFA', '05201425035', 'be22d1d1bd33380d2d706f2910d0f8b8', 'cikalmonoarfa59@gmail.com', '089510649142', 'Aktif', 'MDUyMDE0MjUwMzUxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('AFDAL LAHAY', '05201425036', '4ca232c802a25ee43cf80a7c42dd3e4e', 'afdallahay11@gmail.com', '082293132832', 'Aktif', 'MDUyMDE0MjUwMzYxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('NIA RAMADANI RAMLI', '05201425037', '054f94d61161c31053aef1591ea30515', 'niar1658@gmail.com', '082296509299', 'Aktif', 'MDUyMDE0MjUwMzcxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('I NYOMAN SANDHI LESMANA', '05201425038', 'd63cadfd497db5b406d15ef60f52e3ea', 'sandhilesmana25@gmail.com', '085342051506', 'Aktif', 'MDUyMDE0MjUwMzgxMjNl', 's1teknikelektro', '2025', 'e8e4016ded', 'Kotamobagu ', '2007-03-23', '', ''),
('MOHAMAD RIZKY KASIM', '05201425039', 'b70ed3c2f6dec487634475c7ed7f79bf', 'ikikasim0707@gmail.com', '089637250129', 'Aktif', 'MDUyMDE0MjUwMzkxMjNl', 's1teknikelektro', '2025', 'e8e4016ded', 'Gorontalo', '2007-07-31', 'Jl. imam samadi, Desa Langge', ''),
('ADI MAIL SIMANJORANG', '05201425040', 'fbd78c7a6e42428b855d7d9b9d3dc4f7', 'gressimanjorang@gmail.com', '082167673041', 'Aktif', 'MDUyMDE0MjUwNDAxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOH ZUL SUWANDI', '05201425041', '24e35d7dc62a837323a6280b0422f8b3', 'zullsuwandi@gmail.com', '089529436602', 'Aktif', 'MDUyMDE0MjUwNDExMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('RIZKI PAKAYA', '05201425042', 'a0ee00a6c57701506108aa16ed196b82', 'rizkipakaya88@gmail.com', '085242737972', 'Aktif', 'MDUyMDE0MjUwNDIxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOHAMMAD FAISAL LAIYA', '05201425043', 'eb1e3d035246fd2b039ea65bdbf0fbdf', 'laiyafaisal99@gmail.com', '08979363464', 'Aktif', 'MDUyMDE0MjUwNDMxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('ARDIYANTO A. MAHMUD', '05201425044', 'ff30debf7bc406b5410e444760ab57d8', 'ardiyantomahmud47@gmail.com', '082347827085', 'Aktif', 'MDUyMDE0MjUwNDQxMjNl', 's1teknikelektro', '2025', '92459d083c', '', NULL, '', ''),
('ABDUL RAHMAN KAABA', '05201425045', 'be4bf831a7056d743e787c200d299c3b', 'rahmankaaba12@gmail.com', '', 'Aktif', 'MDUyMDE0MjUwNDUxMjNl', 's1teknikelektro', '2025', '98a4a6f7b7', '', NULL, '', ''),
('FAGIL MOHAMAD ABDUL', '05201425046', '6a3e82e4cb2c53b3120bc495be5ad6e6', 'fagil.moh49@sma.belajar.id', '082187238966', 'Aktif', 'MDUyMDE0MjUwNDYxMjNl', 's1teknikelektro', '2025', '7fbcc3969e', '', NULL, '', ''),
('HIDAYAT MADINA', '05201425047', '89e939e11f0ec0f45e68c972577df69b', 'hidayatmadina90@gmail.com', '085696312263', 'Aktif', 'MDUyMDE0MjUwNDcxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MUH. ADITYA SALITA', '05201425048', '2158b607c7b8509a05a15f30eb87056c', 'aditmuhaditya280@gmail.com', '082268459550', 'Aktif', 'MDUyMDE0MjUwNDgxMjNl', 's1teknikelektro', '2025', '274eccd1e7', 'Lantibung', '2006-12-15', 'Lantibung ', ''),
('HAIRULLIANTO U. HJARATI', '05201425049', '48d143ddc4e6422f4cb19ebd629a224a', 'kronossbahabil@gmail.com', '085399413487', 'Aktif', 'MDUyMDE0MjUwNDkxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOHAMMAD DWIKI REZA POLONTALO', '05201425050', '0a69d3d8e8e96b1db375ee2c23b9a4fc', 'dwikireza2404@gmail.com', '082196738922', 'Aktif', 'MDUyMDE0MjUwNTAxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('ISHAK AHMAD', '05201425051', '2050bab7274a9a30332b01fa10b72a56', 'ishakahmad211105@gmail.com', '083834184308', 'Aktif', 'MDUyMDE0MjUwNTExMjNl', 's1teknikelektro', '2025', 'fb4b7ab909', '', NULL, '', ''),
('FANDIL H. ABDULLATIF', '05201425052', '5abecc1b73c28fdc24a9efe77c5012c9', 'fandilabdullatif875@gmail.com', '083894716047', 'Aktif', 'MDUyMDE0MjUwNTIxMjNl', 's1teknikelektro', '2025', '1a127ddb8e', '', NULL, '', ''),
('MOH. NAZAR', '05201425053', '4d12dcce97f23aeeb078015a90d1cf4a', 'nmuh98369@gmail.com', '082196717012', 'Aktif', 'MDUyMDE0MjUwNTMxMjNl', 's1teknikelektro', '2025', '5864493a02', '', NULL, '', ''),
('WIRNO A BASO', '05201425054', '20bf839b3fed30fad8c3de11127710d7', 'wirnoabaso86@gmail.com', '085348070964', 'Aktif', 'MDUyMDE0MjUwNTQxMjNl', 's1teknikelektro', '2025', '637dfbddee', '', NULL, '', ''),
('MOHAMMAD SEPTIAN SOLEMAN', '05201425055', '4f90d4189a9f47a0f1f3f0c1db8d4127', 'aatwdaw0987@gmail.com', '089520821025', 'Aktif', 'YWF0c29sZW1hbjA2', 's1teknikelektro', '2025', '92459d083c', 'GORONTALO', '2006-09-28', '', 'file/05201425055-passfoto.jpg'),
('ABDURAHMAN IBNU QAYYIM', '05201425056', '239ebefb71c309a3a940d10d47743c15', 'qoyyim006@gmail.com', '081957246325', 'Aktif', 'MDUyMDE0MjUwNTYxMjNl', 's1teknikelektro', '2025', 'f154c17f55', '', NULL, '', 'file/05201425056-passfoto.png'),
('MOH ABD MAJID', '05201425057', '022a06e45b89a39e8f356925dec1d305', 'mohabdmajid@gmail.com', '085827634632', 'Aktif', 'MDUyMDE0MjUwNTcxMjNl', 's1teknikelektro', '2025', NULL, 'TOLISU', '2007-07-23', 'KEC:BATUI\nDESA:KAYOWA\nKAB:LUWUK BANGGAI', 'file/05201425057-passfoto.jpg'),
('AGUNG DARMAWAN', '05201425058', '83981d560651a6580d38980d8fcf809e', 'darmawanagung770@gmail.com', '081243410892', 'Aktif', 'MDUyMDE0MjUwNTgxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOH. ZAKY', '05201425059', '3abf8368f196cb0703db0186b39d0f49', 'moh.zakyyy06@gmail.com', '085184919900', 'Aktif', 'MDUyMDE0MjUwNTkxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOH. RHEZA R. HI. SAIN', '05201425060', '499e5188aede2b6b83ea9487c6ec505f', 'muhrhezarhisain@gmail.com', '081342592772', 'Aktif', 'MDUyMDE0MjUwNjAxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('ALIYANSYAH SAMATAN', '05201425061', '6512263f5b7264bfc4fd01195eb2da75', 'aaan75992@gmail.com', '082288401391', 'Aktif', 'MDUyMDE0MjUwNjExMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('DWI SAPUTRI WARTABONE', '05201425062', 'c6ca314ae39ecfd0a7044ab1189e00bb', 'nelsa.wartabone@icloud.com', '082196483393', 'Aktif', 'MDUyMDE0MjUwNjIxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('MOHAMAD ZULKARNAIN YAHYA', '05201425063', '00b22e428c632969c0b0f187549f8bdc', 'mohzulkarnainyahya@gmail.com', '089690623200', 'Aktif', 'NDI1MjQ2Njcy', 's1teknikelektro', '2025', '5864493a02', 'Gorontalo', '2007-07-13', 'JL. 23 JANUARI', 'file/05201425063-passfoto.jpg'),
('MU\'AMAR T. MARDAN', '05201425064', '7c3ff2f4c5664daeea350ce6a2190591', 'amartangahu5@gmail.com', '082259137425', 'Aktif', 'MDUyMDE0MjUwNjQxMjNl', 's1teknikelektro', '2025', NULL, '', NULL, '', ''),
('KEZRON VIEGAS DE ARAUJO MONIZ FATIMA BELO', '05201425065', '83cc95c1b8c41c7ffe731be5ea36a8a5', 'kezronmonizbelo@gmail.com', '0882020074965', 'Aktif', 'bGt0ZW5v', 's1teknikelektro', '2025', '5864493a02', 'Dili, Timor-Leste', '2007-10-19', 'Jalan Jend Sudirman No. 6, Dulalowo Tim, Dulalowo Timur, Kota Tengah', ''),
('Nawangsih Hanafi', '05201426001', 'f6b2f63cdb2d39ed0a185132a443f8b6', 'hanafinawangsih@gmail.com', '082217464277', 'Aktif', 'MzQ4NzE=', 's1teknikelektro', '2026', '98a4a6f7b7', 'KABUPATEN GORONTALO', '2008-05-31', 'JL. NONI HUNGGU, Kel/Desa Sidomukti Kec. Mootilango KAB. GORONTALO - GORONTALO', ''),
('Dewa Putra Firmansyah Lanontji', '05201426002', '206e1d342980fc27cbdd255294d91dfc', 'banyamulu61@gmail.com', '085656689084', 'Aktif', 'NDUwMTE=', 's1teknikelektro', '2026', NULL, 'PENDATU', '2008-05-03', 'LINGKUNGAN TELADAN, Kel/Desa Pentadu Kec. Paguat KAB. POHUWATO - GORONTALO', ''),
('Nur Anisa Rahmadani', '05201426003', '0163cbbdf9682f2f440e1f89d95c3982', 'anisarahmadani2907@gmail.com', '081234391459', 'Aktif', 'NjY3Njc=', 's1teknikelektro', '2026', '7fbcc3969e', 'TOLITOLI', '2008-07-29', 'JALAN SANTA NO. 56, Kel/Desa Paddumpu Kec. Dampal Selatan KAB. TOLI TOLI - SULAWESI TENGAH', ''),
('Zainal Akbar Katili', '05201426004', 'c720c35e574769bf445957b5643e6fa5', 'zainalakbarkatili@gmail.com', '085823773610', 'Aktif', 'MzU1MDg=', 's1teknikelektro', '2026', NULL, 'BATUI', '2007-10-12', 'SISIPAN, Kel/Desa Sisipan Kec. Batui KAB. BANGGAI - SULAWESI TENGAH', ''),
('Moh. Levi Ginoga', '05201426005', '4b4b61e642ee7ebf43b15ecd7988e29a', 'ginogalevi10@gmail.com', '08953049831', 'Aktif', 'NTE2MTU=', 's1teknikelektro', '2026', '5864493a02', 'OTAM', '2007-08-19', 'OTAM LORONG GARDU INDUK, Kel/Desa Otam Kec. Passi Barat KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Aldiansyah D. Nasir', '05201426006', 'ffaed7f3d34809c9148917232fe456d7', 'adi907802@gmail.com', '082296026924', 'Aktif', 'MDMwNzU=', 's1teknikelektro', '2026', '274eccd1e7', 'LIPULALONGO', '2007-10-27', 'JLN. KAMPUNG ALASAN, Kel/Desa Lipulalongo Kec. Labobo KAB. BANGGAI LAUT - SULAWESI TENGAH', ''),
('Rahmat Taufiq', '05201426007', '9a53a12d48898834a3973953212e58b3', 'rahmattaufiq379@gmail.com', '085213687137', 'Aktif', 'ODE3OTg=', 's1teknikelektro', '2026', NULL, 'PALAPI', '2007-05-05', 'JL TRANS SULAWESI, Kel/Desa Yipilo Kec. Wanggarasi KAB. POHUWATO - GORONTALO', ''),
('Usman Djafar', '05201426008', '5c202eeb3cc15940317d572cf1f092f2', 'djafaru09@gmail.com', '085823426985', 'Aktif', 'NDM4MDk=', 's1teknikelektro', '2026', NULL, 'KAB. GORONTALO', '2007-10-14', 'JLN. TAHIR MANYO, Kel/Desa Ulapato B Kec. Telaga Biru KAB. GORONTALO - GORONTALO', ''),
('Moh. Taufiqurrahman Putra Maniku', '05201426009', '24e960c76053f12c084522e332b241c5', 'manikurahman7@gmail.com', '081527121206', 'Aktif', 'OTM4MDU=', 's1teknikelektro', '2026', 'fb4b7ab909', 'KOTAMOBAGU', '2009-02-07', 'TOLONDADU - KEC. BOLAANG UKI - KAB. BOLAANG MONGONDOW SELATAN, Kel/Desa Tolondadu Kec. Bolaang Uki KAB. BOLAANG MONGONDOW SELATAN - SULAWESI UTARA', ''),
('Mohamad Arif Buni', '05201426010', 'b02b7fa643ce3ea3f531ae3a5f1426f7', 'okibunibuni@gmail.com', '085342894521', 'Aktif', 'NDY3Mzg=', 's1teknikelektro', '2026', '1a127ddb8e', 'ILANGATA', '2008-01-15', 'JLN. TRANS SULAWESI, Kel/Desa Ilangata Kec. Anggrek KAB. GORONTALO UTARA - GORONTALO', ''),
('Rhian Firmansyah', '05201426011', '5c48d5dc19f1a09899cc3d0220f28f36', 'firmansyahrian383@gmail.com', '085295443278', 'Aktif', 'ODYyMzg=', 's1teknikelektro', '2026', '637dfbddee', 'MARGAKENCANA', '2008-07-19', 'JAYA KENCANA, Kel/Desa Jaya Kencana Kec. Toili KAB. BANGGAI - SULAWESI TENGAH', ''),
('Alfito Priansa Ismail', '05201426012', '9a27648df0e418b44411d1901175d269', 'fitoopriansa@gmail.com', '085696607017', 'Aktif', 'ODc5NzU=', 's1teknikelektro', '2026', '92459d083c', 'KAB. GORONTALO', '2008-02-07', 'TRANS SULAWESI, Kel/Desa Parungi Kec. Boliyohuto KAB. GORONTALO - GORONTALO', ''),
('Moh. Christyo M. Koemadji', '05201426013', 'c78568b33d35243e25116d42170fabba', 'mohcristiokoemadji@gmail.com', '0895803348880', 'Aktif', 'MTUzMTI=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-07-18', 'JL. PROF. DR. HB. JASSIN, Kel/Desa Limba B Kec. Kota Selatan KOTA GORONTALO - GORONTALO', ''),
('Alexander a. Putra Harianto', '05201426014', '57fa7542344cfd142ca1577850499301', 'alexaharianto21@gmail.com', '089502776687', 'Aktif', 'NjgyODg=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-02-21', 'JL. PRAMUKA, Kel/Desa Bulotadaa Barat Kec. Sipatana KOTA GORONTALO - GORONTALO', ''),
('Aqila Mirza Annaafi', '05201426015', 'b53b4faaac53dedfbe4d3fd53e07fdd5', 'aqilamirzaannaafi@gmail.com', '082393016912', 'Aktif', 'MDE1Nzc=', 's1teknikelektro', '2026', NULL, 'BLITAR', '2009-03-18', 'DESA HUWONGO, Kel/Desa Huwongo Kec. Paguyaman KAB. BOALEMO - GORONTALO', ''),
('Abdul Fachri Igirisa', '05201426016', '34ab622038a3d89747069fd7b2f51161', 'fahriigirisa203@gmail.com', '082241891847', 'Aktif', 'MzgyNDc=', 's1teknikelektro', '2026', NULL, 'ANGGREK', '2009-09-18', 'JL. TRANS SULAWESI, Kel/Desa Tolitehuyu Kec. Monano KAB. GORONTALO UTARA - GORONTALO', ''),
('Dwi Rizky Annisa S', '05201426017', '1b16274f2983436a4b45ad7dc84755e2', 'rainny.ssa280608@gmail.com', '085824768981', 'Aktif', 'NjM5NzA=', 's1teknikelektro', '2026', NULL, 'DONGIN', '2008-06-28', 'SETAPAK, Kel/Desa Dongin Kec. Toili Barat KAB. BANGGAI - SULAWESI TENGAH', ''),
('Andika Juliyan Putra', '05201426018', '2697bd3553dfd044016f2cd021291150', 'andikaputra10072008@gmail.com', '085394012056', 'Aktif', 'NzgxMzg=', 's1teknikelektro', '2026', NULL, 'PAGUAT', '2008-07-10', 'TRANS SULAWESI, Kel/Desa Bunuyo Kec. Paguat KAB. POHUWATO - GORONTALO', ''),
('Moh. Ramadani Rahim', '05201426019', '975a62d115735d70dffc90a8ecf9b5b2', 'danirahim6679@gmail.com', '083870049693', 'Aktif', 'NDQ0ODc=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-10-04', 'DUSUN III DESA MONGOLATO, Kel/Desa Mongolato Kec. Telaga KAB. GORONTALO - GORONTALO', ''),
('Kharisma Aisyah', '05201426020', 'f3814365af2d530f34eb864841322fa7', 'kharismaa1048@gmail.com', '085823148673', 'Aktif', 'MTAzOTA=', 's1teknikelektro', '2026', NULL, 'PALU', '2008-04-10', 'PERTANIAN, TRANS SULAWESI, Kel/Desa Tampiala Kec. Dampal Selatan KAB. TOLI TOLI - SULAWESI TENGAH', ''),
('Jamaludin Ramli Saleh', '05201426021', '4857581264a7c6a31e5c349642bde872', 'jamaludinsaleh46@gmail.com', '085179765730', 'Aktif', 'MTM0ODc=', 's1teknikelektro', '2026', NULL, 'PAGUYAMAN', '2007-11-20', 'PELABUHAN, Kel/Desa Dudewulo Kec. Popayato Barat KAB. POHUWATO - GORONTALO', ''),
('Muhamad Fahri Abdullah', '05201426022', '44009aa3da8ff5e45368beef405cb7ec', 'Abdullahfahri058@gmail.com', '08986396611', 'Aktif', 'MjQyNjI=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2007-02-19', 'JL. RUSLI DATAU, Kel/Desa Dulomo Selatan Kec. Kota Utara KOTA GORONTALO - GORONTALO', ''),
('Iqbal Y Ishak', '05201426023', 'b3bd31d4b82676243355a1a9e0fdf7dd', 'iqbal.ishak2007@gmail.com', '085136275735', 'Aktif', 'MDI5Mjc=', 's1teknikelektro', '2026', NULL, 'KAB. GORONTALO', '2007-12-18', 'JL. SALIM MANUMBA DESA POTANGA, Kel/Desa Potanga Kec. Boliyohuto KAB. GORONTALO - GORONTALO', ''),
('Ahmad Averros Habibi', '05201426024', '9ad908939ee42131b3175829f9e65c13', 'ahmadaverros030607@gmail.com', '082189689193', 'Aktif', 'NjYzMTc=', 's1teknikelektro', '2026', NULL, 'MAROANGIN', '2007-06-03', 'DUSUN SAKULATI, Kel/Desa Tolotio Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Moh. Rafael Kassa', '05201426025', 'fba133e7f17353d2e3c5ae473000182c', 'dellatoadi@gmail.com', '0895406015529', 'Aktif', 'MzkwOTU=', 's1teknikelektro', '2026', NULL, 'KAB. GORONTALO', '2007-12-26', 'JL. RAYA BATUDAA, Kel/Desa Tabongo Timur Kec. Tabongo KAB. GORONTALO - GORONTALO', ''),
('Arsa a. Lasang', '05201426026', 'eb6e846f172ea98a3266cd1a86a18221', 'arsa.a45@sma.belajar.id', '085119562768', 'Aktif', 'ODc4NTA=', 's1teknikelektro', '2026', NULL, 'BUOL', '2008-03-07', 'DESA TOLAU  KAB. BUOL  PROV. SULAWESI TENGAH 94568, Kel/Desa Tolau Kec. Paleleh KAB. BUOL - SULAWESI TENGAH', ''),
('Repael Ramadan Kaku', '05201426027', '83e6659b8c9b7081a4d04130c8fbc2f0', 'farelramadhan1611@gmail.com', '083189520577', 'Aktif', 'NzExNTc=', 's1teknikelektro', '2026', '7fbcc3969e', 'KAB. GORONTALO', '2008-09-10', 'PONGONGAILA  KAB. GORONTALO  PROV. GORONTALO 96251, Kel/Desa Pongongaila Kec. Pulubala KAB. GORONTALO - GORONTALO', ''),
('Moh. Alfarham Mahmud', '05201426028', '1e5362d5c5cec7c92c7d21a8f50bd6df', 'farhammahmud2@gmail.com', '089653274038', 'Aktif', 'NjQyNzk=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2009-12-19', 'JL. SELAYAR  KOTA GORONTALO  PROV. GORONTALO 96129, Kel/Desa Liluwo Kec. Kota Tengah KOTA GORONTALO - GORONTALO', ''),
('Ocber Vandrey Mamiasa', '05201426029', 'fc86df3a17cc83fe3443a9675f254256', 'gr.gamersmuda01@gmail.com', '085820162070', 'Aktif', 'OTc5MzE=', 's1teknikelektro', '2026', '5864493a02', 'MILANGODAA', '2007-10-16', 'JALAN DESA MILANGODAA  KAB. POHUWATO  PROV. GORONTALO 96367, Kel/Desa Milangodaa Kec. Popayato Timur KAB. POHUWATO - GORONTALO', ''),
('Andra Mokoginta', '05201426030', '2ac54b36b652d1614e69a761a3302dc1', 'mokogintaandra@gmail.com', '085757460538', 'Aktif', 'MTQ1NzE=', 's1teknikelektro', '2026', '274eccd1e7', 'KOTAMOBAGU', '2008-05-23', 'JL. D.C.MANOPPO  KOTA KOTAMOBAGU  PROV. SULAWESI UTARA 95717, Kel/Desa Pobundayan Kec. Kotamobagu Selatan KOTA KOTAMOBAGU - SULAWESI UTARA', 'file/05201426030-passfoto.jpg'),
('Jilan Abdul Gani', '05201426031', '397b3cbee5352a526b868502dc0ce8e3', 'jilanabdulgani02@gmail.com', '081219127403', 'Aktif', 'Njg5NjI=', 's1teknikelektro', '2026', NULL, 'BANDUNG', '2006-03-02', 'KP. CIGENTUR RT 002 RW 009 DESA MEKARJAYA KEC. BANJARAN, Kel/Desa Mekarjaya Kec. Banjaran KAB. BANDUNG - JAWA BARAT', ''),
('Nazhar Mooduto', '05201426032', 'e372c8a5ebe4832f0688ea3f998c77b9', 'nazharmooduto6@gmail.com', '082336127286', 'Aktif', 'NjQ0NTU=', 's1teknikelektro', '2026', NULL, 'MOLOTABU', '2008-01-06', 'TRANS SULAWESI BONE PESISIR  KAB. BONE BOLANGO  PROV. GORONTALO 96591, Kel/Desa Pelita Jaya Kec. Bone Raya KAB. BONE BOLANGO - GORONTALO', ''),
('Mirjan Mohamad Khan', '05201426033', '6c7be0f779f5b103142ccac2af9c5eea', 'mirjanmkhan@gmail.com', '082291905207', 'Aktif', 'MzA4NDE=', 's1teknikelektro', '2026', 'fb4b7ab909', 'GORONTALO', '2008-07-05', 'JL. SAMRATULANGI  KOTA GORONTALO  PROV. GORONTALO 96115, Kel/Desa Limba U I Kec. Kota Selatan KOTA GORONTALO - GORONTALO', ''),
('Isran Raginda', '05201426034', '1ee4f52f6d0a52e573ebbc60471f45f8', 'isranraginda3007@gmail.com', '089609745030', 'Aktif', 'MzI0NDY=', 's1teknikelektro', '2026', '1a127ddb8e', 'GORONTALO', '2008-07-30', 'JL.UPO, Kel/Desa Talulobutu Selatan Kec. Tapa KAB. BONE BOLANGO - GORONTALO', ''),
('Rahmat Falerio Rizal', '05201426035', 'accc957c895f55f9a181d79e6bc2db82', 'rhmtxfbrn@gmail.com', '082252306691', 'Aktif', 'NTI2NjE=', 's1teknikelektro', '2026', '637dfbddee', 'BUOL', '2008-02-15', 'JL. LINTAS PEGUNUNGAN DAMAR. RT 014/RW 008, Kel/Desa Leok I Kec. Biau KAB. BUOL - SULAWESI TENGAH', ''),
('Moh. Haryandi D. Adam', '05201426036', 'f689fd20b301bd6d176a5c8a0322cb3e', 'yandiadam644@gmail.com', '085282675760', 'Aktif', 'MzY3MzE=', 's1teknikelektro', '2026', '92459d083c', 'UEKAMBUNO', '2008-03-11', 'DESA BONGKA MAKMUR, ULUBONGKA, TOJO UNA UNA, SULAWESI TENGAH, Kel/Desa Bongka Makmur Kec. Ulubongka KAB. TOJO UNA UNA - SULAWESI TENGAH', ''),
('Reyhan Firmansa Pontoan', '05201426037', 'b4936d6154f98f790aa63bbdb3391253', 'reypontoan06@gmail.com', '0895385054341', 'Aktif', 'NDU3MzE=', 's1teknikelektro', '2026', NULL, 'KOTAMOBAGU', '2007-02-20', 'POBUNDAYAN  KOTA KOTAMOBAGU  PROV. SULAWESI UTARA 95717, Kel/Desa Pobundayan Kec. Kotamobagu Selatan KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Zeliha Naura Manolang', '05201426038', '461d545ff34668e4eca2e4d3d09881b7', 'zelihamanolang8@gmail.com', '082296259529', 'Aktif', 'MTI5MDE=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-11-08', 'JALAN RAYA DESA TUNGOI LL  KAB. BOLAANG MONGONDOW  PROV. SULAWESI UTARA 95771, Kel/Desa Tungoi II Kec. Lolayan KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Mohamad Revandi Dunggio', '05201426039', '339d9bc5937bcfaa1e388267fe0aa80c', 'dunggiorevandi@gmail.com', '082152545822', 'Aktif', 'ODAyODk=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-06-10', 'KEC. DUHIADAA KAB. POHUWATO  PROV. GORONTALO 96266, Kel/Desa Duhiadaa Kec. Duhiadaa KAB. POHUWATO - GORONTALO', ''),
('Taufik Hermawan', '05201426040', '726b666c37d7bd1142d8fe1d42c37769', 'taufikladiaa@gmail.com', '085278156554', 'Aktif', 'Mjc2Mzk=', 's1teknikelektro', '2026', NULL, 'SALAKAN', '2008-10-17', 'JL.KAL KARATA NO.16 KLURAHAN SALAKAN KEC. TINANGKUNG  KAB. BANGGAI KEPULAUAN  PROV. SULAWESI TENGAH 94785, Kel/Desa Salakan Kec. Tinangkung KAB. BANGGAI KEPULAUAN - SULAWESI TENGAH', ''),
('Renaldi Kadim', '05201426041', '823a7dabe1424f94d862bd05bafc51a0', 'renaldikadim12@gmail.com', '085394630062', 'Aktif', 'MjE1ODU=', 's1teknikelektro', '2026', NULL, 'KWANDANG', '2008-08-07', 'DUSUN UTARA, JL.LAGUNI POLAMOLO, Kel/Desa Titidu Kec. Kwandang KAB. GORONTALO UTARA - GORONTALO', ''),
('Moh Reza', '05201426042', 'db8419248154a574dc3bc139494ac00b', 'rezzxima@gmail.com', '085198634058', 'Aktif', 'MDk1Nzc=', 's1teknikelektro', '2026', NULL, 'BUOL', '2008-01-27', 'GUNUNG SANSIBAR  KAB. BUOL  PROV. SULAWESI TENGAH 94563, Kel/Desa Kali Kec. Biau KAB. BUOL - SULAWESI TENGAH', ''),
('Rafiyari Limehe', '05201426043', '7e505b91323f0b59c80ccf5fb0b0c1ec', 'arilrafiyariaril@gmail.com', '085240904595', 'Aktif', 'MTU3NjA=', 's1teknikelektro', '2026', NULL, 'TOLI - TOLI', '2008-05-15', 'JL. PELABUHAN  KAB. POHUWATO  PROV. GORONTALO 96265, Kel/Desa Bumbulan Kec. Paguat KAB. POHUWATO - GORONTALO', ''),
('Arga Tungoli', '05201426044', '3bcbb8c118e0e78488399c9b48bfa6b1', 'argatungoli9@gmail.com', '082131579632', 'Aktif', 'RWxlY3RybzEkMjM=', 's1teknikelektro', '2026', '6a30d58b44', 'BUOL', '2008-08-03', 'DUSUN TINALIKAN  KAB. BUOL  PROV. SULAWESI TENGAH 94564, Kel/Desa Potangoan Kec. Bukal KAB. BUOL - SULAWESI TENGAH', ''),
('Hilal Nafi Juhair Putra Sulaiman', '05201426045', 'd1b293a19f5bfef72bd54dcef995b0d5', 'lallgg606@gmail.com', '088298520078', 'Aktif', 'Mzk3NTA=', 's1teknikelektro', '2026', NULL, 'SAMARINDA', '2008-05-02', 'JALAN JENDRAL SUDIRMAN GANG 2 LINGKUNGAN 3 NO. 7  KAB. TOLITOLI  PROV. SULAWESI TENGAH 94514, Kel/Desa Baru Kec. Baolan KAB. TOLI TOLI - SULAWESI TENGAH', ''),
('Akmal Milad Dunggio', '05201426046', 'a9ac19e2282ae2bdf94b0772651529b9', 'akmaldunggio21@gmail.com', '089618230140', 'Aktif', 'NjA1NzQ=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-03-21', 'JL. BRIGJEN PIOLA ISA  KOTA GORONTALO  PROV. GORONTALO 96123, Kel/Desa Dulomo Selatan Kec. Kota Utara KOTA GORONTALO - GORONTALO', ''),
('Mohamad Abdul Giffar Pikoli', '05201426047', '58bf5b06fb4b9e3337c7243207f584ee', 'mohamadgiffar@gmail.com', '085194098397', 'Aktif', 'OTY3Njg=', 's1teknikelektro', '2026', NULL, 'GORONTALO', '2008-02-13', 'JL.DULAMAYO  KAB. BONE BOLANGO  PROV. GORONTALO 96544, Kel/Desa Bunuo Kec. Bulango Utara KAB. BONE BOLANGO - GORONTALO', ''),
('Sahril S. Madilau', '05201426048', 'aabf2fd06e27723f20a944dabe14f4f9', 'sahrilmadilau24@gmail.com', '082152353838', 'Aktif', 'MzkyNDI=', 's1teknikelektro', '2026', NULL, 'BUOL', '2008-12-15', 'JL. BARU  KAB. BUOL  PROV. SULAWESI TENGAH 94563, Kel/Desa Busak I Kec. Karamat KAB. BUOL - SULAWESI TENGAH', ''),
('Abdul Fayyadh Panigoro', '05201426049', '1d86b1075aa4c3d8bec1f89dbcf738b0', 'fayyadhpanigoro@gmail.com', '0895428204000', 'Aktif', 'ODg1MDQ=', 's1teknikelektro', '2026', NULL, 'TIMBUOLO', '2008-04-08', 'JL. MUCLIS RAHIM  KAB. BONE BOLANGO  PROV. GORONTALO 96583, Kel/Desa Timbuolo Kec. Botupingge KAB. BONE BOLANGO - GORONTALO', ''),
('Muhammad Ramadhan Sapili', '05201426050', 'abf0d534e3d0095c043ad772ea15ae94', 'fardansapili8@gmail.com', '085256171716', 'Aktif', 'OTA5NTI=', 's1teknikelektro', '2026', '98a4a6f7b7', 'KABUPATEN GORONTALO', '2008-09-01', 'JL. KANCIL KL. BULIIDE KOTA GORONTALO  PROV. GORONTALO 96131, Kel/Desa Buliide Kec. Kota Barat KOTA GORONTALO - GORONTALO', ''),
('Moh. Iqbar S. Nani', '05201426051', '7bb1f91743b2452790f214a86977c17c', 'iqbar20358w@gmail.com', '085340436040', 'Aktif', 'MTQ2Mjc=', 's1teknikelektro', '2026', NULL, 'ILANGATA', '2007-01-10', 'JLN. TRANS SULAWESI, DUSUN TIMUATA, Kel/Desa Ilangata Kec. Anggrek KAB. GORONTALO UTARA - GORONTALO', ''),
('Rahmat Yahya', '05201426052', '9b5fbd0a801053d291a73895a168e6cc', 'rahmatyahya494@gmail.com', '082197379712', 'Aktif', 'ODM0ODE=', 's1teknikelektro', '2026', '7fbcc3969e', 'KAB. GORONTALO', '2007-10-23', 'JLN. TRANS SULAWESI  KAB. GORONTALO  PROV. GORONTALO 96219, Kel/Desa Sukamakmur Kec. Tolangohula KAB. GORONTALO - GORONTALO', ''),
('Muh. Afgani Ramadhan', '05201426053', '522ba3d9e8679817ed60af84562ed25a', 'alganirmdhan@gmail.com', '082292241715', 'Aktif', 'NTYyNjE=', 's1teknikelektro', '2026', NULL, 'LUWUK', '2007-10-05', 'JL. BARU KAB. BANGGAI LAUT  PROV. SULAWESI TENGAH 94814, Kel/Desa Dodung Kec. Banggai KAB. BANGGAI LAUT - SULAWESI TENGAH', ''),
('Abdul Lathiif Al-Hafiidh', '05201426054', 'a41ba3b21d825cce808eb10a229fb9e9', 'abduldhika19@gmail.com', '081280188038', 'Aktif', 'NjE4ODQ=', 's1teknikelektro', '2026', '5864493a02', 'JAKARTA', '2008-05-19', 'JALAN MEDANG KENCANA  KAB. TANGERANG  PROV. BANTEN 15334, Kel/Desa Medang Kec. Pagedangan KAB. TANGERANG - BANTEN', ''),
('Ahmad Taufik', '05201426055', '57f8e509515699871905fa56a2f3182c', 'tahmadtaufik71@gmail.com', '082192790606', 'Aktif', 'NjMzMzg=', 's1teknikelektro', '2026', '274eccd1e7', 'CALEKO', '2008-02-28', 'JL. UPT  KAB. POHUWATO  PROV. GORONTALO 96266, Kel/Desa Mootilango Kec. Duhiadaa KAB. POHUWATO - GORONTALO', ''),
('Mohamad. Gilang Ramadan a. Hulinggi', '05201426056', '1193081d65dcf868b8fcb959ee401ada', 'gilanghulinggi2@gmail.com', '085849596946', 'Aktif', 'QEdpbGFuZzIwMDc=', 's1teknikelektro', '2026', '64809b0cfc', 'MONGKOINIT', '2007-09-10', 'MONGKOINIT BARAT DUSUN III, Kel/Desa Mongkoinit Barat Kec. Lolak KAB. BOLAANG MONGONDOW - SULAWESI UTARA', 'file/05201426056-passfoto.jpg'),
('I Kadek Dwy Ardicha', '05201426057', 'fab561ba55965529efb217594a6694be', 'ikadekdwiardika531@gmail.com', '085141064971', 'Aktif', 'Mzc4Njk=', 's1teknikelektro', '2026', NULL, 'BUOL', '2007-12-31', 'JL. BALI, LORONG PURA, Kel/Desa Kokobuka Kec. Tiloan KAB. BUOL - SULAWESI TENGAH', ''),
('Zafirah Qoulan Mitali', '05201426058', 'b1b42aaef1ce0428f1213b4bc81d1746', 'zafirahqoulan@gmail.com', '082156984118', 'Aktif', 'OTg3ODg=', 's1teknikelektro', '2026', 'fb4b7ab909', 'GORONTALO', '2006-08-10', 'JL. BANDARA, Kel/Desa Tolotio Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Mohamad Nabild Putra Pangkey', '05201426059', '0c53fdbb8b7d78259c9a11fd96cdab67', 'pangkeynabil@gmail.com', '083175730999', 'Aktif', 'Nzk1NDE=', 's1teknikelektro', '2026', '1a127ddb8e', 'KAB. GORONTALO', '2005-12-05', 'JLN. ABDULLAH AMU  KAB. GORONTALO  PROV. GORONTALO 96251, Kel/Desa Botumoputi Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Dwi Andika Patamani', '05201426060', 'cb99a9d5097480e9d73a4ba680795dba', 'dwiandika270470@gmail.com', '081242928808', 'Aktif', 'MTc0MTg=', 's1teknikelektro', '2026', '637dfbddee', 'LUWUK', '2008-04-27', 'DESA PONDING PONDING, Kel/Desa Ponding-ponding Kec. Tinangkung Utara KAB. BANGGAI KEPULAUAN - SULAWESI TENGAH', ''),
('Aditya a. Hulinggi', '05201426061', '4f2a503ab5bfd6bd44751d0e58553631', 'adityahulinggi@gmail.com', '082176128169', 'Aktif', 'MDQyMTc=', 's1teknikelektro', '2026', '92459d083c', 'AMPANA', '2008-05-03', 'JL.TADULAKO, Kel/Desa Wakai Kec. Una Una KAB. TOJO UNA UNA - SULAWESI TENGAH', ''),
('Rahmat H. Lasanang', '05201426063', 'e771ea9dbeeaa36be4970569c0866d30', 'rahmatlasanang38@gmail.com', '085215846102', 'Aktif', 'MjM1NjA=', 's1teknikelektro', '2026', NULL, 'LUWUK', '2008-05-02', 'KEL.SISIPAN KEC.BATUI KAB.BANGGAI, Kel/Desa Sisipan Kec. Batui KAB. BANGGAI - SULAWESI TENGAH', ''),
('SUCI AULYA IDI', '05202425001', '6ba040128d7afeb2d99fa9c3eda1adc4', 'suciaulyaidi@gmail.com', '082159527592', 'Aktif', 'MDUyMDI0MjUwMDExMjNl', 's1teknikkomputer', '2025', '7fbcc3969e', 'molosipat ', '2007-04-13', 'Desa molosipat, kec. Popayato Barat, Kab. Pohuwato, Prov. Gorontalo ', 'file/05202425001-passfoto.png'),
('MOHAMAD ALFAJRIN S. LATIF', '05202425002', '3f3fcd5463d9692f1b352c689ea3a566', 'alfajrinlatif123@gmail.com', '082348706709', 'Aktif', 'MDUyMDI0MjUwMDIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NI KADEK AYU FEBRIWARDANI', '05202425003', 'f722b7a4c24205fb32454f1b721e9f1a', 'nikadekayufebri@gmail.com', '082393210545', 'Aktif', 'MDUyMDI0MjUwMDMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('SRI MUTMAIN ALI', '05202425004', 'b866bbb455fcfb983ca503a04de7c974', 'srimutmainali@gmail.com', '081341816485', 'Aktif', 'MDUyMDI0MjUwMDQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('HAIKAL S . MUSTAPA', '05202425005', '7e0bd3e0163f3953555164354630e9d2', 'haikalmustapa14@gmail.com', '085134249905', 'Aktif', 'MDUyMDI0MjUwMDUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ALNAFSI H. NTOU', '05202425006', 'c00881222c8a92d61b1db9e6b3ff12e8', 'alnafsi.ntou39@smk.belajar.id', '081523866752', 'Aktif', 'MDUyMDI0MjUwMDYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('AHMAD SALEH', '05202425007', 'edb5a03b2b10884650283359b1f959fb', 'ahmadsaleh40@smk.belajar.id', '082335609409', 'Aktif', 'MDUyMDI0MjUwMDcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ABDULLAH TUAMANI', '05202425008', '1672bca9216a75bbd4a6bc5128d534a8', 'abdullahnox8@gmail.com', '082194859305', 'Aktif', 'MDUyMDI0MjUwMDgxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ADITYA PUTRA ADUKA', '05202425009', 'ca575c1b3eedbbc3810c4d844af4ca46', 'aditaduka.02@gmail.com', '082195051122', 'Aktif', 'MDUyMDI0MjUwMDkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD FAKHRI SAMUDRA', '05202425010', '6ef95b21bf4a4b1a18680b288d8ce75c', 'samudrafahri6@gmail.com', '085757085052', 'Aktif', 'MDUyMDI0MjUwMTAxMjNl', 's1teknikkomputer', '2025', '92459d083c', 'Gorontalo ', '2007-06-02', '', ''),
('FADLY HAYATI', '05202425011', 'd387ebe777bbd54baddcad5a65c22bc0', 'fadlyhayati88@gmail.com', '', 'Aktif', 'MDUyMDI0MjUwMTExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ARYA DEFAN KATILI', '05202425012', 'a72163c8887a37f8de66fb7d27520e19', 'defanlubu@gmail.com', '081344205452', 'Aktif', 'QVJZQVNJQVRFSzIzMDUwNw==', 's1teknikkomputer', '2025', '1a127ddb8e', 'Gorontalo', '2007-05-23', 'Desa Popodu, Kecamatan Bulango Timur, Kabupaten BoneBolango, Provinsi Gorontalo', 'file/05202425012-passfoto.jpg'),
('JASSICA KHAIRUNISA ARBIE', '05202425013', '5e8d128d179a8fcc5fed550cc772c6ae', 'jassicakhairunisa@gmail.com', '082291131823', 'Aktif', 'MDUyMDI0MjUwMTMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NAJWA FARADILA MALIKI', '05202425014', 'c5b7cc834f705514324762b00c2fbf14', 'nzfrdilla@gmail.com', '081216505035', 'Aktif', 'MDUyMDI0MjUwMTQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOHAMMAD OMAR AUFADIL DETU', '05202425015', '432a1773a75f7c5f898d28408dcb2564', 'risoladil@gmail.com', '085796992998', 'Aktif', 'MDUyMDI0MjUwMTUxMjNl', 's1teknikkomputer', '2025', '64809b0cfc', '', NULL, '', ''),
('JUSRIADI H. DEILIPU', '05202425016', 'aba0d110c78895bfb8d17363490c85ca', 'ajusri826@gmail.com', '082349745058', 'Aktif', 'MDUyMDI0MjUwMTYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ELANG RABBANI ALGHIFARI YUSUF', '05202425017', 'fbcb9691000da1513ef6f9e9a1628126', 'elangrabbanialghifariyusuf@gmail.com', '081252545793', 'Aktif', 'MDUyMDI0MjUwMTcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ABI RAZYA BUHUNGO', '05202425018', 'c69683a6d9d29139653985ca9c50b6a4', 'buhungorasya@gmail.com', '082196785526', 'Aktif', 'MDUyMDI0MjUwMTgxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', 'file/05202425018-passfoto.jpg'),
('MUTMAINAH SAFITRI LIMONU', '05202425019', '748d9869f1128f8cf4db0a1bbe0d50ee', 'muthysafitry@gmail.com', '085396111285', 'Aktif', 'MDUyMDI0MjUwMTkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('GLEN DOSONO', '05202425020', '27e8ec3e331000a635c587e006f2940d', 'glendosono9@gmail.com', '082241808106', 'Aktif', 'MDUyMDI0MjUwMjAxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOH. FATHAN AHMAD', '05202425021', '7913330c40c40563880f29a061bd494c', 'mohahmad431@smk.belajar.id', '082295013312', 'Aktif', 'MDUyMDI0MjUwMjExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NAFISYA AYATUL HUSNA', '05202425022', 'fc143204101f941438fbbcbbbedb9a89', 'nafisyaayatulhusna22@gmail.com', '082199997702', 'Aktif', 'MDUyMDI0MjUwMjIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('FAISAL IRAWAN BONE', '05202425023', 'adb15c9dfb79acba917896f1582ce728', 'faisalirawanbone@gmail.com', '085136274600', 'Aktif', 'MDUyMDI0MjUwMjMxMjNl', 's1teknikkomputer', '2025', 'e8e4016ded', '', NULL, '', ''),
('ABD.WAHAB AKUBA', '05202425024', 'bbff766e76f1a1148610a687373e8217', 'abdwahabakuba@gmail.com', '', 'Aktif', 'MDUyMDI0MjUwMjQxMjNl', 's1teknikkomputer', '2025', NULL, 'isimu selatan kecamtan tibawa kabupaten gorontalo', '2007-02-21', 'isimu selatan kecamtan tibawa kabupaten gorontalo', ''),
('FRI NUR ANISA OLII', '05202425025', '48ce25dbf08334fddaef34910b3817c5', 'frinuranisaolii@gmail.com', '082196772600', 'Aktif', 'MDUyMDI0MjUwMjUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD ZULFIKAR RAUF', '05202425026', '0510787e3866c4dd94c7a7afee75a12e', 'dekoo2911@gmail.com', '083863137746', 'Aktif', 'MDUyMDI0MjUwMjYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOH. FEBRIANSYAH', '05202425027', 'bd16aca8116988a765db36430ed32f2a', 'moh9292@sma.belajar.id', '082135909112', 'Aktif', 'MDUyMDI0MjUwMjcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('BAYU AGUNG WIBOWO', '05202425028', 'cbf114013b2bb076f887f117d6dffbed', 'pejuangbintang39@gmail.com', '082191134275', 'Aktif', 'MDUyMDI0MjUwMjgxMjNl', 's1teknikkomputer', '2025', '645eef9c30', 'Maros', '2006-09-03', 'Prov. Sulawesi Tengah, Kab. Buol, Kec. Biau, Kel. Kali', ''),
('DJAFAR M DAMITI', '05202425029', '1f9bb3f201199c684d300b1b29e6a727', 'djafarmdamiti@gmail.com', '0851-2398-0964', 'Aktif', 'MDUyMDI0MjUwMjkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ZALSABIL TATU', '05202425030', 'f93c8941f4820ba0743702250f20ef61', 'zalsabiltatu@gmail.com', '082189569505', 'Aktif', 'MDUyMDI0MjUwMzAxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOH. ARJUN ABDI SATYA', '05202425031', '4bd1ca0b01a3c5744d317e8b23ab6e65', 'moharjunabdisatya@gmail.com', '082226989108', 'Aktif', 'MDUyMDI0MjUwMzExMjNl', 's1teknikkomputer', '2025', 'f53eb48005', 'Palu', '2005-06-14', 'Btn muspratama block c no 12, luwuk utara, kabupaten banggai, sulawesi tengah', ''),
('MOH. REZKY FAHREZI DATAU', '05202425032', 'c50379ff637a8bb1a3079e8315724931', 'mohrezkydatau@gmail.com', '082191298439', 'Aktif', 'MDUyMDI0MjUwMzIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('RIDHO ILAHI ALI', '05202425033', '5b3be594e89405ed8e97ecb71cae896d', 'ridhooooali01@gmail.com', '081245010163', 'Aktif', 'MDUyMDI0MjUwMzMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('RAHMAD AFFANDY', '05202425034', '7a0811b80adc3052ba4ca0e9f2b43a06', 'rahmadaffandy58@gmail.com', '082271670018', 'Aktif', 'MDUyMDI0MjUwMzQxMjNl', 's1teknikkomputer', '2025', 'f154c17f55', 'Luwuk, Sulawesi Tengah ', '2007-05-05', 'JL. P.KALIMANTAN NO.34', 'file/05202425034-passfoto.jpg'),
('CARENSYA AULIA HASAN', '05202425035', '4a4a1d773f6d4a28a6df487c84c40d64', 'carensyaaa@gmail.com', '081543233738', 'Aktif', 'MDUyMDI0MjUwMzUxMjNl', 's1teknikkomputer', '2025', 'f154c17f55', 'gorontalo', '2006-02-08', 'desa bube kec.suwawa kab.bonebolango', 'file/05202425035-passfoto.jpeg'),
('MOAMMAR BILAL HARUN', '05202425036', '3a2010d986f8e6a584148c8ed7857c79', 'moammarbilal@gmail.com', '0895712431616', 'Aktif', 'MDUyMDI0MjUwMzYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ASYIFA RIANA APRILIA', '05202425037', 'eba41e054d8d1ef72e3576ecaff6e4ab', 'renaaprilia217@gmail.com', '081340825380', 'Aktif', 'MDUyMDI0MjUwMzcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MELIANSARI DUGIAN', '05202425038', '0fa8d358e8b33b88070b96d12d2dff01', 'imelldgian@gmail.com', '0895351664955', 'Aktif', 'MDUyMDI0MjUwMzgxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD HIDAYATULLAH DUNGGA', '05202425039', 'c2a243bdaf30951f80803183537e41bd', 'dayatdungga59@gmail.com', '085183105158', 'Aktif', 'MDUyMDI0MjUwMzkxMjNl', 's1teknikkomputer', '2025', NULL, 'Kab. Gorontalo', '2007-05-15', 'Lingkungan II\n', 'file/05202425039-passfoto.jpg'),
('RAHMATIA ALAM', '05202425040', '0b2061e5869700f20407d1f300a32f30', 'jubazzx04@gmail.com', '081356571256', 'Aktif', 'ZW5naW5lZXJpbmc=', 's1teknikkomputer', '2025', NULL, 'Gorontalo', '2008-04-03', '', 'file/05202425040-passfoto.jpeg'),
('VINA UMAR', '05202425041', 'd321974ab7d1a7832d14d89f9ef81b2e', 'vinaumar197@gmail.com', '081354845325', 'Aktif', 'MDUyMDI0MjUwNDExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOHAMMAD AMAR FAJRI NAPU', '05202425042', 'e4c94182d0ae653f04bb3cda7d6f9210', 'zufriadinapu@gmail.com', '082293364440', 'Aktif', 'MDUyMDI0MjUwNDIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD HIDAYAH MOZADEK', '05202425043', '63ec7457765166aea13efac5464d9f29', 'dayatmozadek@gmail.com', '082291058789', 'Aktif', 'MDUyMDI0MjUwNDMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('RAFIF RIZQULLAH', '05202425044', 'bb0a773e0d39fdd4a30126e5b2472a3a', 'rafifrizqullah77@gmail.com', '082259025213', 'Aktif', 'MDUyMDI0MjUwNDQxMjNl', 's1teknikkomputer', '2025', 'a5e8c20ec3', 'Nunurantai', '2007-11-03', 'Dusun II Desa Nunurantai, Kec. Taopa, Kab. Parigi Moutong ', 'file/05202425044-passfoto.jpg'),
('RISNAYANTI N. SAURING', '05202425045', '2b5112b80d74fbc343e4285a9a7ee7cf', 'isnasauring8@gmail.com', '082211604507', 'Aktif', 'MDUyMDI0MjUwNDUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NAILA MAHDALI', '05202425046', 'cee5ecad720aa48c432eb54d7acd1923', 'naylamahdali@gmail.com', '082125546379', 'Aktif', 'MDUyMDI0MjUwNDYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('BARTOLOMUS DIAZ RUINT', '05202425047', 'e2c003ae380ad18dc8beaf1bf947c368', 'bartruint@gmail.com', '085338384031', 'Aktif', 'MDUyMDI0MjUwNDcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('VALENCIA PRICILIA HONGI', '05202425048', '477c38297c3076dd1a99375ecc18444c', 'valensiaphongi@gmail.com', '082393500439', 'Aktif', 'MDUyMDI0MjUwNDgxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('DICKY ANANDA YUDANA', '05202425049', '555c961a00ba7af2355690415c87c2d3', 'dickyananda107@gmail.com', '081377652297', 'Aktif', 'MDUyMDI0MjUwNDkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('LUTHFI IRFANI MAULANA MAHMUD', '05202425050', 'a36e470aa7f6b05a8b58546f12678bc3', 'luthfimandiaz@gmail.com', '082182311125', 'Aktif', 'MDUyMDI0MjUwNTAxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('SITI FADHILA MAS HANAPI', '05202425051', 'ac2e8cfb52c5e144d1c106c239245d1a', 'sf065003@gmail.com', '082346426481', 'Aktif', 'MDUyMDI0MjUwNTExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUH. FAJRIANSYAH HADJU', '05202425052', '0e0fec9b6bdd60794169108b52200ad4', 'ittoushura826@gmail.com', '082291322196', 'Aktif', 'MDUyMDI0MjUwNTIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NAYLA ALIFA ADJIDJI', '05202425053', '717c3bdf407cf5db8def80b30e4c90b7', 'nemga856@gmail.com', '082261324856', 'Aktif', 'MDUyMDI0MjUwNTMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('CINDRAWATI MAANAIYA', '05202425054', 'f0598123c293522db6c244d303bfdd06', 'cindimaanaiya386@gmail.com', '081354814972', 'Aktif', 'MDUyMDI0MjUwNTQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('SULFIKA S.T', '05202425055', '35e4e22f378041c4a260a9503260fa34', 'sulfikast@gmail.com', '082210455905', 'Aktif', 'MDUyMDI0MjUwNTUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('AWWAL NASHRULLAH DJAMALUDIN', '05202425056', '297e1b23b4fb5473559f344cc3b7c929', 'awwalnashrullah4@gmail.com', '082194282128', 'Aktif', 'MDUyMDI0MjUwNTYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('FADHIL RAHMAN SIDIKI', '05202425057', '4dbfc47a1eab00083c8239c724455a43', 'fadhilsidiki2314@gmail.com', '085756456517', 'Aktif', 'MDUyMDI0MjUwNTcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOHAMAD RIZAL BARUADI', '05202425058', '27f8b45b9f45b573d7144e40f769a7e6', 'mohamadrizalbaruadi07@gmail.com', '085144387347', 'Aktif', 'MDUyMDI0MjUwNTgxMjNl', 's1teknikkomputer', '2025', '274eccd1e7', 'gorontalo', '2007-02-06', '', ''),
('NUR ADELIA PAKAYA', '05202425059', '20c4eeefdf662389908da94c52be042c', 'adeliapakaya36@gmail.com', '082291322670', 'Aktif', 'MDUyMDI0MjUwNTkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('FAREL HAQ', '05202425060', 'cfbeda3dfa6be4b6a83582883f63ca6c', 'farelfarelhaq@gmail.com', '083867955747', 'Aktif', 'MDUyMDI0MjUwNjAxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('DWIKY RAMDHANI', '05202425061', 'b0d23dc40ccaa7c707a1f735d9c6e307', 'dwikyramdaniibrahi@gmail.com', '082256273274', 'Aktif', 'MDUyMDI0MjUwNjExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('FIDYA M ASAHOYA', '05202425062', 'ec4aafb09b58fe794e3ce0b87b228e2a', 'asahoyafidya@gmail.com', '089692202296', 'Aktif', 'MDUyMDI0MjUwNjIxMjNl', 's1teknikkomputer', '2025', '5864493a02', 'Ampana', '2007-10-22', 'Jl.PABRIK IKAN,RT.001/RW.002,KEL.BAILO,KEC.AMPANA KOTA,KAB.TOJO UNA-UNA', 'file/05202425062-passfoto.jpg'),
('PUTRA RADIT RUMIMPUNU', '05202425063', '7b40e2de8b88d35f4ff8c2e024e1978d', 'putraraditrumimpunu@gmail.com', '085282909834', 'Aktif', 'MDUyMDI0MjUwNjMxMjNl', 's1teknikkomputer', '2025', '14c4f7be30', 'Buata', '2006-09-24', 'Desa posono, Kec. Atinggola Kab. Gorontalo Utara, Prov. Gorontalo', 'file/05202425063-passfoto.jpg'),
('RAHMAT HIDAYAT PONGOLIU', '05202425064', '2648a63581363f6983952ba205df3858', 'hidayatpongoliu2@gmail.com', '082259281897', 'Aktif', 'MDUyMDI0MjUwNjQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOH. ARGA PUTRA LAMUSU', '05202425065', 'ced58daca7b7358c830733c0e70988fa', 'argalamusu7@gmail.com', '081242664833', 'Aktif', 'MDUyMDI0MjUwNjUxMjNl', 's1teknikkomputer', '2025', '14c4f7be30', 'GORONTALO', '2007-07-23', 'JL. SARINI ABDULLAH', 'file/05202425065-passfoto.jpeg'),
('TRIFENA ANGEL POLA', '05202425066', '734e4bb11513aa24480f64d895b5f5db', 'angeltrvpola07@gmail.com', '085298834393', 'Aktif', 'MDUyMDI0MjUwNjYxMjNl', 's1teknikkomputer', '2025', '7fbcc3969e', 'GORONTALO', '2007-06-14', '', ''),
('CUTYANITA PANIGORO', '05202425067', 'e07ca2fb96fb6ee9655e2175ce2c0fca', 'cutyaa25@gmail.com', '085166369413', 'Aktif', 'MDUyMDI0MjUwNjcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('NUR\'ASMI A. TOTILE', '05202425068', 'b6b92be281aaaad1b30a2a02464ca332', 'asmitotile581@gmail.com', '082232840415', 'Aktif', 'MDUyMDI0MjUwNjgxMjNl', 's1teknikkomputer', '2025', '7fbcc3969e', 'Saleati', '2005-02-03', 'Desa Saleati Kecamatan Liang Kab. Banggai Kepulauan Provinsi Sulawesi Tengah', 'file/05202425068-passfoto.jpg'),
('ADRIANSYAH D. ADJUNU', '05202425069', 'f889bd936ed2d4f4e4b528aede7c22a2', 'rasyaadjunu01@gmail.com', '085696363441', 'Aktif', 'MDUyMDI0MjUwNjkxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('APRIANSYAH A. YANALIA', '05202425070', 'd67c69d29c4d49cc06e8df71c6984760', 'apriapriansahayanalia@gmail.com', '085359951098', 'Aktif', 'MDUyMDI0MjUwNzAxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOHAMAD GABRIANSYAH HASAN', '05202425071', '521c583b25d90b0fc6dc77a26403152c', 'gabriansyahhasan@gmail.com', '089524424221', 'Aktif', 'MDUyMDI0MjUwNzExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', '');
INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('MUHAMMAD RAMADHAN RACHMAN', '05202425072', '45e3a271ae605bcdb0464bd7e6490ff4', 'rahmanaan46@gmail.com', '0895704554222', 'Aktif', 'UmFjaG1hbjAx', 's1teknikkomputer', '2025', '98a4a6f7b7', 'Gorontalo', '2006-12-27', 'JL. BELIMBING BLOK H/7', 'file/05202425072-passfoto.jpg'),
('GUSTI GEDE ARI DARMADI', '05202425073', '87b0b2da7b5cc69f497c63635c0fa593', 'arigusti211@gmail.com', '085256016481', 'Aktif', 'MDUyMDI0MjUwNzMxMjNl', 's1teknikkomputer', '2025', '98a4a6f7b7', 'DAMAI MAKMUR', '2007-03-20', 'DAMAI MAKMUR', 'file/05202425073-passfoto.png'),
('MOH.RIFKY SAPUTRA CHAERUDDIN', '05202425074', 'c3bc2f45b2ee9aea4cfab20cbaa1d486', 'rifqysaputrarifqy5544@gmail.com', '082271239878', 'Aktif', 'MDUyMDI0MjUwNzQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ALICA LUTFIAH MATOKA', '05202425075', '7a6fb169ec2064acbe4130a902ce8cc7', 'alicalutfiahmatoka@gmail.com', '082348163805', 'Aktif', 'MDUyMDI0MjUwNzUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('RISKA UMAR', '05202425076', '3baa3f48745585610e61593e477322f3', 'riskaumar2146@gmail.com', '082395190691', 'Aktif', 'MDUyMDI0MjUwNzYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('SARTIKA MANTULANGI', '05202425077', 'ea254c658ae2295ca44d9b92ce966962', 'sartikamantulangi07@gmail.com', '081243547360', 'Aktif', 'MDUyMDI0MjUwNzcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MOHAMAD RIZALDY RAHMAN', '05202425078', '6fa1ddb9541ce5af4182edb373be0724', 'rizaldirahman2305@gmail.com', '089618228247', 'Aktif', 'MDUyMDI0MjUwNzgxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('ARBAREZI MANGKU RAGA', '05202425079', 'f500dd7ff5f0e80a3c1f7529eef41ae3', 'arbarezi20gb@gmail.com', '081356336553', 'Aktif', 'MDUyMDI0MjUwNzkxMjNl', 's1teknikkomputer', '2025', '92459d083c', 'Luwuk', '2008-01-28', '', ''),
('ADESATYA ABDULLAH', '05202425080', 'bb0829dc2554f916b176f336c754bd36', 'adesatyaabd29@gmail.com', '082237032129', 'Aktif', 'MDUyMDI0MjUwODAxMjNl', 's1teknikkomputer', '2025', 'f154c17f55', 'Randangan', '2007-07-27', 'Dusun Sigatange, Desa Motolohu Selatan, Kecamatan Randangan', 'file/05202425080-passfoto.jpeg'),
('ALI FURQAN LATIEF', '05202425081', 'e9de52b6f77ef9afe6f6c5c21fc5b2ec', 'alifurqan.latief06@gmail.com', '082191826541', 'Aktif', 'MDUyMDI0MjUwODExMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('AHMAD FARHAN MARDANI JUSUF', '05202425082', 'c3846f4127c2ad17221c2c49be3c7c4c', 'farhanjusuf69@gmail.com', '082259593034', 'Aktif', 'MDUyMDI0MjUwODIxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('RAHMAD SAFRIZAL LABUGA', '05202425083', 'fcb5fc51bce99eb8a60a7343df988384', 'labugasapri@gmail.com', '082196759441', 'Aktif', 'MDUyMDI0MjUwODMxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('QUDDRATUL ALIF', '05202425084', '4edde1af0c3d2cfb90e00d48d7e5dc58', 'alifparasila@gmail.com', '083896860921', 'Aktif', 'MDUyMDI0MjUwODQxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('FADIQ ALBANI SYAPUTRA NASARU', '05202425085', '6957ce9bb3e119fa350c9627a1ef27bb', 'evannasaru22@gmail.com', '0853960660593', 'Aktif', 'MDUyMDI0MjUwODUxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('MUHAMMAD TAUFIK RAJAB', '05202425086', '4545f35aadb0c8446f5d5229040aa02a', 'taufikrajabkece@gmail.com', '081343665936', 'Aktif', 'MDUyMDI0MjUwODYxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('DENDRA W.PRATAMA S.', '05202425087', 'da4707b553013af043beea4ef8bf8069', 'dendraw773@gmail.com', '085751631242', 'Aktif', 'MDUyMDI0MjUwODcxMjNl', 's1teknikkomputer', '2025', NULL, '', NULL, '', ''),
('Mohamad Alif S. Tagoi', '05202426001', '4e8c5831dce66a067274397fea878a32', 'mohammadalip036@gmail.com', '082195595606', 'Aktif', 'NjQ3MjM=', 's1teknikomputer', '2026', NULL, 'OLUHUTA', '2009-06-22', 'JL. TRANS SULAWESI, Kel/Desa Oluhuta Kec. Kabila Bone KAB. BONE BOLANGO - GORONTALO', ''),
('Aura Setia Bonok', '05202426002', 'fcd7d9666025d4d8a99556547e9eab5c', 'setiabonok@gmail.com', '085820275374', 'Aktif', 'OTE3NjQ=', 's1teknikomputer', '2026', NULL, 'MOPUSI', '2009-02-22', 'RAYA MOPUSI, Kel/Desa Mopusi Kec. Lolayan KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Adriansyah Putra Ismail', '05202426003', '194538bbcbf5b0a6571507a37a30d093', 'adriansyahismailsnbp@gmail.com', '0895337011131', 'Aktif', 'ODc3ODQ=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-08-23', 'JLN.JOHN ARIYO KATILI, Kel/Desa Padengo Kec. Kabila KAB. BONE BOLANGO - GORONTALO', ''),
('Desi Nuraini', '05202426004', '29381db68a02ca7bf84d1dc244e82106', 'desinuraini920@gmail.com', '081340256660', 'Aktif', 'NDI4NTE=', 's1teknikomputer', '2026', NULL, 'BUKIT MAMBUAL', '2008-12-25', 'BUWONO ABDULLAH, Kel/Desa Molowahu Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Suaib Kilo', '05202426005', '4e90f57926b046be13fb67c3d41a2543', 'suaibkilo07@gmail.com', '089504281666', 'Aktif', 'NzkwODE=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-02-25', 'JL. AYUBA YUSUF, Kel/Desa Timuato Kec. Telaga Biru KAB. GORONTALO - GORONTALO', ''),
('Gavin Ardyza Datau', '05202426006', '5b5179fb7d2e06600514fcf27e02919d', 'gavinardyza@gmail.com', '089601586347', 'Aktif', 'NzM0MTk=', 's1teknikomputer', '2026', NULL, 'BANDUNG', '2008-03-19', 'JL. JAKSA AGUNG SUPRAPTO NO. 42, Kel/Desa Limba U II Kec. Kota Selatan KOTA GORONTALO - GORONTALO', ''),
('Alya Dunggio', '05202426007', '66bcdccaa101d14df79e72cda1ae2127', 'alyadunggio11@gmail.com', '085394194563', 'Aktif', 'MzIwMjE=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2007-09-02', 'ARJUNA, Kel/Desa Sidomukti Kec. Mootilango KAB. GORONTALO - GORONTALO', ''),
('Putri Ayu Djafar', '05202426008', '4085268be322c3fc24b36d13f463d069', 'putriayudjafar@gmail.com', '0895346750704', 'Aktif', 'MzA1NTI=', 's1teknikomputer', '2026', NULL, 'BUATA', '2007-06-24', 'JLN. MUCHLIS RAHIM, Kel/Desa Tanah Putih Kec. Botupingge KAB. BONE BOLANGO - GORONTALO', ''),
('Salsabila Mogulaingo', '05202426009', 'aec87349e0792883d18f44549b5e9d5f', 'salsabilamogulaingo30@gmail.com', '085757321074', 'Aktif', 'Njg0NjI=', 's1teknikomputer', '2026', NULL, 'MOTOLOHU', '2008-08-30', 'JALAN TRANS SULAWESI LINTAS SELATAN, DESA MOTOLOHU, Kel/Desa Motolohu Kec. Helumo KAB. BOLAANG MONGONDOW SELATAN - SULAWESI UTARA', ''),
('Muzakky Fathurrahman Tolago', '05202426010', '6a065a0e2783f0e9d6edf6ef21be85c2', 'zakky2738tolago@gmail.com', '085298838139', 'Aktif', 'MzU5MTM=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-03-27', 'JL. SELAYAR BLOK A3 NO.5, Kel/Desa Pulubala Kec. Kota Tengah KOTA GORONTALO - GORONTALO', ''),
('Rezky Aditya Hilimi', '05202426011', '385e662d89a87de1ac1131f11624d51a', 'adityahilimi@gmail.com', '085397696660', 'Aktif', 'NzQyMzg=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2009-07-04', 'JALAN PERTIWI DUSUN SATRIA UTAMA, Kel/Desa Titidu Kec. Kwandang KAB. GORONTALO UTARA - GORONTALO', ''),
('Khalifka Anugrah Belenehu', '05202426012', '4994e9e1e036d592dce9fb7ac9ea29d3', 'alifbelenehu@gmail.com', '085398229854', 'Aktif', 'NTYxODc=', 's1teknikomputer', '2026', NULL, 'SININDIAN', '2009-01-06', 'JL. K.S. TUBUN SININDIAN LORONG BOUGENVILLE, Kel/Desa Sinindian Kec. Kotamobagu Timur KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Qirana Azahra  I. Lamahuseng', '05202426013', 'e43f5069ea828f7eb801ba44ebba4498', 'qiranaazzahra55@gmail.com', '082274498396', 'Aktif', 'NjUwMDM=', 's1teknikomputer', '2026', NULL, 'AMPANA', '2008-05-05', 'POPOLII, Kel/Desa Popolii Kec. Walea Kepulauan KAB. TOJO UNA UNA - SULAWESI TENGAH', ''),
('Rahmatia Mohamad', '05202426014', '8035ddb232b0caf1c1b8853fb41dc533', 'rahmatiamohamad30@gmail.com', '085166836314', 'Aktif', 'MTY4MzY=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-01-30', 'JL POIGAR, Kel/Desa Molosipat U Kec. Sipatana KOTA GORONTALO - GORONTALO', ''),
('Djulfikar Van Gobel', '05202426015', 'dbf43fc5bddff1237123767eb95ef6e2', 'pikargobel617@gmail.com', '082293633994', 'Aktif', 'Nzg5Mzc=', 's1teknikomputer', '2026', NULL, 'BATUBANTAYO', '2008-01-06', 'DESA BATUBANTAYO, Kel/Desa Batu Bantayo Kec. Pinogaluman KAB. BOLAANG MONGONDOW UTARA - SULAWESI UTARA', ''),
('Anggi Sugiarti Hadi', '05202426016', '43f02fc20fc90e822c747827bf752a66', 'anggiicantikk87@gmail.com', '085757358423', 'Aktif', 'ODAxOTY=', 's1teknikomputer', '2026', NULL, 'SAPA', '2008-10-23', 'JL WIRATAMA DESA MOONOW, Kel/Desa Moonow Kec. Modayag Barat KAB. BOLAANG MONGONDOW TIMUR - SULAWESI UTARA', ''),
('Ahmad Fauzan Mamonto', '05202426017', '5b50d5c44c00325a918c7b4e11ce05be', 'af5170484@gmail.com', '085756906620', 'Aktif', 'Mjg1MDI=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2009-02-18', 'KAMPUNG BARU, Kel/Desa Modayag Kec. Modayag KAB. BOLAANG MONGONDOW TIMUR - SULAWESI UTARA', ''),
('Prawira Lestari Lantang', '05202426018', '8b69e4f33b5d705714143b778aff1cd9', 'prawiralestari5@gmail.com', '083192353522', 'Aktif', 'NDE0OTc=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-03-01', 'JL. MAYOR DULLAH, Kel/Desa Talumolo Kec. Dumbo Raya KOTA GORONTALO - GORONTALO', ''),
('Shiren Syafani Hadadi', '05202426019', '63352d7593948ccf0b4ffca9ac6b78c3', 'shirenhadadi@gmail.com', '085241126804', 'Aktif', 'NTgzODI=', 's1teknikomputer', '2026', NULL, 'BOLAANG MONGONDOW UTARA', '2008-03-07', 'JL DESA BUKO UTARA, Kel/Desa Buko Kec. Pinogaluman KAB. BOLAANG MONGONDOW UTARA - SULAWESI UTARA', ''),
('Siti Rahmatia Nur Syawal Katili', '05202426020', '31ad3ddcc86cdba2404a85b6e938c290', 'rahmatiakatili9@gmail.com', '085757259317', 'Aktif', 'ODU3NTI=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-10-19', 'JL. PANCAWARDANA, Kel/Desa Padebuolo Kec. Kota Timur KOTA GORONTALO - GORONTALO', ''),
('Steven Christian Sirupa', '05202426021', '3689e836e62f775ccd3839f7703dc692', 'steven08.ch@gmail.com', '08980237243', 'Aktif', 'MTcxNjM=', 's1teknikomputer', '2026', NULL, 'MAKASSAR', '2008-11-01', 'JL. APEL, Kel/Desa Huangobotu Kec. Dungingi KOTA GORONTALO - GORONTALO', ''),
('Sisi Prisilia Eka', '05202426022', 'a9503ea016ea97840e4393cf3cb5c67b', 'aryatisaleh02@gmail.com', '08386483259', 'Aktif', 'NjAwNDM=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-02-19', 'JLN.TRANS SULAWESI, Kel/Desa Motilango Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Muhamad Hajril Djabar', '05202426023', 'f41ad6377ef748487953e5477df3bb32', 'djabarhajril@gmail.com', '089603945958', 'Aktif', 'MDUyNDk=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-12-06', 'GOGAGOMAN, Kel/Desa Gogagoman Kec. Kotamobagu Barat KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Muhammad Alfajri Makalalag', '05202426024', '50169f34c0d3778b5f04405ac821d057', 'fajri.mkllg.25@gmail.com', '085825232983', 'Aktif', 'NjcwOTI=', 's1teknikomputer', '2026', NULL, 'MOLINOW', '2008-03-22', 'JL. HI. ZAKARIA IMBAN, Kel/Desa Molinow Kec. Kotamobagu Barat KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Siti Fadilah Tajulalam Pomolango', '05202426025', '867778a7ca75a51aca21da1f166cc420', 'fadilapomolango22@gmail.com', '082293132863', 'Aktif', 'OTA4ODM=', 's1teknikomputer', '2026', NULL, 'BOROKO', '2008-08-24', 'JL.PELABUHAN, Kel/Desa Boroko Kec. Kaidipang KAB. BOLAANG MONGONDOW UTARA - SULAWESI UTARA', ''),
('Zulkaida Halid', '05202426026', 'd3104ad9d7368d19c6d269347db39275', 'aidahalid70@gmail.com', '082191003451', 'Aktif', 'NTI5NjU=', 's1teknikomputer', '2026', NULL, 'DALAPULI', '2008-07-01', 'JL. TRANS SULAWESI, Kel/Desa Dalapuli Timur Kec. Pinogaluman KAB. BOLAANG MONGONDOW UTARA - SULAWESI UTARA', ''),
('Sitti Nur Fadila Muslim', '05202426027', '40aded3dbf70e5a6e6ba7671f45ccc75', 'ssp22904@gmail.com', '0895803352150', 'Aktif', 'OTc0MjE=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-02-15', 'JL. PINANG TIMUR 1, Kel/Desa Pulubala Kec. Kota Tengah KOTA GORONTALO - GORONTALO', ''),
('Mohammad Ridwan Potale', '05202426028', '5feea040fc668a06929b6bebfdc0f020', 'ridwanpotalef@gmail.com', '082188251281', 'Aktif', 'MDAxMDc=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-04-27', 'JLN TELAGA, Kel/Desa Gogagoman Kec. Kotamobagu Barat KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Walfadli Damopolii', '05202426029', '1f32478423848734750a964015af3c92', 'walfadlidamopolii05@gmail.com', '085656604570', 'Aktif', 'MDIwNzc=', 's1teknikomputer', '2026', NULL, 'KOPANDAKAN', '2008-10-05', 'JLN.RAYA DESA KOPANDAKAN 2, Kel/Desa Kopandakan II Kec. Lolayan KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Aril Bahtiar Imran', '05202426030', 'aeedd667902309aa7913d360fd7653f0', 'arilimran55@gmail.com', '082188211209', 'Aktif', 'MTE4Mzg=', 's1teknikomputer', '2026', NULL, 'KAB . GORONTALO', '2008-05-05', 'SUKA MULIA, Kel/Desa Suka Mulya Kec. Wonosari KAB. BOALEMO - GORONTALO', ''),
('Ilham Khairudin Djafar', '05202426031', '98d9384a9f10bd2ea4dc0d1f4d464ce2', 'ilhamkhairudindjafar@gmail.com', '085137099008', 'Aktif', 'MTMzODk=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2008-07-05', 'JL. RAJA BOBIHOE, Kel/Desa Limehe Timur Kec. Tabongo KAB. GORONTALO - GORONTALO', ''),
('Iksan Gunawan', '05202426032', '8ce8bd0d59e34030f48856f2c76d286a', 'ilksangun123@gmail.com', '089699780801', 'Aktif', 'Njk5Mjc=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-06-20', 'JL. WS AMPARODO, Kel/Desa Molinow Kec. Kotamobagu Barat KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Kelvin a. Iyati', '05202426033', '6f786f0af516ed30a31eefe12d3bf5eb', 'kelvin.a134@smk.belajar.id', '082236787064', 'Aktif', 'NDEyMzQ=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-12-19', 'JLN. BUKE PANAI, Kel/Desa Lobuto Timur Kec. Biluhu KAB. GORONTALO - GORONTALO', ''),
('Zaky El Izzi Syam', '05202426034', '2b5d12a4306a67ee3559a5a2f56a347c', 'zakyelizzis@gmail.com', '081346777177', 'Aktif', 'MDE4MTY=', 's1teknikomputer', '2026', NULL, 'PALU', '2008-06-14', 'JL. KH. ADAM ZAKARIA, Kel/Desa Wongkaditi Barat Kec. Kota Utara KOTA GORONTALO - GORONTALO', ''),
('Elok Dwi Pujianingsih', '05202426035', 'c9cc9f8d4417dd9734a5c681c757df6c', 'segoketan929@gmail.com', '085346082832', 'Aktif', 'NjEzOTU=', 's1teknikomputer', '2026', NULL, 'KALIMAS', '2007-12-13', 'TRANS TALUDITI, Kel/Desa Kalimas Kec. Taluditi KAB. POHUWATO - GORONTALO', ''),
('Mandalika Menu', '05202426036', '5831fdd6bc33e8cfe79eace78620b5eb', 'mndalikamenu@gmail.com', '085166303578', 'Aktif', 'OTUyMDY=', 's1teknikomputer', '2026', NULL, 'TABULO', '2007-07-11', 'DUSUN MAKMUR, Kel/Desa Tabulo Kec. Mananggu KAB. BOALEMO - GORONTALO', ''),
('Siti Rizkina Polapa', '05202426037', '8d6c818100947ef2b9c782974aae36ad', 'sitypolapa13@gmail.com', '082395278196', 'Aktif', 'NjYyOTA=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-04-09', 'JL DESA BUKO  KAB. BOLAANG MONGONDOW UTARA  PROV. SULAWESI UTARA 95765, Kel/Desa Buko Kec. Pinogaluman KAB. BOLAANG MONGONDOW UTARA - SULAWESI UTARA', ''),
('Wali Akbar', '05202426038', '2f6bd5da2ce5f225433baa73ea89386b', 'walizxakbar@gmail.com', '085136273640', 'Aktif', 'MDg3MTU=', 's1teknikomputer', '2026', NULL, 'MACINNA', '2007-12-10', 'JL. UPT  KAB. POHUWATO  PROV. GORONTALO 96266, Kel/Desa Mootilango Kec. Duhiadaa KAB. POHUWATO - GORONTALO', ''),
('Syahril Zulfikran Mamu', '05202426039', '0a0d96556134ff56cac72dbe19182853', 'syahril.zulfikran32@sma.belajar.id', '085298838029', 'Aktif', 'MzQ4ODA=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2008-01-20', 'BUKE PANAI  KAB. GORONTALO  PROV. GORONTALO 96272, Kel/Desa Bongo Kec. Batudaa Pantai KAB. GORONTALO - GORONTALO', ''),
('Chelsea Aprilia Lasimpala', '05202426040', '2d2dcbaa10ccfe2b60d775c11ff98e8f', 'chelsealasimpala22@gmail.com', '082190308538', 'Aktif', 'NjQ3MTA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-04-28', '23 JANUARI  KOTA GORONTALO  PROV. GORONTALO 96112, Kel/Desa Ipilo Kec. Kota Timur KOTA GORONTALO - GORONTALO', ''),
('Abd. Rasyid Lasulika', '05202426041', '8d3f4265ae84f710efd8cad23a461acd', 'rasyalasulika@gmail.com', '085394050318', 'Aktif', 'MzY4MDA=', 's1teknikomputer', '2026', NULL, 'SUWAWA', '2007-10-04', 'JL. TINELO  KAB. BONE BOLANGO  PROV. GORONTALO 96184, Kel/Desa Tinelo Kec. Suwawa KAB. BONE BOLANGO - GORONTALO', ''),
('Mohamad Aidil Lakoro', '05202426042', '302bd037a8ffee0c581a611afda69262', 'aidilmuhammad1909@gmail.com', '082259569310', 'Aktif', 'NDY3NzA=', 's1teknikomputer', '2026', NULL, 'DULUPI', '2008-09-19', 'DUSUN IV LABUHAN TIMUR  KAB. BOALEMO  PROV. GORONTALO 96267, Kel/Desa Tabongo Kec. Dulupi KAB. BOALEMO - GORONTALO', ''),
('Abdul Rajak Usman', '05202426043', '5e70db50307e85a4a7308e9e5c07fd86', 'abdulrajakusman4@gmail.com', '087765860761', 'Aktif', 'NjE4ODk=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2008-03-01', 'TULADENGGI  KAB. GORONTALO  PROV. GORONTALO 96147, Kel/Desa Tuladenggi Kec. Telaga Biru KAB. GORONTALO - GORONTALO', ''),
('Moh. Syukur a. Pudjiomo', '05202426044', 'cce9e281cfa92af7277297edfb8424f6', 'pudjiomosyukur@gmail.com', '085345759608', 'Aktif', 'Mjk1Mzg=', 's1teknikomputer', '2026', NULL, 'LAMBAKO', '2008-06-30', 'JL. LAPANGAN, Kel/Desa Lambako Kec. Banggai KAB. BANGGAI LAUT - SULAWESI TENGAH', ''),
('Samsudin S. Berahim', '05202426045', '06f1298cb1b00093457ea67e03110058', 'samsudin.s286@sma.belajar.id', '082259758994', 'Aktif', 'MTA0ODI=', 's1teknikomputer', '2026', NULL, 'BUOL', '2007-07-19', 'DESA KWALABESAR  KAB. BUOL  PROV. SULAWESI TENGAH 94568, Kel/Desa Kwalabesar Kec. Paleleh KAB. BUOL - SULAWESI TENGAH', ''),
('Andre Yudistira Saputra H. Adji', '05202426046', 'c80ee32efc03a6ce1df58231c9ac53e1', 'andreyudistirasaputrahadji@gmail.com', '083133760378', 'Aktif', 'ODEwMjA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-12-30', 'JLN. RAJA BOBIHOE, Kel/Desa Bua Kec. Batudaa KAB. GORONTALO - GORONTALO', ''),
('Suherno Jamaludin Tuna', '05202426047', 'd6b414bbd66c9699dcf4d5df71637f8b', 'suhernojtuna@gmail.com', '085147708140', 'Aktif', 'NTk4NzE=', 's1teknikomputer', '2026', NULL, 'WAKAI', '2008-02-08', 'JL. G. RAPAMBAI  KAB. TOJO UNA UNA  PROV. SULAWESI TENGAH 94685, Kel/Desa Ampana Kec. Ampana Kota KAB. TOJO UNA UNA - SULAWESI TENGAH', ''),
('Brilian Alif Naufal Sumarauw', '05202426048', 'dcd89a3241b93e6b306634f209374a97', 'briliansumarauwgi@gmail.com', '082194658923', 'Aktif', 'MzQ3NDA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-06-14', 'JALAN  KOTA GORONTALO  PROV. GORONTALO 96136, Kel/Desa Buladu Kec. Kota Barat KOTA GORONTALO - GORONTALO', ''),
('Angki Aksandi S Rudji', '05202426049', 'a070e160d605f096a0a1cc6e20739f72', 'angkiaksandi@gmail.com', '085119980491', 'Aktif', 'NTg4Nzg=', 's1teknikomputer', '2026', NULL, 'LOPITO', '2006-07-08', 'DESA LOPITO  KAB. BANGGAI KEPULAUAN  PROV. SULAWESI TENGAH 94784, Kel/Desa Lopito Kec. Totikum KAB. BANGGAI KEPULAUAN - SULAWESI TENGAH', ''),
('Muhammad Farhan Dalu', '05202426050', '10e1908a30e02fff8c404e108f1a35ce', 'farhandalu19@gmail.com', '081245075004', 'Aktif', 'MDAyMDI=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-03-19', 'JL. PROF. DR. ALOE SABOE  KOTA GORONTALO  PROV. GORONTALO 96128, Kel/Desa Wongkaditi Timur Kec. Kota Utara KOTA GORONTALO - GORONTALO', ''),
('Jul Rahmat Hulopi', '05202426051', 'eae726f4d9f5c2a7ea06d0550ca19443', 'titohulopi@gmail.com', '081243517236', 'Aktif', 'MDE5NTc=', 's1teknikomputer', '2026', NULL, 'TANJUNG KARANG', '2007-06-28', 'TRANS SULAWESI  KAB. GORONTALO UTARA  PROV. GORONTALO 96252, Kel/Desa Huidu Melito Kec. Tomilito KAB. GORONTALO UTARA - GORONTALO', ''),
('Rusdiyanto R. Laani', '05202426052', '9f16255ce17ae00fc515c544fdf5c3d7', 'rusdianto37b@gmail.com', '085126373646', 'Aktif', 'OTg4MzQ=', 's1teknikomputer', '2026', NULL, 'BUNGIN', '2006-07-30', 'DESA BUNGIN  KAB. BANGGAI LAUT  PROV. SULAWESI TENGAH 98492, Kel/Desa Bungin Kec. Bokan Kepulauan KAB. BANGGAI LAUT - SULAWESI TENGAH', ''),
('Jafrin Nusi', '05202426053', 'e006ea83babe0cb57aa2866970bac000', 'jafrinnusi3101@gmail.com', '089518146076', 'Aktif', 'Mzk0NDM=', 's1teknikomputer', '2026', NULL, 'KAB GORONTALO', '2008-01-31', 'DESA BUNGGALO  KAB. GORONTALO  PROV. GORONTALO 96181, Kel/Desa Bunggalo Kec. Talaga Jaya KAB. GORONTALO - GORONTALO', ''),
('Musyari Ilyas Mamonto', '05202426054', '3966b52dfa8b82b56e8f6b974e3594f7', 'adhellsyifa@gmail.com', '085656607677', 'Aktif', 'Mjg1ODQ=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-02-10', 'JL. H.U MOOTALU, TELAGA - PILOHAYANGA  KAB. GORONTALO  PROV. GORONTALO 96181, Kel/Desa Pilohayanga Kec. Telaga KAB. GORONTALO - GORONTALO', ''),
('Haikal Fithar', '05202426055', 'f211f2f2650c704d14f28774aea17317', 'haikalfithar97@gmail.com', '085220786022', 'Aktif', 'Njg2MTc=', 's1teknikomputer', '2026', NULL, 'TOLITOLI', '2008-09-26', 'MADAKO  KAB. TOLITOLI  PROV. SULAWESI TENGAH 94562, Kel/Desa Lingadan Kec. Dako Pemean KAB. TOLI TOLI - SULAWESI TENGAH', ''),
('Nandar Naito', '05202426056', '4b909859ddee902c228b506abe6792d2', 'nandarnaito256@gmail.com', '08991399800', 'Aktif', 'MTA2MjE=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2007-07-25', 'MUSA KALUKU  KAB. GORONTALO  PROV. GORONTALO 96181, Kel/Desa Luwoo Kec. Talaga Jaya KAB. GORONTALO - GORONTALO', ''),
('Abdul Rauf Mokoagow', '05202426057', '106334ff3de3d2512e7628e5703b22ea', 'arraufmokoagow1704@gmail.com', '0851536094', 'Aktif', 'NDMyMzY=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-04-17', 'JLN BABUSSALAM  KAB. BOLAANG MONGONDOW  PROV. SULAWESI UTARA 95761, Kel/Desa Mongkoinit Kec. Lolak KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Tri Dewi Salwa Halada', '05202426058', 'ed458c776d5bb0a28586ab1488bdbc69', 'salwahalada2@gmail.com', '081356739906', 'Aktif', 'MzU4MDk=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-02-26', 'SATSUIT TUBUN NO. 40  KOTA GORONTALO  PROV. GORONTALO 96117, Kel/Desa Tenda Kec. Hulonthalangi KOTA GORONTALO - GORONTALO', ''),
('Moh. Izzul Mutawaqil Moli', '05202426059', '1a773f1596a26016ada4da77dc7a9a4a', 'izungmoli@gmail.com', '0895367917799', 'Aktif', 'NDc5OTg=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-12-27', 'JL. SULTAN BOTUTIHE  KOTA GORONTALO  PROV. GORONTALO 96135, Kel/Desa Tamalate Kec. Kota Timur KOTA GORONTALO - GORONTALO', ''),
('Moh. Irfandi Panggi', '05202426060', 'd850750b5e2cd63c9360f7a5b7bbd34e', 'fandipanggi123@gmail.com', '089525514788', 'Aktif', 'MDYwMTA=', 's1teknikomputer', '2026', NULL, 'KAB GORONTALO', '2008-07-23', 'JL BALAHU  KAB. GORONTALO  PROV. GORONTALO 96251, Kel/Desa Balahu Kec. Tibawa KAB. GORONTALO - GORONTALO', ''),
('Rana Nadhira Lumbengi', '05202426061', '4d4f42f8b095eb6e4eb81c8176807cd0', 'nanabungili@gmail.com', '089694787300', 'Aktif', 'NTE3NTk=', 's1teknikomputer', '2026', NULL, 'KAB GORONTALO', '2008-04-10', 'A.A.WAHAB  KAB. GORONTALO  PROV. GORONTALO 96181, Kel/Desa Pentadio Barat Kec. Telaga Biru KAB. GORONTALO - GORONTALO', ''),
('Moh. Fadly Pratama Matali', '05202426062', '71a940a644c4c97f3fc40cd65c3c7d45', 'fadlymatali07@gmail.com', '082189243500', 'Aktif', 'OTkyMjA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-07-18', 'PERUM EX RD DPRD LIMBOTO NO.30 DESA HUIDU KEC. LIMBOTO BARAT, Kel/Desa Huidu Kec. Limboto Barat KAB. GORONTALO - GORONTALO', ''),
('Azka Sya\'bana', '05202426063', '75b360dc1a8065c1a523a3e763fa7eaa', 'azkasyabana03@gmail.com', '085656542529', 'Aktif', 'MjUxMzQ=', 's1teknikomputer', '2026', NULL, 'MONGONDOW', '2007-08-12', 'JL.BRAWIJAYA  KOTA KOTAMOBAGU  PROV. SULAWESI UTARA 95717, Kel/Desa Mongondow Kec. Kotamobagu Selatan KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Aditya Rafi\' Adam', '05202426064', '662ff26d941fcce433523089a63c5a93', 'adityacr23@gmail.com', '083865217082', 'Aktif', 'NTk1MzA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-08-23', 'JLN MOH THAYEB GOBEL  KAB. BONE BOLANGO  PROV. GORONTALO 96182, Kel/Desa Tinelo Ayula Kec. Bulango Selatan KAB. BONE BOLANGO - GORONTALO', ''),
('Fahriyanto Mayali', '05202426065', 'a08af807be8adea14c0b080fb0c24f48', 'fahriyantomayali0@gmail.com', '085824394153', 'Aktif', 'OTU5ODM=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-01-29', 'DUSUN II  KAB. BOLAANG MONGONDOW SELATAN  PROV. SULAWESI UTARA 95774, Kel/Desa Dudepo Kec. Bolaang Uki KAB. BOLAANG MONGONDOW SELATAN - SULAWESI UTARA', ''),
('Fachri Pradipta Ente', '05202426066', '7cb509db2782173217eda0a892a96bb1', 'fachriente1408@gmail.com', '081356902987', 'Aktif', 'OTU2ODU=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-08-14', 'JL. PASAR MINGGU  KAB. BONE BOLANGO  PROV. GORONTALO 96583, Kel/Desa Olohuta Kec. Kabila KAB. BONE BOLANGO - GORONTALO', ''),
('Mohamad Mahmudin Ibrahim', '05202426067', '5deebae1198492ab0c015992c1e0c1d5', 'ahmadinrahim813@gmail.com', '085166355272', 'Aktif', 'OTgzMzg=', 's1teknikomputer', '2026', NULL, 'LIMBOTO', '2008-03-01', 'JL. TRANSULAWESI DUSUN BULALO, Kel/Desa Marisa Utara Kec. Marisa KAB. POHUWATO - GORONTALO', ''),
('Hezky Rahman Ulyas', '05202426068', 'f4a8a384828224b4ba927f3c3e81e1e0', 'ekiulys@gmail.com', '082290659032', 'Aktif', 'MTNOb3ZlbWJlcg==', 's1pendidikanvokasionalrekayasaelektro', '2026', NULL, 'TABONGO', '2007-11-13', 'DUSUN 7 MEKAR  KAB. BOALEMO  PROV. GORONTALO 96267, Kel/Desa Tabongo Kec. Dulupi KAB. BOALEMO - GORONTALO', ''),
('Muh. Ainul S. Bahanan', '05202426069', 'ecdc94cd421b67fbd1ce470b5ed41b8d', 'azizahwafiq47@gmail.com', '082268361367', 'Aktif', 'ODQ4OTM=', 's1teknikomputer', '2026', NULL, 'BUOL', '2008-05-10', 'JL. SYARIEF MANSYUR  NO. 41  KAB. BUOL  PROV. SULAWESI TENGAH 94563, Kel/Desa Buol Kec. Biau KAB. BUOL - SULAWESI TENGAH', ''),
('Farel Putra Rumondor', '05202426070', '123b3df3b917b171add88b755a49975c', 'frelrmndr@gmail.com', '083190187791', 'Aktif', 'MTU5Njg=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2008-01-26', 'JL. KI. HAJAR DEWANTARA  KAB. BOALEMO  PROV. GORONTALO 96263, Kel/Desa Limbato Kec. Tilamuta KAB. BOALEMO - GORONTALO', ''),
('Iski D. Latif', '05202426071', 'aa0c12072f28fec968de77f357c34948', 'iskilatif6@gmail.com', '089502672132', 'Aktif', 'MTk1NjE=', 's1teknikomputer', '2026', NULL, 'LIMBOTO', '2007-09-28', 'PILOLAHEYA  KAB. BONE BOLANGO  PROV. GORONTALO 96543, Kel/Desa Mongiilo Kec. Bulango Ulu KAB. BONE BOLANGO - GORONTALO', ''),
('Nur Hidayatula Ngurawan', '05202426072', 'f74c078e0f38d4db28588479cccc4eb6', 'nurhidayatulangurawan@gmail.com', '085823813874', 'Aktif', 'Njc3OTc=', 's1teknikomputer', '2026', NULL, 'MOYAG', '2008-03-02', 'JL. KOLONEL SOEGIONO  KOTA KOTAMOBAGU  PROV. SULAWESI UTARA 95719, Kel/Desa Moyag Tampoan Kec. Kotamobagu Timur KOTA KOTAMOBAGU - SULAWESI UTARA', ''),
('Shafwan Harimurti Tonggi', '05202426073', '468718eb74fde0e15387ca11abe654d4', 'afayiou2@gmail.com', '082199725019', 'Aktif', 'NTg0NTY=', 's1teknikomputer', '2026', NULL, 'DIAT', '2008-08-17', 'JALAN TRANS SULAWESI UTARA, Kel/Desa Diat Kec. Lolak KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Altaf Y. Gifari Otto', '05202426074', '0126225da241464137eac4ca9f32badd', 'altafysuf18@gmail.com', '085757049099', 'Aktif', 'OTM5Nzg=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-07-18', 'JL.AIR PANAS. DUSUN 1  KAB. BOLAANG MONGONDOW SELATAN  PROV. SULAWESI UTARA 95770, Kel/Desa Popodu Kec. Bolaang Uki KAB. BOLAANG MONGONDOW SELATAN - SULAWESI UTARA', ''),
('Imanuel Anugrah Mahesa', '05202426075', 'a5d008a3e6501e0e06aa2a7f45b8f82a', 'emanuelmahesa08@gmail.com', '083865423619', 'Aktif', 'NjY5OTU=', 's1teknikomputer', '2026', NULL, 'PALOPO', '2008-06-06', 'DUSUN UTARA  KAB. GORONTALO UTARA  PROV. GORONTALO 96252, Kel/Desa Titidu Kec. Kwandang KAB. GORONTALO UTARA - GORONTALO', ''),
('Najmul Falah Akbar Putra Rivai', '05202426076', 'b2b3051be1c0731479c36d3289d1be9c', 'bintangrvii15@gmail.com', '082187381749', 'Aktif', 'NDM2NjE=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-05-15', 'TAHIR MANYO, Kel/Desa Tinelo Kec. Telaga Biru KAB. GORONTALO - GORONTALO', ''),
('Afgan Falih Kobandaha', '05202426077', 'ce44b3ca0af359c75cd8565ae4db0537', 'afganfalihkobandaha@gmail.com', '085398220145', 'Aktif', 'MTI5MzA=', 's1teknikomputer', '2026', NULL, 'KOTAMOBAGU', '2008-09-24', 'JLN. TRANS SULAWESI DESA SOLOG, Kel/Desa Solog Kec. Lolak KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Aura Grecia Paputungan', '05202426078', 'efd8a0e44ea463a0c83bd92243f08b0d', 'auragreciapaputungan@gmail.com', '085757755714', 'Aktif', 'ODk4MTI=', 's1teknikomputer', '2026', NULL, 'TANOYAN SELATAN', '2008-05-21', 'TANOYAN SELATAN  KAB. BOLAANG MONGONDOW  PROV. SULAWESI UTARA 95736, Kel/Desa Tanoyan Selatan Kec. Lolayan KAB. BOLAANG MONGONDOW - SULAWESI UTARA', ''),
('Ainiyah Kamilah Slamet', '05202426079', 'ca972e6b1c72b2696e24fc74e5d818b6', 'ainiyahslamet17@gmail.com', '082195616392', 'Aktif', 'Mjg2OTk=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-12-17', 'JL. PANCA KRIDA NO.43, Kel/Desa Padebuolo Kec. Kota Timur KOTA GORONTALO - GORONTALO', ''),
('Erfan Gibran Katili', '05202426080', '6216bf0a12896c87efeba99dd5be09a5', 'zuraopm9@gmail.com', '085398465758', 'Aktif', 'MzM3NzA=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2009-05-17', 'DUSUN IRIGASI SELATAN  KAB. GORONTALO  PROV. GORONTALO 96155, Kel/Desa Pilomonu Kec. Mootilango KAB. GORONTALO - GORONTALO', ''),
('Muhammad Fachry H. Syakur', '05202426081', 'd2ff172fa744541d731f55aa4f3d44ed', 'syakurdiyanty@gmail.com', '085757265422', 'Aktif', 'NTc5NDc=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2009-01-12', 'JL.SAWIT PERUM ALTIRA DUNGINGI KEL. TULADENGGI KOTA GORONTAL, Kel/Desa Tuladenggi Kec. Dungingi KOTA GORONTALO - GORONTALO', ''),
('Farelyansa Halid', '05202426082', '3ef9170519880533edbee85788e6d251', 'halidfarel282@gmail.com', '083171145589', 'Aktif', 'NjQwNTM=', 's1teknikomputer', '2026', NULL, 'KAB. GORONTALO', '2008-06-10', 'DESA TILOTE, Kel/Desa Tilote Kec. Tilango KAB. GORONTALO - GORONTALO', ''),
('Moh. Alparadikma Imanaksa', '05202426083', '826ac8048ecb3c4b54667520b7d54ebd', 'dikmapibete@gmail.com', '082192406368', 'Aktif', 'RGlrbWExMjc=', 's1teknikkomputer', '2026', '80f75173bc', 'LUWUK', '2008-01-19', 'Kel.Batui,desa batui,kec.batui, KAB. BANGGAI - SULAWESI TENGAH', 'file/05202426083-passfoto.jpg'),
('Jusmaida J. Ungke', '05202426084', 'cb9b491df7cd9ab3b2c32f20c5244d2b', 'jusmaidajungke@gmail.com', '081290788993', 'Aktif', 'MTQwOTA4', 's1pendidikanvokasionalrekayasaelektro', '2026', NULL, 'TOGONG SAGU', '2008-09-14', 'DUSUN 1, Kel/Desa Togong  Sagu Kec. Bangkurung KAB. BANGGAI LAUT - SULAWESI TENGAH', ''),
('Salim Putra Rezki Lihu', '05202426085', '2a082dd139936c1bb53b5be745fef660', 'salimputra911@gmail.com', '085136276656', 'Aktif', 'MjM1ODM=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-04-22', 'PERUMAHAN ALTIRA  KAB. GORONTALO  PROV. GORONTALO 96181, Kel/Desa Mongolato Kec. Telaga KAB. GORONTALO - GORONTALO', ''),
('Abd. Rahman Saputra Dama', '05202426086', '82257e953c60f170b93d2e4db37a0143', 'abdrahman.dama@gmail.com', '08981586538', 'Aktif', 'NDczMjk=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2008-12-12', 'JL. JERUK KEL. HUANGOBOTU KEC. DUNGINGI KOTA GORONTALO, Kel/Desa Huangobotu Kec. Dungingi KOTA GORONTALO - GORONTALO', ''),
('Muhammad Fajar Sidiq Makalalag', '05202426087', '1e1621a17c6ce78afc39b558cd5b6b01', 'fajarsidiqmakalalag45@gmail.com', '089502350211', 'Aktif', 'MTYwMzg=', 's1teknikomputer', '2026', NULL, 'BITUNG', '2006-12-20', 'WOLTER MONGINSIDI, Kel/Desa Wangurer Barat Kec. Madidir KOTA BITUNG - SULAWESI UTARA', ''),
('Muhammad Agung Rasjid', '05202426088', '2f3aff18e6f458f44e5e9ab67d1eefa5', 'agungrasjid256@gmail.com', '082228435530', 'Aktif', 'Nzc5NTY=', 's1teknikomputer', '2026', NULL, 'KAB.GORONTALO', '2009-01-30', 'JL.RAMBUTAN, Kel/Desa Buladu Kec. Kota Barat KOTA GORONTALO - GORONTALO', ''),
('Ilham Yusuf', '05202426089', '028329d1468e40b612656cd408f04bd7', 'yusupilham2007@gmail.com', '0895346678900', 'Aktif', 'MDg2MzE=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2007-07-09', 'JLN.RAJA EYATO ,KEL.MOLOSIPAT W, Kel/Desa Molosipat  W Kec. Kota Barat KOTA GORONTALO - GORONTALO', ''),
('Moh. Fahri Pratama Doka', '05202426090', '68fa2e2725e1f21bc99d9c1a05a6a387', 'linamomsfafanay29@gmail.com', '081245598248', 'Aktif', 'MzcwMDM=', 's1teknikomputer', '2026', NULL, 'KABUPATEN GORONTALO', '2008-05-01', 'JL. GARUDA LINGKUNGAN II, Kel/Desa Hutuo Kec. Limboto KAB. GORONTALO - GORONTALO', ''),
('Moh. Aminullah Risky Molose', '05202426092', '54476691a3c87fe11c6a0d9c723e2941', 'mohaminullahriskymolose@gmail.com', '085252320561', 'Aktif', 'MzMzNzQ=', 's1teknikomputer', '2026', NULL, 'GORONTALO', '2005-09-02', 'JLN. H. UMAR SIDIKI, Kel/Desa Wongkaditi Timur Kec. Kota Utara KOTA GORONTALO - GORONTALO', ''),
('Trias Nur Ardiansyah', '05203426001', '5c03b5dce38c6e67a55bdaf7a6139b14', 'triasardi72@gmail.com', '087872145270', 'Aktif', 'NDIxMDA=', 's1pendidikanvokasionalrekayasaelektro', '2026', NULL, 'KAB. GORONTALO', '2007-03-14', 'JALAN ADRIAN BERAHIM, DESA BONGOHULAWA, KEC.BONGOMEME, KAB., Kel/Desa Bongohulawa Kec. Bongomeme KAB. GORONTALO - GORONTALO', ''),
('Muhammad Fadriansyah Paputungan', '05203426002', '7adc39ab4fed6d76f7153a6c0509abae', 'fadrianpaputungan@gmail.com', '082191175763', 'Aktif', 'MTEwNTA=', 's1pendidikanvokasionalrekayasaelektro', '2026', NULL, 'BONGKUDAI', '2009-03-01', 'BONGKUDAI, Kel/Desa Bongkudai Kec. Modayag Barat KAB. BOLAANG MONGONDOW TIMUR - SULAWESI UTARA', ''),
('Dedi Ferinawan', '521409001', '260d8d76d2b98565b92e77020913afb5', '', '', 'Lulus', 'NTIxNDA5MDAx', 's1teknikelektro', '2009', NULL, '', '1989-11-08', '', ''),
('Doni Kadir', '521409002', 'c09a3490b9d5658e273bb7c674e6a8dd', '', '', 'Drop Out', 'NTIxNDA5MDAy', 's1teknikelektro', '2009', NULL, '', '1992-11-02', '', ''),
('Andris Katili', '521409003', 'dc84bbbaf7b88d7d6d9274c05c408c6b', '', '', 'Lulus', 'NTIxNDA5MDAz', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Rivaldi S Kilo', '521409004', 'cd81aa67b5e0203c7ee90ebb0f86d30b', '', '', 'Drop Out', 'NTIxNDA5MDA0', 's1teknikelektro', '2009', NULL, '', NULL, '', ''),
('Abdul Azis Eksan', '521409005', '1616484c16f037ba994ca5fa47bbb852', '', '', 'Drop Out', 'NTIxNDA5MDA1', 's1teknikelektro', '2009', NULL, '', NULL, '', ''),
('Oktoffianus H Umar', '521409006', '19d8d7c179c1ee8f32cef8c781936f86', '', '', 'Drop Out', 'NTIxNDA5MDA2', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Zamaludin B', '521409007', 'e73897d4d6858b47c1c1b1b9ec364b93', '', '', 'Drop Out', 'NTIxNDA5MDA3', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Nurmusadam Bando', '521409008', 'f5b6ea2a90793438e0b89a5b8af74caa', '', '', 'Lulus', 'NTIxNDA5MDA4', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Aswar Toliu', '521409009', '9b3fcb6a63aecd5c5bb0e2d72079a3b5', '', '', 'Lulus', 'NTIxNDA5MDA5', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Icuk Harson', '521409010', '8b656ce1333316b1ad83ae97bdb5d69c', '', '', 'Drop Out', 'NTIxNDA5MDEw', 's1teknikelektro', '2009', NULL, '', '2011-01-01', '', ''),
('Muhamad Firdaus Ramdhani', '521409011', '34d90a1d152efc91ab1931f6812790ff', '', '', 'Lulus', 'NTIxNDA5MDEx', 's1teknikelektro', '2009', NULL, '', '1990-06-04', '', ''),
('Adriansyah Hala', '521409012', '26d6df996b3d201ab2db4e099075fca3', '', '', 'Drop Out', 'NTIxNDA5MDEy', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Rustam Ahmad', '521409013', 'aeab6f4706adffa31826434c1c01efd1', '', '', 'Drop Out', 'NTIxNDA5MDEz', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Abdul Septian Kuengo', '521409014', 'd4c6f30b73d63fe0ee7a02343a283afd', '', '', 'Drop Out', 'NTIxNDA5MDE0', 's1teknikelektro', '2009', NULL, '', NULL, '', ''),
('Yosdi Sutrisno Alim', '521409015', '04be5110f674cd3ffad03fd7de68f7e6', '', '', 'Drop Out', 'NTIxNDA5MDE1', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Didin Alfadin Djafar', '521409016', 'c8c2fb9e33b8e88422a97d17d6d1f00b', '', '', 'Drop Out', 'NTIxNDA5MDE2', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Hamdan', '521409017', '37744322e10e1f1c8cf04cbd99780b6e', '', '', 'Lulus', 'NTIxNDA5MDE3', 's1teknikelektro', '2009', NULL, '', '1991-03-06', '', ''),
('Agus Riyanto Rahim', '521409018', 'ee832640c09cf1376a0df458d53f43c1', '', '', 'Lulus', 'NTIxNDA5MDE4', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Rifaldi Dude', '521409019', 'cf23d79e41a5383ae8504b7111b7d35d', '', '', 'Aktif', 'NTIxNDA5MDE5', 's1teknikelektro', '2009', NULL, '', '1991-11-11', '', ''),
('Nurwahyudin Kamba', '521409020', '43d4791802e2061a379849d80334a1ee', '', '', 'Drop Out', 'NTIxNDA5MDIw', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Ali Hunowu', '521409021', '60a56a522e6c2f1dac71d7431771eedd', '', '', 'Non-Aktif', 'NTIxNDA5MDIx', 's1teknikelektro', '2009', NULL, '', '1992-01-03', '', ''),
('Rafsanjani Nani', '521409022', 'da5ea1f5c1a574c8004ed0f45bb0935e', '', '', 'Lulus', 'NTIxNDA5MDIy', 's1teknikelektro', '2009', NULL, '', '1991-04-03', '', ''),
('Mohamad Firmansyah Panu', '521409023', '48987a4b65edac03a621282cbabc4a86', '', '', 'Lulus', 'NTIxNDA5MDIz', 's1teknikelektro', '2009', NULL, '', '1970-01-01', '', ''),
('Mohamad Jufri Djula', '521410001', '2c2e8ff693040ac84f864d79328386a0', '', '', 'Lulus', 'NTIxNDEwMDAx', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Didin Idrus', '521410002', '35292f3dacd18bd20d1e6ed6e0798ad4', '', '', 'Drop Out', 'NTIxNDEwMDAy', 's1teknikelektro', '2010', NULL, '', '1991-05-11', '', ''),
('I Ketut Suartono', '521410003', 'd73473dc4e13b54e6d79644d25a591ec', '', '', 'Lulus', 'NTIxNDEwMDAz', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Hamdan A Ismail', '521410004', 'a9e6a7a57f4e317d827b1f69621b3f5e', '', '', 'Drop Out', 'NTIxNDEwMDA0', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Akrim Fajar', '521410005', '1a623c13c72b53b3ffdabba494580992', '', '', 'Drop Out', 'NTIxNDEwMDA1', 's1teknikelektro', '2010', NULL, '', '1992-08-05', '', ''),
('Sri Oin Hasan', '521410006', '17a22beb8ea223ee7b45a3716038f306', '', '', 'Drop Out', 'NTIxNDEwMDA2', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Rivai Harun', '521410007', '6bde21db7122ba5d496b6f8fccdc5fff', '', '', 'Aktif', 'NTIxNDEwMDA3', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Agus Putra Cahyadi Waluyo', '521410008', 'f48120c836ad8fb276533781298f9b06', '', '', 'Lulus', 'NTIxNDEwMDA4', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Alfred Lasarudin', '521410009', '74d81569b832f18e7f7b1f07eee8666f', '', '', 'Drop Out', 'NTIxNDEwMDA5', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Abdul Rohman', '521410010', '69fc76ac1826394e6786f08dece2abec', '', '', 'Lulus', 'NTIxNDEwMDEw', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Yunita Pratiwi Doe', '521410011', 'c45dee84ceb5c8b89d28212593c860d3', '', '', 'Lulus', 'NTIxNDEwMDEx', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Fahmid Sasdian Saleh', '521410012', 'd212180ec7911394dc9a222adb5090d2', '', '', 'Drop Out', 'NTIxNDEwMDEy', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Eko Ruli Naptali', '521410013', 'ca86ddc99f96b012dbea8657728ee34f', '', '', 'Lulus', 'NTIxNDEwMDEz', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Rizal A Padu', '521410014', '4fd406579fc69923b8c7050e89caafd3', '', '', 'Drop Out', 'NTIxNDEwMDE0', 's1teknikelektro', '2010', NULL, '', '1991-05-07', '', ''),
('Yomin Usman', '521410015', '524013ff78c9e5a056351106a324dde0', '', '', 'Drop Out', 'NTIxNDEwMDE1', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Saleh Hasan Sabaya', '521410016', '8f940bab31465bda819a63af81b09584', '', '', 'Drop Out', 'NTIxNDEwMDE2', 's1teknikelektro', '2010', NULL, '', '1992-09-12', '', ''),
('Agus Lihawa', '521410017', '438f54555146f8f610d0675f19bd15a4', '', '', 'Drop Out', 'NTIxNDEwMDE3', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Taufik Pratama Putra', '521410018', 'cd362578a24c2c3525323b4e6bfd2f38', '', '', 'Lulus', 'NTIxNDEwMDE4', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Arip Muliyadi Gonibala', '521410019', '8daef1d6d46ef2916d2c0b4b89aaf922', '', '', 'Lulus', 'NTIxNDEwMDE5', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Iman Lauli', '521410020', '513617967f67c51660dbea7c75fe7e0a', '', '', 'Aktif', 'NTIxNDEwMDIw', 's1teknikelektro', '2010', NULL, '', '1989-12-09', '', ''),
('Yusri Djibu', '521410021', 'f7eca4767e5adb4165c0c0b39dbf4384', '', '', 'Drop Out', 'NTIxNDEwMDIx', 's1teknikelektro', '2010', NULL, '', '1991-05-12', '', ''),
('Yowan Karim', '521410022', '4f2f9346975a8f2e0f998c70b15b064e', '', '', 'Drop Out', 'NTIxNDEwMDIy', 's1teknikelektro', '2010', NULL, '', '1990-09-04', '', ''),
('Jemy Y. Saleh', '521410023', '48704647cd37f0ec73b020c60e2f8eff', '', '', 'Drop Out', 'NTIxNDEwMDIz', 's1teknikelektro', '2010', NULL, '', '1992-05-02', '', ''),
('Eko Pudjianto', '521410024', '121f84c439be253e9b187268b4d3fac2', '', '', 'Lulus', 'NTIxNDEwMDI0', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Muhammad F.r Arsyad', '521410025', 'aec927005b12ad80de6d5b4e1cf08cd6', '', '', 'Drop Out', 'NTIxNDEwMDI1', 's1teknikelektro', '2010', NULL, '', '1992-01-07', '', ''),
('Gunawan Olii', '521410026', '78db75dd0ef0fcfc1ea513a1a9f5ef27', '', '', 'Drop Out', 'NTIxNDEwMDI2', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Risky Sasmita Linggulu', '521410027', '7e867d2cff8adad979e017a51314cf20', '', '', 'Drop Out', 'NTIxNDEwMDI3', 's1teknikelektro', '2010', NULL, '', '1992-03-03', '', ''),
('Ramli Taudi', '521410028', 'cea6e0049f3c2246830cb97fe3b63713', '', '', 'Drop Out', 'NTIxNDEwMDI4', 's1teknikelektro', '2010', NULL, '', '1991-01-04', '', ''),
('Fernandus Hasan', '521410029', '7e415549ef7c6823590b97069ba066f1', '', '', 'Drop Out', 'NTIxNDEwMDI5', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Aswar Tahir', '521410030', '42f4808d081da14afdb71a4f6fa4666d', '', '', 'Drop Out', 'NTIxNDEwMDMw', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Apriyanto Poluli', '521410031', '99e28ddc5dba005863036f7c29ab07b2', '', '', 'Aktif', 'NTIxNDEwMDMx', 's1teknikelektro', '2010', NULL, '', '1993-05-04', '', ''),
('Febryansah Kolondam', '521410032', '633f3a485b935d2154a8957b38adf7e6', '', '', 'Drop Out', 'NTIxNDEwMDMy', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Mohamad Dahlan Kokalo', '521410033', '0bf9721e7b42cc9508f213ae30c82b42', '', '', 'Lulus', 'NTIxNDEwMDMz', 's1teknikelektro', '2010', NULL, '', '1993-10-04', '', ''),
('Djamal Mubarak', '521410034', 'caf3d57a7f6f80f40cc2af2fd5420116', '', '', 'Drop Out', 'NTIxNDEwMDM0', 's1teknikelektro', '2010', NULL, '', '1992-11-06', '', ''),
('Herianto Kaharu', '521410035', '4176093c7567d5600deddb4803ed234c', '', '', 'Lulus', 'NTIxNDEwMDM1', 's1teknikelektro', '2010', NULL, '', '1992-01-07', '', ''),
('Andry Purnomo M. K. Ahmad', '521410036', '8eb54f1e0b9160018d8ca51e630caea6', '', '', 'Lulus', 'NTIxNDEwMDM2', 's1teknikelektro', '2010', NULL, '', '1992-01-01', '', ''),
('La Ode Muhamad Taslan', '521410037', '0683c2e15b5cec672bb4796cf129a7aa', '', '', 'Lulus', 'NTIxNDEwMDM3', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Wahid', '521410038', '86919025d3fdb732fb037d22e9f0bd24', '', '', 'Lulus', 'NTIxNDEwMDM4', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Ahmad Yani Djahuno', '521410039', '3d003c03553205f807ad329465a9b882', '', '', 'Drop Out', 'NTIxNDEwMDM5', 's1teknikelektro', '2010', NULL, '', '1991-05-10', '', ''),
('Muhammad Nur Irfan', '521410040', 'a9fa9f78b178d7f4e92a2f1c523d1d5e', '', '', 'Lulus', 'NTIxNDEwMDQw', 's1teknikelektro', '2010', NULL, '', '1990-10-12', '', ''),
('Ardian Paputungan', '521410041', '99b1d0b58597d8ab7b1c7930bf5943d1', '', '', 'Lulus', 'NTIxNDEwMDQx', 's1teknikelektro', '2010', NULL, '', '1992-07-08', '', ''),
('Yuslizar Egar Pratama Bachari', '521410042', '0c0cbbc34fa431a3b1fd389079d66e25', '', '', 'Lulus', 'NTIxNDEwMDQy', 's1teknikelektro', '2010', NULL, '', '1993-09-02', '', ''),
('David D Hasan', '521410043', 'ada095035cedd6464b6a441a80b8061b', '', '', 'Aktif', 'NTIxNDEwMDQz', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Djulhamd Lasande', '521410044', '948c7387ba0b1d20a2b1f5357085c341', '', '', 'Drop Out', 'NTIxNDEwMDQ0', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Nur\'iman M.hulawa', '521410045', '818d42d30828cc131bb7cd79d6a79efa', '', '', 'Drop Out', 'NTIxNDEwMDQ1', 's1teknikelektro', '2010', NULL, '', '1992-02-02', '', ''),
('Saleh Tahir', '521410046', 'b4022478619eaaeb3cba75552f486c3e', '', '', 'Drop Out', 'NTIxNDEwMDQ2', 's1teknikelektro', '2010', NULL, '', '1989-12-10', '', ''),
('Arlan Aripu', '521410047', '13556f2df6d734b2cade2af6d5fa7e4b', '', '', 'Non-Aktif', 'NTIxNDEwMDQ3', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Zainudin Bulongkod', '521410048', '7dde2eff9198d25dad9858d967ef2f1f', '', '', 'Lulus', 'NTIxNDEwMDQ4', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Zulfikar Aidin Uli', '521410049', '779a80cd9f8c860f8664d4fb812346c5', '', '', 'Lulus', 'NTIxNDEwMDQ5', 's1teknikelektro', '2010', NULL, '', '1992-10-04', '', ''),
('Rajib H. Potale', '521410050', 'bbedaf4967dc0ab6c3c290237a85db90', '', '', 'Drop Out', 'NTIxNDEwMDUw', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Sarifudin K Baderan', '521410051', 'ec9799d64dec0b347416bf2b639348bd', '', '', 'Drop Out', 'NTIxNDEwMDUx', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Ihsan Arsyad', '521410052', '80cf837bf8be1217c537a8ef995800d1', '', '', 'Lulus', 'NTIxNDEwMDUy', 's1teknikelektro', '2010', NULL, '', '1991-08-05', '', ''),
('Idris Wange', '521410053', '4564aaa7087ef362c080258e316fafa8', '', '', 'Drop Out', 'NTIxNDEwMDUz', 's1teknikelektro', '2010', NULL, '', '1992-12-08', '', ''),
('Mohamad I Mustapa', '521410054', '2ef6e7a3043838e75c9374f7283e48e8', '', '', 'Drop Out', 'NTIxNDEwMDU0', 's1teknikelektro', '2010', NULL, '', '1992-09-03', '', ''),
('Taufik Mbata', '521410055', '2e15bab1ea26ba7724f870bf37b23328', '', '', 'Drop Out', 'NTIxNDEwMDU1', 's1teknikelektro', '2010', NULL, '', '1970-01-01', '', ''),
('Rezandy Laiya', '521411001', '515b5123db83034fed8622a5b46778a9', '', '', 'Drop Out', 'NTIxNDExMDAx', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Arbi Wiliyanto', '521411002', '6728fc3a8facffb696b743ae5e15ecc1', '', '', 'Drop Out', 'NTIxNDExMDAy', 's1teknikelektro', '2011', NULL, '', '1991-05-10', '', ''),
('Mohamad Uloli', '521411003', '573ac5f3b12b1d738684ace16ed695cf', '', '', 'Drop Out', 'NTIxNDExMDAz', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Fajar Rizky Tayabu', '521411004', '24e56615ed975abe6c23dc9c49be1b14', '', '', 'Drop Out', 'NTIxNDExMDA0', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Iksan L Pakaya', '521411005', 'fc4df22fe530fa0ca8fc957b66b4214c', '', '', 'Drop Out', 'NTIxNDExMDA1', 's1teknikelektro', '2011', NULL, '', '1992-03-11', '', ''),
('Ahmad Muharor', '521411006', 'fd67edbd49a39c2b1b957fd25066946f', '', '', 'Lulus', 'NTIxNDExMDA2', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Andriyanto Djafar', '521411007', '44983acc79c90785e60a4ee9ef77d068', '', '', 'Drop Out', 'NTIxNDExMDA3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Indrayadi Puluhulawa', '521411008', '6ebc677c270d4fbe60d43d6b9f183897', '', '', 'Drop Out', 'NTIxNDExMDA4', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Dody Pratama Ali', '521411009', 'fda3f268860974175405f86d64fc9ce5', '', '', 'Drop Out', 'NTIxNDExMDA5', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Frizky F. Inaku', '521411010', 'c5cac87cffa96c1493953e139e617243', '', '', 'Drop Out', 'NTIxNDExMDEw', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Rikman Hangio', '521411011', 'bf45bffe7dfd6cbc4d63701f66c4ef35', '', '', 'Lulus', 'NTIxNDExMDEx', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Bastian Ole', '521411012', '8ed974e59e0aa81296babd4ac6189216', '', '', 'Lulus', 'NTIxNDExMDEy', 's1teknikelektro', '2011', NULL, '', '1992-11-10', '', ''),
('Ismail Ahmad', '521411013', 'ac9e15cfaeafbdb99b95ed789f015177', '', '', 'Lulus', 'NTIxNDExMDEz', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('I Made Alit Putra', '521411014', 'a0f011766ac1bd0b14d5b840ffdf8383', '', '', 'Lulus', 'NTIxNDExMDE0', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Inten Walangadi', '521411015', 'c39431bb488b47040a068ed84185ba06', '', '', 'Lulus', 'NTIxNDExMDE1', 's1teknikelektro', '2011', NULL, '', '1994-07-03', '', ''),
('Woro Ganjar Arum', '521411016', 'c3ce8d6c52e9afad60090b48b685e5cf', '', '', 'Lulus', 'NTIxNDExMDE2', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Sofyan', '521411017', '39f1cfa63e48f7e07e08bd52aafdfd54', '', '', 'Drop Out', 'NTIxNDExMDE3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Fendy Endrilia Bumbay', '521411018', '64d6e5786c6e5ebe1ad68cac48791115', '', '', 'Lulus', 'NTIxNDExMDE4', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Landi Kuntoro', '521411019', '576d865c8d39440a8553159558312a3c', '', '', 'Drop Out', 'NTIxNDExMDE5', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Abdurahman Hunow', '521411020', '37d76c2fb92acf29481956eac959743d', '', '', 'Drop Out', 'NTIxNDExMDIw', 's1teknikelektro', '2011', NULL, '', '1993-03-02', '', ''),
('Fier Pratama S.', '521411021', '5fb6f6456090ab948aa94f027465952a', '', '', 'Drop Out', 'NTIxNDExMDIx', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Agung Anwar', '521411022', 'd5979f701365f2795f372aa9ae786e45', '', '', 'Lulus', 'NTIxNDExMDIy', 's1teknikelektro', '2011', NULL, '', '1993-10-08', '', ''),
('Maqfira Y. Efendy', '521411023', '9a2e0dabd5105ec3434d86e7039fe2ff', '', '', 'Drop Out', 'NTIxNDExMDIz', 's1teknikelektro', '2011', NULL, '', '1994-05-01', '', ''),
('I Nyoman Ariawan', '521411024', 'f48c384ae52ecdbbd13073946095d5b5', '', '', 'Lulus', 'NTIxNDExMDI0', 's1teknikelektro', '2011', NULL, '', '1992-05-03', '', ''),
('Mamat Abdullah', '521411025', '44434127ba50813ea09bfdec30fa4c61', '', '', 'Keluar', 'NTIxNDExMDI1', 's1teknikelektro', '2011', NULL, '', '1993-01-09', '', ''),
('Herman Kue', '521411026', 'f37b21cca4c1b9d3e81ff79416f62d84', '', '', 'Keluar', 'NTIxNDExMDI2', 's1teknikelektro', '2011', NULL, '', '1993-04-07', '', ''),
('Zainudin Suleman', '521411027', '2f4c3fc73fd1ef9be46392d97ed95a7a', '', '', 'Lulus', 'NTIxNDExMDI3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Fadliyanto Isa', '521411028', '99265f0dfb35a3c4319f9f22ce787d20', '', '', 'Lulus', 'NTIxNDExMDI4', 's1teknikelektro', '2011', NULL, '', '1992-12-01', '', '');
INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('Yadi', '521411029', '4d892677fbe3724953db18e1c1f5f397', '', '', 'Lulus', 'NTIxNDExMDI5', 's1teknikelektro', '2011', NULL, '', '1991-05-10', '', ''),
('I Wayan Teriyanto', '521411030', '8410b689fa903f76cca45baf6ffaea98', '', '', 'Drop Out', 'NTIxNDExMDMw', 's1teknikelektro', '2011', NULL, '', '1992-05-02', '', ''),
('Reinerg. Djafar', '521411031', '9850552f6a05193c24d824bede3c9f4a', '', '', 'Drop Out', 'NTIxNDExMDMx', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Yogi Fachry Ismail', '521411032', '20d69c165d132fd1707b83b70afe8cb4', '', '', 'Drop Out', 'NTIxNDExMDMy', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Wisnu Wijanarko', '521411033', '81c61d167bf8878c3ae7a8443ad259b3', '', '', 'Lulus', 'NTIxNDExMDMz', 's1teknikelektro', '2011', NULL, '', '1992-07-09', '', ''),
('Mohamad Taufiq A. Nusi', '521411034', 'f19a5af25d1c98cad8cbb07182510535', '', '', 'Lulus', 'NTIxNDExMDM0', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Amran Syahni Putra Rahda', '521411035', '8a71109eb6ad118d1436c8b542df98cf', '', '', 'Lulus', 'NTIxNDExMDM1', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Fikriyanto Nurain', '521411036', '0ec755fc593b78ac7399a53fb309aedc', '', '', 'Drop Out', 'NTIxNDExMDM2', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Agus Wahyudi', '521411037', '9bb8cc41e3a0945f7d80ad1488856720', '', '', 'Drop Out', 'NTIxNDExMDM3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Rery Cahyadi Alamri', '521411038', 'b7ffb3e71122841732f9be07eaeffe04', '', '', 'Drop Out', 'NTIxNDExMDM4', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Rendra Abas', '521411039', '2781ea31f8b24eb4318abadff46cf365', '', '', 'Lulus', 'NTIxNDExMDM5', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Siswanto', '521411040', '92297c26683f31fcb347213d6dd2b1e1', '', '', 'Keluar', 'NTIxNDExMDQw', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Suparman Blongkod', '521411041', 'b11686ea45d24b0b2d7316bd07e14a7f', '', '', 'Drop Out', 'NTIxNDExMDQx', 's1teknikelektro', '2011', NULL, '', '1993-01-07', '', ''),
('Saputra Ramdan Amuda', '521411042', '459f8150beff0d105a773017369017d9', '', '', 'Drop Out', 'NTIxNDExMDQy', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Abd. Hanan Dj. H.', '521411043', 'c21f9a057a28ad64189f4e681065b58e', '', '', 'Drop Out', 'NTIxNDExMDQz', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Dul Basar', '521411044', 'a2ef000dccf49f7bac2657edbedadfc9', '', '', 'Drop Out', 'NTIxNDExMDQ0', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Rahmawaty Nur Indah Daud', '521411045', '6de937c526fb2637558a3f3bb908e179', '', '', 'Lulus', 'NTIxNDExMDQ1', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Jumardin', '521411046', 'db3a014cb67c053ecbd49701e40c4b1b', '', '', 'Lulus', 'NTIxNDExMDQ2', 's1teknikelektro', '2011', NULL, '', '1993-06-11', '', ''),
('Jarwan Tangahu', '521411047', '293b51b7906cdf41959f30f3d9425db0', '', '', 'Drop Out', 'NTIxNDExMDQ3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Risman Duko', '521411048', 'c0aaf00aa93049a62d49d9d7176b9aed', '', '', 'Drop Out', 'NTIxNDExMDQ4', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Moh. Richald Hamzah', '521411049', 'dceb473d5b46ff49f08b6931f3575128', '', '', 'Keluar', 'NTIxNDExMDQ5', 's1teknikelektro', '2011', NULL, '', '1993-11-12', '', ''),
('Rusdin Bajumani', '521411050', 'd631fc7caa847f519e1a154971cb924c', '', '', 'Lulus', 'NTIxNDExMDUw', 's1teknikelektro', '2011', NULL, '', '1993-01-11', '', ''),
('Moh. Iqbal Pratama', '521411051', 'b385f8c16890327b501dc4467da5cb71', '', '', 'Drop Out', 'NTIxNDExMDUx', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Bayu', '521411052', '898be28b7b88156855a28934fe08b0ca', '', '', 'Lulus', 'NTIxNDExMDUy', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Hasan P. Usu', '521411053', '305c738f92421429fe9f4173bf99f124', '', '', 'Lulus', 'NTIxNDExMDUz', 's1teknikelektro', '2011', NULL, '', '1991-01-01', '', ''),
('Frengki Max Kadir', '521411054', '5cee6942d1b92c2a0b17533a08cf8dee', '', '', 'Drop Out', 'NTIxNDExMDU0', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Renji  Kurniadi Bambela', '521411055', '8c9fc0866200ba2228feb4d465fbdf73', '', '', 'Lulus', 'NTIxNDExMDU1', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Rizky Aldi Husain', '521411056', '09a6163bc0c0773d08f1ff32f008a759', '', '', 'Lulus', 'NTIxNDExMDU2', 's1teknikelektro', '2011', NULL, '', '1993-04-07', '', ''),
('Ferdi Balaati', '521411057', 'd2e1133ab076f7ebf272f812195dc3be', '', '', 'Lulus', 'NTIxNDExMDU3', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Candra Ismail', '521411058', '6d5f3610127c50103e48413219a602c0', '', '', 'Lulus', 'NTIxNDExMDU4', 's1teknikelektro', '2011', NULL, '', '1993-07-04', '', ''),
('Pirji Modeong', '521411059', 'e6c0359d80c9c5ac3a6717e6c9c8321a', '', '', 'Lulus', 'NTIxNDExMDU5', 's1teknikelektro', '2011', NULL, '', '1992-05-07', '', ''),
('Hendrawan Banteng', '521411060', 'a2c478cf2f2c957cb5090882d8816d6d', '', '', 'Drop Out', 'NTIxNDExMDYw', 's1teknikelektro', '2011', NULL, '', '1970-01-01', '', ''),
('Sasmifta Botutihe', '521411061', '943cefecd1b28cd4dad480880e83d305', '', '', 'Lulus', 'NTIxNDExMDYx', 's1teknikelektro', '2011', NULL, '', '1994-01-04', '', ''),
('Zainal Gani', '521411062', 'cefd7471c4a855f6c5c63172d68ee58a', '', '', 'Drop Out', 'NTIxNDExMDYy', 's1teknikelektro', '2011', NULL, '', '1993-12-03', '', ''),
('Zulfianto Danial', '521412001', '51bb9dbe705cedacd319bdea0f08debe', '', '', 'Lulus', 'NTIxNDEyMDAx', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Vicri Basara', '521412002', '04352054cb035ee6964075886b1f5ef7', '', '', 'Drop Out', 'NTIxNDEyMDAy', 's1teknikelektro', '2012', NULL, '', '1994-09-09', '', ''),
('Ilyas Suleman', '521412003', '8dc28f53318f610e62dc24083b6c6fd3', '', '', 'Drop Out', 'NTIxNDEyMDAz', 's1teknikelektro', '2012', NULL, '', '1994-03-06', '', ''),
('Abdul Samad N. Djamaini', '521412004', 'cb8604d06414ae6665f82698713c7e1c', '', '', 'Lulus', 'a290ZXJ0', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Andry Eka Pranata Ismail', '521412005', 'b192f141700fd84925c561b309080388', '', '', 'Lulus', 'NTIxNDEyMDA1', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Andi Baharsyah', '521412006', '52ff4d0dd1ed4c2941f90dd701253c81', '', '', 'Lulus', 'NTIxNDEyMDA2', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ramdan Umar', '521412007', '5d4392c4b6ab091a9de40dabf5139d4a', '', '', 'Drop Out', 'NTIxNDEyMDA3', 's1teknikelektro', '2012', NULL, '', '1994-11-02', '', ''),
('Haerudin A. Koloid', '521412008', 'f341db290b736a5739d2ff36e58ddb19', '', '', 'Lulus', 'NTIxNDEyMDA4', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ismail U. Mener', '521412009', '0f605a37951355add65e4c9fe66e8b50', '', '', 'Drop Out', 'NTIxNDEyMDA5', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Pratama Aditya Monoarfa', '521412010', '76d0a7a53c0fbc404b9c1f3f4d8bd212', '', '', 'Drop Out', 'NTIxNDEyMDEw', 's1teknikelektro', '2012', NULL, '', '1994-09-04', '', ''),
('Mohammad Rizal Bagtayan', '521412011', '3693d4cdab48ae50b9767a29203b35e6', '', '', 'Lulus', 'NTIxNDEyMDEx', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ridwan Kadir', '521412012', 'ae68c8da3c6b04cd0c3ec924dccccbfc', '', '089669106718', 'Lulus', 'ZW90bHRu', 's1teknikelektro', '2012', NULL, '', '1993-10-04', '', ''),
('Wiranto Anwar', '521412013', '43b05447673b00a44e9a279a33a1a0d6', '', '', 'Lulus', 'NTIxNDEyMDEz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Nasrudin', '521412014', '263e5b479c53ad4d1bf18e61a23b676b', '', '', 'Drop Out', 'NTIxNDEyMDE0', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Nizan Abaidata', '521412015', '42345a2b13e0d08619867c4841bcbf3a', '', '', 'Keluar', 'NTIxNDEyMDE1', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Abdullah Danial', '521412016', '6aa3f2a75a526a6b62859a632c86bbce', '', '', 'Lulus', 'NTIxNDEyMDE2', 's1teknikelektro', '2012', NULL, '', '1992-11-09', '', ''),
('Ribhanrio Humonggio', '521412017', '25c4fe6a78f54fedba54b6a5cd1a33af', '', '', 'Drop Out', 'NTIxNDEyMDE3', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ilham Amu', '521412018', 'd0ae55afd3e08d850bf35392c7c66faf', '', '', 'Lulus', 'NTIxNDEyMDE4', 's1teknikelektro', '2012', NULL, '', '1995-04-01', '', ''),
('Rafli Is Mangge', '521412019', 'd823967bcdda23bef81e615bbcd2d820', '', '', 'Drop Out', 'NTIxNDEyMDE5', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Masrul Tongkingoto', '521412020', '65cd6b4072503aa63463cce1b3e07289', '', '', 'Drop Out', 'NTIxNDEyMDIw', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Zainudin Noho', '521412021', 'fd0c35aef0306112509f4a374115d246', '', '', 'Drop Out', 'NTIxNDEyMDIx', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('M Noor Ikhsan Yahya', '521412022', 'ad6092ed1c7a753e0e94090f616d52d4', '', '', 'Drop Out', 'NTIxNDEyMDIy', 's1teknikelektro', '2012', NULL, '', '1994-07-07', '', ''),
('Reno Waty', '521412023', '7a55a0b5bbd999a54e2fe8a949556363', '', '', 'Lulus', 'NTIxNDEyMDIz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Muhlis Abudi', '521412024', 'bb99fc64a796b3f9946784f4116f362d', '', '', 'Drop Out', 'NTIxNDEyMDI0', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Aripin Datau', '521412025', 'da55609e08ed4f73fdda3e4a654d54dc', '', '', 'Lulus', 'NTIxNDEyMDI1', 's1teknikelektro', '2012', NULL, '', '1994-09-01', '', ''),
('Milson B. Asi', '521412026', 'b01c8bc586516e289252dbfbfa819b6e', '', '', 'Drop Out', 'NTIxNDEyMDI2', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ismail Suleman', '521412027', '9548d640b5e2503f0e70eae21d980112', '', '', 'Drop Out', 'NTIxNDEyMDI3', 's1teknikelektro', '2012', NULL, '', '1993-06-06', '', ''),
('Rachmat Sorongan', '521412028', '6edbe31bbcca9ebe95b332682ef9ea1e', '', '', 'Drop Out', 'NTIxNDEyMDI4', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Mohamad Ramadhan Botutihe', '521412029', 'e01e5b7856d11f8f12c10af0a8f7bb72', '', '', 'Drop Out', 'NTIxNDEyMDI5', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ahmad Kamal', '521412030', 'b8e73253fda61c5b978ff160b75329e1', '', '', 'Drop Out', 'NTIxNDEyMDMw', 's1teknikelektro', '2012', NULL, '', '1993-11-04', '', ''),
('Suharji Mahmud', '521412031', '6f3b953cc65b525928a1e780a886eac6', '', '', 'Drop Out', 'NTIxNDEyMDMx', 's1teknikelektro', '2012', NULL, '', '1993-01-06', '', ''),
('Risdan Hariyono', '521412032', 'fdbce3748b098dcce61d0a83a529712e', '', '', 'Drop Out', 'NTIxNDEyMDMy', 's1teknikelektro', '2012', NULL, '', '1994-10-02', '', ''),
('Arsyila Darma', '521412033', 'ea3f1d628e62e16089d818e29f3ca9fb', '', '', 'Lulus', 'NTIxNDEyMDMz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Mohamad Dandi Usman', '521412034', '2442eecca4e61231d7c8b612578b6e9e', '', '', 'Drop Out', 'NTIxNDEyMDM0', 's1teknikelektro', '2012', NULL, '', '1994-03-03', '', ''),
('Furqan Umar', '521412035', '7698233cbc440e70fc460871b431c2db', '', '', 'Keluar', 'NTIxNDEyMDM1', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Abdul Parlan Arune', '521412036', 'baed041aeb943af678e97ae323bc462b', '', '', 'Lulus', 'NTIxNDEyMDM2', 's1teknikelektro', '2012', NULL, '', '1994-10-02', '', ''),
('Moh. Agustriansyah Bahu', '521412037', 'cd3aa21429a23812454a6135e528b86b', '', '', 'Drop Out', 'NTIxNDEyMDM3', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Gunawan', '521412038', 'ebde1a44a1432c2f57fbfe632a93cf4b', '', '', 'Drop Out', 'NTIxNDEyMDM4', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Muhamad Junaedi', '521412039', 'c13344ba68da4787e1086e5330086710', '', '', 'Drop Out', 'NTIxNDEyMDM5', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Eka Mertayana', '521412040', '70bcc392b9f48f7f44d4de26bfcc8b6e', '', '', 'Lulus', 'NTIxNDEyMDQw', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Fitriadi Usman', '521412041', '89ec947d6dc43d04d36475476f257eaf', '', '', 'Drop Out', 'NTIxNDEyMDQx', 's1teknikelektro', '2012', NULL, '', '1995-01-03', '', ''),
('Rezki Dici Putra Bonenehu', '521412042', '85f0e931719c774e2b139c659292b09a', '', '', 'Drop Out', 'NTIxNDEyMDQy', 's1teknikelektro', '2012', NULL, '', '1995-07-03', '', ''),
('Artha Pirson Supit', '521412043', 'f4b4b8390eac01320ccef881fd788e5a', '', '', 'Keluar', 'NTIxNDEyMDQz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Matris Nonowa', '521412044', '7b7b3fd1df05275bf8953e0d5dc6196e', '', '', 'Lulus', 'NTIxNDEyMDQ0', 's1teknikelektro', '2012', NULL, '', '1993-08-06', '', ''),
('Muchlis Budiono', '521412045', '6f31d03528174803a9e3073fa2b4e41e', '', '', 'Lulus', 'NTIxNDEyMDQ1', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ismail Tangkudung', '521412046', '2398ab04162c1d3728b3db83d8f184e6', '', '', 'Lulus', 'NTIxNDEyMDQ2', 's1teknikelektro', '2012', NULL, '', '1993-10-11', '', ''),
('Inang Abdullah', '521412047', 'bcbf3690cfe3b8de9886a162980823ce', '', '', 'Lulus', 'NTIxNDEyMDQ3', 's1teknikelektro', '2012', NULL, '', '1992-12-07', '', ''),
('Heriyanto Mohamad', '521412048', 'c5e12be2a74cae462c5bbd398294205c', '', '', 'Drop Out', 'NTIxNDEyMDQ4', 's1teknikelektro', '2012', NULL, '', '1991-09-10', '', ''),
('Rizaldi Adam', '521412049', '05d3d09ceb0688a733320c6237796281', '', '', 'Drop Out', 'NTIxNDEyMDQ5', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ilham Abubakar D.nusi', '521412050', 'dba770066233f45af5a2097c7a796383', '', '', 'Lulus', 'NTIxNDEyMDUw', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Deliyanti Dalila', '521412051', '937cf8933450c9a3c2f5fe1929225ecc', '', '', 'Lulus', 'NTIxNDEyMDUx', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Akbar Riyanto Amune', '521412052', 'd8233aaf0b845e9240c2e50f2b21a854', '', '', 'Lulus', 'NTIxNDEyMDUy', 's1teknikelektro', '2012', NULL, '', '1994-03-02', '', ''),
('Sofyan Gani', '521412053', 'dfa79e6fa3338e936f0dfcbbe9f689d0', '', '', 'Lulus', 'NTIxNDEyMDUz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Aldio Zulfikar Rahman', '521412054', '3041c1d93716335abe584c2c9eb595cd', '', '', 'Drop Out', 'NTIxNDEyMDU0', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Barzan Mohamad', '521412055', '37db97c87bcb2d88519215d90f0248a1', '', '', 'Drop Out', 'NTIxNDEyMDU1', 's1teknikelektro', '2012', NULL, '', '1993-01-05', '', ''),
('Ruth Deborah Indriyani', '521412056', 'ec689bd04af2f22422fb53f92f7ce96b', '', '', 'Lulus', 'NTIxNDEyMDU2', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Muhamad Rafly', '521412057', '0fa2d4d61439c5d202b7a4979fe7ab79', '', '', 'Lulus', 'NTIxNDEyMDU3', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Mohamad B. Dumondor', '521412058', 'd708679410299b5f59088377ad5b43fb', '', '', 'Drop Out', 'NTIxNDEyMDU4', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Yusuf H. Kanoi', '521412059', '89451bf57346b413ffd288ec1a3066ed', '', '', 'Lulus', 'NTIxNDEyMDU5', 's1teknikelektro', '2012', NULL, '', '1994-10-05', '', ''),
('Cindra Mahadju', '521412060', '7f9d306314d036e1f1c6566d1b20ed0d', '', '', 'Lulus', 'NTIxNDEyMDYw', 's1teknikelektro', '2012', NULL, '', '1994-06-10', '', ''),
('Ekoriskiyanto', '521412061', 'b6952606dad9c8b5d5b9431d3a531970', '', '', 'Lulus', 'NTIxNDEyMDYx', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Tir Eka Saputra R Bano', '521412062', '3e7ce8464d56a69cd9341d5a9e29b9c5', '', '', 'Keluar', 'NTIxNDEyMDYy', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Andhika Pratama Putra Panu', '521412063', '196dac2ad2594167c6fbf840ee4b7655', '', '', 'Lulus', 'NTIxNDEyMDYz', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Moh. Fahri Hantue', '521412064', '86b170e2b7ad29e7592409f9a3cd8159', '', '', 'Drop Out', 'NTIxNDEyMDY0', 's1teknikelektro', '2012', NULL, '', '1984-12-02', '', ''),
('Idris Hasan', '521412065', 'e4d64d36d45115b8bebb0ef597eecbe4', '', '', 'Lulus', 'NTIxNDEyMDY1', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Darmawan Kasim', '521412066', '9d10a432b289a8829538c892588ce510', '', '', 'Aktif', 'ZWllZW9s', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Ibrahim Rahman', '521412067', 'acad9b2c50e6d44bcb68f6d5d3257bed', '', '', 'Aktif', 'a2tpdGVr', 's1teknikelektro', '2012', NULL, '', '1984-10-06', '', ''),
('Mohamad Ispandi Bukoting', '521412068', '471373b3cef19bb890a000052b35e9bc', '', '', 'Lulus', 'NTIxNDEyMDY4', 's1teknikelektro', '2012', NULL, '', '1970-01-01', '', ''),
('Raif I. Latongko', '521412069', '3b9669ffdf1cecc42a4fb8a1b9073bfa', '', '', 'Drop Out', 'NTIxNDEyMDY5', 's1teknikelektro', '2012', NULL, '', '1985-05-05', '', ''),
('Farid. Sy Abd Latif', '521412070', 'c9d786424467c31735109a20f56272d5', '', '', 'Drop Out', 'NTIxNDEyMDcw', 's1teknikelektro', '2012', NULL, '', '1984-11-09', '', ''),
('Gimin Dako', '521412071', '808d12eaee5d231e8af8e69a55766858', '', '', 'Drop Out', 'NTIxNDEyMDcx', 's1teknikelektro', '2012', NULL, '', '1981-08-09', '', ''),
('Sukriyono Umar', '521413001', '86adeb9081966bc291f2334ebab91cb2', '', '', 'Drop Out', 'NTIxNDEzMDAx', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Amelia Ipango', '521413002', '7a89a504ba9f3407d96e089bfbb66c5f', '', '', 'Lulus', 'NTIxNDEzMDAy', 's1teknikelektro', '2013', NULL, '', '1995-08-08', '', ''),
('Yusuf Polumulo', '521413003', '9a10f75d958888c226381e24ebcf6a54', '', '', 'Drop Out', 'NTIxNDEzMDAz', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Mohamad Rohimawan Sutanto', '521413004', 'e22bd406dca15b779dd7e8ad50438596', '', '', 'Lulus', 'NTIxNDEzMDA0', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Fendi Yusuf', '521413005', '868a62c44970807faf09ef56a9225459', '', '', 'Drop Out', 'NTIxNDEzMDA1', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Hendra Mbata', '521413006', '985599edc81093d7d793b1cef3ed5a5b', '', '', 'Drop Out', 'NTIxNDEzMDA2', 's1teknikelektro', '2013', NULL, '', '1995-03-01', '', ''),
('Andika Mohamad', '521413007', '56916e9eaa3e339c5de757d0ca6d6ef2', '', '', 'Drop Out', 'NTIxNDEzMDA3', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Rahmad Pateda', '521413008', '97fcb438029235770b7ea2481500a717', '', '', 'Drop Out', 'NTIxNDEzMDA4', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Arfan A Napu', '521413009', 'a7413b190d33e4f0e1fea58b6742e715', '', '', 'Keluar', 'NTIxNDEzMDA5', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Hadijah Tahir', '521413010', 'ae8e36d03d3cd4ca22022e7e98eba1d5', '', '', 'Lulus', 'NTIxNDEzMDEw', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Moh Nur Abdul Rasyid', '521413011', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'muhamadnur440@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Novita Palilati', '521413012', '15ecc234bf6e41f0c1a72a748e2e53f3', '', '', 'Lulus', 'NTIxNDEzMDEy', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Rizky Firmansyah Musa', '521413013', 'f8288fb7c785164fcb6d861132bebe95', '', '', 'Drop Out', 'NTIxNDEzMDEz', 's1teknikelektro', '2013', NULL, '', '1994-08-12', '', ''),
('Murdiono Asiking', '521413014', '004e2cd743f871c46e7a74c384110afe', '', '', 'Drop Out', 'NTIxNDEzMDE0', 's1teknikelektro', '2013', NULL, '', '1993-01-09', '', ''),
('Muhamad Arif Sarifudin', '521413015', 'e84208f0f74ff5f4afb2888f30ee0173', '', '', 'Drop Out', 'NTIxNDEzMDE1', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Muhamad Fajriansyah Ngabito', '521413016', '8be35094d95f364beb6adc155c78f5a1', '', '', 'Lulus', 'NTIxNDEzMDE2', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Mohammad Syawal Nento', '521413017', '49d3ecd11c12dd411cf34d9f53315aa8', '', '', 'Lulus', 'NTIxNDEzMDE3', 's1teknikelektro', '2013', NULL, '', '1995-10-03', '', ''),
('Robert Nehemia Benu', '521413018', '7662b6bc976d2fbb008e460567f4a297', '', '', 'Drop Out', 'NTIxNDEzMDE4', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Wulandari Agusnita Naue', '521413019', '14577fd160e06b5fe80d78377cbe410d', '', '', 'Drop Out', 'NTIxNDEzMDE5', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Muhamad Untung Kobandaha', '521413020', 'ef0716c888a3c83298984e2438f5f23d', '', '', 'Drop Out', 'NTIxNDEzMDIw', 's1teknikelektro', '2013', NULL, '', '1995-09-05', '', ''),
('Isran L. Ismail', '521413021', '2ee126a1a4bba2e36253d86146cf597c', 'toddd23@zplotsuu.com', '', 'Aktif', 'MTU5MzU3', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Riyanto', '521413022', '8a466d112c350068e7447fc7237490e7', '', '', 'Drop Out', 'NTIxNDEzMDIy', 's1teknikelektro', '2013', NULL, '', '1991-12-06', '', ''),
('Frengki Djamaini', '521413023', '16512ae81615a61727d0480615b8f365', '', '', 'Drop Out', 'NTIxNDEzMDIz', 's1teknikelektro', '2013', NULL, '', '1992-10-12', '', ''),
('Fadliyani Djaki', '521413024', '9a8ee60ff664fce91b97e934d8fe495c', '', '', 'Lulus', 'NTIxNDEzMDI0', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('I Putu Lingga Dharma', '521413025', '8de668d0f0199c35a0c5e8cdf8a1ae3a', '', '', 'Lulus', 'NTIxNDEzMDI1', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Brayen T Ali', '521413026', '6769074928e7d32ad4f3590874374ddc', '', '', 'Drop Out', 'NTIxNDEzMDI2', 's1teknikelektro', '2013', NULL, '', '1994-11-02', '', ''),
('Fadlul Rahman Usman', '521413027', 'c26f2f342690a328046e2c8511953025', '', '', 'Lulus', 'NTIxNDEzMDI3', 's1teknikelektro', '2013', NULL, '', '1996-01-01', '', ''),
('Bunawan Kolopita', '521413028', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'bunkolopita@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Wahyu Apriliansyah Makalalag', '521413029', '12e6acd09ee2e33033a629815080a6c7', '', '', 'Lulus', 'NTIxNDEzMDI5', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Bahri Balise', '521413030', 'b76d2cfa3210630cdbd2773f7baa46ba', '', '', 'Drop Out', 'NTIxNDEzMDMw', 's1teknikelektro', '2013', NULL, '', '1991-12-03', '', ''),
('Frangki Saliko', '521413031', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'fangki28@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Mohamad Zainudin Djabu', '521413032', '9fe0b9e80870f38c07be137c5f6f1d65', '', '', 'Drop Out', 'NTIxNDEzMDMy', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Christopher Elchrisa Uno', '521413033', '5fd739ec9987b3b30e98b23ae37c735a', '', '', 'Lulus', 'NTIxNDEzMDMz', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Yudiono', '521413034', '8e9cda5f8fd875e629854576dab43a8e', '', '', 'Drop Out', 'NTIxNDEzMDM0', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Danial Gani', '521413035', 'cee2a088528c44f0388c81ce4ba7ad61', '', '', 'Lulus', 'NTIxNDEzMDM1', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Mohamad Vikri Hamzah', '521413036', 'f7fcf8651bc6eb764473b159efcb7be6', '', '', 'Drop Out', 'NTIxNDEzMDM2', 's1teknikelektro', '2013', NULL, '', '1995-05-08', '', ''),
('Kasiyanto Seuselu', '521413037', 'aeab2e16eafef5b275ff8fe8e281f0ba', '', '', 'Lulus', 'NTIxNDEzMDM3', 's1teknikelektro', '2013', NULL, '', '1994-03-09', '', ''),
('Andrian Djamadi', '521413038', '5dd3db241faeb11bf3f4e9184349448a', '', '', 'Drop Out', 'NTIxNDEzMDM4', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Abdurrahman Hulopi', '521413039', 'f447230c3922f2c5b4726cfe293c48a0', '', '', 'Lulus', 'NTIxNDEzMDM5', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Riki Rizki Tamuu', '521413040', '931adec20d773d59a6f2d3a4ca8915ac', '', '', 'Drop Out', 'NTIxNDEzMDQw', 's1teknikelektro', '2013', NULL, '', '1995-03-01', '', ''),
('Rahmat Adi Putra Harun', '521413041', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'rahmatharun39@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Moh. Aditya  Chikar Dwiputra', '521413042', '5dec8bd3ce31d106d449baa8a730e270', '', '', 'Drop Out', 'NTIxNDEzMDQy', 's1teknikelektro', '2013', NULL, '', '1995-11-09', '', ''),
('Rivan Laraga', '521413043', '52589b927cd4fe18cac1a721f93ef739', '', '', 'Drop Out', 'NTIxNDEzMDQz', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Hidayat Tahir', '521413044', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'hidayat_s1elektro2013@mahasiswa.ung.ac.id', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Novaldiyanto K. Nento', '521413045', '20065dd850a19f3164013445153aed98', '', '', 'Lulus', 'NTIxNDEzMDQ1', 's1teknikelektro', '2013', NULL, '', '1993-12-08', '', ''),
('Wayan Lena Astawa', '521413046', '780a53ebb9a22b4393c43131800973ea', '', '', 'Lulus', 'NTIxNDEzMDQ2', 's1teknikelektro', '2013', NULL, '', '1970-01-01', '', ''),
('Rolin Ismail', '521413047', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'rolinismail01@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2013', NULL, '', NULL, '', ''),
('Ihsanudin', '521414001', '1d6463a60f37fddf7311c6ea43ab42fa', '', '', 'Keluar', 'NTIxNDE0MDAx', 's1teknikelektro', '2014', NULL, '', '1995-10-02', '', ''),
('Muh. Yani Ramadhan', '521414002', 'd619f73ab6f9b30f397553623296b960', '', '', 'Aktif', 'ZXRra2Vr', 's1teknikelektro', '2014', NULL, '', '1997-11-01', '', ''),
('Yudin Monoarfa', '521414003', '9f9f7936eece70895d79dce545660b71', '', '', 'Aktif', 'a2lrZW50', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Arfan Umar', '521414004', '22a81c4a3f4202376b8f24bcd384097d', '', '', 'Aktif', 'aW50a2ts', 's1teknikelektro', '2014', NULL, '', '1996-04-05', '', ''),
('Abd. Azis Kantu', '521414005', '1fac796b2755744e5461ad9c8649bdb8', '', '', 'Lulus', 'a2t0ZXRy', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Reka Nindya Putri Salilama', '521414006', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'ekasalilama@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Abdul Rofik Husain', '521414007', '93841695ba78a822e8b692d16bc29406', '', '', 'Lulus', 'a2xrbnJl', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Mohamad Eko Prasetio', '521414008', '0fa3bb9879aa840036429e8ce4095653', '', '', 'Aktif', 'ZXR0ZWxu', 's1teknikelektro', '2014', NULL, '', '1997-05-02', '', ''),
('Mohamad Alif Akbar Lantowa', '521414009', '2df74d415ee7815d9b95b8d3d769d172', 'ryolantoa08@gmail.com', '082293388095', 'Aktif', 'MTIz', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Raman', '521414010', '46d77185ec7116d95d6ccabc1804ca12', '', '', 'Aktif', 'a2VrcmV0', 's1teknikelektro', '2014', NULL, '', '1995-03-11', '', ''),
('Frenki Tahir', '521414011', '8fb487b7748e755a908fa3f7e9c3b29c', '', '', 'Lulus', 'a25la290', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Idrus Lumula', '521414012', 'f02d4e7da427e5a0b228e13d373fbd42', '', '', 'Aktif', 'b2tlaXRr', 's1teknikelektro', '2014', NULL, '', '1994-04-06', '', ''),
('Rahman Musa', '521414013', 'f407f54a8923f8e79eddbd00ab6da8b5', '', '', 'Aktif', 'a2lrcmVl', 's1teknikelektro', '2014', NULL, '', '1994-06-07', '', ''),
('Wahyu Ningsih Datau', '521414014', 'd5a3e885660cdcadd2d34edcc5b8ff54', '', '', 'Aktif', 'ZWtya2xl', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Indriani Hulima', '521414015', 'b86e04d28916b421cbfdc498c609bb5d', 'indrianihulima@gmail.com', '082393602070', 'Aktif', 'MTIzNDU2aW5kcmk=', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Verawati Ahmad', '521414016', 'f4e12f4af24159c038dbdec6a631a0e1', '', '', 'Lulus', 'dGVrbGtu', 's1teknikelektro', '2014', NULL, '', '1996-04-09', '', ''),
('Dicky Dunggio', '521414017', '5223731408444b4bc5739bd855cf530a', '', '', 'Lulus', 'a2t0ZWly', 's1teknikelektro', '2014', NULL, '', '1996-07-08', '', ''),
('Zulfikal Amali', '521414018', '618fc4dd6c6c3d183438312e720c9a9a', '', '', 'Aktif', 'a2VvZXRu', 's1teknikelektro', '2014', NULL, '', '1995-07-07', '', ''),
('Abd. Wahid Arifin Antu', '521414019', 'd2419156241387629d24e6c2292eeeb8', '', '', 'Lulus', 'bGVya3Rp', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Yurlin A. Tahir', '521414020', '0d71694456b73f09a952e33d90340a36', '', '', 'Aktif', 'a25vcmtr', 's1teknikelektro', '2014', NULL, '', '1995-07-04', '', ''),
('Sardin Sahu', '521414021', '6f6a5b5778a834a447541abe4d2c995b', '', '', 'Aktif', 'aW9la25r', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Arfan Syaputra', '521414022', '486f34c5b3d20e10471d156eb41fa586', 'arfansyaputra303@gmail.com', '082347990067', 'Aktif', 'YXJmYW5zdWtzZXMyMDIx', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Randi Virgantara Rontos', '521414023', 'e2fc47c2adb76eb41d428ece3064e709', '', '', 'Keluar', 'dGVlb25l', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Muh Zulfikar Padmon', '521414024', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'muhamadzulfikarpadmon@gmail.com', '081281978168', 'Aktif', '', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Anggie T R Yusuf', '521414025', 'c041a3c1c26f31757425fe83e4108759', '', '', 'Aktif', 'dGlrZXRl', 's1teknikelektro', '2014', NULL, '', '1996-08-07', '', ''),
('Rizkillah Molamahu', '521414026', 'a71d35c7e316c3717e99da336a71f5b5', 'rizkillahmolamahu@gmail.com', '082342016342', 'Aktif', 'aWthbGNhbm8=', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Ismail Kum', '521414027', 'acf3c325fe842a13939f4dbab3490c53', '', '', 'Aktif', 'a2Vlcm50', 's1teknikelektro', '2014', NULL, '', '1995-01-01', '', ''),
('Mohamad Zulkifli Umar', '521414028', '9a4776607d711a8d1a5891fbb4b2e1ef', '', '', 'Aktif', 'bGVya2l0', 's1teknikelektro', '2014', NULL, '', '1994-06-10', '', ''),
('Arif M. Kau', '521414029', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'arifmarzukikau@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Marfanri Lamatenggo', '521414030', '387e3fd62934dbfc255784430f3b75d6', '', '', 'Lulus', 'dGVla25y', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Ismail Pasoo', '521414031', 'dfd5fdeda38e160b4577c4fa7272dd1b', 'ismailpasoo96@gmail.com', '082144880292', 'Aktif', 'aXNtYWlsMzMz', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Dienul Hakim Haryanto', '521414032', '84361db2e8b172c8d6db61b0fb6fd6ee', '', '', 'Lulus', 'dGlra290', 's1teknikelektro', '2014', NULL, '', '1995-09-10', '', ''),
('Faisal Rizki Habie', '521414033', 'b714e10ccd218758f94cdb04da30b165', 'faisalrhabie@gmail.com', '082292634968', 'Aktif', 'ZXRlaXRr', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Malik Abdul Azis', '521414034 ', '2a0cd1ee8bfd4fafa149eade81141442', 'aldi.malikabdulazis31@gmail.com', '085256263426', 'Aktif', 'UVdFUlRZeXkwMzE=', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Nolviani Ikano', '521414035', '0eefa749a48ca4c0e4632bc51703581d', '', '', 'Aktif', 'ZW9rdGll', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Dwiky Heryanto Arief', '521414036', '0a5ead703abc9f3b5d3ebcf9a9868600', '', '', 'Lulus', 'ZXRya2tu', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Bayu Baskoro', '521414037', 'ab44f7cbf19dab166d12cc7618227154', '', '', 'Aktif', 'b2Vrcm5r', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Muh. Juniansyah', '521414038', 'eac600d5bf8a39da28c5cd252153c0fa', '', '', 'Lulus', 'ZWV0ZWtp', 's1teknikelektro', '2014', NULL, '', '1996-12-06', '', ''),
('Moh Hairudin Katili', '521414039', '1759ada54079f1882527470e621bd9c1', '', '', 'Aktif', 'a2VldGxu', 's1teknikelektro', '2014', NULL, '', '1995-07-10', '', ''),
('Sandi Utina', '521414040', '1748931ad10c63cf048047dc9502ff72', '', '', 'Lulus', 'aXRrdHJs', 's1teknikelektro', '2014', NULL, '', '1994-04-10', '', ''),
('Moh. Abdu Adam', '521414041', 'cdadc01d3e9b5d1f6bba02d155f73196', '', '', 'Lulus', 'dGVrcmVv', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Indra Valentino Gude', '521414042', 'd48c3d282bd77e8679e8393e828ccbcb', '', '', 'Aktif', 'b25sa2Vl', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Dery Setiadi Labelo', '521414043', '82d0146e8a3e4ddd976d8c598300bc12', 'ibnuamribin@gmail.com', '082292726236', 'Aktif', 'ZWtpdGx0', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Yusrin R. Puana', '521414044', 'c20121b2fd3611a6bbddf9a2ff7b82e2', '', '', 'Lulus', 'dGlra2tl', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Kadrian R. Puana', '521414045', '0578dd7314269352f89be936de87d1be', '', '', 'Aktif', 'a2Vlbmty', 's1teknikelektro', '2014', NULL, '', '1994-03-04', '', ''),
('Salma Kune', '521414046', 'f5a17f8d88baf493d042a7fc8ab53c31', '', '', 'Lulus', 'b3Rpa2x0', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Roy K Bau', '521414047', 'fb54f7adbfe9f4e38cbca047ec9a59f8', '', '', 'Aktif', 'cmtrdG9s', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Abdul Nasir Nuriman Djaini', '521414048', '400c0dc98422a7f2736fdb07279c18c9', '', '', 'Aktif', 'ZW9pZXRu', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Mohamad Rizky Wirajaya', '521414049', 'b213399060c0da3104d6020466fee71a', '', '', 'Lulus', 'a2tpa3Ru', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Mohamad Rizki Julianto Habie', '521414050', '408f7309590125f2769142009ceb6980', '', '', 'Aktif', 'dGtuZXJs', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Sri Melti Hinelo', '521414051', '667fca93a29ab6971c77ff18538e475b', '', '', 'Aktif', 'a3JlZXRr', 's1teknikelektro', '2014', NULL, '', '1997-09-01', '', ''),
('Zunaid Zus Abdullah', '521414052', '1b0c6f2d9378ee3ca5f0714798ab11d4', '', '', 'Keluar', 'a3RscmVr', 's1teknikelektro', '2014', NULL, '', '2019-01-01', '', ''),
('I Made Dwi Harinata', '521414053', 'd1fe6f9620cdc37782989eeedb6d04a9', '', '', 'Lulus', 'NTIxNDE0MDUz', 's1teknikelektro', '2014', NULL, '', '1995-04-09', '', ''),
('Ica Pebriyani Polapa', '521414054', '94de320212cb3faf22c3c4fa66b6d708', '', '', 'Aktif', 'ZW90bGVr', 's1teknikelektro', '2014', NULL, '', '1996-08-02', '', ''),
('Muhamad Ikbal Sabaka', '521414055', '97a10b94db37dab6cb27883012aa97da', '', '', 'Aktif', 'a25laWVr', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Tawil Buhang', '521414056', '3a983e49ebbfdebfa5db82f94d1d0869', '', '', 'Lulus', 'NTIxNDE0MDU2', 's1teknikelektro', '2014', NULL, '', '1995-03-10', '', ''),
('Muhammad Ziad Musa', '521414057', 'f6b168f9e227da5e3f16dd44365934ef', '', '', 'Aktif', 'a2lrbHRl', 's1teknikelektro', '2014', NULL, '', '1970-01-01', '', ''),
('Mohamad Firmansyah', '521414058', '5763b04665effec963ebfa22d4dbec18', '', '', 'Lulus', 'NTIxNDE0MDU4', 's1teknikelektro', '2014', NULL, '', '1997-01-01', '', ''),
('Riyan Mursali', '521414059', 'e643a342e955390b0aeaae0ed282c008', 'riyan.mursali99@gmail.com', '085240741294', 'Aktif', 'b2tlcnRl', 's1teknikelektro', '2014', NULL, '', NULL, '', ''),
('Fajar Octavianus Eka Putra Tuelah', '521415001', '46c987a54eab963269fe723a9487255d', 'fp602657@gmail.com', '082189555763', 'Aktif', 'a2l0YXRpZGFrdGF1', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Rivaldo Lamadi', '521415002', '8c436309eb529914421da6b5288fc30f', '', '', 'Aktif', 'cmV0dGVv', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('I Kadek Winarta', '521415003', 'e883d9fbc531af7dd1a75f70fc2b023b', '', '', 'Lulus', 'dHJlbmtr', 's1teknikelektro', '2015', NULL, '', '1997-03-11', '', ''),
('Aldi Wiranto D. Lasena', '521415004', 'ac94d38dfb2b1825b9ee930cf2f023f9', '', '', 'Aktif', 'ZXJlb25s', 's1teknikelektro', '2015', NULL, '', '1998-07-01', '', ''),
('Gunawan', '521415005', 'f2fb43b384e8b65c179cc61a5d431530', 'gunawan020795@gmail.com', '081331255322', 'Aktif', 'Z3VuYXdhbjA3MDI5NQ==', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Fadlulhaq Sidadu', '521415006', 'e6de6e1b039fc05103552691bf0bc266', 'fadlulsidadu@gmail.com', '082297457227', 'Aktif', 'aWZhdHNpZGFkdTEzbWFyZXQ=', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('I Gusti Ngurah Eka Darma Putra', '521415007', 'f9a1a34f59e22530edda6369d834ece9', '', '', 'Lulus', 'ZWtrcmx0', 's1teknikelektro', '2015', NULL, '', '1993-10-10', '', ''),
('Nurlela Karim', '521415008', '6726fe04962a6d76a82e36b637a5ff38', 'nurlelakarim93@gmail.com', '085144011774', 'Aktif', 'QmFoYXIyOQ==', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Abdi Mujahidin Rahim', '521415009', '2c7e09308d4eb57ea3fc27d7287521c0', '', '', 'Aktif', 'a2tlcm9r', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Moh Fatur Botutihe', '521415010', '9225040b8c47f053a9ffad996dc06b8d', '', '', 'Aktif', 'a2VrcnR0', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Randani Kurniawan Putra Otaya', '521415011', '25d586a4b0b7beb8ee68dfa745d8563f', 'otayarandy020@gmail.com', '08979465506', 'Aktif', 'IyNSUm5keTAw', 's1teknikelektro', '2015', '637dfbddee', 'kotamobagu', '1997-03-29', 'kotamobagu', ''),
('Rifki Hidayatullah Bando', '521415012', '70e24012c8cfd77e2947b7a901caf392', 'rifkihidayatullah446@gmail.com', '081527927951', 'Aktif', 'YXBpYWlyMTIzNA==', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Yoseph Adolf Rontopaly', '521415013', '0b87368d931172f9ee79da82d31af9c0', '', '', 'Lulus', 'a2xlcmtp', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Satrianto Van Solang', '521415014', '40c42c40aee71d7f8182e4e2ab98be6c', '', '', 'Aktif', 'ZWlybGtu', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Dewa Made Cakra Gunawan', '521415015', 'c2a8e9fc39153172fb359a402b761d52', '', '', 'Lulus', 'b2trbHJl', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Jendris N. Kasim', '521415016', '591496734cd9a01c4c9cb07f7b529038', '', '', 'Aktif', 'a2V0b2ts', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Rafli', '521415017', 'eaddd01b0446b43a0899e4593b82bb32', '', '', 'Lulus', 'a2x0dGVr', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Ezra Yosis Kesek', '521415018', 'e21aa0ccb9a142dacf0ff2516e9cfbd2', 'ezrakesek21051997@gmail.com', '081356233117', 'Aktif', 'cXdlcnR5MTIzNDU=', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Andika Daeng Solong', '521415019', 'd024442cec8c3a9d9ecfbdc51c4cbb86', 'andikadaengsolong2015@gmail.com', '082217456329', 'Aktif', 'MjAxNQ==', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Arifianto Usman', '521415020', '8c996a7eaa88ae95fbf8d3ad7b20159e', '', '', 'Aktif', 'cmt0a2Vl', 's1teknikelektro', '2015', NULL, '', '1997-02-07', '', ''),
('Alis Paki', '521415021', '485d9499e48bcffe1756a877771f4dfe', '', '', 'Lulus', 'a3RlZW9r', 's1teknikelektro', '2015', NULL, '', '1995-06-08', '', ''),
('Risky Amala', '521415022', '2e0840951c81e0dad8dd38e7dd1e2ce9', '', '', 'Lulus', 'ZW50a3Rr', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Hasmirad Ndikade', '521415023', '044745395e61adc3e581a97d64fd75e3', '', '', 'Lulus', 'cnRvZWxp', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Nandar Imale', '521415024', 'd6d08d49d14af9b19d96414d5ef51688', '', '', 'Lulus', 'bnRla2t0', 's1teknikelektro', '2015', NULL, '', '1996-01-10', '', ''),
('Edwar Sabaya', '521415025', '4231310ba8e727f00cab475d2d7880cb', 'azisstevenance0@gmail.com', '082296711691', 'Aktif', 'a2Vra250', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Yusuf Anton Mohamad', '521415026', 'd333c36fef7727e856f7eb11d636e08c', 'mhibyusuf@gmail.com', '082293167517', 'Aktif', 'cmt0ZWVr', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Yahja Igirisa', '521415027', '0b57beca29dfc20007fb43aef2738376', '', '', 'Lulus', 'a2tybmVs', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Rahmatiagandura', '521415028', '2df74d415ee7815d9b95b8d3d769d172', 'rahmatiagandura15@gmail.com', '085345904808', 'Aktif', 'MTIz', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Moh. Marfan Sm. Ali', '521415029', '3625d981aecce4653454cf58c81308b7', '', '', 'Lulus', 'ZWtuZW9y', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Alwin Darma Daima', '521415030', 'e6d0074102d87570ae35a16e00521710', 'daimakago@gmail.com', '082271632887', 'Aktif', 'R29uZHJvbmc=', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Mohammad Napu', '521415031', '4489d544b6763df3a787aaa79cc59ba9', '', '', 'Lulus', 'ZWt0bGVr', 's1teknikelektro', '2015', NULL, '', '1997-10-03', '', ''),
('Risman Ergouna', '521415032', 'a5294c1e2e0b70486c7db8edabfe0910', '', '', 'Aktif', 'cmtlb2Vs', 's1teknikelektro', '2015', NULL, '', '1997-04-06', '', ''),
('Fizranto Umar', '521415033', '81230b933dfe5c5a2812d2ff554870be', 'umarfi27anto@gmail.com', '082271171001', 'Aktif', 'VGVrVHJvRml6Mjc=', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Usman M. Husain', '521415034', '23f04d8b64e573b798f49c1aa3bbc0de', '', '', 'Aktif', 'ZXRsaWVl', 's1teknikelektro', '2015', NULL, '', '1970-01-01', '', ''),
('Indrawati Lihawa', '521415035', 'a6d151c7756afaeef8a0afe83cdbfbd6', 'lindalihawa97@gmail.com', '085144011801', 'Aktif', 'aHVsb3BpMDY=', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Azis Steven S. Ance', '521415036', '682e43092c2666091dd55d10821b4fc2', 'azisstevenance0@gmail.com', '081325025119', 'Aktif', 'QXppc0FuY2UxNA==', 's1teknikelektro', '2015', NULL, '', NULL, '', ''),
('Rohayati Idris', '521416001', 'fc83b5f1b20fe59a7f232c123cecfcd8', 'anyidris3@gmail.com', '', 'Aktif', '', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Andre Muchlis', '521416002', '0251e3a00b23aa81560dffcba2883b80', '', '', 'Lulus', 'a3RrZW9l', 's1teknikelektro', '2016', NULL, '', '1997-10-09', '', ''),
('Febri Janto Rahim', '521416003', 'f2afec1d2aff6956682a70cea3b0ffce', 'rahim.erik@yahoo.com', '0895707060408', 'Aktif', 'Qk9vbTEyMzQ=', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Muhammad Triyadi Adam', '521416004', '601dd1494ad8c8cde39807e52e2b9650', '', '', 'Lulus', 'b250a3Jr', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Luthfiani Azzahra', '521416005', 'cd3cfe4be19fda2ba5fa57dae3d71084', '', '', 'Lulus', 'bG5pa2Vl', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Desyawanti Tawape', '521416006', '6e451f5e00f222651b334e760acb2abb', 'ecitawape@gmail.com', '082293723310', 'tidak aktif', 'RWN5eTE5OTg=', 's1teknikelektro', '2016', '645eef9c30', 'Gorontalo', '1998-12-01', 'Jl. Rambutan', ''),
('Mahmud Laba', '521416007', '24d807d5d8e8f291429d2b96f7542280', 'mahmudlaba22@gmail.com', '085342896309', 'tidak aktif', 'TUFlazIwMTY=', 's1teknikelektro', '2016', 'f53eb48005', 'Biau ', '1997-09-01', 'Desa omuto kecamatan biau kabupaten gorontalo utara', ''),
('Nasir S Amu', '521416008', '7b27c25dc9c2015fd892af5a96a8dafe', 'nasirelektro16@gmail.com', '082240763501', 'tidak aktif', 'dGVrbmlrMTYwOQ==', 's1teknikelektro', '2016', '637dfbddee', 'BUNTULIA TENGAH', '1997-09-16', 'desa buntulia tengah, kecamatan Buntulia, kabupaten pohuwato, provinsi gorontalo.', ''),
('Yusuf Adji Pangestu', '521416009', 'ac0f7551e179bca2cc826195c53c4ae0', '', '', 'Aktif', 'ZWVya29u', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Ahmad Laba', '521416010', 'a2262b0ca789014fb0778f259613fda6', 'ahmadlaba315@gmail.com', '081241107319', 'tidak aktif', 'RU5kYW5nYW5qYXJ3YXRpMTQ=', 's1teknikelektro', '2016', '92459d083c', 'Biau', '1996-12-09', 'Desa Uuan, kecamatan Dumoga Barat, kabupaten BolaangMongondow', ''),
('Maharani Rahma Putri Syafi`i', '521416011', '8ee81d0937ef6dff62276b316e35aa36', '', '', 'Lulus', 'dG5lb2x0', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Moh Aditya Abd.gani', '521416012', '619412d0b8d684e2febb4a9e65aa6f93', '', '', 'Aktif', 'aXRva2tl', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Hermanto Idrus', '521416013', '199a1a9518f368a2ccaa9184b4dd8b16', 'hermanto_s1elektro2016@mahasiswa.ung.ac.id', '082239016013', 'tidak aktif', 'TmltNTIxNDE2MDEz', 's1teknikelektro', '2016', '64809b0cfc', 'Popalo', '1997-07-03', 'Desa putiana kecamatan anggrek kabupaten gorontalo utara', ''),
('Rowin Kaiha', '521416014', '238ac806740e959ec81c9fbc58fb6754', 'rowinkaiha29@gmail.com', '085333156915', 'tidak aktif', 'UlMxNm5hMTIwMTZL', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Ridoh Ibrahim Hasania', '521416015', 'e912fb1d40da19c4cad02306ed1b2324', '', '', 'Aktif', 'ZXRuaWty', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Devita Gude', '521416016', '729a53e4b1b6c6e4bdf35c1e0012afff', 'devitagude17@gmail.com', '082296711198', 'tidak aktif', 'ZGV2aXRhZ3VkZQ==', 's1teknikelektro', '2016', NULL, 'Tahuna', '1997-08-15', '', ''),
('Dadang Ahmad Ghozali', '521416017', '80ee4a3e7be34cab17ed9a485e797fe0', 'ghozaliahmad404@gmail.com', '081248008239', 'tidak aktif', 'dGxub2Vr', 's1teknikelektro', '2016', '5864493a02', 'Karawang', '1998-04-01', 'Desa Palopo, Kec. Marisa , Kab. Pohuwato, Gorontalo', ''),
('Diaz Cipta Pratama Talawo', '521416018', '5113afa7bceaa28a631f8947225fc010', 'diazpratamafix@gmail.com', '082292631715', 'Aktif', 'R29yb250YWxvNDY=', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Saiful Alam', '521416019', '63ecca5cf2b8b61e8f2912b2f18a0b79', 'saifullalamm98@gmail.com', '082259199416', 'tidak aktif', 'U0FJcHVsMDk=', 's1teknikelektro', '2016', 'd53ee838e9', 'GORONTALO', '1998-05-29', 'DESA MARGOMULYO, KECAMATAN TOLANGOHULA, KABUPATEN GORONTALO', ''),
('Aleko Saiful', '521416020', '3f84bd2fabfe23d68dc3fee326d56a9b', 'acochap01@gmail.com', '085340243507', 'Aktif', 'QWxTYTEyMzQ1Njc4', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Dafid Yance', '521416021', 'd0ed2308a4333d064975f377778438a2', '', '', 'Aktif', 'aWV0a25v', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Maulid Afrizal Hala', '521416022', '6cd89c738eb6fafdd9c13810d6a7e6b1', '', '', 'Aktif', 'ZW5pcnRr', 's1teknikelektro', '2016', NULL, '', '1998-02-07', '', ''),
('Annisa Riana Haras', '521416023', '889c5f07adf0883a687f171aae77d0c4', '', '', 'Lulus', 'bnRrb3J0', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Rahman Abdul Aziz', '521416024', 'ca9b89dca9c9e2d18944bafe6cbf97ce', '', '', 'Aktif', 'cm5lZWVs', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Fitriani Lakoro', '521416025', 'a48a8eaf4712b27852f41389f9e2a978', '', '', 'Lulus', 'dGtybGtl', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Septian Aderay Olii', '521416026', 'e9ac32ab96e2987b69d7a2c75e68c4bf', '', '', 'Keluar', 'aXRub3Jl', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Renaldi Ingopo', '521416027', 'c06b3f2fdb0b51dc5c15050b61cda386', 'renaldiingopo@gmail.com', '085366283661', 'tidak aktif', 'Uk5sZHkjMTA=', 's1teknikelektro', '2016', 'd53ee838e9', 'Manado', '1998-10-10', 'Jl. Rambutan\nTomulabutao Selatan\nDungingi', ''),
('Deasy Cicilia Mambe', '521416028', '2bbce4190175b1a61643f9c110d55f5c', 'idulkadir10@gmail.com', '081341768126', 'tidak aktif', 'a2x0ZWt0', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Mohammad Reza', '521416029', '54e07ecdcbc0c2bad24e6a19d01364ea', '', '', 'Aktif', 'ZW5pa29l', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Fathan Kurnia Zain', '521416030', 'd732bcc097de2f602e14fbc4f40df754', '', '', 'Aktif', 'bmtsZWVr', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Jainaldi Tongkad', '521416031', '38629c5914ccaa0f8a678d899bb01bbc', 'jainaldi@gmail.com', '085397472685', 'Aktif', 'SU5hbDAwMDM=', 's1teknikelektro', '2016', 'fb4b7ab909', '', NULL, '', ''),
('Abdul Razak Syamsudin', '521416032', 'a9769401f1f4ee7f3c2fdf267bf621ac', '', '', 'Aktif', 'bmV0bGlr', 's1teknikelektro', '2016', NULL, '', '1997-08-11', '', ''),
('Randa Akuba', '521416033', 'f88bc9cc3a0cbaeb0b7579c9260e878b', '', '', 'Aktif', 'ZWtla2tv', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Rizal Jumaela', '521416034', '8b38aee6ba282cbd7f13cafd7ee57cb2', '', '', 'Aktif', 'dHJla2Vr', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Indah Intania Rahmasari R. Daud', '521416035', '5fa7e5f2006b71f58159e5b8b0936f7c', '', '', 'Aktif', 'ZW9pZWVr', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Junaidi Adam', '521416036', '06c39e396baf917e53fd8f207ebd8a01', 'Junaidiadam02@gmail.com', '085399506624', 'tidak aktif', 'SnVuYWlkaWFkYW0yNw==', 's1teknikelektro', '2016', NULL, '', NULL, '', ''),
('Robert Rahmat Rivay Gaib', '521416037', '8b318ebc43cf0837da9e81cec44bf561', 'robertgaib29@gmail.com', '082190202511', 'tidak aktif', 'Uk9CRVJUZ2FpYjE2', 's1teknikelektro', '2016', 'fb4b7ab909', 'Gorontalo', '1998-02-28', 'Jln. Muchris rahim Desa molintogupo kecamatan suwawa selatan kabupaten bone bolango ', ''),
('Ilham Pratama Laiya', '521416038', '42e8fe64f849901283f57400868d4123', '', '', 'Aktif', 'aWVvZWxy', 's1teknikelektro', '2016', NULL, '', '1970-01-01', '', ''),
('Syahrul Aji Prayoga', '521417001', '4d2c64c39e6f5f99dace90f383258e2d', 'syahrul_s1elektro2017@mahasiswa.ung.ac.id', '085399703760', 'tidak aktif', 'ZWxla3RybzIwMTc=', 's1teknikelektro', '2017', '36d1464083', 'Gorontalo', '1999-11-27', '', ''),
('Indah Oktaviani', '521417002', '4cc14cae3fe0a5a5fca9685f3db0d738', '', '', 'Lulus', 'ZWx0aW9l', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Mohamad Arif Kuna', '521417003', '11f71573df7f8d28a38ac9a497286b16', '', '', 'Aktif', 'aWtuZWVl', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Nasrun Ishak', '521417004', '68224ec59011611623b1113909b902fc', 'nasrunishak123@gmail.com', '087862281031', 'tidak aktif', 'bWFoYXNpc3dh', 's1teknikelektro', '2017', '64809b0cfc', 'Gorontalo ', '1999-04-29', 'jln.imam samadi desa dunggala Kec.Tapa Kab.bonebolango', ''),
('Novrizal Saidi', '521417005', '243e0777c5e4ad1ff4ecc684ea2472bb', '', '', 'Keluar', 'bmtsZW90', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Guntur Hadi Saputra', '521417006', '661c9ee96339a68128f16a5dd2e86b93', 'guntur_s1elektro2017@mahasiswa.ung.ac.id', '082121584043', 'tidak aktif', 'R3VudHVyaGFkaTAwNw==', 's1teknikelektro', '2017', '98a4a6f7b7', 'Ujung Pandang', '1998-11-27', 'Jl.A.Wahab, Lingkungan II Kel.Hunggaluwa Kec. Limboto', ''),
('Ade Cil Taha', '521417007', '62fea0665a063d1c3fa4b16fe2158490', '', '', 'Aktif', 'a25sZWtv', 's1teknikelektro', '2017', NULL, '', '1999-08-10', '', ''),
('Muhammad Irfan Maulana', '521417008', '07c77a15949798d1960bf36e09427c31', 'muhammad_s1elektro2017@mahasiswa.ung.ac.id', '081934649133', 'tidak aktif', 'ZGVwb2s0MjA=', 's1teknikelektro', '2017', '98a4a6f7b7', 'Kab. Bogor, Jawa Barat', '1999-04-29', 'Jalan HJ. Riman Newi RT5 RW5 No.17, cilodong, depok', '');
INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('Shaleh Alhabsyi', '521417009', '7bc86a19a447ffd65dcffd4484dfd241', '', '', 'Aktif', 'b3JpdGV0', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Fahriansyah S. Dg. Parebba', '521417010', '289120339c53c951afc1a69fdb386863', 'fahriansyah_s1elektro2017@mahasiswa.ung.ac.id', '082291586930', 'tidak aktif', 'MTIzZmFocmk=', 's1teknikelektro', '2017', '98a4a6f7b7', 'Kab. Tolitoli, Sulawesi Tengah', '1998-11-28', 'Jl. Hajar', ''),
('Muh. Ichsan', '521417011', '240367c01a190004c712e439df2fd75b', '', '', 'Aktif', 'aWt0ZXJu', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Saripudin', '521417012', '135dfa981f3cc95600cc84d37b9135db', 'saripudin_s1elektro2017@mahasiswa.ung.ac.id', '082291070739', 'tidak aktif', 'SmFuZ2FuTWFyYWgwMQ==', 's1teknikelektro', '2017', 'f53eb48005', 'Dungingis', '1998-05-02', 'Jl. Madako Dusun Madako Desa Dungingis Kecamatan Dako Pemean Kabupaten Tolitoli Provinsi Sulawesi Tengah', ''),
('Zul Ilham', '521417013', 'be0d4f100695106f1f605ad21e23add4', 'zul_s1elektro2017@mahasiswa.ung.ac.id', '08124350808', 'tidak aktif', 'MjEwMU1hcGFsYUFsYXNrYQ==', 's1teknikelektro', '2017', 'f53eb48005', 'Lamena', '1999-01-15', '', ''),
('Fajar Nugraha Ali', '521417014', '03db3c649e495eaa769a6d99e6ed8a7b', '', '', 'Aktif', 'a2Vla2Vy', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Muhamad Ismail Puluhulawa', '521417015', 'cd4a0cf855513b2037147b7cfad36c83', 'muhamad_s1elektro2017@mahasiswa.ung.ac.id', '082291077991', 'tidak aktif', 'SVNtYWlsMTk=', 's1teknikelektro', '2017', 'f53eb48005', 'Biau', '1999-02-19', 'Desa Ilotunggula, Kec Tolinggula, Kab Gorontalo Utara', ''),
('Hendra Palakua', '521417016', '7c24910184a459f5ee61016b798d4ca3', 'hendrapalaku2000@gmail.com', '082393228498', 'tidak aktif', 'ZXRrbmxp', 's1teknikelektro', '2017', 'd53ee838e9', 'Gorontalo', '2000-03-24', 'Jln delima', ''),
('Taufik Firmansyah S.muda', '521417017', '381b2d4ce3a7de86dfc1b51e3feafa07', '', '', 'Aktif', 'a3J0a2V0', 's1teknikelektro', '2017', NULL, '', '1998-12-03', '', ''),
('Saipul Karim Suleman', '521417018', 'b4a39b3b2bf602af99af1c5841d7c154', '', '', 'Aktif', 'aWt0cmVs', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Rizky Ramadhan A. Mobiu', '521417019', '5f06fbfd4e9813c126852d12de24d65a', '', '', 'Aktif', 'b2tuZWts', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Idrus Bin Syeh Abubakar', '521417020', '145541d2f6cbe81eac2683c6b3152d39', '', '', 'Aktif', 'ZWtsbnRl', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Samsul Korompot', '521417021', 'a26641211a6c351ddc130c2a13a2de6e', 'ombixean@gmail.com', '085396243780', 'tidak aktif', 'b2xlbmVr', 's1teknikelektro', '2017', 'fb4b7ab909', 'Pontak ', '1998-05-04', 'PONTAK', ''),
('Anggi', '521417022', '676714b053ad902f55c2fbbd660c5f18', 'anggimahmud21am@gmail.com', '085251969450', 'tidak aktif', 'a3R0aWtl', 's1teknikelektro', '2017', 'fb4b7ab909', 'Luwuk', '1999-04-21', 'Jl. Kerapu, Desa Kalumbatan, Kec. Totikum Selatan, Kab. Banggai Kepulauan, Prov. Sulawesi Tengah', 'file/521417022-passfoto.jpg'),
('Faisal Arasigi', '521417023', 'a867d81f5818d2af49474f133dd28201', '', '', 'Aktif', 'dG5yZWxr', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Fiqry Ahmad', '521417024', 'a7edbd43e7c0e81cf08fd86e479a05c7', 'fiqry_s1elektro2017@mahasiswa.ung.ac.id', '081355161276', 'tidak aktif', 'RklRcnkyNQ==', 's1teknikelektro', '2017', '637dfbddee', 'Salangano', '1999-08-25', 'Salangano', ''),
('Firmanzah Adam', '521417025', 'e4ffb1b070841b0ec31ae18421548c73', '', '', 'Aktif', 'dHRraXJv', 's1teknikelektro', '2017', NULL, '', '1999-08-01', '', ''),
('Reynal Ahaliki', '521417026', '3e15d1c205e0c3954b5cc6b8b12ef8b9', '', '', 'Aktif', 'ZXRrcm5r', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Dandi Bakari', '521417027', '2b46fc654d350f5464f1acdd2d569541', '', '', 'Aktif', 'aXJrdGtl', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Ismail Zulkifly Radjak', '521417028', 'fdd9221623ab3e436a78109a6e9cf8af', 'izradjak@gmail.com', '089698062678', 'tidak aktif', 'SVpSYWRqYWs5OQ==', 's1teknikelektro', '2017', '274eccd1e7', 'gorontalo', '1999-09-07', '', ''),
('Samsir Tawadu', '521417029', '6b38749ff3d076bf6ee70d85d63d92fa', '', '', 'Aktif', 'cmVsb2Vr', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Melky Alfazrin Yusuf', '521417030', '5ccf9c68b96ee0ce0eb658df5f6f20b9', 'melkyalfazrin@gmail.com', '089530223370', 'tidak aktif', 'S0VyYXN1a2FuMTIzNDU=', 's1teknikelektro', '2017', '645eef9c30', 'Gorontalo', '1998-06-27', 'jln.trans Sulawesi desa tunggulo kec.limboto barat kab.gorontalo', ''),
('Abd Hayan Arune', '521417031', '7a97e795e145a7fef71aac1b085f63d0', 'abd_s1elektro2017@mahasiswa.ung.ac.id', '082194349280', 'tidak aktif', 'U0hBS0EgQQ==', 's1teknikelektro', '2017', '645eef9c30', 'Bualemo B', '1999-12-30', 'Bualemo B,kec Bualemo Kab Banggai,Sulawesi Tengah ', ''),
('Wafiq Abd. L Arsyad', '521417032', '8e96ad48086564c583034561155ced0d', '', '', 'Aktif', 'a2xvdGVr', 's1teknikelektro', '2017', NULL, '', '1998-12-12', '', ''),
('Mohamad Iqram Septian Wungguli', '521417033', '460f874f59966d8f38f1ef0ae6a83daf', '', '', 'Keluar', 'a2trdGxl', 's1teknikelektro', '2017', NULL, '', '1999-03-09', '', ''),
('Muh Syukriadi Ansar', '521417034', 'f76ad78171fa8b68f6f5e310aa211121', 'syukriadiansar@gmail.com', '085823769392', 'tidak aktif', 'b3RyZWtl', 's1teknikelektro', '2017', '645eef9c30', 'Desa Eteng, Kec. Maasama, Kab. Banggai', '1999-01-22', 'PADANGON', ''),
('Jafri Renaldi. N Lagembo', '521417035', '5647b8ce57bd4f4da884172a2eab600f', 'jafri_s1elektro2017@mahasiswa.ung.ac.id', '081355015459', 'Aktif', 'MTNhZ3VzdHVzOTk=', 's1teknikelektro', '2017', NULL, '', NULL, '', ''),
('Asip Pulumai', '521417036', '38dcb57e2dd73dcc40b71e87a1614183', '', '', 'Aktif', 'aWtydGVr', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Syahrul Ramadhan Irawan Kamaru', '521417037', '2cd9eab3ff6a101641108c6cfc80bed8', '', '', 'Aktif', 'a2VvaWty', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Majid Abdul', '521417038', 'd0c4a1e09a1adb1696a47da014e30b2b', '', '', 'Aktif', 'dGVubGly', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Ngat Marbi', '521417039', '4923113b9cb87c44f220897f103205d4', '', '', 'Aktif', 'cm5va2t0', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Moh. Fahreza', '521417040', '1ee5bd4936866d95a3c96fb2cdd7fcfe', 'fahreza.syamsuddin@gmail.com', '082291770763', 'Aktif', 'NTIxNDE3MDQwTW9oRmFocmV6YQ==', 's1teknikelektro', '2017', NULL, '', NULL, '', ''),
('Rivaldiyanto Tomayahu', '521417041', '32672df6b4457dd3144f07d8050143eb', '', '', 'Aktif', 'aW5la2tr', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Firmansyah Lahabu', '521417042', '1d46a7f13fa7709ac0512082e0578853', 'firmansyah_s1elektro2017@mahasiswa.ung.ac.id', '082321497377', 'tidak aktif', 'c2lsdmVyZmFuZzEyMzQ=', 's1teknikelektro', '2017', '92459d083c', 'Nunuk', '1999-06-22', '', ''),
('Zaenal Aripin', '521417043', 'c5d8694049e96f1aeb6d8403a34510db', '', '', 'Keluar', 'dGVlaW9r', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Moh.alfaris M.poiyo', '521417044', '58236437501da3abe47c18c12165c263', 'moh._132905_s1elektro2017@mahasiswa.ung.ac.id', '082291844863', 'Aktif', 'ZXJla2Vr', 's1teknikelektro', '2017', NULL, '', NULL, '', ''),
('Ihwaldi Haris Bowta', '521417045', '66906270a5338fb5570d8925d7b1ffd9', 'haris.bowta85@gmail.com', '08991502221', 'Aktif', 'SEFyaXMyMDI2', 's1teknikelektro', '2017', '7fbcc3969e', 'Gorontalo', '2000-05-05', 'Gorontalo', ''),
('Tri Nopriadi Aliu', '521417046', '752be70d6153af356eaff9648c00185d', '', '', 'Aktif', 'b2VraWxu', 's1teknikelektro', '2017', NULL, '', '1999-11-11', '', ''),
('Fathur Kurnia Soleman', '521417047', '62904a8af98ca37f4a0152c06edfb430', '', '', 'Keluar', 'a2lrZWVu', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Anita Abdullah', '521417048', '6ed0a6d8997038ff8d9e1bf685867c67', '', '', 'Aktif', 'a290ZWxu', 's1teknikelektro', '2017', NULL, '', '1999-12-05', '', ''),
('Safrin Hulalango', '521417049', '6ec7a0da703fd323ac69b7b650fd2160', 'safrinhulalango98@gmail.com', '082290657330', 'tidak aktif', 'OTg5ODk4', 's1teknikelektro', '2017', '8e6dd82846', 'Uabanga', '1998-06-17', 'Desa Uabanga Kecamatan Bonepantai Kabupaten Bone Bolango Provinsi Gorontalo', ''),
('Rendiwan Monoarfa', '521417050', '90b444751973418298a67b5f29028dfa', '', '', 'Aktif', 'cmVra2Vp', 's1teknikelektro', '2017', NULL, '', '2000-01-06', '', ''),
('Rivaldi Maulu', '521417051', '7c555d43ede04786748a582e74edb682', '', '', 'Aktif', 'a2tya2xl', 's1teknikelektro', '2017', NULL, '', '1999-04-07', '', ''),
('Aditya Saputra Amin', '521417052', '17e512b490d20bdbc345cad916a940b0', 'aditamin99@gmail.com', '085254459994', 'tidak aktif', 'bnRpZW9r', 's1teknikelektro', '2017', '5864493a02', 'Limboto', '1999-05-18', 'Desa molingkapoto,kabupaten,gorontalo utara', ''),
('Hardiyanto Saputra Pido', '521417053', '776f61cd1b50c12309d6f40fe73678a3', '', '', 'Aktif', 'a2tpZW5r', 's1teknikelektro', '2017', NULL, '', '1970-01-01', '', ''),
('Basrin Suleman', '521417054', '9ee71fd3bc966bc341cefd271f6af83e', '', '', 'Aktif', 'a3R0ZW5r', 's1teknikelektro', '2017', NULL, '', '1996-07-01', '', ''),
('Isnain Sarjan Mento ', '521417055', 'c9679de4677be2683505fb9871f24eed', 'isnain_s1ekektro2017@mahasiswa.ung.ac.id', '082261960482', 'tidak aktif', 'bnJlbGVr', 's1teknikelektro', '2017', '1a127ddb8e', 'Kwandang', '1998-06-28', 'Dusun Potanga,Desa Tudi, Kec.Monano Kab Gorontalo Utara', ''),
('Nuralim Kadir', '521418001', '19b68cbaf7216ae5643d9d23a02fba4d', 'nuralimkadir19@gmail.com', '082291282259', 'tidak aktif', 'MTlNQXJldDIwMDA=', 's1teknikelektro', '2018', '6eba3d13ea', 'Kab. Gorontalo', '2000-03-19', 'Dusun Helumo, Desa Limehe-Timur, Kecamatan Tabongo, Kab. Gorontalo', ''),
('Yusri Husain', '521418002', '9722d986bdcb776e35e2be9dbcc89e39', '', '', 'Aktif', 'a2tsZW5y', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Fadel Mohamad Said', '521418003', '896197b262c6eca0c3128ba437a6e148', 'fadel_s1elektro2018@mahasiswa.ung.ac.id', '0895700231000', 'tidak aktif', 'RmFkM2xNMGgxOQ==', 's1teknikelektro', '2018', 'f53eb48005', 'Gorontalo', '1999-11-19', 'Jl. PROF. DR. ALOEI SABOE\nRT  : 2\nRW : 2\nKelurahan : Dembe 2\nKecamatan : Kota Utara', ''),
('Firman Pakaya', '521418004', '39f0daf51ce835485f58deb6ae83dda2', 'firmanpakaya03@gmail.com', '082199601808', 'tidak aktif', 'S29tYW5kbzIy', 's1teknikelektro', '2018', NULL, 'GORONTALO ', '2000-03-28', 'Desa Mootinelo, Kec. Kwandang, Kab. Gorontalo Utara ', 'file/521418004-passfoto.jpg'),
('Mahmud A. Manoe', '521418005', 'eac6e6b511c2b6dd2d026a1c3fa3ec13', 'mahmud_s1elektro2018@mahasiswa.ung.ac.id', '082259920792', 'tidak aktif', 'SU1hbjEyMzQ=', 's1teknikelektro', '2018', 'f53eb48005', 'Kab. Gorontalo', '2000-02-24', 'Dusun Bunia, Desa Dulamayo Utara, Kecamatan Telaga Biru.', 'file/521418005-passfoto.jpeg'),
('Refki Setiawan Latif ', '521418006', 'dbffb2ba6fe04d337876bdcb50821ef7', 'refkisetiawan_s1elektro2018@mahasiswa.ung.ac.id', '085242791366', 'tidak aktif', 'cmVma3lsYXRpZg==', 's1teknikelektro', '2018', 'f53eb48005', 'Sumalata ', '1999-07-31', 'Desa Bulontio Barat Kecamatan Sumalata Kabupaten Gorontalo Utara ', 'file/521418006-passfoto.jpg'),
('Mohamad Rangga Eko Pratama', '521418007', '93dcefbf85a4141ef19e914257360e29', '', '', 'Aktif', 'b2tlaW50', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Indra Risaji', '521418008', '290c998ab87d0b4088725391647e02b1', 'indra22.ajii@gmail.com', '081327918894', 'tidak aktif', 'aW5kcmF6OTk5', 's1teknikelektro', '2018', 'd53ee838e9', 'Mulyoharjo ', '2000-05-16', 'Desa Mulyoharjo Kecamatan Moilong Kabupaten Banggai Provinsi Sulawesi Tengah ', ''),
('Moh. Rizky J.', '521418009', '87d0b8bee837be21edec97954826c5ba', '', '', 'Aktif', 'cm9rdGtr', 's1teknikelektro', '2018', NULL, '', '2000-04-07', '', ''),
('Mohamad Ikbal Hamidjun', '521418010', '12315d3c42657954b2d29447aadc0b5e', 'ikbalhamidjun@gmail.com', '085345683469', 'tidak aktif', 'cGVydW1haGFuMjU=', 's1teknikelektro', '2018', 'd53ee838e9', 'Monggupo', '1999-06-25', 'Desa. Monggupo, Kec. Atinggola, Kab. Gorontalo Utara', 'file/521418010-passfoto.jpg'),
('Sri Reski Amelia Adnan', '521418011', 'a57c1e041bdf8e45508742611f7d2dd1', 'srireskiamelia@gmail.com', '085175234433', 'tidak aktif', 'Qm9rcGFsMTg=', 's1teknikelektro', '2018', 'd53ee838e9', 'Bulukumba', '2001-09-18', 'Toto utara', ''),
('Akdar', '521418012', '99cd70b29c32f80d13cf057c0fbe14d2', 'akdar_s1elektro2018@mahasiswa.ung.ac.id', '081280770213', 'tidak aktif', 'dGVsZW9r', 's1teknikelektro', '2018', 'd53ee838e9', 'Kore Korea', '1998-07-04', 'Dsn.Labose, Desa Laskap, kec.malili, kab.luwu timur, prov.sulsel', ''),
('Muhammad Ams Rahul', '521418013', '9b1d96451c9e67771986825aa0cf24b1', 'amrulrahul@gmail.com', '082192683973', 'tidak aktif', 'bml0bGtr', 's1teknikelektro', '2018', 'fb4b7ab909', 'Senakin', '2000-01-13', 'Jl. Poros Sultra. Desa Harapan. Kecamatan Malili. Kabupaten Luwu Timur ', ''),
('Budianto', '521418014', 'c16d840e44835f69a1c48feabaab2cb9', 'budianto.toili.123@gmail.com', '081523958348', 'tidak aktif', 'ODc5MDU4MDU=', 's1teknikelektro', '2018', 'fb4b7ab909', 'Tirta sari', '1999-09-25', 'Desa Tirta Jaya, Kecamatan Toili, Kabupaten Banggai', ''),
('Laode Muh Nabil Ikraem Ilahude', '521418015', '5e38733c94eb87f44dff6d59f900109e', 'la_s1elektro2018@mahasiswa.ung.ac.id', '082290154067', 'tidak aktif', 'TGFvZGUyMDAw', 's1teknikelektro', '2018', 'fb4b7ab909', 'Kendari', '2000-08-02', 'BTN MEDY BRATA INDAH 1 BLOK C. NO 3\n', 'file/521418015-passfoto.jpg'),
('Nazila A. Pally', '521418016', 'afb72cdda3aa3472f60181cffd4c8735', '23nazilapally@gmail.com', '082291802386', 'tidak aktif', 'MjMwODIwMDA=', 's1teknikelektro', '2018', '14c4f7be30', 'Luwuk', '2000-08-23', '', ''),
('Ilman Fajar ', '521418017', '2263529e2efa4d9755da89ea04c8461f', 'ilman_s1elektro2018@mahasiswa.ung.ac.id', '081543163844', 'tidak aktif', 'dG9sZWtl', 's1teknikelektro', '2018', 'fb4b7ab909', 'Kintom', '2000-09-14', 'BAKUNG', 'file/521418017-passfoto.jpg'),
('Moh Ihsan Hamsa', '521418018', 'f8ce672b25a5a5f6ac1f3adcd1832109', 'iksanhamsa42@gmail.com', '6285298657508', 'tidak aktif', 'UVdlcnR5MTIzNDU2Nw==', 's1teknikelektro', '2018', NULL, 'Tamboo', '2000-02-04', 'Desa Tamboo Kec. Tilongkabila  Kab. Bone Bolango', 'file/521418018-passfoto.jpg'),
('Wahyudin Tupa', '521418019', '247772e0a6aa904bc72ffd56c888784e', '', '', 'Aktif', 'cmllZWxv', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Frilin Oktavianus Kenap', '521418020', 'dbcd79db8f67e2852e1a23db220252c1', '', '', 'Aktif', 'ZW5rZXJ0', 's1teknikelektro', '2018', NULL, '', '2000-02-10', '', ''),
('Faisal Bahtiar Hasan', '521418021', 'ed68cd012ec8ee79b6dfdaf19ab1f1a3', '', '', 'Aktif', 'dGVuZXRr', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Rahmad Harun ', '521418022', 'ea8286d28db1a95e9135249dc8d58a3a', 'rahmad_s1elektro2018@mahasiswa.ung.ac.id', '082195466053', 'Aktif', 'ZWtua2ty', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Zulkipli Ma`aruf', '521418023', 'c0b8577836d7f5b125219b5a4d53daef', '', '', 'Aktif', 'cmlsa29l', 's1teknikelektro', '2018', NULL, '', '2001-05-01', '', ''),
('Ammar A.r. Mokodompit', '521418024', '863f625ec44c0fe909632fa79da837a1', 'ammarmokodompit@gmail.com', '082187278844', 'Aktif', 'QU1hcjIwMjA=', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Rachmat Maulana Yudistira Iskandar', '521418025', '2b580cea356091532eb9708d83fc0495', '', '', 'Aktif', 'ZWlub2t0', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Aditya Hasin Mooduto', '521418026', '6b49ad09de28962d6fea13da5b1e3c76', 'aditmooduto689@gmail.com', '085395611297', 'tidak aktif', 'TU9vZHV0bzAw', 's1teknikelektro', '2018', '274eccd1e7', 'Gorontalo', '2000-11-25', 'Jl, JHON ARIO KATILI ', 'file/521418026-passfoto.jpg'),
('Nur Fahri Muamar', '521418027', '8c3ed7856205c0a8729ae625d988612a', 'fahrimuamar10@gmail.com', '082188534159', 'tidak aktif', 'U3VuZGFyaTEw', 's1teknikelektro', '2018', 'f154c17f55', 'Gorontalo', '2000-09-10', 'Kota Gorontalo', ''),
('Inal Ibrahim', '521418028', '438c8d0e4a86ba4bd39980ce789ecc60', '', '', 'Aktif', 'dHRybGVl', 's1teknikelektro', '2018', NULL, '', '2000-02-07', '', ''),
('Moh Fadli Mile', '521418029', '487e0287d5cc63281b31a30ac7966976', 'moh_s1elektro2018@mahasiswa.ung.ac.id', '082293937917', 'tidak aktif', 'RmFkbGkyMTM0', 's1teknikelektro', '2018', '645eef9c30', 'Tolitoli', '2001-06-20', '', 'file/521418029-passfoto.jpg'),
('Dandi Iwan Onumo', '521418030', '51c44c20869005a5ad90c2e0a823c988', 'dandi_s1elektro2018@mahasiswa.ung.co.id', '085240358170', 'Aktif', 'b2xpZXRy', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Mauludin', '521418031', '14dd7bdfd5785f207e88d0696d5fc124', 'mauludin1406@gmail.com', '085340656260', 'tidak aktif', 'U2FudU4xNDA2', 's1teknikelektro', '2018', '645eef9c30', 'Ndendek', '2000-06-14', 'Dusun III, Desa Panapat, Kec.Bokan Kepulauan, Kab. Banggai Laut, Sulawesi Tengah', ''),
('Muhammad Akbar Katili', '521418032', 'd950ca50426be52871dace5db5dd44a0', 'muhammad_090014_s1elektro2018@mahasiswa.ung.ac.id', '082190614848', 'tidak aktif', 'QklUVW5nMTk=', 's1teknikelektro', '2018', '645eef9c30', 'Bitung', '1999-01-19', '', 'file/521418032-passfoto.jpg'),
('Alfian Katili', '521418033', 'a7d7984f22e9c1a6919931cd3e461f06', '', '', 'Aktif', 'ZXRva2Vy', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Adi Purwanto Umar', '521418034', '10043e69e657bf5ad84164db6cf38409', 'purw4ntoum4r@gmail.com', '082349366957', 'tidak aktif', 'UkVhbDEyMzQ=', 's1teknikelektro', '2018', '645eef9c30', 'Baka', '1999-12-22', 'Desa alakasing,kecamatan peling tengah, kabupaten banggai kepulauan', ''),
('Sutiyono Hulopi', '521418035', 'b2b8bfbd3cae9363f3827053937b0369', 'sutionohulopi99@gmail.com', '085756624540', 'tidak aktif', 'WU9OTzE5OTk=', 's1teknikelektro', '2018', 'f154c17f55', 'Desa Molutabu', '1999-09-20', '', 'file/521418035-passfoto.jpg'),
('Rizaldy Deke', '521418036', '74025d62f98f0348844f908f9d2be717', '', '', 'Aktif', 'cm90aXRu', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Igo Pratama', '521418037', '26fe6773c4beae29d0a8160801cf21a9', 'igoprtama@gmail.com', '081292681528', 'tidak aktif', 'c3BpbmV2bzEyMw==', 's1teknikelektro', '2018', '36d1464083', 'Surabaya ', '2001-03-15', 'Jl Tengiri, Kec. Luwuk Selatan, kab.banggai, Sulawesi Tengah ', ''),
('Haris Pako', '521418038', 'ccbccd3c9f9df20e878411a7d7025b47', 'haris_s1elektro2018@mahasiswa.ung.ac.id', '081347928129', 'tidak aktif', 'aGFyaXM=', 's1teknikelektro', '2018', '274eccd1e7', 'Kasia', '1998-09-27', 'Desa kasia', 'file/521418038-passfoto.jpg'),
('Yusran Moha', '521418039', '00ef073b5f20c4b4d954bfb496f348e5', 'yusran_s1elektro2018@mahasiswa.ung.ac.id', '085146433806', 'tidak aktif', 'a29ybnRs', 's1teknikelektro', '2018', '92459d083c', 'Buata', '1999-05-20', 'Dusun Tombulilato, Kec Atinggola, Kab Gorontalo Utara, gorontalo', 'file/521418039-passfoto.jpg'),
('Nur Kholis', '521418040', '635d7d0b539a2d22856c10384b4a13d3', '', '', 'Aktif', 'b25rZWly', 's1teknikelektro', '2018', NULL, '', '1999-06-04', '', ''),
('Renaldi Hulukati', '521418041', '722afd9ca91b5c6a07d99b93f8ba4207', '', '', 'Aktif', 'ZW5ldHRv', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Siti Hadijah Nasution', '521418042', '2ca849bb63945ba2bbc9cfc01b2ab3bd', '', '', 'Aktif', 'ZWV0b3Rs', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Aditya Saputra Tombuku', '521418043', 'b0b66302eb7ea3e2e40038447863804a', '', '', 'Aktif', 'ZXRvZWxu', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Adhiyah Kurniawan', '521418044', 'bb0bfc438a675bda652cf2be3b940df6', 'adhiyahkurniawan@gmail.com', '082271299354', 'Aktif', 'ZWxuZWVr', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Rio matute', '521418045', 'dc780bfc0cad138a85620dfe6bf5aaa7', 'riomatute713@gmail.com', '082194916382', 'tidak aktif', 'ZWtub2x0', 's1teknikelektro', '2018', '92459d083c', 'Gorontalo', '1999-05-06', 'Desa luhu kec.Telaga', 'file/521418045-passfoto.jpg'),
('Syarifudin Ga`i', '521418046', 'a622854a635e7303efd7103271ed2157', 'syarifgai@gmail.com', '085343630289', 'Aktif', 'ZWtsa2Vy', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Moh Re Naldy Ali', '521418047', '0bb26d64663923416d8f13bbd4839ebe', '', '', 'Aktif', 'ZXRya2xl', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Moh. Fazril Abas', '521418048', '39637eac32bf8a150b9f18d3b34908c8', '', '', 'Aktif', 'bmx0a3Rr', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('I Kadek Wardana Wisnuwara', '521418049', 'c9acb31bde94c8e9241688005d607377', 'war.kmc55@gmail.com', '082320438011', 'tidak aktif', 'V2FyZGFuYTEyMDcyMDAw', 's1teknikelektro', '2018', '7fbcc3969e', 'KARYA MAKMUR', '2000-07-12', 'KARYA MAKMUR', 'file/521418049-passfoto.png'),
('Sry Nurhayati Olii', '521418050', '0f4c9ff01db58fbcc7852f25d7a0824a', 'yulanolii01@gmail.com', '085255370711', 'Aktif', 'VGVrbmlrRWxla3RybzE4', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Ayu Purnama Sari Atiki', '521418051', 'f4f0a4704b483ace408550b54aed71ae', 'ayu_s1elektro2018@mahasiswa.ung.ac.id', '082271285681', 'tidak aktif', 'QXl1SGF0YW1pMTM=', 's1teknikelektro', '2018', '7fbcc3969e', 'Gorontalo ', '2000-09-13', '', 'file/521418051-passfoto.jpg'),
('Agung Prawitno Yusuf', '521418052', 'a5b106a161df4c478ac3b0d9f2e725b0', '', '', 'Aktif', 'dHRrZWxp', 's1teknikelektro', '2018', NULL, '', '2000-06-06', '', ''),
('Andika Saputra Bengkal', '521418053', '1f95c7f4b9dee89c7687d443dd75bc43', '', '', 'Keluar', 'a2trbmVp', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Moh. Apriadi Kadullah', '521418054', '50e1f0cf47a93b2142210b352e633956', '', '', 'Aktif', 'aWtlbm90', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Aco Karim', '521418055', '98cbba1d60d76308bd98340199be0810', 'aco_s1elektro2018@mahasiswa.ung.ac.id', '085352522010', 'tidak aktif', 'RUxFSzw+dHJvMTg=', 's1teknikelektro', '2018', '274eccd1e7', 'Ondoliang', '1999-02-15', 'Desa Ondoliang', 'file/521418055-passfoto.jpg'),
('Firman Datumula', '521418056', '9fd6e60ce111cab1f11db1c27d60a5f9', '', '', 'Aktif', 'b2trZWV0', 's1teknikelektro', '2018', NULL, '', '1998-09-07', '', ''),
('Moh. Ramdhan P', '521418057', '51ef6c994f00d3c4762a06456c5c6426', 'mohramdhan_s1elektro2018@mahasiswa.ung.ac.id', '081244001167', 'tidak aktif', 'cHVzaG9w', 's1teknikelektro', '2018', '92459d083c', 'LUWUK', '1999-12-16', 'Jl. Pulau Komodo, Kel. Jole, Kec. Luwuk Selatan, Kab. Banggai, SULTENG', 'file/521418057-passfoto.jpg'),
('Agustiar Usman', '521418058', 'f53cc61b20a41d192fdbe05f875c0951', '', '', 'Aktif', 'a3RlbHRy', 's1teknikelektro', '2018', NULL, '', '1998-12-08', '', ''),
('Moh.Wahyu Djafar', '521418059', 'c1ae3d4e7be1bbf56472119004b2b14d', 'wahyudjafar8@gmail.com', '0895707948822', 'tidak aktif', 'R0FSdWRhMDQ=', 's1teknikelektro', '2018', '5864493a02', 'Gorontalo', '2000-07-04', 'JLN. PROF. DR. ALOEI SABOE', 'file/521418059-passfoto.jpg'),
('Audrizky F. R. Ode Husu', '521418060', '7960dde56059ff3b176ae4521a84a9d8', '', '', 'Aktif', 'cmtrb3Rl', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Priyatno Aji Sucipto', '521418061', 'f3cc7c8718649328d194c680af865ecf', 'priyatno_s1elektro2018@mahasiswa.ung.ac.id', '085394141761', 'Aktif', 'RGlydGEyMw==', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Ismail Hendrik', '521418062', 'b7052da9841c1037df20f547f240fe3e', '', '', 'Aktif', 'aWV0ZXRy', 's1teknikelektro', '2018', NULL, '', '2001-10-03', '', ''),
('Muhamad Rifai Damogalad', '521418063', 'f020d43f0f5a3daeaea0e4fcd0d8519e', 'muhamad_s1elektro2018@mahasiswa.ung.ac.id', '081526117532', 'tidak aktif', 'TVVIZmF5MjQwOEBA', 's1teknikelektro', '2018', '5864493a02', 'Lolak', '2000-08-24', 'Desa Lolak Kecamatan Lolak Kabupaten Bolaang Mongondow', ''),
('Faiz Farhan Mohamad', '521418064', '4c9d79866540699b0dfee6a6106e44d2', 'Faiz_s1elektro2018@mahasiswa.ac.id', '085340213551', 'tidak aktif', 'RmFpejk1MjAwMQ==', 's1teknikelektro', '2018', '98a4a6f7b7', 'Manado', '2001-05-09', 'Jln Trans Sulawesi, Desa Molingkapoto, Kec. Kwandang, Kab. Gorontalo Utara, Prof. Gorontalo', 'file/521418064-passfoto.jpeg'),
('Raplin Luneto', '521418065', 'd1818e0ee9075e64400fa2b45b9fb0d4', 'lunetoraplin05@gmail.com', '082271046622', 'Aktif', 'MTczMTUwNDg=', 's1teknikelektro', '2018', '98a4a6f7b7', '', NULL, '', ''),
('Yogi Saputra Abdulah', '521418066', 'abf031cc5c2e5a6ac1d74075165bb983', 'yogi_s1elektro2018@mahasiswa.ung.ac.id', '087844658804', 'tidak aktif', 'aWtyZXRl', 's1teknikelektro', '2018', '98a4a6f7b7', 'Gorontalo', '2000-06-09', 'Gorontalo ', 'file/521418066-passfoto.jpg'),
('Rizkiyanto Anwar', '521418067', '7e5bbffeaf704f60d9350d1cfb3021fe', '', '', 'Aktif', 'bGVvcnRr', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Mohammad Zaidan', '521418068', 'bd0511b7c5ac0ee3b31350f4bff638ee', '', '', 'Aktif', 'aW9rdGty', 's1teknikelektro', '2018', NULL, '', '1970-01-01', '', ''),
('Refki Setiawan Latif ', '521418096', 'e93f15ef5279762b1514b0d48b7a5851', 'refkisetiawan_s1elektro2018@mahasiswa.ung.ac.id', '085242791366', 'Aktif', 'cmVma2lzZXRpYXdhbg==', 's1teknikelektro', '2018', NULL, '', NULL, '', ''),
('Andi Shindy Keyshia', '521419001', 'f1d15b6fa7094e31b593987665258d9c', 'andishindy07@gmail.com', '085244167204', 'tidak aktif', 'Q0hJVFRBUE9OMjI=', 's1teknikelektro', '2019', '7fbcc3969e', 'Makassar', '2001-07-22', 'Bone Sulawesi Selatan', ''),
('Diki Ariwinoto', '521419002', 'e770813252833b286683d1005293d743', 'diki_s1elektro2019@mahasiswa.ung.ac.id', '085398804718', 'tidak aktif', 'ZGsxMjM0NTY3', 's1teknikelektro', '2019', '7fbcc3969e', 'Tirtasari', '2001-09-17', 'Tirta Jaya', 'file/521419002-passfoto.png'),
('Moh Fajar Febriansyah', '521419003', '0c8609b271764a1443a959a2a6e80bb4', 'fyasa1658@gmail.com', '082293754206', 'tidak aktif', 'MjMwMjAyRmFqYXI=', 's1teknikelektro', '2019', '6eba3d13ea', 'Luwuk', '2002-02-23', 'Jl. Transulawesi Desa boyou, Kec. Luwuk Utara, Kab. Banggai, Prov. Sulawesi Tengah ', ''),
('Nirmalasari', '521419004', 'a47607b05b5baba1043eb36f272c1275', 'nirmalasari0301@gmail.com', '082296699677', 'tidak aktif', 'aW5lbHRr', 's1teknikelektro', '2019', '6eba3d13ea', 'Lakatan', '2001-07-03', 'Desa Kinopasan ', ''),
('Fahruansah Pakaya', '521419005', '017b969b1b82d344dba3279dbda2587c', '', '089669106718', 'Aktif', 'bHRpZWtl', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Muh. Alfari Hidayat Lanasir', '521419006', '52e95acc3c3f681e67c849d659d67468', '', '', 'Aktif', 'a2Vybmlr', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Nurvita A. S. Potale', '521419007', '3cf0023f87974682ec2b46303b48de66', 'nurvita_s1elektro2019@mahasiswa.ung.ac.id', '081351886049', 'tidak aktif', 'MDIwNzAwcGl0YQ==', 's1teknikelektro', '2019', '5864493a02', 'Gorontalo', '2000-07-02', 'Jl. Sawah Besar', ''),
('Moh Faisal S. Mayasin', '521419008', '0ad9cd98abd8f07faf53f2e918d8e9a4', 'mohfaisalmayasin19@gmail.com', '082271296204', 'tidak aktif', 'ZW9sdGt0', 's1teknikelektro', '2019', '64809b0cfc', 'Pagimana', '2001-01-01', 'Desa Basabungan', 'file/521419008-passfoto.png'),
('Fathu Rahman A. Mahmud', '521419009', '54a984c3e5435b98dec656ef99e08aa0', '', '', 'Aktif', 'dGlldGty', 's1teknikelektro', '2019', NULL, '', '2001-01-04', '', ''),
('Moh Febryanto Zakaria', '521419010', '89f556ace3b22f18582b89c9a2174d05', 'febryzkria10@gmail.com', '089653343188', 'tidak aktif', 'ZmVicnl6YWthcmlhNDY=', 's1teknikelektro', '2019', '98a4a6f7b7', 'Gorontalo', '2001-02-25', '', 'file/521419010-passfoto.jpg'),
('Jusuf Mohamad Z. Tobamba', '521419011', '3fc8ec45ae31312a0e758661f2c16ec6', 'yusuftobamba13@gmail.com', '089609850040', 'tidak aktif', 'R3RsbzEyMTI=', 's1teknikelektro', '2019', '98a4a6f7b7', 'Gorontalo', '2001-06-19', 'Jl. Yusuf hasiru, Kel. Tanggikiki, Kec. Sipatana \nNo. 62', 'file/521419011-passfoto.jpeg'),
('Muchlis Alfajrin Kiayi', '521419012', '2b922228091dbbb399f64e70364cb1e9', 'afankiay87@gmail.com', '081244113332', 'tidak aktif', 'YWZhbjA4MDc=', 's1teknikelektro', '2019', '8e6dd82846', 'Gorontalo', '2001-07-08', 'Jl. Farid Liputo, Kel. Bugis, Kec. Dumbo Raya, Kota Gorontalo, Gorontalo, Indonesia', ''),
('Yusran Rahim', '521419013', 'ae0b615584cc94c7fd6c17f12dc33f0b', 'yusran_s1elektro2019@mahasiswa.ung.ac.id', '082188755147', 'tidak aktif', 'R29yb250YWxvMTIz', 's1teknikelektro', '2019', '637dfbddee', 'Tibawa', '2000-07-19', 'Dusun Yilantala', 'file/521419013-passfoto.jpg'),
('Rezki Patingki', '521419014', '6cf0a47758b96aa6d12345e4f55378f5', '', '', 'Aktif', 'b2lrbGt0', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Mohamad Taufik Pou', '521419015', '48a67c22e6d4e37f5d091c26f9631ecf', 'mohamad_s1elektro2019@mahasiswa.ung.ac.id', '085246698386', 'tidak aktif', 'UmxzUG91MTc=', 's1teknikelektro', '2019', '92459d083c', 'Deme 1', '2000-09-17', 'Dusun Bahari, Desa Koluwoka, Kecamatan Sumalata Timur, Kabupaten Gorontalo Utara, Provinsi Gorontalo', 'file/521419015-passfoto.png'),
('Abd. Rais R. Gani', '521419016', '94c10264c604483c4df0de302b2764cc', 'abd.rais_s1elektro2019@mahasiswa.ung.ac.id', '082290111915', 'tidak aktif', 'MTIzNDU2Nzg=', 's1teknikelektro', '2019', '92459d083c', 'Pomayagon', '1999-06-21', 'Desa : Lamadong 1\nKecamatan : momunu', 'file/521419016-passfoto.png'),
('Muhammad Alim', '521419017', 'ae4c94594a960ae5e25645b8ea0ae2ed', 'muhammad_s1elektro2019@mahasiswa.ung.ac.id', '082290550462', 'tidak aktif', 'YWxsLnB1dHJhMDM=', 's1teknikelektro', '2019', '92459d083c', 'Kusambi', '2001-07-03', '', 'file/521419017-passfoto.png'),
('Aditya Wahyu Saputro', '521419018', '5899d523712ba16e59d750165a993a14', 'adityawahyu5000@gmail.com', '082293975022', 'Aktif', 'YWRpdHlhd2FoeXU1MDIy', 's1teknikelektro', '2019', '36d1464083', 'Jombang', '2002-04-03', 'Jl. BLOK C', ''),
('Abdul Muthalib Maku', '521419019', 'ae0b615584cc94c7fd6c17f12dc33f0b', 'abdul_s1elektro2019@mahasiswa.ung.ac.id', '081356312540', 'tidak aktif', 'R29yb250YWxvMTIz', 's1teknikelektro', '2019', 'f154c17f55', 'Gorontalo ', '2001-07-09', '', 'file/521419019-passfoto.jpeg'),
('Yudi Anas Sahri', '521419020', '8a8d9457914fe8804f7c60e8ff80264b', 'yudianas33@gmail.com', '082291807346', 'tidak aktif', 'dGVrbmlrVU5HMjAxOQ==', 's1teknikelektro', '2019', 'f154c17f55', 'Tohitisari', '1999-12-18', 'Tohitisari', 'file/521419020-passfoto.png'),
('Ahmad Tamzis', '521419021', '145288412f6c346048d86504eb3e323e', 'ahmad_s1elektro2019@mahasiswa.ung.ac.id', '082259519153', 'tidak aktif', 'MTUwMzIwMDE=', 's1teknikelektro', '2019', '14c4f7be30', 'Rantau Jaya', '2001-03-15', 'Rantau Jaya, kecamatan Simpang Raya, kabupaten Banggai, Sulawesi Tengah', 'file/521419021-passfoto.png'),
('Juan Sebastian Umpele', '521419022', '8090caad4360166d727178142ae70010', 'juan_s1elektro2019@mahasiswa.ung.ac.id', '082395359855', 'tidak aktif', 'anVhbjUyMTQxOTAyMg==', 's1teknikelektro', '2019', '14c4f7be30', 'Sinsingon', '2001-09-29', 'DUSUN IV, DESA SINSINGON BARAT, KEC. PASSI TIMUR', 'file/521419022-passfoto.png'),
('Shahfaldi Rizki Gobel', '521419023', '3234c14a37991c937864f4ed138d34db', 'shahfaldi_s1elektro2019@mahasiswa.ung.ac.id', '089668711622', 'tidak aktif', 'U3lhdmFsZGlHb2JlbDI0', 's1teknikelektro', '2019', '5864493a02', 'GORONTALO', '2001-09-24', 'JL. Thayeb Moh. Gobel. Kel. Bulotada’a Barat Kec. Sipatana Kota Gorontalo', 'file/521419023-passfoto.png'),
('Aldiansyah Nurkamiden', '521419024', '6a6819a1db2593e9c704f83283eb7ece', 'aldiigmm@gmail.com', '085299191646', 'tidak aktif', 'bW9sb3BhdG9kdTMyMQ==', 's1teknikelektro', '2019', '637dfbddee', 'Gorontalo ', '2000-05-27', '', 'file/521419024-passfoto.jpg'),
('Fandi Honi', '521419025', 'ddc9f85b1a7153d21e2e5ad356f89423', 'fandihoni@gmail.com', '082187151370', 'tidak aktif', 'bHRlcm9r', 's1teknikelektro', '2019', '637dfbddee', 'PALU', '2000-10-30', 'Alamat : DESA LIANG\nRT/RW : 000/000\nKel/Desa : LIANG\nKecamatan : LIANG', 'file/521419025-passfoto.png'),
('Wirawan', '521419026', '3762d201e5cdf70b90490c35cd3b24b2', '', '', 'Aktif', 'aWtldGty', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Saprin Pakaya', '521419027', '4ecc55695ffad05586e90873948bb179', 'saprin_1elektro2019@mahasiswa.ung.ac.id', '085240667124', 'tidak aktif', 'aWVsdG9l', 's1teknikelektro', '2019', '637dfbddee', 'Gio', '1998-05-13', 'Dusun molamahu, Desa Lomuli', 'file/521419027-passfoto.jpeg'),
('Darul Salam', '521419028', 'fb38022264cb9d2867ffc6888b1e7e5f', 'darul_s1elektro2019@mahasiswa.ung.ac.id', '082296432916', 'Aktif', 'cm5ldGxr', 's1teknikelektro', '2019', '637dfbddee', 'Baebunta', '1999-11-18', '', ''),
('Muhamad Wiguna Ali Fiqri', '521419029', 'e4be9561de6aae2abdd7abaadb86ad8d', '', '', 'Aktif', 'a2xrb2ll', 's1teknikelektro', '2019', NULL, '', '2001-07-04', '', ''),
('Rayhandi Yahya Usman', '521419030', '4cb67bc51bf530f3707e2f52c92d5012', '', '', 'Keluar', 'ZXRpb2ty', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Mohammad Qasim R. Loleh', '521419031', 'b28220e5544af29b3f0da0bbc1b9c361', '', '', 'Aktif', 'dHRya25l', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Jalaluddin Akbar', '521419032', 'bfcd955a05e3124485ca5fcd7f630b10', 'jalaluddin_s1elektro2019@mahasiswa.ung.ac.id', '082252613800', 'tidak aktif', 'bm9pa2Vr', 's1teknikelektro', '2019', '7bf1f8337d', 'Raha', '2000-08-08', 'Jalan Made Sabara', 'file/521419032-passfoto.jpeg'),
('Gilang Ramadhan', '521419033', 'fda92230cf45cd8f4792016ce33fedb0', '', '', 'Aktif', 'dGVlZWlr', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Riswan S. Banti', '521419034', 'eb11c78d3154dd76dec129b1c8e108f1', '', '', 'Keluar', 'dG9rbGtl', 's1teknikelektro', '2019', NULL, '', '2001-02-08', '', ''),
('Marhaban Aswadi', '521419035', '7648c81ad0aedce0bb80850a0ecc3e6d', '', '', 'Aktif', 'ZXRydG9l', 's1teknikelektro', '2019', NULL, '', '2001-12-05', '', ''),
('Moh.dwi Adji', '521419036', 'fa460285dbae47df4fae053690d68a27', '', '', 'Aktif', 'dGVlbmtp', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Anggun Gunawan', '521419037', 'ae0b615584cc94c7fd6c17f12dc33f0b', 'anggun_s1elektro2019@mahasiswa.ung.ac.id', '081354889677', 'tidak aktif', 'R29yb250YWxvMTIz', 's1teknikelektro', '2019', '7bf1f8337d', 'Karya Agung', '2001-08-21', 'Karya Agung', 'file/521419037-passfoto.jpeg'),
('I Putu Mariawan', '521419038', '619478ccbc376eb2e72d4cac43ac57b0', 'mariawan_s1elektro2019@mahasiswa.ung.ac.id', '082296737270', 'tidak aktif', 'TWFyaWF3YW4wNw==', 's1teknikelektro', '2019', '7bf1f8337d', 'Sarimurni', '1999-02-11', 'Desa Sari Murni, Kec. Randangan, Kab. Pohuwato, Provinsi Gorontalo', 'file/521419038-passfoto.jpg'),
('Nurul Maghfira', '521419039', 'b07f618c318b52341462493e8c5eabe7', '', '', 'Aktif', 'ZXJ0ZWxr', 's1teknikelektro', '2019', NULL, '', '1970-01-01', '', ''),
('Ketut Ngurah', '521419040', '7d20b09fc66df5efae7d95eee2c3e1a2', '', '', 'Aktif', 'a2xraWVu', 's1teknikelektro', '2019', NULL, '', '2001-11-06', '', ''),
('Is Siswanto Sy Lamato', '521419041', 'd15725a35a6db05f84dd3ffe37731ea9', 'issiswantosylamato26@gmail.com', '085292535462', 'tidak aktif', 'bnRpa3Rr', 's1teknikelektro', '2019', '5864493a02', 'Kab Gorontalo', '2000-06-26', '', 'file/521419041-passfoto.jpg'),
('Mohamad Isro Eddy', '521420001', '928edd6a796fc82af02f56e0b92b7748', 'eddyisro@gmail.com', '082296531871', 'tidak aktif', 'aXNyb2VkZHkyMDAy', 's1teknikelektro', '2020', '64809b0cfc', 'Gorontalo', '2002-10-04', 'dusun Rejo mulyo, kecamatan Taluditi, Kab.Pohuwato', 'file/521420001-passfoto.jpg'),
('Rahmat Husa', '521420002', 'ad9b1f2491d6bbbf1054757295c42ee8', 'rahmat_s1elektro@mahasiswa.ung.ac.id', '0895401221155', 'Aktif', 'a2tlbHJp', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Moh. Rafli A. Bolilio ', '521420003', '78501c44798301781bb82962fc91d30c', 'raflybolilio@gmail.com', '082271500393', 'tidak aktif', 'cmFmbGlib2xpbGlvMjkwMjAw', 's1teknikelektro', '2020', '64809b0cfc', 'KAB. BONE BOLANGO', '2000-02-29', '', 'file/521420003-passfoto.jpg'),
('Hijratun A. Samadi', '521420004', '66a12d2a80045c99b4f0f9b9a81c02aa', 'hijrasmdi@gmail.com', '082192932643', 'Aktif', 'SW5jYXJTMTIwMjA=', 's1teknikelektro', '2020', '5864493a02', 'Manado', '2002-04-20', '', ''),
('Moh Rifaldi', '521420005', '57a48f1f6ca2dea806a972081a990d2a', 'moh.rifaldi072002@gmail.com', '085823960892', 'tidak aktif', 'bmtrdGxl', 's1teknikelektro', '2020', '1a127ddb8e', 'Sidoharjo, Kec. Moilong, Kab. Banggai', '2002-07-15', 'Sidoharjo, Kec. Moilong, Kab. Banggai', 'file/521420005-passfoto.jpg'),
('Mohamad Aguslianto Sanu', '521420006', 'fdbb51437515292f10606a323db96952', '', '', 'Aktif', 'ZW90ZWVr', 's1teknikelektro', '2020', NULL, '', '1970-01-01', '', ''),
('Abdul Mufli S. Mento ', '521420007', '5879d47486d91dd48caba026adfc2fce', 'abdulmufli1234@gmail.com', '082292601079', 'Aktif', 'VU5HZ3VsMjA=', 's1teknikelektro', '2020', '36d1464083', 'Gorontalo', '2002-08-10', 'Jln. Trans Sulawesi, Desa Mokonowu, Kec. Monano, Kab. Gorontalo Utara, Provinsi Gorontalo', ''),
('Elisabeth Sihite', '521420008', 'b32b0afb245b4e8892fecb5476f01613', '', '', 'Aktif', 'dGVucmlr', 's1teknikelektro', '2020', NULL, '', '1970-01-01', '', ''),
('Anisa Amalia Pontoan', '521420009', 'ad245b2f2bb023cf01c34265517c69ae', '', '', 'Aktif', 'dG5la29r', 's1teknikelektro', '2020', NULL, '', '2002-06-10', '', ''),
('I Gusti Komang Apriadi', '521420010', '53e66890fd732c34846496b6475c7673', 'igusti_s1elektro@mahasiswa.ung.ac.id', '085796416391', 'tidak aktif', 'bmtraXRy', 's1teknikelektro', '2020', '7fbcc3969e', 'MAKAPA', '2001-04-04', 'MAKAPA', 'file/521420010-passfoto.png'),
('Aditya Kisman', '521420011', '3d1b3034525beb4de3571878388819b1', 'aditya_s1elektro@mahasiswa.ung.ac.id', '085216525032', 'tidak aktif', 'QURpdHlhLi5jb20=', 's1teknikelektro', '2020', '6eba3d13ea', 'Kabupaten Toli-toli', '2002-11-23', 'Dusun kekot, Desa Ginunggung, Kec Galang', 'file/521420011-passfoto.jpeg'),
('Izhar Kadir Saputra', '521420012', '8fdeb99e54c2780e83585305b2a40ada', 'vizhalpoetra@gmail.com', '082290316560', 'tidak aktif', 'MTExODIwMDE=', 's1teknikelektro', '2020', '5864493a02', 'WATONDO', '2001-11-18', 'DESA LIANOSA', 'file/521420012-passfoto.jpg'),
('Mohamad Fauzan Buna', '521420013', '196d212a7281f38cd81752dc31e83caa', '', '', 'Aktif', 'ZXJ0a29r', 's1teknikelektro', '2020', NULL, '', '2001-08-05', '', ''),
('Ariyani Djafar', '521420014', '2863c3f3d2a1ef72f303dfb9841653d3', 'ariyanidjafar2@gmail.com', '081222861253', 'tidak aktif', 'ZWxldHJp', 's1teknikelektro', '2020', '5864493a02', 'Tolango', '2001-12-08', 'Desa Tolango,kecamatan anggrek,kabupaten gorontalo utara,', 'file/521420014-passfoto.png'),
('Hamdan Abas', '521420015', '312a4fa10a4ca9e1de63faecb37d9deb', '', '', 'Aktif', 'bHJrb3R0', 's1teknikelektro', '2020', NULL, '', '2001-06-12', '', ''),
('Muh. Arya Sumarno', '521420016', 'f540e44c794685010cb927a980d574a6', 'aryasumarno16@gmail.com', '085298308591', 'Aktif', 'MTExMjEz', 's1teknikelektro', '2020', '1a127ddb8e', '', NULL, '', ''),
('Stella Maria Rontopaly', '521420017', '4cb916ae554ccbde0f45ead903fb9c86', 'rontopalystella@gmail.com', '082295009690', 'Aktif', 'bWFrOG00NTM=', 's1teknikelektro', '2020', '36d1464083', 'Gorontalo', '2002-09-28', '', ''),
('Tyo Sulistio S. Madjowa', '521420018', '5ad7497cdeac00e5748cac830755109a', 'tiomadjowa12345@gmail.com', '0895330483819', 'tidak aktif', 'VGlvTWFkam93YTE3Mg==', 's1teknikelektro', '2020', '92459d083c', 'TOLI-TOLI', '2002-06-17', 'Desa Tinelo, Kec. Tilango, Kab. Gorontalo', 'file/521420018-passfoto.png'),
('Rival Septian Kadir', '521420019', 'ebcc4f136c85a7fc1a76441f2d6e59a9', 'rriivvaall07@gmail.com', '085215178313', 'tidak aktif', 'MTAwOTIwMDE=', 's1teknikelektro', '2020', '6eba3d13ea', 'Kab. Gorontalo', '2001-09-10', '', 'file/521420019-passfoto.jpeg'),
('Moh Ramadhan Pakaya', '521420020', 'e5995389d9399268f9cd5a7e762d7fc9', '', '', 'Aktif', 'dGl0ZXJr', 's1teknikelektro', '2020', NULL, '', '1970-01-01', '', ''),
('Qais Mohamad', '521420021', '9d5c08f3b84a9a8a1fab8b5c636c78f5', 'qaismuhammad38@gmail.com', '089668662275', 'Aktif', 'dHRva25r', 's1teknikelektro', '2020', '6eba3d13ea', 'Tilamuta', '2003-04-22', '', ''),
('Moh. Fitran Kadir', '521420022', 'a3fee8b376878731549b20c5b47f5c7a', 'kadirmohfitran@gmail.com', '085299438139', 'tidak aktif', 'ZXRrZW5r', 's1teknikelektro', '2020', '36d1464083', 'Anggrek', '2002-05-04', 'Dusun Mutiara, Desa Tolango, Kec. Anggrek', 'file/521420022-passfoto.jpg'),
('Suhartin Rauf', '521420023', '8b0a36e3b6e7e5bf9ae2e3a19254fd17', 'suhartinrauf26@gmail.com', '082396611570', 'Aktif', 'ZGluZGFyYXVmMjAwMQ==', 's1teknikelektro', '2020', '98a4a6f7b7', '', NULL, '', ''),
('Moh Alpi Paudi', '521420024', 'bd8ce29305d2a6529caa449ae8a90079', 'alpis1elktro@gemail.com', '082291458615', 'Aktif', 'ZWt0b3Jl', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Yoshua Stephanus Bulampia', '521420025', 'c9e52fa28c5592c28c74711c5a55e792', 'joshuabulampia16@gmail.com', '082197030528', 'Aktif', 'NTIxNDIwMDI1MTYwOTIwMDI=', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Nur Tahaja Ibrahim', '521420026', '49901dd7289b331d92139dd7a3f6e6b4', 'ajabrhm@gmail.com', '081341599748', 'tidak aktif', 'VGFoYWphMTIz', 's1teknikelektro', '2020', '36d1464083', 'Gorontalo', '2001-09-22', 'Jl. Jalaludin Tantu', 'file/521420026-passfoto.jpg'),
('Zul Fikrah Arifuddin', '521420027', 'a6523ba775167aed4ad7e328325b36d2', 'zul_s1elektro2020@mahasiswa.ung.ac.id', '085231449764', 'tidak aktif', 'cGlsbGFhYTIzdW5n', 's1teknikelektro', '2020', '5864493a02', 'Watampone', '2002-03-23', 'Jl. Lapawawoi K.R. Sigeri', 'file/521420027-passfoto.jpg'),
('Bagus Wijaya', '521420028', '6faad6c9f2d6f1a704e08dccb9fcfa3c', 'baguswijaya018@gmail.com', '085217352653', 'tidak aktif', 'ZWVua3R0', 's1teknikelektro', '2020', '7fbcc3969e', 'Palembang', '2001-07-01', 'Dusun Mutiara', 'file/521420028-passfoto.jpg'),
('Fajialis Akbar Mile', '521420029', '916c37972e6a1472179e2f763610d6fe', 'fajialismile@gmail.com', '081354764634', 'Aktif', 'ZmFqaWFsaXMxNA==', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Mohammad Rifki Iti', '521420030', '78d6c825b636a0f7230024fd1420635a', 'mohamadrifkiiti@gmail.com', '089630807073', 'tidak aktif', 'NTIxNDIwMDMwMDMwMzIwMDI=', 's1teknikelektro', '2020', '6eba3d13ea', 'Dulupi', '2002-03-03', 'Desa juria, kec. Bilato, kab. Gorontalo ', 'file/521420030-passfoto.jpg'),
('Asep Wijayadi', '521420031', 'fc5c171142aec7a60aa51b2658d7c4b6', '', '', 'Aktif', 'dG5rZXJr', 's1teknikelektro', '2020', NULL, '', '2002-02-09', '', ''),
('Sahrul Duhi', '521420032', '508ebd319ed1e12565d2981faf53757a', 'sahrulduhi59@gmail.com', '0895416172361', 'tidak aktif', 'c2FocnVsMDYxMDIwMDI=', 's1teknikelektro', '2020', '7fbcc3969e', 'Bohotokong', '2002-10-06', 'Desa BOHOTOKONG', 'file/521420032-passfoto.png'),
('Yulanda S. Ishak', '521420033', '55d0e1c17f5c15e21a276bb0b986184e', 'ishakyulanda@gmail.com', '082319528810', 'tidak aktif', 'NTIxNDIwMDMzeXVs', 's1teknikelektro', '2020', 'f154c17f55', 'gorontalo', '2003-03-17', 'desa momala, kec. dungaliyo, kab. gorontalo', 'file/521420033-passfoto.png'),
('Enrico Annelka', '521420034', 'f16db1173e318cfa5619ca33f0e6ad24', 'enricoannelka27@gmail.com', '082215631983', 'Aktif', 'ZW5yaWNvMjcxMjAx', 's1teknikelektro', '2020', '36d1464083', 'Bandung', '2001-12-27', 'Komp.margahayu raya blok G2 no 45 bandung', ''),
('Sapril', '521420035', 'b55bcacc53a633b612bbff939f129be1', 'Saprilhasna@gmail.com', '085696571668', 'tidak aktif', 'a3JpbmVv', 's1teknikelektro', '2020', '7bf1f8337d', 'BONEBALANTAK', '2002-02-02', 'BONEBALANTAK', 'file/521420035-passfoto.jpg'),
('Kurniawan Ali Male', '521420036', '356dfb40c44388b72b47b8ec2b07dd19', 'kurniawanmale30@gmail.com', '082290082633', 'tidak aktif', 'd2F3YW4zMDA2MDM=', 's1teknikelektro', '2020', '7bf1f8337d', 'GORONTALO', '2003-06-30', 'Desa,ngofagita.kecamatan,malifut.kabupaten ,halmahera utara. provinsi, maluku utara', 'file/521420036-passfoto.jpg'),
('Fahri Hardiansyah Laite', '521420037', '8250f6739552863a769343274d365d47', 'fahrihardiansyah49@gmail.com', '082292634279', 'Aktif', 'NTIxNDIwMDM3MTgwNDIwMDA=', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Reynaldi Polingala', '521420038', '971e6fab10b6f46c4a0b85c292775638', 'reynaldipolingala14@gmail.com', '087735037247', 'tidak aktif', 'dG9saWVr', 's1teknikelektro', '2020', '1a127ddb8e', 'Kabupaten Gorontalo', '2002-04-14', 'Dusun 1 desa Ilotidea', 'file/521420038-passfoto.jpg'),
('Yohanes Daud', '521420039', '10aee346230412eaaac72ed342b25f27', 'yohanes_s1elektro@mahasiswa.ung.ac.id', '082293929548', 'Aktif', 'eW9oYW5lc2RhdWQzMjE=', 's1teknikelektro', '2020', '36d1464083', 'Gorontalo ', '2001-04-05', 'Dusun: Tabongo\nDesa: Polohungo\nKecamatan: Tolangohula', ''),
('Syahril Antu', '521420040', 'b2a72ed4177ff09930caddb5b96651bb', 'syahril.4ntu@gmail.com', '085346072823', 'Aktif', 'TmFuaW1vMDI=', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Arnol Kusmin', '521420041', 'fa23f1f5dd24694d53bbcbc281178c8e', '', '', 'Aktif', 'ZWt0ZXJp', 's1teknikelektro', '2020', NULL, '', '1970-01-01', '', ''),
('Nopri Jamaludin', '521420042', '564ebd2aecd8ecd998e5eab8605a179d', '', '', 'Aktif', 'aXJrdHRl', 's1teknikelektro', '2020', NULL, '', '2000-10-11', '', ''),
('Fadjril Bahtiar Hunou', '521420043', '08e8a318d556558a8699fb4ed1fc253e', 'amangaspar7@gmail.com', '085213076278', 'Aktif', 'NTIxNDIwMDQzMDA3', 's1teknikelektro', '2020', NULL, '', NULL, '', ''),
('Mohamad Aswin Tampi', '521421001', '1a6b0f5b17b007cc716e855de6764b33', 'mohaswintampi001@gmail.com', '082291637556', 'tidak aktif', 'bW9oYW1hZGFzd2ludGFtcGkyMDAz', 's1teknikelektro', '2021', '64809b0cfc', 'KAB. Bolaang Mongondow Utara', '2003-08-04', 'Boroko,kec. Kaidipang kab. Bolaang mongondow Utara ', 'file/521421001-passfoto.jpg'),
('Wahyu Nurhidayat Halid', '521421002', '186f537f8628b52bdb1746830929873e', '', '', 'Aktif', 'b2t0cmtl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Mohamad Syukur Ahmad', '521421003', 'f3925b144b56b3c03d1d0b3ae09c586f', 'mohamadsyukurahmad@gmail.com', '082196697385', 'Aktif', 'c3l1a3VyMTcyNQ==', 's1teknikelektro', '2021', '64809b0cfc', 'Gorontalo', '2003-10-25', 'JL. MAKASSAR, PERUM. PLN NO. 21', ''),
('Cristian Manggo', '521421004', '8b4ccbd133b1301654a9ff1646bc5256', '', '081526046628', 'Aktif', 'a3RlbG5r', 's1teknikelektro', '2021', '64809b0cfc', 'Matali Baru ', '2002-04-24', '', ''),
('Abdul Hakim Chaniago', '521421005', '3f2c6753a8c0b3301a66e71b2ce8835a', '', '', 'Aktif', 'ZWVpa2Vv', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Mohamad Anan Djafar', '521421006', '7d06fd99011458829f893beb585fd2bb', 'anandjafar07@gmail.com', '089653226827', 'Aktif', 'YW5hbjI4MDk=', 's1teknikelektro', '2021', '6eba3d13ea', 'kab. gorontalo', '2002-09-28', 'dusun 1, Taluhu Bongo', ''),
('Selvana Y Umar', '521421007', '7f749850ff99058476e76d6b09a51b3f', 'selvanayumar@gmail.com', '082293495234', 'Aktif', 'REFuc2hlbDA3', 's1teknikelektro', '2021', '36d1464083', 'Buol Sulawesi Tengah ', '2003-09-26', '', ''),
('Muh.ma`ruf', '521421008', 'ef658435c2dfbf5a3f8d7329974f9cba', 'marufmuh776@gmail.com', '085255424160', 'Aktif', 'RElrYW1wdXM0Ly8=', 's1teknikelektro', '2021', NULL, '', NULL, '', ''),
('Valent Anggel Darise', '521421009', '6623a63db6698289e4040c6a064f0905', '', '', 'Aktif', 'a2t0aWxl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Indra Nugraha', '521421010', '6dae36de88e484df2c75ce81ed211e1d', '', '', 'Aktif', 'ZXJ0dG9r', 's1teknikelektro', '2021', NULL, '', '2002-05-01', '', ''),
('Mohamad Ryan Noor Sahidu', '521421011', 'a54566df6f809b97ff3dc16b5c57da54', 'ryansahidu60@gmail.com', '082271316562', 'Aktif', 'UnlhblNhaGlkdTE0', 's1teknikelektro', '2021', '14c4f7be30', 'Gorontalo', '2003-08-14', 'Dusun Iloponu', ''),
('Halid Santoso', '521421012', '2c4352919f2d8a67afa61e07465e916a', 'halidsantoso576@gmail.com', '081703415984', 'Aktif', 'b2trcmlr', 's1teknikelektro', '2021', '14c4f7be30', 'gorontalo ', '2002-09-23', '', ''),
('Moh. Farhan Pakaya', '521421013', '348505a104ea00d1ee3d4eac13d795f3', 'farhanpakaya08@gmail.com', '082291151614', 'Aktif', 'cGFrYXlhMDkwODAy', 's1teknikelektro', '2021', '14c4f7be30', 'Sogitia', '2002-08-09', 'Sogitia', ''),
('Hariyanto Eka Putra', '521421014', 'e291ae0817e4328992ceb2278e973910', '', '082296327069', 'Aktif', 'dGV0b2xp', 's1teknikelektro', '2021', '14c4f7be30', 'Tahuna ', '2003-06-17', 'Kampung Likuang ', ''),
('Fauzi Riyanto Hasiru', '521421015', '8628c37a7f49c5ca0e0d7e3772f81c08', 'fauzihasiru10@gmail.com', '082393214825', 'Aktif', 'RmlraTcz', 's1teknikelektro', '2021', '14c4f7be30', 'Palu', '2003-10-13', '', ''),
('Novaliyanto Giasi', '521421016', 'c8ead2126d0fc66b9e7c897f06f98cab', 'novalgiasi02@gmail.com', '087815690083', 'Aktif', 'Tm5vdmFsR2dpYXNpMjE=', 's1teknikelektro', '2021', 'f53eb48005', 'Tontayuo', '2002-11-30', 'jl.madura-liluwo-kota tengah-kota gorontalo', ''),
('Supriyandi Hasan', '521421017', '48917620eae9fcc313f7cb314758c8fa', '', '', 'Aktif', 'ZWV0a2ly', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Randi Aditiya', '521421018', '097dad1f166ba3770d4d8af9515d5d34', '', '', 'Aktif', 'aWtrb3Rl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Fahrully A. Kama', '521421019', '0be18c303a5f24e06b209c91aa574d02', '', '', 'Aktif', 'ZWl0ZXRl', 's1teknikelektro', '2021', NULL, '', '2002-02-02', '', ''),
('Ramdan Bennovry Ali', '521421020', '2c9264a41bef2abcb6d5f207cb8a1727', '', '', 'Aktif', 'ZW5rcml0', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Fahmi', '521421021', '470a0237abd2b10c24874030f7475b63', '', '', 'Aktif', 'b2lrbG5r', 's1teknikelektro', '2021', '274eccd1e7', '', '1970-01-01', '', ''),
('Rahmatullah Saud', '521421022', '88fe5e4949c1a7af4d104aaa07cf6071', 'rahmatullahsaud939@gmail.com', '082211374048', 'Aktif', 'cmlvdGtl', 's1teknikelektro', '2021', '274eccd1e7', 'MOLOHU', '2003-03-23', 'DUSUN BATU KERUJUK', ''),
('Sugianto Saputra', '521421023', 'af3e1a3f31fba2fa497adf98f4b3bcbe', 'sugiantosaputra294@gmail.com', '082134902762', 'tidak aktif', 'c3VnaWFudG8xMjA0MjAwMg==', 's1teknikelektro', '2021', '274eccd1e7', 'Desa Pinamula', '2002-04-12', 'Desa Pinamula', 'file/521421023-passfoto.png');
INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('Moh. Alwy K. Rachman', '521421024', '1af95a72628aba51aaca684519be413d', 'alwyrachman10@gmail.coml', '082121561821', 'Aktif', 'UmFjaG1hbjEyMzQ=', 's1teknikelektro', '2021', 'f154c17f55', 'Gorontalo', '2002-05-10', 'Jl. B. Kamboja, Kel. Hanga-hanga 1, Kec. Luwuk Selatan, Kab. Banggai, Prov. Sulawesi Tengah', 'file/521421024-passfoto.jpg'),
('Syahdan Husain', '521421025', 'feecadc6093dff08fa0f326ec526ae74', 'syahanhsn17@gmail.com', '085256682002', 'Aktif', 'a29lcml0', 's1teknikelektro', '2021', '36d1464083', 'Makassar', '2023-01-07', 'JL. PROF. DR. H. HASAN ABAS NUSI KEL. HUANGOBOTU KEC. DUNGUNGI KOTA GORONTALO', ''),
('Mariska Palakum', '521421026', '161ade56d1f4265a51167acd595ab7c6', 'mariskapalakum@gmail.com', '085157228540', 'Aktif', 'TWEyMXNrYTpwYWxha3VNXzAyMDIyMQ==', 's1teknikelektro', '2021', '5864493a02', 'Desa Nunuk', '2002-02-21', '', ''),
('Bergi Arma Yoga', '521421027', '3ef06dc3cf16461027cfe3639e74e371', '', '', 'Aktif', 'ZXRrZWtp', 's1teknikelektro', '2021', NULL, '', '2002-03-09', '', ''),
('Viki Mohamad Hamzah', '521421028', '4ad122e9ff15e4f634eb854556da1045', '', '', 'Aktif', 'dGlsa2Vl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Chairuddin Antula', '521421029', 'ef82a1198e234082ad5238f3dcfe23f0', 'chairuddin_s1elektro@mahasiswa.ung.ac.id', '0895806024038', 'Aktif', 'bGVpcm9r', 's1teknikelektro', '2021', NULL, '', NULL, '', ''),
('Andri Tangahu', '521421030', 'c447a5c5ff4b971a6bcfac64636fc07c', 'andritangahu05@gmail.com', '082290273586', 'Aktif', 'NTIxNDIxMDMwMDcwNjIwMDE=', 's1teknikelektro', '2021', '645eef9c30', 'Buata', '2001-07-06', 'Dusun Posono', 'file/521421030-passfoto.jpg'),
('Zulkifli Kamumu', '521421031', 'c0e93d26eee531b3d68902c2ebbfb337', '', '', 'Aktif', 'b2VubGll', 's1teknikelektro', '2021', NULL, '', '2002-01-07', '', ''),
('Iran Zaini', '521421032', '8d5ed3794baee7d3d04fcb77c803a074', '', '', 'Aktif', 'a2Vya2Vs', 's1teknikelektro', '2021', NULL, '', '2001-02-09', '', ''),
('Fikri Mohamad Sabaya', '521421033', 'e49e38bcc4ef1a8eaf50a289919ab0bc', 'fikrisabaya97@gmail.com', '085823004843', 'tidak aktif', 'ZmlrcmlzYWJheWE=', 's1teknikelektro', '2021', '36d1464083', 'Gorontalo', '2003-07-29', 'Jl.Moh Yamin 3', 'file/521421033-passfoto.png'),
('Ardianto Pasune', '521421034', 'bb5046cc3bf07c7585370b15c5d38587', '', '', 'Aktif', 'dGtuaXRr', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Mohamad Rafik Y. Rahim', '521421035', '8a2398b93040350997f10db11b74f459', '', '082151020101', 'Aktif', 'dGtrdHJr', 's1teknikelektro', '2021', '7fbcc3969e', 'Kec. Paguyaman prov Gorontalo', '2002-09-19', 'Desa wonggahu, Kecamatan Paguyaman, kebupaten boalemo, profinsi gorontalo', ''),
('Fajar S. Biya', '521421036', '6b0336f301953d2224287cee26b8b084', '', '', 'Aktif', 'a3RvZWtl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Riksal Aminullah ', '521421037', 'd4711e84531fcc1dd2b7b2b1f3fbf007', 'riksalaminullah06gmail.com', '085241234371', 'tidak aktif', 'VEVrbmlrMjAwMw==', 's1teknikelektro', '2021', '637dfbddee', 'Longkoga Barat', '2003-09-12', 'Longkoga Barat ', 'file/521421037-passfoto.jpeg'),
('Rahman Ishak ', '521421038', '82a71d5a4e44f5b1e47a69c66453f49c', 'rahmanishak121200@gmail.com', '082293739981', 'Aktif', 'cmFobWFuMTI=', 's1teknikelektro', '2021', 'f154c17f55', 'potanga', '2000-12-12', 'Desa Bohulo, Kec. Biau, Kab. Gorontalo Utara', ''),
('Mohamad Rivai Firman Baha', '521421039', '5b13798a916b6437df330298dadc790a', '', '', 'Aktif', 'cmlla25v', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Farhan Yasin', '521421040', '482bb75fe3bed804148be62a5c937a0b', '', '', 'Aktif', 'ZW50ZWtr', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Mohamad Firman Usulu', '521421041', '60edca9ffdd6665e147a0efe9d93ae45', 'danuusulu21@gmail.com', '085342856150', 'Aktif', 'ZWxra2l0', 's1teknikelektro', '2021', '1a127ddb8e', 'Gorontalo, Desa tontayuo, Kec. Batudaa pantai, Kab. Gorontalo, Prov. Gorontalo', '1970-01-01', '', ''),
('Apriyanto Dj. Ayuba', '521421042', '5b9f91e7e330a096e32f2d963b505213', '', '', 'Aktif', 'ZWtrdGtl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Vahlim R. Madi', '521421043', '31fdf01fad3cab7e7bdd84703b1e24e3', '', '', 'Aktif', 'ZXRlb2tu', 's1teknikelektro', '2021', NULL, '', '2003-07-01', '', ''),
('Andika R Nasim', '521421044', '93657719ef17324f7b4be8a9436628ce', '', '', 'Aktif', 'dHRrbHJl', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Gunawan Ngadi', '521421045', '6167363b629d9220cd1a53eac0f2b8e2', '', '', 'Aktif', 'dGtsZW5v', 's1teknikelektro', '2021', NULL, '', '2002-11-09', '', ''),
('Iksan Kamah', '521421046', '8725c268a46065be1a8b5f7b0e8571a9', '', '', 'Aktif', 'bHRuZWVr', 's1teknikelektro', '2021', NULL, '', '2002-11-07', '', ''),
('Rifki Pakaya', '521421047', 'e1c3786549aa334ed4b1bbeea271ce85', '', '', 'Aktif', 'ZWVraXJ0', 's1teknikelektro', '2021', NULL, '', '2002-08-02', '', ''),
('Yolfan Lasaadi', '521421048', '386a154f2117f7b05fa318d5d5c943cc', 'ylasaadi@gmail.com', '081354175905', 'Aktif', 'NTIxNDIxMDQ4MDcwMTIwMDM=', 's1teknikelektro', '2021', '1a127ddb8e', 'LONGKOGA BARAT', '2003-01-07', 'TINELO', ''),
('Khalil Ar-rahman Balosi', '521421049', '907839c0a76d3cbcda03552c36a7daee', 'khalilarrahmanbalosi@gmail.com', '087777455184', 'tidak aktif', 'YXJ5YWJhbG9zaTIy', 's1teknikelektro', '2021', '1a127ddb8e', 'Padauloyo ', '2003-02-22', 'Padauloyo', 'file/521421049-passfoto.png'),
('Abdul Malik Katili', '521421050', '80547ab73db39cc5527908528e036d69', 'dikikatili29@gmail.com', '085220704083', 'Aktif', 'aWtlbnRr', 's1teknikelektro', '2021', '1a127ddb8e', 'Paguyaman', '2003-03-29', 'Paguyaman, desa diloato', ''),
('Tri Wahyudi Sy.mael', '521421051', 'a2f6d7e1af7b76c58a476b01908de5a0', 'yudimael46@gmail.com', '081215664450', 'tidak aktif', 'bGVra2V0', 's1teknikelektro', '2021', '6eba3d13ea', 'Buol', '2002-03-29', 'DUSUN LAMOLAN', 'file/521421051-passfoto.jpg'),
('Andrian Bakari', '521421052', '6b47c844143fc8dd3a03398044856c5a', 'andrianbakari3@gmail.com', '081935341193', 'Aktif', 'NTIxNDIxMDUyMjYwMzIwMDM=', 's1teknikelektro', '2021', 'f154c17f55', '', NULL, '', ''),
('Muh. Riyan R. Harmain', '521421053', 'af44b224dbde44d10775ca16923e6095', '', '082272157755', 'Aktif', 'a2VpZWtu', 's1teknikelektro', '2021', NULL, '', '1970-01-01', '', ''),
('Tri Putri Julianti', '521421054', '3d6b5bced8d165c5a1f52363c5fd167a', '', '082291360338', 'Aktif', 'dHRrbmVy', 's1teknikelektro', '2021', '5864493a02', '', '2003-12-07', '', ''),
('Harianti', '521421055', '2859fac6d508433e6f0e8e08abba2158', 'hariantisumiati74@gmail.com', '085931094091', 'Aktif', 'bmt0dGty', 's1teknikelektro', '2021', 'fb4b7ab909', 'watondo ', '2001-10-27', '', ''),
('Muh Ghali Raksi Akbar', '521421056', '0480bfb11af3f7d821df55c990b92305', '', '', 'Aktif', 'bmtrb2Vs', 's1teknikelektro', '2021', NULL, '', '2003-05-03', '', ''),
('Risaldi Ibrahim', '521422001', '87576e3e9a88759416e33761cf811607', '', '', 'Aktif', 'bmVrbGl0', 's1teknikelektro', '2022', NULL, '', '2003-05-10', '', ''),
('Moh Sadam R. Abdullah', '521422002', '345b3e13c32d5b6196771801b76d7cc9', 'sadamabdulah0704@.com', '082264933771', 'Aktif', 'a2lrbGVl', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Ardiansyah ', '521422003', 'c1ab37729e678d6694b12797f36dd964', 'ardiansyah210804@gmail.com', '085211304309', 'Aktif', 'NTIxNDIyMDAzMjEwODIwMDQ=', 's1teknikelektro', '2022', '274eccd1e7', 'SENTRAL SARI ', '2004-08-21', 'SENTRAL SARI ', ''),
('Iqbal Faisal Rahman', '521422004', '81024b912a71df78343d47035517a6c5', '', '', 'Aktif', 'ZXJraWxr', 's1teknikelektro', '2022', '98a4a6f7b7', '', '2004-08-03', '', ''),
('Fikram Samir ', '521422005', '97067fdb261ce01ec088c0e822d5cd1e', 'shop65848@gmail.com', '081354597487', 'Aktif', 'ZmlrcmFtODk=', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Adrian Joni', '521422006', '1c88b65135750835680b142cfeb1de95', '', '082271427932', 'Aktif', 'a3JlZW50', 's1teknikelektro', '2022', 'f154c17f55', '', '1970-01-01', '', ''),
('Yusfiyah Fitriani Daud', '521422007', '84c19535c75d18bca70c6b6d7237f76d', 'yusfiafitrianidaud@gmail.com', '083133141193', 'Aktif', 'Rkl5YWgxMjM=', 's1teknikelektro', '2022', 'f154c17f55', '', NULL, '', ''),
('Safrudin Podungge', '521422008', '39f2f36c632cb9816a2ba64c61eb72ec', '', '083850358965', 'Aktif', 'ZXJraW9r', 's1teknikelektro', '2022', '92459d083c', '', '2003-05-12', '', 'file/521422008-passfoto.jpg'),
('Sri Novita Pakaya', '521422009', 'e78dbe282fe0799da73089db08faf149', 'srinovitapakaya651@gmail.com', '08979363481', 'Aktif', 'QUxhZGEyOTExMjAwMw==', 's1teknikelektro', '2022', 'd53ee838e9', '', NULL, '', ''),
('Efdiatul Alfazia Asira', '521422010', '288c36b88b8ffdddaeac08e2096a57c1', '', '', 'Aktif', 'dHJra2tp', 's1teknikelektro', '2022', NULL, '', '2004-12-07', '', ''),
('Rahmat Arif', '521422011', '4a6f7d0e50fbd9608c32c2cc4d610b51', 'rahmatttarif23', '085230483916', 'Aktif', 'UmFobWF0MjMwNw==', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Neni Suwanti', '521422012', '90f8e455c25838a4f2de4f305f39704c', 'nenisuwanti571@gmail.com', '082292030687', 'Aktif', 'NTIxNDIyMDEy', 's1teknikelektro', '2022', '637dfbddee', 'Bone Tondo ', '2004-01-04', 'RT/RW 003/003, Desa Bone Tondo, Kecamatan Bone, Kabupaten Muna, Sulawesi Tenggara ', ''),
('Abdul Muhlis Idrus', '521422013', 'a9cc4dc58d840b3b8821ea608bf75399', '', '', 'Aktif', 'aXJrdHRr', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Firmansyah Paungan ', '521422014', '944c91d54aa34de9516b0dc81b65964f', '082252323206', '082252323206', 'Aktif', 'QU1CSXNpMjI=', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Igel Bakahi', '521422015', '032d2b4c9e3c8622acf4473b5302cce0', '', '', 'Aktif', 'dG5rZXJv', 's1teknikelektro', '2022', NULL, '', '2001-04-07', '', ''),
('Atmal Rahman', '521422016', '5b4c7e2984fbb4a7b08f0d4e10d0c68d', 'rahmanatmal@gmail.com', '082393423937', 'Aktif', 'YXRtYWxlbGVrdHJvMjI=', 's1teknikelektro', '2022', 'fb4b7ab909', '', NULL, '', ''),
('Abdul Zabir Yusuf', '521422017', '67e4b3fa815e032fd017de7ff72b2a1b', 'dandiyusuf93@.gmail.com', '081779423567', 'Aktif', 'NTIxNDIyMDE3MjcwNzIwMDQ=', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Nur Uyun I. Yusuf', '521422018', '197c3910a9c25357aedb123e49a059fe', '', '082346820670', 'Aktif', 'ZWV0a29u', 's1teknikelektro', '2022', '1a127ddb8e', '', '1970-01-01', '', ''),
('Ferdi Saliko ', '521422019', 'be4ac06d7d3b6c99db1796e76c498fc8', 'ferdisaliko3@gmail.com', '082243805674', 'Aktif', 'NTIxNDIyMDE5MzAwNDIwMDI=', 's1teknikelektro', '2022', '92459d083c', '', '2002-04-30', 'Desa Lamu\nKec.Tilamuta\nKab.Boalemo\nProv.Gorontalo', ''),
('Amelia Modeong', '521422020', '572bce1c768c9e39a9901ddf97580807', 'modeongvira@gmail.com', '082293757344', 'Aktif', 'bW9kZW9uZzE2', 's1teknikelektro', '2022', '14c4f7be30', '', NULL, '', ''),
('Rafael Bambela', '521422021', 'a95cafe1b977f04f082d5174742c9a5c', '', '', 'Aktif', 'dGVpdGtr', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Rendy Pratama', '521422022', 'f68c67b6add1e5639376b2a557770cf8', 'rendy_s1elektro@mahasiswa.ung.ac.id', '087844594127', 'Aktif', 'NTIxNDIyMDIyMTcxMjIwMDM=', 's1teknikelektro', '2022', 'd53ee838e9', 'Gorontalo', '2003-12-17', 'Desa Molutabu, Kec.Kabila Bone, Kab.Bone Bolango', ''),
('Hendrik Furman', '521422023', '5d645ce6c99673076aeabdaecec82364', '', '', 'Aktif', 'dHJpa2Vr', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Satriani Kalesing', '521422024', '4b7e4558441b7d9e76c2af2971ada60a', '', '', 'Aktif', 'a2VlbnRv', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Salsabila Buka', '521422025', '54f25773e8387d2009e3b102fb6bb658', 'salsabilabuka73@gmail.com', '0895396024444', 'Aktif', 'U2Fsc2ExMjAz', 's1teknikelektro', '2022', '5864493a02', 'GORONTALO', '2003-12-06', 'Limboto barat', ''),
('Sahrul Hanapi', '521422026', '5f93f5dce07c7a579288e88dd707a34e', '', '', 'Aktif', 'cmlrdGVv', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Antonius Sina Kelen', '521422027', '69845556966204aa14fbce28b115c139', '', '082261001549', 'Aktif', 'a3Jrb2lr', 's1teknikelektro', '2022', '6eba3d13ea', '', '2002-10-23', '', ''),
('Iman Zulvan Pratama', '521422028', 'cc84675fb15e629cd36539b06c82fdeb', '', '085136492620', 'Aktif', 'bGtydHRl', 's1teknikelektro', '2022', '14c4f7be30', '', '2004-09-07', '', ''),
('Arya Ramadhan Patilima ', '521422029', '82e721a57e22f1534455bee11404c1b4', 'aryapatilima06@gmail.com', '085242761183', 'Aktif', 'MzExMDAz', 's1teknikelektro', '2022', '6eba3d13ea', 'Monggupo ', '2003-10-31', 'Kec. Atinggola, Kab. Gorontalo Utara ', ''),
('Ximeko Elkavino Usuman', '521422030', '7e0b129429a8800636c1c49e9c9b5c15', 'ximekousuman@gmail.com', '082291572607', 'Aktif', 'NTIxNDIyMDMwMDQwNjIwMDQ=', 's1teknikelektro', '2022', '7fbcc3969e', '', NULL, '', ''),
('Yasmin Nazwa Alhabsyi ', '521422031', 'f50275a7ab6b69dd2218f079a016822a', 'nazwaalhabsyei@gmail.com', '081241689681', 'Aktif', 'MTIzNDU2Nzg5MTA=', 's1teknikelektro', '2022', '5864493a02', 'Gorontalo ', '2005-06-05', 'Telaga jaya', ''),
('Farhan Hasan', '521422032', '515b5c150898f393c4d2e11ae8a64e86', '', '', 'Aktif', 'dGV0a2ls', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Said Bintang Dwiyana Sugai', '521422033', '9f4ebd8d771e4257730c0bf4aea96b3e', 'saidsugai0@gmail.com', '082293098244', 'Aktif', 'NTIxNDIyMDMzMDUwMTIwMDQ=', 's1teknikelektro', '2022', '64809b0cfc', 'Jakarta', '2004-01-05', 'Desa Pilobuhuta Kecamatan Batudaa Kabupaten Gorontalo, Gorontalo', 'file/521422033-passfoto.jpeg'),
('Iskandar Nur Alfath Mokonu', '521422034', '070f4fc15dc6b36b368bdffae421390d', 'alfathmokonu56@gmail.com', '081342459206', 'Aktif', 'NTIxNDIyMDM0MjUwMTIwMDQ=', 's1teknikelektro', '2022', '6eba3d13ea', 'Suwawa', '2004-01-25', 'DESA TINGKOHUBU TIMUR', ''),
('Jabir Hiola', '521422035', 'a161fd31646a1574e5e8fd9ff36d00a9', 'jabirhiola04@gmail.com', '089529434667', 'Aktif', 'SmFiaXIxMjEy', 's1teknikelektro', '2022', 'fb4b7ab909', 'palu', NULL, '', 'file/521422035-passfoto.jpeg'),
('Aditya Maulana Hasan', '521422036', '004c6d244db64017a41e510171a398fb', 'adithasan018@gmail.com', '087788591931', 'Aktif', 'bGl2ZXJwb29sMTg=', 's1teknikelektro', '2022', 'f53eb48005', 'Desa Tolango Kec. Anggrek', '2003-09-18', 'Desa Tolango', ''),
('Fathan Mohamad ', '521422037', '09e311e45bed756ccecd613bf497e8f2', 'fathanmohamad17@gmail.com', '08979896381', 'tidak aktif', 'S1VoYWt1ODgwOA==', 's1teknikelektro', '2022', 'f154c17f55', 'Gorontalo', '2004-11-17', 'Perum Altira Blok A2 No 4 Desa Mongolato Kec. Telaga Gorontalo', 'file/521422037-passfoto.png'),
('Moh. Risaldi F. Umar', '521422038', '5695c41dc453d0a257429a6cba35245d', '', '', 'Aktif', 'b2xrZXRr', 's1teknikelektro', '2022', '92459d083c', '', '1970-01-01', '', ''),
('Asmat Musa ', '521422039', '2ed2ebf8cde009003ec0ef42c323295e', 'asmatmusa8@gmail.com', '081240419228', 'Aktif', 'QVNtYXRtdXNhMDAy', 's1teknikelektro', '2022', '7bf1f8337d', 'Keramat', '2025-08-03', '', ''),
('Renaldi Abdulrahman', '521422040', '69bc811a26f76f882c4a25c5b286b437', '', '082193114496', 'Aktif', 'NTIxNDIyMDQw', 's1teknikelektro', '2022', '274eccd1e7', 'Gorontalo', '2003-08-24', 'Desa Luwoo Kecamatan Talaga Jaya Kabupaten Gorontalo, Gorontalo', ''),
('Farsya Aditia Roihan Diko', '521422041', '92a6b9a51e200c008a647160deb6adaf', 'farsya.diko05@gmail.com', '081527733258', 'Aktif', 'cm90aWJha2FyMTI=', 's1teknikelektro', '2022', '98a4a6f7b7', 'Luwuk', '2005-04-12', 'Desa Bonebalantak\nKec. Batui Selatan\nKab. Banggai\nProv. Sulawesi Tengah', ''),
('Jeriko Leonardus', '521422042', '93dc3273c13506421662546db862fd03', 'ichoochai70@gmail.com', '082261002360', 'Aktif', 'a3RlcmVr', 's1teknikelektro', '2022', '5864493a02', 'keningau,sabah', '2005-05-25', 'JL.TRANS SEBATIK', 'file/521422042-passfoto.png'),
('Farhan Nur M Ibrahim', '521422043', '6febdb5daa6911c61427c0e2e398fe50', 'lintangibrahim93@gmail.com', '082259028385', 'Aktif', 'TGludGFuZ2NpYmkx', 's1teknikelektro', '2022', 'f154c17f55', 'Gorontalo', '2004-03-11', 'Dusun Nyiur indah lemito,Lemito', ''),
('Rinus Simon Tappik', '521422044', 'b42d381b082150fe330dc1fb632d9df6', 'rinussimontappik@gmail.com', '081347440263', 'Aktif', 'SGFzbml0YTExMDEwNA==', 's1teknikelektro', '2022', 'f53eb48005', '', NULL, '', 'file/521422044-passfoto.jpeg'),
('Meta Barri Palanto', '521422045', 'f8d6c1428da66a7e3687848db12ee089', 'barripalantometa@.com', '081524021650', 'Aktif', 'MDIwNjAz', 's1teknikelektro', '2022', '7fbcc3969e', 'Tator', '2003-06-02', 'Desa Buladu Kecamatan Kota Barat Kabupaten Kota Gorontalo, Gorontalo', ''),
('Ridho Al Zastro Mokodongan', '521422046', '37fd65e1c9b2454312d73995c5e4458e', '', '', 'Aktif', 'bG9udHRl', 's1teknikelektro', '2022', NULL, '', '2002-03-11', '', ''),
('Moh. Firkan Hidayat', '521422047', '01f6e902adf75adeeb337a01b9aa2d41', 'mohfirkanhidayat@gmail.com', '085340328187', 'Aktif', 'TGF1dHQ=', 's1teknikelektro', '2022', '637dfbddee', 'Desa piondo', '2004-07-04', 'Desa Piondo, Kecamatan Toili', ''),
('Sigit Taryanto', '521422048', 'cf92b534ff511a06f3034054560cc9f3', 'sigittaryanto@gmail.com', '082213095523', 'Aktif', 'bmV0a2xv', 's1teknikelektro', '2022', '7bf1f8337d', 'Piondo', '2002-04-02', 'Piondo', ''),
('Muhamad Yusuf', '521422049', 'a64bd79a787d51a12241c8adb7c6c7ed', 'muhamadyusuf200467@gmail.com', '081342981251', 'Aktif', 'WXVzdWYyMl9A', 's1teknikelektro', '2022', 'f53eb48005', 'Moilong', '2004-07-06', 'Mulyoharjo', ''),
('Reza Al Asfah', '521422050', 'd34f1fcaf4fd41294486237524e0d3d0', '', '', 'Aktif', 'ZW5rb2V0', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Aan Setiawan ', '521422051', 'af7aa83901c6e2e4ff93507197a316b4', 'aansetiawan090204@gmail.com', '083834203033', 'Aktif', 'dnkyMDA0dnY=', 's1teknikelektro', '2022', '1a127ddb8e', 'Monsongan ', '2004-02-09', 'Sinorang\nKecamatan batui selatan ', ''),
('Nurhidayat Hamusu', '521422052', 'f9f98f72d30df757d20faec70a1a9881', 'Nurhidayatnugi@gmail.com', '082292475795', 'Aktif', 'dG9rdGlu', 's1teknikelektro', '2022', '64809b0cfc', 'Tangeban', '2004-08-15', 'Tangeban, kecamatan Masama, Kabupaten banggai', 'file/521422052-passfoto.jpg'),
('Rahmat Kai', '521422053', '158a36069014218dcb2b9459e98d872f', 'rahmatkai30@gmail.com', '082393327805', 'Aktif', 'R09yb250YWxvMTI=', 's1teknikelektro', '2022', '7bf1f8337d', 'Gorontalo ', '2003-07-12', 'Jl. Palma, Kel. Buladu, Kec. Kota Barat', ''),
('Muhammad Rivaldi', '521422054', 'a29d264e9bc1e7088001e0778627c869', 'valdysaad937@gmail.com', '085656342606', 'Aktif', 'VmFsZHkyMDAz', 's1teknikelektro', '2022', '92459d083c', '', '2003-01-17', '', ''),
('Satrio Suryo Gemilang', '521422055', '1ab1d0450cf3b5d670080787475cadcb', 'satriosuryogemilang@gmail.com', '081355057492', 'Aktif', 'ZGVkZTEyMTIwMw==', 's1teknikelektro', '2022', '274eccd1e7', '', NULL, '', ''),
('Marvin Alviansyah Bachrun', '521422056', 'b0fbdb1f841d6bcfd86818070d6386c5', 'marvinalviansyah04@gmail.com', '0811313909', 'Aktif', 'bWFydklOMjY=', 's1teknikelektro', '2022', '274eccd1e7', 'gorontalo', '2004-11-26', 'jalan sawit perumahan elivar', ''),
('Mohamad Rifki Abukasi', '521422057', '7483c31e2b2f2e1896e193a75716db4b', '', '', 'Aktif', 'dG5lb3Jl', 's1teknikelektro', '2022', NULL, '', '2002-07-04', '', ''),
('Fauzan Fikri Djakaria', '521422058', 'fae2495509686a7a2dacb14acc37e063', 'fauzantv37@gmail.com', '081347825884', 'Aktif', 'ZmF1emFuMTQ5Mw==', 's1teknikelektro', '2022', 'f154c17f55', 'Desa Malanggo, Provinsi Sulawesi Tengah', '2003-09-14', '', ''),
('Mohammad Farhan Hanai', '521422059', '129952f349f327ebd78c5fb9c570c74d', 'mohammad_s1elektro@mahasiswa.ung.ac.id', '085796040629', 'Aktif', 'ZmFyaGFuMDk3NEA=', 's1teknikelektro', '2022', '1a127ddb8e', 'Bantik', '2004-07-09', 'dusun 1\ndesa bantik\nkecamatan bolaang timur\nkabupaten bolaang mongondow\nprovinsi sulawesi utara', ''),
('Jefrianto M. Abd. Samad', '521422060', '853153759dba371cb0c5c6a780c940ec', '', '', 'Aktif', 'ZWtlcmtp', 's1teknikelektro', '2022', NULL, '', '2003-09-12', '', ''),
('Alfiyan Rantung', '521422061', '87f956dba871a4bcb16eed1070cb43de', '', '', 'Aktif', 'bG9ldGtr', 's1teknikelektro', '2022', NULL, '', '1970-01-01', '', ''),
('Alya Pratiwi Putri Junus', '521422062', 'e9012e3e0b69d47c338bb50f4c89ce18', 'alyajunus04@gmail.com', '085345726136', 'tidak aktif', 'YWx5YTEyMzQ1', 's1teknikelektro', '2022', '36d1464083', 'Gorontalo', '2004-08-30', '', 'file/521422062-passfoto.jpg'),
('Izhar Ismail', '521422063', 'd53aad3f3e0c7dc23cb6e96b0ef377ad', '', '085298435413', 'Aktif', 'NTIxNDIyMDYz', 's1teknikelektro', '2022', '637dfbddee', 'Kota Gorontalo', '2004-08-26', 'Desa Tuladenggi Kecamatan Dungingi Kabupaten Kota Gorontalo, Gorontalo', ''),
('Moh. Prasetyo Ismail', '521422064', '4b21848cc64bfdadd73b3669f6d0601a', '', '', 'Aktif', 'cnRvbG5p', 's1teknikelektro', '2022', NULL, '', '2004-07-08', '', ''),
('Moh. Michael Balikan', '521422065', '4dbaf649e9f4b949fe41e8909a0093bb', '', '', 'Aktif', 'dHJvZWVr', 's1teknikelektro', '2022', '1a127ddb8e', '', '2004-11-11', '', ''),
('Hendra R Lasibani', '521422066', '4973e35f82008b146ce568a0a38267af', '', '', 'Aktif', 'ZWxrdHRu', 's1teknikelektro', '2022', 'fb4b7ab909', '', '2002-12-05', '', ''),
('Ibnu Gul Hakim', '521422067', '1f6329fe5328618a5c67f9d2990d6935', 'ibnugulhakim223@gmail.com', '082217460052', 'Aktif', 'SEFLSU0xMjM0NQ==', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Yolanda Modeong', '521422068', '0b02b4cb0f50a3e6c6b7be2872631cff', 'yolandamdeong@gmail.com', '085242766580', 'Aktif', 'TU9kZW9uZzEyMw==', 's1teknikelektro', '2022', '92459d083c', 'Dumolodo ', '2004-06-06', 'DESA DUMOLODO, KEC GENTUMA RAYA, KAB GORONTALO UTARA ', ''),
('Alif Syahdan Djauhari', '521422069', '348bd1d528f2bfe9a6a991caee8f854f', '', '', 'Aktif', 'a29ldGVr', 's1teknikelektro', '2022', NULL, '', '2003-03-12', '', ''),
('Miftahul Hasan ', '521422070', '1c565011b7b2932fcef6700e0d5f0633', 'miftahulhasan687@gmail.com', '082121563428', 'Aktif', 'MTIzbWl0YQ==', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Rivaldy Abas', '521422071', 'e4ae6833fb00f5f48ecb38e9ff5b3e6a', '', '082335434033', 'Aktif', 'a2Vlbmtl', 's1teknikelektro', '2022', '14c4f7be30', '', '2004-11-06', '', ''),
('Muhammad Zuhair', '521422072', '796d3fd8afa274c1fdcb4be6439eb7e1', 'mz241881@gmail.com', '081348652055', 'Aktif', 'bWVuYW5na2FuMTQ=', 's1teknikelektro', '2022', '645eef9c30', 'Sulawesi Selatan', '2004-06-14', 'Perumahan Verlina Indah Permai Blok C/13', ''),
('Moh Budi Baderan', '521422073', '2ed2fcb02394e737ef5b4cbab0d7aafb', 'mohamad11_s1elektro@mahasiswa.ung.ac.id', '081249424062', 'Aktif', 'QkFkZXJhbjEyMzQ=', 's1teknikelektro', '2022', NULL, '', NULL, '', ''),
('Veren Durand', '521422074', '6f1e591b17b9fe44050271bd995ade2c', 'verendurand73@gmailm.com', '082296421362', 'Aktif', 'NTIxNDIyMDc0MDMwOTIwMDQ=', 's1teknikelektro', '2022', '5864493a02', 'Desa Dumolodo ', '2004-09-03', 'Dusun mekar barat,desa Dumolodo,ke.gentuma raya', ''),
('Rifaldi S. Dahlan', '521423001', '082cbd858e69e01bbaa73f256b365b6f', 'rifaldisdahlan@gmail.com', '082259966256', 'Aktif', 'RGFobGFu', 's1teknikelektro', '2023', 'f53eb48005', 'Kambani', '2005-12-18', '', 'file/521423001-passfoto.png'),
('Dewa Kharisma H. Noe', '521423002', '35b75ccc1a18d3e2514444ed7a039e12', '', '082271205952', 'Aktif', 'ZGV3YTA3', 's1teknikelektro', '2023', 'fb4b7ab909', '', '2004-09-07', '', ''),
('Mohamad Asyqar S. Kono', '521423003', '0c10a2d74b7a9a262a4f3e6e0732b063', '', '089653173480', 'Aktif', 'a25la3R0', 's1teknikelektro', '2023', '637dfbddee', '', '2005-07-06', '', ''),
('Muhammad Shalfiq Adam', '521423004', '911c58eb0cf00741bf08b977fa0e4e60', 'adamshalfiq49@gmail.com', '0895321862439', 'Aktif', 'cmVraWVv', 's1teknikelektro', '2023', NULL, '', '2005-08-26', '', ''),
('Muhammad Yoga Hulopi', '521423005', '2a7b6e82967564c868c78b1e2b2a87bb', 'yogahulopi7@gmail.com', '081527758203', 'Aktif', 'a2lrcm5s', 's1teknikelektro', '2023', NULL, 'Duminanga 09/06/2004', '2004-06-09', '', ''),
('Isti Nazmi Patilima', '521423006', '1b56625b68156757b5c24b338146352a', '', '082221322095', 'Aktif', 'UGF0aWxpbWE=', 's1teknikelektro', '2023', '274eccd1e7', '', '2004-02-19', '', ''),
('Khalifatul Fauzan Ranjani', '521423007', '816580b17f3855521352e2ec32dc5335', '', '082349780941', 'Aktif', 'ZWxraXRr', 's1teknikelektro', '2023', '274eccd1e7', '', '2004-08-13', '', ''),
('Fardias Mahmud', '521423008', '2b1a812d1c21e372677c5fbef029c4f3', '', '085242904396', 'Aktif', 'NTIxNDIzMDA4MjUxMjIwMDQ=', 's1teknikelektro', '2023', '274eccd1e7', 'Desa ilangata', '2004-12-25', 'Desa ilangata kecamatan anggrek kab Gorontalo Utara', ''),
('Moh. Hapid Dj. Daud', '521423009', '9b548bdad0aca8ae88ce86ffd444a64d', '', '082271182560', 'Aktif', 'dGtrZWVp', 's1teknikelektro', '2023', '64809b0cfc', '', '2005-07-13', '', ''),
('Ahmad Ikbaar Prayoga', '521423010', '871777ddf5ae59e1c666f1b638ed8402', '', '', 'Aktif', 'UHJheW9nYQ==', 's1teknikelektro', '2023', NULL, '', '2006-11-14', '', ''),
('Taufik Hatibi', '521423011', 'd7b8557fde88b475aec99f3028168ac4', 'taufikhatibi90@gmail.com', '081242657096', 'Aktif', 'aXJla2V0', 's1teknikelektro', '2023', NULL, 'Gorontalo', '2006-02-11', 'Gorontalo. Jl.Raja Eyato. Kel. Dembe 1Kec. Kota Barat Kota. Gorontalo ', ''),
('Mohammad Alid Amran', '521423012', '5bb8523091b829de69341ab9366883da', '', '', 'Aktif', 'QW1yYW4=', 's1teknikelektro', '2023', '64809b0cfc', '', '2004-12-27', '', ''),
('Rando Rauf', '521423013', 'e95b91b676d45a717a882ac082d16837', 'randorauf2004@gmail.com', '085215177375', 'Aktif', 'Q3Jhenl5eQ==', 's1teknikelektro', '2023', NULL, 'Malango ', '2004-06-24', '', ''),
('Nur Aulia', '521423014', '1fbf984b601f4756a57dc70279dd75f5', '', '082290059287', 'Aktif', 'dGl0ZW9r', 's1teknikelektro', '2023', '36d1464083', 'Gorontalo', '2005-07-23', 'Jl.KH Ahmad Dahlan\nkelurahan Limba B\nKecamatan kota selatan,kota gorontalo', 'file/521423014-passfoto.jpeg'),
('Wahyudin Sango', '521423015', 'c3e98c3bfe4b31b0a41d6d7e9c9c449c', '', '081258325530', 'Aktif', 'ZWVrZWl0', 's1teknikelektro', '2023', NULL, '', '2004-09-18', '', ''),
('Salman Hudoi', '521423016', '1daa57884719576c368d2cf4d167c735', '', '082191923410', 'Aktif', 'a2tsdHRu', 's1teknikelektro', '2023', NULL, '', '2004-09-23', '', ''),
('Andri Ilham Lamaga', '521423017', '098500665d8a9c851b9884a992449edf', '', '085342972500', 'Aktif', 'ZWllbnRs', 's1teknikelektro', '2023', NULL, '', '2005-09-15', '', ''),
('Muh. Fatkur Khoir', '521423018', '9fe3428b9576d054ab901cfdaa6540a3', '', '083844036262', 'Aktif', 'b25laWx0', 's1teknikelektro', '2023', NULL, '', '2005-04-08', '', ''),
('Arfita Sukma Ayu Himran', '521423019', 'd5768882b6c9fb3c6ea6c42c2ea9631f', 'arfitasukmaayuhimran@gmail.com', '082293031293', 'Aktif', 'SGltcmFu', 's1teknikelektro', '2023', '36d1464083', 'Bunta', '2004-10-12', 'Kelurahan Kalaka, Kecamatan Bunta, Kabupaten Banggai, Provinsi Sulawesi Tengah.', ''),
('Abdul Rahman Muslim', '521423020', '9954968f6a536dc41028e8f8d2d9fe38', '', '085220730191', 'Aktif', 'TXVzbGlt', 's1teknikelektro', '2023', '98a4a6f7b7', '', '2004-08-14', '', ''),
('Moh. Rizaldy Datu', '521423021', '83a5efd1f2680f255df68d8f62b1cc3d', 'rizaldydatu68@gmail.com', '082291109580', 'Aktif', 'RGF0dQ==', 's1teknikelektro', '2023', '98a4a6f7b7', 'Kabila', '2006-01-19', '', ''),
('Putra Taufic Didipu', '521423022', 'b31f91785cb87b2ebf95f9ec61c83113', '', '081241688542', 'Aktif', 'RGlkaXB1', 's1teknikelektro', '2023', 'fb4b7ab909', '', '2003-06-18', '', ''),
('Mohamad Amin Jahja', '521423023', '45fe0167b0a4b728982ca5142bf7c4ad', '', '', 'Aktif', 'SmFoamE=', 's1teknikelektro', '2023', 'fb4b7ab909', '', '2004-05-17', '', ''),
('Mustawa S. Timumun', '521423024', 'f0a97796e44c447d08ddec2a63d6179c', 'mustawatimumun@gmail.com', '082298195303', 'Aktif', 'VGltdW11bg==', 's1teknikelektro', '2023', 'd53ee838e9', 'Buol', '2003-02-24', 'Kab. Buol-kec. Bokat-Desa  Tayadun', ''),
('Arfi Helfi Sari', '521423025', '233ade8ec62b176266c3f7cb215b07d4', '', '082259489494', 'Aktif', 'U2FyaQ==', 's1teknikelektro', '2023', NULL, '', '2005-03-31', '', ''),
('Moh. Faisal S Kamal', '521423026', '1403ffee7b243799be91c526ab07b4bf', '', '082349143170', 'Aktif', 'S2FtYWw=', 's1teknikelektro', '2023', 'd53ee838e9', 'Wakai', '2004-09-24', '', ''),
('Eva Lusiana', '521423027', 'd523a8b379943937b528ab80eac7321e', '', '081341761949', 'Aktif', 'THVzaWFuYQ==', 's1teknikelektro', '2023', NULL, '', '2005-04-23', '', ''),
('Nizar Bin Yahya', '521423028', '752b072490166ac417117c73449f5972', 'mawartunggoli@gmail.com', '082262423173', 'Aktif', 'WWFoeWE=', 's1teknikelektro', '2023', '5864493a02', 'Buol', '2004-06-11', 'Desa bongo', ''),
('Algian A. Bukamo', '521423029', 'af812be3884c57340d385398a9395d83', 'algianbukamo@gmail.com', '085398908854', 'Aktif', 'QnVrYW1v', 's1teknikelektro', '2023', 'd53ee838e9', 'Pasir Putih', '2005-05-12', 'Desa Pasir Putih, Kec. Banggai, Kab. Banggai Laut, Sulawesi Tengah', 'file/521423029-passfoto.jpg'),
('Docya Berahima', '521423030', 'ba90ddc7b61f484c70b74c5f255eca2b', 'docyaberahima@gmail.com', '082238433663', 'Aktif', 'QmVyYWhpbWE=', 's1teknikelektro', '2023', NULL, 'BOLAANGMONGONDOW', '2005-03-17', 'Desa ollot induk', ''),
('Mika', '521423031', '05c0044c5d811de62e0670f15e91466a', '', '082352849283', 'Aktif', 'TWlrYQ==', 's1teknikelektro', '2023', '7fbcc3969e', '', '2002-05-03', '', ''),
('Hendrik Rahmadan', '521423032', '4582a4edf26ec09080cc9ecf8f2782e8', '', '085298794086', 'Aktif', 'UmFobWFkYW4=', 's1teknikelektro', '2023', NULL, '', '2004-10-11', '', ''),
('Cindy Anatasya', '521423033', 'e80dbceb0dd8cbf36e374420601efac3', '', '085341277161', 'Aktif', 'QW5hdGFzeWE=', 's1teknikelektro', '2023', '7fbcc3969e', '', '2005-10-10', '', ''),
('Afriandi S. Lasang', '521423034', '91b17c849cdb97cc9e01e7ec26f995ba', 'rianlasang14@gmail.com', '082259803237', 'Aktif', 'dG5rcmtp', 's1teknikelektro', '2023', '7fbcc3969e', '', '2004-09-14', '', ''),
('Rahmad Malik Ibrahim', '521423035', '106c852cf045b2e475d123172bf2c4a8', 'rahmadmalik626@gmail.com', '089507885550', 'Aktif', 'cmtsaWtr', 's1teknikelektro', '2023', '36d1464083', 'Gorontalo', '2004-02-20', 'JLN.GUNUNG TILONGKABILA', ''),
('Mohamad Ishak Suleman', '521423036', '29caefc4fa46e74087c009c72e8ca3cf', '', '', 'Aktif', 'U3VsZW1hbg==', 's1teknikelektro', '2023', NULL, '', '2005-11-23', '', ''),
('Teguh Moh. Reyvansyah Tahawali', '521423037', '78c6ed2c25e727f8f37749ecaf18092b', '', '082191764753', 'Aktif', 'dGtpbnRl', 's1teknikelektro', '2023', NULL, '', '2006-01-08', '', ''),
('Yogi Nata Iskandar', '521423038', 'c95712628497396b85bccbfae995a3e0', '', '081993582730', 'Aktif', 'aXNrYW5kYXIyMjE=', 's1teknikelektro', '2023', '7bf1f8337d', '', '2005-06-06', '', ''),
('Theo V Kolopita', '521423039', 'a30ca024ad2385cc3a82b97e21a42178', '', '085824285946', 'Aktif', 'dGVvdGVr', 's1teknikelektro', '2023', NULL, '', '2005-02-15', '', ''),
('Ramliadi H. Deilipu', '521423040', '18ab1ae0920d081430e499cf1d35ac9f', '', '082268594058', 'Aktif', 'ZWlla3Rl', 's1teknikelektro', '2023', NULL, '', '2005-10-26', '', ''),
('Yuniar G. Anahartasya', '521423041', '4402d5a8556bc5160c8f3ebf608e1457', '', '081254357368', 'Aktif', 'ZW9la250', 's1teknikelektro', '2023', NULL, '', '2005-07-10', '', ''),
('Moh.Dwi Aditya Ibrahim', '521423042', 'ea1c024895515de15e5bd361e02e3ac5', 'aditibrahimm12@gmail.com', '082128840177', 'Aktif', 'a2trdGVl', 's1teknikelektro', '2023', 'f53eb48005', 'Gorontalo', '2004-11-19', '', ''),
('Muh. Sofyan As Sauri Wahyudi', '521423043', '866898ec074540bae8b415a033bced85', '', '081228282706', 'Aktif', 'aXJlb2tl', 's1teknikelektro', '2023', NULL, '', '2005-07-26', '', ''),
('Veron Bastian S. Rahman', '521423044', '9ee8c8750aa8749d239171c15d8b5bba', '', '', 'Aktif', 'UmFobWFu', 's1teknikelektro', '2023', NULL, '', '2004-12-29', '', ''),
('Endrayano Yupano', '521423045', '9afa7d92f75626b979b92a070f9f8a21', '', '082232074981', 'Aktif', 'ZXRrZXJr', 's1teknikelektro', '2023', NULL, '', '2005-08-28', '', ''),
('Edrial Refly Kapu', '521423046', 'c4eb36417de19a2c57f168e1d541a1f1', '', '', 'Aktif', 'S2FwdQ==', 's1teknikelektro', '2023', NULL, '', '2005-07-14', '', ''),
('I Wayan Adi Kumbara', '521423047', '787db7f3134e804dd33fb5c1e69df725', '', '', 'Aktif', 'S3VtYmFyYQ==', 's1teknikelektro', '2023', NULL, '', '2006-12-12', '', ''),
('Brain', '521423048', '8b7f79b78e588c39f770cc0e8c410b8a', '', '0895367826050', 'Aktif', 'QnJhaW4=', 's1teknikelektro', '2023', NULL, 'MANADO', '2005-10-22', '', ''),
('Mohamad Firman Adam', '521423049', '2d39d1fc7683efaeea4cb568c2fd69f9', '', '082194921219', 'Aktif', 'bmtyZXRv', 's1teknikelektro', '2023', NULL, '', '2006-08-28', '', ''),
('Farid Chandra Aditama', '521423050', 'd703110e2c5daf1d65066765a853148c', '', '085161013552', 'Aktif', 'QWRpdGFtYQ==', 's1teknikelektro', '2023', '645eef9c30', '', '2004-03-25', '', ''),
('Nindita Pratiwi R. Ahmad', '521424001', '0c19b3fc422473b88596a03abd148768', 'nindiahmad610@gmail.com', '089502766518', 'Aktif', 'NTIxNDI0MDAxMTIz', 's1teknikelektro', '2024', NULL, 'gorontalo', '2006-06-29', 'jl.k.h adam zakaria,kel.dembe jaya kec.kota utara kota gorontalo', ''),
('Natia Nursuci Septiyusi', '521424002', 'b86e07cd40cc5127ae591a2273cf9a0b', 'natianur05@gmail.com', '085696248563', 'Aktif', 'NTIxNDI0MDAyMTIz', 's1teknikelektro', '2024', '5864493a02', 'GARUT', '2006-08-05', 'konarom utara kec.dumoga tenggara', ''),
('Mohamad Ramdan Lamadi', '521424003', '8bbef690fa7176a60973937e191624de', 'ramalamadi6@gamail.com', '082393229881', 'Aktif', 'NTIxNDI0MDAzMTIz', 's1teknikelektro', '2024', '5864493a02', 'atinggola', '2006-10-23', 'gorontalo,gorontalo utara, atinggola', ''),
('Nuryanti Lasahido', '521424004', 'c14feff8bbd841dd3c139081a27025d7', 'nuriyantilasahido@gmail.com', '085805859149', 'Aktif', 'NTIxNDI0MDA0MTIz', 's1teknikelektro', '2024', NULL, 'moilong', '2005-07-01', 'suwawa, tingkohubu timur', ''),
('Rafli S Abdullah', '521424005', '69e217a3af9f169065414c7cffc29e1c', '', '', 'Aktif', 'NTIxNDI0MDA1MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Mohamat Fadel Husain', '521424006', 'cbf491f5856d5833f0900fde3e274c06', 'fadelhusain908@gmail.com', '089507890359', 'Aktif', 'NTIxNDI0MDA2MTIz', 's1teknikelektro', '2024', NULL, 'gorontalo', '2006-06-21', 'jln.pemerataan, RT 3, RW 4, ipilo, kota timur, ', ''),
('Moh. Adly Egam', '521424007', '4f4128319be7f6a6324d67633acbaa43', 'egamadly@gmail.com', '089531816209', 'Aktif', 'NTIxNDI0MDA3MTIz', 's1teknikelektro', '2024', '274eccd1e7', 'Kortamobagu', '2007-06-05', 'KOTAMOBAGU TIMUR,MATALI,RT12,RW06', ''),
('Rifyan Saini', '521424008', '973fad9bd14ea23809929dc0b8d024c5', '', '', 'Aktif', 'NTIxNDI0MDA4MTIz', 's1teknikelektro', '2024', '274eccd1e7', '', NULL, '', ''),
('Dea Agistiya', '521424009', 'dc4647f6ce797c23827ae86965cd9dba', 'agistyadea@gmail.com', '085824772278', 'Aktif', 'NTIxNDI0MDA5MTIz', 's1teknikelektro', '2024', '274eccd1e7', 'Luwuk', '2006-08-31', '', ''),
('Moh. Atyar Lakoro', '521424010', '4930402d7b38ff773c74093937ce3800', 'atrlro17@gmail.com', '082347103254', 'Aktif', 'NTIxNDI0MDEwMTIz', 's1teknikelektro', '2024', '98a4a6f7b7', 'Gorontalo', '2007-02-17', 'Desa Sipatana, Kec. Buntulia, Kab. Pohuwato Prov. Gorontalo', ''),
('Raihan Febriyan Abas', '521424011', '5617ecbf6fc75e9069d577132a83df57', '', '', 'Aktif', 'NTIxNDI0MDExMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Norman Ali Saefullah Lihawa', '521424012', '6c676d5c213e8f03cae65c4beb0dddfd', 'saefullahlihawa@gmail.com', '089679874488', 'Aktif', 'NTIxNDI0MDEyMTIz', 's1teknikelektro', '2024', '98a4a6f7b7', 'Gorontalo ', '2006-06-15', 'jalan rajawadi palapa. dimuka faninda jaya meubel, rumah kuning', ''),
('Kisman Ak. Toinok', '521424013', '19a93e8e08b9b5c338dacbebae1fa5d8', 'ktoinok@gmail.com', '082261388586', 'Aktif', 'NTIxNDI0MDEzMTIz', 's1teknikelektro', '2024', '5864493a02', 'buol', '2005-05-05', 'kab.buol', ''),
('Irnawanti Umar', '521424014', '3b76ec077bb5da5370fca19ea3a93d05', 'irnawantiumar@gmail.com', '085398669732', 'Aktif', 'NTIxNDI0MDE0MTIz', 's1teknikelektro', '2024', NULL, 'weda utara', '2006-08-16', 'kec. telaga biru \nkab. gorontalo', ''),
('Rayhan Utina', '521424015', 'b3df79419ffa8367452360282e7804c5', '', '', 'Aktif', 'NTIxNDI0MDE1MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Gilang Tobuhu', '521424016', '3d3a2b12bb41c74de1fa588299d6bf6c', 'gilang.r.tobuhu@gmail.com', '085240726309', 'Aktif', 'NTIxNDI0MDE2MTIz', 's1teknikelektro', '2024', NULL, 'mubune', '2003-01-06', 'dusun IV Desa Mebongo kec.Botumoito Kab. Boalemo', ''),
('Moh. Rafli L. Bobihu', '521424017', 'f7f5c8ede6f854efee1763a982c94aa5', 'mohraflibobihu01@gmail.com', '082297408295', 'Aktif', 'NTIxNDI0MDE3MTIz', 's1teknikelektro', '2024', '7fbcc3969e', 'BUOL', '2006-05-12', 'perumahan kaputi indah jln kenangan 2 kel dulawo timur', ''),
('Moh. Fitra Lendjango', '521424018', '0fe95fc8aa0f14c4b59de253505898a1', 'fitralenjango02@gmail.com', '0895352612323', 'Aktif', 'NTIxNDI0MDE4MTIz', 's1teknikelektro', '2024', '7fbcc3969e', 'sinombayuga ', '2005-09-21', 'suwawa gorontalo ', ''),
('Muh. Rangga. Dg. Pacidda', '521424019', '6e0d27bd46e13b414c0cc1ddd625fe43', '', '', 'Aktif', 'NTIxNDI0MDE5MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Alamin Yatni Slamet', '521424020', '91fcb298b4c160465c5213e5cd7b2755', '', '', 'Aktif', 'NTIxNDI0MDIwMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Bintang Fachriza Islami Suratinoyo', '521424021', 'f30e9f2edcb6d3010981b198a5d5d7da', '', '', 'Aktif', 'NTIxNDI0MDIxMTIz', 's1teknikelektro', '2024', '637dfbddee', '', NULL, '', ''),
('Agil M. Ahmad', '521424022', 'db1234a1022a4d481ed8108d03dbffe4', 'agilgossen@gmail.com', '0895332237670', 'Aktif', 'NTIxNDI0MDIyMTIz', 's1teknikelektro', '2024', 'f53eb48005', 'gorontalo, 1 agustus 2004', '2004-08-01', 'desa langge kec tapa', ''),
('Adjril Akbar Kadullah', '521424023', 'cb332e069804651dfa66951811199476', '', '', 'Aktif', 'NTIxNDI0MDIzMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Novan Dion Rezki Saputra Hadju', '521424024', '91e1c4ad408a94a869fba9a4821d3ad4', '', '', 'Aktif', 'NTIxNDI0MDI0MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('I Nengah Dwi Astika Putra', '521424025', '9d5210527bab8d60933a8ff5920287b6', 'dwinengah70@gmail.com', '081343569610', 'Aktif', 'NTIxNDI0MDI1MTIz', 's1teknikelektro', '2024', NULL, 'DESA MALEO JAYA. KEC BATUI  SELATAN .KAB BANGGAI', '2004-11-18', 'DESA MALEO JAYA. KEC BATUI SELATAN .KAB BANGGAI. SULAWESI TENGAH', ''),
('Aprizal Daud Yasin', '521424026', '21d2f89fd71a8b27c158cfdc19464e1b', 'aprizalyasin532@gmail.com', '083135640482', 'Aktif', 'NTIxNDI0MDI2MTIz', 's1teknikelektro', '2024', NULL, 'kab gorontalo', '2005-04-21', 'dusun baru,dulamayo utara,telaga biru', ''),
('Radja Al Fajr Sidariman', '521424027', '0d12284ae8cfeb8589982b6ab7d9dae8', '', '', 'Aktif', 'NTIxNDI0MDI3MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Arib Buako', '521424028', 'b5234e08ffefcac53b732ad1a1fe44d7', '', '', 'Aktif', 'NTIxNDI0MDI4MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Siti Rahmadania R Muchsin', '521424029', '5a2d7edf98d09344428afffc6cbba332', 'sitirahmadaniamuchsin29@gmail.com', '082291583598', 'Aktif', 'NTIxNDI0MDI5MTIz', 's1teknikelektro', '2024', NULL, 'gorontalo', '2006-12-29', 'JL.BOTU LIODU', ''),
('Aqsal Azriansyah Bini', '521424030', 'bcfdbbbf9deb61affeca50621917c813', 'aqsalazriansyah69@gmail.com', '081347039971', 'Aktif', 'NTIxNDI0MDMwMTIz', 's1teknikelektro', '2024', '1a127ddb8e', 'gorontalo', '2006-07-24', 'jl.raja eyato\nkel.buladu\nkec.kota barat', ''),
('Fardan I. Sandala', '521424031', 'a25cf1598da584b3f40a54deaff54f0a', '', '', 'Aktif', 'NTIxNDI0MDMxMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Moh.abdul Ikram Ibrahim', '521424032', '1c070c47c641630899f39f3c8d255a8d', '', '', 'Aktif', 'NTIxNDI0MDMyMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Riski', '521424033', 'e56adbfcd795b5af5e3069c489cec797', 'riskiaditya1803@gmail.com', '085222207897', 'Aktif', 'NTIxNDI0MDMzMTIz', 's1teknikelektro', '2024', '637dfbddee', 'bontosi', '2004-07-29', 'desa bontosi', ''),
('Moh.abdul Akram Ibrahim', '521424034', '8c6f9e9303d6b49e4685f16617a64279', '', '', 'Aktif', 'NTIxNDI0MDM0MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Jihan Fadila Hatibi', '521424035', '77e2ae5bcada744eab0700b8cb68a1dc', 'jihanfadilahatibi@gmail.com', '085342840977', 'Aktif', 'NTIxNDI0MDM1MTIz', 's1teknikelektro', '2024', 'fb4b7ab909', 'tondano', '2006-01-08', 'perumahan surya graha permai. jl. pangeran hidayat', ''),
('Zulkarnain Abbas', '521424036', 'a03350305df080a7b177b569569c582b', '', '', 'Aktif', 'NTIxNDI0MDM2MTIz', 's1teknikelektro', '2024', 'fb4b7ab909', '', NULL, '', ''),
('Mohamad Rizky Panu', '521424037', '98ba34559332532a70de09f2663b2967', '', '', 'Aktif', 'NTIxNDI0MDM3MTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Irgi Fahrezy Albert', '521424038', 'f1b62db9b5084d2fd68babd32494888f', 'irgialbert2005@gmail.com', '081356911216', 'Aktif', 'NTIxNDI0MDM4MTIz', 's1teknikelektro', '2024', NULL, '07-11-2005', '2005-11-07', 'ilangata kab gorut', ''),
('Ashim Al Hafidz Gani', '521424039', 'f35c020ab93a06fe6fd5ea57d029ac4d', 'ganiashim1@gmail.com', '0895417180005', 'Aktif', 'dGVpbnRl', 's1teknikelektro', '2024', '64809b0cfc', 'GORONTALO', '2006-11-24', 'TOTO UTARA', 'file/521424039-passfoto.jpg'),
('Zulkifli Akuba', '521424040', 'a9ace4f6c60ed1bf08d0d2ce5bea822f', 'zulkifliakuba82@gmail.com', '082271562635', 'Aktif', 'NTIxNDI0MDQwMTIz', 's1teknikelektro', '2024', NULL, 'kec.tolinggula,desa ilomangga', '2005-07-22', 'kab.gorontalo utara,kec.tolinggula,desa.ilomangga', ''),
('Asraf Lahami', '521424041', 'f2849b61d43f8fe3797a230883003b9f', 'zidanramdan505@gmail.com', '082291332728', 'Aktif', 'NTIxNDI0MDQxMTIz', 's1teknikelektro', '2024', '64809b0cfc', 'GORONTALO,30-11-2004', '2004-11-30', 'Desa pangadaa, Kec Dungaliyo, Kab Gorontalo', ''),
('Roland Sanaja', '521424042', '71841f767a6020f324d10d5f10ba193e', '', '', 'Aktif', 'NTIxNDI0MDQyMTIz', 's1teknikelektro', '2024', '92459d083c', '', NULL, '', ''),
('Abit Fajar Wartabone', '521424043', 'bfb1820da3e84743f4daddd49526feba', '', '', 'Aktif', 'NTIxNDI0MDQzMTIz', 's1teknikelektro', '2024', NULL, '', NULL, '', ''),
('Moh. Guslin Pilomange', '521424044', '8cc07a2a62579cce3b547c411665b4ff', 'mohguslinpilomange@gmail.com', '081527407095', 'Aktif', 'NTIxNDI0MDQ0MTIz', 's1teknikelektro', '2024', '92459d083c', 'TALUDAA', '2005-08-12', 'DESA ILOHUUWA JL. AIR TERJUN', ''),
('Dirga Kamaru', '521424045', 'f99953de9462754dc92e2aa4268d9a3b', '', '', 'Aktif', 'NTIxNDI0MDQ1MTIz', 's1teknikelektro', '2024', '92459d083c', '', NULL, '', ''),
('Aditya Muhammad Karim', '522423001', 'f53c7569cb4f8cdfdbf322d9667a795a', 'adiitttttt25@gmail.com', '081243260420', 'Aktif', 'Um9sZXJrdW5pbmcyNQ==', 's1teknikkomputer', '2023', '36d1464083', 'Gorontalo ', '2005-07-25', '', ''),
('Nabilah Auliya Fahmi', '522423002', 'ccd69627b86f7181b936b00f0e86e2d7', 'nabilahauliya2005@gmail.com', '082393331273', 'Aktif', 'dGVpZXJr', 's1teknikkomputer', '2023', '5864493a02', 'Gorontalo', '2005-03-01', 'Kel. Hunggaluwa, Kec. Limboto', ''),
('Firmansyah Badu', '522423003', 'ebc84de6d69fe01add3a8dd7baa51d97', '', '089530667712', 'Aktif', 'ZWVsdGlr', 's1teknikkomputer', '2023', '1a127ddb8e', 'Gorontalo', '2005-06-16', '', ''),
('Firman Maranua Uwete', '522423004', 'bf8d0a5330de074a393b27c1a8629ac3', '', '082259033048', 'Aktif', 'dGllbmtr', 's1teknikkomputer', '2023', NULL, '', '2005-09-19', '', ''),
('Teguh Saputra Sukiswanto', '522423005', '5e29d5e44ab908fc75f6eb8bb502486e', 'teguhgaming0@gmail.com', '0895375074646', 'Aktif', 'bm9pa2tl', 's1teknikkomputer', '2023', NULL, 'Gorontalo', '2006-01-17', 'Jalan palma', ''),
('Muhsin Lantapa', '522423006', 'ba589e53458634d72ab8bb93895b179e', '', '081342495785', 'Aktif', 'dG5ya2Vl', 's1teknikkomputer', '2023', NULL, '', '2005-02-25', '', ''),
('Alde Reza Purnama Daliuwa', '522423007', 'd03e6e6542432e7edfc03ba458a8cd7c', '', '', 'Aktif', 'RGFsaXV3YQ==', 's1teknikkomputer', '2023', NULL, '', '2004-12-30', '', ''),
('Moh. Ali Algifari Usman', '522423008', 'e6e5eaae0bd4c29910cfa96f0509112d', 'algidung@gmail.com', '082350595294', 'Aktif', 'QWxnaWZhcmkyMDA1', 's1teknikkomputer', '2023', NULL, 'Molingkapoto, Kec. Kwandang, Kab. Gorontalo Utara', '2005-03-12', '', ''),
('Muh. Ibrahim Al Ghiffari', '522423009', 'b2d0420d9dbb1bc52a652b335b915bd1', '', '082318250097', 'Aktif', 'cmxva3Ru', 's1teknikkomputer', '2023', '1a127ddb8e', '', '2002-07-05', '', ''),
('Moh. Rifaldo Mantali', '522423010', 'd8976e80cd31c1ee291c7683791c5139', 'mohrifaldo23@gmail.com', '082330758710', 'Aktif', 'YWxkb2FsZGkyMw==', 's1teknikkomputer', '2023', '36d1464083', 'kwandang', '2002-12-23', 'Desa Bulalo, Dusun Hulapa', 'file/522423010-passfoto.png'),
('Yunus Kadir', '522423011', '36e8b5bcfbd2d755c0dbd5b61bddcf5e', 'yunskadr12@gmail.com', '082291907851', 'Aktif', 'c2lhdGVrX3l1bnVzIzAxMQ==', 's1teknikkomputer', '2023', 'f154c17f55', '12 September 2003', '2003-12-09', '', ''),
('Moh. Adzriel Alfareza Bilondatu', '522423012', '364a177a606ffb549f9e7cee0563247b', 'moh.adzriel21@gmail.com', '089530203479', 'Aktif', 'QURaUklFTDIwMDU=', 's1teknikkomputer', '2023', 'f154c17f55', 'Gorontalo', '2005-03-21', '', ''),
('Moh Iqbal Hunowu', '522423013', 'a4245dbdcd5265bfab53bb1814dafc4b', 'iqbalhunowu15@gmail.com', '085342932650', 'Aktif', 'd2t3a3drd2sx', 's1teknikkomputer', '2023', '5864493a02', 'Gorontalo', '2004-11-15', '', ''),
('Alexander Matthew Suwandi', '522423014', 'c5145e60414be531776b0b280310359c', '', '', 'Aktif', 'U3V3YW5kaQ==', 's1teknikkomputer', '2023', NULL, '', '2005-11-08', '', ''),
('Reja Aswad Podungge', '522423015', 'a92a0067722f130e14d04a002254d3e9', 'rezaaswad03@gmail.com', '081911490120', 'Aktif', 'cmt0aW5l', 's1teknikkomputer', '2023', NULL, 'Gorontalo', '2003-12-22', '', ''),
('Nabila Nurmalasari', '522423016', 'a4265f5fbf4984e943d6b872e92b0b12', 'nabila_s1teknikkomputer@mahasiswa.ung.ac.id', '082267664346', 'Aktif', 'ZW9udGty', 's1teknikkomputer', '2023', '5864493a02', 'Buol', '2005-05-16', 'Kelurahan Leok 2', 'file/522423016-passfoto.png'),
('Mohammad Dirga Bahsoan', '522423017', 'cd732c71aa4fa233c3b24646421431dd', '', '082259565050', 'Aktif', 'bmVva2Vp', 's1teknikkomputer', '2023', NULL, '', '2005-08-22', '', ''),
('Mohamad Fauzan Padja', '522423018', 'b4b72c8cb79b500e4596182a9b4469a7', '', '0895413202336', 'Aktif', 'dGVlaWVr', 's1teknikkomputer', '2023', NULL, '', '2004-12-09', '', ''),
('Gina Amalia A. Umar', '522423019', 'bddb893ce265da9cc25c186f9a444413', '', '082271425293', 'Aktif', 'QXNhaGlyb2JvdDA0', 's1teknikkomputer', '2023', '5864493a02', 'Tuntung ', '2004-10-05', '', ''),
('Muhamad Afrizal Safii', '522423020', 'e9b24a3745fd51cfc4c1411ee3b435bd', '', '082271020609', 'Aktif', 'QWZyaXphbF8yMA==', 's1teknikkomputer', '2023', '64809b0cfc', '', '2005-07-20', '', ''),
('Moh. Rezza Abdullatif Bano', '522423021', 'a0e7a035dbcef0fe5c90bf332676b64b', '', '081242628024', 'Aktif', 'a2tlZW9y', 's1teknikkomputer', '2023', NULL, 'Gorontalo', '2003-06-18', 'Gorontalo', ''),
('Muhammad Tsaqib J Kojongkam', '522423022', '71c4be1d57eb6183ec92f4d3cfd8baf5', '', '', 'Aktif', 'S29qb25na2Ft', 's1teknikkomputer', '2023', NULL, '', '2005-03-19', '', ''),
('Muhamad Ilham Akbar Porindo Rahaputra', '522423023', '046ff80a085aef777a0776902dde7508', '', '', 'Aktif', 'UmFoYXB1dHJh', 's1teknikkomputer', '2023', NULL, '', '2005-09-22', '', ''),
('La Samandi Rizkiyadi Lahasini', '522423024', '64d97935a462558d38e6b1b1d3470547', '', '081241096568', 'Aktif', 'ZW5rZWxy', 's1teknikkomputer', '2023', NULL, '', '2005-06-05', '', ''),
('Eka Saputri', '522423025', '237d641fb860ad6e4f76bd53ae0219b0', '', '085342194005', 'Aktif', 'bWlyYW50aTE5ODc=', 's1teknikkomputer', '2023', 'f53eb48005', '', '2004-10-23', '', ''),
('Ahmad Fahrezi Lasantu', '522423026', '9c26a0d5abc764ece2bbf9ae3121849e', '', '089609770909', 'Aktif', 'aWVrdHJu', 's1teknikkomputer', '2023', NULL, '', '2004-12-30', '', ''),
('Revalina Maristi Huntoyungo', '522423027', '0adddc12833f21ea5a73c2da7fc650b1', '', '085656385828', 'Aktif', 'S09tcHV0ZXJyZXZhXw==', 's1teknikkomputer', '2023', NULL, 'Tilamuta', '2005-02-20', 'Desa palopo, Kec. Marisa, Kab. Pohuwato, Prov. Gorontalo', ''),
('Mufti Kalbuadi Dude', '522423028', 'ad81626c2d93d37bf87f652c810e032d', 'muftidude@gmail.com', '08979457308', 'Aktif', 'bnJldGts', 's1teknikkomputer', '2023', NULL, 'Gorontalo', '2004-01-24', '', ''),
('Cahya Mukti Dewa Modanggu', '522424001', 'f6c3c0c5970c4cc8e2c493938e869405', 'cahyomuktidewamodanggu@gmail.com', '083847002311', 'Aktif', 'NTIyNDI0MDAxMTIz', 's1teknikkomputer', '2024', '5864493a02', 'Gorontalo Utara', '2006-09-11', '', ''),
('Rizky Pasilia', '522424002', '6d5130f5789d2eb608fb64494427cecb', '', '', 'Aktif', 'NTIyNDI0MDAyMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Pito Mamonto', '522424003', 'fa7346e3e46629f4d9c1b56a43754eeb', '', '', 'Aktif', 'NTIyNDI0MDAzMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Anggun Pratiwi P. Lahunga', '522424004', '86ec579c95bd6133a819b428f8e196e4', 'ap5888932@gmail.com', '085298815795', 'Aktif', 'NTIyNDI0MDA0MTIz', 's1teknikkomputer', '2024', NULL, 'kab.gorontalo', '2006-07-05', '', ''),
('Syamsul Falah Buna', '522424005', '65756d45dc08eeac2eed4d8534adb139', 'alanfalah29@gmail.com', '089685311439', 'Aktif', 'NTIyNDI0MDA1MTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-03-29', 'Gorontalo, kab. bonebolango, kec. bonepantai, desa bilungala. jln. trans sulawesi', ''),
('Hairunnisa Amiri', '522424006', '86209789babb4ae9403903bb5053f876', '', '', 'Aktif', 'NTIyNDI0MDA2MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Bayu Setowinedar To\'u', '522424007', '8cf44cb5fa1f49eb54ff1042d3bb094f', '', '', 'Aktif', 'NTIyNDI0MDA3MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Aulia Putri Eksan', '522424008', 'd509c71f2c4cc29943ab1614a869f5e3', 'auliaputrieksan@gmail.com', '085342834969', 'Aktif', 'NTIyNDI0MDA4MTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2005-07-15', 'Desa Molohu, Dusun Sakura', ''),
('Ahmad Ardani Latodjo', '522424009', '95548d3205df1d6083315b7ace06608d', 'ahmadardani0866@gmail.com', '083871069021', 'Aktif', 'NTIyNDI0MDA5MTIz', 's1teknikkomputer', '2024', '64809b0cfc', 'Tapa', '2006-06-08', 'Desa Dunggala', ''),
('Moh. Tasyriq Prananda Rauf', '522424010', 'd64c33bb037c584bd3ec7ee8d690d8b1', 'tasyriqrauf@gmail.com', '081244137697', 'Aktif', 'NTIyNDI0MDEwMTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-01-12', 'Dusun Bendungan', '');
INSERT INTO `mahasiswa` (`nama`, `nim`, `password`, `email`, `no_hp`, `status`, `ket`, `prodi`, `angkatan`, `dosen_pa`, `tempat_lahir`, `tgl_lahir`, `alamat`, `ktp`) VALUES
('Vaniya Sahabi', '522424011', '124f433d4f5741b45c27579e5fba1f1e', 'vaniyasahabi438@gmail.com', '0895803404439', 'Aktif', 'NTIyNDI0MDExMTIz', 's1teknikkomputer', '2024', '7fbcc3969e', 'Gorontalo', '2006-11-24', 'Jl.Bali II Kel. Paguyaman Kec. Kota Tengah', ''),
('Candri U Lameo', '522424012', 'e438cc7848629a3b83d292d1057a1390', 'chandrilameo@gmail.com', '082346429424', 'Aktif', 'NTIyNDI0MDEyMTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-09-07', '', ''),
('Imam Mulya Aditya', '522424013', 'b1bdd3076fce50a7a20bc69a4f9fffe4', 'imammulya805@gmail.com', '087769351469', 'Aktif', 'NTIyNDI0MDEzMTIz', 's1teknikkomputer', '2024', NULL, 'LIMBOTO', '2006-08-01', 'Kelurahan Hunggaluwa Kecamatan Limboto Kabupaten Gorontalo', ''),
('Sartin Y Bakari', '522424014', 'd0d6355181ad87bbeb17b828f1b35e13', 'sartinbakari1805@gmail.com', '083826583233', 'Aktif', 'NTIyNDI0MDE0MTIz', 's1teknikkomputer', '2024', '6eba3d13ea', 'kabupaten gorontalo', '2005-08-18', '', ''),
('Abid Abdillah Daeng Pawewang', '522424015', 'a4922765300c5859801c45aef43ab972', 'abidabdillahpawewang@gmail.com', '085823548449', 'Aktif', 'NTIyNDI0MDE1MTIz', 's1teknikkomputer', '2024', NULL, 'Kotamobagu', '2007-06-24', '', ''),
('Muhamad Fadhil Damopolii', '522424016', 'ada290e4cf8f4ebcf391997f033ca6ca', '', '', 'Aktif', 'NTIyNDI0MDE2MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Nur Safitri Rasyid', '522424017', '884833d50c4d9163d49f90723de25474', '', '', 'Aktif', 'NTIyNDI0MDE3MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Muammar Van Gobel', '522424018', '19b74efdba98793929352c10414f75c3', 'muammarvangobel204@gmail.com', '085141486553', 'Aktif', 'NTIyNDI0MDE4MTIz', 's1teknikkomputer', '2024', NULL, 'Boroko ', '2006-05-20', 'Boroko Utara jln.wisata indah', ''),
('Fadlan Sibu', '522424019', 'ea36c60045c1807b4fed0f9133f2f5cf', 'fadlansibu28@gmail.com', '081247498165', 'Aktif', 'NTIyNDI0MDE5MTIz', 's1teknikkomputer', '2024', NULL, 'kwandang', '2004-10-28', '', ''),
('Syahrul Ramadan Gumer', '522424020', 'b7ef17ce069f5b02ab5a63048dc23201', '', '', 'Aktif', 'NTIyNDI0MDIwMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Imam Muslih Poluli', '522424021', '5f13bbb96974c84e1ab9fd64d7cbca9f', 'imammuslih1010@gmail.com', '082152542130', 'Aktif', 'SW5FbmdpbmVlcmluZ1dlVHJ1c3Q=', 's1teknikkomputer', '2024', '645eef9c30', 'Gorontalo ', '2006-06-10', 'Kota Gorontalo Kel. Heledulaa Utara Kec. Kota Timur', ''),
('Wahyudin Ahmad', '522424022', 'f0def2bea899431053b26d7bf563fe02', 'wa3912162@gmail.com', '081244353659', 'Aktif', 'NTIyNDI0MDIyMTIz', 's1teknikkomputer', '2024', 'f53eb48005', 'GORONTALO', '2005-09-02', 'DESA TOTO UTARA KECAMATAN TILONGKABILA', ''),
('Audia Inaku', '522424023', '57627207e640489bc1f8732df9e83444', 'haudiainaku@gmail.com', '082192023899', 'Aktif', 'NTIyNDI0MDIzMTIz', 's1teknikkomputer', '2024', NULL, 'Tabulo', '2005-09-22', 'TABULO', ''),
('Muh. Fikri Haikal Adudu', '522424024', 'b8cb2b2e243fe1af9315b4015a9556d9', 'fikrihaikal2101@gmail.com', '089697904252', 'Aktif', 'NTIyNDI0MDI0MTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-01-21', 'Manembo Nembo Atas, Kec Matuari, Kota Bitung, Sulawesi Utara', ''),
('Syachrizal Fardhiansyah Umar', '522424025', 'dc0a6f40cdb22b1cecd046afe61409eb', 'fardhyumar30@gmail.com', '081354659675', 'Aktif', 'NTIyNDI0MDI1MTIz', 's1teknikkomputer', '2024', NULL, 'Toli-toli', '2006-11-30', '', ''),
('Muhammad Dafa Renanda Putra', '522424026', 'ec3a49d6fdbbfac99ddde63c1735279e', '', '', 'Aktif', 'NTIyNDI0MDI2MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Eka Cahyo Deswinta Pagari', '522424027', 'e2e29b0270636b9ad9760b4e0aec2a37', 'cahyoeka031205@gmail.com', '082259043383', 'Aktif', 'NTIyNDI0MDI3MTIz', 's1teknikkomputer', '2024', 'fb4b7ab909', 'Ampana', '2005-12-03', 'desa Tojo', ''),
('Susantri H Rasid', '522424028', '6195e044ef888b26ca4e1853b84dc7d2', 'susantrirasyid@gmail.com', '085240110161', 'Aktif', 'NTIyNDI0MDI4MTIz', 's1teknikkomputer', '2024', NULL, 'gorontalo', '2004-03-11', 'dusun rombongan, desa otopade, kec, bongomeme', ''),
('Fadil Adnandar Tangahu', '522424029', 'e8da28febfbbed44fbec8b55596f8bf0', '', '', 'Aktif', 'NTIyNDI0MDI5MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Muh. Ichsan Kurnia Imran', '522424030', '64765f8aafc3aaeb20dacad453c454e5', 'ikhsandoank800@gmail.com', '082394962377', 'Aktif', 'NTIyNDI0MDMwMTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2004-11-29', '', ''),
('Aisyah Azzahra Salsabila', '522424031', 'c4c643edb3c75a0057b8d86376c71945', '', '', 'Aktif', 'NTIyNDI0MDMxMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Revalina Dwiputri Hasan', '522424032', '480f45f38e10c010e2155b540bbcb803', 'revalinadwiputri11@gmail.com', '082188554599', 'Aktif', 'cnBhYXd3Xw==', 's1teknikkomputer', '2024', '5864493a02', 'Gorontalo', '2005-12-11', '', ''),
('Febriyani Nur Harun', '522424033', 'f0f5ed658fdecdafdf5614fed9fbfe8d', 'febyharun02@gmail.com', '089604103745', 'Aktif', 'NTIyNDI0MDMzMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Riski Saputra Akaseh', '522424034', '1b5f1b6360139e7fc267ba8a243d7fa8', 'rizkiakaseh24923@gmail.com', '085346074812', 'Aktif', 'NTIyNDI0MDM0MTIz', 's1teknikkomputer', '2024', '5864493a02', 'KAB.GORONTALO', '2003-09-24', 'DUSUN SAKULATI,DESA TOLOTIO,KEC.TIBAWA', ''),
('Dhea Putri Lohari', '522424035', '974d455a59ca1b91e299c13a5d564dd7', 'dhealohari@gmail.com', '085298205305', 'Aktif', 'NTIyNDI0MDM1MTIz', 's1teknikkomputer', '2024', '5864493a02', 'Botutonuo', '2004-08-27', 'Desa Botutonuo, Kec kabila bone, Kab Bone Bolango', ''),
('M. Fauzan Syaifullah', '522424036', 'b73ba3ba38a1b555613396ec79bf7659', '268fauzansyaifullah@gmail.com', '08529847108', 'Aktif', 'NTIyNDI0MDM2MTIz', 's1teknikkomputer', '2024', '36d1464083', 'Gorontalo', '2006-08-26', 'JL. Mangga 2, Kel. Huangobotu, Kec. Dungingi.', 'file/522424036-passfoto.jpg'),
('Muhammad Chaisar Dewa', '522424037', '828b759032e2be45cc875b70a6e3c944', 'muhammadchaisardewa@gmail.com', '082196759434', 'Aktif', 'NTIyNDI0MDM3MTIz', 's1teknikkomputer', '2024', NULL, 'kolonodale', '2006-03-06', 'dusun karya sakti desa lakeya kec. tolangohula kab. gorontalo', ''),
('Andi Fadila Risky', '522424038', 'e579ae9d7cdbc6ef9fdf944f1a54c767', 'andif.dila14@gmail.com', '081524335318', 'Aktif', 'NTIyNDI0MDM4MTIz', 's1teknikkomputer', '2024', 'f154c17f55', 'Manampak', '2006-08-14', 'Masing', 'file/522424038-passfoto.jpg'),
('Rendi Wijaya Kurniawan', '522424039', '411e1ab6fbb05422541c5cbf6fa02b48', 'rendyw2778@gmail.com', '081257923651', 'Aktif', 'NTIyNDI0MDM5MTIz', 's1teknikkomputer', '2024', NULL, 'gorontalo', '2006-09-20', 'jalan palma, kec. huangobotu', ''),
('Muhammad Fadhil Pakaya', '522424040', '9821c967a70cbc675166cee37066cc7a', 'fadhilpakaya06@gmail.com', '082393732754', 'Aktif', 'NTIyNDI0MDQwMTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-06-26', 'JL. Durian Selatan 4, Kec. Dungingi, Kel. Huangobotu', ''),
('Elsa S. Mangendre', '522424041', '473ba95da0618cc4c0848cd6d481df1a', 'ecamangendre@icloud.com', '085373012927', 'Aktif', 'NTIyNDI0MDQxMTIz', 's1teknikkomputer', '2024', 'f154c17f55', 'ONDOLIANG', '2006-08-12', 'Desa Ondoliang', 'file/522424041-passfoto.png'),
('Rahmatullah Al Amin', '522424042', 'b926860f21599203500ab117ee17ab53', '', '081524337218', 'Aktif', 'NTIyNDI0MDQyMTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Yuda Pratama Y. Bonenehu', '522424043', 'f68055d1baa851e6dbf1e681ae6dc1ee', 'ismawatiirma10@gmail.com', '082291319255', 'Aktif', 'NTIyNDI0MDQzMTIz', 's1teknikkomputer', '2024', NULL, 'Buol. Sulawesi Tengah', '2006-01-24', 'Desa Bongo,Kecamatan Bokat,Kabupaten Buol', ''),
('Moh. Al Fajri Hadju', '522424044', '1d5a9ee573a4bbd6ac242b0d78cc7c4f', 'ajir46hadju@gmail.com', '085342305889', 'Aktif', 'NTIyNDI0MDQ0MTIz', 's1teknikkomputer', '2024', NULL, 'PALELEH', '2005-12-16', 'Pagimana\nrt/rw : 00\nkel/desa : Pagimana\nkecamatan : Pagimana', ''),
('Muh. Lutfi Farhan Sjarif', '522424045', '799b8890508ebafd8c96bd9c4ad19a4d', 'lutfisyarif63@gmail.com', '082393522632', 'Aktif', 'NTIyNDI0MDQ1MTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo ', '2006-04-20', 'Jln.Beringin Kec.dungigi Kel.tomulabutao', ''),
('Harniyati Karim', '522424046', '4fa5f5b20b44a38876d0ea711af5dfdc', '', '', 'Aktif', 'NTIyNDI0MDQ2MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Daniyal Riffat Maykel', '522424047', 'eb9db70a215f0d5709fd46c4a924f748', 'daniyal@spectrum-computer.biz', '082291820928', 'Aktif', 'NTIyNDI0MDQ3MTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-08-08', 'Jl. Sam Ratulangi No. 361', ''),
('Mohammad Rizky Bagoes Purnomo', '522424048', 'd046b753836f5654e3804ac62f021a2a', '', '085231176657', 'Aktif', 'NTIyNDI0MDQ4MTIz', 's1teknikkomputer', '2024', NULL, '', NULL, '', ''),
('Radit Febriadi Rahmola', '522424049', '117285d50591fb97b07a6fec0a8feba8', 'raditrahmola02@gmail.com', '089668817943', 'Aktif', 'NTIyNDI0MDQ5MTIz', 's1teknikkomputer', '2024', NULL, 'KELURAHAN TALUMOLO KECAMATAN DUMBO RAYA', '2006-02-24', 'DESA TANAH PUTIH KECAMATAN BOTUPINGGE KABUPATEN BONEBOLANGO', ''),
('Rifqi Zaki Palesangi', '522424050', '9bde63b2b571ce20a21b34d9b571aee6', '021206rzp@gmail.com', '085256682579', 'Aktif', 'NTIyNDI0MDUwMTIz', 's1teknikkomputer', '2024', NULL, 'Gorontalo', '2006-12-02', 'PERUM NABILA PERMAI B/1\nRT 002, RW 001\nKelurahan Liluwo, Kecamatan Kota Tengah\nKota Gorontalo, Provinsi Gorontalo', ''),
('Argiyo Prasetyo Tutu', '522424051', '86640a0a541497143b314bad2f26d3da', 'arghioprasetyo@gmail.com', '085696373704', 'Aktif', 'NTIyNDI0MDUxMTIz', 's1teknikkomputer', '2024', NULL, 'PAGUYAMAN', '2005-07-27', 'DUSUN MOOTINELO, DESA DILOATO, KEC. PAGUYAMAN', ''),
('Rahmat Lapangi', '522424052', '2d826770ec7661e3686667d84e75737c', 'lapangirahmat@gmail.com', '085796762042', 'Aktif', 'NTIyNDI0MDUyMTIz', 's1teknikkomputer', '2024', NULL, 'botumoito', NULL, 'kec,paguyaman .desa sosial,dusun linggoboto', ''),
('Sumardi Bilantua', '522424053', 'e25240d37b28f9193b7de786b5423e5d', 'sumardibilantua24@gmail.com', '085242762170', 'Aktif', 'NTIyNDI0MDUzMTIz', 's1teknikkomputer', '2024', NULL, 'biau', '2005-06-30', '', ''),
('Syahrul Junaid Zaman', '522424054', 'e8035494b1c31941def8cc4d58b9d133', 'syahruljunaidzaman06@gmail.com', '082194905703', 'Aktif', 'NTIyNDI0MDU0MTIz', 's1teknikkomputer', '2024', NULL, 'TABULO', '2006-06-06', 'DUSUN MAKMUR', '');

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah`
--

CREATE TABLE `matakuliah` (
  `id` int NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `semester` int NOT NULL,
  `sks` int NOT NULL,
  `kurikulum` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `konsentrasi` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rps` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matakuliah`
--

INSERT INTO `matakuliah` (`id`, `kode`, `nama`, `semester`, `sks`, `kurikulum`, `konsentrasi`, `keterangan`, `rps`) VALUES
(1, 'NAS65212', 'Agama', 1, 2, '1753425426', '', 'Government recommendation', NULL),
(2, 'NAS65312', 'Bahasa Indonesia', 1, 2, '1753425426', '', 'Government recommendation', NULL),
(3, 'EAL60313', 'Fisika 1', 1, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(4, 'EAL60113', 'Kalkulus 1', 1, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(5, 'EAL61413', 'Dasar-Dasar Pemrograman 1', 1, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(6, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', 1, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(7, 'EAL60213', 'Pengantar Statistik dan Probabilitas', 1, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(8, 'EAL61313', 'Teknologi Informasi', 1, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(9, 'EAL60423', 'Kalkulus 2', 2, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(10, 'NAS65422', 'Pancasila', 2, 2, '1753425426', '', 'Government recommendation', NULL),
(11, 'EAL62022', 'Desain dan pemrograman Basisdata', 2, 2, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(12, 'EAL62121', 'Prak. Desain dan pemrograman Basisdata', 2, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(13, 'EAL65522', 'Tata Tulis Laporan dan Karya Ilmiah', 2, 2, '1753425426', '', 'General Education', NULL),
(14, 'EAL67423', 'Komputasi Aljabar Linear', 2, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(15, 'EAL60821', 'Prak. Komputasi Aljabar Linear', 2, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(16, 'EAL61823', 'Dasar-Dasar Pemrograman 2', 2, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(17, 'EAL61921', 'Prak. Dasar-Dasar Pemrograman 2', 2, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(18, 'EAL60522', 'Kimia', 2, 2, '1753425426', '', 'Math & Basic Sciences', NULL),
(19, 'NAS65132', 'Kewarganegaraan', 3, 2, '1753425426', '', 'Institutional Adaptation', NULL),
(20, 'EAL60633', 'Persamaan Diferensial', 3, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(21, 'EAL61633', 'Sirkuit & Elektronika', 3, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(22, 'EAL61731', 'Prak. Sirkuit & Elektronika', 3, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(23, 'EAL62233', 'Desain Digital & Logika', 3, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(24, 'EAL62331', 'Prak. Desain Digital & Logika', 3, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(25, 'EAL60733', 'Fisika 2', 3, 3, '1753425426', '', 'Math & Basic Sciences', NULL),
(26, 'EAL62433', 'Pemrograman Client/Server', 3, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(27, 'EAL62531', 'Prak. Pemrograman Client/Server', 3, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(28, 'UNG64842', 'Technopreneurship', 4, 2, '1753425426', '', 'Institutional Adaptation', NULL),
(29, 'UNG64942', 'Literasi Digital', 4, 2, '1753425426', '', 'Institutional Adaptation', NULL),
(30, 'EAL61043', 'Pengantar Stuktur Diskrit', 4, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(31, 'EAL67543', 'Pengantar Arsitektur & organisasi Komputer', 4, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(32, 'EAL62743', 'Struktur Data & Algoritma', 4, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(33, 'EAL62841', 'Prak. Struktur Data & Algoritma', 4, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(34, 'EAL62943', 'Desain Sistem Digital', 4, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(35, 'EAL63041', 'Prak. Desain Sistem Digital', 4, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(36, 'EAL61142', 'Statistik Teknik', 4, 2, '1753425426', '', 'Math & Basic Sciences', NULL),
(37, 'EAL67653', 'Sistem Tertanam & Mikroprosesor', 5, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(38, 'EAL63251', 'Prak. Sistem Tertanam & Mikroprosesor', 5, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(39, 'EAL63353', 'Multimedia', 5, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(40, 'EAL63451', 'Prak. Multimedia', 5, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(41, 'EAL63553', 'Jaringan Komputasi', 5, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(42, 'EAL63653', 'Sistem Operasi', 5, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(43, 'EAL63753', 'Sinyal & Sistem', 5, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(44, 'EAL63852', 'Desain Rekayasa Komputasi 1', 5, 2, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(45, 'EAL63951', 'Prak. Desain Rekayasa Komputasi 1', 5, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(46, 'UNG65062', 'Kemaritiman', 6, 2, '1753425426', '', 'Institutional Adaptation', NULL),
(47, 'EAL64063', 'Aplikasi bergerak', 6, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(48, 'EAL64163', 'Keamanan Komputer dan Jaringan', 6, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(49, 'EAL64262', 'Desain Rekayasa Komputasi 2', 6, 2, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(50, 'EAL64361', 'Prak. Desain Rekayasa Komputasi 2', 6, 1, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(51, 'EAL64463', 'Sensor dan aktuator', 6, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(52, 'EAL64563', 'Robotika', 6, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(53, 'EAL64663', 'Internet of Things', 6, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(54, 'EAL65673', 'Kerja Praktek', 7, 3, '1753425426', '', 'General Education', NULL),
(55, 'UNG65773', 'KKS', 7, 3, '1753425426', '', 'General Education', NULL),
(56, 'EAL64773', 'Komunikasi & Etika profesional Untuk Insinyur', 7, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(57, 'EAL61272', 'Metode Riset', 7, 2, '1753425426', '', 'Math & Basic Sciences', NULL),
(58, '', 'Pilihan*', 7, 3, '1753425426', '', '', NULL),
(59, '', 'Pilihan*', 7, 3, '1753425426', '', '', NULL),
(60, '', 'Pilihan*', 7, 3, '1753425426', '', '', NULL),
(61, 'EAL65884', 'Skripsi', 8, 4, '1753425426', '', 'General Education', NULL),
(62, 'EAL65903', 'Biomedik', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(63, 'EAL66003', 'Cloud computing', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(64, 'EAL66103', 'Natural Language processing', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(65, 'EAL66203', 'Pemrograman Jaringan', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(66, 'EAL66303', 'Data Mining', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(67, 'EAL66403', 'Pemrograman Game', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(68, 'EAL66503', 'Forensik Digital', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(69, 'EAL66603', 'Sistem Pendukung Keputusan', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(70, 'EAL66703', 'Pengujian Perangkat Lunak', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(71, 'EAL66803', 'Kecerdasan Buatan', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(72, 'EAL66903', 'image processing', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(73, 'EAL67003', 'Kriptografi', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(74, 'EAL67103', 'UI/UX design', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(75, 'EAL67203', 'GIS', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(76, 'EAL67303', 'Machine learning', 0, 3, '1753425426', '', 'Computer Engineering Body of Knowledge', NULL),
(77, 'NAS66712', 'Agama', 1, 2, '1753425420', '', 'General Education', NULL),
(78, 'NAS66912', 'Bahasa Indonesia', 1, 2, '1753425420', '', 'General Education', NULL),
(79, 'EAD60113', 'Kalkulus 1', 1, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(80, 'EAD60313', 'Fisika 1', 1, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(81, 'EAD60713', 'Probabilitas dan Statistika', 1, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(82, 'EAD60613', 'Kimia Elektro', 1, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(83, 'EAD61113', 'Pemrograman Dasar', 1, 3, '1753425420', '', 'Engineering Sciences', NULL),
(84, 'EAD61211', 'Prak. Pemrograman Dasar', 1, 1, '1753425420', '', 'Engineering Sciences', NULL),
(85, 'EAD67422', 'Tata Tulis Laporan dan Karya Ilmiah', 2, 2, '1753425420', '', 'General Education', NULL),
(86, 'EAD60223', 'Kalkulus 2', 2, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(87, 'EAD60423', 'Fisika 2', 2, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(88, 'EAD61322', 'Rangkaian Listrik 1', 2, 2, '1753425420', '', 'Engineering Sciences', NULL),
(89, 'EAD61822', 'Ilmu Bahan Listrik', 2, 2, '1753425420', '', 'Engineering Sciences', NULL),
(90, 'EAD62822', 'Pengukuran dan Instrumentasi', 2, 2, '1753425420', '', 'Engineering Sciences', NULL),
(91, 'EAD60523', 'Persamaan Diferensial', 2, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(92, 'EAD60923', 'Aljabar Linier', 2, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(93, 'NAS66832', 'Pancasila', 3, 2, '1753425420', '', 'General Education', NULL),
(94, 'EAD60833', 'Matematika Diskrit', 3, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(95, 'EAD61932', 'Sinyal dan Sistem', 3, 2, '1753425420', '', 'Engineering Sciences', NULL),
(96, 'EAD62031', 'Prak. Sinyal dan Sistem', 3, 1, '1753425420', '', 'Engineering Sciences', NULL),
(97, 'EAD61432', 'Rangkaian Listrik 2', 3, 2, '1753425420', '', 'Engineering Sciences', NULL),
(98, 'EAD61531', 'Prak. Rangkaian Listrik', 3, 1, '1753425420', '', 'Engineering Sciences', NULL),
(99, 'EAD61632', 'Elektronika Dasar', 3, 2, '1753425420', '', 'Engineering Sciences', NULL),
(100, 'EAD61731', 'Prak. Elektronika Dasar', 3, 1, '1753425420', '', 'Engineering Sciences', NULL),
(101, 'EAD61033', 'Variabel Kompleks', 3, 3, '1753425420', '', 'Math & Basic Sciences', NULL),
(102, 'EAD63133', 'Metode Numerik', 3, 3, '1753425420', '', 'Engineering Sciences', NULL),
(103, 'EAD63231', 'Prak. Metode Numerik', 3, 1, '1753425420', '', 'Engineering Sciences', NULL),
(104, 'NAS67042', 'Kewarganegaraan', 4, 2, '1753425420', '', 'General Education', NULL),
(105, 'EAD62443', 'Teknik Digital', 4, 3, '1753425420', '', 'Engineering science', NULL),
(106, 'EAD62541', 'Prak. Teknik Digital', 4, 1, '1753425420', '', 'Engineering science', NULL),
(107, 'EAD62142', 'Teknik Kendali Dasar', 4, 2, '1753425420', '', 'Engineering science', NULL),
(108, 'EAD62241', 'Prak. Teknik  Kendali Dasar', 4, 1, '1753425420', '', 'Engineering science', NULL),
(109, 'EAD62642', 'Teknik Telekomunikasi Dasar', 4, 2, '1753425420', '', 'Engineering science', NULL),
(110, 'EAD62741', 'Prak. Teknik Telekomunikasi Dasar', 4, 1, '1753425420', '', 'Engineering science', NULL),
(111, 'EAD62943', 'Teknik Tenaga Listrik Dasar', 4, 3, '1753425420', '', 'Engineering science', NULL),
(112, 'EAD63041', 'Prak. Teknik Tenaga Listrik Dasar', 4, 1, '1753425420', '', 'Engineering science', NULL),
(113, 'EAD62343', 'Elektromagnetika', 4, 3, '1753425420', '', 'Engineering science', NULL),
(114, 'EAD63542', 'Jaringan dan Komunikasi Data', 4, 2, '1753425420', '', 'Engineering science', NULL),
(115, 'UNG67252', 'Literasi Digital', 5, 2, '1753425420', 'TTL', 'General Education', NULL),
(116, 'EAD63952', 'Energi Baru Terbarukan', 5, 2, '1753425420', 'TTL', 'Engineering science', NULL),
(117, 'EAD64051', 'Prak. Energi Baru Terbarukan', 5, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(118, 'EAD63352', 'Teknik Instalasi', 5, 2, '1753425420', 'TTL', 'Engineering science', NULL),
(119, 'EAD63451', 'Prak. Teknik Instalasi', 5, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(120, 'EAD63653', 'Pembangkit Tenaga Listrik', 5, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(121, 'EAD63753', 'Transmisi Tenaga Listrik', 5, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(122, 'EAD63853', 'Distribusi Tenaga Listrik', 5, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(123, 'EAD64153', 'Mesin Listrik', 5, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(124, 'EAD64251', 'Prak. Mesin Listrik', 5, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(125, 'UNG67252', 'Literasi Digital', 5, 2, '1753425420', 'TET', 'General Education', NULL),
(126, 'EAD63352', 'Teknik Instalasi', 5, 2, '1753425420', 'TET', 'Engineering science', NULL),
(127, 'EAD63451', 'Prak. Teknik Instalasi', 5, 1, '1753425420', 'TET', 'Engineering science', NULL),
(128, 'EAD66152', 'Rekayasa Trafik', 5, 2, '1753425420', 'TET', 'Engineering science', NULL),
(129, 'EAD68953', 'Pengolahan Sinyal Digital', 5, 3, '1753425420', 'TET', 'Engineering science', NULL),
(130, 'EAD66052', 'Jaringan Telekomunikasi', 5, 2, '1753425420', 'TET', 'Engineering science', NULL),
(131, 'EAD65752', 'Antena dan Propagasi', 5, 2, '1753425420', 'TET', 'Engineering science', NULL),
(132, 'EAD65853', 'Mikroprosesor dan Mikrokontroler', 5, 3, '1753425420', 'TET', 'Engineering science', NULL),
(133, 'EAD65951', 'Prak. Mikroprosesor dan Mikrokontroler', 5, 1, '1753425420', 'TET', 'Engineering science', NULL),
(134, 'EAD65553', 'Piranti Optoelektronika', 5, 3, '1753425420', 'TET', 'Engineering science', NULL),
(135, 'UNG67162', 'Kemaritiman', 6, 2, '1753425420', 'TTL', 'General Education', NULL),
(136, 'UNG67362', 'Technopreneurship', 6, 2, '1753425420', 'TTL', 'General Education', NULL),
(137, 'EAD64763', 'Analisis Sistem Tenaga', 6, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(138, 'EAD64363', 'Teknik Tegangan Tinggi', 6, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(139, 'EAD64461', 'Prak. Teknik Tegangan Tinggi', 6, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(140, 'EAD64563', 'Teknik Proteksi', 6, 3, '1753425420', 'TTL', 'Engineering science', NULL),
(141, 'EAD64661', 'Prak. Teknik Proteksi', 6, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(142, 'EAD64862', 'Elektronika Daya', 6, 2, '1753425420', 'TTL', 'Engineering science', NULL),
(143, 'EAD64961', 'Prak. Elektronika Daya', 6, 1, '1753425420', 'TTL', 'Engineering science', NULL),
(144, 'EAD67662', 'Ekonomi Teknik', 6, 2, '1753425420', 'TTL', 'General Education', NULL),
(145, 'UNG67162', 'Kemaritiman', 6, 2, '1753425420', 'TET', 'General Education', NULL),
(146, 'UNG67362', 'Technopreneurship', 6, 2, '1753425420', 'TET', 'General Education', NULL),
(147, 'EAD65363', 'Perancangan Sistem Digital', 6, 3, '1753425420', 'TET', 'Engineering science', NULL),
(148, 'EAD65461', 'Prak. Perancangan Sistem Digital', 6, 1, '1753425420', 'TET', 'Engineering science', NULL),
(149, 'EAD65663', 'Jaringan Komunikasi Nirkabel', 6, 3, '1753425420', 'TET', 'Engineering science', NULL),
(150, 'EAD69063', 'Elektronika Telekomunikasi', 6, 3, '1753425420', 'TET', 'Engineering science', NULL),
(151, 'EAD65163', 'Elektronika Industri', 6, 3, '1753425420', 'TET', 'Engineering science', NULL),
(152, 'EAD65261', 'Prak.Elektronika Industri', 6, 1, '1753425420', 'TET', 'Engineering science', NULL),
(153, 'EAD67662', 'Ekonomi Teknik', 6, 2, '1753425420', 'TET', 'General Education', NULL),
(154, 'UNG66574', 'KKS', 7, 4, '1753425420', '', 'Engineering Design', NULL),
(155, 'EAD67572', 'K3 dan Etika Profesi', 7, 2, '1753425420', '', 'General Education', NULL),
(156, 'EAD66372', 'Metodologi Riset', 7, 2, '1753425420', '', 'Engineering Design', NULL),
(157, 'EAD66473', 'Kerja Praktek', 7, 3, '1753425420', '', 'Engineering Design', NULL),
(158, '', 'Mata Kuliah Pilihan*', 7, 3, '1753425420', '', 'Engineering science', NULL),
(159, '', 'Mata Kuliah Pilihan*', 7, 3, '1753425420', '', 'Engineering science', NULL),
(160, 'EAD66684', 'Skripsi', 8, 4, '1753425420', '', 'Engineering Design', NULL),
(161, 'EAD69903', 'Komputasi Cerdas', 0, 3, '1753425420', '', 'Engineering science', NULL),
(162, 'EAD67803', 'Internet of Things', 0, 3, '1753425420', '', 'Engineering science', NULL),
(163, 'EAD69203', 'Aplikasi Komputer dalam  Sistem Tenaga Listrik', 0, 3, '1753425420', '', 'Engineering science', NULL),
(164, 'EAD68003', 'Pemrograman Web', 0, 3, '1753425420', '', 'Engineering science', NULL),
(165, 'EAD68103', 'Standarisasi', 0, 3, '1753425420', '', 'Engineering science', NULL),
(166, 'EAD610003', 'TOEFL Preparation', 0, 3, '1753425420', '', 'Engineering science', NULL),
(167, 'EAD68303', 'Robotika', 0, 3, '1753425420', '', 'Engineering science', NULL),
(168, 'EAD69403', 'Pengolahan Citra', 0, 3, '1753425420', '', 'Engineering science', NULL),
(169, 'EAD69503', 'Sistem Komunikasi Bergerak', 0, 3, '1753425420', '', 'Engineering science', NULL),
(170, 'EAD69603', 'Rekayasa Sistem Radar', 0, 3, '1753425420', '', 'Engineering science', NULL),
(171, 'EAD69703', 'Optimisasi Heuristik', 0, 3, '1753425420', '', 'Engineering science', NULL),
(172, 'EAD69803', 'Forensik Digital', 0, 3, '1753425420', '', 'Engineering science', NULL),
(173, 'NAS60112', 'Agama', 1, 2, '1753425335', 'umum', '', NULL),
(174, 'EAD61514', 'Pemrograman Dasar* ', 1, 4, '1753425335', 'umum', '', NULL),
(175, 'EAD62533', 'Rangkaian Listrik', 3, 3, '1753425335', 'umum', '', NULL),
(176, 'EAD64662 ', 'Sistem Kendali Adaptif', 6, 2, '1753425335', 'stk', '', NULL),
(177, 'EAD61823', 'Matematika Teknik Lanjut', 2, 3, '1753425335', 'umum', '', NULL),
(178, 'EAD67103', 'Dinamika dan Stabilitas Sistem Tenaga Listrik', 7, 3, '1753425335', 'umum', '', NULL),
(179, 'EAD68304', 'Skripsi', 8, 4, '1753425335', 'umum', '', NULL),
(180, 'EAD62133', 'Matematika Diskrit dan Logika ', 3, 3, '1753425335', 'umum', '', NULL),
(181, 'UNG60772', 'Literasi Digital', 7, 2, '1753425335', 'umum', '', NULL),
(182, 'EAD60913', 'Matematika Teknik', 1, 3, '1753425335', 'umum', '', NULL),
(183, 'EAD61014', 'Fisika Listrik*', 1, 4, '1753425335', 'umum', '', NULL),
(184, 'EAD61112', 'Tata Tulis Laporan dan Karya Ilmiah', 1, 2, '1753425335', 'umum', '', NULL),
(185, 'EAD61213', 'Probabilitas dan Statistika', 1, 3, '1753425335', 'umum', '', NULL),
(186, 'EAD61313', 'Kimia Elektro', 1, 3, '1753425335', 'umum', '', NULL),
(187, 'EAD61624', 'Elektronika Dasar*', 2, 4, '1753425335', 'umum', '', NULL),
(188, 'EAD61723', 'Ilmu Bahan Listrik', 2, 3, '1753425335', 'umum', '', NULL),
(189, 'EAD61923', 'Aljabar Linier', 2, 3, '1753425335', 'umum', '', NULL),
(190, 'EAD62024', 'Pengukuran dan Instrumentasi*', 2, 4, '1753425335', 'umum', '', NULL),
(191, 'EAD62234', 'Isyarat dan Sistem*', 3, 4, '1753425335', 'umum', '', NULL),
(192, 'EAD62334', 'Teknik Digital dan Mikroprosesor*', 3, 4, '1753425335', 'umum', '', NULL),
(193, 'EAD62434', 'Teknik Telekomunikasi Dasar*', 3, 4, '1753425335', 'umum', '', NULL),
(194, 'EAD62643', 'Medan Elektromagnetis', 4, 3, '1753425335', 'umum', '', NULL),
(195, 'EAD62744', 'Metode Numeris*', 4, 4, '1753425335', 'umum', '', NULL),
(196, 'EAD62944', 'Teknik Tenaga Listrik Dasar*', 4, 4, '1753425335', 'umum', '', NULL),
(197, 'EAD63044', 'Teknik Kendali Dasar*', 4, 4, '1753425335', 'umum', '', NULL),
(198, 'EAD63154', 'Teknik Instalasi*', 5, 4, '1753425335', 'umum', '', NULL),
(199, 'EAD63252', 'Jaringan dan Komunikasi Data', 5, 2, '1753425335', 'umum', '', NULL),
(200, 'EAD63353', 'Pembangkit Tenaga Listrik', 5, 3, '1753425335', 'stl', '', NULL),
(201, 'EAD63453', 'Transmisi Daya Listrik', 5, 3, '1753425335', 'stl', '', NULL),
(202, 'EAD63553', 'Distribusi Tenaga Listrik', 5, 3, '1753425335', 'stl', '', NULL),
(203, 'EAD63654', 'Mesin Listrik*', 5, 4, '1753425335', 'stl', '', NULL),
(204, 'EAD63764', 'Teknik Tegangan Tinggi*', 6, 4, '1753425335', 'stl', '', NULL),
(205, 'EAD63864', 'Teknik Proteksi*', 6, 4, '1753425335', 'stl', '', NULL),
(206, 'EAD63963', 'Analisis Sistem Tenaga', 6, 3, '1753425335', 'stl', '', NULL),
(207, 'EAD64054', 'Teknik Kendali Analog*', 5, 4, '1753425335', 'stk', '', NULL),
(208, 'EAD64153', 'Otomasi Sistem*', 5, 3, '1753425335', 'stk', '', NULL),
(209, 'EAD64253', 'Teknik Variabel State', 5, 3, '1753425335', 'stk', '', NULL),
(210, 'EAD64353', 'Sistem Kendali Cerdas*', 5, 3, '1753425335', 'stk', '', NULL),
(211, 'EAD64464', 'Teknik Kendali Digital*', 6, 4, '1753425335', 'stk', '', NULL),
(212, 'EAD64563', 'Sistem Kendali Optimal', 6, 3, '1753425335', 'stk', '', NULL),
(213, 'EAD64762', 'Instrumentasi Sistem Kendali', 6, 2, '1753425335', 'stk', '', NULL),
(214, 'EAD64853', 'Teknik Pengolahan Isyarat Digital', 5, 3, '1753425335', 'set', '', NULL),
(215, 'EAD64954', 'Sistem Telekomunikasi Lanjut*', 5, 4, '1753425335', 'set', '', NULL),
(216, 'EAD65053', 'Perancangan Sistem Digital*', 5, 3, '1753425335', 'set', '', NULL),
(217, 'EAD65153', 'Penyambungan dan Pensinyalan', 5, 3, '1753425335', 'set', '', NULL),
(218, 'EAD65263', 'Teknik Antena', 6, 3, '1753425335', 'set', '', NULL),
(219, 'EAD65362', 'Perambatan Gelombang Elektromagnetik', 6, 2, '1753425335', 'set', '', NULL),
(220, 'EAD65462', 'Rekayasa Trafik', 6, 2, '1753425335', 'set', '', NULL),
(221, 'EAD65562', 'Transmisi Telekomunikasi', 6, 2, '1753425335', 'set', '', NULL),
(222, 'EAD65662', 'Jaringan Telekomunikasi', 6, 2, '1753425335', 'set', '', NULL),
(223, 'EAD65753', 'Algoritma dan Struktur Data', 5, 3, '1753425335', 'ski', '', NULL),
(224, 'EAD65852', 'Sistem Operasi', 5, 2, '1753425335', 'ski', '', NULL),
(225, 'EAD65952', 'Pemrograman Lanjut', 5, 2, '1753425335', 'ski', '', NULL),
(226, 'EAD66053', 'Antar Muka dan Periferal*', 5, 3, '1753425335', 'ski', '', NULL),
(227, 'EAD66153', 'Multi Media*', 5, 3, '1753425335', 'ski', '', NULL),
(228, 'EAD66263', 'Arsitektur dan Organisasi Komputer', 6, 3, '1753425335', 'ski', '', NULL),
(229, 'EAD66363', 'Jaringan Komputer*', 6, 3, '1753425335', 'ski', '', NULL),
(230, 'EAD66463', 'Sistem Basis Data*', 6, 3, '1753425335', 'ski', '', NULL),
(231, 'EAD66562', 'Rekayasa Perangkat Lunak', 6, 2, '1753425335', 'ski', '', NULL),
(232, 'NAS60222', 'Pancasila', 2, 2, '1753425335', 'umum', '', NULL),
(233, 'NAS60332', 'Kewarganegaraan', 3, 2, '1753425335', 'umum', '', NULL),
(234, 'NAS60422', 'Bahasa Indonesia', 2, 2, '1753425335', 'umum', '', NULL),
(235, 'UNG60642', 'Bahasa Inggris Terapan', 4, 2, '1753425335', 'umum', '', NULL),
(236, 'UNG61404', 'KKS', 7, 4, '1753425335', 'umum', '', NULL),
(237, 'EAD66662', 'K3 dan Etika Profesi', 6, 2, '1753425335', 'umum', '', NULL),
(238, 'EAD66762', 'Metodelogi Riset', 6, 2, '1753425335', 'umum', '', NULL),
(239, 'EAD66862', 'Pengetahuan Lingkungan Hidup', 6, 2, '1753425335', 'umum', '', NULL),
(240, 'EAD66972', 'Ekonomi Teknik', 7, 2, '1753425335', 'umum', '', '1790132782_356ccde6b9767816'),
(241, 'EAD67073', 'Kerja Praktik (KP)', 7, 3, '1753425335', 'umum', '', NULL),
(242, 'EAD67203', 'Komunikasi Digital', 7, 3, '1753425335', 'umum', '', NULL),
(243, 'EAD67303', 'Kecerdasan Buatan Terdistribusi', 7, 3, '1753425335', 'umum', '', NULL),
(244, 'EAD67403', 'Sistem Pendukung Keputusan', 7, 3, '1753425335', 'umum', '', NULL),
(245, 'EAD67503', 'Standarisasi', 7, 3, '1753425335', 'umum', '', NULL),
(246, 'EAD67603', 'Aplikasi Komputer dalam Sitem Tenaga Listrik', 7, 3, '1753425335', 'umum', '', NULL),
(247, 'EAD67703', 'Pemrograman WEB', 7, 3, '1753425335', 'umum', '', NULL),
(248, 'EAD67803', 'Radar dan Navigasi', 7, 3, '1753425335', 'umum', '', NULL),
(249, 'EAD67903', 'Robotika', 7, 3, '1753425335', 'umum', '', NULL),
(250, 'EAD68003', 'Pemodelan Dan Identifikasi Sistem', 7, 3, '1753425335', 'umum', '', NULL),
(251, 'EAD68103', 'Sistem Komunikasi Bergerak', 7, 3, '1753425335', 'umum', '', NULL),
(252, 'EAD68203', 'Pengenalan Pola', 7, 3, '1753425335', 'umum', '', NULL),
(253, 'UNG60562', 'Wawasan Budaya', 6, 2, '1753425335', 'umum', '', NULL),
(254, 'EAD62844', 'Energi Baru Terbarukan*', 4, 4, '1753425335', 'umum', '', NULL),
(255, 'UNG60852', 'Technopreneur', 5, 2, '1753425335', 'umum', '', NULL),
(256, 'UNG54620402', 'PENDIDIKAN KEWARGANEGARAAN', 3, 2, '1753425335', 'umum', '', NULL),
(257, 'EAD610204', 'Perancangan Teknik Elektro', 0, 4, '1753425420', '', 'Engineering science', NULL),
(268, 'NAS0503600212', 'Pancasila', 1, 2, '1785722041', '', '', NULL),
(269, 'EAD600913', 'Kalkulus 1', 1, 3, '1785722041', '', '', NULL),
(270, 'EAD601113', 'Fisika 1', 1, 3, '1785722041', '', '', NULL),
(271, 'NAS0503600412', 'Bahasa Indonesia', 1, 2, '1785722041', '', '', NULL),
(272, 'EAD601513', 'Probabilitas dan Statistika', 1, 3, '1785722041', '', '', NULL),
(273, 'EAD601413', 'Kimia ', 1, 3, '1785722041', '', '', NULL),
(274, 'EAD601913', 'Pemrograman Dasar', 1, 3, '1785722041', '', '', NULL),
(275, 'EAD602011', 'Prak. Pemrograman Dasar', 1, 1, '1785722041', '', '', NULL),
(276, 'NAS0503600122', 'Agama', 2, 2, '1785722041', '', '', NULL),
(277, 'EAD601023', 'Kalkulus 2', 2, 3, '1785722041', '', '', NULL),
(278, 'EAD601223', 'Fisika 2', 2, 3, '1785722041', '', '', NULL),
(279, 'EAD602122', 'Rangkaian Listrik 1', 2, 2, '1785722041', '', '', NULL),
(280, 'NAS0503600322', 'Kewarganegaraan', 2, 2, '1785722041', '', '', NULL),
(281, 'EAD603622', 'Pengukuran dan Instrumentasi', 2, 2, '1785722041', '', '', NULL),
(282, 'EAD601323', 'Persamaan Diferensial', 2, 3, '1785722041', '', '', NULL),
(283, 'EAD601723', 'Aljabar Linier', 2, 3, '1785722041', '', '', NULL),
(284, 'EAD607532', 'Tata Tulis Laporan dan Karya Ilmiah', 3, 2, '1785722041', '', '', NULL),
(285, 'EAD601633', 'Matematika Diskrit', 3, 3, '1785722041', '', '', NULL),
(286, 'EAD602732', 'Sinyal dan Sistem', 3, 2, '1785722041', '', '', NULL),
(287, 'EAD602831', 'Prak. Sinyal dan Sistem', 3, 1, '1785722041', '', '', NULL),
(288, 'EAD602232', 'Rangkaian Listrik 2', 3, 2, '1785722041', '', '', NULL),
(289, 'EAD602331', 'Prak. Rangkaian Listrik', 3, 1, '1785722041', '', '', NULL),
(290, 'EAD602432', 'Elektronika Dasar', 3, 2, '1785722041', '', '', NULL),
(291, 'EAD602531', 'Prak. Elektronika Dasar', 3, 1, '1785722041', '', '', NULL),
(292, 'EAD601833', 'Variabel Kompleks', 3, 3, '1785722041', '', '', NULL),
(293, 'EAD603933', 'Metode Numerik', 3, 3, '1785722041', '', '', NULL),
(294, 'EAD604031', 'Prak. Metode Numerik', 3, 1, '1785722041', '', '', NULL),
(295, 'EAD602642', 'Ilmu Bahan Listrik', 4, 2, '1785722041', '', '', NULL),
(296, 'EAD603242', 'Teknik Digital', 4, 2, '1785722041', '', '', NULL),
(297, 'EAD603341', 'Prak. Teknik Digital', 4, 1, '1785722041', '', '', NULL),
(298, 'EAD602942', 'Teknik Kendali Dasar', 4, 2, '1785722041', '', '', NULL),
(299, 'EAD603041', 'Prak. Teknik  Kendali Dasar', 4, 1, '1785722041', '', '', NULL),
(300, 'EAD603442', 'Teknik Telekomunikasi Dasar', 4, 2, '1785722041', '', '', NULL),
(301, 'EAD603541', 'Prak. Teknik Telekomunikasi Dasar', 4, 1, '1785722041', '', '', NULL),
(302, 'EAD603743', 'Teknik Tenaga Listrik Dasar', 4, 3, '1785722041', '', '', NULL),
(303, 'EAD603841', 'Prak. Teknik Tenaga Listrik Dasar', 4, 1, '1785722041', '', '', NULL),
(304, 'EAD603143', 'Elektromagnetika', 4, 3, '1785722041', '', '', NULL),
(305, 'EAD604342', 'Jaringan dan Komunikasi Data', 4, 2, '1785722041', '', '', NULL),
(306, 'EAD607152', 'Metodologi Riset', 5, 2, '1785722041', 'TTL', '', NULL),
(307, 'EAD604752', 'Energi Baru Terbarukan', 5, 2, '1785722041', 'TTL', '', NULL),
(308, 'EAD604851', 'Prak. Energi Baru Terbarukan', 5, 1, '1785722041', 'TTL', '', NULL),
(309, 'EAD604152', 'Teknik Instalasi', 5, 2, '1785722041', 'TTL', '', NULL),
(310, 'EAD604251', 'Prak. Teknik Instalasi', 5, 1, '1785722041', 'TTL', '', NULL),
(311, 'EAD604453', 'Pembangkit Tenaga Listrik', 5, 3, '1785722041', 'TTL', '', NULL),
(312, 'EAD604552', 'Transmisi Tenaga Listrik', 5, 2, '1785722041', 'TTL', '', NULL),
(313, 'EAD604653', 'Distribusi Tenaga Listrik', 5, 3, '1785722041', 'TTL', '', NULL),
(314, 'EAD604952', 'Mesin Listrik', 5, 2, '1785722041', 'TTL', '', NULL),
(315, 'EAD605051', 'Prak. Mesin Listrik', 5, 1, '1785722041', 'TTL', '', NULL),
(316, 'EAD607152', 'Metodologi Riset', 5, 2, '1785722041', 'TET', '', NULL),
(317, 'EAD604152', 'Teknik Instalasi ', 5, 2, '1785722041', 'TET', '', NULL),
(318, 'EAD604251', 'Prak. Teknik Instalasi', 5, 1, '1785722041', 'TET', '', NULL),
(319, 'EAD604752', 'Energi Baru Terbarukan', 5, 2, '1785722041', 'TET', '', NULL),
(320, 'EAD604851', 'Prak. Energi Baru Terbarukan', 5, 1, '1785722041', 'TET', '', NULL),
(321, 'EAD605852', 'Pengolahan Sinyal Digital', 5, 2, '1785722041', 'TET', '', NULL),
(322, 'EAD606852', 'Jaringan Telekomunikasi', 5, 2, '1785722041', 'TET', '', NULL),
(323, 'EAD606552', 'Antena dan Propagasi', 5, 2, '1785722041', 'TET', '', NULL),
(324, 'EAD606652', 'Mikroprosesor dan Mikrokontroler', 5, 2, '1785722041', 'TET', '', NULL),
(325, 'EAD606751', 'Prak. Mikroprosesor dan Mikrokontroler', 5, 1, '1785722041', 'TET', '', NULL),
(326, 'EAD606352', 'Piranti Optoelektronika', 5, 2, '1785722041', 'TET', '', NULL),
(327, 'UNG0503600762', 'Kemaritiman', 6, 2, '1785722041', 'TTL', '', NULL),
(328, 'UNG0503600662', 'Technopreneurship', 6, 2, '1785722041', 'TTL', '', NULL),
(329, 'EAD605563', 'Analisis Sistem Tenaga Listrik', 6, 3, '1785722041', 'TTL', '', NULL),
(330, 'EAD605163', 'Teknik Tegangan Tinggi', 6, 3, '1785722041', 'TTL', '', NULL),
(331, 'EAD605261', 'Prak. Teknik Tegangan Tinggi', 6, 1, '1785722041', 'TTL', '', NULL),
(332, 'EAD605362', 'Teknik Proteksi', 6, 2, '1785722041', 'TTL', '', NULL),
(333, 'EAD605461', 'Prak. Teknik Proteksi', 6, 1, '1785722041', 'TTL', '', NULL),
(334, 'EAD605662', 'Elektronika Daya', 6, 2, '1785722041', 'TTL', '', NULL),
(335, 'EAD605761', 'Prak. Elektronika Daya', 6, 1, '1785722041', 'TTL', '', NULL),
(336, 'EAD607762', 'Ekonomi Teknik', 6, 2, '1785722041', 'TTL', '', NULL),
(337, 'UNG0503600762', 'Kemaritiman', 6, 2, '1785722041', 'TET', '', NULL),
(338, 'UNG0503600662', 'Technopreneurship', 6, 2, '1785722041', 'TET', '', NULL),
(339, 'EAD606162', 'Perancangan Sistem Digital', 6, 2, '1785722041', 'TET', '', NULL),
(340, 'EAD606261', 'Prak. Perancangan Sistem Digital', 6, 1, '1785722041', 'TET', '', NULL),
(341, 'EAD606462', 'Jaringan Komunikasi Nirkabel', 6, 2, '1785722041', 'TET', '', NULL),
(342, 'EAD607063', 'Elektronika Telekomunikasi', 6, 3, '1785722041', 'TET', '', NULL),
(343, 'EAD605962', 'Elektronika Industri', 6, 2, '1785722041', 'TET', '', NULL),
(344, 'EAD606061', 'Prak.Elektronika Industri', 6, 1, '1785722041', 'TET', '', NULL),
(345, 'EAD606962', 'Rekayasa Trafik', 6, 2, '1785722041', 'TET', '', NULL),
(346, 'EAD607762', 'Ekonomi Teknik', 6, 2, '1785722041', 'TET', '', NULL),
(347, 'UNG0503600874', 'Kuliah Kerja Nyata', 7, 4, '1785722041', '', '', NULL),
(348, 'EAD607672', 'K3 dan Etika Profesi', 7, 2, '1785722041', '', '', NULL),
(349, 'UNG0503600572', 'Literasi Digital', 7, 2, '1785722041', '', '', NULL),
(350, 'EAD607274', 'Magang', 7, 4, '1785722041', '', '', NULL),
(351, 'EAD607374', 'Perancangan Teknik Elektro', 7, 4, '1785722041', '', '', NULL),
(352, 'EAD607486', 'Tugas Akhir', 8, 6, '1785722041', '', '', '1790133010_7f6b129d17f8733a'),
(353, 'EAD607873', 'Komputasi Cerdas', 7, 3, '1785722041', 'Pilihan', '', NULL),
(354, 'EAD607973', 'Internet of Things', 7, 3, '1785722041', 'Pilihan', '', NULL),
(355, 'EAD608073', 'Aplikasi Komputer dalam  Sistem Tenaga Listrik', 7, 3, '1785722041', 'Pilihan', '', NULL),
(356, 'EAD608173', 'Pemrograman Web', 7, 3, '1785722041', 'Pilihan', '', NULL),
(357, 'EAD608273', 'Standarisasi', 7, 3, '1785722041', 'Pilihan', '', NULL),
(358, 'EAD608373', 'Dinamika dan Stabilitas Sistem Tenaga Listrik', 7, 3, '1785722041', 'Pilihan', '', NULL),
(359, 'EAD608473', 'TOEFL Preparation', 7, 3, '1785722041', 'Pilihan', '', NULL),
(360, 'EAD608573', 'Robotika', 7, 3, '1785722041', 'Pilihan', '', NULL),
(361, 'EAD608673', 'Pengolahan Citra', 7, 3, '1785722041', 'Pilihan', '', NULL),
(362, 'EAD608773', 'Sistem Komunikasi Bergerak ', 7, 3, '1785722041', 'Pilihan', '', NULL),
(363, 'EAD608873', 'Rekayasa Sistem Radar', 7, 3, '1785722041', 'Pilihan', '', NULL),
(364, 'EAD608973', 'Optimisasi Heuristik', 7, 3, '1785722041', 'Pilihan', '', NULL),
(365, 'EAD609073', 'Forensik Digital', 7, 3, '1785722041', 'Pilihan', '', NULL),
(367, 'NAS0511600212', 'Pancasila', 1, 2, '1785725609', '', '', NULL),
(368, 'NAS0511600412', 'Bahasa Indonesia', 1, 2, '1785725609', '', '', NULL),
(369, 'EAL600913', 'Fisika 1', 1, 3, '1785725609', '', '', NULL),
(370, 'EAL601013', 'Kalkulus 1', 1, 3, '1785725609', '', '', NULL),
(371, 'EAL601113', 'Dasar-Dasar Pemrograman 1', 1, 3, '1785725609', '', '', NULL),
(372, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', 1, 1, '1785725609', '', '', NULL),
(373, 'EAL601313', 'Pengantar Statistik dan Probabilitas', 1, 3, '1785725609', '', '', NULL),
(374, 'EAL601413', 'Teknologi Informasi', 1, 3, '1785725609', '', '', NULL),
(375, 'EAL601523', 'Kalkulus 2', 2, 3, '1785725609', '', '', NULL),
(376, 'NAS0511600122', 'Agama', 2, 2, '1785725609', '', '', NULL),
(377, 'EAL601622', 'Desain dan pemrograman Basis data', 2, 2, '1785725609', '', '', NULL),
(378, 'EAL601721', 'Prak. Desain dan pemrograman Basisdata', 2, 1, '1785725609', '', '', NULL),
(379, 'NAS0511600322', 'Kewarganegaraan', 2, 2, '1785725609', '', '', NULL),
(380, 'EAL601823', 'Komputasi Aljabar Linear', 2, 3, '1785725609', '', '', NULL),
(381, 'EAL601921', 'Prak. Komputasi Aljabar Linear', 2, 1, '1785725609', '', '', NULL),
(382, 'EAL602023', 'Dasar-Dasar Pemrograman 2', 2, 3, '1785725609', '', '', NULL),
(383, 'EAL602121', 'Prak. Dasar-Dasar Pemrograman 2', 2, 1, '1785725609', '', '', NULL),
(384, 'EAL602222', 'Kimia', 2, 2, '1785725609', '', '', NULL),
(385, 'EAL602332', 'Tata Tulis Laporan dan Karya Ilmiah', 3, 2, '1785725609', '', '', NULL),
(386, 'EAL602433', 'Persamaan Diferensial', 3, 3, '1785725609', '', '', NULL),
(387, 'EAL602533', 'Sirkuit & Elektronika', 3, 3, '1785725609', '', '', NULL),
(388, 'EAL602631', 'Prak. Sirkuit & Elektronika', 3, 1, '1785725609', '', '', NULL),
(389, 'EAL602732', 'Desain Digital & Logika', 3, 2, '1785725609', '', '', NULL),
(390, 'EAL602831', 'Prak. Desain Digital & Logika', 3, 1, '1785725609', '', '', NULL),
(391, 'EAL602933', 'Fisika 2', 3, 3, '1785725609', '', '', NULL),
(392, 'EAL603033', 'Pemrograman Client/Server', 3, 3, '1785725609', '', '', NULL),
(393, 'EAL603131', 'Prak. Pemrograman Client/Server', 3, 1, '1785725609', '', '', NULL),
(394, 'UNG0511600642', 'Technopreneurship', 4, 2, '1785725609', '', '', NULL),
(395, 'UNG0511600542', 'Literasi Digital', 4, 2, '1785725609', '', '', NULL),
(396, 'EAL603242', 'Pengantar Stuktur Diskrit', 4, 2, '1785725609', '', '', NULL),
(397, 'EAL603343', 'Pengantar Arsitektur & organisasi Komputer', 4, 3, '1785725609', '', '', NULL),
(398, 'EAL603443', 'Struktur Data & Algoritma', 4, 3, '1785725609', '', '', NULL),
(399, 'EAL603541', 'Prak. Struktur Data & Algoritma', 4, 1, '1785725609', '', '', NULL),
(400, 'EAL603643', 'Desain Sistem Digital', 4, 3, '1785725609', '', '', NULL),
(401, 'EAL603741', 'Prak. Desain Sistem Digital', 4, 1, '1785725609', '', '', NULL),
(402, 'EAL603842', 'Statistik Teknik', 4, 2, '1785725609', '', '', NULL),
(403, 'EAL603953', 'Sistem Tertanam & Mikroprosesor', 5, 3, '1785725609', '', '', NULL),
(404, 'EAL604051', 'Prak. Sistem Tertanam & Mikroprosesor', 5, 1, '1785725609', '', '', NULL),
(405, 'EAL604152', 'Multimedia', 5, 2, '1785725609', '', '', NULL),
(406, 'EAL604251', 'Prak. Multimedia', 5, 1, '1785725609', '', '', NULL),
(407, 'EAL604353', 'Jaringan Komputasi', 5, 3, '1785725609', '', '', NULL),
(408, 'EAL604452', 'Sistem Operasi', 5, 2, '1785725609', '', '', NULL),
(409, 'EAL604553', 'Sinyal & Sistem', 5, 3, '1785725609', '', '', NULL),
(410, 'EAL604652', 'Desain Rekayasa Komputasi 1', 5, 2, '1785725609', '', '', NULL),
(411, 'EAL604751', 'Prak. Desain Rekayasa Komputasi 1', 5, 1, '1785725609', '', '', NULL),
(412, 'EAL604852', 'Metode Riset', 5, 2, '1785725609', '', '', NULL),
(413, 'UNG0511600762', 'Kemaritiman', 6, 2, '1785725609', '', '', NULL),
(414, 'EAL604962', 'Aplikasi bergerak', 6, 2, '1785725609', '', '', NULL),
(415, 'EAL605063', 'Keamanan Komputer dan Jaringan', 6, 3, '1785725609', '', '', NULL),
(416, 'EAL605162', 'Desain Rekayasa Komputasi 2', 6, 2, '1785725609', '', '', NULL),
(417, 'EAL605261', 'Prak. Desain Rekayasa Komputasi 2', 6, 1, '1785725609', '', '', NULL),
(418, 'EAL605363', 'Sensor dan aktuator', 6, 3, '1785725609', '', '', NULL),
(419, 'EAL605463', 'Robotika', 6, 3, '1785725609', '', '', NULL),
(420, 'EAL605563', 'Internet of Things', 6, 3, '1785725609', '', '', NULL),
(421, 'EAL605674', 'Magang', 7, 4, '1785725609', '', '', NULL),
(422, 'UNG0511600874', 'KKN', 7, 4, '1785725609', '', '', NULL),
(423, 'EAL605774', 'Project Embedded System', 7, 4, '1785725609', '', '', NULL),
(424, 'EAL605886', 'Tugas Akhir', 8, 6, '1785725609', '', '', NULL),
(425, 'EAL605973', 'Biomedik', 7, 3, '1785725609', 'Pilihan', '', NULL),
(426, 'EAL606073', 'Cloud computing', 7, 3, '1785725609', 'Pilihan', '', NULL),
(427, 'EAL606173', 'Natural Language processing', 7, 3, '1785725609', 'Pilihan', '', NULL),
(428, 'EAL606273', 'Pemrograman Jaringan', 7, 3, '1785725609', 'Pilihan', '', NULL),
(429, 'EAL606373', 'Data Mining', 7, 3, '1785725609', 'Pilihan', '', NULL),
(430, 'EAL606473', 'Pemrograman Game', 7, 3, '1785725609', 'Pilihan', '', NULL),
(431, 'EAL606573', 'Forensik Digital', 7, 3, '1785725609', 'Pilihan', '', NULL),
(432, 'EAL606673', 'Sistem Pendukung Keputusan', 7, 3, '1785725609', 'Pilihan', '', NULL),
(433, 'EAL606773', 'Pengujian Perangkat Lunak', 7, 3, '1785725609', 'Pilihan', '', NULL),
(434, 'EAL606873', 'Kecerdasan Buatan', 7, 3, '1785725609', 'Pilihan', '', NULL),
(435, 'EAL606973', 'Image processing', 7, 3, '1785725609', 'Pilihan', '', NULL),
(436, 'EAL607073', 'Kriptografi', 7, 3, '1785725609', 'Pilihan', '', NULL),
(437, 'EAL607173', 'UI/UX design', 7, 3, '1785725609', 'Pilihan', '', NULL),
(438, 'EAL607273', 'GIS', 7, 3, '1785725609', 'Pilihan', '', NULL),
(439, 'EAL607373', 'Machine learning', 7, 3, '1785725609', 'Pilihan', '', NULL),
(440, 'NAS0513600412', 'Bahasa Indonesia', 1, 2, '1784211306', '', '', NULL),
(441, 'NAS0513600212', 'Pancasila', 1, 2, '1784211306', '', '', NULL),
(442, 'UNG0513600912', 'Perkembangan Peserta Didik', 1, 2, '1784211306', '', '', NULL),
(443, 'UNG0513601012', 'Dasar-Dasar Kependidikan', 1, 2, '1784211306', '', '', NULL),
(444, 'EAN601512', 'Pengantar Elektro Teknik', 1, 2, '1784211306', '', '', NULL),
(445, 'EAN601612', 'Matematika dasar', 1, 2, '1784211306', '', '', '1790132927_901239f8cde8af9b'),
(446, 'EAN601712', 'Fisika', 1, 2, '1784211306', '', '', NULL),
(447, 'EAN601812', 'Menggambar Teknik', 1, 2, '1784211306', '', '', NULL),
(448, 'EAN601912', 'Ilmu Bahan Listrik', 1, 2, '1784211306', '', '', NULL),
(449, 'EAN602012', 'Algoritma dan Pemrograman', 1, 2, '1784211306', '', '', NULL),
(455, 'absae', 'Uncle Mutto', 3, 3, '1753425335', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah_mbkm`
--

CREATE TABLE `matakuliah_mbkm` (
  `kode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `sks` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matakuliah_mbkm`
--

INSERT INTO `matakuliah_mbkm` (`kode`, `nama`, `sks`) VALUES
('EADKM60105', 'Dasar Pemrograman Backend', 5),
('EADKM60203', 'Software Engineering', 3),
('EADKM60303', 'sustainable Start Up', 3),
('EADKM60403', 'Sustainable Tourism', 3),
('EADKM60503', 'Product Management', 3),
('EADKM60603', 'Integrasi Membangun Digital Startup (AI Creation dan Fab Creation)', 3),
('EADKM60705', 'Project Capstone AI-Infra', 5),
('EADKM60804', 'Capstone / Final Project', 4),
('EADKM60904', 'Soft skill & Career Development', 4),
('EADKM61003', 'Aplikasi Back-End untuk Pemula', 3),
('EADKM61104', 'Basic Frontend Web Development with HTML & CSS', 4),
('EADKM61202', 'Database Management & Technology', 2),
('EADKM61303', 'Profesionalisme Insinyur', 3),
('EADKM61402', 'Ekosistem Industri Elektronika', 2),
('EADKM61503', 'Perancangan PCB', 3),
('EADKM61603', 'Engineering Design Process', 3),
('EADKM61702', 'Growth Mindset Overview', 2),
('EADKM61803', 'Data Collection Device IoT', 3),
('EADKM61902', 'Teknik Mikrokontroler Wemos D1', 2),
('EADKM62002', 'Teknik Elektronika dan Peralatan Perbengkelan ', 2),
('EADKM62104', 'Proyek Akhir IoT Smart Device', 4),
('EADKM62203', 'teknis kelistrikan', 3),
('EADKM62302', 'teknis kompresor dan pendingin', 2),
('EADKM62402', 'Dasar Teknik Maintenance', 2),
('EADKM62502', 'Management Waktu dan Organisasi', 2),
('EADKM62603', 'Safety, Health and Enviroment', 3),
('EADKM62702', 'Continuos Improvement', 2),
('EADKM62802', 'Corporate Comprehension', 2),
('EADKM62902', 'Kemampuan Teknis Mill', 2),
('EADKM63002', 'Organization Savvy', 2),
('EADKM63103', 'Microsoft Office Operating Skill', 3),
('EADKM63202', 'E-Learning System Abilty', 2),
('EADKM63302', 'Impact Through Imfluence', 2),
('EADKM63402', 'Kemampuan Kewirausahaan', 2),
('EADKM63502', 'Drive For Result', 2),
('EADKM63603', 'Desain HLD dan LLD', 3),
('EADKM63703', 'Monitoring dan Evaluasi I-HLD', 3),
('EADKM63803', 'Ekspor Data Design', 3),
('EADKM63903', 'Back-End Development', 3),
('EADKM64003', 'Dasar Pemrograman  JavaSript', 3),
('EADKM64102', 'Pemrograman Python', 2),
('EADKM64202', 'Logika dan Konsep Teknologi AI ', 2),
('EADKM64302', 'Siklus Proyek AI', 2),
('EADKM64402', 'ChatGPT', 2),
('EADKM64502', 'Etika Profesi & Keterampilan Perusahaan', 2),
('EADKM64603', 'Proyek Akhir', 3),
('EADKM64702', 'Digital Marketing', 2),
('EADKM64802', 'INISIATIF', 2),
('EADKM64903', 'Network Architecture Fundamentals', 3),
('EADKM65003', 'Network Problem Management', 3),
('EADKM65102', 'Cybersecurity', 2),
('EADKM65203', 'Network Operation and Maintenance', 3),
('EADKM65302', 'Core Network Management', 2),
('EADKM65402', 'Sikap', 2),
('EADKM65502', 'Kemampuan Komunikasi', 2),
('EADKM65602', 'Orientasi Hasil Kerja ', 2),
('EADKM65702', 'Berfikir Analisis', 2),
('EADKM65802', 'Perencanaan dan Pengorganisasian', 2),
('EADKM65903', 'Monitoring dan Mengevaluasi', 3),
('EADKM66003', 'Introduction to Stress and Strains', 3),
('EADKM66103', 'Stress and Local Equilibrium', 3),
('EADKM66202', 'Volumetric and Deviatoric', 2),
('EADKM66302', 'Linear and Nonlinear Analysis', 2),
('EADKM66402', 'Time Domain and Frequency Domain Analysis', 2),
('EADKM66502', 'Thermal Strain Analysis', 2),
('EADKM66603', 'Renewable Energy Capstone Project', 3),
('EADKM66704', 'Konsep Dasar dan Solusi Smart City', 4),
('EADKM66804', 'Konsep Pariwisata yang Berkelanjutan', 4),
('EADKM66903', 'Ekosistem Bisnis dan Investasi', 3),
('EADKM67003', 'Strategi Peningkatan Kualitas Branding Kota', 3),
('EADKM67103', 'Project Management dan Soft Skill', 3),
('EADKM67202', 'Kerja Sama Tim', 2),
('EADKM67302', 'Radio Access Network Management', 2);

-- --------------------------------------------------------

--
-- Table structure for table `pemilik_file`
--

CREATE TABLE `pemilik_file` (
  `id` int NOT NULL,
  `id_arsip` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `partner` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `pemilik` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemilik_file`
--

INSERT INTO `pemilik_file` (`id`, `id_arsip`, `partner`, `pemilik`, `keterangan`) VALUES
(5, '1790751423_aebaf2bd6ca2dbda', 'arsip_lain', '2e7b39ada2', ''),
(6, '1790751423_aebaf2bd6ca2dbda', 'arsip_lain', '14c4f7be30', ''),
(7, '1790750932_26cfbd84bd66e5b9', '', 'f154c17f55', ''),
(8, '1790750932_26cfbd84bd66e5b9', '', 'a5e8c20ec3', ''),
(9, '1790750932_26cfbd84bd66e5b9', '', '81399d8536', ''),
(10, '1790750932_26cfbd84bd66e5b9', '', '637dfbddee', ''),
(11, '1790750932_26cfbd84bd66e5b9', '', '5864493a02', '');

-- --------------------------------------------------------

--
-- Table structure for table `persyaratan_berkas_kp`
--

CREATE TABLE `persyaratan_berkas_kp` (
  `id` int NOT NULL,
  `nama_berkas` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tabel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `field` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `progres` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persyaratan_berkas_kp`
--

INSERT INTO `persyaratan_berkas_kp` (`id`, `nama_berkas`, `tabel`, `field`, `progres`) VALUES
(111, 'Persetujuan PA', 'kp', 'persetujuan_pa', 2),
(112, 'Surat Pengantar Kerja Praktek', 'kp', 'pengantar', 2),
(113, 'Pengantar Kerja Praktek (dari fakultas)', 'kp', 'pengantar_fakultas', 2);

-- --------------------------------------------------------

--
-- Table structure for table `persyaratan_berkas_skripsi`
--

CREATE TABLE `persyaratan_berkas_skripsi` (
  `id` int NOT NULL,
  `nama_berkas` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `tabel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `field` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `progres` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persyaratan_berkas_skripsi`
--

INSERT INTO `persyaratan_berkas_skripsi` (`id`, `nama_berkas`, `tabel`, `field`, `progres`) VALUES
(1, 'Kartu Rencana Studi (KRS)', 'proposal', 'file_krs', 1),
(2, 'Transkip Nilai', 'proposal', 'file_transkip', 1),
(3, 'Kartu Tanda Mahasiswa (KTM)', 'proposal', 'file_ktm', 1),
(16, 'Kartu Rencana Studi (KRS) terbaru', 'hasil', 'file_krs', 7),
(17, 'Lembar Pembimbingan 1', 'hasil', 'file_bimbingan1', 7),
(18, 'Lembar Pembimbingan 2', 'hasil', 'file_bimbingan2', 7),
(19, 'Laporan Hasil Penelitian (Lengkap Bab 1 - 5)', 'hasil', 'file_penelitian', 7),
(20, 'Lembar Persetujuan Pembimbing', 'hasil', 'file_persetujuan_pembimbing', 7),
(21, 'Berita Acara', 'hasil', 'file_ba', 11),
(22, 'Lembar Penilaian 1', 'hasil', 'file_penilaian1', 11),
(23, 'Lembar Penilaian 2', 'hasil', 'file_penilaian2', 11),
(24, 'Lembar Penilaian 3', 'hasil', 'file_penilaian3', 11),
(25, 'Lembar Penilaian 4', 'hasil', 'file_penilaian4', 11),
(29, 'Lembar Koreksi 1', 'hasil', 'file_koreksi1', 12),
(30, 'Lembar Koreksi 2', 'hasil', 'file_koreksi2', 12),
(31, 'Lembar Koreksi 3', 'hasil', 'file_koreksi3', 12),
(32, 'Lembar Koreksi 4', 'hasil', 'file_koreksi4', 12),
(34, 'Unggah Persetujuan Pembimbing', 'tutup', 'file_persetujuan_pembimbing', 15),
(35, 'Lembar Penilaian 1', 'tutup', 'file_penilaian1', 19),
(36, 'Lembar Penilaian 2', 'tutup', 'file_penilaian2', 19),
(37, 'Lembar Penilaian 3', 'tutup', 'file_penilaian3', 19),
(38, 'Lembar Penilaian 4', 'tutup', 'file_penilaian4', 19),
(40, 'Berita Acara', 'tutup', 'file_ba', 19),
(41, 'File SKRIPSI (Lengkap)', 'tutup', 'file_skripsi', 20),
(42, 'File Jurnal', 'tutup', 'file_jurnal', 20),
(48, 'Lembar Ajuan Judul', 'proposal', 'file_ajuan_judul', 1),
(49, 'Transkip Nilai PDDIKTI', 'proposal', 'file_transkip_pddikti', 1),
(50, 'Turnitin', 'proposal', 'file_turnitin', 1),
(51, 'Turnitin', 'tutup', 'file_turnitin', 16),
(52, 'Transkip Nilai PDDIKTI', 'hasil', 'file_transkip_pddikti', 7),
(54, 'Transkip Nilai PDDIKTI', 'tutup', 'file_transkip_pddikti', 16),
(56, 'File Bimbingan 1', 'proposal', 'file_bimbingan1', 4),
(57, 'File Bimbingan 2', 'proposal', 'file_bim', 4),
(58, 'File Proposal', 'proposal', 'file_proposal', 4),
(59, 'Lembar Penilaian 5', 'hasil', 'file_penilaian5', 11),
(60, 'Lembar Koreksi 5', 'hasil', 'file_koreksi5', 12),
(61, 'Lembar Penilaian 5', 'tutup', 'file_penilaian5', 19),
(62, 'Bebas Laboratorium', 'tutup', 'file_bebas_lab', 20);

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `kode` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kaprodi` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenjang` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`kode`, `nama`, `kaprodi`, `jenjang`, `status`) VALUES
('s1pendidikanvokasionalrekayasaelektro', 'S1 Pendidikan Vokasional Rekayasa Elektro', '197004052009121001', 'S1', 'Aktif'),
('s1teknikelektro', 'S1 Teknik Elektro', '199510302025061004', 'S1', 'Aktif'),
('s1teknikkomputer', 'S1 Teknik Komputer', '197506242005011003', 'S1', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `progres_kp`
--

CREATE TABLE `progres_kp` (
  `id` int NOT NULL,
  `waktu` int NOT NULL,
  `proses` int NOT NULL,
  `mahasiswa` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progres_kp`
--

INSERT INTO `progres_kp` (`id`, `waktu`, `proses`, `mahasiswa`) VALUES
(1, 1792031270, 1, '05201425002'),
(2, 1792036241, 1, '05201425002'),
(3, 1792036398, 1, '05201425002'),
(6, 1792048050, 2, '05201425002');

-- --------------------------------------------------------

--
-- Table structure for table `progres_skripsi`
--

CREATE TABLE `progres_skripsi` (
  `waktu` int NOT NULL,
  `proses` int NOT NULL,
  `mahasiswa` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progres_skripsi`
--

INSERT INTO `progres_skripsi` (`waktu`, `proses`, `mahasiswa`) VALUES
(1791769453, 1, '05201425003'),
(1791770040, 1, '05201425003'),
(1791769884, 2, '05201425003'),
(1791770293, 2, '05201425003'),
(1791770550, 3, '05201425003'),
(1791777686, 4, '05201425003'),
(1791959124, 4, '05201425003'),
(1791959102, 5, '05201425003'),
(1791959137, 5, '05201425003'),
(1791959150, 6, '05201425003'),
(1791959191, 7, '05201425003');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pendidikan`
--

CREATE TABLE `riwayat_pendidikan` (
  `id` int UNSIGNED NOT NULL,
  `nip` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenjang` enum('TK','SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_studi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `universitas` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` smallint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `riwayat_pendidikan`
--

INSERT INTO `riwayat_pendidikan` (`id`, `nip`, `jenjang`, `program_studi`, `universitas`, `tahun`) VALUES
(1, '10041993', 'S1', 'Teknik Elektro', 'Universitas Negeri Gorontalo', 2012),
(2, '10041993', 'SMA/SMK', 'Rekayasa Perangkat Lunak', 'SMK N 1 Kota Gorontalo', 2008),
(3, '10041993', 'SMP', '-', 'SMP N 13 Kota Gorontalo', 2005),
(4, '10041993', 'SD', '-', 'SDN N 15 Kota Utara', 1999);

-- --------------------------------------------------------

--
-- Table structure for table `ruang_kuliah`
--

CREATE TABLE `ruang_kuliah` (
  `id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kapasitas` int NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `status` set('aktif','tidak aktif') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruang_kuliah`
--

INSERT INTO `ruang_kuliah` (`id`, `nama`, `kapasitas`, `kategori`, `status`) VALUES
('LAB.ELEKTRONIKADANKOMUNIKASI', 'LAB. ELEKTRONIKA DAN KOMUNIKASI', 30, 'Elektronika', 'aktif'),
('LAB.KOMPUTER1', 'LAB. KOMPUTER 1', 20, 'Komputer', 'aktif'),
('LAB.KOMPUTER2', 'LAB. KOMPUTER 2', 20, 'Komputer', 'aktif'),
('LAB.TEKNIKKENDALI', 'LAB. TEKNIK KENDALI', 30, 'Kendali', 'aktif'),
('LAB.TENAGALISTRIK', 'LAB. TENAGA LISTRIK', 30, 'Tenaga Listrik', 'aktif'),
('LAB.UNPROTECT', 'LAB. UNPROTECT', 30, 'Umum', 'aktif'),
('LabInstalasi', 'Lab Instalasi', 10, 'Umum', 'aktif'),
('R.K2.11', 'R.K 2.11', 30, 'Umum', 'aktif'),
('R.K3.16', 'R.K 3.16', 30, 'Umum', 'aktif'),
('TESTING3', 'Komputer 3', 20, 'Komputer', 'tidak aktif');

-- --------------------------------------------------------

--
-- Table structure for table `skp`
--

CREATE TABLE `skp` (
  `id` int NOT NULL,
  `dosen` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penilaian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hasil` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skp`
--

INSERT INTO `skp` (`id`, `dosen`, `penilaian`, `hasil`, `tahun`) VALUES
(136, '2e7b39ada2', '1790906317_52249baadbb89da0', NULL, 2024);

-- --------------------------------------------------------

--
-- Table structure for table `skripsi`
--

CREATE TABLE `skripsi` (
  `nim` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `judul_hasil` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `judul_tutup` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penunjukkan_pembimbing` int DEFAULT NULL,
  `penunjukkan_hasil` int DEFAULT NULL,
  `no_undangan_hasil` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_hasil` date DEFAULT NULL,
  `jam_hasil` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ruangan_hasil` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penunjukkan_tutup` int DEFAULT NULL,
  `no_undangan_tutup` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_tutup` date DEFAULT NULL,
  `jam_tutup` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ruangan_tutup` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','tidak aktif','alumni','kadaluarsa') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skripsi`
--

INSERT INTO `skripsi` (`nim`, `judul`, `judul_hasil`, `judul_tutup`, `penunjukkan_pembimbing`, `penunjukkan_hasil`, `no_undangan_hasil`, `tanggal_hasil`, `jam_hasil`, `ruangan_hasil`, `penunjukkan_tutup`, `no_undangan_tutup`, `tanggal_tutup`, `jam_tutup`, `ruangan_tutup`, `status`) VALUES
('05201425003', '', NULL, NULL, 1791777685, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `sop_kp`
--

CREATE TABLE `sop_kp` (
  `no` int NOT NULL,
  `status` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `eksekutor` varchar(250) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sop_kp`
--

INSERT INTO `sop_kp` (`no`, `status`, `eksekutor`) VALUES
(1, 'cetak persetujuan PA', 'mahasiswa'),
(2, 'mengunggah persyaratan turun kerja praktek', 'mahasiswa'),
(3, 'verifikasi berkas persyaratan turun Kerja Praktek', 'admin'),
(4, 'mengunggah surat dari instansi Kerja Praktek', 'mahasiswa'),
(5, 'verifikasi surat balasan', 'admin'),
(6, 'mengatur dosen pembimbing', 'admin'),
(7, 'mencetak surat pengantar untuk penarikkan', 'admin'),
(8, 'mengunggah surat penarikkan dari fakultas', 'mahasiswa'),
(9, 'verifikasi surat penarikkan', 'admin'),
(10, 'sedang menyusun laporan. Penyusunan laporan dianggap selesai jika mahasiswa sudah mengunggah SEMUA berkas persyaratan seminar Kerja Praktek', 'mahasiswa'),
(11, 'verifikasi berkas kerja praktek', 'admin'),
(12, 'siap seminar', 'mahasiswa'),
(13, 'mengunggah berita acara dan lembar penilaian', 'admin'),
(14, 'mengunggah lembar koreksi', 'mahasiswa'),
(15, 'verifikasi lembar koreksi', 'admin'),
(16, 'Kerja Praktek Selesai', 'mahasiswa');

-- --------------------------------------------------------

--
-- Table structure for table `sop_skripsi`
--

CREATE TABLE `sop_skripsi` (
  `no` int NOT NULL,
  `keterangan` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `eksekutor` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sop_skripsi`
--

INSERT INTO `sop_skripsi` (`no`, `keterangan`, `eksekutor`) VALUES
(1, 'mengunggah persyaratan penyusunan Proposal (KRS, Transkip, CV, KTM)', 'mahasiswa'),
(2, 'Verifikasi Berkas pendaftaran Proposal', 'admin'),
(3, 'Membuat Surat Penunjukan Pembimbing', 'admin'),
(4, 'mengunggah laporan Proposal dan lembar bimbingan proposal untuk masuk ke tahap penelitian', 'mahasiswa'),
(5, 'Verifikasi laporan Proposal dan lembar bimbingan', 'admin'),
(6, 'Tahap Penelitian', 'Mahasiswa'),
(7, 'Mengupload berkas pendaftaran Ujian Hasil Penelitian (KRS dan Persetujuan Pembimbing) ', 'mahasiswa'),
(8, 'Verifikasi berkas pendaftaran Ujian Hasil Penelitian', 'admin'),
(9, 'Membuat surat penunjukan penguji hasil', 'admin'),
(10, 'Siap Ujian Hasil', 'admin'),
(11, 'mengupload Berita Acara dan Lembar Penilaian hasil', 'admin'),
(12, 'upload lembar koreksi', 'mahasiswa'),
(13, 'verifikasi lembar koreksi hasil', 'admin'),
(14, 'Cetak lembar persetujuan Pembimbing', 'mahasiswa'),
(15, 'Upload lembar persetujuan pembimbing', 'mahasiswa'),
(16, 'Verifikasi lembar pembimbing', 'Admin'),
(17, 'Membuat surat penunjukkan penguji tutup', 'admin'),
(18, 'Siap Ujian Tutup', 'admin'),
(19, 'mengupload lembar penilaian', 'admin'),
(20, 'Upload skripsi dan jurnal', 'mahasiswa'),
(21, 'verifikasi SKRIPSI dan Jurnal', 'admin'),
(22, 'mencetak Berita Acara Yudisium', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `surat_penunjukkan`
--

CREATE TABLE `surat_penunjukkan` (
  `id` int NOT NULL,
  `no_surat` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nim` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `pembimbing1` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pembimbing2` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penguji1` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penguji2` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penguji3` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penguji4` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penguji5` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `ttd` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_penunjukkan`
--

INSERT INTO `surat_penunjukkan` (`id`, `no_surat`, `nim`, `pembimbing1`, `pembimbing2`, `penguji1`, `penguji2`, `penguji3`, `penguji4`, `penguji5`, `tanggal`, `ttd`, `keterangan`) VALUES
(1667343411, '', '521414009', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2021-03-23', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343413, '', '521414015', '5864493a02', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2020-11-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343415, '', '521414026', '7fbcc3969e', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2021-03-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343417, '', '521414031', '7bf1f8337d', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2021-04-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343418, '', '521414034', '637dfbddee', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2021-01-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343420, '', '521415001', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2022-01-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343421, '', '521415005', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2022-08-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343423, '', '521415006', '645eef9c30', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, '2022-01-12', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343424, '', '521415008', '7fbcc3969e', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2021-08-25', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343426, '', '521415011', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2022-05-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343428, '', '521415012', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2022-05-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343429, '', '521415018', '637dfbddee', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2022-04-26', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343432, '', '521415019', '645eef9c30', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2022-05-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343434, '', '521415030', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2022-05-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343437, '', '521415033', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2022-01-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343439, '', '521415035', '7fbcc3969e', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2021-11-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343441, '', '521415036', '645eef9c30', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2020-12-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343444, '', '521416003', '7fbcc3969e', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2022-01-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343446, '', '521416008', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2022-02-14', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343449, '', '521416011', '98a4a6f7b7', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2020-10-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343450, '', '521416018', '637dfbddee', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2021-07-23', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343452, '', '521416020', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2021-04-29', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343454, '', '521416036', '98a4a6f7b7', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2022-04-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343458, '', '521417001', 'f154c17f55', '8e6dd82846', NULL, NULL, NULL, NULL, NULL, '2022-03-22', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343461, '', '521417006', '637dfbddee', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2022-04-21', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343465, '', '521417008', '98a4a6f7b7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2021-07-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343466, '', '521417010', '8e6dd82846', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2021-12-29', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343469, '', '521417024', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2022-04-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667343487, '', '521416016', 'd53ee838e9', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2022-06-14', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667344611, '1043/UN47.B5/HK.04/2021', '521414009', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '5864493a02', '7bf1f8337d', '2021-10-04', '197102222001121001', 'PENGUJI HASIL'),
(1667344613, '1047/UN47.B5/HK.04/2021', '521414015', 'fad50ffmahe', NULL, '5864493a02', 'fb4b7ab909', '274eccd1e7', '645eef9c30', '7bf1f8337d', '2021-10-05', '197102222001121001', 'PENGUJI HASIL'),
(1667344615, '1044/UN47.B5/HK.04/2021', '521414026', 'fad50ffmahe', NULL, '7fbcc3969e', '36d1464083', '98a4a6f7b7', '5864493a02', '637dfbddee', '2021-11-12', '197102222001121001', 'PENGUJI HASIL'),
(1667344617, '1041/UN47.B5/HK.04/2021', '521414031', 'fad50ffmahe', NULL, '7bf1f8337d', '5864493a02', '637dfbddee', '98a4a6f7b7', '274eccd1e7', '2021-10-04', '197102222001121001', 'PENGUJI HASIL'),
(1667344619, '1126/UN47.B5/HK.04/2021', '521414034', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '5864493a02', '36d1464083', '98a4a6f7b7', '2021-10-28', '197102222001121001', 'PENGUJI HASIL'),
(1667344621, 'SK Hasil Fajar', '521415001', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-04-08', '197102222001121001', 'PENGUJI HASIL'),
(1667344624, '418/UN47.B5/HK.04/2022', '521415006', 'fad50ffmahe', NULL, '645eef9c30', 'd53ee838e9', '1a127ddb8e', 'fb4b7ab909', '6eba3d13ea', '2022-03-16', '197102222001121001', 'PENGUJI HASIL'),
(1667344626, 'SK hasil Lela', '521415008', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '7bf1f8337d', '274eccd1e7', '637dfbddee', '2022-05-23', '197102222001121001', 'PENGUJI HASIL'),
(1667344628, 'SK hasi randi', '521415011', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2022-06-10', '197102222001121001', 'PENGUJI HASIL'),
(1667344630, 'SK Hasil ezra', '521415018', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-06-08', '197102222001121001', 'PENGUJI HASIL'),
(1667344633, 'SK hasil Andika', '521415019', 'fad50ffmahe', NULL, '645eef9c30', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '6eba3d13ea', '2022-05-19', '197102222001121001', 'PENGUJI HASIL'),
(1667344635, 'SK Hasil darma', '521415030', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '5864493a02', '7bf1f8337d', '36d1464083', '2022-06-07', '197102222001121001', 'PENGUJI HASIL'),
(1667344637, 'SK Hasil Fiz', '521415033', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', 'f53eb48005', '92459d083c', 'e8e4016ded', '2022-05-30', '197102222001121001', 'PENGUJI HASIL'),
(1667344640, 'SK hasil linda', '521415035', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', '36d1464083', '5864493a02', '98a4a6f7b7', '2022-05-23', '197102222001121001', 'PENGUJI HASIL'),
(1667344642, '1280/UN47.B5/HK.04/2021', '521415036', 'fad50ffmahe', NULL, '645eef9c30', 'fb4b7ab909', '92459d083c', 'f53eb48005', '7fbcc3969e', '2021-12-09', '197102222001121001', 'PENGUJI HASIL'),
(1667344644, 'SK Hasil Febri', '521416003', 'fad50ffmahe', NULL, '7fbcc3969e', 'f154c17f55', '274eccd1e7', '36d1464083', '6eba3d13ea', '2022-04-18', '197102222001121001', 'PENGUJI HASIL'),
(1667344646, 'Sk Penguji Nasir', '521416008', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2022-10-08', '197102222001121001', 'PENGUJI HASIL'),
(1667344648, '330/UN47.B5/TD.06/2021', '521416011', 'fad50ffmahe', NULL, '98a4a6f7b7', '274eccd1e7', '36d1464083', '5864493a02', '7fbcc3969e', '2021-03-31', '197102222001121001', 'PENGUJI HASIL'),
(1667344651, '20/UN47.B5/HK.04/2022', '521416018', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '98a4a6f7b7', '7fbcc3969e', '36d1464083', '2022-01-05', '197102222001121001', 'PENGUJI HASIL'),
(1667344653, '479/UN47.B5/HK.04/2022', '521416020', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '6eba3d13ea', 'f53eb48005', '645eef9c30', '2022-03-24', '197102222001121001', 'PENGUJI HASIL'),
(1667344655, 'Sk penguji jun', '521416036', 'fad50ffmahe', NULL, '98a4a6f7b7', 'fb4b7ab909', '5864493a02', '36d1464083', 'e8e4016ded', '2022-10-08', '197102222001121001', 'PENGUJI HASIL'),
(1667344658, 'SK menguji irfan', '521417008', 'fad50ffmahe', NULL, '98a4a6f7b7', '5864493a02', '274eccd1e7', '36d1464083', '7bf1f8337d', '2022-08-20', '197102222001121001', 'PENGUJI HASIL'),
(1667344660, 'SK penguji hasil', '521417010', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '92459d083c', 'f53eb48005', '6eba3d13ea', '2022-10-10', '197102222001121001', 'PENGUJI HASIL'),
(1667346181, '604/UN47.B5/HK.04/2021', '521414009', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '5864493a02', '7bf1f8337d', '2021-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667346183, '610/UN47.B5/HK.04/2021', '521414015', 'fad50ffmahe', NULL, '5864493a02', 'fb4b7ab909', '274eccd1e7', '645eef9c30', '7bf1f8337d', '2021-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667346184, '646/UN47.B5/HK.04/2021', '521414026', 'fad50ffmahe', NULL, '7fbcc3969e', '36d1464083', '98a4a6f7b7', '5864493a02', '637dfbddee', '2021-06-18', '197102222001121001', 'PENGUJI TUTUP'),
(1667346186, '609/UN47.B5/HK.04/2021', '521414031', 'fad50ffmahe', NULL, '7bf1f8337d', '5864493a02', '637dfbddee', '98a4a6f7b7', '274eccd1e7', '2021-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667346187, '604/UN47.B5/HK.04/2021', '521414034', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '5864493a02', '36d1464083', '98a4a6f7b7', '2021-06-09', '197102222001121001', 'PENGUJI TUTUP'),
(1667346188, 'SK Skripsi Fajar', '521415001', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-05-20', '197102222001121001', 'PENGUJI TUTUP'),
(1667346190, 'SK Skripsi Fadlul', '521415006', 'fad50ffmahe', NULL, '645eef9c30', 'd53ee838e9', '1a127ddb8e', 'fb4b7ab909', '6eba3d13ea', '2022-05-20', '197102222001121001', 'PENGUJI TUTUP'),
(1667346191, 'SK skripsi Lela', '521415008', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '7bf1f8337d', '274eccd1e7', '637dfbddee', '2022-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1667346193, 'SK menguji randy', '521415011', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2022-08-20', '197102222001121001', 'PENGUJI TUTUP'),
(1667346194, 'SK skripsi', '521415018', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1667346195, 'SK Skripsi Andika', '521415019', 'fad50ffmahe', NULL, '645eef9c30', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '6eba3d13ea', '2022-05-30', '197102222001121001', 'PENGUJI TUTUP'),
(1667346196, 'sk-tutup gondrong', '521415030', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '5864493a02', '7bf1f8337d', '36d1464083', '2022-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1667346198, 'SK Fizranto', '521415033', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', 'f53eb48005', '92459d083c', 'e8e4016ded', '2022-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667346200, 'SK hasil linda', '521415035', 'fad50ffmahe', NULL, '7fbcc3969e', '274eccd1e7', '36d1464083', '5864493a02', '98a4a6f7b7', '2022-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1667346201, '19/UN47.B5/HK.04/2022', '521415036', 'fad50ffmahe', NULL, '645eef9c30', 'fb4b7ab909', '92459d083c', 'f53eb48005', '7fbcc3969e', '2022-01-05', '197102222001121001', 'PENGUJI TUTUP'),
(1667346203, 'sk tutup febri', '521416003', 'fad50ffmahe', NULL, '7fbcc3969e', 'f154c17f55', '274eccd1e7', '36d1464083', '6eba3d13ea', '2022-07-03', '197102222001121001', 'PENGUJI TUTUP'),
(1667346205, '407/UN47.B5/TD.06/2021', '521416011', 'fad50ffmahe', NULL, '98a4a6f7b7', '274eccd1e7', '36d1464083', '5864493a02', '7fbcc3969e', '2021-04-21', '197102222001121001', 'PENGUJI TUTUP'),
(1667346206, 'SK Penguji Hasil', '521416020', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '6eba3d13ea', 'f53eb48005', '645eef9c30', '2022-04-21', '197102222001121001', 'PENGUJI TUTUP'),
(1667346207, 'pengujo dospem skripsi', '521417008', 'fad50ffmahe', NULL, '98a4a6f7b7', '5864493a02', '274eccd1e7', '36d1464083', '7bf1f8337d', '2022-09-20', '197102222001121001', 'PENGUJI TUTUP'),
(1667347385, '', '521414009', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-07', '197102222001121001', 'PEMBIMBING KP'),
(1667347387, '', '521417001', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2021-01-13', '197102222001121001', 'PEMBIMBING KP'),
(1667347388, '', '521417006', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2021-06-22', '197102222001121001', 'PEMBIMBING KP'),
(1667347389, '', '521417008', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2021-06-25', '197102222001121001', 'PEMBIMBING KP'),
(1667347390, '', '521418001', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1667347391, '', '521418016', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-01', '197102222001121001', 'PEMBIMBING KP'),
(1667347393, '', '521418015', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13', '197102222001121001', 'PEMBIMBING KP'),
(1667347394, '', '521417028', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-26', '197102222001121001', 'PEMBIMBING KP'),
(1667347396, '', '521418037', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-23', '197102222001121001', 'PEMBIMBING KP'),
(1667347397, '', '521416028', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-10', '197102222001121001', 'PEMBIMBING KP'),
(1667347398, '', '521418031', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-25', '197102222001121001', 'PEMBIMBING KP'),
(1667347401, '', '521418008', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-26', '197102222001121001', 'PEMBIMBING KP'),
(1667347403, '', '521418035', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-23', '197102222001121001', 'PEMBIMBING KP'),
(1667347404, '', '521418003', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2022-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1667347406, '', '521418011', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-05', '197102222001121001', 'PEMBIMBING KP'),
(1667347407, '', '521418004', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-08', '197102222001121001', 'PEMBIMBING KP'),
(1667347409, '', '521417013', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-22', '197102222001121001', 'PEMBIMBING KP'),
(1667347411, '', '521418063', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-09', '197102222001121001', 'PEMBIMBING KP'),
(1667347412, '', '521418017', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-29', '197102222001121001', 'PEMBIMBING KP'),
(1667347414, '', '521418014', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-05', '197102222001121001', 'PEMBIMBING KP'),
(1667347416, '', '521418039', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-25', '197102222001121001', 'PEMBIMBING KP'),
(1667347418, '', '521417021', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-25', '197102222001121001', 'PEMBIMBING KP'),
(1667347419, '', '521418064', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-09', '197102222001121001', 'PEMBIMBING KP'),
(1667347420, '', '521418051', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-25', '197102222001121001', 'PEMBIMBING KP'),
(1667347422, '', '521418018', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-09', '197102222001121001', 'PEMBIMBING KP'),
(1667347956, '', '521414009', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '7fbcc3969e', NULL, NULL, '2020-11-30', '197102222001121001', 'PENGUJI KP'),
(1667347959, '', '521417001', 'fad50ffmahe', NULL, 'f154c17f55', 'e8e4016ded', '9eee0f6743', NULL, NULL, '2021-03-18', '197102222001121001', 'PENGUJI KP'),
(1667347962, '', '521417006', 'fad50ffmahe', NULL, '36d1464083', '7fbcc3969e', '7bf1f8337d', NULL, NULL, '2021-10-18', '197102222001121001', 'PENGUJI KP'),
(1667347965, '', '521417008', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', NULL, NULL, '2021-10-18', '197102222001121001', 'PENGUJI KP'),
(1667347969, '', '521418001', 'fad50ffmahe', NULL, '6eba3d13ea', 'd53ee838e9', '1a127ddb8e', NULL, NULL, '2022-04-07', '197102222001121001', 'PENGUJI KP'),
(1667347973, '', '521418016', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '36d1464083', NULL, NULL, '2022-09-28', '197102222001121001', 'PENGUJI KP'),
(1667347976, '', '521417028', 'fad50ffmahe', NULL, 'f154c17f55', 'e8e4016ded', 'fb4b7ab909', NULL, NULL, '2022-10-23', '197102222001121001', 'PENGUJI KP'),
(1667347979, '', '521418037', 'fad50ffmahe', NULL, '36d1464083', '7fbcc3969e', '92459d083c', NULL, NULL, '2022-06-25', '197102222001121001', 'PENGUJI KP'),
(1667347983, '', '521418031', 'fad50ffmahe', NULL, '1a127ddb8e', 'd53ee838e9', '6eba3d13ea', NULL, NULL, '2022-10-22', '197102222001121001', 'PENGUJI KP'),
(1667347986, '', '521418008', 'fad50ffmahe', NULL, 'f154c17f55', 'f53eb48005', '8e6dd82846', NULL, NULL, '2022-10-23', '197102222001121001', 'PENGUJI KP'),
(1667347989, '', '521418035', 'fad50ffmahe', NULL, '36d1464083', 'f154c17f55', '1a127ddb8e', NULL, NULL, '2022-07-24', '197102222001121001', 'PENGUJI KP'),
(1667347992, '', '521418003', 'fad50ffmahe', NULL, 'f53eb48005', 'f154c17f55', 'e8e4016ded', NULL, NULL, '2022-04-18', '197102222001121001', 'PENGUJI KP'),
(1667347996, '', '521418011', 'fad50ffmahe', NULL, '36d1464083', '7fbcc3969e', '7bf1f8337d', NULL, NULL, '2022-11-02', '197102222001121001', 'PENGUJI KP'),
(1667347999, '', '521417013', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '36d1464083', NULL, NULL, '2022-06-25', '197102222001121001', 'PENGUJI KP'),
(1667348002, '', '521418014', 'fad50ffmahe', NULL, '36d1464083', '7fbcc3969e', '7bf1f8337d', NULL, NULL, '2022-11-02', '197102222001121001', 'PENGUJI KP'),
(1667348006, '', '521418039', 'fad50ffmahe', NULL, '98a4a6f7b7', '637dfbddee', '274eccd1e7', NULL, NULL, '2022-10-22', '197102222001121001', 'PENGUJI KP'),
(1667348009, '', '521417021', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', '1a127ddb8e', NULL, NULL, '2022-10-22', '197102222001121001', 'PENGUJI KP'),
(1667348012, '', '521418051', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', NULL, NULL, '2022-10-22', '197102222001121001', 'PENGUJI KP'),
(1667349389, '', '521414017', '6eba3d13ea', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2020-10-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349390, '', '521414040', '274eccd1e7', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2021-04-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349391, '', '521414046', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2021-04-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349392, '', '521414056', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2021-01-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349394, '', '521415003', '14c4f7be30', '113ef62d22', NULL, NULL, NULL, NULL, NULL, '2020-03-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349395, '', '521415013', '7fbcc3969e', '113ef62d22', NULL, NULL, NULL, NULL, NULL, '2020-03-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349396, '', '521415015', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2020-03-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349397, '', '521415017', '637dfbddee', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2020-07-23', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349398, '', '521415021', '7fbcc3969e', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2021-04-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349400, '', '521415022', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2022-04-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349401, '', '521415024', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2022-04-25', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349403, '', '521415027', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2020-08-24', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349404, '', '521415029', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2022-04-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349405, '', '521415031', '7bf1f8337d', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2020-09-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349407, '', '521416002', '8e6dd82846', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2020-11-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349408, '', '521416004', '14c4f7be30', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2020-07-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349409, '', '521416005', 'f154c17f55', '8e6dd82846', NULL, NULL, NULL, NULL, NULL, '2020-11-24', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349410, '', '521416023', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2020-11-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349412, '', '521416025', '1a127ddb8e', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2020-11-03', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667349413, '', '521417002', '637dfbddee', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2021-04-26', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1667350053, '17/UN47.B5/TD.06/2021', '521414017', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', '9eee0f6743', '2021-01-08', '197102222001121001', 'PENGUJI HASIL'),
(1667350056, '1048/UN47.B5/HK.04/202', '521414040', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', '36d1464083', '5864493a02', '7bf1f8337d', '2021-10-04', '197102222001121001', 'PENGUJI HASIL'),
(1667350058, '1184/UN47.B5/HK.04/2021', '521414046', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2021-11-12', '197102222001121001', 'PENGUJI HASIL'),
(1667350060, ' 1042/UN47.B5/HK.04/2021', '521414056', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', '2021-10-04', '197102222001121001', 'PENGUJI HASIL'),
(1667350062, '20/UN47.B5/TD.06/2021', '521415003', 'fad50ffmahe', NULL, '14c4f7be30', '113ef62d22', '637dfbddee', '98a4a6f7b7', '7fbcc3969e', '2021-01-08', '197102222001121001', 'PENGUJI HASIL'),
(1667350064, '004/UN47.B5/TD.06/2020', '521415013', 'fad50ffmahe', NULL, '7fbcc3969e', '113ef62d22', '5864493a02', '36d1464083', '274eccd1e7', '2020-10-03', '197102222001121001', 'PENGUJI HASIL'),
(1667350066, '1362/UN47.B5/TD.06/2020', '521415015', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '2020-12-22', '197102222001121001', 'PENGUJI HASIL'),
(1667350069, '153/UN47.B5/TD.06/2021', '521415017', 'fad50ffmahe', NULL, '637dfbddee', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '7fbcc3969e', '2021-02-02', '197102222001121001', 'PENGUJI HASIL'),
(1667350071, '1093/UN47.B5/HK.04/2022', '521415021', 'fad50ffmahe', NULL, '7fbcc3969e', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '36d1464083', '2021-10-18', '197102222001121001', 'PENGUJI HASIL'),
(1667350073, 'SK hasil Risky', '521415022', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '98a4a6f7b7', '7bf1f8337d', '14c4f7be30', '2022-06-07', '197102222001121001', 'PENGUJI HASIL'),
(1667350075, '1198/UN47.B5/TD.06/2020', '521415023', 'fad50ffmahe', NULL, '98a4a6f7b7', '92459d083c', '637dfbddee', '7bf1f8337d', '14c4f7be30', '2020-11-17', '197102222001121001', 'PENGUJI HASIL'),
(1667350078, 'SK Hasil Nandar', '521415024', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-06-02', '197102222001121001', 'PENGUJI HASIL'),
(1667350081, '78/UN47.B5/TD.06/2021', '521415027', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '274eccd1e7', '7bf1f8337d', '7fbcc3969e', '2021-01-25', '197102222001121001', 'PENGUJI HASIL'),
(1667350083, 'SK Hasil Marfan', '521415029', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '5864493a02', '98a4a6f7b7', '7fbcc3969e', '2022-06-02', '197102222001121001', 'PENGUJI HASIL'),
(1667350085, '194/UN47.B5/TD.06/2021', '521415031', 'fad50ffmahe', NULL, '7bf1f8337d', '98a4a6f7b7', '36d1464083', '274eccd1e7', '5864493a02', '2021-02-18', '197102222001121001', 'PENGUJI HASIL'),
(1667350088, '1278/UN47.B5/TD.06/2020', '521416002', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '9eee0f6743', 'd53ee838e9', '1a127ddb8e', '2020-11-30', '197102222001121001', 'PENGUJI HASIL'),
(1667350090, '53/UN47.B5/TD.06/2021', '521416004', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '7fbcc3969e', '36d1464083', '2021-01-19', '197102222001121001', 'PENGUJI HASIL'),
(1667350092, '1202/UN47.B5/HK.04/2021', '521416005', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2021-11-18', '197102222001121001', 'PENGUJI HASIL'),
(1667350094, '284/UN47.B5/TD.06/2021', '521416023', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '5864493a02', '92459d083c', '637dfbddee', '2021-03-18', '197102222001121001', 'PENGUJI HASIL'),
(1667350097, '329/UN47.B5/TD.06/2021', '521416025', 'fad50ffmahe', NULL, '1a127ddb8e', 'f154c17f55', '9eee0f6743', 'd53ee838e9', '92459d083c', '2021-03-31', '197102222001121001', 'PENGUJI HASIL'),
(1667350099, '942/UN47.B5/HK.04/2021', '521417002', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2021-09-09', '197102222001121001', 'PENGUJI HASIL'),
(1667352719, '193/UN47.B5/TD.06/2021', '521414017', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', '9eee0f6743', '2021-02-01', '197102222001121001', 'PENGUJI TUTUP'),
(1667352720, '648/UN47.B5/HK.04/2021', '521414040', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', '36d1464083', '5864493a02', '7bf1f8337d', '2021-06-18', '197102222001121001', 'PENGUJI TUTUP'),
(1667352721, '647/UN47.B5/HK.04/2021', '521414046', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2021-06-18', '197102222001121001', 'PENGUJI TUTUP'),
(1667352722, '642/UN47.B5/HK.04/2021', '521414056', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', '2021-06-17', '197102222001121001', 'PENGUJI TUTUP'),
(1667352724, '134/UN47.B5/TD.06/2021', '521415003', 'fad50ffmahe', NULL, '14c4f7be30', '113ef62d22', '637dfbddee', '98a4a6f7b7', '7fbcc3969e', '2021-02-02', '197102222001121001', 'PENGUJI TUTUP'),
(1667352726, '1260/UN47.B5/TD.06/2020', '521415013', 'fad50ffmahe', NULL, '7fbcc3969e', '113ef62d22', '5864493a02', '36d1464083', '274eccd1e7', '2020-11-26', '197102222001121001', 'PENGUJI TUTUP'),
(1667352727, '157/UN47.B5/TD.06/2021', '521415015', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '2021-02-05', '197102222001121001', 'PENGUJI TUTUP'),
(1667352728, '432/UN47.B5/TD.06/2021', '521415017', 'fad50ffmahe', NULL, '637dfbddee', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '7fbcc3969e', '2021-04-27', '197102222001121001', 'PENGUJI TUTUP'),
(1667352731, '1201/UN47.B5/HK.04/2021', '521415021', 'fad50ffmahe', NULL, '7fbcc3969e', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '36d1464083', '2021-11-18', '197102222001121001', 'PENGUJI TUTUP'),
(1667352732, 'sk tutup riski', '521415022', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '14c4f7be30', '7bf1f8337d', '98a4a6f7b7', '2022-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1667352733, '30/UN47.B5/TD.06/2021', '521415023', 'fad50ffmahe', NULL, '98a4a6f7b7', '92459d083c', '637dfbddee', '7bf1f8337d', '14c4f7be30', '2021-01-13', '197102222001121001', 'PENGUJI TUTUP'),
(1667352734, 'SK Nandar', '521415024', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '2022-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667352735, '431/UN47.B5/TD.06/2021', '521415027', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '274eccd1e7', '7bf1f8337d', '7fbcc3969e', '2021-04-27', '197102222001121001', 'PENGUJI TUTUP'),
(1667352737, 'SK Marfan', '521415029', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '5864493a02', '98a4a6f7b7', '7fbcc3969e', '2022-06-10', '197102222001121001', 'PENGUJI TUTUP'),
(1667352738, '591/UN47.B5/HK.04/2021', '521415031', 'fad50ffmahe', NULL, '7bf1f8337d', '98a4a6f7b7', '36d1464083', '274eccd1e7', '5864493a02', '2021-06-07', '197102222001121001', 'PENGUJI TUTUP'),
(1667352739, '1299/UN47.B5/TD.06/2020', '521416002', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '9eee0f6743', 'd53ee838e9', '1a127ddb8e', '2020-12-04', '197102222001121001', 'PENGUJI TUTUP'),
(1667352740, '154/UN47.B5/TD.06/2021', '521416004', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '7fbcc3969e', '36d1464083', '2021-02-05', '197102222001121001', 'PENGUJI TUTUP'),
(1667352741, '1282/UN47.B5/HK.04/2021', '521416005', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2021-12-09', '197102222001121001', 'PENGUJI TUTUP'),
(1667352743, '406/UN47.B5/TD.06/2021', '521416023', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '5864493a02', '92459d083c', '637dfbddee', '2021-04-21', '197102222001121001', 'PENGUJI TUTUP'),
(1667352744, '408/UN47.B5/TD.06/2021', '521416025', 'fad50ffmahe', NULL, '1a127ddb8e', 'f154c17f55', '9eee0f6743', 'd53ee838e9', '92459d083c', '2021-04-21', '197102222001121001', 'PENGUJI TUTUP'),
(1667352746, '1046/UN47.B5/HK.04/2021', '521417002', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2021-10-05', '197102222001121001', 'PENGUJI TUTUP'),
(1667442256, 'B/672/UN47.B5/TD.06/2022', '521417006', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2022-11-05', '197102222001121001', 'PENGUJI HASIL'),
(1667445039, 'B/673/UN47.B5/TD.06/2022', '521415005', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '5864493a02', '7fbcc3969e', '36d1464083', '2022-11-06', '197102222001121001', 'PENGUJI HASIL'),
(1667455416, 'B/690/UN47.B5/TD.06/2022', '521418004', 'fad50ffmahe', NULL, '1a127ddb8e', 'd53ee838e9', 'e8e4016ded', NULL, NULL, '2022-11-05', '197102222001121001', 'PENGUJI KP'),
(1667528431, 'B/694/UN47.B5/TD.06/2022', '521415012', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', 'f154c17f55', '92459d083c', 'f53eb48005', '2022-11-05', '197102222001121001', 'PENGUJI HASIL'),
(1667530399, 'B/665/UN47.B5/TD.06/2022', '521418034', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14', '197102222001121001', 'PEMBIMBING KP'),
(1667532537, 'B/700/UN47.B5/TD.06/2022', '521418064', 'fad50ffmahe', NULL, '7bf1f8337d', '7fbcc3969e', '36d1464083', NULL, NULL, '2022-11-06', '197102222001121001', 'PENGUJI KP'),
(1667532607, 'B/701/UN47.B5/TD.06/2022', '521418018', 'fad50ffmahe', NULL, 'f53eb48005', 'fb4b7ab909', '92459d083c', NULL, NULL, '2022-11-06', '197102222001121001', 'PENGUJI KP'),
(1667548662, 'B/708/UN47.B5/TD.06/2022', '521418063', 'fad50ffmahe', NULL, '7fbcc3969e', '637dfbddee', '5864493a02', NULL, NULL, '2022-11-06', '197102222001121001', 'PENGUJI KP'),
(1667549600, 'B/709/UN47.B5/TD.06/2022', '521418010', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '274eccd1e7', NULL, NULL, '2022-11-06', '197102222001121001', 'PENGUJI KP'),
(1668994196, 'B/723/UN47.B5/TD.06/2022', '521418017', 'fad50ffmahe', NULL, '5864493a02', '274eccd1e7', '64809b0cfc', NULL, NULL, '2022-11-26', '197102222001121001', 'PENGUJI KP'),
(1669176264, 'B/729/UN47.B5/TD.06/2022', '521417010', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '92459d083c', 'f53eb48005', '6eba3d13ea', '2022-11-22', '197102222001121001', 'PENGUJI TUTUP'),
(1669300461, 'B/668/UN47.B5/TD.06/2022', '521418037', '5864493a02', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2022-11-24', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1669605936, 'B/738/UN47.B5/TD.06/2022', '521419018', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2022-11-28', '197102222001121001', 'PEMBIMBING KP'),
(1669615727, 'B/665/UN47.B5/TD.06/2022', '521418034', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14', '197102222001121001', 'PEMBIMBING KP'),
(1669863824, 'B/765/UN47.B5/TD.06/2022', '521418034', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', NULL, NULL, '2022-12-11', '197102222001121001', 'PENGUJI KP'),
(1669866505, 'B/766/UN47.B5/TD.06/2022', '521416010', 'd53ee838e9', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2022-12-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1669867003, 'B/669/UN47.B5/TD.06/2022', '521418003', 'd53ee838e9', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2022-12-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1670469516, 'B/767/UN47.B5/TD.06/2022', '521418015', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '64809b0cfc', NULL, NULL, '2022-12-10', '197102222001121001', 'PENGUJI KP'),
(1670652773, 'B492/UN47.85/TD.06/2022', '521418027', 'f154c17f55', '8e6dd82846', NULL, NULL, NULL, NULL, NULL, '2022-12-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1670826835, 'B/774/UN47.B5/TD.06/2022', '521416008', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2022-12-18', '197102222001121001', 'PENGUJI TUTUP'),
(1671077370, 'B/782/UN47.B5/TD.06/2022', '521418027', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2022-12-15', '197102222001121001', 'PENGUJI HASIL'),
(1671427940, 'B/788/UN47.B5/TD.06/2022', '521418037', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '36d1464083', '2022-12-19', '197102222001121001', 'PENGUJI HASIL'),
(1671675397, 'B/794/UN47.B5/TD.06/2022', '521416036', 'fad50ffmahe', NULL, '98a4a6f7b7', 'fb4b7ab909', '5864493a02', '36d1464083', 'e8e4016ded', '2022-12-27', '197102222001121001', 'PENGUJI TUTUP'),
(1671972745, 'B/796/UN47.B5/TD.06/2022', '521418027', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2022-12-25', '197102222001121001', 'PENGUJI TUTUP'),
(1672115324, 'B/805/UN47.B5/TD.06/2022', '521416031', '8e6dd82846', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2022-12-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1672128687, 'B/818/UN47.B5/TD.06/2022', '521417006', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2022-12-27', '197102222001121001', 'PENGUJI TUTUP'),
(1672473733, 'B/830/UN47.B5/TD.06/2022', '521418037', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '36d1464083', '2022-12-31', '197102222001121001', 'PENGUJI TUTUP'),
(1673092160, 'B/579/UN47.B5.5/TD.06/2022', '521419007', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092240, 'B/590/UN47.B5.5/TD.06/2022', '521419003', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092315, 'B/596/UN47.B5.5/TD.06/2022', '521419017', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092377, 'B/597/UN47.B5.5/TD.06/2022', '521419002', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092429, 'B/598/UN47.B5.5/TD.06/2022', '521419010', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092505, 'B/600/UN47.B5.5/TD.06/2022', '521419012', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092561, 'B/607/UN47.B5.5/TD.06/2022', '521419001', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673092600, 'B/608/UN47.B5.5/TD.06/2022', '521419013', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673095620, 'B/619/UN47.B5.5/TD.06/2022', '521419037', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673095671, 'B/620/UN47.B5.5/TD.06/2022', '521419021', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673095716, 'B/621/UN47.B5.5/TD.06/2022', '521417042', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1673231457, 'B/622/UN47.B5.5/TD.06/2022', '521419022', '8e6dd82846', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-09', '197102222001121001', 'PEMBIMBING KP'),
(1673231535, 'B/632/UN47.B5.5/TD.06/2022', '521419041', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-09', '197102222001121001', 'PEMBIMBING KP'),
(1673838749, 'B/635/UN47.B5.5/TD.06/2022', '521419004', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-16', '197102222001121001', 'PEMBIMBING KP'),
(1673838879, 'B/636/UN47.B5.5/TD.06/2022', '521419008', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-16', '197102222001121001', 'PEMBIMBING KP'),
(1673839016, 'B/637/UN47.B5.5/TD.06/2022', '521419032', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-16', '197102222001121001', 'PEMBIMBING KP'),
(1673839075, 'B/637/UN47.B5.5/TD.06/2022', '521419019', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-16', '197102222001121001', 'PEMBIMBING KP'),
(1674268343, 'B/638/UN47.B5.5/TD.06/2022', '521419023', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-21', '197102222001121001', 'PEMBIMBING KP'),
(1674268376, 'B/639/UN47.B5.5/TD.06/2022', '521419027', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-21', '197102222001121001', 'PEMBIMBING KP'),
(1674268551, 'B/32/UN47.B5.5/TD.06/2023', '521416014', 'd53ee838e9', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, '2023-01-21', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1674444872, 'B/770/UN47.B5/TD.06/2022', '521419016', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-23', '197102222001121001', 'PEMBIMBING KP'),
(1674444896, 'B/771/UN47.B5/TD.06/2022', '521419025', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-23', '197102222001121001', 'PEMBIMBING KP'),
(1674649147, 'B/44/UN47.B5/TD.06/2023', '521419007', 'fad50ffmahe', NULL, 'fb4b7ab909', 'd53ee838e9', '1a127ddb8e', NULL, NULL, '2023-01-25', '197102222001121001', 'PENGUJI KP'),
(1674650490, 'B/46/UN47.B5/TD.06/2023', '521419001', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', '92459d083c', NULL, NULL, '2023-01-25', '197102222001121001', 'PENGUJI KP'),
(1674709321, 'B/48/UN47.B5/TD.06/2023', '521419012', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', '1a127ddb8e', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1674723274, 'B/51/UN47.B5/TD.06/2023', '521419003', 'fad50ffmahe', NULL, 'f154c17f55', '64809b0cfc', '92459d083c', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1674727266, 'B/53/UN47.B5/TD/06/2023', '521419019', 'fad50ffmahe', NULL, 'd53ee838e9', '36d1464083', 'f53eb48005', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1674734522, 'B/55/UN47.B5/TD.06/2023', '521419013', 'fad50ffmahe', NULL, 'f154c17f55', 'f53eb48005', '92459d083c', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1674734630, 'B/56/UN47.B5/TD.06/2023', '521419022', 'fad50ffmahe', NULL, '8e6dd82846', 'e8e4016ded', 'f53eb48005', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1674735229, 'B/59/UN47.B5/TD.06/2023', '521419017', 'fad50ffmahe', NULL, '64809b0cfc', '92459d083c', 'e8e4016ded', NULL, NULL, '2023-01-26', '197102222001121001', 'PENGUJI KP'),
(1675316354, 'B/63/UN47.B5.5/TD.06/2023', '521417013', '14c4f7be30', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2023-02-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1675646667, 'B/65/UN47.B5.5/TD.06/2023', '521419007', '645eef9c30', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2023-02-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1675647686, 'B/66/UN47.B5.5/TD.06/2023', '521416013', '637dfbddee', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2023-02-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1675915919, 'B/837/UN47.B5/TD.06/2022', '521419004', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '36d1464083', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675915960, 'B/837/UN47.B5/TD.06/2022', '521419008', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '36d1464083', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675915993, 'B/837/UN47.B5/TD.06/2022', '521419002', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '36d1464083', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916028, 'B/838/UN47.B5/TD.06/2022', '521417042', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '274eccd1e7', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916058, 'B/838/UN47.B5/TD.06/2022', '521419041', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '274eccd1e7', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916084, 'B/838/UN47.B5/TD.06/2022', '521419037', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', '7fbcc3969e', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916113, 'B/839/UN47.B5/TD.06/2022', '521419016', 'fad50ffmahe', NULL, '14c4f7be30', '64809b0cfc', '7fbcc3969e', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916131, 'B/839/UN47.B5/TD.06/2022', '521419010', 'fad50ffmahe', NULL, '14c4f7be30', '64809b0cfc', '7fbcc3969e', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916154, 'B/839/UN47.B5/TD.06/2022', '521419032', 'fad50ffmahe', NULL, '14c4f7be30', '64809b0cfc', '7fbcc3969e', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916188, 'B/840/UN47.B5/TD.06/2022', '521419027', 'fad50ffmahe', NULL, '637dfbddee', '64809b0cfc', 'f154c17f55', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916215, 'B/840/UN47.B5/TD.06/2022', '521419025', 'fad50ffmahe', NULL, '637dfbddee', '64809b0cfc', 'f154c17f55', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675916251, 'B/840/UN47.B5/TD.06/2022', '521419023', 'fad50ffmahe', NULL, '637dfbddee', '64809b0cfc', 'f154c17f55', NULL, NULL, '2023-02-09', '197102222001121001', 'PENGUJI KP'),
(1675995953, 'B/68/UN47.B5.5/TD.06/2023', '521418006', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-10', '197102222001121001', 'PEMBIMBING KP'),
(1675995998, 'B/69/UN47.B5.5/TD.06/2023', '521418032', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2023-02-10', '197102222001121001', 'PEMBIMBING KP'),
(1675997979, 'B/70/UN47.B5.5/TD.06/2023', '521416028', 'fad50ffmahe', NULL, '637dfbddee', '5864493a02', 'f154c17f55', NULL, NULL, '2023-02-10', '197102222001121001', 'PENGUJI KP'),
(1675999917, 'B/72/UN47.B5.5/TD.06/2023', '521417012', '1a127ddb8e', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2023-02-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1676000349, 'B/73/UN47.B5.5/TD.06/2023', '521418016', '274eccd1e7', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2023-02-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1677237078, 'B/79/UN47.B5.5/TD.06/2023', '521419018', 'fad50ffmahe', NULL, '64809b0cfc', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, '2023-02-24', '197102222001121001', 'PENGUJI KP'),
(1677735883, 'B/87/UN47.B5.5/TD.06/2023', '521419012', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2023-03-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1678062354, 'B/89/UN47.B5.5/TD.06/2023', '521419007', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2023-03-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1678328896, 'B/94/UN47.B5.5/TD.06/2023', '521417049', '98a4a6f7b7', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2023-03-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1680500197, 'B/104/UN47.B5.5/TD.06/2023', '521416027', 'd53ee838e9', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2023-04-03', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1680583671, 'B/105/UN47.B5.5/TD.06/2023', '521417028', 'f154c17f55', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2023-04-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1680584867, 'B/106/UN47.B5.5/TD.06/2023', '521417001', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2023-04-04', '197102222001121001', 'PENGUJI HASIL'),
(1680747725, 'B/113/UN47.B5.5/TD.06/2023', '521416007', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2023-04-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1681351206, 'B/116/UN47.B5.5/TD.06/2023', '521416017', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2023-04-13', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1681916203, 'B/117/UN47.B5.5/TD.06/2023', '521416028', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2023-04-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1682477218, 'B/118/UN47.B5.5/TD.06/2023', '521417024', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '36d1464083', '7fbcc3969e', '2023-04-26', '197102222001121001', 'PENGUJI HASIL'),
(1683089808, 'B/120/UN47.B5.5/TD.06/2023', '521417001', 'fad50ffmahe', NULL, 'f154c17f55', '8e6dd82846', 'd53ee838e9', 'f53eb48005', '92459d083c', '2023-05-03', '197102222001121001', 'PENGUJI TUTUP'),
(1683177803, 'B/123/UN47.B5.5/TD.06/2023', '521416037', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2023-05-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1683521351, 'B/128/UN47.B5.5/TD.06/2023', '521416019', 'd53ee838e9', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, '2023-05-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1683533640, 'B/127/UN47.B5.5/TD.06/2023', '521418013', '8e6dd82846', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2023-05-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1683858191, 'B/133/UN47.B5.5/TD.06/2023', '521419012', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '92459d083c', '1a127ddb8e', '6eba3d13ea', '2023-05-12', '197102222001121001', 'PENGUJI HASIL'),
(1684213960, 'B/542/UN47.B5/TD.06/2022', '521418001', '6eba3d13ea', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2023-05-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1684215522, 'B/140/UN47.B5.5/TD.06/2023', '521418001', 'fad50ffmahe', NULL, '6eba3d13ea', 'f53eb48005', '645eef9c30', 'fb4b7ab909', '92459d083c', '2023-05-16', '197102222001121001', 'PENGUJI HASIL'),
(1684465757, 'B/142/UN47.B5.5/TD.06/2023', '521419007', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '1a127ddb8e', '6eba3d13ea', 'e8e4016ded', '2023-05-19', '197102222001121001', 'PENGUJI HASIL'),
(1684558214, 'B/144/UN47.B5.5/TD.06/2023', '521416037', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '7bf1f8337d', '637dfbddee', '2023-05-20', '197102222001121001', 'PENGUJI HASIL'),
(1684578729, 'B/146/UN47.B5.5/TD.06/2023', '521416028', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '5864493a02', '98a4a6f7b7', '2023-05-20', '197102222001121001', 'PENGUJI HASIL'),
(1684820834, 'B/124/UN47.B5.5/TD.06/2023', '521416006', '64809b0cfc', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2023-05-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1684895922, 'B/149/UN47.B5.5/TD.06/2023', '521416007', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '7fbcc3969e', '637dfbddee', '2023-05-24', '197102222001121001', 'PENGUJI HASIL'),
(1685155187, 'B/154/UN47.B5.5/TD.06/2023', '521416006', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', 'd53ee838e9', '8e6dd82846', 'fb4b7ab909', '2023-05-27', '197102222001121001', 'PENGUJI HASIL'),
(1685346807, 'B/156/UN47.B5.5/TD.06/2023', '521416037', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '7bf1f8337d', '637dfbddee', '2023-05-29', '197102222001121001', 'PENGUJI TUTUP'),
(1685414101, 'B/159/UN47.B5.5/TD.06/2023', '521419007', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '1a127ddb8e', '64809b0cfc', 'e8e4016ded', '2023-05-30', '197102222001121001', 'PENGUJI TUTUP'),
(1685414827, 'B/162/UN47.B5.5/TD.06/2023', '521419012', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '92459d083c', '1a127ddb8e', '8e6dd82846', '2023-05-30', '197102222001121001', 'PENGUJI TUTUP'),
(1685416927, 'B/165/UNG47.B5.5/TD.06/2023', '521416017', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '2023-05-30', '197102222001121001', 'PENGUJI HASIL'),
(1685425917, 'B/110/UN47.B5.5/TD.06/2023', '521419038', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2023-05-30', '197102222001121001', 'PEMBIMBING KP'),
(1685427755, 'B/167/UN47.B5.5/TD.06/2023', '521419038', 'fad50ffmahe', NULL, 'f154c17f55', '5864493a02', '36d1464083', NULL, NULL, '2023-05-30', '197102222001121001', 'PENGUJI KP'),
(1685511838, 'B/169/UN47.B5.5/TD.06/2023', '521418016', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '14c4f7be30', '2023-05-31', '197102222001121001', 'PENGUJI HASIL'),
(1685513229, 'B/171/UN47.B5.5/TD.06/2023', '521416007', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '7fbcc3969e', '637dfbddee', '2023-05-31', '197102222001121001', 'PENGUJI TUTUP'),
(1685516579, 'B/174/UN47.B5.5/TD.06/2023', '521418006', 'fad50ffmahe', NULL, 'f53eb48005', '64809b0cfc', '1a127ddb8e', NULL, NULL, '2023-05-31', '197102222001121001', 'PENGUJI KP'),
(1685517254, 'B/176/UN47.B5.5/TD.06/2023', '521418032', 'fad50ffmahe', NULL, '1a127ddb8e', '64809b0cfc', 'f53eb48005', NULL, NULL, '2023-05-31', '197102222001121001', 'PENGUJI KP'),
(1685694102, 'B/178/UN47.B5.5/TD.06/2023', '521416028', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7fbcc3969e', '5864493a02', '98a4a6f7b7', '2023-06-02', '197102222001121001', 'PENGUJI TUTUP'),
(1685694499, 'B/181/UN47.B5.5/TD.06/2023', '521416017', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '2023-06-02', '197102222001121001', 'PENGUJI TUTUP'),
(1685961160, 'B/184/UN47.B5.5/TD.06/2023', '521418001', 'fad50ffmahe', NULL, '6eba3d13ea', 'f53eb48005', '645eef9c30', 'fb4b7ab909', '92459d083c', '2023-06-05', '197102222001121001', 'PENGUJI TUTUP'),
(1685963370, 'B/188/UN47.B5.5/TD.06/2023', '521419004', '7fbcc3969e', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2023-06-05', '197102222001121001', 'PEMBIMBING SKRIPSI');
INSERT INTO `surat_penunjukkan` (`id`, `no_surat`, `nim`, `pembimbing1`, `pembimbing2`, `penguji1`, `penguji2`, `penguji3`, `penguji4`, `penguji5`, `tanggal`, `ttd`, `keterangan`) VALUES
(1686019319, 'B/190/UN47.B5.5/TD.06/2023', '521416031', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '64809b0cfc', 'f154c17f55', 'e8e4016ded', '2023-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1686019680, 'B/192/UN47.B5.5/TD.06/2023', '521416006', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', 'd53ee838e9', '8e6dd82846', 'fb4b7ab909', '2023-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1686021778, 'B/195/UN47.B5.5/TD.06/2023', '521418003', 'fad50ffmahe', NULL, 'd53ee838e9', 'f53eb48005', '1a127ddb8e', '645eef9c30', '6eba3d13ea', '2023-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1686102066, 'B/202/UN47.B5.5/TD.06/2023', '521418016', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '14c4f7be30', '2023-06-07', '197102222001121001', 'PENGUJI TUTUP'),
(1686195573, 'B/202/UN47.B5.5/TD.06/2023', '521418063', '7fbcc3969e', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-06-08', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1686292449, 'B/214/UN47.B5.5/TD.06/2023', '521416010', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', 'fb4b7ab909', 'f53eb48005', '645eef9c30', '2023-06-09', '197102222001121001', 'PENGUJI HASIL'),
(1686556873, 'B/222/UN47.B5.5/TD.06/2023', '521417024', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '36d1464083', '7fbcc3969e', '2023-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1686635362, 'B/230/UN47.B5.5/TD.06/2023', '521416016', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', 'f53eb48005', 'fb4b7ab909', '6eba3d13ea', '2023-06-13', '197102222001121001', 'PENGUJI HASIL'),
(1686637079, 'B/229/UN47.B5.5/TD.06/2023', '521416014', 'fad50ffmahe', NULL, 'd53ee838e9', '1a127ddb8e', 'f53eb48005', 'f154c17f55', 'fb4b7ab909', '2023-06-13', '197102222001121001', 'PENGUJI HASIL'),
(1686637113, 'B/231/UN47.B5.5/TD.06/2023', '521416019', 'fad50ffmahe', NULL, 'd53ee838e9', 'e8e4016ded', '8e6dd82846', '92459d083c', '6eba3d13ea', '2023-06-13', '197102222001121001', 'PENGUJI HASIL'),
(1686637152, 'B/233/UN47.B5.5/TD.06/2023', '521416031', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', '64809b0cfc', 'f154c17f55', 'e8e4016ded', '2023-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1686721861, 'B/240/UN47.B5.5/TD.06/2023', '521416027', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', '64809b0cfc', 'fb4b7ab909', '645eef9c30', '2023-06-14', '197102222001121001', 'PENGUJI HASIL'),
(1686722254, 'B/242/UN47.B5.5/TD.06/2023', '521416010', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', 'fb4b7ab909', 'f53eb48005', '645eef9c30', '2023-06-14', '197102222001121001', 'PENGUJI TUTUP'),
(1686725320, 'B/245/UN47.B5.5/TD.06/2023', '521416013', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '98a4a6f7b7', '36d1464083', '14c4f7be30', '2023-06-14', '197102222001121001', 'PENGUJI HASIL'),
(1686730179, 'B/247/UN47.B5.5/TD.06/2023', '521418003', 'fad50ffmahe', NULL, 'd53ee838e9', 'f53eb48005', '1a127ddb8e', '645eef9c30', '6eba3d13ea', '2023-06-14', '197102222001121001', 'PENGUJI TUTUP'),
(1686980824, 'B/269/UN47.B5.5/TD.06/2023', '521416013', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '98a4a6f7b7', '36d1464083', '14c4f7be30', '2023-06-17', '197102222001121001', 'PENGUJI TUTUP'),
(1687235071, 'B/274/UN47.B5.5/TD.06/2023', '521417016', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2023-06-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1687235128, 'B/275/UN47.B5.5/TD.06/2023', '521417030', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-06-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1687235129, 'B/275/UN47.B5.5/TD.06/2023', '521417030', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-06-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1687235256, 'B/276/UN47.B5.5/TD.06/2023', '521419001', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-06-20', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1687326015, 'B/287/UN47.B5.5/TD.06/2023', '521416016', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', 'f53eb48005', 'fb4b7ab909', '6eba3d13ea', '2023-06-21', '197102222001121001', 'PENGUJI TUTUP'),
(1689310421, 'B/307/UN47.B5.5/TD.06/2023', '521419021', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '6eba3d13ea', NULL, NULL, '2023-07-14', '197102222001121001', 'PENGUJI KP'),
(1690859170, 'B/262/UN47.B5.5/TD.06/2023', '521416014', 'fad50ffmahe', NULL, 'd53ee838e9', '1a127ddb8e', 'f53eb48005', 'f154c17f55', 'fb4b7ab909', '2023-08-01', '197102222001121001', 'PENGUJI TUTUP'),
(1691119892, 'B/330/UN47.B5.5/TD.06/2023', '521417004', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-08-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1691996854, 'B/343/UN47.B5.5/TD.06/2023', '521418049', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691996958, 'B/344/UN47.B5.5/TD.06/2023', '521418055', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691996980, 'B/345/UN47.B5.5/TD.06/2023', '521418059', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691997002, 'B/346/UN47.B5.5/TD.06/2023', '521418066', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691997024, 'B/347/UN47.B5.5/TD.06/2023', '521418038', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691997057, 'B/348/UN47.B5.5/TD.06/2023', '521419020', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1691997072, 'B/349/UN47.B5.5/TD.06/2023', '521417031', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2023-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1693366878, 'B/270/UN47.B5.5/TD.06/2023', '521416019', 'fad50ffmahe', NULL, 'd53ee838e9', 'e8e4016ded', '8e6dd82846', '92459d083c', '6eba3d13ea', '2023-08-30', '197102222001121001', 'PENGUJI TUTUP'),
(1693535906, 'B/365/UN47.B5.5/TD.06/2023', '521418014', '5864493a02', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2023-09-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1693632676, 'B/288/UN47.B5.5/TD.06/2023', '521416027', 'fad50ffmahe', NULL, 'd53ee838e9', '92459d083c', '64809b0cfc', 'fb4b7ab909', '645eef9c30', '2023-09-02', '197102222001121001', 'PENGUJI TUTUP'),
(1694150276, 'B/370/UN47.B5.5/TD.06/2023', '521419004', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '36d1464083', '274eccd1e7', '2023-09-08', '197102222001121001', 'PENGUJI HASIL'),
(1694495153, 'B/379/UN47.B5.5/TD.06/2023', '521419003', 'f154c17f55', '6be551a649', NULL, NULL, NULL, NULL, NULL, '2023-09-12', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1695019743, 'B/382/UN47.B5.5/TD.06/2023', '521419017', '7fbcc3969e', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2023-09-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1696832651, 'B/388/UN47.B5.5/TD.06/2023', '521418034', '5864493a02', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2023-10-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1697088348, 'B/390/UN47.B5.5/TD.06/2023', '521419004', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '36d1464083', '274eccd1e7', '2023-10-12', '197102222001121001', 'PENGUJI TUTUP'),
(1697442076, 'B/584/UN47.B5.5/TD.06/2022', '521418012', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '98a4a6f7b7', NULL, NULL, '2023-10-16', '197102222001121001', 'PENGUJI KP'),
(1697781775, 'B/390/UN47.B5.5/TD.06/2023', '521419004', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '36d1464083', '274eccd1e7', '2023-10-20', '197102222001121001', 'PENGUJI TUTUP'),
(1698125539, 'B/401/UN47.B5.5/TD.06/2023', '521417045', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2023-10-24', '197102222001121001', 'PEMBIMBING KP'),
(1698387024, 'B/403/UN47.B5.5/TD.06/2023', '521418011', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2023-10-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1699234871, 'B/406/UN47.B5.5/TD.06/2023', '521418049', 'fad50ffmahe', NULL, '36d1464083', 'f154c17f55', '1a127ddb8e', NULL, NULL, '2023-11-06', '197102222001121001', 'PENGUJI KP'),
(1700012970, 'B/411/UN47.B5.5/TD.06/2023', '521419020', 'fad50ffmahe', NULL, '98a4a6f7b7', '14c4f7be30', '637dfbddee', NULL, NULL, '2023-11-15', '197102222001121001', 'PENGUJI KP'),
(1700013029, 'B/412/UN47.B5.5/TD.06/2023', '521417028', 'fad50ffmahe', NULL, 'f154c17f55', 'f53eb48005', 'd53ee838e9', '8e6dd82846', '6eba3d13ea', '2023-11-15', '197102222001121001', 'PENGUJI HASIL'),
(1700014280, 'B/413/UN47.B5.5/TD.06/2023', '521419038', 'fb4b7ab909', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, '2023-11-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1700445076, 'B/416/UN47.B5.5/TD.06/2023', '521417049', 'fad50ffmahe', NULL, '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '5864493a02', '36d1464083', '2023-11-20', '197102222001121001', 'PENGUJI HASIL'),
(1701061943, 'B/419/UN47.B5.5/TD.06/2023', '521418008', '64809b0cfc', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2023-11-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1701071040, 'B/420/UN47.B5.5/TD.06/2023', '521417012', 'fad50ffmahe', NULL, '1a127ddb8e', 'f53eb48005', '645eef9c30', '92459d083c', 'fb4b7ab909', '2023-11-27', '197102222001121001', 'PENGUJI HASIL'),
(1701071119, 'B/421/UN47.B5.5/TD.06/2023', '521418055', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '7fbcc3969e', NULL, NULL, '2023-11-27', '197102222001121001', 'PENGUJI KP'),
(1701227098, 'B/424/UN47.B5.5/TD.06/2023', '521418012', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2023-11-29', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1701227116, 'B/425/UN47.B5.5/TD.06/2023', '521418039', '7fbcc3969e', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2023-11-29', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1701227166, 'B/426/UN47.B5.5/TD.06/2023', '521418066', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '637dfbddee', NULL, NULL, '2023-11-29', '197102222001121001', 'PENGUJI KP'),
(1701415776, 'B/427/UN47.B5.5/TD.06/2023', '521418059', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '637dfbddee', NULL, NULL, '2023-12-01', '197102222001121001', 'PENGUJI KP'),
(1701828838, 'B/430/UN47.B5.5/TD.06/2023', '521418013', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', 'd53ee838e9', 'f53eb48005', '92459d083c', '2023-12-06', '197102222001121001', 'PENGUJI HASIL'),
(1701928224, 'B/432/UN47.B5.5/TD.06/2023', '521417031', 'fad50ffmahe', NULL, '1a127ddb8e', 'd53ee838e9', '645eef9c30', NULL, NULL, '2023-12-07', '197102222001121001', 'PENGUJI KP'),
(1701929309, 'B/434/UN47.B5.5/TD.06/2023', '521418038', 'fad50ffmahe', NULL, '92459d083c', '36d1464083', '7bf1f8337d', NULL, NULL, '2023-12-07', '197102222001121001', 'PENGUJI KP'),
(1703037553, 'B/440/UN47.B5.5/TD.06/2023', '521418013', 'fad50ffmahe', NULL, '8e6dd82846', 'fb4b7ab909', 'd53ee838e9', 'f53eb48005', '92459d083c', '2023-12-20', '197102222001121001', 'PENGUJI TUTUP'),
(1703116255, 'B/442/UN47.B5.5/TD.06/2023', '521417013', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2023-12-21', '197102222001121001', 'PENGUJI HASIL'),
(1703116277, 'B/441/UN47.B5.5/TD.06/2023', '521419001', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '7bf1f8337d', '98a4a6f7b7', '274eccd1e7', '2023-12-21', '197102222001121001', 'PENGUJI HASIL'),
(1703224711, 'B/444/UN47.B5.5/TD.06/2023', '521417028', 'fad50ffmahe', NULL, 'f154c17f55', 'f53eb48005', 'd53ee838e9', '8e6dd82846', '6eba3d13ea', '2023-12-22', '197102222001121001', 'PENGUJI TUTUP'),
(1703655288, 'B/451/UN47.B5.5/TD.06/2023', '521417012', 'fad50ffmahe', NULL, '1a127ddb8e', 'f53eb48005', '645eef9c30', '92459d083c', 'fb4b7ab909', '2023-12-27', '197102222001121001', 'PENGUJI TUTUP'),
(1703658360, 'B/457/UN47.B5.5/TD.06/2023', '521418011', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '2023-12-27', '197102222001121001', 'PENGUJI HASIL'),
(1703659043, 'B/458/UN47.B5.5/TD.06/2023', '521418051', '7fbcc3969e', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2023-12-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1704698970, 'B/11/UN47.B5.5/TD.06/2024', '521417013', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2024-01-08', '197102222001121001', 'PENGUJI TUTUP'),
(1704701857, 'B/14/UN47.B5.5/TD.06/2024', '521419001', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '7bf1f8337d', '98a4a6f7b7', '274eccd1e7', '2024-01-08', '197102222001121001', 'PENGUJI TUTUP'),
(1704767110, 'B/17/UN47.B5.5/TD.06/2024', '521417049', 'fad50ffmahe', NULL, '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '5864493a02', '36d1464083', '2024-01-09', '197102222001121001', 'PENGUJI TUTUP'),
(1705560509, '53/UN47.B5.5/TD.06/2023', '521418031', 'f53eb48005', '645eef9c30', NULL, NULL, NULL, NULL, NULL, '2024-01-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1706151506, '69/UN47.B5.5/TD.06/2024', '521418063', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '274eccd1e7', '637dfbddee', 'f154c17f55', '2024-01-25', '197102222001121001', 'PENGUJI HASIL'),
(1706231196, 'B/72/UN47.B5.5/TD.06/2024', '521420001', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', 'f154c17f55', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706231335, 'B/73/UN47.B5.5/TD.06/2024', '521420018', 'fad50ffmahe', NULL, '64809b0cfc', '637dfbddee', '92459d083c', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706234195, 'B/74/UN47.B5.5/TD.06/2024', '521420010', 'fad50ffmahe', NULL, '64809b0cfc', '645eef9c30', '1a127ddb8e', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706235184, 'B/75/UN47.B5.5/TD.06/2024', '521418011', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '36d1464083', '7fbcc3969e', '2024-01-26', '197102222001121001', 'PENGUJI TUTUP'),
(1706259609, 'B/84/UN47.B5.5/TD.06/2024', '521420003', 'fad50ffmahe', NULL, '5864493a02', '637dfbddee', '92459d083c', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706259658, 'B/85/UN47.B5.5/TD.06/2024', '521420035', 'fad50ffmahe', NULL, '64809b0cfc', '637dfbddee', 'fb4b7ab909', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706260325, 'B/88/UN47.B5.5/TD.06/2024', '521420012', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '645eef9c30', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706260388, 'B/89/UN47.B5.5/TD.06/2024', '521420036', 'fad50ffmahe', NULL, '5864493a02', '92459d083c', '1a127ddb8e', NULL, NULL, '2024-01-26', '197102222001121001', 'PENGUJI KP'),
(1706346372, '92/UN47.B5.5/TD.06/2024', '521419003', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', '64809b0cfc', 'fb4b7ab909', '92459d083c', '2024-01-27', '197102222001121001', 'PENGUJI HASIL'),
(1707110122, '103/UN47.B5.5/TD.06/2024', '521417042', 'f154c17f55', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2024-02-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1708935283, '131/UN47.B5.5/TD.06/2024', '521420027', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-26', '197102222001121001', 'PEMBIMBING KP'),
(1708935476, '132/UN47.B5.5/TD.06/2024', '521420028', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-26', '197102222001121001', 'PEMBIMBING KP'),
(1708935499, '133/UN47.B5.5/TD.06/2024', '521420038', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-26', '197102222001121001', 'PEMBIMBING KP'),
(1708935526, '134/UN47.B5.5/TD.06/2024', '521420022', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-26', '197102222001121001', 'PEMBIMBING KP'),
(1709530010, '151/UN47.B5.5/TD.06/2024', '521417021', '645eef9c30', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2024-03-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1709776116, '159/UN47.B5.5/TD.06/2024', '521417034', '637dfbddee', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1709776134, '160/UN47.B5.5/TD.06/2024', '521417022', '637dfbddee', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1709776271, '161/UN47.B5.5/TD.06/2024', '521420014', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709776326, '162/UN47.B5.5/TD.06/2024', '521420004', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709776348, '163/UN47.B5.5/TD.06/2024', '521420016', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709776382, '164/UN47.B5.5/TD.06/2024', '521420030', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709776402, '165/UN47.B5.5/TD.06/2024', '521420032', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709776420, '166/UN47.B5.5/TD.06/2024', '521420033', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1709778960, '165/UN47.B5.5/TD.06/2024', '521420032', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-07', '197102222001121001', 'PEMBIMBING KP'),
(1710115906, '170/UN47.B5.5/TD.06/2024', '521419003', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', '64809b0cfc', 'fb4b7ab909', '92459d083c', '2024-03-11', '197102222001121001', 'PENGUJI TUTUP'),
(1710729370, '176/UN47.B5.5/TD.06/2024', '521420017', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-18', '197102222001121001', 'PEMBIMBING KP'),
(1710729392, '177/UN47.B5.5/TD.06/2024', '521420023', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-18', '197102222001121001', 'PEMBIMBING KP'),
(1710816870, '179/UN47.B5.5/TD.06/2024', '521417052', '637dfbddee', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2024-03-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1710816893, '180/UN47.B5.5/TD.06/2024', '521418004', 'f53eb48005', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2024-03-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1711067218, '186/UN47.B5.5/TD.06/2024', '521418008', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', '1a127ddb8e', '92459d083c', 'd53ee838e9', '2024-03-22', '197102222001121001', 'PENGUJI HASIL'),
(1711067259, '187/UN47.B5.5/TD.06/2024', '521418014', 'fad50ffmahe', NULL, '5864493a02', 'f53eb48005', '7bf1f8337d', '36d1464083', '274eccd1e7', '2024-03-22', '197102222001121001', 'PENGUJI HASIL'),
(1711505701, '204/UN47.B5.5/TD.06/2024', '521418049', '637dfbddee', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2024-03-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1711505714, '205/UN47.B5.5/TD.06/2024', '521417015', 'f53eb48005', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, '2024-03-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1713941746, '231/UN47.B5.5/TD.06/2024', '521418063', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '274eccd1e7', '637dfbddee', 'f154c17f55', '2024-04-24', '197102222001121001', 'PENGUJI TUTUP'),
(1714700025, '246/UN47.B5.5/TD.06/2024', '521418008', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', '1a127ddb8e', '92459d083c', 'd53ee838e9', '2024-05-03', '197102222001121001', 'PENGUJI TUTUP'),
(1714723272, 'B/72/UN47.B5.5/TD.06/2024', '521420028', 'fad50ffmahe', NULL, 'd53ee838e9', '5864493a02', '7bf1f8337d', NULL, NULL, '2024-05-03', '197102222001121001', 'PENGUJI KP'),
(1715567481, '260/UN47.B5.5/TD.06/2024', '521417055', '274eccd1e7', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2024-05-13', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1715567686, '261/UN47.B5.5/TD.06/2024', '521418066', '98a4a6f7b7', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2024-05-13', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1715667291, '263/UN47.B5.5/TD.06/2024', '521417042', 'fad50ffmahe', NULL, 'f154c17f55', '5864493a02', '1a127ddb8e', 'f53eb48005', '64809b0cfc', '2024-05-14', '197102222001121001', 'PENGUJI HASIL'),
(1715828874, '272/UN47.B5.5/TD.06/2024', '521418014', 'fad50ffmahe', NULL, '5864493a02', 'f53eb48005', '7bf1f8337d', '36d1464083', '274eccd1e7', '2024-05-16', '197102222001121001', 'PENGUJI TUTUP'),
(1715924108, 'B/274/UN47.B5.5/TD.06/2024', '521420038', 'fad50ffmahe', NULL, '637dfbddee', '98a4a6f7b7', '14c4f7be30', NULL, NULL, '2024-05-17', '197102222001121001', 'PENGUJI KP'),
(1715924208, 'B/275/UN47.B5.5/TD.06/2024', '521420022', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '6eba3d13ea', NULL, NULL, '2024-05-17', '197102222001121001', 'PENGUJI KP'),
(1715925065, '275/UN47.B5.5/TD.06/2024', '521417021', 'fad50ffmahe', NULL, '645eef9c30', 'f53eb48005', '1a127ddb8e', 'fb4b7ab909', '64809b0cfc', '2024-05-17', '197102222001121001', 'PENGUJI HASIL'),
(1715925132, '276/UN47.B5.5/TD.06/2024', '521418031', 'fad50ffmahe', NULL, 'f53eb48005', '645eef9c30', 'd53ee838e9', '64809b0cfc', 'f154c17f55', '2024-05-17', '197102222001121001', 'PENGUJI HASIL'),
(1716950799, '299/UN47.B5.5/TD.06/2024', '521417045', 'fad50ffmahe', NULL, 'f53eb48005', '36d1464083', '7fbcc3969e', NULL, NULL, '2024-05-29', '197102222001121001', 'PENGUJI KP'),
(1717394679, '306/UN47.B5.5/TD.06/2024', '521417031', '645eef9c30', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, '2024-06-03', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1717394714, '307/UN47.B5.5/TD.06/2024', '521418010', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2024-06-03', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1717572899, '313/UN47.B5.5/TD.06/2024', '521417042', 'fad50ffmahe', NULL, 'f154c17f55', '5864493a02', '1a127ddb8e', 'f53eb48005', '64809b0cfc', '2024-06-05', '197102222001121001', 'PENGUJI TUTUP'),
(1717575238, '316/UN47.B5.5/PK.03.05/2024', '521417021', 'fad50ffmahe', NULL, '645eef9c30', 'f53eb48005', '1a127ddb8e', 'fb4b7ab909', '64809b0cfc', '2024-06-05', '197102222001121001', 'PENGUJI TUTUP'),
(1717652654, '319/UN47.B5.5/TD.06/2024', '521417015', 'fad50ffmahe', NULL, 'f53eb48005', '1a127ddb8e', 'd53ee838e9', '6eba3d13ea', '645eef9c30', '2024-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1717652861, '321/UN47.B5.5/TD.06/2024', '521417016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', '36d1464083', '2024-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1717653110, '323/UN47.B5.5/TD.06/2024', '521417030', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '7fbcc3969e', '36d1464083', '2024-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1717653639, '325/UN47.B5.5/TD.06/2024', '521417055', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '36d1464083', '2024-06-06', '197102222001121001', 'PENGUJI HASIL'),
(1717728027, '327/UN47.B5.5/TD.06/2024', '521418034', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '98a4a6f7b7', '7fbcc3969e', '36d1464083', '2024-06-07', '197102222001121001', 'PENGUJI HASIL'),
(1717743040, '328/UN47.B5.5/TD.06/2024', '521418012', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '7bf1f8337d', '98a4a6f7b7', '274eccd1e7', '2024-06-07', '197102222001121001', 'PENGUJI HASIL'),
(1717748538, '331/UN47.B5.5/TD.06/2024', '521420016', 'fad50ffmahe', NULL, '645eef9c30', 'd53ee838e9', '64809b0cfc', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717748652, '332/UN47.B5.5/TD.06/2024', '521420027', 'fad50ffmahe', NULL, '64809b0cfc', '637dfbddee', '1a127ddb8e', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717748677, '333/UN47.B5.5/TD.06/2024', '521420014', 'fad50ffmahe', NULL, '36d1464083', '7fbcc3969e', 'f53eb48005', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717748771, '334/UN47.B5.5/TD.06/2024', '521420030', 'fad50ffmahe', NULL, '645eef9c30', '64809b0cfc', 'f154c17f55', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717748815, '335/UN47.B5.5/TD.06/2024', '521420032', 'fad50ffmahe', NULL, '5864493a02', '1a127ddb8e', '6eba3d13ea', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717748909, '336/UN47.B5.5/TD.06/2024', '521420004', 'fad50ffmahe', NULL, '6eba3d13ea', 'f53eb48005', 'f154c17f55', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717749129, '337/UN47.B5.5/TD.06/2024', '521420033', 'fad50ffmahe', NULL, '645eef9c30', 'd53ee838e9', '6eba3d13ea', NULL, NULL, '2024-06-07', '197102222001121001', 'PENGUJI KP'),
(1717830904, '344/UN47.B5.5/TD.06/2024', '521417034', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '14c4f7be30', '2024-06-08', '197102222001121001', 'PENGUJI HASIL'),
(1717899783, '347/UN47.B5.5/TD.06/2024', '521417031', 'fad50ffmahe', NULL, '645eef9c30', '1a127ddb8e', 'd53ee838e9', '6eba3d13ea', 'f53eb48005', '2024-06-09', '197102222001121001', 'PENGUJI HASIL'),
(1717988615, '351/UN47.B5.5/TD.06/2024', '521418018', 'fb4b7ab909', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, '2024-06-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1718164091, '374/UN47.B5.5/TD.06/2024', '521417022', 'fad50ffmahe', NULL, '637dfbddee', '64809b0cfc', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2024-06-12', '197102222001121001', 'PENGUJI HASIL'),
(1718164115, '375/UN47.B5.5/TD.06/2024', '521417004', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2024-06-12', '197102222001121001', 'PENGUJI HASIL'),
(1718164141, '376/UN47.B5.5/TD.06/2024', '521417052', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '7bf1f8337d', '5864493a02', '36d1464083', '2024-06-12', '197102222001121001', 'PENGUJI HASIL'),
(1718179503, '381/UN47.B5.5/TD.06/2024', '521417015', 'fad50ffmahe', NULL, 'f53eb48005', '1a127ddb8e', 'd53ee838e9', '6eba3d13ea', '645eef9c30', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718179671, '384/UN47.B5.5/TD.06/2024', '521417016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', '36d1464083', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718180157, '387/UN47.B5.5/TD.06/2024', '521417055', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '36d1464083', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718180305, '390/UN47.B5.5/TD.06/2024', '521418012', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '7bf1f8337d', '98a4a6f7b7', '274eccd1e7', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718181826, '393/UN47.B5.5/TD.06/2024', '521417030', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '7fbcc3969e', '36d1464083', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718184697, '396/UN47.B5.5/TD.06/2024', '521417031', 'fad50ffmahe', NULL, '645eef9c30', '1a127ddb8e', 'd53ee838e9', '6eba3d13ea', 'f53eb48005', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718190008, '399/UN47.B5.5/TD.06/2024', '521417034', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', '14c4f7be30', '2024-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1718247002, '370/UN47.B5.5/TD.06/2024', '521417045', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2024-06-13', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1718261959, '384/UN47.B5.5/TD.06/2024', '521417016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', '36d1464083', '2024-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1718268192, '405/UN47.B5.5/TD.06/2024', '521417052', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '7bf1f8337d', '5864493a02', '36d1464083', '2024-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1718268781, '407/UN47.B5.5/TD.06/2024', '521417004', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2024-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1718271292, '410/UN47.B5.5/TD.06/2024', '521417022', 'fad50ffmahe', NULL, '637dfbddee', '64809b0cfc', '98a4a6f7b7', '7bf1f8337d', '274eccd1e7', '2024-06-13', '197102222001121001', 'PENGUJI TUTUP'),
(1718335018, '384/UN47.B5.5/TD.06/2024', '521417016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', '36d1464083', '2024-06-14', '197102222001121001', 'PENGUJI TUTUP'),
(1718867732, '407/UN47.B5.5/TD.06/2024', '521417004', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2024-06-20', '197102222001121001', 'PENGUJI TUTUP'),
(1718933240, '387/UN47.B5.5/TD.06/2024', '521417055', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '5864493a02', '7fbcc3969e', '36d1464083', '2024-06-21', '197102222001121001', 'PENGUJI TUTUP'),
(1719201968, '413/UN47.B5.5/TD.06/2024', '521418034', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '98a4a6f7b7', '7fbcc3969e', '36d1464083', '2024-06-24', '197102222001121001', 'PENGUJI TUTUP'),
(1719383391, '417/UN47.B5.5/TD.06/2024', '521418031', 'fad50ffmahe', NULL, 'f53eb48005', '645eef9c30', 'd53ee838e9', '64809b0cfc', 'f154c17f55', '2024-06-26', '197102222001121001', 'PENGUJI TUTUP'),
(1721095886, '480/UN47.B5.5/TD.06/2024', '521420018', '637dfbddee', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, '2024-07-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1721095921, '481/UN47.B5.5/TD.06/2024', '521420014', '7fbcc3969e', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2024-07-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1721095938, '482/UN47.B5.5/TD.06/2024', '521420003', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2024-07-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1721208452, '487/UN47.B5.5/TD.06/2024', '521420036', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2024-07-17', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1722915152, '526/UN47.B5.5/TD.06/2024', '521420028', '5864493a02', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2024-08-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1723095184, '178/UN47.B5.5/TD.06/2024', '521420026', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2024-08-08', '197102222001121001', 'PEMBIMBING KP'),
(1723788207, '550/UN47.B5.5/TD.06/2024', '521420026', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '64809b0cfc', NULL, NULL, '2024-08-16', '197102222001121001', 'PENGUJI KP'),
(1725412044, '586/UN47.B5.5/TD.06/2024', '521420010', '637dfbddee', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2024-09-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1725513231, '407/UN47.B5.5/TD.06/2024', '521417004', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2024-09-05', '197102222001121001', 'PENGUJI TUTUP'),
(1726640806, '610/UN47.B5.5/TD.06/2024', '521420035', '64809b0cfc', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2024-09-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1726715101, '612/UN47.B5.5/TD.06/2024', '521418039', 'fad50ffmahe', NULL, '7fbcc3969e', '36d1464083', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '2024-09-19', '197102222001121001', 'PENGUJI HASIL'),
(1727155781, '621/UN47.B5.5/TD.06/2024', '521418057', 'd53ee838e9', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2024-09-24', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1727235087, '624/UN47.B5.5/TD.06/2024', '521418026', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-25', '197102222001121001', 'PEMBIMBING KP'),
(1727328272, '627/UN47.B5.5/TD.06/2024', '521419020', '7fbcc3969e', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2024-09-26', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1727760901, '636/UN47.B5.5/TD.06/2024', '521420038', '14c4f7be30', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2024-10-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1727930272, '642/UN47.B5.5/TD.06/2024', '521420021', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930339, '643/UN47.B5.5/TD.06/2024', '521420005', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930377, '644/UN47.B5.5/TD.06/2024', '521420007', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930406, '645/UN47.B5.5/TD.06/2024', '521420019', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930464, '646/UN47.B5.5/TD.06/2024', '521420011', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930491, '647/UN47.B5.5/TD.06/2024', '521419028', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930519, '648/UN47.B5.5/TD.06/2024', '521420039', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1727930544, '649/UN47.B5.5/TD.06/2024', '521420034', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', '197102222001121001', 'PEMBIMBING KP'),
(1728270966, '655/UN47.B5.5/TD.06/2024', '521420022', '14c4f7be30', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2024-10-07', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1729648629, '676/UN47.B5.5/TD.06/2024', '521420032', '645eef9c30', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2024-10-23', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1730774678, '693/UN47.B5.5/TD.06/2024', '521418035', '637dfbddee', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2024-11-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1730953163, '699/UN47.B5.5/TD.06/2024', '521418039', 'fad50ffmahe', NULL, '7fbcc3969e', '36d1464083', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '2024-11-07', '197102222001121001', 'PENGUJI TUTUP'),
(1731470510, '711/UN47.B5.5/TD.06/2024', '521420002', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-13', '197102222001121001', 'PEMBIMBING KP'),
(1731470532, '712/UN47.B5.5/TD.06/2024', '521419024', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-13', '197102222001121001', 'PEMBIMBING KP'),
(1731470553, '713/UN47.B5.5/TD.06/2024', '521419015', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-13', '197102222001121001', 'PEMBIMBING KP'),
(1731470574, '714/UN47.B5.5/TD.06/2024', '521420024', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-13', '197102222001121001', 'PEMBIMBING KP'),
(1733119408, '748/UN47.B5.5/TD.06/2024', '521419022', '64809b0cfc', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2024-12-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1733119465, '749/UN47.B5.5/TD.06/2024', '521419013', 'fb4b7ab909', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, '2024-12-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1733119874, '750/UN47.B5.5/TD.06/2024', '521420033', '645eef9c30', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2024-12-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1733119924, '751/UN47.B5.5/TD.06/2024', '521420030', '6eba3d13ea', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2024-12-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1733123033, '752/UN47.B5.5/TD.06/2024', '521418038', '274eccd1e7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2024-12-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1733378888, '760/UN47.B5.5/TD.06/2024', '521420014', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '637dfbddee', '2024-12-05', '197102222001121001', 'PENGUJI HASIL'),
(1733384959, '763/UN47.B5.5/TD.06/2024', '521418051', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '274eccd1e7', '36d1464083', '2024-12-05', '197102222001121001', 'PENGUJI HASIL'),
(1733812779, '772/UN47.B5.5/TD.06/2024', '521420026', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2024-12-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1734317442, '782/UN47.B5.5/TD.06/2024', '521420028', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '274eccd1e7', '7fbcc3969e', '36d1464083', '2024-12-16', '197102222001121001', 'PENGUJI HASIL'),
(1734317503, '783/UN47.B5.5/TD.06/2024', '521419038', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', 'd53ee838e9', '1a127ddb8e', '92459d083c', '2024-12-16', '197102222001121001', 'PENGUJI HASIL'),
(1734324061, '784/UN47.B5.5/TD.06/2024', '521418049', 'fad50ffmahe', NULL, '637dfbddee', '7bf1f8337d', '274eccd1e7', '5864493a02', '36d1464083', '2024-12-16', '197102222001121001', 'PENGUJI HASIL'),
(1734329403, '788/UN47.B5.5/TD.06/2024', '521418015', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2024-12-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1734329442, '789/UN47.B5.5/TD.06/2024', '521418064', '98a4a6f7b7', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2024-12-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1734500767, '793/UN47.B5.5/TD.06/2024', '521418026', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '36d1464083', NULL, NULL, '2024-12-18', '197102222001121001', 'PENGUJI KP'),
(1734574345, '796/UN47.B5.5/TD.06/2024', '521420027', 'f154c17f55', '6be551a649', NULL, NULL, NULL, NULL, NULL, '2024-12-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1734931601, '803/UN47.B5.5/TD.06/2024', '521420032', 'fad50ffmahe', NULL, '645eef9c30', '92459d083c', 'd53ee838e9', 'f154c17f55', '36d1464083', '2024-12-23', '197102222001121001', 'PENGUJI HASIL'),
(1734932214, '805/UN47.B5.5/TD.06/2024', '521420007', 'fad50ffmahe', NULL, '92459d083c', '5864493a02', 'a5e8c20ec3', NULL, NULL, '2024-12-23', '197102222001121001', 'PENGUJI KP'),
(1734933124, '807/UN47.B5.5/TD.06/2024', '521420005', 'fad50ffmahe', NULL, 'fb4b7ab909', '7fbcc3969e', 'a5e8c20ec3', NULL, NULL, '2024-12-23', '197102222001121001', 'PENGUJI KP'),
(1735006376, '811/UN47.B5.5/TD.06/2024', '521420021', 'fad50ffmahe', NULL, 'f53eb48005', '645eef9c30', '36d1464083', NULL, NULL, '2024-12-24', '197102222001121001', 'PENGUJI KP'),
(1735536520, '817/UN47.B5.5/TD.06/2024', '521420012', '64809b0cfc', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, '2024-12-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1736482001, '71/UN47.B5.5/TD.06/2025', '521419038', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', 'd53ee838e9', '1a127ddb8e', '92459d083c', '2025-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1736490662, '74/UN47.B5.5/TD.06/2025', '521418049', 'fad50ffmahe', NULL, '637dfbddee', '7bf1f8337d', '274eccd1e7', '5864493a02', '36d1464083', '2025-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1736754824, '80/UN47.B5.5/TD.06/2025', '521420032', 'fad50ffmahe', NULL, '645eef9c30', '92459d083c', 'd53ee838e9', 'f154c17f55', '36d1464083', '2025-01-13', '197102222001121001', 'PENGUJI TUTUP'),
(1736823439, '83/UN47.B5.5/TD.06/2025', '521418059', '274eccd1e7', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2025-01-14', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1736989335, '80/UN47.B5.5/TD.06/2025', '521420032', 'fad50ffmahe', NULL, '645eef9c30', '92459d083c', 'd53ee838e9', 'f154c17f55', '36d1464083', '2025-01-16', '197102222001121001', 'PENGUJI TUTUP'),
(1737346775, 'sama dengan akdar', '521418029', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-20', '197102222001121001', 'PEMBIMBING KP'),
(1738065273, '104/UN47.B5.5/TD.06/2025', '521421023', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-28', '197102222001121001', 'PEMBIMBING KP'),
(1738065307, '105/UN47.B5.5/TD.06/2025', '521421033', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-28', '197102222001121001', 'PEMBIMBING KP'),
(1738065362, '106/UN47.B5.5/TD.06/2025', '521421003', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-28', '197102222001121001', 'PEMBIMBING KP'),
(1738220159, '108/UN47.B5.5/TD.06/2025', '521418006', '1a127ddb8e', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2025-01-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1738293238, '112/UN47.B5.5/TD.06/2025', '521421007', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-31', '197102222001121001', 'PEMBIMBING KP'),
(1738293259, '113/UN47.B5.5/TD.06/2025', '521421025', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-31', '197102222001121001', 'PEMBIMBING KP'),
(1738643661, '119/UN47.B5.5/TD.06/2025', '521420028', 'fad50ffmahe', NULL, '5864493a02', '7bf1f8337d', '274eccd1e7', '7fbcc3969e', '36d1464083', '2025-02-04', '197102222001121001', 'PENGUJI TUTUP'),
(1738723435, '123/UN47.B5.5/TD.06/2025', '521420036', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '637dfbddee', 'a5e8c20ec3', '2025-02-05', '197102222001121001', 'PENGUJI HASIL'),
(1738723462, '124/UN47.B5.5/TD.06/2025', '521420003', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '637dfbddee', '7fbcc3969e', '2025-02-05', '197102222001121001', 'PENGUJI HASIL'),
(1738723479, '125/UN47.B5.5/TD.06/2025', '521418051', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '274eccd1e7', '36d1464083', '2025-02-05', '197102222001121001', 'PENGUJI TUTUP'),
(1738723823, '127/UN47.B5.5/TD.06/2025', '521421023', 'fad50ffmahe', NULL, '1a127ddb8e', '6eba3d13ea', '92459d083c', NULL, NULL, '2025-02-05', '197102222001121001', 'PENGUJI KP'),
(1738723854, '128/UN47.B5.5/TD.06/2025', '521421033', 'fad50ffmahe', NULL, '6eba3d13ea', 'd53ee838e9', 'e8e4016ded', NULL, NULL, '2025-02-05', '197102222001121001', 'PENGUJI KP'),
(1738828165, '136/UN47.B5.5/TD.06/2025', '521421026', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828189, '137/UN47.B5.5/TD.06/2025', '521421011', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828229, '138/UN47.B5.5/TD.06/2025', '521421022', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828879, '139/UN47.B5.5/TD.06/2025', '521421049', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828901, '140/UN47.B5.5/TD.06/2025', '521421029', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828926, '141/UN47.B5.5/TD.06/2025', '521421016', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738828944, '142/UN47.B5.5/TD.06/2025', '521421041', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738829124, '143/UN47.B5.5/TD.06/2025', '521421001', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738829147, '144/UN47.B5.5/TD.06/2025', '521421055', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-06', '197102222001121001', 'PEMBIMBING KP'),
(1738907252, '146/UN47.B5.5/TD.06/2025', '521420019', 'fad50ffmahe', NULL, 'f154c17f55', '5864493a02', '36d1464083', NULL, NULL, '2025-02-07', '197102222001121001', 'PENGUJI KP'),
(1738917179, '149/UN47.B5.5/TD.06/2025', '521421025', 'fad50ffmahe', NULL, '5864493a02', 'f154c17f55', '36d1464083', NULL, NULL, '2025-02-07', '197102222001121001', 'PENGUJI KP'),
(1738917202, '150/UN47.B5.5/TD.06/2025', '521421007', 'fad50ffmahe', NULL, '5864493a02', 'f154c17f55', '36d1464083', NULL, NULL, '2025-02-07', '197102222001121001', 'PENGUJI KP'),
(1739259228, '159/UN47.B5.5/TD.06/2025', '521421012', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-11', '197102222001121001', 'PEMBIMBING KP'),
(1739264083, '161/UN47.B5.5/TD.06/2025', '521421003', 'fad50ffmahe', NULL, '36d1464083', '1a127ddb8e', 'a5e8c20ec3', NULL, NULL, '2025-02-11', '197102222001121001', 'PENGUJI KP'),
(1739523554, '170/UN47.B5.5/TD.06/2025', '521421049', 'fad50ffmahe', NULL, 'e8e4016ded', '274eccd1e7', '645eef9c30', NULL, NULL, '2025-02-14', '197102222001121001', 'PENGUJI KP'),
(1740114147, '185/UN47.B5.5/TD.06/2025', '521420030', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', '916933c219', '2025-02-21', '197102222001121001', 'PENGUJI HASIL'),
(1740447852, '191/UN47.B5.5/TD.06/2025', '521419016', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-02-25', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1740545794, '194/UN47.B5.5/TD.06/2025', '521420001', 'f154c17f55', '6be551a649', NULL, NULL, NULL, NULL, NULL, '2025-02-26', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1740546071, '195/UN47.B5.5/TD.06/2025', '521420016', '645eef9c30', '6806f5f8a7', NULL, NULL, NULL, NULL, NULL, '2025-02-26', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1740972273, '201/UN47.B5.5/TD.06/2025', '521421054', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972298, '202/UN47.B5.5/TD.06/2025', '521421015', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972318, '203/UN47.B5.5/TD.06/2025', '521421006', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972338, '204/UN47.B5.5/TD.06/2025', '521421013', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972370, '205/UN47.B5.5/TD.06/2025', '521421038', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972414, '206/UN47.B5.5/TD.06/2025', '521421035', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972438, '207/UN47.B5.5/TD.06/2025', '521421048', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972465, '208/UN47.B5.5/TD.06/2025', '521421030', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972486, '209/UN47.B5.5/TD.06/2025', '521421037', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972601, '210/UN47.B5.5/TD.06/2025', '521421051', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1740972618, '211/UN47.B5.5/TD.06/2025', '521421014', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-03', '197102222001121001', 'PEMBIMBING KP'),
(1741224966, '217/UN47.B5.5/TD.06/2025', '521421029', 'fad50ffmahe', NULL, '92459d083c', 'f53eb48005', '916933c219', NULL, NULL, '2025-03-06', '197102222001121001', 'PENGUJI KP'),
(1741225299, '218/UN47.B5.5/TD.06/2025', '521420011', 'fad50ffmahe', NULL, '7fbcc3969e', '637dfbddee', 'a5e8c20ec3', NULL, NULL, '2025-03-06', '197102222001121001', 'PENGUJI KP'),
(1741225328, '219/UN47.B5.5/TD.06/2025', '521420014', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '637dfbddee', '2025-03-06', '197102222001121001', 'PENGUJI TUTUP'),
(1741239298, '223/UN47.B5.5/TD.06/2025', '521420005', '5864493a02', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, '2025-03-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1741916963, '239/UN47.B5.5/TD.06/2025', '521419016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '7fbcc3969e', '2025-03-14', '197102222001121001', 'PENGUJI HASIL'),
(1741916990, '240/UN47.B5.5/TD.06/2025', '521418010', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '637dfbddee', 'a5e8c20ec3', '2025-03-14', '197102222001121001', 'PENGUJI HASIL'),
(1742177682, '243/UN47.B5.5/TD.06/2025', '521421050', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-17', '197102222001121001', 'PEMBIMBING KP'),
(1742177748, '244/UN47.B5.5/TD.06/2025', '521421004', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-17', '197102222001121001', 'PEMBIMBING KP'),
(1742281750, '246/UN47.B5.5/TD.06/2025', '521420003', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '637dfbddee', '7fbcc3969e', '2025-03-18', '197102222001121001', 'PENGUJI TUTUP'),
(1742281770, '247/UN47.B5.5/TD.06/2025', '521420036', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '637dfbddee', 'a5e8c20ec3', '2025-03-18', '197102222001121001', 'PENGUJI TUTUP'),
(1744770808, '261/UN47.B5.5/TD.06/2025', '521418029', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2025-04-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1744854260, '263/UN47.B5.5/TD.06/2025', '521418032', '645eef9c30', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, '2025-04-17', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1744854288, '264/UN47.B5.5/TD.06/2025', '521418055', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-04-17', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1744854738, '265/UN47.B5.5/TD.06/2025', '521418045', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2025-04-17', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1745556280, '281/UN47.B5.5/TD.06/2025', '521418005', 'f154c17f55', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, '2025-04-25', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1745560434, '282/UN47.B5.5/TD.06/2025', '521418026', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2025-04-25', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1746417340, '297/UN47.B5.5/TD.06/2025', '521418035', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '7bf1f8337d', '274eccd1e7', '36d1464083', '2025-05-05', '197102222001121001', 'PENGUJI HASIL'),
(1746417516, '298/UN47.B5.5/TD.06/2025', '521418038', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7bf1f8337d', '5864493a02', '7fbcc3969e', '2025-05-05', '197102222001121001', 'PENGUJI HASIL'),
(1746417619, '299/UN47.B5.5/TD.06/2025', '521419015', 'fad50ffmahe', NULL, '1a127ddb8e', '6eba3d13ea', '92459d083c', NULL, NULL, '2025-05-05', '197102222001121001', 'PENGUJI KP'),
(1746598368, '307/UN47.B5.5/TD.06/2025', '521419016', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '7fbcc3969e', '2025-05-07', '197102222001121001', 'PENGUJI TUTUP'),
(1746598383, '308/UN47.B5.5/TD.06/2025', '521418010', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '36d1464083', '637dfbddee', 'a5e8c20ec3', '2025-05-07', '197102222001121001', 'PENGUJI TUTUP'),
(1746605707, '309/UN47.B5.5/TD.06/2025', '521418017', '7fbcc3969e', '645eef9c30', NULL, NULL, NULL, NULL, NULL, '2025-05-07', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1746682513, '313/UN47.B5.5/TD.06/2025', '521420033', 'fad50ffmahe', NULL, '645eef9c30', 'f154c17f55', '92459d083c', 'e8e4016ded', '916933c219', '2025-05-08', '197102222001121001', 'PENGUJI HASIL'),
(1746688100, '315/UN47.B5.5/TD.06/2025', '521418057', 'fad50ffmahe', NULL, 'd53ee838e9', '5864493a02', '7bf1f8337d', '7fbcc3969e', '36d1464083', '2025-05-08', '197102222001121001', 'PENGUJI HASIL'),
(1746758003, '317/UN47.B5.5/TD.06/2025', '521418004', 'fad50ffmahe', NULL, 'f53eb48005', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '645eef9c30', '2025-05-09', '197102222001121001', 'PENGUJI HASIL');
INSERT INTO `surat_penunjukkan` (`id`, `no_surat`, `nim`, `pembimbing1`, `pembimbing2`, `penguji1`, `penguji2`, `penguji3`, `penguji4`, `penguji5`, `tanggal`, `ttd`, `keterangan`) VALUES
(1746766200, '320/UN47.B5.5/TD.06/2025', '521421051', 'fad50ffmahe', NULL, 'f53eb48005', '6eba3d13ea', '1a127ddb8e', NULL, NULL, '2025-05-09', '197102222001121001', 'PENGUJI KP'),
(1746767314, '321/UN47.B5.5/TD.06/2025', '521418055', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', 'a5e8c20ec3', '2025-05-09', '197102222001121001', 'PENGUJI HASIL'),
(1746769464, '323/UN47.B5.5/TD.06/2025', '521421001', 'fad50ffmahe', NULL, '637dfbddee', '274eccd1e7', '36d1464083', NULL, NULL, '2025-05-09', '197102222001121001', 'PENGUJI KP'),
(1746769501, '324/UN47.B5.5/TD.06/2025', '521421016', 'fad50ffmahe', NULL, 'f53eb48005', '5864493a02', '92459d083c', NULL, NULL, '2025-05-09', '197102222001121001', 'PENGUJI KP'),
(1746769544, '325/UN47.B5.5/TD.06/2025', '521421026', 'fad50ffmahe', NULL, '64809b0cfc', '274eccd1e7', '1a127ddb8e', NULL, NULL, '2025-05-09', '197102222001121001', 'PENGUJI KP'),
(1746769572, '326/UN47.B5.5/TD.06/2025', '521421011', 'fad50ffmahe', NULL, '64809b0cfc', '274eccd1e7', 'd53ee838e9', NULL, NULL, '2025-05-09', '197102222001121001', 'PENGUJI KP'),
(1746772723, '331/UN47.B5.5/TD.06/2025', '521418064', 'fad50ffmahe', NULL, '98a4a6f7b7', '36d1464083', '5864493a02', '7fbcc3969e', 'a5e8c20ec3', '2025-05-09', '197102222001121001', 'PENGUJI HASIL'),
(1747281568, '336/UN47.B5.5/TD.06/2025', '521418018', 'fad50ffmahe', NULL, 'fb4b7ab909', '6eba3d13ea', '645eef9c30', 'f53eb48005', '92459d083c', '2025-05-15', '197102222001121001', 'PENGUJI HASIL'),
(1747286171, '337/UN47.B5.5/TD.06/2025', '521418059', 'fad50ffmahe', NULL, '274eccd1e7', '14c4f7be30', '7bf1f8337d', '36d1464083', '7fbcc3969e', '2025-05-15', '197102222001121001', 'PENGUJI HASIL'),
(1747375576, '342/UN47.B5.5/TD.06/2025', '521421013', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', 'a5e8c20ec3', NULL, NULL, '2025-05-16', '197102222001121001', 'PENGUJI KP'),
(1747375830, '343/UN47.B5.5/TD.06/2025', '521421006', 'fad50ffmahe', NULL, 'd53ee838e9', 'fb4b7ab909', '637dfbddee', NULL, NULL, '2025-05-16', '197102222001121001', 'PENGUJI KP'),
(1747376532, '344/UN47.B5.5/TD.06/2025', '521421038', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '7fbcc3969e', NULL, NULL, '2025-05-16', '197102222001121001', 'PENGUJI KP'),
(1747634327, '349/UN47.B5.5/TD.06/2025', '521418066', 'fad50ffmahe', NULL, '98a4a6f7b7', '14c4f7be30', '274eccd1e7', '637dfbddee', '36d1464083', '2025-05-19', '197102222001121001', 'PENGUJI HASIL'),
(1747634611, '351/UN47.B5.5/TD.06/2025', '521421037', 'fad50ffmahe', NULL, '92459d083c', 'fb4b7ab909', 'f53eb48005', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747634657, '352/UN47.B5.5/TD.06/2025', '521421014', 'fad50ffmahe', NULL, '1a127ddb8e', '64809b0cfc', '916933c219', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747635267, '353/UN47.B5.5/TD.06/2025', '521421055', 'fad50ffmahe', NULL, '274eccd1e7', '14c4f7be30', '637dfbddee', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747635308, '354/UN47.B5.5/TD.06/2025', '521419024', 'fad50ffmahe', NULL, 'd53ee838e9', '645eef9c30', 'f154c17f55', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747637580, '359/UN47.B5.5/TD.06/2025', '521421022', 'fad50ffmahe', NULL, '36d1464083', '1a127ddb8e', 'a5e8c20ec3', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747637896, '361/UN47.B5.5/TD.06/2025', '521421012', 'fad50ffmahe', NULL, '916933c219', '5864493a02', '7fbcc3969e', NULL, NULL, '2025-05-19', '197102222001121001', 'PENGUJI KP'),
(1747723722, '365/UN47.B5.5/TD.06/2025', '521418032', 'fad50ffmahe', NULL, '645eef9c30', '6eba3d13ea', 'd53ee838e9', 'fb4b7ab909', '92459d083c', '2025-05-20', '197102222001121001', 'PENGUJI HASIL'),
(1747968052, '372/UN47.B5.5/TD.06/2025', '521418006', 'fad50ffmahe', NULL, '1a127ddb8e', 'f53eb48005', 'd53ee838e9', '92459d083c', '645eef9c30', '2025-05-23', '197102222001121001', 'PENGUJI HASIL'),
(1747968080, '373/UN47.B5.5/TD.06/2025', '521418017', 'fad50ffmahe', NULL, '7fbcc3969e', '645eef9c30', '7bf1f8337d', '5864493a02', 'a5e8c20ec3', '2025-05-23', '197102222001121001', 'PENGUJI HASIL'),
(1747973622, '375/UN47.B5.5/TD.06/2025', '521418035', 'fad50ffmahe', NULL, '637dfbddee', '7fbcc3969e', '7bf1f8337d', '274eccd1e7', '36d1464083', '2025-05-23', '197102222001121001', 'PENGUJI TUTUP'),
(1747973652, '376/UN47.B5.5/TD.06/2025', '521420030', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', '916933c219', '2025-05-23', '197102222001121001', 'PENGUJI TUTUP'),
(1747985058, '382/UN47.B5.5/TD.06/2025', '521420039', 'fad50ffmahe', NULL, '36d1464083', 'f53eb48005', '7fbcc3969e', NULL, NULL, '2025-05-23', '197102222001121001', 'PENGUJI KP'),
(1747985088, '383/UN47.B5.5/TD.06/2025', '521421041', 'fad50ffmahe', NULL, 'a5e8c20ec3', '7bf1f8337d', '5864493a02', NULL, NULL, '2025-05-23', '197102222001121001', 'PENGUJI KP'),
(1747985125, '384/UN47.B5.5/TD.06/2025', '521421054', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', '36d1464083', NULL, NULL, '2025-05-23', '197102222001121001', 'PENGUJI KP'),
(1747985920, '388/UN47.B5.5/TD.06/2025', '521418038', 'fad50ffmahe', NULL, '274eccd1e7', '36d1464083', '7bf1f8337d', '5864493a02', '7fbcc3969e', '2025-05-23', '197102222001121001', 'PENGUJI TUTUP'),
(1748137309, '391/UN47.B5.5/TD.06/2025', '521421030', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '916933c219', NULL, NULL, '2025-05-25', '197102222001121001', 'PENGUJI KP'),
(1748247404, '393/UN47.B5.5/TD.06/2025', '521418029', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-05-26', '197102222001121001', 'PENGUJI HASIL'),
(1748326551, '398/UN47.B5.5/TD.06/2025', '521418005', 'fad50ffmahe', NULL, 'f154c17f55', 'e8e4016ded', '6eba3d13ea', '92459d083c', '916933c219', '2025-05-27', '197102222001121001', 'PENGUJI HASIL'),
(1748419320, '401/UN47.B5.5/TD.06/2025', '521418015', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '36d1464083', 'a5e8c20ec3', '2025-05-28', '197102222001121001', 'PENGUJI HASIL'),
(1748419612, '402/UN47.B5.5/TD.06/2025', '521418026', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-05-28', '197102222001121001', 'PENGUJI HASIL'),
(1748419845, '403/UN47.B5.5/TD.06/2025', '521418045', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-05-28', '197102222001121001', 'PENGUJI HASIL'),
(1748606788, '407/UN47.B5.5/TD.06/2025', '521418055', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '637dfbddee', '7fbcc3969e', 'a5e8c20ec3', '2025-05-30', '197102222001121001', 'PENGUJI TUTUP'),
(1748606805, '408/UN47.B5.5/TD.06/2025', '521418064', 'fad50ffmahe', NULL, '98a4a6f7b7', '36d1464083', '5864493a02', '7fbcc3969e', 'a5e8c20ec3', '2025-05-30', '197102222001121001', 'PENGUJI TUTUP'),
(1748742643, '411/UN47.B5.5/TD.06/2025', '521418004', 'fad50ffmahe', NULL, 'f53eb48005', 'fb4b7ab909', 'd53ee838e9', '92459d083c', '645eef9c30', '2025-06-01', '197102222001121001', 'PENGUJI TUTUP'),
(1749176773, '423/UN47.B5.5/TD.06/2025', '521420033', 'fad50ffmahe', NULL, '645eef9c30', 'f154c17f55', '92459d083c', 'e8e4016ded', '916933c219', '2025-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1749176800, '424/UN47.B5.5/TD.06/2025', '521418059', 'fad50ffmahe', NULL, '274eccd1e7', '14c4f7be30', '7bf1f8337d', '36d1464083', '7fbcc3969e', '2025-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1749176823, '425/UN47.B5.5/TD.06/2025', '521418032', 'fad50ffmahe', NULL, '645eef9c30', '6eba3d13ea', 'd53ee838e9', 'fb4b7ab909', '92459d083c', '2025-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1749176838, '426/UN47.B5.5/TD.06/2025', '521418018', 'fad50ffmahe', NULL, 'fb4b7ab909', '6eba3d13ea', '645eef9c30', 'f53eb48005', '92459d083c', '2025-06-06', '197102222001121001', 'PENGUJI TUTUP'),
(1749612563, '444/UN47.B5.5/TD.06/2025', '521418029', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-06-11', '197102222001121001', 'PENGUJI TUTUP'),
(1749612575, '445/UN47.B5.5/TD.06/2025', '521418057', 'fad50ffmahe', NULL, 'd53ee838e9', '5864493a02', '7bf1f8337d', '7fbcc3969e', '36d1464083', '2025-06-11', '197102222001121001', 'PENGUJI TUTUP'),
(1749621679, '448/UN47.B5.5/TD.06/2025', '521418006', 'fad50ffmahe', NULL, '1a127ddb8e', 'f53eb48005', 'd53ee838e9', '92459d083c', '645eef9c30', '2025-06-11', '197102222001121001', 'PENGUJI TUTUP'),
(1749728251, '456/UN47.B5.5/TD.06/2025', '521418005', 'fad50ffmahe', NULL, 'f154c17f55', 'e8e4016ded', '6eba3d13ea', '92459d083c', '916933c219', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1749728276, '457/UN47.B5.5/TD.06/2025', '521418017', 'fad50ffmahe', NULL, '7fbcc3969e', '645eef9c30', '7bf1f8337d', '5864493a02', 'a5e8c20ec3', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1749728292, '458/UN47.B5.5/TD.06/2025', '521418066', 'fad50ffmahe', NULL, '98a4a6f7b7', '14c4f7be30', '274eccd1e7', '637dfbddee', '36d1464083', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1749728312, '459/UN47.B5.5/TD.06/2025', '521418015', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '274eccd1e7', '36d1464083', 'a5e8c20ec3', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1749728326, '460/UN47.B5.5/TD.06/2025', '521418045', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1749728370, '461/UN47.B5.5/TD.06/2025', '521418026', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', '7bf1f8337d', '7fbcc3969e', 'a5e8c20ec3', '2025-06-12', '197102222001121001', 'PENGUJI TUTUP'),
(1750299961, '449/UN47.B5.5/TD.06/2025', '521422041', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300089, '500/UN47.B5.5/TD.06/2025', '521421024', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300254, '501/UN47.B5.5/TD.06/2025', '521422022', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300298, '502/UN47.B5.5/TD.06/2025', '521422062', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300405, '503/UN47.B5.5/TD.06/2025', '521422047', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300464, '504/UN47.B5.5/TD.06/2025', '521421053', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750300569, '505/UN47.B5.5/TD.06/2025', '521422003', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750302270, '506/UN47.B5.5/TD.06/2025', '521422034', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750302294, '507/UN47.B5.5/TD.06/2025', '521422048', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750302319, '508/UN47.B5.5/TD.06/2025', '521422027', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307677, '509/UN47.B5.5/TD.06/2025', '521422012', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307746, '510/UN47.B5.5/TD.06/2025', '521422029', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307830, '511/UN47.B5.5/TD.06/2025', '521422045', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307868, '512/UN47.B5.5/TD.06/2025', '521422063', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307932, '513/UN47.B5.5/TD.06/2025', '521422040', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750307957, '514/UN47.B5.5/TD.06/2025', '521422025', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750308026, '515/UN47.B5.5/TD.06/2025', '521422031', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750308054, '516/UN47.B5.5/TD.06/2025', '521422009', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750308075, '517/UN47.B5.5/TD.06/2025', '521422049', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750308105, '518/UN47.B5.5/TD.06/2025', '521422019', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750311109, '519/UN47.B5.5/TD.06/2025', '521422072', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750311131, '520/UN47.B5.5/TD.06/2025', '521422071', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750311206, '521/UN47.B5.5/TD.06/2025', '521422033', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19', '197102222001121001', 'PEMBIMBING KP'),
(1750391202, '527/UN47.B5.5/TD.06/2025', '521422020', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-20', '197102222001121001', 'PEMBIMBING KP'),
(1750391279, '528/UN47.B5.5/TD.06/2025', '521422074', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-20', '197102222001121001', 'PEMBIMBING KP'),
(1750391294, '529/UN47.B5.5/TD.06/2025', '521422051', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-20', '197102222001121001', 'PEMBIMBING KP'),
(1750732334, '535/UN47.B5.5/TD.06/2025', '521422037', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24', '197102222001121001', 'PEMBIMBING KP'),
(1750732596, '536/UN47.B5.5/TD.06/2025', '521422058', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24', '197102222001121001', 'PEMBIMBING KP'),
(1750732612, '537/UN47.B5.5/TD.06/2025', '521422053', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-24', '197102222001121001', 'PEMBIMBING KP'),
(1751247762, '570/UN47.B5.5/TD.06/2025', '521422007', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-30', '197102222001121001', 'PEMBIMBING KP'),
(1751250633, '572/UN47.B5.5/TD.06/2025', '521422018', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-30', '197102222001121001', 'PEMBIMBING KP'),
(1751507621, '599/UN47.B5.5/TD.06/2025', '521419011', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-03', '197102222001121001', 'PEMBIMBING KP'),
(1751507651, '600/UN47.B5.5/TD.06/2025', '521422039', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-03', '197102222001121001', 'PEMBIMBING KP'),
(1751507684, '601/UN47.B5.5/TD.06/2025', '521422054', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-03', '197102222001121001', 'PEMBIMBING KP'),
(1751530716, '606/UN47.B5.5/TD.06/2025', '521420019', '5864493a02', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2025-07-03', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1752052478, '619/UN47.B5.5/TD.06/2025', '521422059', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-09', '197102222001121001', 'PEMBIMBING KP'),
(1752631406, '632/UN47.B5.5/TD.06/2025', '521419023', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-07-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1756867954, '702/UN47.B5.5/TD.06/2025', '521419017', 'fad50ffmahe', NULL, '7fbcc3969e', '274eccd1e7', 'f53eb48005', '5864493a02', '36d1464083', '2025-09-03', '197102222001121001', 'PENGUJI HASIL'),
(1756869958, '703/UN47.B5.5/TD.06/2025', '521419023', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '637dfbddee', '2025-09-03', '197102222001121001', 'PENGUJI HASIL'),
(1756871945, '706/UN47.B5.5/TD.06/2025', '521420001', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', 'f53eb48005', '64809b0cfc', 'd53ee838e9', '2025-09-03', '197102222001121001', 'PENGUJI HASIL'),
(1756872190, '707/UN47.B5.5/TD.06/2025', '521420027', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', '6eba3d13ea', '64809b0cfc', 'e8e4016ded', '2025-09-03', '197102222001121001', 'PENGUJI HASIL'),
(1756872222, '708/UN47.B5.5/TD.06/2025', '521420038', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', 'a5e8c20ec3', '2025-09-03', '197102222001121001', 'PENGUJI HASIL'),
(1756872291, '709/UN47.B5.5/TD.06/2025', '521421035', 'fad50ffmahe', NULL, 'f154c17f55', '7bf1f8337d', '14c4f7be30', NULL, NULL, '2025-09-03', '197102222001121001', 'PENGUJI KP'),
(1756872323, '710/UN47.B5.5/TD.06/2025', '521422062', 'fad50ffmahe', NULL, '14c4f7be30', 'd53ee838e9', 'fb4b7ab909', NULL, NULL, '2025-09-03', '197102222001121001', 'PENGUJI KP'),
(1758344787, '738/UN47.B5.5/TD.06/2025', '521420018', 'fad50ffmahe', NULL, '637dfbddee', 'e8e4016ded', 'fb4b7ab909', '92459d083c', '645eef9c30', '2025-09-20', '197102222001121001', 'PENGUJI HASIL'),
(1758344813, '739/UN47.B5.5/TD.06/2025', '521420010', 'fad50ffmahe', NULL, '637dfbddee', '36d1464083', '98a4a6f7b7', '7bf1f8337d', '7fbcc3969e', '2025-09-20', '197102222001121001', 'PENGUJI HASIL'),
(1758344907, '740/UN47.B5.5/TD.06/2025', '521421050', 'fad50ffmahe', NULL, '6eba3d13ea', '7bf1f8337d', '7fbcc3969e', NULL, NULL, '2025-09-20', '197102222001121001', 'PENGUJI KP'),
(1759195085, '752/UN47.B5.5/TD.06/2025', '521419019', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-09-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759195101, '753/UN47.B5.5/TD.06/2025', '521420021', '7bf1f8337d', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2025-09-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759195119, '754/UN47.B5.5/TD.06/2025', '521421023', '6eba3d13ea', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2025-09-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759296367, '755/UN47.B5.5/TD.06/2025', '521419008', '637dfbddee', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2025-10-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759296518, '756/UN47.B5.5/TD.06/2025', '521419041', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-10-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759991761, '768/UN47.B5.5/TD.06/2025', '521420007', '14c4f7be30', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2025-10-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1759991814, '769/UN47.B5.5/TD.06/2025', '521419037', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2025-10-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1760073597, '772/UN47.B5.5/TD.06/2025', '521419020', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '637dfbddee', '2025-10-10', '197102222001121001', 'PENGUJI HASIL'),
(1760075548, '771/UN47.B5.5/TD.06/2025', '521422037', 'fad50ffmahe', NULL, 'f154c17f55', '64809b0cfc', '92459d083c', NULL, NULL, '2025-10-10', '197102222001121001', 'PENGUJI KP'),
(1760077748, '777/UN47.B5.5/TD.06/2025', '521420022', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '7bf1f8337d', 'a5e8c20ec3', '2025-10-10', '197102222001121001', 'PENGUJI HASIL'),
(1760336358, '778/UN47.B5.5/TD.06/2025', '521420005', 'fad50ffmahe', NULL, '5864493a02', 'a5e8c20ec3', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2025-10-13', '197102222001121001', 'PENGUJI HASIL'),
(1760336941, '779/UN47.B5.5/TD.06/2025', '521420035', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', 'd53ee838e9', 'fb4b7ab909', '645eef9c30', '2025-10-13', '197102222001121001', 'PENGUJI HASIL'),
(1760511227, '784/UN47.B5.5/TD.06/2025', '521420034', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '7fbcc3969e', NULL, NULL, '2025-10-15', '197102222001121001', 'PENGUJI KP'),
(1760511282, '786/UN47.B5.5/TD.06/2025', '521422059', 'fad50ffmahe', NULL, '916933c219', 'f154c17f55', '1a127ddb8e', NULL, NULL, '2025-10-15', '197102222001121001', 'PENGUJI KP'),
(1760591343, '788/UN47.B5.5/TD.06/2025', '521419017', 'fad50ffmahe', NULL, '7fbcc3969e', '274eccd1e7', 'f53eb48005', '5864493a02', '36d1464083', '2025-10-16', '197102222001121001', 'PENGUJI TUTUP'),
(1760594686, '791/UN47.B5.5/TD.06/2025', '521419002', 'f53eb48005', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, '2025-10-16', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1761197743, '800/UN47.B5.5/TD.06/2025', '521419023', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '637dfbddee', '2025-10-23', '197102222001121001', 'PENGUJI TUTUP'),
(1761286335, '804/UN47.B5.5/TD.06/2025', '521422029', 'fad50ffmahe', NULL, '7bf1f8337d', '5864493a02', '36d1464083', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761286572, '805/UN47.B5.5/TD.06/2025', '521419011', 'fad50ffmahe', NULL, 'fb4b7ab909', 'e8e4016ded', '14c4f7be30', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761286672, '806/UN47.B5.5/TD.06/2025', '521422003', 'fad50ffmahe', NULL, 'fb4b7ab909', '14c4f7be30', '64809b0cfc', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761286751, '807/UN47.B5.5/TD.06/2025', '521421015', 'fad50ffmahe', NULL, '1a127ddb8e', 'd53ee838e9', 'e8e4016ded', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761287926, '808/UN47.B5.5/TD.06/2025', '521421024', 'fad50ffmahe', NULL, 'd53ee838e9', '1a127ddb8e', '64809b0cfc', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761289073, '814/UN47.B5.5/TD.06/2025', '521422041', 'fad50ffmahe', NULL, 'f154c17f55', '645eef9c30', 'fb4b7ab909', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761289106, '815/UN47.B5.5/TD.06/2025', '521422072', 'fad50ffmahe', NULL, '92459d083c', '64809b0cfc', '916933c219', NULL, NULL, '2025-10-24', '197102222001121001', 'PENGUJI KP'),
(1761536921, '814/UN47.B5.5/TD.06/2025', '521420024', 'fad50ffmahe', NULL, '274eccd1e7', '6eba3d13ea', 'f53eb48005', NULL, NULL, '2025-10-27', '10041993', 'PENGUJI KP'),
(1761893454, '824/UN47.B5.5/PK.01.06/2025', '521420001', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', 'f53eb48005', '64809b0cfc', 'd53ee838e9', '2025-10-31', '197102222001121001', 'PENGUJI TUTUP'),
(1762478393, '829/UN47.B5.5/PK.01.06/2025', '521420027', 'fad50ffmahe', NULL, 'f154c17f55', '6be551a649', '6eba3d13ea', '64809b0cfc', 'e8e4016ded', '2025-11-07', '197102222001121001', 'PENGUJI TUTUP'),
(1762492855, '832/UN47.B5.5/TD.06/2025', '521422031', 'fad50ffmahe', NULL, 'a5e8c20ec3', '36d1464083', 'f53eb48005', NULL, NULL, '2025-11-07', '197102222001121001', 'PENGUJI KP'),
(1762493172, '833/UN47.B5.5/TD.06/2025', '521422033', 'fad50ffmahe', NULL, 'a5e8c20ec3', '92459d083c', 'e8e4016ded', NULL, NULL, '2025-11-07', '197102222001121001', 'PENGUJI KP'),
(1762838676, '836/UN47.B5.5/TD.06/2025', '521422035', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING KP'),
(1762838723, '837/UN47.B5.5/TD.06/2025', '521422008', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING KP'),
(1762838996, '837/UN47.B5.5/TD.06/2025', '521422044', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING KP'),
(1762839077, '839/UN47.B5.5/TD.06/2025', '521422006', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING KP'),
(1762839168, '840/UN47.B5.5/TD.06/2025', '521422056', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING KP'),
(1762840156, '841/UN47.B5.5/TD.06/2025', '521420004', '645eef9c30', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1762840197, '842/UN47.B5.5/TD.06/2025', '521421037', '6eba3d13ea', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1762840254, '843/UN47.B5.5/TD.06/2025', '521421001', '6eba3d13ea', 'd53ee838e9', NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1762840294, '844/UN47.B5.5/TD.06/2025', '521419015', '5864493a02', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1762844188, '845/UN47.B5.5/TD.06/2025', '521420011', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2025-11-11', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1763098388, '850/UN47.B5.5/TD.06/2025', '521421033', 'fb4b7ab909', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2025-11-14', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1763359376, '853/UN47.B5.5/TD.06/2025', '521422047', 'fad50ffmahe', NULL, 'e8e4016ded', '916933c219', 'a5e8c20ec3', NULL, NULL, '2025-11-17', '197102222001121001', 'PENGUJI KP'),
(1763523414, '859/UN47.B5.5/PK.01.06/2025', '521420022', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '7bf1f8337d', 'a5e8c20ec3', '2025-11-19', '197102222001121001', 'PENGUJI TUTUP'),
(1763536231, '862/UN47.B5.5/PK.01.06/2025', '521420038', 'fad50ffmahe', NULL, '14c4f7be30', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', 'a5e8c20ec3', '2025-11-19', '197102222001121001', 'PENGUJI TUTUP'),
(1763622956, '863/UN47.B5.5/PK.01.06/2025', '521419020', 'fad50ffmahe', NULL, '7fbcc3969e', '14c4f7be30', '98a4a6f7b7', '274eccd1e7', '637dfbddee', '2025-11-20', '197102222001121001', 'PENGUJI TUTUP'),
(1763697251, '865/UN47.B5.5/TD.06/2025', '521420012', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', '1a127ddb8e', 'f53eb48005', 'fb4b7ab909', '2025-11-21', '197102222001121001', 'PENGUJI HASIL'),
(1763697347, '868/UN47.B5.5/TD.06/2025', '521422039', 'fad50ffmahe', NULL, 'fb4b7ab909', '7fbcc3969e', '916933c219', NULL, NULL, '2025-11-21', '197102222001121001', 'PENGUJI KP'),
(1763698650, '870/UN47.B5.5/TD.06/2025', '521422062', '14c4f7be30', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2025-11-21', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1764037949, '862/UN47.B5.5/TD.06/2025', '521422045', 'fad50ffmahe', NULL, 'a5e8c20ec3', '92459d083c', '274eccd1e7', NULL, NULL, '2025-11-25', '197102222001121001', 'PENGUJI KP'),
(1764037999, '874/UN47.B5.5/TD.06/2025', '521421048', 'fad50ffmahe', NULL, 'a5e8c20ec3', 'd53ee838e9', '6eba3d13ea', NULL, NULL, '2025-11-25', '197102222001121001', 'PENGUJI KP'),
(1764210801, '875/UN47.B5.5/TD.06/2025', '521422018', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', '637dfbddee', NULL, NULL, '2025-11-27', '197102222001121001', 'PENGUJI KP'),
(1764210803, '875/UN47.B5.5/TD.06/2025', '521422018', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', '637dfbddee', NULL, NULL, '2025-11-27', '197102222001121001', 'PENGUJI KP'),
(1764210829, '877/UN47.B5.5/TD.06/2025', '521422012', 'fad50ffmahe', NULL, 'f53eb48005', 'fb4b7ab909', '274eccd1e7', NULL, NULL, '2025-11-27', '197102222001121001', 'PENGUJI KP'),
(1764210867, '878/UN47.B5.5/TD.06/2025', '521422007', 'fad50ffmahe', NULL, '1a127ddb8e', 'f154c17f55', '645eef9c30', NULL, NULL, '2025-11-27', '197102222001121001', 'PENGUJI KP'),
(1764300005, '881/UN47.B5.5/TD.06/2025875/UN47.B5.5/TD.06/2025', '521422063', 'fad50ffmahe', NULL, '7bf1f8337d', '5864493a02', '98a4a6f7b7', NULL, NULL, '2025-11-28', '197102222001121001', 'PENGUJI KP'),
(1765152317, '885/UN47.B5.5/TD.06/2025', '521422025', 'fad50ffmahe', NULL, '64809b0cfc', '6eba3d13ea', 'fb4b7ab909', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765152664, '886/UN47.B5.5/TD.06/2025', '521422009', 'fad50ffmahe', NULL, '64809b0cfc', '645eef9c30', 'f154c17f55', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765152861, '887/UN47.B5.5/TD.06/2025', '521422027', 'fad50ffmahe', NULL, '7fbcc3969e', '98a4a6f7b7', '36d1464083', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765153283, '888/UN47.B5.5/TD.06/2025', '521422054', 'fad50ffmahe', NULL, '916933c219', 'f53eb48005', '64809b0cfc', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765153398, '889/UN47.B5.5/TD.06/2025', '521422019', 'fad50ffmahe', NULL, '92459d083c', '5864493a02', '274eccd1e7', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765173594, '896/UN47.B5.5/TD.06/2025', '521422040', 'fad50ffmahe', NULL, 'a5e8c20ec3', '645eef9c30', '14c4f7be30', NULL, NULL, '2025-12-08', '197102222001121001', 'PENGUJI KP'),
(1765843703, '903/UN47.B5.5/TD.06/2025', '521420005', 'fad50ffmahe', NULL, '5864493a02', 'a5e8c20ec3', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2025-12-16', '197102222001121001', 'PENGUJI TUTUP'),
(1765843746, '905/UN47.B5.5/TD.06/2025', '521420018', 'fad50ffmahe', NULL, '637dfbddee', 'e8e4016ded', 'fb4b7ab909', '92459d083c', '645eef9c30', '2025-12-16', '197102222001121001', 'PENGUJI TUTUP'),
(1765853397, '907/UN47.B5.5/TD.06/2025', '521420010', 'fad50ffmahe', NULL, '637dfbddee', '36d1464083', '98a4a6f7b7', '7bf1f8337d', '7fbcc3969e', '2025-12-16', '197102222001121001', 'PENGUJI TUTUP'),
(1765864571, '911/UN47.B5.5/TD.06/2025', '521422071', 'fad50ffmahe', NULL, 'f53eb48005', '92459d083c', '1a127ddb8e', NULL, NULL, '2025-12-16', '197102222001121001', 'PENGUJI KP'),
(1765934454, '914/UN47.B5.5/TD.06/2025', '521419041', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2025-12-17', '197102222001121001', 'PENGUJI HASIL'),
(1765934491, '915/UN47.B5.5/TD.06/2025', '521419037', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '92459d083c', 'f53eb48005', 'fb4b7ab909', '2025-12-17', '197102222001121001', 'PENGUJI HASIL'),
(1766022428, '920/UN47.B5.5/TD.06/2025', '521421049', 'e8e4016ded', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2025-12-18', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1766375054, '922/UN47.B5.5/TD.06/2025', '521421004', 'fad50ffmahe', NULL, '36d1464083', '92459d083c', '1a127ddb8e', NULL, NULL, '2025-12-22', '197102222001121001', 'PENGUJI KP'),
(1767596729, '41/UN47.B5.5/TD.06/2026', '521421003', '637dfbddee', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2026-01-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1767596777, '42/UN47.B5.5/TD.06/2026', '521421038', '14c4f7be30', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2026-01-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1767597093, '43/UN47.B5.5/TD.06/2026', '521421054', '14c4f7be30', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2026-01-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1767688526, '925/UN47.B5.5/TD.06/2025', '521422008', 'fad50ffmahe', NULL, '7fbcc3969e', '274eccd1e7', '5864493a02', NULL, NULL, '2026-01-06', '197102222001121001', 'PENGUJI KP'),
(1767762911, '60/UN47.B5.5/TD.06/2026', '521422030', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07', '197102222001121001', 'PEMBIMBING KP'),
(1768019996, '66/UN47.B5.5/TD.06/2026', '521420035', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', 'd53ee838e9', 'fb4b7ab909', '645eef9c30', '2026-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1768020039, '67/UN47.B5.5/TD.06/2026', '521419041', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '7bf1f8337d', '36d1464083', '2026-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1768020053, '68/UN47.B5.5/TD.06/2026', '521420012', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', '1a127ddb8e', 'f53eb48005', 'fb4b7ab909', '2026-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1768020066, '69/UN47.B5.5/TD.06/2026', '521419037', 'fad50ffmahe', NULL, '274eccd1e7', '637dfbddee', '92459d083c', 'f53eb48005', 'fb4b7ab909', '2026-01-10', '197102222001121001', 'PENGUJI TUTUP'),
(1768181501, '68/UN47.B5.5/TD.06/2026', '521420012', 'fad50ffmahe', NULL, '64809b0cfc', 'f154c17f55', '1a127ddb8e', 'f53eb48005', 'fb4b7ab909', '2026-01-12', '197102222001121001', 'PENGUJI TUTUP'),
(1768438566, '81/UN47.B5.5/TD.06/2026', '521419010', '274eccd1e7', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2026-01-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1768439485, '82/UN47.B5.5/TD.06/2026', '521419011', '274eccd1e7', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2026-01-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1768523510, '83/UN47.B5.5/TD.06/2026', '521420019', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '7bf1f8337d', 'a5e8c20ec3', '2026-01-16', '197102222001121001', 'PENGUJI HASIL'),
(1768523553, '84/UN47.B5.5/TD.06/2026', '521420011', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '7bf1f8337d', '637dfbddee', '36d1464083', '2026-01-16', '197102222001121001', 'PENGUJI HASIL'),
(1768795867, '89/UN47.B5.5/TD.06/2026', '521421051', '6eba3d13ea', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2026-01-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1768800040, '89/UN47.B5.5/TD.06/2025', '521422020', 'fad50ffmahe', NULL, '14c4f7be30', '274eccd1e7', '7fbcc3969e', NULL, NULL, '2026-01-19', '197102222001121001', 'PENGUJI KP'),
(1768984222, '95/UN47.B5.5/TD.06/2026', '521422048', 'fad50ffmahe', NULL, 'f154c17f55', '36d1464083', '5864493a02', NULL, NULL, '2026-01-21', '197102222001121001', 'PENGUJI KP'),
(1770271965, '111/UN47.B5.5/TD.06/2026', '521422037', 'f154c17f55', '916933c219', NULL, NULL, NULL, NULL, NULL, '2026-02-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1770275265, '112/UN47.B5.5/TD.06/2026', '521419027', '64809b0cfc', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2026-02-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1770275331, '113/UN47.B5.5/TD.06/2026', '521421013', '637dfbddee', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2026-02-05', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1770276085, '114/UN47.B5.5/TD.06/2026', '521421033', 'fad50ffmahe', NULL, 'fb4b7ab909', '92459d083c', '64809b0cfc', '645eef9c30', 'a5e8c20ec3', '2026-02-05', '197102222001121001', 'PENGUJI HASIL'),
(1770360379, '116/UN47.B5.5/TD.06/2026', '521421023', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', 'e8e4016ded', '2026-02-06', '197102222001121001', 'PENGUJI HASIL'),
(1770361193, '118/UN47.B5.5/TD.06/2026', '521421037', 'fad50ffmahe', NULL, '6eba3d13ea', '1a127ddb8e', '92459d083c', 'e8e4016ded', '916933c219', '2026-02-06', '197102222001121001', 'PENGUJI HASIL'),
(1770807158, '122/UN47.B5.5/TD.06/2026', '521420023', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', 'fb4b7ab909', NULL, NULL, '2026-02-11', '197102222001121001', 'PENGUJI KP'),
(1770807393, '123/UN47.B5.5/TD.06/2026', '521420017', 'fad50ffmahe', NULL, '14c4f7be30', '36d1464083', 'f154c17f55', NULL, NULL, '2026-02-11', '197102222001121001', 'PENGUJI KP'),
(1772077020, '140/UN47.B5.5/TD.06/2026', '521422053', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'f154c17f55', NULL, NULL, '2026-02-26', '197102222001121001', 'PENGUJI KP'),
(1772423098, '141/UN47.B5.5/TD.06/2026', '521422062', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '36d1464083', '7fbcc3969e', '2026-03-02', '197102222001121001', 'PENGUJI HASIL'),
(1772604713, '146/UN47.B5.5/TD.06/2026', '521422030', 'fad50ffmahe', NULL, '5864493a02', '36d1464083', 'a5e8c20ec3', NULL, NULL, '2026-03-04', '197102222001121001', 'PENGUJI KP'),
(1772775072, '151/UN47.B5.5/TD.06/2026', '521419021', '645eef9c30', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, '2026-03-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1773033018, '153/UN47.B5.5/TD.06/2026', '521419019', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '36d1464083', 'a5e8c20ec3', '2026-03-09', '197102222001121001', 'PENGUJI HASIL'),
(1773035690, '155/UN47.B5.5/TD.06/2026', '521421026', '64809b0cfc', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2026-03-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1773040910, '156/UN47.B5.5/TD.06/2026', '521421051', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'f53eb48005', '645eef9c30', 'fb4b7ab909', '2026-03-09', '197102222001121001', 'PENGUJI HASIL'),
(1774577692, '166/UN47.B5.5/TD.06/2026', '521422009', '14c4f7be30', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, '2026-03-27', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1775098059, '172/UN47.B5.5/TD.06/2026', '521422031', '98a4a6f7b7', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2026-04-02', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1775633357, '181/UN47.B5.5/TD.06/2026', '521419013', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', 'd53ee838e9', '645eef9c30', '92459d083c', '2026-04-08', '197102222001121001', 'PENGUJI HASIL'),
(1775633382, '182/UN47.B5.5/TD.06/2026', '521419010', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '7fbcc3969e', '36d1464083', '637dfbddee', '2026-04-08', '197102222001121001', 'PENGUJI HASIL'),
(1775633439, '183/UN47.B5.5/TD.06/2026', '521421001', 'fad50ffmahe', NULL, '6eba3d13ea', 'd53ee838e9', '1a127ddb8e', '916933c219', 'a5e8c20ec3', '2026-04-08', '197102222001121001', 'PENGUJI HASIL'),
(1775706026, '187/UN47.B5.5/TD.06/2026', '521419024', '637dfbddee', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2026-04-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1775706050, '188/UN47.B5.5/TD.06/2026', '521421030', '7bf1f8337d', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2026-04-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1775706075, '189/UN47.B5.5/TD.06/2026', '521419032', '274eccd1e7', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, '2026-04-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1776236014, '195/UN47.B5.5/TD.06/2026', '521422003', '5864493a02', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, '2026-04-15', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1776836978, '205/UN47.B5.5/TD.06/2026', '521421049', 'fad50ffmahe', NULL, 'e8e4016ded', 'fb4b7ab909', 'd53ee838e9', '1a127ddb8e', 'a5e8c20ec3', '2026-04-22', '197102222001121001', 'PENGUJI HASIL'),
(1777012784, '207/UN47.B5.5/TD.06/2026', '521419025', '7fbcc3969e', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2026-04-24', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1777274569, '213/UN47.B5.5/TD.06/2026', '521419011', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '2026-04-27', '197102222001121001', 'PENGUJI HASIL'),
(1777532867, '220/UN47.B5.5/TD.06/2026', '521421014', '6eba3d13ea', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2026-04-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1777611546, '222/UN47.B5.5/TD.06/2026', '521421006', '36d1464083', 'a5e8c20ec3', NULL, NULL, NULL, NULL, NULL, '2026-05-01', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1777874785, '224/UN47.B5.5/TD.06/2026', '521422033', '7fbcc3969e', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2026-05-04', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1778817456, '237/UN47.B5.5/TD.06/2026', '521421023', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'd53ee838e9', '1a127ddb8e', 'e8e4016ded', '2026-05-15', '197102222001121001', 'PENGUJI TUTUP'),
(1778817477, '238/UN47.B5.5/TD.06/2026', '521421001', 'fad50ffmahe', NULL, '6eba3d13ea', 'd53ee838e9', '1a127ddb8e', '916933c219', 'a5e8c20ec3', '2026-05-15', '197102222001121001', 'PENGUJI TUTUP'),
(1778817494, '239/UN47.B5.5/TD.06/2026', '521421037', 'fad50ffmahe', NULL, '6eba3d13ea', '1a127ddb8e', '92459d083c', 'e8e4016ded', '916933c219', '2026-05-15', '197102222001121001', 'PENGUJI TUTUP'),
(1779074144, '245/UN47.B5.5/TD.06/2026', '521422062', 'fad50ffmahe', NULL, '14c4f7be30', '637dfbddee', '274eccd1e7', '36d1464083', '7fbcc3969e', '2026-05-18', '197102222001121001', 'PENGUJI TUTUP'),
(1779074173, '246/UN47.B5.5/TD.06/2026', '521420019', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '7bf1f8337d', 'a5e8c20ec3', '2026-05-18', '197102222001121001', 'PENGUJI TUTUP'),
(1779074195, '247/UN47.B5.5/TD.06/2026', '521420011', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '7bf1f8337d', '637dfbddee', '36d1464083', '2026-05-18', '197102222001121001', 'PENGUJI TUTUP'),
(1779168230, '253/UN47.B5.5/TD.06/2026', '521422034', 'fad50ffmahe', NULL, 'e8e4016ded', '637dfbddee', 'f53eb48005', NULL, NULL, '2026-05-19', '197102222001121001', 'PENGUJI KP'),
(1779326278, '256/UN47.B5.5/TD.06/2026', '521419021', 'fad50ffmahe', NULL, '645eef9c30', '1a127ddb8e', '6eba3d13ea', 'd53ee838e9', 'fb4b7ab909', '2026-05-21', '197102222001121001', 'PENGUJI HASIL'),
(1779326333, '257/UN47.B5.5/TD.06/2026', '521422049', 'fad50ffmahe', NULL, '916933c219', 'fb4b7ab909', '645eef9c30', NULL, NULL, '2026-05-21', '197102222001121001', 'PENGUJI KP'),
(1779344781, '260/UN47.B5.5/TD.06/2026', '521419010', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '7fbcc3969e', '36d1464083', '637dfbddee', '2026-05-21', '197102222001121001', 'PENGUJI TUTUP'),
(1779430619, '261/UN47.B5.5/TD.06/2026', '521420016', 'fad50ffmahe', NULL, '645eef9c30', '6806f5f8a7', 'fb4b7ab909', '92459d083c', 'f53eb48005', '2026-05-22', '197102222001121001', 'PENGUJI HASIL'),
(1779681117, '263/UN47.B5.5/TD.06/2026', '521419013', 'fad50ffmahe', NULL, 'fb4b7ab909', '64809b0cfc', 'd53ee838e9', '645eef9c30', '92459d083c', '2026-05-25', '197102222001121001', 'PENGUJI TUTUP'),
(1780021336, '265/UN47.B5.5/TD.06/2026', '521420026', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', 'f154c17f55', '645eef9c30', '2026-05-29', '197102222001121001', 'PENGUJI HASIL'),
(1780454370, '268/UN47.B5.5/TD.06/2026', '521421049', 'fad50ffmahe', NULL, 'e8e4016ded', 'fb4b7ab909', 'd53ee838e9', '1a127ddb8e', 'a5e8c20ec3', '2026-06-03', '197102222001121001', 'PENGUJI TUTUP'),
(1780538978, '271/UN47.B5.5/TD.06/2026', '521421033', 'fad50ffmahe', NULL, 'fb4b7ab909', '92459d083c', '64809b0cfc', '645eef9c30', 'a5e8c20ec3', '2026-06-04', '197102222001121001', 'PENGUJI TUTUP'),
(1780539839, '273/UN47.B5.5/TD.06/2026', '521419002', 'fad50ffmahe', NULL, 'f53eb48005', 'd53ee838e9', '645eef9c30', '5864493a02', '36d1464083', '2026-06-04', '197102222001121001', 'PENGUJI HASIL'),
(1780539915, '274/UN47.B5.5/TD.06/2026', '521419027', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '645eef9c30', 'f53eb48005', '92459d083c', '2026-06-04', '197102222001121001', 'PENGUJI HASIL'),
(1780540002, '272/UN47.B5.5/TD.06/2026', '521419022', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '6eba3d13ea', 'f154c17f55', '92459d083c', '2026-06-04', '197102222001121001', 'PENGUJI HASIL'),
(1780540651, '278/UN47.B5.5/TD.06/2026', '521419015', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', 'a5e8c20ec3', '2026-06-04', '197102222001121001', 'PENGUJI HASIL'),
(1780548574, '280/UN47.B5.5/TD.06/2026', '521422056', 'fad50ffmahe', NULL, 'f53eb48005', '7fbcc3969e', '92459d083c', NULL, NULL, '2026-06-04', '197102222001121001', 'PENGUJI KP'),
(1780884164, '283/UN47.B5.5/TD.06/2026', '521419021', 'fad50ffmahe', NULL, '645eef9c30', '1a127ddb8e', '6eba3d13ea', 'd53ee838e9', 'fb4b7ab909', '2026-06-08', '197102222001121001', 'PENGUJI TUTUP'),
(1780897360, '285/UN47.B5.5/PK.03.05/2026', '521419011', 'fad50ffmahe', NULL, '274eccd1e7', '7fbcc3969e', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '2026-06-08', '197102222001121001', 'PENGUJI TUTUP'),
(1780976112, '289/UN47.B5.5/TD.06/2026', '521421051', 'fad50ffmahe', NULL, '6eba3d13ea', '92459d083c', 'f53eb48005', '645eef9c30', 'fb4b7ab909', '2026-06-09', '197102222001121001', 'PENGUJI TUTUP'),
(1780976225, '292/UN47.B5.5/TD.06/2026', '521419002', 'fad50ffmahe', NULL, 'f53eb48005', 'd53ee838e9', '645eef9c30', '5864493a02', '36d1464083', '2026-06-09', '197102222001121001', 'PENGUJI TUTUP'),
(1780976368, '294/UN47.B5.5/TD.06/2026', '521419024', 'fad50ffmahe', NULL, '637dfbddee', '36d1464083', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2026-06-09', '197102222001121001', 'PENGUJI HASIL'),
(1780976416, '295/UN47.B5.5/TD.06/2026', '521419025', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '274eccd1e7', '7bf1f8337d', '2026-06-09', '197102222001121001', 'PENGUJI HASIL'),
(1780976450, '296/UN47.B5.5/TD.06/2026', '521419032', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '7fbcc3969e', '2026-06-09', '197102222001121001', 'PENGUJI HASIL'),
(1780987974, '300/UN47.B5.5/TD.06/2026', '521421011', '64809b0cfc', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2026-06-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1780987999, '301/UN47.B5.5/TD.06/2026', '521422072', 'fb4b7ab909', '92459d083c', NULL, NULL, NULL, NULL, NULL, '2026-06-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1780990670, '302/UN47.B5.5/TD.06/2026', '521422012', '98a4a6f7b7', '14c4f7be30', NULL, NULL, NULL, NULL, NULL, '2026-06-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1781053875, '303/UN47.B5.5/TD.06/2026', '521422045', '5864493a02', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2026-06-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1781070442, '306/UN47.B5.5/TD.06/2026', '521422037', 'fad50ffmahe', NULL, 'f154c17f55', '916933c219', 'd53ee838e9', '6eba3d13ea', '92459d083c', '2026-06-10', '197102222001121001', 'PENGUJI HASIL'),
(1781072628, '309/UN47.B5.5/TD.06/2026', '521419008', 'fad50ffmahe', NULL, '637dfbddee', '7bf1f8337d', '98a4a6f7b7', '36d1464083', 'a5e8c20ec3', '2026-06-10', '197102222001121001', 'PENGUJI HASIL'),
(1781077375, '310/UN47.B5.5/TD.06/2026', '521420039', '5864493a02', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2026-06-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1781158999, '312/UN47.B5.5/TD.06/2026', '521419019', 'fad50ffmahe', NULL, '274eccd1e7', '5864493a02', '98a4a6f7b7', '36d1464083', 'a5e8c20ec3', '2026-06-11', '197102222001121001', 'PENGUJI TUTUP'),
(1781159015, '313/UN47.B5.5/TD.06/2026', '521419015', 'fad50ffmahe', NULL, '5864493a02', '7fbcc3969e', '98a4a6f7b7', '274eccd1e7', 'a5e8c20ec3', '2026-06-11', '197102222001121001', 'PENGUJI TUTUP'),
(1781507985, '319/UN47.B5.5/TD.06/2026', '521419027', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '645eef9c30', 'f53eb48005', '92459d083c', '2026-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1781509530, '321/UN47.B5.5/TD.06/2026', '521420026', 'fad50ffmahe', NULL, '637dfbddee', '14c4f7be30', '98a4a6f7b7', 'f154c17f55', '645eef9c30', '2026-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1781511447, '323/UN47.B5.5/TD.06/2026', '521419024', 'fad50ffmahe', NULL, '637dfbddee', '36d1464083', '98a4a6f7b7', '274eccd1e7', '7fbcc3969e', '2026-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1781511812, '325/UN47.B5.5/TD.06/2026', '521422037', 'fad50ffmahe', NULL, 'f154c17f55', '916933c219', 'd53ee838e9', '6eba3d13ea', '92459d083c', '2026-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1781512067, '327/UN47.B5.5/TD.06/2026', '521419022', 'fad50ffmahe', NULL, '64809b0cfc', 'fb4b7ab909', '6eba3d13ea', 'f154c17f55', '92459d083c', '2026-06-15', '197102222001121001', 'PENGUJI TUTUP'),
(1781583622, '328/UN47.B5.5/TD.06/2026', '521419008', 'fad50ffmahe', NULL, '637dfbddee', '7bf1f8337d', '98a4a6f7b7', '36d1464083', 'a5e8c20ec3', '2026-06-16', '197102222001121001', 'PENGUJI TUTUP'),
(1781584381, '330/UN47.B5.5/TD.06/2026', '521419032', 'fad50ffmahe', NULL, '274eccd1e7', '98a4a6f7b7', '7bf1f8337d', '5864493a02', '7fbcc3969e', '2026-06-16', '197102222001121001', 'PENGUJI TUTUP'),
(1781588019, '332/UN47.B5.5/TD.06/2026', '521419025', 'fad50ffmahe', NULL, '7fbcc3969e', '5864493a02', '98a4a6f7b7', '274eccd1e7', '7bf1f8337d', '2026-06-16', '197102222001121001', 'PENGUJI TUTUP'),
(1781746444, '325/UN47.B5.5/TD.06/2026', '521422037', 'fad50ffmahe', NULL, 'f154c17f55', '916933c219', 'd53ee838e9', '6eba3d13ea', '92459d083c', '2026-06-18', '197102222001121001', 'PENGUJI TUTUP'),
(1782800240, '376/UN47.B5.5/TD.06/2026', '521422059', 'f154c17f55', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, '2026-06-30', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1783317028, '390/UN47.B5.5/TD.06/2026', '521421055', '7fbcc3969e', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, '2026-07-06', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1783583734, '423/UN47.B5.5/TD.06/2026', '521422029', '7fbcc3969e', '5864493a02', NULL, NULL, NULL, NULL, NULL, '2026-07-09', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1783660928, '424/UN47.B5.5/TD.06/2026', '521420034', '274eccd1e7', '637dfbddee', NULL, NULL, NULL, NULL, NULL, '2026-07-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1786678307, '476/UN47.B5.5/TD.06/2026', '521421052', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678340, '477/UN47.B5.5/TD.06/2026', '521422068', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678388, '478/UN47.B5.5/TD.06/2026', '521422022', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678405, '479/UN47.B5.5/TD.06/2026', '521422042', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678434, '480/UN47.B5.5/TD.06/2026', '521422051', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678464, '481/UN47.B5.5/TD.06/2026', '521422074', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678509, '482/UN47.B5.5/TD.06/2026', '521422052', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678557, '483/UN47.B5.5/TD.06/2026', '521423012', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678585, '484UN47.B5.5/TD.06/2026', '521423024', '92459d083c', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678616, '485/UN47.B5.5/TD.06/2026', '521423008', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678654, '486/UN47.B5.5/TD.06/2026', '521423022', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678706, '487/UN47.B5.5/TD.06/2026', '521423025', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678730, '488/UN47.B5.5/TD.06/2026', '521423001', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678749, '48/UN47.B5.5/TD.06/2026', '521423041', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786678785, '490/UN47.B5.5/TD.06/2026', '521423027', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679005, '491/UN47.B5.5/TD.06/2026', '521423002', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP');
INSERT INTO `surat_penunjukkan` (`id`, `no_surat`, `nim`, `pembimbing1`, `pembimbing2`, `penguji1`, `penguji2`, `penguji3`, `penguji4`, `penguji5`, `tanggal`, `ttd`, `keterangan`) VALUES
(1786679031, '493/UN47.B5.5/TD.06/2026', '521423038', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679058, '494/UN47.B5.5/TD.06/2026', '521423014', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679076, '495/UN47.B5.5/TD.06/2026', '521423003', '637dfbddee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679094, '496/UN47.B5.5/TD.06/2026', '521423006', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679113, '497/UN47.B5.5/TD.06/2026', '521423019', '36d1464083', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679148, '492/UN47.B5.5/TD.06/2026', '521423020', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679168, '498/UN47.B5.5/TD.06/2026', '521423026', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679205, '500/UN47.B5.5/TD.06/2026', '521423021', '98a4a6f7b7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679225, '501/UN47.B5.5/TD.06/2026', '521423029', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679254, '499/UN47.B5.5/TD.06/2026', '521423042', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679278, '502/UN47.B5.5/TD.06/2026', '521423007', '274eccd1e7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679298, '503/UN47.B5.5/TD.06/2026', '521423033', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679332, '504/UN47.B5.5/TD.06/2026', '521423035', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679353, '505/UN47.B5.5/TD.06/2026', '521423031', '7fbcc3969e', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679390, '506/UN47.B5.5/TD.06/2026', '522423010', '4aa69775f4', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679415, '507/UN47.B5.5/TD.06/2026', '522423006', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679436, '508/UN47.B5.5/TD.06/2026', '522423002', '80f75173bc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679457, '509/UN47.B5.5/TD.06/2026', '522423019', '80f75173bc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679481, '510/UN47.B5.5/TD.06/2026', '522423016', 'fad50faeb8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679498, '511/UN47.B5.5/TD.06/2026', '522423011', 'f154c17f55', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679516, '512/UN47.B5.5/TD.06/2026', '522423013', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679543, '513/UN47.B5.5/TD.06/2026', '522423005', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679560, '514/UN47.B5.5/TD.06/2026', '522423008', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679587, '515/UN47.B5.5/TD.06/2026', '522423025', 'e8e4016ded', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679603, '516/UN47.B5.5/TD.06/2026', '522423021', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679629, '517/UN47.B5.5/TD.06/2026', '522423020', '64809b0cfc', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679649, '518/UN47.B5.5/TD.06/2026', '521423028', '5864493a02', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679672, '519/UN47.B5.5/TD.06/2026', '522423009', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1786679694, '520/UN47.B5.5/TD.06/2026', '521421053', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-14', '197102222001121001', 'PEMBIMBING KP'),
(1787126514, '478/UN47.B5.5/TD.06/2026', '521421022', '7fbcc3969e', '36d1464083', NULL, NULL, NULL, NULL, NULL, '2026-08-19', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1788165085, '485/UN47.B5.5/TD.06/2026', '521421016', '1a127ddb8e', '6eba3d13ea', NULL, NULL, NULL, NULL, NULL, '2026-08-31', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1788165120, '481/UN47.B5.5/TD.06/2026', '521422028', '1a127ddb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-31', '197102222001121001', 'PEMBIMBING KP'),
(1788745455, '489/UN47.B5.5/TD.06/2026', '521422047', '5864493a02', '7bf1f8337d', NULL, NULL, NULL, NULL, NULL, '2026-09-07', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1789023976, '498/UN47.B5.5/TD.06/2026', '521421025', '645eef9c30', '6806f5f8a7', NULL, NULL, NULL, NULL, NULL, '2026-09-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1789023987, '499/UN47.B5.5/TD.06/2026', '521421007', '645eef9c30', 'f53eb48005', NULL, NULL, NULL, NULL, NULL, '2026-09-10', '197102222001121001', 'PEMBIMBING SKRIPSI'),
(1789440563, '501/UN47.B5.5/TD.06/2026', '521423009', '916933c219', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15', '197102222001121001', 'PEMBIMBING KP'),
(1789527938, '502/UN47.B5.5/TD.06/2026', '521422043', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-16', '197102222001121001', 'PEMBIMBING KP'),
(1790128119, '505/UN47.B5.5/TD.06/2026', '521423050', '645eef9c30', NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-23', '197102222001121001', 'PEMBIMBING KP'),
(1791777685, 'testing/2026', '05201425003', '64809b0cfc', 'fb4b7ab909', NULL, NULL, NULL, NULL, NULL, '2026-10-12', NULL, 'PEMBIMBING SKRIPSI');

-- --------------------------------------------------------

--
-- Table structure for table `temp_jadwal_seleksi`
--

CREATE TABLE `temp_jadwal_seleksi` (
  `id` int NOT NULL,
  `kode` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `semester` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sks` int NOT NULL,
  `hari` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jam` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pengajar1` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pengajar2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_jadwal_seleksi`
--

INSERT INTO `temp_jadwal_seleksi` (`id`, `kode`, `nama`, `semester`, `sks`, `hari`, `jam`, `kelas`, `pengajar1`, `pengajar2`, `ruangan`, `status`) VALUES
(1, 'EAD61624', 'ELEKTRONIKA DASAR (KULIAH +PRAKTIKUM)', '2', 4, 'KAMIS', '13.00 - 16.20', 'A', 'Salmawaty Tansa, ST., M.Eng', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'RK 2.11', '2022/2023 - GENAP'),
(2, 'EAD61624', 'ELEKTRONIKA DASAR (KULIAH +PRAKTIKUM)', '2', 4, 'KAMIS', '13.00 - 16.20', 'B', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'Ir. Wahab Musa, MT., Ph.D', 'RK 3.16', '2022/2023 - GENAP'),
(3, 'EAD61624', 'ELEKTRONIKA DASAR (KULIAH +PRAKTIKUM)', '2', 4, 'KAMIS', '13.00 - 16.20', 'C', 'Ir. Wahab Musa, MT., Ph.D', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(4, 'EAD61624', 'ELEKTRONIKA DASAR (KULIAH +PRAKTIKUM)', '2', 4, 'KAMIS', '13.00 - 16.20', 'D', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'Salmawaty Tansa, ST., M.Eng', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(5, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '08.00 - 10.30', 'A', 'Ikhsan Hidayat, S.Kom., MT', 'Jumiati Ilham, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(6, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '08.00 - 10.30', 'B', 'Jumiati Ilham, ST., MT', 'Ikhsan Hidayat, S.Kom., MT', 'RK 3.16', '2022/2023 - GENAP'),
(7, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'SENIN', '08.00 - 10.30', 'C', 'Ikhsan Hidayat, S.Kom., MT', 'Jumiati Ilham, ST., MT', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(8, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'SENIN', '08.00 - 10.30', 'D', 'Jumiati Ilham, ST., MT', 'Ikhsan Hidayat, S.Kom., MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(9, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT (KULIAH + TUTORIAL)', '2', 3, 'SELASA', '08.00 - 10.30', 'A', 'Ervan Hasan Harun, ST., MT', 'Ifan Wiranto, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(10, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT (KULIAH + TUTORIAL)', '2', 3, 'SELASA', '08.00 - 10.30', 'B', 'Ifan Wiranto, ST., MT', 'Ervan Hasan Harun, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(11, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT (KULIAH + TUTORIAL)', '2', 3, 'SELASA', '08.00 - 10.30', 'C', 'Taufiq Ismail Yusuf, ST., M.Si', 'Ervan Hasan Harun, ST., MT', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(12, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT (KULIAH + TUTORIAL)', '2', 3, 'SELASA', '08.00 - 10.30', 'D', 'Taufiq Ismail Yusuf, ST., M.Si', 'Ifan Wiranto, ST., MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(13, 'EAD61923', 'ALJABAR LINEAR', '2', 3, 'SENIN', '08.00 - 10.30', 'A', 'Wrastawa Ridwan, ST., MT', 'Rahmat Deddy Rianto Dako, ST., M.Eng', '', '2022/2023 - GENAP'),
(14, 'EAD61923', 'ALJABAR LINEAR', '2', 3, 'SENIN', '08.00 - 10.30', 'B', 'Rahmat Deddy Rianto Dako, ST., M.Eng', 'Wrastawa Ridwan, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(15, 'EAD61923', 'ALJABAR LINEAR', '2', 3, 'RABU', '08.00 - 10.30', 'C', 'Wrastawa Ridwan, ST., MT', 'Rahmat Deddy Rianto Dako, ST., M.Eng', '', '2022/2023 - GENAP'),
(16, 'EAD61923', 'ALJABAR LINEAR', '2', 3, 'RABU', '08.00 - 10.30', 'D', 'Rahmat Deddy Rianto Dako, ST., M.Eng', 'Wrastawa Ridwan, ST., MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(17, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI (KULIAH + PRAKTIKUM)', '2', 4, 'JUMAT', '13.00 - 16.20', 'A', 'Dr. Sardi Salim, M.Pd', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'RK 2.11', '2022/2023 - GENAP'),
(18, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI (KULIAH + PRAKTIKUM)', '2', 4, 'JUMAT', '13.00 - 16.20', 'B', 'Dr. Lanto Mohamad Kamil Amali, ST., MT', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'RK 3.16', '2022/2023 - GENAP'),
(20, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI (KULIAH + PRAKTIKUM)', '2', 4, 'JUMAT', '13.00 - 16.20', 'D', 'Dr. Lanto Mohamad Kamil Amali, ST., MT', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(21, 'NAS5460222', 'PANCASILA', '2', 2, 'KAMIS', '10.30 - 12.10', 'A', 'Tim UNG', '', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(22, 'NAS5460222', 'PANCASILA', '2', 2, 'KAMIS', '10.30 - 12.10', 'B', 'Tim UNG', '', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(23, 'NAS5460222', 'PANCASILA', '2', 2, '', '', 'C', 'Tim UNG', '', '', '2022/2023 - GENAP'),
(24, 'NAS5460222', 'PANCASILA', '2', 2, '', '', 'D', 'Tim UNG', '', '', '2022/2023 - GENAP'),
(25, 'NAS5460322', 'BAHASA INDONESIA', '2', 2, 'SENIN', '10.30 - 12.10', 'A', 'Tim UNG', '', 'RK 2.11', '2022/2023 - GENAP'),
(26, 'NAS5460322', 'BAHASA INDONESIA', '2', 2, 'SENIN', '10.30 - 12.10', 'B', 'Tim UNG', '', 'RK 3.16', '2022/2023 - GENAP'),
(27, 'NAS5460322', 'BAHASA INDONESIA', '2', 2, '', '', 'C', 'Tim UNG', '', '', '2022/2023 - GENAP'),
(28, 'NAS5460322', 'BAHASA INDONESIA', '2', 2, '', '', 'D', 'Tim UNG', '', '', '2022/2023 - GENAP'),
(29, 'EAD62643', 'MEDAN ELEKTROMAGNETIS (KULIAH + TUTORIAL)', '4', 3, 'RABU', '08.00 - 10.30', 'A', 'Ade Irawaty Tolago, ST., MT', 'Ifan Wiranto, ST., MT', 'LAB EL-3', '2022/2023 - GENAP'),
(30, 'EAD62643', 'MEDAN ELEKTROMAGNETIS (KULIAH + TUTORIAL)', '4', 3, 'RABU', '10.30 - 13.00', 'B', 'Ifan Wiranto, ST., MT', 'Ade Irawaty Tolago, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(31, 'EAD62744', 'METODE NUMERIS (KULIAH + PRAKTIKUM)', '4', 4, 'KAMIS', '08.00 - 11.20', 'A', 'Syahrir Abdussamad, ST., MT', 'Ir. Wahab Musa, MT., Ph.D', 'RK 2.11', '2022/2023 - GENAP'),
(32, 'EAD62744', 'METODE NUMERIS (KULIAH + PRAKTIKUM)', '4', 4, 'SELASA', '08.00 - 11.20', 'B', 'Ir. Wahab Musa, MT., Ph.D', 'Syahrir Abdussamad, ST., MT', 'LAB EL-3', '2022/2023 - GENAP'),
(33, 'EAD62844', 'ENERGI BARU TERBARUKAN (KULIAH + PRAKTIKUM)', '4', 4, 'SELASA', '13.00 - 16.20', 'A', 'Jumiati Ilham, ST., MT', 'Dr. Ir Arifin Matoka, MT', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(34, 'EAD62844', 'ENERGI BARU TERBARUKAN (KULIAH + PRAKTIKUM)', '4', 4, 'SABTU', '08.00 - 11.20', 'B', 'Dr. Sardi Salim, M.Pd', 'Dr. Lanto Mohamad Kamil Amali, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(35, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR (KULIAH + PRAKTIKUM)', '4', 4, 'KAMIS', '07.00 - 10.20', 'A', 'Yasin Mohamad, ST., MT', 'Ervan Hasan Harun, ST., MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(36, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR (KULIAH + PRAKTIKUM)', '4', 4, 'SABTU', '13.00 - 16.20', 'B', 'Ervan Hasan Harun, ST., MT', 'Yasin Mohamad, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(37, 'EAD63044', 'TEKNIK KENDALI DASAR (KULIAH + PRAKTIKUM)', '4', 4, 'SENIN', '13.00 - 16.20', 'A', 'Wrastawa Ridwan, ST., MT', 'Ifan Wiranto, ST., MT', '', '2022/2023 - GENAP'),
(38, 'EAD63044', 'TEKNIK KENDALI DASAR (KULIAH + PRAKTIKUM)', '4', 4, 'JUMAT', '08.00 - 11.20', 'B', 'Ifan Wiranto, ST., MT', 'Wrastawa Ridwan, ST., MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(39, 'UNG5460642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'JUMAT', '08.00 -09.40', 'A', 'Tim UNG', '', 'RK 3.16', '2022/2023 - GENAP'),
(40, 'UNG5460642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'JUMAT', '16.20 - 18.00', 'B', 'Tim UNG', '', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(41, 'EAD63764', 'TEKNIK TEGANGAN TINGGI (TEORI + PRAKTIKUM)', '6', 4, 'SENIN', '13.00 - 16.20', 'A', 'Dr. Lanto Mohamad Kamil Amali, ST., MT', 'Ade Irawaty Tolago, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(42, 'EAD63864', 'TEKNIK PROTEKSI (TEORI + PRAKTIKUM)', '6', 4, 'SENIN', '08.00 - 11.20', 'A', 'Ade Irawaty Tolago, ST., MT', 'Taufiq Ismail Yusuf, ST., M.Si', 'LAB EL-3', '2022/2023 - GENAP'),
(43, 'EAD63864', 'TEKNIK PROTEKSI (TEORI + PRAKTIKUM)', '6', 4, 'SELASA', '13.00 - 16.20', 'B', 'Taufiq Ismail Yusuf, ST., M.Si', 'Ade Irawaty Tolago, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(44, 'EAD63963', 'ANALISA SISTEM TENAGA LISTRIK', '6', 3, 'KAMIS', '13.00 -15.30', 'A', 'Ervan Hasan Harun, ST., MT', 'Yasin Mohamad, ST., MT', 'LAB EL-3', '2022/2023 - GENAP'),
(45, 'EAD65263', 'TEKNIK ANTENA', '6', 3, 'RABU', '13.00 - 15.30', 'A', 'Zainudin Bonok, ST., MT', 'Syahrir Abdussamad, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(46, 'EAD65362', 'PERAMBATAN GELOMBANG ELEKTROMAGNETIK', '6', 2, 'SELASA', '13.00 - 14.40', 'A', 'Zainudin Bonok, ST., MT', 'Ir. Wahab Musa, MT., Ph.D', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(47, 'EAD65462', 'REKAYASA TRAFIK', '6', 2, 'SABTU', '08.00 - 09.40', 'A', 'Bambang Panji Asmara, ST., MT', 'Zainudin Bonok, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(48, 'EAD65562', 'TRANSMISI TELEKOMUNIKASI', '6', 2, 'KAMIS', '13.00 - 14.40', 'A', 'Syahrir Abdussamad, ST., MT', 'Yasin Mohamad, ST., MT', 'LAB EL-4', '2022/2023 - GENAP'),
(49, 'EAD65662', 'JARINGAN TELEKOMUNIKASI', '6', 2, 'SABTU', '10.30 - 12.10', 'A', 'Bambang Panji Asmara, ST., MT', 'Zainudin Bonok, ST., MT', 'LAB EL-3', '2022/2023 - GENAP'),
(50, 'EAD66263', 'ARSITEKTUR DAN ORGANISASI KOMPUTER', '6', 3, 'SENIN', '13.00 - 15.30', 'A', 'Ikhsan Hidayat, S.Kom., MT', 'Bambang Panji Asmara, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(51, 'EAD66363', 'JARINGAN KOMPUTER', '6', 3, 'KAMIS', '08.00 -10.30', 'A', 'Bambang Panji Asmara, ST., MT', 'Ifan Wiranto, ST., MT', 'RK 3.16', '2022/2023 - GENAP'),
(52, 'EAD66463', 'SISTEM BASIS DATA (TEORI + PRAKTIKUM)', '6', 3, 'SELASA', '13.00 -15.30', 'A', 'Amirudin Yunus Dako, ST., M.Eng', 'Rahmat Deddy Rianto Dako, ST., M.Eng', 'RK 2.11', '2022/2023 - GENAP'),
(53, 'EAD66562', 'REKAYASA PERANGKAT LUNAK', '6', 2, 'RABU', '13.00 - 15.30', 'A', 'Rahmat Deddy Rianto Dako, ST., M.Eng', 'Amirudin Yunus Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(54, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'RABU', '10.30 - 12.10', 'A', 'Jumiati Ilham, ST., MT', 'Dr. Lanto Mohamad Kamil Amali, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(55, 'EAD66762', 'METODELOGI RISET', '6', 2, 'JUMAT', '13.00 - 14.40', 'A', 'Ir. Wahab Musa, MT., Ph.D', 'Dr. Ir Arifin Matoka, MT', 'LAB EL-3', '2022/2023 - GENAP'),
(56, 'EAD66862', 'PENGETAHUAN LINGKUNGAN', '6', 2, 'RABU', '08.00 - 09.40', 'A', 'Taufiq Ismail Yusuf, ST., M.Si', 'Amirudin Yunus Dako, ST., M.Eng', 'LAB EL-4', '2022/2023 - GENAP'),
(58, 'EAD67103', 'DINAMIKA DAN STABILITAS SISTEM TENAGA LISTRIK', '8', 3, 'KAMIS', '08.00 - 10.30', 'A', 'Dr. Ir Arifin Matoka, MT', 'Yasin Mohamad, ST., MT', 'LAB EL-4', '2022/2023 - GENAP'),
(59, 'EAD67503', 'STANDARISASI', '7', 3, 'KAMIS', '10.30 -13.00', 'A', 'Dr. Ir Arifin Matoka, MT', 'Jumiati Ilham, ST., MT', 'LAB EL-3', '2022/2023 - GENAP'),
(60, 'EAD67703', 'PEMOGRAMAN WEB', '7', 3, 'SABTU', '08.00 - 10.30', 'A', 'Amirudin Yunus Dako, ST., M.Eng', 'Rahmat Deddy Rianto Dako, ST., M.Eng', 'LAB EL-3', '2022/2023 - GENAP'),
(61, 'EAD68203', 'PENGENALAN POLA', '8', 3, 'SENIN', '08.00 - 10.30', 'A', 'Salmawaty Tansa, ST., M.Eng', 'Bambang Panji Asmara, ST., MT', 'RK 2.11', '2022/2023 - GENAP'),
(62, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'KAMIS', '16.20 - 18.00', 'B', 'Jumiati Ilham, ST, MT', 'Dr. Lanto Mohamad Kamil Amali, MT', 'LAB EL-4', '2022/2023 - GENAP'),
(63, 'EAD66762', 'METODELOGI RISET', '6', 2, 'SELASA', '16.20 - 18.00', 'B', 'Dr. Ir Arifin Matoka, MT', 'Ir. Wahab Musa, M.T, Ph.D', 'RK 3.16', '2022/2023 - GENAP'),
(64, 'EAD63764', 'TEKNIK TEGANGAN TINGGI (TEORI + PRAKTIKUM)', '6', 4, 'RABU', '13.00 - 17.10', 'B', 'Dr. Lanto Mohamad Kamil Amali, MT', 'Ade Irawaty Tolago, ST, MT', 'LAB. KOMPUTER 2', '2022/2023 - GENAP'),
(66, 'UNG5261962', 'KEMARITIMAN', '2', 6, 'SELASA', '10.30 - 12.10', 'A', 'Ervan Hasan Harun, ST.,MT', 'Ifan Wiranto, ST.,MT', 'RK 3.16', '2022/2023 - GENAP'),
(67, 'NAS5460222', 'PANCASILA', '2', 2, 'KAMIS', '08.00 - 09.40', 'B', 'Dr. Sastro M Wantu, SH., M.Si', 'Ramli Mahmud, S.Pd, M.A', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(68, 'EAD66862', 'PENGETAHUAN LINGKUNGAN', '6', 2, 'SENIN', '10.30 - 12.10', 'B', 'Taufiq Ismail Yusuf, ST, M.SI', 'Ade Irawaty Tolago, ST, MT', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(70, '	EAD62024', 'PENGUKURAN DAN INSTRUMENTASI (KULIAH + PRAKTIKUM)', '2', 4, 'JUMAT', '13.00-16.20', 'C', 'Yasin Mohamad, ST., MT', 'Iskandar Zulkarnain Nasibu, S.Pd., M.Eng', 'LAB. KOMPUTER 1', '2022/2023 - GENAP'),
(71, 'EAD65263', 'TEKNIK ANTENA', '6', 3, 'SENIN', '08:00 - 09:40', 'A', 'Zainudin Bonok,ST.,MT', 'Bambang Panji Asmara,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(72, 'EAD62844', 'ENERGI BARU TERBARUKAN*', '4', 4, 'SENIN', '08:00 - 11:20', 'B', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU.', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(73, 'EAD62643', 'MEDAN ELEKTROMAGNETIS', '4', 3, 'SENIN', '08:00 - 10.30', 'A', 'Ade Irawaty Tolago,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(74, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SENIN', '08:00 - 10.30', 'B', 'Ifan Wiranto,ST.,MT', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(75, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'SENIN', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2023/2024 - GENAP'),
(76, 'NAS60222', 'PANCASILA', '2', 2, 'SENIN', '10:30 - 12:10', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2023/2024 - GENAP'),
(77, 'EAD63044', 'TEKNIK KENDALI DASAR*', '4', 4, 'SENIN', '13:00 - 16:20', 'A', 'Syahrir Abdussamad,ST.,MT', 'Ifan Wiranto,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2023/2024 - GENAP'),
(78, 'UNG60642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'SENIN', '13:00 - 14:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2023/2024 - GENAP'),
(79, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ervan Hasan Harun,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(80, 'EAD65662', 'JARINGAN TELEKOMUNIKASI', '6', 2, 'SENIN', '13:00 - 14:40', 'A', 'Bambang Panji Asmara,ST.,MT', 'Zainudin Bonok,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(81, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR*', '4', 4, 'SENIN', '14:40 - 18:00', 'A', 'Yasin Mohamad,ST.,MT', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(82, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'SELASA', '08:00 - 09:40', 'A', 'Jumiati Ilham,ST.,MT', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(83, 'EAD63864', 'TEKNIK PROTEKSI*', '6', 4, 'SELASA', '08:00 - 11:20', 'A', 'Ade Irawaty Tolago,ST.,MT', 'Taufiq Ismail Yusuf,ST.,M.Si', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(84, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'SELASA', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Dr. Ir. Sardi Salim, M.Pd., IPU.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(85, 'EAD61923', 'ALJABAR LINIER', '2', 3, 'SELASA', '08:00 - 10.30', 'B', 'Ifan Wiranto,ST.,MT', 'Rahmat D.R. Dako, ST., M.Eng', 'RK 2.11', '2023/2024 - GENAP'),
(86, 'EAD66862', 'PENGETAHUAN LINGKUNGAN HIDUP', '6', 2, 'SELASA', '10:30 - 12:10', 'B', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ade Irawaty Tolago,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(87, 'EAD66263', 'ARSITEKTUR DAN ORGANISASI KOMPUTER', '6', 3, 'SELASA', '13:00 - 15:30', 'A', 'Bambang Panji Asmara,ST.,MT', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(88, 'EAD61923', 'ALJABAR LINIER', '2', 3, 'SELASA', '13:00 - 15:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Ervan Hasan Harun,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(89, 'EAD62844', 'ENERGI BARU TERBARUKAN*', '4', 4, 'SELASA', '13:00 - 16:20', 'A', 'Jumiati Ilham,ST.,MT', 'Yasin Mohamad,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(90, 'EAD61624', 'ELEKTRONIKA DASAR*', '2', 4, 'SELASA', '13:00 - 16:20', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Salmawaty Tansa,ST.,M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(91, 'EAD61923', 'ALJABAR LINIER', '2', 3, 'RABU', '08:00 - 10.30', 'C', 'Rahmat D.R. Dako, ST., M.Eng', 'Ifan Wiranto,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(92, 'EAD62744', 'METODE NUMERIS*', '4', 4, 'RABU', '08:00 - 11:20', 'C', 'Syahrir Abdussamad,ST.,MT', 'Ir. Wahab Musa,MT.,Ph.D', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(93, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '08:00 - 10.30', 'A', 'Jumiati Ilham,ST.,MT', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(95, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'RABU', '10:30 - 12:10', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2023/2024 - GENAP'),
(96, 'EAD66862', 'PENGETAHUAN LINGKUNGAN HIDUP', '6', 2, 'RABU', '10:30 - 12:10', 'A', 'Ade Irawaty Tolago,ST.,MT', 'Taufiq Ismail Yusuf,ST.,M.Si', 'RK 2.11', '2023/2024 - GENAP'),
(97, 'EAD65562', 'TRANSMISI TELEKOMUNIKASI', '6', 2, 'RABU', '13:00 - 14:40', 'A', 'Zainudin Bonok,ST.,MT', 'Syahrir Abdussamad,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(98, 'EAD63764', 'TEKNIK TEGANGAN TINGGI*', '6', 4, 'RABU', '13:00 - 16:20', 'A', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Ade Irawaty Tolago,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(99, 'EAD67703', 'PEMROGRAMAN WEB', '7', 3, 'RABU', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(100, 'EAD62643', 'MEDAN ELEKTROMAGNETIS', '4', 3, 'RABU', '13:00 - 15:30', 'B', 'Ifan Wiranto,ST.,MT', 'Ade Irawaty Tolago,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(101, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '13:00 - 15:30', 'B', 'Ervan Hasan Harun,ST.,MT', 'Jumiati Ilham,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(102, 'EAD68203', 'PENGENALAN POLA', '7', 3, 'KAMIS', '08:00 - 10.30', 'A', 'Salmawaty Tansa,ST.,M.Eng', 'Ir. Wahab Musa,MT.,Ph.D', 'RK 3.16', '2023/2024 - GENAP'),
(103, 'EAD63864', 'TEKNIK PROTEKSI*', '6', 4, 'KAMIS', '08:00 - 11:20', 'B', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ade Irawaty Tolago,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(104, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'KAMIS', '08:00 - 10.30', 'C', 'Jumiati Ilham,ST.,MT', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(105, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'KAMIS', '08:00 - 11:20', 'B', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(106, 'EAD63044', 'TEKNIK KENDALI DASAR*', '4', 4, 'KAMIS', '08:00 - 11:20', 'B', 'Ifan Wiranto,ST.,MT', 'Syahrir Abdussamad,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2023/2024 - GENAP'),
(107, 'NAS60222', 'PANCASILA', '2', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2023/2024 - GENAP'),
(108, 'EAD61624', 'ELEKTRONIKA DASAR*', '2', 4, 'KAMIS', '13:00 - 16:20', 'A', 'Salmawaty Tansa,ST.,M.Eng', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(109, 'EAD62744', 'METODE NUMERIS*', '4', 4, 'KAMIS', '13:00 - 15:30', 'B', 'Ir. Wahab Musa,MT.,Ph.D', 'Syahrir Abdussamad,ST.,MT', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(110, 'EAD67503', 'STANDARISASI', '7', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Yasin Mohamad,ST.,MT', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(111, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'KAMIS', '15:30 - 17:10', 'B', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Jumiati Ilham,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(112, 'EAD66463', 'SISTEM BASIS DATA*', '6', 3, 'JUMAT', '08:00 - 10.30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(113, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'JUMAT', '08:00 - 11:20', 'C', 'Yasin Mohamad,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(114, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR*', '4', 4, 'JUMAT', '08:00 - 11:20', 'B', 'Ervan Hasan Harun,ST.,MT', 'Yasin Mohamad,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(115, 'UNG64062', 'KEMARITIMAN', '6', 2, 'JUMAT', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2023/2024 - GENAP'),
(116, 'EAD65362', 'PERAMBATAN GELOMBANG ELEKTROMAGNETIK', '6', 2, 'JUMAT', '13:00 - 14:40', 'A', 'Zainudin Bonok,ST.,MT', 'Bambang Panji Asmara,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(118, 'EAD62744', 'METODE NUMERIS*', '4', 4, 'JUMAT', '13:00 - 16:20', 'A', 'Syahrir Abdussamad,ST.,MT', 'Ir. Wahab Musa,MT.,Ph.D', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(119, 'EAD67103', 'DINAMIKA DAN STABILITAS SISTEM TENAGA LISTRIK', '7', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Dr.Ir. Arifin Matoka,MT', 'Yasin Mohamad,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(120, 'EAD61624', 'ELEKTRONIKA DASAR*', '2', 4, 'JUMAT', '14:40 - 18:00', 'C', 'Salmawaty Tansa,ST.,M.Eng', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'RK 2.11', '2023/2024 - GENAP'),
(121, 'EAD66562', 'REKAYASA PERANGKAT LUNAK', '6', 2, 'SABTU', '08:00 - 10.30', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Amirudin Yunus Dako,ST.,M.Eng', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(122, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SABTU', '08:00 - 10.30', 'C', 'Ervan Hasan Harun,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(123, 'EAD63963', 'ANALISIS SISTEM TENAGA', '6', 3, 'SABTU', '08:00 - 10.30', 'A', 'Ervan Hasan Harun,ST.,MT', 'Jumiati Ilham,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(124, 'EAD65462', 'REKAYASA TRAFIK', '6', 2, 'SABTU', '08:00 - 09:40', 'A', 'Bambang Panji Asmara,ST.,MT', 'Zainudin Bonok,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(125, 'EAD66762', 'METODELOGI RISET', '6', 2, 'SABTU', '10:30 - 12:10', 'A', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Amirudin Yunus Dako,ST.,M.Eng', 'RK 3.16', '2023/2024 - GENAP'),
(126, 'EAD62844', 'ENERGI BARU TERBARUKAN*', '4', 4, 'SABTU', '13:00 - 16:20', 'C', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Yasin Mohamad,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2023/2024 - GENAP'),
(127, 'EAD66363', 'JARINGAN KOMPUTER*', '6', 3, 'SABTU', '13:00 - 15:30', 'A', 'Zainudin Bonok,ST.,MT', 'Bambang Panji Asmara,ST.,MT', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(128, 'UNG60642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'SABTU', '13:00 - 14:40', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(129, 'UNG60642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'SABTU', '13:00 - 14:40', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'LAB. TEKNIK SISTEM KENDALI', '2023/2024 - GENAP'),
(130, 'EAL61724', 'PEMOGRAMAN BERORIENTASI OBYEK*', '2', 4, 'SENIN', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(132, 'EAL61623', 'SISTEM BASIS DATA*', '2', 3, 'SELASA', '08:00 - 10.30', 'B', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(133, 'EAL61324', 'ELEKTRONIKA DASAR*', '2', 4, 'RABU', '08:00 - 11:20', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Ir. Wahab Musa,MT.,Ph.D', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(134, 'EAL61623', 'SISTEM BASIS DATA*', '2', 3, 'KAMIS', '08:00 - 10.30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 2', '2023/2024 - GENAP'),
(135, 'EAL61724', 'PEMOGRAMAN BERORIENTASI OBYEK*', '2', 4, 'KAMIS', '08:00 - 11:20', 'B', 'Rahmat D.R. Dako, ST., M.Eng', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. KOMPUTER 1', '2023/2024 - GENAP'),
(137, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'KAMIS', '13:00 - 14:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2023/2024 - GENAP'),
(138, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'KAMIS', '14:40 - 16:20', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2023/2024 - GENAP'),
(139, 'EAL60823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'JUMAT', '08:00 - 10.30', 'A', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(140, 'EAL60923', 'ALJABAR LINIER', '2', 3, 'JUMAT', '08:00 - 10.30', 'B', 'Salmawaty Tansa,ST.,M.Eng', 'Bambang Panji Asmara,ST.,MT', 'RK 3.16', '2023/2024 - GENAP'),
(141, 'NAS60222', 'PANCASILA', '2', 2, 'JUMAT', '10:30 - 12:10', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2023/2024 - GENAP'),
(142, 'NAS60222', 'PANCASILA', '2', 2, 'JUMAT', '13:00 - 14:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2023/2024 - GENAP'),
(143, 'EAL61324', 'ELEKTRONIKA DASAR*', '2', 4, 'SABTU', '09:40 - 13:00', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2023/2024 - GENAP'),
(144, 'EAL60823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SABTU', '13:00 - 15:30', 'B', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2023/2024 - GENAP'),
(145, 'EAL60923', 'ALJABAR LINIER', '2', 3, 'SABTU', '13:00 - 15:30', 'A', 'Bambang Panji Asmara,ST.,MT', 'Salmawaty Tansa,ST.,M.Eng', 'RK 3.16', '2023/2024 - GENAP'),
(300, 'EAL60614', 'FISIKA LISTRIK DAN MAGNET', '1', 4, 'SENIN', '08:00 - 11:20', 'A', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Salmawaty Tansa,ST.,M.Eng', 'RK 3.16', '2024/2025 - GANJIL'),
(301, 'EAL61214', 'PEMOGRAMAN DASAR', '1', 4, 'SENIN', '08:00 - 11:20', 'B', 'Amirudin Yunus Dako,ST.,M.Eng', 'Ulfatun Nadifa, S.Pd, M.Kom', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(302, 'EAL60513', 'MATEMATIKA TEKNIK', '1', 3, 'SENIN', '13:00 - 15:30', 'B', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ifan Wiranto,ST.,MT', 'RK 3.16', '2024/2025 - GANJIL'),
(303, 'EAL62134', 'KEAMANAN JARINGAN DAN KRIPTOGRAFI', '3', 4, 'SENIN', '13:00 - 16:20', 'A', 'Dr. Bambang Panji Asmara,ST.,MT', 'Syahrir Abdussamad,ST.,MT', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(304, 'EAL61513', 'TEKNOLOGI INFORMASI', '1', 3, 'SENIN', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(305, 'EAL61214', 'PEMOGRAMAN DASAR', '1', 4, 'SELASA', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Ulfatun Nadifa, S.Pd, M.Kom', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(306, 'EAL61513', 'TEKNOLOGI INFORMASI', '1', 3, 'SELASA', '13:00 - 15:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng', 'Amirudin Yunus Dako,ST.,M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(307, 'EAL60713', 'PROBABILITAS DAN STATISTIKA', '1', 3, 'RABU', '08:00 - 10:30', 'A', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'Ade Irawaty Tolago,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(308, 'EAL60713', 'PROBABILITAS DAN STATISTIKA', '1', 3, 'RABU', '08:00 - 10:30', 'B', 'Ervan Hasan Harun,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'RK 3.16', '2024/2025 - GANJIL'),
(309, 'NAS51160432', 'KEWARGANEGARAAN', '3', 2, 'RABU', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Dosen Luar DTPS', 'RK 3.16', '2024/2025 - GANJIL'),
(310, 'EAL61412', 'TATA TULIS LAPORAN DAN KARYA ILMIAH', '1', 2, 'RABU', '10:30 - 12:10', 'A', 'Yasin Mohamad,ST.,MT', 'Amirudin Yunus Dako,ST.,M.Eng', 'RK 2.11', '2024/2025 - GANJIL'),
(311, 'EAL62034', 'MICROPROSESOR', '3', 4, 'RABU', '13:00 - 16:20', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(312, 'EAL60513', 'MATEMATIKA TEKNIK', '1', 3, 'RABU', '13:00 - 15:30', 'A', 'Taufiq Ismail Yusuf,ST.,M.Si', 'Ervan Hasan Harun,ST.,MT', 'RK 3.16', '2024/2025 - GANJIL'),
(313, 'EAL61834', 'PEMOGRAMAN WEB', '3', 4, 'KAMIS', '08:00 - 11:20', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Amirudin Yunus Dako,ST.,M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(314, 'EAL61934', 'TEKNIK DIGITAL', '3', 4, 'KAMIS', '13:00 - 16:20', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Dr. Bambang Panji Asmara,ST.,MT', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(315, 'EAL61033', 'MATEMATIKA DISKRIT DAN LOGIKA', '3', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Syahrir Abdussamad,ST.,MT', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(316, 'EAL60614', 'FISIKA LISTRIK DAN MAGNET', '1', 4, 'JUMAT', '08:00 - 11:20', 'B', 'Yasin Mohamad,ST.,MT', 'Ade Irawaty Tolago,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GANJIL'),
(317, 'EAL61412', 'TATA TULIS LAPORAN DAN KARYA ILMIAH', '1', 2, 'JUMAT', '13:00 - 14:40', 'B', 'Amirudin Yunus Dako,ST.,M.Eng', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(318, 'NAS51160112', 'AGAMA', '1', 2, 'SABTU', '08:00 - 09:40', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Dosen Luar DTPS', 'RK 2.11', '2024/2025 - GANJIL'),
(319, 'NAS51160112', 'AGAMA', '1', 2, 'SABTU', '08:00 - 09:40', 'B', 'Amirudin Yunus Dako,ST.,M.Eng', 'Dosen Luar DTPS', 'RK 3.16', '2024/2025 - GANJIL'),
(320, 'EAL61513', 'TEKNOLOGI INFORMASI', '1', 3, 'SABTU', '09:40 - 12:10', 'C', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(321, 'EAL61214', 'PEMOGRAMAN DASAR', '1', 4, 'SABTU', '13:00 - 16:20', 'C', 'Rahmat D.R. Dako, ST., M.Eng', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(322, 'EAD68304', 'SKRIPSI', '7', 4, '', ' - ', 'A', '', '', '', '2024/2025 - GANJIL'),
(323, 'EAD65852', 'SISTEM OPERASI', '5', 2, 'SENIN', '08:00 - 09:40', 'A', 'Dr. Bambang Panji Asmara,ST.,MT', 'Ulfatun Nadifa, S.Pd, M.Kom', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(324, 'EAD67603', 'KOMUNIKASI DIGITAL', '7', 3, 'SENIN', '08:00 - 10:30', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Salmawaty Tansa,ST.,M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(325, 'EAD68403', 'KP', '7', 3, 'SENIN', '08:00 - 10:30', 'A', 'Yasin Mohamad,ST.,MT', 'Dosen Luar DTPS', 'LAB. EL 2', '2024/2025 - GANJIL'),
(326, 'EAD611733', 'RANGKAIAN LISTRIK', '3', 3, 'SENIN', '08:00 - 10:30', 'A', 'Yasin Mohamad,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(327, 'NAS5460112', 'AGAMA', '1', 2, 'SENIN', '08:00 - 09:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2024/2025 - GANJIL'),
(328, 'EAD611333', 'MATEMATIKA DISKRIT DAN LOGIKA', '3', 3, 'SENIN', '08:00 - 11:20', 'B', 'Syahrir Abdussamad,ST.,MT', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GANJIL'),
(329, 'NAS5460112', 'AGAMA', '1', 2, 'SENIN', '08:00 - 09:40', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'LAB. EL 3', '2024/2025 - GANJIL'),
(330, 'UNG5461404', 'KKS', '7', 4, 'SENIN', '08:00 - 11:20', 'A', 'Yasin Mohamad,ST.,MT', 'Dosen Luar DTPS', 'LAB. EL 1', '2024/2025 - GANJIL'),
(331, 'EAD61313', 'KIMIA ELEKTRO', '1', 3, 'SENIN', '09:40 - 12:10', 'A', 'Jumiati Ilham,ST.,MT', 'Ulfatun Nadifa, S.Pd, M.Kom', 'RK 2.11', '2024/2025 - GANJIL'),
(332, 'EAD66053', 'ANTAR MUKA DAN PERIFERAL (TEORI + PRAKTIKUM)', '5', 3, 'SENIN', '13:00 - 15:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Syahrir Abdussamad,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(333, 'EAD63154', 'TEKNIK INSTALASI (TEORI + PRAKTIKUM)', '5', 4, 'SENIN', '13:00 - 16:20', 'A', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Ir. Rahmad Hidayat Dongka, S.Pd.,M.Pd', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(334, 'EAD61313', 'KIMIA ELEKTRO', '1', 3, 'SENIN', '13:00 - 15:30', 'B', 'Ervan Hasan Harun,ST.,MT', 'Ulfatun Nadifa, S.Pd, M.Kom', 'RK 2.11', '2024/2025 - GANJIL'),
(335, 'EAD60913', 'MATEMATIKA TEKNIK', '1', 3, 'SENIN', '13:00 - 15:30', 'C', 'Salmawaty Tansa,ST.,M.Eng', 'Ifan Wiranto,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GANJIL'),
(336, 'EAD611333', 'MATEMATIKA DISKRIT DAN LOGIKA', '3', 3, 'SELASA', '08:00 - 11:20', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Syahrir Abdussamad,ST.,MT', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(337, 'EAD63154', 'TEKNIK INSTALASI (TEORI + PRAKTIKUM)', '5', 4, 'SELASA', '08:00 - 10:30', 'B', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Ade Irawaty Tolago,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(338, 'EAD61014', 'FISIKA LISTRIK (TEORI + PRAKTIKUM)', '1', 4, 'SELASA', '08:00 - 11:20', 'A', 'Yasin Mohamad,ST.,MT', 'Ade Irawaty Tolago,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(339, 'EAD61014', 'FISIKA LISTRIK (TEORI + PRAKTIKUM)', '1', 4, 'SELASA', '08:00 - 11:20', 'B', 'Ade Irawaty Tolago,ST.,MT', 'Yasin Mohamad,ST.,MT', 'RK 3.16', '2024/2025 - GANJIL'),
(340, 'EAD611634', 'TEKNIK TELEKOMUNIKASI DASAR (KULIAH + PRAKTIKUM)', '3', 4, 'SELASA', '08:00 - 11:20', 'B', 'Zainudin Bonok,ST.,MT', 'Dr. Bambang Panji Asmara,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(341, 'EAD61112', 'TATA TULIS LAPORAN DAN KARYA ILMIAH', '1', 2, 'SELASA', '13:00 - 14:40', 'B', 'Amirudin Yunus Dako,ST.,M.Eng', 'Ir. Wahab Musa,MT.,Ph.D', 'RK 2.11', '2024/2025 - GANJIL'),
(342, 'EAD611534', 'TEKNIK DIGITAL DAN MIKROPROSESOR (KULIAH + PRAKTIKUM)', '3', 4, 'SELASA', '13:00 - 16:20', 'B', 'Ir. Wahab Musa,MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(343, 'EAD63252', 'JARINGAN DAN KOMUNIKASI DATA', '5', 2, 'SELASA', '13:00 - 14:40', 'B', 'Dr. Bambang Panji Asmara,ST.,MT', 'Zainudin Bonok,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GANJIL'),
(344, 'EAD67503', 'STANDARISASI', '7', 3, 'SELASA', '13:00 - 15:30', 'A', 'Jumiati Ilham,ST.,MT', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(345, 'EAD611434', 'ISYARAT DAN SISTEM (KULIAH + PRAKTIKUM)', '3', 4, 'SELASA', '13:00 - 16:20', 'A', 'Ifan Wiranto,ST.,MT', 'Salmawaty Tansa,ST.,M.Eng', 'RK 3.16', '2024/2025 - GANJIL'),
(346, 'EAD64954', 'SISTEM TELEKOMUNIKASI LANJUT (TEORI + PRAKTIKUM)', '5', 4, 'RABU', '08:00 - 11:20', 'A', 'Zainudin Bonok,ST.,MT', 'Syahrir Abdussamad,ST.,MT', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GANJIL'),
(347, 'EAD63553', 'DISTRIBUSI TENAGA LISTRIK', '5', 3, 'RABU', '08:00 - 10:30', 'A', 'Yasin Mohamad,ST.,MT', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(348, 'EAD66153', 'MULTI MEDIA (TEORI + PRAKTIKUM)', '5', 3, 'RABU', '08:00 - 10:30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Ulfatun Nadifa, S.Pd, M.Kom', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(349, 'EAD611534', 'TEKNIK DIGITAL DAN MIKROPROSESOR (KULIAH + PRAKTIKUM)', '3', 4, 'RABU', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'Ir. Wahab Musa,MT.,Ph.D', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(350, 'EAD67703', 'PEMOGRAMAN WEB', '7', 3, 'RABU', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako,ST.,M.Eng', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(351, 'EAD611634', 'TEKNIK TELEKOMUNIKASI DASAR (KULIAH + PRAKTIKUM)', '3', 4, 'RABU', '13:00 - 16:20', 'A', 'Dr. Bambang Panji Asmara,ST.,MT', 'Zainudin Bonok,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(352, 'EAD61213', 'PROBABILITAS DAN STATISTIKA', '1', 3, 'RABU', '13:00 - 15:30', 'B', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'Ade Irawaty Tolago,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(353, 'EAD63654', 'MESIN LISTRIK (TEORI + PRAKTIKUM)', '5', 4, 'RABU', '13:00 - 16:20', 'A', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'Dr.Ir. Arifin Matoka,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(354, 'EAD64853', 'TEKNIK PENGOLAHAN ISYARAT DIGITAL', '5', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Salmawaty Tansa,ST.,M.Eng', 'Ir. Wahab Musa,MT.,Ph.D', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(355, 'EAD65753', 'ALGORITMA DAN STRUKTUR DATA', '5', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Dr. Bambang Panji Asmara,ST.,MT', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(356, 'EAD67173', 'DINAMIKA DAN STABILITAS SISTEM TENAGA LISTRIK', '7', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Dr.Ir. Arifin Matoka,MT', 'Yasin Mohamad,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(357, 'EAD61213', 'PROBABILITAS DAN STATISTIKA', '1', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Ade Irawaty Tolago,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'RK 2.11', '2024/2025 - GANJIL'),
(358, 'EAD60913', 'MATEMATIKA TEKNIK', '1', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Ifan Wiranto,ST.,MT', 'Ervan Hasan Harun,ST.,MT', 'RK 3.16', '2024/2025 - GANJIL'),
(359, 'UNG54620402', 'PENDIDIKAN KEWARGANEGARAAN', '3', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2024/2025 - GANJIL'),
(360, 'EAD61112', 'TATA TULIS LAPORAN DAN KARYA ILMIAH', '1', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Ade Irawaty Tolago,ST.,MT', 'Amirudin Yunus Dako,ST.,M.Eng', 'RK 2.11', '2024/2025 - GANJIL'),
(361, 'EAD60913', 'MATEMATIKA TEKNIK', '1', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Ervan Hasan Harun,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(362, 'EAD63252', 'JARINGAN DAN KOMUNIKASI DATA', '5', 2, 'KAMIS', '13:00 - 14:40', 'A', 'Zainudin Bonok,ST.,MT', 'Dr. Bambang Panji Asmara,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(363, 'EAD61514', 'PEMROGRAMAN DASAR (TEORI + PRAKTIKUM)', '1', 4, 'KAMIS', '13:00 - 16:20', 'B', 'Dosen Luar DTPS', 'Rahmat D.R. Dako, ST., M.Eng', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(364, 'EAD61112', 'TATA TULIS LAPORAN DAN KARYA ILMIAH', '1', 2, 'KAMIS', '13:00 - 14:40', 'C', 'Syahrir Abdussamad,ST.,MT', 'Amirudin Yunus Dako,ST.,M.Eng', 'RK 3.16', '2024/2025 - GANJIL'),
(365, 'UNG5261752', 'TECHNOPRENEURSHIP', '5', 2, 'KAMIS', '15:30 - 17:10', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 3.16', '2024/2025 - GANJIL'),
(366, 'UNG5261752', 'TECHNOPRENEURSHIP', '5', 2, 'KAMIS', '15:30 - 17:10', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'RK 2.11', '2024/2025 - GANJIL'),
(367, 'EAD65153', 'PENYAMBUNGAN DAN PENSINYALAN', '5', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Zainudin Bonok,ST.,MT', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(368, 'EAD63453', 'TRANSMISI DAYA LISTRIK', '5', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Ervan Hasan Harun,ST.,MT', 'Taufiq Ismail Yusuf,ST.,M.Si', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(369, 'EAD61213', 'PROBABILITAS DAN STATISTIKA', '1', 3, 'JUMAT', '08:00 - 10:30', 'C', 'Ifan Wiranto,ST.,MT', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'RK 2.11', '2024/2025 - GANJIL'),
(370, 'EAD611434', 'ISYARAT DAN SISTEM (KULIAH + PRAKTIKUM)', '3', 4, 'JUMAT', '08:00 - 11:20', 'B', 'Syahrir Abdussamad,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 3.16', '2024/2025 - GANJIL'),
(371, 'EAD67203', 'EKONOMI TEKNIK', '7', 2, 'JUMAT', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Ervan Hasan Harun,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(372, 'EAD65053', 'PERANCANGAN SISTEM DIGITAL (TEORI + PRAKTIKUM)', '5', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Ir. Wahab Musa,MT.,Ph.D', 'Salmawaty Tansa,ST.,M.Eng', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GANJIL'),
(373, 'EAD61514', 'PEMROGRAMAN DASAR (TEORI + PRAKTIKUM)', '1', 4, 'JUMAT', '13:00 - 16:20', 'A', 'Dosen Luar DTPS', 'Iskandar Z. Nasibu, S.Pd, M.Eng', 'LAB. KOMPUTER 2', '2024/2025 - GANJIL'),
(374, 'EAD63353', 'PEMBANGKIT TENAGA LISTRIK', '5', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Dr.Ir. Arifin Matoka,MT', 'Dr. Lanto M. Kamil Amali,ST.,MT', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GANJIL'),
(375, 'EAD65952', 'PEMROGRAMAN LANJUT', '5', 2, 'JUMAT', '13:00 - 14:40', 'A', 'Rahmat D.R. Dako, ST., M.Eng', 'Dosen Luar DTPS', 'LAB. KOMPUTER 1', '2024/2025 - GANJIL'),
(376, 'EAD611733', 'RANGKAIAN LISTRIK', '3', 3, 'JUMAT', '14:40 - 17:10', 'B', 'Dr. Ir. Sardi Salim, M.Pd., IPU., ASEAN Eng.', 'Yasin Mohamad,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(377, 'EAD61313', 'KIMIA ELEKTRO', '1', 3, 'SABTU', '09:40 - 12:10', 'C', 'Dr. Bambang Panji Asmara,ST.,MT', 'Ifan Wiranto,ST.,MT', 'RK 2.11', '2024/2025 - GANJIL'),
(378, 'UNG5261872', 'LITERASI DIGITAL', '7', 2, 'SABTU', '13:00 - 14:40', 'A', 'Dosen Luar DTPS', 'Amirudin Yunus Dako,ST.,M.Eng', 'RK 2.11', '2024/2025 - GANJIL'),
(379, 'EAL61144', 'METODE NUMERIS*', '4', 4, 'SENIN', '08:00 - 11:20', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(380, 'EAL61724', 'PEMOGRAMAN BERORIENTASI OBYEK*', '2', 4, 'SENIN', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(381, 'EAL62244', 'PEMOGRAMAN PERANGKAT BERGERAK*', '4', 4, 'SENIN', '13:00 - 16:20', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(382, 'EAL61623', 'SISTEM BASIS DATA*', '2', 3, 'SELASA', '08:00 - 10.30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(383, 'EAL62342', 'ANALISIS DAN DESAIN ALGORITMA', '4', 2, 'SELASA', '08:00 - 10.30', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(384, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'SELASA', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(385, 'EAL62443', 'OTOMATA', '4', 3, 'SELASA', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(386, 'NAS60322', 'BAHASA INDONESIA', '2', 2, 'SELASA', '15:30 - 17:10', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(387, 'EAL62544', 'TEKNIK KENDALI DASAR*', '4', 4, 'RABU', '08:00 - 11:20', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ifan Wiranto, ST., MT.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(388, 'EAL61324', 'ELEKTRONIKA DASAR*', '2', 4, 'RABU', '08:00 - 10.30', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(389, 'EAL62643', 'KOMPUTASI CERDAS', '4', 3, 'RABU', '13:00 - 16:20', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(390, 'EAL61724', 'PEMOGRAMAN BERORIENTASI OBYEK*', '2', 4, 'KAMIS', '08:00 - 11:20', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(391, 'EAL61623', 'SISTEM BASIS DATA*', '2', 3, 'KAMIS', '08:00 - 10.30', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(392, 'EAL61724', 'PEMOGRAMAN BERORIENTASI OBYEK*', '2', 4, 'KAMIS', '13:00 - 16:20', 'C', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(393, 'EAL60823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'JUMAT', '08:00 - 10.30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'RK 2.11', '2024/2025 - GENAP'),
(394, 'EAL61623', 'SISTEM BASIS DATA*', '2', 3, 'JUMAT', '08:00 - 10.30', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. EL 1', '2024/2025 - GENAP'),
(396, 'NAS60222', 'PANCASILA', '2', 2, 'JUMAT', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'LAB EL-4', '2024/2025 - GENAP'),
(397, 'NAS60222', 'PANCASILA', '2', 2, 'JUMAT', '10:30 - 12:10', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'RK 3.16', '2024/2025 - GENAP'),
(401, 'EAL60823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SABTU', '13:00 - 15:30', 'B', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ervan Hasan Harun, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(402, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SENIN', '08:00 - 10.30', 'B', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(403, 'EAD63044', 'TEKNIK KENDALI DASAR*', '4', 4, 'SENIN', '08:00 - 10.30', 'A', 'Ifan Wiranto, ST., MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(404, 'EAD65263', 'TEKNIK ANTENA', '6', 3, 'SENIN', '08:00 - 09:40', 'A', 'Zainudin Bonok, ST.,MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(405, 'EAD62643', 'MEDAN ELEKTROMAGNETIS', '4', 3, 'SENIN', '08:00 - 11:20', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Ifan Wiranto, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(406, 'EAD62844', 'ENERGI BARU TERBARUKAN*', '4', 4, 'SENIN', '08:00 - 10.30', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(407, 'EAD65462', 'REKAYASA TRAFIK', '6', 2, 'SENIN', '09.00 - 11:20', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(408, 'NAS60422', 'BAHASA INDONESIA', '2', 2, 'SENIN', '10:30 - 12:10', 'B', 'Dosen Luar DTPS', 'Amirudin Yunus Dako, ST., M.Eng.', 'RK 2.11', '2024/2025 - GENAP'),
(409, 'NAS60422', 'BAHASA INDONESIA', '2', 2, 'SENIN', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Amirudin Yunus Dako, ST., M.Eng.', 'RK 3.16', '2024/2025 - GENAP'),
(410, 'UNG61404', 'KKS', '7', 4, 'SENIN', '13:00 - 15:30', 'A', 'Yasin Mohamad, ST., MT.', 'Dosen Luar DTPS', 'LAB EL-4', '2024/2025 - GENAP'),
(411, 'EAD61823', 'MATEMATIKA TEKNIK LANJUT', '2', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Ifan Wiranto, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(412, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR*', '4', 4, 'SENIN', '13:00 - 15:30', 'A', 'Yasin Mohamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(413, 'UNG60642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'SENIN', '15:30 - 17:10', 'B', 'Dosen Luar DTPS', 'Ikhsan Hidayat, S.Kom., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(414, 'UNG60642', 'BAHASA INGGRIS TERAPAN', '4', 2, 'SENIN', '15:30 - 17:10', 'A', 'Dosen Luar DTPS', 'Ikhsan Hidayat, S.Kom., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(415, 'EAD63864', 'TEKNIK PROTEKSI*', '6', 4, 'SELASA', '08:00 - 10.30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(416, 'EAD65662', 'JARINGAN TELEKOMUNIKASI', '6', 2, 'SELASA', '08:00 - 09:40', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(417, 'EAL60923', 'ALJABAR LINIER', '2', 3, 'JUMAT', '08:00 - 10.30', 'B', 'Salmawaty Tansa, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'RK 3.16', '2024/2025 - GENAP'),
(418, 'EAD61923', 'ALJABAR LINIER', '2', 3, 'SELASA', '08:00 - 10.30', 'B', 'Ifan Wiranto, ST., MT.', 'Rahmat D.R. Dako, ST., M.Eng.', 'RK 2.11', '2024/2025 - GENAP'),
(419, 'EAD62744', 'METODE NUMERIS*', '4', 4, 'SELASA', '09:40 - 12:10', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'RK 3.16', '2024/2025 - GENAP'),
(420, 'EAD66862', 'PENGETAHUAN LINGKUNGAN HIDUP', '6', 2, 'SELASA', '10:30 - 12:10', 'B', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(421, 'EAD66263', 'ARSITEKTUR DAN ORGANISASI KOMPUTER', '6', 3, 'SELASA', '13:00 - 15:30', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(422, 'EAD61624', 'ELEKTRONIKA DASAR*', '2', 4, 'SELASA', '13:00 - 15:30', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'RK 2.11', '2024/2025 - GENAP'),
(423, 'EAL60923', 'ALJABAR LINIER', '2', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ifan Wiranto, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(424, 'EAD61923', 'ALJABAR LINIER', '2', 3, 'SELASA', '13:00 - 15:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(425, 'EAD62844', 'ENERGI BARU TERBARUKAN*', '4', 4, 'SELASA', '13:00 - 15:30', 'A', 'Jumiati Ilham, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(426, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '08:00 - 10.30', 'A', 'Jumiati Ilham, ST., MT.', 'Ikhsan Hidayat, S.Kom., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(427, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'RABU', '08:00 - 09:40', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Jumiati Ilham, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(428, 'EAD66862', 'PENGETAHUAN LINGKUNGAN HIDUP', '6', 2, 'RABU', '10:30 - 12:10', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'RK 2.11', '2024/2025 - GENAP'),
(429, 'EAD63764', 'TEKNIK TEGANGAN TINGGI*', '6', 4, 'RABU', '13:00 - 15:30', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(430, 'EAD61723', 'ILMU BAHAN LISTRIK', '2', 3, 'RABU', '13:00 - 15:30', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Jumiati Ilham, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(431, 'EAD67703', 'PEMROGRAMAN WEB', '7', 3, 'RABU', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 1', '2024/2025 - GENAP'),
(432, 'EAD65562', 'TRANSMISI TELEKOMUNIKASI', '6', 2, 'RABU', '13:00 - 14:40', 'A', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(433, 'EAD62643', 'MEDAN ELEKTROMAGNETIS', '4', 3, 'RABU', '13:00 - 15:30', 'B', 'Ifan Wiranto, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'RK 2.11', '2024/2025 - GENAP'),
(434, 'EAD67503', 'STANDARISASI', '7', 3, 'RABU', '15:30 - 17:10', 'A', 'Yasin Mohamad, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(435, 'EAD63764', 'TEKNIK TEGANGAN TINGGI*', '6', 4, 'KAMIS', '08:00 - 10.30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(436, 'EAL61324', 'ELEKTRONIKA DASAR*', '2', 4, 'SABTU', '09:00 - 11:20', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(437, 'EAD61624', 'ELEKTRONIKA DASAR*', '2', 4, 'KAMIS', '08:00 - 10.30', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'RK 3.16', '2024/2025 - GENAP'),
(438, 'EAD63044', 'TEKNIK KENDALI DASAR*', '4', 4, 'KAMIS', '08:00 - 10.30', 'B', 'Ifan Wiranto, ST., MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(439, 'EAD66762', 'METODELOGI RISET', '6', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'RK 3.16', '2024/2025 - GENAP'),
(440, 'NAS60122', 'PANCASILA', '2', 2, 'KAMIS', '10:30 - 12:10', 'B', 'Dosen Luar DTPS', 'Syahrir Abdussamad, ST., MT.', 'LAB. EL 2', '2024/2025 - GENAP'),
(441, 'NAS60122', 'PANCASILA', '2', 2, 'SENIN', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Syahrir Abdussamad, ST., MT.', 'LAB. EL 1', '2024/2025 - GENAP'),
(442, 'EAD63864', 'TEKNIK PROTEKSI*', '6', 4, 'KAMIS', '13:00 - 15:30', 'B', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(443, 'EAD62744', 'METODE NUMERIS*', '4', 4, 'KAMIS', '13:00 - 15:30', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Syahrir Abdussamad, ST., MT.', 'RK 3.16', '2024/2025 - GENAP');
INSERT INTO `temp_jadwal_seleksi` (`id`, `kode`, `nama`, `semester`, `sks`, `hari`, `jam`, `kelas`, `pengajar1`, `pengajar2`, `ruangan`, `status`) VALUES
(444, 'EAD68203', 'PENGENALAN POLA', '7', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(445, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'KAMIS', '13:00 - 15:30', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'RK 2.11', '2024/2025 - GENAP'),
(446, 'EAD66662', 'K3 DAN ETIKA PROFESI', '6', 2, 'KAMIS', '15:30 - 17:10', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(447, 'EAD66463', 'SISTEM BASIS DATA*', '6', 3, 'JUMAT', '08:00 - 10.30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(448, 'EAD65362', 'PERAMBATAN GELOMBANG ELEKTROMAGNETIK', '6', 2, 'JUMAT', '08:00 - 09:40', 'A', 'Zainudin Bonok, ST.,MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB. ELEKTRONIKA DAN TELEKOMUNIKASI', '2024/2025 - GENAP'),
(449, 'EAD62944', 'TEKNIK TENAGA LISTRIK DASAR*', '4', 4, 'JUMAT', '08:00 - 10.30', 'B', 'Ervan Hasan Harun, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(450, 'UNG64062', 'KEMARITIMAN', '6', 2, 'JUMAT', '10:30 - 12:10', 'A', 'Dosen Luar DTPS', 'Ade Irawaty Tolago, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(451, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'JUMAT', '13:00 - 15:30', 'A', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'RK 3.16', '2024/2025 - GENAP'),
(452, 'EAD67103', 'DINAMIKA DAN STABILITAS SISTEM TENAGA LISTRIK', '7', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Yasin Mohamad, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(453, 'EAD62024', 'PENGUKURAN DAN INSTRUMENTASI*', '2', 4, 'JUMAT', '13:00 - 15:30', 'C', 'Yasin Mohamad, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'LAB. TEKNIK TENAGA LISTRIK', '2024/2025 - GENAP'),
(454, 'EAD63044', 'TEKNIK KENDALI DASAR*', '4', 4, 'SABTU', '08:00 - 10.30', 'C', 'Syahrir Abdussamad, ST., MT.', 'Ifan Wiranto, ST., MT.', 'LAB. TEKNIK SISTEM KENDALI', '2024/2025 - GENAP'),
(455, 'EAD63963', 'ANALISIS SISTEM TENAGA', '6', 3, 'SABTU', '08:00 - 10:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Jumiati Ilham, ST., MT.', 'RK 2.11', '2024/2025 - GENAP'),
(456, 'EAD66562', 'REKAYASA PERANGKAT LUNAK', '6', 2, 'SABTU', '08:00 - 09:40', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(457, 'EAD66762', 'METODELOGI RISET', '6', 2, 'SABTU', '10:30 - 12:10', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'RK 3.16', '2024/2025 - GENAP'),
(458, 'EAD66363', 'JARINGAN KOMPUTER*', '6', 3, 'SABTU', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB. KOMPUTER 2', '2024/2025 - GENAP'),
(459, 'EAD60113', 'KALKULUS 1', '1', 3, 'SENIN', '09:40 - 12:10', 'A', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'R.K2.11', '2025/2026 - GANJIL'),
(460, 'EAD60313', 'FISIKA 1', '1', 3, 'SENIN', '14:40 - 17:10', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Wildan, S.Pd. M.Pd', 'R.K3.16', '2025/2026 - GANJIL'),
(461, 'NAS66712', 'Agama', '1', 2, 'SENIN', '08:00 - 09:40', 'A', 'Yasin Mohamad, ST., MT.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Ganjil'),
(462, 'EAL60113', 'Kalkulus 1', '1', 3, 'RABU', '09:40 - 12:10', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2025/2026 - Ganjil'),
(463, 'EAL60113', 'Kalkulus 1', '1', 3, 'SENIN', '09:40 - 12:10', 'B', 'Salmawaty Tansa, ST., M.Eng.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2025/2026 - Ganjil'),
(464, 'EAL60213', 'Pengantar Statistik dan Probabilitas', '1', 3, 'SELASA', '09:40 - 12:10', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ifan Wiranto, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(465, 'EAL60313', 'Fisika 1', '1', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Yasin Mohamad, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(466, 'EAL60313', 'Fisika 1', '1', 3, 'SELASA', '14:40 - 17:10', 'B', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(467, 'EAL60313', 'Fisika 1', '1', 3, 'RABU', '14:40 - 17:10', 'C', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K3.16', '2025/2026 - Ganjil'),
(468, 'EAL60313', 'Fisika 1', '1', 3, 'SENIN', '14:40 - 17:10', 'D', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K2.11', '2025/2026 - Ganjil'),
(469, 'EAL60633', 'Persamaan Diferensial', '3', 3, 'SABTU', '13:00 - 15:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Ifan Wiranto, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(470, 'EAL60633', 'Persamaan Diferensial', '3', 3, 'RABU', '08:00 - 10:30', 'B', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(471, 'EAL60733', 'Fisika 2', '3', 3, 'AHAD', '13:00 - 15:30', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Ade Irawaty Tolago, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(472, 'EAL60733', 'Fisika 2', '3', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(473, 'EAL61313', 'Teknologi Informasi', '1', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(474, 'EAL61313', 'Teknologi Informasi', '1', 3, 'RABU', '08:00 - 10.30', 'B', 'Zainudin Bonok, ST.,MT.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(475, 'EAL61413', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SENIN', '08:00 - 10:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(476, 'EAL61413', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SELASA', '08:00 - 10.30', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(477, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SENIN', '10:30 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(478, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SELASA', '10:30 - 11:21', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(479, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SELASA', '10:30 - 11:20', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(480, 'EAL61633', 'Sirkuit & Elektronika', '3', 3, 'SELASA', '08:00 - 10:30', 'A', 'Zainudin Bonok, ST.,MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(481, 'EAL61633', 'Sirkuit & Elektronika', '3', 3, 'AHAD', '08:00 - 10:30', 'B', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(482, 'EAL61731', 'Prak. Sirkuit & Elektronika', '3', 1, '', '08:00 - 08:00', 'A', 'Zainudin Bonok, ST.,MT.', 'Ir. Wahab Musa, MT.,Ph.D', '', '2025/2026 - Ganjil'),
(483, 'EAL61731', 'Prak. Sirkuit & Elektronika', '3', 1, '', '08:00 - 08:00', 'B', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Ganjil'),
(484, 'EAL62233', 'Desain Digital & Logika', '3', 3, 'AHAD', '08:00 - 10:30', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(485, 'EAL62233', 'Desain Digital & Logika', '3', 3, 'RABU', '13:00 - 15:30', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(486, 'EAL62331', 'Prak. Desain Digital & Logika', '3', 1, '', '08:00 - 08:00', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', '', '2025/2026 - Ganjil'),
(487, 'EAL62331', 'Prak. Desain Digital & Logika', '3', 1, 'RABU', '15:30 - 16:20', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(488, 'EAL62433', 'Pemrograman Client/Server', '3', 3, 'AHAD', '13:00 - 15:30', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(489, 'EAL62433', 'Pemrograman Client/Server', '3', 3, 'JUMAT', '08:00 - 10:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(490, 'EAL62531', 'Prak. Pemrograman Client/Server', '3', 1, '', '08:00 - 08:00', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2025/2026 - Ganjil'),
(491, 'EAL62531', 'Prak. Pemrograman Client/Server', '3', 1, '', '08:00 - 08:00', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2025/2026 - Ganjil'),
(492, 'EAL63251', 'Prak. Sistem Tertanam & Mikroprosesor', '5', 1, '', '08:00 - 08:00', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', '', '2025/2026 - Ganjil'),
(493, 'EAL63251', 'Prak. Sistem Tertanam & Mikroprosesor', '5', 1, '', '08:00 - 08:00', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Ganjil'),
(494, 'EAL63353', 'Multimedia', '5', 3, 'AHAD', '13:00 - 15:30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(495, 'EAL63451', 'Prak. Multimedia', '5', 1, '', '08:00 - 08:00', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', '', '2025/2026 - Ganjil'),
(496, 'EAL63553', 'Jaringan Komputasi', '5', 3, 'SABTU', '08:00 - 10:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(497, 'EAL63553', 'Jaringan Komputasi', '5', 3, 'AHAD', '13:00 - 15:30', 'B', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(498, 'EAL63653', 'Sistem Operasi', '5', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(499, 'EAL63653', 'Sistem Operasi', '5', 3, 'UNDEFINED', '08:00 - 10:30', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(500, 'EAL63753', 'Sinyal & Sistem', '5', 3, 'UNDEFINED', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(501, 'EAL63753', 'Sinyal & Sistem', '5', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(502, 'EAL63852', 'Desain Rekayasa Komputasi 1', '5', 2, 'SABTU', '10:30 - 12:10', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(503, 'EAL63852', 'Desain Rekayasa Komputasi 1', '5', 2, 'AHAD', '15:30 - 17:10', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(504, 'EAL67653', 'Sistem Tertanam & Mikroprosesor', '5', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(505, 'EAL67653', 'Sistem Tertanam & Mikroprosesor', '5', 3, 'JUMAT', '13:00 - 15:30', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(506, 'NAS65132', 'Kewarganegaraan', '3', 2, 'RABU', '15:30 - 17:10', 'A', 'Dosen Luar', 'Dosen Luar', 'R.K2.11', '2025/2026 - Ganjil'),
(507, 'NAS65132', 'Kewarganegaraan', '3', 2, 'AHAD', '10:30 - 12:10', 'B', 'Ir. Wahab Musa, MT.,Ph.D', 'Ir. Wahab Musa, MT.,Ph.D', 'R.K2.11', '2025/2026 - Ganjil'),
(508, 'NAS65212', 'Agama', '1', 2, 'SELASA', '13:00 - 14:40', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Ganjil'),
(509, 'NAS65312', 'Bahasa Indonesia', '1', 2, 'SELASA', '08:00 - 09:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(510, 'EAD60113', 'Kalkulus 1', '1', 3, 'SABTU', '08:00 - 10:30', 'A', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Ganjil'),
(511, 'EAD60313', 'Fisika 1', '1', 3, 'KAMIS', '08:00 - 10.30', 'A', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(512, 'EAD60313', 'Fisika 1', '1', 3, 'JUMAT', '09:40 - 12:10', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K2.11', '2025/2026 - Ganjil'),
(513, 'EAD60613', 'Kimia Elektro', '1', 3, 'SABTU', '08:00 - 10.30', 'A', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(514, 'EAD60613', 'Kimia Elektro', '1', 3, 'SELASA', '14:40 - 17:10', 'B', 'Wildan, S.Pd. M.Pd', 'Ervan Hasan Harun, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(515, 'EAD60613', 'Kimia Elektro', '1', 3, 'RABU', '09:40 - 12:10', 'C', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(516, 'EAD60713', 'Probabilitas dan Statistika', '1', 3, 'SELASA', '09:40 - 12:10', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(517, 'EAD60713', 'Probabilitas dan Statistika', '1', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K2.11', '2025/2026 - Ganjil'),
(518, 'EAD60833', 'Matematika Diskrit', '3', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - Ganjil'),
(519, 'EAD60833', 'Matematika Diskrit', '3', 3, 'AHAD', '08:00 - 10:30', 'B', 'Hendy Prasetyo, S.S.T., M.T.', 'Syahrir Abdussamad, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(520, 'EAD64153', 'Mesin Listrik', '5', 3, 'SENIN', '13:00 - 15:30', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(521, 'EAD61033', 'Variabel Kompleks', '3', 3, 'SABTU', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'R.K2.11', '2025/2026 - Ganjil'),
(522, 'EAD61033', 'Variabel Kompleks', '3', 3, 'UNDEFINED', '13:00 - 15:30', 'B', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Ervan Hasan Harun, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(523, 'EAD61113', 'Pemrograman Dasar', '1', 3, 'SELASA', '13:00 - 15:30', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(524, 'EAD61113', 'Pemrograman Dasar', '1', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(525, 'EAD61113', 'Pemrograman Dasar', '1', 3, 'SENIN', '08:00 - 10:30', 'C', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(526, 'EAD61211', 'Prak. Pemrograman Dasar', '1', 1, 'SELASA', '15:30 - 16:20', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(527, 'EAD61211', 'Prak. Pemrograman Dasar', '1', 1, 'KAMIS', '10:30 - 11:21', 'B', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(528, 'EAD61211', 'Prak. Pemrograman Dasar', '1', 1, 'SENIN', '10:30 - 11:21', 'C', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(529, 'EAD61432', 'Rangkaian Listrik 2', '3', 2, 'SENIN', '15:30 - 17:10', 'A', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(530, 'EAD61432', 'Rangkaian Listrik 2', '3', 2, 'SENIN', '09:40 - 11:20', 'B', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(531, 'EAD61531', 'Prak. Rangkaian Listrik', '3', 1, '', '08:00 - 08:00', 'A', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', '', '2025/2026 - Ganjil'),
(532, 'EAD61531', 'Prak. Rangkaian Listrik', '3', 1, '', '08:00 - 08:00', 'B', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', '', '2025/2026 - Ganjil'),
(533, 'EAD61632', 'Elektronika Dasar', '3', 2, 'UNDEFINED', '15:30 - 17:10', 'A', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'R.K3.16', '2025/2026 - Ganjil'),
(534, 'EAD61731', 'Prak. Elektronika Dasar', '3', 1, '', '08:00 - 08:00', 'A', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Ganjil'),
(535, 'EAD61932', 'Sinyal dan Sistem', '3', 2, 'AHAD', '15:30 - 17:10', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(536, 'EAD61932', 'Sinyal dan Sistem', '3', 2, 'UNDEFINED', '10:30 - 12:10', 'B', 'Ifan Wiranto, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K3.16', '2025/2026 - Ganjil'),
(537, 'EAD62031', 'Prak. Sinyal dan Sistem', '3', 1, '', '08:00 - 08:00', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', '', '2025/2026 - Ganjil'),
(538, 'EAD62031', 'Prak. Sinyal dan Sistem', '3', 1, '', '08:00 - 08:00', 'B', 'Ifan Wiranto, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', '', '2025/2026 - Ganjil'),
(539, 'EAD63133', 'Metode Numerik', '3', 3, 'SENIN', '08:00 - 10:30', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.TEKNIKKENDALI', '2025/2026 - Ganjil'),
(540, 'EAD63133', 'Metode Numerik', '3', 3, 'UNDEFINED', '08:00 - 10:30', 'B', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.TEKNIKKENDALI', '2025/2026 - Ganjil'),
(541, 'EAD63231', 'Prak. Metode Numerik', '3', 1, '', '08:00 - 08:00', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', '', '2025/2026 - Ganjil'),
(542, 'EAD63231', 'Prak. Metode Numerik', '3', 1, 'SENIN', '10:30 - 11:20', 'B', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(543, 'EAD63352', 'Teknik Instalasi', '5', 2, 'SENIN', '08:00 - 09:40', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(544, 'EAD63352', 'Teknik Instalasi', '5', 2, 'SELASA', '10:30 - 12:10', 'B', 'Wildan, S.Pd. M.Pd', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(545, 'EAD63451', 'Prak. Teknik Instalasi', '5', 1, '', '08:00 - 08:00', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', '', '2025/2026 - Ganjil'),
(546, 'EAD63451', 'Prak. Teknik Instalasi', '5', 1, '', '08:00 - 08:00', 'B', 'Wildan, S.Pd. M.Pd', 'Dr. Lanto M. Kamil Amali, ST., MT.', '', '2025/2026 - Ganjil'),
(547, 'EAD63653', 'Pembangkit Tenaga Listrik', '5', 3, 'SELASA', '13:00 - 15:30', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(548, 'EAD63753', 'Transmisi Tenaga Listrik', '5', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(549, 'EAD63853', 'Distribusi Tenaga Listrik', '5', 3, 'UNDEFINED', '08:00 - 10:30', 'A', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(550, 'EAD63952', 'Energi Baru Terbarukan', '5', 2, 'AHAD', '10:30 - 12:10', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(551, 'EAD64051', 'Prak. Energi Baru Terbarukan', '5', 1, '', '08:00 - 08:00', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', '', '2025/2026 - Ganjil'),
(552, 'EAD64251', 'Prak. Mesin Listrik', '5', 1, '', '08:00 - 08:00', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', '', '2025/2026 - Ganjil'),
(553, 'EAD65553', 'Piranti Optoelektronika', '5', 3, 'UNDEFINED', '13:00 - 15:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Syahrir Abdussamad, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(554, 'EAD65752', 'Antena dan Propagasi', '5', 2, 'SELASA', '10:30 - 12:10', 'A', 'Zainudin Bonok, ST.,MT.', 'Ifan Wiranto, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(555, 'EAD65853', 'Mikroprosesor dan Mikrokontroler', '5', 3, 'UNDEFINED', '08:00 - 10:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', 'R.K2.11', '2025/2026 - Ganjil'),
(556, 'EAD65951', 'Prak. Mikroprosesor dan Mikrokontroler', '5', 1, '', '08:00 - 08:00', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', '', '2025/2026 - Ganjil'),
(557, 'EAD66052', 'Jaringan Telekomunikasi', '5', 2, 'SENIN', '10:30 - 12:10', 'A', 'Zainudin Bonok, ST.,MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(558, 'EAD66152', 'Rekayasa Trafik', '5', 2, 'SENIN', '08:00 - 09:40', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(559, 'EAD66372', 'Metodologi Riset', '7', 2, 'SELASA', '15:30 - 17:10', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(560, 'EAD66473', 'Kerja Praktek', '7', 3, '', '08:00 - 08:00', 'A', 'Ifan Wiranto, ST., MT.', 'Dosen Luar', '', '2025/2026 - Ganjil'),
(561, 'EAD67572', 'K3 dan Etika Profesi', '7', 2, 'SENIN', '15:30 - 17:10', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(562, 'EAD67803', 'Internet of Things', '0', 3, 'UNDEFINED', '13:00 - 15:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(563, 'EAD68003', 'Pemrograman Web', '0', 3, 'UNDEFINED', '08:00 - 10:30', 'A', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(564, 'EAD68103', 'Standarisasi', '0', 3, 'UNDEFINED', '08:00 - 10:30', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Jumiati Ilham, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(565, 'EAD68303', 'Robotika', '0', 3, 'UNDEFINED', '13:00 - 15:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.UNPROTECT', '2025/2026 - Ganjil'),
(566, 'EAD68953', 'Pengolahan Sinyal Digital', '5', 3, 'SENIN', '13:00 - 15:30', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ir. Wahab Musa, MT.,Ph.D', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Ganjil'),
(567, 'EAD69203', 'Aplikasi Komputer dalam  Sistem Tenaga Listrik', '0', 3, 'SELASA', '08:00 - 10:30', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Ganjil'),
(568, 'NAS66712', 'Agama', '1', 2, 'SENIN', '13:00 - 14:40', 'B', 'Yasin Mohamad, ST., MT.', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(569, 'NAS66832', 'Pancasila', '3', 2, 'UNDEFINED', '15:30 - 17:10', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Ir. Wahab Musa, MT.,Ph.D', 'R.K2.11', '2025/2026 - Ganjil'),
(570, 'NAS66912', 'Bahasa Indonesia', '1', 2, 'RABU', '08:00 - 09:40', 'A', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(571, 'EAL60213', 'Pengantar Statistik dan Probabilitas', '1', 3, 'JUMAT', '13:00 - 15:30', 'B', 'Ervan Hasan Harun, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K2.11', '2025/2026 - Ganjil'),
(572, 'NAS65312', 'Bahasa Indonesia', '1', 2, 'SENIN', '13:00 - 14:40', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Ganjil'),
(573, 'NAS65212', 'Agama', '1', 2, 'RABU', '17:10 - 18:50', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Ganjil'),
(574, 'NAS65312', 'Bahasa Indonesia', '1', 2, 'SENIN', '08:00 - 09:40', 'C', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(575, 'EAL61313', 'Teknologi Informasi', '1', 3, 'SENIN', '13:00 - 15:30', 'C', 'Zainudin Bonok, ST.,MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(576, 'EAL61413', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SELASA', '08:00 - 10:30', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(577, 'NAS65212', 'Agama', '1', 2, 'RABU', '13:00 - 14:40', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(578, 'EAL60213', 'Pengantar Statistik dan Probabilitas', '1', 3, 'KAMIS', '10:30 - 13:00', 'C', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Ade Irawaty Tolago, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(579, 'EAL60113', 'Kalkulus 1', '1', 3, 'KAMIS', '13:00 - 15:30', 'C', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - Ganjil'),
(580, 'EAL61313', 'Teknologi Informasi', '1', 3, 'SELASA', '13:00 - 15:30', 'D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(581, 'EAL61413', 'Dasar-Dasar Pemrograman 1', '1', 3, 'RABU', '13:00 - 15:30', 'D', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(582, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'RABU', '15:30 - 16:20', 'D', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(583, 'EAL60113', 'Kalkulus 1', '1', 3, 'JUMAT', '08:00 - 10:30', 'D', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Syahrir Abdussamad, ST., MT.', 'R.K3.16', '2025/2026 - Ganjil'),
(584, 'EAL60213', 'Pengantar Statistik dan Probabilitas', '1', 3, 'SABTU', '08:00 - 10:30', 'D', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'R.K3.16', '2025/2026 - Ganjil'),
(585, 'EAL61313', 'Teknologi Informasi', '1', 3, 'RABU', '10:30 - 13:00', 'E', 'Dr. Bambang Panji Asmara, ST., MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(586, 'EAL61413', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SABTU', '08:00 - 10:30', 'E', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(587, 'EAL61511', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '11:20 - 12:10', 'E', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'LAB.KOMPUTER1', '2025/2026 - Ganjil'),
(588, 'NAS66912', 'Bahasa Indonesia', '1', 2, 'SELASA', '13:00 - 14:40', 'B', 'Dosen Luar DTPS', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - Ganjil'),
(589, 'EAD60113', 'Kalkulus 1', '1', 3, 'KAMIS', '15:30 - 18:00', 'B', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K2.11', '2025/2026 - Ganjil'),
(590, 'EAD60113', 'Kalkulus 1', '1', 3, 'RABU', '14:40 - 17:10', 'C', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ervan Hasan Harun, ST., MT.', 'R.K2.11', '2025/2026 - Ganjil'),
(591, 'EAD60613', 'Kimia Elektro', '1', 3, 'KAMIS', '13:00 - 15:30', 'D', 'Jumiati Ilham, ST., MT.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'LAB.TEKNIKKENDALI', '2025/2026 - Ganjil'),
(592, 'EAD60713', 'Probabilitas dan Statistika', '1', 3, 'JUMAT', '13:00 - 15:30', 'C', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'R.K3.16', '2025/2026 - Ganjil'),
(593, 'EAD60313', 'Fisika 1', '1', 3, 'UNDEFINED', '08:00 - 10:30', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Wildan, S.Pd. M.Pd', 'LAB.TEKNIKKENDALI', '2025/2026 - Ganjil'),
(594, 'EAD61211', 'Prak. Pemrograman Dasar', '1', 1, 'KAMIS', '15:30 - 16:20', 'D', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(595, 'EAD61113', 'Pemrograman Dasar', '1', 3, 'KAMIS', '13:00 - 15:30', 'D', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, \r\n S.Pd, M.Kom.', 'LAB.KOMPUTER2', '2025/2026 - Ganjil'),
(596, 'EAL63951', 'Prak. Desain Rekayasa Komputasi 1', '5', 1, '', '08:00 - 08:00', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', '', '2025/2026 - Ganjil'),
(597, 'EAL63951', 'Prak. Desain Rekayasa Komputasi 1', '5', 1, '', '08:00 - 08:00', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', '', '2025/2026 - Ganjil'),
(598, 'UNG66574', 'KKS', '7', 4, '', '08:00 - 08:00', 'A', 'Yasin Mohamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2025/2026 - Ganjil'),
(599, 'EAD60223', 'Kalkulus 2', '2', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K3.16', '2025/2026 - Genap'),
(600, 'EAD60223', 'Kalkulus 2', '2', 3, 'RABU', '08:00 - 10:30', 'B', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(601, 'EAD60223', 'Kalkulus 2', '2', 3, 'RABU', '13:00 - 15:30', 'C', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K3.16', '2025/2026 - Genap'),
(602, 'EAD60223', 'Kalkulus 2', '2', 3, 'KAMIS', '08:00 - 10:30', 'D', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(603, 'EAD60423', 'Fisika 2', '2', 3, 'RABU', '13:00 - 15:30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(604, 'EAD60423', 'Fisika 2', '2', 3, 'SELASA', '08:00 - 10:30', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(605, 'EAD60423', 'Fisika 2', '2', 3, 'RABU', '08:00 - 10:30', 'C', 'Ade Irawaty Tolago, ST., MT.', 'Yasin Mohamad, ST., MT.', 'R.K3.16', '2025/2026 - Genap'),
(606, 'EAD60423', 'Fisika 2', '2', 3, 'SENIN', '13:00 - 15:30', 'D', 'Ade Irawaty Tolago, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K2.11', '2025/2026 - Genap'),
(607, 'EAD60523', 'Persamaan Diferensial', '2', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2025/2026 - Genap'),
(608, 'EAD60523', 'Persamaan Diferensial', '2', 3, 'RABU', '13:00 - 15:30', 'B', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(609, 'EAD60523', 'Persamaan Diferensial', '2', 3, 'SELASA', '13:00 - 15:30', 'C', 'Ifan Wiranto, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K2.11', '2025/2026 - Genap'),
(610, 'EAD60523', 'Persamaan Diferensial', '2', 3, 'RABU', '08:00 - 10:30', 'D', 'Ifan Wiranto, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'R.K2.11', '2025/2026 - Genap'),
(611, 'EAD60923', 'Aljabar Linier', '2', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K2.11', '2025/2026 - Genap'),
(612, 'EAD60923', 'Aljabar Linier', '2', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Ifan Wiranto, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'R.K2.11', '2025/2026 - Genap'),
(613, 'EAD60923', 'Aljabar Linier', '2', 3, 'JUMAT', '08:00 - 10:30', 'C', 'Ifan Wiranto, ST., MT.', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - Genap'),
(614, 'EAD60923', 'Aljabar Linier', '2', 3, 'SELASA', '08:00 - 10:30', 'D', 'Ifan Wiranto, ST., MT.', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K2.11', '2025/2026 - Genap'),
(615, 'EAD61322', 'Rangkaian Listrik 1', '2', 2, 'RABU', '08:00 - 09:40', 'A', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(616, 'EAD61322', 'Rangkaian Listrik 1', '2', 2, 'SENIN', '09:40 - 11:20', 'B', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(617, 'EAD61322', 'Rangkaian Listrik 1', '2', 2, 'SELASA', '08:00 - 09:40', 'C', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(618, 'EAD61322', 'Rangkaian Listrik 1', '2', 2, 'SENIN', '15:30 - 17:10', 'D', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(619, 'EAD61822', 'Ilmu Bahan Listrik', '2', 2, 'SENIN', '08:00 - 09:40', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(620, 'EAD61822', 'Ilmu Bahan Listrik', '2', 2, 'RABU', '10:30 - 12:10', 'B', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(621, 'EAD61822', 'Ilmu Bahan Listrik', '2', 2, 'SENIN', '10:30 - 12:10', 'C', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'R.K2.11', '2025/2026 - Genap'),
(622, 'EAD61822', 'Ilmu Bahan Listrik', '2', 2, 'KAMIS', '10:30 - 12:10', 'D', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(623, 'EAD62822', 'Pengukuran dan Instrumentasi', '2', 2, 'SELASA', '10:30 - 12:10', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(624, 'EAD62822', 'Pengukuran dan Instrumentasi', '2', 2, 'SELASA', '15:30 - 17:10', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(625, 'EAD62822', 'Pengukuran dan Instrumentasi', '2', 2, 'RABU', '10:30 - 12:10', 'C', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'R.K3.16', '2025/2026 - Genap'),
(626, 'EAD62822', 'Pengukuran dan Instrumentasi', '2', 2, 'KAMIS', '15:30 - 17:10', 'D', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(627, 'EAD67422', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'RABU', '15:30 - 17:10', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'R.K3.16', '2025/2026 - Genap'),
(628, 'EAD67422', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'SENIN', '13:00 - 14:40', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(629, 'EAD67422', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'SENIN', '08:00 - 09:40', 'C', 'Ade Irawaty Tolago, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(630, 'EAD67422', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'RABU', '10:30 - 12:10', 'D', 'Ade Irawaty Tolago, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(631, 'EAL65522', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'SENIN', '09:40 - 11:20', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ir. Wahab Musa, MT.,Ph.D', 'R.K3.16', '2025/2026 - Genap'),
(632, 'EAL65522', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'SELASA', '10:30 - 12:10', 'B', 'Syahrir Abdussamad, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(633, 'EAL65522', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'SENIN', '15:30 - 17:10', 'C', 'Syahrir Abdussamad, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'R.K3.16', '2025/2026 - Genap'),
(634, 'EAD62142', 'Teknik Kendali Dasar', '4', 2, 'SENIN', '10:30 - 12:10', 'A', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(635, 'EAD62142', 'Teknik Kendali Dasar', '4', 2, 'SELASA', '10:30 - 12:10', 'B', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(636, 'EAD62241', 'Prak. Teknik  Kendali Dasar', '4', 1, '', '08:00 - 08:00', 'A', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', '', '2025/2026 - Genap'),
(637, 'EAD62343', 'Elektromagnetika', '4', 3, 'RABU', '08:00 - 10:30', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(638, 'EAD62343', 'Elektromagnetika', '4', 3, 'RABU', '13:00 - 15:30', 'B', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(639, 'EAD62443', 'Teknik Digital', '4', 3, 'SELASA', '13:00 - 15:30', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'R.K3.16', '2025/2026 - Genap'),
(640, 'EAD62443', 'Teknik Digital', '4', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(641, 'EAD62541', 'Prak. Teknik Digital', '4', 1, '', '08:00 - 08:00', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', '', '2025/2026 - Genap'),
(642, 'EAD62642', 'Teknik Telekomunikasi Dasar', '4', 2, 'SELASA', '15:30 - 17:10', 'A', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(643, 'EAD62642', 'Teknik Telekomunikasi Dasar', '4', 2, 'SENIN', '08:00 - 09:40', 'B', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(644, 'EAD62741', 'Prak. Teknik Telekomunikasi Dasar', '4', 1, '', '08:00 - 08:00', 'A', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', '', '2025/2026 - Genap'),
(645, 'EAD62943', 'Teknik Tenaga Listrik Dasar', '4', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(646, 'EAD62943', 'Teknik Tenaga Listrik Dasar', '4', 3, 'SELASA', '13:00 - 15:30', 'B', 'Ervan Hasan Harun, ST., MT.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(647, 'EAD63041', 'Prak. Teknik Tenaga Listrik Dasar', '4', 1, '', '08:00 - 08:00', 'A', 'Yasin Mohamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', '', '2025/2026 - Genap'),
(648, 'EAD63542', 'Jaringan dan Komunikasi Data', '4', 2, 'SELASA', '10:30 - 12:10', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(649, 'EAD63542', 'Jaringan dan Komunikasi Data', '4', 2, 'SENIN', '15:30 - 17:10', 'B', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(650, 'NAS67042', 'Kewarganegaraan', '4', 2, 'SENIN', '15:30 - 17:10', 'A', 'Dosen Luar', 'Dosen Luar', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(651, 'NAS67042', 'Kewarganegaraan', '4', 2, 'RABU', '10:30 - 12:10', 'B', 'Dosen Luar', 'Dosen Luar', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(652, 'EAD64363', 'Teknik Tegangan Tinggi', '6', 3, 'SENIN', '08:00 - 10:30', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2025/2026 - Genap'),
(653, 'EAD64461', 'Prak. Teknik Tegangan Tinggi', '6', 1, '', '08:00 - 08:00', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', '', '2025/2026 - Genap'),
(654, 'EAD64563', 'Teknik Proteksi', '6', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(655, 'EAD64763', 'Analisis Sistem Tenaga', '6', 3, 'SABTU', '08:00 - 10:30', 'A', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(656, 'EAD64862', 'Elektronika Daya', '6', 2, 'SENIN', '10:30 - 12:10', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(657, 'EAD64961', 'Prak. Elektronika Daya', '6', 1, '', '08:00 - 08:00', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Wildan, S.Pd. M.Pd', '', '2025/2026 - Genap'),
(658, 'EAD65163', 'Elektronika Industri', '6', 3, 'SELASA', '13:00 - 15:30', 'A', 'Wildan, S.Pd. M.Pd', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(659, 'EAD65261', 'Prak.Elektronika Industri', '6', 1, '', '08:00 - 08:00', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Wildan, S.Pd. M.Pd', '', '2025/2026 - Genap'),
(660, 'EAD65363', 'Perancangan Sistem Digital', '6', 3, 'RABU', '08:00 - 10:30', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(661, 'EAD65461', 'Prak. Perancangan Sistem Digital', '6', 1, '', '08:00 - 08:00', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Genap'),
(662, 'EAD65663', 'Jaringan Komunikasi Nirkabel', '6', 3, 'SABTU', '13:00 - 15:30', 'A', 'Zainudin Bonok, ST.,MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(663, 'EAD67662', 'Ekonomi Teknik', '6', 2, 'SELASA', '15:30 - 17:10', 'A', 'Ervan Hasan Harun, ST., MT.', 'Prof. Dr. Ir. Eduart Wolok, ST., MT., IPU., ASEAN.', 'R.K2.11', '2025/2026 - Genap'),
(664, 'EAD69063', 'Elektronika Telekomunikasi', '6', 3, 'SELASA', '08:00 - 10:30', 'A', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2025/2026 - Genap'),
(665, 'UNG67162', 'Kemaritiman', '6', 2, 'SELASA', '10:30 - 12:10', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Dosen Luar', 'R.K2.11', '2025/2026 - Genap'),
(666, 'UNG67362', 'Technopreneurship', '6', 2, 'RABU', '10:30 - 12:10', 'A', 'Idham Halid Lahay, ST., M.Sc.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Genap'),
(667, 'EAD610003', 'TOEFL Preparation', '0', 3, 'SELASA', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(668, 'EAD66684', 'Skripsi', '8', 4, '', '08:00 - 08:00', 'A', 'Yasin Mohamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2025/2026 - Genap'),
(669, 'EAL60423', 'Kalkulus 2', '2', 3, 'SELASA', '08:00 - 10:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2025/2026 - Genap'),
(670, 'EAL60423', 'Kalkulus 2', '2', 3, 'SENIN', '13:00 - 15:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(671, 'EAL60423', 'Kalkulus 2', '2', 3, 'KAMIS', '08:00 - 10:30', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(672, 'EAL60423', 'Kalkulus 2', '2', 3, 'SENIN', '08:00 - 10:30', 'D', 'Rahmat D.R. Dako, ST., M.Eng.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(673, 'EAL60522', 'Kimia', '2', 2, 'SENIN', '15:30 - 17:10', 'A', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'R.K2.11', '2025/2026 - Genap'),
(674, 'EAL60522', 'Kimia', '2', 2, 'RABU', '15:30 - 17:10', 'B', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(675, 'EAL60522', 'Kimia', '2', 2, 'KAMIS', '10:30 - 12:10', 'C', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(676, 'EAL60522', 'Kimia', '2', 2, 'SELASA', '15:30 - 17:10', 'D', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(677, 'EAL60821', 'Prak. Komputasi Aljabar Linear', '2', 1, '', '08:00 - 08:00', 'A', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', '', '2025/2026 - Genap'),
(678, 'EAL60821', 'Prak. Komputasi Aljabar Linear', '2', 1, '', '08:00 - 08:00', 'B', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', '', '2025/2026 - Genap'),
(679, 'EAL60821', 'Prak. Komputasi Aljabar Linear', '2', 1, '', '08:00 - 08:00', 'C', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', '', '2025/2026 - Genap'),
(680, 'EAL60821', 'Prak. Komputasi Aljabar Linear', '2', 1, '', '08:00 - 08:00', 'D', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', '', '2025/2026 - Genap'),
(681, 'EAL61823', 'Dasar-Dasar Pemrograman 2', '2', 3, 'RABU', '09:40 - 12:10', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(682, 'EAL61823', 'Dasar-Dasar Pemrograman 2', '2', 3, 'SELASA', '13:00 - 15:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(683, 'EAL61823', 'Dasar-Dasar Pemrograman 2', '2', 3, 'KAMIS', '13:00 - 15:30', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'R.K3.16', '2025/2026 - Genap'),
(684, 'EAL61823', 'Dasar-Dasar Pemrograman 2', '2', 3, 'RABU', '13:00 - 15:30', 'D', 'Rahmat D.R. Dako, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(685, 'EAL61921', 'Prak. Dasar-Dasar Pemrograman 2', '2', 1, 'SABTU', '08:00 - 08:50', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(686, 'EAL61921', 'Prak. Dasar-Dasar Pemrograman 2', '2', 1, '', '08:00 - 08:00', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', '', '2025/2026 - Genap'),
(687, 'EAL61921', 'Prak. Dasar-Dasar Pemrograman 2', '2', 1, '', '08:00 - 08:00', 'C', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Genap'),
(688, 'EAL61921', 'Prak. Dasar-Dasar Pemrograman 2', '2', 1, '', '08:00 - 08:00', 'D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', '', '2025/2026 - Genap'),
(689, 'EAL62022', 'Desain dan pemrograman Basisdata', '2', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(690, 'EAL62022', 'Desain dan pemrograman Basisdata', '2', 2, 'SENIN', '08:00 - 09:40', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - Genap'),
(691, 'EAL62022', 'Desain dan pemrograman Basisdata', '2', 2, 'SELASA', '10:30 - 12:10', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(692, 'EAL62022', 'Desain dan pemrograman Basisdata', '2', 2, 'SENIN', '10:30 - 12:10', 'D', 'Amirudin Yunus Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(693, 'EAL62121', 'Prak. Desain dan pemrograman Basisdata', '2', 1, '', '08:00 - 08:00', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Rahmat D.R. Dako, ST., M.Eng.', '', '2025/2026 - Genap'),
(694, 'EAL62121', 'Prak. Desain dan pemrograman Basisdata', '2', 1, '', '08:00 - 08:00', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2025/2026 - Genap'),
(695, 'EAL62121', 'Prak. Desain dan pemrograman Basisdata', '2', 1, '', '08:00 - 08:00', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', '', '2025/2026 - Genap'),
(696, 'EAL62121', 'Prak. Desain dan pemrograman Basisdata', '2', 1, '', '08:00 - 08:00', 'D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', '', '2025/2026 - Genap'),
(697, 'EAL67423', 'Komputasi Aljabar Linear', '2', 3, 'SELASA', '13:00 - 15:30', 'A', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(698, 'EAL67423', 'Komputasi Aljabar Linear', '2', 3, 'JUMAT', '08:00 - 10:30', 'B', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(699, 'EAL67423', 'Komputasi Aljabar Linear', '2', 3, 'JUMAT', '13:00 - 15:30', 'C', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(700, 'EAL67423', 'Komputasi Aljabar Linear', '2', 3, 'KAMIS', '13:00 - 15:30', 'D', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(701, 'NAS65422', 'Pancasila', '2', 2, 'KAMIS', '15:30 - 17:10', 'A', 'Dosen Luar', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.UNPROTECT', '2025/2026 - Genap'),
(702, 'NAS65422', 'Pancasila', '2', 2, 'KAMIS', '10:30 - 12:10', 'B', 'Dosen Luar', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K3.16', '2025/2026 - Genap'),
(703, 'NAS65422', 'Pancasila', '2', 2, 'JUMAT', '15:30 - 17:10', 'C', 'Ir. Wahab Musa, MT.,Ph.D', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K2.11', '2025/2026 - Genap'),
(704, 'NAS65422', 'Pancasila', '2', 2, 'SELASA', '10:30 - 12:10', 'D', 'Dosen Luar', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K3.16', '2025/2026 - Genap'),
(705, 'EAL61043', 'Pengantar Stuktur Diskrit', '4', 3, 'RABU', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'R.K2.11', '2025/2026 - Genap'),
(706, 'EAL61043', 'Pengantar Stuktur Diskrit', '4', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'R.K3.16', '2025/2026 - Genap'),
(707, 'EAL61142', 'Statistik Teknik', '4', 2, 'SELASA', '08:00 - 09:40', 'A', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(708, 'EAL61142', 'Statistik Teknik', '4', 2, 'SENIN', '10:30 - 12:10', 'B', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(709, 'EAL62743', 'Struktur Data & Algoritma', '4', 3, 'SENIN', '08:00 - 10:30', 'A', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(710, 'EAL62743', 'Struktur Data & Algoritma', '4', 3, 'RABU', '08:00 - 10:30', 'B', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(711, 'EAL62841', 'Prak. Struktur Data & Algoritma', '4', 1, '', '08:00 - 08:00', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', '', '2025/2026 - Genap'),
(712, 'EAL62841', 'Prak. Struktur Data & Algoritma', '4', 1, '', '08:00 - 08:00', 'B', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', '', '2025/2026 - Genap'),
(713, 'EAL62943', 'Desain Sistem Digital', '4', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(714, 'EAL62943', 'Desain Sistem Digital', '4', 3, 'SENIN', '13:00 - 15:30', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(715, 'EAL63041', 'Prak. Desain Sistem Digital', '4', 1, '', '08:00 - 08:00', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', '', '2025/2026 - Genap');
INSERT INTO `temp_jadwal_seleksi` (`id`, `kode`, `nama`, `semester`, `sks`, `hari`, `jam`, `kelas`, `pengajar1`, `pengajar2`, `ruangan`, `status`) VALUES
(716, 'EAL67543', 'Pengantar Arsitektur & organisasi Komputer', '4', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(717, 'EAL67543', 'Pengantar Arsitektur & organisasi Komputer', '4', 3, 'SELASA', '08:00 - 10:30', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(718, 'UNG64842', 'Technopreneurship', '4', 2, 'KAMIS', '15:30 - 17:10', 'A', 'Idham Halid Lahay, ST., M.Sc.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Genap'),
(719, 'UNG64842', 'Technopreneurship', '4', 2, 'JUMAT', '08:00 - 09:40', 'B', 'Idham Halid Lahay, ST., M.Sc.', 'Dosen Luar DTPS', 'R.K2.11', '2025/2026 - Genap'),
(720, 'UNG64942', 'Literasi Digital', '4', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Dosen Luar', 'Amirudin Yunus Dako, ST., M.Eng.', 'R.K2.11', '2025/2026 - Genap'),
(721, 'UNG64942', 'Literasi Digital', '4', 2, 'SELASA', '15:30 - 17:10', 'B', 'Dosen Luar', 'Amirudin Yunus Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - Genap'),
(722, 'EAL64063', 'Aplikasi bergerak', '6', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(723, 'EAL64163', 'Keamanan Komputer dan Jaringan', '6', 3, 'RABU', '13:00 - 15:30', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(724, 'EAL64262', 'Desain Rekayasa Komputasi 2', '6', 2, 'SENIN', '15:30 - 17:10', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(725, 'EAL64361', 'Prak. Desain Rekayasa Komputasi 2', '6', 1, '', '08:00 - 08:00', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', '', '2025/2026 - Genap'),
(726, 'EAL64463', 'Sensor dan aktuator', '6', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(727, 'EAL64563', 'Robotika', '6', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(728, 'EAL64663', 'Internet of Things', '6', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(729, 'UNG65062', 'Kemaritiman', '6', 2, 'RABU', '15:30 - 17:10', 'A', 'Dosen Luar', 'Yasin Mohamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2025/2026 - Genap'),
(730, 'EAD64661', 'Prak. Teknik Proteksi', '6', 1, '', '08:00 - 08:00', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', '', '2025/2026 - Genap'),
(731, 'EAD67803', 'Internet of Things', '0', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER2', '2025/2026 - Genap'),
(732, 'EAD69803', 'Forensik Digital', '0', 3, 'SELASA', '08:00 - 10:30', 'A', 'Syahrir Abdussamad, ST., MT.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(733, 'EAD69903', 'Komputasi Cerdas', '0', 3, 'SENIN', '08:00 - 10:30', 'A', 'Ir. Wahab Musa, MT.,Ph.D', 'Ulfatun Nadifa, \n S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(734, 'EAD68003', 'Pemrograman Web', '0', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(735, 'EAD68103', 'Standarisasi', '0', 3, 'SENIN', '13:00 - 15:30', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Jumiati Ilham, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(736, 'EAL65522', 'Tata Tulis Laporan dan Karya Ilmiah', '2', 2, 'RABU', '15:30 - 17:10', 'D', 'Syahrir Abdussamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'R.K2.11', '2025/2026 - Genap'),
(737, 'EAD610204', 'Perancangan Teknik Elektro', '0', 4, 'RABU', '13:00 - 16:20', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2025/2026 - Genap'),
(738, 'EAD68303', 'Robotika', '0', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER1', '2025/2026 - Genap'),
(739, 'UNG64842', 'TECHNOPRENEURSHIP', '4', 2, 'SABTU', '10:30 - 12:10', 'C', 'Idham Halid Lahay, ST., M.Sc.', 'Dosen Luar DTPS', 'R.K3.16', '2025/2026 - GENAP'),
(740, 'UNG64942', 'LITERASI DIGITAL', '4', 2, 'SABTU', '13:00 - 14:40', 'C', 'Dosen Luar DTPS', 'Amirudin Yunus Dako, ST., M.Eng.', 'R.K3.16', '2025/2026 - GENAP'),
(742, 'EAD600913', 'Kalkulus 1', '1', 3, 'SELASA', '13:00 - 15:30', 'A', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ervan Hasan Harun, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(743, 'EAD601113', 'Fisika 1', '1', 3, 'RABU', '13:00 - 15:30', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(744, 'EAL601013', 'Kalkulus 1', '1', 3, 'SABTU', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2026/2027 - Ganjil'),
(745, 'EAL601013', 'Kalkulus 1', '1', 3, 'SENIN', '08:00 - 10:30', 'B', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(746, 'EAL601313', 'Pengantar Statistik dan Probabilitas', '1', 3, 'KAMIS', '14:40 - 17:10', 'A', 'Ervan Hasan Harun, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(747, 'EAL600913', 'Fisika 1', '1', 3, 'SENIN', '08:00 - 10:30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K2.11', '2026/2027 - Ganjil'),
(748, 'EAL600913', 'Fisika 1', '1', 3, 'SENIN', '15:30 - 18:00', 'B', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(749, 'EAL600913', 'Fisika 1', '1', 3, 'JUMAT', '08:00 - 10:30', 'C', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(750, 'EAL600913', 'Fisika 1', '1', 3, 'SELASA', '08:00 - 10:30', 'D', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ade Irawaty Tolago, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(751, 'EAL602433', 'Persamaan Diferensial', '3', 3, 'KAMIS', '15:30 - 18:00', 'A', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(752, 'EAL602433', 'Persamaan Diferensial', '3', 3, 'JUMAT', '13:00 - 15:30', 'B', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(753, 'EAL602933', 'Fisika 2', '3', 3, 'SELASA', '15:30 - 18:00', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Yasin Mohamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(754, 'EAL602933', 'Fisika 2', '3', 3, 'RABU', '15:30 - 18:00', 'B', 'Yasin Mohamad, ST., MT.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(755, 'EAL601413', 'Teknologi Informasi', '1', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'Zainudin Bonok, ST.,MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(756, 'EAL601413', 'Teknologi Informasi', '1', 3, 'SELASA', '15:30 - 18:00', 'B', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'Zainudin Bonok, ST.,MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(757, 'EAL601113', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SELASA', '08:00 - 10:30', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(758, 'EAL601113', 'Dasar-Dasar Pemrograman 1', '1', 3, 'RABU', '08:00 - 10:30', 'B', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(759, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '11:20 - 12:10', 'A', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(760, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '09:40 - 10:30', 'B', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(761, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '10:30 - 11:20', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(762, 'EAL602533', 'Sirkuit & Elektronika', '3', 3, 'RABU', '13:00 - 15:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Wildan, S.Pd. M.Pd', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(763, 'EAL602533', 'Sirkuit & Elektronika', '3', 3, 'SELASA', '14:40 - 17:10', 'B', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(764, 'EAL602631', 'Prak. Sirkuit & Elektronika', '3', 1, 'SABTU', '13:00 - 14:40', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Wildan, S.Pd. M.Pd', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(765, 'EAL602631', 'Prak. Sirkuit & Elektronika', '3', 1, 'SABTU', '14:40 - 15:30', 'B', 'Wildan, S.Pd. M.Pd', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(766, 'EAL602732', 'Desain Digital & Logika', '3', 2, 'RABU', '15:30 - 17:10', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(767, 'EAL602732', 'Desain Digital & Logika', '3', 2, 'SENIN', '08:00 - 09:40', 'B', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(768, 'EAL602831', 'Prak. Desain Digital & Logika', '3', 1, 'SABTU', '14:40 - 15:30', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(769, 'EAL602831', 'Prak. Desain Digital & Logika', '3', 1, 'SABTU', '13:50 - 14:40', 'B', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(770, 'EAL603033', 'Pemrograman Client/Server', '3', 3, 'KAMIS', '13:00 - 15:30', 'A', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(771, 'EAL603033', 'Pemrograman Client/Server', '3', 3, 'RABU', '08:00 - 10:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(772, 'EAL603131', 'Prak. Pemrograman Client/Server', '3', 1, 'SABTU', '13:50 - 14:40', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(773, 'EAL603131', 'Prak. Pemrograman Client/Server', '3', 1, 'SABTU', '13:00 - 13:50', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(774, 'EAL604051', 'Prak. Sistem Tertanam & Mikroprosesor', '5', 1, 'JUMAT', '09:40 - 10:30', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(775, 'EAL604051', 'Prak. Sistem Tertanam & Mikroprosesor', '5', 1, 'JUMAT', '09:40 - 10:30', 'B', 'Syahrir Abdussamad, ST., MT.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(776, 'EAL604152', 'Multimedia', '5', 2, 'SELASA', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(777, 'EAL604251', 'Prak. Multimedia', '5', 1, 'JUMAT', '08:50 - 09:40', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(778, 'EAL604353', 'Jaringan Komputasi', '5', 3, 'SELASA', '15:30 - 18:00', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(779, 'EAL604353', 'Jaringan Komputasi', '5', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Dr. Bambang Panji Asmara, ST., MT.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(780, 'EAL604452', 'Sistem Operasi', '5', 2, 'RABU', '10:30 - 12:10', 'A', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(781, 'EAL604452', 'Sistem Operasi', '5', 2, 'RABU', '08:00 - 09:40', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(782, 'EAL604553', 'Sinyal & Sistem', '5', 3, 'SABTU', '09:40 - 12:10', 'A', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'R.K3.16', '2026/2027 - Ganjil'),
(783, 'EAL604553', 'Sinyal & Sistem', '5', 3, 'RABU', '13:00 - 15:30', 'B', 'Ifan Wiranto, ST., MT.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(784, 'EAL604652', 'Desain Rekayasa Komputasi 1', '5', 2, 'SENIN', '15:30 - 17:10', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(785, 'EAL604652', 'Desain Rekayasa Komputasi 1', '5', 2, 'SENIN', '10:30 - 12:10', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(786, 'EAL603953', 'Sistem Tertanam & Mikroprosesor', '5', 3, 'KAMIS', '09:40 - 12:10', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(787, 'EAL603953', 'Sistem Tertanam & Mikroprosesor', '5', 3, 'SELASA', '08:00 - 10:30', 'B', 'Syahrir Abdussamad, ST., MT.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(788, 'NAS0511600412', 'Bahasa Indonesia', '1', 2, 'SABTU', '08:00 - 09:40', 'A', 'Dosen Luar', 'Dosen Luar', 'R.K2.11', '2026/2027 - Ganjil'),
(789, 'EAD601113', 'Fisika 1', '1', 3, 'RABU', '08:00 - 10:30', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K3.16', '2026/2027 - Ganjil'),
(790, 'EAD601113', 'Fisika 1', '1', 3, 'SABTU', '15:30 - 18:00', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K3.16', '2026/2027 - Ganjil'),
(791, 'EAD601413', 'Kimia ', '1', 3, 'SENIN', '13:00 - 15:30', 'A', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Jumiati Ilham, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(792, 'EAD601413', 'Kimia ', '1', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Jumiati Ilham, ST., MT.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'R.K3.16', '2026/2027 - Ganjil'),
(793, 'EAD601413', 'Kimia ', '1', 3, 'SENIN', '15:30 - 18:00', 'C', 'Jumiati Ilham, ST., MT.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'R.K3.16', '2026/2027 - Ganjil'),
(794, 'EAD601513', 'Probabilitas dan Statistika', '1', 3, 'SELASA', '15:30 - 18:00', 'A', 'Ade Irawaty Tolago, ST., MT.', 'Nurul, S.Pd., M.Pd', 'R.K3.16', '2026/2027 - Ganjil'),
(795, 'EAD601513', 'Probabilitas dan Statistika', '1', 3, 'JUMAT', '13:00 - 15:30', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Nurul, S.Pd., M.Pd', 'R.K3.16', '2026/2027 - Ganjil'),
(796, 'EAD601633', 'Matematika Diskrit', '3', 3, 'KAMIS', '08:00 - 10:30', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K2.11', '2026/2027 - Ganjil'),
(797, 'EAD601633', 'Matematika Diskrit', '3', 3, 'SELASA', '08:00 - 10:30', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'R.K3.16', '2026/2027 - Ganjil'),
(798, 'EAD604952', 'Mesin Listrik', '5', 2, 'KAMIS', '14:40 - 16:20', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(799, 'EAD601833', 'Variabel Kompleks', '3', 3, 'SELASA', '13:00 - 15:30', 'A', 'Ifan Wiranto, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'R.K2.11', '2026/2027 - Ganjil'),
(800, 'EAD601833', 'Variabel Kompleks', '3', 3, 'KAMIS', '13:00 - 15:30', 'B', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Ifan Wiranto, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(801, 'EAD601913', 'Pemrograman Dasar', '1', 3, 'RABU', '13:00 - 15:30', 'A', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(802, 'EAD601913', 'Pemrograman Dasar', '1', 3, 'SELASA', '13:00 - 15:30', 'B', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(803, 'EAD601913', 'Pemrograman Dasar', '1', 3, 'SENIN', '08:00 - 10:30', 'C', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(804, 'EAD602011', 'Prak. Pemrograman Dasar', '1', 1, 'SABTU', '08:00 - 08:50', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(805, 'EAD602011', 'Prak. Pemrograman Dasar', '1', 1, 'SABTU', '08:50 - 08:50', 'B', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(806, 'EAD602011', 'Prak. Pemrograman Dasar', '1', 1, 'SABTU', '09:40 - 10:30', 'C', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(807, 'EAD602232', 'Rangkaian Listrik 2', '3', 2, 'SENIN', '08:00 - 09:40', 'A', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(808, 'EAD602232', 'Rangkaian Listrik 2', '3', 2, 'SENIN', '10:30 - 12:10', 'B', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'R.K2.11', '2026/2027 - Ganjil'),
(809, 'EAD602331', 'Prak. Rangkaian Listrik', '3', 1, 'SABTU', '13:00 - 13:50', 'A', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(810, 'EAD602331', 'Prak. Rangkaian Listrik', '3', 1, 'SABTU', '13:50 - 14:40', 'B', 'Yasin Mohamad, ST., MT.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(811, 'EAD602432', 'Elektronika Dasar', '3', 2, 'SELASA', '10:30 - 12:10', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(812, 'EAD602531', 'Prak. Elektronika Dasar', '3', 1, 'JUMAT', '13:00 - 13:50', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(813, 'EAD602732', 'Sinyal dan Sistem', '3', 2, 'JUMAT', '15:30 - 17:10', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(814, 'EAD602732', 'Sinyal dan Sistem', '3', 2, 'RABU', '10:30 - 12:10', 'B', 'Ifan Wiranto, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(815, 'EAD602831', 'Prak. Sinyal dan Sistem', '3', 1, 'KAMIS', '10:30 - 11:20', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Ifan Wiranto, ST., MT.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(816, 'EAD602831', 'Prak. Sinyal dan Sistem', '3', 1, 'KAMIS', '11:20 - 12:10', 'B', 'Ifan Wiranto, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(817, 'EAD603933', 'Metode Numerik', '3', 3, 'RABU', '08:00 - 10:30', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(818, 'EAD603933', 'Metode Numerik', '3', 3, 'SENIN', '15:30 - 18:00', 'B', 'Syahrir Abdussamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(819, 'EAD604031', 'Prak. Metode Numerik', '3', 1, 'KAMIS', '15:30 - 16:20', 'A', 'Syahrir Abdussamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(820, 'EAD604031', 'Prak. Metode Numerik', '3', 1, 'KAMIS', '16:20 - 17:10', 'B', 'Syahrir Abdussamad, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(821, 'EAD604152', 'Teknik Instalasi', '5', 2, 'SENIN', '09:40 - 11:20', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(822, 'EAD604152', 'Teknik Instalasi', '5', 2, 'SELASA', '10:30 - 12:10', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(823, 'EAD604251', 'Prak. Teknik Instalasi', '5', 1, 'JUMAT', '08:00 - 08:50', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(824, 'EAD604251', 'Prak. Teknik Instalasi', '5', 1, 'JUMAT', '08:50 - 09:40', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Wildan, S.Pd. M.Pd', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(825, 'EAD604453', 'Pembangkit Tenaga Listrik', '5', 3, 'SELASA', '13:00 - 15:30', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(826, 'EAD604552', 'Transmisi Tenaga Listrik', '5', 2, 'SABTU', '08:00 - 09:40', 'A', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(827, 'EAD604653', 'Distribusi Tenaga Listrik', '5', 3, 'SELASA', '08:00 - 10:30', 'A', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(828, 'EAD604752', 'Energi Baru Terbarukan', '5', 2, 'RABU', '15:30 - 17:10', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(829, 'EAD604851', 'Prak. Energi Baru Terbarukan', '5', 1, 'JUMAT', '15:30 - 16:20', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(830, 'EAD605051', 'Prak. Mesin Listrik', '5', 1, 'KAMIS', '16:20 - 17:10', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(831, 'EAD606352', 'Piranti Optoelektronika', '5', 2, 'SENIN', '08:00 - 09:40', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(832, 'EAD606552', 'Antena dan Propagasi', '5', 2, 'SENIN', '13:00 - 14:40', 'A', 'Zainudin Bonok, ST.,MT.', 'Ifan Wiranto, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(833, 'EAD606652', 'Mikroprosesor dan Mikrokontroler', '5', 2, 'JUMAT', '08:00 - 09:40', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(834, 'EAD606751', 'Prak. Mikroprosesor dan Mikrokontroler', '5', 1, 'JUMAT', '08:00 - 08:50', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(835, 'EAD606852', 'Jaringan Telekomunikasi', '5', 2, 'RABU', '13:00 - 14:40', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Zainudin Bonok, ST.,MT.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(836, 'EAD607152', 'Metodologi Riset', '5', 2, 'KAMIS', '16:20 - 18:00', 'A', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Ifan Wiranto, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(837, 'EAD607274', 'Magang', '7', 4, '', ' - ', 'A', 'Ifan Wiranto, ST., MT.', 'Dosen Luar', '', '2026/2027 - Ganjil'),
(838, 'EAD607672', 'K3 dan Etika Profesi', '7', 2, 'KAMIS', '08:00 - 09:40', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(839, 'EAD607973', 'Internet of Things', '7', 3, 'JUMAT', '15:30 - 18:00', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(840, 'EAD608173', 'Pemrograman Web', '7', 3, 'SELASA', '14:40 - 17:10', 'A', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(841, 'EAD608273', 'Standarisasi', '7', 3, 'RABU', '08:00 - 10:30', 'A', 'Jumiati Ilham, ST., MT.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(842, 'EAD608573', 'Robotika', '7', 3, 'SENIN', '13:00 - 15:30', 'A', 'Hendy Prasetyo, S.S.T., M.T.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(843, 'EAD605852', 'Pengolahan Sinyal Digital', '5', 2, 'KAMIS', '13:00 - 14:40', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Hendy Prasetyo, S.S.T., M.T.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(844, 'EAD608073', 'Aplikasi Komputer dalam  Sistem Tenaga Listrik', '7', 3, 'RABU', '13:00 - 15:30', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(845, 'NAS0503600212', 'Pancasila', '1', 2, 'JUMAT', '09:40 - 11:20', 'A', 'Dosen Luar', 'Dosen Luar', 'R.K3.16', '2026/2027 - Ganjil'),
(846, 'NAS0503600412', 'Bahasa Indonesia', '1', 2, 'JUMAT', '08:00 - 09:40', 'A', 'Dosen Luar', 'Dosen Luar', 'R.K3.16', '2026/2027 - Ganjil'),
(847, 'EAL601313', 'Pengantar Statistik dan Probabilitas', '1', 3, 'KAMIS', '08:00 - 10:30', 'B', 'Ervan Hasan Harun, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(848, 'NAS0511600412', 'Bahasa Indonesia', '1', 2, 'SABTU', '13:00 - 14:40', 'B', 'Dosen Luar', 'Dosen Luar', 'R.K2.11', '2026/2027 - Ganjil'),
(849, 'NAS0511600412', 'Bahasa Indonesia', '1', 2, 'SELASA', '10:30 - 12:10', 'C', 'Dosen Luar', 'Dosen Luar', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(850, 'EAL601413', 'Teknologi Informasi', '1', 3, 'SELASA', '08:00 - 10:30', 'C', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'Zainudin Bonok, ST.,MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(851, 'EAL601113', 'Dasar-Dasar Pemrograman 1', '1', 3, 'RABU', '15:30 - 18:00', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(852, 'EAL601313', 'Pengantar Statistik dan Probabilitas', '1', 3, 'JUMAT', '15:30 - 18:00', 'C', 'Ervan Hasan Harun, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(853, 'EAL601013', 'Kalkulus 1', '1', 3, 'RABU', '08:00 - 10:30', 'C', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(854, 'EAL601413', 'Teknologi Informasi', '1', 3, 'RABU', '09:40 - 12:10', 'D', 'Zainudin Bonok, ST.,MT.', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'R.K2.11', '2026/2027 - Ganjil'),
(855, 'EAL601113', 'Dasar-Dasar Pemrograman 1', '1', 3, 'RABU', '13:00 - 15:30', 'D', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(856, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '08:50 - 09:40', 'D', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(857, 'EAL601013', 'Kalkulus 1', '1', 3, 'RABU', '15:30 - 18:00', 'D', 'Ifan Wiranto, ST., MT.', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'R.K3.16', '2026/2027 - Ganjil'),
(858, 'EAL601313', 'Pengantar Statistik dan Probabilitas', '1', 3, 'SABTU', '09:40 - 12:10', 'D', 'Ade Irawaty Tolago, ST., MT.', 'Ervan Hasan Harun, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(859, 'EAL601413', 'Teknologi Informasi', '1', 3, 'SENIN', '15:30 - 18:00', 'E', 'Zainudin Bonok, ST.,MT.', 'Dr. Mohamad Syafri Tuloli, ST, MT', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(860, 'EAL601113', 'Dasar-Dasar Pemrograman 1', '1', 3, 'SENIN', '08:00 - 10:30', 'E', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(861, 'EAL601211', 'Prak. Dasar-Dasar Pemrograman 1', '1', 1, 'SABTU', '08:00 - 08:50', 'E', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(862, 'NAS0503600412', 'Bahasa Indonesia', '1', 2, 'SELASA', '10:30 - 12:10', 'B', 'Dosen Luar', 'Dosen Luar', 'R.K2.11', '2026/2027 - Ganjil'),
(863, 'EAD600913', 'Kalkulus 1', '1', 3, 'RABU', '15:30 - 18:00', 'B', 'Ervan Hasan Harun, ST., MT.', 'Taufiq Ismail Yusuf, ST., M.Si.', 'R.K2.11', '2026/2027 - Ganjil'),
(864, 'EAD600913', 'Kalkulus 1', '1', 3, 'JUMAT', '13:00 - 15:30', 'C', 'Taufiq Ismail Yusuf, ST., M.Si.', 'Ervan Hasan Harun, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(865, 'EAD601513', 'Probabilitas dan Statistika', '1', 3, 'SENIN', '13:00 - 15:30', 'C', 'Nurul, S.Pd., M.Pd', 'Ade Irawaty Tolago, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(866, 'EAD602011', 'Prak. Pemrograman Dasar', '1', 1, 'SABTU', '10:30 - 11:20', 'D', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(867, 'EAD601913', 'Pemrograman Dasar', '1', 3, 'KAMIS', '08:00 - 10:30', 'D', 'Prof. Lanto Ningrayati Amali, S.Kom., M.Kom., Ph.D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(868, 'EAL604751', 'Prak. Desain Rekayasa Komputasi 1', '5', 1, 'JUMAT', '08:00 - 08:50', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(869, 'EAL604751', 'Prak. Desain Rekayasa Komputasi 1', '5', 1, 'JUMAT', '08:50 - 09:40', 'B', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(870, 'UNG0503600874', 'Kuliah Kerja Nyata', '7', 4, '', ' - ', 'A', 'Yasin Mohamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2026/2027 - Ganjil'),
(871, 'NAS0503600412', 'Bahasa Indonesia', '1', 2, 'SENIN', '10:30 - 12:10', 'C', 'Dosen Luar', 'Dosen Luar', 'R.K3.16', '2026/2027 - Ganjil'),
(872, 'EAD601633', 'Matematika Diskrit', '3', 3, 'RABU', '13:00 - 15:30', 'C', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Rahmat D.R. Dako, ST., M.Eng.', 'R.K3.16', '2026/2027 - Ganjil'),
(873, 'EAD601833', 'Variabel Kompleks', '3', 3, 'SELASA', '15:30 - 18:00', 'C', 'Ifan Wiranto, ST., MT.', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'R.K2.11', '2026/2027 - Ganjil'),
(874, 'EAD602232', 'Rangkaian Listrik 2', '3', 2, 'SENIN', '13:00 - 14:40', 'C', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(875, 'EAD602331', 'Prak. Rangkaian Listrik', '3', 1, 'SABTU', '14:40 - 15:30', 'C', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(876, 'EAD602432', 'Elektronika Dasar', '3', 2, 'SELASA', '13:00 - 14:40', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(877, 'EAD602432', 'Elektronika Dasar', '3', 2, 'KAMIS', '08:00 - 09:40', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(878, 'EAD602531', 'Prak. Elektronika Dasar', '3', 1, 'JUMAT', '13:50 - 14:40', 'B', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(879, 'NAS0503600212', 'Pancasila', '1', 2, 'KAMIS', '10:30 - 12:10', 'B', 'Dosen Luar', 'Dosen Luar', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(880, 'EAL602433', 'Persamaan Diferensial', '3', 3, 'JUMAT', '08:00 - 10:30', 'C', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Ifan Wiranto, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(881, 'EAL602933', 'Fisika 2', '3', 3, 'SELASA', '13:00 - 15:30', 'C', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Yasin Mohamad, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(882, 'EAL602533', 'Sirkuit & Elektronika', '3', 3, 'SENIN', '15:30 - 18:00', 'C', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Wildan, S.Pd. M.Pd', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(883, 'EAL602631', 'Prak. Sirkuit & Elektronika', '3', 1, 'SABTU', '13:50 - 14:40', 'C', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Wildan, S.Pd. M.Pd', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(884, 'EAL602732', 'Desain Digital & Logika', '3', 2, 'SENIN', '10:30 - 12:10', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(885, 'EAL602831', 'Prak. Desain Digital & Logika', '3', 1, 'SABTU', '13:00 - 13:50', 'C', 'Dr. Bambang Panji Asmara, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(886, 'EAL603033', 'Pemrograman Client/Server', '3', 3, 'RABU', '15:30 - 18:00', 'C', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(887, 'EAL603033', 'Pemrograman Client/Server', '3', 3, 'SENIN', '13:00 - 15:30', 'D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(888, 'EAL603131', 'Prak. Pemrograman Client/Server', '3', 1, 'SABTU', '15:30 - 16:20', 'C', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(889, 'EAL603131', 'Prak. Pemrograman Client/Server', '3', 1, 'SABTU', '15:30 - 16:20', 'D', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Rahmat D.R. Dako, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(890, 'EAL604152', 'Multimedia', '5', 2, 'KAMIS', '08:00 - 09:40', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(891, 'EAL604152', 'Multimedia', '5', 2, 'KAMIS', '09:40 - 11:20', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(892, 'EAL604251', 'Prak. Multimedia', '5', 1, 'JUMAT', '08:50 - 09:40', 'B', 'Ikhsan Hidayat, S.Kom., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(893, 'EAL604251', 'Prak. Multimedia', '5', 1, '', ' - ', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Ikhsan Hidayat, S.Kom., MT.', '', '2026/2027 - Ganjil'),
(894, 'EAL604353', 'Jaringan Komputasi', '5', 3, 'SENIN', '14:40 - 17:10', 'C', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(895, 'EAL604452', 'Sistem Operasi', '5', 2, 'SENIN', '13:00 - 14:40', 'C', 'Ikhsan Hidayat, S.Kom., MT.', 'Dr. Bambang Panji Asmara, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(896, 'EAL604652', 'Desain Rekayasa Komputasi 1', '5', 2, 'SELASA', '10:30 - 12:10', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(897, 'EAL604751', 'Prak. Desain Rekayasa Komputasi 1', '5', 1, '', ' - ', 'C', 'Rahmat D.R. Dako, ST., M.Eng.', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', '', '2026/2027 - Ganjil'),
(898, 'EAL605674', 'Magang', '7', 4, '', ' - ', 'A', 'Yasin Mohamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2026/2027 - Ganjil'),
(899, 'UNG0511600874', 'KKN', '7', 4, '', ' - ', 'A', 'Yasin Mohamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', '', '2026/2027 - Ganjil'),
(900, 'EAN601512', 'Pengantar Elektro Teknik', '1', 2, 'KAMIS', '08:00 - 09:40', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Nurul, S.Pd., M.Pd', 'LabInstalasi', '2026/2027 - Ganjil'),
(901, 'EAN601612', 'Matematika dasar', '1', 2, 'KAMIS', '10:30 - 12:10', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LabInstalasi', '2026/2027 - Ganjil'),
(902, 'EAN601712', 'Fisika', '1', 2, 'KAMIS', '13:00 - 14:40', 'A', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'Yasin Mohamad, ST., MT.', 'LabInstalasi', '2026/2027 - Ganjil'),
(903, 'EAN601912', 'Ilmu Bahan Listrik', '1', 2, 'SELASA', '10:30 - 12:10', 'A', 'Nurul, S.Pd., M.Pd', 'Jumiati Ilham, ST., MT.', 'LabInstalasi', '2026/2027 - Ganjil'),
(904, 'EAN601812', 'Menggambar Teknik', '1', 2, 'SENIN', '15:30 - 17:10', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LabInstalasi', '2026/2027 - Ganjil'),
(905, 'EAN602012', 'Algoritma dan Pemrograman', '1', 2, 'RABU', '10:30 - 12:10', 'A', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Dr. Bambang Panji Asmara, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(906, 'NAS0513600412', 'Bahasa Indonesia', '1', 2, 'SABTU', '09:40 - 11:20', 'A', 'Dosen Luar', 'Dosen Luar', 'LabInstalasi', '2026/2027 - Ganjil'),
(907, 'NAS0513600212', 'Pancasila', '1', 2, 'JUMAT', '15:30 - 17:10', 'A', 'Dosen Luar', 'Dosen Luar', 'LabInstalasi', '2026/2027 - Ganjil'),
(908, 'EAD607374', 'Perancangan Teknik Elektro', '7', 4, 'SABTU', '08:00 - 11:20', 'A', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(909, 'EAD608673', 'Pengolahan Citra', '7', 3, 'KAMIS', '09:40 - 12:10', 'A', 'Salmawaty Tansa, ST., M.Eng.', 'Dr. Bambang Panji Asmara, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(910, 'EAD608773', 'Sistem Komunikasi Bergerak ', '7', 3, 'SENIN', '08:00 - 10:30', 'A', 'Zainudin Bonok, ST.,MT.', 'Syahrir Abdussamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(911, 'EAL604852', 'Metode Riset', '5', 2, 'JUMAT', '15:30 - 17:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(912, 'UNG0503600572', 'Literasi Digital', '7', 2, 'SELASA', '13:00 - 14:40', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Prof. Dr. Ir. Sardi Salim, M. Pd.,IPU., ASEAN Eng.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(913, 'UNG0513600912', 'Perkembangan Peserta Didik', '1', 2, 'RABU', '15:30 - 17:10', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Nurul, S.Pd., M.Pd', 'LabInstalasi', '2026/2027 - Ganjil'),
(914, 'UNG0513601012', 'Dasar-Dasar Kependidikan', '1', 2, 'SENIN', '10:30 - 12:10', 'A', 'Ir. Rahmad Hidayat Dongka, S.Pd., M.Pd.', 'Andi Sitti Dwi Auliyani, S.Pd., M.Pd', 'LabInstalasi', '2026/2027 - Ganjil'),
(915, 'EAD607532', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'RABU', '10:30 - 12:10', 'A', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(916, 'EAD607532', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'KAMIS', '10:30 - 12:10', 'B', 'Ade Irawaty Tolago, ST., MT.', 'Yasin Mohamad, ST., MT.', 'R.K2.11', '2026/2027 - Ganjil'),
(917, 'EAD607532', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'SELASA', '10:30 - 12:10', 'C', 'Yasin Mohamad, ST., MT.', 'Ade Irawaty Tolago, ST., MT.', 'R.K3.16', '2026/2027 - Ganjil'),
(918, 'EAL602332', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'SENIN', '10:30 - 12:10', 'A', 'Amirudin Yunus Dako, ST., M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.UNPROTECT', '2026/2027 - Ganjil'),
(919, 'EAL602332', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'KAMIS', '15:30 - 17:10', 'B', 'Syahrir Abdussamad, ST., MT.', 'Amirudin Yunus Dako, ST., M.Eng.', 'R.K2.11', '2026/2027 - Ganjil'),
(920, 'EAL602332', 'Tata Tulis Laporan dan Karya Ilmiah', '3', 2, 'KAMIS', '13:00 - 14:40', 'C', 'Amirudin Yunus Dako, ST., M.Eng.', 'Syahrir Abdussamad, ST., MT.', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(921, 'NAS0503600212', 'Pancasila', '1', 2, 'KAMIS', '08:00 - 09:40', 'C', 'Dosen Luar', 'Dosen Luar', 'LAB.TEKNIKKENDALI', '2026/2027 - Ganjil'),
(922, 'EAD608373', 'Dinamika dan Stabilitas Sistem Tenaga Listrik', '7', 3, 'SENIN', '15:30 - 18:00', 'A', 'Dr.Ir. Arifin Matoka, MT.', 'Yasin Mohamad, ST., MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil'),
(923, 'EAL606573', 'Forensik Digital', '7', 3, 'JUMAT', '13:00 - 15:30', 'A', 'Abdul Gani F. Lihawa, S.Kom., M.Kom', 'Syahrir Abdussamad, ST., MT.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(924, 'EAL606873', 'Kecerdasan Buatan', '7', 3, 'JUMAT', '08:00 - 10:30', 'A', 'Ulfatun Nadifa, S.Pd, M.Kom.', 'Salmawaty Tansa, ST., M.Eng.', 'R.K2.11', '2026/2027 - Ganjil'),
(925, 'EAL606073', 'Cloud computing', '7', 3, 'SELASA', '08:00 - 10:30', 'A', 'Afifah Farhanah Akadji, S.Si., M.Si.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER2', '2026/2027 - Ganjil'),
(926, 'EAL606273', 'Pemrograman Jaringan', '7', 3, 'KAMIS', '15:30 - 18:00', 'A', 'Dr. Bambang Panji Asmara, ST., MT.', 'Salmawaty Tansa, ST., M.Eng.', 'LAB.KOMPUTER1', '2026/2027 - Ganjil'),
(927, 'EAD602531', 'Prak. Elektronika Dasar', '3', 1, 'JUMAT', '14:40 - 15:30', 'C', 'Salmawaty Tansa, ST., M.Eng.', 'Iskandar Z. Nasibu, S.Pd, M.Eng.', 'LAB.ELEKTRONIKADANKOMUNIKASI', '2026/2027 - Ganjil'),
(928, 'EAD605051', 'Prak. Mesin Listrik', '5', 1, 'KAMIS', '17:10 - 18:00', 'B', 'Dr. Lanto M. Kamil Amali, ST., MT.', 'Dr.Ir. Arifin Matoka, MT.', 'LAB.TENAGALISTRIK', '2026/2027 - Ganjil');

-- --------------------------------------------------------

--
-- Table structure for table `temp_kp`
--

CREATE TABLE `temp_kp` (
  `id` int NOT NULL,
  `id_persyaratan` int NOT NULL,
  `nim` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` set('menunggu','diterima','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ket` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'field ini di isi jika dokumen ditolak. alasan penolakan tulis disini.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_skripsi`
--

CREATE TABLE `temp_skripsi` (
  `id` int NOT NULL,
  `id_persyaratan` int NOT NULL,
  `nim` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` set('menunggu','diterima','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ket` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'field ini di isi jika dokumen ditolak. alasan penolakan tulis disini.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip_jurusan`
--
ALTER TABLE `arsip_jurusan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file` (`file`,`pengupload`),
  ADD KEY `pengupload` (`pengupload`);

--
-- Indexes for table `arsip_lain`
--
ALTER TABLE `arsip_lain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file` (`file`,`pengupload`),
  ADD KEY `pengupload` (`pengupload`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar` (`gambar`);

--
-- Indexes for table `berkas_kp`
--
ALTER TABLE `berkas_kp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persyaratan` (`id_persyaratan`,`nim`,`file`),
  ADD KEY `nim` (`nim`),
  ADD KEY `file` (`file`);

--
-- Indexes for table `berkas_skripsi`
--
ALTER TABLE `berkas_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persyaratan` (`id_persyaratan`,`nim`,`file`),
  ADD KEY `nim` (`nim`),
  ADD KEY `file` (`file`);

--
-- Indexes for table `bukti_kerjasama`
--
ALTER TABLE `bukti_kerjasama`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kerjasama` (`id_kerjasama`,`file`),
  ADD KEY `file` (`file`);

--
-- Indexes for table `dokumen_akademik`
--
ALTER TABLE `dokumen_akademik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokumen_akreditas`
--
ALTER TABLE `dokumen_akreditas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori` (`kategori`,`lokasi`),
  ADD KEY `lokasi` (`lokasi`),
  ADD KEY `pengupload` (`pengupload`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profil` (`profil`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengunggah` (`pengunggah`);

--
-- Indexes for table `gambar_beranda`
--
ALTER TABLE `gambar_beranda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar` (`gambar`);

--
-- Indexes for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_matakuliah` (`id_matakuliah`,`pengajar1`,`pengajar2`,`ruangan`),
  ADD KEY `ruangan` (`ruangan`),
  ADD KEY `pengajar1` (`pengajar1`),
  ADD KEY `pengajar2` (`pengajar2`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kajur` (`kajur`,`sekjur`,`kalab`,`penjamu`,`pengelolah_jurnal`),
  ADD KEY `sekjur` (`sekjur`),
  ADD KEY `kalab` (`kalab`),
  ADD KEY `penjamu` (`penjamu`),
  ADD KEY `pengelolah_jurnal` (`pengelolah_jurnal`);

--
-- Indexes for table `kategori-akreditas`
--
ALTER TABLE `kategori-akreditas`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `kerjasama`
--
ALTER TABLE `kerjasama`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kp`
--
ALTER TABLE `kp`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `nim` (`nim`,`surat_penunjukkan_pembimbing`,`surat_penunjukkan_penguji`),
  ADD KEY `surat_penunjukkan_pembimbing` (`surat_penunjukkan_pembimbing`),
  ADD KEY `surat_penunjukkan_penguji` (`surat_penunjukkan_penguji`);

--
-- Indexes for table `kurikulum`
--
ALTER TABLE `kurikulum`
  ADD PRIMARY KEY (`kode`),
  ADD KEY `prodi` (`prodi`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `dosen_pa` (`dosen_pa`);

--
-- Indexes for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum` (`kurikulum`),
  ADD KEY `rps` (`rps`);

--
-- Indexes for table `matakuliah_mbkm`
--
ALTER TABLE `matakuliah_mbkm`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `pemilik_file`
--
ALTER TABLE `pemilik_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemilik` (`pemilik`);

--
-- Indexes for table `persyaratan_berkas_kp`
--
ALTER TABLE `persyaratan_berkas_kp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persyaratan_berkas_skripsi`
--
ALTER TABLE `persyaratan_berkas_skripsi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `progres_kp`
--
ALTER TABLE `progres_kp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mahasiswa` (`mahasiswa`);

--
-- Indexes for table `progres_skripsi`
--
ALTER TABLE `progres_skripsi`
  ADD PRIMARY KEY (`waktu`),
  ADD KEY `proses` (`proses`,`mahasiswa`),
  ADD KEY `mahasiswa` (`mahasiswa`);

--
-- Indexes for table `riwayat_pendidikan`
--
ALTER TABLE `riwayat_pendidikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rp_nip` (`nip`);

--
-- Indexes for table `ruang_kuliah`
--
ALTER TABLE `ruang_kuliah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skp`
--
ALTER TABLE `skp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen` (`dosen`,`penilaian`,`hasil`),
  ADD KEY `penilaian` (`penilaian`),
  ADD KEY `hasil` (`hasil`);

--
-- Indexes for table `skripsi`
--
ALTER TABLE `skripsi`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `nim` (`nim`),
  ADD KEY `penunjukkan_pembimbing` (`penunjukkan_pembimbing`),
  ADD KEY `penunjukkan_hasil` (`penunjukkan_hasil`,`penunjukkan_tutup`),
  ADD KEY `penunjukkan_tutup` (`penunjukkan_tutup`);

--
-- Indexes for table `sop_kp`
--
ALTER TABLE `sop_kp`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `sop_skripsi`
--
ALTER TABLE `sop_skripsi`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `surat_penunjukkan`
--
ALTER TABLE `surat_penunjukkan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembimbing1` (`pembimbing1`,`pembimbing2`,`penguji1`,`penguji2`,`penguji3`,`penguji4`,`penguji5`),
  ADD KEY `nim` (`nim`,`ttd`),
  ADD KEY `pembimbing2` (`pembimbing2`),
  ADD KEY `penguji1` (`penguji1`),
  ADD KEY `penguji2` (`penguji2`),
  ADD KEY `penguji5` (`penguji5`),
  ADD KEY `penguji3` (`penguji3`),
  ADD KEY `penguji4` (`penguji4`);

--
-- Indexes for table `temp_jadwal_seleksi`
--
ALTER TABLE `temp_jadwal_seleksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_kp`
--
ALTER TABLE `temp_kp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persyaratan` (`id_persyaratan`,`nim`,`file`),
  ADD KEY `nim` (`nim`),
  ADD KEY `file` (`file`);

--
-- Indexes for table `temp_skripsi`
--
ALTER TABLE `temp_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persyaratan` (`id_persyaratan`,`nim`,`file`),
  ADD KEY `nim` (`nim`),
  ADD KEY `file` (`file`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip_jurusan`
--
ALTER TABLE `arsip_jurusan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `berkas_kp`
--
ALTER TABLE `berkas_kp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `berkas_skripsi`
--
ALTER TABLE `berkas_skripsi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `bukti_kerjasama`
--
ALTER TABLE `bukti_kerjasama`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `gambar_beranda`
--
ALTER TABLE `gambar_beranda`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `matakuliah`
--
ALTER TABLE `matakuliah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=456;

--
-- AUTO_INCREMENT for table `pemilik_file`
--
ALTER TABLE `pemilik_file`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `persyaratan_berkas_kp`
--
ALTER TABLE `persyaratan_berkas_kp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `persyaratan_berkas_skripsi`
--
ALTER TABLE `persyaratan_berkas_skripsi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `progres_kp`
--
ALTER TABLE `progres_kp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `riwayat_pendidikan`
--
ALTER TABLE `riwayat_pendidikan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `skp`
--
ALTER TABLE `skp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `temp_jadwal_seleksi`
--
ALTER TABLE `temp_jadwal_seleksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=929;

--
-- AUTO_INCREMENT for table `temp_kp`
--
ALTER TABLE `temp_kp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_skripsi`
--
ALTER TABLE `temp_skripsi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arsip_jurusan`
--
ALTER TABLE `arsip_jurusan`
  ADD CONSTRAINT `arsip_jurusan_ibfk_2` FOREIGN KEY (`pengupload`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `arsip_jurusan_ibfk_3` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `arsip_lain`
--
ALTER TABLE `arsip_lain`
  ADD CONSTRAINT `arsip_lain_ibfk_1` FOREIGN KEY (`pengupload`) REFERENCES `dosen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `arsip_lain_ibfk_2` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `berita_ibfk_1` FOREIGN KEY (`gambar`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `berkas_kp`
--
ALTER TABLE `berkas_kp`
  ADD CONSTRAINT `berkas_kp_ibfk_1` FOREIGN KEY (`id_persyaratan`) REFERENCES `persyaratan_berkas_kp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `berkas_kp_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `berkas_kp_ibfk_3` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `berkas_skripsi`
--
ALTER TABLE `berkas_skripsi`
  ADD CONSTRAINT `berkas_skripsi_ibfk_1` FOREIGN KEY (`id_persyaratan`) REFERENCES `persyaratan_berkas_skripsi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `berkas_skripsi_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON UPDATE CASCADE,
  ADD CONSTRAINT `berkas_skripsi_ibfk_3` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `bukti_kerjasama`
--
ALTER TABLE `bukti_kerjasama`
  ADD CONSTRAINT `bukti_kerjasama_ibfk_1` FOREIGN KEY (`id_kerjasama`) REFERENCES `kerjasama` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bukti_kerjasama_ibfk_2` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dokumen_akreditas`
--
ALTER TABLE `dokumen_akreditas`
  ADD CONSTRAINT `dokumen_akreditas_ibfk_1` FOREIGN KEY (`kategori`) REFERENCES `kategori-akreditas` (`no`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dokumen_akreditas_ibfk_2` FOREIGN KEY (`lokasi`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dokumen_akreditas_ibfk_3` FOREIGN KEY (`pengupload`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`profil`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `gambar_beranda`
--
ALTER TABLE `gambar_beranda`
  ADD CONSTRAINT `gambar_beranda_ibfk_1` FOREIGN KEY (`gambar`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD CONSTRAINT `jadwal_kuliah_ibfk_1` FOREIGN KEY (`ruangan`) REFERENCES `ruang_kuliah` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_kuliah_ibfk_2` FOREIGN KEY (`id_matakuliah`) REFERENCES `matakuliah` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_kuliah_ibfk_3` FOREIGN KEY (`pengajar1`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jadwal_kuliah_ibfk_4` FOREIGN KEY (`pengajar2`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD CONSTRAINT `jurusan_ibfk_1` FOREIGN KEY (`kajur`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jurusan_ibfk_2` FOREIGN KEY (`sekjur`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jurusan_ibfk_3` FOREIGN KEY (`kalab`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jurusan_ibfk_4` FOREIGN KEY (`penjamu`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jurusan_ibfk_5` FOREIGN KEY (`pengelolah_jurnal`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kp`
--
ALTER TABLE `kp`
  ADD CONSTRAINT `kp_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kp_ibfk_2` FOREIGN KEY (`surat_penunjukkan_pembimbing`) REFERENCES `surat_penunjukkan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kp_ibfk_3` FOREIGN KEY (`surat_penunjukkan_penguji`) REFERENCES `surat_penunjukkan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kurikulum`
--
ALTER TABLE `kurikulum`
  ADD CONSTRAINT `kurikulum_ibfk_1` FOREIGN KEY (`prodi`) REFERENCES `prodi` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`dosen_pa`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`kurikulum`) REFERENCES `kurikulum` (`kode`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `matakuliah_ibfk_2` FOREIGN KEY (`rps`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pemilik_file`
--
ALTER TABLE `pemilik_file`
  ADD CONSTRAINT `pemilik_file_ibfk_1` FOREIGN KEY (`pemilik`) REFERENCES `dosen` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `progres_kp`
--
ALTER TABLE `progres_kp`
  ADD CONSTRAINT `progres_kp_ibfk_1` FOREIGN KEY (`mahasiswa`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `progres_skripsi`
--
ALTER TABLE `progres_skripsi`
  ADD CONSTRAINT `progres_skripsi_ibfk_1` FOREIGN KEY (`proses`) REFERENCES `sop_skripsi` (`no`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `progres_skripsi_ibfk_2` FOREIGN KEY (`mahasiswa`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `skp`
--
ALTER TABLE `skp`
  ADD CONSTRAINT `skp_ibfk_1` FOREIGN KEY (`penilaian`) REFERENCES `files` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `skp_ibfk_2` FOREIGN KEY (`hasil`) REFERENCES `files` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `skp_ibfk_3` FOREIGN KEY (`dosen`) REFERENCES `dosen` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `skripsi`
--
ALTER TABLE `skripsi`
  ADD CONSTRAINT `skripsi_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `skripsi_ibfk_2` FOREIGN KEY (`penunjukkan_pembimbing`) REFERENCES `surat_penunjukkan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `skripsi_ibfk_3` FOREIGN KEY (`penunjukkan_hasil`) REFERENCES `surat_penunjukkan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `skripsi_ibfk_4` FOREIGN KEY (`penunjukkan_tutup`) REFERENCES `surat_penunjukkan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `surat_penunjukkan`
--
ALTER TABLE `surat_penunjukkan`
  ADD CONSTRAINT `surat_penunjukkan_ibfk_1` FOREIGN KEY (`pembimbing1`) REFERENCES `dosen` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_2` FOREIGN KEY (`pembimbing2`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_3` FOREIGN KEY (`penguji1`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_4` FOREIGN KEY (`penguji2`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_5` FOREIGN KEY (`penguji5`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_6` FOREIGN KEY (`penguji3`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_penunjukkan_ibfk_7` FOREIGN KEY (`penguji4`) REFERENCES `dosen` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `temp_kp`
--
ALTER TABLE `temp_kp`
  ADD CONSTRAINT `temp_kp_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_kp_ibfk_2` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_kp_ibfk_3` FOREIGN KEY (`id_persyaratan`) REFERENCES `sop_kp` (`no`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temp_skripsi`
--
ALTER TABLE `temp_skripsi`
  ADD CONSTRAINT `temp_skripsi_ibfk_1` FOREIGN KEY (`id_persyaratan`) REFERENCES `persyaratan_berkas_skripsi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_skripsi_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON UPDATE CASCADE,
  ADD CONSTRAINT `temp_skripsi_ibfk_3` FOREIGN KEY (`file`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
