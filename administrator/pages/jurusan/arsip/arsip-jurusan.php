<?php
require __DIR__ ."/../../../fungsi.php";
?>
<style>
  .btn-circle {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    transition: all .15s ease;
  }

  .btn-circle-lg {
    width: 2.5rem;
    height: 2.5rem;
  }

  .btn-circle .tip {
    position: absolute;
    top: 115%;
    left: 50%;
    transform: translateX(-50%);
    background: #0f172a;
    color: #fff;
    font-size: 11px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity .15s ease;
    z-index: 40;
    pointer-events: none;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .25);
  }

  .btn-circle:hover .tip {
    opacity: 1;
    visibility: visible;
  }

  .content-scroll {
    overflow-y: auto;
    min-height: 0;
  }

  .tile-orange {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
  }

  .tile-sky {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
  }

  .tile-emerald {
    background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  }

  .tile-violet {
    background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%);
  }

  .tile-corak {
    position: relative;
    overflow: hidden;
  }

  .tile-corak::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: radial-gradient(rgba(255, 255, 255, .22) 1px, transparent 1px);
    background-size: 12px 12px;
    opacity: .35;
    mix-blend-mode: overlay;
  }

  .tile-corak>* {
    position: relative;
  }

  .pg-btn {
    min-width: 2rem;
    height: 2rem;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
  }

  .pg-btn:hover {
    border-color: #fdba74;
    color: #c2410c;
  }

  .pg-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
  }

  .pg-btn.on {
    background: #1e3a5f;
    border-color: #1e3a5f;
    color: #fff;
  }
