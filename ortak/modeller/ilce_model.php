<?php
/**
 * Zita Projesi - İlçe Model Sınıfı
 * 
 * Bu sınıf ilçe verileri ile ilgili işlemleri yönetir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../kutuphaneler/temel_model.php';

class IlceModel extends TemelModel {
    protected $tablo_adi = 'ilceler';
    protected $birincil_anahtar = 'id';
    protected $doldurulabilir_alanlar = [
        'il_id', 'ilce_adi'
    ];
    
    /**
     * İl ID'sine göre ilçeleri getir
     */
    public function ilIdyeGoreGetir($il_id) {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE il_id = :il_id ORDER BY ilce_adi ASC";
        return $this->db->get_results($sql, ['il_id' => $il_id]);
    }
    
    /**
     * İl adına göre ilçeleri getir
     */
    public function ilAdinaGoreGetir($il_adi) {
        $sql = "SELECT ic.*, i.il_adi 
                FROM {$this->tablo_adi} ic 
                INNER JOIN iller i ON ic.il_id = i.id 
                WHERE i.il_adi = :il_adi 
                ORDER BY ic.ilce_adi ASC";
        return $this->db->get_results($sql, ['il_adi' => $il_adi]);
    }
    
    /**
     * İlçe adına göre ara
     */
    public function ilceAdinaGoreAra($ilce_adi) {
        $sql = "SELECT ic.*, i.il_adi 
                FROM {$this->tablo_adi} ic 
                INNER JOIN iller i ON ic.il_id = i.id 
                WHERE ic.ilce_adi LIKE :ilce_adi 
                ORDER BY i.il_adi ASC, ic.ilce_adi ASC";
        return $this->db->get_results($sql, ['ilce_adi' => "%{$ilce_adi}%"]);
    }
    
    /**
     * İl ve ilçe bilgilerini birlikte getir
     */
    public function ilIlceBirlikteGetir($il_id = null) {
        $sql = "SELECT ic.*, i.il_adi, i.plaka_kodu 
                FROM {$this->tablo_adi} ic 
                INNER JOIN iller i ON ic.il_id = i.id";
        
        $params = [];
        if ($il_id) {
            $sql .= " WHERE ic.il_id = :il_id";
            $params['il_id'] = $il_id;
        }
        
        $sql .= " ORDER BY i.il_adi ASC, ic.ilce_adi ASC";
        
        return $this->db->get_results($sql, $params);
    }
    
    /**
     * İlçe istatistiklerini getir
     */
    public function ilceIstatistikleri() {
        $sql = "SELECT 
                    COUNT(*) as toplam_ilce,
                    COUNT(DISTINCT il_id) as toplam_il
                FROM {$this->tablo_adi}";
        
        return $this->db->get_row($sql);
    }
    
    /**
     * İl bazında ilçe sayılarını getir
     */
    public function ilBazindaIlceSayilari() {
        $sql = "SELECT 
                    i.il_adi,
                    i.plaka_kodu,
                    COUNT(ic.id) as ilce_sayisi
                FROM iller i 
                LEFT JOIN {$this->tablo_adi} ic ON i.id = ic.il_id 
                GROUP BY i.id, i.il_adi, i.plaka_kodu 
                ORDER BY ilce_sayisi DESC, i.il_adi ASC";
        
        return $this->db->get_results($sql);
    }
}
?>
