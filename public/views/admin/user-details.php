<?php
/**
 * User Details View
 * Shows detailed information about a specific user
 */

use App\Utils\Session;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">User Details</h1>
                <div>
                    <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="/admin/users" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= Session::getFlash('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= Session::getFlash('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($user) && $user): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">User Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Name</h6>
                                        <p class="text-muted"><?= htmlspecialchars($user['name']) ?></p>
                                        
                                        <h6>Email</h6>
                                        <p class="text-muted">
                                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                                <?= htmlspecialchars($user['email']) ?>
                                            </a>
                                        </p>
                                        
                                        <h6>Role</h6>
                                        <p class="text-muted">
                                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'manager' ? 'warning' : 'info') ?>">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Phone</h6>
                                        <p class="text-muted">
                                            <?php if (!empty($user['phone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($user['phone']) ?>">
                                                    <?= htmlspecialchars($user['phone']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Not provided</span>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <h6>Company</h6>
                                        <p class="text-muted">
                                            <?= !empty($user['company']) ? htmlspecialchars($user['company']) : 'Not provided' ?>
                                        </p>
                                        
                                        <h6>Status</h6>
                                        <p class="text-muted">
                                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Account Activity</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Account Created</h6>
                                        <p class="text-muted"><?= date('F j, Y g:i A', strtotime($user['created_at'])) ?></p>
                                        
                                        <h6>Last Updated</h6>
                                        <p class="text-muted">
                                            <?= $user['updated_at'] !== $user['created_at'] ? date('F j, Y g:i A', strtotime($user['updated_at'])) : 'Never' ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Last Login</h6>
                                        <p class="text-muted">
                                            <?= $user['last_login_at'] ? date('F j, Y g:i A', strtotime($user['last_login_at'])) : 'Never' ?>
                                        </p>
                                        
                                        <h6>Email Verified</h6>
                                        <p class="text-muted">
                                            <?= $user['email_verified_at'] ? date('F j, Y g:i A', strtotime($user['email_verified_at'])) : 'Not verified' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit User
                                    </a>
                                    
                                    <form method="POST" action="/admin/users/toggle-status/<?= $user['id'] ?>" class="d-grid">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <button type="submit" class="btn btn-<?= $user['is_active'] ? 'warning' : 'success' ?>" 
                                                onclick="return confirm('Are you sure you want to <?= $user['is_active'] ? 'deactivate' : 'activate' ?> this user?')">
                                            <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                            <?= $user['is_active'] ? 'Deactivate User' : 'Activate User' ?>
                                        </button>
                                    </form>
                                    
                                    <?php if ($user['role'] !== 'admin' || $this->userModel->countUsersByRole('admin') > 1): ?>
                                        <form method="POST" action="/admin/users/delete/<?= $user['id'] ?>" class="d-grid">
                                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i> Delete User
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Role Permissions</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i> Full system access</li>
                                        <li><i class="fas fa-check text-success me-2"></i> User management</li>
                                        <li><i class="fas fa-check text-success me-2"></i> System settings</li>
                                        <li><i class="fas fa-check text-success me-2"></i> All reports and analytics</li>
                                    </ul>
                                <?php elseif ($user['role'] === 'manager'): ?>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i> Manage radio slots</li>
                                        <li><i class="fas fa-check text-success me-2"></i> Approve/reject bookings</li>
                                        <li><i class="fas fa-check text-success me-2"></i> View reports</li>
                                        <li><i class="fas fa-times text-muted me-2"></i> User management</li>
                                    </ul>
                                <?php else: ?>
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i> Book radio slots</li>
                                        <li><i class="fas fa-check text-success me-2"></i> View own bookings</li>
                                        <li><i class="fas fa-times text-muted me-2"></i> Manage slots</li>
                                        <li><i class="fas fa-times text-muted me-2"></i> Approve bookings</li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    User not found or you don't have permission to view it.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

