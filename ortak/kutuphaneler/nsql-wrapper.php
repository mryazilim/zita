<?php
/**
 * Zita Projesi - nsql Wrapper
 * 
 * Bu dosya nsql kütüphanesini PHP 7.4 uyumlu hale getirir.
 * nsql'in temel özelliklerini kullanır.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// nsql'in temel dosyalarını dahil et
require_once __DIR__ . '/nsql-real/nsql.php';

/**
 * nsql Wrapper Sınıfı
 * PHP 7.4 uyumlu nsql kullanımı
 */
class NsqlWrapper {
    private $nsql;
    
    public function __construct($host, $dbname, $username, $password, $charset = 'utf8mb4') {
        try {
            // nsql'i başlat
            $this->nsql = new nsql\database\nsql(
                "mysql:host={$host};dbname={$dbname};charset={$charset}",
                $username,
                $password
            );
        } catch (Exception $e) {
            throw new Exception("nsql bağlantı hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Tek satır getir
     */
    public function get_row($sql, $params = []) {
        try {
            return $this->nsql->get_row($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Çoklu satır getir
     */
    public function get_results($sql, $params = []) {
        try {
            return $this->nsql->get_results($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Veri ekle
     */
    public function insert($sql, $params = []) {
        try {
            return $this->nsql->insert($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Veri güncelle
     */
    public function update($sql, $params = []) {
        try {
            return $this->nsql->update($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Veri sil
     */
    public function delete($sql, $params = []) {
        try {
            return $this->nsql->delete($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    public function query($sql, $params = []) {
        try {
            return $this->nsql->query($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Generator ile büyük veri setlerini getir
     */
    public function get_yield($sql, $params = []) {
        try {
            return $this->nsql->get_yield($sql, $params);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Chunk ile büyük veri setlerini getir
     */
    public function get_chunk($sql, $params = []) {
        try {
            return $this->nsql->get_chunk($sql, $params);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Bağlantıyı kapat
     */
    public function close() {
        $this->nsql = null;
    }
}
?>
