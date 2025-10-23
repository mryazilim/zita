/**
 * Firma Kayıt Formu JavaScript
 * Form doğrulama, gönderim ve kullanıcı deneyimi iyileştirmeleri
 */

document.addEventListener('DOMContentLoaded', function() {
    // Form elementlerini seç
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('sifre');
    
    // Step management
    let currentStep = 1;
    const totalSteps = 4;
    
    // Form doğrulama kuralları
    const validationRules = {
        vknTc: {
            required: true,
            pattern: /^[0-9]{10,11}$/,
            message: 'VKN/TC 10-11 haneli olmalıdır'
        },
        vergiDairesi: {
            required: true,
            minLength: 2,
            message: 'Vergi dairesi en az 2 karakter olmalıdır'
        },
        firmaAdi: {
            required: true,
            minLength: 2,
            message: 'Firma adı en az 2 karakter olmalıdır'
        },
        unvan: {
            required: true,
            minLength: 5,
            message: 'Ünvan en az 5 karakter olmalıdır'
        },
        telefon: {
            required: true,
            pattern: /^[0-9]{10,11}$/,
            message: 'Telefon numarası 10-11 haneli olmalıdır'
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Geçerli bir e-posta adresi giriniz'
        },
        adres: {
            required: true,
            minLength: 10,
            message: 'Adres en az 10 karakter olmalıdır'
        },
        isimSoyisim: {
            required: true,
            minLength: 2,
            message: 'İsim soyisim en az 2 karakter olmalıdır'
        },
        sektor: {
            required: true,
            message: 'Sektör seçimi zorunludur'
        },
        il: {
            required: true,
            message: 'İl seçimi zorunludur'
        },
        ilce: {
            required: true,
            message: 'İlçe seçimi zorunludur'
        },
        sifre: {
            required: true,
            minLength: 8,
            message: 'Şifre en az 8 karakter olmalıdır'
        },
        sifreTekrar: {
            required: true,
            message: 'Şifre tekrarı zorunludur'
        },
        sozlesme: {
            required: true,
            message: 'Sözleşme kabul edilmelidir'
        }
    };
    
    // Step navigation functions
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });
        
        // Show current step
        document.getElementById(`step${step}`).classList.add('active');
        
        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        const progress = (step / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        
        // Update step indicators
        document.querySelectorAll('.step').forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if (index + 1 < step) {
                stepEl.classList.add('completed');
            } else if (index + 1 === step) {
                stepEl.classList.add('active');
            }
        });
        
        // Update navigation buttons
        prevBtn.style.display = step > 1 ? 'block' : 'none';
        nextBtn.style.display = step < totalSteps ? 'block' : 'none';
        submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        
        // Update button text
        if (step < totalSteps) {
            nextBtn.innerHTML = `Sonraki <i class="bi bi-arrow-right ms-2"></i>`;
        }
    }
    
    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }
    
    function validateCurrentStep() {
        const currentStepEl = document.getElementById(`step${currentStep}`);
        const fields = currentStepEl.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        fields.forEach(field => {
            if (field.name && validationRules[field.name]) {
                if (!validateFieldElement(field)) {
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    // Navigation event listeners
    nextBtn.addEventListener('click', nextStep);
    prevBtn.addEventListener('click', prevStep);
    
    // Şifre göster/gizle
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }
    
    // Form doğrulama fonksiyonu
    function validateField(fieldName, value) {
        const rule = validationRules[fieldName];
        if (!rule) return true;
        
        // Zorunlu alan kontrolü
        if (rule.required && (!value || value.trim() === '')) {
            return {
                valid: false,
                message: `${getFieldLabel(fieldName)} zorunludur`
            };
        }
        
        // Minimum uzunluk kontrolü
        if (rule.minLength && value.length < rule.minLength) {
            return {
                valid: false,
                message: rule.message
            };
        }
        
        // Pattern kontrolü
        if (rule.pattern && !rule.pattern.test(value)) {
            return {
                valid: false,
                message: rule.message
            };
        }
        
        // Şifre tekrar kontrolü
        if (fieldName === 'sifreTekrar') {
            const sifre = document.getElementById('sifre').value;
            if (value !== sifre) {
                return {
                    valid: false,
                    message: 'Şifreler eşleşmiyor'
                };
            }
        }
        
        return { valid: true };
    }
    
    // Alan etiketlerini al
    function getFieldLabel(fieldName) {
        const labels = {
            vknTc: 'VKN/TC',
            vergiDairesi: 'Vergi Dairesi',
            firmaAdi: 'Firma Adı',
            unvan: 'Ünvan',
            telefon: 'Telefon',
            email: 'E-posta',
            adres: 'Adres',
            isimSoyisim: 'İsim Soyisim',
            sektor: 'Sektör',
            il: 'İl',
            ilce: 'İlçe',
            sifre: 'Şifre',
            sifreTekrar: 'Şifre Tekrar',
            sozlesme: 'Sözleşme'
        };
        return labels[fieldName] || fieldName;
    }
    
    // Alan doğrulama
    function validateFieldElement(field) {
        const fieldName = field.name;
        const value = field.value;
        const validation = validateField(fieldName, value);
        
        // Bootstrap validation sınıflarını kaldır
        field.classList.remove('is-valid', 'is-invalid');
        
        // Feedback elementini al
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        
        if (validation.valid) {
            field.classList.add('is-valid');
            if (feedback) {
                feedback.textContent = '';
                feedback.style.display = 'none';
            }
        } else {
            field.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = validation.message;
                feedback.style.display = 'block';
            }
        }
        
        return validation.valid;
    }
    
    // Tüm alanları doğrula
    function validateAllFields() {
        let isValid = true;
        const fields = registerForm.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            if (field.name && validationRules[field.name]) {
                if (!validateFieldElement(field)) {
                    isValid = false;
                }
            }
        });
        
        return isValid;
    }
    
    // Real-time validation
    const fields = registerForm.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            validateFieldElement(this);
        });
        
        field.addEventListener('input', function() {
            // Sadece hata durumunda tekrar doğrula
            if (this.classList.contains('is-invalid')) {
                validateFieldElement(this);
            }
        });
    });
    
    // Form gönderimi
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Son adımda tüm alanları doğrula
        if (!validateAllFields()) {
            // İlk hatalı alana odaklan
            const firstInvalid = registerForm.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        // Loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Kayıt işleniyor...';
        
        // Form verilerini topla
        const formData = new FormData(registerForm);
        const data = Object.fromEntries(formData.entries());
        
        // Simüle edilmiş API çağrısı
        setTimeout(() => {
            // Başarılı kayıt simülasyonu
            showSuccessMessage();
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Kayıt Ol';
        }, 2000);
    });
    
    // Başarı mesajı göster
    function showSuccessMessage() {
        // Mevcut mesajları kaldır
        const existingMessages = registerForm.querySelectorAll('.success-message, .error-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Başarı mesajı oluştur
        const successMessage = document.createElement('div');
        successMessage.className = 'success-message';
        successMessage.innerHTML = `
            <i class="bi bi-check-circle"></i>
            <h5>Kayıt Başarılı!</h5>
            <p>Firma kaydınız başarıyla tamamlandı. E-posta adresinize onay linki gönderildi.</p>
        `;
        
        // Form başına ekle
        registerForm.insertBefore(successMessage, registerForm.firstChild);
        
        // Formu temizle
        registerForm.reset();
        
        // Validation sınıflarını kaldır
        const fields = registerForm.querySelectorAll('.is-valid, .is-invalid');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        
        // Feedback mesajlarını temizle
        const feedbacks = registerForm.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => {
            feedback.textContent = '';
            feedback.style.display = 'none';
        });
        
        // Sayfayı yukarı kaydır
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Hata mesajı göster
    function showErrorMessage(message) {
        // Mevcut mesajları kaldır
        const existingMessages = registerForm.querySelectorAll('.success-message, .error-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Hata mesajı oluştur
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = `
            <i class="bi bi-exclamation-triangle"></i>
            <h5>Kayıt Başarısız!</h5>
            <p>${message}</p>
        `;
        
        // Form başına ekle
        registerForm.insertBefore(errorMessage, registerForm.firstChild);
        
        // Sayfayı yukarı kaydır
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // İl-İlçe bağlantısı
    const ilSelect = document.getElementById('il');
    const ilceSelect = document.getElementById('ilce');
    
    if (ilSelect && ilceSelect) {
        ilSelect.addEventListener('change', function() {
            const selectedIl = this.value;
            ilceSelect.innerHTML = '<option value="">İlçe seçin</option>';
            
            if (selectedIl) {
                // İlçe seçeneklerini yükle (örnek veriler)
                const ilceler = getIlceler(selectedIl);
                ilceler.forEach(ilce => {
                    const option = document.createElement('option');
                    option.value = ilce.value;
                    option.textContent = ilce.name;
                    ilceSelect.appendChild(option);
                });
            }
        });
    }
    
    // İlçe verilerini getir (örnek)
    function getIlceler(il) {
        const ilceData = {
            'istanbul': [
                { value: 'adalar', name: 'Adalar' },
                { value: 'arnavutkoy', name: 'Arnavutköy' },
                { value: 'atasehir', name: 'Ataşehir' },
                { value: 'avcilar', name: 'Avcılar' },
                { value: 'bagcilar', name: 'Bağcılar' },
                { value: 'bahcelievler', name: 'Bahçelievler' },
                { value: 'bakirkoy', name: 'Bakırköy' },
                { value: 'basaksehir', name: 'Başakşehir' },
                { value: 'bayrampasa', name: 'Bayrampaşa' },
                { value: 'besiktas', name: 'Beşiktaş' },
                { value: 'beykoz', name: 'Beykoz' },
                { value: 'beylikduzu', name: 'Beylikdüzü' },
                { value: 'beyoglu', name: 'Beyoğlu' },
                { value: 'buyukcekmece', name: 'Büyükçekmece' },
                { value: 'catalca', name: 'Çatalca' },
                { value: 'cekmekoy', name: 'Çekmeköy' },
                { value: 'esenler', name: 'Esenler' },
                { value: 'esenler', name: 'Esenyurt' },
                { value: 'fatih', name: 'Fatih' },
                { value: 'gaziosmanpasa', name: 'Gaziosmanpaşa' },
                { value: 'gungoren', name: 'Güngören' },
                { value: 'kadikoy', name: 'Kadıköy' },
                { value: 'kagithane', name: 'Kağıthane' },
                { value: 'kartal', name: 'Kartal' },
                { value: 'kucukcekmece', name: 'Küçükçekmece' },
                { value: 'maltepe', name: 'Maltepe' },
                { value: 'pendik', name: 'Pendik' },
                { value: 'sancaktepe', name: 'Sancaktepe' },
                { value: 'sariyer', name: 'Sarıyer' },
                { value: 'silivri', name: 'Silivri' },
                { value: 'sultanbeyli', name: 'Sultanbeyli' },
                { value: 'sultangazi', name: 'Sultangazi' },
                { value: 'sile', name: 'Şile' },
                { value: 'sisli', name: 'Şişli' },
                { value: 'tuzla', name: 'Tuzla' },
                { value: 'umraniye', name: 'Ümraniye' },
                { value: 'uskudar', name: 'Üsküdar' },
                { value: 'zeytinburnu', name: 'Zeytinburnu' }
            ],
            'ankara': [
                { value: 'akyurt', name: 'Akyurt' },
                { value: 'altindag', name: 'Altındağ' },
                { value: 'ayas', name: 'Ayaş' },
                { value: 'bala', name: 'Bala' },
                { value: 'beypazari', name: 'Beypazarı' },
                { value: 'camlidere', name: 'Çamlıdere' },
                { value: 'cankaya', name: 'Çankaya' },
                { value: 'cubuk', name: 'Çubuk' },
                { value: 'elmadag', name: 'Elmadağ' },
                { value: 'etimesgut', name: 'Etimesgut' },
                { value: 'evren', name: 'Evren' },
                { value: 'golbasi', name: 'Gölbaşı' },
                { value: 'gudul', name: 'Güdül' },
                { value: 'haymana', name: 'Haymana' },
                { value: 'kalecik', name: 'Kalecik' },
                { value: 'kizilcahamam', name: 'Kızılcahamam' },
                { value: 'mamak', name: 'Mamak' },
                { value: 'nallihan', name: 'Nallıhan' },
                { value: 'polatli', name: 'Polatlı' },
                { value: 'pursaklar', name: 'Pursaklar' },
                { value: 'sincan', name: 'Sincan' },
                { value: 'sereflikochisar', name: 'Şereflikoçhisar' },
                { value: 'yenimahalle', name: 'Yenimahalle' }
            ],
            'izmir': [
                { value: 'aliaga', name: 'Aliağa' },
                { value: 'balcova', name: 'Balçova' },
                { value: 'bayindir', name: 'Bayındır' },
                { value: 'bayrakli', name: 'Bayraklı' },
                { value: 'bergama', name: 'Bergama' },
                { value: 'beydag', name: 'Beydağ' },
                { value: 'bornova', name: 'Bornova' },
                { value: 'buca', name: 'Buca' },
                { value: 'cesme', name: 'Çeşme' },
                { value: 'cigli', name: 'Çiğli' },
                { value: 'dikili', name: 'Dikili' },
                { value: 'foca', name: 'Foça' },
                { value: 'gaziemir', name: 'Gaziemir' },
                { value: 'guzelbahce', name: 'Güzelbahçe' },
                { value: 'karabaglar', name: 'Karabağlar' },
                { value: 'karsiyaka', name: 'Karşıyaka' },
                { value: 'kemalpasa', name: 'Kemalpaşa' },
                { value: 'kinik', name: 'Kınık' },
                { value: 'kiraz', name: 'Kiraz' },
                { value: 'konak', name: 'Konak' },
                { value: 'menderes', name: 'Menderes' },
                { value: 'menemen', name: 'Menemen' },
                { value: 'narlidere', name: 'Narlıdere' },
                { value: 'odemis', name: 'Ödemiş' },
                { value: 'seferihisar', name: 'Seferihisar' },
                { value: 'selcuk', name: 'Selçuk' },
                { value: 'tire', name: 'Tire' },
                { value: 'torbali', name: 'Torbalı' },
                { value: 'urla', name: 'Urla' }
            ]
        };
        
        return ilceData[il] || [];
    }
    
    // Form temizleme
    function clearForm() {
        registerForm.reset();
        const fields = registerForm.querySelectorAll('.is-valid, .is-invalid');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        
        const feedbacks = registerForm.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => {
            feedback.textContent = '';
            feedback.style.display = 'none';
        });
    }
    
    // Sayfa yüklendiğinde form durumunu kontrol et
    window.addEventListener('load', function() {
        // İlk adımı göster
        showStep(1);
        
        // URL parametrelerini kontrol et
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');
        
        if (success === 'true') {
            showSuccessMessage();
        } else if (error) {
            showErrorMessage(decodeURIComponent(error));
        }
    });
});
