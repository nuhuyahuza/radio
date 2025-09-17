<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex align-items-center" style="min-height:100vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-unlock-alt fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Reset Password</h3>
                            <p class="text-muted">Enter a new password for your account</p>
                        </div>

                        <?php if (Session::hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars(Session::getFlash('error')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$isValid): ?>
                            <div class="alert alert-warning">
                                This reset link is invalid or has expired. Please request a new one.
                            </div>
                            <div class="text-center mt-3">
                                <a href="/forgot-password" class="btn btn-outline-primary">Request New Link</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Reset Password
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

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
