<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? SITENAME; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo URLROOT; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?php echo URLROOT; ?>">
                <i class="fas fa-store me-2 text-gradient"></i>
                <span class="brand-text"><?php echo SITENAME; ?></span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?php echo URLROOT; ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?php echo URLROOT; ?>/products">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?php echo URLROOT; ?>/products/categories">
                            <i class="fas fa-tags me-1"></i>Categories
                        </a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3 search-form" method="GET" action="<?php echo URLROOT; ?>/products/search">
                    <div class="input-group">
                        <input class="form-control border-0 shadow-sm" type="search" name="q" placeholder="Search products..." aria-label="Search">
                        <button class="btn btn-primary px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item me-2">
                            <a class="nav-link position-relative" href="<?php echo URLROOT; ?>/wishlist" data-bs-toggle="tooltip" title="My Wishlist">
                                <i class="fas fa-heart text-danger"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="wishlist-count" style="display: none;">
                                    0
                                </span>
                            </a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="nav-link position-relative cart-link" href="<?php echo URLROOT; ?>/cart">
                                <div class="cart-icon-wrapper">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge" id="cart-count" style="display: none;">
                                        0
                                    </span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="d-none d-md-inline"><?php echo $_SESSION['username']; ?></span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile">
                                    <i class="fas fa-user-edit me-2 text-primary"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/orders">
                                    <i class="fas fa-shopping-bag me-2 text-success"></i>My Orders
                                </a></li>
                                <?php if (isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/admin">
                                        <i class="fas fa-cogs me-2 text-warning"></i>Admin Panel
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-primary btn-sm" href="<?php echo URLROOT; ?>/auth/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="<?php echo URLROOT; ?>/auth/register">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php flash('register_success'); ?>
        <?php flash('cart_success'); ?>
        <?php flash('cart_error'); ?>
        <?php flash('order_success'); ?>
        <?php flash('checkout_error'); ?>
        <?php flash('comment_success'); ?>
        <?php flash('comment_error'); ?>
        <?php flash('contact_message'); ?>
    </div>