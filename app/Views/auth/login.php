<?php require APPROOT . '/Views/inc/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt fa-3x text-dark mb-3"></i>
                        <h3 class="card-title">Login</h3>
                        <p class="text-muted">Welcome back! Please sign in to your account.</p>
                    </div>

                    <form action="<?php echo URLROOT; ?>/auth/login" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" 
                                   class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo $username; ?>"
                                   required>
                            <div class="invalid-feedback">
                                <?php echo $username_err; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                <div class="invalid-feedback">
                                    <?php echo $password_err; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-2">
                            <a href="#" class="text-decoration-none">Forgot your password?</a>
                        </p>
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="<?php echo URLROOT; ?>/auth/register" class="text-decoration-none fw-bold">Sign up here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php require APPROOT . '/Views/inc/footer.php'; ?>