/**
 * Firma Kayıt Formu JavaScript
 * Form doğrulama, gönderim ve kullanıcı deneyimi iyileştirmeleri
 * API entegrasyonu ile dinamik veri yükleme
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
    
    // API endpoint'leri
    const API_ENDPOINTS = {
        iller: 'api/iller.php',
        ilceler: 'api/ilceler.php',
        sektorler: 'api/sektorler.php'
    };

    // Cache için veri saklama
    let cacheData = {
        iller: null,
        sektorler: null,
        ilceler: {}
    };

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
            minLength: 3,
            message: 'İsim soyisim en az 3 karakter olmalıdır'
        },
        sifre: {
            required: true,
            minLength: 6,
            message: 'Şifre en az 6 karakter olmalıdır'
        },
        sifreTekrar: {
            required: true,
            match: 'sifre',
            message: 'Şifreler eşleşmiyor'
        }
    };

    // API'den veri çekme fonksiyonu
    async function fetchData(url) {
        try {
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'Veri çekilemedi');
            }
        } catch (error) {
            console.error('API Hatası:', error);
            throw error;
        }
    }

    // İlleri yükle
    async function loadIller() {
        if (cacheData.iller) {
            return cacheData.iller;
        }
        
        try {
            const iller = await fetchData(API_ENDPOINTS.iller);
            cacheData.iller = iller;
            return iller;
        } catch (error) {
            console.error('İller yüklenemedi:', error);
            return [];
        }
    }

    // Sektörleri yükle
    async function loadSektorler() {
        if (cacheData.sektorler) {
            return cacheData.sektorler;
        }
        
        try {
            const sektorler = await fetchData(API_ENDPOINTS.sektorler);
            cacheData.sektorler = sektorler;
            return sektorler;
        } catch (error) {
            console.error('Sektörler yüklenemedi:', error);
            return [];
        }
    }

    // İlçeleri yükle
    async function loadIlceler(ilId) {
        if (cacheData.ilceler[ilId]) {
            return cacheData.ilceler[ilId];
        }
        
        try {
            const ilceler = await fetchData(`${API_ENDPOINTS.ilceler}?il_id=${ilId}`);
            cacheData.ilceler[ilId] = ilceler;
            return ilceler;
        } catch (error) {
            console.error('İlçeler yüklenemedi:', error);
            return [];
        }
    }

    // İlçe verilerini getir (API'den)
    async function getIlceler(ilId) {
        if (!ilId) return [];
        
        try {
            const ilceler = await loadIlceler(ilId);
            return ilceler.map(ilce => ({
                value: ilce.id,
                name: ilce.ilce_adi
            }));
        } catch (error) {
            console.error('İlçe verileri alınamadı:', error);
            return [];
        }
    }

    // Form alanlarını doldur
    async function populateFormFields() {
        try {
            // İlleri yükle
            const iller = await loadIller();
            const ilSelect = document.getElementById('il');
            
            if (ilSelect && iller.length > 0) {
                ilSelect.innerHTML = '<option value="">İl Seçiniz</option>';
                iller.forEach(il => {
                    const option = document.createElement('option');
                    option.value = il.id;
                    option.textContent = il.il_adi;
                    ilSelect.appendChild(option);
                });
            }

            // Sektörleri yükle
            const sektorler = await loadSektorler();
            const sektorSelect = document.getElementById('sektor');
            
            if (sektorSelect && sektorler.length > 0) {
                sektorSelect.innerHTML = '<option value="">Sektör Seçiniz</option>';
                sektorler.forEach(sektor => {
                    const option = document.createElement('option');
                    option.value = sektor.id;
                    option.textContent = sektor.sektor_adi;
                    sektorSelect.appendChild(option);
                });
            }

        } catch (error) {
            console.error('Form alanları doldurulamadı:', error);
            showError('Form verileri yüklenirken hata oluştu. Sayfayı yenileyin.');
        }
    }

    // İl değiştiğinde ilçeleri yükle
    async function handleIlChange() {
        const ilSelect = document.getElementById('il');
        const ilceSelect = document.getElementById('ilce');
        
        if (!ilSelect || !ilceSelect) return;
        
        const ilId = ilSelect.value;
        
        if (!ilId) {
            ilceSelect.innerHTML = '<option value="">Önce il seçiniz</option>';
            return;
        }
        
        try {
            // Loading göster
            ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            ilceSelect.disabled = true;
            
            const ilceler = await getIlceler(ilId);
            
            ilceSelect.innerHTML = '<option value="">İlçe Seçiniz</option>';
            ilceler.forEach(ilce => {
                const option = document.createElement('option');
                option.value = ilce.value;
                option.textContent = ilce.name;
                ilceSelect.appendChild(option);
            });
            
            ilceSelect.disabled = false;
            
        } catch (error) {
            console.error('İlçeler yüklenemedi:', error);
            ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
            ilceSelect.disabled = false;
        }
    }

    // Form doğrulama
    function validateField(fieldName, value) {
        const rule = validationRules[fieldName];
        if (!rule) return true;

        if (rule.required && (!value || value.trim() === '')) {
            return rule.message;
        }

        if (rule.minLength && value.length < rule.minLength) {
            return rule.message;
        }

        if (rule.pattern && !rule.pattern.test(value)) {
            return rule.message;
        }

        if (rule.match) {
            const matchField = document.getElementById(rule.match);
            if (matchField && value !== matchField.value) {
                return rule.message;
            }
        }

        return true;
    }

    // Gerçek zamanlı doğrulama
    function setupRealTimeValidation() {
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                const fieldName = this.name;
                const value = this.value;
                
                const isValid = validateField(fieldName, value);
                
                if (isValid === true) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    hideFieldError(this);
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    showFieldError(this, isValid);
                }
            });
        });
    }

    // Şifre eşleşme kontrolü
    function setupPasswordMatch() {
        const sifreInput = document.getElementById('sifre');
        const sifreTekrarInput = document.getElementById('sifreTekrar');
        
        if (sifreInput && sifreTekrarInput) {
            sifreTekrarInput.addEventListener('input', function() {
                if (this.value && sifreInput.value !== this.value) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Şifreler eşleşmiyor');
                } else if (this.value && sifreInput.value === this.value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    hideFieldError(this);
                }
            });
        }
    }

    // Şifre göster/gizle
    function setupPasswordToggle() {
        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
        }
    }

    // Adım göster
    function showStep(step) {
        const steps = document.querySelectorAll('.form-step');
        const progressBar = document.querySelector('.progress-bar');
        const stepNumbers = document.querySelectorAll('.step-number');
        
        steps.forEach((stepElement, index) => {
            if (index + 1 === step) {
                stepElement.classList.add('active');
            } else {
                stepElement.classList.remove('active');
            }
        });
        
        if (progressBar) {
            const progress = ((step - 1) / (totalSteps - 1)) * 100;
            progressBar.style.width = progress + '%';
        }
        
        stepNumbers.forEach((stepNumber, index) => {
            if (index + 1 <= step) {
                stepNumber.classList.add('completed');
            } else {
                stepNumber.classList.remove('completed');
            }
        });
        
        currentStep = step;
        updateNavigationButtons();
    }

    // Navigasyon butonlarını güncelle
    function updateNavigationButtons() {
        if (prevBtn) {
            prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';
        }
        
        if (nextBtn) {
            nextBtn.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
        }
        
        if (submitBtn) {
            submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        }
    }

    // Sonraki adım
    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }
    }

    // Önceki adım
    function prevStep() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    }

    // Mevcut adımı doğrula
    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.form-step:nth-child(${currentStep})`);
        if (!currentStepElement) return true;
        
        const inputs = currentStepElement.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        inputs.forEach(input => {
            const fieldName = input.name;
            const value = input.value;
            
            if (fieldName && validationRules[fieldName]) {
                const validation = validateField(fieldName, value);
                
                if (validation !== true) {
                    input.classList.add('is-invalid');
                    showFieldError(input, validation);
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    hideFieldError(input);
                }
            }
        });
        
        return isValid;
    }

    // Hata mesajı göster
    function showFieldError(field, message) {
        hideFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    // Hata mesajını gizle
    function hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    // Genel hata mesajı göster
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = message;
        
        const form = document.querySelector('.register-form');
        if (form) {
            form.insertBefore(errorDiv, form.firstChild);
            
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }
    }

    // Başarı mesajı göster
    function showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success';
        successDiv.textContent = message;
        
        const form = document.querySelector('.register-form');
        if (form) {
            form.insertBefore(successDiv, form.firstChild);
            
            setTimeout(() => {
                successDiv.remove();
            }, 5000);
        }
    }

    // Form gönderimi
    function handleFormSubmit(event) {
        event.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        // Tüm formu doğrula
        const formData = new FormData(registerForm);
        let isFormValid = true;
        
        Object.keys(validationRules).forEach(fieldName => {
            const value = formData.get(fieldName);
            const validation = validateField(fieldName, value);
            
            if (validation !== true) {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.classList.add('is-invalid');
                    showFieldError(field, validation);
                }
                isFormValid = false;
            }
        });
        
        if (!isFormValid) {
            showError('Lütfen tüm alanları doğru şekilde doldurun.');
            return;
        }
        
        // Loading göster
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kayıt Oluşturuluyor...';
        }
        
        // Form verilerini hazırla
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // AJAX ile gönder (şimdilik simüle et)
        setTimeout(() => {
            showSuccess('Kayıt başarıyla oluşturuldu! Yönlendiriliyorsunuz...');
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Kayıt Ol';
            }
            
            // 3 saniye sonra yönlendir
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 3000);
        }, 2000);
    }

    // Event listener'ları ayarla
    function setupEventListeners() {
        // İl değişikliği
        const ilSelect = document.getElementById('il');
        if (ilSelect) {
            ilSelect.addEventListener('change', handleIlChange);
        }
        
        // Navigasyon butonları
        if (nextBtn) {
            nextBtn.addEventListener('click', nextStep);
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', prevStep);
        }
        
        // Form gönderimi
        if (registerForm) {
            registerForm.addEventListener('submit', handleFormSubmit);
        }
        
        // Şifre göster/gizle
        setupPasswordToggle();
        
        // Gerçek zamanlı doğrulama
        setupRealTimeValidation();
        
        // Şifre eşleşme kontrolü
        setupPasswordMatch();
    }

    // Sayfa yüklendiğinde çalıştır
    async function init() {
        try {
            // Form alanlarını doldur
            await populateFormFields();
            
            // Event listener'ları ayarla
            setupEventListeners();
            
            // İlk adımı göster
            showStep(1);
            
        } catch (error) {
            console.error('Form başlatılamadı:', error);
            showError('Form yüklenirken hata oluştu. Sayfayı yenileyin.');
        }
    }

    // Başlat
    init();
});