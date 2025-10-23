<?php
/**
 * Zita Projesi - Giriş API Endpoint
 * 
 * Kullanıcı giriş işlemlerini yönetir
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

// PDO bağlantısı (nsql yerine)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=zita_vt;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Gelen JSON verisini al
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Veri doğrulama
    if (!$data) {
        throw new Exception('Geçersiz JSON verisi');
    }

    // Gerekli alanları kontrol et
    $requiredFields = ['kullanici_adi', 'sifre'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception('Tüm alanlar gereklidir');
        }
    }

    // Kullanıcı adı veya e-posta ile kullanıcıyı bul
    $stmt = $pdo->prepare("
        SELECT id, kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi, aktif, son_giris_tarihi
        FROM sistem_kullanicilari 
        WHERE (kullanici_adi = ? OR email = ?) AND aktif = 1
    ");
    $stmt->execute([$data['kullanici_adi'], $data['kullanici_adi']]);
    $kullanici = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$kullanici) {
        throw new Exception('Kullanıcı adı veya şifre hatalı');
    }

    // Şifre doğrulama
    if (!password_verify($data['sifre'], $kullanici->sifre)) {
        throw new Exception('Kullanıcı adı veya şifre hatalı');
    }

    // Session başlat
    session_start();

    // Giriş başarılı - son giriş tarihini güncelle
    $stmt = $pdo->prepare("
        UPDATE sistem_kullanicilari 
        SET son_giris_tarihi = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$kullanici->id]);

    // Session verilerini oluştur
    $_SESSION['kullanici_id'] = $kullanici->id;
    $_SESSION['firma_id'] = 1; // Sistem kullanıcısı için varsayılan firma ID
    $_SESSION['kullanici_adi'] = $kullanici->kullanici_adi;
    $_SESSION['email'] = $kullanici->email;
    $_SESSION['ad_soyad'] = $kullanici->ad_soyad;
    $_SESSION['yetki_seviyesi'] = $kullanici->yetki_seviyesi;
    $_SESSION['giris_zamani'] = time();

    // JWT token oluştur (basit token)
    $tokenData = [
        'user_id' => $kullanici->id,
        'kullanici_adi' => $kullanici->kullanici_adi,
        'email' => $kullanici->email,
        'yetki_seviyesi' => $kullanici->yetki_seviyesi,
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 saat
    ];

    $token = base64_encode(json_encode($tokenData));

    // Log kaydı oluştur
    $logData = [
        'kullanici_id' => $kullanici->id,
        'kullanici_tipi' => 'sistem',
        'islem' => 'Giriş',
        'detay' => 'Sistem girişi yapıldı',
        'ip_adresi' => $_SERVER['REMOTE_ADDR'] ?? 'Bilinmiyor',
        'tarayici' => $_SERVER['HTTP_USER_AGENT'] ?? 'Bilinmiyor'
    ];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO log_kayitlari 
            (kullanici_id, kullanici_tipi, islem, detay, ip_adresi, tarayici, tarih) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $logData['kullanici_id'],
            $logData['kullanici_tipi'],
            $logData['islem'],
            $logData['detay'],
            $logData['ip_adresi'],
            $logData['tarayici']
        ]);
    } catch (Exception $e) {
        // Log hatası kritik değil, devam et
        error_log("Log kaydı oluşturulamadı: " . $e->getMessage());
    }

    // Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Giriş başarılı',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $kullanici->id,
                'kullanici_adi' => $kullanici->kullanici_adi,
                'email' => $kullanici->email,
                'ad_soyad' => $kullanici->ad_soyad,
                'yetki_seviyesi' => $kullanici->yetki_seviyesi
            ],
            'redirect' => 'index.php'
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Hata yanıtı
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
