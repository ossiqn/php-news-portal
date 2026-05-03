<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Namaz Vakitleri';

$COLLECTAPI_KEY = 'apikey 4fI4zaHViljrqvNE3n5R1N:4USkBzsplmXU7pF4YOne9d';

if (session_status() === PHP_SESSION_NONE) session_start();

$cities = [
    'istanbul'=>'İstanbul','ankara'=>'Ankara','izmir'=>'İzmir','bursa'=>'Bursa',
    'antalya'=>'Antalya','adana'=>'Adana','konya'=>'Konya','trabzon'=>'Trabzon',
    'samsun'=>'Samsun','kayseri'=>'Kayseri','gaziantep'=>'Gaziantep',
    'diyarbakir'=>'Diyarbakır','erzurum'=>'Erzurum','mersin'=>'Mersin',
    'eskisehir'=>'Eskişehir','hatay'=>'Hatay','malatya'=>'Malatya',
    'manisa'=>'Manisa','kahramanmaras'=>'Kahramanmaraş','van'=>'Van',
    'denizli'=>'Denizli','sakarya'=>'Sakarya','tekirdag'=>'Tekirdağ',
    'mugla'=>'Muğla','balikesir'=>'Balıkesir','canakkale'=>'Çanakkale',
    'aydin'=>'Aydın','ordu'=>'Ordu','giresun'=>'Giresun',
];

$cityApiMap = [
    'istanbul'=>'istanbul','ankara'=>'ankara','izmir'=>'izmir','bursa'=>'bursa',
    'antalya'=>'antalya','adana'=>'adana','konya'=>'konya','trabzon'=>'trabzon',
    'samsun'=>'samsun','kayseri'=>'kayseri','gaziantep'=>'gaziantep',
    'diyarbakir'=>'diyarbakir','erzurum'=>'erzurum','mersin'=>'mersin',
    'eskisehir'=>'eskisehir','hatay'=>'hatay','malatya'=>'malatya',
    'manisa'=>'manisa','kahramanmaras'=>'kahramanmaras','van'=>'van',
    'denizli'=>'denizli','sakarya'=>'sakarya','tekirdag'=>'tekirdag',
    'mugla'=>'mugla','balikesir'=>'balikesir','canakkale'=>'canakkale',
    'aydin'=>'aydin','ordu'=>'ordu','giresun'=>'giresun',
];

$citySlug = isset($_GET['city']) && isset($cities[$_GET['city']]) ? $_GET['city'] : 'istanbul';
$cityName = $cities[$citySlug];
$cityApi  = $cityApiMap[$citySlug];

$vakitler = [];
$apiError = '';
$cacheKey = 'namaz_' . $cityApi . '_' . date('Ymd');

if (!empty($_SESSION[$cacheKey]) && is_array($_SESSION[$cacheKey])) {
    $vakitler = $_SESSION[$cacheKey];
} else {
    $result = collectApiGet(
        'https://api.collectapi.com/pray/all?city=' . rawurlencode($cityApi),
        $COLLECTAPI_KEY
    );
    if ($result['ok'] && !empty($result['data']['result'])) {
        foreach ($result['data']['result'] as $v) {
            $vakitler[$v['vakit']] = $v['saat'];
        }
        $_SESSION[$cacheKey] = $vakitler;
    } elseif (!$result['ok']) {
        $apiError = $result['error'];
    } else {
        $apiError = 'Namaz vakitleri alınamadı.';
    }
}

$now = date('H:i');
$activeVakit = '';
$vakitSirasi = ['İmsak','Güneş','Öğle','İkindi','Akşam','Yatsı'];
if (!empty($vakitler)) {
    $activeVakit = 'İmsak';
    foreach ($vakitSirasi as $v) {
        if (!empty($vakitler[$v]) && $now >= $vakitler[$v]) {
            $activeVakit = $v;
        }
    }
}

$nextVakit = '';
$nextTime  = '';
$countdown = '';
if ($activeVakit && !empty($vakitler)) {
    $idx = array_search($activeVakit, $vakitSirasi);
    $nextIdx = ($idx + 1) % count($vakitSirasi);
    $nextVakit = $vakitSirasi[$nextIdx];
    if (!empty($vakitler[$nextVakit])) {
        $nextTime = $vakitler[$nextVakit];
        $nowTs  = strtotime(date('Y-m-d') . ' ' . $now);
        $nextTs = strtotime(date('Y-m-d') . ' ' . $nextTime);
        if ($nextTs <= $nowTs) $nextTs += 86400; // ertesi güne geç
        $diff = $nextTs - $nowTs;
        $h2 = floor($diff / 3600);
        $m2 = floor(($diff % 3600) / 60);
        $countdown = ($h2 > 0 ? $h2 . ' sa ' : '') . $m2 . ' dk';
    }
}

$monthsT = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
$daysT   = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];

$vakitIcons = [
    'İmsak'  => '🌙',
    'Güneş'  => '🌅',
    'Öğle'   => '☀️',
    'İkindi' => '🌤',
    'Akşam'  => '🌆',
    'Yatsı'  => '🌃',
];

