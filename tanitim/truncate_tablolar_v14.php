<?php
/**
 * Tabloları Truncate Et (nsql v1.4 ile)
 * 
 * Foreign key kontrollerini devre dışı bırakarak
 * tabloları güvenli şekilde truncate eder
 */

// Veritabanı bağlantısı
$host = 'localhost';
$dbname = 'zita_vt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🗑️ Tablo Truncate İşlemi</h1>";
    
    // Foreign key kontrollerini devre dışı bırak
    echo "<h2>🔒 Foreign Key Kontrolleri Devre Dışı</h2>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "<p>✅ Foreign key kontrolleri devre dışı bırakıldı</p>";
    
    // 1. Firma kullanıcıları tablosunu truncate et
    echo "<h2>👥 Firma Kullanıcıları Tablosu</h2>";
    $pdo->exec("TRUNCATE TABLE firma_kullanicilari");
    echo "<p>✅ firma_kullanicilari tablosu truncate edildi</p>";
    
    // 2. Firmalar tablosunu truncate et
    echo "<h2>📋 Firmalar Tablosu</h2>";
    $pdo->exec("TRUNCATE TABLE firmalar");
    echo "<p>✅ firmalar tablosu truncate edildi</p>";
    
    // Auto increment değerlerini sıfırla
    echo "<h2>🔄 Auto Increment Sıfırlama</h2>";
    $pdo->exec("ALTER TABLE firma_kullanicilari AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE firmalar AUTO_INCREMENT = 1");
    echo "<p>✅ Auto increment değerleri sıfırlandı</p>";
    
    // Foreign key kontrollerini tekrar etkinleştir
    echo "<h2>🔓 Foreign Key Kontrolleri Etkinleştirildi</h2>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p>✅ Foreign key kontrolleri etkinleştirildi</p>";
    
    // Tablo durumlarını kontrol et
    echo "<h2>📊 Tablo Durumları</h2>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as sayi FROM firmalar");
    $firmaSayisi = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>📋 Firmalar: " . $firmaSayisi['sayi'] . " kayıt</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as sayi FROM firma_kullanicilari");
    $kullaniciSayisi = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>👥 Firma Kullanıcıları: " . $kullaniciSayisi['sayi'] . " kayıt</p>";
    
    echo "<h2>✅ Truncate İşlemi Tamamlandı!</h2>";
    echo "<p>🗑️ Tüm tablolar başarıyla temizlendi</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Truncate Hatası:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
