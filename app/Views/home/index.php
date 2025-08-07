<?php require APPROOT . '/Views/inc/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="hero-content">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="hero-text">
                        <h1 class="display-3 fw-bold mb-4">
                            Welcome to <span class="text-gradient-primary"><?php echo SITENAME; ?></span>
                        </h1>
                        <p class="lead mb-4 text-white-75">
                            Discover amazing products with our premium collection. Quality, style, and value all in one place. 
                            Experience shopping like never before with our curated selection.
                        </p>
                        <div class="hero-stats mb-4">
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h4 class="fw-bold mb-0">10K+</h4>
                                        <small class="text-white-50">Products</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h4 class="fw-bold mb-0">50K+</h4>
                                        <small class="text-white-50">Customers</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h4 class="fw-bold mb-0">99%</h4>
                                        <small class="text-white-50">Satisfaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="<?php echo URLROOT; ?>/products" class="btn btn-secondary btn-lg px-4">
                                <i class="fas fa-shopping-bag me-2"></i>Shop Now
                            </a>
                            <a href="<?php echo URLROOT; ?>/products/categories" class="btn btn-outline-light btn-lg px-4">
                                <i class="fas fa-tags me-2"></i>Browse Categories
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-image">
                        <div class="floating-card card-1">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="floating-card card-2">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="floating-card card-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="main-hero-icon">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <div class="scroll-arrow">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</section>

<!-- Quick Search Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-lg-8">
                <div class="search-hero text-center">
                    <h3 class="fw-bold mb-3">What are you looking for?</h3>
                    <div class="search-wrapper">
                        <form method="GET" action="<?php echo URLROOT; ?>/products/search" class="search-form-hero">
                            <div class="input-group input-group-lg">
                                <input type="text" name="q" class="form-control" placeholder="Search for products..." required>
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <div class="popular-searches mt-3">
                            <small class="text-muted">Popular searches: </small>
                            <a href="<?php echo URLROOT; ?>/products?q=electronics" class="badge bg-primary text-decoration-none me-2">Electronics</a>
                            <a href="<?php echo URLROOT; ?>/products?q=clothing" class="badge bg-success text-decoration-none me-2">Clothing</a>
                            <a href="<?php echo URLROOT; ?>/products?q=books" class="badge bg-info text-decoration-none me-2">Books</a>
                            <a href="<?php echo URLROOT; ?>/products?q=home" class="badge bg-warning text-decoration-none">Home & Garden</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="display-6 fw-bold text-gradient-primary">Shop by Category</h2>
            <p class="lead text-muted">Explore our wide range of product categories</p>
        </div>
        
        <div class="row g-4">
            <?php foreach (array_slice($categories, 0, 6) as $index => $category): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                <a href="<?php echo URLROOT; ?>/products?category=<?php echo $category->category_id; ?>" class="category-card-modern">
                    <div class="card h-100 border-0 shadow-custom category-hover-effect">
                        <div class="category-image-wrapper">
                            <?php if (!empty($category->image_url)): ?>
                                <img src="<?php echo $category->image_url; ?>" class="card-img-top category-image" alt="<?php echo htmlspecialchars($category->name); ?>">
                            <?php else: ?>
                                <div class="card-img-top category-placeholder d-flex align-items-center justify-content-center">
                                    <div class="category-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="category-overlay">
                                <div class="category-action">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($category->name); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($category->description); ?></p>
                            <div class="category-stats">
                                <small class="text-primary">
                                    <i class="fas fa-box me-1"></i>
                                    <?php echo $category->product_count ?? '0'; ?> products
                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($categories) > 6): ?>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo URLROOT; ?>/products/categories" class="btn btn-outline-primary btn-lg px-4">
                <i class="fas fa-th-large me-2"></i>View All Categories
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products Section -->
<?php if (!empty($featured_products)): ?>
<section class="py-5 bg-gradient-light">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="display-6 fw-bold text-gradient-secondary">Featured Products</h2>
            <p class="lead text-muted">Handpicked items just for you</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $index => $product): ?>
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                <div class="product-card card h-100 border-0 shadow-custom">
                    <div class="product-image-wrapper position-relative">
                        <?php if (!empty($product->image_url)): ?>
                            <img src="<?php echo $product->image_url; ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product->name); ?>">
                        <?php else: ?>
                            <div class="card-img-top product-placeholder d-flex align-items-center justify-content-center">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-overlay">
                            <div class="product-actions">
                                <button class="btn btn-light btn-sm rounded-pill me-2" data-bs-toggle="tooltip" title="Add to Wishlist">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="btn btn-light btn-sm rounded-pill" data-bs-toggle="tooltip" title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if (isset($product->discount) && $product->discount > 0): ?>
                        <div class="product-badge">
                            <span class="badge bg-danger">-<?php echo $product->discount; ?>%</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="product-rating mb-2">
                            <?php 
                            $rating = $product->rating ?? 4.5;
                            for ($i = 1; $i <= 5; $i++): 
                            ?>
                                <i class="fas fa-star <?php echo $i <= $rating ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <small class="text-muted ms-2">(<?php echo $product->review_count ?? rand(10, 100); ?>)</small>
                        </div>
                        
                        <h6 class="card-title fw-bold mb-2">
                            <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->product_id; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($product->name); ?>
                            </a>
                        </h6>
                        
                        <p class="card-text text-muted small mb-3">
                            <?php echo substr(htmlspecialchars($product->description), 0, 80) . '...'; ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price-group">
                                <span class="product-price fw-bold text-primary"><?php echo formatPrice($product->price); ?></span>
                                <?php if (isset($product->original_price) && $product->original_price > $product->price): ?>
                                    <small class="text-muted text-decoration-line-through ms-2">
                                        <?php echo formatPrice($product->original_price); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <small class="text-success">
                                <i class="fas fa-check-circle me-1"></i>In Stock
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product->product_id; ?>">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->product_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-secondary btn-lg px-4">
                <i class="fas fa-shopping-bag me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-header text-center mb-5" data-aos="fade-up">
            <h2 class="display-6 fw-bold">Why Choose Us?</h2>
            <p class="lead text-muted">We provide the best shopping experience</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon bg-gradient-primary text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Free Shipping</h5>
                    <p class="text-muted">Free shipping on orders over $50. Fast and reliable delivery nationwide.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon bg-gradient-success text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Easy Returns</h5>
                    <p class="text-muted">30-day return policy. No questions asked, hassle-free returns.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon bg-gradient-secondary text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="fw-bold mb-3">24/7 Support</h5>
                    <p class="text-muted">Round-the-clock customer service. We're here to help anytime.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon text-white rounded-circle mx-auto mb-3" style="background: linear-gradient(135deg, #ec4899, #f59e0b);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Secure Payment</h5>
                    <p class="text-muted">Your payment information is safe with SSL encryption and secure processing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h3 class="fw-bold mb-3">Stay Updated</h3>
                <p class="mb-4">Subscribe to our newsletter and get exclusive offers, new product updates, and special discounts delivered to your inbox.</p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <form class="newsletter-signup">
                    <div class="input-group input-group-lg">
                        <input type="email" class="form-control" placeholder="Enter your email address" required>
                        <button class="btn btn-secondary px-4" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </div>
                    <small class="text-white-50 mt-2 d-block">
                        <i class="fas fa-lock me-1"></i>We respect your privacy. No spam, ever.
                    </small>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* Hero Section Enhancements */
