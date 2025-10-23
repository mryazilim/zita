# Zita Projesi - Klasör Yapısı

## 🏗️ Ana Klasör Yapısı

```
zita/
├── tanitim/                  # Tanıtım sitesi sistemi
│   ├── sayfalar/            # HTML sayfaları
│   ├── kontrolculer/        # PHP kontrolcüler
│   ├── modeller/            # PHP modeller
│   ├── gorunumler/          # PHP görünümler
│   ├── api/                 # API dosyaları
│   ├── statik/              # CSS, JS, resimler
│   └── veritabani/          # Veritabanı dosyaları
├── yonetim/                   # Yönetim paneli sistemi
│   ├── sayfalar/            # HTML sayfaları
│   ├── kontrolculer/        # PHP kontrolcüler
│   ├── modeller/            # PHP modeller
│   ├── gorunumler/          # PHP görünümler
│   ├── api/                 # API dosyaları
│   ├── statik/              # CSS, JS, resimler
│   └── veritabani/          # Veritabanı dosyaları
├── zita-proje/               # Firma proje sistemi
│   ├── sayfalar/            # HTML sayfaları
│   ├── kontrolculer/        # PHP kontrolcüler
│   ├── modeller/            # PHP modeller
│   ├── gorunumler/          # PHP görünümler
│   ├── api/                 # API dosyaları
│   ├── statik/              # CSS, JS, resimler
│   └── veritabani/          # Veritabanı dosyaları
└── ortak/                    # Ortak kütüphaneler
    ├── kutuphaneler/        # Ortak PHP kütüphaneleri
    ├── yardimci_fonksiyonlar/
    └── sabitler/
```

## 📁 Detaylı Klasör Yapıları

### **Tanıtım Sistemi:**
```
tanitim/
├── sayfalar/                # HTML sayfaları
│   ├── index.html           # Ana sayfa
│   ├── ozellikler.html      # Özellikler
│   ├── fiyatlandirma.html   # Fiyatlandırma
│   └── iletisim.html        # İletişim
├── kontrolculer/            # PHP kontrolcüler
│   ├── ana_sayfa_kontrol.php
│   ├── ozellik_kontrol.php
│   ├── fiyat_kontrol.php
│   └── iletisim_kontrol.php
├── modeller/                # PHP modeller
│   ├── sayfa_model.php      # Sayfa verileri
│   ├── ozellik_model.php    # Özellik verileri
│   ├── fiyat_model.php      # Fiyat verileri
│   └── iletisim_model.php   # İletişim verileri
├── gorunumler/              # PHP görünümler
│   ├── ana_sayfa_gorunum.php
│   ├── ozellik_gorunum.php
│   ├── fiyat_gorunum.php
│   └── iletisim_gorunum.php
├── api/                     # API dosyaları
│   ├── sayfa_api.php        # Sayfa API'si
│   ├── ozellik_api.php      # Özellik API'si
│   └── fiyat_api.php        # Fiyat API'si
├── statik/                  # Statik dosyalar
│   ├── css/
│   │   ├── style.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── main.js
│   │   └── animations.js
│   └── resimler/
│       ├── logo.png
│       └── banner.jpg
└── veritabani/              # Veritabanı dosyaları
    ├── baglanti.php         # Veritabanı bağlantısı
    └── tablolar.sql         # Tablo yapıları
```

### **Yönetim Paneli Sistemi:**
```
yonetim/
├── sayfalar/                # HTML sayfaları
│   ├── giris.php            # Giriş sayfası
│   ├── dashboard.html       # Ana panel
│   ├── sayfa-yonetimi.html  # Sayfa yönetimi
│   ├── ozellik-yonetimi.html # Özellik yönetimi
│   └── fiyat-yonetimi.html  # Fiyat yönetimi
├── kontrolculer/            # PHP kontrolcüler
│   ├── giris_kontrol.php    # Giriş kontrolü
│   ├── dashboard_kontrol.php # Dashboard kontrolü
│   ├── sayfa_yonetim_kontrol.php
│   ├── ozellik_yonetim_kontrol.php
│   └── fiyat_yonetim_kontrol.php
├── modeller/                # PHP modeller
│   ├── kullanici_model.php  # Kullanıcı verileri
│   ├── sayfa_model.php      # Sayfa verileri
│   ├── ozellik_model.php    # Özellik verileri
│   └── fiyat_model.php      # Fiyat verileri
├── gorunumler/              # PHP görünümler
│   ├── giris_gorunum.php    # Giriş görünümü
│   ├── dashboard_gorunum.php # Dashboard görünümü
│   ├── sayfa_yonetim_gorunum.php
│   ├── ozellik_yonetim_gorunum.php
│   └── fiyat_yonetim_gorunum.php
├── api/                     # API dosyaları
│   ├── giris_api.php        # Giriş API'si
│   ├── sayfa_api.php        # Sayfa API'si
│   └── yonetim_api.php      # Yönetim API'si
├── statik/                  # Statik dosyalar
│   ├── css/
│   │   ├── admin.css
│   │   └── dashboard.css
│   ├── js/
│   │   ├── admin.js
│   │   └── dashboard.js
│   └── resimler/
│       └── admin-logo.png
└── veritabani/              # Veritabanı dosyaları
    ├── baglanti.php         # Veritabanı bağlantısı
    └── tablolar.sql         # Tablo yapıları
```

