<?php
$page_title = "Checkout";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT ci.*, p.name, p.price, p.stock_quantity 
    FROM cart_items ci 
    JOIN carts c ON ci.cart_id = c.cart_id 
    JOIN products p ON ci.product_id = p.product_id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
$total_items = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

$shipping = $subtotal >= 50 ? 0 : 9.99;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $phone_number = trim($_POST['phone_number']);
    
    if (empty($shipping_address) || empty($phone_number)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Check stock availability again
            $stock_error = false;
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
                $stmt->execute([$item['product_id']]);
                $current_stock = $stmt->fetchColumn();
                
                if ($current_stock < $item['quantity']) {
                    $stock_error = true;
                    break;
                }
            }
            
            if ($stock_error) {
                $pdo->rollBack();
                $error = 'Some items in your cart are no longer available in the requested quantity.';
            } else {
                // Create order
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, phone_number) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $total, $shipping_address, $phone_number]);
                $order_id = $pdo->lastInsertId();
                
                // Create order items and update stock
                foreach ($cart_items as $item) {
                    // Add to order items
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                    
                    // Update stock
                    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
                
                // Clear cart
                $stmt = $pdo->prepare("DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.cart_id WHERE c.user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                
                $pdo->commit();
                
                $_SESSION['order_success'] = 'Order placed successfully! Order #' . $order_id;
                header('Location: order_confirmation.php?order=' . $order_id);
                exit;
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Failed to process order. Please try again.';
        }
    }
}
?>

<div class="container mt-4">
    <h1 class="fw-bold mb-4">
        <i class="fas fa-credit-card me-2"></i>Checkout
    </h1>

    <!-- Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-success rounded-circle p-2 me-2">1</span>
                    <span class="me-3">Cart</span>
                    <i class="fas fa-chevron-right text-muted me-3"></i>
                    <span class="badge bg-dark rounded-circle p-2 me-2">2</span>
                    <span class="me-3 fw-bold">Checkout</span>
                    <i class="fas fa-chevron-right text-muted me-3"></i>
                    <span class="badge bg-secondary rounded-circle p-2 me-2">3</span>
                    <span class="text-muted">Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <!-- Shipping Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" required
                                   value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required
                                      placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cash on Delivery (COD)</strong><br>
                            Pay when your order is delivered to your address.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-0 small"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></small>
                                    </div>
                                    <span class="small">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="<?php echo $shipping == 0 ? 'text-success' : ''; ?>">
                                <?php echo $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2); ?>
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
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark w-100 btn-lg">
                            <i class="fas fa-check me-2"></i>Place Order
                        </button>
                        <a href="cart.php" class="btn btn-outline-dark w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>