.hero-section {
    min-height: 100vh;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
}

.min-vh-75 {
    min-height: 75vh;
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.9);
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.7);
}

.hero-image {
    position: relative;
    height: 400px;
}

.main-hero-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
    backdrop-filter: blur(10px);
}

.floating-card {
    position: absolute;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
    animation: float 3s ease-in-out infinite;
}

.card-1 {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.card-2 {
    top: 60%;
    right: 20%;
    animation-delay: 1s;
}

.card-3 {
    bottom: 20%;
    left: 20%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255, 255, 255, 0.7);
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-10px); }
    60% { transform: translateX(-50%) translateY(-5px); }
}

/* Category Cards */
.category-card-modern {
    text-decoration: none;
    color: inherit;
}

.category-hover-effect {
    transition: all 0.3s ease;
}

.category-hover-effect:hover {
    transform: translateY(-10px);
}

.category-image-wrapper {
    position: relative;
    overflow: hidden;
}

.category-image {
    transition: transform 0.3s ease;
    height: 200px;
    object-fit: cover;
}

.category-placeholder {
    height: 200px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
}

.category-icon {
    font-size: 3rem;
    color: white;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card-modern:hover .category-overlay {
    opacity: 1;
}

.category-card-modern:hover .category-image {
    transform: scale(1.1);
}

.category-action {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1.2rem;
}

/* Product Cards */
.product-image-wrapper {
    overflow: hidden;
}

.product-image {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-placeholder {
    height: 250px;
    background: var(--gray-100);
}

.product-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
}

/* Feature Cards */
.feature-card {
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

/* Search Hero */
.search-form-hero .form-control {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 1rem 1.5rem;
}

.search-form-hero .btn {
    border: none;
    box-shadow: none;
}

/* Background Gradients */
.bg-gradient-light {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Section Headers */
.section-header h2 {
    position: relative;
    display: inline-block;
}

.section-header h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background: var(--gradient-primary);
    border-radius: 2px;
}
</style>

<?php require APPROOT . '/Views/inc/footer.php'; ?>