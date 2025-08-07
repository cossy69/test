<?php require APPROOT . '/Views/inc/header.php'; ?>

<div class="container-fluid py-4 admin-dashboard">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3">
            <div class="admin-sidebar">
                <div class="text-center mb-4">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h6 class="text-white mt-2"><?php echo $_SESSION['username']; ?></h6>
                    <small class="text-white-50">Administrator</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="<?php echo URLROOT; ?>/admin">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/products">
                        <i class="fas fa-box me-2"></i>Products
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/orders">
                        <i class="fas fa-shopping-bag me-2"></i>Orders
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/users">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/categories">
                        <i class="fas fa-tags me-2"></i>Categories
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/reviews">
                        <i class="fas fa-star me-2"></i>Reviews
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/analytics">
                        <i class="fas fa-chart-line me-2"></i>Analytics
                    </a>
                    <a class="nav-link" href="<?php echo URLROOT; ?>/admin/settings">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    
                    <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                    
                    <a class="nav-link" href="<?php echo URLROOT; ?>">
                        <i class="fas fa-arrow-left me-2"></i>Back to Store
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-10 col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold text-gradient-primary mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    </h1>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening with your store today.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel Report</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card bg-gradient-primary text-white" data-aos="fade-up" data-aos-delay="100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-1"><?php echo number_format($data['stats']['total_products']); ?></h3>
                                <p class="mb-0">Total Products</p>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +<?php echo $data['stats']['new_products_today'] ?? 0; ?> today
                                </small>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card bg-gradient-success text-white" data-aos="fade-up" data-aos-delay="200">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-1"><?php echo number_format($data['stats']['total_orders']); ?></h3>
                                <p class="mb-0">Total Orders</p>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +<?php echo $data['stats']['orders_today'] ?? 0; ?> today
                                </small>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-shopping-bag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card bg-gradient-secondary text-white" data-aos="fade-up" data-aos-delay="300">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-1"><?php echo number_format($data['stats']['total_users']); ?></h3>
                                <p class="mb-0">Total Users</p>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +<?php echo $data['stats']['new_users_today'] ?? 0; ?> today
                                </small>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card text-white" style="background: linear-gradient(135deg, #ec4899, #f59e0b);" data-aos="fade-up" data-aos-delay="400">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-1">$<?php echo number_format($data['stats']['total_revenue'], 2); ?></h3>
                                <p class="mb-0">Total Revenue</p>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +12.5% this month
                                </small>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Sales Chart -->
                <div class="col-lg-8">
                    <div class="card shadow-custom" data-aos="fade-up" data-aos-delay="500">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2 text-primary"></i>Sales Overview
                            </h5>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    Last 6 Months
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                                    <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                                    <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                    <li><a class="dropdown-item" href="#">Last Year</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Category Distribution -->
                <div class="col-lg-4">
                    <div class="card shadow-custom" data-aos="fade-up" data-aos-delay="600">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2 text-primary"></i>Categories
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoriesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Row -->
            <div class="row g-4 mb-4">
                <!-- Recent Orders -->
                <div class="col-lg-8">
                    <div class="card shadow-custom" data-aos="fade-up" data-aos-delay="700">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-bag me-2 text-primary"></i>Recent Orders
                            </h5>
                            <a href="<?php echo URLROOT; ?>/admin/orders" class="btn btn-outline-primary btn-sm">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['recent_orders'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['recent_orders'] as $order): ?>
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-primary">
                                                            #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar-sm me-2">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                            <?php echo htmlspecialchars($order['username']); ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo match($order['status']) {
                                                                'pending' => 'warning',
                                                                'processing' => 'info',
                                                                'completed' => 'success',
                                                                'cancelled' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        ?> rounded-pill">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td class="text-muted">
                                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo URLROOT; ?>/admin/orders/view/<?php echo $order['order_id']; ?>" 
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-shopping-bag fa-3x mb-3 opacity-25"></i>
                                    <h6>No recent orders</h6>
                                    <p class="small">Orders will appear here once customers start purchasing.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats & Alerts -->
                <div class="col-lg-4">
                    <!-- Low Stock Alert -->
                    <div class="card shadow-custom mb-4" data-aos="fade-up" data-aos-delay="800">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['low_stock_products'])): ?>
                                <?php foreach ($data['low_stock_products'] as $product): ?>
                                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-muted">
                                                Stock: <?php echo $product['stock_quantity']; ?> units
                                            </small>
                                        </div>
                                        <span class="badge bg-danger rounded-pill">
                                            <?php echo $product['stock_quantity']; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                <div class="card-footer">
                                    <a href="<?php echo URLROOT; ?>/admin/products?filter=low_stock" class="btn btn-warning text-dark btn-sm">
                                        Manage Stock
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="mb-0">All products are well stocked!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pending Orders -->
                    <div class="card shadow-custom" data-aos="fade-up" data-aos-delay="900">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Pending Orders
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="text-info fw-bold mb-2"><?php echo $data['stats']['pending_orders']; ?></h2>
                            <p class="text-muted mb-3">Orders awaiting processing</p>
                            <a href="<?php echo URLROOT; ?>/admin/orders?status=pending" class="btn btn-info btn-sm">
                                Process Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-custom" data-aos="fade-up" data-aos-delay="1000">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="<?php echo URLROOT; ?>/admin/products/add" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center py-3">
                                        <i class="fas fa-plus me-2"></i>Add Product
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?php echo URLROOT; ?>/admin/categories/add" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center py-3">
                                        <i class="fas fa-tags me-2"></i>Add Category
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?php echo URLROOT; ?>/admin/orders?status=pending" class="btn btn-outline-warning w-100 d-flex align-items-center justify-content-center py-3">
                                        <i class="fas fa-clock me-2"></i>Pending Orders
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?php echo URLROOT; ?>/admin/analytics" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center py-3">
                                        <i class="fas fa-chart-line me-2"></i>View Analytics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.5rem;
}

.user-avatar-sm {
    width: 32px;
    height: 32px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.stats-icon {
    opacity: 0.3;
    font-size: 2rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>

<script>
// Initialize dashboard charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardCharts();
});

function initializeDashboardCharts() {
    // Sales Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [1200, 1900, 1500, 2100, 1800, 2400],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    }

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Clothing', 'Books', 'Home & Garden'],
                datasets: [{
                    data: [35, 25, 20, 20],
                    backgroundColor: [
                        '#6366f1',
                        '#f59e0b',
                        '#10b981',
                        '#ec4899'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }
}

function refreshDashboard() {
    // Add loading state
    const refreshBtn = event.target;
    const originalHTML = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate refresh (replace with actual AJAX call)
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>

<?php require APPROOT . '/Views/inc/footer.php'; ?>