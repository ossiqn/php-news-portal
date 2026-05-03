<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Hava Durumu';

if (session_status() === PHP_SESSION_NONE) session_start();

define('CAPI_KEY', 'apikey 4fI4zaHViljrqvNE3n5R1N:4USkBzsplmXU7pF4YOne9d');

$cities = array(
    'ankara'=>'Ankara','istanbul'=>'Istanbul','izmir'=>'Izmir','bursa'=>'Bursa',
    'antalya'=>'Antalya','trabzon'=>'Trabzon','konya'=>'Konya','adana'=>'Adana',
    'gaziantep'=>'Gaziantep','kayseri'=>'Kayseri','samsun'=>'Samsun',
    'erzurum'=>'Erzurum','mersin'=>'Mersin','malatya'=>'Malatya',
    'denizli'=>'Denizli','manisa'=>'Manisa','eskisehir'=>'Eskisehir',
);
$cityLabels = array(
    'ankara'=>'Ankara','istanbul'=>'İstanbul','izmir'=>'İzmir','bursa'=>'Bursa',
    'antalya'=>'Antalya','trabzon'=>'Trabzon','konya'=>'Konya','adana'=>'Adana',
    'gaziantep'=>'Gaziantep','kayseri'=>'Kayseri','samsun'=>'Samsun',
    'erzurum'=>'Erzurum','mersin'=>'Mersin','malatya'=>'Malatya',
    'denizli'=>'Denizli','manisa'=>'Manisa','eskisehir'=>'Eskişehir',
);

$citySlug = (isset($_GET['city']) && isset($cities[$_GET['city']])) ? $_GET['city'] : 'ankara';
$cityApiName = $cities[$citySlug];    // API'ye gönderilecek ad (ASCII)
$cityName    = $cityLabels[$citySlug]; // Görüntülenecek Türkçe ad

function weatherIcon($status) {
    $s = strtolower($status);
    if (strpos($s,'clear')!==false || strpos($s,'sunny')!==false) return '☀️';
    if (strpos($s,'partly')!==false) return '⛅';
    if (strpos($s,'cloudy')!==false || strpos($s,'overcast')!==false) return '☁️';
    if (strpos($s,'fog')!==false) return '🌫️';
    if (strpos($s,'thunder')!==false || strpos($s,'storm')!==false) return '⛈️';
    if (strpos($s,'rain')!==false || strpos($s,'shower')!==false || strpos($s,'drizzle')!==false) return '🌧️';
    if (strpos($s,'snow')!==false || strpos($s,'sleet')!==false) return '❄️';
    if (strpos($s,'wind')!==false) return '💨';
    if (strpos($s,'hot')!==false) return '🌡️';
    return '🌤';
}

$weather  = array();
$apiError = '';
$cacheKey = 'hava_' . $citySlug . '_' . date('Ymd');

if (!empty($_SESSION[$cacheKey]) && is_array($_SESSION[$cacheKey])) {
    $weather = $_SESSION[$cacheKey];
} else {
    $result = collectApiGet(
        'https://api.collectapi.com/weather/getWeather?lang=tr&city=' . rawurlencode($cityApiName),
        CAPI_KEY
    );
    if ($result['ok'] && !empty($result['data']['result'])) {
        $weather = $result['data']['result'];
        $_SESSION[$cacheKey] = $weather;
    } elseif (!$result['ok']) {
        $apiError = $result['error'];
    } else {
        $apiError = 'Hava durumu verisi alınamadı.';
    }
}

