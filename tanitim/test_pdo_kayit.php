<?php
/**
 * Zita Projesi - PDO Kayıt Test
 * 
 * PDO ile firma kayıt işlemini test etmek için script
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔐 Zita PDO Kayıt Test Scripti\n\n";

try {
    // PDO bağlantısı
    $pdo = new PDO('mysql:host=localhost;dbname=zita_vt;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ PDO bağlantısı başarılı\n";
    
    // Test verileri
    $firmaData = [
        'vkn_tc' => '1234567890',
        'vergi_dairesi' => 'Test Vergi Dairesi',
        'firma_adi' => 'Test Firma A.Ş.',
        'unvan' => 'Test Firma Anonim Şirketi',
        'telefon' => '05551234567',
        'email' => 'test@firma.com',
        'adres' => 'Test Adres, Test Mahallesi, Test Sokak No:1',
        'il_id' => 1,
        'ilce_id' => 1,
        'sektor_id' => 1
    ];
    
    $kullaniciData = [
        'kullanici_adi' => 'test@firma.com',
        'email' => 'test@firma.com',
        'sifre' => password_hash('test123', PASSWORD_DEFAULT),
        'ad_soyad' => 'Test Kullanıcı',
        'telefon' => '05551234567',
        'yetki_seviyesi' => 'admin'
    ];
    
    // Firma kaydını oluştur
    $stmt = $pdo->prepare("
        INSERT INTO firmalar 
        (vkn_tc, vergi_dairesi, firma_adi, unvan, telefon, email, adres, il_id, ilce_id, sektor_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $firmaData['vkn_tc'],
        $firmaData['vergi_dairesi'],
        $firmaData['firma_adi'],
        $firmaData['unvan'],
        $firmaData['telefon'],
        $firmaData['email'],
        $firmaData['adres'],
        $firmaData['il_id'],
        $firmaData['ilce_id'],
        $firmaData['sektor_id']
    ]);
    
    if ($result) {
        $firmaId = $pdo->lastInsertId();
        echo "✅ Firma kaydı oluşturuldu! ID: $firmaId\n";
        
        // Kullanıcı kaydını oluştur
        $stmt = $pdo->prepare("
            INSERT INTO firma_kullanicilari 
            (firma_id, kullanici_adi, email, sifre, ad_soyad, telefon, yetki_seviyesi) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $firmaId,
            $kullaniciData['kullanici_adi'],
            $kullaniciData['email'],
            $kullaniciData['sifre'],
            $kullaniciData['ad_soyad'],
            $kullaniciData['telefon'],
            $kullaniciData['yetki_seviyesi']
        ]);
        
        if ($result) {
            $kullaniciId = $pdo->lastInsertId();
            echo "✅ Kullanıcı kaydı oluşturuldu! ID: $kullaniciId\n";
            echo "🎉 Kayıt işlemi başarılı!\n";
        } else {
            echo "❌ Kullanıcı kaydı oluşturulamadı!\n";
        }
        
    } else {
        echo "❌ Firma kaydı oluşturulamadı!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n✅ Test tamamlandı!\n";
?>
