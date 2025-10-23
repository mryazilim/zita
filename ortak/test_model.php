<?php
/**
 * Zita Projesi - Model Test Scripti
 * 
 * Bu script model sınıflarını test eder.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/kutuphaneler/veritabani_sinifi.php';
require_once __DIR__ . '/modeller/il_model.php';
require_once __DIR__ . '/modeller/ilce_model.php';
require_once __DIR__ . '/modeller/sektor_model.php';
require_once __DIR__ . '/modeller/firma_model.php';

echo "🧪 Model Sınıfları Test Ediliyor...\n\n";

try {
    // İl Model Testi
    echo "1️⃣ İl Model Testi:\n";
    $ilModel = new IlModel();
    $iller = $ilModel->tumIlleriGetir();
    echo "   - Toplam il sayısı: " . count($iller) . "\n";
    
    $ankara = $ilModel->plakaKodunaGoreGetir('06');
    echo "   - Ankara plaka kodu: " . ($ankara ? $ankara['il_adi'] : 'Bulunamadı') . "\n";
    
    $istatistik = $ilModel->ilIstatistikleri();
    echo "   - İl istatistikleri: " . $istatistik['toplam_il'] . " il, " . $istatistik['toplam_plaka'] . " plaka\n\n";
    
    // İlçe Model Testi
    echo "2️⃣ İlçe Model Testi:\n";
    $ilceModel = new IlceModel();
    $ankaraIlceleri = $ilceModel->ilIdyeGoreGetir(6); // Ankara ID = 6
    echo "   - Ankara ilçe sayısı: " . count($ankaraIlceleri) . "\n";
    
    $istatistik = $ilceModel->ilceIstatistikleri();
    echo "   - İlçe istatistikleri: " . $istatistik['toplam_ilce'] . " ilçe, " . $istatistik['toplam_il'] . " il\n\n";
    
    // Sektör Model Testi
    echo "3️⃣ Sektör Model Testi:\n";
    $sektorModel = new SektorModel();
    $sektorler = $sektorModel->aktifSektorleriGetir();
    echo "   - Aktif sektör sayısı: " . count($sektorler) . "\n";
    
    $istatistik = $sektorModel->sektorIstatistikleri();
    echo "   - Sektör istatistikleri: " . $istatistik['toplam_sektor'] . " toplam, " . $istatistik['aktif_sektor'] . " aktif\n\n";
    
    // Firma Model Testi
    echo "4️⃣ Firma Model Testi:\n";
    $firmaModel = new FirmaModel();
    $firmalar = $firmaModel->aktifFirmalariGetir();
    echo "   - Aktif firma sayısı: " . count($firmalar) . "\n";
    
    $istatistik = $firmaModel->firmaIstatistikleri();
    echo "   - Firma istatistikleri: " . $istatistik['toplam_firma'] . " toplam, " . $istatistik['aktif_firma'] . " aktif\n\n";
    
    echo "✅ Tüm model sınıfları başarıyla test edildi!\n";
    
} catch (Exception $e) {
    echo "❌ Test hatası: " . $e->getMessage() . "\n";
}
?>
