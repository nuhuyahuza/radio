<?php
$pageTitle = 'System Settings';
$currentPage = 'settings';
ob_start();
?>

<div class="row mb-4">
	<div class="col-12">
		<div class="d-flex justify-content-between align-items-center">
			<h2 class="mb-0">System Settings</h2>
			<div class="d-flex gap-2">
				<button class="btn btn-outline-primary" onclick="resetSettings()">
					<i class="fas fa-undo me-2"></i>Reset to Defaults
				</button>
				<button class="btn btn-primary" onclick="saveAllSettings()">
					<i class="fas fa-save me-2"></i>Save All Changes
				</button>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- General Settings -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-cog me-2"></i>
					General Settings
				</h5>
			</div>
			<div class="card-body">
				<form id="generalSettingsForm">
					<div class="mb-3">
						<label for="siteName" class="form-label">Site Name</label>
						<input type="text" class="form-control" id="siteName" value="Zaa Radio Booking System">
					</div>
					<div class="mb-3">
						<label for="siteDescription" class="form-label">Site Description</label>
						<textarea class="form-control" id="siteDescription"
							rows="3">Professional radio advertisement booking system</textarea>
					</div>
					<div class="mb-3">
						<label for="timezone" class="form-label">Timezone</label>
						<select class="form-select" id="timezone">
							<option value="UTC">UTC</option>
							<option value="America/New_York">Eastern Time</option>
							<option value="America/Chicago">Central Time</option>
							<option value="America/Denver">Mountain Time</option>
							<option value="America/Los_Angeles">Pacific Time</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="currency" class="form-label">Currency</label>
						<select class="form-select" id="currency">
							<option value="GHS">GHS (GH₵)</option>
							<option value="USD">USD ($)</option>
							<option value="EUR">EUR (€)</option>
							<option value="GBP">GBP (£)</option>
							<option value="CAD">CAD (C$)</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="dateFormat" class="form-label">Date Format</label>
						<select class="form-select" id="dateFormat">
							<option value="Y-m-d">YYYY-MM-DD</option>
							<option value="m/d/Y">MM/DD/YYYY</option>
							<option value="d/m/Y">DD/MM/YYYY</option>
							<option value="M j, Y">Jan 1, 2024</option>
						</select>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Booking Settings -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-calendar-check me-2"></i>
					Booking Settings
				</h5>
			</div>
			<div class="card-body">
				<form id="bookingSettingsForm">
					<div class="mb-3">
						<label for="minBookingAdvance" class="form-label">Minimum Booking Advance (hours)</label>
						<input type="number" class="form-control" id="minBookingAdvance" value="24" min="1">
					</div>
					<div class="mb-3">
						<label for="maxBookingAdvance" class="form-label">Maximum Booking Advance (days)</label>
						<input type="number" class="form-control" id="maxBookingAdvance" value="90" min="1">
					</div>
					<div class="mb-3">
						<label for="defaultSlotDuration" class="form-label">Default Slot Duration (minutes)</label>
						<select class="form-select" id="defaultSlotDuration">
							<option value="15">15 minutes</option>
							<option value="30" selected>30 minutes</option>
							<option value="60">60 minutes</option>
							<option value="120">120 minutes</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="autoApproveBookings" class="form-label">Auto-approve Bookings</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="autoApproveBookings">
							<label class="form-check-label" for="autoApproveBookings">
								Automatically approve bookings without manual review
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="allowCancellation" class="form-label">Allow Cancellation</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="allowCancellation" checked>
							<label class="form-check-label" for="allowCancellation">
								Allow users to cancel their bookings
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="cancellationDeadline" class="form-label">Cancellation Deadline (hours before
							slot)</label>
						<input type="number" class="form-control" id="cancellationDeadline" value="2" min="0">
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Email Settings -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-envelope me-2"></i>
					Email Settings
				</h5>
			</div>
			<div class="card-body">
				<form id="emailSettingsForm">
					<div class="mb-3">
						<label for="smtpHost" class="form-label">SMTP Host</label>
						<input type="text" class="form-control" id="smtpHost" placeholder="smtp.gmail.com">
					</div>
					<div class="mb-3">
						<label for="smtpPort" class="form-label">SMTP Port</label>
						<input type="number" class="form-control" id="smtpPort" value="587">
					</div>
					<div class="mb-3">
						<label for="smtpUsername" class="form-label">SMTP Username</label>
						<input type="email" class="form-control" id="smtpUsername" placeholder="your-email@gmail.com">
					</div>
					<div class="mb-3">
						<label for="smtpPassword" class="form-label">SMTP Password</label>
						<input type="password" class="form-control" id="smtpPassword" placeholder="App password">
					</div>
					<div class="mb-3">
						<label for="smtpEncryption" class="form-label">Encryption</label>
						<select class="form-select" id="smtpEncryption">
							<option value="tls">TLS</option>
							<option value="ssl">SSL</option>
							<option value="">None</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="fromEmail" class="form-label">From Email</label>
						<input type="email" class="form-control" id="fromEmail" placeholder="noreply@zaaradio.com">
					</div>
					<div class="mb-3">
						<label for="fromName" class="form-label">From Name</label>
						<input type="text" class="form-control" id="fromName" value="Zaa Radio">
					</div>
					<div class="mb-3">
						<button type="button" class="btn btn-outline-primary" onclick="testEmail()">
							<i class="fas fa-paper-plane me-2"></i>Test Email
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Security Settings -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-shield-alt me-2"></i>
					Security Settings
				</h5>
			</div>
			<div class="card-body">
				<form id="securitySettingsForm">
					<div class="mb-3">
						<label for="sessionTimeout" class="form-label">Session Timeout (minutes)</label>
						<input type="number" class="form-control" id="sessionTimeout" value="60" min="5">
					</div>
					<div class="mb-3">
						<label for="maxLoginAttempts" class="form-label">Max Login Attempts</label>
						<input type="number" class="form-control" id="maxLoginAttempts" value="5" min="3">
					</div>
					<div class="mb-3">
						<label for="lockoutDuration" class="form-label">Lockout Duration (minutes)</label>
						<input type="number" class="form-control" id="lockoutDuration" value="15" min="1">
					</div>
					<div class="mb-3">
						<label for="requireEmailVerification" class="form-label">Require Email Verification</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="requireEmailVerification" checked>
							<label class="form-check-label" for="requireEmailVerification">
								Require email verification for new accounts
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="enableTwoFactor" class="form-label">Enable Two-Factor Authentication</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="enableTwoFactor">
							<label class="form-check-label" for="enableTwoFactor">
								Enable 2FA for admin accounts
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="passwordMinLength" class="form-label">Minimum Password Length</label>
						<input type="number" class="form-control" id="passwordMinLength" value="8" min="6">
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Notification Settings -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-bell me-2"></i>
					Notification Settings
				</h5>
			</div>
			<div class="card-body">
				<form id="notificationSettingsForm">
					<div class="mb-3">
						<label class="form-label">Email Notifications</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="notifyNewBooking" checked>
							<label class="form-check-label" for="notifyNewBooking">
								New booking received
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="notifyBookingCancelled" checked>
							<label class="form-check-label" for="notifyBookingCancelled">
								Booking cancelled
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="notifyPaymentReceived" checked>
							<label class="form-check-label" for="notifyPaymentReceived">
								Payment received
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="notifySystemAlerts" checked>
							<label class="form-check-label" for="notifySystemAlerts">
								System alerts and errors
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="notificationEmail" class="form-label">Notification Email</label>
						<input type="email" class="form-control" id="notificationEmail"
							placeholder="admin@zaaradio.com">
					</div>
					<div class="mb-3">
						<label class="form-label">SMS Notifications</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="enableSMS">
							<label class="form-check-label" for="enableSMS">
								Enable SMS notifications
							</label>
						</div>
					</div>
					<div class="mb-3">
						<label for="smsApiKey" class="form-label">SMS API Key</label>
						<input type="text" class="form-control" id="smsApiKey" placeholder="Your SMS API key">
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- System Information -->
	<div class="col-lg-6 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-info-circle me-2"></i>
					System Information
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-12">
						<div class="d-flex justify-content-between">
							<span>PHP Version:</span>
							<span class="fw-bold"><?= PHP_VERSION ?></span>
						</div>
					</div>
					<div class="col-12">
						<div class="d-flex justify-content-between">
							<span>Database Version:</span>
							<span class="fw-bold" id="dbVersion">Loading...</span>
						</div>
					</div>
				</div>
				<div class="mt-3">
					<button class="btn btn-outline-primary btn-sm" onclick="refreshSystemInfo()">
						<i class="fas fa-sync-alt me-1"></i>Refresh
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Maintenance
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">
					<i class="fas fa-tools me-2"></i>
					Maintenance
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-3">
						<button class="btn btn-outline-warning w-100" onclick="clearCache()">
							<i class="fas fa-broom me-2"></i>Clear Cache
						</button>
					</div>
					<div class="col-md-3">
						<button class="btn btn-outline-info w-100" onclick="optimizeDatabase()">
							<i class="fas fa-database me-2"></i>Optimize Database
						</button>
					</div>
					<div class="col-md-3">
						<button class="btn btn-outline-secondary w-100" onclick="generateBackup()">
							<i class="fas fa-download me-2"></i>Generate Backup
						</button>
					</div>
					<div class="col-md-3">
						<button class="btn btn-outline-danger w-100" onclick="maintenanceMode()">
							<i class="fas fa-wrench me-2"></i>Maintenance Mode
						</button>
					</div>
				</div>
			</div>
		</div>
	</div> -->
