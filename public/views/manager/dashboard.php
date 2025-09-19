<?php
$pageTitle = 'Station Manager Dashboard';
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
			<div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
				<i class="fas fa-clock"></i>
			</div>
			<div class="stats-number"><?= $stats['pending_bookings'] ?? 0 ?></div>
			<div class="stats-label">Pending Bookings</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
				<i class="fas fa-calendar-check"></i>
			</div>
			<div class="stats-number"><?= $stats['todays_bookings'] ?? 0 ?></div>
			<div class="stats-label">Today's Bookings</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
				<i class="fas fa-calendar-alt"></i>
			</div>
			<div class="stats-number"><?= $stats['available_slots'] ?? 0 ?></div>
			<div class="stats-label">Available Slots</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
				<i class="fas fa-dollar-sign"></i>
			</div>
			<div class="stats-number">GH₵<?= number_format($stats['monthly_revenue'] ?? 0, 0) ?></div>
			<div class="stats-label">Monthly Revenue</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Pending Bookings -->
	<div class="col-lg-8 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-clock me-2"></i>
					Pending Bookings
				</h5>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead>
							<tr>
								<th>ID</th>
								<th>Advertiser</th>
								<th>Date & Time</th>
								<th>Amount</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($pendingBookings)): ?>
							<?php foreach ($pendingBookings as $booking): ?>
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
									<div class="btn-group" role="group">
										<a href="/manager/bookings/<?= $booking['id'] ?>/approve"
											class="btn btn-sm btn-success"
											onclick="return confirm('Approve this booking?')">
											<i class="fas fa-check"></i>
										</a>
										<a href="/manager/bookings/<?= $booking['id'] ?>/reject"
											class="btn btn-sm btn-danger"
											onclick="return confirm('Reject this booking?')">
											<i class="fas fa-times"></i>
										</a>
										<a href="/manager/bookings/<?= $booking['id'] ?>"
											class="btn btn-sm btn-outline-primary">
											<i class="fas fa-eye"></i>
										</a>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="5" class="text-center text-muted py-4">
									<i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
									<div>No pending bookings</div>
								</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer text-center">
				<a href="/manager/bookings" class="btn btn-primary">
					<i class="fas fa-list me-2"></i>
					Manage All Bookings
				</a>
			</div>
		</div>
	</div>

	<!-- Today's Schedule & Quick Actions -->
	<div class="col-lg-4">
		<!-- Today's Schedule -->
		<div class="card mb-4">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-calendar-day me-2"></i>
					Today's Schedule
				</h5>
			</div>
			<div class="card-body">
				<?php if (!empty($todaysSchedule)): ?>
				<?php foreach ($todaysSchedule as $slot): ?>
				<div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded"
					style="background: <?= $slot['status'] === 'booked' ? '#e8f5e8' : '#f8f9fa' ?>">
					<div>
						<div class="fw-bold">
							<?= date('g:i A', strtotime($slot['start_time'])) ?> -
							<?= date('g:i A', strtotime($slot['end_time'])) ?>
						</div>
						<?php if ($slot['status'] === 'booked'): ?>
						<small class="text-muted">
							<?= htmlspecialchars($slot['advertiser_name']) ?>
						</small>
						<?php else: ?>
						<small class="text-success">Available</small>
						<?php endif; ?>
					</div>
					<div class="text-end">
						<div class="fw-bold">GH₵<?= number_format($slot['price'], 0) ?></div>
						<span class="badge <?= $slot['status'] === 'booked' ? 'badge-success' : 'badge-info' ?>">
							<?= ucfirst($slot['status']) ?>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
				<?php else: ?>
				<div class="text-center text-muted py-3">
					<i class="fas fa-calendar-times fa-2x mb-2"></i>
					<div>No slots scheduled for today</div>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fas fa-bolt me-2"></i>
					Quick Actions
				</h5>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="/manager/slots/create" class="btn btn-outline-success">
						<i class="fas fa-plus me-2"></i>
						Create New Slot
					</a>
					<a href="/manager/slots" class="btn btn-outline-primary">
						<i class="fas fa-calendar-alt me-2"></i>
						Manage Slots
					</a>
					<a href="/manager/bookings" class="btn btn-outline-warning">
						<i class="fas fa-clipboard-list me-2"></i>
						Manage Bookings
					</a>
					<a href="/manager/reports" class="btn btn-outline-info">
						<i class="fas fa-chart-bar me-2"></i>
						View Reports
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>