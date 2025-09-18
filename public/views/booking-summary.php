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
        .alert-card { border: none; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .alert-card .icon { font-size: 1.2rem; margin-right: 8px; }
    </style>
</head>
<body>
    <?php 
        $user = Session::get('user');
        $role = $user['role'] ?? null;
        $dashboardUrl = $role === 'admin' ? '/admin' : ($role === 'manager' ? '/manager' : ($role === 'advertiser' ? '/advertiser' : null));
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: rgba(0,0,0,0.2)">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-radio me-2"></i><strong>Zaa Radio</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/book">Book</a></li>
                    <?php if ($dashboardUrl): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $dashboardUrl ?>">Dashboard</a></li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <form method="POST" action="/logout" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <button class="btn btn-sm btn-light text-dark">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="summary-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="summary-card p-5">
                        <div id="alertContainer" class="mb-3" style="display:none"></div>
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
            const alertContainer = document.getElementById('alertContainer');
            const csrfInput = document.querySelector('input[name="csrf_token"]');
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return '';
            }
            function showAlert(message, type) {
                const typeMap = { success: 'success', error: 'danger', info: 'info', warning: 'warning' };
                const bsType = typeMap[type] || 'info';
                alertContainer.style.display = 'block';
                alertContainer.innerHTML = `
                    <div class="alert alert-${bsType} alert-card d-flex align-items-start" role="alert">
                        <i class="icon fas ${bsType==='success'?'fa-check-circle':bsType==='danger'?'fa-exclamation-triangle':bsType==='warning'?'fa-exclamation-circle':'fa-info-circle'} mt-1"></i>
                        <div>${message}</div>
                    </div>
                `;
            }
            try {
                // Refresh CSRF token from server to avoid stale token
                fetch('/api/csrf-token', { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => { if (data && data.token && csrfInput) csrfInput.value = data.token; })
                    .catch(() => {});

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

                // Attach submit handler to ensure payload is posted and redirect followed
                const form = document.getElementById('confirmForm');
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        // Refresh payload from sessionStorage in case of changes
                        const latestRaw = sessionStorage.getItem('booking_draft');
                        if (latestRaw) {
                            document.getElementById('draftPayload').value = latestRaw;
                        }
                        const formData = new FormData(form);
                        showAlert('Submitting your booking...', 'info');
                        try {
                            const resp = await fetch('/booking/confirm', {
                                method: 'POST',
                                body: formData,
                                credentials: 'same-origin',
                                redirect: 'manual',
                                headers: { 
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-Token': getCookie('XSRF-TOKEN') || (csrfInput ? csrfInput.value : '')
                                }
                            });
                            const contentType = resp.headers.get('content-type') || '';
                            if (contentType.includes('application/json')) {
                                const data = await resp.json();
                                if (data.success) {
                                    showAlert('Booking submitted successfully. Redirecting...', 'success');
                                    setTimeout(() => { window.location.href = data.redirect || '/'; }, 900);
                                } else {
                                    showAlert(data.message || 'Confirmation failed. Please try again.', 'error');
                                }
                                return;
                            }
                            // Handle server redirect manually (302/303)
                            if (resp.status === 302 || resp.status === 303) {
                                const loc = resp.headers.get('Location') || resp.headers.get('location') || '/';
                                if (loc) {
                                    showAlert('Booking submitted successfully. Redirecting...', 'success');
                                    setTimeout(() => { window.location.href = loc; }, 900);
                                    return;
                                }
                            }
                            const text = await resp.text();
                            if (!resp.ok) {
                                showAlert('Confirmation failed. Please review and try again.', 'error');
                                return;
                            }
                            document.open();
                            document.write(text);
                            document.close();
                        } catch (err) {
                            showAlert('Failed to confirm booking. Please try again.', 'error');
                        }
                    });
                }
            } catch (e) {
                content.style.display = 'none';
                empty.style.display = 'block';
            }
        })();
    </script>
</body>
</html>