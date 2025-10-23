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
