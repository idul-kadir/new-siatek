<?php
require_once __DIR__ . '/../../../../koneksi.php';
?>
<style>
.content-scroll { overflow-y: auto; min-height: 0; }

/* ===== Hover lift & reveal ===== */
.lift { transition: transform .2s ease, box-shadow .2s ease; }
.lift:hover { transform: translateY(-3px); box-shadow: 0 14px 28px -14px rgba(15,23,42,.22); }
@keyframes riseIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.reveal { animation: riseIn .5s cubic-bezier(.16,1,.3,1) forwards; }
@media (prefers-reduced-motion: reduce) { .reveal, .lift { animation: none; transition: none; } }

/* ===== Kartu matakuliah ===== */
.mk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: .9rem; }
.mk-card { position: relative; overflow: hidden; border-radius: 14px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(15,23,42,.05); }
.mk-card::before { content: ""; position: absolute; inset: 0; border-radius: 14px; box-shadow: inset 0 0 0 1px var(--ring); opacity: 0; transition: opacity .2s ease; pointer-events: none; }
.mk-card:hover::before { opacity: .7; }
.mk-bar { position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--bar), var(--bar)); }
.mk-ico { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: 10px; font-size: .85rem; flex-shrink: 0; }
.badge-sks { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 9999px; font-size: 10px; font-weight: 700; line-height: 1; white-space: nowrap; }
.mk-act { opacity: 0; transform: translateY(4px); transition: opacity .15s ease, transform .15s ease; margin-top: .85rem; }
.mk-card:hover .mk-act, .mk-act:focus-within { opacity: 1; transform: none; }

