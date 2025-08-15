// Main JavaScript file for EquipRent ecommerce homepage

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initMobileNavigation();
    initCartFunctionality();
    initSmoothScrolling();
    initProductInteractions();
    initSearchFunctionality();
    initMobileOptimizations();
    initTouchGestures();
    initPerformanceOptimizations();
    initHeroCarousel();
});

// Mobile Navigation Toggle
function initMobileNavigation() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const navbar = document.querySelector('.navbar');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navMenu.classList.contains('active')) {
                document.body.classList.add('modal-open');
                
                // Ensure navbar background consistency when menu is open
                const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (isDarkMode) {
                    navbar.style.background = '#1e293b';
                } else {
                    navbar.style.background = '#ffffff';
                }
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                document.body.classList.remove('modal-open');
                
                // Restore scroll-based background
                if (window.scrollY > 100) {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = 'rgba(30, 41, 59, 0.95)';
                    } else {
                        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    }
                    navbar.style.backdropFilter = 'blur(20px)';
                } else {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = '#1e293b';
                    } else {
                        navbar.style.background = '#ffffff';
                    }
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            }
        });
        
        // Close mobile menu when clicking on a link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('modal-open');
                
                // Restore scroll-based background
                if (window.scrollY > 100) {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = 'rgba(30, 41, 59, 0.95)';
                    } else {
                        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    }
                    navbar.style.backdropFilter = 'blur(20px)';
                } else {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = '#1e293b';
                    } else {
                        navbar.style.background = '#ffffff';
                    }
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('modal-open');
                
                // Restore scroll-based background
                if (window.scrollY > 100) {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = 'rgba(30, 41, 59, 0.95)';
                    } else {
                        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    }
                    navbar.style.backdropFilter = 'blur(20px)';
                } else {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = '#1e293b';
                    } else {
                        navbar.style.background = '#ffffff';
                    }
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            }
        });
        
        // Close mobile menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('modal-open');
                
                // Restore scroll-based background
                if (window.scrollY > 100) {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = 'rgba(30, 41, 59, 0.95)';
                    } else {
                        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    }
                    navbar.style.backdropFilter = 'blur(20px)';
                } else {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = '#1e293b';
                    } else {
                        navbar.style.background = '#ffffff';
                    }
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('modal-open');
                
                // Restore scroll-based background
                if (window.scrollY > 100) {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = 'rgba(30, 41, 59, 0.95)';
                    } else {
                        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    }
                    navbar.style.backdropFilter = 'blur(20px)';
                } else {
                    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (isDarkMode) {
                        navbar.style.background = '#1e293b';
                    } else {
                        navbar.style.background = '#ffffff';
                    }
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            }
        });
    }
}

// Cart Functionality
function initCartFunctionality() {
    let cartCount = 0;
    const cartCountElement = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.btn-outline');
    
    // Initialize cart count from localStorage if available
    const savedCartCount = localStorage.getItem('cartCount');
    if (savedCartCount) {
        cartCount = parseInt(savedCartCount);
        updateCartCount();
    }
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get product info
            const productCard = button.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            const productPrice = productCard.querySelector('.price').textContent;
            
            // Add to cart
            addToCart(productName, productPrice);
            
            // Visual feedback
            button.textContent = 'Added!';
            button.style.background = '#10b981';
            button.style.color = 'white';
            button.style.borderColor = '#10b981';
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.style.background = 'transparent';
                button.style.color = '#2563eb';
                button.style.borderColor = '#2563eb';
            }, 2000);
        });
    });
    
    function addToCart(name, price) {
        cartCount++;
        updateCartCount();
        saveCartCount();
        
        // Show notification
        showNotification(`${name} added to cart!`);
    }
    
    function updateCartCount() {
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
        }
    }
    
    function saveCartCount() {
        localStorage.setItem('cartCount', cartCount.toString());
    }
    
    // Cart button click handler
    const cartBtn = document.querySelector('.cart-btn');
    if (cartBtn) {
        cartBtn.addEventListener('click', function() {
            if (cartCount > 0) {
                showNotification(`Cart has ${cartCount} item(s)`);
            } else {
                showNotification('Cart is empty');
            }
        });
    }
}

// Smooth Scrolling for Navigation Links
function initSmoothScrolling() {
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 70; // Account for fixed navbar
                
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Product Interactions
function initProductInteractions() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // Add hover effect (desktop only)
        if (window.innerWidth > 768) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
        
        // Add click to view details
        card.addEventListener('click', function(e) {
            if (!e.target.classList.contains('btn-outline')) {
                const productName = this.querySelector('h3').textContent;
                const productDescription = this.querySelector('.product-description').textContent;
                const productPrice = this.querySelector('.price').textContent;
                const productRating = this.querySelector('.rating').textContent;
                
                openProductModal(productName, productDescription, productPrice, productRating);
            }
        });
        
        // Touch feedback for mobile
        if (window.innerWidth <= 768) {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
        }
    });
}