</div>

<script>
// Load settings on page load
document.addEventListener('DOMContentLoaded', function() {
	loadSettings();
	refreshSystemInfo();
	startClock();
});

// Load all settings
function loadSettings() {
	fetch('/admin/settings/data')
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				populateSettings(data.settings);
			} else {
				showAlert('Error loading settings: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error loading settings', 'danger');
		});
}

// Populate settings in forms
function populateSettings(settings) {
	// General settings
	if (settings.general) {
		document.getElementById('siteName').value = settings.general.site_name || '';
		document.getElementById('siteDescription').value = settings.general.site_description || '';
		document.getElementById('timezone').value = settings.general.timezone || 'UTC';
		document.getElementById('currency').value = settings.general.currency || 'USD';
		document.getElementById('dateFormat').value = settings.general.date_format || 'Y-m-d';
	}

	// Booking settings
	if (settings.booking) {
		document.getElementById('minBookingAdvance').value = settings.booking.min_advance || 24;
		document.getElementById('maxBookingAdvance').value = settings.booking.max_advance || 90;
		document.getElementById('defaultSlotDuration').value = settings.booking.default_duration || 30;
		document.getElementById('autoApproveBookings').checked = settings.booking.auto_approve || false;
		document.getElementById('allowCancellation').checked = settings.booking.allow_cancellation || true;
		document.getElementById('cancellationDeadline').value = settings.booking.cancellation_deadline || 2;
	}

	// Email settings
	if (settings.email) {
		document.getElementById('smtpHost').value = settings.email.smtp_host || '';
		document.getElementById('smtpPort').value = settings.email.smtp_port || 587;
		document.getElementById('smtpUsername').value = settings.email.smtp_username || '';
		document.getElementById('smtpPassword').value = settings.email.smtp_password || '';
		document.getElementById('smtpEncryption').value = settings.email.smtp_encryption || 'tls';
		document.getElementById('fromEmail').value = settings.email.from_email || '';
		document.getElementById('fromName').value = settings.email.from_name || '';
	}

	// Security settings
	if (settings.security) {
		document.getElementById('sessionTimeout').value = settings.security.session_timeout || 60;
		document.getElementById('maxLoginAttempts').value = settings.security.max_login_attempts || 5;
		document.getElementById('lockoutDuration').value = settings.security.lockout_duration || 15;
		document.getElementById('requireEmailVerification').checked = settings.security.require_email_verification ||
			true;
		document.getElementById('enableTwoFactor').checked = settings.security.enable_two_factor || false;
		document.getElementById('passwordMinLength').value = settings.security.password_min_length || 8;
	}

	// Notification settings
	if (settings.notifications) {
		document.getElementById('notifyNewBooking').checked = settings.notifications.new_booking || true;
		document.getElementById('notifyBookingCancelled').checked = settings.notifications.booking_cancelled || true;
		document.getElementById('notifyPaymentReceived').checked = settings.notifications.payment_received || true;
		document.getElementById('notifySystemAlerts').checked = settings.notifications.system_alerts || true;
		document.getElementById('notificationEmail').value = settings.notifications.email || '';
		document.getElementById('enableSMS').checked = settings.notifications.enable_sms || false;
		document.getElementById('smsApiKey').value = settings.notifications.sms_api_key || '';
	}
}

