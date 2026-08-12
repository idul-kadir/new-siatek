<?php
/**
 * Halaman: Data Tridharma
 * Data dummy murni HTML (bukan PHP/JS). JS hanya untuk: tab, cari, filter tahun, pagination.
 * Model data: 1 data = 1 ketua + banyak anggota, dan 1 data = banyak file.
 * Struktur meniru tabel: arsip_pendidikan, arsip_penelitian, arsip_pengabdian, arsip_penunjang.
 */
?>
<style>
    .content-scroll { overflow-y: auto; min-height: 0; }

    .tile-orange  { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .tile-sky     { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
    .tile-emerald { background: linear-gradient(135deg, #047857 0%, #10b981 100%); }
    .tile-violet  { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }
    .tile-corak { position: relative; overflow: hidden; }
    .tile-corak::before { content: ""; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 12px 12px; opacity: .35; mix-blend-mode: overlay; }
    .tile-corak > * { position: relative; }

    .btn-circle { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 9999px; transition: all .15s ease; }
    .btn-circle .tip { position: absolute; top: 115%; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity .15s ease; z-index: 40; pointer-events: none; box-shadow: 0 2px 6px rgba(15,23,42,.25); }
    .btn-circle:hover .tip { opacity: 1; visibility: visible; }

    .chip-ketua { display: inline-flex; align-items: center; gap: .3rem; border-radius: .375rem; background: #1e3a5f; color: #fff; padding: 3px 8px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .chip-anggota { display: inline-flex; align-items: center; border-radius: .375rem; background: #f1f5f9; color: #475569; padding: 3px 8px; font-size: 11px; white-space: nowrap; }
    .chip-more { display: inline-flex; align-items: center; border: 0; border-radius: .375rem; background: #e2e8f0; color: #334155; padding: 3px 9px; font-size: 11px; font-weight: 600; cursor: pointer; transition: background .15s ease; }
    .chip-more:hover { background: #cbd5e1; }
    .team-popover { position: fixed; z-index: 50; max-width: 340px; border: 1px solid #e2e8f0; border-radius: .625rem; background: #fff; box-shadow: 0 10px 25px rgba(15,23,42,.14); padding: .5rem .625rem .625rem; display: none; }
    .team-popover.show { display: block; }
    .team-popover .pop-title { margin-bottom: .4rem; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; }
    .team-popover .chip-anggota { display: inline-flex; margin: 0 .25rem .25rem 0; }

    .tab-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: .6rem; border: 1px solid #e2e8f0;
        background: #fff; color: #475569; padding: .55rem .9rem; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .tab-btn:hover { border-color: #fdba74; color: #c2410c; }
    .tab-btn .tdot { width: 8px; height: 8px; border-radius: 9999px; }
    .tab-btn .tnum { min-width: 22px; text-align: center; border-radius: 9999px; padding: 1px 6px; font-size: .7rem; background: #f1f5f9; color: #64748b; }
    .tab-btn[aria-selected="true"] { background: #1e3a5f; border-color: #1e3a5f; color: #fff; }
    .tab-btn[aria-selected="true"] .tnum { background: rgba(255,255,255,.18); color: #fff; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .pg-btn { min-width: 2rem; height: 2rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #475569;
        font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s ease; }
    .pg-btn:hover { border-color: #fdba74; color: #c2410c; }
    .pg-btn:disabled { opacity: .45; cursor: not-allowed; }
</style>
<main class="content-area content-scroll">

    <!-- Page Header -->
    <section class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                    <i class="fas fa-ellipsis-h"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-800">Data Tridharma</h1>
                    <p class="text-xs text-slate-500">Pangkalan data tridharma jurusan. Satu data dapat dimiliki 1 ketua + beberapa anggota dan memiliki banyak file — dummy (HTML).</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik (klik = pindah tab) -->
    <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <button type="button" data-tab="pendidikan" class="tile-stat tile-orange tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-orange-500/25 cursor-pointer">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pendidikan</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">12</p>
                    <p class="mt-2 text-[11px] text-white/70">arsip pendidikan</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-graduation-cap"></i></span>
            </div>
        </button>
        <button type="button" data-tab="penelitian" class="tile-stat tile-sky tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-sky-500/25 cursor-pointer" style="animation-delay:.05s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Penelitian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">14</p>
                    <p class="mt-2 text-[11px] text-white/70">arsip penelitian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-flask"></i></span>
            </div>
        </button>
        <button type="button" data-tab="pengabdian" class="tile-stat tile-emerald tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-emerald-500/25 cursor-pointer" style="animation-delay:.10s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengabdian</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">13</p>
                    <p class="mt-2 text-[11px] text-white/70">arsip pengabdian</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-hands-helping"></i></span>
            </div>
        </button>
        <button type="button" data-tab="penunjang" class="tile-stat tile-violet tile-corak reveal rounded-xl p-4 text-left text-white shadow-md shadow-violet-500/25 cursor-pointer" style="animation-delay:.15s">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-white/85">Penunjang</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight">8</p>
                    <p class="mt-2 text-[11px] text-white/70">arsip penunjang</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i class="fas fa-award"></i></span>
            </div>
        </button>
    </section>

    <!-- Tab Pilar -->
    <section class="mb-4">
        <div class="flex flex-wrap gap-2">
            <button type="button" class="tab-btn" data-tab="pendidikan" aria-selected="true"><span class="tdot bg-orange-500"></span>Pendidikan<span class="tnum" id="cnt-pendidikan">12</span></button>
            <button type="button" class="tab-btn" data-tab="penelitian" aria-selected="false"><span class="tdot bg-sky-500"></span>Penelitian<span class="tnum" id="cnt-penelitian">14</span></button>
            <button type="button" class="tab-btn" data-tab="pengabdian" aria-selected="false"><span class="tdot bg-emerald-500"></span>Pengabdian<span class="tnum" id="cnt-pengabdian">13</span></button>
            <button type="button" class="tab-btn" data-tab="penunjang" aria-selected="false"><span class="tdot bg-violet-500"></span>Penunjang<span class="tnum" id="cnt-penunjang">8</span></button>
            <button type="button" id="btnResetTab" class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"><i class="fas fa-rotate"></i> Reset</button>
        </div>
    </section>

    <!-- Toolbar (cari + filter tahun) -->
    <section class="mb-4">
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input type="search" id="fCari" placeholder="Cari judul, ketua, anggota, jurusan…"
                       class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
            </div>
            <select id="fTahun" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
                <option value="">Semua Tahun</option>
                <option value="2026">2026</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
                <option value="2019">2019</option>
                <option value="2018">2018</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
            </select>
            <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
        </div>
    </section>

    <!-- Tabel Data per Pilar -->
    <section>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800" id="judulTabel">Arsip Pendidikan</h2>
                    <p class="mt-0.5 text-xs text-slate-500"><span id="jmlData">0</span> data ditampilkan</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-database"></i> <span id="badgeData">0</span> total
                </span>
            </div>

            <div class="max-h-[560px] overflow-auto">
                <!-- PANEL: PENDIDIKAN -->
                <div class="tab-panel active" id="panel-pendidikan">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                            <tr>
                                <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Deskripsi</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Semester</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Diunggah oleh</th>
                                <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-cari="sk dosen penerima pnbp 2022 yasin mohamad">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">1</span><p class="font-semibold text-slate-800 leading-snug">SK Dosen Penerima PNBP 2022</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2022/2023 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Yasin Mohamad, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-pnbp-2022.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">lampiran.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-cari="dosen pembimbing skripsi mahasiswa semester genap ta 2021 2022 dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">2</span><p class="font-semibold text-slate-800 leading-snug">Dosen Pembimbing Skripsi Mahasiswa Semester Genap TA 2021/2022</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2021/2022 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-pembimbing.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">daftar-bimbingan.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2019" data-cari="model sistem pengamanan rumah otomatis menggunakan sensor pir hc sr501 dan sim 800l berbasis mikrokontroler arduino mega dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">3</span><p class="font-semibold text-slate-800 leading-snug">Model Sistem Pengamanan Rumah Otomatis Menggunakan Sensor PIR HC-SR501 dan SIM 800L Berbasis Mikrokontroler Arduino Mega</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2019</span></td>
                                <td class="px-4 py-4 text-slate-600">2019/2020 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">judul-ta.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">abstrak.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2019" data-cari="network attached storage nas dan web server menggunakan raspberry pi dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">4</span><p class="font-semibold text-slate-800 leading-snug">Network Attached Storage (NAS) dan Web Server Menggunakan Raspberry Pi</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2019</span></td>
                                <td class="px-4 py-4 text-slate-600">2018/2019 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">judul-ta.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-ta.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-cari="network attached storage nas dan web server menggunakan raspberry pi 3 dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">5</span><p class="font-semibold text-slate-800 leading-snug">Network Attached Storage (NAS) dan Web Server Menggunakan Raspberry Pi 3</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                                <td class="px-4 py-4 text-slate-600">2019/2020 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">judul-ta.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-ta.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2019" data-cari="optimalisasi pemetaan kemiskinan di kota gorontalo melalui pemanfaatan sistem informasi geografis dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">6</span><p class="font-semibold text-slate-800 leading-snug">Optimalisasi Pemetaan Kemiskinan di Kota Gorontalo Melalui Pemanfaatan Sistem Informasi Geografis</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2019</span></td>
                                <td class="px-4 py-4 text-slate-600">2018/2019 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">judul-ta.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">peta-gis.zip</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-cari="penasehat akademik semester genap 2021 2022 dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">7</span><p class="font-semibold text-slate-800 leading-snug">Penasehat Akademik Semester Genap 2021/2022</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2021/2022 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-penasehat.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">daftar-pa.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2021" data-cari="penasehat akademik ta 2020 2021 genap dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">8</span><p class="font-semibold text-slate-800 leading-snug">Penasehat Akademik TA 2020-2021 Genap</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4 text-slate-600">2020/2021 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-penasehat.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">daftar-pa.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2022" data-cari="sk beban mengajar dosen gasal ta 2022 2023 wrastawa ridwan">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">9</span><p class="font-semibold text-slate-800 leading-snug">SK Beban Mengajar Dosen Gasal TA 2022/2023</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2022/2023 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Wrastawa Ridwan, ST., MT., MCE, CDSEA</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-beban-mengajar.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-excel text-xs"></i><span class="tip">rekap-beban.xlsx</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2021" data-cari="berita acara ujian proposal skripsi mahasiswa dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">10</span><p class="font-semibold text-slate-800 leading-snug">Berita Acara Ujian Proposal Skripsi Mahasiswa</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4 text-slate-600">2021/2022 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">ba-proposal.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">berita-acara.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-orange-50" data-tahun="2020" data-cari="sk pengangkatan pembimbing tugas akhir mahasiswa dari sister">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">11</span><p class="font-semibold text-slate-800 leading-snug">SK Pengangkatan Pembimbing Tugas Akhir Mahasiswa</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                                <td class="px-4 py-4 text-slate-600">2020/2021 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">DARI SISTER</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-pembimbing-ta.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">lampiran.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-orange-50" data-tahun="2022" data-cari="laporan monitoring dan evaluasi perkuliahan semester genap yasin mohamad">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-sm font-bold text-white shadow-sm">12</span><p class="font-semibold text-slate-800 leading-snug">Laporan Monitoring dan Evaluasi Perkuliahan Semester Genap</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2021/2022 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">Yasin Mohamad, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-monev.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-excel text-xs"></i><span class="tip">rekap-monev.xlsx</span></a></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- PANEL: PENELITIAN -->
                <div class="tab-panel" id="panel-penelitian">
                    <table class="w-full min-w-[960px] text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                            <tr>
                                <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Judul / Deskripsi</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tim</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Sumber Dana</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Anggaran</th>
                                <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2017" data-cari="pengembangan algoritma optimasi koloni semut dan neuro fuzzy pada penjejakan multi target ifan wiranto yasin mohamad salmawaty tansa mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">1</span><p class="font-semibold text-slate-800 leading-snug">Pengembangan Algoritma Optimasi Koloni Semut dan Neuro-Fuzzy pada Penjejakan Multi Target</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2017</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ifan Wiranto, ST., MT.</span><span class="chip-anggota">Yasin Mohamad</span><span class="chip-anggota">Salmawaty Tansa</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">artikel-jurnal.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2017" data-cari="aplikasi jaringan sensor nirkabel dalam perancangan prototipe otomatisasi penerangan rumah wrastawa ridwan ervan hasan harun ifan wiranto mandiri 75000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">2</span><p class="font-semibold text-slate-800 leading-snug">Aplikasi Jaringan Sensor Nirkabel dalam Perancangan Prototipe Otomatisasi Penerangan Rumah</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2017</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Wrastawa Ridwan</span><span class="chip-anggota">Ervan Hasan Harun</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp75.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-haki.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2021" data-cari="rancang bangun prototipe sistem informasi destinasi wisata budaya berbasis kalender musim gorontalo tahun ketiga amirudin yunus dako yasin mohamad ervan hasan harun dalam negeri 150000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">3</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Prototipe Sistem Informasi Destinasi Wisata Budaya Berbasis Kalender Musim Gorontalo (Tahun Ketiga)</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Dalam Negeri</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp150.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-tahun-3.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">artikel.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2020" data-cari="rancang bangun prototipe sistem informasi destinasi wisata budaya berbasis kalender musim gorontalo tahun kedua amirudin yunus dako yasin mohamad ervan hasan harun dalam negeri 150000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">4</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Prototipe Sistem Informasi Destinasi Wisata Budaya Berbasis Kalender Musim Gorontalo (Tahun Kedua)</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Dalam Negeri</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp150.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-tahun-2.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">proposal.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2019" data-cari="rancang bangun prototipe sistem informasi destinasi wisata budaya berbasis kalender musim gorontalo amirudin yunus dako yasin mohamad dalam negeri 150000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">5</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Prototipe Sistem Informasi Destinasi Wisata Budaya Berbasis Kalender Musim Gorontalo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2019</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Yasin Mohamad</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Dalam Negeri</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp150.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-tahun-1.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">proposal.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2017" data-cari="rancang bangun prototipe sistem informasi kalender musim berbasis kearifan lokal masyarakat gorontalo amirudin yunus dako ifan wiranto mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">6</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Prototipe Sistem Informasi Kalender Musim Berbasis Kearifan Lokal Masyarakat Gorontalo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2017</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">publikasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2016" data-cari="rancang bangun prototipe sistem informasi kalender musim berbasis kearifan lokal masyarakat gorontalo amirudin yunus dako ifan wiranto mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">7</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Prototipe Sistem Informasi Kalender Musim Berbasis Kearifan Lokal Masyarakat Gorontalo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2016</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">artikel.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2023" data-cari="sistem monitoring kualitas udara berbasis iot di kawasan pesisir teluk gorontalo ervan hasan harun wrastawa ridwan salmawaty tansa yasin mohamad ifan wiranto zainudin bonok amirudin yunus dako lanto kamil amali andi pratama nurul fadhilah rizky ramadhan sri wahyuni dian kartika luar pt 120000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">8</span><p class="font-semibold text-slate-800 leading-snug">Sistem Monitoring Kualitas Udara Berbasis IoT di Kawasan Pesisir Teluk Gorontalo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4">
                                    <div class="flex max-w-[240px] flex-wrap items-center gap-1.5">
                                        <span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ervan Hasan Harun</span>
                                        <span class="chip-anggota">Wrastawa Ridwan</span>
                                        <span class="chip-anggota">Salmawaty Tansa</span>
                                        <span class="chip-anggota">Yasin Mohamad</span>
                                        <span class="chip-anggota">Ifan Wiranto</span>
                                        <span class="chip-anggota">Zainudin Bonok</span>
                                        <span class="chip-anggota">Amirudin Yunus Dako</span>
                                        <span class="chip-anggota">Dr. Lanto M. Kamil Amali</span>
                                        <span class="chip-anggota">Andi Pratama</span>
                                        <span class="chip-anggota">Nurul Fadhilah</span>
                                        <span class="chip-anggota">Rizky Ramadhan</span>
                                        <span class="chip-anggota">Sri Wahyuni</span>
                                        <span class="chip-anggota">Dian Kartika</span>
                                    </div>
                                    </td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Luar PT</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp120.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-excel text-xs"></i><span class="tip">data-hasil.xlsx</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2022" data-cari="deteksi dini kecurangan akademik menggunakan metode machine learning lanto kamil amali ifan wiranto yasin mohamad mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">9</span><p class="font-semibold text-slate-800 leading-snug">Deteksi Dini Kecurangan Akademik Menggunakan Metode Machine Learning</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Dr. Lanto M. Kamil Amali</span><span class="chip-anggota">Ifan Wiranto</span><span class="chip-anggota">Yasin Mohamad</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">artikel.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2021" data-cari="optimasi penjadwalan perkuliahan menggunakan algoritma genetika zainudin bonok wrastawa ridwan luar pt 85000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">10</span><p class="font-semibold text-slate-800 leading-snug">Optimasi Penjadwalan Perkuliahan Menggunakan Algoritma Genetika</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Zainudin Bonok</span><span class="chip-anggota">Wrastawa Ridwan</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Luar PT</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp85.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">prosiding.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2020" data-cari="pemodelan numerik distribusi tegangan pada jaringan distribusi cerdas salmawaty tansa ervan hasan harun mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">11</span><p class="font-semibold text-slate-800 leading-snug">Pemodelan Numerik Distribusi Tegangan pada Jaringan Distribusi Cerdas</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Salmawaty Tansa</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-penelitian.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2018" data-cari="sistem pakar diagnosa gangguan jaringan komputer berbasis web yasin mohamad ifan wiranto mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">12</span><p class="font-semibold text-slate-800 leading-snug">Sistem Pakar Diagnosa Gangguan Jaringan Komputer Berbasis Web</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2018</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Yasin Mohamad</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-penelitian.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-sky-50" data-tahun="2019" data-cari="rancang bangun alat pendeteksi kebocoran gas lpg berbasis iot ervan hasan harun salmawaty tansa amirudin yunus dako luar pt 50000000">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">13</span><p class="font-semibold text-slate-800 leading-snug">Rancang Bangun Alat Pendeteksi Kebocoran Gas LPG Berbasis IoT</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2019</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ervan Hasan Harun</span><span class="chip-anggota">Salmawaty Tansa</span><span class="chip-anggota">Amirudin Yunus Dako</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Luar PT</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp50.000.000</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-penelitian.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-sky-50" data-tahun="2023" data-cari="penerapan teknologi blockchain untuk keamanan arsip akademik lanto kamil amali yasin mohamad ervan hasan harun mandiri">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white shadow-sm">14</span><p class="font-semibold text-slate-800 leading-snug">Penerapan Teknologi Blockchain untuk Keamanan Arsip Akademik</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[240px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Dr. Lanto M. Kamil Amali</span><span class="chip-anggota">Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">artikel.pdf</span></a></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- PANEL: PENGABDIAN -->
                <div class="tab-panel" id="panel-pengabdian">
                    <table class="w-full min-w-[1020px] text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                            <tr>
                                <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Judul / Deskripsi</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tim</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Sumber Dana</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Anggaran</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Jurusan</th>
                                <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2022" data-cari="sosialisasi penggunaan kwh meter jenis digital dan analog pada masyarakat di desa dutohe barat kab bone bolango yasin mohamad wrastawa ridwan ifan wiranto mandiri teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">1</span><p class="font-semibold text-slate-800 leading-snug">Sosialisasi Penggunaan KWh Meter Jenis Digital dan Analog pada Masyarakat di Desa Dutohe Barat Kab. Bone Bolango</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Yasin Mohamad</span><span class="chip-anggota">Wrastawa Ridwan</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-archive text-xs"></i><span class="tip">foto-kegiatan.zip</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2021" data-cari="pengelolaan bumdes di desa pinomon tiga berbasis web yasin mohamad ervan hasan harun mandiri 7000000 teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">2</span><p class="font-semibold text-slate-800 leading-snug">Pengelolaan BUMDes di Desa Pinomon Tiga Berbasis Web</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp7.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2016" data-cari="pendampingan teknisi listrik instalasi penerangan dasar bagi karang taruna dan masyarakat pengangguran di desa alata karya kec kwandang kab gorontalo utara ervan hasan harun yasin mohamad mandiri 5000000 teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">3</span><p class="font-semibold text-slate-800 leading-snug">Pendampingan Teknisi Listrik (Instalasi Penerangan Dasar) bagi Karang Taruna dan Masyarakat Pengangguran di Desa Alata Karya Kec. Kwandang Kab. Gorontalo Utara</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2016</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ervan Hasan Harun</span><span class="chip-anggota">Yasin Mohamad</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp5.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2021" data-cari="pengelolaan bumdes di desa pinomontiga berbasis web yasin mohamad ervan hasan harun mandiri 7000000 teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">4</span><p class="font-semibold text-slate-800 leading-snug">Pengelolaan BUMDes di Desa Pinomontiga Berbasis Web</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp7.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2021" data-cari="pengelolaan potensi desa dengan pemanfaatan tik dan transformasi digital untuk peningkatan umkm dan bumdes desa botutonuo kec kabila bone kab bone bolango zainudin bonok yasin mohamad ervan hasan harun mandiri 7000000 teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">5</span><p class="font-semibold text-slate-800 leading-snug">Pengelolaan Potensi Desa dengan Pemanfaatan TIK dan Transformasi Digital untuk Peningkatan UMKM dan BUMDes Desa Botutonuo Kec. Kabila Bone Kab. Bone Bolango</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Zainudin Bonok</span><span class="chip-anggota">Yasin Mohamad</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp7.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">luaran.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2018" data-cari="optimalisasi dan penguatan desa tangguh bencana melalui pemberdayaan sampah organik dan anorganik menjadi kompos dan bbm di kec dulupi dan botumoito kab boalemo salmawaty tansa wrastawa ridwan mandiri 25000000 teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">6</span><p class="font-semibold text-slate-800 leading-snug">Optimalisasi dan Penguatan Desa Tangguh Bencana Melalui Pemberdayaan Sampah Organik dan Anorganik Menjadi Kompos dan BBM di Kec. Dulupi dan Botumoito Kab. Boalemo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2018</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Salmawaty Tansa</span><span class="chip-anggota">Wrastawa Ridwan</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp25.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2023" data-cari="pelatihan instalasi listrik rumah tangga bagi remaja di desa molamahu wrastawa ridwan salmawaty tansa luar pt 50000000 teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">7</span><p class="font-semibold text-slate-800 leading-snug">Pelatihan Instalasi Listrik Rumah Tangga bagi Remaja di Desa Molamahu</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Wrastawa Ridwan</span><span class="chip-anggota">Salmawaty Tansa</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Luar PT</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp50.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2023" data-cari="bimbingan teknis digitalisasi administrasi desa untuk aparatur kec telaga ifan wiranto yasin mohamad mandiri teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">8</span><p class="font-semibold text-slate-800 leading-snug">Bimbingan Teknis Digitalisasi Administrasi Desa untuk Aparatur Kec. Telaga</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ifan Wiranto</span><span class="chip-anggota">Yasin Mohamad</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">materi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2022" data-cari="pemanfaatan iot untuk penerangan jalan umum tenaga surya di desa bongo amirudin yunus dako ervan hasan harun dalam negeri 120000000 teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">9</span><p class="font-semibold text-slate-800 leading-snug">Pemanfaatan IoT untuk Penerangan Jalan Umum Tenaga Surya di Desa Bongo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Amirudin Yunus Dako</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Dalam Negeri</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp120.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2021" data-cari="sosialisasi dan pelatihan e commerce bagi umkm di kota gorontalo lanto kamil amali ifan wiranto mandiri teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">10</span><p class="font-semibold text-slate-800 leading-snug">Sosialisasi dan Pelatihan E-Commerce bagi UMKM di Kota Gorontalo</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2021</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Dr. Lanto M. Kamil Amali</span><span class="chip-anggota">Ifan Wiranto</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">luaran.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2020" data-cari="pendampingan keamanan instalasi listrik di sekolah dasar dan madrasah salmawaty tansa ervan hasan harun mandiri teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">11</span><p class="font-semibold text-slate-800 leading-snug">Pendampingan Keamanan Instalasi Listrik di Sekolah Dasar dan Madrasah</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2020</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Salmawaty Tansa</span><span class="chip-anggota">Ervan Hasan Harun</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">dokumentasi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-emerald-50" data-tahun="2022" data-cari="workshop pembuatan media pembelajaran digital bagi guru smk zainudin bonok wrastawa ridwan luar pt 45000000 teknik elektro">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">12</span><p class="font-semibold text-slate-800 leading-snug">Workshop Pembuatan Media Pembelajaran Digital bagi Guru SMK</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Zainudin Bonok</span><span class="chip-anggota">Wrastawa Ridwan</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">Luar PT</span></td>
                                <td class="px-4 py-4 text-slate-600">Rp45.000.000</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Elektro</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">materi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-emerald-50" data-tahun="2023" data-cari="pemberdayaan kelompok tani melalui sistem informasi pertanian berbasis web ervan hasan harun yasin mohamad mandiri teknik informatika">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">13</span><p class="font-semibold text-slate-800 leading-snug">Pemberdayaan Kelompok Tani Melalui Sistem Informasi Pertanian Berbasis Web</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4"><div class="flex max-w-[220px] flex-wrap items-center gap-1.5"><span class="chip-ketua"><i class="fas fa-user-tie text-[9px]"></i>Ervan Hasan Harun</span><span class="chip-anggota">Yasin Mohamad</span></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-700">Mandiri/UNG</span></td>
                                <td class="px-4 py-4 text-slate-600">&mdash;</td>
                                <td class="px-4 py-4 text-slate-600">Teknik Informatika</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">laporan-akhir.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">luaran.pdf</span></a></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- PANEL: PENUNJANG -->
                <div class="tab-panel" id="panel-penunjang">
                    <table class="w-full min-w-[820px] text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
                            <tr>
                                <th class="py-3.5 pl-5 pr-4 font-semibold uppercase tracking-wider">Kegiatan / Dokumen</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Semester</th>
                                <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Diunggah oleh</th>
                                <th class="py-3.5 pr-5 font-semibold uppercase tracking-wider">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-white transition hover:bg-violet-50" data-tahun="2022" data-cari="sertifikat akreditasi program studi s1 teknik elektro yasin mohamad">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">1</span><p class="font-semibold text-slate-800 leading-snug">Sertifikat Akreditasi Program Studi S1 Teknik Elektro</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span></td>
                                <td class="px-4 py-4 text-slate-600">2021/2022 - Genap</td>
                                <td class="px-4 py-4 text-slate-600">Yasin Mohamad, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-akreditasi.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-baped.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-violet-50" data-tahun="2023" data-cari="anggota profesi forteil 2023 2024 zainudin bonok">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">2</span><p class="font-semibold text-slate-800 leading-snug">Anggota Profesi FORTEI 2023 - 2024</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Zainudin Bonok, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">kartu-fortei.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-anggota.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-violet-50" data-tahun="2023" data-cari="sk paw pengurus dpd wi kota gorontalo no d448 qr i 02 1445 zainudin bonok">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">3</span><p class="font-semibold text-slate-800 leading-snug">SK PAW Pengurus DPD WI Kota Gorontalo No. D448/QR/I/02/1445</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Zainudin Bonok, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-paw-wi.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">lampiran.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-violet-50" data-tahun="2023" data-cari="panitia kegiatan pengenalan kehidupan kampus bagi mahasiswa baru fakultas teknik ervan hasan harun">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">4</span><p class="font-semibold text-slate-800 leading-snug">Panitia Kegiatan Pengenalan Kehidupan Kampus bagi Mahasiswa Baru Fakultas Teknik</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Ervan Hasan Harun, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sk-panitia.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">lpj-panitia.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-violet-50" data-tahun="2023" data-cari="webinar nasional kupas tuntas best practice raih akreditasi unggul nasional dan internasional ervan hasan harun">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">5</span><p class="font-semibold text-slate-800 leading-snug">Webinar Nasional "Kupas Tuntas Best Practice Raih Akreditasi Unggul, Nasional dan Internasional"</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Ervan Hasan Harun, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-webinar.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">materi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-violet-50" data-tahun="2023" data-cari="webinar nasional strategi sukses memanfaatkan data utk pengambilan keputusan menuju akreditasi unggul ervan hasan harun">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">6</span><p class="font-semibold text-slate-800 leading-snug">Webinar Nasional "Strategi Sukses Memanfaatkan Data untuk Pengambilan Keputusan menuju Akreditasi Unggul"</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Ervan Hasan Harun, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-webinar.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">materi.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-white transition hover:bg-violet-50" data-tahun="2023" data-cari="seminar keuangan digital syariah berseri kelompok studi fintech syariah ervan hasan harun">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">7</span><p class="font-semibold text-slate-800 leading-snug">Seminar Keuangan Digital Syariah Berseri Kelompok Studi Fintech Syariah</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Ervan Hasan Harun, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-seminar.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">lpj-seminar.pdf</span></a></div></td>
                            </tr>
                            <tr class="bg-slate-50/60 transition hover:bg-violet-50" data-tahun="2023" data-cari="bimbingan teknis fitur akreditasi ervan hasan harun">
                                <td class="py-4 pl-5 pr-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold text-white shadow-sm">8</span><p class="font-semibold text-slate-800 leading-snug">Bimbingan Teknis: Fitur Akreditasi</p></div></td>
                                <td class="px-4 py-4"><span class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span></td>
                                <td class="px-4 py-4 text-slate-600">2023/2024 - Ganjil</td>
                                <td class="px-4 py-4 text-slate-600">Ervan Hasan Harun, ST., MT.</td>
                                <td class="py-4 pr-5"><div class="flex items-center gap-1.5"><a href="#" class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600"><i class="fas fa-file-pdf text-xs"></i><span class="tip">sertifikat-bimtek.pdf</span></a><a href="#" class="btn-circle bg-sky-600 text-white shadow-sm hover:bg-sky-700"><i class="fas fa-file-pdf text-xs"></i><span class="tip">materi-bimtek.pdf</span></a></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="noHasil" class="hidden py-16 text-center">
                <i class="fas fa-database text-4xl text-slate-300"></i>
                <p class="mt-3 font-medium text-slate-500">Tidak ada data ditemukan</p>
                <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter tahun.</p>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 px-5 py-3">
                <button type="button" id="btnPrev" class="pg-btn"><i class="fas fa-chevron-left text-xs"></i></button>
                <p class="text-xs font-semibold text-slate-500" id="lblPage">Halaman 1 / 1</p>
                <button type="button" id="btnNext" class="pg-btn"><i class="fas fa-chevron-right text-xs"></i></button>
            </div>
        </div>
    </section>

</main>

<script>
(function () {
    var ITEM_PER_PAGE = 10;
    var aktif = 'pendidikan', halaman = 1;

    var panels = {
        'pendidikan': document.getElementById('panel-pendidikan'),
        'penelitian': document.getElementById('panel-penelitian'),
        'pengabdian': document.getElementById('panel-pengabdian'),
        'penunjang': document.getElementById('panel-penunjang')
    };
    var rowsByTab = {};
    var labels = { 'pendidikan': 'Arsip Pendidikan', 'penelitian': 'Arsip Penelitian', 'pengabdian': 'Arsip Pengabdian', 'penunjang': 'Arsip Penunjang' };

    var k;
    for (k in panels) {
        if (!panels[k]) continue;
        rowsByTab[k] = Array.prototype.slice.call(panels[k].querySelectorAll('tbody tr'));
        var cntEl = document.getElementById('cnt-' + k);
        if (cntEl) cntEl.textContent = rowsByTab[k].length;
    }

    var fCari = document.getElementById('fCari');
    var fTahun = document.getElementById('fTahun');
    var judulEl = document.getElementById('judulTabel');
    var jmlEl = document.getElementById('jmlData');
    var badgeEl = document.getElementById('badgeData');
    var kosongEl = document.getElementById('noHasil');
    var lblPage = document.getElementById('lblPage');
    var btnPrev = document.getElementById('btnPrev');
    var btnNext = document.getElementById('btnNext');

    function cocok(tr) {
        var kata = (fCari && fCari.value || '').toLowerCase().trim();
        var thn = fTahun ? fTahun.value : '';
        if (kata !== '' && ((tr.getAttribute('data-cari') || '').toLowerCase().indexOf(kata) === -1)) return false;
        if (thn !== '' && tr.getAttribute('data-tahun') !== thn) return false;
        return true;
    }

    function render() {
        var rows = rowsByTab[aktif] || [];
        var tutup = (halaman - 1) * ITEM_PER_PAGE;
        var pos = 0, total = 0, i;
        for (i = 0; i < rows.length; i++) {
            var tampil = cocok(rows[i]);
            if (tampil) {
                total++;
                if (pos >= tutup && pos < tutup + ITEM_PER_PAGE) rows[i].style.display = '';
                else rows[i].style.display = 'none';
                pos++;
            } else {
                rows[i].style.display = 'none';
            }
        }
        var jmlHal = Math.max(1, Math.ceil(total / ITEM_PER_PAGE));
        if (halaman > jmlHal) { halaman = jmlHal; render(); return; }
        if (jmlEl) jmlEl.textContent = total.toLocaleString('id-ID');
        if (badgeEl) badgeEl.textContent = total.toLocaleString('id-ID');
        if (lblPage) lblPage.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;
        if (btnPrev) btnPrev.disabled = halaman <= 1;
        if (btnNext) btnNext.disabled = halaman >= jmlHal;
        if (kosongEl) kosongEl.classList.toggle('hidden', total > 0);
    }

    function pindah(tab) {
        aktif = tab;
        halaman = 1;
        for (k in panels) if (panels[k]) panels[k].classList.toggle('active', k === tab);
        var btns = document.querySelectorAll('.tab-btn');
        for (var i = 0; i < btns.length; i++) {
            btns[i].setAttribute('aria-selected', btns[i].getAttribute('data-tab') === tab ? 'true' : 'false');
        }
        if (judulEl) judulEl.textContent = labels[tab] || 'Data Tridharma';
        render();
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () { pindah(b.getAttribute('data-tab')); });
    });
    document.querySelectorAll('.tile-stat').forEach(function (t) {
        t.addEventListener('click', function () { pindah(t.getAttribute('data-tab')); });
    });
    if (fCari) fCari.addEventListener('input', function () { halaman = 1; render(); });
    if (fTahun) fTahun.addEventListener('change', function () { halaman = 1; render(); });
    if (btnPrev) btnPrev.addEventListener('click', function () { if (halaman > 1) { halaman--; render(); } });
    if (btnNext) btnNext.addEventListener('click', function () { halaman++; render(); });
    var btnReset = document.getElementById('btnReset');
    if (btnReset) btnReset.addEventListener('click', function () {
        if (fCari) fCari.value = '';
        if (fTahun) fTahun.value = '';
        halaman = 1;
        render();
    });
    var btnResetTab = document.getElementById('btnResetTab');
    if (btnResetTab) btnResetTab.addEventListener('click', function () { pindah('pendidikan'); });

    /* Tim > 2 anggota: tampilkan ketua + 2, sisanya diringkas tombol "+N" → popover daftar lengkap (nama tetap di HTML) */
    (function initTim() {
        var pop = document.createElement('div');
        pop.className = 'team-popover';
        document.body.appendChild(pop);

        var ketuas = document.querySelectorAll('.chip-ketua');
        for (var i = 0; i < ketuas.length; i++) (function (box) {
            var anggota = Array.prototype.slice.call(box.children).filter(function (c) { return c.classList.contains('chip-anggota'); });
            if (anggota.length <= 2) return;
            var extra = anggota.slice(2);
            for (var j = 0; j < extra.length; j++) extra[j].style.display = 'none';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chip-more';
            btn.textContent = '+' + extra.length;
            btn.addEventListener('click', function (ev) {
                ev.stopPropagation();
                if (pop.classList.contains('show')) { pop.classList.remove('show'); return; }
                pop.innerHTML = '<p class="pop-title">Anggota tim (' + anggota.length + ')</p>';
                for (var e = 0; e < extra.length; e++) {
                    var c = extra[e].cloneNode(true);
                    c.style.display = '';
                    pop.appendChild(c);
                }
                var r = ev.currentTarget.getBoundingClientRect();
                var w = pop.offsetWidth || 320;
                pop.style.left = Math.max(8, Math.min(r.left, window.innerWidth - w - 8)) + 'px';
                pop.style.top = (r.bottom + 8) + 'px';
                pop.classList.add('show');
            });
            box.appendChild(btn);
        })(ketuas[i].parentNode);

        document.addEventListener('click', function (e) {
            if (pop.classList.contains('show') && !pop.contains(e.target) && !e.target.classList.contains('chip-more')) pop.classList.remove('show');
        });
        window.addEventListener('scroll', function () { pop.classList.remove('show'); }, true);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') pop.classList.remove('show'); });
    })();

    pindah('pendidikan');
})();
</script>