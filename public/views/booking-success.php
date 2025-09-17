<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="success-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="success-card p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                            <h1 class="display-4 fw-bold text-success">Booking Confirmed!</h1>
                            <p class="lead text-muted">Your radio advertisement slot has been successfully booked.</p>
                        </div>

                        <?php if (Session::hasFlash('success')): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars(Session::getFlash('success')) ?>
                            </div>
                        <?php endif; ?>

                        <div class="row text-start mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold">What happens next?</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Booking submitted for approval</li>
                                    <li><i class="fas fa-envelope text-primary me-2"></i>Confirmation email sent</li>
                                    <li><i class="fas fa-clock text-warning me-2"></i>Station manager review</li>
                                    <li><i class="fas fa-broadcast-tower text-info me-2"></i>Ready for broadcast</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Important Notes</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-info-circle text-info me-2"></i>Check your email for details</li>
                                    <li><i class="fas fa-phone text-primary me-2"></i>Contact us if you have questions</li>
                                    <li><i class="fas fa-calendar text-success me-2"></i>Mark your broadcast date</li>
                                    <li><i class="fas fa-file-alt text-warning me-2"></i>Keep your booking reference</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Need Help?</strong> Contact our support team at 
                            <a href="mailto:support@zaaradio.com" class="text-decoration-none">support@zaaradio.com</a> 
                            or call <strong>+1-555-ZAA-RADIO</strong>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="/book" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Book Another Slot
                            </a>
                            <a href="/" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Back to Home
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
