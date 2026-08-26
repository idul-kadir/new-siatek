<!--
  Halaman: Berita Jurusan (daftar + detail cepat).
  DUMMY HTML MURNI — SEMUA data berita ditulis langsung di HTML (kartu <article>),
  termasuk isi artikel (di <template data-isi> tiap kartu). Belum terhubung DB.
  JavaScript HANYA untuk: pagination (12/halaman), cari/filter, modal detail, hapus.
-->
<?php
require __DIR__ . '/../../../fungsi.php';
$data_berita = [];
$list_berita = query("SELECT * FROM `berita` ORDER BY tanggal DESC");
foreach($list_berita as $berita){
  $data_berita[] = $berita;
}

?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .news-cover { position: relative; overflow: hidden; background: linear-gradient(150deg, #1e3a5f 0%, #2b4a73 55%, #3b5f8f 100%); }
    .news-cover::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(circle at 88% 12%, rgba(249,115,22,.18) 0, rgba(249,115,22,0) 42%); }
    .news-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .news-cover .news-cover-empty { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.55); font-size: 2.4rem; }

    .news-card { transition: all .2s ease; }
    .news-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -8px rgba(15,23,42,.14); }
    .news-card .news-actions { opacity: 0; transition: opacity .15s ease; }
    .news-card:hover .news-actions { opacity: 1; }

    /* Isi artikel di modal lihat */
    .article-body-view { line-height: 1.75; color: #334155; }
    .article-body-view p { margin: 0 0 1rem; }

    /* Gaya meniru halaman detail-berita publik */
    .view-hero { position: relative; overflow: hidden; background: linear-gradient(150deg, #1e3a5f 0%, #2b4a73 55%, #3b5f8f 100%); }
    .view-hero img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .view-hero .view-hero-empty { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.55); font-size: 3rem; }
    .view-tag { display: inline-block; background: #1a365d; color: #fdba74; font-size: 10.5px; font-weight: 700; letter-spacing: .12em; padding: 4px 10px; text-transform: uppercase; }
    .view-title { font-size: 1.35rem; font-weight: 800; line-height: 1.3; color: #0f172a; letter-spacing: -0.01em; }
    .view-avatar { display: inline-flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border-radius: 9999px; background: #f97316; color: #fff; font-weight: 700; font-size: .85rem; flex-shrink: 0; }
    .view-share { display: inline-flex; align-items: center; justify-content: center; width: 2.1rem; height: 2.1rem; border-radius: 9999px; color: #fff; font-size: .8rem; transition: transform .15s ease, opacity .15s ease; }
    .view-share:hover { transform: translateY(-2px); opacity: .9; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Berita Jurusan</h1>
                    <p class="text-xs text-slate-500">Kabar dan pengumuman jurusan — dummy HTML (struktur tabel berita).</p>
                </div>
            </div>
            <a href="tulis-berita" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tulis Berita Baru</span>
            </a>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter kategori) ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative min-w-[200px] flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari judul, kategori, penulis, isi…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fKategori" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Kategori</option>
                <option>Prestasi</option>
                <option>Kegiatan</option>
                <option>Akademik</option>
                <option>Kemahasiswaan</option>
            </select>
            <a href="jurusan-berita" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </section>

    <!-- ===== Berita Unggulan (terbaru) ===== -->
    <section class="mb-6">
        <article class="grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:grid-cols-2" data-lead>
            <div class="news-cover min-h-[220px] md:min-h-[300px]">
                <img src="https://picsum.photos/seed/jtekrobokri/900/520" alt="Mahasiswa Teknik Menangkan Kontes Robot Indonesia 2026" loading="lazy">
            </div>
            <div class="flex flex-col p-6 md:p-8">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-orange-700">
                        <i class="fas fa-fire"></i> Terbaru
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Prestasi</span>
                    <span class="ml-auto text-xs font-medium text-slate-400">Rabu, 29 Juli 2026</span>
                </div>
                <h2 class="text-lg font-bold leading-snug tracking-tight text-slate-800 md:text-2xl">Mahasiswa Teknik Menangkan Kontes Robot Indonesia 2026</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-500">Tim Robotik Jurusan Teknik Elektro kembali menorehkan prestasi di tingkat nasional. Berikut rangkuman perjalanan mereka hingga meraih juara pertama.</p>
                <div class="mt-auto flex items-center justify-between pt-6">
                    <span class="text-xs text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                data-judul="Mahasiswa Teknik Menangkan Kontes Robot Indonesia 2026" data-kategori="Prestasi"
                                data-penulis="Humas JTEK" data-tanggal="2026-07-29" data-gambar="https://picsum.photos/seed/jtekrobokri/900/520"><i class="fas fa-eye text-xs"></i><span class="tip">Baca</span></button>
                        <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                    </div>
                </div>
            </div>
            <template data-isi>
                <p>Tim Robotik Jurusan Teknik Elektro berhasil meraih juara pertama pada Kontes Robot Indonesia 2026 yang digelar di Yogyakarta.</p>
                <p>Final yang berlangsung ketat menempatkan mereka di atas dua tim unggulan lainnya dari perguruan tinggi negeri terkemuka.</p>
                <p>Dosen pembimbing menyampaikan bahwa kemenangan ini berkat latihan intensif selama enam bulan serta dukungan penuh dari jurusan dan kampus.</p>
                <p>Kontingen akan melanjutkan persiapan untuk mewakili Indonesia di ajang internasional pada akhir tahun ini.</p>
            </template>
        </article>
    </section>

    <!-- ===== Grid Berita (DATA DI HTML — tiap kartu ditulis manual) ===== -->
    <section>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Semua Berita</h2>
                <p class="mt-0.5 text-xs text-slate-500"><span id="jmlBerita">14</span> berita</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                <i class="fas fa-newspaper"></i> <span id="badgeBerita">14</span> item
            </span>
        </div>
        <div id="noHasil" class="hidden border border-dashed border-slate-300 py-16 text-center">
            <i class="fas fa-newspaper text-4xl text-slate-300"></i>
            <p class="mt-3 font-medium text-slate-500">Tidak ada berita ditemukan</p>
            <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" id="newsGrid">

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="2">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekworkshop/600/375" alt="Workshop IoT dan Machine Learning" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kegiatan</span>
                        <span class="font-medium text-slate-400">27 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Workshop IoT dan Machine Learning Digelar di Lab Komputer</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Workshop dua hari membekali peserta dengan dasar Internet of Things dan machine learning di Lab Komputer jurusan.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Workshop IoT dan Machine Learning Digelar di Lab Komputer" data-kategori="Kegiatan"
                                    data-penulis="Humas JTEK" data-tanggal="2026-07-27" data-gambar="https://picsum.photos/seed/jtekworkshop/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="2"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Workshop Internet of Things dan Machine Learning diadakan selama dua hari di Laboratorium Komputer jurusan.</p>
                    <p>Peserta dikenalkan pada mikrokontroler, sensor, hingga tahapan membangun model prediksi sederhana dengan Python.</p>
                    <p>Materi disampaikan oleh dosen dan praktisi industri, diikuti antusias oleh lebih dari enam puluh mahasiswa.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="3">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekpkl/600/375" alt="Praktik Kerja Lapangan Batch 11" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Akademik</span>
                        <span class="font-medium text-slate-400">24 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Praktik Kerja Lapangan (PKL) Batch 11 Resmi Dibuka</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Pendaftaran PKL Batch 11 dibuka hingga 14 Agustus 2026. Simak syarat dan alur pendaftarannya.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Bagian Akademik</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Praktik Kerja Lapangan (PKL) Batch 11 Resmi Dibuka" data-kategori="Akademik"
                                    data-penulis="Bagian Akademik" data-tanggal="2026-07-24" data-gambar="https://picsum.photos/seed/jtekpkl/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="3"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Pendaftaran Praktik Kerja Lapangan Batch 11 resmi dibuka mulai 24 Juli hingga 14 Agustus 2026.</p>
                    <p>Calon peserta wajib melengkapi transkrip nilai, surat pengantar, dan proposal rencana kegiatan yang disetujui dosen wali.</p>
                    <p>Informasi lengkap dan formulir dapat diunduh melalui laman akademik jurusan.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="4">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekbanjir/600/375" alt="Alat Deteksi Banjir Berbasis IoT" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Prestasi</span>
                        <span class="font-medium text-slate-400">21 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Inovasi Mahasiswa: Alat Deteksi Banjir Berbasis IoT</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Prototipe sensor ketinggian air dengan notifikasi real-time ini lolos seleksi pendanaan hibah desa cerdas.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Inovasi Mahasiswa: Alat Deteksi Banjir Berbasis IoT" data-kategori="Prestasi"
                                    data-penulis="Humas JTEK" data-tanggal="2026-07-21" data-gambar="https://picsum.photos/seed/jtekbanjir/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="4"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Sebuah tim mahasiswa mengembangkan alat deteksi dini banjir yang mengirimkan notifikasi otomatis ke gawai warga.</p>
                    <p>Prototipe menggunakan sensor ultrasonik dan modul IoT yang terhubung ke platform data cloud.</p>
                    <p>Karya ini lolos seleksi pendanaan hibah program desa cerdas dan akan diuji coba di beberapa kelurahan.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="5">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekalumni/600/375" alt="Seminar Karier Alumni Teknik Elektro" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kemahasiswaan</span>
                        <span class="font-medium text-slate-400">18 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Seminar Karier bersama Alumni Teknik Elektro</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">BMFT mengundang alumnus yang berkarier di industri energi dan teknologi untuk berbagi pengalaman.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>BMFT JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Seminar Karier bersama Alumni Teknik Elektro" data-kategori="Kemahasiswaan"
                                    data-penulis="BMFT JTEK" data-tanggal="2026-07-18" data-gambar="https://picsum.photos/seed/jtekalumni/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="5"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Badan Mahasiswa Fakultas Teknik menggelar seminar karier dengan menghadirkan alumni yang berkarier di industri energi dan teknologi.</p>
                    <p>Para narasumber membagikan strategi membangun portofolio, tips menghadapi rekrutmen, serta peluang bekerja di luar negeri.</p>
                    <p>Kegiatan ditutup dengan sesi tanya jawab yang dimoderasi oleh ketua program studi.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="6">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekweb/600/375" alt="Pelatihan Pemrograman Web" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kegiatan</span>
                        <span class="font-medium text-slate-400">15 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Pelatihan Pemrograman Web untuk Mahasiswa Angkatan 2025</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Pengenalan HTML, CSS, dan JavaScript dasar bagi mahasiswa baru dalam dua sesi pekanan.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>UKM IT JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Pelatihan Pemrograman Web untuk Mahasiswa Angkatan 2025" data-kategori="Kegiatan"
                                    data-penulis="UKM IT JTEK" data-tanggal="2026-07-15" data-gambar="https://picsum.photos/seed/jtekweb/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="6"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Unit Kegiatan Mahasiswa IT menggelar pelatihan pemrograman web bagi mahasiswa baru angkatan 2025.</p>
                    <p>Materi mencakup dasar HTML, CSS, dan JavaScript yang disampaikan dalam dua sesi pekanan.</p>
                    <p>Peserta yang lolos sertifikasi akhir akan mendapatkan e-sertifikat dan kesempatan bergabung ke tim pengembangan.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="7">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekintl/600/375" alt="Kompetisi Robotik Internasional" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Prestasi</span>
                        <span class="font-medium text-slate-400">11 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Tim Robotik JTEK Wakili Kampus di Kompetisi Internasional</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Kontingen robotik berangkat bulan depan mewakili kampus pada ajang bergengsi di Asia Tenggara.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Tim Robotik JTEK Wakili Kampus di Kompetisi Internasional" data-kategori="Prestasi"
                                    data-penulis="Humas JTEK" data-tanggal="2026-07-11" data-gambar="https://picsum.photos/seed/jtekintl/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="7"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Setelah menjuarai kontes nasional, tim robotik jurusan kembali mendapat kepercayaan mewakili kampus di kompetisi internasional.</p>
                    <p>Ajang bergengsi di Asia Tenggara tersebut akan diikuti tim dari lebih dari dua puluh negara.</p>
                    <p>Persiapan ditingkatkan termasuk penyempurnaan mekanik, algoritma navigasi, dan uji coba lintasan sematik.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="8">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekpmb/600/375" alt="Penerimaan Mahasiswa Baru Jalur Prestasi" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Akademik</span>
                        <span class="font-medium text-slate-400">8 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Penerimaan Mahasiswa Baru Jalur Prestasi 2026 Dibuka</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Penerimaan mahasiswa baru jalur prestasi tahun akademik 2026/2027 resmi dibuka untuk pendaftaran daring.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Panitia PMB</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Penerimaan Mahasiswa Baru Jalur Prestasi 2026 Dibuka" data-kategori="Akademik"
                                    data-penulis="Panitia PMB" data-tanggal="2026-07-08" data-gambar="https://picsum.photos/seed/jtekpmb/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="8"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Penerimaan mahasiswa baru jalur prestasi tahun akademik 2026/2027 resmi dibuka.</p>
                    <p>Pendaftar diseleksi berdasarkan nilai rapor dan portofolio prestasi dengan jadwal daring.</p>
                    <p>Pengumuman awal akan disampaikan melalui laman resmi dan media sosial jurusan.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="9">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekwira/600/375" alt="Pelatihan Kewirausahaan" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kegiatan</span>
                        <span class="font-medium text-slate-400">5 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Pelatihan Kewirausahaan untuk Mahasiswa Tingkat Akhir</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Pelatihan kewirausahaan diselenggarakan khusus untuk mahasiswa tingkat akhir yang ingin merintis usaha.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>UKM Wirausaha</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Pelatihan Kewirausahaan untuk Mahasiswa Tingkat Akhir" data-kategori="Kegiatan"
                                    data-penulis="UKM Wirausaha" data-tanggal="2026-07-05" data-gambar="https://picsum.photos/seed/jtekwira/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="9"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Pelatihan kewirausahaan diselenggarakan khusus untuk mahasiswa tingkat akhir yang ingin merintis usaha.</p>
                    <p>Materi mencakup validasi ide, pemasaran digital, dan simulasi penyusunan rencana bisnis.</p>
                    <p>Peserta terbaik mendapat pendampingan dan akses permodalan dari mitra inkubator.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="10">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jteksolar/600/375" alt="Kuliah Umum Energi Terbarukan" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kegiatan</span>
                        <span class="font-medium text-slate-400">1 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Kuliah Umum bersama Praktisi Energi Terbarukan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Jurusan mengundang praktisi energi terbarukan untuk membahas transisi energi nasional dan peran lulusan teknik.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Kuliah Umum bersama Praktisi Energi Terbarukan" data-kategori="Kegiatan"
                                    data-penulis="Humas JTEK" data-tanggal="2026-07-01" data-gambar="https://picsum.photos/seed/jteksolar/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="10"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Jurusan mengundang praktisi energi terbarukan untuk menyampaikan kuliah umum bertema transisi energi nasional.</p>
                    <p>Pembahasan menyoroti peran lulusan teknik dalam proyek pembangkit dan efisiensi energi.</p>
                    <p>Kegiatan berlangsung terbuka dan dihadiri mahasiswa dari berbagai angkatan.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="11">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekbangkit/600/375" alt="Program Bangkit 2026" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Prestasi</span>
                        <span class="font-medium text-slate-400">27 Juni 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Mahasiswa JTEK Lolos Program Bangkit 2026</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Sejumlah mahasiswa jurusan dinyatakan lolos Program Bangkit yang diselenggarakan Kampus Merdeka.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Mahasiswa JTEK Lolos Program Bangkit 2026" data-kategori="Prestasi"
                                    data-penulis="Humas JTEK" data-tanggal="2026-06-27" data-gambar="https://picsum.photos/seed/jtekbangkit/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="11"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Sejumlah mahasiswa jurusan dinyatakan lolos Program Bangkit yang diselenggarakan Kampus Merdeka.</p>
                    <p>Program tersebut mencakup pembelajaran cloud computing, machine learning, dan pengembangan karir.</p>
                    <p>Mereka akan mengikuti program intensif selama beberapa bulan bersama mahasiswa dari seluruh Indonesia.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="12">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekkalender/600/375" alt="Kalender Akademik" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Akademik</span>
                        <span class="font-medium text-slate-400">22 Juni 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Kalender Akademik Semester Ganjil 2026/2027 Terbit</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Kalender akademik semester ganjil 2026/2027 telah terbit, termasuk perubahan jadwal UTS dan pendaftaran ulang.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Bagian Akademik</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Kalender Akademik Semester Ganjil 2026/2027 Terbit" data-kategori="Akademik"
                                    data-penulis="Bagian Akademik" data-tanggal="2026-06-22" data-gambar="https://picsum.photos/seed/jtekkalender/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="12"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Kalender akademik semester ganjil tahun akademik 2026/2027 telah terbit dan dapat diunduh.</p>
                    <p>Terdapat perubahan jadwal pendaftaran ulang serta pelaksanaan UTS yang lebih awal.</p>
                    <p>Mahasiswa diimbau mencermati setiap tanggal penting sehingga tidak tertinggal informasi.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="13">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekbaksos/600/375" alt="Bakti Sosial Teknik Elektro" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kemahasiswaan</span>
                        <span class="font-medium text-slate-400">18 Juni 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Kegiatan Bakti Sosial Teknik Elektro di Desa Sekitar</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Bakti sosial perbaikan instalasi listrik dan pelatihan hemat energi di desa sekitar kampus melibatkan puluhan relawan.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>BMFT JTEK</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Kegiatan Bakti Sosial Teknik Elektro di Desa Sekitar" data-kategori="Kemahasiswaan"
                                    data-penulis="BMFT JTEK" data-tanggal="2026-06-18" data-gambar="https://picsum.photos/seed/jtekbaksos/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="13"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Badan Mahasiswa mengadakan bakti sosial berupa perbaikan instalasi listrik dan pelatihan hemat energi di desa sekitar kampus.</p>
                    <p>Kegiatan melibatkan dosen pendamping dan puluhan relawan mahasiswa.</p>
                    <p>Program berjalan lancar dan mendapat apresiasi dari warga serta pemerintah desa.</p>
                </template>
            </article>

            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" data-id="14">
                <div class="news-cover aspect-[16/10]"><img src="https://picsum.photos/seed/jtekbeasiswa/600/375" alt="Beasiswa Unggulan Kemendikbud" loading="lazy"></div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kemahasiswaan</span>
                        <span class="font-medium text-slate-400">12 Juni 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">Pendaftaran Beasiswa Unggulan Kemendikbud Dibuka</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">Pendaftaran beasiswa memerlukan IPK minimal, surat rekomendasi, dan esai rencana studi melalui portal resmi.</p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Bagian Akademik</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btnBaca btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"
                                    data-judul="Pendaftaran Beasiswa Unggulan Kemendikbud Dibuka" data-kategori="Kemahasiswaan"
                                    data-penulis="Bagian Akademik" data-tanggal="2026-06-12" data-gambar="https://picsum.photos/seed/jtekbeasiswa/600/375"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <a href="tulis-berita" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></a>
                            <button type="button" class="btnHapus btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600" data-id="14"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
                <template data-isi>
                    <p>Pendaftaran Beasiswa Unggulan Kemendikbud untuk tahun berjalan resmi dibuka.</p>
                    <p>Persyaratan meliputi IPK minimal, surat rekomendasi, dan esai rencana studi.</p>
                    <p>Mahasiswa yang berminat diminta mendaftar melalui portal resmi sebelum batas waktu.</p>
                </template>
            </article>

        </div>

        <!-- Pagination (1 halaman = 12 berita) -->
        <nav class="mt-6 flex items-center justify-center gap-3" id="pager">
            <button type="button" id="btnPrev" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <span id="pagerLabel" class="min-w-[100px] text-center text-xs font-semibold text-slate-600">Halaman 1 / 1</span>
            <button type="button" id="btnNext" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </nav>
    </section>

    <!-- ===== Modal Lihat Berita (inti artikel saja) ===== -->
    <div class="modal-overlay" id="lihatModal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[92vh]">

            <div class="flex-1 overflow-y-auto">

                <!-- Article core -->
                <div class="relative">

                    <!-- Close -->
                    <button type="button" class="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/70 text-white transition hover:bg-slate-900" data-modal-close>
                        <i class="fas fa-times text-sm"></i>
                    </button>

                    <!-- Hero image -->
                    <div class="view-hero aspect-[16/9]">
                        <img id="viewImg" src="" alt="" loading="lazy">
                        <div class="view-hero-empty" id="viewImgEmpty"><i class="fas fa-newspaper"></i></div>
                    </div>

                    <div class="px-6 pt-5 pb-6">
                        <!-- Tag kategori -->
                        <span class="view-tag" id="viewKategori">—</span>

                        <h1 id="viewJudul" class="view-title mt-3">—</h1>

                        <!-- Meta penulis -->
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="view-avatar" id="viewAvatar">H</span>
                                <div>
                                    <strong id="viewPenulis" class="block text-sm font-semibold text-slate-700">—</strong>
                                    <span id="viewTanggal" class="text-xs text-slate-400">—</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                <i class="far fa-clock text-orange-500"></i><span id="viewWaktuBaca">1</span> menit baca
                            </span>
                        </div>

                        <!-- Share -->
                        <div class="flex flex-wrap items-center gap-1.5 py-4">
                            <span class="mr-1 text-xs font-semibold text-slate-500">Bagikan:</span>
                            <span class="view-share bg-[#25D366]" title="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></span>
                            <span class="view-share bg-[#1877F2]" title="Bagikan ke Facebook"><i class="fab fa-facebook-f"></i></span>
                            <span class="view-share bg-slate-900" title="Bagikan ke X / Twitter"><i class="fab fa-x-twitter"></i></span>
                            <span class="view-share bg-slate-200 text-slate-600" title="Salin tautan"><i class="fas fa-link"></i></span>
                        </div>

                        <!-- Isi artikel -->
                        <div class="article-body-view" id="viewIsi"></div>

                        <!-- Footer -->
                        <footer class="mt-6 border-t border-slate-100 pt-4">
                            <button type="button" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-orange-600" data-modal-close>
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita
                            </button>
                        </footer>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
    (function () {
        'use strict';

        var ITEM_PER_PAGE = 12;   // 1 halaman = 12 berita

        /* ===== Elemen ===== */
        var q = document.getElementById('fCari');
        var kat = document.getElementById('fKategori');
        var grid = document.getElementById('newsGrid');
        var leadCard = document.querySelector('[data-lead]');
        var jml = document.getElementById('jmlBerita');
        var badge = document.getElementById('badgeBerita');
        var kosong = document.getElementById('noHasil');
        var btnPrev = document.getElementById('btnPrev');
        var btnNext = document.getElementById('btnNext');
        var pagerLabel = document.getElementById('pagerLabel');
        var modal = document.getElementById('lihatModal');

        var halaman = 1;
        var kartu = Array.prototype.slice.call(document.querySelectorAll('#newsGrid .news-card'));

        /* ===== Ambil data kartu DARI HTML (data-* pada tombol + <template data-isi>) ===== */
        function infoKartu(el) {
            var b = el.querySelector('.btnBaca');
            var t = el.querySelector('[data-isi]');
            return {
                judul: b ? (b.getAttribute('data-judul') || '') : '',
                kategori: b ? (b.getAttribute('data-kategori') || '') : '',
                penulis: b ? (b.getAttribute('data-penulis') || '') : '',
                tanggal: b ? (b.getAttribute('data-tanggal') || '') : '',
                gambar: b ? (b.getAttribute('data-gambar') || '') : '',
                isi: t ? (t.textContent || '') : ''
            };
        }

        /* ===== Pendukung teks ===== */
        function esc(t) { return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
        function fmtTgl(v) {
            if (!v) return '';
            var p = v.split('-'); if (p.length !== 3) return v;
            return ((+p[2])) + ' ' + ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][+p[1] - 1] + ' ' + p[0];
        }
        function fmtHari(v) {
            if (!v) return '';
            var p = v.split('-'); if (p.length !== 3) return v;
            var d = new Date(+p[0], +p[1] - 1, +p[2]);
            return ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][d.getDay()];
        }
        function waktuBaca(src) {
            var w = stripIsi(src).split(/\s+/).filter(Boolean).length;
            return Math.max(1, Math.round(w / 180));
        }
        function stripIsi(src) { return (src || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(); }

        /* ===== Cocok / tidak dengan filter ===== */
        function cocok(el) {
            var info = infoKartu(el);
            var kata = (q.value || '').toLowerCase().trim();
            var k = kat.value.toLowerCase();
            var teks = (info.judul + ' ' + info.kategori + ' ' + info.penulis + ' ' + stripIsi(info.isi)).toLowerCase();
            if (kata !== '' && teks.indexOf(kata) === -1) return false;
            if (k !== '' && info.kategori.toLowerCase() !== k) return false;
            return true;
        }

        /* ===== Pagination & render ===== */
        function jumlahHalaman() {
            return Math.max(1, Math.ceil(kartu.filter(cocok).length / ITEM_PER_PAGE));
        }

        function render() {
            var leadShown = !leadCard || cocok(leadCard);
            var cocokBaris = kartu.filter(cocok);

            // Kartu unggulan
            if (leadCard) leadCard.style.display = leadShown ? '' : 'none';

            // Grid dipaginasi
            var jmlHal = Math.max(1, Math.ceil(cocokBaris.length / ITEM_PER_PAGE));
            if (halaman > jmlHal) halaman = jmlHal;
            var mulai = (halaman - 1) * ITEM_PER_PAGE;
            var tampilSet = cocokBaris.slice(mulai, mulai + ITEM_PER_PAGE);

            // Semua ditampilkan dulu, lalu sembunyikan yang bukan halaman ini
            kartu.forEach(function (c) { c.style.display = 'none'; });
            if (leadShown && leadCard) leadCard.style.display = '';
            tampilSet.forEach(function (c) { c.style.display = ''; });

            // Counter
            var total = cocokBaris.length + (leadShown ? 1 : 0);
            if (jml) jml.textContent = cocokBaris.length + (leadShown ? 1 : 0);
            if (badge) badge.textContent = total;
            if (kosong) kosong.classList.toggle('hidden', total > 0);

            // Pagination
            pagerLabel.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;
            btnPrev.disabled = halaman <= 1;
            btnNext.disabled = halaman >= jmlHal;
        }

        var timer = null;
        function renderDelay() {
            clearTimeout(timer);
            timer = setTimeout(render, 120);
        }

        /* ===== Bind: cari & filter ===== */
        if (q) q.addEventListener('input', renderDelay);
        if (kat) kat.addEventListener('change', renderDelay);

        /* ===== Bind: pagination ===== */
        btnPrev.addEventListener('click', function () { if (halaman > 1) { halaman--; render(); } });
        btnNext.addEventListener('click', function () { if (halaman < jumlahHalaman()) { halaman++; render(); } });

        /* ===== Bind: aksi kartu (delegasi) & modal ===== */
        function openView(el) {
            var info = infoKartu(el);
            var penulis = info.penulis || 'Humas JTEK';

            document.getElementById('viewJudul').textContent = info.judul;
            document.getElementById('viewKategori').textContent = (info.kategori || 'Berita Jurusan').toUpperCase();
            document.getElementById('viewPenulis').textContent = penulis;
            document.getElementById('viewAvatar').textContent = (penulis.charAt(0) || 'H').toUpperCase();

            var tglTxt = fmtHari(info.tanggal) ? (fmtHari(info.tanggal) + ', ' + fmtTgl(info.tanggal)) : fmtTgl(info.tanggal);
            document.getElementById('viewTanggal').textContent = tglTxt;
            document.getElementById('viewWaktuBaca').textContent = waktuBaca(info.isi);

            var tpl = el.querySelector('[data-isi]');
            document.getElementById('viewIsi').innerHTML = tpl ? tpl.innerHTML : '';

            var img = document.getElementById('viewImg');
            var imgEmpty = document.getElementById('viewImgEmpty');
            if (info.gambar && /^https?:\/\//i.test(info.gambar)) {
                img.src = info.gambar;
                img.classList.remove('hidden');
                imgEmpty.classList.add('hidden');
            } else {
                img.removeAttribute('src');
                img.classList.add('hidden');
                imgEmpty.classList.remove('hidden');
            }

            modal.classList.add('show');
        }

        grid.addEventListener('click', function (e) {
            var baca = e.target.closest('.btnBaca');
            if (baca) { openView(baca.closest('.news-card')); return; }
            var hapus = e.target.closest('.btnHapus');
            if (hapus) {
                if (!confirm('Hapus berita ini?')) return;
                var card = hapus.closest('.news-card');
                card.parentNode.removeChild(card);
                kartu = kartu.filter(function (c) { return c !== card; });
                render();
            }
        });

        if (leadCard) {
            leadCard.querySelector('.btnBaca').addEventListener('click', function () { openView(leadCard); });
        }

        modal.querySelectorAll('[data-modal-close]').forEach(function (c) {
            c.addEventListener('click', function () { modal.classList.remove('show'); });
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

        render(); // render awal
    })();
    </script>

</main>