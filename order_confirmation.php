<?php
$page_title = "Order Confirmation";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['order']) ? (int)$_GET['order'] : 0;

if ($order_id <= 0) {
    header('Location: orders.php');
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = $subtotal >= 50 ? 0 : 9.99;
$tax = $subtotal * 0.08;
?>

<div class="container mt-4">
    <!-- Success Message -->
    <?php if (isset($_SESSION['order_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_SESSION['order_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['order_success']); ?>
    <?php endif; ?>

    <!-- Order Confirmation Header -->
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
        <h1 class="display-4 fw-bold text-success">Order Confirmed!</h1>
        <p class="lead">Thank you for your order. We'll send you a confirmation email shortly.</p>
    </div>

    <!-- Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-success rounded-circle p-2 me-2">1</span>
                    <span class="me-3">Cart</span>
                    <i class="fas fa-chevron-right text-muted me-3"></i>
                    <span class="badge bg-success rounded-circle p-2 me-2">2</span>
                    <span class="me-3">Checkout</span>
                    <i class="fas fa-chevron-right text-muted me-3"></i>
                    <span class="badge bg-success rounded-circle p-2 me-2">3</span>
                    <span class="fw-bold">Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Order Number:</strong> #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong> 
                            <span class="badge bg-warning text-dark"><?php echo ucfirst($order['status']); ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Payment Method:</strong> Cash on Delivery (COD)
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Shipping Address:</strong><br>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Phone Number:</strong> <?php echo htmlspecialchars($order['phone_number']); ?>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Items Ordered</h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($order_items as $item): ?>
                        <div class="row g-0 border-bottom p-3 align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="text-muted small mb-0">$<?php echo number_format($item['price'], 2); ?> each</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <span>Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
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
                        <span>Total Paid:</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">What's Next?</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                        <div>
                            <h6 class="mb-1">Email Confirmation</h6>
                            <small class="text-muted">You'll receive an order confirmation email shortly.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-box text-warning me-3 mt-1"></i>
                        <div>
                            <h6 class="mb-1">Processing</h6>
                            <small class="text-muted">We'll start preparing your order for shipment.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-truck text-success me-3 mt-1"></i>
                        <div>
                            <h6 class="mb-1">Delivery</h6>
                            <small class="text-muted">Your order will be delivered within 3-5 business days.</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="orders.php" class="btn btn-dark w-100">
                        <i class="fas fa-list me-2"></i>View All Orders
                    </a>
                    <a href="products.php" class="btn btn-outline-dark w-100 mt-2">
                        <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>