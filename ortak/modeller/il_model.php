<?php
/**
 * Zita Projesi - İl Model Sınıfı
 * 
 * Bu sınıf il verileri ile ilgili işlemleri yönetir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../kutuphaneler/temel_model.php';

class IlModel extends TemelModel {
    protected $tablo_adi = 'iller';
    protected $birincil_anahtar = 'id';
    protected $doldurulabilir_alanlar = [
        'il_adi', 'plaka_kodu'
    ];
    
    /**
     * Tüm illeri getir
     */
    public function tumIlleriGetir() {
        $sql = "SELECT * FROM {$this->tablo_adi} ORDER BY il_adi ASC";
        return $this->db->get_results($sql);
    }
    
    /**
     * Plaka koduna göre il getir
     */
    public function plakaKodunaGoreGetir($plaka_kodu) {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE plaka_kodu = :plaka_kodu";
        return $this->db->get_row($sql, ['plaka_kodu' => $plaka_kodu]);
    }
    
    /**
     * İl adına göre ara
     */
    public function ilAdinaGoreAra($il_adi) {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE il_adi LIKE :il_adi ORDER BY il_adi ASC";
        return $this->db->get_results($sql, ['il_adi' => "%{$il_adi}%"]);
    }
    
    /**
     * İl istatistiklerini getir
     */
    public function ilIstatistikleri() {
        $sql = "SELECT 
                    COUNT(*) as toplam_il,
                    COUNT(DISTINCT plaka_kodu) as toplam_plaka
                FROM {$this->tablo_adi}";
        
        return $this->db->get_row($sql);
    }
}
?>
