<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .register-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="register-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-radio fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Create Account</h3>
                            <p class="text-muted">Join Zaa Radio as an advertiser</p>
                        </div>
                        
                        <?php if (Session::hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars(Session::getFlash('error')) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (Session::hasFlash('success')): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars(Session::getFlash('success')) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="/register">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                            </div>
                            
                            <div class="mb-3">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" class="form-control" id="company" name="company" placeholder="Enter your company name">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Create Account
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Already have an account? <a href="/login" class="text-decoration-none">Sign in here</a></p>
                            <a href="/" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
