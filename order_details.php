<?php
$page_title = "Order Details";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="orders.php" class="text-decoration-none">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></li>
        </ol>
    </nav>

    <h1 class="fw-bold mb-4">
        Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
        <span class="badge bg-<?php 
            echo match($order['status']) {
                'pending' => 'warning',
                'processing' => 'info',
                'completed' => 'success',
                'cancelled' => 'danger',
                default => 'secondary'
            };
        ?> text-<?php echo $order['status'] === 'pending' ? 'dark' : 'white'; ?> ms-2">
            <?php echo ucfirst($order['status']); ?>
        </span>
    </h1>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Order Date:</strong><br>
                            <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong><br>
                            <?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Payment Method:</strong><br>
                            Cash on Delivery (COD)
                        </div>
                        <div class="col-md-6">
                            <strong>Phone Number:</strong><br>
                            <?php echo htmlspecialchars($order['phone_number']); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Shipping Address:</strong><br>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
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
                            <div class="col-md-5">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="text-muted small mb-0">
                                    <strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?> each
                                </p>
                            </div>
                            <div class="col-md-2 text-center">
                                <span><strong>Qty:</strong> <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                            <div class="col-md-1 text-end">
                                <a href="product.php?id=<?php echo $item['product_id']; ?>" 
                                   class="btn btn-outline-dark btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
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
                        <span>Total:</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Order Actions</h6>
                </div>
                <div class="card-body">
                    <?php if ($order['status'] === 'pending'): ?>
                        <button type="button" class="btn btn-danger w-100 mb-2" 
                                onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                            <i class="fas fa-times me-2"></i>Cancel Order
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] === 'completed'): ?>
                        <button type="button" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-redo me-2"></i>Reorder Items
                        </button>
                    <?php endif; ?>
                    
                    <a href="orders.php" class="btn btn-outline-dark w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Placed</h6>
                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></small>
                            </div>
                        </div>
                        
                        <?php if ($order['status'] !== 'pending'): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?php echo $order['status'] === 'cancelled' ? 'danger' : 'info'; ?>"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1"><?php echo $order['status'] === 'cancelled' ? 'Order Cancelled' : 'Processing'; ?></h6>
                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'completed'): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Delivered</h6>
                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    margin-left: 10px;
}
</style>

<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({order_id: orderId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel order: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error cancelling order. Please try again.');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>