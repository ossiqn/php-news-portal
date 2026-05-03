<?php
require_once __DIR__ . '/../config.php';
adminAuth();
require_once ROOT_DIR . '/functions.php';

try {
    $pdo = getPDO();
} catch (PDOException $e) {
    $pdo = null;
}
$success = $error = '';

try {
    if ($pdo) {
        if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
            $pdo->prepare("UPDATE comments SET status='approved' WHERE id=?")->execute([(int)$_GET['approve']]);
            $success = 'Yorum onaylandı.';
        }
        if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
            $pdo->prepare("UPDATE comments SET status='rejected' WHERE id=?")->execute([(int)$_GET['reject']]);
            $success = 'Yorum reddedildi.';
        }
        if (isset($_GET['spam']) && is_numeric($_GET['spam'])) {
            $pdo->prepare("UPDATE comments SET status='spam' WHERE id=?")->execute([(int)$_GET['spam']]);
            $success = 'Spam olarak işaretlendi.';
        }
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([(int)$_GET['delete']]);
            $success = 'Yorum silindi.';
        }

        $filter  = $_GET['filter'] ?? 'pending';
        $page    = max(1,(int)($_GET['page']??1));
        $perPage = 20;
        $offset  = ($page-1)*$perPage;

        $allowedFilters = ['pending','approved','rejected','spam',''];
        if (!in_array($filter, $allowedFilters)) $filter = 'pending';

        if ($filter !== '') {
            $stmt_cnt = $pdo->prepare("SELECT COUNT(*) FROM comments c WHERE c.status=?");
            $stmt_cnt->execute([$filter]);
            $total = (int)$stmt_cnt->fetchColumn();
            $stmt_list = $pdo->prepare("SELECT c.*,n.title as news_title,n.slug as news_slug FROM comments c LEFT JOIN news n ON n.id=c.news_id WHERE c.status=? ORDER BY c.created_at DESC LIMIT ? OFFSET ?");
            $stmt_list->execute([$filter, $perPage, $offset]);
            $comments = $stmt_list->fetchAll();
        } else {
            $total = (int)$pdo->query("SELECT COUNT(*) FROM comments c")->fetchColumn();
            $stmt_list = $pdo->prepare("SELECT c.*,n.title as news_title,n.slug as news_slug FROM comments c LEFT JOIN news n ON n.id=c.news_id ORDER BY c.created_at DESC LIMIT ? OFFSET ?");
            $stmt_list->execute([$perPage, $offset]);
            $comments = $stmt_list->fetchAll();
        }
        $counts  = $pdo->query("SELECT status,COUNT(*) as cnt FROM comments GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
        $totalAll = array_sum($counts);
    } else {
        $filter = 'pending'; $comments = []; $total = 0; $counts = []; $totalAll = 0; $page = 1; $perPage = 20;
        $error = 'Veritabanı bağlantısı kurulamadı.';
    }
} catch (Throwable $e) {
    $comments = []; $total = 0; $counts = []; $totalAll = 0; $filter = 'pending'; $page = 1; $perPage = 20;
    $error = 'Sorgu hatası: ' . $e->getMessage();
}

$pages = $perPage > 0 ? (int)ceil($total/$perPage) : 1;

$pageTitle   = 'Yorumlar';
$breadcrumbs = [['label'=>'Yorumlar']];
require __DIR__ . '/../includes/layout.php';

