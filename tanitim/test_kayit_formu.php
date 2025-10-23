<?php
/**
 * Zita Projesi - Kayıt Formu Test Scripti
 * 
 * Kayıt formunun çalışıp çalışmadığını test etmek için script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita Kayıt Formu Test Scripti\n\n";

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

// API endpoint'lerini test et
$endpoints = [
    'iller' => 'http://localhost/projeler/zita/tanitim/api/iller.php',
    'ilceler' => 'http://localhost/projeler/zita/tanitim/api/ilceler.php?il_id=1',
    'sektorler' => 'http://localhost/projeler/zita/tanitim/api/sektorler.php',
    'firma_kayit' => 'http://localhost/projeler/zita/tanitim/api/firma_kayit.php'
];

foreach ($endpoints as $name => $url) {
    echo "🔍 $name API Testi:\n";
    echo "   URL: $url\n";
    
    if ($name === 'firma_kayit') {
        // POST isteği
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
    } else {
        // GET isteği
        $result = file_get_contents($url);
    }
    
    if ($result === FALSE) {
        echo "   ❌ API'ye bağlanılamadı!\n";
        echo "   Hata: " . error_get_last()['message'] . "\n";
    } else {
        echo "   ✅ API Yanıtı:\n";
        $response = json_decode($result, true);
        if ($response) {
            if (isset($response['success'])) {
                echo "   Başarı: " . ($response['success'] ? 'Evet' : 'Hayır') . "\n";
                if (isset($response['message'])) {
                    echo "   Mesaj: " . $response['message'] . "\n";
                }
            } else {
                echo "   Veri sayısı: " . count($response) . "\n";
            }
        } else {
            echo "   Ham yanıt: " . substr($result, 0, 200) . "...\n";
        }
    }
    echo "\n";
}

echo "✅ Test tamamlandı!\n";
?>
