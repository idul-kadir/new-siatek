<?php
/**
 * Komponen Dokumen: Surat Kustom Jurusan
 * Format cetak resmi standar template A4 SIATEK.
 * Dipanggil dari surat/template-a4.php:
 *   surat_custom($nim) // $nim menampung id surat custom
 */

function surat_custom($idSurat) {
    global $koneksi;
    require_once dirname(__DIR__, 2) . '/fungsi.php';

    $idClean = bersihkan($idSurat);
    $q = query("SELECT * FROM surat_custom WHERE id = '$idClean' OR nomor_surat = '$idClean' LIMIT 1");

    if (!$q || mysqli_num_rows($q) === 0) {
        echo '<div style="text-align: center; padding: 40px 20px; font-family: sans-serif; color: #64748b;">';
        echo '<h3 style="font-size: 18px; color: #334155;">Surat Tidak Ditemukan</h3>';
        echo '<p style="font-size: 14px; margin-top: 8px;">Dokumen surat kustom dengan ID <b>' . htmlspecialchars($idSurat, ENT_QUOTES, 'UTF-8') . '</b> tidak tercatat di database.</p>';
        echo '</div>';
        return;
    }

    $surat = mysqli_fetch_assoc($q);

    // Ambil data dosen penandatangan (cek jika punya file ttd/qr)
    $ttdDosen = null;
    if (!empty($surat['ttd_dosen_id'])) {
        $qDosen = query("SELECT ttd FROM dosen WHERE id = '" . bersihkan($surat['ttd_dosen_id']) . "' OR nip = '" . bersihkan($surat['ttd_nip']) . "' LIMIT 1");
        if ($qDosen && ($rD = mysqli_fetch_assoc($qDosen))) {
            $ttdDosen = $rD;
        }
    }

    $tglTeks = function_exists('tgl_indo') ? tgl_indo($surat['tanggal_surat']) : date('d F Y', strtotime($surat['tanggal_surat']));
    $qrUrl = base_url() . '/dokumen/surat-custom/' . $surat['id'];

    // 1. KOP SURAT (jika dipilih pakai kop)
    if (!empty($surat['pakai_kop']) && (int)$surat['pakai_kop'] === 1) {
        include __DIR__ . '/kop-surat.php';
    } else {
        // Beri sedikit ruang atas jika tanpa kop agar simetris di lembar A4
        echo '<div style="height: 12mm;"></div>';
    }

    $rawBody = trim((string)($surat['isi_surat'] ?? ''));

    // Cek apakah isi surat merupakan dokumen utuh Word-like canvas baru (memuat seluruh susunan surat)
    $isUnifiedWordDoc = (
        strpos($rawBody, '<p') !== false ||
        strpos($rawBody, '<div') !== false ||
        strpos($rawBody, '<table') !== false ||
        strpos($rawBody, 'borderless') !== false ||
        strpos($rawBody, 'border-grid') !== false ||
        strpos($rawBody, 'table-bordered') !== false ||
        strpos($rawBody, 'ttd-wrap') !== false ||
        strpos($rawBody, 'word-doc') !== false ||
        strpos($rawBody, 'SURAT KETERANGAN') !== false ||
        strpos($rawBody, 'Nomor') !== false
    );
?>

<style>
    .word-canvas-body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.35;
        color: #000;
        text-align: justify;
        word-break: break-word;
    }
    .word-canvas-body p {
        margin: 0 0 0.35rem 0;
        line-height: inherit;
    }
    .word-canvas-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: inherit;
    }
    .word-canvas-body table td, .word-canvas-body table th {
        padding: 4px 6px;
        vertical-align: top;
    }
    .word-canvas-body table.border-grid td, .word-canvas-body table.border-grid th,
    .word-canvas-body table.table-bordered td, .word-canvas-body table.table-bordered th {
        border: 1px solid #000;
    }
    .word-canvas-body table.borderless td, .word-canvas-body table.borderless th,
    .word-canvas-body table.table-borderless td, .word-canvas-body table.table-borderless th {
        border: none !important;
    }
    .word-canvas-body ol {
        margin: 4px 0 6px 24px;
        padding-left: 0;
        list-style-type: decimal;
    }
    .word-canvas-body ul {
        margin: 4px 0 6px 20px;
        padding-left: 0;
        list-style-type: disc;
    }
    .word-canvas-body li {
        margin-bottom: 2px;
    }
    .word-canvas-body img {
        max-width: 100%;
        height: auto;
        display: inline-block;
        margin: 6px auto;
    }
    .word-canvas-body hr {
        border: 0;
        border-top: 1px solid #000;
        margin: 10px 0;
    }
</style>

<?php if ($isUnifiedWordDoc): ?>
    <!-- Mode Dokumen Bebas Word (1:1 apa yang diketik operator di canvas) -->
    <div class="word-canvas-body">
        <?= $rawBody ?>
    </div>
