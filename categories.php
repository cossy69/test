<?php
$page_title = "Categories";
include 'includes/header.php';

// Get all categories with product counts
$stmt = $pdo->query("
    SELECT c.*, COUNT(p.product_id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.category_id = p.category_id 
    GROUP BY c.category_id 
    ORDER BY c.name
");
$categories = $stmt->fetchAll();
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Product Categories</h1>
        <p class="lead text-muted">Browse our wide selection of product categories</p>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
            <div class="col-md-6 col-lg-4">
                <a href="products.php?category=<?php echo $category['category_id']; ?>" class="category-card d-block">
                    <img src="<?php echo htmlspecialchars($category['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($category['name']); ?>">
                    <div class="p-4 text-center">
                        <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($category['name']); ?></h4>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($category['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-dark"><?php echo $category['product_count']; ?> products</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5 py-5 bg-white rounded">
        <h3 class="fw-bold mb-3">Can't find what you're looking for?</h3>
        <p class="text-muted mb-4">Use our search feature to find specific products</p>
        <a href="products.php" class="btn btn-dark btn-lg">Browse All Products</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>