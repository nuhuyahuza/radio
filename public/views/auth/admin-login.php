<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login - Zaa Radio</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
	.login-container { min-height: 100vh; background: linear-gradient(135deg, #141e30 0%, #243b55 100%);} 
	.login-card { background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);} 
	</style>
</head>

<body>
	<div class="login-container d-flex align-items-center">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-4">
					<div class="login-card p-5">
						<div class="text-center mb-4">
							<i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
							<h3 class="fw-bold">Admin/Manager</h3>
							<p class="text-muted">Sign in to continue</p>
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

						<form id="adminLoginForm" method="POST" action="/admin/login">
							<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
							<div class="mb-3">
								<label for="email" class="form-label">Email</label>
								<input type="email" class="form-control" id="email" name="email" placeholder="admin@site.com" required>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label">Password</label>
								<input type="password" class="form-control" id="password" name="password" placeholder="Your password" required>
							</div>
							<div class="d-grid">
								<button type="submit" class="btn btn-primary btn-lg">
									<i class="fas fa-right-to-bracket me-2"></i> Sign In
								</button>
							</div>
						</form>

						<div class="text-center mt-3">
							<a href="/login" class="text-decoration-none">Advertiser login</a>
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
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('adminLoginForm');
		const submitBtn = form.querySelector('button[type="submit"]');
		const originalBtnText = submitBtn.innerHTML;
		form.addEventListener('submit', function(e) {
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
			submitBtn.disabled = true;
		});
	});
	</script>
</body>

</html>


