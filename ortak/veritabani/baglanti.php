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

// Orijinal nsql kütüphanesini dahil et
require_once __DIR__ . '/../kutuphaneler/nsql-real/nsql.php';

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
            // nsql-enhanced kütüphanesini başlat
        $this->nsql = new nsql\database\nsql(
            VeritabaniAyarlari::DB_HOST,
            VeritabaniAyarlari::DB_NAME,
            VeritabaniAyarlari::DB_USER,
            VeritabaniAyarlari::DB_PASS,
            VeritabaniAyarlari::DB_CHARSET
        );
            
            // nsql ayarlarını yapılandır
            $this->nsql->debug_mode = VeritabaniAyarlari::DEBUG_MODE;
            $this->nsql->query_cache_enabled = VeritabaniAyarlari::QUERY_CACHE_ENABLED;
            $this->nsql->statement_cache_limit = VeritabaniAyarlari::STATEMENT_CACHE_LIMIT;
            $this->nsql->query_cache_timeout = VeritabaniAyarlari::QUERY_CACHE_TIMEOUT;
            $this->nsql->auto_adjust_chunk_size = VeritabaniAyarlari::AUTO_ADJUST_CHUNK_SIZE;
            $this->nsql->default_chunk_size = VeritabaniAyarlari::DEFAULT_CHUNK_SIZE;
            
            // Bağlantıyı test et
            $this->nsql->ensure_connection();
            
        } catch (Exception $e) {
            // Hata durumunda log kaydet
            error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
            throw new Exception("Veritabanı bağlantısı kurulamadı: " . $e->getMessage());
        }
    }
    
    /**
     * nsql instance'ını döndür
     * 
     * @return nsql\database\nsql
     */
    public function getNsql() {
        return $this->nsql;
    }
    
    /**
     * Bağlantıyı test et
     */
    public function testBaglanti() {
        try {
            $result = $this->nsql->get_row("SELECT 1 as test");
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
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
 * @return nsql\database\nsql
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
