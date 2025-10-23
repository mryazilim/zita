<?php
/**
 * Zita Projesi - Veri Kontrol Scripti
 * 
 * Bu script veritabanındaki mevcut verileri kontrol eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../ortak/veritabani/baglanti.php';

echo "📊 Veritabanı Veri Kontrolü\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // 1. Firmalar
    echo "1️⃣ Firmalar:\n";
    $firmalar = $db->get_results("SELECT id, firma_adi, vkn_tc, email FROM firmalar ORDER BY id");
    if (!empty($firmalar)) {
        foreach ($firmalar as $firma) {
            echo "   - ID: {$firma['id']}, Firma: {$firma['firma_adi']}, VKN: {$firma['vkn_tc']}, E-posta: {$firma['email']}\n";
        }
    } else {
        echo "   ℹ️ Firma bulunamadı.\n";
    }
    echo "\n";
    
    // 2. Firma Kullanıcıları
    echo "2️⃣ Firma Kullanıcıları:\n";
    $kullanicilar = $db->get_results("SELECT id, firma_id, kullanici_adi, email, ad_soyad, yetki_seviyesi FROM firma_kullanicilari ORDER BY id");
    if (!empty($kullanicilar)) {
        foreach ($kullanicilar as $kullanici) {
            echo "   - ID: {$kullanici['id']}, Firma ID: {$kullanici['firma_id']}, Kullanıcı: {$kullanici['kullanici_adi']}, E-posta: {$kullanici['email']}, Ad: {$kullanici['ad_soyad']}, Yetki: {$kullanici['yetki_seviyesi']}\n";
        }
    } else {
        echo "   ℹ️ Kullanıcı bulunamadı.\n";
    }
    echo "\n";
    
    // 3. İstatistikler
    echo "3️⃣ İstatistikler:\n";
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    $ilSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM iller");
    $ilceSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM ilceler");
    $sektorSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM sektorler");
    
    echo "   - Toplam firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Toplam kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n";
    echo "   - Toplam il sayısı: " . $ilSayisi['toplam'] . "\n";
    echo "   - Toplam ilçe sayısı: " . $ilceSayisi['toplam'] . "\n";
    echo "   - Toplam sektör sayısı: " . $sektorSayisi['toplam'] . "\n\n";
    
    echo "✅ Veri kontrolü tamamlandı!\n";
    
} catch (Exception $e) {
    echo "❌ Kontrol hatası: " . $e->getMessage() . "\n";
}
?>
