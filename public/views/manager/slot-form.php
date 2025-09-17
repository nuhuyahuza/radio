<?php
use App\Utils\Session;

$isEdit = isset($slot) && $slot;
$pageTitle = $isEdit ? 'Edit Slot' : 'Create New Slot';
$formAction = $isEdit ? "/manager/slots/edit/{$slot['id']}" : '/manager/slots/create';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-radio fa-2x text-white"></i>
                        <h5 class="text-white mt-2">Zaa Radio</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager/slots">
                                <i class="fas fa-clock me-2"></i>
                                Manage Slots
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager/bookings">
                                <i class="fas fa-calendar-check me-2"></i>
                                Approve Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/manager/reports">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $pageTitle ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/manager/slots" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Slots
                        </a>
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

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-card p-4">
                            <form method="POST" action="<?= $formAction ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Date *</label>
                                            <input type="date" class="form-control" id="date" name="date" 
                                                   value="<?= $isEdit ? $slot['date'] : ($_GET['date'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="available" <?= $isEdit && $slot['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                                <option value="maintenance" <?= $isEdit && $slot['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                                <?php if ($isEdit): ?>
                                                <option value="cancelled" <?= $slot['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Start Time *</label>
                                            <input type="time" class="form-control" id="start_time" name="start_time" 
                                                   value="<?= $isEdit ? $slot['start_time'] : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">End Time *</label>
                                            <input type="time" class="form-control" id="end_time" name="end_time" 
                                                   value="<?= $isEdit ? $slot['end_time'] : '' ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price ($) *</label>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   step="0.01" min="0" value="<?= $isEdit ? $slot['price'] : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration</label>
                                            <input type="text" class="form-control" id="duration" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" 
                                              placeholder="Optional description for this slot..."><?= $isEdit ? htmlspecialchars($slot['description']) : '' ?></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/manager/slots" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?= $isEdit ? 'Update Slot' : 'Create Slot' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const durationInput = document.getElementById('duration');

            function calculateDuration() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                
                if (startTime && endTime) {
                    const start = new Date('2000-01-01 ' + startTime);
                    const end = new Date('2000-01-01 ' + endTime);
                    
                    if (end > start) {
                        const diffMs = end - start;
                        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                        const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                        
                        if (diffHours > 0) {
                            durationInput.value = diffHours + 'h ' + diffMinutes + 'm';
                        } else {
                            durationInput.value = diffMinutes + ' minutes';
                        }
                    } else {
                        durationInput.value = 'Invalid time range';
                    }
                } else {
                    durationInput.value = '';
                }
            }

            startTimeInput.addEventListener('change', calculateDuration);
            endTimeInput.addEventListener('change', calculateDuration);

            // Calculate initial duration if editing
            calculateDuration();

            // Set minimum date to today
            const dateInput = document.getElementById('date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
        });
    </script>
</body>
</html>
