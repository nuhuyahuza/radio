<?php
$pageTitle = 'Bookings Management';
$currentPage = 'bookings';
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Bookings Management</h2>
            <div class="d-flex gap-2">
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

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" onchange="filterBookings()">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <select class="form-select" id="dateFilter" onchange="filterBookings()">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Advertiser</label>
                        <input type="text" class="form-control" id="advertiserFilter" placeholder="Search advertiser..." onkeyup="filterBookings()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount Range</label>
                        <select class="form-select" id="amountFilter" onchange="filterBookings()">
                            <option value="">All Amounts</option>
                            <option value="0-100">$0 - $100</option>
                            <option value="100-500">$100 - $500</option>
                            <option value="500-1000">$500 - $1,000</option>
                            <option value="1000+">$1,000+</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="row">
    <div class="col-12">
        <div class="card card-hover">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2 text-gradient"></i>
                    Bookings List
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 fade-in" id="bookingsTable">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>ID</th>
                                <th>Advertiser</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <tr id="loadingRow">
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="spinner-border text-primary me-3" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="text-muted">Loading bookings...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkApprove()" id="bulkApproveBtn" disabled>
                            <i class="fas fa-check me-1"></i>Approve Selected
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkReject()" id="bulkRejectBtn" disabled>
                            <i class="fas fa-times me-1"></i>Reject Selected
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">Showing <span id="showingCount">0</span> of <span id="totalCount">0</span> bookings</span>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm" onclick="changePage(-1)" id="prevBtn" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="btn btn-outline-secondary btn-sm" id="pageInfo">Page 1 of 1</span>
                            <button class="btn btn-outline-secondary btn-sm" onclick="changePage(1)" id="nextBtn" disabled>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <!-- Booking details will be loaded here -->
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

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage">Are you sure you want to perform this action on the selected bookings?</p>
                <div class="form-group">
                    <label for="bulkActionReason" class="form-label">Reason (Optional)</label>
                    <textarea class="form-control" id="bulkActionReason" rows="3" placeholder="Enter reason for this action..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmBulkAction()" id="confirmBulkActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;
let selectedBookings = [];
let currentFilters = {};

// Load bookings on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBookings();
});

