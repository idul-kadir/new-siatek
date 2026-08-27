# Konvensi Coding PHP Native - Instruksi untuk AI

> Cara pakai: paste seluruh isi file ini ke bagian "custom instructions" / "system prompt" /
> "project instructions" di AI coding assistant yang kamu pakai (ChatGPT custom instructions,
> project knowledge, rules file, dsb). Instruksi ini WAJIB diikuti setiap kali AI membuat atau
> mengedit halaman backend PHP native di proyek ini. JANGAN diganti dengan gaya lain (PDO, OOP,
> MVC, dsb) kecuali diminta eksplisit.

# PHP Native CRUD Convention

Skill ini mendefinisikan gaya coding PHP native (tanpa framework) yang harus selalu diikuti AI saat membuat atau mengedit halaman backend di proyek user. Gaya ini sudah dipahami dan dipakai user, jadi JANGAN diganti dengan gaya lain (PDO, OOP, MVC, dsb) kecuali user secara eksplisit memintanya.

## Struktur file per halaman

Setiap halaman/fitur (misalnya halaman "arsip", "pegawai", dst) minimal punya:

```
halaman-arsip/
├── index.php        (atau nama tampilan lain — form + tabel data)
└── proses.php       (WAJIB — semua proses tambah/ubah/hapus untuk halaman ini)
```

Dan di level project ada satu file induk:

```
function.php   (WAJIB — kumpulan fungsi yang dipakai di LEBIH DARI SATU halaman)
```

### Aturan penempatan fungsi

- Fungsi yang dipakai di **beberapa halaman** → tulis di `function.php` (induk), lalu di-include/require di setiap `proses.php` yang butuh.
- Fungsi yang **hanya dipakai di satu halaman itu saja** → cukup ditulis langsung di `proses.php` halaman tersebut, tidak perlu masuk `function.php`.
- Di dalam file manapun (`proses.php` maupun `function.php`), **semua function diletakkan di bagian PALING BAWAH file**, setelah semua blok proses `if(isset($_POST[...]))`.

## Pola proses.php

Setiap aksi (tambah, ubah, hapus, dll) dipisahkan dalam blok `if(isset($_POST['nama-aksi']))` sendiri-sendiri. Nama key POST pakai format `aksi-data-entitas`, contoh `tambah-data-arsip`, `edit-data-arsip`, `hapus-data-arsip`.

**Semua request ke `proses.php` dikirim lewat AJAX (jQuery `$.ajax`/`$.post`), bukan submit form biasa.** Karena itu `proses.php` TIDAK PERNAH echo HTML/JS (tidak ada `<script>alert()</script>`, tidak ada `document.location.href` dari PHP). `proses.php` hanya boleh echo satu hal: **JSON**, lewat `echo json_encode([...])`. Semua urusan tampilan (alert, redirect, reload tabel) dikerjakan di JS pemanggilnya lewat `success:`.

Format JSON standar: `['status' => true/false, 'pesan' => '...']`. Boleh ditambah key lain (misal `'data' => [...]`) kalau halaman itu butuh, tapi `status` dan `pesan` selalu ada.

Contoh lengkap `proses.php`:

```php
<?php
// require function induk (kalau proses.php ini butuh fungsi bersama)
require_once '../function.php';

// ================= TAMBAH DATA ARSIP =================
if(isset($_POST['tambah-data-arsip'])){
    // ambil & bersihkan input dari form
    $nomor   = bersihkan($_POST['nomor']);
    $perihal = bersihkan($_POST['perihal']);
    $tanggal = bersihkan($_POST['tanggal']);

    // proses simpan ke database
    $simpan = query("INSERT INTO arsip (nomor, perihal, tanggal) VALUES ('$nomor', '$perihal', '$tanggal')");

    // response ke AJAX selalu JSON, jangan pernah echo HTML/script
    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal ditambahkan']);
    }
}

// ================= EDIT DATA ARSIP =================
if(isset($_POST['edit-data-arsip'])){
    $id      = bersihkan($_POST['id']);
    $nomor   = bersihkan($_POST['nomor']);
    $perihal = bersihkan($_POST['perihal']);
    $tanggal = bersihkan($_POST['tanggal']);

    $update = query("UPDATE arsip SET nomor='$nomor', perihal='$perihal', tanggal='$tanggal' WHERE id='$id'");

    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal diubah']);
    }
}

// ================= HAPUS DATA ARSIP =================
if(isset($_POST['hapus-data-arsip'])){
    $id = bersihkan($_POST['id']);

    $hapus = query("DELETE FROM arsip WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal dihapus']);
    }
}

// ================= FUNCTION KHUSUS HALAMAN INI =================
// taruh function yang HANYA dipakai di halaman arsip di sini, contoh:
// function cek_nomor_arsip_ganda($nomor){
//     $cek = query("SELECT * FROM arsip WHERE nomor = '$nomor'");
//     return mysqli_num_rows($cek) > 0;
// }
?>
```

