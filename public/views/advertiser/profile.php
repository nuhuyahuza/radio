<?php
use App\Utils\Session;
$pageTitle = 'My Profile';
$currentPage = 'profile';
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">My Profile</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fas fa-edit me-2"></i>
                Edit Profile
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>
                    Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['name']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['email']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['phone'] ?? 'Not provided') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($currentUser['company'] ?? 'Not provided') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Account Status</label>
                        <p class="form-control-plaintext">
                            <?php if ($currentUser['is_active']): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Inactive
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Member Since</label>
                        <p class="form-control-plaintext"><?= date('M j, Y', strtotime($currentUser['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Summary -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Account Summary
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <h5 class="mb-1"><?= htmlspecialchars($currentUser['name']) ?></h5>
                <p class="text-muted mb-3"><?= htmlspecialchars($currentUser['email']) ?></p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="fw-bold fs-4"><?= $stats['total_bookings'] ?? 0 ?></div>
                        <small class="text-muted">Total Bookings</small>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-4">GH₵<?= number_format($stats['total_spent'] ?? 0, 0) ?></div>
                        <small class="text-muted">Total Spent</small>
                    </div>
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
                        Book New Slot
                    </a>
                    <a href="/advertiser/bookings" class="btn btn-outline-primary">
                        <i class="fas fa-clipboard-list me-2"></i>
                        View My Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Edit Profile
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/advertiser/profile/update" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($currentUser['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company" name="company" 
                               value="<?= htmlspecialchars($currentUser['company'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                        <div class="form-text">Leave blank to keep current password</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/advertiser-dashboard.php';
?>
