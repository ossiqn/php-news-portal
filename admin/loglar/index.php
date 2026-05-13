<?php
require_once __DIR__ . '/../config.php';
adminAuth();

$pdo   = getAdminPDO();
$logs  = [];
$total = 0;
$page  = max(1,(int)($_GET['page']??1));
$perPage = 30;
$offset  = ($page-1)*$perPage;

if ($pdo) {
    try {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM admin_logs")->fetchColumn();
        $logs  = $pdo->query("SELECT * FROM admin_logs ORDER BY created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();
    } catch(Throwable $e){}
}

$totalPages = $total ? (int)ceil($total/$perPage) : 0;
$pageTitle  = 'Loglar';
$breadcrumbs = [['label'=>'Loglar']];
require __DIR__ . '/../includes/layout.php';
?>

<div class="ph"><h1>LOGLAR <span style="font-size:13px;color:var(--txs);font-weight:600;">(<?= $total ?>)</span></h1></div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="tbl">
            <thead><tr><th>ID</th><th>İşlem</th><th>Detay</th><th>Kullanıcı</th><th>IP</th><th>Tarih</th></tr></thead>
            <tbody>
                <?php if (empty($logs)): ?><tr><td colspan="6" style="text-align:center;padding:32px;color:var(--txs);">Log kaydı bulunamadı.</td></tr><?php endif; ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="font-size:11px;color:var(--txs);"><?= $log['id'] ?></td>
                    <td style="font-weight:600;"><?= sTr($log['action']) ?></td>
                    <td style="color:var(--txm);font-size:12px;"><?= sTr($log['detail']??'') ?></td>
                    <td><span class="badge bg-blue"><?= sTr($log['admin_user']??'admin') ?></span></td>
                    <td style="font-size:11px;color:var(--txs);"><?= sTr($log['ip']??'') ?></td>
                    <td style="font-size:11px;white-space:nowrap;"><?= date('d.m.Y H:i:s',strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <div style="padding:14px 18px;border-top:1px solid var(--border);">
            <div class="pagination">
                <?php if ($page>1): ?><a href="?page=<?= $page-1 ?>" class="pg">‹</a><?php endif; ?>
                <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                <a href="?page=<?= $p ?>" class="pg <?= $p===$page?'active':'' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($page<$totalPages): ?><a href="?page=<?= $page+1 ?>" class="pg">›</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
