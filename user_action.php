<?php
session_start();
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

if (empty($_SESSION['uye'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'Giriş yapmanız gerekiyor.','redirect'=>'/uye-paneli']);
    exit;
}

$userId = (int)$_SESSION['uye']['id'];
$input  = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $input['action'] ?? '';
$newsId = (int)($input['news_id'] ?? 0);

switch ($action) {
    case 'toggle_favorite':
        if (!$newsId) { echo json_encode(['ok'=>false,'msg'=>'Haber bulunamadı.']); exit; }
        $result = toggleFavorite($userId, $newsId);
        echo json_encode(['ok'=>true,'action'=>$result,'msg'=>$result==='added'?'Favorilere eklendi':'Favorilerden çıkarıldı']);
        break;

    case 'toggle_saved':
        if (!$newsId) { echo json_encode(['ok'=>false,'msg'=>'Haber bulunamadı.']); exit; }
        $result = toggleSaved($userId, $newsId);
        echo json_encode(['ok'=>true,'action'=>$result,'msg'=>$result==='added'?'Kaydedildi':'Kayıt kaldırıldı']);
        break;

    case 'mark_read':
        markNotificationsRead($userId);
        echo json_encode(['ok'=>true]);
        break;

    case 'remove_favorite':
        if (!$newsId) { echo json_encode(['ok'=>false]); exit; }
        getPDO()->prepare("DELETE FROM user_favorites WHERE user_id=? AND news_id=?")->execute([$userId, $newsId]);
        echo json_encode(['ok'=>true,'msg'=>'Favorilerden kaldırıldı']);
        break;

    case 'remove_saved':
        if (!$newsId) { echo json_encode(['ok'=>false]); exit; }
        getPDO()->prepare("DELETE FROM user_saved WHERE user_id=? AND news_id=?")->execute([$userId, $newsId]);
        echo json_encode(['ok'=>true,'msg'=>'Kaydedilenlerden kaldırıldı']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok'=>false,'msg'=>'Geçersiz işlem.']);
}