Contoh pemanggilan dari sisi JS (jQuery AJAX) di halaman tampilan, misalnya `arsip.js` atau langsung di `index.php`:

```js
// TAMBAH DATA ARSIP lewat AJAX
$('#form-tambah-arsip').on('submit', function(e){
    e.preventDefault(); // supaya form tidak reload halaman

    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: $(this).serialize() + '&tambah-data-arsip=1', // key aksi ikut dikirim
        dataType: 'json',
        success: function(respon){
            if(respon.status){
                alert(respon.pesan);
                muat_ulang_tabel_arsip(); // reload HANYA konten tabel, bukan seluruh halaman
            } else {
                alert(respon.pesan);
            }
        },
        error: function(){
            alert('Terjadi kesalahan pada server');
        }
    });
});

// function ini yang bertugas reload konten tabel saja, dipanggil ulang tiap ada perubahan data
function muat_ulang_tabel_arsip(){
    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: { 'ambil-tabel-arsip': 1 },
        success: function(html){
            $('#tabel-arsip').html(html); // ganti isi div tabel saja, sisa halaman tidak ikut reload
        }
    });
}
```

## Pola function.php (induk)

`function.php` hanya berisi koneksi database + fungsi-fungsi yang dipakai lintas halaman. Contoh:

```php
<?php
// ================= KONEKSI DATABASE =================
$koneksi = mysqli_connect('localhost', 'root', '', 'nama_database');

// ================= FUNCTION QUERY =================
// dipakai di semua halaman untuk menjalankan query SQL
function query($sql){
    global $koneksi;
    $query = mysqli_query($koneksi, $sql);
    return $query;
}

// ================= FUNCTION BERSIHKAN INPUT =================
// dipakai di semua halaman untuk membersihkan input dari user sebelum masuk query
function bersihkan($data){
    global $koneksi;
    $data = trim($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

// ================= FUNCTION LAIN YANG DIPAKAI DI BEBERAPA HALAMAN =================
// tambahkan di sini kalau ada fungsi baru yang dipakai lebih dari 1 halaman
?>
```

## Pola upload file (WAJIB — semua fitur upload file di project ini pakai pola ini)

Semua file yang diunggah user (foto, dokumen, lampiran, dsb) **TIDAK BOLEH** disimpan langsung ke tabel entitasnya (misal tabel `arsip`, `pegawai`, dst) sebagai file fisik. Aturannya:

- **File fisik** (hasil upload) selalu disimpan di folder `file/` yang ada di **root utama** project (sejajar dengan `function.php`), bukan di folder halaman masing-masing.
- **Data file** (nama asli, nama file di server, ekstensi, dsb) disimpan ke tabel `file` (tabel ini sudah ada di database, jangan buat ulang strukturnya — sesuaikan nama kolom pada contoh di bawah dengan struktur tabel `file` yang sudah ada kalau berbeda).
- Tabel entitas lain (misal `arsip`, `pegawai`) **HANYA** menyimpan **id file** (foreign key ke tabel `file`), **TIDAK PERNAH** menyimpan nama file atau path file langsung di tabel entitas tersebut.
- Karena dipakai di banyak halaman (setiap halaman yang punya fitur upload), function upload wajib ditulis di `function.php` (induk), bukan di `proses.php` masing-masing halaman.

### Function `upload_file()` di `function.php`

Function ini menerima array file dari `$_FILES`, memindahkan file fisik ke folder `file/` di root, menyimpan datanya ke tabel `file`, lalu **mengembalikan id file** yang dipakai tabel entitas lain sebagai referensi.

Id file dibuat dengan menggabungkan **string acak** + **waktu unix (`time()`)**, supaya praktis mustahil ada id yang sama (bentrok/duplikat):

