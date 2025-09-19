<?php
$pageTitle = 'Bookings Management';
$currentPage = 'bookings';
ob_start();
?>

<style>
/* Enhanced Styles for Bookings Management */
.fade-in {
	animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(20px);
	}

	to {
		opacity: 1;
		transform: translateY(0);
	}
}

.card-hover {
	transition: all 0.3s ease;
	border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-hover:hover {
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
	transform: translateY(-2px);
}

.text-gradient {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	font-weight: 600;
}

.chip {
	display: inline-flex;
	align-items: center;
	padding: 0.375rem 0.75rem;
	border-radius: 50px;
	font-size: 0.875rem;
	font-weight: 500;
	color: white;
	gap: 0.25rem;
	min-width: fit-content;
}

.chip.bg-warning {
	background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%) !important;
}

.chip.bg-success {
	background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
}

.chip.bg-danger {
	background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
}

.chip.bg-info {
	background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%) !important;
}

.chip.bg-secondary {
	background: linear-gradient(135deg, #a0aec0 0%, #718096 100%) !important;
}

.status-indicator {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	display: inline-block;
	margin-right: 0.25rem;
}

.status-indicator.pending {
	background: #fff;
	animation: pulse 2s infinite;
}

.status-indicator.approved {
	background: #fff;
}

.status-indicator.rejected {
	background: #fff;
}

.status-indicator.cancelled {
	background: #fff;
}

@keyframes pulse {
	0% {
		opacity: 1;
	}

	50% {
		opacity: 0.5;
	}

	100% {
		opacity: 1;
	}
}

.btn-pulse {
	animation: pulse-button 2s infinite;
}

@keyframes pulse-button {
	0% {
		box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
	}

	70% {
		box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
	}

	100% {
		box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
	}
}

.table {
	border-collapse: separate;
	border-spacing: 0;
	border-radius: 12px;
	overflow: hidden;
	background: white;
}

.table th,
.table td {
	padding: 1rem 0.75rem;
	font-size: 1rem;
	vertical-align: middle;
	border: none;
	background: white;
}

.table th:first-child,
.table td:first-child {
	padding-left: 1rem;
}

.table th:last-child,
.table td:last-child {
	padding-right: 1rem;
}

.table thead th {
	background: #f8f9fa;
	color: #495057;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	box-shadow: none;
}

.table-hover tbody tr:hover {
	background-color: #f6f8fc;
}

.btn-group .btn {
	transition: all 0.2s ease;
}

.btn-group .btn:hover {
	transform: translateY(-1px);
	z-index: 1;
}

.form-control:focus,
.form-select:focus {
	border-color: #667eea;
	box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
	border: none;
	border-radius: 10px;
	box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
}

.modal-content {
	border-radius: 15px;
	border: none;
	box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header {
	border-bottom: 1px solid rgba(0, 0, 0, 0.05);
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	border-radius: 15px 15px 0 0;
}

.spinner-border {
	width: 2rem;
	height: 2rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
	.btn-group {
		flex-direction: column;
		gap: 0.25rem;
	}

	.table-responsive {
		font-size: 0.875rem;
	}

	.chip {
		font-size: 0.75rem;
		padding: 0.25rem 0.5rem;
	}
}

/* Loading state */
.loading {
	opacity: 0.6;
	pointer-events: none;
}

/* Enhanced button styles */
.btn-outline-primary:hover {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-color: #667eea;
}

.btn-primary {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-color: #667eea;
}

.btn-success {
	background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
	border-color: #48bb78;
}

.btn-danger {
	background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
	border-color: #f56565;
}

/* Table row animations */
.table tbody tr {
	transition: background 0.2s;
	background: white;
}

/* Enhanced filter section */
.card {
	border-radius: 15px;
	border: 1px solid rgba(0, 0, 0, 0.08);
	box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-body {
	padding: 1.5rem;
}

/* Improved pagination */
.btn-group .btn {
	border-radius: 6px;
	margin: 0 2px;
}

/* Custom checkbox styles */
.form-check-input:checked {
	background-color: #667eea;
	border-color: #667eea;
}

/* Enhanced dropdown styles */
.form-select,
.form-control {
	border-radius: 8px;
	border: 1px solid rgba(0, 0, 0, 0.15);
	transition: all 0.2s ease;
}

/* Better spacing for action buttons */
.btn-group.d-flex {
	gap: 0.25rem;
}

/* Improved typography */
.fw-bold {
	font-weight: 600 !important;
}

.text-muted {
	color: #6c757d !important;
}

/* Enhanced modal styles */
.modal-backdrop {
	background-color: rgba(0, 0, 0, 0.6);
}

.modal-body {
	padding: 2rem;
}

.modal-footer {
	border-top: 1px solid rgba(0, 0, 0, 0.05);
	padding: 1.5rem 2rem;
}
</style>

<div class="row mb-4">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<div>
				<h2 class="mb-0 text-gradient">Bookings Management</h2>
				<small class="text-muted">Manage and review advertising bookings</small>
			</div>
			<div class="d-flex gap-2 flex-wrap">
				<button class="btn btn-outline-primary" onclick="refreshBookings()">
					<i class="fas fa-sync-alt me-2"></i>Refresh
				</button>
				<button class="btn btn-primary" onclick="exportBookings()">
					<i class="fas fa-download me-2"></i>Export
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Enhanced Filters Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card card-hover">
			<div class="card-body">
				<h6 class="card-title mb-3">
					<i class="fas fa-filter me-2 text-gradient"></i>Filters
				</h6>
				<div class="row g-3">
					<div class="col-lg-3 col-md-6">
						<label class="form-label fw-semibold">Status</label>
						<select class="form-select" id="statusFilter" onchange="filterBookings()">
							<option value="">All Statuses</option>
							<option value="pending">Pending</option>
							<option value="approved">Approved</option>
							<option value="rejected">Rejected</option>
							<option value="cancelled">Cancelled</option>
						</select>
					</div>
					<div class="col-lg-3 col-md-6">
						<label class="form-label fw-semibold">Date Range</label>
						<select class="form-select" id="dateFilter" onchange="filterBookings()">
							<option value="">All Dates</option>
							<option value="today">Today</option>
							<option value="week">This Week</option>
							<option value="month">This Month</option>
							<option value="custom">Custom Range</option>
						</select>
					</div>
					<div class="col-lg-3 col-md-6">
						<label class="form-label fw-semibold">Advertiser</label>
						<input type="text" class="form-control" id="advertiserFilter" placeholder="Search advertiser..."
							onkeyup="filterBookings()">
					</div>
					<div class="col-lg-3 col-md-6">
						<label class="form-label fw-semibold">Amount Range</label>
						<select class="form-select" id="amountFilter" onchange="filterBookings()">
							<option value="">All Amounts</option>
							<option value="0-100">GH₵0 - GH₵100</option>
							<option value="100-500">GH₵100 - GH₵500</option>
							<option value="500-1000">GH₵500 - GH₵1,000</option>
							<option value="1000+">GH₵1,000+</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Enhanced Bookings Table -->
<div class="row">
	<div class="col-12">
		<div class="card card-hover">
			<div class="card-header bg-white border-bottom-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0">
						<i class="fas fa-clipboard-list me-2 text-gradient"></i>
						Bookings List
					</h5>
					<div class="d-flex align-items-center gap-3">
						<span class="text-muted small">Auto-refresh: <span class="text-success">ON</span></span>
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="autoRefresh" checked>
							<label class="form-check-label small" for="autoRefresh">Live Updates</label>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0 fade-in" id="bookingsTable">
						<thead class="table-light">
							<tr>
								<th></th>
								<th class="text-center align-middle" style="width:50px;">
									<input type="checkbox" class="form-check-input" id="selectAll"
										onchange="toggleSelectAll()">
								</th>
								<th class="align-middle" style="width:80px;">
									<div class="d-flex align-items-center">
										ID
										<i class="fas fa-sort ms-1 text-muted" style="cursor:pointer;"></i>
									</div>
								</th>
								<th class="align-middle">
									<div class="d-flex align-items-center">
										Advertiser
										<i class="fas fa-sort ms-1 text-muted" style="cursor:pointer;"></i>
									</div>
								</th>
								<th class="align-middle">
									<div class="d-flex align-items-center">
										Date & Time
										<i class="fas fa-sort ms-1 text-muted" style="cursor:pointer;"></i>
									</div>
								</th>
								<th class="align-middle text-center" style="width:110px;">Duration</th>
								<th class="align-middle text-end" style="width:120px;">
									<div class="d-flex align-items-center justify-content-end">
										Amount
										<i class="fas fa-sort ms-1 text-muted" style="cursor:pointer;"></i>
									</div>
								</th>
								<th class="align-middle text-center" style="width:130px;">Status</th>
								<th class="align-middle" style="width:150px;">
									<div class="d-flex align-items-center">
										Created
										<i class="fas fa-sort ms-1 text-muted" style="cursor:pointer;"></i>
									</div>
								</th>
								<th class="align-middle text-center" style="width:140px;">Actions</th>
							</tr>
						</thead>
						<tbody id="bookingsTableBody">
							<tr id="loadingRow">
								<td colspan="9" class="text-center py-5">
									<div class="d-flex justify-content-center align-items-center">
										<div class="spinner-border text-primary me-3" role="status">
											<span class="visually-hidden">Loading...</span>
										</div>
										<div>
											<div class="fw-semibold">Loading bookings...</div>
											<small class="text-muted">Please wait while we fetch the data</small>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer bg-white">
				<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
					<div class="d-flex gap-2 flex-wrap">
						<button class="btn btn-sm btn-success" onclick="bulkApprove()" id="bulkApproveBtn" disabled>
							<i class="fas fa-check me-1"></i>Approve Selected
						</button>
						<button class="btn btn-sm btn-danger" onclick="bulkReject()" id="bulkRejectBtn" disabled>
							<i class="fas fa-times me-1"></i>Reject Selected
						</button>
						<button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()"
							id="clearSelectionBtn" disabled>
							<i class="fas fa-times me-1"></i>Clear Selection
						</button>
					</div>
					<div class="d-flex align-items-center gap-3 flex-wrap">
						<span class="text-muted small">
							Showing <span id="showingCount" class="fw-semibold">0</span> of
							<span id="totalCount" class="fw-semibold">0</span> bookings
						</span>
						<div class="btn-group" role="group">
							<button class="btn btn-outline-secondary btn-sm" onclick="changePage(-1)" id="prevBtn"
								disabled>
								<i class="fas fa-chevron-left"></i>
							</button>
							<span class="btn btn-outline-secondary btn-sm disabled" id="pageInfo">Page 1 of 1</span>
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

<!-- Enhanced Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-eye me-2 text-gradient"></i>Booking Details
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="bookingDetailsContent">
				<div class="d-flex justify-content-center py-4">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" onclick="approveBooking()" id="approveBookingBtn">
					<i class="fas fa-check me-1"></i>Approve
				</button>
				<button type="button" class="btn btn-danger" onclick="rejectBooking()" id="rejectBookingBtn">
					<i class="fas fa-times me-1"></i>Reject
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Enhanced Bulk Actions Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="bulkActionTitle">
					<i class="fas fa-tasks me-2 text-gradient"></i>Bulk Action
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="alert alert-info border-0">
					<i class="fas fa-info-circle me-2"></i>
					<span id="bulkActionMessage">Are you sure you want to perform this action on the selected
						bookings?</span>
				</div>
				<div class="mb-3">
					<label for="bulkActionReason" class="form-label fw-semibold">Reason (Optional)</label>
					<textarea class="form-control" id="bulkActionReason" rows="3"
						placeholder="Enter reason for this action..."></textarea>
					<div class="form-text">This reason will be logged and may be visible to advertisers.</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" onclick="confirmBulkAction()" id="confirmBulkActionBtn">
					<i class="fas fa-check me-1"></i>Confirm Action
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let selectedBookings = [];
let currentFilters = {};
let autoRefreshInterval;

// Enhanced initialization
document.addEventListener('DOMContentLoaded', function() {
	loadBookings();
	initializeAutoRefresh();
	initializeTooltips();
});

// Initialize tooltips
function initializeTooltips() {
	const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
	tooltipTriggerList.map(function(tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}

// Initialize auto-refresh
function initializeAutoRefresh() {
	const autoRefreshCheckbox = document.getElementById('autoRefresh');
	if (autoRefreshCheckbox.checked) {
		autoRefreshInterval = setInterval(() => {
			if (!document.hidden) {
				loadBookings(false); // Silent refresh
			}
		}, 30000); // Refresh every 30 seconds
	}

	autoRefreshCheckbox.addEventListener('change', function() {
		if (this.checked) {
			autoRefreshInterval = setInterval(() => {
				if (!document.hidden) {
					loadBookings(false);
				}
			}, 30000);
		} else {
			clearInterval(autoRefreshInterval);
		}
	});
}

// Enhanced load bookings function
function loadBookings(showLoading = true) {
	if (showLoading) {
		document.getElementById('bookingsTable').classList.add('loading');
	}

	const params = new URLSearchParams({
		page: currentPage,
		...currentFilters
	});

	fetch(`/admin/bookings/data?${params}`)
		.then(response => response.json())
		.then(data => {
			document.getElementById('bookingsTable').classList.remove('loading');

			if (data.success) {
				renderBookings(data.bookings);
				updatePagination(data.pagination);
				updateSelectionState();
			} else {
				showAlert('Error loading bookings: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			document.getElementById('bookingsTable').classList.remove('loading');
			console.error('Error:', error);
			showAlert('Error loading bookings', 'danger');
		});
}

// Enhanced render bookings function
function renderBookings(bookings) {
	const tbody = document.getElementById('bookingsTableBody');
	tbody.innerHTML = '';

	if (bookings.length === 0) {
		tbody.innerHTML = `
            <tr class="fade-in">
                <td colspan="9" class="text-center py-5">
                    <div class="fade-in">
                        <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                        <div class="h5 text-muted">No bookings found</div>
                        <small class="text-muted">Try adjusting your filters or check back later</small>
                        <div class="mt-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="clearFilters()">
                                <i class="fas fa-filter me-1"></i>Clear Filters
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
		return;
	}

	bookings.forEach((booking, index) => {
		const row = document.createElement('tr');
		row.className = 'fade-in';
		row.style.animationDelay = `${index * 0.05}s`;
		row.dataset.bookingId = booking.id;

		const isSelected = selectedBookings.includes(booking.id);

		row.innerHTML = `
            <td class="text-center align-middle">
                <input type="checkbox" class="form-check-input booking-checkbox" 
                       value="${booking.id}" onchange="toggleBookingSelection(${booking.id})"
                       ${isSelected ? 'checked' : ''}>
            </td>
            <td class="align-middle">
                <span class="fw-bold text-gradient">#${booking.id}</span>
            </td>
            <td class="align-middle">
                <div class="d-flex flex-column">
                    <span class="fw-semibold">${escapeHtml(booking.advertiser_name)}</span>
                    <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>${escapeHtml(booking.advertiser_email)}
                    </small>
                </div>
            </td>
            <td class="align-middle">
                <div class="d-flex flex-column">
                    <span class="fw-semibold">
                        <i class="fas fa-calendar me-1 text-muted"></i>${formatDate(booking.date)}
                    </span>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}
                    </small>
                </div>
            </td>
            <td class="align-middle text-center">
                <span class="chip bg-info">
                    <i class="fas fa-stopwatch"></i>
                    ${booking.duration} min
                </span>
            </td>
            <td class="align-middle text-end">
                <span class="fw-bold text-success h6 mb-0">${formatAmount(booking.total_amount)}</span>
            </td>
            <td class="align-middle text-center">${getStatusBadge(booking.status)}</td>
            <td class="align-middle">
                <small class="text-muted">
                    <i class="fas fa-plus-circle me-1"></i>${formatDateTime(booking.created_at)}
                </small>
            </td>
            <td class="align-middle text-center">
                <div class="btn-group d-flex justify-content-center" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewBooking(${booking.id})" 
                            data-bs-toggle="tooltip" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${booking.status === 'pending' ? `
                        <button class="btn btn-sm btn-success btn-pulse" onclick="quickApprove(${booking.id})" 
                                data-bs-toggle="tooltip" title="Quick Approve">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="quickReject(${booking.id})" 
                                data-bs-toggle="tooltip" title="Quick Reject">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
		tbody.appendChild(row);
	});

	// Reinitialize tooltips for new content
	initializeTooltips();
}

// Enhanced filter functions
function filterBookings() {
	currentFilters = {
		status: document.getElementById('statusFilter').value,
		date: document.getElementById('dateFilter').value,
		advertiser: document.getElementById('advertiserFilter').value,
		amount: document.getElementById('amountFilter').value
	};

	// Remove empty filters
	Object.keys(currentFilters).forEach(key => {
		if (!currentFilters[key]) {
			delete currentFilters[key];
		}
	});

	currentPage = 1;
	selectedBookings = []; // Clear selections when filtering
	loadBookings();
}

function clearFilters() {
	document.getElementById('statusFilter').value = '';
	document.getElementById('dateFilter').value = '';
	document.getElementById('advertiserFilter').value = '';
	document.getElementById('amountFilter').value = '';
	currentFilters = {};
	currentPage = 1;
	selectedBookings = [];
	loadBookings();
}

// Enhanced selection functions
function toggleSelectAll() {
	const selectAll = document.getElementById('selectAll');
	const checkboxes = document.querySelectorAll('.booking-checkbox');

	selectedBookings = [];
	checkboxes.forEach(checkbox => {
		checkbox.checked = selectAll.checked;
		if (selectAll.checked) {
			selectedBookings.push(parseInt(checkbox.value));
		}
	});

	updateSelectionState();
}

function toggleBookingSelection(bookingId) {
	const index = selectedBookings.indexOf(bookingId);
	if (index > -1) {
		selectedBookings.splice(index, 1);
	} else {
		selectedBookings.push(bookingId);
	}

	updateSelectionState();
}

function updateSelectionState() {
	const hasSelection = selectedBookings.length > 0;
	const checkboxes = document.querySelectorAll('.booking-checkbox');
	const selectAll = document.getElementById('selectAll');

	// Update bulk action buttons
	document.getElementById('bulkApproveBtn').disabled = !hasSelection;
	document.getElementById('bulkRejectBtn').disabled = !hasSelection;
	document.getElementById('clearSelectionBtn').disabled = !hasSelection;

	// Update select all checkbox
	if (selectedBookings.length === 0) {
		selectAll.indeterminate = false;
		selectAll.checked = false;
	} else if (selectedBookings.length === checkboxes.length) {
		selectAll.indeterminate = false;
		selectAll.checked = true;
	} else {
		selectAll.indeterminate = true;
	}
}

function clearSelection() {
	selectedBookings = [];
	document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = false);
	document.getElementById('selectAll').checked = false;
	updateSelectionState();
}

// Enhanced bulk action functions
function bulkApprove() {
	if (selectedBookings.length === 0) return;

	document.getElementById('bulkActionTitle').innerHTML =
		'<i class="fas fa-check me-2 text-success"></i>Approve Bookings';
	document.getElementById('bulkActionMessage').textContent =
		`Are you sure you want to approve ${selectedBookings.length} selected booking(s)?`;
	document.getElementById('confirmBulkActionBtn').className = 'btn btn-success';
	document.getElementById('confirmBulkActionBtn').onclick = () => confirmBulkAction('approve');

	new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}

function bulkReject() {
	if (selectedBookings.length === 0) return;

	document.getElementById('bulkActionTitle').innerHTML =
		'<i class="fas fa-times me-2 text-danger"></i>Reject Bookings';
	document.getElementById('bulkActionMessage').textContent =
		`Are you sure you want to reject ${selectedBookings.length} selected booking(s)?`;
	document.getElementById('confirmBulkActionBtn').className = 'btn btn-danger';
	document.getElementById('confirmBulkActionBtn').onclick = () => confirmBulkAction('reject');

	new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}

function confirmBulkAction(action) {
	const reason = document.getElementById('bulkActionReason').value;
	const confirmBtn = document.getElementById('confirmBulkActionBtn');

	// Show loading state
	const originalText = confirmBtn.innerHTML;
	confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
	confirmBtn.disabled = true;

	fetch('/admin/bookings/bulk-action', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify({
				action: action,
				booking_ids: selectedBookings,
				reason: reason
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(
					`Successfully ${action}ed ${selectedBookings.length} booking(s)`,
					'success'
				);
				selectedBookings = [];
				loadBookings();
				bootstrap.Modal.getInstance(document.getElementById('bulkActionModal')).hide();

				// Clear the reason field
				document.getElementById('bulkActionReason').value = '';
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error performing bulk action', 'danger');
		})
		.finally(() => {
			confirmBtn.innerHTML = originalText;
			confirmBtn.disabled = false;
		});
}

// Enhanced view booking function
function viewBooking(bookingId) {
	// Show loading state in modal
	document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="d-flex justify-content-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

	const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
	modal.show();

	fetch(`/admin/bookings/${bookingId}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				document.getElementById('bookingDetailsContent').innerHTML = data.html;

				// Update modal buttons based on booking status
				const approveBtn = document.getElementById('approveBookingBtn');
				const rejectBtn = document.getElementById('rejectBookingBtn');

				if (data.booking && data.booking.status === 'pending') {
					approveBtn.style.display = 'inline-block';
					rejectBtn.style.display = 'inline-block';
					approveBtn.onclick = () => updateBookingStatus(bookingId, 'approved');
					rejectBtn.onclick = () => updateBookingStatus(bookingId, 'rejected');
				} else {
					approveBtn.style.display = 'none';
					rejectBtn.style.display = 'none';
				}
			} else {
				document.getElementById('bookingDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading booking details: ${data.message}
                    </div>
                `;
			}
		})
		.catch(error => {
			console.error('Error:', error);
			document.getElementById('bookingDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading booking details. Please try again.
                </div>
            `;
		});
}

// Enhanced quick action functions
function quickApprove(bookingId) {
	showConfirmDialog(
		'Approve Booking',
		'Are you sure you want to approve this booking?',
		'success',
		() => updateBookingStatus(bookingId, 'approved')
	);
}

function quickReject(bookingId) {
	showConfirmDialog(
		'Reject Booking',
		'Are you sure you want to reject this booking?',
		'danger',
		() => updateBookingStatus(bookingId, 'rejected')
	);
}

// Enhanced update booking status function
function updateBookingStatus(bookingId, status) {
	// Find and update the row visually for immediate feedback
	const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
	if (row) {
		row.classList.add('loading');
	}

	fetch(`/admin/bookings/${bookingId}/status`, {
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
				showAlert(`Booking ${status} successfully`, 'success');
				loadBookings();

				// Close modal if open
				const modal = bootstrap.Modal.getInstance(document.getElementById('bookingDetailsModal'));
				if (modal) {
					modal.hide();
				}
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error updating booking status', 'danger');
		})
		.finally(() => {
			if (row) {
				row.classList.remove('loading');
			}
		});
}

// Enhanced export function
function exportBookings() {
	const params = new URLSearchParams(currentFilters);

	// Show loading feedback
	const exportBtn = document.querySelector('button[onclick="exportBookings()"]');
	const originalText = exportBtn.innerHTML;
	exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
	exportBtn.disabled = true;

	// Create a temporary link for download
	const link = document.createElement('a');
	link.href = `/admin/bookings/export?${params}`;
	link.download = `bookings-export-${new Date().toISOString().split('T')[0]}.csv`;
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);

	// Reset button after a delay
	setTimeout(() => {
		exportBtn.innerHTML = originalText;
		exportBtn.disabled = false;
		showAlert('Export started. Check your downloads folder.', 'info');
	}, 1000);
}

// Enhanced refresh function
function refreshBookings() {
	const refreshBtn = document.querySelector('button[onclick="refreshBookings()"]');
	const originalText = refreshBtn.innerHTML;
	refreshBtn.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i>Refreshing...';
	refreshBtn.disabled = true;

	loadBookings();

	setTimeout(() => {
		refreshBtn.innerHTML = originalText;
		refreshBtn.disabled = false;
	}, 1000);
}

// Enhanced pagination functions
function changePage(direction) {
	const newPage = currentPage + direction;
	if (newPage >= 1 && newPage <= totalPages) {
		currentPage = newPage;
		selectedBookings = []; // Clear selections when changing pages
		loadBookings();
	}
}

function updatePagination(pagination) {
	totalPages = pagination.total_pages || 1;
	currentPage = pagination.current_page || 1;

	document.getElementById('showingCount').textContent = pagination.showing || 0;
	document.getElementById('totalCount').textContent = pagination.total || 0;
	document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;

	document.getElementById('prevBtn').disabled = currentPage <= 1;
	document.getElementById('nextBtn').disabled = currentPage >= totalPages;
}

// Utility functions
function formatDate(dateString) {
	const options = {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
		weekday: 'short'
	};
	return new Date(dateString).toLocaleDateString('en-US', options);
}

function formatTime(timeString) {
	return new Date(`2000-01-01T${timeString}`).toLocaleTimeString([], {
		hour: '2-digit',
		minute: '2-digit',
		hour12: true
	});
}

function formatDateTime(dateString) {
	const options = {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
		hour: '2-digit',
		minute: '2-digit',
		hour12: true
	};
	return new Date(dateString).toLocaleString('en-US', options);
}

function formatAmount(amount) {
	return parseFloat(amount).toLocaleString('en-US', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	});
}

function getStatusBadge(status) {
	const badges = {
		'pending': '<span class="chip bg-warning"><span class="status-indicator pending"></span>Pending</span>',
		'approved': '<span class="chip bg-success"><span class="status-indicator approved"></span>Approved</span>',
		'rejected': '<span class="chip bg-danger"><span class="status-indicator rejected"></span>Rejected</span>',
		'cancelled': '<span class="chip bg-secondary"><span class="status-indicator cancelled"></span>Cancelled</span>'
	};
	return badges[status] || `<span class="chip bg-info"><span class="status-indicator"></span>GH₵{status}</span>`;
}

function escapeHtml(text) {
	const div = document.createElement('div');
	div.textContent = text;
	return div.innerHTML;
}

function getCsrfToken() {
	const token = document.querySelector('meta[name="csrf-token"]');
	return token ? token.getAttribute('content') : '';
}

// Enhanced alert function
function showAlert(message, type = 'info', duration = 5000) {
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
	alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    `;

	const icons = {
		success: 'fas fa-check-circle',
		danger: 'fas fa-exclamation-circle',
		warning: 'fas fa-exclamation-triangle',
		info: 'fas fa-info-circle'
	};

	alertDiv.innerHTML = `
        <i class="${icons[type] || icons.info} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

	document.body.appendChild(alertDiv);

	// Auto-dismiss after specified duration
	setTimeout(() => {
		if (alertDiv && alertDiv.parentNode) {
			alertDiv.classList.remove('show');
			setTimeout(() => {
				if (alertDiv && alertDiv.parentNode) {
					alertDiv.remove();
				}
			}, 150);
		}
	}, duration);
}

// Enhanced confirm dialog function
function showConfirmDialog(title, message, type = 'primary', onConfirm) {
	const modal = document.createElement('div');
	modal.className = 'modal fade';
	modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">GH₵{title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>GH₵{message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-${type}" id="confirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    `;

	document.body.appendChild(modal);
	const bootstrapModal = new bootstrap.Modal(modal);

	modal.querySelector('#confirmBtn').addEventListener('click', () => {
		onConfirm();
		bootstrapModal.hide();
	});

	modal.addEventListener('hidden.bs.modal', () => {
		modal.remove();
	});

	bootstrapModal.show();
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
	// Ctrl/Cmd + R for refresh
	if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
		e.preventDefault();
		refreshBookings();
	}

	// Ctrl/Cmd + A for select all
	if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.target.tagName !== 'INPUT') {
		e.preventDefault();
		document.getElementById('selectAll').click();
	}

	// Escape to clear selection
	if (e.key === 'Escape') {
		clearSelection();
	}
});

// Handle visibility change for auto-refresh
document.addEventListener('visibilitychange', function() {
	if (!document.hidden && document.getElementById('autoRefresh').checked) {
		loadBookings(false);
	}
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
	if (autoRefreshInterval) {
		clearInterval(autoRefreshInterval);
	}
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>