<?php
/**
 * Zita Projesi - Veritabanı Bağlantı Dosyası
 * 
 * Bu dosya tüm sistemler için ortak veritabanı bağlantısını sağlar.
 * nsql kütüphanesi kullanılarak güvenli bağlantı kurulur.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir (geliştirme ortamında)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı sınıfını dahil et
require_once __DIR__ . '/../../ortak/kutuphaneler/veritabani_sinifi.php';

/**
 * Veritabanı bağlantı ayarları
 */
class VeritabaniAyarlari {
    // Veritabanı bilgileri
    const DB_HOST = 'localhost';
    const DB_NAME = 'zita_vt';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // nsql ayarları
    const DEBUG_MODE = true; // Geliştirme ortamında true
    const QUERY_CACHE_ENABLED = true;
    const STATEMENT_CACHE_LIMIT = 150;
    const QUERY_CACHE_TIMEOUT = 600;
    const AUTO_ADJUST_CHUNK_SIZE = true;
    const DEFAULT_CHUNK_SIZE = 1000;
}

/**
 * Veritabanı bağlantı sınıfı
 */
class VeritabaniBaglanti {
    private static $instance = null;
    private $nsql;
    
    /**
     * Singleton pattern ile tek instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Veritabanı bağlantısını kur
     */
    private function __construct() {
        try {
            // Veritabanı sınıfını başlat
            $this->nsql = new VeritabaniSinifi(
                VeritabaniAyarlari::DB_HOST,
                VeritabaniAyarlari::DB_NAME,
                VeritabaniAyarlari::DB_USER,
                VeritabaniAyarlari::DB_PASS,
                VeritabaniAyarlari::DB_CHARSET
            );
            
            // Bağlantıyı test et
            if (!$this->nsql->test()) {
                throw new Exception("Veritabanı bağlantı testi başarısız");
            }
            
        } catch (Exception $e) {
            // Hata durumunda log kaydet
            error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
            throw new Exception("Veritabanı bağlantısı kurulamadı: " . $e->getMessage());
        }
    }
    
    /**
     * Veritabanı sınıfı instance'ını döndür
     */
    public function getNsql() {
        return $this->nsql;
    }
    
    /**
     * Bağlantıyı test et
     */
    public function testBaglanti() {
        return $this->nsql->test();
    }
    
    /**
     * Bağlantıyı kapat
     */
    public function kapat() {
        if ($this->nsql) {
            $this->nsql = null;
        }
    }
}

/**
 * Global veritabanı bağlantı fonksiyonu
 * 
 * @return VeritabaniSinifi
 */
function veritabani_baglanti() {
    return VeritabaniBaglanti::getInstance()->getNsql();
}

/**
 * Veritabanı bağlantısını test et
 * 
 * @return bool
 */
function veritabani_test() {
    return VeritabaniBaglanti::getInstance()->testBaglanti();
}

// Bağlantıyı test et
if (!veritabani_test()) {
    die("Veritabanı bağlantısı başarısız!");
}

?>
