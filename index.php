<?php
$page_title = "Home";
include 'includes/header.php';

// Get featured products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.view_count DESC LIMIT 8");
$featured_products = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
$categories = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to Nekomata Store</h1>
                <p class="lead mb-4">Discover amazing products with unbeatable prices. Shop the latest trends in electronics, fashion, books, and more.</p>
                <a href="products.php" class="btn btn-light btn-lg me-3">Shop Now</a>
                <a href="categories.php" class="btn btn-outline-light btn-lg">Browse Categories</a>
            </div>
            <div class="col-lg-6">
                <img src="https://via.placeholder.com/600x400?text=Hero+Image" alt="Hero Image" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Shop by Category</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-md-6 col-lg-3">
                <a href="products.php?category=<?php echo $category['category_id']; ?>" class="category-card d-block">
                    <img src="<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                    <div class="p-3 text-center">
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">Featured Products</h2>
        <div class="product-grid">
            <?php foreach ($featured_products as $product): ?>
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text text-muted small"><?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p class="card-text"><?php echo substr(htmlspecialchars($product['description']), 0, 100) . '...'; ?></p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                            <small class="text-muted"><?php echo $product['view_count']; ?> views</small>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-dark">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-dark btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                <h4 class="fw-bold">Fast Shipping</h4>
                <p class="text-muted">Free shipping on orders over $50. Get your products delivered quickly and safely.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h4 class="fw-bold">Secure Payment</h4>
                <p class="text-muted">Your payment information is protected with the latest security technology.</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-headset fa-3x mb-3"></i>
                <h4 class="fw-bold">24/7 Support</h4>
                <p class="text-muted">Our customer support team is available around the clock to help you.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>