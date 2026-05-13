<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;

$year  = (int)($_GET['year']  ?? date('Y'));
$month = (int)($_GET['month'] ?? date('n'));

if ($year < 2000 || $year > (int)date('Y')) $year = (int)date('Y');
if ($month < 1 || $month > 12) $month = (int)date('n');

$items = getArchiveNews($year, $month, 30);
$pageTitle = 'Arşiv';

$monthNames = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];

include __DIR__ . '/../header.php';
?>
<style>
    .page-hero { background: var(--navy); padding: 36px 0; margin-bottom: 36px; border-bottom: 4px solid var(--gold); }
    .page-hero h1 { font-family: var(--font-head); font-size: 36px; font-weight: 900; color: #fff; }
    .page-hero p { color: rgba(255,255,255,.6); margin-top: 6px; }
    .archive-layout { display: grid; grid-template-columns: 260px 1fr; gap: 32px; }
    .archive-sidebar { background: var(--bg-card); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow); height: fit-content; }
    .archive-sidebar h3 { font-family: var(--font-head); font-size: 18px; margin-bottom: 16px; border-bottom: 2px solid var(--red); padding-bottom: 8px; }
    .year-group { margin-bottom: 20px; }
    .year-label { font-weight: 700; font-size: 14px; color: var(--navy); margin-bottom: 8px; padding: 6px 0; border-bottom: 1px solid var(--border); }
    .month-links { display: flex; flex-wrap: wrap; gap: 6px; }
    .month-links a {
        padding: 5px 10px; background: var(--bg); border-radius: 3px;
        font-size: 12px; font-weight: 600; color: var(--text);
        transition: all .2s; border: 1px solid var(--border);
    }
    .month-links a:hover, .month-links a.active { background: var(--red); color: #fff; border-color: var(--red); }
    .archive-list { display: flex; flex-direction: column; gap: 0; }
    .arc-item { display: flex; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--border); align-items: flex-start; }
    .arc-img { width: 110px; height: 74px; border-radius: var(--radius); overflow: hidden; flex-shrink: 0; }
    .arc-img img { transition: transform .3s; }
    .arc-item:hover .arc-img img { transform: scale(1.08); }
    .arc-body h3 { font-family: var(--font-head); font-size: 15px; font-weight: 700; line-height: 1.3; }
    .arc-body h3 a:hover { color: var(--red); }
    .arc-body .am { font-size: 11px; color: var(--text-soft); margin-top: 6px; }
    @media (max-width: 768px) { .archive-layout { grid-template-columns: 1fr; } }
</style>
<div class="page-hero">
    <div class="container">
        <h1>🗂 Haber Arşivi</h1>
        <p><?= $monthNames[$month] ?> <?= $year ?> — <?= count($items) ?> haber</p>
    </div>
</div>
<div class="container" style="padding-bottom:60px;">
    <div class="archive-layout">
        <aside class="archive-sidebar">
            <h3>Arşive Göz At</h3>
            <?php
            $currentYear = (int)date('Y');
            for ($y = $currentYear; $y >= max($currentYear - 3, 2020); $y--):
            ?>
            <div class="year-group">
                <div class="year-label"><?= $y ?></div>
                <div class="month-links">
                    <?php for ($m = ($y === $currentYear ? (int)date('n') : 12); $m >= 1; $m--): ?>
                    <a href="?year=<?= $y ?>&month=<?= $m ?>" class="<?= ($y === $year && $m === $month) ? 'active' : '' ?>"><?= $monthNames[$m] ?></a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endfor; ?>
        </aside>

        <div>
            <h2 style="font-family:var(--font-head);font-size:24px;font-weight:900;margin-bottom:24px;"><?= $monthNames[$month] ?> <?= $year ?> Haberleri</h2>
            <?php if (!empty($items)): ?>
            <div class="archive-list">
                <?php foreach ($items as $item): ?>
                <div class="arc-item">
                    <?php if ($item['image']): ?>
                    <div class="arc-img">
                        <a href="/haber/<?= h($item['slug']) ?>">
                            <img src="<?= h(imgUrl($item['image'])) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="arc-body">
                        <?php if ($item['cat_name']): ?>
                        <span style="display:inline-block;background:<?= h($item['cat_color']) ?>;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:2px;text-transform:uppercase;margin-bottom:6px;"><?= h($item['cat_name']) ?></span>
                        <?php endif; ?>
                        <h3><a href="/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h3>
                        <div class="am">📅 <?= formatDate($item['published_at']) ?> · 👁 <?= number_format($item['views']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:60px 0;">
                <div style="font-size:48px;">📰</div>
                <p style="margin-top:12px;color:var(--text-soft);"><?= $monthNames[$month] ?> <?= $year ?> tarihinde haber bulunamadı.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