```php
// ================= FUNCTION UPLOAD FILE =================
// dipakai di semua halaman yang punya fitur upload file (foto, dokumen, lampiran, dll)
// file fisik disimpan ke folder "file" di root project, datanya disimpan ke tabel "file"
// return: id file (kalau berhasil) untuk disimpan di tabel entitas yang butuh, atau false (kalau gagal)
function upload_file($file){
    global $koneksi;

    // pastikan ada file yang diunggah dan tidak ada error dari sisi PHP
    if(!isset($file['tmp_name']) || $file['error'] != 0){
        return false;
    }

    // ambil nama asli & ekstensi file dari user
    $nama_asli = $file['name'];
    $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

    // buat id file yang praktis mustahil sama dengan yang lain:
    // gabungan waktu unix (time()) + string acak (random_bytes -> hex)
    $id_file = time() . '_' . bin2hex(random_bytes(8));

    // nama file fisik yang disimpan di server = id_file + ekstensi asli
    $nama_file = $id_file . '.' . $ekstensi;

    // folder tujuan SELALU folder "file" di root project.
    // pakai __DIR__ (lokasi function.php) supaya path selalu tepat ke root,
    // berapa pun dalamnya proses.php pemanggil di dalam subfolder halaman.
    $folder_tujuan = __DIR__ . '/file/' . $nama_file;

    // pindahkan file dari folder sementara (tmp) ke folder file/ di root
    $upload = move_uploaded_file($file['tmp_name'], $folder_tujuan);

    if(!$upload){
        return false;
    }

    // simpan data file ke tabel "file" (sesuaikan nama kolom dengan tabel file yang sudah ada)
    $nama_asli_bersih = bersihkan($nama_asli);
    $nama_file_bersih = bersihkan($nama_file);
    $ekstensi_bersih  = bersihkan($ekstensi);

    $simpan = query("INSERT INTO file (id, nama_asli, nama_file, ekstensi) VALUES ('$id_file', '$nama_asli_bersih', '$nama_file_bersih', '$ekstensi_bersih')");

    if(!$simpan){
        return false;
    }

    // kembalikan id file, ini yang disimpan di tabel entitas lain (misal kolom id_file)
    return $id_file;
}
```

### Cara pakai di `proses.php` halaman manapun

Karena `upload_file()` ada di `function.php`, `proses.php` yang butuh upload tinggal `require_once '../function.php';` (sudah wajib ada di setiap `proses.php`) lalu panggil function-nya sebelum insert/update ke tabel entitas:

```php
// ================= TAMBAH DATA ARSIP (DENGAN LAMPIRAN FILE) =================
if(isset($_POST['tambah-data-arsip'])){
    $nomor   = bersihkan($_POST['nomor']);
    $perihal = bersihkan($_POST['perihal']);

    // upload file lampiran dulu (kalau user memang mengunggah file)
    $id_file = null;
    if(isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0){
        $id_file = upload_file($_FILES['lampiran']);

        // kalau upload gagal, hentikan proses & beri tahu lewat JSON
        if($id_file === false){
            echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
            exit;
        }
    }

    // simpan ke tabel arsip: HANYA id_file yang disimpan, bukan nama/path file
    $simpan = query("INSERT INTO arsip (nomor, perihal, id_file) VALUES ('$nomor', '$perihal', " . ($id_file ? "'$id_file'" : "NULL") . ")");

    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal ditambahkan']);
    }
}
```

Di sisi tampilan (`index.php`), form yang punya input file wajib pakai `enctype="multipart/form-data"` dan dikirim lewat `FormData` (bukan `.serialize()` biasa) karena AJAX untuk upload file butuh `FormData`, `processData: false`, dan `contentType: false`:

```js
$('#form-tambah-arsip').on('submit', function(e){
    e.preventDefault();

    // pakai FormData karena ada input file, tidak bisa pakai .serialize() biasa
    var data = new FormData(this);
    data.append('tambah-data-arsip', 1);

    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: data,
        contentType: false, // wajib false, biar browser yang atur boundary multipart
        processData: false, // wajib false, biar FormData tidak diubah jadi string
        dataType: 'json',
        success: function(respon){
            if(respon.status){
                alert(respon.pesan);
                muat_ulang_tabel_arsip();
            } else {
                alert(respon.pesan);
            }
        },
        error: function(){
            alert('Terjadi kesalahan pada server');
        }
    });
});
```

