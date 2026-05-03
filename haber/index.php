<?php
require_once __DIR__ . '/../functions.php';

$slug = $_GET['slug'] ?? '';
if(!$slug){ header('Location: ' . BASE_PATH . '/'); exit; }

$news = getNewsBySlug($slug);
if(!$news){ header('HTTP/1.0 404 Not Found'); header('Location: ' . BASE_PATH . '/'); exit; }

try { getPDO()->prepare("UPDATE news SET views=views+1 WHERE id=?")->execute([$news['id']]); } catch(Throwable $e){}

$related = getRelatedNews((int)$news['category_id'],(int)$news['id'],4);
$mostRead = getMostRead(5);

$pageTitle = $news['title'];
$pageDesc  = $news['summary'] ?? mb_substr(strip_tags($news['content']??''),0,155);

include __DIR__ . '/../header.php';
?>
<style>
.news-wrap{padding:28px 0 60px}
.news-layout{display:grid;grid-template-columns:1fr 300px;gap:32px;align-items:start}
.news-article{background:var(--bg-card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden}
.news-hero{width:100%;aspect-ratio:16/9;overflow:hidden;position:relative}
.news-hero img{width:100%;height:100%;object-fit:cover}
.news-body{padding:28px 32px}
.news-breadcrumb{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-3);margin-bottom:18px;flex-wrap:wrap}
.news-breadcrumb a{color:var(--red);transition:color .15s}
.news-breadcrumb a:hover{color:var(--red-dark)}
.news-breadcrumb span{opacity:.5}
.news-cat-badge{display:inline-block;padding:3px 10px;border-radius:2px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#fff;margin-bottom:12px}
.news-title{font-family:var(--fh);font-size:28px;font-weight:900;line-height:1.2;color:var(--navy);letter-spacing:-.02em;margin-bottom:14px}
.news-meta{display:flex;align-items:center;gap:16px;font-size:12px;color:var(--text-3);padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:24px;flex-wrap:wrap}
.news-meta svg{width:13px;height:13px;flex-shrink:0}
.news-meta span{display:flex;align-items:center;gap:5px}
.news-summary{font-size:15px;color:var(--text-2);line-height:1.75;font-weight:500;margin-bottom:22px;padding:16px;background:var(--bg);border-left:3px solid var(--red);border-radius:0 var(--r) var(--r) 0}
.news-content{font-size:14px;line-height:1.85;color:var(--text-2)}
.news-content p{margin-bottom:18px}
.news-content h2,.news-content h3{font-family:var(--fh);color:var(--navy);margin:26px 0 12px}
.news-content img{max-width:100%;border-radius:var(--r);margin:18px 0}
.news-content a{color:var(--red);text-decoration:underline}
.news-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:28px;padding-top:20px;border-top:1px solid var(--border)}
.news-tag{display:inline-block;background:var(--bg);border:1px solid var(--border);padding:4px 11px;border-radius:2px;font-size:11px;color:var(--text-3);transition:all .15s}
.news-tag:hover{background:var(--red);color:#fff;border-color:var(--red)}
.news-share{display:flex;align-items:center;gap:10px;margin-top:22px;padding-top:18px;border-top:1px solid var(--border)}
.share-btn{display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:2px;font-size:12px;font-weight:600;color:#fff;cursor:pointer;border:none;transition:opacity .2s}
.share-btn:hover{opacity:.88}
.related-section{margin-top:36px}
.related-title{font-family:var(--fh);font-size:18px;font-weight:900;color:var(--navy);margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid var(--border);display:flex;align-items:center;gap:10px}
.related-title::before{content:'';width:4px;height:22px;background:var(--red);border-radius:2px;flex-shrink:0}
.related-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.rc{background:var(--bg-card);border-radius:var(--r);overflow:hidden;box-shadow:var(--sh);transition:all .2s}
.rc:hover{box-shadow:var(--sh2);transform:translateY(-2px)}
.rc-img{aspect-ratio:16/9;overflow:hidden}
.rc-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.rc:hover .rc-img img{transform:scale(1.05)}
.rc-body{padding:11px 13px 13px}
.rc-body h4{font-family:var(--fh);font-size:13px;font-weight:700;line-height:1.32;color:var(--text)}
.rc-body h4 a:hover{color:var(--red)}
.rc-meta{font-size:10px;color:var(--text-3);margin-top:6px}
@media(max-width:900px){.news-layout{grid-template-columns:1fr!important}.news-body{padding:20px}.news-title{font-size:22px}.related-grid{grid-template-columns:1fr}}
</style>

<div class="news-wrap">
<div class="container">
<div class="news-layout">
<article class="news-article">
    <?php if(!empty($news['image_url'])): ?>
    <div class="news-hero"><img src="<?= h($news['image_url']) ?>" alt="<?= h($news['title']) ?>"></div>
    <?php endif; ?>
    <div class="news-body">
        <div class="news-breadcrumb">
            <a href="<?= BASE_PATH ?>/">Ana Sayfa</a><span>›</span>
            <?php if(!empty($news['cat_name'])): ?>
            <a href="<?= BASE_PATH ?>/<?= h($news['cat_slug']??'') ?>"><?= h($news['cat_name']) ?></a><span>›</span>
            <?php endif; ?>
            <span style="color:var(--text-2)"><?= h(mb_substr($news['title'],0,50)) ?>...</span>
        </div>
        <?php if(!empty($news['cat_name'])): ?>
        <span class="news-cat-badge" style="background:<?= h($news['cat_color']??'#cc1a25') ?>"><?= h($news['cat_name']) ?></span>
        <?php endif; ?>
        <h1 class="news-title"><?= h($news['title']) ?></h1>
        <div class="news-meta">
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?= timeAgo($news['published_at']) ?></span>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg><?= number_format($news['views']??0) ?> okunma</span>
            <?php if(!empty($news['author'])): ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><?= h($news['author']) ?></span><?php endif; ?>
        </div>
        <?php if(!empty($news['summary'])): ?>
        <div class="news-summary"><?= h($news['summary']) ?></div>
        <?php endif; ?>
        <div class="news-content"><?= $news['content']??'' ?></div>
        <?php if(!empty($news['tags'])): ?>
        <div class="news-tags">
            <?php foreach(explode(',',$news['tags']) as $tag): $tag=trim($tag); if(!$tag)continue; ?>
            <a href="<?= BASE_PATH ?>/arama?q=<?= urlencode($tag) ?>" class="news-tag"># <?= h($tag) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="news-share">
            <span style="font-size:12px;font-weight:600;color:var(--text-3)">Paylas:</span>
            <button onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href),'_blank','width=600,height=400')" class="share-btn" style="background:#1877f2">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg> Facebook
            </button>
            <button onclick="window.open('https://twitter.com/intent/tweet?url='+encodeURIComponent(location.href)+'&text='+encodeURIComponent(document.title),'_blank','width=600,height=400')" class="share-btn" style="background:#1da1f2">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg> Twitter
            </button>
            <button onclick="navigator.clipboard.writeText(location.href);this.textContent='Kopyalandi!'" class="share-btn" style="background:var(--text-3)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Kopyala
            </button>
        </div>
    </div>
    <?php if(!empty($related)): ?>
    <div class="news-body" style="border-top:1px solid var(--border);padding-top:24px">
        <div class="related-title">Ilgili Haberler</div>
        <div class="related-grid">
            <?php foreach($related as $r): ?>
            <div class="rc">
                <div class="rc-img"><img src="<?= h($r['image_url']??'https://placehold.co/400x225/eee/999?text=H') ?>" alt="<?= h($r['title']) ?>"></div>
                <div class="rc-body">
                    <h4><a href="<?= BASE_PATH ?>/haber/<?= h($r['slug']) ?>"><?= h(mb_substr($r['title'],0,65)) ?></a></h4>
                    <div class="rc-meta"><?= timeAgo($r['published_at']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</article>

<aside style="position:sticky;top:60px;display:flex;flex-direction:column;gap:20px">
    <div style="background:var(--bg-card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden">
        <div style="padding:12px 15px;border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:13px;font-weight:900;color:var(--navy)">Cok Okunanlar</span>
        </div>
        <div style="padding:6px 0">
            <?php foreach($mostRead as $i=>$mr): ?>
            <a href="<?= BASE_PATH ?>/haber/<?= h($mr['slug']) ?>" style="display:flex;align-items:flex-start;gap:10px;padding:10px 15px;border-bottom:1px solid var(--border);transition:background .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <span style="font-family:var(--fh);font-size:20px;font-weight:900;color:var(--red);opacity:.18;line-height:1;width:26px;flex-shrink:0;text-align:center"><?= $i+1 ?></span>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--text);line-height:1.35"><?= h(mb_substr($mr['title'],0,60)) ?></div>
                    <div style="font-size:10px;color:var(--text-3);margin-top:3px"><?= timeAgo($mr['published_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</aside>
</div>
</div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
