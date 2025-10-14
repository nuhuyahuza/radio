<?php
use App\Utils\Session;
$pageTitle = 'My Bookings';
$currentPage = 'bookings';
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">My Bookings</h2>
            <a href="/book" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Book New Slot
            </a>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-clipboard-list me-2"></i>
            All Bookings
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date & Time</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold">#<?= $booking['id'] ?></span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?= date('M j, Y', strtotime($booking['date'])) ?></div>
                                        <small class="text-muted">
                                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                            <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $start = new DateTime($booking['start_time']);
                                    $end = new DateTime($booking['end_time']);
                                    $duration = $start->diff($end);
                                    echo $duration->format('%h') . 'h ' . $duration->format('%i') . 'm';
                                    ?>
                                </td>
                                <td class="fw-bold text-success">
                                    GH₵<?= number_format($booking['total_amount'], 2) ?>
                                </td>
                                <td>
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
                                    <span class="badge <?= $statusClass ?>">
                                        <i class="<?= $statusIcon ?> me-1"></i>
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/advertiser/bookings/<?= $booking['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-calendar-plus fa-3x mb-3 text-muted"></i>
                                <div class="fs-5 mb-2">No bookings yet</div>
                                <p class="text-muted mb-3">Start by booking your first radio slot!</p>
                                <a href="/book" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Book Your First Slot
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<?php if (!empty($bookings)): ?>
<div class="row mt-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stats-number"><?= count($bookings) ?></div>
            <div class="stats-label">Total Bookings</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number">
                <?= count(array_filter($bookings, function($b) { return $b['status'] === 'approved'; })) ?>
            </div>
            <div class="stats-label">Approved</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number">
                <?= count(array_filter($bookings, function($b) { return $b['status'] === 'pending'; })) ?>
            </div>
            <div class="stats-label">Pending</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stats-number">
                GH₵<?= number_format(array_sum(array_column($bookings, 'total_amount')), 0) ?>
            </div>
            <div class="stats-label">Total Spent</div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/advertiser-dashboard.php';
?>
