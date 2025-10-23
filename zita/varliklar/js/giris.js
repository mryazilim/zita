/**
 * Zita Projesi - Giriş Sayfası JavaScript
 * 
 * signin-basic.html tasarımı bazlı işlevsellik
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// DOM yüklendiğinde çalışacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function() {
    // Giriş formu elementlerini al
    const girisForm = document.getElementById('girisForm');
    const kullaniciAdiInput = document.getElementById('kullanici_adi');
    const sifreInput = document.getElementById('sifre');
    const girisBtn = document.querySelector('.giris-btn');
    const btnText = document.querySelector('.btn-text');
    const btnLoading = document.querySelector('.btn-loading');
    const mesajAlani = document.getElementById('mesajAlani');
    const beniHatirlaCheckbox = document.getElementById('beni_hatirla');

    // Form validasyon kuralları
    const validationRules = {
        kullanici_adi: {
            required: true,
            minLength: 3,
            pattern: /^[a-zA-Z0-9@._-]+$/,
            message: 'Kullanıcı adı en az 3 karakter olmalı ve sadece harf, rakam, @, ., _, - içermeli'
        },
        sifre: {
            required: true,
            minLength: 6,
            message: 'Şifre en az 6 karakter olmalı'
        }
    };

    // Gerçek zamanlı validasyon
    function validateField(fieldName, value) {
        const rules = validationRules[fieldName];
        if (!rules) return true;

        // Gerekli alan kontrolü
        if (rules.required && (!value || value.trim() === '')) {
            return `${fieldName === 'kullanici_adi' ? 'Kullanıcı adı' : 'Şifre'} gereklidir`;
        }

        // Minimum uzunluk kontrolü
        if (rules.minLength && value.length < rules.minLength) {
            return rules.message;
        }

        // Pattern kontrolü
        if (rules.pattern && !rules.pattern.test(value)) {
            return rules.message;
        }

        return true;
    }

    // Alan hata gösterimi
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }

    // Alan başarı gösterimi
    function showFieldSuccess(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
    }

    // Mesaj gösterimi
    function showMessage(message, type = 'danger') {
        mesajAlani.className = `alert alert-${type}`;
        mesajAlani.textContent = message;
        mesajAlani.classList.remove('d-none');
        
        // 5 saniye sonra mesajı gizle
        setTimeout(() => {
            mesajAlani.classList.add('d-none');
        }, 5000);
    }

    // Loading durumu
    function setLoading(loading) {
        if (loading) {
            girisBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
        } else {
            girisBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        }
    }

    // Form gönderimi
    async function handleFormSubmit(event) {
        event.preventDefault();
        
        // Form verilerini al
        const formData = new FormData(girisForm);
        const data = {
            kullanici_adi: formData.get('kullanici_adi'),
            sifre: formData.get('sifre'),
            beni_hatirla: formData.get('beni_hatirla') === '1'
        };

        // Validasyon
        let isFormValid = true;
        
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const value = data[fieldName];
            const validation = validateField(fieldName, value);
            
            if (validation !== true) {
                showFieldError(field, validation);
                isFormValid = false;
            } else {
                showFieldSuccess(field);
            }
        });

        if (!isFormValid) {
            showMessage('Lütfen tüm alanları doğru şekilde doldurun.');
            return;
        }

        // Loading durumunu başlat
        setLoading(true);
        mesajAlani.classList.add('d-none');

        try {
            // API'ye giriş isteği gönder
            const response = await fetch('api/giris.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showMessage('Giriş başarılı! Yönlendiriliyorsunuz...', 'success');
                
                // Beni hatırla seçeneği
                if (data.beni_hatirla && result.data.token) {
                    localStorage.setItem('zita_token', result.data.token);
                    localStorage.setItem('zita_remember', 'true');
                } else if (result.data.token) {
                    sessionStorage.setItem('zita_token', result.data.token);
                }

                // 2 saniye sonra yönlendir
                setTimeout(() => {
                    window.location.href = result.data.redirect || 'panel/dashboard/';
                }, 2000);
            } else {
                throw new Error(result.message || 'Giriş başarısız');
            }

        } catch (error) {
            console.error('Giriş hatası:', error);
            showMessage('Giriş yapılırken hata oluştu: ' + error.message);
        } finally {
            setLoading(false);
        }
    }

    // Gerçek zamanlı validasyon event listener'ları
    kullaniciAdiInput.addEventListener('blur', function() {
        const value = this.value.trim();
        const validation = validateField('kullanici_adi', value);
        
        if (validation !== true) {
            showFieldError(this, validation);
        } else {
            showFieldSuccess(this);
        }
    });

    sifreInput.addEventListener('blur', function() {
        const value = this.value;
        const validation = validateField('sifre', value);
        
        if (validation !== true) {
            showFieldError(this, validation);
        } else {
            showFieldSuccess(this);
        }
    });

    // Input temizleme
    kullaniciAdiInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
        }
    });

    sifreInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
        }
    });

    // Form submit event listener
    girisForm.addEventListener('submit', handleFormSubmit);

    // Enter tuşu ile form gönderimi
    document.addEventListener('keypress', function(event) {
        if (event.key === 'Enter' && !girisBtn.disabled) {
            girisForm.dispatchEvent(new Event('submit'));
        }
    });

    // Sayfa yüklendiğinde token kontrolü
    function checkExistingToken() {
        const token = localStorage.getItem('zita_token') || sessionStorage.getItem('zita_token');
        const remember = localStorage.getItem('zita_remember');
        
        if (token && remember === 'true') {
            // Token varsa otomatik giriş yap
            setLoading(true);
            showMessage('Otomatik giriş yapılıyor...', 'info');
            
            // Token doğrulama API'si çağır
            fetch('api/token-dogrula.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = 'panel/dashboard/';
                } else {
                    // Token geçersizse temizle
                    localStorage.removeItem('zita_token');
                    sessionStorage.removeItem('zita_token');
                    localStorage.removeItem('zita_remember');
                    setLoading(false);
                    mesajAlani.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Token doğrulama hatası:', error);
                setLoading(false);
                mesajAlani.classList.add('d-none');
            });
        }
    }

    // Sayfa yüklendiğinde token kontrolü yap
    checkExistingToken();

    // Responsive tasarım için ek işlevler
    function handleResize() {
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
            // Mobil cihazlarda ek optimizasyonlar
            document.body.classList.add('mobile-view');
        } else {
            document.body.classList.remove('mobile-view');
        }
    }

    // Resize event listener
    window.addEventListener('resize', handleResize);
    handleResize(); // İlk yüklemede çalıştır

    // Klavye kısayolları
    document.addEventListener('keydown', function(event) {
        // Ctrl + Enter ile form gönderimi
        if (event.ctrlKey && event.key === 'Enter') {
            if (!girisBtn.disabled) {
                girisForm.dispatchEvent(new Event('submit'));
            }
        }
        
        // Escape ile formu temizle
        if (event.key === 'Escape') {
            girisForm.reset();
            mesajAlani.classList.add('d-none');
            kullaniciAdiInput.classList.remove('is-invalid', 'is-valid');
            sifreInput.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Form temizleme butonu (geliştirme için)
    function clearForm() {
        girisForm.reset();
        mesajAlani.classList.add('d-none');
        kullaniciAdiInput.classList.remove('is-invalid', 'is-valid');
        sifreInput.classList.remove('is-invalid', 'is-valid');
        setLoading(false);
    }

    // Geliştirme modunda console'a fonksiyonları ekle
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        window.clearForm = clearForm;
        console.log('Zita giriş sayfası yüklendi. Geliştirme fonksiyonları: clearForm()');
    }
});

// Sayfa kapatılırken temizlik
window.addEventListener('beforeunload', function() {
    // Gerekirse sayfa kapatılırken temizlik işlemleri
});

// Hata yakalama
window.addEventListener('error', function(event) {
    console.error('JavaScript hatası:', event.error);
});

// Promise hata yakalama
window.addEventListener('unhandledrejection', function(event) {
    console.error('Promise hatası:', event.reason);
});
