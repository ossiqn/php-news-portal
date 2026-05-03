<?php
require_once __DIR__ . '/../functions.php';
session_start();

$pageTitle = 'Üye Paneli';
$error = $success = '';
$activeTab = $_GET['tab'] ?? 'genel';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $user  = userLogin($email, $pass);
        if ($user && !empty($user['__pending__'])) {
            $error = 'Üyeliğiniz onay bekliyor. Admin tarafından onaylandığında giriş yapabilirsiniz.';
        } elseif ($user) {
            $_SESSION['uye'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email']];
            header('Location: ' . BASE_PATH . '/uye-paneli?tab=genel');
            exit;
        } else {
            $error = 'E-posta veya şifre hatalı.';
        }

    } elseif ($action === 'register') {
        $name  = trim($_POST['reg_name'] ?? '');
        $email = trim($_POST['reg_email'] ?? '');
        $pass  = $_POST['reg_password'] ?? '';
        $pass2 = $_POST['reg_password2'] ?? '';
        if (!$name || !$email || !$pass) { $error = 'Tüm alanları doldurun.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Geçerli bir e-posta girin.'; }
        elseif (strlen($pass) < 6) { $error = 'Şifre en az 6 karakter olmalı.'; }
        elseif ($pass !== $pass2) { $error = 'Şifreler eşleşmiyor.'; }
        else {
            if (userRegister($name, $email, $pass)) {
                $success = 'Kayıt başarılı! Hesabınız onaylandıktan sonra giriş yapabilirsiniz.';
            } else {
                $error = 'Bu e-posta zaten kayıtlı.';
            }
        }

    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: ' . BASE_PATH . '/uye-paneli');
        exit;

    } elseif ($action === 'update_profile' && !empty($_SESSION['uye'])) {
        $uid  = (int)$_SESSION['uye']['id'];
        $name = trim($_POST['name'] ?? '');
        $email= trim($_POST['email'] ?? '');
        if ($name && $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            updateUserProfile($uid, $name, $email);
            $_SESSION['uye']['name']  = $name;
            $_SESSION['uye']['email'] = $email;
            $success = 'Profil güncellendi.';
        } else { $error = 'Geçerli ad ve e-posta girin.'; }
        $activeTab = 'ayarlar';

    } elseif ($action === 'update_password' && !empty($_SESSION['uye'])) {
        $uid  = (int)$_SESSION['uye']['id'];
        $pass = $_POST['new_password'] ?? '';
        $pass2= $_POST['new_password2'] ?? '';
        if (strlen($pass) < 6) { $error = 'Şifre en az 6 karakter olmalı.'; }
        elseif ($pass !== $pass2) { $error = 'Şifreler eşleşmiyor.'; }
        else { updateUserPassword($uid, $pass); $success = 'Şifre güncellendi.'; }
        $activeTab = 'ayarlar';

    } elseif ($action === 'update_prefs' && !empty($_SESSION['uye'])) {
        $uid   = (int)$_SESSION['uye']['id'];
        $nl    = (int)($_POST['newsletter'] ?? 0);
        $cats  = implode(',', array_map('intval', $_POST['categories'] ?? []));
        updateUserPrefs($uid, $nl, $cats);
        $success = 'Tercihler kaydedildi.';
        $activeTab = 'tercihler';
    }
}

$loggedIn = !empty($_SESSION['uye']);
$user     = null;
$stats    = ['favorites'=>0,'saved'=>0,'notifications'=>0];
$favorites = $saved = $notifications = [];
$allCats  = getAllCategories();

if ($loggedIn) {
    $user          = getUserById((int)$_SESSION['uye']['id']);
    $stats         = getUserStats((int)$user['id']);
    $favorites     = getUserFavorites((int)$user['id']);
    $saved         = getUserSaved((int)$user['id']);
    $notifications = getUserNotifications((int)$user['id']);
    if ($activeTab === 'bildirimler') {
        markNotificationsRead((int)$user['id']);
    }
    $prefCatIds = array_filter(explode(',', $user['pref_categories'] ?? ''));
}