## Pola query & fetch data

- Untuk membaca data selalu pakai `query()`:
  ```php
  $ambil_data = query("SELECT * FROM arsip ORDER BY id DESC");
  ```
- Untuk menampilkan hasilnya selalu pakai `while` + `mysqli_fetch_assoc`:
  ```php
  <?php while($data = mysqli_fetch_assoc($ambil_data)){ ?>
  ```

## Aturan HTML di dalam PHP (WAJIB — sering dilanggar, hati-hati)

**JANGAN PERNAH** menulis tag HTML **atau JS** lewat `echo` di PHP — ini termasuk di `proses.php`. Satu-satunya hal yang boleh di-`echo` dari `proses.php` adalah JSON lewat `json_encode()`. Tidak ada `echo "<script>...</script>"`, tidak ada `document.location.href` dari PHP, tidak ada alert dari PHP. Semua efek visual (alert, reload, ganti tampilan) dikerjakan JS di sisi client lewat `success:` callback AJAX.

Untuk file tampilan (`index.php` dsb) yang berisi HTML biasa, aturannya: Selalu tutup tag PHP dulu (`?>`), tulis HTML biasa, lalu buka lagi (`<?php`) kalau perlu logic lagi.

Contoh yang BENAR (looping tabel data):

```php
<?php while($data = mysqli_fetch_assoc($ambil_data)){ ?>
<tr>
    <td><?= $data['nip']; ?></td>
    <td><?= $data['nama']; ?></td>
    <td><?= $data['jabatan']; ?></td>
    <td>
        <a href="edit.php?id=<?= $data['id']; ?>" class="btn btn-warning">Edit</a>
        <a href="proses.php?hapus=<?= $data['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
    </td>
</tr>
<?php } ?>
```

Contoh yang SALAH (jangan pernah lakukan ini):

```php
<?php
while($data = mysqli_fetch_assoc($ambil_data)){
    echo "<tr><td>".$data['nip']."</td><td>".$data['nama']."</td></tr>"; // ❌ SALAH, jangan echo HTML
}
?>
```

Aturan echo variabel:
- Untuk menampilkan 1 variabel PHP di tengah HTML, selalu pakai short echo tag `<?= $variabel; ?>`, bukan `<?php echo $variabel; ?>` dan bukan digabung ke dalam string `echo`.
- Untuk atribut HTML dinamis (misal `value`, `href`) juga tetap pakai `<?= ?>`, contoh: `<input type="text" name="nama" value="<?= $data['nama']; ?>">`.

## Aturan reload konten (WAJIB — jangan reload/redirect seluruh halaman)

Setiap ada perubahan data (tambah/edit/hapus), yang di-reload **HANYA konten yang berubah** (misalnya isi tabel di dalam sebuah `<div>`), **BUKAN seluruh halaman**. Ini artinya:

- **DILARANG** pakai `location.reload()` atau `window.location.reload()` setelah AJAX sukses.
- **DILARANG** redirect pakai URL penuh/hardcoded seperti `window.location.href = 'http://localhost/nama-project/administrator/index.php?page=biodata'`. Pola redirect semacam ini sampah — jangan pernah dipakai.
- Yang benar: setelah AJAX sukses, panggil function JS terpisah (misalnya `muat_ulang_tabel_x()`) yang melakukan AJAX lagi ke `proses.php` untuk ambil ulang HANYA bagian konten yang berubah (misal isi `<tbody>` tabel), lalu suntikkan hasilnya ke elemen terkait pakai `.html()` atau `.load()`. Sisa halaman (header, sidebar, form, dsb) tidak ikut reload sama sekali.
- Kalau halaman ini pakai sistem SPA-ish dengan query `?page=nama-halaman` (load konten lewat AJAX ke dalam satu container utama), maka pindah konten pun harus tetap lewat AJAX yang ganti isi container itu, bukan lewat `window.location.href` yang bikin seluruh halaman (termasuk header/sidebar) reload dari server.

## Aturan URL & path dinamis (WAJIB — jangan path/URL statis yang rusak saat deploy)

Path URL yang mengarah ke file/folder/halaman proyek **DILARANG di-hardcode** sebagai string statis yang mengunci ke satu lokasi (misal `/redesain-siatek/...` atau `http://localhost/redesain-siatek/...`). Saat aplikasi di-deploy ke server (di docroot langsung, di subfolder, atau nama folder berubah), path statis ini pasti rusak. Aturannya:

