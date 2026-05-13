<?php
$_adminUri = $_SERVER['REQUEST_URI'] ?? '';
function aNav($path){
    global $_adminUri;
    return (strpos($_adminUri,$path)===0)?'active':'';
}
$_adminUser = $_SESSION['admin_user'] ?? 'admin';
$_siteName  = getSetting('site_title','Haber Sitesi');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5,viewport-fit=cover">
<title><?= isset($pageTitle)?sTr($pageTitle).' — ':'' ?>Yonetim</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --c1:#0f172a;--c2:#1e293b;--c3:#334155;
    --acc:#3b82f6;--acc2:#2563eb;--acc3:#1d4ed8;
    --red:#ef4444;--green:#22c55e;--orange:#f97316;--purple:#a855f7;--teal:#14b8a6;--yellow:#eab308;
    --bg:#f1f5f9;--card:#fff;--border:#e2e8f0;--border2:#cbd5e1;
    --tx:#0f172a;--txm:#475569;--txs:#94a3b8;
    --r:6px;--rg:8px;
    --sh:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
    --shm:0 4px 24px rgba(0,0,0,.09);
    --fn:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;
}
html{height:100%}
body{font-family:var(--fn);background:var(--bg);color:var(--tx);font-size:13px;min-height:100vh;display:flex;flex-direction:column}
a{text-decoration:none;color:inherit}
svg{display:inline-block;vertical-align:middle;flex-shrink:0}
input,select,textarea,button{font-family:var(--fn)}
.container{max-width:1460px;margin:0 auto;padding:0 20px}

