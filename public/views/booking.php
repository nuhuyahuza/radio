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
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
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
		text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
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
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
	}

	.modal-content {
		border-radius: 20px;
		border: none;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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

	.form-control,
	.form-select {
		border-radius: 10px;
		border: 2px solid #e9ecef;
		padding: 12px 16px;
		font-size: 1rem;
		transition: all 0.3s ease;
	}

	.form-control:focus,
	.form-select:focus {
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

	.ad-type-badge {
		display: inline-block;
		padding: 6px 12px;
		border-radius: 6px;
		font-weight: 600;
		font-size: 0.9rem;
		margin: 5px 5px 5px 0;
	}

	.badge-jingle {
		background: #28a745;
		color: white;
	}

	.badge-lpm {
		background: #007bff;
		color: white;
	}

	.badge-talkshow {
		background: #fd7e14;
		color: white;
	}

	.legend-box {
		background: rgba(255, 255, 255, 0.2);
		border-radius: 10px;
		padding: 20px;
		margin-bottom: 20px;
		backdrop-filter: blur(10px);
		border: 1px solid rgba(255, 255, 255, 0.3);
	}

	.legend-item {
		display: inline-flex;
		align-items: center;
		margin-right: 30px;
		margin-bottom: 10px;
	}

	.legend-color {
		width: 24px;
		height: 24px;
		border-radius: 6px;
		margin-right: 10px;
		border: 2px solid white;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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

	.form-section {
		display: none;
	}

	.form-section.active {
		display: block;
	}

	.time-slot-btn {
		margin: 5px;
		min-width: 100px;
	}

	.weekday-checkbox {
		margin-right: 15px;
	}

	.session-preview-table {
		max-height: 300px;
		overflow-y: auto;
	}
	</style>
</head>

<body>
	<!-- Navigation -->
	<?php 
        $user = Session::get('user');
        $role = $user['role'] ?? null;
        $dashboardUrl = $role === 'admin' ? '/admin' : ($role === 'manager' ? '/manager' : ($role === 'advertiser' ? '/advertiser' : null));
    ?>
	<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: rgba(0,0,0,0.2)">
		<div class="container">
			<a class="navbar-brand" href="/">
				<i class="fas fa-radio me-2"></i>
				<strong>Zaa Radio</strong>
			</a>
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

	<div class="booking-container">
		<div class="hero-section">
			<div class="container">
				<h1><i class="fas fa-calendar-alt me-3"></i>Book Your Radio Campaign</h1>
				<p>Choose from Jingles, Live Presenter Mentions, or Talkshows</p>

				<!-- Legend -->
				<div class="legend-box mt-4">
					<div class="legend-item">
						<div class="legend-color" style="background: #28a745;"></div>
						<span style="color: white; font-weight: 600;">Jingles (Green)</span>
					</div>
					<div class="legend-item">
						<div class="legend-color" style="background: #007bff;"></div>
						<span style="color: white; font-weight: 600;">LPMs (Blue)</span>
					</div>
					<div class="legend-item">
						<div class="legend-color" style="background: #fd7e14;"></div>
						<span style="color: white; font-weight: 600;">Talkshows (Orange)</span>
					</div>
				</div>
			</div>
		</div>

		<div class="container pb-5">
			<div class="row justify-content-center">
				<div class="col-lg-11">
					<!-- Migration Notice (will be hidden via JS if tables exist) -->
					<div class="alert alert-info alert-dismissible fade show" id="migrationNotice"
						style="display: none;">
						<i class="fas fa-info-circle me-2"></i>
						<strong>Database Setup Required:</strong> To use the new campaign booking features (Jingles,
						LPMs, Talkshows),
						please run the database migration. See <code>QUICK_START.md</code> for instructions.
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>

					<div class="calendar-card p-4">
						<div id="calendar"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Campaign Booking Modal -->
	<div class="modal fade" id="campaignBookingModal" tabindex="-1" data-bs-backdrop="static">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						<i class="fas fa-broadcast-tower me-2"></i>Book Radio Campaign
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

					<form id="campaignBookingForm">
						<input type="hidden" name="csrf_token" id="csrf_token" value="<?= Session::getCsrfToken() ?>">

						<!-- Step 1: Ad Type Selection -->
						<div id="step1" class="form-section active">
							<h4 class="mb-4">Step 1: Choose Advertisement Type</h4>
							<div class="row">
								<div class="col-md-4 mb-3">
									<div class="card h-100" style="cursor: pointer;" onclick="selectAdType('jingle')">
										<div class="card-body text-center">
											<div class="ad-type-badge badge-jingle mb-3">
												<i class="fas fa-music"></i> Jingle
											</div>
											<h6>Jingles</h6>
											<p class="small">Short adverts (30s-3min) that play multiple times per day
												over your campaign period.</p>
										</div>
									</div>
								</div>
								<div class="col-md-4 mb-3">
									<div class="card h-100" style="cursor: pointer;" onclick="selectAdType('lpm')">
										<div class="card-body text-center">
											<div class="ad-type-badge badge-lpm mb-3">
												<i class="fas fa-microphone"></i> LPM
											</div>
											<h6>Live Presenter Mentions</h6>
											<p class="small">Mentions during live shows with continuous daily time
												ranges.</p>
										</div>
									</div>
								</div>
								<div class="col-md-4 mb-3">
									<div class="card h-100" style="cursor: pointer;" onclick="selectAdType('talkshow')">
										<div class="card-body text-center">
											<div class="ad-type-badge badge-talkshow mb-3">
												<i class="fas fa-comments"></i> Talkshow
											</div>
											<h6>Talkshows</h6>
											<p class="small">Longer broadcast segments (30min-3hrs), one-off or
												recurring weekly.</p>
										</div>
									</div>
								</div>
							</div>
							<input type="hidden" name="ad_type" id="ad_type">
						</div>

						<!-- Step 2: Campaign Details (Dynamic based on ad type) -->
						<div id="step2" class="form-section">
							<!-- Jingle Fields -->
							<div id="jingleFields" style="display: none;">
								<h4 class="mb-4">Step 2: Jingle Campaign Details</h4>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign Start Date</label>
										<input type="date" class="form-control" name="start_date" id="jingle_start_date"
											min="<?= date('Y-m-d') ?>">
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign End Date</label>
										<input type="date" class="form-control" name="end_date" id="jingle_end_date"
											min="<?= date('Y-m-d') ?>">
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Duration per Jingle (seconds)</label>
									<select class="form-select" name="duration" id="jingle_duration">
										<option value="30">30 seconds</option>
										<option value="60">60 seconds (1 minute)</option>
										<option value="90">90 seconds</option>
										<option value="120">120 seconds (2 minutes)</option>
										<option value="180">180 seconds (3 minutes)</option>
									</select>
								</div>

								<div class="mb-3">
									<label class="form-label">Select Times of Day (Click to add/remove)</label>
									<div id="jingleTimesContainer">
										<button type="button" class="btn btn-outline-primary time-slot-btn"
											data-time="08:00:00">8:00 AM</button>
										<button type="button" class="btn btn-outline-primary time-slot-btn"
											data-time="12:00:00">12:00 PM</button>
										<button type="button" class="btn btn-outline-primary time-slot-btn"
											data-time="17:00:00">5:00 PM</button>
										<button type="button" class="btn btn-outline-primary time-slot-btn"
											data-time="20:00:00">8:00 PM</button>
									</div>
									<p class="small text-muted mt-2">Or add custom time:</p>
									<div class="input-group" style="max-width: 300px;">
										<input type="time" class="form-control" id="customJingleTime">
										<button type="button" class="btn btn-secondary"
											onclick="addCustomJingleTime()">Add</button>
									</div>
								</div>

								<div class="alert alert-info">
									<i class="fas fa-info-circle me-2"></i>
									Your jingle will play <strong><span id="jingleTimesCount">0</span> times per
										day</strong> from
									<strong><span id="jingleStartDisplay">-</span></strong> to <strong><span
											id="jingleEndDisplay">-</span></strong>.
								</div>

								<button type="button" class="btn btn-primary" onclick="goToStep(3)">
									Next: Preview Schedule <i class="fas fa-arrow-right ms-2"></i>
								</button>
							</div>

							<!-- LPM Fields -->
							<div id="lpmFields" style="display: none;">
								<h4 class="mb-4">Step 2: LPM Campaign Details</h4>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign Start Date</label>
										<input type="date" class="form-control" name="start_date" id="lpm_start_date"
											min="<?= date('Y-m-d') ?>">
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign End Date</label>
										<input type="date" class="form-control" name="end_date" id="lpm_end_date"
											min="<?= date('Y-m-d') ?>">
									</div>
								</div>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Daily Start Time</label>
										<input type="time" class="form-control" name="start_time" id="lpm_start_time">
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Daily End Time</label>
										<input type="time" class="form-control" name="end_time" id="lpm_end_time">
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Recurrence Pattern</label>
									<select class="form-select" name="recurrence" id="lpm_recurrence"
										onchange="toggleLpmWeekdays()">
										<option value="daily">Every Day</option>
										<option value="weekdays">Specific Weekdays</option>
									</select>
								</div>

								<div id="lpmWeekdaysContainer" style="display: none;" class="mb-3">
									<label class="form-label">Select Days</label><br>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="monday"> Monday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="tuesday"> Tuesday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="wednesday"> Wednesday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="thursday"> Thursday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="friday"> Friday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="saturday"> Saturday
									</label>
									<label class="weekday-checkbox">
										<input type="checkbox" name="weekdays[]" value="sunday"> Sunday
									</label>
								</div>

								<button type="button" class="btn btn-primary" onclick="goToStep(3)">
									Next: Preview Schedule <i class="fas fa-arrow-right ms-2"></i>
								</button>
							</div>

							<!-- Talkshow Fields -->
							<div id="talkshowFields" style="display: none;">
								<h4 class="mb-4">Step 2: Talkshow Campaign Details</h4>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign Start Date</label>
										<input type="date" class="form-control" name="start_date"
											id="talkshow_start_date" min="<?= date('Y-m-d') ?>">
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Campaign End Date</label>
										<input type="date" class="form-control" name="end_date" id="talkshow_end_date"
											min="<?= date('Y-m-d') ?>">
									</div>
								</div>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Start Time</label>
										<input type="time" class="form-control" name="start_time"
											id="talkshow_start_time">
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">End Time</label>
										<input type="time" class="form-control" name="end_time" id="talkshow_end_time">
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Recurrence Pattern</label>
									<select class="form-select" name="recurrence" id="talkshow_recurrence"
										onchange="toggleTalkshowWeekdays()">
										<option value="once">One-time Event</option>
										<option value="weekly">Weekly Recurring</option>
									</select>
								</div>

								<div id="talkshowWeekdayContainer" style="display: none;" class="mb-3">
									<label class="form-label">Select Day of Week</label>
									<select class="form-select" name="weekdays[]" id="talkshow_weekday">
										<option value="monday">Monday</option>
										<option value="tuesday">Tuesday</option>
										<option value="wednesday">Wednesday</option>
										<option value="thursday">Thursday</option>
										<option value="friday">Friday</option>
										<option value="saturday">Saturday</option>
										<option value="sunday">Sunday</option>
									</select>
								</div>

								<button type="button" class="btn btn-primary" onclick="goToStep(3)">
									Next: Preview Schedule <i class="fas fa-arrow-right ms-2"></i>
								</button>
							</div>

							<button type="button" class="btn btn-secondary" onclick="goToStep(1)">
								<i class="fas fa-arrow-left me-2"></i> Back
							</button>
						</div>

						<!-- Step 3: Preview & Advertiser Info -->
						<div id="step3" class="form-section">
							<h4 class="mb-4">Step 3: Preview Schedule & Confirm Details</h4>

							<div id="schedulePreview" class="mb-4">
								<div class="loading" id="previewLoading">
									<div class="spinner-border" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
									<p class="mt-2">Checking availability...</p>
								</div>

								<div id="previewResults" style="display: none;">
									<div class="alert alert-success">
										<i class="fas fa-check-circle me-2"></i>
										<strong id="availableCount">0</strong> sessions available for your campaign!
									</div>

									<div class="session-preview-table">
										<table class="table table-striped">
											<thead>
												<tr>
													<th>Date</th>
													<th>Time</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody id="previewTableBody">
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<h5 class="mb-3">Advertiser Information</h5>
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">Full Name *</label>
									<input type="text" class="form-control" name="advertiser_name" id="advertiser_name"
										required>
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label">Email *</label>
									<input type="email" class="form-control" name="advertiser_email"
										id="advertiser_email" required>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">Phone *</label>
									<input type="tel" class="form-control" name="advertiser_phone" id="advertiser_phone"
										required>
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label">Company Name</label>
									<input type="text" class="form-control" name="company_name" id="company_name">
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">Advertisement Message</label>
								<textarea class="form-control" name="message" id="message" rows="3"
									placeholder="Brief description of your advertisement"></textarea>
							</div>

							<div class="d-flex gap-2">
								<button type="button" class="btn btn-secondary" onclick="goToStep(2)">
									<i class="fas fa-arrow-left me-2"></i> Back
								</button>
								<button type="button" class="btn btn-primary btn-lg" onclick="confirmCampaignBooking()"
									id="confirmBtn">
									<i class="fas fa-check me-2"></i> Confirm Booking
								</button>
							</div>
						</div>
					</form>

					<div class="loading" id="bookingLoading">
						<div class="spinner-border" role="status">
							<span class="visually-hidden">Processing...</span>
						</div>
						<p class="mt-2">Processing your booking...</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
	<script>
	let calendar;
	let selectedAdType = null;
	let selectedJingleTimes = [];
	let previewedSlots = [];
	let bookingModal;

	document.addEventListener('DOMContentLoaded', function() {
		const calendarEl = document.getElementById('calendar');

		calendar = new FullCalendar.Calendar(calendarEl, {
			initialView: 'dayGridMonth',
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay'
			},
			events: function(info, successCallback, failureCallback) {
				fetch('/api/slots?start=' + info.startStr + '&end=' + info.endStr)
					.then(response => response.json())
					.then(data => {
						// Check if we got an error (likely table doesn't exist)
						if (data.error) {
							console.warn('Calendar API returned error:', data.message);
							// Show migration notice if table doesn't exist
							if (data.message && data.message.includes('booking_sessions')) {
								document.getElementById('migrationNotice').style.display = 'block';
							}
							successCallback([]); // Return empty array to show calendar
						} else {
							successCallback(data);
						}
					})
					.catch(error => {
						console.error('Failed to load calendar events:', error);
						failureCallback(error);
					});
			},
			height: 'auto',
			dayMaxEvents: 3,
			moreLinkClick: 'popover',
			dateClick: function(info) {
				const date = new Date(info.dateStr);
				const today = new Date();
				today.setHours(0, 0, 0, 0);

				if (date < today) {
					alert('Cannot book slots for past dates.');
					return;
				}

				// Open campaign booking modal and pre-fill the clicked date
				openCampaignBooking(info.dateStr);
			},
			eventClick: function(info) {
				// Check if this is a booked session
				const eventType = info.event.extendedProps.type;
				
				if (eventType === 'booking_session') {
					// This is an existing booking - show details instead of allowing new booking
					const sessionInfo = info.event.extendedProps;
					const startTime = info.event.start.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
					const endTime = info.event.end.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
					alert(`📅 Booked Session\n\n${info.event.title}\n\nDate: ${formatDate(info.event.startStr)}\nTime: ${startTime} - ${endTime}\n\nThis slot is already booked and unavailable.`);
					return;
				}
				
				// If it's an available slot, capture its date/time and open booking modal
				if (eventType === 'slot' && info.event.extendedProps.status === 'available') {
					// Store base date/time from the clicked slot
					window.preSelectedDate = info.event.startStr.substring(0, 10);
					const start = info.event.start;
					const end = info.event.end;
					const toHms = (d) => `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:00`;
					window.preSelectedStartTime = toHms(start);
					window.preSelectedEndTime = toHms(end);
					openCampaignBooking(window.preSelectedDate);
				}
			},
			eventDidMount: function(info) {
				// Events already have colors from the API
				const eventType = info.event.extendedProps.type;
				
				if (eventType === 'booking_session') {
					// Booked sessions - show as non-clickable
					info.el.style.cursor = 'not-allowed';
					info.el.style.opacity = '0.9';
					info.el.title = 'This slot is booked';
				} else {
					// Available slots - clickable
					info.el.style.cursor = 'pointer';
				}
			},
			loading: function(isLoading) {
				if (isLoading) {
					console.log('Loading calendar events...');
				}
			}
		});

		calendar.render();

		bookingModal = new bootstrap.Modal(document.getElementById('campaignBookingModal'));

		// Initialize time slot button clicks
		document.querySelectorAll('.time-slot-btn').forEach(btn => {
			btn.addEventListener('click', function() {
				toggleJingleTime(this.dataset.time, this);
			});
		});
	});

	function openCampaignBooking(selectedDate = null) {
		bookingModal.show();
		goToStep(1);
		
		// Pre-fill the date if one was clicked
		if (selectedDate) {
			// Store for later use when ad type is selected
			window.preSelectedDate = selectedDate;
			console.log('Pre-selected date:', selectedDate);
		}
	}

	function selectAdType(type) {
		selectedAdType = type;
		document.getElementById('ad_type').value = type;
		
		// Pre-fill dates if a date was clicked on calendar
		if (window.preSelectedDate) {
			const dateFields = {
				'jingle': ['jingle_start_date', 'jingle_end_date'],
				'lpm': ['lpm_start_date', 'lpm_end_date'],
				'talkshow': ['talkshow_start_date', 'talkshow_end_date']
			};
			
			const fieldsToFill = dateFields[type] || [];
			fieldsToFill.forEach(fieldId => {
				const field = document.getElementById(fieldId);
				if (field && !field.value) {
					field.value = window.preSelectedDate;
				}
			});
			
			// Also pre-fill time range for LPM/Talkshow using clicked slot times
			if (window.preSelectedStartTime && window.preSelectedEndTime) {
				const toHm = (hms) => hms ? hms.substring(0,5) : '';
				if (type === 'lpm') {
					const st = document.getElementById('lpm_start_time');
					const et = document.getElementById('lpm_end_time');
					if (st && !st.value) st.value = toHm(window.preSelectedStartTime);
					if (et && !et.value) et.value = toHm(window.preSelectedEndTime);
					const rec = document.getElementById('lpm_recurrence');
					if (rec && !rec.value) rec.value = 'daily';
				}
				if (type === 'talkshow') {
					const st = document.getElementById('talkshow_start_time');
					const et = document.getElementById('talkshow_end_time');
					if (st && !st.value) st.value = toHm(window.preSelectedStartTime);
					if (et && !et.value) et.value = toHm(window.preSelectedEndTime);
					const rec = document.getElementById('talkshow_recurrence');
					if (rec && !rec.value) rec.value = 'daily';
				}
			}
			
			console.log('Pre-filled date/time fields for', type, 'with', window.preSelectedDate, window.preSelectedStartTime, window.preSelectedEndTime);
		}
		
		goToStep(2);
	}

	function goToStep(step) {
		// Hide all steps
		document.querySelectorAll('.form-section').forEach(section => {
			section.classList.remove('active');
		});

		// Show selected step
		document.getElementById('step' + step).classList.add('active');

		// Show appropriate form fields for step 2
		if (step === 2 && selectedAdType) {
			document.getElementById('jingleFields').style.display = selectedAdType === 'jingle' ? 'block' : 'none';
			document.getElementById('lpmFields').style.display = selectedAdType === 'lpm' ? 'block' : 'none';
			document.getElementById('talkshowFields').style.display = selectedAdType === 'talkshow' ? 'block' : 'none';
		}

		// Load preview for step 3
		if (step === 3) {
			loadSchedulePreview();
		}
	}

	function toggleJingleTime(time, btn) {
		const index = selectedJingleTimes.indexOf(time);
		if (index > -1) {
			selectedJingleTimes.splice(index, 1);
			btn.classList.remove('active');
			btn.classList.add('btn-outline-primary');
			btn.classList.remove('btn-primary');
		} else {
			selectedJingleTimes.push(time);
			btn.classList.add('active');
			btn.classList.remove('btn-outline-primary');
			btn.classList.add('btn-primary');
		}
		updateJingleSummary();
	}

	function addCustomJingleTime() {
		const timeInput = document.getElementById('customJingleTime');
		const time = timeInput.value + ':00';

		if (time && !selectedJingleTimes.includes(time)) {
			selectedJingleTimes.push(time);

			// Add button to UI
			const btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'btn btn-primary time-slot-btn active';
			btn.dataset.time = time;
			btn.textContent = formatTime(time);
			btn.onclick = function() {
				toggleJingleTime(time, this);
			};

			document.getElementById('jingleTimesContainer').appendChild(btn);
			timeInput.value = '';
			updateJingleSummary();
		}
	}

	function updateJingleSummary() {
		document.getElementById('jingleTimesCount').textContent = selectedJingleTimes.length;

		const startDate = document.getElementById('jingle_start_date').value;
		const endDate = document.getElementById('jingle_end_date').value;

		document.getElementById('jingleStartDisplay').textContent = startDate || '-';
		document.getElementById('jingleEndDisplay').textContent = endDate || '-';
	}

	function toggleLpmWeekdays() {
		const recurrence = document.getElementById('lpm_recurrence').value;
		document.getElementById('lpmWeekdaysContainer').style.display =
			recurrence === 'weekdays' ? 'block' : 'none';
	}

	function toggleTalkshowWeekdays() {
		const recurrence = document.getElementById('talkshow_recurrence').value;
		document.getElementById('talkshowWeekdayContainer').style.display =
			recurrence === 'weekly' ? 'block' : 'none';
	}

	function loadSchedulePreview() {
		document.getElementById('previewLoading').style.display = 'block';
		document.getElementById('previewResults').style.display = 'none';

		// Gather form data based on ad type
		let requestData = {
			ad_type: selectedAdType,
			start_date: '',
			end_date: '',
			start_time: '',
			end_time: '',
			recurrence: 'daily',
			weekdays: [],
			jingle_times: [],
			duration: 30
		};

		if (selectedAdType === 'jingle') {
			requestData.start_date = document.getElementById('jingle_start_date').value;
			requestData.end_date = document.getElementById('jingle_end_date').value;
			requestData.jingle_times = selectedJingleTimes;
			requestData.duration = parseInt(document.getElementById('jingle_duration').value);
		} else if (selectedAdType === 'lpm') {
			requestData.start_date = document.getElementById('lpm_start_date').value;
			requestData.end_date = document.getElementById('lpm_end_date').value;
			const lpmStart = document.getElementById('lpm_start_time').value;
			const lpmEnd = document.getElementById('lpm_end_time').value;
			requestData.start_time = (lpmStart ? (lpmStart + ':00') : (window.preSelectedStartTime || ''));
			requestData.end_time = (lpmEnd ? (lpmEnd + ':00') : (window.preSelectedEndTime || ''));
			requestData.recurrence = document.getElementById('lpm_recurrence').value;

			if (requestData.recurrence === 'weekdays') {
				const checkboxes = document.querySelectorAll('#lpmWeekdaysContainer input[name="weekdays[]"]:checked');
				requestData.weekdays = Array.from(checkboxes).map(cb => cb.value);
			}
		} else if (selectedAdType === 'talkshow') {
			requestData.start_date = document.getElementById('talkshow_start_date').value;
			requestData.end_date = document.getElementById('talkshow_end_date').value;
			const tsStart = document.getElementById('talkshow_start_time').value;
			const tsEnd = document.getElementById('talkshow_end_time').value;
			requestData.start_time = (tsStart ? (tsStart + ':00') : (window.preSelectedStartTime || ''));
			requestData.end_time = (tsEnd ? (tsEnd + ':00') : (window.preSelectedEndTime || ''));
			requestData.recurrence = document.getElementById('talkshow_recurrence').value;

			if (requestData.recurrence === 'weekly') {
				requestData.weekdays = [document.getElementById('talkshow_weekday').value];
			}
		}

		// Validate
		if (!requestData.start_date || !requestData.end_date) {
			alert('Please fill in campaign start and end dates');
			goToStep(2);
			return;
		}

		// Call API
		fetch('/api/slots/check-availability', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify(requestData)
			})
			.then(res => res.json())
			.then(data => {
				document.getElementById('previewLoading').style.display = 'none';

				if (data.success) {
					previewedSlots = data.available_slots;
					displaySchedulePreview(data);
				} else {
					alert('Error: ' + data.message);
				}
			})
			.catch(err => {
				document.getElementById('previewLoading').style.display = 'none';
				alert('Failed to check availability');
				console.error(err);
			});
	}

	function displaySchedulePreview(data) {
		document.getElementById('previewResults').style.display = 'block';
		document.getElementById('availableCount').textContent = data.total_available;

		const tbody = document.getElementById('previewTableBody');
		tbody.innerHTML = '';

		// Show all requested slots with status
		const allSlots = [...data.available_slots, ...data.booked_slots];
		allSlots.sort((a, b) => {
			const dateCompare = a.date.localeCompare(b.date);
			if (dateCompare !== 0) return dateCompare;
			return a.start_time.localeCompare(b.start_time);
		});

		allSlots.forEach(slot => {
			const isAvailable = data.available_slots.some(s =>
				s.date === slot.date && s.start_time === slot.start_time
			);

			const row = document.createElement('tr');
			row.innerHTML = `
				<td>${formatDate(slot.date)}</td>
				<td>${formatTime(slot.start_time)} - ${formatTime(slot.end_time)}</td>
				<td><span class="badge ${isAvailable ? 'bg-success' : 'bg-danger'}">${isAvailable ? 'Available' : 'Booked'}</span></td>
			`;
			tbody.appendChild(row);
		});

		// Enable/disable confirm button
		document.getElementById('confirmBtn').disabled = data.total_available === 0;
	}

	function confirmCampaignBooking() {
		if (previewedSlots.length === 0) {
			alert('No available slots to book');
			return;
		}

		// Validate advertiser info
		const advertiserName = document.getElementById('advertiser_name').value.trim();
		const advertiserEmail = document.getElementById('advertiser_email').value.trim();
		const advertiserPhone = document.getElementById('advertiser_phone').value.trim();

		if (!advertiserName || !advertiserEmail || !advertiserPhone) {
			alert('Please fill in all required advertiser information');
			return;
		}

		document.getElementById('bookingLoading').style.display = 'block';
		document.getElementById('step3').style.display = 'none';

		// Prepare booking data
		const bookingData = {
			csrf_token: document.getElementById('csrf_token').value,
			ad_type: selectedAdType,
			selected_slots: previewedSlots,
			advertiser_name: advertiserName,
			advertiser_email: advertiserEmail,
			advertiser_phone: advertiserPhone,
			company_name: document.getElementById('company_name').value.trim(),
			message: document.getElementById('message').value.trim(),
			start_date: previewedSlots[0]?.date || '',
			end_date: previewedSlots[previewedSlots.length - 1]?.date || '',
			recurrence: 'custom', // Will be set properly based on ad type
			weekdays: []
		};

		// Submit booking
		fetch('/booking/confirm', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify(bookingData)
			})
			.then(async (res) => {
				const contentType = res.headers.get('content-type') || '';
				let payload;
				try {
					if (contentType.includes('application/json')) {
						payload = await res.json();
					} else {
						const text = await res.text();
						payload = { success: false, message: 'Unexpected response from server', debug: { status: res.status, contentType, text } };
					}
				} catch (e) {
					payload = { success: false, message: 'Failed to parse server response', debug: { status: res.status, error: e?.message } };
				}
				return payload;
			})
			.then(data => {
				document.getElementById('bookingLoading').style.display = 'none';

				if (data.success) {
					bookingModal.hide();
					calendar.refetchEvents();

					alert(
						`✅ Success! Your campaign has been booked with ${data.session_count} sessions. Check your email for confirmation.`
					);

					// Reset form
					document.getElementById('campaignBookingForm').reset();
					selectedJingleTimes = [];
					previewedSlots = [];
					window.preSelectedDate = null; // Clear pre-selected date

					if (data.redirect) {
						window.location.href = data.redirect;
					}
				} else {
					document.getElementById('step3').style.display = 'block';
					
					// Show detailed error message
					let errorMsg = '❌ Booking Failed:\n\n' + (data.message || 'Unknown error');
					
					// Add debug info if available
					if (data.debug) {
						errorMsg += '\n\n📍 Error Details:';
						if (data.debug.status) errorMsg += '\nStatus: ' + data.debug.status;
						if (data.debug.file) {
							const fileName = data.debug.file.split('/').pop();
							errorMsg += '\nFile: ' + fileName;
						}
						if (data.debug.line) errorMsg += '\nLine: ' + data.debug.line;
						if (data.debug.contentType) errorMsg += '\nContent-Type: ' + data.debug.contentType;
						if (data.debug.text) errorMsg += '\n\nResponse:\n' + (data.debug.text.slice(0, 400) + (data.debug.text.length > 400 ? '... (truncated)' : ''));
					}
					
					// Add helpful hints based on error message
					if (data.message && data.message.includes('already booked')) {
						errorMsg += '\n\n💡 Tip: This time slot is already booked. Please:';
						errorMsg += '\n• Choose a different date/time';
						errorMsg += '\n• Refresh the calendar to see current availability';
					} else if (data.message && data.message.includes('security token')) {
						errorMsg += '\n\n💡 Tip: Your session may have expired. Please refresh the page and try again.';
					}
					
					alert(errorMsg);
					console.error('Booking Error:', data);
				}
			})
			.catch(err => {
				document.getElementById('bookingLoading').style.display = 'none';
				document.getElementById('step3').style.display = 'block';
				
				alert('❌ Booking Failed:\n\nNetwork error or server timeout. Please try again.\n\nIf the problem persists, contact support.');
				console.error('Booking Error:', err);
			});
	}

	function formatDate(dateStr) {
		const date = new Date(dateStr);
		return date.toLocaleDateString('en-US', {
			weekday: 'short',
			year: 'numeric',
			month: 'short',
			day: 'numeric'
		});
	}

	function formatTime(timeStr) {
		const [h, m] = timeStr.split(':');
		const hour = parseInt(h);
		const ampm = hour >= 12 ? 'PM' : 'AM';
		const displayHour = hour % 12 || 12;
		return `${displayHour}:${m} ${ampm}`;
	}

	// Auto-open modal if requested
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.get('open') === 'campaign') {
		setTimeout(() => openCampaignBooking(), 500);
	}
	</script>
</body>

</html>