$statusCfg = [
    'pending'  => ['badge'=>'bg-orange', 'label'=>'Bekliyor',    'icon'=>'clock'],
    'approved' => ['badge'=>'bg-green',  'label'=>'Onaylı',      'icon'=>'check'],
    'rejected' => ['badge'=>'bg-red',    'label'=>'Reddedildi',  'icon'=>'x'],
    'spam'     => ['badge'=>'bg-gray',   'label'=>'Spam',        'icon'=>'alert'],
];
?>
<style>
.yorum-filter-bar{display:flex;gap:8px;margin-bottom:22px;flex-wrap:wrap;align-items:center}
.yf-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:var(--r);font-size:12px;font-weight:700;border:1.5px solid var(--border);background:#fff;color:var(--tx);cursor:pointer;text-decoration:none;transition:all .15s}
.yf-btn:hover{border-color:var(--acc);color:var(--acc)}
.yf-btn.active{background:var(--acc);color:#fff;border-color:var(--acc)}
.yf-count{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:18px;background:rgba(255,255,255,.25);border-radius:9px;font-size:10px;padding:0 5px}
.yf-btn:not(.active) .yf-count{background:var(--bg);color:var(--txs)}
.comment-row{padding:16px 18px;border-bottom:1px solid var(--border);display:grid;grid-template-columns:200px 1fr auto;gap:16px;align-items:start;transition:background .15s}
.comment-row:last-child{border-bottom:none}
.comment-row:hover{background:#f8fafc}
.cr-author .ca-name{font-size:13px;font-weight:700;color:var(--tx)}
.cr-author .ca-email{font-size:11px;color:var(--txs);margin-top:2px}
.cr-author .ca-ip{font-size:10px;color:var(--txs);margin-top:1px;font-family:monospace}
.cr-author .ca-date{font-size:11px;color:var(--txs);margin-top:6px;display:flex;align-items:center;gap:4px}
.cr-content .cc-text{font-size:13px;color:var(--txm);line-height:1.6;margin-bottom:8px}
.cr-content .cc-news{font-size:11px;color:var(--acc);display:flex;align-items:center;gap:5px;text-decoration:none;font-weight:600}
.cr-content .cc-news:hover{text-decoration:underline}
.cr-actions{display:flex;flex-direction:column;gap:5px;align-items:flex-end}
.action-bar{display:flex;gap:4px;flex-wrap:wrap;justify-content:flex-end}
.yorum-empty{text-align:center;padding:48px 20px;color:var(--txs)}
.yorum-empty svg{width:44px;height:44px;opacity:.25;margin-bottom:12px}
.stat-mini{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px}
.sm-card{background:var(--card);border-radius:var(--r);padding:14px 16px;box-shadow:var(--sh);border-left:3px solid var(--sc,var(--acc));display:flex;align-items:center;justify-content:space-between}
.sm-card .sm-val{font-size:22px;font-weight:900;color:var(--c1);font-family:var(--fn)}
.sm-card .sm-lbl{font-size:10px;color:var(--txs);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-top:2px}
@media(max-width:100%){.comment-row{grid-template-columns:1fr;gap:10px}.stat-mini{grid-template-columns:1fr 1fr}}
</style>

<div class="ph">
    <h1>Yorumlar</h1>
    <div class="ph-actions">
        <span style="font-size:12px;color:var(--txs);">Toplam <strong><?= $totalAll ?></strong> yorum</span>
    </div>
</div>

<?php if ($success): ?>
<div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert al-error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Mini stats -->
<div class="stat-mini">
    <?php foreach (['pending'=>['#e67e22','Bekleyen'],'approved'=>['#27ae60','Onaylı'],'rejected'=>['#e74c3c','Reddedilen'],'spam'=>['#95a5a6','Spam']] as $st=>[$clr,$lbl]): ?>
    <div class="sm-card" style="--sc:<?= $clr ?>">
        <div>
            <div class="sm-val"><?= $counts[$st] ?? 0 ?></div>
            <div class="sm-lbl"><?= $lbl ?></div>
        </div>
        <div style="width:36px;height:36px;border-radius:8px;background:<?= $clr ?>;opacity:.12;"></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filter bar -->
<div class="yorum-filter-bar">
    <?php
    $filterDefs = [
        'pending'  => ['Bekleyen',   '#e67e22'],
        'approved' => ['Onaylı',     '#27ae60'],
        'rejected' => ['Reddedilen', '#e74c3c'],
        'spam'     => ['Spam',       '#95a5a6'],
        ''         => ['Tümü',       '#2980b9'],
    ];
    foreach ($filterDefs as $fk => [$fl, $fc]): ?>
    <a href="?filter=<?= urlencode($fk) ?>" class="yf-btn <?= $filter===$fk?'active':'' ?>">
        <?= $fl ?>
        <span class="yf-count"><?= $fk === '' ? $totalAll : ($counts[$fk] ?? 0) ?></span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Comments list -->
<div class="card">
    <?php if (empty($comments)): ?>
    <div class="yorum-empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <div style="font-size:14px;font-weight:600;color:var(--txm);">Bu filtrede yorum bulunamadı</div>
        <div style="font-size:12px;margin-top:4px;">Farklı bir filtre seçin</div>
    </div>
    <?php endif; ?>
    <?php foreach ($comments as $c):
        $cfg = $statusCfg[$c['status']] ?? ['badge'=>'bg-gray','label'=>$c['status']];
    ?>
    <div class="comment-row">
        <div class="cr-author">
            <div class="ca-name"><?= htmlspecialchars($c['author_name']) ?></div>
            <?php if ($c['author_email']): ?><div class="ca-email"><?= htmlspecialchars($c['author_email']) ?></div><?php endif; ?>
            <?php if ($c['ip']): ?><div class="ca-ip"><?= htmlspecialchars($c['ip']) ?></div><?php endif; ?>
            <div class="ca-date">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= date('d.m.Y H:i', strtotime($c['created_at'])) ?>
            </div>
            <div style="margin-top:6px;"><span class="badge <?= $cfg['badge'] ?>"><?= $cfg['label'] ?></span></div>
        </div>
        <div class="cr-content">
            <div class="cc-text"><?= htmlspecialchars(mb_substr($c['content'],0,200)) ?><?= mb_strlen($c['content'])>200?'…':'' ?></div>
            <?php if ($c['news_title']): ?>
            <a href="<?= BASE_PATH ?>/haber/<?= htmlspecialchars($c['news_slug']) ?>" target="_blank" class="cc-news">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <?= htmlspecialchars(mb_substr($c['news_title'],0,60)) ?>
            </a>
            <?php endif; ?>
        </div>
        <div class="cr-actions">
            <div class="action-bar">
                <?php if ($c['status'] !== 'approved'): ?>
                <a href="?approve=<?= $c['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-success btn-xs">Onayla</a>
                <?php endif; ?>
                <?php if ($c['status'] !== 'rejected'): ?>
                <a href="?reject=<?= $c['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-warning btn-xs">Reddet</a>
                <?php endif; ?>
                <?php if ($c['status'] !== 'spam'): ?>
                <a href="?spam=<?= $c['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-secondary btn-xs">Spam</a>
                <?php endif; ?>
                <a href="javascript:void(0)" onclick="delConfirm('?delete=<?= $c['id'] ?>&filter=<?= urlencode($filter) ?>')" class="btn btn-danger btn-xs">Sil</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($pages > 1): ?>
<div class="pagination">
    <?php for ($i=1;$i<=$pages;$i++): ?>
    <a href="?filter=<?= urlencode($filter) ?>&page=<?= $i ?>" class="pg <?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
