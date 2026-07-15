<?php
/**
 * Folder index — guard agar direktori tidak bisa diakses langsung via browser.
 * Halaman ada di folder yang sama dengan nama file <page-id>.php.
 */
http_response_code(403);
header('Location: ../../index.php', true, 303);
exit;