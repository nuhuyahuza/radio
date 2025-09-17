<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .forgot-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .forgot-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="forgot-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="forgot-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-key fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Forgot Password</h3>
                            <p class="text-muted">Enter your email to reset your password</p>
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
                        
                        <form method="POST" action="/forgot-password">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Reset Link
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="/login" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Login
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
