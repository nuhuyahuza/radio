<?php
/**
 * Booking Details View
 * Shows detailed information about a specific booking
 */

use App\Utils\Session;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Booking Details</h1>
                <a href="/manager/bookings" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Bookings
                </a>
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

            <?php if (isset($booking) && $booking): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Booking Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Booking ID</h6>
                                        <p class="text-muted">#<?= htmlspecialchars($booking['id']) ?></p>
                                        
                                        <h6>Status</h6>
                                        <span class="badge bg-<?= $booking['status'] === 'approved' ? 'success' : ($booking['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                        
                                        <h6>Date</h6>
                                        <p class="text-muted"><?= date('F j, Y', strtotime($booking['date'])) ?></p>
                                        
                                        <h6>Time Slot</h6>
                                        <p class="text-muted">
                                            <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                            <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Total Amount</h6>
                                        <p class="text-muted">$<?= number_format($booking['total_amount'], 2) ?></p>
                                        
                                        <h6>Created At</h6>
                                        <p class="text-muted"><?= date('F j, Y g:i A', strtotime($booking['created_at'])) ?></p>
                                        
                                        <?php if ($booking['updated_at'] !== $booking['created_at']): ?>
                                            <h6>Last Updated</h6>
                                            <p class="text-muted"><?= date('F j, Y g:i A', strtotime($booking['updated_at'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6>Advertisement Message</h6>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($booking['advertisement_message'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Advertiser Information</h5>
                            </div>
                            <div class="card-body">
                                <h6>Name</h6>
                                <p class="text-muted"><?= htmlspecialchars($booking['advertiser_name']) ?></p>
                                
                                <h6>Email</h6>
                                <p class="text-muted">
                                    <a href="mailto:<?= htmlspecialchars($booking['advertiser_email']) ?>">
                                        <?= htmlspecialchars($booking['advertiser_email']) ?>
                                    </a>
                                </p>
                                
                                <h6>Phone</h6>
                                <p class="text-muted">
                                    <a href="tel:<?= htmlspecialchars($booking['advertiser_phone']) ?>">
                                        <?= htmlspecialchars($booking['advertiser_phone']) ?>
                                    </a>
                                </p>
                                
                                <?php if (!empty($booking['company_name'])): ?>
                                    <h6>Company</h6>
                                    <p class="text-muted"><?= htmlspecialchars($booking['company_name']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($booking['status'] === 'pending'): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/manager/bookings/approve/<?= $booking['id'] ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <button type="submit" class="btn btn-success btn-sm me-2" 
                                                onclick="return confirm('Are you sure you want to approve this booking?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="/manager/bookings/reject/<?= $booking['id'] ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to reject this booking?')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Booking not found or you don't have permission to view it.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

