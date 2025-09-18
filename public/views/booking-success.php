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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .success-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.2);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 60px 40px;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
        
        .success-title {
            font-size: 3rem;
            font-weight: 800;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-subtitle {
            font-size: 1.3rem;
            color: #6c757d;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            border-left: 5px solid #28a745;
        }
        
        .info-card h6 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            margin: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.4);
        }
        
        .btn-secondary {
            border-radius: 15px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.2rem;
            margin: 10px;
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
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffd700;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
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

    <div class="success-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="success-card pulse">
                        <!-- Success Icon -->
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        
                        <!-- Success Message -->
                        <h1 class="success-title">Booking Confirmed!</h1>
                        <p class="success-subtitle">
                            Your radio slot has been successfully booked and confirmed.
                        </p>
                        
                        <!-- Information Cards -->
                        <div class="info-card">
                            <h6><i class="fas fa-envelope me-2"></i>Email Confirmation</h6>
                            <p class="mb-0">
                                A confirmation email has been sent to your registered email address with all the details.
                            </p>
                        </div>
                        
                        <div class="info-card">
                            <h6><i class="fas fa-clock me-2"></i>What's Next?</h6>
                            <p class="mb-0">
                                Our team will contact you within 24 hours with detailed instructions for your radio slot.
                            </p>
                        </div>
                        
                        <div class="info-card">
                            <h6><i class="fas fa-headphones me-2"></i>Need Help?</h6>
                            <p class="mb-0">
                                If you have any questions, please contact our support team at 
                                <strong>support@zaaradio.com</strong> or call <strong>(555) 123-4567</strong>.
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <a href="/book" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Book Another Slot
                            </a>
                            <a href="/" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confetti Animation -->
    <div id="confetti-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Create confetti animation
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            const colors = ['#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                container.appendChild(confetti);
                
                // Remove confetti after animation
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 5000);
            }
        }
        
        // Start confetti animation
        document.addEventListener('DOMContentLoaded', function() {
            createConfetti();
            
            // Create more confetti every 2 seconds for 10 seconds
            let confettiInterval = setInterval(createConfetti, 2000);
            setTimeout(() => {
                clearInterval(confettiInterval);
            }, 10000);
        });
    </script>
</body>
</html>