-- Zita Projesi - Veritabanı Tablo Yapıları
-- 
-- Bu dosya tüm sistemler için gerekli tabloları oluşturur.
-- Türkçe isimlendirme standardı kullanılır (snake_case).
-- 
-- @author Zita Projesi
-- @version v25.1.0.0
-- @date 2025-01-13

-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS zita_vt 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE zita_vt;

-- ========================================
-- ORTAK VERİ TABLOLARI
-- ========================================

-- İller tablosu
CREATE TABLE IF NOT EXISTS iller (
    id INT AUTO_INCREMENT PRIMARY KEY,
    il_adi VARCHAR(50) NOT NULL,
    plaka_kodu VARCHAR(2) NOT NULL,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_il_adi (il_adi),
    INDEX idx_plaka_kodu (plaka_kodu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İlçeler tablosu
CREATE TABLE IF NOT EXISTS ilceler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    il_id INT NOT NULL,
    ilce_adi VARCHAR(100) NOT NULL,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (il_id) REFERENCES iller(id) ON DELETE CASCADE,
    INDEX idx_il_id (il_id),
    INDEX idx_ilce_adi (ilce_adi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sektörler tablosu
CREATE TABLE IF NOT EXISTS sektorler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sektor_adi VARCHAR(100) NOT NULL,
    sektor_aciklama TEXT,
    aktif BOOLEAN DEFAULT TRUE,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sektor_adi (sektor_adi),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SİSTEM YÖNETİMİ TABLOLARI
-- ========================================

-- Sistem kullanıcıları tablosu
CREATE TABLE IF NOT EXISTS sistem_kullanicilari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    yetki_seviyesi ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    aktif BOOLEAN DEFAULT TRUE,
    son_giris_tarihi TIMESTAMP NULL,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kullanici_adi (kullanici_adi),
    INDEX idx_email (email),
    INDEX idx_yetki_seviyesi (yetki_seviyesi),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistem ayarları tablosu
CREATE TABLE IF NOT EXISTS sistem_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ayar_adi VARCHAR(100) NOT NULL UNIQUE,
    ayar_degeri TEXT,
    ayar_tipi ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    aciklama TEXT,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ayar_adi (ayar_adi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log kayıtları tablosu
CREATE TABLE IF NOT EXISTS log_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NULL,
    islem_tipi VARCHAR(50) NOT NULL,
    islem_aciklama TEXT,
    ip_adresi VARCHAR(45),
    user_agent TEXT,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES sistem_kullanicilari(id) ON DELETE SET NULL,
    INDEX idx_kullanici_id (kullanici_id),
    INDEX idx_islem_tipi (islem_tipi),
    INDEX idx_olusturma_tarihi (olusturma_tarihi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- FİRMA SİSTEMİ TABLOLARI
-- ========================================

-- Firmalar tablosu
CREATE TABLE IF NOT EXISTS firmalar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_adi VARCHAR(200) NOT NULL,
    unvan VARCHAR(300) NOT NULL,
    vkn_tc VARCHAR(20) NOT NULL UNIQUE,
    vergi_dairesi VARCHAR(100),
    telefon VARCHAR(20),
    email VARCHAR(100),
    adres TEXT,
    il_id INT,
    ilce_id INT,
    sektor_id INT,
    aktif BOOLEAN DEFAULT TRUE,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (il_id) REFERENCES iller(id) ON DELETE SET NULL,
    FOREIGN KEY (ilce_id) REFERENCES ilceler(id) ON DELETE SET NULL,
    FOREIGN KEY (sektor_id) REFERENCES sektorler(id) ON DELETE SET NULL,
    INDEX idx_firma_adi (firma_adi),
    INDEX idx_vkn_tc (vkn_tc),
    INDEX idx_email (email),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Firma kullanıcıları tablosu
CREATE TABLE IF NOT EXISTS firma_kullanicilari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_id INT NOT NULL,
    kullanici_adi VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    telefon VARCHAR(20),
    yetki_seviyesi ENUM('admin', 'kullanici', 'goruntuleme') DEFAULT 'kullanici',
    aktif BOOLEAN DEFAULT TRUE,
    son_giris_tarihi TIMESTAMP NULL,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    INDEX idx_firma_id (firma_id),
    INDEX idx_kullanici_adi (kullanici_adi),
    INDEX idx_email (email),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Firma ayarları tablosu
CREATE TABLE IF NOT EXISTS firma_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_id INT NOT NULL,
    ayar_adi VARCHAR(100) NOT NULL,
    ayar_degeri TEXT,
    ayar_tipi ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    UNIQUE KEY unique_firma_ayar (firma_id, ayar_adi),
    INDEX idx_firma_id (firma_id),
    INDEX idx_ayar_adi (ayar_adi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- ZİYARETÇİ TAKİP SİSTEMİ TABLOLARI
-- ========================================

-- Ziyaretçiler tablosu
CREATE TABLE IF NOT EXISTS ziyaretciler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_id INT NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    telefon VARCHAR(20),
    email VARCHAR(100),
    tc_kimlik_no VARCHAR(11),
    firma_adi VARCHAR(200),
    giris_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cikis_tarihi TIMESTAMP NULL,
    ziyaret_nedeni TEXT,
    karsilayan_kisi VARCHAR(100),
    durum ENUM('beklemede', 'kabul_edildi', 'reddedildi', 'tamamlandi') DEFAULT 'beklemede',
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    INDEX idx_firma_id (firma_id),
    INDEX idx_ad_soyad (ad_soyad),
    INDEX idx_telefon (telefon),
    INDEX idx_durum (durum),
    INDEX idx_giris_tarihi (giris_tarihi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ziyaret kayıtları tablosu
CREATE TABLE IF NOT EXISTS ziyaret_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ziyaretci_id INT NOT NULL,
    firma_id INT NOT NULL,
    giris_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cikis_tarihi TIMESTAMP NULL,
    ziyaret_suresi INT NULL, -- dakika cinsinden
    ziyaret_notu TEXT,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ziyaretci_id) REFERENCES ziyaretciler(id) ON DELETE CASCADE,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    INDEX idx_ziyaretci_id (ziyaretci_id),
    INDEX idx_firma_id (firma_id),
    INDEX idx_giris_tarihi (giris_tarihi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- ARAÇ TAKİP SİSTEMİ TABLOLARI
-- ========================================

-- Araçlar tablosu
CREATE TABLE IF NOT EXISTS araclar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firma_id INT NOT NULL,
    plaka VARCHAR(20) NOT NULL,
    marka VARCHAR(50),
    model VARCHAR(50),
    yil INT,
    renk VARCHAR(30),
    sahip_adi VARCHAR(100),
    sahip_telefon VARCHAR(20),
    aktif BOOLEAN DEFAULT TRUE,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    INDEX idx_firma_id (firma_id),
    INDEX idx_plaka (plaka),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Araç kayıtları tablosu
CREATE TABLE IF NOT EXISTS arac_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arac_id INT NOT NULL,
    firma_id INT NOT NULL,
    giris_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cikis_tarihi TIMESTAMP NULL,
    bekleme_suresi INT NULL, -- dakika cinsinden
    notlar TEXT,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arac_id) REFERENCES araclar(id) ON DELETE CASCADE,
    FOREIGN KEY (firma_id) REFERENCES firmalar(id) ON DELETE CASCADE,
    INDEX idx_arac_id (arac_id),
    INDEX idx_firma_id (firma_id),
    INDEX idx_giris_tarihi (giris_tarihi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- BAŞLANGIÇ VERİLERİ
-- ========================================

-- Sistem ayarları başlangıç verileri
INSERT INTO sistem_ayarlari (ayar_adi, ayar_degeri, ayar_tipi, aciklama) VALUES
('site_baslik', 'Zita - Ziyaretçi ve Araç Takip Sistemi', 'string', 'Site başlığı'),
('site_aciklama', 'Firmalar için ziyaretçi ve araç takip çözümü', 'string', 'Site açıklaması'),
('max_ziyaretci_sayisi', '1000', 'number', 'Maksimum ziyaretçi sayısı'),
('arac_takip_aktif', 'true', 'boolean', 'Araç takip sistemi aktif mi'),
('ziyaretci_takip_aktif', 'true', 'boolean', 'Ziyaretçi takip sistemi aktif mi'),
('otomatik_cikis_saati', '18:00', 'string', 'Otomatik çıkış saati'),
('bildirim_email', 'admin@zita.com', 'string', 'Bildirim e-posta adresi');

-- Varsayılan sistem kullanıcısı (şifre: admin123)
INSERT INTO sistem_kullanicilari (kullanici_adi, email, sifre, ad_soyad, yetki_seviyesi) VALUES
('admin', 'admin@zita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'super_admin');
