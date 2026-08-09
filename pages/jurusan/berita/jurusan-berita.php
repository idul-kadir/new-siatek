<main class="content-area content-scroll">

    <style>
        .content-scroll { overflow-y: auto; min-height: 0; }
        .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
        .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
        .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
        .btn-circle:hover .tip { opacity: 1; visibility: visible; }

        /* ===== Cover berita: foto atau gradasi navy default ===== */
        .news-cover { position: relative; overflow: hidden; background: linear-gradient(150deg, #1e3a5f 0%, #2b4a73 55%, #3b5f8f 100%); }
        .news-cover::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(circle at 88% 12%, rgba(249,115,22,.18) 0, rgba(249,115,22,0) 42%); }
        .news-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .news-cover .news-cover-empty { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.55); font-size: 2.4rem; }

        .news-card { transition: all .2s ease; }
        .news-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -8px rgba(15,23,42,.14); }
        .news-card .news-actions { opacity: 0; transition: opacity .15s ease; }
        .news-card:hover .news-actions { opacity: 1; }
    </style>

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
            <button type="button" class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600" title="Tulis Berita Baru">
                <i class="fas fa-plus text-sm"></i>
                <span class="tip">Tulis Berita Baru</span>
            </button>
        </div>
    </section>

    <!-- ===== Toolbar (cari + filter tahun) ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative min-w-[200px] flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari judul, kategori, penulis…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option>2026</option>
                <option>2025</option>
                <option>2024</option>
            </select>
            <a href="jurusan-berita" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </section>

    <!-- ===== Berita Unggulan (terbaru) ===== -->
    <section class="mb-6">
        <article class="grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:grid-cols-2">
            <div class="news-cover min-h-[220px] md:min-h-[300px]">
                <img src="https://picsum.photos/seed/skpganjil/900/600" alt="Sosialisasi Penyusunan SKP" loading="lazy">
            </div>
            <div class="flex flex-col p-6 md:p-8">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-orange-700">
                        <i class="fas fa-fire"></i> Terbaru
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Pengumuman</span>
                    <span class="ml-auto text-xs font-medium text-slate-400">1 Agustus 2026</span>
                </div>
                <h2 class="text-lg font-bold leading-snug tracking-tight text-slate-800 md:text-2xl">
                    Sosialisasi Penyusunan SKP Semester Ganjil Tahun Akademik 2026/2027
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-500">
                    Seluruh dosen dan tenaga kependidikan diharapkan hadir pada kegiatan sosialisasi penyusunan SKP.
                    Acara dilaksanakan secara luring di ruang rapat jurusan dan daring melalui zoom.
                </p>
                <div class="mt-auto flex items-center justify-between pt-6">
                    <span class="text-xs text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas</span>
                    <button type="button" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">
                        Baca Selengkapnya
                    </button>
                </div>
            </div>
        </article>
    </section>

    <!-- ===== Grid Berita ===== -->
    <section>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <!-- Item 1 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://picsum.photos/seed/rapakreditasi/600/400" alt="Rapat Koordinasi Prodi" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Akademik</span>
                        <span class="font-medium text-slate-400">28 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Undangan Rapat Koordinasi Prodi Persiapan Akreditasi
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Rapat koordinasi prodi membahas persiapan akreditasi dan penyesuaian kurikulum. Seluruh ketua
                        prodi dan sekretaris prodi dimohon hadir tepat waktu.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Sekjur</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Item 2 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://siatek.web.id/adm/file/yudisium.jpg" alt="Jadwal Sidang Skripsi" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Akademik</span>
                        <span class="font-medium text-slate-400">25 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Jadwal Sidang Skripsi Periode Agustus 2026
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Jadwal sidang skripsi periode Agustus telah diterbitkan. Mahasiswa diharapkan memeriksa jadwal
                        dan memastikan seluruh berkas persyaratan telah lengkap.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Sekjur</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Item 3 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://picsum.photos/seed/robotnasional/600/400" alt="Juara 1 Kontes Robot Nasional" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Prestasi</span>
                        <span class="font-medium text-slate-400">20 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Mahasiswa Raih Juara 1 Kontes Robot Nasional 2026
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Tim robotika jurusan berhasil meraih juara 1 pada Kontes Robot Nasional 2026. Selamat kepada
                        seluruh anggota tim dan dosen pembimbing atas prestasi yang membanggakan.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Item 4 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://picsum.photos/seed/mbkmganjil/600/400" alt="Pendaftaran MBKM" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kemahasiswaan</span>
                        <span class="font-medium text-slate-400">15 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Pendaftaran Program MBKM Semester Ganjil Dibuka
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Pendaftaran program MBKM semester ganjil dibuka hingga akhir bulan. Mahasiswa dapat memilih
                        skema pertukaran pelajar, magang, atau proyek kemanusiaan.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Item 5 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://picsum.photos/seed/embeddedlab/600/400" alt="Pelatihan Embedded System" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Kegiatan</span>
                        <span class="font-medium text-slate-400">12 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Pelatihan Pemrograman Embedded System untuk Mahasiswa
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Workshop pemrograman embedded system menggunakan ESP32 akan diselenggarakan di Lab. Komputer 1.
                        Peserta terbatas dengan prioritas mahasiswa tingkat akhir.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Humas</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Item 6 -->
            <article class="news-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="news-cover aspect-[16/10]">
                    <img src="https://picsum.photos/seed/kipkuliah/600/400" alt="Beasiswa KIP Kuliah" loading="lazy">
                </div>
                <div class="flex flex-col p-4">
                    <div class="mb-2 flex items-center gap-2 text-[11px]">
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 font-semibold text-slate-600">Pengumuman</span>
                        <span class="font-medium text-slate-400">8 Juli 2026</span>
                    </div>
                    <h3 class="text-sm font-semibold leading-snug text-slate-800">
                        Pengumuman Beasiswa KIP Kuliah Tahap Kedua
                    </h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Penerimaan proposal Beasiswa KIP Kuliah tahap kedua dibuka. Persyaratan dan formulir dapat
                        diunduh pada bagian arsip jurusan.
                    </p>
                    <div class="mt-auto flex items-center justify-between pt-4">
                        <span class="text-[11px] text-slate-400"><i class="fas fa-user-pen mr-1"></i>Sekjur</span>
                        <div class="news-actions flex items-center gap-1.5">
                            <button type="button" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700"><i class="fas fa-eye text-xs"></i><span class="tip">Lihat</span></button>
                            <button type="button" class="btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600"><i class="fas fa-pen text-xs"></i><span class="tip">Edit</span></button>
                            <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"><i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span></button>
                        </div>
                    </div>
                </div>
            </article>

        </div>
    </section>

</main>
