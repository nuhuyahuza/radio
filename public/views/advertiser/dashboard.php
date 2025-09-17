<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertiser Dashboard - Zaa Radio</title>
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
                            <a class="nav-link text-white" href="/advertiser">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/book">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Book New Slot
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/advertiser/bookings">
                                <i class="fas fa-calendar-check me-2"></i>
                                My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/advertiser/profile">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Advertiser Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/book" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>
                            Book New Slot
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="fas fa-user me-1"></i>
                            Advertiser User
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">Total Bookings</h4>
                                        <h2>5</h2>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">Approved</h4>
                                        <h2>3</h2>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">Pending</h4>
                                        <h2>2</h2>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Bookings</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time Slot</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2024-01-15</td>
                                                <td>Morning Drive (6:00 AM - 9:00 AM)</td>
                                                <td><span class="badge bg-success">Approved</span></td>
                                                <td>$150.00</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024-01-20</td>
                                                <td>Evening Rush (5:00 PM - 7:00 PM)</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td>$200.00</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> This is a placeholder advertiser dashboard. Full functionality will be implemented in upcoming todos.
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
