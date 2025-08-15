// Product Modal JavaScript

// Global variables for focus management
let productModalFocusableElements = [];
let productFirstFocusableElement = null;
let productLastFocusableElement = null;

// Product Modal Functions
function openProductModal(productName, description, price, rating) {
    const modal = document.getElementById('productModal');
    if (modal) {
        // Update modal content with product data
        updateProductModalContent(productName, description, price, rating);
        
        // Show modal
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
        
        // Prevent background scrolling
        preventBackgroundScroll();
        
        // Set up focus management
        setupProductModalFocus(modal);
        
        // Set minimum date for date inputs
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        
        if (startDateInput) startDateInput.min = today;
        if (endDateInput) endDateInput.min = today;
        
        // Add event listeners for date inputs
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', updateRentalCalculation);
            endDateInput.addEventListener('change', updateRentalCalculation);
        }
        
        // Add event listeners for focus trapping
        addProductModalEventListeners(modal);
        
        // Focus on first focusable element
        setTimeout(() => {
            const firstFocusableElement = modal.querySelector(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
        }, 100);
    }
}

function closeProductModal() {
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Restore background scrolling
        restoreBackgroundScroll();
        
        // Remove event listeners
        removeProductModalEventListeners(modal);
        
        // Reset date inputs
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('startDate');
        if (startDateInput) startDateInput.value = '';
        if (endDateInput) endDateInput.value = '';
        
        // Reset rental calculation
        updateRentalCalculation();
    }
}

// Enhanced product modal focus management
function setupProductModalFocus(modal) {
    // Focus management is handled in the open function
    // Store the element that had focus before modal opened
    modal.dataset.previousFocus = document.activeElement ? document.activeElement.id || 'body' : 'body';
}

function addProductModalEventListeners(modal) {
    // Focus trap with Tab key
    modal.addEventListener('keydown', handleProductModalKeydown);
    
    // Prevent clicks outside modal from closing it (unless clicking on backdrop)
    modal.addEventListener('click', handleProductModalClick);
    
    // Handle window resize to ensure modal stays properly positioned
    window.addEventListener('resize', () => handleProductModalResize(modal));
}

function removeProductModalEventListeners(modal) {
    modal.removeEventListener('keydown', handleProductModalKeydown);
    modal.removeEventListener('click', handleProductModalClick);
    window.removeEventListener('resize', () => handleProductModalResize(modal));
}

function handleProductModalKeydown(event) {
    if (event.key === 'Tab') {
        // Get all focusable elements in the modal
        const focusableElements = event.currentTarget.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (event.shiftKey) {
            // Shift + Tab: go to previous element
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab: go to next element
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    } else if (event.key === 'Escape') {
        // Close modal on Escape
        closeProductModal();
    }
}

function handleProductModalClick(event) {
    // Only close if clicking on the modal backdrop (not on modal content)
    if (event.target === event.currentTarget) {
        closeProductModal();
    }
}

function handleProductModalResize(modal) {
    // Ensure modal stays centered and properly sized on window resize
    if (modal.style.display === 'flex') {
        // Force a reflow to ensure proper positioning
        modal.style.display = 'none';
        modal.offsetHeight; // Trigger reflow
        modal.style.display = 'flex';
    }
}

// Product modal content management
function updateProductModalContent(productName, description, price, rating) {
    // Update title
    const titleElement = document.querySelector('.product-title');
    if (titleElement) {
        titleElement.textContent = productName;
    }
    
    // Update description
    const descElement = document.querySelector('.product-description-full p');
    if (descElement) {
        descElement.textContent = description;
    }
    
    // Update price (extract number from price string like "$25/day")
    const priceMatch = price.match(/\$(\d+)/);
    if (priceMatch) {
        const priceAmount = priceMatch[1];
        const priceAmountElement = document.querySelector('.price-amount');
        if (priceAmountElement) {
            priceAmountElement.textContent = `$${priceAmount}`;
        }
        
        // Update weekly and monthly prices
        const weeklyPrice = parseInt(priceAmount) * 6; // 6 days for weekly
        const monthlyPrice = parseInt(priceAmount) * 20; // 20 days for monthly
        
        const priceOptions = document.querySelectorAll('.price-option');
        if (priceOptions.length >= 2) {
            priceOptions[0].textContent = `$${weeklyPrice}/week`;
            priceOptions[1].textContent = `$${monthlyPrice}/month`;
        }
    }
    
    // Update rating
    const ratingMatch = rating.match(/(\d+\.?\d*)/);
    if (ratingMatch) {
        const ratingValue = parseFloat(ratingMatch[1]);
        updateStarRating(ratingValue);
    }
}

function updateStarRating(rating) {
    const starsContainer = document.querySelector('.stars');
    if (!starsContainer) return;
    
    // Clear existing stars
    starsContainer.innerHTML = '';
    
    // Add full stars
    const fullStars = Math.floor(rating);
    for (let i = 0; i < fullStars; i++) {
        const star = document.createElement('i');
        star.className = 'fas fa-star';
        starsContainer.appendChild(star);
    }
    
    // Add half star if needed
    if (rating % 1 !== 0) {
        const halfStar = document.createElement('i');
        halfStar.className = 'fas fa-star-half-alt';
        starsContainer.appendChild(halfStar);
    }
    
    // Add empty stars to complete 5 stars
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        const star = document.createElement('i');
        star.className = 'far fa-star';
        starsContainer.appendChild(star);
    }
}

function updateRentalCalculation() {
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const rentalDaysElement = document.querySelector('.rental-days');
    const rentalTotalElement = document.querySelector('.rental-total');
    
    if (!startDateInput || !endDateInput || !rentalDaysElement || !rentalTotalElement) return;
    
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    if (startDateInput.value && endDateInput.value && endDate > startDate) {
        const timeDiff = endDate.getTime() - startDate.getTime();
        const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        // Get price from the modal
        const priceAmountElement = document.querySelector('.price-amount');
        if (priceAmountElement) {
            const priceText = priceAmountElement.textContent;
            const priceMatch = priceText.match(/\$(\d+)/);
            if (priceMatch) {
                const dailyPrice = parseInt(priceMatch[1]);
                const totalPrice = dailyPrice * daysDiff;
                
                rentalDaysElement.textContent = `${daysDiff} day${daysDiff > 1 ? 's' : ''}`;
                rentalTotalElement.textContent = `Total: $${totalPrice}`;
            }
        }
    } else {
        rentalDaysElement.textContent = '0 days';
        rentalTotalElement.textContent = 'Total: $0';
    }
}

// Product action functions
function addToCart() {
    showNotification('Added to cart!', 'success');
}

function chatWithSeller() {
    showNotification('Chat feature coming soon!', 'info');
}

// Close product modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeProductModal();
    }
});
