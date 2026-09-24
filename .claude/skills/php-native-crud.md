# PHP Native CRUD

Standar & aturan penulisan kode PHP native (tanpa framework) untuk aplikasi backend.

## Skill Metadata

- **Trigger:** When user asks to create, edit, or review PHP CRUD pages with SQL database operations.
- **Scope:** All PHP backend pages, `proses.php`, `function.php`, database interactions, file uploads, AJAX patterns, loading indicators, and toast notifications.
- **Dependencies:** None beyond existing project conventions (Tailwind CSS, jQuery, Font Awesome).

## Core Principles (Read First)

1. **Fokus Inti** — Kerjakan hanya yang diminta user. Jangan rapikan/refactor bagian lain yang tidak diminta.
2. **Scoped Fix** — Bug kecil → perbaiki bagian itu saja.
3. **Hemat Eksplorasi** — Jangan baca banyak file di luar yang relevan dengan persoalan.
4. **Asumsi Wajar** — Pilih penyebab paling masuk akal berdasarkan konteks. Jangan habiskan waktu bertanya hal-hal kecil.
5. **Respons Ringkas** — Langsung ke perbaikan/kode. Tidak perlu muter-muter menjelaskan hal yang sudah jelas dari kode yang ada.

## File Structure Rules

Every page/feature MUST have:
```
halaman-arsip/
├── index.php        (view: form + table data)
└── proses.php       (backend logic: all create/edit/delete via AJAX)
```

Global file:
```
function.php   (shared functions used across multiple pages)
```

**Function placement rules:**
- Functions used in **multiple pages** → write in `function.php` (shared), include/require in each `proses.php` that needs it.
- Functions used in **only one page** → write directly in that page's `proses.php`, do NOT put in `function.php`.
- In ANY file (`proses.php` or `function.php`), **all functions go at the BOTTOM**, after all `if(isset($_POST[...]))` blocks.

## Process File Pattern (`proses.php`)

### Critical Rules
- All requests to `proses.php` MUST be via AJAX (jQuery `$.ajax`/`$.post`), NOT regular form submit.
- `proses.php` **NEVER** echoes HTML/JS — no `<script>alert()</script>`, no `document.location.href` from PHP.
- `proses.php` only echoes **JSON** via `echo json_encode([...])`.
- All UI concerns (alerts, redirects, table reload) handled in JS caller via `success:` callback.

### Standard JSON Format
```php
echo json_encode(['status' => true/false, 'pesan' => '...']);
```
- `status` and `pesan` keys ALWAYS present.
- Can add other keys (e.g., `'data' => [...]`) if page needs it.

### Action Blocks
Each action separated into its own block:
```php
// ================= TAMBAH DATA ARSIP =================
if(isset($_POST['tambah-data-arsip'])){
    // ...
}

// ================= EDIT DATA ARSIP =================
if(isset($_POST['edit-data-arsip'])){
    // ...
}

// ================= HAPUS DATA ARSIP =================
if(isset($_POST['hapus-data-arsip'])){
    // ...
}
```

POST key format: `aksi-data-entitas` (e.g., `tambah-data-arsip`, `edit-data-arsip`, `hapus-data-arsip`).

## Shared Functions File Pattern (`function.php`)

Only contains:
1. Database connection
2. `query($sql)` — runs SQL queries
3. `bersihkan($data)` — sanitizes user input
4. Other shared functions used across multiple pages (e.g., `upload_file()`, `base_url()`)

```php
<?php
// ================= KONEKSI DATABASE =================
$koneksi = mysqli_connect('localhost', 'root', '', 'nama_database');

// ================= FUNCTION QUERY =================
function query($sql){
    global $koneksi;
    return mysqli_query($koneksi, $sql);
}

// ================= FUNCTION BERSIHKAN INPUT =================
function bersihkan($data){
    global $koneksi;
    $data = trim($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

// ================= FUNCTION LAINNYA =================
// Upload file, base URL, etc.
?>
```

## Query & Data Fetching Rules

- Read data: `query("SELECT * FROM tabel ORDER BY id DESC")`
- Display: `<?php while($data = mysqli_fetch_assoc($ambil_data)){ ?> ... <?php } ?>`
- Always use `query($sql)`, never `mysqli_query()` directly.
- Always use `while($data = mysqli_fetch_assoc(...))`, never `foreach` or other loop types for DB results.

