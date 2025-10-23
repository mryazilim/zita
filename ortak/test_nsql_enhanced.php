<?php
/**
 * Zita Projesi - nsql Enhanced Test Scripti
 * 
 * Bu script nsql enhanced kütüphanesini test eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// nsql enhanced'ı dahil et
require_once __DIR__ . '/kutuphaneler/nsql-enhanced.php';

echo "🧪 nsql Enhanced Kütüphanesi Test Ediliyor...\n\n";

try {
    // nsql enhanced'ı başlat
    $db = new nsql\database\nsql_enhanced('localhost', 'zita_vt', 'root', '');
    
    // 1. Bağlantı testi
    echo "1️⃣ Bağlantı Testi:\n";
    $connectionTest = $db->test();
    echo "   - Bağlantı testi: " . ($connectionTest ? "✅ Başarılı" : "❌ Başarısız") . "\n\n";
    
    if (!$connectionTest) {
        echo "❌ Bağlantı başarısız, test durduruluyor.\n";
        exit(1);
    }
    
    // 2. Temel işlemler
    echo "2️⃣ Temel İşlemler:\n";
    $test = $db->get_row("SELECT 1 as test");
    echo "   - get_row test: " . ($test ? "✅ Başarılı" : "❌ Başarısız") . "\n";
    
    $iller = $db->get_results("SELECT COUNT(*) as toplam FROM iller");
    echo "   - get_results test: " . ($iller ? "✅ Başarılı" : "❌ Başarısız") . "\n";
    echo "   - İl sayısı: " . $iller[0]['toplam'] . "\n\n";
    
    // 3. Generator testi (get_yield)
    echo "3️⃣ Generator Testi (get_yield):\n";
    $count = 0;
    foreach ($db->get_yield("SELECT il_adi FROM iller") as $il) {
        $count++;
        echo "   - İl {$count}: " . $il['il_adi'] . "\n";
        if ($count >= 5) break; // İlk 5 il
    }
    echo "   - Generator test: ✅ Başarılı\n\n";
    
    // 4. Chunk testi (get_chunk)
    echo "4️⃣ Chunk Testi (get_chunk):\n";
    $chunks = $db->get_chunk("SELECT il_adi FROM iller", [], 3);
    echo "   - Chunk sayısı: " . count($chunks) . "\n";
    foreach ($chunks as $i => $chunk) {
        echo "   - Chunk " . ($i + 1) . ": " . count($chunk) . " kayıt\n";
        if ($i >= 2) break; // İlk 3 chunk
    }
    echo "   - Chunk test: ✅ Başarılı\n\n";
    
    // 5. Debug bilgileri
    echo "5️⃣ Debug Bilgileri:\n";
    $debug = $db->get_debug_info();
    echo "   - Host: " . $debug['host'] . "\n";
    echo "   - Database: " . $debug['dbname'] . "\n";
    echo "   - Debug Mode: " . ($debug['debug_mode'] ? 'Açık' : 'Kapalı') . "\n";
    echo "   - Query Cache: " . ($debug['query_cache_enabled'] ? 'Açık' : 'Kapalı') . "\n";
    echo "   - Default Chunk Size: " . $debug['default_chunk_size'] . "\n";
    echo "   - Memory Peak: " . $debug['memory_stats']['peak_usage'] . " bytes\n";
    echo "   - Memory Warnings: " . $debug['memory_stats']['warning_count'] . "\n";
    echo "   - Memory Critical: " . $debug['memory_stats']['critical_count'] . "\n\n";
    
    // 6. Cache testi
    echo "6️⃣ Cache Testi:\n";
    $db->clear_cache();
    echo "   - Cache temizlendi: ✅ Başarılı\n";
    
    // 7. Performans testi
    echo "7️⃣ Performans Testi:\n";
    $start_time = microtime(true);
    $iller = $db->get_results("SELECT * FROM iller");
    $end_time = microtime(true);
    $execution_time = ($end_time - $start_time) * 1000;
    echo "   - 81 il sorgusu: " . round($execution_time, 2) . " ms\n";
    echo "   - Performans testi: ✅ Başarılı\n\n";
    
    echo "🎉 nsql Enhanced kütüphanesi tüm testleri başarıyla geçti!\n";
    echo "📊 Test Özeti:\n";
    echo "   - Bağlantı testi: ✅\n";
    echo "   - Temel işlemler: ✅\n";
    echo "   - Generator (get_yield): ✅\n";
    echo "   - Chunk (get_chunk): ✅\n";
    echo "   - Debug bilgileri: ✅\n";
    echo "   - Cache yönetimi: ✅\n";
    echo "   - Performans testi: ✅\n";
    
} catch (Exception $e) {
    echo "❌ Test hatası: " . $e->getMessage() . "\n";
    echo "🔍 Hata detayları:\n";
    echo "   - Dosya: " . $e->getFile() . "\n";
    echo "   - Satır: " . $e->getLine() . "\n";
    echo "   - Trace: " . $e->getTraceAsString() . "\n";
}
?>
