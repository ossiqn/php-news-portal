<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;
$pageTitle = 'Reklam';
include __DIR__ . '/../header.php';
?>
<style>
    .page-hero{background:var(--navy);padding:36px 0;margin-bottom:40px;border-bottom:4px solid var(--gold);}
    .page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff;}
    .page-hero p{color:rgba(255,255,255,.6);margin-top:6px;}
    .ad-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin:28px 0;}
    .ad-card{background:var(--bg-card);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);text-align:center;border-top:4px solid var(--red);}
    .ad-card h3{font-family:var(--font-head);font-size:18px;font-weight:700;margin-bottom:8px;}
    .ad-card .ad-price{font-family:var(--font-head);font-size:28px;font-weight:900;color:var(--red);margin:12px 0;}
    .ad-card p{font-size:13px;color:var(--text-mid);line-height:1.6;}
    .ad-spec-table{width:100%;border-collapse:collapse;margin:20px 0;background:var(--bg-card);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
    .ad-spec-table th{background:var(--navy);color:#fff;padding:12px 16px;text-align:left;font-size:13px;}
    .ad-spec-table td{padding:12px 16px;border-bottom:1px solid var(--border);font-size:13px;}
    .ad-spec-table tr:last-child td{border-bottom:none;}
    .ad-spec-table tr:nth-child(even) td{background:var(--bg);}
    @media(max-width:768px){.ad-grid{grid-template-columns:1fr;}}
</style>
<div class="page-hero">
    <div class="container">
        <h1>📢 Reklam</h1>
        <p>Milyonlarca okuyucuya ulaşın</p>
    </div>
</div>
<div class="container" style="padding-bottom:60px;">
    <div style="max-width:860px;margin:0 auto;">
        <h2 style="font-family:var(--font-head);font-size:26px;font-weight:900;margin-bottom:12px;">Neden <?= h(SITE_NAME) ?>?</h2>
        <p style="font-size:16px;color:var(--text-mid);line-height:1.8;margin-bottom:28px;">
            <?= h(SITE_NAME) ?> geniş okuyucu kitlesiyle bölgenin en çok ziyaret edilen haber platformlarından biridir.
            Hedef kitlenize doğrudan ulaşmak için çeşitli reklam seçenekleri sunuyoruz.
        </p>

        <div class="ad-grid">
            <div class="ad-card">
                <div style="font-size:40px;">🖼️</div>
                <h3>Banner Reklam</h3>
                <div class="ad-price">İletişime Geçin</div>
                <p>Ana sayfa, kategori ve haber sayfalarında banner gösterimi. Geniş kitleye markanızı tanıtın.</p>
            </div>
            <div class="ad-card" style="border-top-color:var(--gold);">
                <div style="font-size:40px;">📰</div>
                <h3>Sponsorlu Haber</h3>
                <div class="ad-price" style="color:var(--gold);">İletişime Geçin</div>
                <p>Markanız adına hazırlanmış haber formatında içerik yayını. Güvenilir editöryal ortamda.</p>
            </div>
            <div class="ad-card" style="border-top-color:#059669;">
                <div style="font-size:40px;">📧</div>
                <h3>E-posta Bülteni</h3>
                <div class="ad-price" style="color:#059669;">İletişime Geçin</div>
                <p>Günlük bültenimizde markanıza yer açın. Yüksek açılma oranlarıyla doğrudan ulaşın.</p>
            </div>
        </div>

        <h2 style="font-family:var(--font-head);font-size:22px;font-weight:900;margin:32px 0 16px;">Reklam Boyutları</h2>
        <table class="ad-spec-table">
            <thead><tr><th>Konum</th><th>Boyut</th><th>Format</th><th>Açıklama</th></tr></thead>
            <tbody>
                <tr><td>Ana Sayfa Üst Banner</td><td>970×90 px</td><td>JPG, PNG, GIF</td><td>En yüksek görünürlük</td></tr>
                <tr><td>Haber İçi Reklam</td><td>728×90 px</td><td>JPG, PNG</td><td>Haber akışı içinde gösterim</td></tr>
                <tr><td>Kare Reklam</td><td>300×250 px</td><td>JPG, PNG, GIF</td><td>Tüm sayfalarda yan panel</td></tr>
                <tr><td>Mobil Banner</td><td>320×50 px</td><td>JPG, PNG</td><td>Mobil kullanıcılara özel</td></tr>
                <tr><td>Tam Sayfa</td><td>1200×628 px</td><td>JPG, PNG</td><td>Özel kampanya sayfası</td></tr>
            </tbody>
        </table>

        <div style="background:var(--navy);color:#fff;padding:32px;border-radius:var(--radius);margin-top:32px;text-align:center;">
            <h3 style="font-family:var(--font-head);font-size:24px;font-weight:900;margin-bottom:12px;">Reklam Vermek İstiyor musunuz?</h3>
            <p style="color:rgba(255,255,255,.7);margin-bottom:20px;">Detaylı bilgi ve fiyat teklifi için bizimle iletişime geçin.</p>
            <a href="/iletisim" style="display:inline-block;background:var(--red);color:#fff;padding:12px 32px;border-radius:4px;font-weight:700;font-size:15px;">📬 İletişime Geçin →</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
