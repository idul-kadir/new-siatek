<!--
  Halaman: Tulis Berita (editor dengan pratinjau live).
  DUMMY HTML MURNI — murni frontend (HTML/CSS/JS), TANPA logika simpan ke server.
  Tombol "Simpan Berita" hanya kembali ke daftar berita.
  Preview meniru halaman detail-berita publik:
    cover → kategori + judul → penulis + tanggal + estimasi baca → share → isi.
-->
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    /* Cover preview: default gradasi navy + highlight oranye halus */
    .news-cover { position: relative; overflow: hidden; background: linear-gradient(150deg, #1e3a5f 0%, #2b4a73 55%, #3b5f8f 100%); }
    .news-cover::after { content: ""; position: absolute; inset: 0; pointer-events: none; background: radial-gradient(circle at 88% 12%, rgba(249,115,22,.18) 0, rgba(249,115,22,0) 42%); }
    .news-cover img { width: 100%; height: 100%; object-fit: cover; display: block; }

    /* Panel preview: sticky HANYA di desktop (2 kolom), supaya tidak menutupi form di layar sempit */
    .preview-panel { border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; box-shadow: 0 1px 3px rgba(15,23,42,.06); }
    @media (min-width: 1024px) {
        .preview-panel { position: sticky; top: 16px; max-height: calc(100vh - 4rem); overflow-y: auto; }
    }

    /* Isi artikel di preview */
    .article-body p { margin: 0 0 1rem; line-height: 1.75; color: #334155; }
    .article-body p:empty { display: none; }

    .share-link { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; font-size: .8rem; }

    .empty-hint { color: #94a3b8; font-style: italic; }
</style>
<main class="content-area content-scroll">

    <!-- ===== Page Header ===== -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="jurusan-berita" class="btn-circle bg-slate-900 text-white shadow-sm hover:bg-slate-700">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span class="tip">Kembali ke daftar berita</span>
                </a>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-pen-nib"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Tulis Berita</h1>
                    <p class="text-xs text-slate-500">Tulis berita langsung dengan pratinjau — dummy (belum tersimpan ke database).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnPreviewTab" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200 lg:hidden">
                    <i class="fas fa-eye mr-1"></i>Lihat Preview
                </button>
                <button type="button" id="btnSimpan" class="rounded-lg bg-orange-500 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-600">
                    <i class="fas fa-check mr-1"></i>Simpan Berita
                </button>
            </div>
        </div>
    </section>

    <!-- ===== Editor: form kiri + preview kanan ===== -->
    <section class="grid gap-5 lg:grid-cols-2">

        <!-- ============ FORM ============ -->
        <div id="paneTulis">
            <form id="beritaForm" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" onsubmit="return false;">

                <div class="grid gap-4">
                    <div>
                        <label for="fJudul" class="mb-1 block text-xs font-semibold text-slate-600">Judul Berita <span class="text-rose-500">*</span></label>
                        <input type="text" id="fJudul" required
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
                               placeholder="Contoh: Mahasiswa Raih Juara 1 Kontes Robot Nasional 2026">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="fKategori" class="mb-1 block text-xs font-semibold text-slate-600">Kategori</label>
                            <select id="fKategori"
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                                <option>Berita Jurusan</option>
                                <option>Akademik</option>
                                <option>Prestasi</option>
                                <option>Kemahasiswaan</option>
                                <option>Kegiatan</option>
                                <option>Pengumuman</option>
                            </select>
                        </div>
                        <div>
                            <label for="fTanggal" class="mb-1 block text-xs font-semibold text-slate-600">Tanggal</label>
                            <input type="date" id="fTanggal"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="fPenulis" class="mb-1 block text-xs font-semibold text-slate-600">Penulis</label>
                            <input type="text" id="fPenulis" value="Humas JTEK"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                        </div>
                        <div>
                            <label for="fSumber" class="mb-1 block text-xs font-semibold text-slate-600">Sumber</label>
                            <input type="text" id="fSumber"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
                                   placeholder="opsional">
                        </div>
                    </div>

                    <div>
                        <label for="fDeskripsi" class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi Ringkas</label>
                        <input type="text" id="fDeskripsi"
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
                               placeholder="Ringkasan singkat untuk daftar berita">
                    </div>

                    <div>
                        <label for="fGambarUrl" class="mb-1 block text-xs font-semibold text-slate-600">URL Gambar</label>
                        <input type="url" id="fGambarUrl"
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
                               placeholder="https://…">
                    </div>

                    <div>
                        <label for="fGambarFile" class="mb-1 block text-xs font-semibold text-slate-600">Atau Unggah Gambar</label>
                        <input type="file" id="fGambarFile" accept="image/*"
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white file:mr-3 file:rounded-md file:border-0 file:bg-orange-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-orange-600">
                        <p class="mt-1 text-[11px] text-slate-400">Dummy: hanya untuk pratinjau, tidak benar-benar diunggah.</p>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <label for="fIsi" class="block text-xs font-semibold text-slate-600">Isi Berita <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-slate-400">Paragraf baru: gunakan <b>||</b></span>
                        </div>
                        <textarea id="fIsi" rows="16" required
                                  class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm leading-relaxed outline-none focus:border-orange-400 focus:bg-white"
                                  placeholder="Tulis isi berita di sini…"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- ============ PREVIEW (detail template) ============ -->
        <div id="panePreview" class="preview-panel">
            <!-- Header kecil -->
            <div class="flex items-center gap-1.5 border-b border-slate-100 px-5 py-3 text-[11px] text-slate-400">
                <i class="fas fa-eye text-orange-500"></i> Pratinjau — detail berita
                <span class="ml-auto hidden items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 md:inline-flex">
                    <i class="fas fa-circle text-[6px]"></i> Update otomatis
                </span>
            </div>
            <div class="p-5 md:p-6">

                <!-- Breadcrumb -->
                <nav class="mb-4 text-xs text-slate-400">
                    Beranda <i class="fas fa-chevron-right mx-1 text-[9px]"></i> Berita <i class="fas fa-chevron-right mx-1 text-[9px]"></i>
                    <span class="font-semibold text-slate-600">Detail</span>
                </nav>

                <!-- Cover + badge -->
                <div class="news-cover h-48 rounded-xl md:h-64">
                    <img id="pvCoverImg" src="" alt="" loading="lazy" class="hidden">
                    <div class="flex h-full w-full items-center justify-center">
                        <span class="text-5xl text-white/40"><i class="fas fa-newspaper"></i></span>
                        <span class="absolute left-3 top-3 rounded-full bg-slate-900/70 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white"><span id="pvTahun">2026</span></span>
                        <span class="absolute right-3 top-3 rounded-full bg-orange-500 px-2.5 py-1 text-[10px] font-semibold text-white"><span id="pvKategori">Berita Jurusan</span></span>
                    </div>
                </div>

                <!-- Judul -->
                <h1 id="pvJudul" class="mt-5 text-xl font-bold leading-snug tracking-tight text-slate-800 md:text-2xl">Judul berita akan tampil di sini…</h1>

                <!-- Meta penulis -->
                <div class="mt-4 flex items-center gap-3 border-b border-slate-100 pb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-sm font-bold text-white"><span id="pvInisial">H</span></span>
                    <div>
                        <p class="text-sm font-semibold text-slate-700"><span id="pvPenulis">Humas JTEK</span></p>
                        <p class="text-xs text-slate-400"><span id="pvTanggal">Senin, 10 Agustus 2026</span> • <span id="pvWaktuBaca">1</span> menit baca</p>
                    </div>
                </div>

                <!-- Share -->
                <div class="flex items-center gap-1.5 py-4">
                    <span class="mr-1 text-xs font-semibold text-slate-500">Bagikan:</span>
                    <span class="share-link bg-[#25D366] text-white"><i class="fab fa-whatsapp"></i></span>
                    <span class="share-link bg-[#1877F2] text-white"><i class="fab fa-facebook-f"></i></span>
                    <span class="share-link bg-slate-900 text-white"><i class="fab fa-x-twitter"></i></span>
                    <span class="share-link bg-slate-200 text-slate-600"><i class="fas fa-link"></i></span>
                </div>

                <!-- Isi artikel -->
                <div class="article-body" id="pvIsi"></div>

            </div>
        </div>

    </section>

    <!-- Toast kecil (dummy simpan) -->
    <div id="savedToast" class="pointer-events-none fixed bottom-6 left-1/2 z-[2000] -translate-x-1/2 translate-y-16 rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white opacity-0 shadow-lg transition-all duration-300"></div>

    <script>
    (function () {
        'use strict';

        var $id = function (x) { return document.getElementById(x); };
        var formVal = function (field) { var el = $id(field); return el ? el.value : ''; };

        /* Isi awal: tanggal hari ini */
        (function () {
            var d = new Date();
            var pad = function (n) { return (n < 10 ? '0' : '') + n; };
            var t = $id('fTanggal');
            if (t) t.value = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
        })();

        /* ===== Teks → HTML: paragraf dipisah dengan "||" ===== */
        function esc(t) {
            return (t || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        function mdToHtml(src) {
            var parts = (src || '').split('||');
            var html = '';
            for (var i = 0; i < parts.length; i++) {
                var p = parts[i].replace(/\s*\n\s*/g, ' ').replace(/\s{2,}/g, ' ').trim();
                if (p) html += '<p>' + esc(p) + '</p>';
            }
            return html || '<p class="empty-hint">Isi artikel akan dirender di sini secara langsung saat Anda mengetik…</p>';
        }

        /* ===== Format tanggal: YYYY-MM-DD → Senin, 10 Agustus 2026 ===== */
        function fmtTgl(v) {
            if (!v) return '';
            var p = v.split('-');
            if (p.length !== 3) return v;
            var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            var hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            var d = new Date(+p[0], +p[1] - 1, +p[2]);
            return (hari[d.getDay()] + ', ' + (+p[2]) + ' ' + bulan[+p[1] - 1] + ' ' + p[0]);
        }

        /* ===== Estimasi baca (180 kata/menit) ===== */
        function waktuBaca(src) {
            var w = (src || '').trim().split(/\s+/).filter(Boolean).length;
            return Math.max(1, Math.round(w / 180));
        }

        /* ===== Gambar preview ===== */
        function setCover(url) {
            var img = $id('pvCoverImg');
            if (!img) return;
            if (url) {
                img.src = url; img.classList.remove('hidden');
            } else {
                img.removeAttribute('src'); img.classList.add('hidden');
            }
        }

        /* ===== Render semua ===== */
        function render() {
            var judul = formVal('fJudul'), kategori = formVal('fKategori') || 'Berita Jurusan';
            var tgl = formVal('fTanggal') || '2026-08-10';
            var penulis = formVal('fPenulis') || 'Humas JTEK';
            var isi = $id('fIsi') ? $id('fIsi').value : '';
            var coverUrl = formVal('fGambarUrl');

            // file terpilih lewat input file → preview nama (tidak bisa dibuka utk file lokal)
            var f = $id('fGambarFile');
            if (!coverUrl && f && f.files && f.files[0]) {
                coverUrl = URL.createObjectURL(f.files[0]);
            }

            if ($id('pvJudul'))      $id('pvJudul').textContent = judul || 'Judul berita akan tampil di sini…';
            if ($id('pvKategori'))   $id('pvKategori').textContent = kategori;
            if ($id('pvTahun'))      $id('pvTahun').textContent = tgl.slice(0, 4);
            if ($id('pvPenulis'))    $id('pvPenulis').textContent = penulis;
            if ($id('pvInisial'))    $id('pvInisial').textContent = (penulis.charAt(0) || 'H').toUpperCase();
            if ($id('pvTanggal'))    $id('pvTanggal').textContent = fmtTgl(tgl);
            if ($id('pvWaktuBaca'))  $id('pvWaktuBaca').textContent = waktuBaca(isi);
            if ($id('pvIsi'))        $id('pvIsi').innerHTML = mdToHtml(isi);
            setCover(coverUrl);
        }

        /* ===== Bind event ===== */
        ['fJudul','fKategori','fTanggal','fPenulis','fSumber','fDeskripsi','fGambarUrl'].forEach(function (id) {
            var el = $id(id);
            if (el) el.addEventListener('input', render);
        });
        var txt = $id('fIsi');
        if (txt) txt.addEventListener('input', render);
        var file = $id('fGambarFile');
        if (file) file.addEventListener('change', render);

        /* ===== Dummy simpan: toast lalu kembali ke daftar ===== */
        var toast = $id('savedToast');
        $id('btnSimpan').addEventListener('click', function () {
            toast.textContent = 'Berita disimpan (dummy) ✓';
            toast.classList.remove('translate-y-16', 'opacity-0');
            setTimeout(function () { toast.classList.add('translate-y-16', 'opacity-0'); }, 1200);
            setTimeout(function () { location.href = 'jurusan-berita'; }, 1500);
        });

        /* ===== Toggle mobile: tulis <-> preview ===== */
        var btnPrev = $id('btnPreviewTab');
        if (btnPrev) btnPrev.addEventListener('click', function () {
            var t = $id('paneTulis'), p = $id('panePreview');
            var showingPrev = !p.classList.contains('hidden');
            p.classList.toggle('hidden', showingPrev);
            t.classList.toggle('hidden', !showingPrev);
            btnPrev.innerHTML = showingPrev
                ? '<i class="fas fa-pen mr-1"></i>Kembali Menulis'
                : '<i class="fas fa-eye mr-1"></i>Lihat Preview';
        });

        render(); // render awal
    })();
    </script>

</main>