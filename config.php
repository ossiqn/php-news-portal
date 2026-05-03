<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'haberdb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
define('SITE_NAME', 'Haber Sitesi');
// BASE_PATH otomatik hesaplaniyor (localhost alt klasor veya sunucu koku)
$_scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$_docRoot   = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
$_subPath   = str_replace($_docRoot, '', $_scriptDir);
$_parts     = explode('/', trim($_subPath, '/'));
// config.php'nin bulundugu klasoru bul (proje koku)
$_configDir = str_replace('\\', '/', __DIR__);
$_basePath  = str_replace($_docRoot, '', $_configDir);
$_basePath  = '/' . trim($_basePath, '/');
if ($_basePath === '/') $_basePath = '';
define('BASE_PATH', $_basePath);
unset($_scriptDir, $_docRoot, $_subPath, $_parts, $_configDir, $_basePath);