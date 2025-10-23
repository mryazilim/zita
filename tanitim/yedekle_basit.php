<?php
/**
 * Basit Tablo Yedekleme
 * 
 * PDO kullanarak tabloları yedekler
 */

// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'zita_vt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>💾 Basit Tablo Yedekleme</h1>";
    
    // Yedekler klasörünü oluştur
    $yedeklerKlasoru = __DIR__ . '/yedekler';
    if (!is_dir($yedeklerKlasoru)) {
        mkdir($yedeklerKlasoru, 0755, true);
    }
    
    // 1. Firmalar tablosunu yedekle
    echo "<h2>📋 Firmalar Tablosu</h2>";
    $stmt = $pdo->query("SELECT * FROM firmalar");
    $firmalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $firmalarYedek = [
        'tablo_adi' => 'firmalar',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => count($firmalar),
        'veriler' => $firmalar
    ];
    
    file_put_contents($yedeklerKlasoru . '/firmalar_yedek_' . date('Y-m-d_H-i-s') . '.json', 
        json_encode($firmalarYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firmalar yedeklendi: " . count($firmalar) . " kayıt</p>";
    
    // 2. Firma kullanıcıları tablosunu yedekle
    echo "<h2>👥 Firma Kullanıcıları Tablosu</h2>";
    $stmt = $pdo->query("SELECT * FROM firma_kullanicilari");
    $firmaKullanicilari = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $firmaKullanicilariYedek = [
        'tablo_adi' => 'firma_kullanicilari',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => count($firmaKullanicilari),
        'veriler' => $firmaKullanicilari
    ];
    
    file_put_contents($yedeklerKlasoru . '/firma_kullanicilari_yedek_' . date('Y-m-d_H-i-s') . '.json', 
        json_encode($firmaKullanicilariYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firma kullanıcıları yedeklendi: " . count($firmaKullanicilari) . " kayıt</p>";
    
    // 3. Boş tablo yapılarını kaydet
    echo "<h2>🏗️ Boş Tablo Yapıları</h2>";
    
    // Firmalar tablosu yapısı
    $stmt = $pdo->query("DESCRIBE firmalar");
    $firmalarYapisi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $firmalarBosYedek = [
        'tablo_adi' => 'firmalar',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => 0,
        'tablo_yapisi' => $firmalarYapisi,
        'veriler' => []
    ];
    
    file_put_contents($yedeklerKlasoru . '/firmalar_bos_yedek.json', 
        json_encode($firmalarBosYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firmalar boş yapısı kaydedildi</p>";
    
    // Firma kullanıcıları tablosu yapısı
    $stmt = $pdo->query("DESCRIBE firma_kullanicilari");
    $firmaKullanicilariYapisi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $firmaKullanicilariBosYedek = [
        'tablo_adi' => 'firma_kullanicilari',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => 0,
        'tablo_yapisi' => $firmaKullanicilariYapisi,
        'veriler' => []
    ];
    
    file_put_contents($yedeklerKlasoru . '/firma_kullanicilari_bos_yedek.json', 
        json_encode($firmaKullanicilariBosYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firma kullanıcıları boş yapısı kaydedildi</p>";
    
    echo "<h2>✅ Yedekleme Tamamlandı!</h2>";
    echo "<p>📁 Yedek dosyaları: tanitim/yedekler/ klasöründe</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Hata:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
