/**
 * Zita Projesi - Giriş Sayfası JavaScript
 * 
 * Giriş formu işlevselliği ve API entegrasyonu
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

// DOM yüklendiğinde çalışacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function() {
    // Form elementlerini al
    const signinForm = document.querySelector('.card-body');
    const usernameInput = document.getElementById('signin-username');
    const passwordInput = document.getElementById('signin-password');
    const rememberCheckbox = document.getElementById('defaultCheck1');
    const signinBtn = document.getElementById('girisBtn');
    const forgetPasswordLink = document.querySelector('a[href="reset-password-basic.html"]');
    const signupLink = document.querySelector('a[href="signup-basic.html"]');

    // Form validasyon kuralları
    const validationRules = {
        username: {
            required: true,
            minLength: 3,
            pattern: /^[a-zA-Z0-9@._-]+$/,
            message: 'Kullanıcı adı en az 3 karakter olmalı'
        },
        password: {
            required: true,
            minLength: 6,
            message: 'Şifre en az 6 karakter olmalı'
        }
    };

    // Gerçek zamanlı validasyon
    function validateField(fieldName, value) {
        const rules = validationRules[fieldName];
        if (!rules) return true;

        if (rules.required && (!value || value.trim() === '')) {
            return `${fieldName === 'username' ? 'Kullanıcı adı' : 'Şifre'} gereklidir`;
        }

        if (rules.minLength && value.length < rules.minLength) {
            return rules.message;
        }

        if (rules.pattern && !rules.pattern.test(value)) {
            return rules.message;
        }

        return true;
    }

    // Alan hata gösterimi
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        // Hata mesajı için div oluştur
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    // Alan başarı gösterimi
    function showFieldSuccess(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }

    // Mesaj gösterimi
    function showMessage(message, type = 'danger') {
        // Mevcut mesajı kaldır
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Yeni mesaj oluştur
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} mt-3`;
        alertDiv.textContent = message;
        
        // Form sonrasına ekle
        const form = document.querySelector('.row.gy-3');
        form.parentNode.insertBefore(alertDiv, form.nextSibling);
        
        // 5 saniye sonra mesajı gizle
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Loading durumu
    function setLoading(loading) {
        if (loading) {
            signinBtn.disabled = true;
            signinBtn.innerHTML = '<i class="ri-loader-4-line me-2"></i>Giriş yapılıyor...';
        } else {
            signinBtn.disabled = false;
            signinBtn.innerHTML = 'Giriş Yap';
        }
    }

    // Form gönderimi
    async function handleFormSubmit(event) {
        event.preventDefault();
        
        // Form verilerini al
        const data = {
            kullanici_adi: usernameInput.value.trim(),
            sifre: passwordInput.value,
            beni_hatirla: rememberCheckbox.checked
        };

        // Validasyon
        let isFormValid = true;
        
        // Kullanıcı adı validasyonu
        const usernameValidation = validateField('username', data.kullanici_adi);
        if (usernameValidation !== true) {
            showFieldError(usernameInput, usernameValidation);
            isFormValid = false;
        } else {
            showFieldSuccess(usernameInput);
        }

        // Şifre validasyonu
        const passwordValidation = validateField('password', data.sifre);
        if (passwordValidation !== true) {
            showFieldError(passwordInput, passwordValidation);
            isFormValid = false;
        } else {
            showFieldSuccess(passwordInput);
        }

        if (!isFormValid) {
            showMessage('Lütfen tüm alanları doğru şekilde doldurun.');
            return;
        }

        // Loading durumunu başlat
        setLoading(true);

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
                    window.location.href = result.data.redirect || 'index.php';
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
    usernameInput.addEventListener('blur', function() {
        const value = this.value.trim();
        const validation = validateField('username', value);
        
        if (validation !== true) {
            showFieldError(this, validation);
        } else {
            showFieldSuccess(this);
        }
    });

    passwordInput.addEventListener('blur', function() {
        const value = this.value;
        const validation = validateField('password', value);
        
        if (validation !== true) {
            showFieldError(this, validation);
        } else {
            showFieldSuccess(this);
        }
    });

    // Input temizleme
    usernameInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
        }
    });

    passwordInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
            this.classList.remove('is-invalid');
        }
    });

    // Form submit event listener
    signinBtn.addEventListener('click', handleFormSubmit);

    // Enter tuşu ile form gönderimi
    document.addEventListener('keypress', function(event) {
        if (event.key === 'Enter' && !signinBtn.disabled) {
            handleFormSubmit(event);
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
                    window.location.href = 'index.php';
                } else {
                    // Token geçersizse temizle
                    localStorage.removeItem('zita_token');
                    sessionStorage.removeItem('zita_token');
                    localStorage.removeItem('zita_remember');
                    setLoading(false);
                }
            })
            .catch(error => {
                console.error('Token doğrulama hatası:', error);
                setLoading(false);
            });
        }
    }

    // Sayfa yüklendiğinde token kontrolü yap
    checkExistingToken();

    // Responsive tasarım için ek işlevler
    function handleResize() {
        const isMobile = window.innerWidth < 768;
        
        if (isMobile) {
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
            if (!signinBtn.disabled) {
                handleFormSubmit(event);
            }
        }
        
        // Escape ile formu temizle
        if (event.key === 'Escape') {
            usernameInput.value = '';
            passwordInput.value = '';
            rememberCheckbox.checked = false;
            usernameInput.classList.remove('is-invalid', 'is-valid');
            passwordInput.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Form temizleme butonu (geliştirme için)
    function clearForm() {
        usernameInput.value = '';
        passwordInput.value = '';
        rememberCheckbox.checked = false;
        usernameInput.classList.remove('is-invalid', 'is-valid');
        passwordInput.classList.remove('is-invalid', 'is-valid');
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