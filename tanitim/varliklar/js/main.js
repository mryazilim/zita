/**
 * Zita Tanıtım Sitesi - Ana JavaScript Dosyası
 * Modern, interaktif ve kullanıcı dostu deneyim
 */

// DOM Yüklendiğinde çalışacak fonksiyonlar
document.addEventListener('DOMContentLoaded', function() {
    // Tüm animasyonları başlat
    initAnimations();
    
    // Smooth scroll özelliğini etkinleştir
    initSmoothScroll();
    
    // Navbar scroll efektini başlat
    initNavbarScroll();
    
    // Form validasyonunu başlat
    initFormValidation();
    
    // İstatistik sayaçlarını başlat
    initCounters();
    
    // Intersection Observer ile animasyonları tetikle
    initIntersectionObserver();
    
    // Slider kontrollerini başlat
    initSliderControls();
});

/**
 * Smooth scroll özelliğini başlatır
 */
function initSmoothScroll() {
    // Tüm anchor linklerini bul
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Navbar yüksekliği için
                
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Navbar scroll efektini başlatır
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Scroll pozisyonuna göre navbar stilini değiştir
        if (scrollTop > 100) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
        
        // Scroll aşağı doğru gidiyorsa navbar'ı hafifçe gizle
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            navbar.style.transform = 'translateY(-80%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });
}

/**
 * Form validasyonunu başlatır
 */
function initFormValidation() {
    const contactForm = document.querySelector('#iletisim form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form verilerini al
            const formData = new FormData(this);
            const formObject = {};
            
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            
            // Basit validasyon
            if (validateForm(formObject)) {
                // Form gönderme animasyonu
                showLoadingState();
                
                // Simüle edilmiş form gönderme
                setTimeout(() => {
                    showSuccessMessage();
                    this.reset();
                }, 2000);
            }
        });
    }
}

/**
 * Form validasyonu yapar
 */
function validateForm(data) {
    const errors = [];
    
    // Ad soyad kontrolü
    if (!data.ad || data.ad.trim().length < 2) {
        errors.push('Ad soyad en az 2 karakter olmalıdır.');
    }
    
    // E-posta kontrolü
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!data.email || !emailRegex.test(data.email)) {
        errors.push('Geçerli bir e-posta adresi giriniz.');
    }
    
    // Konu kontrolü
    if (!data.konu || data.konu.trim().length < 3) {
        errors.push('Konu en az 3 karakter olmalıdır.');
    }
    
    // Mesaj kontrolü
    if (!data.mesaj || data.mesaj.trim().length < 10) {
        errors.push('Mesaj en az 10 karakter olmalıdır.');
    }
    
    // Hata varsa göster
    if (errors.length > 0) {
        showFormErrors(errors);
        return false;
    }
    
    return true;
}

/**
 * Form hatalarını gösterir
 */
function showFormErrors(errors) {
    // Önceki hata mesajlarını temizle
    clearFormErrors();
    
    // Hata mesajları container'ı oluştur
    const errorContainer = document.createElement('div');
    errorContainer.className = 'alert alert-danger mt-3';
    errorContainer.innerHTML = '<ul class="mb-0">' + 
        errors.map(error => `<li>${error}</li>`).join('') + 
        '</ul>';
    
    // Form'a ekle
    const form = document.querySelector('#iletisim form');
    form.appendChild(errorContainer);
    
    // 5 saniye sonra kaldır
    setTimeout(() => {
        errorContainer.remove();
    }, 5000);
}

/**
 * Form hatalarını temizler
 */
function clearFormErrors() {
    const existingErrors = document.querySelectorAll('.alert-danger');
    existingErrors.forEach(error => error.remove());
}

/**
 * Loading durumunu gösterir
 */