// Search Functionality
function initSearchFunctionality() {
    const searchBtn = document.querySelector('.search-btn');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            // Create search input
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search equipment...';
            searchInput.className = 'search-input';
            
            // Style the search input
            searchInput.style.cssText = `
                position: absolute;
                top: 100%;
                right: 0;
                padding: 0.75rem;
                border: 2px solid #e2e8f0;
                border-radius: 8px;
                width: 250px;
                font-size: 0.9rem;
                outline: none;
                transition: all 0.3s ease;
                z-index: 1001;
                background: white;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            `;
            
            // Mobile responsive search input
            if (window.innerWidth <= 768) {
                searchInput.style.cssText = `
                    position: fixed;
                    top: 70px;
                    left: 0;
                    right: 0;
                    padding: 1rem;
                    border: none;
                    border-bottom: 2px solid #e2e8f0;
                    border-radius: 0;
                    width: 100%;
                    font-size: 1rem;
                    outline: none;
                    transition: all 0.3s ease;
                    z-index: 1001;
                    background: white;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                `;
            }
            
            // Add focus styles
            searchInput.addEventListener('focus', function() {
                this.style.borderColor = '#2563eb';
                this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
            });
            
            searchInput.addEventListener('blur', function() {
                this.style.borderColor = '#e2e8f0';
                this.style.boxShadow = 'none';
            });
            
            // Handle search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.trim();
                    if (searchTerm) {
                        performSearch(searchTerm);
                    }
                    this.remove();
                }
            });
            
            // Handle escape key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.remove();
                }
            });
            
            // Add to DOM
            const navActions = document.querySelector('.nav-actions');
            navActions.style.position = 'relative';
            navActions.appendChild(searchInput);
            
            // Focus and select
            searchInput.focus();
            searchInput.select();
            
            // Remove on click outside
            document.addEventListener('click', function removeSearch(e) {
                if (!searchInput.contains(e.target) && !searchBtn.contains(e.target)) {
                    searchInput.remove();
                    document.removeEventListener('click', removeSearch);
                }
            });
        });
    }
    
    function performSearch(term) {
        showNotification(`Searching for: ${term}`);
        // Here you would implement actual search functionality
        console.log('Searching for:', term);
    }
}

// Mobile Optimizations
function initMobileOptimizations() {
    // Prevent zoom on double tap (iOS)
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
    
    // Improve scroll performance on mobile
    let ticking = false;
    function updateScroll() {
        ticking = false;
        // Add any scroll-based animations here
    }
    
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScroll);
            ticking = true;
        }
    }
    
    window.addEventListener('scroll', requestTick);
    
    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            // Recalculate any layout-dependent elements
            window.dispatchEvent(new Event('resize'));
        }, 100);
    });
}

// Touch Gestures
function initTouchGestures() {
    let startX, startY, distX, distY;
    const threshold = 50; // minimum distance for swipe
    
    document.addEventListener('touchstart', function(e) {
        const touch = e.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
    });
    
    document.addEventListener('touchmove', function(e) {
        if (!startX || !startY) return;
        
        const touch = e.touches[0];
        distX = touch.clientX - startX;
        distY = touch.clientY - startY;
    });
    
    document.addEventListener('touchend', function(e) {
        if (!startX || !startY) return;
        
        if (Math.abs(distX) > Math.abs(distY)) {
            // Horizontal swipe
            if (Math.abs(distX) > threshold) {
                if (distX > 0) {
                    // Swipe right
                    console.log('Swipe right');
                } else {
                    // Swipe left
                    console.log('Swipe left');
                }
            }
        } else {
            // Vertical swipe
            if (Math.abs(distY) > threshold) {
                if (distY > 0) {
                    // Swipe down
                    console.log('Swipe down');
                } else {
                    // Swipe up
                    console.log('Swipe up');
                }
            }
        }
        
        // Reset values
        startX = startY = distX = distY = null;
    });
}

// Performance Optimizations
function initPerformanceOptimizations() {
    // Lazy load images (if you add real images later)
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Debounce resize events
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Handle resize logic here
            console.log('Window resized');
        }, 250);
    });
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#2563eb'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
        font-size: 0.9rem;
        font-weight: 500;
    `;
    
    // Mobile responsive notification
    if (window.innerWidth <= 768) {
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            left: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#2563eb'};
            color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            transform: translateY(-100px);
            transition: transform 0.3s ease;
            max-width: none;
            word-wrap: break-word;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
        `;
    }
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = window.innerWidth <= 768 ? 'translateY(0)' : 'translateX(0)';
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = window.innerWidth <= 768 ? 'translateY(-100px)' : 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Login Button Handler
function initLoginHandler() {
    // Login functionality is now handled by the modal system
    // This function is kept for compatibility but no longer needed
}

