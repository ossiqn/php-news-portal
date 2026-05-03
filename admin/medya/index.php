<?php
require_once __DIR__ . '/../config.php';
adminAuth();

$pdo     = getAdminPDO();
$success = $error = '';
$uploadDir = ROOT_DIR . '/uploads/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['media_file']['name'])) {
    $file    = $_FILES['media_file'];
    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($file['type'], $allowed)) {
        $error = 'Sadece JPG, PNG, GIF, WEBP yükleyebilirsiniz.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = 'Dosya boyutu 5MB\'ı geçemez.';
    } else {
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        $dest     = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            if ($pdo) {
                try {
                    $pdo->prepare("INSERT INTO media (filename,original_name,mime_type,size,path) VALUES (?,?,?,?,?)")
                        ->execute([$filename,$file['name'],$file['type'],$file['size'],'/uploads/'.$filename]);
                } catch(Throwable $e){}
            }
            logAction('Medya Yüklendi.', $file['name']);
            $success = sTr($file['name']) . ' başarıyla yüklendi.';
        } else {
            $error = 'Dosya yüklenemedi. Klasör izinlerini kontrol edin.';
        }
    }
}

if (isset($_GET['delete'])) {
    $fname = basename($_GET['delete']);
    $fpath = $uploadDir . $fname;
    if (file_exists($fpath)) { unlink($fpath); }
    if ($pdo) { try { $pdo->prepare("DELETE FROM media WHERE filename=?")->execute([$fname]); } catch(Throwable $e){} }
    $success = 'Dosya silindi.';
}

$files = [];
if (is_dir($uploadDir)) {
    foreach (glob($uploadDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE) as $f) {
        $files[] = ['name'=>basename($f),'path'=>'/uploads/'.basename($f),'size'=>filesize($f),'time'=>filemtime($f)];
    }
    usort($files, function($a,$b) { return $b['time'] - $a['time']; });
}

$pageTitle   = 'Medya Kütüphanesi';
$breadcrumbs = [['label'=>'Medya']];
require __DIR__ . '/../includes/layout.php';
?>

<style>
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
.media-item{background:#fff;border-radius:var(--r);overflow:hidden;box-shadow:var(--sh);transition:all .2s;position:relative;cursor:pointer}
.media-item:hover{box-shadow:var(--shm);transform:translateY(-2px)}
.media-item img{width:100%;height:120px;object-fit:cover;display:block}
.media-info{padding:8px}
.media-info .mname{font-size:11px;font-weight:600;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.media-info .msize{font-size:10px;color:var(--txs);margin-top:2px}
.media-actions{position:absolute;top:6px;right:6px;display:none;gap:4px}
.media-item:hover .media-actions{display:flex}
.ma-btn{width:26px;height:26px;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;font-size:11px}
.drop-zone{border:2px dashed var(--border);border-radius:var(--r);background:#f8f9fb;padding:40px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:20px}
.drop-zone:hover,.drop-zone.dragover{border-color:var(--acc);background:#eaf4fb}
.drop-zone svg{width:48px;height:48px;color:var(--border);margin:0 auto 12px;display:block}
.copy-toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--c1);color:#fff;padding:10px 20px;border-radius:20px;font-size:13px;font-weight:700;z-index:9999;display:none;box-shadow:var(--shm)}
</style>

<?php if ($success): ?><div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert al-error"><?= sTr($error) ?></div><?php endif; ?>

<div class="ph">
    <h1>MEDYA KÜTÜPHANESİ <span style="font-size:13px;color:var(--txs);font-weight:600;">(<?= count($files) ?> dosya)</span></h1>
</div>

<form method="post" enctype="multipart/form-data" id="uploadForm">
    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileIn').click()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <div style="font-size:15px;font-weight:700;color:var(--txm);">Görsel Yükle</div>
        <div style="font-size:12px;color:var(--txs);margin-top:6px;">Dosyaları buraya sürükleyin veya tıklayın</div>
        <div style="font-size:11px;color:var(--acc);margin-top:8px;">JPG, PNG, GIF, WEBP — Max 5MB</div>
        <input type="file" name="media_file" id="fileIn" accept="image/*" style="display:none" onchange="document.getElementById('uploadForm').submit()">
    </div>
</form>

<div class="media-grid">
    <?php foreach ($files as $f): ?>
    <div class="media-item" onclick="copyUrl('<?= sTr($f['path']) ?>')">
        <img src="<?= sTr($f['path']) ?>" alt="<?= sTr($f['name']) ?>" loading="lazy">
        <div class="media-actions">
            <button class="ma-btn" style="background:var(--green);color:#fff;" onclick="event.stopPropagation();copyUrl('<?= sTr($f['path']) ?>')" title="Kopyala">📋</button>
            <a href="?delete=<?= urlencode($f['name']) ?>" class="ma-btn" style="background:var(--red);color:#fff;" onclick="event.stopPropagation();return confirm('Silinsin mi?')" title="Sil">🗑</a>
        </div>
        <div class="media-info">
            <div class="mname"><?= sTr($f['name']) ?></div>
            <div class="msize"><?= formatBytes($f['size']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($files)): ?>
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--txs);">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border);margin:0 auto 16px;display:block"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Henüz görsel yüklenmemiş.
    </div>
    <?php endif; ?>
</div>

<div class="copy-toast" id="copyToast">✓ URL Kopyalandı!</div>

<script>
function copyUrl(url){
    navigator.clipboard.writeText(window.location.origin + url).then(function(){
        var t = document.getElementById('copyToast');
        t.style.display='block';
        setTimeout(function(){t.style.display='none'},2000);
    });
}
var dz = document.getElementById('dropZone');
dz.addEventListener('dragover',function(e){e.preventDefault();this.classList.add('dragover')});
dz.addEventListener('dragleave',function(){this.classList.remove('dragover')});
dz.addEventListener('drop',function(e){
    e.preventDefault();
    this.classList.remove('dragover');
    var fi = document.getElementById('fileIn');
    fi.files = e.dataTransfer.files;
    document.getElementById('uploadForm').submit();
});
</script>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
