<?php
/**
 * Zita Projesi - nsql Enhanced
 * 
 * Bu dosya nsql'in temel özelliklerini kullanarak gelişmiş bir veritabanı kütüphanesi oluşturur.
 * nsql'in tüm özelliklerini PHP 7.4 uyumlu hale getirir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

namespace nsql\database;

class nsql_enhanced {
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    
    // nsql benzeri ayarlar
    public $debug_mode = false;
    public $query_cache_enabled = true;
    public $statement_cache_limit = 150;
    public $query_cache_timeout = 600;
    public $auto_adjust_chunk_size = true;
    public $default_chunk_size = 1000;
    
    // Cache ve performans
    private $query_cache = [];
    private $statement_cache = [];
    private $query_cache_usage = [];
    private $statement_cache_usage = [];
    
    // Debug ve hata yönetimi
    private $last_error = null;
    private $last_query = '';
    private $last_params = [];
    private $last_called_method = 'unknown';
    private $log_file = 'error_log.txt';
    
    // Memory yönetimi
    private $memory_stats = [
        'peak_usage' => 0,
        'warning_count' => 0,
        'critical_count' => 0,
    ];
    
    public function __construct($host, $dbname, $username, $password, $charset = 'utf8mb4') {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        
        $this->connect();
    }
    
    /**
     * Veritabanına bağlan
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new \PDO($dsn, $this->username, $this->password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Bağlantıyı test et
     */
    public function ensure_connection() {
        if (!$this->pdo) {
            $this->connect();
        }
        return true;
    }
    
    /**
     * Tek satır getir
     */
    public function get_row($sql, $params = []) {
        $this->set_last_called_method('get_row');
        
        // LIMIT 1 ekle eğer yoksa
        if (!preg_match('/\bLIMIT\s+\d+(?:\s*,\s*\d+)?$/i', $sql)) {
            $sql .= ' LIMIT 1';
        }
        
        // Cache kontrolü
        $cache_key = $this->generate_query_cache_key($sql, $params);
        if ($this->query_cache_enabled) {
            $cached = $this->get_from_query_cache($cache_key);
            if ($cached !== null) {
                return is_array($cached) && !empty($cached) ? $cached[0] : $cached;
            }
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            // Cache'e ekle
            if ($result && $this->query_cache_enabled) {
                $this->add_to_query_cache($cache_key, $result);
            }
            
            return $result;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'get_row');
            return null;
        }
    }
    
    /**
     * Çoklu satır getir
     */
    public function get_results($sql, $params = []) {
        $this->set_last_called_method('get_results');
        
        // Memory kontrolü
        $this->check_memory_status();
        
        // Cache kontrolü
        $cache_key = $this->generate_query_cache_key($sql, $params);
        if ($this->query_cache_enabled) {
            $cached = $this->get_from_query_cache($cache_key);
            if ($cached !== null) {
                return $cached;
            }
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            // Büyük veri seti uyarısı
            $result_count = count($results);
            if ($result_count > 15000) {
                $this->log_warning("Büyük veri seti ({$result_count} satır). get_chunk() veya get_yield() kullanmayı düşünün.");
            }
            
            // Cache'e ekle
            if ($this->query_cache_enabled) {
                $this->add_to_query_cache($cache_key, $results);
            }
            
            return $results;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'get_results');
            return [];
        }
    }
    
    /**
     * Generator ile büyük veri setlerini getir
     */
    public function get_yield($sql, $params = []) {
        $this->set_last_called_method('get_yield');
        
        // LIMIT kontrolü
        if (preg_match('/\bLIMIT\b|\bOFFSET\b/i', $sql)) {
            throw new \InvalidArgumentException('get_yield() metodu LIMIT veya OFFSET içeren sorgularla kullanılamaz.');
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            while ($row = $stmt->fetch()) {
                yield $row;
            }
        } catch (\PDOException $e) {
            $this->handle_error($e, 'get_yield');
            return;
        }
    }
    
    /**
     * Chunk ile büyük veri setlerini getir
     */
    public function get_chunk($sql, $params = [], $chunk_size = null) {
        $this->set_last_called_method('get_chunk');
        
        if ($chunk_size === null) {
            $chunk_size = $this->default_chunk_size;
        }
        
        // LIMIT kontrolü
        if (preg_match('/\bLIMIT\b|\bOFFSET\b/i', $sql)) {
            throw new \InvalidArgumentException('get_chunk() metodu LIMIT veya OFFSET içeren sorgularla kullanılamaz.');
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $chunks = [];
            $current_chunk = [];
            $count = 0;
            
            while ($row = $stmt->fetch()) {
                $current_chunk[] = $row;
                $count++;
                
                if ($count >= $chunk_size) {
                    $chunks[] = $current_chunk;
                    $current_chunk = [];
                    $count = 0;
                }
            }
            
            if (!empty($current_chunk)) {
                $chunks[] = $current_chunk;
            }
            
            return $chunks;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'get_chunk');
            return [];
        }
    }
    
    /**
     * Veri ekle
     */
    public function insert($sql, $params = []) {
        $this->set_last_called_method('insert');
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'insert');
            return false;
        }
    }
    
    /**
     * Veri güncelle
     */
    public function update($sql, $params = []) {
        $this->set_last_called_method('update');
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            return $result ? $stmt->rowCount() : false;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'update');
            return false;
        }
    }
    
    /**
     * Veri sil
     */
    public function delete($sql, $params = []) {
        $this->set_last_called_method('delete');
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            return $result ? $stmt->rowCount() : false;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'delete');
            return false;
        }
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    public function query($sql, $params = []) {
        $this->set_last_called_method('query');
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            $this->handle_error($e, 'query');
            return false;
        }
    }
    
    /**
     * Bağlantıyı test et
     */
    public function test() {
        try {
            $result = $this->get_row("SELECT 1 as test");
            return $result !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Cache'i temizle
     */
    public function clear_cache() {
        $this->query_cache = [];
        $this->statement_cache = [];
        $this->query_cache_usage = [];
        $this->statement_cache_usage = [];
    }
    
    /**
     * Debug bilgilerini getir
     */
    public function get_debug_info() {
        return [
            'host' => $this->host,
            'dbname' => $this->dbname,
            'username' => $this->username,
            'charset' => $this->charset,
            'debug_mode' => $this->debug_mode,
            'query_cache_enabled' => $this->query_cache_enabled,
            'statement_cache_limit' => $this->statement_cache_limit,
            'query_cache_timeout' => $this->query_cache_timeout,
            'auto_adjust_chunk_size' => $this->auto_adjust_chunk_size,
            'default_chunk_size' => $this->default_chunk_size,
            'memory_stats' => $this->memory_stats
        ];
    }
    
    // Private helper metodları
    
    private function set_last_called_method($method) {
        $this->last_called_method = $method;
    }
    
    private function generate_query_cache_key($sql, $params) {
        return md5($sql . serialize($params));
    }
    
    private function get_from_query_cache($key) {
        if (isset($this->query_cache[$key])) {
            $this->query_cache_usage[$key] = time();
            return $this->query_cache[$key];
        }
        return null;
    }
    
    private function add_to_query_cache($key, $data) {
        if (count($this->query_cache) >= $this->statement_cache_limit) {
            $this->cleanup_query_cache();
        }
        
        $this->query_cache[$key] = $data;
        $this->query_cache_usage[$key] = time();
    }
    
    private function cleanup_query_cache() {
        $oldest_key = array_search(min($this->query_cache_usage), $this->query_cache_usage);
        unset($this->query_cache[$oldest_key]);
        unset($this->query_cache_usage[$oldest_key]);
    }
    
    private function check_memory_status() {
        $memory_usage = memory_get_usage(true);
        $memory_peak = memory_get_peak_usage(true);
        
        $this->memory_stats['peak_usage'] = max($this->memory_stats['peak_usage'], $memory_peak);
        
        if ($memory_usage > 201326592) { // 192MB
            $this->memory_stats['warning_count']++;
            $this->log_warning("Yüksek bellek kullanımı: " . $this->format_bytes($memory_usage));
        }
        
        if ($memory_usage > 402653184) { // 384MB
            $this->memory_stats['critical_count']++;
            $this->log_error("Kritik bellek kullanımı: " . $this->format_bytes($memory_usage));
        }
    }
    
    private function handle_error($e, $method) {
        $this->last_error = $e->getMessage();
        $this->log_error("{$method} hatası: " . $e->getMessage());
    }
    
    private function log_warning($message) {
        if ($this->debug_mode) {
            error_log("[nsql WARNING] " . $message);
        }
    }
    
    private function log_error($message) {
        error_log("[nsql ERROR] " . $message);
    }
    
    private function format_bytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
?>
