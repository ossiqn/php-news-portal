<?php
require_once __DIR__ . '/../config.php';
adminAuth();
require_once ROOT_DIR . '/functions.php';

$activeTab = $_GET['tab'] ?? 'genel';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tab = $_POST['tab'] ?? 'genel';
    try {
        if ($tab === 'sifre') {
            $newUser = trim($_POST['new_username'] ?? '');
            $newPass = trim($_POST['new_password'] ?? '');
            $confirm = trim($_POST['confirm_password'] ?? '');
            if ($newPass && $newPass !== $confirm) {
                $error = 'Sifreler eslesmiyor.';
            } elseif ($newPass && strlen($newPass) < 4) {
                $error = 'Sifre en az 4 karakter olmali.';
            } else {
                $cfgPath = __DIR__ . '/config.php';
                $cfg = file_get_contents($cfgPath);
                if ($newUser) {
                    $cfg = preg_replace("/define\('ADMIN_USER',\s*'[^']*'\)/", "define('ADMIN_USER', '" . addslashes($newUser) . "')", $cfg);
                }
                if ($newPass) {
                    $newHash = hash('sha256', $newPass . ADMIN_PASS_SALT);
                    $cfg = preg_replace("/define\('ADMIN_PASS_HASH',\s*'[^']*'\)/", "define('ADMIN_PASS_HASH', '" . $newHash . "')", $cfg);
                }
                file_put_contents($cfgPath, $cfg);
                if ($newUser) $_SESSION['admin_user'] = $newUser;
                $success = 'Giris bilgileri guncellendi.';
                $activeTab = 'sifre';
            }
        } else {
        foreach ($_POST as $key => $value) {
            if (in_array($key, array('tab','submit','new_username','new_password','confirm_password'))) continue;
            if (is_array($value)) $value = implode(',', array_map('intval', $value));
            saveSetting($key, trim($value));
        }
        if (!empty($_FILES['logo_file']['name'])) {
            $uploadDir = ROOT_DIR . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
            $allowed = array('jpg','jpeg','png','gif','svg','webp');
            if (in_array($ext, $allowed)) {
                $fname = 'logo_' . date('Ymd_His') . '.' . $ext;
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadDir . $fname)) {
                    saveSetting('logo_path', '/uploads/' . $fname);
                    $success .= ' Logo guncellendi.';
                }
            }
        }
        logAction('Genel Ayarlar Duzenlendi.', $tab . ' sekmesi');
        $success = 'Ayarlar basariyla kaydedildi.' . $success;
        $activeTab = $tab;
        }
    } catch (PDOException $e) {
        $error = 'Kayit hatasi: ' . $e->getMessage();
    }
}

$s = function($k, $d='') { return getSetting($k, $d); };
$allCats = getAllCategories();

$modules = array(
    array('mod_weather',    'Hava Durumu'),
    array('mod_prayer',     'Namaz Vakitleri'),
    array('mod_pharmacy',   'Nobetci Eczaneler'),
    array('mod_archive',    'Arsiv'),
    array('mod_newspaper',  'Gazete Mansetleri'),
    array('mod_daily',      'Gunun Haberleri'),
    array('mod_membership', 'Uyelik Sistemi'),
    array('mod_search',     'Arama'),
    array('mod_comments',   'Yorumlar'),
    array('mod_rss',        'RSS Beslemesi'),
);

$pageTitle = 'Genel Ayarlar';
$breadcrumbs = array(array('label'=>'Ayarlar','url'=>'/admin/ayarlar/'),array('label'=>'Genel Ayarlar'));
require __DIR__ . '/../includes/layout.php';

$tabStyle = function($t) use ($activeTab) {
    return 'id="t-'.$t.'" class="tab-pane'.($activeTab===$t?' active':'').'" style="padding:28px 32px;"';
};
?>

<div class="page-head">
    <h1>GENEL AYARLAR</h1>
</div>