// Load bookings from server
function loadBookings() {
    const params = new URLSearchParams({
        page: currentPage,
        ...currentFilters
    });
    
    fetch(`/admin/bookings/data?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderBookings(data.bookings);
                updatePagination(data.pagination);
            } else {
                showAlert('Error loading bookings: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading bookings', 'danger');
        });
}

// Render bookings in table
function renderBookings(bookings) {
    const tbody = document.getElementById('bookingsTableBody');
    tbody.innerHTML = '';
    
    if (bookings.length === 0) {
        tbody.innerHTML = `
            <tr class="fade-in">
                <td colspan="9" class="text-center text-muted py-5">
                    <div class="fade-in">
                        <i class="fas fa-inbox fa-3x mb-3 text-gradient"></i>
                        <div class="h5">No bookings found</div>
                        <small>Try adjusting your filters or check back later</small>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    bookings.forEach((booking, index) => {
        const row = document.createElement('tr');
        row.className = 'fade-in';
        row.style.animationDelay = `${index * 0.1}s`;
        
        row.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input booking-checkbox" value="${booking.id}" onchange="toggleBookingSelection(${booking.id})">
            </td>
            <td>
                <span class="fw-bold text-gradient">#${booking.id}</span>
            </td>
            <td>
                <div>
                    <div class="fw-bold">${booking.advertiser_name}</div>
                    <small class="text-muted">${booking.advertiser_email}</small>
                </div>
            </td>
            <td>
                <div>
                    <div class="fw-bold">${formatDate(booking.date)}</div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}
                    </small>
                </div>
            </td>
            <td>
                <span class="badge bg-info">${booking.duration} min</span>
            </td>
            <td class="fw-bold text-success">$${formatAmount(booking.total_amount)}</td>
            <td>${getStatusBadge(booking.status)}</td>
            <td>
                <small class="text-muted">${formatDateTime(booking.created_at)}</small>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewBooking(${booking.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${booking.status === 'pending' ? `
                        <button class="btn btn-sm btn-success btn-pulse" onclick="quickApprove(${booking.id})" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="quickReject(${booking.id})" title="Reject">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Filter bookings
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
    loadBookings();
}

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.booking-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        toggleBookingSelection(parseInt(checkbox.value));
    });
}

// Toggle individual booking selection
function toggleBookingSelection(bookingId) {
    const index = selectedBookings.indexOf(bookingId);
    if (index > -1) {
        selectedBookings.splice(index, 1);
    } else {
        selectedBookings.push(bookingId);
    }
    
    updateBulkActionButtons();
}

// Update bulk action buttons
function updateBulkActionButtons() {
    const hasSelection = selectedBookings.length > 0;
    document.getElementById('bulkApproveBtn').disabled = !hasSelection;
    document.getElementById('bulkRejectBtn').disabled = !hasSelection;
}

// Bulk approve
function bulkApprove() {
    if (selectedBookings.length === 0) return;
    
    document.getElementById('bulkActionTitle').textContent = 'Approve Bookings';
    document.getElementById('bulkActionMessage').textContent = `Are you sure you want to approve ${selectedBookings.length} selected booking(s)?`;
    document.getElementById('confirmBulkActionBtn').className = 'btn btn-success';
    document.getElementById('confirmBulkActionBtn').onclick = () => confirmBulkAction('approve');
    
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}

// Bulk reject
function bulkReject() {
    if (selectedBookings.length === 0) return;
    
    document.getElementById('bulkActionTitle').textContent = 'Reject Bookings';
    document.getElementById('bulkActionMessage').textContent = `Are you sure you want to reject ${selectedBookings.length} selected booking(s)?`;
    document.getElementById('confirmBulkActionBtn').className = 'btn btn-danger';
    document.getElementById('confirmBulkActionBtn').onclick = () => confirmBulkAction('reject');
    
    new bootstrap.Modal(document.getElementById('bulkActionModal')).show();
}

// Confirm bulk action
function confirmBulkAction(action) {
    const reason = document.getElementById('bulkActionReason').value;
    
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
            showAlert(`${action.charAt(0).toUpperCase() + action.slice(1)}ed ${selectedBookings.length} booking(s) successfully`, 'success');
            selectedBookings = [];
            loadBookings();
            bootstrap.Modal.getInstance(document.getElementById('bulkActionModal')).hide();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error performing bulk action', 'danger');
    });
}

// View booking details
function viewBooking(bookingId) {
    fetch(`/admin/bookings/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('bookingDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('bookingDetailsModal')).show();
            } else {
                showAlert('Error loading booking details: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading booking details', 'danger');
        });
}

// Quick approve
function quickApprove(bookingId) {
    if (confirm('Are you sure you want to approve this booking?')) {
        updateBookingStatus(bookingId, 'approved');
    }
}

// Quick reject
function quickReject(bookingId) {
    if (confirm('Are you sure you want to reject this booking?')) {
        updateBookingStatus(bookingId, 'rejected');
    }
}

// Update booking status
function updateBookingStatus(bookingId, status) {
    fetch(`/admin/bookings/${bookingId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken()
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Booking ${status} successfully`, 'success');
            loadBookings();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating booking status', 'danger');
    });
}

// Export bookings
function exportBookings() {
    const params = new URLSearchParams(currentFilters);
    window.open(`/admin/bookings/export?${params}`, '_blank');
}

// Refresh bookings
function refreshBookings() {
    loadBookings();
}

// Change page
function changePage(direction) {
    const newPage = currentPage + direction;
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        loadBookings();
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
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}

function formatAmount(amount) {
    return parseFloat(amount).toFixed(2);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning"><span class="status-indicator pending"></span>Pending</span>',
        'approved': '<span class="badge bg-success"><span class="status-indicator approved"></span>Approved</span>',
        'rejected': '<span class="badge bg-danger"><span class="status-indicator rejected"></span>Rejected</span>',
        'cancelled': '<span class="badge bg-secondary"><span class="status-indicator cancelled"></span>Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-info"><span class="status-indicator"></span>' + status + '</span>';
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>
