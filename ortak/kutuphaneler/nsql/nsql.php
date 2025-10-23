<?php
/**
 * Zita Projesi - nsql Benzeri Veritabanı Kütüphanesi
 * 
 * Bu kütüphane nsql benzeri API sağlar.
 * PDO tabanlı güvenli veritabanı işlemleri.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

namespace nsql\database;

class nsql {
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
    
    private $query_cache = [];
    private $statement_cache = [];
    
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
        $this->ensure_connection();
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("get_row hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Çoklu satır getir
     */
    public function get_results($sql, $params = []) {
        $this->ensure_connection();
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("get_results hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Veri ekle
     */
    public function insert($table, $data) {
        $this->ensure_connection();
        
        try {
            $columns = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("insert hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Veri güncelle
     */
    public function update($table, $data, $where, $whereParams = []) {
        $this->ensure_connection();
        
        try {
            $setClause = [];
            foreach ($data as $key => $value) {
                $setClause[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setClause);
            
            $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
            $stmt = $this->pdo->prepare($sql);
            
            $allParams = array_merge($data, $whereParams);
            $stmt->execute($allParams);
            
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("update hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Veri sil
     */
    public function delete($table, $where, $params = []) {
        $this->ensure_connection();
        
        try {
            $sql = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("delete hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    public function query($sql, $params = []) {
        $this->ensure_connection();
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("query hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Generator ile büyük veri setlerini getir
     */
    public function get_yield($sql, $params = [], $chunk_size = null) {
        $this->ensure_connection();
        
        if ($chunk_size === null) {
            $chunk_size = $this->default_chunk_size;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            while ($row = $stmt->fetch()) {
                yield $row;
            }
        } catch (\PDOException $e) {
            if ($this->debug_mode) {
                throw new \Exception("get_yield hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Chunk ile büyük veri setlerini getir
     */
    public function get_chunk($sql, $params = [], $chunk_size = null) {
        $this->ensure_connection();
        
        if ($chunk_size === null) {
            $chunk_size = $this->default_chunk_size;
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
            if ($this->debug_mode) {
                throw new \Exception("get_chunk hatası: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Bağlantıyı test et
     */
    public function test() {
        try {
            $result = $this->get_row("SELECT 1 as test");
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Bağlantıyı kapat
     */
    public function close() {
        $this->pdo = null;
    }
    
    /**
     * Cache'i temizle
     */
    public function clear_cache() {
        $this->query_cache = [];
        $this->statement_cache = [];
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
            'default_chunk_size' => $this->default_chunk_size
        ];
    }
}
?>
