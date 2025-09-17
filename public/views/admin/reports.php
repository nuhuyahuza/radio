<?php
/**
 * Admin Reports Dashboard
 * Shows analytics and reports for the system
 */

use App\Utils\Session;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Reports & Analytics</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Bookings</h6></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=bookings&format=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=bookings&format=pdf">PDF</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=bookings&format=excel">Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Revenue</h6></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=revenue&format=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=revenue&format=pdf">PDF</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=revenue&format=excel">Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Users</h6></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=users&format=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=users&format=pdf">PDF</a></li>
                        <li><a class="dropdown-item" href="/admin/reports/export?type=users&format=excel">Excel</a></li>
                    </ul>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Bookings</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Active Users</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['active_users'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Bookings</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pending_bookings'] ?? 0 ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Booking Trends</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                    <div class="dropdown-header">Group By:</div>
                                    <a class="dropdown-item" href="#" onclick="updateChart('day')">Day</a>
                                    <a class="dropdown-item" href="#" onclick="updateChart('month')">Month</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="bookingTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Booking Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="bookingStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue Trends</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                    <div class="dropdown-header">Group By:</div>
                                    <a class="dropdown-item" href="#" onclick="updateRevenueChart('day')">Day</a>
                                    <a class="dropdown-item" href="#" onclick="updateRevenueChart('month')">Month</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="revenueTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Advertisers</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($stats['top_advertisers'])): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($stats['top_advertisers'] as $advertiser): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($advertiser['name']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($advertiser['email']) ?></small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge bg-primary rounded-pill"><?= $advertiser['booking_count'] ?> bookings</span>
                                                <br>
                                                <small class="text-muted">$<?= number_format($advertiser['total_spent'], 2) ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filter by Date Range</h6>
                        </div>
                        <div class="card-body">
                            <form id="dateFilterForm" class="row g-3">
                                <div class="col-md-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" 
                                           value="<?= date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" 
                                           value="<?= date('Y-m-t') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="groupBy" class="form-label">Group By</label>
                                    <select class="form-select" id="groupBy" name="group_by">
                                        <option value="day">Day</option>
                                        <option value="month">Month</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Update Charts</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global chart variables
let bookingTrendsChart, bookingStatusChart, revenueTrendsChart;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initBookingTrendsChart();
    initBookingStatusChart();
    initRevenueTrendsChart();
    
    // Setup date filter form
    document.getElementById('dateFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateAllCharts();
    });
});

function initBookingTrendsChart() {
    const ctx = document.getElementById('bookingTrendsChart').getContext('2d');
    bookingTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Bookings',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Approved',
                data: [],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initBookingStatusChart() {
    const ctx = document.getElementById('bookingStatusChart').getContext('2d');
    const statusData = <?= json_encode($stats['booking_status_distribution'] ?? []) ?>;
    
    bookingStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function initRevenueTrendsChart() {
    const ctx = document.getElementById('revenueTrendsChart').getContext('2d');
    revenueTrendsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue',
                data: [],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateChart(groupBy) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`/admin/reports/booking-analytics?start_date=${startDate}&end_date=${endDate}&group_by=${groupBy}`)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.period);
            const totalBookings = data.map(item => item.bookings);
            const approvedBookings = data.map(item => item.approved);
            
            bookingTrendsChart.data.labels = labels;
            bookingTrendsChart.data.datasets[0].data = totalBookings;
            bookingTrendsChart.data.datasets[1].data = approvedBookings;
            bookingTrendsChart.update();
        })
        .catch(error => console.error('Error:', error));
}

function updateRevenueChart(groupBy) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`/admin/reports/revenue-analytics?start_date=${startDate}&end_date=${endDate}&group_by=${groupBy}`)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.period);
            const revenue = data.map(item => item.revenue);
            
            revenueTrendsChart.data.labels = labels;
            revenueTrendsChart.data.datasets[0].data = revenue;
            revenueTrendsChart.update();
        })
        .catch(error => console.error('Error:', error));
}

function updateAllCharts() {
    const groupBy = document.getElementById('groupBy').value;
    updateChart(groupBy);
    updateRevenueChart(groupBy);
}
</script>

