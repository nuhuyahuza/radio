<?php
use App\Utils\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slots - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        .slots-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .calendar-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
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
                            <a class="nav-link text-white active" href="/manager/slots">
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
                    <h1 class="h2">Manage Slots</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/manager/slots/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create New Slot
                        </a>
                        <div class="dropdown ms-2">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars(Session::getUser()['name'] ?? 'Manager') ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['total'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Total Slots</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['available'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Available</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-bookmark fa-2x text-danger mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['booked'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Booked</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card p-3 text-center">
                            <i class="fas fa-times-circle fa-2x text-secondary mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['cancelled'] ?? 0 ?></h4>
                            <p class="text-muted mb-0">Cancelled</p>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="calendar-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Slots Calendar</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshCalendar()">
                                <i class="fas fa-sync-alt me-1"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div id="slotsCalendar"></div>
                </div>

                <!-- Slots List -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Slots</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($slots, 0, 10) as $slot): ?>
                                    <tr>
                                        <td><?= date('M j, Y', strtotime($slot['date'])) ?></td>
                                        <td><?= date('g:i A', strtotime($slot['start_time'])) ?> - <?= date('g:i A', strtotime($slot['end_time'])) ?></td>
                                        <td>$<?= number_format($slot['price'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $slot['status'] === 'available' ? 'success' : ($slot['status'] === 'booked' ? 'danger' : 'secondary') ?>">
                                                <?= ucfirst($slot['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($slot['description'] ?: 'No description') ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="/manager/slots/edit/<?= $slot['id'] ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($slot['status'] !== 'booked'): ?>
                                                <form method="POST" action="/manager/slots/delete/<?= $slot['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slot?')">
                                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                <?php if ($slot['status'] === 'available'): ?>
                                                <form method="POST" action="/manager/slots/cancel/<?= $slot['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this slot?')">
                                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                                    <button type="submit" class="btn btn-outline-warning">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('slotsCalendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/manager/slots/data',
                eventClick: function(info) {
                    const slotId = info.event.id;
                    const status = info.event.extendedProps.status;
                    
                    if (status === 'available') {
                        if (confirm('Edit this slot?')) {
                            window.location.href = '/manager/slots/edit/' + slotId;
                        }
                    } else {
                        alert('This slot is ' + status + ' and cannot be edited.');
                    }
                },
                dateClick: function(info) {
                    const date = info.dateStr;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (info.date < today) {
                        alert('Cannot create slots for past dates.');
                        return;
                    }
                    
                    if (confirm('Create a new slot for ' + date + '?')) {
                        window.location.href = '/manager/slots/create?date=' + date;
                    }
                }
            });
            calendar.render();
        });

        function refreshCalendar() {
            const calendar = FullCalendar.getApi();
            calendar.refetchEvents();
        }
    </script>
</body>
</html>
