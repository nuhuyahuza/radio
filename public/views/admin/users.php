<?php
/**
 * Admin Users Management View
 * Lists all users with management options
 */

use App\Utils\Session;
?>

<?php
$pageTitle = 'User Management';
$currentPage = 'users';
ob_start();
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h1 class="h3 mb-0">User Management</h1>
				<a href="/admin/users/create" class="btn btn-primary">
					<i class="fas fa-plus"></i> Add New User
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

			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">All Users</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table id="usersTable" class="table table-striped table-hover">
							<thead class="table-light">
								<tr>
									<th></th>
									<th>ID</th>
									<th>Name</th>
									<th>Email</th>
									<th>Role</th>
									<th>Phone</th>
									<th>Company</th>
									<th>Status</th>
									<th>Created</th>
									<th>Last Login</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if (isset($users) && !empty($users)): ?>
								<?php foreach ($users as $user): ?>
								<tr>
									<td><?= $user['id'] ?></td>
									<td><?= htmlspecialchars($user['name']) ?></td>
									<td>
										<a href="mailto:<?= htmlspecialchars($user['email']) ?>">
											<?= htmlspecialchars($user['email']) ?>
										</a>
									</td>
									<td>
										<span
											class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'manager' ? 'warning' : 'info') ?>">
											<?= ucfirst($user['role']) ?>
										</span>
									</td>
									<td><?= htmlspecialchars($user['phone'] ?? '') ?></td>
									<td><?= htmlspecialchars($user['company'] ?? '') ?></td>
									<td>
										<span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
											<?= $user['is_active'] ? 'Active' : 'Inactive' ?>
										</span>
									</td>
									<td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
									<td>
										<?= $user['last_login_at'] ? date('M j, Y g:i A', strtotime($user['last_login_at'])) : 'Never' ?>
									</td>
									<td>
										<div class="btn-group" role="group">
											<a href="/admin/users/<?= $user['id'] ?>"
												class="btn btn-sm btn-outline-info" title="View Details">
												<i class="fas fa-eye"></i>
											</a>
											<a href="/admin/users/edit/<?= $user['id'] ?>"
												class="btn btn-sm btn-outline-primary" title="Edit">
												<i class="fas fa-edit"></i>
											</a>

											<form method="POST" action="/admin/users/toggle-status/<?= $user['id'] ?>"
												class="d-inline">
												<input type="hidden" name="csrf_token"
													value="<?= Session::getCsrfToken() ?>">
												<button type="submit"
													class="btn btn-sm btn-outline-<?= $user['is_active'] ? 'warning' : 'success' ?>"
													title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>"
													onclick="return confirm('Are you sure you want to <?= $user['is_active'] ? 'deactivate' : 'activate' ?> this user?')">
													<i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
												</button>
											</form>

											<?php if ($user['role'] !== 'admin' || $this->userModel->countUsersByRole('admin') > 1): ?>
											<form method="POST" action="/admin/users/delete/<?= $user['id'] ?>"
												class="d-inline">
												<input type="hidden" name="csrf_token"
													value="<?= Session::getCsrfToken() ?>">
												<button type="submit" class="btn btn-sm btn-outline-danger"
													title="Delete"
													onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
													<i class="fas fa-trash"></i>
												</button>
											</form>
											<?php endif; ?>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php else: ?>
								<tr>
									<td colspan="10" class="text-center text-muted py-4">
										<i class="fas fa-users fa-3x mb-3"></i>
										<p>No users found.</p>
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Initialize DataTables if available
	if (typeof $.fn.DataTable !== 'undefined') {
		$('#usersTable').DataTable({
			responsive: true,
			pageLength: 25,
			order: [
				[0, 'desc']
			],
			columnDefs: [{
					orderable: false,
					targets: -1
				} // Disable sorting on actions column
			]
		});
	}
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>