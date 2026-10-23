<?php
require_once __DIR__ . '/../fungsi.php';
// ============================================================
// TEMPLATE SURAT — KERTAS A4 (halaman utama)
// ------------------------------------------------------------
// Simpel & aman: dibuka lewat URL bersih
//     /dokumen/<kategori>/<nim>
// yang di-rewrite .htaccess menjadi
//     surat/template-a4.php?kategori=...&nim=...
//
// Isi surat dipilih lewat switch($kategori) — whitelist. Tidak
// ada include file sembarang dari input URL. Tambahkan case baru
// untuk setiap jenis surat yang mau dicetak.
//
// Variabel $nim otomatis tersedia di dalam file konten yang
// di-require (dipakai nanti untuk data real).
// ============================================================

$kategori = isset($_GET['kategori']) ? (string) $_GET['kategori'] : '';
$nim      = isset($_GET['nim'])      ? (string) $_GET['nim']      : '';
$reqKertas = isset($_GET['kertas']) ? strtoupper(trim((string) $_GET['kertas'])) : '';

$ukuranKertas = 'A4';
if (in_array($reqKertas, ['A4', 'F4'])) {
    $ukuranKertas = $reqKertas;
} elseif ($kategori === 'surat-custom' && !empty($nim)) {
    $idClean = bersihkan($nim);
    $qUkuran = query("SELECT ukuran_kertas FROM surat_custom WHERE id = '$idClean' LIMIT 1");
    if ($qUkuran && ($rUk = mysqli_fetch_assoc($qUkuran))) {
        if (!empty($rUk['ukuran_kertas']) && in_array(strtoupper($rUk['ukuran_kertas']), ['A4', 'F4'])) {
            $ukuranKertas = strtoupper($rUk['ukuran_kertas']);
        }
    }
}

$pageWidth   = ($ukuranKertas === 'F4') ? '215mm' : '210mm';
$pageHeight  = ($ukuranKertas === 'F4') ? '330mm' : '297mm';
$pageSizeCss = ($ukuranKertas === 'F4') ? '215mm 330mm' : 'A4 portrait';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Template Surat — <?php echo htmlspecialchars($kategori); ?></title>
<style>
    @page { size: <?= $pageSizeCss ?>; margin: 0; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: auto; }
    body {
        font-family: 'Times New Roman', Georgia, serif;
        background: #334155;
        color: #000;
        font-size: 12pt;
        line-height: 1.25;
        padding-top: 55px;
        padding-bottom: 80px;
    }

    /* ---- Kertas Dokumen (A4 / F4 Margin Mutlak: 2.5cm sekeliling, Custom Surat: Mepet Bawah) ---- */
    .sheet {
        background: #fff;
        width: <?= $pageWidth ?>;
        min-height: <?= $pageHeight ?>;
        margin: 20px auto;
        padding: 25mm; /* 2.5 cm mutlak */
        box-shadow: 0 10px 35px rgba(0, 0, 0, .5);
        border: 1px solid #94a3b8;
        box-sizing: border-box;
        position: relative;
    }
    .sheet.is-custom {
        height: <?= $pageHeight ?>;
        max-height: <?= $pageHeight ?>;
        overflow: hidden;
        padding: 20mm 25mm 12mm 25mm; /* Mepet ke bawah sesuai permintaan operator */
    }
    .sheet.is-custom.f4 {
        width: 215mm;
        height: 330mm;
        min-height: 330mm;
        max-height: 330mm;
    }

    /* ---- Kop surat ---- */
    .kop { position: relative; min-height: 24mm; }
    .kop img.logo { position: absolute; top: 0; left: 0; width: 22mm; }
    .kop .brand { text-align: center; margin: 0 22mm; }
    .kop .brand .org { font-size: 12pt; margin: 0; line-height: 1.22; }
    .kop .brand .addr { font-size: 10pt; margin: 0; line-height: 1.22; }
    .kop hr { border: 1.5px solid #000; margin: 4px 0 0; }

    /* ---- Isi surat ---- */
    .judul { text-align: center; margin: 8px 0 3px; }
    .judul b, .judul u { font-size: 12pt; }
    .nomor { text-align: center; margin-top: 3px; }
    .pembuka { text-align: justify; margin-top: 10px; }

    table.data { width: 100%; border-collapse: collapse; margin-top: 2mm; }
    table.data td { vertical-align: top; padding: 0; }

    .penutup { text-align: justify; margin-top: 2.5mm; }

    .ttd-wrap { width: 100%; margin-top: 3.5mm; }
    .ttd-wrap td { vertical-align: top; }
    .ttd-place { height: 85px; }
    .ttd-place svg { height: 42px; width: auto; }

    .tembusan { margin-top: 3mm; }

    @media print {
        @page {
            size: <?= $pageSizeCss ?>;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            background: #fff;
        }
        body {
            background: #fff;
            padding: 0;
        }
        .no-print, .print-action-bar {
            display: none !important;
        }
        .sheet {
            box-shadow: none;
            border: none;
            margin: 0;
            width: <?= $pageWidth ?>;
            min-height: <?= $pageHeight ?>;
            padding: 25mm; /* 2.5 cm mutlak */
            page-break-after: always;
            break-after: page;
            page-break-inside: auto;
        }
        .sheet.is-custom {
            height: <?= $pageHeight ?>;
            max-height: <?= $pageHeight ?>;
            overflow: hidden;
            padding: 20mm 25mm 12mm 25mm; /* Mepet ke bawah */
        }
        .sheet:last-of-type, .sheet:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }
        .kop .brand .org { font-size: 12pt; line-height: 1.22; }
        .kop .brand .addr { font-size: 10pt; line-height: 1.22; }
        .judul { margin: 8px 0 3px; }
        .nomor { margin-top: 3px; }
        .pembuka { margin-top: 10px; }
        .penutup { margin-top: 2.5mm; }
        .ttd-wrap { margin-top: 3.5mm; }
        .ttd-place { height: 85px; }
        .ttd-place svg { height: 40px; width: auto; }
    }
