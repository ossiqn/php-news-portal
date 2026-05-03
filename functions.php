<?php
require_once __DIR__ . '/config.php';

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
    return $pdo;
}

function timeAgo(string $datetime): string {
    $diff = (new DateTime())->diff(new DateTime($datetime));
    if ($diff->days >= 7)   return date('d.m.Y', strtotime($datetime));
    if ($diff->days >= 1)   return $diff->days . ' gün önce';
    if ($diff->h >= 1)      return $diff->h . ' saat önce';
    if ($diff->i >= 1)      return $diff->i . ' dk önce';
    return 'Az önce';
}

function formatDate(string $datetime): string {
    $months = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $d = new DateTime($datetime);
    return $d->format('d') . ' ' . $months[(int)$d->format('n')] . ' ' . $d->format('Y') . ', H:i';
}

function getAllCategories(): array {
    static $cats = null;
    if ($cats === null) {
        $cats = getPDO()->query('SELECT * FROM categories ORDER BY sort_order ASC')->fetchAll();
    }
    return $cats;
}

function getBreakingNews(): array {
    return getPDO()->query('SELECT title, slug FROM news WHERE status=1 AND is_breaking=1 ORDER BY published_at DESC LIMIT 8')->fetchAll();
}

function getCategoryBySlug(string $slug): ?array {
    $stmt = getPDO()->prepare('SELECT * FROM categories WHERE slug = ?');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getNewsByCategory(int $catId, int $limit = 12, int $offset = 0): array {
    $stmt = getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND n.category_id=?
        ORDER BY n.published_at DESC LIMIT $limit OFFSET $offset
    ");
    $stmt->execute([$catId]);
    return $stmt->fetchAll();
}

function countNewsByCategory(int $catId): int {
    $stmt = getPDO()->prepare('SELECT COUNT(*) FROM news WHERE status=1 AND category_id=?');
    $stmt->execute([$catId]);
    return (int)$stmt->fetchColumn();
}

function getFeaturedNews(int $limit = 9): array {
    return getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND n.is_featured=1
        ORDER BY n.published_at DESC LIMIT $limit
    ")->fetchAll();
}

function getLatestNews(int $limit = 12): array {
    return getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1
        ORDER BY n.published_at DESC LIMIT $limit
    ")->fetchAll();
}

function getMostRead(int $limit = 5): array {
    return getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1
        ORDER BY n.views DESC LIMIT $limit
    ")->fetchAll();
}

function getNewsBySlug(string $slug): ?array {
    $stmt = getPDO()->prepare('
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.slug=? AND n.status=1
    ');
    $stmt->execute([$slug]);
    $news = $stmt->fetch() ?: null;
    if ($news) {
        getPDO()->prepare('UPDATE news SET views = views + 1 WHERE id = ?')->execute([$news['id']]);
    }
    return $news;
}

function getRelatedNews(int $catId, int $excludeId, int $limit = 4): array {
    $stmt = getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND n.category_id=? AND n.id != ?
        ORDER BY n.published_at DESC LIMIT $limit
    ");
    $stmt->execute([$catId, $excludeId]);
    return $stmt->fetchAll();
}

function getTodayNews(int $limit = 20): array {
    return getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND DATE(n.published_at) = CURDATE()
        ORDER BY n.published_at DESC LIMIT $limit
    ")->fetchAll();
}

function searchNews(string $q, int $limit = 20): array {
    $like = '%' . $q . '%';
    $stmt = getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND (n.title LIKE ? OR n.summary LIKE ?)
        ORDER BY n.published_at DESC LIMIT $limit
    ");
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll();
}

function getArchiveNews(int $year, int $month, int $limit = 20): array {
    $stmt = getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND YEAR(n.published_at)=? AND MONTH(n.published_at)=?
        ORDER BY n.published_at DESC LIMIT $limit
    ");
    $stmt->execute([$year, $month]);
    return $stmt->fetchAll();
}

function currentUrl(): string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

if (!function_exists('getSetting')) {
function getSetting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $rows = getPDO()->query("SELECT `key`,`value` FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            $cache = $rows ?: [];
        } catch (Throwable $e) {}
    }
    return (string)($cache[$key] ?? $default);
}
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function getTrendingNews(int $limit = 5): array {
    try {
        return getPDO()->query("
            SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
            FROM news n LEFT JOIN categories c ON n.category_id = c.id
            WHERE n.status=1 AND n.published_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY n.views DESC LIMIT $limit
        ")->fetchAll();
    } catch (PDOException $e) { return []; }
}

function getNewsByCategoryExcluding(int $catId, array $excludeIds, int $limit = 4): array {
    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
    $stmt = getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug
        FROM news n LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status=1 AND n.category_id=? AND n.id NOT IN ($placeholders)
        ORDER BY n.published_at DESC LIMIT $limit
    ");
    $stmt->execute(array_merge([$catId], $excludeIds));
    return $stmt->fetchAll();
}

function getCategoryStats(): array {
    $rows = getPDO()->query("
        SELECT c.id, c.name, c.slug, c.color,
               COUNT(n.id) as news_count,
               SUM(n.views) as total_views
        FROM categories c
        LEFT JOIN news n ON n.category_id = c.id AND n.status=1
        GROUP BY c.id ORDER BY c.sort_order ASC
    ")->fetchAll();
    $out = [];
    foreach ($rows as $r) $out[$r['slug']] = $r;
    return $out;
}

function userLogin(string $email, string $pass): ?array {
    $stmt = getPDO()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($pass, $user['password'])) {
        return null;
    }
    if ($user['status'] === 'pending') {
        return ['__pending__' => true];
    }
    if ($user['status'] !== 'active') {
        return null;
    }
    getPDO()->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$user['id']]);
    return $user;
}