/* TOPBAR */
.topbar{background:var(--c1);height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 24px;position:sticky;top:0;z-index:1500;box-shadow:0 2px 12px rgba(0,0,0,.2)}
.tb-brand{display:flex;align-items:center;gap:10px}
.tb-logo{width:30px;height:30px;background:var(--acc);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tb-logo svg{width:16px;height:16px;color:#fff}
.tb-name{font-size:13px;font-weight:700;color:#fff;letter-spacing:.02em}
.tb-ver{font-size:10px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.6);padding:1px 6px;border-radius:3px;margin-left:2px}
.tb-mid{display:flex;align-items:center;gap:2px}
.tb-btn{display:flex;align-items:center;gap:5px;color:rgba(255,255,255,.5);font-size:11px;font-weight:600;padding:5px 10px;border-radius:5px;transition:all .15s;cursor:pointer;background:none;border:none;letter-spacing:.02em}
.tb-btn:hover{color:#fff;background:rgba(255,255,255,.08)}
.tb-btn svg{width:13px;height:13px}
@media(max-width:640px){.tb-btn span{display:none}.tb-btn{padding:5px 7px}}
.tb-right{display:flex;align-items:center;gap:6px}
.tb-user{position:relative;display:flex;align-items:center;gap:7px;color:rgba(255,255,255,.75);font-size:12px;font-weight:600;padding:5px 10px;border-radius:5px;cursor:pointer;transition:background .15s}
.tb-user:hover{background:rgba(255,255,255,.08)}
.tb-user svg{width:13px;height:13px}
.tb-avatar{width:26px;height:26px;background:var(--acc);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0}
.ud{position:absolute;top:calc(100%+6px);right:0;background:var(--card);border-radius:var(--rg);box-shadow:var(--shm);min-width:180px;z-index:999;border:1px solid var(--border);overflow:hidden;display:none}
.tb-user:hover .ud{display:block}
.ud a{display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:12px;color:var(--tx);border-bottom:1px solid var(--border);transition:background .15s}
.ud a:last-child{border-bottom:none}
.ud a:hover{background:var(--bg)}
.ud a svg{width:13px;height:13px;color:var(--txs)}
.ud a.danger{color:var(--red)}
.ud a.danger svg{color:var(--red)}

/* MAIN NAV */
.main-nav{background:var(--c2);border-bottom:1px solid rgba(255,255,255,.06);overflow:visible;position:relative;z-index:1100}
.nav-row{display:flex;align-items:stretch;overflow-x:auto;overflow-y:visible;white-space:nowrap;scrollbar-width:none}
.nav-row::-webkit-scrollbar{display:none}
.nav-item{position:relative;overflow:visible}
.nl{display:flex;align-items:center;gap:6px;padding:0 14px;height:42px;font-size:11px;font-weight:600;color:rgba(255,255,255,.55);letter-spacing:.04em;text-transform:uppercase;cursor:pointer;transition:all .15s;border-bottom:2px solid transparent;margin-bottom:-1px;white-space:nowrap}
/* Mobile nav */
@media(max-width:768px){
    .main-nav{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .nav-row{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .nav-dd{position:absolute!important;left:0!important;min-width:160px!important;width:auto!important}
    .wrap{padding:12px 12px 60px}
    .tb-mid{display:none}
    .tb-name{font-size:12px}
    .tb-ver{display:none}
    .tbl th:nth-child(4),.tbl td:nth-child(4){display:none}
    .tbl th:nth-child(5),.tbl td:nth-child(5){display:none}
    .sc{padding:12px 14px}
    .sc-num{font-size:20px}
    .card-head{padding:10px 14px}
    .card-body{padding:14px}
    .btn-xs{padding:4px 8px}
    .actions{gap:3px}
}
.nl svg{width:13px;height:13px}
.nl .arr{font-size:7px;opacity:.35;margin-left:1px;transition:transform .15s}
.nl:hover,.nl.active{color:#fff;border-bottom-color:var(--acc);background:rgba(255,255,255,.04)}
.nav-item:hover .arr{transform:rotate(180deg)}
.nav-dd{position:absolute;top:100%;left:0;min-width:210px;background:var(--card);border-top:2px solid var(--acc);box-shadow:0 8px 32px rgba(0,0,0,.25);opacity:0;visibility:hidden;transform:translateY(0);transition:opacity .18s;z-index:9999;border-radius:0 0 8px 8px;overflow:visible}
.nav-item:hover .nav-dd,.nav-dd.open{opacity:1;visibility:visible;transform:translateY(0)}
.nav-dd a{display:flex;align-items:center;gap:8px;padding:11px 16px;font-size:12px;font-weight:500;color:var(--txm);border-bottom:1px solid var(--border);transition:background .12s,color .12s;white-space:nowrap}
.nav-dd a:last-child{border-bottom:none}
.nav-dd a:hover{background:var(--bg);color:var(--acc);padding-left:20px}
.nav-dd a svg{width:12px;height:12px;flex-shrink:0}

/* BREADCRUMB */
.bc{display:flex;align-items:center;gap:6px;padding:11px 0;font-size:11px;color:var(--txs)}
.bc a{color:var(--acc)}
.bc a:hover{text-decoration:underline}
.bc-sep{font-size:10px}

/* PAGE HEADER */
.ph{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.ph h1{font-size:20px;font-weight:800;color:var(--c1);letter-spacing:-.02em}
.ph-actions{display:flex;gap:8px;align-items:center}
.page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.page-head h1{font-size:20px;font-weight:800;color:var(--c1);letter-spacing:-.02em}
.page-head-actions{display:flex;gap:8px;align-items:center}

/* CARD */
.card{background:var(--card);border-radius:var(--rg);box-shadow:var(--sh);overflow:hidden;margin-bottom:18px;border:1px solid var(--border)}
.card-head{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--border);background:#fafbfc}
.card-head h2{font-size:13px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:7px}
.card-head h2 svg{width:14px;height:14px;color:var(--acc)}
.card-body{padding:18px}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:var(--fn);transition:all .15s;letter-spacing:.01em;white-space:nowrap}
.btn svg{width:13px;height:13px}
.btn-primary{background:var(--acc);color:#fff}
.btn-primary:hover{background:var(--acc2)}
.btn-success{background:#16a34a;color:#fff}
.btn-success:hover{background:#15803d}
.btn-danger{background:#dc2626;color:#fff}
.btn-danger:hover{background:#b91c1c}
.btn-warning{background:#d97706;color:#fff}
.btn-warning:hover{background:#b45309}
.btn-secondary{background:#f1f5f9;color:var(--tx);border:1px solid var(--border)}
.btn-secondary:hover{background:#e2e8f0}
.btn-purple{background:#9333ea;color:#fff}
.btn-sm{padding:5px 10px;font-size:11px}
.btn-xs{padding:3px 8px;font-size:10px;font-weight:700;border-radius:4px}
.btn-lg{padding:10px 22px;font-size:13px}

/* FORM */
.fg{margin-bottom:16px}
.fg label{display:block;font-size:11px;font-weight:700;color:var(--txm);margin-bottom:5px;letter-spacing:.04em;text-transform:uppercase}
.fg input[type=text],.fg input[type=url],.fg input[type=email],.fg input[type=number],.fg input[type=date],.fg input[type=time],.fg input[type=datetime-local],.fg select,.fg textarea{width:100%;padding:8px 11px;border:1.5px solid var(--border2);border-radius:6px;font-size:13px;color:var(--tx);outline:none;transition:border-color .18s,box-shadow .18s;background:var(--card)}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--acc);box-shadow:0 0 0 3px rgba(59,130,246,.12)}
.fg textarea{resize:vertical;min-height:90px;line-height:1.6}
.fg .hint{font-size:11px;color:var(--txs);margin-top:4px;line-height:1.45}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}

/* RADIO / CHECK */
.rc-group{display:flex;gap:16px;flex-wrap:wrap;align-items:center}
.rc-item{display:flex;align-items:center;gap:6px;cursor:pointer}
.rc-item input{width:14px;height:14px;cursor:pointer;accent-color:var(--acc)}
.rc-item span{font-size:13px;font-weight:500;color:var(--tx)}
.cb-item{display:flex;align-items:center;gap:7px;padding:8px 10px;background:var(--bg);border-radius:5px;cursor:pointer;border:1.5px solid var(--border);transition:all .15s}
.cb-item input{width:14px;height:14px;accent-color:var(--acc);cursor:pointer}
.cb-item:has(input:checked){border-color:var(--acc);background:#eff6ff}
.cb-item span{font-size:12px;font-weight:500;color:var(--txm)}

/* TABLE */
.tbl{width:100%;border-collapse:collapse}
.tbl th{background:#f8fafc;padding:9px 13px;text-align:left;font-size:10px;font-weight:700;color:var(--txs);text-transform:uppercase;letter-spacing:.07em;border-bottom:2px solid var(--border)}
.tbl td{padding:10px 13px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:middle}
.tbl tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:#f8fafc}
.actions{display:flex;gap:5px;align-items:center}

/* BADGES */
.badge{display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:.03em}
.bg-green{background:#dcfce7;color:#166534}
.bg-red{background:#fee2e2;color:#991b1b}
.bg-orange{background:#ffedd5;color:#9a3412}
.bg-blue{background:#dbeafe;color:#1e40af}
.bg-gray{background:#f1f5f9;color:#475569}
.bg-purple{background:#f3e8ff;color:#6b21a8}
.badge-green{background:#dcfce7;color:#166534}
.badge-red{background:#fee2e2;color:#991b1b}
.badge-orange{background:#ffedd5;color:#9a3412}
.badge-blue{background:#dbeafe;color:#1e40af}
.badge-gray{background:#f1f5f9;color:#475569}

/* ALERTS */
.alert{padding:11px 15px;border-radius:var(--rg);font-size:12px;font-weight:600;margin-bottom:14px;display:flex;align-items:center;gap:9px}
.alert svg{width:15px;height:15px;flex-shrink:0}
.al-success,.alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.al-error,.alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.al-info,.alert-info{background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe}
.al-warn,.alert-warn{background:#fffbeb;color:#92400e;border:1px solid #fde68a}

/* TABS */
.tab-nav{display:flex;border-bottom:2px solid var(--border);margin-bottom:22px;gap:2px}
.tab-btn,.tab-link{padding:9px 16px;font-size:12px;font-weight:600;color:var(--txs);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;background:none;border-top:none;border-left:none;border-right:none;font-family:var(--fn)}
.tab-btn.active,.tab-link.active{color:var(--acc);border-bottom-color:var(--acc)}
.tab-btn:hover,.tab-link:hover{color:var(--tx)}
.tab-pane{display:none}
.tab-pane.active{display:block}

/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.sc{background:var(--card);border-radius:var(--rg);padding:16px 18px;box-shadow:var(--sh);border:1px solid var(--border);border-left:3px solid var(--cc,var(--acc));display:flex;align-items:center;gap:12px}
.sc-icon{width:40px;height:40px;border-radius:8px;background:var(--cc,var(--acc));display:flex;align-items:center;justify-content:center;flex-shrink:0;opacity:.15}
.sc-num{font-size:24px;font-weight:900;color:var(--c1);line-height:1;letter-spacing:-.02em}
.sc-lbl{font-size:10px;color:var(--txs);margin-top:2px;font-weight:700;text-transform:uppercase;letter-spacing:.05em}

/* DASH TILES */
.dash-tiles{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;margin-bottom:20px}
.dt{border-radius:var(--rg);padding:16px 8px;text-align:center;cursor:pointer;transition:transform .15s,box-shadow .15s;color:#fff;display:block}
.dt:hover{transform:translateY(-2px);box-shadow:var(--shm)}
.dt svg{width:32px;height:32px;margin:0 auto 7px;display:block}
.dt .dn{font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;line-height:1.3}

/* LOG LIST */
.log-list{max-height:240px;overflow-y:auto}
.li{display:flex;gap:10px;align-items:flex-start;padding:9px 0;border-bottom:1px solid var(--border)}
.li:last-child{border-bottom:none}
.li-dot{width:24px;height:24px;border-radius:50%;background:var(--acc);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.li-dot svg{width:11px;height:11px;color:#fff}
.li-text{flex:1;font-size:11px;color:var(--txm);line-height:1.4}
.li-time{font-size:10px;color:var(--txs);white-space:nowrap}

/* GRID */
.grid2,.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.grid3,.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}

/* PAGINATION */
.pagination{display:flex;gap:5px;align-items:center;margin-top:14px;flex-wrap:wrap}
.pg{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;border-radius:6px;font-size:12px;font-weight:600;border:1.5px solid var(--border);background:var(--card);color:var(--tx);cursor:pointer;transition:all .15s;padding:0 8px}
.pg:hover,.pg.active{background:var(--acc);color:#fff;border-color:var(--acc)}

/* PHOTO UPLOAD */
.photo-drop{border:2px dashed var(--border2);border-radius:var(--rg);background:#f8fafc;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:all .2s;position:relative;overflow:hidden;min-height:120px}
.photo-drop:hover{border-color:var(--acc);background:#eff6ff}
.photo-drop input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.photo-drop-icon svg{width:32px;height:32px;color:var(--txs)}
.photo-drop-text{font-size:12px;color:var(--txs);text-align:center}
.photo-drop-size{font-size:11px;font-weight:700;color:var(--acc)}

/* WRAP */
.wrap{padding:20px 24px 60px;max-width:1600px;margin:0 auto;flex:1;min-width:0}

/* RESPONSIVE */
@media(min-width:1200px){
    .stats-row{grid-template-columns:repeat(4,1fr)}
    .dash-tiles{grid-template-columns:repeat(7,1fr)}
    .grid2,.grid-2{grid-template-columns:1fr 1fr}
    .grid3,.grid-3{grid-template-columns:1fr 1fr 1fr}
}
@media(min-width:900px) and (max-width:1199px){
    .stats-row{grid-template-columns:repeat(4,1fr)}
    .dash-tiles{grid-template-columns:repeat(4,1fr)}
    .grid2,.grid-2{grid-template-columns:1fr 1fr}
    .grid3,.grid-3{grid-template-columns:1fr 1fr}
    .form-row{grid-template-columns:1fr 1fr}
}
@media(max-width:899px){
    .stats-row{grid-template-columns:1fr 1fr}
    .dash-tiles{grid-template-columns:repeat(4,1fr)}
    .form-row,.form-row3{grid-template-columns:1fr}
    .grid2,.grid-2,.grid3,.grid-3{grid-template-columns:1fr}
}
@media(max-width:600px){
    .stats-row{grid-template-columns:1fr 1fr}
    .dash-tiles{grid-template-columns:repeat(3,1fr)}
    .wrap{padding:14px 14px 50px}
}
@media(max-width:400px){
    .dash-tiles{grid-template-columns:repeat(2,1fr)}
    .stats-row{grid-template-columns:1fr}
}
::-webkit-scrollbar{width:5px;height:5px}
/* Form mobile fixes */
@media(max-width:1000px){
    .news-form{grid-template-columns:1fr!important}
    .news-side{position:static!important}
    .wy-area{min-height:200px}
}
@media(max-width:600px){
    .news-form{gap:12px!important}
    .cb-grid{grid-template-columns:1fr 1fr 1fr!important}
}
::-webkit-scrollbar-track{background:#f1f5f9}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:#94a3b8}
</style>
</head>
<body>

<div class="topbar">
    <div class="tb-brand">
        <div class="tb-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></div>
        <span class="tb-name">YONETIM</span>
        <span class="tb-ver">v<?= ADMIN_VERSION ?></span>
    </div>
    <div class="tb-mid">
        <a href="<?= SITE_BASE ?: '/' ?>" target="_blank" rel="noopener" class="tb-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Siteyi Gor
        </a>
        <a href="<?= ADMIN_BASE ?>/haberler/ekle.php" class="tb-btn" style="color:rgba(255,255,255,.7)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Haber Ekle
        </a>
    </div>
    <div class="tb-right">
        <div class="tb-user">
            <div class="tb-avatar"><?= strtoupper(substr($_adminUser,0,1)) ?></div>
            <?= sTr($_adminUser) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;opacity:.4"><polyline points="6 9 12 15 18 9"/></svg>
            <div class="ud">
                <a href="<?= ADMIN_BASE ?>/ayarlar/"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-2.82.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Ayarlar</a>
                <a href="<?= ADMIN_BASE ?>/logout.php" class="danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Cikis</a>
            </div>
        </div>
    </div>
</div>

<nav class="main-nav">
    <div style="max-width:1460px;margin:0 auto;padding:0 20px">
    <div class="nav-row">
        <a href="<?= ADMIN_BASE ?>/" class="nav-item nl <?= !aNav('/admin/ayarlar')&&!aNav('/admin/haberler')&&!aNav('/admin/kategoriler')&&!aNav('/admin/reklamlar')&&!aNav('/admin/uyeler')&&!aNav('/admin/yorumlar')&&!aNav('/admin/medya')&&!aNav('/admin/loglar')&&$_adminUri!=='/admin/login.php'?'active':'' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Panel
        </a>
        <div class="nav-item">
            <span class="nl <?= aNav('/admin/ayarlar') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Ayarlar <span class="arr">▼</span>
            </span>
            <div class="nav-dd">
                <a href="<?= ADMIN_BASE ?>/ayarlar/">Genel Ayarlar</a>
                <a href="<?= ADMIN_BASE ?>/ayarlar/?tab=moduller">Moduller</a>
                <a href="<?= ADMIN_BASE ?>/ayarlar/?tab=yorumlar">Yorumlar</a>
                <a href="<?= ADMIN_BASE ?>/ayarlar/?tab=seo">SEO</a>
                <a href="<?= ADMIN_BASE ?>/ayarlar/?tab=diger">Diger</a>
            </div>
        </div>
        <div class="nav-item">
            <span class="nl <?= (aNav('/admin/haberler')||aNav('/admin/kategoriler'))?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8M15 18h-5M10 6h8v4h-8V6Z"/></svg>
                Haberler <span class="arr">▼</span>
            </span>
            <div class="nav-dd">
                <a href="<?= ADMIN_BASE ?>/haberler/">Tum Haberler</a>
                <a href="<?= ADMIN_BASE ?>/haberler/ekle.php">Haber Ekle</a>
                <a href="<?= ADMIN_BASE ?>/kategoriler/">Kategoriler</a>
                <a href="<?= ADMIN_BASE ?>/yorumlar/">Yorumlar</a>
            </div>
        </div>
        <div class="nav-item">
            <span class="nl <?= aNav('/admin/reklamlar') ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Reklamlar <span class="arr">▼</span>
            </span>
            <div class="nav-dd">
                <a href="<?= ADMIN_BASE ?>/reklamlar/">Tum Reklamlar</a>
                <a href="<?= ADMIN_BASE ?>/reklamlar/ekle.php">Reklam Ekle</a>
            </div>
        </div>
        <a href="<?= ADMIN_BASE ?>/uyeler/" class="nav-item nl <?= aNav('/admin/uyeler') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Uyeler
        </a>
        <a href="<?= ADMIN_BASE ?>/medya/" class="nav-item nl <?= aNav('/admin/medya') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Medya
        </a>
        <a href="<?= ADMIN_BASE ?>/loglar/" class="nav-item nl <?= aNav('/admin/loglar') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Loglar
        </a>
    </div>
    </div>
</nav>

<div class="wrap">
    <?php if(isset($breadcrumbs)): ?>
    <div class="bc">
        <a href="<?= ADMIN_BASE ?>/">Panel</a>
        <?php foreach($breadcrumbs as $bc): ?>
        <span class="bc-sep">›</span>
        <?php if(isset($bc['url'])): ?><a href="<?= sTr($bc['url']) ?>"><?= sTr($bc['label']) ?></a>
        <?php else: ?><span><?= sTr($bc['label']) ?></span><?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
