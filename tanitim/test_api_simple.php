<?php
/**
 * Zita Projesi - Basit API Test
 * 
 * API'yi basit şekilde test etmek için script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita Basit API Test Scripti\n\n";

// Test verileri
$testData = [
    'vknTc' => '1234567891',
    'vergiDairesi' => 'Test Vergi Dairesi 2',
    'firmaAdi' => 'Test Firma 2 A.Ş.',
    'unvan' => 'Test Firma 2 Anonim Şirketi',
    'telefon' => '05551234568',
    'email' => 'test2@firma.com',
    'adres' => 'Test Adres 2, Test Mahallesi, Test Sokak No:2',
    'il_id' => '1',
    'ilce_id' => '1',
    'isimSoyisim' => 'Test Kullanıcı 2',
    'sifre' => 'test123',
    'sifreTekrar' => 'test123',
    'sektor_id' => '1'
];

echo "📋 Test Verileri:\n";
foreach ($testData as $key => $value) {
    echo "   - $key: $value\n";
}
echo "\n";

// API'yi direkt olarak çağır
echo "🔍 API'yi direkt çağırıyorum...\n";

// POST verisini simüle et
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// JSON verisini simüle et
$jsonData = json_encode($testData);

// API'yi dahil et ve çalıştır
ob_start();
include 'api/firma_kayit.php';
$output = ob_get_clean();

echo "✅ API Çıktısı:\n";
echo $output . "\n";

$response = json_decode($output, true);
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
    echo "   - Ham yanıt: " . substr($output, 0, 500) . "...\n";
}

echo "\n✅ Test tamamlandı!\n";
?>
