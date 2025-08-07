<?php require APPROOT . '/Views/inc/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to <?php echo SITENAME; ?></h1>
                <p class="lead mb-4">Discover amazing products with our premium collection. Quality, style, and value all in one place.</p>
                <div class="d-flex gap-3">
                    <a href="<?php echo URLROOT; ?>/products" class="btn btn-light btn-lg">Shop Now</a>
                    <a href="<?php echo URLROOT; ?>/products/categories" class="btn btn-outline-light btn-lg">Browse Categories</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-shopping-bag display-1 text-white opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Shop by Category</h2>
        <div class="row g-4">
            <?php foreach (array_slice($categories, 0, 6) as $category): ?>
            <div class="col-md-4">
                <a href="<?php echo URLROOT; ?>/products?category=<?php echo $category->category_id; ?>" class="category-card d-block">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if (!empty($category->image_url)): ?>
                            <img src="<?php echo $category->image_url; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($category->name); ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="fas fa-tag fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($category->name); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($category->description); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($categories) > 6): ?>
        <div class="text-center mt-4">
            <a href="<?php echo URLROOT; ?>/products/categories" class="btn btn-outline-dark">View All Categories</a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Featured Products Section -->
<?php if (!empty($featured_products)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($product->image_url)): ?>
                        <img src="<?php echo $product->image_url; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name); ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo htmlspecialchars($product->name); ?></h6>
                        <p class="card-text text-muted small flex-grow-1"><?php echo substr(htmlspecialchars($product->description), 0, 100) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price"><?php echo formatPrice($product->price); ?></span>
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> <?php echo $product->view_count; ?>
                            </small>
                        </div>
                        <div class="mt-2">
                            <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->product_id; ?>" class="btn btn-outline-dark btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-dark">View All Products</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="mb-3">
                    <i class="fas fa-shipping-fast fa-3x text-dark"></i>
                </div>
                <h5>Free Shipping</h5>
                <p class="text-muted">Free shipping on orders over $50</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="mb-3">
                    <i class="fas fa-undo fa-3x text-dark"></i>
                </div>
                <h5>Easy Returns</h5>
                <p class="text-muted">30-day return policy</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="mb-3">
                    <i class="fas fa-headset fa-3x text-dark"></i>
                </div>
                <h5>24/7 Support</h5>
                <p class="text-muted">Round-the-clock customer service</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="mb-3">
                    <i class="fas fa-shield-alt fa-3x text-dark"></i>
                </div>
                <h5>Secure Payment</h5>
                <p class="text-muted">Your payment information is safe</p>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/Views/inc/footer.php'; ?>