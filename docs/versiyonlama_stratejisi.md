# Zita Projesi - Versiyonlama Stratejisi

## 🏷️ Versiyonlama Formatı

### Yıl Bazlı Versiyonlama
```
vYIL.MAJOR.MINOR.PATCH
```

**Örnekler:**
- `v25.1.0.0` - 2025 yılı, 1. major, 0. minor, 0. patch
- `v25.1.1.0` - 2025 yılı, 1. major, 1. minor, 0. patch
- `v25.2.0.0` - 2025 yılı, 2. major, 0. minor, 0. patch
- `v26.1.0.0` - 2026 yılı, 1. major, 0. minor, 0. patch

## 🌿 Branch Stratejisi

### Ana Branch'ler
- **main** - Ana branch (production) - İngilizce kalacak
- **gelistirme** - Geliştirme branch

### Özellik Branch'leri
- **ozellik/kullanici-girisi** - Kullanıcı girişi özelliği
- **ozellik/urun-katalogu** - Ürün kataloğu özelliği
- **ozellik/sepet-sistemi** - Sepet sistemi özelliği

### Acil Düzeltme Branch'leri
- **acil-duzeltme/guvenlik-acigi** - Güvenlik açığı düzeltmesi
- **acil-duzeltme/kritik-hata** - Kritik hata düzeltmesi

## 🚀 Geliştirme Süreci

### 1. Özellik Geliştirme
```bash
# Geliştirme branch'inden özellik branch'i oluştur
git checkout gelistirme
git checkout -b ozellik/kullanici-girisi

# Geliştirme yap
# ... kod yazma ...

# Geliştirme branch'ine merge
git checkout gelistirme
git merge ozellik/kullanici-girisi
```

### 2. Versiyonlama
```bash
# Tag oluşturma
git tag -a v25.1.0.0 -m "2025 yılı ilk major release"
git tag -a v25.1.1.0 -m "2025 yılı 1.1 minor release"

# Tag'leri push etme
git push origin --tags
```

### 3. Production'a Geçiş
```bash
# Geliştirme branch'inden main'e merge
git checkout main
git merge gelistirme
git tag -a v25.1.0.0 -m "2025 yılı ilk major release"
git push origin main --tags
```

## 📋 Versiyonlama Kuralları

### Major (X.0.0.0)
- Büyük değişiklikler
- API değişiklikleri
- Geriye uyumsuzluk

### Minor (X.Y.0.0)
- Yeni özellikler
- Geriye uyumlu değişiklikler
- Performans iyileştirmeleri

### Patch (X.Y.Z.0)
- Bug düzeltmeleri
- Güvenlik güncellemeleri
- Küçük iyileştirmeler

## 🔄 Geliştirme Akışı

1. **Özellik Geliştirme** → `ozellik/***` branch
2. **Test ve Geliştirme** → `gelistirme` branch
3. **Production** → `main` branch
4. **Versiyonlama** → Tag oluşturma
5. **Dokümantasyon** → Changelog güncelleme
