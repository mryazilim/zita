<?php
/**
 * Zita Projesi - PDO Test
 * 
 * PDO ile veritabanı bağlantısını test etmek için basit script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita PDO Test Scripti\n\n";

try {
    // PDO bağlantısı
    $pdo = new PDO('mysql:host=localhost;dbname=zita_vt;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ PDO bağlantısı başarılı\n";
    
    // Sistem kullanıcılarını kontrol et
    $stmt = $pdo->prepare("
        SELECT id, kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi, aktif, son_giris_tarihi
        FROM sistem_kullanicilari 
        WHERE kullanici_adi = ? AND aktif = 1
    ");
    $stmt->execute(['admin']);
    $kullanici = $stmt->fetch(PDO::FETCH_OBJ);
    
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
        
        // Varsayılan kullanıcı oluştur
        echo "🔧 Varsayılan kullanıcı oluşturuluyor...\n";
        
        $stmt = $pdo->prepare("
            INSERT INTO sistem_kullanicilari 
            (kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi, aktif, olusturma_tarihi) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            'admin',
            'admin@zita.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'Sistem Yöneticisi',
            'super_admin',
            1
        ]);
        
        if ($result) {
            echo "✅ Varsayılan kullanıcı oluşturuldu!\n";
            echo "   - Kullanıcı Adı: admin\n";
            echo "   - Şifre: admin123\n";
        } else {
            echo "❌ Kullanıcı oluşturulamadı!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n✅ Test tamamlandı!\n";
?>
