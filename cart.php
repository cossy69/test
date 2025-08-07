<?php
$page_title = "Shopping Cart";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $cart_item_id => $quantity) {
            $cart_item_id = (int)$cart_item_id;
            $quantity = (int)$quantity;
            
            if ($quantity <= 0) {
                // Remove item
                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
                $stmt->execute([$cart_item_id]);
            } else {
                // Update quantity
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
                $stmt->execute([$quantity, $cart_item_id]);
            }
        }
        $_SESSION['cart_success'] = 'Cart updated successfully!';
    } elseif (isset($_POST['remove_item'])) {
        $cart_item_id = (int)$_POST['cart_item_id'];
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
        $stmt->execute([$cart_item_id]);
        $_SESSION['cart_success'] = 'Item removed from cart!';
    }
    
    header('Location: cart.php');
    exit;
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.price, p.image_url, p.stock_quantity 
    FROM cart_items ci 
    JOIN carts c ON ci.cart_id = c.cart_id 
    JOIN products p ON ci.product_id = p.product_id 
    WHERE c.user_id = ? 
    ORDER BY ci.cart_item_id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
$total_items = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

$shipping = $subtotal >= 50 ? 0 : 9.99; // Free shipping over $50
$tax = $subtotal * 0.08; // 8% tax
$total = $subtotal + $shipping + $tax;
?>

<div class="container mt-4">
    <h1 class="fw-bold mb-4">
        <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
        <?php if ($total_items > 0): ?>
            <span class="badge bg-secondary"><?php echo $total_items; ?> items</span>
        <?php endif; ?>
    </h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['cart_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['cart_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['cart_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['cart_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['cart_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to your cart to get started!</p>
            <a href="products.php" class="btn btn-dark btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <form method="POST">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Cart Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="row g-0 border-bottom p-3 align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             style="height: 80px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <p class="text-muted small mb-0">$<?php echo number_format($item['price'], 2); ?> each</p>
                                        <p class="text-muted small mb-0">Stock: <?php echo $item['stock_quantity']; ?> available</p>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="quantities[<?php echo $item['cart_item_id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="0" max="<?php echo $item['stock_quantity']; ?>">
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="submit" name="remove_item" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Remove this item from cart?')">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="products.php" class="btn btn-outline-dark">
                                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                </a>
                                <button type="submit" name="update_cart" class="btn btn-dark">
                                    <i class="fas fa-sync me-2"></i>Update Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (<?php echo $total_items; ?> items):</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="<?php echo $shipping == 0 ? 'text-success' : ''; ?>">
                                <?php if ($shipping == 0): ?>
                                    FREE
                                <?php else: ?>
                                    $<?php echo number_format($shipping, 2); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>$<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <?php if ($shipping > 0): ?>
                            <div class="alert alert-info mt-3 small">
                                <i class="fas fa-info-circle me-1"></i>
                                Add $<?php echo number_format(50 - $subtotal, 2); ?> more for free shipping!
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="checkout.php" class="btn btn-dark w-100 btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>

                <!-- Recommended Products -->
                <?php
                // Get recommended products based on cart items
                $product_ids = array_column($cart_items, 'product_id');
                if (!empty($product_ids)) {
                    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
                    $stmt = $pdo->prepare("
                        SELECT DISTINCT p.* 
                        FROM products p 
                        JOIN products p2 ON p.category_id = p2.category_id 
                        WHERE p2.product_id IN ($placeholders) 
                        AND p.product_id NOT IN ($placeholders)
                        ORDER BY RAND() 
                        LIMIT 3
                    ");
                    $stmt->execute(array_merge($product_ids, $product_ids));
                    $recommended = $stmt->fetchAll();
                    
                    if (!empty($recommended)):
                ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">You might also like</h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recommended as $product): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;"
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <p class="mb-0 small text-muted">$<?php echo number_format($product['price'], 2); ?></p>
                                    </div>
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-outline-dark btn-sm">View</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                    endif;
                } 
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>