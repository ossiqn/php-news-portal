<?php
require_once __DIR__ . '/../config.php';
adminAuth();
require_once ROOT_DIR . '/functions.php';

try {
    $pdo = getPDO();
} catch (PDOException $e) {
    $pdo = null;
    $error = 'Veritabani baglantisi kurulamadi.';
}

if ($pdo && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM news WHERE id=?")->execute([(int)$_GET['delete']]);
        logAction('Haber Silindi.', 'ID:'.(int)$_GET['delete']);
    } catch(Throwable $e) {}
    header('Location: ' . ADMIN_BASE . '/haberler/?deleted=1');
    exit;
}
if ($pdo && isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    try {
        $pdo->prepare("UPDATE news SET status=1-status WHERE id=?")->execute([(int)$_GET['toggle']]);
    } catch(Throwable $e) {}
    header('Location: ' . ADMIN_BASE . '/haberler/');
    exit;
}

$page        = max(1,(int)($_GET['page']??1));
$perPage     = 20;
$offset      = ($page-1)*$perPage;
$search      = trim($_GET['q']??'');
$catFilter   = (int)($_GET['cat']??0);
$statusFilter = isset($_GET['status']) && $_GET['status']!=='' ? (int)$_GET['status'] : null;

$where  = ['1=1'];
$params = [];
if ($search) {
    $where[]  = '(n.title LIKE ? OR n.slug LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catFilter) {
    $where[]  = 'n.category_id=?';
    $params[] = $catFilter;
}
if ($statusFilter !== null) {
    $where[]  = 'n.status=?';
    $params[] = $statusFilter;
}
$whereStr = implode(' AND ', $where);

$total = 0;
$news  = [];
$cats  = getAllCategories();

if ($pdo) {
    try {
        $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM news n WHERE $whereStr");
        $cntStmt->execute($params);
        $total = (int)$cntStmt->fetchColumn();

        $listStmt = $pdo->prepare("SELECT n.*, c.name as cat_name, c.color as cat_color FROM news n LEFT JOIN categories c ON c.id=n.category_id WHERE $whereStr ORDER BY n.published_at DESC LIMIT $perPage OFFSET $offset");
        $listStmt->execute($params);
        $news = $listStmt->fetchAll();
    } catch(Throwable $e) {
        $error = 'Sorgu hatasi: '.$e->getMessage();
    }
}

$totalPages = $perPage > 0 ? (int)ceil($total/$perPage) : 1;
$pageTitle  = 'Haberler';
$breadcrumbs = [['label'=>'Haberler']];
require __DIR__ . '/../includes/layout.php';
?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Haber silindi.</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert al-error"><?= sTr($error) ?></div>
<?php endif; ?>

<div class="ph">
    <h1>Haberler <span style="font-size:14px;color:var(--txs);font-weight:500;"><?= number_format($total) ?> haber</span></h1>
    <div class="ph-actions">
        <a href="/admin/haberler/ekle.php" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Haber Ekle
        </a>
    </div>
</div>

<div class="card" style="margin-bottom:14px">
    <div class="card-body" style="padding:12px 16px">
        <form method="get" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="q" placeholder="Baslik veya slug ara..." value="<?= sTr($search) ?>" style="flex:1;min-width:180px;padding:7px 11px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;outline:none">
            <select name="cat" style="padding:7px 11px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;background:#fff">
                <option value="0">Tum Kategoriler</option>
                <?php foreach ($cats as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $catFilter===(int)$c['id']?'selected':'' ?>><?= sTr($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" style="padding:7px 11px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;background:#fff">
                <option value="">Tum Durumlar</option>
                <option value="1" <?= $statusFilter===1?'selected':'' ?>>Yayinda</option>
                <option value="0" <?= $statusFilter===0?'selected':'' ?>>Taslak</option>
            </select>
            <button type="submit" class="btn btn-primary">Ara</button>
            <?php if ($search || $catFilter || $statusFilter !== null): ?>
            <a href="/admin/haberler/" class="btn btn-secondary">Temizle</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:40px">ID</th>
                    <th>Baslik</th>
                    <th>Kategori</th>
                    <th>Goruntuleme</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th style="width:140px">Islem</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($news)): ?>
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--txs)">Haber bulunamadi.</td></tr>
                <?php endif; ?>
                <?php foreach ($news as $n): ?>
                <tr>
                    <td style="color:var(--txs);font-size:11px"><?= $n['id'] ?></td>
                    <td>
                        <a href="/admin/haberler/ekle.php?id=<?= $n['id'] ?>" style="color:var(--acc);font-weight:600;font-size:13px;line-height:1.35;display:block">
                            <?= sTr(mb_substr($n['title'],0,65)) ?><?= mb_strlen($n['title'])>65?'...':'' ?>
                        </a>
                        <div style="display:flex;gap:4px;margin-top:3px">
                            <?php if ($n['is_breaking']): ?><span class="badge bg-red" style="font-size:9px">SON DAKIKA</span><?php endif; ?>
                            <?php if ($n['is_featured']): ?><span class="badge bg-blue" style="font-size:9px">MANSET</span><?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($n['cat_name']): ?>
                        <span class="badge" style="background:<?= sTr($n['cat_color']??'#64748b') ?>;color:#fff"><?= sTr($n['cat_name']) ?></span>
                        <?php else: ?><span style="color:var(--txs)">—</span><?php endif; ?>
                    </td>
                    <td style="font-size:12px"><?= number_format($n['views']??0) ?></td>
                    <td style="font-size:11px;color:var(--txs);white-space:nowrap"><?= date('d.m.Y H:i',strtotime($n['published_at'])) ?></td>
                    <td>
                        <a href="/admin/haberler/?toggle=<?= $n['id'] ?>">
                            <?= $n['status'] ? '<span class="badge bg-green">Yayinda</span>' : '<span class="badge bg-orange">Taslak</span>' ?>
                        </a>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="/admin/haberler/ekle.php?id=<?= $n['id'] ?>" class="btn btn-primary btn-xs">Duzenle</a>
                            <a href="/haber/<?= sTr($n['slug']) ?>" target="_blank" class="btn btn-secondary btn-xs">Gor</a>
                            <a href="javascript:void(0)" onclick="delConfirm('/admin/haberler/?delete=<?= $n['id'] ?>')" class="btn btn-danger btn-xs">Sil</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <span style="font-size:12px;color:var(--txs)">Toplam <?= $total ?> haber, <?= $totalPages ?> sayfa</span>
            <div class="pagination" style="margin-top:0;margin-left:auto">
                <?php if ($page>1): ?><a href="?page=<?= $page-1 ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter??'' ?>" class="pg">&lsaquo;</a><?php endif; ?>
                <?php for ($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                <a href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter??'' ?>" class="pg <?= $p===$page?'active':'' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($page<$totalPages): ?><a href="?page=<?= $page+1 ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter??'' ?>" class="pg">&rsaquo;</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
