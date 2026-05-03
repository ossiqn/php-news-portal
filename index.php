<?php
require_once __DIR__ . '/functions.php';

$featured  = getFeaturedNews(7);
$latest    = getLatestNews(8);
$mostRead  = getMostRead(5);
$trending  = getTrendingNews(4);
$allCats   = getAllCategories();

$heroMain  = $featured[0] ?? null;
$heroSide  = array_slice($featured,1,2);
$heroSmall = array_slice($featured,3,4);

$catSections = array();
foreach($allCats as $cat){
    $items = getNewsByCategory((int)$cat['id'],6);
    if(!empty($items)) $catSections[] = array('cat'=>$cat,'items'=>$items);
}

function badge($name,$color,$size='sm'){
    $fs=$size==='xs'?'10px':'11px';
    $pad=$size==='xs'?'2px 7px':'3px 9px';
    return '<span style="display:inline-block;background:'.htmlspecialchars($color).';color:#fff;font-size:'.$fs.';font-weight:700;padding:'.$pad.';border-radius:2px;letter-spacing:.05em;text-transform:uppercase;line-height:1.4;">'.htmlspecialchars($name).'</span>';
}
function vc($v){
    if($v>=1000000)return round($v/1000000,1).'M';
    if($v>=1000)return round($v/1000,1).'B';
    return (string)$v;
}

try { $totalNews  = (int)getPDO()->query('SELECT COUNT(*) FROM news WHERE status=1')->fetchColumn(); } catch(Throwable $e){ $totalNews=0; }
try { $totalViews = (int)getPDO()->query('SELECT COALESCE(SUM(views),0) FROM news WHERE status=1')->fetchColumn(); } catch(Throwable $e){ $totalViews=0; }
try { $todayCount = (int)getPDO()->query("SELECT COUNT(*) FROM news WHERE status=1 AND DATE(published_at)=CURDATE()")->fetchColumn(); } catch(Throwable $e){ $todayCount=0; }
try { $catCount   = (int)getPDO()->query('SELECT COUNT(*) FROM categories')->fetchColumn(); } catch(Throwable $e){ $catCount=0; }

include __DIR__ . '/header.php';
?>
<style>
:root{--gap:18px}

