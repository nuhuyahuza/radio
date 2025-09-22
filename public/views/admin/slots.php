<?php
$pageTitle = 'Time Slots Management';
$currentPage = 'slots';
ob_start();
?>

<div class="row mb-4">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center">
			<h2 class="mb-0">Time Slots Management</h2>
			<div class="d-flex gap-2">
				<button class="btn btn-outline-primary" onclick="refreshSlots()">
					<i class="fas fa-sync-alt me-2"></i>Refresh
				</button>
				<button class="btn btn-success" onclick="showCreateSlotModal()">
					<i class="fas fa-plus me-2"></i>Add New Slot
				</button>
				<button class="btn btn-primary" onclick="generateSlots()">
					<i class="fas fa-magic me-2"></i>Generate Slots
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Calendar View -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0">
						<i class="fas fa-calendar-alt me-2"></i>
						Calendar View
					</h5>
					<div class="d-flex gap-2">
						<button class="btn btn-sm btn-outline-secondary" onclick="previousMonth()">
							<i class="fas fa-chevron-left"></i>
						</button>
						<span class="btn btn-outline-secondary btn-sm" id="currentMonth">January 2024</span>
						<button class="btn btn-sm btn-outline-secondary" onclick="nextMonth()">
							<i class="fas fa-chevron-right"></i>
						</button>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div id="calendar" class="calendar-container"></div>
			</div>
		</div>
	</div>
</div>

<!-- Slots List -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0">
						<i class="fas fa-clock me-2"></i>
						Slots List
					</h5>
					<div class="d-flex gap-2">
						<select class="form-select form-select-sm" id="dateFilter" onchange="filterSlots()">
							<option value="">All Dates</option>
							<option value="today">Today</option>
							<option value="tomorrow">Tomorrow</option>
							<option value="week">This Week</option>
							<option value="month">This Month</option>
						</select>
						<select class="form-select form-select-sm" id="statusFilter" onchange="filterSlots()">
							<option value="">All Statuses</option>
							<option value="available">Available</option>
							<option value="booked">Booked</option>
							<option value="blocked">Blocked</option>
						</select>
					</div>
				</div>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0" id="slotsTable">
						<thead class="table-light">
							<tr>
								<th></th>
								<th>
									<input type="checkbox" class="form-check-input" id="selectAll"
										onchange="toggleSelectAll()">
								</th>
								<th>Date</th>
								<th>Time</th>
								<th>Duration</th>
								<th>Price</th>
								<th>Status</th>
								<th>Booked By</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody id="slotsTableBody">
							<!-- Slots will be loaded here via JavaScript -->
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer">
				<div class="d-flex justify-content-between align-items-center">
					<div class="d-flex gap-2">
						<button class="btn btn-sm btn-warning" onclick="bulkBlock()" id="bulkBlockBtn" disabled>
							<i class="fas fa-ban me-1"></i>Block Selected
						</button>
						<button class="btn btn-sm btn-success" onclick="bulkUnblock()" id="bulkUnblockBtn" disabled>
							<i class="fas fa-check me-1"></i>Unblock Selected
						</button>
						<button class="btn btn-sm btn-danger" onclick="bulkDelete()" id="bulkDeleteBtn" disabled>
							<i class="fas fa-trash me-1"></i>Delete Selected
						</button>
					</div>
					<div class="d-flex align-items-center">
						<span class="text-muted me-3">Showing <span id="showingCount">0</span> of <span
								id="totalCount">0</span> slots</span>
						<div class="btn-group" role="group">
							<button class="btn btn-outline-secondary btn-sm" onclick="changePage(-1)" id="prevBtn"
								disabled>
								<i class="fas fa-chevron-left"></i>
							</button>
							<span class="btn btn-outline-secondary btn-sm" id="pageInfo">Page 1 of 1</span>
							<button class="btn btn-outline-secondary btn-sm" onclick="changePage(1)" id="nextBtn"
								disabled>
								<i class="fas fa-chevron-right"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Create/Edit Slot Modal -->
