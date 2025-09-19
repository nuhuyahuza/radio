<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-radio fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Zaa Radio</h3>
                            <p class="text-muted">Sign in to your account</p>
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
                        
                        <form id="loginForm" method="POST" action="/login">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="/forgot-password" class="text-decoration-none">Forgot your password?</a>
                        </div>
                        
                        <div class="text-center mt-4">
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
    <script>
        (function(){
            const form = document.getElementById('loginForm');
            const csrfInput = form.querySelector('input[name="csrf_token"]');
            function refreshToken(){
                fetch('/api/csrf-token', { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(d => { if (d && d.token) csrfInput.value = d.token; })
                    .catch(() => {});
            }
            refreshToken();
            form.addEventListener('submit', function(){
                // console hook to verify submit actually fires
                try { console.log('Submitting /login ...'); } catch(e) {}
            });
        })();
    </script>
</body>
</html>
