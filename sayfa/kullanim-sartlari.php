<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Kullanım Şartları';
include __DIR__ . '/../header.php';
?>
<style>
    .page-hero{background:var(--navy);padding:36px 0;margin-bottom:40px;border-bottom:4px solid var(--gold);}
    .page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff;}
    .page-hero p{color:rgba(255,255,255,.6);margin-top:6px;}
    .legal-layout{display:grid;grid-template-columns:240px 1fr;gap:28px;padding-bottom:60px;}
    .legal-nav{background:var(--bg-card);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow);height:fit-content;position:sticky;top:70px;}
    .legal-nav h3{font-family:var(--font-head);font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text-soft);text-transform:uppercase;letter-spacing:.06em;}
    .legal-nav a{display:block;padding:8px 10px;font-size:13px;font-weight:600;color:var(--text);border-radius:3px;transition:all .15s;border-bottom:1px solid var(--border);}
    .legal-nav a:hover{color:var(--red);background:var(--bg);}
    .legal-nav a:last-child{border-bottom:none;}
    .prose{font-size:15px;line-height:1.85;color:var(--text-mid);}
    .prose h2{font-family:var(--font-head);font-size:24px;font-weight:900;color:var(--navy);margin:32px 0 12px;padding-bottom:8px;border-bottom:2px solid var(--border);}
    .prose h3{font-family:var(--font-head);font-size:18px;font-weight:700;color:var(--navy);margin:20px 0 8px;}
    .prose p{margin-bottom:14px;}
    .prose ul{list-style:disc;padding-left:24px;margin-bottom:14px;}
    .prose ul li{margin-bottom:6px;}
    .prose .note{background:#fef9c3;border-left:4px solid #fde047;padding:14px 16px;border-radius:4px;font-size:14px;color:#713f12;margin:16px 0;}
    @media(max-width:768px){.legal-layout{grid-template-columns:1fr;}.legal-nav{position:static;}}
</style>
<div class="page-hero">
    <div class="container">
        <h1>📜 Kullanım Şartları</h1>
        <p>Son güncelleme: <?= date('d.m.Y') ?></p>
    </div>
</div>
<div class="container">
    <div class="legal-layout">
        <aside class="legal-nav">
            <h3>İçindekiler</h3>
            <a href="#kabul">Şartların Kabulü</a>
            <a href="#hizmet">Hizmet Tanımı</a>
            <a href="#kullanim-kurallari">Kullanım Kuralları</a>
            <a href="#fikri-mulkiyet">Fikri Mülkiyet</a>
            <a href="#sorumluluk">Sorumluluk Sınırı</a>
            <a href="#degisiklikler">Değişiklikler</a>
            <a href="#hukuk">Uygulanacak Hukuk</a>
            <br>
            <a href="<?= BASE_PATH ?>/gizlilik" style="color:var(--red);border-bottom:none;">→ Gizlilik Politikası</a>
        </aside>
        <div class="prose">
            <div class="note">⚠️ Sitemizi kullanmaya devam etmekle aşağıdaki kullanım şartlarını kabul etmiş sayılırsınız. Lütfen dikkatlice okuyunuz.</div>

            <h2 id="kabul">1. Şartların Kabulü</h2>
            <p><?= h(SITE_NAME) ?> web sitesini kullanarak bu kullanım şartlarını, gizlilik politikamızı ve yürürlükteki tüm yasal düzenlemeleri kabul etmiş sayılırsınız. Bu şartları kabul etmiyorsanız lütfen sitemizi kullanmayınız.</p>

            <h2 id="hizmet">2. Hizmet Tanımı</h2>
            <p><?= h(SITE_NAME) ?>, Türkiye ve dünyadan güncel haberleri, analizleri, köşe yazılarını ve içerikleri kullanıcılarıyla buluşturan dijital bir haber platformudur. Hizmetlerimizi önceden bildirmeksizin değiştirme, askıya alma veya sonlandırma hakkını saklı tutarız.</p>

            <h2 id="kullanim-kurallari">3. Kullanım Kuralları</h2>
            <p>Sitemizi kullanırken aşağıdaki kurallara uymakla yükümlüsünüz:</p>
            <ul>
                <li>Yasa dışı, zararlı, tehdit edici veya hakaret içeren içerik paylaşmamak</li>
                <li>Başkalarının kişisel bilgilerini izinsiz kullanmamak</li>
                <li>Site altyapısını bozmaya veya aşırı yüklemeye çalışmamak</li>
                <li>Otomatik araçlar (bot, scraper) ile içerik toplamaya çalışmamak</li>
                <li>Telif hakkı ihlali oluşturacak içerikleri yaymamak</li>
                <li>Nefret söylemi, ayrımcılık ve şiddeti teşvik edici içerik paylaşmamak</li>
            </ul>

            <h2 id="fikri-mulkiyet">4. Fikri Mülkiyet</h2>
            <p>Sitemizdeki tüm metin, görsel, video, grafik ve diğer içerikler <?= h(SITE_NAME) ?>'ne veya içerik sağlayıcılarına aittir ve telif hukuku kapsamında korunmaktadır.</p>
            <p>İçeriklerimizi yalnızca kişisel, ticari olmayan amaçlarla kullanabilirsiniz. Kaynak göstermek koşuluyla kısa alıntı yapılabilir. Ticari kullanım veya içeriklerin toplu kopyalanması yasaktır.</p>

            <h2 id="sorumluluk">5. Sorumluluk Sınırlaması</h2>
            <p><?= h(SITE_NAME) ?>, sitede yayımlanan bilgilerin doğruluğu ve güncelliği için azami özeni göstermekle birlikte, bilgilerin doğruluğunu garanti etmez. Sitemizde yer alan haberler bilgilendirme amacıyla sunulmaktadır.</p>
            <p>Üçüncü taraf sitelerine yönlendiren bağlantılar için sorumluluk kabul etmiyoruz. Bağlantıların güvenliğinden ve içeriğinden ilgili üçüncü taraf sorumludur.</p>

            <h2 id="degisiklikler">6. Değişiklikler</h2>
            <p>Bu kullanım şartları önceden haber verilmeksizin güncellenebilir. Değişiklikler sitemizde yayımlandığı tarihten itibaren geçerli olur. Siteyi kullanmaya devam etmeniz değişiklikleri kabul ettiğiniz anlamına gelir.</p>

            <h2 id="hukuk">7. Uygulanacak Hukuk</h2>
            <p>Bu kullanım şartları Türkiye Cumhuriyeti hukukuna tabidir. Herhangi bir uyuşmazlık durumunda Türk mahkemelerinin yargı yetkisi geçerli olacaktır.</p>

            <div style="background:var(--navy);color:#fff;padding:20px;border-radius:var(--radius);margin-top:32px;">
                <p style="margin:0;font-size:14px;">Kullanım şartlarımız veya gizlilik politikamız hakkında sorularınız için <a href="<?= BASE_PATH ?>/iletisim" style="color:var(--gold);">iletişim sayfamızdan</a> bize ulaşabilirsiniz.</p>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
