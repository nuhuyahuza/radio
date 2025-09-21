<?php
use App\Utils\Session;

$pageTitle = 'Settings';
$currentPage = 'settings';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="mb-0">Application Settings</h2>
	<div class="btn-group">
		<button type="button" class="btn btn-outline-secondary" onclick="resetSettings()">
			<i class="fas fa-undo me-2"></i>
			Reset to Defaults
		</button>
		<button type="button" class="btn btn-primary" onclick="saveSettings()">
			<i class="fas fa-save me-2"></i>
			Save Settings
		</button>
	</div>
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

<form method="POST" action="/admin/settings/update" id="settingsForm">
	<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
	
	<div class="row">
		<div class="col-lg-3">
			<!-- Settings Navigation -->
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">Settings Categories</h6>
				</div>
				<div class="card-body p-0">
					<div class="list-group list-group-flush">
						<?php foreach ($groupedSettings as $categoryKey => $category): ?>
						<a href="#<?= $categoryKey ?>" class="list-group-item list-group-item-action settings-nav" 
						   data-category="<?= $categoryKey ?>">
							<i class="<?= $category['icon'] ?> me-2"></i>
							<?= htmlspecialchars($category['title']) ?>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-lg-9">
			<!-- Settings Content -->
			<?php foreach ($groupedSettings as $categoryKey => $category): ?>
			<div class="settings-category" id="<?= $categoryKey ?>" style="display: none;">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title mb-0">
							<i class="<?= $category['icon'] ?> me-2"></i>
							<?= htmlspecialchars($category['title']) ?>
						</h5>
					</div>
					<div class="card-body">
						<?php foreach ($category['settings'] as $setting): ?>
						<div class="mb-4">
							<label for="<?= htmlspecialchars($setting['key']) ?>" class="form-label">
								<?= htmlspecialchars($setting['description']) ?>
							</label>
							
							<?php if ($setting['type'] === 'boolean'): ?>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" 
									   id="<?= htmlspecialchars($setting['key']) ?>" 
									   name="<?= htmlspecialchars($setting['key']) ?>" 
									   value="1" <?= $setting['value'] ? 'checked' : '' ?>>
								<label class="form-check-label" for="<?= htmlspecialchars($setting['key']) ?>">
									Enable <?= htmlspecialchars($setting['description']) ?>
								</label>
							</div>
							<?php elseif ($setting['type'] === 'integer'): ?>
							<input type="number" class="form-control" 
								   id="<?= htmlspecialchars($setting['key']) ?>" 
								   name="<?= htmlspecialchars($setting['key']) ?>" 
								   value="<?= htmlspecialchars($setting['value']) ?>">
							<?php elseif ($setting['type'] === 'float'): ?>
							<input type="number" class="form-control" step="0.01" 
								   id="<?= htmlspecialchars($setting['key']) ?>" 
								   name="<?= htmlspecialchars($setting['key']) ?>" 
								   value="<?= htmlspecialchars($setting['value']) ?>">
							<?php elseif ($setting['type'] === 'email'): ?>
							<input type="email" class="form-control" 
								   id="<?= htmlspecialchars($setting['key']) ?>" 
								   name="<?= htmlspecialchars($setting['key']) ?>" 
								   value="<?= htmlspecialchars($setting['value']) ?>">
							<?php elseif ($setting['type'] === 'url'): ?>
							<input type="url" class="form-control" 
								   id="<?= htmlspecialchars($setting['key']) ?>" 
								   name="<?= htmlspecialchars($setting['key']) ?>" 
								   value="<?= htmlspecialchars($setting['value']) ?>">
							<?php elseif (in_array($setting['key'], ['app_description', 'contact_address'])): ?>
							<textarea class="form-control" rows="3" 
									  id="<?= htmlspecialchars($setting['key']) ?>" 
									  name="<?= htmlspecialchars($setting['key']) ?>"><?= htmlspecialchars($setting['value']) ?></textarea>
							<?php else: ?>
							<input type="text" class="form-control" 
								   id="<?= htmlspecialchars($setting['key']) ?>" 
								   name="<?= htmlspecialchars($setting['key']) ?>" 
								   value="<?= htmlspecialchars($setting['value']) ?>">
							<?php endif; ?>
							
							<?php if ($setting['key'] === 'app_logo'): ?>
							<div class="mt-2">
								<img src="<?= htmlspecialchars($setting['value']) ?>" alt="Logo" 
									 class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
							</div>
							<?php endif; ?>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</form>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Reset Settings</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to reset all settings to their default values? This action cannot be undone.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<form method="POST" action="/admin/settings/reset" class="d-inline">
					<input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
					<button type="submit" class="btn btn-danger">Reset Settings</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Show first category by default
	const firstCategory = document.querySelector('.settings-category');
	if (firstCategory) {
		firstCategory.style.display = 'block';
		const firstNav = document.querySelector('.settings-nav');
		if (firstNav) {
			firstNav.classList.add('active');
		}
	}
	
	// Handle category navigation
	document.querySelectorAll('.settings-nav').forEach(nav => {
		nav.addEventListener('click', function(e) {
			e.preventDefault();
			
			// Remove active class from all nav items
			document.querySelectorAll('.settings-nav').forEach(item => {
				item.classList.remove('active');
			});
			
			// Add active class to clicked item
			this.classList.add('active');
			
			// Hide all categories
			document.querySelectorAll('.settings-category').forEach(category => {
				category.style.display = 'none';
			});
			
			// Show selected category
			const categoryId = this.getAttribute('data-category');
			const category = document.getElementById(categoryId);
			if (category) {
				category.style.display = 'block';
			}
		});
	});
});

function saveSettings() {
	document.getElementById('settingsForm').submit();
}

function resetSettings() {
	const modal = new bootstrap.Modal(document.getElementById('resetModal'));
	modal.show();
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>