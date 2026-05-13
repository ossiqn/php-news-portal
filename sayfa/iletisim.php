<?php
require_once __DIR__ . '/../functions.php';
if (!defined('BASE_PATH')) require_once __DIR__ . '/../config.php';
$_base = BASE_PATH;
$pageTitle = 'İletişim';
$sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)    $errors[] = 'Ad Soyad zorunludur.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli bir e-posta giriniz.';
    if (!$subject) $errors[] = 'Konu zorunludur.';
    if (strlen($message) < 20) $errors[] = 'Mesaj en az 20 karakter olmalıdır.';

    if (empty($errors)) {
        // In production: mail() or SMTP here
        $sent = true;
    }
}

include __DIR__ . '/../header.php';
?>
<style>
    .page-hero{background:var(--navy);padding:36px 0;margin-bottom:40px;border-bottom:4px solid var(--red);}
    .page-hero h1{font-family:var(--font-head);font-size:36px;font-weight:900;color:#fff;}
    .page-hero p{color:rgba(255,255,255,.6);margin-top:6px;}
    .contact-layout{display:grid;grid-template-columns:1fr 340px;gap:28px;padding-bottom:60px;}
    .contact-form-card{background:var(--bg-card);border-radius:var(--radius);padding:32px;box-shadow:var(--shadow);}
    .contact-form-card h2{font-family:var(--font-head);font-size:22px;font-weight:700;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--red);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .fg{margin-bottom:18px;}
    .fg label{display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text-mid);}
    .fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;border:2px solid var(--border);border-radius:4px;font-size:14px;font-family:var(--font-body);outline:none;transition:border-color .2s;background:var(--bg);}
    .fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--red);background:#fff;}
    .fg textarea{resize:vertical;min-height:140px;}
    .btn-send{width:100%;background:var(--red);color:#fff;border:none;padding:13px;border-radius:4px;font-size:15px;font-weight:700;cursor:pointer;font-family:var(--font-body);transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
    .btn-send:hover{background:var(--red-dark);}
    .alert-e{padding:12px 16px;border-radius:4px;font-size:13px;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;margin-bottom:16px;}
    .alert-s{padding:16px;border-radius:4px;font-size:14px;background:#f0fdf4;color:#15803d;border:1px solid #86efac;margin-bottom:16px;text-align:center;}
    .info-cards{display:flex;flex-direction:column;gap:16px;}
    .info-card{background:var(--bg-card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);}
    .info-card h3{font-family:var(--font-head);font-size:16px;font-weight:700;margin-bottom:12px;border-bottom:2px solid var(--red);padding-bottom:8px;}
    .info-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:13px;}
    .info-row .ico{font-size:18px;flex-shrink:0;}
    .map-placeholder{height:180px;background:var(--bg);border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--text-soft);font-size:13px;margin-top:12px;}
    @media(max-width:768px){.contact-layout{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}}
</style>
<div class="page-hero">
    <div class="container">
        <h1>📬 İletişim</h1>
        <p>Görüş, öneri ve haberleriniz için bize ulaşın</p>
    </div>
</div>
<div class="container">
    <div class="contact-layout">
        <div class="contact-form-card">
            <h2>Mesaj Gönderin</h2>
            <?php if ($sent): ?>
            <div class="alert-s">
                <div style="font-size:32px;">✅</div>
                <div style="font-weight:700;margin-top:8px;">Mesajınız başarıyla gönderildi!</div>
                <div style="font-size:13px;margin-top:4px;">En kısa sürede size dönüş yapacağız.</div>
            </div>
            <?php else: ?>
            <?php if (!empty($errors)): ?>
            <div class="alert-e">⚠️ <?= implode(' · ', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>
            <form method="post" novalidate>
                <div class="form-row">
                    <div class="fg"><label>Ad Soyad *</label><input type="text" name="name" value="<?= h($_POST['name'] ?? '') ?>" placeholder="Adınız Soyadınız" required></div>
                    <div class="fg"><label>E-posta *</label><input type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" placeholder="ornek@mail.com" required></div>
                </div>
                <div class="fg">
                    <label>Konu *</label>
                    <select name="subject" required>
                        <option value="">— Konu Seçin —</option>
                        <option <?= ($_POST['subject'] ?? '') === 'haber' ? 'selected' : '' ?>>Haber Gönderme</option>
                        <option <?= ($_POST['subject'] ?? '') === 'reklam' ? 'selected' : '' ?>>Reklam</option>
                        <option <?= ($_POST['subject'] ?? '') === 'duzeltme' ? 'selected' : '' ?>>Düzeltme Talebi</option>
                        <option <?= ($_POST['subject'] ?? '') === 'sikayet' ? 'selected' : '' ?>>Şikayet</option>
                        <option <?= ($_POST['subject'] ?? '') === 'diger' ? 'selected' : '' ?>>Diğer</option>
                    </select>
                </div>
                <div class="fg"><label>Mesajınız *</label><textarea name="message" placeholder="Mesajınızı buraya yazın..." required><?= h($_POST['message'] ?? '') ?></textarea></div>
                <div class="fg" style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" id="kvkk" name="kvkk" required style="width:auto;margin:0;">
                    <label for="kvkk" style="font-size:12px;font-weight:400;color:var(--text-mid);">
                        <a href="/gizlilik" style="color:var(--red);">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                    </label>
                </div>
                <button type="submit" class="btn-send">📨 Mesajı Gönder</button>
            </form>
            <?php endif; ?>
        </div>

        <div class="info-cards">
            <div class="info-card">
                <h3>İletişim Bilgileri</h3>
                <div class="info-row"><span class="ico">📍</span><div><strong>Adres</strong><br>Örnek Mah. Haber Cad. No:1<br>Türkiye</div></div>
                <div class="info-row"><span class="ico">📞</span><div><strong>Telefon</strong><br><a href="tel:+900000000000" style="color:var(--red);">+90 (000) 000 00 00</a></div></div>
                <div class="info-row"><span class="ico">📧</span><div><strong>E-posta</strong><br><a href="mailto:iletisim@habersitesi.com" style="color:var(--red);">iletisim@habersitesi.com</a></div></div>
                <div class="info-row"><span class="ico">🕐</span><div><strong>Çalışma Saatleri</strong><br>7/24 Online<br>Ofis: Pzt–Cum 09:00–18:00</div></div>
                <div class="map-placeholder">🗺️ Harita buraya entegre edilecek<br><small>Google Maps API ile aktif edilir</small></div>
            </div>
            <div class="info-card">
                <h3>Sosyal Medya</h3>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#1877f2;color:#fff;border-radius:4px;font-size:13px;font-weight:700;">📘 Facebook'ta Takip Edin</a>
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#1da1f2;color:#fff;border-radius:4px;font-size:13px;font-weight:700;">🐦 Twitter'da Takip Edin</a>
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);color:#fff;border-radius:4px;font-size:13px;font-weight:700;">📸 Instagram'da Takip Edin</a>
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#ff0000;color:#fff;border-radius:4px;font-size:13px;font-weight:700;">▶ YouTube'da Abone Olun</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../footer.php'; ?>
