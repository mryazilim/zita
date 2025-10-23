<?php
/**
 * Zita Projesi - İlçeler API
 * 
 * Bu dosya belirli bir ile ait ilçe verilerini JSON formatında döndürür.
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
    // İl ID'sini al
    $il_id = isset($_GET['il_id']) ? (int)$_GET['il_id'] : 0;
    
    if ($il_id <= 0) {
        throw new Exception('Geçerli bir il ID\'si gerekli');
    }
    
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // İlçeleri getir
    $ilceler = $db->get_results("
        SELECT id, ilce_adi, il_id 
        FROM ilceler 
        WHERE il_id = ? 
        ORDER BY ilce_adi ASC
    ", [$il_id]);
    
    // Object'leri array'e çevir
    $ilceler_array = [];
    foreach ($ilceler as $ilce) {
        $ilceler_array[] = [
            'id' => $ilce->id,
            'ilce_adi' => $ilce->ilce_adi,
            'il_id' => $ilce->il_id
        ];
    }
    
    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'data' => $ilceler_array,
        'count' => count($ilceler_array),
        'il_id' => $il_id,
        'message' => 'İlçeler başarıyla getirildi'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Hata yanıtı
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'İlçeler getirilirken hata oluştu'
    ], JSON_UNESCAPED_UNICODE);
}
?>
