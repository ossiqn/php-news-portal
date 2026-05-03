<?php
if (!function_exists('getPDO')) {
    require_once __DIR__ . '/functions.php';
}
$_breaking = getBreakingNews();
$_allCats  = getAllCategories();
$_currentPage = basename($_SERVER['PHP_SELF'], '.php');
$_siteName    = getSetting('site_title', SITE_NAME);
$_siteDesc    = getSetting('site_desc', 'En guncel haberler ve son dakika gelismeleri.');
$_siteKeywords= getSetting('site_keywords', '');
$_logoPath    = getSetting('logo_path', '');
$_social = array(
    'facebook'  => getSetting('social_facebook',''),
    'twitter'   => getSetting('social_twitter',''),
    'instagram' => getSetting('social_instagram',''),
    'youtube'   => getSetting('social_youtube',''),
);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($pageTitle)?h($pageTitle).' — ':'' ?><?= h($_siteName) ?></title>
<meta name="description" content="<?= isset($pageDesc)?h($pageDesc):h($_siteDesc) ?>">
<?php if($_siteKeywords):?><meta name="keywords" content="<?= h($_siteKeywords) ?>"><?php endif;?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --red:#cc1a25;--red-dark:#a8141d;
    --navy:#0c1a2e;--navy-2:#142036;--navy-3:#1c2d47;
    --gold:#c8952a;
    --bg:#f5f4f0;--bg-2:#eeece7;--bg-card:#fff;
    --text:#111;--text-2:#3a3a3a;--text-3:#6b6b6b;
    --border:#e0ddd6;--border-2:#ccc9c0;
    --fh:'Merriweather',Georgia,serif;
    --fb:'Inter',-apple-system,sans-serif;
    --r:3px;
    --sh:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.07);
    --sh2:0 8px 32px rgba(0,0,0,.12);
}
html{scroll-behavior:smooth}
body{font-family:var(--fb);background:var(--bg);color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
a{text-decoration:none;color:inherit}
img{display:block;width:100%;height:100%;object-fit:cover}
svg{display:inline-block;vertical-align:middle;flex-shrink:0}
.container{max-width:1280px;margin:0 auto;padding:0 18px}

.breaking-bar{background:var(--navy);height:34px;display:flex;align-items:center;overflow:hidden}
.breaking-label{background:var(--red);color:#fff;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:0 14px;height:100%;display:flex;align-items:center;white-space:nowrap;gap:5px;flex-shrink:0}
.breaking-label::before{content:'';width:6px;height:6px;background:#fff;border-radius:50%;animation:blink 1.1s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.breaking-track-wrap{flex:1;overflow:hidden}
.breaking-track{display:flex;animation:scroll 35s linear infinite;white-space:nowrap}
.breaking-track:hover{animation-play-state:paused}
@keyframes scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.breaking-item{padding:0 32px;font-size:12px;color:rgba(255,255,255,.8);font-weight:500}
.breaking-item::after{content:'·';margin-left:32px;color:var(--red);opacity:.7}

.top-header{background:var(--bg-card);border-bottom:1px solid var(--border)}
.top-header-inner{display:flex;align-items:center;justify-content:space-between;padding:12px 0;gap:16px}
.brand{display:flex;align-items:center;gap:12px}
.brand-mark{width:44px;height:44px;background:var(--red);border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-weight:900;font-size:20px;color:#fff;flex-shrink:0}
.brand-text .brand-name{font-family:var(--fh);font-size:22px;font-weight:900;color:var(--navy);line-height:1.1}
.brand-text .brand-tag{font-size:10px;color:var(--text-3);margin-top:1px;letter-spacing:.04em}
.brand-logo-img{max-height:48px;max-width:200px;object-fit:contain;width:auto;height:auto}
.header-right{display:flex;align-items:center;gap:14px}
.header-date{font-size:11px;color:var(--text-3);text-align:right;line-height:1.6}
.header-date strong{display:block;font-size:12px;color:var(--text-2);font-weight:600}
.search-box{display:flex;align-items:center;background:var(--bg);border:1.5px solid var(--border);border-radius:20px;overflow:hidden;transition:border-color .2s}
.search-box:focus-within{border-color:var(--red)}
.search-box input{background:none;border:none;padding:7px 14px;font-family:var(--fb);font-size:13px;outline:none;width:170px;color:var(--text)}
.search-box button{background:var(--red);border:none;color:#fff;padding:8px 12px;cursor:pointer;display:flex;align-items:center;transition:background .2s}
.search-box button:hover{background:var(--red-dark)}

.main-nav{background:var(--navy);position:sticky;top:0;z-index:900;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.nav-inner{display:flex;align-items:stretch;overflow:visible}
.nav-item{position:relative;overflow:visible}
.nav-link{display:flex;align-items:center;gap:4px;padding:0 13px;height:44px;font-size:12px;font-weight:600;color:rgba(255,255,255,.75);letter-spacing:.05em;text-transform:uppercase;white-space:nowrap;transition:all .18s;border-bottom:2px solid transparent;cursor:pointer}
.nav-link:hover,.nav-link.active{color:#fff;border-bottom-color:var(--red);background:rgba(255,255,255,.04)}
.nav-link .arr{font-size:7px;opacity:.4;transition:transform .18s;margin-left:2px}
.nav-item:hover .arr{transform:rotate(180deg)}
.nav-dd{position:absolute;top:100%;left:0;min-width:200px;background:var(--bg-card);border-top:2px solid var(--red);box-shadow:var(--sh2);opacity:0;visibility:hidden;transform:translateY(-6px);transition:all .2s;z-index:1100}
.nav-item:hover .nav-dd{opacity:1;visibility:visible;transform:translateY(0)}
.nav-dd a{display:flex;align-items:center;gap:8px;padding:10px 15px;font-size:12px;font-weight:500;color:var(--text-2);border-bottom:1px solid var(--border);transition:all .15s}
.nav-dd a:last-child{border-bottom:none}
.nav-dd a:hover{background:var(--bg);color:var(--red);padding-left:19px}
.nav-mega{position:absolute;top:100%;left:0;width:580px;background:var(--bg-card);border-top:2px solid var(--red);box-shadow:var(--sh2);opacity:0;visibility:hidden;transform:translateY(-6px);transition:all .2s;z-index:1100;display:grid;grid-template-columns:repeat(3,1fr);padding:6px 0}
.nav-item:hover .nav-mega{opacity:1;visibility:visible;transform:translateY(0)}
.nav-mega a{display:flex;align-items:center;gap:6px;padding:9px 14px;font-size:12px;font-weight:500;color:var(--text-2);border-bottom:1px solid var(--border);transition:all .15s}
.nav-mega a:hover{background:var(--bg);color:var(--red)}
.nav-mega a::before{content:'›';color:var(--red);font-size:14px;font-weight:900;flex-shrink:0}
.nav-hamburger{display:none;align-items:center;justify-content:space-between;padding:0 14px;height:50px}
.ham-btn{background:none;border:none;cursor:pointer;display:flex;flex-direction:column;gap:5px;padding:5px;-webkit-tap-highlight-color:transparent}
.ham-btn span{display:block;width:21px;height:2px;background:#fff;transition:all .28s;border-radius:2px}
.ham-btn.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.ham-btn.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.ham-btn.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
.mob-search{flex:1;max-width:200px;display:flex;background:rgba(255,255,255,.09);border-radius:16px;overflow:hidden;margin:0 10px}
.mob-search input{background:none;border:none;color:#fff;padding:7px 12px;font-size:13px;outline:none;width:100%;font-family:var(--fb)}
.mob-search input::placeholder{color:rgba(255,255,255,.35)}
.mob-search button{background:none;border:none;color:#fff;padding:7px 10px;cursor:pointer;display:flex;align-items:center}
.mob-menu{display:flex;flex-direction:column;background:var(--navy-2);height:0;overflow:hidden;transition:height .3s cubic-bezier(.4,0,.2,1)}
.mob-menu.open{height:auto;max-height:75vh;overflow-y:auto}
.mob-menu a{display:flex;align-items:center;gap:10px;padding:13px 18px;color:rgba(255,255,255,.78);font-size:14px;font-weight:500;border-bottom:1px solid rgba(255,255,255,.05);-webkit-tap-highlight-color:transparent}
.mob-menu a svg{opacity:.45;flex-shrink:0}
.mob-cat{padding:6px 18px;font-size:10px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:.12em;background:rgba(0,0,0,.25)}

@media(max-width:860px){.top-header-inner{gap:10px}.brand-text .brand-name{font-size:18px}.header-date,.search-box{display:none}}
@media(max-width:680px){.nav-inner{display:none}.nav-hamburger{display:flex}}
</style>
</head>
<body>

<?php if (!empty($_breaking)): ?>
<div class="breaking-bar">
    <div class="container" style="display:flex;align-items:center;height:100%;padding:0 18px;max-width:100%">
        <div class="breaking-label">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
            Son Dakika
        </div>
        <div class="breaking-track-wrap">
            <div class="breaking-track">
                <?php foreach(array_merge($_breaking,$_breaking) as $b): ?>
                <span class="breaking-item"><a href="<?= BASE_PATH ?>/haber/<?= h($b['slug']) ?>" style="color:inherit"><?= h($b['title']) ?></a></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<header class="top-header">
    <div class="container">
        <div class="top-header-inner">
            <a href="<?= BASE_PATH ?>/" class="brand">
                <?php if($_logoPath): ?>
                <img src="<?= BASE_PATH . h($_logoPath) ?>" alt="<?= h($_siteName) ?>" class="brand-logo-img">
                <div class="brand-text">
                    <div class="brand-name"><?= h($_siteName) ?></div>
                    <div class="brand-tag">Dogru Haber · Guvenilir Kaynak</div>
                </div>
                <?php else: ?>
                <div class="brand-mark"><?= mb_substr($_siteName,0,1) ?></div>
                <div class="brand-text">
                    <div class="brand-name"><?= h($_siteName) ?></div>
                    <div class="brand-tag">Dogru Haber · Guvenilir Kaynak</div>
                </div>
                <?php endif; ?>
            </a>
            <div class="header-right">
                <div class="header-date">
                    <?php
                    $days2=['Pazar','Pazartesi','Sali','Carsamba','Persembe','Cuma','Cumartesi'];
                    $months2=['','Ocak','Subat','Mart','Nisan','Mayis','Haziran','Temmuz','Agustos','Eylul','Ekim','Kasim','Aralik'];
                    ?>
                    <strong><?= $days2[(int)date('w')] ?></strong>
                    <?= (int)date('d') ?> <?= $months2[(int)date('n')] ?> <?= date('Y') ?>
                </div>
                <form class="search-box" action="<?= BASE_PATH ?>/arama" method="get">
                    <input type="text" name="q" placeholder="Haber ara...">
                    <button type="submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<nav class="main-nav">
    <div class="container">
        <div class="nav-inner">
            <a href="<?= BASE_PATH ?>/" class="nav-link <?= $_currentPage==='index'?'active':'' ?>">Ana Sayfa</a>
            <div class="nav-item">
                <span class="nav-link">Haberler <span class="arr">▼</span></span>
                <div class="nav-mega">
                    <?php foreach($_allCats as $nc): ?>
                    <a href="<?= BASE_PATH ?>/<?= h($nc['slug']) ?>"><?= h($nc['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= BASE_PATH ?>/gundem" class="nav-link">Gundem</a>
            <a href="<?= BASE_PATH ?>/siyaset" class="nav-link">Siyaset</a>
            <a href="<?= BASE_PATH ?>/ekonomi" class="nav-link">Ekonomi</a>
            <a href="<?= BASE_PATH ?>/spor" class="nav-link">Spor</a>
            <a href="<?= BASE_PATH ?>/dunya" class="nav-link">Dunya</a>
            <a href="<?= BASE_PATH ?>/teknoloji" class="nav-link">Teknoloji</a>
            <div class="nav-item">
                <span class="nav-link">Hizmetler <span class="arr">▼</span></span>
                <div class="nav-dd">
                    <a href="<?= BASE_PATH ?>/hava-durumu">Hava Durumu</a>
                    <a href="<?= BASE_PATH ?>/namaz-vakitleri">Namaz Vakitleri</a>
                    <a href="<?= BASE_PATH ?>/nobetci-eczaneler">Nobetci Eczaneler</a>
                    <a href="<?= BASE_PATH ?>/gazete-arsivi">Gazete Arsivi</a>
                </div>
            </div>
            <div class="nav-item">
                <span class="nav-link">Daha Fazla <span class="arr">▼</span></span>
                <div class="nav-dd">
                    <a href="<?= BASE_PATH ?>/gunun-haberleri">Gunun Haberleri</a>
                    <a href="<?= BASE_PATH ?>/arsiv">Arsiv</a>
                    <a href="<?= BASE_PATH ?>/arama">Arama</a>
                    <a href="<?= BASE_PATH ?>/uye-paneli">Uye Paneli</a>
                </div>
            </div>
        </div>
        <div class="nav-hamburger">
            <a href="<?= BASE_PATH ?>/" style="color:#fff;font-family:var(--fh);font-weight:900;font-size:16px;flex-shrink:0"><?= h($_siteName) ?></a>
            <form class="mob-search" action="<?= BASE_PATH ?>/arama" method="get">
                <input type="text" name="q" placeholder="Ara...">
                <button type="submit"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></button>
            </form>
            <button class="ham-btn" id="hamBtn"><span></span><span></span><span></span></button>
        </div>
    </div>
    <div class="mob-menu" id="mobMenu">
        <a href="<?= BASE_PATH ?>/"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Ana Sayfa</a>
        <div class="mob-cat">Haberler</div>
        <?php foreach($_allCats as $mc): ?>
        <a href="<?= BASE_PATH ?>/<?= h($mc['slug']) ?>" style="padding-left:28px"><?= h($mc['name']) ?></a>
        <?php endforeach; ?>
        <div class="mob-cat">Hizmetler</div>
        <a href="<?= BASE_PATH ?>/hava-durumu">Hava Durumu</a>
        <a href="<?= BASE_PATH ?>/namaz-vakitleri">Namaz Vakitleri</a>
        <a href="<?= BASE_PATH ?>/nobetci-eczaneler">Nobetci Eczaneler</a>
        <div class="mob-cat">Diger</div>
        <a href="<?= BASE_PATH ?>/gunun-haberleri">Gunun Haberleri</a>
        <a href="<?= BASE_PATH ?>/arsiv">Arsiv</a>
        <a href="<?= BASE_PATH ?>/uye-paneli">Uye Paneli</a>
    </div>
</nav>

<script>
(function(){
    var btn=document.getElementById('hamBtn');
    var menu=document.getElementById('mobMenu');
    var open=false;
    function toggle(){
        open=!open;
        btn.classList.toggle('open',open);
        if(open){menu.style.height='0';menu.style.display='flex';requestAnimationFrame(function(){menu.style.height=menu.scrollHeight+'px';});menu.classList.add('open');}
        else{menu.style.height=menu.scrollHeight+'px';requestAnimationFrame(function(){requestAnimationFrame(function(){menu.style.height='0';});});menu.addEventListener('transitionend',function h(){menu.style.height='';menu.classList.remove('open');menu.removeEventListener('transitionend',h);},{once:true});}
    }
    if(btn)btn.addEventListener('click',toggle);
    document.querySelectorAll('.nav-item').forEach(function(item){
        var dd=item.querySelector('.nav-dd,.nav-mega');
        if(!dd)return;
        item.addEventListener('touchstart',function(e){
            var vis=dd.style.opacity==='1';
            document.querySelectorAll('.nav-dd,.nav-mega').forEach(function(d){d.style.opacity='';d.style.visibility='';d.style.transform='';});
            if(!vis){dd.style.opacity='1';dd.style.visibility='visible';dd.style.transform='translateY(0)';e.preventDefault();}
        },{passive:false});
    });
    document.addEventListener('touchstart',function(e){if(!e.target.closest('.nav-item')){document.querySelectorAll('.nav-dd,.nav-mega').forEach(function(d){d.style.opacity='';d.style.visibility='';d.style.transform='';});}});
})();
</script>