<?php if ($success): ?><div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert al-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="tab-nav" style="padding:0 20px;margin-bottom:0;border-bottom:2px solid var(--border);">
            <button class="tab-link <?= $activeTab==='genel'?'active':'' ?>" onclick="showTab('t-genel',this)">Genel</button>
            <button class="tab-link <?= $activeTab==='moduller'?'active':'' ?>" onclick="showTab('t-moduller',this)">Moduller</button>
            <button class="tab-link <?= $activeTab==='yorumlar'?'active':'' ?>" onclick="showTab('t-yorumlar',this)">Yorumlar</button>
            <button class="tab-link <?= $activeTab==='reklamlar'?'active':'' ?>" onclick="showTab('t-reklamlar',this)">Reklamlar</button>
            <button class="tab-link <?= $activeTab==='seo'?'active':'' ?>" onclick="showTab('t-seo',this)">SEO</button>
            <button class="tab-link <?= $activeTab==='diger'?'active':'' ?>" onclick="showTab('t-diger',this)">Diger</button>
        <button class="tab-link <?= $activeTab==='sifre'?'active':'' ?>" onclick="showTab('t-sifre',this)">Sifre & Guvenlik</button>
        </div>

        <!-- TAB: GENEL -->
        <div <?= $tabStyle('genel') ?>>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="tab" value="genel">
                <div style="max-width:700px;">
                    <div class="fg">
                        <label>Site Basligi</label>
                        <input type="text" name="site_title" value="<?= htmlspecialchars($s('site_title')) ?>" maxlength="70">
                        <div class="hint">Arama motorlarinda gorunecek baslik. Maks. 70 karakter.</div>
                    </div>
                    <div class="fg">
                        <label>Site Aciklamasi</label>
                        <textarea name="site_desc" rows="3" maxlength="160"><?= htmlspecialchars($s('site_desc')) ?></textarea>
                        <div class="hint">Meta description. Maks. 160 karakter.</div>
                    </div>
                    <div class="fg">
                        <label>Anahtar Kelimeler</label>
                        <input type="text" name="site_keywords" value="<?= htmlspecialchars($s('site_keywords')) ?>" maxlength="160">
                    </div>
                    <div class="fg">
                        <label>Sitenin Kisa Adi</label>
                        <input type="text" name="site_short_name" value="<?= htmlspecialchars($s('site_short_name')) ?>" maxlength="75">
                    </div>
                    <div class="fg">
                        <label>Sosyal Medya</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <input type="url" name="social_facebook" placeholder="Facebook URL" value="<?= htmlspecialchars($s('social_facebook')) ?>">
                            <input type="url" name="social_twitter" placeholder="Twitter/X URL" value="<?= htmlspecialchars($s('social_twitter')) ?>">
                            <input type="url" name="social_instagram" placeholder="Instagram URL" value="<?= htmlspecialchars($s('social_instagram')) ?>">
                            <input type="url" name="social_youtube" placeholder="YouTube URL" value="<?= htmlspecialchars($s('social_youtube')) ?>">
                        </div>
                    </div>
                    <div class="fg">
                        <label>Site Logosu</label>
                        <?php $logoPath = $s('logo_path',''); ?>
                        <?php if ($logoPath): ?>
                        <div style="margin-bottom:10px;"><img src="<?= sTr($logoPath) ?>" alt="Logo" style="max-height:60px;border:1px solid var(--border);border-radius:4px;padding:6px;background:#f8f9fb;"></div>
                        <?php endif; ?>
                        <input type="file" name="logo_file" accept="image/*" style="padding:8px;border:1.5px solid var(--border);border-radius:var(--r);width:100%;background:#fff;">
                        <div class="hint">PNG veya SVG onerilen. Max 2MB.</div>
                    </div>
                    <div class="fg">
                        <label>Google Analytics ID</label>
                        <input type="text" name="google_analytics" placeholder="G-XXXXXXXXXX" value="<?= htmlspecialchars($s('google_analytics')) ?>">
                    </div>
                    <div class="fg">
                        <label>Google AdSense ID</label>
                        <input type="text" name="google_adsense" placeholder="ca-pub-XXXXXXXXXXXXXXXX" value="<?= htmlspecialchars($s('google_adsense')) ?>">
                    </div>
                    <div class="fg">
                        <label>Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel" placeholder="XXXXXXXXXXXXXXXX" value="<?= htmlspecialchars($s('facebook_pixel')) ?>">
                    </div>
                    <button type="submit" name="submit" class="btn btn-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Kaydet
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB: MODULLER -->
        <div <?= $tabStyle('moduller') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="moduller">
                <div style="max-width:700px;">
                    <div class="alert al-info" style="margin-bottom:20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Kapattiginizda ilgili sayfaya erisim olmayacaktir.
                    </div>
                    <?php foreach ($modules as $mod): $key=$mod[0]; $label=$mod[1]; ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--tx);"><?= $label ?></div>
                            <div style="font-size:11px;color:var(--txs);margin-top:2px;">Kapatirsaniz ilgili sayfaya erisim olmayacaktir.</div>
                        </div>
                        <div class="rc-group">
                            <label class="rc-item"><input type="radio" name="<?= $key ?>" value="1" <?= $s($key,'1')!=='0'?'checked':'' ?>><span>Aktif</span></label>
                            <label class="rc-item"><input type="radio" name="<?= $key ?>" value="0" <?= $s($key,'1')==='0'?'checked':'' ?>><span>Pasif</span></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div style="margin-top:20px;">
                        <button type="submit" name="submit" class="btn btn-success btn-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Modulleri Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB: YORUMLAR -->
        <div <?= $tabStyle('yorumlar') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="yorumlar">
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head">
                        <h2>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--acc)"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            Yorum Ayarlari
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php
                        $commentFields = array(
                            array('comments_status','Yorum Sistemi','open','Yorumlari tamamen acip kapatabilirsiniz.','Acik','open','Kapali','closed'),
                            array('comment_approval','Onay Sistemi','manual','Yorumlari moderasyon gerektirerek veya direkt yayinlayabilirsiniz.','Onay Gereksin','manual','Direkt Yayinla','auto'),
                            array('comment_membership','Yorum Yetkileri','all','Kimlerin yorum yapabilecegini belirleyin.','Sadece Uyeler','members','Herkes','all'),
                            array('comment_email_required','Email Zorunlu','0','Yorum formunda email alani zorunlu olsun mu?','Zorunlu','1','Istege Bagli','0'),
                        );
                        foreach ($commentFields as $cf):
                            $key=$cf[0]; $label=$cf[1]; $def=$cf[2]; $hint=$cf[3]; $l1=$cf[4]; $v1=$cf[5]; $l2=$cf[6]; $v2=$cf[7];
                        ?>
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:14px 0;border-bottom:1px solid var(--border);">
                            <div style="flex:1;max-width:420px;">
                                <div style="font-size:13px;font-weight:700;color:var(--tx);"><?= $label ?></div>
                                <?php if($hint): ?><div style="font-size:11px;color:var(--txs);margin-top:3px;"><?= $hint ?></div><?php endif; ?>
                            </div>
                            <div class="rc-group">
                                <label class="rc-item"><input type="radio" name="<?= $key ?>" value="<?= $v1 ?>" <?= $s($key,$def)===$v1?'checked':'' ?>><span><?= $l1 ?></span></label>
                                <label class="rc-item"><input type="radio" name="<?= $key ?>" value="<?= $v2 ?>" <?= $s($key,$def)===$v2?'checked':'' ?>><span><?= $l2 ?></span></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head">
                        <h2>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--acc)"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Icerik Filtresi
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="fg">
                            <label>Sansurlecek Kelimeler</label>
                            <input type="text" name="censored_words" value="<?= htmlspecialchars($s('censored_words')) ?>" placeholder="kelime1,kelime2,kelime3">
                            <div class="hint">Virgülle ayirin. Bu kelimeleri iceren yorumlar ***** ile degistirilir.</div>
                        </div>
                        <div class="fg">
                            <label>Yorum Formu Bilgi Metni</label>
                            <textarea name="comment_info" rows="3" placeholder="Yorum kurallari veya bilgilendirme metni..."><?= htmlspecialchars($s('comment_info')) ?></textarea>
                            <div class="hint">Yorum formunun ustunde goruntulenecek metin.</div>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-success btn-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Yorum Ayarlarini Kaydet
                </button>
            </form>
        </div>

        <!-- TAB: REKLAMLAR -->
        <div <?= $tabStyle('reklamlar') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="reklamlar">
                <div style="max-width:700px;">
                    <div class="fg">
                        <label>ads.txt Icerigi</label>
                        <textarea name="ads_txt" rows="6" placeholder="google.com, pub-XXXXXXXX, DIRECT, f08c47fec0942fa0"><?= htmlspecialchars($s('ads_txt')) ?></textarea>
                        <div class="hint">Google AdSense yayinci kimligi.</div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-size:13px;font-weight:700;">Site Onyz Reklamlar</div>
                            <div style="font-size:11px;color:var(--txs);">Goster secili oldugunda reklam alanlari gorunur.</div>
                        </div>
                        <div class="rc-group">
                            <label class="rc-item"><input type="radio" name="ads_preview" value="1" <?= $s('ads_preview')==='1'?'checked':'' ?>><span>Goster</span></label>
                            <label class="rc-item"><input type="radio" name="ads_preview" value="0" <?= $s('ads_preview')!=='1'?'checked':'' ?>><span>Gosterme</span></label>
                        </div>
                    </div>
                    <div style="margin-top:20px;">
                        <button type="submit" name="submit" class="btn btn-success btn-lg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Reklam Ayarlarini Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB: SEO -->
        <div <?= $tabStyle('seo') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="seo">
                <div class="alert al-warn" style="margin-bottom:24px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Ozellestirme hakkinda yeterli bilgi sahibi degilseniz bu kisimla ilgilenmeyin.
                </div>
                <?php
                $seoGroups = array(
                    'Haber Listeleme' => array(
                        array('seo_news_title','Haber Etiket Title','{kelime} haberleri','{kelime}'),
                        array('seo_news_desc','Haber Etiket Description','{kelime} haberleri en son {kelime} haberleri','{kelime}'),
                    ),
                    'Arsiv Sayfalari' => array(
                        array('seo_archive_title','Arsiv Title','Arsiv',''),
                        array('seo_archive_desc','Arsiv Description','Haber arsivimiz',''),
                        array('seo_archive_detail_title','Arsiv Detay Title','{tarih} Arsiv','{tarih}'),
                        array('seo_archive_detail_desc','Arsiv Detay Description','{tarih} tarihinde yayinlanan icerikler','{tarih}'),
                    ),
                    'Haber Detay' => array(
                        array('seo_daily_title','Gunun Haberleri Title','{tarih} Haberleri','{tarih}'),
                        array('seo_daily_desc','Gunun Haberleri Description','Gun icinde yayinlanan tum icerikler',''),
                        array('seo_news_detail_title','Haber Detay Title','{baslik} - {kategori} - {sitekisa}','{baslik},{kategori},{sitekisa}'),
                    ),
                );
                foreach ($seoGroups as $groupName => $fields):
                ?>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head">
                        <h2>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--acc)"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <?= $groupName ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php foreach ($fields as $field): $key=$field[0]; $label=$field[1]; $placeholder=$field[2]; $tags=$field[3]; ?>
                        <div class="fg">
                            <label><?= $label ?></label>
                            <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($s($key,$placeholder)) ?>" placeholder="<?= htmlspecialchars($placeholder) ?>">
                            <?php if($tags): ?><div class="hint">Kullanilabilir degiskenler: <code style="background:var(--bg);padding:1px 5px;border-radius:3px;font-size:11px;"><?= $tags ?></code></div><?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <button type="submit" name="submit" class="btn btn-success btn-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    SEO Ayarlarini Kaydet
                </button>
            </form>
        </div>

        <!-- TAB: DIGER -->
        <div <?= $tabStyle('diger') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="diger">
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-head">
                        <h2>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--acc)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Sistem
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="fg">
                            <label>Saat Dilimi</label>
                            <select name="timezone">
                                <?php
                                $tzs = array(
                                    'Europe/Istanbul' => 'Europe/Istanbul (IMT +3)',
                                    'Europe/London'   => 'Europe/London (GMT)',
                                    'America/New_York'=> 'America/New_York (EST -5)',
                                    'Asia/Dubai'      => 'Asia/Dubai (GST +4)',
                                );
                                foreach ($tzs as $val => $lbl):
                                ?>
                                <option value="<?= $val ?>" <?= $s('timezone','Europe/Istanbul')===$val?'selected':'' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        $digerFields = array(
                            array('rss_enabled','RSS Beslemesi','1','1','Acik','0','Kapali','RSS erisimini kapatip acabilirsiniz.'),
                            array('rss_content','RSS Icerik Detayi','1','1','Tam Icerik','0','Sadece Ozet','RSS haber icerigini tam veya ozet gosterir.'),
                            array('search_type','Arama Motoru','content','content','Baslik ve Icerik','title','Sadece Baslik','Aramanin hangi alanlarda yapilacagini belirler.'),
                            array('view_cookie','Tekrar Okuma Sayimi','1','1','Cookie ile Engelle','0','Her Zaman Say','Ayni kullanicidan tekrar okunmayi sayar.'),
                            array('view_performance','Okunma Sayaci','0','0','Kapali (Daha Hizli)','1','Acik','Kapatmak sayfa performansini artirir.'),
                            array('performance_cache','Onbellek (Cache)','1','1','Acik','0','Kapali','Sayfa onbellekleme etkin/pasif.'),
                        );
                        foreach ($digerFields as $df):
                            $key=$df[0]; $label=$df[1]; $def=$df[2]; $v1=$df[3]; $l1=$df[4]; $v2=$df[5]; $l2=$df[6]; $hint=$df[7];
                        ?>
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);gap:20px;">
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:700;color:var(--tx);"><?= $label ?></div>
                                <div style="font-size:11px;color:var(--txs);margin-top:3px;"><?= $hint ?></div>
                            </div>
                            <div class="rc-group" style="flex-shrink:0;">
                                <label class="rc-item"><input type="radio" name="<?= $key ?>" value="<?= $v1 ?>" <?= $s($key,$def)===$v1?'checked':'' ?>><span><?= $l1 ?></span></label>
                                <label class="rc-item"><input type="radio" name="<?= $key ?>" value="<?= $v2 ?>" <?= $s($key,$def)===$v2?'checked':'' ?>><span><?= $l2 ?></span></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-success btn-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Diger Ayarlari Kaydet
                </button>
            </form>
        </div>

    </div>
