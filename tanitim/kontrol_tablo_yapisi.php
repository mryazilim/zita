<?php
/**
 * Zita Projesi - Tablo Yapısı Kontrol Scripti
 * 
 * Bu script sistem_kullanicilari tablosunun yapısını kontrol eder.
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

echo "📋 Tablo Yapısı Kontrolü\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Sistem kullanıcıları tablosu yapısını kontrol et
    echo "1️⃣ sistem_kullanicilari Tablosu Yapısı:\n";
    $sistemKullanicilarCols = $db->get_results("SHOW COLUMNS FROM sistem_kullanicilari");
    foreach ($sistemKullanicilarCols as $col) {
        echo "   - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "\n";
    
    // Sistem kullanıcıları verilerini kontrol et
    echo "2️⃣ Sistem Kullanıcıları Verileri:\n";
    $sistemKullanicilar = $db->get_results("SELECT * FROM sistem_kullanicilari");
    
    if (!empty($sistemKullanicilar)) {
        foreach ($sistemKullanicilar as $kullanici) {
            echo "   - ID: {$kullanici['id']}\n";
            echo "     Kullanıcı Adı: {$kullanici['kullanici_adi']}\n";
            echo "     E-posta: {$kullanici['email']}\n";
            echo "     Ad Soyad: {$kullanici['ad_soyad']}\n";
            echo "     Yetki: {$kullanici['yetki_seviyesi']}\n";
            echo "     Oluşturma: {$kullanici['olusturma_tarihi']}\n";
            echo "     ---\n";
        }
    } else {
        echo "   ℹ️ Sistem kullanıcısı bulunamadı.\n";
    }
    echo "\n";
    
    // Sistem kullanıcı sayısı
    $sistemKullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM sistem_kullanicilari");
    echo "📊 İstatistikler:\n";
    echo "   - Toplam sistem kullanıcı sayısı: " . $sistemKullaniciSayisi['toplam'] . "\n\n";
    
    // Eğer sistem kullanıcısı yoksa varsayılan oluştur
    if ($sistemKullaniciSayisi['toplam'] == 0) {
        echo "⚠️ Sistem kullanıcısı bulunamadı!\n";
        echo "   Varsayılan sistem kullanıcısı oluşturulacak...\n\n";
        
        // Varsayılan sistem kullanıcısı oluştur
        $varsayilanKullanici = [
            'kullanici_adi' => 'admin',
            'email' => 'admin@zita.com',
            'sifre' => password_hash('admin123', PASSWORD_DEFAULT),
            'ad_soyad' => 'Sistem Yöneticisi',
            'yetki_seviyesi' => 'super_admin'
        ];
        
        $kullaniciId = $db->insert('sistem_kullanicilari', $varsayilanKullanici);
        
        if ($kullaniciId) {
            echo "✅ Varsayılan sistem kullanıcısı oluşturuldu:\n";
            echo "   - Kullanıcı Adı: admin\n";
            echo "   - Şifre: admin123\n";
            echo "   - E-posta: admin@zita.com\n";
            echo "   - Yetki: super_admin\n";
        } else {
            echo "❌ Varsayılan sistem kullanıcısı oluşturulamadı!\n";
        }
    }
    
    echo "\n✅ Tablo yapısı kontrolü tamamlandı!\n";
    
} catch (Exception $e) {
    echo "❌ Kontrol hatası: " . $e->getMessage() . "\n";
}
?>
