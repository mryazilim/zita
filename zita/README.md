# Zita Projesi

Dijital dönüşüm platformu - Firma ziyaretçi takibi, araç yönetimi ve işletme dijitalleştirme sistemi.

## 📁 Klasör Yapısı

```
zita/
├── giris.php               # Giriş sayfası
├── varliklar/
│   ├── css/
│   │   └── giris.css       # Giriş sayfası stilleri
│   ├── js/
│   │   └── giris.js        # Giriş sayfası JavaScript
│   └── resimler/           # Resim dosyaları
├── api/
│   ├── giris.php           # Giriş API endpoint
│   └── token-dogrula.php   # Token doğrulama API
└── README.md               # Bu dosya
```

## 🎨 Tasarım Özellikleri

### ✨ Tema Bazlı Tasarım
- **Velvet Tema:** Modern admin panel teması
- **Bootstrap 5:** Responsive framework
- **Remix Icons:** Modern ikon kütüphanesi
- **Zita Marka Renkleri:** Özel renk paleti

### 🔧 Teknik Özellikler
- **Tema Entegrasyonu:** Mevcut tema yapısı kullanılıyor
- **Responsive:** Tüm cihazlarda uyumlu
- **Modern UI/UX:** Kullanıcı dostu arayüz
- **Türkçe Dil Desteği:** Tam Türkçe arayüz

### 📱 Responsive Tasarım
- **Desktop:** İki panel (form + tanıtım)
- **Tablet:** Tek panel (form)
- **Mobile:** Optimize edilmiş form

## 🔐 Güvenlik

### Form Validasyonu
- **Gerçek Zamanlı:** Input sırasında kontrol
- **Sunucu Tarafı:** API'de tekrar validasyon
- **XSS Koruması:** HTML escape
- **CSRF Koruması:** Token tabanlı

### Şifre Güvenliği
- **Hash:** PHP password_hash()
- **Doğrulama:** password_verify()
- **Güçlü Şifre:** Minimum 6 karakter

## 📋 Özellikler

### Giriş Formu
- ✅ Kullanıcı adı/e-posta girişi
- ✅ Şifre göster/gizle
- ✅ Beni hatırla seçeneği
- ✅ Gerçek zamanlı validasyon
- ✅ Loading durumu
- ✅ Hata mesajları

### Tanıtım Paneli
- ✅ Zita logosu
- ✅ Özellik listesi
- ✅ Modern animasyonlar
- ✅ Responsive tasarım

### JavaScript İşlevleri
- ✅ Form validasyonu
- ✅ AJAX giriş
- ✅ Token yönetimi
- ✅ Otomatik giriş
- ✅ Klavye kısayolları

## 🌐 API Endpoints

### Giriş API
```javascript
POST zita/api/giris.php
{
    "kullanici_adi": "admin",
    "sifre": "admin123",
    "beni_hatirla": true
}
```

### Token Doğrulama
```javascript
POST zita/api/token-dogrula.php
{
    "token": "base64_encoded_token"
}
```

## 🎯 Sistem Entegrasyonu

### Veritabanı
- **Sistem Kullanıcıları:** `sistem_kullanicilari` tablosu
- **Log Kayıtları:** Giriş işlemleri loglanır
- **Token Yönetimi:** JWT benzeri basit token

### Giriş Bilgileri
- **Kullanıcı Adı:** `admin`
- **Şifre:** `admin123`
- **E-posta:** `admin@zita.com`
- **Yetki:** `super_admin`

## 🚀 Kullanım

### Giriş Sayfası
```html
<!-- Giriş sayfasını aç -->
<a href="zita/giris.php">Giriş Yap</a>
```

### Tema Entegrasyonu
- **CSS:** Tema CSS dosyaları kullanılıyor
- **JS:** Tema JavaScript dosyaları kullanılıyor
- **İkonlar:** Remix Icons kütüphanesi
- **Bootstrap:** Tema Bootstrap sürümü

## 🔧 Geliştirme

### Yeni Özellik Ekleme
1. HTML'e yeni element ekle
2. CSS'te stil tanımla
3. JavaScript'te işlev ekle
4. API'de endpoint oluştur

### Tema Güncelleme
1. Tema dosyalarını güncelle
2. CSS değişkenlerini ayarla
3. Renk paletini değiştir
4. Responsive breakpoint'leri güncelle

## 📊 Sonraki Adımlar

1. **Dashboard Modülü:** Ana panel tasarımı
2. **Ziyaretçi Modülü:** Ziyaretçi takip sistemi
3. **Araç Modülü:** Araç takip sistemi
4. **Raporlama:** Analiz ve raporlar

## 📞 Destek

Herhangi bir sorun için:
- **GitHub Issues:** Proje sayfasında issue açın
- **E-posta:** admin@zita.com
- **Dokümantasyon:** docs/ klasörü

## 📄 Lisans

Bu proje GPL-3.0 lisansı altında lisanslanmıştır.