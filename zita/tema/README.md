# Zita Tema

Zita projesi için modern, responsive ve kullanıcı dostu tema dosyaları.

## 📁 Klasör Yapısı

```
zita/tema/
├── giris.html              # Giriş sayfası
├── varliklar/
│   ├── css/
│   │   └── giris.css       # Giriş sayfası stilleri
│   ├── js/
│   │   └── giris.js         # Giriş sayfası JavaScript
│   ├── resimler/           # Resim dosyaları
│   └── fontlar/            # Font dosyaları
├── api/
│   ├── giris.php           # Giriş API endpoint
│   └── token-dogrula.php   # Token doğrulama API
└── README.md               # Bu dosya
```

## 🎨 Tasarım Özellikleri

### ✨ Modern Tasarım
- **Gradient Arka Plan:** Mavi-mor gradient
- **Glassmorphism:** Şeffaf cam efekti
- **Responsive:** Tüm cihazlarda uyumlu
- **Animasyonlar:** Smooth geçişler

### 🔧 Teknik Özellikler
- **Bootstrap 5.3.0:** Modern CSS framework
- **Font Awesome 6.4.0:** İkon kütüphanesi
- **Google Fonts:** Inter font ailesi
- **Vanilla JavaScript:** Framework bağımsız

### 📱 Responsive Tasarım
- **Desktop:** İki panel (form + tanıtım)
- **Tablet:** Tek panel (form)
- **Mobile:** Optimize edilmiş form

## 🚀 Kullanım

### Giriş Sayfası
```html
<!-- Giriş sayfasını aç -->
<a href="zita/tema/giris.html">Giriş Yap</a>
```

### API Endpoints
```javascript
// Giriş API'si
POST zita/tema/api/giris.php
{
    "kullanici_adi": "admin",
    "sifre": "admin123",
    "beni_hatirla": true
}

// Token doğrulama API'si
POST zita/tema/api/token-dogrula.php
{
    "token": "base64_encoded_token"
}
```

## 🔐 Güvenlik

### Form Validasyonu
- **Gerçek zamanlı:** Input sırasında validasyon
- **Sunucu tarafı:** API'de tekrar validasyon
- **XSS Koruması:** HTML escape
- **CSRF Koruması:** Token tabanlı

### Şifre Güvenliği
- **Hash:** PHP password_hash()
- **Doğrulama:** password_verify()
- **Güçlü şifre:** Minimum 6 karakter

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

## 🎯 Geliştirme

### Yeni Özellik Ekleme
1. HTML'e yeni element ekle
2. CSS'te stil tanımla
3. JavaScript'te işlev ekle
4. API'de endpoint oluştur

### Tema Güncelleme
1. CSS değişkenlerini güncelle
2. Renk paletini değiştir
3. Animasyonları ayarla
4. Responsive breakpoint'leri güncelle

## 📞 Destek

Herhangi bir sorun için:
- **GitHub Issues:** Proje sayfasında issue açın
- **E-posta:** admin@zita.com
- **Dokümantasyon:** docs/ klasörü

## 📄 Lisans

Bu proje GPL-3.0 lisansı altında lisanslanmıştır.
