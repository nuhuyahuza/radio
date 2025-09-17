<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bookings-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-radio fa-2x text-white"></i>
                        <h5 class="text-white mt-2">Zaa Radio</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager/slots">
                                <i class="fas fa-clock me-2"></i>
                                Manage Slots
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="/manager/bookings">
                                <i class="fas fa-calendar-check me-2"></i>
                                Approve Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager/reports">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Bookings</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown me-2">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>
                                Filter: <?= ucfirst($status) ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?status=all">All Bookings</a></li>
                                <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                                <li><a class="dropdown-item" href="?status=approved">Approved</a></li>
                                <li><a class="dropdown-item" href="?status=rejected">Rejected</a></li>
                                <li><a class="dropdown-item" href="?status=cancelled">Cancelled</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars(Session::getUser()['name'] ?? 'Manager') ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['total'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Total Bookings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['pending'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['approved'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Approved</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['rejected'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Rejected</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions for Pending Bookings -->
                <?php if (!empty($pendingBookings)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions - Pending Bookings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/manager/bookings/bulk-approve" id="bulkApproveForm">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllPending">
                                        <label class="form-check-label" for="selectAllPending">
                                            Select All Pending Bookings
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i>
                                        Approve Selected
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <form method="POST" action="/manager/bookings/bulk-reject" id="bulkRejectForm" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="reason" placeholder="Rejection reason (optional)">
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-2"></i>
                                        Reject Selected
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Bookings List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Bookings List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($bookings)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No bookings found</h5>
                                <p class="text-muted">There are no bookings matching your current filter.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>ID</th>
                                            <th>Advertiser</th>
                                            <th>Date & Time</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                <input type="checkbox" name="booking_ids[]" value="<?= $booking['id'] ?>" class="form-check-input booking-checkbox">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="/manager/bookings/<?= $booking['id'] ?>" class="text-decoration-none">
                                                    #<?= $booking['id'] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($booking['advertiser_name']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($booking['advertiser_email']) ?></small>
                                                    <?php if ($booking['advertiser_company']): ?>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($booking['advertiser_company']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= date('M j, Y', strtotime($booking['date'])) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                                        <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">$<?= number_format($booking['total_amount'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $booking['status'] === 'pending' ? 'warning' : ($booking['status'] === 'approved' ? 'success' : 'danger') ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M j, Y g:i A', strtotime($booking['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/manager/bookings/<?= $booking['id'] ?>" class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($booking['status'] === 'pending'): ?>
                                                    <form method="POST" action="/manager/bookings/approve/<?= $booking['id'] ?>" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                                        <button type="submit" class="btn btn-outline-success" title="Approve" onclick="return confirm('Approve this booking?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <button type="button" class="btn btn-outline-danger" title="Reject" onclick="showRejectModal(<?= $booking['id'] ?>)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (in_array($booking['status'], ['pending', 'approved'])): ?>
                                                    <button type="button" class="btn btn-outline-warning" title="Cancel" onclick="showCancelModal(<?= $booking['id'] ?>)">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rejectForm">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">Reason for rejection (optional)</label>
                            <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="Please provide a reason for rejecting this booking..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="cancelForm">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason for cancellation (optional)</label>
                            <textarea class="form-control" id="cancelReason" name="reason" rows="3" placeholder="Please provide a reason for cancelling this booking..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Cancel Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('selectAllPending').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Bulk form handling
        document.getElementById('bulkApproveForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.booking-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one booking to approve.');
                return;
            }
            
            if (!confirm(`Approve ${checkedBoxes.length} selected booking(s)?`)) {
                e.preventDefault();
            }
        });

        document.getElementById('bulkRejectForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.booking-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one booking to reject.');
                return;
            }
            
            if (!confirm(`Reject ${checkedBoxes.length} selected booking(s)?`)) {
                e.preventDefault();
            }
        });

        // Modal functions
        function showRejectModal(bookingId) {
            const form = document.getElementById('rejectForm');
            form.action = '/manager/bookings/reject/' + bookingId;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        function showCancelModal(bookingId) {
            const form = document.getElementById('cancelForm');
            form.action = '/manager/bookings/cancel/' + bookingId;
            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }
    </script>
</body>
</html>

