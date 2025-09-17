<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaa Radio - Advertisement Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .cta-button {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/book">Book a Slot</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 fw-bold mb-4">Book Your Radio Advertisement</h1>
                    <p class="lead mb-4">Reach thousands of listeners with Zaa Radio's premium advertising slots. Easy booking, instant confirmation, and maximum impact.</p>
                    <a href="/book" class="btn btn-light cta-button">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Book a Slot Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold">How It Works</h2>
                    <p class="lead text-muted">Simple steps to get your message on air</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                            <h5 class="card-title">1. Choose Your Slot</h5>
                            <p class="card-text">Browse available time slots on our interactive calendar and select the perfect time for your advertisement.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-edit fa-lg"></i>
                            </div>
                            <h5 class="card-title">2. Fill Details</h5>
                            <p class="card-text">Provide your contact information and advertisement details. We'll handle the rest.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-broadcast-tower fa-lg"></i>
                            </div>
                            <h5 class="card-title">3. Go Live</h5>
                            <p class="card-text">Your advertisement will be broadcasted at the scheduled time to our thousands of listeners.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-4">Why Choose Zaa Radio?</h3>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Wide Reach</h6>
                            <p class="text-muted mb-0">Connect with thousands of listeners across the region</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Easy Booking</h6>
                            <p class="text-muted mb-0">Simple online booking system with instant confirmation</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Flexible Pricing</h6>
                            <p class="text-muted mb-0">Competitive rates for all time slots and durations</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Professional Service</h6>
                            <p class="text-muted mb-0">Dedicated support team to ensure your success</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white p-4 rounded shadow">
                        <h4 class="fw-bold mb-3">Ready to Get Started?</h4>
                        <p class="text-muted mb-4">Join hundreds of businesses who trust Zaa Radio for their advertising needs.</p>
                        <a href="/book" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-rocket me-2"></i>
                            Start Booking Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-radio me-2"></i>Zaa Radio</h5>
                    <p class="text-muted">Your trusted partner in radio advertising</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