include __DIR__ . '/../header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#1a0a2e 0%,#0d2240 100%);padding:36px 0;margin-bottom:40px;border-bottom:4px solid var(--gold)}
.page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff}
.page-hero p{color:rgba(255,255,255,.6);margin-top:6px}
.namaz-layout{display:grid;grid-template-columns:260px 1fr;gap:28px;padding-bottom:60px}
.namaz-sidebar{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);height:fit-content;position:sticky;top:80px}
.namaz-sidebar h3{font-family:var(--font-head);font-size:17px;margin-bottom:14px;border-bottom:2px solid var(--gold);padding-bottom:8px;color:var(--navy)}
.city-list{max-height:420px;overflow-y:auto}
.city-list a{display:block;padding:8px 11px;font-size:13px;font-weight:600;border-radius:3px;margin-bottom:2px;transition:all .15s;color:var(--text)}
.city-list a:hover{background:var(--navy);color:#fff}
.city-list a.active{background:var(--navy);color:#fff}
.today-card{background:var(--navy);color:#fff;border-radius:var(--radius);padding:28px;margin-bottom:24px;position:relative;overflow:hidden}
.today-card::before{content:'';position:absolute;right:-40px;top:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.03)}
.tc-city{font-size:11px;opacity:.55;text-transform:uppercase;letter-spacing:.12em;font-weight:700}
.tc-date{font-family:var(--font-head);font-size:26px;font-weight:900;margin:8px 0}
.tc-next{font-size:13px;opacity:.7;margin-bottom:20px}
.tc-next strong{color:var(--gold-light,#e8ad3f);opacity:1}
.times-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:4px}
.time-pill{background:rgba(255,255,255,.07);border-radius:8px;padding:14px 10px;text-align:center;border:1px solid rgba(255,255,255,.08);transition:all .2s}
.time-pill.active-time{background:var(--gold);color:var(--navy);border-color:var(--gold);box-shadow:0 4px 16px rgba(212,149,42,.4)}
.tp-icon{font-size:20px;line-height:1}
.tp-label{font-size:10px;font-weight:700;opacity:.65;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}
.time-pill.active-time .tp-label{opacity:.8}
.tp-time{font-family:var(--font-head);font-size:21px;font-weight:900;margin-top:3px;letter-spacing:.02em}
.namaz-info{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);margin-top:20px;display:flex;align-items:flex-start;gap:14px}
.namaz-info svg{flex-shrink:0;margin-top:2px;color:var(--gold)}
.namaz-info p{font-size:13px;color:var(--text-mid);line-height:1.7}
.alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:16px 20px;color:#991b1b;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px}
@media(max-width:768px){.namaz-layout{grid-template-columns:1fr}.times-grid{grid-template-columns:repeat(3,1fr)}.namaz-sidebar{position:static}}
@media(max-width:480px){.times-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="page-hero">
    <div class="container">
        <h1>🕌 Namaz Vakitleri</h1>
        <p><?= h($cityName) ?> ve tüm iller için günlük namaz vakitleri</p>
    </div>
</div>

<div class="container namaz-layout">
    <aside class="namaz-sidebar">
        <h3>🏙 İl Seçin</h3>
        <div class="city-list">
            <?php foreach ($cities as $slug => $name): ?>
            <a href="?city=<?= h($slug) ?>" class="<?= $slug === $citySlug ? 'active' : '' ?>"><?= h($name) ?></a>
            <?php endforeach; ?>
        </div>
    </aside>

    <div class="namaz-main">
        <?php if ($apiError): ?>
        <div class="alert-err">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= h($apiError) ?>
        </div>
        <?php endif; ?>

        <div class="today-card">
            <div class="tc-city">🕌 <?= h($cityName) ?> — Namaz Vakitleri</div>
            <div class="tc-date"><?= $daysT[(int)date('w')] ?>, <?= date('d') ?> <?= $monthsT[(int)date('n')] ?> <?= date('Y') ?></div>
            <?php if ($nextVakit && $countdown): ?>
            <div class="tc-next">
                Sonraki vakit: <strong><?= h($nextVakit) ?> — <?= h($nextTime) ?></strong>
                &nbsp;·&nbsp; Kalan: <strong><?= h($countdown) ?></strong>
            </div>
            <?php endif; ?>

            <?php if (!empty($vakitler)): ?>
            <div class="times-grid">
                <?php foreach ($vakitSirasi as $vName):
                    $saat = $vakitler[$vName] ?? '--:--';
                    $isActive = ($vName === $activeVakit);
                ?>
                <div class="time-pill <?= $isActive ? 'active-time' : '' ?>">
                    <div class="tp-icon"><?= $vakitIcons[$vName] ?? '🕐' ?></div>
                    <div class="tp-label"><?= h($vName) ?></div>
                    <div class="tp-time"><?= h($saat) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="opacity:.6;margin-top:16px;">Vakitler yüklenemedi. Lütfen sayfayı yenileyin.</p>
            <?php endif; ?>
        </div>

        <div class="namaz-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>Namaz vakitleri <strong>CollectAPI</strong> üzerinden anlık olarak sunulmaktadır. Vakitler il bazında hesaplanmıştır; ilçelere göre birkaç dakikalık fark olabilir. Kesin vakitler için Diyanet İşleri Başkanlığı'nın resmi sitesini ziyaret ediniz.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
