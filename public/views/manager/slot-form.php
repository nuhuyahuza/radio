<?php
use App\Utils\Session;

$isEdit = isset($slot) && $slot;
$pageTitle = $isEdit ? 'Edit Slot' : 'Create New Slot';
$currentPage = 'slots';     
$formAction = $isEdit ? "/manager/slots/edit/{$slot['id']}" : '/manager/slots/create';
ob_start();
?>

<div class="row justify-content-center">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-calendar-plus me-2"></i>
					<?= $pageTitle ?>
				</h5>
			</div>
			<div class="card-body">
				<form method="POST" action="<?= $formAction ?>">
					<input type="hidden" name="csrf_token" value="<?= \App\Utils\Session::getCsrfToken() ?>">

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="date" class="form-label">Date *</label>
								<input type="date" class="form-control" id="date" name="date"
									value="<?= $isEdit ? $slot['date'] : ($_GET['date'] ?? '') ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="status" class="form-label">Status</label>
								<select class="form-select" id="status" name="status">
									<option value="available"
										<?= $isEdit && $slot['status'] === 'available' ? 'selected' : '' ?>>Available
									</option>
									<option value="maintenance"
										<?= $isEdit && $slot['status'] === 'maintenance' ? 'selected' : '' ?>>
										Maintenance</option>
									<?php if ($isEdit): ?>
									<option value="cancelled" <?= $slot['status'] === 'cancelled' ? 'selected' : '' ?>>
										Cancelled</option>
									<?php endif; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="start_time" class="form-label">Start Time *</label>
								<input type="time" class="form-control" id="start_time" name="start_time"
									value="<?= $isEdit ? $slot['start_time'] : '' ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="end_time" class="form-label">End Time *</label>
								<input type="time" class="form-control" id="end_time" name="end_time"
									value="<?= $isEdit ? $slot['end_time'] : '' ?>" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="price" class="form-label">Price ($) *</label>
								<input type="number" class="form-control" id="price" name="price" step="0.01" min="0"
									value="<?= $isEdit ? $slot['price'] : '' ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label for="duration" class="form-label">Duration</label>
								<input type="text" class="form-control" id="duration" readonly>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description</label>
						<textarea class="form-control" id="description" name="description" rows="3"
							placeholder="Optional description for this slot..."><?= $isEdit ? htmlspecialchars($slot['description']) : '' ?></textarea>
					</div>

					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<a href="/manager/slots" class="btn btn-secondary me-md-2">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save me-2"></i>
							<?= $isEdit ? 'Update Slot' : 'Create Slot' ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/manager-dashboard.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const startTimeInput = document.getElementById('start_time');
	const endTimeInput = document.getElementById('end_time');
	const durationInput = document.getElementById('duration');

	function calculateDuration() {
		const startTime = startTimeInput.value;
		const endTime = endTimeInput.value;

		if (startTime && endTime) {
			const start = new Date('2000-01-01 ' + startTime);
			const end = new Date('2000-01-01 ' + endTime);

			if (end > start) {
				const diffMs = end - start;
				const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
				const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

				if (diffHours > 0) {
					durationInput.value = diffHours + 'h ' + diffMinutes + 'm';
				} else {
					durationInput.value = diffMinutes + ' minutes';
				}
			} else {
				durationInput.value = 'Invalid time range';
			}
		} else {
			durationInput.value = '';
		}
	}

	startTimeInput.addEventListener('change', calculateDuration);
	endTimeInput.addEventListener('change', calculateDuration);

	// Calculate initial duration if editing
	calculateDuration();

	// Set minimum date to today
	const dateInput = document.getElementById('date');
	const today = new Date().toISOString().split('T')[0];
	dateInput.min = today;
});
</script>