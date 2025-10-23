<?php
/**
 * Zita Projesi - Sektör Model Sınıfı
 * 
 * Bu sınıf sektör verileri ile ilgili işlemleri yönetir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../kutuphaneler/temel_model.php';

class SektorModel extends TemelModel {
    protected $tablo_adi = 'sektorler';
    protected $birincil_anahtar = 'id';
    protected $doldurulabilir_alanlar = [
        'sektor_adi', 'sektor_aciklama', 'aktif'
    ];
    
    /**
     * Aktif sektörleri getir
     */
    public function aktifSektorleriGetir() {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE aktif = 1 ORDER BY sektor_adi ASC";
        return $this->db->get_results($sql);
    }
    
    /**
     * Sektör adına göre ara
     */
    public function sektorAdinaGoreAra($sektor_adi) {
        $sql = "SELECT * FROM {$this->tablo_adi} 
                WHERE sektor_adi LIKE :sektor_adi 
                ORDER BY sektor_adi ASC";
        return $this->db->get_results($sql, ['sektor_adi' => "%{$sektor_adi}%"]);
    }
    
    /**
     * Sektör istatistiklerini getir
     */
    public function sektorIstatistikleri() {
        $sql = "SELECT 
                    COUNT(*) as toplam_sektor,
                    COUNT(CASE WHEN aktif = 1 THEN 1 END) as aktif_sektor,
                    COUNT(CASE WHEN aktif = 0 THEN 1 END) as pasif_sektor
                FROM {$this->tablo_adi}";
        
        return $this->db->get_row($sql);
    }
    
    /**
     * Sektörü aktif/pasif yap
     */
    public function sektorDurumDegistir($id, $aktif) {
        $sql = "UPDATE {$this->tablo_adi} SET aktif = :aktif WHERE id = :id";
        return $this->db->query($sql, ['aktif' => $aktif, 'id' => $id]);
    }
    
    /**
     * Sektör kullanım istatistiklerini getir
     */
    public function sektorKullanimIstatistikleri() {
        $sql = "SELECT 
                    s.sektor_adi,
                    COUNT(f.id) as firma_sayisi
                FROM {$this->tablo_adi} s 
                LEFT JOIN firmalar f ON s.id = f.sektor_id 
                GROUP BY s.id, s.sektor_adi 
                ORDER BY firma_sayisi DESC, s.sektor_adi ASC";
        
        return $this->db->get_results($sql);
    }
}
?>
