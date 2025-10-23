<?php
/**
 * Zita Projesi - nsql v1.4 Test
 * 
 * nsql v1.4'ün yeni özelliklerini test eder
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 nsql v1.4 Test Sayfası</h1>";

try {
    // Veritabanı bağlantısını dahil et
    require_once __DIR__ . '/ortak/veritabani/baglanti.php';
    
    echo "<p>✅ Veritabanı bağlantı dosyası yüklendi</p>";
    
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    echo "<p>✅ Veritabanı bağlantısı başarılı</p>";
    echo "<p>📊 Bağlantı türü: " . get_class($db) . "</p>";
    
    // Temel sorgu testi
    echo "<h2>📊 Temel Sorgular</h2>";
    
    $il_sayisi = $db->get_row("SELECT COUNT(*) as toplam FROM iller");
    echo "<p>📈 İl sayısı: " . $il_sayisi['toplam'] . "</p>";
    
    $ilce_sayisi = $db->get_row("SELECT COUNT(*) as toplam FROM ilceler");
    echo "<p>📈 İlçe sayısı: " . $ilce_sayisi['toplam'] . "</p>";
    
    $sektor_sayisi = $db->get_row("SELECT COUNT(*) as toplam FROM sektorler");
    echo "<p>📈 Sektör sayısı: " . $sektor_sayisi['toplam'] . "</p>";
    
    // nsql v1.4 yeni özellikler
    echo "<h2>🚀 nsql v1.4 Yeni Özellikler</h2>";
    
    // Debug bilgileri
    try {
        $debug_info = $db->get_debug_info();
        echo "<p>🔍 Toplam sorgu sayısı: " . $debug_info['total_queries'] . "</p>";
        echo "<p>🔍 Cache hit sayısı: " . $debug_info['cache_hits'] . "</p>";
        echo "<p>🔍 Cache miss sayısı: " . $debug_info['cache_misses'] . "</p>";
        echo "<p>🔍 Ortalama sorgu süresi: " . round($debug_info['avg_query_time'], 4) . " ms</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Debug bilgileri alınamadı: " . $e->getMessage() . "</p>";
    }
    
    // Cache testi
    echo "<h2>💾 Cache Testi</h2>";
    $start_time = microtime(true);
    $db->get_results("SELECT * FROM iller WHERE id < 10");
    $end_time = microtime(true);
    echo "<p>⏱️ İlk sorgu süresi: " . round(($end_time - $start_time) * 1000, 2) . " ms</p>";
    
    $start_time = microtime(true);
    $db->get_results("SELECT * FROM iller WHERE id < 10");
    $end_time = microtime(true);
    echo "<p>⏱️ Cache'li sorgu süresi: " . round(($end_time - $start_time) * 1000, 2) . " ms</p>";
    
    // Generator testi (get_yield)
    echo "<h2>🔄 Generator Testi (get_yield)</h2>";
    $count = 0;
    foreach ($db->get_yield("SELECT id, il_adi FROM iller LIMIT 5") as $il) {
        $count++;
        echo "<p>🏙️ " . $il['id'] . " - " . $il['il_adi'] . "</p>";
    }
    echo "<p>📊 Toplam işlenen kayıt: " . $count . "</p>";
    
    // Chunk testi (get_chunk)
    echo "<h2>📦 Chunk Testi (get_chunk)</h2>";
    $chunk_count = 0;
    foreach ($db->get_chunk("SELECT id, il_adi FROM iller", 3) as $chunk) {
        $chunk_count++;
        echo "<p>📦 Chunk " . $chunk_count . " (3 kayıt):</p>";
        foreach ($chunk as $il) {
            echo "<p>&nbsp;&nbsp;🏙️ " . $il['id'] . " - " . $il['il_adi'] . "</p>";
        }
    }
    echo "<p>📊 Toplam chunk sayısı: " . $chunk_count . "</p>";
    
    // Connection pool testi
    echo "<h2>🏊 Connection Pool Testi</h2>";
    try {
        $pool_stats = $db->get_pool_stats();
        echo "<p>🏊 Mevcut bağlantı sayısı: " . $pool_stats['current_connections'] . "</p>";
        echo "<p>🏊 Kullanılabilir bağlantı sayısı: " . $pool_stats['available_connections'] . "</p>";
        echo "<p>🏊 Minimum bağlantı sayısı: " . $pool_stats['min_connections'] . "</p>";
        echo "<p>🏊 Maksimum bağlantı sayısı: " . $pool_stats['max_connections'] . "</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Connection pool bilgileri alınamadı: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>✅ Tüm testler başarılı!</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Hata: " . $e->getMessage() . "</h2>";
    echo "<pre>📋 Hata detayı: " . $e->getTraceAsString() . "</pre>";
}
?>