## File Upload Rules (STRICT)

### Storage Pattern
- **Physical file** → save to `file/` folder in project root (alongside `function.php`).
- **File metadata** → save to `file` table (id, nama_asli, nama_file, ekstensi).
- **Entity tables** → store ONLY `id_file` (foreign key), NEVER filename or path directly.

### Upload Function (`upload_file()` in `function.php`)
```php
function upload_file($file){
    global $koneksi;
    
    if(!isset($file['tmp_name']) || $file['error'] != 0){
        return false;
    }
    
    $nama_asli = $file['name'];
    $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    
    // Unique ID: unix time + random hex
    $id_file = time() . '_' . bin2hex(random_bytes(8));
    $nama_file = $id_file . '.' . $ekstensi;
    $folder_tujuan = __DIR__ . '/file/' . $nama_file;
    
    $upload = move_uploaded_file($file['tmp_name'], $folder_tujuan);
    if(!$upload){
        return false;
    }
    
    // Save metadata to file table
    $nama_asli_bersih = bersihkan($nama_asli);
    $nama_file_bersih = bersihkan($nama_file);
    $ekstensi_bersih  = bersihkan($ekstensi);
    
    $simpan = query("INSERT INTO file (id, nama_asli, nama_file, ekstensi) VALUES ('$id_file', '$nama_asli_bersih', '$nama_file_bersih', '$ekstensi_bersih')");
    
    if(!$simpan){
        return false;
    }
    
    return $id_file;
}
```

### Usage in `proses.php`
```php
// Upload file (if user uploaded one)
$id_file = null;
if(isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0){
    $id_file = upload_file($_FILES['lampiran']);
    if($id_file === false){
        echo json_encode(['status' => false, 'pesan' => 'Upload file gagal']);
        exit;
    }
}

// Insert with FK only
$simpan = query("INSERT INTO arsip (nomor, perihal, id_file) VALUES ('$nomor', '$perihal', " . ($id_file ? "'$id_file'" : "NULL") . ")");
```

### Frontend Upload Form
```html
<form id="form-tambah-arsip" enctype="multipart/form-data">
    <!-- fields -->
    <input type="file" name="lampiran">
</form>
```

```javascript
$('#form-tambah-arsip').on('submit', function(e){
    e.preventDefault();
    var data = new FormData(this);
    data.append('tambah-data-arsip', 1);
    
    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: data,
        contentType: false,  // required for file upload
        processData: false,  // required for file upload
        dataType: 'json',
        success: function(respon){
            if(respon.status){
                toast(respon.pesan, 'success');
                muat_ulang_tabel_arsip();
            } else {
                toast(respon.pesan, 'error');
            }
        },
        complete: function() {
            setButtonLoading(btnSimpan, false);
        }
    });
});
```

## URL & Path Rules (Anti-Hardcode)

### Helper `base_url()` (in `function.php`)
```php
function base_url(){
    $root = str_replace('\\', '/', rtrim((string)dirname(__DIR__), '/'));
    $doc  = str_replace('\\', '/', rtrim((string)(getenv('DOCUMENT_ROOT') ?: ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/'));
    if ($doc === ''){
      return '';
    }
    if (strpos($root . '/', $doc . '/') === 0){
      return substr($root, strlen($doc));
    }
    return '';
}
```

### Rules
- **Internal paths** → calculate dynamically via `base_url()` or `__DIR__`.
- **Physical file paths** → use `__DIR__` (always correct regardless of deployment location).
- **Redirect/URL** → use `base_url()`, never hardcode `/redesain-siatek/...` or `http://localhost/...`.
- **Exception**: external URLs (CDN, OAuth) can be absolute web URLs.

### Examples
```php
// Correct: dynamic base URL
$return_url = base_url() . '/file/' . $nama_file;

// Correct: physical path
$folder_tujuan = __DIR__ . '/file/' . $nama_file;

// WRONG: hardcoded path
$return_url = '/redesain-siatek/file/' . $nama_file;
```

## HTML in PHP Rules (Common Violation!)

### DO NOT echo HTML/JS from PHP
```php
// WRONG - NEVER DO THIS
while($data = mysqli_fetch_assoc($ambil_data)){
    echo "<tr><td>" . $data['nama'] . "</td></tr>";
}
```