</div>

        <!-- TAB: SIFRE -->
        <div <?= $tabStyle('sifre') ?>>
            <form method="post">
                <input type="hidden" name="tab" value="sifre">
                <div class="card" style="margin-bottom:20px;max-width:500px">
                    <div class="card-head"><h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--acc)"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Giris Bilgileri</h2></div>
                    <div class="card-body">
                        <div class="fg">
                            <label>Kullanici Adi</label>
                            <input type="text" name="new_username" placeholder="<?= sTr(ADMIN_USER) ?>" autocomplete="off">
                            <div class="hint">Bos birakirsaniz degismez.</div>
                        </div>
                        <div class="fg">
                            <label>Yeni Sifre</label>
                            <input type="password" name="new_password" placeholder="Yeni sifre girin..." autocomplete="new-password">
                            <div class="hint">Bos birakirsaniz degismez. En az 4 karakter.</div>
                        </div>
                        <div class="fg">
                            <label>Sifre Tekrar</label>
                            <input type="password" name="confirm_password" placeholder="Sifreyi tekrar girin...">
                        </div>
                    </div>
                </div>
                <div class="alert al-warn" style="max-width:500px;margin-bottom:20px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Degisiklikten sonra tekrar giris yapmaniz gerekebilir.
                </div>
                <button type="submit" name="submit" class="btn btn-danger btn-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Bilgileri Guncelle
                </button>
            </form>
        </div>

    </div>
</div>

<script>
var activeTab = '<?= $activeTab ?>';
var tabMap = {genel:'t-genel',moduller:'t-moduller',yorumlar:'t-yorumlar',reklamlar:'t-reklamlar',seo:'t-seo',diger:'t-diger'};
if (tabMap[activeTab]) {
    var pane = document.getElementById(tabMap[activeTab]);
    var btns = document.querySelectorAll('.tab-link');
    if (pane) { document.querySelectorAll('.tab-pane').forEach(function(p){p.classList.remove('active');}); pane.classList.add('active'); }
    btns.forEach(function(b){ if(b.textContent.trim().toLowerCase().indexOf(activeTab)>=0) {} });
}
</script>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
