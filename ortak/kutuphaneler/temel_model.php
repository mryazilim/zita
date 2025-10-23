<?php
/**
 * Zita Projesi - Temel Model Sınıfı
 * 
 * Bu sınıf tüm model sınıfları için temel işlevleri sağlar.
 * CRUD işlemleri ve veritabanı işlemleri için kullanılır.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/veritabani_sinifi.php';

abstract class TemelModel {
    protected $db;
    protected $tablo_adi;
    protected $birincil_anahtar = 'id';
    protected $doldurulabilir_alanlar = [];
    protected $guvenli_alanlar = [];
    
    public function __construct() {
        $this->db = new VeritabaniSinifi();
    }
    
    /**
     * Tüm kayıtları getir
     */
    public function tumunuGetir($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->tablo_adi}";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->get_results($sql);
    }
    
    /**
     * ID'ye göre tek kayıt getir
     */
    public function idyeGoreGetir($id) {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE {$this->birincil_anahtar} = :id";
        return $this->db->get_row($sql, ['id' => $id]);
    }
    
    /**
     * Koşula göre kayıtları getir
     */
    public function kosulaGoreGetir($kosul, $params = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->tablo_adi} WHERE {$kosul}";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->get_results($sql, $params);
    }
    
    /**
     * Yeni kayıt ekle
     */
    public function ekle($veri) {
        // Güvenli alanları filtrele
        $guvenli_veri = $this->guvenliVeriFiltrele($veri);
        
        return $this->db->insert($this->tablo_adi, $guvenli_veri);
    }
    
    /**
     * Kayıt güncelle
     */
    public function guncelle($id, $veri) {
        // Güvenli alanları filtrele
        $guvenli_veri = $this->guvenliVeriFiltrele($veri);
        
        return $this->db->update(
            $this->tablo_adi, 
            $guvenli_veri, 
            "{$this->birincil_anahtar} = :id", 
            ['id' => $id]
        );
    }
    
    /**
     * Kayıt sil
     */
    public function sil($id) {
        return $this->db->delete(
            $this->tablo_adi, 
            "{$this->birincil_anahtar} = :id", 
            ['id' => $id]
        );
    }
    
    /**
     * Toplam kayıt sayısını getir
     */
    public function toplamKayitSayisi($kosul = null, $params = []) {
        $sql = "SELECT COUNT(*) as toplam FROM {$this->tablo_adi}";
        
        if ($kosul) {
            $sql .= " WHERE {$kosul}";
        }
        
        $sonuc = $this->db->get_row($sql, $params);
        return $sonuc['toplam'];
    }
    
    /**
     * Güvenli veri filtreleme
     */
    protected function guvenliVeriFiltrele($veri) {
        $guvenli_veri = [];
        
        foreach ($veri as $alan => $deger) {
            // Sadece doldurulabilir alanları kabul et
            if (in_array($alan, $this->doldurulabilir_alanlar)) {
                // Güvenli alanları kontrol et
                if (empty($this->guvenli_alanlar) || in_array($alan, $this->guvenli_alanlar)) {
                    $guvenli_veri[$alan] = $deger;
                }
            }
        }
        
        return $guvenli_veri;
    }
    
    /**
     * Sayfalama ile kayıtları getir
     */
    public function sayfalamaGetir($sayfa = 1, $sayfa_boyutu = 10, $kosul = null, $params = []) {
        $offset = ($sayfa - 1) * $sayfa_boyutu;
        $kayitlar = $this->kosulaGoreGetir($kosul, $params, $sayfa_boyutu, $offset);
        $toplam = $this->toplamKayitSayisi($kosul, $params);
        
        return [
            'kayitlar' => $kayitlar,
            'toplam' => $toplam,
            'sayfa' => $sayfa,
            'sayfa_boyutu' => $sayfa_boyutu,
            'toplam_sayfa' => ceil($toplam / $sayfa_boyutu)
        ];
    }
    
    /**
     * Arama yap
     */
    public function ara($arama_terimi, $arama_alanlari = [], $limit = 10) {
        if (empty($arama_alanlari)) {
            return [];
        }
        
        $kosullar = [];
        $params = [];
        
        foreach ($arama_alanlari as $alan) {
            $kosullar[] = "{$alan} LIKE :arama";
            $params['arama'] = "%{$arama_terimi}%";
        }
        
        $kosul = implode(' OR ', $kosullar);
        return $this->kosulaGoreGetir($kosul, $params, $limit);
    }
}
?>
