<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .summary-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .summary-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .hero-section {
            text-align: center;
            padding: 60px 0 40px;
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .status-badge {
            font-size: 1.1rem;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .status-confirmed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
        
        .info-card h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
            font-weight: 500;
        }
        
        .price-highlight {
            font-size: 2rem;
            font-weight: 800;
            color: #28a745;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            border-radius: 15px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            font-size: 1.1rem;
            padding: 20px;
        }
        
        .navbar {
            background: rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .nav-link {
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
        }
        
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #667eea;
        }
        
        .timeline-item.completed::before {
            background: #28a745;
            box-shadow: 0 0 0 3px #28a745;
        }
        
        .timeline-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-radio me-2"></i>
                <strong>Zaa Radio</strong>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link" href="/book">Book Another Slot</a>
            </div>
        </div>
    </nav>

    <div class="summary-container">
        <div class="hero-section">
            <div class="container">
                <h1><i class="fas fa-check-circle me-3"></i>Booking Summary</h1>
                <p>Your radio slot booking has been submitted successfully</p>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if (Session::hasFlash('success')): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars(Session::getFlash('success')) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Session::hasFlash('error')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars(Session::getFlash('error')) ?>
                        </div>
                    <?php endif; ?>

                    <div class="summary-card p-5">
                        <!-- Status Badge -->
                        <div class="text-center mb-4">
                            <span class="status-badge status-<?= $booking['status'] ?>">
                                <i class="fas fa-clock me-2"></i>
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </div>

                        <!-- Booking Details -->
                        <div class="info-card">
                            <h6><i class="fas fa-calendar-alt me-2"></i>Booking Details</h6>
                            <div class="info-row">
                                <span class="info-label">Booking ID</span>
                                <span class="info-value">#<?= $booking['id'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date</span>
                                <span class="info-value"><?= date('l, F j, Y', strtotime($booking['date'])) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Time Slot</span>
                                <span class="info-value">
                                    <?= date('g:i A', strtotime($booking['start_time'])) ?> - 
                                    <?= date('g:i A', strtotime($booking['end_time'])) ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Station</span>
                                <span class="info-value"><?= htmlspecialchars($booking['station_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Amount</span>
                                <span class="info-value price-highlight">$<?= number_format($booking['total_amount'], 2) ?></span>
                            </div>
                        </div>

                        <!-- Advertiser Details -->
                        <div class="info-card">
                            <h6><i class="fas fa-user me-2"></i>Advertiser Information</h6>
                            <div class="info-row">
                                <span class="info-label">Name</span>
                                <span class="info-value"><?= htmlspecialchars($booking['advertiser_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?= htmlspecialchars($booking['advertiser_email']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?= htmlspecialchars($booking['advertiser_phone']) ?></span>
                            </div>
                            <?php if (!empty($booking['company_name'])): ?>
                            <div class="info-row">
                                <span class="info-label">Company</span>
                                <span class="info-value"><?= htmlspecialchars($booking['company_name']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Advertisement Message -->
                        <div class="info-card">
                            <h6><i class="fas fa-comment me-2"></i>Advertisement Message</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars($booking['message'])) ?></p>
                        </div>

                        <!-- Next Steps -->
                        <div class="info-card">
                            <h6><i class="fas fa-list-check me-2"></i>Next Steps</h6>
                            <div class="timeline">
                                <div class="timeline-item completed">
                                    <div class="timeline-content">
                                        <h6 class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Booking Submitted</h6>
                                        <p class="mb-0">Your booking request has been submitted and is awaiting approval.</p>
                                    </div>
                                </div>
                                <div class="timeline-item <?= $booking['status'] === 'confirmed' ? 'completed' : '' ?>">
                                    <div class="timeline-content">
                                        <h6 class="mb-2">
                                            <i class="fas fa-<?= $booking['status'] === 'confirmed' ? 'check-circle text-success' : 'clock' ?> me-2"></i>
                                            Station Manager Review
                                        </h6>
                                        <p class="mb-0">Our station manager will review your booking and send you a confirmation email.</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <h6 class="mb-2"><i class="fas fa-clock me-2"></i>Confirmation</h6>
                                        <p class="mb-0">Once approved, you'll receive detailed instructions for your radio slot.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="text-center mt-4">
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" action="/booking/<?= $booking['id'] ?>/confirm" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="/book" class="btn btn-secondary">
                                <i class="fas fa-plus me-2"></i>Book Another Slot
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>