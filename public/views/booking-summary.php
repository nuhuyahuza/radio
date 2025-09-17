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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .summary-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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

    <div class="summary-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="summary-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold">Booking Summary</h2>
                            <p class="text-muted">Review your booking details before confirmation</p>
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

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Booking Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Booking ID:</strong></td>
                                        <td>#<?= $booking['id'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-warning"><?= ucfirst($booking['status']) ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td><?= date('F j, Y', strtotime($booking['date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Time:</strong></td>
                                        <td><?= date('g:i A', strtotime($booking['start_time'])) ?> - <?= date('g:i A', strtotime($booking['end_time'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Station:</strong></td>
                                        <td><?= htmlspecialchars($booking['station_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td class="fw-bold text-success">$<?= number_format($booking['total_amount'], 2) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Advertiser Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td><?= htmlspecialchars($booking['advertiser_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?= htmlspecialchars($booking['advertiser_email']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td><?= htmlspecialchars($booking['advertiser_phone']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Company:</strong></td>
                                        <td><?= htmlspecialchars($booking['advertiser_company'] ?: 'N/A') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">Advertisement Message</h5>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0"><?= nl2br(htmlspecialchars($booking['message'])) ?></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">Next Steps</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>What happens next?</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Your booking is currently pending approval</li>
                                    <li>Our station manager will review your request</li>
                                    <li>You'll receive an email notification once approved</li>
                                    <li>Payment will be processed upon approval</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <form method="POST" action="/booking-confirm/<?= $booking['id'] ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <button type="submit" class="btn btn-success btn-lg me-3">
                                    <i class="fas fa-check me-2"></i>
                                    Confirm Booking
                                </button>
                            </form>
                            <a href="/book" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Book Another Slot
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
