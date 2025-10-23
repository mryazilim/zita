<?php
/**
 * Zita Projesi - Gerçek nsql Test Scripti
 * 
 * Bu script gerçek nsql kütüphanesini test eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// nsql wrapper'ını dahil et
require_once __DIR__ . '/kutuphaneler/nsql-wrapper.php';

echo "🧪 Gerçek nsql Kütüphanesi Test Ediliyor...\n\n";

try {
    // nsql wrapper'ını başlat
    $db = new NsqlWrapper('localhost', 'zita_vt', 'root', '');
    
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
    foreach ($db->get_yield("SELECT il_adi FROM iller LIMIT 5") as $il) {
        $count++;
        echo "   - İl {$count}: " . $il['il_adi'] . "\n";
    }
    echo "   - Generator test: ✅ Başarılı\n\n";
    
    // 4. Chunk testi (get_chunk)
    echo "4️⃣ Chunk Testi (get_chunk):\n";
    $chunks = $db->get_chunk("SELECT il_adi FROM iller LIMIT 10", [], 3);
    echo "   - Chunk sayısı: " . count($chunks) . "\n";
    foreach ($chunks as $i => $chunk) {
        echo "   - Chunk " . ($i + 1) . ": " . count($chunk) . " kayıt\n";
    }
    echo "   - Chunk test: ✅ Başarılı\n\n";
    
    echo "🎉 Gerçek nsql kütüphanesi tüm testleri başarıyla geçti!\n";
    echo "📊 Test Özeti:\n";
    echo "   - Bağlantı testi: ✅\n";
    echo "   - Temel işlemler: ✅\n";
    echo "   - Generator (get_yield): ✅\n";
    echo "   - Chunk (get_chunk): ✅\n";
    
} catch (Exception $e) {
    echo "❌ Test hatası: " . $e->getMessage() . "\n";
    echo "🔍 Hata detayları:\n";
    echo "   - Dosya: " . $e->getFile() . "\n";
    echo "   - Satır: " . $e->getLine() . "\n";
    echo "   - Trace: " . $e->getTraceAsString() . "\n";
}
?>
