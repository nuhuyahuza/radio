<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station Manager Dashboard - Zaa Radio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <h1 class="h2">Station Manager Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="fas fa-user me-1"></i>
                            Manager User
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pending Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Morning Drive Slot</h6>
                                            <small class="text-muted">Tomorrow 6:00 AM - 9:00 AM</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-success me-1">Approve</button>
                                            <button class="btn btn-sm btn-danger">Reject</button>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Evening Rush Slot</h6>
                                            <small class="text-muted">Friday 5:00 PM - 7:00 PM</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-success me-1">Approve</button>
                                            <button class="btn btn-sm btn-danger">Reject</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Today's Schedule</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Morning Drive</h6>
                                            <small>6:00 AM - 9:00 AM</small>
                                        </div>
                                        <p class="mb-1">ABC Company - Product Launch</p>
                                        <small class="text-success">Confirmed</small>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Midday</h6>
                                            <small>12:00 PM - 2:00 PM</small>
                                        </div>
                                        <p class="mb-1">Available</p>
                                        <small class="text-muted">No booking</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> This is a placeholder manager dashboard. Full functionality will be implemented in upcoming todos.
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
