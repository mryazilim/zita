<?php
/**
 * Zita Projesi - Basit API Test
 * 
 * API'yi doğrudan test etmek için basit script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita API Test Scripti\n\n";

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../ortak/veritabani/baglanti.php';

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    echo "✅ Veritabanı bağlantısı başarılı\n";
    
    // Sistem kullanıcılarını kontrol et
    $kullanici = $db->get_row("
        SELECT id, kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi, aktif, son_giris_tarihi
        FROM sistem_kullanicilari 
        WHERE kullanici_adi = ? AND aktif = 1
    ", ['admin']);
    
    if ($kullanici) {
        echo "✅ Kullanıcı bulundu:\n";
        echo "   - ID: {$kullanici->id}\n";
        echo "   - Kullanıcı Adı: {$kullanici->kullanici_adi}\n";
        echo "   - E-posta: {$kullanici->email}\n";
        echo "   - Ad Soyad: {$kullanici->ad_soyad}\n";
        echo "   - Yetki: {$kullanici->yetki_seviyesi}\n";
        
        // Şifre testi
        $testSifre = 'admin123';
        if (password_verify($testSifre, $kullanici->sifre)) {
            echo "✅ Şifre doğrulandı!\n";
        } else {
            echo "❌ Şifre yanlış!\n";
        }
        
    } else {
        echo "❌ Kullanıcı bulunamadı!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n✅ Test tamamlandı!\n";
?>
