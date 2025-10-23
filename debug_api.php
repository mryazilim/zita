<?php
/**
 * Zita Projesi - API Debug
 * 
 * API hatalarını debug etmek için
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 API Debug Sayfası</h1>";

echo "<h2>📋 PHP Bilgileri</h2>";
echo "<p>PHP Sürümü: " . phpversion() . "</p>";
echo "<p>Hata Raporlama: " . (error_reporting() ? 'Açık' : 'Kapalı') . "</p>";

echo "<h2>🧪 Veritabanı Bağlantı Testi</h2>";

try {
    // Veritabanı bağlantısını dahil et
    require_once __DIR__ . '/ortak/veritabani/baglanti.php';
    
    echo "<p>✅ Veritabanı bağlantı dosyası yüklendi</p>";
    
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    echo "<p>✅ Veritabanı bağlantısı başarılı</p>";
    echo "<p>📊 Bağlantı türü: " . get_class($db) . "</p>";
    
    // Basit sorgu testi
    $il_sayisi = $db->get_row("SELECT COUNT(*) as toplam FROM iller");
    echo "<p>📈 İl sayısı: " . $il_sayisi['toplam'] . "</p>";
    
    // nsql v1.4 debug bilgileri
    echo "<h2>🔍 nsql v1.4 Debug Bilgileri</h2>";
    $debug_info = $db->get_debug_info();
    echo "<p>🔍 Toplam sorgu sayısı: " . $debug_info['total_queries'] . "</p>";
    echo "<p>🔍 Cache hit sayısı: " . $debug_info['cache_hits'] . "</p>";
    echo "<p>🔍 Cache miss sayısı: " . $debug_info['cache_misses'] . "</p>";
    echo "<p>🔍 Ortalama sorgu süresi: " . round($debug_info['avg_query_time'], 4) . " ms</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Hata: " . $e->getMessage() . "</p>";
    echo "<pre>📋 Hata detayı: " . $e->getTraceAsString() . "</pre>";
}

echo "<h2>🔗 API Test Linkleri</h2>";
echo "<p><a href='tanitim/api/iller.php' target='_blank'>İller API</a></p>";
echo "<p><a href='tanitim/api/sektorler.php' target='_blank'>Sektörler API</a></p>";
echo "<p><a href='tanitim/api/ilceler.php?il_id=1' target='_blank'>İlçeler API</a></p>";
echo "<p><a href='test_nsql_web.html' target='_blank'>Test Sayfası</a></p>";
echo "<p><a href='ortak/kutuphaneler/nsql/README.md' target='_blank'>nsql v1.4 Dokümantasyonu</a></p>";
?>