// Initialize login handler
initLoginHandler();

// Scroll Effects
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (window.scrollY > 100) {
        if (isDarkMode) {
            navbar.style.background = 'rgba(30, 41, 59, 0.95)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        }
        navbar.style.backdropFilter = 'blur(20px)';
    } else {
        if (isDarkMode) {
            navbar.style.background = '#1e293b';
        } else {
            navbar.style.background = '#ffffff';
        }
        navbar.style.backdropFilter = 'blur(10px)';
    }
});

// Handle dark mode changes
if (window.matchMedia) {
    const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
    darkModeQuery.addEventListener('change', function(e) {
        const navbar = document.querySelector('.navbar');
        if (e.matches) {
            // Dark mode enabled
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(30, 41, 59, 0.95)';
            } else {
                navbar.style.background = '#1e293b';
            }
        } else {
            // Light mode enabled
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            } else {
                navbar.style.background = '#ffffff';
            }
        }
    });
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.category-item, .product-card');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// Performance optimization: Debounce scroll events
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

// Apply debouncing to scroll events
const debouncedScrollHandler = debounce(function() {
    // Scroll-based animations and effects
}, 10);

window.addEventListener('scroll', debouncedScrollHandler);

// Hero Carousel Functionality
function initHeroCarousel() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    let currentSlide = 0;
    let autoPlayInterval;
    
    if (slides.length === 0) return;
    
    // Function to show a specific slide
    function showSlide(index) {
        // Remove active class from all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Add active class to current slide and dot
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        
        currentSlide = index;
    }
    
    // Function to go to next slide
    function nextSlide() {
        const nextIndex = (currentSlide + 1) % slides.length;
        showSlide(nextIndex);
    }
    
    // Function to go to previous slide
    function prevSlide() {
        const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
    }
    
    // Add click event listeners to dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            resetAutoPlay();
        });
    });
    
    // Auto-play functionality
    function startAutoPlay() {
        autoPlayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
    }
    
    function resetAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            startAutoPlay();
        }
    }
    
    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
        }
    }
    
    // Start auto-play
    startAutoPlay();
    
    // Pause auto-play on hover (desktop only)
    if (window.innerWidth > 768) {
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', stopAutoPlay);
            heroSection.addEventListener('mouseleave', startAutoPlay);
        }
    }
    
    // Touch/swipe support for mobile
    let startX, startY, distX, distY;
    const threshold = 50;
    
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        heroSection.addEventListener('touchstart', function(e) {
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
        });
        
        heroSection.addEventListener('touchmove', function(e) {
            if (!startX || !startY) return;
            
            const touch = e.touches[0];
            distX = touch.clientX - startX;
            distY = touch.clientY - startY;
        });
        
        heroSection.addEventListener('touchend', function(e) {
            if (!startX || !startY) return;
            
            if (Math.abs(distX) > Math.abs(distY) && Math.abs(distX) > threshold) {
                if (distX > 0) {
                    // Swipe right - go to previous slide
                    prevSlide();
                } else {
                    // Swipe left - go to next slide
                    nextSlide();
                }
                resetAutoPlay();
            }
            
            // Reset values
            startX = startY = distX = distY = null;
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            resetAutoPlay();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            resetAutoPlay();
        }
    });
    
    // Pause auto-play when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoPlay();
        } else {
            startAutoPlay();
        }
    });
    
    // Responsive adjustments
    function handleResize() {
        if (window.innerWidth <= 768) {
            // On mobile, reduce auto-play interval
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
                autoPlayInterval = setInterval(nextSlide, 4000); // 4 seconds on mobile
            }
        } else {
            // On desktop, normal interval
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
                autoPlayInterval = setInterval(nextSlide, 5000); // 5 seconds on desktop
            }
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', handleResize);
    
    // Initial resize call
    handleResize();
}

// Global functions
function openAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

async function logout() {
    try {
        const response = await fetch('controller/logout.php', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update global login status if we're on the browse page
            if (window.updateLoginStatus) {
                window.updateLoginStatus(false);
            }
            
            // Redirect to homepage
            window.location.href = data.redirect;
        }
    } catch (error) {
        console.error('Logout error:', error);
        // Fallback redirect
        window.location.href = 'index.php';
    }
}

function updateLoginStatus(isLoggedIn) {
    // Update global login status
    window.isUserLoggedIn = isLoggedIn;
    
    // Refresh equipment display to update button states if we're on the browse page
    if (window.equipmentBrowser && window.equipmentBrowser.refreshEquipmentDisplay) {
        window.equipmentBrowser.refreshEquipmentDisplay();
    }
}