$today = !empty($weather[0]) ? $weather[0] : null;
include __DIR__ . '/../header.php';
?>
<style>
.page-hero{background:linear-gradient(135deg,#0d1b2a 0%,#1b3a5c 100%);padding:36px 0;margin-bottom:40px;border-bottom:4px solid #38bdf8}
.page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff}
.page-hero p{color:rgba(255,255,255,.6);margin-top:6px}
.weather-layout{display:grid;grid-template-columns:220px 1fr;gap:24px;padding-bottom:60px}
.w-sidebar{background:var(--bg-card);border-radius:var(--radius);padding:18px;box-shadow:var(--shadow);height:fit-content;position:sticky;top:80px}
.w-sidebar h3{font-family:var(--font-head);font-size:16px;margin-bottom:12px;border-bottom:2px solid #38bdf8;padding-bottom:8px;color:var(--navy)}
.city-list{max-height:480px;overflow-y:auto}
.city-list a{display:block;padding:8px 10px;font-size:13px;font-weight:600;border-radius:3px;margin-bottom:2px;transition:all .15s;color:var(--text)}
.city-list a:hover{background:var(--navy);color:#fff}
.city-list a.active{background:#0ea5e9;color:#fff}
.today-hero{background:linear-gradient(135deg,#0f3460 0%,#0ea5e9 100%);border-radius:var(--radius);padding:32px;color:#fff;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}
.today-temp{font-family:var(--font-head);font-size:80px;font-weight:900;line-height:1;letter-spacing:-.04em}
.today-temp sup{font-size:36px;vertical-align:super;opacity:.7}
.today-desc{font-size:18px;opacity:.9;margin-top:6px;font-weight:600;text-transform:capitalize}
.today-meta{display:flex;gap:20px;margin-top:16px;flex-wrap:wrap}
.today-meta span{font-size:13px;opacity:.75}
.today-icon{font-size:96px;line-height:1}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.stat-pill{background:var(--bg-card);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow);text-align:center;border-top:3px solid #0ea5e9}
.stat-pill .sp-icon{font-size:24px}
.stat-pill .sp-val{font-family:var(--font-head);font-size:22px;font-weight:900;color:var(--navy);margin-top:4px}
.stat-pill .sp-lbl{font-size:11px;color:var(--text-soft);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-top:2px}
.forecast-title{font-family:var(--font-head);font-size:20px;font-weight:900;color:var(--navy);margin-bottom:14px}
.forecast-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px}
.fc-card{background:var(--bg-card);border-radius:var(--radius);padding:16px 12px;box-shadow:var(--shadow);text-align:center;transition:all .2s;border-bottom:3px solid transparent}
.fc-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.12);border-bottom-color:#0ea5e9}
.fc-card.fc-today{background:linear-gradient(135deg,#0f3460,#0ea5e9);color:#fff;border-bottom-color:rgba(255,255,255,.3)}
.fc-day{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;opacity:.7}
.fc-today .fc-day,.fc-today .fc-date{color:#fff;opacity:.85}
.fc-date{font-size:10px;opacity:.5;margin-top:2px}
.fc-icon{font-size:32px;margin:10px 0}
.fc-desc{font-size:11px;font-weight:600;opacity:.65;margin-bottom:8px;text-transform:capitalize}
.fc-temp{font-family:var(--font-head);font-size:24px;font-weight:900;color:var(--navy)}
.fc-today .fc-temp{color:#fff}
.fc-range{font-size:11px;color:var(--text-soft);margin-top:3px;display:flex;justify-content:center;gap:6px}
.fc-today .fc-range{color:rgba(255,255,255,.6)}
.fc-range .fc-max{color:#ef4444;font-weight:700}
.fc-today .fc-range .fc-max{color:#fca5a5}
.fc-range .fc-min{color:#3b82f6;font-weight:700}
.fc-today .fc-range .fc-min{color:#93c5fd}
.fc-humid{font-size:11px;color:#0ea5e9;margin-top:4px;font-weight:600}
.fc-today .fc-humid{color:#bae6fd}
.alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:16px;color:#991b1b;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px}
@media(max-width:900px){.weather-layout{grid-template-columns:1fr}.w-sidebar{position:static}.forecast-grid{grid-template-columns:repeat(3,1fr)}.stats-row{grid-template-columns:1fr 1fr}}
@media(max-width:500px){.forecast-grid{grid-template-columns:repeat(2,1fr)}.today-hero{flex-direction:column}.today-icon{display:none}}
</style>

<div class="page-hero">
    <div class="container">
        <h1>🌤 Hava Durumu</h1>
        <p>Günlük ve 5 günlük hava tahminleri</p>
    </div>
</div>

<div class="container weather-layout">
    <aside class="w-sidebar">
        <h3>🏙 Şehir Seç</h3>
        <div class="city-list">
            <?php foreach ($cityLabels as $slug => $name): ?>
            <a href="hava-durumu.php?city=<?= h($slug) ?>" class="<?= $slug === $citySlug ? 'active' : '' ?>"><?= h($name) ?></a>
            <?php endforeach; ?>
        </div>
    </aside>

    <div class="w-main">
        <?php if ($apiError): ?>
        <div class="alert-err">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= h($apiError) ?>
        </div>
        <?php endif; ?>

        <?php if ($today): ?>
        <div class="today-hero">
            <div>
                <div style="font-size:11px;opacity:.55;text-transform:uppercase;letter-spacing:.12em;font-weight:700;">
                    📍 <?= h($cityName) ?> &nbsp;·&nbsp; <?= h($today['day']) ?>, <?= h($today['date']) ?>
                </div>
                <div class="today-temp"><?= round((float)$today['degree']) ?><sup>°C</sup></div>
                <div class="today-desc"><?= h($today['description']) ?></div>
                <div class="today-meta">
                    <span>🌡 Max <?= round((float)$today['max']) ?>°</span>
                    <span>🌡 Min <?= round((float)$today['min']) ?>°</span>
                    <span>🌙 Gece <?= round((float)$today['night']) ?>°</span>
                    <span>💧 Nem %<?= h($today['humidity']) ?></span>
                </div>
            </div>
            <div class="today-icon"><?= weatherIcon($today['status']) ?></div>
        </div>

        <div class="stats-row">
            <div class="stat-pill">
                <div class="sp-icon">🌡️</div>
                <div class="sp-val"><?= round((float)$today['degree']) ?>°C</div>
                <div class="sp-lbl">Sıcaklık</div>
            </div>
            <div class="stat-pill" style="border-top-color:#22c55e">
                <div class="sp-icon">💧</div>
                <div class="sp-val">%<?= h($today['humidity']) ?></div>
                <div class="sp-lbl">Nem</div>
            </div>
            <div class="stat-pill" style="border-top-color:#ef4444">
                <div class="sp-icon">↑</div>
                <div class="sp-val"><?= round((float)$today['max']) ?>°C</div>
                <div class="sp-lbl">Maksimum</div>
            </div>
            <div class="stat-pill" style="border-top-color:#3b82f6">
                <div class="sp-icon">↓</div>
                <div class="sp-val"><?= round((float)$today['min']) ?>°C</div>
                <div class="sp-lbl">Minimum</div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($weather)): ?>
        <div class="forecast-title">📅 <?= count($weather) ?> Günlük Tahmin</div>
        <div class="forecast-grid">
            <?php foreach (array_slice($weather, 0, 5) as $i => $day): ?>
            <div class="fc-card <?= $i === 0 ? 'fc-today' : '' ?>">
                <div class="fc-day"><?= h($day['day']) ?></div>
                <div class="fc-date"><?= h($day['date']) ?></div>
                <div class="fc-icon"><?= weatherIcon($day['status']) ?></div>
                <div class="fc-desc"><?= h($day['description']) ?></div>
                <div class="fc-temp"><?= round((float)$day['degree']) ?>°</div>
                <div class="fc-range">
                    <span class="fc-max">↑<?= round((float)$day['max']) ?>°</span>
                    <span class="fc-min">↓<?= round((float)$day['min']) ?>°</span>
                </div>
                <div class="fc-humid">💧 %<?= h($day['humidity']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif (!$apiError): ?>
        <div style="text-align:center;padding:60px 0;">
            <div style="font-size:56px;">🌤</div>
            <p style="margin-top:16px;font-size:16px;color:var(--text-soft);">Şehir seçin veya sayfayı yenileyin.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
