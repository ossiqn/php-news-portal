<?php
require_once __DIR__ . '/../config.php';
adminAuth();
require_once ROOT_DIR . '/functions.php';

try {
    $pdo = getPDO();
} catch (PDOException $e) {
    $pdo = null;
    $error = 'Veritabanı bağlantısı kurulamadı: ' . $e->getMessage();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM news WHERE id=?")->execute([(int)$_GET['delete']]);
    logAction('Haber Silindi.', 'ID: '.(int)$_GET['delete']);
    header('Location: ' . BASE_PATH . '/admin/haberler/?deleted=1');
    exit;
}
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pdo->prepare("UPDATE news SET status = 1-status WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: ' . BASE_PATH . '/admin/haberler/');
    exit;
}

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;
$search  = trim($_GET['q'] ?? '');
$catFilter = (int)($_GET['cat'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

$where = ['1=1'];
$params = [];
if ($search) { $where[] = '(n.title LIKE ? OR n.slug LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($catFilter) { $where[] = 'n.category_id=?'; $params[] = $catFilter; }
if ($statusFilter !== '') { $where[] = 'n.status=?'; $params[] = (int)$statusFilter; }
$whereStr = implode(' AND ', $where);

$total = (int)$pdo->prepare("SELECT COUNT(*) FROM news n WHERE $whereStr")->execute($params) ? (function() use ($pdo, $whereStr, $params) { $s=$pdo->prepare("SELECT COUNT(*) FROM news n WHERE $whereStr"); $s->execute($params); return $s->fetchColumn(); })() : 0;
$stmt = $pdo->prepare("SELECT n.*, c.name as cat_name, c.color as cat_color FROM news n LEFT JOIN categories c ON c.id=n.category_id WHERE $whereStr ORDER BY n.published_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$news = $stmt->fetchAll();

$cats = getAllCategories();
$totalPages = (int)ceil($total / $perPage);

$pageTitle = 'Haberler';
$breadcrumbs = [['label'=>'Haberler']];
require __DIR__ . '/../includes/layout.php';
?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Haber silindi.</div><?php endif; ?>

<div class="page-head">
    <h1>HABERLER <span style="font-size:14px;color:var(--text-soft);font-weight:600;">(<?= number_format($total) ?>)</span></h1>
    <div class="page-head-actions">
        <a href="<?= BASE_PATH ?>/admin/haberler/ekle.php" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Haber Ekle
        </a>
    </div>
</div>

<div class="card" style="margin-bottom:14px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="get" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Başlık veya slug ara..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px;padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;outline:none;">
            <select name="cat" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;background:#fff;">
                <option value="0">Tüm Kategoriler</option>
                <?php foreach ($cats as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $catFilter==(int)$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;background:#fff;">
                <option value="">Tüm Durumlar</option>
                <option value="1" <?= $statusFilter==='1'?'selected':'' ?>>Yayında</option>
                <option value="0" <?= $statusFilter==='0'?'selected':'' ?>>Taslak</option>
            </select>
            <button type="submit" class="btn btn-primary">Ara</button>
            <?php if ($search || $catFilter || $statusFilter !== ''): ?>
            <a href="<?= BASE_PATH ?>/admin/haberler/" class="btn btn-secondary">Temizle</a>
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
                    <th>Başlık</th>
                    <th>Kategori</th>
                    <th>Görüntülenme</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th style="width:160px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($news)): ?>
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-soft);">Haber bulunamadı.</td></tr>
                <?php endif; ?>
                <?php foreach ($news as $n): ?>
                <tr>
                    <td style="color:var(--text-soft);font-size:12px;"><?= $n['id'] ?></td>
                    <td>
                        <a href="<?= BASE_PATH ?>/admin/haberler/duzenle.php?id=<?= $n['id'] ?>" style="color:var(--blue);font-weight:600;font-size:13px;">
                            <?= htmlspecialchars(mb_substr($n['title'],0,65)) ?><?= mb_strlen($n['title'])>65?'...':'' ?>
                        </a>
                        <?php if ($n['is_breaking']): ?><span class="badge badge-red" style="margin-left:4px;font-size:9px;">SON DAKİKA</span><?php endif; ?>
                        <?php if ($n['is_featured']): ?><span class="badge badge-blue" style="margin-left:2px;font-size:9px;">MANŞET</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($n['cat_name']): ?>
                        <span style="display:inline-block;background:<?= htmlspecialchars($n['cat_color']??'#ccc') ?>;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:2px;"><?= htmlspecialchars($n['cat_name']) ?></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td><?= number_format($n['views']) ?></td>
                    <td style="font-size:12px;white-space:nowrap;"><?= date('d.m.Y H:i', strtotime($n['published_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_PATH ?>/admin/haberler/?toggle=<?= $n['id'] ?>" style="cursor:pointer;">
                            <?= $n['status'] ? '<span class="badge badge-green">Yayında</span>' : '<span class="badge badge-gray">Taslak</span>' ?>
                        </a>
                    </td>
                    <td class="actions">
                        <a href="<?= BASE_PATH ?>/admin/haberler/duzenle.php?id=<?= $n['id'] ?>" class="btn btn-primary btn-xs">Düzenle</a>
                        <a href="<?= BASE_PATH ?>/haber/<?= htmlspecialchars($n['slug']) ?>" target="_blank" class="btn btn-secondary btn-xs">Gör</a>
                        <a href="javascript:void(0)" onclick="confirmDelete('/admin/haberler/?delete=<?= $n['id'] ?>','\"<?= addslashes(mb_substr($n['title'],0,30)) ?>\" haberini silmek istediğinize emin misiniz?')" class="btn btn-danger btn-xs">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <div style="padding:16px 20px;border-top:1px solid var(--border);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span style="font-size:12px;color:var(--text-soft);">Toplam <?= $total ?> haber, <?= $totalPages ?> sayfa</span>
            <div class="pagination" style="margin-top:0;margin-left:auto;">
                <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter ?>" class="page-btn">‹</a><?php endif; ?>
                <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
                <a href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter ?>" class="page-btn <?= $p===$page?'active':'' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?>&q=<?= urlencode($search) ?>&cat=<?= $catFilter ?>&status=<?= $statusFilter ?>" class="page-btn">›</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
