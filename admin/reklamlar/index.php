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
$success = $error = '';

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $code     = trim($_POST['code'] ?? '');
    $status   = (int)($_POST['status'] ?? 1);
    $start    = $_POST['start_date'] ?: null;
    $end      = $_POST['end_date'] ?: null;
    $editId   = (int)($_POST['edit_id'] ?? 0);

    try {
        if ($editId) {
            $pdo->prepare("UPDATE ads SET name=?,position=?,code=?,status=?,start_date=?,end_date=? WHERE id=?")->execute([$name,$position,$code,$status,$start,$end,$editId]);
            $success = 'Reklam güncellendi.';
        } else {
            $pdo->prepare("INSERT INTO ads (name,position,code,status,start_date,end_date) VALUES (?,?,?,?,?,?)")->execute([$name,$position,$code,$status,$start,$end]);
            $success = 'Reklam eklendi.';
        }
        logAction($editId?'Reklam Düzenlendi.':'Reklam Eklendi.', $name);
    } catch (PDOException $e) { $error = $e->getMessage(); }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM ads WHERE id=?")->execute([(int)$_GET['delete']]);
        $success = 'Reklam silindi.';
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pdo->prepare("UPDATE ads SET status=1-status WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: ' . BASE_PATH . '/admin/reklamlar/');
    exit;
}

try { $ads = $pdo ? ($pdo->query("SELECT * FROM ads ORDER BY created_at DESC")->fetchAll() ?: []) : []; } catch(Throwable $e) { $ads = []; if(!$error) $error = "Tablo bulunamadı: ".$e->getMessage(); }
$editAd = null;
if ($pdo && isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM ads WHERE id=?");
        $stmt->execute([(int)$_GET['edit']]);
        $editAd = $stmt->fetch() ?: null;
    } catch(PDOException $e) { $editAd = null; }
}

$positions = ['header'=>'Header (970x90)','content_top'=>'İçerik Üstü (728x90)','content_mid'=>'İçerik Ortası (300x250)','content_bottom'=>'İçerik Altı (728x90)','sidebar_top'=>'Sidebar Üst (300x250)','sidebar_bottom'=>'Sidebar Alt (300x600)','footer'=>'Footer (970x90)','popup'=>'Pop-up'];

$pageTitle = 'Reklamlar';
$breadcrumbs = [['label'=>'Reklamlar']];
require __DIR__ . '/../includes/layout.php';
?>

<?php if ($success): ?><div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="page-head"><h1>REKLAMLAR</h1></div>

<div class="grid-2">
    <div class="card">
        <div class="card-head"><h2>Reklamlar (<?= count($ads) ?>)</h2></div>
        <div class="card-body" style="padding:0">
            <table class="tbl">
                <thead><tr><th>Ad</th><th>Konum</th><th>Tarihler</th><th>Durum</th><th>İşlem</th></tr></thead>
                <tbody>
                    <?php if (empty($ads)): ?><tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-soft);">Henüz reklam yok.</td></tr><?php endif; ?>
                    <?php foreach ($ads as $ad): ?>
                    <tr>
                        <td style="font-weight:600;"><?= htmlspecialchars($ad['name']) ?></td>
                        <td><span class="badge badge-blue"><?= htmlspecialchars($positions[$ad['position']] ?? $ad['position']) ?></span></td>
                        <td style="font-size:11px;color:var(--text-soft);">
                            <?= $ad['start_date'] ? date('d.m.Y',strtotime($ad['start_date'])) : '—' ?>
                            <?= $ad['end_date'] ? ' → '.date('d.m.Y',strtotime($ad['end_date'])) : '' ?>
                        </td>
                        <td><a href="?toggle=<?= $ad['id'] ?>"><?= $ad['status'] ? '<span class="badge badge-green">Aktif</span>' : '<span class="badge badge-gray">Pasif</span>' ?></a></td>
                        <td class="actions">
                            <a href="?edit=<?= $ad['id'] ?>" class="btn btn-primary btn-xs">Düzenle</a>
                            <a href="javascript:void(0)" onclick="confirmDelete('/admin/reklamlar/?delete=<?= $ad['id'] ?>','Reklamı silmek istediğinize emin misiniz?')" class="btn btn-danger btn-xs">Sil</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2><?= $editAd ? 'Reklam Düzenle' : 'Yeni Reklam Ekle' ?></h2></div>
        <div class="card-body">
            <form method="post">
                <?php if ($editAd): ?><input type="hidden" name="edit_id" value="<?= $editAd['id'] ?>"><?php endif; ?>
                <div class="fg"><label>Reklam Adı *</label><input type="text" name="name" value="<?= htmlspecialchars($editAd['name']??'') ?>" placeholder="Google Adsense Header" required></div>
                <div class="fg">
                    <label>Konum</label>
                    <select name="position">
                        <?php foreach ($positions as $pv=>$pl): ?>
                        <option value="<?= $pv ?>" <?= ($editAd['position']??'')===$pv?'selected':'' ?>><?= $pl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fg"><label>Reklam Kodu</label><textarea name="code" rows="5" placeholder="<script async src=...></script>"><?= htmlspecialchars($editAd['code']??'') ?></textarea><div class="hint">HTML/JS reklam kodunuzu yapıştırın.</div></div>
                <div class="form-row">
                    <div class="fg"><label>Başlangıç Tarihi</label><input type="date" name="start_date" value="<?= htmlspecialchars($editAd['start_date']??'') ?>"></div>
                    <div class="fg"><label>Bitiş Tarihi</label><input type="date" name="end_date" value="<?= htmlspecialchars($editAd['end_date']??'') ?>"></div>
                </div>
                <div class="fg">
                    <label>Durum</label>
                    <div class="radio-group">
                        <label class="radio-item"><input type="radio" name="status" value="1" <?= ($editAd['status']??1)==1?'checked':'' ?>><span>Aktif</span></label>
                        <label class="radio-item"><input type="radio" name="status" value="0" <?= ($editAd['status']??1)==0?'checked':'' ?>><span>Pasif</span></label>
                    </div>
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-success"><?= $editAd ? 'Güncelle' : 'Ekle' ?></button>
                    <?php if ($editAd): ?><a href="<?= BASE_PATH ?>/admin/reklamlar/" class="btn btn-secondary">İptal</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
