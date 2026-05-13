<?php
require_once __DIR__ . '/../functions.php';

if(!isset($catSlug)) $catSlug = basename(dirname($_SERVER['PHP_SELF']));
$cat = getCategoryBySlug($catSlug);
if(!$cat){
    if(isset($catName)){
        $cat = array('id'=>0,'name'=>$catName,'slug'=>$catSlug,'color'=>$catColor??'#cc1a25');
    } else {
        header('Location: /'); exit;
    }
}

$page    = max(1,(int)($_GET['page']??1));
$perPage = 12;
$offset  = ($page-1)*$perPage;
$total   = countNewsByCategory((int)$cat['id']);
$news    = getNewsByCategory((int)$cat['id'],$perPage,$offset);
$pages   = (int)ceil($total/$perPage);
$mostRead = getMostRead(5);

$pageTitle = $cat['name'];
$pageDesc  = $cat['name'].' kategorisindeki son haberler';
include __DIR__ . '/../header.php';
?>
<style>
.cat-wrap{padding:24px 0 60px}
.cat-header{background:var(--bg-card);border-bottom:1px solid var(--border);padding:18px 0;margin-bottom:28px}
.cat-header-inner{display:flex;align-items:center;gap:14px}
.cat-color-bar{width:6px;height:36px;border-radius:3px;flex-shrink:0}
.cat-title{font-family:var(--fh);font-size:24px;font-weight:900;color:var(--navy)}
.cat-count{font-size:12px;color:var(--text-3);margin-top:2px}
.news-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.nc{background:var(--bg-card);border-radius:var(--r);overflow:hidden;box-shadow:var(--sh);transition:all .22s cubic-bezier(.4,0,.2,1)}
.nc:hover{box-shadow:var(--sh2);transform:translateY(-3px)}
.nc-img{aspect-ratio:16/9;overflow:hidden;position:relative}
.nc-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.nc:hover .nc-img img{transform:scale(1.05)}
.nc-body{padding:13px 15px 15px}
.nc-body h3{font-family:var(--fh);font-size:14px;font-weight:700;line-height:1.32;color:var(--text)}
.nc-body h3 a:hover{color:var(--red)}
.nc-body p{font-size:12px;color:var(--text-2);margin-top:5px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.nc-meta{font-size:11px;color:var(--text-3);margin-top:9px;display:flex;gap:10px}
.pager{display:flex;gap:5px;align-items:center;margin-top:28px;flex-wrap:wrap}
.pager a,.pager span{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:var(--r);font-size:12px;font-weight:600;border:1.5px solid var(--border);background:var(--bg-card);color:var(--text);padding:0 8px;transition:all .15s}
.pager a:hover,.pager span.cur{background:var(--red);color:#fff;border-color:var(--red)}
@media(max-width:860px){.news-grid{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.news-grid{grid-template-columns:1fr}}
</style>

<div class="cat-header">
    <div class="container">
        <div class="cat-header-inner">
            <div class="cat-color-bar" style="background:<?= h($cat['color']??'#cc1a25') ?>"></div>
            <div>
                <div class="cat-title"><?= h($cat['name']) ?></div>
                <div class="cat-count"><?= number_format($total) ?> haber bulundu</div>
            </div>
        </div>
    </div>
</div>

<div class="cat-wrap">
<div class="container">
<div style="display:grid;grid-template-columns:1fr 300px;gap:28px;align-items:start">
<div>
    <?php if(empty($news)): ?>
    <div style="text-align:center;padding:60px 0;color:var(--text-3)">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.2;margin:0 auto 16px;display:block"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
        Bu kategoride henuz haber yok.
    </div>
    <?php else: ?>
    <div class="news-grid">
        <?php foreach($news as $n): ?>
        <div class="nc">
            <div class="nc-img"><a href="/haber/<?= h($n['slug']) ?>"><img src="<?= h(imgUrl($n['image'])??'https://placehold.co/400x225/eee/999?text=H') ?>" alt="<?= h($n['title']) ?>"></a></div>
            <div class="nc-body">
                <h3><a href="/haber/<?= h($n['slug']) ?>"><?= h($n['title']) ?></a></h3>
                <?php if(!empty($n['summary'])): ?><p><?= h($n['summary']) ?></p><?php endif; ?>
                <div class="nc-meta">
                    <span><?= timeAgo($n['published_at']) ?></span>
                    <span><?= number_format($n['views']??0) ?> okunma</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if($pages>1): ?>
    <div class="pager">
        <?php if($page>1): ?><a href="?page=<?= $page-1 ?>">&lsaquo;</a><?php endif; ?>
        <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
        <?php if($i===$page): ?><span class="cur"><?= $i ?></span><?php else: ?><a href="?page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if($page<$pages): ?><a href="?page=<?= $page+1 ?>">&rsaquo;</a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<aside style="position:sticky;top:60px">
    <div style="background:var(--bg-card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden">
        <div style="padding:12px 15px;border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:13px;font-weight:900;color:var(--navy)">Cok Okunanlar</span>
        </div>
        <div>
            <?php foreach($mostRead as $i=>$mr): ?>
            <a href="/haber/<?= h($mr['slug']) ?>" style="display:flex;align-items:flex-start;gap:10px;padding:10px 15px;border-bottom:1px solid var(--border);transition:background .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <span style="font-family:var(--fh);font-size:20px;font-weight:900;color:var(--red);opacity:.2;line-height:1;width:26px;flex-shrink:0;text-align:center"><?= $i+1 ?></span>
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