<?php else: ?>
    <!-- Mode Fallback Dokumen Lama (Legacy Support) -->
    <!-- Header Meta: Nomor, Hal, Tanggal -->
    <table style="width: 100%; margin-top: <?= !empty($surat['pakai_kop']) ? '8px' : '0' ?>; border-collapse: collapse; font-size: 12pt;">
        <tr>
            <td style="width: 85px; vertical-align: top;">Nomor</td>
            <td style="width: 15px; vertical-align: top;">:</td>
            <td style="vertical-align: top;"><?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="text-align: right; vertical-align: top; width: 230px;">Gorontalo, <?= htmlspecialchars($tglTeks, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Lampiran</td>
            <td style="vertical-align: top;">:</td>
            <td colspan="2" style="vertical-align: top;">-</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Hal</td>
            <td style="vertical-align: top;">:</td>
            <td colspan="2" style="vertical-align: top;"><b><?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></b></td>
        </tr>
    </table>

    <!-- Tujuan Surat / Penerima (jika diisi) -->
    <?php if (!empty($surat['tujuan'])): ?>
    <div style="margin-top: 14px; line-height: 1.35; font-size: 12pt;">
        <p>Kepada Yth.</p>
        <p><b><?= nl2br(htmlspecialchars($surat['tujuan'], ENT_QUOTES, 'UTF-8')) ?></b></p>
        <p>di Tempat</p>
    </div>
    <?php endif; ?>

    <!-- Paragraf Pembuka -->
    <p class="pembuka" style="margin-top: 14px; text-align: justify; line-height: 1.35; font-size: 12pt;">
        Dengan hormat,
    </p>

    <!-- Badan / Isi Surat -->
    <div class="word-canvas-body" style="margin-top: 6px;">
        <?php
        $isHtml = (strpos($rawBody, '<table') !== false ||
                   strpos($rawBody, '<ol') !== false ||
                   strpos($rawBody, '<ul') !== false ||
                   strpos($rawBody, '<img') !== false ||
                   strpos($rawBody, '<p') !== false ||
                   strpos($rawBody, '<div') !== false);

        if ($isHtml) {
            echo $rawBody;
        } else {
            $cleanText = str_replace(["\r\n", "\r"], "\n", $rawBody);
            $cleanText = str_replace('||', "\n\n", $cleanText);
            $paragraphs = explode("\n\n", $cleanText);

            foreach ($paragraphs as $p) {
                $p = trim($p);
                if ($p !== '') {
                    echo '<p style="text-align: justify; text-indent: 2.5em; margin-top: 8px; line-height: 1.35; font-size: 12pt;">'
                        . nl2br(htmlspecialchars($p, ENT_QUOTES, 'UTF-8'))
                        . '</p>';
                }
            }
        }
        ?>
    </div>

    <!-- Paragraf Penutup -->
    <?php
    $penutupTeks = !empty($surat['penutup'])
        ? $surat['penutup']
        : 'Demikian surat ini disampaikan. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.';
    ?>
    <p class="penutup" style="text-align: justify; margin-top: 12px; line-height: 1.35; font-size: 12pt;">
        <?= htmlspecialchars($penutupTeks, ENT_QUOTES, 'UTF-8') ?>
    </p>

    <!-- Blok Tanda Tangan -->
    <table class="ttd-wrap" style="width: 100%; margin-top: 22px; font-size: 12pt; border-collapse: collapse;">
        <tr>
            <td style="width: 52%;"></td>
            <td style="width: 48%;"><?= htmlspecialchars($surat['ttd_jabatan'], ENT_QUOTES, 'UTF-8') ?>,</td>
        </tr>
        <tr class="ttd-place" style="height: 60px;">
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b><u><?= htmlspecialchars($surat['ttd_nama'], ENT_QUOTES, 'UTF-8') ?></u></b></td>
        </tr>
        <tr>
            <td></td>
            <td>NIP. <?= htmlspecialchars($surat['ttd_nip'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    </table>

    <!-- Blok Tembusan (jika ada) -->
    <?php
    $tembusanRaw = trim((string)($surat['tembusan'] ?? ''));
    if ($tembusanRaw !== ''):
        $barisTembusan = array_filter(array_map('trim', explode("\n", str_replace(["\r\n", "\r"], "\n", $tembusanRaw))));
        if (!empty($barisTembusan)):
    ?>
    <div class="tembusan" style="margin-top: 14px; font-size: 11pt; line-height: 1.3;">
        <p><b>Tembusan Yth. :</b></p>
        <ol style="margin-left: 20px; padding-left: 0; margin-top: 3px;">
            <?php foreach ($barisTembusan as $tb):
                $cleanTb = preg_replace('/^\d+[\.\)]\s*/', '', $tb);
            ?>
                <li><?= htmlspecialchars($cleanTb, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php
        endif;
    endif;
    ?>
<?php endif; ?>

<?php
}
