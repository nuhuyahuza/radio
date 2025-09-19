<?php
/**
 * User Form View
 * Form for creating and editing users
 */

use App\Utils\Session;

$isEdit = isset($user) && $user;
$pageTitle = $isEdit ? 'Edit User' : 'Create New User';
$currentPage = 'users';
ob_start();
$old = \App\Utils\Session::hasFlash('old') ? \App\Utils\Session::getFlash('old') : [];
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h1 class="h3 mb-0"><?= $pageTitle ?></h1>
				<a href="/admin/users" class="btn btn-outline-secondary">
					<i class="fas fa-arrow-left"></i> Back to Users
				</a>
			</div>

			<?php if (Session::hasFlash('success')): ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?= Session::getFlash('success') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
			<?php endif; ?>

			<?php if (Session::hasFlash('error')): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?= Session::getFlash('error') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
			<?php endif; ?>

			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">User Information</h5>
						</div>
						<div class="card-body">
							<form method="POST" action="/admin/users/create">
								<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name" class="form-label">Full Name *</label>
											<input type="text" class="form-control" id="name" name="name"
												value="<?= htmlspecialchars($old['name'] ?? ($isEdit ? $user['name'] : '')) ?>"
												required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="email" class="form-label">Email Address *</label>
											<input type="email" class="form-control" id="email" name="email"
												value="<?= htmlspecialchars($old['email'] ?? ($isEdit ? $user['email'] : '')) ?>"
												required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="password" class="form-label">
												Password <?= $isEdit ? '(leave blank to keep current)' : '*' ?>
											</label>
											<input type="password" class="form-control" id="password" name="password"
												<?= $isEdit ? '' : 'required' ?>>
											<?php if ($isEdit): ?>
											<div class="form-text">Leave blank to keep the current password.</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="role" class="form-label">Role *</label>
											<select class="form-select" id="role" name="role" required>
												<option value="">Select a role</option>
												<option value="admin"
													<?= ($isEdit && $user['role'] === 'admin') || (isset($old['role']) && $old['role'] === 'admin') ? 'selected' : '' ?>>
													Admin
												</option>
												<option value="station_manager"
													<?= ($isEdit && $user['role'] === 'station_manager') || (isset($old['role']) && $old['role'] === 'station_manager') ? 'selected' : '' ?>>
													Station Manager</option>
												<option value="advertiser"
													<?= ($isEdit && $user['role'] === 'advertiser') || (isset($old['role']) && $old['role'] === 'advertiser') ? 'selected' : '' ?>>
													Advertiser</option>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="phone" class="form-label">Phone Number</label>
											<input type="tel" class="form-control" id="phone" name="phone"
												value="<?= htmlspecialchars($old['phone'] ?? ($isEdit ? ($user['phone'] ?? '') : '')) ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="company" class="form-label">Company</label>
											<input type="text" class="form-control" id="company" name="company"
												value="<?= htmlspecialchars($old['company'] ?? ($isEdit ? ($user['company'] ?? '') : '')) ?>">
										</div>
									</div>
								</div>

								<div class="mb-3">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="is_active" name="is_active"
											<?= (isset($old['is_active']) && $old['is_active']) || (!$isEdit && !isset($old['is_active'])) || ($isEdit && $user['is_active']) ? 'checked' : '' ?>>
										<label class="form-check-label" for="is_active">
											Active User
										</label>
									</div>
									<div class="form-text">Inactive users cannot log in to the system.</div>
								</div>

								<div class="d-flex justify-content-between">
									<a href="/admin/users" class="btn btn-secondary">Cancel</a>
									<button type="submit" class="btn btn-primary">
										<i class="fas fa-save"></i> <?= $isEdit ? 'Update User' : 'Create User' ?>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">Role Information</h5>
						</div>
						<div class="card-body">
							<div class="mb-3">
								<h6>Admin</h6>
								<p class="text-muted small">Full system access including user management, system
									settings, and all features.</p>
							</div>
							<div class="mb-3">
								<h6>Station Manager</h6>
								<p class="text-muted small">Can manage radio slots, approve/reject bookings, and view
									reports.</p>
							</div>
						</div>
					</div>

					<?php if ($isEdit): ?>
					<div class="card mt-3">
						<div class="card-header">
							<h5 class="card-title mb-0">Account Information</h5>
						</div>
						<div class="card-body">
							<p class="small text-muted mb-1">Created:
								<?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></p>
							<?php if ($user['updated_at'] !== $user['created_at']): ?>
							<p class="small text-muted mb-1">Last Updated:
								<?= date('M j, Y g:i A', strtotime($user['updated_at'])) ?></p>
							<?php endif; ?>
							<?php if ($user['last_login_at']): ?>
							<p class="small text-muted mb-1">Last Login:
								<?= date('M j, Y g:i A', strtotime($user['last_login_at'])) ?></p>
							<?php else: ?>
							<p class="small text-muted mb-1">Last Login: Never</p>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>