<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;
$pageTitle = 'Gazete Manşetleri';
include __DIR__ . '/../header.php';
$monthsT = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
?>
<style>
    .page-hero { background: var(--navy); padding: 36px 0; margin-bottom: 40px; border-bottom: 4px solid var(--gold); }
    .page-hero h1 { font-family: var(--font-head); font-size: 36px; font-weight: 900; color: #fff; }
    .page-hero p { color: rgba(255,255,255,.6); margin-top: 6px; }
    .mansest-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .mansest-card { background: var(--bg-card); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
    .mansest-header { padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid var(--col); }
    .mansest-name { font-family: var(--font-head); font-size: 18px; font-weight: 900; color: var(--col); }
    .mansest-date { font-size: 11px; color: var(--text-soft); }
    .mansest-body { padding: 16px; }
    .mansest-main-head { font-family: var(--font-head); font-size: 16px; font-weight: 700; line-height: 1.3; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
    .mansest-item { padding: 7px 0; border-bottom: 1px solid var(--border); font-size: 13px; font-weight: 600; color: var(--text-mid); display: flex; gap: 6px; align-items: baseline; }
    .mansest-item::before { content: '›'; color: var(--col); font-size: 14px; flex-shrink: 0; }
    .mansest-item:last-child { border-bottom: none; }
    .mansest-footer { padding: 12px 16px; background: var(--bg); display: flex; justify-content: space-between; align-items: center; }
    .mansest-footer a { font-size: 12px; font-weight: 700; color: var(--red); }
    @media (max-width: 1024px) { .mansest-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 640px) { .mansest-grid { grid-template-columns: 1fr; } }
</style>

<div class="page-hero">
    <div class="container">
        <h1>🗞 Gazete Manşetleri</h1>
        <p><?= date('d') . ' ' . $monthsT[(int)date('n')] . ' ' . date('Y') ?> — Günün gazete manşetleri</p>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="mansest-grid">
        <?php
        $gazeteler = [
            ['name'=>'Hürriyet',    'color'=>'#c8102e', 'url'=>'https://www.hurriyet.com.tr',
             'main'=>'Kabinede Kritik Toplantı: Ekonomi Masaya Yatırılıyor',
             'others'=>['Borsa rekor kırmaya devam ediyor','Milli takımda büyük değişim','Sağlık reformunda yeni adım']],
            ['name'=>'Sabah',       'color'=>'#1d4ed8', 'url'=>'https://www.sabah.com.tr',
             'main'=>'Cumhurbaşkanı Önemli Zirve İçin Ankara\'da',
             'others'=>['Enflasyonda düşüş sinyali','Yeni yatırım paketi açıklandı','Eğitimde devrim niteliğinde karar']],
            ['name'=>'Milliyet',    'color'=>'#dc2626', 'url'=>'https://www.milliyet.com.tr',
             'main'=>'Süper Lig\'de Şampiyonluk Son Haftaya Kaldı',
             'others'=>['Ekonomide iyi haberler geliyor','Deprem bölgesinde yeniden yapılanma','Teknoloji zirvesinde Türk imzası']],
            ['name'=>'Sözcü',       'color'=>'#7c3aed', 'url'=>'https://www.sozcu.com.tr',
             'main'=>'Muhalefetten Hükümete Sert Eleştiri',
             'others'=>['Emekli maaşlarına zam tartışması','Kira artışına önlem geliyor','İklim değişikliğinde alarm']],
            ['name'=>'Cumhuriyet',  'color'=>'#059669', 'url'=>'https://www.cumhuriyet.com.tr',
             'main'=>'Seçim Sonrası Ekonomik Değerlendirme',
             'others'=>['İşsizlik rakamları açıklandı','Basın özgürlüğünde endişeler','Eğitim sisteminde kriz']],
            ['name'=>'Haber Türk',  'color'=>'#0891b2', 'url'=>'https://www.haberturk.com',
             'main'=>'Döviz Kurunda Yeni Denge Arayışı',
             'others'=>['Faiz kararı açıklanıyor','Altın fiyatları rekor kırdı','Enerji sektöründe büyük hamle']],
        ];

        foreach ($gazeteler as $g): ?>
        <div class="mansest-card" style="--col:<?= h($g['color']) ?>;">
            <div class="mansest-header">
                <div class="mansest-name"><?= h($g['name']) ?></div>
                <div class="mansest-date"><?= date('d.m.Y') ?></div>
            </div>
            <div class="mansest-body">
                <div class="mansest-main-head"><?= h($g['main']) ?></div>
                <?php foreach ($g['others'] as $o): ?>
                <div class="mansest-item"><?= h($o) ?></div>
                <?php endforeach; ?>
            </div>
            <div class="mansest-footer">
                <span style="font-size:12px;color:var(--text-soft);">Tüm manşetler için</span>
                <a href="<?= h($g['url']) ?>" target="_blank">Gazeteyi Aç →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:28px;background:var(--bg-card);padding:20px;border-radius:var(--radius);box-shadow:var(--shadow);">
        <p style="font-size:13px;color:var(--text-soft);">
            ⚠️ <strong>Not:</strong> Yukarıdaki manşetler örnek içerik olup günlük olarak admin panelinden güncellenebilir.
            Gerçek manşetler için gazete API'si veya RSS feed entegrasyonu yapılabilir.
        </p>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