function userRegister(string $name, string $email, string $pass): bool {
    try {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = getPDO()->prepare("INSERT INTO users (name,email,password,status) VALUES (?,?,?,'pending')");
        $stmt->execute([$name, $email, $hash]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getUserById(int $id): ?array {
    $stmt = getPDO()->prepare("SELECT id,name,email,status,email_newsletter,pref_categories,created_at,last_login FROM users WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function updateUserProfile(int $id, string $name, string $email): bool {
    $stmt = getPDO()->prepare("UPDATE users SET name=?,email=? WHERE id=?");
    return $stmt->execute([$name, $email, $id]);
}

function updateUserPassword(int $id, string $newPass): bool {
    $hash = password_hash($newPass, PASSWORD_DEFAULT);
    $stmt = getPDO()->prepare("UPDATE users SET password=? WHERE id=?");
    return $stmt->execute([$hash, $id]);
}

function updateUserPrefs(int $id, int $newsletter, string $cats): bool {
    $stmt = getPDO()->prepare("UPDATE users SET email_newsletter=?,pref_categories=? WHERE id=?");
    return $stmt->execute([$newsletter, $cats, $id]);
}

function getUserFavorites(int $userId): array {
    return getPDO()->prepare("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug,
               uf.created_at as fav_at
        FROM user_favorites uf
        JOIN news n ON n.id=uf.news_id
        LEFT JOIN categories c ON c.id=n.category_id
        WHERE uf.user_id=? AND n.status=1
        ORDER BY uf.created_at DESC
    ")->execute([$userId]) ? getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug,
               uf.created_at as fav_at
        FROM user_favorites uf
        JOIN news n ON n.id=uf.news_id
        LEFT JOIN categories c ON c.id=n.category_id
        WHERE uf.user_id=$userId AND n.status=1
        ORDER BY uf.created_at DESC
    ")->fetchAll() : [];
}

function getUserSaved(int $userId): array {
    return getPDO()->query("
        SELECT n.*, c.name as cat_name, c.color as cat_color, c.slug as cat_slug,
               us.created_at as saved_at
        FROM user_saved us
        JOIN news n ON n.id=us.news_id
        LEFT JOIN categories c ON c.id=n.category_id
        WHERE us.user_id=$userId AND n.status=1
        ORDER BY us.created_at DESC
    ")->fetchAll();
}

function getUserNotifications(int $userId): array {
    return getPDO()->query("
        SELECT * FROM user_notifications
        WHERE user_id=$userId
        ORDER BY created_at DESC LIMIT 30
    ")->fetchAll();
}

function countUnreadNotifications(int $userId): int {
    $stmt = getPDO()->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id=? AND is_read=0");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

function markNotificationsRead(int $userId): void {
    getPDO()->prepare("UPDATE user_notifications SET is_read=1 WHERE user_id=?")->execute([$userId]);
}

function toggleFavorite(int $userId, int $newsId): string {
    $stmt = getPDO()->prepare("SELECT id FROM user_favorites WHERE user_id=? AND news_id=?");
    $stmt->execute([$userId, $newsId]);
    if ($stmt->fetch()) {
        getPDO()->prepare("DELETE FROM user_favorites WHERE user_id=? AND news_id=?")->execute([$userId, $newsId]);
        return 'removed';
    }
    getPDO()->prepare("INSERT IGNORE INTO user_favorites (user_id,news_id) VALUES (?,?)")->execute([$userId, $newsId]);
    return 'added';
}

function toggleSaved(int $userId, int $newsId): string {
    $stmt = getPDO()->prepare("SELECT id FROM user_saved WHERE user_id=? AND news_id=?");
    $stmt->execute([$userId, $newsId]);
    if ($stmt->fetch()) {
        getPDO()->prepare("DELETE FROM user_saved WHERE user_id=? AND news_id=?")->execute([$userId, $newsId]);
        return 'removed';
    }
    getPDO()->prepare("INSERT IGNORE INTO user_saved (user_id,news_id) VALUES (?,?)")->execute([$userId, $newsId]);
    return 'added';
}

function isNewsInFavorites(int $userId, int $newsId): bool {
    $stmt = getPDO()->prepare("SELECT 1 FROM user_favorites WHERE user_id=? AND news_id=?");
    $stmt->execute([$userId, $newsId]);
    return (bool)$stmt->fetch();
}

function isNewsSaved(int $userId, int $newsId): bool {
    $stmt = getPDO()->prepare("SELECT 1 FROM user_saved WHERE user_id=? AND news_id=?");
    $stmt->execute([$userId, $newsId]);
    return (bool)$stmt->fetch();
}

function getUserStats(int $userId): array {
    $favCount  = (int)getPDO()->query("SELECT COUNT(*) FROM user_favorites WHERE user_id=$userId")->fetchColumn();
    $saveCount = (int)getPDO()->query("SELECT COUNT(*) FROM user_saved WHERE user_id=$userId")->fetchColumn();
    $notifCount = countUnreadNotifications($userId);
    return ['favorites'=>$favCount,'saved'=>$saveCount,'notifications'=>$notifCount];
}

function collectApiGet(string $endpoint, string $apiKey): array {
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; HaberSitesi/1.0)',
        CURLOPT_HTTPHEADER     => array(
            'authorization: ' . $apiKey,
            'content-type: application/json',
            'Accept: application/json',
        ),
    ));
    $response = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    $curlNo   = curl_errno($ch);
    curl_close($ch);

    if ($curlErr || $curlNo) {
        return array('ok' => false, 'error' => 'cURL hatası: ' . $curlErr, 'code' => $curlNo);
    }
    if ($httpCode !== 200) {
        return array('ok' => false, 'error' => 'HTTP ' . $httpCode, 'code' => $httpCode);
    }
    $data = json_decode($response, true);
    if (!$data) {
        return array('ok' => false, 'error' => 'JSON parse hatası', 'code' => 0);
    }
    return array('ok' => true, 'data' => $data, 'code' => 200);
}
