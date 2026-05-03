<?php
require_once __DIR__ . '/../functions.php';
$q = trim($_GET['q'] ?? '');
$results = $q ? searchNews($q, 20) : [];
$pageTitle = $q ? '"' . $q . '" için Arama Sonuçları' : 'Arama';
include __DIR__ . '/../header.php';
?>
<style>
    .search-page { padding: 40px 0 60px; }
    .search-header { margin-bottom: 32px; }
    .search-header h1 { font-family: var(--font-head); font-size: 28px; font-weight: 900; }
    .search-header p { color: var(--text-soft); margin-top: 6px; }
    .search-form-big { display: flex; max-width: 600px; margin-bottom: 36px; border: 2px solid var(--navy); border-radius: 4px; overflow: hidden; }
    .search-form-big input { flex: 1; border: none; padding: 14px 18px; font-size: 16px; font-family: var(--font-body); outline: none; background: #fff; }
    .search-form-big button { background: var(--navy); color: #fff; border: none; padding: 14px 24px; font-size: 16px; cursor: pointer; font-weight: 700; transition: background .2s; }
    .search-form-big button:hover { background: var(--red); }
    .results-list { display: flex; flex-direction: column; gap: 0; }
    .result-item { display: flex; gap: 16px; padding: 18px 0; border-bottom: 1px solid var(--border); align-items: flex-start; }
    .result-img { width: 120px; height: 80px; border-radius: var(--radius); overflow: hidden; flex-shrink: 0; }
    .result-img img { transition: transform .3s; }
    .result-item:hover .result-img img { transform: scale(1.06); }
    .result-body h2 { font-family: var(--font-head); font-size: 18px; font-weight: 700; line-height: 1.3; }
    .result-body h2 a:hover { color: var(--red); }
    .result-body p { font-size: 13px; color: var(--text-mid); margin-top: 6px; line-height: 1.5; }
    .result-body .rm { font-size: 11px; color: var(--text-soft); margin-top: 8px; }
    @media (max-width: 480px) { .result-img { width: 80px; height: 60px; } .result-body h2 { font-size: 15px; } }
</style>
<div class="container search-page">
    <div class="search-header">
        <?php if ($q): ?>
        <h1>"<?= h($q) ?>" için <?= count($results) ?> sonuç bulundu</h1>
        <p>Arama sonuçları aşağıda listelenmektedir.</p>
        <?php else: ?>
        <h1>Haber Ara</h1>
        <?php endif; ?>
    </div>
    <form class="search-form-big" action="<?= BASE_PATH ?>/arama" method="get">
        <input type="text" name="q" placeholder="Aramak istediğiniz konuyu yazın..." value="<?= h($q) ?>">
        <button type="submit">🔍 Ara</button>
    </form>
    <?php if ($q && empty($results)): ?>
    <p style="color:var(--text-soft);font-size:16px;">Aramanızla eşleşen haber bulunamadı. Farklı kelimeler deneyin.</p>
    <?php endif; ?>
    <div class="results-list">
        <?php foreach ($results as $item): ?>
        <div class="result-item">
            <?php if ($item['image']): ?>
            <div class="result-img">
                <a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>">
                    <img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" loading="lazy">
                </a>
            </div>
            <?php endif; ?>
            <div class="result-body">
                <?php if ($item['cat_name']): ?>
                <span style="display:inline-block;background:<?= h($item['cat_color'] ?? '#c8102e') ?>;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:2px;text-transform:uppercase;margin-bottom:6px;"><?= h($item['cat_name']) ?></span>
                <?php endif; ?>
                <h2><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h2>
                <?php if ($item['summary']): ?>
                <p><?= h(mb_substr($item['summary'], 0, 150)) ?>...</p>
                <?php endif; ?>
                <div class="rm">📅 <?= timeAgo($item['published_at']) ?> · 👁 <?= number_format($item['views']) ?> görüntülenme</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