// Save all settings
function saveAllSettings() {
	const settings = {
		general: {
			site_name: document.getElementById('siteName').value,
			site_description: document.getElementById('siteDescription').value,
			timezone: document.getElementById('timezone').value,
			currency: document.getElementById('currency').value,
			date_format: document.getElementById('dateFormat').value
		},
		booking: {
			min_advance: parseInt(document.getElementById('minBookingAdvance').value),
			max_advance: parseInt(document.getElementById('maxBookingAdvance').value),
			default_duration: parseInt(document.getElementById('defaultSlotDuration').value),
			auto_approve: document.getElementById('autoApproveBookings').checked,
			allow_cancellation: document.getElementById('allowCancellation').checked,
			cancellation_deadline: parseInt(document.getElementById('cancellationDeadline').value)
		},
		email: {
			smtp_host: document.getElementById('smtpHost').value,
			smtp_port: parseInt(document.getElementById('smtpPort').value),
			smtp_username: document.getElementById('smtpUsername').value,
			smtp_password: document.getElementById('smtpPassword').value,
			smtp_encryption: document.getElementById('smtpEncryption').value,
			from_email: document.getElementById('fromEmail').value,
			from_name: document.getElementById('fromName').value
		},
		security: {
			session_timeout: parseInt(document.getElementById('sessionTimeout').value),
			max_login_attempts: parseInt(document.getElementById('maxLoginAttempts').value),
			lockout_duration: parseInt(document.getElementById('lockoutDuration').value),
			require_email_verification: document.getElementById('requireEmailVerification').checked,
			enable_two_factor: document.getElementById('enableTwoFactor').checked,
			password_min_length: parseInt(document.getElementById('passwordMinLength').value)
		},
		notifications: {
			new_booking: document.getElementById('notifyNewBooking').checked,
			booking_cancelled: document.getElementById('notifyBookingCancelled').checked,
			payment_received: document.getElementById('notifyPaymentReceived').checked,
			system_alerts: document.getElementById('notifySystemAlerts').checked,
			email: document.getElementById('notificationEmail').value,
			enable_sms: document.getElementById('enableSMS').checked,
			sms_api_key: document.getElementById('smsApiKey').value
		}
	};

	fetch('/admin/settings/save', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify(settings)
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert('Settings saved successfully', 'success');
			} else {
				showAlert('Error saving settings: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error saving settings', 'danger');
		});
}

