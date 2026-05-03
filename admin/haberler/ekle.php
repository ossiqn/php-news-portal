<?php
require_once __DIR__ . '/../config.php';
adminAuth();
require_once ROOT_DIR . '/functions.php';

$pdo    = getAdminPDO();
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$newsId = $isEdit ? (int)$_GET['id'] : 0;
$n      = [];
$error  = $success = '';

if ($isEdit && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id=?");
    $stmt->execute([$newsId]);
    $n = $stmt->fetch() ?: [];
    if (!$n) { header('Location: ' . BASE_PATH . '/admin/haberler/'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $title       = trim($_POST['title'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $subtitle    = trim($_POST['subtitle'] ?? '');
    $red_title   = trim($_POST['red_title'] ?? '');
    $summary     = trim($_POST['summary'] ?? '');
    $content     = $_POST['content'] ?? '';
    $catId       = (int)($_POST['category_id'] ?? 0);
    $image       = trim($_POST['image'] ?? '');
    $image_big   = trim($_POST['image_big'] ?? '');
    $video_embed = trim($_POST['video_embed'] ?? '');
    $status      = (int)($_POST['status'] ?? 1);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $is_spot     = isset($_POST['is_spot']) ? 1 : 0;
    $is_flash    = isset($_POST['is_flash']) ? 1 : 0;
    $is_gundem   = isset($_POST['is_gundem']) ? 1 : 0;
    $comments_on = isset($_POST['comments_on']) ? 1 : 0;
    $show_home   = isset($_POST['show_home']) ? 1 : 0;
    $news_type   = trim($_POST['news_type'] ?? 'haber');
    $city        = trim($_POST['city'] ?? '');
    $keywords    = trim($_POST['keywords'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $meta_tag    = trim($_POST['meta_tag'] ?? '');
    $views_set   = max(0,(int)($_POST['views_set'] ?? 0));
    $link        = trim($_POST['link'] ?? '');
    $pubDate     = trim($_POST['published_at'] ?? date('Y-m-d H:i:s'));

    if (!$title) {
        $error = 'Başlık zorunludur.';
    } else {
        if (!$slug) {
            $slug = mb_strtolower($title);
            $slug = str_replace(['ş','ı','ğ','ü','ö','ç','Ş','İ','Ğ','Ü','Ö','Ç'],['s','i','g','u','o','c','s','i','g','u','o','c'], $slug);
            $slug = preg_replace('/[^a-z0-9\-]/','-', $slug);
            $slug = trim(preg_replace('/-+/','-',$slug),'-');
        }

        try {
            if ($isEdit) {
                $pdo->prepare("UPDATE news SET title=?,slug=?,summary=?,content=?,category_id=?,image=?,status=?,is_featured=?,is_breaking=?,author=?,tags=?,published_at=?,updated_at=NOW(),views=IF(?=0,views,?) WHERE id=?")
                    ->execute([$title,$slug,$summary,$content,$catId?:null,$image,$status,$is_featured,$is_breaking,$author,$keywords,$pubDate,$views_set,$views_set,$newsId]);
                logAction('Haber Düzenlendi.', $title);
                $success = 'Haber başarıyla güncellendi.';
                $n = array_merge($n, ['title'=>$title,'slug'=>$slug,'subtitle'=>$subtitle,'red_title'=>$red_title,'summary'=>$summary,'content'=>$content,'category_id'=>$catId,'image'=>$image,'image_big'=>$image_big,'video_embed'=>$video_embed,'status'=>$status,'is_featured'=>$is_featured,'is_breaking'=>$is_breaking,'is_spot'=>$is_spot,'is_flash'=>$is_flash,'is_gundem'=>$is_gundem,'comments_on'=>$comments_on,'show_home'=>$show_home,'news_type'=>$news_type,'city'=>$city,'tags'=>$keywords,'author'=>$author,'meta_tag'=>$meta_tag,'link'=>$link,'published_at'=>$pubDate]);
            } else {
                $pdo->prepare("INSERT INTO news (title,slug,summary,content,category_id,image,status,is_featured,is_breaking,author,tags,views,published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
                    ->execute([$title,$slug,$summary,$content,$catId?:null,$image,$status,$is_featured,$is_breaking,$author,$keywords,$views_set,$pubDate]);
                $newId = (int)$pdo->lastInsertId();
                logAction('Haber Eklendi.', $title);
                header('Location: ' . BASE_PATH . '/admin/haberler/ekle.php?id='.$newId.'&saved=1');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Hata: '.$e->getMessage();
        }
    }
}

$cats = getAllCategories();
$pageTitle = $isEdit ? 'Haber Düzenle' : 'Haber Ekle';
$breadcrumbs = [['label'=>'Haberler','url'=>'/admin/haberler/'],['label'=>$pageTitle]];

require __DIR__ . '/../includes/layout.php';
?>

<style>
.news-form{display:grid;grid-template-columns:1fr 310px;gap:18px;align-items:start}
.news-main{}
.news-side{position:sticky;top:50px}
.side-box{background:#fff;border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;margin-bottom:14px}
.sb-head{padding:10px 15px;background:#f3f5f8;border-bottom:1px solid var(--border);font-size:12px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:7px}
.sb-head svg{width:13px;height:13px;color:var(--acc)}
.sb-body{padding:13px 15px}
.pub-btn{width:100%;padding:11px;border-radius:var(--r);font-size:13px;font-weight:700;cursor:pointer;border:none;font-family:var(--fn);transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:8px}
.pub-btn svg{width:14px;height:14px}
.field-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border)}
.field-row:last-child{border-bottom:none}
.field-label{font-size:12px;font-weight:600;color:var(--txm)}
.img-preview-box{width:100%;aspect-ratio:16/9;background:#f0f2f5;border-radius:4px;overflow:hidden;border:1px solid var(--border);margin-top:8px;display:flex;align-items:center;justify-content:center;color:var(--txs);font-size:11px}
.img-preview-box img{width:100%;height:100%;object-fit:cover}
.img-input{font-size:11px;margin-top:8px}
.wysiwyg{border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden}
.wysiwyg-bar{display:flex;gap:0;background:#f3f5f8;border-bottom:1px solid var(--border);padding:6px 8px;flex-wrap:wrap;gap:2px}
.wy-btn{background:none;border:none;width:28px;height:26px;display:inline-flex;align-items:center;justify-content:center;border-radius:3px;cursor:pointer;font-size:13px;color:var(--txm);font-family:var(--fn);font-weight:700;transition:all .15s}
.wy-btn:hover{background:var(--border);color:var(--tx)}
.wy-btn.active{background:var(--acc);color:#fff}
.wy-sep{width:1px;background:var(--border);margin:0 3px;height:20px;align-self:center;flex-shrink:0}
.wy-area{min-height:300px;padding:14px;outline:none;font-size:14px;line-height:1.7;color:var(--tx);font-family:var(--fn)}
.wy-footer{padding:6px 10px;background:#f8f9fb;border-top:1px solid var(--border);font-size:11px;color:var(--txs);text-align:right}
.section-title{font-size:13px;font-weight:800;color:var(--c1);margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid var(--border);display:flex;align-items:center;gap:7px}
.section-title svg{width:14px;height:14px;color:var(--acc)}
@media(max-width:100%){.news-form{grid-template-columns:1fr}.news-side{position:static}}
</style>

<?php if (isset($_GET['saved'])): ?><div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Haber başarıyla eklendi!</div><?php endif; ?>
<?php if ($success): ?><div class="alert al-success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= sTr($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert al-error"><?= sTr($error) ?></div><?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;background:var(--c1);padding:10px 16px;border-radius:var(--r);margin-bottom:18px;">
    <h1 style="font-size:15px;font-weight:800;color:#fff;letter-spacing:.04em;"><?= strtoupper($pageTitle) ?></h1>
    <div style="display:flex;gap:8px;">
        <a href="<?= BASE_PATH ?>/admin/haberler/" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Geri Dön
        </a>
        <button type="submit" form="newsForm" class="btn btn-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Kaydet
        </button>
    </div>
</div>

<form method="post" id="newsForm" enctype="multipart/form-data">
<div class="news-form">

<div class="news-main">

    <div class="card">
        <div class="card-body">
            <div class="fg">
                <label>Kategori</label>
                <select name="category_id" style="background:#fff;">
                    <option value="0">---Kategori Seçiniz---</option>
                    <?php foreach ($cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($n['category_id']??0)==(int)$cat['id']?'selected':'' ?>><?= sTr($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fg" style="margin-bottom:10px;">
                <label>Haber Yeri</label>
                <div class="cb-grid">
                    <label class="cb-item"><input type="checkbox" name="is_featured" <?= ($n['is_featured']??0)?'checked':'' ?>><span>Manşet</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_dikey"><span>Dikey</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_spot" <?= ($n['is_spot']??0)?'checked':'' ?>><span>Spot</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_hikaye"><span>Hikayeler</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_dev_manset"><span>Dev Manşet</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_yatay"><span>Yatay</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_flash" <?= ($n['is_flash']??0)?'checked':'' ?>><span>Flash</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_gundem" <?= ($n['is_gundem']??0)?'checked':'' ?>><span>Gündem</span></label>
                    <label class="cb-item"><input type="checkbox" name="is_breaking" <?= ($n['is_breaking']??0)?'checked':'' ?>><span>Son Dakika</span></label>
                </div>
            </div>

            <div class="fg">
                <label style="display:flex;align-items:center;justify-content:space-between;">
                    Başlık
                    <label style="display:flex;align-items:center;gap:5px;text-transform:none;font-size:12px;cursor:pointer;color:var(--txm);">
                        <input type="checkbox" name="detail_title_same" checked style="accent-color:var(--acc);"> Detay başlığıda olsun
                    </label>
                </label>
                <input type="text" name="title" id="titleIn" value="<?= sTr($n['title']??'') ?>" placeholder="Haber başlığını giriniz..." required oninput="genSlug(this.value)">
                <input type="text" name="slug" id="slugIn" value="<?= sTr($n['slug']??'') ?>" placeholder="haber-basligi-gibi" style="margin-top:6px;font-size:12px;color:var(--txs);" oninput="document.getElementById('slugPrev').textContent=this.value">
                <div class="hint">/haber/<strong id="slugPrev"><?= sTr($n['slug']??'') ?></strong></div>
            </div>

            <div class="fg">
                <label>Alt Başlık</label>
                <input type="text" name="subtitle" value="<?= sTr($n['subtitle']??'') ?>" placeholder="Fotoğraf üstünde görünecek olan başlık. Boş bırakırsanız görünmez.">
                <div class="hint">Fotoğraf üstünde görünecek olan başlık. Boş bırakırsanız görünmez.</div>
            </div>

            <div class="fg">
                <label>Kırmızı Başlık</label>
                <input type="text" name="red_title" value="<?= sTr($n['red_title']??'') ?>" placeholder="Manşet Alanında görünecek başlık. Boş bırakırsanız görünmez.">
                <div class="hint">Manşet Alanında görünecek olan başlıktır. Boş bırakırsanız görünmez.</div>
            </div>

            <div class="fg">
                <label>Özet</label>
                <textarea name="summary" rows="4" placeholder="Haberin kısa özeti (liste görünümü ve SEO için)..."><?= sTr($n['summary']??'') ?></textarea>
            </div>

            <div class="fg">
                <label>Detay</label>
                <div class="wysiwyg">
                    <div class="wysiwyg-bar">
                        <button type="button" class="wy-btn" title="Kaynak" onclick="toggleSource()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        </button>
                        <div class="wy-sep"></div>
                        <button type="button" class="wy-btn" onclick="fmt('bold')" title="Kalın"><b>B</b></button>
                        <button type="button" class="wy-btn" onclick="fmt('italic')" title="İtalik"><i>I</i></button>
                        <button type="button" class="wy-btn" onclick="fmt('strikeThrough')" title="Üstü Çizili"><s>S</s></button>
                        <div class="wy-sep"></div>
                        <button type="button" class="wy-btn" onclick="fmt('justifyLeft')" title="Sola Hizala">◀</button>
                        <button type="button" class="wy-btn" onclick="fmt('justifyCenter')" title="Ortala">▶◀</button>
                        <button type="button" class="wy-btn" onclick="fmt('justifyRight')" title="Sağa Hizala">▶</button>
                        <button type="button" class="wy-btn" onclick="fmt('justifyFull')" title="İki Yana Hizala">≡</button>
                        <div class="wy-sep"></div>
                        <button type="button" class="wy-btn" onclick="fmt('insertUnorderedList')" title="Madde İşareti">•</button>
                        <button type="button" class="wy-btn" onclick="fmt('insertOrderedList')" title="Numaralı Liste">1.</button>
                        <div class="wy-sep"></div>
                        <button type="button" class="wy-btn" onclick="insertLink()" title="Bağlantı">🔗</button>
                        <button type="button" class="wy-btn" onclick="insertImage()" title="Görsel">🖼</button>
                        <div class="wy-sep"></div>
                        <select onchange="fmt('formatBlock',this.value);this.value=''" style="height:26px;font-size:11px;border:1px solid var(--border);border-radius:3px;background:#fff;padding:0 4px;color:var(--tx);">
                            <option value="">Biçim</option>
                            <option value="p">Paragraf</option>
                            <option value="h2">Başlık 2</option>
                            <option value="h3">Başlık 3</option>
                            <option value="blockquote">Alıntı</option>
                        </select>
                        <select onchange="document.execCommand('fontSize',false,this.value);this.value=''" style="height:26px;font-size:11px;border:1px solid var(--border);border-radius:3px;background:#fff;padding:0 4px;color:var(--tx);margin-left:3px;">
                            <option value="">Boyut</option>
                            <?php for($i=1;$i<=7;$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div id="editor" class="wy-area" contenteditable="true" oninput="syncContent()" onkeyup="updateStats()"><?= $n['content']??'' ?></div>
                    <textarea name="content" id="contentHidden" style="display:none"><?= sTr($n['content']??'') ?></textarea>
                    <div class="wy-footer">Paragraf: <span id="wyParag">0</span>, Kelime: <span id="wyWord">0</span></div>
                </div>
            </div>

            <div class="fg">
                <label>Anahtar Kelime Üret</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="kw-gen" placeholder="Kelime yazın ve Enter'a basın..." style="flex:1;" onkeydown="if(event.key==='Enter'){event.preventDefault();addKeyword(this.value);this.value=''}">
                    <button type="button" class="btn btn-secondary" onclick="addKeyword(document.getElementById('kw-gen').value);document.getElementById('kw-gen').value=''">Ekle</button>
                </div>
            </div>
            <div class="fg">
                <label>Anahtar Kelimeler</label>
                <div id="kwTags" style="display:flex;flex-wrap:wrap;gap:6px;min-height:36px;padding:6px;border:1.5px solid var(--border);border-radius:var(--r);background:#fff;"></div>
                <input type="hidden" name="keywords" id="kwInput" value="<?= sTr($n['tags']??'') ?>">
                <div class="hint">Haber ile ilgili anahtar kelimelerdir.</div>
            </div>

            <div style="border:1px solid var(--border);border-radius:var(--r);padding:14px;margin-bottom:16px;background:#f8f9fb;">
                <div style="font-size:11px;font-weight:700;color:var(--acc);margin-bottom:10px;text-transform:uppercase;letter-spacing:.06em;">SEO Önizleme</div>
                <div style="font-size:16px;color:#1a0dab;font-weight:700;" id="seoTitle"><?= sTr($n['title']??'Site Başlığı') ?></div>
                <div style="font-size:12px;color:#006621;margin:2px 0;" id="seoUrl">https://siteadi.com/haber/<?= sTr($n['slug']??'haber-basligi') ?></div>
                <div style="font-size:13px;color:#545454;" id="seoDesc"><?= sTr(mb_substr($n['summary']??'Site açıklaması burada görünecek.',0,160)) ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Fotoğraf & Video</h2></div>
        <div class="card-body">
            <div class="fg">
                <label>Küçük Fotoğraf (641x380)</label>
                <div class="img-preview-box" id="imgSmallPreview" style="height:150px;" onclick="document.getElementById('imgSmallUrl').focus()">
                    <?php if (!empty($n['image'])): ?><img src="<?= sTr($n['image']) ?>" id="imgSmallImg" alt=""><?php else: ?><div style="text-align:center"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border);margin:0 auto"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><br>641×380</div><?php endif; ?>
                </div>
                <input type="url" name="image" id="imgSmallUrl" class="img-input" value="<?= sTr($n['image']??'') ?>" placeholder="Görsel URL girin..." oninput="prevImg('imgSmallUrl','imgSmallPreview')">
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top:6px;width:100%;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Arşivden seç...
                </button>
            </div>

            <div class="fg">
                <label>Büyük Fotoğraf (1280x720)</label>
                <div class="img-preview-box" id="imgBigPreview" style="height:200px;" onclick="document.getElementById('imgBigUrl').focus()">
                    <?php if (!empty($n['image_big'])): ?><img src="<?= sTr($n['image_big']) ?>" alt=""><?php else: ?><div style="text-align:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border);margin:0 auto"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><br>1280×720</div><?php endif; ?>
                </div>
                <input type="url" name="image_big" id="imgBigUrl" class="img-input" value="<?= sTr($n['image_big']??'') ?>" placeholder="Büyük görsel URL..." oninput="prevImg('imgBigUrl','imgBigPreview')">
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top:6px;width:100%;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Arşivden seç...
                </button>
            </div>

            <div class="fg">
                <label>Video Embed</label>
                <textarea name="video_embed" rows="3" placeholder="Embed &amp; Frame kod alanıdır. Video linki eklerseniz çalışmayacaktır."><?= sTr($n['video_embed']??'') ?></textarea>
                <label style="display:flex;align-items:center;gap:7px;margin-top:8px;cursor:pointer;font-size:12px;color:var(--txm);">
                    <input type="checkbox" name="video_show" style="accent-color:var(--acc);">
                    Haber detayda fotoğraf yerine video görünsün.
                </label>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>Diğer Ayarlar</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="fg">
                    <label>Haber Tipi</label>
                    <div class="rc-group">
                        <label class="rc-item"><input type="radio" name="news_type" value="haber" <?= ($n['news_type']??'haber')==='haber'?'checked':'' ?>><span>Haber</span></label>
                        <label class="rc-item"><input type="radio" name="news_type" value="foto" <?= ($n['news_type']??'')==='foto'?'checked':'' ?>><span>Foto Haber</span></label>
                        <label class="rc-item"><input type="radio" name="news_type" value="reklam" <?= ($n['news_type']??'')==='reklam'?'checked':'' ?>><span>Haber reklam</span></label>
                    </div>
                </div>
                <div class="fg">
                    <label>Anasayfa</label>
                    <div class="rc-group">
                        <label class="rc-item"><input type="radio" name="show_home" value="1" <?= ($n['show_home']??1)?'checked':'' ?>><span>Göster</span></label>
                        <label class="rc-item"><input type="radio" name="show_home" value="0" <?= ($n['show_home']??1)?'':'checked' ?>><span>Gösterme</span></label>
                    </div>
                    <div class="hint">Haber anasayfada hiçbir alanda görünmez.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Bant (Manşet Başlığı)</label>
                    <div class="rc-group">
                        <label class="rc-item"><input type="radio" name="band_show" value="1" checked><span>Göster</span></label>
                        <label class="rc-item"><input type="radio" name="band_show" value="0"><span>Gizle</span></label>
                    </div>
                    <div class="hint">Fotoğrafın üzerinde Haber başlığı ve bandı görünmez.</div>
                </div>
                <div class="fg">
                    <label>Yorum</label>
                    <div class="rc-group">
                        <label class="rc-item"><input type="radio" name="comments_on" value="1" <?= ($n['comments_on']??1)?'checked':'' ?>><span>Yapılsın</span></label>
                        <label class="rc-item"><input type="radio" name="comments_on" value="0" <?= ($n['comments_on']??1)?'':'checked' ?>><span>Yapılmasın</span></label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Şehir</label>
                    <select name="city">
                        <option value="">—Şehir Seçin—</option>
                        <?php global $CITIES; foreach ($CITIES as $c): ?>
                        <option value="<?= sTr($c) ?>" <?= ($n['city']??'')===$c?'selected':'' ?>><?= sTr($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fg">
                    <label>İlçe</label>
                    <input type="text" name="district" value="<?= sTr($n['district']??'') ?>" placeholder="İlçe adı...">
                </div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Durum</label>
                    <div class="rc-group">
                        <label class="rc-item"><input type="radio" name="status" value="1" <?= ($n['status']??1)?'checked':'' ?>><span>Aktif</span></label>
                        <label class="rc-item"><input type="radio" name="status" value="0" <?= ($n['status']??1)?'':'checked' ?>><span>Pasif</span></label>
                    </div>
                    <div class="hint">Haber durumunu aktif / pasif yapabilirsiniz.</div>
                </div>
                <div class="fg">
                    <label>Bildirim Gönder</label>
                    <label class="rc-item" style="margin-top:8px;"><input type="checkbox" name="send_notif" style="accent-color:var(--acc);"><span>Evet</span></label>
                </div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Okunma Sayısı</label>
                    <input type="number" name="views_set" value="<?= (int)($n['views']??0) ?>" min="0">
                    <div class="hint">Haber okunma sayısını tanımlayabilirsiniz.</div>
                </div>
                <div class="fg">
                    <label>Link</label>
                    <input type="url" name="link" value="<?= sTr($n['link']??'') ?>" placeholder="http://">
                    <div class="hint">Habere özel link. Haber linki yerine bu linke gider.</div>
                </div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Tarih</label>
                    <input type="datetime-local" name="published_at" value="<?= isset($n['published_at']) ? date('Y-m-d\TH:i',strtotime($n['published_at'])) : date('Y-m-d\TH:i') ?>">
                </div>
                <div class="fg">
                    <label>Editör</label>
                    <input type="text" name="author" value="<?= sTr($n['author']??$_adminUser) ?>" placeholder="Editör adı">
                </div>
            </div>
            <div class="fg">
                <label>Meta Tag</label>
                <textarea name="meta_tag" rows="3" placeholder="İçeriğe özel meta tag. Örnek: &lt;meta name=&quot;googlebot-news&quot; content=&quot;noindex&quot;&gt;"><?= sTr($n['meta_tag']??'') ?></textarea>
                <div class="hint">İçeriğe özel meta tag kullanabileceğiniz alandır. Meta Key alanı değildir.</div>
                <div class="hint red">Örnek: &lt;meta name="googlebot-news" content="noindex"&gt;<br>bu tag ile google news'te görünmesi engellenebilir.</div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end;padding:6px 0;">
        <a href="<?= BASE_PATH ?>/admin/haberler/" class="btn btn-secondary btn-lg">İptal</a>
        <?php if ($isEdit): ?><a href="<?= BASE_PATH ?>/haber/<?= sTr($n['slug']??'') ?>" target="_blank" class="btn btn-purple btn-lg">Haberi Gör</a><?php endif; ?>
        <button type="submit" class="btn btn-success btn-lg" style="min-width:120px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Kaydet
        </button>
    </div>

</div>

<!-- SIDE PANEL -->
<div class="news-side">
    <div class="side-box">
        <div class="sb-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Yayın</div>
        <div class="sb-body">
            <button type="submit" form="newsForm" class="pub-btn" style="background:var(--green);color:#fff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?= $isEdit ? 'Güncelle' : 'Yayınla' ?>
            </button>
            <?php if ($isEdit): ?>
            <a href="javascript:void(0)" onclick="delConfirm('/admin/haberler/?delete=<?= $newsId ?>','Bu haberi silmek istediğinize emin misiniz?')" class="pub-btn" style="background:var(--red);color:#fff;text-decoration:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Haberi Sil
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="side-box">
        <div class="sb-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>Durum & Özellikler</div>
        <div class="sb-body">
            <div class="field-row">
                <span class="field-label">Yayın Durumu</span>
                <span id="statusBadge" class="badge bg-green">Aktif</span>
            </div>
            <div class="field-row">
                <span class="field-label">Manşet</span>
                <span id="featuredBadge" class="badge <?= ($n['is_featured']??0)?'bg-blue':'bg-gray' ?>"><?= ($n['is_featured']??0)?'Evet':'Hayır' ?></span>
            </div>
            <div class="field-row">
                <span class="field-label">Son Dakika</span>
                <span id="breakingBadge" class="badge <?= ($n['is_breaking']??0)?'bg-red':'bg-gray' ?>"><?= ($n['is_breaking']??0)?'Evet':'Hayır' ?></span>
            </div>
            <?php if ($isEdit): ?>
            <div class="field-row">
                <span class="field-label">Okunma</span>
                <strong style="font-size:14px;color:var(--c1);"><?= number_format($n['views']??0) ?></strong>
            </div>
            <div class="field-row">
                <span class="field-label">Eklenme</span>
                <span style="font-size:11px;color:var(--txs);"><?= isset($n['published_at']) ? date('d.m.Y H:i',strtotime($n['published_at'])) : '—' ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="side-box">
        <div class="sb-head"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>Haber Yeri Özeti</div>
        <div class="sb-body" style="font-size:12px;color:var(--txm);line-height:1.8;">
            <div id="positionSummary">—</div>
        </div>
    </div>

    <div class="side-box">
        <div class="sb-head">Hızlı Yardım</div>
        <div class="sb-body" style="font-size:11px;color:var(--txs);line-height:1.8;">
            <strong>Manşet:</strong> Ana sayfada büyük görünür<br>
            <strong>Son Dakika:</strong> Ticker'da gösterilir<br>
            <strong>Dev Manşet:</strong> En büyük manşet alanı<br>
            <strong>Spot:</strong> Özel spot alanı<br>
            <strong>Flash:</strong> Flash haber bandı<br>
        </div>
    </div>
</div>

</div>
</form>

<script>
function genSlug(v) {
    var s = v.toLowerCase()
        .replace(/ş/g,'s').replace(/ı/g,'i').replace(/ğ/g,'g')
        .replace(/ü/g,'u').replace(/ö/g,'o').replace(/ç/g,'c')
        .replace(/[^a-z0-9\s\-]/g,'').replace(/\s+/g,'-').replace(/\-+/g,'-').replace(/^\-|\-$/g,'');
    var si = document.getElementById('slugIn');
    if(!si.dataset.manual) { si.value = s; document.getElementById('slugPrev').textContent = s; document.getElementById('seoUrl').textContent = 'https://siteadi.com/haber/'+s; }
    document.getElementById('seoTitle').textContent = v;
}
document.getElementById('slugIn').addEventListener('input',function(){ this.dataset.manual='1'; document.getElementById('slugPrev').textContent=this.value; document.getElementById('seoUrl').textContent='https://siteadi.com/haber/'+this.value; });
document.querySelector('[name=summary]').addEventListener('input',function(){ document.getElementById('seoDesc').textContent=this.value.substring(0,160); });

function fmt(cmd,val){ document.getElementById('editor').focus(); document.execCommand(cmd,false,val||null); syncContent(); }
function syncContent(){ document.getElementById('contentHidden').value = document.getElementById('editor').innerHTML; }
function updateStats(){
    var text = document.getElementById('editor').innerText;
    var words = text.trim() ? text.trim().split(/\s+/).length : 0;
    var parag = document.getElementById('editor').querySelectorAll('p,h1,h2,h3,h4').length;
    document.getElementById('wyWord').textContent = words;
    document.getElementById('wyParag').textContent = parag;
}
var srcMode = false;
function toggleSource(){
    var ed = document.getElementById('editor');
    if(!srcMode){ ed.textContent = ed.innerHTML; srcMode=true; } 
    else { ed.innerHTML = ed.textContent; srcMode=false; }
}
function insertLink(){
    var url = prompt('Bağlantı URL:');
    if(url) fmt('createLink',url);
}
function insertImage(){
    var url = prompt('Görsel URL:');
    if(url) fmt('insertImage',url);
}
function prevImg(inputId, previewId){
    var url = document.getElementById(inputId).value;
    var box = document.getElementById(previewId);
    if(url){ box.innerHTML = '<img src="'+url+'" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display=\'none\'">'; }
}

var kwArr = <?= json_encode(array_filter(explode(',', $n['tags']??''))) ?>;
function renderKw(){
    var box = document.getElementById('kwTags');
    box.innerHTML = kwArr.map(function(k){ return '<span style="display:inline-flex;align-items:center;gap:4px;background:var(--acc);color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:700;">'+k+'<span onclick="removeKw(\''+k+'\')" style="cursor:pointer;opacity:.7;font-size:14px;line-height:1;">×</span></span>'; }).join('');
    document.getElementById('kwInput').value = kwArr.join(',');
}
function addKeyword(k){ k=k.trim(); if(k&&!kwArr.includes(k)){ kwArr.push(k); renderKw(); } }
function removeKw(k){ kwArr=kwArr.filter(function(x){return x!==k;}); renderKw(); }
renderKw();

function updatePositionSummary(){
    var positions = [];
    document.querySelectorAll('.cb-item input:checked').forEach(function(cb){ positions.push(cb.closest('.cb-item').querySelector('span').textContent); });
    document.getElementById('positionSummary').textContent = positions.length ? positions.join(', ') : '—';
}
document.querySelectorAll('.cb-item input').forEach(function(cb){ cb.addEventListener('change', updatePositionSummary); });
updatePositionSummary();
</script>

<?php require __DIR__ . '/../includes/layout_end.php'; ?>
