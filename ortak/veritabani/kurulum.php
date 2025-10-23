<?php
/**
 * Zita Projesi - Veritabanı Kurulum Scripti
 * 
 * Bu script veritabanını oluşturur ve tüm tabloları kurar.
 * İl, ilçe ve sektör verilerini yükler.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// nsql kütüphanesini dahil et
require_once __DIR__ . '/../../ortak/kutuphaneler/nsql/nsql.php';

/**
 * Veritabanı kurulum sınıfı
 */
class VeritabaniKurulum {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    
    public function __construct() {
        $this->host = 'localhost';
        $this->dbname = 'zita_vt';
        $this->username = 'root';
        $this->password = '';
        $this->charset = 'utf8mb4';
    }
    
    /**
     * Veritabanını oluştur
     */
    public function veritabaniOlustur() {
        try {
            // Önce veritabanı olmadan bağlan
            $pdo = new PDO(
                "mysql:host={$this->host};charset={$this->charset}", 
                $this->username, 
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Veritabanını oluştur
            $sql = "CREATE DATABASE IF NOT EXISTS {$this->dbname} 
                    CHARACTER SET {$this->charset} 
                    COLLATE {$this->charset}_unicode_ci";
            
            $pdo->exec($sql);
            echo "✅ Veritabanı '{$this->dbname}' oluşturuldu.\n";
            
            return true;
            
        } catch (PDOException $e) {
            echo "❌ Veritabanı oluşturma hatası: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * SQL dosyasını çalıştır
     */
    public function sqlDosyasiCalistir($dosyaYolu) {
        try {
            // Veritabanına bağlan
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}", 
                $this->username, 
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // SQL dosyasını oku
            $sql = file_get_contents($dosyaYolu);
            
            if ($sql === false) {
                throw new Exception("SQL dosyası okunamadı: {$dosyaYolu}");
            }
            
            // SQL'i çalıştır
            $pdo->exec($sql);
            echo "✅ SQL dosyası çalıştırıldı: " . basename($dosyaYolu) . "\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ SQL dosyası çalıştırma hatası: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Kurulumu tamamla
     */
    public function kurulumuTamamla() {
        echo "🚀 Zita Projesi Veritabanı Kurulumu Başlıyor...\n\n";
        
        // 1. Veritabanını oluştur
        echo "1️⃣ Veritabanı oluşturuluyor...\n";
        if (!$this->veritabaniOlustur()) {
            return false;
        }
        
        // 2. Tablo yapılarını oluştur
        echo "\n2️⃣ Tablo yapıları oluşturuluyor...\n";
        if (!$this->sqlDosyasiCalistir(__DIR__ . '/tablolar.sql')) {
            return false;
        }
        
        // 3. İl verilerini yükle
        echo "\n3️⃣ İl verileri yükleniyor...\n";
        if (!$this->sqlDosyasiCalistir(__DIR__ . '/il_ilce_verileri.sql')) {
            return false;
        }
        
        // 4. Sektör verilerini yükle
        echo "\n4️⃣ Sektör verileri yükleniyor...\n";
        if (!$this->sqlDosyasiCalistir(__DIR__ . '/sektor_verileri.sql')) {
            return false;
        }
        
        echo "\n🎉 Veritabanı kurulumu başarıyla tamamlandı!\n";
        echo "📊 Kurulum Özeti:\n";
        echo "   - Veritabanı: {$this->dbname}\n";
        echo "   - Tablolar: Oluşturuldu\n";
        echo "   - İl/İlçe verileri: Yüklendi\n";
        echo "   - Sektör verileri: Yüklendi\n";
        echo "   - Sistem kullanıcısı: admin/admin123\n\n";
        
        return true;
    }
    
    /**
     * Kurulumu test et
     */
    public function kurulumuTestEt() {
        try {
            // nsql kütüphanesi ile bağlantıyı test et
            $db = new nsql\database\nsql(
                $this->host,
                $this->dbname,
                $this->username,
                $this->password,
                $this->charset
            );
            
            // Test sorguları
            $iller = $db->get_results("SELECT COUNT(*) as toplam FROM iller");
            $ilceler = $db->get_results("SELECT COUNT(*) as toplam FROM ilceler");
            $sektorler = $db->get_results("SELECT COUNT(*) as toplam FROM sektorler");
            
            echo "🧪 Kurulum Test Sonuçları:\n";
            echo "   - İl sayısı: " . $iller[0]['toplam'] . "\n";
            echo "   - İlçe sayısı: " . $ilceler[0]['toplam'] . "\n";
            echo "   - Sektör sayısı: " . $sektorler[0]['toplam'] . "\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ Kurulum test hatası: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Kurulumu başlat
if (php_sapi_name() === 'cli') {
    $kurulum = new VeritabaniKurulum();
    
    if ($kurulum->kurulumuTamamla()) {
        $kurulum->kurulumuTestEt();
    }
} else {
    echo "Bu script sadece komut satırından çalıştırılabilir.\n";
    echo "Kullanım: php kurulum.php\n";
}
?>
