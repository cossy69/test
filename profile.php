<?php
$page_title = "Profile";
include 'includes/header.php';

// Redirect if not logged in
if (!$is_logged_in) {
    header('Location: login.php');
    exit;
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($full_name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email is taken by another user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            $error = 'This email is already in use by another account.';
        } else {
            // Validate password change if provided
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Please enter your current password to change it.';
                } elseif (!password_verify($current_password, $user['password'])) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                }
            }
            
            if (empty($error)) {
                try {
                    if (!empty($new_password)) {
                        // Update with password change
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ?, password = ? WHERE user_id = ?");
                        $stmt->execute([$full_name, $email, $phone_number, $address, $hashed_password, $_SESSION['user_id']]);
                    } else {
                        // Update without password change
                        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?");
                        $stmt->execute([$full_name, $email, $phone_number, $address, $_SESSION['user_id']]);
                    }
                    
                    $success = 'Profile updated successfully!';
                    
                    // Refresh user data
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    
                } catch (PDOException $e) {
                    $error = 'Failed to update profile. Please try again.';
                }
            }
        }
    }
}

// Get user statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_spent FROM orders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_reviews FROM comments WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$review_stats = $stmt->fetch();
?>

<div class="container mt-4">
    <h1 class="fw-bold mb-4">
        <i class="fas fa-user-edit me-2"></i>My Profile
    </h1>

    <div class="row">
        <!-- Profile Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                   value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <h6 class="fw-bold mb-3">Change Password (Optional)</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Profile Statistics -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Account Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="fw-bold text-primary"><?php echo $stats['total_orders']; ?></h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="fw-bold text-success">$<?php echo number_format($stats['total_spent'], 2); ?></h4>
                            <small class="text-muted">Total Spent</small>
                        </div>
                        <div class="col-6">
                            <h4 class="fw-bold text-info"><?php echo $review_stats['total_reviews']; ?></h4>
                            <small class="text-muted">Reviews Written</small>
                        </div>
                        <div class="col-6">
                            <h4 class="fw-bold text-warning"><?php echo ucfirst($user['role']); ?></h4>
                            <small class="text-muted">Account Type</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Account Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Member Since:</strong><br>
                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                    
                    <p><strong>Last Updated:</strong><br>
                    <?php echo date('M j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="orders.php" class="btn btn-outline-dark">
                            <i class="fas fa-shopping-bag me-2"></i>View Orders
                        </a>
                        <a href="cart.php" class="btn btn-outline-dark">
                            <i class="fas fa-shopping-cart me-2"></i>View Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>