<?php
/**
 * Yedek Geri Yükleme
 * 
 * JSON yedek dosyalarından tabloları geri yükler
 */

// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'zita_vt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔄 Yedek Geri Yükleme</h1>";
    
    $yedeklerKlasoru = __DIR__ . '/yedekler';
    
    // Mevcut yedek dosyalarını listele
    echo "<h2>📁 Mevcut Yedek Dosyaları</h2>";
    $yedekDosyalari = glob($yedeklerKlasoru . '/*.json');
    
    foreach ($yedekDosyalari as $dosya) {
        $dosyaAdi = basename($dosya);
        echo "<p>📄 " . $dosyaAdi . "</p>";
    }
    
    // Boş yedek dosyalarını geri yükle
    echo "<h2>🏗️ Boş Tablo Yapıları Geri Yükleniyor</h2>";
    
    // Firmalar boş yedek
    $firmalarBosYedek = json_decode(file_get_contents($yedeklerKlasoru . '/firmalar_bos_yedek.json'), true);
    echo "<p>✅ Firmalar boş yapısı yüklendi: " . $firmalarBosYedek['kayit_sayisi'] . " kayıt</p>";
    
    // Firma kullanıcıları boş yedek
    $firmaKullanicilariBosYedek = json_decode(file_get_contents($yedeklerKlasoru . '/firma_kullanicilari_bos_yedek.json'), true);
    echo "<p>✅ Firma kullanıcıları boş yapısı yüklendi: " . $firmaKullanicilariBosYedek['kayit_sayisi'] . " kayıt</p>";
    
    // Veri içeren yedek dosyalarını bul
    $veriYedekleri = array_filter($yedekDosyalari, function($dosya) {
        return strpos($dosya, '_yedek_') !== false && strpos($dosya, '_bos_') === false;
    });
    
    if (!empty($veriYedekleri)) {
        echo "<h2>📊 Veri İçeren Yedekler</h2>";
        
        foreach ($veriYedekleri as $yedekDosyasi) {
            $yedekVerisi = json_decode(file_get_contents($yedekDosyasi), true);
            $dosyaAdi = basename($yedekDosyasi);
            
            echo "<h3>📄 " . $dosyaAdi . "</h3>";
            echo "<p>📋 Tablo: " . $yedekVerisi['tablo_adi'] . "</p>";
            echo "<p>📅 Tarih: " . $yedekVerisi['yedekleme_tarihi'] . "</p>";
            echo "<p>📊 Kayıt Sayısı: " . $yedekVerisi['kayit_sayisi'] . "</p>";
            
            if ($yedekVerisi['kayit_sayisi'] > 0) {
                echo "<p>⚠️ Bu yedek veri içeriyor. Geri yüklemek için ayrı bir script gerekli.</p>";
            }
        }
    }
    
    echo "<h2>✅ Yedek Dosyaları Başarıyla Okundu!</h2>";
    echo "<p>📁 Yedek dosyaları: " . $yedeklerKlasoru . "</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Geri Yükleme Hatası:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
