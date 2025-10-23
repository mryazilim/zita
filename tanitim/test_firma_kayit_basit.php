<?php
/**
 * Zita Projesi - Basit Firma Kayıt Test
 * 
 * Firma kayıt API'sini basit şekilde test etmek için script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita Basit Firma Kayıt Test Scripti\n\n";

// Test verileri
$testData = [
    'vknTc' => '1234567890',
    'vergiDairesi' => 'Test Vergi Dairesi',
    'firmaAdi' => 'Test Firma A.Ş.',
    'unvan' => 'Test Firma Anonim Şirketi',
    'telefon' => '05551234567',
    'email' => 'test@firma.com',
    'adres' => 'Test Adres, Test Mahallesi, Test Sokak No:1',
    'il_id' => '1',
    'ilce_id' => '1',
    'isimSoyisim' => 'Test Kullanıcı',
    'sifre' => 'test123',
    'sifreTekrar' => 'test123',
    'sektor_id' => '1'
];

echo "📋 Test Verileri:\n";
foreach ($testData as $key => $value) {
    echo "   - $key: $value\n";
}
echo "\n";

// API'ye POST isteği gönder
$url = 'http://localhost/projeler/zita/tanitim/api/firma_kayit.php';
$data = json_encode($testData);

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => $data
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "❌ API'ye bağlanılamadı!\n";
    echo "   - URL: $url\n";
    echo "   - Hata: " . error_get_last()['message'] . "\n";
} else {
    echo "✅ API Yanıtı:\n";
    echo $result . "\n";
    
    $response = json_decode($result, true);
    if ($response && isset($response['success'])) {
        if ($response['success']) {
            echo "🎉 Kayıt başarılı!\n";
            echo "   - Firma ID: " . ($response['data']['firma_id'] ?? 'Bilinmiyor') . "\n";
            echo "   - Kullanıcı ID: " . ($response['data']['kullanici_id'] ?? 'Bilinmiyor') . "\n";
        } else {
            echo "❌ Kayıt başarısız!\n";
            echo "   - Hata: " . ($response['message'] ?? 'Bilinmeyen hata') . "\n";
        }
    } else {
        echo "❌ JSON parse hatası!\n";
        echo "   - Ham yanıt: " . substr($result, 0, 500) . "...\n";
    }
}

echo "\n✅ Test tamamlandı!\n";
?>
