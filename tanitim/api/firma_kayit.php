<?php
/**
 * Zita Projesi - Firma Kayıt API
 * 
 * Bu dosya firma kayıt formundan gelen verileri işler.
 * Firma ve kullanıcı verilerini veritabanına kaydeder.
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
    // Sadece POST isteklerini kabul et
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Sadece POST istekleri kabul edilir');
    }
    
    // JSON verisini al
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Geçersiz JSON verisi');
    }
    
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Veri doğrulama
    $validation = validateFirmaData($data);
    if (!$validation['valid']) {
        throw new Exception($validation['message']);
    }
    
    try {
        // 1. Firma verilerini hazırla
        $firmaData = [
            'vkn_tc' => $data['vknTc'],
            'vergi_dairesi' => $data['vergiDairesi'],
            'firma_adi' => $data['firmaAdi'],
            'unvan' => $data['unvan'],
            'telefon' => $data['telefon'],
            'email' => $data['email'],
            'adres' => $data['adres'],
            'il_id' => $data['il'],
            'ilce_id' => $data['ilce'],
            'sektor_id' => $data['sektor']
        ];
        
        // 2. Firma kaydını oluştur
        $firmaSql = "INSERT INTO firmalar (vkn_tc, vergi_dairesi, firma_adi, unvan, telefon, email, adres, il_id, ilce_id, sektor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $firmaParams = [
            $firmaData['vkn_tc'],
            $firmaData['vergi_dairesi'],
            $firmaData['firma_adi'],
            $firmaData['unvan'],
            $firmaData['telefon'],
            $firmaData['email'],
            $firmaData['adres'],
            $firmaData['il_id'],
            $firmaData['ilce_id'],
            $firmaData['sektor_id']
        ];
        
        $insertResult = $db->insert($firmaSql, $firmaParams);
        
        if (!$insertResult) {
            throw new Exception('Firma kaydı oluşturulamadı');
        }
        
        // Insert ID'yi al
        $firmaId = $db->insert_id();
        
        // 3. Kullanıcı verilerini hazırla
        $kullaniciData = [
            'firma_id' => $firmaId,
            'kullanici_adi' => $data['email'], // E-posta kullanıcı adı olarak kullanılır
            'email' => $data['email'],
            'sifre' => password_hash($data['sifre'], PASSWORD_DEFAULT),
            'ad_soyad' => $data['isimSoyisim'],
            'telefon' => $data['telefon'],
            'yetki_seviyesi' => 'admin' // İlk kullanıcı admin olur
        ];
        
        // 4. Kullanıcı kaydını oluştur
        $kullaniciSql = "INSERT INTO firma_kullanicilari (firma_id, kullanici_adi, email, sifre, ad_soyad, telefon, yetki_seviyesi) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $kullaniciParams = [
            $kullaniciData['firma_id'],
            $kullaniciData['kullanici_adi'],
            $kullaniciData['email'],
            $kullaniciData['sifre'],
            $kullaniciData['ad_soyad'],
            $kullaniciData['telefon'],
            $kullaniciData['yetki_seviyesi']
        ];
        
        $kullaniciInsertResult = $db->insert($kullaniciSql, $kullaniciParams);
        
        if (!$kullaniciInsertResult) {
            throw new Exception('Kullanıcı kaydı oluşturulamadı');
        }
        
        // Kullanıcı ID'yi al
        $kullaniciId = $db->insert_id();
        
        // 5. Log kaydı oluştur (şimdilik log tablosunu atla)
        // $logData = [
        //     'kullanici_id' => $kullaniciId,
        //     'kullanici_tipi' => 'firma',
        //     'islem' => 'Firma Kayıt',
        //     'detay' => 'Yeni firma kaydı oluşturuldu: ' . $data['firmaAdi'],
        //     'ip_adresi' => $_SERVER['REMOTE_ADDR'] ?? 'Bilinmiyor',
        //     'tarayici' => $_SERVER['HTTP_USER_AGENT'] ?? 'Bilinmiyor'
        // ];
        
        // $db->insert('log_kayitlari', $logData);
        
        // Başarılı yanıt
        echo json_encode([
            'success' => true,
            'message' => 'Firma kaydı başarıyla oluşturuldu',
            'data' => [
                'firma_id' => $firmaId,
                'kullanici_id' => $kullaniciId,
                'firma_adi' => $data['firmaAdi'],
                'email' => $data['email']
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        throw $e;
    }
    
} catch (Exception $e) {
    // Hata yanıtı
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Firma kaydı oluşturulamadı'
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Firma verilerini doğrula
 */
function validateFirmaData($data) {
    $requiredFields = [
        'vknTc', 'vergiDairesi', 'firmaAdi', 'unvan', 
        'telefon', 'email', 'adres', 'isimSoyisim', 
        'il', 'ilce', 'sektor', 'sifre'
    ];
    
    // Gerekli alanları kontrol et
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            return [
                'valid' => false,
                'message' => ucfirst($field) . ' alanı zorunludur'
            ];
        }
    }
    
    // VKN/TC format kontrolü
    if (!preg_match('/^[0-9]{10,11}$/', $data['vknTc'])) {
        return [
            'valid' => false,
            'message' => 'VKN/TC 10-11 haneli olmalıdır'
        ];
    }
    
    // E-posta format kontrolü
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'message' => 'Geçerli bir e-posta adresi giriniz'
        ];
    }
    
    // Telefon format kontrolü
    if (!preg_match('/^[0-9]{10,11}$/', $data['telefon'])) {
        return [
            'valid' => false,
            'message' => 'Telefon numarası 10-11 haneli olmalıdır'
        ];
    }
    
    // Şifre uzunluk kontrolü
    if (strlen($data['sifre']) < 6) {
        return [
            'valid' => false,
            'message' => 'Şifre en az 6 karakter olmalıdır'
        ];
    }
    
    return ['valid' => true];
}
?>
