<?php
require_once dirname(__DIR__) . '/config.php';
define('ADMIN_USER',    'admin');
define('ADMIN_PASS_HASH', '7f9ec1594b32ac7ac5c2342f6e37a9708ccb6967371baa1288639f8e44701dad');
define('ADMIN_PASS_SALT', 'hbr2024');
define('ADMIN_VERSION', '1.0');
define('ADMIN_DIR',     __DIR__);
define('ROOT_DIR',      dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function adminAuth(): void {
    if (empty($_SESSION['admin_logged'])) {
        header('Location: ' . BASE_PATH . '/admin/login.php');
        exit;
    }
}

function logAction(string $action, string $detail = ''): void {
    try {
        require_once ROOT_DIR . '/functions.php';
        $pdo  = getPDO();
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
        $user = $_SESSION['admin_user'] ?? 'admin';
        $pdo->prepare("INSERT INTO admin_logs (action,detail,admin_user,ip,created_at) VALUES (?,?,?,?,NOW())")
            ->execute([$action, $detail, $user, $ip]);
    } catch (Throwable $e) {}
}

function getSetting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            require_once ROOT_DIR . '/functions.php';
            $rows = getPDO()->query("SELECT `key`,`value` FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            $cache = $rows ?: [];
        } catch (Throwable $e) {}
    }
    return (string)($cache[$key] ?? $default);
}

function saveSetting(string $key, string $value): void {
    require_once ROOT_DIR . '/functions.php';
    getPDO()->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")
        ->execute([$key, $value]);
}

function getAdminPDO(): ?PDO {
    try {
        require_once ROOT_DIR . '/functions.php';
        return getPDO();
    } catch (Throwable $e) {
        return null;
    }
}

function formatBytes(int $b): string {
    if ($b >= 1073741824) return round($b/1073741824,1).' GB';
    if ($b >= 1048576)    return round($b/1048576,1).' MB';
    return round($b/1024,1).' KB';
}

function sTr(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$CITIES = ['Adana','Adıyaman','Afyonkarahisar','Ağrı','Amasya','Ankara','Antalya','Artvin','Aydın','Balıkesir','Bilecik','Bingöl','Bitlis','Bolu','Burdur','Bursa','Çanakkale','Çankırı','Çorum','Denizli','Diyarbakır','Edirne','Elazığ','Erzincan','Erzurum','Eskişehir','Gaziantep','Giresun','Gümüşhane','Hakkari','Hatay','Isparta','Mersin','İstanbul','İzmir','Kars','Kastamonu','Kayseri','Kırklareli','Kırşehir','Kocaeli','Konya','Kütahya','Malatya','Manisa','Kahramanmaraş','Mardin','Muğla','Muş','Nevşehir','Niğde','Ordu','Rize','Sakarya','Samsun','Siirt','Sinop','Sivas','Tekirdağ','Tokat','Trabzon','Tunceli','Şanlıurfa','Uşak','Van','Yozgat','Zonguldak','Aksaray','Bayburt','Karaman','Kırıkkale','Batman','Şırnak','Bartın','Ardahan','Iğdır','Yalova','Karabük','Kilis','Osmaniye','Düzce'];
