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

<div class="kop">
    <img class="logo" src="/redesain-siatek/surat/img/logo.png" alt="Logo">
    <div class="brand">
        <p class="org">KEMENTERIAN PENDIDIKAN,KEBUDAYAAN,<br>
            RISET DAN TEKNOLOGI<br>
            UNIVERSITAS NEGERI GORONTALO<br>
            FAKULTAS TEKNIK<br>
            JURUSAN TEKNIK ELEKTRO DAN KOMPUTER</p>
        <p class="addr">Jalan B.J.Habibie Desa Moutong Kecamatan Tilongkabila Kab.Bone Bolango<br>
            Telp. (0435) 821125, Fax. (0435) 821752 Gorontalo<br>
            Laman :http:// www.ft.ung.ac.id</p>
    </div>
    <hr>
</div>