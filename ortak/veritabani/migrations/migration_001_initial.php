<?php
/**
 * Zita Projesi - Migration 001: Initial Database Setup
 * 
 * Bu migration veritabanının ilk kurulumunu yapar.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../../kutuphaneler/veritabani_sinifi.php';

class Migration001Initial {
    private $db;
    
    public function __construct() {
        $this->db = new VeritabaniSinifi();
    }
    
    /**
     * Migration'ı çalıştır
     */
    public function up() {
        echo "🚀 Migration 001: Initial Database Setup başlatılıyor...\n";
        
        try {
            // Veritabanını oluştur
            $this->db->query("CREATE DATABASE IF NOT EXISTS zita_vt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Veritabanı oluşturuldu\n";
            
            // Tabloları oluştur
            $this->createTables();
            echo "✅ Tablolar oluşturuldu\n";
            
            // Başlangıç verilerini yükle
            $this->loadInitialData();
            echo "✅ Başlangıç verileri yüklendi\n";
            
            echo "🎉 Migration 001 başarıyla tamamlandı!\n";
            return true;
            
        } catch (Exception $e) {
            echo "❌ Migration hatası: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Migration'ı geri al
     */
    public function down() {
        echo "🔄 Migration 001 geri alınıyor...\n";
        
        try {
            // Veritabanını sil
            $this->db->query("DROP DATABASE IF EXISTS zita_vt");
            echo "✅ Veritabanı silindi\n";
            
            echo "🎉 Migration 001 başarıyla geri alındı!\n";
            return true;
            
        } catch (Exception $e) {
            echo "❌ Rollback hatası: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Tabloları oluştur
     */
    private function createTables() {
        // İller tablosu
        $this->db->query("
            CREATE TABLE IF NOT EXISTS iller (
                id INT AUTO_INCREMENT PRIMARY KEY,
                il_adi VARCHAR(50) NOT NULL,
                plaka_kodu VARCHAR(2) NOT NULL,
                olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_il_adi (il_adi),
                INDEX idx_plaka_kodu (plaka_kodu)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // İlçeler tablosu
        $this->db->query("
            CREATE TABLE IF NOT EXISTS ilceler (
                id INT AUTO_INCREMENT PRIMARY KEY,
                il_id INT NOT NULL,
                ilce_adi VARCHAR(100) NOT NULL,
                olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (il_id) REFERENCES iller(id) ON DELETE CASCADE,
                INDEX idx_il_id (il_id),
                INDEX idx_ilce_adi (ilce_adi)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Sektörler tablosu
        $this->db->query("
            CREATE TABLE IF NOT EXISTS sektorler (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sektor_adi VARCHAR(100) NOT NULL,
                sektor_aciklama TEXT,
                aktif BOOLEAN DEFAULT TRUE,
                olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_sektor_adi (sektor_adi),
                INDEX idx_aktif (aktif)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Sistem kullanıcıları tablosu
        $this->db->query("
            CREATE TABLE IF NOT EXISTS sistem_kullanicilari (
                id INT AUTO_INCREMENT PRIMARY KEY,
                kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                sifre VARCHAR(255) NOT NULL,
                ad_soyad VARCHAR(100) NOT NULL,
                yetki_seviyesi ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
                aktif BOOLEAN DEFAULT TRUE,
                son_giris_tarihi TIMESTAMP NULL,
                olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_kullanici_adi (kullanici_adi),
                INDEX idx_email (email),
                INDEX idx_yetki_seviyesi (yetki_seviyesi),
                INDEX idx_aktif (aktif)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Firmalar tablosu
        $this->db->query("
            CREATE TABLE IF NOT EXISTS firmalar (
                id INT AUTO_INCREMENT PRIMARY KEY,
                firma_adi VARCHAR(200) NOT NULL,
                unvan VARCHAR(300) NOT NULL,
                vkn_tc VARCHAR(20) NOT NULL UNIQUE,
                vergi_dairesi VARCHAR(100),
                telefon VARCHAR(20),
                email VARCHAR(100),
                adres TEXT,
                il_id INT,
                ilce_id INT,
                sektor_id INT,
                aktif BOOLEAN DEFAULT TRUE,
                olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (il_id) REFERENCES iller(id) ON DELETE SET NULL,
                FOREIGN KEY (ilce_id) REFERENCES ilceler(id) ON DELETE SET NULL,
                FOREIGN KEY (sektor_id) REFERENCES sektorler(id) ON DELETE SET NULL,
                INDEX idx_firma_adi (firma_adi),
                INDEX idx_vkn_tc (vkn_tc),
                INDEX idx_email (email),
                INDEX idx_aktif (aktif)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    
    /**
     * Başlangıç verilerini yükle
     */
    private function loadInitialData() {
        // Sistem kullanıcısı ekle
        $this->db->insert('sistem_kullanicilari', [
            'kullanici_adi' => 'admin',
            'email' => 'admin@zita.com',
            'sifre' => password_hash('admin123', PASSWORD_DEFAULT),
            'ad_soyad' => 'Sistem Yöneticisi',
            'yetki_seviyesi' => 'super_admin'
        ]);
    }
}
?>