### CORRECT Pattern
```php
<?php while($data = mysqli_fetch_assoc($ambil_data)){ ?>
<tr>
    <td><?= $data['nama']; ?></td>
    <td>
        <button data-edit-open data-id="<?= $data['id']; ?>">Edit</button>
    </td>
</tr>
<?php } ?>
```

### Echo Rules
- Single variable in HTML: `<?= $data['x']; ?>` (short echo tag).
- Dynamic attributes: `<input value="<?= $data['nama']; ?>">`.
- NEVER: `<?php echo ?>` or concatenated strings for variables.

## Reload Content Rules (NOT Page Reload)

### Forbidden Patterns
- ❌ `location.reload()` / `window.location.reload()`
- ❌ `window.location.href = 'http://localhost/...'` (full URL redirect)
- ❌ Full page refresh after AJAX success

### Correct Pattern
```javascript
// Call separate function to reload only changed content
function muat_ulang_tabel_arsip(){
    $.ajax({
        url: 'proses.php',
        method: 'POST',
        data: { 'ambil-tabel-arsip': 1 },
        success: function(html){
            $('#tabel-arsip').html(html);
        }
    });
}
```

For SPA-like pages (`?page=nama-halaman`):
- Use AJAX to swap container content.
- NEVER `window.location.href` to full page URL.

## Notification Rules (Toast, Not Alert)

### Forbidden
- ❌ `window.alert()`
- ❌ `window.confirm()`
- ❌ `window.prompt()`

### Required: Toast Notification
```javascript
toast('Data berhasil ditambahkan', 'success');
toast('Upload file gagal', 'error');
toast('Peringatan: ukuran file melebihi batas', 'info');
```

- Toast appears in top-right corner.
- Auto-dismisses after 3-5 seconds.
- FontAwesome icon.
- Gradient background (green=success, red=error, blue=info).
- Slide-in animation.
- Close button.

### Confirm Dialog
Use global `konfirmasiHapus(nama, callback)` for dangerous actions (delete).

## Loading Indicator Rules (Required)

### Button Loading Pattern
```html
<button type="submit" class="btn-orange" data-text="Simpan Dokumen">Simpan Dokumen</button>
```

```javascript
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

$('#formX').on('submit', function(e) {
    e.preventDefault();
    var btnSimpan = this.querySelector('button[type="submit"]');
    setButtonLoading(btnSimpan, true);
    
    $.ajax({
        url: PROSES_URL,
        method: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(respon) {
            toast(respon.pesan, respon.status ? 'success' : 'error');
            if (respon.status) {
                // handle success
            }
        },
        complete: function() {
            setButtonLoading(btnSimpan, false);
        }
    });
});
```

### Table/List Loading Pattern
```javascript
function muatUlangTabel() {
    var container = document.getElementById('tabelWrap');
    container.innerHTML = '<div class="acc-loading"><div class="spinner-slate mx-auto"></div><p class="mt-3">Memuat data...</p></div>';
    
    $.ajax({
        url: PROSES_URL,
        method: 'POST',
        data: { 'ambil-tabel-X': 1 },
        dataType: 'json',
        success: function(respon) {
            if (respon.status) {
                container.innerHTML = respon.data;
                render();
            } else {
                container.innerHTML = '<div class="acc-empty text-center">Gagal memuat data</div>';
            }
        },
        error: function() {
            container.innerHTML = '<div class="acc-empty text-center">Terjadi kesalahan pada server</div>';
        }
    });
}
```

### Required CSS
```css
.btn-loader { display: inline-flex; align-items: center; gap: 0.5rem; }
.btn-loader .spinner { width: 0.9rem; height: 0.9rem; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
.spinner-slate { width: 1rem; height: 1rem; border: 2px solid rgba(15,23,42,0.2); border-top-color: #1a365d; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.is-loading { opacity: 0.7; cursor: not-allowed !important; pointer-events: none; }
```

## Checklist Before Finishing (STRICT)

Before considering a PHP page complete, verify:

