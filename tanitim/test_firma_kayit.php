<?php
/**
 * Zita Projesi - Firma Kayıt API Test Scripti
 * 
 * Bu script firma kayıt API'sini test eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Test verisi
$testData = [
    'vknTc' => '1111111111',
    'vergiDairesi' => 'Test Vergi Dairesi',
    'firmaAdi' => 'Test Firma A.Ş.',
    'unvan' => 'Test Firma Anonim Şirketi',
    'telefon' => '05551234567',
    'email' => 'test3@firma.com',
    'adres' => 'Test Adres Mahallesi Test Sokak No:1',
    'isimSoyisim' => 'Test Kullanıcı',
    'il' => '6', // Ankara
    'ilce' => '1', // Altındağ
    'sektor' => '1', // Teknoloji ve Yazılım
    'sifre' => '123456'
];

// cURL ile API'yi test et
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/projeler/zita/tanitim/api/firma_kayit.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($testData))
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "🧪 Firma Kayıt API Test Ediliyor...\n\n";
echo "📤 Gönderilen Veri:\n";
echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "📥 API Yanıtı (HTTP {$httpCode}):\n";
echo $response . "\n\n";

$result = json_decode($response, true);

if ($result && $result['success']) {
    echo "✅ Test Başarılı!\n";
    echo "   - Firma ID: " . $result['data']['firma_id'] . "\n";
    echo "   - Kullanıcı ID: " . $result['data']['kullanici_id'] . "\n";
    echo "   - Firma Adı: " . $result['data']['firma_adi'] . "\n";
    echo "   - E-posta: " . $result['data']['email'] . "\n";
} else {
    echo "❌ Test Başarısız!\n";
    echo "   - Hata: " . ($result['message'] ?? 'Bilinmeyen hata') . "\n";
}
?>
