<?php
/**
 * Zita Projesi - Otomatik Tablo Sıfırlama Scripti
 * 
 * Bu script firma ve kullanıcı tablolarını TRUNCATE ile tamamen sıfırlar.
 * Onay beklemeden otomatik olarak çalışır.
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

echo "🗑️ Otomatik Tablo Sıfırlama İşlemi\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Sıfırlama öncesi durum
    echo "📊 Sıfırlama Öncesi Durum:\n";
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    echo "   - Firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n\n";
    
    echo "🔄 Tablolar sıfırlanıyor...\n\n";
    
    // Foreign key kontrolünü devre dışı bırak
    echo "0️⃣ Foreign key kontrolü devre dışı bırakılıyor...\n";
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    echo "   ✅ Foreign key kontrolü devre dışı bırakıldı.\n\n";
    
    // 1. Firma kullanıcıları tablosunu sıfırla
    echo "1️⃣ firma_kullanicilari tablosu sıfırlanıyor...\n";
    $db->query("TRUNCATE TABLE firma_kullanicilari");
    echo "   ✅ firma_kullanicilari tablosu sıfırlandı.\n\n";
    
    // 2. Firmalar tablosunu sıfırla
    echo "2️⃣ firmalar tablosu sıfırlanıyor...\n";
    $db->query("TRUNCATE TABLE firmalar");
    echo "   ✅ firmalar tablosu sıfırlandı.\n\n";
    
    // Foreign key kontrolünü tekrar etkinleştir
    echo "3️⃣ Foreign key kontrolü etkinleştiriliyor...\n";
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "   ✅ Foreign key kontrolü etkinleştirildi.\n\n";
    
    // Sıfırlama sonrası durum
    echo "📊 Sıfırlama Sonrası Durum:\n";
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    echo "   - Firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n\n";
    
    // Auto increment değerlerini sıfırla
    echo "4️⃣ Auto increment değerleri sıfırlanıyor...\n";
    $db->query("ALTER TABLE firmalar AUTO_INCREMENT = 1");
    $db->query("ALTER TABLE firma_kullanicilari AUTO_INCREMENT = 1");
    echo "   ✅ Auto increment değerleri sıfırlandı.\n\n";
    
    echo "🎉 Tablo sıfırlama işlemi başarıyla tamamlandı!\n";
    echo "   - Tüm firma verileri silindi\n";
    echo "   - Tüm kullanıcı verileri silindi\n";
    echo "   - Auto increment değerleri sıfırlandı\n";
    
} catch (Exception $e) {
    echo "❌ Sıfırlama hatası: " . $e->getMessage() . "\n";
}
?>