</style>
</head>
<body class="<?= strtolower($ukuranKertas) ?>">

<!-- Bar Aksi Pratinjau Cetak (Hanya Tampil di Layar, Tersembunyi Otomatis Saat Dicetak / Save PDF) -->
<div class="print-action-bar no-print" style="position: fixed; top: 0; left: 0; right: 0; height: 50px; background: #0f172a; border-bottom: 1px solid #334155; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-family: system-ui, -apple-system, sans-serif; color: #fff;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-weight: 700; font-size: 13px; color: #f97316;">SIATEK Dokumen</span>
        <span style="font-size: 11px; background: #1e293b; color: #cbd5e1; border: 1px solid #334155; padding: 2px 8px; border-radius: 4px; font-weight: 600;">Ukuran: <?= $ukuranKertas ?> (<?= $pageWidth ?> × <?= $pageHeight ?>)<span id="sheetCountBadge" style="display: none; color: #38bdf8; font-weight: 700;"></span></span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 11px; color: #94a3b8;">Ganti Ukuran:</span>
        <a href="?kategori=<?= urlencode($kategori) ?>&nim=<?= urlencode($nim) ?>&kertas=A4" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; <?= ($ukuranKertas === 'A4') ? 'background: #ea580c; color: #fff;' : 'background: #1e293b; color: #cbd5e1; border: 1px solid #334155;' ?>">A4</a>
        <a href="?kategori=<?= urlencode($kategori) ?>&nim=<?= urlencode($nim) ?>&kertas=F4" style="padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; <?= ($ukuranKertas === 'F4') ? 'background: #ea580c; color: #fff;' : 'background: #1e293b; color: #cbd5e1; border: 1px solid #334155;' ?>">F4 (Folio)</a>

        <div style="width: 1px; height: 20px; background: #334155; margin: 0 4px;"></div>

        <button type="button" onclick="window.print();" style="display: inline-flex; align-items: center; gap: 6px; background: #16a34a; color: #fff; font-size: 12px; font-weight: 700; padding: 6px 14px; border: none; border-radius: 6px; cursor: pointer;">
            <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Cetak Dokumen (Ctrl+P)
        </button>

        <button type="button" onclick="window.close(); if(!window.closed) history.back();" style="background: #334155; color: #cbd5e1; font-size: 12px; font-weight: 600; padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer;">
            Tutup
        </button>
    </div>
</div>

<?php if ($kategori === 'surat-custom'): ?>
    <?php
    require_once __DIR__ . '/content-surat/surat-custom.php';
    surat_custom($nim);
    ?>
<?php else: ?>
<section class="sheet">

    <?php
    if ($kategori != '') {
        switch ($kategori) {
            case 'surat-penunjukkan-penguji-hasil-penelitian':
                require_once __DIR__ . '/content-surat/surat-penunjukkan-penguji.php';
                surat_penunjukkan_penguji_hasil_penelitian($nim);
                break;

            case 'surat-penunjukkan-pembimbing-skripsi':
                require_once __DIR__ . '/content-surat/surat-penunjukkan-pembimbing-skripsi.php';
                surat_penunjukkan_pembimbing_skripsi($nim);
                break;

            case 'surat-penunjukkan-penguji-tutup':
                require_once __DIR__ . '/content-surat/surat-penunjukkan-penguji-tutup.php';
                surat_penunjukkan_penguji_tutup($nim);
                break;

            case 'surat-penunjukkan-pembimbing-kp':
                require_once __DIR__ . '/content-surat/surat-penunjukkan-pembimbing-kp.php';
                surat_penunjukkan_pembimbing_kp($nim);
                break;

            case 'surat-penunjukkan-penguji-kp':
                require_once __DIR__ . '/content-surat/surat-penunjukkan-penguji-kp.php';
                surat_penunjukkan_penguji_kp($nim);
                break;

            case 'surat-penarikan-kp':
                require_once __DIR__ . '/content-surat/surat-penarikan-kp.php';
                break;

            case 'surat-permohonan-kp':
                require_once __DIR__ . '/content-surat/surat-permohonan-kp.php';
                surat_permohonan_kp($nim);
                break;

            case 'lembar-bimbingan-kp':
                require_once __DIR__ . '/content-surat/lembar-bimbingan-kp.php';
                lembar_bimbingan_kp($nim);
                break;

            case 'surat-keterangan-pembimbing-kp':
                require_once __DIR__ . '/content-surat/surat-keterangan-pembimbing-kp.php';
                surat_keterangan_pembimbing_kp($nim);
                break;

            case 'surat-permohonan-izin-kp':
                require_once __DIR__ . '/content-surat/surat-permohonan-izin-kp.php';
                surat_permohonan_izin_kp($nim);
                break;

            default:
                echo 'Dokumen yang Anda minta tidak ada.';
        }
    } else {
        echo 'Jenis dokumen tidak dikenali.';
    }
    ?>

</section>
<?php endif; ?>

<script>
window.addEventListener('DOMContentLoaded', function() {
    var sheets = document.querySelectorAll('.sheet');
    var badge = document.getElementById('sheetCountBadge');
    if (badge && sheets.length > 1) {
        badge.textContent = ' • ' + sheets.length + ' Lembar (Lampiran)';
        badge.style.display = 'inline';
    }
});
</script>

</body>
</html>