- **Path file yang disimpan di lokasi proyek** (contoh: folder `file/` di root) harus di-resolve ke **URL dinamis**, dihitung dari posisi proyek terhadap `DOCUMENT_ROOT` server. Contoh pola helper `base_url()` yang menghitung otomatis (deploy di docroot -> `''`, di subfolder -> `/nama-subfolder`):

  ```php
  // ================= FUNCTION BASE URL =================
  // Hitung base path URL proyek dari lokasi file ini terhadap DOCUMENT_ROOT server.
  // Otomatis menyesuaikan saat deploy: di docroot -> '', di subfolder -> '/nama-subfolder'.
  function base_url(){
    $root = str_replace('\\', '/', rtrim((string)dirname(__DIR__), '/')); // folder yang jadi "root proyek"
    $doc  = str_replace('\\', '/', rtrim((string)(getenv('DOCUMENT_ROOT') ?: ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/'));
    if ($doc === ''){
      return ''; // tidak dikenal (mis. CLI): fallback aman kosong
    }
    if (strpos($root . '/', $doc . '/') === 0){
      return substr($root, strlen($doc));
    }
    return '';
  }
  ```

  Lalu setiap URL file memakai helper itu, contoh: `return base_url() . '/file/' . $nama_file;`.

- **Path fisik penyimpanan file** (untuk `move_uploaded_file`/`file_put_contents`) memakai `__DIR__` (atau `dirname(__DIR__)` dari subfolder), **bukan** string path absolut yang dihardcode. `__DIR__` selalu mengikuti lokasi file di server, jadi otomatis benar di mana pun proyek diletakkan.

- Untuk redirect/login `header('Location: ...')` maupun ajax `target`, kalau memang butuh base path proyek, hitung dari helper di atas (atau simpan di konstanta yang diambil dari helper), **jangan** menulis `/redesain-siatek/...` mentah-mentah.

- Pengecualian: URL eksternal (mis. CDN, endpoint Google OAuth) memang mutlak web absolute — itu sah. Yang dilarang adalah path internal proyek yang di-hardcode.

## Komentar wajib

Setiap blok proses (`if(isset($_POST[...]))`) dan setiap function diberi komentar singkat di atasnya yang menjelaskan tujuannya, supaya konsep alurnya gampang dibaca ulang. Gunakan header komentar seperti `// ================= TAMBAH DATA X =================` untuk memisahkan tiap blok secara visual, dan komentar satu baris untuk menjelaskan baris-baris penting (misalnya kenapa sebuah input dibersihkan, apa yang di-return sebuah function).

## Checklist sebelum selesai menulis/mengedit halaman PHP

Sebelum menganggap kode selesai, AI harus mengecek ulang:

- [ ] Ada file `proses.php` terpisah untuk halaman ini (bukan logic proses dicampur di file tampilan)
- [ ] Semua aksi (tambah/edit/hapus/dll) dipisah dalam blok `if(isset($_POST['aksi-data-entitas']))` sendiri-sendiri
- [ ] Semua function ada di paling bawah file (baik `proses.php` maupun `function.php`)
- [ ] Function yang dipakai >1 halaman sudah dipindah ke `function.php`, bukan diduplikasi di tiap `proses.php`
- [ ] `proses.php` sudah `require`/`include` `function.php` kalau butuh fungsi bersama
- [ ] Semua input `$_POST` dibungkus `bersihkan()` sebelum dipakai di query
- [ ] Semua query pakai `query($sql)`, bukan `mysqli_query()` langsung
- [ ] Semua looping data pakai `while($data = mysqli_fetch_assoc($ambil_data))`
- [ ] TIDAK ADA tag HTML/JS yang di-`echo` dari PHP di manapun (termasuk `proses.php`) — semua HTML ditulis dengan menutup tag `?>` lalu buka lagi `<?php`
- [ ] Echo variabel tunggal ke HTML pakai `<?= $data['x']; ?>`, bukan `<?php echo ?>` atau digabung string
- [ ] Setiap blok proses di `proses.php` diakhiri `echo json_encode(['status' => ..., 'pesan' => ...])`, TIDAK ADA `<script>alert()</script>` atau `document.location.href` dari PHP
- [ ] Request ke `proses.php` dari sisi tampilan pakai jQuery AJAX (`$.ajax`/`$.post`) dengan `dataType: 'json'`, dan hasilnya ditangani di `success:` (alert, reload, dsb dikerjakan di JS, bukan di PHP)
- [ ] Setelah AJAX sukses, yang direload HANYA konten yang berubah (misalnya tabel) lewat AJAX lagi — TIDAK ADA `location.reload()` dan TIDAK ADA redirect pakai `window.location.href` ke URL halaman penuh (misal `index.php?page=...`)
- [ ] Ada komentar penjelas di atas tiap blok proses dan tiap function
- [ ] Kalau halaman ini punya fitur upload file: file fisik disimpan ke folder `file/` di root (bukan folder halaman), datanya disimpan ke tabel `file`, dan tabel entitas hanya menyimpan **id file**, bukan nama/path file
- [ ] Upload file selalu lewat function `upload_file()` di `function.php` (induk), tidak ditulis ulang di tiap `proses.php`
- [ ] Form yang ada input file pakai `enctype`/`FormData` + `contentType: false, processData: false` di AJAX-nya
- [ ] TIDAK ADA path/URL internal proyek yang di-hardcode statis (mis. `/redesain-siatek/...` atau `http://localhost/...`) — semua URL/path file proyek di-resolve dinamis via helper `base_url()` (dihitung dari `DOCUMENT_ROOT`) dan path fisik pakai `__DIR__`, supaya tidak rusak saat deploy ke server