</style>
<main class="content-area content-scroll">

  <!-- ===== Page Header ===== -->
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
          <i class="fas fa-archive"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Arsip Jurusan</h1>
          <p class="text-xs text-slate-500">Dokumen arsip tingkat jurusan </p>
        </div>
      </div>
      <button type="button" id="btnTambah"
        class="btn-circle btn-circle-lg bg-orange-500 text-white shadow-md shadow-orange-500/25 hover:bg-orange-600">
        <i class="fas fa-plus text-sm"></i>
        <span class="tip">Tambah Dokumen</span>
      </button>
    </div>
  </section>

  <!-- ===== Statistik ===== -->
  <section class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="tile-orange tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-orange-500/25">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Total Dokumen</p>
          <p class="mt-2 text-2xl font-bold tracking-tight">12</p>
          <p class="mt-2 text-[11px] text-white/70">seluruh arsip tersimpan</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i
            class="fas fa-box-archive"></i></span>
      </div>
    </div>
    <div class="tile-sky tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-sky-500/25"
      style="animation-delay:.05s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terlama</p>
          <p class="mt-2 text-2xl font-bold tracking-tight">2022</p>
          <p class="mt-2 text-[11px] text-white/70">arsip tertua</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i
            class="fas fa-hourglass-start"></i></span>
      </div>
    </div>
    <div class="tile-emerald tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-emerald-500/25"
      style="animation-delay:.10s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Tahun Terbaru</p>
          <p class="mt-2 text-2xl font-bold tracking-tight">2026</p>
          <p class="mt-2 text-[11px] text-white/70">arsip paling baru</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i
            class="fas fa-layer-group"></i></span>
      </div>
    </div>
    <div class="tile-violet tile-corak reveal rounded-xl p-4 text-white shadow-md shadow-violet-500/25"
      style="animation-delay:.15s">
      <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
          <p class="text-xs font-medium uppercase tracking-wide text-white/85">Pengunggah Terbanyak</p>
          <p class="mt-2 text-xl font-bold tracking-tight truncate">10041993</p>
          <p class="mt-2 text-[11px] text-white/70">7 dokumen</p>
        </div>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/20 text-sm"><i
            class="fas fa-user"></i></span>
      </div>
    </div>
  </section>

  <!-- ===== Toolbar (cari + filter tahun, live via JS) ===== -->
  <section class="mb-4">
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
      <div class="relative flex-1 min-w-[200px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
        <input type="search" id="fCari" placeholder="Cari nama dokumen, pengupload…"
          class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none focus:border-orange-400 focus:bg-white">
      </div>
      <select id="fTahun"
        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400">
        <option value="">Semua Tahun</option>
        <option value="2026">2026</option>
        <option value="2025">2025</option>
        <option value="2024">2024</option>
        <option value="2023">2023</option>
        <option value="2022">2022</option>
      </select>
      <a href="index.php?page=arsip-jurusan"
        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"
        onclick="document.getElementById('fCari').value='';document.getElementById('fTahun').value='';document.querySelectorAll('#arsipBody tr').forEach(function(r){r.style.display=''});document.getElementById('jmlArsip').textContent='12';document.getElementById('badgeArsip').textContent='12';document.getElementById('noHasil').classList.add('hidden');return false;">
        <i class="fas fa-times mr-1"></i>Reset
      </a>
    </div>
  </section>

  <!-- ===== Daftar Arsip ===== -->
  <section>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
          <h2 class="text-base font-semibold text-slate-800">Daftar Dokumen</h2>
          <p class="mt-0.5 text-xs text-slate-500"><span id="jmlArsip">12</span> arsip ditemukan</p>
        </div>
        <span
          class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
          <i class="fas fa-file-archive"></i> <span id="badgeArsip">12</span> file
        </span>
      </div>
      <div id="noHasil" class="hidden py-16 text-center">
        <i class="fas fa-archive text-4xl text-slate-300"></i>
        <p class="mt-3 font-medium text-slate-500">Tidak ada arsip ditemukan</p>
        <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter tahun.</p>
      </div>
      <div class="max-h-[560px] overflow-y-auto">
        <table class="w-full text-left text-sm">
          <thead class="sticky top-0 z-10 bg-slate-900 text-xs text-white">
            <tr>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Dokumen</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Tahun</th>
              <th class="py-3.5 px-4 font-semibold uppercase tracking-wider">Diunggah oleh</th>
              <th class="py-3.5 px-5 font-semibold uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody id="arsipBody" class="divide-y divide-slate-100">

            <!-- 1. SK Pendirian Perguruan Tinggi — 2026 — Humas — PDF -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="1" data-tahun="2026"
              data-nama="sk pendirian perguruan tinggi" data-ptext="SK Pendirian Perguruan Tinggi"
              data-pengupload="humas" data-file="file/sk-pendirian.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">1</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">SK Pendirian Perguruan Tinggi</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span>
              </td>
              <td class="px-4 py-4 text-slate-600">Humas</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/sk-pendirian.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 2. BKD 25/26 GANJIL - ULFATUN NADIFA — 2026 — 20101993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="2" data-tahun="2026"
              data-nama="bkd 25/26 ganjil - ulfatun nadifa" data-ptext="BKD 25/26 GANJIL - ULFATUN NADIFA"
              data-pengupload="20101993" data-file="file/bkd-ulfatun.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">2</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">BKD 25/26 GANJIL - ULFATUN NADIFA</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span>
              </td>
              <td class="px-4 py-4 text-slate-600">20101993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/bkd-ulfatun.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 3. PANDUAN PELAKSANAAN KKN TEMATIK 2024 — 2026 — 20101993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="3" data-tahun="2026"
              data-nama="panduan pelaksanaan kkn tematik 2024" data-ptext="PANDUAN PELAKSANAAN KKN TEMATIK 2024"
              data-pengupload="20101993" data-file="file/panduan-kkn-2024.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">3</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">PANDUAN PELAKSANAAN KKN TEMATIK 2024</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span>
              </td>
              <td class="px-4 py-4 text-slate-600">20101993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/panduan-kkn-2024.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 4. SOP KERJA PRAKTEK 2024 — 2026 — 20101993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="4" data-tahun="2026"
              data-nama="sop kerja praktek 2024" data-ptext="SOP KERJA PRAKTEK 2024" data-pengupload="20101993"
              data-file="file/sop-kp-2024.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">4</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">SOP KERJA PRAKTEK 2024</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2026</span>
              </td>
              <td class="px-4 py-4 text-slate-600">20101993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/sop-kp-2024.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 5. SK Tim Penyusun Visi Prodi — 2025 — 10041993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="5" data-tahun="2025"
              data-nama="sk tim penyusun visi prodi" data-ptext="SK Tim Penyusun Visi Prodi" data-pengupload="10041993"
              data-file="file/sk-tim-visi.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">5</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">SK Tim Penyusun Visi Prodi</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/sk-tim-visi.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 6. Kurikulum 2020 Prodi S1 Informatika — 2025 — 10041993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="6" data-tahun="2025"
              data-nama="kurikulum 2020 prodi s1 informatika" data-ptext="Kurikulum 2020 Prodi S1 Informatika"
              data-pengupload="10041993" data-file="file/kurikulum-2020.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">6</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Kurikulum 2020 Prodi S1 Informatika</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/kurikulum-2020.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 7. Laporan Akreditasi Unggul — 2025 — 20101993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="7" data-tahun="2025"
              data-nama="laporan akreditasi unggul" data-ptext="Laporan Akreditasi Unggul" data-pengupload="20101993"
              data-file="file/laporan-akreditasi.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">7</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Laporan Akreditasi Unggul</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2025</span>
              </td>
              <td class="px-4 py-4 text-slate-600">20101993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/laporan-akreditasi.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 8. Lembar Kerja LKPS 2024 — 2024 — 10041993 — XLSX -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="8" data-tahun="2024"
              data-nama="lembar kerja lkps 2024" data-ptext="Lembar Kerja LKPS 2024" data-pengupload="10041993"
              data-file="file/lkps-2024.xlsx">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">8</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Lembar Kerja LKPS 2024</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-emerald-600 text-white"><i
                        class="fas fa-file-export"></i>XLSX</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/lkps-2024.xlsx" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 9. Rencana Strategis Jurusan 2024-2028 — 2024 — 10041993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="9" data-tahun="2024"
              data-nama="rencana strategis jurusan 2024-2028" data-ptext="Rencana Strategis Jurusan 2024-2028"
              data-pengupload="10041993" data-file="file/renstra-2024-2028.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">9</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Rencana Strategis Jurusan 2024-2028</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2024</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/renstra-2024-2028.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 10. MoU Kerjasama Industri — 2023 — 10041993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="10" data-tahun="2023"
              data-nama="mou kerjasama industri" data-ptext="MoU Kerjasama Industri" data-pengupload="10041993"
              data-file="file/mou-industri.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">10</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">MoU Kerjasama Industri</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/mou-industri.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 11. Daftar Dosen & Jabatan Fungsional — 2023 — 10041993 — XLSX -->
            <tr class="transition hover:bg-orange-50 bg-white" data-id="11" data-tahun="2023"
              data-nama="daftar dosen & jabatan fungsional" data-ptext="Daftar Dosen &amp; Jabatan Fungsional"
              data-pengupload="10041993" data-file="file/daftar-dosen.xlsx">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">11</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Daftar Dosen &amp; Jabatan Fungsional</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-emerald-600 text-white"><i
                        class="fas fa-file-export"></i>XLSX</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2023</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/daftar-dosen.xlsx" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

            <!-- 12. Dokumen AKMAL Pendampingan — 2022 — 10041993 — PDF -->
            <tr class="transition hover:bg-orange-50 bg-slate-50/60" data-id="12" data-tahun="2022"
              data-nama="dokumen akmal pendampingan" data-ptext="Dokumen AKMAL Pendampingan" data-pengupload="10041993"
              data-file="file/akmal-pendampingan.pdf">
              <td class="py-4 pl-5 pr-4">
                <div class="flex items-center gap-3">
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-base font-bold text-white shadow-sm">12</span>
                  <div class="min-w-0">
                    <p class="font-semibold text-slate-800 leading-snug">Dokumen AKMAL Pendampingan</p>
                    <span
                      class="mt-1 inline-flex items-center gap-1 rounded px-1.5 py-px text-[10px] font-bold bg-rose-500 text-white"><i
                        class="fas fa-file-export"></i>PDF</span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span
                  class="inline-flex items-center rounded-full bg-slate-200/70 px-2.5 py-1 text-xs font-bold text-slate-700">2022</span>
              </td>
              <td class="px-4 py-4 text-slate-600">10041993</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-1.5">
                  <a href="file/akmal-pendampingan.pdf" target="_blank" rel="noopener"
                    class="btn-circle bg-emerald-500 text-white shadow-sm hover:bg-emerald-600">
                    <i class="fas fa-download text-xs"></i><span class="tip">Unduh</span>
                  </a>
                  <button type="button"
                    class="btnCircleEdit btn-circle bg-sky-500 text-white shadow-sm hover:bg-sky-600">
                    <i class="fas fa-pen text-xs"></i><span class="tip">Edit</span>
                  </button>
                  <button type="button" class="btn-circle bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                    onclick="hapusArsip(this)">
                    <i class="fas fa-trash text-xs"></i><span class="tip">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
        <p class="pg-label text-xs font-semibold text-slate-500" id="lblPage">Halaman 1 / 1</p>
        <div class="inline-flex items-center gap-1.5" id="pgWrap"></div>
      </div>

    </div>
  </section>

  <!-- ===== Modal Unggah / Edit Data ===== -->
  <div class="modal-overlay" id="arsipModal" role="dialog" aria-modal="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
        <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-archive mr-1 text-[#f97316]"></i><span
            id="arsipModalTitle">Unggah Dokumen</span></h6>
        <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none"
          data-modal-close>&times;</button>
      </div>
      <form id="arsipForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="fAction" value="tambah">
        <input type="hidden" name="id" id="fId" value="">
        <input type="hidden" name="file_lama" id="fFileLama" value="">
        <div class="grid gap-4 p-5">
          <div>
            <label for="fNama" class="mb-1 block text-xs font-semibold text-slate-600">Nama Dokumen</label>
            <input type="text" id="fNama" name="nama" required
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white"
              placeholder="contoh: SK Pendirian">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="fInTahun" class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
              <select id="fInTahun" name="tahun" required
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white">
                <option value="2026" selected>2026</option>
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
                <option value="2015">2015</option>
                <option value="2014">2014</option>
                <option value="2013">2013</option>
                <option value="2012">2012</option>
                <option value="2011">2011</option>
                <option value="2010">2010</option>
                <option value="2009">2009</option>
                <option value="2008">2008</option>
                <option value="2007">2007</option>
                <option value="2006">2006</option>
                <option value="2005">2005</option>
                <option value="2004">2004</option>
                <option value="2003">2003</option>
                <option value="2002">2002</option>
                <option value="2001">2001</option>
                <option value="2000">2000</option>
              </select>
            </div>
            <div>
              <label for="fInUpload" class="mb-1 block text-xs font-semibold text-slate-600">Diunggah oleh</label>
              <input type="text" id="fInUpload" value="Sistem" disabled
                class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
            </div>
          </div>
          <div>
            <label for="fFile" class="mb-1 block text-xs font-semibold text-slate-600">File Dokumen</label>
            <input type="file" id="fFile" name="file_upload"
              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-orange-400 focus:bg-white file:mr-3 file:rounded-md file:border-0 file:bg-orange-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-orange-600">
            <p class="mt-1 text-[11px] text-slate-400">Dummy: hanya nama file yang tersimpan, file belum benar-benar
              diunggah.</p>
          </div>
        </div>
        <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
          <button type="button"
            class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium"
            data-modal-close>Batal</button>
          <button type="button"
            class="px-4 py-1.5 text-xs rounded-md bg-[#f97316] hover:bg-[#ea6a0f] text-white font-semibold"
            onclick="document.getElementById('arsipModal').classList.remove('show')">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function hapusArsip(btn) {
      var tr = btn.closest('tr');
      var nama = tr.querySelector('p.font-semibold').textContent;
      konfirmasiHapus(nama, function () {
        tr.remove();
        render();
      });
    }

    (function () {
      var ITEM_PER_PAGE = 10;
      var halaman = 1;
      var fCari = document.getElementById('fCari');
      var fTahun = document.getElementById('fTahun');
      var arsipBody = document.getElementById('arsipBody');
      var rows = Array.prototype.slice.call(arsipBody ? arsipBody.querySelectorAll('tr') : []);
      var jmlArsip = document.getElementById('jmlArsip');
      var badgeArsip = document.getElementById('badgeArsip');
      var noHasil = document.getElementById('noHasil');
      var lblPage = document.getElementById('lblPage');
      var pgWrap = document.getElementById('pgWrap');

      function jumlahHalaman() {
        var visible = rows.filter(function (r) { return r.style.display !== 'none'; });
        return Math.max(1, Math.ceil(visible.length / ITEM_PER_PAGE));
      }

      function render() {
        var kata = (fCari.value || '').toLowerCase().trim();
        var tahun = fTahun.value;
        var tampil = 0;

        rows.forEach(function (r) {
          var ok = true;
          if (kata !== '') {
            var teks = (r.getAttribute('data-nama') || '') + ' ' + (r.getAttribute('data-pengupload') || '');
            if (teks.indexOf(kata) === -1) ok = false;
          }
          if (ok && tahun !== '' && r.getAttribute('data-tahun') !== tahun) ok = false;
          r.style.display = ok ? '' : 'none';
          if (ok) tampil++;
        });

        var jmlHal = jumlahHalaman();
        if (halaman > jmlHal) halaman = jmlHal;

        var allVisible = rows.filter(function (r) { return r.style.display !== 'none'; });
        var tutup = (halaman - 1) * ITEM_PER_PAGE;
        rows.forEach(function (r) { r.style.display = 'none'; });
        allVisible.slice(tutup, tutup + ITEM_PER_PAGE).forEach(function (r) { r.style.display = ''; });

        if (jmlArsip) jmlArsip.textContent = tampil;
        if (badgeArsip) badgeArsip.textContent = tampil.toLocaleString('id-ID');
        if (noHasil) noHasil.classList.toggle('hidden', tampil > 0);
        if (lblPage) lblPage.textContent = 'Halaman ' + halaman + ' / ' + jmlHal;

        if (pgWrap) {
          pgWrap.innerHTML = '';
          var prev = document.createElement('button');
          prev.type = 'button';
          prev.className = 'pg-btn';
          prev.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
          prev.disabled = halaman <= 1;
          prev.addEventListener('click', function () { if (halaman > 1) { halaman--; render(); } });
          pgWrap.appendChild(prev);

          for (var i = 1; i <= jmlHal; i++) {
            (function (n) {
              var b = document.createElement('button');
              b.type = 'button';
              b.className = 'pg-btn' + (n === halaman ? ' on' : '');
              b.textContent = n;
              b.addEventListener('click', function () { halaman = n; render(); });
              pgWrap.appendChild(b);
            })(i);
          }

          var next = document.createElement('button');
          next.type = 'button';
          next.className = 'pg-btn';
          next.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
          next.disabled = halaman >= jmlHal;
          next.addEventListener('click', function () { if (halaman < jmlHal) { halaman++; render(); } });
          pgWrap.appendChild(next);
        }
      }

      if (fCari) fCari.addEventListener('input', function () { halaman = 1; render(); });
      if (fTahun) fTahun.addEventListener('change', function () { halaman = 1; render(); });
      render();
    })();

    /* ===== Modal Unggah / Edit ===== */
    document.addEventListener('DOMContentLoaded', function () {
      var overlay = document.getElementById('arsipModal');
      var title = document.getElementById('arsipModalTitle');
      var f = document.getElementById('arsipForm');
      var iId = document.getElementById('fId');
      var iNama = document.getElementById('fNama');
      var iTahun = document.getElementById('fInTahun');
      var iFileLama = document.getElementById('fFileLama');
      var act = document.getElementById('fAction');

      function show(t, isEdit) {
        title.textContent = t;
        act.value = isEdit ? 'edit' : 'tambah';
        if (!isEdit) {
          f.reset();
          iId.value = '';
        }
        overlay.classList.add('show');
      }
      function close() { overlay.classList.remove('show'); }

      document.getElementById('btnTambah').addEventListener('click', function () {
        show('Unggah Dokumen Baru', false);
      });
      var cls = overlay.querySelectorAll('[data-modal-close]');
      for (var i = 0; i < cls.length; i++)
        cls[i].addEventListener('click', close);
      overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

      var edits = document.querySelectorAll('.btnCircleEdit');
      for (var j = 0; j < edits.length; j++) {
        edits[j].addEventListener('click', function () {
          var tr = this.closest('tr');
          iId.value = tr.getAttribute('data-id');
          iNama.value = tr.getAttribute('data-ptext');
          iTahun.value = tr.getAttribute('data-tahun');
          iFileLama.value = tr.getAttribute('data-file');
          show('Edit Dokumen', true);
        });
      }
    });
  </script>

</main>