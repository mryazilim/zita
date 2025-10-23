<?php

namespace nsql\database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Connection Pool sınıfı
 * Veritabanı bağlantı havuzu yönetimi
 */
class connection_pool
{
    private static array $pool = [];
    private static array $config = [];
    private static int $min_connections = 2;
    private static int $max_connections = 10;
    private static int $current_connections = 0;
    private static bool $initialized = false;

    /**
     * Connection pool'u başlat
     */
    public static function initialize(array $config, int $min_connections = 2, int $max_connections = 10): void
    {
        self::$config = $config;
        self::$min_connections = $min_connections;
        self::$max_connections = $max_connections;
        self::$initialized = true;
        
        // Minimum bağlantı sayısı kadar bağlantı oluştur
        for ($i = 0; $i < $min_connections; $i++) {
            self::create_connection();
        }
    }

    /**
     * Yeni bağlantı oluştur
     */
    private static function create_connection(): PDO
    {
        if (self::$current_connections >= self::$max_connections) {
            throw new RuntimeException('Maksimum bağlantı sayısına ulaşıldı');
        }

        try {
            $dsn = "mysql:host={self::$config['host']};dbname={self::$config['dbname']};charset={self::$config['charset']}";
            $pdo = new PDO($dsn, self::$config['username'], self::$config['password'], self::$config['options']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            self::$pool[] = $pdo;
            self::$current_connections++;
            
            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException("Bağlantı oluşturulamadı: " . $e->getMessage());
        }
    }

    /**
     * Bağlantı al
     */
    public static function get_connection(): PDO
    {
        if (!self::$initialized) {
            throw new RuntimeException('Connection pool başlatılmamış');
        }

        // Mevcut bağlantı varsa onu döndür
        if (!empty(self::$pool)) {
            return array_pop(self::$pool);
        }

        // Yeni bağlantı oluştur
        if (self::$current_connections < self::$max_connections) {
            return self::create_connection();
        }

        // Bağlantı yoksa bekle ve tekrar dene
        usleep(10000); // 10ms bekle
        return self::get_connection();
    }

    /**
     * Bağlantıyı geri ver
     */
    public static function release_connection(PDO $connection): void
    {
        if (count(self::$pool) < self::$max_connections) {
            self::$pool[] = $connection;
        } else {
            // Fazla bağlantıyı kapat
            $connection = null;
            self::$current_connections--;
        }
    }

    /**
     * Pool istatistikleri
     */
    public static function get_stats(): array
    {
        return [
            'current_connections' => self::$current_connections,
            'available_connections' => count(self::$pool),
            'min_connections' => self::$min_connections,
            'max_connections' => self::$max_connections,
            'initialized' => self::$initialized
        ];
    }

    /**
     * Pool'u temizle
     */
    public static function cleanup(): void
    {
        foreach (self::$pool as $connection) {
            $connection = null;
        }
        self::$pool = [];
        self::$current_connections = 0;
        self::$initialized = false;
    }
}
?>
