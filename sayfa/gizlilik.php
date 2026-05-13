<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;
$pageTitle = 'Gizlilik Politikası';
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
        <h1>🔒 Gizlilik Politikası</h1>
        <p>Son güncelleme: <?= date('d.m.Y') ?></p>
    </div>
</div>
<div class="container">
    <div class="legal-layout">
        <aside class="legal-nav">
            <h3>İçindekiler</h3>
            <a href="#toplanan-veriler">Toplanan Veriler</a>
            <a href="#verilerin-kullanimi">Verilerin Kullanımı</a>
            <a href="#cerezler">Çerezler</a>
            <a href="#ucuncu-taraf">Üçüncü Taraf</a>
            <a href="#veri-guvenligi">Veri Güvenliği</a>
            <a href="#haklariniz">Haklarınız</a>
            <a href="#iletisim">İletişim</a>
            <br>
            <a href="/kullanim-sartlari" style="color:var(--red);border-bottom:none;">→ Kullanım Şartları</a>
        </aside>
        <div class="prose">
            <div class="note">📌 Bu gizlilik politikası, <?= h(SITE_NAME) ?> web sitesini kullandığınızda kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklamaktadır.</div>

            <h2 id="toplanan-veriler">1. Toplanan Veriler</h2>
            <h3>Otomatik Toplanan Veriler</h3>
            <p>Sitemizi ziyaret ettiğinizde otomatik olarak aşağıdaki teknik bilgiler toplanabilir:</p>
            <ul>
                <li>IP adresi ve tarayıcı bilgileri</li>
                <li>Ziyaret edilen sayfalar ve ziyaret süreleri</li>
                <li>Erişim tarihi ve saati</li>
                <li>Kullanılan cihaz ve işletim sistemi bilgileri</li>
            </ul>
            <h3>Doğrudan Sağlanan Veriler</h3>
            <p>İletişim formu veya üyelik kaydı aracılığıyla ad, soyad ve e-posta gibi kişisel bilgilerinizi bize iletebilirsiniz. Bu bilgiler yalnızca belirtilen amaçlarla kullanılır.</p>

            <h2 id="verilerin-kullanimi">2. Verilerin Kullanımı</h2>
            <p>Topladığımız veriler şu amaçlarla kullanılır:</p>
            <ul>
                <li>Hizmetlerimizi sağlamak ve geliştirmek</li>
                <li>Kullanıcı deneyimini kişiselleştirmek</li>
                <li>Teknik sorunları tespit etmek ve çözmek</li>
                <li>İstatistiksel analizler yapmak</li>
                <li>Yasal yükümlülükleri yerine getirmek</li>
            </ul>

            <h2 id="cerezler">3. Çerezler (Cookies)</h2>
            <p>Sitemiz, kullanıcı deneyimini iyileştirmek amacıyla çerezler kullanmaktadır. Çerezler, tarayıcınız aracılığıyla cihazınıza yerleştirilen küçük veri dosyalarıdır. Tarayıcı ayarlarınızdan çerezleri devre dışı bırakabilirsiniz; ancak bu durumda bazı site özelliklerini kullanamayabilirsiniz.</p>
            <p>Kullandığımız çerez türleri: Zorunlu çerezler, analiz çerezleri (Google Analytics), tercih çerezleri.</p>

            <h2 id="ucuncu-taraf">4. Üçüncü Taraf Hizmetler</h2>
            <p>Sitemiz, Google Analytics, reklam ağları ve sosyal medya paylaşım butonları gibi üçüncü taraf hizmetler içerebilir. Bu hizmetlerin kendi gizlilik politikaları geçerlidir.</p>

            <h2 id="veri-guvenligi">5. Veri Güvenliği</h2>
            <p>Kişisel verilerinizin güvenliği için gerekli teknik ve idari önlemleri alıyoruz. SSL şifreleme ve güvenli sunucu altyapısı kullanılmaktadır. Ancak internet üzerinden hiçbir veri iletiminin %100 güvenli olmadığını belirtmek gerekir.</p>

            <h2 id="haklariniz">6. KVKK Kapsamındaki Haklarınız</h2>
            <p>6698 sayılı Kişisel Verilerin Korunması Kanunu uyarınca aşağıdaki haklara sahipsiniz:</p>
            <ul>
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme hakkı</li>
                <li>İşlenen kişisel verileriniz hakkında bilgi talep etme hakkı</li>
                <li>Yanlış işlenmiş verilerin düzeltilmesini isteme hakkı</li>
                <li>Kişisel verilerinizin silinmesini veya yok edilmesini isteme hakkı</li>
                <li>İşleme itiraz etme hakkı</li>
            </ul>

            <h2 id="iletisim">7. İletişim</h2>
            <p>Gizlilik politikamızla ilgili sorularınız için <a href="/iletisim" style="color:var(--red);">iletişim sayfamız</a> üzerinden bize ulaşabilirsiniz.</p>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
