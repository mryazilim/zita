<?php
/**
 * Firma ve Kullanıcı Tablolarını Yedekle
 * 
 * Bu dosya firmalar ve firma_kullanicilari tablolarının
 * yedeklerini alır ve boş hallerini kaydeder.
 */

require_once __DIR__ . '/../ortak/veritabani/baglanti.php';

try {
    $db = veritabani_baglanti();
    
    echo "<h1>💾 Tablo Yedekleme İşlemi</h1>";
    
    // 1. Firmalar tablosunu yedekle
    echo "<h2>📋 Firmalar Tablosu Yedekleme</h2>";
    $firmalar = $db->get_results("SELECT * FROM firmalar");
    
    $firmalarYedek = [
        'tablo_adi' => 'firmalar',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => count($firmalar),
        'veriler' => $firmalar
    ];
    
    file_put_contents('yedekler/firmalar_yedek_' . date('Y-m-d_H-i-s') . '.json', 
        json_encode($firmalarYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firmalar tablosu yedeklendi: " . count($firmalar) . " kayıt</p>";
    
    // 2. Firma kullanıcıları tablosunu yedekle
    echo "<h2>👥 Firma Kullanıcıları Tablosu Yedekleme</h2>";
    $firmaKullanicilari = $db->get_results("SELECT * FROM firma_kullanicilari");
    
    $firmaKullanicilariYedek = [
        'tablo_adi' => 'firma_kullanicilari',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => count($firmaKullanicilari),
        'veriler' => $firmaKullanicilari
    ];
    
    file_put_contents('yedekler/firma_kullanicilari_yedek_' . date('Y-m-d_H-i-s') . '.json', 
        json_encode($firmaKullanicilariYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firma kullanıcıları tablosu yedeklendi: " . count($firmaKullanicilari) . " kayıt</p>";
    
    // 3. Boş tablo yapılarını kaydet
    echo "<h2>🏗️ Boş Tablo Yapıları Kaydediliyor</h2>";
    
    // Firmalar tablosu yapısı
    $firmalarYapisi = $db->get_results("DESCRIBE firmalar");
    $firmalarBosYedek = [
        'tablo_adi' => 'firmalar',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => 0,
        'tablo_yapisi' => $firmalarYapisi,
        'veriler' => []
    ];
    
    file_put_contents('yedekler/firmalar_bos_yedek.json', 
        json_encode($firmalarBosYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firmalar boş yapısı kaydedildi</p>";
    
    // Firma kullanıcıları tablosu yapısı
    $firmaKullanicilariYapisi = $db->get_results("DESCRIBE firma_kullanicilari");
    $firmaKullanicilariBosYedek = [
        'tablo_adi' => 'firma_kullanicilari',
        'yedekleme_tarihi' => date('Y-m-d H:i:s'),
        'kayit_sayisi' => 0,
        'tablo_yapisi' => $firmaKullanicilariYapisi,
        'veriler' => []
    ];
    
    file_put_contents('yedekler/firma_kullanicilari_bos_yedek.json', 
        json_encode($firmaKullanicilariBosYedek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "<p>✅ Firma kullanıcıları boş yapısı kaydedildi</p>";
    
    echo "<h2>✅ Yedekleme İşlemi Tamamlandı!</h2>";
    echo "<p>📁 Yedek dosyaları: tanitim/yedekler/ klasöründe</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Yedekleme Hatası:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
