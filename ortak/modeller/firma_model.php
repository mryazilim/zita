<?php
/**
 * Zita Projesi - Firma Model Sınıfı
 * 
 * Bu sınıf firma verileri ile ilgili işlemleri yönetir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../kutuphaneler/temel_model.php';

class FirmaModel extends TemelModel {
    protected $tablo_adi = 'firmalar';
    protected $birincil_anahtar = 'id';
    protected $doldurulabilir_alanlar = [
        'firma_adi', 'unvan', 'vkn_tc', 'vergi_dairesi', 'telefon', 'email', 
        'adres', 'il_id', 'ilce_id', 'sektor_id', 'aktif'
    ];
    
    /**
     * Aktif firmaları getir
     */
    public function aktifFirmalariGetir() {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.aktif = 1 
                ORDER BY f.firma_adi ASC";
        return $this->db->get_results($sql);
    }
    
    /**
     * VKN/TC'ye göre firma getir
     */
    public function vknTcyeGoreGetir($vkn_tc) {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.vkn_tc = :vkn_tc";
        return $this->db->get_row($sql, ['vkn_tc' => $vkn_tc]);
    }
    
    /**
     * E-posta'ya göre firma getir
     */
    public function emailGoreGetir($email) {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.email = :email";
        return $this->db->get_row($sql, ['email' => $email]);
    }
    
    /**
     * Firma adına göre ara
     */
    public function firmaAdinaGoreAra($firma_adi) {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.firma_adi LIKE :firma_adi 
                ORDER BY f.firma_adi ASC";
        return $this->db->get_results($sql, ['firma_adi' => "%{$firma_adi}%"]);
    }
    
    /**
     * Sektöre göre firmaları getir
     */
    public function sektoreGoreGetir($sektor_id) {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.sektor_id = :sektor_id AND f.aktif = 1 
                ORDER BY f.firma_adi ASC";
        return $this->db->get_results($sql, ['sektor_id' => $sektor_id]);
    }
    
    /**
     * İle göre firmaları getir
     */
    public function ileGoreGetir($il_id) {
        $sql = "SELECT f.*, i.il_adi, ic.ilce_adi, s.sektor_adi 
                FROM {$this->tablo_adi} f 
                LEFT JOIN iller i ON f.il_id = i.id 
                LEFT JOIN ilceler ic ON f.ilce_id = ic.id 
                LEFT JOIN sektorler s ON f.sektor_id = s.id 
                WHERE f.il_id = :il_id AND f.aktif = 1 
                ORDER BY f.firma_adi ASC";
        return $this->db->get_results($sql, ['il_id' => $il_id]);
    }
    
    /**
     * Firma istatistiklerini getir
     */
    public function firmaIstatistikleri() {
        $sql = "SELECT 
                    COUNT(*) as toplam_firma,
                    COUNT(CASE WHEN aktif = 1 THEN 1 END) as aktif_firma,
                    COUNT(CASE WHEN aktif = 0 THEN 1 END) as pasif_firma
                FROM {$this->tablo_adi}";
        
        return $this->db->get_row($sql);
    }
    
    /**
     * Sektör bazında firma sayılarını getir
     */
    public function sektorBazindaFirmaSayilari() {
        $sql = "SELECT 
                    s.sektor_adi,
                    COUNT(f.id) as firma_sayisi
                FROM sektorler s 
                LEFT JOIN {$this->tablo_adi} f ON s.id = f.sektor_id AND f.aktif = 1
                GROUP BY s.id, s.sektor_adi 
                ORDER BY firma_sayisi DESC, s.sektor_adi ASC";
        
        return $this->db->get_results($sql);
    }
    
    /**
     * İl bazında firma sayılarını getir
     */
    public function ilBazindaFirmaSayilari() {
        $sql = "SELECT 
                    i.il_adi,
                    i.plaka_kodu,
                    COUNT(f.id) as firma_sayisi
                FROM iller i 
                LEFT JOIN {$this->tablo_adi} f ON i.id = f.il_id AND f.aktif = 1
                GROUP BY i.id, i.il_adi, i.plaka_kodu 
                ORDER BY firma_sayisi DESC, i.il_adi ASC";
        
        return $this->db->get_results($sql);
    }
    
    /**
     * Firma kayıt tarihine göre istatistikler
     */
    public function kayitTarihiIstatistikleri($baslangic_tarihi = null, $bitis_tarihi = null) {
        $sql = "SELECT 
                    DATE(olusturma_tarihi) as tarih,
                    COUNT(*) as kayit_sayisi
                FROM {$this->tablo_adi}";
        
        $params = [];
        if ($baslangic_tarihi && $bitis_tarihi) {
            $sql .= " WHERE DATE(olusturma_tarihi) BETWEEN :baslangic AND :bitis";
            $params['baslangic'] = $baslangic_tarihi;
            $params['bitis'] = $bitis_tarihi;
        }
        
        $sql .= " GROUP BY DATE(olusturma_tarihi) ORDER BY tarih DESC";
        
        return $this->db->get_results($sql, $params);
    }
}
?>
