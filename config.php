<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'haberdb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
// Subdirectory desteği - sitenin hangi klasörde olduğunu otomatik algılar
if (!defined('BASE_PATH')) {
    $__script = $_SERVER['SCRIPT_FILENAME'] ?? __FILE__;
    $__docroot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
    $__sitedir = rtrim(dirname(dirname(dirname(__FILE__))), '/\\');
    if ($__docroot && strpos($__sitedir, $__docroot) === 0) {
        $__rel = substr($__sitedir, strlen($__docroot));
        $__rel = str_replace('\\', '/', $__rel);
        define('BASE_PATH', rtrim($__rel, '/'));
    } else {
        define('BASE_PATH', '');
    }
}
define('SITE_NAME', 'Haber Sitesi');