// Test email
function testEmail() {
	fetch('/admin/settings/test-email', {
			method: 'POST',
			headers: {
				'X-CSRF-Token': getCsrfToken()
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert('Test email sent successfully', 'success');
			} else {
				showAlert('Error sending test email: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error sending test email', 'danger');
		});
}

// Reset settings
function resetSettings() {
	if (confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
		fetch('/admin/settings/reset', {
				method: 'POST',
				headers: {
					'X-CSRF-Token': getCsrfToken()
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					showAlert('Settings reset to defaults', 'success');
					loadSettings();
				} else {
					showAlert('Error resetting settings: ' + data.message, 'danger');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showAlert('Error resetting settings', 'danger');
			});
	}
}

// Refresh system information
function refreshSystemInfo() {
	fetch('/admin/settings/system-info')
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				document.getElementById('dbVersion').textContent = data.info.db_version || 'Unknown';
				document.getElementById('serverTime').textContent = data.info.server_time || 'Unknown';
				document.getElementById('uptime').textContent = data.info.uptime || 'Unknown';
				document.getElementById('memoryUsage').textContent = data.info.memory_usage || 'Unknown';
				document.getElementById('diskUsage').textContent = data.info.disk_usage || 'Unknown';
			}
		})
		.catch(error => {
			console.error('Error:', error);
		});
}

// Start clock
function startClock() {
	setInterval(() => {
		const now = new Date();
		document.getElementById('serverTime').textContent = now.toLocaleString();
	}, 1000);
}

// Maintenance functions
function clearCache() {
	if (confirm('Are you sure you want to clear the cache?')) {
		performMaintenanceAction('clear-cache');
	}
}

function optimizeDatabase() {
	if (confirm('Are you sure you want to optimize the database? This may take a few minutes.')) {
		performMaintenanceAction('optimize-database');
	}
}

function generateBackup() {
	if (confirm('Are you sure you want to generate a backup?')) {
		performMaintenanceAction('generate-backup');
	}
}

function maintenanceMode() {
	if (confirm('Are you sure you want to toggle maintenance mode?')) {
		performMaintenanceAction('maintenance-mode');
	}
}

// Perform maintenance action
function performMaintenanceAction(action) {
	fetch('/admin/settings/maintenance', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-Token': getCsrfToken()
			},
			body: JSON.stringify({
				action: action
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				showAlert(data.message, 'success');
			} else {
				showAlert('Error: ' + data.message, 'danger');
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showAlert('Error performing maintenance action', 'danger');
		});
}

// Utility functions
function getCsrfToken() {
	return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function showAlert(message, type) {
	const alertDiv = document.createElement('div');
	alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
	alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

	const container = document.querySelector('.content-area');
	container.insertBefore(alertDiv, container.firstChild);

	setTimeout(() => {
		alertDiv.remove();
	}, 5000);
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>