## Aturan perilaku AI sendiri saat mengerjakan tugas ini (WAJIB — sering dilanggar)

Tiga masalah ini bukan soal gaya PHP, tapi soal cara AI bekerja, dan harus dihindari:

1. **Jangan sampai ada karakter rusak/mojibake** (contoh: `â€™`, `â€œ`, `â€`, `â€"`) muncul di kode atau komentar yang ditulis. Ini biasanya muncul kalau tanda kutip pintar ("smart quotes"), dash panjang, atau karakter non-ASCII lain ditulis/di-copy dengan encoding yang salah. Solusinya: AI harus selalu pakai tanda kutip lurus biasa (petik satu dan petik dua) dan tanda hubung biasa di dalam kode maupun komentar PHP -- jangan pernah pakai smart quotes atau em dash di dalam file .php. Sebelum mengirim file final, cek ulang tidak ada karakter aneh semacam itu.

2. **Jangan bilang "akan saya perbaiki" lalu berhenti tanpa benar-benar mengerjakannya.** Kalau AI mengidentifikasi ada yang perlu direvisi (baik dari feedback user maupun temuan sendiri), langsung lakukan revisinya di respons yang sama -- jangan cuma menjanjikan dan berhenti. Juga jangan mengulang-ulang langkah yang sama (misalnya baca-tulis file bolak-balik) tanpa progres nyata, karena ini boros token dan bikin user harus dorong ulang berkali-kali. Kerjakan sampai selesai dalam satu jalan, baru laporkan hasilnya ke user.

3. **DILARANG mengambil keputusan sepihak yang berisiko fatal terhadap program tanpa izin eksplisit dari user, sekecil apapun keputusan itu terlihat di mata AI.** Yang dimaksud keputusan berisiko fatal, termasuk tapi tidak terbatas pada:
   - Melakukan **rollback** (baik lewat perintah git seperti `git reset`/`git checkout`/`git revert`, maupun dengan cara menimpa/mengembalikan file ke versi sebelumnya secara manual) tanpa diminta.
   - **Menghapus atau menimpa (overwrite) file, folder, tabel, atau kolom database** yang sudah ada, termasuk data di dalamnya (`DROP TABLE`, `TRUNCATE`, `DELETE FROM ... ` tanpa `WHERE`, menghapus file lewat kode, dsb).
   - **Menulis ulang total (rewrite) sebuah file besar atau seluruh halaman/fitur** padahal user hanya minta perbaikan/tambahan kecil di bagian tertentu.
   - **Mengubah struktur database** (menambah/menghapus/mengganti nama kolom atau tabel) atau **mengubah konvensi/pola coding project** (misalnya pola di file instruksi ini) di luar apa yang diminta.
   - Tindakan apa pun yang **sulit atau tidak bisa dibalik** (irreversible) dan **berdampak ke bagian program lain di luar scope permintaan user saat itu**.

   AI seringkali menganggap tindakan semacam ini "kecil" atau "solusi cepat", padahal bagi user dampaknya bisa fatal dan bikin program jadi kacau. Karena itu: kalau AI merasa perlu melakukan salah satu hal di atas untuk menyelesaikan masalah, AI **WAJIB berhenti dulu, jelaskan ke user apa yang mau dilakukan beserta risikonya, dan minta konfirmasi eksplisit** sebelum benar-benar mengeksekusinya -- jangan langsung dieksekusi lalu baru dilaporkan setelah kejadian. Kalau ada cara lain yang lebih kecil/lebih aman (scoped) untuk mencapai tujuan yang sama tanpa rollback/hapus/rewrite besar, AI harus tawarkan cara itu duluan sebagai opsi utama.