### **Zita Proje Sistemi:**
```
zita-proje/
├── sayfalar/                # HTML sayfaları
│   ├── giris.php            # Firma girişi
│   ├── dashboard.html       # Ana panel
│   ├── ziyaretci-takibi.html # Ziyaretçi takibi
│   └── arac-takibi.html     # Araç takibi
├── kontrolculer/            # PHP kontrolcüler
│   ├── firma_giris_kontrol.php
│   ├── dashboard_kontrol.php
│   ├── ziyaretci_kontrol.php
│   └── arac_kontrol.php
├── modeller/                # PHP modeller
│   ├── firma_model.php      # Firma verileri
│   ├── ziyaretci_model.php  # Ziyaretçi verileri
│   ├── arac_model.php       # Araç verileri
│   └── ortak_veri_model.php # Ortak veri modeli
├── gorunumler/              # PHP görünümler
│   ├── firma_giris_gorunum.php
│   ├── dashboard_gorunum.php
│   ├── ziyaretci_gorunum.php
│   └── arac_gorunum.php
├── api/                     # API dosyaları
│   ├── firma_api.php        # Firma API'si
│   ├── ziyaretci_api.php     # Ziyaretçi API'si
│   └── arac_api.php         # Araç API'si
├── statik/                  # Statik dosyalar
│   ├── css/
│   │   ├── panel.css
│   │   └── dashboard.css
│   ├── js/
│   │   ├── panel.js
│   │   └── dashboard.js
│   └── resimler/
│       └── panel-logo.png
└── veritabani/              # Veritabanı dosyaları
    ├── baglanti.php         # Veritabanı bağlantısı
    └── tablolar.sql         # Tablo yapıları
```

### **Ortak Kütüphaneler:**
```
ortak/
├── kutuphaneler/            # Ortak PHP kütüphaneleri
│   ├── nsql/               # nsql kütüphanesi
│   ├── guvenlik/            # Güvenlik kütüphaneleri
│   └── yardimci/           # Yardımcı kütüphaneler
├── yardimci_fonksiyonlar/   # Ortak fonksiyonlar
│   ├── veritabani_fonksiyonlari.php
│   ├── guvenlik_fonksiyonlari.php
│   └── api_fonksiyonlari.php
└── sabitler/                # Sabit değerler
    ├── veritabani_sabitleri.php
    ├── api_sabitleri.php
    └── sistem_sabitleri.php
```

## 🎯 Özel MVC Yapısının Özellikleri

### **Esneklik:**
- **Kendi düzenimiz** - Standart yapılara bağımlı değil
- **Özelleştirilebilir** - İhtiyaca göre düzenlenebilir
- **Basit yapı** - Karmaşık framework'ler yok

### **Yönetilebilirlik:**
- **Açık yapı** - Her dosya ne işe yaradığı belli
- **Kolay geliştirme** - Hızlı geliştirme
- **Bağımsızlık** - Her sistem kendi yapısı

### **Yeniden Kullanılabilirlik:**
- **Yönetim paneli** - Farklı projeler için kullanılabilir
- **Tanıtım sitesi** - Farklı yönetim panelleri ile değiştirilebilir
- **Zita proje** - Bağımsız olarak geliştirilebilir

## 📝 Güncelleme Notları

- **v25.1.0.0** - İlk klasör yapısı oluşturuldu
- **v25.1.1.0** - yonetim-paneli klasörü yonetim olarak güncellendi
- **Tarih:** 2025-01-13
- **Durum:** Planlama aşamasında
- **Sonraki adım:** Alt klasörlerin oluşturulması

## 🔄 Güncelleme Süreci

Bu dosya proje ilerledikçe güncellenecek:
1. Yeni klasörler eklendiğinde
2. Yapı değişiklikleri yapıldığında
3. Yeni modüller eklendiğinde
4. Sistem entegrasyonları yapıldığında
