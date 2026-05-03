<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Nöbetçi Eczaneler';

$COLLECTAPI_KEY = 'apikey 4fI4zaHViljrqvNE3n5R1N:4USkBzsplmXU7pF4YOne9d';

if (session_status() === PHP_SESSION_NONE) session_start();

$ilMap = [
    'adana'=>'Adana','adiyaman'=>'Adıyaman','afyonkarahisar'=>'Afyonkarahisar',
    'agri'=>'Ağrı','amasya'=>'Amasya','ankara'=>'Ankara','antalya'=>'Antalya',
    'artvin'=>'Artvin','aydin'=>'Aydın','balikesir'=>'Balıkesir',
    'bilecik'=>'Bilecik','bingol'=>'Bingöl','bitlis'=>'Bitlis','bolu'=>'Bolu',
    'burdur'=>'Burdur','bursa'=>'Bursa','canakkale'=>'Çanakkale',
    'cankiri'=>'Çankırı','corum'=>'Çorum','denizli'=>'Denizli',
    'diyarbakir'=>'Diyarbakır','edirne'=>'Edirne','elazig'=>'Elazığ',
    'erzincan'=>'Erzincan','erzurum'=>'Erzurum','eskisehir'=>'Eskişehir',
    'gaziantep'=>'Gaziantep','giresun'=>'Giresun','gumushane'=>'Gümüşhane',
    'hakkari'=>'Hakkari','hatay'=>'Hatay','isparta'=>'Isparta',
    'mersin'=>'Mersin','istanbul'=>'İstanbul','izmir'=>'İzmir',
    'kars'=>'Kars','kastamonu'=>'Kastamonu','kayseri'=>'Kayseri',
    'kirklareli'=>'Kırklareli','kirsehir'=>'Kırşehir','kocaeli'=>'Kocaeli',
    'konya'=>'Konya','kutahya'=>'Kütahya','malatya'=>'Malatya',
    'manisa'=>'Manisa','kahramanmaras'=>'Kahramanmaraş','mardin'=>'Mardin',
    'mugla'=>'Muğla','mus'=>'Muş','nevsehir'=>'Nevşehir','nigde'=>'Niğde',
    'ordu'=>'Ordu','rize'=>'Rize','sakarya'=>'Sakarya','samsun'=>'Samsun',
    'siirt'=>'Siirt','sinop'=>'Sinop','sivas'=>'Sivas','tekirdag'=>'Tekirdağ',
    'tokat'=>'Tokat','trabzon'=>'Trabzon','tunceli'=>'Tunceli',
    'sanliurfa'=>'Şanlıurfa','usak'=>'Uşak','van'=>'Van','yozgat'=>'Yozgat',
    'zonguldak'=>'Zonguldak','aksaray'=>'Aksaray','bayburt'=>'Bayburt',
    'karaman'=>'Karaman','kirikkale'=>'Kırıkkale','batman'=>'Batman',
    'sirnak'=>'Şırnak','bartin'=>'Bartın','ardahan'=>'Ardahan',
    'igdir'=>'Iğdır','yalova'=>'Yalova','karabuk'=>'Karabük',
    'kilis'=>'Kilis','osmaniye'=>'Osmaniye','duzce'=>'Düzce',
];

$citySlug = trim($_GET['city'] ?? '');
$district = trim($_GET['district'] ?? '');
$pharmacies = [];
$apiError   = '';

if ($citySlug && isset($ilMap[$citySlug])) {
    $ilAdi = $ilMap[$citySlug];
    $url   = 'https://api.collectapi.com/health/dutyPharmacy?il=' . rawurlencode($ilAdi);
    if ($district !== '') {
        $url .= '&ilce=' . rawurlencode($district);
    }
    $cacheKey = 'ecz_' . $citySlug . '_' . preg_replace('/[^a-z0-9]/','',strtolower($district)) . '_' . date('Ymd');
    if (!empty($_SESSION[$cacheKey]) && is_array($_SESSION[$cacheKey])) {
        $pharmacies = $_SESSION[$cacheKey];
    } else {
        $result = collectApiGet($url, $COLLECTAPI_KEY);
        if ($result['ok'] && !empty($result['data']['result'])) {
            $pharmacies = $result['data']['result'];
            $_SESSION[$cacheKey] = $pharmacies;
        } elseif (!$result['ok']) {
            $apiError = $result['error'];
        } else {
            $apiError = 'Bu il/ilçe için nöbetçi eczane kaydı bulunamadı.';
        }
    }
}