include __DIR__ . '/../header.php';
?>
<style>
.ph{background:var(--navy);padding:32px 0;margin-bottom:0;border-bottom:4px solid var(--gold)}
.ph h1{font-family:var(--font-head);font-size:32px;font-weight:900;color:#fff}
.ph p{color:rgba(255,255,255,.55);margin-top:5px;font-size:13px}
.panel-wrap{display:grid;grid-template-columns:260px 1fr;gap:24px;padding:28px 0 60px;align-items:start}
.panel-side{background:var(--bg-card);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);position:sticky;top:70px}
.user-card{padding:24px;text-align:center;background:linear-gradient(135deg,var(--navy),var(--navy-light));border-bottom:3px solid var(--red)}
.user-av{width:68px;height:68px;border-radius:50%;background:var(--red);display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:28px;font-weight:900;color:#fff;margin:0 auto 10px}
.user-nm{font-family:var(--font-head);font-size:16px;font-weight:700;color:#fff}
.user-em{font-size:11px;color:rgba(255,255,255,.5);margin-top:2px}
.user-since{font-size:10px;color:rgba(255,255,255,.35);margin-top:4px}
.panel-nav{padding:8px 0}
.pn-item{display:flex;align-items:center;gap:10px;padding:11px 18px;font-size:13px;font-weight:600;color:var(--text-mid);transition:all .15s;cursor:pointer;border-left:3px solid transparent;text-decoration:none}
.pn-item svg{width:16px;height:16px;flex-shrink:0;opacity:.6}
.pn-item:hover{color:var(--text);background:var(--bg)}
.pn-item.active{color:var(--red);border-left-color:var(--red);background:rgba(200,16,46,.05);font-weight:700}
.pn-item.active svg{opacity:1;color:var(--red)}
.pn-badge{margin-left:auto;background:var(--red);color:#fff;font-size:10px;font-weight:800;padding:1px 7px;border-radius:10px;min-width:20px;text-align:center}
.pn-logout{padding:12px 18px;border-top:1px solid var(--border)}
.panel-main{}
.tab-content{display:none}
.tab-content.active{display:block}
.pc{background:var(--bg-card);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);margin-bottom:20px}
.pc h2{font-family:var(--font-head);font-size:18px;font-weight:900;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid var(--border);color:var(--navy)}
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.st-card{background:var(--bg);border-radius:var(--radius);padding:16px;text-align:center;border-top:3px solid var(--c,var(--red))}
.st-num{font-family:var(--font-head);font-size:28px;font-weight:900;color:var(--navy)}
.st-lbl{font-size:11px;color:var(--text-soft);margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.04em}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:12px;font-weight:700;margin-bottom:6px;color:var(--text-mid);text-transform:uppercase;letter-spacing:.04em}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;border:2px solid var(--border);border-radius:4px;font-size:14px;font-family:var(--font-body);outline:none;transition:border-color .2s;background:var(--bg)}
.fg input:focus,.fg select:focus{border-color:var(--red);background:#fff}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.btn{padding:11px 22px;border-radius:4px;font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font-body);transition:all .2s;border:none}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:var(--red-dark)}
.btn-navy{background:var(--navy);color:#fff}
.btn-navy:hover{background:var(--navy-light)}
.btn-outline{background:none;border:2px solid var(--border);color:var(--text)}
.btn-outline:hover{border-color:var(--red);color:var(--red)}
.alert-e{padding:12px 16px;border-radius:4px;font-size:13px;font-weight:600;margin-bottom:16px;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5}
.alert-s{padding:12px 16px;border-radius:4px;font-size:13px;font-weight:600;margin-bottom:16px;background:#f0fdf4;color:#15803d;border:1px solid #86efac}
.news-list-item{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);align-items:flex-start}
.news-list-item:last-child{border-bottom:none}
.nl-img{width:100px;height:68px;border-radius:var(--radius);overflow:hidden;flex-shrink:0}
.nl-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.news-list-item:hover .nl-img img{transform:scale(1.06)}
.nl-body h4{font-family:var(--font-head);font-size:15px;font-weight:700;line-height:1.3;margin-top:5px}
.nl-body h4 a:hover{color:var(--red)}
.nl-meta{font-size:11px;color:var(--text-soft);margin-top:5px;display:flex;gap:10px}
.nl-act{margin-left:auto;flex-shrink:0}
.nl-act button{background:none;border:1.5px solid var(--border);padding:5px 10px;border-radius:3px;font-size:11px;font-weight:700;cursor:pointer;color:var(--text-soft);transition:all .2s;font-family:var(--font-body)}
.nl-act button:hover{border-color:var(--red);color:var(--red)}
.notif-item{display:flex;gap:12px;align-items:flex-start;padding:13px 0;border-bottom:1px solid var(--border)}
.notif-item:last-child{border-bottom:none}
.notif-item.unread{background:rgba(200,16,46,.03)}
.notif-dot{width:8px;height:8px;border-radius:50%;background:var(--red);flex-shrink:0;margin-top:5px}
.notif-dot.read{background:var(--border)}
.notif-msg{font-size:13px;line-height:1.5;color:var(--text-mid)}
.notif-time{font-size:11px;color:var(--text-soft);margin-top:3px}
.cat-pref-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px}
.cat-pref-item{display:flex;align-items:center;gap:8px;padding:10px 12px;background:var(--bg);border-radius:4px;border:2px solid var(--border);cursor:pointer;transition:all .15s;font-size:13px;font-weight:600}
.cat-pref-item input[type=checkbox]{display:none}
.cat-pref-item.checked{border-color:var(--c,var(--red));background:rgba(200,16,46,.06);color:var(--c,var(--red))}
.cat-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.empty-state{text-align:center;padding:48px 20px}
.empty-state svg{width:56px;height:56px;color:var(--border);margin:0 auto 14px}
.empty-state h3{font-family:var(--font-head);font-size:18px;color:var(--text-soft)}
.empty-state p{font-size:13px;color:var(--text-soft);margin-top:6px}
.tab-btns{display:flex;gap:0;background:var(--bg);border-radius:4px;overflow:hidden;border:1px solid var(--border);margin-bottom:0}
.tab-btn{flex:1;padding:10px 8px;text-align:center;font-size:12px;font-weight:700;cursor:pointer;border:none;background:none;font-family:var(--font-body);transition:all .2s;color:var(--text-mid);border-bottom:3px solid transparent}
.tab-btn.active{background:var(--bg-card);color:var(--red);border-bottom-color:var(--red)}
.login-wrap{max-width:420px;margin:0 auto;padding:40px 0 60px}
@media(max-width:768px){
    .panel-wrap{grid-template-columns:1fr}
    .panel-side{position:static}
    .stats-row{grid-template-columns:1fr 1fr 1fr}
    .fg-row{grid-template-columns:1fr}
    .cat-pref-grid{grid-template-columns:1fr 1fr}
    .panel-nav{display:flex;overflow-x:auto;padding:0;border-bottom:1px solid var(--border)}
    .pn-item{border-left:none;border-bottom:3px solid transparent;white-space:nowrap;padding:12px 14px}
    .pn-item.active{border-bottom-color:var(--red);background:none}
}
</style>

<div class="ph">
    <div class="container">
        <h1><?= $loggedIn ? 'Hoş geldin, '.h($user['name'] ?? '') : 'Üye Paneli' ?></h1>
        <p><?= $loggedIn ? 'Kişisel panelinizi yönetin' : 'Giriş yapın veya üye olun' ?></p>
    </div>
</div>

<?php if (!$loggedIn): ?>
<div class="container login-wrap">
    <?php if ($error): ?><div class="alert-e"><?= h($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert-s"><?= h($success) ?></div><?php endif; ?>

    <div class="tab-btns" style="margin-bottom:24px;">
        <button class="tab-btn active" onclick="showLoginTab('giris',this)">Giriş Yap</button>
        <button class="tab-btn" onclick="showLoginTab('kayit',this)">Üye Ol</button>
    </div>

    <div id="lt-giris" class="pc" style="padding:28px;">
        <h2 style="border-bottom-color:var(--red);">Giriş Yap</h2>
        <form method="post">
            <input type="hidden" name="action" value="login">
            <div class="fg"><label>E-posta</label><input type="email" name="email" placeholder="ornek@mail.com" required autofocus></div>
            <div class="fg"><label>Şifre</label><input type="password" name="password" placeholder="••••••••" required></div>
            <div style="display:flex;justify-content:flex-end;margin-bottom:16px;"><a href="#" style="font-size:13px;color:var(--red);">Şifremi Unuttum?</a></div>
            <button type="submit" class="btn btn-red" style="width:100%;">Giriş Yap</button>
        </form>
        <p style="text-align:center;margin-top:14px;font-size:12px;color:var(--text-soft);">Test: test@test.com / password</p>
    </div>

    <div id="lt-kayit" class="pc" style="display:none;padding:28px;">
        <h2 style="border-bottom-color:var(--red);">Üye Ol</h2>
        <form method="post">
            <input type="hidden" name="action" value="register">
            <div class="fg"><label>Ad Soyad</label><input type="text" name="reg_name" placeholder="Adınız Soyadınız" required></div>
            <div class="fg"><label>E-posta</label><input type="email" name="reg_email" placeholder="ornek@mail.com" required></div>
            <div class="fg-row">
                <div class="fg"><label>Şifre</label><input type="password" name="reg_password" placeholder="Min. 6 karakter" required></div>
                <div class="fg"><label>Şifre Tekrar</label><input type="password" name="reg_password2" placeholder="Tekrar girin" required></div>
            </div>
            <button type="submit" class="btn btn-navy" style="width:100%;">Üye Ol</button>
        </form>
    </div>
</div>

<?php else: ?>
<div class="container">
    <div class="panel-wrap">
        <aside class="panel-side">
            <div class="user-card">
                <div class="user-av"><?= mb_substr($user['name'], 0, 1) ?></div>
                <div class="user-nm"><?= h($user['name']) ?></div>
                <div class="user-em"><?= h($user['email']) ?></div>
                <div class="user-since">Üyelik: <?= date('d.m.Y', strtotime($user['created_at'])) ?></div>
            </div>
            <nav class="panel-nav">
                <a href="?tab=genel" class="pn-item <?= $activeTab==='genel'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Genel Bakış
                </a>
                <a href="?tab=kaydedilenler" class="pn-item <?= $activeTab==='kaydedilenler'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    Kaydedilenler
                    <?php if ($stats['saved'] > 0): ?><span class="pn-badge"><?= $stats['saved'] ?></span><?php endif; ?>
                </a>
                <a href="?tab=favoriler" class="pn-item <?= $activeTab==='favoriler'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Favorilerim
                    <?php if ($stats['favorites'] > 0): ?><span class="pn-badge"><?= $stats['favorites'] ?></span><?php endif; ?>
                </a>
                <a href="?tab=bildirimler" class="pn-item <?= $activeTab==='bildirimler'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    Bildirimler
                    <?php if ($stats['notifications'] > 0): ?><span class="pn-badge"><?= $stats['notifications'] ?></span><?php endif; ?>
                </a>
                <a href="?tab=ayarlar" class="pn-item <?= $activeTab==='ayarlar'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Hesap Ayarları
                </a>
                <a href="?tab=tercihler" class="pn-item <?= $activeTab==='tercihler'?'active':'' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    E-posta Tercihleri
                </a>
            </nav>
            <div class="pn-logout">
                <form method="post">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-outline" style="width:100%;font-size:13px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:5px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </aside>

        <div class="panel-main">
            <?php if ($error): ?><div class="alert-e"><?= h($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert-s"><?= h($success) ?></div><?php endif; ?>

            <?php if ($activeTab === 'genel'): ?>
            <div class="pc">
                <h2>Genel Bakış</h2>
                <div class="stats-row">
                    <div class="st-card" style="--c:#c8102e">
                        <div class="st-num"><?= $stats['favorites'] ?></div>
                        <div class="st-lbl">Favori</div>
                    </div>
                    <div class="st-card" style="--c:#2563eb">
                        <div class="st-num"><?= $stats['saved'] ?></div>
                        <div class="st-lbl">Kaydedilen</div>
                    </div>
                    <div class="st-card" style="--c:#f59e0b">
                        <div class="st-num"><?= $stats['notifications'] ?></div>
                        <div class="st-lbl">Bildirim</div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                    <a href="?tab=kaydedilenler" class="btn btn-navy">Kaydedilen Haberler</a>
                    <a href="?tab=favoriler" class="btn btn-red">Favorilerim</a>
                </div>
            </div>
            <?php if (!empty($favorites)): ?>
            <div class="pc">
                <h2>Son Favoriler</h2>
                <?php foreach (array_slice($favorites, 0, 3) as $item): ?>
                <div class="news-list-item">
                    <div class="nl-img"><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" loading="lazy"></a></div>
                    <div class="nl-body">
                        <span style="display:inline-block;background:<?= h($item['cat_color']??'#c8102e') ?>;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:2px;text-transform:uppercase;"><?= h($item['cat_name']??'Haber') ?></span>
                        <h4><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h4>
                        <div class="nl-meta"><span><?= timeAgo($item['published_at']) ?></span></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php elseif ($activeTab === 'kaydedilenler'): ?>
            <div class="pc">
                <h2>Kaydedilen Haberler</h2>
                <?php if (empty($saved)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    <h3>Henüz kaydedilen haber yok</h3>
                    <p>Haber sayfalarındaki "Kaydet" butonuna basarak haberleri buraya ekleyin.</p>
                    <a href="<?= BASE_PATH ?>/" class="btn btn-red" style="display:inline-block;margin-top:16px;">Haberlere Göz At</a>
                </div>
                <?php else: ?>
                <?php foreach ($saved as $item): ?>
                <div class="news-list-item" id="saved-<?= $item['id'] ?>">
                    <div class="nl-img"><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" loading="lazy"></a></div>
                    <div class="nl-body" style="flex:1;">
                        <span style="display:inline-block;background:<?= h($item['cat_color']??'#c8102e') ?>;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:2px;text-transform:uppercase;"><?= h($item['cat_name']??'Haber') ?></span>
                        <h4><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h4>
                        <div class="nl-meta"><span><?= timeAgo($item['published_at']) ?></span><span><?= number_format($item['views']) ?> okuma</span></div>
                    </div>
                    <div class="nl-act">
                        <button onclick="removeItem('saved',<?= $item['id'] ?>,'saved-<?= $item['id'] ?>')">Kaldır</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php elseif ($activeTab === 'favoriler'): ?>
            <div class="pc">
                <h2>Favori Haberlerim</h2>
                <?php if (empty($favorites)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <h3>Henüz favori haber yok</h3>
                    <p>Haber sayfalarındaki kalp ikonuna tıklayarak favorilere ekleyin.</p>
                    <a href="<?= BASE_PATH ?>/" class="btn btn-red" style="display:inline-block;margin-top:16px;">Haberlere Göz At</a>
                </div>
                <?php else: ?>
                <?php foreach ($favorites as $item): ?>
                <div class="news-list-item" id="fav-<?= $item['id'] ?>">
                    <div class="nl-img"><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><img src="<?= h($item['image']) ?>" alt="<?= h($item['title']) ?>" loading="lazy"></a></div>
                    <div class="nl-body" style="flex:1;">
                        <span style="display:inline-block;background:<?= h($item['cat_color']??'#c8102e') ?>;color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:2px;text-transform:uppercase;"><?= h($item['cat_name']??'Haber') ?></span>
                        <h4><a href="<?= BASE_PATH ?>/haber/<?= h($item['slug']) ?>"><?= h($item['title']) ?></a></h4>
                        <div class="nl-meta"><span><?= timeAgo($item['published_at']) ?></span><span><?= number_format($item['views']) ?> okuma</span></div>
                    </div>
                    <div class="nl-act">
                        <button onclick="removeItem('favorite',<?= $item['id'] ?>,'fav-<?= $item['id'] ?>')">Kaldır</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php elseif ($activeTab === 'bildirimler'): ?>
            <div class="pc">
                <h2>Bildirimler</h2>
                <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <h3>Bildirim yok</h3>
                    <p>Yeni bildirimler burada görünecek.</p>
                </div>
                <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                <div class="notif-item <?= !$notif['is_read']?'unread':'' ?>">
                    <div class="notif-dot <?= $notif['is_read']?'read':'' ?>"></div>
                    <div>
                        <div class="notif-msg"><?= h($notif['message']) ?></div>
                        <?php if ($notif['link']): ?><a href="<?= h($notif['link']) ?>" style="font-size:12px;color:var(--red);">Habere git →</a><?php endif; ?>
                        <div class="notif-time"><?= timeAgo($notif['created_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php elseif ($activeTab === 'ayarlar'): ?>
            <div class="pc">
                <h2>Profil Bilgileri</h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="fg-row">
                        <div class="fg"><label>Ad Soyad</label><input type="text" name="name" value="<?= h($user['name']) ?>" required></div>
                        <div class="fg"><label>E-posta</label><input type="email" name="email" value="<?= h($user['email']) ?>" required></div>
                    </div>
                    <button type="submit" class="btn btn-red">Kaydet</button>
                </form>
            </div>
            <div class="pc">
                <h2>Şifre Değiştir</h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_password">
                    <div class="fg-row">
                        <div class="fg"><label>Yeni Şifre</label><input type="password" name="new_password" placeholder="Min. 6 karakter" required></div>
                        <div class="fg"><label>Yeni Şifre Tekrar</label><input type="password" name="new_password2" placeholder="Tekrar girin" required></div>
                    </div>
                    <button type="submit" class="btn btn-navy">Şifreyi Güncelle</button>
                </form>
            </div>

            <?php elseif ($activeTab === 'tercihler'): ?>
            <div class="pc">
                <h2>E-posta Tercihleri</h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_prefs">
                    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--bg);border-radius:4px;margin-bottom:20px;border:2px solid var(--border);" id="nl-toggle" onclick="toggleNl()" style="cursor:pointer;">
                        <input type="checkbox" name="newsletter" value="1" id="nl-cb" <?= $user['email_newsletter']?'checked':'' ?> style="width:18px;height:18px;cursor:pointer;">
                        <div>
                            <div style="font-size:14px;font-weight:700;">Günlük Haber Bülteni</div>
                            <div style="font-size:12px;color:var(--text-soft);margin-top:2px;">Günün öne çıkan haberlerini e-posta ile alın.</div>
                        </div>
                    </div>
                    <h3 style="font-family:var(--font-head);font-size:16px;margin-bottom:12px;color:var(--navy);">İlgilendiğiniz Kategoriler</h3>
                    <div class="cat-pref-grid">
                        <?php foreach ($allCats as $cat):
                            $checked = in_array($cat['id'], $prefCatIds ?? []);
                        ?>
                        <label class="cat-pref-item <?= $checked?'checked':'' ?>" style="--c:<?= h($cat['color']) ?>;">
                            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= $checked?'checked':'' ?> onchange="this.closest('label').classList.toggle('checked',this.checked)">
                            <span class="cat-dot" style="background:<?= h($cat['color']) ?>;"></span>
                            <?= h($cat['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-red">Tercihleri Kaydet</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php endif; ?>

<script>
function showLoginTab(tab, el) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('lt-giris').style.display = tab === 'giris' ? 'block' : 'none';
    document.getElementById('lt-kayit').style.display = tab === 'kayit'  ? 'block' : 'none';
}

function removeItem(type, newsId, elId) {
    var action = type === 'favorite' ? 'remove_favorite' : 'remove_saved';
    fetch('/user_action.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({action: action, news_id: newsId})
    })
    .then(r => r.json())
    .then(d => {
        if(d.ok) {
            var el = document.getElementById(elId);
            if(el) { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(), 300); }
        } else if(d.redirect) { window.location = d.redirect; }
    });
}

function toggleNl() {
    var cb = document.getElementById('nl-cb');
    cb.checked = !cb.checked;
}
</script>

<?php include __DIR__ . '/../footer.php'; ?>