## Template lengkap

### Template `proses.php` (contoh untuk halaman baru)

```php
<?php
/*
    TEMPLATE proses.php
    ---------------------------------------------
    Ganti "ENTITAS" dengan nama data halaman ini (contoh: arsip, pegawai, siswa)
    Ganti nama tabel & kolom sesuai struktur database masing-masing.

    PENTING: file ini dipanggil lewat AJAX (jQuery), BUKAN submit form biasa.
    Karena itu proses.php TIDAK PERNAH echo HTML/JS (tidak ada <script>alert()</script>,
    tidak ada document.location.href). Satu-satunya yang boleh di-echo adalah JSON
    lewat json_encode(). Urusan alert/reload/tampilan dikerjakan di JS pemanggil
    lewat success: callback.
    ---------------------------------------------
*/

// require function induk, karena butuh query() dan bersihkan()
require_once '../function.php';

// ================= TAMBAH DATA ENTITAS =================
if(isset($_POST['tambah-data-entitas'])){
    // ambil & bersihkan semua input dari form
    $kolom1 = bersihkan($_POST['kolom1']);
    $kolom2 = bersihkan($_POST['kolom2']);

    // simpan ke database
    $simpan = query("INSERT INTO nama_tabel (kolom1, kolom2) VALUES ('$kolom1', '$kolom2')");

    // response ke AJAX selalu JSON: {status, pesan}
    if($simpan){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal ditambahkan']);
    }
}

// ================= EDIT DATA ENTITAS =================
if(isset($_POST['edit-data-entitas'])){
    $id     = bersihkan($_POST['id']);
    $kolom1 = bersihkan($_POST['kolom1']);
    $kolom2 = bersihkan($_POST['kolom2']);

    $update = query("UPDATE nama_tabel SET kolom1='$kolom1', kolom2='$kolom2' WHERE id='$id'");

    if($update){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil diubah']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal diubah']);
    }
}

// ================= HAPUS DATA ENTITAS =================
if(isset($_POST['hapus-data-entitas'])){
    $id = bersihkan($_POST['id']);

    $hapus = query("DELETE FROM nama_tabel WHERE id='$id'");

    if($hapus){
        echo json_encode(['status' => true, 'pesan' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'pesan' => 'Data gagal dihapus']);
    }
}

// ================= FUNCTION KHUSUS HALAMAN INI =================
// taruh di sini HANYA function yang dipakai di halaman ini saja.
// kalau ternyata dibutuhkan di halaman lain juga, pindahkan ke function.php (induk).

// contoh: cek apakah kolom1 sudah pernah dipakai sebelum insert
// function cek_kolom1_ganda($kolom1){
//     $cek = query("SELECT * FROM nama_tabel WHERE kolom1 = '$kolom1'");
//     return mysqli_num_rows($cek) > 0;
// }
?>

<?php /*
================= CONTOH PEMANGGILAN DARI JS (taruh di index.php / file js terpisah) =================

$('#form-tambah-entitas').on('submit', function(e){
    e.preventDefault();

    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: $(this).serialize() + '&tambah-data-entitas=1',
        dataType: 'json',
        success: function(respon){
            if(respon.status){
                alert(respon.pesan);
                muat_ulang_tabel_entitas(); // reload HANYA konten tabel, JANGAN location.reload()
            } else {
                alert(respon.pesan);
            }
        },
        error: function(){
            alert('Terjadi kesalahan pada server');
        }
    });
});

// function terpisah untuk reload konten tabel saja lewat AJAX
function muat_ulang_tabel_entitas(){
    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: { 'ambil-tabel-entitas': 1 },
        success: function(html){
            $('#tabel-entitas').html(html); // ganti isi container tabel saja
        }
    });
}

*/ ?>

```

