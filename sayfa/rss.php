<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;

$catSlug  = trim($_GET['cat'] ?? '');
$limit    = min(50, max(10, (int)($_GET['limit'] ?? 20)));
$wantXml  = isset($_GET['xml']) || isset($_GET['m3u']) ||
            (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/rss') !== false);

$siteName = getSetting('site_title', SITE_NAME);
$siteDesc = getSetting('site_desc', 'En guncel haberler');
$siteUrl  = BASE_URL . BASE_PATH;

$allCats = getAllCategories();
$news    = [];
$curCat  = null;

try {
    if ($catSlug) {
        $curCat = getCategoryBySlug($catSlug);
        if ($curCat) {
            $news = getNewsByCategory((int)$curCat['id'], $limit);
        }
    } else {
        $news = getLatestNews($limit);
    }
} catch(Throwable $e) { $news = []; }

// XML modu
if ($wantXml) {
    header('Content-Type: application/rss+xml; charset=UTF-8');
    header('Cache-Control: public, max-age=1800');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">';
    echo '<channel>';
    echo '<title>' . htmlspecialchars($siteName . ($curCat ? ' - ' . $curCat['name'] : '')) . '</title>';
    echo '<link>' . $siteUrl . '</link>';
    echo '<description>' . htmlspecialchars($siteDesc) . '</description>';
    echo '<language>tr-TR</language>';
    echo '<lastBuildDate>' . date('r') . '</lastBuildDate>';
    echo '<atom:link href="' . $siteUrl . '/rss' . ($catSlug ? '/' . $catSlug : '') . '?xml=1" rel="self" type="application/rss+xml"/>';
    echo '<ttl>30</ttl>';
    foreach ($news as $item) {
        $pubDate = date('r', strtotime($item['published_at']));
        $link    = $siteUrl . '/haber/' . $item['slug'];
        $desc    = !empty($item['summary']) ? $item['summary'] : mb_substr(strip_tags($item['content'] ?? ''), 0, 200);
        echo '<item>';
        echo '<title>' . htmlspecialchars($item['title']) . '</title>';
        echo '<link>' . htmlspecialchars($link) . '</link>';
        echo '<guid isPermaLink="true">' . htmlspecialchars($link) . '</guid>';
        echo '<pubDate>' . $pubDate . '</pubDate>';
        if (!empty($item['cat_name'])) echo '<category>' . htmlspecialchars($item['cat_name']) . '</category>';
        echo '<description>' . htmlspecialchars($desc) . '</description>';
        if (!empty($item['content'])) echo '<content:encoded><![CDATA[' . $item['content'] . ']]></content:encoded>';
        if (!empty($item['image'])) echo '<enclosure url="' . htmlspecialchars($item['image']) . '" type="image/jpeg"/>';
        echo '</item>';
    }
    echo '</channel></rss>';
    exit;
}

// HTML modu
$pageTitle = 'RSS Beslemesi';
include __DIR__ . '/../header.php';
?>
<style>
.rss-wrap{padding:28px 0 60px}
.rss-hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%);border-radius:var(--r);padding:32px;margin-bottom:28px;display:flex;align-items:center;gap:24px;box-shadow:var(--sh2)}
.rss-icon-big{width:64px;height:64px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 8px 24px rgba(249,115,22,.35)}
.rss-icon-big svg{width:34px;height:34px;color:#fff}
.rss-hero h1{font-family:var(--fh);font-size:26px;font-weight:900;color:#fff;margin-bottom:6px}
.rss-hero p{font-size:13px;color:rgba(255,255,255,.5);line-height:1.6;max-width:500px}
.rss-feeds{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:28px}
.feed-card{background:var(--bg-card);border-radius:var(--r);padding:18px;box-shadow:var(--sh);border-left:4px solid var(--clr,#f97316);transition:all .2s;display:flex;flex-direction:column;gap:8px}
.feed-card:hover{box-shadow:var(--sh2);transform:translateY(-2px)}
.feed-card-name{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--navy)}
.feed-card-desc{font-size:12px;color:var(--text-3);line-height:1.5}
.feed-card-actions{display:flex;gap:8px;margin-top:4px}
.feed-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:3px;font-size:11px;font-weight:700;transition:all .18s;text-decoration:none;border:none;cursor:pointer}
.feed-btn-xml{background:#f97316;color:#fff}
.feed-btn-xml:hover{background:#ea580c}
.feed-btn-copy{background:var(--bg);border:1px solid var(--border);color:var(--text-2)}
.feed-btn-copy:hover{background:var(--border)}
.rss-latest h2{font-family:var(--fh);font-size:20px;font-weight:900;color:var(--navy);margin-bottom:18px;display:flex;align-items:center;gap:10px}
.rss-latest h2::before{content:'';width:4px;height:24px;background:#f97316;border-radius:2px;flex-shrink:0}
.rss-item{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);align-items:flex-start}
.rss-item:last-child{border-bottom:none}
.rss-thumb{width:80px;height:56px;border-radius:var(--r);overflow:hidden;flex-shrink:0}
.rss-thumb img{width:100%;height:100%;object-fit:cover}
.rss-item-body h4{font-family:var(--fh);font-size:14px;font-weight:700;line-height:1.32;color:var(--text)}
.rss-item-body h4 a:hover{color:var(--red)}
.rss-item-meta{font-size:11px;color:var(--text-3);margin-top:5px;display:flex;gap:10px}
.rss-cat-filter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.rss-cat-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:3px;font-size:11px;font-weight:700;border:1.5px solid var(--border);background:var(--bg-card);color:var(--text-2);cursor:pointer;text-decoration:none;transition:all .15s}
.rss-cat-btn:hover,.rss-cat-btn.active{background:var(--red);color:#fff;border-color:var(--red)}
.rss-url-box{background:var(--bg);border:1.5px solid var(--border);border-radius:var(--r);padding:11px 14px;font-size:12px;color:var(--text-2);font-family:monospace;word-break:break-all;margin-top:6px}
@media(max-width:768px){.rss-feeds{grid-template-columns:1fr 1fr}.rss-hero{flex-direction:column;gap:16px}}
@media(max-width:480px){.rss-feeds{grid-template-columns:1fr}.rss-hero h1{font-size:20px}}
</style>

<div class="rss-wrap">
<div class="container">

    <div class="rss-hero">
        <div class="rss-icon-big">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
        </div>
        <div>
            <h1>RSS Beslemesi</h1>
            <p><?= h($siteName) ?> haberlerini favori RSS okuyucunuza ekleyin. Tüm haberlerden veya kategori bazlı beslemelerden yararlanın.</p>
            <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
                <a href="<?= h($_base) ?>/rss?xml=1" class="feed-btn feed-btn-xml" target="_blank">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                    Tum Haberler RSS
                </a>
                <button onclick="copyUrl('<?= h($siteUrl) ?>/rss?xml=1')" class="feed-btn feed-btn-copy">
                    📋 URL Kopyala
                </button>
            </div>
        </div>
    </div>

    <h2 style="font-family:var(--fh);font-size:18px;font-weight:900;color:var(--navy);margin-bottom:14px">Kategori Beslemeleri</h2>
    <div class="rss-feeds">
        <div class="feed-card" style="--clr:#f97316">
            <div class="feed-card-name">Tüm Haberler</div>
            <div class="feed-card-desc">Tüm kategorilerden son haberler</div>
            <div class="rss-url-box"><?= h($siteUrl) ?>/rss?xml=1</div>
            <div class="feed-card-actions">
                <a href="<?= h($_base) ?>/rss?xml=1" class="feed-btn feed-btn-xml" target="_blank">RSS Aç</a>
                <button onclick="copyUrl('<?= h($siteUrl) ?>/rss?xml=1')" class="feed-btn feed-btn-copy">Kopyala</button>
            </div>
        </div>
        <?php foreach (array_slice($allCats, 0, 11) as $cat): ?>
        <div class="feed-card" style="--clr:<?= h($cat['color'] ?? '#cc1a25') ?>">
            <div class="feed-card-name"><?= h($cat['name']) ?></div>
            <div class="feed-card-desc"><?= h($cat['name']) ?> kategorisi haberleri</div>
            <div class="rss-url-box"><?= h($siteUrl) ?>/rss/<?= h($cat['slug']) ?>?xml=1</div>
            <div class="feed-card-actions">
                <a href="<?= h($_base) ?>/rss/<?= h($cat['slug']) ?>?xml=1" class="feed-btn feed-btn-xml" target="_blank">RSS Aç</a>
                <button onclick="copyUrl('<?= h($siteUrl) ?>/rss/<?= h($cat['slug']) ?>?xml=1')" class="feed-btn feed-btn-copy">Kopyala</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="rss-latest">
        <h2>Son Haberler</h2>
        <div class="rss-cat-filter">
            <a href="<?= h($_base) ?>/rss" class="rss-cat-btn <?= !$catSlug ? 'active' : '' ?>">Tumu</a>
            <?php foreach ($allCats as $cat): ?>
            <a href="<?= h($_base) ?>/rss/<?= h($cat['slug']) ?>" class="rss-cat-btn <?= $catSlug === $cat['slug'] ? 'active' : '' ?>" style="<?= $catSlug === $cat['slug'] ? 'background:'.h($cat['color']??'#cc1a25').';border-color:'.h($cat['color']??'#cc1a25') : '' ?>"><?= h($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php if (empty($news)): ?>
        <div style="text-align:center;padding:40px;color:var(--text-3)">Bu kategoride haber bulunamadı.</div>
        <?php endif; ?>
        <?php foreach ($news as $item): ?>
        <div class="rss-item">
            <div class="rss-thumb">
                <a href="<?= h($_base) ?>/haber/<?= h($item['slug']) ?>">
                    <img src="<?= h(imgUrl($item['image']) ?: 'https://placehold.co/160x112/eee/999?text=H') ?>" alt="<?= h($item['title']) ?>">
                </a>
            </div>
            <div class="rss-item-body">
                <?php if (!empty($item['cat_name'])): ?>
                <span style="display:inline-block;background:<?= h($item['cat_color']??'#cc1a25') ?>;color:#fff;font-size:9px;font-weight:700;padding:1px 7px;border-radius:2px;margin-bottom:5px;text-transform:uppercase"><?= h($item['cat_name']) ?></span>
                <?php endif; ?>
                <h4><a href="<?= h($_base) ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h4>
                <div class="rss-item-meta">
                    <span><?= timeAgo($item['published_at']) ?></span>
                    <span><?= number_format($item['views'] ?? 0) ?> okunma</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>
</div>

<script>
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function(){
        var btns = document.querySelectorAll('.feed-btn-copy');
        btns.forEach(function(b){ if(b.getAttribute('onclick') && b.getAttribute('onclick').indexOf(url) > -1){ var orig=b.textContent; b.textContent='✓ Kopyalandı!'; setTimeout(function(){b.textContent=orig;},2000); } });
    });
}
</script>

<?php include __DIR__ . '/../footer.php'; ?>
