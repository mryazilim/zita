<?php
/**
 * Zita Projesi - Giriş API Test
 * 
 * Giriş API'sini test etmek için basit test sayfası
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Zita Giriş API Test</h2>";

// Veritabanı bağlantısını test et
echo "<h3>1. Veritabanı Bağlantı Testi</h3>";
try {
    require_once __DIR__ . '/../../ortak/veritabani/baglanti.php';
    $db = veritabani_baglanti();
    echo "✅ Veritabanı bağlantısı başarılı<br>";
    
    // Test sorgusu
    $test = $db->get_row("SELECT 1 as test");
    if ($test) {
        echo "✅ Test sorgusu başarılı<br>";
    } else {
        echo "❌ Test sorgusu başarısız<br>";
    }
} catch (Exception $e) {
    echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "<br>";
}

// Sistem kullanıcılarını kontrol et
echo "<h3>2. Sistem Kullanıcıları Kontrolü</h3>";
try {
    $kullanicilar = $db->get_results("SELECT id, kullanici_adi, email, ad_soyad, aktif FROM sistem_kullanicilari");
    if ($kullanicilar) {
        echo "✅ Sistem kullanıcıları bulundu: " . count($kullanicilar) . " kullanıcı<br>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Kullanıcı Adı</th><th>E-posta</th><th>Ad Soyad</th><th>Aktif</th></tr>";
        foreach ($kullanicilar as $kullanici) {
            echo "<tr>";
            echo "<td>" . $kullanici['id'] . "</td>";
            echo "<td>" . $kullanici['kullanici_adi'] . "</td>";
            echo "<td>" . $kullanici['email'] . "</td>";
            echo "<td>" . $kullanici['ad_soyad'] . "</td>";
            echo "<td>" . ($kullanici['aktif'] ? 'Evet' : 'Hayır') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Sistem kullanıcıları bulunamadı<br>";
    }
} catch (Exception $e) {
    echo "❌ Sistem kullanıcıları sorgu hatası: " . $e->getMessage() . "<br>";
}

// Giriş API'sini test et
echo "<h3>3. Giriş API Testi</h3>";
try {
    // Test verisi
    $testData = [
        'kullanici_adi' => 'admin',
        'sifre' => 'admin123',
        'beni_hatirla' => true
    ];
    
    // API'ye istek gönder
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/projeler/zita/zita/api/giris.php');
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
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        echo "✅ API yanıtı alındı (HTTP: $httpCode)<br>";
        echo "<strong>Yanıt:</strong><br>";
        echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        if ($result && $result['success']) {
            echo "✅ Giriş API testi başarılı<br>";
        } else {
            echo "❌ Giriş API testi başarısız: " . ($result['message'] ?? 'Bilinmeyen hata') . "<br>";
        }
    } else {
        echo "❌ API yanıtı alınamadı<br>";
    }
} catch (Exception $e) {
    echo "❌ Giriş API test hatası: " . $e->getMessage() . "<br>";
}

// Token doğrulama API'sini test et
echo "<h3>4. Token Doğrulama API Testi</h3>";
try {
    // Önce giriş yap ve token al
    $testData = [
        'kullanici_adi' => 'admin',
        'sifre' => 'admin123',
        'beni_hatirla' => true
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/projeler/zita/zita/api/giris.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($testData))
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $result = json_decode($response, true);
        if ($result && $result['success'] && isset($result['data']['token'])) {
            $token = $result['data']['token'];
            echo "✅ Token alındı: " . substr($token, 0, 20) . "...<br>";
            
            // Token doğrulama testi
            $tokenData = [
                'token' => $token
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/projeler/zita/zita/api/token-dogrula.php');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tokenData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($tokenData))
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $tokenResponse = curl_exec($ch);
            $tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($tokenResponse) {
                $tokenResult = json_decode($tokenResponse, true);
                echo "✅ Token doğrulama yanıtı alındı (HTTP: $tokenHttpCode)<br>";
                echo "<strong>Token Doğrulama Yanıtı:</strong><br>";
                echo "<pre>" . json_encode($tokenResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
                
                if ($tokenResult && $tokenResult['success']) {
                    echo "✅ Token doğrulama testi başarılı<br>";
                } else {
                    echo "❌ Token doğrulama testi başarısız: " . ($tokenResult['message'] ?? 'Bilinmeyen hata') . "<br>";
                }
            } else {
                echo "❌ Token doğrulama yanıtı alınamadı<br>";
            }
        } else {
            echo "❌ Token alınamadı<br>";
        }
    } else {
        echo "❌ Giriş yanıtı alınamadı<br>";
    }
} catch (Exception $e) {
    echo "❌ Token doğrulama test hatası: " . $e->getMessage() . "<br>";
}

echo "<h3>5. Test Tamamlandı</h3>";
echo "<p>Test sonuçları yukarıda görüntülenmektedir.</p>";
?>