<div class="modal fade" id="slotModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="slotModalTitle">Add New Slot</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form id="slotForm">
				<div class="modal-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="slotDate" class="form-label">Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control" id="slotDate" required>
						</div>
						<div class="col-md-3">
							<label for="slotStartTime" class="form-label">Start Time <span
									class="text-danger">*</span></label>
							<input type="time" class="form-control" id="slotStartTime" required>
						</div>
						<div class="col-md-3">
							<label for="slotEndTime" class="form-label">End Time <span
									class="text-danger">*</span></label>
							<input type="time" class="form-control" id="slotEndTime" required>
						</div>
						<div class="col-md-6">
							<label for="slotPrice" class="form-label">Price <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text">GH₵</span>
								<input type="number" class="form-control" id="slotPrice" step="0.01" min="0" required>
							</div>
						</div>
						<div class="col-md-6">
							<label for="slotStatus" class="form-label">Status</label>
							<select class="form-select" id="slotStatus">
								<option value="available">Available</option>
								<option value="blocked">Blocked</option>
							</select>
						</div>
						<div class="col-12">
							<label for="slotDescription" class="form-label">Description</label>
							<textarea class="form-control" id="slotDescription" rows="3"
								placeholder="Optional description for this slot..."></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" id="saveSlotBtn">Save Slot</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Generate Slots Modal -->
<div class="modal fade" id="generateSlotsModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Generate Time Slots</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form id="generateSlotsForm">
				<div class="modal-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="genStartDate" class="form-label">Start Date <span
									class="text-danger">*</span></label>
							<input type="date" class="form-control" id="genStartDate" required>
						</div>
						<div class="col-md-6">
							<label for="genEndDate" class="form-label">End Date <span
									class="text-danger">*</span></label>
							<input type="date" class="form-control" id="genEndDate" required>
						</div>
						<div class="col-md-6">
							<label for="genStartTime" class="form-label">Start Time <span
									class="text-danger">*</span></label>
							<input type="time" class="form-control" id="genStartTime" required>
						</div>
						<div class="col-md-6">
							<label for="genEndTime" class="form-label">End Time <span
									class="text-danger">*</span></label>
							<input type="time" class="form-control" id="genEndTime" required>
						</div>
						<div class="col-md-6">
							<label for="genDuration" class="form-label">Slot Duration (minutes) <span
									class="text-danger">*</span></label>
							<select class="form-select" id="genDuration" required>
								<option value="15">15 minutes</option>
								<option value="30" selected>30 minutes</option>
								<option value="60">60 minutes</option>
								<option value="120">120 minutes</option>
							</select>
						</div>
						<div class="col-md-6">
							<label for="genPrice" class="form-label">Default Price <span
									class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text">GH₵</span>
								<input type="number" class="form-control" id="genPrice" step="0.01" min="0" required>
							</div>
						</div>
						<div class="col-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="genWeekdaysOnly">
								<label class="form-check-label" for="genWeekdaysOnly">
									Generate only on weekdays (Monday - Friday)
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Generate Slots</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Slot Details Modal -->
<div class="modal fade" id="slotDetailsModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Slot Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="slotDetailsContent">
				<!-- Slot details will be loaded here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="editSlot()" id="editSlotBtn">Edit</button>
			</div>
		</div>
	</div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let selectedSlots = [];
let currentFilters = {};
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let editingSlotId = null;

// Load slots on page load
document.addEventListener('DOMContentLoaded', function() {
	loadSlots();
	updateCalendar();
	setDefaultDates();
});

// Set default dates
function setDefaultDates() {
	const today = new Date().toISOString().split('T')[0];
	document.getElementById('slotDate').value = today;
	document.getElementById('genStartDate').value = today;
	document.getElementById('genEndDate').value = today;
}

