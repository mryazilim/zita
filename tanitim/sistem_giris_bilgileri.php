<?php
/**
 * Zita Projesi - Sistem Giriş Bilgileri
 * 
 * Bu script sistem kullanıcılarının giriş bilgilerini gösterir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../ortak/veritabani/baglanti.php';

echo "🔐 Sistem Giriş Bilgileri\n\n";

try {
    // Veritabanı bağlantısını al
    $db = veritabani_baglanti();
    
    // Sistem kullanıcılarını getir
    $sistemKullanicilar = $db->get_results("
        SELECT id, kullanici_adi, email, ad_soyad, yetki_seviyesi, aktif, olusturma_tarihi 
        FROM sistem_kullanicilari 
        ORDER BY id
    ");
    
    if (!empty($sistemKullanicilar)) {
        echo "👤 Sistem Kullanıcıları:\n\n";
        
        foreach ($sistemKullanicilar as $kullanici) {
            echo "📋 Kullanıcı Bilgileri:\n";
            echo "   - ID: {$kullanici['id']}\n";
            echo "   - Kullanıcı Adı: {$kullanici['kullanici_adi']}\n";
            echo "   - E-posta: {$kullanici['email']}\n";
            echo "   - Ad Soyad: {$kullanici['ad_soyad']}\n";
            echo "   - Yetki Seviyesi: {$kullanici['yetki_seviyesi']}\n";
            echo "   - Durum: " . ($kullanici['aktif'] ? 'Aktif' : 'Pasif') . "\n";
            echo "   - Oluşturma Tarihi: {$kullanici['olusturma_tarihi']}\n";
            echo "\n";
        }
        
        echo "🔑 Giriş Bilgileri:\n";
        echo "   - Kullanıcı Adı: admin\n";
        echo "   - Şifre: admin123\n";
        echo "   - E-posta: admin@zita.com\n";
        echo "   - Yetki: super_admin\n\n";
        
        echo "⚠️  GÜVENLİK UYARISI:\n";
        echo "   - Bu şifreler varsayılan şifrelerdir!\n";
        echo "   - Üretim ortamında mutlaka değiştirin!\n";
        echo "   - Güçlü şifreler kullanın!\n";
        
    } else {
        echo "❌ Sistem kullanıcısı bulunamadı!\n";
        echo "   Varsayılan sistem kullanıcısı oluşturulacak...\n\n";
        
        // Varsayılan sistem kullanıcısı oluştur
        $varsayilanKullanici = [
            'kullanici_adi' => 'admin',
            'email' => 'admin@zita.com',
            'sifre' => password_hash('admin123', PASSWORD_DEFAULT),
            'ad_soyad' => 'Sistem Yöneticisi',
            'yetki_seviyesi' => 'super_admin',
            'aktif' => 1
        ];
        
        $kullaniciId = $db->insert('sistem_kullanicilari', $varsayilanKullanici);
        
        if ($kullaniciId) {
            echo "✅ Varsayılan sistem kullanıcısı oluşturuldu:\n";
            echo "   - Kullanıcı Adı: admin\n";
            echo "   - Şifre: admin123\n";
            echo "   - E-posta: admin@zita.com\n";
            echo "   - Yetki: super_admin\n";
        } else {
            echo "❌ Varsayılan sistem kullanıcısı oluşturulamadı!\n";
        }
    }
    
    echo "\n✅ Sistem giriş bilgileri kontrolü tamamlandı!\n";
    
} catch (Exception $e) {
    echo "❌ Kontrol hatası: " . $e->getMessage() . "\n";
}
?>
