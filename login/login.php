<?php
/**
 * Halaman: LOGIN SIATEK (dokumen penuh, tanpa shell aplikasi).
 * Tampilan saja - semua proses ada di login/proses.php (AJAX + JSON,
 * sesuai konvensi php-native-crud).
 *
 * Akun : tabel dosen (staf) dan tabel mahasiswa.
 */
require_once __DIR__ . '/../fungsi.php';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - SIATEK</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { font-family: "Inter", sans-serif; }
    body { background-color: #f1f5f9; }

    /* Panel kiri: gradasi navy -> oranye dengan corak polkadot halus */
    .brand-panel {
        background: linear-gradient(150deg, #12263f 0%, #1a365d 45%, #234670 70%, #b45309 130%);
        position: relative;
        overflow: hidden;
    }
    .brand-panel::before {
        content: "";
        position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.16) 1px, transparent 1px);
        background-size: 14px 14px;
        opacity: .35;
        pointer-events: none;
    }
    .brand-panel > * { position: relative; }

    .glow-orb { position: absolute; border-radius: 9999px; filter: blur(60px); opacity: .35; pointer-events: none; }

    @keyframes riseIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    .reveal { animation: riseIn .55s cubic-bezier(.16,1,.3,1) both; }
    @media (prefers-reduced-motion: reduce) { .reveal { animation: none; } }

    .form-input { width: 100%; border-radius: .5rem; border: 1px solid #e2e8f0; background: #f8fafc;
        padding: .62rem .85rem; font-size: .9rem; outline: none; transition: all .15s ease; }
    .form-input:focus { border-color: #fb923c; background: #fff; box-shadow: 0 0 0 3px rgba(251,146,60,.15); }

    .btn-google { transition: all .15s ease; }
    .btn-google:hover { background: #f8fafc; border-color: #cbd5e1; }

    .shake { animation: shakeX .4s ease; }
    @keyframes shakeX { 0%,100%{transform:none} 20%{transform:translateX(-7px)} 40%{transform:translateX(7px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)} }
</style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">

    <!-- ===== Panel Brand (kiri) ===== -->
    <div class="brand-panel reveal hidden w-[46%] flex-col justify-between p-12 lg:flex">
        <span class="glow-orb left-[-80px] top-[-80px] h-72 w-72 bg-orange-500"></span>
        <span class="glow-orb bottom-[-100px] right-[-60px] h-96 w-96 bg-sky-400/40"></span>

        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-500 text-lg text-white shadow-lg shadow-orange-500/40">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <p class="text-base font-bold tracking-wide text-white">SIATEK v4</p>
                <p class="text-xs text-white/60">Sistem Informasi Akademik T. Elektro &amp; Komputer</p>
            </div>
        </div>

        <div class="max-w-md">
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-white">
                Satu pintu untuk seluruh<br>administrasi jurusan.
            </h1>
            <p class="mt-4 text-sm leading-relaxed text-white/70">
                Kelola mahasiswa, skripsi, jadwal, surat penunjukkan, sampai sinkronisasi
                Sister Dikti - semuanya dari satu dashboard.
            </p>
            <ul class="mt-8 space-y-3.5 text-sm text-white/85">
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fas fa-user-shield text-xs"></i></span>Akses sesuai peran - admin, laboran, dosen</li>
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fas fa-file-signature text-xs"></i></span>Surat penunjukkan &amp; agenda terpusat</li>
                <li class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fas fa-database text-xs"></i></span>Data nyata dari database jurusan</li>
            </ul>
        </div>

        <p class="text-xs text-white/50">&copy; <?= date('Y') ?> Jurusan Teknik Elektro dan Komputer - FT UNG</p>
    </div>

    <!-- ===== Form (kanan) ===== -->
    <div class="flex flex-1 items-center justify-center p-6 sm:p-10">
        <div class="reveal w-full max-w-sm" style="animation-delay:.08s">

            <!-- Logo kecil utk layar kecil -->
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white shadow-md shadow-orange-500/30"><i class="fas fa-bolt"></i></div>
                <div>
                    <p class="font-bold tracking-wide text-slate-800">SIATEK v4</p>
                    <p class="text-[11px] text-slate-500">T. Elektro &amp; Komputer - FT UNG</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-900/5 sm:p-8">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg text-orange-600">
                        <i class="fas fa-right-to-bracket"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-slate-800">Masuk ke SIATEK</h2>
                        <p class="text-xs text-slate-500">Gunakan NIP/NIM dan password akun Anda.</p>
                    </div>
                </div>

                <!-- Kotak pesan galat/sukses (diisi via AJAX) -->
                <div id="boxPesan" class="hidden mb-4 flex items-start gap-2 rounded-xl px-3.5 py-2.5 text-xs font-semibold"></div>

                <form id="formLogin" method="post" action="/redesain-siatek/login/proses.php" class="space-y-4" novalidate>
                    <div>
                        <label for="inNip" class="mb-1 block text-xs font-semibold text-slate-600">NIP / NIM</label>
                        <div class="relative">
                            <i class="fas fa-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input type="text" id="inNip" name="nip" required autocomplete="username"
                                   placeholder="NIP / NIM Anda"
                                   class="form-input pl-9">
                        </div>
                    </div>
                    <div>
                        <label for="inPw" class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input type="password" id="inPw" name="password" required autocomplete="current-password"
                                   placeholder="Password Anda" class="form-input pl-9 pr-10">
                            <button type="button" data-toggle-pw="inPw"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-500/30 transition hover:bg-orange-600 active:scale-[.99] disabled:opacity-60">
                        <i class="fas fa-arrow-right-to-bracket"></i>Masuk
                    </button>

                    <!-- Pembatas -->
                    <div class="flex items-center gap-3 pt-1">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">atau</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    <!-- Login via Google -->
                    <button type="button" id="btnGoogle"
                            class="btn-google inline-flex w-full items-center justify-center gap-2.5 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">
                        <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        Masuk dengan Google
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-[11px] leading-relaxed text-slate-400">
                Lupa password? Hubungi administrator jurusan<br>
                Laboratorium Teknik Elektro &amp; Komputer - FT UNG
            </p>
        </div>
    </div>

</div>

<script>
// ================= TOGGLE LIHAT PASSWORD =================
document.querySelectorAll('[data-toggle-pw]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var inp = document.getElementById(btn.getAttribute('data-toggle-pw'));
        var show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        btn.innerHTML = '<i class="fas ' + (show ? 'fa-eye-slash' : 'fa-eye') + ' text-xs"></i>';
    });
});

