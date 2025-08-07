/**
 * Nekomata Store - Enhanced JavaScript
 * Modern e-commerce functionality with animations and interactive features
 */

// ==========================================
// GLOBAL VARIABLES & CONFIGURATION
// ==========================================

const NEKOMATA = {
    config: {
        apiEndpoints: {
            cart: '/cart',
            search: '/products/search',
            wishlist: '/wishlist'
        },
        animations: {
            duration: 300,
            easing: 'ease-in-out'
        }
    },
    
    // Cart management
    cart: {
        count: 0,
        total: 0
    },
    
    // Search functionality
    search: {
        debounceTimer: null,
        minLength: 2
    }
};

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${getToastIcon(type)} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function getToastIcon(type) {
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-triangle',
        warning: 'exclamation-circle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Debounce function for search
 */
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

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Animate element
 */
function animateElement(element, animation) {
    element.classList.add('animate__animated', `animate__${animation}`);
    element.addEventListener('animationend', () => {
        element.classList.remove('animate__animated', `animate__${animation}`);
    }, { once: true });
}

// ==========================================
// CART FUNCTIONALITY
// ==========================================

/**
 * Update cart count in navigation
 */
function updateCartCount() {
    fetch(`${window.location.origin}/get_cart_count.php`)
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                if (data.count > 0) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = 'inline';
                    animateElement(cartBadge, 'pulse');
                } else {
                    cartBadge.style.display = 'none';
                }
                NEKOMATA.cart.count = data.count;
            }
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
        });
}

/**
 * Update wishlist count in navigation
 */
function updateWishlistCount() {
    fetch(`${window.location.origin}/wishlist/count`)
        .then(response => response.json())
        .then(data => {
            const wishlistBadge = document.getElementById('wishlist-count');
            if (wishlistBadge) {
                if (data.count > 0) {
                    wishlistBadge.textContent = data.count;
                    wishlistBadge.style.display = 'inline';
                    animateElement(wishlistBadge, 'pulse');
                } else {
                    wishlistBadge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error updating wishlist count:', error);
        });
}

/**
 * Add item to cart
 */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch(`${window.location.origin}/add_to_cart.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateCartCount();
            
            // Add visual feedback to the add to cart button
            const button = document.querySelector(`[data-product-id="${productId}"]`);
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.classList.add('btn-success');
                button.disabled = true;
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.disabled = false;
                }, 2000);
            }
        } else {
            showToast(data.message || 'Error adding item to cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showToast('Error adding item to cart', 'danger');
    });
}

/**
 * Update cart item quantity
 */
function updateCartQuantity(cartItemId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartItemId);
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', quantity);
    
    fetch(`${window.location.origin}/update_cart.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
            updateCartCount();
        } else {
            showToast(data.message || 'Error updating cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating cart:', error);
        showToast('Error updating cart', 'danger');
    });
}

/**
 * Remove item from cart
 */
function removeFromCart(cartItemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    
    fetch(`${window.location.origin}/remove_from_cart.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Item removed from cart', 'info');
            updateCartDisplay();
            updateCartCount();
        } else {
            showToast(data.message || 'Error removing item', 'danger');
        }
    })
    .catch(error => {
        console.error('Error removing from cart:', error);
        showToast('Error removing item from cart', 'danger');
    });
}

/**
 * Update cart display on cart page
 */
function updateCartDisplay() {
    if (window.location.pathname.includes('/cart')) {
        location.reload();
    }
}

// ==========================================
// WISHLIST FUNCTIONALITY
// ==========================================

/**
 * Add item to wishlist
 */
function addToWishlist(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(`${window.location.origin}/wishlist/add`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateWishlistCount();
            
            // Update wishlist button state
            const button = document.querySelector(`[data-wishlist-id="${productId}"]`);
            if (button) {
                button.innerHTML = '<i class="fas fa-heart text-danger"></i> In Wishlist';
                button.classList.add('btn-outline-danger');
                button.classList.remove('btn-outline-primary');
                button.disabled = true;
            }
        } else {
            showToast(data.message || 'Error adding item to wishlist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding to wishlist:', error);
        showToast('Error adding item to wishlist', 'danger');
    });
}

/**
 * Remove item from wishlist
 */
function removeFromWishlist(wishlistId) {
    if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('wishlist_id', wishlistId);
    
    fetch(`${window.location.origin}/wishlist/remove`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'info');
            updateWishlistCount();
            
            // Remove item from wishlist page
            const wishlistItem = document.querySelector(`[data-wishlist-item="${wishlistId}"]`);
            if (wishlistItem) {
                wishlistItem.remove();
                
                // Check if wishlist is empty
                const remainingItems = document.querySelectorAll('[data-wishlist-item]');
                if (remainingItems.length === 0) {
                    location.reload();
                }
            }
        } else {
            showToast(data.message || 'Error removing item from wishlist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error removing from wishlist:', error);
        showToast('Error removing item from wishlist', 'danger');
    });
}

/**
 * Move item from wishlist to cart
 */
function moveToCart(wishlistId) {
    const formData = new FormData();
    
    fetch(`${window.location.origin}/wishlist/moveToCart/${wishlistId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateWishlistCount();
            updateCartCount();
            
            // Remove item from wishlist page
            const wishlistItem = document.querySelector(`[data-wishlist-item="${wishlistId}"]`);
            if (wishlistItem) {
                wishlistItem.remove();
            }
        } else {
            showToast(data.message || 'Error moving item to cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error moving to cart:', error);
        showToast('Error moving item to cart', 'danger');
    });
}