function showLoadingState() {
    const submitBtn = document.querySelector('#iletisim form button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<span class="loading"></span> Gönderiliyor...';
    submitBtn.disabled = true;
    
    // 2 saniye sonra orijinal haline döndür
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

/**
 * Başarı mesajını gösterir
 */
function showSuccessMessage() {
    // Önceki mesajları temizle
    clearFormErrors();
    
    // Başarı mesajı oluştur
    const successContainer = document.createElement('div');
    successContainer.className = 'alert alert-success mt-3';
    successContainer.innerHTML = '<i class="bi bi-check-circle me-2"></i>Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.';
    
    // Form'a ekle
    const form = document.querySelector('#iletisim form');
    form.appendChild(successContainer);
    
    // 5 saniye sonra kaldır
    setTimeout(() => {
        successContainer.remove();
    }, 5000);
}

/**
 * İstatistik sayaçlarını başlatır
 */
function initCounters() {
    const counters = document.querySelectorAll('.stat-content h3');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
        const duration = 2000; // 2 saniye
        const increment = target / (duration / 16); // 60 FPS
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        // Intersection Observer ile tetikle
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
}

/**
 * Intersection Observer ile animasyonları başlatır
 */
function initIntersectionObserver() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Animasyon yapılacak elementleri gözlemle
    const animateElements = document.querySelectorAll('.feature-card, .pricing-card, .hero-content, .hero-image');
    animateElements.forEach(el => {
        observer.observe(el);
    });
}

/**
 * Genel animasyonları başlatır
 */
function initAnimations() {
    // CSS animasyonları için gerekli sınıfları ekle
    const style = document.createElement('style');
    style.textContent = `
        .feature-card, .pricing-card, .hero-content, .hero-image {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }
        
        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
        }
        
        /* Slider buton animasyonları */
        .carousel-control-prev,
        .carousel-control-next {
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .carousel:hover .carousel-control-prev,
        .carousel:hover .carousel-control-next {
            opacity: 1;
        }
        
        .carousel-control-prev:active,
        .carousel-control-next:active {
            transform: translateY(-50%) scale(0.95);
        }
    `;
    document.head.appendChild(style);
}

/**
 * Slider kontrollerini geliştirir
 */
function initSliderControls() {
    const carousel = document.querySelector('#heroCarousel');
    const prevBtn = document.querySelector('.carousel-control-prev');
    const nextBtn = document.querySelector('.carousel-control-next');
    const indicators = document.querySelectorAll('.carousel-indicators button');
    
    if (carousel) {
        // Slider değiştiğinde animasyonları tetikle
        carousel.addEventListener('slide.bs.carousel', function(e) {
            const activeSlide = e.relatedTarget;
            const slideContent = activeSlide.querySelector('.slide-content');
            
            if (slideContent) {
                // Slide içeriğini animasyonla göster
                slideContent.style.opacity = '0';
                slideContent.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    slideContent.style.transition = 'all 0.6s ease-out';
                    slideContent.style.opacity = '1';
                    slideContent.style.transform = 'translateY(0)';
                }, 100);
            }
        });
        
        // Klavye kontrolleri
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevBtn.click();
            } else if (e.key === 'ArrowRight') {
                nextBtn.click();
            }
        });
        
        // Touch/swipe desteği
        let startX = 0;
        let endX = 0;
        
        carousel.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        });
        
        carousel.addEventListener('touchend', function(e) {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const threshold = 50;
            const diff = startX - endX;
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    nextBtn.click();
                } else {
                    prevBtn.click();
                }
            }
        }
        
        // İndikatörlere hover efekti
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.3)';
                this.style.background = 'rgba(255, 255, 255, 0.8)';
            });
            
            indicator.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'scale(1)';
                    this.style.background = 'rgba(255, 255, 255, 0.5)';
                }
            });
        });
    }
}

/**
 * Utility fonksiyonlar
 */

// Debounce fonksiyonu
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle fonksiyonu
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Sayfa yükleme performansını ölç
window.addEventListener('load', function() {
    console.log('Zita Tanıtım Sitesi yüklendi');
    console.log('Performans:', performance.now(), 'ms');
});

// Hata yakalama
window.addEventListener('error', function(e) {
    console.error('JavaScript Hatası:', e.error);
});

// Console mesajı
console.log('%cZita Tanıtım Sitesi', 'color: #0d6efd; font-size: 20px; font-weight: bold;');
console.log('%cModern, hızlı ve kullanıcı dostu deneyim', 'color: #6c757d; font-size: 14px;');
