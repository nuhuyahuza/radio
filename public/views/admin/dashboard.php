<?php
$pageTitle = 'Admin Dashboard';
$currentPage = 'dashboard';
ob_start();
?>

<div class="row mb-4">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center">
			<h2 class="mb-0">Welcome back, <?= htmlspecialchars($currentUser['name']) ?>!</h2>
			<div class="text-muted">
				<i class="fas fa-calendar-alt me-2"></i>
				<?= date('l, F j, Y') ?>
			</div>
		</div>
	</div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
				<i class="fas fa-clipboard-list"></i>
			</div>
			<div class="stats-number"><?= $stats['total_bookings'] ?? 0 ?></div>
			<div class="stats-label">Total Bookings</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
				<i class="fas fa-dollar-sign"></i>
			</div>
			<div class="stats-number">GH₵<?= number_format($stats['total_revenue'] ?? 0, 0) ?></div>
			<div class="stats-label">Total Revenue</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
				<i class="fas fa-users"></i>
			</div>
			<div class="stats-number"><?= $stats['active_advertisers'] ?? 0 ?></div>
			<div class="stats-label">Active Advertisers</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
				<i class="fas fa-clock"></i>
			</div>
			<div class="stats-number"><?= $stats['pending_bookings'] ?? 0 ?></div>
			<div class="stats-label">Pending Bookings</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Recent Bookings -->
	<div class="col-lg-8 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-clipboard-list me-2"></i>
					Recent Bookings
				</h5>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead>
							<tr>
								<th></th>
								<th>ID</th>
								<th>Advertiser</th>
								<th>Date & Time</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($recentBookings)): ?>
							<?php foreach ($recentBookings as $booking): ?>
							<tr>
								<td>#<?= $booking['id'] ?></td>
								<td>
									<div>
										<div class="fw-bold"><?= htmlspecialchars($booking['advertiser_name']) ?></div>
										<small
											class="text-muted"><?= htmlspecialchars($booking['advertiser_email']) ?></small>
									</div>
								</td>
								<td>
									<div>
										<div><?= date('M j, Y', strtotime($booking['date'])) ?></div>
										<small class="text-muted">
											<?= date('g:i A', strtotime($booking['start_time'])) ?> -
											<?= date('g:i A', strtotime($booking['end_time'])) ?>
										</small>
									</div>
								</td>
								<td class="fw-bold">GH₵<?= number_format($booking['total_amount'], 2) ?></td>
								<td>
									<?php
                                            $statusClass = '';
                                            switch ($booking['status']) {
                                                case 'pending':
                                                    $statusClass = 'badge-warning';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'badge-success';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'badge-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-info';
                                            }
                                            ?>
									<span class="badge <?= $statusClass ?>">
										<?= ucfirst($booking['status']) ?>
									</span>
								</td>
								<td>
									<a href="/admin/bookings/<?= $booking['id'] ?>"
										class="btn btn-sm btn-outline-primary">
										<i class="fas fa-eye"></i>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="6" class="text-center text-muted py-4">
									<i class="fas fa-inbox fa-2x mb-2"></i>
									<div>No recent bookings</div>
								</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer text-center">
				<a href="/admin/bookings" class="btn btn-primary">
					<i class="fas fa-list me-2"></i>
					View All Bookings
				</a>
			</div>
		</div>
	</div>

	<!-- Quick Actions & Stats -->
	<div class="col-lg-4">
		<!-- Quick Actions -->
		<div class="card mb-4">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-bolt me-2"></i>
					Quick Actions
				</h5>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="/admin/users/create" class="btn btn-outline-primary">
						<i class="fas fa-user-plus me-2"></i>
						Create Station Manager
					</a>
					<a href="/admin/slots" class="btn btn-outline-success">
						<i class="fas fa-calendar-plus me-2"></i>
						Manage Slots
					</a>
					<a href="/admin/reports" class="btn btn-outline-info">
						<i class="fas fa-chart-bar me-2"></i>
						Generate Reports
					</a>
				</div>
			</div>
		</div>

		<!-- System Status -->
		<!-- <div class="card">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-server me-2"></i>
					System Status
				</h5>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span>Database</span>
					<span class="badge badge-success">
						<i class="fas fa-check-circle me-1"></i>
						Online
					</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span>Email Service</span>
					<span class="badge badge-success">
						<i class="fas fa-check-circle me-1"></i>
						Online
					</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span>Redis Queue</span>
					<span class="badge badge-success">
						<i class="fas fa-check-circle me-1"></i>
						Online
					</span>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span>Storage</span>
					<span class="badge badge-warning">
						<i class="fas fa-exclamation-triangle me-1"></i>
						85% Used
					</span>
				</div>
			</div>
		</div> -->
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>