/* ===== Chip filter SKS ===== */
.chip-sks { border-color: #e2e8f0; background: #fff; color: #64748b; }
.chip-sks:hover { border-color: #fdba74; color: #ea580c; }
.chip-sks.active { border-color: #f97316; background: #fff7ed; color: #ea580c; box-shadow: 0 0 0 1px #f97316; }

/* ===== Empty state ===== */
.empty-hidden { display: none; }
.empty-show { display: flex; }

/* ===== Toolbar ===== */
.toolbar { border-radius: 14px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.05); }

/* ===== Loading Spinner ===== */
.btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
.btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
.spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
</style>
<main class="content-area content-scroll">
  <section class="mb-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600"><i class="fas fa-user-graduate"></i></div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-800">Matakuliah MBKM</h1>
          <p class="text-xs text-slate-500">Merdeka Belajar Kampus Merdeka &middot; matakuliah kesetaraan program MBKM.</p>
        </div>
      </div>
      <button type="button" id="btnTambah" class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600"><i class="fas fa-plus mr-1"></i>Tambah Matakuliah</button>
    </div>
  </section>

  <section class="toolbar mb-4 p-3">
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative min-w-[200px] flex-1"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" id="fCari" placeholder="Cari kode atau nama matakuliah..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-orange-400 focus:bg-white"></div>
      <div class="flex flex-wrap items-center gap-1.5" id="chipContainer">
        <button type="button" class="chip-sks active rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="">Semua</button>
        <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="2">SKS 2</button>
        <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="3">SKS 3</button>
        <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="4">SKS 4</button>
        <button type="button" class="chip-sks rounded-full border px-3 py-1.5 text-xs font-semibold transition" data-sks="5">SKS 5</button>
      </div>
      <button type="button" id="btnReset" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset</button>
    </div>
  </section>

  <div id="emptyState" class="empty-hidden flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white py-14 text-center">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"><i class="fas fa-search text-lg"></i></div>
    <p class="mt-3 text-sm font-semibold text-slate-600">Tidak ada hasil</p>
    <p class="mt-1 text-xs text-slate-400" id="emptyStateDetail">Tidak ada matakuliah MBKM yang cocok dengan pencarian.</p>
    <button type="button" id="btnEmptyReset" class="mt-4 rounded-lg bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-200"><i class="fas fa-times mr-1"></i>Reset Filter</button>
  </div>

  <section class="mb-6" id="matkulContainer">
    <div class="mb-3 flex items-center justify-between">
      <p class="text-xs font-medium text-slate-500"><i class="fas fa-list mr-1 text-orange-400"></i>Menampilkan <span id="countNow" class="font-bold text-slate-700">0</span> dari 0 matakuliah</p>
    </div>
    <div class="mk-grid" id="mkGrid">
    </div>
    <p class="mt-3 text-[11px] text-slate-400"><i class="fas fa-lightbulb mr-1 text-amber-400"></i>Matakuliah kesetaraan program MBKM yang diakui dan dapat dikonversi SKS-nya.</p>
  </section>
</main>

<!-- Modal Tambah/Edit Matakuliah -->
<div id="modalMatkul" class="modal-overlay" role="dialog" aria-modal="true">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
      <h6 class="font-semibold text-slate-900 text-sm"><i class="fas fa-book mr-1 text-[#f97316]"></i><span id="modalTitle">Tambah Matakuliah</span></h6>
      <button type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none" onclick="closeModal()">&times;</button>
    </div>
    <div class="p-5">
      <input type="hidden" id="matkulKodeLama">
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Kode Matakuliah</label>
          <input type="text" id="matkulKode" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-sm outline-none transition focus:border-orange-400 focus:bg-white" placeholder="Contoh: EADKM60105">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Nama Matakuliah</label>
          <input type="text" id="matkulNama" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-sm outline-none transition focus:border-orange-400 focus:bg-white" placeholder="Nama matakuliah...">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Jumlah SKS</label>
          <select id="matkulSks" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-sm outline-none transition focus:border-orange-400 focus:bg-white">
            <option value="2">2 SKS</option>
            <option value="3" selected>3 SKS</option>
            <option value="4">4 SKS</option>
            <option value="5">5 SKS</option>
          </select>
        </div>
      </div>
    </div>
    <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-200 bg-slate-50">
      <button type="button" class="px-3 py-1.5 text-xs rounded-md bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium" onclick="closeModal()">Batal</button>
      <button type="button" id="btnSimpan" class="px-4 py-1.5 text-xs rounded-md bg-orange-500 hover:bg-orange-600 text-white font-semibold" data-text="Simpan">Simpan</button>
    </div>
  </div>
</div>

<script>
(function () {
  var PROSES_URL = 'pages/jurusan/matakuliah-mbkm/proses.php';

  function toast(msg, type) {
    var bg = '#0f1f3d';
    var icon = 'fa-circle-info';
    if (type === 'success') { bg = '#16a34a'; icon = 'fa-circle-check'; }
    else if (type === 'error') { bg = '#dc2626'; icon = 'fa-triangle-exclamation'; }

    var t = document.createElement("div");
    t.innerHTML = '<i class="fas ' + icon + ' mr-2"></i><span>' + msg + '</span>';
    t.style.cssText = "position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:.75rem 1.25rem;border-radius:.6rem;font-size:.85rem;box-shadow:0 10px 25px rgba(15,23,42,.3);transition:all .3s ease;color:#fff;background:" + bg + ";transform:translateX(100%);opacity:0;";
    document.body.appendChild(t);

    setTimeout(function () {
      t.style.transform = "translateX(0)";
      t.style.opacity = "1";
    }, 50);

    setTimeout(function () {
      t.style.transform = "translateX(100%)";
      t.style.opacity = "0";
      setTimeout(function () { t.remove(); }, 300);
    }, 3000);
  }

  function setButtonLoading(btn, isLoading) {
    if (isLoading) {
      btn.classList.add('is-loading');
      btn.setAttribute('disabled', '');
      btn.innerHTML = '<span class="spinner-slate"></span> ' + (btn.getAttribute('data-text') || btn.textContent);
    } else {
      btn.classList.remove('is-loading');
      btn.removeAttribute('disabled');
      btn.innerHTML = btn.getAttribute('data-text') || btn.textContent;
    }
  }

  var fCari = document.getElementById("fCari");
  var chips = Array.prototype.slice.call(document.querySelectorAll(".chip-sks"));
  var sksFilter = "";

  function filterAll() {
    var kata = (fCari && fCari.value || "").toLowerCase().trim();
    var cards = Array.prototype.slice.call(document.querySelectorAll(".mk-card"));
    var countNow = document.getElementById("countNow");
    var totalVisible = 0;
    
    cards.forEach(function (c) {
      var mKata = !kata || c.getAttribute("data-cari").indexOf(kata) !== -1;
      var mSks = !sksFilter || c.getAttribute("data-sks") === sksFilter;
      c.style.display = (mKata && mSks) ? "" : "none";
      if (mKata && mSks) totalVisible++;
    });
    
    if (countNow) countNow.textContent = totalVisible;
    var emptyState = document.getElementById("emptyState");
    var detail = document.getElementById("emptyStateDetail");
    var hasFilter = !!kata || !!sksFilter;
    
    if (emptyState) {
      emptyState.classList.toggle("empty-show", totalVisible === 0 && hasFilter);
      emptyState.classList.toggle("empty-hidden", !(totalVisible === 0 && hasFilter));
    }
    if (detail) {
      detail.textContent = sksFilter
        ? "Tidak ada matakuliah MBKM ber-SKS " + sksFilter + (kata ? " '" + kata + "'." : ".")
        : "Tidak ada matakuliah MBKM dengan kata '" + kata + "'. Coba kata lain atau reset filter.";
    }
  }

  if (fCari) fCari.addEventListener("input", filterAll);
  chips.forEach(function (ch) {
    ch.addEventListener("click", function () {
      chips.forEach(function (o) { o.classList.remove("active"); });
      ch.classList.add("active");
      sksFilter = ch.getAttribute("data-sks");
      filterAll();
    });
  });

  function resetFilters() {
    if (fCari) fCari.value = "";
    sksFilter = "";
    chips.forEach(function (o) { o.classList.remove("active"); });
    var x = document.querySelector('.chip-sks[data-sks=""]');
    if (x) x.classList.add("active");
    filterAll();
  }
  var btnReset = document.getElementById("btnReset");
  var btnEmptyReset = document.getElementById("btnEmptyReset");
  if (btnReset) btnReset.addEventListener("click", resetFilters);
  if (btnEmptyReset) btnEmptyReset.addEventListener("click", resetFilters);

  // Function muat ulang konten tabel/list matakuliah via AJAX
  function muat_ulang_tabel_matkul() {
    var container = document.getElementById("matkulContainer");
    if (!container) return;
    
    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: { 'ambil-tabel-matkul': 1 },
      dataType: 'json',
      success: function(respon) {
        if (respon.status) {
          container.innerHTML = respon.data;
          filterAll();
        } else {
          container.innerHTML = '<div class="text-center py-10 text-slate-500">Gagal memuat data</div>';
        }
      },
      error: function() {
        container.innerHTML = '<div class="text-center py-10 text-rose-500">Terjadi kesalahan pada server</div>';
      }
    });
  }

  // Modal functions
  window.closeModal = function() {
    document.getElementById("modalMatkul").classList.remove("show");
    document.getElementById("matkulKodeLama").value = "";
    document.getElementById("matkulKode").value = "";
    document.getElementById("matkulNama").value = "";
    document.getElementById("matkulSks").value = "3";
    document.getElementById("modalTitle").textContent = "Tambah Matakuliah";
  };

  window.openModal = function(kode) {
    document.getElementById("modalMatkul").classList.add("show");
    if (kode) {
      document.getElementById("modalTitle").textContent = "Edit Matakuliah";
      $.ajax({
        url: PROSES_URL,
        method: 'POST',
        data: { 'ambil-detail-matkul': 1, kode: kode },
        dataType: 'json',
        success: function(json) {
          if (json.status && json.data) {
            document.getElementById("matkulKodeLama").value = json.data.kode;
            document.getElementById("matkulKode").value = json.data.kode;
            document.getElementById("matkulNama").value = json.data.nama;
            document.getElementById("matkulSks").value = json.data.sks;
          } else {
            toast(json.pesan || "Gagal memuat data", "error");
          }
        },
        error: function() {
          toast("Terjadi kesalahan pada server", "error");
        }
      });
    } else {
      document.getElementById("modalTitle").textContent = "Tambah Matakuliah";
    }
  };

  // Edit & Hapus button handlers
  document.addEventListener("click", function (e) {
    var btnEdit = e.target.closest(".btn-edit");
    if (btnEdit) {
      var kode = btnEdit.getAttribute("data-kode");
      openModal(kode);
      return;
    }

    var btnHapus = e.target.closest(".btn-hapus");
    if (btnHapus) {
      var kodeHapus = btnHapus.getAttribute("data-kode");
      var namaHapus = btnHapus.getAttribute("data-nama");
      if (typeof konfirmasiHapus === "function") {
        konfirmasiHapus(kodeHapus + " - " + namaHapus, function() {
          $.ajax({
            url: PROSES_URL,
            method: 'POST',
            data: { 'hapus-data-matkul': 1, kode: kodeHapus },
            dataType: 'json',
            success: function(json) {
              if (json.status) {
                toast(json.pesan, "success");
                muat_ulang_tabel_matkul();
              } else {
                toast(json.pesan || "Gagal menghapus", "error");
              }
            },
            error: function() {
              toast("Terjadi kesalahan pada server", "error");
            }
          });
        });
      }
    }
  });

  // Simpan button handler
  document.getElementById("btnSimpan").addEventListener("click", function() {
    var btnSimpan = this;
    var kodeLama = document.getElementById("matkulKodeLama").value;
    var kode = document.getElementById("matkulKode").value.trim();
    var nama = document.getElementById("matkulNama").value.trim();
    var sks = document.getElementById("matkulSks").value;

    if (!kode || !nama || !sks) {
      toast("Kode, nama, dan SKS wajib diisi", "error");
      return;
    }

    setButtonLoading(btnSimpan, true);

    var postData = {
      kode: kode,
      nama: nama,
      sks: sks
    };

    if (kodeLama) {
      postData['edit-data-matkul'] = 1;
      postData['kodeLama'] = kodeLama;
    } else {
      postData['tambah-data-matkul'] = 1;
    }

    $.ajax({
      url: PROSES_URL,
      method: 'POST',
      data: postData,
      dataType: 'json',
      success: function(json) {
        if (json.status) {
          toast(json.pesan, "success");
          closeModal();
          muat_ulang_tabel_matkul();
        } else {
          toast(json.pesan || "Gagal menyimpan", "error");
        }
      },
      error: function() {
        toast("Terjadi kesalahan pada server", "error");
      },
      complete: function() {
        setButtonLoading(btnSimpan, false);
      }
    });
  });

  var btnTambah = document.getElementById("btnTambah");
  if (btnTambah) btnTambah.addEventListener("click", function () { openModal(); });

  // Muat data awal saat halaman dibuka
  muat_ulang_tabel_matkul();
})();
</script>