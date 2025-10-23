<?php

namespace nsql\database\security;

/**
 * Session Manager sınıfı
 * Oturum yönetimi
 */
class session_manager
{
    private static bool $started = false;

    /**
     * Oturumu başlat
     */
    public static function start(): void
    {
        if (!self::$started) {
            session_start();
            self::$started = true;
        }
    }

    /**
     * Oturum değeri al
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Oturum değeri ayarla
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Oturum değeri sil
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Oturumu temizle
     */
    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }

    /**
     * Oturumu sonlandır
     */
    public static function destroy(): void
    {
        if (self::$started) {
            session_destroy();
            self::$started = false;
        }
    }
}
?>
