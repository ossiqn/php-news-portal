<?php
if (!function_exists('getPDO')) require_once __DIR__ . '/functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/config.php';
$_base = BASE_PATH;
$_fcats = getAllCategories();
$_fName = getSetting('site_title', SITE_NAME);
$_fDesc = getSetting('site_desc','Guncel haberler ve son dakika gelismeleri.');
$_fSocial = array(
    'facebook'  => getSetting('social_facebook','#'),
    'twitter'   => getSetting('social_twitter','#'),
    'instagram' => getSetting('social_instagram','#'),
    'youtube'   => getSetting('social_youtube','#'),
);
?>
<footer style="background:var(--navy);color:rgba(255,255,255,.7);margin-top:60px">
<div class="container" style="padding-top:52px;padding-bottom:0">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:44px;padding-bottom:44px;border-bottom:1px solid rgba(255,255,255,.07)">
        <div>
            <a href="/" style="display:flex;align-items:center;gap:11px;margin-bottom:18px">
                <div style="width:40px;height:40px;background:var(--red);border-radius:var(--r);display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-weight:900;font-size:18px;color:#fff;flex-shrink:0"><?= mb_substr($_fName,0,1) ?></div>
                <span style="font-family:var(--fh);font-size:18px;font-weight:900;color:#fff"><?= h($_fName) ?></span>
            </a>
            <p style="font-size:13px;line-height:1.9;color:rgba(255,255,255,.4);max-width:250px"><?= h($_fDesc) ?></p>
            <div style="display:flex;gap:9px;margin-top:22px">
                <?php
                $socials = array(
                    'facebook'=>array('#1877f2','<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'),
                    'twitter'=>array('#1da1f2','<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>'),
                    'instagram'=>array('#e1306c','<rect x="2" y="2" width="20" height="20" rx="5" fill="none" stroke="#fff" stroke-width="2"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="none" stroke="#fff" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'),
                    'youtube'=>array('#ff0000','<path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="var(--navy)"/>'),
                );
                foreach($socials as $key=>list($hoverClr,$path)):
                    $url = $_fSocial[$key];
                    if(!$url||$url==='#') continue;
                ?>
                <a href="<?= h($url) ?>" target="_blank" rel="noopener"
                   style="width:34px;height:34px;background:rgba(255,255,255,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;transition:background .2s;flex-shrink:0"
                   onmouseover="this.style.background='<?= $hoverClr ?>'"
                   onmouseout="this.style.background='rgba(255,255,255,.08)'">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" color="#fff"><?= $path ?></svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <h4 style="font-family:var(--fh);font-size:12px;color:#fff;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--red);text-transform:uppercase;letter-spacing:.06em">Kategoriler</h4>
            <ul style="list-style:none;display:flex;flex-direction:column;gap:9px">
                <?php foreach(array_slice($_fcats,0,7) as $fc): ?>
                <li><a href="/<?= h($fc['slug']) ?>" style="font-size:12px;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:6px;transition:color .15s" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <?= h($fc['name']) ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h4 style="font-family:var(--fh);font-size:12px;color:#fff;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--red);text-transform:uppercase;letter-spacing:.06em">Hizmetler</h4>
            <ul style="list-style:none;display:flex;flex-direction:column;gap:9px">
                <?php foreach(array('hava-durumu'=>'Hava Durumu','namaz-vakitleri'=>'Namaz Vakitleri','nobetci-eczaneler'=>'Nobetci Eczaneler','gazete-arsivi'=>'Gazete Arsivi','gazete-mansestleri'=>'Gazete Mansetleri','rss'=>'RSS Beslemesi') as $slug=>$name): ?>
                <li><a href="/<?= $slug ?>" style="font-size:12px;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:6px;transition:color .15s" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <?= $name ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h4 style="font-family:var(--fh);font-size:12px;color:#fff;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--red);text-transform:uppercase;letter-spacing:.06em">Kurumsal</h4>
            <ul style="list-style:none;display:flex;flex-direction:column;gap:9px">
                <?php foreach(array('hakkimizda'=>'Hakkimizda','iletisim'=>'Iletisim','gizlilik'=>'Gizlilik Politikasi','kullanim-sartlari'=>'Kullanim Sartlari','reklam'=>'Reklam','uye-paneli'=>'Uye Paneli') as $slug=>$name): ?>
                <li><a href="/<?= $slug ?>" style="font-size:12px;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:6px;transition:color .15s" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.45)'">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <?= $name ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 0;font-size:11px;color:rgba(255,255,255,.3);flex-wrap:wrap;gap:8px">
        <span>&copy; <?= date('Y') ?> <?= h($_fName) ?> &mdash; Tum Haklari Saklidir</span>
        <span>PHP <?= PHP_MAJOR_VERSION ?>.<?= PHP_MINOR_VERSION ?> &nbsp;&middot;&nbsp; <?= date('d.m.Y H:i') ?></span>
    </div>
</div>
</footer>
<style>
@media(max-width:900px){footer .container>div:first-of-type{grid-template-columns:1fr 1fr!important;gap:28px!important}}
@media(max-width:560px){footer .container>div:first-of-type{grid-template-columns:1fr!important}}
</style>
