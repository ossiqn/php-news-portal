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
$error = $success = '';

try {
    if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
        $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([(int)$_GET['approve']]);
        logAction('Üye Onaylandı.', 'ID: '.(int)$_GET['approve']);
        $success = 'Üye onaylandı.';
    }
    if (isset($_GET['ban']) && is_numeric($_GET['ban'])) {
        $pdo->prepare("UPDATE users SET status='banned' WHERE id=?")->execute([(int)$_GET['ban']]);
        $success = 'Üye banlandı.';
    }
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_GET['delete']]);
        $success = 'Üye silindi.';
    }

    $page    = max(1,(int)($_GET['page']??1));
    $perPage = 25;
    $offset  = ($page-1)*$perPage;
    $search  = trim($_GET['q']??'');
    $status  = $_GET['status']??'';

    $where = ['1=1']; $params = [];
    if ($search) { $where[] = '(name LIKE ? OR email LIKE ?)'; $params[]= "%$search%"; $params[]= "%$search%"; }
    if ($status) { $where[] = 'status=?'; $params[] = $status; }
    $wStr = implode(' AND ',$where);

    $total  = (int)$pdo->prepare("SELECT COUNT(*) FROM users WHERE $wStr")->execute($params) ? (function() use ($pdo,$wStr,$params){ $s=$pdo->prepare("SELECT COUNT(*) FROM users WHERE $wStr"); $s->execute($params); return $s->fetchColumn(); })() : 0;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE $wStr ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $users = $stmt->fetchAll();

    $pending = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn();
} catch (PDOException $e) {
    $error = 'Üyeler tablosu bulunamadı. Lütfen user_tables.sql dosyasını import edin.';
    $users = []; $total = 0; $pending = 0;
}

$pageTitle = 'Üyeler';
$breadcrumbs = [['label'=>'Üyeler']];
require __DIR__ . '/../includes/layout.php';
?>

<?php if ($success): ?><div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="page-head">
    <h1>ÜYELER <span style="font-size:14px;color:var(--text-soft);font-weight:600;">(<?= $total ?>)</span></h1>
    <?php if ($pending > 0): ?><span class="badge badge-orange" style="font-size:13px;padding:6px 14px;"><?= $pending ?> Onay Bekleyen</span><?php endif; ?>
</div>

<div class="card" style="margin-bottom:14px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Ad veya e-posta ara..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px;padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;outline:none;">
            <select name="status" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:13px;background:#fff;">
                <option value="">Tüm Durumlar</option>
                <option value="active" <?= $status==='active'?'selected':'' ?>>Aktif</option>
                <option value="pending" <?= $status==='pending'?'selected':'' ?>>Onay Bekleyen</option>
                <option value="banned" <?= $status==='banned'?'selected':'' ?>>Banlı</option>
            </select>
            <button type="submit" class="btn btn-primary">Ara</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <table class="tbl">
            <thead><tr><th>ID</th><th>Ad Soyad</th><th>E-posta</th><th>Durum</th><th>Üyelik Tarihi</th><th>Son Giriş</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-soft);">Üye bulunamadı.</td></tr>
                <?php endif; ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-size:12px;color:var(--text-soft);"><?= $u['id'] ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($u['name']) ?></td>
                    <td style="font-size:13px;color:var(--text-mid);"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php
                        $statusMap = ['active'=>'badge-green','pending'=>'badge-orange','banned'=>'badge-red'];
                        $statusLabel = ['active'=>'Aktif','pending'=>'Bekliyor','banned'=>'Banlı'];
                        ?>
                        <span class="badge <?= $statusMap[$u['status']] ?? 'badge-gray' ?>"><?= $statusLabel[$u['status']] ?? $u['status'] ?></span>
                    </td>
                    <td style="font-size:12px;"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                    <td style="font-size:12px;"><?= $u['last_login'] ? date('d.m.Y H:i', strtotime($u['last_login'])) : '—' ?></td>
                    <td class="actions">
                        <?php if ($u['status'] === 'pending'): ?>
                        <a href="?approve=<?= $u['id'] ?>" class="btn btn-success btn-xs">Onayla</a>
                        <?php endif; ?>
                        <?php if ($u['status'] !== 'banned'): ?>
                        <a href="?ban=<?= $u['id'] ?>" class="btn btn-warning btn-xs" onclick="return confirm('Banlamak istediğinize emin misiniz?')">Banla</a>
                        <?php else: ?>
                        <a href="?approve=<?= $u['id'] ?>" class="btn btn-success btn-xs">Aktif Et</a>
                        <?php endif; ?>
                        <a href="javascript:void(0)" onclick="confirmDelete('/admin/uyeler/?delete=<?= $u['id'] ?>','Bu üyeyi silmek istediğinize emin misiniz?')" class="btn btn-danger btn-xs">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