/**
 * Clear entire wishlist
 */
function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist? This action cannot be undone.')) {
        return;
    }
    
    fetch(`${window.location.origin}/wishlist/clear`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'info');
            updateWishlistCount();
            location.reload();
        } else {
            showToast(data.message || 'Error clearing wishlist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error clearing wishlist:', error);
        showToast('Error clearing wishlist', 'danger');
    });
}

// ==========================================
// SEARCH FUNCTIONALITY
// ==========================================

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchInput = document.querySelector('input[name="q"]');
    if (!searchInput) return;
    
    const debouncedSearch = debounce(performLiveSearch, 300);
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        if (query.length >= NEKOMATA.search.minLength) {
            debouncedSearch(query);
        } else {
            hideSearchResults();
        }
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-form')) {
            hideSearchResults();
        }
    });
}

/**
 * Perform live search
 */
function performLiveSearch(query) {
    const searchForm = document.querySelector('.search-form');
    if (!searchForm) return;
    
    // Show loading state
    showSearchLoading();
    
    fetch(`${window.location.origin}/search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data.results || []);
        })
        .catch(error => {
            console.error('Search error:', error);
            hideSearchResults();
        });
}

/**
 * Display search results dropdown
 */
function displaySearchResults(results) {
    let dropdown = document.querySelector('.search-dropdown');
    
    if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.className = 'search-dropdown position-absolute bg-white border rounded shadow-lg';
        dropdown.style.cssText = 'top: 100%; left: 0; right: 0; z-index: 1000; max-height: 300px; overflow-y: auto;';
        
        const searchForm = document.querySelector('.search-form');
        searchForm.style.position = 'relative';
        searchForm.appendChild(dropdown);
    }
    
    if (results.length === 0) {
        dropdown.innerHTML = '<div class="p-3 text-muted">No products found</div>';
    } else {
        dropdown.innerHTML = results.map(product => `
            <a href="/product.php?id=${product.product_id}" class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom search-result-item">
                <img src="${product.image_url || '/assets/images/placeholder.jpg'}" alt="${product.name}" class="me-3 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="flex-grow-1">
                    <div class="fw-medium">${product.name}</div>
                    <div class="text-primary fw-bold">${formatCurrency(product.price)}</div>
                </div>
            </a>
        `).join('');
    }
    
    dropdown.style.display = 'block';
}

/**
 * Show search loading state
 */
function showSearchLoading() {
    let dropdown = document.querySelector('.search-dropdown');
    if (dropdown) {
        dropdown.innerHTML = '<div class="p-3 text-center"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';
        dropdown.style.display = 'block';
    }
}

/**
 * Hide search results
 */
function hideSearchResults() {
    const dropdown = document.querySelector('.search-dropdown');
    if (dropdown) {
        dropdown.style.display = 'none';
    }
}

// ==========================================
// PRODUCT FUNCTIONALITY
// ==========================================

/**
 * Initialize product page functionality
 */
function initializeProductPage() {
    // Quantity controls
    const quantityInput = document.querySelector('.quantity-input');
    const minusBtn = document.querySelector('.quantity-minus');
    const plusBtn = document.querySelector('.quantity-plus');
    
    if (quantityInput && minusBtn && plusBtn) {
        minusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        plusBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            const maxStock = parseInt(quantityInput.getAttribute('max'));
            if (currentValue < maxStock) {
                quantityInput.value = currentValue + 1;
            }
        });
    }
    
    // Product image gallery
    initializeImageGallery();
    
    // Product reviews
    initializeReviews();
}

/**
 * Initialize image gallery
 */
function initializeImageGallery() {
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    const mainImage = document.querySelector('.main-product-image');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update main image
            if (mainImage) {
                mainImage.src = this.href;
                mainImage.alt = this.querySelector('img').alt;
            }
            
            // Update active thumbnail
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

/**
 * Initialize product reviews
 */
function initializeReviews() {
    const reviewForm = document.querySelector('.review-form');
    if (!reviewForm) return;
    
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Review submitted successfully!', 'success');
                this.reset();
                // Reload reviews section
                location.reload();
            } else {
                showToast(data.message || 'Error submitting review', 'danger');
            }
        })
        .catch(error => {
            console.error('Error submitting review:', error);
            showToast('Error submitting review', 'danger');
        });
    });
}

// ==========================================
// ADMIN DASHBOARD CHARTS
// ==========================================

/**
 * Initialize admin dashboard charts
 */
function initializeAdminCharts() {
    if (!document.querySelector('.admin-dashboard')) return;
    
    // Sales chart
    initializeSalesChart();
    
    // Products chart
    initializeProductsChart();
    
    // Orders chart
    initializeOrdersChart();
    
    // Revenue chart
    initializeRevenueChart();
}

/**
 * Initialize sales chart
 */
function initializeSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Initialize products chart
 */
function initializeProductsChart() {
    const ctx = document.getElementById('productsChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Electronics', 'Clothing', 'Books', 'Home & Garden'],
            datasets: [{
                data: [30, 25, 20, 25],
                backgroundColor: [
                    '#6366f1',
                    '#f59e0b',
                    '#10b981',
                    '#ec4899'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Initialize orders chart
 */
function initializeOrdersChart() {
    const ctx = document.getElementById('ordersChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Orders',
                data: [12, 19, 15, 17, 14, 22, 18],
                backgroundColor: '#10b981',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

/**
 * Initialize revenue chart
 */
function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Revenue',
                data: [1200, 1900, 1500, 2100],
                borderColor: '#ec4899',
                backgroundColor: 'rgba(236, 72, 153, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// ==========================================
// FORM ENHANCEMENTS
// ==========================================

/**
 * Initialize form enhancements
 */
function initializeForms() {
    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !form.classList.contains('no-loading')) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    });
    
    // Enhanced form validation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('blur', validateInput);
        input.addEventListener('input', clearValidation);
    });
}

/**
 * Validate input field
 */
function validateInput(e) {
    const input = e.target;
    const value = input.value.trim();
    
    // Remove existing validation classes
    input.classList.remove('is-valid', 'is-invalid');
    
    // Basic validation
    if (input.hasAttribute('required') && !value) {
        input.classList.add('is-invalid');
        return false;
    }
    
    // Email validation
    if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            input.classList.add('is-invalid');
            return false;
        }
    }
    
    // Password validation
    if (input.type === 'password' && value) {
        if (value.length < 6) {
            input.classList.add('is-invalid');
            return false;
        }
    }
    
    input.classList.add('is-valid');
    return true;
}

/**
 * Clear validation styling
 */
function clearValidation(e) {
    const input = e.target;
    input.classList.remove('is-valid', 'is-invalid');
}

// ==========================================
// INITIALIZATION
// ==========================================

/**
 * Initialize all functionality when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count
    updateCartCount();
    
    // Update wishlist count
    updateWishlistCount();
    
    // Initialize search
    initializeSearch();
    
    // Initialize product page
    initializeProductPage();
    
    // Initialize forms
    initializeForms();
    
    // Initialize admin charts
    initializeAdminCharts();
    
    // Add to cart button handlers
    document.addEventListener('click', function(e) {
        if (e.target.matches('.add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
            e.preventDefault();
            const button = e.target.matches('.add-to-cart-btn') ? e.target : e.target.closest('.add-to-cart-btn');
            const productId = button.getAttribute('data-product-id');
            const quantity = button.getAttribute('data-quantity') || 1;
            
            if (productId) {
                addToCart(productId, quantity);
            }
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// ==========================================
// GLOBAL FUNCTIONS (for backward compatibility)
// ==========================================

// Export functions to global scope for use in inline event handlers
window.addToCart = addToCart;
window.updateCartQuantity = updateCartQuantity;
window.removeFromCart = removeFromCart;
window.updateCartCount = updateCartCount;
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.moveToCart = moveToCart;
window.clearWishlist = clearWishlist;
window.updateWishlistCount = updateWishlistCount;
window.showToast = showToast;