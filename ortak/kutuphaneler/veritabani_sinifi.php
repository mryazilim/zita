<?php
/**
 * Zita Projesi - Veritabanı Sınıfı
 * 
 * Bu sınıf PDO tabanlı güvenli veritabanı işlemlerini sağlar.
 * nsql kütüphanesi yerine kullanılır.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

class VeritabaniSinifi {
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset;
    
    public function __construct($host = 'localhost', $dbname = 'zita_vt', $username = 'root', $password = '', $charset = 'utf8mb4') {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        
        $this->baglan();
    }
    
    /**
     * Veritabanına bağlan
     */
    private function baglan() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            throw new Exception("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Tek satır getir
     */
    public function get_row($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Sorgu hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Çoklu satır getir
     */
    public function get_results($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Sorgu hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Veri ekle
     */
    public function insert($table, $data) {
        try {
            $columns = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Ekleme hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Veri güncelle
     */
    public function update($table, $data, $where, $whereParams = []) {
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
        } catch (PDOException $e) {
            throw new Exception("Güncelleme hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Veri sil
     */
    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Silme hatası: " . $e->getMessage());
        }
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Sorgu hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Bağlantıyı test et
     */
    public function test() {
        try {
            $result = $this->get_row("SELECT 1 as test");
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Bağlantıyı kapat
     */
    public function kapat() {
        $this->pdo = null;
    }
}
?>
