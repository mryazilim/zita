<?php
/**
 * Zita Projesi - Giriş Test Scripti
 * 
 * Giriş işlemini test etmek için basit script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita Giriş Test Scripti\n\n";

// Test verileri
$testData = [
    'kullanici_adi' => 'admin',
    'sifre' => 'admin123'
];

echo "📋 Test Verileri:\n";
echo "   - Kullanıcı Adı: {$testData['kullanici_adi']}\n";
echo "   - Şifre: {$testData['sifre']}\n\n";

// API'ye POST isteği gönder
$url = 'http://localhost/projeler/zita/zita/api/giris.php';
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
    if ($response && $response['success']) {
        echo "🎉 Giriş başarılı!\n";
        echo "   - Token: " . substr($response['data']['token'], 0, 50) . "...\n";
        echo "   - Kullanıcı: {$response['data']['user']['kullanici_adi']}\n";
        echo "   - Yönlendirme: {$response['data']['redirect']}\n";
    } else {
        echo "❌ Giriş başarısız!\n";
        echo "   - Hata: " . ($response['message'] ?? 'Bilinmeyen hata') . "\n";
    }
}

echo "\n✅ Test tamamlandı!\n";
?>
