<?php
use App\Utils\Session;
$pageTitle = 'Booking Details';
$currentPage = 'bookings';
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Booking Details</h2>
            <a href="/advertiser/bookings" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Bookings
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Booking Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Booking Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Booking ID</label>
                        <p class="form-control-plaintext">#<?= $booking['id'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p class="form-control-plaintext">
                            <?php
                            $statusClass = '';
                            $statusIcon = '';
                            switch ($booking['status']) {
                                case 'pending':
                                    $statusClass = 'badge-warning';
                                    $statusIcon = 'fas fa-clock';
                                    break;
                                case 'approved':
                                    $statusClass = 'badge-success';
                                    $statusIcon = 'fas fa-check-circle';
                                    break;
                                case 'rejected':
                                    $statusClass = 'badge-danger';
                                    $statusIcon = 'fas fa-times-circle';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'badge-secondary';
                                    $statusIcon = 'fas fa-ban';
                                    break;
                                default:
                                    $statusClass = 'badge-info';
                                    $statusIcon = 'fas fa-info-circle';
                            }
                            ?>
                            <span class="badge <?= $statusClass ?> fs-6">
                                <i class="<?= $statusIcon ?> me-1"></i>
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <p class="form-control-plaintext"><?= date('l, F j, Y', strtotime($booking['date'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Time</label>
                        <p class="form-control-plaintext">
                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                            <?= date('g:i A', strtotime($booking['end_time'])) ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Duration</label>
                        <p class="form-control-plaintext">
                            <?php
                            $start = new DateTime($booking['start_time']);
                            $end = new DateTime($booking['end_time']);
                            $duration = $start->diff($end);
                            echo $duration->format('%h') . ' hours ' . $duration->format('%i') . ' minutes';
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Amount</label>
                        <p class="form-control-plaintext fw-bold text-success fs-5">
                            GH₵<?= number_format($booking['total_amount'], 2) ?>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Ad Content</label>
                        <div class="border rounded p-3 bg-light">
                            <?= nl2br(htmlspecialchars($booking['ad_content'] ?? 'No content provided')) ?>
                        </div>
                    </div>
                    <?php if (!empty($booking['notes'])): ?>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <div class="border rounded p-3 bg-light">
                            <?= nl2br(htmlspecialchars($booking['notes'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Timeline -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Booking Timeline
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Booking Created</h6>
                            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($booking['created_at'])) ?></small>
                        </div>
                    </div>
                    
                    <?php if ($booking['status'] === 'approved'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Booking Approved</h6>
                            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($booking['updated_at'])) ?></small>
                        </div>
                    </div>
                    <?php elseif ($booking['status'] === 'rejected'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Booking Rejected</h6>
                            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($booking['updated_at'])) ?></small>
                        </div>
                    </div>
                    <?php elseif ($booking['status'] === 'cancelled'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Booking Cancelled</h6>
                            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($booking['updated_at'])) ?></small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Pending Review</h6>
                            <small class="text-muted">Awaiting station manager approval</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/book" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Book Another Slot
                    </a>
                    <a href="/advertiser/bookings" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>
                        View All Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/advertiser-dashboard.php';
?>
