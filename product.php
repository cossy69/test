<?php
$page_title = "Product Details";
include 'includes/header.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit;
}

// Update view count
$stmt = $pdo->prepare("UPDATE products SET view_count = view_count + 1 WHERE product_id = ?");
$stmt->execute([$product_id]);

// Get product details
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Get product comments with user details
$stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.product_id = ? ORDER BY c.created_at DESC");
$stmt->execute([$product_id]);
$comments = $stmt->fetchAll();

// Calculate average rating
$avg_rating = 0;
$total_ratings = 0;
if (!empty($comments)) {
    $ratings = array_filter(array_column($comments, 'rating'));
    if (!empty($ratings)) {
        $avg_rating = array_sum($ratings) / count($ratings);
        $total_ratings = count($ratings);
    }
}

// Handle comment submission
$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    if (!$is_logged_in) {
        $comment_error = 'Please login to leave a comment.';
    } else {
        $comment_text = trim($_POST['comment_text']);
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        
        if (empty($comment_text)) {
            $comment_error = 'Please enter a comment.';
        } elseif ($rating < 1 || $rating > 5) {
            $comment_error = 'Please select a valid rating.';
        } else {
            // Check if user already commented on this product
            $stmt = $pdo->prepare("SELECT comment_id FROM comments WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$product_id, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $comment_error = 'You have already commented on this product.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO comments (product_id, user_id, comment_text, rating) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$product_id, $_SESSION['user_id'], $comment_text, $rating]);
                    $comment_success = 'Comment added successfully!';
                    
                    // Refresh comments
                    $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.product_id = ? ORDER BY c.created_at DESC");
                    $stmt->execute([$product_id]);
                    $comments = $stmt->fetchAll();
                    
                    // Recalculate average rating
                    if (!empty($comments)) {
                        $ratings = array_filter(array_column($comments, 'rating'));
                        if (!empty($ratings)) {
                            $avg_rating = array_sum($ratings) / count($ratings);
                            $total_ratings = count($ratings);
                        }
                    }
                } catch (PDOException $e) {
                    $comment_error = 'Failed to add comment. Please try again.';
                }
            }
        }
    }
}

// Get related products
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND product_id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$product['category_id'], $product_id]);
$related_products = $stmt->fetchAll();

$page_title = $product['name'];
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none">Products</a></li>
            <li class="breadcrumb-item"><a href="products.php?category=<?php echo $product['category_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     style="height: 400px; object-fit: cover;">
            </div>
        </div>
        
        <div class="col-md-6">
            <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <small class="text-muted">
                    <i class="fas fa-eye me-1"></i><?php echo $product['view_count']; ?> views
                </small>
            </div>

            <?php if ($total_ratings > 0): ?>
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <div class="rating me-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i <= round($avg_rating) ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span><?php echo number_format($avg_rating, 1); ?> (<?php echo $total_ratings; ?> reviews)</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <span class="price fs-2 fw-bold">$<?php echo number_format($product['price'], 2); ?></span>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold">Description</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="mb-4">
                <div class="row">
                    <div class="col-6">
                        <strong>Stock:</strong> 
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <span class="text-success"><?php echo $product['stock_quantity']; ?> available</span>
                        <?php else: ?>
                            <span class="text-danger">Out of stock</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <strong>SKU:</strong> #<?php echo str_pad($product['product_id'], 6, '0', STR_PAD_LEFT); ?>
                    </div>
                </div>
            </div>

            <?php if ($is_logged_in && $product['stock_quantity'] > 0): ?>
                <form method="POST" action="add_to_cart.php" class="mb-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity</label>
                            <select class="form-select" name="quantity" id="quantity">
                                <?php for ($i = 1; $i <= min(10, $product['stock_quantity']); $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-lg w-100">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            <?php elseif (!$is_logged_in): ?>
                <div class="alert alert-info">
                    <a href="login.php" class="text-decoration-none">Login</a> to add this product to your cart.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="fw-bold mb-4">Customer Reviews</h3>
            
            <?php if ($is_logged_in): ?>
                <!-- Comment Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Leave a Review</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($comment_error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($comment_error); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($comment_success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($comment_success); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" name="rating" id="rating" required>
                                    <option value="">Select a rating</option>
                                    <option value="5">5 stars - Excellent</option>
                                    <option value="4">4 stars - Very Good</option>
                                    <option value="3">3 stars - Good</option>
                                    <option value="2">2 stars - Fair</option>
                                    <option value="1">1 star - Poor</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment_text" class="form-label">Review</label>
                                <textarea class="form-control" name="comment_text" id="comment_text" rows="4" 
                                         placeholder="Share your thoughts about this product..." required></textarea>
                            </div>
                            
                            <button type="submit" name="submit_comment" class="btn btn-dark">Submit Review</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments List -->
            <?php if (empty($comments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No reviews yet</h5>
                    <p class="text-muted">Be the first to review this product!</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($comment['username']); ?></h6>
                                    <?php if ($comment['rating']): ?>
                                        <div class="rating mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $comment['rating'] ? '' : '-o'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="mt-5">
            <h3 class="fw-bold mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">$<?php echo number_format($related['price'], 2); ?></span>
                                    </div>
                                    <a href="product.php?id=<?php echo $related['product_id']; ?>" 
                                       class="btn btn-outline-dark btn-sm w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>