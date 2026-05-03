<?php
require_once __DIR__ . '/../functions.php';
$pageTitle = 'Hakkımızda';
include __DIR__ . '/../header.php';
?>
<style>
    .page-hero{background:var(--navy);padding:36px 0;margin-bottom:40px;border-bottom:4px solid var(--gold);}
    .page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff;}
    .page-hero p{color:rgba(255,255,255,.6);margin-top:6px;}
    .prose-wrap{max-width:860px;margin:0 auto;padding-bottom:60px;}
    .prose-wrap h2{font-family:var(--font-head);font-size:26px;font-weight:900;margin:32px 0 12px;color:var(--navy);}
    .prose-wrap h3{font-family:var(--font-head);font-size:20px;margin:24px 0 10px;color:var(--navy);}
    .prose-wrap p{font-size:16px;line-height:1.85;color:var(--text-mid);margin-bottom:16px;}
    .prose-wrap ul{list-style:none;margin:0 0 16px;padding:0;}
    .prose-wrap ul li{padding:8px 0 8px 20px;border-bottom:1px solid var(--border);font-size:15px;position:relative;color:var(--text-mid);}
    .prose-wrap ul li::before{content:'›';color:var(--red);position:absolute;left:0;font-size:16px;font-weight:700;}
    .team-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin:24px 0;}
    .team-card{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);text-align:center;}
    .team-avatar{width:72px;height:72px;border-radius:50%;margin:0 auto 12px;background:var(--red);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:28px;font-weight:900;color:#fff;}
    .team-name{font-family:var(--font-head);font-size:15px;font-weight:700;}
    .team-role{font-size:12px;color:var(--text-soft);margin-top:3px;}
    .value-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin:20px 0;}
    .value-card{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);border-left:4px solid var(--red);}
    .value-card h4{font-family:var(--font-head);font-size:16px;font-weight:700;margin-bottom:6px;}
    .value-card p{font-size:13px;color:var(--text-mid);line-height:1.6;margin:0;}
    @media(max-width:640px){.team-grid{grid-template-columns:1fr 1fr;}.value-grid{grid-template-columns:1fr;}}
</style>
<div class="page-hero">
    <div class="container">
        <h1>Hakkımızda</h1>
        <p><?= h(SITE_NAME) ?> — Güvenilir haberin adresi</p>
    </div>
</div>
<div class="container">
    <div class="prose-wrap">
        <h2><?= h(SITE_NAME) ?> Nedir?</h2>
        <p><?= h(SITE_NAME) ?>, bölgemizin ve Türkiye'nin dört bir yanından güncel, doğru ve tarafsız haberleri okuyucularına ulaştırmak amacıyla kurulmuş dijital bir haber platformudur. Gazetecilik ilkelerine bağlı kalarak kamuoyunu en doğru şekilde bilgilendirmeyi temel misyon olarak benimsedik.</p>
        <p>Yerel haberlerden dünya gündemine, spordan ekonomiye, sağlıktan teknolojiye kadar geniş bir içerik yelpazesiyle her gün yüzlerce haberi takipçilerimizle buluşturuyoruz. Habercilikte şeffaflık ve doğruluk ilkeleri bizim için vazgeçilmezdir.</p>

        <div class="value-grid">
            <div class="value-card"><h4>🎯 Tarafsızlık</h4><p>Her konuyu tüm boyutlarıyla ele alarak okuyucularımızın kendi kanaatlerini oluşturmasına katkı sağlarız.</p></div>
            <div class="value-card"><h4>✅ Doğruluk</h4><p>Yayımlamadan önce haberi birden fazla kaynaktan teyit ederiz. Hata yaptığımızda düzeltme yapmaktan çekinmeyiz.</p></div>
            <div class="value-card"><h4>⚡ Hız</h4><p>Son dakika gelişmeleri anında takipçilerimize ulaşır. 7/24 kesintisiz yayın anlayışıyla çalışıyoruz.</p></div>
            <div class="value-card"><h4>🤝 Sorumluluk</h4><p>Kamuoyunu yanlış yönlendirmek yerine doğru bilgiye erişimi kolaylaştırmayı öncelikli görev sayarız.</p></div>
        </div>

        <h2>Tarihçemiz</h2>
        <p><?= h(SITE_NAME) ?> dijital gazetecilik alanında yolculuğuna yerel habercilikle başladı. Kısa sürede güvenilir içeriği ve dinamik yayın anlayışıyla dikkat çekti. Bugün binlerce okuyucusuna gün içinde onlarca haber ulaştıran köklü bir haber platformu haline geldi.</p>

        <h2>Ekibimiz</h2>
        <div class="team-grid">
            <div class="team-card"><div class="team-avatar">G</div><div class="team-name">Genel Yayın Yönetmeni</div><div class="team-role">Yönetim</div></div>
            <div class="team-card"><div class="team-avatar">H</div><div class="team-name">Haber Editörü</div><div class="team-role">Editöryal</div></div>
            <div class="team-card"><div class="team-avatar">S</div><div class="team-name">Spor Editörü</div><div class="team-role">Spor</div></div>
            <div class="team-card"><div class="team-avatar">E</div><div class="team-name">Ekonomi Muhabiri</div><div class="team-role">Ekonomi</div></div>
            <div class="team-card"><div class="team-avatar">T</div><div class="team-name">Teknoloji Yazarı</div><div class="team-role">Teknoloji</div></div>
            <div class="team-card"><div class="team-avatar">D</div><div class="team-name">Dijital Editör</div><div class="team-role">Dijital</div></div>
        </div>

        <h2>Yayın Politikamız</h2>
        <ul>
            <li>Haberlerde kaynak belirtme zorunluluğu</li>
            <li>Kişisel verilerin korunması ve mahremiyet hakkına saygı</li>
            <li>Nefret söylemi ve ayrımcılık içeren içeriklerden kaçınma</li>
            <li>Reklam ve içerik ayrımının açıkça gösterilmesi</li>
            <li>Okuyucu şikayetlerinin ciddiye alınması ve düzeltmelerin şeffaf biçimde yapılması</li>
        </ul>

        <div style="background:var(--navy);color:#fff;padding:28px;border-radius:var(--radius);margin-top:32px;text-align:center;">
            <h3 style="font-family:var(--font-head);font-size:22px;font-weight:900;margin-bottom:8px;">Bizimle İletişime Geçin</h3>
            <p style="color:rgba(255,255,255,.7);margin-bottom:16px;">Görüş, öneri veya haberleriniz için bizimle iletişime geçebilirsiniz.</p>
            <a href="<?= BASE_PATH ?>/iletisim" style="display:inline-block;background:var(--red);color:#fff;padding:12px 28px;border-radius:4px;font-weight:700;font-size:15px;">İletişim Formu →</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
