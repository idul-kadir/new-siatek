<?php
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
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Template Surat — <?php echo htmlspecialchars($kategori); ?></title>
<style>
    @page { size: A4 portrait; margin: 0; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: auto; }
    body {
        font-family: 'Times New Roman', Georgia, serif;
        background: #e2e8f0;
        color: #000;
        font-size: 12pt;
        line-height: 1.35;
    }

    /* ---- Kertas A4 ---- */
    .sheet {
        background: #fff;
        width: 210mm;
        margin: 20px auto;
        padding: 12mm 20mm 14mm;
        box-shadow: 0 4px 24px rgba(15, 23, 42, .25);
    }

    /* ---- Kop surat ---- */
    .kop { position: relative; }
    .kop img.logo { position: absolute; top: 0; left: 35px; width: 100px; }
    .kop .brand { text-align: center; margin-left: 35px; }
    .kop .brand .org { font-size: 12pt; margin: 0; line-height: 1.25; }
    .kop .brand .addr { font-size: 10pt; margin: 0; line-height: 1.3; }
    .kop hr { border: 1px solid #000; margin: 0; }

    /* ---- Isi surat ---- */
    .judul { text-align: center; margin: 18px 0 10px; }
    .judul b, .judul u { font-size: 12pt; }
    .nomor { text-align: center; margin-top: 8px; }
    .pembuka { text-align: justify; margin-top: 28px; }

    table.data { width: 100%; border-collapse: collapse; margin-top: 2mm; }
    table.data td { vertical-align: top; padding: 0; }

    .penutup { text-align: justify; margin-top: 4mm; }

    .ttd-wrap { width: 100%; margin-top: 8mm; }
    .ttd-wrap td { vertical-align: top; }
    .ttd-place { height: 100px; }
    .ttd-place svg { height: 60px; width: auto; }

    .tembusan { margin-top: 6mm; }

    @media print {
        @page { size: A4 portrait; margin: 0; }
        html, body { margin: 0; padding: 0; }
        body { background: #fff; }
        .sheet { box-shadow: none; margin: 0; width: 210mm; padding: 12mm 20mm 14mm; }
        .kop .brand .org { font-size: 12pt; line-height: 1.25; }
        .kop .brand .addr { font-size: 10pt; line-height: 1.3; }
        .judul { margin: 18px 0 10px; }
        .pembuka { margin-top: 28px; }
        .ttd-wrap { margin-top: 7mm; }
        .ttd-place { height: 95px; }
        .ttd-place svg { height: 55px; width: auto; }
    }
</style>
</head>
<body class="a4">

<section class="sheet">

    <?php
    if ($kategori != '') {
        switch ($kategori) {
            case 'surat-penunjukkan-penguji':
            case 'surat-penunjukkan-hasil':
                require __DIR__ . '/content-surat/surat-penunjukkan-penguji.php';
                break;

            case 'surat-penunjukkan-pembimbing-skripsi':
                require __DIR__ . '/content-surat/surat-penunjukkan-pembimbing-skripsi.php';
                break;

            // Jenis surat lain tinggal ditambahkan, contoh:
            // case 'surat-pembimbing':
            //     require __DIR__ . '/content-surat/surat-pembimbing.php';
            //     break;

            default:
                echo 'Dokumen yang Anda minta tidak ada.';
        }
    } else {
        echo 'Jenis dokumen tidak dikenali.';
    }
    ?>

</section>

</body>
</html>