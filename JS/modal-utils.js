// Modal Utilities - Shared functions for all modals

// Background scroll prevention functions (shared by all modals)
function preventBackgroundScroll() {
    const scrollY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
    
    // Store scroll position for restoration
    document.body.dataset.scrollY = scrollY.toString();
    
    // Prevent iOS Safari from bouncing
    document.documentElement.style.overflow = 'hidden';
    document.documentElement.style.position = 'fixed';
    document.documentElement.style.width = '100%';
    document.documentElement.style.height = '100%';
}

function restoreBackgroundScroll() {
    const scrollY = parseInt(document.body.dataset.scrollY || '0');
    
    // Restore body styles
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    
    // Restore document element styles
    document.documentElement.style.overflow = '';
    document.documentElement.style.position = '';
    document.documentElement.style.width = '';
    document.documentElement.style.height = '';
    
    // Restore scroll position
    window.scrollTo(0, scrollY);
    
    // Clean up
    delete document.body.dataset.scrollY;
}

// Enhanced touch event handling for modals
function preventScrollOnTouch(event) {
    if (!document.body.classList.contains('modal-open')) {
        return;
    }
    
    const target = event.target;
    const modal = target.closest('.modal');
    
    if (!modal) {
        // Touch outside any modal - prevent scroll
        event.preventDefault();
        return;
    }
    
    const modalContent = target.closest('.modal-content');
    if (!modalContent) {
        // Touch on modal backdrop - prevent scroll
        event.preventDefault();
        return;
    }
    
    // Touch inside modal content - allow scrolling within modal
    // Check if we're at the top or bottom of scrollable content
    const content = modalContent;
    const touch = event.touches[0];
    const currentY = touch.clientY;
    
    // Store initial touch position for comparison
    if (!content.dataset.touchStartY) {
        content.dataset.touchStartY = currentY;
    }
    
    const touchStartY = parseInt(content.dataset.touchStartY);
    const deltaY = currentY - touchStartY;
    
    if (content.scrollTop === 0 && deltaY > 0) {
        // At top and trying to scroll up - prevent
        event.preventDefault();
    } else if (content.scrollTop + content.clientHeight >= content.scrollHeight && deltaY < 0) {
        // At bottom and trying to scroll down - prevent
        event.preventDefault();
    }
}

// Clean up touch data when touch ends
document.addEventListener('touchend', function() {
    const modalContents = document.querySelectorAll('.modal-content');
    modalContents.forEach(content => {
        delete content.dataset.touchStartY;
    });
});

// Add touch event listener for modal scroll prevention
document.addEventListener('touchmove', preventScrollOnTouch, { passive: false });

// Common modal utilities
function getFocusableElements(container) {
    return container.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
}

function setupModalFocus(modal, focusableElements) {
    if (focusableElements.length > 0) {
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        // Store the element that had focus before modal opened
        modal.dataset.previousFocus = document.activeElement ? document.activeElement.id || 'body' : 'body';
        
        return { firstElement, lastElement };
    }
    return null;
}

function handleModalTabKey(event, firstElement, lastElement) {
    if (event.key === 'Tab') {
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
    }
}

function handleModalResize(modal) {
    // Ensure modal stays centered and properly sized on window resize
    if (modal.style.display === 'flex') {
        // Force a reflow to ensure proper positioning
        modal.style.display = 'none';
        modal.offsetHeight; // Trigger reflow
        modal.style.display = 'flex';
    }
}

// Ensure body scroll is restored when page is unloaded or refreshed
window.addEventListener('beforeunload', function() {
    document.body.classList.remove('modal-open');
});

// Handle page visibility change to ensure scroll is restored
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        document.body.classList.remove('modal-open');
    }
});

// Global escape key handler for all modals
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Close any open modal
        const authModal = document.getElementById('authModal');
        if (authModal && authModal.style.display === 'flex') {
            closeAuthModal();
        }
        
        const enlistModal = document.getElementById('enlistModal');
        if (enlistModal && enlistModal.style.display === 'flex') {
            closeEnlistModal();
        }
        
        const productModal = document.getElementById('productModal');
        if (productModal && productModal.style.display === 'flex') {
            closeProductModal();
        }
    }
});
