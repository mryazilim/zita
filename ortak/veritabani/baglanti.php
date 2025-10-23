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

// nsql v1.4 autoloader'ını dahil et
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/config.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/connection_pool.php';

// nsql v1.4 config ayarlarını yapılandır
nsql\database\config::set('db_host', VeritabaniAyarlari::DB_HOST);
nsql\database\config::set('db_name', VeritabaniAyarlari::DB_NAME);
nsql\database\config::set('db_user', VeritabaniAyarlari::DB_USER);
nsql\database\config::set('db_pass', VeritabaniAyarlari::DB_PASS);
nsql\database\config::set('db_charset', VeritabaniAyarlari::DB_CHARSET);
nsql\database\config::set('debug_mode', VeritabaniAyarlari::DEBUG_MODE);
nsql\database\config::set('query_cache_enabled', VeritabaniAyarlari::QUERY_CACHE_ENABLED);
nsql\database\config::set('statement_cache_limit', VeritabaniAyarlari::STATEMENT_CACHE_LIMIT);
nsql\database\config::set('query_cache_timeout', VeritabaniAyarlari::QUERY_CACHE_TIMEOUT);
nsql\database\config::set('auto_adjust_chunk_size', VeritabaniAyarlari::AUTO_ADJUST_CHUNK_SIZE);
nsql\database\config::set('default_chunk_size', VeritabaniAyarlari::DEFAULT_CHUNK_SIZE);
nsql\database\config::set('min_connections', 2);
nsql\database\config::set('max_connections', 10);

// Trait dosyalarını dahil et
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/cache_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/connection_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/debug_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/query_analyzer_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/query_parameter_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/statement_cache_trait.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/traits/transaction_trait.php';

// Security sınıflarını dahil et
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/security/session_manager.php';
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/security/query_analyzer.php';

// Ana nsql sınıfını dahil et
require_once __DIR__ . '/../kutuphaneler/nsql/src/database/nsql.php';

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
            VeritabaniAyarlari::DB_CHARSET,
            VeritabaniAyarlari::DEBUG_MODE
        );
            
            // nsql v1.4 ayarlarını yapılandır (constructor'da otomatik yapılır)
            // Debug mode ve cache ayarları nsql v1.4'te otomatik olarak yapılandırılır
            
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