// Load slots from server
function loadSlots() {
	const params = new URLSearchParams({
		page: currentPage,
		...currentFilters
	});

	fetch(`/admin/slots/data?${params}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				renderSlots(data.slots);
				updatePagination(data.pagination);
			} else {
				showAlert('Error loading slots: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error loading slots', 'danger');
		});
}

// Render slots in table
function renderSlots(slots) {
	const tbody = document.getElementById('slotsTableBody');
	tbody.innerHTML = '';

	if (slots.length === 0) {
		tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <div>No slots found</div>
                </td>
            </tr>
        `;
		return;
	}

	slots.forEach(slot => {
		const row = document.createElement('tr');
		row.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input slot-checkbox" value="${slot.id}" onchange="toggleSlotSelection(${slot.id})">
            </td>
            <td>${formatDate(slot.date)}</td>
            <td>
                <div>
                    <div class="fw-bold">${formatTime(slot.start_time)}</div>
                    <small class="text-muted">to ${formatTime(slot.end_time)}</small>
                </div>
            </td>
            <td>${slot.duration} min</td>
            <td class="fw-bold">GH₵${formatAmount(slot.price)}</td>
            <td>${getStatusBadge(slot.status)}</td>
            <td>
                ${slot.booked_by ? `
                    <div>
                        <div class="fw-bold">${slot.booked_by.name}</div>
                        <small class="text-muted">${slot.booked_by.email}</small>
                    </div>
                ` : '<span class="text-muted">-</span>'}
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewSlot(${slot.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editSlot(${slot.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${slot.status === 'available' ? `
                        <button class="btn btn-sm btn-warning" onclick="blockSlot(${slot.id})" title="Block">
                            <i class="fas fa-ban"></i>
                        </button>
                    ` : slot.status === 'blocked' ? `
                        <button class="btn btn-sm btn-success" onclick="unblockSlot(${slot.id})" title="Unblock">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSlot(${slot.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
		tbody.appendChild(row);
	});
}

// Update calendar
function updateCalendar() {
	const calendar = document.getElementById('calendar');
	const monthNames = ["January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"
	];

	document.getElementById('currentMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;

	// Generate calendar HTML
	const firstDay = new Date(currentYear, currentMonth, 1);
	const lastDay = new Date(currentYear, currentMonth + 1, 0);
	const daysInMonth = lastDay.getDate();
	const startingDayOfWeek = firstDay.getDay();

	let calendarHTML = '<div class="calendar-grid">';

	// Days of week header
	const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	dayNames.forEach(day => {
		calendarHTML += `<div class=\"calendar-day-header\">${day}</div>`;
	});

	// Empty cells for days before month starts
	for (let i = 0; i < startingDayOfWeek; i++) {
		calendarHTML += '<div class="calendar-day empty"></div>';
	}

	// Days of the month
	for (let day = 1; day <= daysInMonth; day++) {
		const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
		calendarHTML += `
            <div class="calendar-day" data-date="${dateStr}" onclick="showDaySlots('${dateStr}')">
                <div class=\"day-number\">${day}</div>
                <div class="day-slots" id="slots-${dateStr}"></div>
            </div>
        `;
	}

	calendarHTML += '</div>';
	calendar.innerHTML = calendarHTML;

	// Load slots for this month
	loadMonthSlots();
}

// Load slots for current month
function loadMonthSlots() {
	const startDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-01`;
	const endDate =
		`${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${new Date(currentYear, currentMonth + 1, 0).getDate()}`;

	fetch(`/admin/slots/calendar?start_date=${startDate}&end_date=${endDate}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				updateCalendarSlots(data.slots);
			}
		})
		.catch(error => {
			console.error('Error loading calendar slots:', error);
		});
}

// Update calendar slots
function updateCalendarSlots(slots) {
	slots.forEach(slot => {
		const dayElement = document.querySelector(`[data-date="${slot.date}"]`);
		if (dayElement) {
			const slotsContainer = dayElement.querySelector('.day-slots');
			if (!slotsContainer.querySelector('.slot-indicator')) {
				const indicator = document.createElement('div');
				indicator.className = `slot-indicator ${slot.status}`;
				indicator.title =
					`${formatTime(slot.start_time)} - ${formatTime(slot.end_time)} ($${formatAmount(slot.price)})`;
				slotsContainer.appendChild(indicator);
			}
		}
	});
}

// Previous month
function previousMonth() {
	currentMonth--;
	if (currentMonth < 0) {
		currentMonth = 11;
		currentYear--;
	}
	updateCalendar();
}

// Next month
function nextMonth() {
	currentMonth++;
	if (currentMonth > 11) {
		currentMonth = 0;
		currentYear++;
	}
	updateCalendar();
}

// Show day slots
function showDaySlots(date) {
	currentFilters.date = date;
	loadSlots();
}

// Filter slots
function filterSlots() {
	currentFilters = {
		date: document.getElementById('dateFilter').value,
		status: document.getElementById('statusFilter').value
	};

	// Remove empty filters
	Object.keys(currentFilters).forEach(key => {
		if (!currentFilters[key]) {
			delete currentFilters[key];
		}
	});

	currentPage = 1;
	loadSlots();
}

// Show create slot modal
function showCreateSlotModal() {
	editingSlotId = null;
	document.getElementById('slotModalTitle').textContent = 'Add New Slot';
	document.getElementById('slotForm').reset();
	setDefaultDates();
	new bootstrap.Modal(document.getElementById('slotModal')).show();
}

// Edit slot
function editSlot(slotId) {
	editingSlotId = slotId;
	document.getElementById('slotModalTitle').textContent = 'Edit Slot';

	fetch(`/admin/slots/${slotId}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				const slot = data.slot;
				document.getElementById('slotDate').value = slot.date;
				document.getElementById('slotStartTime').value = slot.start_time;
				document.getElementById('slotEndTime').value = slot.end_time;
				document.getElementById('slotPrice').value = slot.price;
				document.getElementById('slotStatus').value = slot.status;
				document.getElementById('slotDescription').value = slot.description || '';
				new bootstrap.Modal(document.getElementById('slotModal')).show();
			} else {
				showAlert('Error loading slot: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error loading slot', 'danger');
		});
}

// Save slot
document.getElementById('slotForm').addEventListener('submit', function(e) {
	e.preventDefault();

	const formData = {
		date: document.getElementById('slotDate').value,
		start_time: document.getElementById('slotStartTime').value,
		end_time: document.getElementById('slotEndTime').value,
		price: document.getElementById('slotPrice').value,
		status: document.getElementById('slotStatus').value,
		description: document.getElementById('slotDescription').value
	};

	const url = editingSlotId ? `/admin/slots/${editingSlotId}` : '/admin/slots';
	const method = editingSlotId ? 'PUT' : 'POST';

	fetch(url, {
			method: method,
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify(formData)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(editingSlotId ? 'Slot updated successfully' : 'Slot created successfully',
					'success');
				bootstrap.Modal.getInstance(document.getElementById('slotModal')).hide();
				loadSlots();
				updateCalendar();
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error saving slot', 'danger');
		});
});

// Generate slots
function generateSlots() {
	new bootstrap.Modal(document.getElementById('generateSlotsModal')).show();
}

// Generate slots form submission
document.getElementById('generateSlotsForm').addEventListener('submit', function(e) {
	e.preventDefault();

	const formData = {
		start_date: document.getElementById('genStartDate').value,
		end_date: document.getElementById('genEndDate').value,
		start_time: document.getElementById('genStartTime').value,
		end_time: document.getElementById('genEndTime').value,
		duration: document.getElementById('genDuration').value,
		price: document.getElementById('genPrice').value,
		weekdays_only: document.getElementById('genWeekdaysOnly').checked
	};

	fetch('/admin/slots/generate', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify(formData)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(`Generated ${data.count} slots successfully`, 'success');
				bootstrap.Modal.getInstance(document.getElementById('generateSlotsModal')).hide();
				loadSlots();
				updateCalendar();
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error generating slots', 'danger');
		});
});

// Toggle select all
function toggleSelectAll() {
	const selectAll = document.getElementById('selectAll');
	const checkboxes = document.querySelectorAll('.slot-checkbox');

	checkboxes.forEach(checkbox => {
		checkbox.checked = selectAll.checked;
		toggleSlotSelection(parseInt(checkbox.value));
	});
}

// Toggle individual slot selection
function toggleSlotSelection(slotId) {
	const index = selectedSlots.indexOf(slotId);
	if (index > -1) {
		selectedSlots.splice(index, 1);
	} else {
		selectedSlots.push(slotId);
	}

	updateBulkActionButtons();
}

// Update bulk action buttons
function updateBulkActionButtons() {
	const hasSelection = selectedSlots.length > 0;
	document.getElementById('bulkBlockBtn').disabled = !hasSelection;
	document.getElementById('bulkUnblockBtn').disabled = !hasSelection;
	document.getElementById('bulkDeleteBtn').disabled = !hasSelection;
}

// Bulk actions
function bulkBlock() {
	if (selectedSlots.length === 0) return;
	performBulkAction('block', selectedSlots);
}

function bulkUnblock() {
	if (selectedSlots.length === 0) return;
	performBulkAction('unblock', selectedSlots);
}

function bulkDelete() {
	if (selectedSlots.length === 0) return;
	if (confirm(`Are you sure you want to delete ${selectedSlots.length} selected slot(s)?`)) {
		performBulkAction('delete', selectedSlots);
	}
}

// Perform bulk action
function performBulkAction(action, slotIds) {
	fetch('/admin/slots/bulk-action', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify({
				action: action,
				slot_ids: slotIds
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(
					`${action.charAt(0).toUpperCase() + action.slice(1)}ed ${slotIds.length} slot(s) successfully`,
					'success');
				selectedSlots = [];
				loadSlots();
				updateCalendar();
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error performing bulk action', 'danger');
		});
}

// Individual slot actions
function viewSlot(slotId) {
	fetch(`/admin/slots/${slotId}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				document.getElementById('slotDetailsContent').innerHTML = data.html;
				new bootstrap.Modal(document.getElementById('slotDetailsModal')).show();
			} else {
				showAlert('Error loading slot details: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error loading slot details', 'danger');
		});
}

function blockSlot(slotId) {
	if (confirm('Are you sure you want to block this slot?')) {
		updateSlotStatus(slotId, 'blocked');
	}
}

function unblockSlot(slotId) {
	if (confirm('Are you sure you want to unblock this slot?')) {
		updateSlotStatus(slotId, 'available');
	}
}

function deleteSlot(slotId) {
	if (confirm('Are you sure you want to delete this slot?')) {
		fetch(`/admin/slots/${slotId}`, {
				method: 'DELETE',
				headers: {
					'X-CSRF-Token': getCsrfToken()
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					showAlert('Slot deleted successfully', 'success');
					loadSlots();
					updateCalendar();
				} else {
					showAlert('Error: ' + data.message, 'danger');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showAlert('Error deleting slot', 'danger');
			});
	}
}

// Update slot status
function updateSlotStatus(slotId, status) {
	fetch(`/admin/slots/${slotId}/status`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify({
				status: status
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(`Slot ${status} successfully`, 'success');
				loadSlots();
				updateCalendar();
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error updating slot status', 'danger');
		});
}

// Refresh slots
function refreshSlots() {
	loadSlots();
	updateCalendar();
}

// Change page
function changePage(direction) {
	const newPage = currentPage + direction;
	if (newPage >= 1 && newPage <= totalPages) {
		currentPage = newPage;
		loadSlots();
	}
}

// Update pagination
function updatePagination(pagination) {
	totalPages = pagination.total_pages;
	currentPage = pagination.current_page;

	document.getElementById('showingCount').textContent = pagination.showing;
	document.getElementById('totalCount').textContent = pagination.total;
	document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;

	document.getElementById('prevBtn').disabled = currentPage <= 1;
	document.getElementById('nextBtn').disabled = currentPage >= totalPages;
}

// Utility functions
function formatDate(dateString) {
	return new Date(dateString).toLocaleDateString();
}

function formatTime(timeString) {
	return new Date(`2000-01-01T${timeString}`).toLocaleTimeString([], {
		hour: '2-digit',
		minute: '2-digit'
	});
}

function formatAmount(amount) {
	return parseFloat(amount).toFixed(2);
}

function getStatusBadge(status) {
	const badges = {
		'available': '<span class="badge bg-success">Available</span>',
		'booked': '<span class="badge bg-primary">Booked</span>',
		'blocked': '<span class="badge bg-warning">Blocked</span>'
	};
	return badges[status] || '<span class="badge bg-info">' + status + '</span>';
}

function getCsrfToken() {
	return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function showAlert(message, type) {
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
	alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

	const container = document.querySelector('.content-area');
	container.insertBefore(alertDiv, container.firstChild);

	setTimeout(() => {
		alertDiv.remove();
	}, 5000);
}
</script>

<style>
.calendar-container {
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.calendar-grid {
	display: grid;
	grid-template-columns: repeat(7, 1fr);
	gap: 1px;
	background-color: #e9ecef;
	border: 1px solid #e9ecef;
	border-radius: 8px;
	overflow: hidden;
}

.calendar-day-header {
	background-color: #f8f9fa;
	padding: 12px 8px;
	text-align: center;
	font-weight: 600;
	color: #495057;
	border-bottom: 1px solid #e9ecef;
}

.calendar-day {
	background-color: white;
	padding: 8px;
	min-height: 80px;
	cursor: pointer;
	transition: background-color 0.2s;
	position: relative;
}

.calendar-day:hover {
	background-color: #f8f9fa;
}

.calendar-day.empty {
	background-color: #f8f9fa;
	cursor: default;
}

.calendar-day.today {
	background-color: #e3f2fd;
}

.day-number {
	font-weight: 600;
	margin-bottom: 4px;
}

.day-slots {
	display: flex;
	flex-wrap: wrap;
	gap: 2px;
}

.slot-indicator {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	flex-shrink: 0;
}

.slot-indicator.available {
	background-color: #28a745;
}

.slot-indicator.booked {
	background-color: #007bff;
}

.slot-indicator.blocked {
	background-color: #ffc107;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>