    <!-- Footer -->
    <footer class="bg-gradient-primary text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-store me-2"></i><?php echo SITENAME; ?>
                    </h5>
                    <p class="mb-3">Your trusted online marketplace for quality products at unbeatable prices. Shop with confidence and enjoy our exceptional customer service.</p>
                    <div class="d-flex">
                        <a href="#" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2 rounded-pill">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm rounded-pill">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>" class="text-white-50 text-decoration-none">
                                <i class="fas fa-home me-2"></i>Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/products" class="text-white-50 text-decoration-none">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/products/categories" class="text-white-50 text-decoration-none">
                                <i class="fas fa-tags me-2"></i>Categories
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/about" class="text-white-50 text-decoration-none">
                                <i class="fas fa-info-circle me-2"></i>About Us
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/contact" class="text-white-50 text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/faq" class="text-white-50 text-decoration-none">
                                <i class="fas fa-question-circle me-2"></i>FAQ
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/shipping" class="text-white-50 text-decoration-none">
                                <i class="fas fa-shipping-fast me-2"></i>Shipping Info
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo URLROOT; ?>/returns" class="text-white-50 text-decoration-none">
                                <i class="fas fa-undo me-2"></i>Returns
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">My Account</h6>
                    <ul class="list-unstyled">
                        <?php if (isLoggedIn()): ?>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/users/profile" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/orders" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-shopping-bag me-2"></i>Orders
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/cart" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-shopping-cart me-2"></i>Cart
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/wishlist" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-heart me-2"></i>Wishlist
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/auth/login" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo URLROOT; ?>/auth/register" class="text-white-50 text-decoration-none">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Newsletter</h6>
                    <p class="small mb-3">Subscribe to get updates on new products and offers!</p>
                    <form class="newsletter-form">
                        <div class="input-group mb-2">
                            <input type="email" class="form-control border-0" placeholder="Your email" required>
                            <button class="btn btn-secondary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <small class="text-white-50">
                            <i class="fas fa-shield-alt me-2"></i>We respect your privacy
                        </small>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-white-50">
                        <i class="fas fa-copyright me-1"></i>
                        <?php echo date('Y'); ?> <?php echo SITENAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end justify-content-center align-items-center mt-3 mt-md-0">
                        <small class="text-white-50 me-3">Secure payments with:</small>
                        <img src="<?php echo URLROOT; ?>/assets/images/payment-methods.png" alt="Payment Methods" class="payment-methods" style="height: 24px; opacity: 0.8;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll to Top Button -->
        <button class="btn btn-secondary rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg" 
                id="scrollToTop" 
                style="display: none; z-index: 1000; width: 50px; height: 50px;">
            <i class="fas fa-arrow-up"></i>
        </button>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo URLROOT; ?>/assets/js/script.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Scroll to top functionality
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.display = 'block';
            } else {
                scrollToTopBtn.style.display = 'none';
            }
        });
        
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Newsletter subscription
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simple validation
            if (email) {
                // Show success message (you can implement actual subscription logic)
                const btn = this.querySelector('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                    this.reset();
                }, 2000);
            }
        });
        
        // Update cart count on page load
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
    </script>
</body>
</html>