.ic{position:relative;overflow:hidden;display:block;border-radius:var(--r)}
.ic img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.ic:hover img{transform:scale(1.04)}
.ic-grad{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.92) 0%,rgba(0,0,0,.3) 50%,transparent 100%)}
.ic-body{position:absolute;bottom:0;left:0;right:0;padding:16px 14px}
.ic-body h3{font-family:var(--fh);color:#fff;line-height:1.25;margin-top:6px}
.ic-meta{font-size:11px;color:rgba(255,255,255,.55);margin-top:5px;display:flex;align-items:center;gap:10px}

.mc{background:var(--bg-card);border-radius:var(--r);overflow:hidden;box-shadow:var(--sh);transition:all .22s cubic-bezier(.4,0,.2,1)}
.mc:hover{box-shadow:var(--sh2);transform:translateY(-3px)}
.mc-img{aspect-ratio:16/9;overflow:hidden;position:relative;flex-shrink:0}
.mc-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.mc:hover .mc-img img{transform:scale(1.05)}
.mc-body{padding:13px 15px 15px}
.mc-body h3{font-family:var(--fh);font-size:14px;font-weight:700;line-height:1.32;margin-top:7px;color:var(--text)}
.mc-body h3 a:hover{color:var(--red)}
.mc-body p{font-size:12px;color:var(--text-2);margin-top:5px;line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.mc-meta{font-size:11px;color:var(--text-3);margin-top:9px;display:flex;gap:10px;align-items:center}

.li{display:flex;gap:11px;padding:12px 0;border-bottom:1px solid var(--border);align-items:flex-start}
.li:last-child{border-bottom:none}
.li-thumb{width:86px;height:58px;border-radius:var(--r);overflow:hidden;flex-shrink:0}
.li-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.li:hover .li-thumb img{transform:scale(1.07)}
.li-body h4{font-family:var(--fh);font-size:13px;font-weight:700;line-height:1.32;color:var(--text);margin-top:4px}
.li-body h4 a:hover{color:var(--red)}
.li-meta{font-size:10px;color:var(--text-3);margin-top:4px;display:flex;gap:7px}

.sec-h{display:flex;align-items:center;gap:11px;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--border)}
.sec-h::before{content:'';width:4px;height:24px;background:var(--ac,var(--red));border-radius:2px;flex-shrink:0}
.sec-h h2{font-family:var(--fh);font-size:18px;font-weight:900;color:var(--navy);letter-spacing:-.01em}
.sec-h .all{margin-left:auto;font-size:10px;font-weight:700;color:var(--ac,var(--red));text-transform:uppercase;letter-spacing:.09em;padding:4px 11px;border:1.5px solid currentColor;border-radius:2px;transition:all .18s;white-space:nowrap}
.sec-h .all:hover{background:var(--ac,var(--red));color:#fff}

.pg-wrap{padding:22px 0 56px}

.stat-strip{background:var(--bg-card);border-bottom:1px solid var(--border);padding:12px 0;margin-bottom:28px}
.stat-strip-inner{display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.stat-item{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-3)}
.stat-item strong{font-size:16px;font-weight:800;color:var(--navy);font-family:var(--fh)}
.stat-item svg{color:var(--red);width:14px;height:14px}

@media(max-width:900px){.g3{grid-template-columns:1fr 1fr!important}}
@media(max-width:600px){.g3{grid-template-columns:1fr!important}.g2{grid-template-columns:1fr!important}}
</style>

<div class="pg-wrap">
<div class="container">

<div class="stat-strip" style="background:var(--bg-card);border-bottom:1px solid var(--border);padding:10px 0;margin-bottom:24px">
    <div class="container">
        <div class="stat-strip-inner">
            <div class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
                <span><strong><?= number_format($totalNews) ?></strong> Haber</span>
            </div>
            <div class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <span><strong><?= vc($totalViews) ?></strong> Okunma</span>
            </div>
            <div class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span><strong><?= $todayCount ?></strong> Bugunku Haber</span>
            </div>
            <div class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <span><strong><?= $catCount ?></strong> Kategori</span>
            </div>
        </div>
    </div>
</div>

<?php if($heroMain): ?>
<div style="display:grid;grid-template-columns:1.55fr 1fr;gap:var(--gap);margin-bottom:28px" class="g2">
    <a href="<?= BASE_PATH ?>/haber/<?= h($heroMain['slug']) ?>" class="ic" style="height:420px">
        <img src="<?= h($heroMain['image_url']??'https://placehold.co/900x500/0c1a2e/fff?text=Haber') ?>" alt="<?= h($heroMain['title']) ?>">
        <div class="ic-grad"></div>
        <div class="ic-body">
            <?= badge($heroMain['cat_name']??'Haber',$heroMain['cat_color']??'#cc1a25') ?>
            <h3 style="font-size:22px;font-weight:900;margin-top:8px"><?= h(mb_substr($heroMain['title'],0,90)) ?></h3>
            <div class="ic-meta">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= timeAgo($heroMain['published_at']) ?>
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <?= vc($heroMain['views']??0) ?>
            </div>
        </div>
    </a>
    <div style="display:flex;flex-direction:column;gap:var(--gap)">
        <?php foreach($heroSide as $hs): ?>
        <a href="<?= BASE_PATH ?>/haber/<?= h($hs['slug']) ?>" class="ic" style="flex:1;min-height:196px">
            <img src="<?= h($hs['image_url']??'https://placehold.co/600x350/142036/fff?text=Haber') ?>" alt="<?= h($hs['title']) ?>">
            <div class="ic-grad"></div>
            <div class="ic-body">
                <?= badge($hs['cat_name']??'Haber',$hs['cat_color']??'#cc1a25','xs') ?>
                <h3 style="font-size:15px;font-weight:800;margin-top:6px"><?= h(mb_substr($hs['title'],0,70)) ?></h3>
                <div class="ic-meta"><?= timeAgo($hs['published_at']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php if(!empty($heroSmall)): ?>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--gap);margin-bottom:36px" class="g3">
    <?php foreach($heroSmall as $hs): ?>
    <a href="<?= BASE_PATH ?>/haber/<?= h($hs['slug']) ?>" class="ic" style="height:180px">
        <img src="<?= h($hs['image_url']??'https://placehold.co/400x250/1c2d47/fff?text=Haber') ?>" alt="<?= h($hs['title']) ?>">
        <div class="ic-grad"></div>
        <div class="ic-body">
            <?= badge($hs['cat_name']??'Haber',$hs['cat_color']??'#cc1a25','xs') ?>
            <h3 style="font-size:13px;font-weight:800;margin-top:5px"><?= h(mb_substr($hs['title'],0,55)) ?></h3>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start" class="g2">
<div>

<?php foreach($catSections as $section):
    $cat=$section['cat'];
    $items=$section['items'];
    $main=$items[0];
    $rest=array_slice($items,1,5);
    $ac=$cat['color']??'#cc1a25';
?>
<div style="margin-bottom:44px">
    <div class="sec-h" style="--ac:<?= h($ac) ?>">
        <h2><?= h($cat['name']) ?></h2>
        <a href="<?= BASE_PATH ?>/<?= h($cat['slug']) ?>" class="all">Tumu &rsaquo;</a>
    </div>
    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:var(--gap)" class="g2">
        <a href="<?= BASE_PATH ?>/haber/<?= h($main['slug']) ?>" class="ic" style="height:260px">
            <img src="<?= h($main['image_url']??'https://placehold.co/600x380/'.ltrim($ac,'#').'/fff?text='.urlencode($cat['name'])) ?>" alt="<?= h($main['title']) ?>">
            <div class="ic-grad"></div>
            <div class="ic-body">
                <h3 style="font-size:16px;font-weight:800"><?= h(mb_substr($main['title'],0,80)) ?></h3>
                <div class="ic-meta"><?= timeAgo($main['published_at']) ?></div>
            </div>
        </a>
        <div>
            <?php foreach($rest as $r): ?>
            <div class="li">
                <div class="li-thumb"><img src="<?= h($r['image_url']??'https://placehold.co/200x140/eee/999?text=H') ?>" alt="<?= h($r['title']) ?>"></div>
                <div class="li-body">
                    <h4><a href="<?= BASE_PATH ?>/haber/<?= h($r['slug']) ?>"><?= h(mb_substr($r['title'],0,65)) ?></a></h4>
                    <div class="li-meta"><?= timeAgo($r['published_at']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

</div>
<aside style="position:sticky;top:60px;display:flex;flex-direction:column;gap:22px">

    <div style="background:var(--bg-card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden">
        <div style="padding:13px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            <span style="font-family:var(--fh);font-size:13px;font-weight:900;color:var(--navy)">Cok Okunanlar</span>
        </div>
        <div style="padding:8px 0">
            <?php foreach($mostRead as $i=>$mr): ?>
            <a href="<?= BASE_PATH ?>/haber/<?= h($mr['slug']) ?>" style="display:flex;align-items:flex-start;gap:10px;padding:10px 16px;border-bottom:1px solid var(--border);transition:background .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <span style="font-family:var(--fh);font-size:22px;font-weight:900;color:var(--red);opacity:.18;line-height:1;width:28px;flex-shrink:0;text-align:center"><?= $i+1 ?></span>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--text);line-height:1.35"><?= h(mb_substr($mr['title'],0,65)) ?></div>
                    <div style="font-size:10px;color:var(--text-3);margin-top:4px;display:flex;gap:6px">
                        <span><?= timeAgo($mr['published_at']) ?></span>
                        <span><?= vc($mr['views']??0) ?> okunma</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if(!empty($trending)): ?>
    <div style="background:var(--navy);border-radius:var(--r);overflow:hidden">
        <div style="padding:13px 16px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:8px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
            <span style="font-family:var(--fh);font-size:13px;font-weight:900;color:#fff">Trend</span>
        </div>
        <?php foreach($trending as $tr): ?>
        <a href="<?= BASE_PATH ?>/haber/<?= h($tr['slug']) ?>" style="display:flex;gap:10px;align-items:flex-start;padding:11px 16px;border-bottom:1px solid rgba(255,255,255,.06);transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background=''">
            <div style="width:72px;height:50px;border-radius:var(--r);overflow:hidden;flex-shrink:0"><img src="<?= h($tr['image_url']??'https://placehold.co/200x140/142036/fff?text=H') ?>" alt="<?= h($tr['title']) ?>"></div>
            <div>
                <div style="font-size:12px;font-weight:600;color:rgba(255,255,255,.85);line-height:1.35"><?= h(mb_substr($tr['title'],0,60)) ?></div>
                <div style="font-size:10px;color:rgba(255,255,255,.35);margin-top:4px"><?= timeAgo($tr['published_at']) ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="background:var(--bg-card);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden">
        <div style="padding:13px 16px;border-bottom:1px solid var(--border)">
            <span style="font-family:var(--fh);font-size:13px;font-weight:900;color:var(--navy)">Kategoriler</span>
        </div>
        <div style="padding:10px 16px 14px;display:flex;flex-wrap:wrap;gap:6px">
            <?php foreach($allCats as $ac2): ?>
            <a href="<?= BASE_PATH ?>/<?= h($ac2['slug']) ?>" style="display:inline-block;background:var(--bg);border:1px solid var(--border);border-radius:2px;padding:4px 10px;font-size:11px;font-weight:600;color:var(--text-2);transition:all .15s" onmouseover="this.style.background='<?= h($ac2['color']??'#cc1a25') ?>';this.style.color='#fff';this.style.borderColor='transparent'" onmouseout="this.style.background='var(--bg)';this.style.color='var(--text-2)';this.style.borderColor='var(--border)'"><?= h($ac2['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

</aside>
</div>

</div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