- [ ] Separate `proses.php` exists (logic not mixed with view)
- [ ] All actions separated into `if(isset($_POST['aksi-data-entitas']))` blocks
- [ ] All functions at bottom of file (both `proses.php` and `function.php`)
- [ ] Shared functions in `function.php`, not duplicated in each `proses.php`
- [ ] `proses.php` requires/includes `function.php` if needed
- [ ] All `$_POST` inputs wrapped in `bersihkan()` before query
- [ ] All queries use `query($sql)`, not `mysqli_query()` directly
- [ ] All loops use `while($data = mysqli_fetch_assoc($ambil_data))`
- [ ] NO HTML/JS echoed from PHP anywhere (including `proses.php`)
- [ ] Single variable echo uses `<?= $data['x']; ?>`, not `<?php echo ?>` or concatenated strings
- [ ] Each process block in `proses.php` ends with `echo json_encode(['status' => ..., 'pesan' => ...])`
- [ ] AJAX requests use jQuery with `dataType: 'json'`, handled in `success:` callback
- [ ] After AJAX success, ONLY changed content reloaded (not full page reload)
- [ ] Explanatory comments above each process block and function
- [ ] File upload: physical file to `file/` root, metadata to `file` table, entity only stores `id_file`
- [ ] Upload via `upload_file()` in `function.php`
- [ ] Upload form uses `enctype` + `FormData` + `contentType: false, processData: false`
- [ ] NO hardcoded internal project paths/URLs — all resolved dynamically via `base_url()` or `__DIR__`
- [ ] Foreign key columns use **ID from session** (`$_SESSION['id']`, `$_SESSION['nip']`, `$_SESSION['nim']`), NOT names
- [ ] NO `alert()` JavaScript — use toast notifications
- [ ] All submit buttons have loading indicator (`setButtonLoading()`, `data-text`, `complete:` callback)
- [ ] CSS spinner included (`.spinner-slate`, `.is-loading`, `@keyframes spin`)

## AI Behavior Rules (Critical)

### 1. No Mojibake Characters
- Use plain quotes and hyphens in code/comments.
- NEVER smart quotes (`"` `"` `"`), em dashes (`—`), or other non-ASCII in PHP files.
- Before sending final file, verify no weird characters like `â€™`, `â€œ`, `â€`, `â€"`.

### 2. Execute, Don't Just Promise
- If AI identifies something needing revision, DO THE REVISION in the same response.
- Don't say "I'll fix it" and stop.
- Don't repeat the same steps without real progress.

### 3. NO Destructive Decisions Without Explicit Permission
Forbidden WITHOUT user confirmation:
- Git rollback (`git reset`/`checkout`/`revert`)
- Delete/overwrite files, folders, database tables, columns
- Full rewrite of large files/pages when user only asked for small fix
- Database structure changes (add/drop/modify columns or tables)
- Changing project conventions outside requested scope
- Any irreversible action impacting code outside request scope

**Required:** STOP, explain to user what you want to do + risks, get explicit confirmation before executing.

### 4. NEVER Touch Existing Page Structure Without Permission
Forbidden without explicit user authorization:
- Delete HTML elements (cards, badges, templates, modals, sections) because AI thinks "not needed"
- Replace placeholder/random images with "better" versions
- Simplify layouts that are actually final designs
- Delete documentation comments
- Change classes, colors, or styling "because more modern"
- Delete buttons, links, or actions that "look unused"
- Change placeholder UI text
- Add/remove/change form fields not requested
- Change icons, labels, or button order

**Exception (narrow):**
- Bugs that clearly prevent feature from working (syntax error, wrong ID so JS can't find target)
- Security (critical XSS, SQL injection)
- Fatal performance (infinite loop, memory leak)

**For everything else → ASK FIRST.** No justification ("cleaner", "best practice", "industry standard") overrides user's context.

## Template Files

See `php-native-crud-instructions.md` in project root for complete templates with examples.

## Quick Reference

| Rule | Pattern |
|------|---------|
| AJAX only | `$.ajax({ url: 'proses.php', method: 'POST', dataType: 'json', ... })` |
| JSON response | `echo json_encode(['status' => true/false, 'pesan' => '...']);` |
| No HTML echo | `?>` HTML `<?php` — never `echo "<script>...</script>"` |
| Variable display | `<?= $data['field']; ?>` |
| Upload storage | Physical: `file/` root, Metadata: `file` table, FK: only `id_file` |
| URL paths | `base_url()` for URLs, `__DIR__` for physical paths |
| Notifications | `toast(pesan, 'success'|'error'|'info')` |
| Loading | `setButtonLoading(btn, isLoading)` + `complete:` callback |
| Reload content | AJAX partial refresh only, never `location.reload()` |
