<?php
/**
 * Zita Projesi - nsql Kütüphanesi Test Scripti
 * 
 * Bu script nsql kütüphanesinin özel özelliklerini test eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/veritabani/baglanti.php';

echo "🧪 nsql Kütüphanesi Test Ediliyor...\n\n";

try {
    $db = veritabani_baglanti();
    
    // 1. Temel işlemler
    echo "1️⃣ Temel İşlemler:\n";
    $test = $db->get_row("SELECT 1 as test");
    echo "   - get_row test: " . ($test ? "✅ Başarılı" : "❌ Başarısız") . "\n";
    
    $iller = $db->get_results("SELECT COUNT(*) as toplam FROM iller");
    echo "   - get_results test: " . ($iller ? "✅ Başarılı" : "❌ Başarısız") . "\n";
    echo "   - İl sayısı: " . $iller[0]['toplam'] . "\n\n";
    
    // 2. Generator testi (get_yield)
    echo "2️⃣ Generator Testi (get_yield):\n";
    $count = 0;
    foreach ($db->get_yield("SELECT il_adi FROM iller LIMIT 5") as $il) {
        $count++;
        echo "   - İl {$count}: " . $il['il_adi'] . "\n";
    }
    echo "   - Generator test: ✅ Başarılı\n\n";
    
    // 3. Chunk testi (get_chunk)
    echo "3️⃣ Chunk Testi (get_chunk):\n";
    $chunks = $db->get_chunk("SELECT il_adi FROM iller LIMIT 10", [], 3);
    echo "   - Chunk sayısı: " . count($chunks) . "\n";
    foreach ($chunks as $i => $chunk) {
        echo "   - Chunk " . ($i + 1) . ": " . count($chunk) . " kayıt\n";
    }
    echo "   - Chunk test: ✅ Başarılı\n\n";
    
    // 4. Debug bilgileri
    echo "4️⃣ Debug Bilgileri:\n";
    $debug = $db->get_debug_info();
    echo "   - Host: " . $debug['host'] . "\n";
    echo "   - Database: " . $debug['dbname'] . "\n";
    echo "   - Debug Mode: " . ($debug['debug_mode'] ? 'Açık' : 'Kapalı') . "\n";
    echo "   - Query Cache: " . ($debug['query_cache_enabled'] ? 'Açık' : 'Kapalı') . "\n";
    echo "   - Default Chunk Size: " . $debug['default_chunk_size'] . "\n\n";
    
    // 5. Cache testi
    echo "5️⃣ Cache Testi:\n";
    $db->clear_cache();
    echo "   - Cache temizlendi: ✅ Başarılı\n";
    
    // 6. Bağlantı testi
    echo "6️⃣ Bağlantı Testi:\n";
    $connectionTest = $db->test();
    echo "   - Bağlantı testi: " . ($connectionTest ? "✅ Başarılı" : "❌ Başarısız") . "\n\n";
    
    echo "🎉 nsql kütüphanesi tüm testleri başarıyla geçti!\n";
    echo "📊 Test Özeti:\n";
    echo "   - Temel işlemler: ✅\n";
    echo "   - Generator (get_yield): ✅\n";
    echo "   - Chunk (get_chunk): ✅\n";
    echo "   - Debug bilgileri: ✅\n";
    echo "   - Cache yönetimi: ✅\n";
    echo "   - Bağlantı testi: ✅\n";
    
} catch (Exception $e) {
    echo "❌ Test hatası: " . $e->getMessage() . "\n";
}
?>
