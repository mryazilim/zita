<?php
/**
 * Zita Projesi - Token Doğrulama API
 * 
 * JWT token doğrulama işlemlerini yönetir
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS başlıkları
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// OPTIONS isteği için yanıt
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Sadece POST istekleri kabul edilir'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Gelen JSON verisini al
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Veri doğrulama
    if (!$data || !isset($data['token'])) {
        throw new Exception('Token gereklidir');
    }

    // Token'ı decode et
    $tokenData = json_decode(base64_decode($data['token']), true);

    if (!$tokenData) {
        throw new Exception('Geçersiz token formatı');
    }

    // Token süresi kontrolü
    if (isset($tokenData['exp']) && $tokenData['exp'] < time()) {
        throw new Exception('Token süresi dolmuş');
    }

    // Kullanıcı ID kontrolü
    if (!isset($tokenData['user_id'])) {
        throw new Exception('Token kullanıcı bilgisi içermiyor');
    }

    // Veritabanı bağlantısını dahil et
    require_once __DIR__ . '/../../ortak/veritabani/baglanti.php';
    $db = veritabani_baglanti();

    // Kullanıcıyı veritabanından kontrol et
    $kullanici = $db->get_row("
        SELECT id, kullanici_adi, email, ad_soyad, yetki_seviyesi, aktif
        FROM sistem_kullanicilari 
        WHERE id = ? AND aktif = 1
    ", [$tokenData['user_id']]);

    if (!$kullanici) {
        throw new Exception('Kullanıcı bulunamadı veya pasif');
    }

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Token geçerli',
        'data' => [
            'user' => [
                'id' => $kullanici['id'],
                'kullanici_adi' => $kullanici['kullanici_adi'],
                'email' => $kullanici['email'],
                'ad_soyad' => $kullanici['ad_soyad'],
                'yetki_seviyesi' => $kullanici['yetki_seviyesi']
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Hata yanıtı
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
