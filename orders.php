<?php
$page_title = "My Orders";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Get user's orders
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.order_item_id) as item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.order_id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.order_id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h1 class="fw-bold mb-4">
        <i class="fas fa-shopping-bag me-2"></i>My Orders
        <?php if (!empty($orders)): ?>
            <span class="badge bg-secondary"><?php echo count($orders); ?> orders</span>
        <?php endif; ?>
    </h1>

    <?php if (empty($orders)): ?>
        <!-- No Orders -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No orders yet</h3>
            <p class="text-muted mb-4">Start shopping to see your orders here!</p>
            <a href="products.php" class="btn btn-dark btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">
                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-<?php 
                                        echo match($order['status']) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?> text-<?php echo $order['status'] === 'pending' ? 'dark' : 'white'; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Items:</strong> <?php echo $order['item_count']; ?> item(s)</p>
                                    <p class="mb-0 text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo substr(htmlspecialchars($order['shipping_address']), 0, 50) . '...'; ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <a href="order_details.php?id=<?php echo $order['order_id']; ?>" 
                                       class="btn btn-outline-dark btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] === 'completed'): ?>
                                        <button type="button" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-redo me-1"></i>Reorder
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Order Status Legend -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Order Status Guide</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-warning text-dark me-2">Pending</span>
                        <small>Order received, processing payment</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-info me-2">Processing</span>
                        <small>Payment confirmed, preparing shipment</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-success me-2">Completed</span>
                        <small>Order delivered successfully</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-danger me-2">Cancelled</span>
                        <small>Order cancelled or refunded</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

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