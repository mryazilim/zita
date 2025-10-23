<?php
/**
 * nsql v1.4 ile Firma Kayıt Testi
 * 
 * Bu dosya nsql v1.4 kütüphanesi ile firma kayıt API'sini test eder.
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // Test verileri
    $testData = [
        'vknTc' => '123456789' . rand(0, 9), // Benzersiz VKN
        'vergiDairesi' => 'Test Vergi Dairesi',
        'firmaAdi' => 'Test Firma ' . date('Y-m-d H:i:s'),
        'unvan' => 'Test Firma Ltd. Şti.',
        'telefon' => '05551234567',
        'email' => 'test' . rand(1000, 9999) . '@test.com', // Benzersiz email
        'adres' => 'Test Adres, Test Mahallesi',
        'isimSoyisim' => 'Test Kullanıcı',
        'il' => '1', // Adana
        'ilce' => '1', // Seyhan
        'sifre' => 'test123',
        'sifreTekrar' => 'test123',
        'sektor' => '1' // İlk sektör
    ];
    
    echo "<h1>🧪 nsql v1.4 Firma Kayıt Testi</h1>";
    echo "<h2>📋 Test Verileri:</h2>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
    // API'yi test et
    $url = 'http://localhost/projeler/zita/tanitim/api/firma_kayit.php';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($testData))
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<h2>🌐 API Yanıtı:</h2>";
    echo "<p><strong>HTTP Kodu:</strong> " . $httpCode . "</p>";
    
    if ($error) {
        echo "<p><strong>CURL Hatası:</strong> " . $error . "</p>";
    }
    
    echo "<p><strong>Ham Yanıt:</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // JSON parse et
    $jsonResponse = json_decode($response, true);
    
    if ($jsonResponse) {
        echo "<p><strong>JSON Yanıtı:</strong></p>";
        echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        if (isset($jsonResponse['success']) && $jsonResponse['success']) {
            echo "<h2>✅ Test Başarılı!</h2>";
            echo "<p>Firma ID: " . ($jsonResponse['firma_id'] ?? 'N/A') . "</p>";
            echo "<p>Kullanıcı ID: " . ($jsonResponse['kullanici_id'] ?? 'N/A') . "</p>";
        } else {
            echo "<h2>❌ Test Başarısız!</h2>";
            echo "<p>Hata: " . ($jsonResponse['error'] ?? 'Bilinmeyen hata') . "</p>";
        }
    } else {
        echo "<h2>❌ JSON Parse Hatası!</h2>";
        echo "<p>Yanıt geçerli JSON formatında değil.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Test Hatası:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
