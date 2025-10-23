<?php
/**
 * Zita Projesi - Şifre Güncelleme
 * 
 * Sistem kullanıcısının şifresini güncellemek için script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita Şifre Güncelleme Scripti\n\n";

try {
    // PDO bağlantısı
    $pdo = new PDO('mysql:host=localhost;dbname=zita_vt;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ PDO bağlantısı başarılı\n";
    
    // Şifreyi güncelle
    $yeniSifre = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("
        UPDATE sistem_kullanicilari 
        SET sifre = ? 
        WHERE kullanici_adi = 'admin'
    ");
    
    $result = $stmt->execute([$yeniSifre]);
    
    if ($result) {
        echo "✅ Şifre güncellendi!\n";
        echo "   - Kullanıcı Adı: admin\n";
        echo "   - Yeni Şifre: admin123\n";
        
        // Test et
        $stmt = $pdo->prepare("
            SELECT sifre FROM sistem_kullanicilari 
            WHERE kullanici_adi = 'admin'
        ");
        $stmt->execute();
        $kullanici = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (password_verify('admin123', $kullanici->sifre)) {
            echo "✅ Şifre testi başarılı!\n";
        } else {
            echo "❌ Şifre testi başarısız!\n";
        }
        
    } else {
        echo "❌ Şifre güncellenemedi!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n✅ İşlem tamamlandı!\n";
?>
