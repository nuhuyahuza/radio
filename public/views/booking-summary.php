<?php
use App\Utils\Session;
$hasServerDraft = isset($booking) && empty($booking['id']);
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
        .summary-container { min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden; }
        .summary-card { background: rgba(255, 255, 255, 0.95); border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); position: relative; z-index: 1; }
        .info-card { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 25px; margin-bottom: 20px; border-left: 5px solid #667eea; }
        .price-highlight { font-size: 2rem; font-weight: 800; color: #28a745; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px; padding: 15px 40px; font-weight: 700; font-size: 1.2rem; }
        .btn-secondary { border-radius: 15px; padding: 15px 40px; font-weight: 700; font-size: 1.2rem; }
        .status-badge { font-size: 1.1rem; padding: 12px 24px; border-radius: 50px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .status-draft { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; }
        .status-confirmed { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(0,0,0,0.1)">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-radio me-2"></i><strong>Zaa Radio</strong></a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/book">Book</a>
            </div>
        </div>
    </nav>

    <div class="summary-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="summary-card p-5">
                        <div class="text-center mb-4">
                            <span class="status-badge status-draft">
                                <i class="fas fa-clock me-2"></i>
                                Draft
                            </span>
                        </div>

                        <div id="draftContent" style="display:none">
                            <div class="info-card">
                                <h6><i class="fas fa-calendar-alt me-2"></i>Booking Details</h6>
                                <div class="d-flex justify-content-between"><span>Date</span><span id="draftDate"></span></div>
                                <div class="d-flex justify-content-between"><span>Time</span><span id="draftTime"></span></div>
                                <div class="d-flex justify-content-between"><span>Station</span><span id="draftStation">Zaa Radio</span></div>
                                <div class="d-flex justify-content-between"><span>Total</span><span class="price-highlight" id="draftTotal"></span></div>
                            </div>

                            <div class="info-card">
                                <h6><i class="fas fa-user me-2"></i>Advertiser</h6>
                                <div class="d-flex justify-content-between"><span>Name</span><span id="draftName"></span></div>
                                <div class="d-flex justify-content-between"><span>Email</span><span id="draftEmail"></span></div>
                                <div class="d-flex justify-content-between"><span>Phone</span><span id="draftPhone"></span></div>
                                <div class="d-flex justify-content-between"><span>Company</span><span id="draftCompany"></span></div>
                            </div>

                            <div class="info-card">
                                <h6><i class="fas fa-comment me-2"></i>Advertisement Message</h6>
                                <p class="mb-0" id="draftMessage"></p>
                            </div>

                            <div class="text-center mt-4">
                                <form id="confirmForm" method="POST" action="/booking/confirm" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                    <input type="hidden" name="draft_payload" id="draftPayload">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
                                    </button>
                                </form>
                                <form method="POST" action="/booking/cancel" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div id="noDraft" class="text-center text-muted" style="display:none">
                            <i class="fas fa-info-circle me-2"></i>No booking draft found.
                            <div class="mt-3"><a href="/book" class="btn btn-secondary">Back to Booking</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hydrate from sessionStorage if server-side draft not provided
        (function() {
            const content = document.getElementById('draftContent');
            const empty = document.getElementById('noDraft');
            try {
                const raw = sessionStorage.getItem('booking_draft');
                if (!raw) { content.style.display = 'none'; empty.style.display = 'block'; return; }
                const draft = JSON.parse(raw);
                // Expect that the slot title contains date/time/price; if not, leave blanks
                document.getElementById('draftName').textContent = draft.advertiser.name || '';
                document.getElementById('draftEmail').textContent = draft.advertiser.email || '';
                document.getElementById('draftPhone').textContent = draft.advertiser.phone || '';
                document.getElementById('draftCompany').textContent = draft.advertiser.company || '';
                document.getElementById('draftMessage').textContent = draft.message || '';
                // Render date/time/total from draft
                document.getElementById('draftDate').textContent = draft.slot.date || '';
                if (draft.slot.start_time && draft.slot.end_time) {
                    document.getElementById('draftTime').textContent = `${draft.slot.start_time} - ${draft.slot.end_time}`;
                } else {
                    document.getElementById('draftTime').textContent = '';
                }
                document.getElementById('draftTotal').textContent = draft.slot.price != null ? `$${draft.slot.price}` : '';
                if (draft.slot.station_name) {
                    document.getElementById('draftStation').textContent = draft.slot.station_name;
                }
                // Attach the payload so server can persist on confirm
                document.getElementById('draftPayload').value = raw;
                content.style.display = 'block';
                empty.style.display = 'none';
            } catch (e) {
                content.style.display = 'none';
                empty.style.display = 'block';
            }
        })();
    </script>
</body>
</html>