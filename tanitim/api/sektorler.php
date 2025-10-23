<?php
/**
 * Zita Projesi - Sektörler API
 * 
 * Bu dosya sektör verilerini JSON formatında döndürür.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// CORS başlıklarını ayarla
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS isteği için
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../../ortak/veritabani/baglanti.php';

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Sektörleri getir
    $sektorler = $db->get_results("
        SELECT id, sektor_adi 
        FROM sektorler 
        ORDER BY sektor_adi ASC
    ");
    
    // Object'leri array'e çevir
    $sektorler_array = [];
    foreach ($sektorler as $sektor) {
        $sektorler_array[] = [
            'id' => $sektor->id,
            'sektor_adi' => $sektor->sektor_adi
        ];
    }
    
    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'data' => $sektorler_array,
        'count' => count($sektorler_array),
        'message' => 'Sektörler başarıyla getirildi'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Hata yanıtı
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Sektörler getirilirken hata oluştu'
    ], JSON_UNESCAPED_UNICODE);
}
?>
