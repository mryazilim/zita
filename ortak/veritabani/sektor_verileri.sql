-- Zita Projesi - Sektör Verileri
-- 
-- Bu dosya Türkiye'deki güncel sektör verilerini içerir.
-- 2025 yılı güncel sektör listesi ile oluşturulmuştur.
-- 
-- @author Zita Projesi
-- @version v25.1.0.0
-- @date 2025-01-13

USE zita_vt;

-- ========================================
-- SEKTÖRLER VERİLERİ
-- ========================================

INSERT INTO sektorler (sektor_adi, sektor_aciklama) VALUES
('Teknoloji ve Yazılım', 'Bilgisayar, yazılım, teknoloji hizmetleri'),
('İnşaat ve Yapı', 'İnşaat, yapı malzemeleri, mimarlık'),
('Otomotiv', 'Araç üretimi, yedek parça, servis'),
('Gıda ve İçecek', 'Gıda üretimi, restoran, catering'),
('Tekstil ve Konfeksiyon', 'Tekstil üretimi, hazır giyim'),
('Kimya ve Petrokimya', 'Kimyasal ürünler, petrokimya'),
('Enerji', 'Elektrik, doğalgaz, yenilenebilir enerji'),
('Sağlık ve İlaç', 'Hastane, ilaç, medikal cihazlar'),
('Eğitim', 'Okul, kurs, eğitim hizmetleri'),
('Finans ve Bankacılık', 'Banka, sigorta, yatırım'),
('Turizm ve Otelcilik', 'Otel, tatil köyü, turizm hizmetleri'),
('Lojistik ve Taşımacılık', 'Kargo, nakliye, depolama'),
('Tarım ve Hayvancılık', 'Tarım ürünleri, hayvancılık'),
('Madencilik', 'Maden çıkarma, işleme'),
('Elektronik', 'Elektronik cihazlar, beyaz eşya'),
('Makine ve Metal', 'Makine üretimi, metal işleme'),
('Kağıt ve Orman Ürünleri', 'Kağıt, mobilya, orman ürünleri'),
('Plastik ve Kauçuk', 'Plastik ürünler, kauçuk'),
('Cam ve Seramik', 'Cam üretimi, seramik'),
('Kozmetik ve Temizlik', 'Kozmetik, temizlik ürünleri'),
('Spor ve Rekreasyon', 'Spor ekipmanları, fitness'),
('Medya ve İletişim', 'TV, radyo, reklam'),
('Gayrimenkul', 'Emlak, inşaat, yatırım'),
('Hukuk ve Danışmanlık', 'Avukatlık, danışmanlık hizmetleri'),
('Güvenlik', 'Güvenlik hizmetleri, güvenlik sistemleri'),
('Çevre ve Geri Dönüşüm', 'Çevre teknolojileri, geri dönüşüm'),
('Araştırma ve Geliştirme', 'AR-GE, inovasyon, patent'),
('E-ticaret', 'Online satış, dijital platformlar'),
('Eğlence ve Oyun', 'Oyun, eğlence, etkinlik'),
('Sosyal Hizmetler', 'Sosyal yardım, vakıf, dernek'),
('Devlet Kurumları', 'Kamu kurumları, belediye'),
('Sivil Toplum', 'Dernek, vakıf, sivil toplum'),
('Diğer', 'Belirtilmemiş veya diğer sektörler');
