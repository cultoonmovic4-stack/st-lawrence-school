// ST. LAWRENCE JUNIOR SCHOOL - MODERN JAVASCRIPT

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // NAVIGATION
    // ========================================
    const header = document.getElementById('header');
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Sticky header on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    if (hamburger && navMenu) {
        console.log('Hamburger menu initialized');
        hamburger.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Hamburger clicked');
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
            console.log('Menu active:', navMenu.classList.contains('active'));
        });
    } else {
        console.error('Hamburger or navMenu not found', {hamburger, navMenu});
    }
    
    // Handle dropdown in mobile
    const navDropdown = document.querySelector('.nav-dropdown');
    if (navDropdown) {
        const dropdownLink = navDropdown.querySelector('.nav-link');
        dropdownLink.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                e.preventDefault();
                navDropdown.classList.toggle('active');
            }
        });
    }
    
    // Close mobile menu when clicking nav link
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't close if it's the dropdown toggle
            if (!this.closest('.nav-dropdown')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.header') && navMenu.classList.contains('active')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Active nav link on scroll
    const sections = document.querySelectorAll('section[id]');
    
    window.addEventListener('scroll', function() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                document.querySelector('.nav-link[href*=' + sectionId + ']')?.classList.add('active');
            } else {
                document.querySelector('.nav-link[href*=' + sectionId + ']')?.classList.remove('active');
            }
        });
    });
    
    // ========================================
    // HERO SLIDER - CLEAN VERSION
    // ========================================
    const slides = document.querySelectorAll('.hero-slide');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const progressBar = document.getElementById('progressBar');
    let currentSlide = 0;
    let slideInterval;
    let isPlaying = true;
    let progressInterval;
    let progress = 0;
    const slideDuration = 6000; // 6 seconds
    
    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        
        if (n >= slides.length) currentSlide = 0;
        if (n < 0) currentSlide = slides.length - 1;
        
        slides[currentSlide].classList.add('active');
        
        // Reset progress
        progress = 0;
        updateProgress();
    }
    
    function nextSlide() {
        currentSlide++;
        showSlide(currentSlide);
    }
    
    function prevSlide() {
        currentSlide--;
        showSlide(currentSlide);
    }
    
    function updateProgress() {
        if (isPlaying) {
            progress += 100 / (slideDuration / 100);
            if (progress >= 100) {
                progress = 0;
                nextSlide();
            }
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
        }
    }
    
    function startSlideShow() {
        if (isPlaying) {
            progressInterval = setInterval(updateProgress, 100);
        }
    }
    
    function stopSlideShow() {
        clearInterval(progressInterval);
    }
    
    function togglePlayPause() {
        isPlaying = !isPlaying;
        const icon = playPauseBtn.querySelector('i');
        
        if (isPlaying) {
            icon.className = 'fas fa-pause';
            playPauseBtn.setAttribute('aria-label', 'Pause slideshow');
            startSlideShow();
        } else {
            icon.className = 'fas fa-play';
            playPauseBtn.setAttribute('aria-label', 'Play slideshow');
            stopSlideShow();
        }
    }
    
    // Button controls
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            stopSlideShow();
            nextSlide();
            if (isPlaying) startSlideShow();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            stopSlideShow();
            prevSlide();
            if (isPlaying) startSlideShow();
        });
    }
    
    // Play/Pause button
    if (playPauseBtn) {
        playPauseBtn.addEventListener('click', togglePlayPause);
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            stopSlideShow();
            prevSlide();
            if (isPlaying) startSlideShow();
        }
        if (e.key === 'ArrowRight') {
            stopSlideShow();
            nextSlide();
            if (isPlaying) startSlideShow();
        }
        if (e.key === ' ') {
            e.preventDefault();
            togglePlayPause();
        }
    });
    
    // Pause on hover
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        heroSlider.addEventListener('mouseenter', function() {
            if (isPlaying) {
                stopSlideShow();
            }
        });
        heroSlider.addEventListener('mouseleave', function() {
            if (isPlaying) {
                startSlideShow();
            }
        });
    }
    
    // Start slideshow
    startSlideShow();
    
    // ========================================
    // SMOOTH SCROLL
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // ========================================
    // SUBJECTS CAROUSEL - AUTO SCROLL (ONE CARD AT A TIME)
    // ========================================
    const subjectsCarousel = document.querySelector('.subjects-carousel');
    if (subjectsCarousel) {
        const subjectCards = subjectsCarousel.querySelectorAll('.subject-card-large');
        const prevSubjectBtn = document.getElementById('prevSubject');
        const nextSubjectBtn = document.getElementById('nextSubject');
        let currentSubjectIndex = 0;
        
        function scrollToSubject(index) {
            if (subjectCards[index]) {
                const cardWidth = subjectCards[index].offsetWidth;
                const gap = 32; // 2rem gap
                const scrollPosition = (cardWidth + gap) * index;
                
                subjectsCarousel.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });
            }
        }
        
        function autoScrollSubjects() {
            currentSubjectIndex = (currentSubjectIndex + 1) % subjectCards.length;
            scrollToSubject(currentSubjectIndex);
        }
        
        // Auto scroll every 2.5 seconds (faster)
        let subjectsInterval = setInterval(autoScrollSubjects, 2500);
        
        // Next button
        if (nextSubjectBtn) {
            nextSubjectBtn.addEventListener('click', () => {
                currentSubjectIndex = (currentSubjectIndex + 1) % subjectCards.length;
                scrollToSubject(currentSubjectIndex);
                clearInterval(subjectsInterval);
                subjectsInterval = setInterval(autoScrollSubjects, 2500);
            });
        }
        
        // Previous button
        if (prevSubjectBtn) {
            prevSubjectBtn.addEventListener('click', () => {
                currentSubjectIndex = (currentSubjectIndex - 1 + subjectCards.length) % subjectCards.length;
                scrollToSubject(currentSubjectIndex);
                clearInterval(subjectsInterval);
                subjectsInterval = setInterval(autoScrollSubjects, 2500);
            });
        }
        
        // Pause on hover
        subjectsCarousel.addEventListener('mouseenter', () => {
            clearInterval(subjectsInterval);
        });
        
        subjectsCarousel.addEventListener('mouseleave', () => {
            subjectsInterval = setInterval(autoScrollSubjects, 2500);
        });
        
        // Pause on manual scroll
        let subjectsScrollTimeout;
        subjectsCarousel.addEventListener('scroll', () => {
            clearInterval(subjectsInterval);
            clearTimeout(subjectsScrollTimeout);
            
            subjectsScrollTimeout = setTimeout(() => {
                subjectsInterval = setInterval(autoScrollSubjects, 2500);
            }, 2000);
        });
    }
    
    // ========================================
    // TESTIMONIALS CAROUSEL - AUTO SCROLL (ONE CARD AT A TIME)
    // ========================================
    const testimonialsCarousel = document.querySelector('.testimonials-carousel');
    if (testimonialsCarousel) {
        const testimonialCards = testimonialsCarousel.querySelectorAll('.testimonial-card');
        let currentTestimonialIndex = 0;
        
        function scrollToTestimonial(index) {
            if (testimonialCards[index]) {
                const cardWidth = testimonialCards[index].offsetWidth;
                const gap = 32; // 2rem gap
                const scrollPosition = (cardWidth + gap) * index;
                
                testimonialsCarousel.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });
            }
        }
        
        function autoScrollTestimonials() {
            currentTestimonialIndex = (currentTestimonialIndex + 1) % testimonialCards.length;
            scrollToTestimonial(currentTestimonialIndex);
        }
        
        // Auto scroll every 5 seconds
        let testimonialsInterval = setInterval(autoScrollTestimonials, 5000);
        
        // Pause on hover
        testimonialsCarousel.addEventListener('mouseenter', () => {
            clearInterval(testimonialsInterval);
        });
        
        testimonialsCarousel.addEventListener('mouseleave', () => {
            testimonialsInterval = setInterval(autoScrollTestimonials, 5000);
        });
        
        // Pause on manual scroll
        let testimonialsScrollTimeout;
        testimonialsCarousel.addEventListener('scroll', () => {
            clearInterval(testimonialsInterval);
            clearTimeout(testimonialsScrollTimeout);
            
            testimonialsScrollTimeout = setTimeout(() => {
                testimonialsInterval = setInterval(autoScrollTestimonials, 5000);
            }, 2000);
        });
    }
    
    // ========================================
    // GALLERY CATEGORY FILTER
    // ========================================
    const categoryButtons = document.querySelectorAll('.category-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    console.log('Category buttons found:', categoryButtons.length);
    console.log('Gallery items found:', galleryItems.length);
    
    if (categoryButtons.length > 0 && galleryItems.length > 0) {
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Button clicked:', this.getAttribute('data-category'));
                
                // Remove active class from all buttons
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get selected category
                const category = this.getAttribute('data-category');
                
                // Filter gallery items
                galleryItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    
                    if (category === 'all' || itemCategory === category) {
                        item.style.display = 'block';
                        console.log('Showing item:', itemCategory);
                    } else {
                        item.style.display = 'none';
                        console.log('Hiding item:', itemCategory);
                    }
                });
            });
        });
    } else {
        console.error('Gallery elements not found!');
    }
    
    // ========================================
    // GALLERY ZOOM FUNCTIONALITY
    // ========================================
    const galleryZoomButtons = document.querySelectorAll('.gallery-zoom');
    
    if (galleryZoomButtons.length > 0) {
        galleryZoomButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const galleryItem = button.closest('.gallery-item');
                const img = galleryItem.querySelector('img');
                const imgSrc = img.getAttribute('src');
                
                // Create lightbox
                const lightbox = document.createElement('div');
                lightbox.className = 'gallery-lightbox';
                lightbox.innerHTML = `
                    <div class="lightbox-content">
                        <button class="lightbox-close"><i class="fas fa-times"></i></button>
                        <img src="${imgSrc}" alt="Gallery Image">
                    </div>
                `;
                
                document.body.appendChild(lightbox);
                document.body.style.overflow = 'hidden';
                
                // Animate in
                setTimeout(() => {
                    lightbox.classList.add('active');
                }, 10);
                
                // Close lightbox
                const closeBtn = lightbox.querySelector('.lightbox-close');
                closeBtn.addEventListener('click', () => {
                    lightbox.classList.remove('active');
                    setTimeout(() => {
                        document.body.removeChild(lightbox);
                        document.body.style.overflow = '';
                    }, 300);
                });
                
                // Close on background click
                lightbox.addEventListener('click', (e) => {
                    if (e.target === lightbox) {
                        lightbox.classList.remove('active');
                        setTimeout(() => {
                            document.body.removeChild(lightbox);
                            document.body.style.overflow = '';
                        }, 300);
                    }
                });
            });
        });
    }
    
});

// Add CSS animations for gallery filter
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
    
    .gallery-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gallery-lightbox.active {
        opacity: 1;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        animation: zoomIn 0.3s ease;
    }
    
    @keyframes zoomIn {
        from {
            transform: scale(0.8);
        }
        to {
            transform: scale(1);
        }
    }
    
    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    
    .lightbox-close {
        position: absolute;
        top: -50px;
        right: 0;
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 2px solid white;
        border-radius: 50%;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-close:hover {
        background: white;
        color: #1a1a1a;
        transform: rotate(90deg);
    }
`;
document.head.appendChild(style);
