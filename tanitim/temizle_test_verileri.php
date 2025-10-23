<?php
/**
 * Zita Projesi - Test Verilerini Temizleme Scripti
 * 
 * Bu script test sırasında oluşturulan verileri temizler.
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

echo "🧹 Test Verileri Temizleniyor...\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // 1. Test firmalarını sil
    echo "1️⃣ Test firmaları siliniyor...\n";
    $testFirmalar = $db->get_results("
        SELECT id, firma_adi, vkn_tc 
        FROM firmalar 
        WHERE vkn_tc IN ('1234567890', '9876543210', '1111111111')
        OR firma_adi LIKE '%Test%'
    ");
    
    if (!empty($testFirmalar)) {
        foreach ($testFirmalar as $firma) {
            echo "   - Siliniyor: {$firma['firma_adi']} (VKN: {$firma['vkn_tc']})\n";
            
            // Önce firma kullanıcılarını sil
            $db->query("DELETE FROM firma_kullanicilari WHERE firma_id = ?", [$firma['id']]);
            
            // Sonra firmayı sil
            $db->query("DELETE FROM firmalar WHERE id = ?", [$firma['id']]);
        }
        echo "   ✅ " . count($testFirmalar) . " test firması silindi.\n\n";
    } else {
        echo "   ℹ️ Silinecek test firması bulunamadı.\n\n";
    }
    
    // 2. Test kullanıcılarını sil
    echo "2️⃣ Test kullanıcıları siliniyor...\n";
    $testKullanicilar = $db->get_results("
        SELECT id, kullanici_adi, email, ad_soyad 
        FROM firma_kullanicilari 
        WHERE email LIKE '%test%' 
        OR kullanici_adi LIKE '%test%'
        OR ad_soyad LIKE '%Test%'
    ");
    
    if (!empty($testKullanicilar)) {
        foreach ($testKullanicilar as $kullanici) {
            echo "   - Siliniyor: {$kullanici['ad_soyad']} ({$kullanici['email']})\n";
            $db->query("DELETE FROM firma_kullanicilari WHERE id = ?", [$kullanici['id']]);
        }
        echo "   ✅ " . count($testKullanicilar) . " test kullanıcısı silindi.\n\n";
    } else {
        echo "   ℹ️ Silinecek test kullanıcısı bulunamadı.\n\n";
    }
    
    // 3. Test log kayıtlarını sil (şimdilik atla - log tablosu yapısı farklı)
    echo "3️⃣ Test log kayıtları siliniyor...\n";
    echo "   ℹ️ Log tablosu yapısı farklı olduğu için atlanıyor.\n\n";
    
    
    // 4. Temizlik sonrası durum
    echo "4️⃣ Temizlik sonrası durum:\n";
    
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    $logSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM log_kayitlari");
    
    echo "   - Toplam firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Toplam kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n";
    echo "   - Toplam log kaydı sayısı: " . $logSayisi['toplam'] . "\n\n";
    
    echo "🎉 Test verileri başarıyla temizlendi!\n";
    
} catch (Exception $e) {
    echo "❌ Temizlik hatası: " . $e->getMessage() . "\n";
}
?>
