<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Slot - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        .booking-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .booking-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .calendar-card {
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
            padding: 80px 0 60px;
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .fc {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .fc-toolbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
            margin: 0;
        }
        
        .fc-toolbar-title {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .fc-button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .fc-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .fc-button:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.5);
        }
        
        .fc-daygrid-day {
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .fc-daygrid-day:hover {
            background: #f8f9fa;
        }
        
        .fc-daygrid-day.fc-day-past {
            background: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
        }
        
        .fc-daygrid-day.fc-day-past .fc-daygrid-day-number {
            color: #adb5bd;
        }
        
        .fc-daygrid-day.fc-day-today {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            color: white;
            font-weight: 700;
        }
        
        .fc-event {
            border-radius: 8px;
            border: none;
            padding: 4px 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .slot-available {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .slot-booked {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }
        
        .slot-pending {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 25px 30px;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .slot-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
        
        .slot-info h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner-border {
            color: #667eea;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
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
                <a class="nav-link" href="/login">Login</a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="hero-section">
            <div class="container">
                <h1><i class="fas fa-calendar-alt me-3"></i>Book Your Radio Slot</h1>
                <p>Choose your perfect time slot and reach thousands of listeners</p>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="calendar-card p-4">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Form Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-microphone me-2"></i>Book Radio Slot
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (Session::hasFlash('error')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars(Session::getFlash('error')) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="slot-info">
                        <h6><i class="fas fa-clock me-2"></i>Selected Time Slot</h6>
                        <div id="slotDetails"></div>
                    </div>
                    
                    <form id="bookingForm" method="POST" action="/book">
                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                        <input type="hidden" name="slot_id" id="slotId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="advertiserName" class="form-label">
                                        <i class="fas fa-user me-2"></i>Full Name *
                                    </label>
                                    <input type="text" class="form-control" id="advertiserName" name="advertiser_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="advertiserEmail" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email *
                                    </label>
                                    <input type="email" class="form-control" id="advertiserEmail" name="advertiser_email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="advertiserPhone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Phone *
                                    </label>
                                    <input type="tel" class="form-control" id="advertiserPhone" name="advertiser_phone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">
                                        <i class="fas fa-building me-2"></i>Company
                                    </label>
                                    <input type="text" class="form-control" id="companyName" name="company_name">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="advertisementMessage" class="form-label">
                                <i class="fas fa-comment me-2"></i>Advertisement Message *
                            </label>
                            <textarea class="form-control" id="advertisementMessage" name="advertisement_message" rows="4" required placeholder="Describe your advertisement content..."></textarea>
                        </div>
                    </form>
                    
                    <div class="loading" id="loading">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <p class="mt-2">Processing your booking...</p>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitBooking()">
                        <i class="fas fa-check me-2"></i>Book Slot
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        let calendar;
        let selectedSlot = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/api/slots',
                height: 'auto',
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                dateClick: function(info) {
                    const date = new Date(info.dateStr);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (date < today) {
                        showAlert('Cannot book slots for past dates.', 'warning');
                        return;
                    }
                    
                    // Find available slots for this date
                    const events = calendar.getEvents();
                    const dayEvents = events.filter(event => {
                        const eventDate = new Date(event.start);
                        return eventDate.toDateString() === date.toDateString() && 
                               event.extendedProps.status === 'available';
                    });
                    
                    if (dayEvents.length === 0) {
                        showAlert('No available slots for ' + info.dateStr, 'info');
                        return;
                    }
                    
                    // Show slots for this date
                    showSlotsForDate(info.dateStr, dayEvents);
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.status === 'available') {
                        selectedSlot = {
                            id: info.event.extendedProps.slotId,
                            title: info.event.title,
                            start: info.event.start,
                            end: info.event.end,
                            price: info.event.extendedProps.price || 0
                        };
                        
                        showBookingModal();
                    } else {
                        showAlert('This slot is not available for booking.', 'warning');
                    }
                },
                eventDidMount: function(info) {
                    // Add custom styling based on status
                    const status = info.event.extendedProps.status;
                    if (status === 'available') {
                        info.el.classList.add('slot-available');
                    } else if (status === 'booked') {
                        info.el.classList.add('slot-booked');
                    } else if (status === 'pending') {
                        info.el.classList.add('slot-pending');
                    }
                }
            });
            calendar.render();
        });

        function showSlotsForDate(dateStr, events) {
            let message = `Available slots for ${dateStr}:\n\n`;
            events.forEach(event => {
                const start = new Date(event.start);
                const end = new Date(event.end);
                message += `• ${start.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})} - ${end.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}\n`;
            });
            message += '\nClick on any available slot to book it.';
            showAlert(message, 'info');
        }

        function showBookingModal() {
            if (!selectedSlot) return;
            
            // Update slot details
            const start = new Date(selectedSlot.start);
            const end = new Date(selectedSlot.end);
            const slotDetails = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${selectedSlot.title}</strong><br>
                        <small class="text-muted">
                            ${start.toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="h5 text-primary mb-0">$${selectedSlot.price}</div>
                        <small class="text-muted">
                            ${start.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})} - 
                            ${end.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                        </small>
                    </div>
                </div>
            `;
            document.getElementById('slotDetails').innerHTML = slotDetails;
            document.getElementById('slotId').value = selectedSlot.id;
            
            // Clear form
            document.getElementById('bookingForm').reset();
            
            // Show modal
            const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();
        }

        function submitBooking() {
            const form = document.getElementById('bookingForm');
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            // Validate form
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                showAlert('Please fill in all required fields.', 'danger');
                return;
            }
            
            // Read XSRF-TOKEN cookie and use it as CSRF source
            const xsrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
            const xsrfToken = xsrfCookie ? decodeURIComponent(xsrfCookie.split('=')[1]) : '';
            const tokenInput = form.querySelector('input[name="csrf_token"]');
            if (tokenInput) {
                tokenInput.value = xsrfToken;
            }

            // Show loading
            document.getElementById('loading').style.display = 'block';
            form.style.display = 'none';

            // Submit form
            fetch('/book', {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': xsrfToken
                },
                credentials: 'same-origin',
                cache: 'no-store',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || '/booking-success';
                } else {
                    showAlert(data.message || 'Booking failed. Please try again.', 'danger');
                    document.getElementById('loading').style.display = 'none';
                    form.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.', 'danger');
                document.getElementById('loading').style.display = 'none';
                form.style.display = 'block';
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert at the top of the modal body
            const modalBody = document.querySelector('.modal-body');
            modalBody.insertBefore(alertDiv, modalBody.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>