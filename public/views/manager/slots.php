<?php
$pageTitle = 'Time Slots Management';
$currentPage = 'slots';
ob_start();
?>

<!-- Statistics Cards -->
<div class="row mb-4">
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
				<i class="fas fa-calendar-alt"></i>
			</div>
			<div class="stats-number"><?= $stats['total'] ?? 0 ?></div>
			<div class="stats-label">Total Slots</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
				<i class="fas fa-check-circle"></i>
			</div>
			<div class="stats-number"><?= $stats['available'] ?? 0 ?></div>
			<div class="stats-label">Available</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
				<i class="fas fa-bookmark"></i>
			</div>
			<div class="stats-number"><?= $stats['booked'] ?? 0 ?></div>
			<div class="stats-label">Booked</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stats-card">
			<div class="stats-icon" style="background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);">
				<i class="fas fa-times-circle"></i>
			</div>
			<div class="stats-number"><?= $stats['cancelled'] ?? 0 ?></div>
			<div class="stats-label">Cancelled</div>
		</div>
	</div>
</div>

<!-- Calendar -->
<div class="card mb-4">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="card-title mb-0">
				<i class="fas fa-calendar-alt me-2"></i>
				Slots Calendar
			</h5>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshCalendar()">
					<i class="fas fa-sync-alt me-1"></i>
					Refresh
				</button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div id="slotsCalendar"></div>
	</div>
</div>

<!-- Slots List -->
<div class="card">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="card-title mb-0">
				<i class="fas fa-list me-2"></i>
				Recent Slots
			</h5>
			<a href="/manager/slots/create" class="btn btn-primary btn-sm">
				<i class="fas fa-plus me-1"></i>
				Create New Slot
			</a>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-hover mb-0">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Price</th>
						<th>Status</th>
						<th>Description</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (array_slice($slots, 0, 10) as $slot): ?>
					<tr>
						<td><?= date('M j, Y', strtotime($slot['date'])) ?></td>
						<td><?= date('g:i A', strtotime($slot['start_time'])) ?> -
							<?= date('g:i A', strtotime($slot['end_time'])) ?></td>
						<td class="fw-bold">GH₵<?= number_format($slot['price'], 2) ?></td>
						<td>
							<span
								class="badge bg-<?= $slot['status'] === 'available' ? 'success' : ($slot['status'] === 'booked' ? 'danger' : 'secondary') ?>">
								<?= ucfirst($slot['status']) ?>
							</span>
						</td>
						<td><?= htmlspecialchars($slot['description'] ?: 'No description') ?></td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href="/manager/slots/edit/<?= $slot['id'] ?>" class="btn btn-outline-primary">
									<i class="fas fa-edit"></i>
								</a>
								<?php if ($slot['status'] !== 'booked'): ?>
								<form method="POST" action="/manager/slots/delete/<?= $slot['id'] ?>" class="d-inline"
									onsubmit="return confirm('Are you sure you want to delete this slot?')">
									<input type="hidden" name="csrf_token"
										value="<?= \App\Utils\Session::getCsrfToken() ?>">
									<button type="submit" class="btn btn-outline-danger">
										<i class="fas fa-trash"></i>
									</button>
								</form>
								<?php endif; ?>
								<?php if ($slot['status'] === 'available'): ?>
								<form method="POST" action="/manager/slots/cancel/<?= $slot['id'] ?>" class="d-inline"
									onsubmit="return confirm('Are you sure you want to cancel this slot?')">
									<input type="hidden" name="csrf_token"
										value="<?= \App\Utils\Session::getCsrfToken() ?>">
									<button type="submit" class="btn btn-outline-warning">
										<i class="fas fa-ban"></i>
									</button>
								</form>
								<?php endif; ?>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/manager-dashboard.php';
?>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const calendarEl = document.getElementById('slotsCalendar');
	const calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: 'dayGridMonth',
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
		events: '/manager/slots/data',
		eventClick: function(info) {
			const slotId = info.event.id;
			const status = info.event.extendedProps.status;

			if (status === 'available') {
				if (confirm('Edit this slot?')) {
					window.location.href = '/manager/slots/edit/' + slotId;
				}
			} else {
				alert('This slot is ' + status + ' and cannot be edited.');
			}
		},
		dateClick: function(info) {
			const date = info.dateStr;
			const today = new Date();
			today.setHours(0, 0, 0, 0);

			if (info.date < today) {
				alert('Cannot create slots for past dates.');
				return;
			}

			if (confirm('Create a new slot for ' + date + '?')) {
				window.location.href = '/manager/slots/create?date=' + date;
			}
		}
	});
	calendar.render();
});

function refreshCalendar() {
	const calendar = FullCalendar.getApi();
	calendar.refetchEvents();
}
</script>