    <!-- Footer -->
    <footer class="bg-dark text-light mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3"><?php echo SITENAME; ?></h5>
                    <p class="text-muted">Your trusted e-commerce partner for quality products and exceptional service.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URLROOT; ?>" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="<?php echo URLROOT; ?>/products" class="text-muted text-decoration-none">Products</a></li>
                        <li><a href="<?php echo URLROOT; ?>/products/categories" class="text-muted text-decoration-none">Categories</a></li>
                        <li><a href="<?php echo URLROOT; ?>/home/about" class="text-muted text-decoration-none">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URLROOT; ?>/home/contact" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Connect With Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. All rights reserved. Version <?php echo APPVERSION; ?></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/script.js"></script>
    
    <!-- Update cart count on page load -->
    <?php if (isLoggedIn()): ?>
    <script>
        // Update cart count on page load
        fetch('<?php echo URLROOT; ?>/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge && data.count > 0) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = 'inline';
                }
            })
            .catch(error => console.error('Error loading cart count:', error));
    </script>
    <?php endif; ?>
</body>
</html>