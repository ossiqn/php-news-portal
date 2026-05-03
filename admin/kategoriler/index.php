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

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name  = trim($_POST['name'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $color = trim($_POST['color'] ?? '#dc2626');
        $order = (int)($_POST['sort_order'] ?? 0);
        if (!$name) { $error = 'Kategori adı zorunludur.'; }
        else {
            if (!$slug) $slug = mb_strtolower(preg_replace('/[^a-z0-9\-]/i','-',str_replace([' ','ş','ı','ğ','ü','ö','ç'],['- ','s','i','g','u','o','c'],$name)));
            $slug = trim(preg_replace('/-+/','-',$slug),'-');
            try {
                $pdo->prepare("INSERT INTO categories (name,slug,color,sort_order) VALUES (?,?,?,?)")->execute([$name,$slug,$color,$order]);
                logAction('Kategori Eklendi.', $name);
                $success = '"'.$name.'" kategorisi eklendi.';
            } catch (PDOException $e) { $error = 'Bu slug zaten kullanımda.'; }
        }
    } elseif ($action === 'edit') {
        $id    = (int)$_POST['id'];
        $name  = trim($_POST['name'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $color = trim($_POST['color'] ?? '#dc2626');
        $order = (int)($_POST['sort_order'] ?? 0);
        if ($name && $slug) {
            $pdo->prepare("UPDATE categories SET name=?,slug=?,color=?,sort_order=? WHERE id=?")->execute([$name,$slug,$color,$order,$id]);
            logAction('Kategori Düzenlendi.', $name);
            $success = 'Kategori güncellendi.';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
        logAction('Kategori Silindi.', 'ID: '.$id);
        $success = 'Kategori silindi.';
    }
}

try { $cats = $pdo ? ($pdo->query("SELECT c.*, COUNT(n.id) as news_count FROM categories c LEFT JOIN news n ON n.category_id=c.id GROUP BY c.id ORDER BY c.sort_order ASC")->fetchAll() ?: []) : []; } catch(Throwable $e) { $cats = []; if(!$error) $error = "Tablo bulunamadı: ".$e->getMessage(); }
$editCat = null;
if ($pdo && isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->execute([(int)$_GET['edit']]);
        $editCat = $stmt->fetch() ?: null;
    } catch(PDOException $e) { $editCat = null; }
}

$pageTitle = 'Kategoriler';
$breadcrumbs = [['label'=>'Haberler','url'=>'/admin/haberler/'],['label'=>'Kategoriler']];
require __DIR__ . '/../includes/layout.php';
?>

<?php if ($success): ?><div class="alert alert-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="page-head"><h1>KATEGORİLER</h1></div>

<div class="grid-2">
    <div class="card">
        <div class="card-head"><h2>Mevcut Kategoriler (<?= count($cats) ?>)</h2></div>
        <div class="card-body" style="padding:0">
            <table class="tbl">
                <thead><tr><th>Renk</th><th>Adı</th><th>Slug</th><th>Haber</th><th>Sıra</th><th>İşlem</th></tr></thead>
                <tbody>
                    <?php foreach ($cats as $cat): ?>
                    <tr>
                        <td><span style="display:inline-block;width:22px;height:22px;border-radius:4px;background:<?= htmlspecialchars($cat['color']) ?>;"></span></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($cat['name']) ?></td>
                        <td style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td><?= $cat['news_count'] ?></td>
                        <td><?= $cat['sort_order'] ?></td>
                        <td class="actions">
                            <a href="?edit=<?= $cat['id'] ?>" class="btn btn-primary btn-xs">Düzenle</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('<?= htmlspecialchars($cat['name']) ?> kategorisini silmek istediğinize emin misiniz? İçindeki haberler kategorisiz kalacak.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-xs">Sil</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-head"><h2><?= $editCat ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' ?></h2></div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editCat ? 'edit' : 'add' ?>">
                    <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
                    <div class="fg">
                        <label>Kategori Adı *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($editCat['name']??'') ?>" placeholder="Gündem" required oninput="genCatSlug(this.value)">
                    </div>
                    <div class="fg">
                        <label>Slug</label>
                        <input type="text" name="slug" id="catSlug" value="<?= htmlspecialchars($editCat['slug']??'') ?>" placeholder="gundem">
                    </div>
                    <div class="form-row">
                        <div class="fg">
                            <label>Renk</label>
                            <input type="color" name="color" value="<?= htmlspecialchars($editCat['color']??'#dc2626') ?>" style="height:40px;padding:3px 6px;cursor:pointer;">
                        </div>
                        <div class="fg">
                            <label>Sıralama</label>
                            <input type="number" name="sort_order" value="<?= $editCat['sort_order']??0 ?>" min="0">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" class="btn btn-success"><?= $editCat ? 'Güncelle' : 'Ekle' ?></button>
                        <?php if ($editCat): ?><a href="<?= BASE_PATH ?>/admin/kategoriler/" class="btn btn-secondary">İptal</a><?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function genCatSlug(v) {
    var s = v.toLowerCase().replace(/ş/g,'s').replace(/ı/g,'i').replace(/ğ/g,'g').replace(/ü/g,'u').replace(/ö/g,'o').replace(/ç/g,'c').replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').replace(/^-|-$/g,'');
    var el = document.getElementById('catSlug');
    if(el && !el.dataset.manual) el.value = s;
}
var cs = document.getElementById('catSlug');
if(cs) cs.addEventListener('input', function(){ this.dataset.manual = '1'; });
</script>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