include __DIR__ . '/../header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#0d2a1a 0%,#0a1a2e 100%);padding:36px 0;margin-bottom:40px;border-bottom:4px solid #10b981}
.page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff}
.page-hero p{color:rgba(255,255,255,.6);margin-top:6px}
.ecz-search{background:var(--bg-card);border-radius:var(--radius);padding:28px;box-shadow:var(--shadow);margin-bottom:28px}
.ecz-search h2{font-family:var(--font-head);font-size:20px;margin-bottom:16px}
.ecz-form{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.ecz-form select,.ecz-form input{padding:10px 14px;border:2px solid var(--border);border-radius:4px;font-size:14px;font-family:var(--font-body);background:var(--bg);outline:none;transition:border-color .2s;min-width:180px}
.ecz-form select:focus,.ecz-form input:focus{border-color:#10b981}
.ecz-form button{background:#10b981;color:#fff;border:none;padding:11px 26px;border-radius:4px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s;display:flex;align-items:center;gap:6px}
.ecz-form button:hover{background:#059669}
.ecz-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.ecz-card{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);border-left:4px solid #10b981;transition:all .2s}
.ecz-card:hover{box-shadow:var(--shadow-lg);transform:translateY(-2px)}
.ecz-card h3{font-family:var(--font-head);font-size:16px;font-weight:700;color:var(--navy)}
.ec-dist{display:inline-block;background:#d1fae5;color:#065f46;font-size:10px;font-weight:700;padding:2px 8px;border-radius:2px;margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em}
.ec-info{font-size:13px;color:var(--text-mid);margin-top:8px;line-height:1.8}
.ec-phone{display:inline-flex;align-items:center;gap:6px;margin-top:14px;background:#10b981;color:#fff;padding:8px 18px;border-radius:4px;font-weight:700;font-size:13px;text-decoration:none;transition:background .2s}
.ec-phone:hover{background:#059669}
.ec-map{display:inline-flex;align-items:center;gap:5px;margin-top:14px;margin-left:8px;color:#10b981;font-size:13px;font-weight:600;text-decoration:none}
.ec-map:hover{text-decoration:underline}
.ecz-info-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius);padding:20px;margin-bottom:24px}
.ecz-info-box h3{color:#15803d;font-family:var(--font-head);font-size:16px;margin-bottom:8px}
.ecz-count{font-size:13px;color:var(--text-soft);font-weight:600}
.ecz-count strong{color:#10b981;font-size:16px}
.ecz-empty{text-align:center;padding:60px 0}
.alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:16px 20px;color:#991b1b;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.fg-label{display:block;font-size:11px;font-weight:700;color:var(--text-soft);margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em}
@media(max-width:900px){.ecz-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.ecz-grid{grid-template-columns:1fr}.ecz-form{flex-direction:column}}
</style>

<div class="page-hero">
    <div class="container">
        <h1>💊 Nöbetçi Eczaneler</h1>
        <p>Seçtiğiniz ile göre günlük nöbetçi eczane bilgisi — <?= date('d.m.Y') ?></p>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="ecz-info-box">
        <h3>ℹ️ Nöbetçi Eczane Bilgisi</h3>
        <p style="font-size:13px;line-height:1.7;">Nöbetçi eczaneler her gece saat <strong>20:00</strong>'de değişmektedir. Acil durumlarda <strong>ALO 182</strong> numaralı Sağlık hattını arayabilirsiniz.</p>
    </div>

    <div class="ecz-search">
        <h2>İl ve İlçe Seçin</h2>
        <form class="ecz-form" method="get">
            <div>
                <label class="fg-label">İl *</label>
                <select name="city" required>
                    <option value="">— İl Seçin —</option>
                    <?php foreach ($ilMap as $slug => $ad): ?>
                    <option value="<?= h($slug) ?>" <?= $citySlug === $slug ? 'selected' : '' ?>><?= h($ad) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="fg-label">İlçe <span style="font-weight:400;text-transform:none;">(opsiyonel)</span></label>
                <input type="text" name="district" placeholder="Örn: Kadıköy" value="<?= h($district) ?>">
            </div>
            <div>
                <label class="fg-label">&nbsp;</label>
                <button type="submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Eczane Bul
                </button>
            </div>
        </form>
    </div>

    <?php if ($citySlug): ?>

        <?php if ($apiError): ?>
        <div class="alert-err">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= h($apiError) ?>
        </div>
        <?php elseif (!empty($pharmacies)): ?>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
            <h2 style="font-family:var(--font-head);font-size:22px;"><?= h($ilMap[$citySlug]) ?><?= $district ? ' — '.h(mb_convert_case($district,'MB_CASE_TITLE','UTF-8')) : '' ?> Nöbetçi Eczaneleri</h2>
            <span class="ecz-count"><strong><?= count($pharmacies) ?></strong> eczane listelendi</span>
        </div>

        <div class="ecz-grid">
        <?php foreach ($pharmacies as $p):
            $mapUrl = '';
            if (!empty($p['loc'])) {
                $parts = explode(',', $p['loc'], 2);
                if (count($parts) === 2) {
                    $mapUrl = 'https://www.google.com/maps?q=' . rawurlencode(trim($parts[0])) . ',' . rawurlencode(trim($parts[1]));
                }
            }
        ?>
        <div class="ecz-card">
            <?php if (!empty($p['dist'])): ?><span class="ec-dist"><?= h($p['dist']) ?></span><?php endif; ?>
            <h3><?= h($p['name'] ?? 'İsimsiz Eczane') ?></h3>
            <div class="ec-info">
                <?php if (!empty($p['address'])): ?>
                <div>📍 <?= h($p['address']) ?></div>
                <?php endif; ?>
                <div>🗓 <?= date('d.m.Y') ?> — Nöbet bitiş: 20:00</div>
            </div>
            <div style="display:flex;align-items:center;flex-wrap:wrap;">
                <?php if (!empty($p['phone'])): ?>
                <a href="tel:<?= h(preg_replace('/[^0-9+]/','',$p['phone'])) ?>" class="ec-phone">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.6 19.79 19.79 0 0 1 1.6 5.09 2 2 0 0 1 3.57 3h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 10.91A16 16 0 0 0 13 15.82l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21 17z"/></svg>
                    <?= h($p['phone']) ?>
                </a>
                <?php endif; ?>
                <?php if ($mapUrl): ?>
                <a href="<?= h($mapUrl) ?>" target="_blank" rel="noopener" class="ec-map">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    Haritada Gör
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="ecz-empty">
            <div style="font-size:56px;">💊</div>
            <p style="margin-top:16px;font-size:16px;color:var(--text-soft);">Seçilen ilde nöbetçi eczane bulunamadı.</p>
            <p style="margin-top:8px;font-size:14px;color:var(--text-soft);">Acil durumlarda <strong style="color:var(--red);">ALO 182</strong>'yi arayın.</p>
        </div>
        <?php endif; ?>

    <?php else: ?>
    <div class="ecz-empty">
        <div style="font-size:56px;">💊</div>
        <p style="margin-top:16px;font-size:16px;color:var(--text-soft);">Nöbetçi eczane bilgisi için lütfen bir il seçin.</p>
        <p style="margin-top:8px;font-size:14px;color:var(--text-soft);">Acil durumlarda <strong style="color:var(--red);">ALO 182</strong>'yi arayın.</p>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
