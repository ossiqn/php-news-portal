<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Gazete Arşivi';
include __DIR__ . '/../header.php';
?>
<style>
    .page-hero { background: var(--navy); padding: 36px 0; margin-bottom: 40px; border-bottom: 4px solid var(--gold); }
    .page-hero h1 { font-family: var(--font-head); font-size: 36px; font-weight: 900; color: #fff; }
    .page-hero p { color: rgba(255,255,255,.6); margin-top: 6px; }
    .gazete-filter { background: var(--bg-card); padding: 20px 24px; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 28px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
    .gazete-filter label { font-weight: 700; font-size: 14px; }
    .gazete-filter input[type=date] { padding: 8px 12px; border: 2px solid var(--border); border-radius: 4px; font-size: 14px; outline: none; }
    .gazete-filter input[type=date]:focus { border-color: var(--red); }
    .gazete-filter button { background: var(--red); color: #fff; border: none; padding: 9px 20px; border-radius: 4px; font-weight: 700; cursor: pointer; transition: background .2s; }
    .gazete-filter button:hover { background: var(--red-dark); }
    .gazete-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .gazete-card { background: var(--bg-card); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); transition: all .2s; cursor: pointer; }
    .gazete-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-3px); }
    .gazete-card-img { height: 240px; overflow: hidden; background: #e5e7eb; display: flex; align-items: center; justify-content: center; }
    .gazete-card-img img { object-fit: contain; transition: transform .3s; }
    .gazete-card:hover .gazete-card-img img { transform: scale(1.04); }
    .gazete-card-body { padding: 14px; border-top: 3px solid var(--border); }
    .gazete-card-body h3 { font-family: var(--font-head); font-size: 14px; font-weight: 700; }
    .gazete-card-body .gm { font-size: 11px; color: var(--text-soft); margin-top: 4px; }
    .gazete-card-body .gread { display: inline-block; margin-top: 8px; font-size: 12px; font-weight: 700; color: var(--red); }
    @media (max-width: 1024px) { .gazete-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px) { .gazete-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="page-hero">
    <div class="container">
        <h1>📰 Gazete Arşivi</h1>
        <p>Türkiye'nin önde gelen gazetelerinin manşetleri ve arşivleri</p>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="gazete-filter">
        <label>Tarih Seçin:</label>
        <input type="date" id="archDate" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
        <button onclick="loadDate()">📰 Göster</button>
        <span style="font-size:13px;color:var(--text-soft);">Seçili tarih: <span id="selDate"><?= date('d.m.Y') ?></span></span>
    </div>

    <h2 style="font-family:var(--font-head);font-size:22px;margin-bottom:20px;" id="gazeteTarih">Bugünkü Gazeteler — <?= date('d.m.Y') ?></h2>

    <div class="gazete-grid">
        <?php
        $gazeteler = [
            ['name'=>'Hürriyet',     'color'=>'#c8102e', 'url'=>'https://www.hurriyet.com.tr'],
            ['name'=>'Sabah',        'color'=>'#1d4ed8', 'url'=>'https://www.sabah.com.tr'],
            ['name'=>'Milliyet',     'color'=>'#dc2626', 'url'=>'https://www.milliyet.com.tr'],
            ['name'=>'Sözcü',        'color'=>'#7c3aed', 'url'=>'https://www.sozcu.com.tr'],
            ['name'=>'Cumhuriyet',   'color'=>'#059669', 'url'=>'https://www.cumhuriyet.com.tr'],
            ['name'=>'Posta',        'color'=>'#d97706', 'url'=>'https://www.posta.com.tr'],
            ['name'=>'Haber Türk',   'color'=>'#0891b2', 'url'=>'https://www.haberturk.com'],
            ['name'=>'Star',         'color'=>'#ea580c', 'url'=>'https://www.star.com.tr'],
        ];
        foreach ($gazeteler as $g):
        ?>
        <div class="gazete-card" onclick="window.open('<?= h($g['url']) ?>','_blank')">
            <div class="gazete-card-img" style="background:<?= h($g['color']) ?>15;">
                <div style="text-align:center;padding:20px;">
                    <div style="font-family:var(--font-head);font-size:28px;font-weight:900;color:<?= h($g['color']) ?>;"><?= h($g['name']) ?></div>
                    <div style="margin-top:12px;font-size:13px;color:var(--text-mid);"><?= date('d.m.Y') ?></div>
                    <div style="margin-top:24px;font-size:48px;">📰</div>
                    <div style="margin-top:12px;font-size:11px;color:var(--text-soft);">Gazete görüntüsü için<br>web sitesini ziyaret edin</div>
                </div>
            </div>
            <div class="gazete-card-body">
                <h3 style="color:<?= h($g['color']) ?>;"><?= h($g['name']) ?></h3>
                <div class="gm"><?= date('d.m.Y') ?> Sayısı</div>
                <a href="<?= h($g['url']) ?>" target="_blank" class="gread">Gazeteyi Oku →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:32px;background:var(--bg-card);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);">
        <h3 style="font-family:var(--font-head);font-size:18px;margin-bottom:12px;">📌 Diğer Yayınlar</h3>
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            <?php
            $digerleri = ['Dünya','BirGün','Yeniçağ','Türkiye','Aydınlık','Güneş','Yeni Şafak','A Haber','CNN Türk','NTV'];
            foreach ($digerleri as $g): ?>
            <span style="padding:7px 14px;background:var(--bg);border-radius:3px;font-size:13px;font-weight:600;border:1px solid var(--border);"><?= h($g) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function loadDate() {
    var d = document.getElementById('archDate').value;
    if (d) {
        var parts = d.split('-');
        document.getElementById('selDate').textContent = parts[2] + '.' + parts[1] + '.' + parts[0];
        document.getElementById('gazeteTarih').textContent = parts[2] + '.' + parts[1] + '.' + parts[0] + ' Tarihi Gazete Arşivi';
    }
}
</script>

<?php include __DIR__ . '/../footer.php'; ?>
