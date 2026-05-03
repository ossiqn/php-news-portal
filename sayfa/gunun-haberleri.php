<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = "Günün Haberleri";
$items = getTodayNews(30);
include __DIR__ . '/../header.php';
?>
<style>
    .page-hero { background: var(--navy); padding: 36px 0; margin-bottom: 36px; border-bottom: 4px solid var(--red); }
    .page-hero h1 { font-family: var(--font-head); font-size: 36px; font-weight: 900; color: #fff; }
    .page-hero p { color: rgba(255,255,255,.6); margin-top: 6px; font-size: 14px; }
    .day-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .day-card { background: var(--bg-card); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); transition: all .2s; }
    .day-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
    .day-card-img { aspect-ratio: 16/9; overflow: hidden; }
    .day-card-img img { transition: transform .4s; }
    .day-card:hover .day-card-img img { transform: scale(1.06); }
    .day-card-body { padding: 14px; }
    .day-card-body h2 { font-family: var(--font-head); font-size: 15px; font-weight: 700; line-height: 1.35; margin-top: 8px; }
    .day-card-body h2 a:hover { color: var(--red); }
    .day-card-body .dm { font-size: 11px; color: var(--text-soft); margin-top: 8px; }
    @media (max-width: 768px) { .day-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px) { .day-grid { grid-template-columns: 1fr; } }
</style>
<div class="page-hero">
    <div class="container">
        <h1>📅 Günün Haberleri</h1>
        <p><?= date('d.m.Y') ?> tarihinli <?= count($items) ?> haber</p>
    </div>
</div>
<div class="container" style="padding-bottom:60px;">
    <?php if (!empty($items)): ?>
    <div class="day-grid">
        <?php foreach ($items as $item): ?>
        <div class="day-card">
            <div class="day-card-img">
                <a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>">
                    <img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
                </a>
            </div>
            <div class="day-card-body">
                <?php if ($item['cat_name']): ?>
                <span style="display:inline-block;background:<?= h($item['cat_color']) ?>;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:2px;text-transform:uppercase;"><?= h($item['cat_name']) ?></span>
                <?php endif; ?>
                <h2><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h2>
                <div class="dm">🕐 <?= timeAgo($item['published_at']) ?> · 👁 <?= number_format($item['views']) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:80px 0;">
        <div style="font-size:64px;">📰</div>
        <h3 style="font-family:var(--font-head);font-size:24px;margin-top:16px;color:var(--text-soft);">Bugün henüz haber eklenmedi.</h3>
        <a href="<?= BASE_PATH ?>/" style="display:inline-block;margin-top:20px;background:var(--red);color:#fff;padding:10px 24px;border-radius:4px;font-weight:700;">Ana Sayfaya Dön</a>
    </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