// ================= TAMPILKAN PESAN DI KOTAK =================
function tampilPesan(pesan, gagal) {
    var box = $('#boxPesan');
    box.removeClass('hidden bg-rose-50 text-rose-600 border-rose-200 bg-emerald-50 text-emerald-700 border-emerald-200 shake');
    box.addClass(gagal
        ? 'bg-rose-50 text-rose-600 border border-rose-200 shake'
        : 'bg-emerald-50 text-emerald-700 border border-emerald-200');
    box.html('<i class="fas ' + (gagal ? 'fa-circle-exclamation' : 'fa-circle-check') + ' mt-0.5"></i>' + pesan);
}

// Pesan galat dari callback Google (?err=...)
var ERR_URL = {
    ggagal:   'Login Google gagal. Silakan coba lagi.',
    gstate:   'Sesi login Google kedaluwarsa. Coba lagi.',
    gconf:    'Login Google belum dikonfigurasi pada server ini.',
    gemail:   'Email Google Anda tidak terdaftar di SIATEK.',
    gdomain:  'Domain email Google tidak diizinkan.',
    nonaktif: 'Akun Anda tidak aktif. Hubungi administrator.'
};
(function () {
    var q = new URLSearchParams(location.search);
    if (ERR_URL[q.get('err')]) tampilPesan(ERR_URL[q.get('err')], true);
})();

// ================= SUBMIT LOGIN (AJAX -> JSON) =================
$('#formLogin').on('submit', function (e) {
    e.preventDefault();

    var form = $(this);
    var btn  = form.find('button[type=submit]');
    btn.prop('disabled', true);

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize() + '&masuk-login=1',
        dataType: 'json',
        success: function (respon) {
            if (respon.status) {
                tampilPesan(respon.pesan, false);
                setTimeout(function () { location.href = respon.target; }, 350);
            } else {
                tampilPesan(respon.pesan, true);
                btn.prop('disabled', false);
            }
        },
        error: function () {
            tampilPesan('Terjadi kesalahan pada server.', true);
            btn.prop('disabled', false);
        }
    });
});

// ================= LOGIN VIA GOOGLE =================
$('#btnGoogle').on('click', function () {
    var btn = $(this);
    btn.prop('disabled', true);
    $.ajax({
        url: '/redesain-siatek/login/proses.php',
        method: 'POST',
        data: { 'google-login': 1 },
        dataType: 'json',
        success: function (respon) {
            if (respon.status && respon.target) {
                location.href = respon.target;
            } else {
                tampilPesan(respon.pesan, true);
                btn.prop('disabled', false);
            }
        },
        error: function () {
            tampilPesan('Terjadi kesalahan pada server.', true);
            btn.prop('disabled', false);
        }
    });
});
</script>
</body>
</html>
