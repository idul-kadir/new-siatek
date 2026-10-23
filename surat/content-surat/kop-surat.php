<!-- ============================================================
     KOP SURAT — komponen (fragment) yang diload saat dibutuhkan.
     Data DUMMY.

     File konten yang MEMBUTUHKAN kop memanggilnya di bagian atas:
         include __DIR__ . '/kop-surat.php';   (dalam tag PHP)

     Konten yang TIDAK butuh kop cukup tidak memanggil file ini.

     Catatan: src logo memakai path absolut web
     (/redesain-siatek/surat/img/logo.png) agar tetap tampil
     walau surat dibuka lewat URL bersih /dokumen/<kategori>/<nim>.
     ============================================================ -->

<?php
$logoSrc = function_exists('base_url') ? (base_url() . '/surat/img/logo.png') : '/redesain-siatek/surat/img/logo.png';
?>
<div class="kop" id="cvKop" style="position: relative; margin-bottom: 4px; user-select: none;">
    <div class="kop-header" style="display: flex; align-items: center; justify-content: center; position: relative;">
        <img class="logo" src="<?= $logoSrc ?>" alt="Logo UNG" style="position: absolute; left: 0; top: 45%; transform: translateY(-50%); width: 22mm; height: auto;">
        <div class="brand" style="text-align: center; margin: 0 24mm; width: 100%;">
            <p class="org" style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.0;">KEMENTRIAN PENDIDIKAN TINGGI,<br>
                SAINS DAN TEKNOLOGI<br>
                UNIVERSITAS NEGERI GORONTALO<br>
                FAKULTAS TEKNIK<br>
                JURUSAN TEKNIK ELEKTRO DAN KOMPUTER</p>
            <p class="addr" style="font-family: 'Times New Roman', Times, serif; font-size: 11pt; font-style: italic; margin: 3px 0 0; line-height: 1.0;">jl. B.J. Habibie Desa Moutong Kec. Tilongkabila Kab. Bone Bolango<br>
                Telp. (0435) 821125, Fax. (0435) 821752 Gorontalo<br>
                Laman : http://www.ft.ung.ac.id</p>
        </div>
    </div>
    <hr style="border: 1.5px solid #000; margin: 4px 0 0;">
</div>