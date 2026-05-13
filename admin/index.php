<?php
require_once __DIR__ . '/config.php';
adminAuth();

$pdo = getAdminPDO();
$totalNews=$draftNews=$todayNews=$totalViews=$catCount=$totalUsers=$pendingComments=0;
$logs=$recentNews=array();

if($pdo){
    try{$totalNews=(int)$pdo->query("SELECT COUNT(*) FROM news WHERE status=1")->fetchColumn();}catch(Throwable $e){}
    try{$draftNews=(int)$pdo->query("SELECT COUNT(*) FROM news WHERE status=0")->fetchColumn();}catch(Throwable $e){}
    try{$todayNews=(int)$pdo->query("SELECT COUNT(*) FROM news WHERE DATE(published_at)=CURDATE()")->fetchColumn();}catch(Throwable $e){}
    try{$totalViews=(int)$pdo->query("SELECT COALESCE(SUM(views),0) FROM news")->fetchColumn();}catch(Throwable $e){}
    try{$catCount=(int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();}catch(Throwable $e){}
    try{$totalUsers=(int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();}catch(Throwable $e){}
    try{$pendingComments=(int)$pdo->query("SELECT COUNT(*) FROM comments WHERE status='pending'")->fetchColumn();}catch(Throwable $e){}
    try{$logs=$pdo->query("SELECT * FROM admin_logs ORDER BY created_at DESC LIMIT 8")->fetchAll();}catch(Throwable $e){}
    try{$recentNews=$pdo->query("SELECT n.*,c.name as cat_name,c.color as cat_color FROM news n LEFT JOIN categories c ON c.id=n.category_id ORDER BY n.published_at DESC LIMIT 6")->fetchAll();}catch(Throwable $e){}
}

$pageTitle='Panel';
require __DIR__ . '/includes/layout.php';
?>
<div class="ph">
    <h1>Genel Bakis</h1>
    <div class="ph-actions">
        <a href="/admin/haberler/ekle.php" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Haber Ekle
        </a>
    </div>
</div>

<div class="stats-row">
    <div class="sc" style="--cc:#3b82f6">
        <div class="sc-icon" style="background:#3b82f6"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg></div>
        <div>
            <div class="sc-num"><?= number_format($totalNews) ?></div>
            <div class="sc-lbl">Yayindaki Haber</div>
        </div>
    </div>
    <div class="sc" style="--cc:#22c55e">
        <div class="sc-icon" style="background:#22c55e"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
        <div>
            <div class="sc-num"><?= $totalViews>=1000?round($totalViews/1000,1).'K':$totalViews ?></div>
            <div class="sc-lbl">Toplam Okunma</div>
        </div>
    </div>
    <div class="sc" style="--cc:#f97316">
        <div class="sc-icon" style="background:#f97316"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <div>
            <div class="sc-num"><?= $todayNews ?></div>
            <div class="sc-lbl">Bugun Eklenen</div>
        </div>
    </div>
    <div class="sc" style="--cc:#a855f7">
        <div class="sc-icon" style="background:#a855f7"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        <div>
            <div class="sc-num"><?= number_format($totalUsers) ?></div>
            <div class="sc-lbl">Kayitli Uye</div>
        </div>
    </div>
</div>

<div class="dash-tiles">
    <?php
    $tiles=array(
        array('/admin/haberler/ekle.php','#3b82f6','<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>','Haber Ekle'),
        array('/admin/haberler/','#0f172a','<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>','Haberler'),
        array('/admin/kategoriler/','#14b8a6','<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>','Kategoriler'),
        array('/admin/yorumlar/','#f97316','<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>','Yorumlar'.($pendingComments?' ('.$pendingComments.')':'')),
        array('/admin/reklamlar/','#eab308','<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>','Reklamlar'),
        array('/admin/medya/','#22c55e','<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>','Medya'),
        array('/admin/ayarlar/','#8b5cf6','<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>','Ayarlar'),
    );
    foreach($tiles as $t): ?>
    <a href="<?= $t[0] ?>" class="dt" style="background:<?= $t[1] ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><?= $t[2] ?></svg>
        <div class="dn"><?= $t[3] ?></div>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid2">
    <div class="card">
        <div class="card-head"><h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>Son Haberler</h2><a href="/admin/haberler/" class="btn btn-secondary btn-xs">Tumunu Gor</a></div>
        <div class="card-body" style="padding:0">
            <table class="tbl">
                <thead><tr><th>Baslik</th><th>Kategori</th><th>Tarih</th><th>Durum</th></tr></thead>
                <tbody>
                    <?php if(empty($recentNews)): ?>
                    <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--txs)">Henuz haber eklenmemis.</td></tr>
                    <?php endif; ?>
                    <?php foreach($recentNews as $n): ?>
                    <tr>
                        <td><a href="/admin/haberler/ekle.php?edit=<?= $n['id'] ?>" style="color:var(--acc);font-weight:600"><?= sTr(mb_substr($n['title'],0,45)) ?>...</a></td>
                        <td><?php if($n['cat_name']):?><span class="badge" style="background:<?= sTr($n['cat_color']??'#64748b') ?>;color:#fff"><?= sTr($n['cat_name']) ?></span><?php endif;?></td>
                        <td style="font-size:11px;color:var(--txs)"><?= date('d.m.Y',strtotime($n['published_at'])) ?></td>
                        <td><span class="badge <?= $n['status']?'bg-green':'bg-orange' ?>"><?= $n['status']?'Yayinda':'Taslak' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Son Islemler</h2><a href="/admin/loglar/" class="btn btn-secondary btn-xs">Tum Loglar</a></div>
        <div class="card-body" style="padding:0 0 4px">
            <div class="log-list">
                <?php if(empty($logs)): ?>
                <div style="text-align:center;padding:24px;color:var(--txs);font-size:12px">Henuz islem yok.</div>
                <?php endif; ?>
                <?php foreach($logs as $log): ?>
                <div style="display:flex;gap:10px;align-items:flex-start;padding:10px 18px;border-bottom:1px solid var(--border)">
                    <div style="width:24px;height:24px;border-radius:50%;background:var(--acc);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12px;font-weight:600;color:var(--tx)"><?= sTr($log['action']) ?></div>
                        <?php if($log['detail']):?><div style="font-size:11px;color:var(--txs);margin-top:2px"><?= sTr(mb_substr($log['detail'],0,60)) ?></div><?php endif;?>
                    </div>
                    <div style="font-size:10px;color:var(--txs);white-space:nowrap"><?= date('d.m H:i',strtotime($log['created_at'])) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/layout_end.php'; ?>
