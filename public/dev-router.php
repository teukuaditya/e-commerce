<?php

/**
 * Router untuk `php -S` (pengganti `php artisan serve`).
 * Bedanya di sini opcache bisa dinyalakan via -d ... sehingga boot framework
 * tidak membaca ulang seluruh vendor dari disk NTFS tiap request.
 *
 * Jalankan:
 * PHP_CLI_SERVER_WORKERS=4 php -d opcache.enable_cli=1 -d opcache.file_cache=/tmp/op/opcache-file \
 *   -S 0.0.0.0:8000 public/dev-router.php
 */

$publicPath = __DIR__;

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');

// Biarkan built-in server mengirim file statis (css/js/gambar) sendiri.
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicPath.'/index.php';
require $publicPath.'/index.php';
