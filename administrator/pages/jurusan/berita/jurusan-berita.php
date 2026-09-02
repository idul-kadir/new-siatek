<!--
  Halaman: Berita Jurusan (daftar + detail cepat).
  Data berita diambil dari tabel `berita` via AJAX (proses.php).
  JavaScript: pagination (12/halaman), cari/filter, modal detail, hapus via AJAX.
-->
<?php
require __DIR__ . '/../../../fungsi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// pastikan user sudah login
$sudahLogin = !empty($_SESSION['user_id']) || !empty($_SESSION['nip']) || !empty($_SESSION['username']);
if (!$sudahLogin) {
    header('Location: ' . base_url() . '/');
    exit;
}

?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }
    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle-lg { width: 2.5rem; height: 2.5rem; }
    .btn-circle .tip { position: absolute; bottom: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
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
                    <p class="text-xs text-slate-500">Kabar dan pengumuman jurusan.</p>
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
                <option>Berita Jurusan</option>
                <option>Akademik</option>
                <option>Prestasi</option>
                <option>Kemahasiswaan</option>
                <option>Kegiatan</option>
                <option>Pengumuman</option>
            </select>
            <a href="jurusan-berita" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </section>

    <!-- ===== Berita Unggulan (terbaru, AJAX-loaded) ===== -->
    <section class="mb-6" id="leadSection">
        <div id="leadContainer"></div>
    </section>

    <!-- ===== Grid Berita (AJAX-loaded dari database) ===== -->
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

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" id="newsGrid"></div>

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
    var BASE_URL = '<?= base_url() ?>';
    var PROSES_URL = BASE_URL + '/administrator/pages/jurusan/berita/proses.php';

    /* ===== Toast Notification (sesuai aturan php-native-crud-instruction) ===== */
    function toast(pesan, tipe) {
        tipe = tipe || 'info';
        var container = document.getElementById('toastContainer');
        if(!container){
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'pointer-events-none fixed bottom-6 right-6 z-[2000] flex flex-col gap-2';
            document.body.appendChild(container);
        }
        var item = document.createElement('div');
        item.className = 'toast-item toast-' + tipe + ' pointer-events-auto rounded-lg px-4 py-3 text-sm font-semibold text-white shadow-lg';
        var warna = tipe === 'success' ? 'bg-emerald-600' : (tipe === 'error' ? 'bg-rose-600' : 'bg-sky-600');
        item.classList.add(warna);
        var ikon = tipe === 'success' ? 'fa-check-circle' : (tipe === 'error' ? 'fa-times-circle' : 'fa-info-circle');
        item.innerHTML = '<i class="fas ' + ikon + ' mr-2"></i>' + pesan;
        container.appendChild(item);
        setTimeout(function(){ item.remove(); }, 3000);
    }

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
        // kartu dibaca ulang setiap kali dibutuhkan (grid diisi ulang via AJAX)
        function ambilKartu() {
            return Array.prototype.slice.call(document.querySelectorAll('#newsGrid .news-card'));
        }

        /* ===== Muat ulang berita dari database via AJAX ===== */
        function muatBerita() {
            $.ajax({
                url: PROSES_URL,
                method: 'POST',
                data: { 'ambil-tabel-berita': 1 },
                dataType: 'json',
                success: function(respon){
                    if(respon.status){
                        // kartu unggulan (berita terbaru)
                        var leadSection = document.getElementById('leadSection');
                        var leadContainer = document.getElementById('leadContainer');
                        if(leadContainer && leadSection){
                            leadContainer.innerHTML = respon.lead || '';
                            leadSection.style.display = respon.lead ? '' : 'none';
                        }
                        // grid kartu (sisanya)
                        grid.innerHTML = respon.data;
                        // update jumlah
                        if(jml) jml.textContent = respon.total;
                        if(badge) badge.textContent = respon.total;
                        render();
                    }
                },
                error: function(xhr){
                    toast('Terjadi kesalahan pada server', 'error');
                }
            });
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
            return Math.max(1, Math.ceil(ambilKartu().filter(cocok).length / ITEM_PER_PAGE));
        }

        function render() {
            var kartu = ambilKartu();
            var leadShown = !leadCard || cocok(leadCard);
            var cocokBaris = kartu.filter(cocok);

            // Kartu unggulan ditangani oleh muatBerita (di luar grid, tidak ikut paginasi)
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

            // Counter (hanya kartu grid; lead ditampilkan terpisah dan sudah dihitung respon.total)
            if (jml) jml.textContent = cocokBaris.length;
            if (badge) badge.textContent = cocokBaris.length;
            if (kosong) kosong.classList.toggle('hidden', cocokBaris.length > 0);

            // Pagination: sembunyikan kalau grid kosong
            var pager = document.getElementById('pager');
            if (pager) pager.classList.toggle('hidden', cocokBaris.length === 0);
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
        // ambil data kartu dari atribut data-* pada tombol .btnBaca + isi dari <template data-isi>
        function infoKartu(el) {
            var b = el.querySelector('.btnBaca');
            var t = el.querySelector('[data-isi]');
            return {
                judul: b ? (b.getAttribute('data-judul') || '') : '',
                kategori: b ? (b.getAttribute('data-kategori') || '') : '',
                penulis: b ? (b.getAttribute('data-penulis') || '') : '',
                tanggal: b ? (b.getAttribute('data-tanggal') || '') : '',
                gambar: b ? (b.getAttribute('data-gambar') || '') : '',
                isi: t ? (t.innerHTML || '') : ''
            };
        }

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
            if (info.gambar) {
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

        // Event delegation di DOCUMENT (bukan cuma #newsGrid) supaya lead card di luar grid juga kena
        document.addEventListener('click', function (e) {
            var baca = e.target.closest('.btnBaca');
            if (baca) {
                // cari parent news-card (bisa dari .news-card atau [data-lead])
                var cardBaca = baca.closest('.news-card, [data-lead]');
                if (cardBaca) openView(cardBaca);
                return;
            }

            var edit = e.target.closest('.btnCircleEdit');
            if (edit) {
                e.preventDefault();
                var idEdit = edit.getAttribute('data-id');
                // ambil data detail dari server lalu redirect ke tulis-berita dengan id
                $.ajax({
                    url: PROSES_URL,
                    method: 'POST',
                    data: { 'ambil-berita': 1, id: idEdit },
                    dataType: 'json',
                    success: function(respon){
                        if(respon.status){
                            // arahkan ke tulis-berita?edit=<id> — ditangani oleh halaman tulis-berita
                            location.href = 'tulis-berita?edit=' + idEdit;
                        } else {
                            toast(respon.pesan, 'error');
                        }
                    },
                    error: function(){ toast('Terjadi kesalahan pada server', 'error'); }
                });
                return;
            }

            var hapus = e.target.closest('.btnHapus');
            if (hapus) {
                var id = hapus.getAttribute('data-id');
                var card = hapus.closest('.news-card, [data-lead]');
                // judul bisa di <h3> (kartu) atau <h2> (lead) atau dari data-judul di tombol baca
                var judul = '';
                if (card) {
                    judul = (card.querySelector('h3') || card.querySelector('h2') || {}).textContent || '';
                }
                if (!judul) {
                    var btnB = hapus.parentElement && hapus.parentElement.querySelector('.btnBaca');
                    if (btnB) judul = btnB.getAttribute('data-judul') || '';
                }
                judul = judul || 'berita ini';
                if (typeof konfirmasiHapus === 'function') {
                    konfirmasiHapus(judul, function(){
                        $.ajax({
                            url: PROSES_URL,
                            method: 'POST',
                            data: { 'hapus-data-berita': 1, id: id },
                            dataType: 'json',
                            success: function(respon){
                                toast(respon.pesan, respon.status ? 'success' : 'error');
                                if(respon.status){ muatBerita(); }
                            },
                            error: function(){ toast('Terjadi kesalahan pada server', 'error'); }
                        });
                    });
                } else {
                    toast('Fungsi konfirmasi tidak tersedia', 'error');
                }
                return;
            }
        });

        // leadCard sudah dihapus - data dari AJAX

        modal.querySelectorAll('[data-modal-close]').forEach(function (c) {
            c.addEventListener('click', function () { modal.classList.remove('show'); });
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });

        // Load data berita dari database saat DOM siap (jQuery sudah ter-load saat ini)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', muatBerita);
        } else {
            muatBerita();
        }
    })();
    </script>

</main>
