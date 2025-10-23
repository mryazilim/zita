<?php
/**
 * Zita Projesi - Tablo Sıfırlama Scripti
 * 
 * Bu script firma ve kullanıcı tablolarını TRUNCATE ile tamamen sıfırlar.
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

echo "🗑️ Tablo Sıfırlama İşlemi\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Sıfırlama öncesi durum
    echo "📊 Sıfırlama Öncesi Durum:\n";
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    echo "   - Firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n\n";
    
    // Onay iste
    echo "⚠️  DİKKAT: Bu işlem tüm firma ve kullanıcı verilerini silecektir!\n";
    echo "   - firmalar tablosu tamamen sıfırlanacak\n";
    echo "   - firma_kullanicilari tablosu tamamen sıfırlanacak\n";
    echo "   - Bu işlem geri alınamaz!\n\n";
    
    echo "Devam etmek için 'EVET' yazın: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim($line) !== 'EVET') {
        echo "❌ İşlem iptal edildi.\n";
        exit;
    }
    
    echo "\n🔄 Tablolar sıfırlanıyor...\n\n";
    
    // 1. Firma kullanıcıları tablosunu sıfırla (foreign key kısıtlaması nedeniyle önce)
    echo "1️⃣ firma_kullanicilari tablosu sıfırlanıyor...\n";
    $db->query("TRUNCATE TABLE firma_kullanicilari");
    echo "   ✅ firma_kullanicilari tablosu sıfırlandı.\n\n";
    
    // 2. Firmalar tablosunu sıfırla
    echo "2️⃣ firmalar tablosu sıfırlanıyor...\n";
    $db->query("TRUNCATE TABLE firmalar");
    echo "   ✅ firmalar tablosu sıfırlandı.\n\n";
    
    // Sıfırlama sonrası durum
    echo "📊 Sıfırlama Sonrası Durum:\n";
    $firmaSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firmalar");
    $kullaniciSayisi = $db->get_row("SELECT COUNT(*) as toplam FROM firma_kullanicilari");
    echo "   - Firma sayısı: " . $firmaSayisi['toplam'] . "\n";
    echo "   - Kullanıcı sayısı: " . $kullaniciSayisi['toplam'] . "\n\n";
    
    // Auto increment değerlerini sıfırla
    echo "3️⃣ Auto increment değerleri sıfırlanıyor...\n";
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
