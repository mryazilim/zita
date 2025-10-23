<?php

namespace nsql\database;

/**
 * Config sınıfı
 * Konfigürasyon yönetimi
 */
class config
{
    private static array $config = [
        'debug_mode' => true,
        'query_cache_enabled' => true,
        'statement_cache_limit' => 150,
        'query_cache_timeout' => 300,
        'auto_adjust_chunk_size' => true,
        'default_chunk_size' => 1000,
        'min_connections' => 2,
        'max_connections' => 10,
        'log_dir' => 'storage/logs',
        'log_max_size' => 1048576
    ];

    /**
     * Konfigürasyon değeri al
     */
    public static function get(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    /**
     * Konfigürasyon değeri ayarla
     */
    public static function set(string $key, $value): void
    {
        self::$config[$key] = $value;
    }

    /**
     * Tüm konfigürasyonu al
     */
    public static function all(): array
    {
        return self::$config;
    }

    /**
     * Konfigürasyonu sıfırla
     */
    public static function reset(): void
    {
        self::$config = [
            'debug_mode' => true,
            'query_cache_enabled' => true,
            'statement_cache_limit' => 150,
            'query_cache_timeout' => 300,
            'auto_adjust_chunk_size' => true,
            'default_chunk_size' => 1000,
            'min_connections' => 2,
            'max_connections' => 10,
            'log_dir' => 'storage/logs',
            'log_max_size' => 1048576
        ];
    }
}
?>