### Template `function.php` (induk)

```php
<?php
/*
    TEMPLATE function.php (INDUK)
    ---------------------------------------------
    File ini di-require/include oleh setiap proses.php di semua halaman.
    Hanya taruh di sini:
    1. Koneksi database
    2. Function query() dan bersihkan() (wajib ada di semua project)
    3. Function lain yang dipakai di LEBIH DARI SATU halaman
    Jangan taruh function yang hanya dipakai di 1 halaman saja di sini —
    itu cukup ditulis langsung di proses.php halaman tersebut.
    ---------------------------------------------
*/

// ================= KONEKSI DATABASE =================
$koneksi = mysqli_connect('localhost', 'root', '', 'nama_database');

// ================= FUNCTION QUERY =================
// dipakai di semua halaman untuk menjalankan query SQL (SELECT/INSERT/UPDATE/DELETE)
function query($sql){
    global $koneksi;
    $query = mysqli_query($koneksi, $sql);
    return $query;
}

// ================= FUNCTION BERSIHKAN INPUT =================
// dipakai di semua halaman untuk membersihkan input user sebelum masuk ke query,
// supaya aman dari SQL Injection dan spasi berlebih
function bersihkan($data){
    global $koneksi;
    $data = trim($data);                              // buang spasi di awal/akhir
    $data = mysqli_real_escape_string($koneksi, $data); // escape karakter berbahaya
    return $data;
}

// ================= FUNCTION UPLOAD FILE =================
// dipakai di semua halaman yang punya fitur upload file (foto, dokumen, lampiran, dll)
// file fisik disimpan ke folder "file" di root project, datanya disimpan ke tabel "file"
// return: id file (kalau berhasil) untuk disimpan di tabel entitas yang butuh, atau false (kalau gagal)
function upload_file($file){
    global $koneksi;

    // pastikan ada file yang diunggah dan tidak ada error dari sisi PHP
    if(!isset($file['tmp_name']) || $file['error'] != 0){
        return false;
    }

    // ambil nama asli & ekstensi file dari user
    $nama_asli = $file['name'];
    $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

    // buat id file yang praktis mustahil sama dengan yang lain:
    // gabungan waktu unix (time()) + string acak (random_bytes -> hex)
    $id_file = time() . '_' . bin2hex(random_bytes(8));

    // nama file fisik yang disimpan di server = id_file + ekstensi asli
    $nama_file = $id_file . '.' . $ekstensi;

    // folder tujuan SELALU folder "file" di root project.
    // pakai __DIR__ (lokasi function.php) supaya path selalu tepat ke root,
    // berapa pun dalamnya proses.php pemanggil di dalam subfolder halaman.
    $folder_tujuan = __DIR__ . '/file/' . $nama_file;

    // pindahkan file dari folder sementara (tmp) ke folder file/ di root
    $upload = move_uploaded_file($file['tmp_name'], $folder_tujuan);

    if(!$upload){
        return false;
    }

    // simpan data file ke tabel "file" (sesuaikan nama kolom dengan tabel file yang sudah ada)
    $nama_asli_bersih = bersihkan($nama_asli);
    $nama_file_bersih = bersihkan($nama_file);
    $ekstensi_bersih  = bersihkan($ekstensi);

    $simpan = query("INSERT INTO file (id, nama_asli, nama_file, ekstensi) VALUES ('$id_file', '$nama_asli_bersih', '$nama_file_bersih', '$ekstensi_bersih')");

    if(!$simpan){
        return false;
    }

    // kembalikan id file, ini yang disimpan di tabel entitas lain (misal kolom id_file)
    return $id_file;
}

// ================= FUNCTION LINTAS HALAMAN LAINNYA =================
// contoh: function yang dipakai di banyak halaman untuk generate nomor otomatis
// function buat_nomor_otomatis($tabel, $kolom){
//     $ambil = query("SELECT $kolom FROM $tabel ORDER BY id DESC LIMIT 1");
//     if(mysqli_num_rows($ambil) > 0){
//         $data = mysqli_fetch_assoc($ambil);
//         $nomor = (int) $data[$kolom] + 1;
//     } else {
//         $nomor = 1;
//     }
//     return $nomor;
// }
?>

```