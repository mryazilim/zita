# nsql Kütüphanesi

Bu klasör, orijinal `github.com/ngunenc/nsql` kütüphanesinin dosyalarını içerir.

## 📁 Klasör Yapısı

```
nsql/
├── nsql.php              # Ana nsql sınıfı
├── config.php             # Konfigürasyon dosyası
├── query_builder.php      # Sorgu oluşturucu
└── traits/                # Trait dosyaları
    ├── cache_trait.php
    ├── connection_trait.php
    ├── debug_trait.php
    ├── query_analyzer_trait.php
    ├── query_parameter_trait.php
    ├── statement_cache_trait.php
    └── transaction_trait.php
```

## 🔧 Kullanım

```php
// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/../kutuphaneler/nsql/nsql.php';

// nsql instance'ı oluştur
$db = new nsql\database\nsql($host, $dbname, $username, $password, $charset);

// Sorgu çalıştır
$results = $db->get_results("SELECT * FROM users");
$row = $db->get_row("SELECT * FROM users WHERE id = ?", [1]);
```

## 📋 Özellikler

- **PHP 8.0+ Uyumlu** - Modern PHP özellikleri
- **PDO Tabanlı** - Güvenli veritabanı işlemleri
- **Cache Desteği** - Sorgu önbellekleme
- **Debug Modu** - Geliştirme modu
- **Trait Desteği** - Modüler yapı
- **Transaction Desteği** - İşlem yönetimi

## 🎯 Proje Entegrasyonu

Bu kütüphane `ortak/veritabani/baglanti.php` dosyası üzerinden projeye entegre edilmiştir.

## 📝 Notlar

- Orijinal nsql kütüphanesi PHP 8.0+ gerektirir
- WampServer'da PHP 8.0+ kullanılmalıdır
- CLI'da PHP 7.4 kullanılıyorsa web üzerinden test edilmelidir
