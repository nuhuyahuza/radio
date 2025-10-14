<?php
$pageTitle = 'Advertiser Dashboard';
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

<!-- Quick Book CTA -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body text-center py-5">
                <h3 class="mb-3">
                    <i class="fas fa-microphone me-2"></i>
                    Ready to Reach Thousands of Listeners?
                </h3>
                <p class="mb-4 fs-5">Book your radio slot today and get your message heard!</p>
                <a href="/book" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-calendar-plus me-2"></i>
                    Book a Slot Now
                </a>
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
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number"><?= $stats['approved_bookings'] ?? 0 ?></div>
            <div class="stats-label">Approved Bookings</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number"><?= $stats['pending_bookings'] ?? 0 ?></div>
            <div class="stats-label">Pending Bookings</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stats-number">GH₵<?= number_format($stats['total_spent'] ?? 0, 0) ?></div>
            <div class="stats-label">Total Spent</div>
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
                    My Recent Bookings
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                                <div class="fw-bold"><?= date('M j, Y', strtotime($booking['date'])) ?></div>
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
                                            <a href="/advertiser/bookings/<?= $booking['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                        <div>No bookings yet</div>
                                        <small>Start by booking your first slot!</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="/advertiser/bookings" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>
                    View All Bookings
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Profile -->
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
                    <a href="/book" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Book New Slot
                    </a>
                    <a href="/advertiser/bookings" class="btn btn-outline-primary">
                        <i class="fas fa-clipboard-list me-2"></i>
                        My Bookings
                    </a>
                    <a href="/advertiser/profile" class="btn btn-outline-secondary">
                        <i class="fas fa-user me-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-user me-2"></i>
                    Profile Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                    </div>
                    <h6 class="mb-1"><?= htmlspecialchars($currentUser['name']) ?></h6>
                    <small class="text-muted"><?= htmlspecialchars($currentUser['email']) ?></small>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="fw-bold fs-5"><?= $stats['total_bookings'] ?? 0 ?></div>
                        <small class="text-muted">Bookings</small>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-5">GH₵<?= number_format($stats['total_spent'] ?? 0, 0) ?></div>
                        <small class="text-muted">Spent</small>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="/advertiser/profile" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-edit me-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/advertiser-dashboard.php';
?>