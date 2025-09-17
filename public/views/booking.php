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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .calendar-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
                <a class="nav-link" href="/login">Login</a>
            </div>
        </div>
    </nav>

    <div class="booking-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <h1 class="display-4 fw-bold text-primary">Book Your Radio Slot</h1>
                        <p class="lead text-muted">Select your preferred time slot and fill in your details</p>
                    </div>

                    <div class="calendar-card p-4">
                        <div id="calendar"></div>
                    </div>

                    <!-- Booking Form Modal -->
                    <div class="modal fade" id="bookingModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Book Radio Slot</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="bookingForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="advertiserName" class="form-label">Full Name *</label>
                                                    <input type="text" class="form-control" id="advertiserName" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="advertiserEmail" class="form-label">Email *</label>
                                                    <input type="email" class="form-control" id="advertiserEmail" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="advertiserPhone" class="form-label">Phone *</label>
                                                    <input type="tel" class="form-control" id="advertiserPhone" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="companyName" class="form-label">Company</label>
                                                    <input type="text" class="form-control" id="companyName">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="advertisementMessage" class="form-label">Advertisement Message *</label>
                                            <textarea class="form-control" id="advertisementMessage" rows="3" required placeholder="Describe your advertisement content..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="selectedSlot" class="form-label">Selected Time Slot</label>
                                            <input type="text" class="form-control" id="selectedSlot" readonly>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="submitBooking()">Book Slot</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/api/slots',
                dateClick: function(info) {
                    // For now, show a placeholder message
                    alert('Slot booking for ' + info.dateStr + ' - This will be implemented in the next steps!');
                },
                eventClick: function(info) {
                    // Show booking form for available slots
                    if (info.event.extendedProps.status === 'available') {
                        document.getElementById('selectedSlot').value = info.event.title + ' - ' + info.event.start.toLocaleString();
                        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                        bookingModal.show();
                    }
                }
            });
            calendar.render();
        });

        function submitBooking() {
            // Placeholder for booking submission
            alert('Booking submission will be implemented in the next steps!');
        }
    </script>
</body>
</html>
