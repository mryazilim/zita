<?php
/**
 * Zita Projesi - nsql Autoloader
 * 
 * nsql kütüphanesi için otomatik yükleme
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// nsql autoloader
spl_autoload_register(function ($class) {
    // nsql namespace'ini kontrol et
    if (strpos($class, 'nsql\\database\\') === 0) {
        $class = str_replace('nsql\\database\\', '', $class);
        
        // Trait dosyaları için
        if (strpos($class, 'traits\\') === 0) {
            $trait = str_replace('traits\\', '', $class);
            $file = __DIR__ . '/traits/' . $trait . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
        
        // Ana sınıf dosyaları için
        $file = __DIR__ . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Temel sınıfları yükle
$core_files = [
    'config.php',
    'connection_pool.php'
];

foreach ($core_files as $core_file) {
    $core_path = __DIR__ . '/' . $core_file;
    if (file_exists($core_path)) {
        require_once $core_path;
    }
}

// Security sınıflarını yükle
$security_files = [
    'session_manager.php',
    'query_analyzer.php'
];

foreach ($security_files as $security_file) {
    $security_path = __DIR__ . '/security/' . $security_file;
    if (file_exists($security_path)) {
        require_once $security_path;
    }
}

// Trait dosyalarını manuel olarak yükle
$trait_files = [
    'cache_trait.php',
    'connection_trait.php', 
    'debug_trait.php',
    'query_analyzer_trait.php',
    'query_parameter_trait.php',
    'statement_cache_trait.php',
    'transaction_trait.php'
];

foreach ($trait_files as $trait_file) {
    $trait_path = __DIR__ . '/traits/' . $trait_file;
    if (file_exists($trait_path)) {
        require_once $trait_path;
    }
}
?>
