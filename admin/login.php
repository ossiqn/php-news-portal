<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['admin_logged'])) {
    header('Location: ' . BASE_PATH . '/admin/');
    exit;
}

$error = '';
$attempts_key = 'login_attempts';
$lock_key = 'login_lock_until';
$attempts = (int)($_SESSION[$attempts_key] ?? 0);
$locked_until = (int)($_SESSION[$lock_key] ?? 0);

if ($locked_until > time()) {
    $wait = ceil(($locked_until - time()) / 60);
    $error = $wait . ' dakika bekleyin.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pw_check = hash('sha256', $pass . ADMIN_PASS_SALT);
    if ($user === ADMIN_USER && hash_equals(ADMIN_PASS_HASH, $pw_check)) {
        $_SESSION[$attempts_key] = 0;
        unset($_SESSION[$lock_key]);
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $user;
        $_SESSION['admin_login_time'] = time();
        logAction('Giris yapildi.');
        header('Location: ' . BASE_PATH . '/admin/');
        exit;
    }
    $attempts++;
    $_SESSION[$attempts_key] = $attempts;
    if ($attempts >= 5) {
        $_SESSION[$lock_key] = time() + 900;
        $error = 'Cok fazla yanlis giris. 15 dakika bekleyin.';
    } else {
        $kalan = 5 - $attempts;
        $error = 'Kullanici adi veya sifre yanlis. ' . $kalan . ' hak kaldi.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Giris</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
body{
    min-height:100vh;
    background:#0b1120;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}
body::before{
    content:'';
    position:fixed;
    top:-30%;left:-20%;
    width:60%;height:60%;
    background:radial-gradient(circle,rgba(56,189,248,.07) 0%,transparent 70%);
    pointer-events:none;
}
body::after{
    content:'';
    position:fixed;
    bottom:-20%;right:-10%;
    width:50%;height:50%;
    background:radial-gradient(circle,rgba(99,102,241,.06) 0%,transparent 70%);
    pointer-events:none;
}
.box{
    width:100%;
    max-width:400px;
    animation:up .35s ease both;
    position:relative;
    z-index:1;
}
@keyframes up{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.logo-wrap{
    text-align:center;
    margin-bottom:28px;
}
.logo-icon{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:52px;height:52px;
    background:linear-gradient(140deg,#38bdf8,#818cf8);
    border-radius:14px;
    box-shadow:0 8px 24px rgba(56,189,248,.25);
    margin-bottom:12px;
}
.logo-icon svg{width:26px;height:26px;stroke:#fff;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round}
.logo-wrap h1{font-size:18px;font-weight:700;color:#f0f6fc;letter-spacing:-.01em}
.logo-wrap small{font-size:12px;color:#475569;display:block;margin-top:3px}
.card{
    background:#111827;
    border:1px solid #1e293b;
    border-radius:16px;
    padding:28px;
    box-shadow:0 20px 60px rgba(0,0,0,.5),0 1px 0 rgba(255,255,255,.04) inset;
}
.err{
    background:rgba(239,68,68,.1);
    border:1px solid rgba(239,68,68,.25);
    color:#fca5a5;
    border-radius:10px;
    padding:11px 14px;
    font-size:13px;
    font-weight:500;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:9px;
    line-height:1.4;
}
.err svg{width:15px;height:15px;flex-shrink:0;stroke:#f87171;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.dots{display:flex;gap:5px;justify-content:flex-end;margin-bottom:20px}
.dot{width:7px;height:7px;border-radius:50%;background:#1e293b;transition:background .2s}
.dot.on{background:#ef4444}
.fg{margin-bottom:16px}
.fg label{
    display:block;
    font-size:11px;
    font-weight:600;
    color:#64748b;
    text-transform:uppercase;
    letter-spacing:.07em;
    margin-bottom:7px;
}
.inp-wrap{position:relative}
.inp-wrap input{
    width:100%;
    background:#0b1120;
    border:1.5px solid #1e293b;
    border-radius:10px;
    color:#e2e8f0;
    font-size:14px;
    font-family:inherit;
    padding:11px 42px 11px 14px;
    outline:none;
    transition:border-color .18s,box-shadow .18s;
    -webkit-appearance:none;
}
.inp-wrap input:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,.12);
}
.inp-wrap input::placeholder{color:#334155}
.eye{
    position:absolute;
    right:12px;top:50%;
    transform:translateY(-50%);
    background:none;border:none;
    padding:4px;cursor:pointer;
    color:#475569;
    display:flex;align-items:center;
    transition:color .15s;
}
.eye:hover{color:#94a3b8}
.eye svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.btn{
    width:100%;
    background:linear-gradient(135deg,#38bdf8,#818cf8);
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:14px;
    font-weight:700;
    font-family:inherit;
    padding:12px;
    cursor:pointer;
    letter-spacing:.02em;
    margin-top:4px;
    transition:opacity .18s,transform .1s;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    -webkit-appearance:none;
}
.btn:hover{opacity:.9}
.btn:active{transform:scale(.98)}
.btn svg{width:15px;height:15px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
.sep{height:1px;background:#1e293b;margin:22px 0}
.meta{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
}
.meta-item{
    display:flex;
    align-items:center;
    gap:5px;
    font-size:11px;
    color:#475569;
}
.meta-item svg{width:11px;height:11px;stroke:#38bdf8;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.foot{text-align:center;margin-top:20px;font-size:11px;color:#334155}
@keyframes spin{to{transform:rotate(360deg)}}
.spin{animation:spin .7s linear infinite;display:inline-block}
</style>
</head>
<body>
<div class="box">
    <div class="logo-wrap">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <h1>Yonetim Paneli</h1>
        <small>v<?= ADMIN_VERSION ?> &middot; Guvenli Erisim</small>
    </div>

    <div class="card">
        <?php if ($error): ?>
        <div class="err">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($attempts > 0 && $attempts < 5): ?>
        <div class="dots">
            <?php for ($i=1;$i<=5;$i++): ?>
            <div class="dot <?= $i<=$attempts?'on':'' ?>"></div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <form method="post" id="lf">
            <div class="fg">
                <label>Kullanici Adi</label>
                <div class="inp-wrap">
                    <input type="text" name="username" placeholder="admin" autofocus required autocomplete="username">
                    <span class="eye" style="cursor:default">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                </div>
            </div>
            <div class="fg">
                <label>Sifre</label>
                <div class="inp-wrap">
                    <input type="password" name="password" id="pw" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="eye" onclick="var i=document.getElementById('pw');i.type=i.type==='password'?'text':'password'">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn" id="sb">
                <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Giris Yap
            </button>
        </form>

        <div class="sep"></div>
        <div class="meta">
            <span class="meta-item">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= date('d.m.Y H:i') ?>
            </span>
            <span class="meta-item">
                <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                PHP <?= PHP_MAJOR_VERSION ?>.<?= PHP_MINOR_VERSION ?>
            </span>
            <span class="meta-item">
                <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                XAMPP
            </span>
        </div>
    </div>
    <div class="foot">&copy; <?= date('Y') ?> Yonetim Paneli</div>
</div>
<script>
document.getElementById('lf').addEventListener('submit',function(){
    var b=document.getElementById('sb');
    b.innerHTML='<svg class="spin" viewBox="0 0 24 24" style="stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Giris yapiliyor...';
    b.disabled=true;
});
</script>
</body>
</html>
